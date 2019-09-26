<?php
/**
* This is main class of module. 
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/

if (!defined("_PS_VERSION_"))
    exit;
include_once(_PS_MODULE_DIR_ . 'gwadvancedinvoice/model/gwadvancedinvoicetemplateModel.php');
class Gwadvancedinvoice extends Module
{
    public function __construct()
    {
        $this->name = "gwadvancedinvoice";
        $this->tab = "billing_invoicing";
        $this->version = "1.1.3";
        $this->author = "Globo Jsc";
        $this->need_instance = 1;
        $this->bootstrap = 1;
        $this->module_key = '4271a0ff21d167ad428a7ca2fb61544c';
        parent::__construct();
        $this->displayName = $this->l("Advanced Invoice Builder");
        $this->description = $this->l("Advanced Invoice Template Builder is the perfect tool to customize your invoice without any technical knowledge required.");
    }
    public function install()
    {
        if (Shop::isFeatureActive()){
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        return parent::install()
            && $this->_createTables()
            && $this->_createTab()
            && $this->_installFonts()
            && $this->registerHook('displayBackOfficeHeader');
    }
    public function uninstall()
    {
        return parent::uninstall()
            && $this->_deleteTables()
            && $this->_deleteTab()
            && $this->unregisterHook("displayBackOfficeHeader");
    }

    private function _createTables()
    {
         $response = (bool) Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gwadvancedinvoicetemplate` (
                `id_gwadvancedinvoicetemplate` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `rtl` INT NULL DEFAULT  "0",
                `active` tinyint(1) unsigned NOT NULL,
                `choose_design` varchar(255) NOT NULL,
                `layout` varchar(255) NOT NULL,
                `activeheader` tinyint(1) unsigned NOT NULL,
                `activefooter` tinyint(1) unsigned NOT NULL,
                `pagesize` varchar(5) NOT NULL,
                `mgheader` INT NOT NULL DEFAULT  "0",
                `mgfooter` INT NOT NULL DEFAULT  "0",
                `mgcontent` VARCHAR( 255 ) NULL,
                `barcodetype` varchar(10) NOT NULL,
                `barcodeformat` varchar(255) NULL,
                `barcodeproducttype` varchar(10) NOT NULL,
                `barcodeproductformat` varchar(255) NULL,
                `pageorientation` varchar(5) NOT NULL,
                `discountval` varchar(15) NOT NULL,
                `customcss` text NULL,
                `template_config` text NULL,
                PRIMARY KEY (`id_gwadvancedinvoicetemplate`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
        $response &= (bool) Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gwadvancedinvoicetemplate_lang` (
                        `id_gwadvancedinvoicetemplate` int(10) unsigned NOT NULL,
                        `id_lang` int(10) unsigned NOT NULL,
                        `title` varchar(255) NOT NULL,
                        `invoice` text  NULL,
                        `header` text  NULL,
                        `footer` text  NULL,
                        `watermark` varchar(255) NULL,
                        `watermarktext` varchar(255) NULL,
                        `watermarkfont` varchar(255) NULL,
                        `watermarksize` INT(10) NULL,
                        `productcolumns` text  NULL,
                        PRIMARY KEY (`id_gwadvancedinvoicetemplate`,`id_lang`)
                    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
                ');
        $response &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gwadvancedinvoicetemplate_shop` (
                `id_gwadvancedinvoicetemplate` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_gwadvancedinvoicetemplate`,`id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
        return $response;
    }
    private function _installFonts(){
        $fonts_dir = _PS_MODULE_DIR_.$this->name.'/views/fonts';
        $pdf_renderer = new PDFGenerator((bool)Configuration::get('PS_PDF_USE_CACHE'));
        if(Tools::file_exists_no_cache($fonts_dir)){
            $fonts = scandir($fonts_dir);
            foreach($fonts as $font){
                if (!in_array($font, array('.', '..', '.svn', '.git', '__MACOSX'))){
                    if (Tools::substr($font, -4) == '.ttf'){
                        $pdf_renderer->addTTFfont($fonts_dir.'/'.$font, 'TrueTypeUnicode', '', 96);
                    }
                }
            }
        }
        return true;
    }
    private function _deleteTables()
    {
        return Db::getInstance()->execute('
                DROP TABLE IF EXISTS    `' . _DB_PREFIX_ . 'gwadvancedinvoicetemplate`, 
                                        `' . _DB_PREFIX_ . 'gwadvancedinvoicetemplate_lang`, 
                                        `' . _DB_PREFIX_ . 'gwadvancedinvoicetemplate_shop`;
        ');
    }
    private function _createTab()
    {
        $res = true;
        $tabparent = "AdminGwadvancedinvoice";
        $id_parent = Tab::getIdFromClassName($tabparent);
        if(!$id_parent){
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = "AdminGwadvancedinvoice";
            $tab->name = array();
            foreach (Language::getLanguages() as $lang){
                $tab->name[$lang["id_lang"]] = "Advanced Invoice";
            }
            $tab->id_parent = 0;
            $tab->module = $this->name;
            $res &= $tab->add();
            $id_parent = $tab->id;
        }
        $subtabs = array(
            array(
                'class'=>'AdminGwadvancedinvoiceconfig',
                'name'=>'Genaral Settings'
            ),
            array(
                'class'=>'AdminGwadvancedinvoicetemplate',
                'name'=>'Manage Templates'
            ),
            array(
                'class'=>'AdminGwadvancedinvoiceabout',
                'name'=>'List Variables'
            ),
        );
        foreach($subtabs as $subtab){
            $idtab = Tab::getIdFromClassName($subtab['class']);
            if(!$idtab){
                $tab = new Tab();
                $tab->active = 1;
                $tab->class_name = $subtab['class'];
                $tab->name = array();
                foreach (Language::getLanguages() as $lang){
                    $tab->name[$lang["id_lang"]] = $subtab['name'];
                }
                $tab->id_parent = $id_parent;
                $tab->module = $this->name;
                $res &= $tab->add();
            }
        }
        return $res;
    }
    private function _deleteTab()
    {
        $id_tabs = array("AdminGwadvancedinvoiceconfig","AdminGwadvancedinvoicetemplate","AdminGwadvancedinvoiceabout");
        foreach($id_tabs as $id_tab){
            $idtab = Tab::getIdFromClassName((int)$id_tab);
            $tab = new Tab((int)$idtab);
            $parentTabID = $tab->id_parent;
            $tab->delete();
            $tabCount = Tab::getNbTabs((int)$parentTabID);
            if ($tabCount == 0){
                $parentTab = new Tab((int)$parentTabID);
                $parentTab->delete();
            }
        }
        return true;
    }
    public function hookDisplayBackOfficeHeader($params){
        $this->context->controller->addCss($this->_path.'/views/css/admin/gwadvancedinvoice.css');
    }
    public function hookAjaxCall($params){
        $res = '';
        if (Configuration::get('PS_CATALOG_MODE'))
			return;
        $this->smarty->assign('templates',$this->getBaseTemplateConfig($params['template'],$params['pagesize']));
        $res['templates'] = $this->display(__FILE__, 'templates.tpl');
        $res = Tools::jsonEncode($res);
		return $res;
    }
    public function hookAjaxCallStyle($params){
        $choose_design = $params['choose_design'];
        $id_language = (int)Context::getContext()->language->id;
        if(isset($params['id_language']) && $params['id_language'] > 0)
            $id_language =  (int)$params['id_language'];
        $file = '';
        $style = '';
        $temp = str_replace('-','/',$choose_design);
        if($temp !='' && is_string($temp)){
            $language = new Language($id_language);
            if(Tools::file_exists_no_cache(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/'.$language->iso_code.'/styles.tpl')){
                $file = _PS_MODULE_DIR_.$this->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/'.$language->iso_code.'/styles.tpl';
            }elseif(Tools::file_exists_no_cache(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/en/styles.tpl')){
                $file = _PS_MODULE_DIR_.$this->name.'/views/templates/admin/tpltemplates/base/'.$temp.'/en/styles.tpl';
            }
            if($file !=''){
                Context::getContext()->smarty->assign($params['template_config']);
                $style = Context::getContext()->smarty->fetch($file);
            }  
        }
        die(strip_tags($style));
    }
    public function getContent()
	{
		Tools::redirectAdmin($this->context->link->getAdminLink('AdminGwadvancedinvoiceconfig'));
	}   
    public function getBaseTemplateConfig($temp = '',$pagesize=''){
        $templates = array();
        $template = null;
        $fontsdir = opendir(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/tpltemplates/base/');
		while (($file = readdir($fontsdir)) !== false) {
		    if (!in_array($file, array('.', '..', '.svn', '.git', '__MACOSX')) && is_dir(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/tpltemplates/base/'.$file)){
                if(Tools::file_exists_no_cache(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/tpltemplates/base/'.$file.'/config.php')){
                    include_once(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/tpltemplates/base/'.$file.'/config.php');
                    //$template = $file::getTemplate();
                    //fix error in php version 5.2
                    $template = call_user_func_array(array($file, 'getTemplate'), array());
                    if($template)
                        $templates[$template['id']] = $template;
                    
                }
		    }
		}
        if($temp !='' && isset($templates["$temp"])){
            return $templates["$temp"];
        }else{
            if($pagesize !=''){
                $results = array();
                foreach($templates as $key=>$template){
                    if(in_array($pagesize,$template['pagesize'])){
                        $results[$key] = $template;
                    }
                }
                return $results;
            }
            else
                return array();
        }
        closedir($fontsdir);
    }
 }
?>