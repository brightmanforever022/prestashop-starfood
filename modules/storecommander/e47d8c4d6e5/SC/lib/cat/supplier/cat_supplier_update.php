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
$id_supplier=Tools::getValue('id_supplier','0');
$value=Tools::getValue('value','0');

$multiple = false;
if(strpos($idlist, ",") !== false)
	$multiple = true;

$ids = explode(",", $idlist);

if($action!='' && !empty($id_supplier) && !empty($idlist)/* && !empty($id_actual_supplier)*/)
{
	switch($action)
	{
		case 'fields':
			$field=Tools::getValue('field','');
			foreach($ids as $id)
			{
				if(isset($value))
				{
					$sql = '
						SELECT *
						FROM `'._DB_PREFIX_.'product_supplier` ps
						WHERE ps.`id_supplier` = "'.(int)$id_supplier.'"
						AND ps.`id_product` = "'.(int)$id.'"
						AND ps.`id_product_attribute` = 0';
					$check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
					if(empty($check_in_supplier[0]["id_product_supplier"]))
					{
						$new = new ProductSupplier();
						$new->id_product = (int)$id;
						$new->id_supplier = (int)$id_supplier;
						$new->id_product_attribute = 0;
						$new->$field = $value;
						$new->save();
					}
					else
					{
						$new = new ProductSupplier($check_in_supplier[0]["id_product_supplier"]);
						$new->$field = $value;
						$new->save();
					}
					
					$product = new Product((int)$id, false, (int)$id_lang, (int)SCI::getSelectedShop());
					// Si pas de fournisseur par défaut 
					if(empty($product->id_supplier))
					{
						// on le met en défaut
						$product->id_supplier = (int)$id_supplier;
							// Si ref non vide
							if(!empty($new->product_supplier_reference))
								$product->supplier_reference = $new->product_supplier_reference;
							// Si ref par défaut non vide et que ref vide
							elseif(!empty($product->supplier_reference) && empty($new->product_supplier_reference))
							{
								$new->product_supplier_reference = $product->supplier_reference;
								$new->save();
							}
							
							// Si prix d'achat vide
							if(empty($product->wholesale_price) && !empty($new->product_supplier_price_te))
								$product->wholesale_price = $new->product_supplier_price_te;
							// Si prix d'achat par défaut non vide et que prix d'achat vide
							elseif(!empty($product->wholesale_price) && empty($new->product_supplier_price_te))
							{
								$new->product_supplier_price_te = $product->wholesale_price;
								$new->save();
							}
						$product->save();
					}
					else
					{
						// Si le champs modifié est la reference
						// et que fournisseur par défaut
						// on remplace la valeur par défaut 
						// par la nouvelle référence
						if($field=="product_supplier_reference" && $product->id_supplier==$id_supplier)
						{
							$product->supplier_reference = $new->product_supplier_reference;
							$product->save();
						}
						// Si le champs modifié est le prix d'achat
						// et que fournisseur par défaut
						// on remplace la valeur par défaut
						// par la nouvelle référence
						if($field=="product_supplier_price_te" && $product->id_supplier==$id_supplier)
						{
							$product->wholesale_price = $new->product_supplier_price_te;
							$product->save();
						}
					}
				}
			}
		break;
		case 'present':
			if($value=="true")
				$value = 1;
			else
				$value = 0;
			
			foreach($ids as $id)
			{
				$product = new Product((int)$id, false, (int)$id_lang, (int)SCI::getSelectedShop());
				if($value=="1")
				{
					$sql = '
						SELECT *
						FROM `'._DB_PREFIX_.'product_supplier` ps
						WHERE ps.`id_supplier` = "'.(int)$id_supplier.'"
						AND ps.`id_product` = "'.(int)$id.'"
						AND ps.`id_product_attribute` = 0';
					$check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
					if(empty($check_in_supplier[0]["id_product_supplier"]))
					{
						$new = new ProductSupplier();
						$new->id_product = (int)$id;
						$new->id_supplier = (int)$id_supplier;
						$new->id_product_attribute = 0;
						$new->save();
					}
                    else
                    {
                        $new = new ProductSupplier($check_in_supplier[0]["id_product_supplier"]);
                        $new->save();
                    }

					// Si pas de fournisseur par défaut
					if(empty($product->id_supplier))
					{
						// on le met en défaut
						$product->id_supplier = (int)$id_supplier;
							// Si ref par défaut non vide et que ref vide
							if(!empty($product->supplier_reference) && empty($new->product_supplier_reference))
							{
								$new->product_supplier_reference = $product->supplier_reference;
								$new->save();
							}
							// Si prix d'achat par défaut non vide et que prix d'achat vide
							if(!empty($product->wholesale_price) && empty($new->product_supplier_price_te))
							{
								$new->product_supplier_price_te = $product->wholesale_price;
								$new->save();
							}
						$product->save();
					}
					else
					{
						// Si ce fournisseur est le fournisseur par défaut
						// mais qu'il n'était pas présent
						// on lui met la référence par défaut
						if(!empty($product->supplier_reference) && empty($new->product_supplier_reference) && $product->id_supplier==$id_supplier)
						{
							$new->product_supplier_reference = $product->supplier_reference;
							$new->save();
						}
						if(!empty($product->wholesale_price) && empty($new->product_supplier_price_te) && $product->id_supplier==$id_supplier)
						{
							$new->product_supplier_price_te = $product->wholesale_price;
							$new->save();
						}
						
					}
				}
				elseif(empty($value))
				{
					$sql = '
						SELECT *
						FROM `'._DB_PREFIX_.'product_supplier` ps
						WHERE ps.`id_supplier` = "'.(int)$id_supplier.'"
						AND ps.`id_product` = "'.(int)$id.'"';
					$check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
					if(!empty($check_in_supplier[0]["id_product_supplier"]))
					{
						$sql = 'DELETE FROM `'._DB_PREFIX_.'product_supplier`
						WHERE `id_supplier` = "'.(int)$id_supplier.'"
							AND `id_product` = "'.(int)$id.'"';
						Db::getInstance()->execute($sql);
					}

					// Si fournisseur par défaut
					if(!empty($product->id_supplier) && $product->id_supplier==$id_supplier)
					{
						$product->id_supplier = null;
						$product->supplier_reference = null;
						$product->save();
					}
				}
				
				$combinations = $product->getAttributeCombinations((int)$id_lang);
				if(!empty($combinations))
				{
					foreach($combinations as $combination)
					{
						$id_product = $id;
						$id = $combination["id_product_attribute"];
						if($value=="1")
						{
							$sql = '
							SELECT *
							FROM `'._DB_PREFIX_.'product_supplier` ps
							WHERE ps.`id_supplier` = "'.(int)$id_supplier.'"
							AND ps.`id_product_attribute` = "'.(int)$id.'"';
							$check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
							if(empty($check_in_supplier[0]["id_product_supplier"]))
							{
								$new = new ProductSupplier();
								$new->id_product = (int)$id_product;
								$new->id_supplier = (int)$id_supplier;
								$new->id_product_attribute = (int)$id;
								$new->save();
							}
						}
						elseif(empty($value))
						{
							$sql = '
							SELECT *
							FROM `'._DB_PREFIX_.'product_supplier` ps
							WHERE ps.`id_supplier` = "'.(int)$id_supplier.'"
							AND ps.`id_product_attribute` = "'.(int)$id.'"';
							$check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
							if(!empty($check_in_supplier[0]["id_product_supplier"]))
							{
								$sql = 'DELETE FROM `'._DB_PREFIX_.'product_supplier`
							WHERE `id_supplier` = "'.(int)$id_supplier.'"
								AND `id_product_attribute` = "'.(int)$id.'"';
								Db::getInstance()->execute($sql);
							}
						}
					}
				}
			}
		break;
		case 'default':
			if($value=="true")
				$value = 1;
			else
				$value = 0;
			
			foreach($ids as $id)
			{
				$product = new Product((int)$id, false, (int)$id_lang, (int)SCI::getSelectedShop());
				if($value=="1")
				{
					$sql = '
						SELECT *
						FROM `'._DB_PREFIX_.'product_supplier` ps
						WHERE ps.`id_supplier` = "'.(int)$id_supplier.'"
						AND ps.`id_product` = "'.(int)$id.'"
						AND ps.`id_product_attribute` = 0';
					$check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
					if(empty($check_in_supplier[0]["id_product_supplier"]))
					{
						$new = new ProductSupplier();
						$new->id_product = (int)$id;
						$new->id_supplier = (int)$id_supplier;
						$new->id_product_attribute = 0;
						$new->save();
					}
					else
					{
						$new = new ProductSupplier((int)$check_in_supplier[0]["id_product_supplier"]);
					}
					
					$product->id_supplier = (int)$id_supplier;
					if(!empty($new->product_supplier_reference))
						$product->supplier_reference = $new->product_supplier_reference;
					if(!empty($new->product_supplier_price_te))
						$product->wholesale_price = $new->product_supplier_price_te;
					$product->save();

					$combinations = Product::getProductAttributesIds((int)$id);
					if(!empty($combinations))
					{
						foreach($combinations as $combination)
						{
							if(empty($combination['id_product_attribute']))
								continue;
							$id_product_attr = $combination["id_product_attribute"];

							$id_product_supplier = (int)ProductSupplier::getIdByProductAndSupplier((int)$id, (int)$id_product_attr, (int)$id_supplier);
							if(empty($id_product_supplier))
							{
								$product_supplier_entity = new ProductSupplier();
								$product_supplier_entity->id_product = (int)$id;
								$product_supplier_entity->id_product_attribute = (int)$id_product_attr;
								$product_supplier_entity->id_supplier = (int)$id_supplier;
								$product_supplier_entity->product_supplier_reference = '';
								$product_supplier_entity->product_supplier_price_te = 0;
								$product_supplier_entity->id_currency = 0;
								$product_supplier_entity->save();
							}
						}
					}
				}
			}
		break;
		case 'mass_present':
			if($value=="true")
				$value = 1;
			else
				$value = 0;
			
			$suppliers  = explode(",", $id_supplier);
			foreach($suppliers as $id_supplier)
			{
				foreach($ids as $id)
				{
					$product = new Product((int)$id, false, (int)$id_lang, (int)SCI::getSelectedShop());
					if($value=="1")
					{
						$sql = '
							SELECT *
							FROM `'._DB_PREFIX_.'product_supplier` ps
							WHERE ps.`id_supplier` = "'.(int)$id_supplier.'"
							AND ps.`id_product` = "'.(int)$id.'"
							AND ps.`id_product_attribute` = 0';
						$check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
						if(empty($check_in_supplier[0]["id_product_supplier"]))
						{
							$new = new ProductSupplier();
							$new->id_product = (int)$id;
							$new->id_supplier = (int)$id_supplier;
							$new->id_product_attribute = 0;
							$new->save();
						}
						else
                        {
                            $new = new ProductSupplier($check_in_supplier[0]["id_product_supplier"]);
                            $new->save();
                        }
						
						// Si pas de fournisseur par défaut
						if(empty($product->id_supplier))
						{
							// on le met en défaut
							$product->id_supplier = (int)$id_supplier;
							// Si ref par défaut non vide et que ref vide
							if(!empty($product->supplier_reference) && empty($new->product_supplier_reference))
							{
								$new->product_supplier_reference = $product->supplier_reference;
								$new->save();
							}
							$product->save();
						}
						else
						{
							// Si ce fournisseur est le fournisseur par défaut
							// mais qu'il n'était pas présent
							// on lui met la référence par défaut
							if(!empty($product->supplier_reference) && empty($new->product_supplier_reference) && $product->id_supplier==$id_supplier)
							{
								$new->product_supplier_reference = $product->supplier_reference;
								$new->save();
							}
						
						}
					}
					elseif(empty($value))
					{
						$sql = '
							SELECT *
							FROM `'._DB_PREFIX_.'product_supplier` ps
							WHERE ps.`id_supplier` = "'.(int)$id_supplier.'"
							AND ps.`id_product` = "'.(int)$id.'"';
						$check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
						if(!empty($check_in_supplier[0]["id_product_supplier"]))
						{
							$sql = 'DELETE FROM `'._DB_PREFIX_.'product_supplier`
							WHERE `id_supplier` = "'.(int)$id_supplier.'"
								AND `id_product` = "'.(int)$id.'"';
							Db::getInstance()->execute($sql);
						}
						
						// Si fournisseur par défaut
						if(!empty($product->id_supplier) && $product->id_supplier==$id_supplier)
						{
							$product->id_supplier = null;
							$product->supplier_reference = null;
							$product->save();
						}
					}
					
					$combinations = $product->getAttributeCombinations((int)$id_lang);
					if(!empty($combinations))
					{
						foreach($combinations as $combination)
						{
							$id_product = $id;
							$id = $combination["id_product_attribute"];
							if($value=="1")
							{
								$sql = '
							SELECT *
							FROM `'._DB_PREFIX_.'product_supplier` ps
							WHERE ps.`id_supplier` = "'.(int)$id_supplier.'"
							AND ps.`id_product_attribute` = "'.(int)$id.'"';
								$check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
								if(empty($check_in_supplier[0]["id_product_supplier"]))
								{
									$new = new ProductSupplier();
									$new->id_product = (int)$id_product;
									$new->id_supplier = (int)$id_supplier;
									$new->id_product_attribute = (int)$id;
									$new->save();
								}
							}
							elseif(empty($value))
							{
								$sql = '
							SELECT *
							FROM `'._DB_PREFIX_.'product_supplier` ps
							WHERE ps.`id_supplier` = "'.(int)$id_supplier.'"
							AND ps.`id_product_attribute` = "'.(int)$id.'"';
								$check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
								if(!empty($check_in_supplier[0]["id_product_supplier"]))
								{
									$sql = 'DELETE FROM `'._DB_PREFIX_.'product_supplier`
							WHERE `id_supplier` = "'.(int)$id_supplier.'"
								AND `id_product_attribute` = "'.(int)$id.'"';
									Db::getInstance()->execute($sql);
								}
							}
						}
					}
				}
			}
		break;
	}

	if(!empty($ids)) {
		//update date_upd
		$sql = "UPDATE "._DB_PREFIX_."product SET date_upd = '".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product IN (".pSQL($ids).");";
		if(SCMS) {
			$sql .= "UPDATE "._DB_PREFIX_."product_shop SET date_upd = '".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product IN (".pSQL($ids).") AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true)).")";
		}
		Db::getInstance()->Execute($sql);
		// PM Cache
		ExtensionPMCM::clearFromIdsProduct($ids);
	}
}
