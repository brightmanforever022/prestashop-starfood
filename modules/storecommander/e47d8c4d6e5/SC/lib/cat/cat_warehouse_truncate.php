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
$history=(int)Tools::getValue('history',0);

$return = array(
	"type"=>"error",
	"message"=>"",
	"debug"=>""
);

if(!empty($id_warehouse))
{
	/*$sql = "SELECT id_product,id_product_attribute FROM `"._DB_PREFIX_."warehouse_product_location` WHERE id_warehouse='".(int)$id_warehouse."' AND id_product=3653";
	$res = Db::getInstance()->executeS($sql);
	foreach($res as $row)
	{
		$sql = "SELECT sa.id_shop, sa.depends_on_stock, sa.quantity, ps.advanced_stock_management
					FROM `"._DB_PREFIX_."stock_available` sa
						INNER JOIN `"._DB_PREFIX_."product_shop` ps ON (sa.id_product=ps.id_product AND sa.id_shop=ps.id_shop)
					WHERE sa.id_product='".(int)$row["id_product"]."' AND sa.id_product_attribute='".(int)$row["id_product_attribute"]."'";
		$couples = Db::getInstance()->executeS($sql);
		foreach($couples as $couple)
		{
			// If quantity not empty
			// and Advanced Stock Mgmt. activate
			if($couple["advanced_stock_management"]=="1" && $couple["depends_on_stock"]=="1" && $couple["quantity"]!="0")
			{
				Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."stock_available` SET quantity=0 WHERE id_product='".(int)$row["id_product"]."' AND id_product_attribute='".(int)$row["id_product_attribute"]."' AND id_shop='".(int)$couple["id_shop"]."'");
			}
		}
	}*/

    $sql = "SELECT id_product
					FROM `"._DB_PREFIX_."stock`
					WHERE id_warehouse='".(int)$id_warehouse."'";
    $id_products = Db::getInstance()->executeS($sql);

	if(empty($history))
	{
		Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'stock_mvt`
				WHERE `id_stock` IN (SELECT id_stock FROM `'._DB_PREFIX_.'stock` WHERE id_warehouse="'.(int)$id_warehouse.'")');
		Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'stock` WHERE id_warehouse="'.(int)$id_warehouse.'"');
	}
	else
	{
		Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'stock` SET  physical_quantity=0, usable_quantity=0 WHERE id_warehouse="'.(int)$id_warehouse.'"');
	}

	foreach ($id_products as $product)
    {
        StockAvailable::synchronize((int)$product['id_product']);
    }
	
	addToHistory('warehouse','truncate','warehouse',(int)$id_warehouse,$id_lang,_DB_PREFIX_."stock",(int)$id_warehouse,false);
	
	$return = array(
			"type"=>"success",
			"message"=>"",
			"debug"=>""
	);
}


echo json_encode($return);