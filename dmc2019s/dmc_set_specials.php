<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shops												*
*  dmc_set_specials.php														*
*  Artikel-Aktionspreise schreiben											*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
16.03.12
- Funktion aus dmconnector.php ausgegliedert (an Stelle von dmc_set_specials() zu verwenden)
13.2.2051
- Unterstützung wooCommerce
*/
 
	defined( 'VALID_DMC' ) or die( 'Direct Access to this location is not allowed.' );
	
	function dmc_set_specials() {
	
		global $dateihandle, $action;
		
		if (is_file('userfunctions/products/dmc_art_functions.php')) 
			include ('userfunctions/products/dmc_art_functions.php');
		else include ('functions/products/dmc_art_functions.php');
		
		fwrite($dateihandle, "dmc_set_specials - ArtNr ".$Artikel_Artikelnr." - ".date("l d of F Y h:i:s A")."\n");
	
		$Artikel_ID = (sonderzeichen2html(true,$_POST['Artikel_ID']));	// wird nicht verwendet
		$Artikel_Artikelnr = sonderzeichen2html(true,$_POST['Artikel_Artikelnr']);  
		$Aktionspreis = sonderzeichen2html(true,$_POST['Aktionspreis']);
		$Aktionspreis=str_replace(",",".",$Aktionspreis);
		$Artikel_Anzahl = (integer)(sonderzeichen2html(true,$_POST['Artikel_Anzahl']));
		$Artikel_Anzahl=str_replace(",",".",$Artikel_Anzahl);
		$DatumVon = sonderzeichen2html(true,$_POST['DatumVon']);
		$DatumBis = sonderzeichen2html(true,$_POST['DatumBis']);
		if ($DatumVon=='' || $DatumVon==null) $DatumVon='now()';
		if ($DatumBis=='' || $DatumBis==null) $DatumBis='now()+7';

		// NEU 21032018 fuer Presta - Rabattpreis statt Aktionspreis (zB Regulär 300, Sonderpreis 250 = Rabatt 50€)
		if ($Artikel_ID == "rabatt") {
			$rabatt=$Aktionspreis;
			$modus="rabatt";
		} else {
			$modus="";
			$rabatt=0;
		}
		
		if (DEBUGGER>=1) 
			fwrite($dateihandle, "Artikel-Aktionspreise zu Artikel-Nr=".$Artikel_Artikelnr." ab $DatumVon (".$_POST['DatumVon'].") bis $DatumBis\n");
		
		// Artikel laden
		$Artikel_ID=dmc_get_id_by_artno($Artikel_Artikelnr);
		if ($Artikel_ID=="") 
			return "Aktionspreise konnte nicht gesetzt werden -> ".$Artikel_Artikelnr." nicht vorhsnden.";
		else
			$exists = 1;	
		
		// Wenn Artikel  existiert
		if ($exists == 1 && $Aktionspreis>0) {		
		
			if (SHOPSYSTEM == 'presta') {
				// NEU 21032018 fuer Presta - Rabattpreis statt Aktionspreis (zB Regulär 300, Sonderpreis 250 = Rabatt 50€)
		        if ($modus == "rabatt") {
					if (DEBUGGER>=1) fwrite($dateihandle, "Rabattpreise Presta LOESCHEN für Artikel_ID =".$Artikel_ID." \n");
					$query = "DELETE FROM ps_specific_price WHERE from_quantity=1 AND reduction_type='amount' AND id_product=".$Artikel_ID;
					dmc_sql_query($query);
					$Artikel_Anzahl=1;
					$Aktionspreis=-1;
					if ($rabatt>0) {
						if (DEBUGGER>=1) fwrite($dateihandle, "Rabattpreise Presta setzen Rabattbetrag ".$rabatt." für Artikel_ID =".$Artikel_ID." \n");
						$query = "INSERT INTO `ps_specific_price` (`id_specific_price_rule`,`id_cart`,`id_product`,`id_shop`,`id_shop_group`,`id_currency`,`id_country`,`id_group`,`id_customer`,`id_product_attribute`,`price`,`from_quantity`,`reduction`,`reduction_tax`,`reduction_type`,`from`,`to`) VALUES ('0','0','".$Artikel_ID."','1','0','0','0','0','0','0','".$Aktionspreis."','".$Artikel_Anzahl."','".$rabatt."','1','amount','0000-00-00 00:00:00','0000-00-00 00:00:00')";
						dmc_sql_query($query);
					}
				} else if ($Aktionspreis!='' && $Aktionspreis>0) {
					if (DEBUGGER>=1) fwrite($dateihandle, "Aktionspreis Presta LOESCHEN für Artikel_ID =".$Artikel_ID." \n");
					$query = "DELETE FROM ps_specific_price WHERE from_quantity=1 AND reduction_type='amount' AND id_product=".$Artikel_ID;
					dmc_sql_query($query);
					$Artikel_Anzahl=1;
					if (DEBUGGER>=1) fwrite($dateihandle, "Aktionspreis Presta setzen Preis ".$Aktionspreis." für Artikel_ID =".$Artikel_ID." \n");
					$query = "INSERT INTO `ps_specific_price` (`id_specific_price_rule`,`id_cart`,`id_product`,`id_shop`,`id_shop_group`,`id_currency`,`id_country`,`id_group`,`id_customer`,`id_product_attribute`,`price`,`from_quantity`,`reduction`,`reduction_tax`,`reduction_type`,`from`,`to`) VALUES ('0','0','".$Artikel_ID."','1','0','0','0','0','0','0','".$Aktionspreis."','".$Artikel_Anzahl."','0.000000','1','amount','0000-00-00 00:00:00','0000-00-00 00:00:00')";
					dmc_sql_query($query);
				}
				
				
			} else if (SHOPSYSTEM == 'shopware') {
				// Aktionspreis Update -> Pseudopreis ist Standardpreis und price Aktionspreis
				// Setzen, wenn Preis niedriger
				$query="select id from s_articles_details where ordernumber = '".$Artikel_Artikelnr."'";
				$sql_query = dmc_db_query($query);
				if (($result = dmc_db_fetch_array($sql_query)))
				{
					$Artikel_details_ID=$result['id'];
					$query="select price from s_articles_prices where articledetailsID = ".$Artikel_details_ID." and pricegroup = 'EK'";
					$sql_query = dmc_db_query($query);
					if (($result = dmc_db_fetch_array($sql_query)))
					{
						// Achtung: Von Schnittstelle kommt brutto in Shopware steht in der Regel netto
						$Aktionspreis = $Aktionspreis / 1.19;
						// Existiert (muesste es auch)
						$alter_preis=$result['price'];
						fwrite($dateihandle, "alter_preis=".$alter_preis." Aktionspreis : ".round($Aktionspreis)."\n");
						if (round($alter_preis)>round($Aktionspreis)) {
							$table = "s_articles_prices";
							dmc_sql_update($table, "price='".$Aktionspreis."', pseudoprice=".$alter_preis , "pricegroup='EK' AND articledetailsID=".$Artikel_details_ID);				// Aktionspreis  
						}
					}
				}
				
			} else if (SHOPSYSTEM == 'woocommerce') {
				// Bei wooCommerce einfach nur Update auf _sale_price 		
				// Aktionspreis Update
				$table = "postmeta";
				dmc_sql_update($table, "meta_value='".$Aktionspreis."'" , "meta_key= '_sale_price' AND post_id=".$Artikel_ID);				// Aktionspreis  
				dmc_sql_update($table, "meta_value='".$DatumVon."'" , "meta_key= '_sale_price_dates_from' AND post_id=".$Artikel_ID);	// Gültig ab
				dmc_sql_update($table, "meta_value='".$DatumBis."'" , "meta_key= '_sale_price_dates_to' AND post_id=".$Artikel_ID);		// Gültig bis
			} else { // nicht woocommerce
								// Alte Zuordnungen löschen -> NUR WENN von Warenwirtschaft UND NICHT MANUELL eingetragen wurde		
				if (SHOPSYSTEM != 'hhg') {
					if (SHOPSYSTEM == 'veyton') {
						$where = "products_id='$Artikel_ID' and date_expired = '$DatumBis'";
						$TABLE_SPECIALS='xt_products_price_special';
					} else {
						$where = "products_id='$Artikel_ID' and date_status_change = '1973-02-28 05:00:00'";
						$TABLE_SPECIALS=TABLE_SPECIALS;
					}
				} else{
					$where = "products_id='$Artikel_ID' and date_status_change = '1973-02-28 05:00:00' OR specials_date_added = '1973-02-28 05:00:00'";
				}
				
				// Eintrag schon vorhanden?
				if (dmc_sql_select_query("products_id",$TABLE_SPECIALS,$where) != "") {
					// Zuordnungen löschen, wenn bereits existent
					// fwrite($dateihandle, "del first\n");
					dmc_sql_delete($TABLE_SPECIALS, "products_id='$Artikel_ID'");
				}
					
				// Aktionspreis schreiben
				if (SHOPSYSTEM != 'hhg') {
					if (SHOPSYSTEM == 'veyton') {
						fwrite($dateihandle, "70 products_id = $Artikel_ID und specials_quantity = $Artikel_Anzahl und Preis=$Aktionspreis und bis $DatumBis ");
						$sql_data_array = array('products_id' => $Artikel_ID,
												'specials_price' => $Aktionspreis,
												'date_available' => $DatumVon,
												'date_expired' => $DatumBis,
												'group_permission_all' => '1',
												'group_permission_1' => '1',
												'group_permission_2' => '1',
												'group_permission_3' => '1',
												'status' => '1');
						dmc_sql_insert($TABLE_SPECIALS, 
							'products_id, specials_price, date_available,date_expired,group_permission_all,group_permission_1,group_permission_2,group_permission_3,status', 
							"$Artikel_ID, $Aktionspreis, '$DatumVon', '$DatumBis', '1', '1','1','1', '1'");
						dmc_sql_update(TABLE_PRODUCTS, "flag_has_specials=1", "products_id=$Artikel_ID");
						fwrite($dateihandle, "okay");
						 
					} else {
						//nicht veyton und nicht hhg
						fwrite($dateihandle, "products_id = $Artikel_ID und specials_quantity = $Artikel_Anzahl und Preis=$Aktionspreis und bis $DatumBis\n");
					/*	$sql_data_array = array(
												'products_id' => $Artikel_ID,
												'specials_quantity' => $Artikel_Anzahl,
											//	'date_status_change' => '1973-02-28 05:00:00',
												'specials_new_products_price' => $Aktionspreis,
												//'expires_date' => $DatumBis,
												'status' => '1'); */
						//if (STORE_ID != "")
						//	$sql_data_array['store_id'] = STORE_ID;
						//else 
						//	$sql_data_array['store_id'] = 1;
						
						dmc_sql_insert($TABLE_SPECIALS, 
							'products_id, specials_quantity, date_status_change,specials_new_products_price,expires_date,status', 
							"$Artikel_ID, $Artikel_Anzahl,'1973-02-28 05:00:00', $Aktionspreis, '$DatumBis', 1");
						
						
					}
				} else {
					//hhg
					$sql_data_array = array('products_id' => $Artikel_ID,
											'specials_quantity' => $Artikel_Anzahl,
											'specials_date_added' => '1973-02-28 05:00:00',
											'specials_new_products_price' => $Aktionspreis,
											'expires_date' => $DatumBis,
											'status' => '1');
				}
											
				$ergebnis="Aktionspreis eingetragen.";
				if (DEBUGGER>=1) fwrite($dateihandle, $ergebnis."\n");
				// Abgelaufene Aktionspreise löschen (NUR wenn von WaWi übertragen
				if (SHOPSYSTEM != 'veyton') {
					dmc_sql_delete($TABLE_SPECIALS, "expires_date < now() and specials_date_added = '1973-02-28 05:00:00'");
				} else {
					dmc_sql_delete($TABLE_SPECIALS,  "date_expired < now() and date_expired = '1973-02-28 05:00:00'");
				}
				// Artikel in Kategorie schreiben
				if (SPECIAL_PRICE_CATEGORY<>'0' && SPECIAL_PRICE_CATEGORY<>'') {
					if (is_numeric($Kategorie_ID)) {
						if (DEBUGGER>=1) fwrite($dateihandle, "SPECIAL_PRICE_CATEGORY mit Kategorie_ID=".SPECIAL_PRICE_CATEGORY." zuordnen\n");
						// Kategorie_ID ist eine Zahl, daher KEINE Sonderkategorie				
						// Überprüfen, ob Produkt Kategorien zugeordnet sind
						$cmd = 	"SELECT products_id,categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE " .
								"products_id='$Artikel_ID' AND categories_id=".SPECIAL_PRICE_CATEGORY;
						// Kategorie-Zuordnung, nur wenn noch nicht existent
						$desc_query = dmc_db_query($cmd);
						if (($desc = dmc_db_fetch_array($desc_query)))
						{
							// Existiert bereits
						} else {
							// Existiert noch nicht
							// Kategoriezuordnung eintragen
							if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
								$insert_sql_data= array(
									'products_id' => $Artikel_ID,
									'categories_id' => SPECIAL_PRICE_CATEGORY,
									// 'store_id'	=> $STORE_ID,				// Ab veyton 4.2
									'master_link' => '1');
								if (SHOPSYSTEM_VERSION>=4.2) 
									$insert_sql_data['store_id'] = SHOP_ID;		// Ab veyton 4.2 
							} else {
								$insert_sql_data= array(
									'products_id' => $Artikel_ID,
									'categories_id' => SPECIAL_PRICE_CATEGORY);
							}
							dmc_sql_insert_array(TABLE_PRODUCTS_TO_CATEGORIES, $insert_sql_data);
						}						
					} else { 
						if (DEBUGGER>=1) fwrite($dateihandle, "/// Problem mit der Kategoriezuordnung, pruefen Sie ob ID durch dmc_generate_cat_id.php ermittelt wurde -> hier ID = $Kategorie_ID\n");			
					} // endif 
				}
			}
		} 
		
		// Rueckgabe
		echo '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
		   "<STATUS>\n" .
		   "  <STATUS_INFO>\n" .
		   "    <ACTION>$action</ACTION>\n" .
		   "    <MESSAGE>OK</MESSAGE>\n" .
		   "    <MODE>$mode</MODE>\n" .
		   "    <ID>$Artikel_ID</ID>\n" .
		   "  </STATUS_INFO>\n" .
		   "</STATUS>\n\n";
		
		return $ergebnis;	
	} // end function

	
?>
	