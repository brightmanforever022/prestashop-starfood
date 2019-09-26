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

	$QuickAccess= new QuickAccess();
	$tmp=array();
	foreach($languages AS $lang){
		$tmp[$lang['id_lang']]="Store Commander";
	}
	$QuickAccess->name=$tmp;
	$QuickAccess->link="SC/index.php";
	$QuickAccess->new_window=true;
	$QuickAccess->add();
	echo _l('The shortcut has been created. The installation is finished you can now use Store Commander!',1);
	