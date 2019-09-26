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

	$id_lang=(int)Tools::getValue('id_lang');
	$shop_ids=pSQl(Tools::getValue('idshop', 0));
	
	$view=Tools::getValue('view','grid_light');
	$grids=SCI::getGridViews("manufacturer");
	
	$exportedCms = array();
	$cdata=(isset($_COOKIE['cg_man_treegrid_col_'.$view])?$_COOKIE['cg_man_treegrid_col_'.$view]:'');
	//check validity
	$check=explode(',',$cdata);
	foreach($check as $c)
		if ($c=='undefined')
		{
			$cdata='';
			break;
		}
	if ($cdata!='') $grids[$view]=$cdata;

	$cols=explode(',',$grids[$view]);

	$colSettings=array();
	$colSettings=SCI::getGridFields("manufacturer");

	function getColSettingsAsXML()
	{
		global $cols,$colSettings,$view;
		
		$uiset = uisettings::getSetting('man_grid_'.$view);
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
			if (!empty($colSettings[$col]['options']))
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


	function getManufacturers()
	{
		global $sql,$col,$id_lang,$cols,$view,$colSettings,$user_lang_iso,$fields,$fields_lang,$fieldsWithHTML,$shop_ids;
		$link=new Link();
		$col = "";

		$fields=array('id_manufacturer','name','date_add','date_upd','active');
        $fields_lang=array('meta_title', 'meta_description', 'meta_keywords');
		$fieldsWithHTML=array();
		$sqlManufacturer='';
		$sqlManufacturerLang='';

		foreach($cols as $col)
		{
			if (sc_in_array($col,$fields,'fields'))
				$sqlManufacturer.=',man.`'.$col.'`';
			if (sc_in_array($col,$fields_lang,'fields_lang'))
				$sqlManufacturerLang.=',manl.`'.$col.'`';
		}
		$sqlManufacturer=trim($sqlManufacturer,',').(!empty($sqlManufacturerLang) ? ',' : '');
		$sqlManufacturerLang=trim($sqlManufacturerLang,',');

		if (version_compare(_PS_VERSION_, '1.5.0.10', '<'))
		{
			$sql="SELECT ".$sqlManufacturer.$sqlManufacturerLang.
			$sql.=" FROM "._DB_PREFIX_."manufacturer man
					LEFT JOIN "._DB_PREFIX_."manufacturer_lang manl ON (manl.id_manufacturer= man.id_manufacturer AND manl.id_lang=".(int)$id_lang.")
					WHERE 1=1 
					ORDER BY man.id_manufacturer DESC";
		}else{
			$sql="SELECT ".$sqlManufacturer.$sqlManufacturerLang.
				$sql.=" FROM "._DB_PREFIX_."manufacturer man
					LEFT JOIN "._DB_PREFIX_."manufacturer_lang manl ON (manl.id_manufacturer= man.id_manufacturer AND manl.id_lang=".(int)$id_lang.")
					LEFT JOIN "._DB_PREFIX_."manufacturer_shop mans ON (mans.id_manufacturer= man.id_manufacturer)
					WHERE 1=1 
					AND mans.id_shop IN (".pSQL($shop_ids).")
					GROUP BY id_manufacturer
					ORDER BY man.id_manufacturer DESC";
		}

		global $dd;
		$dd=$sql;
//		d($sql);
		$res=Db::getInstance()->ExecuteS($sql);
		
		foreach($res as $manRow){
			echo '<row id="'.$manRow['id_manufacturer'].'">';
			echo '  <userdata name="id_manufacturer">'.(int)$manRow['id_manufacturer'].'</userdata>';

			foreach($cols AS $key => $col)
			{
				switch($col){
					case'id':
						echo 	"<cell>".$manRow['id_manufacturer']."</cell>"; //  style=\"color:tan\"
						break;
					case'image':
						$id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');
						if (version_compare(_PS_VERSION_, '1.5.0.10', '>=')) {
							$shopUrl = new ShopUrl($id_shop);
							$shop_url = $shopUrl->getURL(Configuration::get('PS_SSL_ENABLED'));
						} else {
							$shop = new Shop($id_shop);
							if(Configuration::get('PS_SSL_ENABLED')) {
								$shop_url = 'https://'.$shop->domain_ssl.$shop->getBaseURI();
							} else {
								$shop_url = 'http://'.$shop->domain.$shop->getBaseURI();
							}
						}
						$to_img = '/img/m/'.$manRow['id_manufacturer'].'.jpg';
						if(file_exists(_PS_MANU_IMG_DIR_.$manRow['id_manufacturer'].'.jpg')) {
							echo 	"<cell><![CDATA[<img src=\"".$shop_url.$to_img."?time=".time()."\" width=\"100%\"/>]]></cell>";
						} else {
							echo 	"<cell></cell>";
						}
						break;
					case'meta_title':case'meta_description':case'meta_keywords':
						echo '<cell><![CDATA['.$manRow[$col].']]></cell>';
						break;
					default:
						if (sc_array_key_exists('buildDefaultValue',$colSettings[$col]) && $colSettings[$col]['buildDefaultValue']!='')
						{
							if ($colSettings[$col]['buildDefaultValue']=='ID')
								echo "<cell>ID".$manRow['id_manufacturer']."</cell>";
						}else{
							if ($manRow[$col]=='' || $manRow[$col]===0 || $manRow[$col]===1) // opti perf is_numeric($manRow[$col]) ||
							{
								echo "<cell>".$manRow[$col]."</cell>";
							}else{
								echo "<cell><![CDATA[".$manRow[$col]."]]></cell>";
							}
						}
				}
			}
			echo "</row>\n";
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
	echo '<afterInit>
						<call command="attachHeader"><param>'.getFilterColSettings().'</param></call>
						<call command="attachFooter"><param><![CDATA['.getFooterColSettings().']]></param></call>
					</afterInit>';
	echo '</head>';

	$uiset = uisettings::getSetting('man_grid_'.$view);
	$tmp = explode('|',$uiset);
	$uiset = "|".$tmp[1]."||".$tmp[3];
	echo '<userdata name="uisettings">'.$uiset.'</userdata>'."\n";
	echo '<userdata name="LIMIT_SMARTRENDERING">'.(int)_s("CMS_PAGE_LIMIT_SMARTRENDERING").'</userdata>';
	echo "\n";
	getManufacturers();

	if (isset($_GET['DEBUG'])) echo '<az><![CDATA['.$dd.']]></az>';
	echo '</rows>';
