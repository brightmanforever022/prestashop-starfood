<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for shopware													*
*  dmc_write_art_shopware.php												*
*  Artikel schreiben fuer shopware											*
*  Copyright (C) 2014 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
*/
 ini_set("display_errors", 1);
error_reporting(E_ERROR & ~E_NOTICE & ~E_DEPRECATED);

	defined( 'VALID_DMC' ) or die( 'Direct Access to this location is not allowed.' );
	
	function dmc_write_art($ExportModus, $Artikel_ID, $Kategorie_ID, $Hersteller_ID,$Artikel_Artikelnr,$Artikel_Menge,
		$Artikel_Preis,$Artikel_Preis1,$Artikel_Preis2,$Artikel_Preis3,$Artikel_Preis4,$Artikel_Gewicht,$Artikel_Status,$Artikel_Steuersatz,
		$Artikel_Bilddatei,$Artikel_VPE,$Artikel_Lieferstatus,$Artikel_Startseite,$SkipImages,$Aktiv,$Aenderungsdatum,$Artikel_Variante_Von,$Artikel_Merkmal,
		$Artikel_Auspraegung,$Artikel_Bezeichnung ,$Artikel_Langtext ,$Artikel_Kurztext,$Artikel_Sprache,$Artikel_MetaText,$Artikel_MetaDescription,$Artikel_MetaKeyword,$Artikel_MetaUrl) {
	
		global $dateihandle, $action, $client, $Artikel_Attr_Werte_123;
				
		// Laufzeit
		$beginn = microtime(true); 
		fwrite($dateihandle, "dmc_write_art @ dmc_write_art_shopware- ArtNr: $Artikel_Artikelnr (".date("l d of F Y h:i:s A").")"); 
		//	require_once('conf/configure_shop_shopware.php');
		
		// Hauptartikelpreis auf niedrigsten Var Preis setzen, wenn dieser 0
		$set_lowest_var_price_4_main=false;
		
		// Aktuelles Datum im Shopware konformen Format
		$objDateTime = new DateTime('NOW');
		$actual_date = $objDateTime->format(DateTime::ISO8601); // Date as an ISO8601 formatted string
		// Artikel Funktionen einbinden
		if (is_file('userfunctions/products/dmc_art_functions.php')) include ('userfunctions/products/dmc_art_functions.php');
		else include ('functions/products/dmc_art_functions.php');
		
		// Mappings
		// Mappings, zB Ermittlung von $Artikel_EAN
		if (is_file('userfunctions/products/dmc_mappings.php')) include ('userfunctions/products/dmc_mappings.php');
		else include ('functions/products/dmc_mappings.php');
		$art_id=dmc_get_id_by_artno($Artikel_Artikelnr);
		
		if ($art_id=="") {
			$exits=false; 
			if ($Artikel_Status=="delete" || $Artikel_Status=="loeschen" || $Aktiv=="delete" || $Aktiv=="loeschen") {
				// Wenn Status = delete, dann keine weitere Verarbeitung
				$rueckgabe = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
			   "<STATUS>\n" .
			   " <STATUS_INFO>\n" .
			   "  <ACTION>$action</ACTION>\n" .
			   "  <MESSAGE>Artikel nicht angelegt</MESSAGE>\n" .
			   "  <MODE>DELETED</MODE>\n" .
			   "    <success>1</success>\n" .
			   "  <ID>$art_id</ID>\n" .
			   " </STATUS_INFO>\n" .
			   "</STATUS>\n\n";
			
				echo $rueckgabe;
				fwrite($dateihandle, "dmc_write_art_shopware - Artikel=".$art_id." nicht angelegt, da Status delete\n"); 
						
				return $art_id;	
			}
		} else {
			$exits=true;
			if ($Artikel_Status=="delete" || $Artikel_Status=="loeschen" || $Aktiv=="delete" || $Aktiv=="loeschen") {
				// Wenn Status = delete, dann keine weitere Verarbeitung
				// Shopware API BugFIX -> Bilder aus Media Ordner erst löschen, dann Produkt
				$ergebnis=$client->call('articles/'.$art_id, ApiClient::METHODE_GET);
				//echo "ArtID:".$ergebnis["data"]["id"]."<br />\n";
				// echo "Anz Bilder:".sizeof($ergebnis["data"]['images'])."<br />\n";
				// Bilder löschen
				foreach ($ergebnis["data"]['images'] as $bild)
					$client->call('media/'.$bild['mediaId'], ApiClient::METHODE_DELETE);
					
				fwrite($dateihandle, "Artikel ".$Artikel_Artikelnr." loeschen\n");
				$client->delete('articles/'.$art_id);
					
				$rueckgabe = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
			   "<STATUS>\n" .
			   " <STATUS_INFO>\n" .
			   "  <ACTION>$action</ACTION>\n" .
			   "  <MESSAGE>Artikel geloescht</MESSAGE>\n" .
			   "  <MODE>DELETED</MODE>\n" .
			   "    <success>1</success>\n" .
			   "  <ID>$art_id</ID>\n" .
			   " </STATUS_INFO>\n" .
			   "</STATUS>\n\n";
				echo $rueckgabe;
				fwrite($dateihandle, "dmc_write_art_shopware - Artikel=".$art_id." geloescht, da Status delete\n"); 
				return $art_id;	
			}
			// fwrite($dateihandle, "DELETE FIRST ".$art_id." ******\n");
			// $client->delete('articles/'.$art_id);
			// $exits=false;
			// $art_id=="";
			/*$configurator_set_id=dmc_get_configurator_set_id_by_art_id($art_id);
			// DELETE FIRST, da Shopware BUG -> Aktualisierung von Variantenartikeln nicht möglich
			if ($configurator_set_id!="") {
				fwrite($dateihandle, "Var-Artikel ID ".$art_id." vorab loeschen\n");
				$client->delete('articles/'.$art_id);
				$mode="deleted";
				$exits=false; 
				$art_id="";
			} */
		}
		
		if ($art_id=="") $exits=false; else $exits=true;
			if ($Artikel_Steuersatz==1) $Artikel_Steuersatz=19; else if ($Artikel_Steuersatz==2) $Artikel_Steuersatz=7;
		if ( $Artikel_Startseite == '1' || $Artikel_Startseite == 'yes') { $Artikel_Startseite= 'yes'; } // (Startseitenartikel yes/no)	
		else { $Artikel_Startseite = 'no';	}	
		// if (!is_numeric($Artikel_Lieferstatus)) $Artikel_Lieferstatus = 0;		// Numerisch in Tagen , kann auch 3-5 sein.
		// Lager
		if ( $Artikel_Menge > 0) { 		// Artikel auf Lager
			
			$_stock_status= 'instock'; 		// Bestandsstatus
			$_backorders = 'yes';			// (Nachbestellung yes/no)
			$_manage_stock  = 'yes';		// (Bestandsverwaltung yes/no)			
		} else { 						// Kein Bestand
			$_stock_status= 'instock'; 		// Bestandsstatus
			$_backorders = 'yes';			// (Nachbestellung yes/no)
			$_manage_stock  = 'yes';		// (Bestandsverwaltung yes/no)
		}
		if ($Kategorie_ID!="")
			if (is_file('userfunctions/products/dmc_generate_cat_id.php')) include ('userfunctions/products/dmc_generate_cat_id.php');
			else include ('functions/products/dmc_generate_cat_id.php');
			
		// ARTIKEL ANLEGEN
		if ($exits==false) {
			/*$Artikel_Preis1,$Artikel_Preis2,$Artikel_Preis3,$Artikel_Preis4,$Artikel_Status,,
			$Artikel_Bilddatei,,$,$,$SkipImages,$Aktiv,$Aenderungsdatum,$Artikel_Variante_Von,
			$Artikel_Merkmal,
			$Artikel_Auspraegung,$ ,$ ,$Artikel_Kurztext,$Artikel_Sprache,$Artikel_MetaText,
			,$Artikel_MetaUrl */			
			fwrite($dateihandle, "dmc_write_art SHOPWARE ARTIKEL ANLEGEN ... "); 
				if ( $Aktiv == 0) $Aktiv = false; else $Aktiv = true;
	
			// Artikel Array einlesen $sql_data_array 
			if (is_file('userfunctions/products/dmc_array_create_shopware.php')) include ('userfunctions/products/dmc_array_create_shopware.php');
			else include ('functions/products/dmc_array_create_shopware.php');

			fwrite($dateihandle, " GrundDaten ... "); 
			for ( $i = 0; $i < count ( $Kategorie_IDs ); $i++ ) {
				// KategorieIds ergaenzen
				if ($Kategorie_IDs[$i] !="" && $Kategorie_IDs[$i] !="0") {
					fwrite($dateihandle, " KategorieIds ergaenzen ... ".$Kategorie_IDs[$i]); 
					$sql_data_array['categories'][$i]['id'] = $Kategorie_IDs[$i];

				}
			}
			// Wenn Variantenartikel, dann nicht als Hauptartikel anlegen
			if ($Artikel_Variante_Von=="") {
				fwrite($dateihandle, " CALL API ... "); 
				
				$ausgeben = print_r($sql_data_array, true);
				fwrite($dateihandle, $ausgeben); 
				
				$result = $client->call('articles', ApiClient::METHODE_POST, $sql_data_array);
				$art_id = $result['data']['id'];
				fwrite($dateihandle, " Standardartikel mit ID $art_id angelegt. \n"); 
				if ($art_id=='')
					$fehlermeldung="Eventuell Validation-Error";
				else
					$fehlermeldung="";
				
				// Shopware API Bug abfangen,  Artikel aktivieren - UPDATE s_articles_details SET active=1 WHERE articleID = 
			//	$query="UPDATE s_articles_details SET active=$Aktiv WHERE articleID =".$art_id;
			//	dmc_sql_query($query);
				
				// Shopware API Bug abfangen, weil teilweise Varianten mit bestimmten Artikelnummern nicht angelegt werden STEP 2
				// Peter-Rutz - Kundengruppe Shopkunden  sperren
				//fwrite($dateihandle, "  - Kundengruppe Shopkunden  sperren \n"); 
				//$query="INSERT INTO s_articles_avoid_customergroups VALUES ($art_id ,1)";
				//dmc_sql_query($query);
			
			} else if ($Artikel_Variante_Von!="") {
				// Shopware API Bug abfangen, weil teilweise Varianten mit bestimmten Artikelnummern nicht angelegt werden STEP 1
				$Artikel_Artikelnr_orig=utf8_decode($Artikel_Artikelnr);
				$Artikel_Artikelnr="vari"; 
				$art_id = $result['data']['id'];
				// Ggfls als zum Variantenartikel erweitern 
				$main_art_id=dmc_get_id_by_artno($Artikel_Variante_Von);
				// Erste Variante anlegen
				// Configurator Set mit Merkmalen der ersten Variante "anlegen" 
				if (preg_match('/@/', $Artikel_Merkmal)) {
					$Artikel_Merkmale = explode ( '@', $Artikel_Merkmal);
					$Artikel_Auspraegungen = explode ( '@', $Artikel_Auspraegung);				
				} else {
					$Artikel_Merkmale[0] =  $Artikel_Merkmal;
					$Artikel_Auspraegungen[0] =  $Artikel_Auspraegung;			
					// Mapping auf Groesse und Farbe
					if (strpos($Artikel_Merkmale[0],"Größe")!==false)
					{	
						$Artikel_Merkmale[0]="Größe";
					}
					if (strpos($Artikel_Merkmale[0],"Farbe")!==false)
					{	
						$Artikel_Merkmale[0]="Farbe";
					}
				} // endif  
				fwrite($dateihandle, "104   Artikel_Merkmal=  $Artikel_Merkmal und $ Artikel_Merkmale[0] = ".$Artikel_Merkmale[0]."\n");
				for ( $i = 0; $i < count ( $Artikel_Merkmale ); $i++ )
				{			
					$varianten_artikel['configuratorSet']['groups'][$i]['name'] = $Artikel_Merkmale[$i];
					$varianten_artikel['configuratorSet']['groups'][$i]['options'][0]['name'] = $Artikel_Auspraegungen[$i];
				} // end for
				$varianten_artikel['variants'][0]['isMain'] = false;		// Erste Variante ist Hauptauswahl ! NUR eine!!!
				$varianten_artikel['variants'][0]['number'] = $Artikel_Artikelnr;	
				$varianten_artikel['variants'][0]['inStock'] = $Artikel_Menge;	
					$varianten_artikel['variants'][0]['weight'] = $Artikel_Gewicht;	 
			
				// i te Dimension 
				for ( $i = 0; $i < count ( $Artikel_Merkmale ); $i++ )
				{			
					$varianten_artikel['variants'][0]['additionaltext'] .= $Artikel_Auspraegungen[$i]. " ";	
					$varianten_artikel['variants'][0]['configuratorOptions'][$i]['group'] = $Artikel_Merkmale[$i];			// $i te Dimension
					$varianten_artikel['variants'][0]['configuratorOptions'][$i]['option'] = $Artikel_Auspraegungen[$i];		// $i te Dimension
				} // end for
				$varianten_artikel['variants'][0]['prices'][0]['customerGroupKey'] = 'EK';	
				$varianten_artikel['variants'][0]['prices'][0]['price'] = $Artikel_Preis;
				$varianten_artikel['variants'][0]['prices'][1]['customerGroupKey'] = 'H';	 
				$varianten_artikel['variants'][0]['prices'][1]['price'] = $Artikel_Preis1;
				$result=$client->call('articles/' . $main_art_id , ApiClient::METHODE_PUT, $varianten_artikel);
				$ausgabe=print_r($result,true);
				fwrite($dateihandle, "# Variantenartikel API Ergebnis = $ausgabe \n");
				fwrite($dateihandle, "# Variantenartikel $Artikel_Artikelnr  zu ArtID $main_art_id mit ".$Artikel_Merkmale[0]."-> ".$varianten_artikel['variants'][0]['additionaltext']." angelegt.\n");
			//	$results = print_r($varianten_artikel, true);
			//	fwrite($dateihandle, "# Variantenartikel: $results \n");
				// BUG FIX Shopware, Optionen werden nicht aktiviert und Variantenartikel als inaktiv gekennzeichnet
				$artikel = $client->call('articles/'.$main_art_id, ApiClient::METHODE_GET);
				//echo "configuratorSetId=".$artikel['data']['configuratorSetId']."\n";
				// echo "Anzahl Optionen=".count($artikel['data']['details'])."\n";
				//echo "configuratorSet OptionId=".$artikel['data']['details'][6]['configuratorOptions'][0]['id']."\n";
				//echo "configuratorSet GroupId=".$artikel['data']['details'][6]['configuratorOptions'][0]['groupId']."\n";
				//echo "configuratorSet OptionName=".$artikel['data']['details'][6]['configuratorOptions'][0]['name']."\n";
				// 1. Optionen aktivieren - INSERT INTO s_article_configurator_set_option_relations (set_id,option_id) VALUES (8,30)
				$configuratorSetId=$artikel['data']['configuratorSetId'];		// configuratorSetId ermitteln
				// Anzahl der Merkmale je configuratorSetId
				$merkmale_in_configuratorset = count($artikel['data']['details'][0]['configuratorOptions']);
				fwrite($dateihandle, "Anzahl der Merkmale je configuratorSetId=".$merkmale_in_configuratorset ."\n");
			
				for ( $j = 0; $j < $merkmale_in_configuratorset; $j++ ) {
					for ( $i = 0; $i < count ( $artikel['data']['details'] ); $i++ )
					{			
						$optionID=$artikel['data']['details'][$i]['configuratorOptions'][$j]['id'];			// Erste Option, zB Groesse
						$optionGroupID=$artikel['data']['details'][$i]['configuratorOptions'][$j]['id'];			// Erste Option, zB Groesse
						if ($optionID=="") {
							$unterabfrage="(SELECT id FROM s_article_configurator_options where name = '".$Artikel_Auspraegungen[$i]."' AND group_id=".$optionGroupID." ORDER BY id desc limit 1)"; // achtung evtl Einschraenkung auf group_ip noch erforderlich
							$query="REPLACE INTO s_article_configurator_set_option_relations values (".$configuratorSetId.",".$unterabfrage.")";
						} else {
							$query="REPLACE INTO s_article_configurator_set_option_relations values (".$configuratorSetId.",".$optionID.")";
						}						
						fwrite($dateihandle, "222 query $j/$i = ".$query." angelegt.");
						dmc_sql_query($query);					
					} // end for
				} // end for
				
				// optionen dem configuratorSetId zuordnen
				// 2. Varianten aktivieren - UPDATE s_articles_details SET active=1 WHERE articleID = 
				$query="UPDATE s_articles_details SET active=1 WHERE articleID =".$main_art_id;
				dmc_sql_query($query);
				// Shopware API Bug abfangen, weil teilweise Varianten mit bestimmten Artikelnummern nicht angelegt werden STEP 2
				$query="UPDATE s_articles_details SET ordernumber='".$Artikel_Artikelnr_orig."' WHERE ordernumber ='".$Artikel_Artikelnr."'";
				dmc_sql_query($query);
				$Artikel_Artikelnr = $Artikel_Artikelnr_orig;
				
				//fwrite($dateihandle, "249 Artikel_Preis=$Artikel_Preis, hauptartikel_nettopreis=".$artikel['data']['mainDetail']['prices'][0]['price']." \n.");
				if ($set_lowest_var_price_4_main && $Artikel_Preis<>"0") {
					$hauptartikel_nettopreis=$artikel['data']['mainDetail']['prices'][0]['price'];
					$varartikel_nettopreis=$Artikel_Preis / 1.19;
					if ($varartikel_nettopreis<$hauptartikel_nettopreis || $hauptartikel_nettopreis=="0") {
						$main_artdetails_id=dmc_get_details_id_by_artno($Artikel_Variante_Von);
				//		fwrite($dateihandle, "249 main_artdetails_id=".$main_artdetails_id." \n.");
						if ($main_artdetails_id>0) {
							$query="UPDATE s_articles_prices SET price='".$varartikel_nettopreis."' WHERE pricegroup='EK' AND `from`=1 AND articledetailsID=".$main_artdetails_id."";
							dmc_sql_query($query);	
						}
					}
				}
				
			} // end Variantenartikel
			// Insert in posts durchfuehren
			$mode='INSERTED';
			// Bildzuordnung -> Anlage über dmc_array_create_shopware
			if (DEBUGGER>=50) fwrite($dateihandle, " 207 Laufzeit = ".(microtime(true) - $beginn)."\n");
			/* if ($Artikel_Variante_Von == "" && !$SkipImages) {
				if (is_file('userfunctions/products/set_images_shopware.php')) include ('userfunctions/products/set_images_shopware.php');
				else include ('functions/products/set_images_shopware.php');
				set_images_shopware($Artikel_Artikelnr, $art_id, $Artikel_Bezeichnung, 1, $Artikel_Bilddatei, $dateihandle);
				if (DEBUGGER>=50) fwrite($dateihandle, " 132 Laufzeit = ".(microtime(true) - $beginn)."\n");
			} */
		} else {
			// ARTIKEL UPDATE
			fwrite($dateihandle, "ARTIKEL UPDATE ID $art_id  , Aktiv= $Aktiv\n");
			// Wenn $Aktiv= 'deaktivieren' - dann deaktivieren und abbruch
			if ($Aktiv=='.1.') $Aktiv=1;
			if (!is_numeric($Aktiv)) {
					fwrite($dateihandle, "Aktiv = .".$Aktiv.". \n");
				if ($Aktiv=='deaktivieren' || $Aktiv=='deactivate' || $Aktiv=='inactive') {
					fwrite($dateihandle, "Artikel ".$Artikel_Artikelnr." deaktivieren und Abbruch\n");
					$client->put('articles/'.$art_id, array(
						'active' => false
					));
					$mode="deactivated";
					// Rueckgabe
					$rueckgabe = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
					   "<STATUS>\n" .
					   "  <STATUS_INFO>\n" .
					   "    <ACTION>$action</ACTION>\n" .
					   "    <MESSAGE>OK</MESSAGE>\n" .
					   "    <MODE>$mode</MODE>\n" .
					   "    <ID>$art_id</ID>\n" .
					   "  </STATUS_INFO>\n" .
					   "</STATUS>\n\n";
					echo $rueckgabe;
					return;
				}
				// (vorab) loeschen? / delete (first)? / $Aktiv == 'loeschen' || $Aktiv == 'delete'
				if ($Aktiv=='delete' || $Aktiv=='loeschen' || $Aktiv=='delete') {
					// Shopware API BugFIX -> Bilder aus Media Ordner erst löschen, dann Produkt
					$ergebnis=$client->call('articles/'.$art_id, ApiClient::METHODE_GET);
					//echo "ArtID:".$ergebnis["data"]["id"]."<br />\n";
					// echo "Anz Bilder:".sizeof($ergebnis["data"]['images'])."<br />\n";
					// Bilder löschen
					foreach ($ergebnis["data"]['images'] as $bild)
						$client->call('media/'.$bild['mediaId'], ApiClient::METHODE_DELETE);
				 	
					fwrite($dateihandle, "Artikel ".$Artikel_Artikelnr." loeschen\n");
					$client->delete('articles/'.$art_id);
					$mode="deleted";
					// Rueckgabe
					$rueckgabe = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
					   "<STATUS>\n" .
					   "  <STATUS_INFO>\n" .
					   "    <ACTION>$action</ACTION>\n" .
					   "    <MESSAGE>OK</MESSAGE>\n" .
					   "    <MODE>$mode</MODE>\n" .
					   "    <ID>$art_id</ID>\n" .
					   "  </STATUS_INFO>\n" .
					   "</STATUS>\n\n";
					echo $rueckgabe;
					return;
				}			
			}
			
			// Artikel Array einlesen $sql_update_data_array 
			if (is_file('userfunctions/products/dmc_array_update_shopware.php')) include ('userfunctions/products/dmc_array_update_shopware.php');
			else include ('functions/products/dmc_array_update_shopware.php');

			fwrite($dateihandle, "\n AKTUALISIERUNG GrundDaten ... "); 
			$temp_cat_ids="";
			$tempi=0;
			for ( $i = 0; $i < count ( $Kategorie_IDs ); $i++ ) {
					// KategorieIds ergaenzen
					if ($Kategorie_IDs[$i] !="" && $Kategorie_IDs[$i] !="0") {
						fwrite($dateihandle, " KategorieIds ergaenzen ... ".$Kategorie_IDs[$i]); 
						// doppelte Zuordnung vermeiden
						if ( strpos($temp_cat_ids,"-".$Kategorie_IDs[$i]."-")===false) {
							$sql_update_data_array['categories'][$tempi]['id'] = $Kategorie_IDs[$i];			// !!! $Kategorie_ID wird geprueft
							$sql_update_categories_array['categories'][$tempi]['id'] = $sql_update_data_array['categories'][$tempi]['id']; // BUG Abfangen
							$tempi++;
						}
						$temp_cat_ids .= "-".$Kategorie_IDs[$i]."-";
					}
			}	
			//$ausgabe = print_r($sql_update_data_array, true);
			//fwrite($dateihandle, "sql_update_data_array: $ausgabe \n");
			$result=$client->call('articles/' . $art_id , ApiClient::METHODE_PUT, $sql_update_data_array);
			$ausgeben = print_r($result, true);
			//fwrite($dateihandle,  "Ergebnis: $ausgeben\n");
			$ausgeben = print_r($result, true);
			if ($ausgeben=='')
				$fehlermeldung="Eventuell Validation-Error";
			else
				$fehlermeldung="";
			//fwrite($dateihandle, "RESULT=".$ausgeben); 
			
			// FREIGEBEN, WENN Shopware Kategoriesierung nicht im UPDATE funktioniert $ausgeben = print_r($sql_update_categories_array, true);
			// fwrite($dateihandle,  "\nSHOPWARE BUG abfangen durch separates API Update nur mit KategorieIDs, da bei Gesamtupdate nicht berücksichtigt.\n");
			// fwrite($dateihandle,  "\sql_update_categories_array: $ausgeben\n");
			// $result=$client->call('articles/' . $art_id , ApiClient::METHODE_PUT, $sql_update_categories_array);
			// $ausgeben = print_r($result, true);
			
			//ALTERNATIV ZU API
			//	fwrite($dateihandle, "Einfaches Update art_id = $art_id bei Variantenartikel $Artikel_Artikelnr Aktiv=$Aktiv\n");
			// $query="UPDATE s_articles_details SET position='".$Artikel_Position."',active=".$Aktiv.", instock=".$Artikel_Menge." WHERE ordernumber ='".$Artikel_Artikelnr."'";
			// dmc_sql_query($query);
				
			// ggfls Hauptartikel deaktivieren, wenn alle Unterartikel keinen Bestand haben
			$cmd = "SELECT SUM(instock) AS Gesamtmenge FROM s_articles_details WHERE instock > 0 AND `articleID` = ".$main_art_id;
			$sql_query = dmc_db_query($cmd);
			if ($sql_result = dmc_db_fetch_array($sql_query)) {
					if ($sql_result['abmenge']<=0) {
						if (DEBUGGER>=1)  fwrite($dateihandle, "\n");
					$cmd = 	"UPDATE s_articles SET active = 0 " . 
							" WHERE id = ".$main_art_id;
					// $sql_query = dmc_db_query($cmd);
				}
			}
				
			// Bilder zuordnen
			if ($Artikel_Bilddatei != "") 
			{	
					// Standardartikel
					fwrite($dateihandle, "Bilder zuordnen ARTID $art_id \n");
					if (is_file('userfunctions/set_images_shopware.php')) include ('userfunctions/set_images_shopware.php');
					else include ('functions/set_images_shopware.php');
					// Alte Bilder löschen
					if ($sql_data_array['images'][0]['link']!='') {
						// DELETE FIRST
						$query="delete FROM `s_articles_img` where `articleID` =  ".$art_id;
						dmc_sql_query($query);
					}
					$ausgabe = print_r($sql_data_array, true);
					fwrite($dateihandle, "Bilder zuordnen #$art_id sql_data_array: $ausgabe \n");
					$result=$client->call('articles/' . $art_id , ApiClient::METHODE_PUT, $sql_data_array);
					$ausgabe = print_r($result, true);
					fwrite($dateihandle, "Bilder zuordnen #$art_id result: $ausgabe \n");
					// Hauptartikel
					if ($main_art_id<>'') {
						// Alte Bilder löschen
						fwrite($dateihandle, "Hauptartikel Bilder zuordnen main_art_id $main_art_id \n");
					
						if ($sql_data_array['images'][0]['link']!='') {
									// DELETE FIRST
									$query="delete FROM `s_articles_img` where `articleID` =  ".$main_art_id;
									dmc_sql_query($query);
								}
						if (is_file('userfunctions/set_images_shopware.php')) include ('userfunctions/set_images_shopware.php');
						else include ('functions/set_images_shopware.php');
						$result=$client->call('articles/' . $main_art_id , ApiClient::METHODE_PUT, $sql_data_array);
						$ausgabe = print_r($result, true);
						fwrite($dateihandle, "# result: $ausgabe \n");
						$ausgabe = print_r($sql_data_array, true);
						fwrite($dateihandle, "# sql_data_array: $ausgabe \n");

					}
			}

				
			// Bildzuordnung -> Anlage über dmc_array_update_shopware
			if (DEBUGGER>=50) fwrite($dateihandle, "\n 337 Laufzeit = ".(microtime(true) - $beginn));
			// Shopware API Bug abfangen,  Artikel aktivieren - UPDATE s_articles_details SET active=1 WHERE articleID = 
		//	$query="UPDATE s_articles_details SET active=$Aktiv WHERE articleID =".$art_id;
		//	dmc_sql_query($query);
			
					/* $query="update s_articles_prices set price = ".$Artikel_Preis1." where articleid=".$art_id." AND pricegroup='H'";
					//		fwrite($dateihandle, "330 query $i = ".$query." angelegt.");
					dmc_sql_query($query);					
					$query="update s_articles_prices set price = ".$Artikel_Preis2." where articleid=".$art_id." AND pricegroup='GH'";
					//		fwrite($dateihandle, "333 query $i = ".$query." angelegt.");
					dmc_sql_query($query);		
					// Gewicht auch fuer Varianten updaten
					$query="update s_articles_details set weight = ".$Artikel_Gewicht." where articleid=".$art_id." ";
					*/
				
			fwrite($dateihandle, " ** ARTIKEL UPDATE ENDE \n");
		}	// Artikel Update Ende
		
		$Kollektionskennzeichen = "";
		// Sonderfunktion "Vor Kundengruppen verstecken"
		// Kollektionskennzeichen A soll für Händler + Benutzergruppe  intern und Shop
		// K für alle
		if (substr($Kollektionskennzeichen,0,1) == "K") {
			fwrite($dateihandle, " **Freigeben fuer alle Kundengruppen \n");
			$query="delete from s_articles_avoid_customergroups WHERE articleID=".$art_id.";";
			dmc_sql_query($query);
		}
		if (substr($Kollektionskennzeichen,0,1) == "A") {
			fwrite($dateihandle, " **Freigeben fuer alle Kundengruppen \n");
			$query="delete from s_articles_avoid_customergroups WHERE articleID=".$art_id.";";
			dmc_sql_query($query);
			fwrite($dateihandle, " **Sperren fuer Kundengruppe EK - B2C DE \n");
			$query="replace into s_articles_avoid_customergroups (articleID, customergroupID) VALUES (".$art_id.",1);";
			dmc_sql_query($query);
			fwrite($dateihandle, " **Sperren fuer Kundengruppe B2C U - B2C US \n");
			$query="replace into s_articles_avoid_customergroups (articleID, customergroupID) VALUES (".$art_id.",4);";
			dmc_sql_query($query);
		}
		// N Nur Benutzergruppe  Team
		if (substr($Kollektionskennzeichen,0,1) == "N") {
			fwrite($dateihandle, " **Freigeben fuer alle Kundengruppen \n");
			$query="delete from s_articles_avoid_customergroups WHERE articleID=".$art_id.";";
			dmc_sql_query($query);
			fwrite($dateihandle, " **Sperren fuer Kundengruppe EK - B2C DE \n");
			$query="replace into s_articles_avoid_customergroups (articleID, customergroupID) VALUES (".$art_id.",1);";
			dmc_sql_query($query);
			fwrite($dateihandle, " **Sperren fuer Kundengruppe B2C U - B2C US \n");
			$query="replace into s_articles_avoid_customergroups (articleID, customergroupID) VALUES (".$art_id.",4);";
			dmc_sql_query($query);
			fwrite($dateihandle, " **Sperren fuer Kundengruppe 	H - B2B DE \n");
			$query="replace into s_articles_avoid_customergroups (articleID, customergroupID) VALUES (".$art_id.",2);";
			dmc_sql_query($query);
			fwrite($dateihandle, " **Sperren fuer Kundengruppe USA - B2B US \n");
			$query="replace into s_articles_avoid_customergroups (articleID, customergroupID) VALUES (".$art_id.",3);";
			dmc_sql_query($query);
		}
		
		
		// Sonderfunktion - Dynamische Ermittlung der Kategorien 2ter Ordnung zur Verwendung als Filter
		/*$value = 'description';
		$table = 's_categories';
		$where = 'id = (SELECT parent FROM s_categories WHERE id = (SELECT categoryID FROM s_articles_categories where articleID='.$art_id.'))';
		$FilterEigenschaft=dmc_sql_select_value($value, $table, $where);
		if (DEBUGGER>=50) fwrite($dateihandle, "FilterEigenschaft = ".$FilterEigenschaft." ergaenzen\n");
		$query="UPDATE s_articles SET filtergroupID = (SELECT id FROM s_filter WHERE name = '$FilterEigenschaft'";
		fwrite($dateihandle, "Filter query 1 = ".$query." \n");
		// dmc_sql_query($query);		
		*/ 
		
		// Wenn Variantenartikel, dann nicht als Hauptartikel anlegen
		/*
			if ($Artikel_Variante_Von!="") { 
				if (DEBUGGER>=50) fwrite($dateihandle, "1188 Wenn Variantenartikel, dann darf dieser nicht auch als Hauptartikel anlegt sein \n" );
				$query = "DELETE FROM s_articles_details WHERE ordernumber='".$Artikel_Artikelnr_orig."'";
				if (DEBUGGER>=50) fwrite($dateihandle, "$query  \n" );
				dmc_sql_query($query);
			} else {
				if (DEBUGGER>=50) fwrite($dateihandle, "1193 Kein Variantenartikel, da Artikel_Variante_Von=$Artikel_Variante_Von bzw Groessen=$Groessen  \n" );
			}
			*/
		
		if (DEBUGGER>=50) fwrite($dateihandle, "Artikel schreiben Ende Laufzeit = ".(microtime(true) - $beginn)."\n");
		
		// Rueckgabe
		if ($fehlermeldung=='')
			$rueckgabe = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
		   "<STATUS>\n" .
		   " <STATUS_INFO>\n" .
		   "  <ACTION>$action</ACTION>\n" .
		   "  <MESSAGE>OK</MESSAGE>\n" .
		   "  <MODE>$mode</MODE>\n" .
   		/ "    <success>1</success>\n" .
		   "  <ID>$art_id</ID>\n" .
		   " </STATUS_INFO>\n" .
		   "</STATUS>\n\n";
		else
			$rueckgabe = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
		   "<STATUS>\n" .
		   " <STATUS_INFO>\n" .
		   "  <ACTION>$action</ACTION>\n" .
		   "  <MESSAGE>error: $fehlermeldung</MESSAGE>\n" .
		   "  <MODE>$mode</MODE>\n" .
		  "    <success>-1</success>\n" .
		   "  <ID>$art_id</ID>\n" .
		   " </STATUS_INFO>\n" .
		   "</STATUS>\n\n";
		
		echo $rueckgabe;
		fwrite($dateihandle, "dmc_write_art - rueckgabe=".$rueckgabe."\n"); 
				
		return $art_id;	
	} // end function

	
?>
	