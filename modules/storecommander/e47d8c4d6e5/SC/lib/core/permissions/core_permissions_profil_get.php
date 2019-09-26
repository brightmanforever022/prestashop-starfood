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

	$id_lang=(int)Tools::getValue('id_lang');

	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); 
	} else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<tree id="0">';
	
	$profiles = Profile::getProfiles($id_lang);
	foreach($profiles as $profile)
	{
		$icon = "folderOpen.gif";
		$icon_employee = "user.png";
		$icon_employee_diff = "user_red.png";
		echo '<item 
				id="pr_'.$profile['id_profile'].'" 
				text="'.htmlspecialchars($profile['name']).'"
				im0="'.$icon.'"
				im1="'.$icon.'"
				im2="'.$icon.'"
				tooltip="'.htmlspecialchars($profile['name']).'">';
		
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			$employees = Employee::getEmployeesByProfile($profile['id_profile']);
		else
		{
			$employees = Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'employee`
			WHERE `id_profile` = '.(int)$profile['id_profile']);
		}
		foreach($employees as $employee)
		{
			$icon_employee_temp = $icon_employee;
			if(!empty($local_permissions["employees"][$employee['id_employee']]))
				$icon_employee_temp = $icon_employee_diff;
			
			echo '<item
				id="em_'.$employee['id_employee'].'"
				text="'.htmlspecialchars($employee['firstname'].' '.$employee['lastname']).'"
				im0="'.$icon_employee_temp.'"
				im1="'.$icon_employee_temp.'"
				im2="'.$icon_employee_temp.'"
				tooltip="#'.$employee['id_employee'].' - '.htmlspecialchars($employee['firstname'].' '.$employee['lastname']).' ('.$employee['email'].')"></item>\n';
		}
		
		echo	"</item>\n";
	}
	
	echo '</tree>';

