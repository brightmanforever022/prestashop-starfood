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

	$sql="SELECT * FROM "._DB_PREFIX_."attribute_group_lang";
	$rows=Db::getInstance()->ExecuteS($sql);
	$names=array();
	foreach($rows AS $row)
	{
		$names[$row['id_attribute_group']][$row['id_lang']]['name']=$row['name'];
		$names[$row['id_attribute_group']][$row['id_lang']]['public_name']=$row['public_name'];
	}
	
	$xml='';
	$cols='';
	$filters='';
	foreach($languages AS $lang)
	{
		$cols.='<column id="name¤'.$lang['iso_code'].'" width="100" type="edtxt" align="left" sort="str">'._l('Name').' '.strtoupper($lang['iso_code']).'</column>
<column id="public_name¤'.$lang['iso_code'].'" width="100" type="edtxt" align="left" sort="str">'._l('Public name').' '.strtoupper($lang['iso_code']).'</column>';
		$filters.=',#text_filter,#text_filter';
	}

	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		$groups=Db::getInstance()->executeS('
			SELECT DISTINCT agl.`name`, ag.*, agl.*
			FROM `'._DB_PREFIX_.'attribute_group` ag
			LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
				ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND `id_lang` = '.(int)$id_lang.')
			ORDER BY `position` ASC
		');
	}
	else
		$groups=AttributeGroup::getAttributesGroups($id_lang);

	foreach($groups AS $row)
	{
		$xml.=("<row id='".$row['id_attribute_group']."'>");
			$xml.=("<cell style=\"color:#999999\">".$row['id_attribute_group']."</cell>");
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				$xml.=("<cell>".$row['position']."</cell>");
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				$xml.=("<cell><![CDATA[".$row['group_type']."]]></cell>");
			$xml.=("<cell><![CDATA[".$row['is_color_group']."]]></cell>");
			foreach($languages AS $lang)
			{
				@$xml.=("<cell><![CDATA[".$names[$row['id_attribute_group']][$lang['id_lang']]['name']."]]></cell>");
				@$xml.=("<cell><![CDATA[".$names[$row['id_attribute_group']][$lang['id_lang']]['public_name']."]]></cell>");
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
<call command="attachHeader"><param><![CDATA[#text_filter,<?php echo (version_compare(_PS_VERSION_, '1.5.0.0', '>=')?'#text_filter,#select_filter,':'');?>#select_filter<?php echo $filters ?>]]></param></call>
</beforeInit>
<column id="id_attribute_group" width="40" type="ro" align="right" sort="int"><?php echo _l('ID')?></column>
<?php
if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
?>
<column id="position" width="40" type="ro" align="right" sort="int"><?php echo _l('Position')?></column>
<column id="group_type" width="60" type="coro" align="center" sort="str"><?php echo _l('Type')?><option value="select"><![CDATA[<?php echo _l('select')?>]]></option><option value="radio"><![CDATA[<?php echo _l('radio')?>]]></option><option value="color"><![CDATA[<?php echo _l('color')?>]]></option></column>
<?php
}?>
<column id="is_color_group" width="60" type="coro" align="center" sort="int"><?php echo _l('Color group?')?><option value="0"><![CDATA[<?php echo _l('No')?>]]></option>
<option value="1"><![CDATA[<?php echo _l('Yes')?>]]></option></column>
<?php
	echo 	$cols;
?>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_win-attribute_group').'</userdata>'."\n";
	echo 	$xml;
?>
</rows>
