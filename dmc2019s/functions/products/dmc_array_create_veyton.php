<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for veyton													*
*  dmc_array_create_veyton.php												*
*  inkludiert von dmc_write_art.php 										*	
*  Veyton Artikel Array mit Werten fuellen									*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*  24.7.2013 -	Erweitert um Attributs Mapping zur Aussonderung von 		*
*				Datenbankfeldern aus dmc_mappings							*
*****************************************************************************/
/*
12.03.2012
- neu
*/
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
					// Veyton Details
					//'external_id' => null,				// Veyton
					//'permission_id' => null,				// Veyton
					'products_owner' => 1,					// Veyton
					'products_average_quantity' => 1, 		// Veyton
					//'products_option_template' => null, // Veyton Standard NULL
					//'products_option_list_template' => null, // Veyton Standard NULL
					'price_flag_graduated_all' => 1, // Veyton Standard 0, wenn rabattierbar = 1
			/*		'price_flag_graduated_1' => 1, 		// Veyton Standard 0, wenn rabattierbar = 1
					'price_flag_graduated_2' => 1,			// Veyton Standard 0, wenn rabattierbar = 1
					'price_flag_graduated_3' => 1,			// Veyton Standard 0, wenn rabattierbar = 1 */
					'products_sort' => $Sortierung,	// Veyton Standard 0, 
					//'date_available' => null, 			// Veyton Standard NULL 
					'product_list_template' => '', 			// Veyton Standard ''
					'products_ordered' => 0,					// Veyton Standard 0
					'products_transactions' => 0,				// Veyton Standard 0
					// 'products_startpage_sort' => 0,				// Veyton Standard 0
					'products_average_rating' => 0,				// Veyton Standard 0
					'products_rating_count' => 0,				// Veyton Standard 0
					'products_digital' => 0,				// Veyton Standard 0
					'flag_has_specials' => 0,				// Veyton Standard 0
					'products_serials' => 0,			// Veyton Standard 0
					'date_added' => $Aenderungsdatum,
					'date_available' => $Aenderungsdatum,
					'last_modified' => 'now()'
				);
				
				
				// Standard oder Variantenartikel?
				if ($Artikel_Variante_Von == "") {
					$sql_data_array['products_master_model'] =NULL;	// Veyton FUER HAUPTARTIKEL = NULL
					$sql_data_array['products_master_flag'] = NULL;	// Veyton FUER STANDARD-HAUPTARTIKEL = NULL
				} else {
					// Variantenartikel
					$sql_data_array['products_master_model'] =$Artikel_Variante_Von;	// Veyton FUER HAUPTARTIKEL = NULL
					$sql_data_array['products_master_flag'] =0;	// Veyton FUER HAUPTARTIKEL = NULL
					// Hauptartikel ggfls Master Flag zuordnen, wenn Hauptartikel existent
					if ($Artikel_Variante_Von_id != "") dmc_set_master_flag($Artikel_Variante_Von_id);
				} // end if variante
	
				// product description
				$sql_product_details_array['products_name'] = $Artikel_Bezeichnung;
				$sql_product_details_array['products_description'] = $Artikel_Langtext;
				$sql_product_details_array['products_short_description'] = $Artikel_Kurztext;
				$sql_product_details_array['products_keywords'] = $Artikel_MetaKeyword;
				
				// $sql_attribute_array aus dmc_mappings
				if(!empty($sql_attribute_array))
					$sql_data_array = array_merge($sql_data_array, $sql_attribute_array);
				
?>
	