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
class AdminGwadvancedinvoiceconfigController extends ModuleAdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
		$this->display = 'edit';
		$this->meta_title = $this->l('Advanced Invoice Template Builder');
		parent::__construct();
		if (!$this->module->active)
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
	}
    public function setMedia()
	{
	   parent::setMedia();
       $this->addJqueryPlugin('colorpicker');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/admin/colResizable-1.5.min.js');
       $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/admin/gwadvancedinvoice.js');
       return true;
	}
    public function initContent()
	{
		$this->display = 'edit';
		$this->initTabModuleList();
		$this->initToolbar();
		$this->initPageHeaderToolbar();
		$this->content .= $this->renderForm();
		$this->content .= $this->initAddNewFont();
        $this->content .= $this->initAddNewBaseTemplate();

		$this->context->smarty->assign(array(
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,			
			'show_page_header_toolbar' => $this->show_page_header_toolbar,
			'page_header_toolbar_title' => $this->page_header_toolbar_title,
			'page_header_toolbar_btn' => $this->page_header_toolbar_btn
		));
	}
    public function initToolBarTitle()
	{
		$this->toolbar_title[] = $this->l('Advanced Invoice Template Builder');
		$this->toolbar_title[] = $this->l('Genaral Settings');
	}
    public function initAddNewFont(){
        $this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Add new font'),
				'icon' => 'icon-list'
			),
			'input' => array(
				array(
					'type' => 'file',
					'label' => $this->l('New font'),
					'name' => 'font',
					'required' => true,
					'desc' => $this->l('Upload your customize font for your invoice. You must upload .ttf format'),
				),
                array(
					'type' => 'hidden',
					'label' => $this->l('Font'),
					'name' => 'newfont',
					'value' => '1',
				)
			),
			'submit' => array(
				'title' => $this->l('Add'),
				'id' => 'submitAddNewFont',
				'icon' => 'process-icon-save'
			)
		);
		$this->show_toolbar = false;
		$this->show_form_cancel_button = false;
		$this->toolbar_title = $this->l('Font');
		return parent::renderForm();
    }
    public function initAddNewBaseTemplate(){
        $this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Add new base template'),
				'icon' => 'icon-file'
			),
			'input' => array(
				array(
					'type' => 'file',
					'label' => $this->l('Template'),
					'name' => 'template',
					'required' => true,
					'desc' => $this->l('You can add new template When we update new invoice template in future.So you dont have to reinstall the module.'),
				),
                array(
					'type' => 'hidden',
					'label' => $this->l('Font'),
					'name' => 'newtemplate',
					'value' => '1',
				)
			),
			'submit' => array(
				'title' => $this->l('Add'),
				'id' => 'submitAddNewTemplate',
				'icon' => 'process-icon-save'
			)
		);
        
		$this->show_toolbar = false;
		$this->show_form_cancel_button = false;
		$this->toolbar_title = $this->l('Template');
		return parent::renderForm();
    }
    public function initPageHeaderToolbar()
	{
        $this->page_header_toolbar_btn = array(
            'new' => array(
                'href' => $this->context->link->getAdminLink('AdminGwadvancedinvoicetemplate'),
                'desc' => $this->l('Manage Templates', null, null, false),
                'icon' => 'process-icon-duplicate'
            ),
            'about' => array(
                'href' => $this->context->link->getAdminLink('AdminGwadvancedinvoiceabout'),
                'desc' => $this->l('Document', null, null, false),
                'icon' => 'process-icon-modules-list'
            ),
        );
		parent::initPageHeaderToolbar();
	}
    public function postProcess()
	{
	   
        if (Tools::isSubmit('saveConfig'))
        {
            $shop_groups_list = array();
			$shops = Shop::getContextListShopID();
            $shop_context = Shop::getContext();
            //$allgroup = Group::getGroups((int)$this->context->language->id,(int)$shop_id);
            $allgroup = Group::getGroups((int)$this->context->language->id,(int)Context::getContext()->shop->id);
            $res = true;
            foreach ($shops as $shop_id)
			{
				$shop_group_id = (int)Shop::getGroupFromShop((int)$shop_id, true);
				if (!in_array($shop_group_id, $shop_groups_list))
					$shop_groups_list[] = (int)$shop_group_id;
				$res &= Configuration::updateValue('GWADVANCEDINVOICE_ACTIVE', (int)Tools::getValue('GWADVANCEDINVOICE_ACTIVE'), false, (int)$shop_group_id, (int)$shop_id);
				$res &= Configuration::updateValue('GWADVANCEDINVOICE_TEMPLATE', (int)Tools::getValue('GWADVANCEDINVOICE_TEMPLATE'), false, (int)$shop_group_id, (int)$shop_id);
			    $res &= Configuration::updateValue('GWADVANCEDDELIVERY_TEMPLATE', (int)Tools::getValue('GWADVANCEDDELIVERY_TEMPLATE'), false, (int)$shop_group_id, (int)$shop_id);
                if($allgroup)
                    foreach($allgroup as $group){
                        $res &=Configuration::updateValue('GWADVANCEDINVOICE_GROUP_'.(int)$group['id_group'],(int)Tools::getValue('GWADVANCEDINVOICE_GROUP_'.(int)$group['id_group']), false, (int)$shop_group_id, (int)$shop_id);
                        $res &=Configuration::updateValue('GWADVANCEDIDELIVERY_GROUP_'.(int)$group['id_group'],(int)Tools::getValue('GWADVANCEDIDELIVERY_GROUP_'.(int)$group['id_group']), false, (int)$shop_group_id, (int)$shop_id);
                    }
            }
			/* Update global shop context if needed*/
			switch ($shop_context)
			{
				case Shop::CONTEXT_ALL:
					$res &= Configuration::updateValue('GWADVANCEDINVOICE_ACTIVE', (int)Tools::getValue('GWADVANCEDINVOICE_ACTIVE'));
					$res &= Configuration::updateValue('GWADVANCEDINVOICE_TEMPLATE', (int)Tools::getValue('GWADVANCEDINVOICE_TEMPLATE'));
                    $res &= Configuration::updateValue('GWADVANCEDDELIVERY_TEMPLATE', (int)Tools::getValue('GWADVANCEDDELIVERY_TEMPLATE'));
					if($allgroup)
                    foreach($allgroup as $group){
                        $res &=Configuration::updateValue('GWADVANCEDINVOICE_GROUP_'.(int)$group['id_group'],(int)Tools::getValue('GWADVANCEDINVOICE_GROUP_'.(int)$group['id_group']));
                        $res &=Configuration::updateValue('GWADVANCEDIDELIVERY_GROUP_'.(int)$group['id_group'],(int)Tools::getValue('GWADVANCEDIDELIVERY_GROUP_'.(int)$group['id_group']));
                    }
                    if (count($shop_groups_list))
					{
						foreach ($shop_groups_list as $shop_group_id)
						{
							$res &= Configuration::updateValue('GWADVANCEDINVOICE_ACTIVE', (int)Tools::getValue('GWADVANCEDINVOICE_ACTIVE'), false, (int)$shop_group_id);
							$res &= Configuration::updateValue('GWADVANCEDINVOICE_TEMPLATE', (int)Tools::getValue('GWADVANCEDINVOICE_TEMPLATE'), false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GWADVANCEDDELIVERY_TEMPLATE', (int)Tools::getValue('GWADVANCEDDELIVERY_TEMPLATE'), false, (int)$shop_group_id);
						    if($allgroup)
                                foreach($allgroup as $group){
                                    $res &=Configuration::updateValue('GWADVANCEDINVOICE_GROUP_'.(int)$group['id_group'],(int)Tools::getValue('GWADVANCEDINVOICE_GROUP_'.(int)$group['id_group']), false, (int)$shop_group_id);
                                    $res &=Configuration::updateValue('GWADVANCEDIDELIVERY_GROUP_'.(int)$group['id_group'],(int)Tools::getValue('GWADVANCEDIDELIVERY_GROUP_'.(int)$group['id_group']), false, (int)$shop_group_id);
                                }
                        }
					}
					break;
				case Shop::CONTEXT_GROUP:
					if (count($shop_groups_list))
					{
						foreach ($shop_groups_list as $shop_group_id)
						{
							$res &= Configuration::updateValue('GWADVANCEDINVOICE_ACTIVE', (int)Tools::getValue('GWADVANCEDINVOICE_ACTIVE'), false, (int)$shop_group_id);
							$res &= Configuration::updateValue('GWADVANCEDINVOICE_TEMPLATE', (int)Tools::getValue('GWADVANCEDINVOICE_TEMPLATE'), false, (int)$shop_group_id);
						    $res &= Configuration::updateValue('GWADVANCEDDELIVERY_TEMPLATE', (int)Tools::getValue('GWADVANCEDDELIVERY_TEMPLATE'), false, (int)$shop_group_id);
                            if($allgroup)
                                foreach($allgroup as $group){
                                    $res &=Configuration::updateValue('GWADVANCEDINVOICE_GROUP_'.(int)$group['id_group'],(int)Tools::getValue('GWADVANCEDINVOICE_GROUP_'.(int)$group['id_group']), false, (int)$shop_group_id);
                                    $res &=Configuration::updateValue('GWADVANCEDIDELIVERY_GROUP_'.(int)$group['id_group'],(int)Tools::getValue('GWADVANCEDIDELIVERY_GROUP_'.(int)$group['id_group']), false, (int)$shop_group_id);
                                }
                                    
                        }
					}
					break;
			}
            if (!$res)
				$this->errors[] = $this->l('The configuration could not be updated.');
			else
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminGwadvancedinvoiceconfig', true));
        }elseif (Tools::isSubmit('newfont')){
            $type = Tools::strtolower(Tools::substr(strrchr($_FILES['font']['name'], '.'), 1));
            if (isset($_FILES['font']) &&
					isset($_FILES['font']['tmp_name']) &&
					!empty($_FILES['font']['tmp_name']) &&
					$type == 'ttf'
				)
				{
				    
				    if(Tools::file_exists_no_cache(_PS_MODULE_DIR_ . 'gwadvancedinvoice/views/fonts/'.$_FILES['font']['name'])){
				        $this->errors[] = $this->l('Font already exists.');
				    }else{
				        try {
                            if(move_uploaded_file($_FILES['font']['tmp_name'],dirname(__FILE__).'/../../views/fonts/'.$_FILES['font']['name'])){
                                $pdf_renderer = new PDFGenerator((bool)Configuration::get('PS_PDF_USE_CACHE'));
                                $pdf_renderer->addTTFfont(_PS_MODULE_DIR_ . 'gwadvancedinvoice/views/fonts/'.$_FILES['font']['name'], 'TrueTypeUnicode', '', 96);
                            }else{
                                $this->errors[] = $this->l('Font could not be uploaded.');
                            }
                        } catch (Exception $e) {
                            $this->errors[] = $e->getMessage();
                        }
				    }
                        
				}else
                    $this->errors[] = $this->l('Font could not be uploaded.');
        }elseif (Tools::isSubmit('newtemplate')){
            if (isset($_FILES['template']) &&
					isset($_FILES['template']['tmp_name']) &&
					!empty($_FILES['template']['tmp_name'])
				)
				{
				    if (Tools::substr($_FILES['template']['name'], -4) != '.tar' && Tools::substr($_FILES['template']['name'], -4) != '.zip'
				        && Tools::substr($_FILES['template']['name'], -4) != '.tgz' && Tools::substr($_FILES['template']['name'], -7) != '.tar.gz')
                        $this->errors[] = Tools::displayError('Unknown archive type.');
                    elseif(move_uploaded_file($_FILES['template']['tmp_name'],_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$_FILES['template']['name']))
                        $this->extractArchive(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$_FILES['template']['name']);
                    else
                        $this->errors[] = $this->l('Template could not be uploaded.');
                }
                else
                    $this->errors[] = $this->l('Template could not be uploaded.');
        }
    }
    protected function extractArchive($file)
	{
		$zip_folders = array();
		$tmp_folder = _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.md5(time());
        $exists = false;
		$success = false;
        $pdf_renderer = new PDFGenerator((bool)Configuration::get('PS_PDF_USE_CACHE'));
		if (Tools::substr($file, -4) == '.zip')
		{
			if (Tools::ZipExtract($file, $tmp_folder))
			{
				$zip_folders = scandir($tmp_folder);
                
                foreach($zip_folders as $zip_folder){
                    if (!in_array($zip_folder, array('.', '..', '.svn', '.git', '__MACOSX'))){
                        if(Tools::file_exists_no_cache(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$zip_folder)){
                            $exists = true;
                            break;
                        } 
                    }
                }
                if(!$exists){
                    if (Tools::ZipExtract($file, _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base')){
                        $success = true;
                        foreach($zip_folders as $zip_folder){
                            if (!in_array($zip_folder, array('.', '..', '.svn', '.git', '__MACOSX'))){
                                $fonts_dir = _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$zip_folder.'/fonts';
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
                                //move image to img folder
                                $img_dir = _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$zip_folder.'/img';
                                if(Tools::file_exists_no_cache($img_dir)){
                                    $this->recurse_copy($img_dir,_PS_MODULE_DIR_.$this->module->name.'/views/img/imgtemplates/'.$zip_folder,true);
                                }
                            }
                         }
                    }
					        
                }else
                    $this->errors[] = $this->l('Template already exists.');
			}
		}else
		{
			require_once(_PS_TOOL_DIR_.'tar/Archive_Tar.php');
			$archive = new Archive_Tar($file);
			if ($archive->extract($tmp_folder))
			{
				$zip_folders = scandir($tmp_folder);
                foreach($zip_folders as $zip_folder){
                    if (!in_array($zip_folder, array('.', '..', '.svn', '.git', '__MACOSX'))){
                        if(Tools::file_exists_no_cache(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$zip_folder)){
                            $exists = true;
                            break;
                        } 
                    }
                }
                if(!$exists){
    				if ($archive->extract( _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base')){
    				    $success = true;
                        foreach($zip_folders as $zip_folder){
                            if (!in_array($zip_folder, array('.', '..', '.svn', '.git', '__MACOSX'))){
                                // install fonts
                                $fonts_dir = _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$zip_folder.'/fonts';
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
                                //move image to img folder
                                $img_dir = _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/tpltemplates/base/'.$zip_folder.'/img';
                                if(Tools::file_exists_no_cache($img_dir)){
                                    $this->recurse_copy($img_dir,_PS_MODULE_DIR_.$this->module->name.'/views/img/imgtemplates/'.$zip_folder,true);
                                }
                            }
                         }
                    }	
                }else
                    $this->errors[] = $this->l('Template already exists.');
			}
		}
        
		if (!$success)
			$this->errors[] = $this->l('Template could not be uploaded.'); 
        @unlink($file);
		$this->recursiveDeleteOnDisk($tmp_folder);
		return $success;
	}
    protected function recursiveDeleteOnDisk($dir)
	{
		if (is_dir($dir))
		{
			$objects = scandir($dir);
			foreach ($objects as $object)
				if ($object != '.' && $object != '..')
				{
					if (filetype($dir.'/'.$object) == 'dir')
						$this->recursiveDeleteOnDisk($dir.'/'.$object);
					else
						unlink($dir.'/'.$object);
				}
			reset($objects);
			rmdir($dir);
		}
	}
    protected function recurse_copy($src,$dst,$delete=false) { 
        $dir = opendir($src); 
        @mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    $this->recurse_copy($src . '/' . $file,$dst . '/' . $file); 
                } 
                else { 
                    copy($src . '/' . $file,$dst . '/' . $file); 
                } 
            } 
        } 
        closedir($dir); 
        if($delete)
            $this->recursiveDeleteOnDisk($src);
    } 
    
    public function renderForm() {
        $_templates = gwadvancedinvoicetemplateModel::getAllBlock();
        $templates = array();
        $templates[] = array(
    				'value' => '',
    				'name' => $this->l('-- Choose template --')
    			);
       if($_templates)
        foreach($_templates as $template){
            $templates[] = array(
    				'value' => $template['id_gwadvancedinvoicetemplate'],
    				'name' => $template['title']
    			);
        }
        $template_url  = 'index.php?controller=AdminGwadvancedinvoicetemplate&token='.Tools::getAdminTokenLite('AdminGwadvancedinvoicetemplate');
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('General'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
					'type' => 'select',
					'label' => $this->l('Default invoice'),
					'hint' => $this->l('Select an invoice template that you created. Then it will be used for default invoice for customer group and generate multi invoice'),
					'name' => 'GWADVANCEDINVOICE_TEMPLATE',
                    /* for new version 1.0.5 */
                    'required' => true,
                    /* end*/
                    'desc' => $this->l('The field is require to active the module. If the list is empty, ').'<a href="'.$template_url.'">'.$this->l('Click here').'</a>'.$this->l(' to create template.'),
					'lang' => false,
					'class' => 'GWADVANCEDINVOICE_TEMPLATE',
					'options' => array(
						'query' => $templates,
						'id' => 'value',
						'name' => 'name'
					)
                ),
                array(
                    'type' => 'customergroupselect',
                    'label' => $this->l('Invoice for customer group'),
                    'name' => 'GWADVANCEDINVOICE_CUSTOMER_TEMPLATE',
                    'desc' => $this->l('You can select an invoice for a special customer group. If you don\'t select then default invoice will be used.'),
                    'options'=>$templates
                ),
                array(
					'type' => 'select',
					'label' => $this->l('Default delivery'),
					'hint' => $this->l('Select an delivery template that you created. Then it will be used for default delivery for customer group and generate multi delivery'),
					'name' => 'GWADVANCEDDELIVERY_TEMPLATE',
                    /* for new version 1.0.5 */
                    'required' => true,
                    /* end*/
                    'desc' => $this->l('The field is require to active the module. If the list is empty, ').'<a href="'.$template_url.'">'.$this->l('Click here').'</a>'.$this->l(' to create template.'),
					'lang' => false,
					'class' => 'GWADVANCEDDELIVERY_TEMPLATE',
					'options' => array(
						'query' => $templates,
						'id' => 'value',
						'name' => 'name'
					)
                ),
                array(
                    'type' => 'customergroupselect',
                    'label' => $this->l('Delivery for customer group'),
                    'name' => 'GWADVANCEDDELIVERY_CUSTOMER_TEMPLATE',
                    'desc' => $this->l('You can select an delivery for a special customer group. If you don\'t select then default delivery will be used.'),
                    'options'=>$templates
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active Module'),
    				'hint' => $this->l('Disable the module if you want to use default invoice of Prestashop'),
                    'name' => 'GWADVANCEDINVOICE_ACTIVE',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(array(
                            'id' => 'GWADVANCEDINVOICE_ACTIVE_on',
                            'value' => 1,
                            'label' => $this->l('Yes')), array(
                            'id' => 'GWADVANCEDINVOICE_ACTIVE_off',
                            'value' => 0,
                            'label' => $this->l('No')))),
                array(
                    'type' => 'free',
                    'name' => 'warrning_text',
                    'label' => $this->l(''),
    				'desc' => $this->l('You MUST create template, then select a template before ACTIVE MODULE. So DON\'T active module if you haven\'t yet choose any template.')
                    ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'saveConfig'
            )
        );
        $this->fields_value = $this->getConfigFieldsValues();
        return parent::renderForm();
    }
    public function getConfigFieldsValues()
	{
		$id_shop_group = Shop::getContextShopGroupID();
		$id_shop = Shop::getContextShopID(); 
        if($id_shop <= 0) $id_shop = (int)Context::getContext()->shop->id;
        $allgroup = Group::getGroups((int)$this->context->language->id,(int)$id_shop);
        $fields = array(
			'GWADVANCEDINVOICE_ACTIVE' => Tools::getValue('GWADVANCEDINVOICE_ACTIVE', Configuration::get('GWADVANCEDINVOICE_ACTIVE', null, $id_shop_group, $id_shop)),
			'GWADVANCEDINVOICE_TEMPLATE' => Tools::getValue('GWADVANCEDINVOICE_TEMPLATE', Configuration::get('GWADVANCEDINVOICE_TEMPLATE', null, $id_shop_group, $id_shop)),
		    'GWADVANCEDDELIVERY_TEMPLATE' => Tools::getValue('GWADVANCEDDELIVERY_TEMPLATE', Configuration::get('GWADVANCEDDELIVERY_TEMPLATE', null, $id_shop_group, $id_shop)),
            'groups'=>$allgroup
        );
        if($allgroup) 
            foreach($allgroup as $group)
            {
                $fields['GWADVANCEDINVOICE_GROUP_'.(int)$group['id_group']] = Tools::getValue('GWADVANCEDINVOICE_GROUP_'.(int)$group['id_group'], Configuration::get('GWADVANCEDINVOICE_GROUP_'.(int)$group['id_group'], null, $id_shop_group, $id_shop));
                $fields['GWADVANCEDIDELIVERY_GROUP_'.(int)$group['id_group']] = Tools::getValue('GWADVANCEDIDELIVERY_GROUP_'.(int)$group['id_group'], Configuration::get('GWADVANCEDIDELIVERY_GROUP_'.(int)$group['id_group'], null, $id_shop_group, $id_shop));
            };
		return $fields;
	}
 }
?>