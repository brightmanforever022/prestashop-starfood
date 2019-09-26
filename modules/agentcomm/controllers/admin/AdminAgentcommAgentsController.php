<?php

require _PS_MODULE_DIR_ .'agentcomm/classes/AgentVoucher.php';

class AdminAgentcommAgentsController extends ModuleAdminController
{
    public $bootstrap = true;
    
    public function __construct()
    {
        parent::__construct();
        
        //$this->required_database = true;
        //$this->required_fields = array();
        $this->table = 'agentcomm_agent_voucher';
        $this->className = 'AgentVoucher';
        $this->identifier = 'id_agent_voucher';
        $this->_defaultOrderBy = 'cr!date_to';
        $this->_defaultOrderWay = 'DESC';
        $this->lang = false;
        $this->context = Context::getContext();
        
        $this->fields_list = array(
            //'id_address' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'agent_firstname' => array('title' => $this->l('Agent first name'), 'filter_key' => 'c!firstname'),
            'agent_lastname' => array('title' => $this->l('Agent last name'), 'filter_key' => 'c!lastname'),
            'voucher_code' => array('title' => $this->l('Voucher'), 'filter_key' => 'cr!code'),
            //'voucher_date' => array('title' => $this->l('Zip/Postal Code'), 'align' => 'right'),
            'orders_total' => array('title' => $this->l('Orders total sum'), 'type' => 'number', 'filter_type' => 'number', 'havingFilter' => 'orders_total'),
            'commisions_total' => array('title' => $this->l('Order total commision'), 'type' => 'number', 'filter_type' => 'number', 'havingFilter' => 'orders_total'),
            'date_to' => array(
                'title' => $this->l('Date'),
                'align' => 'text-right',
                'type' => 'datetime',
                'filter_key' => 'cr!date_to'
            )
            
        );
        
        $this->_select = 'c.`firstname` AS agent_firstname, c.lastname AS agent_lastname, 
            cr.`id_cart_rule`, cr.code as voucher_code, cr.date_from, cr.date_to';
        $this->_join = '
            INNER JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_agent`)
			INNER JOIN `'._DB_PREFIX_.'cart_rule` cr ON a.id_voucher = cr.id_cart_rule
        ';
        
        $this->_select .= '
            ,(
                SELECT ROUND(SUM(s1o.total_products - s1o.total_discounts_tax_excl), 2) 
                FROM `'._DB_PREFIX_.'orders` s1o 
                LEFT JOIN `'._DB_PREFIX_.'order_cart_rule` s1ocr 
                    ON s1ocr.id_order = s1o.`id_order`
                WHERE s1ocr.`id_cart_rule` = cr.`id_cart_rule`
                    AND s1o.`valid` = 1
            ) AS orders_total, 
            (
                SELECT ROUND(IF(a.agent_commision_type = 1, 
                    SUM(s1o.total_products - s1o.total_discounts_tax_excl) / 100 * a.agent_commision,
                    COUNT(s1o.id_order) * a.agent_commision
                ), 2)
                FROM `'._DB_PREFIX_.'orders` s1o 
                LEFT JOIN `'._DB_PREFIX_.'order_cart_rule` s1ocr 
                    ON s1ocr.id_order = s1o.`id_order`
                WHERE s1ocr.`id_cart_rule` = cr.`id_cart_rule`
                    AND s1o.`valid` = 1
            ) AS commisions_total
        ';
        
        $this->toolbar_title = $this->l('Statistics');
        
        
        
    }
    
    public function init()
    {
        parent::init();
        $action = Tools::getValue('action', 'default');
        if( Tools:: isSubmit('updateagentcomm_agent_voucher') ){
            $action = 'updateagentcomm_agent_voucher';
        }
        switch($action){
            case 'start_voucher':
                $this->startNewVoucher();
                break;
            case 'get_voucher':
                $this->getVoucherInfo();
                break;
            case 'updateagentcomm_agent_voucher':
                $this->getVoucherInfo();
                break;
            default:
                //$this->getStats();
                break;
        }

        
    }
    
    public function startNewVoucher()
    {
        $id_customer = Tools::getValue('id_customer');
        
        $this->module->startNewAgentVoucher($id_customer);
        
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminCustomers').'&id_customer='.$id_customer.'&viewcustomer');
    }
    
    public function getVoucherInfo()
    {
        $id_voucher = (int)Tools::getValue('id_voucher');
        $id_agent_voucher = (int)Tools::getValue('id_agent_voucher');
        
        if(!empty($id_voucher)){
            $voucherOrders = $this->module->getOrdersByVoucher($id_voucher);
            $this->context->smarty->assign(array(
                'voucher_orders_info' => $voucherOrders,
                'currency' => Currency::getDefaultCurrency(),
                'layout' => false
            ));
            
            die( $this->context->smarty->fetch($this->module->getTemplatePath('views/templates/admin/voucher.tpl')) );
        }
        elseif(!empty($id_agent_voucher)){
            $voucherOrders = $this->module->getOrdersByVoucher(null, $id_agent_voucher);
            $this->context->smarty->assign(array(
                'voucher_orders_info' => $voucherOrders,
                'currency' => Currency::getDefaultCurrency(),
                'layout' => true
            ));
            
            $this->content = $this->context->smarty->fetch($this->module->getTemplatePath('views/templates/admin/voucher.tpl')) ;
            
        }
    }
    
    public function getStats()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Conditions'),
                'icon' => 'icon-calendar'
            ),
            'input' => array(
                array(
                    'type' => 'date',
                    'label' => $this->l('From'),
                    'name' => 'date_from',
                    'maxlength' => 10,
                    'required' => true,
                    'hint' => $this->l('Format: 2018-01-01 (inclusive).')
                ),
                array(
                    'type' => 'date',
                    'label' => $this->l('To'),
                    'name' => 'date_to',
                    'maxlength' => 10,
                    'required' => true,
                    'hint' => $this->l('Format: 2018-01-31 (inclusive).')
                )
            ),
            'submit' => array(
                'title' => $this->l('Generate statistics'),
                'id' => 'submitPrint',
                'icon' => 'process-icon-download-alt'
            )
        );
        
        $this->fields_value = array(
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d')
        );
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
            $dateFrom = Tools::getValue('date_from');
            $dateTo = Tools::getValue('date_to');
            $validateRegex = '#^\d{4}-\d{2}-\d{2}$#';
            if( preg_match($validateRegex, $dateFrom) ){
                $this->fields_value['date_from'] = $dateFrom;
            }
            if( preg_match($validateRegex, $dateTo) ){
                $this->fields_value['date_to'] = $dateTo;
            }
            
        }
        
        $this->show_toolbar = false;
        $this->show_form_cancel_button = false;
        $this->toolbar_title = $this->l('Agent\'s statistics');
        $this->content .= parent::renderForm();
        
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
            $this->content .= $this->showStats();
        }
        
    }
    
    protected function showStats()
    {
        
        $dateFrom = Tools::getValue('date_from');
        $dateTo = Tools::getValue('date_to');
        
        $validateRegex = '#^\d{4}-\d{2}-\d{2}$#';
        if( !preg_match($validateRegex, $dateFrom) || !preg_match($validateRegex, $dateTo) ){
            return;
        }
        
        $sqlDateFrom = date('Y-m-d H:i:s', strtotime($dateFrom));
        $sqlDateTo = date('Y-m-d H:i:s', (strtotime($dateTo) + 86399) );
        
        $agentToPastVoucher = Db::getInstance()->executeS('
            SELECT aav.*, cr.id_cart_rule, cr.code, cr.date_from, cr.date_to
            FROM `'._DB_PREFIX_.'agentcomm_agent_voucher` aav
            INNER JOIN `'._DB_PREFIX_.'cart_rule` cr ON aav.id_voucher = cr.id_cart_rule
            WHERE aav.`status` = 0
                AND cr.active = 0
                AND cr.date_from >= "'. pSQL($sqlDateFrom) .'"
                AND cr.date_to <= "'. pSQL($sqlDateTo) .'"
        ');
        if( !is_array($agentToPastVoucher) || !count($agentToPastVoucher) ){
            return;
        }
        
        $agentsStats = array(
            'vouchers' => array(),
            'agents_orders_total' => 0,
            'agents_commisions_total' => 0
        );
        foreach($agentToPastVoucher as $agentPastVoucher){
            $agentData = array();
            $agent = new Customer($agentPastVoucher['id_agent']);
            $cartRule = new CartRule($agentPastVoucher['id_cart_rule']);
            $voucherData = $this->module->getOrdersByVoucher($agentPastVoucher['id_cart_rule']);
            
            $agentData['voucher_code'] = $cartRule->code;
            $agentData['voucher_date'] = $cartRule->date_from .' - '. $cartRule->date_to;
            $agentData['agent_name'] = $agent->firstname .' '. $agent->lastname;
            $agentData['orders_count'] = count($voucherData['orders_list']);
            $agentData['orders_total'] = $voucherData['orders_products_total'];
            $agentData['agent_commision'] = $agentPastVoucher['agent_commision'];
            $agentData['commisions_total'] = $voucherData['commision_total'];
            
            $agentsStats['vouchers'][] = $agentData;
            $agentsStats['agents_orders_total'] += $agentData['orders_total'];
            $agentsStats['agents_commisions_total'] += $agentData['commisions_total'];
        }
        
        $this->context->smarty->assign(array(
            'agents_stats' => $agentsStats,
            'stats_date_from' => $sqlDateFrom,
            'stats_date_to' => $sqlDateTo
        ));
        
        return $this->context->smarty->fetch($this->module->getTemplatePath('views/templates/admin/stats.tpl'));
    }
    
}