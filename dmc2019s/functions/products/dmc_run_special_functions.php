<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_run_special_functions.php											*
*  inkludiert von dmc_write_art.php 										*	
*  spezielle funktionen der shops ausfuehren, wie index, cache etc 			*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
13.03.2012
- neu
15.05.2012
- dmc_set_veyton_group_permissions // GGfl Kundengruppenberechtigungen setzen
*/
		if (DEBUGGER>=1) fwrite($dateihandle, "dmc_run_special_functions \n");
		
		// Spezielle Gambio GX 2 Funktionen
		// SHOPSYSTEM == 'gambiogx'  -> reset_categories_index
		if (strpos(strtolower(SHOPSYSTEM), 'gambiogx2') !== false) {
			//require_once('includes/application_top.php');
			# clear cache
			// $coo_cache_control->clear_cache();
			# optional: clear categories_index
			// $coo_cache_control->rebuild_products_categories_index();
			$coo_feature_handler = MainFactory::create_object('ProductFeatureHandler');
			$coo_feature_handler->build_categories_index($Artikel_ID);
		} // end if 
		
		// Spezielle HHG Funktionalitaeten
		if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false) {
			include('../../functions/hhg_special_functions.php');
			//HHG -> Artikel  dem Shop zuordnen 
			HHGSetProduct2Store($Artikel_ID);	
			//HHG -> Produkt Calkulationstabelle fuellen 
			HHGSetProductsCalculation($Artikel_ID);
		} 
		
		// GGfl Kundengruppenberechtigungen setzen
		if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
			// Kundenrechte pid	 permission	Primärschlüssel pgroup Veyon
		/*	if (substr($Artikel_Artikelnr,0,1)=='J') {
				$gruppenberechtigung='shop_1'; 
				dmc_set_veyton_group_permissions($Artikel_ID,$gruppenberechtigung,'product','1');
				$gruppenberechtigung='shop_3'; 
				dmc_set_veyton_group_permissions($Artikel_ID,$gruppenberechtigung,'product','1');
			} else if (substr($Artikel_Artikelnr,0,1)=='P') {
				$gruppenberechtigung='shop_1'; 
				dmc_set_veyton_group_permissions($Artikel_ID,$gruppenberechtigung,'product','0');
				$gruppenberechtigung='shop_2'; 
				dmc_set_veyton_group_permissions($Artikel_ID,$gruppenberechtigung,'product','1');
				$gruppenberechtigung='shop_3'; 
				dmc_set_veyton_group_permissions($Artikel_ID,$gruppenberechtigung,'product','1');
			} else if (substr($Artikel_Artikelnr,0,1)=='X') {
				$gruppenberechtigung='shop_1'; 
				dmc_set_veyton_group_permissions($Artikel_ID,$gruppenberechtigung,'product','0');
				$gruppenberechtigung='shop_3'; 
				dmc_set_veyton_group_permissions($Artikel_ID,$gruppenberechtigung,'product','1');
			} else {
				// NUR fuer MULTI_STORE
				// $gruppenberechtigung='shop_1'; 
				// dmc_set_veyton_group_permissions($Artikel_ID,$gruppenberechtigung,'product');
			}
			*/
			//$gruppenberechtigung='group_permission_1';
			//dmc_set_veyton_group_permissions($Artikel_ID,$gruppenberechtigung);
			//$gruppenberechtigung='group_permission_8';
			//dmc_set_veyton_group_permissions($Artikel_ID,$gruppenberechtigung);
			//$gruppenberechtigung='group_permission_11';
			//dmc_set_veyton_group_permissions($Artikel_ID,$gruppenberechtigung);
		} 
		
		if (DEBUGGER>=1) fwrite($dateihandle, "dmc_run_special_functions - ende\n");
		
		
?>
	