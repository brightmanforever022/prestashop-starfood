<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_set_art_slave_standard.php											*
*  inkludiert von dmc_write_art.php 										*	
*  Artikel Variante für Shop anlegen -> standard							*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
13.03.2012
- neu
*/
				if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_art_slave_standard - produkt variante anlegen  \n");
		
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
					   if (DEBUGGER>=1) fwrite($dateihandle, "*** VARIANTENARTIKEL (Variante von ID=$Artikel_Variante_Von_id) mit Merkmal=".$Merkmale[$Anz_Merkmale].
													" und Auspraegung=".$Auspraegungen[$Anz_Merkmale]."\n");
						// Überprüfen, ob Merkmal  (z.B. Grösse) bereits angelegt.		
						$temp_id_query = dmc_db_query("SELECT count(*) as total from products_options where products_options_name ='".$Merkmale[$Anz_Merkmale]."'");				
						$TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if (!$TEMP_ID['total']=='0') {
							// Merkmalid der Datenbank ermitteln
							$temp_id_query = dmc_db_query("SELECT products_options_id as total from products_options where products_options_name ='".$Merkmale[$Anz_Merkmale]."'");
							$TEMP_ID = dmc_db_fetch_array($temp_id_query);
							$Merkmalid = $TEMP_ID['total'];
						}
						else
						{
							// Merkmal anlegen, wenn nicht existiert,
							$temp_id_query = dmc_db_query("SELECT max(products_options_id) as total from products_options");				
							$TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
							// Wenn noch kein Eintrag, erster Eintrag, sonst erhoehen
							if ($TEMP_ID['total']=='' || $TEMP_ID['total']==null)
								$Merkmalid = 1;
							else 
								$Merkmalid = $TEMP_ID['total']+1;	    
							for ( $rcm = 1; $rcm < 3; $rcm++ ) {
								$sql_data_array = array(  	'products_options_id' => $Merkmalid,
															'language_id' => $rcm,
															'products_options_name' => $Merkmale[$Anz_Merkmale]);	    
								dmc_sql_insert_array('products_options', $sql_data_array);
							}
							$Merkmal_TB_ID = dmc_db_get_new_id();
						} // Ende Merkmal
						
						// Überprüfen, ob Auspraegung  (z.B. XXL) bereits angelegt.		
						$temp_id_query = dmc_db_query("SELECT count(*) as total from products_options_values where products_options_values_name ='".$Auspraegungen[$Anz_Merkmale]."'");
						$TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if (!$TEMP_ID['total']=='0') {
							// Auspraegungsid der Datenbank ermitteln
							$temp_id_query = dmc_db_query("SELECT products_options_values_id as total from products_options_values where products_options_values_name ='".$Auspraegungen[$Anz_Merkmale]."'");
							$TEMP_ID = dmc_db_fetch_array($temp_id_query);
							$Auspraegungsid = $TEMP_ID['total'];
						}
						else
						{
							// Auspraegung anlegen, wenn nicht existiert,
							$temp_id_query = dmc_db_query("SELECT max(products_options_values_id) as total from products_options_values");				
							$TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
							// Wenn noch kein Eintrag, erster Eintrag, sonst erhoehen
							if ($TEMP_ID['total']=='' || $TEMP_ID['total']==null)
								$Auspraegungsid = 1;
							else 
								$Auspraegungsid = $TEMP_ID['total']+1;	 
							for ( $rcm = 1; $rcm < 3; $rcm++ ) {
								$sql_data_array = array(  	'products_options_values_id' => $Auspraegungsid,
															'language_id' =>  $rcm,
															'products_options_values_name' => $Auspraegungen[$Anz_Merkmale]);	    
								dmc_sql_insert_array('products_options_values', $sql_data_array);
							}
							$Auspraegung_TB_ID = dmc_db_get_new_id();
						} // Ende Auspraegung
						
						//  Auspraegung dem Merkmal  zugeordnen
						// Überprüfen, ob bereits angelegt.
						// if (DEBUGGER>=1) fwrite($dateihandle, "SELECT count(*) as total from products_options_values_to_products_options where products_options_id ='".$Merkmalid."' and products_options_values_id ='".$Auspraegungsid."'\n");
						$temp_id_query = dmc_db_query("SELECT count(*) as total from products_options_values_to_products_options where products_options_id ='".$Merkmalid."' and products_options_values_id ='".$Auspraegungsid."'");
						$TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if (!$TEMP_ID['total']=='0')
							$existiert = 1;
						else
						{
							// Auspraegung anlegen, wenn nicht existiert,
							$temp_id_query = dmc_db_query("SELECT max(products_options_values_to_products_options_id) as total from products_options_values_to_products_options");
							$TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
							// Wenn noch kein Eintrag, erster Eintrag, sonst erhoehen
							if ($TEMP_ID['total']=='' || $TEMP_ID['total']==null)
								$Zuordnungsid = 1;
							else 
								$Zuordnungsid = $TEMP_ID['total']+1;	

							if (DEBUGGER>=1) fwrite($dateihandle, "** products_options_values_to_products_options_id=".$Zuordnungsid." und products_options_id=".$Merkmalid." und products_options_values_id=".$Auspraegungsid."\n");
								
							$sql_data_array = array(  	'products_options_values_to_products_options_id' => $Zuordnungsid,
														'products_options_id' => $Merkmalid,
														'products_options_values_id' => $Auspraegungsid);	    
							dmc_sql_insert_array('products_options_values_to_products_options', $sql_data_array);
							$Auspraegung_TB_ID = dmc_db_get_new_id();
						} // Ende Zuordnungen
						
						// Überprüfen, ob bereits angelegt.
						$temp_id_query = dmc_db_query("SELECT count(*) as total from products_attributes where ".
											"options_id ='".$Merkmalid."' and options_values_id ='".$Auspraegungsid."' and products_id='".$Artikel_Variante_Von_id."'");
					/*	if (DEBUGGER>=1) fwrite($dateihandle, " \n*** Step3a TEMP_ID['total']=".$TEMP_ID['total']." mit".
														"SELECT count(*) as total from products_attributes where ".
														"options_id ='".$Merkmalid."' and options_values_id ='".$Auspraegungsid."' and products_id='".$Artikel_Variante_Von_id."'");		*/		
						$TEMP_ID = dmc_db_fetch_array($temp_id_query);		
	
						$existiert = 0;
						if ($TEMP_ID['total']<>'0') { 
							$existiert = 1;
							 /* if (DEBUGGER>=1) fwrite($dateihandle, "\n*** Step4 TEMP_ID['total']=".$TEMP_ID['total']." mit".
														"SELECT count(*) as total from products_attributes where ".
														"options_id ='".$Merkmalid."' and options_values_id ='".$Auspraegungsid."' and products_id='".$Artikel_Variante_Von."'"); */
						} 
							/* if (DEBUGGER>=1) fwrite($dateihandle, "*** Variante existiert nicht - Step5 TEMP_ID['total']=".$TEMP_ID['total']." mit".
														"SELECT count(*) as total from products_attributes where ".
														"options_id ='".$Merkmalid."' and options_values_id ='".$Auspraegungsid."' and products_id='".$Artikel_Variante_Von."'"); */
							
							// Preisberechnung = Variantenpreis - Standandproduktpreis
							$price_prefix = '+';
							$weight_prefix = '+';
							$options_values_price = 0;
							// NUR für erste Ausprägung eines Produktes gurchlaufen (sonst zuschlag wegen Grösse und Farbe)
							if ($Anz_Merkmale==0) {
								// Preisdifferenz ermitteln, wenn Preis fuer Variantenartikel uebergeben
								if ($Artikel_Preis>0) {
									$options_values_price = $Artikel_Preis-dmc_get_price($Artikel_Variante_Von_id);
									// Nachlass
									if ($options_values_price<0) {
										$price_prefix = '-';
										$options_values_price = $options_values_price * -1;
									} // endif
								} else {
									$options_values_price = 0;
								}
								// Gewichtsdifferenz ermitteln , wenn Gewicht fuer Variantenartikel uebergeben
								if ($Artikel_Gewicht>0) {
									$options_values_weight = $Artikel_Gewicht-dmc_get_weight($Artikel_Variante_Von_id);
									// Nachlass
									if ($options_values_weight<0) {
										$weight_prefix = '-';
										$options_values_weight = $options_values_weight * -1;
									} // endif
								} else {
									$options_values_weight = 0;
								}
							} // endif
						//	if (DEBUGGER>=1) fwrite($dateihandle, "Preis Haupt=".dmc_get_price($Artikel_Variante_Von_id)." Artikel_Preis=$Artikel_Preis, price_prefix=$price_prefix, options_values_price=$options_values_price");
							
							// Auspraegung anlegen, wenn nicht existiert,
							$temp_id_query = dmc_db_query("SELECT max(products_attributes_id) as total from products_attributes");
							$TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
							// Wenn noch kein Eintrag, erster Eintrag, sonst erhoehen
							if ($TEMP_ID['total']=='' || $TEMP_ID['total']==null)
								$Zuordnungsid = 1;
							else 
								$Zuordnungsid = $TEMP_ID['total']+1;	
							
							// wenn KEINEsortierung von wawi uebergeben
							if ($Sortierung==0) {
								$Sortierung=10*$Anz_Merkmale;
							} 
							
							$sql_data_array = array(  	'products_id' => $Artikel_Variante_Von_id,
														'options_id' => $Merkmalid,
														'options_values_id' => $Auspraegungsid,
														'options_values_price' => $options_values_price,		
														'price_prefix' => $price_prefix,						
														// 'attributes_model' => $Artikel_ID,
														'attributes_model' => $Artikel_Artikelnr,
														'attributes_stock' => $Artikel_Menge,
														'options_values_weight' => $options_values_weight,					// Gewichtsdifferenz uzum Standardartikel
														'weight_prefix' => $weight_prefix,
														// 'weight_prefix' => '0.0000',
														'sortorder' => $Sortierung);
											
							// Ab gambiogx -> vpe und ean fuer attribut setzen
							if (strpos(strtolower(SHOPSYSTEM), 'gambiogx') !== false) {
						//		if (DEBUGGER>=1) fwrite($dateihandle, "2296=".$Artikel_VPE."\n");
								//$Ergebnis=$Artikel_VPE_ID."@".$products_vpe_status."@".$products_vpe_value;
								$werte = explode ( '@', dmc_prepare_vpe($Artikel_VPE));
								$sql_data_array['products_vpe_id'] = $werte[0];
								$sql_data_array['gm_vpe_value']=$werte[2];
								$sql_data_array['gm_ean']=$Artikel_EAN;
							}
														
							if ($existiert == 1) {			
								// Update
								$sql_data_array = array(  	'products_id' => $Artikel_Variante_Von_id,
														'options_id' => $Merkmalid,
														'options_values_id' => $Auspraegungsid,
														'options_values_price' => $options_values_price,		
														'price_prefix' => $price_prefix,						
														// 'attributes_model' => $Artikel_ID,
														'attributes_model' => $Artikel_Artikelnr,
														'attributes_stock' => $Artikel_Menge,
														'options_values_weight' => $options_values_weight,					// Gewichtsdifferenz uzum Standardartikel
														'weight_prefix' => $weight_prefix,
														// 'weight_prefix' => '0.0000',
														);
											
								// Ab gambiogx -> vpe und ean fuer attribut setzen
								if (strpos(strtolower(SHOPSYSTEM), 'gambiogx') !== false) {
							//		if (DEBUGGER>=1) fwrite($dateihandle, "2296=".$Artikel_VPE."\n");
									//$Ergebnis=$Artikel_VPE_ID."@".$products_vpe_status."@".$products_vpe_value;
									$werte = explode ( '@', dmc_prepare_vpe($Artikel_VPE));
									$sql_data_array['products_vpe_id'] = $werte[0];
									$sql_data_array['gm_vpe_value']=$werte[2];
									$sql_data_array['gm_ean']=$Artikel_EAN;
								} 
								// $sql_data_array['sortorder'] = $Sortierung;
								dmc_sql_update_array('products_attributes', $sql_data_array, 
								"options_id ='".$Merkmalid."' and options_values_id ='".$Auspraegungsid."' and products_id='".$Artikel_Variante_Von_id."'");
							} else {
								// Insert
								$sql_data_array = array(  	'products_id' => $Artikel_Variante_Von_id,
														'options_id' => $Merkmalid,
														'options_values_id' => $Auspraegungsid,
														'options_values_price' => $options_values_price,		
														'price_prefix' => $price_prefix,						
														// 'attributes_model' => $Artikel_ID,
														'attributes_model' => $Artikel_Artikelnr,
														'attributes_stock' => $Artikel_Menge,
														'options_values_weight' => $options_values_weight,					// Gewichtsdifferenz uzum Standardartikel
														'weight_prefix' => $weight_prefix,
														// 'weight_prefix' => '0.0000',
														);
											
								// Ab gambiogx -> vpe und ean fuer attribut setzen
								if (strpos(strtolower(SHOPSYSTEM), 'gambiogx') !== false) {
							//		if (DEBUGGER>=1) fwrite($dateihandle, "2296=".$Artikel_VPE."\n");
									//$Ergebnis=$Artikel_VPE_ID."@".$products_vpe_status."@".$products_vpe_value;
									$werte = explode ( '@', dmc_prepare_vpe($Artikel_VPE));
									$sql_data_array['products_vpe_id'] = $werte[0];
									$sql_data_array['gm_vpe_value']=$werte[2];
									$sql_data_array['gm_ean']=$Artikel_EAN;
								}
								$sql_data_array['sortorder'] = $Sortierung;
								$sql_data_array['products_attributes_id'] = $Zuordnungsid;
								dmc_sql_insert_array('products_attributes', $sql_data_array);
								$Neue_ID = dmc_db_get_new_id();
							}
					} // endif  Nur, wenn Merkmal übergeben wurde
				} // End for $Anz_Merkmale
					
				// update auf den Hauptartikel bzgl des gesamtbestandes der zugehoerigen attribute
				dmc_update_conf_stock($Artikel_Variante_Von_id);
				
?>				
?>
	