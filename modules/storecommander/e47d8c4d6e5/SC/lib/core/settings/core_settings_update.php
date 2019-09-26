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

	$id_setting=Tools::getValue('gr_id');
	$value=Tools::getValue('value');

	if(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="updated"){
		
		$action = "update";
		if (sc_array_key_exists($id_setting,$local_settings))
		{
			$local_settings[$id_setting]['value']=$value;
			saveSettings();
			if (sc_array_key_exists('needRefresh',$default_settings[$id_setting]))
				if ($default_settings[$id_setting]['needRefresh'])
					$action = "updateAndRefresh";
		}
		$newId = $id_setting;
	}
	
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<data>';
	echo "<action type='".$action."' sid='".$_POST["gr_id"]."' tid='".$newId."'/>";
/*
	echo "<debug>";
	print_r($local_settings);
	echo "</debug>";
*/
	echo '</data>';
