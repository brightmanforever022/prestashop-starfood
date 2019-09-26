<?php
/**
* The file is controller. Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/

include_once(_PS_MODULE_DIR_ . 'gwadvancedinvoice/model/gwadvancedinvoicetemplateModel.php');
include_once(_PS_MODULE_DIR_.'/gwadvancedinvoice/libs/barcode.php');
include_once(_PS_MODULE_DIR_.'/gwadvancedinvoice/libs/qrcode.php');
class AdminGwadvancedinvoicetemplateController extends ModuleAdminController
{
    public function __construct()
    {
        $this->className = 'gwadvancedinvoicetemplateModel';
        $this->table = 'gwadvancedinvoicetemplate';
        $this->meta_title = $this->l('Invoice builder pro template');
        $this->deleted = false;
        $this->explicitSelect = true;
        $this->context = Context::getContext();
        $this->lang = true;
        $this->bootstrap = true;
        $this->_defaultOrderBy = 'id_gwadvancedinvoicetemplate';
        $this->filter = true;
        if (Shop::isFeatureActive()) {
            Shop::addTableAssociation($this->table, array('type' => 'shop'));
        }
        $this->position_identifier = 'id_gwadvancedinvoicetemplate';
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowAction('duplicate');
        
        $this->fields_list = array(
            'id_gwadvancedinvoicetemplate' => array(
                'title' => $this->l('ID'),
                'type' => 'int',
                'width' => 'auto',
                'orderby' => false),
            'title' => array(
                'title' => $this->l('Name'),
                'width' => 'auto',
                'orderby' => false),
            
            'active' => array(
                'title' => $this->l('Status'),
                'width' => 'auto',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false),
            );
        parent::__construct();
    }
    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryPlugin('colorpicker');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/admin/colResizable-1.5.min.js');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/admin/gwadvancedinvoice.js');
        return true;
    }
    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('Advanced Invoice Template Builder');
        $this->toolbar_title[] = $this->l('Manage Templates');
    }
    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn = array(

            'cogs' => array(

                'href' => $this->context->link->getAdminLink('AdminGwadvancedinvoiceconfig'),

                'desc' => $this->l('Genaral Settings', null, null, false),

                'icon' => 'process-icon-cogs'),
            'about' => array(

                'href' => $this->context->link->getAdminLink('AdminGwadvancedinvoiceabout'),

                'desc' => $this->l('Document', null, null, false),

                'icon' => 'process-icon-modules-list'),

            );
        parent::initPageHeaderToolbar();
    }
    public function postProcess()
	{
	   if (Tools::isSubmit('previewTemplate')){
            $pdf_renderer = new PDFGenerator((bool)Configuration::get('PS_PDF_USE_CACHE'), Tools::getValue('pageorientation'));
	        $useSSL = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
    		$protocol_content = ($useSSL) ? 'https://' : 'http://';
            $base_url = $protocol_content.Tools::getHttpHost().__PS_BASE_URI__;
            
            $layout = time().rand(1,999999999);
            if(version_compare(_PS_VERSION_,'1.6.0') == 1){
                $datas = $_POST+$_GET;
                // version left than 1.6.4->1.6.9 missing function Tools::getAllValues();
            }else{
                $datas = Tools::getAllValues();
            }
            $id_language = Context::getContext()->language->id;
            if(Tools::getValue('previewTemplate'))
                $id_language =  Tools::getValue('previewTemplate');
            
            $styles = '';
            $temp = $datas['choose_design'];
            $language = new Language($id_language);
            if(Tools::file_exists_no_cache(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/'.$language->iso_code.'/styles.tpl')){
                $styles = Tools::file_get_contents(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/'.$language->iso_code.'/styles.tpl');
            }elseif(Tools::file_exists_no_cache(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/en/styles.tpl')){
                $styles = Tools::file_get_contents(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/en/styles.tpl');
            }
            $discounttype = Tools::getValue('discountval');
            if($discounttype != 'exclude') $discounttype = 'include';
            gwadvancedinvoicetemplateModel::writeTemplate('styles.tpl','../modules/'.$this->module->name.'/views/templates/admin/tpltemplates/previews/'.$layout,$styles);
            gwadvancedinvoicetemplateModel::writeTemplate('product_list.tpl','../modules/'.$this->module->name.'/views/templates/admin/tpltemplates/previews/'.$layout,gwadvancedinvoicetemplateModel::getProductListTpl($datas['widthtitle'],$datas['colums_title_'.$id_language],$datas['colums_content_'.$id_language],$datas['colums_align_'.$id_language],$temp,$language->iso_code,$discounttype));
            gwadvancedinvoicetemplateModel::writeTemplate('template.tpl','../modules/'.$this->module->name.'/views/templates/admin/tpltemplates/previews/'.$layout,str_replace('{displayPrice:$','{displayPrice currency=$order->id_currency price=$',$datas['invoice_'.$id_language]));
            
            /*
            if(Tools::getValue('header_'.$id_language)){
                gwadvancedinvoicetemplateModel::writeTemplate('header.tpl','../modules/'.$this->module->name.'/views/templates/admin/tpltemplates/previews/'.$layout,Tools::getValue('header_'.$id_language));
            }
            if(Tools::getValue('footer_'.$id_language)){
                gwadvancedinvoicetemplateModel::writeTemplate('footer.tpl','../modules/'.$this->module->name.'/views/templates/admin/tpltemplates/previews/'.$layout,Tools::getValue('footer_'.$id_language));
            }
            */
            //fix in vs 1.1.1
            $header_content = '';
            if(Tools::getValue('header_'.$id_language)) $header_content = Tools::getValue('header_'.$id_language);
            if(Tools::getValue('activeheader')){
                gwadvancedinvoicetemplateModel::writeTemplate('header.tpl','../modules/'.$this->module->name.'/views/templates/admin/tpltemplates/previews/'.$layout,$header_content);
            }
            $footer_content = '';
            if(Tools::getValue('footer_'.$id_language)) $footer_content = Tools::getValue('footer_'.$id_language);
            if(Tools::getValue('activefooter')){
                gwadvancedinvoicetemplateModel::writeTemplate('footer.tpl','../modules/'.$this->module->name.'/views/templates/admin/tpltemplates/previews/'.$layout,$footer_content);
            }
            //#fix in vs 1.1.1
            
            $data = gwadvancedinvoicetemplateModel::getDataDemo();
            
            $template_config = Tools::getValue('template_config');
            foreach($template_config as $key=>$template_config){
                $data[$key] = $template_config;
            }
            $code = $datas['barcodeproductformat'];
            foreach($data['order_details'] as &$order_details){
                $text = '';
                if($code == 'product_link'){
                    $text = urlencode($base_url);
                }else{
                    $text = urlencode($order_details[$code]);
                }
                $filename = md5($datas['barcodeproducttype'].'_'.$text).'.png';
                if(!Tools::file_exists_no_cache(_PS_MODULE_DIR_.'gwadvancedinvoice/views/img/barcodes/'.$filename)){
                    if($datas['barcodeproducttype'] == 'qrcode'){
                        $qrcodeObj =  new QRCodeLib($text);
                        $im = $qrcodeObj->createImage(4,2);
                        imagepng($im,_PS_MODULE_DIR_.'gwadvancedinvoice/views/img/barcodes/'.$filename);
                    }else{
                        $bacodeObj = new Barcode($text,$datas['barcodeproducttype']);
                        $bacodeObj->getBarcodePNG(_PS_MODULE_DIR_.'gwadvancedinvoice/views/img/barcodes/'.$filename,2,35,array(0,0,0)); 
                    }
                }
                if(Tools::file_exists_no_cache(_PS_MODULE_DIR_.'gwadvancedinvoice/views/img/barcodes/'.$filename)){
                    $order_details['barcode'] = '<img src="'.$base_url.'modules/gwadvancedinvoice/views/img/barcodes/'.$filename.'"/>';
                }else{
                    $order_details['barcode'] = '';
                }
            }
            Context::getContext()->smarty->assign($data);
            $products_list_temp =  _PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/previews/'.$layout.'/product_list.tpl';
            $products_list = Context::getContext()->smarty->fetch($products_list_temp);
            $data['products_list'] = $products_list;
            $code = '1234567890';
            $filename = md5($datas['barcodetype'].'_'.$code).'.png';
            if(!Tools::file_exists_no_cache(_PS_MODULE_DIR_.'gwadvancedinvoice/views/img/barcodes/'.$filename)){
                if($datas['barcodetype'] == 'qrcode'){
                    $qrcodeObj =  new QRCodeLib($code);
                    $im = $qrcodeObj->createImage(4,2);
                    imagepng($im,_PS_MODULE_DIR_.'gwadvancedinvoice/views/img/barcodes/'.$filename);
                }else{
                    $bacodeObj = new Barcode($code,$datas['barcodetype']);
                    $bacodeObj->getBarcodePNG(_PS_MODULE_DIR_.'gwadvancedinvoice/views/img/barcodes/'.$filename,2,35,array(0,0,0)); 
                }
            }
            
            if(Tools::file_exists_no_cache(_PS_MODULE_DIR_.'gwadvancedinvoice/views/img/barcodes/'.$filename)){
                $data['barcode_invoice'] = '<img src="'.$base_url.'modules/gwadvancedinvoice/views/img/barcodes/'.$filename.'"/>';
            }else{
                $data['barcode_invoice'] = '';
            }
            $data['custom_style'] = $datas['customcss'];
            Context::getContext()->smarty->assign($data);
            $pdf_renderer->setCurOrientation($datas['pagesize'],$datas['pageorientation']);
            if(Tools::getValue('rtl')){
                $pdf_renderer->setRTL((bool)Tools::getValue('rtl'));
            }
            
            $style = Context::getContext()->smarty->fetch(_PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/previews/'.$layout.'/styles.tpl');
            if(Tools::getValue('activeheader')){
                $temp =  _PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/previews/'.$layout.'/header.tpl';
                $pdf_renderer->createHeader('<style>'.strip_tags($style).'</style>'.Context::getContext()->smarty->fetch($temp));
                $pdf_renderer->SetPrintHeader(true);
            }else
                $pdf_renderer->SetPrintHeader(false);
            
            
            $temp =  _PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/previews/'.$layout.'/template.tpl';
            
            // fix fdf can't load image
            
            $content = Context::getContext()->smarty->fetch($temp);
            
            $content = preg_replace_callback("/(<img[^>]*src *= *[\"']?)([^\"']*)/i",
                function ($matches) {
                    $useSSL = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? true : false;
                    $protocol_content = ($useSSL) ? 'https://' : 'http://';
                    $base_url = $protocol_content.Tools::getHttpHost().__PS_BASE_URI__;
                    $link = str_replace(
    						$base_url,
    						_PS_ROOT_DIR_.DIRECTORY_SEPARATOR,
    						$matches['2']);
                  return $matches[1] . $link;
                }
            , $content);
            $pdf_renderer->createContent('<style>'.strip_tags($style).'</style>'.$content);
            if(Tools::getValue('activefooter')){
                
                $temp =  _PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/previews/'.$layout.'/footer.tpl';
                $pdf_renderer->createFooter('<style>'.strip_tags($style).'</style>'.Context::getContext()->smarty->fetch($temp));
                $pdf_renderer->SetPrintFooter(true);
            }else
                $pdf_renderer->SetPrintFooter(false);
                
            $pdf_renderer->writePageGw(Tools::getValue('mgheader'),Tools::getValue('mgfooter'),Tools::getValue('mgcontent'));
            $watermank_img = '';
            $type = Tools::strtolower(Tools::substr(strrchr($_FILES['watermark_'.$id_language]['name'], '.'), 1));
			$imagesize = @getimagesize($_FILES['watermark_'.$id_language]['tmp_name']);
            if (isset($_FILES['watermark_'.$id_language]) &&
				isset($_FILES['watermark_'.$id_language]['tmp_name']) &&
				!empty($_FILES['watermark_'.$id_language]['tmp_name']) &&
				!empty($imagesize) &&
				in_array(
					Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1)), array(
						'jpg',
						'gif',
						'jpeg',
						'png'
					)
				) &&
				in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
			)
			{
			    $error = false;
				$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
				$salt = sha1(microtime());
				if (ImageManager::validateUpload($_FILES['watermark_'.$id_language]))
					$error =  true;
				elseif (!$temp_name || !move_uploaded_file($_FILES['watermark_'.$id_language]['tmp_name'], $temp_name))
					$error =  true;
                elseif (!ImageManager::resize($temp_name, _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/previews/'.$layout.'/'.$salt.'_'.$_FILES['watermark_'.$id_language]['name'], null, null, $type))
                    $error =  true;
				if(!$error) $watermank_img=$base_url.'modules/'.$this->module->name.'/views/templates/admin/tpltemplates/previews/'.$layout.'/'.$salt.'_'.$_FILES['watermark_'.$id_language]['name'];
			}else{
			     if(Tools::getValue('id_gwadvancedinvoicetemplate')){
                    $template = new gwadvancedinvoicetemplateModel((int)Tools::getValue('id_gwadvancedinvoicetemplate'));
                    if($template->watermark){
                        if(!is_dir(_PS_MODULE_DIR_.$this->module->name.'/views/img/watermark/'.$template->watermark[$id_language]))
                            if(file_exists(_PS_MODULE_DIR_.$this->module->name.'/views/img/watermark/'.$template->watermark[$id_language]))
                                $watermank_img = $base_url.'modules/gwadvancedinvoice/views/img/watermark/'.$template->watermark[$id_language];
                    }
                }
			}
            $watermank_text = Tools::getValue('watermarktext_'.$id_language);
            $watermank_font = Tools::getValue('watermarkfont_'.$id_language);
            $watermank_size = Tools::getValue('watermarksize_'.$id_language);
            if($watermank_img !='' || $watermank_text !=''){
                $pdf_renderer->addWaterMark($watermank_text,$watermank_img,45,0,'0.1',$watermank_font,$watermank_size);
            }   
            if (ob_get_level() && ob_get_length() > 0)
				ob_clean();
            $dir = _PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/previews/'.$layout;
            gwadvancedinvoicetemplateModel::delTree($dir);
            $pdf_renderer->renderInvoice('preview.pdf', 'I');
            die();
	   }elseif (Tools::isSubmit('chooseTemplate')){
            if(Tools::getValue('choose_design') != null && Tools::getValue('choose_design') !=''){
                $link = new Link();
                $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
                $_link = $protocol_link.Tools::getShopDomainSsl().__PS_BASE_URI__.basename(_PS_ADMIN_DIR_ ).'/'.$link->getAdminLink('AdminGwadvancedinvoicetemplate').'&addgwadvancedinvoicetemplate&choose_design='.Tools::getValue('choose_design').'&pagesize='.Tools::getValue('pagesize');
                Tools::redirectLink($_link);
            }
        }
        elseif (Tools::isSubmit('saveTemplate') || Tools::isSubmit('submitAddgwadvancedinvoicetemplateAndStay')){
            $layout = time();
            $template = null;
            if(Tools::getValue('id_gwadvancedinvoicetemplate')){
                $template = new gwadvancedinvoicetemplateModel((int)Tools::getValue('id_gwadvancedinvoicetemplate'));
                if($template->layout != '' && $template->layout){
                    $layout = $template->layout;
                }
            }
            $_POST['layout'] = $layout;
            $_POST['template_config'] = (Tools::getValue('template_config') !='') ? Tools::jsonEncode(Tools::getValue('template_config')) : '';
            $languages = Language::getLanguages(false);
            if(!Tools::getValue('checkBoxShopAsso_gwadvancedinvoicetemplate')){
                $_POST['checkBoxShopAsso_gwadvancedinvoicetemplate'] = array(Context::getContext()->shop->id);
            }
            $temp = Tools::getValue('choose_design');
            foreach(Tools::getValue('checkBoxShopAsso_gwadvancedinvoicetemplate') as $shop){
        		foreach ($languages as $lang)
        		{
        		      // fix utf8 title error when json_encode
        		      $titles = Tools::getValue('colums_title_'.$lang['id_lang']);
                      if($titles)
                        foreach($titles as &$title)
                            $title = htmlentities($title);
                      // #fix utf8 title error when json_encode
        		      $_POST['productcolumns_'.$lang['id_lang']] = Tools::jsonEncode(
                        array(
                        'widthtitle'=>Tools::getValue('widthtitle'),
                        'title'=>$titles,//htmlentities(Tools::getValue('colums_title_'.$lang['id_lang'])),
                        'content'=>Tools::getValue('colums_content_'.$lang['id_lang']),
                        'align'=>Tools::getValue('colums_align_'.$lang['id_lang'])
                        )
                      );
                      
                      $styles = '';
                        if(Tools::file_exists_no_cache(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/'.$lang['iso_code'].'/styles.tpl')){
                            $styles = Tools::file_get_contents(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/'.$lang['iso_code'].'/styles.tpl');
                        }elseif(Tools::file_exists_no_cache(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/en/styles.tpl')){
                            $styles = Tools::file_get_contents(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/en/styles.tpl');
                        }
                      $discounttype = Tools::getValue('discountval');
                      if($discounttype != 'exclude') $discounttype = 'include';
                      gwadvancedinvoicetemplateModel::writeTemplate('styles.tpl','../modules/'.$this->module->name.'/views/templates/admin/tpltemplates/customize/'.$shop.'/'.$layout.'/'.$lang['iso_code'],$styles);
                      gwadvancedinvoicetemplateModel::writeTemplate('product_list.tpl','../modules/'.$this->module->name.'/views/templates/admin/tpltemplates/customize/'.$shop.'/'.$layout.'/'.$lang['iso_code'],gwadvancedinvoicetemplateModel::getProductListTpl(Tools::getValue('widthtitle'),Tools::getValue('colums_title_'.$lang['id_lang']),Tools::getValue('colums_content_'.$lang['id_lang']),Tools::getValue('colums_align_'.$lang['id_lang']),$temp,$lang['iso_code'],$discounttype));
                      gwadvancedinvoicetemplateModel::writeTemplate('template.tpl','../modules/'.$this->module->name.'/views/templates/admin/tpltemplates/customize/'.$shop.'/'.$layout.'/'.$lang['iso_code'],str_replace('{displayPrice:$','{displayPrice currency=$order->id_currency price=$',Tools::getValue('invoice_'.$lang['id_lang'])));
        		      /*
                      if(Tools::getValue('header_'.$lang['id_lang'])){
                            gwadvancedinvoicetemplateModel::writeTemplate('header.tpl','../modules/'.$this->module->name.'/views/templates/admin/tpltemplates/customize/'.$shop.'/'.$layout.'/'.$lang['iso_code'],Tools::getValue('header_'.$lang['id_lang']));
                      }
                      if(Tools::getValue('footer_'.$lang['id_lang'])){
                            gwadvancedinvoicetemplateModel::writeTemplate('footer.tpl','../modules/'.$this->module->name.'/views/templates/admin/tpltemplates/customize/'.$shop.'/'.$layout.'/'.$lang['iso_code'],Tools::getValue('footer_'.$lang['id_lang']));
                      }
                      */
                      //fix in vs 1.1.1
                        $header_content = '';
                        if(Tools::getValue('header_'.$lang['id_lang'])) $header_content = Tools::getValue('header_'.$lang['id_lang']);
                        if(Tools::getValue('activeheader')){
                            gwadvancedinvoicetemplateModel::writeTemplate('header.tpl','../modules/'.$this->module->name.'/views/templates/admin/tpltemplates/customize/'.$shop.'/'.$layout.'/'.$lang['iso_code'],$header_content);
                        }
                        $footer_content = '';
                        if(Tools::getValue('footer_'.$lang['id_lang'])) $footer_content = Tools::getValue('footer_'.$lang['id_lang']);
                        if(Tools::getValue('activefooter')){
                            gwadvancedinvoicetemplateModel::writeTemplate('footer.tpl','../modules/'.$this->module->name.'/views/templates/admin/tpltemplates/customize/'.$shop.'/'.$layout.'/'.$lang['iso_code'],$footer_content);
                        }
                      //#fix in vs 1.1.1
                }
                
            }
            foreach ($languages as $language)
      		{
      		    if(Tools::getValue('watermark_remove_'.$language['id_lang'])){
      		        if($template && isset($template->watermark[$language['id_lang']])){
      		            unlink(_PS_MODULE_DIR_.$this->module->name.'/views/img/watermark/'.$template->watermark[$language['id_lang']]);
      		            $_POST['watermark_'.$language['id_lang']] = '';
                      }
                }
                $type = Tools::strtolower(Tools::substr(strrchr($_FILES['watermark_'.$language['id_lang']]['name'], '.'), 1));
				$imagesize = @getimagesize($_FILES['watermark_'.$language['id_lang']]['tmp_name']);
                if (isset($_FILES['watermark_'.$language['id_lang']]) &&
					isset($_FILES['watermark_'.$language['id_lang']]['tmp_name']) &&
					!empty($_FILES['watermark_'.$language['id_lang']]['tmp_name']) &&
					!empty($imagesize) &&
					in_array(
						Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1)), array(
							'jpg',
							'gif',
							'jpeg',
							'png'
						)
					) &&
					in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
				)
				{
					$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
					$salt = sha1(microtime());
					if ($error = ImageManager::validateUpload($_FILES['watermark_'.$language['id_lang']]))
						$this->errors[] = $error;
					elseif (!$temp_name || !move_uploaded_file($_FILES['watermark_'.$language['id_lang']]['tmp_name'], $temp_name))
						return false;
					elseif (!ImageManager::resize($temp_name, _PS_MODULE_DIR_.$this->module->name.'/views/img/watermark/'.$salt.'_'.$_FILES['watermark_'.$language['id_lang']]['name'], null, null, $type))
						$this->errors[] = $this->displayError($this->l('An error occurred during the image upload process.'));
					if (isset($temp_name))
						@unlink($temp_name);
                    if($template && isset($template->watermark[$language['id_lang']])){
      		            unlink(_PS_MODULE_DIR_.$this->module->name.'/views/img/watermark/'.$template->watermark[$language['id_lang']]);
                    }
                    $_POST['watermark_'.$language['id_lang']] = $salt.'_'.$_FILES['watermark_'.$language['id_lang']]['name'];
				}
			}       
            parent::postProcess(true);
        }elseif (Tools::isSubmit('deletegwadvancedinvoicetemplate')){
            $id_shop_group = Shop::getContextShopGroupID();
		    $id_shop = Shop::getContextShopID();
            $chossed_temp = Configuration::get('GWADVANCEDINVOICE_TEMPLATE', null, $id_shop_group, $id_shop);
            if($chossed_temp == Tools::getValue('id_gwadvancedinvoicetemplate')){
                Configuration::updateValue('GWADVANCEDINVOICE_TEMPLATE', '', false, $id_shop_group, $id_shop);
            }
            
            $layout = '';
            if(Tools::getValue('id_gwadvancedinvoicetemplate')){
                $template = new gwadvancedinvoicetemplateModel((int)Tools::getValue('id_gwadvancedinvoicetemplate'));
                if($template->layout != ''){
                    $layout = $template->layout;                     
                }
            }
            if($layout !=''){
                $shops = Shop::getContextListShopID();
                foreach ($shops as $shop_id)
                {
                    $dir = _PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/customize/'.$shop_id.'/'.$layout;
                    if(Tools::file_exists_no_cache($dir)){
                        gwadvancedinvoicetemplateModel::delTree($dir);
                    }
                    
                }
                $dir = _PS_MODULE_DIR_.'gwadvancedinvoice/views/img/imgtemplates/'.$layout;
                if(Tools::file_exists_no_cache($dir)){
                    gwadvancedinvoicetemplateModel::delTree($dir);
                }
                
            }
            parent::postProcess(true);
        }elseif (Tools::isSubmit('duplicategwadvancedinvoicetemplate')){
            $id = (int)Tools::getValue('id_gwadvancedinvoicetemplate');
            $layout = time();
            $template = new gwadvancedinvoicetemplateModel((int)$id);
            if(Validate::isLoadedObject($template)){
                $template_new = clone $template;
                $template_new->id_gwadvancedinvoicetemplate = null;
                $template_new->id = null;
                $template_new->layout = $layout;
                foreach($template_new->title as &$title){
                    $title .='-'.$this->l('Copy');
                }
                $shops = Shop::getContextListShopID();
                foreach ($shops as $shop_id){
                    $dir = _PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/customize/'.$shop_id;
                    gwadvancedinvoicetemplateModel::copyTree($dir.'/'.$template->layout,$dir.'/'.$template_new->layout);
                }
                $template_new->save();
            }
            parent::postProcess(true);
        }
        else
			parent::postProcess(true);
	}
    
    public function renderForm()
    {
        if (!Tools::isSubmit('choose_design') && !Tools::isSubmit('id_gwadvancedinvoicetemplate'))
		{
		      $this->fields_form = array(
                    'legend' => array('title' => $this->l('Invoice Template'), 'icon' => 'icon-cogs'),
                    'input' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Page size'),
                            'hint' => $this->l('Select a page size for your invoice template.'),
                            'name' => 'pagesize',
                            'required' => false,
                            'lang' => false,
                            'class' => 'pagesize pagesize_ajaxcall',
                            'options' => array(
                                'query' => gwadvancedinvoicetemplateModel::getAllPageSize(),
                                'id' => 'value',
                                'name' => 'name'
                            )),          
                        array(
                            'type' => 'choose_design',
                            'label' => $this->l('Select design'),
                            'hint' => $this->l('You have to select "Page size" then you can select a default design (default template)'),
                            'name' => 'choose_design',
                            'required' => true),
                    ),
                    'submit' => array(
                                    'title' => $this->l('Next'), 
                                    'name' =>'chooseTemplate',
                                    'class'=>'btn btn-default pull-right chooseTemplate_ajaxcall'
                                    ),
                );
                return parent::renderForm();
                
		}else{
            $fields = array();
            $choose_design = '';
		    if(!Tools::isSubmit('id_gwadvancedinvoicetemplate')){
		        $choose_design =   Tools::getValue('choose_design');
    		    $base_design = Module::getInstanceByName('gwadvancedinvoice')->getBaseTemplateConfig($choose_design);
                $fields['template_config'] = $base_design['template_config'];
                $fields['pagesize'] = Tools::getValue('pagesize');
                $fields['barcodeformat'] = $base_design['barcodeformat'];
                
                $languages = Language::getLanguages(false);
                $useSSL = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
        		$protocol_content = ($useSSL) ? 'https://' : 'http://';
                $base_url = $protocol_content.Tools::getHttpHost().__PS_BASE_URI__;
        		foreach ($languages as $lang)
        		{
        		    $fields['invoice'][$lang['id_lang']] = '';
        		    if(Tools::getValue('choose_design') !=''){
                        $temp = Tools::getValue('choose_design');
                        if(Tools::file_exists_no_cache(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/'.$lang['iso_code'].'/template.tpl')){
                            $template = $this->remove_tpl_comments(Tools::file_get_contents(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/'.$lang['iso_code'].'/template.tpl'));
                            $fields['invoice'][$lang['id_lang']] = str_replace('{$tpltemplate_dir}',$base_url.'modules/'.$this->module->name,str_replace('|escape:\'htmlall\':\'UTF-8\'','',$template));
                        }elseif(Tools::file_exists_no_cache(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/en/template.tpl')){
                            $template = $this->remove_tpl_comments(Tools::file_get_contents(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/en/template.tpl'));
                            $fields['invoice'][$lang['id_lang']] = str_replace('{$tpltemplate_dir}',$base_url.'modules/'.$this->module->name,str_replace('|escape:\'htmlall\':\'UTF-8\'','',$template));
                        }
                        if($base_design['activeheader']){
                            if(Tools::file_exists_no_cache(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/'.$lang['iso_code'].'/header.tpl')){
                                $template = $this->remove_tpl_comments(Tools::file_get_contents(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/'.$lang['iso_code'].'/header.tpl'));
                                $fields['header'][$lang['id_lang']] = str_replace('{$tpltemplate_dir}',$base_url.'modules/'.$this->module->name,str_replace('|escape:\'htmlall\':\'UTF-8\'','',$template));
                            }elseif(Tools::file_exists_no_cache(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/en/header.tpl')){
                                $template = $this->remove_tpl_comments(Tools::file_get_contents(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/en/header.tpl'));
                                $fields['header'][$lang['id_lang']] = str_replace('{$tpltemplate_dir}',$base_url.'modules/'.$this->module->name,str_replace('|escape:\'htmlall\':\'UTF-8\'','',$template));
                            }
                        }
                        if($base_design['activefooter']){
                            if(Tools::file_exists_no_cache(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/'.$lang['iso_code'].'/footer.tpl')){
                                $template = $this->remove_tpl_comments(Tools::file_get_contents(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/'.$lang['iso_code'].'/footer.tpl'));
                                $fields['footer'][$lang['id_lang']] = str_replace('{$tpltemplate_dir}',$base_url.'modules/'.$this->module->name,str_replace('|escape:\'htmlall\':\'UTF-8\'','',$template));
                            }elseif(Tools::file_exists_no_cache(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/en/footer.tpl')){
                                $template = $this->remove_tpl_comments(Tools::file_get_contents(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/en/footer.tpl'));
                                $fields['footer'][$lang['id_lang']] = str_replace('{$tpltemplate_dir}',$base_url.'modules/'.$this->module->name,str_replace('|escape:\'htmlall\':\'UTF-8\'','',$template));
                            }
                        }
                    }
        		}
                foreach($base_design as $key=>$base){
                    $fields[$key] = $base;
                }
                $languages = Language::getLanguages(false);
        		$fields['productcolumns'] = array();
                foreach ($languages as $lang)
        		{
        		    $fields['productcolumns'][$lang['id_lang']] = Tools::jsonDecode(Tools::jsonEncode($base_design['productcolumns']));
        		}
                $fields['id_language'] = $this->context->language->id;
                $fields['mgheader'] = $base_design['mgheader'];
                $fields['mgcontent'] = $base_design['mgcontent'];
                $fields['mgfooter'] = $base_design['mgfooter'];
                $fields['pagesize'] = Tools::getValue('pagesize');
                
            }else{
                $template = new gwadvancedinvoicetemplateModel((int)Tools::getValue('id_gwadvancedinvoicetemplate'));
                $choose_design = $template->choose_design;
                $base_design = Module::getInstanceByName('gwadvancedinvoice')->getBaseTemplateConfig($choose_design);
                $_template_config = Tools::jsonDecode($template->template_config);
                foreach($base_design['template_config'] as &$template_config){
                    
                    if(isset($_template_config->{$template_config['name']}) && $_template_config->{$template_config['name']} !=''){
                        $template_config['value'] = $_template_config->{$template_config['name']};
                    }
                }
                $fields['template_config'] = $base_design['template_config'];
                $languages = Language::getLanguages(false);
        		$fields['productcolumns'] = array();
                foreach ($languages as $lang)
        		{
  		            // fix utf8 title error when json_encode
                    $productcolumns = Tools::jsonDecode($template->productcolumns[$lang['id_lang']]);
        		    if(isset($productcolumns->title))
                        foreach($productcolumns->title as &$title)
                            $title = html_entity_decode($title);
                    // #fix utf8 title error when json_encode
                    $fields['productcolumns'][$lang['id_lang']] = $productcolumns;
                }
                $fields['id_language'] = $this->context->language->id;
                $fields['mgheader'] = $template->mgheader;
                $fields['mgcontent'] = $template->mgcontent;
                $fields['mgfooter'] = $template->mgfooter;
                $fields['watermarktext'] = $template->watermarktext;
                $fields['watermarkfont'] = $template->watermarkfont;
                $fields['watermarksize'] = $template->watermarksize;
            }
            $fields['fonts'] = gwadvancedinvoicetemplateModel::getListFont();
            $fields['image_baseurl'] = __PS_BASE_URI__.'modules/'.$this->module->name.'/views/img/watermark/';
            $this->fields_value = $fields;
            $this->fields_form = array(
                'legend' => array('title' => $this->l('Invoice Template'), 'icon' => 'icon-cogs'),
                'input' => array(
                    array(
                        'type' => 'choose_design',
                        'label' => $this->l('Design'),
                        'name' => 'choose_design',
                        'hint' => $this->l('This is defaul design (template) that you selected in previous step. You can not choose other default design in this step'),
                        'choosed' =>$base_design,
                        'required' => true),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Name'),
                        'hint' => $this->l('This is name of your template. It will help you easier to manage multi templates.'),
                        'name' => 'title',
                        'size' => 255,
                        'required' => true,
                        'lang' => true),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Page Orientation'),
                        'name' => 'pageorientation',
                        'required' => false,
                        'lang' => false,
                        'class' => 'pageorientation',
                        'options' => array(
                            'query' => gwadvancedinvoicetemplateModel::getPageOrientation(),
                            'id' => 'value',
                            'name' => 'name'
                        )),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Page Size'),
                        'name' => 'pagesize',
                        'required' => false,
                        'lang' => false,
                        'class' => 'pagesize',
                        'options' => array(
                            'query' => gwadvancedinvoicetemplateModel::getPageSize($choose_design),
                            'id' => 'value',
                            'name' => 'name'
                        )
                        ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'required' => false,
                        'is_bool' => true,
                        'values' => array(array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Active')), array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Inactive')))),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Right to Left'),
                        'name' => 'rtl',
                        'required' => false,
                        'is_bool' => true,
                        'hint' => $this->l('Yes if you want to create invoice for Right to Left (RTL) language'),
                        'values' => array(array(
                                'id' => 'rtl_on',
                                'value' => 1,
                                'label' => $this->l('Yes')), array(
                                'id' => 'rtl_off',
                                'value' => 0,
                                'label' => $this->l('No')))),
                    
                    array(
                        'type' => 'margin_layout',
                        'label' => $this->l('Margin Layout'),
                        'name' => 'margin_layout'),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Barcode Invoice Type'),
                        'name' => 'barcodetype',
                        'required' => false,
                        'lang' => false,
                        'class' => 'barcodetype',
                        'options' => array(
                            'query' => gwadvancedinvoicetemplateModel::getBarcodeType(),
                            'id' => 'value',
                            'name' => 'name'
                        )),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Barcode Invoice Format'),
                        'hint' => $this->l('You can decide any content to be encoded'),
                        'name' => 'barcodeformat',
                        'size' => 255),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Barcode Product Type'),
                        'name' => 'barcodeproducttype',
                        'required' => false,
                        'lang' => false,
                        'class' => 'barcodeproducttype',
                        'options' => array(
                            'query' => gwadvancedinvoicetemplateModel::getBarcodeType(),
                            'id' => 'value',
                            'name' => 'name'
                        )),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Barcode Product Format'),
                        'name' => 'barcodeproductformat',
                        'required' => false,
                        'lang' => false,
                        'class' => 'barcodeproductformat',
                        'options' => array(
                            'query' => gwadvancedinvoicetemplateModel::getBarcodeProductFormat(),
                            'id' => 'value',
                            'name' => 'name'
                        )),
                    array(
                        'type'=>'watermark_lang',
                        'label' => $this->l('Watermark'),
                        'name' => 'watermark',
                        'size' => 255), 
                    array(
                        'type' => 'template_config',
                        'label' => $this->l(''),
                        'name' => 'template_config'), 
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Header'),
                        'name' => 'activeheader',
                        'is_bool' => true,
                        'values' => array(array(
                                'id' => 'activeheader_on',
                                'value' => 1,
                                'label' => $this->l('Yes')), array(
                                'id' => 'activeheader_off',
                                'value' => 0,
                                'label' => $this->l('No')))),
                    array(
                        'type' => 'textarea_fullwidth',
                        'label' => $this->l('Header Template'),
                        'name' => 'header',
                        'autoload_rte' => true,
                        'class'=> 'col-lg-12',
    				    'lang' => true,
                    ),
                    array(
                        'type' => 'textarea_fullwidth',
                        'label' => $this->l('Template'),
                        'name' => 'invoice',
                        'autoload_rte' => true,
                        'class'=> 'col-lg-12',
    				    'lang' => true,
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Footer'),
                        'name' => 'activefooter',
                        'is_bool' => true,
                        'values' => array(array(
                                'id' => 'activefooter_on',
                                'value' => 1,
                                'label' => $this->l('Yes')), array(
                                'id' => 'activefooter_off',
                                'value' => 0,
                                'label' => $this->l('No')))),
                    array(
                        'type' => 'textarea_fullwidth',
                        'label' => $this->l('Footer Template'),
                        'name' => 'footer',
                        'autoload_rte' => true,
                        'class'=> 'col-lg-12',
    				    'lang' => true,
                    ),
                    array(
                        'type' => 'productlist',
                        'label' => $this->l('Product List'),
                        'name' => 'productcolumns',
                        'autoload_rte' => true,
    				    'lang' => true,
                        'required' => true,),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Discount price'),
                        'name' => 'discountval',
                        'required' => false,
                        'lang' => false,
                        'class' => 'discountval',
                        'options' => array(
                            'query' => array(
                                array('value' => 'include','name' => 'Include Tax'),
                                array('value' => 'exclude','name' => 'Exclude Tax'),
                            ),
                            'id' => 'value',
                            'name' => 'name'
                        )),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Custom Css'),
                        'name' => 'customcss',
                        'rows'=>10
                    ),
                ),
                'submit' => array(
                                'title' => $this->l('Save'), 
                                'name' =>'saveTemplate'
                                ),
                'buttons' => array(
                    'save_and_stay' => array(
    					'name' => 'submitAddgwadvancedinvoicetemplateAndStay',
    					'type' => 'submit',
    					'title' => $this->l('Save and Stay'),
    					'class' => 'btn btn-default pull-right',
    					'icon' => 'process-icon-save'
    				),
                    'save_and_preview' => array(
    					'name' => 'previewTemplate',
    					'type' => 'submit',
    					'title' => $this->l('Preview'),
    					'class' => 'btn btn-default pull-right',
    					'icon' => 'process-icon-preview',
    				),
    			)
                
                
            );
            if (Shop::isFeatureActive()) {
                $this->fields_form['input'][] = array(
                    'type' => 'shop',
                    'label' => $this->l('Shop association'),
                    'name' => 'checkBoxShopAsso',
                    );
            }
            return parent::renderForm();
       }
    }
    public function remove_tpl_comments($content = '') {
    	return preg_replace('/{\*(.|\s)*?\*}/', '', $content);
    }
}
?>