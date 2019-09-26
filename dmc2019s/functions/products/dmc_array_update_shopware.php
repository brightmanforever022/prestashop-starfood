<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_array_update_shopware.php											*
*  inkludiert von dmc_write_art_shopware.php								*	
*  Shopware Artikel Array mit Werten fuellen								*
*  Copyright (C) 2014 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
09.05.2014
- neu
*/
				// Anlage NUR von Standardartikeln
				
				// Abfangroutine
				//if ($Artikel_Steuersatz==2) $Artikel_Steuersatz=7; else $Artikel_Steuersatz=19;
				if ($exists==0) $Aktiv=0;
				if ($Artikel_Steuersatz==1) $Artikel_Steuersatz=19; else if ($Artikel_Steuersatz==2) $Artikel_Steuersatz=7;
			
				// Alten Sonderpreis ermitteln, da dieser sonst verloren geht. ACHTUNG: Sonderpreis in Array ist netto, daher umrechnen
				$ursprungsartikel=$client->call('articles/'.$art_id, ApiClient::METHODE_GET);
				$pseudopreis=$ursprungsartikel["data"]['mainDetail']['prices'][0]['pseudoPrice'] * 1.19;
			
				$sql_update_data_array =  array(
					'name' => $Artikel_Bezeichnung,			
				//	'tax' => $Artikel_Steuersatz,				
				//	'supplier' => $Hersteller_ID,			
					'active' => $Aktiv,
				//	'inStock' => $Artikel_Menge,
				//	'descriptionLong' => $Artikel_Langtext,
				//	'description' => $Artikel_MetaDescription,
				//	'keywords' => $Artikel_MetaKeyword,
				//	'metaTitle' => $Artikel_MetaText,
				//	'highlight' => $Artikel_Startseite,
					'mainDetail' => array(
							'inStock' => $Artikel_Menge,
				//			'shippingTime' => $Artikel_Lieferstatus,
				//			'number' => $Artikel_Artikelnr,			// Pflichtfeld
				//			'weight' => $Artikel_Gewicht,
							'active' => $Aktiv,
				//			'packUnit' => $Artikel_VPE,
				//			'ean' => $Artikel_EAN,
				//			'unitId' => 1,							// VPE fuer Grundpreise	, 1 fuer Liter
				//		    "purchaseUnit" =>	$Gebindegroesse,	// Verpackungsgroesse, zB 0.75
				//			"referenceUnit" =>	'1',						// Referenz zB 1 fuer 1 Liter
				//			'additionalText' => $Artikel_Kurztext,
							// Weitere Details
				//			'releaseDate' => $actual_date,
				//			'position' => $Sortierung,
				// Wird weiter unten zugeordnet
				/*			'attribute' => array(
								'attr1' => $attribut[1],
								'attr2' => $attribut[2],
								'attr3' => $attribut[3],
							), */
							'prices' => array(
								array(
									'customerGroupKey' => 'EK',		// Standard Kundengruppe Endkunden
									'price' => $Artikel_Preis,		// Standard Preis
									'pseudoPrice' => $pseudopreis,
								//	'baseprice' => $Artikel_Preis1, 	// Einkaufspreis
								),
								array(
									'customerGroupKey' => 'H',		// Standard Kundengruppe Haendler
									'price' => $Artikel_Preis1,		// Standard Preis
									'pseudoPrice' => $pseudopreis,
								),
							/*		array(
								'customerGroupKey' => 'USA',		// Standard Kundengruppe Haendler
								'price' => $Artikel_Preis2,		// Standard Preis
								),
								array(
									'customerGroupKey' => 'B2C U',		// Standard Kundengruppe Haendler
									'price' => $Artikel_Preis3,		// Standard Preis
								), */
							)
						), 
					);

									// Artikelattribute -> Pruefen auf 10 weitere ($Artikel_Attr_Werte_123 AS Artikel_Startseite) durch @ getrennte Attributswerte
				$attribut[0]='';
				$Artikel_Attr_Wert = explode ( '@', $Artikel_Attr_Werte_123);
				for($Anzahl = 1; $Anzahl <= count($Artikel_Attr_Wert); $Anzahl++) {     
					// Preufen, ob Werte vorhanden
					//if (count($Artikel_Attr_Wert) >= $Anzahl) {
						// Pruefen ab spezielle Bezeichnung des Attributes verwendet werden soll (muss Tabellenspaltenname aus s_articles_attributes entsprechen)
						// Wenn zB gruen@leder@1m, dann attr1-attr3 befuellen, wenn color|gruen@material|leder@length|1m, dann Freitextfelder color, material etc fuellen
						if (preg_match( '/|/' , $Artikel_Attr_Wert[$Anzahl-1])) {
							$werte = explode ("|", $Artikel_Attr_Wert[$Anzahl-1]);
							$attribut_bezeichnung[$Anzahl]=$werte[0];
							$attribut_wert[$Anzahl] =$werte[1];
							fwrite($dateihandle, "Freifeld (Spalte ".$attribut_bezeichnung[$Anzahl]." aus s_articles_attributes) fuellen mit ".$attribut_wert[$Anzahl]."\n");
							
						}  else {
							fwrite($dateihandle, "| nicht in  ".$Artikel_Attr_Wert[$Anzahl-1]." vorhanden\n");
							// Sonst Standard attr1-attr10
							$attribut_bezeichnung[$Anzahl]='attr'.$Anzahl;
							$attribut_wert[$Anzahl] = $Artikel_Attr_Wert[$Anzahl-1];
						}
						$sql_data_array['mainDetail']['attribute'][$attribut_bezeichnung[$Anzahl]] = $attribut_wert[$Anzahl];
				/*	} else {
						$attribut[$Anzahl] = '';
					}*/
				}
				/* ALT NUN IN set_images_shopware
				// Bilder - mehrere durch @ getrennt
				// Unterscheidung auf Lokal und URLs
				if (strpos($bilder[0], "http")!==false) {
						// URLs
						fwrite($dateihandle, "dmc_array_create_shopware ".count ( $bilder )." URL Bilder anlegen \n");
						for ( $i = 0; $i < count ( $bilder ); $i++ ) {
							if (strpos($bilder[$i], "http")!==false) {
								$sql_update_data_array['images'][$i]['link']=$bilder[$i];
								fwrite($dateihandle, "URL Bild[$i] = ".$sql_update_data_array['images'][$i]['link']." \n");
							} 
						} 
					} else {
						// lokale Bilder
						fwrite($dateihandle, "\n dmc_array_create_shopware lokale Bilder hinzufuegen $Artikel_Bilddatei \n");
						$shop_media_url=SHOP_URL.DIR_ORIGINAL_IMAGES;
						
						//$bilder=explode ('@', $Artikel_Bilddatei);
						
						for ( $i = 0; $i < count ( $bilder ); $i++ ) { 
							if (strpos($bilder[$i], "\\") !== false) {
								$bilder[$i]=substr($bilder[$i],(strrpos($bilder[$i],"\\")+1),254); 
							} else if (strpos($bilder[$i], "/") !== false && strpos($bilder[$i], "http") === false ) {
								$bilder[$i]=substr($bilder[$i],(strrpos($bilder[$i],"/")+1),254); 
							} 
							// Zuordnen, wenn Bild vorhanden
							$bild_exists = true;
							$bilddatei = SHOP_ROOT.DIR_ORIGINAL_IMAGES.$bilder[$i];
							$bildurl = $shop_media_url.$bilder[$i];
							$bildurl_headers = @get_headers($bildurl);
							if(!strpos($bildurl_headers[0], '200 OK')) {
								// EVTL mit .JPG gross
								$bilder[$i] = str_replace(".jpg",".JPG",$bilder[$i]);
								$bilddatei = SHOP_ROOT.DIR_ORIGINAL_IMAGES.$bilder[$i];
								$bildurl = $shop_media_url.$bilder[$i];
								$bildurl_headers = @get_headers($bildurl);
								if(strpos($bildurl_headers[0], '200 OK')) { 
									$bild_exists = true;
								} else {
									$bild_exists = false;
									fwrite($dateihandle, "bildurl $i $bildurl (order mit .jpg) existiert nicht \n");
								}
							}
							if ($bild_exists) {
								 fwrite($dateihandle, "bildurl $i $bildurl exists : ".$bildurl_headers[0]. "\n");
								if ($i==0) {
									fwrite($dateihandle, "dmc_array_update_shopware Bild $bilddatei (".$shop_media_url.$bilder[$i].") hinzufuegen, nachdem bisherige Zuordnungen geloescht sind. \n");
									$query="DELETE FROM `s_articles_img` WHERE articleID=$art_id";
									dmc_sql_query($query);
								}
								$sql_update_data_array['images'][$i]['link']=$shop_media_url.$bilder[$i];
								// Auf Zusatzbilder 1 bis 10 pruefen
								if ($i==0) {
									for ( $j = 1; $j <=10; $j++ ) {
										$zusatzbilddatei = str_replace(".JPG","_".$j.".JPG",$bilder[$i]);
										$zusatzbilddatei = str_replace(".jpg","_".$j.".jpg",$bilder[$i]);
										if (file_exists($zusatzbilddatei)) {
											$sql_update_data_array['images'][count($bilder)+$j]['link']=$shop_media_url.$zusatzbilddatei;
										// 	unlink($zusatzbilddatei);
										}
									}
								}
								fwrite($dateihandle, "Bild 0 = "."\n".$sql_update_data_array['images'][0]['link']);
								// unlink($bilddatei);
							} else {
								if (DEBUGGER>=1 && $i==0) fwrite($dateihandle, "dmc_array_update_shopware Bild $bilddatei (".$shop_media_url.$bilder[$i].") nicht vorhanden \n");
							}						
						}
					}
				}
				
				*/
				// Bei Standard Artikel entsprechen Merkmal und Auspraeung Shopware Eigenschaften
				if (preg_match('/@/', $Artikel_Merkmal)) {
					$Artikel_Merkmale = explode ( '@', $Artikel_Merkmal);
					$Artikel_Auspraegungen = explode ( '@', $Artikel_Auspraegung);				
					fwrite($dateihandle, "126 Shopware Eigenschaften Artikel_Merkmal=  $Artikel_Merkmal und $ Artikel_Merkmale[0] = ".$Artikel_Merkmale[0]."\n");
					$filter=true;
				}  else {
					$filter=false;
				}	
			//	$filter=false;
				if ($filter) {
					// Pruefen, ob Filtergruppe vorhanden
					$Anzahl_Artikel_Merkmale=count ( $Artikel_Merkmale );
					// ACHTUNG: Im Standard maximale Anzahl von Filtern 15
				//	if ($Anzahl_Artikel_Merkmale>16)
				//					$Anzahl_Artikel_Merkmale=16;
					for ( $i = 0; $i < $Anzahl_Artikel_Merkmale; $i++ )
					{			
						$filtergruppe .= $Artikel_Merkmale[$i].' ';
					}
					$filtergruppe='dmConnector_'.$Anzahl_Artikel_Merkmale;
					$col='id';
					$table='s_filter';
					$where="name='$filtergruppe'";
					$filtergruppenid=dmc_sql_select_query($col,$table,$where);
					if ($filtergruppenid=='') {
						# Filtergruppe anlegen, wenn noch nicht vorhanden
						fwrite($dateihandle, " Filtergruppe  $filtergruppe anlegen ... "); 
							$properties = array(
								"name" => $filtergruppe,
								'position' => 1,
								'comparable' => 1,
								'sortmode' => 2
							);
						$client->call('propertyGroups', ApiClient::METHODE_POST, $properties );
						$filtergruppenid=dmc_sql_select_query($col,$table,$where);
						fwrite($dateihandle, "... done mit ID $filtergruppenid \n"); 
					}
						
				$sql_update_data_array['filterGroupId'] = $filtergruppenid;
					// Weitere Filter
					$anzahl_filter=0;
					for ( $i = 0; $i < $Anzahl_Artikel_Merkmale; $i++ )
					{	
						// ACHTUNG, wenn mehrere Filter gesetzt werden, diese auch verarbeiten, hier Ubergabe @Farbe|gruen|blau statt @Farbe|gruen
						if (strpos($Artikel_Auspraegungen[$i],"|")) {
							//fwrite($dateihandle, "**** Artikel_Auspraegungen ". $Artikel_Auspraegungen[$i]."\n"); 
							$temp_auspraegungen=explode('|',$Artikel_Auspraegungen[$i]);
							//fwrite($dateihandle, "**** Sind ". $temp_auspraegungen."Stueck \n");
							for ( $j = 0; $j < count ( $temp_auspraegungen ); $j++ )
							{
								//fwrite($dateihandle, "**** Filter ".$Artikel_Merkmale[$i]."mit: ". $temp_auspraegungen[$j]."\n"); 
								if (trim($Artikel_Merkmale[$i]) != "" && trim($temp_auspraegungen[$j]) != "") $sql_update_data_array['propertyValues'][$anzahl_filter]['option']['name'] = $Artikel_Merkmale[$i];
								if (trim($Artikel_Merkmale[$i]) != "" && trim($temp_auspraegungen[$j]) != "") $sql_update_data_array['propertyValues'][$anzahl_filter]['value'] = $temp_auspraegungen[$j];
								$anzahl_filter++;
							}
						} else {
							// Nur ein Filter
							if (trim($Artikel_Merkmale[$i]) != "" && trim($Artikel_Auspraegungen[$i]) != "") $sql_update_data_array['propertyValues'][$anzahl_filter]['option']['name'] = $Artikel_Merkmale[$i];
							if (trim($Artikel_Merkmale[$i]) != "" && trim($Artikel_Auspraegungen[$i]) != "") $sql_update_data_array['propertyValues'][$anzahl_filter]['value'] = $Artikel_Auspraegungen[$i];
							$anzahl_filter++;
						}
					}
					//$ergebnis=print_r($sql_update_data_array['propertyValues'],true);
					// fwrite($dateihandle, " Filter dem Artikel Update Array hinzugefuegt: $ergebnis\n"); 
					fwrite($dateihandle, " Filter dem Artikel Update Array hinzugefuegt\n"); 
				}
				
				
				
				
?>