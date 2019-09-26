<?php
/*******************************************************************************************
*                                                                               			*
*  dmConnector  for shopware shop															*
*  dmc_art_update_shopware.php																*
*  Artikel Preis und Bestand 																*
*  Copyright (C) 2018 DoubleM-GmbH.de														*
*                                                                                 			*
*******************************************************************************************/
/*

*/

defined( '_DMC_ACCESSIBLE' ) or die( 'Direct Access to this location is not allowed.' );

ini_set("display_errors", 1);
error_reporting(E_ALL);

	function Art_Update_Shopware() {
		
		global $action, $dateihandle;
		if (DEBUGGER>=1) fwrite($dateihandle, "function Art_Update_Shopware in dmc_art_update_shopware.php	action=$action \n");
	
		$ExportModus = ($_POST['ExportModus']);
		$sonderfunktion = (($_POST['Artikel_ID']));						
		$Artikel_Artikelnr = explode ( '@', $_POST['Artikel_Artikelnr'])[0];
		$Artikel_Artikelnr = trim($Artikel_Artikelnr);
		// $Artikel_ArtikelEAN = explode ( '@', $_POST['Artikel_Artikelnr'])[1];
		$Artikel_Menge = ($_POST['Artikel_Menge']);
		if ($Artikel_Menge=='0E-14')  $Artikel_Menge = 0;
		$Artikel_Preis = ($_POST['Artikel_Preis']);							// Mehrere Preise durch @ getrennt
		if (strpos($Artikel_Preis,'@')!== false) {
			$Preis = explode('@',$Artikel_Preis);
			$Artikel_Preis=$Preis[0];
			$Artikel_Preis1=$Preis[1];
			$Artikel_Preis2=$Preis[2];
			$Artikel_Preis3=$Preis[3];
		} else {
			$Artikel_Preis1=0;
			$Artikel_Preis2=0;
			$Artikel_Preis3=0;
		}
		$Artikel_Status = ($_POST['Artikel_Status']);
		$Artikel_Steuersatz = ($_POST['Artikel_Steuersatz']);
		$Artikel_Lieferstatus = ($_POST['Artikel_Lieferstatus']);
		// Sonderfunktionen wolf
		if ($sonderfunktion == "wolf") {
			$SkipImages=$USER_Variante=$Artikel_Steuersatz;		// zB Ohrhänger
			// Mappings inkludieren fuer Preisberechnungsformeln etc.
			if (is_file('userfunctions/products/dmc_mappings.php')) include ('userfunctions/products/dmc_mappings.php');
			else include ('functions/products/dmc_mappings.php');
			if (DEBUGGER>=1) fwrite($dateihandle, "Mappings ergibt Preisberechnungsformel $Preisberechnungsformel \n");
			if (is_file('userfunctions/products/dmc_array_update_price_shopware.php')) include ('userfunctions/products/dmc_array_update_price_shopware.php');
			else include ('functions/products/dmc_array_update_price_shopware.php');
			if (DEBUGGER>=1) fwrite($dateihandle, "Mappings Preise auf Gruppen ergibt zB Preis fuer Gruppe ".$sql_update_price_array['mainDetail']['prices'][11]['customerGroupKey']." = ".$sql_update_price_array['mainDetail']['prices'][11]['price']." \n");
		}
		
		// Artikel laden
		$Artikel_ID=dmc_get_id_by_artno($Artikel_Artikelnr);
		
		if ($Artikel_ID!="") { 
			$exists = 1;      
			if (DEBUGGER>=1) fwrite($dateihandle, "Art_Update - ".date("l d of F Y h:i:s A")." - Artikel $Artikel_Artikelnr mit ID $Artikel_ID fuer Update existiert ...  $ExportModus \n");      
			if (strpos($ExportModus,'Preis')!==false)
			{
				if ($sonderfunktion == "wolf") {
				    for ($j=0;$j<=24;$j++) {
						fwrite($dateihandle, "Wolf Preisberechnungsformel = $Preisberechnungsformel \n");
							
						if ($Preisberechnungsformel=="" || $Preisberechnungsformel=="ohne Berechnungsformel") {
							$query="UPDATE s_articles_prices SET price=".$sql_update_price_array['mainDetail']['prices'][$j]['price']." WHERE pricegroup='".$sql_update_price_array['mainDetail']['prices'][$j]['customerGroupKey']."' AND `from`=1 ".
			              //	"AND articledetailsID = ".$Artikel_ID;
				        	"AND articledetailsID = (select id from s_articles_details  WHERE ordernumber ='".$Artikel_Artikelnr."')";
			            	fwrite($dateihandle, "Artikel ID ".$Artikel_ID." Shopware DB Preis aktualisieren ($query).");
			            	if ($sql_update_price_array['mainDetail']['prices'][$j]['price']>0) 
								dmc_sql_query($query);
			            //	$query="UPDATE s_articles_details SET active=".$Artikel_Status." WHERE articleID =".$Artikel_ID;
			           //	dmc_sql_query($query);
						} else {
							fwrite($dateihandle, "Preisberechnungsformel = $Preisberechnungsformel \n");
							// Groessen kommt nun bereits aus dmc_mappings
							// $Artikel_Groessen =  explode ( '|', $Groessen);
							fwrite($dateihandle, "Artikel_Groessen = $Groessen mit Standardgroesse $Standardgroesse \n");
							
							/*	$query="UPDATE s_articles_prices SET price=".$sql_update_price_array['mainDetail']['prices'][$j]['price']." WHERE pricegroup='".$sql_update_price_array['mainDetail']['prices'][$j]['customerGroupKey']."' AND `from`=1 ".
							//	"AND articledetailsID = ".$Artikel_ID;
								"AND articledetailsID = (select id from s_articles_details  WHERE ordernumber ='".$Artikel_Artikelnr."')";
								fwrite($dateihandle, "Artikel ID ".$Artikel_ID." Shopware DB Preis (EK) aktualisieren ($query).");
								*/
							//	if ($Artikel_Preis>0) dmc_sql_query($query);
							//	$query="UPDATE s_articles_details SET active=".$Artikel_Status." WHERE articleID =".$Artikel_ID;
							//	dmc_sql_query($query);
							if ( $Groessen != "") {
								fwrite($dateihandle, "96 Variantenpreise updaten, da Groessen vorhanden, Groessen:  $Groessen \n");
								$Artikel_Variante_Von=($Artikel_Artikelnr);
								$Artikel_Artikelnr_orig=utf8_decode($Artikel_Artikelnr);
								$Artikel_Artikelnr="vari"; 
								// Ggfls als zum Variantenartikel erweitern 
								$main_art_id=dmc_get_id_by_artno($Artikel_Variante_Von);
								// Erste Variante anlegen
								// Configurator Set mit Merkmalen der ersten Variante "anlegen" 
								$Artikel_Merkmal="Größe";
								// Groessen kommt nun bereits aus dmc_mappings
								
								$Artikel_Groessen =  explode ( '|', $Groessen);
								for ( $durchlauf = 0; $durchlauf < count ( $Artikel_Groessen ); $durchlauf++ ) {
									if ($durchlauf>15) break;
									$Artikel_Merkmale = "";
									$Artikel_Auspraegungen = "";
									$Artikel_Merkmal = "Groesse";
									$Artikel_Auspraegung=$Artikel_Groessen[$durchlauf];
									$Artikel_Artikelnr = $Artikel_Artikelnr_orig."-".$Artikel_Auspraegung;		// Achtung KEINE | oder / in Artikelnummern
									$Artikel_Artikelnr = str_replace("/","--",$Artikel_Artikelnr);
									$Artikel_Artikelnr = str_replace("//","--",$Artikel_Artikelnr);
									fwrite($dateihandle, "118 - Variantenartikel $durchlauf updaten: $Artikel_Artikelnr und mit Artikel_Auspraegung ".$Artikel_Auspraegung."\n");
									if (preg_match('/@/', $Artikel_Merkmal)) {
										$Artikel_Merkmale = explode ( '@', $Artikel_Merkmal);
										$Artikel_Auspraegungen = explode ( '@', $Artikel_Auspraegung);				
									} else {
										$Artikel_Merkmale[0] =  $Artikel_Merkmal;
										$Artikel_Auspraegungen[0] =  $Artikel_Auspraegung;			
										// Mapping auf Groesse und Farbe
										if (strpos($Artikel_Merkmale[0],"Groesse")!==false)
										{	
											$Artikel_Merkmale[0]="Groesse";
										}
										if (strpos($Artikel_Merkmale[0],"Farbe")!==false)
										{	
											$Artikel_Merkmale[0]="Farbe";
										}
									} // endif  
										
									$varianten_artikel['variants'][0]['number'] = $Artikel_Artikelnr;	
							
							// WOLF Auf-Preisberechnung, wenn Groesse von Standardgroesse abweicht
									//	$Preisberechnungsformel = "BP + 10 (€/B2B) /15 (($/B2B) /20(€/B2C)/30 ($/B2B)";
									//	$Faktor = "";
									//	$Mindestaufpreis = "10€/20€/15$/30$";
									// 	$Standardgroesse = "45cm";
									//	$Groessen = "43|45|47";
									if (substr($Preisberechnungsformel,0,4) != "ohne" && strpos($Standardgroesse,$Artikel_Auspraegung) === false) {
										fwrite($dateihandle, "145 Aufpreis fuer Groesse: $Artikel_Auspraegung \n");
										$aufpreis1=$aufpreis2=$aufpreis3=$aufpreis=0;
										if ($Preisberechnungsformel == "BP + 10 (€/B2B) /15 (($/B2B) /20(€/B2C)/30 ($/B2B)") {
											fwrite($dateihandle, "148 Preisberechnung nach $Preisberechnungsformel \n");
											$variantenpreis = $Artikel_Preis	+20;			// Ek 
											$variantenpreis1 = $Artikel_Preis1	+10;			// h  
											$variantenpreis2 = $Artikel_Preis2	+15;			// usa 
											$variantenpreis3 = $Artikel_Preis3	+30;			// b2c us
										} else if (strpos($Preisberechnungsformel,"Preis = Basispreis x neue")!==false) {
											// Preis = Basispreis + (neue Länge/Standardlänge x Faktor)
											//	$Mindestaufpreis = "10€/20€/15$/30$";
											if ($Faktor == "" || $Faktor == "0")
												$Faktor = 1;
											$standardlaenge=str_replace("cm","",$Standardgroesse);
											fwrite($dateihandle, "159 Preisberechnung mit Standardlaenge=$standardlaenge fuer aktuelle Groesse=".$Artikel_Auspraegung." nach $Preisberechnungsformel \n");
											if ($Artikel_Auspraegung>$standardlaenge) {
												// Wenn neue Länge > Standardlänge dann Formel
												// $aufpreis=round(abs($standardlaenge-$Artikel_Auspraegung)*$Faktor);
												//fwrite($dateihandle, "243 aufpreis gemäß Formel = round(abs($standardlaenge-".$Artikel_Auspraegung." )*$Faktor) = ".$aufpreis." \n");
												$aufpreis1=$aufpreis2=$aufpreis3=$aufpreis;
												$variantenpreistemp = round($Artikel_Preis*$Artikel_Auspraegung/$standardlaenge);			// Ek 
												fwrite($dateihandle, " aufpreis gemäß Formel = variantenpreis ($variantenpreis) = $Artikel_Preis* round($Artikel_Auspraegung/$standardlaenge) \n");
												$variantenpreis1temp =round( $Artikel_Preis1*$Artikel_Auspraegung/$standardlaenge);				// h  
												$variantenpreis2temp = round($Artikel_Preis2*$Artikel_Auspraegung/$standardlaenge);			// usa 
												$variantenpreis3temp = round($Artikel_Preis3*$Artikel_Auspraegung/$standardlaenge);			// b2c us
												// 10012017 - Wenn neuer Preis < als Preis+Mindestaufschlag, dann den Preis mit Mindestaufschlag nehmen
												// Wenn neue Länge < Standardlänge dann feste werte als Aufpreise gem. $Mindestaufpreis
												$Mindestaufpreis = str_replace("€","",$Mindestaufpreis);
												$Mindestaufpreis = str_replace("$","",$Mindestaufpreis);
												$Mindestaufpreise = explode("/",$Mindestaufpreis); 
												fwrite($dateihandle, " Feste Aufpreise 1=".$Mindestaufpreise[0].", 2=".$Mindestaufpreise[1].", 3=".$Mindestaufpreise[2].", 4=".$Mindestaufpreise[3]."\n");
												$aufpreis=$Mindestaufpreise[0];
												$aufpreis1=$Mindestaufpreise[1];
												$aufpreis2=$Mindestaufpreise[2];
												$aufpreis3=$Mindestaufpreise[3];
												$variantenpreistemp2 = $Artikel_Preis	+$aufpreis1;			// Ek 
												$variantenpreis1temp2 = $Artikel_Preis1	+$aufpreis;				// h  
												$variantenpreis2temp2 = $Artikel_Preis2	+$aufpreis2;			// usa 
												$variantenpreis3temp2 = $Artikel_Preis3	+$aufpreis3;			// b2c us
												// 10012017 - Wenn neuer Preis < als Preis+Mindestaufschlag, dann den Preis mit Mindestaufschlag nehmen
												if ($variantenpreistemp<$variantenpreistemp2) {
													$variantenpreis=$variantenpreistemp2;
												} else {
													$variantenpreis=$variantenpreistemp;
												}
												if ($variantenpreis1temp<$variantenpreis1temp2) {
													$variantenpreis1=$variantenpreis1temp2;
												} else {
													$variantenpreis1=$variantenpreis1temp;
												}
												if ($variantenpreis2temp<$variantenpreis2temp2) {
													$variantenpreis2=$variantenpreis2temp2;
												} else {
													$variantenpreis2=$variantenpreis2temp;
												}
												if ($variantenpreis3temp<$variantenpreis3temp2) {
													$variantenpreis3=$variantenpreis3temp2;
												} else {
													$variantenpreis3=$variantenpreis3temp;
												}
												
											} else if ($Artikel_Auspraegung<$standardlaenge) {
												// Wenn neue Länge < Standardlänge dann feste werte als Aufpreise gem. $Mindestaufpreis
												$Mindestaufpreis = str_replace("€","",$Mindestaufpreis);
												$Mindestaufpreis = str_replace("$","",$Mindestaufpreis);
												$Mindestaufpreise = explode("/",$Mindestaufpreis); 
												fwrite($dateihandle, " Feste Aufpreise 1=".$Mindestaufpreise[0].", 2=".$Mindestaufpreise[1].", 3=".$Mindestaufpreise[2].", 4=".$Mindestaufpreise[3]."\n");
												$aufpreis=$Mindestaufpreise[0];
												$aufpreis1=$Mindestaufpreise[1];
												$aufpreis2=$Mindestaufpreise[2];
												$aufpreis3=$Mindestaufpreise[3];
												$variantenpreis = $Artikel_Preis	+$aufpreis1;			// Ek 
												$variantenpreis1 = $Artikel_Preis1	+$aufpreis;				// h  
												$variantenpreis2 = $Artikel_Preis2	+$aufpreis2;			// usa 
												$variantenpreis3 = $Artikel_Preis3	+$aufpreis3;			// b2c us
											} else {
												// Wenn neue Länge = Standardlänge dann kein Aufpreis
												fwrite($dateihandle, " KEIN aufpreis, da Laengen identisch \n");
												$aufpreis1=$aufpreis2=$aufpreis3=$aufpreis=0;
												$variantenpreis = $Artikel_Preis	+$aufpreis1;			// Ek 
												$variantenpreis1 = $Artikel_Preis1	+$aufpreis;				// h  
												$variantenpreis2 = $Artikel_Preis2	+$aufpreis2;			// usa 
												$variantenpreis3 = $Artikel_Preis3	+$aufpreis3;			// b2c us
											}
											
										} else {
											fwrite($dateihandle, " ACHTUNG Preisberechnungsformel nicht bekannt: $Preisberechnungsformel \n");
											$variantenpreis = $Artikel_Preis;
											$variantenpreis1 = $Artikel_Preis1;
											$variantenpreis2 = $Artikel_Preis2;
											$variantenpreis3 = $Artikel_Preis3;
										} 
									} else {
										$variantenpreis = $Artikel_Preis;
										$variantenpreis1 = $Artikel_Preis1;
										$variantenpreis2 = $Artikel_Preis2;
										$variantenpreis3 = $Artikel_Preis3;
									}
									
									fwrite($dateihandle, "variantenpreise ArtNr $Artikel_Artikelnr EK=".$variantenpreis.", H=".$variantenpreis1.", USA=".$variantenpreis2.", B2C U=".$variantenpreis3." \n");
									
									// Mapping Preise B2B und B2C auf Kundengruppen //						
									// Endkunden
									$varianten_artikel['prices'][0]['customerGroupKey'] = 'EK';	
							
									$varianten_artikel['prices'][0]['customerGroupKey'] = 'EK';	
									$varianten_artikel['prices'][1]['customerGroupKey'] = '9100';	
									$varianten_artikel['prices'][2]['customerGroupKey'] = '9500';	
									$varianten_artikel['prices'][3]['customerGroupKey'] = '9155';	
									$varianten_artikel['prices'][4]['customerGroupKey'] = '9220';	
									$varianten_artikel['prices'][5]['customerGroupKey'] = '9222';	
									$varianten_artikel['prices'][6]['customerGroupKey'] = '9223';	
									$varianten_artikel['prices'][7]['customerGroupKey'] = '9224';	
									$varianten_artikel['prices'][8]['customerGroupKey'] = '9800';	
									$varianten_artikel['prices'][9]['customerGroupKey'] = '9210';	
									$varianten_artikel['prices'][10]['customerGroupKey'] = '9212';	
									for ($j=0;$j<=10;$j++)
										$varianten_artikel['prices'][$j]['price'] = $variantenpreis*0.8403361344537815;	// Endkundengruppe Preisumrechnung
									// B2B Deutschland
									$varianten_artikel['prices'][11]['customerGroupKey'] = 'x';	 
									$varianten_artikel['prices'][12]['customerGroupKey'] = 'xx';	 
									$varianten_artikel['prices'][13]['customerGroupKey'] = 'xxx';	 
									$varianten_artikel['prices'][14]['customerGroupKey'] = 'xxxx';	 
									$varianten_artikel['prices'][15]['customerGroupKey'] = 'xxx5';	 
									$varianten_artikel['prices'][16]['customerGroupKey'] = 'pi';	 
									$varianten_artikel['prices'][17]['customerGroupKey'] = 'po';	 
									for ($j=11;$j<=17;$j++)
										$varianten_artikel['prices'][$j]['price'] = $variantenpreis1;
									// B2B USA
									$varianten_artikel['prices'][18]['customerGroupKey'] = '1100';	 
									$varianten_artikel['prices'][19]['customerGroupKey'] = '1190';	 
									for ($j=18;$j<=19;$j++)
										$varianten_artikel['prices'][$j]['price'] = $variantenpreis2;
									// Schweiz - International
									$varianten_artikel['prices'][20]['customerGroupKey'] = '1200';	 
									$varianten_artikel['prices'][21]['customerGroupKey'] = '1250';	 
									for ($j=20;$j<=21;$j++)
										$varianten_artikel['prices'][$j]['price'] = $variantenpreis3;
									$varianten_artikel['prices'][22]['customerGroupKey'] = '9130';	 
									$varianten_artikel['prices'][23]['customerGroupKey'] = '9300';	 
									for ($j=22;$j<=23;$j++)
										$varianten_artikel['prices'][$j]['price'] = $variantenpreis3*0.8403361344537815;	// Endkundengruppe Preisumrechnung
							
									// Weitere Preise id 24 ...
									$varianten_artikel['prices'][24]['customerGroupKey'] = '1300';	// B2B de 
									$varianten_artikel['prices'][24]['price'] =  $variantenpreis1;
								
									$var_art_id=dmc_get_articles_details_id_by_artno($Artikel_Artikelnr);
									fwrite($dateihandle, "varianten ArtNr $Artikel_Artikelnr mit ID $var_art_id ".
										"hat fuer Gruppe 1300(".$varianten_artikel['prices'][24]['customerGroupKey'].") ".
										" einen Preis von ".$varianten_artikel['prices'][24]['price']." \n");
									
									// Update Variantenpreise
									for ($j=0;$j<=24;$j++) {
										$query="UPDATE s_articles_prices SET price=".$varianten_artikel['prices'][$j]['price']." WHERE pricegroup='".$varianten_artikel['prices'][$j]['customerGroupKey']."' AND `from`=1 ".
								  //	"AND articledetailsID = ".$Artikel_ID;
											"AND articledetailsID = $var_art_id";
										fwrite($dateihandle, "Artikel ID ".$Artikel_ID." Shopware DB Preis aktualisieren ($query). \n");
										if ($varianten_artikel['prices'][$j]['price']>0) 
												dmc_sql_query($query);

									}
									//$result=$client->call('variants/' . $var_art_id , ApiClient::METHODE_PUT, $varianten_artikel);
								   // $result=$client->call('articles/' . $var_art_id , ApiClient::METHODE_PUT, $varianten_artikel);
									//$results = print_r($result, true);
									//fwrite($dateihandle, "# 240 Ergebnis: $results \n");
								
									//	fwrite($dateihandle, "# Variantenartikel API = $result \n");
								//	fwrite($dateihandle, "# Variantenartikel $Artikel_Artikelnr  zu ArtID $main_art_id mit ".$Artikel_Merkmale[0]."-> .\n");
								} // end for einzelne groessen
								
							}
						}
				    	
				    }
				} else {
					$query="UPDATE s_articles_prices SET price=".$Artikel_Preis." WHERE pricegroup='EK' AND `from`=1 ".
			    //	"AND articledetailsID = ".$Artikel_ID;
					"AND articledetailsID = (select id from s_articles_details  WHERE  ordernumber ='".$Artikel_Artikelnr."')";
			    	fwrite($dateihandle, "Artikel ID ".$Artikel_ID." Shopware DB Preis (EK) aktualisieren ($query).");
			    //	if ($Artikel_Preis>0) dmc_sql_query($query);
			    //	$query="UPDATE s_articles_details SET active=".$Artikel_Status." WHERE articleID =".$Artikel_ID;
			    //	dmc_sql_query($query);
				}
				
				
			}
			if (strpos($ExportModus,'Quantity')!==false)
			{
				if ($sonderfunktion == "wolf") {
					$update_auf_varianten=false;		// Bestand auf alle Varianten setzen
					fwrite($dateihandle, "Artikel_Groessen = $Groessen mit Standardgroesse $Standardgroesse \n");
							
					if ( $Groessen != "" && $update_auf_varianten==true) {
							fwrite($dateihandle, "336 Variantenbestaende updaten, da Groessen vorhanden, Groessen:  $Groessen \n");
							$Artikel_Variante_Von=($Artikel_Artikelnr);
							$Artikel_Artikelnr_orig=utf8_decode($Artikel_Artikelnr);
							$Artikel_Artikelnr="vari"; 
							// Ggfls als zum Variantenartikel erweitern 
							$main_art_id=dmc_get_id_by_artno($Artikel_Variante_Von);
							// Erste Variante anlegen
							// Configurator Set mit Merkmalen der ersten Variante "anlegen" 
							$Artikel_Merkmal="Größe";
							// Groessen kommt nun bereits aus dmc_mappings
								
							$Artikel_Groessen =  explode ( '|', $Groessen);
							for ( $durchlauf = 0; $durchlauf < count ( $Artikel_Groessen ); $durchlauf++ ) {
								if ($durchlauf>15) break;
								$Artikel_Merkmale = "";
								$Artikel_Auspraegungen = "";
								$Artikel_Merkmal = "Groesse";
								$Artikel_Auspraegung=$Artikel_Groessen[$durchlauf];
								$Artikel_Artikelnr = $Artikel_Artikelnr_orig."-".$Artikel_Auspraegung;		// Achtung KEINE | oder / in Artikelnummern
								$Artikel_Artikelnr = str_replace("/","--",$Artikel_Artikelnr);
								$Artikel_Artikelnr = str_replace("//","--",$Artikel_Artikelnr);
									$varianten_artikel['variants'][0]['number'] = $Artikel_Artikelnr;	
							
								$var_art_id=dmc_get_articles_details_id_by_artno($Artikel_Artikelnr);
								fwrite($dateihandle, "varianten ArtNr $Artikel_Artikelnr hat ID $var_art_id \n");
								// Update Variantenbestaende
								$query="UPDATE s_articles_details SET instock=".$Artikel_Menge." WHERE articledetailsID = $var_art_id";
								fwrite($dateihandle, "Varianten Artikel ID ".$var_art_id." Shopware DB Bestand aktualisierten ($query).");
								// dmc_sql_query($query);
							} // end for einzelne groessen
								
					} else {
						// Standardartikel ohne Groessen bzw Hauptartikel
						fwrite($dateihandle, "Standardartikel Artikel ID ".$Artikel_ID." Shopware DB Bestand aktualisierten ($query).");
						$query="UPDATE s_articles_details SET instock=".$Artikel_Menge." WHERE articledetailsID = $var_art_id";
						fwrite($dateihandle, "Varianten Artikel ID ".$var_art_id." Shopware DB Bestand aktualisierten ($query).");
						// dmc_sql_query($query);
					}
					
				} else {
					fwrite($dateihandle, "Bestand ArtNr $Artikel_Artikelnr updaten = $Artikel_Menge \n");
					//if ($Artikel_Status==true) $Artikel_Status=1;
					//$query="UPDATE s_articles_details SET active=".$Artikel_Status.", instock=".$Artikel_Menge." WHERE ordernumber ='".$Artikel_Artikelnr."'";
					$query="UPDATE s_articles_details SET instock=".$Artikel_Menge." WHERE ordernumber ='".$Artikel_Artikelnr."'";
					fwrite($dateihandle, "Artikel ID ".$Artikel_ID." Shopware DB Bestand aktualisierten ($query).");
					// dmc_sql_query($query);

				}
			}
		}
		else
		{
			// Artikel existiert nicht
			if (DEBUGGER>=1) 	fwrite($dateihandle, "Art_Update - ".date("l d of F Y h:i:s A")." - Artikel (als Hauptartikel) exisitert nicht...\n");
			$exists = 0;	
		}
		
	} // end function
	
?>
	
	