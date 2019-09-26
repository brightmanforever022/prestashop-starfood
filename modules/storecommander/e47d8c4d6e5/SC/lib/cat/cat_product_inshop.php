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
$id_product = intval(Tools::getValue('id_product','0'));

$id_shop_default = SCI::getConfigurationValue("PS_SHOP_DEFAULT");

$present = false;

if(!empty($id_product) && !empty($id_shop_default))
{
	$sql2 ="SELECT id_product
				FROM "._DB_PREFIX_."product_shop
				WHERE id_product = '".intval($id_product)."'
					AND id_shop = '".intval($id_shop_default)."'";
	$res2 = Db::getInstance()->ExecuteS($sql2);
	foreach($res2 as $product)
	{
		if(!empty($product["id_product"]))
		{
			$present = $product["id_product"];
		}
	}
}

echo intval($present);