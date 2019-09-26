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

	$productliststr=Tools::getValue('productlist');
	$field=Tools::getValue('field','');
	$todo=Tools::getValue('todo','');
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
					Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET price=price'.psql($todo).', indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.psql($productliststr).')');
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop SET price=price'.psql($todo).', indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.psql($productliststr).') AND id_shop IN ('.SCI::getSelectedShopActionList(true).')');
				}else{
					$todo=str_replace('%','',$todo);
					Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET price=(price+price*('.psql($todo).'/100)), indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.psql($productliststr).')');
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop SET price=(price+price*('.psql($todo).'/100)), indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.psql($productliststr).') AND id_shop IN ('.SCI::getSelectedShopActionList(true).')');
				}
				break;
			case'pricetax':
				$needUpdateProductHook=true;
				$todo=str_replace(',','.',$todo);
				if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
				{
					$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																														FROM '._DB_PREFIX_.'product p 
																														LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																														WHERE p.id_product IN ('.psql($productliststr).')');
				}else{
					$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																														FROM '._DB_PREFIX_.'product p 
																														LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																												    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																														WHERE p.id_product IN ('.psql($productliststr).')');
				}
				if (strpos($todo,'%')===false)
				{
					foreach($productwithtaxrate as $p)
					{
						if ($p['prate']==0) $p['prate']=1;
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET price=('.floatval(($p['price']*$p['prate']+$todo)/$p['prate'])."), indexed=0, date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".intval($p['id_product'])."'");
					}
				}else{
					$todo=str_replace('%','',$todo);
					foreach($productwithtaxrate as $p)
					{
						if ($p['prate']==0) $p['prate']=1;
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET price=('.floatval((($p['price']*$p['prate'])+($p['price']*$p['prate']*($todo/100)))/$p['prate'])."), indexed=0, date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".$p['id_product']."'");
					}
				}
				break;
			case'quantity':
				$needUpdateProductHook=true;
				if (strpos($todo,'-')===false && strpos($todo,'+')===false) $todo='+'.$todo;
				$todo=str_replace(',','.',$todo);
				if (strpos($todo,'%')===false)
				{
					Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET quantity=quantity'.$todo.', indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.psql($productliststr).')');
					if(_s("CAT_ACTIVE_HOOK_UPDATE_QUANTITY")=="1")
					{
						$pds = explode(",", $productliststr);
						foreach($pds as $pd)
						{
							$newQuantity = Db::getInstance()->ExecuteS('SELECT quantity FROM '._DB_PREFIX_.'product WHERE id_product = "'.intval($pd).'"');
							if(isset($newQuantity[0]["quantity"]))
							{
								SCI::hookExec('actionUpdateQuantity',
								   array(
								   	'id_product' => $id_product,
								   	'id_product_attribute' => 0,
								   	'quantity' => $newQuantity[0]["quantity"]
								   )
								  );
							}
						}
					}
				}else{
					$todo=str_replace('%','',$todo);
					Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET quantity=FLOOR(quantity+quantity*('.$todo.'/100)), indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.psql($productliststr).')');
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop SET quantity=FLOOR(quantity+quantity*('.$todo.'/100)), indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.psql($productliststr).') AND id_shop IN ('.SCI::getSelectedShopActionList(true).')');
				}
				break;
			case'margin':
				$needUpdateProductHook=true;
				$method=_s('CAT_PROD_GRID_MARGIN_OPERATION');
				switch($method)
				{
					case 0:
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=p.wholesale_price+'.floatval($todo).' , indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																				WHERE p.id_product IN ('.psql($productliststr).') 
																				AND p.wholesale_price>0
																				AND NOT EXISTS (
																					SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																					)');
						if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
						{
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																															FROM '._DB_PREFIX_.'product p 
																															LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																															WHERE p.id_product IN ('.psql($productliststr).')
																															AND EXISTS (
																																	SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																	)');
						}else{
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																															FROM '._DB_PREFIX_.'product p 
																															LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																													    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																															WHERE p.id_product IN ('.psql($productliststr).')
																															AND EXISTS (
																																	SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																	)');
						}
						foreach($productwithtaxrate as $p)
						{
							$p['prate']=floatval($p['prate']);
							if ($p['prate']==0) $p['prate']=1;
							$pit=$p['price']*$p['prate'];
							$pet=$p['price'];
							if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
							{
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price*'.$p['prate'].'+'.(floatval($todo)*$p['prate']-$pit)." , date_upd='".pSQL(date("Y-m-d H:i:s"))."' 
																						WHERE id_product ='".intval($p['id_product'])."' 
																						AND wholesale_price>0");
							}else{
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price+'.(floatval($todo)-$pet).", date_upd='".pSQL(date("Y-m-d H:i:s"))."'  
																						WHERE id_product ='".intval($p['id_product'])."' 
																						AND wholesale_price>0");
							}
						}
						break;
					case 1:
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=((p.wholesale_price*'.floatval($todo).')/100+p.wholesale_price), indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																				WHERE p.id_product IN ('.psql($productliststr).') 
																				AND p.wholesale_price>0
																				AND NOT EXISTS (
																					SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																					)');
						if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
						{
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																																WHERE p.id_product IN ('.psql($productliststr).')
																																AND EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
						}else{
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																														    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																																WHERE p.id_product IN ('.psql($productliststr).')
																																AND EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
						}
						foreach($productwithtaxrate as $p)
						{
							if ($p['prate']==0) $p['prate']=1;
							$pit=$p['price']*$p['prate'];
							$pet=$p['price'];
							if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
							{
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=((wholesale_price*'.(floatval($todo)/100).'+wholesale_price)*'.$p['prate'].'-'.$pit."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".intval($p['id_product'])."' 
																						AND wholesale_price>0");
							}else{
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=((wholesale_price*'.(floatval($todo)/100).'+wholesale_price)-'.$pet."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".intval($p['id_product'])."' 
																						AND wholesale_price>0");
							}
						}
						break;
					case 2:
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=p.wholesale_price*'.floatval($todo).' , indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																				WHERE p.id_product IN ('.psql($productliststr).') 
																				AND p.wholesale_price>0
																				AND NOT EXISTS (
																					SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																					)');
						if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
						{
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																																WHERE p.id_product IN ('.psql($productliststr).')
																																AND EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
						}else{
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																														    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																																WHERE p.id_product IN ('.psql($productliststr).')
																																AND EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
						}
						foreach($productwithtaxrate as $p)
						{
							if ($p['prate']==0) $p['prate']=1;
							$pit=$p['price']*$p['prate'];
							$pet=$p['price'];
							if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
							{
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price*'.($p['prate']*floatval($todo)).'-'.($pit).", date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".$p['id_product']."' 
																						AND wholesale_price>0");
							}else{
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price*'.(floatval($todo)).'-'.($pet).", date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".$p['id_product']."' 
																						AND wholesale_price>0");
							}
						}
						break;
					case 3:
						if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
						{
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																																WHERE p.id_product IN ('.psql($productliststr).')');
						}else{
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																														    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																																WHERE p.id_product IN ('.psql($productliststr).')');
						}
						foreach($productwithtaxrate as $p)
						{
							if ($p['prate']==0) $p['prate']=1;
							$pit=$p['price']*$p['prate'];
							$pet=$p['price'];
							if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
							{
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=p.wholesale_price*'.(floatval($todo)/$p['prate']).' , indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																						WHERE p.id_product = '.intval($p['id_product']).'
																						AND p.wholesale_price>0
																						AND NOT EXISTS (
																							SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																							)');
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price*'.(floatval($todo)).'-'.floatval($pit)." , date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".intval($p['id_product'])."' 
																						AND wholesale_price>0");
							}else{
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=p.wholesale_price*'.(floatval($todo)/$p['prate']).', indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																						WHERE p.id_product = '.intval($p['id_product']).'
																						AND p.wholesale_price>0
																						AND NOT EXISTS (
																							SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																							)');
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price*'.(floatval($todo)*$p['prate']).'-'.floatval($pet)." , date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".intval($p['id_product'])."' 
																						AND wholesale_price>0");
							}
						}
						break;
					case 4:
						if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
						{
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																																WHERE p.id_product IN ('.psql($productliststr).')');
						}else{
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																														    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																																WHERE p.id_product IN ('.psql($productliststr).')');
						}
						foreach($productwithtaxrate as $p)
						{
							if ($p['prate']==0) $p['prate']=1;
							$pit=$p['price']*$p['prate'];
							$pet=$p['price'];
							if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
							{
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=((p.wholesale_price+(p.wholesale_price*'.floatval($todo).')/100)/'.$p['prate'].'), indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																						WHERE p.id_product IN ('.psql($productliststr).') 
																						AND p.wholesale_price>0
																						AND NOT EXISTS (
																							SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																							)');
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=((wholesale_price+((wholesale_price*'.floatval($todo).')/100))-'.$pit."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".$p['id_product']."' 
																						AND wholesale_price>0");
							}else{
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=((p.wholesale_price+(p.wholesale_price*'.floatval($todo).')/100)/'.$p['prate'].'), indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																						WHERE p.id_product IN ('.psql($productliststr).') 
																						AND p.wholesale_price>0
																						AND NOT EXISTS (
																							SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																							)');
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=((wholesale_price+((wholesale_price*'.floatval($todo).')/100))-'.$pet."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".$p['id_product']."' 
																						AND wholesale_price>0");
							}
						}
						break;
					case 5:
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=((100*p.wholesale_price)/(100-'.floatval($todo).')), indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																				WHERE p.id_product IN ('.psql($productliststr).') 
																				AND p.wholesale_price>0
																				AND NOT EXISTS (
																					SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																					)');
						if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
						{
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																																WHERE p.id_product IN ('.psql($productliststr).')
																																AND EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
						}else{
							$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																														    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																																WHERE p.id_product IN ('.psql($productliststr).')
																																AND EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
						}
						foreach($productwithtaxrate as $p)
						{
							if ($p['prate']==0) $p['prate']=1;
							$pit=$p['price']*$p['prate'];
							$pet=$p['price'];
							if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
							{
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=(((100*wholesale_price)/(100-'.(floatval($todo)).'))*'.$p['prate'].'-'.$pit."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".$p['id_product']."' 
																						AND wholesale_price>0");
							}else{
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=(((100*wholesale_price)/(100-'.(floatval($todo)).'))-'.$pet."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".$p['id_product']."' 
																						AND wholesale_price>0");
							}
						}
						break;

				}
				break;
			case'combi_price':
				$needUpdateAttributeHook=true;
				//if (strpos($todo,'-')===false && strpos($todo,'+')===false) $todo='+'.$todo;
				$todo=str_replace(',','.',$todo);
				
				if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
				{
					$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price ,pa.id_product_attribute,pa.price as price_pa
																	FROM '._DB_PREFIX_.'product p 
																		LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
																	WHERE p.id_product IN ('.psql($productliststr).')');
				}else{
					$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price ,pa.id_product_attribute,pa.price as price_pa
																	FROM '._DB_PREFIX_.'product p 
																	LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
															   			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
																	WHERE p.id_product IN ('.psql($productliststr).')');
				}
				if (strpos($todo,'%')===false)
				{
					foreach($productwithtaxrate as $p)
					{
						if ($p['prate']==0) $p['prate']=1;
						if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
						{
							$todotax=$todo*$p['prate'];
						}else{
							$todotax=$todo;
						}
						if (strpos($todotax,'-')===false && strpos($todotax,'+')===false) $todotax='+'.$todotax;
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET price=price'.psql($todotax).", date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".(int)$p['id_product']."'");
					}
				}else{
					$todo=str_replace('%','',$todo);
					foreach($productwithtaxrate as $p)
					{
						if ($p['prate']==0) $p['prate']=1;
						$add = ($p["price"]+$p["price_pa"])*($todo/100);
						if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
						{
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																					SET price=price+'.$add.", date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																					WHERE id_product ='".$p['id_product']."'
																						AND id_product_attribute ='".$p['id_product_attribute']."'");
						}else{
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																					SET price=price+'.$add.", date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																					WHERE id_product ='".$p['id_product']."'
																						AND id_product_attribute ='".$p['id_product_attribute']."'");
						}
					}
				}
				break;
			case'combi_pricetax':
				$needUpdateAttributeHook=true;
/*				if (strpos($todo,'-')===false && strpos($todo,'+')===false) $todo='+'.$todo;
				$todo=str_replace(',','.',$todo);
				if (strpos($todo,'%')===false)
				{
					Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET price=price'.$todo.' WHERE id_product IN ('.psql($productliststr).')');
				}else{
					$todo=str_replace('%','',$todo);
					Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET price=(price+price*('.$todo.'/100)) WHERE id_product IN ('.psql($productliststr).')');
				}*/
				//if (strpos($todo,'-')===false && strpos($todo,'+')===false) $todo='+'.$todo;
				$todo=str_replace(',','.',$todo);
				if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
				{
					$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price ,pa.id_product_attribute,pa.price as price_pa
																	FROM '._DB_PREFIX_.'product p 
																	LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																	INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
																	WHERE p.id_product IN ('.psql($productliststr).')');
				}else{
					$productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price ,pa.id_product_attribute,pa.price as price_pa
																	FROM '._DB_PREFIX_.'product p 
																	LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
															   			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																	INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
																	WHERE p.id_product IN ('.psql($productliststr).')');
				}
				if (strpos($todo,'%')===false)
				{
					foreach($productwithtaxrate as $p)
					{
						if ($p['prate']==0) $p['prate']=1;
						if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
						{
							$todotax=$todo;
						}else{
							$todotax=$todo/$p['prate'];
						}
						if (strpos($todotax,'-')===false && strpos($todotax,'+')===false) $todotax='+'.$todotax;
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
								SET price=price'.psql($todotax)." , date_upd='".pSQL(date("Y-m-d H:i:s"))."'
								WHERE 
									id_product ='".(int)$p['id_product']."'
									AND id_product_attribute ='".(int)$p['id_product_attribute']."'");
					}
				}else{
					$todo=str_replace('%','',$todo);
					foreach($productwithtaxrate as $p)
					{
						if ($p['prate']==0) $p['prate']=1;
						$add = ($p["price"]+$p["price_pa"])*($todo/100);
						if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
						{
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																					SET price=price+'.$add.", date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																					WHERE id_product ='".$p['id_product']."'
																						AND id_product_attribute ='".$p['id_product_attribute']."'");
						}else{
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																					SET price=price+'.$add.", date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																					WHERE id_product ='".$p['id_product']."'
																						AND id_product_attribute ='".$p['id_product_attribute']."'");
						}
					}
				}


				break;
			case'combi_quantity':
				$needUpdateAttributeHook=true;
				if (strpos($todo,'-')===false && strpos($todo,'+')===false) $todo='+'.$todo;
				$todo=str_replace(',','.',$todo);
				if (strpos($todo,'%')===false)
				{
					Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET quantity=quantity'.$todo.', date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.psql($productliststr).')');
				}else{
					$todo=str_replace('%','',$todo);
					Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET quantity=FLOOR(quantity+quantity*('.$todo.'/100)), date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.psql($productliststr).')');
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
							), date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
						WHERE `id_product` = '.intval($p));
				}
				break;
			case'defaultcombination':
				$needUpdateAttributeHook=true;
				$todo=Tools::getValue('todo','');
				switch($todo)
				{
					case'cheapest':
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET default_on=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.psql($productliststr).')');
						$res=Db::getInstance()->ExecuteS('SELECT pa.id_product_attribute FROM `'._DB_PREFIX_.'product_attribute` pa
									WHERE pa.id_product IN ('.psql($productliststr).') AND pa.id_product_attribute=(
											SELECT pa2.id_product_attribute FROM  `'._DB_PREFIX_.'product_attribute` pa2
											WHERE pa.id_product=pa2.id_product ORDER BY price LIMIT 1)');
						$list=array();
						foreach($res as $r)
						{
							$list[]=$r['id_product_attribute'];
						}
					
						if (count($list))
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET default_on=1, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																					WHERE id_product_attribute IN ('.join(',',$list).')');
					case'instockandcheapest':
						$res=Db::getInstance()->ExecuteS('SELECT pa.id_product, pa.id_product_attribute FROM `'._DB_PREFIX_.'product_attribute` pa
									WHERE pa.id_product IN ('.psql($productliststr).') AND pa.id_product_attribute=(
											SELECT pa2.id_product_attribute FROM  `'._DB_PREFIX_.'product_attribute` pa2
											WHERE pa.id_product=pa2.id_product AND pa2.quantity > 0 ORDER BY price LIMIT 1)');
						$list=array();
						$listProducts=array();
						foreach($res as $r)
						{
							$list[]=$r['id_product_attribute'];
							$listProducts[]=$r['id_product'];
						}
						Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET default_on=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.psql(join(',',$listProducts)).')');
					
						if (count($list))
							Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET default_on=1, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																					WHERE id_product_attribute IN ('.join(',',$list).')');
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
									"product"=>0
							);
							$tax_rates = array(
									"product"=>0
							);
							$ecotaxes = array(
									"product"=>0
							);
				
							// Récupération du prix à modifier
							if($column=="price" || $column=="wholesale_price")
							{
								$sql = 'SELECT '.$column.' FROM '._DB_PREFIX_.'product WHERE id_product = "'.(int)$id_product.'"';
								$rslt = Db::getInstance()->ExecuteS($sql);
								if(isset($rslt[0][$column]))
									$old_price["product"]=$rslt[0][$column];
							}
							elseif($column=="price_inc_tax")
							{
								if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
								{
									$sql = 'SELECT 1+(t.rate/100) AS prate,p.price,p.ecotax
												FROM '._DB_PREFIX_.'product p
												LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
										    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
												WHERE p.id_product = "'.(int)$id_product.'"';
								}elseif (version_compare(_PS_VERSION_, '1.3.0.0', '>='))
								{
									$sql = 'SELECT 1+(t.rate/100) AS prate,p.price,p.ecotax
												FROM '._DB_PREFIX_.'product p
												LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax)
												WHERE p.id_product = "'.(int)$id_product.'"';
								}else{
									$sql = 'SELECT 1+(t.rate/100) AS prate,p.price, "0" AS ecotax
												FROM '._DB_PREFIX_.'product p
												LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax)
												WHERE p.id_product = "'.(int)$id_product.'"';
								}
								$rslt = Db::getInstance()->ExecuteS($sql);
								$ecotax = (_s('CAT_PROD_ECOTAXINCLUDED') ? $rslt[0]['ecotax']*SCI::getEcotaxTaxRate() : 0);
								$ecotaxes["product"]=$ecotax;
								if(empty($rslt[0]["prate"]))
									$rslt[0]["prate"] = 1;
								if(isset($rslt[0]["price"]) && isset($rslt[0]["prate"]))
									$old_price["product"]=$rslt[0]["price"]*$rslt[0]["prate"]+$ecotax;
								if(isset($rslt[0]["prate"]))
									$tax_rates["product"]=$rslt[0]["prate"];
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
							if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
							{
								$sql='SELECT t.rate,p.price,p.ecotax
								FROM `'._DB_PREFIX_.'product` p
								LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
						   			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
								WHERE p.id_product='.intval($id_product);
							}
							elseif (version_compare(_PS_VERSION_, '1.3.0.0', '>='))
							{
								$sql='SELECT t.rate,p.price,p.ecotax 
									FROM `'._DB_PREFIX_.'product` p, `'._DB_PREFIX_.'tax` t 
									WHERE 
										p.id_product='.intval($id_product).' 
										AND t.id_tax=p.id_tax';
							}
							else
							{
								$sql='SELECT t.rate,p.price, "0" AS ecotax 
								FROM `'._DB_PREFIX_.'product` p, `'._DB_PREFIX_.'tax` t 
								WHERE 
									p.id_product='.intval($id_product).' 
									AND t.id_tax=p.id_tax';
							}
							$p=Db::getInstance()->getRow($sql);
							if(empty($p['rate']))
								$p['rate'] = 0;
							$taxrate=$p['rate'];
							$pprice = $p['price'];
							$pecotax = $p['ecotax'];
								
							// COMBINATIONS
							$sql='SELECT id_product_attribute
								FROM `'._DB_PREFIX_.'product_attribute`
								WHERE id_product='.intval($id_product);
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
									if(isset($rslt[0][$select_column]))
									{
										$price=$rslt[0][$select_column];
										if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
											$old_price["product"]=number_format($price+$pprice, 6, '.', '');
										else
										{
											if(!empty($taxrate))
												$old_price["product"]=number_format($price/($taxrate/100+1)+$pprice, 6, '.', '');
											else
												$old_price["product"]=number_format($price+$pprice, 6, '.', '');
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
								}
								elseif($column=="price")
								{
									$select_column = "price";

									/*$sql = 'SELECT '.$select_column.' '.((version_compare(_PS_VERSION_, '1.4.0.0', '>=') && _s('CAT_PROD_ECOTAXINCLUDED'))?',ecotax':'').'
									FROM '._DB_PREFIX_.'product_attribute
									WHERE id_product_attribute = "'.(int)$id_product_attribute.'"';*/
									if (version_compare(_PS_VERSION_, '1.3.0.0', '>='))
										$sql = 'SELECT '.$select_column.',ecotax
										FROM '._DB_PREFIX_.'product_attribute
										WHERE id_product_attribute = "'.(int)$id_product_attribute.'"';
									else
										$sql = 'SELECT '.$select_column.', "0" AS ecotax
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
											if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
												$old_price["product"]=number_format($price*($taxrate/100+1)+$pprice*($taxrate/100+1) + $ecotax_temp, 6, '.', '');
											else
												$old_price["product"]=number_format($price+$pprice*($taxrate/100+1) + $ecotax_temp, 6, '.', '');
										}
										else
										{
											$old_price["product"]=number_format($price+$pprice + $ecotax_temp, 6, '.', '');
										}	
										if(_s('CAT_PROD_ECOTAXINCLUDED'))
											$eco_price["product"]=$ecotax_temp;
									}
								}
							
								// Arrondir le prix
								//echo $old_price["product"]." -> ";
								$new_price = SCI::roundPrice($old_price["product"], $todo);
								//echo $new_price." =>\n";
								$update_column = $column;
								if($column=="price") // TTC
								{
									$update_column = "price";
									if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
									{
										if(!empty($taxrate))
											$new_price = floatval( ( floatval($new_price - $eco_price["product"]) / ($taxrate/100+1) ) - ($pprice)  );
											//$new_price = floatval( (floatval($new_price)- ($pprice*($taxrate/100+1)) - $eco_price["product"]) /($taxrate/100+1) );
										else
											$new_price = floatval( ( floatval($new_price - $eco_price["product"]) ) - ($pprice)  );
											//$new_price = floatval( (floatval($new_price)- ($pprice*($taxrate/100+1)) - $eco_price["product"]) );
									}
									else
									{
										if(!empty($taxrate))
											$new_price = floatval( ( ( floatval($new_price - $eco_price["product"]) ) / ($taxrate/100+1) ) - ( ($pprice) / ($taxrate/100+1) ) );
										else
											$new_price = floatval( ( floatval($new_price - $eco_price["product"]) ) - ($pprice)  );
									}
								}
								elseif($column=="priceextax") // HT
								{
									$update_column = "price";
									if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
										$new_price = floatval($new_price)-floatval($pprice);
									else
									{
										if(!empty($taxrate))
											$new_price = ((floatval($new_price)-(floatval($pprice)))*($taxrate/100+1));
										else
											$new_price = ((floatval($new_price)-(floatval($pprice))));
									}
								}
								Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET '.$update_column.'="'.pSQL($new_price).'", date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product_attribute = "'.(int)$id_product_attribute.'"');
							}
						}
					}
					break;
		}
		if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
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
		}elseif(_s('APP_COMPAT_EBAY')){
			$plist=explode(',',$productliststr);
			sort($plist);
			Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'),intval($plist[0])));
		}

		if(!empty($productliststr))
			ExtensionPMCM::clearFromIdsProduct(explode(",",$productliststr));
	}
