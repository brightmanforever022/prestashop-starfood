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
	$id_attribute_group=intval(Tools::getValue('id_attribute_group'));
	$iscolor=intval(Tools::getValue('iscolor'));
	$sc_active=SCI::getConfigurationValue('SC_PLUG_DISABLECOMBINATIONS',0);


	$sql="SELECT al.*,a.*,(SELECT count(pac.id_attribute) FROM "._DB_PREFIX_."product_attribute_combination pac WHERE pac.id_attribute=a.id_attribute )as nb, a.*
				FROM "._DB_PREFIX_."attribute a 
				RIGHT JOIN "._DB_PREFIX_."attribute_lang al ON (al.id_attribute=a.id_attribute) 
				WHERE a.id_attribute_group=".intval($id_attribute_group);
	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		$sql .= " ORDER BY a.position ASC ";
	$rows=Db::getInstance()->ExecuteS($sql);
	$names=array();
	foreach($rows AS $row)
		$names[$row['id_attribute']][$row['id_lang']]=$row['name'];

	$xml='';
	$cols='';
	$filters='';
	foreach($languages AS $lang)
	{
		$cols.='<column id="name¤'.$lang['iso_code'].'" width="100" type="edtxt" align="left" sort="str">'._l('Name').' '.strtoupper($lang['iso_code']).'</column>';
		$filters.=',#text_filter';
	}
	
	foreach($rows AS $row)
	{
		if ($row['id_lang']!=$id_lang) continue;
		$xml.=("<row id='".$row['id_attribute']."'>");
			$xml.=("<cell style=\"color:#999999\">".$row['id_attribute']."</cell>");
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				$xml.=("<cell>".$row['position']."</cell>");
			if ($sc_active)
				$xml.=("<cell>".$row['sc_active']."</cell>");
			if ($iscolor) $xml.="<cell><![CDATA[".$row['color']."]]></cell>"."<cell><![CDATA[".$row['color']."]]></cell>";
			foreach($languages AS $lang)
			{
				@$xml.=("<cell><![CDATA[".$names[$row['id_attribute']][$lang['id_lang']]."]]></cell>");
			}
			if ($iscolor) {
				$ext = checkAndGetImgExtension(_PS_COL_IMG_DIR_.$row['id_attribute']);
				$img = "";
				if(!empty($ext))
					$img = "<img src=\""._THEME_COL_DIR_.$row['id_attribute'].".".$ext."\" height=40/>";
				$xml.='<cell><![CDATA['.$img.']]></cell>';
			}
			$xml.=("<cell style=\"color:#999999\">".(int)$row['nb']."</cell>");
		$xml.=("</row>\n");
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
<call command="attachHeader"><param><![CDATA[#numeric_filter<?php echo (version_compare(_PS_VERSION_, '1.5.0.0', '>=')?',#numeric_filter':''); if ($sc_active) echo ",#select_filter"; if ($iscolor) {echo ',#text_filter,';}  echo $filters;  if ($iscolor) {echo ',';} ?>,#numeric_filter]]></param></call>
<call command="enableMultiselect"><param>1</param></call>
</beforeInit>
<column id="id_attribute" width="40" type="ro" align="right" sort="int"><?php echo _l('ID')?></column>
<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {?>
<column id="position" width="40" type="ro" align="right" sort="int"><?php echo _l('Position')?></column>
<?php } 
if ($sc_active) {?><column id="sc_active" width="50" type="co" align="center" sort="str"><?php echo _l('Used')?><option value="0"><![CDATA[<?php echo _l('No')?>]]></option><option value="1"><![CDATA[<?php echo _l('Yes')?>]]></option></column><?php } ?>
<?php if ($iscolor) {?><column id="color" width="60" type="edtxt" align="left" sort="str"><?php echo _l('Color code')?></column>
<column id="color2" width="60" type="cp" align="left" sort="str"><?php echo _l('Color')?></column><?php } ?>
<?php
	echo 	$cols;
?>
<?php if ($iscolor) {?><column id="image" width="120" type="ro" align="center" sort="na"><?php echo _l('Image')?></column><?php } ?>
<column id="usedby" width="40" type="ro" align="right" sort="int"><?php echo _l('Used by')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_win-attribute').'</userdata>'."\n";
	echo 	$xml;
?>
</rows>
