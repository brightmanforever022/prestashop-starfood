<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_array_create_presta.php												*
*  inkludiert von dmc_write_art.php 										*	
*  Presta Artikel Array mit Werten fuellen									*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
12.03.2012
- neu
*/
	
				$sql_data_array = array(
					'id_product' => $Artikel_ID,
					'id_supplier' => $Hersteller_ID,
					'id_manufacturer' => $Hersteller_ID,
					// 'id_tax' => $Artikel_Steuersatz,  	WOHL AB V1.3
					'id_tax_rules_group' => $Artikel_Steuersatz,  //WOHL AB 1.4
			//		'id_category_default' => $Kategorie_ID,	// !!! $Kategorie_ID wird erst spaeter geprueft
					'on_sale' => 0,
					'ean13' => $Artikel_EAN,	// $Artikel_EAN,
					'ecotax' => 0,
					'quantity' => $Artikel_Menge,
					'price' => $Artikel_Preis,
					'wholesale_price' => $Artikel_Preis4,
					'unity' => $Artikel_VPE,
					'unit_price_ratio' => $Artikel_VPE_Value,					// 0 fuer Einheit NICHT anzeigen, sonst 1 fuer Einheit anzeigen oder 
					// V1.3 'reduction_price' => 0,
					// V1.3 'reduction_percent' => 0,
					// V1.3 'reduction_from' => 'now()',
					// V1.3 'reduction_to' => 'now()',
					// V1.3  'reduction_percent' => 0,
					'reference' => $Artikel_Artikelnr,
					'supplier_reference' => $Artikel_Herstellernummer,
					
					'redirect_type' => '404',
					'upc' => '',
					'cache_default_attribute' => 0,
					'location' => '',
					'weight' => $Artikel_Gewicht,
					'out_of_stock' => $Artikel_Status,	// lieferbar = 2
					'quantity_discount' => 0,
					'customizable' => 0,
					'uploadable_files' => 0,
					'text_fields' => 0,
					'active' => $Aktiv,
					'indexed' => 0,
					'date_add' => 'now()',
					'date_upd' => 'now()'
				);
	
?>
	