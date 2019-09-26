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
	$id_product=(Tools::getValue('id_product'));
	$link=new Link();
	
	$multiple = false;
	if(strpos($id_product, ",") !== false)
		$multiple = true;

	$shops = array();
	if(SCMS) 
	{
		if(!$multiple)
		{
			$sql ="SELECT s.id_shop, s.name
				FROM "._DB_PREFIX_."shop s
					INNER JOIN "._DB_PREFIX_."product_shop ps ON ps.id_shop = s.id_shop
					".((!empty($sc_agent->id_employee))?" INNER JOIN "._DB_PREFIX_."employee_shop es ON (es.id_shop = s.id_shop AND es.id_employee = '".(int)$sc_agent->id_employee."') ":"")."
				WHERE s.deleted!='1'
					AND ps.`id_product` = '".intval($id_product)."'
				GROUP BY s.id_shop
				ORDER BY s.name";
		}
		else
		{
			$sql ="SELECT s.id_shop, s.name
				FROM "._DB_PREFIX_."shop s
					".((!empty($sc_agent->id_employee))?" INNER JOIN "._DB_PREFIX_."employee_shop es ON (es.id_shop = s.id_shop AND es.id_employee = '".(int)$sc_agent->id_employee."') ":"")."
				WHERE s.deleted!='1'
				GROUP BY s.id_shop
				ORDER BY s.name";
		}
		$res = Db::getInstance()->executeS($sql);
		foreach($res as $shop)
			$shops[$shop["id_shop"]] = str_replace("&", _l("and"), $shop["name"])." (#".$shop["id_shop"].")";
	}
	
	$xml='';
	$image=array();
	
	// SETTINGS, FILTERS AND COLONNES
	$sourceGridFormat=SCI::getGridViews("image");
	$sql_gridFormat = $sourceGridFormat;
	sc_ext::readCustomImageGridConfigXML('gridConfig');
	$gridFormat=$sourceGridFormat;
	if($multiple) 
	{
		$gridFormat=str_replace('legend','',$gridFormat);
		$gridFormat=str_replace('position','',$gridFormat);
		$gridFormat=str_replace('cover','',$gridFormat);
	}
	else
	{
		if(!(version_compare(_PS_VERSION_, '1.5.0.0', '<') || version_compare(_PS_VERSION_, '1.5.6.1', '>=') ))
			$gridFormat=str_replace('legend','',$gridFormat);
		$gridFormat=str_replace('id_product','',$gridFormat);
		$gridFormat=str_replace('reference','',$gridFormat);
		$gridFormat=str_replace('name','',$gridFormat);
	}
	$cols=explode(',',$gridFormat);
	foreach($cols AS $k => $v)
		if ($v=='')
			unset($cols[$k]);
	$all_cols = explode(',',$gridFormat);
	foreach($all_cols AS $k => $v)
		if ($v=='')
			unset($cols[$k]);
	
	$colSettings=array();
	$colSettings=SCI::getGridFields("image");
	sc_ext::readCustomImageGridConfigXML('colSettings');
	
	function getFooterColSettings()
	{
		global $cols,$colSettings,$shops;
	
		$footer='';
		foreach($cols AS $id => $col)
		{
			if($col!="_SHOPS_")
			{
				if (sc_array_key_exists($col,$colSettings) && sc_array_key_exists('footer',$colSettings[$col]))
					$footer.=$colSettings[$col]['footer'].',';
				else
					$footer.=',';
			}
			else
			{
				foreach($shops as $idS=>$nameS)
				{
					if (sc_array_key_exists($col,$colSettings) && sc_array_key_exists('footer',$colSettings[$col]))
						$footer.=$colSettings[$col]['footer'].',';
					else
						$footer.=',';
				}
			}
		}
		return $footer;
	}
	
	function getFilterColSettings()
	{
		global $cols,$colSettings,$shops;
	
		$filters='';
		foreach($cols AS $id => $col)
		{
			if($col!="_SHOPS_")
			{
				if($colSettings[$col]['filter']=="na")
					$colSettings[$col]['filter'] = "";
				$filters.=$colSettings[$col]['filter'].',';
			}
			else
			{
				foreach($shops as $idS=>$nameS)
				{
					if($colSettings[$col]['filter']=="na")
						$colSettings[$col]['filter'] = "";
					$filters.=$colSettings[$col]['filter'].',';
				}
			}
		}
		$filters=trim($filters,',');
		return $filters;
	}
	
	function getColSettingsAsXML()
	{
		global $cols,$colSettings,$shops;
		
		$uiset = uisettings::getSetting('cat_image'.($multiple?"_multiple":""));
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
			if($col!="_SHOPS_")
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
			else
			{
				foreach($shops as $idS=>$nameS)
				{
					$xml.='<column id="shop_'.$idS.'"'.(sc_array_key_exists('format',$colSettings[$col])?
							' format="'.$colSettings[$col]['format'].'"':'').
							' width="'.( sc_array_key_exists($col,$sizes) ? $sizes[$col] : $colSettings[$col]['width']).'"'.
							' hidden="'.( sc_array_key_exists($col,$hidden) ? $hidden[$col] : 0 ).'"'.
							' align="'.$colSettings[$col]['align'].'"
						type="'.$colSettings[$col]['type'].'"
						sort="'.$colSettings[$col]['sort'].'"
						color="'.$colSettings[$col]['color'].'">'.$nameS;
					$xml.='</column>'."\n";
				}
			}
		}
		return $xml;
	}	
	
	function generateValue($col, $imgrow, $pos)
	{
		global $colSettings,$shops;
		$imgrow = (array)$imgrow;
		$defaultimg='lib/img/i.gif';
		$return = "";
		switch($col){
			case 'id_image':
				$return .= ("<cell style=\"color:".($imgrow['cover']?'#0000FF':'#999999')."\">".$imgrow['id_image']."</cell>");
				break;
			case 'image':
				if (file_exists(SC_PS_PATH_REL."img/p/".getImgPath(intval($imgrow['id_product']),intval($imgrow['id_image']),_s('CAT_PROD_GRID_IMAGE_SIZE')))) {
					$return .= "<cell><![CDATA[<img src='".SC_PS_PATH_REL."img/p/".getImgPath(intval($imgrow['id_product']),intval($imgrow['id_image']),_s('CAT_PROD_GRID_IMAGE_SIZE'))."'/>".(_s('CAT_PROD_IMG_SAVE_FILENAME') && _s('CAT_PROD_IMG_DISPLAY_FILENAME')?'<br/>'.$imgrow['sc_path']:'')."]]></cell>";
				} else {
					$return .= "<cell><![CDATA[<img src='".$defaultimg."'/>--]]></cell>";
				}
				break;
			case 'name':
				$return .= "<cell><![CDATA[".$imgrow['p_name']."]]></cell>";
				break;
			case 'position':
				$return .= "<cell><![CDATA[".$pos."]]></cell>";
				break;
			case '_SHOPS_':
				foreach($shops as $id=>$name)
				{
					$sql_shop ="SELECT *
							FROM "._DB_PREFIX_."image_shop
							WHERE `id_image` = '".$imgrow['id_image']."'
								AND id_shop = '".$id."'";
					$res_shop = Db::getInstance()->getRow($sql_shop);
					if(!empty($res_shop["id_image"]))
						$return.=("<cell>1</cell>");
					else
						$return.=("<cell>0</cell>");
				}
				break;
			default:
				$return .= "<cell><![CDATA[".$imgrow[$col]."]]></cell>";
		}
		return $return;
	}

	$ids_products = explode(',',$id_product);
	foreach($ids_products as $product_id) {
		$sql = '
			SELECT il.*,i.*,pl.link_rewrite, p.reference, pl.name as p_name
			'.(SCMS?' ,ims.cover ':'').' ';
		sc_ext::readCustomImageGridConfigXML('SQLSelectDataSelect');
		$sql.=' FROM `'._DB_PREFIX_.'image` i
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.intval($id_lang).')
				LEFT JOIN `'._DB_PREFIX_.'product` p ON (i.`id_product` = p.`id_product`)
			'.(SCMS?' LEFT JOIN `'._DB_PREFIX_.'image_shop` ims ON (i.`id_image` = ims.`id_image` AND ims.`id_shop` = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').') ':'').'
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = i.`id_product` AND pl.`id_lang` = '.intval($id_lang).' '.(SCMS?(SCI::getSelectedShop()>0?' AND pl.id_shop='.(int)SCI::getSelectedShop():' AND pl.id_shop=p.id_shop_default '):'').')';
		sc_ext::readCustomImageGridConfigXML('SQLSelectDataLeftJoin');
		$sql.=' WHERE i.`id_product` IN ('.pSQL($product_id).')
			GROUP BY i.id_image
			ORDER BY i.position';
		$res=Db::getInstance()->ExecuteS($sql);
		$pos = 1;
		foreach ($res AS $image)
		{
			$xml.=("<row id='".$image['id_image']."'>");
			sc_ext::readCustomImageGridConfigXML('rowUserData',(array)$image);
			foreach ($cols as $field)
			{
				if(!empty($field) && !empty($colSettings[$field]))
				{
					$xml .= generateValue($field, $image, $pos);
				}
			}
			$xml.=("</row>\n");

			if($pos!=$image["position"])
			{
				$sql2 = "UPDATE "._DB_PREFIX_."image SET position='".$pos."' WHERE id_image=".$image['id_image'];
				Db::getInstance()->Execute($sql2);
			}
			$pos++;
		}
	}

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

	$uisettings = uisettings::getSetting('cat_image'.($multiple?"_multiple":""));
	list($hidden,$order,$size,$sort) = explode("|",$uisettings);
	if(!empty($order))
	{
		$order_fields = explode("-",$order);
		$new_order = "";
		foreach($order_fields as $order_field){
			if(strpos($order_field,"shop_")===false)
			{
				if(!empty($new_order))
					$new_order .= "-";
				$new_order .= $order_field;
			}
		}
	}
	$uisettings = $hidde."|".$new_order."|".$size."|".$sort;

	echo '<userdata name="uisettings">'.$uisettings.'</userdata>'."\n";
	sc_ext::readCustomImageGridConfigXML('gridUserData');

	echo $xml;
?>
</rows>
