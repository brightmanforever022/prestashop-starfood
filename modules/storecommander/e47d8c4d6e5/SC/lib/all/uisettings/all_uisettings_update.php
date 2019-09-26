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

if(empty($sc_agent->id_employee))
	die();

// RECUPERATION ACTUELLE CONFIG
$employee_settings = UISettings::load_ini_file();

// ECRASEMENT AVEC LES NOUVELLES DONNEES
$name=Tools::getValue('name','');
$data=Tools::getValue('data','');
if($name!=''/* && $data!=''*/)
	$employee_settings[$name] = $data;

if(substr($name, 0, 15)=="cat_combination")
{
	$new_value = "";
	$parts = explode("|", $employee_settings[$name]);
	foreach($parts as $i=>$part)
	{
		$new_parts = "";
		if($i>0)
			$new_value .= "|";
			
		$fields = explode("-", $part);
		foreach($fields as $j=>$field)
		{
			if($j>0)
				$new_parts .= "-";
		
			list($name_field, $value_field) = explode(":", $field);
			if(substr($name_field, 0, 5)=="attr_")
			{
				$exp = explode("_",$name_field);
				$name_field = "{attr_".$exp[1]."}";
			}
			if(!empty($name_field))
				$new_parts .= $name_field.":".$value_field;
		}
		$new_value .= $new_parts;
	}
	$employee_settings[$name] = $new_value;
}

$employee_settings = UISettingsConvert::convert($employee_settings);

// ECRITURE DANS FICHIER INI
UISettings::write_ini_file($employee_settings, false);