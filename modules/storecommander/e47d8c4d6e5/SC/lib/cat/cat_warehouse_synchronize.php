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
$id_lang=(int)Tools::getValue('id_lang');
$id_warehouse=(int)Tools::getValue('id_warehouse');

$return = array(
	"type"=>"error",
	"message"=>"",
	"debug"=>""
);

if(!empty($id_warehouse))
{
	$res = Db::getInstance()->executeS('SELECT w.id_product FROM '._DB_PREFIX_.'warehouse_product_location w WHERE w.id_warehouse = '.(int)$id_warehouse);

	$ids = array();
	foreach($res as $row)
		$ids[] = $row['id_product'];
	
	SCI::synchronizeArrayOfProducts($ids);

	$return = array(
			"type"=>"success",
			"message"=>"",
			"debug"=>""
	);
}


echo json_encode($return);