<?php

class HistoryController extends HistoryControllerCore
{
    public function initContent()
    {
        parent::initContent();
        
        if ($orders = Order::getCustomerOrders($this->context->customer->id)) {
            foreach ($orders as &$order) {
                $myOrder = new Order((int)$order['id_order']);
                if (Validate::isLoadedObject($myOrder)) {
                    $order['virtual'] = $myOrder->isVirtual(false);
                }
                
                $orderInvoices = $myOrder->getInvoicesCollection()->getResults();
                for( $oii = 0; $oii < count($orderInvoices); $oii++ ){
                    $orderInvoices[$oii]->number_formatted = $orderInvoices[$oii]->getInvoiceNumberFormatted($myOrder->id_lang);
                }
                $order['invoices_list'] = $orderInvoices;
            }
        }
        
        $this->context->smarty->assign(array(
            'orders' => $orders,
            'invoiceAllowed' => (int)Configuration::get('PS_INVOICE'),
            'reorderingAllowed' => !(bool)Configuration::get('PS_DISALLOW_HISTORY_REORDERING'),
            'slowValidation' => Tools::isSubmit('slowvalidation')
        ));
        
        $this->setTemplate(_PS_THEME_DIR_.'history.tpl');
    }
    
}