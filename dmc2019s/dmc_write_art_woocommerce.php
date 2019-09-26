<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shops												*
*  dmc_write_art_woocommerce.php											*
*  Artikel schreiben fuer woocommerce										*
*  Copyright (C) 2014 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
*/
 
	defined( 'VALID_DMC' ) or die( 'Direct Access to this location is not allowed.' );
	
	function dmc_write_art($ExportModus, $Artikel_ID, $Kategorie_ID, $Hersteller_ID,$Artikel_Artikelnr,$Artikel_Menge,
		$Artikel_Preis,$Artikel_Preis1,$Artikel_Preis2,$Artikel_Preis3,$Artikel_Preis4,$Artikel_Gewicht,$Artikel_Status,$Artikel_Steuersatz,
		$Artikel_Bilddatei,$Artikel_VPE,$Artikel_Lieferstatus,$Artikel_Startseite,$SkipImages, $Aktiv,$Aenderungsdatum,$Artikel_Variante_Von,$Artikel_Merkmal,
		$Artikel_Auspraegung,$Artikel_Bezeichnung ,$Artikel_Langtext ,$Artikel_Kurztext,$Artikel_Sprache,$Artikel_MetaText,$Artikel_MetaDescription,$Artikel_MetaKeyword,$Artikel_MetaUrl) {
	
		// NEU 14022017 POSTMETA basierend auf einem Vorlageartikel generieren und die wichtigen Werte Updaten
		$vorlageartikelnummer = "vorlage"; // Setzen, wenn Vorlageartikel vorhanden und verwendet werden soll.
		// Voreinstellungen Lager (Mappings von $Artikel_Lieferstatus=0 bis 5 siehe weiter unten)
		$mindestbestand_nichtvorraetig = 0;			// zB Artikel mit bestand = 0 als nicht vorraetig kennzeichnen. zB -10000 werden in der Regel alle Produkte als vorraetig gekennzeichnet
				
		// Abfangroutine
	/*	$Artikel_Auspraegung = str_replace("ü","ue",($Artikel_Auspraegung));
		$Artikel_Auspraegung = str_replace("ä","ae",($Artikel_Auspraegung));
		$Artikel_Auspraegung = str_replace("ö","oe",($Artikel_Auspraegung));
		$Artikel_Auspraegung = str_replace("ß","ss",($Artikel_Auspraegung));
		$Artikel_Auspraegung = str_replace("ÃŸ","ss",($Artikel_Auspraegung));
		$Artikel_Auspraegung = str_replace("ü","ue",utf8_encode($Artikel_Auspraegung));
		$Artikel_Auspraegung = str_replace("ä","ae",utf8_encode($Artikel_Auspraegung));
		$Artikel_Auspraegung = str_replace("ö","oe",utf8_encode($Artikel_Auspraegung));
		$Artikel_Auspraegung = str_replace("ß","ss",utf8_encode($Artikel_Auspraegung));
		$Artikel_Auspraegung = str_replace("ÃŸ","ss",utf8_encode($Artikel_Auspraegung));
		$Artikel_Auspraegung = str_replace("ߢ,"ss",utf8_encode($Artikel_Auspraegung));
	
		*/
		// module role_based_price ?
		$_enable_role_based_price=0;
		
		include_once "../wp-load.php";

		//call addaprdct function when plugin is activated by admin:
		register_activation_hook( __FILE__, 'addaprdct' );

		global $dateihandle, $action, $wpdb;
				
		// Laufzeit
		$beginn = microtime(true); 
		
		fwrite($dateihandle, "dmc_write_art @ dmc_write_art_woocommerce- "); 
		// fwrite($dateihandle, " - Bez: =".$Artikel_Bezeichnung." \n");

		if (is_file('userfunctions/products/dmc_art_functions.php')) include ('userfunctions/products/dmc_art_functions.php');
		else include ('functions/products/dmc_art_functions.php');
		// ggfls Mappings
		// Mappings, zB Ermittlung von Artikel_EAN
		if (is_file('userfunctions/products/dmc_mappings.php')) include ('userfunctions/products/dmc_mappings.php');
		else include ('functions/products/dmc_mappings.php');

		fwrite($dateihandle, " ArtNr: $Artikel_Artikelnr (".date("l d of F Y h:i:s A").")"); 

		$Artikel_Artikelnr=trim($Artikel_Artikelnr);
		
		$post_ID=dmc_get_id_by_artno($Artikel_Artikelnr);
		if ($post_ID=="") 
			$exits=false; 
		else
			$exits=true;
		
		$delete_first=false;
		// (vorab) loeschen? / delete (first)? / $Aktiv == 'loeschen' || $Aktiv == 'delete'
		if ($delete_first==true) {
			wp_delete_post($post_ID, true);  
			fwrite($dateihandle, "Artikel ".$Artikel_Artikelnr." vorab geloescht\n");
			$post_ID="";
			$exits=false; 
		}
		if ($Aktiv=='delete' || $Aktiv=='loeschen' || $Aktiv=='delete') {
			fwrite($dateihandle, "Artikel ".$Artikel_Artikelnr." loeschen\n");
			wp_delete_post($post_ID, true);  
				/* $query = "DELETE FROM ".DB_PREFIX ."posts"." WHERE ID='$post_ID'";	
				dmc_sql_query($query);
				$query = "DELETE FROM ".DB_PREFIX ."postmeta"." WHERE post_id='$post_ID'";	
				dmc_sql_query($query);*/
			return;
		}
		
		if ($Aktiv=='0' || $Aktiv=='deaktivieren') {
			fwrite($dateihandle, "Artikel ".$Artikel_Artikelnr." deaktivieren, wenn vorhanden und zurückspingen\n");
			if ($exits==false) {
				fwrite($dateihandle, "Kein Shopartikel \n");
			} else {
				$query = "UPDATE ".DB_PREFIX ."posts"." SET post_status = 'draft' WHERE ID='$post_ID'";		
				dmc_sql_query($query);
				fwrite($dateihandle, "Artikel ".$Artikel_Artikelnr." deaktivieren und Abbruch\n");
			}
			return;
		}
		
		fwrite($dateihandle, " post_ID= ".$post_ID." existiert= ".$exits."\n" );				
		
		// Mappings
		$Artikel_Preis = str_replace(',', '.', $Artikel_Preis );
		// Aktiv Passiv Status
		if ( $Aktiv == '1' || $Aktiv == 'publish') { 
			$post_status= 'publish'; 
		} else if ($Aktiv == 'private')  { 
			$post_status= 'private'; 
		} else   { 
			$post_status= 'draft'; 	// nicht online 
		}	
		// Artikel_Status Sichtbarkeit  visible oder hidden
		if ( $Artikel_Status == '1'  || $Artikel_Status == 'publish' || $Artikel_Status == 'visible'  || $Artikel_Status == '-1') { 
			$Artikel_Status= 'visible'; 
		} else   { 
			$Artikel_Status= 'hidden'; 	// hidden
		}	
		
		// Sonderfall: Wenn dem Merkmal  IndividuellerArtikel eine Nummer zugeweisen ist, dann Status private und hidden
		// ZB <Artikel_Merkmal>IndividuellerArtikel@_recommended@pa_farbe@pa_gewicht@pa_groesse</Artikel_Merkmal>
		// ZB <Artikel_Auspraegung>1334900000@@weiss@650@50x70</Artikel_Auspraegung>
		if (substr($Artikel_Merkmal,0,5)=='Indiv' && substr($Artikel_Auspraegung,0,1)!='@') {
			// 0000000000 ist kein individueller Artikel
			if (substr($Artikel_Merkmal,0,5)!='00000') {
				$post_status= 'private';
				$Artikel_Status= 'hidden'; 	// hidden
			}
			
		} 			
		
		// Steuersaetze woocommerce
		// _tax_class zB = 'erhoehter-satz' oder NULL und 
		// _Tax_status =  ��taxable', ��none' oder ��shipping'
		if ($Artikel_Steuersatz == 0) {
			$_tax_status = 'none';
		} else if ($Artikel_Steuersatz == 7) {
			$_tax_status = 'taxable';
			$_tax_class = 'verringerter-satz';
		} else {
			$_tax_status = 'taxable';
			$_tax_class = null;				// Standard == NULL
		} 
			
		// (Startseitenartikel yes/no)	
		if ( $Artikel_Startseite == '1' || $Artikel_Startseite == 'yes' || $Artikel_Startseite == 'Ja' || $Artikel_Startseite == 'ja') { 
			$Artikel_Startseite= 'yes'; 
		} else { 
			$Artikel_Startseite = 'no';	
		}	
		// Lager
		if ( $Artikel_Menge > -100000) { 		// Artikel auf Lager
			$_stock_status= 'instock'; 		// Bestandsstatus
			$_backorders = 'yes';			// (Nachbestellung yes/no)
			$_manage_stock  = 'yes';		// (Bestandsverwaltung yes/no)			
		} else { 						// Kein Bestand
			$_stock_status= 'outofstock'; 		// Bestandsstatus
			$_backorders = 'yes';			// (Nachbestellung yes/no)
			$_manage_stock  = 'yes';		// (Bestandsverwaltung yes/no)
		}	
		
		// wooCommerce - Lieferstatus mappen
		if ($Artikel_Lieferstatus=="0") {
			$_stock_status= 'outofstock'; 		// Bestandsstatus
			$_backorders = 'yes';			// (Nachbestellung yes/no)
			$_manage_stock  = 'yes';		// (Bestandsverwaltung yes/no)
		} else if ($Artikel_Lieferstatus=="1") {
			$_stock_status= 'instock'; 		// Bestandsstatus
			$_backorders = 'yes';			// (Nachbestellung yes/no)
			$_manage_stock  = 'yes';		// (Bestandsverwaltung yes/no)
		} else if ($Artikel_Lieferstatus=="2") {
			$_stock_status= 'instock'; 		// Bestandsstatus
			$_backorders = 'yes';			// (Nachbestellung yes/no)
			$_manage_stock  = 'no';		// (Bestandsverwaltung yes/no)
		} else if ($Artikel_Lieferstatus=="3") {
			$_stock_status= 'instock'; 		// Bestandsstatus
			$_backorders = 'notify';			// (Nachbestellung yes/no)
			$_manage_stock  = 'yes';		// (Bestandsverwaltung yes/no)
		} else if ($Artikel_Lieferstatus=="4") {
			$_stock_status= 'instock'; 		// Bestandsstatus
			$_backorders = 'no';			// (Nachbestellung yes/no)
			$_manage_stock  = 'yes';		// (Bestandsverwaltung yes/no)
		} else {  
			// Standard , wenn keine Angabe
			// Lager
			if ( $Artikel_Menge > $mindestbestand_nichtvorraetig) { 		// Artikel auf Lager
				$_stock_status= 'instock'; 		// Bestandsstatus
				$_backorders = 'yes';			// (Nachbestellung yes/no)
				$_manage_stock  = 'yes';		// (Bestandsverwaltung yes/no)			
			} else { 						// Kein Bestand
				$_stock_status= 'outofstock'; 		// Bestandsstatus
				$_backorders = 'yes';			// (Nachbestellung yes/no)
				$_manage_stock  = 'yes';		// (Bestandsverwaltung yes/no)
			}	
		}
		
		
		$d1 = array(" ","Ä", "Ö", "Ü", "ä" , "ö", "ü", "ß","<",">","#","\"","'","�",",","&","�","?",";","\\","/","-");
		$d2 = array("-", "Ae","Oe","Ue","ae","oe","ue","sz","_","_","_","_","_","_","_","-","2","-","-","_","_","_");
		$seo_text = str_replace($d1, $d2, $Artikel_Bezeichnung);		 
		$Artikel_SEO_Name = preg_replace('/[^0-9a-zA-Z-_]/', '', $seo_text);
			
		 fwrite($dateihandle, "Artikel_SEO_Name $Artikel_SEO_Name.\n");
			
		// ARTIKEL ANLEGEN
		if ($exits==false) {
			// 1.Schritt Post anlegen
			$post_type='product';
			
			// Wenn Variante, ParentID ermitteln
			$post_type='product';
			$post_parent_ID=0;
			if ($Artikel_Variante_Von!='') {
				$post_parent_ID=dmc_get_id_by_artno($Artikel_Variante_Von);
				// hauptproduct ggfls von simple auf variable aendern
				if ($post_parent_ID!='') {
					wp_set_object_terms ($post_parent_ID,'variable','product_type');
					//################### Add size attributes to main product: ####################
						//Array for setting attributes
					/*	$avail_attributes = array(
						'xl',
						'l',
						'm',
						's'
						);
						wp_set_object_terms($post_ID, $avail_attributes, 'pa_size');

						$thedata = Array('pa_size'=>Array(
						'name'=>'pa_size',
						'value'=>'',
						'is_visible' => '1', 
						'is_variation' => '1',
						'is_taxonomy' => '1'
						));
						update_post_meta( $post_ID,'_product_attributes',$thedata);*/
						//########################## Done adding attributes to product #################
				} 
				// Variante
				$post_type='product_variation';
				$ping_status = 'open';
				$guid = SHOP_URL.'?post_type=product_variation&p='.$post_ID;
				// oder $i=1;$guid = home_url() . '/?product_variation=product-' . $post_ID . '-variation-' . $i;

			} else {
				// SEO URL ermitteln
				$guid = SHOP_URL.'?post_type=product&p='.$post_ID;
			}
		
			// Sonderfall start
			$comment_status = 'closed';
			// Sonderfall ende
	
			// ACHTUNG: BUG in wp_insert_port: GEHT scheinbar nicht mit UTF8
			$post = array(
				'post_title'   => trim($Artikel_SEO_Name), // trim($Artikel_Bezeichnung),			// Name
 				'post_content' => trim($Artikel_SEO_Name), // trim($Artikel_Langtext),			// Langtext
 				'post_status'  => $post_status,
 				'post_excerpt' => trim($Artikel_SEO_Name), // trim($Artikel_Kurztext),			// Kurzbeschreibung
    		    'post_parent' => $post_parent_ID,			//post is a child post of product post
				 'post_name'    => trim($Artikel_SEO_Name), //name/slug
 				// Sonderfall START
				'comment_status' => $comment_status,
				'post_author'	=> '1',
				// Sonderfall ENDE
				'post_type'    => $post_type
 			); 
 			
			//$ausgeben = print_r($post,true);
			//fwrite($dateihandle, " Insert Array = ".$ausgeben."\n" );				
			
			//Create post
			$mode='INSERTED'; 
			$post_ID = $new_post_id = wp_insert_post($post, $wp_error);
			fwrite($dateihandle, "275 - Artikel ID $post_ID \n");	
			
			// ACHTUNG: BUG in wp_insert_port: GEHT scheinbar nicht mit UTF8
			//update post
			$table = "posts";
			dmc_sql_update($table,  "post_title='".trim($Artikel_Bezeichnung)."',  
			post_excerpt='".trim($Artikel_Kurztext)."', post_content='".trim($Artikel_Langtext)."'" , 
			"post_type= 'product' AND ID=".$post_ID);			
			// print_r($post);
 			if ($post_ID == 0 )
				fwrite($dateihandle, "285 - Artikel $Artikel_ID, in post NICHT. Möglicherweise aufgrund eines Zeichensatzproblems. Bitte utf8decode/encode prüfen \n");	
			else 
				fwrite($dateihandle, "287 - Artikel $Artikel_ID  , in post angelegt mit post_id ".$post_ID."\n");	
			$variantendefaultmerkmale=false;
			// 2ter Schritt, wenn conf Produkt, dann "Hauptattribute" mit Werten setzen und Produkt auf Variant ändern.
			if (substr($Artikel_ID,0,4)=='conf' && $post_ID > 0) {
				fwrite($dateihandle, "291 Variantenhauptprodukt $Artikel_Merkmal\n");	
				if ($Artikel_Merkmal<>'') {
					//fwrite($dateihandle, "207 \n");	
						$hauptmerkmale=false;
						$Artikel_Merkmale  = explode ( '@', $Artikel_Merkmal);
						$Artikel_Auspraegungen = explode ( '@', $Artikel_Auspraegung);
						for($Anzahl = 0; $Anzahl <= count($Artikel_Merkmale)-1; $Anzahl++) {     
							// ggfls alle verfuegbaren attribute ergaenzen
							// da. zb fuer pa_size sollten dann die verfuegbaren uebergeben werden S|M|L
							if (substr($Artikel_Merkmale[$Anzahl],0,3)=='pa_') {
								$hauptmerkmale=true;
								if (substr($Artikel_Auspraegungen[$Anzahl],-1)=="|")
									$Artikel_Auspraegungen[$Anzahl]=substr($Artikel_Auspraegungen[$Anzahl],0,-1);
								fwrite($dateihandle, "303 - ".$Artikel_Merkmale[$Anzahl]." -> ".$Artikel_Auspraegungen[$Anzahl]."\n");
								// ggfls alle verfuegbaren attribute ergaenzen
								/* ZB $available_attributes = array(
									'l',
									'm',
									's'
								); */
								$available_attributes = explode ( '|', $Artikel_Auspraegungen[$Anzahl]);
								$log = print_r($available_attributes,true);
								fwrite($dateihandle, "311 - wp_set_object_terms - available_attributes -> ".$log."\n");
							
								wp_set_object_terms($post_ID, $available_attributes, $Artikel_Merkmale[$Anzahl]);
								// Auswahl Attribute dem main Produkt hinzufuegen
								//$thedata = Array('pa_size'=>Array(
								/*$thedata = Array($Artikel_Merkmale[$Anzahl]=> Array(
									'name'=> $Artikel_Merkmale[$Anzahl] ,
									'value'=>'',
									'is_visible' => '1', 
									'is_variation' => '1',
									'is_taxonomy' => '1'
								));*/
								$thedata[$Artikel_Merkmale[$Anzahl]] =  Array(
									'name'=> $Artikel_Merkmale[$Anzahl] ,
									'value'=>'',
									'is_visible' => '1', 
									'is_variation' => '1',
									'is_taxonomy' => '1'
								);
								// ERST NACH ABSCHLUSS DES DURCHLAUFES :update_post_meta( $post_ID,'_product_attributes',$thedata);
							} else if (substr($Artikel_Merkmale[$Anzahl],0,7)=='default') {
								// Sonderfall: Als "Default" Attribute für Varianten vorsehen
								$Artikel_Merkmale_kurz = str_replace('default_', '', $Artikel_Merkmale[$Anzahl]);
								if (substr($Artikel_Merkmale_kurz,0,3)=='pa_') {
									$variantendefaultmerkmale=true;
									fwrite($dateihandle, "336  variantendefaultmerkmale - ".$Artikel_Merkmale[$Anzahl]." -> ".$Artikel_Auspraegungen[$Anzahl]."\n");
									// ERST NACH ABSCHLUSS DES DURCHLAUFES :update_post_meta( $post_ID,'_product_attributes',$thedata);
									// Array fuer Hauptvariante fuer Hauptartikel vorbereiten
									$Hauptvariante[$Artikel_Merkmale_kurz] =  $Artikel_Auspraegungen[$Anzahl];
								}
								 add_post_meta($post_ID, $Artikel_Merkmale[$Anzahl], $Artikel_Auspraegungen[$Anzahl]);	// attribute und auspraegungen	
								 
							} else {
								// Als "normale" Attribute anlegen
								 add_post_meta($post_ID, $Artikel_Merkmale[$Anzahl], $Artikel_Auspraegungen[$Anzahl]);	// attribute und auspraegungen	
								 
								 // Wenn Color oder Size, dann ist Variation
								 if ($Artikel_Merkmale[$Anzahl]=='color' || $Artikel_Merkmale[$Anzahl]=='size')
									 $isvariation=1;
								 else
									 $isvariation=0;
								 $use_eigenschaften = true; // Merkmale auch als Eigenschaften anlegen
								 $Artikel_Merkmale_kurz = str_replace('attribute_', '', $Artikel_Merkmale[$Anzahl]);
								 if ($use_eigenschaften) {
									 $hauptmerkmale=true;
										// Eigenschaft dem Artikel also Standardartikel zuweisen.
										$Artikel_Merkmale_kurz =  str_replace('pa_', '', $Artikel_Merkmale_kurz);
										$variantenmerkmale=true;
										fwrite($dateihandle, "353 - ".$Artikel_Merkmale_kurz." -> ".$Artikel_Auspraegungen[$Anzahl]."\n");
										// Auswahl Attribute der Variante hinzufuegen
										//$thedata = Array('pa_size'=>Array(
										$thedata[$Artikel_Merkmale_kurz] =  Array(
												'name'=> $Artikel_Merkmale_kurz ,
												'value'=>$Artikel_Auspraegungen[$Anzahl],
												'position' => '0', 
												'is_visible' => 1, 
												'is_variation' => $isvariation,
												'is_taxonomy' => 0
											);
										
								}
							}
						} // end for
						if ($hauptmerkmale) {
							$log = print_r($thedata,true);
							fwrite($dateihandle, "370 - thedata = $log\n");
							update_post_meta( $post_ID,'_product_attributes', $thedata);
						}
				}
			} // end if configurable product
			
			// 3ter Schritt - postmeta hinzufuegen select * from wp_postmeta where meta_key 
			if($post_ID>0){
				// NEU 14022017 POSTMETA basierend auf einem Vorlageartikel generieren und die wichtigen Werte Updaten
				if ($vorlageartikelnummer != "")
					$vorlage_post_id=dmc_get_id_by_artno($vorlageartikelnummer);
				if ($vorlage_post_id != "") {
					fwrite($dateihandle, "276 - Postmeta basierend auf Vorlageartikel ArtikelID ".$vorlage_post_id." generieren\n");
					$cmd = "SELECT meta_key, meta_value FROM " . DB_PREFIX . "postmeta WHERE post_id = " . (int)$vorlage_post_id . "";
				 	// fwrite($dateihandle, "Query =$cmd\n");
					$metas_query = dmc_db_query($cmd);
					while ($metas_query_result = dmc_db_fetch_array($metas_query))
					{
						//update_post_meta($post_ID, '_width', '40');			
					//update_post_meta($post_ID, '_height', '40');	 
					//update_post_meta($post_ID, '_length', '60');		/
						// Neuem Artikel metas von Vorlageartikel zuweisen
						if (strpos($metas_query_result['meta_value'], "{")===false) {
							// Standard woocommerce Funktion
						//	if ($metas_query_result['meta_key']!='_width' && $metas_query_result['meta_key']!='_height' && $metas_query_result['meta_key']!='_length')
							add_post_meta($post_ID, $metas_query_result['meta_key'], $metas_query_result['meta_value']);		// (meta key und metavalue)
						} else {
							// add_post_meta setzt JSON nicht korrekt, daher Abfangroutine
							$query = "INSERT INTO ".DB_PREFIX ."postmeta"." (post_id, meta_key, meta_value) ".
								"VALUES (".$post_ID.",'".$metas_query_result['meta_key']."', '".$metas_query_result['meta_value']."') ";
							 dmc_sql_query($query);
						}
					}
					// Wesentliche Artikelwerte aktualisieren, da <> Vorlageartikel
					update_post_meta($post_ID, '_visibility', $Artikel_Status);	// (Sichtbarkeit wie visible)
					update_post_meta($post_ID, '_sku', $Artikel_Artikelnr);		// (Artikelnummer)
					update_post_meta($post_ID, '_ts_gtin', $Artikel_EAN); // EAN Nummer
					
					update_post_meta($post_ID, '_regular_price', $Artikel_Preis);	// (Standardpreis)
					update_post_meta($post_ID, '_price', $Artikel_Preis);		// (Preis) -> Wenn Aktionspreis, dann Aktionspreis, sonst regular price
					// _tax_status (Tax Status) _tax_class (Tax Class) im WP Standard NULL
					update_post_meta($post_ID, '_tax_status', $_tax_status );
					// add_post_meta($post_ID, '_tax_class', );
					update_post_meta($post_ID, '_featured', $Artikel_Startseite);		// (Startseitenartikel yes/no)	
					update_post_meta($post_ID, '_recommended', $Artikel_Startseite);		// (Startseitenartikel yes/no)	
					update_post_meta($post_ID, '_mini_desc', $Artikel_MetaDescription);
					update_post_meta($post_ID, '_stock', $Artikel_Menge);			
					update_post_meta($post_ID, '_stock_status', $_stock_status);	
					update_post_meta($post_ID, '_backorders', $_backorders);			
					update_post_meta($post_ID, '_manage_stock', $_manage_stock);	
					update_post_meta($post_ID, '_unit', $Artikel_VPE);		// VPE Einheit
					update_post_meta($post_ID, '_weight', $Artikel_Gewicht);	// (Gewicht)
					update_post_meta($post_ID, '_images', $Artikel_Bilddatei);		// Bilderbezeichnungen	
					update_post_meta($post_ID, '_product_image_gallery', '');		// Bilderbezeichnungen	
					update_post_meta($post_ID, '_enable_role_based_price', $_enable_role_based_price);		// Kundengruppe Preise aktivieren 
					
					// Spezielle Preise
				//	update_post_meta($post_ID, '_msrp_price', $Artikel_Preis4);			// Listenpreis
				//	update_post_meta($post_ID, '_ebay_buynow_price', $Artikel_Preis1);	// Amazon 
				//	update_post_meta($post_ID, '_amazon_price', $Artikel_Preis2);		// eBay 

					// Pflichtwerte
					//update_post_meta($post_ID, '_width', '40');			
					//update_post_meta($post_ID, '_height', '40');	 
					//update_post_meta($post_ID, '_length', '60');		/
			
					
					// UPDATE JSON PostMetas, da nicht korrekt von add_post_meta gesetzt.
					//dmc_sql_query("UPDATE ".DB_PREFIX ."postmeta"." (post_id, meta_key, meta_value) ".
					// "VALUES (".$post_ID.",'_product_attributes', 'a:0:{}') ");
					//dmc_sql_query($query);
					fwrite($dateihandle, "ende add_post_meta basierend auf Vorlageartikel\n");									
				} else {
					fwrite($dateihandle, "add_post_meta OHNE Vorlageartiel\n");
					// $attach_id = get_post_meta($product->parent_id, "_thumbnail_id", true);
					add_post_meta($post_ID, '_sku', $Artikel_Artikelnr);		// (Artikelnummer)
					add_post_meta($post_ID, '_ts_gtin', $Artikel_EAN); // EAN Nummer
					
					add_post_meta($post_ID, '_visibility', $Artikel_Status);	// (Sichtbarkeit wie visible)
					add_post_meta($post_ID, '_regular_price', $Artikel_Preis);	// (Standardpreis)
					add_post_meta($post_ID, '_price', $Artikel_Preis);		// (Preis) -> Wenn Aktionspreis, dann Aktionspreis, sonst regular price
					// _tax_status (Tax Status) _tax_class (Tax Class) im WP Standard NULL
					add_post_meta($post_ID, '_tax_status', $_tax_status );
					// add_post_meta($post_ID, '_tax_class', );
					add_post_meta($post_ID, '_featured', $Artikel_Startseite);		// (Startseitenartikel yes/no)	
					add_post_meta($post_ID, '_recommended', $Artikel_Startseite);		// (Startseitenartikel yes/no)	
					add_post_meta($post_ID, '_mini_desc', $Artikel_MetaDescription);
					// Standardwerte (ggfls anpassen)
					add_post_meta($post_ID, '_disable_woothumbs', 'no');		// Galerie abschalten
					add_post_meta($post_ID, '_total_sales', '0');
					add_post_meta($post_ID, 'total_sales', '0');
					add_post_meta($post_ID, '_variation_image_gallery', '');
					add_post_meta($post_ID, '_woocommerce_disable_product_photos', 'no');
					add_post_meta($post_ID, '_downloadable', '0');
					add_post_meta($post_ID, '_virtual', '0');
					add_post_meta($post_ID, '_product_image_gallery', '');
					add_post_meta($post_ID, '_purchase_note', '');
					// Sonderfall nicht relevant
					add_post_meta($post_ID, '_stock', $Artikel_Menge);			
					add_post_meta($post_ID, '_stock_status', $_stock_status);	
					add_post_meta($post_ID, '_backorders', $_backorders);			
					add_post_meta($post_ID, '_manage_stock', $_manage_stock);	
					add_post_meta($post_ID, '_unit', $Artikel_VPE);		// VPE Einheit
					//add_post_meta($post_ID, '_unit_base', '');			// VPE Anzahl zB 1
					//add_post_meta($post_ID, '_unit_price', '');			// VPE Preis Standard, zB 5.2
					//add_post_meta($post_ID, '_unit_regular', '');		// VPE Preis, zB 5.2
					//add_post_meta($post_ID, '_unit_price_auto', 'yes');	// Alternativ VPE Automatische Berechnung des VPE-Preises zB yes
					//add_post_meta($post_ID, '_unit_product', '');		// Stückzahl in VPE zB 6
					add_post_meta($post_ID, '_sale_price', '');				// Aktionspreis
					add_post_meta($post_ID, '_sale_price_dates_from', '');	// (Aktionspreis von)		
					add_post_meta($post_ID, '_sale_price_dates_to', '');	// (Aktionspreis bis)	
					add_post_meta($post_ID, '_weight', $Artikel_Gewicht);	// (Gewicht)	
					add_post_meta($post_ID, '_length', '');					// (Laenge)
					add_post_meta($post_ID, '_width', '');					// (Breite)	
					add_post_meta($post_ID, '_height', '');					// (Hoehe)	
					add_post_meta($post_ID, '_sold_individually', '');		// (Einzelverkauf)	
					
					// bei Bedarf
					//add_post_meta($post_ID, 'slide_template', 'default');					
					//add_post_meta($post_ID, '_specifications_attributes_title', '');		
					//add_post_meta($post_ID, '_specifications_display_attributes', 'yes');	
				
					// a:1:{s:10:"pa_groesse";a:6:{s:4:"name";s:10:"pa_groesse";s:5:"value";s:0:"";s:8:"position";s:1:"0";s:10:"is_visible";i:1;s:12:"is_variation";i:1;s:11:"is_taxonomy";i:1;}}
					// ACHTUNG: add_post_meta ändert JSON , daher SQL
					// add_post_meta($post_ID, '_product_attributes', 'a:0:{}');	// (Attribute)
					 // add_post_meta($post_ID, '_wc_rating_count', 'a:0:{}'); 
					 // add_post_meta($post_ID, '_crosssell_ids', 'a:0:{}'); 
					 // add_post_meta($post_ID, '_upsell_ids', 'a:0:{}'); 
					 $query = "INSERT INTO ".DB_PREFIX ."postmeta"." (post_id, meta_key, meta_value) ".
						"VALUES (".$post_ID.",'_product_attributes', 'a:0:{}') ".
					 dmc_sql_query($query);
					 $query = "INSERT INTO ".DB_PREFIX ."postmeta"." (post_id, meta_key, meta_value) ".
						"VALUES (".$post_ID.",'_wc_rating_count', 'a:0:{}') ".
					 dmc_sql_query($query);
					 $query = "INSERT INTO ".DB_PREFIX ."postmeta"." (post_id, meta_key, meta_value) ".
						"VALUES (".$post_ID.",'_crosssell_ids', 'a:0:{}') ".
					 dmc_sql_query($query);
					 $query = "INSERT INTO ".DB_PREFIX ."postmeta"." (post_id, meta_key, meta_value) ".
					// Weitere Sonderfall zusatzfelder
					add_post_meta($post_ID, '_images', $Artikel_Bilddatei);		// Bilderbezeichnungen	
				//	add_post_meta($post_ID, '_IndividuellerArtikel', '');		// Kundennummer, wenn ein individueller Artikel 
					add_post_meta($post_ID, '_enable_role_based_price', $_enable_role_based_price);		// Kundengruppe Preise aktivieren 
					fwrite($dateihandle, "ende add_post_meta \n");	
				}
			}
			
			// Swatches - fuer Hauptartikel hinzufuegen
			$use_swatches=true;
			if ($use_swatches && substr($Artikel_ID,0,4)=='conf' && $post_ID > 0) {
				fwrite($dateihandle, "Swatches - fuer Hauptartikel hinzufuegen \n");
			//	add_post_meta($post_ID, '_swatch_type_options', 'a:3:{s:32:"58cccce3598e8c0b02eff5449ffbbf4b";a:4:{s:4:"type";s:12:"term_options";s:6:"layout";s:7:"default";s:4:"size";s:19:"swatches_image_size";s:10:"attributes";a:4:{s:32:"fcdc7b4207660a1372d0cd5491ad856e";a:3:{s:4:"type";s:5:"color";s:5:"color";s:7:"#FFFFFF";s:5:"image";s:1:"0";}s:32:"f9f33d493fdae03f9c86e72b677fee35";a:3:{s:4:"type";s:5:"color";s:5:"color";s:7:"#FFFFFF";s:5:"image";s:1:"0";}s:32:"5948e633898c4f2646653c8c936b6317";a:3:{s:4:"type";s:5:"color";s:5:"color";s:7:"#FFFFFF";s:5:"image";s:1:"0";}s:32:"8b823f49ba31caa5cd88e520c2f82bdc";a:3:{s:4:"type";s:5:"color";s:5:"color";s:7:"#FFFFFF";s:5:"image";s:1:"0";}}}s:32:"6764ec0b9478e1d5204704b45bd86994";a:4:{s:4:"type";s:7:"default";s:6:"layout";s:7:"default";s:4:"size";s:19:"swatches_image_size";s:10:"attributes";a:1:{s:32:"4ffce04d92a4d6cb21c1494cdfcd6dc1";a:3:{s:4:"type";s:5:"color";s:5:"color";s:7:"#FFFFFF";s:5:"image";s:1:"0";}}}s:32:"b33e2b8ac7a19e921633e5274bc3432e";a:4:{s:4:"type";s:7:"default";s:6:"layout";s:7:"default";s:4:"size";s:19:"swatches_image_size";s:10:"attributes";a:1:{s:32:"e5d43c1511d58590899a63a02aaf9aff";a:3:{s:4:"type";s:5:"color";s:5:"color";s:7:"#FFFFFF";s:5:"image";s:1:"0";}}}}');		// Fester Wert	
				add_post_meta($post_ID, '_swatch_type', 'pickers');		// Bilderbezeichnungen	
				$table = "postmeta";
				$swatchesstring = 'a:3:{s:32:"58cccce3598e8c0b02eff5449ffbbf4b";a:4:{s:4:"type";s:12:"term_options";s:6:"layout";s:7:"default";s:4:"size";s:19:"swatches_image_size";s:10:"attributes";a:6:{s:32:"12bbd9846d51a4f3239e5f72c150eb07";a:3:{s:4:"type";s:5:"color";s:5:"color";s:7:"#FFFFFF";s:5:"image";s:1:"0";}s:32:"efc5f4b470619b932aed03953e50ce60";a:3:{s:4:"type";s:5:"color";s:5:"color";s:7:"#FFFFFF";s:5:"image";s:1:"0";}s:32:"fcdc7b4207660a1372d0cd5491ad856e";a:3:{s:4:"type";s:5:"color";s:5:"color";s:7:"#FFFFFF";s:5:"image";s:1:"0";}s:32:"f9f33d493fdae03f9c86e72b677fee35";a:3:{s:4:"type";s:5:"color";s:5:"color";s:7:"#FFFFFF";s:5:"image";s:1:"0";}s:32:"5948e633898c4f2646653c8c936b6317";a:3:{s:4:"type";s:5:"color";s:5:"color";s:7:"#FFFFFF";s:5:"image";s:1:"0";}s:32:"8b823f49ba31caa5cd88e520c2f82bdc";a:3:{s:4:"type";s:5:"color";s:5:"color";s:7:"#FFFFFF";s:5:"image";s:1:"0";}}}s:32:"6764ec0b9478e1d5204704b45bd86994";a:4:{s:4:"type";s:7:"default";s:6:"layout";s:7:"default";s:4:"size";s:19:"swatches_image_size";s:10:"attributes";a:1:{s:32:"4ffce04d92a4d6cb21c1494cdfcd6dc1";a:3:{s:4:"type";s:5:"color";s:5:"color";s:7:"#FFFFFF";s:5:"image";s:1:"0";}}}s:32:"b33e2b8ac7a19e921633e5274bc3432e";a:4:{s:4:"type";s:7:"default";s:6:"layout";s:7:"default";s:4:"size";s:19:"swatches_image_size";s:10:"attributes";a:1:{s:32:"e5d43c1511d58590899a63a02aaf9aff";a:3:{s:4:"type";s:5:"color";s:5:"color";s:7:"#FFFFFF";s:5:"image";s:1:"0";}}}}';
				dmc_sql_insert($table,  "post_id, meta_key, meta_value" , $post_ID.", '_swatch_type_options', '".$swatchesstring."'");		// (Artikelnummer)	
			
			}
			
			
			// Sonderfall $_enable_role_based_price=1 role base price modul
			if ($_enable_role_based_price==1) {
				// Preis1, Preis2, Preis3 fuer die gruppen grosshandel, sonderkonditionen, waescherei
				$kundengruppenpreis["grosshandel"]["regular_price"]=str_replace(".",",",$Artikel_Preis1);
				$kundengruppenpreis["sonderkonditionen"]["regular_price"]=str_replace(".",",",$Artikel_Preis2);
				$kundengruppenpreis["waescherei"]["regular_price"]=str_replace(".",",",$Artikel_Preis3);
				$kundengruppenpreise=serialize($kundengruppenpreis);
				fwrite($dateihandle, " add_post_meta _role_based_price mit $kundengruppenpreise\n");	
				// add_post_meta geht bei serialied array string nicht, bzw ergaenzt diesen
				add_post_meta($post_ID, '_role_based_price', $kundengruppenpreise);		// Kundengruppe Preise setzen 
				$query = "UPDATE ".DB_PREFIX ."postmeta"." SET meta_value = '".$kundengruppenpreise."' ".
				"WHERE meta_key='_role_based_price' AND post_id='$post_ID'";
				fwrite($dateihandle, " query mit $query\n");	
				dmc_sql_query($query);
			}
			
			fwrite($dateihandle, "553 - Artikel_Merkmal = ".$Artikel_Merkmal." und Artikel_Auspraegung=$Artikel_Auspraegung\n");	
			// Wenn Merkmale und Auspraegungen gesetzt dann mit den Merkmal Key die Auspraegung schreiben
			// zB pa_farbe, pa_groesse
			if ($Artikel_Merkmal<>'' && substr($Artikel_ID,0,4)!='conf' && $post_ID > 0) {
				fwrite($dateihandle, "547 - Artikel_Merkmal = ".$Artikel_Merkmal."\n");
				$Artikel_Merkmale  = explode ( '@', $Artikel_Merkmal);
				$Artikel_Auspraegungen = explode ( '@', $Artikel_Auspraegung);
				$variantenmerkmale=false;
				// zb update_post_meta($variation_id, 'attribute_pa_size', '2xl');
				for($Anzahl = 0; $Anzahl <= count($Artikel_Merkmale)-1; $Anzahl++) {     
					// ggfls alle verfuegbaren attribute ergaenzen
					/* $avail_attributes = array(
						'xl',
						'l',
						'm',
						's'
					);
					wp_set_object_terms($post_ID, $avail_attributes, 'pa_size');
					*/
					// Attribute anlegen, wenn variantenauswahl attribute, dann bezeichung attribute_pa_... zB attribute_pa_size
				//	$Artikel_Auspraegungen[$Anzahl] = utf8_encode($Artikel_Auspraegungen[$Anzahl]);
					fwrite($dateihandle, "564 - ".$Artikel_Merkmale[$Anzahl]." -> ".$Artikel_Auspraegungen[$Anzahl]."\n");
					if (substr($Artikel_Merkmale[$Anzahl],0,3)=='pa_' || strtolower($Artikel_Merkmale[$Anzahl])=='color' || strtolower($Artikel_Merkmale[$Anzahl])=='size') {
						$d1 = array(" ","Ä", "Ö", "Ü", "ä" , "ö", "ü", "ß","<",">","#","\"","'","�",",","&","�","?",";","\\","/","-");
						$d2 = array("-", "Ae","Oe","Ue","ae","oe","ue","sz","_","_","_","_","_","_","_","-","2","-","-","_","_","_");
						$seo_text = str_replace($d1, $d2, $Artikel_Auspraegungen[$Anzahl]);		 
						$Artikel_SEO_Auspraegung = preg_replace('/[^0-9a-zA-Z-_]/', '', $seo_text);
						$Artikel_SEO_Auspraegung = strtolower($Artikel_SEO_Auspraegung);
						// Variantenartikel
						$Artikel_Merkmale[$Anzahl] = 'attribute_'.strtolower($Artikel_Merkmale[$Anzahl]);
						$Artikel_Auspraegungen[$Anzahl] = strtolower($Artikel_SEO_Auspraegung);
					}	else if (substr($Artikel_Merkmale[$Anzahl],0,8)=='metakey:') {
						// Benutzerdefinierte Meta Keys 
						$Artikel_Merkmale[$Anzahl] = str_replace('metakey:', '', $Artikel_Merkmale[$Anzahl]);
					}
				
					// ACHTUNG / zu _ ersetzen
					$post_meta_attribute = str_replace('/', '_',  $Artikel_Auspraegungen[$Anzahl]);
					/*
					$post_meta_attribute = str_replace(' ', '-',  $post_meta_attribute);
					$post_meta_attribute = str_replace('---', '-',  $post_meta_attribute);
					$post_meta_attribute = str_replace('.', '',  $post_meta_attribute);
					$d1 = array(" ","Ä", "Ö", "Ü", "ä" , "ö", "ü", "ß","<",">","#","\"","'","�",",","&","�","?",";","\\","/","---");
					$d2 = array("-", "Ae","Oe","Ue","ae","oe","ue","sz","_","_","_","_","_","_","_","-","2","-","-","_","_","-");
					$seo_text = str_replace($d1, $d2, $Artikel_Auspraegungen[$Anzahl]);		 
					$post_meta_attribute = preg_replace('/[^0-9a-zA-Z-_]/', '', $seo_text);
					*/
						
					// fwrite($dateihandle, "Artikel_SEO_Name $Artikel_SEO_Name.\n");
				
					// $post_meta_attribute = strtolower($post_meta_attribute);
					
					if (dmc_entry_exists(DB_PREFIX ."postmeta",'meta_key',$post_meta_attribute,"and","post_id",$post_ID)) {
						$query = "UPDATE ".DB_PREFIX ."postmeta"." SET meta_value='".$Artikel_Auspraegungen[$Anzahl]."' WHERE meta_key='".$post_meta_attribute."' AND post_id= ".$post_ID;
					} else {
						// add_post_meta($post_ID, $Artikel_Merkmale[$Anzahl], $Artikel_Auspraegungen[$Anzahl]);	// attribute und auspraegungen	
						$query = "REPLACE INTO ".DB_PREFIX ."postmeta"." (meta_key,meta_value,post_id) ".
								"VALUES ('".$Artikel_Merkmale[$Anzahl]."','".$post_meta_attribute ."',".$post_ID.")";
					}
					dmc_sql_query($query); 
							
					// Attribut der Variante als variantenattribte hinzufuegen, wenn beginnt mit pa_ 
					// benoetigt wird Merkmal kurz, zb attribute_pa_size statt attribute_pa_size
					// Als Standard Attribute anlegen
					$use_eigenschaften = true; // Merkmale auch als Eigenschaften anlegen
					$Artikel_Merkmale_kurz = str_replace('attribute_', '', $Artikel_Merkmale[$Anzahl]);
					if (substr($Artikel_Merkmale_kurz,0,3)=='pa_') {
							$variantenmerkmale=true;
							fwrite($dateihandle, "596 - ".$Artikel_Merkmale[$Anzahl]." -> ".strtolower($Artikel_Auspraegungen[$Anzahl])."\n");
							// Auswahl Attribute der Variante hinzufuegen
							//$thedata = Array('pa_size'=>Array(
							$thedata[$Artikel_Merkmale_kurz] =  Array(
									'name'=> $Artikel_Merkmale_kurz ,
									'value'=> strtolower($Artikel_Auspraegungen[$Anzahl]),
									'is_visible' => '1', 
									'is_variation' => '1',
									'is_taxonomy' => '1'
								);
							// ERST NACH ABSCHLUSS DES DURCHLAUFES :update_post_meta( $post_ID,'_product_attributes',$thedata);
							// Array fuer Hauptvariante fuer Hauptartikel vorbereiten
							$Hauptvariante[$Artikel_Merkmale_kurz] =  $Artikel_Auspraegungen[$Anzahl];
					} else if ($use_eigenschaften) {
							// Eigenschaft dem Artikel also Standardartikel zuweisen.
							$Artikel_Merkmale_kurz =  str_replace('pa_', '', $Artikel_Merkmale_kurz);
							$variantenmerkmale=true;
							fwrite($dateihandle, "473 - ".$Artikel_Merkmale_kurz." -> ".$Artikel_Auspraegungen[$Anzahl]."\n");
							// Auswahl Attribute der Variante hinzufuegen
							//$thedata = Array('pa_size'=>Array(
							$thedata[$Artikel_Merkmale_kurz] =  Array(
									'name'=> $Artikel_Merkmale_kurz ,
									'value'=> strtolower($Artikel_Auspraegungen[$Anzahl]),
									'position' => '0', 
									'is_visible' => 1, 
									'is_variation' => 0,
									'is_taxonomy' => 0
								);
							
					}
				} // end for
				
				// Sonderfall: Dem Hauptartikel Standard -Varianten-Felder zuweisen
				//$thedata = Array('pa_size'=>Array(
				if ($variantenmerkmale) {
					$log = print_r($thedata,true);
					fwrite($dateihandle, "632 - thedata = $log\n");
					update_post_meta( $post_ID,'_product_attributes', $thedata);
					// Pruefen, ob Standard Variante gesetzt und ggfls setzen (Atttibute _default_attributes des Hauptartikels vorhanden und gesetzt)
					$table = "postmeta";
					$where = "post_id=".$post_parent_ID." AND meta_key='_default_attributes'";
					fwrite($dateihandle, "637 pruefe postmeta:$where\n");
					// wenn _default_attributes noch nicht gefüllt, dynmasich erste Variante als Standardvariante
					if ($post_parent_ID<>0 && $Artikel_Merkmal<>'' && dmc_sql_select_query("meta_value",$table,$where)=="") {
						// Wird serialied hinterlegt
						fwrite($dateihandle, "641 - Hautvariante festlegen\n");
						update_post_meta( $post_parent_ID,'_default_attributes', $Hauptvariante);
					}
				}
				
			
				// Art des Attributes (wie size ) der variante ergaenzen
				if ($Artikel_Variante_Von!='') {
					
				}
				
				// Wenn Artikel is_ebay, dann für WP_Lister freischalten
				if ($Artikel_Merkmale[$Anzahl]=="is_ebay" && ($Artikel_Auspraegungen[$Anzahl]=='1' || $Artikel_Auspraegungen[$Anzahl]=='-1' || $Artikel_Auspraegungen[$Anzahl]=='true')) {
					//wplister_prepare_listing
					fwrite($dateihandle, "Artikel fuer WP-Lister freischalten (wplister_prepare_listing) \n");
					// Prepare a new listing from a WooCommerce product and apply a profile. This hook is available in version 1.5.0.5+.
					//Usage do_action('wplister_prepare_listing', $post_ID, $profile_id );
					$profile_id = 1;
					do_action('wplister_prepare_listing', $post_ID, $profile_id );
					fwrite($dateihandle, "ELEDIGT unter Verwendung Profil ID = $profile_id \n");
				}
			}
			// Sonderfall: Dem Hauptartikel Standard -Varianten-Felder zuweisen
			// hier bei Variantenhauptprodukt
			$use_variantendefaultmerkmale=false;
			if ($use_variantendefaultmerkmale && $variantendefaultmerkmale) {
					// Pruefen, ob Standard Variante gesetzt und ggfls setzen (Atttibute _default_attributes des Hauptartikels vorhanden und gesetzt)
					$table = DB_PREFIX."postmeta";
					$where = "post_id=".$post_ID." AND meta_key='_default_attributes'";
					fwrite($dateihandle, "670 pruefe postmeta variantendefaultmerkmale:$where\n");
					// wenn _default_attributes noch nicht gefüllt, dynmasich erste Variante als Standardvariante
					if (dmc_sql_select_query("meta_value",$table,$where)=="") {
						// Wird serialied hinterlegt
						fwrite($dateihandle, "426 - Hauptvariante festlegen\n");
						update_post_meta( $post_ID,'_default_attributes', $Hauptvariante);
					}
			}
			 
			
			if (USE_WPML==true) {
				// Fremdsprachenmodul WPML
				// Hoechste ID fuer sprachgruppe ermitteln
				fwrite($dateihandle, " Fremdsprachenmodul WPML\n" );	
				$trid=dmc_get_highest_id("trid", DB_PREFIX."icl_translations")+1;
				// SEO URL ermitteln
				$insert_sql_data = array(	
										//	'translation_id' => $translation_id,
											'element_type' => 'post_'.$post_type,
											'element_id' => $post_ID,
											'trid' => $trid,
											'language_code' => 'de',			
											'source_language_code' => null,											
				);
				// Insert in posts durchfuehren
				$mode='INSERTED';
				dmc_sql_insert_array(DB_PREFIX."icl_translations", $insert_sql_data);
			}
			// INSERT INTO `wp_icl_translations` (`translation_id`,`element_type`,`element_id`,`trid`,`language_code`,`source_language_code`) VALUES ('4779','post_product','2152','4311','de',NULL);
			
			// Bild hinzufuenen 
			if ($Artikel_Bilddatei!="") {
				// Wenn $Artikel_SEO_Name angegeben, Bilder von Artikelnummer auf $Artikel_SEO_Name umbenennen
				// dmc_add_woocommerce_image($Artikel_Bilddatei, $post_ID, $Artikel_SEO_Name);
				dmc_add_woocommerce_image($Artikel_Bilddatei, $post_ID, '');
			}
						
			// product_type zuordnen
			if ($Artikel_ID!="") {
				if ($Artikel_ID=="conf")
					$Artikel_ID="variable";
				if ($Artikel_ID!="simple" && $Artikel_ID!="variable" && $Artikel_ID!="grouped" 
				&& $Artikel_ID!="external")
					$Artikel_ID="simple";
				// Wenn noch nicht zugeordnet
				// if (dmc_sql_select_query('term_taxonomy_id',$tables,$where)=='') 
					fwrite($dateihandle, "635 - Artikelart aendern zu  \n");
					wp_set_object_terms($post_ID, $Artikel_ID, 'product_type');
			}
			
			//SET THE PRODUCT TAGS / Schlagwörter zuordnen - Hersteller zB
			/*if ($Hersteller_ID!="") {
				// wp_set_object_terms($productID, array('tag1','tag2','tag3'), 'product_tag');
				// Wenn noch nicht zugeordnet
				// Tags stehen in temps ivm termmeta, term_taxonomy, term_relationships, search_filter_term_results, search_filter_cache
				// if (dmc_sql_select_query('term_taxonomy_id',$tables,$where)=='') 
					fwrite($dateihandle, "421 - product_tag  (hersteller) ergaenzen \n");
					wp_set_object_terms($post_ID, array($Hersteller_ID), 'product_tag');
			}*/
			// ALTERNATIV: Tags aus Artikel_MetaKeyword
			if ($Artikel_MetaKeyword!="") {
				// wp_set_object_terms($productID, array('tag1','tag2','tag3'), 'product_tag');
				// Wenn noch nicht zugeordnet
				// Tags stehen in temps ivm termmeta, term_taxonomy, term_relationships, search_filter_term_results, search_filter_cache
				// if (dmc_sql_select_query('term_taxonomy_id',$tables,$where)=='') 
					// SEO, da WP nicht TAGS in UTF8 schreiben kann
			/*	$d1 = array(" ","Ä", "Ö", "Ü", "ä" , "ö", "ü", "ß","", "o", "ue", "" , "o", "ue", "","<",">","#","\"","'","�",",","&","�","?",";","\\","/","-");
				$d2 = array("-", "Ae","Oe","Ue","ae","oe","ue","sz","Ae","Oe","Ue","ae","oe","ue","sz","_","_","_","_","_","_","_","-","2","-","-","_","_","_");
				$seo_text = str_replace($d1, $d2, $Artikel_MetaKeyword);		 
				$Artikel_MetaKeyword_SEO = preg_replace('/[^0-9a-zA-Z-_]/', '', $Artikel_MetaKeyword);
				*/
				$schlagwoerter =str_replace("|","@",$Artikel_MetaKeyword);
				$schlagwoerter=explode ( '@', $schlagwoerter);
				
				$d1 = array(" ", "Ä", "Ö", "Ü", "ä" , "ö", "ü", "ß", "#", "*");
				$d2 = array("-", "Ae","Oe","Ue","ae","oe","ue","ss", "-", "-");
				$product_tags = str_replace($d1, $d2, $Artikel_MetaKeyword);
				$product_tags =  utf8_encode($product_tags);
				$product_tags = str_replace($d1, $d2, $product_tags);
				$product_tags =  utf8_decode($product_tags);
				
				$product_tags=preg_replace('/[^0-9a-zA-Z-_]/', '_', $product_tags);
				$product_tags=str_replace("|","@",$product_tags);
				$product_tags=explode ( '@', $product_tags);
				//wp_set_object_terms($post_ID, array($Hersteller_ID), 'product_tag');
				wp_set_object_terms($post_ID, $product_tags, 'product_tag');
				$i=0;
				foreach ($product_tags as $value) {
					// Update mit der richtigen Bezeichnung
					dmc_sql_update('terms', "name = '".$schlagwoerter[$i]."'", "name = '".$value."'");
					$i++;
				}
				
			}
			// GGfls in TAGs schreiben: product_shipping_class, product_delivery_time, product_unit, product_price_label
		  
			//_edit_last
			//_edit_locks
			// nicht verwendet (im Mustermandanten)
			/*
			_tax_status (Tax Status)
			_tax_class (Tax Class)
			product_cat (Categories, by Name or ID)
			product_tag (Tags, by Name or ID)
			Custom Fields
			Product Images (By URL or Local File Path)
			_button_text (Button Text, for External Products)
			_product_url (Product URL, for External Products)
			_file_path (File Path, for Downloadable Product)
			_download_expiry (Download Expiration, in Days)
			_download_limit (Download Limit, an integer)
			*/
		} else {
			// ARTIKEL UPDATE
			// Wenn $Aktiv= 'deaktivieren' - dann deaktivieren und abbruch
			/*if ($Aktiv=='deaktivieren' || $Aktiv=='deactivate' || $Aktiv=='inactive') {
				fwrite($dateihandle, "Artikel ".$Artikel_Artikelnr." deaktivieren und Abbruch\n");
				$query = "UPDATE ".DB_PREFIX ."posts"." SET post_status = 'draft' WHERE ID='$post_ID'";		
				dmc_sql_query($query);
				return;
			}
			
			
			*/
			
			// $attach_id = get_post_meta($product->parent_id, "_thumbnail_id", true);
				update_post_meta($post_ID, '_ts_gtin', $Artikel_EAN); // EAN Nummer
				
				update_post_meta($post_ID, '_visibility', $Artikel_Status);	// (Sichtbarkeit wie visible)
				update_post_meta($post_ID, '_regular_price', $Artikel_Preis);	// (Standardpreis)
				update_post_meta($post_ID, '_price', $Artikel_Preis);		// (Preis) -> Wenn Aktionspreis, dann Aktionspreis, sonst regular price
				// _tax_status (Tax Status) _tax_class (Tax Class) im WP Standard NULL
				update_post_meta($post_ID, '_tax_status', $_tax_status );
				// add_post_meta($post_ID, '_tax_class', );
				update_post_meta($post_ID, '_featured', $Artikel_Startseite);		// (Startseitenartikel yes/no)	
				// update_post_meta($post_ID, '_recommended', $Artikel_Startseite);		// (Startseitenartikel yes/no)	
				update_post_meta($post_ID, '_mini_desc', $Artikel_MetaDescription);
				// Standardwerte (ggfls anpassen)
				// Sonderfall nicht relevant
				update_post_meta($post_ID, '_stock', $Artikel_Menge);			
				update_post_meta($post_ID, '_stock_status', $_stock_status);	
				update_post_meta($post_ID, '_manage_stock', $_manage_stock);	
				update_post_meta($post_ID, '_backorders', $_backorders);			
				
				update_post_meta($post_ID, '_unit', $Artikel_VPE);		// VPE Einheit
				//add_post_meta($post_ID, '_unit_base', '');			// VPE Anzahl zB 1
				//add_post_meta($post_ID, '_unit_price', '');			// VPE Preis Standard, zB 5.2
				//add_post_meta($post_ID, '_unit_regular', '');		// VPE Preis, zB 5.2
				//add_post_meta($post_ID, '_unit_price_auto', 'yes');	// Alternativ VPE Automatische Berechnung des VPE-Preises zB yes
				//add_post_meta($post_ID, '_unit_product', '');		// Stückzahl in VPE zB 6
				update_post_meta($post_ID, '_weight', $Artikel_Gewicht);	// (Gewicht)	
				fwrite($dateihandle, "ende update_post_meta \n");	
		
			// update posts durchfuehren
				$table = "posts";
				dmc_sql_update($table,  "post_status='".$post_status."'" ,
				//"post_title='".trim($Artikel_Bezeichnung)."',  
				//post_excerpt='".trim($Artikel_Kurztext)."', post_content='".trim($Artikel_Langtext)."'" , 
				"post_type= 'product' AND ID=".$post_ID);	
				// Update Artikel Beschreibung Texte
				if (UPDATE_DESC=="true") {
					$table = "posts";
					dmc_sql_update($table,  "post_title='".trim($Artikel_Bezeichnung)."', ". 
					"post_excerpt='".trim($Artikel_Kurztext)."', post_content='".trim($Artikel_Langtext)."', ".
					"post_modified = now(), post_modified_gmt = now() ",
					"(post_type= 'product_variation' OR post_type= 'product') AND ID=".$post_ID);		
					
				}
				
				$update_attribute=false;
				if ($update_attribute) {
					// Wenn Merkmale und Auspraegungen gesetzt dann mit den Merkmal Key die Auspraegung schreiben
					// zB pa_farbe, pa_groesse
					if ($Artikel_Merkmal<>'' && substr($Artikel_ID,0,4)!='conf' && $post_ID > 0) {
						$Artikel_Merkmale  = explode ( '@', $Artikel_Merkmal);
						$Artikel_Auspraegungen = explode ( '@', $Artikel_Auspraegung);
						$variantenmerkmale=false;
						// zb update_post_meta($variation_id, 'attribute_pa_size', '2xl');
						for($Anzahl = 0; $Anzahl <= count($Artikel_Merkmale)-1; $Anzahl++) {     
							// ggfls alle verfuegbaren attribute ergaenzen
							/* $avail_attributes = array(
								'xl',
								'l',
								'm',
								's'
							);
							wp_set_object_terms($post_ID, $avail_attributes, 'pa_size');
							*/
							// Attribute anlegen, wenn variantenauswahl attribute, dann bezeichung attribute_pa_... zB attribute_pa_size
						//	$Artikel_Auspraegungen[$Anzahl] = utf8_encode($Artikel_Auspraegungen[$Anzahl]);
							fwrite($dateihandle, "974 Update Attribute - ".$Artikel_Merkmale[$Anzahl]." -> ".$Artikel_Auspraegungen[$Anzahl]."\n");
							if ($Artikel_Variante_Von!='' && substr($Artikel_Merkmale[$Anzahl],0,3)=='pa_') {
								// Variantenartikel
								fwrite($dateihandle, "Varianten Attribute ".$Artikel_Merkmale[$Anzahl]." -> ".$Artikel_Auspraegungen[$Anzahl]."\n");
								$Artikel_Merkmale[$Anzahl] = 'attribute_'.$Artikel_Merkmale[$Anzahl];
							} else if (substr($Artikel_Merkmale[$Anzahl],0,3)=='pa_') {
								// Hauptartikel
								$Artikel_Merkmale[$Anzahl] = str_replace("pa_","",$Artikel_Merkmale[$Anzahl]);
								fwrite($dateihandle, "Objekt Attribute PA - ".$Artikel_Merkmale[$Anzahl]." -> ".$Artikel_Auspraegungen[$Anzahl]."\n");
								// ggfls alle verfuegbaren attribute ergaenzen
								/* ZB $available_attributes = array(
									'l',
									'm',
									's'
								); */
								$available_attributes = explode ( '|', $Artikel_Auspraegungen[$Anzahl]);
								// $log = print_r($available_attributes,true);
								// fwrite($dateihandle, "828 - wp_set_object_terms - available_attributes -> ".$log."\n");
								wp_set_object_terms($post_ID, $available_attributes, $Artikel_Merkmale[$Anzahl]);
							}	else if (substr($Artikel_Merkmale[$Anzahl],0,8)=='metakey:') {
								// Benutzerdefinierte Meta Keys 
								$Artikel_Merkmale[$Anzahl] = str_replace('metakey:', '', $Artikel_Merkmale[$Anzahl]);
							}
							//  
							// ACHTUNG / zu _ ersetzen
						/*	$post_meta_attribute = str_replace('/', '_',  $Artikel_Auspraegungen[$Anzahl]);
							$post_meta_attribute = str_replace(' ', '-',  $post_meta_attribute);
							$post_meta_attribute = str_replace('---', '-',  $post_meta_attribute);
							
							$post_meta_attribute = strtolower($post_meta_attribute);
						*/
							$d1 = array(" ","Ä", "Ö", "Ü", "ä" , "ö", "ü", "ß","<",">","#","\"","'","�",",","&","�","?",";","\\","/","---");
							$d2 = array("-", "Ae","Oe","Ue","ae","oe","ue","sz","_","_","_","_","_","_","_","-","2","-","-","_","_","-");
							$seo_text = str_replace($d1, $d2, $Artikel_Auspraegungen[$Anzahl]);		 
							$post_meta_attribute = preg_replace('/[^0-9a-zA-Z-_]/', '', $seo_text);
								
							// fwrite($dateihandle, "Artikel_SEO_Name $Artikel_SEO_Name.\n");
						
							$post_meta_attribute = strtolower($post_meta_attribute);
								
							if (dmc_entry_exists(DB_PREFIX ."postmeta",'meta_key',$post_meta_attribute,"and","post_id",$post_ID)) {
								$query = "UPDATE ".DB_PREFIX ."postmeta"." SET meta_value='".$Artikel_Auspraegungen[$Anzahl]."' WHERE meta_key='".$post_meta_attribute."' AND post_id= ".$post_ID;
							} else {
								// add_post_meta($post_ID, $Artikel_Merkmale[$Anzahl], $Artikel_Auspraegungen[$Anzahl]);	// attribute und auspraegungen	
								$query = "REPLACE INTO ".DB_PREFIX ."postmeta"." (meta_key,meta_value,post_id) ".
								"VALUES ('".$Artikel_Merkmale[$Anzahl]."','".$post_meta_attribute ."',".$post_ID.")";
							}
							dmc_sql_query($query); 
							
							// Wenn Artikel is_ebay, dann für WP_Lister freischalten
							if ($Artikel_Merkmale[$Anzahl]=="is_ebay" && ($Artikel_Auspraegungen[$Anzahl]=='1' || $Artikel_Auspraegungen[$Anzahl]=='-1' || $Artikel_Auspraegungen[$Anzahl]=='true')) {
								//wplister_prepare_listing NUR Freischalten, wenn noch nicht freigeschaltet
								if (dmc_entry_exits("id", "ebay_auctions", "post_id=".$post_ID) === false) {
									// Prepare a new listing from a WooCommerce product and apply a profile. This hook is available in version 1.5.0.5+.
									//Usage do_action('wplister_prepare_listing', $post_ID, $profile_id );
									$profile_id = 1;
									do_action('wplister_prepare_listing', $post_ID, $profile_id );
									do_action('wplister_relist_item', $post_ID );
									fwrite($dateihandle, "ELEDIGT unter Verwendung Profil ID = $profile_id \n");

								}
							}
							
							// Eigenschaften updaten
							$update_eigenschaften=true;
							if ($update_eigenschaften) {
								// Eigenschaft dem Artikel also Standardartikel zuweisen.
								$Artikel_Merkmale_kurz =  str_replace('pa_', '', $Artikel_Merkmale[$Anzahl] );
								fwrite($dateihandle, "802 Eigenschaften - ".$Artikel_Merkmale_kurz." -> ".$Artikel_Auspraegungen[$Anzahl]."\n");
								// Auswahl Attribute der Variante hinzufuegen
								//$thedata = Array('pa_size'=>Array(
								$thedata[$Artikel_Merkmale_kurz] =  Array(
												'name'=> $Artikel_Merkmale_kurz ,
												'value'=> strtolower($Artikel_Auspraegungen[$Anzahl]),
												'position' => '0', 
												'is_visible' => 1, 
												'is_variation' => 0,
												'is_taxonomy' => 0
											);
								$log = print_r($thedata,true);
								fwrite($dateihandle, "814 - Eigenschaften - thedata = $log\n");
								update_post_meta( $post_ID,'_product_attributes', $thedata);
							
							}
							
						} // end for
						
					}
				}
				
			
			// SONDERFALL $_enable_role_based_price=1 role base price modul
			if ($_enable_role_based_price==1) {
				// Preis1, Preis2, Preis3 fuer die gruppen grosshandel, sonderkonditionen, waescherei
				$kundengruppenpreis["grosshandel"]["regular_price"]=str_replace(".",",",$Artikel_Preis1);
				$kundengruppenpreis["sonderkonditionen"]["regular_price"]=str_replace(".",",",$Artikel_Preis2);
				$kundengruppenpreis["waescherei"]["regular_price"]=str_replace(".",",",$Artikel_Preis3);
				$kundengruppenpreise=serialize($kundengruppenpreis);
				// update_post_meta geht bei serialied array string nicht, bzw ergaenzt diesen
				//	update_post_meta($post_ID, '_role_based_price', $_enable_role_based_price);		// Kundengruppe Preise aktualisieren 
				$query = "UPDATE ".DB_PREFIX ."postmeta"." SET meta_value = '".$kundengruppenpreise."' ".
				"WHERE meta_key='_role_based_price' AND post_id='$post_ID'";
				fwrite($dateihandle, " query mit $query\n");	
				dmc_sql_query($query);
		
			}
		
			$update_images=true;
			if ($Artikel_Bilddatei!="" && $update_images) {
				// Wenn $Artikel_SEO_Name angegeben, Bilder von Artikelnummer auf $Artikel_SEO_Name umbenennen
				// dmc_add_woocommerce_image($Artikel_Bilddatei, $post_ID, $Artikel_SEO_Name);
				dmc_add_woocommerce_image($Artikel_Bilddatei, $post_ID, '');
			}
			
			// UPDATE THE PRODUCT TAGS / Schlagwörter zuordnen - Hersteller zB
			/*if ($Hersteller_ID!="") {
				// wp_set_object_terms($productID, array('tag1','tag2','tag3'), 'product_tag');
				// Wenn noch nicht zugeordnet
				// Tags stehen in temps ivm termmeta, term_taxonomy, term_relationships, search_filter_term_results, search_filter_cache
				// if (dmc_sql_select_query('term_taxonomy_id',$tables,$where)=='') 
					wp_set_object_terms($post_ID, array($Hersteller_ID), 'product_tag');
			}*/
			// ALTERNATIVE: TAGS finden sich in Meta Keywords
			
			// ALTERNATIV: Tags aus Artikel_MetaKeyword
			if ($Artikel_MetaKeyword!="") {
				// wp_set_object_terms($productID, array('tag1','tag2','tag3'), 'product_tag');
				// Wenn noch nicht zugeordnet
				// Tags stehen in temps ivm termmeta, term_taxonomy, term_relationships, search_filter_term_results, search_filter_cache
				// if (dmc_sql_select_query('term_taxonomy_id',$tables,$where)=='') 
					// SEO, da WP nicht TAGS in UTF8 schreiben kann
			/*	$d1 = array(" ","Ä", "Ö", "Ü", "ä" , "ö", "ü", "ß","", "o", "ue", "" , "o", "ue", "","<",">","#","\"","'","�",",","&","�","?",";","\\","/","-");
				$d2 = array("-", "Ae","Oe","Ue","ae","oe","ue","sz","Ae","Oe","Ue","ae","oe","ue","sz","_","_","_","_","_","_","_","-","2","-","-","_","_","_");
				$seo_text = str_replace($d1, $d2, $Artikel_MetaKeyword);		 
				$Artikel_MetaKeyword_SEO = preg_replace('/[^0-9a-zA-Z-_]/', '', $Artikel_MetaKeyword);
				*/
				$schlagwoerter =str_replace("|","@",$Artikel_MetaKeyword);
				$schlagwoerter =str_replace(",","@",$schlagwoerter);
				$schlagwoerter=explode ( '@', $schlagwoerter);
				
				$product_tags=str_replace("|","@",$Artikel_MetaKeyword);
				$product_tags=str_replace(",","@",$product_tags);
				$d1 = array(" ", "Ä", "Ö", "Ü", "ä" , "ö", "ü", "ß", "#", "*");
				$d2 = array("-", "Ae","Oe","Ue","ae","oe","ue","ss", "-", "-");
				$product_tags = str_replace($d1, $d2, $product_tags);
				$product_tags =  utf8_encode($product_tags);
				$product_tags = str_replace($d1, $d2, $product_tags);
				$product_tags =  utf8_decode($product_tags);
				$product_tags=preg_replace('/[^0-9a-zA-Z-_@]/', '_', $product_tags);
				$product_tags=str_replace(",","@",$product_tags); 
				$product_tags=explode ( '@', $product_tags);
				
				//wp_set_object_terms($post_ID, array($Hersteller_ID), 'product_tag');
				wp_set_object_terms($post_ID, $product_tags, 'product_tag');
				$i=0;
				foreach ($product_tags as $value) {
					// Update mit der richtigen Bezeichnung
					dmc_sql_update('terms', "name = '".$schlagwoerter[$i]."'", "name = '".$value."'");
					$i++;
				} 
			} else {
				 fwrite($dateihandle, "1104 Keine Tags vorhanden.\n");
			}
			// GGfls in TAGs schreiben: product_shipping_class, product_delivery_time, product_unit, product_price_label

		} // ende update
		 fwrite($dateihandle, "Artikel_SEO_Name $Artikel_SEO_Name.\n");
		// Kategorie zuordnen
		// Entweder wp_set_object_terms( $post_ID, 'Races', 'product_cat' );
		// term_taxonomy_ids = wp_set_object_terms( $post_ID, (int)$term_taxonomy_id, 'product_cat' );
		// ODER
		if ($Kategorie_ID!='0' && $Kategorie_ID!='' ) {	// && $exits==false
			// Sonderfall Bett|Bett>Bettwäsche
			$Kategorie_ID_plain_array1 = explode ( '@', $Kategorie_ID);
			for($Anzahl = 0; $Anzahl <= count($Kategorie_ID_plain_array1); $Anzahl++) {   
				$Kategorie_ID_plain_array2 = explode ( '>', $Kategorie_ID_plain_array1[$Anzahl]);
				for($Anzahl2 = 0; $Anzahl2 <= count($Kategorie_ID_plain_array2); $Anzahl2++) {   
					// $Kategorie_ID = dmc_prepare_seo_name($Kategorie_ID,'DE');		// SEO Feld ist mit Kategorie_ID gefuellt
					if ($Kategorie_ID_plain_array2[$Anzahl2]!="") {
						$Kategorie_ID_plain_array2[$Anzahl2]=str_replace(".","",$Kategorie_ID_plain_array2[$Anzahl2]);
						fwrite($dateihandle, "Step 0 Kategorie_woo ID =".$Kategorie_ID_plain_array2[$Anzahl2].".\n");
						$term_taxonomy_id=$Kategorie_ID_WOO = dmc_get_category_id($Kategorie_ID_plain_array2[$Anzahl2]);
						fwrite($dateihandle, "Step 1 WOO Kategorie_woo ID =".$Kategorie_ID_WOO.".\n");
						// Wenn 0 und ID aber Numerisch war, dann diese verwenden
						if ($Kategorie_ID_WOO==0 && is_numeric($Kategorie_ID_plain_array2[$Anzahl2]))
							$Kategorie_ID_WOO=$Kategorie_ID_plain_array2[$Anzahl2];
						
						$tables='term_taxonomy AS TT, '.DB_PREFIX.'terms AS T';
						$where = "T.term_id = TT.term_id AND TT.taxonomy = 'product_cat' AND (T.slug = '".$Kategorie_ID."' OR T.name ='".$Kategorie_ID_plain_array2[$Anzahl2]."')";
						$term_taxonomy_id = dmc_sql_select_query('term_taxonomy_id',$tables,$where);
						fwrite($dateihandle, " Step 2 term_taxonomy_id='".$term_taxonomy_id."'.\n");
						// Wenn '' und ID aber Numerisch war, dann diese verwenden
						if ($term_taxonomy_id=='' && is_numeric($Kategorie_ID_plain_array2[$Anzahl2]))
							$term_taxonomy_id=$Kategorie_ID_plain_array2[$Anzahl2];
							
						// Artikel-Kategoriezuordnung
						/*$insert_sql_data = array(	'object_id' => $post_ID,
													'term_taxonomy_id' => $term_taxonomy_id,		
													'term_order' => 0
												);
						// Pruefen ob schon zugeordnet
						$tables=DB_PREFIX.'term_relationships ';
						$where = "object_id = ".$post_ID." AND term_taxonomy_id = '".$term_taxonomy_id."'";
						$object_id = dmc_sql_select_query('object_id',$tables,$where); */
						$term_taxonomy_ids = wp_set_object_terms( $post_ID, (int)$term_taxonomy_id, 'product_cat' );

						if ( is_wp_error( $term_taxonomy_ids ) ) {
							// There was an error somewhere and the terms couldn't be set.
						} else {
							// Success! The post's categories were set.
						}
						 
						// term_relationships
					/*	if ($object_id > 0) {
							fwrite($dateihandle, "Artikel ist bereits der Kategorie ".$Kategorie_ID_WOO." zugeordnet \n");
						} else {	
							fwrite($dateihandle, "Artikel-Kategoriezuordnung\n");
							dmc_sql_insert_array(DB_PREFIX."term_relationships", $insert_sql_data);
							// Anzahl Produkte der Kategorie um 1 hochzaehlen.
							dmc_sql_update(DB_PREFIX.'term_taxonomy', 'count=count+1', "term_taxonomy_id = '".$term_taxonomy_id."'");
							 fwrite($dateihandle, " Step 3 erledigt\n");
						} */
						// Zusaetzlich Hauptkategorie zuordnen
						// Wenn 0 und ID aber Numerisch war, dann diese verwenden
						if ($term_taxonomy_id!='' && is_numeric($term_taxonomy_id)) {
							$term_taxonomy_id_array[0]=(int)$term_taxonomy_id;
							fwrite($dateihandle, "Zusaetzlich Hauptkategorie zuordnen von Kategorie ".$term_taxonomy_id." zugeordnet \n");
							$tables='term_taxonomy AS TT';
							$where = "TT.taxonomy = 'product_cat' AND TT.term_taxonomy_id=$term_taxonomy_id";
							$term_taxonomy_id = dmc_sql_select_query('parent',$tables,$where);
							fwrite($dateihandle, " Step 2 tparent_id='".$term_taxonomy_id."'.\n");
							if ($term_taxonomy_id!='' && is_numeric($term_taxonomy_id)) {
								$term_taxonomy_id_array[1]=(int)$term_taxonomy_id;
								$term_taxonomy_ids = wp_set_object_terms( $post_ID, $term_taxonomy_id_array, 'product_cat' );

								if ( is_wp_error( $term_taxonomy_ids ) ) {
									// There was an error somewhere and the terms couldn't be set.
								} else {
									// Success! The post's categories were set.
								}
							}
						}
						
					}
					
				}
				
			
			}
			
		}
		
		// ggfls BUG abfangen
		dmc_sql_query("delete FROM `".DB_PREFIX."term_relationships` where   term_taxonomy_id=0");
		
		if (DEBUGGER>=50) fwrite($dateihandle, "Artikel schreiben - Ende Laufzeit = ".(microtime(true) - $beginn)."\n");
		
		// Rueckgabe
		$rueckgabe = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
		   "<STATUS>\n" .
		   "  <STATUS_INFO>\n" .
		   "    <ACTION>$action</ACTION>\n" .
		   "    <MESSAGE>OK</MESSAGE>\n" .
		   "    <MODE>$mode</MODE>\n" .
		   "    <ID>$post_ID</ID>\n" .
		   "  </STATUS_INFO>\n" .
		   "</STATUS>\n\n";
		
		echo $rueckgabe;
		fwrite($dateihandle, "dmc_write_art - rueckgabe=".$rueckgabe."\n"); 
				
		return $post_ID;	
	} // end function

	
?>
