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

class NewsletterProTemplateUser
{
	/**
	 * Only the public properties will be converted into variables
	 */

	public $email;

	public $firstname;

	public $lastname;

	public $id;

	public $id_lang;

	public $id_shop;

	public $id_default_group;

	public $newsletter_date_add;

	public $ip;

	public $active;

	public $language;

	public $date;

	public $date_full;

	public $shop_name;

	public $img_path;

	public $user_type;

	public $is_forwarder;

	private $variables = array();

	private $variables_chimp = array();

	private $template;

	private $create_use_type;

	const USER_TYPE_CUSTOMER = 100;

	const USER_TYPE_REGISTRED = 101;

	const USER_TYPE_REGISTRED_NP = 102;

	const USER_TYPE_ADDED = 103;

	const USER_TYPE_UNREGISTRED = 104;

	const USER_TYPE_EMPLOYEE = 105;

	public function __construct($email)
	{
		$this->email = $email;
	}

	public static function newInstance($email)
	{
		return new self($email);
	}

	public function setTemplate($template)
	{
		$this->template = $template;
		return $this;
	}

	public function getTypesName()
	{
		return array(
			self::USER_TYPE_CUSTOMER => 'customer',
			self::USER_TYPE_REGISTRED => 'registred',
			self::USER_TYPE_REGISTRED_NP => 'registred_np',
			self::USER_TYPE_ADDED => 'added',
			self::USER_TYPE_UNREGISTRED => 'unregistred',
			self::USER_TYPE_EMPLOYEE => 'employee',
		);
	}

	public function refresh($id_lang)
	{
		$this->create($this->create_use_type, $id_lang);
	}

