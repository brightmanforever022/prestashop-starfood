<?php

if (!defined('_PS_VERSION_'))
    exit;

class Agentcomm extends Module
{
    const DISCOUNT_TYPE_PERCENT = 1;
    const DISCOUNT_TYPE_AMOUNT = 2;
    
    protected $config_form = false;
    
    public function __construct()
    {
        $this->name = 'agentcomm';
        $this->tab = 'advertising_marketing';
        $this->version = '0.6.0';
        $this->author = 'NSWEB';
        $this->need_instance = 0;
        $this->bootstrap = true;
    
        $this->controllers = array('commisions');
        
        parent::__construct();
    
        $this->displayName = $this->l('Agents commisions');
        $this->description = $this->l('Agents commisions');
    
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');
        
    }

    public function hookActionBeforeSubmitAccount($params)
    {
        $agentAutoParam = Configuration::get('AGENTCOMM_AGENT_AUTO_PARAM');
        $moveToAgentsGroup = false;
        
        if( !empty($this->context->cookie->agentcomm_reg_agnt) && $this->context->cookie->agentcomm_reg_agnt ){
            $moveToAgentsGroup = true;
        }
        
        if( !empty($agentAutoParam) && array_key_exists($agentAutoParam, $params['_POST']) ){
            $moveToAgentsGroup = true;
        }
        
        // Disable temporary registration email for this customers group
        if($moveToAgentsGroup){
            Configuration::updateValue('PS_CUSTOMER_CREATION_EMAIL', 0);
        }
    }
    public function hookActionCustomerAccountAdd($params)
    {
        // Revert back registration email
        Configuration::updateValue('PS_CUSTOMER_CREATION_EMAIL', 1);
        
        $agentAutoParam = Configuration::get('AGENTCOMM_AGENT_AUTO_PARAM');
        $moveToAgentsGroup = false;
        
        if( !empty($this->context->cookie->agentcomm_reg_agnt) && $this->context->cookie->agentcomm_reg_agnt ){
            unset($this->context->cookie->agentcomm_reg_agnt);
            $moveToAgentsGroup = true;
        }
        
        if( !empty($agentAutoParam) && array_key_exists($agentAutoParam, $params['_POST']) ){
            $moveToAgentsGroup = true;
        }
        
        if($moveToAgentsGroup){
            $agentsCustomersGroup = (int)Configuration::get('AGENTCOMM_AGENT_GROUP');
            
            if($params['newCustomer']->id_default_group == $agentsCustomersGroup){
                return;
            }
            
            $params['newCustomer']->id_default_group = $agentsCustomersGroup;
            $params['newCustomer']->groupBox[] = $agentsCustomersGroup;
            
            try{
                $params['newCustomer']->save();
                
                $this->startNewAgentVoucher($params['newCustomer']->id);
            }
            catch(Exception $e){
                PrestaShopLogger::addLog($e->getMessage());
            }
            
            if( !$this->context->controller->ajax ){
                $redirectUrl = $this->context->link->getModuleLink($this->name, 'commisions', array(
                    'action' => 'vouchers'
                ));
                Tools::redirect( $redirectUrl );
            }
        }
    }
    
    public function hookHeader($params)
    {
        $agentAutoParam = Configuration::get('AGENTCOMM_AGENT_AUTO_PARAM');
        if(
            empty($this->context->cookie->id_customer) 
            && strlen($_SERVER['QUERY_STRING'])
            && empty($this->context->cookie->agentcomm_reg_agnt)
            && !empty($agentAutoParam)
        ){
            $agentAutoParamQuoted = preg_quote($agentAutoParam, '#');
            if( preg_match('#'. $agentAutoParamQuoted .'#i', $_SERVER['QUERY_STRING']) ){
                $this->context->cookie->agentcomm_reg_agnt = 1;
            }
        }
    }
    
    public function hookCustomerAccount()
    {
        $agentsCustomersGroup = (int)Configuration::get('AGENTCOMM_AGENT_GROUP');
        
        if($this->context->customer->id_default_group != $agentsCustomersGroup){
            return;
        }
        
        return $this->context->smarty->fetch($this->getTemplatePath('views/templates/hook/my-account.tpl'));
        
    }
    
