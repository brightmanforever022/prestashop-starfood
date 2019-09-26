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

if($value=="true")
	$value = 1;
else
	$value = 0;

$multiple = false;
if(strpos($idlist, ",") !== false)
	$multiple = true;

$ids = explode(",", $idlist);

$noRefreshCategory = false;

if($action!='' && !empty($id_shop) && !empty($idlist))
{
	switch($action)
	{
		// Modification de present pour le shop passé en params
		// pour un ou plusieurs categories passés en params
		case 'present':
			foreach($ids as $id)
			{
				$sql2 ="SELECT id_shop_default
					FROM "._DB_PREFIX_."category
					WHERE id_category = '".psql($id)."'";
				$res2 = Db::getInstance()->getRow($sql2);
				if(!empty($res2["id_shop_default"]))
				{
					$category = new Category($id, null, $res2["id_shop_default"]);
					
					if(!$category->isAssociatedToShop($id_shop) && $value=="1")
					{
						$category->id_shop_list=array($id_shop);
						$category->save();
					}
					elseif($category->isAssociatedToShop($id_shop) && empty($value))
					{
						if($id_shop != $category->id_shop_default)
						{
							$sql2 ="DELETE FROM "._DB_PREFIX_."category_shop
							WHERE id_category = '".intval($id)."' AND id_shop = '".intval($id_shop)."'";
							Db::getInstance()->execute($sql2);
							$sql2 ="DELETE FROM "._DB_PREFIX_."category_lang
							WHERE id_category = '".intval($id)."' AND id_shop = '".intval($id_shop)."'";
							Db::getInstance()->execute($sql2);
						}
					}
				}
			}
		break;
		// Modification la boutique par défaut
		// pour un ou plusieurs categories passés en params
		case 'default':
			foreach($ids as $id)
			{
				$sql2 ="SELECT id_shop_default
					FROM "._DB_PREFIX_."category
					WHERE id_category = '".psql($id)."'";
				$res2 = Db::getInstance()->getRow($sql2);
				if(!empty($res2["id_shop_default"]))
				{
					$category = new Category($id, null, $res2["id_shop_default"]);
					
					if(!$category->isAssociatedToShop($id_shop))
					{
						$category->id_shop_list=array($id_shop);
					}
					
					$category->id_shop_default=$id_shop;
					$category->save();
				}
			}
		break;
		// Modification de present 
		// pour un ou plusieurs shops passés en params
		// pour un ou plusieurs categories passés en params
		case 'mass_present':
			$shops  = explode(",", $id_shop);
			foreach($shops as $shop)
			{
				foreach($ids as $id)
				{
					$category = new Category($id);
					if(!$category->isAssociatedToShop($shop) && $value=="1")
					{
						$category->id_shop_list=array($shop);
						$category->save();
					
					}
					elseif($category->isAssociatedToShop($shop) && empty($value))
					{
						if($shop != $category->id_shop_default)
						{
							$sql2 ="DELETE FROM "._DB_PREFIX_."category_shop
							WHERE id_category = '".intval($id)."' AND id_shop = '".intval($shop)."'";
							Db::getInstance()->execute($sql2);
							$sql2 ="DELETE FROM "._DB_PREFIX_."category_lang
							WHERE id_category = '".intval($id)."' AND id_shop = '".intval($shop)."'";
							Db::getInstance()->execute($sql2);
						}
					}
				}
			}
		break;
	}

	// PM Cache
	if(!empty($idlist))
		ExtensionPMCM::clearFromIdsCategory($idlist);
}

if($noRefreshCategory)
	echo "noRefreshCategory";