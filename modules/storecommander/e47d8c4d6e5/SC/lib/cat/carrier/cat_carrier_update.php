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

$idlist=Tools::getValue('idlist',0);
$id_lang=intval(Tools::getValue('id_lang'));
$id_carrier=(Tools::getValue('id_carrier',0));
$value=(Tools::getValue('value'));
$action=Tools::getValue('action','');

if($value=="true")
	$value = 1;
else
	$value = 0;

$multiple = false;
if(strpos($idlist, ",") !== false)
	$multiple = true;
$products=(explode(',',$idlist));

if(!empty($idlist) && !empty($id_carrier))
{
	if(empty($action))
	{
		if($value == 1)
		{
			if(SCMS)
			{
				$shops = SCI::getSelectedShopActionList();
				foreach($shops as $shop_id)
				{
					foreach($products as $id_product)
					{
						$sql = 'SELECT *
								FROM `'._DB_PREFIX_.'product_carrier`
								WHERE `id_carrier_reference` = "'.(int)$id_carrier.'"
									AND `id_product` = '.(int)$id_product.'
									AND id_shop = "'.(int)$shop_id.'"';
						$tmp_used = Db::getInstance()->executeS($sql);
						if(empty($tmp_used))
						{
							$sql = 'INSERT INTO `'._DB_PREFIX_.'product_carrier` (id_product, id_carrier_reference, id_shop)
									VALUES ("'.(int)$id_product.'","'.(int)$id_carrier.'","'.(int)$shop_id.'")';
							Db::getInstance()->execute($sql);
						}
					}
				}
			}
			else
			{
				foreach($products as $id_product)
				{
					$sql = 'SELECT *
							FROM `'._DB_PREFIX_.'product_carrier`
							WHERE `id_carrier_reference` = "'.(int)$id_carrier.'"
								AND `id_product` = '.(int)$id_product.'';
					$tmp_used = Db::getInstance()->executeS($sql);
					if(empty($tmp_used))
					{
						$sql = 'INSERT INTO `'._DB_PREFIX_.'product_carrier` (id_product, id_carrier_reference, id_shop)
								VALUES ("'.(int)$id_product.'","'.(int)$id_carrier.'","1")';
						Db::getInstance()->execute($sql);
					}
				}
			}
		}
		else
		{
			if(SCMS)
			{
				$shops = SCI::getSelectedShopActionList();
				foreach($shops as $shop_id)
				{
					foreach($products as $id_product)
					{
						$sql = 'DELETE FROM `'._DB_PREFIX_.'product_carrier`
								WHERE `id_carrier_reference` = "'.(int)$id_carrier.'"
									AND `id_product` = '.(int)$id_product.'
									AND id_shop = "'.(int)$shop_id.'"';
						Db::getInstance()->execute($sql);
					}
				}
			}
			else
			{
				foreach($products as $id_product)
				{
					$sql = 'DELETE FROM `'._DB_PREFIX_.'product_carrier`
						WHERE `id_carrier_reference` = "'.(int)$id_carrier.'"
						AND `id_product` = '.(int)$id_product.'';
					Db::getInstance()->execute($sql);
				}
			}
		}
	}
	elseif($action=="mass_add")
	{
		$carriers=(explode(',',$id_carrier));
		$shops = SCI::getSelectedShopActionList();
		foreach($carriers as $id_carrier)
		{
			if(SCMS)
			{
				foreach($shops as $shop_id)
				{
					foreach($products as $id_product)
					{
						$sql = 'SELECT *
									FROM `'._DB_PREFIX_.'product_carrier`
									WHERE `id_carrier_reference` = "'.(int)$id_carrier.'"
										AND `id_product` = '.(int)$id_product.'
										AND id_shop = "'.(int)$shop_id.'';
						$tmp_used = Db::getInstance()->executeS($sql);
						if(empty($tmp_used))
						{
							$sql = 'INSERT INTO `'._DB_PREFIX_.'product_carrier` (id_product, id_carrier_reference, id_shop)
										VALUES ("'.(int)$id_product.'","'.(int)$id_carrier.'","'.(int)$shop_id.'")';
							Db::getInstance()->execute($sql);
						}
					}
				}
			}
			else
			{
				foreach($products as $id_product)
				{
					$sql = 'SELECT *
								FROM `'._DB_PREFIX_.'product_carrier`
								WHERE `id_carrier_reference` = "'.(int)$id_carrier.'"
									AND `id_product` = '.(int)$id_product.'';
					$tmp_used = Db::getInstance()->executeS($sql);
					if(empty($tmp_used))
					{
						$sql = 'INSERT INTO `'._DB_PREFIX_.'product_carrier` (id_product, id_carrier_reference, id_shop)
									VALUES ("'.(int)$id_product.'","'.(int)$id_carrier.'","1")';
						Db::getInstance()->execute($sql);
					}
				}
			}
		}
	}
	elseif($action=="mass_delete")
	{
		$carriers=(explode(',',$id_carrier));
		$shops = SCI::getSelectedShopActionList();
		foreach($carriers as $id_carrier)
		{
			if(SCMS)
			{
				$shops = SCI::getSelectedShopActionList();
				foreach($shops as $shop_id)
				{
					foreach($products as $id_product)
					{
						$sql = 'DELETE FROM `'._DB_PREFIX_.'product_carrier`
								WHERE `id_carrier_reference` = "'.(int)$id_carrier.'"
									AND `id_product` = '.(int)$id_product.'
									AND id_shop = "'.(int)$shop_id.'"';
						Db::getInstance()->execute($sql);
					}
				}
			}
			else
			{
				foreach($products as $id_product)
				{
					$sql = 'DELETE FROM `'._DB_PREFIX_.'product_carrier`
						WHERE `id_carrier_reference` = "'.(int)$id_carrier.'"
						AND `id_product` = '.(int)$id_product.'';
					Db::getInstance()->execute($sql);
				}
			}
		}
	}
}