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

	function getSettings()
	{
		global $default_settings,$local_settings;
		$default_settings_temp = $default_settings;
		
		if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
		{
			// PS ADD A CONFIGURATION FOR THIS ACTION
			// SO CUSTOMER PARAMS IT IN PS BACKOFFICE
			unset($default_settings_temp['CAT_SEO_NAME_TO_URL']);
		}
		
		foreach($default_settings_temp AS $k => $v){
			if($v['id']!="CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE")
			{
				echo "<row id=\"".$v['id']."\">";
				echo 		"<cell>"._l($v['section1'])."</cell>";
				echo 		"<cell>"._l($v['section2'])."</cell>";
				echo 		"<cell><![CDATA["._l($v['name'])."]]></cell>";
				echo 		"<cell><![CDATA[".$local_settings[$k]['value']."]]></cell>";
				echo 		"<cell><![CDATA["._l($v['description'])."]]></cell>";
				echo 		"<cell><![CDATA[<span style='color:#888888'>".$v['default_value']."</span>]]></cell>";
				echo 		"<cell>".$v['id']."</cell>";
				echo "</row>";
			}
		}
	}

	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); 
	} else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<rows>';
	getSettings();
	echo '</rows>';

