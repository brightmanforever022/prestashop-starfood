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

	    if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_art_cat_virtuemart - PRODUCTS_TO_CATEGORIES for Kategorie_ID=".$Kategorie_ID." \n");
        // Kategorie eintragen, wenn Artikel neu und zugewiesen
		// bei einem vorhandenen Artikel sollte die Kategorie nicht mehr geändert werden, da mehrere Kategorien zulässig,
		// und daher die alte Kategorie übergeben werden müsste
		// Es werden Kategorien ggfls hinzugefügt.	      
	   	if (is_numeric($Kategorie_ID)) {
				if (DEBUGGER>=1) fwrite($dateihandle, "Artikel Kategorie_ID=$Kategorie_ID zuordnen\n");
				// Kategorie_ID ist eine Zahl, daher KEINE Sonderkategorie				
				// Überprüfen, ob Produkt Kategorien zugeordnet sind
				$cmd = 	"SELECT virtuemart_product_id,virtuemart_category_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE " .
						"virtuemart_product_id='$Artikel_ID'";
		        // Kategorie-Zuordnungen löschen, wenn bereits existent
		        $desc_query = dmc_db_query($cmd);
		        if (($desc = dmc_db_fetch_array($desc_query)))
		        {
						
					$cmd = 	"DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE " .
							"virtuemart_product_id='$Artikel_ID'";
					dmc_db_query($cmd);
		        }
				// (Haupt)-Kategoriezuordnung eintragen
			    $insert_sql_data= array(
			           // id
					   'virtuemart_product_id' => $Artikel_ID,
			           'virtuemart_category_id' => $Kategorie_ID,
			           'ordering' => '0'
				);
				dmc_sql_insert_array(TABLE_PRODUCTS_TO_CATEGORIES, $insert_sql_data);
				
				// Zusatzkategorien zuordnen, wenn vorhanden
				if ($multicat) // gesetzt in dmc_generate_cat_id
					for ($i=1; $i<=count($Kategorie_IDs); $i++) {
						$Kategorie_ID = $Kategorie_IDs[$i];
						if (DEBUGGER>=1) fwrite($dateihandle, "Kategorie_ID=".$Kategorie_ID." eintragen für products_id=".$Artikel_ID." \n");				
						if ($Kategorie_ID == "") break; // Abbruch, wenn keine Kategorie_ID
						$position=$i*10;
						// Kategoriezuordnung eintragen
						$insert_sql_data= array(
							'virtuemart_product_id' => $Artikel_ID,
							'virtuemart_category_id' => $Kategorie_ID,
							'ordering' => $position
			            );
						dmc_sql_insert_array(TABLE_PRODUCTS_TO_CATEGORIES, $insert_sql_data);							
					} // end for 
		} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "/// Problem mit der Kategoriezuordnung, pruefen Sie ob ID durch dmc_generate_cat_id.php ermittelt wurde -> hier ID = $Kategorie_ID\n");			
		} // endif 
			
	     // ende Kategorie eintragen
		
		if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_art_cat_virutemart - ende \n");

?>
	