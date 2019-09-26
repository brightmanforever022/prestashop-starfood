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

class BaOrderInvoice extends ba_prestashop_invoice
{
    private $ps_where = array();
    private $ps_searchable_fields=array('name', 'status', 'id_lang');
    public function __construct()
    {
        parent::__construct();
    }
    public function createbaOrderInvoiceList()
    {
        $helper = new HelperList();
        $helper->no_link = true;
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->actions = array('edit', 'Duplicate', 'delete');
        $helper->toolbar_btn['new'] =  array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name
            .'&add'.$this->name.'_invoice&task=orderinvoice&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );
        $helper->toolbar_btn['import'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&import'.$this->name
            .'_invoice&task=orderinvoice&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Import')
        );
        $helper->identifier = 'id';// Ten khoa chinh
        $helper->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
        $helper->show_toolbar = true;
        $helper->title = $this->l('Invoice Manager');
        $helper->table = $this->name.'_invoice';
        $helper->list_id = $this->name.'_invoice';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex=AdminController::$currentIndex.'&configure='.$this->name.'&task=orderinvoice';
        $page = Tools::getValue('submitFilter'.$helper->list_id);
        if (!empty($page)) {
            $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name
            .'&task=orderinvoice&submitFilter'.$helper->list_id.'='.(int)$page;
        }
        ///////////
        
