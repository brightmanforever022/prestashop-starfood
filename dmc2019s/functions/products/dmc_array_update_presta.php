<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_array_update_presta.php												*
*  inkludiert von dmc_write_art.php 										*	
*  Presta Artikel Array mit Werten fuellen									*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
12.03.2012
- neu
*/
			//	fwrite($dateihandle, "Artikel-UPDATE presta \n");
				if ($Artikel_VPE_Value==0) $Artikel_VPE_Value=1; // Einheit anzeigen
	
				$sql_data_array = array(
				//	'id_supplier' => $Hersteller_ID,
				//	'id_manufacturer' => $Hersteller_ID,
					// 'id_tax' => $Artikel_Steuersatz,  	WOHL AB V1.3
					'supplier_reference' => $Artikel_Herstellernummer,
					
			//		'id_tax_rules_group' => $Artikel_Steuersatz,  //WOHL AB 1.4
					'ean13' => $Artikel_EAN,	// $Artikel_EAN,
					'quantity' => $Artikel_Menge,
					'price' => $Artikel_Preis,
					'wholesale_price' => $Artikel_Preis4,
					'unity' => $Artikel_VPE,
					'unit_price_ratio' => $Artikel_VPE_Value,					// 0 fuer Einheit NICHT anzeigen, sonst 1 fuer Einheit anzeigen oder 
					'weight' => $Artikel_Gewicht,
					'out_of_stock' => $Artikel_Status,	// lieferbar = 2
					'active' => $Aktiv,
					'date_upd' => 'now()'			 
				);
	
?>
	