    public function hookDisplayAdminCustomers($params)
    {
        $agentsCustomersGroup = (int)Configuration::get('AGENTCOMM_AGENT_GROUP');
        $agentCurrentVoucher = null;
        
        $customer = new Customer($params['id_customer']);

        if((int)$customer->id_default_group != $agentsCustomersGroup){
            return;
        }

        $startVoucherFormUrl = Context::getContext()->link->getAdminLink('AdminAgentcommAgents');
        
        $this->context->smarty->assign(array(
            'agent_current_voucher' => $agentCurrentVoucher,
            'agents_controller_url' => $startVoucherFormUrl,
            'id_customer' => $customer->id
        ));
        
        $agentToVoucher = Db::getInstance()->getRow('
            SELECT * FROM `'._DB_PREFIX_.'agentcomm_agent_voucher`
            WHERE `id_agent` = '. $customer->id .'
                AND `status` = 1
        ');
        
        if( is_array($agentToVoucher) && !empty($agentToVoucher['id_agent_voucher'])){
            $agentCurrentVoucher = new CartRule($agentToVoucher['id_voucher']);
            
            $cartRuleAdminLink = $this->context->link->getAdminLink('AdminCartRules')
                .'&id_cart_rule='.$agentCurrentVoucher->id.'&updatecart_rule'
            ;
            
            $this->context->smarty->assign(array(
                'agent_current_voucher' => $agentCurrentVoucher,
                'cart_rule_admin_link' => $cartRuleAdminLink,
            ));
        }
        
        $agentToPastVoucher = Db::getInstance()->executeS('
            SELECT aav.*, cr.id_cart_rule, cr.code, cr.date_from, cr.date_to
            FROM `'._DB_PREFIX_.'agentcomm_agent_voucher` aav
            INNER JOIN `'._DB_PREFIX_.'cart_rule` cr ON aav.id_voucher = cr.id_cart_rule
            WHERE aav.`id_agent` = '. $customer->id .'
                AND aav.`status` = 0
        ');
        if( $agentToPastVoucher && is_array($agentToPastVoucher) && count($agentToPastVoucher) ){
            $this->context->smarty->assign(array(
                'vouchers_past' => $agentToPastVoucher
            ));
            
        }
        
        
        return $this->context->smarty->fetch($this->local_path.'views/templates/admin/customer.tpl');
    }
    
    public function hookActionAdminCustomersFormModifier($params)
    {
        $agentsCustomersGroup = (int)Configuration::get('AGENTCOMM_AGENT_GROUP');

        $id_customer = (int)Tools::getValue('id_customer');
        $customer = new Customer($id_customer);
        
        if((int)$customer->id_default_group != $agentsCustomersGroup){
            return;
        }
        
        $params['fields'][] = array(
            'form' => array(
                'legend' => array(
                    'title' => 'Agents commisions'
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'radio',
                        //'prefix' => '%',
                        'required' => true,
                        'desc' => $this->l('Agent\'s commision type'),
                        'name' => 'agent_commision_type',
                        'label' => $this->l('Custom agent commision type'),
                        'values' => array(
                            array(
                                'id' => 'agent_commision_type_1',
                                'value' => self::DISCOUNT_TYPE_PERCENT,
                                'label' => $this->l('Percent') .' (%)'
                            ),
                            array(
                                'id' => 'agent_commision_type_2',
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
                        'desc' => $this->l('Applied only to new vouchers'),
                        'name' => 'agent_commision',
                        'label' => $this->l('Custom agent commision value'),
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            )
        );
        
        
        $params['fields_value']['agent_commision'] = $customer->agent_commision;
        $params['fields_value']['agent_commision_type'] = $customer->agent_commision_type;
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
    
    public function startNewAgentVoucher($id_customer)
    {
        $agentsCustomersGroup = (int)Configuration::get('AGENTCOMM_AGENT_GROUP');
        $newVoucherValidityDays = (int)Configuration::get('AGENTCOMM_VCHR_DAYS');
        $newVoucherDiscountType = (float)Configuration::get('AGENTCOMM_VCHR_DSCNT_TYPE');
        $newVoucherDiscount = (float)Configuration::get('AGENTCOMM_VCHR_DSCNT');
        $agentCommisionType = (int)Configuration::get('AGENTCOMM_VCHR_COMM_TYPE');
        $agentCommision = (int)Configuration::get('AGENTCOMM_VCHR_COMM');
        
        $customer = new Customer((int)$id_customer);
        if($customer->id_default_group != $agentsCustomersGroup){
            return;
        }
        
        if( $customer->agent_commision > 0 ){
            $agentCommision = $customer->agent_commision;
            $agentCommisionType = $customer->agent_commision_type;
        }

        $agentCurrentVoucherInfo = Db::getInstance()->getRow('
            SELECT * FROM `'._DB_PREFIX_.'agentcomm_agent_voucher`
            WHERE `id_agent` = '. $customer->id .'
                AND `status` = 1
        ');
        if( is_array($agentCurrentVoucherInfo) && count($agentCurrentVoucherInfo) ){
            $currentVoucher = new CartRule($agentCurrentVoucherInfo['id_voucher']);
            $currentVoucher->date_to = date('Y-m-d H:i:s');
            $currentVoucher->active = 0;
            
            $currentVoucher->update();
            
            Db::getInstance()->update('agentcomm_agent_voucher', array('status' => 0), 
                'id_agent = '. $customer->id);
        }
        
        $voucherNew = new CartRule();
        $voucherNew->active = 1;
        foreach( Language::getLanguages(true) as $language ){
            $voucherNew->name[ $language['id_lang'] ] = 'Discount voucher';
        }
        $voucherNew->description =
            'Discount voucher for agent: '. $customer->firstname .' '. $customer->lastname;
        
        $voucherNew->date_from = date('Y-m-d H:i:s');
        $voucherNew->date_to = date('Y-m-d H:i:s', time() + ($newVoucherValidityDays * 86400) );
        $voucherNew->quantity = 9999999;
        $voucherNew->quantity_per_user = 9999999;
        $voucherNew->partial_use = 0;
        $voucherNew->reduction_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        
        if($newVoucherDiscountType == self::DISCOUNT_TYPE_PERCENT){
            $voucherNew->reduction_percent = $newVoucherDiscount;
        }
        else{
            $voucherNew->reduction_amount = $newVoucherDiscount;
        }
        
        $voucherCode = false;
        do{
            //$voucherCode = Tools::passwdGen(8);
            //$voucherCode = Tools::strtoupper($voucherCode);
            $voucherCode = 
                mb_strtoupper(trim($customer->firstname)) 
                . Tools::passwdGen(5, 'NUMERIC')
            ;

            $existenVoucher = (int)CartRule::getIdByCode($voucherCode);
            if( !empty($existenVoucher) && ($existenVoucher > 0) ){
                $voucherCode = false;
            }
        }
        while(!strlen($voucherCode));
        $voucherNew->code = $voucherCode;
        //$voucherNew->code = mb_strtoupper($customer->firstname) . date('Y');

        try{
            $voucherNew->save();
            
            Db::getInstance()->insert('agentcomm_agent_voucher', 
                array(
                    'id_agent' => $customer->id,
                    'id_voucher' => $voucherNew->id,
                    'status' => 1,
                    'agent_commision' => $agentCommision,
                    'agent_commision_type' => $agentCommisionType,
                )
            );
            
        }
        catch(Exception $e){
            PrestaShopLogger::addLog($e->getMessage());
            return false;
        }
        
        $templateVars = array(
            '{voucher_code}' => $voucherNew->code,
            '{voucher_date_from}' => $voucherNew->date_from,
            '{voucher_date_to}' => $voucherNew->date_to,
            '{name}' => Tools::safeOutput($customer->firstname .' '. $customer->lastname)
        );
        
        /* Email sending */
        if (!Mail::Send((int)$this->context->cookie->id_lang,
            'agentcomm_newvoucher',
            Mail::l('New voucher', (int)$this->context->cookie->id_lang),
            $templateVars, 
            $customer->email,
            $customer->firstname .' '. $customer->lastname,
            null,
            null,
            null,
            null,
            dirname(__FILE__).'/mails/')
        ){
            PrestaShopLogger::addLog('New voucher email does not sent');;
        }
        

        return true;
    }
    
    public function getContent()
    {
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
            $this->_postProcess();
        }
        
        $this->context->smarty->assign('module_dir', $this->_path);
    
        
        
        $affiliateParam = Configuration::get('AGENTCOMM_AGENT_AUTO_PARAM');
        $affiliateLinks = '<h3>'. $this->l('Affiliate links') .'</h3><ul>';
        foreach (Language::getLanguages () as $lang){
            $url = $this->context->link->getPageLink(null, null, $lang['id_lang']) .'?'. $affiliateParam;
            $affiliateLinks .= '<li>'. $lang['name'] .': '. $url .'</li>';
        }
        $affiliateLinks .= '</ul>';
        
        $this->context->smarty->assign('affiliate_links', $affiliateLinks);
        
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
                        'desc' => $this->l('Customer\'s discount type'),
                        'name' => 'AGENTCOMM_VCHR_DSCNT_TYPE',
                        'label' => $this->l('New voucher discount type'),
                        'values' => array(
                            array(
                                'id' => 'AGENTCOMM_VCHR_DSCNT_TYPE_1',
                                'value' => self::DISCOUNT_TYPE_PERCENT,
                                'label' => $this->l('Percent') .' (%)'
                            ),
                            array(
                                'id' => 'AGENTCOMM_VCHR_DSCNT_TYPE_2',
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
                        'desc' => $this->l('Customer\'s discount value'),
                        'name' => 'AGENTCOMM_VCHR_DSCNT',
                        'label' => $this->l('New voucher discount value'),
                    ),
                    
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => $this->l('days'),
                        'required' => true,
                        'desc' => $this->l('How long voucher is valid'),
                        'name' => 'AGENTCOMM_VCHR_DAYS',
                        'label' => $this->l('New voucher validity'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'radio',
                        //'prefix' => '%',
                        'required' => true,
                        'desc' => $this->l('Agent\'s commision type'),
                        'name' => 'AGENTCOMM_VCHR_COMM_TYPE',
                        'label' => $this->l('New voucher agent commision type'),
                        'values' => array(
                            array(
                                'id' => 'AGENTCOMM_VCHR_COMM_TYPE_1',
                                'value' => self::DISCOUNT_TYPE_PERCENT,
                                'label' => $this->l('Percent') .' (%)'
                            ),
                            array(
                                'id' => 'AGENTCOMM_VCHR_COMM_TYPE_2',
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
                        'desc' => $this->l('Agent\'s commision value'),
                        'name' => 'AGENTCOMM_VCHR_COMM',
                        'label' => $this->l('New voucher agent commision value'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Agents customers group'),
                        'name' => 'AGENTCOMM_AGENT_GROUP',
                        'multiple' => false,
                        'required' => true,
                        'options' => array(
                            'query' => $groups,
                            'id' => 'id_group',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        //'prefix' => '%',
                        'desc' => $this->l('POST or GET parameter which enable automatic assigning to Agents customers group'),
                        'name' => 'AGENTCOMM_AGENT_AUTO_PARAM',
                        'label' => $this->l('Agent auto parameter'),
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
            'AGENTCOMM_VCHR_DSCNT_TYPE' => Configuration::get('AGENTCOMM_VCHR_DSCNT_TYPE'),
            'AGENTCOMM_VCHR_DSCNT' => Configuration::get('AGENTCOMM_VCHR_DSCNT'),
            'AGENTCOMM_VCHR_DAYS' => Configuration::get('AGENTCOMM_VCHR_DAYS'),
            'AGENTCOMM_VCHR_COMM_TYPE' => Configuration::get('AGENTCOMM_VCHR_COMM_TYPE'),
            'AGENTCOMM_VCHR_COMM' => Configuration::get('AGENTCOMM_VCHR_COMM'),
            'AGENTCOMM_AGENT_GROUP' => Configuration::get('AGENTCOMM_AGENT_GROUP'),
            'AGENTCOMM_AGENT_AUTO_PARAM' => Configuration::get('AGENTCOMM_AGENT_AUTO_PARAM')
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
        $this->context->controller->addJS($this->_path.'js/back.js');
        //$this->context->controller->addCSS($this->_path.'css/back.css');
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
        	SELECT * FROM `' . _DB_PREFIX_ . 'hook` WHERE `name` = "actionBeforeSubmitAccount"
        ');
        if( empty($hookData) ){
            $hook = new Hook();
            $hook->name = 'actionBeforeSubmitAccount';
            $hook->title = 'actionBeforeSubmitAccount';
            $hook->position = 1;
            $hook->add();
        }
        
        
        if( !parent::install()
			|| !$this->registerHook('adminCustomers')
            || !$this->registerHook('customerAccount')
			|| !$this->registerHook('actionAdminControllerSetMedia')
            || !$this->registerHook('Header')
            || !$this->registerHook('actionCustomerAccountAdd')
            || !$this->registerHook('actionAdminCustomersFormModifier')
            || !$this->registerHook('actionObjectCustomerUpdateBefore')
            || !$this->registerHook('actionBeforeSubmitAccount')
        ){
            return false;
        }
        
        $tableCreateQuery =
        'CREATE TABLE `'._DB_PREFIX_.'agentcomm_agent_voucher` (
            `id_agent_voucher` int(11) NOT NULL AUTO_INCREMENT,
            `id_agent` int(11) NOT NULL,
            `id_voucher` int(11) NOT NULL,
            `status` int(1),
            `agent_commision_type` int(11) NOT NULL DEFAULT "0",
            `agent_commision` int(11) NOT NULL DEFAULT "0",
            PRIMARY KEY  (`id_agent_voucher`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        
        if (Db::getInstance()->execute($tableCreateQuery) == false){
            $this->_errors[] = Db::getInstance()->getMsgError();
            return false;
        }
        
        $customerFieldQuery = '
            ALTER TABLE `'._DB_PREFIX_.'customer`
                ADD `agent_commision_type` int(11) NOT NULL DEFAULT "0",
                ADD `agent_commision` int(11) NOT NULL DEFAULT "0"
        ';
        if (Db::getInstance()->execute($customerFieldQuery) == false){
            $this->_errors[] = Db::getInstance()->getMsgError();
            return false;
        }
        Configuration::updateValue('AGENTCOMM_VCHR_DSCNT_TYPE', 1);
        Configuration::updateValue('AGENTCOMM_VCHR_DSCNT', 10);
        Configuration::updateValue('AGENTCOMM_VCHR_DAYS', 180);
        Configuration::updateValue('AGENTCOMM_VCHR_COMM_TYPE', 1);
        Configuration::updateValue('AGENTCOMM_VCHR_COMM', 5);
        Configuration::updateValue('AGENTCOMM_AGENT_GROUP', 0);
        Configuration::updateValue('AGENTCOMM_AGENT_AUTO_PARAM', 'affiliate');
        
        $tab = new Tab ();
        $tab->class_name = 'AdminAgentcommAgents';
        $tab->module = $this->name;
        $tab->id_parent = (int)Tab::getIdFromClassName ( 'AdminCustomers' );
        foreach (Language::getLanguages () as $lang){
            $tab->name[(int)$lang['id_lang']] = 'Agents commisions';
        }
        if (! $tab->save ()){
            $this->_errors[] = $this->l('Tab "Agents commisions" install error');
            return false;
        }
        return true;
    }
    
    public function uninstall()
    {
        $agentsVouchers = Db::getInstance()->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'agentcomm_agent_voucher`
            WHERE `status` = 1
        ');
        if($agentsVouchers && is_array($agentsVouchers) && count($agentsVouchers)){
            foreach($agentsVouchers as $voucher){
                $cartRule = new CartRule($voucher['id_voucher']);
                $cartRule->status = 0;
                $cartRule->save();
            }
        }
        
        $tabId = (int) Tab::getIdFromClassName('AdminAgentcommAgents');
        $tab = new Tab($tabId);
        $tab->delete();
        
        
        Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'agentcomm_agent_voucher`');
        Db::getInstance()->query('ALTER TABLE `'._DB_PREFIX_.'customer` DROP `agent_commision`');
        Db::getInstance()->query('ALTER TABLE `'._DB_PREFIX_.'customer` DROP `agent_commision_type`');
        return parent::uninstall();
    }
}
