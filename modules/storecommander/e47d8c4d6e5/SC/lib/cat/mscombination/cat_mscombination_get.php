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
	$idlist=Tools::getValue('idlist',0);
	$id_lang=intval(Tools::getValue('id_lang'));
	$cntProducts=count(explode(',',$idlist));
	$shops = array();
	$sc_active=SCI::getConfigurationValue('SC_PLUG_DISABLECOMBINATIONS',0);
	
	$filters = "";
	$colonnes = "";
	$xml = "";
	
	$sql ="SELECT *
			FROM "._DB_PREFIX_."shop
			WHERE deleted!='1'
			ORDER BY name";
	$res = Db::getInstance()->executeS($sql);
	foreach($res as $shop)
	{
		$shop['name'] = str_replace("&", _l('and'), $shop['name']);
		$shops[$shop["id_shop"]] = $shop["name"]." (#".$shop["id_shop"].")";
	}

	// Tax
	$arrTax=array(0 => '-');
	$tax=array(0 => 0);
		if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
		{
			$sql='SELECT trg.name, trg.id_tax_rules_group,t.rate
			FROM `'._DB_PREFIX_.'tax_rules_group` trg
			LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (trg.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
  	  LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
	    WHERE trg.active=1';
			$res=Db::getInstance()->ExecuteS($sql);
			foreach($res as $row){
				if ($row['name']=='') $row['name']=' ';
				$arrTax[$row['id_tax_rules_group']]=$row['name'];
				$tax[$row['id_tax_rules_group']]=$row['rate'];
			}
		}else{
			$sql = "SELECT id_tax,rate FROM "._DB_PREFIX_."tax";
			$res=Db::getInstance()->ExecuteS($sql);
			foreach($res as $row){
				$arrTax[$row['id_tax']]=$row['rate'];
				$tax[$row['id_tax']]=$row['rate'];
			}
		}
		
	
	// SETTINGS, FILTERS AND COLONNES
	$sourceGridFormat=SCI::getGridViews("mscombination");
	$sql_gridFormat = $sourceGridFormat;
	sc_ext::readCustomMsCombinationGridConfigXML('gridConfig');
	if(empty($sc_active))
		$sourceGridFormat = str_replace(",sc_active,", ",", $sourceGridFormat);
	$gridFormat=$sourceGridFormat;
	$cols=explode(',',$gridFormat);
	$all_cols = explode(',',$gridFormat);
	
	$colSettings=array();
	$colSettings=SCI::getGridFields("mscombination");
	sc_ext::readCustomMsCombinationGridConfigXML('colSettings');

	/*
	 0: coef = PV HT - PV HT
	1: coef = (PV HT - PA HT) / PA HT
	2: coef = PV HT / PA HT
	3: coef = PV TTC / PA HT
	4: coef = (PV TTC - PA HT) / PA HT
	*/
	function getColIndex($col)
	{
		global $list_shop_fields;
		$tmp=explode(",",$list_shop_fields);
		foreach($tmp as $key=>$field)
		{
			if($field==$col)
				return $key+7;
		}
		return -1;
	}
	$marginMatrix=array(
			0=>'[=c'.getColIndex('priceextax').'-c'.getColIndex('wholesale_price').']',
			1=>'[=(c'.getColIndex('priceextax').'-c'.getColIndex('wholesale_price').')/c'.getColIndex('wholesale_price').']',
			2=>'[=c'.getColIndex('priceextax').'/c'.getColIndex('wholesale_price').']',
			3=>'[=c'.getColIndex('price').'/c'.getColIndex('wholesale_price').']',
			4=>'[=(c'.getColIndex('price').'-c'.getColIndex('wholesale_price').')*100/c'.getColIndex('wholesale_price').']',
			5=>'[=(c'.getColIndex('priceextax').'-c'.getColIndex('wholesale_price').')*100/c'.getColIndex('priceextax').']'
	);
	$marginMatrix_form=array(
			0=>'{price}-{wholesale_price}',
			1=>'({price}-{wholesale_price})*100/{wholesale_price}',
			2=>'{price}/{wholesale_price}',
			3=>'{price_inc_tax}/{wholesale_price}',
			4=>'({price_inc_tax}-{wholesale_price})*100/{wholesale_price}',
			5=>'({price}-{wholesale_price})*100/{price}'
	);
	
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
	
	function getFilterColSettings()
	{
		global $cols,$colSettings;
	
		$filters='';
		foreach($cols AS $id => $col)
		{
			if($colSettings[$col]['filter']=="na")
				$colSettings[$col]['filter'] = "";
			$filters.=$colSettings[$col]['filter'].',';
		}
		$filters=trim($filters,',');
		return $filters;
	}
	
	function getColSettingsAsXML()
	{
		global $cols,$colSettings;
		
		$uiset = uisettings::getSetting('cat_mscombination');
		$tmp = explode('|',$uiset);
		$tmp = explode('-',$tmp[2]);
		$sizes = array();
		foreach($tmp AS $v)
		{
			$s = explode(':',$v);
			$sizes[$s[0]] = $s[1];
		}
		$tmp = explode('|',$uiset);
		$tmp = explode('-',$tmp[0]);
		$hidden = array();
		foreach($tmp AS $v)
		{
			$s = explode(':',$v);
			$hidden[$s[0]] = $s[1];
		}
		
		$xml='';
		foreach($cols AS $id => $col)
		{
			$xml.='<column id="'.$col.'"'.(sc_array_key_exists('format',$colSettings[$col])?
					' format="'.$colSettings[$col]['format'].'"':'').
					' width="'.( sc_array_key_exists($col,$sizes) ? $sizes[$col] : $colSettings[$col]['width']).'"'.
					' hidden="'.( sc_array_key_exists($col,$hidden) ? $hidden[$col] : 0 ).'"'.
					' align="'.$colSettings[$col]['align'].'" 
					type="'.$colSettings[$col]['type'].'" 
					sort="'.$colSettings[$col]['sort'].'" 
					color="'.$colSettings[$col]['color'].'">'.$colSettings[$col]['text'];
			if (sc_array_key_exists('options',$colSettings[$col]))
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
	
	function generateValue($col, $product_attribute, $product, $taxrate)
	{
		global $colSettings,$id_lang;
		
		$product_attribute = (array)$product_attribute;
		$product = (array)$product;
		
		if(($product_attribute['ecotax']*1)==0)
			$product_attribute['ecotax']=$product['ecotax'];
		
		$return = "";
		switch($col){
			case'ecotax':
				$ecotax = 0;
				if(_s('CAT_PROD_ECOTAXINCLUDED'))
					$ecotax = $product_attribute['ecotax'] * SCI::getEcotaxTaxRate();
				$return .= "<cell>".(  $ecotax )."</cell>";
				break;
			case'taxrate':
				$return .= "<cell>".number_format($taxrate, 2, '.', '')."</cell>";
				break;
			case'price':
				$ecotax = 0;
				if(_s('CAT_PROD_ECOTAXINCLUDED'))
					$ecotax = $product_attribute['ecotax'] * SCI::getEcotaxTaxRate();
				$return.=("<cell>".($product_attribute['price']*($taxrate/100+1)+$product['price']*($taxrate/100+1) + $ecotax)."</cell>");
				break;
			case'pprice':
				$ecotax = 0;
				if(_s('CAT_PROD_ECOTAXINCLUDED'))
					$ecotax = $product['ecotax'] * SCI::getEcotaxTaxRate();
				$return.=("<cell>".number_format($product['price']*($taxrate/100+1)+$ecotax, 2, '.', '')."</cell>");
				break;
			case'ppriceextax':
				$return.=("<cell>".number_format($product['price'], 2, '.', '')."</cell>");
				break;
			case'priceextax':
				$return.=("<cell>".($product_attribute['price']+$product['price'])."</cell>");
				break;
			case'margin':
				$return.="<cell></cell>";
				break;
			case'weight':
				$return.="<cell>".number_format($product_attribute['weight']+$product['weight'], 6, '.', '')."</cell>";
				break;
			case'wholesale_price':
				$return.="<cell>".number_format($product_attribute['wholesale_price'], (_s('CAT_PROD_WHOLESALEPRICE4DEC')?4:2), '.', '')."</cell>";
				break;
			case'default_on':
				$return.="<cell>".(!empty($product_attribute['default_on'])?_l('Yes'):_l('No'))."</cell>";
				break;
			case'quantity':
				//$return .="<cell>".StockAvailable::getQuantityAvailableByProduct($prodrow['id'], $product_attribute['id'], $prodrow['id_selected_shop'])."</cell>";
				$return .= "<cell>".SCI::getProductQty((int)$product['id'], (int)$product_attribute['id'], null, (int)$product['id_selected_shop'])."</cell>";
				break;
			case'supplier_reference':
				$sql_supplier ="SELECT *
				FROM "._DB_PREFIX_."product_supplier
				WHERE id_product = '".$product['id']."'
					AND id_product_attribute = '".$product_attribute['id']."'
					AND id_supplier = '".$product['id_supplier']."'";
				$product_supplier = Db::getInstance()->getRow($sql_supplier);
				$ref_supp  = "";
				if(!empty($product_supplier['product_supplier_reference']))
					$ref_supp = $product_supplier['product_supplier_reference'];
				$return .= '<cell><![CDATA['.$ref_supp.']]></cell>';
				break;
			case'name':
				$name = "";
				$sql_attr ="SELECT agl.name as gp, al.name
						FROM "._DB_PREFIX_."product_attribute_combination pac
							INNER JOIN "._DB_PREFIX_."attribute a ON pac.id_attribute = a.id_attribute
								INNER JOIN "._DB_PREFIX_."attribute_group_lang agl ON a.id_attribute_group = agl.id_attribute_group
							INNER JOIN "._DB_PREFIX_."attribute_lang al ON pac.id_attribute = al.id_attribute
						WHERE pac.id_product_attribute = '".$product_attribute['id']."'
							AND agl.id_lang = '".$id_lang."'
							AND al.id_lang = '".$id_lang."'
						GROUP BY a.id_attribute
						ORDER BY agl.name";
				$res_attr = Db::getInstance()->executeS($sql_attr);
				foreach($res_attr as $attr)
				{
					if(!empty($attr["gp"]) && !empty($attr["name"]))
					{
						if(!empty($name))
							$name .= ", ";
						$name .= $attr["gp"]." : ".$attr["name"];
					}
				}
				$return .= '<cell><![CDATA['.$name.']]></cell>';
				break;
			default:
				if (sc_array_key_exists('buildDefaultValue',$colSettings[$col]) && $colSettings[$col]['buildDefaultValue']!='')
				{
					if ($colSettings[$col]['buildDefaultValue']=='ID')
						$return .= "<cell>ID".$product_attribute['id_product']."</cell>";
				}else{
					if ($col=='id_product')
						$return .= "<cell>".$product['id']."</cell>";
					elseif ($col=='id_product_attribute')
						$return .= "<cell style=\"color:".($product_attribute['default_on']?'#0000FF':'#999999')."\">".$product_attribute['id']."</cell>";
					elseif ($col=='id_shop')
						$return .= "<cell>".$product['id_selected_shop']."</cell>";
					else
						$return .= "<cell><![CDATA[".$product_attribute[$col]."]]></cell>";
				}
		}
		return $return;
	}
	
	/*
	 * PRODUCT SHOP
	 */
	$sql ="SELECT pas.id_product_attribute,pas.id_shop, pa.id_product ".($sc_active?" ,pa.sc_active ":"");
	sc_ext::readCustomMsCombinationGridConfigXML('SQLSelectDataSelect');
	$sql .=" FROM "._DB_PREFIX_."product_attribute_shop pas
				INNER JOIN "._DB_PREFIX_."product_attribute pa ON pas.id_product_attribute = pa.id_product_attribute
			".((!empty($sc_agent->id_employee))?" INNER JOIN "._DB_PREFIX_."employee_shop es ON (es.id_shop = pas.id_shop AND es.id_employee = '".(int)$sc_agent->id_employee."') ":"")." ";
	sc_ext::readCustomMsCombinationGridConfigXML('SQLSelectDataLeftJoin');
	$sql .=" WHERE pa.id_product IN (".psql($idlist).")
			ORDER BY pa.id_product, pas.id_shop";
	$res = Db::getInstance()->executeS($sql);
	foreach($res as $product_attr_by_shop)
	{
		if(!empty($product_attr_by_shop["id_product"]) && !empty($product_attr_by_shop["id_shop"]) && !empty($product_attr_by_shop["id_product_attribute"]))
		{
			$product = new Product($product_attr_by_shop["id_product"], false, $id_lang,$product_attr_by_shop["id_shop"]);
			$product_attr = new Combination($product_attr_by_shop["id_product_attribute"],null,$product_attr_by_shop["id_shop"]);
			$shop = new Shop($product_attr_by_shop["id_shop"]);
			$product->id_selected_shop = $product_attr_by_shop['id_shop'];
			
			$sql='SELECT t.rate
				FROM `'._DB_PREFIX_.'product_shop` p
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getConfigurationValue("PS_COUNTRY_DEFAULT", null, 0, (int)$product_attr_by_shop["id_shop"]).' AND tr.`id_state` = 0)
		   		LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				WHERE p.id_product='.intval($product_attr_by_shop['id_product']).'
					AND p.id_shop = '.$product_attr_by_shop["id_shop"];
			$p=Db::getInstance()->executeS($sql);
			//echo $sql."\n";
			$taxrate=$p[0]['rate'];
			
			if(!empty($sc_active))
				$product_attr->sc_active = $product_attr_by_shop["sc_active"];
			
			$xml .="<row id=\"".$product_attr_by_shop['id_product']."_".$product_attr_by_shop['id_product_attribute']."_".$product_attr_by_shop['id_shop']."\">";
			sc_ext::readCustomMsCombinationGridConfigXML('rowUserData',(array)$product_attr);
			foreach ($cols as $field)
			{
				if(!empty($field) && !empty($colSettings[$field]))
				{
					$xml .= generateValue($field, $product_attr,$product,$taxrate);
				}
			}
			$xml .="</row>";
		}
	}

	$sql ="SELECT pas.id_product_attribute, pas.id_shop
			FROM "._DB_PREFIX_."product_attribute_shop pas
				INNER JOIN "._DB_PREFIX_."product_attribute pa ON pas.id_product_attribute = pa.id_product_attribute
			WHERE pa.id_product IN (".psql($idlist).")
			GROUP BY pas.id_product_attribute";
	$res = Db::getInstance()->executeS($sql);
	$nb_combinations = count($res);
	
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
		header("Content-type: application/xhtml+xml");
	} else {
		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
	echo '<rows><head>';
	echo getColSettingsAsXML();
	echo '<afterInit><call command="attachHeader"><param>'.getFilterColSettings().'</param></call>
			<call command="attachFooter"><param><![CDATA['.getFooterColSettings().']]></param></call></afterInit>';
	echo '</head>'."\n";

	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_mscombination').'</userdata>'."\n";
	echo '<userdata name="nb_combinations">'.$nb_combinations.'</userdata>'."\n";
	echo '<userdata name="marginMatrix_form">'.$marginMatrix_form[_s('CAT_PROD_GRID_MARGIN_OPERATION')].'</userdata>'."\n";
	sc_ext::readCustomMsCombinationGridConfigXML('gridUserData');
	
	echo $xml;
?>
</rows>
