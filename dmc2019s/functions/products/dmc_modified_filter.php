<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_modified_filter.php													*
*  inkludiert von dmc_write_art.php 										*				
*  Hauptartikel übergebene Variablen ermitteln und als modified  Filter setzen*
*  Copyright (C) 2014 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
25.02.2014
- neu
*/
	
			fwrite($dateihandle, "dmc_modified_filter * ");
			
			// Gglfs weitere Filter Funktionen in dmc_array_create_additional.php gesetzt ($filter=true;) 
			// und in Variable $sql_filter_array[$filter_bezeichnung] = $Artikel_Auspraegungen[$i]; geschrieben
			$filteranzahl=0;
			// Einzelne Filter durchlaufen und setzen
			foreach ($sql_filter_array as $FilterName => $FilterWert)
			{	
				//language_id	 titel	 status
				fwrite($dateihandle, " 1 ");
				$filteranzahl++;
				// ID des FilterNamens ermitteln aus product_filter_categories
				$FilterNameID[$filteranzahl] = dmc_sql_select_query('feature_id','product_filter_categories',"language_id=$language_id AND status=1 AND titel='".$FilterName."'"); 
				if ($FilterNameID[$filteranzahl]=="") {
					$filteranzahl--;		// wenn nicht vorhanden -> TODO -> anlegen
					fwrite($dateihandle, " 2 ");				
				} else {
					fwrite($dateihandle, " 3 ");
					// Wenn Filter vorhanden, dann ID des FilterWertes ermitteln aus product_filter_item					
					$FilterWertID[$filteranzahl] = dmc_sql_select_query('id','product_filter_item',"language_id=$language_id AND status=1 AND titel='".$FilterWert."'"); 
					if ($FilterWertID[$filteranzahl]=="") {
						fwrite($dateihandle, " 4 ");				
						// wenn nicht vorhanden -> anlegen	
						//  language_id	 categories_id	 title	 name	 description   [BB]	 status	 position	 colors
						$FilterWertID[$filteranzahl]=dmc_get_highest_id('id','product_filter_item') + 1;
						dmc_sql_insert('product_filter_item', 
										'id, language_id, categories_id, title, name, description, status, position, colors',
										$FilterWertID[$filteranzahl].", ".$language_id.", ".$FilterNameID[$filteranzahl].", '".$FilterName.
										"', '".$FilterName."', '', 1, 0, ''" );								
					}
					fwrite($dateihandle, " 5 ");				
					// Verkuepfung auf Filter auf Produkt eintragen
					dmc_sql_insert('products_to_filter', 
										'products_id, filter_id',
										$Artikel_ID.','.$FilterWertID[$filteranzahl] );										
				}				
			}
			             
			fwrite($dateihandle, "* \n ");
			
?>
	