        $language_options=array();
        foreach ($this->languagesArr as $v) {
            $language_options[$v['id_lang']]=trim($v['name']);
        }
        // fillter by language
        $fields_list = array(
            'thumbnail' => array(
                'title' => $this->l('Photo'),
                'type' => 'text',
                'width' => 40,
                'align' => 'left',
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'callback' =>'getImageToHelpperList',
                'callback_object' =>$this,
                'class' => 'image_helpperlist'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 100,
                'type' => 'text',
                'align' => 'left name'
            ),
            'useAdminOrClient' => array(
                'title' => $this->l('Use Admin or Client'),
                'width' => 100,
                'type' => 'text',
                'align' => 'left name',
                'callback' =>'callBackUseAdminOrClient',
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'callback_object' =>$this
            ),
            'status' => array(
                'title' => $this->l('Default'),
                'active' => 'status',
                'width' => 100,
                'type' => 'bool',
                'align' => 'center',
                'orderby' => false
            ),
            'id_lang' => array(
                'title' => $this->l('Language'),
                'width' => 100,
                'type' => 'select',
                'align' => 'left',
                'list' =>$language_options,
                'filter_type' =>'int',
                'filter_key' =>'id_lang',
                'callback' =>'fillLanguageName',
                'callback_object' =>$this
            ),
            'description' => array(
                'title' => $this->l('Description'),
                'width' => 100,
                'type' => 'text',
                'align' => 'center',
                'callback' =>'defaultDescription',
                'callback_object' =>$this
            )
            
        );
        $helper->listTotal=$this->getTotalList($helper);
        $html=$helper->generateList($this->getListContentInvoice($helper), $fields_list);
        return $html;
    }
    public function callBackUseAdminOrClient($useAdminOrClient)
    {
        if ($useAdminOrClient=="0") {
            return "Admin and Client";
        } elseif ($useAdminOrClient=="1") {
            return "Admin";
        } else {
            return "Client";
        }
    }
    public function getTotalList($helper)
    {
        // var_dump($this->context->shop->id);
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        //// Order by
        $orderby = pSQL(Tools::getValue($helper->list_id."Orderby", "name"));
        $orderway = pSQL(Tools::getValue($helper->list_id."Orderway", "ASC"));
        $sql = 'SELECT count(*) FROM '._DB_PREFIX_.'ba_prestashop_invoice WHERE id_shop='.$this->context->shop->id;
        $sql .= $this->setWhereClause($helper);
        $sql .=' ORDER BY '.$orderby.' '.$orderway;
        $total=$db->getValue($sql);
        return $total;
    }
    public function getListContentInvoice($helper)
    {
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        //// Order by
        $orderby = pSQL(Tools::getValue($helper->list_id."Orderby", "name"));
        $orderway = pSQL(Tools::getValue($helper->list_id."Orderway", "ASC"));
        
        //pagination
        // get items per page
        $cookiePagination=$this->context->cookie->{$helper->list_id.'_pagination'};
        $selected_pagination=(int)Tools::getValue($helper->list_id.'_pagination', $cookiePagination);
        if ($selected_pagination<=0) {
            $selected_pagination = 20;
        }
        $this->context->cookie->{$helper->list_id.'_pagination'}=$selected_pagination; // get Selected Page number
        //$cookiepage=$this->context->cookie->{'submitFilter'.$helper->list_id};
        $page = (int)Tools::getValue('submitFilter'.$helper->list_id);
        if (!$page) {
            $page =1;
        }
        //$this->context->cookie->{'submitFilter'.$helper->list_id}=$page;
        $start = ($page -1 )* $selected_pagination;
        $sql = 'SELECT * FROM '._DB_PREFIX_.'ba_prestashop_invoice WHERE id_shop='.$this->context->shop->id;
        // $id = $this->context->shop->id;
        // echo "<pre>";print_r($id);
        $sql .= $this->setWhereClause($helper);
        $sql.=' ORDER BY '.$orderby.' '.$orderway.' LIMIT '.$start.', '.$selected_pagination;
        $rows = $db->ExecuteS($sql);
        //echo "<pre>";var_dump($sql);die;
        return $rows;
    }
    
    public function setWhereClause($helper)
    {
        //var_dump($_POST);die;
        foreach ($this->ps_searchable_fields as $search_field) {
            $search_value=Tools::getValue($helper->list_id."Filter_".$search_field, null);
            if ($search_value!==null) {
                $this->ps_where[]=" $search_field LIKE '%".pSQL($search_value)."%' ";
                $this->context->cookie->{$helper->list_id.'Filter_'.$search_field}=pSQL($search_value);
            } else {
                $this->context->cookie->{$helper->list_id.'Filter_'.$search_field}=null;
            }
        }
        if (!empty($this->ps_where)) {
            $whereClause = " AND ".implode(" AND ", $this->ps_where);
        } else {
            $whereClause = '';
        }
        return $whereClause;
    }
    
    public function caseInvoice()
    {
        $html="";
        $adminControllers=AdminController::$currentIndex;
        $token='&token='.Tools::getAdminTokenLite('AdminModules');
        $configAndTask='&configure='.$this->name.'&task=orderinvoice';
        if (Tools::isSubmit('submitBaSave')) {
            $this->saveDataInvoice();
            return $html.=$this->createbaOrderInvoiceList();
        } elseif (Tools::isSubmit('submitBaSaveAndStay')) {
            $id  = Tools::getValue('id');
            $this->saveDataInvoice();
            if (empty($id)) {
                $id = Db::getInstance()->Insert_ID();
            }
            $url = $adminControllers.$token.$configAndTask.'&id='.$id.'&updateba_prestashop_invoice_invoice';
            Tools::redirectAdmin($url);
        } elseif (Tools::isSubmit('submitCancel')) {
            Tools::redirectAdmin($adminControllers.$token.$configAndTask);
        } elseif (Tools::isSubmit('submitResetba_prestashop_invoice_invoice')) {
            Tools::redirectAdmin($adminControllers.$token.$configAndTask);
        } elseif (Tools::isSubmit('import')) {
            $this->importDataPDF("ba_prestashop_invoice", "orderinvoice", "importba_prestashop_invoice_invoice");
            Tools::redirectAdmin($adminControllers.$token.$configAndTask);
        } elseif (Tools::isSubmit('importba_prestashop_invoice_invoice')) {
            return $this->display("ba_prestashop_invoice", 'views/templates/admin/import.tpl');
        } elseif (Tools::isSubmit('updateba_prestashop_invoice_invoice')) {
            $this->getDataInvoice();
            return $this->display("ba_prestashop_invoice", 'views/templates/admin/order_invoice/invoice_edit.tpl');
        } elseif (Tools::isSubmit('addba_prestashop_invoice_invoice')) {
            $this->getDataInvoice();
            return $this->display("ba_prestashop_invoice", 'views/templates/admin/order_invoice/invoice_add.tpl');
        } elseif (Tools::isSubmit('statusba_prestashop_invoice_invoice')) {
            $id = Tools::getValue('id');
            $sql="SELECT id,id_lang,status,useAdminOrClient FROM "._DB_PREFIX_
            ."ba_prestashop_invoice WHERE id=".(int)$id;
            $result = Db::getInstance()->getRow($sql);
            if ($result['status']=='0') {
                $this->updateUseAdminOrClient($result['useAdminOrClient'], $result['id_lang']);

            }
            $sql="UPDATE "._DB_PREFIX_."ba_prestashop_invoice SET status=1-status WHERE id=".(int)$id;
            Db::getInstance()->execute($sql);
            $html.=$this->displayConfirmation($this->l('Update successful'));
            return $html.=$this->createbaOrderInvoiceList();
        } elseif (Tools::isSubmit('deleteba_prestashop_invoice_invoice')) {
            $id = Tools::getValue('id');
            //$sql="DELETE FROM "._DB_PREFIX_."ba_templates WHERE id=".$id;
            Db::getInstance()->delete('ba_prestashop_invoice', "id=".(int)$id);
            return $this->createbaOrderInvoiceList();
        } elseif (Tools::isSubmit('submitBulkdeleteba_prestashop_invoice_invoice')) {
            $idArray = Tools::getValue('ba_prestashop_invoice_invoiceBox');
            $idString = implode(",", $idArray);
            Db::getInstance()->delete('ba_prestashop_invoice', "id IN (".pSQL($idString).")");
            return $this->createbaOrderInvoiceList();
        } elseif (Tools::isSubmit('duplicateba_prestashop_invoice_invoice')) {
            $this->processDuplicate();
            return $this->createbaOrderInvoiceList();
        } else {
            return $this->createbaOrderInvoiceList();
        }
    }
    
    public function processDuplicate()
    {
        $id = Tools::getValue('id');
        $db = Db::getInstance();
        $sql='INSERT INTO '._DB_PREFIX_.'ba_prestashop_invoice(`showShippingInProductList`, `showDiscountInProductList`,
        `baInvoiceEnableLandscape`, `showPagination`, `name`, `description`, `thumbnail`, `header_invoice_template`,
        `invoice_template`, `footer_invoice_template`, `customize_css`, `numberColumnOfTableTemplaterPro`,
        `columsTitleJson`, `columsContentJson`, `columsColorJson`, `columsColorBgJson`, `id_lang`,
        `useAdminOrClient`, `id_shop`, `id_shop_group`) 
        SELECT `showShippingInProductList`, `showDiscountInProductList`, `baInvoiceEnableLandscape`, `showPagination`, 
        `name`, `description`, `thumbnail`, `header_invoice_template`, `invoice_template`, `footer_invoice_template`, 
        `customize_css`, `numberColumnOfTableTemplaterPro`, `columsTitleJson`, `columsContentJson`, `columsColorJson`, 
        `columsColorBgJson`, `id_lang`, `useAdminOrClient`, `id_shop`, `id_shop_group` 
        FROM '._DB_PREFIX_.'ba_prestashop_invoice WHERE id='. (int) $id;
        $db->query($sql);
        
    }
    
    private function saveDataInvoice()
    {
        //var_dump($ba_lang);die;
        $db = Db::getInstance();
        $sel_language = Tools::getValue('sel_language');
        $id=Tools::getValue('id');
        $name=Tools::getValue('nameInvoice');
        $description=Tools::htmlentitiesUTF8(strip_tags(Tools::getValue('descriptionInvoice')));
        $invoice_template=Tools::htmlentitiesUTF8(Tools::getValue('invoice_template'));
        $header_invoice_template=Tools::htmlentitiesUTF8(Tools::getValue('header_invoice_template'));
        $footer_invoice_template=Tools::htmlentitiesUTF8(Tools::getValue('footer_invoice_template'));
        $customize_css=strip_tags(Tools::htmlentitiesUTF8(Tools::getValue('customize_css')));
        $numberColumn = (int) Tools::getValue('numberColumnOfTableTemplaterPro');
        $colums_title = Tools::getValue('colums_title');
        $columsTitleJson = $this->enNonlatin($colums_title);
        
        $colums_content = Tools::getValue('colums_content');
        $columsContentJson = Tools::jsonEncode($colums_content);
        
        $colums_color = Tools::getValue('colums_color');
        $columsColorJson = Tools::jsonEncode($colums_color);
        
        $colums_bgcolor = Tools::getValue('colums_bgcolor');
        $columsColorBgJson = Tools::jsonEncode($colums_bgcolor);
        
        $showShippingInProductList=(Tools::getIsset("showShippingInProductList")==true)?'Y':'N';
        $showDiscountInProductList=(Tools::getIsset("showDiscountInProductList")==true)?'Y':'N';
        $baInvoiceEnableLandscape=(Tools::getIsset("baInvoiceEnableLandscape")==true)?'Y':'N';
        $showPagination=(Tools::getIsset("showPagination")==true)?'Y':'N';
        $useAdminOrClient=Tools::getValue("useAdminOrClient");
        if (!empty($id)) {
            if ((int) Tools::getValue('status') == 1) {
                $this->updateUseAdminOrClient(Tools::getValue('useAdminOrClient'), $sel_language);
            }
            $db->update("ba_prestashop_invoice", array(
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
                'id_lang' => (int) $sel_language,
                'status' => (int) Tools::getValue('status'),
                'useAdminOrClient' => (int) Tools::getValue('useAdminOrClient'),
                'sum_to_pay_percent' => floatval(Tools::getValue('sum_to_pay_percent')),
                'due_date_plus' => intval(Tools::getValue('due_date_plus')),
                'auto_set_paid' => intval(Tools::getValue('auto_set_paid')),
                'no_tax' => intval(Tools::getValue('no_tax')),
            ), 'id='.(int)$id);
        } else {
            if ((int) Tools::getValue('status') == 1) {
                $this->updateUseAdminOrClient($useAdminOrClient, $sel_language);
            }
            $db->insert("ba_prestashop_invoice", array(
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
                'id_lang' => (int) $sel_language,
                'status' => (int) Tools::getValue('status'),
                'useAdminOrClient' => (int) Tools::getValue('useAdminOrClient'),
                'id_shop' => (int) $this->context->shop->id,
                'id_shop_group' => (int) $this->context->shop->id_shop_group,
                'sum_to_pay_percent' => floatval(Tools::getValue('sum_to_pay_percent')),
                'due_date_plus' => intval(Tools::getValue('due_date_plus')),
                'auto_set_paid' => intval(Tools::getValue('auto_set_paid')),
                'no_tax' => intval(Tools::getValue('no_tax')),
            ));
            $id = $db->Insert_ID();
        }
        // save invoice categories
        require_once 'BaTemplateCategory.php';
        $categoryIds = is_array(Tools::getValue('tpl_cat_ids')) ? Tools::getValue('tpl_cat_ids') : [];
        array_walk($categoryIds, function(&$item, $key) {$item = intval($item); });
        BaTemplateCategory::saveTemplateCats(intval($id), $categoryIds);
    }
    
    public function updateUseAdminOrClient($useAdminOrClient, $id_lang)
    {
        $db = Db::getInstance();
        $id_shop = $this->context->shop->id;
        if ($useAdminOrClient=="1") {
            $db->update("ba_prestashop_invoice", array(
                    'status' => 0
            ), ' id_shop='.$id_shop.' AND (useAdminOrClient=1 OR useAdminOrClient=0)');
        }
        if ($useAdminOrClient=="2") {
            $where =  ' id_shop='.$id_shop.' AND id_lang='.(int) $id_lang.' AND id_shop= '
            .$id_shop.' AND (useAdminOrClient=2 OR useAdminOrClient=0)';
            $db->update("ba_prestashop_invoice", array(
                    'status' => 0
            ), $where);
        }
        if ($useAdminOrClient=="0") {
            $db->update("ba_prestashop_invoice", array(
                    'status' => 0
            ), ' id_lang='.(int) $id_lang.' AND id_shop='.$id_shop);
        }
    }
    private function getDataInvoice()
    {
        $db = Db::getInstance();
        $templateId = intval(Tools::getValue('id'));
        $sql='SELECT * FROM '._DB_PREFIX_.'ba_prestashop_invoice WHERE id='. $templateId;
        $invoiceTemplateArr = $db->ExecuteS($sql);
        
        
        foreach ($invoiceTemplateArr as $key => $invoiceTemplate) {
            $header_invoice_template=Tools::htmlentitiesDecodeUTF8($invoiceTemplate['header_invoice_template']);
            $invoiceTemplateArr[$key]['header_invoice_template'] = $header_invoice_template;
            $description=Tools::htmlentitiesDecodeUTF8($invoiceTemplate['description']);
            $invoiceTemplateArr[$key]['description'] = $description;
            $invoice_template= Tools::htmlentitiesDecodeUTF8($invoiceTemplate['invoice_template']);
            $invoiceTemplateArr[$key]['invoice_template'] =$invoice_template;
            $footer_invoice_template = Tools::htmlentitiesDecodeUTF8($invoiceTemplate['footer_invoice_template']);
            $invoiceTemplateArr[$key]['footer_invoice_template'] = $footer_invoice_template;
            $customize_css = Tools::htmlentitiesDecodeUTF8($invoiceTemplate['customize_css']);
            $invoiceTemplateArr[$key]['customize_css'] = $customize_css;
            $invoiceTemplateArr[$key]['columsTitleJson'] = $this->deNonlatin($invoiceTemplate['columsTitleJson']);
            $invoiceTemplateArr[$key]['columsContentJson'] = Tools::jsonDecode($invoiceTemplate['columsContentJson']);
            $invoiceTemplateArr[$key]['columsColorJson'] = Tools::jsonDecode($invoiceTemplate['columsColorJson']);
            $invoiceTemplateArr[$key]['columsColorBgJson'] = Tools::jsonDecode($invoiceTemplate['columsColorBgJson']);
            
        }
        $this->smarty->assign('invoiceTemplateArr', $invoiceTemplateArr);
        
        require_once 'BaTemplateCategory.php';
        $this->smarty->assign('templateCatsList', BaTemplateCategory::getList());
        if ($templateId)
        {
            $this->smarty->assign('templateCatIds', BaTemplateCategory::getTemplateCategoryIds($templateId));
        }
        
        $this->smarty->assign('_PS_MODULE_DIR_', _PS_MODULE_DIR_);
        $languages = Language::getLanguages(false);
        $this->smarty->assign('languages_select', $languages);
        //$this->smarty->assign('ba_lang', $ba_lang);
        $token=Tools::getAdminTokenLite('AdminModules');
        $this->smarty->assign('token', $token);
        $bamodule=AdminController::$currentIndex;
        $this->smarty->assign('bamodule', $bamodule);
        $this->smarty->assign('configure', $this->name);
        $taskBar = 'orderinvoice';
        if (Tools::getValue('task') != false) {
            $taskBar = Tools::getValue('task');
        }
        $this->smarty->assign('taskbar', $taskBar);
        $toolBarBtn=array();
        $toolBarBtn[] = array(
            'imgclass' => 'preview',
            'href'     => 'javascript:void(0)',
            //'target'   => 'demo3',
            //'js'       => 'demo4',
            'desc'     => 'Preview',
            //'class'    => 'demo6'
        );
        $this->smarty->assign('toolBarBtn', $toolBarBtn);
    }
}
