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

	$product_list=Tools::getValue('product_list');
	$id_lang=Tools::getValue('id_lang');
	$id_product=Tools::getValue('id_product');
	$name=Tools::getValue('name');
	$tagsFilter=Tools::getValue('tagsFilter');
	$id_category=Tools::getValue('id_category');
	$isoCode=Tools::getValue('iso_code');
	$default_id_lang = Configuration::get('PS_LANG_DEFAULT');
	$sqlLang = "SELECT `iso_code`,`id_lang` FROM `"._DB_PREFIX_."lang`";
	$listLang = Db::getInstance()->ExecuteS($sqlLang);
	$langOption='';
	foreach($listLang AS $list)
		$langOption.='<option value="'.$list['id_lang'].'">'.$list['iso_code'].'</option>';

	function getTags()
	{
		global $product_list,$id_lang,$name,$id_product,$tagsFilter,$id_category,$sql,$isoCode,$nblanguages;
		if (intval($tagsFilter)){
			$sql="	SELECT t.id_tag,t.name,t.id_lang
					FROM "._DB_PREFIX_."product_tag pt
					LEFT JOIN "._DB_PREFIX_."tag t ON (pt.id_tag=t.id_tag)
					LEFT JOIN "._DB_PREFIX_."lang l ON (t.id_lang=l.id_lang)
					WHERE pt.id_product IN (SELECT cp.id_product FROM "._DB_PREFIX_."category_product cp WHERE cp.id_category=".intval($id_category).")
					AND t.id_tag > 0
					GROUP BY t.id_tag
					ORDER BY t.name";
		}
		else{
			$sql="	SELECT t.id_tag,t.name,t.id_lang FROM "._DB_PREFIX_."tag t
					LEFT JOIN "._DB_PREFIX_."lang l ON (t.id_lang=l.id_lang)
					GROUP BY t.id_tag
					ORDER BY t.name";
		}
		$res=Db::getInstance()->ExecuteS($sql);
		foreach($res as $row){
			echo "<row id=\"".$row['id_tag']."\">";
			echo 		"<cell>".$row['id_tag']."</cell>";
			echo 		"<cell>0</cell>";
			if ($nblanguages>1)
				echo 	"<cell>".$row['id_lang']."</cell>";
			echo 		"<cell><![CDATA[".$row['name']."]]></cell>";
			echo "</row>";
		}
	}

	//XML HEADER
	//include XML Header (as response will be in xml format)

	if (stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")){
	 	header("Content-type: application/xhtml+xml");
	}else{
	 	header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
?>
<rows parent="0">
<head>
<beforeInit>
<?php
	if ($nblanguages==1){
		echo '<call command="attachHeader"><param><![CDATA[#text_filter,,#text_filter]]></param></call>';
	}else{
		echo '<call command="attachHeader"><param><![CDATA[#text_filter,,#select_filter_strict,#text_filter]]></param></call>';
	}
?>
</beforeInit>
<column id="id_tag" width="50" type="ro" align="right" sort="int"><?php echo _l('ID')?></column>
<column id="used" width="50" type="ch" align="center" sort="str"><?php echo _l('Used')?></column>
<?php
	if ($nblanguages>1){
		echo '<column id="lang" width="50" type="coro" align="center" sort="str">'._l('Lang').$langOption.'</column>';
	}
?>
<column id="name" width="200" type="ed" align="left" sort="str"><?php echo _l('Name')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_tag').'</userdata>'."\n";
	getTags();
//	echo "<debug>".$sql."</debug>";
?>
</rows>