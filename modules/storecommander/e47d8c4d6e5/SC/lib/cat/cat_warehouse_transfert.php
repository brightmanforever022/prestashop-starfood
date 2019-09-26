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
$id_warehouse_A=(int)Tools::getValue('id_warehouse_A');
$id_warehouse_B=(int)Tools::getValue('id_warehouse_B');
$truncate_A=(int)Tools::getValue('truncate_A', 0);

$return = array(
	"type"=>"error",
	"message"=>"",
	"debug"=>0
);

if(!empty($id_warehouse_A) && !empty($id_warehouse_B) && $id_warehouse_A!=$id_warehouse_B)
{
	// INSERT PRODUCTS IN WAREHOUSE B
	$sql='SELECT * 
	FROM `'._DB_PREFIX_.'warehouse_product_location`
	WHERE
	id_warehouse = "'.(int)$id_warehouse_A.'"
	AND	( 
			(id_product_attribute = 0 AND id_product NOT IN (SELECT id_product FROM `ps_warehouse_product_location` WHERE id_warehouse = "'.(int)$id_warehouse_B.'" AND id_product_attribute = 0))
			OR
			( id_product_attribute>0 AND id_product_attribute NOT IN (SELECT id_product_attribute FROM `ps_warehouse_product_location` WHERE id_warehouse = "'.(int)$id_warehouse_B.'") )
		)';
	
	$res = Db::getInstance()->executeS($sql);
	foreach($res as $location)
	{
		Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'warehouse_product_location (id_product, id_product_attribute, id_warehouse)
				VALUES ("'.(int)$location["id_product"].'","'.(int)$location["id_product_attribute"].'","'.(int)$id_warehouse_B.'")');
	
		//$return["debug"]++;
	}
	
	// TRUNCATE STOCK MVT FOR WAREHOUSE A
	Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'stock_mvt`
			WHERE `id_stock` IN (SELECT id_stock FROM `'._DB_PREFIX_.'stock` WHERE id_warehouse="'.(int)$id_warehouse_A.'")');
	
	// UPDATE ID_WAREHOUSE FROM A TO B
	$stock_manager = StockManagerFactory::getManager();
	$warehouse = new Warehouse((int)$id_warehouse_B);
	$sql='SELECT *
	FROM `'._DB_PREFIX_.'stock`
	WHERE
		id_warehouse = "'.(int)$id_warehouse_A.'"';
	$res = Db::getInstance()->executeS($sql);
	foreach($res as $stock)
	{
		if ($stock_manager->addProduct((int)$stock["id_product"], (int)$stock["id_product_attribute"], $warehouse, (int)$stock["physical_quantity"], 7, $stock["price_te"], true))
		{
			SCI::synchronize($id_product);
			
			Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'stock`
			WHERE id_warehouse="'.(int)$id_warehouse_A.'" AND id_product="'.(int)$stock["id_product"].'" AND id_product_attribute="'.(int)$stock["id_product_attribute"].'"');	
		}	
		if($stock["physical_quantity"]==0)
		{
			Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'stock`
			WHERE id_warehouse="'.(int)$id_warehouse_A.'" AND id_product="'.(int)$stock["id_product"].'" AND id_product_attribute="'.(int)$stock["id_product_attribute"].'"');
		}
	}
	
	if($truncate_A=="1")
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'warehouse_product_location WHERE id_warehouse = "'.(int)$id_warehouse_A.'"');
	
	addToHistory('warehouse','transfer','warehouse',(int)$id_warehouse_A,$id_lang,_DB_PREFIX_."warehouse_product_location",(int)$id_warehouse_B,false);
		
	$return = array(
			"type"=>"success",
			"message"=>"",
			"debug"=>$return["debug"]
	);
}


echo json_encode($return);