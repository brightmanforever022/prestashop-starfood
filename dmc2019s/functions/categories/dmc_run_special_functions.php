<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_run_special_functions.php											*
*  inkludiert von dmc_write_cat.php 										*	
*  spezielle Funktionen der shops ausfuehren, wie index, cache etc 			*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
15.03.2012
- neu
*/
		if (DEBUGGER>=1) fwrite($dateihandle, "dmc_run_special_functions \n");
		
		//HHG -> Kategorie dem Shop zuordnen
		if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false) {
			if (STORE_ALL)
						 $sql_data_array = array(
							  'categories_id' => $Kategorie_ID,
							  'store_all' => '1',
							  'store_1' => 0);
			else
						 $sql_data_array = array(
							  'categories_id' => $Kategorie_ID,
							  'store_all' => '0',
							  'store_1' => 1);
			
			if (!$exists) 
				xtc_db_perform(TABLE_MS_CATEGORIES_TO_STORE, $sql_data_array);
			else 
				xtc_db_perform(TABLE_MS_CATEGORIES_TO_STORE, $sql_data_array,'update', "categories_id = '$Kategorie_ID'");
		 
		}
		
		// SEO Tool von Bluegate
		// Einbinden, wenn existiert
		if (file_exists(DIR_FS_INC.'bluegate_seo.inc.php')) { 
			if (DEBUGGER>=1) fwrite($dateihandle, "*SEO Tool von Bluegate initialisieren* \n");
			// *************************** BLUEGATE SUMA OPTIMIZER ************************* //
			include_once(DIR_FS_INC . 'xtc_db_error.inc.php');
	  
			require_once (DIR_FS_INC.'bluegate_seo.inc.php');
			!$bluegateSeo ? $bluegateSeo = new BluegateSeo() : false;
			// Update bluegate_seo_url Table
			$bluegateSeo->updateSeoDBTable('category','update', $Kategorie_ID);	
		} // end if // SEO Tool von Bluegate
		
?>
	