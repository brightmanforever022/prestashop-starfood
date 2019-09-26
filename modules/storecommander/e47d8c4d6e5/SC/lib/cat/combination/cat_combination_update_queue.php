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

@error_reporting(E_ERROR | E_PARSE);
@ini_set("display_errors", "ON");

$id_lang = Tools::getValue('id_lang','0');

$return = "ERROR: Try again later";

// FUNCTIONS
$updated_products = array();
function checkDefaultAttributes($id_product)
{
	$row = Db::getInstance()->getRow('
							SELECT id_product, id_product_attribute
							FROM `'._DB_PREFIX_.'product_attribute`
							WHERE `default_on` = 1 AND `id_product` = '.(int)($id_product));
	if ($row)
		return (int)($row['id_product_attribute']);
		
	$mini = Db::getInstance()->getRow('
							SELECT MIN(pa.id_product_attribute) as `id_attr`
							FROM `'._DB_PREFIX_.'product_attribute` pa
							WHERE `id_product` = '.(int)($id_product));
	if (!$mini)
		return 0;
		
	if (!Db::getInstance()->Execute('
								UPDATE `'._DB_PREFIX_.'product_attribute`
								SET `default_on` = 1
								WHERE `id_product_attribute` = '.(int)($mini['id_attr'])))
		return 0;
	return (int)($mini['id_attr']);
}

// Récupération de toutes les modifications à effectuer
if(!empty($_POST["rows"]))
{
	if(_PS_MAGIC_QUOTES_GPC_)
		$_POST["rows"] = stripslashes($_POST["rows"]);
	$rows = json_decode($_POST["rows"]);
	
	if(is_array($rows) && count($rows)>0)
	{
		$callbacks = '';
		
		// Première boucle pour remplir la table sc_queue_log 
		// avec toutes ces modifications
		$log_ids = array();
		$date = date("Y-m-d H:i:s");
		foreach($rows as $num => $row)
		{
			$id = QueueLog::add($row->name, $row->row, $row->action, (!empty($row->params)?$row->params:array()), (!empty($row->callback)?$row->callback:null), $date);
			$log_ids[$num] = $id;
		}
		
		// Deuxième boucle pour effectuer les 
		// actions les une après les autres
		foreach($rows as $num => $row)
		{
			if(!empty($log_ids[$num]))
			{
				$gr_id = intval($row->row);
				$action = $row->action;
				
				if(!empty($row->callback))
					$callbacks .= $row->callback.";";

				$_POST=array();
				$_POST = (array) json_decode($row->params);
				
				if(!empty($action) && $action=="delete" && !empty($gr_id))
				{
					$idpa_array=explode(',',Tools::getValue('id_product_attribute','0'));
					$id_product=Tools::getValue('id_product','0');
					if(!empty($id_product))
					{
						$updated_products[$id_product]=$id_product;
						$TODO="UPDATE "._DB_PREFIX_."product SET `date_upd`='".psql(date("Y-m-d H:i:s"))."' WHERE id_product=".intval($id_product).";";
						if(SCMS)
							$TODO.="UPDATE "._DB_PREFIX_."product_shop SET `date_upd`='".psql(date("Y-m-d H:i:s"))."' WHERE id_product=".intval($id_product)." AND id_shop IN (".psql(SCI::getSelectedShopActionList(true)).");";
						Db::getInstance()->Execute($TODO);
						
						if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
						{
                            $shops = Shop::getShops(false, null, true);

                            $p = new Product($id_product, false, (int)$id_lang, (int)SCI::getSelectedShop());
							foreach($idpa_array AS $id_product_attribute)
							{
								if (is_numeric($id_product_attribute) && $id_product_attribute)
								{
									$c = new Combination($id_product_attribute);
                                    $c->id_shop_list = Shop::getShops(false, null, true);
									$c->delete();
									foreach($shops as $shop)
									{
										StockAvailable::removeProductFromStockAvailable(intval($id_product), intval($id_product_attribute), $shop);
									}
									
									$sql = "SELECT * FROM "._DB_PREFIX_."stock WHERE id_product_attribute = '".intval($id_product_attribute)."' ";
									$stocks = Db::getInstance()->ExecuteS($sql);
									foreach($stocks as $stock)
									{
										$sql="DELETE FROM "._DB_PREFIX_."stock_mvt WHERE id_stock='".intval($stock["id_stock"])."'";
										Db::getInstance()->Execute($sql);
									}
									$sql="DELETE FROM "._DB_PREFIX_."stock WHERE id_product_attribute = '".intval($id_product_attribute)."' ";
									Db::getInstance()->Execute($sql);
									$sql="DELETE FROM "._DB_PREFIX_."warehouse_product_location WHERE id_product_attribute = '".intval($id_product_attribute)."' ";
									Db::getInstance()->Execute($sql);
								}
							}
						
							$p->checkDefaultAttributes();
							if (!$p->hasAttributes())
							{
								/*$p->cache_default_attribute = 0;
								$p->update();*/
								$sql="UPDATE "._DB_PREFIX_."product SET cache_default_attribute='0' WHERE id_product='".intval($id_product)."'";
								Db::getInstance()->Execute($sql);
								$sql="UPDATE "._DB_PREFIX_."product_shop SET cache_default_attribute='0' WHERE id_product='".intval($id_product)."' AND id_shop IN (".SCI::getSelectedShopActionList(true).") ";
								Db::getInstance()->Execute($sql);
							}
							else
							{
								if(SCMS)
								{
									$id_default_attribute = (int)Product::getDefaultAttribute($id_product);
									$shops = implode(",", Shop::getShops(false, null, true));
									
									$result =  Db::getInstance()->update('product_shop', array(
											'cache_default_attribute' => $id_default_attribute,
									), 'id_product = '.(int)$id_product." AND id_shop IN (".pSQL($shops).") ");
									
									$sql="UPDATE "._DB_PREFIX_."product_attribute_shop SET default_on='1' WHERE `id_product_attribute` = '".(int)$id_default_attribute."' AND id_shop IN (".pSQL($shops).") ";
									Db::getInstance()->Execute($sql);
									
									$result &=  Db::getInstance()->update('product', array(
											'cache_default_attribute' => $id_default_attribute,
									), 'id_product = '.(int)$id_product);
								}
								else
									Product::updateDefaultAttribute(intval($id_product));
							}
						
							SCI::qtySumStockAvailable($id_product);
						}else{
						
							foreach($idpa_array AS $id_product_attribute)
							{
								if (is_numeric($id_product_attribute))
								{
									$sql="DELETE FROM "._DB_PREFIX_."product_attribute WHERE id_product_attribute='".intval($id_product_attribute)."'";
									Db::getInstance()->Execute($sql);
									$sql="DELETE FROM "._DB_PREFIX_."product_attribute_combination WHERE id_product_attribute='".intval($id_product_attribute)."'";
									Db::getInstance()->Execute($sql);
									$sql='DELETE FROM `'._DB_PREFIX_.'cart_product` WHERE `id_product_attribute` = '.intval($id_product_attribute);
									Db::getInstance()->Execute($sql);
									if (version_compare(_PS_VERSION_, '1.2.0.1', '>='))
									{
										$sql="DELETE FROM "._DB_PREFIX_."product_attribute_image WHERE id_product_attribute='".intval($id_product_attribute)."'";
										Db::getInstance()->Execute($sql);
									}
									if (version_compare(_PS_VERSION_, '1.4.0.0', '>=') && _s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
									{
										SCI::hookExec('deleteProductAttribute', array('id_product_attribute' => intval($id_product_attribute), 'id_product' => intval($id_product), 'deleteAllAttributes' => false));
									}elseif(_s('APP_COMPAT_EBAY')){
										Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'),intval($id_product)));
									}
								}
							}
						
							$default_id=checkDefaultAttributes(intval($id_product));
						
							if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
							{
								Db::getInstance()->Execute('
									UPDATE `'._DB_PREFIX_.'product`
									SET `cache_default_attribute` ='.intval($default_id).'
									WHERE `id_product` = '.intval($id_product));
							}
							if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
							{
								Db::getInstance()->Execute('
									UPDATE `'._DB_PREFIX_.'product_shop`
									SET `cache_default_attribute` ='.intval($default_id).'
									WHERE `id_product` = '.intval($id_product).' AND id_shop IN ('.SCI::getSelectedShopActionList(true,$id_product).')');
							}
						
						
							Db::getInstance()->Execute('
								UPDATE `'._DB_PREFIX_.'product`
								SET `quantity` =
									(
									SELECT SUM(`quantity`)
									FROM `'._DB_PREFIX_.'product_attribute`
									WHERE `id_product` = '.intval($id_product).'
									)
								WHERE `id_product` = '.intval($id_product));
						}
						if(_s("CAT_APPLY_ALL_CART_RULES"))
							SpecificPriceRule::applyAllRules(array((int)$id_product));
					}
				}
				elseif(!empty($action) && $action=="update" && !empty($gr_id))
				{
					$id_product=intval(Tools::getValue('id_product'));
					
					if(!empty($id_product))
					{
						$updated_products[$id_product]=$id_product;
						$TODO="UPDATE "._DB_PREFIX_."product SET `date_upd`='".psql(date("Y-m-d H:i:s"))."' WHERE id_product=".intval($id_product).";";
						if(SCMS)
							$TODO.="UPDATE "._DB_PREFIX_."product_shop SET `date_upd`='".psql(date("Y-m-d H:i:s"))."' WHERE id_product=".intval($id_product)." AND id_shop IN (".psql(SCI::getSelectedShopActionList(true)).");";
						Db::getInstance()->Execute($TODO);
						
						$doHookUpdateQuantity = false;
						$ecotaxrate=SCI::getEcotaxTaxRate();
						$id_product_attribute=$gr_id;
						$fields=array('reference','supplier_reference','ean13','upc','location','default_on','wholesale_price','minimal_quantity','unit_price_impact','available_date','sc_active');
                        if(version_compare(_PS_VERSION_, '1.7.0.0', '>='))
                            $fields[] = "isbn";
						$shopfields=array('wholesale_price','unit_price_impact','default_on','minimal_quantity','available_date');
						$updated_field=(Tools::getValue('updated_field'));
						if($updated_field=="price")
							$updated_field="priceextax";
						$todo=array();
						$shoptodo=array(); // used for actions in PS 1.5
						sc_ext::readCustomCombinationsGridConfigXML('updateSettings');
						foreach($fields AS $field)
						{
							if (isset($_POST[$field]) && $updated_field==$field)
							{
								if(version_compare(_PS_VERSION_, '1.6.1', '>=') && $field=="default_on")
								{
									$val = Tools::getValue($field);
									if(empty($val))
										$val = "NULL";
									else
										$val = "'".intval($val)."'";
									$todo[]='`'.$field."`=".$val;
								}
								else
									$todo[]='`'.$field."`='".psql(Tools::getValue($field))."'";
								addToHistory('cat_prop_attr','modification',$field,intval($id_product_attribute),0,_DB_PREFIX_."product_attribute",psql(Tools::getValue($field)));
							}
						}
						if (version_compare(_PS_VERSION_, '1.4.0.0', '>=') && $updated_field=="default_on" && isset($_POST['default_on']) && intval($_POST['default_on'])==1)
						{
							$p=new Product($id_product);
							$p->deleteDefaultAttributes();
							$p->setDefaultAttribute($id_product_attribute);
						}
						if ((isset($_POST['quantityupdate']) || isset($_POST['quantity'])) && ($updated_field=="quantityupdate" || $updated_field=="quantity"))
						{
							$quantity=intval(Tools::getValue('quantity'));
							$quantityUpdate=intval(Tools::getValue('quantityupdate',0));
							if ($quantityUpdate!=0)
							{
								if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
								{
									foreach(SCI::getSelectedShopActionList(false, $id_product) AS $id_shop)
									{
										$exist = StockAvailable::getStockAvailableIdByProductId((int)$id_product, (int)$id_product_attribute, (int)$id_shop);
										if(!empty($exist))
											SCI::updateQuantity((int)$id_product, (int)$id_product_attribute, $quantityUpdate, (int)$id_shop);
										else
											StockAvailable::setQuantity((int)$id_product, (int)$id_product_attribute, $quantityUpdate, (int)$id_shop);
									}
									//SCI::qtySumStockAvailable((int)$id_product);
								}
								$newQuantity = $quantity;
							}else{
								$newQuantity = $quantity;
								if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
									foreach(SCI::getSelectedShopActionList(false, $id_product) AS $id_shop)
									SCI::setQuantity($id_product, $id_product_attribute, $newQuantity, $id_shop);
							}
							$todo[]='`quantity`='.intval($newQuantity);
						
							if(_s("CAT_ACTIVE_HOOK_UPDATE_QUANTITY")=="1" && version_compare(_PS_VERSION_, '1.5.0.0', '<'))
							{
								$doHookUpdateQuantity = true;
							}
							addToHistory('cat_prop_attr','modification','quantity',intval($id_product_attribute),0,_DB_PREFIX_."product_attribute",intval($newQuantity));
						}
						if ((isset($_POST['price']) || isset($_POST['priceextax'])) && ($updated_field=="price" || $updated_field=="priceextax")) // need tax rate?
						{
							$sql='SELECT t.rate,p.price,p.weight FROM `'._DB_PREFIX_.'product` p, `'._DB_PREFIX_.'tax` t WHERE p.id_product='.intval($id_product).' AND t.id_tax=p.id_tax';
							if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
								$sql='SELECT t.rate,p.price,p.weight
										FROM `'._DB_PREFIX_.'product` p
										LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
								    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
										WHERE p.id_product='.intval($id_product);
							$p=Db::getInstance()->getRow($sql);
							$taxrate=$p['rate']/100+1;
						}
						if (isset($_POST['priceextax']) && $updated_field=="priceextax") // excluding tax should be placed before including taxe for price round.
						{
							if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
							{
								$todo[]="`price`='".((floatval($_POST["priceextax"])-(floatval($_POST["productprice"]))))."'";
								$shoptodo[]="`price`='".((floatval($_POST["priceextax"])-(floatval($_POST["productprice"]))))."'";
							}elseif(!isset($_POST['price'])){ // priority to TTC value for old PS versions
								$todo[]="`price`='".((floatval($_POST["priceextax"])-(floatval($_POST["productprice"])))*$taxrate)."'";
							}
							addToHistory('cat_prop_attr','modification','price',intval($id_product_attribute),0,_DB_PREFIX_."product_attribute",(floatval($_POST["priceextax"])-(floatval($_POST["productprice"]))));
						}
						if (isset($_POST['ecotax']) && $updated_field=="ecotax" && isset($_POST['ecotaxentered']) && $_POST['ecotaxentered']==1)
						{
							if (version_compare(_PS_VERSION_, '1.3.0.0', '>='))
							{
								$todo[]="`ecotax`='".(floatval($_POST["ecotax"])/$ecotaxrate)."'";
								if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
									$shoptodo[]="`ecotax`='".(floatval($_POST["ecotax"])/$ecotaxrate)."'";
							}
							else
								$todo[]="`ecotax`='".floatval($_POST["ecotax"])."'";
							addToHistory('cat_prop_attr','modification','ecotax',intval($id_product_attribute),0,_DB_PREFIX_."product_attribute",floatval($_POST["ecotax"]));
						}
						if (isset($_POST['price']) && $updated_field=="price" && version_compare(_PS_VERSION_, '1.4.0.0', '<'))
						{
							//$todo[]="`price`='".(floatval($_POST["price"])-(floatval($_POST["productpriceinctax"]-(_s('CAT_PROD_ECOTAXINCLUDED') && isset($_POST["pecotax"]) ? floatval($_POST["pecotax"]):0))))."'";
							$ecotax = (_s('CAT_PROD_ECOTAXINCLUDED') && isset($_POST["ecotax"]) ? floatval($_POST["ecotax"]):0);
							$pecotax = (_s('CAT_PROD_ECOTAXINCLUDED') && isset($_POST["productecotax"]) ? floatval($_POST["productecotax"]*$ecotaxrate):0);
							$todo[]="`price`='".floatval((floatval($_POST["price"])-$ecotax)-(floatval($_POST["productpriceinctax"])-$pecotax))."'";
							addToHistory('cat_prop_attr','modification','price',intval($id_product_attribute),0,_DB_PREFIX_."product_attribute",(floatval($_POST["price"])-(floatval($_POST["productpriceinctax"]))));
						}
						if (isset($_POST['weight']) && $updated_field=="weight")
						{
							$todo[]="`weight`='".(floatval($_POST["weight"])-(floatval($_POST["pweight"])))."'";
							$shoptodo[]="`weight`='".(floatval($_POST["weight"])-(floatval($_POST["pweight"])))."'";
							addToHistory('cat_prop_attr','modification','weight',intval($id_product_attribute),0,_DB_PREFIX_."product_attribute",(floatval($_POST["weight"])-(floatval($_POST["pweight"]))));
						}
						if (isset($_POST['available_later']) && $updated_field=="available_later" && SCI::getConfigurationValue("SC_DELIVERYDATE_INSTALLED")=="1")
						{
							if(!empty($_POST['available_later']))
							{
								$sql = "SELECT id_sc_available_later FROM "._DB_PREFIX_."sc_available_later WHERE available_later='".pSQL($_POST['available_later'])."' AND id_lang='".(int)$id_lang."'";
								$find_available_later=Db::getInstance()->ExecuteS($sql);
								if(!empty($find_available_later[0]["id_sc_available_later"]))
									$_POST['available_later'] = $find_available_later[0]["id_sc_available_later"];
								else
								{
									$sql = "INSERT INTO "._DB_PREFIX_."sc_available_later (id_lang, available_later) VALUES ('".(int)$id_lang."', '".pSQL($_POST['available_later'])."')";
									Db::getInstance()->Execute($sql);
									$_POST['available_later'] = Db::getInstance()->Insert_ID();
								}
							}
							else
								$_POST['available_later'] = 0;
								
							$todo[]="`id_sc_available_later`='".intval($_POST['available_later'])."'";
						}
						if (count($todo))
						{
							$sql = "UPDATE "._DB_PREFIX_."product SET `date_upd`=NOW() WHERE id_product=".intval($id_product);
							Db::getInstance()->Execute($sql);
							$sql = "UPDATE "._DB_PREFIX_."product_attribute SET `date_upd`=NOW(),".join(' , ',$todo)." WHERE id_product_attribute=".intval($id_product_attribute);
							Db::getInstance()->Execute($sql);
								
							if($doHookUpdateQuantity && isset($newQuantity))
							{
								if (!_s('APP_COMPAT_EBAY'))
								{
									if (_s('APP_COMPAT_HOOK'))
										SCI::hookExec('actionUpdateQuantity',
											array(
											'id_product' => $id_product,
											'id_product_attribute' => $id_product_attribute,
											'quantity' => $newQuantity
											)
										);
								}
							}
						}
						
						if ((isset($_POST['quantityupdate']) || isset($_POST['quantity'])) && ($updated_field=="quantityupdate" || $updated_field=="quantity"))
						{
							Db::getInstance()->Execute('
							UPDATE `'._DB_PREFIX_.'product`
							SET `quantity` =
								(
								SELECT SUM(`quantity`)
								FROM `'._DB_PREFIX_.'product_attribute`
								WHERE `id_product` = '.intval($id_product).'
								)
							WHERE `id_product` = '.intval($id_product));
						}
						if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
						{
							foreach($shopfields AS $field)
							{
								if (isset($_POST[$field]) && $updated_field==$field)
								{
									if(version_compare(_PS_VERSION_, '1.6.1', '>=') && $field=="default_on")
									{
										$val = $_POST[$field];
										if(empty($val))
											$val = "NULL";
										else
											$val = "'".intval($val)."'";
										$shoptodo[]='`'.$field."`=".$val;
									}
									else
										$shoptodo[]=psql($field)."='".psql($_POST[$field])."'";
								}
							}
							if (count($shoptodo))
							{
								$sql = "UPDATE "._DB_PREFIX_."product_attribute_shop SET ".join(' , ',$shoptodo)." WHERE id_product_attribute=".intval($id_product_attribute)." AND id_shop IN (".psql(SCI::getSelectedShopActionList(true)).")";
								Db::getInstance()->Execute($sql);
							}
							if (isset($_POST['supplier_reference']) && $updated_field=="supplier_reference")
							{
								$sql = "SELECT id_supplier FROM "._DB_PREFIX_."product WHERE id_product=".intval($id_product);
								$row = Db::getInstance()->getRow($sql);
								$id_supplier=(int)$row['id_supplier'];
								//	public function addSupplierReference($id_supplier, $id_product_attribute, $supplier_reference = null, $price = null, $id_currency = null)
								if ($id_supplier > 0)
								{
									$id_product_supplier = (int)ProductSupplier::getIdByProductAndSupplier((int)$id_product, (int)$id_product_attribute, (int)$id_supplier);
						
									if (!$id_product_supplier)
									{
										//create new record
										$product_supplier_entity = new ProductSupplier();
										$product_supplier_entity->id_product = (int)$id_product;
										$product_supplier_entity->id_product_attribute = (int)$id_product_attribute;
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
							if (isset($_POST['wholesale_price']) && $updated_field=="wholesale_price")
							{
								$sql = "SELECT id_supplier FROM "._DB_PREFIX_."product WHERE id_product=".intval($id_product);
								$row = Db::getInstance()->getRow($sql);
								$id_supplier=(int)$row['id_supplier'];
								//	public function addSupplierReference($id_supplier, $id_product_attribute, $supplier_reference = null, $price = null, $id_currency = null)
								if ($id_supplier > 0)
								{
									$id_product_supplier = (int)ProductSupplier::getIdByProductAndSupplier((int)$id_product, (int)$id_product_attribute, (int)$id_supplier);
						
									if (!$id_product_supplier)
									{
										//create new record
										$product_supplier_entity = new ProductSupplier();
										$product_supplier_entity->id_product = (int)$id_product;
										$product_supplier_entity->id_product_attribute = (int)$id_product_attribute;
										$product_supplier_entity->id_supplier = (int)$id_supplier;
										$product_supplier_entity->product_supplier_price_te = psql($_POST['wholesale_price']);
										$product_supplier_entity->id_currency = 0;
										$product_supplier_entity->save();
									}
									else
									{
										$product_supplier = new ProductSupplier((int)$id_product_supplier);
										$product_supplier->product_supplier_price_te = psql($_POST['wholesale_price']);
										$product_supplier->update();
									}
								}
							}
						}
						
						$deleted = false;
						foreach ($_POST as $key=>$value)
						{
							$sub = substr($key, 0, 5);
							if($sub=="attr_" && $key!="attr_ids")
							{
								if(!$deleted)
								{
									$sql = "DELETE FROM "._DB_PREFIX_."product_attribute_combination WHERE id_product_attribute='".(int)$id_product_attribute."'";
									Db::getInstance()->Execute($sql);
									$deleted=true;
								}
						
								if(!is_numeric($value))
								{
									$exp = explode("|||", $value);
									if(!empty($exp[1]))
										$value = $exp[1];
								}
								else 
									$value = '';
								if(!empty($value))
								{
									$sql = "INSERT INTO "._DB_PREFIX_."product_attribute_combination (id_product_attribute, id_attribute)
								VALUES ('".(int)$id_product_attribute."','".(int)$value."')";
									Db::getInstance()->Execute($sql);
								}
							}
						}
						
						if (!_s('APP_COMPAT_EBAY'))
						{
							if (_s('APP_COMPAT_HOOK'))
								SCI::hookExec('updateProductAttribute', array('id_product_attribute' => intval($id_product_attribute),'product'=>new Product(intval($id_product))));
						}elseif(_s('APP_COMPAT_EBAY')){
							Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'),intval($id_product)));
						}
						
						sc_ext::readCustomCombinationsGridConfigXML('onAfterUpdateSQL');
					}
				}

				sc_ext::readCustomCombinationsGridConfigXML('extraVars');
				
				QueueLog::delete(($log_ids[$num]));
			}
		}

		// PM Cache
		if(!empty($updated_products))
			ExtensionPMCM::clearFromIdsProduct($updated_products);
		
		// RETURN
		$return = json_encode(array("callback"=>$callbacks));
	}	
}
echo $return;
