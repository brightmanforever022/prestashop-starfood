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

class NewsletterProSubscriptionTpl extends ObjectModel
{
	public $name;

	public $active;

	public $voucher;

	public $css_style;

	public $display_gender;

	public $display_firstname;

	public $display_lastname;

	public $display_language;

	public $display_birthday;

	public $display_list_of_interest;

	public $display_subscribe_message;

	public $list_of_interest_type;

	public $body_width;

	public $body_max_width;

	public $body_min_width;

	public $body_top;

	public $show_on_pages;

	public $cookie_lifetime;

	public $start_timer;

	public $when_to_show;

	public $allow_multiple_time_subscription;

	public $mandatory_fields;

	public $date_add;

	public $content;

	public $subscribe_message;

	public $email_subscribe_voucher_message;

	public $email_subscribe_confirmation_message;

	public $terms_and_conditions_url;

	/* defined */
	public $context;

	public $module;

	public $errors = array();

	public $extend_vars = array();

	public $css_dir_path;

	public $css_uri_path;

	public static $replace_vars = array();

	const CSS_STYLE_GLOBAL_PATH = 'newsletter_subscribe.css';

	const LIST_OF_INTEREST_TYPE_SELECT = 0;

	const LIST_OF_INTEREST_TYPE_CHECKBOX = 1;

	const DEFAULT_BODY_WIDTH = '40%';

	const SHOW_ON_PAGES_NONE = 0;

	const SHOW_ON_PAGES_ALL = -1;

	const WHEN_TO_SHOW_POPUP_COOKIE = 0;

	const WHEN_TO_SHOW_POPUP_ALWAYS = 1;

