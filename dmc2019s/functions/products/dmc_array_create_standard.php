<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_array_create_standard.php											*
*  inkludiert von dmc_write_art.php 										*	
*  Standard Artikel Array mit Werten fuellen								*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
12.03.2012
- neu
*/
				fwrite($dateihandle, " dmc_array_create_standard \n");
				// product essentials
				$sql_data_array = array(
					'products_id' => $Artikel_ID,
					'products_quantity' => $Artikel_Menge,
					'products_model' => $Artikel_Artikelnr,
					'products_price' => $Artikel_Preis,
					'products_weight' => $Artikel_Gewicht,
					'products_tax_class_id' => $Artikel_Steuersatz,
					'products_status' => $Artikel_Status,
					'manufacturers_id' => $Hersteller_ID,
					'products_date_added' => 'now()',
					'products_last_modified' => 'now()',
					
				);
			
				// Sortierungsfeld abweichend in ZENCART
				if (strpos(strtolower(SHOPSYSTEM), 'zencart') !== false) {
					// ZENCart
					$sql_data_array['products_sort_order'] =  $Sortierung;
					$sql_data_array['master_categories_id'] = $Kategorie_ID;
					
				} else if (strpos(strtolower(SHOPSYSTEM), 'osc') === false) {
					// Nur, wenn NICHT oscommerce
					fwrite($dateihandle, " 38 SHOPSYSTEM = ".SHOPSYSTEM."\n");
					$sql_data_array['products_sort'] =  $Sortierung;
				}
			
				// GGFLS für GX3 auskommentieren
				// $sql_product_details_array['checkout_information'] = "";
				
				// product description
				$sql_product_details_array['products_name'] = $Artikel_Bezeichnung;
				$sql_product_details_array['products_description'] = $Artikel_Langtext;
				// Keine Details etc in ZENCART
				if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false && strpos(strtolower(SHOPSYSTEM), 'osc') === false) {
					$sql_product_details_array['products_short_description'] = $Artikel_Kurztext;
					$sql_product_details_array['products_meta_title'] = $Artikel_MetaText;
					$sql_product_details_array['products_meta_description'] = $Artikel_MetaDescription;
					$sql_product_details_array['products_meta_keywords'] =  $Artikel_MetaKeyword;
				}

				if (strpos(strtolower(SHOPSYSTEM), 'commerceseo') !== false) {
				for($gruppe = 0; $gruppe <= 10; $gruppe++) {     //  durchlaufen	     
							if (defined(constant('GROUP_PERMISSION_' . $gruppe)))
							if (constant('GROUP_PERMISSION_' . $gruppe)!=''){  	
								 $sql_data_array[constant('GROUP_PERMISSION_' . $gruppe)] = constant('GROUP_PERMISSION_' . $gruppe);	
							} // end if
					} // END FOR	
				}
				
				// $sql_attribute_array aus dmc_mappings
				if(!empty($sql_attribute_array))
					$sql_data_array = array_merge($sql_data_array, $sql_attribute_array);
				
?>
	