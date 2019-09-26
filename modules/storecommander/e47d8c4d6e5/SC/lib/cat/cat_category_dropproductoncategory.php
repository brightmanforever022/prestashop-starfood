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

	$id_lang=intval(Tools::getValue('id_lang'));
	$mode=Tools::getValue('mode');
	$displayProductsFrom=Tools::getValue('displayProductsFrom','all');
	$id_categoryTarget=Tools::getValue('categoryTarget');
	$id_categorySource=Tools::getValue('categorySource');
	$droppedProducts=Tools::getValue('products');
	$products=explode(',',$droppedProducts);

	$sql = "SELECT MAX(position) AS max FROM "._DB_PREFIX_."category_product WHERE id_category=".intval($id_categoryTarget);
	$res=Db::getInstance()->getRow($sql);
	$max=$res['max'];
	
	$sql = "SELECT * FROM "._DB_PREFIX_."category_product WHERE id_category=".intval($id_categoryTarget);
	$res=Db::getInstance()->ExecuteS($sql);
	$plist=array();
	foreach($res AS $row)
	{
		$plist[]=$row['id_product'];
	}
	if ($mode=='copy')
	{
		foreach($products AS $id_product){
			if($displayProductsFrom=='default')
			{
				$sql = "SELECT id_category_default FROM "._DB_PREFIX_."product WHERE id_product=".intval($id_product);
				$res=Db::getInstance()->getRow($sql);
				if ($res['id_category_default']==$id_categorySource)
				{
					$sql = "UPDATE "._DB_PREFIX_."product SET date_upd=NOW(),id_category_default=".intval($id_categoryTarget)." WHERE id_product=".intval($id_product);
					Db::getInstance()->Execute($sql);
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					{
						$sql = "UPDATE "._DB_PREFIX_."product_shop SET date_upd=NOW(),id_category_default=".intval($id_categoryTarget)." WHERE id_product=".intval($id_product)." AND id_shop IN (".SCI::getSelectedShopActionList(true).")";
						Db::getInstance()->Execute($sql);
					}
				}
			}
			if (!sc_in_array($id_product,$plist,"catDropproductoncategory_plist"))
			{
				$max++;
				$sql = "INSERT INTO "._DB_PREFIX_."category_product (id_category,id_product,position) VALUES(".intval($id_categoryTarget).",".intval($id_product).",".intval($max).")";
				Db::getInstance()->Execute($sql);
				$plist[]=$id_product;
				addToHistory('catalog_tree','relation_add','id_product',intval($id_product),$id_lang,_DB_PREFIX_."category_product",'Parent added:'.intval($id_categoryTarget));
			}
			if(SCMS)
			{
				$cat_shops = SCI::getShopsByCategory(intval($id_categoryTarget));
				$product_shops = SCI::getShopsByProduct(intval($id_product));
				// si le produit est lié à au moins un boutique
				// on vérifie s'il y a une intersection
				$intersec = array_intersect($cat_shops, $product_shops);
				if(!empty($intersec) && count($intersec)>0)
				{}
				else
				{
					$checked_shops = SCI::getSelectedShopActionList();
					$intersec = array_intersect($cat_shops, $checked_shops);
					if(!empty($intersec) && count($intersec)>0)
					{
						$product = new Product($id_product);
						$product->id_shop_list = $intersec;
						$product->save();
					}
				}
			}
		}
	}else{
		$id_categorySources = explode(",",$id_categorySource);
		foreach($id_categorySources as $id_categorySource)
		{
			foreach($products AS $id_product){
				$sql = "SELECT id_category_default FROM "._DB_PREFIX_."product WHERE id_product=".intval($id_product);
				$res=Db::getInstance()->getRow($sql);
				if ($res['id_category_default']==$id_categorySource)
				{
					$sql = "UPDATE "._DB_PREFIX_."product SET date_upd=NOW(),id_category_default=".intval($id_categoryTarget)." WHERE id_product=".intval($id_product);
					Db::getInstance()->Execute($sql);
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					{
						$sql = "UPDATE "._DB_PREFIX_."product_shop SET date_upd=NOW(),id_category_default=".intval($id_categoryTarget)." WHERE id_product=".intval($id_product)." AND id_shop IN (".SCI::getSelectedShopActionList(true).")";
						Db::getInstance()->Execute($sql);
					}
				}
				if (intval($id_categorySource)!=intval($id_categoryTarget))
				{
					$sql = "DELETE FROM "._DB_PREFIX_."category_product WHERE id_product=".intval($id_product)." AND id_category=".intval($id_categorySource);
					Db::getInstance()->Execute($sql);
				}
	
				if (!sc_in_array($id_product,$plist,"catDropproductoncategory_plist"))
				{
					$max++;
					$sql = "INSERT INTO "._DB_PREFIX_."category_product (id_category,id_product,position) VALUES(".intval($id_categoryTarget).",".intval($id_product).",".intval($max).")";
					$plist[]=$id_product;
					addToHistory('catalog_tree','relation_move','id_product',intval($id_product),$id_lang,_DB_PREFIX_."category_product",'New parent:'.intval($id_categoryTarget));
					Db::getInstance()->Execute($sql);
				}
				if(SCMS)
				{
					$cat_shops = SCI::getShopsByCategory(intval($id_categoryTarget));
					$product_shops = SCI::getShopsByProduct(intval($id_product));
					// si le produit est lié à au moins un boutique
					// on vérifie s'il y a une intersection
					$intersec = array_intersect($cat_shops, $product_shops);
					if(!empty($intersec) && count($intersec)>0)
					{}
					else
					{
						$checked_shops = SCI::getSelectedShopActionList();
						$intersec = array_intersect($cat_shops, $checked_shops);
						if(!empty($intersec) && count($intersec)>0)
						{
							$product = new Product($id_product);
							$product->id_shop_list = $intersec;
							$product->save();
						}
					}
				}
			}
		}
	}
	
	if(_s("CAT_APPLY_ALL_CART_RULES"))
		SpecificPriceRule::applyAllRules($products);
