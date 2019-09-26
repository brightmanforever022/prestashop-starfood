<?php
/**
* The file is Model of module. Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/

class gwadvancedinvoicetemplateModel extends ObjectModel
{
    public $id_gwadvancedinvoicetemplate;
    public $active = 1;
    public $rtl = 0;
    public $activeheader = 0;
    public $activefooter = 0;
    public $choose_design;
    public $layout;
    public $pagesize;
    public $mgheader;
    public $mgfooter;
    public $mgcontent;
    public $barcodetype;
    public $barcodeformat;
    public $barcodeproducttype;
    public $barcodeproductformat;
    public $pageorientation;
    public $template_config;
    public $title;
    public $header;
    public $footer;
    public $invoice;
    public $productcolumns;
    public $watermark;
    public $watermarktext;
    public $watermarkfont;
    public $watermarksize;
    public $discountval;
    public $customcss;
    public static $definition = array(

        'table' => 'gwadvancedinvoicetemplate',
        'primary' => 'id_gwadvancedinvoicetemplate',
        'multilang' => true,
        'fields' => array(
            //Fields
            'active'          =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'rtl'          =>  array('type' => self::TYPE_INT),
            'activeheader'          =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'activefooter'          =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'pagesize'        =>  array('type' => self::TYPE_STRING, 'size' => 5),
            'mgheader'          =>  array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'mgfooter'          =>  array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'mgcontent'        =>  array('type' => self::TYPE_STRING, 'size' => 255),
            'barcodetype'        =>  array('type' => self::TYPE_STRING, 'size' => 10),
            'barcodeformat'        =>  array('type' => self::TYPE_STRING, 'size' => 255),
            'barcodeproducttype'        =>  array('type' => self::TYPE_STRING, 'size' => 10),
            'barcodeproductformat'        =>  array('type' => self::TYPE_STRING, 'size' => 255),
            'pageorientation'        =>  array('type' => self::TYPE_STRING, 'size' => 5),
            'choose_design'  =>  array('type' => self::TYPE_STRING, 'size' => 255),
            'layout'  =>  array('type' => self::TYPE_STRING, 'size' => 255),
            'discountval' =>  array('type' => self::TYPE_STRING, 'size' => 15),
            'customcss'  =>  array('type' => self::TYPE_STRING),
            'template_config'  =>  array('type' => self::TYPE_STRING),
            //lang = true
            'title'       =>  array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255),
            'watermark' =>  array('type' => self::TYPE_STRING, 'lang' => true,'size' => 255),
            'watermarktext' =>  array('type' => self::TYPE_STRING, 'lang' => true,'size' => 255),
            'watermarkfont' =>  array('type' => self::TYPE_STRING, 'lang' => true),
            'watermarksize' =>  array('type' => self::TYPE_INT, 'lang' => true,'validate' => 'isunsignedInt'),
            'invoice' => 	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'header' => 	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'footer' => 	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'productcolumns' => 	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'),
        )

    );

    public function __construct($id_gwadvancedinvoicetemplate = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation('gwadvancedinvoicetemplate', array('type' => 'shop'));
        parent::__construct($id_gwadvancedinvoicetemplate, $id_lang, $id_shop);

    }
    public static function getAllBlock(){

        $id_shop = (int)Context::getContext()->shop->id;

        $id_lang = (int)Context::getContext()->language->id;

        $response = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('

            SELECT a.*,b.title

            FROM '._DB_PREFIX_.'gwadvancedinvoicetemplate as a,

                 '._DB_PREFIX_.'gwadvancedinvoicetemplate_lang as b,

                 '._DB_PREFIX_.'gwadvancedinvoicetemplate_shop as c

            WHERE a.id_gwadvancedinvoicetemplate = b.id_gwadvancedinvoicetemplate

            AND a.id_gwadvancedinvoicetemplate = c.id_gwadvancedinvoicetemplate

            AND c.id_shop = '.(int)$id_shop.'

            AND b.id_lang = '.(int)$id_lang.'

            AND a.active = 1'

        );
        return $response;
    }
    public static function getAllPageSize(){
        return array(
            array(
				'value' => '0',
				'name' => ''
			),
            array(
				'value' => 'A4',
				'name' => 'A4'
			),
            array(
				'value' => 'A5',
				'name' => 'A5'
			),
            array(
				'value' => 'A6',
				'name' => 'A6'
			),
            array(
				'value' => 'A7',
				'name' => 'A7'
			)
            
        );
    }
    public static function getBarcodeType(){
        return array(
            array('value' => 'qrcode','name' => 'QRCODE'),
            array('value' => 'C39','name' => 'C39'),
        	array('value' => 'C39+','name' => 'C39+'),
        	array('value' => 'C39E','name' => 'C39E'),
        	array('value' => 'C39E+','name' => 'C39E+'),
        	array('value' => 'C93','name' => 'C93'),
        	array('value' => 'S25','name' => 'S25'),
        	array('value' => 'S25+','name' => 'S25+'),
        	array('value' => 'I25','name' => 'I25'),
        	array('value' => 'I25+','name' => 'I25+'),
        	array('value' => 'C128','name' => 'C128'),
        	array('value' => 'C128A','name' => 'C128A'),
        	array('value' => 'C128B','name' => 'C128A'),
        	array('value' => 'C128C','name' => 'C128C'),
        	array('value' => 'EAN2','name' => 'EAN2'),
        	array('value' => 'EAN5','name' => 'EAN5'),
        	array('value' => 'EAN8','name' => 'EAN8'),
        	array('value' => 'EAN13','name' => 'EAN13'),
        	array('value' => 'UPCA','name' => 'UPCA'),
        	array('value' => 'UPCE','name' => 'UPCE'),
        	array('value' => 'MSI','name' => 'MSI'),
        	array('value' => 'MSI+','name' => 'MSI+'),
        	array('value' => 'POSTNET','name' => 'POSTNET'),
        	array('value' => 'PLANET','name' => 'PLANET'),
        	array('value' => 'RMS4CC','name' => 'RMS4CC'),
        	array('value' => 'KIX','name' => 'KIX'),
        	array('value' => 'IMB','name' => 'IMB'),
        	array('value' => 'CODABAR','name' => 'CODABAR'),
        	array('value' => 'CODE11','name' => 'CODE11'),
        	array('value' => 'PHARMA','name' => 'PHARMA'),
            array('value' => 'PHARMA2T','name' => 'PHARMA2T')
        );
    }
    public static function getBarcodeProductFormat(){
        return array(
			array('value' => 'product_id','name' => 'Product Id'),
            array('value' => 'product_link','name' => 'Product Link'),
            array('value' => 'ean13','name' => 'Ean13'),
            array('value' => 'upc','name' => 'Upc'),
            array('value' => 'reference','name' => 'Reference'),
        );
    }
    public static function getPageOrientation(){
        return array(
			array(
				'value' => 'P',
				'name' => 'Portrait'
			),
            array(
				'value' => 'L',
				'name' => 'Landscape'
			)
        );
    }
    public static function getListFont(){
        $fontlists = array();
        $fontsdir = opendir(_PS_TCPDF_PATH_.'fonts/');
		while (($file = readdir($fontsdir)) !== false) {
			if (Tools::substr($file, -4) == '.php') {
                $fontfamily = Tools::strtolower(basename($file, '.php'));
                if($fontfamily != 'index'){
                    $name = '';
    			    include_once(_PS_TCPDF_PATH_.'fonts/'.$file);
                    $fontlists[$fontfamily] = $name;
                }
			}
		}
		closedir($fontsdir);
        return $fontlists;
    }
    public static function getPageSize($choose_design = ''){
        if($choose_design == ''){
            return array(
    			array('value' => 'A4','name' => 'A4'),
                array('value' => 'A5','name' => 'A5'),
                array('value' => 'A6','name' => 'A6'),
                array('value' => 'A7','name' => 'A7'),
            );
        }else{
            $pagesizes = array();
            if($choose_design !=''){
                if(Tools::file_exists_no_cache(_PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/base/'.$choose_design.'/config.php')){
                    include_once(_PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/base/'.$choose_design.'/config.php');
                    if(method_exists($choose_design,'getTemplate')){
                        //$template = $choose_design::getTemplate();
                        //fix error in php version 5.2
                        $template = call_user_func_array(array($choose_design, 'getTemplate'), array());
                        $pagesize = $template['pagesize'];
                        if($pagesize){
                            foreach($pagesize as $size){
                                $pagesizes[] = array('value'=>$size,'name'=>$size);
                            }
                        }
                    }
                }
            }
            return $pagesizes;
        }
        
    }
    public static function writeTemplate($file='',$dir='',$content=''){
        if(!Tools::file_exists_no_cache($dir)){
            if (!mkdir($dir, 0777, true)) {
                return false;
            }
        }
        if(!Tools::file_exists_no_cache($dir.'/'.$file)){
            if (!fopen($dir.'/'.$file, "w")) {
                return false;
            }
        }
        file_put_contents($dir.'/'.$file, $content);
    }
    public static function copyTree($dir = '',$destination=''){
        if (is_dir($dir))
		{
			$objects = scandir($dir);
			foreach ($objects as $object)
				if ($object != '.' && $object != '..')
				{
					if (filetype($dir.'/'.$object) == 'dir'){
					   if(!Tools::file_exists_no_cache($destination.'/'.$object)){
                            if (!mkdir($destination.'/'.$object, 0777, true)) {
                                return false;
                            }
                        }
						self::copyTree($dir.'/'.$object,$destination.'/'.$object);
					}else
						Tools::copy($dir.'/'.$object,$destination.'/'.$object);
				}
			reset($objects);
		}
    }
    public static function delTree($dir = ''){
        if (is_dir($dir))
		{
			$objects = scandir($dir);
			foreach ($objects as $object)
				if ($object != '.' && $object != '..')
				{
					if (filetype($dir.'/'.$object) == 'dir')
						self::delTree($dir.'/'.$object);
					else
						unlink($dir.'/'.$object);
				}
			reset($objects);
			rmdir($dir);
		}
    }
    public static function getProductListTpl($widthtitles,$titles,$contents,$align,$temp = '',$iso_lang='en',$discountval = 'exclude'){
        if($contents){
            foreach($contents as &$content){
                $content = str_replace('{$order_detail.product_name}','<p class="product_name">{$order_detail.product_name}</p>',str_replace('{displayPrice:$','{displayPrice currency=$order->id_currency price=$',$content));
            }
        }
        Context::getContext()->smarty->assign(array(
            'widthtitles' => $widthtitles,
            'titles' => $titles,
            'contents' => $contents,
            'align' => $align,
            'discountval' =>$discountval
        ));
        $products_list_temp =  _PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/base/'.$temp.'/'.$iso_lang.'/product_list.tpl';
        if(!Tools::file_exists_no_cache($products_list_temp))
            $products_list_temp =  _PS_MODULE_DIR_.'gwadvancedinvoice/views/templates/admin/tpltemplates/base/'.$temp.'/en/product_list.tpl';
        $products_list = Context::getContext()->smarty->fetch($products_list_temp);
        return $products_list;
    }
    public static function getDataDemo(){
        $useSSL = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($useSSL) ? 'https://' : 'http://';
        $base_url = $protocol_content.Tools::getHttpHost().__PS_BASE_URI__;
        $fakeorder = new Order();
        $fakeorder->id_currency = (int)Context::getContext()->currency->id;
       $logo = Context::getContext()->link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO'));
       $carrier_img = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'gwadvancedinvoice'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'carrier.png';
       return array(
        'shopname' => 'Shopname',
        'shopurl'=>$base_url,
        'logo' => '<img src="'.$logo.'">',
        'billing_state' => 'Florida',
        'billing_country' => 'United States',
        'billing_alias' => 'My address',
        'billing_company' => 'MyCompany',
        'billing_lastname' => 'DOE',
        'billing_firstname' => 'John',
        'billing_address1' => '16, Main street',
        'billing_address2' => '2nd floor',
        'billing_postcode' => '33133',
        'billing_city' => 'Miami',
        'billing_other' => '',
        'billing_phone' => '0102030405',
        'billing_phone_mobile' => '',
        'billing_vat_number' => '',
        'billing_dni' => '',
        'billing_date_add' => '2015-11-30',
        'billing_date_upd' => '2015-11-30',
        'delivery_state' => 'Florida',
        'delivery_country' => 'United States',
        'delivery_alias' => 'My address',
        'delivery_company' => 'MyCompany',
        'delivery_lastname' => 'DOE',
        'delivery_firstname' => 'John',
        'delivery_address1' => '16,Main street',
        'delivery_address2' => '2nd floor',
        'delivery_postcode' => '33133',
        'delivery_city' => 'Miami',
        'delivery_other' => '',
        'delivery_phone' => '0102030405',
        'delivery_phone_mobile' => '',
        'delivery_vat_number' => '',
        'delivery_dni' => '',
        'delivery_date_add' => '2015-11-30',
        'delivery_date_upd' => '2015-11-30',
        'customeremail' => 'demo@demo.com',
        'customerfirstname' => 'demo',
        'customerlastname' => 'demo',
        'HOOK_DISPLAY_PDF'=>'',
        'id_order'=>'1',
        'barcode_invoice' =>'',
        'reference' => 'DHSEPTLQH',
        'invoice_number' => '#IN000002',
        'delivery_number'=> '#D000002',
        'order_notes'=>'',
        'order_status'=>'<table cellpadding="5" cellspacing="0"  style="float:left;background-color:#32CD32;color:#ffffff;text-align:center;">
                                                <tbody>
                                                    <tr>
                                                        <td>Payment accepted</td>
                                                    </tr>
                                                </tbody>
                                        </table>',
        'gift_message'=>'',
        'invoice_date' => '05/12/2015',
        'date_add' => '2015-12-05',
        'date_upd' => '2015-12-05',
        'payment' => 'Bankwire',
        'order_carrier_name' => 'Mycarrier',
        'order_carrier_logo' =>'<img src="'.$carrier_img.'">',
        'total_discounts' => '0.000000',
        'total_discounts_tax_incl' => '0.000000',
        'total_discounts_tax_excl' => '0.000000',
        'total_paid' => '56.530000',
        'total_paid_tax_incl' => '56.530000',
        'total_paid_tax_excl' => '36.720000',
        'total_paid_real' => '56.530000',
        'total_products' => '49.530000',
        'total_products_wt' => '39.620000',
        'total_shipping' => '8.400000',
        'total_shipping_tax_incl' => '8.400000',
        'total_shipping_tax_excl' => '7.000000',
        'carrier_tax_rate' => '20.000',
        'total_wrapping' => '0.000000',
        'total_wrapping_tax_incl' => '0.000000',
        'total_wrapping_tax_excl' => '0.000000',
        'tax_tab'=>'',
        'cart_rules'=>array(),
        'order'=>$fakeorder,
        'footer' => array(
            'products_before_discounts_tax_excl' => '69.510000',
            'product_discounts_tax_excl' => '0.000000',
            'products_after_discounts_tax_excl' => '69.51',
            'products_before_discounts_tax_incl' => '69.510000',
            'product_discounts_tax_incl' => '0.000000',
            'products_after_discounts_tax_incl' => '69.51',
            'product_taxes' => '0',
            'shipping_tax_excl' => '2.000000',
            'shipping_taxes' => '0',
            'shipping_tax_incl' => '2.000000',
            'wrapping_tax_excl' => '0.000000',
            'wrapping_taxes' => '0',
            'wrapping_tax_incl' => '0.000000',
            'ecotax_taxes' => '0',
            'total_taxes' => '0',
            'total_paid_tax_excl' => '71.510000',
            'total_paid_tax_incl' => '71.510000',
            ),
        'order_details' => array(
            '1' => array(
                'product_id' => '1',
                'product_attribute_id' => '1',
                'product_name' => 'Lorem ipsum dolor sit amet',
                'description_short' =>'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy...',
                'product_quantity' => '1',
                'product_price' => '16.510000',
                'reduction_percent' => '0.00',
                'reduction_amount' => '0.000000',
                'ean13' => 'demo_1',
                'upc' => 'demo_1',
                'price' => '16.510000',
                'reference' => 'demo_1',
                'unit_price_tax_excl_including_ecotax' => '16.510000',
                'unit_price_tax_incl_including_ecotax' => '19.812000',
                'total_price_tax_excl_including_ecotax' => '16.510000',
                'total_price_tax_incl_including_ecotax' => '19.810000',
                'tax_rate' => '0.000',
                'order_detail_tax_label' => '0%',
                'image_tag' => '<img src="'.$base_url.'modules/gwadvancedinvoice/views/img/productdemo.png" alt="" class="imgmimg-thumbnail">',
                'barcode' => '',
                'customizedDatas' =>array(),
                'total_tax'=>0
                ),
            '2' => array(
                'product_id' => '2',
                'product_attribute_id' => '2',
                'product_name' => 'Lorem ipsum dolor sit amet',
                'description_short' =>'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy...',
                'product_quantity' => '1',
                'product_price' => '16.510000',
                'reduction_percent' => '0.00',
                'reduction_amount' => '0.000000',
                'ean13' => 'demo_2',
                'upc' => 'demo_2',
                'price' => '16.510000',
                'reference' => 'demo_2',
                'unit_price_tax_excl_including_ecotax' => '16.510000',
                'unit_price_tax_incl_including_ecotax' => '19.812000',
                'total_price_tax_excl_including_ecotax' => '16.510000',
                'total_price_tax_incl_including_ecotax' => '19.810000',
                'tax_rate' => '0.000',
                'order_detail_tax_label' => '0%',
                'image_tag' => '<img src="'.$base_url.'modules/gwadvancedinvoice/views/img/productdemo.png" alt="" class="imgmimg-thumbnail">',
                'barcode' => '',
                'customizedDatas' =>array(),
                'total_tax'=>0
                ),
            '3' => array(
                'product_id' => '3',
                'product_attribute_id' => '3',
                'product_name' => 'Lorem ipsum dolor sit amet',
                'description_short' =>'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy...',
                'product_quantity' => '1',
                'product_price' => '16.510000',
                'reduction_percent' => '0.00',
                'reduction_amount' => '0.000000',
                'ean13' => 'demo_3',
                'upc' => 'demo_3',
                'price' => '16.510000',
                'reference' => 'demo_3',
                'unit_price_tax_excl_including_ecotax' => '16.510000',
                'unit_price_tax_incl_including_ecotax' => '19.812000',
                'total_price_tax_excl_including_ecotax' => '16.510000',
                'total_price_tax_incl_including_ecotax' => '19.810000',
                'tax_rate' => '0.000',
                'order_detail_tax_label' => '0%',
                'image_tag' => '<img src="'.$base_url.'modules/gwadvancedinvoice/views/img/productdemo.png" alt="" class="imgmimg-thumbnail">',
                'barcode' => '',
                'customizedDatas' =>array(),
                'total_tax'=>0
                ),
            
            )
        );
    }
}