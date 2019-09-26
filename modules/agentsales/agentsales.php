<?php

if (!defined('_PS_VERSION_'))
    exit;

class Agentsales extends Module
{
    const DISCOUNT_TYPE_PERCENT = 1;
    const DISCOUNT_TYPE_AMOUNT = 2;
    
    protected $config_form = false;
    
    public function __construct()
    {
        $this->name = 'agentsales';
        $this->tab = 'advertising_marketing';
        $this->version = '0.1.0';
        $this->author = 'NSWEB';
        $this->need_instance = 0;
        $this->bootstrap = true;
    
        //$this->controllers = array('commisions');
        
        parent::__construct();
    
        $this->displayName = $this->l('Agents sales');
        $this->description = $this->l('Koehlert agents sales');
    
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');
        
    }

    public function getAgentsList()
    {
        /**
         * 
         * @var DbQueryCore $query
         */
        $query = new DbQuery();
        $query->select('id_customer, company, firstname, lastname, email');
        $query->from('customer');
        $query->where('id_default_group = '. (int)Configuration::get('AGENTSALES_AGENT_GROUP') );
        $query->orderBy('lastname');
    }
    
    // admin customer view
    public function hookDisplayAdminCustomers($params)
    {
        
        return $this->context->smarty->fetch($this->local_path.'views/templates/admin/customer.tpl');
    }
    
