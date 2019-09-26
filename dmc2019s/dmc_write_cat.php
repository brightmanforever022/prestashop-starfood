<?php
/****************************************************************************************
*                                                              							*
*  dmConnector  for magento shop														*
*  dmc_write_cat.php																	*
*  Kategorie schreiben																	*
*  Copyright (C) 2011-12 DoubleM-GmbH.de												*
*                                                                  						*
*****************************************************************************************/
/*
05.03.12 - Neu
17.08.14 - Unterstuetzung Shopware
*/

defined('VALID_DMC') or die( 'Direct Access to this location is not allowed.' );

	function dmc_write_cat() {
		global $dateihandle, $action, $client;
		// Div variablen initialisieren
		$new_cat_id=0;
		$language_id='2'; 
		$seo="";
		
		// Sonderfall
		$generate_cat_tree=false;
		
		if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false)
			$language_id='1'; 
		fwrite($dateihandle, "dmc_write_cat\n");
		
		// Gepostete Werte ermitteln
		if (is_file('userfunctions/categories/dmc_get_posts.php')) include ('userfunctions/categories/dmc_get_posts.php');
		else include ('functions/categories/dmc_get_posts.php');
	
		fwrite($dateihandle, "dmc_write_cat - Kat=".$Kategorie_Bezeichnung." - mit ID ".$Kategorie_ID." ".date("l d of F Y h:i:s A")."\n");
	
		// Mappings fuer Sortierungen etc
		if (is_file('userfunctions/categories/dmc_mappings.php')) include ('userfunctions/categories/dmc_mappings.php');
		else include ('functions/categories/dmc_mappings.php');
	//	if (DEBUGGER>=50) fwrite($dateihandle, "Kat_id=".$Kategorie_ID." - mit Status =$Aktiv und Vater=$Kategorie_Vater_ID\n");
	


		$mode='INSERTED'; 
		// Anzahl der Fremdsprachen
		if(strpos(strtolower(SHOPSYSTEM), 'woo') !== false || strpos(strtolower(SHOPSYSTEM), 'shopware') !== false || strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false)
			$no_of_languages=1;
		else
			$no_of_languages=dmc_count_languages();
		// Überprüfung, ob Kategorien NICHT Standard, sondern verarbeitet müssen
		// bei Format z.B.: Installation\Fittings\Lötfittings\Übergangsmuffen
		// Definition: ist der Fall, wenn Kategorie ID = 280873 	rcm
		// Fuer woocommerce und shopware noch nicht existent
		if ($Kategorie_ID=="280273"){
			if(strpos(strtolower(SHOPSYSTEM), 'woo') === false && strpos(strtolower(SHOPSYSTEM), 'shopware') === false)
				// Kategorie anlegen basierend auf uebergeben Baum String, z.B.: Installation\Fittings\Lötfittings\Übergangsmuffen
			if (is_file('userfunctions/categories/dmc_set_cat_from_tree.php')) include ('userfunctions/categories/dmc_set_cat_from_tree.php');
				else include ('functions/categories/dmc_set_cat_from_tree.php');
				echo '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
				   "<STATUS>\n" .
				   "  <STATUS_INFO>\n" .
				   "    <ACTION>$action</ACTION>\n" .
				   "    <CODE>0</CODE>\n" .
				   "    <MESSAGE>OK</MESSAGE>\n" .
				   "    <MODE>$mode</MODE>\n" .
				   "    <ID>$Kategorie_ID</ID>\n" .
				   "    <SCRIPT_VERSION_MAJOR>$version_major</SCRIPT_VERSION_MAJOR>\n" .
				   "    <SCRIPT_VERSION_MINOR>$version_minor</SCRIPT_VERSION_MINOR>\n" .
				   "  </STATUS_INFO>\n" .
				   "</STATUS>\n\n";
				
				return $newCategoryId;		
		} // endif
		// IDs überprüfen und exists Flag setzen
		if (is_file('userfunctions/categories/dmc_proove_cat_ids.php')) include ('userfunctions/categories/dmc_proove_cat_ids.php');
		else include ('functions/categories/dmc_proove_cat_ids.php');
		
		// Wenn existiert und nicht aktiv dann loeschen
		if ($Aktiv=='0' && $exists) {
			fwrite($dateihandle, "Kategorie ".$new_cat_id." loeschen bei woocommerce \n");
			 if(strpos(strtolower(SHOPSYSTEM), 'woo') !== false) {
				// Unterkategorien loeschen 
			//	$query = "DELETE FROM ".DB_PREFIX ."terms WHERE term_id in (SELECT term_id FROM `wp_term_taxonomy` WHERE parent=".$new_cat_id.")";
			//	dmc_sql_query($query);
				$query = "DELETE FROM ".DB_PREFIX ."terms WHERE term_id=".$new_cat_id;
				dmc_sql_query($query);
				// Unterkategorien loeschen 
			//	$query = "DELETE FROM ".DB_PREFIX ."term_taxonomy WHERE parent=".$new_cat_id."";
			//	dmc_sql_query($query);
				$query = "DELETE FROM ".DB_PREFIX ."term_taxonomy"." SET `parent` = '".$Kategorie_Vater_ID."'".
						 " WHERE term_id=".$new_cat_id;
				dmc_sql_query($query);
						
			}
			return;
		}
		// Wenn nicht aktiv nicht anlegen
		if ($Aktiv=='0' && !$exists) {
			fwrite($dateihandle, "Kategorie_ID ".$Kategorie_ID." nicht aktiv und nicht anlegen\n");
			return;
		}
		
		if (DEBUGGER>=50) fwrite($dateihandle, "Kategorie_ID=$Kategorie_ID (new_cat_id=$new_cat_id) und VaterID=$Kategorie_Vater_ID, Ebene:$kat_ebene ...");
		// #1 Kategorie anlegen oder updaten
		if (!$exists && !$sonderkategorie)
		{
			if (DEBUGGER>=50) fwrite($dateihandle, "ANLEGEN \n");
			// Sonderfall 
			//  SHOPWARE
			if ($generate_cat_tree==true){
				if (DEBUGGER>=1) fwrite($dateihandle, "'Sonderfall: Kategorie aus Baum $Kategorie_Bezeichnung anlegen \n");
				//  Aufbau Damen>Schuhe , daher anlegen Damen, wenn noch nicht existent und Unterkategorien Schuhe 
				$Kategorie_array = explode(">", $Kategorie_Bezeichnung);			
				// Hautkategorie anlegen, wenn noch nicht existent
				$Haupt_Kategorie_ID = $Kategorie_array[0];
				$Kategorie_Vater_ID=$main_cat_id=dmc_get_category_id($Haupt_Kategorie_ID);
				if ($main_cat_id=='0' || $main_cat_id=='') {
					// Hauptkategorie anlegen SHOPWARE
					// Abfangroutine
					if ($Kategorie_Vater_ID==0) $Kategorie_Vater_ID=1;
					fwrite($dateihandle, "Hauptkategorie ".$Kategorie_array[0]." mit Vater $Kategorie_Vater_ID anlegen ... ");
					$categoryData = array(
						'parentId' => $Kategorie_Vater_ID,
						"name" => $Kategorie_array[0],
						"metaDescription" => $Haupt_Kategorie_ID,
						"metaKeywords" => $Haupt_Kategorie_ID,
						//"cmsHeadline" => "headlineTest",
						//"cmsText" => "cmsTextTest",
						"active" => $Aktiv,
						// "noViewSelect" => true,
						"attribute" => array(
							1 => $Haupt_Kategorie_ID,
							//2 => "Attribut2",
						) 
					);
					$result=$client->post('categories', $categoryData );
					$ausgabe=print_r($result,true);
					fwrite($dateihandle, "Ergebnis=".$Kategorie_Vater_ID.".\n"); 		
					$Kategorie_Vater_ID=dmc_get_category_id($Haupt_Kategorie_ID);
					fwrite($dateihandle, "erfolgt mit id=".$Kategorie_Vater_ID.".\n"); 			
				}
				// Unterkategorie anlegen
				fwrite($dateihandle, "Unterkategorie ".$Kategorie_array[1]." anlegen ... ");
				if ($Kategorie_Vater_ID==0) $Kategorie_Vater_ID=3;
				$categoryData = array(
					'parentId' => $Kategorie_Vater_ID,
					"name" => $Kategorie_array[1],
					"metaDescription" => $Kategorie_MetaD,
					"metaKeywords" => $Kategorie_MetaK,
					//"cmsHeadline" => "headlineTest",
					//"cmsText" => "cmsTextTest",
					"active" => $Aktiv,
					// "noViewSelect" => true,
					"attribute" => array(
						1 => $Kategorie_ID,
						//2 => "Attribut2",
					) 
				);
				$client->post('categories', $categoryData );
				fwrite($dateihandle, "erfolgt.\n"); 
								
			
			} else if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) 
			{ 	
				// presta Array fuellen
				$new_cat_id=dmc_get_highest_id('id_category', DB_TABLE_PREFIX.'category')+1;
				if (is_file('userfunctions/categories/dmc_array_create_presta.php')) include ('userfunctions/categories/dmc_array_create_presta.php');
				else include ('functions/categories/dmc_array_create_presta.php');
				$insert_sql_data['id_category']=$new_cat_id;
				dmc_sql_insert_array('category', $insert_sql_data);
				dmc_sql_insert_array('category_group', $insert_sql_grp_data);	
				dmc_sql_insert_array('category_shop', $insert_sql_shop_data);	
			} else if(strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) 
			{ 	
				if (is_file('userfunctions/products/dmc_art_functions.php')) include ('userfunctions/products/dmc_art_functions.php');
				else include ('functions/products/dmc_art_functions.php');	
				// Virtuemart Array fuellen
				$new_cat_id=dmc_get_highest_id('virtuemart_category_id', TABLE_CATEGORIES)+1;
				fwrite($dateihandle, "95\n");
				if (is_file('userfunctions/categories/dmc_array_create_virtuemart.php')) include ('userfunctions/categories/dmc_array_create_virtuemart.php');
				else include ('functions/categories/dmc_array_create_virtuemart.php');
				fwrite($dateihandle, "98\n");
				// jo340_virtuemart_categories
				dmc_sql_insert_array(TABLE_CATEGORIES, $insert_sql_data);	
				fwrite($dateihandle, "101\n");
				// Description jo340_virtuemart_categories_de_de
				dmc_sql_insert_array(TABLE_CATEGORIES.'_de_de', $insert_sql_desc_data);	
				// categorytree jo340_virtuemart_category_categories`
				dmc_sql_insert_array(str_replace('virtuemart_categories', 'virtuemart_category_categories', TABLE_CATEGORIES), $insert_sql_tree_data);					
			} else if(strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) 
			{ 	
				
				if ($client->get('version') == '' ) {
					$status = "<XML><STATUS>0</STATUS><FEHLER>API Zugang verwehrt</FEHLER></XML>";
					echo $status;
					fwrite($dateihandle, $status);	
				} else {
					fwrite($dateihandle, "Shopware API Version=".$client->get('version') ."\n");
				}
				
				fwrite($dateihandle, "Shopware Kategorie $Kategorie_Bezeichnung anlegen ... ");
				if ($Kategorie_Vater_ID==0) $Kategorie_Vater_ID=3;
				$categoryData = array(
					'parentId' => $Kategorie_Vater_ID,
					"name" => $Kategorie_Bezeichnung,
					"metaDescription" => $Kategorie_MetaD,
					"metaKeywords" => $Kategorie_MetaK,
					//"cmsHeadline" => "headlineTest",
					//"cmsText" => "cmsTextTest",
					"active" => $Aktiv,
					// "noViewSelect" => true,
					"attribute" => array(
						1 => $Kategorie_ID,
						//2 => "Attribut2",
					) 
				);
					$ausgabe=print_r($categoryData,true);
					fwrite($dateihandle, "categoryData=".$ausgabe.".\n"); 		
				
				$ergebnis=$client->post('categories', $categoryData );
			
				$ausgabe=print_r($ergebnis,true);
					fwrite($dateihandle, "categoryData=".$ausgabe.".\n"); 		
					
				fwrite($dateihandle, "erfolgt.\n"); 
								
			} else if(strpos(strtolower(SHOPSYSTEM), 'woo') !== false) 
			{ 	
				// Sonderfall, Katowie aus Baum erstellen, zB Zubehör---Ständer---Notenständer
				if (strpos($Kategorie_Bezeichnung,'___')==true){
					// use sort_order_plugin
					$sort_order_plugin=true;
					if (DEBUGGER>=1) fwrite($dateihandle, "'Sonderfall: woo Kategorie aus Baum $Kategorie_Bezeichnung mit ID  ".$Kategorie_ID."  anlegen (--- enthalten) \n");
					//  Aufbau Damen>Schuhe , daher anlegen Damen, wenn noch nicht existent und Unterkategorien Schuhe 
					$Kategorie_array = explode("___", $Kategorie_Bezeichnung);			// Trennzeichen --- muss URL Konform sein
					$Kategorie_ID_array = explode("___", $Kategorie_ID);			// Trennzeichen --- muss URL Konform sein
					// Hautkategorie anlegen, wenn noch nicht existent
					$Haupt_Kategorie_Name = $Kategorie_array[0];
					$Haupt_Kategorie_ID = $Kategorie_ID_array[0];
					$Haupt_Kategorie_ID = dmc_convert_umlaute($Haupt_Kategorie_ID);
					$Haupt_Kategorie_ID = dmc_generate_seo($Haupt_Kategorie_ID);
					$Kategorie_Vater_ID=$main_cat_id=dmc_get_category_id($Haupt_Kategorie_ID);
					if ($sort_order_plugin==true)
						$Kategorie_sort_order_temp = substr($woo_cat_sortierung,0,3);					// Hauptkategorie immer ersten 3 Ziffern von woo_cat_sortierung
					else 
						$Kategorie_sort_order_temp = "";
					
					// Hauptkategorie anlegen, wenn noch nicht existent
					if ($Kategorie_Vater_ID==0) {
						$Kategorie_Bezeichnung=$Kategorie_array[0];
						$Kategorie_ID=$Haupt_Kategorie_ID;
						$new_cat_id=dmc_get_highest_id('term_id', DB_TABLE_PREFIX.'terms')+1;
						if (is_file('userfunctions/categories/dmc_array_create_woocommerce.php')) 
							include ('userfunctions/categories/dmc_array_create_woocommerce.php');
						else 
							include ('functions/categories/dmc_array_create_woocommerce.php');
						// wp_terms 
						$insert_sql_data['term_id']=$new_cat_id; 
						if (DEBUGGER>=1) fwrite($dateihandle, "'Sonderfall:Kategorie_sort_order_temp  = ".$Kategorie_sort_order_temp."  verwenden \n");
						//  Aufbau Damen>Schuhe , daher anlegen Damen, wenn noch nicht existent und Unterkategorien Schuhe 
				
						if ($sort_order_plugin==true)
							$insert_sql_data['term_order']=$Kategorie_sort_order_temp;
					 
						dmc_sql_insert_array(DB_TABLE_PREFIX.'terms', $insert_sql_data);
						
						// wp_term_taxonomy_id 
						$term_taxonomy_id=dmc_get_highest_id('term_taxonomy_id', DB_TABLE_PREFIX.'term_taxonomy')+1;
						$insert_sql_grp_data['term_id']=$new_cat_id;
						$insert_sql_grp_data['term_taxonomy_id']=$term_taxonomy_id;
						dmc_sql_insert_array(DB_TABLE_PREFIX.'term_taxonomy', $insert_sql_grp_data);	
						$Kategorie_Vater_ID=$new_cat_id;		// dmc_get_category_id($Kategorie_ID_temp);
					}
					
					
					// Unterkategorien
					for ( $i = 1; $i < sizeof($Kategorie_array); $i++ ) {
						// Kategorie pruefen und anlegen
						$Kategorie_Name_temp = $Kategorie_array[$i];
						$Kategorie_ID_temp = "";
						for ( $j = 0; $j <= $i; $j++ ) {
							$Kategorie_ID_temp .= $Kategorie_ID_array[$j]."___";					// Trennzeichen --- muss URL Konform sein
						}
						$Kategorie_ID_temp = substr($Kategorie_ID_temp,0,-3);					// letztes Trennzeichen entfernen --- 
						$Kategorie_ID_temp = dmc_convert_umlaute($Kategorie_ID_temp);
						$Kategorie_ID_temp = dmc_generate_seo($Kategorie_ID_temp);
						
						if ($sort_order_plugin==true)
							$Kategorie_sort_order_temp = substr($woo_cat_sortierung, (3*$i),3);					// Unterkategorie jeweils die nachesten 3 Ziffern von woo_cat_sortierung
						
						fwrite($dateihandle, "264 - Kategorie Name = ".$Kategorie_Name_temp." mit ID pruefen:".$Kategorie_ID_temp." mit VaterID woo=".$Kategorie_Vater_ID." und Sortierung: $Kategorie_sort_order_temp \n");
						// Unterkategorie anlegen, wenn noch nicht existent
						$Kategorie_temp_ID=$new_cat_id=dmc_get_category_id($Kategorie_ID_temp);
						// Hauptkategorie anlegen, wenn noch nicht existent
						if ($Kategorie_temp_ID!=0) { 
							fwrite($dateihandle, "Kategorie existiert bereits mit wooID = ".$new_cat_id." \n");
						} else {
							fwrite($dateihandle, "Kategorie anlegen mit Vater ".$Kategorie_Vater_ID."\n");
							// woocommerce Array fuellen
							$Kategorie_Bezeichnung=$Kategorie_Name_temp;
							$Kategorie_ID=$Kategorie_ID_temp;
							$new_cat_id=dmc_get_highest_id('term_id', DB_TABLE_PREFIX.'terms')+1;
							if (is_file('userfunctions/categories/dmc_array_create_woocommerce.php')) 
								include ('userfunctions/categories/dmc_array_create_woocommerce.php');
							else 
								include ('functions/categories/dmc_array_create_woocommerce.php');
							if ($sort_order_plugin==true)
								$insert_sql_data['term_order']=$Kategorie_sort_order_temp;
							// wp_terms
							$insert_sql_data['term_id']=$new_cat_id; 
							dmc_sql_insert_array(DB_TABLE_PREFIX.'terms', $insert_sql_data);
							
							// wp_term_taxonomy_id 
							$term_taxonomy_id=dmc_get_highest_id('term_taxonomy_id', DB_TABLE_PREFIX.'term_taxonomy')+1;
							$insert_sql_grp_data['term_id']=$new_cat_id;
							$insert_sql_grp_data['term_taxonomy_id']=$term_taxonomy_id;
							dmc_sql_insert_array(DB_TABLE_PREFIX.'term_taxonomy', $insert_sql_grp_data);	
						}
						$Kategorie_Vater_ID=$new_cat_id;		// dmc_get_category_id($Kategorie_ID_temp);
					}
					fwrite($dateihandle, "done ");
					
				} else {
					// woocommerce Array fuellen
					$new_cat_id=dmc_get_highest_id('term_id', DB_TABLE_PREFIX.'terms')+1;
					if (is_file('userfunctions/categories/dmc_array_create_woocommerce.php')) 
						include ('userfunctions/categories/dmc_array_create_woocommerce.php');
					else 
						include ('functions/categories/dmc_array_create_woocommerce.php');
					// wp_terms
					$insert_sql_data['term_id']=$new_cat_id; 
					dmc_sql_insert_array(DB_TABLE_PREFIX.'terms', $insert_sql_data);
					fwrite($dateihandle, "neue term id= ".$new_cat_id);
					// wp_termmeta
					$insert_sql_data_wp_termmeta = array( 'term_id' => $new_cat_id,
										'meta_key' => 'order',
										'meta_value' => '0'
									);
					dmc_sql_insert_array(DB_TABLE_PREFIX.'termmeta', $insert_sql_data_wp_termmeta);
					$insert_sql_data_wp_termmeta = array( 'term_id' => $new_cat_id,
										'meta_key' => 'display_type',
										'meta_value' => ''
									);
					dmc_sql_insert_array(DB_TABLE_PREFIX.'termmeta', $insert_sql_data_wp_termmeta);
					$insert_sql_data_wp_termmeta = array( 'term_id' => $new_cat_id,
										'meta_key' => 'thumbnail_id',
										'meta_value' => '0'
									);
					dmc_sql_insert_array(DB_TABLE_PREFIX.'termmeta', $insert_sql_data_wp_termmeta);
					
					// wp_term_taxonomy_id 
					$term_taxonomy_id=dmc_get_highest_id('term_taxonomy_id', DB_TABLE_PREFIX.'term_taxonomy')+1;
					$insert_sql_grp_data['term_id']=$new_cat_id;
					$insert_sql_grp_data['term_taxonomy_id']=$term_taxonomy_id;
					dmc_sql_insert_array(DB_TABLE_PREFIX.'term_taxonomy', $insert_sql_grp_data);	
				}						 
			} else { 
				// Standard array fuellen
				if (is_file('userfunctions/categories/dmc_array_create_standard.php')) include ('userfunctions/categories/dmc_array_create_standard.php');
				else include ('functions/categories/dmc_array_create_standard.php');
				
				// bei neuer Kategorie -> neue ID ermitteln
				$new_cat_id=dmc_get_highest_id('categories_id', TABLE_CATEGORIES)+1;
				$insert_sql_data['categories_id']=$new_cat_id;
				dmc_sql_insert_array(TABLE_CATEGORIES, $insert_sql_data);	
				// wp_termmeta
				/*	$insert_sql_data_wp_termmeta = array( 'term_id' => $new_cat_id,
										'meta_key' => 'order',
										'meta_value' => '0'
									);
					dmc_sql_insert_array(DB_TABLE_PREFIX.'termmeta', $insert_sql_data_wp_termmeta);
					$insert_sql_data_wp_termmeta = array( 'term_id' => $new_cat_id,
										'meta_key' => 'display_type',
										'meta_value' => ''
									); 
					dmc_sql_insert_array(DB_TABLE_PREFIX.'termmeta', $insert_sql_data_wp_termmeta);
					$insert_sql_data_wp_termmeta = array( 'term_id' => $new_cat_id,
										'meta_key' => 'thumbnail_id',
										'meta_value' => '0'
									);
					dmc_sql_insert_array(DB_TABLE_PREFIX.'termmeta', $insert_sql_data_wp_termmeta);
					*/
				if (DEBUGGER>=50) fwrite($dateihandle, "angelegt mit id ".$insert_sql_data['categories_id']." und vater ".$insert_sql_data['parent_id']." \n");

			} // end if presta
		} else if ($exists && UPDATE_CATEGORY) { // existiert  und UPDATE_CATEGORY
			if (DEBUGGER>=50) fwrite($dateihandle, "AKTUALISIEREN \n");
			// Update Kat 4 Presta  (DESC und SEO weiter unten)
			if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
				if (is_file('userfunctions/categories/dmc_array_update_presta.php')) include ('userfunctions/categories/dmc_array_update_presta.php');
				else include ('functions/categories/dmc_array_update_presta.php');
				dmc_sql_update_array('category', $update_sql_data, "id_category='$new_cat_id'");
			// 	fwrite($dateihandle, "update ".TABLE_CATEGORIES." set 'id_parent' = $Kategorie_Vater_ID where id_category=$new_cat_id\n");
			} else if(strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) {
				fwrite($dateihandle, "Kategorie ID $new_cat_id aktualisieren ... ");
				if ($Kategorie_Vater_ID==0) $Kategorie_Vater_ID=3;				// Standard root "deutsch" Shopware
				$categoryData = array(
					'parentId' => $Kategorie_Vater_ID,
					"name" => $Kategorie_Bezeichnung,
				//	"metaDescription" => $Kategorie_ID,
				//	"metaKeywords" => $Kategorie_MetaK,
					//"cmsHeadline" => "headlineTest",
					//"cmsText" => "cmsTextTest",
					"active" => $Aktiv,
					/* "noViewSelect" => true,
					"attribute" => array(
						1 => "Attribut1",
						2 => "Attribut2",
					) */
				);
				$result=$client->call('categories/' . $new_cat_id , ApiClient::METHODE_PUT, $categoryData);
				fwrite($dateihandle, "erfolgt.\n"); 
					
		
			} else if(strpos(strtolower(SHOPSYSTEM), 'woo') !== false) {
				if (DEBUGGER>=50) fwrite($dateihandle, "woocommerce \n");
				// Update Bezeichnung 
				$query = "UPDATE ".DB_PREFIX ."terms"." SET `name` = '".$Kategorie_Bezeichnung."'".
						 " WHERE term_id=".$new_cat_id;
				dmc_sql_query($query);
				// Update Position
				$query = "UPDATE ".DB_PREFIX ."term_taxonomy"." SET `parent` = '".$Kategorie_Vater_ID."'".
						 " WHERE term_id=".$new_cat_id;
				dmc_sql_query($query);
						
			} else { // Update fuer Standard (incl veyton
				if (is_file('userfunctions/categories/dmc_array_update_standard.php')) include ('userfunctions/categories/dmc_array_update_standard.php');
				else include ('functions/categories/dmc_array_update_standard.php');
				dmc_sql_update_array(TABLE_CATEGORIES, $update_sql_data, "categories_id='$new_cat_id'");
			} // End if update
		} // end if #1
	
		// #2 Kategorie Description und SEO anlegen/update (keine Sonderkategorie)
		if(strpos(strtolower(SHOPSYSTEM), 'woo') === false && strpos(strtolower(SHOPSYSTEM), 'shopware') === false && strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false) 
			if (!$exists || UPDATE_CATEGORY_DESC) {
				// Wenn schon existent, dann ist die neue=die alte Kategorie ID
				if (DEBUGGER >= 50) fwrite($dateihandle, "Standard-Kategorie Text anlegen/updaten\n");
				// Bestehende Daten prüfen -> Kategorie Description bereits existent?
				$desc_exists=dmc_cat_desc_exists($new_cat_id,$language_id);
				// existierende description
				if (DEBUGGER>=1 && ($exists || $desc_exists)) fwrite($dateihandle, "Kategorie $new_cat_id Text aktualisieren\n");
				else if (DEBUGGER>=1) fwrite($dateihandle, "Kategorie $new_cat_id Text neu anlegen\n");
				
				if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
						// Description
					if ($exists || $desc_exists) { // Update
					    $language_id='de';
						if (DEBUGGER>=1) fwrite($dateihandle, "176 Update fuer sprache $language_id\n");
						if (is_file('userfunctions/categories/dmc_array_update_veyton.php')) include ('userfunctions/categories/dmc_array_update_veyton.php');
						else include ('functions/categories/dmc_array_update_veyton.php');	
						xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_desc_array, 'update',"categories_id=$new_cat_id AND language_code = '$language_id'");
						// SEO -> KEIN Update, das SEO ja in den Suchmaschinen eingetragen ist 
					} else { // INSERT
					
						if (DEBUGGER>=1) fwrite($dateihandle, "455 insert $Kategorie_Bezeichnung, category, $new_cat_id,'de' \n");
						// Fuer SEO werden dmc_art_functions.php benoetigt
					//	if (is_file('userfunctions/products/dmc_art_functions.php')) include ('userfunctions/products/dmc_art_functions.php');
					//	else include ('functions/products/dmc_art_functions.php');	
						if (DEBUGGER>=1) fwrite($dateihandle, "459 \n");
						// SEO 
						$Kategorie_SEO_Bezeichnung = dmc_prepare_seo ($Kategorie_Bezeichnung, "category", $new_cat_id,'de');
						if (DEBUGGER>=1) fwrite($dateihandle, "462 Kategorie_SEO_Bezeichnung $Kategorie_SEO_Bezeichnung \n");
					//	fwrite($dateihandle, "SEO alt=".$Kategorie_Bezeichnung." / neu= ".$Kategorie_SEO_Bezeichnung.".\n");
						if (DEBUGGER>=1) fwrite($dateihandle, "464 insert n\n");
						if (is_file('userfunctions/categories/dmc_array_create_veyton.php')) include ('userfunctions/categories/dmc_array_create_veyton.php');
						else include ('functions/categories/dmc_array_create_veyton.php');	
						if (DEBUGGER>=1) fwrite($dateihandle, "467 insert n\n");
						dmc_sql_insert_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_desc_array);	
						if (DEBUGGER>=1) fwrite($dateihandle, "469 insert n\n");
						dmc_sql_insert_array(TABLE_SEO_URL, $sql_data_seo_array);
						if (DEBUGGER>=1) fwrite($dateihandle, "471 insert n\n");
						// wenn fremdsprachen vorhanden, englisch anlegen
						if ($no_of_languages>1) {
							// Description
							$language_id='en';
							// SEO
							if (DEBUGGER>=1) fwrite($dateihandle, "477 insert n\n");
						
							$Kategorie_SEO_Bezeichnung = dmc_prepare_seo($Kategorie_Bezeichnung,"category",$new_cat_id,$language_id);
							if (is_file('userfunctions/categories/dmc_array_create_veyton.php')) include ('userfunctions/categories/dmc_array_create_veyton.php');
							else include ('functions/categories/dmc_array_create_veyton.php');	
							dmc_sql_insert_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_desc_array);	
							dmc_sql_insert_array(TABLE_SEO_URL, $sql_data_seo_array);
						}
						
					}
				} else if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) { // presta
					if (DEBUGGER>=1) fwrite($dateihandle, "126\n");
					if(!$desc_exists) {	// INSERT 
						// SEO Bezeichnung
						if (DEBUGGER>=1) fwrite($dateihandle, "129\n");
						// Fuer SEO werden dmc_art_functions.php benoetigt
						if (is_file('userfunctions/products/dmc_art_functions.php')) include ('userfunctions/products/dmc_art_functions.php');
						else include ('functions/products/dmc_art_functions.php');	
						if (DEBUGGER>=1) fwrite($dateihandle, "133\n");
						
						$seo = dmc_prepare_seo ($Kategorie_Bezeichnung,"category",$new_cat_id,$language_id);
						// extrahierte Kategorie
						if (DEBUGGER>=1) fwrite($dateihandle, "132\n");
						if (is_file('userfunctions/categories/dmc_array_create_presta.php')) include ('userfunctions/categories/dmc_array_create_presta.php');
						else include ('functions/categories/dmc_array_create_presta.php');	
						if (DEBUGGER>=1) fwrite($dateihandle, "135\n");
						for ( $language_id = 1; $language_id <= $no_of_languages; $language_id++ ) {
							if (DEBUGGER>=1) fwrite($dateihandle, "137\n");
							// Standard und Fremdsprachen mit Standard "initialisieren"
							$insert_sql_desc_data['id_lang']=$language_id;
							dmc_sql_insert_array(DB_TABLE_PREFIX.'category_lang', $insert_sql_desc_data);
						} // end for	
						
						if (DEBUGGER>=1) fwrite($dateihandle, "141\n");
					
					} else { // UPDATE nur auf DESC
						if (is_file('userfunctions/categories/dmc_array_update_presta.php')) include ('userfunctions/categories/dmc_array_update_presta.php');
						else include ('functions/categories/dmc_array_update_presta.php');	
						dmc_sql_update_array(DB_TABLE_PREFIX.'category_lang', $update_sql_desc_data,"id_category='$new_cat_id' AND id_lang = '$language_id'");
						//xtc_db_perform('category_lang', $update_sql_desc_data, 'update',"id_category='$new_cat_id' AND id_lang = '$language_id'");
					}
				} else { // end else if -> standard
					if(!$desc_exists) { // INSERT
						if (is_file('userfunctions/categories/dmc_array_create_desc_standard.php')) include ('userfunctions/categories/dmc_array_create_desc_standard.php');
						else include ('functions/categories/dmc_array_create_desc_standard.php');
						dmc_sql_insert_array(TABLE_CATEGORIES_DESCRIPTION, $sql_insert_desc_array);
						// bereits gesetzte Sprache ausnehmen
						$main_language_id=$language_id;
						for ( $language_id = 1; $language_id <= $no_of_languages; $language_id++ ) {
							// Fremdsprachen mit Standard "initialisieren"
							if ($language_id==2) 
								$sql_insert_desc_array['language_id']=$main_language_id;
							else
								$sql_insert_desc_array['language_id']=$language_id;
						
							$sql_insert_desc_array['language_id']=$language_id;
							/*foreach ($sql_insert_desc_array as $Key => $Value)
							{
								$ergebnis .= "    <$Key>$Value</$Key>\n";
							}
							fwrite($dateihandle, "164 main=$main_language_id und aktuell=$language_id => $ergebnis\n"); */
							if ($main_language_id<>$language_id) dmc_sql_insert_array(TABLE_CATEGORIES_DESCRIPTION, $sql_insert_desc_array);
						} // end for
					} else { // UPDATE
						if (is_file('userfunctions/categories/dmc_array_update_desc_standard.php')) 
							include ('userfunctions/categories/dmc_array_update_desc_standard.php');
						else include ('functions/categories/dmc_array_update_desc_standard.php');	
						if ($language_id==0) $language_id = 1;
						dmc_sql_update_array(TABLE_CATEGORIES_DESCRIPTION, $sql_update_desc_array, "categories_id='$new_cat_id' AND language_id = '$language_id'");		
					}
				} // end if
			} // end if ($exists && UPDATE_CATEGORY) 
		
		// ggfls Bildzuordung
		if(strpos(strtolower(SHOPSYSTEM), 'woo') === false && strpos(strtolower(SHOPSYSTEM), 'shopware') === false) 
			if ($Kategorie_Bild!="") {
				if (is_file('userfunctions/set_images.php')) include ('userfunctions/set_images.php');
				else include ('functions/set_images.php');	
				attach_images_to_category($new_cat_id, $Kategorie_Bild, $dateihandle);
			}
  
		// extensions wie SEO tool bluegate, HHG specials etc
		if (is_file('userfunctions/categories/dmc_run_special_functions.php')) include ('userfunctions/categories/dmc_run_special_functions.php');
		else include ('functions/categories/dmc_run_special_functions.php');	
		
		// Presta Kategoriebaum reindexieren
		if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) { // presta
			if (DEBUGGER>=50) fwrite($dateihandle, "Presta Kategoriebaum");
			include('../config/config.inc.php');
			if (DEBUGGER>=50) fwrite($dateihandle, "soll");
			//$category->recalculateLevelDepth(1);
			Category::regenerateEntireNtree();			
			if (DEBUGGER>=50) fwrite($dateihandle, "reindexiert\n");

		}

		if (DEBUGGER>=50) fwrite($dateihandle, " ... angelegt mit id ".$new_cat_id." und vater ".$Kategorie_Vater_ID." \n");

		echo '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
		   "<STATUS>\n" .
		   "  <STATUS_INFO>\n" .
		   "    <ACTION>$action</ACTION>\n" .
		   "    <CODE>0</CODE>\n" .
		   "    <MESSAGE>OK</MESSAGE>\n" .
		   "    <MODE>$mode</MODE>\n" .
		   "    <ID>$Kategorie_ID</ID>\n" .
		   "    <SCRIPT_VERSION_MAJOR>$version_major</SCRIPT_VERSION_MAJOR>\n" .
		   "    <SCRIPT_VERSION_MINOR>$version_minor</SCRIPT_VERSION_MINOR>\n" .
		   "  </STATUS_INFO>\n" .
		   "</STATUS>\n\n";
		
		return $newCategoryId;	
	} // end function
?>