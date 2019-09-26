<?php

class Notereminder extends Module
{
    public function __construct()
    {
        $this->name = 'notereminder';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Vitaliy';
        $this->need_instance = 0;
        $this->bootstrap = true;
        
        parent::__construct();
        
        //$this->controllers = array('map');
        
        $this->displayName = $this->l('Order note reminder');
        
    }
    
    public function getContent()
    {
        $remindersQuery = '
            SELECT nm.*, cm.`message`
            FROM `' . _DB_PREFIX_ . 'note_reminder` nm
            INNER JOIN `' . _DB_PREFIX_ . 'customer_message` cm
                ON cm.`id_customer_message` = nm.`id_customer_message`
            WHERE nm.`remind_sent` = 0
        ';

        $reminders = Db::getInstance()->executeS($remindersQuery);
        
        for($i = 0; $i < count($reminders); $i++){
            $reminders[$i]['order_link'] = $this->context->link->getAdminLink('AdminOrders')
                .'&id_order='. $reminders[$i]['id_order'] .'&vieworder';
        }
        
        $this->context->smarty->assign(array(
            'reminders' => $reminders
        ));
        
        return $this->context->smarty->fetch($this->local_path.'views/templates/admin/configuration.tpl');
    }
    
    public function install()
    {
        if(!parent::install()){
            return false;
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
        
        $hookData = Db::getInstance()->ExecuteS('
        	SELECT * FROM `' . _DB_PREFIX_ . 'hook` WHERE `name` = "actionObjectCustomerMessageAddAfter"
        ');
        if( empty($hookData) ){
            $hook = new Hook();
            $hook->name = 'actionObjectCustomerMessageAddAfter';
            $hook->title = 'actionObjectCustomerMessageAddAfter';
            $hook->position = 1;
            $hook->add();
        }
        
        
        if( !$this->registerHook('displayAdminOrderRight') ){
            $this->_errors[] = 'hook "displayAdminOrderRight"';
            return false;
        }
        if( !$this->registerHook('actionObjectCustomerMessageAddAfter') ){
            $this->_errors[] = 'hook "actionObjectCustomerMessageAddAfter"';
            return false;
        }
        
        
        $dbQueries = array(
            'CREATE TABLE `' . _DB_PREFIX_ . 'note_reminder` (
              `id` int(10) UNSIGNED NOT NULL,
              `id_order` int(11) NOT NULL,
              `id_customer_message` int(10) UNSIGNED NOT NULL,
              `remind_date` date NOT NULL,
              `remind_sent` tinyint(4) NOT NULL DEFAULT "0"
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ',
            'ALTER TABLE `' . _DB_PREFIX_ . 'note_reminder` ADD PRIMARY KEY (`id`)',
            'ALTER TABLE `' . _DB_PREFIX_ . 'note_reminder` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT'
        );
        
        foreach( $dbQueries as $query ){
            if(Db::getInstance()->query($query) == false){
                $this->_errors[] = Db::getInstance()->getMsgError();
                return false;
            }
        }
        
        return true;
    }
    
    public function uninstall()
    {
        parent::uninstall();
        
        Db::getInstance()->query('DROP TABLE `' . _DB_PREFIX_ . 'note_reminder`');
        
        return true;
    }
    
    public function hookDisplayAdminOrderRight($params)
    {
        $this->context->smarty->assign(array(
            'noteReminderDateLabelTrns' => $this->l('Date send reminder')
        ));
        
        $this->context->controller->addJS($this->_path.'js/admin.js');
        
        return $this->context->smarty->fetch($this->local_path.'views/templates/admin/display_admin_order_right.tpl');
    }
    
    public function hookActionObjectCustomerMessageAddAfter($params)
    {
        $id_order = (int)Tools::getValue('id_order');
        $date_remind = Tools::getValue('notereminder_date');
        if( empty($params['object']->id)
            || empty($id_order)
            || empty($date_remind)
            || !Validate::isDate($date_remind)
        ){
            return;
        }
        
        $date_remind = date('Y-m-d', strtotime($date_remind));
        
        try{
            Db::getInstance()->insert('note_reminder', array(
                'id_order' => $id_order,
                'id_customer_message' => $params['object']->id,
                'remind_date' => $date_remind
            ));
        }
        catch(Exception $e){
            
        }
    }
}

