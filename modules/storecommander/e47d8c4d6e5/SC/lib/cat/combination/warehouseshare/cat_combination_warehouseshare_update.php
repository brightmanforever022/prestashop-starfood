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
					$combination = new Combination($id);
					$check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$combination->id_product, (int)$id, (int)$id_warehouse);
					if(empty($check_in_warehouse))
					{
						$new = new WarehouseProductLocation();
						$new->id_product = (int)$combination->id_product;
						$new->id_product_attribute = $id;
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
				}
			}
		break;
		// Modification de present pour le warehouse passé en params
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
					$combination = new Combination($id);
					$check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$combination->id_product, (int)$id, (int)$id_warehouse);
					if(empty($check_in_warehouse))
					{
						$new = new WarehouseProductLocation();
						$new->id_product = (int)$combination->id_product;
						$new->id_product_attribute = $id;
						$new->id_warehouse = (int)$id_warehouse;
						$new->save();
					}
				}
				elseif(empty($value))
				{
					$combination = new Combination($id);
					$check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$combination->id_product, (int)$id, (int)$id_warehouse);
					if(!empty($check_in_warehouse))
					{
						$sql = 'DELETE FROM `'._DB_PREFIX_.'warehouse_product_location`
						WHERE id_warehouse_product_location = "'.(int)$check_in_warehouse.'"';
						Db::getInstance()->execute($sql);
					}
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
						$combination = new Combination($id);
						$check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$combination->id_product, (int)$id, (int)$id_warehouse);
						if(empty($check_in_warehouse))
						{
							$new = new WarehouseProductLocation();
							$new->id_product = (int)$combination->id_product;
							$new->id_product_attribute = $id;
							$new->id_warehouse = (int)$id_warehouse;
							$new->save();
						}
					}
					elseif(empty($value))
					{
						$combination = new Combination($id);
						$check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$combination->id_product, (int)$id, (int)$id_warehouse);
						if(!empty($check_in_warehouse))
						{
							$sql = 'DELETE FROM `'._DB_PREFIX_.'warehouse_product_location`
							WHERE id_warehouse_product_location = "'.(int)$check_in_warehouse.'"';
							Db::getInstance()->execute($sql);
						}
					}
				}
			}
		break;
	}

	if(!empty($ids[0]))
		$id_product = SCI::getIdPdtFromCombi((int)$ids[0]);
	if(!empty($id_product))
		ExtensionPMCM::clearFromIdsProduct($id_product);
}