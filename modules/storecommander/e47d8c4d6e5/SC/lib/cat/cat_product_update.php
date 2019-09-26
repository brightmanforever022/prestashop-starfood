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
$debug=false;
	$doUpdateCombinationsOption=false;
	$newQuantity='';
	$extraVars='';
if ($debug) $dd='';

	if(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="inserted"){

		$action = "insert";

		$id_category=intval(Tools::getValue('id_category',1));
		$newprod=new Product();
		$newprod->id_category_default=$id_category;
		if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
		{
			$newprod->id_tax=0;
		}else{
			$newprod->id_tax_rules_group=0;
		}
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			$newprod->id_shop_list = SCI::getSelectedShopActionList();
		$newprod->active=_s('CAT_PROD_CREA_ACTIVE');
		$newQuantity=intval(_s('CAT_PROD_CREA_QTY'));
		if (_s('CAT_PROD_CREA_REF')!='')
			$newprod->reference=_s('CAT_PROD_CREA_REF');
		if (_s('CAT_PROD_CREA_SUPREF')!='')
			$newprod->supplier_reference=_s('CAT_PROD_CREA_SUPREF');
		$newprod->quantity=$newQuantity;
		foreach($languages AS $lang)
		{
			$newprod->link_rewrite[$lang['id_lang']]='product';
			$newprod->name[$lang['id_lang']]='new';
			$newprod->description_short[$lang['id_lang']]='';
			$newprod->description[$lang['id_lang']]='';
		}
		$newprod->save();
		$newId = $newprod->id;
		$sql="SELECT MAX(position) as maxpos FROM "._DB_PREFIX_."category_product WHERE id_category=".intval($id_category);
		$row=Db::getInstance()->getRow($sql);
		$sql="INSERT INTO "._DB_PREFIX_."category_product (id_category,id_product,position) VALUE (".intval($id_category).",".$newId.",".($row['maxpos']+1).")";
		$row=Db::getInstance()->Execute($sql);

		// Script pour empécher qu'un produit ai pour
		// catégorie par défaut, pour chaque boutique,
		// une catégorie non associée à la boutique
		if (SCMS)
		{
			$shops_cats = array();
			$shops_cats_tmp = Category::getShopsByCategory($id_category);
			foreach($shops_cats_tmp as $shops_cat_tmp)
				$shops_cats[] = $shops_cat_tmp["id_shop"];
			$shops = Shop::getShops(false);
			foreach ($shops as $shop)
			{
				if(!sc_in_array($shop["id_shop"], $shops_cats,"catProductUpdate_shops_cats"))
				{
					$sql = "UPDATE "._DB_PREFIX_."product_shop SET id_category_default='".psql($shop["id_category"])."' 
							WHERE id_product=".intval($newId)." AND id_shop=".intval($shop["id_shop"])."";
					Db::getInstance()->Execute($sql);
				}
				else
				{
					$p = new Product($newId, false, null, $shop["id_shop"]);
					if(empty($p->id_category_default))
					{
						$sql = "UPDATE "._DB_PREFIX_."product_shop SET id_category_default='".psql($shop["id_category"])."'
							WHERE id_product=".intval($newId)." AND id_shop=".intval($shop["id_shop"])."";
						Db::getInstance()->Execute($sql);
					}
					
				}
			}
		}
		
		if(SCAS)
		{
			$type_default = _s("CAT_ADVANCEDSTOCK_DEFAULT");
			
			if($type_default==2) // enabled
			{
				$id_selected_warehouse = SCI::getSelectedWarehouse();
				if(!empty($id_selected_warehouse))
				{
					$stock_manager = StockManagerFactory::getManager();
			
					// ADD IN WAREHOUSE
					$wpl = new WarehouseProductLocation();
					$wpl->id_product = intval($newId);
					$wpl->id_product_attribute = 0;
					$wpl->id_warehouse = $id_selected_warehouse;
					$wpl->save();					
				}

				$value = 1;
				$shops = SCI::getSelectedShopActionList(false, intval($newId));
				foreach ($shops as $shop)
					StockAvailable::setProductDependsOnStock(intval($newId), true, $shop);
			}
			elseif($type_default==3) // enabled + manual
			{
				$value = 1;
				$shops = SCI::getSelectedShopActionList(false, intval($newId));
				foreach ($shops as $shop)
					StockAvailable::setProductDependsOnStock(intval($newId), false, $shop);
					
				$id_selected_warehouse = SCI::getSelectedWarehouse();
				if(!empty($id_selected_warehouse))
				{
					$wpl = new WarehouseProductLocation();
					$wpl->id_product = intval($newId);
					$wpl->id_product_attribute = 0;
					$wpl->id_warehouse = $id_selected_warehouse;
					$wpl->save();
				}
			}
			
			if(!empty($type_default) && sc_in_array($type_default, array(2,3),"catProductUpdate_astTypes"))
			{
				$sql = "UPDATE "._DB_PREFIX_."product SET `advanced_stock_management`='".psql(html_entity_decode($value))."' WHERE id_product=".intval($newId)."";
				Db::getInstance()->Execute($sql);
				
				$sql = "UPDATE "._DB_PREFIX_."product_shop SET `advanced_stock_management`='".psql(html_entity_decode($value))."' WHERE id_product=".intval($newId)." AND id_shop IN (".psql(SCI::getSelectedShopActionList(true)).")";
				Db::getInstance()->Execute($sql);
			}
		}
		
	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="deleted"){
		//Products are not deleted from the products grid, use the recycled bin to delete products or delete icon in toolbar
		// in order to avoid to lose products
		$action = "delete";
		$newId = $_POST["gr_id"];
	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="updated"){

		$action = "update";

		$newQuantity='';
		$ecotaxrate=SCI::getEcotaxTaxRate();
		$id_lang=intval(Tools::getValue('id_lang'));
		$id_specific_price=intval(Tools::getValue('id_specific_price'),0);
		$id_product = intval(Tools::getValue('gr_id'));
		$id_product = $id_product; // for compatibility with old extensions - DO NOT REMOVE
		$fields=array('reference','wholesale_price','price','unit_price_ratio','unity','ecotax','weight','supplier_reference','id_manufacturer','id_supplier',
									'id_tax','id_tax_rules_group','ean13','location','reduction_price','reduction_percent','reduction_from','reduction_to','on_sale',
									'out_of_stock','active','date_add','id_color_default','minimal_quantity','upc','width','height','depth',
									'available_for_order','show_price','online_only','condition','additional_shipping_cost','visibility','available_date','redirect_type','id_product_redirected');
		if(SCAS)
		{
			$fields[] = "advanced_stock_management";
			$fields[] = "location_warehouse";
		}
		$fields_lang=array('name','available_now','available_later','link_rewrite','meta_title','meta_description','meta_keywords','description_short','description');
		$forceUpdateCombinations=array('price_inc_tax','price','id_tax','id_tax_rules_group','ecotax');
		$fieldsWithHTML=array('description','description_short');
		sc_ext::readCustomGridsConfigXML('updateSettings');
		sc_ext::readCustomGridsConfigXML('onBeforeUpdateSQL');
		$reduction_updated_fields = array("from","to","reduction","reduction_type","id_group","id_currency","id_country","from_quantity","price");
		if (version_compare(_PS_VERSION_, '1.6.0.11', '>='))
		{
			$reduction_updated_fields[] = "reduction_tax";
			$fields[] = "reduction_tax";
		}
		$todo=array();
		$todoshop=array();
		$todo_lang=array();
		$newQuantity='';
		$versSuffix='';
		if (isset($_POST['price_inc_tax']) || isset($_POST['ecotax']))
		{
			$tax=Tools::getValue('tax',1)*1;
			$ecotax=Tools::getValue('ecotax',0)*1;
			if ($tax=='NaN' || $tax==0) $tax=1;
			if (
				(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && (int)SCI::getConfigurationValue('PS_USE_ECOTAX', null, 0, SCI::getSelectedShop())==1)
				||
				((version_compare(_PS_VERSION_, '1.4.0.0', '>=') && version_compare(_PS_VERSION_, '1.5.0.0', '<')) && (int)SCI::getConfigurationValue('PS_USE_ECOTAX')==1)
				||
				((version_compare(_PS_VERSION_, '1.3.0.0', '>=') && version_compare(_PS_VERSION_, '1.4.0.0', '<')))
			)
			{
				$_POST['price']=(Tools::getValue('price_inc_tax')*1 - ( _s('CAT_PROD_ECOTAXINCLUDED') ? $ecotax : 0 )) / $tax;
			}else{
				$_POST['price']=(Tools::getValue('price_inc_tax')*1) / $tax;
			}
			if (isset($_POST['ecotax']) && version_compare(_PS_VERSION_, '1.3.0.0', '>='))
				$_POST['ecotax']=$_POST['ecotax'] / $ecotaxrate;
		}
		foreach($fields AS $field)
		{
			if (isset($_POST[$field]))
			{
				if (sc_in_array($field,array('reduction_price','reduction_percent','reduction_from','reduction_to','reduction_tax','unit_price_ratio','price'),"catProductUpdate_specialFields") && version_compare(_PS_VERSION_, '1.4.0.0', '>='))
				{
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) $versSuffix='15';
					switch($field.$versSuffix){
						case 'id_supplier':
							$value = Tools::getValue($field);
							if(empty($value))
							{
								$sql = "UPDATE "._DB_PREFIX_."product SET id_supplier='".psql($value)."',product_supplier_reference=NULL WHERE id_product='".intval($id_product)."'";
								Db::getInstance()->Execute($sql);
							}
							else
							{
								$sql = "UPDATE "._DB_PREFIX_."product SET id_supplier='".psql($value)."' WHERE id_product='".intval($id_product)."'";
								Db::getInstance()->Execute($sql);
							}
							break;
						case 'reduction_price':
							$res=Db::getInstance()->getRow("SELECT COUNT(*) AS nb,reduction_type,reduction FROM "._DB_PREFIX_."specific_price WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 GROUP BY reduction_type");
							if ($res['reduction_type']=='amount' && (int)Tools::getValue($field)==0)
							{
									$sql = "DELETE FROM "._DB_PREFIX_."specific_price WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1";
									Db::getInstance()->Execute($sql);
							}else{
								if ((int)$res['nb']>0)
								{
									$sql = "UPDATE "._DB_PREFIX_."specific_price SET reduction='".psql(Tools::getValue($field))."',reduction_type='amount' WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1";
									Db::getInstance()->Execute($sql);
								}elseif ((int)Tools::getValue($field)!=0){
									$sql = "INSERT INTO "._DB_PREFIX_."specific_price (`from`,`to`,reduction,reduction_type,id_product,id_group,id_currency,id_country,from_quantity) VALUES ('0000-00-00 00:00:00','0000-00-00 00:00:00','".psql(Tools::getValue($field))."','amount',".intval($id_product).",0,0,0,1)";
									Db::getInstance()->Execute($sql);
								}
							}
							addToHistory('catalog_tree','modification',$field,intval($id_product),$id_lang,_DB_PREFIX_."specific_price",psql(Tools::getValue($field)),$res['reduction']);
							break;
						case 'reduction_tax15':
							// UPDATE
							if(!empty($id_specific_price))
							{
								$sql = "UPDATE "._DB_PREFIX_."specific_price SET reduction_tax='".intval(Tools::getValue($field))."' WHERE id_specific_price='".intval($id_specific_price)."'";
								Db::getInstance()->Execute($sql);
							}
							// INSERT
							else
							{
								$sql = "INSERT INTO "._DB_PREFIX_."specific_price (`from`,`to`,reduction_type,id_product,id_group,id_currency,id_country,from_quantity,id_customer,id_product_attribute,price,id_shop,reduction_tax) VALUES ('0000-00-00 00:00:00','0000-00-00 00:00:00','amount',".intval($id_product).",0,0,0,1,0,0,-1,".SCI::getSelectedShop().",'".intval(Tools::getValue($field))."')";
								Db::getInstance()->Execute($sql);
								$id_specific_price = Db::getInstance()->Insert_ID();
							}
							
							if(SCMS && SCI::getSelectedShop()>0)
							{
								$sql_specific_price = "SELECT *
										FROM `"._DB_PREFIX_."specific_price`
										WHERE id_specific_price = '".$id_specific_price."'";
								$original_specific_price=Db::getInstance()->getRow($sql_specific_price);
									$shops = SCI::getSelectedShopActionList(false, $id_product);
								foreach($shops as $shop_id)
								{
									// Si ce n'est pas la shop sélectionné
									// et si le produit est lié à cette shop
									if($shop_id!=SCI::getSelectedShop()/* && in_array($shop_id, $authorized_shops)*/)
									{
										$sql_specific_price = "SELECT id_specific_price
										FROM `"._DB_PREFIX_."specific_price`
										WHERE id_product = '".$id_product."'
											 AND `from` <= '".date("Y-m-d H:i:s")."'
											 AND (`to` >= '".date("Y-m-d H:i:s")."' OR `to`='0000-00-00 00:00:00')
											 AND (
											 		`reduction` >= 0
											 		OR `price` >= 0
											 	)
											 AND id_shop = '".$shop_id."'
										 LIMIT 1";
										$res_specific_price=Db::getInstance()->executeS($sql_specific_price);
										// UPDATE
										if(!empty($res_specific_price[0]["id_specific_price"]))
										{
											$update = "";
											foreach ($reduction_updated_fields as $reduction_updated_field)
											{
												if(!empty($update))
													$update .= ", ";
												$update .= "`".$reduction_updated_field."` = '".psql($original_specific_price[$reduction_updated_field])."'";
											}
											
											$res_specific_price = $res_specific_price[0];
											$sql = "UPDATE "._DB_PREFIX_."specific_price SET ".$update." WHERE id_specific_price='".intval($res_specific_price["id_specific_price"])."'";
											Db::getInstance()->Execute($sql);
										}
										// INSERT
										else
										{
											$insert = "";
											$insert_values = "";
											foreach ($reduction_updated_fields as $reduction_updated_field)
											{
												$insert .= ",`".$reduction_updated_field."`";
												$insert_values .= ",'".psql($original_specific_price[$reduction_updated_field])."'";
											}
											$sql = "INSERT INTO "._DB_PREFIX_."specific_price (id_product,id_shop".$insert.") VALUES (".intval($id_product).",".(int)$shop_id."".$insert_values.")";
											Db::getInstance()->Execute($sql);
										}
									}
								}
							}
							addToHistory('catalog_tree','modification',$field,intval($id_product),$id_lang,_DB_PREFIX_."specific_price",psql(Tools::getValue($field)),$res['reduction']);
							break;
						case 'reduction_price15':
							// UPDATE
							if(!empty($id_specific_price))
							{
								$sql = "UPDATE "._DB_PREFIX_."specific_price SET reduction='".psql(Tools::getValue($field))."',reduction_type='amount' WHERE id_specific_price='".intval($id_specific_price)."'";
								Db::getInstance()->Execute($sql);
							}
							// INSERT
							else
							{
								$sql = "INSERT INTO "._DB_PREFIX_."specific_price (`from`,`to`,reduction,reduction_type,id_product,id_group,id_currency,id_country,from_quantity,id_shop,price".(version_compare(_PS_VERSION_, '1.6.0.11', '>=')?",reduction_tax":"").") 
										VALUES ('0000-00-00 00:00:00','0000-00-00 00:00:00','".psql(Tools::getValue($field))."','amount',".intval($id_product).",0,0,0,1,".SCI::getSelectedShop().",'-1'".(version_compare(_PS_VERSION_, '1.6.0.11', '>=')?",'"._s('CAT_PROD_SPECIFIC_PRICES_DEFAULT_TAX')."'":"").")";
								Db::getInstance()->Execute($sql);
								$id_specific_price = Db::getInstance()->Insert_ID();
							}
							
							if(SCMS && SCI::getSelectedShop()>0)
							{
								$sql_specific_price = "SELECT *
										FROM `"._DB_PREFIX_."specific_price`
										WHERE id_specific_price = '".$id_specific_price."'";
								$original_specific_price=Db::getInstance()->getRow($sql_specific_price);
								//if(SCI::getSelectedShop()!=0)
									$shops = SCI::getSelectedShopActionList(false, $id_product);
								/*else
									$shops = Shop::getShops(true,null,true);*/
								//$authorized_shops = SCI::getShopsByProduct($id_product);
								foreach($shops as $shop_id)
								{
									// Si ce n'est pas la shop sélectionné
									// et si le produit est lié à cette shop
									if($shop_id!=SCI::getSelectedShop()/* && in_array($shop_id, $authorized_shops)*/)
									{
										$sql_specific_price = "SELECT id_specific_price
										FROM `"._DB_PREFIX_."specific_price`
										WHERE id_product = '".$id_product."'
											 AND `from` <= '".date("Y-m-d H:i:s")."'
											 AND (`to` >= '".date("Y-m-d H:i:s")."' OR `to`='0000-00-00 00:00:00')
											 AND (
											 		`reduction` >= 0
											 		OR `price` >= 0
											 	)
											 AND id_shop = '".$shop_id."'
										 LIMIT 1";
										$res_specific_price=Db::getInstance()->executeS($sql_specific_price);
										// UPDATE
										if(!empty($res_specific_price[0]["id_specific_price"]))
										{
											$update = "";
											foreach ($reduction_updated_fields as $reduction_updated_field)
											{
												if(!empty($update))
													$update .= ", ";
												$update .= "`".$reduction_updated_field."` = '".psql($original_specific_price[$reduction_updated_field])."'";
											}
											
											$res_specific_price = $res_specific_price[0];
											$sql = "UPDATE "._DB_PREFIX_."specific_price SET ".$update." WHERE id_specific_price='".intval($res_specific_price["id_specific_price"])."'";
											Db::getInstance()->Execute($sql);
										}
										// INSERT
										else
										{
											$insert = "";
											$insert_values = "";
											foreach ($reduction_updated_fields as $reduction_updated_field)
											{
												$insert .= ",`".$reduction_updated_field."`";
												$insert_values .= ",'".psql($original_specific_price[$reduction_updated_field])."'";
											}
											$sql = "INSERT INTO "._DB_PREFIX_."specific_price (id_product,id_shop".$insert.") VALUES (".intval($id_product).",".(int)$shop_id."".$insert_values.")";
											Db::getInstance()->Execute($sql);
										}
									}
								}
							}
							
							/*$res=Db::getInstance()->getRow("SELECT COUNT(*) AS nb,reduction_type,reduction FROM "._DB_PREFIX_."specific_price WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 AND id_customer=0 AND id_product_attribute=0 GROUP BY reduction_type");
							if ($res['reduction_type']=='amount' && (int)Tools::getValue($field)==0)
							{
									$sql = "DELETE FROM "._DB_PREFIX_."specific_price WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 AND id_customer=0 AND id_product_attribute=0";
									Db::getInstance()->Execute($sql);
							}else{
								if ((int)$res['nb']>0)
								{
									$sql = "UPDATE "._DB_PREFIX_."specific_price SET reduction='".psql(Tools::getValue($field))."',reduction_type='amount' WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 AND id_customer=0 AND id_product_attribute=0";
									Db::getInstance()->Execute($sql);
								}elseif ((int)Tools::getValue($field)!=0){
									$sql = "INSERT INTO "._DB_PREFIX_."specific_price (`from`,`to`,reduction,reduction_type,id_product,id_group,id_currency,id_country,from_quantity,id_customer,id_product_attribute,price) VALUES ('0000-00-00 00:00:00','0000-00-00 00:00:00','".psql(Tools::getValue($field))."','amount',".intval($id_product).",0,0,0,1,0,0,-1)";
									Db::getInstance()->Execute($sql);
								}
							}*/
							addToHistory('catalog_tree','modification',$field,intval($id_product),$id_lang,_DB_PREFIX_."specific_price",psql(Tools::getValue($field)),'-');
							break;
						case 'reduction_percent':
							$res=Db::getInstance()->getRow("SELECT COUNT(*) AS nb,reduction_type,reduction FROM "._DB_PREFIX_."specific_price WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 GROUP BY reduction_type");
							if ($res['reduction_type']=='percentage' && (int)Tools::getValue($field)==0)
							{
									$sql = "DELETE FROM "._DB_PREFIX_."specific_price WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1";
									Db::getInstance()->Execute($sql);
							}else{
								if ((int)$res['nb']>0)
								{
									$sql = "UPDATE "._DB_PREFIX_."specific_price SET reduction='".psql(Tools::getValue($field)/100)."',reduction_type='percentage' WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1";
									Db::getInstance()->Execute($sql);
								}elseif ((int)Tools::getValue($field)!=0){
									$sql = "INSERT INTO "._DB_PREFIX_."specific_price (`from`,`to`,reduction,reduction_type,id_product,id_group,id_currency,id_country,from_quantity) VALUES ('0000-00-00 00:00:00','0000-00-00 00:00:00','".psql(Tools::getValue($field)/100)."','percentage',".intval($id_product).",0,0,0,1)";
									Db::getInstance()->Execute($sql);
								}
							}
							addToHistory('catalog_tree','modification',$field,intval($id_product),$id_lang,_DB_PREFIX_."specific_price",psql(Tools::getValue($field)),$res['reduction']*100);
							break;
						case 'reduction_percent15':
							// UPDATE
							if(!empty($id_specific_price))
							{
								$sql = "UPDATE "._DB_PREFIX_."specific_price SET reduction='".psql(Tools::getValue($field)/100)."',reduction_type='percentage' WHERE id_specific_price='".intval($id_specific_price)."'";
								Db::getInstance()->Execute($sql);
							}
							// INSERT
							else
							{
								$sql = "INSERT INTO "._DB_PREFIX_."specific_price (`from`,`to`,reduction,reduction_type,id_product,id_group,id_currency,id_country,from_quantity,id_customer,id_product_attribute,price,id_shop".(version_compare(_PS_VERSION_, '1.6.0.11', '>=')?",reduction_tax":"").") VALUES ('0000-00-00 00:00:00','0000-00-00 00:00:00','".psql(Tools::getValue($field)/100)."','percentage',".intval($id_product).",0,0,0,1,0,0,-1,".SCI::getSelectedShop()."".(version_compare(_PS_VERSION_, '1.6.0.11', '>=')?",'"._s('CAT_PROD_SPECIFIC_PRICES_DEFAULT_TAX')."'":"").")";
								Db::getInstance()->Execute($sql);
								$id_specific_price = Db::getInstance()->Insert_ID();
							}
								
							if(SCMS && SCI::getSelectedShop()>0)
							{
								$sql_specific_price = "SELECT *
										FROM `"._DB_PREFIX_."specific_price`
										WHERE id_specific_price = '".$id_specific_price."'";
								$original_specific_price=Db::getInstance()->executeS($sql_specific_price);
								$original_specific_price = $original_specific_price[0];
								//if(SCI::getSelectedShop()!=0)
									$shops = SCI::getSelectedShopActionList(false, $id_product);
								/*else
									$shops = Shop::getShops(true,null,true);*/
								//$authorized_shops = SCI::getShopsByProduct($id_product);
								foreach($shops as $shop_id)
								{
									// Si ce n'est pas la shop sélectionné
									// et si le produit est lié à cette shop
									if($shop_id!=SCI::getSelectedShop()/* && in_array($shop_id, $authorized_shops)*/)
									{
										$sql_specific_price = "SELECT id_specific_price
										FROM `"._DB_PREFIX_."specific_price`
										WHERE id_product = '".$id_product."'
											 AND `from` <= '".date("Y-m-d H:i:s")."'
											 AND (`to` >= '".date("Y-m-d H:i:s")."' OR `to`='0000-00-00 00:00:00')
											 AND (
											 		`reduction` >= 0
											 		OR `price` >= 0
											 	)
											 AND id_shop = '".$shop_id."'
										 LIMIT 1";
										$res_specific_price=Db::getInstance()->executeS($sql_specific_price);
										// UPDATE
										if(!empty($res_specific_price[0]["id_specific_price"]))
										{
											$update = "";
											foreach ($reduction_updated_fields as $reduction_updated_field)
											{
												if(!empty($update))
													$update .= ", ";
												$update .= "`".$reduction_updated_field."` = '".psql($original_specific_price[$reduction_updated_field])."'";
											}
											
											$res_specific_price = $res_specific_price[0];
											$sql = "UPDATE "._DB_PREFIX_."specific_price SET ".$update." WHERE id_specific_price='".intval($res_specific_price["id_specific_price"])."'";
											Db::getInstance()->Execute($sql);
										}
										// INSERT
										else
										{
											$insert = "";
											$insert_values = "";
											foreach ($reduction_updated_fields as $reduction_updated_field)
											{
												$insert .= ",`".$reduction_updated_field."`";
												$insert_values .= ",'".psql($original_specific_price[$reduction_updated_field])."'";
											}
											$sql = "INSERT INTO "._DB_PREFIX_."specific_price (id_product,id_shop".$insert.") VALUES (".intval($id_product).",".(int)$shop_id."".$insert_values.")";
											Db::getInstance()->Execute($sql);
										}
									}
								}
							}
							/*$res=Db::getInstance()->getRow("SELECT COUNT(*) AS nb,reduction_type,reduction FROM "._DB_PREFIX_."specific_price WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 AND id_customer=0 AND id_product_attribute=0 GROUP BY reduction_type");
							if ($res['reduction_type']=='percentage' && (int)Tools::getValue($field)==0)
							{
									$sql = "DELETE FROM "._DB_PREFIX_."specific_price WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 AND id_customer=0 AND id_product_attribute=0";
									Db::getInstance()->Execute($sql);
							}else{
								if ((int)$res['nb']>0)
								{
									$sql = "UPDATE "._DB_PREFIX_."specific_price SET reduction='".psql(Tools::getValue($field)/100)."',reduction_type='percentage' WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 AND id_customer=0 AND id_product_attribute=0";
									Db::getInstance()->Execute($sql);
								}elseif ((int)Tools::getValue($field)!=0){
									$sql = "INSERT INTO "._DB_PREFIX_."specific_price (`from`,`to`,reduction,reduction_type,id_product,id_group,id_currency,id_country,from_quantity,id_customer,id_product_attribute,price) VALUES ('0000-00-00 00:00:00','0000-00-00 00:00:00','".psql(Tools::getValue($field)/100)."','percentage',".intval($id_product).",0,0,0,1,0,0,-1)";
									Db::getInstance()->Execute($sql);
								}
							}*/
							addToHistory('catalog_tree','modification',$field,intval($id_product),$id_lang,_DB_PREFIX_."specific_price",psql(Tools::getValue($field)),'-');
							break;
						case 'reduction_from':
							$value=Tools::getValue($field);
							if ($value=='') $value='0000-00-00 00:00:00';
							$res=Db::getInstance()->getRow("SELECT COUNT(*) AS nb,`from` AS dfrom,`to` AS dto FROM "._DB_PREFIX_."specific_price WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 GROUP BY id_product");
							$othervalue=$res['dto'];
							if ($value==$othervalue) {$value='0000-00-00 00:00:00'; $othervalue='0000-00-00 00:00:00';}
							if ((int)$res['nb']>0)
							{
								$sql = "UPDATE "._DB_PREFIX_."specific_price SET `from`='".psql($value)."',`to`='".psql($othervalue)."' WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1";
								Db::getInstance()->Execute($sql);
							}else{
								$sql = "INSERT INTO "._DB_PREFIX_."specific_price (`from`,`to`,reduction_type,id_product,id_group,id_currency,id_country,from_quantity) VALUES ('".psql($value)."','".psql($value)."','amount',".intval($id_product).",0,0,0,1)";
								Db::getInstance()->Execute($sql);
							}
							addToHistory('catalog_tree','modification',$field,intval($id_product),$id_lang,_DB_PREFIX_."specific_price",psql($value),$res['dfrom']);
							break;
						case 'reduction_from15':
							$value=Tools::getValue($field);
							if ($value=='') $value='0000-00-00 00:00:00';
							$res=Db::getInstance()->getRow("SELECT COUNT(*) AS nb,`from` AS dfrom,`to` AS dto FROM "._DB_PREFIX_."specific_price WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 AND id_customer=0 AND id_product_attribute=0 GROUP BY id_product");
							$othervalue=$res['dto'];
							if ($value==$othervalue && $value!='0000-00-00 00:00:00') { $othervalue = '0000-00-00 00:00:00'; }
							// UPDATE
							if(!empty($id_specific_price))
							{
								$sql = "UPDATE "._DB_PREFIX_."specific_price SET `from`='".psql($value)."',`to`='".psql($othervalue)."' WHERE id_specific_price='".intval($id_specific_price)."'";
								Db::getInstance()->Execute($sql);
							}
							// INSERT
							else
							{
								$sql = "INSERT INTO "._DB_PREFIX_."specific_price (`from`,`to`,reduction_type,id_product,id_group,id_currency,id_country,from_quantity,id_customer,id_product_attribute,price,id_shop".(version_compare(_PS_VERSION_, '1.6.0.11', '>=')?",reduction_tax":"").") VALUES ('".psql($value)."','".psql($othervalue)."','amount',".intval($id_product).",0,0,0,1,0,0,-1,".SCI::getSelectedShop()."".(version_compare(_PS_VERSION_, '1.6.0.11', '>=')?",'"._s('CAT_PROD_SPECIFIC_PRICES_DEFAULT_TAX')."'":"").")";
								Db::getInstance()->Execute($sql);
								$id_specific_price = Db::getInstance()->Insert_ID();
							}
								
							if(SCMS && SCI::getSelectedShop()>0)
							{
								$sql_specific_price = "SELECT *
										FROM `"._DB_PREFIX_."specific_price`
										WHERE id_specific_price = '".$id_specific_price."'";
								$original_specific_price=Db::getInstance()->getRow($sql_specific_price);
								//if(SCI::getSelectedShop()!=0)
									$shops = SCI::getSelectedShopActionList(false, $id_product);
								/*else
									$shops = Shop::getShops(true,null,true);*/
								//$authorized_shops = SCI::getShopsByProduct($id_product);
								foreach($shops as $shop_id)
								{
									// Si ce n'est pas la shop sélectionné
									// et si le produit est lié à cette shop
									if($shop_id!=SCI::getSelectedShop()/* && in_array($shop_id, $authorized_shops)*/)
									{
										$sql_specific_price = "SELECT id_specific_price
										FROM `"._DB_PREFIX_."specific_price`
										WHERE id_product = '".$id_product."'
											 AND `from` <= '".date("Y-m-d H:i:s")."'
											 AND (`to` >= '".date("Y-m-d H:i:s")."' OR `to`='0000-00-00 00:00:00')
											 AND (
											 		`reduction` >= 0
											 		OR `price` >= 0
											 	)
											 AND id_shop = '".$shop_id."'
										 LIMIT 1";
										$res_specific_price=Db::getInstance()->executeS($sql_specific_price);
										// UPDATE
										if(!empty($res_specific_price[0]["id_specific_price"]))
										{
											$update = "";
											foreach ($reduction_updated_fields as $reduction_updated_field)
											{
												if(!empty($update))
													$update .= ", ";
												$update .= "`".$reduction_updated_field."` = '".psql($original_specific_price[$reduction_updated_field])."'";
											}
											
											$res_specific_price = $res_specific_price[0];
											$sql = "UPDATE "._DB_PREFIX_."specific_price SET ".$update." WHERE id_specific_price='".intval($res_specific_price["id_specific_price"])."'";
											Db::getInstance()->Execute($sql);
										}
										// INSERT
										else
										{
											$insert = "";
											$insert_values = "";
											foreach ($reduction_updated_fields as $reduction_updated_field)
											{
												$insert .= ",`".$reduction_updated_field."`";
												$insert_values .= ",'".psql($original_specific_price[$reduction_updated_field])."'";
											}
											$sql = "INSERT INTO "._DB_PREFIX_."specific_price (id_product,id_shop".$insert.") VALUES (".intval($id_product).",".(int)$shop_id."".$insert_values.")";
											Db::getInstance()->Execute($sql);
										}
									}
								}
							}
							/*$value=Tools::getValue($field);
							if ($value=='') $value='0000-00-00 00:00:00';
							$res=Db::getInstance()->getRow("SELECT COUNT(*) AS nb,`from` AS dfrom,`to` AS dto FROM "._DB_PREFIX_."specific_price WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 AND id_customer=0 AND id_product_attribute=0 GROUP BY id_product");
							$othervalue=$res['dto'];
							if ($value==$othervalue) {$value='0000-00-00 00:00:00'; $othervalue='0000-00-00 00:00:00';}
							if ((int)$res['nb']>0)
							{
								$sql = "UPDATE "._DB_PREFIX_."specific_price SET `from`='".psql($value)."',`to`='".psql($othervalue)."' WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 AND id_customer=0 AND id_product_attribute=0";
								Db::getInstance()->Execute($sql);
							}else{
								$sql = "INSERT INTO "._DB_PREFIX_."specific_price (`from`,`to`,reduction_type,id_product,id_group,id_currency,id_country,from_quantity,id_customer,id_product_attribute,price) VALUES ('".psql($value)."','".psql($value)."','amount',".intval($id_product).",0,0,0,1,0,0,-1)";
								Db::getInstance()->Execute($sql);
							}*/
							addToHistory('catalog_tree','modification',$field,intval($id_product),$id_lang,_DB_PREFIX_."specific_price",psql($value),$res['dfrom']);
							break;
						case 'reduction_to':
							$value=Tools::getValue($field);
							if ($value=='') $value='0000-00-00 00:00:00';
							$res=Db::getInstance()->getRow("SELECT COUNT(*) AS nb,`from` AS dfrom,`to` AS dto FROM "._DB_PREFIX_."specific_price WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 GROUP BY id_product");
							$othervalue=$res['dfrom'];
							if ($value==$othervalue) {$value='0000-00-00 00:00:00'; $othervalue='0000-00-00 00:00:00';}
							if ((int)$res['nb']>0)
							{
								$sql = "UPDATE "._DB_PREFIX_."specific_price SET `from`='".psql($othervalue)."',`to`='".psql($value)."' WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1";
								Db::getInstance()->Execute($sql);
							}else{
								$sql = "INSERT INTO "._DB_PREFIX_."specific_price (`from`,`to`,reduction_type,id_product,id_group,id_currency,id_country,from_quantity) VALUES ('".psql($value)."','".psql($value)."','amount',".intval($id_product).",0,0,0,1)";
								Db::getInstance()->Execute($sql);
							}
							addToHistory('catalog_tree','modification',$field,intval($id_product),$id_lang,_DB_PREFIX_."specific_price",psql(Tools::getValue($field)),$res['dto']);
							break;
						case 'reduction_to15':
							$value=Tools::getValue($field);
							if ($value=='') $value='0000-00-00 00:00:00';
							$res=Db::getInstance()->getRow("SELECT COUNT(*) AS nb,`from` AS dfrom,`to` AS dto FROM "._DB_PREFIX_."specific_price WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 AND id_customer=0 AND id_product_attribute=0 GROUP BY id_product");
							$othervalue=$res['dfrom'];
							if ($value==$othervalue && $value!='0000-00-00 00:00:00') { $othervalue = '0000-00-00 00:00:00'; }
							// UPDATE
							if(!empty($id_specific_price))
							{
								$sql = "UPDATE "._DB_PREFIX_."specific_price SET `from`='".psql($othervalue)."',`to`='".psql($value)."' WHERE id_specific_price='".intval($id_specific_price)."'";
								Db::getInstance()->Execute($sql);
							}
							// INSERT
							else
							{
								$sql = "INSERT INTO "._DB_PREFIX_."specific_price (`from`,`to`,reduction_type,id_product,id_group,id_currency,id_country,from_quantity,id_customer,id_product_attribute,price,id_shop".(version_compare(_PS_VERSION_, '1.6.0.11', '>=')?",reduction_tax":"").") VALUES ('".psql($othervalue)."','".psql($value)."','amount',".intval($id_product).",0,0,0,1,0,0,-1,".SCI::getSelectedShop()."".(version_compare(_PS_VERSION_, '1.6.0.11', '>=')?",'"._s('CAT_PROD_SPECIFIC_PRICES_DEFAULT_TAX')."'":"").")";
								Db::getInstance()->Execute($sql);
								$id_specific_price = Db::getInstance()->Insert_ID();
							}
								
							if(SCMS && SCI::getSelectedShop()>0)
							{
								$sql_specific_price = "SELECT *
										FROM `"._DB_PREFIX_."specific_price`
										WHERE id_specific_price = '".$id_specific_price."'";
								$original_specific_price=Db::getInstance()->getRow($sql_specific_price);
								//if(SCI::getSelectedShop()!=0)
									$shops = SCI::getSelectedShopActionList(false, $id_product);
								/*else
									$shops = Shop::getShops(true,null,true);*/
								//$authorized_shops = SCI::getShopsByProduct($id_product);
								foreach($shops as $shop_id)
								{
									// Si ce n'est pas la shop sélectionné
									// et si le produit est lié à cette shop
									if($shop_id!=SCI::getSelectedShop()/* && in_array($shop_id, $authorized_shops)*/)
									{
										$sql_specific_price = "SELECT id_specific_price
										FROM `"._DB_PREFIX_."specific_price`
										WHERE id_product = '".$id_product."'
											 AND `from` <= '".date("Y-m-d H:i:s")."'
											 AND (`to` >= '".date("Y-m-d H:i:s")."' OR `to`='0000-00-00 00:00:00')
											 AND (
											 		`reduction` >= 0
											 		OR `price` >= 0
											 	)
											 AND id_shop = '".$shop_id."'
										 LIMIT 1";
										$res_specific_price=Db::getInstance()->executeS($sql_specific_price);
										// UPDATE
										if(!empty($res_specific_price[0]["id_specific_price"]))
										{
											$update = "";
											foreach ($reduction_updated_fields as $reduction_updated_field)
											{
												if(!empty($update))
													$update .= ", ";
												$update .= "`".$reduction_updated_field."` = '".psql($original_specific_price[$reduction_updated_field])."'";
											}
											
											$res_specific_price = $res_specific_price[0];
											$sql = "UPDATE "._DB_PREFIX_."specific_price SET ".$update." WHERE id_specific_price='".intval($res_specific_price["id_specific_price"])."'";
											Db::getInstance()->Execute($sql);
										}
										// INSERT
										else
										{
											$insert = "";
											$insert_values = "";
											foreach ($reduction_updated_fields as $reduction_updated_field)
											{
												$insert .= ",`".$reduction_updated_field."`";
												$insert_values .= ",'".psql($original_specific_price[$reduction_updated_field])."'";
											}
											$sql = "INSERT INTO "._DB_PREFIX_."specific_price (id_product,id_shop".$insert.") VALUES (".intval($id_product).",".(int)$shop_id."".$insert_values.")";
											Db::getInstance()->Execute($sql);
										}
									}
								}
							}
							/*$value=Tools::getValue($field);
							if ($value=='') $value='0000-00-00 00:00:00';
							$res=Db::getInstance()->getRow("SELECT COUNT(*) AS nb,`from` AS dfrom,`to` AS dto FROM "._DB_PREFIX_."specific_price WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 AND id_customer=0 AND id_product_attribute=0 GROUP BY id_product");
							$othervalue=$res['dfrom'];
							if ($value==$othervalue) {$value='0000-00-00 00:00:00'; $othervalue='0000-00-00 00:00:00';}
							if ((int)$res['nb']>0)
							{
								$sql = "UPDATE "._DB_PREFIX_."specific_price SET `from`='".psql($othervalue)."',`to`='".psql($value)."' WHERE id_product=".intval($id_product)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 AND id_customer=0 AND id_product_attribute=0";
								Db::getInstance()->Execute($sql);
							}else{
								$sql = "INSERT INTO "._DB_PREFIX_."specific_price (`from`,`to`,reduction_type,id_product,id_group,id_currency,id_country,from_quantity,id_customer,id_product_attribute,price) VALUES ('".psql($value)."','".psql($value)."','amount',".intval($id_product).",0,0,0,1,0,0,-1)";
								Db::getInstance()->Execute($sql);
							}*/
							addToHistory('catalog_tree','modification',$field,intval($id_product),$id_lang,_DB_PREFIX_."specific_price",psql(Tools::getValue($field)),$res['dto']);
							break;
						case 'unit_price_ratio':
						case 'unit_price_ratio15':
							//$sql = "UPDATE "._DB_PREFIX_."product SET `unit_price_ratio`= 0 WHERE id_product=".intval($id_product);
							//$todoshop[]="`unit_price_ratio`=0";
							if (floatval(Tools::getValue($field))>0)
							{
								$sql = "UPDATE "._DB_PREFIX_."product SET `unit_price_ratio`= price/".floatval(Tools::getValue($field))." WHERE id_product=".intval($id_product);
								$todoshop[]="`unit_price_ratio`=price/".floatval(Tools::getValue($field))."";
							}
							Db::getInstance()->Execute($sql);
							addToHistory('catalog_tree','modification',$field,intval($id_product),$id_lang,_DB_PREFIX_."product",psql(Tools::getValue($field)),($row['unit_price_ratio']>0?number_format($row['price']/$row['unit_price_ratio'],2):0));
							break;
						case 'price':
						case 'price15':
							if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
								$sql = "SELECT ps.unit_price_ratio,ps.price FROM "._DB_PREFIX_."product_shop ps LEFT JOIN "._DB_PREFIX_."product p ON (p.id_product=ps.id_product) WHERE ps.id_product=".intval($id_product)." AND ps.id_shop='".(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default')."'";
							else
								$sql = "SELECT unit_price_ratio,price FROM "._DB_PREFIX_."product WHERE id_product=".intval($id_product);
							$row = Db::getInstance()->getRow($sql);
							if ($row['price']>0 && $row['unit_price_ratio']>0)
							{
								$ratio = floatval(Tools::getValue($field))/($row['price']/$row['unit_price_ratio']);
							}else{
								$ratio = 0;
							}
							$sql = "UPDATE "._DB_PREFIX_."product SET `price`=".floatval(Tools::getValue($field)).",`unit_price_ratio`=".floatval($ratio)." WHERE id_product=".intval($id_product);
							Db::getInstance()->Execute($sql);
							$todoshop[]="`price`='".floatval(Tools::getValue($field))."'";
							$todoshop[]="`unit_price_ratio`='".floatval($ratio)."'";
							addToHistory('catalog_tree','modification',$field,intval($id_product),$id_lang,_DB_PREFIX_."product",psql(Tools::getValue($field)),$row['price']);
							break;
					}
				}else{
					if(SCAS && $field=="location_warehouse")
					{
						$advanced_stock_management = Tools::getValue("type_advanced_stock_management");
						$val = Tools::getValue($field);
						if($advanced_stock_management==2 || $advanced_stock_management==3) // enabled OR enabled + manual
						{
							$id_selected_warehouse = SCI::getSelectedWarehouse();
							if(!empty($id_selected_warehouse))
							{
								// ADD IN WAREHOUSE
								$exist = WarehouseProductLocation::getIdByProductAndWarehouse(intval($id_product), 0, (int)$id_selected_warehouse);
								if(empty($exist))
									$wpl = new WarehouseProductLocation();
								else
									$wpl = new WarehouseProductLocation((int)$exist);
								$wpl->id_product = intval($id_product);
								$wpl->id_product_attribute = 0;
								$wpl->id_warehouse = (int)$id_selected_warehouse;
								$wpl->location = $val;
								$wpl->save();
							}
						}
					}
					elseif(SCAS && $field=="advanced_stock_management")
					{
						$value = 0;
						$val = Tools::getValue($field);
						if($val==1) // disabled
						{
							$value = 0;
							$shops = SCI::getSelectedShopActionList(false, intval($id_product));
							foreach ($shops as $shop)
								StockAvailable::setProductDependsOnStock(intval($id_product), false, $shop);
						}
						elseif($val==2) // enabled
						{
							$id_selected_warehouse = SCI::getSelectedWarehouse();
							if(!empty($id_selected_warehouse))
							{				
								$stock_manager = StockManagerFactory::getManager();
								
								// ADD IN WAREHOUSE
								$exist = WarehouseProductLocation::getIdByProductAndWarehouse(intval($id_product), 0, (int)$id_selected_warehouse);
								if(empty($exist))
								{
									$wpl = new WarehouseProductLocation();
									$wpl->id_product = intval($id_product);
									$wpl->id_product_attribute = 0;
									$wpl->id_warehouse = (int)$id_selected_warehouse;
									$wpl->save();
								}
								
								$combinations = Db::getInstance()->executeS('
									SELECT *
									FROM `'._DB_PREFIX_.'product_attribute` pa
									WHERE pa.`id_product` = '.(int)$id_product);
								if(!empty($combinations) && count($combinations)>0)
								{
									$warehouse = new Warehouse($id_selected_warehouse);
									
									foreach($combinations as $combination)
									{
										// ADD IN WAREHOUSE
										$exist = WarehouseProductLocation::getIdByProductAndWarehouse(intval($id_product), (int)$combination["id_product_attribute"], (int)$id_selected_warehouse);
										if(empty($exist))
										{
											$wpl = new WarehouseProductLocation();
											$wpl->id_product = intval($id_product);
											$wpl->id_product_attribute = (int)$combination["id_product_attribute"];
											$wpl->id_warehouse = (int)$id_selected_warehouse;
											$wpl->save();
										}
									
										// EMPTY ACUTAL STOCK FOR COMBINATION
										$query = new DbQuery();
										$query->select('SUM(st.physical_quantity) as physical_quantity');
										$query->from('stock', "st");
										$query->where('st.id_product = '.(int)$id_product.'');
										$query->where('st.id_product_attribute = '.(int)$combination["id_product_attribute"].'');
										$query->where('st.id_warehouse = '.(int)$id_selected_warehouse.'');
										$avanced_quantities = Db::getInstance()->getRow($query);
										if(!empty($avanced_quantities["physical_quantity"]))
										{
											$stock_manager->removeProduct($id_product, $combination["id_product_attribute"], $warehouse, $avanced_quantities["physical_quantity"], 4, 1);
										}
										
										// ADD STOCK FOR COMBINATION
										$price = 0;
										$quantity = 0;
										$res=Db::getInstance()->ExecuteS("SELECT sa.quantity, pas.wholesale_price
											FROM "._DB_PREFIX_."stock_available sa
											INNER JOIN "._DB_PREFIX_."product p ON (sa.id_product = p.id_product AND sa.id_shop = p.id_shop_default)
												INNER JOIN "._DB_PREFIX_."product_attribute_shop pas ON (pas.id_product_attribute = sa.id_product_attribute AND pas.id_shop = p.id_shop_default)
											WHERE sa.id_product='".(int)$id_product."'
											AND sa.id_product_attribute='".(int)$combination["id_product_attribute"]."'");
										/*echo "SELECT sa.quantity, pas.wholesale_price
											FROM "._DB_PREFIX_."stock_available sa
											INNER JOIN "._DB_PREFIX_."product p ON (sa.id_product = p.id_product AND sa.id_shop = p.id_shop_default)
												INNER JOIN "._DB_PREFIX_."product_attribute_shop pas ON (pas.id_product_attribute = sa.id_product_attribute AND pas.id_shop = p.id_shop_default)
											WHERE sa.id_product='".(int)$id_product."'
											AND sa.id_product_attribute='".(int)$combination["id_product_attribute"]."'\n";*/
										if(!empty($res[0]["wholesale_price"]))
											$price = $res[0]["wholesale_price"];
										if(!empty($res[0]["quantity"]))
											$quantity = $res[0]["quantity"];
										if(!empty($quantity) && $quantity>0)
										{
											$id_currency=$cookie->id_currency;
											if ($id_currency != $warehouse->id_currency)
											{
												$price_converted_to_default_currency = Tools::convertPrice($price, $id_currency, false);
												$price = Tools::convertPrice($price_converted_to_default_currency, $warehouse->id_currency, true);
											}
											if($quantity>0)
												$stock_manager->addProduct($id_product, $combination["id_product_attribute"], $warehouse, $quantity, 4, $price, 1);
											/*else
												$stock_manager->removeProduct($id_product, $combination["id_product_attribute"], $warehouse, $quantity, 4, 1);
											if ($stock_manager->addProduct($id_product, $combination["id_product_attribute"], $warehouse, $quantity, 4, $price, 1))
												StockAvailable::synchronize($id_product);*/
										}
									}
								}
								else
								{
									$warehouse = new Warehouse($id_selected_warehouse);
									
									// EMPTY ACUTAL STOCK FOR PRODUCT
									$query = new DbQuery();
									$query->select('SUM(st.physical_quantity) as physical_quantity');
									$query->from('stock', "st");
									$query->where('st.id_product = '.(int)$id_product.'');
									$query->where('st.id_warehouse = '.(int)$id_selected_warehouse.'');
									$avanced_quantities = Db::getInstance()->getRow($query);
									if(!empty($avanced_quantities["physical_quantity"]))
									{
										$stock_manager->removeProduct($id_product, 0, $warehouse, $avanced_quantities["physical_quantity"], 4, 1);
									}
									
									// ADD STOCK FOR PRODUCT
									$price = 0;
									$quantity = 0;
									$res=Db::getInstance()->ExecuteS("SELECT sa.quantity, ps.wholesale_price
									FROM "._DB_PREFIX_."stock_available sa
									INNER JOIN "._DB_PREFIX_."product p ON (sa.id_product = p.id_product AND sa.id_shop = p.id_shop_default)
										INNER JOIN "._DB_PREFIX_."product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop = p.id_shop_default)
									WHERE sa.id_product='".(int)$id_product."'
									AND sa.id_product_attribute=0");
									if(!empty($res[0]["wholesale_price"]))
										$price = $res[0]["wholesale_price"];
									if(!empty($res[0]["quantity"]))
										$quantity = $res[0]["quantity"];
									if(!empty($quantity) && $quantity>0)
									{
										//$id_currency=$cookie->id_currency;
										$id_currency=SCI::getConfigurationValue("PS_CURRENCY_DEFAULT");
										if (!empty($id_currency) && $id_currency != $warehouse->id_currency)
										{
											$price_converted_to_default_currency = Tools::convertPrice($price, $id_currency, false);
											$price = Tools::convertPrice($price_converted_to_default_currency, $warehouse->id_currency, true);
										}
										if($quantity>0)
										{
											$stock_manager->addProduct($id_product, 0, $warehouse, $quantity, 4, $price, 1);
										}
										/*else
											$stock_manager->removeProduct($id_product, 0, $warehouse, $quantity, 4, 1);
										if ($stock_manager->addProduct($id_product, 0, $warehouse, $quantity, 4, $price, 1))
											StockAvailable::synchronize($id_product);*/
									}
								}
							}
							
							$value = 1;
							$shops = SCI::getSelectedShopActionList(false, intval($id_product));
							foreach ($shops as $shop)
								StockAvailable::setProductDependsOnStock(intval($id_product), true, $shop);
						}
						elseif($val==3) // enabled + manual
						{
							$value = 1;
							$shops = SCI::getSelectedShopActionList(false, intval($id_product));
							foreach ($shops as $shop)
								StockAvailable::setProductDependsOnStock(intval($id_product), false, $shop);
							
							$id_selected_warehouse = SCI::getSelectedWarehouse();
							if(!empty($id_selected_warehouse))
							{
								if (WarehouseProductLocation::getIdByProductAndWarehouse((int)$id_product, 0, (int)$id_selected_warehouse))
								{
									$wpl = new WarehouseProductLocation();
									$wpl->id_product = intval($id_product);
									$wpl->id_product_attribute = 0;
									$wpl->id_warehouse = $id_selected_warehouse;
									$wpl->save();
								}
								
								$combinations = Db::getInstance()->executeS('
									SELECT *
									FROM `'._DB_PREFIX_.'product_attribute` pa
									WHERE pa.`id_product` = '.(int)$id_product);
								if(!empty($combinations) && count($combinations)>0)
								{
									foreach($combinations as $combination)
									{
										$exist = WarehouseProductLocation::getIdByProductAndWarehouse(intval($id_product), (int)$combination["id_product_attribute"], (int)$id_selected_warehouse);
										if(empty($exist))
										{
											$wpl = new WarehouseProductLocation();
											$wpl->id_product = intval($id_product);
											$wpl->id_product_attribute = (int)$combination["id_product_attribute"];
											$wpl->id_warehouse = (int)$id_selected_warehouse;
											$wpl->save();
										}
									}
								}
							}
						}
						$todo[]='`'.$field."`='".psql(html_entity_decode($value))."'";
						$todoshop[]='`'.$field."`='".psql(html_entity_decode($value))."'";
					}
					else
					{
						$todo[]='`'.$field."`='".psql(html_entity_decode( Tools::getValue($field)))."'";
						if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') 
							&& ($def = ObjectModel::getDefinition('Product'))
							&& isset($def['fields'][$field]['shop'])
							&& $def['fields'][$field]['shop'])
						{
							$todoshop[]='`'.$field."`='".psql(html_entity_decode( Tools::getValue($field)))."'";
						}
					}
					if($field=='out_of_stock' && version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					{
						$shops = SCI::getSelectedShopActionList(false, intval($id_product));
						foreach ($shops as $shop)
							StockAvailable::setProductOutOfStock($id_product, psql(html_entity_decode( Tools::getValue($field))), (int)$shop, 0);
					}
					if($field!='location_warehouse')
						addToHistory('catalog_tree','modification',$field,intval($id_product),$id_lang,_DB_PREFIX_."product",psql(Tools::getValue($field)));
				}
			}
		}
		
		// force combinations update
		foreach($forceUpdateCombinations AS $field)
		{
			if (isset($_POST[$field]))
			{
				$doUpdateCombinationsOption=true;
			}
		}
		if (isset($_POST['combinations']) && substr($_POST['combinations'],0,13)=='combinations_')
		{
			$doHookUpdateQuantity = false;
			$doUpdateCombinationsOption=true;
			// get combination values
			$prefixlen=strlen('combinations_');
			$id_productsource=substr($_POST['combinations'],$prefixlen,strlen($_POST['combinations']));
			if ($id_productsource!=$id_product)
			{
				if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				{
					$sql="SELECT pas.id_product_attribute, pas.price, pas.weight, pa.unit_price_impact, pas.ecotax, pa.reference, pa.ean13, pas.default_on, pa.location, pa.upc, pas.minimal_quantity
								FROM "._DB_PREFIX_."product_attribute_shop pas
								LEFT JOIN "._DB_PREFIX_."product_attribute pa ON (pa.id_product_attribute=pas.id_product_attribute) 
								WHERE pa.id_product=".(int)$id_productsource."
								GROUP BY pa.id_product_attribute";
					$res=Db::getInstance()->ExecuteS($sql);
$dd=$sql;					
					$id_shop_list_array = Product::getShopsByProduct($id_productsource);
					$id_shop_list = array();
					foreach ($id_shop_list_array as $array_shop)
						$id_shop_list[] = $array_shop['id_shop'];
					
					$p=new Product($id_product);
					foreach($res as $key => $row)
					{
						$idco=$p->addAttribute($row['price'], $row['weight'], $row['unit_price_impact'], $row['ecotax'], 0, $row['reference'], $row['ean13'],
								 		$row['default_on'], $row['location'], $row['upc'], $row['minimal_quantity']);
						if ((int)$idco)
						{
							$sql="SELECT GROUP_CONCAT(id_attribute) AS ids FROM "._DB_PREFIX_.'product_attribute_combination WHERE id_product_attribute='.(int)$row['id_product_attribute'];
							$res=_qgv($sql);
							$ids_attribute=explode(',',$res);
							$combi=new Combination($idco);
							$combi->id_shop_list = $id_shop_list;
							$combi->setAttributes($ids_attribute);
							$combi->save();
						}
					}
				}else{
					$sqlinsert='';
					$cols=array('`id_product`');
					$sql="SELECT * FROM "._DB_PREFIX_.'product_attribute WHERE id_product='.(int)$id_productsource;
					$res=Db::getInstance()->ExecuteS($sql);
					foreach($res as $key => $row)
					{
						$sqlinsert='';
						foreach($row as $col => $val)
						{
							if ($col!='id_product' && $col!='id_product_attribute')
								$sqlinsert.='\''.psql($val).'\',';
							if ($col!='id_product' && $col!='id_product_attribute' && !sc_in_array('`'.$col.'`',$cols,"catProductUpdate_checkInCols"))
								$cols[]='`'.$col.'`';
						}
						$sql="INSERT INTO "._DB_PREFIX_."product_attribute (".join(',',$cols).") VALUES (".(int)$id_product.",".trim($sqlinsert,',').")";
						Db::getInstance()->Execute($sql);
						$newid=Db::getInstance()->Insert_ID();
						if ($newid)
						{
							$sql="SELECT GROUP_CONCAT(id_attribute) AS ids FROM "._DB_PREFIX_.'product_attribute_combination WHERE id_product_attribute='.(int)$row['id_product_attribute'];
							$res=_qgv($sql);
							$ids=explode(',',$res);
							if (count($ids))
							{
								$sql2="INSERT INTO "._DB_PREFIX_."product_attribute_combination (`id_attribute`,`id_product_attribute`) VALUES (".join(','.(int)$newid.'),(',$ids).",'".(int)$newid."')";
								Db::getInstance()->Execute($sql2);
							}
						}
					}
				}
			}
		}
		if (isset($_POST['quantityupdate']) || isset($_POST['quantity']))
		{
			$quantity=intval(Tools::getValue('quantity'));
			$quantityUpdate=intval(Tools::getValue('quantityupdate',0));
			if ($quantityUpdate!=0)
			{
				if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					$row=Db::getInstance()->getRow("SELECT quantity FROM "._DB_PREFIX_."stock_available WHERE id_product='".(int)$id_product."' AND id_product_attribute=0");
				else
					$row=Db::getInstance()->getRow("SELECT quantity FROM "._DB_PREFIX_."product WHERE id_product=".$id_product);
				$newQuantity = $row['quantity'] + $quantityUpdate;
				if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					foreach(SCI::getSelectedShopActionList(false, $id_product) AS $id_shop)
						SCI::updateQuantity($id_product, null, $quantityUpdate, $id_shop);
			}else{
				$newQuantity = $quantity;
				if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				{
					foreach(SCI::getSelectedShopActionList(false,$id_product) AS $id_shop)
					{
						SCI::setQuantity($id_product, null, $newQuantity, $id_shop);
					}
				}
			}
			$todo[]='`quantity`='.intval($newQuantity);

			if(_s("CAT_ACTIVE_HOOK_UPDATE_QUANTITY")=="1" && version_compare(_PS_VERSION_, '1.5.0.0', '<'))
			{
				$doHookUpdateQuantity = true;
			}
/*
			if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
			{
				$stockMvt = new StockMvt();
				$stockMvt->id_product = (int)$id_product;
				$stockMvt->id_product_attribute = 0;
				$stockMvt->id_order = 0;
				$stockMvt->id_employee = (int)$id_employee;
				$stockMvt->quantity = (int)$quantityUpdate;
				$stockMvt->id_stock_mvt_reason = (int)$id_reason;
				$stockMvt->add();
			}
*/
			addToHistory('catalog_tree','modification','quantity',intval($id_product),$id_lang,_DB_PREFIX_."product",intval($newQuantity));
		}
		if (isset($_POST['discountprice']) && version_compare(_PS_VERSION_, '1.4.0.0', '<'))
		{
			$sql = "DELETE FROM "._DB_PREFIX_."discount_quantity WHERE id_product=".intval($id_product);
			Db::getInstance()->Execute($sql);
			$dpList=explode('_',$_POST['discountprice']);
			foreach($dpList AS $dp)
			{
				$dp=str_replace(' ','',$dp);
				$val=explode(':',$dp);
				if (count($val)==2)
				{
					if (strpos($val[1],'%')!==false)
					{
						$type=1;
						$val[1]=trim($val[1],'%');
					}else{
						$type=2;
					}
					$sql = "INSERT INTO "._DB_PREFIX_."discount_quantity (id_discount_type,id_product,quantity,value,id_product_attribute) VALUES (".intval($type).",".intval($id_product).",".intval($val[0]).",".intval($val[1]).",0)";
					Db::getInstance()->Execute($sql);
				}
			}
			addToHistory('discount_quantity','modification','value',intval($id_product),$id_lang,_DB_PREFIX_."discount_quantity",'value');
		}
		if (isset($_POST['discountprice']) && version_compare(_PS_VERSION_, '1.4.0.0', '>='))
		{
			$sql = "DELETE FROM "._DB_PREFIX_."specific_price WHERE id_product=".intval($id_product)."";
			Db::getInstance()->Execute($sql);
			$dpList=explode('_',$_POST['discountprice']);
			foreach($dpList AS $dp)
			{
				$val=explode('|',$dp);
				if ((version_compare(_PS_VERSION_, '1.5.0.0', '<') && count($val)==8) || 
						(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && count($val)==10))
				{
					if (strpos($val[1],'%')!==false)
					{
						$type='percentage';
						$val[1]=floatval(trim($val[1],'%'))/100;
					}else{
						$type='amount';
					}
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					{
						$sql = "INSERT INTO `"._DB_PREFIX_."specific_price` (`reduction_type`,`id_product`,`from_quantity`,`reduction`,`price`,`from`,`to`,`id_group`,`id_country`,`id_currency`,`id_customer`,`id_product_attribute`,`id_shop_group`,`id_shop`) VALUES ('".psql($type)."',".intval($id_product).",".floatval($val[0]).",".floatval($val[1]).",".floatval($val[2]).",'".psql($val[3])."','".psql($val[4])."','".(int)($val[5])."','".(int)($val[6])."','".(int)($val[7])."',0,0,'".(int)($val[8])."','".(int)($val[9])."')";
						Db::getInstance()->Execute($sql);
					}else{
						$sql = "INSERT INTO `"._DB_PREFIX_."specific_price` (`reduction_type`,`id_product`,`from_quantity`,`reduction`,`price`,`from`,`to`,`id_group`,`id_country`,`id_currency`) VALUES ('".psql($type)."',".intval($id_product).",".floatval($val[0]).",".floatval($val[1]).",".floatval($val[2]).",'".psql($val[3])."','".psql($val[4])."','".(int)($val[5])."','".(int)($val[6])."','".(int)($val[7])."')";
						Db::getInstance()->Execute($sql);
					}
				}
			}
			addToHistory('specific_price','modification','value',intval($id_product),$id_lang,_DB_PREFIX_."specific_price",str_replace('_',"\n",$_POST['discountprice']),'');
		}
		foreach($fields_lang AS $field)
		{
			if (isset($_POST[$field]))
			{
				$value=psql(Tools::getValue($field),(sc_in_array($field,$fieldsWithHTML,"catProductUpdate_fieldsWithHTML")?true:false));
				if ($field=='name' && _s('CAT_SEO_NAME_TO_URL'))
				{
					$todo_lang[]="`link_rewrite`='".link_rewrite($value)."'";
				}
				$todo_lang[]="`".$field."`='".$value."'";
				addToHistory('catalog_tree','modification',$field,intval($id_product),$id_lang,_DB_PREFIX_."product_lang",$value);
			}
		}

		if (count($todo))
		{
			$todo[]='`date_upd`=NOW()';
			$sql = "UPDATE "._DB_PREFIX_."product SET ".join(' , ',$todo)." WHERE id_product=".intval($id_product);
//if ($debug) $dd.=$sql."\n";
			Db::getInstance()->Execute($sql);
			
			if($doHookUpdateQuantity && isset($newQuantity))
			{
				SCI::hookExec('actionUpdateQuantity',
				   array(
				   	'id_product' => $id_product,
				   	'id_product_attribute' => 0,
				   	'quantity' => $newQuantity
				   )
				);
			}
		}
		if (count($todo_lang))
		{
			
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				$sql = "UPDATE "._DB_PREFIX_."product_shop SET date_upd=NOW(),indexed=0 WHERE id_product=".intval($id_product)." AND id_shop=".(int)SCI::getSelectedShop();
			}elseif (version_compare(_PS_VERSION_, '1.2.0.1', '>='))
			{
				$sql = "UPDATE "._DB_PREFIX_."product SET date_upd=NOW(),indexed=0 WHERE id_product=".intval($id_product);
			}else{
				$sql = "UPDATE "._DB_PREFIX_."product SET date_upd=NOW() WHERE id_product=".intval($id_product);
			}
			Db::getInstance()->Execute($sql);
			$sql2 = "UPDATE "._DB_PREFIX_."product_lang SET ".join(' , ',$todo_lang)." WHERE id_product=".intval($id_product)." AND id_lang=".intval($id_lang);
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				$sql2 .= " AND id_shop IN (".psql(SCI::getSelectedShopActionList(true)).")";
if ($debug) $dd.=$sql2."\n";
			Db::getInstance()->Execute($sql2);
			if (isset($_POST['name']))
			{
				$sql3 = "UPDATE "._DB_PREFIX_."product_lang SET name='".pSQL($_POST['name'])."' WHERE id_product=".intval($id_product)." AND name='new'";
				if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					$sql3 .= " AND id_shop IN (".psql(SCI::getSelectedShopActionList(true)).")";
				Db::getInstance()->Execute($sql3);
			}
		}
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			if (isset($_POST['supplier_reference']))
			{
				$sql = "SELECT id_supplier FROM "._DB_PREFIX_."product WHERE id_product=".intval($id_product);
				$row = Db::getInstance()->getRow($sql);
				$id_supplier=(int)$row['id_supplier'];
//	public function addSupplierReference($id_supplier, $id_product_attribute, $supplier_reference = null, $price = null, $id_currency = null)
				if ($id_supplier > 0)
				{
					$id_product_supplier = (int)ProductSupplier::getIdByProductAndSupplier((int)$id_product, 0, (int)$id_supplier);
		
					if (!$id_product_supplier)
					{
						//create new record
						$product_supplier_entity = new ProductSupplier();
						$product_supplier_entity->id_product = (int)$id_product;
						$product_supplier_entity->id_product_attribute = 0;
						$product_supplier_entity->id_supplier = (int)$id_supplier;
						$product_supplier_entity->product_supplier_reference = psql($_POST['supplier_reference']);
						$product_supplier_entity->product_supplier_price_te = 0;
						$product_supplier_entity->id_currency = 0;
						$product_supplier_entity->save();
					}
					else
					{
						$product_supplier = new ProductSupplier((int)$id_product_supplier);
						$product_supplier->product_supplier_reference = psql($_POST['supplier_reference']);
						$product_supplier->update();
					}
				}
			}
			if (isset($_POST['id_supplier']))
			{
				$id_supplier=(int)$_POST['id_supplier'];
				if ($id_supplier > 0)
				{
					$id_product_supplier = (int)ProductSupplier::getIdByProductAndSupplier((int)$id_product, 0, (int)$id_supplier);
		
					if (!$id_product_supplier)
					{
						//create new record
						$product_supplier_entity = new ProductSupplier();
						$product_supplier_entity->id_product = (int)$id_product;
						$product_supplier_entity->id_product_attribute = 0;
						$product_supplier_entity->id_supplier = (int)$id_supplier;
						$product_supplier_entity->product_supplier_reference = '';
						$product_supplier_entity->product_supplier_price_te = 0;
						$product_supplier_entity->id_currency = 0;
						$product_supplier_entity->save();
					}
				}
			}
			/*$todo=array();
			$todo[]='`date_upd`=NOW()';
			$shopfields=array('id_tax_rules_group','id_category_default','active','on_sale','online_only','ecotax','minimal_quantity','price','wholesale_price',
												'unity','unit_price_ratio','additional_shipping_cost','available_for_order','available_date',
												'condition','show_price','visibility');
			foreach($shopfields AS $field)
				if (isset($_POST[$field]))
					$todo[]=psql($field)."='".psql($_POST[$field])."'";*/
//if ($debug) $dd.=count($todoshop)."\n";
			if (count($todoshop))
			{
				$sql = "UPDATE "._DB_PREFIX_."product_shop SET ".join(' , ',$todoshop)." WHERE id_product=".intval($id_product)." AND id_shop IN (".psql(SCI::getSelectedShopActionList(true)).")";
				//if ($debug) $dd.=$sql."\n";
				Db::getInstance()->Execute($sql);
			}
		}
		if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
		{
			$product=new Product(intval($id_product));
			SCI::hookExec('updateProduct', array('product' => $product));
		}elseif(_s('APP_COMPAT_EBAY')){
			Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'),intval($id_product)));
		}
		
		sc_ext::readCustomGridsConfigXML('onAfterUpdateSQL');
		
		$newId = $_POST["gr_id"];

	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="position"){

		$id_category=intval(Tools::getValue('id_category'));
		$todo=array();
		$row=explode(';',Tools::getValue('positions'));
		foreach($row AS $v)
		{
			if ($v!='')
			{
				$pos=explode(',',$v);
				$todo[]="UPDATE "._DB_PREFIX_."category_product SET position=".intval($pos[1])." WHERE id_category=".intval($id_category)." AND id_product=".intval($pos[0]);
			}
		}
		foreach($todo AS $task)
		{
			Db::getInstance()->Execute($task);
		}
		$sql=join("\n",$todo);
		die('ok');
	}

	sc_ext::readCustomGridsConfigXML('extraVars');

	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
	echo '<data>';
	echo "<action type='".$action."' sid='".$_POST["gr_id"]."' tid='".$newId."' ".$extraVars." quantity='".$newQuantity."' ".($doUpdateCombinationsOption ? "doUpdateCombinations='1'":"")." ".(isset($id_specific_price) && $id_specific_price ? "id_specific_price='".$id_specific_price."'":"")."/>";
	echo ($debug && isset($sql) ? '<sql><![CDATA['.$sql.']]></sql>'."\n":'');
	echo ($debug && isset($sql2) ? '<sql><![CDATA['.$sql2.']]></sql>'."\n":'');
	echo ($debug && isset($sql3) ? '<sql><![CDATA['.$sql3.']]></sql>'."\n":'');
	echo '<sql0><![CDATA[';
if ($debug) echo ($dd);
	echo ']]></sql0>';
	echo '</data>';
