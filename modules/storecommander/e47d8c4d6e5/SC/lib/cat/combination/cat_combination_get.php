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
	$id_product=intval(Tools::getValue('id_product'));
	$forceGroups=Tools::getValue('forceGroups','');
	$sortByPosition=(int)Tools::getValue('sortByPosition','0');
	$attributeGroupsNames=array();
	$attributeGroupsIDs=array();
	$sc_active=SCI::getConfigurationValue('SC_PLUG_DISABLECOMBINATIONS',0);
	$results=array();
	$name_field_attr=array();
	$productpriceinctax=0;
	$productIDsupplier = 0;

	$sql = "SELECT id_supplier FROM "._DB_PREFIX_."product WHERE id_product = ".(int)$id_product;
	$res = Db::getInstance()->getRow($sql);
	if(!empty($res))
	{
		$productIDsupplier = $res['id_supplier'];
	}

	$arrMsgAvailableLater = array();
	$arrIdAvailableLater = array();
	if(SCI::getConfigurationValue("SC_DELIVERYDATE_INSTALLED")=="1")
	{
		$sql = "SELECT DISTINCT available_later FROM "._DB_PREFIX_."product_lang WHERE id_lang=".intval($id_lang)." ORDER BY available_later";
		$res=Db::getInstance()->ExecuteS($sql);
		foreach($res as $row){
			$arrMsgAvailableLater[$row['available_later']]=$row['available_later'];
		}
		$sql = "SELECT DISTINCT * FROM "._DB_PREFIX_."sc_available_later WHERE id_lang=".intval($id_lang)." ORDER BY available_later";
		$res=Db::getInstance()->ExecuteS($sql);
		foreach($res as $row){
			$arrMsgAvailableLater[$row['available_later']]=$row['available_later'];
			$arrIdAvailableLater[$row['id_sc_available_later']]=$row['available_later'];
		}
		ksort($arrMsgAvailableLater);
	}

    $uiSetts = uisettings::load_ini_file();

	function checkUiConfigAfterUpdate()
	{
		global $cols;

		$numberFromUi = '';
		$uiKeyToDelete= null;
		$countingCols = array();
		foreach($cols as $key => $val) {
			if(!preg_match('(attr_)',$val)) {
				$countingCols[] = $key;
			}
		};
		$countingCols = count($countingCols);

		foreach($uiSetts as $name => $setting) {
			if(substr($name, 0, 15)=="cat_combination") {
				$combarraySetting = explode('|',$setting);
				$rowSettings = explode('-',$combarraySetting[0]);
				foreach($rowSettings as $key => $val) {
					if(preg_match('({attr_)',$val)) {
						unset($rowSettings[$key]);
					}
				};
				$numberFromUi = count($rowSettings);
				if($numberFromUi != $countingCols) {
					$uiKeyToDelete = $name;
					if(!empty($uiKeyToDelete)) {
						unset($uiSetts[$uiKeyToDelete]);
					}
				}
			}
		}
		if(!empty($uiKeyToDelete)) {
			UISettings::write_ini_file($uiSetts, false);
		}
	}
	
	function getRowsFromDB()
	{
		global $attributeGroupsNames,$sortByPosition,$shop_id,$attributeGroupsIDs,$id_lang,$id_product,$productpriceinctax,$taxrate,$groups,$cols,$gridFormat,$sourceGridFormat,$combArray,$combinaison,$sc_active,$sql2,$results;

		checkUiConfigAfterUpdate();

		$is_advanced_stock_management = false;
		$type_advanced_stock_management = 1;// Not Advanced Stock Management
		if(SCAS)
		{
			$product_ins = new Product($id_product, false, null, (int)SCI::getSelectedShop());
			if($product_ins->advanced_stock_management==1)
			{
				$is_advanced_stock_management = true;
				$type_advanced_stock_management = 2;// With Advanced Stock Management
				
				if(!StockAvailable::dependsOnStock((int)$id_product, (int)SCI::getSelectedShop()))
					$type_advanced_stock_management = 3;// With Advanced Stock Management + Manual management
			}
		}
		
		foreach ($combArray AS $id_product_attribute => $product_attribute)
		{
			echo ("<row id='".$product_attribute['id']."'>");
			
			$not_in_warehouse = true;
			$without_warehouse = true;
			if(SCAS)
			{
				// Déclinaison est liée à l'entrepôt
				$temp_check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$id_product, (int)$product_attribute['id'], (int)SCI::getSelectedWarehouse());
				if(!empty($temp_check_in_warehouse))
				{
					$not_in_warehouse = false;
					$without_warehouse = false;
				}
					
				// Produit lié à au moins un entrepôt
				if($not_in_warehouse)
				{
					$query = new DbQuery();
					$query->select('wpl.id_warehouse_product_location');
					$query->from('warehouse_product_location', 'wpl');
					$query->where('wpl.id_product = '.(int)$id_product.'
						AND wpl.id_product_attribute = '.(int)$product_attribute['id'].'
						AND wpl.id_warehouse != 0'
					);
					$rslt = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
					if(count($rslt)>0)
						$without_warehouse = false;
				}
					
				$query = new DbQuery();
				$query->select('SUM(physical_quantity) as physical_quantity');
				$query->select('SUM(usable_quantity) as usable_quantity');
				//$query->select('SUM(price_te * physical_quantity) as valuation');
				$query->from('stock');
				$query->where('id_product = '.(int)$id_product.'');
				$query->where('id_product_attribute = '.(int)$product_attribute['id'].'');
				$query->where('id_warehouse = '.(int)SCI::getSelectedWarehouse().'');
				$avanced_quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
			}

			$reductionNameColor='';
			if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				// COULEUR DU CHAMPS "NOM" POUR LES PROMOTIONS
				// PROMOTIONS ACTIVES
				$sql_reduc = "SELECT id_specific_price
									FROM `"._DB_PREFIX_."specific_price`
									WHERE id_product = '".$id_product."'
										AND id_product_attribute = '".(int)$product_attribute['id']."'
										 AND `from` <= '".date("Y-m-d H:i:s")."'
										 AND (`to` >= '".date("Y-m-d H:i:s")."' OR `to`='0000-00-00 00:00:00')
										 AND (
										 		`reduction` > 0
										 		OR `price` > 0
										 	)
									 ORDER BY id_specific_price
									 LIMIT 1";
				$res_reduc=Db::getInstance()->executeS($sql_reduc);
				if(!empty($res_reduc[0]["id_specific_price"]))
					$reductionNameColor='#FFAAFF';
				
				// PROMOTIONS A VENIR
				if(empty($reductionNameColor))
				{
					$sql_reduc = "SELECT id_specific_price
									FROM `"._DB_PREFIX_."specific_price`
									WHERE id_product = '".$id_product."'
										 AND id_product_attribute = '".(int)$product_attribute['id']."'
										 AND `from` > '".date("Y-m-d H:i:s")."'
										 AND (
										 		`reduction` > 0
										 		OR `price` > 0
										 	)
									 ORDER BY id_specific_price
									 LIMIT 1";
					$res_reduc=Db::getInstance()->executeS($sql_reduc);
					if(!empty($res_reduc[0]["id_specific_price"]))
						$reductionNameColor='#eed9ee';
				}
			}
			//echo "\n\n\ncolor: ".$reductionNameColor."\n\n\n";

			$sourcecols=explode(',',$sourceGridFormat);
			//if(SCI::getConfigurationValue('PS_USE_ECOTAX')==1 && version_compare(_PS_VERSION_, '1.4.0.0', '>=') && ($product_attribute['ecotax']*1)==0) // use product ecotax as default 
			if(/*_s('CAT_PROD_ECOTAXINCLUDED')==1 && */version_compare(_PS_VERSION_, '1.3.0.0', '>=') && ($product_attribute['ecotax']*1)==0) // use product ecotax as default 
				$product_attribute['ecotax']=$product_attribute['pecotax'];
			sc_ext::readCustomCombinationsGridConfigXML('rowUserData',$product_attribute);
			$combi_attr_ids = "-";
			foreach($sourcecols AS $key => $col)
			{
				switch($col){
					case'id_product_attribute':
						echo ("<cell".(!empty($reductionNameColor)?' bgColor="'.$reductionNameColor.'"':"")." style=\"color:".($product_attribute['default_on']?'#0000FF':'#999999')."\">".$product_attribute['id']."</cell>");
						break;
					case'quantity':
						if($type_advanced_stock_management==2)
						{
							echo 		"<cell type=\"ro\"></cell>";
						}else{
							echo 		"<cell>".$product_attribute[$col]."</cell>";
						}
						//						}
						break;
					case'quantityupdate':
						$editable='';

						if ($without_warehouse && $type_advanced_stock_management==2)
							$editable=' bgColor="#e7ab70" type="ro"';
						elseif ($not_in_warehouse && $type_advanced_stock_management==2)
							$editable=' bgColor="#f7e4bf" type="ro"';
						elseif ($type_advanced_stock_management==2)
							$editable=' bgColor="#d7f7bf"';
						echo 		"<cell".$editable."></cell>";
						break;
					case'quantity_usable':
						$editable='';
					
						$value = $avanced_quantities['usable_quantity'];
						/*if(empty($value) || $value<=0)
							$value = 0;*/
						if($type_advanced_stock_management!=2)
							$value = "";

						if ($without_warehouse && $type_advanced_stock_management==2)
							$editable=' bgColor="#e7ab70" type="ro"';
						elseif ($not_in_warehouse && $type_advanced_stock_management==2)
							$editable=' bgColor="#f7e4bf" type="ro"';
						elseif ($type_advanced_stock_management==2)
							$editable=' bgColor="#d7f7bf"';
						echo 		"<cell".$editable.">".$value."</cell>";
						break;
					case'quantity_physical':
						$editable='';
						$value = $avanced_quantities['physical_quantity'];
						/*if(empty($value) || $value<=0)
							$value = 0;*/
						if($type_advanced_stock_management!=2)
							$value = "";
					
						if ($without_warehouse && $type_advanced_stock_management==2)
							$editable=' bgColor="#e7ab70" type="ro"';
						elseif ($not_in_warehouse && $type_advanced_stock_management==2)
							$editable=' bgColor="#f7e4bf" type="ro"';
						elseif ($type_advanced_stock_management==2)
							$editable=' bgColor="#d7f7bf"';
						echo 		"<cell".$editable.">".$value."</cell>";
						break;
					case'quantity_real':
						$editable='';
						/*$manager = StockManagerFactory::getManager();
						$value = $manager->getProductRealQuantities($id_product,
								(int)$product_attribute['id'],
								(int)SCI::getSelectedWarehouse(),
								true);*/
						$value = SCI::getProductRealQuantities((int)$id_product,
							(int)$product_attribute['id'],
							(int)SCI::getSelectedWarehouse(),
							true);
						if(/*empty($value) || $value<=0 ||*/ $not_in_warehouse)
							$value = 0;
						if($type_advanced_stock_management!=2)
							$value = "";
					
						if ($without_warehouse && $type_advanced_stock_management==2)
							$editable=' bgColor="#e7ab70" type="ro"';
						elseif ($not_in_warehouse && $type_advanced_stock_management==2)
							$editable=' bgColor="#f7e4bf" type="ro"';
						elseif ($type_advanced_stock_management==2)
							$editable=' bgColor="#d7f7bf"';
						echo 		"<cell".$editable.">".$value."</cell>";
						break;
					case'ATTR':
						if (_s('CAT_PROD_COMBI_METHOD'))
						{
							echo "<cell><![CDATA[";
							$xml = '';
							foreach($product_attribute['attributes'] AS $group)
							{
								$combi_attr_ids .= $group[2]."-";
								$xml .= $group[0].':'.$group[1].' - ';
								// $group[0] : group name
								// $group[1] : attribute name
								// $group[2] : attribute id
							}
							if (count($product_attribute['attributes'])) $xml=substr($xml,0,-3);
							echo $xml;
							echo "]]></cell>";
						}else{
							/*echo "\n\n\n";
							print_r($product_attribute['attributes']);
							echo"\n\n\n";*/
							foreach($attributeGroupsIDs as $attributeGroupsID)
							{
								if(!empty($product_attribute['attributes']))
								{
									$present = false;
									foreach($product_attribute['attributes'] AS $group)
									{
										if(!empty($group[3]))
										{
											//echo "\n\n\n".$group[3]."==".$attributeGroupsID."\n\n\n";
											if($group[3]==$attributeGroupsID)
											{
												$combi_attr_ids .= $group[2]."-";
												$name_value = $group[1];
												$name_value = str_replace("&", _l("and"), $name_value);
												$name_value = str_replace("<", "1", $name_value);
												$name_value = str_replace(">", "2", $name_value);
												$name_value = str_replace('"', "''", $name_value);
												if(version_compare(_PS_VERSION_, '1.5.0.1', '>='))
												{
													if(!empty($sortByPosition))
														echo ("<cell><![CDATA[".str_pad($group[4], 6, "0", STR_PAD_LEFT).';;;'.$name_value."|||".$group[2]."]]></cell>");
													else
														echo ("<cell><![CDATA[".$name_value.';;;'.$name_value."|||".$group[2]."]]></cell>");
												}
												else
													echo ("<cell><![CDATA[".$name_value."|||".$group[2]."]]></cell>");
												// $group[0] : group name
												// $group[1] : attribute name
												// $group[2] : attribute id
												$present = true;
											}
										}
									}
									if($present==false)
									{
										if(version_compare(_PS_VERSION_, '1.5.0.1', '>='))
										{
											if(!empty($sortByPosition))
												echo ("<cell><![CDATA[0000000;;;|||]]></cell>");
											else
												echo ("<cell><![CDATA[;;;|||]]></cell>");
										}
										else
											echo ("<cell><![CDATA[|||]]></cell>");
									}
								}
								else
								{
									if(version_compare(_PS_VERSION_, '1.5.0.1', '>='))
									{
										if(!empty($sortByPosition))
											echo ("<cell><![CDATA[0000000;;;|||]]></cell>");
										else
											echo ("<cell><![CDATA[;;;|||]]></cell>");
									}
									else
										echo ("<cell><![CDATA[|||]]></cell>");
								}
							}
							/*foreach($product_attribute['attributes'] AS $group)
							{
								$combi_attr_ids .= $group[2]."-";
								$name_value = $group[1];
								$name_value = str_replace("&", _l("and"), $name_value);
								echo ("<cell><![CDATA[".$name_value."|||".$group[2]."]]></cell>");
								// $group[0] : group name
								// $group[1] : attribute name
								// $group[2] : attribute id
							}*/
						}
						break;
					case'weight':
						echo ("<cell><![CDATA[".number_format($product_attribute['weight']+$product_attribute['pweight'], 6, '.', '')."]]></cell>");
						break;
/*					case'price_public': // SPECIAL
						echo ("<cell>".number_format($product_attribute['price_public'], 2, '.', '')."</cell>");
						echo ("<cell>".($product_attribute['price_public']==0?0:number_format((($product_attribute['price_public']-($product_attribute['price']/($taxrate/100+1)+$product_attribute['pprice']))/$product_attribute['price_public'])*100, 2, '.', ''))."</cell>");
						break;*/
					case'ecotax':
						echo ("<cell>".number_format(( version_compare(_PS_VERSION_, '1.3.0.0', '>=') ? $product_attribute['ecotax'] * SCI::getEcotaxTaxRate() : $product_attribute['ecotax'] ), 6, '.', '')."</cell>");
						break;
					case'pprice':
						//$productpriceinctax=$product_attribute['pprice']*($taxrate/100+1)+( _s('CAT_PROD_ECOTAXINCLUDED') && version_compare(_PS_VERSION_, '1.4.0.0', '>=') ? $product_attribute['pecotax']*SCI::getEcotaxTaxRate() : 0 );
						$ecotax = (_s('CAT_PROD_ECOTAXINCLUDED') && version_compare(_PS_VERSION_, '1.3.0.0', '>=') ? $product_attribute['pecotax']*SCI::getEcotaxTaxRate() : 0);
						if(!empty($taxrate))
							$productpriceinctax=$product_attribute['pprice']*($taxrate/100+1)+$ecotax;
						else
							$productpriceinctax=$product_attribute['pprice']+$ecotax;
						echo ("<cell>".number_format($productpriceinctax, 6, '.', '')."</cell>");
						break;
					case'price':
						if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
						{
							$ecotax = (_s('CAT_PROD_ECOTAXINCLUDED') ? $product_attribute['ecotax']*SCI::getEcotaxTaxRate() : 0 );
							if(!empty($taxrate))
                                echo ("<cell>".number_format(($product_attribute['price']+$product_attribute['pprice'])*($taxrate/100+1) + $ecotax, 6, '.', '')."</cell>");
							else
								echo ("<cell>".number_format($product_attribute['price']+$product_attribute['pprice'] + $ecotax, 6, '.', '')."</cell>");
						}
						elseif (version_compare(_PS_VERSION_, '1.3.0.0', '>='))
						{
							$ecotax = (_s('CAT_PROD_ECOTAXINCLUDED') ? $product_attribute['ecotax']*SCI::getEcotaxTaxRate() : 0 );
							if(!empty($taxrate))
								echo ("<cell>".number_format($product_attribute['price']+$product_attribute['pprice']*($taxrate/100+1)+$ecotax, 6, '.', '')."</cell>");
							else
								echo ("<cell>".number_format($product_attribute['price']+$product_attribute['pprice']+$ecotax, 6, '.', '')."</cell>");
						}else{
							if(!empty($taxrate))
								echo ("<cell>".number_format($product_attribute['price']+$product_attribute['pprice']*($taxrate/100+1), 6, '.', '')."</cell>");
							else
								echo ("<cell>".number_format($product_attribute['price']+$product_attribute['pprice'], 6, '.', '')."</cell>");
						}
						break;
					case'ppriceextax':
						echo ("<cell>".number_format($product_attribute['pprice'], 6, '.', '')."</cell>");
						break;
					case'priceextax':
						if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
						{
							echo ("<cell>".number_format($product_attribute['price']+$product_attribute['pprice'], 6, '.', '')."</cell>");
						}else{
							if(!empty($taxrate))
								echo ("<cell>".number_format($product_attribute['price']/($taxrate/100+1)+$product_attribute['pprice'], 6, '.', '')."</cell>");
							else
								echo ("<cell>".number_format($product_attribute['price']+$product_attribute['pprice'], 6, '.', '')."</cell>");
						}
						break;
					case'margin':
						echo "<cell></cell>";
						break;
					case'reference':
						echo ("<cell".(!empty($reductionNameColor)?' bgColor="'.$reductionNameColor.'"':"")."><![CDATA[".$product_attribute[$col]."]]></cell>");
					break;
					case 'unit_price_impact_inc_tax':
						$temp_val = 0;
						if(!empty($taxrate)) {
							$temp_val = number_format($product_attribute['unit_price_impact']*($taxrate/100+1), 6, '.', '');
						}
						echo "<cell>".$temp_val."</cell>";
						break;
					default:
						if ($col!='')
							echo ("<cell><![CDATA[".$product_attribute[$col]."]]></cell>");
				}
			}
			echo '<userdata name="attr_ids">'.pSQL($combi_attr_ids).'</userdata>'."\n";
			echo ("</row>\n");
		}
	}
	
	function getAttributesForGroup($id_group)
	{
		global $id_lang,$shop_id,$sortByPosition;
		
		$sql = '
		SELECT al.`name` AS name, a.`id_attribute` '.(version_compare(_PS_VERSION_, '1.5.0.1', '>=')?', a.position ':'').'
		FROM `'._DB_PREFIX_.'attribute` a
		LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.intval($id_lang).')'.
		(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($shop_id)?'LEFT JOIN `'._DB_PREFIX_.'attribute_shop` ats ON (a.`id_attribute` = ats.`id_attribute` AND ats.id_shop = "'.$shop_id.'")':'').'
		WHERE a.`id_attribute_group` = '.intval($id_group).'
		ORDER BY '.(version_compare(_PS_VERSION_, '1.5.0.1', '>=')?'a.position':'al.name').'';
		$attributes=Db::getInstance()->ExecuteS($sql);
		$res='';
		foreach($attributes as $attr)
		{
			if(version_compare(_PS_VERSION_, '1.5.0.1', '>='))
			{
				if(!empty($sortByPosition))
					$res.=str_pad($attr['position'], 6, "0", STR_PAD_LEFT).';;;';
				else
					$res.=str_replace('&','|and|',$attr['name']).';;;';
			}
			$res.=$attr['id_attribute'].'|||'.str_replace('&','|and|',$attr['name']).'@@@';
		}
		return $res;
	}

	//XML HEADER

	//include XML Header (as response will be in xml format)
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");

	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		$sql='SELECT t.rate,ps.price,p.weight,ps.ecotax,p.id_shop_default
		FROM `'._DB_PREFIX_.'product` p
		INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (p.`id_product` = ps.`id_product` AND ps.`id_shop` = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
    	LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
    		LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
		WHERE p.id_product='.intval($id_product);
		
	}
	elseif (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
	{
		$sql='SELECT t.rate,p.price,p.weight,p.ecotax
		FROM `'._DB_PREFIX_.'product` p
		LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
    		LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
		WHERE p.id_product='.intval($id_product);
	}
	else
	{
		$sql='SELECT t.rate,p.price,p.weight,p.ecotax 
				FROM `'._DB_PREFIX_.'product` p, `'._DB_PREFIX_.'tax` t 
				WHERE p.id_product='.intval($id_product).' 
					AND t.id_tax=p.id_tax';
	}
	$p=Db::getInstance()->getRow($sql);
	$taxrate=$p['rate'];
	$productprice=$p['price'];
	$productweight=$p['weight'];
	$productecotax=$p['ecotax'];
	if(!empty($p['id_shop_default']))
		$default_shop=$p['id_shop_default'];
	else
		$default_shop=0;
	$groups=0;
	$xml='';
	
	$shop_id = SCI::getSelectedShop();
	if(empty($shop_id))
		$shop_id = $default_shop;
	
	

	$sourceGridFormat=SCI::getGridViews("combination");
	$sql_gridFormat = $sourceGridFormat;
	sc_ext::readCustomCombinationsGridConfigXML('gridConfig');
	$gridFormat=$sourceGridFormat;
	$all_cols = explode(',',$gridFormat);

/*	function getColIndex($col)
	{
		global $cols;
		$tmp=array_flip($cols);
		return (sc_array_key_exists($col,$tmp) ? $tmp[$col] : -1 );
	}*/
	

		/*
		0: coef = PV HT - PV HT
		1: coef = (PV HT - PA HT) / PA HT
		2: coef = PV HT / PA HT
		3: coef = PV TTC / PA HT
		4: coef = (PV TTC - PA HT) / PA HT
		*/
		/*$marginMatrix=array(
					0=>'[=c'.getColIndex('priceextax').'-c'.getColIndex('wholesale_price').']',
					1=>'[=(c'.getColIndex('priceextax').'-c'.getColIndex('wholesale_price').')/c'.getColIndex('wholesale_price').']',
					2=>'[=c'.getColIndex('priceextax').'/c'.getColIndex('wholesale_price').']',
					3=>'[=c'.getColIndex('price').'/c'.getColIndex('wholesale_price').']',
					4=>'[=(c'.getColIndex('price').'-c'.getColIndex('wholesale_price').')*100/c'.getColIndex('wholesale_price').']',
					5=>'[=(c'.getColIndex('priceextax').'-c'.getColIndex('wholesale_price').')*100/c'.getColIndex('priceextax').']'
					);*/
		$marginMatrix_form=array(
				0=>'{price}-{wholesale_price}',
				1=>'({price}-{wholesale_price})*100/{wholesale_price}',
				2=>'{price}/{wholesale_price}',
				3=>'{price_inc_tax}/{wholesale_price}',
				4=>'({price_inc_tax}-{wholesale_price})*100/{wholesale_price}',
				5=>'({price}-{wholesale_price})*100/{price}'
		);
	
		$colSettings=array();
		$colSettings=SCI::getGridFields("combination");
		sc_ext::readCustomCombinationsGridConfigXML('colSettings');

	
	// new combination creation from product without combination: display new attribute group columns
	if ($forceGroups!='' && $forceGroups!='undefined')
	{
		$idsGroup=explode(',',$forceGroups);
		$groups=count($idsGroup);
		$sql = '
		SELECT name, ag.id_attribute_group
		FROM '._DB_PREFIX_.'attribute_group ag
		LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (ag.id_attribute_group = agl.id_attribute_group AND agl.id_lang = '.intval($id_lang).')'.
		(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($shop_id)?'LEFT JOIN `'._DB_PREFIX_.'attribute_group_shop` ags ON (ag.`id_attribute_group` = ags.`id_attribute_group` AND ags.id_shop = "'.(int)$shop_id.'")':'').'
	 	WHERE ag.id_attribute_group IN ('.$forceGroups.')';
		$res=Db::getInstance()->ExecuteS($sql);
		foreach($res as $g)
		{
			$attributeGroupsNames[]=$g['name'];
			$attributeGroupsIDs[]=$g['id_attribute_group'];
		}
		$tmp='';
		for($i=0;$i < $groups;$i++)
		{
			$tmp.='attr_'.$i.'_'.$attributeGroupsIDs[$i].',';
		}
		if ($tmp!='')
		{
			$gridFormat=str_replace('ATTR',trim($tmp,','),$gridFormat);
		}else{
			$gridFormat=str_replace('ATTR,','',$gridFormat);
		}
		$cols=explode(',',$gridFormat);
	}


	function getColSettingsAsXML()
	{
		global $sql,$name_field_attr,$sql_gridFormat,$sortByPosition,$arrIdAvailableLater,$attributeGroupsNames,$colSettings,$view,$shop_id,$attributeGroupsIDs,$id_lang,$id_product,$taxrate,$groups,$cols,$gridFormat,$sourceGridFormat,$combArray,$combinaison,$sc_active,$sql2,$results;

		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$shop = new Shop($shop_id);
			$shop_group = $shop->getGroup();
		}
		$cols=explode(',',$sql_gridFormat);
		
		$pa_cols = array();
		foreach ($cols as $col)
		{
			if(!sc_in_array($col, array('ATTR','quantityupdate','pprice','ppriceextax','priceextax','margin','pweight','quantity_physical','quantity_usable','quantity_real','available_later','unit_price_impact_inc_tax'),"catCombiGet_specialFields"))
				$pa_cols[] = $col;
		}

		$sql = '
		SELECT pa.id_product_attribute,pa.id_product '.((!empty($pa_cols))?',pa.`'.implode("`,pa.`",$pa_cols):'').'`, ag.`id_attribute_group`, agl.`name` AS group_name, al.`name` AS attribute_name, a.`id_attribute` '.(version_compare(_PS_VERSION_, '1.5.0.1', '>=')?', a.position ':'').', p.`price` AS pprice, p.`weight` AS pweight, p.`ecotax` AS pecotax'.
		/*(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?",ps.product_supplier_reference AS supplier_reference":'').*/
		(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($shop_id)?",p_shop.price AS pprice,p_shop.ecotax AS pecotax":'').
		(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($shop_id)?",(sa.quantity) AS quantity":'').
		(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($shop_id)?',pa_shop.wholesale_price,pa_shop.price,pa_shop.ecotax,pa_shop.weight,pa_shop.unit_price_impact,pa_shop.minimal_quantity,pa_shop.default_on,pa_shop.available_date':'').
		(SCI::getConfigurationValue("SC_DELIVERYDATE_INSTALLED")=="1"?',pa.id_sc_available_later':'');
		sc_ext::readCustomCombinationsGridConfigXML('SQLSelectDataSelect');

/*
Problème de compatibilité avec anciennes extensions :
il faut rajouter si version XML < 2 (tag à ajouter dans le xml) : 
	si table du champ == product_attribute alors
		rajouter [ 'pa.' + nom du champ ] dans la requete

*/

		$sql.=' FROM `'._DB_PREFIX_.'product_attribute` pa
		LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
		LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
		LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
		LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.intval($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.intval($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'product` p ON (pa.`id_product` = p.`id_product`)';
		sc_ext::readCustomCombinationsGridConfigXML('SQLSelectDataLeftJoin');
		$sql.=
		(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($shop_id)?' LEFT JOIN `'._DB_PREFIX_.'product_shop` p_shop ON (p.`id_product` = p_shop.`id_product`)':'').
	 	(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?" LEFT JOIN "._DB_PREFIX_."product_supplier ps ON (ps.id_product=p.id_product AND ps.id_product_attribute=pa.id_product_attribute AND ps.id_supplier=p.id_supplier)":'').
	 	(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($shop_id)?" LEFT JOIN "._DB_PREFIX_."stock_available sa ON (sa.id_product_attribute=pa.id_product_attribute ".($shop_group->share_stock ? "AND sa.id_shop_group=".(int)$shop_group->id." AND sa.id_shop=0":"AND sa.id_shop=".(int)$shop_id).")":'').
	 	(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && version_compare(_PS_VERSION_, '1.6.0.0', '<') && !empty($shop_id)?" LEFT JOIN "._DB_PREFIX_."product_attribute_shop pa_shop ON (pa_shop.id_product_attribute=pa.id_product_attribute AND pa_shop.id_shop=".(int)$shop_id.")":'').
	 	(version_compare(_PS_VERSION_, '1.6.0.0', '>=') && !empty($shop_id)?" INNER JOIN "._DB_PREFIX_."product_attribute_shop pa_shop ON (pa_shop.id_product_attribute=pa.id_product_attribute AND pa_shop.id_shop=".(int)$shop_id.")":'').
	 	' WHERE pa.`id_product` = '.intval($id_product).'
	 	'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($shop_id)?' AND p_shop.id_shop = "'.$shop_id.'" ':'').'
	 	GROUP BY pac.`id_product_attribute`, pac.`id_attribute`
		ORDER BY pa.`id_product_attribute`, group_name';
		$results=Db::getInstance()->ExecuteS($sql);
		//echo "\n\n\n".$sql;die();
		
		$multiStoresFields=array('wholesale_price','price','ecotax','weight','unit_price_impact','minimal_quantity','default_on','available_date');

		$combArray = array();
		foreach($results as $combinaison)
		{
			$combArray[$combinaison['id_product_attribute']]['id'] = $combinaison['id_product_attribute'];
			$combArray[$combinaison['id_product_attribute']]['default_on'] = $combinaison['default_on'];
			$combArray[$combinaison['id_product_attribute']]['wholesale_price'] = (sc_array_key_exists('wholesale_price',$combinaison) ? number_format($combinaison['wholesale_price'], (_s('CAT_PROD_WHOLESALEPRICE4DEC')?4:2), '.', '') : 'NA');
			$combArray[$combinaison['id_product_attribute']]['pprice'] = $combinaison['pprice'];
			$combArray[$combinaison['id_product_attribute']]['price'] = $combinaison['price'];
			$combArray[$combinaison['id_product_attribute']]['pweight'] = $combinaison['pweight'];
			$combArray[$combinaison['id_product_attribute']]['weight'] = number_format($combinaison['weight'], 6, '.', '');
			$combArray[$combinaison['id_product_attribute']]['reference'] = $combinaison['reference'];
		    $combArray[$combinaison['id_product_attribute']]['supplier_reference'] = $combinaison['supplier_reference'];
		    if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		    	$combArray[$combinaison['id_product_attribute']]['available_date'] = $combinaison['available_date'];
		    $combArray[$combinaison['id_product_attribute']]['ean13'] = $combinaison['ean13'];
			$combArray[$combinaison['id_product_attribute']]['location'] = $combinaison['location'];
			$combArray[$combinaison['id_product_attribute']]['quantity'] = (int)$combinaison['quantity'];
			$combArray[$combinaison['id_product_attribute']]['pecotax'] = number_format($combinaison['pecotax'], 6, '.', '');
			$combArray[$combinaison['id_product_attribute']]['ecotax'] = number_format($combinaison['ecotax'], 6, '.', '');
			if(!empty($combinaison['id_attribute_group']))
			{
				if(version_compare(_PS_VERSION_, '1.5.0.1', '>='))
					$combArray[$combinaison['id_product_attribute']]['attributes'][] = array($combinaison['group_name'], $combinaison['attribute_name'], $combinaison['id_attribute'], $combinaison['id_attribute_group'],$combinaison['position']);
				else
					$combArray[$combinaison['id_product_attribute']]['attributes'][] = array($combinaison['group_name'], $combinaison['attribute_name'], $combinaison['id_attribute'], $combinaison['id_attribute_group']);
			}
			if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
			{
				$combArray[$combinaison['id_product_attribute']]['unit_price_impact'] = $combinaison['unit_price_impact'];
				$combArray[$combinaison['id_product_attribute']]['upc'] = $combinaison['upc'];
				$combArray[$combinaison['id_product_attribute']]['minimal_quantity'] = $combinaison['minimal_quantity'];
			}
			if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
			{
				$combArray[$combinaison['id_product_attribute']]['isbn'] = $combinaison['isbn'];
			}
			if ($sc_active)
				$combArray[$combinaison['id_product_attribute']]['sc_active'] = $combinaison['sc_active'];
			
			if(SCI::getConfigurationValue("SC_DELIVERYDATE_INSTALLED")=="1")
			{
				if(!empty($combinaison['id_sc_available_later']) && !empty($arrIdAvailableLater[$combinaison['id_sc_available_later']]))
					$combArray[$combinaison['id_product_attribute']]['available_later'] = $arrIdAvailableLater[$combinaison['id_sc_available_later']];
				else
					$combArray[$combinaison['id_product_attribute']]['available_later'] = "";
			}
			
			sc_ext::readCustomCombinationsGridConfigXML('definition');

			if (!sc_in_array($combinaison['group_name'],$attributeGroupsNames,"catCombiGet_attributeGroupsNames")){
				if(!empty($combinaison['id_attribute_group']))
				{
					$attributeGroupsNames[]=$combinaison['group_name'].'';
					$attributeGroupsIDs[]=$combinaison['id_attribute_group'];
				}
			}
//			sort($combArray[$combinaison['id_product_attribute']]['attributes']); // ne pas trier ici sinon décalage dans les colonnes
		}

		/*if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($shop_id))
			foreach ($combArray AS $id_product_attribute => $product_attribute)
		    {
		    	//$combArray[$id_product_attribute]['quantity'] = StockAvailable::getQuantityAvailableByProduct($id_product,$id_product_attribute, $shop_id);
	
					$sql2="SELECT `".join('`,`',$multiStoresFields)."` FROM "._DB_PREFIX_."product_attribute_shop
									WHERE id_product_attribute=".(int)$id_product_attribute." AND id_shop=".(int)$shop_id;
					$res2=Db::getInstance()->getRow($sql2);
					foreach($multiStoresFields as $field)
					{
						if($field=="wholesale_price")
						{
							if(empty($res2[$field]))
								$res2[$field] = 0;
							$combArray[$id_product_attribute][$field] = number_format($res2[$field], (_s('CAT_PROD_WHOLESALEPRICE4DEC')?4:2), '.', '');
						}	
						else
							$combArray[$id_product_attribute][$field] = $res2[$field];
					}
		    }*/

		if ($groups==0) $groups=count($attributeGroupsIDs);
		
		if (!_s('CAT_PROD_COMBI_METHOD')) // not specific grid
		{
			$tmp='';
			for($i=0;$i < $groups;$i++)
			{
				$tmp.='attr_'.$i.'_'.$attributeGroupsIDs[$i].',';
			}
			if ($tmp!='')
			{
				$gridFormat=str_replace('ATTR',trim($tmp,','),$gridFormat);
			}else{
				$gridFormat=str_replace('ATTR,','',$gridFormat);
			}
		}
		
		$cols=explode(',',$gridFormat);

		for($i=0;$i < $groups;$i++)
		{
			if (!_s('CAT_PROD_COMBI_METHOD'))
			{
				$options = array();
				$attrs = AttributeGroup::getAttributes((int)$id_lang, (int)$attributeGroupsIDs[$i]);
				foreach($attrs as $attr)
				{
					$name_value = $attr["name"];
					$name_value = str_replace("&", _l("and"), $name_value);
					$name_value = str_replace("<", "1", $name_value);
					$name_value = str_replace(">", "2", $name_value);
					$name_value = str_replace('"', "''", $name_value);
					if(version_compare(_PS_VERSION_, '1.5.0.1', '>='))
					{
						if(!empty($attr["name"]) || $attr["name"]==="0")
						{
							if(!empty($sortByPosition))
								$options[str_pad($attr['position'], 6, "0", STR_PAD_LEFT).';;;'.$name_value."|||".$attr["id_attribute"]] = $attr["name"];
							else
								$options[$name_value.';;;'.$name_value."|||".$attr["id_attribute"]] = $attr["name"];
						}
					}
					else
					$options[$name_value."|||".$attr["id_attribute"]] = $attr["name"];
				}
				if(version_compare(_PS_VERSION_, '1.5.0.1', '>='))
				{
					if(!empty($sortByPosition))
						$options["0000000;;;|||"]="-";
					else
						$options[";;;|||"]="-";
					ksort($options);
				}
				else
				{
					$options["|||"]="-";
				asort($options);
				}
				$name_group = $attributeGroupsNames[$i];
				$name_group = str_replace("&", _l("and"), $name_group);
				$colSettings['attr_'.$i.'_'.$attributeGroupsIDs[$i].'']=array('text' => $name_group,'width'=>90,'align'=>'left','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#text_filter', 'options'=>$options);

			}
			else	
			{
				$name_group = $attributeGroupsNames[$i];
				$name_group = str_replace("&", _l("and"), $name_group);
				$colSettings['attr_'.$i.'_'.$attributeGroupsIDs[$i].'']=array('text' => $name_group,'width'=>90,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
			}
			$name_field_attr['attr_'.$i] = 'attr_'.$i.'_'.$attributeGroupsIDs[$i].'';
		}



		$xml='';
		foreach($cols AS $id => $col)
		{
			 $xml.='<column id="'.$col.'"'.(is_array($colSettings[$col]) && sc_array_key_exists('format',$colSettings[$col])?' format="'.$colSettings[$col]['format'].'"':'').' width="'.($view=='grid_combination_price'&&$col=='id' ? $colSettings[$col]['width']+50:$colSettings[$col]['width']).'" align="'.$colSettings[$col]['align'].'" type="'.$colSettings[$col]['type'].'" sort="'.$colSettings[$col]['sort'].'" color="'.$colSettings[$col]['color'].'"><![CDATA['.$colSettings[$col]['text'].']]>';
			if (is_array($colSettings[$col]) && sc_array_key_exists('options',$colSettings[$col]))
			{
				foreach($colSettings[$col]['options'] AS $k => $v)
				{
					$xml.='<option value="'.str_replace('"','\'',$k).'"><![CDATA['.$v.']]></option>'; 
				}
			}
			$xml.='</column>'."\n";
		}
		return $xml;
	}

	function getFilterColSettings()
	{
		global $cols,$colSettings;
		$filters='';
		$num = 0;
		foreach($cols AS $id => $col)
		{
			if($num>0)
				$filters.=",";
			if($colSettings[$col]['filter']=="na")
				$colSettings[$col]['filter'] = "";
			$filters.=$colSettings[$col]['filter'];
			$num++;
		}
		return $filters;
	}
	
	function getFooterColSettings()
	{
		global $cols,$colSettings;
	
		$footer='';
		foreach($cols AS $id => $col)
		{
			if (sc_array_key_exists($col,$colSettings) && sc_array_key_exists('footer',$colSettings[$col]))
				$footer.=$colSettings[$col]['footer'].',';
			else
				$footer.=',';
		}
		return $footer;
	}
?>
<rows id="0">
<head>
<?php
echo getColSettingsAsXML();
echo '<afterInit>
<call command="attachHeader"><param>'.getFilterColSettings().'</param></call>
<call command="attachFooter"><param><![CDATA['.getFooterColSettings().']]></param></call>
<call command="enableMultiselect"><param>1</param></call>
</afterInit>';
?>
</head>
<userdata name="marginMatrix_form"><?php echo $marginMatrix_form[_s('CAT_PROD_GRID_MARGIN_OPERATION')]; ?></userdata>
<?php
	echo '<userdata name="id_product">'.$id_product.'</userdata>'."\n";
	echo '<userdata name="taxrate">'.$taxrate.'</userdata>'."\n";
	echo '<userdata name="productprice">'.number_format($productprice,6, '.', '').'</userdata>'."\n";
	echo '<userdata name="productweight">'.$productweight.'</userdata>'."\n";
	echo '<userdata name="productecotax">'.$productecotax.'</userdata>'."\n";
	echo '<userdata name="productIDsupplier">'.$productIDsupplier.'</userdata>'."\n";

	if (_s('CAT_PROD_COMBI_METHOD')) {
		$uisettings = uisettings::getSetting('cat_combination_separate' . count($cols));
	} else {
		$uisettings = uisettings::getSetting('cat_combination' . count($cols));
	}

	foreach($name_field_attr as $tag=>$name)
		$uisettings = str_replace("{".$tag."}", $name, $uisettings);
	echo '<userdata name="uisettings">'.$uisettings.'</userdata>'."\n";
	
	if(SCI::getSelectedShop()==0)
		echo '<userdata name="default_shop">'.$default_shop.'</userdata>'."\n";
	for($i=0;$i < $groups;$i++)
	{
		echo '<userdata name="attrValues_'.$attributeGroupsIDs[$i].'"><![CDATA['.getAttributesForGroup($attributeGroupsIDs[$i]).']]></userdata>'."\n";
	}
	sc_ext::readCustomCombinationsGridConfigXML('gridUserData');
	if ($forceGroups=='' || $forceGroups=='undefined') 
		getRowsFromDB();
	echo '<userdata name="productpriceinctax">'.number_format($productpriceinctax,6, '.', '').'</userdata>'."\n";
?>
</rows>
