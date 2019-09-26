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


$idlist=Tools::getValue('idlist','');
$action=Tools::getValue('action','');
$id_lang=Tools::getValue('id_lang','0');
$id_shop=Tools::getValue('id_shop','0');
$value=Tools::getValue('value','0');
$id_product=intval(Tools::getValue('id_product'));

$multiple = false;
if(strpos($idlist, ",") !== false)
	$multiple = true;

$ids = explode(",", $idlist);

if($action!='' && !empty($id_shop) && !empty($idlist) && !empty($id_product))
{
	$product = new Product($id_product);
						
	switch($action)
	{
		// Modification de present pour le shop passé en params
		// pour une ou plusieurs déclinaisons passées en params
		case 'present':
			if($value=="true")
				$value = 1;
			else
				$value = 0;
			
			foreach($ids as $id)
			{
				if($value=="1")
				{
					$sql_in_shop ="SELECT id_product_attribute
						FROM "._DB_PREFIX_."product_attribute_shop
						WHERE id_product_attribute = '".(int)$id."'
							AND  id_shop = '".(int)$id_shop."'";
					$in_shop = Db::getInstance()->ExecuteS($sql_in_shop);
					if(empty($in_shop[0]["id_product_attribute"]))
					{
						$sql_ref_shop ="SELECT id_shop
						FROM "._DB_PREFIX_."product_attribute_shop
						WHERE id_product_attribute = '".(int)$id."'
							AND  id_shop != '".(int)$id_shop."'
							AND id_shop!=0 AND id_shop IS NOT NULL AND id_shop!=''
						LIMIT 1";
						$ref_shop = Db::getInstance()->ExecuteS($sql_ref_shop);
						if(!empty($ref_shop[0]["id_shop"]))
						{
							$new = new Combination($id, null, (int)$ref_shop[0]["id_shop"]);
							$new->id_shop_list = array($id_shop);
							$new->save();
						}
					}
				}
				elseif(empty($value))
				{
					$sql_in_shop ="SELECT id_product_attribute
						FROM "._DB_PREFIX_."product_attribute_shop
						WHERE id_product_attribute = '".(int)$id."'
							AND  id_shop = '".(int)$id_shop."'";
					$in_shop = Db::getInstance()->ExecuteS($sql_in_shop);
					if(!empty($in_shop[0]["id_product_attribute"]) /*&& $product->id_shop_default!=$id_shop*/)
					{
						$sql = 'DELETE FROM `'._DB_PREFIX_.'product_attribute_shop`
						WHERE id_product_attribute = "'.(int)$id.'" AND  id_shop = "'.(int)$id_shop.'"';
						Db::getInstance()->execute($sql);
					}
				}
			}
		break;
		// Modification de present 
		// pour un ou plusieurs shops passés en params
		// pour un ou plusieurs products passés en params
		case 'mass_present':
			if($value=="true")
				$value = 1;
			else
				$value = 0;
			
			$shops  = explode(",", $id_shop);
			foreach($shops as $id_shop)
			{
				foreach($ids as $id)
				{
					if($value=="1")
					{
						$sql_in_shop ="SELECT id_product_attribute
							FROM "._DB_PREFIX_."product_attribute_shop
							WHERE id_product_attribute = '".(int)$id."'
								AND  id_shop = '".(int)$id_shop."'";
						$in_shop = Db::getInstance()->ExecuteS($sql_in_shop);
						if(empty($in_shop[0]["id_product_attribute"]))
						{
							$new = new Combination($id, null, $product->id_shop_default);
							$new->id_shop_list = array($id_shop);
							$new->save();
						}
					}
					elseif(empty($value))
					{
						$sql_in_shop ="SELECT id_product_attribute
							FROM "._DB_PREFIX_."product_attribute_shop
							WHERE id_product_attribute = '".(int)$id."'
								AND  id_shop = '".(int)$id_shop."'";
						$in_shop = Db::getInstance()->ExecuteS($sql_in_shop);
						if(!empty($in_shop[0]["id_product_attribute"]) /*&& $product->id_shop_default!=$id_shop*/)
						{
							$sql = 'DELETE FROM `'._DB_PREFIX_.'product_attribute_shop`
							WHERE id_product_attribute = "'.(int)$id.'" AND  id_shop = "'.(int)$id_shop.'"';
							Db::getInstance()->execute($sql);
						}
					}
				}
			}
		break;
	}

	if(!empty($id_product))
		ExtensionPMCM::clearFromIdsProduct($id_product);
}