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
$id_lang=intval(Tools::getValue('id_lang'));
$id_category=intval(Tools::getValue('id_category'),0);
$row=explode(';',Tools::getValue('positions'));
$todo=array();
$updated_products=array();
foreach($row AS $v)
{
	if ($v!='')
	{
		$pos=explode(',',$v);
		$todo[]="UPDATE "._DB_PREFIX_."category_product SET position=".(intval($pos[1]))." WHERE id_category=".intval($id_category)." AND id_product=".intval($pos[0]);
		$updated_products[intval($pos[0])]=intval($pos[0]);
	}
}
foreach($todo AS $task)
{
	Db::getInstance()->Execute($task);
}

// PM Cache
if(!empty($updated_products))
	ExtensionPMCM::clearFromIdsProduct($updated_products);