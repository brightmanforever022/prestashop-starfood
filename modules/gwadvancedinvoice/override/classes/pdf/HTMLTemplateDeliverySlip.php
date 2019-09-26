<?php
/**
* This file will override class HTMLTemplateDeliverySlipCore. Do not modify this file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/

class HTMLTemplateDeliverySlip extends HTMLTemplateDeliverySlipCore
{
    public function getHeaderGw($data){
        $id_shop_group = (int)$this->order->id_shop_group;
		$id_shop = (int)$this->order->id_shop;
        if(Configuration::get('GWADVANCEDINVOICE_ACTIVE', null, $id_shop_group, $id_shop)){
            $id_template = Configuration::get('GWADVANCEDDELIVERY_TEMPLATE', null, $id_shop_group, $id_shop);
            include_once(_PS_MODULE_DIR_.'/gwadvancedinvoice/model/gwadvancedinvoicetemplateModel.php');
            $date_from = Tools::getValue('date_from');
            $date_to = Tools::getValue('date_to');
            $submitAction = Tools::getValue('submitAction');
            if(trim($date_from) =='' && trim($date_to) =='' && trim($submitAction) !='generateInvoicesPDF2'){
                include_once(_PS_MODULE_DIR_.'/gwadvancedinvoice/model/gwadvancedinvoicetemplateModel.php');
                $customer_group = Customer::getDefaultGroupId((int)$this->order->id_customer);
                if($customer_group){
                    $_id_template = (int)Configuration::get('GWADVANCEDIDELIVERY_GROUP_'.$customer_group, null, $id_shop_group, $id_shop);
                    if($_id_template > 0) $id_template = (int)$_id_template;
                }
            }
            $template = new gwadvancedinvoicetemplateModel((int)$id_template);
            if($template && $template->layout !=''){
                $this->smarty->assign($data);
                $temp =  _PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/customize/'.Context::getContext()->shop->id.'/'.$template->layout.'/'.Context::getContext()->language->iso_code.'/header.tpl';
                $styles = $this->smarty->fetch(_PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/customize/'.Context::getContext()->shop->id.'/'.$template->layout.'/'.Context::getContext()->language->iso_code.'/styles.tpl');
                $content = $this->smarty->fetch($temp);
                $content = preg_replace_callback("/(<img[^>]*src *= *[\"']?)([^\"']*)/i",
                    function ($matches) {
                        $useSSL = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? true : false;
                        $protocol_content = ($useSSL) ? 'https:/'.'/' : 'http:/'.'/';
                        $base_url = $protocol_content.Tools::getHttpHost().__PS_BASE_URI__;
                        $link = str_replace(
        						$base_url,
        						_PS_ROOT_DIR_.DIRECTORY_SEPARATOR,
        						$matches['2']);
                      return $matches[1] . $link;
                    }
                , $content);
                return '<style>'.strip_tags($styles).'</style>'.$content;
            }
        }
        return '';
    }
    public function getFooterGw($data){
        $id_shop_group = (int)$this->order->id_shop_group;
		$id_shop = (int)$this->order->id_shop;
        if(Configuration::get('GWADVANCEDINVOICE_ACTIVE', null, $id_shop_group, $id_shop)){
            $id_template = Configuration::get('GWADVANCEDDELIVERY_TEMPLATE', null, $id_shop_group, $id_shop);
            $date_from = Tools::getValue('date_from');
            $date_to = Tools::getValue('date_to');
            $submitAction = Tools::getValue('submitAction');
            if(trim($date_from) =='' && trim($date_to) =='' && trim($submitAction) !='generateInvoicesPDF2'){
                include_once(_PS_MODULE_DIR_.'/gwadvancedinvoice/model/gwadvancedinvoicetemplateModel.php');
                $customer_group = Customer::getDefaultGroupId((int)$this->order->id_customer);
                if($customer_group){
                    $_id_template = (int)Configuration::get('GWADVANCEDIDELIVERY_GROUP_'.$customer_group, null, $id_shop_group, $id_shop);
                    if($_id_template > 0) $id_template = (int)$_id_template;
                }
            }
            include_once(_PS_MODULE_DIR_.'/gwadvancedinvoice/model/gwadvancedinvoicetemplateModel.php');
            $template = new gwadvancedinvoicetemplateModel((int)$id_template);
            if($template && $template->layout !=''){
                $this->smarty->assign($data);
                $temp =  _PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/customize/'.Context::getContext()->shop->id.'/'.$template->layout.'/'.Context::getContext()->language->iso_code.'/footer.tpl';
                $styles = $this->smarty->fetch(_PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/customize/'.Context::getContext()->shop->id.'/'.$template->layout.'/'.Context::getContext()->language->iso_code.'/styles.tpl');
                $content = $this->smarty->fetch($temp);
                $content = preg_replace_callback("/(<img[^>]*src *= *[\"']?)([^\"']*)/i",
                    function ($matches) {
                        $useSSL = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? true : false;
                        $protocol_content = ($useSSL) ? 'https:/'.'/' : 'http:/'.'/';
                        $base_url = $protocol_content.Tools::getHttpHost().__PS_BASE_URI__;
                        $link = str_replace(
        						$base_url,
        						_PS_ROOT_DIR_.DIRECTORY_SEPARATOR,
        						$matches['2']);
                      return $matches[1] . $link;
                    }
                , $content);
                return '<style>'.strip_tags($styles).'</style>'.$content;
            }
        }
        return '';
    }
    public function getContentGw($data)
	{
        $id_shop_group = (int)$this->order->id_shop_group;
		$id_shop = (int)$this->order->id_shop;
        if(Configuration::get('GWADVANCEDINVOICE_ACTIVE', null, $id_shop_group, $id_shop)){
            $id_template = Configuration::get('GWADVANCEDDELIVERY_TEMPLATE', null, $id_shop_group, $id_shop);
            $date_from = Tools::getValue('date_from');
            $date_to = Tools::getValue('date_to');
            $submitAction = Tools::getValue('submitAction');
            if(trim($date_from) =='' && trim($date_to) =='' && trim($submitAction) !='generateInvoicesPDF2'){
                include_once(_PS_MODULE_DIR_.'/gwadvancedinvoice/model/gwadvancedinvoicetemplateModel.php');
                $customer_group = Customer::getDefaultGroupId((int)$this->order->id_customer);
                if($customer_group){
                    $_id_template = (int)Configuration::get('GWADVANCEDIDELIVERY_GROUP_'.$customer_group, null, $id_shop_group, $id_shop);
                    if($_id_template > 0) $id_template = (int)$_id_template;
                }
            }
            include_once(_PS_MODULE_DIR_.'/gwadvancedinvoice/model/gwadvancedinvoicetemplateModel.php');
            $template = new gwadvancedinvoicetemplateModel((int)$id_template);
            if($template && $template->layout !=''){
                $this->smarty->assign($data);
                $products_list_temp =  _PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/customize/'.Context::getContext()->shop->id.'/'.$template->layout.'/'.Context::getContext()->language->iso_code.'/product_list.tpl';
                $products_list = $this->smarty->fetch($products_list_temp);
                $data['products_list'] = $products_list;
                $this->smarty->assign($data);
                $temp =  _PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/customize/'.Context::getContext()->shop->id.'/'.$template->layout.'/'.Context::getContext()->language->iso_code.'/template.tpl';
                $styles = $this->smarty->fetch(_PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/customize/'.Context::getContext()->shop->id.'/'.$template->layout.'/'.Context::getContext()->language->iso_code.'/styles.tpl');
                $content = $this->smarty->fetch($temp);
                $content = preg_replace_callback("/(<img[^>]*src *= *[\"']?)([^\"']*)/i",
                    function ($matches) {
                        $useSSL = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? true : false;
                        $protocol_content = ($useSSL) ? 'https:/'.'/' : 'http:/'.'/';
                        $base_url = $protocol_content.Tools::getHttpHost().__PS_BASE_URI__;
                        $link = str_replace(
        						$base_url,
        						_PS_ROOT_DIR_.DIRECTORY_SEPARATOR,
        						$matches['2']);
                      return $matches[1] . $link;
                    }
                , $content);
                return '<style>'.strip_tags($styles).'</style>'.$content;
            }else{
                return parent::getContent();
            }
        }else{
            return  parent::getContent();
        }
	}
    public function assignData($id_template){
        include_once(_PS_MODULE_DIR_.'/gwadvancedinvoice/model/gwadvancedinvoicetemplateModel.php');
        include_once(_PS_MODULE_DIR_.'/gwadvancedinvoice/libs/barcode.php');
        include_once(_PS_MODULE_DIR_.'/gwadvancedinvoice/libs/qrcode.php');
        $template = new gwadvancedinvoicetemplateModel((int)$id_template);
        $data_assign = array();
        $data_assign = Tools::jsonDecode(Tools::jsonEncode($this->order), true);
        $this->setShopId();
		$id_shop = (int)$this->shop->id;
        $id_lang = (int)$this->order->id_lang;
        $data_assign['shopname'] = Configuration::get('PS_SHOP_NAME', null, null, $id_shop);
        $data_assign['shop_details'] = Configuration::get('PS_SHOP_DETAILS', null, null, (int)$id_shop);
        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https:/'.'/' : 'http:/'.'/';
        $_link = $protocol_link.Tools::getShopDomainSsl().__PS_BASE_URI__;
        $useSSL = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
		$protocol_content = ($useSSL) ? 'https:/'.'/' : 'http:/'.'/';
        $base_url = $protocol_content.Tools::getHttpHost().__PS_BASE_URI__;
        $data_assign['shopurl'] = $base_url;
        //$path_logo = str_replace(_PS_ROOT_DIR_.'/',$_link,$this->getLogo());
        $path_logo = str_replace($_link,_PS_ROOT_DIR_.'/',$this->getLogo());
        $data_assign['invoice_number'] = Configuration::get('PS_INVOICE_PREFIX', $data_assign['id_lang']).sprintf('%06d', $data_assign['invoice_number']);
        $prefix = Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id);
        $data_assign['delivery_number'] = sprintf(HTMLTemplateDeliverySlip::l('%1$s%2$06d'), $prefix, $this->order_invoice->delivery_number);
		$invoice_address = new Address((int)$this->order->id_address_invoice);
        $invoiceaddress = Tools::jsonDecode(Tools::jsonEncode($invoice_address), true);
        foreach($invoiceaddress as $key=>$val){
            if($key == 'id_state'){
                $data_assign['billing_state'] = State::getNameById($val);
            }else
            $data_assign['billing_'.$key] = $val;
        }
		if (isset($this->order->id_address_delivery) && $this->order->id_address_delivery)
		{
		    $delivery_address = new Address((int)$this->order->id_address_delivery);
            $deliveryaddress = Tools::jsonDecode(Tools::jsonEncode($delivery_address), true);
			foreach($deliveryaddress as $key=>$val){
			    if($key == 'id_state'){
                    $data_assign['delivery_state'] = State::getNameById($val);
                }else
                    $data_assign['delivery_'.$key] = $val;
            }
			
		}else
		{
            $deliveryaddress = Tools::jsonDecode(Tools::jsonEncode($invoice_address), true);
			foreach($deliveryaddress as $key=>$val){
			    if($key == 'id_state'){
                    $data_assign['delivery_state'] = State::getNameById($val);
                }else
                    $data_assign['delivery_'.$key] = $val;
            }
		}
        // new variable
        $orderstate = new OrderState((int)$this->order->current_state,(int)$id_lang);
        $state_color = Tools::getBrightness($orderstate->color) < 128 ? 'white' : 'black';
        $data_assign['order_status'] = '<table cellpadding="5" cellspacing="0"  style="float:left;background-color:'.$orderstate->color.';color:'.$state_color.';text-align:center;">
                                                <tbody>
                                                    <tr>
                                                        <td>'.$orderstate->name.'</td>
                                                    </tr>
                                                </tbody>
                                        </table>';
        // #new variable
        $carrier = new Carrier((int)($data_assign['id_carrier']), $data_assign['id_lang']);
        $carrier = Carrier::getCarrierByReference($carrier->id_reference);
        $data_assign['order_carrier_name'] = $carrier->name;
        $data_assign['order_carrier_transit'] = $carrier->delay;
        if(Tools::file_exists_no_cache(_PS_IMG_DIR_.'s/'.(int)$carrier->id.'.jpg')){
            $data_assign['order_carrier_logo'] = '<img src="'.$base_url.'img/s/'.(int)$carrier->id.'.jpg" />';
        }else
            $data_assign['order_carrier_logo'] = '';
        
        
		$customer = new Customer((int)$this->order->id_customer);
		$order_details = $this->order_invoice->getProducts();
		foreach ($order_details as $id => &$order_detail)
		{
		    if ($order_detail['reduction_amount_tax_excl'] > 0)
			{
				$order_detail['unit_price_tax_excl_before_specific_price'] = $order_detail['unit_price_tax_excl_including_ecotax'] + $order_detail['reduction_amount_tax_excl'];
			}
			elseif ($order_detail['reduction_percent'] > 0)
			{
				$order_detail['unit_price_tax_excl_before_specific_price'] = (100 * $order_detail['unit_price_tax_excl_including_ecotax']) / (100 - 15);
			}

			//$taxes = OrderDetail::getTaxListStatic($id);
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'order_detail_tax`
					WHERE `id_order_detail` = '.(int)$id;
		    $taxes = Db::getInstance()->executeS($sql);
			$tax_temp = array();
			foreach ($taxes as $tax)
            {
                $obj = new Tax((int)$tax['id_tax']);
				$tax_temp[] = sprintf($this->l('%1$s%2$s%%'), ($obj->rate + 0), '&nbsp;');
            }

			$order_detail['order_detail_tax'] = $taxes;
			$order_detail['order_detail_tax_label'] = implode(', ', $tax_temp);
            $order_detail['total_tax'] = $order_detail['total_price_tax_incl_including_ecotax'] - $order_detail['total_price_tax_excl_including_ecotax'];
		}
		unset($tax_temp);
		unset($order_detail);
		foreach ($order_details as &$order_detail)
		{
			if ($order_detail['image'] != null)
			{
				$name = 'product_mini_'.(int)$order_detail['product_id'].(isset($order_detail['product_attribute_id']) ? '_'.(int)$order_detail['product_attribute_id'] : '').'.jpg';
				$path = _PS_PROD_IMG_DIR_.$order_detail['image']->getExistingImgPath().'.jpg';
                if (Tools::file_exists_no_cache(_PS_TMP_IMG_DIR_.$name)) {
				    $infos = getimagesize(_PS_TMP_IMG_DIR_.$name);
                    if($infos[0] < 150){
                        ImageManager::thumbnail($path, $name, 150, 'jpg', true,true);
                    }
				}
				$order_detail['image_tag'] = preg_replace(
					'/\.*'.preg_quote(__PS_BASE_URI__, '/').'/',
					_PS_ROOT_DIR_.DIRECTORY_SEPARATOR,
					ImageManager::thumbnail($path, $name, 150, 'jpg', false),
					1
				);
				if (Tools::file_exists_no_cache(_PS_TMP_IMG_DIR_.$name))
					$order_detail['image_size'] = getimagesize(_PS_TMP_IMG_DIR_.$name);
				else
					$order_detail['image_size'] = false;
			}
            $product  = new Product((int)$order_detail['product_id'],false,$id_lang);
            $description_short = strip_tags($product->description_short);
            if (Tools::strlen($description_short) > 90) {
                $stringCut = Tools::substr($description_short, 0, 90);
                $description_short = Tools::substr($stringCut, 0, strrpos($stringCut, ' ')).'...'; 
            }
            $order_detail['description_short'] = $description_short;
            $order_detail['barcode'] = '';
            $code = $template->barcodeproductformat;
            $text = '';
            if($code == 'product_link'){
                $text = urlencode($product->getLink());
            }else{
                $text = urlencode($order_detail[$code]);
            }
            $filename = md5($template->barcodeproducttype.'_'.$text).'.png';
            if(!Tools::file_exists_no_cache(_PS_MODULE_DIR_.'gwadvancedinvoice/views/img/barcodes/'.$filename)){
                if($template->barcodeproducttype == 'qrcode'){
                    $qrcodeObj =  new QRCodeLib($text);
                    $im = $qrcodeObj->createImage(4,2);
                    imagepng($im,_PS_MODULE_DIR_.'gwadvancedinvoice/views/img/barcodes/'.$filename);
                }else{
                    $bacodeObj = new Barcode($text,$template->barcodeproducttype);
                    $bacodeObj->getBarcodePNG(_PS_MODULE_DIR_.'gwadvancedinvoice/views/img/barcodes/'.$filename,2,35,array(0,0,0)); 
                }
            }
            if(Tools::file_exists_no_cache(_PS_MODULE_DIR_.'gwadvancedinvoice/views/img/barcodes/'.$filename)){
                $order_detail['barcode'] = '<img src="'.$base_url.'modules/gwadvancedinvoice/views/img/barcodes/'.$filename.'"/>';
            }else{
                $order_detail['barcode'] = '';
            }
            if(version_compare(_PS_VERSION_,'1.6.0') == 1){
                $order_detail['unit_price_tax_excl_including_ecotax'] = $order_detail['unit_price_tax_excl'];
                $order_detail['total_price_tax_excl_including_ecotax'] = $order_detail['total_price_tax_excl'];
                
            }
		}
		unset($order_detail); // don't overwrite the last order_detail later

		$cart_rules = $this->order->getCartRules((int)$this->order_invoice->id);
		$free_shipping = false;
		foreach ($cart_rules as $key => $cart_rule)
		{
			if ($cart_rule['free_shipping'])
			{
				$free_shipping = true;
				$cart_rules[$key]['value_tax_excl'] -= $this->order_invoice->total_shipping_tax_excl;
				$cart_rules[$key]['value'] -= $this->order_invoice->total_shipping_tax_incl;
				if ($cart_rules[$key]['value'] == 0)
					unset($cart_rules[$key]);
			}
		}

		$product_taxes = 0;
		foreach ($this->order_invoice->getProductTaxesBreakdown($this->order) as $details)
			$product_taxes += $details['total_amount'];

		$product_discounts_tax_excl = $this->order_invoice->total_discount_tax_excl;
		$product_discounts_tax_incl = $this->order_invoice->total_discount_tax_incl;
		if ($free_shipping)
		{
			$product_discounts_tax_excl -= $this->order_invoice->total_shipping_tax_excl;
			$product_discounts_tax_incl -= $this->order_invoice->total_shipping_tax_incl;
		}

		$products_after_discounts_tax_excl = $this->order_invoice->total_products - $product_discounts_tax_excl;
		$products_after_discounts_tax_incl = $this->order_invoice->total_products_wt - $product_discounts_tax_incl;

		$shipping_tax_excl = $free_shipping ? 0 : $this->order_invoice->total_shipping_tax_excl;
		$shipping_tax_incl = $free_shipping ? 0 : $this->order_invoice->total_shipping_tax_incl;
		$shipping_taxes = $shipping_tax_incl - $shipping_tax_excl;

		$wrapping_taxes = $this->order_invoice->total_wrapping_tax_incl - $this->order_invoice->total_wrapping_tax_excl;
            
		$total_taxes = $this->order_invoice->total_paid_tax_incl - $this->order_invoice->total_paid_tax_excl;
        
		$footer = array(
			'products_before_discounts_tax_excl' => $this->order_invoice->total_products,
			'product_discounts_tax_excl' => $product_discounts_tax_excl,
			'products_after_discounts_tax_excl' => $products_after_discounts_tax_excl,
			'products_before_discounts_tax_incl' => $this->order_invoice->total_products_wt,
			'product_discounts_tax_incl' => $product_discounts_tax_incl,
			'products_after_discounts_tax_incl' => $products_after_discounts_tax_incl,
			'product_taxes' => $product_taxes,
			'shipping_tax_excl' => $shipping_tax_excl,
			'shipping_taxes' => $shipping_taxes,
			'shipping_tax_incl' => $shipping_tax_incl,
			'wrapping_tax_excl' => $this->order_invoice->total_wrapping_tax_excl,
			'wrapping_taxes' => $wrapping_taxes,
			'wrapping_tax_incl' => $this->order_invoice->total_wrapping_tax_incl,
			'ecotax_taxes' => $total_taxes - $product_taxes - $wrapping_taxes - $shipping_taxes,
			'total_taxes' => $total_taxes,
			'total_paid_tax_excl' => $this->order_invoice->total_paid_tax_excl,
			'total_paid_tax_incl' => $this->order_invoice->total_paid_tax_incl
		);
        if(version_compare(_PS_VERSION_,'1.6.0') == 1){
            foreach ($footer as $key => &$value)
    			$value = Tools::ps_round($value, 2);
		}else{
		      foreach ($footer as $key => $value)
    			$footer[$key] = Tools::ps_round($value, _PS_PRICE_COMPUTE_PRECISION_, $this->order->round_mode);
		}
        /**
		 * Need the $round_mode for the tests.
		 */
		$round_type = null;
        if(version_compare(_PS_VERSION_,'1.6.0') == 1){
            $round_type = 'total';
        }else
		switch ($this->order->round_type)
		{
			case Order::ROUND_TOTAL:
				$round_type = 'total';
				break;
			case Order::ROUND_LINE;
				$round_type = 'line';
				break;
			case Order::ROUND_ITEM:
				$round_type = 'item';
				break;
			default:
				$round_type = 'line';
				break;
		}

		$display_product_images = Configuration::get('PS_PDF_IMG_INVOICE');
		$tax_excluded_display = Group::getPriceDisplayMethod((int)$customer->id_default_group);

		$legal_free_text = Hook::exec('displayInvoiceLegalFreeText', array('order' => $this->order));
		if (!$legal_free_text)
			$legal_free_text = Configuration::get('PS_INVOICE_LEGAL_FREE_TEXT', (int)Context::getContext()->language->id, null, (int)$this->order->id_shop);


        $data = Tools::jsonDecode(Tools::jsonEncode($this->order), true);
        
        $data2 = Tools::jsonDecode(Tools::jsonEncode($this->order_invoice), true);
        foreach($data2 as $key=> $_data){
            $data[$key] = $_data;
        }
		$data3 = array(
            'logo' =>'<img src="'.$path_logo.'" />',
			'order' => $this->order,
            'order_invoice' => $this->order_invoice,
            'order_details' => $order_details,
			'cart_rules' => $cart_rules,
			'tax_excluded_display' => $tax_excluded_display,
			'display_product_images' => $display_product_images,
			'tax_tab' => $this->getTaxTabContent($template->choose_design),
			'customer' => $customer,
            'customeremail' => (isset($customer->email) ? $customer->email : ''),
            'customerfirstname' => (isset($customer->firstname) ? $customer->firstname : ''),
            'customerlastname' => (isset($customer->lastname) ? $customer->lastname : ''),
			'footer' => $footer,
			'ps_price_compute_precision' => _PS_PRICE_COMPUTE_PRECISION_,
			'round_type' => $round_type,
			'legal_free_text' => $legal_free_text,
		);
        foreach($data3 as $key=> $_data){
            $data[$key] = $_data;
        }
        foreach($data_assign as $key=> $_data){
            $data[$key] = $_data;
        }
		if (Tools::getValue('debug'))
			die(Tools::jsonEncode($data));
		
            $code = $template->barcodeformat;
            foreach($data as $key=>$_data){
                if(!is_array($_data) && !is_object($_data)){
                    $code = str_replace('{$'.$key.'}',$_data,$code);
                }
            }
            $filename = md5($template->barcodetype.'_'.$code).'.png';
            if(!Tools::file_exists_no_cache(_PS_MODULE_DIR_.'gwadvancedinvoice/views/img/barcodes/'.$filename)){
                if($template->barcodetype == 'qrcode'){
                    $qrcodeObj =  new QRCodeLib($code);
                    $im = $qrcodeObj->createImage(4,2);
                    imagepng($im,_PS_MODULE_DIR_.'gwadvancedinvoice/views/img/barcodes/'.$filename);
                }else{
                    $bacodeObj = new Barcode($code,$template->barcodetype);
                    $bacodeObj->getBarcodePNG(_PS_MODULE_DIR_.'gwadvancedinvoice/views/img/barcodes/'.$filename,2,35,array(0,0,0));
                }
            }
            if(Tools::file_exists_no_cache(_PS_MODULE_DIR_.'gwadvancedinvoice/views/img/barcodes/'.$filename)){
                $data['barcode_invoice'] = '<img src="'.$base_url.'modules/gwadvancedinvoice/views/img/barcodes/'.$filename.'"/>';
            }else{
                $data['barcode_invoice'] = '';
            }
        $data['invoice_date'] = date('d/m/Y',strtotime($data['invoice_date']));
        $data['date_add'] = date('d/m/Y',strtotime($data['date_add']));
        $data['custom_style'] = $template->customcss;
        if($template->template_config !=''){
            $template_configs = Tools::jsonDecode($template->template_config);
            foreach($template_configs as $key=>$template_config){
                $data[$key] = $template_config;
            }
        }
        $data['order_notes'] = nl2br($this->order->getFirstMessage());
        return $data;
    }
	public function getTaxTabContent($template='')
    {
        if(version_compare(_PS_VERSION_,'1.6.0') == 1){
            $address = new Address((int)$this->order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
			$tax_exempt = Configuration::get('VATNUMBER_MANAGEMENT')
								&& !empty($address->vat_number)
								&& $address->id_country != Configuration::get('VATNUMBER_COUNTRY');
			$carrier = new Carrier($this->order->id_carrier);
			
			$this->smarty->assign(array(
				'tax_exempt' => $tax_exempt,
				'use_one_after_another_method' => $this->order_invoice->useOneAfterAnotherTaxComputationMethod(),
				'product_tax_breakdown' => $this->order_invoice->getProductTaxesBreakdown(),
				'shipping_tax_breakdown' => $this->order_invoice->getShippingTaxesBreakdown($this->order),
				'ecotax_tax_breakdown' => $this->order_invoice->getEcoTaxTaxesBreakdown(),
				'wrapping_tax_breakdown' => $this->order_invoice->getWrappingTaxesBreakdown(),
				'order' => $this->order,
				'order_invoice' => $this->order_invoice,
				'carrier' => $carrier,
                'version'=>160
			));
        }else{
            $debug = Tools::getValue('debug');
    
            $address = new Address((int)$this->order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
            $tax_exempt = Configuration::get('VATNUMBER_MANAGEMENT')
                                && !empty($address->vat_number)
                                && $address->id_country != Configuration::get('VATNUMBER_COUNTRY');
            $carrier = new Carrier($this->order->id_carrier);
    
            $tax_breakdowns = $this->getTaxBreakdown();
            $display_tax_bases_in_breakdowns = Db::getInstance()->getValue('
        		SELECT od.`tax_computation_method`
        		FROM `'._DB_PREFIX_.'order_detail_tax` odt
        		LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = odt.`id_order_detail`)
        		WHERE od.`id_order` = '.(int)$this->id.'
        		AND od.`tax_computation_method` = '.(int)TaxCalculator::ONE_AFTER_ANOTHER_METHOD
    		);
            $data = array(
                'tax_exempt' => $tax_exempt,
                'use_one_after_another_method' => $this->order_invoice->useOneAfterAnotherTaxComputationMethod(),
                'display_tax_bases_in_breakdowns' => !$display_tax_bases_in_breakdowns,
                'product_tax_breakdown' => $this->order_invoice->getProductTaxesBreakdown($this->order),
                'shipping_tax_breakdown' => $this->order_invoice->getShippingTaxesBreakdown($this->order),
                'ecotax_tax_breakdown' => $this->order_invoice->getEcoTaxTaxesBreakdown(),
                'wrapping_tax_breakdown' => $this->order_invoice->getWrappingTaxesBreakdown(),
                'tax_breakdowns' => $tax_breakdowns,
                'order' => $debug ? null : $this->order,
                'order_invoice' => $debug ? null : $this->order_invoice,
                'carrier' => $debug ? null : $carrier,
                'version'=>161
            );
    
            if ($debug) {
                return $data;
            }
    
            $this->smarty->assign($data);
            
        }
        if($template !=''){
            if(Tools::file_exists_no_cache(_PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/base/'.$template.'/en/tax-tab.tpl')){
                return $this->smarty->fetch(_PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/base/'.$template.'/en/tax-tab.tpl');
            }
        } 
        return $this->smarty->fetch($this->getTemplate('invoice.tax-tab'));
    }
    protected function getTaxBreakdown()
    {
        $breakdowns = array(
            'product_tax' => $this->order_invoice->getProductTaxesBreakdown($this->order),
            'shipping_tax' => $this->order_invoice->getShippingTaxesBreakdown($this->order),
            'ecotax_tax' => $this->order_invoice->getEcoTaxTaxesBreakdown(),
            'wrapping_tax' => $this->order_invoice->getWrappingTaxesBreakdown(),
        );

        foreach ($breakdowns as $type => $bd) {
            if (empty($bd)) {
                unset($breakdowns[$type]);
            }
        }

        if (empty($breakdowns)) {
            $breakdowns = false;
        }

        if (isset($breakdowns['product_tax'])) {
            foreach ($breakdowns['product_tax'] as &$bd) {
                $bd['total_tax_excl'] = $bd['total_price_tax_excl'];
            }
        }

        if (isset($breakdowns['ecotax_tax'])) {
            foreach ($breakdowns['ecotax_tax'] as &$bd) {
                $bd['total_tax_excl'] = $bd['ecotax_tax_excl'];
                $bd['total_amount'] = $bd['ecotax_tax_incl'] - $bd['ecotax_tax_excl'];
            }
        }
        return $breakdowns;
    }
    protected function setShopId()
	{
		if (isset($this->order) && Validate::isLoadedObject($this->order))
			$id_shop = (int)$this->order->id_shop;
		else
			$id_shop = (int)Context::getContext()->shop->id;

		$this->shop = new Shop($id_shop);
		if (Validate::isLoadedObject($this->shop))
			Shop::setContext(Shop::CONTEXT_SHOP, (int)$this->shop->id);
	}
}
?>