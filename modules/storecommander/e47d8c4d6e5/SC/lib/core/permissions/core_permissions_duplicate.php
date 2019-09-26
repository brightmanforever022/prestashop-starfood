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

	$target=Tools::getValue('id_target', 0);
	$source=Tools::getValue('id_source', 0);
	// Récupréation des valeurs de la destination
	$source_values = array();
	if(strpos($source, "pr_") !== false)
	{
		$id_source = str_replace("pr_", "", $source);
		if(!empty($local_permissions["profils"][$id_source]))
			$source_values = $local_permissions["profils"][$id_source];
	}
	elseif(strpos($source, "em_") !== false)
	{
		$id_source = str_replace("em_", "", $source);
		if(!empty($local_permissions["employees"][$id_source]))
			$source_values = $local_permissions["employees"][$id_source];
		if (empty($source_values)) // try to get profile settings if no employee settings
		{
			$agent=new Employee((int)$id_source);
			if(!empty($local_permissions["profils"][$agent->id_profile]))
				$source_values = $local_permissions["profils"][$agent->id_profile];
		}
	}
	
	if(strpos($target, "pr_") !== false)
	{
		$id_target = str_replace("pr_", "", $target);
		if(!empty($source_values))
			$local_permissions["profils"][$id_target] = $source_values;
		else
			unset($local_permissions["profils"][$id_target]);
	}
	elseif(strpos($target, "em_") !== false)
	{
		$id_target = str_replace("em_", "", $target);
		if(!empty($source_values))
			$local_permissions["employees"][$id_target] = $source_values;
		else
			unset($local_permissions["employees"][$id_target]);
	}
		
	SCI::updateConfigurationValue('SC_PERMISSIONS', serialize($local_permissions));

