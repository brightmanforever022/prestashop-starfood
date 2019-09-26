<?php
/**
 * Store Commander
 *
 * @category administration
 * @author Store Commander - support@storecommander.com
 * @version 2015-09-15
 * @uses Prestashop modules
 * @since 2009
 * @copyright Copyright &copy; 2009-2015, Store Commander
 * @license commercial
 * All rights reserved! Copying, duplication strictly prohibited
 *
 * *****************************************
 * *           STORE COMMANDER             *
 * *   http://www.StoreCommander.com       *
 * *            V 2015-09-15               *
 * *****************************************
 *
 * Compatibility: PS version: 1.1 to 1.6.1
 *
 **/
error_reporting(E_ALL ^ E_NOTICE);
@ini_set('display_errors', 'on');

$id_lang=intval(Tools::getValue('id_lang'));
$mapping=Tools::getValue('mapping','');
$mappingname=Tools::getValue('mappingname','');
$mapppinggridlength=Tools::getValue('mapppinggridlength',0);
$filename=Tools::getValue('filename','');
$exportfilename=Tools::getValue('exportfilename','');
$shop=Tools::getValue('shop','');
$category=Tools::getValue('category','');

require_once(SC_DIR.'lib/cat/win-export/cat_win-export_tools.php');

$return = "";

if(!empty($filename)) {

    $filename = str_replace('.script.xml', '', $filename);

    if (empty($exportfilename)) {
        $return .= "<strong>" . _l("!!! WARNING !!!") . "</strong> " . _l('You have to define a filename for the export.') . "<br/><br/>";
    } elseif (strpos($exportfilename, '.csv') == false) {
        $return .= "<strong>"._l("!!! WARNING !!!")."</strong> "._l('You forgot to add the file extension, such as ".csv", to your exported filename.')."<br/><br/>";
    } else {
        $return .= sprintf(_l('The script <strong>%s</strong> will export to <strong>%s</strong> file.'),$filename,$exportfilename)."<br/><br/>";
    }
    if(SCMS) {
        if (!empty($shop)) {
            $return .= sprintf(_l('The shop <strong>"%s"</strong> will be used.'), $shop) . "<br/><br/>";
        } else {
            $return .= "<strong>"._l("!!! WARNING !!!")."</strong> "._l('You have to set the shop for the script.')."<br/><br/>";
        }
    }
    if (!empty($mappingname) || !empty($mapping)) {
        $return .= _l('The mapping <strong>"%s"</strong> will be used.',false,array(($mappingname != '' ? $mappingname : $mapping)))."<br/><br/>";
    } else {
        $return .= "<strong>"._l("!!! WARNING !!!")."</strong> "._l('You have to set the mapping for the script.')."<br/><br/>";
    }
    if (!empty($category) && htmlentities($category) != '&nbsp;') {
        $return .= sprintf(_l('Categories <strong>"%s"</strong> will be used.'),$category)."<br/><br/>";
    } else {
        $return .= "<strong>"._l("!!! WARNING !!!")."</strong> "._l('You have to set the category selection for the script')."<br/><br/>";
    }
}

if(!empty($return))
{
    $return = '<div style="font-family: Tahoma;font-size: 12px !important; height: 100%; overflow: auto;"><div style="padding: 10px;">'.$return.'</div></div>';
    echo $return;
}
