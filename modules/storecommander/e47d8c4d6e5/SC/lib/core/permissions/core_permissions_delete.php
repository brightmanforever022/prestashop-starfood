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

	$id_lang=(int)Tools::getValue('id_lang', 1);
	$profil=Tools::getValue('id', 0);
		
	$_is = "profil";
	
	if(strpos($profil, "pr_") !== false)
	{
		$_is = "profil";
		$id = str_replace("pr_", "", $profil);
		if(!empty($local_permissions["profils"][$id]))
			unset($local_permissions["profils"][$id]);
	}
	elseif(strpos($profil, "em_") !== false)
	{
		$_is = "employee";
		$id = str_replace("em_", "", $profil);
		if(!empty($local_permissions["employees"][$id]))
			unset($local_permissions["employees"][$id]);
	}
		
	SCI::updateConfigurationValue('SC_PERMISSIONS', serialize($local_permissions));

