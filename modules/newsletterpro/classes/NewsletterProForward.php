<?php
/**
* 2013-2015 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright 2013-2015 Ovidiu Cimpean
* @version   Release: 4
* @license   Do not edit, modify or copy this file
*/

class NewsletterProForward extends ObjectModel
{
	public $from;

	public $to;

	public $date_add;

	/* defined vars */

	public $errors = array();

	public $context;

	public $module;

	const FOREWORD_LIMIT = 5;

	public static $static_errors = array();

	public static $definition = array(
		'table'   => 'newsletter_pro_forward',
		'primary' => 'id_newsletter_pro_forward',
		'fields' => array(
			'from'     => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true),
			'to'       => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true),
			'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
		)
	);

	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->context = Context::getContext();
		$this->module  = $this->context->controller->module;
	}

	public function addError($error)
	{
		$this->errors[] = $error;
	}

	public function getErrors()
	{
		return array_unique($this->errors);
	}

	public function add($autodate = true, $null_values = false)
	{
		if ($this->limitExceeded())
		{
			$this->addError(sprintf($this->module->l('You cannot add more emails. You exceeded the limit of %s emails.'), self::FOREWORD_LIMIT));
			return false;
		}

		if ($this->isToDuplicate())
		{
			$this->addError(sprintf($this->module->l('Your friend with the email %s is already subscribed at our newsletter.'), $this->to));
			return false;
		}

		if ($info = $this->getUserTableByEmail($this->to))
		{
			$is_subscribed = (int)Db::getInstance()->getValue('SELECT `'.$info['newsletter'].'` FROM `'._DB_PREFIX_.$info['table'].'` WHERE `email` = "'.pSQL($this->to).'"');
			if ($is_subscribed)
			{
				$this->addError(sprintf($this->module->l('Your friend with the email %s is already subscribed at our newsletter.'), $this->to));
				return false;
			}
		}

		try
		{
			$return = parent::add($autodate, $null_values);

			if (!$return)
				$this->addError(sprintf($this->module->l('An error occurred when adding the email %s into the database.'), $this->to));

			return $return;
		}
		catch(Exception $e)
		{
			$this->addError(sprintf($this->module->l('An error occurred when adding the email %s into the database.'), $this->to));
		}

		return false;
	}

	public function limitExceeded()
	{
		$limit = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_forward` WHERE `from` = "'.pSQL($this->from).'"');
		if ($limit >= self::FOREWORD_LIMIT)
			return true;
		return false;
	}

	public static function addMultiple($from, $emails)
	{
		foreach ($emails as $email)
		{
			$instance = new NewsletterProForward();
			$instance->from = $from;
			$instance->to = $email;
			$instance->add();
			if ($instance->hasErrors())
				self::$static_errors = array_merge(self::$static_errors, $instance->getErrors());
		}
	}

	public static function getStaticErrors()
	{
		return array_unique(self::$static_errors);
	}

	public static function addStaticError($error)
	{
		self::$static_errors[] = $error;
	}

	public static function hasStaticErrors()
	{
		return (!empty(self::$static_errors));
	}

	public function hasErrors()
	{
		return (!empty($this->errors));
	}

	public function delete()
	{
		try
		{
			$result = parent::delete();
			if (!$result)
				$this->addError(sprintf($this->module->l('An error occurred when deleting the email %s.'), $this->to));

			return $result;
		}
		catch(Exception $e)
		{
			$this->addError(sprintf($this->module->l('An error occurred when deleting the email %s.'), $this->to));
		}
		return false;
	}

	public function isToDuplicate()
	{
		if (self::getInstanceByTo($this->to))
			return true;
		return false;
	}

	public static function getInstanceByTo($to)
	{
		$id = (int)Db::getInstance()->getValue('SELECT `id_newsletter_pro_forward` FROM `'._DB_PREFIX_.'newsletter_pro_forward` WHERE `to` = "'.pSQL($to).'"');
		if ($id)
			return new NewsletterProForward($id);
		return false;
	}

	public function getUserTableByEmail($email)
	{
		$definition = array(
			'customer'             => array('email' => 'email', 'newsletter' => 'newsletter'),
			'newsletter'           => array('email' => 'email', 'newsletter' => 'active'),
			'newsletter_pro_email' => array('email' => 'email', 'newsletter' => 'active'),
		);

		foreach ($definition as $table => $fields)
		{
			$sql = 'SELECT COUNT(*) FROM `'._DB_PREFIX_.$table.'` WHERE `'.$fields['email'].'` = "'.pSQL($email).'"';
			if (Db::getInstance()->getValue($sql))
				return array(
						'table'      => $table,
						'email'      => $fields['email'],
						'newsletter' => $fields['newsletter']
						);
		}

		return false;
	}

	public static function getEmailsToByEmailFrom($from)
	{
		if (is_array($from))
		{
			reset($from);
			$from = key($from);
		}

		$emails = array();
		$result = Db::getInstance()->executeS('SELECT `to` FROM `'._DB_PREFIX_.'newsletter_pro_forward` WHERE `from` = "'.pSQL($from).'"');

		if ($result)
			foreach ($result as $email)
				$emails[] = $email['to'];

		return $emails;
	}

	public static function getForwarders($from)
	{
		$emails = self::getEmailsToByEmailFrom($from);
		$emails_join = '"'.trim(join($emails, '","')).'"';
		$definition = array(
			'customer'                   => array('email' => 'email', 'newsletter' => 'newsletter'),
			'newsletter'                 => array('email' => 'email', 'newsletter' => 'active'),
			'newsletter_pro_email'       => array('email' => 'email', 'newsletter' => 'active'),
			'newsletter_pro_subscribers' => array('email' => 'email', 'newsletter' => 'active'),
		);

		$valid_emails = array();
		$delete_emails = array();
		if (!empty($emails))
		{
			foreach ($definition as $table => $fields)
			{
				$sql = 'SELECT `'.$fields['email'].'` FROM `'._DB_PREFIX_.$table.'` WHERE `'.$fields['email'].'` IN ('.$emails_join.')';
				if ($result = Db::getInstance()->executeS($sql))
					foreach ($result as $value)
						$delete_emails[] = $value['email'];
			}
		}

		$delete_emails = array_unique($delete_emails);

		foreach ($delete_emails as $to)
			Db::getInstance()->delete('newsletter_pro_forward', '`to` = "'.pSQL($to).'"', 1);

		$valid_emails = array_diff($emails, $delete_emails);

		return $valid_emails;
	}
}