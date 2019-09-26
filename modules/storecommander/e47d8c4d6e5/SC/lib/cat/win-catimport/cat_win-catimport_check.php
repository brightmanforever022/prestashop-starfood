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
$filename=Tools::getValue('filename','');
$importlimit=Tools::getValue('importlimit','');
$mapppinggridlength=Tools::getValue('mapppinggridlength',0);
$mappingname=Tools::getValue('mappingname','');

include_once(SC_DIR.'lib/php/parsecsv.lib.php');
require_once(SC_DIR.'lib/cat/win-catimport/cat_win-catimport_tools.php');

$return = "";

if(!empty($filename))
{
    // INIT
    $files = array_diff( scandir( SC_CSV_IMPORT_DIR."category/" ), array_merge( Array( ".", "..", "index.php", ".htaccess", SC_CSV_IMPORT_CONF)) );
    readCatImportConfigXML($files);

    $mapping=loadCatMapping($importConfig[$filename]['mapping']);
    $mappingDataArray=explode(';',$mapping);
    $mappingData=array('CSVArray' => array(),'DBArray' => array(),'CSV2DB' => array(),'CSV2DBOptions' => array(),'CSV2DBOptionsMerged' => array());
    foreach($mappingDataArray AS $val)
    {
        if ($val!='')
        {
            $tmp=explode(',',$val);
            $tmp2=$tmp[0];
            escapeCharForPS($tmp2);
            $mappingData['CSVArray'][]=$tmp2;
            $mappingData['DBArray'][]=$tmp[1];
            $mappingData['CSV2DB'][$tmp[0]]=$tmp[1];
            $mappingData['CSV2DBOptions'][$tmp[0]]=$tmp[2];
            $mappingData['CSV2DBOptionsMerged'][$tmp[0]]=$tmp[1].'_'.$tmp[2];
        }
    }

    // LINE LIMIT AND FILE NAME
    $importlimit=($importlimit > 0 ? $importlimit : intval($importConfig[$filename]['importlimit']));

    $return .= _l('<strong>%s</strong> lines of file <strong>"%s"</strong> will be imported.',false,array($importlimit,$filename))."<br/><br/>";
    $return .= _l('The mapping <strong>"%s"</strong> will be used.',false,array(($mappingname != '' ? $mappingname : $importConfig[$filename]['mapping'])))."<br/><br/>";

    // ACTION NEW PRODUCT
    $fornewcat = $importConfig[$filename]['fornewcat'];
    $fornewcat_txt = $fornewcat;
    if($fornewcat=="skip")
        $fornewcat_txt = _l('Skip');
    if($fornewcat=="create")
        $fornewcat_txt =  _l('Check if parent categories exist and create new category');
    if($fornewcat=="createall")
        $fornewcat_txt =  _l('Force creation of parent categories and create new category');

    // ACTION FOUND PRODUCT
    $idby = $importConfig[$filename]['idby'];
    $idby_txt = $idby;
    if($idby=="catname")
        $idby_txt = _l('Category name');
    if($idby=="idcategory")
        $idby_txt =  _l('id_category');
    if($idby=="path")
        $idby_txt =  _l('Path');
    if($idby=="specialIdentifier")
        $idby_txt =  _l('specialIdentifier');

    $forfoundcat = $importConfig[$filename]['forfoundcat'];
    $forfoundcat_txt = $forfoundcat;
    if($forfoundcat=="skip")
        $forfoundcat_txt = _l('Skip');
    if($forfoundcat=="update")
        $forfoundcat_txt =  _l('Modify category');
    if($forfoundcat=="create")
        $forfoundcat_txt =  _l('Created as duplication');

    $return .= _l("Categories will be identified by <strong>%s</strong>.",false,array($idby_txt))."<br/><br/>";
    $return .= _l("Action for new categories: <strong>%s</strong>.",false,array($fornewcat_txt))."<br/><br/>";
    if($fornewcat!="skip")
    {
        $has_path = false;
        $has_name = false;
        $has_parents = false;
        foreach($mappingData['CSV2DB'] as $name=>$field)
        {
            if($field=="path")
                $has_path = true;
            if($field=="name")
                $has_name = true;
            if($field=="parents")
                $has_parents = true;
        }
        if($fornewcat=="createall")
        {
            if($has_path)
                $return .=  _l("To create the category and its parents, the <strong>Path</strong> field will be used")."<br/><br/>";
            if($has_name && $has_parents)
                $return .=  _l("To create the category and its parents, the <strong>Name + Parents</strong> fields will be used")."<br/><br/>";
        }
        elseif($fornewcat=="create")
        {
            if($has_path)
                $return .=  _l("To create the category in its parents, the <strong>Path</strong> field will be used")."<br/><br/>";
            if($has_name && $has_parents)
                $return .=  _l("To create the category in its parents, the <strong>Name + Parents</strong> fields will be used")."<br/><br/>";
        }
    }
    $return .= _l("Action for existing categories: <strong>%s</strong>.",false,array($forfoundcat_txt))."<br/><br/>";

    if ($mapppinggridlength == 1)
        $return .= "<strong>"._l("!!! WARNING !!!")."</strong> "._l("Field/Value separators selected in your configuation do not seem to match your CSV file. Check your settings.")."<br/><br/>";

    if ($fornewcat=="skip" && $forfoundcat=="skip")
        $return .= "<strong>"._l("!!! WARNING !!!")."</strong> "._l("An action needs to be selected before importing.")."<br/><br/>";

    // CHECK MULTILINES
    if ($importConfig[$filename]['fieldsep']=='dcomma') $importConfig[$filename]['fieldsep']=';';
    if ($importConfig[$filename]['fieldsep']=='dcommamac') $importConfig[$filename]['fieldsep']=';';
    $DATAFILE=remove_utf8_bom(file_get_contents(SC_CSV_IMPORT_DIR."category/".$filename));
    $DATA = preg_split("/(?:\r\n|\r|\n)/", $DATAFILE);
    if ($importConfig[$filename]['firstlinecontent']!='')
    {
        $firstLineData=explode($importConfig[$filename]['fieldsep'],$importConfig[$filename]['firstlinecontent']);
        $FIRST_CONTENT_LINE=0;
    }else{
        $firstLineData=explode($importConfig[$filename]['fieldsep'],$DATA[0]);
        $FIRST_CONTENT_LINE=1;
    }
    $nb_element_by_line = count($firstLineData);
    for ($current_line = $FIRST_CONTENT_LINE; ((($current_line <= (count($DATA)-1)) && $line = parseCSVLine($importConfig[$filename]['fieldsep'],$DATA[$current_line]))) ; $current_line++)
    {
        if ($DATA[$current_line]=='') continue;
        if (count($line) < $nb_element_by_line)
        {
            $return .= _l('Veuillez vérifier votre fichier car il semblerait que toutes les lignes ne possèdent pas le bon nombre de colonnes. Cela peut également venir d\'une description sur plusieurs lignes.')."<br/><br/>";
            $return .= _l("Lines of your CSV file do not use the correct number of columns, please check your file. Alternatively, this can be caused by descriptions spread on multiple lines.")."<br/><br/>";
            break;
        }
    }

    $return .= '<img src="lib/img/accept.png" alt="" style="margin-bottom: -4px;" /> <a href="'.getHelpLink('cat_win-catimport_check').'" target="_blank"><b>'._l("Is your import ready? See the Checklist!").'</b></a>';
}

if(!empty($return))
{
    $return = '<div style="font-family: Tahoma;font-size: 12px !important; height: 100%; overflow: auto;"><div style="padding: 10px;">'.$return.'</div></div>';
    echo $return;
}