	public function create($use_type = null, $id_lang = null)
	{
		$this->create_use_type = $use_type;

		$context = Context::getContext();

		$default_lang  = (int)NewsletterPro::getConfiguration('PS_LANG_DEFAULT');

		$sql_customer     = 'SELECT count(*) FROM `'._DB_PREFIX_.'customer` WHERE `email` = "'.pSQL($this->email).'"';
		$sql_registred    = 'SELECT count(*) FROM `'._DB_PREFIX_.'newsletter` WHERE `email` = "'.pSQL($this->email).'"';
		$sql_registred_np = 'SELECT count(*) FROM `'._DB_PREFIX_.'newsletter_pro_subscribers` WHERE `email` = "'.pSQL($this->email).'"';
		$sql_added        = 'SELECT count(*) FROM `'._DB_PREFIX_.'newsletter_pro_email` WHERE `email` = "'.pSQL($this->email).'"';

		$type = self::USER_TYPE_UNREGISTRED;

		// default behavior, search for the user
		if (!isset($use_type))
		{
			if (Db::getInstance()->getValue($sql_customer))
				$type = self::USER_TYPE_CUSTOMER;
			else if (!((bool)NewsletterPro::getConfiguration('SUBSCRIPTION_ACTIVE')) && Db::getInstance()->getValue($sql_registred))
				$type = self::USER_TYPE_REGISTRED;
			else if (((bool)NewsletterPro::getConfiguration('SUBSCRIPTION_ACTIVE')) && Db::getInstance()->getValue($sql_registred_np))
				$type = self::USER_TYPE_REGISTRED_NP;
			else if (Db::getInstance()->getValue($sql_added))
				$type = self::USER_TYPE_ADDED;
		}
		else
		{
			// this is for the type employee or unregistred
			$type = $use_type;
		}

		// types name for the variables
		$types_name = $this->getTypesName();
		$this->user_type = $types_name[$type];

		$exception_no_row_msg = sprintf(NewsletterPro::getInstance()->l('Cannot create the user for the email "%s".'), $this->email);

		switch ($type)
		{
			case self::USER_TYPE_CUSTOMER:
				$row = Db::getInstance()->getRow('
					SELECT 
						c.`firstname`, 
						c.`lastname`, 
						c.`email`, 
						c.`id_customer` AS `id`, 
						c.`id_lang`, 
						c.`id_shop`,
						c.`id_default_group`, 
						c.`newsletter_date_add`, 
						c.`ip_registration_newsletter` AS `ip`, 
						c.`active` , 
						lg.`name` AS `language`, 
						lg.`date_format_lite` AS `date`, 
						lg.`date_format_full` AS `date_full`, 
						sh.`name` AS `shop_name`
					FROM `'._DB_PREFIX_.'customer` c
					LEFT JOIN `'._DB_PREFIX_.'lang` lg ON (c.`id_lang` = lg.`id_lang`)
					INNER JOIN `'._DB_PREFIX_.'shop` sh ON (c.`id_shop` = sh.`id_shop`)
					WHERE c.`email` = "'.pSQL($this->email).'"
				');

				if (!$row)
					throw new NewsletterProTemplateUserException($exception_no_row_msg);

				$this->firstname = $row['firstname'];
				$this->lastname = $row['lastname'];
				$this->id = (int)$row['id'];
				
				if (!isset($id_lang))
					$this->id_lang = (int)$row['id_lang'];
				else 
					$this->id_lang = $id_lang;

				$this->id_shop = (int)$row['id_shop'];
				$this->id_default_group = (int)$row['id_default_group'];
				$this->newsletter_date_add = $row['newsletter_date_add'];
				$this->ip = $row['ip'];
				$this->active = (int)$row['active'];
				$this->language = $row['language'];
				$this->date = date($row['date']);
				$this->date_full = date($row['date_full']);
				$this->shop_name = $row['shop_name'];

				$this->setImgPath($this->id_lang);

				$customer = new Customer((int)$this->id);
				if (Validate::isLoadedObject($customer))
					$context->customer = $customer;

				break;

			case self::USER_TYPE_REGISTRED:
				$row = Db::getInstance()->getRow('
					SELECT 
						n.`id`, 
						n.`id_shop`, 
						n.`ip_registration_newsletter` AS `ip`, 
						n.`email`, n.`newsletter_date_add` , 
						s.`name` AS `shop_name`, 
						l.`date_format_lite` AS `date`, 
						l.`date_format_full` AS `date_full`, 
						l.`name` AS `language`
					FROM `'._DB_PREFIX_.'newsletter` n
					LEFT JOIN `'._DB_PREFIX_.'lang` l ON (l.id_lang = '.(int)$default_lang.')
					LEFT JOIN `'._DB_PREFIX_.'shop` s ON (s.`id_shop` = n.`id_shop`)
					WHERE n.`email` = "'.pSQL($this->email).'"
				');
		
				if (!$row)
					throw new NewsletterProTemplateUserException($exception_no_row_msg);

				$this->id = (int)$row['id'];
				$this->id_shop = (int)$row['id_shop'];
				$this->ip = $row['ip'];
				$this->newsletter_date_add = $row['newsletter_date_add'];
				$this->shop_name = $row['shop_name'];
				$this->date = date($row['date']);
				$this->date_full = date($row['date_full']);
				$this->language = $row['language'];

				// default settings			
				$this->firstname        = '';
				$this->lastname         = '';

				if (!isset($id_lang))
					$this->id_lang = $default_lang;
				else
					$this->id_lang = $id_lang;

				$this->id_default_group = '';
				$this->active           = 1;

				$this->setImgPath($default_lang);

				break;

			case self::USER_TYPE_ADDED:
				$row = Db::getInstance()->getRow('
					SELECT 
						n.`id_newsletter_pro_email` AS `id`,
						n.`email`, 
						n.`firstname`, 
						n.`lastname`, 
						n.`id_lang`, 
						n.`id_shop`, 
						n.`ip_registration_newsletter` AS `ip`, 
						n.`date_add` AS `newsletter_date_add`, 
						n.`active`,
						s.`name` AS `shop_name`,
						l.`date_format_lite` AS `date`, 
						l.`date_format_full` AS `date_full`, 
						l.`name` AS `language`
					FROM `'._DB_PREFIX_.'newsletter_pro_email` n
					LEFT JOIN `'._DB_PREFIX_.'lang` l ON (l.`id_lang` = n.`id_lang`)
					LEFT JOIN `'._DB_PREFIX_.'shop` s ON (s.`id_shop` = n.`id_shop`)
					WHERE n.`email` = "'.pSQL($this->email).'"
				');

				if (!$row)
					throw new NewsletterProTemplateUserException($exception_no_row_msg);
				
				$this->id = (int)$row['id'];
				$this->firstname = $row['firstname'];
				$this->lastname = $row['lastname'];
				
				if (!isset($id_lang))
					$this->id_lang = (int)$row['id_lang'];
				else
					$this->id_lang = $id_lang;

				$this->id_shop = (int)$row['id_shop'];
				$this->ip = $row['ip'];
				$this->newsletter_date_add = $row['newsletter_date_add'];
				$this->active = (int)$row['active'];
				$this->shop_name = $row['shop_name'];
				$this->date = date($row['date']);
				$this->date_full = date($row['date_full']);
				$this->language = $row['language'];

				// default settings
				$this->id_default_group = '';

				$this->setImgPath($this->id_lang);

				break;

			case self::USER_TYPE_REGISTRED_NP:
				$row = Db::getInstance()->getRow('
					SELECT 	
						n.`id_newsletter_pro_subscribers` AS `id`,
						n.`email`, 
						n.`firstname`, 
						n.`lastname`, 
						n.`id_lang`, 
						n.`id_shop`, 
						n.`ip_registration_newsletter` AS `ip`, 
						n.`date_add` AS `newsletter_date_add`, 
						n.`active`,
						s.`name` AS `shop_name`,
						l.`date_format_lite` AS `date`, 
						l.`date_format_full` AS `date_full`, 
						l.`name` AS `language`
					FROM `'._DB_PREFIX_.'newsletter_pro_subscribers` n
					LEFT JOIN `'._DB_PREFIX_.'lang` l ON (l.`id_lang` = n.`id_lang`)
					LEFT JOIN `'._DB_PREFIX_.'shop` s ON (s.`id_shop` = n.`id_shop`)
					WHERE n.`email` = "'.pSQL($this->email).'"
				');

				if (!$row)
					throw new NewsletterProTemplateUserException($exception_no_row_msg);

				$this->id = (int)$row['id'];
				$this->firstname = $row['firstname'];
				$this->lastname = $row['lastname'];

				if (!isset($id_lang))
					$this->id_lang = (int)$row['id_lang'];
				else 
					$this->id_lang = $id_lang;

				$this->id_shop = (int)$row['id_shop'];
				$this->ip = $row['ip'];
				$this->newsletter_date_add = $row['newsletter_date_add'];
				$this->active = (int)$row['active'];
				$this->shop_name = $row['shop_name'];
				$this->date = date($row['date']);
				$this->date_full = date($row['date_full']);
				$this->language = $row['language'];

				// default settings
				$this->id_default_group = '';

				$this->setImgPath($this->id_lang);

				break;

			case self::USER_TYPE_UNREGISTRED:
				$this->createDefault(self::USER_TYPE_UNREGISTRED, $id_lang);
				break;

			case self::USER_TYPE_EMPLOYEE:
				$this->createDefault(self::USER_TYPE_EMPLOYEE, $id_lang);
				break;

			default:
					throw new NewsletterProTemplateUserException(NewsletterPro::getInstance()->l('Unregistered user are not allowed'));
				break;
		}

		$this->initVariables();

		return $this;
	}

	private function createDefault($type, $id_lang_load = null)
	{
		$context = Context::getContext();

		// types name for the variables
		$types_name = $this->getTypesName();
		$this->user_type = $types_name[$type];

		$id_lang = (int)NewsletterPro::getConfiguration('PS_LANG_DEFAULT');
		$firstname = '';
		$lastname = '';
		$id = 0;

		switch ((int)$type) 
		{
			case self::USER_TYPE_UNREGISTRED:
				break;

			case self::USER_TYPE_EMPLOYEE:
				$id_lang = (int)$context->language->id;


				if (isset($context->employee))
				{
					if (!isset($this->email))
						$this->email = $context->employee->email;

					$id_lang = (int)$context->employee->id_lang;
					$firstname = $context->employee->firstname;
					$lastname = $context->employee->lastname;
					$id = (int)$context->employee->id;
				}
				else
				{
					if (!isset($this->email))
						$this->email = NewsletterPro::getConfiguration('PS_SHOP_EMAIL');
				}
				break;

			default:
				throw new NewsletterProTemplateUserException(sprintf(NewsletterPro::getInstance()->l('Invalid user type "%s". Only the "%s", "%s" values are allowed.'), $type, self::USER_TYPE_EMPLOYEE, self::USER_TYPE_UNREGISTRED));
				break;
		}
		$this->active = 1;
		$this->firstname = $firstname;
		$this->lastname = $lastname;
		$this->id = (int)$id;
		if (isset($id_lang_load))
			$this->id_lang = (int)$id_lang_load;
		else
			$this->id_lang = (int)$id_lang;

		$this->id_default_group = '';
		$this->id_shop = (int)$context->shop->id;
		$this->ip = '127.0.0.1';

		$this->setImgPath($id_lang);

		$row = Db::getInstance()->getRow('
			SELECT 
				`name`, 
				`date_format_lite` AS `date`, 
				`date_format_full` AS `date_full` 
			FROM `'._DB_PREFIX_.'lang` 
			WHERE `id_lang` = '.(int)$id_lang.'
		');

		if (!$row)
		{
			$row['name'] = 'Unknow';
			$row['date'] = 'Y-m-d';
			$row['date_full'] = 'Y-m-d H:i:s';
		}

		$this->language = $row['name'];
		$this->date = date($row['date']);
		$this->date_full = date($row['date_full']);
		$this->newsletter_date_add = date('Y-m-d H:i:s');

		$row = Db::getInstance()->getRow('
			SELECT `name` 
			FROM `'._DB_PREFIX_.'shop` 
			WHERE `id_shop` = '.(int)$this->id_shop.'
		');

		if (!$row)
			$row['name'] = NewsletterPro::getConfiguration('PS_SHOP_NAME');

		$this->shop_name = $row['name'];
	}

	private function setImgPath($id_lang)
	{
		$lang_img_path = Tools::getHttpHost(true)._PS_IMG_.'l/';
		$lang_img_dir  = _PS_IMG_DIR_.'l/';

		if (file_exists($lang_img_dir.$id_lang.'.jpg'))
			$this->img_path = NewsletterPro::relplaceAdminLink($lang_img_path.$id_lang.'.jpg');
		else
			$this->img_path = NewsletterPro::relplaceAdminLink($lang_img_path.'none.jpg');
	}

	private function initVariables()
	{
		$module = NewsletterPro::getInstance();
		$context = Context::getContext();

		$base_uri = NewsletterPro::relplaceAdminLink(__PS_BASE_URI__);
		$default_shop_url = Tools::getHttpHost(true).$base_uri;
		$shop_url = $module->getShopUrl();

		$this->default_shop_url = $default_shop_url;
		$this->module_url = $this->default_shop_url.'modules/newsletterpro/';
		$this->shop_url = $module->getShopUrl();
		$this->shop_logo_url = $module->getShopLogoUrl($this->id_shop);
		$this->shop_logo = '<a title="'.$this->shop_name.'" href="'.$this->shop_url.'"> <img style="border: none;" src="'.$this->shop_logo_url.'" alt="'.$this->shop_name.'" /> </a>';

		
		$this->unsubscribe_link = urldecode($context->link->getModuleLink($module->name, 'unsubscribe', array(
			'email' => $this->email,
			'u_token' => Tools::encrypt($this->email),
			'msg' => true,
		), null, $this->id_lang, $this->id_shop));


		$this->elastic_unsubscribe_link = '{unsubscribe:'.$this->unsubscribe_link.'}';
		$this->elastic_unsubscribeauto_link = '{unsubscribeauto:'.$this->unsubscribe_link.'}';

		$this->front_subscription_link = NewsletterProSubscriptionTpl::getFrontLink((int)$this->id_lang, (int)$this->id_shop);
		$this->front_subscription = '<a href="'.$this->front_subscription_link.'" target="_blank">'.$module->l('subscribe').'</a>';
		$this->unsubscribe = '<a href="'.$this->unsubscribe_link.'" target="_blank">'.$module->l('unsubscribe').'</a>';


		$this->unsubscribe_link_redirect = urldecode($context->link->getModuleLink($module->name, 'unsubscribe', array(
			'email' => $this->email,
			'u_token' => Tools::encrypt($this->email),
			'msg' => false,
		), null, $this->id_lang, $this->id_shop));

		$this->unsubscribe_redirect = '<a href="'.$this->unsubscribe_link_redirect.'" target="_blank">'.$module->l('unsubscribe').'</a>';
		

		$this->subscribe_link = urldecode($context->link->getModuleLink($module->name, 'subscribe', array(
			'email' => $this->email,
			'token' => Tools::encrypt($this->email)
		), null, $this->id_lang, $this->id_shop));

		$this->subscribe = '<a href="'.$this->subscribe_link.'" target="_blank">'.$module->l('subscribe').'</a>';
		
		$this->forward_link = urldecode($context->link->getModuleLink($module->name, 'forward', array(
			'email' => $this->email,
			'token' => Tools::encrypt($this->email)
		), null, $this->id_lang, $this->id_shop));

		// $this->unsubscribe_link = $this->shop_url.'index.php?fc=module&module=newsletterpro&controller=unsubscribe&email='.$this->email.'&u_token='.Tools::encrypt($this->email);
		// $this->unsubscribe_link_redirect = $this->shop_url.'index.php?fc=module&module=newsletterpro&controller=unsubscribe&email='.$this->email.'&u_token='.Tools::encrypt($this->email).'&msg=false';
		// $this->subscribe_link = $this->shop_url.'index.php?fc=module&module=newsletterpro&controller=subscribe&email='.$this->email.'&token='.Tools::encrypt($this->email);
		// $this->forward_link = $this->shop_url.'index.php?fc=module&module=newsletterpro&controller=forward&email='.$this->email.'&token='.Tools::encrypt($this->email);
		
		$this->forward = '<a href="'.$this->forward_link.'" target="_blank">'.$module->l('forward').'</a>';
		$this->date_text = date('F j.Y');
		$this->date_year = date('Y');
		$this->date_day = date('j');

		$mounths = $module->dateMonths($this->id_lang);
		$mounth_numeric = date('m');

		if (isset($mounths[$mounth_numeric]))
			$this->date_month_text = $mounths[$mounth_numeric];
		else
			$this->date_month_text = date('F');

		$this->domain = $context->shop->domain;
		$this->domain_ssl = $context->shop->domain_ssl;
		$this->shop_email = Configuration::get('PS_SHOP_EMAIL');

		// the variables with tokens will be replaced for mailchimp
		$this->page_index_link       = $context->link->getPageLink('index', null, $this->id_lang, null, false, $this->id_shop);
		$this->page_contact_link     = $context->link->getPageLink('contact', null, $this->id_lang, null, false, $this->id_shop);
		$this->page_new_products     = $context->link->getPageLink('new-products', null, $this->id_lang, null, false, $this->id_shop);
		$this->page_best_sales       = $context->link->getPageLink('best-sales', null, $this->id_lang, null, false, $this->id_shop);
		$this->page_sitemap          = $context->link->getPageLink('sitemap', null, $this->id_lang, null, false, $this->id_shop);
		$this->page_my_account       = $context->link->getPageLink('my-account', null, $this->id_lang, null, false, $this->id_shop);
		$this->page_my_orders        = $context->link->getPageLink('history', null, $this->id_lang, null, false, $this->id_shop);
		$this->page_my_order_slip    = $context->link->getPageLink('order-slip', null, $this->id_lang, null, false, $this->id_shop);
		$this->page_my_addresses     = $context->link->getPageLink('addresses', null, $this->id_lang, null, false, $this->id_shop);
		$this->page_my_personal_info = $context->link->getPageLink('identity', null, $this->id_lang, null, false, $this->id_shop);
		$this->page_my_vouchers      = $context->link->getPageLink('discount', null, $this->id_lang, null, false, $this->id_shop);

		if (!isset($this->template))
			throw new NewsletterProTemplateUserException(sprintf('You mut set the template variable.', 'template'));

		// this value wll be replaced as a template variable
		$this->is_forwarder = $this->template->is_forwarder;
		$this->forwarder_email = $this->template->getForwarderData('forwarder_email');

		if ($this->template instanceof NewsletterProTemplateHistory)
		{
			$this->unsubscribe_link = $this->unsubscribe_link.'&token='.$module->getTokenByIdHistory((int)$this->template->id_history);
			$this->unsubscribe_link_redirect = $this->unsubscribe_link_redirect.'&token='.$module->getTokenByIdHistory((int)$this->template->id_history);

			$this->view_in_browser_link = $module->url_location.'newsletter.php?&email='.$this->email.'&token_tpl='.$module->getTokenByIdHistory((int)$this->template->id_history);
			$this->view_in_browser = '<a href="'.$this->view_in_browser_link.'" target="_blank">'.$module->l('Click here').'</a>';

			$this->view_in_browser_link_share = urlencode($this->view_in_browser_link);
			$this->view_in_browser_share = '<a href="'.$this->view_in_browser_link_share.'" target="_blank">'.$module->l('Click here').'</a>';
		}
		else
		{
			$this->view_in_browser_link = '';
			$this->view_in_browser = '';
		}

		// mail chimp variables
		$this->setChimpVariables(array(
			'EMAIL' => $this->email,
			'FNAME' => $this->firstname, 
			'LNAME' => $this->lastname,
			'SHOP' => $this->shop_name,
			'SUBSCRIBED' => $this->active,
			'USER_TYPE' => $this->user_type,
			'LANGUAGE' => $this->language,
		));

		// extend user defined variables
		NewsletterProExtendTemplateVars::newInstance()->set($this);

		return $this;
	}

	public function variables()
	{
		$method = new ReflectionObject($this);
		$variables = array();
		foreach ($method->getProperties() as $property)
		{
			if ($property->isPublic())
				$variables[$property->getName()] = $property->getValue($this);
		}

		$variables = array_merge($variables, $this->variables);
		ksort($variables);
		return $variables;
	}

	public function variablesChimp()
	{
		return $this->variables_chimp;
	}

	public function setVariables($variables)
	{
		$this->variables = array_merge($this->variables, $variables);
		return $this;
	}

	public function setChimpVariables($variables)
	{
		$this->variables_chimp = array_merge($this->variables_chimp, $variables);
		return $this;
	}

	public function to()
	{
		return NewsletterProMail::getToFromUser($this);
	}
}