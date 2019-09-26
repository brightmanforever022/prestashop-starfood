<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_array_create_shopware.php											*
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
				// if ($Artikel_Steuersatz==2) $Artikel_Steuersatz=7; else $Artikel_Steuersatz=19;
				if ($Artikel_Steuersatz==1) $Artikel_Steuersatz=19; else if ($Artikel_Steuersatz==2) $Artikel_Steuersatz=7;
				if ($Aktiv==1) $Aktiv=true;
				if ($Hersteller_ID=='') $Hersteller_ID='-'; // Pflichtfeld
				if ($Sortierung=='') $Sortierung=0;
				
				$sql_data_array =  array(
					'name' => $Artikel_Bezeichnung,				// Pflichtfeld
					'tax' => $Artikel_Steuersatz,				// Pflichtfeld (oder taxId
					'supplier' => $Hersteller_ID,				// HerstellerName, wenn  noch nicht existiert, wird er erstellt 
					'active' => $Aktiv,
					'descriptionLong' => $Artikel_Langtext,
					'description' => $Artikel_MetaDescription,
					'keywords' => $Artikel_MetaKeyword,
					'metaTitle' => $Artikel_MetaText,
					'highlight' => $Artikel_Startseite,
					// [position] => 0
					/* [supplierNumber] => 
					[kind] => 1
					[additionalText] => 
					[stockMin] => 0
					[width] => 
					[len] => 
					[height] => 
					[minPurchase] => 
					[purchaseSteps] => 
					[maxPurchase] =>  */
				//	[shippingFree] => 
				//	[releaseDate] => 
					// filterGroupId
			//		'categories' => array(
			//			array('id' => $Kategorie_ID),			// !!! $Kategorie_ID wird gglfs erst spaeter geprueft
			//		),
				/*
					'images' => array(
						array('link' => 'http://www.wizard.ch/pictures_1000x1000/00123_Bamboo_Forest.jpg'),
						array('link' => 'http://www.wizard.ch/pictures_1000x1000/00123_Interior_Bamboo_Forest.jpg'),
						array('link' => 'http://www.wizard.ch/pictures_1000x1000/00123_close_up_Bamboo_Forest.jpg'),
						
					),*/
				 	'mainDetail' => array(
						'inStock' => $Artikel_Menge,
						'shippingTime' => $Artikel_Lieferstatus,						// ZB '3-5'
						'number' => $Artikel_Artikelnr,						// Pflichtfeld
						'weight' => $Artikel_Gewicht,
						'active' => $Aktiv,
						'packUnit' => $Artikel_VPE,
						/*'unit' => array(
							'unit' => $Artikel_VPE,
							'name' => $Artikel_VPE
						), */
						'ean' => $Artikel_EAN,
						'additionalText' => $Artikel_Kurztext,
						// Weitere Details
						'releaseDate' => $actual_date,
						'position' => $Sortierung,
						/*
						'supplierNumber' => '',
						'kind' => 1,
						'additionalText' => '',
						'stockMin' => '',
						'width' => '',
						'length' => '',
						'height' => '',
						'minPurchase' => '',
						'purchaseSteps' => '',
						'maxPurchase' => '',
	
							'unitId' => 1,							// VPE fuer Grundpreise	, 1 fuer Liter
						    "purchaseUnit" =>	$Gebindegroesse,	// Verpackungsgroesse, zB 0.75
							"referenceUnit" =>	'1',						// Referenz zB 1 fuer 1 Liter
     
						//'purchaseUnit' => 6.0000
						//'referenceUnit' => 1.000
						'shippingFree' => '', */
						// Artikelattribute siehe weiter UNTEN
						/*'attribute' => array(
							'attr1' => $attribut[1],
							'attr2' => $attribut[2],
							'attr3' => $attribut[3],
							'attr4' => $attribut[4],
							'attr5' => $attribut[5],
							'attr6' => $attribut[6],
							'attr7' => $attribut[7],
							'attr8' => $attribut[8],
							'attr9' => $attribut[9],
							'attr10' => $attribut[10],
						),*/
						'prices' => array(
								array(
								'customerGroupKey' => 'EK',		// Standard Kundengruppe Endkunden
								'price' => $Artikel_Preis,		// Standard Preis
								//'baseprice' => $Artikel_Preis1, 	// Einkaufspreis
							),
							array(
								'customerGroupKey' => 'H',		// Standard Kundengruppe Haendler
								'price' => $Artikel_Preis1,		// Standard Preis
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
					
				//	'attribute' => array(
					//	'attr1' => 'S/Weiß Attr1',
						// 'attr2' => 'Freitext2',
				//	),
				

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
				
				
				// Bilder - mehrere durch @ getrennt
				if ($Artikel_Bilddatei != "") {
					if (is_file('userfunctions/set_images_shopware.php')) include ('userfunctions/set_images_shopware.php');
					else if (is_file('../functions/set_images_shopware.php')) include ('../functions/set_images_shopware.php');
					else include ('functions/set_images_shopware.php');
				}
				
								
				// Bei Standard Artikel entsprechen Merkmal und Auspraeung Shopware Eigenschaften
				if (preg_match('/@/', $Artikel_Merkmal)) {
					$Artikel_Merkmale = explode ( '@', $Artikel_Merkmal);
					$Artikel_Auspraegungen = explode ( '@', $Artikel_Auspraegung);				
					fwrite($dateihandle, "100   Shopware Eigenschaften Artikel_Merkmal=  $Artikel_Merkmal und $ Artikel_Merkmale[0] = ".$Artikel_Merkmale[0]."\n");
					$filter=true;
				}  else {
					$filter=false;
				}	
			//	$filter=false;
				if ($filter) {
					$Anzahl_Artikel_Merkmale=count ( $Artikel_Merkmale );
					// ACHTUNG: Im Standard maximale Anzahl von Filtern 15
				//	if ($Anzahl_Artikel_Merkmale>16)
					//				$Anzahl_Artikel_Merkmale=16;
					// Pruefen, ob Filtergruppe vorhanden
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
						
					$sql_data_array['filterGroupId'] = $filtergruppenid;
					// Weitere Filter
					/*for ( $i = 0; $i < count ( $Artikel_Merkmale ); $i++ )
					{			
						if (trim($Artikel_Merkmale[$i]) != "" && trim($Artikel_Auspraegungen[$i]) != "") $sql_data_array['propertyValues'][$i]['option']['name'] = $Artikel_Merkmale[$i];
						if (trim($Artikel_Merkmale[$i]) != "" && trim($Artikel_Auspraegungen[$i]) != "") $sql_data_array['propertyValues'][$i]['value'] = $Artikel_Auspraegungen[$i];
					}*/
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
								if (trim($Artikel_Merkmale[$i]) != "" && trim($temp_auspraegungen[$j]) != "") $sql_data_array['propertyValues'][$anzahl_filter]['option']['name'] = $Artikel_Merkmale[$i];
								if (trim($Artikel_Merkmale[$i]) != "" && trim($temp_auspraegungen[$j]) != "") $sql_data_array['propertyValues'][$anzahl_filter]['value'] = $temp_auspraegungen[$j];
								$anzahl_filter++;
							}
						} else {
							// Nur ein Filter
							if (trim($Artikel_Merkmale[$i]) != "" && trim($Artikel_Auspraegungen[$i]) != "") $sql_data_array['propertyValues'][$anzahl_filter]['option']['name'] = $Artikel_Merkmale[$i];
							if (trim($Artikel_Merkmale[$i]) != "" && trim($Artikel_Auspraegungen[$i]) != "") $sql_data_array['propertyValues'][$anzahl_filter]['value'] = $Artikel_Auspraegungen[$i];
							$anzahl_filter++;
						}
					}
					//$ergebnis=print_r($sql_data_array['propertyValues'],true);
					// fwrite($dateihandle, " Filter dem Artikelarray hinzugefuegt: $ergebnis\n"); 
					
					fwrite($dateihandle, " Filter mit anzahl_filter = $anzahl_filter dem Artikelarray hinzugefuegt \n"); 
				}
			
				
?>