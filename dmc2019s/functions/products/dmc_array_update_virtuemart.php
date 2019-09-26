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

 	 	 	 	 	 	 	 	 	 			
				// product essentials
				$sql_data_array = array(
					//'virtuemart_product_id' => $Artikel_ID,
					'virtuemart_vendor_id' => 1,	// Lieferant / NICHT Hersteller!!!
					// 'product_parent_id' => 0,
					// 'product_sku' => $Artikel_Artikelnr,
					'product_weight' => $Artikel_Gewicht,
					// product_weight_uom 	product_length 	product_width 	product_height 	product_lwh_uom 	product_url
					'product_in_stock' => $Artikel_Menge,
					// product_ordered 	low_stock_notification 	product_available_date 	product_availability 	product_special
					// product_sales 	product_unit 	product_packaging 	product_params 	hits 	intnotes 	metarobot 	metaauthor 	layout
					'published' => $Artikel_Status,
					//'created_on' => 'now()',
					'modified_on' => 'now()'
					// created_by 	modified_by 	locked_on 	locked_by					
				);
				
				$slug=dmc_prepare_seo_name($Artikel_Bezeichnung,'de').'-'.$Artikel_ID;
				fwrite($dateihandle, "sql_product_details_array.35: zu slug=".$slug);
				// product description
				$sql_product_details_array = array(
					//'virtuemart_product_id' => $Artikel_ID,
					'product_s_desc' => $Artikel_Kurztext,
					'product_desc' => $Artikel_Langtext,
					'product_name' => $Artikel_Bezeichnung,
					//'metadesc' => $Artikel_MetaDescription,
					//'metakey' => $Artikel_MetaKeyword,
					//customtitle
					'slug' => $slug									
				);
				
				
				
?>
	