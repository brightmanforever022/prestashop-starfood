<?php
/****************************************************************************
*                                                                        	*
*  dmConnector für virtuemart												*
*  dmc_array_create_virtuemart.php											*
*  inkludiert von dmc_write_art.php 										*	
*  Standard Artikel Array mit Werten fuellen								*
*  Copyright (C) 2013 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
12.02.2013
- neu
*/

 	 	 	 	 // Neue Artikel ID generieren
				// $Artikel_ID = dmc_get_highest_id('virtuemart_product_id', TABLE_PRODUCTS)+1;
				// product essentials
				$sql_data_array = array(
					'virtuemart_product_id' => $Artikel_ID,
					'virtuemart_vendor_id' => 1,	// Lieferant / NICHT Hersteller!!!
					//'product_parent_id' => 0,
					'product_sku' => $Artikel_Artikelnr,
					'product_weight' => $Artikel_Gewicht,
					// product_weight_uom 	product_length 	product_width 	product_height 	product_lwh_uom 	product_url
					'product_in_stock' => $Artikel_Menge,
					// product_ordered 	low_stock_notification 	product_available_date 	product_availability 	product_special
					// product_sales 	product_unit 	product_packaging 	product_params 	hits 	intnotes 	metarobot 	metaauthor 	layout
					'published' => $Artikel_Status,
					'created_on' => 'now()',
					'modified_on' => 'now()'
					// created_by 	modified_by 	locked_on 	locked_by					
				);
				
					// Standard oder Variantenartikel?
				if ($Artikel_Variante_Von == "") {
					// fwrite($dateihandle, "165.37") ;
					$sql_data_array['product_parent_id'] =0;										// virtuemart FUER HAUPTARTIKEL = 0
				} else {
					// Variantenartikel
					$Vater_Artikel_ID=dmc_get_id_by_artno($Artikel_Variante_Von);
					if ($Vater_Artikel_ID=="") {
						$Vater_Artikel_ID=0;
						fwrite($dateihandle, "(P) in dmc_array_create_virtuemart: Vater Artikelnummer nícht gefunden: ".$Artikel_Variante_Von."\n");
					}
					$sql_data_array['product_parent_id'] = $Vater_Artikel_ID;						// virtuemart FUER HAUPTARTIKEL = 0
					//fwrite($dateihandle, "165.47:".$sql_data_array['product_parent_id']);
				} // end if variante
				
				$slug=dmc_prepare_seo_name($Artikel_Bezeichnung,'de').'-'.$Artikel_ID;
				fwrite($dateihandle, "165.51: zu slug=".$slug);
				// product description
				$sql_product_details_array = array(
					'virtuemart_product_id' => $Artikel_ID,
					'product_s_desc' => $Artikel_Kurztext,
					'product_desc' => $Artikel_Langtext,
					'product_name' => $Artikel_Bezeichnung,
					'metadesc' => $Artikel_MetaDescription,
					'metakey' => $Artikel_MetaKeyword,
					//customtitle
					'slug' => $slug									
				);
				
				
?>
	