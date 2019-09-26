<?php
/**
* Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/

if (!defined('_PS_ADMIN_DIR_'))
	define('_PS_ADMIN_DIR_', getcwd());
include(_PS_ADMIN_DIR_.'/../../../../config/config.inc.php');
$context = Context::getContext();
if (!isset($context->employee) || !$context->employee->isLoggedBack()) {
    die();
}else{
    $getstyle = (bool)Tools::getValue('getstyle');
    if($getstyle){
        $gwadvancedinvoice = Module::getInstanceByName('gwadvancedinvoice');
        $choose_design = Tools::getValue('choose_design');
        $template_config = Tools::getValue('template_config');
        $id_language = (int)Tools::getValue('id_language');
        if($choose_design !='' && is_array($template_config) && !empty($template_config))
            echo  $gwadvancedinvoice->hookAjaxCallStyle(array('choose_design' => $choose_design,'template_config'=>$template_config,'id_language'=>$id_language));
    }else{
        $gwadvancedinvoice = Module::getInstanceByName('gwadvancedinvoice');
        $pagesize = Tools::getValue('pagesize');
        if($pagesize !='')
            echo $gwadvancedinvoice->hookAjaxCall(array('template' => '','pagesize'=>$pagesize));
    }
}
?>