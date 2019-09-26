<?php

class PdfInvoiceController extends PdfInvoiceControllerCore
{
    public function postProcess()
    {
        if (!$this->context->customer->isLogged() && !Tools::getValue('secure_key')) {
            Tools::redirect('index.php?controller=authentication&back=pdf-invoice');
        }
        
        if (!(int)Configuration::get('PS_INVOICE')) {
            die(Tools::displayError('Invoices are disabled in this shop.'));
        }
        
        if( Tools::isSubmit('id_order_invoice') ){
            $id_order_invoice = (int) Tools::getValue('id_order_invoice');
            $order_invoice = new OrderInvoice($id_order_invoice);
            
            if( !Validate::isLoadedObject($order_invoice) ){
                throw new Exception('Invoice not found');
            }
            
            $order = $order_invoice->getOrder();
        }
        else{
            $id_order = (int)Tools::getValue('id_order');
            if (Validate::isUnsignedId($id_order)) {
                $order = new Order((int)$id_order);
            }
            
        }
        
        if (!isset($order) || !Validate::isLoadedObject($order)) {
            die(Tools::displayError('The invoice was not found.'));
        }
        
        if ((isset($this->context->customer->id) && $order->id_customer != $this->context->customer->id) || (Tools::isSubmit('secure_key') && $order->secure_key != Tools::getValue('secure_key'))) {
            die(Tools::displayError('The invoice was not found.'));
        }
        
        /*
        if (!OrderState::invoiceAvailable($order->getCurrentState()) && !$order->invoice_number) {
            die(Tools::displayError('No invoice is available.'));
        }
        */
        $order_invoice_list = $order->getInvoicesCollection();
        if( empty($order_invoice_list) ){
            throw new Exception('No invoices found');
        }
        
        $this->order = $order;
    }
    
    public function display()
    {
        $id_order_invoice = (int) Tools::getValue('id_order_invoice');
        //$order_invoice_list = $this->order->getInvoicesCollection();
        //Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => $order_invoice_list));

        $order_invoice = new OrderInvoice($id_order_invoice);
        
        if( !Validate::isLoadedObject($order_invoice) ){
            throw new Exception('Invoice not found');
        }
        
        if (file_exists($order_invoice->getInvoiceFilePath()))
        {
            $fileName = $order_invoice->getInvoiceFileName();
            // send to browser
            header("Content-type: application/pdf");
            header("Content-Disposition: attachment; filename=$fileName");
            
            header('Pragma: no-cache',true);
            header('Expires: 0',true);
            
            readfile($order_invoice->getInvoiceFilePath());
        }
        else{
            $pdf = new PDF($order_invoice, PDF::TEMPLATE_INVOICE, $this->context->smarty);
            $pdf->render();
        }
        
    }
    
}

