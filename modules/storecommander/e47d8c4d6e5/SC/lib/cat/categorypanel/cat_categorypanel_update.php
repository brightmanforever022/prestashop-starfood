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
	$id_category=Tools::getValue('id_category','0');
	
	$reloadCat = false;
	
if($action!='')
{
	switch($action){
		case '1':
			$sql = "DELETE FROM "._DB_PREFIX_."category_product WHERE id_product IN (".psql($idlist).") AND id_category = ".intval($id_category)."";
			Db::getInstance()->Execute($sql);
			$sql = "SELECT MAX(position) AS max FROM "._DB_PREFIX_."category_product WHERE id_category=".intval($id_category);
			$res=Db::getInstance()->getRow($sql);
			$max=intval($res['max']);
			$id_product_src=explode(',',Tools::getValue('idlist','0'));
			$sql='';
			foreach($id_product_src AS $src)
			{
					$max++;
					$sql.='('.$src.','.intval($id_category).','.$max.'),';
			}
			$sql = trim($sql,',');
			if ($sql!='')
			{
				if(SCMS)
				{
					$id_shop = SCI::getSelectedShop();
					$category = new Category($id_category);
					if(!$category->existsInShop($id_shop))
					{
						$category->addShop($id_shop);
						$reloadCat = true;
					}
				}
				$sql = "INSERT INTO `"._DB_PREFIX_."category_product` (id_product,id_category,position) VALUES ".psql($sql);
				Db::getInstance()->Execute($sql);
				$sql = "UPDATE `"._DB_PREFIX_."product` SET date_upd=NOW() WHERE id_product IN (".psql($idlist).")";
				Db::getInstance()->Execute($sql);
				if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					$sql = "UPDATE "._DB_PREFIX_."product_shop SET date_upd=NOW(),indexed=0 WHERE id_product IN (".psql($idlist).") AND id_shop=".(int)SCI::getSelectedShop();
				if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
				{
					$ids=explode(',',$idlist);
					foreach($ids AS $idproduct)
					{
						$product=new Product(intval($idproduct));
						SCI::hookExec('updateProduct', array('product' => $product));
					}
				}elseif(_s('APP_COMPAT_EBAY')){
					$ids=explode(',',$idlist);
					sort($ids);
					Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'),intval($ids[0])));
				}
			}
			break;
		case '0':
			$sql = "DELETE FROM `"._DB_PREFIX_."category_product` WHERE `id_product` IN (".psql($idlist).") AND `id_category` = ".intval($id_category)."";
			Db::getInstance()->Execute($sql);
			$sql = "UPDATE `"._DB_PREFIX_."product` SET date_upd=NOW() WHERE id_product IN (".psql($idlist).")";
			Db::getInstance()->Execute($sql);
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				$sql = "UPDATE "._DB_PREFIX_."product_shop SET date_upd=NOW(),indexed=0 WHERE id_product IN (".psql($idlist).") AND id_shop=".(int)SCI::getSelectedShop();
			if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
			{
				$ids=explode(',',$idlist);
				foreach($ids AS $idproduct)
				{
					$product=new Product(intval($idproduct));
					SCI::hookExec('updateProduct', array('product' => $product));
				}
			}elseif(_s('APP_COMPAT_EBAY')){
				$ids=explode(',',$idlist);
				sort($ids);
				Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'),intval($ids[0])));
			}
			break;
		case 'default1':
			$sql = "UPDATE `"._DB_PREFIX_."product` SET date_upd=NOW(),id_category_default=".(int)$id_category." WHERE id_product IN (".psql($idlist).")";
			Db::getInstance()->Execute($sql);
			$ids=explode(',',$idlist);
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				$idshops=SCI::getSelectedShopActionList();
				foreach($idshops AS $id_shop)
				{
					$sql = "UPDATE "._DB_PREFIX_."product_shop SET date_upd=NOW(),id_category_default=".(int)$id_category." WHERE id_product IN (".psql($idlist).") AND id_shop = ".(int)$id_shop."";
					Db::getInstance()->Execute($sql);
					
					foreach($ids AS $idproduct)
					{
						$product=new Product(intval($idproduct),false,null,(int)$id_shop);
						$product->setGroupReduction();
					}
				}
			}
			else if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
			{
				foreach($ids AS $idproduct)
				{
					$product=new Product(intval($idproduct),false);
					$product->setGroupReduction();
				}
			}
			if (!_s('CAT_PROD_CAT_DEF_EXT'))
			{
				$sql = "SELECT GROUP_CONCAT(id_product) FROM `"._DB_PREFIX_."category_product` WHERE id_category=".intval($id_category);
				$plist=_qgv($sql);
				$sql = "SELECT MAX(position) AS max FROM "._DB_PREFIX_."category_product WHERE id_category=".intval($id_category);
				$res=Db::getInstance()->getRow($sql);
				$max=intval($res['max']);
				$id_product_src=explode(',',Tools::getValue('idlist','0'));
				$id_product_alreadyin=explode(',',$plist);
				$id_product_src=array_diff($id_product_src,$id_product_alreadyin);
				$sql='';
				foreach($id_product_src AS $src)
				{
						if ($src!=0 && $id_category!=0)
						{
							$max++;
							$sql.='('.$src.','.intval($id_category).','.$max.'),';
						}
				}
				if ($sql!='')
				{
					$sql = trim($sql,',');
					$sql = "INSERT INTO `"._DB_PREFIX_."category_product` (id_product,id_category,position) VALUES ".$sql;
					Db::getInstance()->Execute($sql);
				}
			}
			if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
			{
				$ids=explode(',',$idlist);
				foreach($ids AS $idproduct)
				{
					$product=new Product(intval($idproduct));
					SCI::hookExec('updateProduct', array('product' => $product));
				}
			}elseif(_s('APP_COMPAT_EBAY')){
				$ids=explode(',',$idlist);
				sort($ids);
				Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'),intval($ids[0])));
			}
			break;
		case 'multi_add':
			$idprod=Tools::getValue('idprod','0');
			$idcateg=Tools::getValue('idcateg','0');
			if ($idprod!='0' && $idcateg!='0')
			{
				$sql="SELECT distinct id_product,id_category FROM "._DB_PREFIX_."category_product WHERE id_product IN (".psql($idprod).") AND id_category IN (".psql($idcateg).")";
				$res=Db::getInstance()->ExecuteS($sql);
				$alreadylinked=array();
				foreach($res AS $row)
					$alreadylinked[]=$row['id_category'].','.$row['id_product'];
				$sqlinsert='';
				$posarray=array();
				$listprod=explode(',',$idprod);
				$listcateg=explode(',',$idcateg);
				$id_shop = SCI::getSelectedShop();
				foreach($listcateg AS $idc)
				{
					if (!sc_array_key_exists($idc,$posarray))
					{
						$sql = "SELECT MAX(position) AS max FROM "._DB_PREFIX_."category_product WHERE id_category=".intval($idc);
						$res=Db::getInstance()->getRow($sql);
						$posarray[$idc]=intval($res['max']);
					}
					foreach($listprod AS $idp)
					{
						if (!sc_in_array($idc.','.$idp,$alreadylinked,"catCategorypanelUpdate_alreadylinked"))
						{
							if ($idp!=0 && $idc!=0)
							{
								$posarray[$idc]++;
								$sqlinsert.='('.intval($idp).','.intval($idc).','.intval($posarray[$idc]).'),';
							}
						}
					}
					
					if(SCMS)
					{
						$category = new Category(intval($idc));
						if(!$category->existsInShop($id_shop))
						{
							$category->addShop($id_shop);
							$reloadCat = true;
						}
					}
				}
				$sqlinsert=trim($sqlinsert,',');
				if ($sqlinsert!='')
				{
					$sql = "INSERT INTO `"._DB_PREFIX_."category_product` (id_product,id_category,position) VALUES ".psql($sqlinsert);
					Db::getInstance()->Execute($sql);
				}
				if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
				{
					$ids=explode(',',$idprod);
					foreach($ids AS $idproduct)
					{
						$product=new Product(intval($idproduct));
						SCI::hookExec('updateProduct', array('product' => $product));
					}
				}elseif(_s('APP_COMPAT_EBAY')){
					$ids=explode(',',$idprod);
					sort($ids);
					Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'),intval($ids[0])));
				}
			}
			break;
		case 'multi_del':
			$idprod=Tools::getValue('idprod','0');
			$idcateg=Tools::getValue('idcateg','0');
			if ($idprod!='0' && $idcateg!='0')
			{
				$sql="SELECT id_product,id_category_default FROM "._DB_PREFIX_."product WHERE id_product IN (".psql($idprod).")";
				$res=Db::getInstance()->ExecuteS($sql);
				$defcateg=array();
				foreach($res AS $row)
					$defcateg[]=$row['id_category_default'].','.$row['id_product'];
				$sqldelete=array();
				$listprod=explode(',',$idprod);
				$listcateg=explode(',',$idcateg);
				foreach($listcateg AS $idc)
				{
					foreach($listprod AS $idp)
					{
						if (!sc_in_array($idc.','.$idp,$defcateg,"catCategorypanelUpdate_defcateg"))
						{
							$sqldelete[]="id_product=".intval($idp)." AND id_category=".intval($idc);
						}
					}
				}
				if (count($sqldelete))
				{
					foreach($sqldelete AS $sqldel)
					{
						$sql = "DELETE FROM `"._DB_PREFIX_."category_product` WHERE ".psql($sqldel);
						Db::getInstance()->Execute($sql);
					}
				}
				if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
				{
					$ids=explode(',',$idprod);
					foreach($ids AS $idproduct)
					{
						$product=new Product(intval($idproduct));
						SCI::hookExec('updateProduct', array('product' => $product));
					}
				}elseif(_s('APP_COMPAT_EBAY')){
					$ids=explode(',',$idprod);
					sort($ids);
					Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'),intval($ids[0])));
				}
			}
			break;
	}
}

if($reloadCat)
	echo "reload_cat";	