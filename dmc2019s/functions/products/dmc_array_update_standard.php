<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_array_update_standard.php											*
*  inkludiert von dmc_write_art.php 										*	
*  Standard Artikel Array mit Werten fuellen								*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
12.03.2012
- neu
*/
				// product essentials
				$sql_data_array = array(
					// 'products_id' => $Artikel_ID,
					'products_quantity' => $Artikel_Menge,
					//  'products_model' => $Artikel_Artikelnr,
					'products_price' => $Artikel_Preis,
					'products_weight' => $Artikel_Gewicht,
					'products_tax_class_id' => $Artikel_Steuersatz,
					'products_status' => $Artikel_Status,
					'manufacturers_id' => $Hersteller_ID,
					'products_last_modified' => 'now()',
				);
				
				if (strpos(strtolower(SHOPSYSTEM), 'osc') === false) {
					// Nur, wenn NICHT oscommerce
					//$sql_data_array['products_sort'] =  $Sortierung;
				}
				
				// Wenn $Artikel_Neu, dann als neuen Artikel anzeigen // fuer Update als   `products` . `products_date_added`
				if ($Artikel_Neu==1) 
					$sql_data_array['products_date_added'] = 'now()';
				
				// product description
				$sql_product_details_array['products_name'] = $Artikel_Bezeichnung;
				// Update mit Besonderheit der Berücksitigung von GAMBIO TABs Modul
				// Step 1: Bisherige products_description ermitteln
				$products_description_tabs=utf8_encode(dmc_sql_select_query('products_description','products_description',"products_id=".$Artikel_ID." AND language_id=2"));
				// Step 2: Bereich ab dem ersten Vorkommnis [TAB ausschneiden
				$pos = strpos($products_description_tabs,'[TAB');
				if ($pos !== false) {
					// Step 3: products_description = Neuer WaWi Text + Bereich ab dem ersten Vorkommnis [TAB ausschneiden
					$Artikel_Langtext = $Artikel_Langtext.' '.substr($products_description_tabs,$pos,10000);  
				}
				$sql_product_details_array['products_description'] = $Artikel_Langtext;
				// Keine Details etc in ZENCART
				if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false) {
					$sql_product_details_array['products_short_description'] = $Artikel_Kurztext;
					$sql_product_details_array['products_meta_title'] = $Artikel_MetaText;
					$sql_product_details_array['products_meta_description'] = $Artikel_MetaDescription;
					$sql_product_details_array['products_meta_keywords'] =  $Artikel_MetaKeyword;
				}
				
				if (strpos(strtolower(SHOPSYSTEM), 'commerceseo') !== false) {
					for($gruppe = 0; $gruppe <= 10; $gruppe++) {     //  durchlaufen	     
							if (defined(constant('GROUP_PERMISSION_' . $gruppe)))
							if (constant('GROUP_PERMISSION_' . $gruppe)!=''){  	
								 $insert_sql_data[constant('GROUP_PERMISSION_' . $gruppe)] = constant('GROUP_PERMISSION_' . $gruppe);	
							} // end if
					} // END FOR	
				}
				
				// $sql_attribute_array aus dmc_mappings
				if(!empty($sql_attribute_array))
					$sql_data_array = array_merge($sql_data_array, $sql_attribute_array);
					
?>
	