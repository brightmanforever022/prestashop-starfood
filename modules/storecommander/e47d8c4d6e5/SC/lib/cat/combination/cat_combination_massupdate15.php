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

	if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
		die();

	$combiliststr=Tools::getValue('combilist');
	$id_lang=Tools::getValue('id_lang',SCI::getConfigurationValue("PS_LANG_DEFAULT"));
	$id_product=Tools::getValue('id_product',0);
	$field=Tools::getValue('field','');
	$todo=Tools::getValue('todo','');
	$alert_msg_qty = "";
	if ($combiliststr!='' && $todo!='')
	{
		$needUpdateAttributeHook=false;
		$needUpdateProductHook=false;
		switch($field)
		{
			case'mass_round':
				$todo=Tools::getValue('todo','0');
				$column=Tools::getValue('column','');
				
				if(!empty($todo) && !empty($column))
				{
					// PRODUCT
					$sql='SELECT t.rate,p.price,p.ecotax
						FROM `'._DB_PREFIX_.'product` p
						LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
				   			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
						WHERE p.id_product='.intval($id_product);
					$p=Db::getInstance()->getRow($sql);
					if(empty($p['rate']))
						$p['rate'] = 0;
					$taxrate=$p['rate'];
					$pprice = $p['price'];
					$pecotax = $p['ecotax'];
					
					$sql='SELECT t.rate,ps.price,ps.id_shop,ps.ecotax
						FROM `'._DB_PREFIX_.'product_shop` ps
						LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
				   			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
						WHERE ps.id_product='.intval($id_product);
					$p_shops=Db::getInstance()->executeS($sql);
					$pprice_shops = array();
					$taxrate_shops = array();
					$ecotax_shops = array();
					foreach($p_shops as $p_shop)
					{
						if(empty($p_shop['rate']))
							$p_shop['rate'] = 0;
						$pprice_shops[$p_shop["id_shop"]] = $p_shop['price'];
						$taxrate_shops[$p_shop["id_shop"]] = $p_shop['rate'];
						$ecotax_shops[$p_shop["id_shop"]] = $p_shop['ecotax'];
					}
					
					// COMBINATIONS
					$combilist=explode(',',$combiliststr);
					foreach($combilist AS $id_product_attribute)
					{
						$old_price = array(
							"product"=>0,
							"shops" => array()		
						);
						$eco_price = array(
							"product"=>0,
							"shops" => array()		
						);
						
						// Récupération du prix à modifier
						if($column=="priceextax")
						{
							$select_column = "price";
							
							$sql = 'SELECT '.$select_column.'
									FROM '._DB_PREFIX_.'product_attribute
									WHERE id_product_attribute = "'.(int)$id_product_attribute.'"';
							$rslt = Db::getInstance()->ExecuteS($sql);
							if(isset($rslt[0][$select_column]))
							{
								$price=$rslt[0][$select_column];
									
								$old_price["product"]=number_format($price+$pprice, 6, '.', '');
							}
							
							$shops = SCI::getSelectedShopActionList(false, (int)$id_product);
							foreach($shops as $shop)
							{
								$sql = 'SELECT '.$select_column.' FROM '._DB_PREFIX_.'product_attribute_shop WHERE id_product_attribute = "'.(int)$id_product_attribute.'" AND id_shop = "'.(int)$shop.'"';
								$rslt = Db::getInstance()->ExecuteS($sql);
								if(!empty($rslt[0][$select_column]))
								{
									$price=$rslt[0][$select_column];
									$old_price["shops"][$shop]=number_format($price+$pprice_shops[$shop], 6, '.', '');
								}
							}
						}
						elseif($column=="wholesale_price")
						{
							$select_column = $column;
							$sql = 'SELECT '.$select_column.' FROM '._DB_PREFIX_.'product_attribute WHERE id_product_attribute = "'.(int)$id_product_attribute.'"';
							$rslt = Db::getInstance()->ExecuteS($sql);
							if(!empty($rslt[0][$select_column]))
								$old_price["product"]=$rslt[0][$select_column];
							
							$shops = SCI::getSelectedShopActionList(false, (int)$id_product);
							foreach($shops as $shop)
							{
								$sql = 'SELECT '.$select_column.' FROM '._DB_PREFIX_.'product_attribute_shop WHERE id_product_attribute = "'.(int)$id_product_attribute.'" AND id_shop = "'.(int)$shop.'"';
								$rslt = Db::getInstance()->ExecuteS($sql);
								if(!empty($rslt[0][$select_column]))
									$old_price["shops"][$shop]=$rslt[0][$select_column];
							}
						}
						elseif($column=="price")
						{
							$select_column = "price";
							
							/*$sql = 'SELECT '.$select_column.' '.((version_compare(_PS_VERSION_, '1.4.0.0', '>=') && _s('CAT_PROD_ECOTAXINCLUDED'))?',ecotax':'').'
									FROM '._DB_PREFIX_.'product_attribute
									WHERE id_product_attribute = "'.(int)$id_product_attribute.'"';*/
							$sql = 'SELECT '.$select_column.',ecotax
									FROM '._DB_PREFIX_.'product_attribute
									WHERE id_product_attribute = "'.(int)$id_product_attribute.'"';
							$rslt = Db::getInstance()->ExecuteS($sql);
							if(!empty($rslt[0][$select_column]))
							{
								$price=$rslt[0][$select_column];
									
								$ecotax_temp = (_s('CAT_PROD_ECOTAXINCLUDED') ? $rslt[0]['ecotax']*SCI::getEcotaxTaxRate() : 0);
								if(($ecotax_temp*1) == 0)
									$ecotax_temp = (_s('CAT_PROD_ECOTAXINCLUDED') ? $pecotax*SCI::getEcotaxTaxRate() : 0);
								
								if(!empty($taxrate))
								{
									$old_price["product"]=number_format($price*($taxrate/100+1)+$pprice*($taxrate/100+1) + $ecotax_temp, 6, '.', '');
								}
								else
								{
									$old_price["product"]=number_format($price+$pprice + $ecotax_temp, 6, '.', '');
								}
								if(_s('CAT_PROD_ECOTAXINCLUDED'))
									$eco_price["product"]=$ecotax_temp;
							}
							
							$shops = SCI::getSelectedShopActionList(false, (int)$id_product);
							foreach($shops as $shop)
							{
								//$sql = 'SELECT '.$select_column.' '.((version_compare(_PS_VERSION_, '1.4.0.0', '>=') && _s('CAT_PROD_ECOTAXINCLUDED'))?',ecotax':'').' FROM '._DB_PREFIX_.'product_attribute_shop WHERE id_product_attribute = "'.(int)$id_product_attribute.'" AND id_shop = "'.(int)$shop.'"';
								$sql = 'SELECT '.$select_column.',ecotax FROM '._DB_PREFIX_.'product_attribute_shop WHERE id_product_attribute = "'.(int)$id_product_attribute.'" AND id_shop = "'.(int)$shop.'"';
								$rslt = Db::getInstance()->ExecuteS($sql);
								if(!empty($rslt[0][$select_column]))
								{
									$price=$rslt[0][$select_column];
									
									$ecotax_temp = (_s('CAT_PROD_ECOTAXINCLUDED') ? $rslt[0]['ecotax']*SCI::getEcotaxTaxRate() : 0);
									if(($ecotax_temp*1) == 0)
										$ecotax_temp = (_s('CAT_PROD_ECOTAXINCLUDED') ? $ecotax_shops[$shop]*SCI::getEcotaxTaxRate() : 0);
										
									if(!empty($taxrate_shops[$shop]))
										$old_price["shops"][$shop]=number_format($price*($taxrate_shops[$shop]/100+1)+$pprice_shops[$shop]*($taxrate_shops[$shop]/100+1) + $ecotax_temp, 6, '.', '');
									else
										$old_price["shops"][$shop]=number_format($price+$pprice_shops[$shop] + $ecotax_temp, 6, '.', '');
									if(_s('CAT_PROD_ECOTAXINCLUDED'))
										$eco_price["shops"][$shop]=$ecotax_temp;
								}
							}
						}
						
						// Arrondir le prix
						$new_price = SCI::roundPrice($old_price["product"], $todo);
						$update_column = $column;
						if($column=="price") // TTC
						{
							$update_column = "price";		
							if(!empty($taxrate))
								$new_price = floatval( ( floatval($new_price - $eco_price["product"]) / ($taxrate/100+1) ) - ($pprice)  );
							else
								$new_price = floatval( ( floatval($new_price - $eco_price["product"]) ) - ($pprice)  );
							/*if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
							{
								if(!empty($taxrate))
									$new_price = floatval( (floatval($new_price)- ($pprice*($taxrate/100+1)) - $eco_price["product"]) /($taxrate/100+1) );
								else
									$new_price = floatval( (floatval($new_price)- ($pprice*($taxrate/100+1)) - $eco_price["product"]) );
							}
							else
								$new_price = floatval( floatval($new_price) - $pprice*($taxrate/100+1) );*/
						}
						elseif($column=="priceextax") // HT
						{
							$update_column = "price";
							$new_price = floatval($new_price)-floatval($pprice);
						} 
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET '.$update_column.'="'.pSQL($new_price).'" WHERE id_product_attribute = "'.(int)$id_product_attribute.'"');
					
						foreach($old_price["shops"] as $shop=>$price)
						{
							$new_price = SCI::roundPrice($price, $todo);
							
							if($column=="price") // TTC
							{
								if(!empty($taxrate_shops[$shop]))
									$new_price = floatval( ( ( floatval($new_price - $eco_price["shops"][$shop]) / ($taxrate_shops[$shop]/100+1) ) - ($pprice_shops[$shop])) );
								else
									$new_price = floatval( ( ( floatval($new_price - $eco_price["shops"][$shop]) ) - ($pprice_shops[$shop])) );
								/*if(!empty($taxrate_shops[$shop]))
									$new_price = floatval( ( (floatval($new_price)/($taxrate_shops[$shop]/100+1)) - ($pprice_shops[$shop]) - $eco_price["shops"][$shop]) );
								else
									$new_price = floatval( ( floatval($new_price) - ($pprice_shops[$shop]) - $eco_price["shops"][$shop]) );*/
							}
							elseif($column=="priceextax") // HT
								$new_price = ((floatval($new_price)-(floatval($pprice_shops[$shop]))));
							
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET '.$update_column.'="'.pSQL($new_price).'" WHERE id_product_attribute = "'.(int)$id_product_attribute.'" AND id_shop = "'.(int)$shop.'"');
						}
					}
				}
			break;
		}

		if(!empty($updated_products))
			ExtensionPMCM::clearFromIdsProduct($id_product);
	}
	
	if(!empty($alert_msg_qty))
	{
		echo "quantity|".$alert_msg_qty;
	}
