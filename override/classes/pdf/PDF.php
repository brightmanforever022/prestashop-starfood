<?php
class PDF extends PDFCore
{
    /*
    * module: ba_prestashop_invoice
    * date: 2016-12-13 10:14:55
    * version: 1.1.16
    */
    public function __construct($objects, $template, $smarty)
    {
        $this->pdf_renderer = new PDFGeneratorCore((bool)Configuration::get('PS_PDF_USE_CACHE'));
        $this->template = $template;
        $is_enable = Module::isEnabled('ba_prestashop_invoice');
        $is_invoice = $this->template == PDF::TEMPLATE_INVOICE;
        $is_delivery = $this->template == PDF::TEMPLATE_DELIVERY_SLIP;
        if ($is_enable==true && ( $is_invoice == true || $is_delivery == true)) {
            //$this->pdf_renderer = new PDFGenerator((bool)Configuration::get('PS_PDF_USE_CACHE'), '','A4',0,'',15,15,16,42,9,9);
            $this->pdf_renderer = new PDFGenerator((bool)Configuration::get('PS_PDF_USE_CACHE'), '','A4',0,'',0,0,0,0,0,0);
        }
        
        $this->smarty = $smarty;
        $this->objects = $objects;
        if (!($objects instanceof Iterator) && !is_array($objects)) {
            $this->objects = array($objects);
        }
    }
    
    /*
    * module: ba_prestashop_invoice
    * date: 2016-12-13 10:14:55
    * version: 1.1.16
    */
    public function render($display = true)
    {
        $render = false;
        $this->pdf_renderer->setFontForLang(Context::getContext()->language->iso_code);
        foreach ($this->objects as $object) {
            $template = $this->getTemplateObject($object);
            if (!$template) {
                continue;
            }
            if (empty($this->filename)) {
                $this->filename = $template->getFilename();
                if (count($this->objects) > 1) {
                    $this->filename = $template->getBulkFilename();
                }
            }
            $template->assignHookData($object);
            
            $is_enable = Module::isEnabled('ba_prestashop_invoice');
            $is_invoice = $this->template == PDF::TEMPLATE_INVOICE;
            $is_delivery = $this->template == PDF::TEMPLATE_DELIVERY_SLIP;
            if ($is_enable==true && ( $is_invoice == true || $is_delivery == true)) {
                $this->configMPDF($object);
            }
            
            $this->pdf_renderer->createHeader($template->getHeader());
            $this->pdf_renderer->createFooter($template->getFooter(), true);
            $this->pdf_renderer->createContent($template->getContent());
            
            $this->pdf_renderer->writePage();
            $render = true;
            unset($template);
        }
        if ($render) {
            if (ob_get_level() && ob_get_length() > 0) {
                ob_clean();
            }
            return $this->pdf_renderer->render($this->filename, $display);
        }
    }
    
    /*
    * module: ba_prestashop_invoice
    * date: 2016-12-13 10:14:55
    * version: 1.1.16
    */
    public function configMPDF($object)
    {
        require_once(_PS_MODULE_DIR_ . "ba_prestashop_invoice/ba_prestashop_invoice.php");
        $ba_prestashop_invoice = new ba_prestashop_invoice();
        $order = new Order($object->id_order);
        $db = Db::getInstance();
        if ($this->template == PDF::TEMPLATE_INVOICE) {
            
            $sql='SELECT * FROM '._DB_PREFIX_.'ba_prestashop_invoice WHERE id_lang='
            .(int)$order->id_lang.' AND status=1';
            $htmlTemplate = $db->ExecuteS($sql);
            
            $landscape=null;
            $showPagination="N";
            if (!empty($htmlTemplate)) {
                $landscape = $htmlTemplate[0]['baInvoiceEnableLandscape'];
                $showPagination = $htmlTemplate[0]['showPagination'];
            }
            if ($landscape=="Y") {
                $this->pdf_renderer->mpdf->AddPage("L");
            } else {
                $this->pdf_renderer->mpdf->AddPage("P");
            }
            
            if ($showPagination=="Y") {
                $this->pdf_renderer->mpdf->setFooter($ba_prestashop_invoice->returnFooterText());
            }
        } elseif ($this->template == PDF::TEMPLATE_DELIVERY_SLIP) {
            $sql='SELECT * FROM '._DB_PREFIX_.'ba_prestashop_delivery_slip WHERE id_lang='
            .(int)$order->id_lang.' AND status=1';
            $htmlTemplate = $db->ExecuteS($sql);
            
            $landscape=null;
            $showPagination="N";
            if (!empty($htmlTemplate)) {
                $landscape = $htmlTemplate[0]['baInvoiceEnableLandscape'];
                $showPagination = $htmlTemplate[0]['showPagination'];
            }
            if ($landscape=="Y") {
                $this->pdf_renderer->mpdf->AddPage("L");
            } else {
                $this->pdf_renderer->mpdf->AddPage("P");
            }
            
            if ($showPagination=="Y") {
                $this->pdf_renderer->mpdf->setFooter($ba_prestashop_invoice->returnFooterText());
            }
        }
    }
}
