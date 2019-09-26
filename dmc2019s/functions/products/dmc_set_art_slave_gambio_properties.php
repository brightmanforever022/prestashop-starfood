<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_set_art_slave_gambio_properties.php									*
*  inkludiert von dmc_write_art.php 										*	
*  Artikel Variante für Shop anlegen -> standard							*
*  Copyright (C) 2013 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
18.07.2013
- neu
*/
				if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_art_slave_gambio_properties - produkt variante anlegen  \n");
		
				// Übergebene Merkmale und Ausprägungen ermitteln - werden als attribue1@attribe2@... übergeben
				$Merkmale = explode ( '@', $Artikel_Merkmal);
				$Auspraegungen = explode ( '@', $Artikel_Auspraegung);
				// Ggfls entfernen von wertlosen Attributen
				for ( $Anz_Merkmale = 0; $Anz_Merkmale < count ( $Merkmale ); $Anz_Merkmale++ )
				{
					if ($Auspraegungen[$Anz_Merkmale]=='') {
						// elemente loeschen
						unset($Merkmale[$Anz_Merkmale]);
						unset($Auspraegungen[$Anz_Merkmale]);
						// indexe neu aufbauen
						$Merkmale = array_values($Merkmale);
						$Auspraegungen = array_values($Auspraegungen);
					}
				}	
				  
				// AusprägungsIDs aus Datenbank ermitteln
				for ( $Anz_Merkmale = 0; $Anz_Merkmale < count ( $Merkmale ); $Anz_Merkmale++ )
				{
					// Nur, wenn Merkmal übergeben wurde und Auspraegung gefuellt
					if ($Merkmale[$Anz_Merkmale] <> "" && $Auspraegungen[$Anz_Merkmale]<>"") {
					   if (DEBUGGER>=1) fwrite($dateihandle, "*** VARIANTENARTIKEL (Variante von ID=$Artikel_Variante_Von_id) mit Merkmal=".$Merkmale[$Anz_Merkmale]." und Auspraegung=".$Auspraegungen[$Anz_Merkmale]."\n");
						// Überprüfen, ob Property  (z.B. Grösse) bereits angelegt.
						// Ermitteln der ID der Property (wenn vorhanden)
						$properties_id= dmc_sql_select_query('properties_id','properties_description',"properties_admin_name='".$Merkmale[$Anz_Merkmale]."'"); 
						if ($properties_id=="") {
							// Property anlegen, wenn nicht existiert,
							$properties_id = dmc_get_highest_id("properties_id",'properties')   + 1; 
							
							$sql_data_array = array(
										'properties_id' => $properties_id,
										'sort_order' => '1'										
									);
							$sql_data_array['sort_order']=1;											
				if (DEBUGGER>=1) fwrite($dateihandle, "properties mit werten ".$sql_data_array['properties_id']." und ".$sql_data_array['sort_order']."\n");
		 dmc_sql_insert_array('`properties`', $sql_data_array);
							// dmc_sql_insert_array('`properties`', $sql_data_array);
							for ( $rcm = 1; $rcm < 3; $rcm++ ) {
								$sql_data_array = array(  	//properties_description_id
															'properties_id' => $properties_id,
															'language_id' => $rcm,
															'properties_name' => $Merkmale[$Anz_Merkmale],
															'properties_admin_name' => $Merkmale[$Anz_Merkmale]
															);	    
								dmc_sql_insert_array('properties_description', $sql_data_array);
							}
						}
						 
						// Überprüfen, ob Auspraegung (z.B. XXL) bereits angelegt.		
						// Ermitteln der ID der Property (wenn vorhanden)
						$properties_values_id= dmc_sql_select_query('properties_values_id','properties_values',"properties_id=$properties_id AND value_model='".$Auspraegungen[$Anz_Merkmale]."'"); 
						if ($properties_values_id=="") {
							// Property anlegen, wenn nicht existiert,
							$properties_values_id = dmc_get_highest_id("properties_values_id","properties_values")  + 1;  
							
							$sql_data_array = array(  	'properties_values_id' => $properties_values_id,
														'properties_id' => $properties_id,
														'sort_order' => 1,
														'value_model' => $Auspraegungen[$Anz_Merkmale],
														'value_price_type' => '',
														'value_price' => 0
													);	    
							dmc_sql_insert_array('properties_values', $sql_data_array);
						 
							for ( $rcm = 1; $rcm < 3; $rcm++ ) {
								$sql_data_array = array(  	//properties_values_description_id
															'properties_values_id' => $properties_values_id,
															'language_id' => $rcm,
															'values_name' => $Auspraegungen[$Anz_Merkmale]
															// values_image
															);	    
								dmc_sql_insert_array('properties_values_description', $sql_data_array);
							}
						}
						
						// Produkt Merkmal und Auspraegung zuordnen fuer Admin, wenn noch nicht zugeordnet
						// Überprüfen, ob bereits angelegt.		
						$products_properties_admin_select_id= dmc_sql_select_query('products_properties_admin_select_id','products_properties_admin_select',"properties_id='".$properties_id."' AND properties_values_id='".$properties_values_id."'"); 
						if ($products_properties_admin_select_id=="") {
							//  anlegen, wenn nicht existiert,
							$products_properties_admin_select_id = dmc_get_highest_id("products_properties_admin_select_id","products_properties_admin_select")  + 1;  
							$sql_data_array = array(  	'products_properties_admin_select_id' => $products_properties_admin_select_id,
														'products_id' => $Artikel_Variante_Von_id,
														'properties_id' => $properties_id,
														'properties_values_id' => $properties_values_id
													);	    
							dmc_sql_insert_array('products_properties_admin_select', $sql_data_array);						 
						}
						
						// Preisberechnung = Variantenpreis - Standandproduktpreis
						$price_prefix = '';
						$weight_prefix = '';
						$options_values_price = 0;
						// NUR für erste Ausprägung eines Produktes gurchlaufen (sonst zuschlag wegen Grösse und Farbe)
						if ($Anz_Merkmale==0) {
							// Preisdifferenz ermitteln, wenn Preis fuer Variantenartikel uebergeben
							if ($Artikel_Preis>0.01) {
								$options_values_price = $Artikel_Preis-dmc_get_price($Artikel_Variante_Von_id);
								// Nachlass
								if ($options_values_price < -0.01) {
									$price_prefix = 'minus';
									$options_values_price = $options_values_price * -1;
								} else if ($options_values_price > 0.01) {
									$price_prefix = 'plus';
								} else {
									$options_values_price = 0.00;
									$price_prefix = 'plus';
								}// endif
							} else {
								$options_values_price = 0;
							}
							// Gewichtsdifferenz ermitteln , wenn Gewicht fuer Variantenartikel uebergeben
							if ($Artikel_Gewicht>0) {
								$options_values_weight = $Artikel_Gewicht-dmc_get_weight($Artikel_Variante_Von_id);
								// Nachlass
								if ($options_values_weight<0) {
									$weight_prefix = 'minus';
									$options_values_weight = $options_values_weight * -1;
								} // endif
							} else {
								$options_values_weight = 0;
							}
						} // endif
						// UEBERSCHREIBEN
						$options_values_weight = 0.00;
						
						// Kombination erstellen für Produkt (einmal, da zB Größe und Farbe ja auch nur 1 Kombination sind)
						if ($Anz_Merkmale==0) {
							// Überprüfen, ob bereits angelegt.		
							$sort_order=$Anz_Merkmale;
							$products_properties_combis_id= dmc_sql_select_query('products_properties_combis_id','products_properties_combis',"products_id='".$Artikel_Variante_Von_id."' AND combi_model=".$Artikel_Artikelnr); 
							if ($products_properties_combis_id=="") {
								//  anlegen, wenn nicht existiert,
								$products_properties_combis_id = dmc_get_highest_id("products_properties_combis_id","products_properties_combis")  + 1;  
							
								$werte = explode ( '@', dmc_prepare_vpe($Artikel_VPE));
								$products_vpe_id = $werte[0];
													
								$sql_data_array = array(  	'products_properties_combis_id' => $products_properties_combis_id,
															'products_id' => $Artikel_Variante_Von_id,
															'sort_order' => $sort_order,
															'combi_model' => $Artikel_Artikelnr,
															'combi_quantity_type' => '',
															'combi_quantity' => $Artikel_Menge,
															'combi_shipping_status_id' => 0,
															'combi_weight' => $options_values_weight,
															'combi_price_type' => $price_prefix,
															'combi_price' => $options_values_price,
															'combi_image' => $Artikel_Bilddatei,
															'products_vpe_id' => $products_vpe_id,
															'vpe_value' => 0.00
														);	    
								dmc_sql_insert_array('products_properties_combis', $sql_data_array);						 
							} else {
								// update
								dmc_sql_update('products_properties_combis', "combi_quantity= $Artikel_Menge, combi_weight =$options_values_weight,		combi_price_type= '$price_prefix', combi_price = $options_values_price, combi_image= '$Artikel_Bilddatei'", "products_properties_combis_id=$products_properties_combis_id");
							} // end if
						} // end if
						 	 	 
							
						// products_properties_combis_values - Überprüfen, ob bereits angelegt.		
						$products_properties_combis_values_id= dmc_sql_select_query('products_properties_combis_values_id','products_properties_combis_values',"products_properties_combis_id='".$products_properties_combis_id."' AND properties_values_id='".$properties_values_id."'"); 
						if ($products_properties_combis_values_id=="") {
							//  anlegen, wenn nicht existiert,
							$products_properties_combis_values_id = dmc_get_highest_id("products_properties_combis_values_id","products_properties_combis_values")  + 1;  
							$sql_data_array = array(  	'products_properties_combis_values_id' => $products_properties_combis_values_id,
														'products_properties_combis_id' => $products_properties_combis_id,
														'properties_values_id' => $properties_values_id
													);	    
							dmc_sql_insert_array('products_properties_combis_values', $sql_data_array);						 
						}
						
					} // endif  Nur, wenn Merkmal übergeben wurde
				} // End for $Anz_Merkmale
					
				// update auf den Hauptartikel bzgl des gesamtbestandes der zugehoerigen attribute
				// dmc_update_conf_stock($Artikel_Variante_Von_id);
				
?>
	