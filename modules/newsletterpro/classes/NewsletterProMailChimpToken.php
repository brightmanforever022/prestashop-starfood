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

class NewsletterProMailChimpToken extends ObjectModel
{
	public $token;

	public $creation_date;

	public $modified_date;
	public $expiration_date;

	public static $token_static;

	public static $definition = array(
		'table'     => 'newsletter_pro_mailchimp_token',
		'primary'   => 'id_newsletter_pro_mailchimp_token',
		'fields' => array(
			'token'                  => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
			'creation_date'                   => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'modified_date'                   => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'expiration_date'                   => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		)
	);

	public function __construct($id = null)
	{
		parent::__construct($id);
	}

	public static function newInstance($id = null)
	{
		return new self($id);
	}

	public static function getInstanceByToken($token)
	{
		$id = (int)Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_mailchimp_token`
			FROM `'._DB_PREFIX_.'newsletter_pro_mailchimp_token`
			WHERE `token` = "'.pSQL($token).'"
		');

		$instance = self::newInstance($id);
		if (Validate::isLoadedObject($instance))
			return $instance;

		return false;
	}

	public static function generateToken()
	{
		return Tools::encrypt(uniqid() + time());
	}

	public static function generateDayToken()
	{
		return Tools::encrypt('mailchimp'.date('Y-m-d'));
	}

	public static function generateHourToken()
	{
		return Tools::encrypt('mailchimp'.date('Y-m-d H'));
	}

	public static function generateMinuteToken()
	{
		return Tools::encrypt('mailchimp'.date('Y-m-d H:i'));
	}

	public static function getToken()
	{
		if (!isset(self::$token_static))
			self::$token_static = self::generateToken();

		return self::$token_static;
	}

	public function add($auto_date = true, $null_values = false)
	{
		$this->updateDates();

		return parent::add($auto_date, $null_values);
	}

	public function update($null_values = false)
	{
		$this->updateDates();

		return parent::update($null_values);
	}

	private function updateDates()
	{
		if (isset($this->creation_date) || !$this->creation_date)
			$this->creation_date = date('Y-m-d H:i:s');

		$this->modified_date = date('Y-m-d H:i:s');
		$this->expiration_date = date('Y-m-d H:i:s', strtotime('+1 month'));
	}

	public function expired()
	{
		return strtotime($this->expiration_date) < time();
	}

	public static function validateToken($token_var = 'mc_token')
	{
		if (Tools::isSubmit($token_var))
		{
			$mc_token = NewsletterProMailChimpToken::getInstanceByToken(Tools::getValue($token_var));

			if ($mc_token)
			{
				if ($mc_token->expired())
					$mc_token->delete();
				else
					return true;
			}
		}

		return false;
	}
}