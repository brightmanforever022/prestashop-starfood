<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for veyton													*
*  dmc_array_update_veyton.php												*
*  inkludiert von dmc_write_art.php 										*	
*  Veyton Artikel Array mit Werten fuellen									*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*  24.7.2013 -	Erweitert um Attributs Mapping zur Aussonderung von 		*
*				Datenbankfeldern aus dmc_mappings							*
*****************************************************************************/
/*
13.03.2012
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
					'manufacturers_id' => $Hersteller_ID
				);
	
				$sql_data_array['last_modified'] = 'now()';
				
				// Standard oder Variantenartikel?
				if ($Artikel_Variante_Von <> "") {
					// Variantenartikel
					$sql_data_array['products_master_model'] =$Artikel_Variante_Von;	// Veyton FUER HAUPTARTIKEL = NULL
					$sql_data_array['products_master_flag'] = 0;	// Veyton FUER HAUPTARTIKEL = NULL
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
	