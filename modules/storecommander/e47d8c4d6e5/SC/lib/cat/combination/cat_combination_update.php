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

	$gr_id = Tools::getValue('gr_id');
	$id_lang = Tools::getValue('id_lang','0');
	$action = Tools::getValue('action','');
	$callback = Tools::getValue('callback','');
	$error='';
	$return = "";
	if (substr($gr_id,0,3)=='NEW' && $action=="insert")
	{
		$newId = 0;
		$id_product=intval(Tools::getValue('id_product'));
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			$product = new Product($id_product, false, (int)$id_lang, (int)SCI::getSelectedShop());
		else
			$product = new Product($id_product);
		if (Validate::isLoadedObject($product))
		{
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				$has_small_num = false;
				
				$price = max(floatval(Tools::getValue('price',0)) - floatval(Tools::getValue('pprice',0)),0);
				$small_price = $price;
				if($price>0 && $price<0.001)
				{
					$price = 0;
					$has_small_num = true;
				}
				
				$weight = max(floatval(Tools::getValue('weight',0)) - floatval(Tools::getValue('pweight',0)),0);
				$small_weight = $weight;
				if($weight>0 && $weight<0.001)
				{
					$weight = 0;
					$has_small_num = true;
				}
				
				$id_product_attribute = $product->addAttribute(
						number_format( $price , 6, ".", ""),
						number_format( $weight ,6, ".", ""),
						0,
						0,
						0,
						Tools::getValue('reference'),
						Tools::getValue('ean13'),
						0,
						Tools::getValue('location'),
						Tools::getValue('upc'),
						1,
						SCI::getShopsByProduct($product->id)
						);
				
				if($has_small_num && (!empty($small_price) || !empty($small_weight)))
				{
					$set = "";
					
					if(!empty($small_price))
						$set .= ", price='".pSQL(number_format( $small_price , 6, ".", ""))."'";
					
					if(!empty($small_weight))
						$set .= ", weight='".pSQL(number_format( $small_weight , 6, ".", ""))."'";
					
					if(!empty($set))
					{
						$sql = "UPDATE "._DB_PREFIX_."product_attribute SET `date_upd`=NOW() ".$set." WHERE id_product_attribute=".intval($id_product_attribute);
						Db::getInstance()->Execute($sql);
						if(SCMS)
						{
							$shops = SCI::getShopsByProduct($product->id);
							foreach ($shops as $shop_id)
							{
								$sql = "UPDATE "._DB_PREFIX_."product_attribute_shop SET `date_upd`=NOW() ".$set." WHERE id_shop = ".(int)$shop_id." AND id_product_attribute=".intval($id_product_attribute);
								Db::getInstance()->Execute($sql);
							}
						}
					}
				}

				$qty = Tools::getValue('quantity',0);
				if($qty==0)
					$qty = _s("CAT_PROD_COMBI_CREA_QTY");
				
				SCI::setQuantity($product->id, $id_product_attribute, $qty, SCI::getShopsByProduct($product->id));
				$combination = new Combination((int)$id_product_attribute);
				$combination->id_product=$product->id;
				$combination->minimal_quantity=max(1,(int)$combination->minimal_quantity);
				$combination->id_shop_list=SCI::getShopsByProduct($product->id);
				//$combination->setAttributes($attribute_combinaison_list);
				$combination->save();

				if(_s("CAT_APPLY_ALL_CART_RULES"))
					SpecificPriceRule::applyAllRules(array((int)$product->id));
				
				if(SCAS && $product->advanced_stock_management=="1")
				{
					$row = Db::getInstance()->getRow('
					SELECT pa.id_product_attribute
					FROM `'._DB_PREFIX_.'product_attribute` pa
					'.Shop::addSqlAssociation('product_attribute', 'pa').'
					WHERE product_attribute_shop.`default_on` = 1
						AND pa.`id_product` = '.(int)$product->id
							);
					if (!empty($row["id_product_attribute"]))
					{
						$sql = 'SELECT DISTINCT(id_warehouse) as id_warehouse
								FROM `'._DB_PREFIX_.'warehouse_product_location`
								WHERE id_product_attribute = "'.(int)$row["id_product_attribute"].'"';
					}
					else
					{
						$sql = 'SELECT DISTINCT(id_warehouse) as id_warehouse
								FROM `'._DB_PREFIX_.'warehouse_product_location`
								WHERE id_product = "'.(int)$product->id.'"
									AND id_product_attribute="0"';
					}
					$warehouses = Db::getInstance()->executeS($sql);
					if(!empty($warehouses) && count($warehouses)>0)
					{	
						foreach($warehouses as $warehouse)
						{
							if(!empty($warehouse["id_warehouse"]))
							{
								Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'warehouse_product_location (id_product, id_product_attribute, id_warehouse)
								VALUES ("'.(int)$product->id.'","'.(int)$id_product_attribute.'","'.(int)$warehouse["id_warehouse"].'")');
							}
						}
					}
					
					Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'warehouse_product_location WHERE id_product = "'.(int)$product->id.'" AND id_product_attribute="0"');
				}
			}else{
				if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
				{
					$has_small_num = false;
					
					$price = max(floatval(Tools::getValue('price',0)) - floatval(Tools::getValue('pprice',0)),0);
					$small_price = $price;
					if($price>0 && $price<0.001)
					{
						$price = 0;
						$has_small_num = true;
					}
					
					$weight = max(floatval(Tools::getValue('weight',0)) - floatval(Tools::getValue('pweight',0)),0);
					$small_weight = $weight;
					if($weight>0 && $weight<0.001)
					{
						$weight = 0;
						$has_small_num = true;
					}
					
					$qty = Tools::getValue('quantity',0);
					if($qty==0)
						$qty = _s("CAT_PROD_COMBI_CREA_QTY");
					
					$id_product_attribute = $product->addProductAttribute( 
								number_format( $price , 6, ".", ""),
						number_format( $weight ,6, ".", ""),
	                            0, 
	                            0,
	                            $qty,	
	                            '', 
	                            Tools::getValue('reference'), 
	                            Tools::getValue('supplier_reference'), 
	                            Tools::getValue('ean13'), 
	                            0,
	                            Tools::getValue('location'));
				
					if($has_small_num && (!empty($small_price) || !empty($small_weight)))
					{
						$set = "";
						
						if(!empty($small_price))
							$set .= ", price='".pSQL(number_format( $small_price , 6, ".", ""))."'";
						
						if(!empty($small_weight))
							$set .= ", weight='".pSQL(number_format( $small_weight , 6, ".", ""))."'";
						
						if(!empty($set))
						{
							$sql = "UPDATE "._DB_PREFIX_."product_attribute SET `date_upd`=NOW() ".$set." WHERE id_product_attribute=".intval($id_product_attribute);
							Db::getInstance()->Execute($sql);
						}
					}
				}else{
					
					$qty = Tools::getValue('quantity',0);
					if($qty==0)
						$qty = _s("CAT_PROD_COMBI_CREA_QTY");
					
					$id_product_attribute = $product->addProductAttribute( 
															round(max(floatval(Tools::getValue('price')) - floatval(Tools::getValue('pprice')),0),6),
	                            round(max(floatval(Tools::getValue('weight')) - floatval(Tools::getValue('pweight')),0),6), 
	                            0, 
	                            $qty,	
	                            '', 
	                            Tools::getValue('reference'), 
	                            Tools::getValue('supplier_reference'), 
	                            Tools::getValue('ean13'), 
	                            0,
	                            Tools::getValue('location'));
				}
				//$product->addAttributeCombinaison($id_product_attribute, $attribute_combinaison_list);
			}
			$product->checkDefaultAttributes();
			$sql = "UPDATE "._DB_PREFIX_."product_attribute SET `date_upd`=NOW() WHERE id_product_attribute=".intval($id_product_attribute);
			Db::getInstance()->Execute($sql);
			$newId = $id_product_attribute;

			if(!empty($id_product))
				ExtensionPMCM::clearFromIdsProduct($id_product);

			// RETURN
			if(!empty($newId))
			{
				$callback = str_replace("{newid}", $newId, $callback) ;
				$return = json_encode(array("callback"=>$callback));
			}
		}
		else
			$error='Product not found';
	}

	sc_ext::readCustomCombinationsGridConfigXML('extraVars');
	
if(empty($return))
	$error = "ERROR: Try again";

if(!empty($error))
	$return = $error;
	
echo $return;