    public function hookActionAdminCustomersFormModifier($params)
    {
        $agentsCustomersGroup = (int)Configuration::get('AGENTSALES_AGENT_GROUP');

        $id_customer = (int)Tools::getValue('id_customer');
        $customer = new Customer($id_customer);
        
        if((int)$customer->id_default_group != $agentsCustomersGroup){
            return;
        }
        
        $params['fields'][] = array(
            'form' => array(
                'legend' => array(
                    'title' => 'Agents commision'
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'radio',
                        //'prefix' => '%',
                        'required' => true,
                        'name' => 'agentsales_commision_type',
                        'label' => $this->l('Agent\'s commision type'),
                        'values' => array(
                            array(
                                'id' => 'agentsales_commision_type_1',
                                'value' => self::DISCOUNT_TYPE_PERCENT,
                                'label' => $this->l('Percent') .' (%)'
                            ),
                            array(
                                'id' => 'agentsales_commision_type_2',
                                'value' => self::DISCOUNT_TYPE_AMOUNT,
                                'label' => $this->l('Amount')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'required' => true,
                        'name' => 'agentsales_commision_value',
                        'label' => $this->l('Agent\'s commision value'),
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            )
        );
        
        
        $params['fields_value']['agentsales_commision_type'] = $customer->agentsales_commision_type;
        $params['fields_value']['agentsales_commision_value'] = $customer->agentsales_commision_value;
    }
    /*
    public function hookActionObjectCustomerUpdateBefore(&$params)
    {
        if(Tools::isSubmit('agent_commision')){
            $agent_commision = (int)Tools::getValue('agent_commision');
            $params['object']->agent_commision = $agent_commision;
        }
    }
    */
    
    /*
    public function getOrdersByVoucher($id_cart_rule = null, $id_voucher = null)
    {
        if( is_null($id_cart_rule) && !empty($id_voucher) ){
            $agentCurrentVoucherInfo = Db::getInstance()->getRow('
                SELECT * FROM `'._DB_PREFIX_.'agentcomm_agent_voucher`
                WHERE `id_agent_voucher` = '. $id_voucher .'
            ');
            $id_cart_rule = $agentCurrentVoucherInfo['id_voucher'];
        }
        else{
            $agentCurrentVoucherInfo = Db::getInstance()->getRow('
                SELECT * FROM `'._DB_PREFIX_.'agentcomm_agent_voucher`
                WHERE `id_voucher` = '. $id_cart_rule .'
            ');
        }
        
        $agentsCommision = (float)Configuration::get('AGENTCOMM_VCHR_COMM');
        $query = '
            SELECT 
        		a.id_currency, a.id_order, a.reference,
        		CONCAT(LEFT(c.`firstname`, 1), ". ", c.`lastname`) AS `customer`,
        		osl.`name` AS `osname`, a.valid,
                a.total_paid_tax_excl AS total, 
                a.total_discounts_tax_excl AS total_discounts, 
                a.total_products AS total_products,
        		ocr.value AS cart_rule_value, ocr.value_tax_excl AS cart_rule_value_tax_excl 
            FROM `'._DB_PREFIX_.'orders` a
            '. Shop::addSqlAssociation('orders', 'o'). '
    		LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
    		LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`current_state`)
    		LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$this->context->language->id.')
    		INNER JOIN `'._DB_PREFIX_.'order_cart_rule` ocr ON (ocr.id_order = a.`id_order` AND ocr.`id_cart_rule` = '. ((int)$id_cart_rule) .')
    		WHERE
    		    a.`valid` = 1
        ';
        $orders = Db::getInstance()->executeS($query);
        
        if( $agentCurrentVoucherInfo['agent_commision'] > 0 ){
            $agentsCommisionType = $agentCurrentVoucherInfo['agent_commision_type'];
            $agentsCommision = $agentCurrentVoucherInfo['agent_commision'];
        }
        
        $ordersTotal = 0;
        $commisionsTotal = 0;
        foreach($orders as $oi => $orderData){
            $orders[$oi]['total_products'] = round($orderData['total_products'], 2);
            $ordersTotal += ($orderData['total_products'] - $orderData['total_discounts']);
            $orders[$oi]['total_discounts'] = round($orderData['total_discounts'], 2);
            
            if( $agentsCommisionType == self::DISCOUNT_TYPE_PERCENT ){
                $orders[$oi]['order_commision'] = round(
                    ($orderData['total_products'] - $orderData['total_discounts']) / 100 * $agentsCommision, 2);
            }
            else{
                $orders[$oi]['order_commision'] = round($agentsCommision, 2);
            }
            $commisionsTotal += $orders[$oi]['order_commision'];
        }
        
        return array(
            'orders_list' => $orders,
            'orders_products_total' => $ordersTotal,
            //'agent_commision' => $agentsCommision,
            'current_voucher_info' => $agentCurrentVoucherInfo,
            'commision_total' => $commisionsTotal
        );
    }
    */
    
    
    public function getContent()
    {
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
            $this->_postProcess();
        }
        
        $this->context->smarty->assign('module_dir', $this->_path);
    
        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
    
        return $output.$this->renderForm();
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
        $helper->submit_action = 'submitAgentcommModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
    
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
    
        return $helper->generateForm(array($this->getConfigForm()));
    }
    
    protected function getConfigForm()
    {
        $groups = Group::getGroups($this->context->language->id);
        
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'radio',
                        //'prefix' => '%',
                        'required' => true,
                        //'desc' => $this->l(''),
                        'name' => 'AGENTSALES_COMM_TYPE',
                        'label' => $this->l('Agent\'s commision type'),
                        'values' => array(
                            array(
                                'id' => 'AGENTSALES_COMM_TYPE_1',
                                'value' => self::DISCOUNT_TYPE_PERCENT,
                                'label' => $this->l('Percent') .' (%)'
                            ),
                            array(
                                'id' => 'AGENTSALES_COMM_TYPE_2',
                                'value' => self::DISCOUNT_TYPE_AMOUNT,
                                'label' => $this->l('Amount')
                            )
                        ),
                    
                    ),
                    
                    array(
                        'col' => 3,
                        'type' => 'text',
                        //'prefix' => '%',
                        'required' => true,
                        //'desc' => $this->l('Agent\'s commision value'),
                        'name' => 'AGENTSALES_COMM_VALUE',
                        'label' => $this->l('Agent\'s commision value'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Agents customers group'),
                        'name' => 'AGENTSALES_AGENT_GROUP',
                        'multiple' => false,
                        'required' => true,
                        'options' => array(
                            'query' => $groups,
                            'id' => 'id_group',
                            'name' => 'name'
                        )
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }
    
    protected function getConfigFormValues()
    {
        return array(
            'AGENTSALES_COMM_TYPE' => Configuration::get('AGENTSALES_COMM_TYPE'),
            'AGENTSALES_COMM_VALUE' => Configuration::get('AGENTSALES_COMM_VALUE'),
            'AGENTSALES_AGENT_GROUP' => Configuration::get('AGENTSALES_AGENT_GROUP'),
        );
    }
    
    protected function _postProcess()
    {
        $form_values = $this->getConfigFormValues();
    
        foreach (array_keys($form_values) as $key)
            Configuration::updateValue($key, Tools::getValue($key));
    }
    
    public function hookActionAdminControllerSetMedia()
    {
        
        //$this->context->controller->addCSS($this->_path.'css/back.css');
    }
    
    public function hookDisplayAdminOrderRight($params)
    {
        $agents = array();
        
        $agents = $this->getAgentsList();
        
        $this->context->smarty->assign(array(
            'agents' => $agents
        ));
        
        $this->context->controller->addJS($this->_path.'js/back.js');
    
        return $this->context->smarty->fetch($this->local_path.'views/templates/admin/display_admin_order_right.tpl');
    }
    
    public function install()
    {
        $hookData = Db::getInstance()->ExecuteS('
        	SELECT * FROM `' . _DB_PREFIX_ . 'hook` WHERE `name` = "actionAdminCustomersFormModifier"
        ');
        if( empty($hookData) ){
            $hook = new Hook();
            $hook->name = 'actionAdminCustomersFormModifier';
            $hook->title = 'actionAdminCustomersFormModifier';
            $hook->position = 1;
            $hook->add();
        }

        $hookData = Db::getInstance()->ExecuteS('
        	SELECT * FROM `' . _DB_PREFIX_ . 'hook` WHERE `name` = "actionObjectCustomerUpdateBefore"
        ');
        if( empty($hookData) ){
            $hook = new Hook();
            $hook->name = 'actionObjectCustomerUpdateBefore';
            $hook->title = 'actionObjectCustomerUpdateBefore';
            $hook->position = 1;
            $hook->add();
        }
        
        $hookData = Db::getInstance()->ExecuteS('
        	SELECT * FROM `' . _DB_PREFIX_ . 'hook` WHERE `name` = "displayAdminOrderRight"
        ');
        if( empty($hookData) ){
            $hook = new Hook();
            $hook->name = 'displayAdminOrderRight';
            $hook->title = 'displayAdminOrderRight';
            $hook->position = 1;
            $hook->add();
        }
        
        
        if( !parent::install()
			|| !$this->registerHook('adminCustomers')
			|| !$this->registerHook('actionAdminControllerSetMedia')
            || !$this->registerHook('actionAdminCustomersFormModifier')
            || !$this->registerHook('actionObjectCustomerUpdateBefore')
            || !$this->registerHook('displayAdminOrderRight')
        ){
            return false;
        }
        
        $tableCreateQuery =
        'CREATE TABLE `'._DB_PREFIX_.'agentsales_order` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_agent` int(11) NOT NULL,
            `id_order` int(11) NOT NULL,
            `commision_type` int(11) NOT NULL DEFAULT "0",
            `commision_value` int(11) NOT NULL DEFAULT "0",
            `paidout` int(1),
            PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
        
        if (Db::getInstance()->execute($tableCreateQuery) == false){
            $this->_errors[] = Db::getInstance()->getMsgError();
            return false;
        }
        
        $customerFieldQuery = '
            ALTER TABLE `'._DB_PREFIX_.'customer`
                ADD `agentsales_commision_type` int(11) NOT NULL DEFAULT "0",
                ADD `agentsales_commision_value` int(11) NOT NULL DEFAULT "0"
        ';
        if (Db::getInstance()->execute($customerFieldQuery) == false){
            $this->_errors[] = Db::getInstance()->getMsgError();
            return false;
        }
        Configuration::updateValue('AGENTSALES_COMM_TYPE', 1);
        Configuration::updateValue('AGENTSALES_COMM_VALUE', 5);
        Configuration::updateValue('AGENTSALES_AGENT_GROUP', 0);
        
        $tab = new Tab ();
        $tab->class_name = 'AdminAgentsalesOrders';
        $tab->module = $this->name;
        $tab->id_parent = (int)Tab::getIdFromClassName ( 'AdminOrders' );
        foreach (Language::getLanguages () as $lang){
            $tab->name[(int)$lang['id_lang']] = 'Agents sales';
        }
        if (! $tab->save ()){
            $this->_errors[] = $this->l('Tab "Agents sales" install error');
            return false;
        }
        return true;
    }
    
    public function uninstall()
    {
        
        $tabId = (int) Tab::getIdFromClassName('AdminAgentsalesOrders');
        $tab = new Tab($tabId);
        $tab->delete();
        
        
        Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'agentsales_order`');
        Db::getInstance()->query('ALTER TABLE `'._DB_PREFIX_.'customer` DROP `agentsales_commision_type`');
        Db::getInstance()->query('ALTER TABLE `'._DB_PREFIX_.'customer` DROP `agentsales_commision_value`');
        return parent::uninstall();
    }
}
