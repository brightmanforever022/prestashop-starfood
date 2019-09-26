<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_set_cat_from_tree.php												*
*  inkludiert von dmc_write_cat.php 										*				
*  Kategorie anlegen basierend auf uebergeben Baum String, 					*
*  z.B.: Installation\Fittings\Lötfittings\Übergangsmuffen					*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
15.03.2012
- neu
*/
			// Kategorie vorhanden?
			// Voller Kategoriename sollte im Feld categories_meta_description von cartegories_description abgelegt sein
			// Sonderfall für Kategorietrenner \
			$Kategorie_Bezeichnung = str_replace('\\\\', '\\', $Kategorie_Bezeichnung);		// Backslash (\) durch slash (/) ersetzen
			$tmp = str_replace(KATEGORIE_TRENNER, '/', $Kategorie_Bezeichnung);		// Backslash (\) durch slash (/) ersetzen
			fwrite($dateihandle, "dmc_set_cat_from_tree - Neue Kategoriebezeichnung: ".$tmp." \n");
			if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
				$cmd = "select id_category as categories_id from " . TABLE_CATEGORIES_DESCRIPTION .
						" where meta_description='$tmp'";
			} else {
				$cmd = "select categories_id from " . TABLE_CATEGORIES_DESCRIPTION .
						" where categories_meta_description='$tmp'";
			}
		 
			$sql_query = dmc_db_query($cmd);
			if ($result_query = dmc_db_fetch_array($sql_query))
			{ // Kategorie existiert bereits: Bestehende categories_id ermitteln	
				$Kategorie_ID=$result_query['categories_id'];			
				fwrite($dateihandle, "Kategorie existiert bereits: Bestehende categories_id:".$Kategorie_id);
			} 
			else  
			{ // Kategorie existiert nicht 
				// Kategorie-Bezeichnung ermitteln -> Einzelne Kategorien in Array
				// Kategorien aus $Kategorie_Bezeichnung getrennt durch \ 	rcm
				// fwrite($dateihandle, "KATEGORIE_TRENNER:".KATEGORIE_TRENNER." für Kategorie_Bezeichnung ".$Kategorie_Bezeichnung);
				$Kategorie_array = explode(KATEGORIE_TRENNER, $Kategorie_Bezeichnung);			
				// Backslash entfernen (siehe oben)
				$Kategorie_Bezeichnung = $tmp;
				
				$meta_desc='';
				
				for($Anzahl = 0; $Anzahl < count($Kategorie_array); $Anzahl++) {     // Kategorien und Unterkategorien durchlaufen	     			
					if ($meta_desc=='')
						$meta_desc = $Kategorie_array[$Anzahl];
					else
						$meta_desc .= '/'.$Kategorie_array[$Anzahl];
				
					// Überprüfen, ob Kategorie existiert			
					if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
						if ($Kategorie_Vater_ID	== "1") // Hauptcategorie
							$cmd = "select c.id_category AS categories_id from " . TABLE_CATEGORIES ." as c, ". TABLE_CATEGORIES_DESCRIPTION ." as cd ".
							" where c.id_category = cd.id_category AND c.id_parent = 0 and cd.meta_description='$meta_desc'";
						else // eine der Unterkategorien mit soeben ermittelter parent_id
							$cmd = "select c.id_category AS categories_id from " . TABLE_CATEGORIES ." as c, ". TABLE_CATEGORIES_DESCRIPTION ." as cd ".
							" where c.id_category = cd.id_category AND c.id_parent = $Kategorie_Vater_ID and cd.meta_description='$meta_desc'";	
					} else if(strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) { 
						$cmd = 	"select c.id AS categories_id FROM s_categories AS c LEFT OUTER JOIN s_categories_attributes AS ca ON c.id=ca.categoryID" .
								" where c.metadescription = '$Kategorie_ID' OR c.metadescription like '$Kategorie_ID,%' OR c.metakeywords = '$Kategorie_ID' OR c.metakeywords like '$Kategorie_ID,%'  OR ca.attribute1='$Kategorie_ID' LIMIT 1";
					}else {
						$cmd = "select c.categories_id AS categories_id from " . TABLE_CATEGORIES ." as c, ". TABLE_CATEGORIES_DESCRIPTION ." as cd ".
							" where c.categories_id = cd.categories_id AND cd.categories_meta_description='$meta_desc'";						
					}
					//fwrite($dateihandle, "Abfrage: ".$cmd."\n");
				
					$sql_query = dmc_db_query($cmd);
					if ($result_query = dmc_db_fetch_array($sql_query))
					{ // Kategorie existiert bereits: Bestehende categories_id als Vater_id (für nächsten Schritt) ermitteln	
						
						if ($Kategorie_Vater_ID==0) // NICHT AENDERN
							$Kategorie_Vater_ID=0;
						else
							$Kategorie_Vater_ID=$result_query['categories_id'];			
						fwrite($dateihandle, "Kategorie existiert bereits: Kategorie_Vater_ID:".$Kategorie_Vater_ID);						
					} else { // Kategorie noch nicht existent
						//fwrite($dateihandle, "Kategorie noch nicht existent\n");
				
						// Kategorie anlegen und ID ermitteln   								
						if ($Anzahl == 0) $Kategorie_Vater_ID = 0;   // Erste Kategorie ist Hauptkategorie

						if(strpos(strtolower(SHOPSYSTEM), 'presta') === false) {
							$insert_sql_data = array('parent_id' => $Kategorie_Vater_ID,
											'categories_status' => $Aktiv,
											'sort_order' => $Sortierung);
											
							// Details
							if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false) {
								if (GM_SHOW_QTY_INFO != "" && GM_SHOW_QTY_INFO != "false" && (strtolower(SHOPSYSTEM) == 'gambio' || strpos(strtolower(SHOPSYSTEM), 'gambiogx') !== false))
									$insert_sql_data['gm_show_qty_info'] = GM_SHOW_QTY_INFO;
								for($gruppe = 0; $gruppe <= 10; $gruppe++) {     //  durchlaufen	     
									if (defined(constant('GROUP_PERMISSION_' . $gruppe)))
									if (constant('GROUP_PERMISSION_' . $gruppe)!=''){  	
										 $insert_sql_data[constant('GROUP_PERMISSION_' . $gruppe)] = constant('GROUP_PERMISSION_' . $gruppe);	
									} // end if
								} // END FOR	
							}
							// Weitere Details
							if (CATEGORIES_TEMPLATE != "")
							$insert_sql_data['categories_template'] = CATEGORIES_TEMPLATE;
							if (LISTING_TEMPLATE != "")
							$insert_sql_data['listing_template'] = LISTING_TEMPLATE;
							if (PRODUCTS_SORTING != "")
							$insert_sql_data['products_sorting'] = PRODUCTS_SORTING;
							if (PRODUCTS_SORTING2 != "")
							$insert_sql_data['products_sorting2'] = PRODUCTS_SORTING2;
							
							fwrite($dateihandle, "102 KATEGORIE_ ANLEGEN in TABLE_CATEGORIES parent_id:".$insert_sql_data[parent_id]." categories_status ".$insert_sql_data[categories_status]." sort_order ".$insert_sql_data[sort_order]."\n");
					
							// xtc_db_perform(TABLE_CATEGORIES, $insert_sql_data);
							dmc_sql_insert_array(TABLE_CATEGORIES, $insert_sql_data);
							$Kategorie_ID = dmc_db_get_new_id();  // ID wird auf Basis der letzten per autoincrement eingefügten id (+1) ermittelt
							fwrite($dateihandle, "116 angelegt mit ID= $Kategorie_ID \n");
							
						} else { // presta
							// todo kat_ebene optimieren
							//logik geaendert
							if(strpos(strtolower(SHOPSYSTEM), 'presta') === false && $Anzahl == 0) {
								$Kategorie_Vater_ID = 0;   // Erste Kategorie ist Hauptkategorie
							} else { 
								$Kategorie_Vater_ID = 1;
							}
							//logik gaendert
							/*if ($Kategorie_Vater_ID == 0 || $Kategorie_Vater_ID == 1) {
								$Kategorie_Vater_ID = 1;
								$kat_ebene=1;
							} else {
								$kat_ebene=$kat_ebene;
								$Kategorie_Vater_ID = dmc_get_category_id($Kategorie_Vater_ID);
							}
							
							$insert_sql_data = array(	'id_parent' => $Kategorie_Vater_ID,
														'level_depth' => $kat_ebene,
														'active' => $Aktiv,
														'date_add' => 'now()',
														'date_upd' => 'now()'
														//'position' => $Sortierung
													);
							*/
							
							if($kat_ebene != 1) {
								$Kategorie_Vater_ID = $Kategorie_ID;
								//fwrite($dateihandle, "136 katebene!=1: ".$Kategorie_Vater_ID."\n");
							} else {
								$Kategorie_Vater_ID = 1;			
								//fwrite($dateihandle, "id_category= $Kategorie_ID\n");
								//fwrite($dateihandle, "id_parent => $Kategorie_Vater_ID\n");
							}
							
							$insert_sql_data = array(	
											'id_category' => $Kategorie_ID,
											'id_parent' => $Kategorie_Vater_ID,
											'level_depth' => $kat_ebene,
											'active' => 1,
											'date_add' => 'now()',
											'date_upd' => 'now()'
										);
										
							dmc_sql_insert_array(TABLE_CATEGORIES, $insert_sql_data);
							$Kategorie_ID = dmc_db_get_new_id();  // ID wird auf Basis der letzten per autoincrement eingefügten id (+1) ermittelt
		 
						} // end if presta
								
						// Category Group
						if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
							$insert_sql_data = array(	'id_category' => $Kategorie_ID,
														'id_group' => 1);
							dmc_sql_insert_array(TABLE_CATEGORIES_GROUP, $insert_sql_data);							
						}
						
						// Kategorie Beschreibung anlegen mit Sprache deutsch = 2				
						if(strpos(strtolower(SHOPSYSTEM), 'presta') === false) {
							if (($Anzahl+1) == count($Kategorie_array)) {	
								// übermittelte Kategoriebezeichnung, z.B. Installation\Fittings\Lötfittings\Übergangsmuffen	
								if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
										$insert_sql_data = 
											array(
												'categories_id' => $Kategorie_ID,
												'language_code' => $language_id,
												'categories_name' => $Kategorie_array[$Anzahl],
												'categories_description' => $Kategorie_Beschreibung,
												'categories_meta_description' => $meta_desc);
								} else {
										$insert_sql_data = 
											array('categories_id' => $Kategorie_ID,
													  'language_id' => $language_id,
													  'categories_name' => $Kategorie_array[$Anzahl],
													  'categories_description' => $Kategorie_Beschreibung,
													  'categories_meta_description' => $meta_desc);
								}

							} else {
								// extrahierte Kategorie
								
								if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
									$insert_sql_data = array(
													'categories_id' => $Kategorie_ID,
													'language_code' => $language_id,
													'categories_name' => $Kategorie_array[$Anzahl]);
								} else {
									$insert_sql_data = array('categories_id' => $Kategorie_ID,
													  'language_id' => $language_id,
													  'categories_name' => $Kategorie_array[$Anzahl],
													  'categories_meta_description' => $meta_desc);
								}
							} //  endif
							fwrite($dateihandle, "210 Description angelegen mit Kategorie_Beschreibung= $Kategorie_Beschreibung \n");
							dmc_sql_insert_array(TABLE_CATEGORIES_DESCRIPTION, $insert_sql_data);
							fwrite($dateihandle, "212 erledigt \n");
						} else { // presta
							$seo = dmc_prepare_seo ($Kategorie_array[$Anzahl],"category",$Kategorie_ID,'de');
								// extrahierte Kategorie
								if ($meta_desc=='')
									$meta_desc = $Kategorie_array[$Anzahl];
								else
									$meta_desc .= '/'.$Kategorie_array[$Anzahl];
								for ( $language_id = 1; $language_id <= $no_of_languages; $language_id++ ) {
									$insert_sql_data = array(	
											'id_category' => $Kategorie_ID,
											'id_lang' => $language_id,
											'name' => $Kategorie_array[$Anzahl],
											'description' =>  $Kategorie_Beschreibung,
											'link_rewrite' => $language_id.'/'.$seo,
											'meta_description' => $meta_desc,
											'meta_title' => $meta_desc,
											'meta_keywords' => $meta_desc);	
									dmc_sql_insert_array(TABLE_CATEGORIES_DESCRIPTION, $insert_sql_data);
								} // end for
						} // end if presta
						
						if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
						
							$Kategorie_SEO_Bezeichnung = dmc_prepare_seo ($Kategorie_array[$Anzahl],"category",$Kategorie_ID,'de');
							//fwrite($dateihandle, "229 SEO alt=".$Kategorie_array[$Anzahl]." / neu=".$Kategorie_SEO_Bezeichnung.".\n");
							
							$insert_sql_data = array(				
										'url_md5' => md5($language_id.'/'.$Kategorie_SEO_Bezeichnung),
										'url_text' => $language_id.'/'.$Kategorie_SEO_Bezeichnung,
										'language_code' => $language_id,
										'link_type' => '2',   			//2 = kategorie
										'meta_description' => $meta_desc,
										'meta_title' => $meta_desc,
										'meta_keywords' => $meta_desc,
										'link_id' =>  $Kategorie_ID);		
							if (SHOPSYSTEM_VERSION>=4.2) $insert_sql_data['store_id'] = SHOP_ID;		// Ab veyton 4.2 
			
							dmc_sql_insert_array(TABLE_SEO_URL, $insert_sql_data);			
						}  // extension fuer Vayton -> SEO TABELLE MUSS GEFUELLT WERDEN, sonst wird es nicht angezeigt
						
						fwrite($dateihandle, "kat_id= ".$Kategorie_ID." / Kategorie=".$Kategorie_array[$Anzahl]." mit Vater $Kategorie_Vater_ID angelegt.\n");
						
						// für nächsten Svchritt wird die Kategorie_ID als Kategorie_Vater_ID benötigt
						$Kategorie_Vater_ID = $Kategorie_ID;
						
					} // endif result_query else 
					fwrite($dateihandle, "Kategorie aus Array:".$Kategorie_array[$Anzahl]."\n");
				} // for schleife
				}		// endif result_query else  	
				$sonderkategorie=true;
				$new_cat_id=$Kategorie_ID;
			
?>
	