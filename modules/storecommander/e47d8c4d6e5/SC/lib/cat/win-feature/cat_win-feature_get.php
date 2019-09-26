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

	$attributegroups=array();
	$id_lang=intval(Tools::getValue('id_lang'));

	$sql="SELECT * FROM "._DB_PREFIX_."feature_lang";
	$rows=Db::getInstance()->ExecuteS($sql);
	$names=array();
	foreach($rows AS $row)
	{
		$names[$row['id_feature']][$row['id_lang']]['name']=$row['name'];
	}
	
	$xml='';
	$cols='';
	$filters='';
	foreach($languages AS $lang)
	{
		$cols.='<column id="name¤'.$lang['iso_code'].'" width="150" type="edtxt" align="left" sort="str">'._l('Name').' '.strtoupper($lang['iso_code']).'</column>';
		$filters.=',#text_filter';
	}

	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		$Features=Feature::getFeatures($id_lang, false);
	}else{
		$Features=Feature::getFeatures($id_lang);
	}

	foreach($Features AS $row)
	{
		$xml.=("<row id='".$row['id_feature']."'>");
			$xml.=("<cell style=\"color:#999999\">".$row['id_feature']."</cell>");
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				$xml.=("<cell>".$row['position']."</cell>");
			foreach($languages AS $lang)
			{
				@$xml.=("<cell><![CDATA[".$names[$row['id_feature']][$lang['id_lang']]['name']."]]></cell>");
			}
			$xml.=("<cell></cell>");
		$xml.=("</row>");
	}

	//XML HEADER

	//include XML Header (as response will be in xml format)
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");

?>
<rows id="0">
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#text_filter<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) echo ","; ?><?php echo $filters ?>]]></param></call>
</beforeInit>
<column id="id_feature" width="40" type="ro" align="right" sort="int"><?php echo _l('ID')?></column>
<?php
if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {?>
<column id="position" width="40" type="ro" align="right" sort="int"><?php echo _l('Position')?></column>
<?php } 
	echo 	$cols;
?>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_win-feature').'</userdata>'."\n";
	echo 	$xml;
?>
</rows>
