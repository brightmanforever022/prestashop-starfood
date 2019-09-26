<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@buy-addons.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Buy-Addons <contact@buy-addons.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
* @since 1.6
*/

class ba_prestashop_invoice extends Module
{
    private $demoMode=false;
    public $languagesArr;
    public function __construct()
    {
        require_once "includes/baorderinvoice.php";
        require_once "includes/badeliveryslip.php";
        $this->name = "ba_prestashop_invoice";
        $this->tab = "billing_invoicing";
        $this->version = "1.1.16";
        $this->author = "buy-addons";
        $this->need_instance = 0;
        $this->secure_key = Tools::encrypt($this->name);
        $this->bootstrap = true;
        $this->module_key = '0deba47e596f8932b1610da4e1214d11';
        $this->languagesArr=Language::getLanguages(false);
        parent::__construct();
        if (strpos(_PS_VERSION_, "1.5") === 0) {
            $this->context->controller->addCSS($this->_path.'views/css/style_v1.5.5.0.css');
            $this->context->controller->addJS($this->_path . 'views/js/langueage_click.js');
        }
        $this->displayName = $this->l('Invoice, Delivery Template Builder');
        $this->description  = $this->l('Author: buy-addons');
    }

    public function install()
    {
        if (parent::install()===false) {
            return false;
        }
       
        $this->registerHook('actionValidateOrder');
        $this->saveDefaultConfig();
        return true;
    }
    
    public function defaultDescription($param)
    {
        if (!empty($param)) {
            return '<p style="text-align:left;">'.$param.'<p>';
        }
        return '<p style="text-align:center;">--</p>';
    }
    
