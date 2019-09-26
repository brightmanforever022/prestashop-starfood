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

class NewsletterProSubscribers extends ObjectModel
{
	public $id_shop;

	public $id_shop_group;

	public $id_lang;

	public $id_gender;

	public $firstname;

	public $lastname;

	public $email;

	public $birthday;

	public $ip_registration_newsletter;

	public $list_of_interest;

	public $date_add;

	public $active;

	/* defined */
	public $context;

	public $module;

	public $errors = array();

	public static $definition = array(
		'table'     => 'newsletter_pro_subscribers',
		'primary'   => 'id_newsletter_pro_subscribers',
		'fields' => array(
			'id_shop'                    => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_shop_group'              => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_lang'                    => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_gender'                  => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'firstname'                  => array('type' => self::TYPE_STRING, 'validate' => 'isName'),
			'lastname'                   => array('type' => self::TYPE_STRING, 'validate' => 'isName'),
			'email'                      => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true),
			'birthday'                   => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'ip_registration_newsletter' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'list_of_interest'           => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'date_add'                   => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'active'                     => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
		)
	);

	public function __construct($id = null)
	{
		$this->context = Context::getContext();
		$this->module = NewsletterPro::getInstance();

		// add the new fields to the ObjectModel
		$variables_name = NewsletterProSubscribersCustomField::getVariables();

		if (!empty($variables_name))
		{
			foreach ($variables_name as $variable_name) 
			{
				$this->{$variable_name} = null;
				self::$definition['fields'][$variable_name] = array('type' => self::TYPE_STRING, 'validate' => 'isString');
			}
		}

		parent::__construct($id);

		$this->id_shop                    = $this->context->shop->id;
		$this->id_shop_group              = $this->context->shop->id_shop_group;
		$this->id_lang                    = $this->context->language->id;
		$this->id_gender				  = 0;
		$this->firstname                  = '';
		$this->lastname                   = '';
		$this->birthday                   = '';
		$this->ip_registration_newsletter = Tools::getRemoteAddr();
		$this->date_add                   = date('Y-m-d H:i:s');
		$this->active                     = 1;
	}

	public static function newInstance($id = null)
	{
		return new self($id);
	}

	public function add($autodate = true, $null_values = true)
	{
		try
		{
			if (!Validate::isName($this->firstname))
				$this->addError(sprintf(NewsletterPro::getInstance()->l('The "%s" is not a valid name.'), $this->firstname));

			if (!Validate::isName($this->lastname))
				$this->addError(sprintf(NewsletterPro::getInstance()->l('The "%s" is not a valid name.'), $this->lastname));

			if (!Validate::isEmail($this->email))
				$this->addError(sprintf(NewsletterPro::getInstance()->l('The email "%s" is not a valid email address.'), $this->email));

			$id_duplicate = (int)$this->isDuplicateEmail();
			if ($id_duplicate)
				$this->addError(sprintf(NewsletterPro::getInstance()->l('The email "%s" already exists in our database.'), $this->email));

			if (gettype($this->list_of_interest) != 'string')
				$this->list_of_interest = (string)$this->list_of_interest;

			if (!$this->hasErrors())
				return parent::add($autodate, $null_values);
		}
		catch(Exception $e)
		{
			if (_PS_MODE_DEV_)
				$this->addError($e->getMessage());
			else
				$this->addError(NewsletterPro::getInstance()->l('An error occurred when inserting the record into database!'));
		}

		return false;
	}

	public function update($null_values = true)
	{
		try
		{
			if (gettype($this->list_of_interest) != 'string')
				$this->list_of_interest = (string)$this->list_of_interest;

			if (!$this->hasErrors())
				return parent::update($null_values);
		}
		catch(Exception $e)
		{
			if (_PS_MODE_DEV_)
				$this->addError($e->getMessage());
			else
				$this->addError(NewsletterPro::getInstance()->l('An error occurred when inserting the record into database!'));
		}
		return false;
	}

	public function save($null_values = false, $autodate = true)
	{
		try
		{
			if (gettype($this->list_of_interest) != 'string')
				$this->list_of_interest = (string)$this->list_of_interest;

			if (!$this->hasErrors())
				return parent::save($null_values, $autodate);
		}
		catch(Exception $e)
		{
			if (_PS_MODE_DEV_)
				$this->addError($e->getMessage());
			else
				$this->addError(NewsletterPro::getInstance()->l('An error occurred when inserting the record into database!'));
		}
		return false;
	}

	public function isDuplicateEmail()
	{
		return Db::getInstance()->getValue('
				SELECT `id_newsletter_pro_subscribers` FROM `'._DB_PREFIX_.'newsletter_pro_subscribers` WHERE `email` = "'.pSQL($this->email).'"
			');
	}

	public static function getIdByEmail($email)
	{
		return Db::getInstance()->getValue('
				SELECT `id_newsletter_pro_subscribers` FROM `'._DB_PREFIX_.'newsletter_pro_subscribers` WHERE `email` = "'.pSQL($email).'"
			');
	}

	public function addError($error)
	{
		$this->errors[] = $error;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function hasErrors()
	{
		return !empty($this->errors);
	}
}