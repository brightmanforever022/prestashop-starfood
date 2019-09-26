<?php
/**
* The file is controller. Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/

class AdminGwadvancedinvoiceaboutController extends ModuleAdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
		$this->display = 'view';
		$this->meta_title = $this->l('Advanced Invoice Template Builder');
		parent::__construct();
		if (!$this->module->active)
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
	}
    public function setMedia()
	{
		return parent::setMedia();
	}
    public function initToolBarTitle()
	{
		$this->toolbar_title[] = $this->l('Advanced Invoice Template Builder');
		$this->toolbar_title[] = $this->l('Document');
	}
    public function initPageHeaderToolbar()
	{
        $this->page_header_toolbar_btn = array(

            'cogs' => array(

                'href' => $this->context->link->getAdminLink('AdminGwadvancedinvoiceconfig'),

                'desc' => $this->l('Genaral Settings', null, null, false),

                'icon' => 'process-icon-cogs'

            ),
            'new' => array(

                'href' => $this->context->link->getAdminLink('AdminGwadvancedinvoicetemplate'),

                'desc' => $this->l('Manage Templates', null, null, false),

                'icon' => 'process-icon-duplicate'

            ),

        );
		parent::initPageHeaderToolbar();
	}
    public function renderView()
	{
	  
	   if (version_compare(_PS_VERSION_, '1.5.6.0', '>'))
			$this->base_tpl_view = 'about.tpl';
		return parent::renderView();
	}
}
?>