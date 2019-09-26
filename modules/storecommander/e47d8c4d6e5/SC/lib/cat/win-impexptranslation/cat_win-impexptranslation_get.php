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

$action=Tools::getValue('action');
$id_lang=(int)Tools::getValue('id_lang');
$languages = Language::getLanguages(true);

switch($action) {
	case 'group_feature':
		$sql="SELECT * FROM "._DB_PREFIX_."feature_lang";
		$rows=Db::getInstance()->ExecuteS($sql);
		$names=array();
		foreach($rows AS $row)
		{
			$names[$row['id_feature']][$row['id_lang']]['name']=$row['name'];
		}

		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$features = Feature::getFeatures($id_lang, false);
		}else{
			$features = Feature::getFeatures($id_lang);
		}

		$return .= "ID";
		foreach($languages AS $lang)
		{
			$return .= "\t"._l('Name')." ".strtoupper($lang['iso_code']);
		}
		$return .= "\n";

		foreach($features AS $row)
		{
			$return .= $row['id_feature'];
			foreach($languages AS $lang)
			{
				$return .= "\t".$names[$row['id_feature']][$lang['id_lang']]['name'];
			}
			$return .= "\n";
		}
	break;
	case 'feature_value':
		$sql="SELECT fv.id_feature_value,fvl.value,fvl.id_lang
				FROM "._DB_PREFIX_."feature_value fv 
				LEFT JOIN "._DB_PREFIX_."feature_value_lang fvl 
				ON (fvl.id_feature_value=fv.id_feature_value)";
		$rows=Db::getInstance()->ExecuteS($sql);
		$names=array();
		foreach($rows AS $row)
		{
			$names[$row['id_feature_value']][$row['id_lang']]=$row['value'];
		}

		$sql = "SELECT *
			FROM `"._DB_PREFIX_."feature_value` v
			LEFT JOIN `"._DB_PREFIX_."feature_value_lang` vl
				ON (v.`id_feature_value` = vl.`id_feature_value` AND vl.`id_lang` = ".(int)$id_lang.")
			ORDER BY vl.`value` ASC";
		$feature_values=Db::getInstance()->ExecuteS($sql);

		$return .= "ID";
		foreach($languages AS $lang)
		{
			$return .= "\t"._l('Name')." ".strtoupper($lang['iso_code']);
		}
		$return .= "\n";

		foreach($feature_values AS $row)
		{
			$return .= $row['id_feature_value'];
			foreach($languages AS $lang)
			{
				$return .= "\t".$names[$row['id_feature_value']][$lang['id_lang']];
			}
			$return .= "\n";
		}
		break;
	case 'group_attribute':
		$sql="SELECT * FROM "._DB_PREFIX_."attribute_group_lang";
		$rows=Db::getInstance()->ExecuteS($sql);
		$names=array();
		foreach($rows AS $row)
		{
			$names[$row['id_attribute_group']][$row['id_lang']]['name']=$row['name'];
			$names[$row['id_attribute_group']][$row['id_lang']]['public_name']=$row['public_name'];
		}

		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$groups=Db::getInstance()->executeS('
				SELECT DISTINCT agl.`name`, ag.*, agl.*
				FROM `'._DB_PREFIX_.'attribute_group` ag
				LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
					ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND `id_lang` = '.(int)$id_lang.')
				ORDER BY `position` ASC');
		} else {
			$groups = AttributeGroup::getAttributesGroups($id_lang);
		}

		$return .= "ID";
		foreach($languages AS $lang)
		{
			$return .= "\t"._l('Name')." ".strtoupper($lang['iso_code']);
			$return .= "\t"._l('Public name')." ".strtoupper($lang['iso_code']);
		}
		$return .= "\n";

		foreach($groups AS $row)
		{
			$return .= $row['id_attribute_group'];
			foreach($languages AS $lang)
			{
				$return .= "\t".$names[$row['id_attribute_group']][$lang['id_lang']]['name'];
				$return .= "\t".$names[$row['id_attribute_group']][$lang['id_lang']]['public_name'];
			}
			$return .= "\n";
		}
	break;
	case 'attribute_value':
		$sql="SELECT al.*,a.*
				FROM "._DB_PREFIX_."attribute a 
				RIGHT JOIN "._DB_PREFIX_."attribute_lang al ON (al.id_attribute=a.id_attribute)";
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			$sql .= " ORDER BY a.position ASC ";
		}
		$rows=Db::getInstance()->ExecuteS($sql);
		$names=array();
		foreach($rows AS $row) {
			$names[$row['id_attribute']][$row['id_lang']] = $row['name'];
		}

		$return .= "ID";
		foreach($languages AS $lang)
		{
			$return .= "\t"._l('Name')." ".strtoupper($lang['iso_code']);
		}
		$return .= "\n";

		foreach($rows AS $row)
		{
			$return .= $row['id_attribute'];
			foreach($languages AS $lang)
			{
				$return .= "\t".$names[$row['id_attribute']][$lang['id_lang']];
			}
			$return .= "\n";
		}
	break;
}

echo rtrim($return);

