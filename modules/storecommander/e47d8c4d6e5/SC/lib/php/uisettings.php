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

class UISettings
{

	public static function getFilename()
	{
		global $sc_agent;
		return SC_TOOLS_DIR."UISettings/".$sc_agent->id_employee.".ini";
	}

	// ----------------------------------------------------------------------------
	//
	//  Function:   loadJS
	//  Purpose:		Load settings from ini file and build JS cache inside SC
	//
	// ----------------------------------------------------------------------------
	public static function loadJS($page="cat_tree")
	{
		global $sc_agent;
		if (isset($_GET['resetuisettings']) && $_GET['resetuisettings']==1)
			self::resetSettings();
		echo "ui_settings=new Object();\n";
		if (!is_dir(SC_TOOLS_DIR."UISettings")) 
		{
			$writePermissions=octdec('0'.substr(decoct(fileperms(realpath(SC_PS_PATH_DIR.'img/p'))),-3));
			$old = umask(0);
			mkdir(SC_TOOLS_DIR."UISettings",$writePermissions);
			umask($old);
		}
		
		$prefix = "cat_";
		if(!empty($page))
		{
			$exp = explode("_",$page);
			$prefix = $exp[0]."_";
		}
		
// skip loading of biggggg array in page
		$employee_settings = array();

		$filename = self::getFilename();
		if(file_exists($filename))
			$employee_settings = parse_ini_file($filename, true);
		$employee_settings = UISettingsConvert::convert($employee_settings);
		foreach ($employee_settings as $key=>$value)
		{
			if(strpos($key, "start_".$prefix) !== false)
				echo 'ui_settings["'.$key.'"]="'.(string)htmlspecialchars($value).'";'."\n";
		}
	}

	// ----------------------------------------------------------------------------
	//
	//  Purpose:		Get a setting from ini file
	//
	// ----------------------------------------------------------------------------
	public static function getSetting($name)
	{
		global $sc_agent;
		$employee_settings = array();
		$filename = self::getFilename();
		if(file_exists($filename))
			$employee_settings = parse_ini_file($filename, true);
		$employee_settings = UISettingsConvert::convert($employee_settings);
		foreach ($employee_settings as $key=>$value)
			if ($key==$name)
				return $value;
		return null;
	}


	public static function load_ini_file()
	{
		$return = array();
		$filename = self::getFilename();
		if(file_exists($filename))
		{
			$return = parse_ini_file($filename, true);
			$return = UISettingsConvert::convert($return);
		}
		return $return;
	}
	
	
	public static function write_ini_file($assoc_arr, $has_sections=FALSE) {
		$path = self::getFilename();
		$content = "";
		$assoc_arr["version"] = SC_UISETTINGS_VERSION;
		if ($has_sections) {
			foreach ($assoc_arr as $key=>$elem) {
				$content .= "[".$key."]\n";
				foreach ($elem as $key2=>$elem2) {
					if(is_array($elem2))
					{
						for($i=0;$i<count($elem2);$i++)
						{
							$content .= $key2."[] = \"".$elem2[$i]."\"\n";
						}
					}
					else if($elem2=="") $content .= $key2." = \n";
					else $content .= $key2." = \"".$elem2."\"\n";
				}
			}
	    }
	    else {
		    foreach ($assoc_arr as $key=>$elem) {
			    if(is_array($elem))
			    {
			    	for($i=0;$i<count($elem);$i++)
			    	{
			    	$content .= $key."[] = \"".$elem[$i]."\"\n";
			    	}
			    }
			    else if($elem=="") $content .= $key." = \n";
			    else $content .= $key." = \"".$elem."\"\n";
		    }
	    }
	 	if (!$handle = @fopen($path, 'w'))
		 	return false;
	 	if (!@fwrite($handle, $content))
	 		return false;
	 	@fclose($handle);
	 	return true;
	}

	public static function resetSettings()
	{
		dirEmpty(SC_TOOLS_DIR.'UISettings');
	}

}