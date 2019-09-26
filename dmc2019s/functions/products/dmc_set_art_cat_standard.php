<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_set_art_cat_standard.php												*
*  inkludiert von dmc_write_art.php 										*	
*  Artikel Kategroriezuordnungen für Shop anlegen							*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
13.03.2012
- neu
*/
	
	    if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_art_cat_standard - PRODUCTS_TO_CATEGORIES for Kategorie_ID=".$Kategorie_ID." \n");
        // Kategorie eintragen, wenn Artikel neu und zugewiesen
		// bei einem vorhandenen Artikel sollte die Kategorie nicht mehr geändert werden, da mehrere Kategorien zulässig,
		// und daher die alte Kategorie übergeben werden müsste
		// Es werden Kategorien ggfls hinzugefügt.	      
	    //if ($Kategorie_ID!="0" && ($exists==0 || UPDATE_PROD_TO_CAT == 1))
	    if ($Kategorie_ID!="0" && $Kategorie_ID!="")
	    {
			if (is_numeric($Kategorie_ID)) {
				if (DEBUGGER>=1) fwrite($dateihandle, "Kategorie mit Kategorie_ID=$Kategorie_ID zuordnen\n");
				// Kategorie_ID ist eine Zahl, daher KEINE Sonderkategorie				
				// Überprüfen, ob Produkt Kategorien zugeordnet sind
		        $cmd = 	"select products_id,categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where " .
						"products_id='$Artikel_ID'";
		        // Kategorie-Zuordnungen löschen, wenn bereits existent
		        $desc_query = dmc_db_query($cmd);
		        if (($desc = dmc_db_fetch_array($desc_query)))
		        {
					$cmd = 	"delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where " .
							"products_id='$Artikel_ID'";
					dmc_db_query($cmd);
		        }
				// (Haupt)-Kategoriezuordnung eintragen
				if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
					$insert_sql_data= array(
			            'products_id' => $Artikel_ID,
			            'categories_id' => $Kategorie_ID,
						// 'store_id'	=> $STORE_ID,				// Ab veyton 4.2
			            'master_link' => '1');
					if (SHOPSYSTEM_VERSION>=4.2) $insert_sql_data['store_id'] = SHOP_ID;		// Ab veyton 4.2 
				} else {
					$insert_sql_data= array(
			            'products_id' => $Artikel_ID,
			            'categories_id' => $Kategorie_ID);
				}
				dmc_sql_insert_array(TABLE_PRODUCTS_TO_CATEGORIES, $insert_sql_data);
				
				// Zusatzkategorien zuordnen, wenn vorhanden
				if ($multicat) // gesetzt in dmc_generate_cat_id
					for ($i=1; $i<=count($Kategorie_IDs); $i++) {
						$Kategorie_ID = $Kategorie_IDs[$i];
						if (DEBUGGER>=1) fwrite($dateihandle, "Kategorie_ID=".$Kategorie_ID." eintragen für products_id=".$Artikel_ID." \n");				
						if ($Kategorie_ID == "") break; // Abbruch, wenn keine Kategorie_ID
						
						// Kategoriezuordnung eintragen
						if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
							$insert_sql_data= array(
								'products_id' => $Artikel_ID,
								'categories_id' => $Kategorie_ID,
								'master_link' => '1');
						} else {
							$insert_sql_data= array(
								'products_id' => $Artikel_ID,
								'categories_id' => $Kategorie_ID);
						}
						if (SHOPSYSTEM_VERSION>=4.2) $insert_sql_data['store_id'] = SHOP_ID;		// Ab veyton 4.2 
						dmc_sql_insert_array(TABLE_PRODUCTS_TO_CATEGORIES, $insert_sql_data);
							
					} // end for 
		    } else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "/// Problem mit der Kategoriezuordnung, pruefen Sie ob ID durch dmc_generate_cat_id.php ermittelt wurde -> hier ID = $Kategorie_ID\n");			
			} // endif 
	    } // ende Kategorie eintragen
		
			    if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_art_cat_standard - ende \n");

?>
	