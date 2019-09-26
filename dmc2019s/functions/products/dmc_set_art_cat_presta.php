<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_set_art_cat_presta.php												*
*  inkludiert von dmc_write_art.php 										*	
*  Artikel Kategroriezuordnungen für Presta anlegen							*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
13.03.2012
- neu
*/

	        if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_art_cat_presta - Presta PRODUCTS_TO_CATEGORIES \n");
            // Update oder insert?
            // Bestehende Daten laden
            $cmd = 	"select id_product from " . TABLE_PRODUCTS_TO_CATEGORIES .
					" where id_product='$Artikel_ID'";
	 		
			$desc_query = dmc_db_query($cmd);
            
			if ($desc = dmc_db_fetch_array($desc_query))
            { //  Beschreibung update
                 if (UPDATE_PROD_TO_CAT == 1) 
				 {
                    // nur Standardprache 1
                    /*
					$sql_data_array = array(
					    'id_category' => $Kategorie_ID,
                        'id_product' => $Artikel_ID
                    );
					// Beschreibung Std update
					dmc_sql_update_array(TABLE_PRODUCTS_TO_CATEGORIES, 
										$sql_data_array, 
										"id_product = '$Artikel_ID' AND store_id=1");
					*/
					// Alte Verknupfung loschen
					dmc_sql_delete(TABLE_PRODUCTS_TO_CATEGORIES, "id_product='".$Artikel_ID."'");
				
					 for ($i=0; $i<=count($Kategorie_IDs); $i++) {
						$Kategorie_ID = $Kategorie_IDs[$i];
					
						// Insert 
					   // Bestehende Daten laden
						$cmd = "SELECT count(*)+0 as Anzahl_Position FROM " . TABLE_PRODUCTS_TO_CATEGORIES .
							" where id_category='$Kategorie_ID'";
						$desc_query = dmc_db_query($cmd);
						if ($desc = dmc_db_fetch_array($desc_query))
						{
							$Position=$desc['Anzahl_Position'];
						}
						 $sql_data_array = array(
									'id_category' => $Kategorie_ID,
									'id_product' => $Artikel_ID,
									'position' => $Position
							);
						dmc_sql_insert_array(TABLE_PRODUCTS_TO_CATEGORIES, $sql_data_array);
					 }
			    }
            }
            else
            {
                // Insert 
               // Bestehende Daten laden
			   for ($i=0; $i<=count($Kategorie_IDs); $i++) {
					$Kategorie_ID = $Kategorie_IDs[$i];
					
					$cmd = "SELECT count(*)+0 as Anzahl_Position FROM " . TABLE_PRODUCTS_TO_CATEGORIES .
						" where id_category='$Kategorie_ID'";
					$desc_query = dmc_db_query($cmd);
					if ($desc = dmc_db_fetch_array($desc_query))
					{
						$Position=$desc['Anzahl_Position'];
					}
					 $sql_data_array = array(
								'id_category' => $Kategorie_ID,
								'id_product' => $Artikel_ID,
								'position' => $Position
						);
					dmc_sql_insert_array(TABLE_PRODUCTS_TO_CATEGORIES, $sql_data_array);
			   }
					
            } // end if PRODUCTS_TO_CATEGORIES
        	
?>
	