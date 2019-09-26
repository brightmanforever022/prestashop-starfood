<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_set_group_prices.php													*
*  inkludiert von dmc_write_art.php 										*	
*  Kundengruppenpreise setzen												*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
12.03.2012
- neu
*/
			if ($quantity=="") $quantity=1;
			if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_group_prices fuer Anzahl $quantity \n");
			
			// Tabellen für Preis
			// Gastpreis -> personal_offers_by_customers_status_1
			// Neuer Kunde -> personal_offers_by_customers_status_2
			// Händler -> personal_offers_by_customers_status_3
			// Händler EU -> personal_offers_by_customers_status_4
			// Uberpruefen auf 10 Preisgruppen
			for($Preisgruppe = 1; $Preisgruppe <= 15; $Preisgruppe++) 
			{   
				// Zu setzenden Preis 1-15 ermitteln
				if (defined('TABLE_PRICE' . $Preisgruppe) && constant('TABLE_PRICE' . $Preisgruppe) != '') 
				{
					$pricenumber = constant('GROUP_PRICE' . $Preisgruppe);
					$products_price=${"Artikel_Preis$pricenumber"};
					// fwrite($dateihandle, "31 Artikel Preis Nr. $pricenumber (GROUP_PRICE$Preisgruppe) setzen: $products_price Euro in Tabelle ".constant('TABLE_PRICE' . $Preisgruppe)." \n");
					if ( $products_price >0.01 && constant('TABLE_PRICE' . $Preisgruppe)!='') 
					{  	
						//if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_group_prices Artikel Preis Nr.$Preisgruppe setzen: $products_price Euro \n");
						//if (DEBUGGER>=50 && defined('TABLE_PRICE' . $Preisgruppe)) 
						//	fwrite($dateihandle, "dmc_set_group_prices TABLE_PRICE$Preisgruppe ist definiert:".constant('TABLE_PRICE' . $Preisgruppe)." \n");
						// Anzahl Einträge in Tabelle ermitteln
						if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
							$temp_id_query = dmc_db_query("SELECT max(id) as total from " . constant('TABLE_PRICE' . $Preisgruppe) ." ");
						} else {
							// Standard 
							$temp_id_query = dmc_db_query("SELECT max(price_id) as total from " . constant('TABLE_PRICE' . $Preisgruppe) ." ");
						}
					
						// fwrite($dateihandle, "dmc_set_group_prices 40\n");
						$TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						
						if ($TEMP_ID['total']=='' || $TEMP_ID['total']==null) {
							$NEUE_ID = 1;
						} else {
							$NEUE_ID = $TEMP_ID['total'] + 1;
						}
						// fwrite($dateihandle, "dmc_set_group_prices 47\n");		
						// Zu setzenden Preis 1-15 ermitteln
						$pricenumber = constant('GROUP_PRICE' . $Preisgruppe);
						$products_price=${"Artikel_Preis$pricenumber"};
						//		 fwrite($dateihandle, "dmc_set_group_prices 51 fuer $Artikel_ID\n");
						// Für (zusätzliche) Preise  (Artikel_Preis1-4)
						// price_id   	  products_id   	  quantity   	  personal_offer
						// quantity   =1 , da keine Staffelpreise	 
						if (strpos(strtolower(SHOPSYSTEM), 'veyton') === false) {
							// fwrite($dateihandle, "dmc_set_group_prices 56\n");	
							$sql_data_price_array = array(
								'price_id' => $NEUE_ID,
								'products_id' => $Artikel_ID,
								'quantity' => $quantity,
								'personal_offer' => $products_price);
						
							// Wenn Preis-Zuordnung existiert (Fehler von XTC beim Artikel loeschen) -> update
							if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false) 
								if (STORE_ID != "")
									$sql_data_price_array['store_id'] = STORE_ID;
								else 
									$sql_data_price_array['store_id'] = 1;
								
							// Bug Behebung xtCommerce, Kundenpreistabellen werden beim löschen des Artikels nicht gelöscht.
							$cmd = "select price_id from " . constant('TABLE_PRICE' . $Preisgruppe) .
									" where products_id = '$Artikel_ID' and quantity=$quantity";
										
							$sql_query = dmc_db_query($cmd);
							fwrite($dateihandle, "dmc_set_group_prices 75 $cmd \n");			
							if ($sql_result = dmc_db_fetch_array($sql_query)) {
								$temp_id=$sql_result['price_id'];
								dmc_sql_update_array(constant('TABLE_PRICE' . $Preisgruppe), $sql_data_price_array, "price_id = '$temp_id' and quantity=$quantity");
							} else { // nicht existent
								dmc_sql_insert_array(constant('TABLE_PRICE' . $Preisgruppe), $sql_data_price_array);
							}
						} else { // ABWEICHEND  NUR FUER VEYTON (NUR wenn Preis abweichend von Standardpreis
													 fwrite($dateihandle, "dmc_set_group_prices veyton \n");
							$cmd = "SELECT id AS price_id from " . TABLE_PRODUCTS_PRICE_GROUP . $Preisgruppe  .
									" WHERE products_id = $Artikel_ID and discount_quantity=$quantity";
							$sql_query = dmc_db_query($cmd);
							fwrite($dateihandle, "dmc_set_group_prices veyton \n");
							if ($sql_result = dmc_db_fetch_array($sql_query)) {
								if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_group_prices (update) -  ArtId ".$Artikel_ID."\n");
								$temp_id=$sql_result['price_id'];
								$sql_data_price_array = array(
									'products_id' => $Artikel_ID,
									'price' => $products_price
									);
								dmc_sql_update_array(TABLE_PRODUCTS_PRICE_GROUP . $Preisgruppe, $sql_data_price_array, "id = $temp_id and discount_quantity=$quantity");								
							} else {// nicht existent
								if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_group_prices (insert) -  ArtId ".$Artikel_ID."\n");
								$sql_data_price_array = array(
								//	'price_id' => $NEUE_ID,
									'products_id' => $Artikel_ID,
									'discount_quantity' => $quantity,
									'price' => $products_price
									);
								dmc_sql_insert_array(TABLE_PRODUCTS_PRICE_GROUP . $Preisgruppe, $sql_data_price_array);
							}
						} // end if Preis setzen
					} // end if ( ${"Artikel_Preis$Anzahl"} >0.01 && constant('TABLE_PRICE' . $Anzahl)!='')
				} // end if (defined('TABLE_PRICE' . $Preisgruppe)) 
			} // end for 
	
?>
	