    public function saveDefaultConfig()
    {
        $sql='
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ba_prestashop_invoice` (
                `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `showShippingInProductList` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
                `showDiscountInProductList` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
                `baInvoiceEnableLandscape` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
                `showPagination` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
                `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `description` text COLLATE utf8_unicode_ci NOT NULL,
                `thumbnail` text COLLATE utf8_unicode_ci NOT NULL,
                `header_invoice_template` text COLLATE utf8_unicode_ci NOT NULL,
                `invoice_template` text COLLATE utf8_unicode_ci NOT NULL,
                `footer_invoice_template` text COLLATE utf8_unicode_ci NOT NULL,
                `customize_css` text COLLATE utf8_unicode_ci NOT NULL,
                `numberColumnOfTableTemplaterPro` int(5) NOT NULL,
                `columsTitleJson` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `columsContentJson` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `columsColorJson` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `columsColorBgJson` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `id_lang` int(5) NOT NULL,
                `useAdminOrClient` int(1) NOT NULL,
                `status` int(1) NOT NULL,
                `id_shop` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `id_shop_group` varchar(255) COLLATE utf8_unicode_ci NOT NULL
            );
            
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ba_prestashop_delivery_slip` (
                `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `showShippingInProductList` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
                `showDiscountInProductList` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
                `baInvoiceEnableLandscape` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
                `showPagination` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
                `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `description` text COLLATE utf8_unicode_ci NOT NULL,
                `thumbnail` text COLLATE utf8_unicode_ci NOT NULL,
                `header_invoice_template` text COLLATE utf8_unicode_ci NOT NULL,
                `invoice_template` text COLLATE utf8_unicode_ci NOT NULL,
                `footer_invoice_template` text COLLATE utf8_unicode_ci NOT NULL,
                `customize_css` text COLLATE utf8_unicode_ci NOT NULL,
                `numberColumnOfTableTemplaterPro` int(5) NOT NULL,
                `columsTitleJson` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `columsContentJson` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `columsColorJson` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `columsColorBgJson` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `id_lang` int(5) NOT NULL,
                `useAdminOrClient` int(1) NOT NULL,
                `status` int(1) NOT NULL,
                `id_shop` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `id_shop_group` varchar(255) COLLATE utf8_unicode_ci NOT NULL
            );
            
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ba_prestashop_invoice_tax` (
                `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `id_order` int(11) NOT NULL,
                `id_product` int(11) NOT NULL,
                `id_tax` int(11) NOT NULL,
                `tax_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `tax_rate` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `tax_amount` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `product_qty` int(11) NOT NULL,
                `unit_price_tax_excl` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `unit_price_tax_incl` varchar(255) COLLATE utf8_unicode_ci NOT NULL
            );
        ';
        
        Db::getInstance()->query($sql);
        $languagesArr=Language::getLanguages(false);
        $shopArrayList = Shop::getShops(false);
        foreach ($shopArrayList as $shopArray) {
            foreach ($languagesArr as $language) {
                $ba_lang =(int) $language['id_lang'];
                $files = scandir(_PS_MODULE_DIR_."/ba_prestashop_invoice/invoice_invoice/");
                foreach ($files as $file) {
                    if (is_dir(_PS_MODULE_DIR_."/ba_prestashop_invoice/invoice_invoice/".$file)==false
                        && $file != "." && $file != "..") {
                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                        if ($ext == 'xml') {
                            $dirFile = _PS_MODULE_DIR_."/ba_prestashop_invoice/invoice_invoice/".$file;
                            $dataArray = Tools::simplexml_load_file($dirFile);
                            $name = (string)$dataArray->name;
                            $invoice_template=(string)$dataArray->pdf_content;
                            if (!empty($name) && !empty($invoice_template)) {
                                $name = (string)$dataArray->name;
                                $description=Tools::htmlentitiesUTF8(strip_tags((string)$dataArray->description));
                                $thumbnail=strip_tags((string)$dataArray->thumbnail);
                                
                                $invoice_template=Tools::htmlentitiesUTF8((string)$dataArray->pdf_content);
                                $header_invoice_template=Tools::htmlentitiesUTF8((string)$dataArray->pdf_header);
                                $footer_invoice_template=Tools::htmlentitiesUTF8((string)$dataArray->pdf_footer);
                                $customize_css=strip_tags(Tools::htmlentitiesUTF8((string)$dataArray->customize_css));
                                
                                $numberColumn = (int) $dataArray->products_template[0]['columns_size'];
                                $columsColorBg = array();
                                $columsColorArray = array();
                                $columsContentArray = array();
                                $columnsTitleArray = array();
                                for ($i = 0; $i < $numberColumn; $i++) {
                                    $columnsTitleArray[] = (string)$dataArray->products_template->col[$i]->col_title;
                                    $columsContentArray[] = (string)$dataArray->products_template->col[$i]->col_content;
                                    $columsColorArray[]=(string)$dataArray->products_template->col[$i]->col_title_color;
                                    $columsColorBg[]=(string)$dataArray->products_template->col[$i]->col_title_bgcolor;
                                }
                                $columsTitleJson =  Tools::jsonEncode($columnsTitleArray);
                                $columsContentJson =  Tools::jsonEncode($columsContentArray);
                                $columsColorJson =  Tools::jsonEncode($columsColorArray);
                                $columsColorBgJson =  Tools::jsonEncode($columsColorBg);
                                $showShippingInProductList=(string)$dataArray->show_shipping;
                                $showDiscountInProductList=(string)$dataArray->show_discount;
                                $baInvoiceEnableLandscape=(string)$dataArray->enable_landscape;
                                $showPagination=(string)$dataArray->show_pagination;
                                $status=(int)$dataArray->status;
                                $useAdminOrClient=(int)$dataArray->useAdminOrClient;
                                Db::getInstance()->insert("ba_prestashop_invoice", array(
                                    'name' => pSQL($name),
                                    'description' => pSQL($description),
                                    'thumbnail' => Tools::htmlentitiesUTF8($thumbnail),
                                    'showShippingInProductList' => $showShippingInProductList,
                                    'showDiscountInProductList' => $showDiscountInProductList,
                                    'baInvoiceEnableLandscape' => $baInvoiceEnableLandscape,
                                    'showPagination' => $showPagination,
                                    'header_invoice_template' => $header_invoice_template,
                                    'invoice_template' => $invoice_template,
                                    'footer_invoice_template' => $footer_invoice_template,
                                    'customize_css' => $customize_css,
                                    'numberColumnOfTableTemplaterPro' => $numberColumn,
                                    'columsTitleJson' => $columsTitleJson,
                                    'columsContentJson' => $columsContentJson,
                                    'columsColorJson' => $columsColorJson,
                                    'columsColorBgJson' => $columsColorBgJson,
                                    'id_lang' => $ba_lang,
                                    'useAdminOrClient' => (int) $useAdminOrClient,
                                    'status' => (int) $status,
                                    'id_shop' => $shopArray['id_shop'],
                                    'id_shop_group' => $shopArray['id_shop_group']
                                ));
                            }
                        } else {
                            continue;
                        }
                    }
                }
                
            }
            foreach ($languagesArr as $language) {
                $ba_lang =(int) $language['id_lang'];
                $files = scandir(_PS_MODULE_DIR_."/ba_prestashop_invoice/delivery_invoice/");
                foreach ($files as $file) {
                    if (is_dir(_PS_MODULE_DIR_."/ba_prestashop_invoice/delivery_invoice/".$file)==false
                        && $file != "." && $file != "..") {
                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                        if ($ext == 'xml') {
                            $dirFile = _PS_MODULE_DIR_."/ba_prestashop_invoice/delivery_invoice/".$file;
                            $dataArray = Tools::simplexml_load_file($dirFile);
                            $name = (string)$dataArray->name;
                            $invoice_template=(string)$dataArray->pdf_content;
                            if (!empty($name) && !empty($invoice_template)) {
                                $name = (string)$dataArray->name;
                                $description=Tools::htmlentitiesUTF8(strip_tags((string)$dataArray->description));
                                $thumbnail=strip_tags((string)$dataArray->thumbnail);
                               
                                $invoice_template=Tools::htmlentitiesUTF8((string)$dataArray->pdf_content);
                                $header_invoice_template=Tools::htmlentitiesUTF8((string)$dataArray->pdf_header);
                                $footer_invoice_template=Tools::htmlentitiesUTF8((string)$dataArray->pdf_footer);
                                $customize_css=strip_tags(Tools::htmlentitiesUTF8((string)$dataArray->customize_css));
                                
                                $numberColumn = (int) $dataArray->products_template[0]['columns_size'];
                                $columsColorBg = array();
                                $columsColorArray = array();
                                $columsContentArray = array();
                                $columnsTitleArray = array();
                                for ($i = 0; $i < $numberColumn; $i++) {
                                    $columnsTitleArray[] = (string)$dataArray->products_template->col[$i]->col_title;
                                    $columsContentArray[] = (string)$dataArray->products_template->col[$i]->col_content;
                                    $columsColorArray[]=(string)$dataArray->products_template->col[$i]->col_title_color;
                                    $columsColorBg[]=(string)$dataArray->products_template->col[$i]->col_title_bgcolor;
                                }
                                $columsTitleJson =  Tools::jsonEncode($columnsTitleArray);
                                $columsContentJson =  Tools::jsonEncode($columsContentArray);
                                $columsColorJson =  Tools::jsonEncode($columsColorArray);
                                $columsColorBgJson =  Tools::jsonEncode($columsColorBg);
                                $showShippingInProductList=(string)$dataArray->show_shipping;
                                $showDiscountInProductList=(string)$dataArray->show_discount;
                                $baInvoiceEnableLandscape=(string)$dataArray->enable_landscape;
                                $showPagination=(string)$dataArray->show_pagination;
                                $status=(int)$dataArray->status;
                                $useAdminOrClient=(int)$dataArray->useAdminOrClient;
                                Db::getInstance()->insert("ba_prestashop_delivery_slip", array(
                                    'name' => pSQL($name),
                                    'description' => pSQL($description),
                                    'thumbnail' => Tools::htmlentitiesUTF8($thumbnail),
                                    'showShippingInProductList' => $showShippingInProductList,
                                    'showDiscountInProductList' => $showDiscountInProductList,
                                    'baInvoiceEnableLandscape' => $baInvoiceEnableLandscape,
                                    'showPagination' => $showPagination,
                                    'header_invoice_template' => $header_invoice_template,
                                    'invoice_template' => $invoice_template,
                                    'footer_invoice_template' => $footer_invoice_template,
                                    'customize_css' => $customize_css,
                                    'numberColumnOfTableTemplaterPro' => $numberColumn,
                                    'columsTitleJson' => $columsTitleJson,
                                    'columsContentJson' => $columsContentJson,
                                    'columsColorJson' => $columsColorJson,
                                    'columsColorBgJson' => $columsColorBgJson,
                                    'id_lang' => $ba_lang,
                                    'useAdminOrClient' => (int) $useAdminOrClient,
                                    'status' => (int) $status,
                                    'id_shop' => $shopArray['id_shop'],
                                    'id_shop_group' => $shopArray['id_shop_group']
                                ));
                            }
                        } else {
                            continue;
                        }
                    }
                }
                
            }
        }
    }
    
    public function fillLanguageName($id_lang)
    {
        foreach ($this->languagesArr as $v) {
            if ($v["id_lang"]==$id_lang) {
                return $v["name"];
            }
        }
    }
    
    public function getImageToHelpperList($imageTags)
    {
        if (!empty($imageTags)) {
            return '<a class="riverroad1" href="#" title="" img="'.Tools::htmlentitiesDecodeUTF8($imageTags).'">'
            ."<img src='".__PS_BASE_URI__."modules/ba_prestashop_invoice/views/img/img_invoice/"
            .Tools::htmlentitiesDecodeUTF8($imageTags)."'></a>";
        }
        return '<p style="text-align:center;">--</p>';
    }
    
    public function uninstall()
    {
        $sql="
            DROP TABLE IF EXISTS "._DB_PREFIX_."ba_prestashop_invoice;
            DROP TABLE IF EXISTS "._DB_PREFIX_."ba_prestashop_delivery_slip;
        ";
        Db::getInstance()->query($sql);
        if (parent::uninstall() == false) {
            return false;
        }
        return true;
    }
    public function urlExists($url)
    {
        $url = str_replace("http://", "", $url);
        if (strstr($url, "/")) {
            $url = explode("/", $url, 2);
            $url[1] = "/".$url[1];
        } else {
            $url = array($url, "/");
        }

        $fh = fsockopen($url[0], 80);
        if ($fh) {
            fputs($fh, "GET ".$url[1]." HTTP/1.1\nHost:".$url[0]."\n\n");
            if (fread($fh, 22) == "HTTP/1.1 404 Not Found") {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
    
    
    
    public function getContent()
    {
        // $obj = new Product(1,false,1);
        // $array = (array)$obj;
        // $products = ProductSale::getBestSales($this->context->language->id);
        // echo "<pre>";print_r($products);die;
        $this->context->controller->addJS($this->_path . 'views/js/jscolor/jscolor.js');
        $this->context->controller->addJS($this->_path . 'views/js/ajaxpreview.js');
        $this->context->controller->addJS($this->_path . 'views/js/showmoretoken.js');
        $this->context->controller->addCSS($this->_path . 'views/css/style.css');
        $iso=$this->context->language->iso_code;
        $this->context->controller->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
        $this->context->controller->addJqueryUI('ui.tooltip');
        $html='
            <script type="text/javascript">    
                var iso = \''.(file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'\' ;
                var pathCSS = \''._THEME_CSS_DIR_.'\' ;
                var ad = \''.dirname($_SERVER['PHP_SELF']).'\' ;
                var baseUrl = \''.Tools::getShopProtocol().Tools::getHttpHost().__PS_BASE_URI__.'\' ;
            </script>
            <script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
            <script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>
            <script language="javascript" type="text/javascript">
                id_language = Number('.$this->context->language->id.');
                tinySetup();
            </script>
        ';
        $token=Tools::getAdminTokenLite('AdminModules');
        $this->smarty->assign('token', $token);
        $bamodule=AdminController::$currentIndex;
        $this->smarty->assign('bamodule', $bamodule);
        $this->smarty->assign('configure', $this->name);
        
        $ba_lang = $this->context->language->id;
        $getBaLang=Tools::getValue('ba_lang');
        if ($getBaLang != "") {
            $ba_lang = (int) Tools::getValue('ba_lang');
        }
        $taskBar = 'orderinvoice';
        if (Tools::getValue('task') != false) {
            $taskBar = Tools::getValue('task');
        }
        $this->smarty->assign('taskbar', $taskBar);
        $this->smarty->assign('ba_lang', $ba_lang);
        $buttonDemoArr = array(
            'submitBaSave',
            'submitBaSaveAndStay',
            'import',
            'statusba_prestashop_invoice_invoice',
            'statusba_prestashop_invoice_deliveryslip',
            'duplicateba_prestashop_invoice_invoice',
            'deleteba_prestashop_invoice_invoice',
            'duplicateba_prestashop_invoice_deliveryslip',
            'deleteba_prestashop_invoice_deliveryslip',
            'submitBulkdeleteba_prestashop_invoice_deliveryslip',
            'submitBulkdeleteba_prestashop_invoice_invoice'
        );
        if ($this->demoMode==true) {
            foreach ($buttonDemoArr as $buttonDemo) {
                if (Tools::isSubmit($buttonDemo)) {
                    Tools::redirectAdmin($bamodule.'&token='.$token.'&configure='.$this->name.'&demoMode=1');
                }
            }
        }
        $this->smarty->assign('demoMode', Tools::getValue('demoMode'));
        if (Tools::getValue('importerror') == "2") {
            $html.=$this->displayError($this->l('<name>, <pdf_content> tags is NOT empty'));
        } elseif (Tools::getValue('importerror') == "1") {
            $html.=$this->displayError($this->l('Your file must be a *.xml file'));
        }
        $html .= $this->display(__FILE__, 'views/templates/admin/taskbar.tpl');
        //$this->getDataInvoice();
        //var_dump($taskBar);die;
        if ($taskBar == "orderinvoice") {
            $baOrderInvoice = new BaOrderInvoice();
            $html .= $baOrderInvoice->caseInvoice();
        } else {
            $baDeliverySlip = new BaDeliverySlip();
            $html .= $baDeliverySlip->caseDeliverySlip();
        }
        
        //$html .= $this->display(__FILE__, 'views/templates/admin/order_invoice/form.tpl');
        return $html;
    }
    
    public function importDataPDF($table, $task, $import)
    {
        $adminControllers=AdminController::$currentIndex;
        $token='&token='.Tools::getAdminTokenLite('AdminModules');
        $configAndTask='&configure='.$this->name.'&task='.$task;
        $ext = pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION);
        $tmpFile = $_FILES['fileToUpload']['tmp_name'];
        $dirUpload = _PS_MODULE_DIR_."/ba_prestashop_invoice/upload/".$_FILES['fileToUpload']['name'];
        if ($ext == 'xml') {
            move_uploaded_file($tmpFile, $dirUpload);
            $dataArray = Tools::simplexml_load_file($dirUpload);
            $name = (string)$dataArray->name;
            $invoice_template=(string)$dataArray->pdf_content;
            if (!empty($name) && !empty($invoice_template)) {
                $name = (string)$dataArray->name;
                $description=Tools::htmlentitiesUTF8(strip_tags((string)$dataArray->description));
                $invoice_template=Tools::htmlentitiesUTF8((string)$dataArray->pdf_content);
                $header_invoice_template=Tools::htmlentitiesUTF8((string)$dataArray->pdf_header);
                $footer_invoice_template=Tools::htmlentitiesUTF8((string)$dataArray->pdf_footer);
                $customize_css=strip_tags(Tools::htmlentitiesUTF8((string)$dataArray->customize_css));
                
                $numberColumn = (int) $dataArray->products_template[0]['columns_size'];
                $columsColorBgArray=array();
                $columsColorArray=array();
                $columsContentArray=array();
                $columnsTitleArray=array();
                for ($i = 0; $i < $numberColumn; $i++) {
                    $columnsTitleArray[] = (string)$dataArray->products_template->col[$i]->col_title;
                    $columsContentArray[] = (string)$dataArray->products_template->col[$i]->col_content;
                    $columsColorArray[] = (string)$dataArray->products_template->col[$i]->col_title_color;
                    $columsColorBgArray[] = (string)$dataArray->products_template->col[$i]->col_title_bgcolor;
                }
                $columsTitleJson =  Tools::jsonEncode($columnsTitleArray);
                $columsContentJson =  Tools::jsonEncode($columsContentArray);
                $columsColorJson =  Tools::jsonEncode($columsColorArray);
                $columsColorBgJson =  Tools::jsonEncode($columsColorBgArray);
                //echo "<pre>";var_dump($columsTitleJson);die;
                $showShippingInProductList=(string)$dataArray->show_shipping;
                $showDiscountInProductList=(string)$dataArray->show_discount;
                $baInvoiceEnableLandscape=(string)$dataArray->enable_landscape;
                $showPagination=(string)$dataArray->show_pagination;
                $id_lang=(int)$dataArray->id_lang;
                $status=(int)$dataArray->status;
                Db::getInstance()->insert($table, array(
                    'name' => pSQL($name),
                    'description' => $description,
                    'showShippingInProductList' => $showShippingInProductList,
                    'showDiscountInProductList' => $showDiscountInProductList,
                    'baInvoiceEnableLandscape' => $baInvoiceEnableLandscape,
                    'showPagination' => $showPagination,
                    'header_invoice_template' => $header_invoice_template,
                    'invoice_template' => $invoice_template,
                    'footer_invoice_template' => $footer_invoice_template,
                    'customize_css' => $customize_css,
                    'numberColumnOfTableTemplaterPro' => $numberColumn,
                    'columsTitleJson' => $columsTitleJson,
                    'columsContentJson' => $columsContentJson,
                    'columsColorJson' => $columsColorJson,
                    'columsColorBgJson' => $columsColorBgJson,
                    'id_lang' => (int) $id_lang,
                    'status' => (int) $status,
                    'id_shop' => $this->context->shop->id,
                    'id_shop_group' => $this->context->shop->id_group
                ));
                @unlink($dirUpload);
            } else {
                @unlink($dirUpload);
                Tools::redirectAdmin($adminControllers.$token.$configAndTask.'&'.$import.'&importerror=2');
            }
            
        } else {
            Tools::redirectAdmin($adminControllers.$token.$configAndTask.'&'.$import.'&importerror=1');
        }
    }
    
    public function returnFooterText()
    {
        return sprintf($this->l('Page %s of %s'), '{PAGENO}', '{nb}');
    }
    
    public function hookActionValidateOrder($params)
    {
        $order = $params['order'];
        $productList = $order->getProducts();
        //echo '<pre>';print_r($productList);
        //echo '<pre>';print_r($order);
        //die;
        foreach ($productList as $productArr) {
            $taxObj = new Tax((int)$productArr['id_tax_rules_group'], (int)$order->id_lang);
            $taxAmount = $productArr['unit_price_tax_incl'] - $productArr['unit_price_tax_excl'];
            Db::getInstance()->insert('ba_prestashop_invoice_tax', array(
                'id_order'            => $order->id,
                'id_product'          => $productArr['product_id'],
                'id_tax'              => (int)$productArr['id_tax_rules_group'],
                'tax_name'            => $taxObj->name,
                'tax_rate'            => $taxObj->rate,
                'tax_amount'          => $taxAmount,
                'product_qty'         => $productArr['product_quantity'],
                'unit_price_tax_excl' => $productArr['unit_price_tax_excl'],
                'unit_price_tax_incl' => $productArr['unit_price_tax_incl']
            ));
            // chen ecotax neu co
            if (isset($productArr['ecotax']) && $productArr['ecotax']>0 && $productArr['ecotax_tax_rate']>0) {
                
                Db::getInstance()->insert('ba_prestashop_invoice_tax', array(
                    'id_order'            => $order->id,
                    'id_product'          => $productArr['product_id'],
                    'id_tax'              => (int)Configuration::get('PS_ECOTAX_TAX_RULES_GROUP_ID'),
                    'tax_name'            => $this->l('Ecotax'),
                    'tax_rate'            => $productArr['ecotax_tax_rate'],
                    'tax_amount'          => $productArr['ecotax'] * ($productArr['ecotax_tax_rate']/100),
                    'product_qty'         => $productArr['product_quantity'],
                    'unit_price_tax_excl' => $productArr['ecotax'],
                    'unit_price_tax_incl' => $productArr['ecotax'] * (1+$productArr['ecotax_tax_rate']/100)
                ));
            }
        }
        
        $idTaxRulesGroup = Carrier::getIdTaxRulesGroupByIdCarrier((int)$order->id_carrier);
        $shippingTaxObj = new Tax((int)$idTaxRulesGroup, (int)$order->id_lang);
        $taxAmount = $order->total_shipping_tax_incl - $order->total_shipping_tax_excl;
        Db::getInstance()->insert('ba_prestashop_invoice_tax', array(
            'id_order'            => $order->id,
            'id_product'          => 0,
            'id_tax'              => (int)$idTaxRulesGroup,
            'tax_name'            => $shippingTaxObj->name,
            'tax_rate'            => $shippingTaxObj->rate,
            'tax_amount'          => $taxAmount,
            'product_qty'         => 1,
            'unit_price_tax_excl' => $order->total_shipping_tax_excl,
            'unit_price_tax_incl' => $order->total_shipping_tax_incl
        ));
        // chen Gift-wrapping TAX neu co
        if (isset($order->total_wrapping) && ($order->total_wrapping_tax_incl > $order->total_wrapping_tax_excl)) {
            $a = $order->total_wrapping_tax_incl - $order->total_wrapping_tax_excl;
            Db::getInstance()->insert('ba_prestashop_invoice_tax', array(
                'id_order'            => $order->id,
                'id_product'          => 0,
                'id_tax'              => (int)Configuration::get('PS_GIFT_WRAPPING_TAX_RULES_GROUP'),
                'tax_name'            => $this->l('Gift-wrapping tax'),
                'tax_rate'            => $a/$order->total_wrapping_tax_excl,
                'tax_amount'          => $order->total_wrapping_tax_incl - $order->total_wrapping_tax_excl,
                'product_qty'         => 1,
                'unit_price_tax_excl' => $order->total_wrapping_tax_excl,
                'unit_price_tax_incl' => $order->total_wrapping_tax_incl
            ));
        }
    }
    public static function utf8Encode($value)
    {
        // echo mb_internal_encoding();
        $arr_encodeing=mb_detect_encoding($value, mb_list_encodings(), true);
        if (!empty($arr_encodeing)) {
            $value = mb_convert_encoding($value, "UTF-8", $arr_encodeing);
            //$value=w1250_to_utf8($value);
        }
        return $value;
    }
    // return string
    public static function enNonlatin($arr)
    {
        foreach ($arr as $key => & $value) {
            $arr[$key] = self::utf8Encode($value);
            
        }
        $str = Tools::jsonEncode($arr);
        $str = str_replace('\u', '#u', $str);
       
        return $str;
    }
    // return array
    public static function deNonlatin($str)
    {
        $c = str_replace('#u', '\u', $str);
        
        $d = Tools::jsonDecode($c);
        //echo '<pre>';var_dump($d);die;
        return $d;
    }
    /**
     * For a given product, returns the warehouses it is stored in
     *
     * @param int $id_product Product Id
     * @param int $id_product_attribute Optional, Product Attribute Id - 0 by default (no attribues)
     * @return array Warehouses Ids and names
     */
    public static function getWarehousesByProductId($id_product, $id_product_attribute = 0)
    {
        if (!$id_product && !$id_product_attribute) {
            return array();
        }

        $query = new DbQuery();
        $query->select('DISTINCT w.id_warehouse, CONCAT(w.reference, " - ", w.name) as name, wpl.location');
        $query->from('warehouse', 'w');
        $query->leftJoin('warehouse_product_location', 'wpl', 'wpl.id_warehouse = w.id_warehouse');
        if ($id_product) {
            $query->where('wpl.id_product = '.(int)$id_product);
        }
        if ($id_product_attribute) {
            $query->where('wpl.id_product_attribute = '.(int)$id_product_attribute);
        }
        $query->orderBy('w.reference ASC');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }
}
