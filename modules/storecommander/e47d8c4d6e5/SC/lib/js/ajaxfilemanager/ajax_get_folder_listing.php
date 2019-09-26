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
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
	echo '{';
	$count = 1;
	foreach(getFolderListing(CONFIG_SYS_ROOT_PATH) as $k=>$v)
	{
		

		echo (($count > 1)?', ':''). "'" . $v . "':'" . $k . "'"; 
		$count++;
	}
	echo "}";
?>
