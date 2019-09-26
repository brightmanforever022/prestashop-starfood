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

class NewsletterProGenerateCustomers
{
	private $id_shop;

	private $id_shop_group;

	private $id_lang;

	private $id_gender;

	private $email_prefix = 'demo.com';

	public function __construct()
	{
		ini_set('max_execution_time', '15000');

		$this->id_shop       = array();
		$this->id_shop_group = array();
		$this->id_lang       = array();
		$this->id_gender     = array();

		foreach (Shop::getShops() as $value) 
			$this->id_shop[] = (int)$value['id_shop'];

		foreach (ShopGroup::getShopGroups() as $value) 
			$this->id_shop_group[] = (int)$value->id;

		foreach (Language::getLanguages() as $value)
			$this->id_lang[] = (int)$value['id_lang'];

		foreach (Gender::getGenders() as $value)
			$this->id_gender[] = (int)$value->id;
	}

	public static function newInstance()
	{
		return new self();
	}

	public function generate($customers, $visitors, $visitors_np, $added)
	{
		$sum = 0;
		$sum += $this->generateCustomers($customers);
		$sum += $this->generateVisitors($visitors);
		$sum += $this->generateVisitorsNp($visitors_np);
		$sum += $this->generateAdded($added);
		return $sum;
	}

	public function generateCustomers($num)
	{
		$sum = 0;
		for ($i = 0; $i < $num; $i++) 
		{
			$email = $this->getEmail();
			$firstname = $this->getFirstNameFromEmail($email);
			$lastname = $this->getLastNameFromEmail($email);

			$customer = new Customer();

			$customer->id_shop = $this->getIdShop();
			$customer->id_shop_group = $this->getIdShopGroup($customer->id_shop);

			$customer->note = 'sas';
			$customer->id_gender = $this->getIdGender();
			$customer->id_default_group = $this->getDefaultGroup();

			$customer->id_lang = $this->getIdLang();

			$customer->lastname = $firstname;
			$customer->firstname = $lastname;
			$customer->birthday = date('Y-m-d');
			$customer->email = $email;
			$customer->newsletter = 1;
			$customer->ip_registration_newsletter = '127.0.0.1';
			$customer->newsletter_date_add = '2013-06-17 19:28:06';

			$customer->website = 'www.nowebsite.com';
			$customer->company = 'freelancer';

			$customer->show_public_prices = 1;
			$customer->id_risk = 0;

			$customer->passwd = 'abcdefghijklmnoprstuvxyz';

			$customer->active = 1;
			$customer->is_guest = 0;
			$customer->deleted = 0;
			$customer->date_add  = date('Y-m-d H:i:s');
			$customer->date_upd  = date('Y-m-d H:i:s');

			if ($customer->save())
				$sum++;
		}
		
		return $sum;
	}

	public function generateVisitors($num)
	{
		$sum = 0;
		for ($i = 0; $i < $num; $i++) 
		{
			$id_shop = $this->getIdShop();
			$sql = 'INSERT INTO `'._DB_PREFIX_.'newsletter` 
				(`id_shop`, `id_shop_group`, `email`, `newsletter_date_add`, `ip_registration_newsletter`, `http_referer`, `active`)
				VALUES 
				('.(int)$id_shop.', '.(int)$this->getIdShopGroup($id_shop).", '".pSQL($this->getEmail())."', NULL, '127.0.0.1', NULL, 1);";

			if (Db::getInstance()->execute($sql))
				$sum++;
		}
		return $sum;
	}

	public function generateVisitorsNp($num)
	{
		$sum = 0;
		for ($i = 0; $i < $num; $i++) 
		{
			$email = $this->getEmail();
			$id_shop = $this->getIdShop();

			$sql = 'INSERT INTO `'._DB_PREFIX_.'newsletter_pro_subscribers`
					(`id_shop`, `id_shop_group`, `id_lang`, `id_gender`, `firstname`, `lastname`, `email`, `birthday`, `ip_registration_newsletter`, `list_of_interest`, `date_add`, `active`)
					VALUES
					('.(int)$id_shop.', '.(int)$this->getIdShopGroup($id_shop).', '.(int)$this->getIdLang().', '.(int)$this->getIdGender().', "'.pSQL($this->getFirstNameFromEmail($email)).'", "'.pSQL($this->getLastNameFromEmail($email)).'", "'.pSQL($email).'", "'.pSQL(date('Y-m-d')).'", "'.pSQL('127.0.0.1').'", NULL, "'.pSQL(date('Y-m-d H:i:s')).'", 1)';

			if (Db::getInstance()->execute($sql))
				$sum++;
		}
		return $sum;
	}

	public function generateAdded($num)
	{
		$sum = 0;
		for ($i = 0; $i < $num; $i++) 
		{
			$email = $this->getEmail();
			$id_shop = $this->getIdShop();

			$sql = 'INSERT INTO `'._DB_PREFIX_.'newsletter_pro_email` 
					(`id_shop`, `id_shop_group`, `id_lang`, `firstname`, `lastname`, `email`, `date_add`, `ip_registration_newsletter`, `active`)
					VALUES 
					('.(int)$id_shop.', '.(int)$this->getIdShopGroup($id_shop).', '.(int)$this->getIdLang().", '".pSQL($this->getFirstNameFromEmail($email))."', '".pSQL($this->getLastNameFromEmail($email))."', '".pSQL($email)."', NULL, '127.0.0.1', 1 );";

			if (Db::getInstance()->execute($sql))
				$sum++;
		}

		return $sum;
	}

	private function getIdShop()
	{
		return $this->random($this->id_shop);
	}

	private function getIdShopGroup($id_shop)
	{
		$shop = new Shop($id_shop);
		if (Validate::isLoadedObject($shop))
			return (int)$shop->id_shop_group;

		return $this->id_shop_group[0];
	}

	private function getDefaultGroup()
	{
		return (int)Configuration::get('PS_CUSTOMER_GROUP');
	}

	private function getIdRandomShopGroup()
	{
		return $this->random($this->id_shop_group);
	}

	private function getIdLang()
	{
		return $this->random($this->id_lang);
	}

	private function getIdGender()
	{
		return $this->random($this->id_gender);
	}

	private function getEmail()
	{
		$upper = 'ABCDEFGHIJKLMNOPRSTUVXYZ';
		$lower = 'abcdefghijklmnoprstuvxyz';
		$firstname = Tools::substr(str_shuffle($upper), 0, 1).Tools::substr(str_shuffle($lower), 0, rand(5, 8));
		$lastname = Tools::substr(str_shuffle($upper), 0, 1).Tools::substr(str_shuffle($lower), 0, rand(5, 8));

		$email = Tools::strtolower($firstname).'.'.Tools::strtolower($lastname).'@'.$this->email_prefix;
		return $email;
	}

	private function getFirstNameFromEmail($email)
	{
		if (preg_match('/^(\w+)\.(\w+)/', $email, $match))
			return Tools::ucfirst($match[1]);

		return 'Unknown';
	}

	private function getLastNameFromEmail($email)
	{
		if (preg_match('/^(\w+)\.(\w+)/', $email, $match))
			return Tools::ucfirst($match[2]);

		return 'Unknown';
	}

	private function getName()
	{
		$upper = 'ABCDEFGHIJKLMNOPRSTUVXYZ';
		$lower = 'abcdefghijklmnoprstuvxyz';
		$name = Tools::substr(str_shuffle($upper), 0, 1).Tools::substr(str_shuffle($lower), 0, rand(5, 8));
		return $name;
	}

	private function random($object)
	{
		return rand($object[0], $object[count($object) - 1]);
	}
}