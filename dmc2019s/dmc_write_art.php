<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shops												*
*  dmc_write_art.php														*
*  Artikel schreiben														*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
12.03.12
- Funktion aus dmconenctor.php ausgegliedert (an Stelle von writeart() zu verwenden
21.06.12
- SONDERFUNKTION GAMBIO GX2 Filter setzen (inkludiert dmc_gambio_filter.php)
*/
 
	defined( 'VALID_DMC' ) or die( 'Direct Access to this location is not allowed.' );
	
	function dmc_write_art($ExportModus, $Artikel_ID, $Kategorie_ID, $Hersteller_ID,$Artikel_Artikelnr,$Artikel_Menge,
		$Artikel_Preis,$Artikel_Preis1,$Artikel_Preis2,$Artikel_Preis3,$Artikel_Preis4,$Artikel_Gewicht,$Artikel_Status,$Artikel_Steuersatz,
		$Artikel_Bilddatei,$Artikel_VPE,$Artikel_Lieferstatus,$Artikel_Startseite,$SkipImages,$Aktiv,$Aenderungsdatum,$Artikel_Variante_Von,$Artikel_Merkmal,
		$Artikel_Auspraegung,$Artikel_Bezeichnung ,$Artikel_Langtext ,$Artikel_Kurztext,$Artikel_Sprache,$Artikel_MetaText,$Artikel_MetaDescription,$Artikel_MetaKeyword,$Artikel_MetaUrl) {
	
		global $dateihandle, $action, $client, $Artikel_Herstellernummer;
		
		// Laufzeit
		$beginn = microtime(true); 
		
		fwrite($dateihandle, "dmc_write_art - ArtNr: $Artikel_Artikelnr (".date("l d of F Y h:i:s A").") merkmal $Artikel_Merkmal "); 
		fwrite($dateihandle, " - Bez: =".$Artikel_Bezeichnung." \n");
				
		if (is_file('userfunctions/products/dmc_art_functions.php')) include ('userfunctions/products/dmc_art_functions.php');
		else include ('functions/products/dmc_art_functions.php');
					// Mappings, zB Ermittlung von Artikel_EAN
		if (is_file('userfunctions/products/dmc_mappings.php')) include ('userfunctions/products/dmc_mappings.php');
		else include ('functions/products/dmc_mappings.php');
						
		$Artikel_ID=dmc_get_id_by_artno($Artikel_Artikelnr);
			fwrite($dateihandle, "Artikel_ID ".$Artikel_ID." mit Bild= $Artikel_Bilddatei\n");
					
	/*	if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false && 
			strpos(strtolower(SHOPSYSTEM), 'hhg') === false &&
			strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false &&
			strpos(strtolower(SHOPSYSTEM), 'presta') === false)
				$Artikel_Bilddatei =  dmc_prove_image_name($Artikel_Bilddatei, $Artikel_Artikelnr);
				*/
		// Wenn $Aktiv= 'deaktivieren' - dann deaktivieren und abbruch
		if ($Aktiv=='deaktivieren' || $Aktiv=='deactivate' || $Aktiv=='inactive') {
			fwrite($dateihandle, "Artikel ".$Artikel_Artikelnr." deaktivieren und Abbruch\n");
			dmc_deactivate_product($Artikel_Artikelnr);
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
			fwrite($dateihandle, "Artikel ".$Artikel_Artikelnr." loeschen\n");
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
			dmc_delete_product($Artikel_ID);
			return;
		}
		
		// GGfl vorhandene benutzerdefinierte Funktionen a
		if (is_file('userfunctions/products/dmc_userfunctions_a.php')) include ('userfunctions/products/dmc_userfunctions_a.php');
		fwrite($dateihandle, " 85 Laufzeit = ".(microtime(true) - $beginn)."\n");
		// KategorieID ermitteln oder uebergebene verwenden $Kategorie_IDs als array ist das ergebnis
		if ($Kategorie_ID!="")
			if (is_file('userfunctions/products/dmc_generate_cat_id.php')) include ('userfunctions/products/dmc_generate_cat_id.php');
			else include ('functions/products/dmc_generate_cat_id.php');
		 // Artikel laden
		if (DEBUGGER>=50) fwrite($dateihandle, " 91 (LZ = ".(microtime(true) - $beginn).") $Artikel_Merkmal \n");
		if ($Artikel_ID=="") { 
			$exists=0;	// Neuer Artikel
			if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) 
				$Artikel_ID = dmc_get_highest_id("id_product",TABLE_PRODUCTS)+1;
			else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) 
				$Artikel_ID = dmc_get_highest_id("virtuemart_product_id",TABLE_PRODUCTS)+1;
			else
				$Artikel_ID = dmc_get_highest_id("products_id",TABLE_PRODUCTS)+1;
			if (DEBUGGER>=1) 	fwrite($dateihandle, "Neuer Artikel wird angelegt mit ID = $Artikel_ID (LZ = ".(microtime(true) - $beginn).")\n");
		} else {
			$exists=1;	// Update
			if (DEBUGGER>=1) 	fwrite($dateihandle, "Artikel existent mit Artikel_ID=".$Artikel_ID." (LZ = ".(microtime(true) - $beginn).")\n");	
		}
		
		if (DEBUGGER>=1) {
			fwrite($dateihandle, "*********** ArtID ".$Artikel_ID." mit Preis ".$Artikel_Preis." fuer Kategorie $Kategorie_ID schreiben:\n");
			$i=1;
			if (DEBUGGER>=50) fwrite($dateihandle, "Artikel_Bezeichnung = ".$Artikel_Bezeichnung." mit Desc(Anfang)=".substr($Artikel_Text,0,200)."\n");	
			if ($Artikel_Variante_Von != ""){
				if (DEBUGGER>=50) fwrite($dateihandle, "Artikel_Variante_Von - ".$Artikel_Variante_Von." mit Ausprägung: ".$Artikel_Auspraegung." size=$AuspraegungenID[0] bzw $Merkmale[0] und color=$AuspraegungenID[1] bze $Merkmale[1]");
				fwrite($dateihandle, "\n");		
			}
			fwrite($dateihandle, "**************\n");
		}		
		
		// GGfl vorhandene benutzerdefinierte Funktionen b
		if (is_file('userfunctions/products/dmc_userfunctions_b.php')) include ('userfunctions/products/dmc_userfunctions_b.php');
		
		// Ggfls Arrays fuer Kundengruppenpreise fuellen / Fill customer group price arrays
		// if (is_file('userfunctions/products/dmc_group_prices_arrays.php')) include ('userfunctions/products/dmc_group_prices_arrays.php');
		// else include ('functions/products/dmc_group_prices_arrays.php');
		
		// Variantenartikel -> Artikel_Variante_Von ist dann ebenfalls die Artikelnummer des Hauptartikels 
		if ($Artikel_Variante_Von != ""){
			// Art-ID des Hauptartikels ermitteln
			$Artikel_Variante_Von_id=dmc_get_id_by_artno($Artikel_Variante_Von);

			if ($Artikel_Variante_Von_id != ""){
				fwrite($dateihandle, "Artikel_ID des Hauptartikels - ".$Artikel_Variante_Von_id." \n");	
			} else {
				fwrite($dateihandle, "Hauptartikel existiert nicht -> Variante kann nicht angelegt werden. ABBRUCH. \n");	
				// ABBRUCH
				exit;
			}
		} // end if ($Artikel_Variante_Von != "")
    
		// Hersteller_ID ermitteln -> bei Bedarf Hersteller anlegen
		if ($Hersteller_ID != "" && !is_numeric($Hersteller_ID)) {
 			if (strpos(strtolower(SHOPSYSTEM), 'shopware') === false ) 						// Bei Shopware wird Bezeichnung direkt mit dem Artikel angelegt
				$Hersteller_ID=dmc_get_manufacturer_id($Hersteller_ID,$no_of_languages);
		}

		// NUR WENN KEIN VARIANTENARTIKEL
		if ($Artikel_Variante_Von == "" 
			|| strpos(strtolower(SHOPSYSTEM), 'veyton') !== false
			|| strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false){
				if (DEBUGGER>=1 && strpos(strtolower(SHOPSYSTEM), 'veyton') === false) 
				fwrite($dateihandle, "112 - KEIN VARIANTENARTIKEL (LZ = ".(microtime(true) - $beginn).")\n");
				if (DEBUGGER>=1 && strpos(strtolower(SHOPSYSTEM), 'veyton') !== false && $Artikel_Variante_Von == "") 
				fwrite($dateihandle, "115 - VEYTON - KEIN VARIANTENARTIKEL (LZ = ".(microtime(true) - $beginn).")\n");
				if (DEBUGGER>=1 && strpos(strtolower(SHOPSYSTEM), 'veyton') !== false && $Artikel_Variante_Von != "") 
				fwrite($dateihandle, "117 - VEYTON - VARIANTENARTIKEL (LZ = ".(microtime(true) - $beginn).")\n");
				if (DEBUGGER>=1 && strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false && $Artikel_Variante_Von == "") 
				fwrite($dateihandle, "155 - virtuemart - KEIN VARIANTENARTIKEL (LZ = ".(microtime(true) - $beginn).")\n");
				if (DEBUGGER>=1 && strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false && $Artikel_Variante_Von != "") 
				fwrite($dateihandle, "157 - virtuemart - VARIANTENARTIKEL (LZ = ".(microtime(true) - $beginn).")\n");
			if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
				// Entweder bei Übertragung Conf zunächst de Slaves löschen, oder deaktivieren, damit nur vorhandene angezeigt werden
				$delete_slaves_first=false;
				$deactivate_slaves_first=true;
				if ($delete_slaves_first)
					if (is_file('userfunctions/products/dmc_delete_veyton_slaves.php')) include ('userfunctions/products/dmc_delete_veyton_slaves.php');
					else include ('functions/products/dmc_delete_veyton_slaves.php');
				if ($deactivate_slaves_first)
					if (is_file('userfunctions/products/dmc_deactivate_veyton_slaves.php')) include ('userfunctions/products/dmc_deactivate_veyton_slaves.php');
					else include ('functions/products/dmc_deactivate_veyton_slaves.php');
			}
			// NEUE Artikel
			if ($exists==0)
			{
				// Array nur komplett füllen, wenn ein Insert oder ein Komplettes Update
				// durchgeführt wird (und nicht nur der Preis)
				if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
					if (is_file('userfunctions/products/dmc_array_create_veyton.php')) include ('userfunctions/products/dmc_array_create_veyton.php');
					else include ('functions/products/dmc_array_create_veyton.php');
				} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
					if (is_file('userfunctions/products/dmc_array_create_presta.php')) include ('userfunctions/products/dmc_array_create_presta.php');
					else include ('functions/products/dmc_array_create_presta.php');
				} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
					if (is_file('userfunctions/products/dmc_array_create_virtuemart.php')) include ('userfunctions/products/dmc_array_create_virtuemart.php');
					else include ('functions/products/dmc_array_create_virtuemart.php');
				} else {
					// Standard
					if (is_file('userfunctions/products/dmc_array_create_standard.php')) 
					{ 	
						include ('userfunctions/products/dmc_array_create_standard.php');
					} else {  
						include ('functions/products/dmc_array_create_standard.php');
					}
				} // end if SHOPSYSTEM spezifische standards
				
				// GGfl vorhandene benutzerdefinierte Funktionen b
				if (is_file('userfunctions/products/dmc_userfunctions_c.php')) include ('userfunctions/products/dmc_userfunctions_c.php');
		
				// Weitere (shopspezifische wie HHG gambiogx ) Details ( wie TEMPLATE etc) fuer Produkt Anlage 
				if (is_file('userfunctions/products/dmc_array_create_additional.php')) include ('userfunctions/products/dmc_array_create_additional.php');
				else include ('functions/products/dmc_array_create_additional.php');
				//fwrite($dateihandle, "188");		
				// Insert durchfuehren
				if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false ) {
					// Shopware API
					$response=$client->call('articles', ApiClient::METHODE_POST, $sql_data_array);
					$Artikel_ID=$response['data']['id'];
					fwrite($dateihandle, "Shopware AIP Produktanlage mit ID: ".$Artikel_ID." . \n");
				} else {
					// Standard auf Datenbank
					$mode='INSERTED';
				//	fwrite($dateihandle, "196 - INSERT ... ");
					dmc_sql_insert_array(TABLE_PRODUCTS, $sql_data_array);
					$Artikel_ID=dmc_get_id_by_artno($Artikel_Artikelnr);
					fwrite($dateihandle, "Artikel angelegt mit Artikel_ID ".$Artikel_ID." \n");
					//fwrite($dateihandle, "erfolgt ... \n");
					if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false &&
						strpos(strtolower(SHOPSYSTEM), 'presta') === false ) { 
						// Bei virtuemart UND PRESTA wurde die neue Artikel_ID im Vorfeld ermittelt (dmc_array_create_virtuemart.php)
						$sql_product_details_array['products_id']=$Artikel_ID;
						// dmc_sql_insert_array(TABLE_PRODUCTS_DESCRIPTION, $sql_product_details_array);
					} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false ) { 
						fwrite($dateihandle, "230 - Artikel_ID =".$Artikel_ID." (hersteller = $Hersteller_ID) ... ");
						// Hersteller dem Artikel zuordnen
						if ($Hersteller_ID!=0 && $Hersteller_ID !='') {
							$insert_sql_data= array(
								'virtuemart_product_id' => $Artikel_ID,
								'virtuemart_manufacturer_id' => $Hersteller_ID
							);
							dmc_sql_insert_array(DB_PREFIX.'virtuemart_product_manufacturers', $insert_sql_data);	
						}
					} 
				}						
				// GGfl vorhandene benutzerdefinierte Funktionen b
				if (is_file('userfunctions/products/dmc_userfunctions_d.php')) include ('userfunctions/products/dmc_userfunctions_d.php');
	
				if (strpos(strtolower(SHOPSYSTEM), 'gambiogx2') !== false OR (strtolower(SHOPSYSTEM) == 'gambio' && (SHOPSYSTEM_VERSION=='gx' || SHOPSYSTEM_VERSION=='gx2'))) {
					// Gambio GX2 Feld Mengeneinheit updaten
					//$update['products_id']=$Artikel_ID;
					//$update['quantity_unit_id']=$sql_data_array['products_vpe'];
					//dmc_sql_insert_array('products_quantity_unit', $update);
				}
	
				// Kundengruppenpreise setzen - Keine Kundenpreise in ZENCART virtuemart / todo presta
				$quantity=1;
				if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false 
				&& strpos(strtolower(SHOPSYSTEM), 'presta') === false 
				&& strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false
				&& strpos(strtolower(SHOPSYSTEM), 'osc') === false)  {
					if (is_file('userfunctions/products/dmc_set_group_prices.php')) include ('userfunctions/products/dmc_set_group_prices.php');
					else include ('functions/products/dmc_set_group_prices.php');
				} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
					fwrite($dateihandle, "237 Artikel-create Preise virtuemart (LZ = ".(microtime(true) - $beginn).")\n");
					// Update Preis(e) Virtuemart
					// product price							
					// Standardpreis=1 mit virtuemart_shoppergroup_id=1
					// $kundeNgruppe=1; //  mit $Artikel_Preis
					$kundeNgruppe=0; //  mit 0 fuer alle Gruppen
					$sql_product_price_array = array(
						// virtuemart_product_price_id
						'virtuemart_product_id' => $Artikel_ID,
				//		'product_price' => $Artikel_Preis,
						'virtuemart_shoppergroup_id' => $kundeNgruppe,	 
						//	override 	product_override_price
						'product_tax_id' => $Artikel_Steuersatz,
						// product_discount_id 	product_length 	product_width 	product_height 	product_lwh_uom 	product_url
						'product_currency' => '47',	// CH im Standard = 27, EUR im Standard  = 47
						// product_price_publish_up 	product_price_publish_down
						'price_quantity_start' => 0,
						'price_quantity_end' => 0,
						'created_on' => 'now()',
						'modified_on' => 'now()'
						// created_by 	modified_by 	locked_on 	locked_by					
					);
					if ($Artikel_Preis!='' && $Artikel_Preis>'0')
						$sql_product_price_array['product_price'] = $Artikel_Preis;
					else
						$sql_product_price_array['product_price'] = null;
					
					dmc_sql_insert_array(TABLE_PRODUCTS_PRICES, $sql_product_price_array);
					/*if ($Artikel_Preis2!='' && $Artikel_Preis2>0) {
						$kundeNgruppe=2;
						$sql_product_price_array['virtuemart_shoppergroup_id']= $kundeNgruppe;
						$sql_product_price_array['product_price']=$Artikel_Preis2;
						dmc_sql_insert_array(TABLE_PRODUCTS_PRICES, $sql_product_price_array);
					}
					if ($Artikel_Preis3!='' && $Artikel_Preis3>0) {
						$kundeNgruppe=3;
						$sql_product_price_array['virtuemart_shoppergroup_id']= $kundeNgruppe;
						$sql_product_price_array['product_price']=$Artikel_Preis3;
						dmc_sql_insert_array(TABLE_PRODUCTS_PRICES, $sql_product_price_array);
					}
					if ($Artikel_Preis4!='' && $Artikel_Preis4>0) {
						$kundeNgruppe=4;
						$sql_product_price_array['virtuemart_shoppergroup_id']= $kundeNgruppe;
						$sql_product_price_array['product_price']=$Artikel_Preis4;
						dmc_sql_insert_array(TABLE_PRODUCTS_PRICES, $sql_product_price_array);
					} */
					
				}
	
				// Insert Artikelbeschreibung
				if (strpos(strtolower(SHOPSYSTEM), 'presta') === false)
					if (is_file('userfunctions/products/dmc_set_art_desc.php')) include ('userfunctions/products/dmc_set_art_desc.php');
					else include ('functions/products/dmc_set_art_desc.php');
					
				// Insert Artikelbeschreibung -> Sonderfall Presta
				if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)
					if (is_file('userfunctions/products/dmc_set_art_desc_presta.php')) include ('userfunctions/products/dmc_set_art_desc_presta.php');
					else include ('functions/products/dmc_set_art_desc_presta.php');
		
				// Insert oder Update Varianteninformationen bei Virtuemart
				if ($Artikel_Variante_Von != "" && strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false && $Artikel_Auspraegung!= "") {
					// customfield_params muessen in `jo340_virtuemart_product_customfields gesetzt oder aktualisiert werden`
					$Vater_Artikel_ID=dmc_get_id_by_artno($Artikel_Variante_Von);
					// Überprüfen ob customfield_params bereits angelegt sind
					$cmd = 	"SELECT customfield_params from ".DB_PREFIX."virtuemart_product_customfields".
							" WHERE virtuemart_product_id = '$Vater_Artikel_ID'";
					$sql_query = dmc_db_query($cmd);
		     	 	if ($sql_result = dmc_db_fetch_array($sql_query))
					{      
						// customfield_params von bestehendem Artikel ermitteln, 
						// zB usecanonical=0|selectoptions=[{"voption":"clabels","clabel":"Größe","values":"26\r\n28\r\n30\r\n31\r\n32"}]|clabels=0|options={"1022":["26"],"1023":["28"],"1024":["30"],"1025":["31"],"1026":["31"],"1027":["32"]}|
						$customfield_params = $sql_result['customfield_params'];
						fwrite($dateihandle, "\n312 - virtuemart customfield_params: $customfield_params\n");
						// Existiert
						$exists_customfield_params = 1;
						// Pruefen ob Artikel-Größe - bzw der Variantenartikel -  bereits eingetragen ist
						if (strpos($customfield_params,"\"".$Artikel_ID."\"")) {
							// Variantenartikel bereits eingetragen
							fwrite($dateihandle, "Variantenartikel id $Artikel_ID bereits eingetragen in  virtuemart customfield_params: $customfield_params \n");
						} else {
							// Variantenartikel eingetragen
							// Einfuegen als value vor die Position des "} hinter values
							$customfield_params = substr($customfield_params,0,strpos($customfield_params,"\"}",strpos($customfield_params,"values"))).
													'\r\n'.$Artikel_Auspraegung.
													substr($customfield_params,strpos($customfield_params,"\"}",strpos($customfield_params,"values")),10024);
							// Einfuegen als option vor die Position des "} hinter values
							$customfield_params = substr($customfield_params,0,strpos($customfield_params,"}|",strpos($customfield_params,"options"))).
													',"'.$Artikel_ID.'":["'.$Artikel_Auspraegung.'"]'.
													substr($customfield_params,strpos($customfield_params,"}|",strpos($customfield_params,"options")),10024);					
							fwrite($dateihandle, "\nvirtuemart customfield_params neu: $customfield_params\n");
							$query="UPDATE ".DB_PREFIX."virtuemart_product_customfields SET customfield_params='".str_replace("\\", "\\\\", $customfield_params)."' WHERE virtuemart_product_id = '$Vater_Artikel_ID'";
							dmc_sql_query($query);
						}
					} else {
						// Eintrag noch nicht vorhanden, daher neu anhand Skelett erstellen
						fwrite($dateihandle, "\n334 - virtuemart customfield_params: $customfield_params\n");
					//	if ($Artikel_Merkmal=="") 
							$Artikel_Merkmal='Gr\\\\u00f6\\\\u00dfe';
						$customfield_params = 'usecanonical=0|selectoptions=[{"voption":"clabels","clabel":"'.$Artikel_Merkmal.'","values":"'.$Artikel_Auspraegung.'"}]|clabels=0|options={"'.$Artikel_ID.'":["'.$Artikel_Auspraegung.'"]}|';
						$query=	"INSERT INTO ".DB_PREFIX."virtuemart_product_customfields (virtuemart_product_id,virtuemart_custom_id,customfield_params) ".
								"VALUES (".$Vater_Artikel_ID.", 3, '".$customfield_params."')";
						dmc_sql_query($query);
						fwrite($dateihandle, "virtuemart customfield_params eingetragen $query \n");
					} //endif
				}
					
				// SEO Tabellen fuellen
				if (is_file('userfunctions/products/dmc_set_art_seo.php')) include ('userfunctions/products/dmc_set_art_seo.php');
				else include ('functions/products/dmc_set_art_seo.php');
				
			} // Ende insert Artikel
			elseif ($exists==1) // Update Artikel
			{
				// fwrite($dateihandle, "237 Artikel-UPDATE (LZ = ".(microtime(true) - $beginn).")\n");
			
				// Artikel Array fuellen
				if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {	// presta
					if (is_file('userfunctions/products/dmc_array_update_presta.php')) include ('userfunctions/products/dmc_array_update_presta.php');
					else include ('functions/products/dmc_array_update_presta.php');
				} else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) { // veyton
					if (is_file('userfunctions/products/dmc_array_update_veyton.php')) include ('userfunctions/products/dmc_array_update_veyton.php');
					else include ('functions/products/dmc_array_update_veyton.php');
				} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) { // veyton
					if (is_file('userfunctions/products/dmc_array_update_virtuemart.php')) include ('userfunctions/products/dmc_array_update_virtuemart.php');
					else include ('functions/products/dmc_array_update_virtuemart.php');
				}	else { // Standard
					if (is_file('userfunctions/products/dmc_array_update_standard.php')) include ('userfunctions/products/dmc_array_update_standard.php');
					else include ('functions/products/dmc_array_update_standard.php');
				} // end Standard
			
				// fwrite($dateihandle, "254 Artikel-UPDATE (LZ = ".(microtime(true) - $beginn).")\n");
			
				// Weitere (shopspezifische wie HHG gambiogx ) Details ( wie TEMPLATE, VPE etc) fuer Produkt Anlage 
				if (is_file('userfunctions/products/dmc_array_update_additional.php')) include ('userfunctions/products/dmc_array_update_additional.php');
				else include ('functions/products/dmc_array_update_additional.php');
				$mode='UPDATED';
				// fwrite($dateihandle, "260 Artikel-UPDATE (LZ = ".(microtime(true) - $beginn).")\n");
			  
				// Artikeleintraege updaten
				if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
					dmc_sql_update_array(TABLE_PRODUCTS, $sql_data_array, "id_product = '$Artikel_ID'");
				} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
				    fwrite($dateihandle, "381 Artikel-UPDATE virtuemart (LZ = ".(microtime(true) - $beginn).") ...\n");
					dmc_sql_update_array(TABLE_PRODUCTS, $sql_data_array, "virtuemart_product_id = '$Artikel_ID'");
					// Hersteller dem Artikel zuordnen
					if ($Hersteller_ID!=0 && $Hersteller_ID !='') {
						fwrite($dateihandle, "382 - Artikel_ID =".$Artikel_ID." (hersteller = $Hersteller_ID) ... ");
						$desc_query = dmc_db_query($cmd);
						if (($desc = dmc_db_fetch_array($desc_query)))
						{
							$cmd = 	"DELETE FROM " . DB_PREFIX.'virtuemart_product_manufacturers' . " WHERE " .
									"virtuemart_product_id='$Artikel_ID'";
							dmc_db_query($cmd);
						}
						// Hersteller dem Artikel zuordnen
						$insert_sql_data= array(
							   // id
							   'virtuemart_product_id' => $Artikel_ID,
								'virtuemart_manufacturer_id' => $Hersteller_ID
						);
						dmc_sql_insert_array(DB_PREFIX.'virtuemart_product_manufacturers', $insert_sql_data);
					}
				} else {
					dmc_sql_update_array(TABLE_PRODUCTS, $sql_data_array, "products_id = '$Artikel_ID'");
				}
				// fwrite($dateihandle, "270 Artikel-UPDATE (LZ = ".(microtime(true) - $beginn).")\n");
			
				if (strpos(strtolower(SHOPSYSTEM), 'gambiogx2') !== false OR (strtolower(SHOPSYSTEM) == 'gambio' && (SHOPSYSTEM_VERSION=='gx2'))) {
					// Gambio GX2 Feld Mengeneinheit updaten
					//$update['quantity_unit_id']=$sql_data_array['products_vpe'];
					//dmc_sql_update_array('products_quantity_unit', $update, "products_id = '$Artikel_ID'");
				}
				// fwrite($dateihandle, "277 Artikel-UPDATE (LZ = ".(microtime(true) - $beginn).")\n");
				
				// Keine Kundenpreise in ZENCART auf xt_products_price_group_1
				// Kundengruppenpreise aktualisieren - Keine Kundenpreise in ZENCART / todo presta
				if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false && 
					strpos(strtolower(SHOPSYSTEM), 'hhg') === false &&
					strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false &&
					strpos(strtolower(SHOPSYSTEM), 'presta') === false
					&& strpos(strtolower(SHOPSYSTEM), 'osc') === false) {
					if (is_file('userfunctions/products/dmc_update_group_prices.php')) include ('userfunctions/products/dmc_update_group_prices.php');
					else include ('functions/products/dmc_update_group_prices.php');
				} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
					// fwrite($dateihandle, "289 Artikel-UPDATE Preise virtuemart (LZ = ".(microtime(true) - $beginn).")\n");
					// Update Preis(e) Virtuemart
					// product price							
					// Standardpreis=1 mit virtuemart_shoppergroup_id=1
					 $sql_product_price_array = array(
						//'product_price' => $Artikel_Preis,
						'modified_on' => 'now()'
					);
					if ($Artikel_Preis!='' && $Artikel_Preis>0)
						$sql_product_price_array['product_price'] = $Artikel_Preis;
					else 
						$sql_product_price_array['product_price'] = null;
					
					//$kundeNgruppe=1; //  mit $Artikel_Preis
					$kundeNgruppe=0; //  mit 0 fuer alle Gruppen
					dmc_sql_update_array(TABLE_PRODUCTS_PRICES, $sql_product_price_array, "virtuemart_product_id = '$Artikel_ID' AND virtuemart_shoppergroup_id=$kundeNgruppe AND price_quantity_start=0 AND price_quantity_end=0");
					/*if ($Artikel_Preis2!='' && $Artikel_Preis2>0) {
						$kundeNgruppe=2;
						$sql_product_price_array['product_price']=$Artikel_Preis2;
						dmc_sql_update_array(TABLE_PRODUCTS_PRICES, $sql_product_price_array, "virtuemart_product_id = '$Artikel_ID' AND virtuemart_shoppergroup_id=$kundeNgruppe AND price_quantity_start=0 AND price_quantity_end=0");
					}
					if ($Artikel_Preis3!='' && $Artikel_Preis3>0) {
						$kundeNgruppe=3;
						$sql_product_price_array['product_price']=$Artikel_Preis3;
						dmc_sql_update_array(TABLE_PRODUCTS_PRICES, $sql_product_price_array, "virtuemart_product_id = '$Artikel_ID' AND virtuemart_shoppergroup_id=$kundeNgruppe AND price_quantity_start=0 AND price_quantity_end=0");
					}
					if ($Artikel_Preis4!='' && $Artikel_Preis4>0) {
						$kundeNgruppe=4;
						$sql_product_price_array['product_price']=$Artikel_Preis4;
						dmc_sql_update_array(TABLE_PRODUCTS_PRICES, $sql_product_price_array, "virtuemart_product_id = '$Artikel_ID' AND virtuemart_shoppergroup_id=$kundeNgruppe AND price_quantity_start=0 AND price_quantity_end=0");
					}*/
					
				}
				
				// Update Artikelbeschreibung
				if (UPDATE_DESC && strpos(strtolower(SHOPSYSTEM), 'presta') === false && strpos(strtolower(SHOPSYSTEM), 'osc') === false)
					if (is_file('userfunctions/products/dmc_update_art_desc.php')) include ('userfunctions/products/dmc_update_art_desc.php');
					else include ('functions/products/dmc_update_art_desc.php');
				// fwrite($dateihandle, "294 Artikel-UPDATE (LZ = ".(microtime(true) - $beginn).")\n");
				
				// Update Artikelbeschreibung -> Sonderfall Presta
				if (UPDATE_DESC && strpos(strtolower(SHOPSYSTEM), 'presta') !== false && strpos(strtolower(SHOPSYSTEM), 'osc') === false)
					if (is_file('userfunctions/products/dmc_update_art_desc_presta.php')) include ('userfunctions/products/dmc_update_art_desc_presta.php');
					else include ('functions/products/dmc_update_art_desc_presta.php');
							
				// SEO Tabellen updaten
				if (is_file('userfunctions/products/dmc_update_art_seo.php')) include ('userfunctions/products/dmc_update_art_seo.php');
				else include ('functions/products/dmc_update_art_seo.php');				
				// fwrite($dateihandle, "304 Artikel-UPDATE (LZ = ".(microtime(true) - $beginn).")\n");
				if (DEBUGGER>=50) fwrite($dateihandle, "460 $Artikel_Variante_Von $Artikel_Auspraegung");
				// Insert oder Update Varianteninformationen bei Virtuemart
				if ($Artikel_Variante_Von != "" && strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false && $Artikel_Auspraegung!= ""){
					// customfield_params muessen in `jo340_virtuemart_product_customfields gesetzt oder aktualisiert werden`
					$Vater_Artikel_ID=dmc_get_id_by_artno($Artikel_Variante_Von);
					// Überprüfen ob customfield_params bereits angelegt sind
					$cmd = 	"SELECT customfield_params FROM ".DB_PREFIX."virtuemart_product_customfields".
							" WHERE virtuemart_product_id = '$Vater_Artikel_ID'";
					$sql_query = dmc_db_query($cmd);
		     	 	if ($sql_result = dmc_db_fetch_array($sql_query))
					{      
						// customfield_params von bestehendem Artikel ermitteln, 
						// zB usecanonical=0|selectoptions=[{"voption":"clabels","clabel":"Größe","values":"26\r\n28\r\n30\r\n31\r\n32"}]|clabels=0|options={"1022":["26"],"1023":["28"],"1024":["30"],"1025":["31"],"1026":["31"],"1027":["32"]}|
						$customfield_params = $sql_result['customfield_params'];
						if (DEBUGGER>=50) fwrite($dateihandle, "virtuemart customfield_params: $customfield_params\n");
						// Existiert
						$exists_customfield_params = 1;
						// Pruefen ob Artikel-Größe - bzw der Variantenartikel -  bereits eingetragen ist
						// Pruefen ob Artikel-Größe - bzw der Variantenartikel -  bereits eingetragen ist
						if (strpos($customfield_params,"\"".$Artikel_ID."\"")) {
							// Variantenartikel bereits eingetragen
							fwrite($dateihandle, "494 Variantenartikel id $Artikel_ID bereits eingetragen in  virtuemart customfield_params: $customfield_params \n");
							// Variantenartikel bereits eingetragen
						} else {
							// Variantenartikel eingetragen
							// Einfuegen als value vor die Position des "} hinter values
							$customfield_params = substr($customfield_params,0,strpos($customfield_params,"\"}",strpos($customfield_params,"values"))).
													'\r\n'.$Artikel_Auspraegung.
													substr($customfield_params,strpos($customfield_params,"\"}",strpos($customfield_params,"values")),1024);
							// Einfuegen als option vor die Position des "} hinter values
							$customfield_params = substr($customfield_params,0,strpos($customfield_params,"}|",strpos($customfield_params,"options"))).
													',"'.$Artikel_ID.'":["'.$Artikel_Auspraegung.'"]'.
													substr($customfield_params,strpos($customfield_params,"}|",strpos($customfield_params,"options")),1024);					
							if (DEBUGGER>=50) fwrite($dateihandle, "virtuemart customfield_params neu: $customfield_params\n");
							$query="UPDATE ".DB_PREFIX."virtuemart_product_customfields SET customfield_params='".str_replace("\\", "\\\\", $customfield_params)."' WHERE virtuemart_product_id = '$Vater_Artikel_ID'";
							dmc_sql_query($query);
						}
					} else {
						// Eintrag noch nicht vorhanden, daher neu anhand Skelett erstellen
						if (DEBUGGER>=50) fwrite($dateihandle, "virtuemart customfield_params: $customfield_params\n");
						//if ($Artikel_Merkmal=="") 
							$Artikel_Merkmal='Gr\\\\u00f6\\\\u00dfe';
						$customfield_params = 'usecanonical=0|selectoptions=[{"voption":"clabels","clabel":"'.$Artikel_Merkmal.'","values":"'.$Artikel_Auspraegung.'"}]|clabels=0|options={"'.$Artikel_ID.'":["'.$Artikel_Auspraegung.'"]}|';
						$query=	"INSERT INTO ".DB_PREFIX."virtuemart_product_customfields (virtuemart_product_id,virtuemart_custom_id,customfield_params) ".
								"VALUES (".$Vater_Artikel_ID.", 3, '".$customfield_params."')";
						dmc_sql_query($query);
						if (DEBUGGER>=50) fwrite($dateihandle, "virtuemart customfield_params eingetragen \n");
					} //endif
				}
				
				// GGfl vorhandene benutzerdefinierte Funktionen e
				if (is_file('userfunctions/products/dmc_userfunctions_e.php')) include ('userfunctions/products/dmc_userfunctions_f.php');
		
			} // Ende Artikel Update 
			//fwrite($dateihandle, "511 Artikel-UPDATE (LZ = ".(microtime(true) - $beginn).")\n");
			
			// GGfl vorhandene benutzerdefinierte Funktionen f
			if (is_file('userfunctions/products/dmc_userfunctions_f.php')) include ('userfunctions/products/dmc_userfunctions_f.php');
	
			if (DEBUGGER>=50) fwrite($dateihandle, "516 Laufzeit = ".(microtime(true) - $beginn)."\n");
			
			// Kategorien zuordnen (nur fuer neue Artikel)
			if ($Kategorie_ID!="" && $Kategorie_ID!="0" && ($exists==0 || UPDATE_PROD_TO_CAT==1) ) {
				fwrite($dateihandle, " 520 PROD_TO_CAT, da UPDATE_PROD_TO_CAT=".UPDATE_PROD_TO_CAT." bzw exists=".$exists." \n");
				if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {						// Presta
					if (is_file('userfunctions/products/dmc_set_art_cat_presta.php')) include ('userfunctions/products/dmc_set_art_cat_presta.php');
						else include ('functions/products/dmc_set_art_cat_presta.php');
				} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {			// virtuemart
					if (is_file('userfunctions/products/dmc_set_art_cat_virtuemart.php')) include ('userfunctions/products/dmc_set_art_cat_virtuemart.php');
						else include ('functions/products/dmc_set_art_cat_virtuemart.php');
				} else { 																		// Standard
					if (is_file('userfunctions/products/dmc_set_art_cat_standard.php')) include ('userfunctions/products/dmc_set_art_cat_standard.php');
					else include ('functions/products/dmc_set_art_cat_standard.php');
				} // end if 
			}	
		   
		   // Shop Details - Nur Presta product_shop
			if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {	// Presta
				if (is_file('userfunctions/products/dmc_set_art_shop_presta.php')) include ('userfunctions/products/dmc_set_art_shop_presta.php');
				else include ('functions/products/dmc_set_art_shop_presta.php');
				// Ggfls Merkmale als Eigenschaften fuer Presta Hauptprodukte
				if ($Artikel_Auspraegung!='')
					if (is_file('userfunctions/products/dmc_presta_eigenschaften.php')) include ('userfunctions/products/dmc_presta_eigenschaften.php');
					else include ('functions/products/dmc_presta_eigenschaften.php');
				// GGfls Herstellernummer, Lieferantennummer setzen
				// Lieferant = 	$lieferant="TALKSKY Großhandels GmbH"; 
				// $lieferantID=1;
				//  id_product_supplier	 id_product	 id_product_attribute	 id_supplier	 product_supplier_reference	 product_supplier_price_te	 id_currency
				// $cmd = 	"REPLACE INTO ".DB_PREFIX."product_supplier (id_product, id_product_attribute, id_supplier, product_supplier_reference, product_supplier_price_te, id_currency) VALUES  ($Artikel_ID,0,$lieferantID,'$Artikel_Herstellernummer',$Artikel_Preis,1) ";
				// if (DEBUGGER>=1) fwrite($dateihandle, "Artikel_Herstellernummer setzen ".$cmd."\n");
				// $sql_query = dmc_db_query($cmd);
					// Lieferant setzen
			//	$cmd = 	"UPDATE ".DB_PREFIX."product SET id_supplier=".$lieferantID.
			//			" WHERE id_product = '$Artikel_ID'";
			//	$sql_query = dmc_db_query($cmd);
			}
			
			// SONDERFALL  VARIANTENARTIKEL VEYTON
			if ($exists==0 && $Artikel_Variante_Von != "" && strpos(strtolower(SHOPSYSTEM), 'veyton') !== false)
			{
				// veyton funktionen
				if (DEBUGGER>=1) fwrite($dateihandle, "veyton set_configurable_product \n");
				if (is_file('userfunctions/veyton_set_conf_product.php')) include_once ('userfunctions/veyton_set_conf_product.php');
					else include_once ('functions/veyton_set_conf_product.php');
				if (DEBUGGER>=1) fwrite($dateihandle, "veyton set_configurable_product end \n");
			}
			
			// Nicht ShopAktive Artikel löschen
			if (DELETE_INACTIVE_PRODUCT && strpos(strtolower(SHOPSYSTEM), 'presta') === false)
			  if ($Aktiv=="False" || $Aktiv=="false" || $Aktiv=='0')
			  {
				if (strpos(strtolower(SHOPSYSTEM), 'veyton') === false) dmc_delete_product($Artikel_ID); 
			  }
		  
		} // "Normale Artikel Ende"
		else// Variantenartikel
		{		
			DEFINE( 'GAMBIO_PROPERTIES',true );
			if (DEBUGGER>=1) fwrite($dateihandle, "\n *** Artikel Variante anlegen fuer Shopsystem ".SHOPSYSTEM."\n");
			 if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) { // presta 
				if (is_file('userfunctions/products/dmc_set_art_slave_presta.php')) include ('userfunctions/products/dmc_set_art_slave_presta.php');
				else include ('functions/products/dmc_set_art_slave_presta.php');
				// Erweiterung zur Erkennung von Variantenbildern
				if (is_file('userfunctions/products/dmc_set_art_slave_presta_images.php')) include ('userfunctions/products/dmc_set_art_slave_presta_images.php');
				else if (is_file('functions/products/dmc_set_art_slave_presta_images.php')) include ('functions/products/dmc_set_art_slave_presta_images.php');
			} else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) { // veyton
				// Erfolgt ueber Standard Artikel
				/* if (is_file('userfunctions/products/dmc_set_art_slave_veyton.php')) include ('userfunctions/products/dmc_set_art_slave_veyton.php');
				else include ('functions/products/dmc_set_art_slave_veyton.php'); */
			} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) { // veyton
				// Erfolgt ueber Standard Artikel
				/* if (is_file('userfunctions/products/dmc_set_art_slave_veyton.php')) include ('userfunctions/products/dmc_set_art_slave_veyton.php');
				else include ('functions/products/dmc_set_art_slave_veyton.php'); */
			} else if (GAMBIO_PROPERTIES && (strpos(strtolower(SHOPSYSTEM), 'gambiogx') !== false OR SHOPSYSTEM_VERSION=='gx2'))
			{
				// Gambio Properties statt Attribute
				// Variantenhauptprodukt bekommt Variantenpreis, wenn Preis = 0
				dmc_main_price_not_0($Artikel_Variante_Von_id, $Artikel_Preis);
				if (is_file('userfunctions/products/dmc_set_art_slave_gambio_properties.php')) include ('userfunctions/products/dmc_set_art_slave_gambio_properties.php');
				else include ('functions/products/dmc_set_art_slave_gambio_properties.php');
			} else { // Standard
				// HHG Besonderheiten
				if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false) {	// hhg
					if (is_file('userfunctions/products/dmc_set_art_slave_hhg.php')) include ('userfunctions/products/dmc_set_art_slave_hhg.php');
					else include ('functions/products/dmc_set_art_slave_hhg.php');
				} // Ende Zusatz HHG 
				// Variantenhauptprodukt bekommt Variantenpreis, wenn Preis = 0
				dmc_main_price_not_0($Artikel_Variante_Von_id, $Artikel_Preis);
				if (is_file('userfunctions/products/dmc_set_art_slave_standard.php')) include ('userfunctions/products/dmc_set_art_slave_standard.php');
				else include ('functions/products/dmc_set_art_slave_standard.php');
			} // end Standard 
		
			// SONDERFUNKTION GAMBIO GX2 Filter setzen
			DEFINE( 'GAMBIO_FILTER',false);
			if (GAMBIO_FILTER && strpos(strtolower(SHOPSYSTEM), 'gambiogx2') !== false OR (strtolower(SHOPSYSTEM) == 'gambio' && (SHOPSYSTEM_VERSION=='gx2'))
				&& $Artikel_Merkmal != "" && $Artikel_Auspraegung != "" )
			{
				if (DEBUGGER>=1) fwrite($dateihandle, "SONDERFUNKTION GAMBIO GX2 Filter setzen\n");
				
				DEFINE( 'GAMBIO_FILTER_ATTRIBUTES','Größe@Farbe@Material' );
				DEFINE( 'GAMBIO_FILTER_ATTRIBUTES_INTERN','size_herren@Farbe@Material' );
				if (is_file('userfunctions/products/dmc_gambio_filter.php')) include ('userfunctions/products/dmc_gambio_filter.php');
				else include ('functions/products/dmc_gambio_filter.php');
			}
			
			// Gglfs weitere Filter Funktionen in dmc_array_create_additional gesetzt ($filter=true;) / zB fuer modified
			if ($filter==true && $Artikel_ID>0 && $exists==0 ) {
				if (is_file('userfunctions/products/dmc_modified_filter.php')) include ('userfunctions/products/dmc_modified_filter.php');
				else include ('functions/products/dmc_modified_filter.php');
			}
			
			// GGfl vorhandene benutzerdefinierte Funktionen g
			if (is_file('userfunctions/products/dmc_userfunctions_g.php')) include ('userfunctions/products/dmc_userfunctions_g.php');
		
		} // Ende Varianten
											
		if (DEBUGGER>=50) fwrite($dateihandle, "\n 619 Laufzeit = ".(microtime(true) - $beginn)."\n");
	
		// Variante für Bestellimport in Hilfs Tabelle einfügen.
		//28.08.2013 - decrepated
		/*if ($Artikel_Variante_Von != "") 
			if (is_file('userfunctions/products/dmc_set_art_slave_table.php')) include ('userfunctions/products/dmc_set_art_slave_table.php');
			else include ('functions/products/dmc_set_art_slave_table.php');
		if (DEBUGGER>=50) fwrite($dateihandle, " 350 Laufzeit = ".(microtime(true) - $beginn)."\n");
		*/
		// Bildverarbeitung dmc_set_images
		//if (DEBUGGER>=50) fwrite($dateihandle, " 629 Laufzeit = ".(microtime(true) - $beginn)."\n");
		if (is_file('userfunctions/products/dmc_set_images.php')) include ('userfunctions/products/dmc_set_images.php');
		else include ('functions/products/dmc_set_images.php');
		//if (DEBUGGER>=50) fwrite($dateihandle, " 632 Laufzeit = ".(microtime(true) - $beginn)."\n");
		
		// GGfl vorhandene benutzerdefinierte Funktionen h
		if (is_file('userfunctions/products/dmc_userfunctions_h.php')) include ('userfunctions/products/dmc_userfunctions_h.php');
		//if (DEBUGGER>=50) fwrite($dateihandle, " 636 Laufzeit = ".(microtime(true) - $beginn)."\n");
		
		// extensions wie SEO tool bluegate, HHG specials, Kundengruppenrechte etc
		if (is_file('userfunctions/products/dmc_run_special_functions.php')) include ('userfunctions/products/dmc_run_special_functions.php');
		else include ('functions/products/dmc_run_special_functions.php');
		//if (DEBUGGER>=50) fwrite($dateihandle, " 641 Laufzeit = ".(microtime(true) - $beginn)."\n");
		
		// GGfl vorhandene benutzerdefinierte Funktionen 
		if (is_file('userfunctions/products/dmc_userfunctions_i.php')) include ('userfunctions/products/dmc_userfunctions_i.php');
		
		if (DEBUGGER>=50) fwrite($dateihandle, " Ende Laufzeit = ".(microtime(true) - $beginn)."\n");
		
		// Rueckgabe
		$rueckgabe = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
		   "<STATUS>\n" .
		   "  <STATUS_INFO>\n" .
		   "    <ACTION>$action</ACTION>\n" .
		   "    <MESSAGE>OK</MESSAGE>\n" .
		   "    <MODE>$mode</MODE>\n" .
		   "    <ID>$Artikel_ID</ID>\n" .
		   "  </STATUS_INFO>\n" .
		   "</STATUS>\n\n";
		
		echo $rueckgabe;
		fwrite($dateihandle, "dmc_write_art - rueckgabe=".$rueckgabe."\n"); 
		
		
		return $newProductId;	
	} // end function

	
?>
	