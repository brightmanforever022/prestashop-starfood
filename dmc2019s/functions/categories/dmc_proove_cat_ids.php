<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_proove_cat_ids.php													*
*  inkludiert von dmc_write_cat.php 										*				
*  IDs überprüfen und exists Flag setzen									*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
15.03.2012
- neu
*/
		 if (DEBUGGER>=50) fwrite($dateihandle, "dmc_proove_cat_ids ".$Kategorie_ID."\n");

		// Vater ID ermitteln
		if (($Kategorie_Vater_ID == '0' || $Kategorie_Vater_ID == '1')) {
			//if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) 
			//	$Kategorie_Vater_ID = '1';
			//else
				$Kategorie_Vater_ID = '0';
			$kat_ebene=1;
		} else { // Vater
			$Kategorie_Vater_ID = dmc_get_category_id($Kategorie_Vater_ID);
			if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) 
				$kat_ebene == 2;				
		}
		
		// Check if exists
		
		$new_cat_id=dmc_get_category_id($Kategorie_ID);
		 fwrite($dateihandle, "dmc_proove_cat_ids .. $Kategorie_ID \n");
		if ($new_cat_id=='0')
			$exists = false;
		else
			$exists = true;
		
		/*if ($Kategorie_ID!='0' && strpos(strtolower(SHOPSYSTEM), 'presta') === false) {
			$new_cat_id=dmc_get_category_id($Kategorie_ID);
			if ($new_cat_id=='0')
				$exists = false;
			else
				$exists = true;
		} else {
			//presta
			$new_cat_id=dmc_get_category_id($Kategorie_ID);
			if ($new_cat_id=='1')
				$exists = false;
			else
				$exists = true; 
		}*/
			
?>
	