<?php

class AgentcommCommisionsModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    
    public function initContent()
    {
        parent::initContent();
        
        if( Tools::isSubmit('action') ){
            $action = Tools::getValue('action');
        
            switch($action){
                case 'vouchers':
                    $this->getVouchers();
                    break;
                default:
                    break;
            }
        }
    }

    public function getVouchers()
    {
        $voucher_code = Tools::getValue('voucher_code');

        if(empty($voucher_code)){
            $agentToVoucher = Db::getInstance()->getRow('
                SELECT * FROM `'._DB_PREFIX_.'agentcomm_agent_voucher`
                WHERE `id_agent` = '. $this->context->customer->id .'
                    AND `status` = 1
            ');
            
            if( is_array($agentToVoucher) && !empty($agentToVoucher['id_agent_voucher'])){
                $agentCurrentVoucher = new CartRule($agentToVoucher['id_voucher']);
                $id_voucher = $agentCurrentVoucher->id;
                $this->context->smarty->assign(array(
                    'agent_current_voucher' => $agentCurrentVoucher,
                ));
            }
        }
        else{
            $agentToVoucher = Db::getInstance()->getRow('
                SELECT aav.*, cr.id_cart_rule, cr.code, cr.date_from, cr.date_to
                FROM `'._DB_PREFIX_.'agentcomm_agent_voucher` aav
                INNER JOIN `'._DB_PREFIX_.'cart_rule` cr ON aav.id_voucher = cr.id_cart_rule
                WHERE aav.`id_agent` = '. $this->context->customer->id .'
                    AND aav.`status` = 0
                    AND cr.code = "'. pSQL($voucher_code) .'"
            ');
            if( is_array($agentToVoucher) && !empty($agentToVoucher['id_agent_voucher'])){
                $agentCurrentVoucher = new CartRule($agentToVoucher['id_voucher']);
                $id_voucher = $agentCurrentVoucher->id;
                $this->context->smarty->assign(array(
                    'agent_current_voucher' => $agentCurrentVoucher,
                ));
            }
            
        }
        
        if(!empty($id_voucher)){
            $voucherOrders = $this->module->getOrdersByVoucher($id_voucher);
            $this->context->smarty->assign(array(
                'voucher_orders_info' => $voucherOrders
            ));
        }
        
        $agentToPastVoucher = Db::getInstance()->executeS('
            SELECT aav.*, cr.id_cart_rule, cr.code, cr.date_from, cr.date_to
            FROM `'._DB_PREFIX_.'agentcomm_agent_voucher` aav
            INNER JOIN `'._DB_PREFIX_.'cart_rule` cr ON aav.id_voucher = cr.id_cart_rule
            WHERE aav.`id_agent` = '. $this->context->customer->id .'
                AND aav.`status` = 0
        ');
        
        if( $agentToPastVoucher && is_array($agentToPastVoucher) && count($agentToPastVoucher) ){
            $this->context->smarty->assign(array(
                'vouchers_past' => $agentToPastVoucher
            ));
        
        }
        
        return $this->setTemplate('vouchers.tpl');
    }
    
}
