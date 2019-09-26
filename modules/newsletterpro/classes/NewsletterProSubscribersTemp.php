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

class NewsletterProSubscribersTemp extends ObjectModel
{
	public $email;

	public $id_newsletter_pro_subscription_tpl;

	public $token;

	public $data;

	public $date_add;

	/* defined */
	public $context;

	public $module;

	public $errors = array();

	public static $definition = array(
		'table'     => 'newsletter_pro_subscribers_temp',
		'primary'   => 'id_newsletter_pro_subscribers_temp',
		'fields' => array(
			'email'                              => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true),
			'id_newsletter_pro_subscription_tpl' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'token'                              => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
			'data'                               => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'date_add'                           => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		)
	);

	public function __construct($id = null)
	{
		$this->context = Context::getContext();
		$this->module = NewsletterPro::getInstance();

		parent::__construct($id);
	}

	public function copyFromSubscribe(NewsletterProSubscribers $subscribe)
	{
		$result = array(
			'id_shop'                    => $subscribe->id_shop,
			'id_shop_group'              => $subscribe->id_shop_group,
			'id_lang'                    => $subscribe->id_lang,
			'id_gender'                  => $subscribe->id_gender,
			'firstname'                  => $subscribe->firstname,
			'lastname'                   => $subscribe->lastname,
			'email'                      => $subscribe->email,
			'birthday'                   => $subscribe->birthday,
			'list_of_interest'           => $subscribe->list_of_interest,
			'ip_registration_newsletter' => $subscribe->ip_registration_newsletter,
			'date_add'                   => $subscribe->date_add,
			'active'                     => $subscribe->active,
		);

		$custom_fields = NewsletterProSubscribersCustomField::getVariables();

		foreach ($custom_fields as $variable)
		{
			if (property_exists($subscribe, $variable))
				$result[$variable] = $subscribe->{$variable};
		}

		return $result;
	}

	public function saveTemp(NewsletterProSubscribers $subscribe)
	{
		$data = $this->copyFromSubscribe($subscribe);

		$this->email    = $subscribe->email;
		$this->token    = Tools::encrypt($this->email);
		$this->data     = serialize($data);
		$this->date_add = date('Y-m-d H:i:s');

		if ($this->isDuplicateEmail())
		{
			$id = self::getIdByEmail($this->email);
			$obj = new NewsletterProSubscribersTemp($id);

			if (Validate::isLoadedObject($obj))
			{
				$obj->email                              = $this->email;
				$obj->token                              = $this->token;
				$obj->data                               = $this->data;
				$obj->date_add                           = $this->date_add;
				$obj->id_newsletter_pro_subscription_tpl = $this->id_newsletter_pro_subscription_tpl;
				return $obj->save();
			}
		}

		return $this->add();
	}

	public function add($autodate = true, $null_values = false)
	{
		try
		{
			if (!Validate::isEmail($this->email))
				$this->addError(sprintf('The email "%s" is not a valid email address.', $this->email));

			if (!$this->hasErrors())
				return parent::add($autodate, $null_values);
		}
		catch(Exception $e)
		{
			if (_PS_MODE_DEV_)
				$this->addError($e->getMessage());
			else
				$this->addError('An error occurred when inserting the record into database!');
		}

		return false;
	}

	public function update($null_values = false)
	{
		try
		{
			return parent::update($null_values);
		}
		catch(Exception $e)
		{
			if (_PS_MODE_DEV_)
				$this->addError($e->getMessage());
			else
				$this->addError('An error occurred when updateing the record into database!');
		}

		return false;
	}

	public function moveToSubscribers()
	{
		if ($subscribe = $this->buildSubscribersObj())
		{
			if (!$subscribe->save())
			{
				foreach ($subscribe->getErrors() as $error)
					$this->addError($error);
			}
			else
			{
				$this->delete();
				return true;
			}
		}
		return false;
	}

	public static function isSerialized($str)
	{
		return (is_array(@unserialize($str)));
	}

	public function buildSubscribersObj()
	{
		$data = array();

		if (self::isSerialized($this->data))
			$data = unserialize($this->data);
		else
		{
			$this->addError($this->module->l('Invalid serielized data.'));
			return false;
		}

		$id = NewsletterProSubscribers::getIdByEmail($data['email']);
		$subscribe = new NewsletterProSubscribers($id);

		$subscribe->id_shop                    = (int)$data['id_shop'];
		$subscribe->id_shop_group              = (int)$data['id_shop_group'];
		$subscribe->id_lang                    = (int)$data['id_lang'];
		$subscribe->id_gender                  = $data['id_gender'];
		$subscribe->firstname                  = $data['firstname'];
		$subscribe->lastname                   = $data['lastname'];
		$subscribe->email                      = $data['email'];
		$subscribe->birthday                   = $data['birthday'];
		$subscribe->list_of_interest           = $data['list_of_interest'];
		$subscribe->ip_registration_newsletter = $data['ip_registration_newsletter'];
		$subscribe->date_add                   = $data['date_add'];
		$subscribe->active                     = (int)$data['active'];

		$custom_fields = NewsletterProSubscribersCustomField::getVariables();

		foreach ($custom_fields as $variable)
		{
			if (array_key_exists($variable, $data))
				$subscribe->{$variable} = $data[$variable];
		}

		return $subscribe;
	}

	public function isDuplicateEmail()
	{
		return Db::getInstance()->getValue('
				SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_subscribers_temp` WHERE `email` = "'.pSQL($this->email).'"
			');
	}

	public static function getIdByEmail($email)
	{
		return Db::getInstance()->getValue('
				SELECT `id_newsletter_pro_subscribers_temp` FROM `'._DB_PREFIX_.'newsletter_pro_subscribers_temp` WHERE `email` = "'.pSQL($email).'"
			');
	}

	public static function getIdByToken($token)
	{
		return Db::getInstance()->getValue('
				SELECT `id_newsletter_pro_subscribers_temp` FROM `'._DB_PREFIX_.'newsletter_pro_subscribers_temp` WHERE `token` = "'.pSQL($token).'"
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
