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
	$idlist=(Tools::getValue('idlist',0));

	function getRowsFromDB(){
		global $id_lang,$idlist;
		
		$array_langs = array();
		$langs = Language::getLanguages(false);
		foreach($langs as $lang)
			$array_langs[$lang["id_lang"]] = strtoupper($lang["iso_code"]);
		
		if(SCMS)
		{
			$array_shops = array();
			$shops = Shop::getShops(false);
			foreach($shops as $shop)
			{
				$shop['name'] = str_replace("&", _l('and'), $shop['name']);
				$array_shops[$shop["id_shop"]] = $shop["name"];
			}
		}
		
		$sql = '
		SELECT cl.*
		FROM '._DB_PREFIX_.'category_lang cl
			'.((!_s("CAT_PROD_LANGUAGE_ALL"))?" INNER JOIN "._DB_PREFIX_."lang l ON (cl.id_lang = l.id_lang AND l.active = 1)":"").'
		WHERE cl.id_category IN ('.pSQL($idlist).')
			AND cl.name != "SC Recycle Bin"
		ORDER BY cl.id_category, cl.id_lang';
		if(SCMS)
			$sql .= ",id_shop";
		$res=Db::getInstance()->ExecuteS($sql);
		$xml='';
		foreach ($res AS $row)
		{
			
			$xml.=("<row id='".$row['id_category']."_".$row['id_lang'].(SCMS?"_".$row['id_shop']:"")."'>");
				$xml.=("<cell>".$row['id_category']."</cell>");
				if (SCMS)
				{
					$xml.=("<cell>".$array_shops[$row['id_shop']]."</cell>");
				}
				$xml.=("<cell>".$array_langs[$row['id_lang']]."</cell>");
				$xml.=("<cell><![CDATA[".$row['link_rewrite']."]]></cell>");
				$xml.=("<cell><![CDATA[".$row['meta_title']."]]></cell>");
				$xml.=("<cell><![CDATA[".strlen($row['meta_title'])."]]></cell>");
				$xml.=("<cell><![CDATA[".$row['meta_description']."]]></cell>");
				$xml.=("<cell><![CDATA[".strlen($row['meta_description'])."]]></cell>");
				$xml.=("<cell><![CDATA[".$row['meta_keywords']."]]></cell>");
				$xml.=("<cell><![CDATA[".strlen($row['meta_keywords'])."]]></cell>");
			$xml.=("</row>");
		}
		return $xml;
	}

	//XML HEADER
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");

	$xml = "";
	if(!empty($idlist))
		$xml=getRowsFromDB();
?>
<rows id="0">
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#text_filter<?php if(SCMS){ ?>,#select_filter<?php } ?>,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter]]></param></call>
</beforeInit>
<column id="id_category" width="40" type="ro" align="right" sort="int"><?php echo _l('ID')?></column>
<?php if(SCMS){ ?>
<column id="shop" width="100" type="ro" align="right" sort="int"><?php echo _l('Shop')?></column>
<?php } ?>
<column id="lang" width="60" type="ro" align="center" sort="str"><?php echo _l('Lang')?></column>
<column id="link_rewrite" width="120" type="ed" align="left" sort="str"><?php echo _l('Link rewrite')?></column>
<column id="meta_title" width="120" type="ed" align="left" sort="str"><?php echo _l('META title')?></column>
<column id="meta_title_width" width="40" type="ro" align="right" sort="str"><?php echo _l('META title length')?></column>
<column id="meta_description" width="200" type="ed" align="left" sort="str"><?php echo _l('META description')?></column>
<column id="meta_description_width" width="ro" type="ro" align="right" sort="str"><?php echo _l('META description length')?></column>
<column id="meta_keywords" width="120" type="ed" align="left" sort="str"><?php echo _l('META keywords')?></column>
<column id="meta_keywords_width" width="40" type="ro" align="right" sort="str"><?php echo _l('META keywords length')?></column>
<afterInit>
<call command="enableMultiselect"><param>1</param></call>
</afterInit>
</head>
<?php
//  format="%Y-%m-%d 00:00:00"
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_prop_seo_grid').'</userdata>'."\n";
	echo $xml;
?>
</rows>
