<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_update_art_desc.php													*
*  inkludiert von dmc_write_art.php 										*	
*  Artikel Beschreibung aktualisieren										*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
13.03.2012
- neu
25.9.2012
- Bug Fix Insert, wenn noch nicht gefuellt
*/

			// Array ist gefuellt durch dmc_array_create_standard
			// BUGFiX - Bestehende Daten laden (BUGFiX, falls schon existent)
			if (UPDATE_DESC == "true") {
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
				if ($desc = dmc_db_fetch_array($desc_query)) { //  Beschreibung update
					// Array ist gefuellt durch dmc_array_create_standard
					if (DEBUGGER>=1) fwrite($dateihandle, "dmc_update_art_desc - Beschreibung update products_id ='$Artikel_ID'  Sprache - ".$Artikel_Sprache."\n");
					// nur Standardprache
					if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
						dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_product_details_array,  "products_id ='$Artikel_ID' and language_code = '" . $Artikel_Sprache . "'");
					} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
						if (DEBUGGER>=1) fwrite($dateihandle, "dmc_update_art_desc - ".TABLE_PRODUCTS_DESCRIPTION." virtuemart_product_id= $Artikel_ID");
						dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_product_details_array,  "virtuemart_product_id ='$Artikel_ID' ");	
						if (DEBUGGER>=1) fwrite($dateihandle, " ... done");
					} else {
						dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_product_details_array,  "products_id ='$Artikel_ID' and language_id = '" . $Artikel_Sprache . "'");
					}
				} else {
					// ACHTUNG: Bescheibung insert bei bereits bestehenden Artikeln -> Fehlerabfangroutine
					if (DEBUGGER>=1) fwrite($dateihandle, "dmc_update_art_desc (BUGFIX UPDATE!!! product) -  Beschreibung INSERT with language ".$Artikel_Sprache."\n");
					if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
						$sql_product_details_array['language_code'] = $Artikel_Sprache;
						$sql_product_details_array['products_id'] = $Artikel_ID;
					} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
						// Kein Language Code erforderlich/vorhanden					
						$no_of_languages=0;
						$sql_product_details_array['virtuemart_product_id'] = $Artikel_ID;
						$sql_product_details_array['slug'] = dmc_prepare_seo_name($Artikel_Bezeichnung,'de').'-'. $Artikel_ID;	
					} else {
						$sql_product_details_array['products_id'] = $Artikel_ID;
						$sql_product_details_array['language_id'] = $Artikel_Sprache;
					}
					// do insert
					dmc_sql_insert_array(TABLE_PRODUCTS_DESCRIPTION, $sql_product_details_array); 
				}
			}
			 
			// Neu 21072015 rcm - Unterstuetzung Gambio Tabelle products_item_codes für MPN Herstellernummer ISBN Marke etc
			if (strpos(strtolower(SHOPSYSTEM), 'gambiogx') !== false) {
				// BUGFiX - Bestehende Daten laden (BUGFiX, falls NICHT NICHT existent)
				$cmd = "SELECT products_id FROM products_item_codes" .
							" WHERE products_id='$Artikel_ID'";
				$desc_query = dmc_db_query($cmd);
				if ($desc = dmc_db_fetch_array($desc_query)) { //  Werte update
					if (DEBUGGER>=1) fwrite($dateihandle, "dmc_update_art_desc - products_item_codes update products_id ='$Artikel_ID' \n");
					if ($Artikel_google_export_condition=='') $Artikel_google_export_condition = 'neu';
					if ($Artikel_code_mpn != '') $sql_product_products_item_codes_array['code_mpn'] = $Artikel_code_mpn;
					if ($Artikel_code_isbn != '') $sql_product_products_item_codes_array['code_isbn'] = $Artikel_code_isbn;
					if ($Artikel_code_upc != '') $sql_product_products_item_codes_array['code_upc'] = $Artikel_code_upc;
					if ($Artikel_code_jan != '') $sql_product_products_item_codes_array['code_jan'] = $Artikel_code_jan;
					if ($Artikel_google_export_condition != '') $sql_product_products_item_codes_array['google_export_condition'] = $Artikel_google_export_condition;
					if ($Artikel_brand_name != '') $sql_product_products_item_codes_array['brand_name'] = $Artikel_brand_name;
				
					dmc_sql_update_array('products_item_codes', $sql_product_products_item_codes_array, "products_id ='$Artikel_ID'"); 						
				} else {
					if (DEBUGGER>=1) fwrite($dateihandle, "dmc_update_art_desc - products_item_codes insert products_id ='$Artikel_ID' \n");
					if ($Artikel_google_export_condition=='') $Artikel_google_export_condition = 'neu';
					$sql_product_products_item_codes_array = array(  
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
					dmc_sql_insert_array('products_item_codes', $sql_product_products_item_codes_array); 	
				}
									
			}
?>