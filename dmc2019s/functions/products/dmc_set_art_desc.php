<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_set_art_desc.php														*
*  inkludiert von dmc_write_art.php 										*	
*  Artikel Beschreibung anlegen												*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
13.03.2012
- neu
*/
			// Array ist gefuellt durch dmc_array_create_standard
			// BUGFiX - Bestehende Daten laden (BUGFiX, falls schon existent)
			if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
				$cmd = 	"select products_id from " . TABLE_PRODUCTS_DESCRIPTION .
						" where products_id='$Artikel_ID' and language_code='".$Artikel_Sprache . "'";
				
			} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
				$cmd = "SELECT virtuemart_product_id AS products_id FROM " . TABLE_PRODUCTS_DESCRIPTION .
						" where virtuemart_product_id='$Artikel_ID' ";
			} else {
				$cmd = "select products_id from " . TABLE_PRODUCTS_DESCRIPTION .
						" where products_id='$Artikel_ID' and language_id='". $Artikel_Sprache . "'";
			}
			$desc_query = dmc_db_query($cmd);
	        if ($desc = dmc_db_fetch_array($desc_query))
	        { //  Beschreibung update
				if (DEBUGGER>=1) fwrite($dateihandle, "ACHTUNG: dmc_set_art_desc - Beschreibung UPDATE!!! bei NEUEM Artikel.\n");
					 // nur Standardprache
					if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
						dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_product_details_array,  "products_id ='$Artikel_ID' and language_id = '" . $Artikel_Sprache . "'");
					} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
						dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_product_details_array,  "virtuemart_product_id ='$Artikel_ID' ");
					} else {
						dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_product_details_array,  "products_id ='$Artikel_ID' and language_code = '" . $Artikel_Sprache . "'");
					}
				
	        } else
			{
				// Bescheibung insert
				if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_art_desc -  Beschreibung INSERT with language ".$Artikel_Sprache."\n");
				
				if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
					$sql_product_details_array['products_id'] = $Artikel_ID;
					$sql_product_details_array['language_code'] = $Artikel_Sprache;
				} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
					// Kein Language Code erforderlich/vorhanden, $ ArtikelID bereits zugeordnet
					fwrite($dateihandle, "vmart**");
					$no_of_languages=0;
				} else {
					$sql_product_details_array['products_id'] = $Artikel_ID;
					$sql_product_details_array['language_id'] = $Artikel_Sprache;
				}
				// do insert
				dmc_sql_insert_array(TABLE_PRODUCTS_DESCRIPTION, $sql_product_details_array); 
				$no_of_languages=0;
				// Wenn Fremdsprachen vorhanden, "leere" Eintraege vorbereiten
				if ($no_of_languages>1) {
					// Englisch
					if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_art_desc - Beschreibung insert Sprache Englisch als Zusatzsprache\n");    
					$sql_product_details_array = array(
						'products_name' => '',
						'products_description' => ''
					);			
					// Keine Details etc in ZENCART und todo veyton
					if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false && strpos(strtolower(SHOPSYSTEM), 'veyton') === false) {
						$sql_product_details_array['products_short_description'] = '';
						$sql_product_details_array['products_meta_title'] = '';
						$sql_product_details_array['products_meta_description'] = '';
						$sql_product_details_array['products_meta_keywords'] =  '';
					}
					if (strpos(strtolower(SHOPSYSTEM), 'veyton') === false) {
						$sql_product_details_array['language_id'] = '1';
					} else {
						$sql_product_details_array['language_code'] = 'en';
					}
					if (strpos(strtolower(SHOPSYSTEM), 'gambiogx') !== false) {
						$sql_product_details_array['checkout_information'] = '';
					}
					
					dmc_sql_insert_array(TABLE_PRODUCTS_DESCRIPTION, $sql_product_details_array); 
				}
			}
			
			// Neu 21072015 rcm - Unterstuetzung Gambio Tabelle products_item_codes für MPN Herstellernummer ISBN Marke etc
			if (strpos(strtolower(SHOPSYSTEM), 'gambiogx') !== false) {
				if ($Artikel_google_export_condition=='') $Artikel_google_export_condition = 'neu';
				$sql_product_details_array = array(  
						'products_id' => $Artikel_ID,
						'code_mpn' => $Artikel_code_mpn,
						'code_isbn' => $Artikel_code_isbn,
						'code_upc' => $Artikel_code_upc,
						'code_jan' => $Artikel_code_jan,
						'google_export_condition' => $Artikel_google_export_condition,
						'brand_name' => $Artikel_brand_name,
						'identifier_exists' => 1,
						'gender' => '',
						'age_group' => '',
						'expiration_date' => '0000-00-00'
					);
				dmc_sql_insert_array('products_item_codes', $sql_product_details_array); 						
			}
			
?>
	