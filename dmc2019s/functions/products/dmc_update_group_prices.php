<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_update_group_prices.php												*
*  inkludiert von dmc_write_art.php 										*	
*  Kundengruppenpreise aktualisieren										*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
13.03.2012
- neu
*/

			// Veyton // Abweichende Preise anzeigen Veyton
			//	  price_flag_graduated_2	 price_flag_graduated_3	
			//	if (strpos(strtolower(SHOPSYSTEM), 'veyton') === false && ($Artikel_Preis)) {
			//		$sql_data_array['price_flag_graduated_1'] = 1;
			//	} // end if

			// Tabellen für Preis
			// Gastpreis -> personal_offers_by_customers_status_1
			// Neuer Kunde -> personal_offers_by_customers_status_2
			// Händler -> personal_offers_by_customers_status_3
			// Händler EU -> personal_offers_by_customers_status_4
			// Uberpruefen auf 10 Preisgruppen
			$anzahl_preisgruppen=3;
			for($Preisgruppe = 1; $Preisgruppe <= $anzahl_preisgruppen; $Preisgruppe++) 
			{   
				//if (DEBUGGER>=1) fwrite($dateihandle, "\ndmc_update_group_prices Preis Nr.$Preisgruppe ... ");
				// Zu setzenden Preis 1-4 ermitteln
				//if (defined('TABLE_PRICE' . $Preisgruppe) && constant('TABLE_PRICE' . $Preisgruppe) != '') 
				if (constant('TABLE_PRICE' . $Preisgruppe) != '') 
				{
					if (DEBUGGER>=1) fwrite($dateihandle, "Tabelle=".constant('TABLE_PRICE' . $Preisgruppe) );
					$pricenumber = constant('GROUP_PRICE' . $Preisgruppe);
					$products_price=${"Artikel_Preis$pricenumber"};
				//	fwrite($dateihandle, "1445 Artikel Preis Nr.$Preisgruppe aktualisieren: $products_price Euro \n");
					if (strpos(strtolower(SHOPSYSTEM), 'veyton') === false) {
						if ( $products_price >0.01 && constant('TABLE_PRICE' . $Preisgruppe)!='') 
						{  	
						//	if (DEBUGGER>=1) fwrite($dateihandle, "dmc_update_group_prices Preis Nr.$Preisgruppe aktualisieren: $products_price Euro \n");
							// Zu setzenden Preis 1-4 ermitteln
							$pricenumber = constant('GROUP_PRICE' . $Preisgruppe);
							$products_price=${"Artikel_Preis$pricenumber"};
							
							// Für (zusätzliche) Preise  (Artikel_Preis1-4)
							// price_id   	  products_id   	  quantity   	  personal_offer
							// quantity   =1 , da keine Staffelpreise	 
							
							
								$sql_data_price_array = array(
									// 'quantity' => 1,
									'personal_offer' => $products_price);
							
								// Wenn Preis-Zuordnung existiert (Fehler von XTC beim Artikel loeschen) -> update
								if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false) 
									if (STORE_ID != "")
										$sql_data_price_array['store_id'] = STORE_ID;
									else 
										$sql_data_price_array['store_id'] = 1;
								// Update Preistabelle
								dmc_sql_update_array(constant('TABLE_PRICE' . $Preisgruppe), $sql_data_price_array, "products_id = '$Artikel_ID' and quantity='1'"); 
						}
					} else { // ABWEICHEND NUR FUER VEYTON (NUR wenn Preis abweichend von Standardpreis
						if ( $products_price >0.01) {
							if (DEBUGGER>=1) fwrite($dateihandle, "veyton dmc_update_group_prices Preis Nr.$Preisgruppe aktualisieren: $products_price Euro Tabelle=".TABLE_PRODUCTS_PRICE_GROUP . $Preisgruppe ." \n");
							$sql_data_price_array = array(
								//'discount_quantity' => 1,
								'price' => $products_price
							);
							// GGfls setzten, falls noch nicht gesetzt, sonst Update
							$cmd = "select id AS price_id FROM " . TABLE_PRODUCTS_PRICE_GROUP . $Preisgruppe .
									" WHERE products_id = $Artikel_ID and discount_quantity='1'";
							$sql_query = dmc_db_query($cmd);
							if ($sql_result = dmc_db_fetch_array($sql_query)) {
								$temp_id=$sql_result['price_id'];
								dmc_sql_update_array(TABLE_PRODUCTS_PRICE_GROUP . $Preisgruppe, $sql_data_price_array, "id = $temp_id and discount_quantity=1");
							} else {// nicht existent
								 dmc_sql_insert_array(TABLE_PRODUCTS_PRICE_GROUP . $Preisgruppe, $sql_data_price_array);
							}
						}
					} // end if ( ${"Artikel_Preis$Anzahl"} >0.01 && constant('TABLE_PRICE' . $Anzahl)!='')
				} // end if (defined('TABLE_PRICE' . $Preisgruppe)) 
			} // end for 
	if (DEBUGGER>=1) fwrite($dateihandle, "ENDE \n ");
?>