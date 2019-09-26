<?php
/**
* This file will override class PDFCore. Do not modify this file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/

class PDF extends PDFCore
{
	public function render($display = true)
	{
		$render = false;
		$this->pdf_renderer->setFontForLang(Context::getContext()->language->iso_code);
		foreach ($this->objects as $object)
		{
			$template = $this->getTemplateObject($object);
			if (!$template)
				continue;

			if (empty($this->filename))
			{
				$this->filename = $template->getFilename();
				if (count($this->objects) > 1)
					$this->filename = $template->getBulkFilename();
			}
			$template->assignHookData($object);
            $id_shop_group = (int)Shop::getContextShopGroupID();
		    $id_shop = (int)Shop::getContextShopID();
            if($id_shop <= 0) $id_shop = (int)Context::getContext()->shop->id;
            if(Module::isInstalled('gwadvancedinvoice') && Module::isEnabled('gwadvancedinvoice') && Configuration::get('GWADVANCEDINVOICE_ACTIVE', null, $id_shop_group, $id_shop) && get_class($object) == 'OrderInvoice'){
                $templatetype =  get_class($template);
                $_id_template = 0;
                if($templatetype == 'HTMLTemplateInvoice'){
                    $id_template = (int)Configuration::get('GWADVANCEDINVOICE_TEMPLATE', null, $id_shop_group, $id_shop);
                    $date_from = Tools::getValue('date_from');
                    $date_to = Tools::getValue('date_to');
                    $submitAction = Tools::getValue('submitAction');
                    if(trim($date_from) =='' && trim($date_to) =='' && trim($submitAction) !='generateInvoicesPDF2'){
                        $order = new Order((int)$object->id_order);
                        $customer_group = Customer::getDefaultGroupId((int)$order->id_customer);
                        if($customer_group){
                            $_id_template = (int)Configuration::get('GWADVANCEDINVOICE_GROUP_'.$customer_group, null, $id_shop_group, $id_shop);
                            if($_id_template > 0) $id_template = (int)$_id_template;
                        }
                    }
                }elseif($templatetype == 'HTMLTemplateDeliverySlip'){
                    $id_template = (int)Configuration::get('GWADVANCEDDELIVERY_TEMPLATE', null, $id_shop_group, $id_shop);
                    $date_from = Tools::getValue('date_from');
                    $date_to = Tools::getValue('date_to');
                    $submitAction = Tools::getValue('submitAction');
                    if(trim($date_from) =='' && trim($date_to) =='' && trim($submitAction) !='generateInvoicesPDF2'){
                        $order = new Order((int)$object->id_order);
                        $customer_group = Customer::getDefaultGroupId((int)$order->id_customer);
                        if($customer_group){
                            $_id_template = (int)Configuration::get('GWADVANCEDIDELIVERY_GROUP_'.$customer_group, null, $id_shop_group, $id_shop);
                            if($_id_template > 0) $id_template = (int)$_id_template;
                        }
                    }
                }
                include_once(_PS_MODULE_DIR_.'/gwadvancedinvoice/model/gwadvancedinvoicetemplateModel.php');
                $templateinvoice = new gwadvancedinvoicetemplateModel((int)$id_template);
                $this->pdf_renderer->setCurOrientation($templateinvoice->pagesize,$templateinvoice->pageorientation);
                $data = $template->assignData($id_template);
                if($templateinvoice->rtl){
                    $this->pdf_renderer->setRTL((bool)$templateinvoice->rtl);
                }
                if($templateinvoice->activeheader){
                    $this->pdf_renderer->createHeader($template->getHeaderGw($data));
                    $this->pdf_renderer->SetPrintHeader(true);
                }else
                    $this->pdf_renderer->SetPrintHeader(false);
                $this->pdf_renderer->createContent($template->getContentGw($data));
                if($templateinvoice->activefooter){
    			    $this->pdf_renderer->createFooter($template->getFooterGw($data));
                    $this->pdf_renderer->SetPrintFooter(true);
                }else
                    $this->pdf_renderer->SetPrintFooter(false);
                    
                $this->pdf_renderer->writePageGw($templateinvoice->mgheader,$templateinvoice->mgfooter,$templateinvoice->mgcontent);
                $watermank_img = '';
                $watermank_text = '';
                $watermank_font = '';
                $watermank_size = '10';
                if(isset($templateinvoice->watermark[(int)Context::getContext()->language->id]) && $templateinvoice->watermark[(int)Context::getContext()->language->id]!=''){
                    $useSSL = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
            		$protocol_content = ($useSSL) ? 'https:/'.'/' : 'http:/'.'/';
                    $base_url = $protocol_content.Tools::getHttpHost().__PS_BASE_URI__;
                    if(!is_dir(_PS_MODULE_DIR_.'/gwadvancedinvoice/views/img/watermark/'.$templateinvoice->watermark[(int)Context::getContext()->language->id]))
                            if(file_exists(_PS_MODULE_DIR_.'/gwadvancedinvoice/views/img/watermark/'.$templateinvoice->watermark[(int)Context::getContext()->language->id]))
                                $watermank_img = $base_url.'modules/gwadvancedinvoice/views/img/watermark/'.$templateinvoice->watermark[(int)Context::getContext()->language->id];
                }
                if(isset($templateinvoice->watermarktext[(int)Context::getContext()->language->id]) && $templateinvoice->watermarktext[(int)Context::getContext()->language->id]!=''){
                    $watermank_text = $templateinvoice->watermarktext[(int)Context::getContext()->language->id];
                    $watermank_font = $templateinvoice->watermarkfont[(int)Context::getContext()->language->id];
                    $watermank_size = $templateinvoice->watermarksize[(int)Context::getContext()->language->id];
                }
                if($watermank_img !='' || $watermank_text !=''){
                    $this->pdf_renderer->addWaterMark($watermank_text,$watermank_img,45,0,'0.1',$watermank_font,$watermank_size);
                }
                
            }else{
                $this->pdf_renderer->createHeader($template->getHeader());
    			$this->pdf_renderer->createFooter($template->getFooter());
    			$this->pdf_renderer->createContent($template->getContent());
                $this->pdf_renderer->writePage();
            }
			$render = true;
			unset($template);
		}
		if ($render)
		{
			if (ob_get_level() && ob_get_length() > 0)
				ob_clean();
            if(Module::isInstalled('gwadvancedinvoice') && Module::isEnabled('gwadvancedinvoice') && Configuration::get('GWADVANCEDINVOICE_ACTIVE', null, $id_shop_group, $id_shop) && get_class($object) == 'OrderInvoice'){
                return $this->pdf_renderer->renderInvoice($this->filename, $display);
            }
            else
			     return $this->pdf_renderer->render($this->filename, $display);
		}
	}
}
?>