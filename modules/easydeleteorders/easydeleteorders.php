<?php
/**
* Easy Delete Orders
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate.com <info@idnovate.com>
*  @copyright 2016 idnovate.com
*  @license   See above
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class EasyDeleteOrders extends Module
{
    public function __construct()
    {
        $this->name = 'easydeleteorders';
        $this->tab = 'administration';
        $this->version = '1.2.0';
        $this->author = 'idnovate';
        $this->need_instance = 0;
        $this->module_key = '6a8de15b5ffaa54d4cb54e478e0daf17';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Easy Delete Orders');
        $this->description = $this->l('Delete orders safe and easily.');
        $this->confirmUninstall = $this->l('Are you sure you want to delete the module and the related data?');

        /* Backward compatibility */
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');
            $this->local_path = _PS_MODULE_DIR_.$this->name.'/';
        }
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->registerHook('backOfficeHeader') ||
            (version_compare(_PS_VERSION_, '1.5', '>=') && !$this->registerHook('actionObjectOrderDeleteAfter')) ||
            !Configuration::updateValue('EASYDELETEORDERS_ENABLED', '0') ||
            !Configuration::updateValue('EASYDELETEORDERS_DELETECARTS', '0') ||
            !Configuration::updateValue('EASYDELETEORDERS_DELETEINVOICES', '0') ||
            !Configuration::updateValue('EASYDELETEORDERS_PROFILES', '0')) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (parent::uninstall() &&
            Configuration::deleteByName('EASYDELETEORDERS_ENABLED') &&
            Configuration::deleteByName('EASYDELETEORDERS_DELETECARTS') &&
            Configuration::deleteByName('EASYDELETEORDERS_DELETEINVOICES') &&
            Configuration::deleteByName('EASYDELETEORDERS_PROFILES')) {
            return true;
        }

        return false;
    }

    public function getContent()
    {
        $html = '';

        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitEasyDeleteOrdersModule')) == true) {
            $html .= $this->postProcess();
        }

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            return $html.$this->renderForm14();
        } else {
            return $html.$this->renderForm();
        }
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitEasyDeleteOrdersModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function renderForm14()
    {
        $html = '';

        $helper = new Helper();

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => Language::getLanguages(false),
            'id_language' => $this->context->language->id,
            'THEME_LANG_DIR' => _PS_IMG_.'l/'
        );

        $html .= $helper->generateForm(array($this->getConfigForm()));

        return $html;
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $profiles = Profile::getProfiles($this->context->language->id);
        foreach ($profiles as &$profile) {
            $profile['id'] = $profile['id_profile'];
        }

        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                 ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                        'name' => 'EASYDELETEORDERS_ENABLED',
                        'label' => $this->l('Enabled'),
                        'class' => 't',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                        'name' => 'EASYDELETEORDERS_DELETECARTS',
                        'label' => $this->l('Delete related carts'),
                        'class' => 't',
                        'values' => array(
                            array(
                                'id' => 'delete_carts_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'delete_carts_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                        'name' => 'EASYDELETEORDERS_DELETEINVOICES',
                        'label' => $this->l('Delete related invoices'),
                        'class' => 't',
                        'values' => array(
                            array(
                                'id' => 'delete_invoices_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'delete_invoices_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'label' => $this->l('Select profile/s with permissions to delete orders'),
                        'name' => 'EASYDELETEORDERS_PROFILES',
                        'values' => array(
                            'query' => $profiles,
                            'id' => 'id',
                            'name' => 'name',
                        ),
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'type' => 'submit',
                    'class' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? '' : 'button big',
                    'name' => 'submitEasyDeleteOrdersModule',
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array_merge(
            array(
                'EASYDELETEORDERS_ENABLED' => Configuration::get('EASYDELETEORDERS_ENABLED'),
                'EASYDELETEORDERS_DELETECARTS' => Configuration::get('EASYDELETEORDERS_DELETECARTS'),
                'EASYDELETEORDERS_DELETEINVOICES' => Configuration::get('EASYDELETEORDERS_DELETEINVOICES'),
                'EASYDELETEORDERS_PROFILES' => Configuration::get('EASYDELETEORDERS_PROFILES'),
            ),
            is_array(Tools::jsonDecode(Configuration::get('EASYDELETEORDERS_PROFILES'), true)) ? Tools::jsonDecode(Configuration::get('EASYDELETEORDERS_PROFILES'), true) : array()
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $html = '';
        $errors = array();

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $html .= $this->displayError($error);
            }
        } else {
            $form_values = $this->getConfigFormValues();

            foreach (array_keys($form_values) as $key) {
                if ($key != 'EASYDELETEORDERS_PROFILES') {
                    Configuration::updateValue($key, Tools::getValue($key));
                } else {
                    $profiles = Profile::getProfiles($this->context->language->id);

                    $fields = array();
                    foreach ($profiles as $profile) {
                        $key = 'EASYDELETEORDERS_PROFILES_'.$profile['id_profile'];
                        $fields[$key] = Tools::getValue($key);
                    }

                    $config_value = array();
                    foreach (array_keys($fields) as $key) {
                        $config_value[$key] = $fields[$key];
                    }

                    Configuration::updateValue('EASYDELETEORDERS_PROFILES', Tools::jsonEncode($config_value));
                }
            }

            $html .= $this->displayConfirmation($this->l('Configuration saved successfully.'));
        }

        return $html;
    }

    public function hookbackOfficeHeader()
    {
        if (Configuration::get('EASYDELETEORDERS_ENABLED') &&
            ((version_compare(_PS_VERSION_, '1.5', '>=') && Tools::strtolower(Dispatcher::getInstance()->getController()) == 'adminorders') ||
            (version_compare(_PS_VERSION_, '1.5', '<') && Tools::strtolower(Tools::getValue('tab')) == 'adminorders'))) {
            if (Configuration::get('EASYDELETEORDERS_ENABLED') == '1') {
            	$profilesEnabled = Tools::jsonDecode(Configuration::get('EASYDELETEORDERS_PROFILES'), true);
                if ($profilesEnabled['EASYDELETEORDERS_PROFILES_'.(int)Context::getContext()->employee->id_profile]) {
                    $this->context->smarty->assign(array(
                        'action' => $this->l('Delete'),
                        'action_delete' => $this->l('Delete this order'),
                        'action_delete_bulk' => $this->l('Delete selected orders'),
                        'confirm' => $this->l('Are you sure that you want to delete the order ~?'),
                        'action_delete_bulk_confirm' => $this->l('Are you sure that you want to delete the selected orders?'),
                        'admin_base_dir' => $this->currentPageURL(),
                        'this_path' => $this->_path,
                        'msg' => ''
                    ));
                    if (Tools::getValue('submitBulkdeleteorder') && !Tools::getValue('easydeleteorder')) {
                        if (Tools::getValue('orderBox')) {
                            $orders = Tools::getValue('orderBox');
                            $params = array();
                            foreach ($orders as $order) {
                                $params['object'] = new Order((int)$order);
                                $this->hookActionObjectOrderDeleteAfter($params);
                            }
                            $this->context->smarty->assign(array(
                                'msg' => preg_replace('/[\n|\r|\n\r]/i', '', $this->displayConfirmation($this->l('Order(s) deleted successfully.')))
                            ));
                        }
                    }
                    if (Tools::getValue('easydeleteorder') && !Tools::getValue('submitBulkdeleteorder')) {
                        if (Tools::getValue('id_order')) {
                            $params = array();
                            $params['object'] = new Order((int)Tools::getValue('id_order'));
                            $this->hookActionObjectOrderDeleteAfter($params);
                            $this->context->smarty->assign(array(
                                'msg' => preg_replace('/[\n|\r|\n\r]/i', '', $this->displayConfirmation(sprintf($this->l('Order %s deleted successfully.'), Tools::getValue('id_order'))))
                            ));
                            if (Tools::getValue('easydeleteorder') == '2') {
                                if (version_compare(_PS_VERSION_, '1.6', '<')) {
                                    return Tools::redirectAdmin('index.php?tab=AdminOrders&token=' . Tools::getAdminTokenLite('AdminOrders'));
                                } else {
                                    return Tools::redirectAdmin('index.php?controller=AdminOrders&token=' . Tools::getAdminTokenLite('AdminOrders'));
                                }
                            }
                        }
                    }
                    if (version_compare(_PS_VERSION_, '1.5', '<')) {
                        return $this->display(__FILE__, '/views/templates/hook/easydeleteorders_view.tpl');
                    } else {
                        return $this->display(__FILE__, 'easydeleteorders_view.tpl');
                    }
                }
            }
        }
    }

    public function hookActionObjectDeleteAfter($params)
    {
        $object = $params['object'];

        if ($object instanceof Order) {
            $this->hookActionObjectOrderDeleteAfter($params);
        }
    }

    public function hookActionObjectOrderDeleteAfter($params)
    {
        Db::getInstance()->Execute("SHOW TABLES LIKE '"._DB_PREFIX_."customer_thread'");
        $tableCustomerThreadExists = Db::getInstance()->NumRows() > 0 ? true : false;

        Db::getInstance()->Execute("SHOW TABLES LIKE '"._DB_PREFIX_."order_carrier'");
        $tableOrderCarrierExists = Db::getInstance()->NumRows() > 0 ? true : false;

        Db::getInstance()->Execute("SHOW TABLES LIKE '"._DB_PREFIX_."order_cart_rule'");
        $tableOrderCartRuleExists = Db::getInstance()->NumRows() > 0 ? true : false;

        Db::getInstance()->Execute("SHOW TABLES LIKE '"._DB_PREFIX_."order_detail'");
        $tableOrderDetailExists = Db::getInstance()->NumRows() > 0 ? true : false;

        Db::getInstance()->Execute("SHOW TABLES LIKE '"._DB_PREFIX_."order_history'");
        $tableOrderHistoryExists = Db::getInstance()->NumRows() > 0 ? true : false;

        if (Configuration::get('EASYDELETEORDERS_DELETEINVOICES') == '1') {
            Db::getInstance()->Execute("SHOW TABLES LIKE '"._DB_PREFIX_."order_invoice'");
            $tableOrderInvoiceExists = Db::getInstance()->NumRows() > 0 ? true : false;
    
            Db::getInstance()->Execute("SHOW TABLES LIKE '"._DB_PREFIX_."order_invoice_payment'");
            $tableOrderInvoicePaymentExists = Db::getInstance()->NumRows() > 0 ? true : false;
        } else {
            $tableOrderInvoiceExists = false;
            $tableOrderInvoicePaymentExists = false;
        }
        
        Db::getInstance()->Execute("SHOW TABLES LIKE '"._DB_PREFIX_."order_return'");
        $tableOrderReturnExists =  Db::getInstance()->NumRows() > 0 ? true : false;

        Db::getInstance()->Execute("SHOW TABLES LIKE '"._DB_PREFIX_."order_slip'");
        $tableOrderSlipExists = Db::getInstance()->NumRows() > 0 ? true : false;

        Db::getInstance()->Execute("SHOW TABLES LIKE '"._DB_PREFIX_."stock_mvt'");
        $tableStockMvtExists = Db::getInstance()->NumRows() > 0 ? true : false;

        if (Configuration::get('EASYDELETEORDERS_DELETECARTS') == '1') {
            Db::getInstance()->Execute("SHOW TABLES LIKE '"._DB_PREFIX_."cart'");
            $tableCartExists = Db::getInstance()->NumRows() > 0 ? true : false;
    
            Db::getInstance()->Execute("SHOW TABLES LIKE '"._DB_PREFIX_."cart_cart_rule'");
            $tableCartCartRuleExists = Db::getInstance()->NumRows() > 0 ? true : false;
    
            Db::getInstance()->Execute("SHOW TABLES LIKE '"._DB_PREFIX_."cart_product'");
            $tableCartProductExists = Db::getInstance()->NumRows() > 0 ? true : false;
        } else {
            $tableCartExists = false;
            $tableCartCartRuleExists = false;
            $tableCartProductExists = false;
        }
        
        Db::getInstance()->Execute("SHOW TABLES LIKE '"._DB_PREFIX_."customization'");
        $tableCustomizationExists = Db::getInstance()->NumRows() > 0 ? true : false;

        Db::getInstance()->Execute("SHOW TABLES LIKE '"._DB_PREFIX_."message'");
        $tableMessageExists = Db::getInstance()->NumRows() > 0 ? true : false;

        Db::getInstance()->Execute("SHOW TABLES LIKE '"._DB_PREFIX_."specific_price'");
        $tableSpecificPriceExists = Db::getInstance()->NumRows() > 0 ? true : false;

        $sql = "DELETE
                    `"._DB_PREFIX_."orders`
                    ".($tableCustomerThreadExists ? ",`"._DB_PREFIX_."customer_thread`" : "")."
                    ".($tableOrderCarrierExists ? ",`"._DB_PREFIX_."order_carrier`" : "")."
                    ".($tableOrderCartRuleExists ? ",`"._DB_PREFIX_."order_cart_rule`" : "")."
                    ".($tableOrderDetailExists ? ",`"._DB_PREFIX_."order_detail`" : "")."
                    ".($tableOrderHistoryExists ? ",`"._DB_PREFIX_."order_history`" : "")."
                    ".($tableOrderInvoiceExists ? ",`"._DB_PREFIX_."order_invoice`" : "")."
                    ".($tableOrderInvoicePaymentExists ? ",`"._DB_PREFIX_."order_invoice_payment`" : "")."
                    ".($tableOrderReturnExists ? ",`"._DB_PREFIX_."order_return`" : "")."
                    ".($tableOrderSlipExists ? ",`"._DB_PREFIX_."order_slip`" : "")."
                    ".($tableStockMvtExists ? ",`"._DB_PREFIX_."stock_mvt`" : "")."
                    ".($tableCartExists ? ",`"._DB_PREFIX_."cart`" : "")."
                    ".($tableCartCartRuleExists ? ",`"._DB_PREFIX_."cart_cart_rule`" : "")."
                    ".($tableCartProductExists ? ",`"._DB_PREFIX_."cart_product`" : "")."
                    ".($tableCustomizationExists ? ",`"._DB_PREFIX_."customization`" : "")."
                    ".($tableMessageExists ? ",`"._DB_PREFIX_."message`" : "")."
                    ".($tableSpecificPriceExists ? ",`"._DB_PREFIX_."specific_price`" : "")."
                FROM
                    `"._DB_PREFIX_."orders`
                    ".($tableCustomerThreadExists ? "LEFT JOIN `"._DB_PREFIX_."customer_thread` ON `"._DB_PREFIX_."orders`.`id_order` = `"._DB_PREFIX_."customer_thread`.`id_order`" : "")."
                    ".($tableOrderCarrierExists ? "LEFT JOIN `"._DB_PREFIX_."order_carrier` ON `"._DB_PREFIX_."orders`.`id_order` = `"._DB_PREFIX_."order_carrier`.`id_order`" : "")."
                    ".($tableOrderCartRuleExists ? "LEFT JOIN `"._DB_PREFIX_."order_cart_rule` ON `"._DB_PREFIX_."orders`.`id_order` = `"._DB_PREFIX_."order_cart_rule`.`id_order`" : "")."
                    ".($tableOrderDetailExists ? "LEFT JOIN `"._DB_PREFIX_."order_detail` ON `"._DB_PREFIX_."orders`.`id_order` = `"._DB_PREFIX_."order_detail`.`id_order`" : "")."
                    ".($tableOrderHistoryExists ? "LEFT JOIN `"._DB_PREFIX_."order_history` ON `"._DB_PREFIX_."orders`.`id_order` = `"._DB_PREFIX_."order_history`.`id_order`" : "")."
                    ".($tableOrderInvoiceExists ? "LEFT JOIN `"._DB_PREFIX_."order_invoice` ON `"._DB_PREFIX_."orders`.`id_order` = `"._DB_PREFIX_."order_invoice`.`id_order`" : "")."
                    ".($tableOrderInvoicePaymentExists ? "LEFT JOIN `"._DB_PREFIX_."order_invoice_payment` ON `"._DB_PREFIX_."orders`.`id_order` = `"._DB_PREFIX_."order_invoice_payment`.`id_order`" : "")."
                    ".($tableOrderReturnExists ? "LEFT JOIN `"._DB_PREFIX_."order_return` ON `"._DB_PREFIX_."orders`.`id_order` = `"._DB_PREFIX_."order_return`.`id_order`" : "")."
                    ".($tableOrderSlipExists ? "LEFT JOIN `"._DB_PREFIX_."order_slip` ON `"._DB_PREFIX_."orders`.`id_order` = `"._DB_PREFIX_."order_slip`.`id_order`" : "")."
                    ".($tableStockMvtExists ? "LEFT JOIN `"._DB_PREFIX_."stock_mvt` ON `"._DB_PREFIX_."orders`.`id_order` = `"._DB_PREFIX_."stock_mvt`.`id_order`" : "")."
                    ".($tableCartExists ? "LEFT JOIN `"._DB_PREFIX_."cart` ON `"._DB_PREFIX_."orders`.`id_cart` = `"._DB_PREFIX_."cart`.`id_cart`" : "")."
                    ".($tableCartCartRuleExists ? "LEFT JOIN `"._DB_PREFIX_."cart_cart_rule` ON `"._DB_PREFIX_."orders`.`id_cart` = `"._DB_PREFIX_."cart_cart_rule`.`id_cart`" : "")."
                    ".($tableCartProductExists ? "LEFT JOIN `"._DB_PREFIX_."cart_product` ON `"._DB_PREFIX_."orders`.`id_cart` = `"._DB_PREFIX_."cart_product`.`id_cart`" : "")."
                    ".($tableCustomizationExists ? "LEFT JOIN `"._DB_PREFIX_."customization` ON `"._DB_PREFIX_."orders`.`id_cart` = `"._DB_PREFIX_."customization`.`id_cart`" : "")."
                    ".($tableMessageExists ? "LEFT JOIN `"._DB_PREFIX_."message` ON `"._DB_PREFIX_."orders`.`id_cart` = `"._DB_PREFIX_."message`.`id_cart`" : "")."
                    ".($tableSpecificPriceExists ? "LEFT JOIN `"._DB_PREFIX_."specific_price` ON `"._DB_PREFIX_."orders`.`id_cart` = `"._DB_PREFIX_."specific_price`.`id_cart`" : "")."
                WHERE `"._DB_PREFIX_."orders`.`id_order` = ".(int)$params['object']->id;

        return Db::getInstance()->Execute($sql);
    }

    private function currentPageURL()
    {
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            {$pageURL = "https://";}
        } else {
            {$pageURL = "http://";}
        }

        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }

        return $pageURL;
    }
}
