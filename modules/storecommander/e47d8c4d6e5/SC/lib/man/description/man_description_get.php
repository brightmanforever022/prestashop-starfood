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

$contentType=Tools::getValue('content','');
if(Tools::getValue('act','')=='man_description_get' && sc_in_array($contentType,array('short_description','description'),"catDescGet_fields"))
{
	$id_manufacturer=Tools::getValue('id_manufacturer','0');
	$id_lang=Tools::getValue('id_lang','0');
	
	$sql = "SELECT ".psql($contentType)." FROM "._DB_PREFIX_."manufacturer_lang WHERE id_manufacturer='".intval($id_manufacturer)."' AND id_lang='".intval($id_lang)."'";
	$row=Db::getInstance()->getRow($sql);
	echo $row[$contentType];
}
