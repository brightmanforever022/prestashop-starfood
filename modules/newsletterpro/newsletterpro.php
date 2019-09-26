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

if (!defined('_PS_VERSION_'))
	exit;

require_once dirname(__FILE__).'/config/config.inc.php';

class NewsletterPro extends Module
{
	public $configuration           = array();
	public $url_location            = '';
	public $uri_location            = '';
	public $dir_location            = '';
	public $tpl_location            = '';
	public $lang_img_path           = '';
	public $lang_img_dir            = '';
	public $class_name              = 'AdminNewsletterPro';
	public $menu_name               = 'Newsletter Pro';
	public $ajax_file_name          = 'ajax_newsletterpro.php';
	public $id_parent               = 11;
	public $token                   = '';
	public $link                    = '';
	public $default_campaign_params = '';

	/**
	 * The value needs to be changed from the file config.ini
	 * @var integer
	 */
	public $step;

	public $my_account_url          = '';
	public $ajax_destination 		= '';
	public $subscribe_hooks         = array('displayFooter', 'displayRightColumn', 'displayLeftColumn');

	public $chimp;

	/* ini configuration */
	public $uninstall_all_tables	= false;

	public $ini_config;

	public $demo_mode;

	public static $replace_vars;
	public static $instance;
	
	public $embed_images_message;

	public $context;

	const SEND_BOOSTER = true;

	const BACKUP_TYPE = 'xml';

	const BACKUP_TYPE_XML = 1;

	const BACKUP_TYPE_SQL = 2;

	/**
	 * This constants need to be changed also in the file init.js
	 */
	const SEND_METHOD_DEFAULT = 0;

	const SEND_METHOD_ANTIFLOOD = 1;

	const SEND_THROTTLER_TYPE_EMAILS = 0;

	const SEND_THROTTLER_TYPE_MB = 1;

	/* the value is md5(newsletterpro) */
	const NEWSLETTER_PRO_KEY = '5a112de88bead4ef8eeb07825d5af6e1';

	/* 
	* if the opton is false, you can import emails that already exists into ps_customer into the table ps_newsletter_pro_email, when you are importing a csv file 
	*/
	const CSV_IMPORT_STRICT = true;

	const LIST_CUSTOMERS = 101;
	
	const LIST_VISITORS = 102;

	const LIST_VISITORS_NP = 103;

	const LIST_ADDED = 104;

	const SEARCH_CONDITION_CONTAINS = 100;

	const SEARCH_CONDITION_IS = 101;
	
	const SEARCH_CONDITION_IS_NOT = 102;

	const SEARCH_CONDITION_GREATER = 103;

	const SEARCH_CONDITION_LESS = 104;
	
	const REPLACE_ADMIN_PATH = true;

	/**
	 * Test function
	 */
	public function dev()
	{

	}

	/**
	 * Init
	 */
	public function __construct()
	{
		self::$instance =& $this;

		$this->name                   = 'newsletterpro';
		$this->tab                    = 'advertising_marketing';
		$this->version                = '4.5.2';
		$this->author                 = 'ProQuality';
		$this->module_key             = 'cd3f5ac1a07c62f20ba41465a387a8de';
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.7');

		if (version_compare(_PS_VERSION_, '1.6.0.1', '>='))
			$this->bootstrap = true;
		else
			$this->bootstrap = false;

		parent::__construct();

		$this->displayName      = $this->l('Newsletter Pro');
		$this->description      = $this->l('Advertise selected products from your store via newsletter.');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall this module? All data will be lost. If you desire you can create a backup from the module "Settings" tab.');

		if (defined('_PS_ADMIN_DIR_'))
			$this->admin_name = basename(_PS_ADMIN_DIR_);
		else
			$this->admin_name = '';

		$this->displayCompatibilityError();

		try
		{
			// this will protect the front office
			$module_dir  = $this->relplaceAdminLink(_MODULE_DIR_);
			$ps_img      = $this->relplaceAdminLink(_PS_IMG_);
			$this->_path = $this->relplaceAdminLink($this->_path);

			$this->initContext();
			$this->initInitConfiguration();
			$this->initConfiguration();

			$this->url_location = Tools::getHttpHost(true).$module_dir.$this->name.'/';
			$this->uri_location = $module_dir.$this->name.'/';

			$this->dir_location = _PS_MODULE_DIR_.$this->name.'/';
			$this->tpl_location = _PS_MODULE_DIR_.$this->name.'/mail_templates/';
			$this->lang_img_path = Tools::getHttpHost(true).$ps_img.'l/';
			$this->lang_img_dir = _PS_IMG_DIR_.'l/';

			if (defined('_PS_ADMIN_DIR_'))
				$this->ajax_destination = $this->isHigherVersion('1.6.0.4') ? _PS_ADMIN_DIR_.'/filemanager/' : _PS_ADMIN_DIR_.'/ajaxfilemanager/';

			$this->my_account_url = $this->context->link->getModuleLink($this->name, 'myaccount');

			$this->default_campaign_params = array( 'UTM_SOURCE'   => 'Newsletter',
													'UTM_MEDIUM'   => 'email',
													'UTM_CAMPAIGN' => '{newsletter_title}',
													'UTM_CONTENT'  => '{product_name}',
												);

			$this->token = Tools::getAdminToken( $this->class_name.(int)Tab::getIdFromClassName($this->class_name).(int)$this->context->cookie->id_employee);
			$this->link = 'index.php?controller='.$this->class_name.'&token='.$this->token;

			$this->initChimp();

			$this->upgrade = new NewsletterProUpgrade();

			$this->dev();
		}
		catch(Exception $e)
		{
			NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
			$this->exception_message = $e->getMessage();
		}
	}

	public static function getNewsletterProToken()
	{
		return Tools::encrypt(NewsletterPro::NEWSLETTER_PRO_KEY);
	}

	/**
	 * Display the compatibility problems for older versions of prestashop
	 */
	private function displayCompatibilityError()
	{
		if (Tools::isSubmit('controller') && Tools::strtolower(Tools::getValue('controller')) == Tools::strtolower('AdminNewsletterPro'))
		{
			if (version_compare(_PS_VERSION_, '1.5.1.0', '<=') && (!method_exists('Db', 'update') || !method_exists('Db', 'insert') || !method_exists('Db', 'delete')))
			{
				die(Tools::displayError(
					sprintf($this->l('You have to override the class "%s" from the folder "%s" into the folder "%s" in order to work properly. If you have experience with PHP please ask the developer to do this.'), 'Db.php', '/newsletterpro/compatibility/', '/override/classes/db/')
				));
			}

			if (version_compare(_PS_VERSION_, '1.5.1.0', '<='))
			{
				$reflection_error = sprintf($this->l('You need to change the property "%s" from private to protected variable, from the file "%s".'), '$asso_tables', 'Shop.php');

				$reflection_class = new ReflectionClass('Shop');

				try
				{
					$property = $reflection_class->getProperty('asso_tables');

					if ($property->isPrivate())
						die(Tools::displayError($reflection_error));
				}
				catch(Exception $e)
				{
					die(Tools::displayError($reflection_error));
				}
			}
		}
	}

	/**
	 * Module installation
	 *
	 * @return bool
	 */
	public function install()
	{
		@ini_set('max_execution_time', 600);

		$success   = array();
		$success[] = parent::install()
					&& $this->installConfiguration()
					&& $this->installModuleTab('AdminNewsletterPro', 'Newsletter Pro')
					&& $this->registerHook('header')
					&& $this->registerHook('customerAccount')
					&& $this->registerHook('createAccount')
					&& $this->registerHook('actionShopDataDuplication')
					&& ($this->isLowerVersion('1.5.4.1') ? $this->installLang('id_lang') : true)
					&& ($this->isLowerVersion('1.5.0.4') ? $this->updateShopName((string)Configuration::get('PS_SHOP_NAME')) : true)
					&& $this->installFiles()
					&& $this->installTemplates();

		if (!Tools::isSubmit('reset'))
			$success[] = $this->installDb();

		return !in_array(false, $success);
	}

	/**
	 * Module desinstallation
	 *
	 * @return bool
	 */
	public function uninstall()
	{
		// 10 minutes if need regards the prestashop uninstallation time bug, in some cases
		@ini_set('max_execution_time', 600);

		$success = array();
		$success[] = parent::uninstall()
					&& $this->uninstallConfiguration()
					&& $this->uninstallAttachments()
					&& $this->uninstallModuleTab('AdminNewsletterPro');

		if (!Tools::isSubmit('reset'))
			$success[] = $this->uninstallDb();

		return !in_array(false, $success);
	}

	/**
	 * Initialize module configuration
	 */
	public function initInitConfiguration()
	{
		$file = _PS_MODULE_DIR_.$this->name.'/config.ini';
		if (file_exists($file))
		{
			try
			{
				$config = @parse_ini_file($file);

				if (!$config) {
					$config = array();
					
					$init_file = Tools::file_get_contents($file);
					if (preg_match_all('/^\w+\s+?=\s+?\w+/m', $init_file, $matches)) {
						$matches = $matches[0];

						foreach ($matches as $line) {
							$line_arr = explode('=', $line);
							$key = trim($line_arr[0]);
							$value = trim($line_arr[1]);

							if ($value === 'true') {
								$value = true;
							} else if ($value === 'false') {
								$value = false;
							} else {
								$value = (int)$value;
							}

							$config[$key] = $value;
						}
					}
				}

				$this->ini_config = $config;

				if (isset($config['uninstall_all_tables']))
					$this->uninstall_all_tables = $config['uninstall_all_tables'];

				if (isset($config['step']))
					$this->step = (int)$config['step'];
			}
			catch (Exception $e)
			{
				NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
			}
		}

		$file = _PS_MODULE_DIR_.$this->name.'/demo_mode.json';

		if (file_exists($file))
			if ((int)pqnp_ini_config('demo_mode'))
				$this->demo_mode = Tools::jsonDecode(Tools::file_get_contents($file), true);
	}

	/**
	 * Install prestashop files for the older versions of prestashop
	 *
	 * @return bool
	 */
	public function installFiles()
	{
		if (version_compare(_PS_VERSION_, '1.5.2.0', '<'))
		{
			$to_file = _PS_ROOT_DIR_.'/js/tiny_mce/plugins/';
			$destination = $to_file.'fullpage/';

			$zip_path = $this->dir_location.'install/tiny_mce/';
			$zip_file = $zip_path.'fullpage.zip';
			if (file_exists($zip_file))
			{
				$zip = new ZipArchive;
				if ($zip->open($zip_file) === true)
				{
					// create all folders
					for ($i = 0; $i < $zip->numFiles; $i++)
					{
						$only_file_name = $zip->getNameIndex($i);
						$full_file_name = $zip->statIndex($i);
						if ($full_file_name['name'][Tools::strlen($full_file_name['name']) - 1] == '/')
							@NewsletterProTools::createFolder($zip_path.$full_file_name['name']);
					}

					// unzip into the folders
					for ($i = 0; $i < $zip->numFiles; $i++)
					{
						$only_file_name = $zip->getNameIndex($i);
						$full_file_name = $zip->statIndex($i);

						if (!($full_file_name['name'][Tools::strlen($full_file_name['name']) - 1] == '/'))
							copy('zip://'.$zip_file.'#'.$only_file_name, $zip_path.$full_file_name['name']);
					}
					$zip->close();

					// unzip done
					// copy the folder only the folder does not exists
					if (file_exists($to_file) && !file_exists($destination))
					{
						$move_file = $this->dir_location.'install/tiny_mce/fullpage/';

						if (!file_exists($move_file))
						{
							$this->addError(sprintf($this->l('The tiny mce plugin "%s" not exists for instalation.'), 'fullpage'));
							return false;
						}
						@NewsletterProTools::recurseCopy($move_file, $destination);
					}
				}
				else
					$this->addError(sprintf($this->l('Failed to open file "%s"'), $zip_file));
			}
		}

		return true;
	}

	public function installTemplates()
	{
		$path = $this->tpl_location.'newsletter/';
		$result = NewsletterProTools::getDirectoryIterator($path, '/^[a-zA-Z0-9_-]+$/');
		$languages = Language::getLanguages(false);

		$index_file = $path.'index.php';

		try
		{
			foreach ($result as $file)
			{
				if ($file->isDir())
				{
					$folder_path = $file->getPathName();

					if (!$file->isWritable())
						throw new Exception(sprintf($this->l('The file "%s" is not writable, please check the CHMOD permissions.'), $folder_path));
						
					$file_name = $file->getFileName().'.html';
					$file_path = $folder_path.'/en/'.$file_name;

					foreach ($languages as $lang)
					{
						if ($lang['iso_code'] != 'en')
						{
							$new_iso_path = $folder_path.'/'.$lang['iso_code'];

							if (!file_exists($new_iso_path))
							{
								if (!mkdir($new_iso_path, 0777))
									throw new Exception(sprintf($this->l('Cannot create the directory "%s", please check the CHMOD permissions.'), $new_iso_path));
							}
								
							$copy_message = $this->l('Cannot copy the file "%s", please checked the CHMOD permissions.');
							$new_index = $new_iso_path.'/index.php';
							
							if (!copy($index_file, $new_index))
								throw new Exception(sprintf($copy_message, $new_index));
							
							$new_file_name = $new_iso_path.'/'.$file_name;

							if (!copy($file_path, $new_file_name))
								throw new Exception(sprintf($copy_message, $new_file_name));
						}
					}
				}
			}
		}
		catch(Exception $e)
		{
			$this->addError($e->getMessage());
			return false;
		}

		return true;
	}

	/**
	 * Get an instance of the module
	 *
	 * @return object
	 */
	public static function getInstance()
	{
		return self::$instance;
	}

	/**
	 * Initialize the MailChimp object
	 *
	 * @return object
	 */
	public function initChimp()
	{
		if (!isset($this->chimp))
		{
			if (Tools::isSubmit('api_key'))
			{
				$api_key = Tools::getValue('api_key');
				$list_id = Tools::getValue('list_id');

				$chimp_cfg =& $this->configuration['CHIMP'];
				$chimp_cfg['API_KYE'] = $api_key;
				$chimp_cfg['ID_LIST'] = $list_id;

				$this->updateDbConfiguration();
			}
			else
			{
				$chimp_config = $this->getConfiguration('CHIMP');
				if ($chimp_config && isset($chimp_config['API_KYE']))
					$api_key = $chimp_config['API_KYE'];
				else
					$api_key = '';
			}

			$this->chimp = new NewsletterProMailChimpController($api_key);
		}
	}

	/**
	 * Allow authentication with postman
	 * 
	 * This function will be placed in the file admin/init.php
	 * 
	 * $newsletterpro = Module::getInstanceByName('newsletterpro');
	 * if (Validate::isLoadedObject($newsletterpro))
	 *	 	$newsletterpro->allowAuthorization();
	 *
	 * You need to place in .htaccess file the lines
	 *
	 * RewriteEngine on
	 * 
	 * RewriteCond %{HTTP:Authorization} ^(.*)
	 * RewriteRule ^(.*) - [E=HTTP_AUTHORIZATION:%1]
	 */
	public function allowAuthorization()
	{
		if (function_exists('getallheaders'))
		{
			$headers = call_user_func('getallheaders');

			if (isset($headers['Authorization']) && !empty($headers['Authorization']))
			{
				$context = Context::getContext();

				$authorization = explode(' ', $headers['Authorization']);
				$auth = call_user_func_array('base64_decode', array($authorization[1]));
				$user = explode(':', $auth);

				if (isset($user[0], $user[1]))
				{
					$context->employee = new Employee();
					$context->employee = $context->employee->getByEmail($user[0], $user[1]);
					if (Validate::isLoadedObject($context->employee))
					{
						$context->employee->remote_addr = (int)ip2long(Tools::getRemoteAddr());
						// Update cookie
						$cookie = Context::getContext()->cookie;
						$cookie->id_employee = $context->employee->id;
						$cookie->email = $context->employee->email;
						$cookie->profile = $context->employee->id_profile;
						$cookie->passwd = $context->employee->passwd;
						$cookie->remote_addr = $context->employee->remote_addr;

						if (!Tools::getValue('stay_logged_in'))
							$cookie->last_activity = time();

						$cookie->write();
					}
				}
			}
		}
	}

	/**
	 * Get the MailChimp object
	 *
	 * @return object
	 */
	public function getChimp()
	{
		return $this->chimp;
	}

	/**
	 * Hook customer account
	 *
	 * @return string
	 */
	public function hookCustomerAccount()
	{
		if ((bool)pqnp_config('DISPLYA_MY_ACCOUNT_NP_SETTINGS'))
		{
			$this->context->smarty->assign(array(
				'url_location'   => $this->url_location,
				'my_account_url' => $this->my_account_url,
			));

			if ($this->isPS16())
				return $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/front/1.6/my_account_button.tpl'));
			else
				return $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/front/1.5/my_account_button.tpl'));
		}
		return '';
	}

	/**
	 * Get the module css path, according to the prestashop version
	 *
	 * @return string
	 */
	public function getCssPath($default = false)
	{
		$css_path = $this->uri_location.'views/css/';

		if ($default)
			return $css_path;

		if ($this->isLowerVersion('1.6.0.0'))
			$css_path = $this->uri_location.'views/css/1.5/';
		else
			$css_path = $this->uri_location.'views/css/1.6/';
		return $css_path;
	}

	/**
	 * Get css dir path
	 *
	 * @return string
	 */
	public function getCssDirPath()
	{
		$css_path = $this->dir_location.'views/css/';

		if ($this->isLowerVersion('1.6.0.0'))
			$css_path = $this->dir_location.'views/css/1.5/';
		else
			$css_path = $this->dir_location.'views/css/1.6/';
		return $css_path;
	}

	/**
	 * Id language exists
	 *
	 * @param  int $id_lang
	 * @return bool
	 */
	private function idLangExists($id_lang)
	{
		$languages = Language::getLanguages(true, $this->context->shop->id);
		foreach ($languages as $lang)
			if ($id_lang == $lang['id_lang'])
				return true;
		return false;
	}

	/**
	 * Id gender exists
	 *
	 * @param  int $id_gender
	 * @return bool
	 */
	private function idGenderExists($id_gender)
	{
		$genders = Gender::getGenders();
		foreach ($genders as $gender)
			if ($gender->id_gender == $id_gender)
				return true;
		return false;
	}

	/**
	 * Close front subscription window popup, according to the cookie lifetime
	 *
	 * @return json
	 */
	public function submitNewsletterProSubscribeCloseForever()
	{
		$response = new NewsletterProAjaxResponse();

		$subscrbtion_template = NewsletterProSubscriptionTpl::getActiveTemplateInstance();

		$one_day = 60 * 60 * 24;
		$life_time_days = round($one_day * (float)$subscrbtion_template->cookie_lifetime);

		$cookie = new NewsletterProCookie('subscription_template_front', time() + $life_time_days);

		$cookie->set('popup_show', '0');

		if ((bool)$cookie->get('popup_show') == true)
			$response->addError($this->l('An error occurred to proceed this action.'));

		return $response->display();
	}

	/**
	 * Submit the front subscription forum
	 *
	 * @return json
	 */
	public function submitNewsletterProSubscribe()
	{
		$response = new NewsletterProAjaxResponse(array(
			'msg' => '',
			'displaySubscribeMessage' => false,
		));

		try
		{
			$subscribe = new NewsletterProSubscribers();

			$subscribe->active = 1;
			$subscribe->firstname = Tools::getValue('firstname');
			$subscribe->lastname  = Tools::getValue('lastname');

			$email = trim(Tools::getValue('email'));

			if (!empty($email))
				$subscribe->email = $email;
			else
				$response->addError($this->l('The email address cannot be empty.'));

			$custom_variabels_hidden_keys = preg_grep('/^hidden_custom_variable_.*$/', array_keys($_POST));

			if (!empty($custom_variabels_hidden_keys))
			{
				$required_message = $this->l('You must fill all the required fields.');

				foreach ($custom_variabels_hidden_keys as $variable_name) 
				{
					$variable_name = str_replace('hidden_', '', $variable_name);
					$name = str_replace('custom_variable_', '', $variable_name);

					if (Tools::isSubmit($variable_name))
					{
						$value = Tools::getValue($variable_name);

						$field = NewsletterProSubscribersCustomField::getInstanceByVariableName($name);

						if ($field)
						{
							if (is_array($value) && $field->type == NewsletterProSubscribersCustomField::TYPE_CHECKBOX)
							{
								if (empty($value))
									$response->addError($required_message);

								$subscribe->{$name} = Tools::jsonEncode($value);
							}
							else if (is_string($value))
							{

								if ($field->isRequired())
								{
									$tr = Tools::strlen(trim($value));
									if ($tr == 0)
										$response->addError($required_message);
								}

								$subscribe->{$name} = $value;
							}
						}
					}
					else
					{
						$field = NewsletterProSubscribersCustomField::getInstanceByVariableName($name);

						if ($field)
						{
							if ($field->isRequired())
								$response->addError($required_message);
						}
					}
				}
			}

			$id_lang = $this->context->language->id;
			if (Tools::isSubmit('id_lang'))
			{
				$get_id_lang = Tools::getValue('id_lang');
				if ($this->idLangExists($get_id_lang))
					$id_lang = $get_id_lang;
			}

			if (Tools::isSubmit('id_gender'))
			{
				$id_gender = Tools::getValue('id_gender');
				if ($this->idGenderExists($id_gender))
					$subscribe->id_gender = (int)$id_gender;
			}

			$subscribe->id_lang = (int)$id_lang;

			$days   = trim(Tools::getValue('days'));
			$months = trim(Tools::getValue('months'));
			$years  = trim(Tools::getValue('years'));

			if (!empty($days) && !empty($months) && !empty($years))
			{
				$date = new DateTime();
				$date->setDate((int)$years, (int)$months, (int)$days);
				$subscribe->birthday = $date->format('Y-m-d');
			}

			if (Tools::isSubmit('list_of_interest'))
			{
				$list_of_interest = (int)Tools::getValue('list_of_interest');

				if ($list_of_interest)
					$subscribe->list_of_interest = $list_of_interest;
			}
			else
			{
				$loi_array = array();
				$post_keys = array_keys($_POST);
				$loi_grep = preg_grep('/^list_of_interest_\d+$/', $post_keys);

				foreach ($loi_grep as $key)
					if (Tools::isSubmit($key) && ($value = (int)Tools::getValue($key)))
						$loi_array[] = $value;

				$subscribe->list_of_interest = rtrim(implode(',', $loi_array), ',');
			}

			$subscrbtion_template = NewsletterProSubscriptionTpl::getActiveTemplateInstance();

			// check if the user is already registered to the newsletter
			if ($subscrbtion_template->allow_multiple_time_subscription)
			{
				$tables = array(
					'customer'                   => array('primary' => 'id_customer', 'active' => 'newsletter'),
					'newsletter_pro_subscribers' => array('primary' => 'id_newsletter_pro_subscribers', 'active' => 'active'),
				);

				if ((bool)pqnp_config('SUBSCRIPTION_ACTIVE'))
					$tables['newsletter_pro_email'] = array('primary' => 'id_newsletter_pro_email', 'active' => 'active');

				if (NewsletterProTools::tableExists('newsletter'))
					$tables['newsletter'] = array('primary' => 'id', 'active' => 'active');

				foreach ($tables as $table_name => $data)
				{	
					$sql = 'SELECT `'.pSQL($data['primary']).'` FROM `'._DB_PREFIX_.pSQL($table_name).'` WHERE `email` = "'.pSQL($subscribe->email).'" AND `'.pSQL($data['active']).'` = 1';
					if (Db::getInstance()->getValue($sql))
						$response->addError($this->l('The email address is already subscribed at our newsletter.'));
				}
			}

			if ($subscrbtion_template->mandatory_fields != null)
			{
				$mandatory_fields = NewsletterProTools::unSerialize($subscrbtion_template->mandatory_fields);

				if (!empty($mandatory_fields))
				{
					if (in_array('firstname', $mandatory_fields))
					{
						$fn = trim($subscribe->firstname);
						if (empty($fn) || !Validate::isName($fn))
							$response->addError($this->l('The First Name field is required.'));
					}

					if (in_array('lastname', $mandatory_fields))
					{
						$ln = trim($subscribe->lastname);
						if (empty($ln) || !Validate::isName($ln))
							$response->addError($this->l('The Last Name field is required.'));
					}
				}
			}

			$voucher = $subscrbtion_template->getVoucher();

			if ($response->success())
			{
				if ($this->getConfiguration('SUBSCRIPTION_SECURE_SUBSCRIBE'))
				{
					$subscribe_temp = new NewsletterProSubscribersTemp();
					$subscribe_temp->id_newsletter_pro_subscription_tpl = $subscrbtion_template->id;

					if (!$subscribe_temp->saveTemp($subscribe))
						$response->mergeErrors($subscribe_temp->getErrors());
					else
					{
						$link = $this->context->link->getModuleLink('newsletterpro', 'subscribeconfirmation', array('token' => $subscribe_temp->token));

						$subscrbtion_template->extendVars(array(
							'firstname' => htmlentities(Tools::getValue('firstname')),
							'lastname' => htmlentities(Tools::getValue('lastname')),
							'email_confirmation_link' => $link,
							'email_confirmation'	=> '<a href="'.$link.'" style="color: blue">'.$this->l('here').'</a>',
						));

						$secure_subscribe_msg = trim($subscrbtion_template->renderEmailSubscribeConfirmationMessage((int)$this->context->language->id));
						$secure_subscribe_msg_strip = trim(strip_tags($secure_subscribe_msg));

						$email_title = $this->l('Newsletter Subscription Confirmation');
						$email_body = '';

						if (!empty($secure_subscribe_msg_strip))
							$email_body = $secure_subscribe_msg;
						else
						{
							$file_tpl = dirname(__FILE__).'/views/templates/front/newsletter_subscribe.tpl';
							$this->context->smarty->assign(array(
								'confirmation_link' => $link
							));
							$content = $this->context->smarty->fetch($file_tpl);

							$template = NewsletterProTemplate::newString(array('', $content), $subscribe->email)->load();
							$message = $template->message();

							$email_title = $message['title'];
							$email_body = $message['body'];
						}

						if (NewsletterPro::getConfiguration('DEBUG_MODE'))
							$send = NewsletterProSendManager::getInstance()->sendNewsletter($email_title, $email_body, $subscribe_temp->email, array(), array(), false);
						else
							$send = @NewsletterProSendManager::getInstance()->sendNewsletter($email_title, $email_body, $subscribe_temp->email, array(), array(), false);

						if (is_array($send))
							$response->addError($this->l('An error occurred when sending the confirmation email.'));
					}
				}
				else
				{
					$id_duplicate = (int)$subscribe->isDuplicateEmail();
					if ($id_duplicate)
					{
						$subscribe->id = $id_duplicate;
						if (!$subscribe->update())
							$response->mergeErrors($subscribe->getErrors());
					}
					else
					{
						if (!$subscribe->add())
							$response->mergeErrors($subscribe->getErrors());
					}

				}
			}

			if ($response->success())
			{
				if (!$this->getConfiguration('SUBSCRIPTION_SECURE_SUBSCRIBE'))
				{
					$subscribe_msg = trim($subscrbtion_template->renderSubscribeMessage((int)$this->context->language->id));
					$subscribe_msg_strip = trim(strip_tags($subscribe_msg));

					if ($subscrbtion_template->display_subscribe_message && !empty($subscribe_msg_strip))
					{
						$response->set('displaySubscribeMessage', true);
						$response->set('msg', $subscribe_msg);
					}
					else if (!empty($subscribe_msg_strip))
						$response->set('msg', $subscribe_msg);
					else
					{
						$success_msg = $this->l('You have been successfully subscribed to our newsletter.');
						if ($voucher)
							$response->set('msg', $success_msg.sprintf($this->l('You can use this voucher %s.'), $voucher));
						else
							$response->set('msg', $success_msg);
					}

					// send the email with the voucher it the voucher exists
					if ($voucher)
					{
						$subscribe_voucher_msg = trim($subscrbtion_template->renderEmailSubscribeVoucherMessage((int)$this->context->language->id));
						$subscribe_voucher_msg_strip = trim(strip_tags($subscribe_voucher_msg));

						if (!empty($subscribe_voucher_msg_strip))
						{
							if (NewsletterPro::getConfiguration('DEBUG_MODE'))
								$send = NewsletterProSendManager::getInstance()->sendNewsletter($this->l('Newsletter Subscription Voucher'), $subscribe_voucher_msg, $subscribe->email, array(), array(), false);
							else
								$send = @NewsletterProSendManager::getInstance()->sendNewsletter($this->l('Newsletter Subscription Voucher'), $subscribe_voucher_msg, $subscribe->email, array(), array(), false);
						}
					}
				}
				else
				{
					if ($subscrbtion_template->display_subscribe_message)
						$response->set('displaySubscribeMessage', true);
					else
						$response->set('displaySubscribeMessage', false);

					$response->set('msg', sprintf($this->l('A configuration email has been sent to the email address "%s". To subscribe please click on the link from your email address.'), $subscribe->email));
				}
			}
		}
		catch(Exception $e)
		{
			NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
			$response->addError($this->l('An error occurred.'));
		}

		$response->errors = array_unique($response->errors);

		return $response->display();
	}

	/**
	 * Get shop logo
	 *
	 * @param  int $id_shop
	 * @return string
	 */
	public function getShopLogoUrl($id_shop = null)
	{
		if (!isset($id_shop))
			$id_shop = (int)$this->context->shop->id;

		$sql = 'SELECT `value` FROM `'._DB_PREFIX_.'configuration`
				WHERE `name`="PS_LOGO_MAIL"
				AND `id_shop` = '.(int)$id_shop;
		$row = Db::getInstance()->getRow($sql);

		$ps_logo_mail = isset($row['value']) ? $row['value'] : $this->getConfiguration('PS_LOGO_MAIL');

		$shop_logo_url = '';
		if ($ps_logo_mail !== false && file_exists(_PS_IMG_DIR_.$ps_logo_mail))
			$shop_logo_url = Tools::getHttpHost(true)._PS_IMG_.$ps_logo_mail;
		else
		{
			if (file_exists(_PS_IMG_DIR_.'logo.jpg'))
				$shop_logo_url = Tools::getHttpHost(true)._PS_IMG_.'logo.jpg';
		}

		$shop_logo_url = $this->relplaceAdminLink( $shop_logo_url );

		return $shop_logo_url;
	}

	/**
	 * Process the ajax requests
	 *
	 * @return die
	 */
	public function ajaxProcess()
	{
		$ajax = new NewsletterProAjaxController();
		$ajax->processFront(Tools::getValue('submit'));
	}

	/**
	 * Check of the availability of the uniform js library
	 *
	 * @return boolean
	 */
	public static function isUniformRequired()
	{
		return version_compare(_PS_VERSION_, '1.6.0.1', '<');
	}

	/**
	 * Include the uniform js library
	 *
	 * @param  object $controller
	 */
	public static function includeUniform($controller)
	{
		$module = NewsletterPro::getInstance();
		$controller->addCSS($module->uri_location.'views/css/uniform.default.css');
		$controller->addJS(array(
			$module->uri_location.'views/js/jquery.uniform.min.js',
		));
	}

	/**
	 * Check of the availabillity of the fontawoseme css library
	 *
	 * @return boolean
	 */
	public static function isFontAwesomeRequired()
	{
		// some of the icons are not displaied on prestashop 1.6.0.6, and I've updated to 1.6.1
		return version_compare(_PS_VERSION_, '1.6.1', '<');
	}

	/**
	 * Include the fontawesome js library
	 *
	 * @param  object $controller
	 */
	public static function includeFontAwesome($controller)
	{
		$module = NewsletterPro::getInstance();
		$controller->addCSS($module->uri_location.'views/css/font-awesome.css');
	}

	/**
	 * Hook display top
	 *
	 * @param  array $params
	 * @return string
	 */
	public function hookDisplayTop($params)
	{
		$output = array();

		try
		{
			if ($this->getConfiguration('SUBSCRIPTION_ACTIVE'))
			{
				$template = NewsletterProSubscriptionTpl::getActiveTemplateInstance();

				$css_path = $this->getCssPath();

				if (self::isUniformRequired())
					self::includeUniform($this->context->controller);

				if (self::isFontAwesomeRequired())
					self::includeFontAwesome($this->context->controller);

				$this->context->controller->addCSS($css_path.NewsletterProSubscriptionTpl::CSS_STYLE_GLOBAL_PATH, 'all');

				$css_details = $template->getSubscriptionCSSLinkWithDetails();
				if ($css_details['file_exists'])
					$this->context->controller->addCSS($css_details['link']);
				else
				{
					// cannot add the file with the regular function addCSS
					$this->context->controller->css_files[$css_details['link']] = 'all';
				}

				$this->context->controller->addCSS($css_path.'front_window.css', 'all');

				$this->context->controller->addJS(array(
					$this->uri_location.'views/js/newsletter_subscribe.js',
					$this->uri_location.'views/js/front_window.js',
				));

				$this->context->smarty->assign(array(
					'display_in_footer'                => true,
					'tpl_location'                     => $this->dir_location.'views/',
					'render_template'                  => $template->render((int)$this->context->language->id),
					'subscription_template_front_info' => $template->getJSData(),
				));

				$output[] = $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/hook/newsletter_subscribe.tpl'));
			}
		}
		catch(Exception $e)
		{
			NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
		}

		$this->restoreJqueryIfNeeded('displayTop');

		return implode('', $output);
	}

	/**
	 * Get languages
	 *
	 * @return array
	 */
	public function getLanguages()
	{
		$languages = Language::getLanguages(true, $this->context->shop->id);
		foreach ($languages as &$lang)
		{
			if ($lang['id_lang'] == $this->context->language->id)
				$lang['selected'] = true;
			else
				$lang['selected'] = false;
		}
		return $languages;
	}

	/**
	 * Get newsletter subscribe content
	 *
	 * @return string
	 */
	public function newsletterSubscribe()
	{
		$this->context->controller->addCSS($this->getCssPath().'newsletter_subscribe_block.css', 'all');
		return $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/hook/newsletter_subscribe_block.tpl'));
	}

	/**
	 * Hook display left column
	 *
	 * @param  array $params
	 * @return string
	 */
	public function hookDisplayLeftColumn($params)
	{
		$output = array();
		if ($this->getConfiguration('SUBSCRIPTION_ACTIVE'))
		{
			$this->context->smarty->assign(array(
				'display_hook' => 'displayLeftColumn',
			));
			$output[] = $this->newsletterSubscribe();
		}
		return implode('', $output);
	}

	/**
	 * Hook display right column
	 *
	 * @param  array $params
	 * @return string
	 */
	public function hookDisplayRightColumn($params)
	{
		$output = array();
		if ($this->getConfiguration('SUBSCRIPTION_ACTIVE'))
		{
			$this->context->smarty->assign(array(
				'display_hook' => 'displayRightColumn',
			));
			$output[] = $this->newsletterSubscribe();
		}
		return implode('', $output);
	}

	/**
	 * Hook display footer column
	 *
	 * @param  array $params
	 * @return string
	 */
	public function hookFooter($params)
	{
		$output = array();
		if ($this->getConfiguration('SUBSCRIPTION_ACTIVE'))
		{
			$this->context->smarty->assign(array(
				'display_hook' => 'displayFooter',
			));
			$output[] = $this->newsletterSubscribe();
		}
		return implode('', $output);
	}

	/**
	 * Hook action shop data duplicate
	 *
	 * @param  array $params
	 * @return string
	 */
	public function hookActionShopDataDuplication($params)
	{
		try
		{
			$old_id_shop = (int)$params['old_id_shop'];
			$new_id_shop = (int)$params['new_id_shop'];

			if (!$old_id_shop)
				$old_id_shop = Configuration::get('PS_SHOP_DEFAULT');

			$deleted = false;

			$asso_tables = array_merge(
				NewsletterProSubscriptionTpl::getAssoTables(),
				NewsletterProListOfInterest::getAssoTables()
			);
			foreach ($asso_tables as $table_name => $row)
			{
				$id = 'id_'.$row['type'];
				if ($row['type'] == 'fk_shop' || preg_match('/_lang$/', $table_name))
					$id = 'id_shop';
				else
					$table_name .= '_'.$row['type'];

				if (!$deleted)
				{
					$res = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.$table_name.'` WHERE `'.$id.'` = '.(int)$old_id_shop);

					if ($res)
					{
						unset($res[$id]);
						if (isset($row['primary']))
							unset($res[$row['primary']]);

						$keys = implode('`, `', array_keys($res));

						$sql = 'INSERT IGNORE INTO `'._DB_PREFIX_.$table_name.'` (`'.$keys.'`, '.$id.')
								(SELECT `'.$keys.'`, '.(int)$new_id_shop.' FROM '._DB_PREFIX_.$table_name.'
								WHERE `'.$id.'` = '.(int)$old_id_shop.')';
					}
					Db::getInstance()->execute($sql);
				}
			}
		}
		catch (Exception $e)
		{
			NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);

			if (_PS_MODE_DEV_)
				die(Tools::displayError($e->getMessage()));
		}
	}

	/**
	 * Hook display header
	 *
	 * @return string
	 */
	public function hookHeader()
	{
		$output = '';
		// the same script in available in the file /views/js/php/define.front.php
		$output .= '<script type="text/javascript">
					NewsletterPro.dataStorage.addObject({
						\'ajax_url\': \''.addcslashes($this->uri_location.'ajax/ajax_newsletterpro_front.php', '\'').'\'
					});

					// script alias, for the websites that have cache, this variables are not required, they can be deleted
					var NPRO_AJAX_URL = NewsletterPro.dataStorage.get(\'ajax_url\');

					</script>';

		$css_path = $this->getCssPath();

		$this->context->controller->addCSS($css_path.'newsletterpro_front.css', 'all');
		$this->context->controller->addCSS($this->uri_location.'views/css/newsletterpro_cross.css', 'all');

		$this->restore_jquery = false;
		if (version_compare(_PS_JQUERY_VERSION_, '1.7.2', '<'))
		{
			$this->restore_jquery = true;

			$this->context->controller->addJS(array(
				$this->uri_location.'views/js/php/define.front.jquery_old.php'
			));

			$this->context->controller->addJquery('1.7.2', $this->uri_location.'views/js/');

			$this->context->controller->addJS(array(
				$this->uri_location.'views/js/php/define.front.jquery_new.php',
			));
		}

		$this->context->controller->addJS(array(
			$this->uri_location.'views/js/newsletter.js',
			$this->uri_location.'views/js/init.js',
			$this->uri_location.'views/js/viewport.js',
			$this->uri_location.'views/js/front/my_account.js'
		));

		if (Tools::isSubmit('newsletterpro_source') && Tools::getValue('newsletterpro_source') == 'newsletter' && Tools::isSubmit('id_product'))
		{
			try
			{
				$id_product = (int)Tools::getValue('id_product');

				$product_exists = 'SELECT COUNT(*) FROM `'._DB_PREFIX_.'product` WHERE `id_product` = '.(int)$id_product.'';

				if (Db::getInstance()->getValue($product_exists) > 0)
				{
					$cookie = new NewsletterProCookie('statistics_products', time() + 259200);

					if (!$cookie->get('products_id'))
						$cookie->set('products_id', array());

					$products_id = $cookie->get('products_id');

					if (!in_array($id_product, $products_id))
					{
						$cookie->append('products_id', $id_product);

						if (Tools::isSubmit('id_newsletter'))
						{
							$id_history = (int)Tools::getValue('id_newsletter');
							$history_sql = 'SELECT `clicks` FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history` WHERE `id_newsletter_pro_tpl_history` = '.(int)$id_history.'';

							$history = Db::getInstance()->getRow($history_sql);

							if ($history && isset($history['clicks']))
							{
								$h_clicks = (int)$history['clicks'];
								$h_clicks++;

								Db::getInstance()->update('newsletter_pro_tpl_history', array(
									'clicks' => (int)$h_clicks,
								), '`id_newsletter_pro_tpl_history`='.(int)$id_history);
							}
						}

						$need_update = 'SELECT `clicks` FROM `'._DB_PREFIX_.'newsletter_pro_statistics` WHERE `id_product` = '.(int)$id_product.'';
						$clicks = Db::getInstance()->getValue($need_update);
						if ($clicks !== false)
						{
							$clicks++;
							Db::getInstance()->update('newsletter_pro_statistics', array(
								'clicks' => (int)$clicks,
							), '`id_product`='.(int)$id_product);
						}
						else
						{
							Db::getInstance()->insert('newsletter_pro_statistics', array(
								'id_product' => (int)$id_product,
								'clicks'     => 1,
							));
						}
					}
				}
			}
			catch (Exception $e)
			{
				NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
			}
		}

		if ($this->isLowerVersion('1.5.2.0') && isset($this->context->cookie->id_customer) && isset($this->context->cookie->id_lang))
		{
			$id_lang = (int)$this->context->cookie->id_lang;
			$this->updateCustomerLanguage($id_lang);
		}

		if ((bool)$this->getConfiguration('GOOGLE_ANALYTICS_ACTIVE'))
		{
			if ((bool)$this->getConfiguration('GOOGLE_UNIVERSAL_ANALYTICS_ACTIVE'))
				$google_analytics = pqnp_template_path($this->dir_location.'views/templates/admin/analyticstracking_new.tpl');
			else
				$google_analytics = pqnp_template_path($this->dir_location.'views/templates/admin/analyticstracking_old.tpl');

			if (file_exists($google_analytics))
			{
				$c_params = $this->getCampaignSubmition();
				$this->context->smarty->assign($c_params);

				$this->context->smarty->assign( array(
						'CAMPAIGN_ACTIVE'     => (bool)$this->getConfiguration('CAMPAIGN_ACTIVE'),
						'GOOGLE_ANALYTICS_ID' => $this->getConfiguration('GOOGLE_ANALYTICS_ID'),
						'HOST'                => Tools::getHttpHost(false),
					) );

				$output .= $this->context->smarty->fetch($google_analytics);
			}
		}
		elseif ((bool)$this->getConfiguration('CAMPAIGN_ACTIVE'))
			$this->context->smarty->assign(array('NEWSLETTER_CAMPAIGN' => self::getCampaignScript()));

		$this->restoreJqueryIfNeeded('displayHeader');
		return $output;
	}

	public static function getNewsletterCampaign()
	{
		$output = '';

		if (Module::isInstalled('newsletterpro'))
		{
			$module = NewsletterPro::getInstance();
			if (!(bool)$module->getConfiguration('GOOGLE_ANALYTICS_ACTIVE') && (bool)$module->getConfiguration('CAMPAIGN_ACTIVE'))
				$output = self::getCampaignScript();
		}

		return $output;
	}

	/**
	 * Restomre jQuery variable, in case the jQuery version is depracted
	 *
	 * @param  string $hook_name
	 */
	public function restoreJqueryIfNeeded($hook_name)
	{
		if (isset($this->restore_jquery) && $this->restore_jquery)
		{
			$in_display_top = $this->isRegisteredInHook('displayTop');
			if ($in_display_top && $hook_name == 'displayTop')
			{
				$this->context->controller->addJS(array(
					$this->uri_location.'views/js/php/define.front.jquery_restore.php'
				));
			}
			else if (!$in_display_top && $hook_name == 'displayHeader')
			{
				$this->context->controller->addJS(array(
					$this->uri_location.'views/js/php/define.front.jquery_restore.php'
				));
			}
		}
	}

	/**
	 * Get google campaign script
	 *
	 * @return string
	 */
	public static function getCampaignScript()
	{
		$module = NewsletterPro::getInstance();
		$c_params = $module->getCampaignSubmition();

		$script = '';
		if ((bool)$module->getConfiguration('GOOGLE_UNIVERSAL_ANALYTICS_ACTIVE'))
		{
			// The new version of the Google Analytics API
			if (isset($c_params['utm_source']))
				$script .= 'ga(\'set\', \'campaignSource\', \'('.$c_params['utm_source'].')\');'."\n";
			if (isset($c_params['utm_medium']))
				$script .= 'ga(\'set\', \'campaignMedium\', \'('.$c_params['utm_medium'].')\');'."\n";
			if (isset($c_params['utm_campaign']))
				$script .= 'ga(\'set\', \'campaignName\', \'('.$c_params['utm_campaign'].')\');'."\n";
			if (isset($c_params['utm_content']))
				$script .= 'ga(\'set\', \'campaignContent\', \'('.$c_params['utm_content'].')\');'."\n";
		}
		else
		{
			// The old version of the Google Analytics API
			$script .= '_gaq.push([\'_setCampaignTrack\', true]);'."\n";
			if (isset($c_params['utm_source']))
				$script .= '_gaq.push([\'_setCampSourceKey\', \''.$c_params['utm_source'].'\']);'."\n";
			if (isset($c_params['utm_medium']))
				$script .= '_gaq.push([\'_setCampMediumKey\', \''.$c_params['utm_medium'].'\']);'."\n";
			if (isset($c_params['utm_campaign']))
				$script .= '_gaq.push([\'_setCampNameKey\', \''.$c_params['utm_campaign'].'\']);'."\n";
			if (isset($c_params['utm_content']))
				$script .= '_gaq.push([\'_setCampContentKey\', \''.$c_params['utm_content'].'\']);'."\n";
		}

		if (Tools::getValue('utm_source') == 'testCampaign')
			http_response_code(205);

		if (Tools::isSubmit('testCampaign'))
		{
			if (Tools::getValue('testCampaign') == 'script')
				ddd($script);
		}

		return $script;
	}

	/**
	 * Get campaign post fields
	 *
	 * @return array
	 */
	public function getCampaignSubmition()
	{
		$params = array();
		$params_values = array_keys($this->getCampaignParamsArray());

		foreach ($params_values as $param)
			if (Tools::isSubmit($param))
			{
				// can replace addcslashes with Tools::safeOutput( from prestashop but first I need to check that
				$params[$param] = addcslashes(Tools::getValue($param), "'");
			}

		if (Tools::getValue('utm_source') == 'testCampaign')
			http_response_code(205);

		if (Tools::isSubmit('testCampaign'))
		{
			if (Tools::getValue('testCampaign') == 'params')
				ddd($params);
		}

		return $params;
	}

	/**
	 * Add a restriction on id_shop for multishop lang table
	 *
	 * @param string $alias
	 * @param Context $context
	 * @return string
	 */
	public static function addSqlRestrictionOnLang($alias = null, $id_shop = null)
	{
		if (version_compare(_PS_VERSION_, '1.5.2.0', '>='))
			return Shop::addSqlRestrictionOnLang($alias, $id_shop);

		if (is_null($id_shop))
			$id_shop = Context::getContext()->shop->id;
		return ' AND '.(($alias) ? $alias.'.' : '').'id_shop = '.$id_shop.' ';
	}

	/**
	 * This method allow to return children categories with the number of sub children selected for a product
	 *
	 * @param int $id_parent
	 * @param int $id_product
	 * @param int $id_lang
	 * @return array
	 */
	public static function getChildrenWithNbSelectedSubCat($id_parent, $selected_cat, $id_lang, Shop $shop = null, $use_shop_context = true)
	{
		if (!$shop)
			$shop = Context::getContext()->shop;

		$id_shop = $shop->id ? $shop->id : Configuration::get('PS_SHOP_DEFAULT');
		$selected_cat = explode(',', str_replace(' ', '', $selected_cat));
		$sql = '
		SELECT c.`id_category`, c.`level_depth`, cl.`name`,
		IF((
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'category` c2
			WHERE c2.`id_parent` = c.`id_category`
		) > 0, 1, 0) AS has_children,
		'.($selected_cat ? '(
			SELECT count(c3.`id_category`)
			FROM `'._DB_PREFIX_.'category` c3
			WHERE c3.`nleft` > c.`nleft`
			AND c3.`nright` < c.`nright`
			AND c3.`id_category`  IN ('.implode(',', array_map('intval', $selected_cat)).')
		)' : '0').' AS nbSelectedSubCat
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` '.NewsletterPro::addSqlRestrictionOnLang('cl', (int)$id_shop).')
		LEFT JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.`id_category` = cs.`id_category` AND cs.`id_shop` = '.(int)$id_shop.')
		WHERE `id_lang` = '.(int)$id_lang.'
		AND c.`active` = 1
		AND c.`id_parent` = '.(int)$id_parent;
		if (Shop::getContext() == Shop::CONTEXT_SHOP && $use_shop_context)
			$sql .= ' AND cs.`id_shop` = '.(int)$shop->id;
		if (!Shop::isFeatureActive() || Shop::getContext() == Shop::CONTEXT_SHOP && $use_shop_context)
			$sql .= ' ORDER BY cs.`position` ASC';

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
	}

	/**
	 * Explode the campaign params into an array
	 *
	 * @return array
	 */
	public function getCampaignParamsArray()
	{
		$params_str = Configuration::get('NEWSLETTER_PRO_CAMPAIGN');
		$params_exp = explode("\n", $params_str);

		$params = array();
		foreach ($params_exp as $key => $param)
		{
			$param_exp = explode('=', $param);

			if (isset($param_exp[0], $param_exp[1]))
				$params[trim($param_exp[0])] = trim($param_exp[1]);
		}
		$utm = array();
		$utm_db = $this->getConfiguration('CAMPAIGN');
		foreach ($utm_db as $key => $value)
			$utm[Tools::strtolower($key)] = $value;

		$return = array_merge($params, $utm);

		return $return;
	}

	/**
	 * Hook create account
	 *
	 * @param  array $params
	 * @return string
	 */
	public function hookCreateAccount($params)
	{
		if ((bool)$this->getConfiguration('SEND_NEWSLETTER_ON_SUBSCRIBE'))
		{
			if (isset($params['newCustomer']) && Validate::isLoadedObject($params['newCustomer']))
			{
				$customer = $params['newCustomer'];
				try
				{
					$this->sendLastNewsletter((string)$customer->email);
				}
				catch(Exception $e)
				{
					NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
				}
			}
		}

		if ($this->isLowerVersion('1.5.4.0'))
		{
			if (isset($this->context->cookie->id_customer) && isset($this->context->cookie->id_lang))
			{
				$id_lang = (int)$this->context->cookie->id_lang;
				$this->updateCustomerLanguage( $id_lang );
			}
		}

		// fix the bug for some shops
		if (!isset($this->context->cart))
			$this->context->cart = new Cart();
	}

	/**
	 * Send the last newsletter to the new customer subscribed
	 *
	 * @param  string $email
	 */
	public function sendLastNewsletter($email)
	{
		$id_history = $this->getLastSendNewsletterIdHisotry();

		if ($id_history > 0)
		{
			$template = NewsletterProTemplate::newHistory((int)$id_history, $email)->load();
			$message = $template->message();

			$send_manager = NewsletterProSendManager::getInstance();
			$send_manager->setTemplateNameForAttachment($template->name);
			$send_manager->sendNewsletter($message['title'], $message['body'], $template->user->to(), array(
				'user' => $template->user,
			));
		}
	}

	/**
	 * Get the last newsletter id sent from the hisotry
	 *
	 * @return int
	 */
	public function getLastSendNewsletterIdHisotry()
	{
		$send_count = 10;

		$sql = 'SELECT MAX(`id_newsletter_pro_tpl_history`) AS `id_newsletter_pro_tpl_history`, `date`
				FROM `'._DB_PREFIX_.'newsletter_pro_send`
				WHERE `emails_completed` >= '.(int)$send_count;
		$send = Db::getInstance()->getRow($sql);
		$send_date = $send['date'];

		$sql = 'SELECT MAX(`id_newsletter_pro_tpl_history`) AS `id_newsletter_pro_tpl_history`, `date_start`
				FROM `'._DB_PREFIX_.'newsletter_pro_task`
				WHERE `emails_completed` >= '.(int)$send_count;

		$task = Db::getInstance()->getRow($sql);
		$task_date = $task['date_start'];

		$id_newsletter_pro_tpl_history = 0;

		if (!isset($send_date) && isset($task_date))
			$id_newsletter_pro_tpl_history = (int)$task['id_newsletter_pro_tpl_history'];
		else if (isset($send_date) && !isset($task_date))
			$id_newsletter_pro_tpl_history = (int)$send['id_newsletter_pro_tpl_history'];
		else if (isset($send_date) && isset($task_date))
		{
			if (strtotime($send_date) < strtotime($task_date))
				$id_newsletter_pro_tpl_history = (int)$task['id_newsletter_pro_tpl_history'];
			else if (strtotime($send_date) > strtotime($task_date))
				$id_newsletter_pro_tpl_history = (int)$send['id_newsletter_pro_tpl_history'];
			else if ((int)$send['id_newsletter_pro_tpl_history'] < (int)$task['id_newsletter_pro_tpl_history'])
				$id_newsletter_pro_tpl_history = (int)$task['id_newsletter_pro_tpl_history'];
			else
				$id_newsletter_pro_tpl_history = (int)$send['id_newsletter_pro_tpl_history'];
		}
		else
			$id_newsletter_pro_tpl_history = 0;

		return $id_newsletter_pro_tpl_history;
	}

	/**
	 * Update customer language
	 *
	 * @param  int $id_lang
	 */
	public function updateCustomerLanguage($id_lang)
	{
		$sql = 'SELECT count(*) AS count FROM `'._DB_PREFIX_.'lang` where `id_lang` = '.(int)$id_lang.' AND `active` = 1';
		$count = Db::getInstance()->executeS($sql);
		if (!empty($count) && isset($count[0]['count']) && $count[0]['count'] >= 1)
		{
			$sql = 'UPDATE `'._DB_PREFIX_."customer` SET `id_lang` = '".(int)$id_lang."'
					WHERE `id_customer` = ".(int)$this->context->cookie->id_customer.' LIMIT 1;';
			Db::getInstance()->execute($sql);
		}
	}

	/**
	 * Install the database field id_lang for the oder versions of prestashop
	 *
	 * @param  string $column_name
	 * @return bool
	 */
	public function installLang($column_name = 'id_lang')
	{
		$sql = "SELECT COUNT(*) AS `count` FROM INFORMATION_SCHEMA.COLUMNS
				WHERE TABLE_SCHEMA = '"._DB_NAME_."'
				AND TABLE_NAME='"._DB_PREFIX_."customer'
				AND COLUMN_NAME = '".pSQL($column_name)."';";

		$count = Db::getInstance()->executeS($sql);

		if (!empty($count) && isset($count[0]['count']) && $count[0]['count'] == 0)
		{
			$sql = 'ALTER TABLE `'._DB_PREFIX_.'customer` ADD COLUMN `'.pSQL($column_name).'` INT(10) UNSIGNED NULL DEFAULT NULL;';
			if (!Db::getInstance()->execute($sql))
			{
				$this->_errors[] = $this->l('Cannot create the field "id_lang" into the table "customer".');
				return false;
			}

			$id_default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
			$sql = 'UPDATE `'._DB_PREFIX_.'customer` SET `'.pSQL($column_name)."` = '".(int)$id_default_lang."';";

			if (!Db::getInstance()->execute($sql))
			{
				$this->_errors[] = $this->l('Cannot update the language for the existing customers into the table "customer".');
				return false;
			}
		}

		if (!$this->registerHook('createAccount'))
		{
			$this->_errors[] = $this->l('The module cannot be registered to hook "createAccount".');
			return false;
		}

		return true;
	}

	/**
	 * Fix the prestashop problem shop name in single store
	 *
	 * @param  string $shop_name
	 * @return bool
	 */
	public function updateShopName($shop_name)
	{
		$sql = 'UPDATE `'._DB_PREFIX_.'shop` SET `name` = "'.pSQL($shop_name).'" WHERE `id_shop` = '.(int)$this->context->shop->id.' LIMIT 1;';
		if (!Db::getInstance()->execute($sql))
			$this->_errors[] = $this->l('Cannot update the shop name.');
		return true;
	}

	/**
	 * Is version
	 *
	 * @param  string  $string
	 * @return boolean
	 */
	public function isVersion($string)
	{
		$string = str_replace('.', '\.', $string);
		if (preg_match('/^'.$string.'$/', _PS_VERSION_ ))
			return true;
		else
			return false;
	}

	/**
	 * Is a lower version then prestashop
	 *
	 * @param  string  $string
	 * @return boolean
	 */
	public function isLowerVersion($string)
	{
		$higher_version = $this->getHigherVersion($string, _PS_VERSION_);

		if ($string == $higher_version)
			return true;
		else
			return false;
	}

	/**
	 * Is a higher version then prestashop
	 *
	 * @param  string  $string
	 * @return boolean
	 */
	public function isHigherVersion($string)
	{
		return !$this->isLowerVersion($string);
	}

	/**
	 * Is between version of prestashop
	 *
	 * @param  string  $lower_version
	 * @param  string  $higher_version
	 * @return boolean
	 */
	public function isBetweenVersions($lower_version, $higher_version)
	{
		if (!$this->isLowerVersion( $lower_version ) && $this->isLowerVersion( $higher_version ))
			return true;
		else
			return false;
	}

	/**
	 * Is prestashop 1.6 version
	 *
	 * @return boolean
	 */
	public function isPS16()
	{
		return $this->isHigherVersion('1.5.9.9');
	}

	/**
	 * Version compare
	 *
	 * @param  string $v1
	 * @param  string $v2
	 * @return bool
	 */
	public function getHigherVersion($v1, $v2)
	{
		$v1_e = explode('.', $v1);
		$v2_e = explode('.', $v2);

		$i = 0;
		$equal = true;
		while ($equal == true)
		{
			if (isset($v1_e[$i], $v2_e[$i]) && (int)$v1_e[$i] == (int)$v2_e[$i])
				$equal = true;
			elseif (isset($v1_e[$i], $v2_e[$i]) && (int)$v1_e[$i] < (int)$v2_e[$i])
			{
				$equal = false;
				return $v2;
			}
			elseif (isset($v1_e[$i], $v2_e[$i]) && (int)$v1_e[$i] > (int)$v2_e[$i])
			{
				$equal = false;
				return $v1;
			}
			if ($i >= count($v1_e) || $i >= count($v2_e))
				break;

			$i++;

			if ($i >= 4)
			break;
		}
	}

	/**
	 * Install the module configuration
	 * @return boolean
	 */
	public function installConfiguration()
	{
		$categories_depth     = $this->isLowerVersion('1.5.0.5') ? 1 : 2;
		$type_home = 'home';
		$this->configuration = array(
			'NEWSLETTER_TEMPLATE'               => 'sample.html',
			'PRODUCT_TEMPLATE'                  =>'sample.html',
			'IMAGE_TYPE'                        => ( $this->isLowerVersion('1.5.1.0') ? $type_home : $type_home.'_default' ),
			'SLEEP'                             => '3',
			'FWD_FEATURE_ACTIVE'                => '1',
			'CURRENCY'                          => (int)Configuration::get('PS_CURRENCY_DEFAULT'),
			'LANG'                              => (int)Configuration::get('PS_LANG_DEFAULT'),
			'CATEGORIES_DEPTH'                  => $categories_depth,
			'DISPLAY_PRODUCT_IMAGE'             => '1',
			'VIEW_ACTIVE_ONLY'                  => '1',
			'SUBSCRIBE_BY_CATEGORY'             => '1',
			'SEND_NEWSLETTER_ON_SUBSCRIBE'      => '0',
			'SMTP_ACTIVE'                       => '0',
			'SMTP'                              => '0',
			'DEBUG_MODE'                        => '0',
			'GOOGLE_ANALYTICS_ID'               => '',
			'TOKEN'                             => $this->token,
			'GOOGLE_ANALYTICS_ACTIVE'           => '0',
			'CAMPAIGN_ACTIVE'                   => '0',
			'CAMPAIGN'                          => $this->default_campaign_params,
			'PRODUCT_LINK_REWRITE'              => (int)Configuration::get('PS_REWRITING_SETTINGS'),
			'ONLY_ACTIVE_PRODUCTS'              => '1',
			'GOOGLE_UNIVERSAL_ANALYTICS_ACTIVE' => '0',
			'CONVERT_CSS_TO_INLINE_STYLE'       => '1',
			// subscription feature options
			'SUBSCRIPTION_SECURE_SUBSCRIBE'     => '0',
			'SUBSCRIPTION_ACTIVE'               => '0',
			'LEFT_MENU_ACTIVE'                  => '1',
			'CROSS_TYPE_CLASS'                  => 'np-icon-cross_5',
			// send limit settings
			'SEND_METHOD'                       => NewsletterPro::SEND_METHOD_DEFAULT, // 0 - Default, 1 - Anti flood. The values needs to be changed from the file init.js SEND_METHOD_DEFAULT and SEND_METHOD_ANTIFLOOD
			'SEND_ANTIFLOOD_ACTIVE'             => '1',
			'SEND_ANTIFLOOD_EMAILS'             => '100', // emails
			'SEND_ANTIFLOOD_SLEEP'              => '10', // seconds

			'SEND_THROTTLER_ACTIVE'             => '0',
			'SEND_THROTTLER_LIMIT'              => '100',
			'SEND_THROTTLER_TYPE'               => NewsletterPro::SEND_THROTTLER_TYPE_EMAILS, // 0 - Emails, 1 - MB. The values needs to be changed from the file init.js SEND_THROTTLER_TYPE_EMAILS and SEND_THROTTLER_TYPE_MB

			'PAGE_HEADER_TOOLBAR'				=> array(
													'CSV'                => 1,
													'MANAGE_IMAGES'      => 1,
													'SELECT_PRODUCTS'    => 1,
													'CREATE_TEMPLATE'    => 1,
													'SEND_NEWSLETTERS'   => 1,
													'TASK'               => 1,
													'HISTORY'            => 1,
													'STATISTICS'         => 1,
													'CAMPAIGN'           => 0,
													'SMTP'               => 1,
													'MAILCHIMP'          => 0,
													'FORWARD'            => 0,
													'FRONT_SUBSCRIPTION' => 1,
													'SETTINGS'           => 0,
													'TUTORIALS'          => 1,
												),
			'SHOW_CLEAR_CACHE'					=> 0,
			'SEND_EMBEDED_IMAGES'				=> 0,
			'SHOW_CUSTOM_COLUMNS'               => array(),
			'CHIMP_SYNC_UNSUBSCRIBED'           => 1,
			'SEND_LIMIT_END_SCRIPT'				=> 100,
			'LAST_DATE_NEWSLETTER_BLOCK_SYNC'   => '0000-00-00 00:00:00',
			'CUSTOMER_SUBSCRIBE_BY_LOI'         => '1',
			'DISPLYA_MY_ACCOUNT_NP_SETTINGS'    => '1',
			'USE_CACHE'							=> '1',
		);

		if (!Configuration::updateValue( 'NEWSLETTER_PRO_CAMPAIGN', '', false, 0, 0 ))
		{
			$this->_errors[] = sprintf($this->l('The configuration "%s" cannot be installed into "%s" table.'), 'NEWSLETTER_PRO_CAMPAIGN', _DB_PREFIX_.'configuration' );
			return false;
		}

		if (!Configuration::updateValue('NEWSLETTER_PRO', serialize($this->configuration), false, 0, 0))
		{
			$this->_errors[] = sprintf($this->l('The configuration "%s" cannot be installed into "%s" table.'), 'NEWSLETTER_PRO', _DB_PREFIX_.'configuration');
			return false;
		}

		if (!NewsletterProConfigurationShop::install())
		{
			$this->_errors[] = array_merge($this->_errors, NewsletterProConfigurationShop::getErrors());
			return false;
		}

		return true;
	}

	/**
	 * Uninstall the module configuraion
	 *
	 * @return boolean
	 */
	public function uninstallConfiguration()
	{
		return Configuration::deleteByName('NEWSLETTER_PRO')
				&& Configuration::deleteByName('NEWSLETTER_PRO_CAMPAIGN')
				&& NewsletterProConfigurationShop::uninstall();
	}

	/**
	 * Initialize configuration
	 */
	public function initConfiguration()
	{
		$this->configuration                                = NewsletterProTools::unSerialize(Configuration::get('NEWSLETTER_PRO'));
		$this->configuration['PS_SHOP_EMAIL']               = Configuration::get('PS_SHOP_EMAIL');
		$this->configuration['PS_MAIL_METHOD']              = Configuration::get('PS_MAIL_METHOD');
		$this->configuration['PS_CURRENCY_DEFAULT']         = Configuration::get('PS_CURRENCY_DEFAULT');
		$this->configuration['PS_LANG_DEFAULT']             = Configuration::get('PS_LANG_DEFAULT');
		$this->configuration['PS_SHOP_DEFAULT']             = Configuration::get('PS_SHOP_DEFAULT');
		$this->configuration['PS_MULTISHOP_FEATURE_ACTIVE'] = Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');
		$this->configuration['PS_LOGO_MAIL']                = Configuration::get('PS_LOGO_MAIL');
		$this->configuration['PS_SHOP_NAME']                = Configuration::get('PS_SHOP_NAME');
		$this->configuration['PS_LOCALE_COUNTRY']           = Configuration::get('PS_LOCALE_COUNTRY');
		$this->configuration['PS_REWRITING_SETTINGS']       = Configuration::get('PS_REWRITING_SETTINGS');

		NewsletterProConfigurationShop::init();
	}

	/**
	 * Update module configuration if exists
	 *
	 * @param  string $name
	 * @param  string $value
	 * @return boolean
	 */
	public static function updateConfiguration($name, $value)
	{
		$module = NewsletterPro::getInstance();

		if (isset($module->configuration[$name]))
		{
			$module->configuration[$name] = $value;

			return $module->updateDbConfiguration();
		}
		else
			return false;
	}

	/**
	 * Update module configuration
	 *
	 * @param  string $name
	 * @param  string $value
	 * @return boolean
	 */
	public static function writeConfiguration($name, $value)
	{
		$module = NewsletterPro::getInstance();

		$module->configuration[$name] = $value;
		return $module->updateDbConfiguration();
	}

	/**
	 * Delete module configuration
	 *
	 * @param  string $name
	 * @return boolean
	 */
	public function deleteConfiguration($name)
	{
		if (isset($this->configuration[$name]))
			unset($this->configuration[$name]);
		return $this->updateDbConfiguration();
	}

	/**
	 * Get multiple configurations
	 *
	 * @return array
	 */
	public function getUpdateConfiguration()
	{
		$update_configuration = array();
		foreach ($this->configuration as $c_name => $c_value)
		{
			if (!in_array($c_name, array(	'PS_SHOP_EMAIL',
											'PS_MAIL_METHOD',
											'PS_CURRENCY_DEFAULT',
											'PS_LANG_DEFAULT',
											'PS_SHOP_DEFAULT',
											'PS_MULTISHOP_FEATURE_ACTIVE',
											'PS_LOGO_MAIL',
											'PS_SHOP_NAME',
											'PS_LOCALE_COUNTRY',
											'PS_REWRITING_SETTINGS') ))
			$update_configuration[$c_name] = $c_value;
		}
		return $update_configuration;
	}

	/**
	 * Update module global configuration
	 *
	 * @return boolean
	 */
	public function updateDbConfiguration()
	{
		$update_configuration = $this->getUpdateConfiguration();
		return Configuration::updateValue('NEWSLETTER_PRO', serialize($update_configuration), false, 0, 0 );
	}

	/**
	 * Get module configuration
	 *
	 * @param  string $name
	 * @return boolean/string
	 */
	public static function getConfiguration($name)
	{
		$module = NewsletterPro::getInstance();

		if (isset($module->configuration[$name]))
			return $module->configuration[$name];
		else
			return false;
	}

	public function configurationExists($name)
	{
		return isset($this->configuration[$name]);
	}

	/**
	 * Redirect to the module dashboard if the employee will click on the module config anchor
	 */
	public function getContent()
	{
		Tools::redirectAdmin($this->link);
	}

	/**
	 * Install module database tables, fields and data
	 *
	 * @return boolean
	 */
	public function installDb()
	{
		$install = new NewsletterProInstall();

		$lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
		$shop_default = (int)Configuration::get('PS_SHOP_DEFAULT');

		$install->createTable('newsletter_pro_attachment',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_attachment` (
				`id_newsletter_pro_attachment` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`template_name` VARCHAR(255) NOT NULL,
				`files` LONGTEXT NOT NULL,
				PRIMARY KEY (`id_newsletter_pro_attachment`),
				UNIQUE INDEX `template_name` (`template_name`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_customer_category',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_customer_category` (
				`id_customer` INT(10) UNSIGNED NOT NULL DEFAULT '0',
				`categories` TEXT NULL,
				UNIQUE INDEX `id_customer` (`id_customer`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_customer_list_of_interests',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_customer_list_of_interests` (
				`id_customer` INT(10) UNSIGNED NOT NULL DEFAULT '0',
				`categories` TEXT NULL,
				UNIQUE INDEX `id_customer` (`id_customer`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_email',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_email` (
				`id_newsletter_pro_email` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_shop` INT(10) UNSIGNED NOT NULL DEFAULT '".$shop_default."',
				`id_shop_group` INT(10) UNSIGNED NOT NULL DEFAULT '".$shop_default."',
				`id_lang` INT(10) UNSIGNED NULL DEFAULT '".$lang_default."',
				`firstname` VARCHAR(32) NULL DEFAULT NULL,
				`lastname` VARCHAR(32) NULL DEFAULT NULL,
				`email` VARCHAR(255) NOT NULL,
				`ip_registration_newsletter` VARCHAR(15) NULL DEFAULT NULL,
				`filter_name` VARCHAR(255) NULL DEFAULT NULL,
				`date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`active` TINYINT(1) NOT NULL DEFAULT '1',
				PRIMARY KEY (`id_newsletter_pro_email`),
				INDEX `id_shop` (`id_shop`),
				INDEX `id_lang` (`id_lang`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_email_exclusion',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_email_exclusion` (
				`id_newsletter_pro_email_exclusion` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`email` VARCHAR(255) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id_newsletter_pro_email_exclusion`),
				UNIQUE INDEX `email` (`email`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_filters_selection',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_filters_selection` (
				`id_newsletter_pro_filters_selection` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(255) NOT NULL,
				`value` TEXT NULL,
				PRIMARY KEY (`id_newsletter_pro_filters_selection`),
				UNIQUE INDEX `name` (`name`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_forward',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_forward` (
				`id_newsletter_pro_forward` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`from` VARCHAR(128) NOT NULL,
				`to` VARCHAR(128) NOT NULL,
				`date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (`id_newsletter_pro_forward`),
				UNIQUE INDEX `to` (`to`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_fwd_unsibscribed',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_fwd_unsibscribed` (
				`id_newsletter_pro_fwd_unsibscribed` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_newsletter_pro_tpl_history` INT(10) UNSIGNED NOT NULL DEFAULT '0',
				`email` VARCHAR(255) NOT NULL,
				`date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (`id_newsletter_pro_fwd_unsibscribed`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_send',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_send` (
				`id_newsletter_pro_send` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_newsletter_pro_tpl_history` INT(11) UNSIGNED NOT NULL DEFAULT '0',
				`template` VARCHAR(50) NULL DEFAULT NULL,
				`active` INT(1) NOT NULL DEFAULT '0',
				`state` INT(10) NOT NULL DEFAULT '0',
				`emails_count` INT(11) UNSIGNED NOT NULL DEFAULT '0',
				`emails_success` INT(11) UNSIGNED NOT NULL DEFAULT '0',
				`emails_error` INT(11) UNSIGNED NOT NULL DEFAULT '0',
				`emails_completed` INT(11) UNSIGNED NOT NULL DEFAULT '0',
				`error_msg` LONGTEXT NULL,
				`date` DATETIME NULL DEFAULT NULL,
				PRIMARY KEY (`id_newsletter_pro_send`),
				INDEX `id_newsletter_pro_tpl_history` (`id_newsletter_pro_tpl_history`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_send_connection',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_send_connection` (
				`id_newsletter_pro_send_connection` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_newsletter_pro_smtp` INT(10) UNSIGNED NOT NULL,
				`state` INT(1) NOT NULL DEFAULT '0',
				`script_uid` VARCHAR(50) NULL DEFAULT NULL,
				PRIMARY KEY (`id_newsletter_pro_send_connection`, `id_newsletter_pro_smtp`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_send_step',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_send_step` (
				`id_newsletter_pro_send_step` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_newsletter_pro_send` INT(11) UNSIGNED NOT NULL DEFAULT '0',
				`id_newsletter_pro_send_connection` INT(11) UNSIGNED NOT NULL DEFAULT '0',
				`step` INT(11) UNSIGNED NOT NULL DEFAULT '0',
				`step_active` INT(1) NOT NULL DEFAULT '0',
				`emails_to_send` LONGTEXT NULL,
				`emails_sent` LONGTEXT NULL,
				`error_msg` LONGTEXT NULL,
				`date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
				`date_modified` TIMESTAMP NULL DEFAULT NULL,
				PRIMARY KEY (`id_newsletter_pro_send_step`),
				INDEX `id_step` (`step`),
				INDEX `id_task` (`id_newsletter_pro_send`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_smtp',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_smtp` (
				`id_newsletter_pro_smtp` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`method` INT(1) NOT NULL DEFAULT '1',
				`name` VARCHAR(64) NOT NULL,
				`from_name` VARCHAR(255) NULL DEFAULT NULL,
				`from_email` VARCHAR(255) NULL DEFAULT NULL,
				`reply_to` VARCHAR(255) NULL DEFAULT NULL,
				`domain` VARCHAR(255) NULL DEFAULT NULL,
				`server` VARCHAR(255) NULL DEFAULT NULL,
				`user` VARCHAR(255) NOT NULL,
				`passwd` VARCHAR(255) NULL DEFAULT NULL,
				`encryption` VARCHAR(255) NULL DEFAULT NULL,
				`port` VARCHAR(255) NULL DEFAULT NULL,
				`list_unsubscribe_active` INT(1) NULL DEFAULT '0',
				`list_unsubscribe_email` VARCHAR(255) NOT NULL,
				PRIMARY KEY (`id_newsletter_pro_smtp`),
				UNIQUE INDEX `name` (`name`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_statistics',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_statistics` (
				`id_product` INT(10) UNSIGNED NOT NULL DEFAULT '0',
				`clicks` INT(10) NOT NULL DEFAULT '0',
				UNIQUE INDEX `id_product` (`id_product`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_task',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_task` (
				`id_newsletter_pro_task` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_newsletter_pro_smtp` INT(10) UNSIGNED NOT NULL DEFAULT '0',
				`id_newsletter_pro_tpl_history` INT(10) UNSIGNED NOT NULL DEFAULT '0',
				`date_start` DATETIME NULL DEFAULT NULL,
				`date_modified` DATETIME NULL DEFAULT NULL,
				`active` INT(1) NOT NULL DEFAULT '0',
				`template` VARCHAR(128) NOT NULL,
				`send_method` ENUM('mail','smtp') NOT NULL DEFAULT 'mail',
				`status` INT(10) NOT NULL DEFAULT '0',
				`sleep` INT(10) NOT NULL DEFAULT '3',
				`pause` INT(10) NOT NULL DEFAULT '0',
				`emails_count` INT(10) NOT NULL DEFAULT '0',
				`emails_error` INT(10) NOT NULL DEFAULT '0',
				`emails_success` INT(10) NOT NULL DEFAULT '0',
				`emails_completed` INT(10) NOT NULL DEFAULT '0',
				`done` INT(10) NOT NULL DEFAULT '0',
				`error_msg` LONGTEXT NULL,
				PRIMARY KEY (`id_newsletter_pro_task`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_task_step',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_task_step` (
				`id_newsletter_pro_task_step` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_newsletter_pro_task` INT(11) UNSIGNED NOT NULL DEFAULT '0',
				`step` INT(11) UNSIGNED NOT NULL DEFAULT '0',
				`step_active` INT(1) NOT NULL DEFAULT '0',
				`emails_to_send` LONGTEXT NULL,
				`emails_sent` LONGTEXT NULL,
				`date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id_newsletter_pro_task_step`),
				INDEX `id_step` (`step`),
				INDEX `id_task` (`id_newsletter_pro_task`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

				// `template` LONGTEXT NULL,
		$install->createTable('newsletter_pro_tpl_history',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_tpl_history` (
				`id_newsletter_pro_tpl_history` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`token` VARCHAR(32) NOT NULL,
				`template_name` VARCHAR(255) NOT NULL,
				`active` INT(1) NULL DEFAULT '1',
				`clicks` INT(10) NOT NULL DEFAULT '0',
				`opened` INT(10) NOT NULL DEFAULT '0',
				`unsubscribed` INT(10) NOT NULL DEFAULT '0',
				`fwd_unsubscribed` INT(10) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id_newsletter_pro_tpl_history`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_tpl_history_lang',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_tpl_history_lang` (
				`id_newsletter_pro_tpl_history` INT(10) UNSIGNED NOT NULL,
				`id_lang` INT(10) UNSIGNED NOT NULL,
				`template` LONGTEXT NULL,
				PRIMARY KEY (`id_newsletter_pro_tpl_history`, `id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_unsibscribed',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_unsibscribed` (
				`id_newsletter_pro_unsibscribed` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_newsletter_pro_tpl_history` INT(10) UNSIGNED NOT NULL DEFAULT '0',
				`email` VARCHAR(255) NOT NULL,
				`date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (`id_newsletter_pro_unsibscribed`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		// 3.1.1
		$install->createTable('newsletter_pro_list_of_interest',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_list_of_interest` (
				`id_newsletter_pro_list_of_interest` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`active` TINYINT(1) NOT NULL DEFAULT '1',
				`position` INT(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id_newsletter_pro_list_of_interest`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_list_of_interest_lang',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter_pro_list_of_interest_lang` (
				`id_newsletter_pro_list_of_interest` INT(11) UNSIGNED NOT NULL,
				`id_lang` INT(11) UNSIGNED NOT NULL,
				`id_shop` INT(11) UNSIGNED NOT NULL,
				`name` VARCHAR(255) NULL DEFAULT NULL,
				PRIMARY KEY (`id_newsletter_pro_list_of_interest`, `id_lang`, `id_shop`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_list_of_interest_shop',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_list_of_interest_shop` (
				`id_newsletter_pro_list_of_interest` INT(10) UNSIGNED NOT NULL DEFAULT '0',
				`id_shop` INT(10) UNSIGNED NOT NULL DEFAULT '0',
				`active` TINYINT(1) NOT NULL DEFAULT '1',
				`position` INT(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id_newsletter_pro_list_of_interest`, `id_shop`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_mailchimp_token',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_mailchimp_token` (
				`id_newsletter_pro_mailchimp_token` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`token` varchar(50) NOT NULL,
				`creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`expiration_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				PRIMARY KEY (`id_newsletter_pro_mailchimp_token`),
				UNIQUE KEY `token` (`token`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_subscribers',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_subscribers` (
				`id_newsletter_pro_subscribers` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_shop` INT(10) UNSIGNED NOT NULL DEFAULT '1',
				`id_shop_group` INT(10) UNSIGNED NOT NULL DEFAULT '1',
				`id_lang` INT(10) UNSIGNED NULL DEFAULT '1',
				`id_gender` INT(10) UNSIGNED NULL DEFAULT '0',
				`firstname` VARCHAR(32) NULL DEFAULT NULL,
				`lastname` VARCHAR(32) NULL DEFAULT NULL,
				`email` VARCHAR(255) NOT NULL,
				`birthday` DATE NULL DEFAULT NULL,
				`ip_registration_newsletter` VARCHAR(15) NULL DEFAULT NULL,
				`list_of_interest` TEXT NULL,
				`date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`active` TINYINT(1) NOT NULL DEFAULT '1',
				PRIMARY KEY (`id_newsletter_pro_subscribers`),
				INDEX `id_shop` (`id_shop`),
				INDEX `id_lang` (`id_lang`),
				INDEX `id_shop_group` (`id_shop_group`),
				INDEX `id_gender` (`id_gender`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_subscribers_custom_field',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_subscribers_custom_field` (
				`id_newsletter_pro_subscribers_custom_field` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`variable_name` VARCHAR(50) NOT NULL,
				`type` INT(10) NOT NULL,
				`required` INT(10) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id_newsletter_pro_subscribers_custom_field`),
				UNIQUE INDEX `variable_name` (`variable_name`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_subscribers_custom_field_lang',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_subscribers_custom_field_lang` (
				`id_newsletter_pro_subscribers_custom_field` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_lang` INT(10) UNSIGNED NOT NULL DEFAULT '0',
				`value` LONGTEXT NOT NULL,
				PRIMARY KEY (`id_newsletter_pro_subscribers_custom_field`, `id_lang`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_subscribers_temp',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_subscribers_temp` (
				`id_newsletter_pro_subscribers_temp` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_newsletter_pro_subscription_tpl` INT(11) NOT NULL DEFAULT '0',
				`token` VARCHAR(32) NOT NULL,
				`email` VARCHAR(255) NOT NULL,
				`data` LONGTEXT NOT NULL,
				`date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (`id_newsletter_pro_subscribers_temp`, `id_newsletter_pro_subscription_tpl`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_subscription_tpl',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_subscription_tpl` (
				`id_newsletter_pro_subscription_tpl` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(255) NOT NULL,
				`voucher` VARCHAR(255) NULL DEFAULT NULL,
				`active` TINYINT(1) NOT NULL DEFAULT '0',
				`display_gender` TINYINT(1) NOT NULL DEFAULT '0',
				`display_firstname` TINYINT(1) NOT NULL DEFAULT '0',
				`display_lastname` TINYINT(1) NOT NULL DEFAULT '0',
				`display_language` TINYINT(1) NOT NULL DEFAULT '0',
				`display_birthday` TINYINT(1) NOT NULL DEFAULT '0',
				`display_list_of_interest` TINYINT(1) NOT NULL DEFAULT '0',
				`list_of_interest_type` TINYINT(1) NOT NULL DEFAULT '0',
				`display_subscribe_message` TINYINT(1) NOT NULL DEFAULT '1',
				`body_width` VARCHAR(255) NOT NULL DEFAULT '40%',
				`body_min_width` INT(11) NOT NULL DEFAULT '0',
				`body_max_width` INT(11) NOT NULL DEFAULT '0',
				`body_top` INT(11) NOT NULL DEFAULT '100',
				`show_on_pages` VARCHAR(255) NOT NULL DEFAULT '0',
				`cookie_lifetime` FLOAT NOT NULL DEFAULT '366',
				`start_timer` INT(11) NOT NULL DEFAULT '0',
				`when_to_show` INT(11) NOT NULL DEFAULT '0',
				`allow_multiple_time_subscription` INT(11) NOT NULL DEFAULT '1',
				`mandatory_fields` VARCHAR(255) NULL DEFAULT NULL,
				`date_add` DATETIME NULL DEFAULT NULL,
				`css_style` LONGTEXT NULL,
				`terms_and_conditions_url` TEXT NULL,
				PRIMARY KEY (`id_newsletter_pro_subscription_tpl`),
				UNIQUE INDEX `name` (`name`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_subscription_tpl_lang',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_subscription_tpl_lang` (
				`id_newsletter_pro_subscription_tpl` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_lang` INT(10) UNSIGNED NOT NULL DEFAULT '0',
				`id_shop` INT(10) UNSIGNED NOT NULL DEFAULT '0',
				`content` LONGTEXT NULL,
				`subscribe_message` LONGTEXT NULL,
				`email_subscribe_voucher_message` LONGTEXT NULL,
				`email_subscribe_confirmation_message` LONGTEXT NULL,
				PRIMARY KEY (`id_newsletter_pro_subscription_tpl`, `id_lang`, `id_shop`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		$install->createTable('newsletter_pro_subscription_tpl_shop',
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_."newsletter_pro_subscription_tpl_shop` (
				`id_newsletter_pro_subscription_tpl` INT(10) UNSIGNED NOT NULL DEFAULT '0',
				`id_shop` INT(10) UNSIGNED NOT NULL DEFAULT '0',
				`active` TINYINT(1) NOT NULL DEFAULT '1',
				`css_style` LONGTEXT NULL,
				`show_on_pages` VARCHAR(255) NOT NULL DEFAULT '0',
				`cookie_lifetime` FLOAT NOT NULL DEFAULT '366',
				`start_timer` INT(11) NOT NULL DEFAULT '0',
				`when_to_show` INT(11) NOT NULL DEFAULT '0',
				`terms_and_conditions_url` TEXT NULL,
				PRIMARY KEY (`id_newsletter_pro_subscription_tpl`, `id_shop`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
		);

		if (!$install->execute())
		{
			$this->mergeErrors($install->getErrors());
			return false;
		}

		// add records into the  database
		require_once _NEWSLETTER_PRO_DIR_.'/install/Install-3.1.1.php';

		// install the "newsletter_pro_config" table
		if (!NewsletterProConfig::install())
		{
			$this->mergeErrors(NewsletterProConfig::getErrors());
			return false;
		}

		return $this->success();
	}

	/**
	 * Add an error to the module
	 */
	public function addError($error)
	{
		if (is_array($error))
		{
			foreach ($error as $err)
				$this->_errors[] = $err;
		}
		else
			$this->_errors[] = $error;
	}

	/**
	 * Check if the module has errors
	 *
	 * @return boolean
	 */
	public function success()
	{
		return empty($this->_errors);
	}

	/**
	 * Add errors as array
	 *
	 * @param  array $errors
	 */
	public function mergeErrors($errors)
	{
		$this->_errors = array_merge($this->_errors, $errors);
	}

	/**
	 * Install the module to the customers menu
	 *
	 * @param  string $tab_class
	 * @param  string $tab_name
	 * @param  string $tab_parent_name
	 * @return boolean
	 */
	public function installModuleTab($tab_class, $tab_name, $tab_parent_name = 'AdminCustomers')
	{
		$logo_file = _NEWSLETTER_PRO_DIR_.'/logo.png';
		$logo_destination = _PS_IMG_DIR_.'t/'.$tab_class.'.png';
		copy($logo_file, $logo_destination);

		$id = Tab::getIdFromClassName($tab_class);

		$tab = new Tab((int)$id);

		foreach (Language::getLanguages(false) as $lang)
			$tab->name[$lang['id_lang']] = $tab_name;
		$tab->class_name = $tab_class;
		$tab->module = $this->name;
		$tab->id_parent = (int)Tab::getIdFromClassName($tab_parent_name);
		$tab->active = 1;

		if (version_compare(_PS_VERSION_, '1.5.1.0', '<='))
		{
			if (!$tab->save())
				return (bool)$tab->id;
			return true;
		}
		else
		{
			if (!$tab->save())
				return false;
			return true;
		}
	}

	/**
	 * Uninstall the module from the menu
	 *
	 * @param  string $tab_class
	 * @return boolean
	 */
	public function uninstallModuleTab($tab_class)
	{
		$id_tab = Tab::getIdFromClassName($tab_class);
		if ($id_tab != 0)
		{
			$tab = new Tab($id_tab);
			$tab->delete();
			return true;
		}
		return false;
	}

	/**
	 * Copy data from the $_POST variable
	 *
	 * @param  object $object
	 * @param  string $table
	 */
	protected function copyFromPost(&$object, $table)
	{
		$post =& $_POST;

		foreach ($post as $key => $value)
			if (array_key_exists($key, $object) && $key != 'id_'.$table)
			{
				if ($key == 'passwd' && Tools::getValue('id_'.$table) && empty($value))
					continue;

				if ($key == 'passwd' && !empty($value))
					$value = Tools::encrypt($value);
				$object->{$key} = $value;
			}

		$rules = call_user_func(array(get_class($object), 'getValidationRules'), get_class($object));
		if (count($rules['validateLang']))
		{
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
				foreach (array_keys($rules['validateLang']) as $field)
					if (isset($post[$field.'_'.(int)$language['id_lang']]))
						$object->{$field}[(int)$language['id_lang']] = $post[$field.'_'.(int)$language['id_lang']];
		}
	}

	/**
	 * Update asso shop
	 *
	 * @param  int $id_object
	 * @param  string $table
	 * @return int/bool
	 */
	public function updateAssoShop($id_object, $table)
	{
		if (!Shop::isFeatureActive())
			return;

		if (!NewsletterPro::isTableAssociated($table))
			return;

		$assos_data = $this->getSelectedAssoShop($table, $id_object);

		$exclude_ids = $assos_data;
		foreach (Db::getInstance()->executeS('SELECT id_shop FROM '._DB_PREFIX_.'shop') as $row)
			if (!$this->context->employee->hasAuthOnShop($row['id_shop']))
				$exclude_ids[] = $row['id_shop'];
		Db::getInstance()->delete($table.'_shop', '`'.$id_object.'` = '.(int)$id_object.($exclude_ids ? ' AND id_shop NOT IN ('.implode(', ', $exclude_ids).')' : ''));

		$insert = array();
		foreach ($assos_data as $id_shop)
			$insert[] = array(
				$id_object => $id_object,
				'id_shop' => (int)$id_shop,
			);
		return Db::getInstance()->insert($table.'_shop', $insert, false, true, Db::INSERT_IGNORE);
	}

	/**
	 * Get asso shop
	 *
	 * @param  string $table
	 * @return array
	 */
	public function getSelectedAssoShop($table)
	{
		if (!Shop::isFeatureActive() || !NewsletterPro::isTableAssociated($table))
			return array();

		$shops = Shop::getShops(true, null, true);
		if (count($shops) == 1 && isset($shops[0]))
			return array($shops[0], 'shop');

		$assos = array();
		if (Tools::isSubmit('checkBoxShopAsso_'.$table))
		{
			$check = Tools::getValue('checkBoxShopAsso_'.$table);
			foreach (array_keys($check) as $id_shop)
				$assos[] = (int)$id_shop;
		}
		else if (Shop::getTotalShops(false) == 1)
			$assos[] = (int)NewsletterPro::getContextShopID();
		return $assos;
	}

	/**
	 * Uninstall the module from the menu
	 *
	 * @return boolean
	 */
	public function uninstallFromMenu()
	{
		$id_tab = Tab::getIdFromClassName($this->class_name);
		$tab	= new Tab( $id_tab );
		return $tab->delete();
	}

	/**
	 * Uninstall the database
	 *
	 * @return boolean
	 */
	private function uninstallDb()
	{
		// this option will always be uninstalled
		$full_uninstall = array(
			'newsletter_pro_send',
			'newsletter_pro_send_step',
			'newsletter_pro_smtp',
			'newsletter_pro_task',
			'newsletter_pro_task_step',
			// 3.2.8
			'newsletter_pro_email_exclusion',
			// in 4.0.0
			'newsletter_pro_filters_selection',
			'newsletter_pro_send_connection',
			// in 4.0.1
			'newsletter_pro_attachment',
			'newsletter_pro_subscribers_custom_field',
			'newsletter_pro_subscribers_custom_field_lang',
			'newsletter_pro_mailchimp_token',
		);

		// this tables will uninstall only if the option uninstall_all_tables is true
		$protected_uninstall = array(
			'newsletter_pro_email',
			'newsletter_pro_tpl_history',
			'newsletter_pro_tpl_history_lang',
			'newsletter_pro_customer_category',
			'newsletter_pro_customer_list_of_interests',
			'newsletter_pro_statistics',
			'newsletter_pro_unsibscribed',
			'newsletter_pro_fwd_unsibscribed',
			'newsletter_pro_forward',
			// 3.1.1
			'newsletter_pro_list_of_interest',
			'newsletter_pro_list_of_interest_lang',
			'newsletter_pro_list_of_interest_shop',
			'newsletter_pro_subscribers',
			'newsletter_pro_subscribers_temp',
			'newsletter_pro_subscription_tpl',
			'newsletter_pro_subscription_tpl_lang',
			'newsletter_pro_subscription_tpl_shop',
		);

		foreach ($full_uninstall as $table_name)
		{
			if (!Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.$table_name.'`;'))
				return false;
		}

		if ($this->uninstall_all_tables)
		{
			foreach ($protected_uninstall as $table_name)
			{
				if (!Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.$table_name.'`;'))
					return false;
			}

			if (!NewsletterProConfig::uninstall())
				return false;
		}

		return true;
	}

	private function uninstallAttachments()
	{
		$files = NewsletterProTools::getDirectoryIterator($this->tpl_location.'attachments/', '/.*/');

		foreach ($files as $file)
		{
			if ($file->isFile() && !in_array($file->getFileName(), array('.htaccess', 'index.php')))
				@unlink($file->getPathName());
		}

		return true;
	}

	/**
	 * Get the context shop id
	 *
	 * @param  boolean
	 * @return boolean
	 */
	public static function getContextShopID($null_value_without_multishop = false)
	{
		if (method_exists('Shop', 'getContextShopID'))
			return Shop::getContextShopID($null_value_without_multishop);

		return Shop::getContextShopIDNewsletterPro($null_value_without_multishop);
	}

	/**
	 * Get the context shop group id
	 *
	 * @param  boolean $null_value_without_multishop
	 * @return boolean
	 */
	public static function getContextShopGroupID($null_value_without_multishop = false)
	{
		if (method_exists('Shop', 'getContextShopGroupID'))
			return Shop::getContextShopGroupID($null_value_without_multishop);

		return Shop::getContextShopGroupIDNewsletterPro($null_value_without_multishop);
	}

	/**
	 * Check if the database table is associated
	 *
	 * @param  string $table
	 * @return boolean
	 */
	public static function isTableAssociated($table)
	{
		if (method_exists('Shop', 'isTableAssociated'))
			return Shop::isTableAssociated($table);

		return Shop::isTableAssociatedNewsletterPro($table);
	}

	/**
	 * Get shops groups
	 *
	 * @param  boolean $active
	 * @return array
	 */
	public static function getShopGroups($active = true)
	{
		if (class_exists('ShopGroup'))
			return ShopGroup::getShopGroups($active);

		return GroupShop::getGroupShops($active);
	}

	/**
	 * Init content
	 */
	private function initContext()
	{
		$this->context = Context::getContext();
		// Set Front Office Link
		$protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
		$use_ssl = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
		$protocol_content = ($use_ssl) ? 'https://' : 'http://';
		$link = new Link($protocol_link, $protocol_content);
		$link->protocol_link = $this->relplaceAdminLink($link->protocol_link);
		$link->protocol_content = $this->relplaceAdminLink($link->protocol_content);

		$this->context->link =& $link;

		if (!isset($this->context->shop->id_shop_group))
			$this->context->shop->id_shop_group =& $this->context->shop->id_group_shop;

		if (!Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE'))
		{
			$ps_shop_name    = Configuration::get('PS_SHOP_NAME');
			$ps_shop_default = (int)Configuration::get('PS_SHOP_DEFAULT');

			if ($ps_shop_name && $ps_shop_default && ($this->context->shop->name != $ps_shop_name))
			{
				Db::getInstance()->update('shop', array(
					'name' => pSQL($ps_shop_name),
				), '`id_shop` = '.(int)$ps_shop_default, 1);
			}
		}
	}

	/**
	 * Save products number per row
	 * @param  int $number
	 * @return json
	 */
	public function saveProductNumberPerRow($number)
	{
		$errors = array();
		$response = array('status' => false, 'errors' => &$errors);

		$template = $this->getConfiguration('PRODUCT_TEMPLATE');
		$path = $this->tpl_location.'product/'.$template;

		if ($template == 'default.html')
			$errors[] = $this->l('Save a copy of the default template to change the number of products on row.');
		else
		{
			if (file_exists($path))
			{
				$content = Tools::file_get_contents($path);
				$full_content = preg_replace('/\{columns=\d+\}(\n)?/', '{columns='.$number.'}', $content);

				if ($handle = fopen($path, 'w'))
				{
					fwrite($handle, $full_content);
					fclose($handle);
				}
				else
					$errors[] = $this->l('Template cannot be written');
			}
			else
				$errors[] = $this->l('Missing template file');
		}

		if (empty($errors))
			$response['status'] = true;

		return Tools::jsonEncode($response);
	}

	/**
	 * Save product template
	 * @param  string $content
	 * @param  int $product_nr
	 * @return json
	 */
	public function saveProductTemplate($content, $product_nr)
	{
		$data = array();

		$template = $this->getConfiguration('PRODUCT_TEMPLATE');
		$path = $this->tpl_location.'product/'.$template;

		if ($template == 'default.html')
		{
			$data['message'] = $this->l('You cannot override the default templates. Please save a copy.');
			$data['type'] = false;
			return Tools::jsonEncode($data);
		}

		if (file_exists($path))
		{
			if (isset($content) && $content != false)
			{
				if (preg_match('/<!-- \{columns=\d+\} -->/', $content, $match)) {
					$content = str_replace($match[1], '<!-- {columns='.$product_nr.'} -->', $content);
				}

				$header = '';
				// $header = '<!-- {columns='.$product_nr."} -->\n";
				// $header = '<!-- {columns='.$product_nr."} -->\n";
				$full_content = $header.$content;

				if (preg_match('/<(?=\/?img(?=>|\s.*>))\/?.*?>/', $full_content, $img_match))
				{
					$img_match = (string)$img_match[0];

					$img_re_match = preg_replace('/\ssrc\="[^"]*"/', ' src="{image_path}"', $img_match);
					$img_re_match = preg_replace('/\swidth\="[^"]*"/', ' width="{image_width}"', $img_re_match);
					$img_re_match = preg_replace('/\sheight\="[^"]*"/', ' height="{image_height}"', $img_re_match);

					$full_content = str_replace($img_match, $img_re_match, $full_content);
				}

				if ($handle = fopen($path, 'w'))
				{
					fwrite($handle, $full_content);
					fclose($handle);
					$data['message'] = $this->l('Update successfully');
					$data['type'] = true;
					$data['content'] = $full_content;
				}
				else
				{
					$data['message'] = $this->l('Template cannot be written');
					$data['type'] = false;
				}
			}
			else
			{
				$data['message'] = $this->l('Template not saved');
				$data['type'] = false;
			}
		}
		else
		{
			$data['message'] = $this->l('Missing template file');
			$data['type'] = false;
		}
		return Tools::jsonEncode( $data );
	}

	/**
	 * Save as the product template
	 *
	 * @param  string $name
	 * @param  string $content
	 * @param  int $product_nr
	 * @return json
	 */
	public function saveAsProductTemplate($name, $content, $product_nr)
	{
		$name = preg_replace('/\s+/i', '_', $name);
		$validate_name = $this->verifyName($name);

		if ($validate_name !== true)
			return '{"status":"'.false.'", "msg":"'.$validate_name.'"}';

		$name = $name.'.html';
		$path = $this->tpl_location.'product/'.$name;

		if (!file_exists($path))
		{
			if (isset($content) && $content != false)
			{
				if (preg_match('/<!-- \{columns=\d+\} -->/', $content, $match)) {
					$content = str_replace($match[1], '<!-- {columns='.$product_nr.'} -->', $content);
				}

				$header = '';
				// $header = '<!-- {columns='.$product_nr."} -->\n";
				$full_content = $header.$content;

				$header_content = '';
				if (preg_match('/<!-- start header -->\s*?<!--(?P<header>[\s\S]*)-->\s*?<!-- end header -->/', $content, $match))
					$header_content	= trim($match['header']);

				if (!preg_match('/content\s+=\s+template/', $header_content) && preg_match('/<(?=\/?img(?=>|\s.*>))\/?.*?>/', $full_content, $img_match)) {
					$img_match = (string)$img_match[0];

					$img_re_match = preg_replace('/\ssrc\="[^"]*"/', ' src="{image_path}"', $img_match);
					$img_re_match = preg_replace('/\swidth\="[^"]*"/', ' width="{image_width}"', $img_re_match);
					$img_re_match = preg_replace('/\sheight\="[^"]*"/', ' height="{image_height}"', $img_re_match);

					$full_content = str_replace($img_match, $img_re_match, $full_content);
				}

				if ($handle = fopen($path, 'w'))
				{
					fwrite($handle, $full_content);
					fclose($handle);
					$this->updateConfiguration('PRODUCT_TEMPLATE', $name);
					return Tools::jsonEncode(array(
						'status' => true,
						'msg' => '',
						'full_content' => $full_content,
					));
					// return '{"status":"'.true.'", "msg":""}';
				}
				else
					return '{"status":"'.false.'", "msg":"'.$this->l('Cannot write the template').'"}';
			}
			else
				return '{"status":"'.false.'", "msg":"'.$this->l('Template was not saved').'"}';
		}
		else
			return '{"status":"'.false.'", "msg":"'.$this->l('This file name already exists').'"}';

		return '{"status":"'.false.'", "msg":"'.$this->l('Saved unsuccessfully').'"}';
	}

	/**
	 * Get all products from category by id category
	 *
	 * @param  int $id_category
	 * @return json
	 */
	public function getProducts($id_category)
	{
		$data            = array();
		$id_lang         = (int)$this->getConfiguration('LANG');
		$order_by_prefix = null;
		$front           = false;
		$order_by        = 'p.supplier_reference';
		$order_way       = 'ASC';
		$limit           = 1000;
		$id_supplier     = false;
		$start           = 0;
		$only_active     = $this->getConfiguration('ONLY_ACTIVE_PRODUCTS');

		if ($this->isBetweenVersions('1.5.0.0', '1.5.0.6'))
		{
			$sql = 'SELECT p.*, pl.* , t.`rate` AS tax_rate, m.`name` AS manufacturer_name, s.`name` AS supplier_name, i.`id_image`, il.`legend`
				FROM `'._DB_PREFIX_.'product` p
				'.$this->context->shop->addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.$this->context->shop->addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
				AND tr.`id_country` = '.(int)$this->context->country->id.'
				AND tr.`id_state` = 0)
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)  AND i.cover = 1
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON ( i.`id_image` = il.`id_image`) AND il.`id_lang` = '.(int)$id_lang.'
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)'.
				($id_category ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '').'
				WHERE pl.`id_lang` = '.(int)$id_lang.
					($id_category ? ' AND c.`id_category` = '.(int)$id_category : '').
					($only_active ? ' AND p.`active` = 1' : '').'
				GROUP BY p.`id_product`
				ORDER BY '.(isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').'`'.pSQL($order_by).'` '.pSQL($order_way).
				($limit > 0 ? ' LIMIT '.(int)$start.','.(int)$limit : '');
		}
		elseif ($this->isBetweenVersions('1.5.0.4', '1.5.0.18'))
		{
			$sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, product_attribute_shop.`id_product_attribute`,
						pl.`description`, pl.`description_short`, pl.`available_now`,
						pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image`,
						il.`legend`, m.`name` AS manufacturer_name, tl.`name` AS tax_name, t.`rate`, cl.`name` AS category_default,
						DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
						INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
							DAY)) > 0 AS new,
						(product_shop.`price` * IF(t.`rate`,((100 + (t.`rate`))/100),1)) AS orderprice
					FROM `'._DB_PREFIX_.'category_product` cp
					LEFT JOIN `'._DB_PREFIX_.'product` p
						ON p.`id_product` = cp.`id_product`
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
					ON (p.`id_product` = pa.`id_product`)
					'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
					'.Product::sqlStock('p', 'product_attribute_shop', false, $this->context->shop).'
					LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
						ON (product_shop.`id_category_default` = cl.`id_category`
						AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
					LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
						ON (p.`id_product` = pl.`id_product`
						AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
					LEFT JOIN `'._DB_PREFIX_.'image` i
					ON (i.`id_product` = p.`id_product`)'.
					Shop::addSqlAssociation('image', 'i', false, 'i.cover=1').'
					LEFT JOIN `'._DB_PREFIX_.'image_lang` il
						ON (image_shop.`id_image` = il.`id_image`
						AND il.`id_lang` = '.(int)$id_lang.')
					LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr
						ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`
						AND tr.`id_country` = '.(int)$this->context->country->id.'
						AND tr.`id_state` = 0
						AND tr.`zipcode_from` = 0)
					LEFT JOIN `'._DB_PREFIX_.'tax` t
						ON (t.`id_tax` = tr.`id_tax`)
					LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl
						ON (t.`id_tax` = tl.`id_tax`
						AND tl.`id_lang` = '.(int)$id_lang.')
					LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
						ON m.`id_manufacturer` = p.`id_manufacturer`
					WHERE product_shop.`id_shop` = '.(int)$this->context->shop->id.'
					AND (pa.id_product_attribute IS NULL OR product_attribute_shop.id_shop='.(int)$this->context->shop->id.')
					AND (i.id_image IS NULL OR image_shop.id_shop='.(int)$this->context->shop->id.')
						AND cp.`id_category` = '.(int)$id_category
						.($only_active ? ' AND product_shop.`active` = 1' : '')
						.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
						.($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '')
						.'
					GROUP BY p.`id_product`
					ORDER BY `'.pSQL($order_by).'` '.pSQL($order_way).' LIMIT 0,'.(int)$limit.';';
		}
		else
		{
			$sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, product_attribute_shop.`id_product_attribute`,
						pl.`description`, pl.`description_short`, pl.`available_now`,
						pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image`,
						il.`legend`, m.`name` AS manufacturer_name, tl.`name` AS tax_name, t.`rate`, cl.`name` AS category_default,
						DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
						INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
							DAY)) > 0 AS new,
						(product_shop.`price` * IF(t.`rate`,((100 + (t.`rate`))/100),1)) AS orderprice
					FROM `'._DB_PREFIX_.'category_product` cp
					LEFT JOIN `'._DB_PREFIX_.'product` p
						ON p.`id_product` = cp.`id_product`
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
					ON (p.`id_product` = pa.`id_product` )
					'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
					'.Product::sqlStock('p', 'product_attribute_shop', false, $this->context->shop).'
					LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
						ON (product_shop.`id_category_default` = cl.`id_category`
						AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
					LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
						ON (p.`id_product` = pl.`id_product`
						AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
					LEFT JOIN `'._DB_PREFIX_.'image` i
						ON (i.`id_product` = p.`id_product`)'.
					Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
					LEFT JOIN `'._DB_PREFIX_.'image_lang` il
						ON (image_shop.`id_image` = il.`id_image`
						AND il.`id_lang` = '.(int)$id_lang.')
					LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr
						ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`
						AND tr.`id_country` = '.(int)$this->context->country->id.'
						AND tr.`id_state` = 0
						AND tr.`zipcode_from` = 0)
					LEFT JOIN `'._DB_PREFIX_.'tax` t
						ON (t.`id_tax` = tr.`id_tax`)
					LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl
						ON (t.`id_tax` = tl.`id_tax`
						AND tl.`id_lang` = '.(int)$id_lang.')
					LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
						ON m.`id_manufacturer` = p.`id_manufacturer`
					WHERE product_shop.`id_shop` = '.(int)$this->context->shop->id.'
					AND (pa.id_product_attribute IS NULL OR product_attribute_shop.id_shop='.(int)$this->context->shop->id.')
					AND (i.id_image IS NULL OR image_shop.id_shop='.(int)$this->context->shop->id.')
						AND cp.`id_category` = '.(int)$id_category
						.($only_active ? ' AND product_shop.`active` = 1' : '')
						.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
						.($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '')
						.'
					GROUP BY p.`id_product`
					ORDER BY '.pSQL($order_by).' '.pSQL($order_way).' LIMIT 0,'.(int)$limit.';';

		}

		if ((int)pqnp_ini_config('get_category_products_prestashop')) {
			$category = new Category($id_category);
			$products = $category->getProducts($id_lang, 1, 999999, $order_by, $order_way, false, false);
		} else {
			$products = Db::getInstance()->executeS($sql);
		}

		if (empty($products))
		{
			$data['products'] = $products;
			return Tools::jsonEncode( $data );
		}

		$data['products'] = $this->getProductsAttributes( $id_lang, $products );

		return Tools::jsonEncode( $data );
	}

	/**
	 * Setup a new context
	 */
	public function setRelatedContext()
	{
		$this->context->currency = new Currency((int)$this->getConfiguration('CURRENCY'));
		$this->context->language = new Language((int)$this->getConfiguration('LANG'));
	}

	/**
	 * Get products attributes
	 *
	 * @param  int $id_lang
	 * @param  array $pr
	 * @param  int $id_currency
	 * @return array
	 */
	public function getProductsAttributes($id_lang, $pr, $id_currency = null)
	{
		$this->setRelatedContext();
		$prop = $this->getNewProperties('large_default', $id_currency);

		$products = Product::getProductsProperties($id_lang, $pr);

		foreach ($products as &$product)
			$this->createProductTemplateVars( $id_lang, $product, $prop );

		return $products;
	}

	/**
	 * Get product attributes
	 *
	 * @param  int $id_lang
	 * @param  array $pr
	 * @return array
	 */
	public function getProductAttributes($id_lang, $pr)
	{
		$this->setRelatedContext();
		$prop = $this->getNewProperties();
		$product = Product::getProductProperties($id_lang, $pr);

		$this->createProductTemplateVars( $id_lang, $product, $prop );

		return $product;
	}

	/**
	 * The the products new properties
	 *
	 * @param  string $image_type
	 * @param  int $id_currency
	 * @return array
	 */
	public function getNewProperties($image_type = null, $id_currency = null)
	{
		$prop = array();

		if (!isset($image_type))
			$image_type = (string)$this->getConfiguration('IMAGE_TYPE');
		$image = Image::getSize($image_type);

		if ($image == false)
		{
			$sql = 'SELECT `id_image_type` as `id`, `name`, `width`, `height` FROM `'._DB_PREFIX_.'image_type`
					WHERE `products` = 1 AND width > 150 ORDER BY `width` ASC LIMIT 1;';
			$image = Db::getInstance()->executeS($sql);
			$image = isset($image[0]) ? $image[0] : null;
		}

		$image_thumb_type = '';
		$type_cart = 'medium';
		$type_small = 'small';
		if ($this->isPS16())
			$image_thumb_type = $type_cart.'_default';
		else
			$image_thumb_type = ( $this->isLowerVersion('1.5.1.0') ? 'small' : $type_small.'_default' );

		/*if ($images_types = ImageType::getImagesTypes('products'))
		{
			$smallest = array();
			foreach ($images_types as $img)
			{
				if (empty($smallest))
					$smallest = $img;
				else if ($img['width'] < $smallest['width'])
					$smallest = $img;
			}

			if (isset($smallest['name']))
				$image_thumb_type = $smallest['name'];
		}*/

		$image_thumb = Image::getSize($image_thumb_type);

		if ($image_thumb == false)
		{
			$sql = 'SELECT `id_image_type` as `id`, `name`, `width`, `height` FROM `'._DB_PREFIX_.'image_type`
					WHERE `products` = 1 AND width > 1 ORDER BY `width` ASC LIMIT 1;';
			$image_thumb = Db::getInstance()->executeS($sql);
			$image_thumb = isset($image_thumb[0]) ? $image_thumb[0] : null;
		}

		if ($image == null || $image_thumb == null)
			return false;



		$prop['image_type']       = $image_type;
		$prop['image_thumb_type'] = $image_thumb_type;
		$prop['width']            = $image['width'];
		$prop['height']           = $image['height'];
		$prop['thumb_width']      = $image_thumb['width'];
		$prop['thumb_height']     = $image_thumb['height'];
		$prop['current_currency'] = isset($id_currency) ? Currency::getCurrency((int)$id_currency) : Currency::getCurrency($this->getConfiguration('CURRENCY'));
		$prop['mod_rewrite']      = $this->getConfiguration('PS_REWRITING_SETTINGS');

		return $prop;
	}

	/**
	 * Convert the url's to SSL protocol
	 *
	 * @param  string $url
	 * @return string
	 */
	public function convertToSSL($url)
	{
		// if (Configuration::get('PS_SSL_ENABLED') && isset($url[4]) && $url[4] !== 's')
		// 	$url = Tools::substr($url, 0, 4).'s'.Tools::substr($url, 4);
		return $url;
	}

	/**
	 * Returns a link to a product image for display
	 * Note: the new image filesystem stores product images in subdirectories of img/p/
	 *
	 * @param string $name rewrite link of the image
	 * @param string $ids id part of the image filename - can be "id_product-id_image" (legacy support, recommended) or "id_image" (new)
	 * @param string $type
	 */
	public function getImageLink($name, $ids, $type = null)
	{
		$link = $this->context->link->getImageLink($name, $ids, $type);
		$link = $this->relplaceAdminLink($link);
		$link = $this->convertToSSL($link);
		return $link;
	}

	/**
	 * Create a link to a product
	 *
	 * @param mixed $product Product object (can be an ID product, but deprecated)
	 * @param string $alias
	 * @param string $category
	 * @param string $ean13
	 * @param int $id_lang
	 * @param int $id_shop (since 1.5.0) ID shop need to be used when we generate a product link for a product in a cart
	 * @param int $ipa ID product attribute
	 * @return string
	 */
	public function getProductLink($product, $alias = null, $category = null, $ean13 = null, $id_lang = null, $id_shop = null, $ipa = 0, $force_routes = false)
	{
		$link = $this->context->link->getProductLink($product, $alias, $category, $ean13, $id_lang, $id_shop, $ipa, $force_routes);
		$link = $this->relplaceAdminLink($link);
		return $link;
	}

	/**
	 * Generate a product link
	 *
	 * @param  int $id_product
	 * @param  int $id_lang
	 * @return string
	 */
	public function makeProductLink($id_product, $id_lang)
	{
		$link = $this->getShopUrl().'index.php?id_product='.$id_product.'&controller=product&id_lang='.$id_lang;
		$link = $this->relplaceAdminLink($link);
		return $link;
	}

	/**
	 * Create and sanitize products template variables
	 *
	 * @param  int $id_lang
	 * @param  array $product
	 * @param  array $prop
	 */
	public function createProductTemplateVars($id_lang, &$product, $prop)
	{
		$product['image_path'] = $this->getImageLink($product['link_rewrite'], $product['id_image'], $prop['image_type']);
		$product['image_width']  = $prop['width'];
		$product['image_height'] = $prop['height'];

		if ($prop['mod_rewrite'] && (bool)$this->getConfiguration('PRODUCT_LINK_REWRITE'))
			$product['link'] = $this->getProductLink((int)$product['id_product']);
		else
			$product['link'] = $this->makeProductLink((int)$product['id_product'], (int)$id_lang);

		if ($this->getConfiguration('CAMPAIGN_ACTIVE'))
			$product['link'] = $this->setCampaignVariables( $product['link'], array('product_name' => $product['name']) );

		$product['link'] = $this->setStatisticsVariables($product['link']);

		$decimals = 2;

		$product['thumb_path']   = $this->getImageLink($product['link_rewrite'], $product['id_image'], $prop['image_thumb_type']);
		$product['thumb_width']  = $prop['thumb_width'];
		$product['thumb_height'] = $prop['thumb_height'];
		$product['currency']     = $prop['current_currency']['sign'];

		if (isset($product['specific_prices']['reduction_type']) && $product['specific_prices']['reduction_type'] === 'amount')
			$product['discount'] = number_format( abs ( ( ( (float)$product['specific_prices']['reduction'] / (float)$product['price_without_reduction'] ) * 100)), $decimals).'%';
		elseif (isset($product['specific_prices']['reduction_type']))
			$product['discount'] = number_format( abs ( $product['specific_prices']['reduction'] * 100 ), $decimals).'%';

		$product['price_convert']                   = (pqnp_ini_config('price_convert')
															? Tools::convertPrice((float)$product['price'], (int)$prop['current_currency']['id_currency'])
															: (float)$product['price']
														);

		$product['price_display']                   = Tools::displayPrice((float)$product['price_convert'], (int)$prop['current_currency']['id_currency']);

		$product['price_without_reduction']         = $product['price_without_reduction'];
		$product['price_without_reduction_convert'] = (pqnp_ini_config('price_convert')
														? Tools::convertPrice((float)$product['price_without_reduction'], (int)$prop['current_currency']['id_currency'])
														: (float)$product['price_without_reduction']
													);

		$product['price_without_reduction_display'] = Tools::displayPrice((float)$product['price_without_reduction_convert'], (int)$prop['current_currency']['id_currency']);

		$product['price_tax_exc']                   = $product['price_tax_exc'];
		$product['price_tax_exc_convert']           = (pqnp_ini_config('price_convert')
														? Tools::convertPrice((float)$product['price_tax_exc'], (int)$prop['current_currency']['id_currency'])
														: (float)$product['price_tax_exc']
													);
		$product['price_tax_exc_display']           = Tools::displayPrice((float)$product['price_tax_exc_convert'], (int)$prop['current_currency']['id_currency']);

		$product['wholesale_price']                 = $product['wholesale_price'];
		$product['wholesale_price_convert']         = (pqnp_ini_config('price_convert')
														? Tools::convertPrice((float)$product['wholesale_price'], (int)$prop['current_currency']['id_currency'])
														: (float)$product['wholesale_price']
													);
		$product['wholesale_price_display']         = Tools::displayPrice((float)$product['wholesale_price_convert'], (int)$prop['current_currency']['id_currency']);

		$unit_price = (float)$product['price'];
		$unit_price_tax_exc = (float)$product['price_tax_exc'];
		if ((float)$product['unit_price_ratio'] > 0)
		{
			$unit_price = (float)$product['price'] / (float)$product['unit_price_ratio'];
			$unit_price_tax_exc = (float)$product['price_tax_exc'] / (float)$product['unit_price_ratio'];
		}

		$product['unit_price']         = number_format($unit_price, $decimals);
		$product['unit_price_convert'] = number_format(pqnp_ini_config('price_convert')
											? Tools::convertPrice($unit_price, (int)$prop['current_currency']['id_currency'])
											: $unit_price, 
										$decimals);

		$product['unit_price_display'] = Tools::displayPrice($unit_price, (int)$prop['current_currency']['id_currency']);

		$product['unit_price_tax_exc']         = number_format($unit_price_tax_exc, $decimals);
		$product['unit_price_tax_exc_convert'] = number_format(pqnp_ini_config('price_convert')
													? Tools::convertPrice($unit_price_tax_exc, (int)$prop['current_currency']['id_currency'])
													: $unit_price_tax_exc, 
												$decimals);
		$product['unit_price_tax_exc_display'] = Tools::displayPrice($unit_price_tax_exc, (int)$prop['current_currency']['id_currency']);

		$product['pre_tax_retail_price'] = (float)$product['price_tax_exc'] + (float)$product['reduction'];
		
		$unity = (int)$product['unity'] <= 0 ? 1 : (int)$product['unity'];

		$unit_price_bo = (float)$product['pre_tax_retail_price'] / $unity;

		$product['unit_price_bo'] = number_format($unit_price_bo, $decimals);
		$product['unit_price_bo_convert'] = number_format(pqnp_ini_config('price_convert') 
												? Tools::convertPrice($unit_price_bo, (int)$prop['current_currency']['id_currency'])
												: $unit_price_bo,
											$decimals);
		$product['unit_price_bo_display'] = Tools::displayPrice($unit_price_bo, (int)$prop['current_currency']['id_currency']);

		// {$price_display|EUR}
		// {$name|12|...|en} // force langauge to english

		// the array will fill later in NewsletterProTemplate.php
		$product['dynamic_vars'] = array();
	}

	/**
	 * Get the campaign statistics params as string
	 *
	 * @return string
	 */
	public function getCampaignParams()
	{
		$params = $this->getCampaignParamsArray();

		$string = '';
		foreach ($params as $key => $line)
			$string .= '&'.$key.'='.$line;

		$string = str_replace("\n", '', $string);
		$string = preg_replace('/(?<=\w)\s(?=\w)/', '+', $string);
		$string = str_replace(' ', '', $string);

		return $string;
	}

	/**
	 * Append google campaign variables to the link
	 *
	 * @param  string $link
	 * @param  array $campaign_vars
	 * @return string
	 */
	public function setCampaignVariables($link, $campaign_vars)
	{
		$params = $this->getCampaignParams();

		foreach ($campaign_vars as &$var)
			$var = urlencode($var);

		$link = trim($link);
		$last_letter = Tools::substr($link, Tools::strlen($link) - 1, Tools::strlen($link));

		if (preg_match('/\?/', $link))
		{
			if ($last_letter === '?')
				$link = $link.Tools::substr($params, 1);
			else
				$link = $link.$params;
		}
		else
			$link = $link.'?'.Tools::substr($params, 1);

		$link = $this->replaceVars($link, $campaign_vars);

		$link = preg_replace('/\?\&/', '?', $link);

		return $link;
	}

	/**
	 * Set campagin statistics variables
	 *
	 * @param string $link
	 * @param array  $tpl_vars
	 */
	public function setStatisticsVariables($link, $tpl_vars = array())
	{
		$params = $this->getStatisticsParams();

		$link = trim($link);
		$last_letter = Tools::substr($link, Tools::strlen($link) - 1, Tools::strlen($link));

		if (preg_match('/\?/', $link))
		{
			if ($last_letter === '?')
				$link = $link.Tools::substr($params, 1);
			else
				$link = $link.$params;
		}
		else
		{
			if ($last_letter === '?')
				$link = $link.'?'.Tools::substr($params, 1);
			else
				$link = $link.'?'.$params;
		}

		$link = $this->replaceVars( $link, $tpl_vars );
		$link = preg_replace('/\?\&/', '?', $link);
		return $link;
	}

	/**
	 * Get the campaign statistics variables as string
	 *
	 * @return string
	 */
	public function getStatisticsParams()
	{
		$params = array(
			'newsletterpro_source' => 'newsletter',
			'id_newsletter' => '{id_newsletter_pro_tpl_history}',
		);

		$string = '';
		foreach ($params as $key => $line)
			$string .= '&'.$key.'='.$line;

		$string = str_replace("\n", '', $string);
		$string = preg_replace('/(?<=\w)\s(?=\w)/', '+', $string);
		$string = str_replace(' ', '', $string);
		return $string;
	}

	/**
	 * Replace the newsletter template variables
	 *
	 * @param  string $template
	 * @param  array $variables
	 * @return string
	 */
	public function replaceVars($template, $variables)
	{
		self::$replace_vars = $variables;
		$template = preg_replace_callback('/{(?P<tag>[^}]+)}/', 
					array($this, 'replaceCallback'),
					$template);
		return $template;
	}

	/**
	 * Replace the newsletter template variables callback
	 *
	 * @param  array $matches
	 * @return string
	 */
	public function replaceCallback($matches)
	{
		$tag = $matches[1]; // tag
		return isset(self::$replace_vars[$tag]) ? str_replace(' ', '+', trim(self::$replace_vars[$tag])) : '{'.$tag.'}';
	}

	/**
	 * Search product by name, reference, category
	 *
	 * @param  string $query
	 * @param  int $id_lang
	 * @param  int $id_currency
	 * @return json
	 */
	public function searchProducts($query, $id_lang = null, $id_currency = null)
	{
		$data = array();
		$id_category = (int)Tools::getValue('get_products');

		if (!isset($id_lang))
			$id_lang = (int)$this->getConfiguration('LANG');

		$order_by_prefix = null;
		$front           = false;
		$order_by        = '`name`';
		$order_way       = 'ASC';
		$limit           = 1000;
		$id_supplier     = false;

		$start = 0;
		$only_active = $this->getConfiguration('ONLY_ACTIVE_PRODUCTS');

		$query_new_products = $this->l('new products');
		$query_price_drop   = $this->l('price drop');
		$datetime = date('Y-m-d H:i:s');
		$sql = '';

		if ($this->isBetweenVersions('1.5.0.0', '1.5.0.6'))
		{
			$sql = 'SELECT p.*, pl.* , t.`rate` AS tax_rate, m.`name` AS manufacturer_name, s.`name` AS supplier_name,
				i.`id_image`, il.`legend`, cl.`name` AS category_default
				FROM `'._DB_PREFIX_.'product` p
				'.$this->context->shop->addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.$this->context->shop->addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
					AND tr.`id_country` = '.(int)$this->context->country->id.'
					AND tr.`id_state` = 0)
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)  AND i.cover = 1
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON ( i.`id_image` = il.`id_image`) AND il.`id_lang` = '.(int)$id_lang.'
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)

				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON( cl.`id_lang` = pl.`id_lang` AND p.`id_category_default` = cl.`id_category` ) '.
				($id_category ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '');

			if (preg_match('/^(\s+)?'.$query_price_drop.'(\s+)?$/i', $query, $price_drop))
			{
				$sql .= ' INNER JOIN `'._DB_PREFIX_.'specific_price` sp	ON (
					sp.`id_product` = p.`id_product`
					AND ((sp.`from` <= "'.$datetime.'" AND sp.`to` >= "'.$datetime.'") OR sp.`to` = "0000-00-00 00:00:00")
				) ';
			}

			$sql .= 'WHERE pl.`id_lang` = '.(int)$id_lang.
					($id_category ? ' AND c.`id_category` = '.(int)$id_category : '').
					($only_active ? ' AND p.`active` = 1' : '');

			if (preg_match('/^(\s+)?'.$query_new_products.'(\s+)?$/i', $query) && empty($price_drop))
			{
				$order_by = 'p.`date_add`';
				$order_way = 'DESC';
				$sql .= ' AND ( DATEDIFF(
						p.`date_add`,
						DATE_SUB(
							NOW(),
							INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
						)
					) > 0 ) = 1 ';
			}
			elseif (empty($price_drop))
				$sql .= ' AND ( pl.`name` LIKE "%'.pSQL($query).'%" OR cl.`name` LIKE "%'.pSQL($query).'%" OR p.`reference` LIKE "%'.pSQL($query).'%" )';

			$sql .= ' GROUP BY p.`id_product`
					ORDER BY '.(isset($order_by_prefix) ? pSQL($order_by_prefix ).'.' : '').''.pSQL($order_by).' '.pSQL($order_way).
					($limit > 0 ? ' LIMIT '.(int)$start.','.(int)$limit : '');

		}
		elseif ($this->isBetweenVersions('1.5.0.4', '1.5.0.18'))
		{
			$sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, product_attribute_shop.`id_product_attribute`,
					pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image`,
					il.`legend`, m.`name` AS manufacturer_name, tl.`name` AS tax_name, t.`rate`, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
						DAY)) > 0 AS new,
					(product_shop.`price` * IF(t.`rate`,((100 + (t.`rate`))/100),1)) AS orderprice
					FROM `'._DB_PREFIX_.'category_product` cp
					LEFT JOIN `'._DB_PREFIX_.'product` p
						ON p.`id_product` = cp.`id_product`
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
					ON (p.`id_product` = pa.`id_product`)
					'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
					'.Product::sqlStock('p', 'product_attribute_shop', false, $this->context->shop).'
					LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
						ON (product_shop.`id_category_default` = cl.`id_category`
						AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').'  )
					LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
						ON (p.`id_product` = pl.`id_product`
						AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'  )
					LEFT JOIN `'._DB_PREFIX_.'image` i
						ON (i.`id_product` = p.`id_product`)'.
						Shop::addSqlAssociation('image', 'i', false, 'i.cover=1').'
					LEFT JOIN `'._DB_PREFIX_.'image_lang` il
						ON (image_shop.`id_image` = il.`id_image`
						AND il.`id_lang` = '.(int)$id_lang.')
					LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr
						ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`
						AND tr.`id_country` = '.(int)$this->context->country->id.'
						AND tr.`id_state` = 0
						AND tr.`zipcode_from` = 0)
					LEFT JOIN `'._DB_PREFIX_.'tax` t
						ON (t.`id_tax` = tr.`id_tax`)
					LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl
						ON (t.`id_tax` = tl.`id_tax`
						AND tl.`id_lang` = '.(int)$id_lang.')
					LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
						ON m.`id_manufacturer` = p.`id_manufacturer`';

			if (preg_match('/^(\s+)?'.$query_price_drop.'(\s+)?$/i', $query, $price_drop))
			{
				$sql .= ' INNER JOIN `'._DB_PREFIX_.'specific_price` sp	ON (
					sp.`id_product` = p.`id_product`
					AND ((sp.`from` <= "'.pSQL($datetime).'" AND sp.`to` >= "'.pSQL($datetime).'") OR sp.`to` = "0000-00-00 00:00:00")
				) ';
			}

			$sql .= 'WHERE product_shop.`id_shop` = '.(int)$this->context->shop->id.'
					AND (pa.id_product_attribute IS NULL OR product_attribute_shop.id_shop='.(int)$this->context->shop->id.')
					AND (i.id_image IS NULL OR image_shop.id_shop='.(int)$this->context->shop->id.')'
						.($only_active ? ' AND product_shop.`active` = 1' : '')
						.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
						.($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '');

			if (preg_match('/^(\s+)?'.$query_new_products.'(\s+)?$/i', $query) && empty($price_drop))
			{
				$order_by = 'product_shop.`date_add`';
				$order_way = 'DESC';
				$sql .= ' AND ( DATEDIFF(
						product_shop.`date_add`,
						DATE_SUB(
							NOW(),
							INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
						)
					) > 0 ) = 1 ';
			}
			elseif (empty($price_drop))
				$sql .= ' AND ( pl.`name` LIKE "%'.pSQL($query).'%" OR cl.`name` LIKE "%'.pSQL($query).'%" OR p.`reference` LIKE "%'.pSQL($query).'%" )';

			$sql .= ' GROUP BY p.`id_product`
					ORDER BY '.pSQL($order_by).' '.pSQL($order_way).' LIMIT 0,'.(int)$limit.';';
		}
		else
		{
			$sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, product_attribute_shop.`id_product_attribute`,
					pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image`,
					il.`legend`, m.`name` AS manufacturer_name, tl.`name` AS tax_name, t.`rate`, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
						DAY)) > 0 AS new,
					(product_shop.`price` * IF(t.`rate`,((100 + (t.`rate`))/100),1)) AS orderprice
					FROM `'._DB_PREFIX_.'category_product` cp
					LEFT JOIN `'._DB_PREFIX_.'product` p
						ON p.`id_product` = cp.`id_product`
						'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
					ON (p.`id_product` = pa.`id_product`)
						'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
						'.Product::sqlStock('p', 'product_attribute_shop', false, $this->context->shop).'
					LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
						ON (product_shop.`id_category_default` = cl.`id_category`
						AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').'  )
					LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
						ON (p.`id_product` = pl.`id_product`
						AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'  )
					LEFT JOIN `'._DB_PREFIX_.'image` i
						ON (i.`id_product` = p.`id_product`)'.
						Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
					LEFT JOIN `'._DB_PREFIX_.'image_lang` il
						ON (image_shop.`id_image` = il.`id_image`
						AND il.`id_lang` = '.(int)$id_lang.')
					LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr
						ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`
						AND tr.`id_country` = '.(int)$this->context->country->id.'
						AND tr.`id_state` = 0
						AND tr.`zipcode_from` = 0)
					LEFT JOIN `'._DB_PREFIX_.'tax` t
						ON (t.`id_tax` = tr.`id_tax`)
					LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl
						ON (t.`id_tax` = tl.`id_tax`
						AND tl.`id_lang` = '.(int)$id_lang.')
					LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
						ON m.`id_manufacturer` = p.`id_manufacturer`';

			if (preg_match('/^(\s+)?'.$query_price_drop.'(\s+)?$/i', $query, $price_drop))
			{
				$sql .= ' INNER JOIN `'._DB_PREFIX_.'specific_price` sp	ON (
					sp.`id_product` = p.`id_product`
					AND ((sp.`from` <= "'.pSQL($datetime).'" AND sp.`to` >= "'.pSQL($datetime).'") OR sp.`to` = "0000-00-00 00:00:00")
				) ';
			}

			$sql .= ' WHERE product_shop.`id_shop` = '.(int)$this->context->shop->id.'
					AND (pa.id_product_attribute IS NULL OR product_attribute_shop.id_shop='.(int)$this->context->shop->id.')
					AND (i.id_image IS NULL OR image_shop.id_shop='.(int)$this->context->shop->id.')'
						.($only_active ? ' AND product_shop.`active` = 1' : '')
						.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
						.($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '');

			if (preg_match('/^(\s+)?'.$query_new_products.'(\s+)?$/i', $query) && empty($price_drop))
			{
				$order_by = 'product_shop.`date_add`';
				$order_way = 'DESC';
				$sql .= ' AND ( DATEDIFF(
						product_shop.`date_add`,
						DATE_SUB(
							NOW(),
							INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
						)
					) > 0 ) = 1 ';
			}
			elseif (empty($price_drop)){
			    $order_by = 'p.`supplier_reference`';
			    $order_way = 'ASC';
				$sql .= ' 
				    AND ( pl.`name` LIKE "%'.pSQL($query).'%" 
				        OR cl.`name` LIKE "%'.pSQL($query).'%" 
				        OR p.`reference` LIKE "%'.pSQL($query).'%"
				        OR p.`supplier_reference` LIKE "%'.pSQL($query).'%"
				)';
			}
			$sql .= ' GROUP BY p.`id_product`
					ORDER BY '.pSQL($order_by).' '.pSQL($order_way).' LIMIT 0,'.(int)$limit.';';
		}

		if ((int)pqnp_ini_config('get_category_products_prestashop')) {
			$products = Product::searchByName($id_lang, $query);
		} else {
			$products = Db::getInstance()->executeS($sql);
		}

		if (empty($products))
		{
			$data['products'] = $products;
			return Tools::jsonEncode( $data );
		}

		$data['products'] = $this->getProductsAttributes( $id_lang, $products, $id_currency );

		return Tools::jsonEncode( $data );
	}

	/**
	 * Sort products id's
	 *
	 * @param  array $products
	 * @param  int $ids
	 * @return array
	 */
	private function sortProductsIds($products, $ids)
	{
		$products_sorted = array();

		foreach ($products as $product)
		{
			$id_product = $product['id_product'];

			if (($search = array_search($id_product, $ids)) !== false)
				$products_sorted[$search] = $product;
		}

		ksort($products_sorted);
		return array_values($products_sorted);
	}

	/**
	 * Get product image
	 *
	 * @param  int $id
	 * @param  string $image_type
	 * @return json
	 */
	public function getImageOfProduct($id, $image_type)
	{
		$errors = array();
		$product = array();
		$response = array('status' => false, 'errors' => &$errors, 'product' => array() );

		if (!$id)
		{
			$response['status'] = true;
			return Tools::jsonEncode($response);
		}

		if ($image_size = Image::getSize($image_type))
		{
			$id_lang = (int)$this->getConfiguration('LANG');
			$prod = new Product($id, false, $id_lang);
			$id_image = (int)$prod->getCoverWs();

			$src = $this->getImageLink($prod->link_rewrite, $id_image, $image_type);

			$product['id_product'] = (int)$prod->id;
			$product['src'] = $src;
			$product['width'] = $image_size['width'];
			$product['height'] = $image_size['height'];
		}
		else
			$errors[] = $this->l('Invalid image type!');

		if (!$product)
			$errors[] = $this->l('Invalid product!');

		if (empty($errors))
		{
			$response['status'] = true;
			$response['product'] = $product;
		}

		return Tools::jsonEncode($response);
	}

	/**
	 * Get products images
	 *
	 * @param  int $ids
	 * @param  string $image_type
	 * @return json
	 */
	public function getImagesOfProducts($ids, $image_type)
	{
		$errors = array();
		$products = array();
		$response = array('status' => false, 'errors' => &$errors, 'products' => $products );

		if (!$ids)
		{
			$response['status'] = true;
			return Tools::jsonEncode($response);
		}

		if ($image_size = Image::getSize($image_type))
		{
			$sql = $this->getProductsByIdSql($ids);
			$prod = Db::getInstance()->executeS($sql);

			foreach ($prod as $product)
			{
				$id_product = (int)$product['id_product'];
				$products[$id_product]['id_product'] = $id_product;
				$products[$id_product]['src'] = $this->getImageLink($product['link_rewrite'], $product['id_image'], $image_type);
				$products[$id_product]['width'] = $image_size['width'];
				$products[$id_product]['height'] = $image_size['height'];
			}
		}
		else
			$errors[] = $this->l('Invalid image type!');

		if (empty($errors))
		{
			$response['status'] = true;
			$response['products'] = $products;
		}

		return Tools::jsonEncode($response);
	}

	/**
	 * Get products sql
	 *
	 * @param  array $ids
	 * @param  int $id_lang
	 * @return string
	 */
	private function getProductsByIdSql($ids, $id_lang = null)
	{
		if (!$ids)
			return '';

		$ids_implode = trim(implode(',', $ids), ',');

		$id_category = (int)Tools::getValue('get_products');
		if (!isset($id_lang))
			$id_lang = (int)$this->getConfiguration('LANG');

		$order_by_prefix = null;
		$front = false;
		$order_by = '`name`';
		$order_way = 'ASC';
		$limit = 1000;
		$id_supplier = false;

		$start = 0;
		$only_active = $this->getConfiguration('ONLY_ACTIVE_PRODUCTS');

		$sql = '';

		if ($this->isBetweenVersions('1.5.0.0', '1.5.0.6'))
		{
			$sql = 'SELECT p.*, pl.* , t.`rate` AS tax_rate, m.`name` AS manufacturer_name, s.`name` AS supplier_name,
				i.`id_image`, il.`legend`,cl.`name` AS category_default
				FROM `'._DB_PREFIX_.'product` p
				'.$this->context->shop->addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.$this->context->shop->addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
					AND tr.`id_country` = '.(int)$this->context->country->id.'
					AND tr.`id_state` = 0)
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)  AND i.cover = 1
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON ( i.`id_image` = il.`id_image`) AND il.`id_lang` = '.(int)$id_lang.'
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)

				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON( cl.`id_lang` = pl.`id_lang` AND p.`id_category_default` = cl.`id_category` ) '.
				($id_category ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '');

			$sql .= 'WHERE pl.`id_lang` = '.(int)$id_lang.
					($id_category ? ' AND c.`id_category` = '.(int)$id_category : '').
					($only_active ? ' AND p.`active` = 1' : '');

			$sql .= ' AND (p.`id_product` IN ('.$ids_implode.')) ';

			$sql .= ' GROUP BY p.`id_product`
					ORDER BY '.(isset($order_by_prefix) ? pSQL($order_by_prefix ).'.' : '').''.pSQL($order_by).' '.pSQL($order_way).
					($limit > 0 ? ' LIMIT '.(int)$start.','.(int)$limit : '');
		}
		elseif ($this->isBetweenVersions('1.5.0.4', '1.5.0.18'))
		{
			$sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, product_attribute_shop.`id_product_attribute`,
					pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image`,
					il.`legend`, m.`name` AS manufacturer_name, tl.`name` AS tax_name, t.`rate`, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
						DAY)) > 0 AS new,
					(product_shop.`price` * IF(t.`rate`,((100 + (t.`rate`))/100),1)) AS orderprice
					FROM `'._DB_PREFIX_.'category_product` cp
					LEFT JOIN `'._DB_PREFIX_.'product` p
						ON p.`id_product` = cp.`id_product`
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
					ON (p.`id_product` = pa.`id_product`)
					'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
					'.Product::sqlStock('p', 'product_attribute_shop', false, $this->context->shop).'
					LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
						ON (product_shop.`id_category_default` = cl.`id_category`
						AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').'  )
					LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
						ON (p.`id_product` = pl.`id_product`
						AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'  )
					LEFT JOIN `'._DB_PREFIX_.'image` i
						ON (i.`id_product` = p.`id_product`)'.
						Shop::addSqlAssociation('image', 'i', false, 'i.cover=1').'
					LEFT JOIN `'._DB_PREFIX_.'image_lang` il
						ON (image_shop.`id_image` = il.`id_image`
						AND il.`id_lang` = '.(int)$id_lang.')
					LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr
						ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`
						AND tr.`id_country` = '.(int)$this->context->country->id.'
						AND tr.`id_state` = 0
						AND tr.`zipcode_from` = 0)
					LEFT JOIN `'._DB_PREFIX_.'tax` t
						ON (t.`id_tax` = tr.`id_tax`)
					LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl
						ON (t.`id_tax` = tl.`id_tax`
						AND tl.`id_lang` = '.(int)$id_lang.')
					LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
						ON m.`id_manufacturer` = p.`id_manufacturer`';

			$sql .= 'WHERE product_shop.`id_shop` = '.(int)$this->context->shop->id.'
					AND (pa.id_product_attribute IS NULL OR product_attribute_shop.id_shop='.(int)$this->context->shop->id.')
					AND (i.id_image IS NULL OR image_shop.id_shop='.(int)$this->context->shop->id.')'
						.($only_active ? ' AND product_shop.`active` = 1' : '')
						.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
						.($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '');

			$sql .= ' AND (p.`id_product` IN ('.$ids_implode.')) ';

			$sql .= ' GROUP BY p.`id_product`
					ORDER BY '.pSQL($order_by).' '.pSQL($order_way).' LIMIT 0,'.(int)$limit.';';
		}
		else
		{
			$sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, product_attribute_shop.`id_product_attribute`,
					pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image`,
					il.`legend`, m.`name` AS manufacturer_name, tl.`name` AS tax_name, t.`rate`, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
						DAY)) > 0 AS new,
					(product_shop.`price` * IF(t.`rate`,((100 + (t.`rate`))/100),1)) AS orderprice
					FROM `'._DB_PREFIX_.'category_product` cp
					LEFT JOIN `'._DB_PREFIX_.'product` p
						ON p.`id_product` = cp.`id_product`
						'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
					ON (p.`id_product` = pa.`id_product`)
						'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
						'.Product::sqlStock('p', 'product_attribute_shop', false, $this->context->shop).'
					LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
						ON (product_shop.`id_category_default` = cl.`id_category`
						AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').'  )
					LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
						ON (p.`id_product` = pl.`id_product`
						AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'  )
					LEFT JOIN `'._DB_PREFIX_.'image` i
						ON (i.`id_product` = p.`id_product`)'.
						Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
					LEFT JOIN `'._DB_PREFIX_.'image_lang` il
						ON (image_shop.`id_image` = il.`id_image`
						AND il.`id_lang` = '.(int)$id_lang.')
					LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr
						ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`
						AND tr.`id_country` = '.(int)$this->context->country->id.'
						AND tr.`id_state` = 0
						AND tr.`zipcode_from` = 0)
					LEFT JOIN `'._DB_PREFIX_.'tax` t
						ON (t.`id_tax` = tr.`id_tax`)
					LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl
						ON (t.`id_tax` = tl.`id_tax`
						AND tl.`id_lang` = '.(int)$id_lang.')
					LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
						ON m.`id_manufacturer` = p.`id_manufacturer`';

			$sql .= ' WHERE product_shop.`id_shop` = '.(int)$this->context->shop->id.'
					AND (pa.id_product_attribute IS NULL OR product_attribute_shop.id_shop='.(int)$this->context->shop->id.')
					AND (i.id_image IS NULL OR image_shop.id_shop='.(int)$this->context->shop->id.')'
						.($only_active ? ' AND product_shop.`active` = 1' : '')
						.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
						.($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '');

			$sql .= ' AND (p.`id_product` IN ('.$ids_implode.')) ';

			$sql .= ' GROUP BY p.`id_product`
					ORDER BY '.pSQL($order_by).' '.pSQL($order_way).' LIMIT 0,'.(int)$limit.';';
		}
		return $sql;
	}

	/**
	 * Get products by id's
	 *
	 * @param  array $ids
	 * @return json
	 */
	public function getProductsById($ids)
	{
		$data = array();
		$sql = $this->getProductsByIdSql($ids);

		if (!$sql)
			return Tools::jsonEncode(array('products' => array()));

		$products = Db::getInstance()->executeS($sql);
		$products = $this->sortProductsIds($products, $ids);

		if (empty($products))
		{
			$data['products'] = $products;
			return Tools::jsonEncode( $data );
		}

		$id_lang = (int)$this->getConfiguration('LANG');
		$data['products'] = $this->getProductsAttributes( $id_lang, $products );

		return Tools::jsonEncode( $data );
	}

	/**
	 * Get product by id
	 *
	 * @param  int $id
	 * @param  int $id_lang
	 * @return boolean
	 */
	public function getProductById($id, $id_lang = null)
	{
		$sql = $this->getProductsByIdSql(array($id), $id_lang);

		if (!$sql)
			return false;

		$products = Db::getInstance()->executeS($sql);

		if (empty($products))
			return false;

		if (!isset($id_lang))
			$id_lang = (int)$this->getConfiguration('LANG');

		$products = $this->getProductsAttributes( $id_lang, $products );
		if (isset($products[0]))
			return $products[0];
		return false;
	}

	/**
	 * Get shop url
	 *
	 * @param  int $id_shop
	 * @return string
	 */
	public function getShopUrl($id_shop = null)
	{
		if (!isset($id_shop))
			$id_shop = (int)$this->context->shop->id;

		$base_uri = __PS_BASE_URI__;
		$default_shop_url = Tools::getHttpHost(true).$base_uri;

		$shop_url = '';

		if ((bool)Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && ($shop = Shop::getShop((int)$id_shop)))
			$shop_url = $this->context->shop->getBaseURL();
		else
			$shop_url = $default_shop_url;

		$len = Tools::strlen($shop_url);

		if (isset($shop_url[$len - 1]))
		{
			$char = $shop_url[$len - 1];
			if ($char !== '/')
				$shop_url .= '/';
		}

		$shop_url = $this->relplaceAdminLink($shop_url);
		return $shop_url;
	}

	/**
	 * Prepare emails for sending newsletters
	 *
	 * @return json
	 */
	public function prepareEmails($content = null, $emails_json = null)
	{
		$emails = Tools::jsonEncode(array());
		$post =& $_POST;

		if (isset($post['prepareEmails']))
			$emails = $post['prepareEmails'];

		NewsletterProLog::clearSend();

		NewsletterProSendConnection::clearAll();

		$emails = Tools::jsonDecode($emails);

		if (isset($emails_json))
			$emails = array_merge($emails, Tools::jsonDecode($emails_json));

		$response = NewsletterProAjaxResponse::newInstance();

		try
		{
			$prepare = NewsletterProSendManager::newInstance()->prepare;

			if (isset($content) && $content)
			{

				$template = NewsletterProTemplate::newString(array('', $content))->load();
				$prepare->setTemplate($template);
			}
			else
			{
				$template = NewsletterProTemplate::newFile(pqnp_config('NEWSLETTER_TEMPLATE'))->load();
				$prepare->setTemplate($template);
			}

			$prepare->setEmails($emails)->add();
			$response->set('count_emails', count($prepare->emails));
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	/**
	 * Unserialize a string
	 *
	 * @param  string $serialized
	 * @return array
	 */
	public static function unSerialize($serialized)
	{
		if (is_string($serialized) && preg_match('/a:[0-9]+:\{.*\}/', $serialized))
			return @unserialize($serialized);

		return array();
	}

	/**
	 * Select all customers
	 *
	 * @return json
	 */
	public function selectAllCustomers()
	{
		$sql_shops_id = '';
		$get_active_shops_id = NewsletterProTools::getActiveShopsId();

		foreach ($get_active_shops_id as $key => $id_shop)
			$sql_shops_id .= 'c.`id_shop` = '.(int)$id_shop.(end($get_active_shops_id) == $id_shop ? '' : ' OR ');

		$sql = 'SELECT c.`id_customer` AS `id`, c.`email`
				FROM `'._DB_PREFIX_.'customer` c
				WHERE( '.$sql_shops_id.' )
				ORDER BY c.`id_customer` DESC;';

		$userlist = Db::getInstance()->executeS($sql);

		foreach (array_keys($userlist) as $key)
			$userlist[$key]['user_type']  = 'customer';

		return Tools::jsonEncode($userlist);
	}

	public function leftMenuActive($val)
	{
		$response = NewsletterProAjaxResponse::newInstance();

		if (!$this->updateConfiguration('LEFT_MENU_ACTIVE', $val))
			$response->addError($this->l('The menu layout has not been changed in database.'));

		return $response->display();
	}

	/**
	 * Change settings view active only
	 *
	 * @param  bool $val
	 * @return json
	 */
	public function viewActiveOnly($val)
	{
		if ($this->updateConfiguration('VIEW_ACTIVE_ONLY', $val))
			return Tools::jsonEncode( array('status'=>true) );
		else
			return Tools::jsonEncode( array('status'=>false) );
	}

	/**
	 * Change settings convert css to inline style
	 *
	 * @param  bool $val
	 * @return json
	 */
	public function convertCssToInlineStyle($val)
	{
		if ($this->updateConfiguration('CONVERT_CSS_TO_INLINE_STYLE', $val))
			return Tools::jsonEncode( array('status'=>true) );
		else
			return Tools::jsonEncode( array('status'=>false) );
	}

	/**
	 * Change settings rum multiple tasks
	 *
	 * @param  bool $val
	 * @return json
	 */
	public function runMultimpleTasks($val)
	{
		if ($this->updateConfiguration('RUN_MULTIPLE_TASKS', $val))
			return Tools::jsonEncode( array('status'=>true) );
		else
			return Tools::jsonEncode( array('status'=>false) );
	}

	/**
	 * Change settings
	 *
	 * @param  bool $val
	 * @return json
	 */
	public function displayCustomerAccountSettings($val)
	{
		if ($this->updateConfiguration('DISPLYA_MY_ACCOUNT_NP_SETTINGS', $val))
			return Tools::jsonEncode( array('status'=>true) );
		else
			return Tools::jsonEncode( array('status'=>false) );
	}

	/**
	 * Change settings subscribe by category
	 *
	 * @param  bool $val
	 * @return json
	 */
	public function subscribeByCategory($val)
	{
		if ($this->updateConfiguration('SUBSCRIBE_BY_CATEGORY', $val))
			return Tools::jsonEncode( array('status'=>true) );
		else
			return Tools::jsonEncode( array('status'=>false) );
	}

	/**
	 * Change settings subscribe by list of interests
	 *
	 * @param  bool $val
	 * @return json
	 */
	public function subscribeByCListOfInterest($val)
	{
		if ($this->updateConfiguration('CUSTOMER_SUBSCRIBE_BY_LOI', $val))
			return Tools::jsonEncode( array('status'=>true) );
		else
			return Tools::jsonEncode( array('status'=>false) );
	}

	/**
	 * Change settings send newsletter on subscribe
	 *
	 * @param  bool $val
	 * @return json
	 */
	public function sendNewsletterOnSubscribe($val)
	{
		if ($this->updateConfiguration('SEND_NEWSLETTER_ON_SUBSCRIBE', $val))
			return Tools::jsonEncode( array('status'=>true) );
		else
			return Tools::jsonEncode( array('status'=>false) );
	}

	/**
	 * Change settings forward feature active
	 *
	 * @param  bool $val
	 * @return json
	 */
	public function forwardingFeatureActive($val)
	{
		if ($this->updateConfiguration('FWD_FEATURE_ACTIVE', $val))
			return Tools::jsonEncode( array('status'=>true) );
		else
			return Tools::jsonEncode( array('status'=>false) );
	}

	/**
	 * Change settings forward feature active
	 *
	 * @param  bool $val
	 * @return json
	 */
	public function sendEmbededImagesActive($val)
	{
		$response = NewsletterProAjaxResponse::newInstance(array());

		if (ini_get('allow_url_fopen'))
		{
			if (!$this->updateConfiguration('SEND_EMBEDED_IMAGES', $val))
				$response->addError($this->l('An error occurred.'));
		}
		else
		{

			$this->updateConfiguration('SEND_EMBEDED_IMAGES', 0);
			$response->addError($this->l('You can embed files from a URL if allow_url_fopen is on in php.ini.'));
		}

		return $response->display();
	}

	/**
	 * Change settings view active products
	 *
	 * @param  bool $val
	 * @return json
	 */
	public function chimpSyncUnsubscribed($val)
	{
		if ($this->updateConfiguration('CHIMP_SYNC_UNSUBSCRIBED', $val))
			return Tools::jsonEncode( array('status'=>true) );
		else
			return Tools::jsonEncode( array('status'=>false) );
	}

	/**
	 * Change settings view active products
	 *
	 * @param  bool $val
	 * @return json
	 */
	public function displayOnliActiveProducts($val)
	{
		if ($this->updateConfiguration('ONLY_ACTIVE_PRODUCTS', $val))
			return Tools::jsonEncode( array('status'=>true) );
		else
			return Tools::jsonEncode( array('status'=>false) );
	}

	/**
	 * Change settings product link rewrite
	 *
	 * @param  bool $val
	 * @return json
	 */
	public function productFriendlyURL($val)
	{
		if ($this->updateConfiguration('PRODUCT_LINK_REWRITE', $val))
			return Tools::jsonEncode( array('status'=>true) );
		else
			return Tools::jsonEncode( array('status'=>false) );
	}

	public function clearModuleCache()
	{
		$response = NewsletterProAjaxResponse::newInstance(array(
			'status' => false,
		));

		$media = NewsletterProMedia::newInstance()
			->addCSS('admin_newsletter_pro.cache.css', AdminNewsletterPro::getCSSMedia())
			->addJS('admin_newsletter_pro.cache.js', AdminNewsletterPro::getJSMedia())
			->cache(true);
		
		return $response->display();
	}

	/**
	 * Change settings debug mode
	 *
	 * @param  bool $val
	 * @return json
	 */
	public function debugMode($bool)
	{
		if ($this->updateConfiguration('DEBUG_MODE', $bool))
			return Tools::jsonEncode( array('status'=>true) );
		else
			return Tools::jsonEncode( array('status'=>false) );
	}

	public function useCache($bool)
	{

		if ($bool) {
			$media = NewsletterProMedia::newInstance()
				->addCSS('admin_newsletter_pro.cache.css', AdminNewsletterPro::getCSSMedia())
				->addJS('admin_newsletter_pro.cache.js', AdminNewsletterPro::getJSMedia())
				->cache(false);
		}

		if ($this->updateConfiguration('USE_CACHE', $bool))
			return Tools::jsonEncode( array('status'=>true) );
		else
			return Tools::jsonEncode( array('status'=>false) );
	}

	/**
	 * Change settings subscription secure subscribe
	 *
	 * @param  bool $val
	 * @return json
	 */
	public function subscriptionSecureSubscribe($bool)
	{
		if ($this->updateConfiguration('SUBSCRIPTION_SECURE_SUBSCRIBE', $bool))
			return Tools::jsonEncode( array('status'=>true) );
		else
			return Tools::jsonEncode( array('status'=>false) );
	}

	/**
	 * Clear the secure subscribed temporary email addresses
	 *
	 * @param  bool $val
	 * @return json
	 */
	public function clearSubscribersTemp()
	{
		$errors = array();
		$response = array('status' => false, 'errors' => &$errors, 'msg' => '');

		$sql = 'DELETE FROM `'._DB_PREFIX_.'newsletter_pro_subscribers_temp` WHERE `date_add` < "'.date('Y-m-d H:i:s', strtotime('-1 weeks')).'"';

		if (!Db::getInstance()->execute($sql))
			$errors[] = $this->l('The emails has not been cleared.');

		if (empty($errors))
		{
			$response['msg'] = $this->l('The emails has been cleared successfully.');
			$response['status'] = true;
		}

		return Tools::jsonEncode($response);
	}

	/**
	 * Imprt email addresses from the blcok newsletter module
	 *
	 * @return json
	 */
	public function importEmailsFromBlockNewsletter($newsletter_date_add = null)
	{
		$id_default_shop = (int)$this->getConfiguration('PS_SHOP_DEFAULT');

		$errors = array();
		$response = array('status' => false, 'errors' => &$errors, 'msg' => '');

		$bn_info = $this->getBlockNewsletterInfo();

		$cout_success = 0;
		$cout_errors  = 0;

		if ($bn_info['isInstalled'])
		{
			$emails_to_import = Db::getInstance()->executeS('
				SELECT * FROM `'._DB_PREFIX_.'newsletter`
				'.(isset($newsletter_date_add) ? ' WHERE newsletter_date_add >= "'.pSQL($newsletter_date_add).'"' : '').'
			');

			if ($emails_to_import)
			{
				foreach ($emails_to_import as $row)
				{
					try
					{
						$id_shop                    = isset($row['id_shop']) ? $row['id_shop'] : $id_default_shop;
						$id_shop_group              = isset($row['id_shop_group']) ? $row['id_shop_group'] : $id_default_shop;
						$email                      = $row['email'];
						$newsletter_date_add        = isset($row['newsletter_date_add']) ? $row['newsletter_date_add'] : date('Y-m-d H:i:s');
						$ip_registration_newsletter = isset($row['ip_registration_newsletter']) ? $row['ip_registration_newsletter'] : '';
						$active                     = isset($row['active']) ? $row['active'] : 1;

						$id = NewsletterProSubscribers::getIdByEmail($email);
						// check if the email not exists
						if (!$id)
						{
							$subscriber = new NewsletterProSubscribers();
							$subscriber->id_shop                    = (int)$id_shop;
							$subscriber->id_shop_group              = (int)$id_shop_group;
							$subscriber->email                      = $email;
							$subscriber->newsletter_date_add        = $newsletter_date_add;
							$subscriber->ip_registration_newsletter = $ip_registration_newsletter;
							$subscriber->active                     = (int)$active;

							if ($subscriber->add())
								$cout_success++;
							else
								$cout_errors++;
						}
					}
					catch(Exception $e)
					{
						$cout_errors++;
					}
				}
			}
		}

		if (empty($errors))
		{
			if ($cout_success > 0 && $cout_errors == 0)
				$response['msg'] = sprintf($this->l('(%s) emails has been imported successfully.'), $cout_success);
			else if ($cout_success > 0 && $cout_errors > 0)
				$response['msg'] = sprintf($this->l('(%s) emails has been imported successfully and at (%s) emails an error occurred.'), $cout_success, $cout_errors);
			else if ($cout_success == 0 && $cout_errors > 0)
				$response['msg'] = sprintf($this->l('An error occurred at (%s) emails.'), $cout_errors);
			else if (!empty($emails_to_import))
				$response['msg'] = $this->l('All the emails are imported.');
			else
				$response['msg'] = $this->l('There are no emails to import.');

			$response['status'] = true;
		}

		return Tools::jsonEncode($response);
	}

	public function importEmailsFromBlockNewsletterCron($newsletter_date_add)
	{
		return $this->importEmailsFromBlockNewsletter($newsletter_date_add);
	}

	/**
	 * Get newsletter pro subscription info
	 *
	 * @return array
	 */
	public function getNewsletterProSubscriptionInfo()
	{
		$hooks = array();

		foreach ($this->subscribe_hooks as $key => $hook)
		{
			$id_hook = Hook::getIdByName($hook);

			$hooks[$key]['id_hook']   = $id_hook;
			$hooks[$key]['name']      = $hook;
			$hooks[$key]['isRegistred'] = false;
			$hooks[$key]['position']  = 0;

			if ($this->isRegisteredInHook($hook) && $this->isHookableOn($hook))
			{
				$hooks[$key]['isRegistred'] = true;
				$hooks[$key]['position'] = $this->getModulePosition($this, $id_hook);
			}
		}

		return array(
			'name'        => $this->name,
			'isInstalled' => true,
			'isEnabled'   => true,
			'hooks'       => $hooks,
		);
	}

	/**
	 * Get newsletter pro info
	 *
	 * @return array
	 */
	public function getNewsletterProInfo()
	{
		$hooks = array();

		foreach ($this->getHooksList() as $key => $hook)
		{
			$id_hook = Hook::getIdByName($hook);

			$hooks[$key]['id_hook']   = $id_hook;
			$hooks[$key]['name']      = $hook;
			$hooks[$key]['isRegistred'] = false;
			$hooks[$key]['position']  = 0;

			if ($this->isRegisteredInHook($hook) && $this->isHookableOn($hook))
			{
				$hooks[$key]['isRegistred'] = true;
				$hooks[$key]['position'] = $this->getModulePosition($this, $id_hook);
			}
		}

		return array(
			'name'        => $this->name,
			'isInstalled' => true,
			'isEnabled'   => true,
			'hooks'       => $hooks,
		);
	}

	/**
	 * Get module position
	 *
	 * @param  object $obj
	 * @param  int $id_hook
	 * @return int
	 */
	public function getModulePosition($obj, $id_hook)
	{
		if (method_exists($obj, 'getPosition'))
			return $obj->getPosition($id_hook);
		else
		{
			if (isset(Hook::$preloadModulesFromHooks))
				if (isset(Hook::$preloadModulesFromHooks[$id_hook]))
					if (isset(Hook::$preloadModulesFromHooks[$id_hook]['module_position'][$obj->id]))
						return Hook::$preloadModulesFromHooks[$id_hook]['module_position'][$obj->id];
					else
						return 0;

			$result = Db::getInstance()->getRow('
				SELECT `position`
				FROM `'._DB_PREFIX_.'hook_module`
				WHERE `id_hook` = '.(int)$id_hook.'
				AND `id_module` = '.(int)$obj->id.'
				AND `id_shop` = '.(int)Context::getContext()->shop->id);

			return (int)$result['position'];
		}
	}

	/**
	 * Get block newsletter info
	 *
	 * @return array
	 */
	public function getBlockNewsletterInfo()
	{
		$hooks = array();
		$name = 'blocknewsletter';
		$is_installed = Module::isInstalled($name);

		if ($is_installed)
		{
			$blocknewsletter = Module::getInstanceByName($name);

			foreach ($this->subscribe_hooks as $key => $hook)
			{
				$id_hook = Hook::getIdByName($hook);

				$hooks[$key]['id_hook']   = $id_hook;
				$hooks[$key]['name']      = $hook;
				$hooks[$key]['isRegistred'] = false;
				$hooks[$key]['position']  = 0;

				if ($blocknewsletter->isRegisteredInHook($hook) && $blocknewsletter->isHookableOn($hook))
				{
					$hooks[$key]['isRegistred'] = true;
					$hooks[$key]['position'] = $this->getModulePosition($blocknewsletter, $id_hook);
				}
			}
		}

		return array(
			'name'        => $name,
			'isInstalled' => $is_installed,
			'isEnabled'   => NewsletterPro::isModuleEnabled($name),
			'hooks'       => $hooks,
		);
	}

	/**
	 * Check if the module is enabled
	 *
	 * @param  string  $name
	 * @return boolean
	 */
	public static function isModuleEnabled($name)
	{
		if (method_exists('Module', 'isEnabled'))
			return Module::isEnabled($name);
		else
		{
			if (!Cache::isStored('Module::isEnabled'.$name))
			{
				$active = false;
				$id_module = Db::getInstance()->getValue('SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE `name` = \''.pSQL($name).'\'');
				if (Db::getInstance()->getValue('SELECT `id_module` FROM `'._DB_PREFIX_.'module_shop` WHERE `id_module` = '.(int)$id_module.' AND `id_shop` = '.(int)Context::getContext()->shop->id))
					$active = true;
				Cache::store('Module::isEnabled'.$name, (bool)$active);
			}
			return Cache::retrieve('Module::isEnabled'.$name);
		}
	}

	/**
	 * Get module google analytics info
	 *
	 * @return array
	 */
	public function getGAnalyticsModuleInfo()
	{
		$hooks = array();
		$name = 'ganalytics';
		$is_installed = Module::isInstalled($name);

		if ($is_installed)
		{
			$module = Module::getInstanceByName($name);

			foreach (array('displayHeader', 'displayFooter', 'displayOrderConfirmation') as $key => $hook)
			{
				$id_hook = Hook::getIdByName($hook);

				$hooks[$key]['id_hook']   = $id_hook;
				$hooks[$key]['name']      = $hook;
				$hooks[$key]['isRegistred'] = false;
				$hooks[$key]['position']  = 0;

				if ($module->isRegisteredInHook($hook) && $module->isHookableOn($hook))
				{
					$hooks[$key]['isRegistred'] = true;
					$hooks[$key]['position'] = $this->getModulePosition($module, $id_hook);
				}
			}
		}

		return array(
			'name'        => $name,
			'isInstalled' => $is_installed,
			'isEnabled'   => NewsletterPro::isModuleEnabled($name),
			'hooks'       => $hooks,
		);
	}

	/**
	 * Get block newsletter registred hooks
	 *
	 * @return array
	 */
	public function getBlockNewsletterHooksRegistered()
	{
		$hooks = array();
		$bn_info = $this->getBlockNewsletterInfo();

		foreach ($bn_info['hooks'] as $hook)
		{
			if ($hook['isRegistred'])
				$hooks[] = $hook;
		}
		return $hooks;
	}

	/**
	 * Get newsletter pro subscription hooks
	 *
	 * @return array
	 */
	public function getNewsletterProSubscriptionHooks()
	{
		$np_hooks = $this->getNewsletterProSubscriptionInfo();
		return $np_hooks['hooks'];
	}

	private function buildRegisteredHooks($hooks, $bn_hooks = array())
	{
		$register_hooks = array();
		$start_hooks = $hooks;

		foreach ($bn_hooks as $bn_hook)
		{
			foreach ($start_hooks as $key => $hook)
			{
				if ($bn_hook['isRegistred'] && $bn_hook['name'] == $hook)
				{
					$register_hooks[] = array(
											'id_hook'     => $bn_hook['id_hook'],
											'name'        => $hook,
											'hasPosition' => true,
											'position'    => $bn_hook['position']
										);

					unset($start_hooks[$key]);
				}
			}
		}

		foreach ($start_hooks as $hook)
		{
			$register_hooks[] = array(
									'id_hook'     => Hook::getIdByName($hook),
									'name'        => $hook,
									'hasPosition' => false,
									'position'    => 0,
								);
		}
		return $register_hooks;
	}

	/**
	 * Build unregistred hooks
	 *
	 * @param  array $hooks
	 * @param  array  $bn_hooks
	 * @return array
	 */
	public function buildUnregistredHooks($hooks, $bn_hooks = array())
	{
		$bn_hooks_names = array();
		foreach ($bn_hooks as $hook)
			$bn_hooks_names[] = $hook['name'];

		return array_diff($bn_hooks_names, $hooks);
	}

	/**
	 * Check if the module is registred in a hook
	 *
	 * @param  string  $hook
	 * @return boolean
	 */
	public function isRegisteredInHook($hook)
	{
		if ($hook == 'header')
		{
			$hook_name = Db::getInstance()->getValue('
				SELECT `name` FROM `'._DB_PREFIX_.'hook_alias` WHERE `alias` = "'.pSQL($hook).'"
			');

			if ($hook_name)
				$hook = $hook_name;
		}

		return parent::isRegisteredInHook($hook);
	}

	/**
	 * Enable the newsletter pro subscription
	 * The module will be registered in the hook displayTop
	 *
	 * @param  array  $hooks Regsitrer module to the hooks, leave it empty if you want to copy the hooks from the blocknewsletter module
	 * @return null
	 */
	public function enableNewsletterProSubscription($hooks = array())
	{
		if (!$this->isRegisteredInHook('displayTop'))
			$this->registerHook('displayTop');

		if (!$this->isRegisteredInHook('displayHeader'))
			$this->registerHook('displayHeader');

		$bn_info = $this->getBlockNewsletterInfo();
		$np_info = $this->getNewsletterProSubscriptionInfo();

		if ($bn_info['isInstalled'])
		{
			$blocknewsletter = Module::getInstanceByName($bn_info['name']);

			if ($bn_info['isEnabled'])
				$blocknewsletter->disable();

			$register_hooks = $this->buildRegisteredHooks($hooks, $bn_info['hooks']);

			if (!empty($register_hooks))
			{
				foreach ($register_hooks as $hook)
				{
					$this->registerHook($hook['name']);

					if ($hook['hasPosition'])
						$this->updatePosition($hook['id_hook'], 0, $hook['position']);
				}
			}
			else
			{
				// copy the hooks from the blocknewsletter module
				foreach ($bn_info['hooks'] as $hook)
				{
					if ($hook['isRegistred'])
					{
						$this->registerHook($hook['name']);
						$this->updatePosition($hook['id_hook'], 0, $hook['position']);
					}
				}
			}

			$unregister_hooks = $this->buildUnregistredHooks($hooks, $np_info['hooks']);

			if (!empty($unregister_hooks))
				foreach ($unregister_hooks as $hook_name)
					$this->unregisterHook($hook_name);
		}
		else
		{
			if (!empty($hooks))
			{
				foreach ($hooks as $hook)
					$this->registerHook($hook);
			}
		}
	}

	/**
	 * Register/Unregister hooks by hooks info
	 *
	 * @param  array $hooks_info
	 * @return bool
	 */
	public static function executeHooksByInfo($hooks_info)
	{
		$module = NewsletterPro::getInstance();
		$success = array();

		foreach ($hooks_info as $hook)
		{
			if ($hook['isRegistred'])
			{
				$success[] = $module->registerHook($hook['name']);

				$module->updatePosition($hook['id_hook'], 0, ++$hook['position']);
			}
			else
				$success[] = $module->unregisterHook($hook['name']);
		}

		return !in_array(false, $success);
	}

	/**
	 * Disable newsletter pro subscription option
	 */
	public function disableNewsletterProSubscription()
	{
		$bn_info = $this->getBlockNewsletterInfo();
		$np_info = $this->getNewsletterProSubscriptionInfo();

		if ($bn_info['isInstalled'])
		{
			$blocknewsletter = Module::getInstanceByName($bn_info['name']);

			if (!$bn_info['isEnabled'])
				$blocknewsletter->enable();

			foreach ($bn_info['hooks'] as $hook)
			{
				if ($hook['isRegistred'])
				{
					$blocknewsletter->registerHook($hook['name']);
					$blocknewsletter->updatePosition($hook['id_hook'], 0, $hook['position']);
				}
			}
		}

		foreach ($np_info['hooks'] as $hook)
		{
			if ($hook['isRegistred'])
				$this->unregisterHook($hook['id_hook']);
		}
	}

	/**
	 * Setup the newsletter pro subscription
	 *
	 * @param  bool $bool Enable/Disable the newsletter pro subscription
	 * @param  array  $hooks Regsitrer module to the hooks, leave it empty if you want to copy the hooks from the blocknewsletter module
	 * @return null
	 */
	public function newsletterproSubscriptionActive($bool, $hooks = array())
	{
		$errors = array();
		$response = array('status' => false, 'errors' => &$errors);

		if ($this->updateConfiguration('SUBSCRIPTION_ACTIVE', $bool))
		{
			if ($bool)
				$this->enableNewsletterProSubscription($hooks);
			else
				$this->disableNewsletterProSubscription();
		}
		else
			$errors[] = $this->l('An error occurred when updating this settings.');

		if (empty($errors))
			$response['status'] = true;

		return Tools::jsonEncode($response);
	}

	/**
	 * Change newsletter template
	 *
	 * @param  string $template
	 * @return json
	 */
	public function changeNewsletterTemplate($template)
	{
		$path = $this->tpl_location.'newsletter/'.$template;

		if (preg_match('/^.*.html$/', $template) && file_exists($path))
		{
			$this->updateConfiguration('NEWSLETTER_TEMPLATE', $template);
			return Tools::jsonEncode( array('status'=>true, 'msg'=>'') );
		}
		else
			return Tools::jsonEncode( array('status'=>false, 'msg'=> $this->l('Invalid template') ) );
	}

	/**
	 * Change product template
	 *
	 * @param  string $template
	 * @return json
	 */
	public function changeProductTemplate($template)
	{
		$path = $this->tpl_location.'product/'.$template;

		if (preg_match('/^.*.html$/', $template) && file_exists($path))
		{
			$this->updateConfiguration('PRODUCT_TEMPLATE', $template);
			return Tools::jsonEncode( array('status'=>true, 'msg'=>'') );
		}
		else
			return Tools::jsonEncode( array('status'=>false, 'msg'=> $this->l('Invalid template') ) );
	}

	/**
	 * Change produt image size
	 *
	 * @param  string $image_size
	 * @return json
	 */
	public function changeProductImageSize($image_size)
	{
		$errors = array();

		if (Image::getSize( $image_size ) != false)
			$this->updateConfiguration('IMAGE_TYPE', trim($image_size));
		else
			$errors[] = $this->l('Invalid image type!');

		return Tools::jsonEncode($errors);
	}

	/**
	 * Change product currency
	 *
	 * @param  int $get_currency
	 * @return json
	 */
	public function changeProductCurrency($get_currency)
	{
		$errors = array();

		$currencies = Currency::getCurrencies();
		$currency_exists = false;
		foreach ($currencies as $currency)
			if ((int)$currency['id_currency'] == (int)$get_currency)
			{
				$currency_exists = true;
				break;
			}

		$get_currency = ( $currency_exists == true ) ? (int)$get_currency : (int)$this->getConfiguration('PS_CURRENCY_DEFAULT');

		if (!$this->updateConfiguration('CURRENCY', $get_currency))
			$errors[] = $this->l('Currency not changed');

		return Tools::jsonEncode($errors);
	}

	/**
	 * Change product language
	 *
	 * @param  int $id
	 * @return json
	 */
	public function changeProductLanguage($id)
	{
		$errors = array();
		$languages = Language::getLanguages(true, $this->context->shop->id);

		$id_lang_exists = false;
		foreach ($languages as $language)
		{
			if ((int)$language['id_lang'] == (int)$id)
			{
				$id_lang_exists = true;
				break;
			}
		}
		$id = ( $id_lang_exists == true ) ? (int)$id : (int)$this->getConfiguration('PS_LANG_DEFAULT');

		if (!$this->updateConfiguration('LANG', $id))
			$errors[] = $this->l('Language not changed');

		return Tools::jsonEncode($errors);
	}

	/**
	 * Get product template content
	 *
	 * @param  array $data
	 * @param  boolean $readcontent
	 * @return json
	 */
	public function getProductTemplateContent($data, $readcontent)
	{
		$errors = array();
		$content = '';
		$columns = 0;
		$render = '';
		$response = array(
			'status' => false, 
			'errors' => &$errors, 
			'content' => &$content, 
			'render' => &$render, 
			'columns' => &$columns
		);
		$name = $data['filename'];
		$path = $data['path'];

		if (file_exists($path))
		{
			if ((Tools::file_get_contents($path)) !== false)
			{
				if (!$this->updateConfiguration('PRODUCT_TEMPLATE', $name))
					$errors[] = $this->l('Error on creating the configuration!');
				else
				{
					if (NewsletterPro::getConfiguration('DEBUG_MODE'))
						$content = $this->getProductContent();
					else
						$content = @$this->getProductContent();

					$columns = $this->getProductColumns();

					if ($readcontent)
					{
						try 
						{
							if (NewsletterPro::getConfiguration('DEBUG_MODE'))
								$render = $this->getProductContent(true);
							else
								$render = @$this->getProductContent(true);
						} 
						catch (Exception $e)
						{
							$errors[] = $e->getMessage();
						}
					}
				}
			}
			else
				$errors[] = $this->l('The file cannot be read, check the CHMOD !');
		}
		else
			$errors[] = $this->l('File not exists!');

		if (empty($errors))
			$response['status'] = true;

		return Tools::jsonEncode($response);
	}

	/**
	 * Get product content
	 *
	 * @param  boolean $view
	 * @return string
	 */
	public function getProductContent($view = false)
	{
		$template = $this->getConfiguration('PRODUCT_TEMPLATE');
		$path = $this->tpl_location.'product/'.$template;
		if (file_exists($path))
		{
			$content = Tools::file_get_contents($path);

			if ($view == false)
				return $content;

			// remove comments
			// $content = preg_replace('/<!--[\s\S]*?-->/', '', $content);
			
			// remove columns for the old templates
			// $content = preg_replace('/\{columns=\d+\}/', '', $content);

			$image_type = (string)$this->getConfiguration('IMAGE_TYPE');
			$img_name = (string)$this->context->language->iso_code.'-default-'.$image_type.'.jpg';
			$image_path = _PS_PROD_IMG_DIR_.$img_name;

			if (file_exists($image_path))
				$image_path = Tools::getHttpHost(true)._THEME_PROD_DIR_.$img_name;
			else
			{
				$files = scandir(_PS_PROD_IMG_DIR_);
				$files = preg_grep('/^('.$this->context->language->iso_code.').*'.$image_type.'.*.jpg$/', $files);

				$img_name = array_values($files);
				$image_path = isset($img_name[0]) ? $img_name[0] : '';
				$image_path = Tools::getHttpHost(true)._THEME_PROD_DIR_.$image_path;
			}

			$size = Image::getSize($image_type);

			$header_content = '';
			if (preg_match('/<!-- start header -->\s*?<!--(?P<header>[\s\S]*)-->\s*?<!-- end header -->/', $content, $match))
				$header_content	= trim($match['header']);

			if (!preg_match('/content\s+=\s+template/', $header_content))
				$content = str_replace( array('{image_path}', '{image_width}', '{image_height}'), array($image_path, $size['width'], $size['height'] ), $content );

			return $content;
		}
		return false;
	}

	/**
	 * Get product columns
	 *
	 * @return int
	 */
	public function getProductColumns()
	{
		$template = $this->getConfiguration('PRODUCT_TEMPLATE');
		$path = $this->tpl_location.'product/'.$template;
		if (file_exists($path))
		{
			$content = Tools::file_get_contents($path);

			if (preg_match('/\{columns=(?P<number>\d+)\}/', $content, $match))
				return (int)$match['number'];
		}
		return 0;
	}

	/**
	 * Delete images
	 *
	 * @param  array $data
	 * @return json
	 */
	public function deleteImage($data)
	{
		$errors         = array();
		$response       = array('status'=> 0, 'errors' => &$errors);
		$name           = $data['filename'];
		$path           = $data['path'];
		$thumb_filename = $data['thumb_filename'];
		$thumb_path     = $data['thumb_path'];

		if (file_exists($path))
		{
			if (!unlink($path))
				$errors[] = sprintf($this->l('You cannot delete the image "%s", please check the CHMOD !'), $name);
		}
		else
			$errors[] = sprintf($this->l('The image thumbnail "%s" file not exists!'), $name);

		if (file_exists($thumb_path))
		{
			if (!unlink($thumb_path))
				$errors[] = sprintf($this->l('You cannot delete the image thumbnail "%s", please check the CHMOD !'), $thumb_filename);
		}
		else
			$errors[] = sprintf($this->l('The image thumbnail "%s" file not exists!'), $thumb_filename);

		if (empty($errors))
			$response['status'] = 1;

		return Tools::jsonEncode($response);
	}

	/**
	 * Get send history
	 *
	 * @return json
	 */
	public function getSendHistory()
	{
		$sql = 'SELECT  s.`id_newsletter_pro_send`,
						s.`id_newsletter_pro_tpl_history`,
						s.`active`,
						s.`emails_count`,
						s.`emails_success`,
						s.`emails_error`,
						s.`emails_completed`,
						s.`error_msg`,
						s.`date`,
						s.`template`,
						ss.`id_newsletter_pro_send_step`,
						h.`clicks`,
						h.`opened`,
						h.`unsubscribed`,
						h.`fwd_unsubscribed`,

				GROUP_CONCAT(ss.`id_newsletter_pro_send_step`) AS `steps`
				FROM `'._DB_PREFIX_.'newsletter_pro_send` s
					LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_send_step` ss
						ON (ss.`id_newsletter_pro_send` = s.`id_newsletter_pro_send`)

					LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_tpl_history` h
						ON (s.`id_newsletter_pro_tpl_history` = h.`id_newsletter_pro_tpl_history`)

				WHERE s.`active` = 0
				GROUP BY s.`id_newsletter_pro_send`
				ORDER BY s.`date` DESC;';

		if ($histories = Db::getInstance()->executeS($sql))
		{
			foreach ($histories as &$history)
			{
				$error_msg_db = Db::getInstance()->executeS('
					SELECT `error_msg` 
					FROM `'._DB_PREFIX_.'newsletter_pro_send_step`
					WHERE `id_newsletter_pro_send` = '.(int)$history['id_newsletter_pro_send'].'
				');

				$error_msg = array();

				foreach ($error_msg_db as $value) 
				{
					$value['error_msg'] = trim($value['error_msg']);

					if (!empty($value['error_msg']))
					{
						$em = unserialize($value['error_msg']);
						foreach ($em as $val) 
							foreach (array_keys($val) as $error)
								$error_msg[] = $error;
					}
				}

				$history['date'] = date('Y-m-d', strtotime($history['date']));
				$history['error_msg'] = array_unique($error_msg);
				$history['template'] = Tools::ucfirst(pathinfo($history['template'], PATHINFO_FILENAME));
			}

			return Tools::jsonEncode($histories);
		}
		else
			return Tools::jsonEncode(array());
	}

	/**
	 * Get forwarders list
	 *
	 * @return json
	 */
	public function getForwardList()
	{
		$sql = 'SELECT `id_newsletter_pro_forward`, `from`, `to`, `date_add`, COUNT(*) as `count`
				FROM `'._DB_PREFIX_.'newsletter_pro_forward`
				GROUP BY `from`';

		if ($result = Db::getInstance()->executeS($sql))
			return Tools::jsonEncode($result);
		else
			return Tools::jsonEncode(array());
	}

	/**
	 * Search forwarder
	 *
	 * @param  string $value
	 * @return json
	 */
	public function searchForwarder($value)
	{
		$sql = 'SELECT `id_newsletter_pro_forward`, `from`, `to`, `date_add`, COUNT(*) as `count`
				FROM `'._DB_PREFIX_.'newsletter_pro_forward`
				WHERE `from` LIKE "%'.pSQL($value).'%" OR `to` LIKE "%'.pSQL($value).'%"
				GROUP BY `from`';

		if ($result = Db::getInstance()->executeS($sql))
			return Tools::jsonEncode($result);
		else
			return Tools::jsonEncode(array());
	}

	/**
	 * Sleep newsletter
	 *
	 * @param  int $seconds
	 * @return json
	 */
	public function sleepNewsletter($seconds)
	{
		if ($this->updateConfiguration('SLEEP', $seconds))
			return Tools::jsonEncode( array( 'status'=>true, 'msg'=> $this->l('Updated') ) );
		else
			return Tools::jsonEncode( array( 'status'=>false, 'msg'=> $this->l('Update error') ) );
	}

	/**
	 * Verify template filename
	 *
	 * @param  string $name
	 * @return boolean
	 */
	public function verifyName($name)
	{
		// frech characters àâçéèêëîïôûùüÿñæœ
		// turkey characters İıÖöÜüÇçĞğŞş₤
		if ($name == '')
			return $this->l('Please insert the name');
		elseif (Tools::strlen($name) < 4)
			return $this->l('Name must contain at least 4 characters');
		elseif (!preg_match('/^[a-zA-Z0-9\-_\.\%àâçéèêëîïôûùüÿñæœčšđžćČŠĐĆŽİıÖöÜüÇçĞğŞş₤]+$/i', $name)) // elseif (!NewsletterProTools::isFileName($name)) 
			return $this->l('Name contain illegal characters');
		elseif (preg_match('/\.html$/', $name))
			return $this->l('Do not use .html extension');

		return true;
	}

	/**
	 * Show newsletter help template
	 *
	 * @return string
	 */
	public function showNewsletterHelp()
	{
		include_once dirname(__FILE__).'/classes/NewsletterProExtendTemplateVars.php';

		$external_vars = NewsletterProExtendTemplateVars::$external_vars;
		$help_vars = array();
		foreach ($external_vars as $path => $to_load)
		{
			if ($to_load)
			{

				$path = dirname(__FILE__).'/'.$path;
				$variable_name = pathinfo($path, PATHINFO_FILENAME);

				$help = pqnp_template_path($this->dir_location.'views/templates/admin/variables_help/'.$variable_name.'.tpl');

				if (file_exists($help) && is_file($help))
					$help_vars[] = $this->context->smarty->fetch($help);
			}
		}
		$this->context->smarty->assign('help_vars', $help_vars);
		return $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/admin/help_newsletter.tpl'));
	}

	/**
	 * Show product help template
	 *
	 * @return string
	 */
	public function showProductHelp()
	{
		$languages = Language::getLanguages(false);
		$languages_iso = array();

		foreach ($languages as $language)
			$languages_iso[$language['id_lang']] = $language['iso_code'];

		$currencies = NewsletterPro::getCurrenciesByIdShop($this->context->shop->id);

		$currencies_iso = array();

		foreach ($currencies as $currency)
			$currencies_iso[$currency['id_currency']] = $currency['iso_code'];

		$this->context->smarty->assign(array(
			'lang_iso' => trim(implode(', ', $languages_iso)),
			'currencies_iso' => trim(implode(', ', $currencies_iso)),
		));
		return $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/admin/help_product.tpl'));
	}

	/**
	 * Get currencies by is shop
	 *
	 * @param  integer $id_shop
	 * @return array
	 */
	public static function getCurrenciesByIdShop($id_shop = 0)
	{
		// if there is a problem with the currencies, uncomment this line
		// return Currency::getCurrencies(false, true);

		$context = Context::getContext();

		if (method_exists('Currency', 'getCurrenciesByIdShop'))
			return Currency::getCurrenciesByIdShop($context->shop->id);
		else
			return Db::getInstance()->executeS('
				SELECT *
				FROM `'._DB_PREFIX_.'currency` c
				LEFT JOIN `'._DB_PREFIX_.'currency_shop` cs ON (cs.`id_currency` = c.`id_currency`)
				'.($id_shop ? ' WHERE cs.`id_shop` = '.(int)$id_shop : '').'
				ORDER BY `name` ASC
			');
	}

	/**
	 * Search emails
	 *
	 * @return json
	 */
	public function searchEmails()
	{
		$query = Tools::getValue('search_emails');

		$sql = 'SELECT c.`id_customer`, c.`firstname`, c.`lastname`, c.`email`, gl.`name` AS `group_name`, l.`name` AS `lang_name`, s.`name` AS `shop_name`
				FROM `'._DB_PREFIX_.'customer` c
				LEFT JOIN `'._DB_PREFIX_.'group_lang` gl ON ( gl.`id_group` = c.`id_default_group` ) AND gl.`id_lang` = '.(int)$this->context->language->id.'
				LEFT JOIN `'._DB_PREFIX_.'lang` l ON ( l.`id_lang` = c.`id_lang` )
				LEFT JOIN `'._DB_PREFIX_.'shop` s ON ( s.`id_shop` = c.`id_shop` )
				WHERE c.`firstname` LIKE "%'.pSQL($query).'%"
				OR c.`lastname` LIKE "%'.pSQL($query).'%" OR c.`email` LIKE "%'.pSQL($query).'%"
				OR gl.`name` LIKE "%'.pSQL($query).'%"  OR l.`name` LIKE "%'.pSQL($query).'%"  OR s.`name` LIKE "%'.pSQL($query).'%"';
		$result = Db::getInstance()->executeS($sql);
		$costomers_id = array();

		if (!empty($result))
			foreach ($result as $customer)
				$costomers_id[] = $customer['id_customer'];

		return Tools::jsonEncode( $costomers_id );
	}

	/**
	 * Display produt image
	 *
	 * @param  boolean $boolean
	 * @return json
	 */
	public function displayProductImage($boolean)
	{
		$data = array();
		$boolean = ($boolean == 'true') ? 1 : 0;
		if ($this->updateConfiguration('DISPLAY_PRODUCT_IMAGE', $boolean))
		{
			$data['status'] = true;
			$data['msg'] = '';
		}
		else
		{
			$data['status'] = false;
			$data['msg'] = $this->l('The field cannot be updated');
		}

		return Tools::jsonEncode($data);
	}

	/**
	 * Clear prestashop cache
	 *
	 * @return boolean
	 */
	public function clearCache()
	{
		if (method_exists('Tools', 'clearSmartyCache'))
		{
			if ($this->isLowerVersion('1.6'))
			{
				if (method_exists('Tools', 'clearSmartyCache'))
					Tools::clearSmartyCache();

				if (method_exists('Media', 'clearCache'))
					Media::clearCache();

				$autoload = Autoload::getInstance();
				if (method_exists($autoload, 'generateIndex'))
					$autoload->generateIndex();
			}
			else
			{
				if (method_exists('Tools', 'clearSmartyCache'))
					Tools::clearSmartyCache();

				if (method_exists('Tools', 'clearXMLCache'))
					Tools::clearXMLCache();

				if (method_exists('Media', 'clearCache'))
					Media::clearCache();

				if (method_exists('Tools', 'generateIndex'))
					Tools::generateIndex();
			}
			return true;
		}
		return false;
	}

	/**
	 * Clear newsletter history
	 *
	 * @return json
	 */
	public function clearHistory()
	{
		$response = array( 'status' => false );

		$sql = 'SELECT `id_newsletter_pro_tpl_history` AS `id`
				FROM `'._DB_PREFIX_.'newsletter_pro_send`
				WHERE `id_newsletter_pro_tpl_history` > 0;';

		if ($ids = Db::getInstance()->executeS($sql))
			foreach ($ids as $id)
			{
				Db::getInstance()->delete('newsletter_pro_tpl_history', '`id_newsletter_pro_tpl_history`='.(int)$id['id']);
				Db::getInstance()->delete('newsletter_pro_unsibscribed', '`id_newsletter_pro_tpl_history`='.(int)$id['id']);
			}

		if (Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'newsletter_pro_send` WHERE 1'))
			$response['status'] = true;
		else
			$response['status'] = false;
		return Tools::jsonEncode($response);
	}

	/**
	 * Save smtp configuration
	 *
	 * @param  array $data
	 * @return json
	 */
	public function saveSMTP($data)
	{
		$response = array( 'status' => false, 'errors' => array() );

		if ((int)pqnp_ini_config('demo_mode'))
		{
			$name = Tools::strtolower(trim($data['name']));
			$name_demo = Tools::strtolower(pqnp_demo_mode('demo_freeze_smtp_name'));

			if ($name == $name_demo)
			{
				$response['status'] = false;
				$response['errors'][] = $this->l('This is a demo, you cannot override this SMTP connection.');
				return Tools::jsonEncode($response);
			}
		}

		if (!trim($data['name']))
			$this->_errors[] = $this->l('The SMTP "Name" field is required.');
		if (!trim($data['from_email']))
			$this->_errors[] = $this->l('The "From email" field is required.');
		else
		{

			$sql = 'SELECT `name` FROM `'._DB_PREFIX_.'newsletter_pro_smtp` WHERE `id_newsletter_pro_smtp`='.(int)$data['id_newsletter_pro_smtp'].'';
			$name = Db::getInstance()->getValue($sql);

			if ($name !== $data['name'])
			{
				$sql = 'SELECT count(*) FROM `'._DB_PREFIX_.'newsletter_pro_smtp` WHERE `name` = "'.pSQL($data['name']).'" AND `id_newsletter_pro_smtp`!='.(int)$data['id_newsletter_pro_smtp'].'';

				if (Db::getInstance()->getValue($sql))
					$this->_errors[] = $this->l('Duplicate SMTP name.');
			}
		}

		if (empty($this->_errors))
		{
			$mail = new NewsletterProMail((int)$data['id_newsletter_pro_smtp']);
			$mail->name       = $data['name'];
			$mail->method     = $data['method'];
			$mail->from_name  = $data['from_name'];
			$mail->from_email = $data['from_email'];
			$mail->reply_to   = $data['reply_to'];
			$mail->domain     = $data['domain'];
			$mail->server     = $data['server'];
			$mail->user       = $data['user'];
			$mail->passwd     = (trim($data['passwd']) == '' ? $mail->passwd : $data['passwd']);
			$mail->encryption = $data['encryption'];
			$mail->port       = $data['port'];
			$mail->list_unsubscribe_active = (int)$data['list_unsubscribe_active'];
			$mail->list_unsubscribe_email = trim($data['list_unsubscribe_email']);

			if (Validate::isLoadedObject($mail)
				&& $mail->update()
				&& $this->updateConfiguration('SMTP', $mail->id))
			{
				$response['obj'] = $data;
				$response['status'] = true;
			}
			else
				$response['status'] = false;
		}
		else
		{
			$response['errors'] = $this->_errors;
			$response['status'] = false;
		}
		return Tools::jsonEncode( $response );
	}

	/**
	 * Delete smtp configuration
	 *
	 * @param  int $id
	 * @return json
	 */
	public function deleteSMTP($id)
	{
		$response = NewsletterProAjaxResponse::newInstance(array(
			'status' => false,
			'msg'    => '',
			'count'  => 0
		));

		try
		{
			$sql = 'SELECT count(*)
					FROM `'._DB_PREFIX_.'newsletter_pro_task`
					WHERE `id_newsletter_pro_smtp`='.(int)$id.'
					AND `done` = 0;';

			if ($count = Db::getInstance()->getValue($sql))
			{
				$response->addError($this->l('The SMTP cannot be deleted because is used in').' '.$count.' '.$this->l('tasks. Change the tasks SMTP or delete them.'));
				$response->set('count', $count);
			}
			else
			{
				$mail = NewsletterProMail::newInstance((int)$id);

				if (!Validate::isLoadedObject($mail))
					return $response->addError(sprintf($this->l('Invalid connection id "%s"'), $id))->display();

				if ((int)pqnp_ini_config('demo_mode'))
				{
					$demo_return = NewsletterProDemoMode::deleteSMTP($mail->name);

					if ($demo_return)
						return $demo_return;
				}

				$error_msg = $this->l('An error occurred when deleting the connection.');

				if (!$mail->delete())
					$response->addError($error_msg);
				else
				{
					if (!NewsletterProSendConnection::deleteBySmtpId((int)$id))
						$response->addError($error_msg);
				}

				if (!$this->countSMTP())
					$this->updateConfiguration('SMTP', '0');

				$response->set('status', true);
				$response->set('count', Db::getInstance()->Affected_Rows());
			}
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	/**
	 * Count smtp connections
	 *
	 * @return int
	 */
	public function countSMTP()
	{
		$sql = 'SELECT count(*) FROM `'._DB_PREFIX_.'newsletter_pro_smtp` WHERE 1';
		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Add a new smtp connection
	 *
	 * @param array $data
	 */
	public function addSMTP($data)
	{
		$response = array( 'status' => false, 'errors' => array() );

		if (!trim($data['name']))
			$this->_errors[] = $this->l('The SMTP "Name" is required.');
		if (!trim($data['from_email']))
			$this->_errors[] = $this->l('The "From email" field is required.');
		else
		{
			$sql = 'SELECT count(*) FROM `'._DB_PREFIX_.'newsletter_pro_smtp` WHERE `name` = "'.pSQL($data['name']).'"';
			if (Db::getInstance()->getValue($sql))
				$this->_errors[] = $this->l('Duplicate name.');
		}

		if (!trim($data['user']))
			$this->_errors[] = $this->l('The "SMTP user" is required.');

		if (!$this->_errors)
		{

			$mail = new NewsletterProMail();
			$mail->name       = trim($data['name']);
			$mail->method     = $data['method'];
			$mail->from_name  = trim($data['from_name']);
			$mail->from_email = trim($data['from_email']);
			$mail->reply_to   = trim($data['reply_to']);
			$mail->domain     = trim($data['domain']);
			$mail->server     = trim($data['server']);
			$mail->user       = trim($data['user']);
			$mail->passwd     = trim($data['passwd']);
			$mail->encryption = $data['encryption'];
			$mail->port       = $data['port'];
			$mail->list_unsubscribe_active = (int)$data['list_unsubscribe_active'];
			$mail->list_unsubscribe_email = trim($data['list_unsubscribe_email']);

			if ($mail->add())
			{
				if ($mail->id && $this->updateConfiguration( 'SMTP', $mail->id ))
				{
					$sql = 'SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_smtp` WHERE `id_newsletter_pro_smtp`='.(int)$mail->id.'';
					$last_row = Db::getInstance()->getRow($sql);

					$response['obj'] = $last_row;
					$response['status'] = true;
				}
				else
					$response['status'] = false;
			}
			else
				$response['status'] = false;
		}
		else
		{
			$response['errors'] = $this->_errors;
			$response['status'] = false;
		}

		return Tools::jsonEncode( $response );
	}

	/**
	 * Change smtp
	 *
	 * @param  int $id
	 * @return json
	 */
	public function changeSMTP($id)
	{
		$response = array('status' => false, 'msg' => '');

		if ($this->updateConfiguration( 'SMTP', $id ))
			$response['status'] = true;
		else
		{
			$response['msg'] = $this->l('The SMTP cannot be changed.');
			$response['status'] = false;
		}

		return Tools::jsonEncode($response);
	}

	/**
	 * Change newsletter pro smtp active configuration
	 *
	 * @param  boolean $bool
	 * @return json
	 */
	public function smtpActive($bool)
	{
		$bool = $bool == 'true' ? 1 : 0;
		$response = array('status' => false);
		if ($this->updateConfiguration('SMTP_ACTIVE', $bool))
			$response['status'] = true;
		else
			$response['status'] = false;

		return Tools::jsonEncode( $response );
	}

	/**
	 * Replace admin link
	 *
	 * @param  string $path
	 * @return string
	 */
	public static function relplaceAdminLink($path)
	{
		if (self::REPLACE_ADMIN_PATH) {
			$module = NewsletterPro::getInstance();

			if (!empty($module->admin_name))
			{
				$name = str_replace($module->admin_name.'/', '', $path);
				return $name;
			}
			else
				return $path;
		}

		return $path;
	}

	/**
	 * Empty the personal list with email addresses
	 *
	 * @return json
	 */
	public function emptyAddedEmails()
	{
		$response = array('status'=>false);
		if (Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'newsletter_pro_email` WHERE 1'))
			$response['status'] = true;
		return Tools::jsonEncode($response);
	}

	/**
	 * Update the google analytics id
	 *
	 * @param  int $g_analytics_id
	 * @return json
	 */
	public function updateGAnalyticsID($g_analytics_id)
	{
		$response = array('status' => false );
		if ($this->updateConfiguration('GOOGLE_ANALYTICS_ID', $g_analytics_id))
			$response['status'] = true;
		return Tools::jsonEncode($response);
	}

	/**
	 * Activate google analytics configuration
	 *
	 * @param  boolean $bool
	 * @return json
	 */
	public function activeGAnalytics($bool)
	{
		$bool = $bool == 'true' ? 1 : 0;
		$response = array('status' => false );
		if ($this->updateConfiguration('GOOGLE_ANALYTICS_ACTIVE', $bool))
			$response['status'] = true;
		return Tools::jsonEncode($response);
	}

	/**
	 * Activate universal analytics option
	 *
	 * @param  boolean $bool
	 * @return json
	 */
	public function universalAnaliytics($bool)
	{
		$bool = $bool == 'true' ? 1 : 0;
		$response = array('status' => false );
		if ($this->updateConfiguration('GOOGLE_UNIVERSAL_ANALYTICS_ACTIVE', $bool))
			$response['status'] = true;
		return Tools::jsonEncode($response);
	}

	/**
	 * Activate google analytics campaign
	 *
	 * @param  boolean $bool
	 * @return json
	 */
	public function activeCampaign($bool)
	{
		$ga_info = $this->getGAnalyticsModuleInfo();
		if ($ga_info['isInstalled'])
		{
			$header_hook = false;

			foreach ($ga_info['hooks'] as $hook)
			{
				if (($hook['name'] == 'displayHeader' || $hook['name'] == 'header') && $hook['isRegistred'])
				{
					$header_hook = $hook;
					break;
				}
			}

			if ($header_hook)
			{
				$np_position = (int)$this->getModulePosition($this, $header_hook['id_hook']);
				$ga_position = (int)$header_hook['position'];

				if ($ga_position <= $np_position)
					$this->updatePosition($header_hook['id_hook'], 0, $ga_position);
			}
		}

		$bool = $bool == 'true' ? 1 : 0;
		$response = array('status' => false );
		if ($this->updateConfiguration('CAMPAIGN_ACTIVE', $bool))
			$response['status'] = true;
		return Tools::jsonEncode($response);
	}

	/**
	 * Create image thumbnail
	 *
	 * @param  string $path
	 * @param  string $temp_path
	 * @param  string $name
	 * @param  string $thumb_height
	 * @return int
	 */
	public function createImageThumb($path, $temp_path, $name, $thumb_height)
	{
		list(, , $image_type) = getimagesize($temp_path);

		$new_image = null;
		if ($image_type == IMAGETYPE_JPEG)
			$new_image = imagecreatefromjpeg($temp_path);
		elseif ($image_type == IMAGETYPE_GIF)
			$new_image = imagecreatefromgif ($temp_path);
		elseif ($image_type == IMAGETYPE_PNG)
			$new_image = imagecreatefrompng($temp_path);

		$get_width = imagesx($new_image);
		$get_height = imagesy($new_image);

		$ratio = $thumb_height / $get_height;
		$thumb_width = $get_width * $ratio;

		$thumb = imagecreatetruecolor($thumb_width, $thumb_height);
		imagecopyresampled($thumb, $new_image, 0, 0, 0, 0, $thumb_width, $thumb_height, $get_width, $get_height);

		$thumb_name = 'thumb_'.$name;
		$thumb_path = $path.'thumb/'.$thumb_name;
		if ($image_type == IMAGETYPE_JPEG)
			imagejpeg($thumb, $thumb_path, 100);
		elseif ($image_type == IMAGETYPE_GIF)
			imagegif($thumb, $thumb_path);
		elseif ($image_type == IMAGETYPE_PNG)
			imagepng($thumb, $thumb_path);
	}

	/**
	 * Upload image
	 *
	 * @param  array $image
	 * @param  int $img_width
	 * @return json
	 */
	public function uploadImage($image, $img_width)
	{
		$errors = array();
		$response = array('status' => false, 'errors' => &$errors);

		if (!empty($image))
		{
			$path = $this->dir_location.'images/';
			$validate_file = $this->verifyFileErros($image);

			if ($validate_file === true)
			{
				if (preg_match('/\.jpg|\.jpeg|\.gif|\.png$/i', $image['name']))
				{
					if (!file_exists($path))
						$errors[] = $this->l('Images path does not exist');
					else
					{
						$image_ext = pathinfo($image['name'], PATHINFO_EXTENSION);
						$name = uniqid().'.'.$image_ext;
						$name = str_replace(' ', '-', $name);
						$temp_path = $image['tmp_name'];
						$full_path = $path.$name;

						if ((int)$img_width > 0 && Tools::strlen((string)$img_width) <= 4)
						{
							list(, , $image_type) = getimagesize($temp_path);

							$new_image = null;
							if ($image_type == IMAGETYPE_JPEG)
								$new_image = imagecreatefromjpeg($temp_path);
							elseif ($image_type == IMAGETYPE_GIF)
								$new_image = imagecreatefromgif ($temp_path);
							elseif ($image_type == IMAGETYPE_PNG)
								$new_image = imagecreatefrompng($temp_path);

							$get_width = imagesx($new_image);
							$get_height = imagesy($new_image);

							$ratio = $img_width / $get_width;
							$img_height = $get_height * $ratio;

							$img = imagecreatetruecolor($img_width, $img_height);
							imagecopyresampled($img, $new_image, 0, 0, 0, 0, $img_width, $img_height, $get_width, $get_height);

							$img_path = $path.$name;
							if ($image_type == IMAGETYPE_JPEG)
								imagejpeg($img, $img_path, 100);
							elseif ($image_type == IMAGETYPE_GIF)
								imagegif($img, $img_path);
							elseif ($image_type == IMAGETYPE_PNG)
								imagepng($img, $img_path);

							$this->createImageThumb($path, $temp_path, $name, 50);

							$response['status'] = true;
						}
						else
						{
							if (move_uploaded_file($temp_path, $full_path))
							{
								$this->createImageThumb($path, $full_path, $name, 50);
								$response['status'] = true;
							}
							else
								$errors[] = $this->l('Image was not uploaded, please check the CHMOD.');
						}
					}
				}
				else
					$errors[] = $this->l('The file extension is not allowed');
			}
			else
				$errors[] = $validate_file;
		}
		else
			$errors[] = $this->l('No file was uploaded');

		return Tools::jsonEncode($response);
	}

	/**
	 * Upload a csv file
	 *
	 * @param  string $file
	 * @return json
	 */
	public function uploadCSV($file)
	{
		$response = array( 'status' => false, 'msg' => '', 'data' => array() );

		$validate = $this->verifyFileErros($file);
		if ($validate === true)
		{
			$csv_path = 'csv/import/';
			$path = $this->dir_location.$csv_path;
			$name = $file['name'];
			$tmp_name = $file['tmp_name'];

			$extension = pathinfo( $name, PATHINFO_EXTENSION );
			if (preg_match('/csv/i', $extension))
			{
				$unique_name = $this->uniqueName($name);
				$full_path = $path.$unique_name;

				if (file_exists($path) && move_uploaded_file($tmp_name, $full_path))
				{
					$response['status'] = true;
					$response['data']['name'] = $unique_name;
					$response['data']['uri'] = $this->uri_location.$unique_name;
					$response['data']['url'] = $this->url_location.$unique_name;
				}
				else
					$response['msg'] = $this->l('The file cannot be uploaded.');
			}
			else
				$response['msg'] = $this->l('The file extension is not allowed.');
		}
		else
			$response['msg'] = $validate;

		return Tools::jsonEncode( $response );
	}

	/**
	 * Get an unique name
	 *
	 * @param  string $name
	 * @return string
	 */
	public function uniqueName($name)
	{
		$name = preg_replace('/[\s]/', '_', trim($name) );
		return uniqid().'_'.$name;
	}

	/**
	 * Verify upload file errors
	 *
	 * @param  string $file
	 * @return string
	 */
	public function verifyFileErros($file)
	{
		$message = '';

		if ($file['error'] == UPLOAD_ERR_OK && $file['size'] != 0)
			return true;
		elseif ($file['error'] == UPLOAD_ERR_INI_SIZE)
			$message = $this->l('The uploaded file exceeds the upload_max_filesize directive in php.ini');
		elseif ($file['error'] == UPLOAD_ERR_PARTIAL)
			$message = $this->l('The uploaded file was only partially uploaded');
		elseif ($file['error'] == UPLOAD_ERR_NO_FILE)
			$message = $this->l('No file was uploaded');
		elseif ($file['error'] == UPLOAD_ERR_NO_TMP_DIR)
			$message = $this->l('Missing a temporary folder');
		elseif ($file['error'] == UPLOAD_ERR_CANT_WRITE)
			$message = $this->l('Failed to write file to disk');
		elseif ($file['error'] == UPLOAD_ERR_EXTENSION)
			$message = $this->l('A PHP extension stopped the file upload');
		else
			$message = $this->l('File error');

		return $message;
	}

	/**
	 * Delete the csv file by name
	 *
	 * @param  string $name
	 * @return json
	 */
	public function deleteCSVByName($name)
	{
		$response = array( 'status' => false, 'msg' => '' );

		$path = $this->dir_location.'csv/import/'.$name;
		if (file_exists($path) && unlink($path))
			$response['status'] = true;
		else
			$response['msg'] = $this->l('The file cannot be deleted.');

		return Tools::jsonEncode($response);
	}

	/**
	 * Get the csv file line info
	 * 
	 * @param  array/null $line
	 * @return array
	 */
	private function getCSVLineInfo($line)
	{
		$from = 1;
		$to = 0;
		if (isset($line) && is_array($line) && isset($line['from'], $line['to']))
		{
			$from = (int)$line['from'] < 1 ? 1 : (int)$line['from'];
			$to   = (int)$line['to'] < 0 ? 0 : (int)$line['to'];
		}

		return array(
			'from' => $from,
			'to'   => $to,
		);
	}

	/**
	 * Load a csv file
	 *
	 * @param  string $filename
	 * @param  string $delimiter
	 * @return boolean
	 */
	public function loadCSV($filename, $delimiter = ',', $line = null)
	{
		$full_path = $this->dir_location.'csv/import/'.$filename;
		
		$line_info = $this->getCSVLineInfo($line);
		$from      = $line_info['from'];
		$to        = $line_info['to'];

		if ($data = $this->csvToArray($full_path, $delimiter, $from, $to))
		{
			$db_fields = array(
					'email'                      => 'Email',
					'firstname'                  => 'First Name',
					'lastname'                   => 'Last Name',
					'id_lang'                    => 'Language ID',
					'id_shop'                    => 'Shop ID',
					'id_shop_group'              => 'Shop Group ID',
					'ip_registration_newsletter' => 'Registration IP Address',
					'active'                     => 'Active',
				);

			$count     = null;
			$header    = null;
			$rows      = null;

			extract($data);

			$this->context->smarty->assign(array(
					'count'     => $count - 1,
					'header'    => $header,
					'rows'      => $rows,
					'db_fields' => $db_fields,
				));

			return $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/admin/preview_details.tpl'));
		}
		return false;
	}

	/**
	 * Sanitize utf8
	 *
	 * @param  string $value
	 * @return string
	 */
	public function sanitizeUTF8($value)
	{
		return preg_replace('/[^(\x20-\x7F)]*/', '', trim($value, " \t\n\r\0\x0B,;"));
	}

	/**
	 * Import emails from a csv file
	 *
	 * @param  string $filename
	 * @param  string $delimiter
	 * @param  array  $fields_get
	 * @return boolean
	 */

	public function importCSV($filename, $delimiter = ',', $fields_get, $line = null, $filter_name = null)
	{
		$line_info = $this->getCSVLineInfo($line);
		$from      = $line_info['from'];
		$to        = $line_info['to'];

		$full_path = $this->dir_location.'csv/import/'.$filename;

		if ($data = $this->csvToArray($full_path, $delimiter, $from, $to))
		{
			$count     = null;
			$header    = null;
			$rows      = null;

			extract($data);

			$fields = array();
			foreach ($fields_get as $field)
				$fields[$field['db_field']] = $field['csv_field'];

			if (!isset($fields['email']))
			{
				$this->context->smarty->assign(array(
						'status' => false,
						'msg' => $this->l('You have to set the email field!'),
					));
				return $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/admin/import_details.tpl'));
			}

			$valid_rows = array();

			if (!empty($rows))
			{
				foreach ($rows as $key => $row)
				{
					if (isset($fields['email'], $row[$fields['email']]) && Validate::isEmail($this->sanitizeUTF8($row[$fields['email']])))
					{
						$valid_rows[$key]['email']                      = $this->sanitizeUTF8($row[$fields['email']]);
						$valid_rows[$key]['date_add']                   = date('Y:m:d H:i:s');
						$valid_rows[$key]['firstname']                  = null;
						$valid_rows[$key]['lastname']                   = null;
						$valid_rows[$key]['id_lang']                    = (int)$this->context->language->id;
						$valid_rows[$key]['id_shop']                    = (int)$this->context->shop->id;
						$valid_rows[$key]['id_shop_group']              = (int)$this->context->shop->id_shop_group;
						$valid_rows[$key]['ip_registration_newsletter'] = null;
						$valid_rows[$key]['active']                     = 1;

						if (isset($fields['firstname'], $row[$fields['firstname']]))
							$valid_rows[$key]['firstname'] = $row[$fields['firstname']];

						if (isset($fields['lastname'], $row[$fields['lastname']]))
							$valid_rows[$key]['lastname'] = $row[$fields['lastname']];

						if (isset($fields['id_lang'], $row[$fields['id_lang']]) && $row[$fields['id_lang']] != 0)
							$valid_rows[$key]['id_lang'] = $row[$fields['id_lang']];

						if (isset($fields['id_shop'], $row[$fields['id_shop']]) && $row[$fields['id_shop']] != 0)
							$valid_rows[$key]['id_shop'] = $row[$fields['id_shop']];

						if (isset($fields['id_shop_group'], $row[$fields['id_shop_group']]) && $row[$fields['id_shop_group']] != 0)
							$valid_rows[$key]['id_shop_group'] = $row[$fields['id_shop_group']];

						if (isset($fields['ip_registration_newsletter'], $row[$fields['ip_registration_newsletter']]))
							$valid_rows[$key]['ip_registration_newsletter'] = $row[$fields['ip_registration_newsletter']];

						if (isset($fields['active'], $row[$fields['active']]))
							$valid_rows[$key]['active'] = $row[$fields['active']];
					}
				}
			}

			if (empty($valid_rows))
			{
				$this->context->smarty->assign(array(
						'status' => false,
						'msg' => $this->l('The column with emails was not found!'),
					));
				return $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/admin/import_details.tpl'));
			}

			$newsletter_pro_email = Db::getInstance()->executeS('SELECT `email`, `active` FROM `'._DB_PREFIX_.'newsletter_pro_email`');

			if (self::CSV_IMPORT_STRICT) {
				$newsletter = array();
				if (Db::getInstance()->getValue("
					SELECT COUNT(*) AS `count`
					FROM INFORMATION_SCHEMA.TABLES
					WHERE  TABLE_SCHEMA = '"._DB_NAME_."'
					AND TABLE_NAME = '"._DB_PREFIX_."newsletter'
				"))
					$newsletter = Db::getInstance()->executeS('SELECT `email`, `active` FROM `'._DB_PREFIX_.'newsletter`');
			}

			if (self::CSV_IMPORT_STRICT)
				$customer = Db::getInstance()->executeS('SELECT `email`, `newsletter` AS `active` FROM `'._DB_PREFIX_.'customer` WHERE `newsletter` = 1');
			else
				$customer = array();

			$emails_db = array_merge($newsletter, $customer);

			foreach ($emails_db as $key => $email)
				$emails_db[$key] = trim($email['email']);

			foreach ($newsletter_pro_email as $key => $email)
				$newsletter_pro_email[$key] = trim($email['email']);

			foreach ($valid_rows as $key => $row)
			{
				$row['email'] = trim($row['email']);

				if (in_array($row['email'], $emails_db))
					unset($valid_rows[$key]);

				elseif (in_array($row['email'], $newsletter_pro_email))
				{
					$update_sql = 'UPDATE `'._DB_PREFIX_.'newsletter_pro_email` SET ';
					$update_sql .= '`firstname` = '.( $row['firstname'] == null ? 'NULL' : '"'.pSQL($row['firstname']).'"' ).', ';
					$update_sql .= '`lastname` = '.( $row['lastname'] == null ? 'NULL' : '"'.pSQL($row['lastname']).'"' ).', ';
					$update_sql .= '`filter_name` = '.( !isset($filter_name) ? 'NULL' : '"'.pSQL(trim($filter_name)).'"' ).', ';
					$update_sql .= '`id_lang` = '.(int)$row['id_lang'].', ';
					$update_sql .= '`id_shop` = '.(int)$row['id_shop'].', ';
					$update_sql .= '`id_shop_group` = '.(int)$row['id_shop_group'].', ';
					$update_sql .= '`ip_registration_newsletter` = '.
									( $row['ip_registration_newsletter'] == null ? 'NULL' : '"'.(int)$row['ip_registration_newsletter'].'"' ).
									', ';
					$update_sql .= '`date_add` = "'.pSQL($row['date_add']).'", ';
					$update_sql .= '`active` = '.(int)$row['active'];
					$update_sql .= ' WHERE `email` = "'.pSQL($row['email']).'" ;';

					Db::getInstance()->execute($update_sql);
				}
				elseif (Validate::isEmail($row['email']))
				{
					$sql = 'INSERT INTO `'._DB_PREFIX_.'newsletter_pro_email` (`email`, `firstname`, `lastname`,`filter_name` ,`id_lang`, `id_shop`,
							`id_shop_group`, `ip_registration_newsletter`, `active`, `date_add`) VALUES ';
					$sql .= '(';
					$sql .= '"'.$row['email'].'", ';
					$sql .= ( $row['firstname'] == null ? 'NULL,' : '"'.pSQL($row['firstname']).'", ' );
					$sql .= ( $row['lastname'] == null ? 'NULL,' : '"'.pSQL($row['lastname']).'", ' );
					$sql .= ( !isset($filter_name) ? 'NULL,' : '"'.pSQL(trim($filter_name)).'", ' );
					$sql .= (int)$row['id_lang'].', ';
					$sql .= (int)$row['id_shop'].', ';
					$sql .= (int)$row['id_shop_group'].', ';
					$sql .= ( $row['ip_registration_newsletter'] == null ? 'NULL,' : '"'.(int)$row['ip_registration_newsletter'].'", ' );
					$sql .= (int)$row['active'].', ';
					$sql .= '"'.pSQL($row['date_add']).'" ';
					$sql .= ') ';
					$sql = Tools::substr($sql, 0, -1).';';

					Db::getInstance()->execute($sql);
				}

				// Fix rows
				foreach (array_keys($row) as $k)
					if (!array_key_exists($k, $fields))
						unset($valid_rows[$key][$k]);
			}


			if (!empty($valid_rows))
			{
				foreach ($header as $key => $head)
					if (!in_array($head, $fields))
						unset($header[$key]);


				// sort items for a proper display
				$sorted_valid_rows = array();

				foreach ($valid_rows as $key => $value)
				{
					$sorted_value = array();
					foreach (array_keys($fields) as $fk)
						if (array_key_exists($fk, $value))
							$sorted_value[$fk] = $value[$fk];

					$sorted_valid_rows[$key] = $sorted_value;
				}

				$this->context->smarty->assign(array(
					'count'  => $count - 1,
					'header' => $header,
					'rows'   => $sorted_valid_rows,
					'valid'  => count($valid_rows),
				));

				return $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/admin/import_details.tpl'));
			}
			else
			{
				$this->context->smarty->assign(array(
						'status' => false,
						'msg' => $this->l('No emails imported. Only those emails that are not duplicate can be imported!'),
					));
				return $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/admin/import_details.tpl'));
			}
		}
		return false;
	}

	/**
	 * Convert csv file to array
	 *
	 * @param  string $filename
	 * @param  string $delimiter
	 * @return array/boolean
	 */
	public function csvToArray($filename = '', $delimiter = ',', $from = 2, $to = 0, $has_header = true)
	{
		ini_set('auto_detect_line_endings', true);
		if (!file_exists($filename) || !is_readable($filename))
			return false;
		
		if ($has_header)
		{
			$from = $from - 1;
			$to = $to - 1;
		}

		$diff = $to - $from;
		$current_row_count = 0;

		$rows = file( $filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

		foreach ($rows as $key => $value)
		{ 
			// this solve the russian characters plobelm
			// $value = utf8_encode($value);

			$rows[$key] = mb_convert_encoding($rows[$key], 'UTF-8', mb_detect_encoding($rows[$key], 'UTF-8, ISO-8859-1, ISO-8859-15', true));
		}

		foreach ($rows as $key => $line)
			if (preg_match('/^(,+|\s+?|)$/', $line))
				unset($rows[$key]);

		if (!empty($rows))
		{
			$regex = '/'.$delimiter.'(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/';

			$header = null;
			if ($has_header)
			{
				$row_head = rtrim($rows[0], ';, ');
				$header = preg_split($regex, $row_head, -1, PREG_SPLIT_DELIM_CAPTURE);
				unset($rows[0]);
			}

			$content = array();
			foreach ($rows as $key => $row)
			{
				$current_row_count++;

				$row = rtrim($row, ';, ');

				$row = preg_split($regex, $row, -1, PREG_SPLIT_DELIM_CAPTURE);
				foreach ($row as $k => $v)
				{
					if (isset($header[$k]))
					{
						$header[$k] = trim($header[$k]);
						if (isset($v[0], $v[Tools::strlen($v) - 1]) && $v[0] == '"' && $v[Tools::strlen($v) - 1] == '"')
							$v = Tools::substr($v, 1, -1);
						$row[$header[$k]] = str_replace('""', '"', $v);
						unset($row[$k]);
					}
				}

				if ($diff > 0 && ($current_row_count >= $from && $current_row_count < $to)) // this will import the renge records
					$content[$key + 1] = $row;
				else if ($diff <= 0)  // this case will import all the records
					$content[$key + 1] = $row;
				else if ($current_row_count >= $to) // this care will break the loop if all the required records has been imported
					break; 
			}

			return array( 'count'=> count($content) + 1 , 'header'=> $header, 'rows' => $content);
		}
		else
			return false;
	}

	/**
	 * Save newsletter campaign parameters
	 *
	 * @param  array $params
	 * @return boolean
	 */
	public function saveParameteres($params)
	{
		if (Configuration::updateValue('NEWSLETTER_PRO_CAMPAIGN', $params, false, 0, 0 ))
			return true;
		return false;
	}

	/**
	 * Save campaign
	 *
	 * @param  array $data
	 * @return json
	 */
	public function saveCampaign($data)
	{
		$response = array( 'status' => false );
		if (count($data) > 1)
		{
			$campaign = array( 	'UTM_SOURCE'   => preg_replace('/[&?]/', '', $data['utm_source']),
								'UTM_MEDIUM'   => preg_replace('/[&?]/', '', $data['utm_medium']),
								'UTM_CAMPAIGN' => preg_replace('/[&?]/', '', $data['utm_campaign']),
								'UTM_CONTENT'  => preg_replace('/[&?]/', '', $data['utm_content']),
						);

			if ($this->updateConfiguration('CAMPAIGN', $campaign ) && $this->saveParameteres( $data['params'] ))
				$response['status'] = true;
		}
		else
			$response['status'] = null;

		return Tools::jsonEncode($response);
	}

	/**
	 * Setup the google analytics campaign default parameters
	 *
	 * @return json
	 */
	public function makeDefaultParameteres()
	{
		$response = array( 'status' => false, 'params' => '', 'campaign' => $this->default_campaign_params  );

		if ($this->updateConfiguration('CAMPAIGN', $this->default_campaign_params ) && $this->saveParameteres( '' ))
			$response['status'] = true;
		return Tools::jsonEncode($response);
	}

	/**
	 * Get task template
	 *
	 * @return string
	 */
	public function getTaskTemplate()
	{
		$tpl = pqnp_template_path($this->dir_location.'views/templates/admin/task/template.tpl');
		return $this->context->smarty->fetch($tpl);
	}

	/**
	 * Transform a date format to a jQuery date format
	 *
	 * @param  string $date_string
	 * @return string
	 */
	public function dateToJQuery($date_string)
	{
		$pattern = array('d', 'j', 'l', 'z', 'F', 'M', 'n', 'm', 'Y', 'y');

		$replace = array(
			'dd', 'd', 'DD', 'o',
			'MM', 'M', 'm', 'mm',
			'yy', 'y'
		);

		foreach ($pattern as &$p)
			$p = '/'.$p.'/';

		return preg_replace($pattern, $replace, $date_string);
	}

	/**
	 * Sort array elements by date
	 *
	 * @param  array $a
	 * @param  array $b
	 * @return array
	 */
	private static function sortByDate($a, $b)
	{
		return strtotime($a['date']) < strtotime($b['date']);
	}

	/**
	 * Get product templates
	 *
	 * @return json
	 */
	public function getProductTemplates()
	{
		$list = array();
		$path = $this->dir_location.'mail_templates/product/';

		$result = NewsletterProTools::getDirectoryIterator($path, '/^.+\.html$/i');

		$id = 1;
		$i = 0;
		foreach ($result as $file)
		{
			$list[$i]['id'] = $id;
			$list[$i]['name'] = Tools::ucfirst(str_replace('_', ' ', pathinfo($file->getFilename(), PATHINFO_FILENAME)));
			$list[$i]['filename'] = $file->getFilename();
			$list[$i]['path'] = $file->getPathName();
			$list[$i]['date'] = date($this->context->language->date_format_full, filemtime($file->getPathName()) );
			$list[$i]['selected'] = false;
			$list[$i]['info'] = array();

			if (is_readable($file->getPathName()))
			{
				$content = Tools::file_get_contents($file->getPathName());

				if (preg_match('/<!-- start header -->[\s\S]*?<!--([\s\S]*)?-->[\s\S]*?<!-- end header -->/', $content, $match))
				{
					$header = $match[1];

					if (preg_match('/info\s+?=\s+?(.*);/', $header, $m))
						$list[$i]['info'] = Tools::jsonDecode(trim($m[1]), true);
				}
			}

			if ($list[$i]['filename'] == $this->getConfiguration('PRODUCT_TEMPLATE'))
				$list[$i]['selected'] = true;

			$id++;
			$i++;
		}

		usort($list, array($this, 'sortByDate'));
		return Tools::jsonEncode($list);
	}

	/**
	 * Get images
	 *
	 * @return json
	 */
	public function getImages()
	{
		$list = array();
		$path = $this->dir_location.'images/';

		$result = NewsletterProTools::getDirectoryIterator($path, '/\.jpg|\.jpeg|\.png|\.gif$/i');

		$id = 1;
		$i = 0;
		foreach ($result as $file)
		{
			$list[$i]['id'] = $id;
			$list[$i]['name'] = Tools::ucfirst(str_replace('_', ' ', pathinfo($file->getFilename(), PATHINFO_FILENAME)));
			$list[$i]['filename'] = $file->getFilename();
			$thumb_filename = 'thumb_'.$file->getFilename();
			$list[$i]['thumb_filename'] = $thumb_filename;
			$list[$i]['path'] = $file->getPathName();
			$list[$i]['size'] = $file->getSize();

			$image_size = getimagesize($file->getPathName());
			$width = $image_size[0];
			$height = $image_size[1];

			$list[$i]['width'] = $width;
			$list[$i]['height'] = $height;

			$list[$i]['link'] = $this->url_location.'images/'.$file->getFilename();
			$list[$i]['thumb_link'] = $this->url_location.'images/thumb/'.$thumb_filename;

			$list[$i]['thumb_path'] = null;
			$thumb_path = $file->getPath().'/thumb/'.$thumb_filename;

			$list[$i]['thumb_path'] = null;
			if (file_exists($thumb_path))
				$list[$i]['thumb_path'] = $thumb_path;

			$list[$i]['date'] = date($this->context->language->date_format_full, filemtime($file->getPathName()) );

			$id++;
			$i++;
		}

		usort($list, array($this, 'sortByDate'));

		return Tools::jsonEncode($list);
	}

	/**
	 * Get added list
	 *
	 * @param  boolean $encode
	 * @return json/array
	 */
	public function getAdded($encode = true)
	{
		$sql = $this->getAddedSql();

		$userlist = Db::getInstance()->executeS($sql);

		foreach ($userlist as &$user)
			$user['img_path'] = $this->getLangImageById($user['id_lang']);

		if ($encode)
			return Tools::jsonEncode($userlist);
		else
			return $userlist;
	}

	/**
	 * Get added list sql
	 *
	 * @param  string $and
	 * @param  string $end
	 * @return string
	 */
	private function getAddedSql($and = '', $end = '')
	{
		$sql_shops_id = '';
		$get_active_shops_id = NewsletterProTools::getActiveShopsId();
		$subscribed = (int)$this->getConfiguration('VIEW_ACTIVE_ONLY');

		foreach ($get_active_shops_id as $id_shop)
			$sql_shops_id .= 'n.`id_shop` = '.(int)$id_shop.(end($get_active_shops_id) == $id_shop ? '' : ' OR ');

		$sql = 'SELECT n.`id_newsletter_pro_email`, n.`email`, n.`firstname`, n.`lastname`, n.`id_shop`, n.`id_lang`,
				n.`date_add` AS `newsletter_date_add`, n.`active`, s.`name` AS `shop_name`, l.`name` AS `language`
				FROM `'._DB_PREFIX_.'newsletter_pro_email` n
				LEFT JOIN `'._DB_PREFIX_.'lang` l ON (l.`id_lang` = n.`id_lang`)
				LEFT JOIN `'._DB_PREFIX_.'shop` s ON (s.`id_shop` = n.`id_shop`)
				WHERE ( '.$sql_shops_id.' ) ';
		$sql .= ( $subscribed ? ' AND n.`active` = '.(int)$subscribed : '');

		if (Tools::strlen(trim($and)) > 0)
			$sql .= ' AND '.$and.' ';

		$sql .= ' ORDER BY n.`id_newsletter_pro_email` ASC ';

		if (Tools::strlen(trim($end)) > 0)
			$sql .= ' '.$end.' ';

		$sql .= ';';
		return $sql;
	}

	/**
	 * Update added email
	 *
	 * @param  int $id
	 * @return int
	 */
	public function updateAdded($id)
	{
		parse_str(Tools::file_get_contents('php://input'), $put);
		$data = array(
			'active' => (int)$put['active'],
		);
		return (int)(Db::getInstance()->update('newsletter_pro_email', $data, '`id_newsletter_pro_email`= '.(int)$id));
	}

	/**
	 * Delete added
	 *
	 * @param  int $id
	 * @return int
	 */
	public function deleteAdded($id)
	{
		return (int)Db::getInstance()->delete('newsletter_pro_email', '`id_newsletter_pro_email`= '.(int)$id, 1);
	}

	/**
	 * Delete forwarder to email
	 *
	 * @param  string $email
	 * @return int
	 */
	public function deleteForwardToEmail($email)
	{
		return (int)Db::getInstance()->delete('newsletter_pro_forward', '`to`="'.pSQL($email).'"');
	}

	/**
	 * Delete forwarder from email
	 *
	 * @param  email $email
	 * @return int
	 */
	public function deleteForwardFromEmail($email)
	{
		return (int)Db::getInstance()->delete('newsletter_pro_forward', '`from`="'.pSQL($email).'"');
	}

	/**
	 * Clear forwarders
	 *
	 * @return int
	 */
	public function clearForwarders()
	{
		$sql = 'DELETE FROM `'._DB_PREFIX_.'newsletter_pro_forward` WHERE 1';
		return (int)Db::getInstance()->execute($sql);
	}

	/**
	 * Search in personal list with email addresses
	 *
	 * @param  string $value
	 * @return json
	 */
	public function searchAdded($value)
	{
		$search = ' ( n.`email` LIKE "%'.pSQL($value).'%" OR n.`firstname` LIKE "%'.pSQL($value).'%" OR n.`lastname` LIKE "%'.pSQL($value).'%" )';
		$sql = $this->getAddedSql($search);

		$userlist = Db::getInstance()->executeS($sql);
		foreach ($userlist as &$user)
			$user['img_path'] = $this->getLangImageById($user['id_lang']);

		return Tools::jsonEncode($userlist);
	}

	/**
	 * Filter added
	 *
	 * @param  array $filters
	 * @return json
	 */
	public function filterAdded($filters)
	{
		if (is_array($filters) && !empty($filters))
		{
			if (isset($filters['range_selection']['min'], $filters['range_selection']['max']))
			{
				if ($filters['range_selection']['min'] && $filters['range_selection']['max'])
					$range_selection = $filters['range_selection'];

				unset($filters['range_selection']);
			}
		}

		if (is_array($filters) && !empty($filters) || isset($range_selection))
		{
			$sql = '';

			$filters_count = count($filters);
			$filters_index = 1;
			foreach ($filters as $type => $ids)
			{
				if ($type == 'languages')
				{
					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;
					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
							$sql .= ' n.id_lang = '.(int)$id.' OR ';
						else
							$sql .= ' n.id_lang = '.(int)$id.' ';

						$ids_index++;
					}
					$sql .= ' ) ';
				}
				elseif ($type == 'shops')
				{
					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;
					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
							$sql .= ' n.id_shop = '.(int)$id.' OR ';
						else
							$sql .= ' n.id_shop = '.(int)$id.' ';

						$ids_index++;
					}
					$sql .= ' ) ';
				}
				elseif ($type == 'csv_name')
				{
					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;
					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
							$sql .= ' n.`filter_name` = "'.pSQL($id).'" OR ';
						else
							$sql .= ' n.`filter_name` = "'.pSQL($id).'" ';

						$ids_index++;
					}
					$sql .= ' ) ';
				}
				elseif ($type == 'subscribed')
				{
					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;
					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
							$sql .= ' n.active = '.(int)$id.' OR ';
						else
							$sql .= ' n.active = '.(int)$id.' ';

						$ids_index++;
					}
					$sql .= ' ) ';
				}

				if ($filters_index != $filters_count)
					$sql .= ' AND ';

				$filters_index++;
			}

			if ($this->getConfiguration('VIEW_ACTIVE_ONLY') == true && $filters_count > 0)
				$sql .= ' AND n.`active` = 1';
			else if ($this->getConfiguration('VIEW_ACTIVE_ONLY') == true)
				$sql .= ' n.`active` = 1';
			else
				$sql .= '';

			$end = '';
			if (isset($range_selection))
			{
				$lim_start = (int)$range_selection['min'];
				if ($lim_start < 0)
					$lim_start = 0;

				$lim_end = (int)$range_selection['max'] - (int)$range_selection['min'];
				if ($lim_end < 0)
					$lim_end = 0;

				$end = ' LIMIT '.(int)$lim_start.', '.(int)$lim_end.' ';
			}

			$sql = $this->getAddedSql($sql, $end);

			$userlist = Db::getInstance()->executeS($sql);

			foreach ($userlist as &$user)
				$user['img_path'] = $this->getLangImageById($user['id_lang']);

			return Tools::jsonEncode($userlist);
		}

		return $this->getAdded();
	}

	/**
	 * Create added
	 *
	 * @param  array $post
	 * @return json
	 */
	public function createAdded($post)
	{
		$errors = array();
		$response = array('status'=> false, 'errors' => &$errors);
		$data = array(
			'firstname' => pSQL($post['firstname']),
			'lastname'  => pSQL($post['lastname']),
			'email'     => pSQL($post['email']),
			'id_shop'   => (int)$post['id_shop'],
			'id_lang'   => (int)$post['id_lang'],
		);

		if (!Validate::isName($data['firstname']))
			$errors[] = $this->l('Invalid First Name!');

		if (!Validate::isName($data['lastname']))
			$errors[] = $this->l('Invalid Last Name!');

		if (!Validate::isEmail($data['email']))
			$errors[] = $this->l('Invalid email address!');

		$count_email = 'SELECT count(*) FROM `'._DB_PREFIX_.'newsletter_pro_email` WHERE `email` = "'.pSQL($data['email']).'"';
		if (Db::getInstance()->getValue($count_email))
			$errors[] = $this->l('Duplicate email address!');

		if (!Validate::isInt($data['id_shop']))
			$errors[] = $this->l('Invalid shop!');

		if (!Validate::isInt($data['id_lang']))
			$errors[] = $this->l('Invalid language!');

		if (empty($errors))
		{
			if (Db::getInstance()->insert('newsletter_pro_email', $data))
				$response['status'] = true;
			else
				$errors[] = $this->l('The email address cannot be added!');
		}

		return Tools::jsonEncode($response);
	}

	public function getCustomSubscriptionColumns()
	{
		$custom_columns = $this->getConfiguration('SHOW_CUSTOM_COLUMNS');
		$valid_columns = array();

		if (!empty($custom_columns))
		{
			foreach ($custom_columns as $name) 
			{
				if (NewsletterProTools::columnExists('newsletter_pro_subscribers', $name))
					$valid_columns[] = $name;
			}
		}

		return $valid_columns;
	}

	public function getVisitorsNpColumns()
	{
		$columns = NewsletterProTools::getDbColumns('newsletter_pro_subscribers');

		foreach ($columns as $key => $column)
		{
			if ($column == 'id_newsletter_pro_subscribers')	
				unset($columns[$key]);
		}

		return array_values($columns);
	}

	/**
	 * Get newsletter pro visitors subscribed sql
	 *
	 * @param  string $and
	 * @param  string $end
	 * @return string
	 */
	private function getVisitorsNPSql($and = '', $end = '')
	{
		$sql_shops_id = '';
		$get_active_shops_id = NewsletterProTools::getActiveShopsId();
		$subscribed = (int)$this->getConfiguration('VIEW_ACTIVE_ONLY');

		foreach ($get_active_shops_id as $id_shop)
			$sql_shops_id .= 'n.`id_shop` = '.(int)$id_shop.(end($get_active_shops_id) == $id_shop ? '' : ' OR ');

		$valid_columns = $this->getCustomSubscriptionColumns();

		$valid_columns_sql = '';
		foreach ($valid_columns as $name) 
			$valid_columns_sql .= 'n.`'.$name.'`,';

		$sql = 'SELECT n.`id_newsletter_pro_subscribers`,
						n.`email`,
						n.`birthday`,
						n.`firstname`,
						n.`lastname`,
						n.`id_shop`,
						n.`id_lang`,
						n.`id_gender`,
						n.`date_add` AS `newsletter_date_add`,
						n.`active`, s.`name` AS `shop_name`,
						'.pSQL($valid_columns_sql).'
						l.`name` AS `language`
				FROM `'._DB_PREFIX_.'newsletter_pro_subscribers` n
				LEFT JOIN `'._DB_PREFIX_.'lang` l ON (l.`id_lang` = n.`id_lang`)
				LEFT JOIN `'._DB_PREFIX_.'shop` s ON (s.`id_shop` = n.`id_shop`)
				WHERE ( '.$sql_shops_id.' ) ';

		$sql .= ( $subscribed ? ' AND n.`active` = '.(int)$subscribed : '');

		if (Tools::strlen(trim($and)) > 0)
			$sql .= ' AND '.$and.' ';

		$sql .= ' ORDER BY n.`id_newsletter_pro_subscribers` ASC ';

		if (Tools::strlen(trim($end)) > 0)
			$sql .= ' '.$end.' ';

		$sql .= ';';
		return $sql;
	}

	/**
	 * Get newsletter pro visitors subscribed
	 *
	 * @param  boolean $encode
	 * @return json/array
	 */
	public function getVisitorsNP($encode = true)
	{
		$sql = $this->getVisitorsNPSql();

		$userlist = Db::getInstance()->executeS($sql);

		foreach ($userlist as &$user)
			$user['img_path'] = $this->getLangImageById($user['id_lang']);

		if ($encode)
			return Tools::jsonEncode($userlist);
		else
			return $userlist;
	}

	/**
	 * Update newsletter pro visitors subscribed
	 *
	 * @param  int $id
	 * @return int
	 */
	public function updateVisitorNP($id)
	{
		parse_str(Tools::file_get_contents('php://input'), $put);
		$data = array(
			'active' => (int)$put['active'],
		);
		return (int)(Db::getInstance()->update('newsletter_pro_subscribers', $data, '`id_newsletter_pro_subscribers`= '.(int)$id));
	}

	/**
	 * Delete newsletter pro visitors subscribed
	 *
	 * @param  int $id
	 * @return int
	 */
	public function deleteVisitorNP($id)
	{
		return (int)Db::getInstance()->delete('newsletter_pro_subscribers', '`id_newsletter_pro_subscribers`= '.(int)$id, 1);
	}

	/**
	 * Search newsletter pro visitors subscribed
	 *
	 * @param  string $value
	 * @return json
	 */
	public function searchVisitorNP($value, $conditions)
	{
		if (is_array($conditions) && !empty($conditions))
		{
			$selected_condition = (int)$conditions['selected_condition'];
			$selected_field = $conditions['selected_field'];

			$db_fields = $this->getVisitorsNpColumns();
			$search_in_fields = $db_fields;

			if ($selected_field != '0' && in_array($selected_field, $db_fields))
				$search_in_fields = array($selected_field);

			$search_query = '';

			// if the filter is setup to all fields, filter only the below columns

			if ($selected_field == '0')
			{
				$new_fields = array();

				if (in_array('firstname', $search_in_fields))
					$new_fields[] = 'firstname';

				if (in_array('lastname', $search_in_fields))
					$new_fields[] = 'lastname';

				if (in_array('email', $search_in_fields))
					$new_fields[] = 'email';

				$search_in_fields = $new_fields;
			}

			foreach ($search_in_fields as $field) 
			{
				if (!in_array($field, $db_fields))
					continue;

				$is_int = false;

				if (in_array($field, array('id_shop', 'id_shop_group', 'id_lang', 'id_gender', 'active')))
					$is_int = true;
				else
					$is_int = false;

				switch ($selected_condition) 
				{
					case self::SEARCH_CONDITION_CONTAINS:
							$search_query .= ' n.`'.$field.'` LIKE '.($is_int ? '"%'.(int)$value.'%"' : '"%'.pSQL($value).'%"').' OR';
						break;

					case self::SEARCH_CONDITION_IS:
							$search_query .= ' n.`'.$field.'` = '.($is_int ? (int)$value : '"'.pSQL($value).'"').' OR';
						break;

					case self::SEARCH_CONDITION_IS_NOT:
							$search_query .= ' n.`'.$field.'` != '.($is_int ? (int)$value : '"'.pSQL($value).'"').' OR';
						break;

					case self::SEARCH_CONDITION_GREATER:
						$search_query .= ' n.`'.$field.'` >= '.($is_int ? (int)$value : '"'.pSQL($value).'"').' OR';
						break;

					case self::SEARCH_CONDITION_LESS:
						$search_query .= ' n.`'.$field.'` <= '.($is_int ? (int)$value : '"'.pSQL($value).'"').' OR';
						break;
				}
			}

			$search = '(
				'.rtrim($search_query, 'OR').'
			)';
		}
		else
			$search = ' ( n.`email` LIKE "%'.pSQL($value).'%" OR n.`firstname` LIKE "%'.pSQL($value).'%" OR n.`lastname` LIKE "%'.pSQL($value).'%" )';
		
		$sql = $this->getVisitorsNPSql($search);

		$userlist = Db::getInstance()->executeS($sql);
		foreach ($userlist as &$user)
			$user['img_path'] = $this->getLangImageById($user['id_lang']);

		return Tools::jsonEncode($userlist);
	}

	/**
	 * Filter newsletter pro visitors subscribed
	 *
	 * @param  array $filters
	 * @return json
	 */
	public function filterVisitorNP($filters)
	{
		if (is_array($filters) && !empty($filters))
		{
			if (isset($filters['by_birthday']['from'], $filters['by_birthday']['to']))
			{
				if (trim($filters['by_birthday']['from']) == '' || trim($filters['by_birthday']['to']) == '')
					unset($filters['by_birthday']);
			}

			if (isset($filters['range_selection']['min'], $filters['range_selection']['max']))
			{
				if ($filters['range_selection']['min'] && $filters['range_selection']['max'])
					$range_selection = $filters['range_selection'];

				unset($filters['range_selection']);
			}
		}

		if (is_array($filters) && !empty($filters) || isset($range_selection))
		{
			$sql = '';

			$filters_count = count($filters);
			$filters_index = 1;
			foreach ($filters as $type => $ids)
			{
				if ($type == 'languages')
				{
					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;
					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
							$sql .= ' n.id_lang = '.(int)$id.' OR ';
						else
							$sql .= ' n.id_lang = '.(int)$id.' ';

						$ids_index++;
					}
					$sql .= ' ) ';
				}
				elseif ($type == 'shops')
				{
					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;
					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
							$sql .= ' n.id_shop = '.(int)$id.' OR ';
						else
							$sql .= ' n.id_shop = '.(int)$id.' ';

						$ids_index++;
					}
					$sql .= ' ) ';
				}
				elseif ($type == 'gender')
				{
					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;
					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
							$sql .= ' n.id_gender = '.(int)$id.' OR ';
						else
							$sql .= ' n.id_gender = '.(int)$id.' ';

						$ids_index++;
					}
					$sql .= ' ) ';
				}
				elseif ($type == 'subscribed')
				{
					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;
					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
							$sql .= ' n.active = '.(int)$id.' OR ';
						else
							$sql .= ' n.active = '.(int)$id.' ';

						$ids_index++;
					}
					$sql .= ' ) ';
				}
				elseif ($type == 'by_interest')
				{

					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;

					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
						{
							if ($id == -1)
								$sql .= " n.`list_of_interest` IS NULL OR n.`list_of_interest` = '' OR ";
							else
							{
								$sql .= ' FIND_IN_SET ('.(int)$id.', n.`list_of_interest`) OR ';
								// $sql .= '  n.`list_of_interest` = '.(int)$id.' OR '; not ok
							}
						}
						else
						{
							if ($id == -1)
								$sql .= " n.`list_of_interest` IS NULL OR n.`list_of_interest` = '' ";
							else
							{
								$sql .= ' FIND_IN_SET ('.(int)$id.', n.`list_of_interest`) ';
								// $sql .= ' n.`list_of_interest` = '.(int)$id.' '; not ok
							}
						}

						$ids_index++;
					}
					$sql .= ' ) ';
				}
				elseif ($type == 'by_birthday')
				{
					$only_month_day = true;

					if ($only_month_day)
					{
						$form = date('m-d', strtotime(str_replace('.', '-', $filters['by_birthday']['from'])));
						$to   = date('m-d', strtotime(str_replace('.', '-', $filters['by_birthday']['to'])));

						$sql .= ' DATE_FORMAT(n.birthday, "%m-%d") >= "'.pSQL($form).'" AND DATE_FORMAT(n.birthday, "%m-%d") <= "'.pSQL($to).'" ';
					}
					else
					{
						$form = date('Y-m-d', strtotime(str_replace('.', '-', $filters['by_birthday']['from'])));
						$to   = date('Y-m-d', strtotime(str_replace('.', '-', $filters['by_birthday']['to'])));

						$sql .= ' n.birthday >= "'.pSQL($form).'" AND n.birthday <= "'.pSQL($to).'" ';
					}
				}

				if ($filters_index != $filters_count)
					$sql .= ' AND ';

				$filters_index++;
			}

			if ($this->getConfiguration('VIEW_ACTIVE_ONLY') == true && $filters_count > 0)
				$sql .= ' AND n.`active` = 1';
			else if ($this->getConfiguration('VIEW_ACTIVE_ONLY') == true)
				$sql .= ' n.`active` = 1';
			else
				$sql .= '';

			$end = '';
			if (isset($range_selection))
			{
				$lim_start = (int)$range_selection['min'];
				if ($lim_start < 0)
					$lim_start = 0;

				$lim_end = (int)$range_selection['max'] - (int)$range_selection['min'];
				if ($lim_end < 0)
					$lim_end = 0;

				$end = ' LIMIT '.(int)$lim_start.', '.(int)$lim_end.' ';
			}

			$sql = $this->getVisitorsNPSql($sql, $end);

			$userlist = Db::getInstance()->executeS($sql);

			foreach ($userlist as &$user)
				$user['img_path'] = $this->getLangImageById($user['id_lang']);

			return Tools::jsonEncode($userlist);
		}
		return $this->getVisitorsNP();
	}

	/**
	 * Get visitors
	 *
	 * @param  boolean $encode
	 * @return json/array
	 */
	public function getVisitors($encode = true)
	{
		$sql = $this->getVisitorsSql();
		if ($sql)
		{
			$userlist = Db::getInstance()->executeS($sql);

			$default_lang = (int)$this->getConfiguration('PS_LANG_DEFAULT');

			foreach ($userlist as &$user)
				$user['img_path'] = $this->getLangImageById($default_lang);

			if ($encode)
				return Tools::jsonEncode($userlist);
			else
				return $userlist;
		}
		else
		{
			if ($encode)
				return Tools::jsonEncode(array());
			else
				return array();
		}
	}

	/**
	 * Get visitors sql
	 *
	 * @param  string $and
	 * @param  string $end
	 * @return string/boolean
	 */
	private function getVisitorsSql($and = '', $end = '')
	{
		$sql_check = "SELECT COUNT(*) AS `count`
				FROM INFORMATION_SCHEMA.TABLES
				WHERE  TABLE_SCHEMA = '"._DB_NAME_."'
				AND TABLE_NAME = '"._DB_PREFIX_."newsletter'";

		if (Db::getInstance()->getValue($sql_check))
		{
			$sql_shops_id = '';
			$get_active_shops_id = NewsletterProTools::getActiveShopsId();
			$subscribed = (int)$this->getConfiguration('VIEW_ACTIVE_ONLY');

			foreach ($get_active_shops_id as $id_shop)
				$sql_shops_id .= 'n.`id_shop` = '.(int)$id_shop.(end($get_active_shops_id) == $id_shop ? '' : ' OR ');

			$sql = 'SELECT n.`id`, n.`id_shop`, n.`ip_registration_newsletter`, n.`email`, n.`active`, sh.`name` AS `shop_name`
					FROM `'._DB_PREFIX_.'newsletter` n
					INNER JOIN `'._DB_PREFIX_.'shop` sh ON (n.`id_shop` = sh.`id_shop`)
					WHERE ('.$sql_shops_id.') ';
			$sql .= ($subscribed ? ' AND n.`active` = '.(int)$subscribed : '');

			if (Tools::strlen(trim($and)) > 0)
				$sql .= ' AND '.$and.' ';

			$sql .= ' ORDER BY n.`id` ASC ';

			if (Tools::strlen(trim($end)) > 0)
				$sql .= ' '.$end.' ';

			$sql .= ';';

			return $sql;
		}
		else
			return false;
	}

	/**
	 * Update visitor subscribed
	 *
	 * @param  int $id
	 * @return int
	 */
	public function updateVisitor($id)
	{
		parse_str(Tools::file_get_contents('php://input'), $put);
		$data = array(
			'active' => (int)$put['active'],
		);
		return (int)(Db::getInstance()->update('newsletter', $data, "`id`={$id}"));
	}

	/**
	 * Delete visitor subscribed
	 *
	 * @param  int $id
	 * @return int
	 */
	public function deleteVisitor($id)
	{
		return (int)Db::getInstance()->delete('newsletter', '`id`='.(int)$id, 1);
	}

	/**
	 * Search visitors
	 *
	 * @param  string $value
	 * @return json
	 */
	public function searchVisitor($value)
	{
		$search = ' ( n.`email` LIKE "%'.pSQL($value).'%" )';
		$sql = $this->getVisitorsSql($search);

		$default_lang = (int)$this->getConfiguration('PS_LANG_DEFAULT');
		$userlist = Db::getInstance()->executeS($sql);
		foreach ($userlist as &$user)
			$user['img_path'] = $this->getLangImageById($default_lang);

		return Tools::jsonEncode($userlist);
	}

	/**
	 * Filter visitor subscribed
	 *
	 * @param  array $filters
	 * @return json
	 */
	public function filterVisitor($filters)
	{
		if (is_array($filters) && !empty($filters))
		{
			if (isset($filters['range_selection']['min'], $filters['range_selection']['max']))
			{
				if ($filters['range_selection']['min'] && $filters['range_selection']['max'])
					$range_selection = $filters['range_selection'];

				unset($filters['range_selection']);
			}
		}

		if (is_array($filters) && !empty($filters) || isset($range_selection))
		{
			$sql = '';

			$filters_count = count($filters);
			$filters_index = 1;
			foreach ($filters as $type => $ids)
			{
				if ($type == 'shops')
				{
					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;
					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
							$sql .= ' n.id_shop = '.(int)$id.' OR ';
						else
							$sql .= ' n.id_shop = '.(int)$id.' ';

						$ids_index++;
					}
					$sql .= ' ) ';
				}
				else if ($type == 'subscribed')
				{
					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;
					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
							$sql .= ' n.active = '.(int)$id.' OR ';
						else
							$sql .= ' n.active = '.(int)$id.' ';

						$ids_index++;
					}
					$sql .= ' ) ';
				}

				if ($filters_index != $filters_count)
					$sql .= ' AND ';

				$filters_index++;
			}

			if ($this->getConfiguration('VIEW_ACTIVE_ONLY') == true && $filters_count > 0)
				$sql .= ' AND n.`active` = 1';
			else if ($this->getConfiguration('VIEW_ACTIVE_ONLY') == true)
				$sql .= ' n.`active` = 1';
			else
				$sql .= '';

			$end = '';

			if (isset($range_selection))
			{
				$lim_start = (int)$range_selection['min'];
				if ($lim_start < 0)
					$lim_start = 0;

				$lim_end = (int)$range_selection['max'] - (int)$range_selection['min'];
				if ($lim_end < 0)
					$lim_end = 0;

				$end = ' LIMIT '.(int)$lim_start.', '.(int)$lim_end.' ';
			}

			$sql = $this->getVisitorsSql($sql, $end);

			$default_lang = (int)$this->getConfiguration('PS_LANG_DEFAULT');

			$userlist = Db::getInstance()->executeS($sql);

			foreach ($userlist as &$user)
				$user['img_path'] = $this->getLangImageById($default_lang);

			return Tools::jsonEncode($userlist);
		}

		return $this->getVisitors();
	}

	/**
	 * Get customers
	 *
	 * @param  boolean $encode
	 * @return json/array
	 */
	public function getCustomers($encode = true)
	{
		$sql = $this->getCustomersSql(array(
			'subscribed' => (int)$this->getConfiguration('VIEW_ACTIVE_ONLY'),
		));

		$userlist = Db::getInstance()->executeS($sql);

		foreach ($userlist as &$user)
			$user['img_path'] = $this->getLangImageById($user['id_lang']);

		if ($encode)
			return Tools::jsonEncode($userlist);

		return $userlist;
	}

	/**
	 * Get customers sql statement
	 *
	 * @param  array  $cfg
	 * @return string
	 */
	public function getCustomersSql($cfg = array())
	{
		$select = null;
		$from  = null;
		$join = null;
		$where = null;
		$and = null;
		$end = null;
		$subscribed = null;

		extract($cfg);

		$sql_shops_id = '';
		$get_active_shops_id = NewsletterProTools::getActiveShopsId();
		foreach ($get_active_shops_id as $id_shop)
			$sql_shops_id .= 'c.`id_shop` = '.(int)$id_shop.(end($get_active_shops_id) == $id_shop ? '' : ' OR ');

		$sql = 'SELECT 	c.`firstname`, c.`lastname`, c.`email`, c.`id_customer` AS `id`,
						c.`id_lang`, c.`id_shop`, c.`id_default_group`, c.`newsletter`, c.`id_gender`,
						sh.`name` AS `shop_name`, c.company,
						npcc.`categories`,
						loi.`categories` AS categories_loi,
						GROUP_CONCAT(cg.`id_group`) AS `id_group` ';
		$sql .= (isset($select) ? ', '.$select.' ': '');
		$sql .= ' FROM `'._DB_PREFIX_.'customer` c ';
		$sql .= (isset($from) ? ' '.$from.' ': '');
		$sql .= ' LEFT JOIN `'._DB_PREFIX_.'customer_group` cg ON ( cg.`id_customer` = c.`id_customer` )
				LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_customer_category` npcc ON (c.`id_customer` = npcc.`id_customer`)
				LEFT JOIN `'._DB_PREFIX_.'lang` lg ON (c.`id_lang` = lg.`id_lang`)
				LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_customer_list_of_interests` loi ON (c.id_customer = loi.id_customer)
				INNER JOIN `'._DB_PREFIX_.'shop` sh ON (c.`id_shop` = sh.`id_shop`) AND ('.$sql_shops_id.') ';
		$sql .= (isset($join) ? ' '.$join.' ': '');

		if (isset($where))
			$sql .= (isset($where) ? ' WHERE '.$where.' ': ' WHERE 1 ');
		else if (isset($subscribed))
			$sql .= ((int)$this->getConfiguration('VIEW_ACTIVE_ONLY') ? ' WHERE c.`newsletter` = '.(int)$subscribed : ' WHERE 1 ');
		else
			$sql .= ' WHERE 1 ';

		$sql .= (isset($and) && trim($and) ? ' AND '.$and.' ': '');
		$sql .= ' GROUP BY c.id_customer
				ORDER BY c.`id_customer` ASC ';
		$sql .= (isset($end) && trim($end) ? ' '.$end.' ': '');
		$sql .= ';';

		return $sql;
	}

	/**
	 * Search customer
	 *
	 * @param  string $value
	 * @return json
	 */
	public function searchCustomer($value)
	{
		$search = ' ( c.`email` LIKE "%'.pSQL($value).'%" OR c.`firstname` LIKE "%'.pSQL($value).'%" OR c.`lastname` LIKE "%'.pSQL($value).'%" )';
		$sql = $this->getCustomersSql(array(
			'and'        => $search,
			'subscribed' => (int)$this->getConfiguration('VIEW_ACTIVE_ONLY'),
		));

		$userlist = Db::getInstance()->executeS($sql);

		foreach ($userlist as &$user)
			$user['img_path'] = $this->getLangImageById($user['id_lang']);

		return Tools::jsonEncode($userlist);
	}

	/**
	 * Filter customer
	 *
	 * @param  array $filters
	 * @return json
	 */
	public function filterCustomer($filters)
	{
		if (is_array($filters) && !empty($filters))
		{
			if (isset($filters['by_birthday']['from'], $filters['by_birthday']['to']))
			{
				if (trim($filters['by_birthday']['from']) == '' || trim($filters['by_birthday']['to']) == '')
					unset($filters['by_birthday']);
			}

			if (isset($filters['range_selection']['min'], $filters['range_selection']['max']))
			{
				if ($filters['range_selection']['min'] && $filters['range_selection']['max'])
					$range_selection = $filters['range_selection'];

				unset($filters['range_selection']);
			}
		}

		if ((is_array($filters) && !empty($filters)) || isset($range_selection))
		{
			$sql = '';
			$sql_filter = array();

			$filters_count = count($filters);
			$filters_index = 1;
			foreach ($filters as $type => $ids)
			{
				if ($type == 'groups')
				{
					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;
					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
							$sql .= ' cg.`id_group` = '.(int)$id.' OR '; // $sql .= ' FIND_IN_SET ('.(int)$id.', cg.`id_group`) OR ';
						else
							$sql .= ' cg.`id_group` = '.(int)$id.' '; // $sql .= ' FIND_IN_SET ('.(int)$id.', cg.`id_group`) ';

						$ids_index++;
					}
					$sql .= ' ) ';
				}
				elseif ($type == 'languages')
				{
					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;
					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
							$sql .= ' c.id_lang = '.(int)$id.' OR ';
						else
							$sql .= ' c.id_lang = '.(int)$id.' ';

						$ids_index++;
					}
					$sql .= ' ) ';
				}
				elseif ($type == 'shops')
				{
					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;
					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
							$sql .= ' c.id_shop = '.(int)$id.' OR ';
						else
							$sql .= ' c.id_shop = '.(int)$id.' ';

						$ids_index++;
					}
					$sql .= ' ) ';
				}
				elseif ($type == 'gender')
				{
					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;
					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
							$sql .= ' c.id_gender = '.(int)$id.' OR ';
						else
							$sql .= ' c.id_gender = '.(int)$id.' ';

						$ids_index++;
					}
					$sql .= ' ) ';
				}
				elseif ($type == 'subscribed')
				{
					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;
					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
							$sql .= ' c.newsletter = '.(int)$id.' OR ';
						else
							$sql .= ' c.newsletter = '.(int)$id.' ';

						$ids_index++;
					}
					$sql .= ' ) ';
				}
				elseif($type == 'active'){
				    $sql .= ' ( ';
				    $ids_count = count($ids);
				    $ids_index = 1;
				    foreach ($ids as $id)
				    {
				        if ($ids_index != $ids_count){
				            $sql .= ' c.active = '.(int)$id.' OR ';
				        }else{
				            $sql .= ' c.active = '.(int)$id.' ';
				        }
				        $ids_index++;
				    }
				    $sql .= ' ) ';
				    
				}
				elseif($type == 'postcode'){
				    $sql .= ' ( ';
				    $ids_count = count($ids);
				    $ids_index = 1;
				    foreach ($ids as $id)
				    {
				        if ($ids_index != $ids_count){
				            $sql .= ' c.postcode LIKE "'.$id.'%" OR ';
				        }else{
				            $sql .= ' c.postcode LIKE "'.$id.'%" ';
				        }
				        $ids_index++;
				    }
				    $sql .= ' ) ';
				
				}
				
				elseif ($type == 'by_interest')
				{
					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;

					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
						{
							if ($id == -1)
								$sql .= " loi.`categories` IS NULL OR loi.`categories` = '' OR ";
							else
							{
								$sql .= ' FIND_IN_SET ('.(int)$id.', loi.`categories`) OR ';
								// $sql .= '  n.`list_of_interest` = '.(int)$id.' OR '; not ok
							}
						}
						else
						{
							if ($id == -1)
								$sql .= " loi.`categories` IS NULL OR loi.`categories` = '' ";
							else
							{
								$sql .= ' FIND_IN_SET ('.(int)$id.', loi.`categories`) ';
								// $sql .= ' n.`list_of_interest` = '.(int)$id.' '; not ok
							}
						}

						$ids_index++;
					}
					$sql .= ' ) ';
				}
				elseif ($type == 'categories')
				{
					$sql .= ' ( ';
					$ids_count = count($ids);
					$ids_index = 1;

					foreach ($ids as $id)
					{
						if ($ids_index != $ids_count)
						{
							if ($id == -1)
								$sql .= " npcc.`categories` IS NULL OR npcc.`categories` = '' OR ";
							else
								$sql .= ' npcc.`categories` = '.(int)$id.' OR '; // $sql .= ' FIND_IN_SET ('.(int)$id.', npcc.`categories`) OR ';
						}
						else
						{
							if ($id == -1)
								$sql .= " npcc.`categories` IS NULL OR npcc.`categories` = '' ";
							else
								$sql .= ' npcc.`categories` = '.(int)$id.' '; // $sql .= ' FIND_IN_SET ('.(int)$id.', npcc.`categories`) ';
						}

						$ids_index++;
					}
					$sql .= ' ) ';
				}
				elseif ($type == 'purchased_product')
				{
					$sql .= ' c.`id_customer` IN ( ';

					$ids_str = trim(implode(',', $ids), ',');

					$sql .= 'SELECT o.`id_customer`
								FROM `'._DB_PREFIX_.'orders` o
								INNER JOIN `'._DB_PREFIX_.'cart` car ON (o.`id_cart` = car.`id_cart`)
								INNER JOIN `'._DB_PREFIX_.'cart_product` carp ON (
									car.`id_cart` = carp.`id_cart`
									AND carp.`id_product` IN ('.pSQL($ids_str).')
								)
								WHERE 1 ';
					$sql .= ' ) ';

				}
				elseif ($type == 'by_birthday')
				{
					$only_month_day = true;

					if ($only_month_day)
					{
						$form = date('m-d', strtotime(str_replace('.', '-', $filters['by_birthday']['from'])));
						$to   = date('m-d', strtotime(str_replace('.', '-', $filters['by_birthday']['to'])));

						$sql .= ' DATE_FORMAT(c.birthday, "%m-%d") >= "'.pSQL($form).'" AND DATE_FORMAT(c.birthday, "%m-%d") <= "'.pSQL($to).'" ';
					}
					else
					{
						$form = date('Y-m-d', strtotime(str_replace('.', '-', $filters['by_birthday']['from'])));
						$to   = date('Y-m-d', strtotime(str_replace('.', '-', $filters['by_birthday']['to'])));

						$sql .= ' c.birthday >= "'.pSQL($form).'" AND c.birthday <= "'.pSQL($to).'" ';
					}
				}
				elseif ($type == 'total_spent')
				{
					$sql .= '(
						(SELECT SUM(o.`total_paid_real` / o.`conversion_rate`)
							FROM `'._DB_PREFIX_.'orders` o WHERE o.`id_customer` = c.`id_customer` '.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').') >= '.(int)$filters['total_spent']['from'].'
						AND
						(SELECT SUM(o.`total_paid_real` / o.`conversion_rate`) 
							FROM `'._DB_PREFIX_.'orders` o WHERE o.`id_customer` = c.`id_customer` '.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').') <= '.(int)$filters['total_spent']['to'].'
						)	
					';
				}
				elseif ($type == 'filter_by_country')
				{
					$ids_count = count($ids);
					$ids_index = 1;
					$sql_filter['from'] = 'LEFT JOIN `'._DB_PREFIX_.'country` country ON country.id_country = c.id_country';

					$sql .= ' ( ';
					foreach ($ids as $iso_code)
					{
						if ($ids_index != $ids_count)
							$sql .= ' country.iso_code LIKE "'.pSQL(Tools::strtolower($iso_code)).'" OR ';
						else
							$sql .= ' country.iso_code LIKE "'.pSQL(Tools::strtolower($iso_code)).'" ';
					}
					$sql .= ' ) ';
				}

				if ($filters_index != $filters_count)
					$sql .= ' AND ';

				$filters_index++;
			}

			if ($this->getConfiguration('VIEW_ACTIVE_ONLY') == true && $filters_count > 0)
				$sql .= ' AND c.`newsletter` = 1';
			else if ($this->getConfiguration('VIEW_ACTIVE_ONLY') == true)
				$sql .= ' c.`newsletter` = 1';
			else
				$sql .= '';

			$sql_filter['and'] = $sql;
			$sql_filter['subscribed'] = (int)$this->getConfiguration('VIEW_ACTIVE_ONLY');

			if (isset($range_selection))
			{
				$lim_start = (int)$range_selection['min'];
				if ($lim_start < 0)
					$lim_start = 0;

				$lim_end = (int)$range_selection['max'] - (int)$range_selection['min'];
				if ($lim_end < 0)
					$lim_end = 0;

				$sql_filter['end'] = ' LIMIT '.(int)$lim_start.', '.(int)$lim_end.' ';
			}

			$sql = $this->getCustomersSql($sql_filter);

			$userlist = Db::getInstance()->executeS($sql);

			foreach ($userlist as &$user)
				$user['img_path'] = $this->getLangImageById($user['id_lang']);

			return Tools::jsonEncode($userlist);
		}

		return $this->getCustomers();
	}

	/**
	 * Get language image by id
	 *
	 * @param  int $id
	 * @return string
	 */
	public function getLangImageById($id)
	{
		$path = '';
		if (file_exists($this->lang_img_dir.$id.'.jpg'))
			$path = $this->lang_img_path.$id.'.jpg';
		else
			$path = $this->lang_img_path.'none.jpg';

		$path = $this->relplaceAdminLink($path);
		return $path;
	}

	/**
	 * Update customer by id
	 *
	 * @param  int $id
	 * @return int
	 */
	public function updateCustomer($id)
	{
		parse_str(Tools::file_get_contents('php://input'), $put);

		$data = array(
			'newsletter' => (int)$put['newsletter'],
		);
		return (int)(Db::getInstance()->update('customer', $data, '`id_customer`= '.(int)$id));
	}

	/**
	 * Delete customer by id
	 *
	 * @param  int $id
	 * @return int
	 */
	public function deleteCustomer($id)
	{
		$customer = new Customer($id);
		if ($customer->id != null)
			return (int)$customer->delete();
		return 0;
	}

	/**
	 * Get tasks
	 *
	 * @return json
	 */
	public function getTasks()
	{
		$sql = 'SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_task` t
				WHERE `done` = 0
				ORDER BY t.`date_start` ASC';
		if ($tasks = Db::getInstance()->executeS($sql))
		{
			$current_day_time = strtotime(date('Y-m-d'));

			foreach ($tasks as &$task)
			{
				$status = &$task['status'];
				$date_start = strtotime($task['date_start']);

				$task['error_msg'] = NewsletterProTools::unSerialize($task['error_msg']);

				if ((int)$status === 1)
					$status = 1;
				else if ($current_day_time === $date_start)
					$status = $this->l('today');
				else if (($current_day_time + 24 * 60 * 60) === $date_start)
					$status = $this->l('tomorrow');
				else if ($current_day_time > $date_start)
					$status = $this->l('in the past');
				else
					$status = '';
			}

			return Tools::jsonEncode($tasks);
		}
		else
			return Tools::jsonEncode(array());
	}

	/**
	 * Render the template history
	 *
	 * @param  int $id_history
	 * @return json
	 */
	public function renderTemplateHistory($id_history)
	{
		$url = $this->getTemplateHistoryUrl($id_history);
		// viewInBrowser will remove the opened email tracking if the browser is viewed by admin
		$url .= '&viewInBrowser=1';
		return Tools::jsonEncode(array(
			'url' => $url,
			// jQueryNoConflict is important
			'content' => Tools::file_get_contents($url.'&jQueryNoConflict')
		));
	}

	/**
	 * Get template history url
	 *
	 * @param  int $id_history
	 * @return string
	 */
	public function getTemplateHistoryUrl($id_history)
	{
		return $this->url_location.'newsletter.php?token_tpl='.$this->getTokenByIdHistory((int)$id_history).'&email='.$this->getConfiguration('PS_SHOP_EMAIL');
	}

	/**
	 * Get token by id hisotry
	 *
	 * @param  int $id_history
	 * @return int
	 */
	public function getTokenByIdHistory($id_history)
	{
		return Db::getInstance()->getValue('SELECT `token` FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history` WHERE `id_newsletter_pro_tpl_history` = '.(int)$id_history);
	}

	/**
	 * Get hisotry id by token
	 *
	 * @param  string $token
	 * @return int
	 */
	public function getHistoryIdByToken($token)
	{
		return (int)Db::getInstance()->getValue('SELECT `id_newsletter_pro_tpl_history` FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history` WHERE `token` = "'.pSQL($token).'"');
	}

	/**
	 * Get task history
	 *
	 * @return json
	 */
	public function getTasksHistory()
	{
		$sql = 'SELECT t.`id_newsletter_pro_task`,
				t.`id_newsletter_pro_smtp`,
				t.`id_newsletter_pro_tpl_history`,
				t.`date_start`,
				t.`active`,
				t.`template`,
				t.`status`,
				t.`sleep`,
				t.`emails_count`,
				t.`emails_error`,
				t.`emails_success`,
				t.`emails_completed`,
				t.`done`,
				t.`error_msg`,
				h.`clicks`,
				h.`opened`,
				h.`unsubscribed`,
				h.`fwd_unsubscribed`,
				GROUP_CONCAT(ts.`id_newsletter_pro_task_step`) AS `steps`
				FROM `'._DB_PREFIX_.'newsletter_pro_task` t
				LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_task_step` ts
					ON (ts.`id_newsletter_pro_task` = t.`id_newsletter_pro_task`)

				LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_tpl_history` h
					ON (t.`id_newsletter_pro_tpl_history` = h.`id_newsletter_pro_tpl_history`)

				WHERE t.`done` = 1
				GROUP BY t.`id_newsletter_pro_task`
				ORDER BY t.`date_start` DESC;';

		if ($tasks = Db::getInstance()->executeS($sql))
		{
			foreach ($tasks as &$task)
			{
				$task['date_start'] = date('Y-m-d', strtotime($task['date_start']));
				$task['error_msg']  = unserialize($task['error_msg']);
				$task['template']   = Tools::ucfirst(pathinfo($task['template'], PATHINFO_FILENAME));
			}

			return Tools::jsonEncode($tasks);
		}
		else
			return Tools::jsonEncode(array());
	}

	/**
	 * Get unsubscribed details
	 *
	 * @param  int $id_newsletter
	 * @return string
	 */
	public function getUnsubscribedDetails($id_newsletter)
	{
		$sql = 'SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_unsibscribed` WHERE `id_newsletter_pro_tpl_history` = '.(int)$id_newsletter;
		$result = Db::getInstance()->executeS($sql);

		$this->context->smarty->assign(array(
			'result' => $result
		));

		return $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/admin/task/unsubscribed_detail.tpl'));
	}

	/**
	 * Get the task unsubscribed details
	 *
	 * @param  int $id_newsletter
	 * @return string
	 */
	public function getTaskUnsubscribedDetails($id_newsletter)
	{
		return $this->getUnsubscribedDetails($id_newsletter);
	}

	/**
	 * Get the task forwarders unsubscribed details
	 *
	 * @param  int $id_newsletter
	 * @return string
	 */
	public function getTaskFwdUnsubscribedDetails($id_newsletter)
	{
		return $this->getFwdUnsubscribedDetails($id_newsletter);
	}

	/**
	 * Get forwarders subscribed details
	 *
	 * @param  int $id_newsletter
	 * @return string
	 */
	public function getFwdUnsubscribedDetails($id_newsletter)
	{
		$sql = 'SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_fwd_unsibscribed` WHERE `id_newsletter_pro_tpl_history` = '.(int)$id_newsletter;
		$result = Db::getInstance()->executeS($sql);

		$this->context->smarty->assign(array(
			'result' => $result
		));

		return $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/admin/task/unsubscribed_detail.tpl'));
	}

	/**
	 * Get forwarders details
	 *
	 * @param  string $email
	 * @return string
	 */
	public function getForwarderDetails($email)
	{
		$sql = 'SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_forward` WHERE `from` = "'.pSQL($email).'" ;';
		$result = Db::getInstance()->executeS($sql);

		$this->context->smarty->assign(array(
			'result' => $result
		));

		return $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/admin/task/forwarder_detail.tpl'));
	}

	/**
	 * Get tasks history detail
	 *
	 * @param  int $id_step
	 * @return string
	 */
	public function getTasksHistoryDetail($id_step)
	{
		$sql = 'SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_task_step` WHERE `id_newsletter_pro_task_step` = '.(int)$id_step;
		$step = Db::getInstance()->getRow($sql);
		if ($step)
		{
			$step['emails_to_send'] = NewsletterProTools::unSerialize($step['emails_to_send']);
			$step['emails_sent'] = NewsletterProTools::unSerialize($step['emails_sent']);
		}
		$this->context->smarty->assign(array(
			'step' => $step
		));
		return $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/admin/task/task_history_detail.tpl'));
	}

	/**
	 * Get send history details
	 *
	 * @param  int $id_step
	 * @return string
	 */
	public function getSendHistoryDetail($id_step)
	{
		$sql = 'SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_send_step` WHERE `id_newsletter_pro_send_step` = '.(int)$id_step;
		$step = Db::getInstance()->getRow($sql);
		if ($step)
		{
			$step['emails_to_send'] = NewsletterProTools::unSerialize($step['emails_to_send']);
			$step['emails_sent'] = NewsletterProTools::unSerialize($step['emails_sent']);
		}
		$this->context->smarty->assign(array(
			'step' => $step
		));
		return $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/admin/task/send_history_detail.tpl'));
	}

	/**
	 * Clear task history
	 *
	 * @return json
	 */
	public function clearTaskHistory()
	{
		$response = array('status' => false, 'msg' => '');

		$sql = 'SELECT `id_newsletter_pro_tpl_history` AS `id`
				FROM `'._DB_PREFIX_.'newsletter_pro_task`
				WHERE `done` = 1
				AND `id_newsletter_pro_tpl_history` > 0;';

		if ($ids = Db::getInstance()->executeS($sql))
			foreach ($ids as $id)
			{
				Db::getInstance()->delete('newsletter_pro_tpl_history', '`id_newsletter_pro_tpl_history`='.(int)$id['id']);
				Db::getInstance()->delete('newsletter_pro_unsibscribed', '`id_newsletter_pro_tpl_history`='.(int)$id['id']);
			}

		$sql = 'DELETE t.*, ts.*
				FROM `'._DB_PREFIX_.'newsletter_pro_task` t
				LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_task_step` ts ON ts.`id_newsletter_pro_task` = t.`id_newsletter_pro_task`
				WHERE t.`done` = 1;';

		if (Db::getInstance()->execute($sql))
			$response['status'] = true;
		else
			$response['msg'] = $this->l('The task history cannot be deleted!');

		return Tools::jsonEncode($response);
	}

	/**
	 * Clear statistics
	 *
	 * @return json
	 */
	public function clearStatistics()
	{
		$errors = array();
		$response = array('status' => false, 'errors' => &$errors);

		$sql = 'DELETE FROM `'._DB_PREFIX_.'newsletter_pro_statistics` WHERE 1';

		if (!Db::getInstance()->execute($sql))
			$errors[] = $this->l('The statistics cannot be deleted!');

		if (empty($errors))
			$response['status'] = true;

		return Tools::jsonEncode($response);
	}

	/**
	 * Clear send history
	 *
	 * @return json
	 */
	public function clearSendHistory()
	{
		$response = array('status' => false, 'msg' => '');

		$sql = 'DELETE s.*, ss.*
			FROM `'._DB_PREFIX_.'newsletter_pro_send` s
			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_send_step` ss ON ss.`id_newsletter_pro_send` = s.`id_newsletter_pro_send`
			WHERE s.`active` = 0;';

		if (Db::getInstance()->execute($sql))
			$response['status'] = true;
		else
			$response['msg'] = $this->l('The history cannot be deleted!');

		return Tools::jsonEncode($response);
	}

	/**
	 * Delete task by id
	 *
	 * @param  int $id
	 * @return int
	 */
	public function deleteTask($id)
	{
		$sql = 'SELECT `id_newsletter_pro_tpl_history` FROM `'._DB_PREFIX_.'newsletter_pro_task` WHERE `id_newsletter_pro_task`='.(int)$id;
		if ($id_history = Db::getInstance()->getValue($sql))
		{
			Db::getInstance()->delete('newsletter_pro_tpl_history', '`id_newsletter_pro_tpl_history`='.(int)$id_history, 1);
			Db::getInstance()->delete('newsletter_pro_unsibscribed', '`id_newsletter_pro_tpl_history`='.(int)$id_history, 1);
		}

		return (int)(Db::getInstance()->delete('newsletter_pro_task', '`id_newsletter_pro_task`='.(int)$id, 1)
				&& Db::getInstance()->delete('newsletter_pro_task_step', '`id_newsletter_pro_task`='.(int)$id));
	}

	/**
	 * Delete send history by id
	 *
	 * @param  int $id
	 * @return int
	 */
	public function deleteSendHistory($id)
	{
		$sql = 'SELECT `id_newsletter_pro_tpl_history` FROM `'._DB_PREFIX_.'newsletter_pro_send` WHERE `id_newsletter_pro_send`='.(int)$id;
		if ($id_history = Db::getInstance()->getValue($sql))
		{
			Db::getInstance()->delete('newsletter_pro_tpl_history', '`id_newsletter_pro_tpl_history`='.(int)$id_history, 1);
			Db::getInstance()->delete('newsletter_pro_unsibscribed', '`id_newsletter_pro_tpl_history`='.(int)$id_history, 1);
		}

		return (int)(Db::getInstance()->delete('newsletter_pro_send', '`id_newsletter_pro_send`='.(int)$id, 1)
				&& Db::getInstance()->delete('newsletter_pro_send_step', '`id_newsletter_pro_send`='.(int)$id));
	}

	/**
	 * Update task
	 *
	 * @param  int $id
	 * @return int
	 */
	public function updateTask($id)
	{
		parse_str(Tools::file_get_contents('php://input'), $put);
		$data = array(
			'id_newsletter_pro_smtp' => (int)$put['id_newsletter_pro_smtp'],
			'send_method'			 => pSQL($put['send_method']),
			'date_start'             => pSQL($put['date_start']),
			'template'               => addcslashes($put['template'], "'"),
			'active'                 => (int)$put['active'],
		);

		$template = NewsletterProTemplate::newFile($put['template'])->load();
		$history = NewsletterProTplHistory::newInstance($put['id_newsletter_pro_tpl_history']);

		$history->template = $template->html(NewsletterProTemplateContent::CONTENT_HTML, true);
		$history->active = (bool)$put['active'];
		$history->template_name = $template->name;

		return (int)(Db::getInstance()->update('newsletter_pro_task', $data, '`id_newsletter_pro_task`= '.(int)$id) && $history->update());
	}

	/**
	 * Remove duplicate emails
	 *
	 * @param  string $emails
	 * @return array
	 */
	public function removeDuplicateEmails($emails)
	{
		$emails_return = array();
		foreach ($emails as $email)
		{
			if (!in_array($email, $emails_return))
				$emails_return[] = $email;
		}
		return $emails_return;
	}

	/**
	 * Create a new task
	 *
	 * @param json $data
	 */
	public function addTask($data)
	{
		$post =& $_POST;

		$emails = Tools::jsonEncode(array());

		if (isset($post['emails']))
			$emails = $post['emails'];

		$emails = Tools::jsonDecode($emails);

		$response = array('status' => false, 'errors' => &$this->_errors);

		$time = (int)strtotime($data['mysql_date']);

		if ($time == 0)
			$this->_errors[] = $this->l('The date is not valid!');
		if (empty($emails))
			$this->_errors[] = $this->l('No email was selected!');
		if (!trim($data['template']))
			$this->_errors[] = $this->l('Invalid template!');
		if (!(int)$data['id_newsletter_pro_smtp'] && $data['send_method'] == 'smtp')
			$this->_errors[] = $this->l('The smtp does not exists!');
		if ((int)$data['sleep'] < 0)
			$this->_errors[] = $this->l('Sleep time is not a valid integer!');

		$emails = $this->removeDuplicateEmails($emails);

		if (empty($this->_errors))
		{
			$step = $this->step;
			$last_tpl_id = 0;

			$template = NewsletterProTemplate::newFile($data['template'])->load();

			$history = NewsletterProTplHistory::newFromTemplate($template);
			$history->add();

			if (Db::getInstance()->insert('newsletter_pro_task', array(
				'id_newsletter_pro_smtp'        => (int)$data['id_newsletter_pro_smtp'],
				'id_newsletter_pro_tpl_history' => (int)$history->id,
				'send_method'                   => pSQL($data['send_method']),
				'template'                      => addcslashes($data['template'], "'"),
				'date_start'                    => pSQL($data['mysql_date']),
				'date_modified'                 => pSQL(date('Y-m-d H:i:s')),
				'active'                        => 1,
				'status'                        => 0,
				'sleep'                         => (int)$data['sleep'],
				'emails_count'                  => count($emails),
				'error_msg'                     => NewsletterProTools::dbSerialize(array()),
			)))
			{
				$id_task = (int)Db::getInstance()->Insert_ID();

				$emails_chuck = array_chunk($emails, $step);

				foreach ($emails_chuck as $i => $emails_list)
				{
					$task_smtp = array(
						'id_newsletter_pro_task' => $id_task,
						'step' => ++$i,
						'step_active' => 1,
						'emails_to_send' => NewsletterProTools::dbSerialize($emails_list),
						'emails_sent' => NewsletterProTools::dbSerialize(array()),
						'date' => date('Y-m-d H:i:s')
					);
					Db::getInstance()->insert('newsletter_pro_task_step', $task_smtp);
				}
				$response['status'] = true;
			}
			else
				$this->_errors[] = $this->l('The task cannot be added to the database!');
		}

		return Tools::jsonEncode($response);
	}

	/**
	* Select all in progress task
	* The returned data must be identical with the getTasks function
	*
	* @return array/boolean
	*/
	public function getTasksInProgress($look_for = array())
	{
		if (!is_array($look_for))
			$look_for = array();

		$sql = 'SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_task`
				WHERE `status` = 1
				AND `done` = 0
				ORDER BY `date_start` ASC;';

		$result_look = false;
		if (!empty($look_for))
		{
			$sql_look = 'SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_task`
						WHERE `id_newsletter_pro_task` IN ('.implode(', ', $look_for).');';

			$result_look = Db::getInstance()->executeS($sql_look);
		}

		$result = Db::getInstance()->executeS($sql);

		if ($result || $result_look)
		{
			if (is_array($result))
			{
				foreach ($result as &$row)
					$row['error_msg'] = NewsletterProTools::unSerialize($row['error_msg']);
			}

			return Tools::jsonEncode(array(
				'result' => is_array($result) ? $result : array(),
				'result_look' => is_array($result_look) ? array_values($result_look) : array(),
			));
		}
		else
		{
			try
			{
				$sql = 'SELECT count(*) AS `count` FROM `'._DB_PREFIX_.'newsletter_pro_task`
						WHERE `status` = 0
						AND `done` = 0;';

				if ($count = Db::getInstance()->getValue($sql))
					return (int)$count;
			}
			catch (Exception $e)
			{
				return (int)false;
			}
		}

		return (int)false;
	}

	/**
	 * Continue tasks
	 *
	 * @param  int $id
	 * @return int
	 */
	public function continueTaskAjax($id)
	{
		if (Db::getInstance()->update('newsletter_pro_task', array(
			'status' => 0,
			'done'   => 0,
			'pause'  => 0,
			), 'id_newsletter_pro_task = '.(int)$id))
		{
			$this->sendTaskAjax($id);
			return 1;
		}
		return 0;
	}

	/**
	 * Send one task runned in background
	 *
	 * @param  int $id
	 * @param  boolean $jump_next
	 */
	public function sendTaskAjax($id)
	{
		$task = NewsletterProTask::newInstance($id);
		if (Validate::isLoadedObject($task))
			$task->sendTaskAjax();
	}

	/**
	 * Pause task
	 *
	 * @param  int $id
	 * @return int
	 */
	public function pauseTask($id)
	{
		return NewsletterProTask::newInstance($id)->pauseTask();
	}

	/**
	 * Get all smtp connections
	 *
	 * @return array
	 */
	public function getAllSMTP()
	{
		$result = NewsletterProMail::getAllMails();

		$selected = $this->getConfiguration('SMTP');
		if (!empty($result))
		{
			if (!$selected)
				$result[0]['selected'] = true;
			else
			{
				$selected_exist = false;
				foreach ($result as &$value)
				{
					if ($value['id_newsletter_pro_smtp'] == $selected)
					{
						$selected_exist = true;
						$value['selected'] = true;
						break;
					}
				}

				if (!$selected_exist)
				{
					$this->updateConfiguration('SMTP', $result[0]['id_newsletter_pro_smtp']);
					$result[0]['selected'] = true;
				}
			}
		}

		return $result;
	}

	/**
	 * Get all smtp connections as a json format
	 *
	 * @return json
	 */
	public function getAllSMTPJson()
	{
		return Tools::jsonEncode($this->getAllSMTP());
	}

	/**
	 * Get category tree
	 *
	 * @return array
	 */
	public function getCategoryTree()
	{
		$root = Category::getRootCategory();
		$tab_root = array('id_category' => $root->id, 'name' => $root->name);

		$category_tree = $this->renderCategoryTree(array(
			'root'                => $tab_root,
			'selected_cat'        => array(),
			'input_name'          => 'categoryBox',
			'use_radio'           => false,
			'disabled_categories' => array(),
			'use_search'          => true,
			'use_in_popup'        => false,
			'use_shop_context'    => true,
			'option_no_decide'    => true,
			'ajax_request_url'    => $this->uri_location.'ajax/ajax_newsletterpro_front.php',
		));

		return $category_tree;
	}

	/**
	 * Set controller
	 */
	private function setController()
	{
		require_once $this->dir_location.'AdminNewsletterPro.php';
		$this->context->controller = new AdminNewsletterPro();
	}

	/**
	 * Get the top catetory
	 *
	 * @param  int $id_lang
	 * @return object
	 */
	public static function getTopCategory($id_lang = null)
	{
		if (method_exists('Category', 'getTopCategory'))
			return Category::getTopCategory($id_lang);

		if (is_null($id_lang))
			$id_lang = Context::getContext()->language->id;

		$id_category = Db::getInstance()->getValue('
			SELECT `id_category`
			FROM `'._DB_PREFIX_.'category`
			WHERE `id_parent` = 0
		');
		return new Category($id_category, $id_lang);
	}

	/**
	 * Add the wright javascript tree plugin files
	 *
	 * @param object $controller
	 */
	public function addTreeViewFiles($controller)
	{
		$jquery_plugins_foldername = _PS_ROOT_DIR_.'/js/jquery/plugins';

		if (file_exists($jquery_plugins_foldername.'/treeview-categories'))
		{
			$controller->addCSS(_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.css');
			$controller->addJs(array(
				_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.js',
				_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.async.js',
				_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.edit.js',
			));
		}
		else
		{
			$controller->addCSS(_PS_JS_DIR_.'jquery/plugins/treeview/jquery.treeview.css');
			$controller->addJs(array(
				_PS_JS_DIR_.'jquery/plugins/treeview/jquery.treeview.js',
				_PS_JS_DIR_.'jquery/plugins/treeview/jquery.treeview.async.js',
				_PS_JS_DIR_.'jquery/plugins/treeview/jquery.treeview.edit.js',
			));
		}
	}

	/**
	 * Render category tree
	 *
	 * @param  array $cfg
	 * @return string
	 */
	public function renderCategoryTree($cfg)
	{
		$root = isset($cfg['root']) ? $cfg['root'] : null;
		$selected_cat = isset($cfg['selected_cat']) ? $cfg['selected_cat'] : array();
		$input_name = isset($cfg['input_name']) ? $cfg['input_name'] : 'categoryBox';
		$use_radio = isset($cfg['use_radio']) ? $cfg['use_radio'] : false;
		$use_search = isset($cfg['use_search']) ? $cfg['use_search'] : false;
		$disabled_categories = isset($cfg['disabled_categories']) ? $cfg['disabled_categories'] : array();
		$use_in_popup = isset($cfg['use_in_popup']) ? $cfg['use_in_popup'] : false;
		$use_shop_context = isset($cfg['use_shop_context']) ? $cfg['use_shop_context'] : false;
		$ajax_request_url = isset($cfg['ajax_request_url']) ? $cfg['ajax_request_url'] : 'ajax.php';

		$option_no_decide = isset($cfg['option_no_decide']) ? $cfg['option_no_decide'] : false;

		if (!property_exists($this->context, 'controller') || !isset($this->context->controller))
			$this->setController();

		$translations = array(
			'selected'     => $this->l('Selected'),
			'Collapse All' => $this->l('Collapse All'),
			'Expand All'   => $this->l('Expand All'),
			'Check All'    => $this->l('Check All'),
			'Uncheck All'  => $this->l('Uncheck All'),
			'search'       => $this->l('Find a category')
		);

		$top_category = NewsletterPro::getTopCategory();
		if (Tools::isSubmit('id_shop'))
			$id_shop = Tools::getValue('id_shop');
		else
			if (Context::getContext()->shop->id)
				$id_shop = Context::getContext()->shop->id;
			else
				if (!Shop::isFeatureActive())
					$id_shop = Configuration::get('PS_SHOP_DEFAULT');
				else
					$id_shop = 0;

		$shop = new Shop($id_shop);
		$root_category = Category::getRootCategory(null, $shop);
		$disabled_categories[] = $top_category->id;
		if (!$root)
			$root = array('name' => $root_category->name, 'id_category' => $root_category->id);

		if (!$use_radio)
			$input_name = $input_name.'[]';

		$this->addTreeViewFiles($this->context->controller);

		$this->context->controller->addJs(array(
			$this->uri_location.'views/js/categories-tree.js'
		));

		if ($use_search)
			$this->context->controller->addJs(_PS_JS_DIR_.'jquery/plugins/autocomplete/jquery.autocomplete.js');

		$selected_cat_var = '';
		if (count($selected_cat) > 0)
		{
			if (isset($selected_cat[0]))
				$selected_cat_var = (int)implode(',', $selected_cat);
			else
				$selected_cat_var = (int)implode(',', array_keys($selected_cat));
		}

		$content = '';
		$home_is_selected = false;
		foreach ($selected_cat as $cat)
		{
			if (is_array($cat))
			{
				$disabled = in_array($cat['id_category'], $disabled_categories);
				if ($cat['id_category'] != $root['id_category'])
					$content .= '<input '.($disabled?'disabled="disabled"':'').' type="hidden" name="'.$input_name.'" value="'.$cat['id_category'].'" >';
				else
					$home_is_selected = true;
			}
			else
			{
				$disabled = in_array($cat, $disabled_categories);
				if ($cat != $root['id_category'])
					$content .= '<input '.($disabled?'disabled="disabled"':'').' type="hidden" name="'.$input_name.'" value="'.$cat.'" >';
				else
					$home_is_selected = true;
			}
		}

		$this->context->smarty->assign(array(
			'input_name' => addcslashes($input_name, "'\""),
			'selected_cat' => $selected_cat_var,
			'selected_label' => addcslashes($translations['selected'], "'\""),
			'home' => addcslashes($root['name'], "'\""),
			'use_radio' => $use_radio,
			'use_search' => $use_search,
			'use_in_popup' => $use_in_popup,
			'use_shop_context' => $use_shop_context,
			'root' => $root,
			'content' => $content,
			'root_input' => ($root['id_category'] != $top_category->id || (Tools::isSubmit('ajax') && Tools::getValue('action') == 'getCategoriesFromRootCategory')),
			'home_is_selected' => $home_is_selected,
			'ajax_request_url' => $ajax_request_url,
			'option_no_decide' => $option_no_decide,
		));

		return $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/front/category_tree.tpl'));
	}

	/**
	 * Check if the campaign is running properly
	 *
	 * @return json
	 */
	public function checkIfCampaignIsRunning()
	{
		$errors = array();
		$response = array('status' => false, 'errors' => &$errors, 'msg' => '');

		try
		{

			$curl = new NewsletterProCurl();

			$params = array(
				'newsletterpro_source' => 'newsletter',
				'utm_source'           => 'testCampaign',
			);

			$index_url = $this->context->link->getPageLink('index');

			$result = $curl->request('GET', $curl->url($index_url, 'html'), $params);

			if (strpos($result['response'], '[Debug]') !== false)
			{
				preg_match('/("|\')(?P<url>.*newsletterpro_source=newsletter\&utm_source=testCampaign.*?)\1/', $result['response'], $match);
				if (isset($match['url']))
					$result = $curl->request('GET', $curl->url($match['url'], 'html'), $params);
			}

			if ($result['code'] == 205)
			{
				$index = $result['response'];

				if ((bool)$this->getConfiguration('GOOGLE_UNIVERSAL_ANALYTICS_ACTIVE'))
				{
					if (!preg_match('/(?:\s+)?ga(?:\s+)?\((?:\s+)?\'set\'(?:\s+)?,(?:\s+)?\'campaignSource\'/', $index))
						$errors[] = $this->l('The campaign is not set correctly!');
				}
				else
				{
					if (!preg_match('/_setCampaignTrack(?:\s+)?(\'|")(?:\s+)?,(?:\s+)?true/', $index))
						$errors[] = $this->l('The campaign is not set correctly!');
				}
			}
			else if ($result['code'] == 200)
				$errors[] = $this->l('The campaign is not set correctly!');
			else if ($result['code'] == 1)
				$errors[] = $result['response'];
			else
				$errors[] = $this->l('Error on running this verification!');
		}
		catch (Exception $e)
		{
			$errors[] = $e->getMessage();
		}

		if (empty($errors))
		{
			$response['msg'] = $this->l('The campaign is set correctly!');
			$response['status'] = true;
		}
		return Tools::jsonEncode($response);
	}

	/**
	 * Get statistics
	 *
	 * @return json
	 */
	public function getStatistics()
	{
		$id_lang = (int)$this->context->language->id;

		if ($this->isLowerVersion('1.5.1'))
		{
			$sql = 'SELECT
					nps.`id_product`, nps.`clicks`, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`name`, pl.`id_lang`, p.*, asso_shop_image.`id_image`
					FROM '._DB_PREFIX_.'newsletter_pro_statistics nps
					LEFT JOIN '._DB_PREFIX_.'product p on (nps.id_product = p.id_product)
					LEFT JOIN '._DB_PREFIX_.'product_lang pl on (p.id_product = pl.id_product AND pl.id_lang = '.$id_lang.')
					LEFT JOIN `'._DB_PREFIX_.'image` i
						ON (i.`id_product` = p.`id_product`)'.
						@Shop::addSqlAssociation('image', 'i', true, 'image_shop.cover=1').'
					LEFT JOIN `'._DB_PREFIX_.'image_lang` il
						ON (asso_shop_image.`id_image` = il.`id_image`
						AND il.`id_lang` = '.(int)$id_lang.')

					GROUP BY nps.`id_product`
					ORDER BY nps.`clicks` DESC
					LIMIT 100;';
		}
		else
		{
			$sql = 'SELECT
					nps.`id_product`, nps.`clicks`, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`name`, pl.`id_lang`, p.*, image_shop.`id_image`
					FROM '._DB_PREFIX_.'newsletter_pro_statistics nps
					LEFT JOIN '._DB_PREFIX_.'product p on (nps.id_product = p.id_product)
					LEFT JOIN '._DB_PREFIX_.'product_lang pl on (p.id_product = pl.id_product AND pl.id_lang = '.$id_lang.')
					LEFT JOIN `'._DB_PREFIX_.'image` i
						ON (i.`id_product` = p.`id_product`)'.
						Shop::addSqlAssociation('image', 'i', true, 'image_shop.cover=1').'
					LEFT JOIN `'._DB_PREFIX_.'image_lang` il
						ON (image_shop.`id_image` = il.`id_image`
						AND il.`id_lang` = '.(int)$id_lang.')

					GROUP BY nps.`id_product`
					ORDER BY nps.`clicks` DESC
					LIMIT 100;';
		}

		$result = Db::getInstance()->executeS($sql);
		$products = array();
		if ($result)
		{
			$this->context->currency = new Currency((int)$this->getConfiguration('PS_CURRENCY_DEFAULT'));
			$result = $this->getStatisticsProductsAttributes( $id_lang, $result, (int)$this->context->currency->id);

			foreach ($result as $key => $value)
			{
				$products[$key]['id_product'] = $value['id_product'];
				$products[$key]['clicks'] = $value['clicks'];
				$products[$key]['name'] = $value['name'];
				$products[$key]['price_display'] = $value['price_display'];
				$products[$key]['link'] = $this->sanitizeLink($value['link']);
				$products[$key]['thumb_path'] = $value['thumb_path'];
				$products[$key]['top'] = $key + 1;
			}
			return Tools::jsonEncode($products);
		}

		return Tools::jsonEncode(array());
	}

	/**
	 * Some of the queries are unuseful in the admin backoffice and a sanitize is required
	 *
	 * @param  string $link
	 * @return string
	 */
	public function sanitizeLink($link)
	{
		$link = preg_replace('/&id_newsletter=[^}]+\}(?=&|$)/', '', $link);
		$link = preg_replace('/&newsletterpro_source=newsletter/', '', $link);
		return $link;
	}

	/**
	 * Execute the module update
	 *
	 * @return json
	 */
	public function updateModule()
	{
		$errors = array();
		$response = array('status' => false, 'errors' => &$errors, 'message' => array());

		if (!$this->upgrade->execute())
			$errors = array_merge($errors, $this->upgrade->getErrors());

		if (empty($errors))
		{
			$response['status'] = true;
			$response['message'][] = $this->l('The module was update successfully!');
			$response['message'][] = $this->l('The browser will refresh in %s seconds.');
		}

		return Tools::jsonEncode($response);
	}

	/**
	 * Get update details
	 *
	 * @return array
	 */
	public function getUpdateDetails()
	{
		$db_version = $this->getDbVersion();
		return array(
			'needs_update' => ($db_version != $this->version),
			'db_version'   => $db_version,
			'version'      => $this->version,
		);
	}

	/**
	 * Get module database version
	 *
	 * @return string
	 */
	public function getDbVersion()
	{
		return Db::getInstance()->getValue('SELECT `version` FROM `'._DB_PREFIX_.'module` WHERE `name` = "'.pSQL($this->name).'"');
	}

	/**
	 * Get the products statistics attributes
	 *
	 * @param  int $id_lang
	 * @param  array $pr
	 * @param  int $id_currency
	 * @return array
	 */
	public function getStatisticsProductsAttributes($id_lang, $pr, $id_currency = null)
	{
		$prop = $this->getNewProperties(null, $id_currency);

		$products = Product::getProductsProperties($id_lang, $pr);

		foreach ($products as &$product)
			$this->createProductTemplateVars( $id_lang, $product, $prop );

		return $products;
	}

	/**
	 * Get the filter by purchase content
	 *
	 * @return string
	 */
	public function getFilterByPurchaseContent()
	{
		$tpl = $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/admin/filter_by_purchase.tpl'));
		return $tpl;
	}

	/**
	 * Get filter by birthday content
	 *
	 * @param  string $fbb_class
	 * @return string
	 */
	public function getFilterByBirthdayContent($fbb_class = '')
	{
		$this->context->smarty->assign(array(
			'fbb_class' => $fbb_class,
		));

		$tpl = $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/admin/filter_by_birthday.tpl'));
		return $tpl;
	}

	/**
	 * Get range selection content
	 *
	 * @return string
	 */
	public function getRangeSelectionContent()
	{
		$tpl = $this->context->smarty->fetch(pqnp_template_path($this->dir_location.'views/templates/admin/filter_by_range.tpl'));
		return $tpl;
	}

	/**
	 * Search by purchased product
	 *
	 * @param  string $query
	 * @return json
	 */
	public function searchByPurchase($query)
	{
		return $this->searchProducts($query, (int)$this->context->language->id, (int)$this->getConfiguration('PS_CURRENCY_DEFAULT'));
	}

	/**
	 * Get customer language id
	 *
	 * @param  int $id_customer
	 * @return int
	 */
	public function getCustomerIdLang($id_customer)
	{
		return Db::getInstance()->getValue('SELECT `id_lang` FROM '._DB_PREFIX_.'customer WHERE `id_customer` = '.(int)$id_customer);
	}

	/**
	 * Strip array
	 *
	 * @param  array  $array
	 * @param  integer $level
	 * @return array
	 */
	public static function strip($array, $level = 10)
	{
		if (_PS_MAGIC_QUOTES_GPC_)
		{
			if ($level < 0)
				return $array;

			if (is_array($array))
			{
				foreach ($array as $key => $value)
				{
					if (is_array($value))
						$array[$key] = self::strip($value, --$level);
					else
						$array[$key] = Tools::stripslashes($value);
				}
			}
			else if (is_string($array))
				$array = Tools::stripslashes($array);
		}

		return $array;
	}

	/**
	 * Get hooks list
	 *
	 * @return array
	 */
	public function getHooksList()
	{
		$reflection = new ReflectionObject($this);
		$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
		$all_hooks = array();
		foreach ($methods as $method)
		{
			if (preg_match('/^hook[A-Z]/', $method->name) && $method->class == __CLASS__)
				$all_hooks[] = lcfirst(preg_replace('/^hook/', '', $method->name));
		}

		return $all_hooks;
	}

	/**
	 * For the order version of prestashop
	 *
	 * @param  boolean $share
	 * @return array
	 */
	public function getListOfID($share = false)
	{
		$shop_id = $this->getID();
		$shop_group_id = $this->getGroupID();

		if ($shop_id)
			$list = ($share) ? Shop::getSharedShops($shop_id, $share) : array($shop_id);
		else if ($shop_group_id)
			$list = Shop::getShops(true, $shop_group_id, true);
		else
			$list = Shop::getShops(true, null, true);

		return $list;
	}

	/**
	 * For the order version of prestashop
	 *
	 * @param  boolean $use_default
	 * @return array
	 */
	public function getID($use_default = false)
	{
		return (!$this->id && $use_default) ? (int)Configuration::get('PS_SHOP_DEFAULT') : (int)$this->id;
	}

	/**
	 * For the order version of prestashop
	 *
	 * @return int
	 */
	public function getGroupID()
	{
		if (defined('_PS_ADMIN_DIR_'))
			return Shop::getContextGroupID();
		return (int)$this->id_group_shop;
	}

	/**
	 * Get our modules
	 *
	 * @return string
	 */
	public function getOurModules()
	{
		return NewsletterProOurModules::newInstance()->get();
	}

	/**
	 * Clear log files
	 *
	 * @return json
	 */
	public function clearLogFiles()
	{
		$response = new NewsletterProAjaxResponse(array(
			'msg' => '',
		));

		foreach (NewsletterProLog::getFiles() as $filename)
		{
			if (!NewsletterProLog::clear($filename))
				$response->addError(sprintf($this->l('The log file "%s" cannot be cleared.'), $filename));
		}

		if ($response->success())
			$response->set('msg', $this->l('The log files has been cleared.'));

		return $response->display();
	}

	/**
	 * Create backup
	 *
	 * @param  string  $name
	 * @param  boolean $check_duplicate
	 * @return boolean
	 */
	public function ajaxCreateBackup($name, $check_duplicate = true)
	{
		@ini_set('max_execution_time', '600');

		$response = new NewsletterProAjaxResponse(array(
			'msg' => '',
		));

		try
		{
			$backup_tables = array(
					'newsletter_pro_config',
					'newsletter_pro_customer_category',
					'newsletter_pro_email',
					'newsletter_pro_email_exclusion',
					'newsletter_pro_forward',
					'newsletter_pro_filters_selection',
					'newsletter_pro_fwd_unsibscribed',
					'newsletter_pro_list_of_interest',
					'newsletter_pro_list_of_interest_lang',
					'newsletter_pro_list_of_interest_shop',
					'newsletter_pro_send',
					'newsletter_pro_send_connection',
					'newsletter_pro_send_step',
					'newsletter_pro_smtp',
					'newsletter_pro_statistics',
					'newsletter_pro_subscribers',
					'newsletter_pro_subscribers_temp',
					'newsletter_pro_subscription_tpl',
					'newsletter_pro_subscription_tpl_lang',
					'newsletter_pro_subscription_tpl_shop',
					'newsletter_pro_task',
					'newsletter_pro_task_step',
					'newsletter_pro_tpl_history',
					'newsletter_pro_unsibscribed',
			);

			$required_config_shop = NewsletterProConfigurationShop::getAllShopsConfiguration();

			$name = trim($name);
			if (empty($name))
				$response->addError($this->l('The name cannot be empty.'));
			else if (!NewsletterProTools::isFileName($name))
				$response->addError($this->l('Some of the name characters are not allowed.'));

			if (self::getBackupType() == self::BACKUP_TYPE_XML)
			{
				$backup = new NewsletterProBackupXml();
				// if the second value is true, the backup will be in hex values
				$backup->create($backup_tables, false);

				$backup->addHeader('configuration_shop', serialize($required_config_shop));
				$backup->addHeader('configuration', serialize($this->configuration));
				$backup->addHeader('configuration_campaign', Configuration::get('NEWSLETTER_PRO_CAMPAIGN'));

				$bn_info = $this->getNewsletterProInfo();

				$backup->addHeader('hooks', serialize($bn_info['hooks']));

				$path_name = 'global/xml/'.NewsletterProBackupXml::formatName($name);

				if (NewsletterProBackupXml::pathNameExists($path_name, true) && $check_duplicate)
					$response->addError($this->l('The backup name already exists.'));

				if ($response->success())
				{
					if (!$backup->save($path_name, false))
						$response->addError($this->l('An error occurred at the backup creation. Please check the CHMOD permissions.'));
					else
						$response->set('msg', $this->l('The backup has made successfully.'));
				}
			}
			else
			{
				// create a global backup
				$backup = new NewsletterProBackupSql();
				$backup->create($backup_tables);

				$backup->setHeader('configuration_shop', serialize($required_config_shop));
				$backup->setHeader('configuration', serialize($this->configuration));

				$path_name = 'global/sql/'.NewsletterProBackupSql::formatName($name);

				if (NewsletterProBackupSql::pathNameExists($path_name, true) && $check_duplicate)
					$response->addError($this->l('The backup name already exists. Try with a different name.'));

				if ($response->success())
				{
					if (!$backup->save($path_name, false))
						$response->addError($this->l('An error occurred at the backup creation. Please check the CHMOD permissions.'));
					else
						$response->set('msg', $this->l('The backup has made successfully.'));
				}

			}
		}
		catch (Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	/**
	 * Show backups
	 *
	 * @return string
	 */
	public function showLoadBackup()
	{
		$tpl = $this->context->smarty->createTemplate(pqnp_template_path($this->dir_location.'views/templates/admin/global_create_backup.tpl'));
		return $tpl->fetch();
	}

	/**
	 * Get backups
	 *
	 * @return json
	 */
	public function ajaxGetBackup()
	{
		$list = array();
		$date = array();

		if (self::getBackupType() == self::BACKUP_TYPE_XML)
		{
			$index = 1;
			foreach (NewsletterProBackupXml::getList('global/xml') as $item)
			{
				$item['id'] = $index++;
				$list[$index] = $item;
				$date[$index] = $item['m_date'];
			}
		}
		else
		{
			$index = 1;
			foreach (NewsletterProBackupSql::getList('global/sql') as $item)
			{
				$item['id'] = $index++;
				$list[$index] = $item;
				$date[$index] = $item['m_date'];
			}
		}

		// srot array by last modification
		array_multisort($date, SORT_DESC, $list);
		return Tools::jsonEncode($list);
	}

	/**
	 * Delete backup
	 *
	 * @param  string $basename
	 * @return json
	 */
	public function ajaxDeleteBackup($basename)
	{
		$response = new NewsletterProAjaxResponse(array(
			'msg' => '',
		));

		if (self::getBackupType() == self::BACKUP_TYPE_XML)
			$path = NewsletterProBackupXml::path().'/global/xml/'.$basename;
		else
			$path = NewsletterProBackupSql::path().'/global/sql/'.$basename;

		if (file_exists($path))
		{
			if (@unlink($path) === false)
				$this->addError($this->l('Cannot delete the record. Please check the CHMOD permissions.'));
		}
		else
			$this->addError($this->l('The file does not exists anymore.'));

		return $response->display();
	}

	/**
	 * Restore backup
	 *
	 * @param  string $basename
	 * @return json
	 */
	public function ajaxLoadBackup($basename)
	{
		@ini_set('max_execution_time', '600');

		$response = new NewsletterProAjaxResponse(array(
			'msg' => '',
		));

		try
		{
			if (self::getBackupType() == self::BACKUP_TYPE_XML)
			{
				$backup = new NewsletterProBackupXml();
				$backup->load('/global/xml/'.$basename);
				if (!$backup->execute())
					$response->addError($this->l('The restore process has faild.'));
				else
					$response->set('msg', $this->l('The backup was restored successfully.'));
			}
			else
			{
				$backup = new NewsletterProBackupSql();
				$backup->load('/global/sql/'.$basename);
				if (!$backup->execute())
					$response->addError($this->l('The restore process has faild.'));
				else
					$response->set('msg', $this->l('The backup was restored successfully.'));
			}

		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	/**
	 * Get backup type
	 *
	 * @return int
	 */
	public static function getBackupType()
	{
		if (Tools::strtolower(self::BACKUP_TYPE) == 'xml')
			return self::BACKUP_TYPE_XML;
		return self::BACKUP_TYPE_SQL;
	}

	/**
	 * Delete the bounced emails
	 * 
	 * @param  array $file
	 * @param  array $post 
	 * @return json
	 */
	public function deleteBouncedEmails($file)
	{
		@ini_set('max_execution_time', 600);

		$response = new NewsletterProAjaxResponse(array(
			'msg'   => '',
			'lists' => array()
		));

		if (!Tools::isSubmit('bounced_customers_list')
			&& !Tools::isSubmit('bounced_visitors_list')
			&& !Tools::isSubmit('bounced_visitors_np_list')
			&& !Tools::isSubmit('bounced_added_list'))
			$response->addError($this->l('You must select at least a list before to proceed.'));

		if (!$response->success())
			return $response->display();

		$bounced_method = (int)Tools::getValue('bounced_method');
		$success        = 0;
		$errors         = 0;
		$emails_count   = 0;

		$tables_list = array();

		if (Tools::isSubmit('bounced_customers_list'))
			$tables_list[] = 'customers';

		if (Tools::isSubmit('bounced_visitors_list'))
			$tables_list[] = 'visitors';

		if (Tools::isSubmit('bounced_visitors_np_list'))
			$tables_list[] = 'visitors_np';

		if (Tools::isSubmit('bounced_added_list'))
			$tables_list[] = 'added';

		$response->set('lists', $tables_list);

		if (isset($file))
		{
			if (preg_match('/\.csv$/i', $file['name']))
			{
				$validate = $this->verifyFileErros($file);
				if ($validate === true)
				{
					$separator = trim(Tools::getValue('bounced_csv_separator'));
					
					if ($separator != ';' || $separator != ',')
						$separator = ';';

					$rows = $this->csvToArray($file['tmp_name'], $separator, 2, 0, false);
					foreach ($rows['rows'] as $row_array) 
					{
						$emails_count++;
						$email = trim($row_array[key($row_array)]);

						if ($this->executeBouncedEmail($email, $tables_list, $bounced_method))
							$success++;
						else
							$errors++;
					}
				}
				else 
					$response->addError($validate);
			}
			else
				$response->addError($this->l('The file extension is not allowed. Only the .csv file extensions are allowed.'));
		}
		else
			$response->addError($this->l('You need to select the .CSV file first.'));

		if ($response->success())
		{
			$action_msg = $bounced_method == -1 ? $this->l('removed') : $this->l('unsubscribed');
			$response->set('msg', $this->l(sprintf('You have %s %s from %s emails.', $action_msg, $success, $emails_count)));
		}

		return $response->display();
	}

	/**
	 * Bounce email from the four list
	 * 
	 * @param  string $email 
	 * @param  array $lists  [customers, visitors, visitors_np, added]
	 * @param  int $method 
	 * @return boolean
	 */
	public function executeBouncedEmail($email, $lists = array(), $method = -1)
	{
		$intersect = array_intersect($lists, array('customers', 'visitors', 'visitors_np', 'added'));

		if (!empty($lists) && empty($intersect))
			throw new InvalidArgumentException(sprintf('Invalid parameter %s values (%s).', '$lists', implode(', ', $lists)));

		$success = array();
		
		if (empty($lists) || in_array('visitors', $lists))
		{
			// Verify if the newsletter table exists
			$sql = "SELECT COUNT(*) AS `count`
					FROM INFORMATION_SCHEMA.TABLES
					WHERE  TABLE_SCHEMA = '"._DB_NAME_."' 
					AND TABLE_NAME = '"._DB_PREFIX_."newsletter'";

			$count = (int)Db::getInstance()->getValue($sql);
			if ($count >= 1)
			{
				$id_visitor = (int)Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'newsletter` WHERE `email` = "'.pSQL($email).'"');

				if ($id_visitor)
				{
					if ($method == -1)
						$success[] = $this->deleteVisitor($id_visitor);
					else
					{
						$success[] = Db::getInstance()->update('newsletter', array(
							'active' => 0
						), '`id` = '.(int)$id_visitor);
					}
				}
			}
		}

		if (empty($lists) || in_array('customers', $lists))
		{
			$id_customer = (int)Db::getInstance()->getValue('SELECT `id_customer` FROM `'._DB_PREFIX_.'customer` WHERE `email` = "'.pSQL($email).'"');
			
			if ($id_customer)
			{
				if ($method == -1)
					$success[] = $this->deleteCustomer($id_customer);
				else
				{
					$success[] = Db::getInstance()->update('customer', array(
						'newsletter' => 0
					), '`id_customer` = '.(int)$id_customer);
				}
			}
		}

		if (empty($lists) || in_array('added', $lists))
		{
			$id_added = (int)Db::getInstance()->getValue('SELECT `id_newsletter_pro_email` FROM `'._DB_PREFIX_.'newsletter_pro_email` WHERE `email` = "'.pSQL($email).'"');
		
			if ($id_added)			
			{
				if ($method == -1)
					$success[] = $this->deleteAdded($id_added);
				else
				{
					$success[] = Db::getInstance()->update('newsletter_pro_email', array(
						'active' => 0
					), '`id_newsletter_pro_email` = '.(int)$id_added);
				}
			}
		}

		if (empty($lists) || in_array('visitors_np', $lists))
		{
			$id_visitor_np = (int)Db::getInstance()->getValue('SELECT `id_newsletter_pro_subscribers` FROM `'._DB_PREFIX_.'newsletter_pro_subscribers` WHERE `email` = "'.pSQL($email).'"');
			
			if ($id_visitor_np)
			{
				if ($method == -1)
					$success[] = $this->deleteVisitorNP($id_visitor_np);
				else
				{
					$success[] = Db::getInstance()->update('newsletter_pro_subscribers', array(
						'active' => 0
					), '`id_newsletter_pro_subscribers` = '.(int)$id_visitor_np);
				}
			}
		}

		return (!empty($success) ? !in_array(false, $success) : false);
	}

	public function getHistoryExclusion()
	{
		$list = array();

		$send = Db::getInstance()->executeS('
			SELECT h.`id_newsletter_pro_tpl_history`,
					s.`id_newsletter_pro_send`, 
					s.`template`, 
					s.`date`, 
					s.`emails_count`, 
					s.`emails_success`,
					s.`emails_error`
			FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history` h
			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_send` s ON (s.`id_newsletter_pro_tpl_history` = h.`id_newsletter_pro_tpl_history`)
			WHERE s.`active` = 0
		');

		$i = 0;
		foreach ($send as $value) 
		{
			$value['type'] = 'send';
			$list[$i] = $value;
			$i++;
		}

		$task = Db::getInstance()->executeS('
			SELECT h.`id_newsletter_pro_tpl_history`,
					t.`id_newsletter_pro_task`, 
					t.`template`, 
					t.`date_start` as `date`, 
					t.`emails_count`, 
					t.`emails_success`,
					t.`emails_error`
			FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history` h
			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_task` t ON (t.`id_newsletter_pro_tpl_history` = h.`id_newsletter_pro_tpl_history`)
			WHERE t.`done` = 1
		');

		foreach ($task as $value) 
		{
			$value['type'] = 'task';
			$list[$i] = $value;	
			$i++;
		}

		usort($list, array($this, 'sortByDate'));

		foreach ($list as &$value) 
		{
			$value['template'] = Tools::ucfirst(str_replace('_', ' ', pathinfo($value['template'], PATHINFO_FILENAME)));
			$value['date'] = date('Y-m-d', strtotime($value['date']));
		}

		return Tools::jsonEncode($list);
	}
	
	public function getExclusionList()
	{
	    
	    $list = Db::getInstance()->executeS('
            SELECT *
			FROM `'._DB_PREFIX_.'newsletter_pro_email_exclusion`
        ');
	    
	    return Tools::jsonEncode($list);
	}

	public function addHistoryEmailsToExclusion($data, $bool_remaining_email, $bool_sent_email)
	{
		$response = NewsletterProAjaxResponse::newInstance(array(
			'msg' => '',
			'count' => 0,
		));

		try
		{
			if (!$bool_remaining_email && !$bool_sent_email)
			{
				$response->addError($this->l('One of the option "Remaining email" or "Sent email" should me checked.'));
				return $response->display();
			}

			if (is_array($data) && !empty($data))
			{
				$type_send = 'send';

				$ids_send = array();
				$ids_task = array();

				foreach ($data as $value) 
					if ($value['type'] == $type_send)
						$ids_send[] = $value['id'];
					else
						$ids_task[] = $value['id'];

				$email_exclusion = NewsletterProEmailExclusion::newInstance();

				$send_emails = $email_exclusion->getEmailsFromSend($ids_send, $bool_remaining_email, $bool_sent_email);
				$task_emails = $email_exclusion->getEmailsFromSend($ids_task, $bool_remaining_email, $bool_sent_email);

				$emails = array_merge($send_emails, $task_emails);

				$result = $email_exclusion->add($emails);
				$response->set('msg', sprintf($this->l('(%s) emails was added to exclusion list and (%s) emails already exists into exclusion list.'), $result[0], $result[1]));
				$response->set('count', $email_exclusion->countList());
			}
			else
				$response->addError($this->l('There are not items selected.'));
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	public function clearExclusionEmails()
	{
		$response = NewsletterProAjaxResponse::newInstance(array(
			'msg' => ''
		));

		if (!NewsletterProEmailExclusion::newInstance()->emptyList())
			$response->addError($this->l('An error occurred.'));
		else
			$response->set('msg', $this->l('The exclusion emails list has been cleared.'));

		return $response->display();
	}

	public function addCsvEmailsToExclusion($file)
	{
		$response = NewsletterProAjaxResponse::newInstance(array(
			'msg' => '',
			'count' => 0,
		));

		try
		{
			if (isset($file))
			{
				if (preg_match('/\.csv$/i', $file['name']))
				{
					$validate = $this->verifyFileErros($file);
					if ($validate === true)
					{
						$separator = trim(Tools::getValue('exclusion_emails_csv_separator'));
						
						if ($separator != ';' || $separator != ',')
							$separator = ';';

						$rows = $this->csvToArray($file['tmp_name'], $separator, 2, 0, false);
						
						$email_exclusion = NewsletterProEmailExclusion::newInstance();
						$emails = array();
						foreach ($rows['rows'] as $row_array) 
							$emails[] = trim($row_array[key($row_array)]);

						$result = $email_exclusion->add($emails);

						$response->set('msg', sprintf($this->l('(%s) emails was added to exclusion list and (%s) emails already exists into exclusion list.'), $result[0], $result[1]));
						$response->set('count', $email_exclusion->countList());
					}
					else 
						$response->addError($validate);
				}
				else
					$response->addError($this->l('The file extension is not allowed. Only the .csv file extensions are allowed.'));
			}
			else
				$response->addError($this->l('You need to select the .CSV file first.'));
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	public function isSendNewsletterInProgress()
	{
		return (int)Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_send`
			FROM `'._DB_PREFIX_.'newsletter_pro_send`
			WHERE `id_newsletter_pro_send` = (SELECT MAX(`id_newsletter_pro_send`) FROM '._DB_PREFIX_.'newsletter_pro_send)
			AND `active` = 1
		');
	}

	public function resendSendHistory($id_history, $left_list = 0, $right_list_undelivered = 0)
	{
		$emails = array();
		$response = NewsletterProAjaxResponse::newInstance(array(
			'emails'	=> &$emails,
			'id_newsletter_pro_send'  => 0,
			'id_history'              => 0,
		));

		try
		{
			if (!$left_list && !$right_list_undelivered)
			{
				$response->addError($this->l('You must select at least one list before to proceed.'));
				return $response->display();
			}

			$id_history = $this->getHistoryId($id_history);
			$response->set('id_history', (int)$id_history);

			if (!$id_history)
			{
				$response->addError($this->l('Invalid hisotry id.'));
				return $response->display();
			}

			$id_newsletter_pro_send = (int)Db::getInstance()->getValue('
				SELECT `id_newsletter_pro_send` FROM `'._DB_PREFIX_.'newsletter_pro_send`
				WHERE `id_newsletter_pro_tpl_history` = '.(int)$id_history.'
			');

			$response->set('id_newsletter_pro_send', (int)$id_newsletter_pro_send);

			if (!$id_newsletter_pro_send)
			{
				$response->addError(sprintf($this->l('Invalid database table "%s" id.'), 'newsletter_pro_send'));
				return $response->display();
			}

			$emails_to_send = array();
			$emails_sent_faild = array();
			$emails_sent_success = array();

			$results = Db::getInstance()->executeS('
				SELECT `emails_to_send`, `emails_sent`
				FROM `'._DB_PREFIX_.'newsletter_pro_send_step`
				WHERE `id_newsletter_pro_send` = '.(int)$id_newsletter_pro_send.'
				ORDER BY `step` ASC
			');

			if (!$results)
			{
				$response->addError($this->l('There are no records.'));
				return $response->display();
			}

			foreach ($results as $row) 
			{
				foreach (NewsletterProTools::unSerialize($row['emails_to_send']) as $email) 
					$emails_to_send[] = $email;

				foreach (NewsletterProTools::unSerialize($row['emails_sent']) as $email_result)
				{
					if ((bool)$email_result['status'] == false)
						$emails_sent_faild[] = $email_result['email'];
					else
						$emails_sent_success[] = $email_result['email'];
				}
			}

			if ($left_list)
				$emails = array_merge($emails, $emails_to_send);

			if ($right_list_undelivered)
				$emails = array_merge($emails, $emails_sent_faild);

			if (empty($emails))
			{
				$response->addError($this->l('Cannot find any emails addresses.'));
				return $response->display();
			}
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	private function getHistoryId($id_history)
	{
		return (int)Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_tpl_history`
			FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history`
			WHERE `id_newsletter_pro_tpl_history` = '.(int)$id_history.'
		');
	}

	public function subscribe($email, $id_lang = null, $id_shop = null)
	{
		$errors = array();
		$error_message = $this->l('An error occurred at the subscription.');

		$id_lang = isset($id_lang) ? (int)$id_lang : (int)$this->context->language->id;
		$id_shop = isset($id_shop) ? (int)$id_shop : (int)$this->context->shop->id;

		if ($info = $this->getUserTableByEmail($email))
		{
			foreach ($info as $value)
			{
				if (!Db::getInstance()->update($value['table'], array(
					$value['newsletter'] => 1,
				), '`'.$value['email'].'`= "'.pSQL($email).'"'))
					$errors[] = $error_message;
			}
		}
		else
		{
			// this part will not check for duplicate emails, that check is made on the upper rows
			if ((bool)$this->getConfiguration('SUBSCRIPTION_ACTIVE'))
			{
				$subscriber = new NewsletterProSubscribers();
				$subscriber->id_shop                    = (int)$id_shop;
				$subscriber->email                      = pSQL($email);
				$subscriber->date_add                   = date('Y-m-d H:i:s');
				$subscriber->ip_registration_newsletter = (string)Tools::getRemoteAddr();
				$subscriber->active                     = 1;

				if (!$subscriber->add())
					$errors = array_merge($errors, $subscriber->getErrors());
			}
			else
			{
				if (NewsletterProTools::blockNewsletterExists())
				{
					if (!Db::getInstance()->insert('newsletter', array(
						'id_shop'                    => (int)$id_shop,
						'email'                      => pSQL($email),
						'newsletter_date_add'        => date('Y-m-d H:i:s'),
						'ip_registration_newsletter' => (string)Tools::getRemoteAddr(),
						'active'                     => 1 )))
						$errors[] = $error_message;
				}
				else
				{
					if (!Db::getInstance()->insert('newsletter_pro_email', array(
						'id_shop'                    => (int)$id_shop,
						'id_lang'                    => (int)$id_lang,
						'email'                      => pSQL($email),
						'date_add'                   => date('Y-m-d H:i:s'),
						'ip_registration_newsletter' => (string)Tools::getRemoteAddr(),
						'active'                     => 1 )))
						$errors[] = $error_message;
				}
			}
		}

		// if the emails is in the forward list will be deleted because the alreay subscribed
		Db::getInstance()->delete('newsletter_pro_forward', '`to` = "'.pSQL($email).'"');

		return $errors;
	}

	private function getUserTableByEmail($email)
	{
		$definition = array(
			'customer'                   => array('email' => 'email', 'newsletter' => 'newsletter'),
			'newsletter'                 => array('email' => 'email', 'newsletter' => 'active'),
			'newsletter_pro_email'       => array('email' => 'email', 'newsletter' => 'active'),
			'newsletter_pro_subscribers' => array('email' => 'email', 'newsletter' => 'active'),
		);

		$info = array();
		foreach ($definition as $table => $fields)
		{
			$sql = 'SELECT COUNT(*) FROM `'._DB_PREFIX_.$table.'` WHERE `'.$fields['email'].'` = "'.pSQL($email).'"';
			if (Db::getInstance()->getValue($sql))
				$info[] = array(
						'table'      => $table,
						'email'      => $fields['email'],
						'newsletter' => $fields['newsletter']
						);
		}

		return !empty($info) ? $info : false;
	}

	public function syncNewsletters($id = null, $limit = null, $get_last_id = false)
	{
		return NewsletterProSendNewsletter::newInstance()->sync($id, $limit, $get_last_id);
	}

	public function startSendNewsletters($trigger)
	{
		if ($trigger)
			NewsletterProSendConnection::clearAll();

		return NewsletterProSendNewsletter::newInstance()->send();
	}

	public function continueSendNewsletters($trigger)
	{
		$continue = $trigger ? true : false;

		return NewsletterProSendNewsletter::newInstance()->send($continue);
	}

	public function stopSendNewsletters()
	{
		return NewsletterProSendNewsletter::newInstance()->stop();
	}

	public function pauseSendNewsletters()
	{
		return NewsletterProSendNewsletter::newInstance()->pause();
	}

	public function dateMonths($id_lang = null)
	{

		if (isset($id_lang)) {
			$language = new Language($id_lang);

			if (Validate::isLoadedObject($language)) {
				if ($language->iso_code == 'fr') {
					return array(
						'01' => 'Janvier',
						'02' => 'Février',
						'03' => 'Mars',
						'04' => 'Avril',
						'05' => 'Mai',
						'06' => 'Juin',
						'07' => 'Juillet',
						'08' => 'Août',
						'09' => 'Septembre',
						'10' => 'Octobre',
						'11' => 'Novembre',
						'12' => 'Décembre',
					);
				}
			}
		}

		return array(
			'01' => $this->l('January'),
			'02' => $this->l('February'),
			'03' => $this->l('March'),
			'04' => $this->l('April'),
			'05' => $this->l('May'),
			'06' => $this->l('June'),
			'07' => $this->l('July'),
			'08' => $this->l('August'),
			'09' => $this->l('September'),
			'10' => $this->l('October'),
			'11' => $this->l('November'),
			'12' => $this->l('December')
		);
	}

	public function jsUpdateConfiguration($name, $value)
	{
		$response = NewsletterProAjaxResponse::newInstance(array());
		$error = sprintf($this->l('Cannot update the configuration "%s".'), $name);

		if (!$name)
		{
			$response->addError($error);
			return $response->display();
		}

		if (!$this->updateConfiguration($name, $value))
			$response->addError($error);

		return $response->display();
	}

	public function updateTopShortcuts($name, $value)
	{
		$response = NewsletterProAjaxResponse::newInstance(array());

		$page_header_toolbar = $this->getConfiguration('PAGE_HEADER_TOOLBAR');
		$name = Tools::strtoupper($name);

		if (!isset($page_header_toolbar[$name]))
			$response->addError(sprintf($this->l('The configuration key %s does not exists.'), $name));
		else
		{
			$page_header_toolbar[$name] = $value;
			if (!$this->updateConfiguration('PAGE_HEADER_TOOLBAR', $page_header_toolbar))
				$response->addError($this->l('An error occurred.'));
		}

		$response->set('page_header_toolbar', $page_header_toolbar);

		return $response->display();
	}

	public function clearSendHistoryDetails()
	{
		$response = NewsletterProAjaxResponse::newInstance(array());

		try
		{
			$ids = Db::getInstance()->executeS('
				SELECT ss.`id_newsletter_pro_send_step` 
				FROM `'._DB_PREFIX_.'newsletter_pro_send` s
				LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_send_step` ss
					ON (s.`id_newsletter_pro_send` = ss.`id_newsletter_pro_send`)
				WHERE `active` = 0
			');

			if ($ids)
			{
				foreach ($ids as $row) 
				{
					$id = (int)$row['id_newsletter_pro_send_step'];
					$send_step = NewsletterProSendStep::newInstance($id);
					if (Validate::isLoadedObject($send_step))
					{
						$send_step->updateFields(array(
							'emails_to_send' => null,
							'emails_sent' => null,
							'error_msg' => null,
						));
					}
				}
			}
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	public function clearTaskHistoryDetails()
	{
		$response = NewsletterProAjaxResponse::newInstance(array());

		try
		{
			$ids = Db::getInstance()->executeS('
				SELECT ts.`id_newsletter_pro_task_step`
				FROM `'._DB_PREFIX_.'newsletter_pro_task` t
				LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_task_step` ts
					ON (t.`id_newsletter_pro_task` = ts.`id_newsletter_pro_task`)
				WHERE `active` = 0
			');

			if ($ids)
			{
				foreach ($ids as $row) 
				{
					$id = (int)$row['id_newsletter_pro_task_step'];
					$send_step = NewsletterProTaskStep::newInstance($id);
					if (Validate::isLoadedObject($send_step))
					{
						$send_step->updateFields(array(
							'emails_to_send' => null,
							'emails_sent' => null,
						));
					}
				}
			}
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	public function replaceEmbedCallback($matches)
	{
		$path = $matches[3];

		if (preg_match('/data-embed=("|\')0\1/', $matches[0]))
			return $matches[0];

		$swift_image = NewsletterPro_Swift_Image::fromPath($path);

		return $matches[1].$this->embed_images_message->embed($swift_image).$matches[4];
	}

	public function embedImages($embed_images_message, $template)
	{
		$this->embed_images_message = $embed_images_message;

		// embed images
		// You can embed files from a URL if allow_url_fopen is on in php.ini
		if (NewsletterPro::getConfiguration('SEND_EMBEDED_IMAGES'))
			$template = preg_replace_callback('/(<img.*src=("|\'))(http.*?)(\2[^>]+>)/', array($this, 'replaceEmbedCallback'), $template);

		return $template;
	}

	public function addFilterSelection($name, $filters)
	{
		$response = NewsletterProAjaxResponse::newInstance(array());

		try
		{
			$filter = NewsletterProFiltersSelection::newInstance();
			$filter->name = trim($name);

			if (empty($filters))
			{
				$response->addError($this->l('There are no filters selected.'));
				return $response->display();
			}

			$filter->value = Tools::jsonEncode($filters);

			if (empty($filter->name))
				$response->addError($this->l('The filename cannot be empty.'));
			else if ($filter->nameExists())
				$response->addError(sprintf($this->l('The filter name already exists, please use a different name.'), $name));
			else
			{
				if (!$filter->save())
					$response->addError($this->l('An error occurred.'));
			}
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		$response->set('filters', NewsletterProFiltersSelection::getFilters());

		return $response->display();
	}

	public function deleteFilterSelection($id)
	{
		$response = NewsletterProAjaxResponse::newInstance(array());

		try
		{
			if ((int)$id == 0)
			{
				$this->addError($this->l('You must to select a filter.'));
				return $response->display();
			}

			$filter = NewsletterProFiltersSelection::newInstance((int)$id);
			if (!Validate::isLoadedObject($filter))
			{
				$this->addError($this->l('The filter does not exists anymore.'));
				return $response->display();
			}

			if (!$filter->delete())
				$this->addError($this->l('An error occurred.'));
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	public function getFilterSelectionById($id)
	{
		$response = NewsletterProAjaxResponse::newInstance();

		try
		{
			if ((int)$id == 0)
			{
				$this->addError($this->l('You must to select a filter.'));
				return $response->display();
			}

			$filter = NewsletterProFiltersSelection::newInstance((int)$id);
			$response->set('value', Tools::jsonDecode($filter->value));
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	public function getExportOptions($value)
	{
		$response = NewsletterProAjaxResponse::newInstance();

		try
		{
			switch ($value) 
			{
				case self::LIST_CUSTOMERS:
					$columns = NewsletterProTools::getTableColumns('customer');
					$response->set('columns', $columns);
					break;

				case self::LIST_VISITORS:

					if (!NewsletterProTools::tableExists('newsletter'))
						throw new Exception(sprintf($this->l('The table "%s" does not exists.'), _DB_PREFIX_.'newsletter'));

					$columns = NewsletterProTools::getTableColumns('newsletter');
					$response->set('columns', $columns);
					break;
				case self::LIST_VISITORS_NP:
					$columns = NewsletterProTools::getTableColumns('newsletter_pro_subscribers');
					$response->set('columns', $columns);
					break;
				case self::LIST_ADDED:
					$columns = NewsletterProTools::getTableColumns('newsletter_pro_email');
					$response->set('columns', $columns);
					break;

				default:
					throw new Exception($this->l('Invalid list id.'));
					break;
			}
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	public function openLogFIle($href)
	{
		$response = NewsletterProAjaxResponse::newInstance(array(
			'content' => ''
		));

		try
		{
			$basename = pathinfo($href, PATHINFO_BASENAME);

			$filename = $this->dir_location.'logs/'.$basename;

			if (!file_exists($filename))
				throw new Exception(sprintf($this->l('The filename %s does not exists.'), $basename));
			
			$content = Tools::file_get_contents($filename);
			$response->set('content', $content);
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	public static function getSearchConstions()
	{
		$module = NewsletterPro::getInstance();

		return array(
			self::SEARCH_CONDITION_CONTAINS => $module->l('contains'),
			self::SEARCH_CONDITION_IS       => $module->l('is equal'),
			self::SEARCH_CONDITION_IS_NOT   => $module->l('is not equal'),
			self::SEARCH_CONDITION_GREATER  => $module->l('is equal or grater than'),
			self::SEARCH_CONDITION_LESS     => $module->l('is equal or less than'),
		);
	}

	public static function getSearchConstionsJs()
	{
		return array(
			'SEARCH_CONDITION_CONTAINS' => self::SEARCH_CONDITION_CONTAINS,
			'SEARCH_CONDITION_IS'       => self::SEARCH_CONDITION_IS,
			'SEARCH_CONDITION_IS_NOT'   => self::SEARCH_CONDITION_IS_NOT,
			'SEARCH_CONDITION_GREATER'  => self::SEARCH_CONDITION_GREATER,
			'SEARCH_CONDITION_LESS'     => self::SEARCH_CONDITION_LESS,
		);
	}

	public function getMaxTotalSpent()
	{
		return ceil((float)Db::getInstance()->getValue('
			SELECT MAX(o.`total_paid_real` / o.`conversion_rate`) as total_spent
			FROM `'._DB_PREFIX_.'orders` o 
			LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer = o.id_customer)
			WHERE 1
		'));
	}

	private function getCountriesSql($search = null)
	{
		return '
			SELECT c.`id_country`, cl.`name`, c.`iso_code`, c.`active`
			FROM `'._DB_PREFIX_.'country` c
			INNER JOIN `'._DB_PREFIX_.'country_lang` cl 
				ON (c.id_country = cl.id_country)
			AND cl.`id_lang` = '.(int)$this->context->language->id.'
			'.(isset($search) ? ' AND (cl.`name` LIKE "%'.pSQL($search).'%" OR c.`iso_code` LIKE "%'.pSQL($search).'%")' : '').'
			'.((int)pqnp_ini_config('filter_only_active_countries') ? ' AND c.`active` = 1 ' : '').'
			ORDER BY cl.`name` ASC
		';
	}

	public function getCountries()
	{
		return Tools::jsonEncode(Db::getInstance()->executeS($this->getCountriesSql()));
	}

	public function searchCountries($value)
	{
		return Tools::jsonEncode(Db::getInstance()->executeS($this->getCountriesSql($value)));
	}

	public function deleteProductTemplate($path)
	{
		$response = NewsletterProAjaxResponse::newInstance();

		if ((int)pqnp_ini_config('demo_mode'))
		{
			$demo_return = NewsletterProDemoMode::deleteProductTemplate($path);

			if ($demo_return)
				return $demo_return;
		}

		try
		{
			if (file_exists($path))
			{
				if (!unlink($path))
					throw new Exception($this->l('The template cannot be delete. Please checked the CHMOD permissions.'));
					
			}
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}
		return $response->display();
	}
}
?>