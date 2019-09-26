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
	$sourceGridFormat=SCI::getGridViews("msproduct");
	$sql_gridFormat = $sourceGridFormat;
	sc_ext::readCustomMsProductGridConfigXML('gridConfig');
	$gridFormat=$sourceGridFormat;
	$cols=explode(',',$gridFormat);
	$all_cols = explode(',',$gridFormat);
	
	$colSettings=array();
	$colSettings=SCI::getGridFields("msproduct");
	sc_ext::readCustomMsProductGridConfigXML('colSettings');
	
	/*
	0: coef = PV HT - PV HT
	1: coef = (PV HT - PA HT)*100 / PA HT
	2: coef = PV HT / PA HT
	3: coef = PV TTC / PA HT
	4: coef = (PV TTC - PA HT)*100 / PA HT
	5: coef = (PV HT - PA HT)*100 / PV HT
	*/
	function getColIndex($col)
	{
		global $cols;
		foreach($cols as $key=>$field)
		{
			if($field==$col)
				return $key+7;
		}
		return -1;
	}
	$marginMatrix=array(
			0=>'[=c'.getColIndex('price').'-c'.getColIndex('wholesale_price').']',
			1=>'[=(c'.getColIndex('price').'-c'.getColIndex('wholesale_price').')*100/c'.getColIndex('wholesale_price').']',
			2=>'[=c'.getColIndex('price').'/c'.getColIndex('wholesale_price').']',
			3=>'[=c'.getColIndex('price_inc_tax').'/c'.getColIndex('wholesale_price').']',
			4=>'[=(c'.getColIndex('price_inc_tax').'-c'.getColIndex('wholesale_price').')*100/c'.getColIndex('wholesale_price').']',
			5=>'[=(c'.getColIndex('price').'-c'.getColIndex('wholesale_price').')*100/c'.getColIndex('price').']'
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
		
		$uiset = uisettings::getSetting('cat_msproduct');
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
	
	function generateValue($col, $prodrow)
	{
		global $colSettings, $tax;
		$prodrow = (array)$prodrow;
		$return = "";
		switch($col){
			case'wholesale_price':
				$return .= "<cell".($prodrow['wholesale_price'] > $prodrow['price']?' bgColor="#FF0000"  style="color:#FFFFFF"':'').">".number_format($prodrow['wholesale_price'], (_s('CAT_PROD_WHOLESALEPRICE4DEC')?4:2), '.', '')."</cell>";
				break;
			case'quantity':
				$return .= "<cell>".SCI::getProductQty((int)$prodrow['id'],false,false,(int)$prodrow['id_selected_shop'])."</cell>";
				break;
			case'advanced_stock_management':
				$type_advanced_stock_management = 1;// Not Advanced Stock Management
				if(SCAS)
				{
					if($prodrow['advanced_stock_management']==1)
					{
						$type_advanced_stock_management = 2;// With Advanced Stock Management
						if(!StockAvailable::dependsOnStock((int)$prodrow['id_product'], $prodrow['id_selected_shop']))
							$type_advanced_stock_management = 3;// With Advanced Stock Management + Manual management
					}
				}
				$return .= "<cell>".$type_advanced_stock_management."</cell>";
				break;
			case'ecotax':
				$return .= "<cell>".( $prodrow['ecotax'] * SCI::getEcotaxTaxRate() )."</cell>";
				break;
			case'price':
				$return .= "<cell".(sc_array_key_exists('wholesale_price',$prodrow) && $prodrow['wholesale_price'] > $prodrow['price']?' bgColor="#FF0000"  style="color:#FFFFFF"':'').">".($prodrow['price'])."</cell>";
				break;
			case'available_now':case'available_later':
				$return .= "<cell".(sc_array_key_exists($col,$prodrow) ? '><![CDATA['.$prodrow[$col].']]>' : ' type="ro">NA')."</cell>";
				break;
			case'link_rewrite':
				$return .= '<cell><![CDATA['.$prodrow[$col].']]></cell>';
				break;
			case'price_inc_tax':
				$return .= "<cell>".($prodrow['price']*($tax[intval($prodrow['id_tax_rules_group'])]/100+1)+( $prodrow['ecotax']*SCI::getEcotaxTaxRate() ))."</cell>";
				break;
			case'margin':
				$return .= '<cell></cell>';
				break;
			case'supplier_reference':
				$sql_supplier ="SELECT *
				FROM "._DB_PREFIX_."product_supplier
				WHERE id_product = '".(int)$prodrow['id_product']."'
					AND id_product_attribute=0
					AND id_supplier = '".(int)$prodrow['id_supplier']."'";
				$product_supplier = Db::getInstance()->ExecuteS($sql_supplier);
				$ref_supp  = "";
				if(!empty($product_supplier[0]['product_supplier_reference']))
					$ref_supp = $product_supplier[0]['product_supplier_reference'];
				$return .= '<cell><![CDATA['.$ref_supp.']]></cell>';
				break;
			default:
				if (sc_array_key_exists('buildDefaultValue',$colSettings[$col]) && $colSettings[$col]['buildDefaultValue']!='')
				{
					if ($colSettings[$col]['buildDefaultValue']=='ID' )
						$return .= "<cell>ID".$prodrow['id_product']."</cell>";
				}else{
					if ($col=='id_product')
						$return .= "<cell>".$prodrow['id']."</cell>";
					elseif ($col=='id_shop')
						$return .= "<cell>".$prodrow['id_selected_shop']."</cell>";
					else
						$return .= "<cell><![CDATA[".$prodrow[$col]."]]></cell>";
				}
		}
		return $return;
	}
	
	/*
	 * PRODUCT SHOP
	 */
	$sql ="SELECT ps.* "; 
	sc_ext::readCustomMsProductGridConfigXML('SQLSelectDataSelect');
	$sql.=" FROM "._DB_PREFIX_."product_shop ps
			".((!empty($sc_agent->id_employee))?" INNER JOIN "._DB_PREFIX_."employee_shop es ON (es.id_shop = ps.id_shop AND es.id_employee = '".(int)$sc_agent->id_employee."') ":"").""; 
	sc_ext::readCustomMsProductGridConfigXML('SQLSelectDataLeftJoin');
	$sql.=" WHERE ps.id_product IN (".psql($idlist).")
			ORDER BY ps.id_product, ps.id_shop";
	$res = Db::getInstance()->executeS($sql);
	foreach($res as $product_by_shop)
	{
		if(!empty($product_by_shop["id_product"]) && !empty($product_by_shop["id_shop"]))
		{
			$product = new Product($product_by_shop["id_product"], false, $id_lang,$product_by_shop["id_shop"]);
			$product->id_selected_shop = $product_by_shop['id_shop'];
			$product->id_product = $product_by_shop["id_product"];
			$xml .="<row id=\"".$product_by_shop['id_product']."_".$product_by_shop['id_shop']."\">";
			sc_ext::readCustomMsProductGridConfigXML('rowUserData',(array)$product);
			foreach ($cols as $field)
			{
				if(!empty($field) && !empty($colSettings[$field]))
				{
					$xml .= generateValue($field, $product);
				}
			}
			$xml .="</row>";
		}
	}
		
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
	
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_msproduct').'</userdata>'."\n";
	echo '<userdata name="marginMatrix_form">'.$marginMatrix_form[_s('CAT_PROD_GRID_MARGIN_OPERATION')].'</userdata>'."\n";
	sc_ext::readCustomMsProductGridConfigXML('gridUserData');

	echo $xml;
?>
</rows>