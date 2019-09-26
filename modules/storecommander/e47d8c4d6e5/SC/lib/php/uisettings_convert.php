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


class UISettingsConvert
{
	public static function convert($datas)
	{
		$new_datas = $datas;
		
		$file_version = 1;
		$actual_version = 1;
		if(!empty($datas["version"]) && is_numeric($datas["version"]))
			$file_version = (int)$datas["version"];
		$new_datas["version"] = $file_version;
		
		if(defined("SC_UISETTINGS_VERSION") && SC_UISETTINGS_VERSION>0)
			$actual_version = (int)SC_UISETTINGS_VERSION;
		
		if($file_version!=$actual_version)
		{
			$start = $file_version+1;
			for ($i=$start; $i<=$actual_version;$i++)
			{
				$new_datas = call_user_func(array(self, '_convert_from_'.$file_version.'_to_'.$i), $new_datas);
				$file_version++;
			}
			$new_datas["version"] = $actual_version;
			UISettings::write_ini_file($new_datas, false);
		}
		
		return $new_datas;
	}
		
	public static function _convert_from_1_to_2($datas)
	{
		$new_datas = $datas;
		
		$to_delete = array(
			"cat_win-attribute_group",
			"cat_attachment",
			"cat_customization",
			"cat_image",
			"cat_win-feature",
			"cat_win-feature_value"
		);
		
		foreach($to_delete as $grid)
		{
			if(!empty($new_datas[$grid]))
			{
				$new_datas[$grid] = "";
				unset($new_datas[$grid]);
			}
		}
		
		return $new_datas;
	}
		
	public static function _convert_from_2_to_3($datas)
	{
		$new_datas = $datas;
		
		foreach($datas as $name)
		{
			if(substr($name, 0, 15)=="cat_combination")
			{
				$new_datas[$name] = "";
				unset($new_datas[$name]);
			}
		}
		
		return $new_datas;
	}
		
	public static function _convert_from_3_to_4($datas)
	{
		$new_datas = $datas;
		
		foreach($datas as $name=>$values)
		{
			if(substr($name, 0, 15)=="cat_combination")
			{
				$new_datas[$name] = "";
				unset($new_datas[$name]);
			}
		}
		
		return $new_datas;
	}
}