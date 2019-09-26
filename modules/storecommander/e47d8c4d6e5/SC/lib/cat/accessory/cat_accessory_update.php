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
	$id_accessory=Tools::getValue('id_accessory','0');
	$id_category=Tools::getValue('id_category','0');
	
if($action!='')
{
	switch($action){
		case 'delete':
			$sql = "DELETE FROM `"._DB_PREFIX_."accessory` WHERE `id_product_2` IN (".psql($id_accessory).")";	
			Db::getInstance()->Execute($sql);
		break;
		case '1':
			$sql = "DELETE FROM `"._DB_PREFIX_."accessory` WHERE `id_product_2` IN (".psql($id_accessory).") AND `id_product_1` IN (".psql($idlist).")";
			Db::getInstance()->Execute($sql);
			$id_product_acc=explode(',',$id_accessory);
			$id_product_src=explode(',',$idlist);
			$sql='';
			foreach($id_product_acc AS $acc)
			{
				foreach($id_product_src AS $src)
				{
					if ($acc!=$src && $src!=0 && $acc!=0)
						$sql.='('.$src.','.$acc.'),';
				}
			}
			if ($sql!='')
			{
				$sql = trim($sql,',');
				$sql = "INSERT INTO `"._DB_PREFIX_."accessory` (id_product_1,id_product_2) VALUES ".psql($sql);
				Db::getInstance()->Execute($sql);
			}
		break;
		case '0':
			$sql = "DELETE FROM `"._DB_PREFIX_."accessory` WHERE `id_product_2` IN (".psql($id_accessory).") AND `id_product_1` IN (".psql($idlist).")";
			Db::getInstance()->Execute($sql);
		break;
		case 'addSel':
			$sql = "DELETE FROM `"._DB_PREFIX_."accessory` WHERE `id_product_2` = 0 OR `id_product_1` = 0";
			Db::getInstance()->Execute($sql);
			$sql = "DELETE FROM `"._DB_PREFIX_."accessory` WHERE `id_product_2` IN (".psql($id_accessory).") AND `id_product_1` IN (".psql($idlist).")";
			Db::getInstance()->Execute($sql);
			$id_product_acc=explode(',',$id_accessory);
			$id_product_src=explode(',',$idlist);
			$sql='';
			foreach($id_product_acc AS $acc)
			{
				foreach($id_product_src AS $src)
				{
					if ($acc!=$src && $src!=0 && $acc!=0)
						$sql.='('.$src.','.$acc.'),';
				}
			}
			if ($sql!='')
			{
				$sql = trim($sql,',');
				$sql = "INSERT INTO `"._DB_PREFIX_."accessory` (id_product_1,id_product_2) VALUES ".psql($sql);
				Db::getInstance()->Execute($sql);
			}
		break;
		case 'delSel':
			$sql = "DELETE FROM `"._DB_PREFIX_."accessory` WHERE `id_product_2` IN (".psql($id_accessory).") AND `id_product_1` IN (".psql($idlist).")";
			Db::getInstance()->Execute($sql);
		break;
		case 'active_accessory':
			if(!empty($id_accessory))
			{
				$value=Tools::getValue('value','0');
				if(SCMS && SCI::getSelectedShop()>0)
					$product = new Product((int)$id_accessory, false, null, (int)SCI::getSelectedShop());
				elseif(SCMS)
					$product = new Product((int)$id_accessory, false);
				else
					$product = new Product((int)$id_accessory);
				
				$product->active = (int)$value;
				$product->save();
			}
		break;
	}
}