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

	function getPermissions()
	{
		global $permissions_list, $local_permissions, $profil;
		
		$employee_values = array();
		$profil_values = array(); 
		$_is = "profil";
		$super_admin_id = 1;
		$gris = false; 
		
		if(strpos($profil, "pr_") !== false)
		{
			$_is = "profil";
			$id = str_replace("pr_", "", $profil);
			if($id==1)
				$gris = true;
			if(!empty($local_permissions["profils"][$id]))
				$profil_values = $local_permissions["profils"][$id];
		}
		elseif(strpos($profil, "em_") !== false)
		{
			$_is = "employee";
			$id = str_replace("em_", "", $profil);
			$employee = new Employee($id);
			if(!empty($local_permissions["employees"][$id]))
				$employee_values = $local_permissions["employees"][$id];
			if(!empty($local_permissions["profils"][$employee->id_profile]))
				$profil_values = $local_permissions["profils"][$employee->id_profile];
			if($employee->id_profile==1)
				$gris = true;
		}
		$csv_en = "";
		$csv_fr = "";
		foreach($permissions_list AS $k => $v)
		{
			$value = null;
			$value_profil = null;
			
			// Si employé sélectionné
			// et si la config n'est pas propre à l'employé mais au profil
			// OU
			// Si profil sélectionné
			// et si la config est propre au profil
			if(isset($profil_values[$v['id']]))
			{
				$value = $profil_values[$v['id']];
				$value_profil = $profil_values[$v['id']];
			}
			
			// Si employé sélectionné 
			// et si la config est propre à l'employé
			if(isset($employee_values[$v['id']]))
			{
				$value = $employee_values[$v['id']];
			}
			
			// si ni config pour l'employé et pour le profil
			if($value===null)
			{
				if($_is=="profil")
				{
					if($id==$super_admin_id)
						$value = $value_profil = $v["default_admin"];
					else
						$value = $value_profil = $v["default_value"];
				}
				elseif($_is=="employee")
				{
					if($employee->id_profile==$super_admin_id)
						$value = $value_profil = $v["default_admin"];
					else
						$value = $value_profil = $v["default_value"];
				}
			}
			if($value_profil===null)
			{
				if($_is=="profil")
				{
					if($id==$super_admin_id)
						$value_profil = $v["default_admin"];
					else
						$value_profil = $v["default_value"];
				}
				elseif($_is=="employee")
				{
					if($employee->id_profile==$super_admin_id)
						$value_profil = $v["default_admin"];
					else
						$value_profil = $v["default_value"];
				}
			}
			
			$is_diff = false;
			if($value_profil!=$value)
				$is_diff = true;
			if($is_diff==1)
				$is_diff = _l('Yes');
			else
				$is_diff = _l('No');
			
			if($value_profil==1)
				$value_profil = _l('Yes');
			else
				$value_profil = _l('No');
			
			$name = _l($v['name']);
			$name = str_replace('Product grid:',_l('Product grid:'),$name);
			$name = str_replace('Customer grid:',_l('Customer grid:'),$name);
			$name = str_replace('Order grid:',_l('Order grid:'),$name);
			
			echo "<row id=\"".$profil."#".$v['id']."\">";
			echo 		"<cell><![CDATA["._l($v['section1'])."]]></cell>";
			echo 		"<cell><![CDATA["._l($v['section2'])."]]></cell>";
			echo 		"<cell><![CDATA[".$name."]]></cell>";
			echo 		"<cell><![CDATA[".$value."]]></cell>";
			echo 		"<cell><![CDATA["._l($v['description'])."]]></cell>";
			echo 		"<cell><![CDATA[".$value_profil."]]></cell>";
			echo 		"<cell><![CDATA[".$is_diff."]]></cell>";
			echo "</row>";
			$csv_en .= $v['name']."\n";
			$csv_fr .= _l($v['name'])."\n";
		}
		/*if(!empty($csv_en))
			file_put_contents(dirname(__FILE__)."/liste_permissions_en.csv", $csv_en);
		if(!empty($csv_fr))
			file_put_contents(dirname(__FILE__)."/liste_permissions_fr.csv", $csv_fr);*/
	}

	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); 
	} else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<rows>';
	getPermissions();
	echo '</rows>';

