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
	$id_product=(Tools::getValue('id_product',0));


	if (SCMS)
	{
		$sql = 'SELECT s.*
				FROM '._DB_PREFIX_.'shop s
				'.((!empty($sc_agent->id_employee))?" INNER JOIN "._DB_PREFIX_."employee_shop es ON (es.id_shop = s.id_shop AND es.id_employee = '".(int)$sc_agent->id_employee."') ":"").'
				ORDER BY s.name';
		$res=Db::getInstance()->ExecuteS($sql);
		$shops=array();
		$shops[0]=_l('All');
		foreach ($res AS $shop)
		{
			//$shop['name'] = str_replace("&", _l('and'), $shop['name']);
			$shops[$shop['id_shop']]=$shop['name'];
		}
	
		$has_shops_restrictions = false;
		$all_shops = Db::getInstance()->ExecuteS('SELECT id_shop FROM '._DB_PREFIX_.'shop');
		if(count($all_shops) != count($res))
			$has_shops_restrictions = true;
	
		$group_shops=array();
		$group_shops[0]=_l('All');
		if(!$has_shops_restrictions)
		{
			$sql = 'SELECT *
							FROM '._DB_PREFIX_.'shop_group
							ORDER BY name';
			$res=Db::getInstance()->ExecuteS($sql);
			foreach ($res AS $group)
				$group_shops[$group['id_shop_group']]=$group['name'];
		}
	}
	
	$sql = 'SELECT *
					FROM '._DB_PREFIX_.'group_lang
					WHERE id_lang='.(int)$id_lang.'
					ORDER BY id_group';
	$res=Db::getInstance()->ExecuteS($sql);
	$groups=array();
	$groups[0]=_l('All');
	foreach ($res AS $group)
	{
		//$group['name'] = str_replace("&", _l('and'), $group['name']);
		$groups[$group['id_group']]=$group['name'];
	}
	
	$sql = 'SELECT cl.id_country,cl.name
					FROM '._DB_PREFIX_.'country_lang cl
					LEFT JOIN '._DB_PREFIX_.'country c ON (c.id_country=cl.id_country)
					WHERE cl.id_lang='.(int)$id_lang.' AND c.active=1
					ORDER BY cl.name';
	$res=Db::getInstance()->ExecuteS($sql);
	$countries=array();
	$countries[0]=_l('All');
	foreach ($res AS $country)
	{
		//$country['name'] = str_replace("&", _l('and'), $country['name']);
		$countries[$country['id_country']]=$country['name'];
	}
	
	$sql = 'SELECT id_manufacturer,name
					FROM '._DB_PREFIX_.'manufacturer';
	$res=Db::getInstance()->ExecuteS($sql);
	$manus=array();
	foreach ($res AS $manu)
	{
		//$country['name'] = str_replace("&", _l('and'), $country['name']);
		$manus[$manu['id_manufacturer']]=$manu['name'];
	}
	
	$sql = 'SELECT id_supplier,name
					FROM '._DB_PREFIX_.'supplier';
	$res=Db::getInstance()->ExecuteS($sql);
	$suppliers=array();
	foreach ($res AS $supplier)
	{
		//$country['name'] = str_replace("&", _l('and'), $country['name']);
		$suppliers[$supplier['id_supplier']]=$supplier['name'];
	}
	
	$sql = 'SELECT id_currency,iso_code
					FROM '._DB_PREFIX_.'currency
					WHERE active=1
					ORDER BY iso_code';
	$res=Db::getInstance()->ExecuteS($sql);
	$currencies=array();
	$currencies[0]=_l('All');
	foreach ($res AS $currency)
		$currencies[$currency['id_currency']]=$currency['iso_code'];

	$defaultimg='lib/img/i.gif';
	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		if (file_exists(SC_PS_PATH_DIR."img/p/".$user_lang_iso."-default-"._s('CAT_PROD_GRID_IMAGE_SIZE')."_default.jpg"))
			$defaultimg=SC_PS_PATH_REL."img/p/".$user_lang_iso."-default-"._s('CAT_PROD_GRID_IMAGE_SIZE')."_default.jpg";
	}else{
		if (file_exists(SC_PS_PATH_DIR."img/p/".$user_lang_iso."-default-"._s('CAT_PROD_GRID_IMAGE_SIZE').".jpg"))
			$defaultimg=SC_PS_PATH_REL."img/p/".$user_lang_iso."-default-"._s('CAT_PROD_GRID_IMAGE_SIZE').".jpg";
	}
	
	// SETTINGS, FILTERS AND COLONNES
	$sourceGridFormat=SCI::getGridViews("propspeprice");
	$sql_gridFormat = $sourceGridFormat;
	sc_ext::readCustomPropSpePriceGridConfigXML('gridConfig');
	$gridFormat=$sourceGridFormat;
	$cols=explode(',',$gridFormat);
	$all_cols = explode(',',$gridFormat);
	
	$colSettings=array();
	$colSettings=SCI::getGridFields("propspeprice");
	sc_ext::readCustomPropSpePriceGridConfigXML('colSettings');
	


	$tax=array(0 => 0);
	if (sc_in_array('id_tax',$cols,'cols') || sc_in_array('id_tax_rules_group',$cols,'cols') || sc_in_array('price_inc_tax',$cols,'cols'))
	{
		if (version_compare(_PS_VERSION_, '1.6.0.10', '>='))
		{
			$inner = "";
	
			if (SCMS && SCI::getSelectedShop()>0)
				$inner = " INNER JOIN "._DB_PREFIX_."tax_rules_group_shop trgs ON (trgs.id_tax_rules_group = trg.id_tax_rules_group AND trgs.id_shop = '".(int)SCI::getSelectedShop()."')";
	
			$sql='SELECT trg.name, trg.id_tax_rules_group,t.rate, trg.deleted
			FROM `'._DB_PREFIX_.'tax_rules_group` trg
			LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (trg.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
  	  			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
  	  		'.$inner.'
	    	WHERE trg.active=1
  	  		ORDER BY trg.deleted ASC, trg.name ASC';
			$res=Db::getInstance()->ExecuteS($sql);
			foreach($res as $row){
				if ($row['name']=='') $row['name']=' ';
	
				if($row['deleted']=="1")
					$row['name'] .= " "._l("(deleted)");
	
				$tax[$row['id_tax_rules_group']]=$row['rate'];
			}
		}
		elseif (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
		{
			$inner = "";
	
			if (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && SCMS && SCI::getSelectedShop()>0)
				$inner = " INNER JOIN "._DB_PREFIX_."tax_rules_group_shop trgs ON (trgs.id_tax_rules_group = trg.id_tax_rules_group AND trgs.id_shop = '".(int)SCI::getSelectedShop()."')";
	
			$sql='SELECT trg.name, trg.id_tax_rules_group,t.rate
			FROM `'._DB_PREFIX_.'tax_rules_group` trg
			LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (trg.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
  	  			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
  	  		'.$inner.'
	    	WHERE trg.active=1';
			$res=Db::getInstance()->ExecuteS($sql);
			foreach($res as $row){
				if ($row['name']=='') $row['name']=' ';
				$tax[$row['id_tax_rules_group']]=$row['rate'];
			}
		}else{
			$sql = "SELECT id_tax,rate FROM "._DB_PREFIX_."tax";
			$res=Db::getInstance()->ExecuteS($sql);
			foreach($res as $row){
				$tax[$row['id_tax']]=$row['rate'];
			}
		}
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

		$uiset = uisettings::getSetting('cat_specificprice');
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
					'.($colSettings[$col]['type'] == 'combo' ? 'source="index.php?ajax=1&amp;act=cat_specificprice_customer_get&amp;ajaxCall=1" auto="true" cache="false"' : '').'
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
	
	function generateValue($col, $row, $p, $pa=null)
	{
		global $colSettings,$id_lang,$tax,$defaultimg,$manus,$suppliers;
		$return = "";
		switch($col){
			case 'id_specific_price':
				$return .= ("<cell style=\"color:#999999\">".$row['id_specific_price']."</cell>");
				break;
			case 'from_quantity':
				if(_s("APP_COMPAT_MODULE_PPE"))
					$row['from_quantity'] = number_format($row['from_quantity'],6,".","");
				$return .= ("<cell><![CDATA[".$row['from_quantity']."]]></cell>");
				break;
			case 'price':
				$return .= ("<cell>".($row['price'] != -1 || version_compare(_PS_VERSION_, '1.5.0.0', '<') ? number_format($row['price'],2):"-1")."</cell>");
				break;
			case 'reduction':
				$return .= ("<cell>".($row['reduction_type']=='percentage'?(number_format($row['reduction']*100,2)).'%':number_format($row['reduction'],2))."</cell>");
				break;
			case 'name':
				$name = $p->name;
				if(is_array($name))
					$name = $name[$id_lang];
				$return .= '<cell><![CDATA['.$name.']]></cell>';
				break;
			case 'image':
				$f = "";
				$image = Image::getCover((int)$p->id);
				if (empty($image['id_image']))
				{
					$f = "<img src='".$defaultimg."'/>";
				}else{
					$f = "<img src='".SC_PS_PATH_REL."img/p/".getImgPath(intval($p->id),intval($image['id_image']),_s('CAT_PROD_GRID_IMAGE_SIZE'))."'/>";
				}
				$return .= '<cell><![CDATA['.$f.']]></cell>';
				break;
			case 'active':
				$f = "";
				if(!empty($p->active))
					$f = _l("Yes");
				else
					$f = _l("No");
				$return .= '<cell><![CDATA['.$f.']]></cell>';
				break;
			case 'id_manufacturer':
				$return .= '<cell><![CDATA['.$manus[$p->id_manufacturer].']]></cell>';
				break;
			case 'id_supplier':
				$return .= '<cell><![CDATA['.$suppliers[$p->id_supplier].']]></cell>';
				break;
			case 'id_customer':
				if ($row['id_customer'] > 0 ){
					$sql = 'SELECT firstname, lastname FROM '._DB_PREFIX_.'customer WHERE id_customer = '.(int)$row['id_customer'];
					$customer = Db::getInstance()->getRow($sql);
					$return .= '<cell><![CDATA['.$customer['firstname'].' '.$customer['lastname'].']]></cell>';
				} else {
					$return .= '<cell><![CDATA['._l('All').']]></cell>';
				}
				break;
			case 'price_exl_tax':
				if(empty($row['id_product_attribute']))
					$return .= '<cell><![CDATA['.number_format($p->price, 6, '.', '').']]></cell>';
				else
				{
					if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
					{
						echo ("<cell>".number_format($pa->price+$p->price, 6, '.', '')."</cell>");
					}else{
						$taxrate = $tax[intval($p->id_tax)];
						if(!empty($taxrate))
							echo ("<cell>".number_format($pa->price/($taxrate/100+1)+$p->price, 6, '.', '')."</cell>");
						else
							echo ("<cell>".number_format($pa->price+$p->price, 6, '.', '')."</cell>");
					}	
				}
				break;
			case 'price_inc_tax':
				if(empty($row['id_product_attribute']))
				{
					$ecotax = (_s('CAT_PROD_ECOTAXINCLUDED') ? ( version_compare(_PS_VERSION_, '1.3.0.0', '>=') ? $p->ecotax*SCI::getEcotaxTaxRate() : $p->ecotax ) : 0);
					$return .= "<cell>".number_format($p->price*($tax[intval($p->id_tax_rules_group)]/100+1)+$ecotax, 6, '.', '')."</cell>";
					//$return .= "<cell>".$p->id_tax_rules_group."</cell>";
				}
				else
				{
					if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
					{
						$taxrate = $tax[intval($p->id_tax_rules_group)];
						$ecotax = (_s('CAT_PROD_ECOTAXINCLUDED') ? $pa->ecotax*SCI::getEcotaxTaxRate() : 0 );
						if(!empty($taxrate))
							echo ("<cell>".number_format($pa->price*($taxrate/100+1)+$p->price*($taxrate/100+1) + $ecotax, 6, '.', '')."</cell>");
						else
							echo ("<cell>".number_format($pa->price+$p->price + $ecotax, 6, '.', '')."</cell>");
					}
					elseif (version_compare(_PS_VERSION_, '1.3.0.0', '>='))
					{
						$taxrate = $tax[intval($p->id_tax)];
						$ecotax = (_s('CAT_PROD_ECOTAXINCLUDED') ? $pa->ecotax*SCI::getEcotaxTaxRate() : 0 );
						if(!empty($taxrate))
							echo ("<cell>".number_format($pa->price+$p->price*($taxrate/100+1)+$ecotax, 6, '.', '')."</cell>");
						else
							echo ("<cell>".number_format($pa->price+$p->price+$ecotax, 6, '.', '')."</cell>");
					}else{
						$taxrate = $tax[intval($p->id_tax)];
						if(!empty($taxrate))
							echo ("<cell>".number_format($pa->price+$p->price*($taxrate/100+1), 6, '.', '')."</cell>");
						else
							echo ("<cell>".number_format($pa->price+$p->price, 6, '.', '')."</cell>");
					}
				}
				break;
			case 'reference':case 'supplier_reference':case 'ean13':case 'upc':
				if(empty($row['id_product_attribute']))
					$return .= '<cell><![CDATA['.$p->{$col}.']]></cell>';
				else
					$return .= '<cell><![CDATA['.$pa->{$col}.']]></cell>';
				break;
			case 'id_specific_price_rule':
				if($row['id_specific_price_rule'] > 0)
					$return .= '<cell><![CDATA['. _l('Catalog price rule') .']]></cell>';
				else
					$return .= '<cell><![CDATA['. _l('Special price') .']]></cell>';
				break;
			default:
				$return .= "<cell><![CDATA[".$row[$col]."]]></cell>";
		}
		return $return;
	}
	
	function getRowsFromDB(){
		global $id_lang,$id_product,$cols,$colSettings;

		$where = "";
		
		$sql = '
		SELECT * '; 
		sc_ext::readCustomPropSpePriceGridConfigXML('SQLSelectDataSelect');
		$sql.=' FROM '._DB_PREFIX_.'specific_price ';
		sc_ext::readCustomPropSpePriceGridConfigXML('SQLSelectDataLeftJoin');
		$sql.=' WHERE id_product IN ('.pSQL($id_product).')
		ORDER BY `from` DESC';
		$res=Db::getInstance()->ExecuteS($sql);
		$xml='';
		foreach ($res AS $specific_price)
		{
			$row_color = "";
			$id_product = $specific_price['id_product'];
			$pa = null;
			
			if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				$product = new Product((int)$id_product, false, (int)$id_lang, (int)SCI::getSelectedShop());
			else
				$product = new Product((int)$id_product, (int)$id_lang);
			
			if(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($specific_price['id_product_attribute']))
			{
				$row_color = "color:#999999";
				$id_product .= "_".$specific_price['id_product_attribute'];
				if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					$pa = new Combination((int)$specific_price['id_product_attribute'],(int)$id_lang, (int)SCI::getSelectedShop());
				else
					$pa = new Combination((int)$specific_price['id_product_attribute'],(int)$id_lang);
			}
			
			if ($specific_price['from']==$specific_price['to'])
			{
				$specific_price['from']=date('Y-01-01 00:00:00');
				$specific_price['to']=(date('Y')+1).date('-m-d 00:00:00');
			}
			if ($specific_price['from']=='0000-00-00 00:00:00') $specific_price['from']=date('Y-01-01 00:00:00');
			if ($specific_price['to']=='0000-00-00 00:00:00') $specific_price['to']=(date('Y')+1).date('-m-d 00:00:00');

			$xml.=("<row id='".$specific_price['id_specific_price']."' style=\"".$row_color."\">");
				if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
					$xml .= ('<userdata name="is_combination">' . ((!empty($row_color)) ? "1" : "0") . '</userdata>');
					$xml .= ('<userdata name="id_specific_price_rule">' . $specific_price['id_specific_price_rule'] . '</userdata>');
				}
				sc_ext::readCustomPropSpePriceGridConfigXML('rowUserData',(array)$specific_price);
				foreach ($cols as $field)
				{
					if(!empty($field) && !empty($colSettings[$field]))
					{
						$xml .= generateValue($field, $specific_price, $product, $pa);
					}
				}
			$xml.=("</row>");
		}
		return $xml;
	}

$xml=getRowsFromDB();

	//XML HEADER

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
	
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_specificprice').'</userdata>'."\n";
	sc_ext::readCustomPropSpePriceGridConfigXML('gridUserData');

	echo $xml;
?>
</rows>
