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

	$productliststr=Tools::getValue('productlist');
	$id_lang=Tools::getValue('id_lang',SCI::getConfigurationValue("PS_LANG_DEFAULT"));
	$field=Tools::getValue('field','');
	$todo=Tools::getValue('todo','');
	$alert_msg_qty = "";
	if ($productliststr!='' && $todo!='')
	{
		$needUpdateAttributeHook=false;
		$needUpdateProductHook=false;
		switch($field)
		{
			case'price':
				$needUpdateProductHook=true;
				if (strpos($todo,'-')===false && strpos($todo,'+')===false) $todo='+'.$todo;
				$todo=str_replace(',','.',$todo);
				if (strpos($todo,'%')===false)
				{
					Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET price=price'.pSQL($todo).', indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.pSQL($productliststr).')');
					//if(SCMS)
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop SET price=price'.pSQL($todo).', indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.pSQL($productliststr).') AND id_shop IN ('.SCI::getSelectedShopActionList(true).')');
				}else{
					$todo=str_replace('%','',$todo);
					Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET price=(price+price*('.pSQL($todo).'/100)), indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.pSQL($productliststr).')');
					//if(SCMS)
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop SET price=(price+price*('.pSQL($todo).'/100)), indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.pSQL($productliststr).') AND id_shop IN ('.SCI::getSelectedShopActionList(true).')');
				}
				break;
			case'pricetax':
				$needUpdateProductHook=true;
				$todo=str_replace(',','.',$todo);
				$todo_shop=str_replace(',','.',$todo);
				
				// pricetax pour product
				$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																FROM '._DB_PREFIX_.'product p 
																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
														    		LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																WHERE p.id_product IN ('.pSQL($productliststr).')');
				
				if (strpos($todo,'%')===false)
				{
					foreach($productwithtaxrate as $p)
					{
						if ($p['prate']==0) $p['prate']=1;
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET price=('.floatval(($p['price']*$p['prate']+$todo)/$p['prate'])."), indexed='0', date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".intval($p['id_product'])."'");
					}
				}else{
					$todo=str_replace('%','',$todo);
					foreach($productwithtaxrate as $p)
					{
						if ($p['prate']==0) $p['prate']=1;
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET price=('.floatval((($p['price']*$p['prate'])+($p['price']*$p['prate']*($todo/100)))/$p['prate'])."), indexed='0', date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".$p['id_product']."'");
					}
				}
				
				// pricetax pour product_shop
				/*if(SCMS && SCI::getSelectedShop()>0)
				{*/
					$todo = $todo_shop;
					$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,ps.price
																FROM '._DB_PREFIX_.'product p
																INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
														    		LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																WHERE p.id_product IN ('.pSQL($productliststr).')');

					if (strpos($todo,'%')===false)
					{
						foreach($productwithtaxrate as $p)
						{
							if ($p['prate']==0) $p['prate']=1;
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop SET price=('.floatval(($p['price']*$p['prate']+$todo)/$p['prate'])."), indexed='0', date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".intval($p['id_product'])."' AND id_shop IN (".SCI::getSelectedShopActionList(true, $p['id_product']).")");
						}
					}else{
						$todo=str_replace('%','',$todo);
						foreach($productwithtaxrate as $p)
						{
							if ($p['prate']==0) $p['prate']=1;
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop SET price=('.floatval((($p['price']*$p['prate'])+($p['price']*$p['prate']*($todo/100)))/$p['prate'])."), indexed='0', date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".$p['id_product']."' AND id_shop IN (".SCI::getSelectedShopActionList(true, $p['id_product']).")");
						}
					}
				//}
				break;
			case'quantity':
				$needUpdateProductHook=true;
				//if (strpos($todo,'-')===false && strpos($todo,'+')===false) $todo='+'.$todo;
				if (strpos($todo,'+')!==false) $todo=str_replace("+","",$todo);
				$todo=str_replace(',','.',$todo);
				$productliststr = explode(",",$productliststr);
				
				if(SCAS)
				{
					$productliststr_temp = array();
					foreach($productliststr as $product_id)
					{
						if(
								!SCI::usesAdvancedStockManagement($product_id) 
								|| 
								(SCI::usesAdvancedStockManagement($product_id) && !StockAvailable::dependsOnStock((int)$product_id, (int)SCI::getSelectedShop()))
							)
						{
							$productliststr_temp[] = $product_id;
						}
					}
					$productliststr = $productliststr_temp;
				}
				if (strpos($todo,'%')===false)
				{
					/*if (SCMS)
					{*/
						foreach($productliststr as $product_id)
						{
							$product = new Product($product_id, false, $id_lang);
							if(!$product->hasAttributes())
							{
								$shops = SCI::getSelectedShopActionList(false, $product_id);
								foreach($shops as $shop_id)
								{
									$id_stock_available = StockAvailable::getStockAvailableIdByProductId($product_id, null, $shop_id);
									if(!empty($id_stock_available))
										$stock_available = SCI::updateQuantity($product_id, null, $todo, $shop_id);
									else
										$stock_available = SCI::setQuantity($product_id, null, $todo, $shop_id);
								}
								SCI::hookExec('actionUpdateQuantity',
									array(
									'id_product' => $product_id,
									'id_product_attribute' => 0,
									'quantity' => $todo
									)
								);
							}
							else
							{
								if(!empty($alert_msg_qty))
									$alert_msg_qty .= ", ";
								$alert_msg_qty .= $product->name;
							}
						}
					/*}
					else
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET quantity=quantity'.$todo.' WHERE id_product IN ('.pSQL($productliststr).')');*/
				}else{
					$todo=str_replace('%','',$todo);
					/*if (SCMS)
					{*/
						foreach($productliststr as $product_id)
						{
							$product = new Product($product_id, false, $id_lang);
							if(!$product->hasAttributes())
							{
								$shops = SCI::getSelectedShopActionList(false, $product_id);
								foreach($shops as $shop_id)
								{
									$id_stock_available = StockAvailable::getStockAvailableIdByProductId($product_id, null, $shop_id);
									if(!empty($id_stock_available))
									{
										$actual_qty = StockAvailable::getQuantityAvailableByProduct($product_id, null, $shop_id);
										
										if($todo>0)
											$qty = -1*($actual_qty*abs($todo)/100);
										else
											$qty = ($actual_qty*($todo)/100);
										
										$stock_available = SCI::updateQuantity($product_id, null, $qty, $shop_id);
									}
								}

								SCI::hookExec('actionUpdateQuantity',
								array(
								'id_product' => $product_id,
								'id_product_attribute' => 0,
								'quantity' => $qty
								)
								);
							}
							else
							{
								if(!empty($alert_msg_qty))
									$alert_msg_qty .= ", ";
								$alert_msg_qty .= $product->name;
							}
						}
					/*}
					else
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET quantity=FLOOR(quantity+quantity*('.$todo.'/100)) WHERE id_product IN ('.pSQL($productliststr).')');*/
				}
				break;
			case'margin':
				$needUpdateProductHook=true;
				$method=_s('CAT_PROD_GRID_MARGIN_OPERATION');
				switch($method)
				{
					case 0:
						// update pour product
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=p.wholesale_price+'.floatval($todo).' 
													WHERE p.id_product IN ('.pSQL($productliststr).') 
													AND p.wholesale_price>0
													AND NOT EXISTS (
														SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
													)');
						// product attribute
						$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,p.price
																		FROM '._DB_PREFIX_.'product p 
																		WHERE p.id_product IN ('.pSQL($productliststr).')
																		AND EXISTS (
																				SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																		)');
						
						foreach($productwithtaxrate as $p)
						{
							$pet=$p['price'];
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute
														SET price=wholesale_price+'.(floatval($todo)-$pet)."
														WHERE id_product ='".intval($p['id_product'])."'
														AND wholesale_price>0");
						}
						
						// update pour product_shop
						/*if(SCMS && SCI::getSelectedShop()>0)
						{*/
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop ps SET ps.price=ps.wholesale_price+'.floatval($todo).', indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
														WHERE ps.id_product IN ('.pSQL($productliststr).')
														AND ps.id_shop IN ('.pSQL(SCI::getSelectedShopActionList(true)).')
														AND ps.wholesale_price>0
														AND NOT EXISTS (
															SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=ps.id_product
														)');
							
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,ps.price, pa.id_product_attribute
																	FROM '._DB_PREFIX_.'product p
																		INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
																	WHERE p.id_product IN ('.pSQL($productliststr).')');
							
							foreach($productwithtaxrate as $p)
							{
								$pet=$p['price'];
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
															SET price=wholesale_price+'.(floatval($todo)-$pet)."
															WHERE id_product_attribute ='".intval($p['id_product_attribute'])."'
															AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
															AND wholesale_price>0");
							}
						//}
						break;
					case 1:
						// product
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=((p.wholesale_price*'.floatval($todo).')/100+p.wholesale_price), indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
													WHERE p.id_product IN ('.pSQL($productliststr).') 
													AND p.wholesale_price>0
													AND NOT EXISTS (
														SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
													)');
						
						// product attribute
						$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,p.price
																		FROM '._DB_PREFIX_.'product p 
																		WHERE p.id_product IN ('.pSQL($productliststr).')
																		AND EXISTS (
																				SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																		)');
						
						foreach($productwithtaxrate as $p)
						{
							$pet=$p['price'];
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
														SET price=((wholesale_price*'.(floatval($todo)/100).'+wholesale_price)-'.$pet."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
														WHERE id_product ='".intval($p['id_product'])."' 
														AND wholesale_price>0");
						}
						
						// update pour product_shop
						/*if(SCMS && SCI::getSelectedShop()>0)
						{*/
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop ps SET ps.price=((ps.wholesale_price*'.floatval($todo).')/100+ps.wholesale_price), indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
														WHERE ps.id_product IN ('.pSQL($productliststr).')
														AND ps.id_shop IN ('.pSQL(SCI::getSelectedShopActionList(true)).')
														AND ps.wholesale_price>0
														AND NOT EXISTS (
															SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=ps.id_product
														)');
							
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,ps.price, pa.id_product_attribute
																	FROM '._DB_PREFIX_.'product p
																		INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
																	WHERE p.id_product IN ('.pSQL($productliststr).')');
							
							foreach($productwithtaxrate as $p)
							{
								$pet=$p['price'];
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
															SET price=((wholesale_price*'.(floatval($todo)/100).'+wholesale_price)-'.$pet.")
															WHERE id_product_attribute ='".intval($p['id_product_attribute'])."'
															AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
															AND wholesale_price>0");
							}
						//}
						break;
					case 2:
						// product
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=p.wholesale_price*'.floatval($todo).' , indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																				WHERE p.id_product IN ('.pSQL($productliststr).') 
																				AND p.wholesale_price>0
																				AND NOT EXISTS (
																					SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																				)');
						
						// product attribute
						$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,p.price
																		FROM '._DB_PREFIX_.'product p 
																		WHERE p.id_product IN ('.pSQL($productliststr).')
																		AND EXISTS (
																				SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																		)');
						
						foreach($productwithtaxrate as $p)
						{
							$pet=$p['price'];
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
														SET price=wholesale_price*'.(floatval($todo)).'-'.($pet).", date_upd='".pSQL(date("Y-m-d H:i:s"))."'
														WHERE id_product ='".$p['id_product']."' 
														AND wholesale_price>0");
						}
						
						// update pour product_shop
						/*if(SCMS && SCI::getSelectedShop()>0)
						{*/
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop ps SET ps.price=ps.wholesale_price*'.floatval($todo).' , indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
														WHERE ps.id_product IN ('.pSQL($productliststr).')
														AND ps.id_shop IN ('.pSQL(SCI::getSelectedShopActionList(true)).')
														AND ps.wholesale_price>0
														AND NOT EXISTS (
															SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=ps.id_product
														)');
							
							// product attribute shop
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,ps.price, pa.id_product_attribute
																	FROM '._DB_PREFIX_.'product p
																		INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
																	WHERE p.id_product IN ('.pSQL($productliststr).')');
							
							foreach($productwithtaxrate as $p)
							{
								$pet=$p['price'];
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
															SET price=wholesale_price*'.(floatval($todo)).'-'.($pet)."
															WHERE id_product_attribute ='".intval($p['id_product_attribute'])."'
															AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
															AND wholesale_price>0");
							}
						//}
						break;
					case 3:
						// product
						$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																		FROM '._DB_PREFIX_.'product p 
																		LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																   			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																		WHERE p.id_product IN ('.pSQL($productliststr).')');
						
						foreach($productwithtaxrate as $p)
						{
							if ($p['prate']==0) $p['prate']=1;
							$pit=$p['price']*$p['prate'];
							$pet=$p['price'];
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=p.wholesale_price*'.(floatval($todo)/$p['prate']).', indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'" 
														WHERE p.id_product = '.intval($p['id_product']).'
														AND p.wholesale_price>0
														AND NOT EXISTS (
															SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
														)');
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
														SET price=wholesale_price*'.(floatval($todo)*$p['prate']).'-'.floatval($pet)." ,  date_upd='".pSQL(date("Y-m-d H:i:s"))."'
														WHERE id_product ='".intval($p['id_product'])."' 
														AND wholesale_price>0");
							
						}
						
						// update pour product_shop
						/*if(SCMS && SCI::getSelectedShop()>0)
						{*/
							// product_shop
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,ps.price 
																			FROM '._DB_PREFIX_.'product p 
																				INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																					LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																			   			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																			WHERE p.id_product IN ('.pSQL($productliststr).')');
							
							foreach($productwithtaxrate as $p)
							{
								if ($p['prate']==0) $p['prate']=1;
								$pit=$p['price']*$p['prate'];
								$pet=$p['price'];
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop p SET p.price=p.wholesale_price*'.(floatval($todo)/$p['prate']).', indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
															WHERE p.id_product = '.intval($p['id_product']).'
															AND p.wholesale_price>0
															AND p.id_shop IN ('.pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).')
															AND NOT EXISTS (
																SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
															)');
								
							}
							
							// product_attribute_shop
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,ps.price,pa.id_product_attribute
																			FROM '._DB_PREFIX_.'product p
																				INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																					LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																			   			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																				INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
																			WHERE p.id_product IN ('.pSQL($productliststr).')');
								
							foreach($productwithtaxrate as $p)
							{
								if ($p['prate']==0) $p['prate']=1;
								$pit=$p['price']*$p['prate'];
								$pet=$p['price'];
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
															SET price=wholesale_price*'.(floatval($todo)*$p['prate']).'-'.floatval($pet)."
															WHERE id_product_attribute ='".intval($p['id_product_attribute'])."'
															AND p.id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
															AND wholesale_price>0");
							
							}
						//}
						break;
					case 4:
						// product
						$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																		FROM '._DB_PREFIX_.'product p 
																		LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																    		LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																		WHERE p.id_product IN ('.pSQL($productliststr).')');
						
						foreach($productwithtaxrate as $p)
						{
							if ($p['prate']==0) $p['prate']=1;
							$pit=$p['price']*$p['prate'];
							$pet=$p['price'];
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=((p.wholesale_price+(p.wholesale_price*'.floatval($todo).')/100)/'.$p['prate'].'), indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
														WHERE p.id_product IN ('.pSQL($productliststr).') 
														AND p.wholesale_price>0
														AND NOT EXISTS (
															SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
														)');
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
														SET price=((wholesale_price+((wholesale_price*'.floatval($todo).')/100))-'.$pet."),  date_upd='".pSQL(date("Y-m-d H:i:s"))."'
														WHERE id_product ='".$p['id_product']."' 
														AND wholesale_price>0");
							
						}
						
						// update pour product_shop
						/*if(SCMS && SCI::getSelectedShop()>0)
						{*/
							// product_shop
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,ps.price 
																			FROM '._DB_PREFIX_.'product p 
																				INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																					LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																			   			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																			WHERE p.id_product IN ('.pSQL($productliststr).')');
							
							foreach($productwithtaxrate as $p)
							{
								if ($p['prate']==0) $p['prate']=1;
								$pit=$p['price']*$p['prate'];
								$pet=$p['price'];
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop p SET p.price=((p.wholesale_price+(p.wholesale_price*'.floatval($todo).')/100)/'.$p['prate'].'),  date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
															WHERE p.id_product = '.intval($p['id_product']).'
															AND p.wholesale_price>0
															AND p.id_shop IN ('.pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).')
															AND NOT EXISTS (
																SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
															)');
								
							}

							// product_attribute_shop
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,ps.price,pa.id_product_attribute
																			FROM '._DB_PREFIX_.'product p
																				INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																					LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																			   			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																				INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
																			WHERE p.id_product IN ('.pSQL($productliststr).')');
							foreach($productwithtaxrate as $p)
							{
								if ($p['prate']==0) $p['prate']=1;
								$pit=$p['price']*$p['prate'];
								$pet=$p['price'];
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
															SET price=((wholesale_price+((wholesale_price*'.floatval($todo).')/100))-'.$pet.")
															WHERE id_product_attribute ='".intval($p['id_product_attribute'])."' 
															AND p.id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
															AND wholesale_price>0");
								
							}
						//}
						break;
					case 5:
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=((100*p.wholesale_price)/(100-'.floatval($todo).')), indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
													WHERE p.id_product IN ('.pSQL($productliststr).') 
													AND p.wholesale_price>0
													AND NOT EXISTS (
														SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
														)');
						
						// product attribute
						$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,p.price
																		FROM '._DB_PREFIX_.'product p 
																		WHERE p.id_product IN ('.pSQL($productliststr).')
																		AND EXISTS (
																				SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																		)');
						foreach($productwithtaxrate as $p)
						{
							$pet=$p['price'];
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
														SET price=(((100*wholesale_price)/(100-'.(floatval($todo)).'))-'.$pet."),  date_upd='".pSQL(date("Y-m-d H:i:s"))."'
														WHERE id_product ='".$p['id_product']."' 
														AND wholesale_price>0");
							
						}
						
						// update pour product_shop
						/*if(SCMS && SCI::getSelectedShop()>0)
						{*/
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop ps SET ps.price=((100*ps.wholesale_price)/(100-'.floatval($todo).')), indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
														WHERE ps.id_product IN ('.pSQL($productliststr).')
														AND ps.id_shop IN ('.pSQL(SCI::getSelectedShopActionList(true)).')
														AND ps.wholesale_price>0
														AND NOT EXISTS (
															SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=ps.id_product
														)');
							
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,ps.price,pa.id_product_attribute
																	FROM '._DB_PREFIX_.'product p
																		INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
																	WHERE p.id_product IN ('.pSQL($productliststr).')');
							
							foreach($productwithtaxrate as $p)
							{
								$pet=$p['price'];
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
															SET price=(((100*wholesale_price)/(100-'.(floatval($todo)).'))-'.$pet.")
															WHERE id_product_attribute ='".intval($p['id_product_attribute'])."'
															AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
															AND wholesale_price>0");
							}
						//}
						break;

				}
				break;
			case'combi_price':
				$needUpdateAttributeHook=true;
				//if (strpos($todo,'-')===false && strpos($todo,'+')===false) $todo='+'.$todo;
				$todo=str_replace(',','.',$todo);
				$todo_shop = $todo;
				$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,p.price,pa.id_product_attribute,pa.price as price_pa
																FROM '._DB_PREFIX_.'product p 
																	INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
																WHERE p.id_product IN ('.pSQL($productliststr).')');
				
				if (strpos($todo,'%')===false)
				{
					foreach($productwithtaxrate as $p)
					{
						$todotax=$todo;
						if (strpos($todotax,'-')===false && strpos($todotax,'+')===false) $todotax='+'.$todotax;
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET price=price'.pSQL($todotax).", date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".(int)$p['id_product']."' AND id_product_attribute ='".(int)$p['id_product_attribute']."'");
					}
				}else{
					$todo=str_replace('%','',$todo);
					foreach($productwithtaxrate as $p)
					{
						$add = ($p["price"]+$p["price_pa"])*($todo/100);
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
													SET price=price+'.$add.",  date_upd='".pSQL(date("Y-m-d H:i:s"))."'
													WHERE id_product ='".$p['id_product']."'");
					}
				}
				
				// product attribute shop
				/*if(SCMS && SCI::getSelectedShop()>0)
				{*/
					$todo = $todo_shop;
					$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,ps.price, pa.id_product_attribute,pas.price as price_pa
																	FROM '._DB_PREFIX_.'product p
																		INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
																			INNER JOIN '._DB_PREFIX_.'product_attribute_shop pas ON (pa.id_product_attribute = pas.id_product_attribute AND pas.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																	WHERE p.id_product IN ('.pSQL($productliststr).')');					
					if (strpos($todo,'%')===false)
					{
						foreach($productwithtaxrate as $p)
						{
							$todotax=$todo;
							if (strpos($todotax,'-')===false && strpos($todotax,'+')===false) $todotax='+'.$todotax;
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET price=price'.pSQL($todotax)." WHERE id_product_attribute ='".(int)$p['id_product_attribute']."' AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true)).")");
						}
					}else{
						$todo=str_replace('%','',$todo);
						foreach($productwithtaxrate as $p)
						{
							$add = ($p["price"]+$p["price_pa"])*($todo/100);
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
													SET price=price+'.$add."
													WHERE id_product_attribute ='".$p['id_product_attribute']."'
														AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true)).")");
						}
					}
				//}
				break;
			case'combi_pricetax':
				$needUpdateAttributeHook=true;
				$todo=str_replace(',','.',$todo);
				$todo_shop = $todo;
				$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price,pa.id_product_attribute,pa.price as price_pa
																FROM '._DB_PREFIX_.'product p 
																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
														    		LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
																WHERE p.id_product IN ('.pSQL($productliststr).')');
				
				if (strpos($todo,'%')===false)
				{
					foreach($productwithtaxrate as $p)
					{
						if ($p['prate']==0) $p['prate']=1;
						$todotax=$todo/$p['prate'];
						if (strpos($todotax,'-')===false && strpos($todotax,'+')===false) $todotax='+'.$todotax;
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET price=price'.pSQL($todotax).", date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".(int)$p['id_product']."'");
					}
				}else{
					$todo=str_replace('%','',$todo);
					foreach($productwithtaxrate as $p)
					{
						if ($p['prate']==0) $p['prate']=1;
						$add = (($p["price"])+($p["price_pa"]))*($todo/100);
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
													SET price=price+'.$add.", date_upd='".pSQL(date("Y-m-d H:i:s"))."'
													WHERE id_product ='".$p['id_product']."'");
						
					}
				}
				
				// product attribute shop
				/*if(SCMS && SCI::getSelectedShop()>0)
				{*/
					$todo = $todo_shop;
					
					$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,ps.price,pa.id_product_attribute,pas.price as price_pa
																	FROM '._DB_PREFIX_.'product p
																		INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																			LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																	    		LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
																			INNER JOIN '._DB_PREFIX_.'product_attribute_shop pas ON (pa.id_product_attribute = pas.id_product_attribute AND pas.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																	WHERE p.id_product IN ('.pSQL($productliststr).')');
					if (strpos($todo,'%')===false)
					{
						foreach($productwithtaxrate as $p)
						{
							if ($p['prate']==0) $p['prate']=1;
							$todotax=$todo/$p['prate'];
							if (strpos($todotax,'-')===false && strpos($todotax,'+')===false) $todotax='+'.$todotax;
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET price=price'.pSQL($todotax)." WHERE id_product_attribute ='".(int)$p['id_product_attribute']."' AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true)).")");
						}
					}else{
						$todo=str_replace('%','',$todo);
						foreach($productwithtaxrate as $p)
						{
							if ($p['prate']==0) $p['prate']=1;
							$add = (($p["price"])+($p["price_pa"]))*($todo/100);
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
													SET price=price+'.$add."
													WHERE id_product_attribute ='".$p['id_product_attribute']."'
														AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true)).")");
					
						}
					}
				//}
				break;
			case'combi_quantity':
				$needUpdateAttributeHook=true;
				if (strpos($todo,'-')===false && strpos($todo,'+')===false) $todo='+'.$todo;
				$todo=str_replace(',','.',$todo);
				$todo_shop = $todo;
				
				if (strpos($todo,'%')===false)
				{
					Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET quantity=quantity'.$todo.', date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.pSQL($productliststr).')');
				}else{
					$todo=str_replace('%','',$todo);
					Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET quantity=FLOOR(quantity+quantity*('.$todo.'/100)), date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.pSQL($productliststr).')');
				}
				
				$plist=explode(',',$productliststr);
				foreach($plist AS $p)
				{
					Db::getInstance()->Execute('
						UPDATE `'._DB_PREFIX_.'product`
						SET `quantity` =
							(
							SELECT SUM(`quantity`)
							FROM `'._DB_PREFIX_.'product_attribute`
							WHERE `id_product` = '.intval($p).'
							),  date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
						WHERE `id_product` = '.intval($p));
				}
				
				// product attribute shop
				/*if(SCMS && SCI::getSelectedShop()>0)
				{*/
					$todo = $todo_shop;
					if (strpos($todo,'+')!==false) $todo=str_replace("+","",$todo);
					$product_attrs=Db::getInstance()->ExecuteS('SELECT p.id_product,pa.id_product_attribute
																	FROM '._DB_PREFIX_.'product p
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
																			INNER JOIN '._DB_PREFIX_.'product_attribute_shop pas ON (pa.id_product_attribute = pas.id_product_attribute AND pas.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																	WHERE p.id_product IN ('.pSQL($productliststr).')');
					
					if(SCAS)
					{
						$product_attrs_temp = array();
						foreach($productliststr_temp as $product_attr)
						{
							$c = new Combination((int)$product_attr);
							if(
								!SCI::usesAdvancedStockManagement($c->id_product) 
								|| 
								(SCI::usesAdvancedStockManagement($c->id_product) && !StockAvailable::dependsOnStock((int)$c->id_product, (int)SCI::getSelectedShop())))
							{
								$product_attrs_temp[] = $product_attr;
							}
						}
						$productliststr_temp = $productliststr_temp;
					}
					
					if (strpos($todo,'%')===false)
					{
						foreach($product_attrs as $product_attr)
						{
							$shops = SCI::getSelectedShopActionList(false, $product_attr["id_product"]);
							foreach($shops as $shop_id)
							{
								$id_stock_available = StockAvailable::getStockAvailableIdByProductId($product_attr["id_product"], $product_attr["id_product_attribute"], $shop_id);
								if(!empty($id_stock_available))
									$stock_available = SCI::updateQuantity($product_attr["id_product"], $product_attr["id_product_attribute"], $todo, $shop_id);
								else
									$stock_available = SCI::setQuantity($product_attr["id_product"], $product_attr["id_product_attribute"], $todo, $shop_id);
							}
						}
					}else{
						$todo=str_replace('%','',$todo);
						foreach($product_attrs as $product_attr)
						{
							$shops = SCI::getSelectedShopActionList(false, $product_attr["id_product"]);
							foreach($shops as $shop_id)
							{
								$id_stock_available = StockAvailable::getStockAvailableIdByProductId($product_attr["id_product"], $product_attr["id_product_attribute"], $shop_id);
								if(!empty($id_stock_available))
								{
									$actual_qty = StockAvailable::getQuantityAvailableByProduct($product_attr["id_product"], $product_attr["id_product_attribute"], $shop_id);
									
									if($todo>0)
										$qty = -1*($actual_qty*abs($todo)/100);
									else
										$qty = ($actual_qty*($todo)/100);
									
									$stock_available = SCI::updateQuantity($product_attr["id_product"], $product_attr["id_product_attribute"], $qty, $shop_id);
								}
							}
						}
					}
					
					$products = explode(",",$productliststr);
					foreach ($products as $product_id)
						SCI::qtySumStockAvailable($product_id);
				//}
				
				break;
			case'defaultcombination':
				$needUpdateAttributeHook=true;
				$todo=Tools::getValue('todo','');
				switch($todo)
				{
					case'cheapest':
						$productlist=explode(',',$productliststr);
						foreach($productlist AS $id_product)
						{
							$shops = SCI::getSelectedShopActionList(false, $id_product);
							foreach($shops as $shop_id)
							{
								if(version_compare(_PS_VERSION_, '1.6.1', '>='))
									Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET default_on=NULL WHERE id_shop = "'.(int)$shop_id.'" AND id_product_attribute IN (SELECT id_product_attribute FROM '._DB_PREFIX_.'product_attribute WHERE id_product = '.(int)$id_product.')');
								else
									Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET default_on=0 WHERE id_shop = "'.(int)$shop_id.'" AND id_product_attribute IN (SELECT id_product_attribute FROM '._DB_PREFIX_.'product_attribute WHERE id_product = '.(int)$id_product.')');
								$ida=Db::getInstance()->getValue('SELECT pas2.id_product_attribute FROM '._DB_PREFIX_.'product_attribute_shop pas2 WHERE pas2.id_shop = "'.(int)$shop_id.'" AND pas2.id_product_attribute IN (SELECT pa.id_product_attribute FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product = '.(int)$id_product.') ORDER BY pas2.price ASC');
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET default_on=1 WHERE id_shop = "'.(int)$shop_id.'" AND id_product_attribute = '.(int)$ida);
							}
						}
						break;
					case'instockandcheapest':
						$productlist=explode(',',$productliststr);
						foreach($productlist AS $id_product)
						{
							$shops = SCI::getSelectedShopActionList(false, $id_product);
							foreach($shops as $shop_id)
							{
								$res = Db::getInstance()->ExecuteS('SELECT pa.id_product,pas2.id_product_attribute FROM '._DB_PREFIX_.'product_attribute_shop pas2
																										LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pas2.id_product_attribute = pa.id_product_attribute)
																										WHERE id_shop = "'.(int)$shop_id.'"
																											 AND pas2.id_product_attribute IN (SELECT pa.id_product_attribute FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product = '.(int)$id_product.') ORDER BY pas2.price ASC');
								foreach($res as $row)
								{
									$qty = SCI::_getProductAttributeQty($row['id_product'],$row['id_product_attribute']);
									if ($qty > 0)
									{
										if(version_compare(_PS_VERSION_, '1.6.1', '>='))
											Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET default_on=NULL WHERE id_shop = "'.(int)$shop_id.'" AND id_product_attribute IN (SELECT id_product_attribute FROM '._DB_PREFIX_.'product_attribute WHERE id_product = '.(int)$row['id_product'].')');
										else
											Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET default_on=0 WHERE id_shop = "'.(int)$shop_id.'" AND id_product_attribute IN (SELECT id_product_attribute FROM '._DB_PREFIX_.'product_attribute WHERE id_product = '.(int)$row['id_product'].')');
										Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET default_on=1 WHERE id_shop = "'.(int)$shop_id.'" AND id_product_attribute = '.(int)$row['id_product_attribute']);
										break;
									}
								}
							}
						}
						break;
				}
				break;
			case'mass_round':
				$needUpdateProductHook=true;
				$todo=Tools::getValue('todo','0');
				$column=Tools::getValue('column','');
				
				if(!empty($todo) && !empty($column))
				{
					$productlist=explode(',',$productliststr);
					foreach($productlist AS $id_product)
					{
						$old_price = array(
							"product"=>0,
							"shops" => array()		
						);
						$tax_rates = array(
								"product"=>0,
								"shops" => array()
						);
						$ecotaxes = array(
								"product"=>0,
								"shops" => array()
						);
						
						// Récupération du prix à modifier
						if($column=="price" || $column=="wholesale_price")
						{
							$sql = 'SELECT '.$column.' FROM '._DB_PREFIX_.'product WHERE id_product = "'.(int)$id_product.'"';
							$rslt = Db::getInstance()->ExecuteS($sql);
							if(isset($rslt[0][$column]))
								$old_price["product"]=$rslt[0][$column];
							
							$shops = SCI::getSelectedShopActionList(false, (int)$id_product);
							foreach($shops as $shop)
							{
								$sql = 'SELECT '.$column.' FROM '._DB_PREFIX_.'product_shop WHERE id_product = "'.(int)$id_product.'" AND id_shop = "'.(int)$shop.'"';
								$rslt = Db::getInstance()->ExecuteS($sql);
								if(isset($rslt[0][$column]))
									$old_price["shops"][$shop]=$rslt[0][$column];
							}
						}
						elseif($column=="price_inc_tax")
						{
							$sql = 'SELECT 1+(t.rate/100) AS prate,p.price, p.ecotax
																FROM '._DB_PREFIX_.'product p
																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
														    		LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																WHERE p.id_product = "'.(int)$id_product.'"';
							$rslt = Db::getInstance()->ExecuteS($sql);
							$no_tax = false;
							$ecotax = (_s('CAT_PROD_ECOTAXINCLUDED') ? $rslt[0]['ecotax']*SCI::getEcotaxTaxRate() : 0);
							$ecotaxes["product"]=$ecotax;
							if(empty($rslt[0]["prate"]))
								$rslt[0]["prate"] = 1;							
							if(isset($rslt[0]["price"]) && isset($rslt[0]["prate"]))
								$old_price["product"]=($rslt[0]["price"]*$rslt[0]["prate"])+$ecotax;
							if(isset($rslt[0]["prate"]))
								$tax_rates["product"]=$rslt[0]["prate"];
							
							
							$shops = SCI::getSelectedShopActionList(false, (int)$id_product);
							foreach($shops as $shop)
							{
								$sql = 'SELECT 1+(t.rate/100) AS prate,ps.price, ps.ecotax
																FROM '._DB_PREFIX_.'product p
																INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product)
																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
														    		LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																WHERE ps.id_product = "'.(int)$id_product.'" AND ps.id_shop = "'.(int)$shop.'"';
								$rslt = Db::getInstance()->ExecuteS($sql);
								if(empty($rslt[0]["prate"]))
									$rslt[0]["prate"] = 1;
								if(isset($rslt[0]["price"]) && isset($rslt[0]["prate"]))
								{
									$ecotax = (_s('CAT_PROD_ECOTAXINCLUDED') ? $rslt[0]['ecotax']*SCI::getEcotaxTaxRate() : 0);
									$old_price["shops"][$shop]=($rslt[0]["price"]*$rslt[0]["prate"])+$ecotax;
									$tax_rates["shops"][$shop]=$rslt[0]["prate"];
									$ecotaxes["shops"][$shop]=$ecotax;
								}
							}
						}
						
						// Arrondir le prix
						$new_price = SCI::roundPrice($old_price["product"], $todo);
						$update_column = $column;
						if($column=="price_inc_tax")
						{
							$update_column = "price";
							if(!empty($tax_rates["product"]))
								$new_price = floatval(($new_price-$ecotaxes["product"])/$tax_rates["product"]);
							else
								$new_price = floatval($new_price-$ecotaxes["product"]);
						}
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET '.$update_column.'="'.pSQL($new_price).'", indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product = "'.(int)$id_product.'"');
					
						foreach($old_price["shops"] as $shop=>$price)
						{
							$new_price = SCI::roundPrice($price, $todo);
							if($column=="price_inc_tax")
							{
								if(!empty($tax_rates["shops"][$shop]))
									$new_price = floatval(($new_price-$ecotaxes["shops"][$shop])/$tax_rates["shops"][$shop]);
								else
									$new_price = floatval($new_price-$ecotaxes["shops"][$shop]);
							}
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop SET '.$update_column.'="'.pSQL($new_price).'", indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product = "'.(int)$id_product.'" AND id_shop = "'.(int)$shop.'"');
						}
					}
				}
			break;
			case'mass_round_combi':
				$needUpdateAttributeHook=true;
				$todo=Tools::getValue('todo','0');
				$column=Tools::getValue('column','');

				if($column=="price")
					$column = "priceextax";
				elseif($column=="price_inc_tax")
					$column = "price";
				
				if(!empty($todo) && !empty($column))
				{
					$productlist=explode(',',$productliststr);
					foreach($productlist AS $id_product)
					{
						// PRODUCT
						$sql='SELECT t.rate,p.price,p.ecotax,p.id_shop_default
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
						if(SCMS && SCI::getSelectedShop()!=0)
						{
							$sql='SELECT pa.id_product_attribute
								FROM `'._DB_PREFIX_.'product_attribute` pa
								LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` pas ON (pas.`id_product_attribute` = pa.`id_product_attribute` AND pas.`id_shop` = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():$p['id_shop_default']).')
						   		WHERE pa.id_product='.intval($id_product);
						}
						else
						{
							$sql='SELECT pa.id_product_attribute
								FROM `'._DB_PREFIX_.'product_attribute` pa
								WHERE pa.id_product='.intval($id_product);
						}
						$combilist=Db::getInstance()->executeS($sql);
						foreach($combilist AS $id_product_attribute)
						{
							if(is_array($id_product_attribute))
								$id_product_attribute = $id_product_attribute["id_product_attribute"];
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
								if(!empty($rslt[0][$select_column]))
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
											$eco_price["shops"][$shop]= $ecotax_temp;
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
									//$new_price = floatval( floatval($new_price) - $pprice*($taxrate/100+1) );
							}
							elseif($column=="priceextax") // HT
							{
								$update_column = "price";
								$new_price = floatval($new_price)-floatval($pprice);
							}
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET '.$update_column.'="'.pSQL($new_price).'", date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product_attribute = "'.(int)$id_product_attribute.'"');
								
							foreach($old_price["shops"] as $shop=>$price)
							{
								$new_price = SCI::roundPrice($price, $todo);
								if($column=="price") // TTC
								{
									if(!empty($taxrate_shops[$shop]))
										$new_price = floatval( ( ( floatval($new_price - $eco_price["shops"][$shop]) / ($taxrate_shops[$shop]/100+1) ) - ($pprice_shops[$shop])) );
									else
										$new_price = floatval( ( ( floatval($new_price - $eco_price["shops"][$shop]) ) - ($pprice_shops[$shop])) );
								}
								elseif($column=="priceextax") // HT
									$new_price = ((floatval($new_price)-(floatval($pprice_shops[$shop]))));

								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET '.$update_column.'="'.pSQL($new_price).'" WHERE id_product_attribute = "'.(int)$id_product_attribute.'" AND id_shop = "'.(int)$shop.'"');
							}
						}
					}
				}
			break;
		}
		if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY') && !empty($productliststr))
		{
			$plist=explode(',',$productliststr);
			foreach($plist AS $p)
			{
				$product=new Product($p);
				if ($needUpdateProductHook)
					SCI::hookExec('updateProduct', array('product' => $product));
				if ($needUpdateAttributeHook)
					SCI::hookExec('updateProductAttribute', array('product' => $product));
			}
		}elseif(_s('APP_COMPAT_EBAY') && !empty($productliststr)){
			$plist=explode(',',$productliststr);
			sort($plist);
			Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'),intval($plist[0])));
		}

		if(!empty($productliststr))
			ExtensionPMCM::clearFromIdsProduct(explode(",",$productliststr));
	}
	
	if(!empty($alert_msg_qty))
	{
		echo "quantity|".$alert_msg_qty;
	}
