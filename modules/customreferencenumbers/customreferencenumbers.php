<?php
/**
 * Module allow to set custom number of your invoices.
 * 
 * @author    IT Present <cvikenzi@gmail.com>
 * @copyright 2015 IT Present
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_'))
	exit;

class CustomReferenceNumbers extends Module
{
	public function __construct()
	{
		$this->name = 'customreferencenumbers';
		$this->tab = 'billing_invoicing';
		$this->version = '1.4.0';
		$this->author = 'ITPresent';
		$this->module_key = '40efdba7e3e61e6dc9038027df690e61';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => (version_compare(_PS_VERSION_, '1.6', '<') ? '1.6' : _PS_VERSION_ ));
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Custom Reference Numbers for orders, invoices, delivery slips and credit slips');
		$this->description = $this->l('Module allow to set custom reference numbers for orders, invoices, delivery slips and credit slips.');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
		
		$this->overrides = array(
			0=>array('source'=>_PS_MODULE_DIR_.$this->name.'/override/classes/order/Order.php',
				'target'=>_PS_OVERRIDE_DIR_.'classes/order/Order.php',
				'targetdir'=>_PS_OVERRIDE_DIR_.'classes/'),
			1=>array('source'=>_PS_MODULE_DIR_.$this->name.'/override/classes/order/OrderInvoice.php',
				'target'=>_PS_OVERRIDE_DIR_.'classes/order/OrderInvoice.php',
				'targetdir'=>_PS_OVERRIDE_DIR_.'classes/'),
			2=>array('source'=>_PS_MODULE_DIR_.$this->name.'/override/classes/order/OrderSlip.php',
				'target'=>_PS_OVERRIDE_DIR_.'classes/order/OrderSlip.php',
				'targetdir'=>_PS_OVERRIDE_DIR_.'classes/'),
			3=>array('source'=>_PS_MODULE_DIR_.$this->name.'/override/classes/order/OrderPayment.php',
				'target'=>_PS_OVERRIDE_DIR_.'classes/order/OrderPayment.php',
				'targetdir'=>_PS_OVERRIDE_DIR_.'classes/'),
			4=>array('source'=>_PS_MODULE_DIR_.$this->name.'/override/classes/pdf/HTMLTemplateInvoice.php',
				'target'=>_PS_OVERRIDE_DIR_.'classes/pdf/HTMLTemplateInvoice.php',
				'targetdir'=>_PS_OVERRIDE_DIR_.'classes/'),
			5=>array('source'=>_PS_MODULE_DIR_.$this->name.'/override/classes/pdf/HTMLTemplateDeliverySlip.php',
				'target'=>_PS_OVERRIDE_DIR_.'classes/pdf/HTMLTemplateDeliverySlip.php',
				'targetdir'=>_PS_OVERRIDE_DIR_.'classes/'),
			6=>array('source'=>_PS_MODULE_DIR_.$this->name.'/override/classes/pdf/HTMLTemplateOrderSlip.php',
				'target'=>_PS_OVERRIDE_DIR_.'classes/pdf/HTMLTemplateOrderSlip.php',
				'targetdir'=>_PS_OVERRIDE_DIR_.'classes/')
		);
		if (!Configuration::get($this->name))      
			$this->warning = $this->l('No name provided');
	}
	public function install()
	{
		if (file_exists(_PS_ROOT_DIR_.'/cache/class_index.php'))
			unlink(_PS_ROOT_DIR_.'/cache/class_index.php');

		if (!method_exists($this, 'installOverrides') && !method_exists($this, 'uninstallOverrides'))
		{
			//installOverrides	
			try{
				$this->installMyOverrides();
			}catch(Exception $e){
				$this->_errors[] = sprintf(Tools::displayError('Unable to install override: %s'), $e->getMessage());
				$this->uninstallMyOverrides();
				return false;
			}
		}

		// Install tables
		try{
			$this->alterTable(true);
		}catch(Exception $e){
			$this->_errors[] = sprintf(Tools::displayError('Unable to alter tables: %s'), $e->getMessage());
			return false;
		}
		
		$customReferenceNumberData = $this->initCustomReferenceNumberData();
		$counterNumberData = $this->initCounterNumberData();
		if (!parent::install() || !$this->registerHook('backOfficeHeader') || !Configuration::updateGlobalValue($this->name, Tools::jsonEncode($customReferenceNumberData)) || !Configuration::updateGlobalValue('counterNumberData', Tools::jsonEncode($counterNumberData)))
			return false;

		return true;
	}
	public function uninstall()
	{
		if (file_exists(_PS_ROOT_DIR_.'/cache/class_index.php'))
			unlink(_PS_ROOT_DIR_.'/cache/class_index.php');

		if (!method_exists($this, 'uninstallOverrides'))
		{
			//installOverrides
			try{
				$this->uninstallMyOverrides();
			}catch(Exception $e){
				$this->_errors[] = sprintf(Tools::displayError('Unable to uninstall override: %s'), $e->getMessage());
				return false;
			}
		}

		// Uninstall tables
		try{
			$this->alterTable(false);
		}catch (Exception $e) {
			$this->_errors[] = sprintf(Tools::displayError('Unable to alter tables: %s'), $e->getMessage());
			return false;
		}
		return (parent::uninstall() && $this->unregisterHook('backOfficeHeader') && Configuration::deleteByName($this->name) && Configuration::deleteByName('counterNumberData'));
	}
	
	/**
	 * Install overrides files for the module
	 *
	 * @return bool
	 */
	public function installMyOverrides()
	{
		foreach ($this->overrides as $override)
		{
			if (file_exists($override['target']) && filesize($override['target']) > 86)
			{
				if (crc32(Tools::file_get_contents($override['target'])) != crc32(Tools::file_get_contents($override['source'])))
					throw new Exception(Tools::displayError('The property '.$override['target'].' is already defined.'));
			}
			if (!is_writable($override['targetdir']))
				throw new Exception(Tools::displayError('directory '.$override['target'].' not writable'));
			else
			{
				if (version_compare(_PS_VERSION_, '1.6', '<'))
				{
					$fp = fopen($override['target'], 'w');
					if (!$fp)
						throw new Exception(Tools::displayError('Can not copy '.$override['source'].' to '.$override['target']));
					fclose($fp);
					if (!rename($override['source'], $override['target']))
						throw new Exception(Tools::displayError('Can not copy '.$override['source'].' to '.$override['target']));
				}
				else
				{
					if (!Tools::copy($override['source'], $override['target']))
						throw new Exception(Tools::displayError('Can not copy '.$override['source'].' to '.$override['target']));
				}
			}
		} 
		if (file_exists(_PS_ROOT_DIR_.'/cache/class_index.php'))
			unlink(_PS_ROOT_DIR_.'/cache/class_index.php');
	}

	/**
	 * uninstall overrides files for the module
	 *
	 * @return bool
	 */
	public function uninstallMyOverrides()
	{
		foreach ($this->overrides as $override)
		{
			if (file_exists($override['target']))
			{
				$fp = fopen($override['source'], 'w');
				if (!$fp)
					throw new Exception(Tools::displayError('Can not copy '.$override['source'].' to '.$override['target']));
				fclose($fp);
				if (!rename($override['target'], $override['source']))
					throw new Exception(Tools::displayError('Can not copy '.$override['source'].' to '.$override['target']));
			}
		} 
		if (file_exists(_PS_ROOT_DIR_.'/cache/class_index.php'))
			unlink(_PS_ROOT_DIR_.'/cache/class_index.php');
	}
	
	/**
	 * Install module tables with exisiting
	 *
	 * @return bool
	 */
	public function alterTable($isInstall = false)
	{	
		if ($isInstall)
		{
			if ((int)Db::getInstance()->NumRows(Db::getInstance()->execute('SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = "'._DB_NAME_.'" AND TABLE_NAME = "'._DB_PREFIX_.'orders" AND COLUMN_NAME = "crn_invoice_number"')) == 0)
				Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'orders` ADD crn_invoice_number VARCHAR(255)');
			if ((int)Db::getInstance()->NumRows(Db::getInstance()->execute('SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = "'._DB_NAME_.'" AND TABLE_NAME = "'._DB_PREFIX_.'orders" AND COLUMN_NAME = "crn_delivery_number"')) == 0)
				Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'orders` ADD crn_delivery_number VARCHAR(255)');
			if ((int)Db::getInstance()->NumRows(Db::getInstance()->execute('SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = "'._DB_NAME_.'" AND TABLE_NAME = "'._DB_PREFIX_.'order_slip" AND COLUMN_NAME = "crn_order_slip_number"')) == 0)
				Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'order_slip` ADD crn_order_slip_number VARCHAR(255)');
			Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'orders` MODIFY COLUMN reference VARCHAR(255)');
			Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'order_payment` MODIFY COLUMN order_reference VARCHAR(255)');
		}
	}
	public function getContent()
	{
		Order::checkForCounterReset(0);
		Order::checkForCounterReset(1);
		Order::checkForCounterReset(2);
		Order::checkForCounterReset(3);
		$getAllCounterData = Tools::jsonDecode(Configuration::getGlobalValue('counterNumberData'), true);
		if (Tools::isSubmit('submitCustomReferenceNumber'))
		{
			$settingsUpdated = '';
			$this->storeReferenceNumbers();
			$settingsUpdated .= $this->displayConfirmation($this->l('Settings updated'));
			return $settingsUpdated.$this->displayList();
		}
		elseif (Tools::isSubmit('updatecustomreferencenumbers'))
		{
			$output = $this->getFormJavascript();
			$howToUse = $this->getHowToUse();
			return $this->displayForm().$output.$howToUse;
		}
		elseif (Tools::isSubmit('statuscustomreferencenumbers'))
		{
			$settingsUpdated = '';
			$this->setReferenceNumberStatus();
			$settingsUpdated .= $this->displayConfirmation($this->l('Settings updated'));
			return $settingsUpdated.$this->displayList();
		}
		else
			return $this->displayList();		
	}
	protected function displayList()
	{
		$this->fields_list = array();
		$this->fields_list['id_reference_number'] = array(
				'title' => $this->l('#'),
				'type' => 'hidden',
				'search' => false,
				'orderby' => false,
			);
		$this->fields_list['reference_number_name'] = array(
				'title' => $this->l('Reference number type'),
				'type' => 'text',
				'search' => false,
				'orderby' => false
			);

		if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP)
			$this->fields_list['reference_number_name'] = array(
					'title' => $this->l('Reference number of'),
					'type' => 'text',
					'search' => false,
					'orderby' => false
				);

		$this->fields_list['reference_number_format'] = array(
				'title' => $this->l('Reference number format'),
				'type' => 'text',
				'search' => false,
				'orderby' => false
			);
		$this->fields_list['activate_reference_number'] = array(
				'title' => $this->l('Status'),
				'active' => 'status',
				'type' => 'bool',
				'filter' => false,
				'search' => false
			);
			

		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = false;
		$helper->identifier = 'id_reference_number';
		$helper->actions = array('edit');
		$helper->show_toolbar = false;
		$helper->title = $this->displayName;
		$helper->table = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		return $helper->generateList(Tools::jsonDecode(Configuration::get($this->name), true), $this->fields_list);
	}
	public function displayForm()
	{
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$helper = new Helper();
		$selectRandomLength = array(
			array(
			'id_option' => '1',
			'name' => 1           
			),
			array(
			'id_option' => '2',
			'name' => 2
			),
			array(
			'id_option' => '3',
			'name' => 3
			),
			array(
			'id_option' => '4',
			'name' => 4
			),
			array(
			'id_option' => '5',
			'name' => 5
			),
			array(
			'id_option' => '6',
			'name' => 6
			),
			array(
			'id_option' => '7',
			'name' => 7
			),
			array(
			'id_option' => '8',
			'name' => 8
			),
			array(
			'id_option' => '9',
			'name' => 9
			),
		);
		$selectCounterLength = array(
			array(
			'id_option' => '2',
			'name' => 2
			),
			array(
			'id_option' => '3',
			'name' => 3
			),
			array(
			'id_option' => '4',
			'name' => 4
			),
			array(
			'id_option' => '5',
			'name' => 5
			),
			array(
			'id_option' => '6',
			'name' => 6
			),
			array(
			'id_option' => '7',
			'name' => 7
			),
			array(
			'id_option' => '8',
			'name' => 8
			),
			array(
			'id_option' => '9',
			'name' => 9
			)
		);
		$fields_form = array();
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Settings'),
			),
			'input' => array(
				array(
					'type'=>'radio',
					'label'     => $this->l('Activate this reference number:'),
					'name'      => 'activate_reference_number',
					'required'  => false,
					'class'     => 'activateReferenceNumber',
					'is_bool'   => true,
					'values'    => array(
						array(
							'id'    => 'activate_reference_number_on',
							'value' => true,   
							'label' => $this->l('Active')
						),
						array(
							'id'    => 'activate_reference_number_off',
							'value' => false,
							'label' => $this->l('Inactive')
						)
					),
				),
				array(
					'type'     => 'text',
					'label'    => $this->l('Reference number format'),
					'name'     => 'reference_number_format',
					'class'    => 'referenceNumberFormat',
					'required' => false,
					'desc'     => $this->l('Enter the format of the reference number (e.g. : INV-{COUNTER}/{WEEK}). All available tags are display in "DESCRIPTION OF THE AVAILABLE TAGS" table.')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Random number length:'),
					'desc' => $this->l('Length of random number reference'),
					'name' => 'random_number_length',
					'required' => false,
					'options' => array(
						'query' => $selectRandomLength,
						'id' => 'id_option',
						'name' => 'name'
					)
				),
				array(
					'type' => 'select',
					'label' => $this->l('Random alphabetic length:'),
					'desc' => $this->l('Length of random alphabetic reference'),
					'name' => 'random_alphabetic_length',
					'required' => false,
					'options' => array(
						'query' => $selectRandomLength,
						'id' => 'id_option',
						'name' => 'name'
					)
				),
				array(
					'type' => 'select',
					'label' => $this->l('Random alphanumeric length:'),
					'desc' => $this->l('Length of random alphanumeric reference'),
					'name' => 'random_alphanumeric_length',
					'required' => false,
					'options' => array(
						'query' => $selectRandomLength,
						'id' => 'id_option',
						'name' => 'name'
					)
				),
				array(
					'type' => 'text',
					'label' => $this->l('Counter number start at:'),
					'desc' => $this->l('Starting value of counter reference. Each time you set this value to another, counter will be reset to retype value.'),
					'class' => 'counterStartAt',
					'name' => 'counter_start_at',
					'required' => false
				),
				array(
					'type' => 'text',
					'label' => $this->l('Counter step by:'),
					'desc' => $this->l('This field represent counter value rising. For example if you starting value is 10 and step by value is 5, the counter will generate this reference: 10, 15, 20, 25, ...'),
					'class' => 'CounterIncrementBy',
					'name' => 'counter_increment_by',
					'required' => false
				),
				array(
					'type'=>'radio',
					'label'     => $this->l('Intervals of reseting counter:'),
					'desc'      => $this->l('You can set how often will reset your counter automatically. Everytime after reset, counter value will set to number from "Counter number start at" field. If you choose 3rd or 4th option (reached selected number or reach selected date), system will automatically display field for entering the number or entering the date.'),
					'name'      => 'counter_reset_interval',
					'required'  => false,
					'class'     => 'ResetCounterInterval',
					'values'    => array(
						array(
							'id'    => 'counter_reset_interval_week',
							'value' => 0,   
							'label' => $this->l('Each week')
						),
						array(
							'id'    => 'counter_reset_interval_month',
							'value' => 1,
							'label' => $this->l('Each month')
						),
						array(
							'id'    => 'counter_reset_interval_year',
							'value' => 2,
							'label' => $this->l('Each year')
						),
						array(
							'id'    => 'counter_reset_interval_number',
							'value' => 3,
							'label' => $this->l('If counter value will reach selected number')
						),
						array(
							'id'    => 'counter_reset_interval_date',
							'value' => 4,
							'label' => $this->l('If date will reach selected date')
						),
						array(
							'id'    => 'counter_reset_interval_never',
							'value' => 5,
							'label' => $this->l('Never')
						)
					)
				),
				array(
					'type' => 'text',
					'label' => $this->l('Reset counter if value will reach number:'),
					'desc' => $this->l('If counter will reach bigger number as you set in this field, counter value will automatically reset to value from "Counter number start at" field.'),
					'class' => 'CounterResetNumber',
					'name' => 'counter_reset_number',
					'required' => false
				),
				array(
					'type' => 'text',
					'label' => $this->l('Reset counter if date will reach date:'),
					'desc' => $this->l('If current date will reach this date, or later date as you set in this field, counter value will automatically reset to value from "Counter number start at" field. Format of date is "YYYY-MM-DD" (YEAR-MONTH-DAY).'),
					'class' => 'CounterResetDate',
					'name' => 'counter_reset_date',
					'required' => false
				),
				array(
					'type'=>'radio',
					'label'     => $this->l('Reset counter now:'),
					'desc'      => $this->l('You can reset actual counter value to value, which is set in "Counter number start at" field.'),
					'name'      => 'counter_reset_now',
					'required'  => false,
					'class'     => 'CounterResetNow',
					'is_bool'   => true,
					'values'    => array(
						array(
							'id'    => 'counter_reset_now_on',
							'value' => 1,   
							'label' => $this->l('Yes')
						),
						array(
							'id'    => 'counter_reset_now_off',
							'value' => 0,
							'label' => $this->l('No')
						)
					)
				),
				array(
					'type'=>'radio',
					'label'     => $this->l('Set counter reference length:'),
					'desc'      => $this->l('If you want to get static length of counter value you can set counter reference length. If you set this field to "active", system will display options to set counter reference length. For example if you set this value to "5" and your current counter value will be "10", final counter value will look like this: 00010. For current counter value = "100", it will be 00100. For current counter value = "1000", it will be 01000. After save, you can see live preview on the top in "next counter value" info field.'),
					'name'      => 'counter_reference_length_in_use',
					'required'  => false,
					'class'     => 'CounterReferenceLength',
					'is_bool'   => true,
					'values'    => array(
						array(
							'id'    => 'counter_reference_length_in_use_on',
							'value' => 1,   
							'label' => $this->l('Active')
						),
						array(
							'id'    => 'counter_reference_length_in_use_off',
							'value' => 0,
							'label' => $this->l('Inactive')
						)
					)
				),
				array(
					'type' => 'select',
					'label' => $this->l('Counter length:'),
					'desc' => $this->l('You can set length of final counter reference. If counter value length will be shorter as counter length, system automatically add zeros as prefix of counter value.'),
					'name' => 'counter_reference_length',
					'required' => false,
					'options' => array(
						'query' => $selectCounterLength,
						'id' => 'id_option',
						'name' => 'name'
					)
				),
				array(
					'type' => 'hidden',
					'class' => 'counterActualValue',
					'name' => 'counter_actual_value',
					'required' => false
				),
				array(
					'type' => 'hidden',
					'class' => 'idReferenceNumber',
					'name' => 'id_reference_number',
					'required' => true
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'submitCustomReferenceNumber',
				'name' => 'submitCustomReferenceNumber',
			)
		);
		$id_reference_number = (int)Tools::getValue('id_reference_number');
		$referenceNumbersData = Tools::jsonDecode(Configuration::get($this->name), true);
		$getAllCounterData = Tools::jsonDecode(Configuration::getGlobalValue('counterNumberData'), true);
		$helper = new HelperForm();
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		$helper->title = $this->displayName;
		$helper->show_toolbar = false;
		$helper->toolbar_scroll = false;
		$helper->submit_action = 'submit'.$this->name;
		$helper->tpl_vars = array(
			'fields_value' => array_merge($referenceNumbersData[$id_reference_number - 1], $getAllCounterData[$id_reference_number - 1]),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);
		$helper->toolbar_btn = array(
			'save' => array(
				'desc' => $this->l('Save'),
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
				'&token='.Tools::getAdminTokenLite('AdminModules'),
			),
			'back' => array(
				'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Back to list')
			)
		);
		return $helper->generateForm($fields_form);
	}
	public function storeReferenceNumbers()
	{
		$customReferenceNumbersArray = array();
		$counterNumbersArray = array();
		$id_reference_number = (int)Tools::getValue('id_reference_number');
		$getAllReferenceNumberData = Tools::jsonDecode(Configuration::get($this->name), true);
		$getAllCounterData = Tools::jsonDecode(Configuration::getGlobalValue('counterNumberData'), true);
		$currentCustomOrderReferenceArray = $getAllReferenceNumberData[$id_reference_number - 1];
		$currentCounterData = $getAllCounterData[$id_reference_number - 1];
		$customReferenceNumbersArray['id_reference_number'] = $currentCustomOrderReferenceArray['id_reference_number'];
		$customReferenceNumbersArray['activate_reference_number'] = Tools::getValue('activate_reference_number');
		$customReferenceNumbersArray['reference_number_name'] = $currentCustomOrderReferenceArray['reference_number_name'];
		$customReferenceNumbersArray['reference_number_format'] = Tools::getValue('reference_number_format');
		$customReferenceNumbersArray['random_number_length'] = (int)Tools::getValue('random_number_length');
		$customReferenceNumbersArray['random_alphabetic_length'] = (int)Tools::getValue('random_alphabetic_length');
		$customReferenceNumbersArray['random_alphanumeric_length'] = (int)Tools::getValue('random_alphanumeric_length');
		if ((int)Tools::getValue('counter_reset_now') || (int)Tools::getValue('counter_start_at') != $currentCounterData['counter_start_at'])
			$counterNumbersArray['counter_actual_value'] = (int)Tools::getValue('counter_start_at');
		else
			$counterNumbersArray['counter_actual_value'] = (int)$currentCounterData['counter_actual_value'];
		$counterNumbersArray['counter_reset_now'] = '0';
		$counterNumbersArray['counter_start_at'] = (int)Tools::getValue('counter_start_at');
		$counterNumbersArray['counter_increment_by'] = (int)Tools::getValue('counter_increment_by');
		$counterNumbersArray['counter_reset_interval'] = (int)Tools::getValue('counter_reset_interval');
		$counterNumbersArray['counter_reset_number'] = (int)Tools::getValue('counter_reset_number');
		$counterNumbersArray['counter_reset_date'] = Tools::getValue('counter_reset_date');
		$counterNumbersArray['counter_reference_length_in_use'] = (int)Tools::getValue('counter_reference_length_in_use');
		$counterNumbersArray['counter_reference_length'] = (int)Tools::getValue('counter_reference_length');
		$counterNumbersArray['counter_last_set_date'] = $currentCounterData['counter_last_set_date'];
		$getAllReferenceNumberData[$id_reference_number - 1] = $customReferenceNumbersArray;
		$getAllCounterData[$id_reference_number - 1] = $counterNumbersArray;
		Configuration::updateValue($this->name, Tools::jsonEncode($getAllReferenceNumberData));
		Configuration::updateGlobalValue('counterNumberData', Tools::jsonEncode($getAllCounterData));
	}
	public function setReferenceNumberStatus()
	{
		$id_reference_number = (int)Tools::getValue('id_reference_number');
		$getAllReferenceNumberData = Tools::jsonDecode(Configuration::get($this->name), true);
		if ($getAllReferenceNumberData[$id_reference_number - 1]['activate_reference_number'])
			$getAllReferenceNumberData[$id_reference_number - 1]['activate_reference_number'] = false;
		else
			$getAllReferenceNumberData[$id_reference_number - 1]['activate_reference_number'] = true;
		Configuration::updateValue($this->name, Tools::jsonEncode($getAllReferenceNumberData));
	}
	public function initCustomReferenceNumberData()
	{
		$customReferenceNumbersArray = array();
		$customReferenceNumbersArray[0] = array(
			'id_reference_number' => 1,
			'activate_reference_number' => true,
			'reference_number_name' => 'Orders',
			'reference_number_format' => 'ORD-{RANDOM_ALPHABETIC}',
			'random_number_length' => '9',
			'random_alphabetic_length' => '6',
			'random_alphanumeric_length' => '6'
		);
		$customReferenceNumbersArray[1] = array(
			'id_reference_number' => 2,
			'activate_reference_number' => true,
			'reference_number_name' => 'Invoices',
			'reference_number_format' => 'INV-{RANDOM_ALPHABETIC}',
			'random_number_length' => '9',
			'random_alphabetic_length' => '6',
			'random_alphanumeric_length' => '6'
		);
		$customReferenceNumbersArray[2] = array(
			'id_reference_number' => 3,
			'activate_reference_number' => true,
			'reference_number_name' => 'Delivery slips',
			'reference_number_format' => 'DEL-{RANDOM_ALPHABETIC}',
			'random_number_length' => '9',
			'random_alphabetic_length' => '6',
			'random_alphanumeric_length' => '6'
		);
		$customReferenceNumbersArray[3] = array(
			'id_reference_number' => 4,
			'activate_reference_number' => true,
			'reference_number_name' => 'Credit slips',
			'reference_number_format' => 'SLIP-{RANDOM_ALPHABETIC}',
			'random_number_length' => '9',
			'random_alphabetic_length' => '6',
			'random_alphanumeric_length' => '6'
		);
		return $customReferenceNumbersArray;
	}
	public function initCounterNumberData()
	{
		$counterNumberData = array();
		$counterNumberData[0] = array(
			'counter_start_at' => '',
			'counter_increment_by' => '',
			'counter_actual_value' => '',
			'counter_reset_interval' => '5',
			'counter_reset_number' => '',
			'counter_reset_date' => '',
			'counter_reset_now' => '0',
			'counter_reference_length_in_use' => '0',
			'counter_reference_length' => '4',
			'counter_last_set_date' => date("Y-m-d")
		);
		$counterNumberData[1] = array(
			'counter_start_at' => '',
			'counter_increment_by' => '',
			'counter_actual_value' => '',
			'counter_reset_interval' => '5',
			'counter_reset_number' => '',
			'counter_reset_date' => '',
			'counter_reset_now' => '0',
			'counter_reference_length_in_use' => '0',
			'counter_reference_length' => '4',
			'counter_last_set_date' => date("Y-m-d")
		);
		$counterNumberData[2] = array(
			'counter_start_at' => '',
			'counter_increment_by' => '',
			'counter_actual_value' => '',
			'counter_reset_interval' => '5',
			'counter_reset_number' => '',
			'counter_reset_date' => '',
			'counter_reset_now' => '0',
			'counter_reference_length_in_use' => '0',
			'counter_reference_length' => '4',
			'counter_last_set_date' => date("Y-m-d")
		);
		$counterNumberData[3] = array(
			'counter_start_at' => '',
			'counter_increment_by' => '',
			'counter_actual_value' => '',
			'counter_reset_interval' => '5',
			'counter_reset_number' => '',
			'counter_reset_date' => '',
			'counter_reset_now' => '0',
			'counter_reference_length_in_use' => '0',
			'counter_reference_length' => '4',
			'counter_last_set_date' => date("Y-m-d")
		);
		return $counterNumberData;
	}
	public function getFormJavascript()
	{
		$id_reference_number = (int)Tools::getValue('id_reference_number');
		$getAllCounterData = Tools::jsonDecode(Configuration::getGlobalValue('counterNumberData'), true);
		$currentCounterData = $getAllCounterData[$id_reference_number - 1];
		$output = '<script type="text/javascript">
			var counterActualValue = '.(int)$currentCounterData['counter_actual_value'].';
			var numberReferenceFormatError = "'.$this->l('Counter number start at field or Counter step by field can not be empty and have to be a positive number').'";
			var counterResetNumberError = "'.$this->l('Reached number can not be empty and have to be a positive number').'";
			var counterResetDateError = "'.$this->l('Reached date can not be empty and date have to start from tomorrow').'";
			var numberReferenceFormatEmptyError = "'.$this->l('Reference number format field can not be empty').'";
			</script>';
		if (version_compare(_PS_VERSION_, '1.6.0.2', '<'))
		{
			$output .= '<script type="text/javascript" src="../js/jquery/ui/jquery.ui.core.min.js"></script>';
			$output .= '<link rel="stylesheet" type="text/css" href="../js/jquery/ui/themes/base/jquery.ui.all.css">';
		}
		$output .= '<script type="text/javascript" src="../js/jquery/ui/jquery.ui.datepicker.min.js"></script>';
		if (version_compare(_PS_VERSION_, '1.6', '<'))
		{
			$output .= '<script type="text/javascript" src="'.$this->_path.'views/js/admin15.js"></script>';
		}
		else
		{
			if(version_compare(_PS_VERSION_, '1.6.0.6', '<'))
				$output .= '<script type="text/javascript" src="'.$this->_path.'views/js/adminLT1605.js"></script>';
			else
				$output .= '<script type="text/javascript" src="'.$this->_path.'views/js/admin16.js"></script>';
		}
		$output .= '<style>';
		if (version_compare(_PS_VERSION_, '1.6', '<'))
		{
				$output .= 'input[type="radio"]{float:left}';
				$output .= 'label.activateReferenceNumber, label.CounterResetNow, label.CounterReferenceLength{width: 23px;margin-top: -2px;margin-right: 7px;}';
		}
		$output .= '.howToUse{border-collapse: collapse;width: 100%;margin-bottom:13px;}';
		$output .= '.howToUse th{height: 50px}';
		$output .= '.howToUse th, .tagName{text-align: center;}';
		$output .= '.howToUse td{height: 50px;vertical-align: bottom;padding: 15px;}';
		$output .= '.howToUse, .howToUse th, .howToUse td{border: 1px solid black;}';
		$output .= '</style>';
		return $output;
	}
	public function getHowToUse()
	{
		$howToUse = '<div style="width:100%;text-align:center;margin-bottom:13px;"><strong>'.$this->l('DESCRIPTION OF THE AVAILABLE TAGS').':</strong></div>';
		$howToUse .= '<table class="howToUse">';
		$howToUse .= '<tr><th>Tag name</th><th>'.$this->l('Tag description').'</th></tr>';
		$howToUse .= '<tr><td class="tagName">{RANDOM_NUMBER}</td><td>'.$this->l('This tag generate random number. If you use this tag, system automaticaly display field "Random number length" to select length of random number.').'</td></tr>';
		$howToUse .= '<tr><td class="tagName">{RANDOM_ALPHABETIC}</td><td>'.$this->l('This tag generate alphabetic reference. If you use this tag, system automaticaly display field "Random alphabetic length" to select length of random alphabetic reference.').'</td></tr>';
		$howToUse .= '<tr><td class="tagName">{RANDOM_ALPHANUMERIC}</td><td>'.$this->l('This tag generate alphanumeric (numbers and alphabetical characters together) reference. If you use this tag, system automaticaly display field "Random alphanumeric length" to select length of random alphanumeric reference.').'</td></tr>';
		$howToUse .= '<tr><td class="tagName">{COUNTER}</td><td>'.$this->l('This tag generated automatically counted values. You can choose starting value of counter and a step, which represent counter value rising. You can reset counter every time you want when you set "Counter number start at" field to another value.').'</td></tr>';
		$howToUse .= '<tr><td class="tagName">{ORDER_ID}</td><td>'.$this->l('This tag generate order ID number.').'</td></tr>';
		$howToUse .= '<tr><td class="tagName">{SHOP_ID}</td><td>'.$this->l('This tag generate shop ID number.').'</td></tr>';
		$howToUse .= '<tr><td class="tagName">{YEAR}</td><td>'.$this->l('This tag generate actual year number.').'</td></tr>';
		$howToUse .= '<tr><td class="tagName">{YEARSHORTCUT}</td><td>'.$this->l('This tag generate shortcut of actual year, for example 15,16,17.').'</td></tr>';
		$howToUse .= '<tr><td class="tagName">{MONTH}</td><td>'.$this->l('This tag generate actual month number.').'</td></tr>';
		$howToUse .= '<tr><td class="tagName">{WEEK}</td><td>'.$this->l('This tag generate actual week number.').'</td></tr>';
		$howToUse .= '<tr><td class="tagName">{DAYINWEEK}</td><td>'.$this->l('This tag generate actual day in week number.').'</td></tr>';
		$howToUse .= '<tr><td class="tagName">{DAY}</td><td>'.$this->l('This tag generate actual day in month number.').'</td></tr>';
		$howToUse .= '</table>';
		return $howToUse; 
	}
	public function hookBackOfficeHeader()
	{
		$js = '';
		$customNumberReferenceData = Tools::jsonDecode(Configuration::get('customreferencenumbers'));
		$customDeliverySlipReferenceData = $customNumberReferenceData[2];
		if ($customDeliverySlipReferenceData->activate_reference_number && Tools::getValue('id_order'))
		{
			$js .= '<script type="text/javascript">
					$(document).ready(function(){
						if($("#documents_table").length > 0){
							deliveryNumber = "";
							$( "#documents_table tr" ).each(function( index ) {
								if (typeof $(this).attr("id") !== typeof undefined && $(this).attr("id") !== false) {
									if($(this).attr("id").indexOf("delivery_") >= 0){
										deliveryNumber = $(this).attr("id").substr(9);
									}
								}
							});
							if(deliveryNumber != ""){
								$("#documents_table #delivery_" + deliveryNumber + " td").eq(2).find("a").html("'.Order::getCMRDeliverySlipByOrderID(Tools::getValue('id_order')).'");
							}
						}
					});
				</script>';
		}
		$customOrderSlipReferenceData = $customNumberReferenceData[3];
		if ($customOrderSlipReferenceData->activate_reference_number && Tools::getValue('id_order'))
		{
			$orderSlips = OrderSlip::getCMROrderSlipsByOrderID(Tools::getValue('id_order'));
			if (count($orderSlips))
			{
				$js .= '<script type="text/javascript">
						$(document).ready(function(){';
						foreach ($orderSlips as $orderSlip)
							$js .= '$("#documents_table #orderslip_" + '.(int)$orderSlip['id_order_slip'].' + " td").eq(2).find("a").html("'.$orderSlip['crn_order_slip_number'].'");';
				$js .= '});
						</script>';
			}
		}
		return $js;
	}
}