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

$action=Tools::getValue('action',0);
if(!empty($action) && ($action=="add_mass" || $action=="delete_mass"))
{
	$permissions=Tools::getValue('permissions',0);
	$profil=Tools::getValue('profil',0);
	if(!empty($permissions) && !empty($profil))
	{
		$permissions = explode(",", $permissions);
		
		if($action=="add_mass")
			$value = 1;
		else
			$value = 0;
				
		if(strpos($profil, "pr_") !== false)
		{
			$_is = "profil";
			$id = str_replace("pr_", "", $profil);
			foreach($permissions as $permission)
			{
				$permission = explode("#", $permission);
				$local_permissions["profils"][$id][$permission[1]] = (int)$value;
			}
		}
		elseif(strpos($profil, "em_") !== false)
		{
			$_is = "employee";
			$id = str_replace("em_", "", $profil);
			foreach($permissions as $permission)
			{
				$permission = explode("#", $permission);
				$local_permissions["employees"][$id][$permission[1]] = (int)$value;
			}
		}
		
		SCI::updateConfigurationValue('SC_PERMISSIONS', serialize($local_permissions));
	}
}
else
{
	list($id_element,$id_permission)=explode("#",Tools::getValue('gr_id',"0#0"));
	$value=Tools::getValue('value',0);

	if(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="updated" && !empty($id_element) && !empty($id_permission))
	{
		if($value!=0 && $value!=1)
			$value = 0;
		
		if(strpos($id_element, "pr_") !== false)
		{
			$_is = "profil";
			$id = str_replace("pr_", "", $id_element);
			$local_permissions["profils"][$id][$id_permission] = (int)$value;
		}
		elseif(strpos($id_element, "em_") !== false)
		{
			$_is = "employee";
			$id = str_replace("em_", "", $id_element);
			$local_permissions["employees"][$id][$id_permission] = (int)$value;
		}
		
		SCI::updateConfigurationValue('SC_PERMISSIONS', serialize($local_permissions));
		
		$action = "update";
	}
	
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<data>';
	echo "<action type='".$action."' sid='".$_POST["gr_id"]."' tid='".$_POST["gr_id"]."'/>";
/*
	echo "<debug>";
	print_r($local_settings);
	echo "</debug>";
*/
	echo '</data>';
}