	public static $definition = array(
		'table'     => 'newsletter_pro_subscription_tpl',
		'primary'   => 'id_newsletter_pro_subscription_tpl',
		'multilang' => true,
		'multilang_shop' => true,
		'fields' => array(
			'name'                                 => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
			'voucher'                              => array('type' => self::TYPE_STRING, 'validate' => 'isString'),

			'display_gender'                       => array('type' => self::TYPE_BOOL, 'validate' => 'isUnsignedId'),
			'display_firstname'                    => array('type' => self::TYPE_BOOL, 'validate' => 'isUnsignedId'),
			'display_lastname'                     => array('type' => self::TYPE_BOOL, 'validate' => 'isUnsignedId'),
			'display_language'                     => array('type' => self::TYPE_BOOL, 'validate' => 'isUnsignedId'),
			'display_birthday'                     => array('type' => self::TYPE_BOOL, 'validate' => 'isUnsignedId'),
			'display_list_of_interest'             => array('type' => self::TYPE_BOOL, 'validate' => 'isUnsignedId'),
			'display_subscribe_message'            => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'list_of_interest_type'                => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'body_width'                           => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'body_max_width'                       => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'body_min_width'                       => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'body_top'                             => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'date_add'                             => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'allow_multiple_time_subscription'     => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'mandatory_fields'                     => array('type' => self::TYPE_STRING, 'validate' => 'isString'),

			/* Lang fields */
			'content'                              => array('type' => self::TYPE_HTML, 'validate' => 'isString', 'lang' => true),
			'subscribe_message'                    => array('type' => self::TYPE_HTML, 'validate' => 'isString', 'lang' => true),
			'email_subscribe_voucher_message'      => array('type' => self::TYPE_HTML, 'validate' => 'isString', 'lang' => true),
			'email_subscribe_confirmation_message' => array('type' => self::TYPE_HTML, 'validate' => 'isString', 'lang' => true),

			/* Shop fields */
			'active'                               => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'shop' => true),
			'css_style'                            => array('type' => self::TYPE_HTML, 'validate' => 'isString', 'shop' => true),

			'show_on_pages'                        => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'shop' => true),
			'cookie_lifetime'                      => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'shop' => true),
			'start_timer'                          => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'shop' => true),
			'when_to_show'                         => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'shop' => true),

			'terms_and_conditions_url'             => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'shop' => true),
		)
	);

	public function __construct($id = null, $id_lang = null, $id_shop = null)
	{
		self::initAssoTables();

		parent::__construct($id, $id_lang, $id_shop);

		$this->context  = Context::getContext();
		$this->module   = NewsletterPro::getInstance();
		$this->css_dir_path = $this->module->dir_location.'views/css/subscription_template';
		$this->css_uri_path = $this->module->uri_location.'views/css/subscription_template';
	}

	public static function initAssoTables()
	{
		NewsletterProTools::addTableAssociationArray(self::getAssoTables());
	}

	public static function getAssoTables()
	{
		return array(
			'newsletter_pro_subscription_tpl'      => array('type' => 'shop'),
			// if it si liltiland multishop the fk_shop is requered, all the values will be availalbe in all the shop
			'newsletter_pro_subscription_tpl_lang' => array('type' => 'shop')
		);
	}

	public function delete()
	{
		try
		{
			$response = parent::delete();
			if ($response)
			{
				$css_filename = $this->getCSSStyleFileName();
				$full_path = $this->css_dir_path.'/'.$css_filename;

				if (file_exists($this->css_dir_path) && file_exists($full_path) && is_file($full_path))
				{
					$response = @unlink($full_path);
					if (!$response)
						$this->addError(sprintf('Cannot delete the css template file "%s" from the disk. Please check the CHMOD permissions!'), $full_path);
				}
				
			}
			return $response;
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

	public function add($autodate = true, $null_values = false)
	{
		// verify duplocate names
		try
		{
			if (!isset($this->date_add))
				$this->date_add = date('Y-m-d H:i:s');

			$this->name = Tools::strtolower(preg_replace('/\s+/', '_', trim($this->name)));

			$name_display = Tools::ucfirst(str_replace('_', ' ', $this->name));

			if (empty($this->name))
				$this->addError('The template name cannot be empty.');
			else if (!NewsletterProTools::isFileName($this->name))
				$this->addError(sprintf('The template name "%s" is not valid, there are illegal charactes.', $name_display));
			else if ($this->isDuplicateName())
				$this->addError(sprintf('The template name "%s" already exists in database.', $name_display));

			if ($this->active)
				self::setActive((int)$this->id);

			if (!$this->hasErrors())
			{
				$response = parent::add($autodate, $null_values);
				// only if the option Smarty Cache for CSS is activated a css file will be created | if ($response && (int)Configuration::get('PS_CSS_THEME_CACHE'))
				if ($response)
				{
					$response = $this->saveCSSStyleAsFile($this->css_style);
					if (!$response)
						$this->addError(sprintf('The css style cannot be saved as a file. Please check the CHMOD permissions.'));
				}

				self::setActiveIfNotExists();
				return $response;
			}
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
		if ($this->active)
			self::setActive((int)$this->id);

		$response = parent::update($null_values);
		// only if the option Smarty Cache for CSS is activated a css file will be created | if ($response && (int)Configuration::get('PS_CSS_THEME_CACHE'))
		if ($response)
		{
			$response = $this->saveCSSStyleAsFile($this->css_style);
			if (!$response)
				$this->addError(sprintf('The css style cannot be saved as a file. Please check the CHMOD permissions.'));
		}
		self::setActiveIfNotExists();
		return $response;
	}

	public static function setActive($id_template)
	{
		$shops_id = NewsletterProTools::getActiveShopsId();

		$success = array();

		if (Db::getInstance()->update('newsletter_pro_subscription_tpl_shop', array(
			'active' => 0,
		), '`active` = 1 AND `id_shop` IN ('.implode(',', $shops_id).')'))
		{
			$success[] = Db::getInstance()->update('newsletter_pro_subscription_tpl_shop', array(
				'active' => 1,
			), '`id_newsletter_pro_subscription_tpl` = '.(int)$id_template.' AND `id_shop` IN ('.implode(',', $shops_id).') ', 1);
		}

		if (Db::getInstance()->update('newsletter_pro_subscription_tpl', array(
			'active' => 0,
		), '`active` = 1'))
		{
			$success[] = Db::getInstance()->update('newsletter_pro_subscription_tpl', array(
				'active' => 1,
			), '`id_newsletter_pro_subscription_tpl` = '.(int)$id_template, 1);
		}

		self::setActiveIfNotExists();

		return !in_array(false, $success);
	}

	public static function setActiveIfNotExists()
	{
		$success = array();
		if (!Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl` WHERE `active` = 1'))
		{
			$success[] = Db::getInstance()->update('newsletter_pro_subscription_tpl', array(
				'active' => 1,
			), '`name` = "default"', 1);
		}

		return !in_array(false, $success);
	}

	public function isDuplicateName()
	{
		return Db::getInstance()->getValue('
				SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl` WHERE `name` = "'.pSQL($this->name).'"
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

	public static function getTemplateByName($name)
	{
		$id = (int)Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_subscription_tpl`
			FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl`
			WHERE `name` = "'.pSQL($name).'"
		');
		$template = new NewsletterProSubscriptionTpl($id);
		if (Validate::isLoadedObject($template))
			return $template;
		return false;
	}

	public static function getTemplatesDataGrid($id_lang = null, $id_shop = null)
	{
		$id_lang = (isset($id_lang) ? $id_lang : Context::getContext()->language->id);
		$id_shop = (isset($id_shop) ? $id_shop : Context::getContext()->shop->id);

		$result = Db::getInstance()->executeS(self::getTemplatesSql(array(
			'select' => '
				s.`id_newsletter_pro_subscription_tpl`,
				s.`name`,
				s.`voucher`,
				s.`display_gender`,
				s.`display_firstname`,
				s.`display_lastname`,
				s.`display_language`,
				s.`display_birthday`,
				s.`display_list_of_interest`,
				s.`list_of_interest_type`,
				s.`display_subscribe_message`,
				s.`date_add`,
				sl.`id_lang`,
				sl.`id_shop`,
				ss.`active`,
				ss.`show_on_pages`,
				ss.`cookie_lifetime`,
				ss.`start_timer`,
				ss.`when_to_show`,
				ss.`terms_and_conditions_url`
			',
			'id_lang' => (int)$id_lang,
			'id_shop' => (int)$id_shop,
		)));

		return $result;
	}

	public static function getTemplatesSql($config = array())
	{
		$context = Context::getContext();

		$select = isset($config['select']) ? $config['select'] : '*';

		if (isset($config['id_lang']))
			$id_lang = (int)$config['id_lang'];
		else
			$id_lang = (int)$context->language->id;

		if (isset($config['id_shop']))
			$id_shop = (int)$config['id_shop'];
		else
			$id_shop = (int)$context->shop->id;

		$sql = array();
		$sql[] = '
			SELECT '.$select.' FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl` s
			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_subscription_tpl_lang` sl
				ON (s.`id_newsletter_pro_subscription_tpl` = sl.`id_newsletter_pro_subscription_tpl`)

			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_subscription_tpl_shop` ss
				ON (sl.`id_newsletter_pro_subscription_tpl` = ss.`id_newsletter_pro_subscription_tpl`
					AND sl.`id_shop` = ss.`id_shop` )

			WHERE sl.`id_lang` = '.(int)$id_lang.'
			AND sl.`id_shop` = '.(int)$id_shop;

		if (isset($config['and']))
			$sql[] = 'AND '.$config['and'];

		$sql[] = 'ORDER BY s.`date_add`
			DESC
		';

		return implode(' ', $sql);
	}

	public static function getActiveTemplatesAllShops($id_lang = null)
	{
		$context = Context::getContext();

		if (!isset($id_lang))
			$id_lang = (int)$context->language->id;

		$sql = array();

		$sql[] = '
			SELECT
				s.`id_newsletter_pro_subscription_tpl`,
				s.`name`,
				s.`voucher`,
				s.`display_gender`,
				s.`display_firstname`,
				s.`display_lastname`,
				s.`display_language`,
				s.`display_birthday`,
				s.`display_list_of_interest`,
				s.`list_of_interest_type`,
				s.`display_subscribe_message`,
				s.`date_add`,
				sl.`id_lang`,
				sl.`id_shop`,
				ss.`active`,
				ss.`show_on_pages`,
				ss.`cookie_lifetime`,
				ss.`start_timer`,
				ss.`when_to_show`,
				ss.`terms_and_conditions_url`
			FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl` s
			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_subscription_tpl_lang` sl
				ON (s.`id_newsletter_pro_subscription_tpl` = sl.`id_newsletter_pro_subscription_tpl`)

			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_subscription_tpl_shop` ss
				ON (sl.`id_newsletter_pro_subscription_tpl` = ss.`id_newsletter_pro_subscription_tpl`
					AND sl.`id_shop` = ss.`id_shop` ) ';
		$sql[] = 'WHERE sl.`id_lang` = '.(int)$id_lang;
		$sql[] = 'AND ss.`active` = 1 ';
		$sql[] = 'ORDER BY s.`date_add`
			DESC
		';

		$sql_str = implode(' ', $sql);

		return Db::getInstance()->executeS($sql_str);
	}

	public function renderAll($id_lang = null)
	{
		return array(
			'content'                              => $this->render($id_lang),
			'subscribe_message'                    => $this->renderSubscribeMessage($id_lang),
			'email_subscribe_voucher_message'      => $this->renderEmailSubscribeVoucherMessage($id_lang),
			'email_subscribe_confirmation_message' => $this->renderEmailSubscribeConfirmationMessage($id_lang),
		);
	}

	public function render($id_lang = null)
	{
		return $this->renderByField('content', $id_lang);
	}

	public function renderSubscribeMessage($id_lang = null)
	{
		return $this->renderByField('subscribe_message', $id_lang);
	}

	public function renderEmailSubscribeVoucherMessage($id_lang = null)
	{
		return $this->renderByField('email_subscribe_voucher_message', $id_lang);
	}

	public function renderEmailSubscribeConfirmationMessage($id_lang = null)
	{
		return $this->renderByField('email_subscribe_confirmation_message', $id_lang);
	}

	public function renderByField($field_name, $id_lang = null)
	{
		if (!isset($id_lang))
			$id_lang = $this->context->language->id;

		$default_lang = $this->module->getConfiguration('PS_LANG_DEFAULT');

		$content = '';
		if (isset($this->{$field_name}[$id_lang]))
			$content = $this->{$field_name}[$id_lang];
		else
			$content = $this->{$field_name}[$default_lang];

		$content_final = $this->getRenderedContent($content, $id_lang);

		return $content_final;
	}

	public function getRenderedContent($content, $id_lang)
	{
		$content_final = $content;
		$conditions    = $this->getConditions($content);
		$template_vars = $this->getTemplateVars($id_lang);

		foreach ($conditions as $var => $value)
		{
			if (isset($template_vars[$var]))
			{
				if ($template_vars[$var])
					$content_final = str_replace($value['match'], $value['replace'], $content_final);
				else
					$content_final = str_replace($value['match'], '', $content_final);
			}
		}
		// replace vars
		$content_final = $this->replaceVars($content_final, $template_vars);
		return $content_final;
	}

	public function replaceVars($template, $variables = array())
	{
		self::$replace_vars = $variables;
		$template = preg_replace_callback('/\{(?P<tag>\w+)\}/', 
					array($this, 'replaceCallback'),
					$template);
		return $template;
	}

	public function replaceCallback($matches)
	{
		$tag = $matches[1]; // tag
		return (isset(self::$replace_vars[$tag])) ? self::$replace_vars[$tag] : '{'.$tag.'}';
	}

	private function getConditions($content)
	{
		$conditions = array();
		if (preg_match_all('/(?P<all>\{if\s(?:\s+)?(?P<variables>\w+[^}])\}(?P<if>[\s\S]*?)\{\/if\})/', $content, $matches))
		{
			$variables = $matches['variables'];
			$if        = $matches['if'];
			$all       = $matches['all'];

			foreach ($variables as $key => $variable)
			{
				if (isset($if[$key]))
				$conditions[$variable] = array(
					'match'   => $all[$key],
					'replace' => $if[$key],
				);
			}
		}
		return $conditions;
	}

	public function getTemplateVars($id_lang)
	{
		$voucher = $this->getVoucher();

		$voucher_vars = array(
			'voucher'             => $voucher,
			'voucher_name'        => false,
			'voucher_quantity'    => false,
			'voucher_value'       => false,
		);

		if ($voucher && ($id_cart_rule = CartRule::getIdByCode($voucher)))
		{
			$cart_rule = new CartRule($id_cart_rule);
			if (Validate::isLoadedObject($cart_rule))
			{
				// if the name language exists, other values exists alsow
				if (isset($cart_rule->name[$id_lang]))
					$voucher_vars['voucher_name']        = $cart_rule->name[$id_lang];

				$voucher_vars['voucher_quantity']    = $cart_rule->quantity;

				if ($cart_rule->reduction_percent > 0)
					$voucher_vars['voucher_value'] = $cart_rule->reduction_percent.'%';
				else if ($cart_rule->reduction_amount > 0)
				{
					if ((int)$this->context->currency->id != (int)$cart_rule->reduction_currency)
						$price_convert = Tools::convertPrice((float)$cart_rule->reduction_amount, (int)$cart_rule->reduction_currency, false);
					else
						$price_convert = (float)$cart_rule->reduction_amount;

					$price_display = Tools::displayPrice($price_convert);
					$voucher_vars['voucher_value'] = $price_display;
				}
			}
		}

		$shop_url           = $this->context->link->getPageLink('index', null, $id_lang, array(), false, (int)$this->context->shop->id);
		$shop_name          = $this->context->shop->name;
		$shop_logo_url      = $this->module->getShopLogoUrl();
		$shop_logo          = '<a title="'.$this->context->shop->name.'" href="'.$shop_url.'"> <img style="border: none;" src="'.$shop_logo_url.'" alt="'.$shop_name.'" /> </a>';

		$vars = array(
			'displayGender'                  => ($this->display_gender ? $this->getGenderHTML() : false),
			'displayFirstName'               => ($this->display_firstname ? $this->getFirstNameHTML() : false),
			'displayLastName'                => ($this->display_lastname ? $this->getLastNameHTML() : false),
			'displayEmail'                   => $this->getEmailHTML(),
			'displayLanguages'               => ($this->display_language ? $this->getLanguagesHTML() : false),
			'displayBirthday'                => ($this->display_birthday ? $this->getBirthdayHTML() : false),
			'displayInfo'                    => $this->getInfoHTML(),
			'displayListOfInterest'          => ($this->display_list_of_interest ? $this->getListOfInterestHTML() : false),
			'submitButton'                   => $this->getSubmitHTML(),

			'displayTermsAndConditionsLink'     => $this->getTermsAndConditionsLinkHTML(),
			'displayTermsAndConditionsCheckbox' => $this->getTermsAndConditionsCheckboxHTML(),
			'displayTermsAndConditionsFull'     => $this->getTermsAndConditionsFullHTML(),

			'shop_url'                       => $shop_url,
			'shop_name'                      => $shop_name,
			'shop_logo_url'                  => $shop_logo_url,
			'shop_logo'                      => $shop_logo,

			'close_forever'                  => $this->getCloseForeverHTML(),
			'close_forever_onclick_function' => 'NewsletterPro.modules.newsletterSubscribe.closeForever();',

			'module_url'					 => Tools::getHttpHost(true).$this->module->uri_location,
			'module_path'					 => $this->module->uri_location,
		);

		$custom_vars = array();

		$variables_name = NewsletterProSubscribersCustomField::getVariables();

		foreach ($variables_name as $variable_name) 
		{
			$field = NewsletterProSubscribersCustomField::getInstanceByVariableName($variable_name);
			if ($field)
				$custom_vars[$variable_name] = $field->render();
		}

		$all_vars = array_merge($vars, $voucher_vars, $this->extend_vars, $custom_vars);

		return $all_vars;
	}

	public function extendVars($vars)
	{
		$this->extend_vars = $vars;
	}

	public function getVoucher()
	{
		if (isset($this->voucher) && !NewsletterProTools::isEmpty($this->voucher))
			return $this->voucher;
		return false;
	}

	public function getStyle()
	{
		$style = '';
		$style .= '<style type="text/css">';
		$style .= $this->css_style;
		$style .= '</style>'."\n";

		return $style;
	}

	public function getStyleLink()
	{
		return '<link rel="stylesheet" type="text/css" href="'.$this->getSubscriptionCSSLink().'">'."\n";
	}

	public function getSubscriptionCSSLinkWithDetails($id_shop = null)
	{
		if (!isset($id_shop))
			$id_shop = (int)$this->context->shop->id;

		$css_filename = $this->getCSSStyleFileName($id_shop);
		$full_path = $this->css_dir_path.'/'.$css_filename;

		$link        = '';
		$file_exists = false;

		if (file_exists($full_path) && is_file($full_path) && is_readable($full_path))
		{
			$file_exists = true;
			$link = $this->css_uri_path.'/'.$css_filename;
		}
		else
		{
			$file_exists = false;
			$link = _MODULE_DIR_.'newsletterpro/css.php?getSubscriptionCSS&idTemplate='.(int)$this->id.'&idShop='.(int)$id_shop.'&uid='.uniqid();
		}

		return array(
			'link'        => $link,
			'file_exists' => (bool)$file_exists,
		);
	}

	public function getSubscriptionCSSLink($id_shop = null)
	{
		$details = $this->getSubscriptionCSSLinkWithDetails($id_shop);
		return $details['link'];
	}

	public function saveCSSStyleAsFile($content, $id_shop = null)
	{
		if (!isset($id_shop))
			$id_shop = (int)$this->context->shop->id;

		if (file_exists($this->css_dir_path))
		{
			$css_filename = $this->getCSSStyleFileName($id_shop);

			$full_path = $this->css_dir_path.'/'.$css_filename;

			$h = @fopen($full_path, 'w');
			$success = fwrite($h, $content);
			fclose($h);
			return $success;
		}
		return false;
	}

	public function getCSSStyleFileName($id_shop = null)
	{
		if (!isset($id_shop))
			$id_shop = (int)$this->context->shop->id;

		return $this->name.'.'.$this->id.'.'.$id_shop.'.css';
	}

	/**
	 * this should be use only in the bakcoffice
	 * @param  boolean $get_link
	 * @return string
	 */
	public function getGlobalStyle($get_link = true)
	{
		$style = '';
		foreach (array_keys($this->getFrontOfficeCSSFiles()) as $path)
			$style .= '<link rel="stylesheet" type="text/css" href="'.$path.'">'."\n";

		$info = $this->getCssGlobalStyleInfo();
		$style .= '<link rel="stylesheet" type="text/css" href="'.$info['full_path'].'">'."\n";

		if ($get_link)
			$style .= $this->getStyleLink();
		else
			$style .= $this->getStyle();

		return $style;
	}

	public function getGlobalStyleLinks($uniqid = false)
	{
		$links = array();
		foreach (array_keys($this->getFrontOfficeCSSFiles()) as $path)
		{
			if ($uniqid)
				$links[] = $path.'?uid='.uniqid();
			else
				$links[] = $path;
		}

		$info = $this->getCssGlobalStyleInfo();

		$links[] = $info['full_path'];
		$links[] = $this->getSubscriptionCSSLink();

		return $links;
	}

	/**
	 * this should be use only in the bakcoffice
	 * @param  boolean $js_regex
	 * @param  boolean $get_link
	 * @return string
	 */
	public function getGlobalHeader($js_regex = false, $get_link = true)
	{
		$output = '';

		$controller_header = $this->getFrontControllerHeader();

		$info = $this->getCssGlobalStyleInfo();
		$output .= '<link rel="stylesheet" type="text/css" href="'.$info['full_path'].'">'."\n";

		if ($controller_header)
		{
			$css_files = $controller_header['css_files'];
			$header    = $controller_header['header'];
			$js_files  = $controller_header['js_files'];

			foreach (array_keys($css_files) as $path)
				$output .= '<link rel="stylesheet" type="text/css" href="'.$path.'">'."\n";

			if ($get_link)
				$output .= $this->getStyleLink();
			else
				$output .= $this->getStyle();

			if ($js_regex)
			{
				$grep = preg_grep($js_regex, $js_files);

				foreach ($grep as $path)
					$output .= '<script type="text/javascript" src="'.$path.'"></script>'."\n";
			}
			else
			{
				foreach ($js_files as $path)
					$output .= '<script type="text/javascript" src="'.$path.'"></script>'."\n";
			}

			$output .= $header;
		}
		else
		{
			$css_files = array();
			$css_files[] = _THEME_CSS_DIR_.'grid_prestashop.css';
			$css_files[] = _THEME_CSS_DIR_.'global.css';

			foreach (array_keys($css_files) as $path)
				$output .= '<link rel="stylesheet" type="text/css" href="'.$path.'">'."\n";

			if ($get_link)
				$output .= $this->getStyleLink();
			else
				$output .= $this->getStyle();
		}

		return $output;
	}

	public function getFrontOfficeCSSFiles()
	{
		$css_files = array();

		$controller_header = $this->getFrontControllerHeader();

		if ($controller_header)
			$css_files = $controller_header['css_files'];
		else
		{
			$css_files[] = _THEME_CSS_DIR_.'grid_prestashop.css';
			$css_files[] = _THEME_CSS_DIR_.'global.css';
		}

		return $css_files;
	}

	private function getThemeDir()
	{
		return $this->useMobileTheme() ? _PS_THEME_MOBILE_DIR_ : _PS_THEME_DIR_;
	}

	private function useMobileTheme()
	{
		static $use_mobile_template = null;

		if ($use_mobile_template === null)
			$use_mobile_template = ($this->context->getMobileDevice() && file_exists(_PS_THEME_MOBILE_DIR_.'layout.tpl'));

		return $use_mobile_template;
	}

	public function getFrontControllerHeader()
	{
		try
		{
			$css_module_path = $this->module->getCssPath();
			$css_module_path_default = $this->module->getCssPath(true);
			
			$this->context->cookie   = new Cookie('ps');
			$this->context->controller = new FrontController();
			$this->context->customer = new Customer();
			$this->context->cart     = new Cart();

			if (pqnp_ini_config('load_subscription_front_controller') == 1)
			{
				try
				{
					if (method_exists($this->context->controller, 'init'))
						$this->context->controller->init();
						
					if (method_exists($this->context->controller, 'setMedia'))
						$this->context->controller->setMedia();
				}
				catch(Exception $e)
				{
					// do nothing
				}

			}
			else if (pqnp_ini_config('load_subscription_front_controller') == 2)
			{
				try
				{
					$this->context->controller->addCSS(_THEME_CSS_DIR_.'grid_prestashop.css', 'all');  // retro compat themes 1.5.0.1
					$this->context->controller->addCSS(_THEME_CSS_DIR_.'global.css', 'all');
					$this->context->controller->addJquery();
					$this->context->controller->addJqueryPlugin('easing');
					$this->context->controller->addJS(_PS_JS_DIR_.'tools.js');
					$this->context->controller->addJS(_THEME_JS_DIR_.'global.js');

					if (@filemtime($this->getThemeDir().'js/autoload/'))
						foreach (scandir($this->getThemeDir().'js/autoload/', 0) as $file)
							if (preg_match('/^[^.].*\.js$/', $file))
								$this->context->controller->addJS($this->getThemeDir().'js/autoload/'.$file);

					if (@filemtime($this->getThemeDir().'css/autoload/'))
						foreach (scandir($this->getThemeDir().'css/autoload', 0) as $file)
							if (preg_match('/^[^.].*\.css$/', $file))
								$this->context->controller->addCSS($this->getThemeDir().'css/autoload/'.$file);

					if (Configuration::get('PS_QUICK_VIEW'))
						$this->context->controller->addjqueryPlugin('fancybox');

					if (Configuration::get('PS_COMPARATOR_MAX_ITEM') > 0)
						$this->context->controller->addJS(_THEME_JS_DIR_.'products-comparison.js');

					// Execute Hook FrontController SetMedia
					Hook::exec('actionFrontControllerSetMedia', array());
				}
				catch (Exception $e)
				{
					// do nothing
				}
			}

			if (pqnp_ini_config('load_subscription_hook_header'))
				$header = Hook::exec('displayHeader');
			else 
				$header = '';

			$css_files = $this->context->controller->css_files;
			$js_files = $this->context->controller->js_files;


			if (NewsletterPro::isUniformRequired())
				$css_files[$css_module_path_default.'uniform.default.css'] = 'all';

			if (NewsletterPro::isFontAwesomeRequired())
				$css_files[$css_module_path_default.'font-awesome.css'] = 'all';

			$css_files[$css_module_path.'front_window.css'] = 'all';

			// filter the undesired scripts that can cause an error
			$js_files = preg_grep('/modules\/newsletterpro|jquery|bootstrap\.min\.js|jquery\.uniform\.min\.js/', $js_files);

			$js_files = array_merge($js_files, array(
				Media::getJSPath($this->module->uri_location.'views/js/newsletter_subscribe.js'),
				Media::getJSPath($this->module->uri_location.'views/js/front_window.js'),
			));

			// restore the controller
			$this->context->cookie     = new Cookie('psAdmin');
			$this->context->controller = Controller::getController($this->module->class_name);

			try
			{
				$this->context->controller->init();
				if (method_exists($this->context->controller, 'initToolbar'))
					$this->context->controller->initToolbar();
				if (method_exists($this->context->controller, 'initPageHeaderToolbar'))
					$this->context->controller->initPageHeaderToolbar();
				if (method_exists($this->context->controller, 'setMedia'))
					$this->context->controller->setMedia();
				if (method_exists($this->context->controller, 'initHeader'))
					$this->context->controller->initHeader();
				if (method_exists($this->context->controller, 'initFooter'))
					$this->context->controller->initFooter();
			}
			catch (Exception $e)
			{
				NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
			}

			return array(
				'header'    => $header,
				'css_files' => $css_files,
				'js_files'  => $js_files,
			);
		}
		catch(Exception $e)
		{
			NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
			return false;
		}
		return false;
	}

	public static function templateIdExists($id)
	{
		if (Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_subscription_tpl`
			FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl`
			WHERE `id_newsletter_pro_subscription_tpl` = '.(int)$id.'
		'))
			return (int)$id;
		else
			return false;
	}

	public function getCssGlobalStyleInfo()
	{
		$info = pathinfo(self::CSS_STYLE_GLOBAL_PATH);
		return array(
			'path'          => $this->module->getCssPath(),
			'dir_path'      => $this->module->getCssDirPath(),
			'dir_path_full' => $this->module->getCssDirPath().$info['basename'],
			'full_path'     => $this->module->getCssPath().$info['basename'],
			'basename'      => $info['basename'],
			'extension'     => $info['extension'],
			'filename'      => $info['filename'],
		);
	}

	public function getCssGlobalStyleContent()
	{
		$info = $this->getCssGlobalStyleInfo();
		$filename = $info['dir_path'].$info['basename'];
		if (file_exists($filename))
		{
			$content = Tools::file_get_contents($filename);
			if ($content !== false)
				return $content;
		}
		return '';
	}

	public function getTermsAndConditionsLinkHTML()
	{
		$url = trim($this->terms_and_conditions_url);

		return $this->fetchComponent(array(
			'get_terms_and_conditions_link' => true,
			'terms_and_conditions_url' => (!empty($url) ? $url : '#'),
		));
	}

	public function getTermsAndConditionsCheckboxHTML()
	{
		return $this->fetchComponent(array(
			'get_terms_and_conditions_checkbox' => true
		));
	}

	public function getTermsAndConditionsFullHTML()
	{
		return $this->fetchComponent(array(
			'get_terms_and_conditions_full' => true,
			'gtac_link' => $this->getTermsAndConditionsLinkHTML(),
			'gtac_checkbox' => $this->getTermsAndConditionsCheckboxHTML(),
		));
	}

	public function getGenderHTML()
	{
		return $this->fetchComponent(array(
			'get_gender' => true,
			'genders'	=> Gender::getGenders(),
		));
	}

	public function getFirstNameHTML()
	{
		return $this->fetchComponent(array(
			'get_firstname' => true,
		));
	}

	public function getLastNameHTML()
	{
		return $this->fetchComponent(array(
			'get_lastname' => true,
		));
	}

	public function getLanguagesHTML()
	{
		$languages = $this->module->getLanguages();

		return $this->fetchComponent(array(
			'get_languages' => true,
			'langs_sub'     => $languages,
		));
	}

	public function getBirthdayHTML()
	{
		return $this->fetchComponent(array(
			'get_birthday' => true,
			'years'        => Tools::dateYears(),
			'months'       => $this->module->dateMonths(),
			'days'         => Tools::dateDays(),
		));
	}

	public function getListOfInterestHTML()
	{
		return $this->fetchComponent(array(
			'get_list_of_interest'           => true,
			'list_of_interest_type'          => $this->list_of_interest_type,
			'LIST_OF_INTEREST_TYPE_SELECT'   => self::LIST_OF_INTEREST_TYPE_SELECT,
			'LIST_OF_INTEREST_TYPE_CHECKBOX' => self::LIST_OF_INTEREST_TYPE_CHECKBOX,
			'list_of_interest'               => NewsletterProListOfInterest::getListActive(),
		));
	}

	public function getEmailHTML()
	{
		return $this->fetchComponent(array(
			'get_email' => true,
		));
	}

	public function getSubmitHTML()
	{
		return $this->fetchComponent(array(
			'get_submit' => true,
		));
	}

	public function getCloseForeverHTML()
	{
		return $this->fetchComponent(array(
			'get_close_forever' => true,
		));
	}

	public function getInfoHTML()
	{
		return $this->fetchComponent(array(
			'get_info' => true,
		));
	}

	public function fetchComponent($params = array())
	{
		$tpl = $this->context->smarty->createTemplate(pqnp_template_path($this->module->dir_location.'views/templates/hook/newsletter_subscribe_components.tpl'));
		$tpl->assign($params);
		return $tpl->fetch();
	}

	public static function getActiveTemplateInstance()
	{
		$context = Context::getContext();

		$sql = '
			SELECT s.`id_newsletter_pro_subscription_tpl`
			FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl` s
			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_subscription_tpl_shop` ss
				ON (s.`id_newsletter_pro_subscription_tpl` = ss.`id_newsletter_pro_subscription_tpl`)
			WHERE ss.`active` = 1
			AND ss.`id_shop` = '.(int)$context->shop->id.'
		';

		$id = Db::getInstance()->getValue($sql);

		if (!$id)
		{
			$sql = '
				SELECT s.`id_newsletter_pro_subscription_tpl`
				FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl` s
				WHERE s.`active` = 1
			';
			$id = Db::getInstance()->getValue($sql);
		}

		if ($id)
			return new NewsletterProSubscriptionTpl((int)$id);
		else
		{
			self::setActiveIfNotExists();
			$id = Db::getInstance()->getValue($sql);
			if ($id)
				return new NewsletterProSubscriptionTpl((int)$id);
		}

		return false;
	}

	public function getJSData()
	{
		$one_day = 60 * 60 * 24;
		$life_time_days = round($one_day * (float)$this->cookie_lifetime);

		$cookie = new NewsletterProCookie('subscription_template_front', time() + $life_time_days);

		if (!$cookie->exists('popup_show'))
			$cookie->set('popup_show', '1');

		$popup_show = (int)$cookie->get('popup_show');

		$page_name = (Tools::getValue('controller') ? Tools::getValue('controller') : $this->context->controller->php_self);

		// if the page is a cms page
		if ($page_name == 'cms' && Tools::isSubmit('id_cms') && Tools::getValue('id_cms') && preg_match('/cms-\d+/', $this->show_on_pages))
		{
			$id_cms = (int)Tools::getValue('id_cms');
			if (Tools::strtolower('cms'.'-'.$id_cms) == Tools::strtolower($this->show_on_pages))
				$page_name = $this->show_on_pages;
		}

		$bool_show_on_page = 0;
		if ($page_name == $this->show_on_pages)
			$bool_show_on_page = 1;
		else if ($this->show_on_pages == self::SHOW_ON_PAGES_ALL)
			$bool_show_on_page = 1;
		else if ($this->show_on_pages == self::SHOW_ON_PAGES_NONE)
			$bool_show_on_page = 0;

		if ((int)$this->when_to_show == self::WHEN_TO_SHOW_POPUP_ALWAYS)
			$display_popup = ($bool_show_on_page ? true : false);
		else
			$display_popup = ($popup_show && $bool_show_on_page ? true : false);

		return pqnp_addcslashes(Tools::jsonEncode(array(
			'subscription_template_front_info' => array(
				'body_width'           => $this->body_width,
				'body_min_width'       => (int)$this->body_min_width,
				'body_max_width'       => (int)$this->body_max_width,
				'body_top'             => (int)$this->body_top,
				
				'show_on_pages'        => $this->show_on_pages,
				'cookie_lifetime'      => (float)$this->cookie_lifetime,
				'start_timer'          => (int)$this->start_timer,
				'when_to_show'         => (int)$this->when_to_show,
				
				'bool_show_on_page'    => $bool_show_on_page,
				'popup_show_cookie'    => $popup_show,
				'display_popup'        => $display_popup,
				'close_forever' => (int)pqnp_ini_config('close_forever')
			),

			'configuration'			=> array(
				'CROSS_TYPE_CLASS'      => NewsletterPro::getConfiguration('CROSS_TYPE_CLASS'),
			),

		)));
	}

	public static function getFrontLink($id_lang = null, $id_shop = null)
	{
		$context = Context::getContext();

		$link = $context->link->getPageLink('index', null, $id_lang, array('newsletterproSubscribe' => 1), false, $id_shop);
		return $link;
	}

	public static function getPagesAsMeta()
	{
		$selected_pages = array();
		$context = Context::getContext();

		$path = _PS_ROOT_DIR_.'/controllers/front/';
		if (file_exists($path))
		{

			$dirs = new DirectoryIterator($path);
			$filesi = new RegexIterator($dirs, '/.php$/', RecursiveRegexIterator::MATCH);

			$files = array();
			foreach ($filesi as $file)
			{
				if (method_exists($file, 'getBasename'))
					$files[] = $file->getBasename();
				else
					$files[] = basename($file->getFilename());
			}

			$exlude_pages = array(
				'category', 'changecurrency', 'cms', 'footer', 'header',
				'pagination', 'product', 'product-sort', 'statistics'
			);

			foreach ($files as $file)
				if ($file != 'index.php' && preg_match('/^[a-z0-9_.-]*\.php$/i', $file) && !in_array(Tools::strtolower(str_replace('Controller.php', '', $file)), $exlude_pages))
					$selected_pages[Tools::strtolower(str_replace('Controller.php', '', $file))] = Tools::strtolower(str_replace('Controller.php', '', $file));
				else if ($file != 'index.php' && preg_match('/^([a-z0-9_.-]*\/)?[a-z0-9_.-]*\.php$/i', $file) && !in_array(Tools::strtolower(str_replace('Controller.php', '', $file)), $exlude_pages))
					$selected_pages[Tools::strtolower(sprintf(Tools::displayError('%1$s (in %2$s)'), dirname($file), str_replace('Controller.php', '', basename($file))))] = Tools::strtolower(str_replace('Controller.php', '', basename($file)));

			// Add modules controllers to list (this function is cool !)
			foreach (glob(_PS_MODULE_DIR_.'*/controllers/front/*.php') as $file)
			{
				$filename = basename($file, '.php');
				if ($filename == 'index')
					continue;

				$module = basename(dirname(dirname(dirname($file))));
				$selected_pages[$module.' - '.$filename] = 'module-'.$module.'-'.$filename;
			}

			foreach (CMS::listCms($context->language->id) as $value) 
				$selected_pages['CMS'.' - '.$value['meta_title']] = 'cms-'.$value['id_cms'];
		}

		return $selected_pages;
	}

	public static function getPages()
	{
		$result = array();
		try
		{
			$selected_pages = self::getPagesAsMeta();

			foreach ($selected_pages as $key => $value)
			{
				$result[] = array(
					'title' => Tools::ucfirst(preg_replace('/\s+/', ' ', str_replace('-', ' ', trim($key)))),
					'value' => $value,
				);
			}
		}
		catch(Exception $e)
		{
			NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
		}
		return $result;
	}
}