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
$id_warehouse=Tools::getValue('id_warehouse','0');
$id_actual_warehouse = SCI::getSelectedWarehouse();
$value=Tools::getValue('value','0');

$CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE = _s('CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE');

$multiple = false;
if(strpos($idlist, ",") !== false)
	$multiple = true;

$ids = explode(",", $idlist);

if($action!='' && !empty($id_warehouse) && !empty($idlist)/* && !empty($id_actual_warehouse)*/)
{
	switch($action)
	{
		// Modification de location pour le warehouse passé en params
		// pour un ou plusieurs products passés en params
		case 'location':
			foreach($ids as $id)
			{
				if(!empty($value))
				{
					$check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$id, 0, (int)$id_warehouse);
					if(empty($check_in_warehouse))
					{
						$new = new WarehouseProductLocation();
						$new->id_product = (int)$id;
						$new->id_product_attribute = 0;
						$new->id_warehouse = (int)$id_warehouse;
						$new->location = $value;
						$new->save();
					}
					else
					{
						$new = new WarehouseProductLocation($check_in_warehouse);
						$new->location = $value;
						$new->save();
					}
					
					$advanced_stock_management = SCI::usesAdvancedStockManagement((int)$id);
					
					if($CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE==1 && !$advanced_stock_management) // enabled
					{
						$shops = SCI::getSelectedShopActionList(false, intval($id));
						foreach ($shops as $shop)
						{
							StockAvailable::setProductDependsOnStock(intval($id), true, $shop);
						
							$sql = 'UPDATE `'._DB_PREFIX_.'product_shop`
									SET advanced_stock_management = "1"
									WHERE id_product = "'.(int)$id.'"
										AND id_shop = "'.(int)$shop.'"';
							Db::getInstance()->execute($sql);
						}
					}
					elseif($CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE==2 && !$advanced_stock_management) // enabled + manual
					{
						$shops = SCI::getSelectedShopActionList(false, intval($id));
						foreach ($shops as $shop)
						{
							StockAvailable::setProductDependsOnStock(intval($id), false, $shop);
						
							$sql = 'UPDATE `'._DB_PREFIX_.'product_shop`
									SET advanced_stock_management = "1"
									WHERE id_product = "'.(int)$id.'"
										AND id_shop = "'.(int)$shop.'"';
							Db::getInstance()->execute($sql);
						}
					}
				}
			}
		break;
		// Modification de present pour le warehouse passé en params
		// pour un ou plusieurs products passés en params
		case 'present':
			if($value=="true")
				$value = 1;
			else
				$value = 0;
			
			foreach($ids as $id)
			{
				if($value=="1")
				{
					$check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$id, 0, (int)$id_warehouse);
					if(empty($check_in_warehouse))
					{
						$new = new WarehouseProductLocation();
						$new->id_product = (int)$id;
						$new->id_product_attribute = 0;
						$new->id_warehouse = (int)$id_warehouse;
						$new->save();
					
						SCI::synchronize($id);
					}
					
					$advanced_stock_management = SCI::usesAdvancedStockManagement((int)$id);
					
					if($CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE==1 && !$advanced_stock_management) // enabled
					{
						$shops = SCI::getSelectedShopActionList(false, intval($id));
						foreach ($shops as $shop)
							StockAvailable::setProductDependsOnStock(intval($id), true, $shop);
						
						$sql = 'UPDATE `'._DB_PREFIX_.'product_shop`
								SET advanced_stock_management = "1"
								WHERE id_product = "'.(int)$id.'"
									AND id_shop = "'.(int)SCI::getSelectedShop().'"';
						Db::getInstance()->execute($sql);
					}
					elseif($CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE==2 && !$advanced_stock_management) // enabled + manual
					{
						$shops = SCI::getSelectedShopActionList(false, intval($id));
						foreach ($shops as $shop)
							StockAvailable::setProductDependsOnStock(intval($id), false, $shop);
						
						$sql = 'UPDATE `'._DB_PREFIX_.'product_shop`
								SET advanced_stock_management = "1"
								WHERE id_product = "'.(int)$id.'"
									AND id_shop = "'.(int)SCI::getSelectedShop().'"';
						Db::getInstance()->execute($sql);
					}
				}
				elseif(empty($value))
				{
					$check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$id, 0, (int)$id_warehouse);
					if(!empty($check_in_warehouse))
					{
						$sql = 'DELETE FROM `'._DB_PREFIX_.'warehouse_product_location`
						WHERE id_warehouse_product_location = "'.(int)$check_in_warehouse.'"';
						Db::getInstance()->execute($sql);
					}
					
					SCI::synchronize($id);
				}
			}
		break;
		// Modification de present 
		// pour un ou plusieurs warehouses passés en params
		// pour un ou plusieurs products passés en params
		case 'mass_present':
			if($value=="true")
				$value = 1;
			else
				$value = 0;
			
			$warehouses  = explode(",", $id_warehouse);
			foreach($warehouses as $id_warehouse)
			{
				foreach($ids as $id)
				{
					if($value=="1")
					{
						$check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$id, 0, (int)$id_warehouse);
						if(empty($check_in_warehouse))
						{
							$new = new WarehouseProductLocation();
							$new->id_product = (int)$id;
							$new->id_product_attribute = 0;
							$new->id_warehouse = (int)$id_warehouse;
							$new->save();
					
							SCI::synchronize($id);
						}
					
						$advanced_stock_management = SCI::usesAdvancedStockManagement((int)$id);
						
						if($CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE==1 && !$advanced_stock_management) // enabled
						{
							$shops = SCI::getSelectedShopActionList(false, intval($id));
							foreach ($shops as $shop)
								StockAvailable::setProductDependsOnStock(intval($id), true, $shop);
							
							$sql = 'UPDATE `'._DB_PREFIX_.'product_shop`
									SET advanced_stock_management = "1"
									WHERE id_product = "'.(int)$id.'"
										AND id_shop = "'.(int)SCI::getSelectedShop().'"';
							Db::getInstance()->execute($sql);
						}
						elseif($CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE==2 && !$advanced_stock_management) // enabled + manual
						{
							$shops = SCI::getSelectedShopActionList(false, intval($id));
							foreach ($shops as $shop)
								StockAvailable::setProductDependsOnStock(intval($id), false, $shop);
							
							$sql = 'UPDATE `'._DB_PREFIX_.'product_shop`
									SET advanced_stock_management = "1"
									WHERE id_product = "'.(int)$id.'"
										AND id_shop = "'.(int)SCI::getSelectedShop().'"';
							Db::getInstance()->execute($sql);
						}
					}
					elseif(empty($value))
					{
						$check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$id, 0, (int)$id_warehouse);
						if(!empty($check_in_warehouse))
						{
							$sql = 'DELETE FROM `'._DB_PREFIX_.'warehouse_product_location`
							WHERE id_warehouse_product_location = "'.(int)$check_in_warehouse.'"';
							Db::getInstance()->execute($sql);
					
							SCI::synchronize($id);
						}
					}
				}
			}
		break;
	}

	// PM Cache
	if(!empty($ids))
		ExtensionPMCM::clearFromIdsProduct($ids);
}