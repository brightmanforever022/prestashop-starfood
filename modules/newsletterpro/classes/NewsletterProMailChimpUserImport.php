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

class NewsletterProMailChimpUserImport
{
	public $email;

	public $firstname;

	public $lastname;

	public $subscribed;

	public $id_lang;

	public $id_shop;

	public $date_add;

	public $ip;

	public $birthday;

	public $user_type;

	public $user_object;

	private $languages_iso = array();

	private $shops_name = array();

	public function __construct($email)
	{
		$this->email = $email;
		$this->user_object = $this->getUserObject();

		$languages = Language::getLanguages(false);

		foreach ($languages as $lang)
			$this->languages_iso[$lang['iso_code']] = (int)$lang['id_lang'];

		$shops = Shop::getShops(false);

		foreach ($shops as $shop)
			$this->shops_name[Tools::strtolower($shop['name'])] = (int)$shop['id_shop'];
	}

	public static function newInstance($email)
	{
		return new self($email);
	}

	public function userExists()
	{
		return (Validate::isLoadedObject($this->user_object));
	}

	public function set($chimp_data)
	{
		$this->email = $chimp_data['email'];
		$this->firstname = $chimp_data['merges']['FNAME'];
		$this->lastname = $chimp_data['merges']['LNAME'];
		$this->subscribed = $chimp_data['merges']['SUBSCRIBED'] == 'yes'  ? true : false;

		$iso_code = Tools::strtolower($chimp_data['language']);

		if (array_key_exists($iso_code, $this->languages_iso))
			$this->id_lang = $this->languages_iso[$iso_code];
		else
			$this->id_lang = (int)pqnp_config('PS_LANG_DEFAULT');

		$shop_name = Tools::strtolower($chimp_data['merges']['SHOP']);

		if (array_key_exists($shop_name, $this->shops_name))
			$this->id_shop = $this->shops_name[$shop_name];
		else
			$this->id_shop = (int)pqnp_config('PS_SHOP_DEFAULT');

		$this->date_add = date('Y-m-d', strtotime($chimp_data['merges']['DATE_ADD']));

		$this->ip = $chimp_data['ip_opt'];

		$this->birthday = $chimp_data['merges']['birthday'];
		$this->user_type = $chimp_data['merges']['USER_TYPE'];

		// setup the user object
		$this->user_object->email = $this->email;

		if ($this->user_object instanceof Customer)
		{
			$this->user_object->firstname = $this->firstname;
			$this->user_object->lastname = $this->lastname;
			$this->user_object->newsletter = $this->subscribed;
			$this->user_object->id_lang = $this->id_lang;
			$this->user_object->id_shop = $this->id_shop;

			if (!Validate::isLoadedObject($this->user_object))
				$this->user_object->date_add = $this->date_add;

			$this->user_object->ip_registration_newsletter = $this->ip;
		}
		else if ($this->user_object instanceof NewsletterProSubscribers || $this->user_object instanceof NewsletterProEmail)
		{
			$this->user_object->firstname = $this->firstname;
			$this->user_object->lastname = $this->lastname;
			$this->user_object->active = $this->subscribed;
			$this->user_object->id_lang = $this->id_lang;
			$this->user_object->id_shop = $this->id_shop;

			if (!Validate::isLoadedObject($this->user_object))
				$this->user_object->date_add = $this->date_add;

			$this->user_object->ip_registration_newsletter = $this->ip;
		}
		else if ($this->user_object instanceof NewsletterProBlockNewsletter)
		{
			$this->user_object->active = $this->subscribed;
			$this->user_object->id_shop = $this->id_shop;

			if (!Validate::isLoadedObject($this->user_object))
				$this->user_object->newsletter_date_add = $this->date_add;
			$this->user_object->ip_registration_newsletter = $this->ip;
		}
		else
			throw new Exception(NewsletterPro::getInstance()->l('Invalid user_object type.'));

		return $this;
	}

	public function save()
	{
		if (!isset($this->user_object))
			throw new Exception(NewsletterPro::getInstance()->l('The user_object is not set.'));

		return $this->user_object->save();
	}

	private function getUserObject()
	{
		$tables = array();
		$tables_id = array();

		$tables['customer'] = 'id_customer';
		
		if (NewsletterProTools::blockNewsletterExists())
			$tables['newsletter'] = 'id';

		$tables['newsletter_pro_email'] = 'id_newsletter_pro_email';
		$tables['newsletter_pro_subscribers'] = 'id_newsletter_pro_subscribers';

		foreach ($tables as $table_name => $table_id)
		{
			$id = (int)Db::getInstance()->getValue('
				SELECT `'.pSQL($table_id).'` 
				FROM `'._DB_PREFIX_.pSQL($table_name).'`
				WHERE `email` = "'.pSQL($this->email).'"
			');

			if ($id)
				$tables_id[$table_name] = $id;
		}

		$user_object = null;

		if (isset($tables_id['customer']))
			$user_object = new Customer((int)$tables_id['customer']);
		else if (isset($tables_id['newsletter_pro_subscribers']) && pqnp_config('SUBSCRIPTION_ACTIVE'))
			$user_object = NewsletterProSubscribers::newInstance((int)$tables_id['newsletter_pro_subscribers']);
		else if (isset($tables_id['newsletter']) && NewsletterProTools::blockNewsletterExists() && !pqnp_config('SUBSCRIPTION_ACTIVE'))
			$user_object = NewsletterProBlockNewsletter::newInstance((int)$tables_id['newsletter']);
		else if (isset($tables_id['newsletter_pro_email']))
			$user_object = NewsletterProEmail::newInstance((int)$tables_id['newsletter_pro_email']);
		else
		{
			// if the email does not exists in database

			if (pqnp_config('SUBSCRIPTION_ACTIVE'))
				$user_object = NewsletterProSubscribers::newInstance();
			else if (NewsletterProTools::blockNewsletterExists())
				$user_object = NewsletterProBlockNewsletter::newInstance();
			else
				$user_object = NewsletterProEmail::newInstance();
		}

		return $user_object;
	}
}