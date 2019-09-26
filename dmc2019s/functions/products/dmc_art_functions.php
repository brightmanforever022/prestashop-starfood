<?php
/************************************************
*                                            	*
*  dmConnector  for shops						*
*  dmc_art_functions.php						*
*  Funktionen fuer Produkte						*
*  Copyright (C) 2011-2017 DoubleM-GmbH.de		*
*                                               *
*************************************************/

// 100311 - erweitert fuer presta
// 100311 - erweitert um dmc_count_languages
// 100311 - erweitert um dmc_validate_image
// 100311 - erweitert um dmc_get_manufacturer_id
// 210411 - erweitert um dmc_attach_category_to_product, welche wiederum fuer presta erweitert wurde.
// 210411 - erweitert um dmc_delete_product
// 210711 - erweitert um dmc_prepare_seo
// 260711 - erweitert um dmc_set_master_flag 
// 121211 - erweitert um  dmc_prepare_seo_name
// 200312 - dmc_count_languages in dmc_db_functions
// 150512 - erweitert um dmc_set_veyton_group_permissions // fuer Urban 
// 220512 - erweitert um dmc_prepare_seo -> Unterstuetzung von Multi Kategorie Zuordnungen 
// 121012 - erweitert um dmc_deactivate_product // Produkt deaktiveren(SQL Statement ...'deaktivieren' AS Aktiv,... )
// 040717 - dmc_get_details_id_by_artno // Artikel-Details-ID ermitteln - Shopware 
// 210717 - dmc_add_woocommerce_image Artikelbilder wooCommerce zuordnen

defined( '_DMC_ACCESSIBLE' ) or die( 'Direct Access to this location is not allowed.' );

	
	// Artikel-ID ermitteln
	function dmc_get_id_by_artno($artno) {
		global  $dateihandle;
		if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
			$query = "SELECT id_product as id from ".TABLE_PRODUCTS."";
			$query .= " WHERE reference ='".$artno."'";
		} else if (strpos(strtolower(SHOPSYSTEM), 'woo') !== false) {
			$query = "SELECT pm.post_id AS id FROM ".DB_PREFIX ."postmeta AS pm";
			$query .= " INNER JOIN ".DB_PREFIX ."posts AS p ON pm.post_id=p.ID ";	
			$query .= " WHERE pm.meta_key='_sku' AND pm.meta_value='".$artno."' AND  (p.post_type='product' or p.post_type='product_variation')";		
		} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
			$query = "SELECT virtuemart_product_id AS id from ".TABLE_PRODUCTS."";
			$query .= " WHERE product_sku ='".$artno."'";
		} else if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) {
			$query = "SELECT articleID AS id FROM s_articles_details";
			$query .= " WHERE ordernumber = '".$artno."'";
			$query .= " OR (ordernumber like '".$artno."-%' AND kind=1) limit 1";
		} else { 
			$query = "SELECT products_id as id from ".TABLE_PRODUCTS."";
			$query .= " WHERE products_model ='".$artno."'";
		}

		$link=dmc_db_connect();
		//	$dateiname=LOG_FILE;	
		//$dateihandle = fopen($dateiname,"a");
		if (DEBUGGER==99)  
			fwrite($dateihandle, "dmc_get_id_by_artno-SQL= ".$query." .\n");
		$id = "";	
		$sql_query = mysqli_query($link,$query);				
		while ($TEMP_ID = mysqli_fetch_assoc($sql_query)) {
			if ($TEMP_ID['id']=='' || $TEMP_ID['id']=='null')
				// IF no ID -> Product not available
				$id = "";
			else
				$id  = $TEMP_ID['id'];
		}		
		return $id;	
	} // end function dmc_get_id_by_artno
	
	
	
	// Bilddateiname ueberpruefen und ggfls korregieren
	function dmc_validate_image($Artikel_Bilddatei) {
	
		global  $dateihandle;
		if (DEBUGGER==99)  fwrite($dateihandle, "dmc_validate_image -> $Artikel_Bilddatei\n");
		// Leerzeichen entfernen
		$Artikel_Bilddatei=trim($Artikel_Bilddatei);
		// wenn \ enthalten, dann pfad und bildbezeichnung hinter letztem \
		if (strrpos($Artikel_Bilddatei, '\\') !== false) {
			$Artikel_Bilddatei=substr($Artikel_Bilddatei,strrpos($Artikel_Bilddatei, '\\')+1);
		} 
		
		// Wenn Bilddatei nicht vorhanden, vielleicht als kleingeschriebene Bilddatei:
		if ($Artikel_Bilddatei!="" && (is_file(DIR_FS_CATALOG.DIR_WS_THUMBNAIL_IMAGES.$Artikel_Bilddatei) || is_file(DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES.$Artikel_Bilddatei))) {			
			// if (DEBUGGER>=50) fwrite($dateihandle, "Bild $Artikel_Bilddatei vorhanden\n");
		} else {		
			if (DEBUGGER>=50) fwrite($dateihandle, "Bild nicht vorhanden:".DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES.$Artikel_Bilddatei."\n");
			$Artikel_Bilddatei= str_replace ( 'JPG', 'jpg', $Artikel_Bilddatei );
			$Artikel_Bilddatei= str_replace ( 'GIF', 'gif', $Artikel_Bilddatei );
			//fwrite($dateihandle, "Bild Name geaendert auf: $Artikel_Bilddatei .\n");
			if ($Artikel_Bilddatei!="" && (is_file(DIR_FS_CATALOG.DIR_WS_THUMBNAIL_IMAGES.$Artikel_Bilddatei) || is_file(DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES.$Artikel_Bilddatei))) {	
				// fwrite($dateihandle, "79 Bild vorhanden\n");
			} else {
				$Artikel_Bilddatei=strtolower($Artikel_Bilddatei); // Wenn Bilddatei nicht vorhanden, vielleicht als kleingeschriebene Bilddatei:
				// fwrite($dateihandle, "82 - Bild Name geändert auf: $Artikel_Bilddatei .\n");
			}
			if ($Artikel_Bilddatei!="" && (is_file(DIR_FS_CATALOG.DIR_WS_THUMBNAIL_IMAGES.$Artikel_Bilddatei) || is_file(DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES.$Artikel_Bilddatei))) {	
				// fwrite($dateihandle, "85 Bild vorhanden\n");
			} else {
				//$Artikel_Bilddatei= str_replace ( 'jpg', 'JPG', $Artikel_Bilddatei );
				$Artikel_Bilddatei= str_replace ( 'gif', 'GIF', $Artikel_Bilddatei );
				// fwrite($dateihandle, "89 - Bild Name geändert auf: $Artikel_Bilddatei .\n");
			}
		} 
		
		return $Artikel_Bilddatei;	
	} // end function dmc_validate_image
	
	// Hersteller_ID ermitteln -> bei Bedarf Hersteller anlegen
	function dmc_get_manufacturer_id($Hersteller_ID,$no_of_languages) {
	
		global  $dateihandle;

		if (DEBUGGER>=1) fwrite($dateihandle, "dmc_get_manufacturer_id - Hersteller ".$Hersteller_ID." ...\n"); 
		// Nur ausführen, wenn Hersteller_ID keine Zahl, d.h. EINE WIRKLICHE ID
		if (!is_numeric($Hersteller_ID)) {
		
			if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
				// id_manufacturer 	name 	date_add 	date_upd
				$cmd = "select id_manufacturer as manufacturers_id from " . TABLE_MANUFACTURERS .
				" where name='$Hersteller_ID'";
			} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
				// id_manufacturer 	name 	date_add 	date_upd
				$cmd = "select virtuemart_manufacturer_id AS manufacturers_id FROM " . TABLE_MANUFACTURERS_LANG .
				" where mf_name='$Hersteller_ID'";
			} else {
				$cmd = "select manufacturers_id from " . TABLE_MANUFACTURERS .
				" where manufacturers_name='$Hersteller_ID'";
			}			

			if (DEBUGGER>=1) fwrite($dateihandle, "Hersteller-id ermitteln ".$cmd." ...\n"); 
		
		    $sql_query = dmc_db_query($cmd);
			// Wenn exisitiert
		    if ($Hersteller = dmc_db_fetch_array($sql_query))
		    {
				// Hersteller exisitert bereits, ID aus Datenbabnk zuordnen
		      $Hersteller_ID = $Hersteller['manufacturers_id'];
			  if (DEBUGGER>=1) fwrite($dateihandle, "Hersteller existiert mit id: ".$Hersteller_ID." ...\n"); 
			} else  { 
				// Hersteller mit neuer  ID anlegen   		( todo virtuemart)
				$Hersteller_Name=$Hersteller_ID;
				if (DEBUGGER>=1) fwrite($dateihandle, "Hersteller ".$Hersteller_Name." mit neuer ID anlegen ...\n"); 
				if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
					// id_manufacturer 	name 	date_add 	date_upd
					$Hersteller_ID = dmc_get_highest_id("id_manufacturer",TABLE_MANUFACTURERS)+1;
					$insert_sql_data = array('id_manufacturer'=> $Hersteller_ID, 'name' => $Hersteller_Name,
											 'date_add' => 'now()', 'date_add' => 'now()');
					dmc_sql_insert_array(TABLE_MANUFACTURERS, $insert_sql_data);
					for ($i=1; $i<=$no_of_languages; $i++) {
						// id_manufacturer 	id_lang 	description 	short_description 	meta_title 	meta_keywords 	meta_description
						$insert_sql_data = array('id_manufacturer'=> $Hersteller_ID, 'id_lang' => $i);
						dmc_sql_insert_array(TABLE_MANUFACTURERS_LANG, $insert_sql_data);
					}
				} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
					// virtuemart_manufacturer_id	 virtuemart_manufacturercategories_id	 hits	 published	 created_on	 created_by	 modified_on	 modified_by
					$Hersteller_ID = dmc_get_highest_id("virtuemart_manufacturer_id",TABLE_MANUFACTURERS)+1;
					$insert_sql_data = array('virtuemart_manufacturer_id'=> $Hersteller_ID, 'published' => 1,
				 							 'created_on' => 'now()', 'modified_on' => 'now()');
					dmc_sql_insert_array(TABLE_MANUFACTURERS, $insert_sql_data);
					// virtuemart_manufacturer_id	 mf_name	 mf_email	 mf_desc	 mf_url	Index slug
					$slug=dmc_prepare_seo_name($Hersteller_Name,'de');
					$insert_sql_data = array('virtuemart_manufacturer_id'=> $Hersteller_ID, 'mf_name' => $Hersteller_Name,
											 'slug' => $slug);
					dmc_sql_insert_array(TABLE_MANUFACTURERS_LANG, $insert_sql_data);					
				} else {
					$Hersteller_ID = dmc_get_highest_id("manufacturers_id", TABLE_MANUFACTURERS)+1;
					$insert_sql_data = array('manufacturers_id'=> $Hersteller_ID, 'manufacturers_name' => $Hersteller_Name);
					dmc_sql_insert_array(TABLE_MANUFACTURERS, $insert_sql_data);
					// Ergaenzungen fuer Veyton
					if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
						// deutsch
						$insert_sql_data = array('manufacturers_id'=> $Hersteller_ID, 'language_code' => 'de');
						dmc_sql_insert_array(TABLE_MANUFACTURERS_DESCRIPTION, $insert_sql_data);
						// english initial 
						$insert_sql_data = array('manufacturers_id'=> $Hersteller_ID, 'language_code' => 'en');
						dmc_sql_insert_array(TABLE_MANUFACTURERS_DESCRIPTION, $insert_sql_data);
						// Shop Berechtigungen
						// $insert_sql_data = array('pid'=> $Hersteller_ID, 'permission' => '1', 'pgroup' => 'shop_1');
						// dmc_sql_insert_array('xt_manufacturers_permission', $insert_sql_data);
					}
				}
				if (DEBUGGER>=1) fwrite($dateihandle, "Hersteller ".$Hersteller_Name." mit id=".$Hersteller_ID." wurde angelegt "); 
		
				$Hersteller_ID = dmc_db_get_new_id();  // ID wird auf Basis der letzten per autoincrement eingefuegten id (+1) ermittelt
		
				if (DEBUGGER>=1) fwrite($dateihandle, "mit Hersteller_ID = ".$Hersteller_ID.". \n");		
				// TODO
				// uebergabe mehrerer Parameter aus WaWi, wie Link, Bild etc.
				// z.B. $Hersteller_ID = "DoubleM\www.doublem-gmbh.de\dm.jpg";                         
				// Bereucksichtigung von Fremdsprachen in Tabelle manufacturers_info
				// list ($manufacturers_name, $manufacturers_link, $manufacturers_image) = split ('[\]', $Hersteller_ID);
				// echo "Name: $manufacturers_name; Link: $manufacturers_link; Bild: $manufacturers_image<br>\n";
			}
		} // endif is_numeric
		
		return $Hersteller_ID;
	} // end function dmc_get_manufacturer_id
	
	// Produkt-Kategoriezuordnung
	function dmc_attach_category_to_product($Kategorie_ID, $Artikel_ID, $dateihandle) {
		
			if (DEBUGGER>=1) fwrite($dateihandle, "// attach_category_to_product $Kategorie_ID, $Artikel_ID, Kategorie eintragen, wenn Artikel neu und zugewiesen\n");
	     	// ueBERPrueFEN; OB SONDERKATEGORIE
			if (DEBUGGER>=1) fwrite($dateihandle, "/// UEBERPRUEFEN; OB SONDERKATEGORIE \n");
			if (is_numeric($Kategorie_ID)) {
				if (DEBUGGER>=1) fwrite($dateihandle, "/// Standardkategorie ID\n");
				// Kategorie_ID ist eine Zahl, daher KEINE Sonderkategorie				
				// ueberpruefen, ob Produkt Kategorien zugeordnet sind

				if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)	
					$cmd = "select id_product as products_id,id_category as categories_id, position from " . 
						TABLE_PRODUCTS_TO_CATEGORIES . " where " .
						"id_product='$Artikel_ID'";			
				else
					$cmd = "select products_id,categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where " .
						"products_id='$Artikel_ID'";
					 
					fwrite($dateihandle, "/// 192 $cmd^1\n");
		        // Kategorie-Zuordnungen lueschen, wenn bereits existent
		        $desc_query = dmc_db_query($cmd);
		        if (($desc = dmc_db_fetch_array($desc_query)))
		        {
					if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)
						$cmd = "delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where " .
						"id_product='$Artikel_ID'";
					else 
						$cmd = "delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where " .
						"products_id='$Artikel_ID'";
					dmc_db_query($cmd);
		        }
				
			//	fwrite($dateihandle, "/// 207 $cmd \n");
				if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
					// Kategoriezuordnung eintragen
					$insert_sql_data= array(
			            'products_id' => $Artikel_ID,
			            'categories_id' => $Kategorie_ID,
			            'master_link' => '1');
				} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
					// Kategoriezuordnung eintragen
					$insert_sql_data= array(
			            'id_product' => $Artikel_ID,
			            'id_category' => $Kategorie_ID,
						'position' => "SELECT MAX(position)+1 FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE id_category = $Kategorie_ID"
						);
				} else {
					// Kategoriezuordnung eintragen
					$insert_sql_data= array(
			            'products_id' => $Artikel_ID,
			            'categories_id' => $Kategorie_ID);
				}
				dmc_sql_insert_array(TABLE_PRODUCTS_TO_CATEGORIES, $insert_sql_data);
fwrite($dateihandle, "/// 227 $cmd^1\n");
				
		        // Bestehende Kategorie laden 
		        if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)	
					$cmd = "select id_product as products_id,id_category as categories_id, position from " . 
						TABLE_PRODUCTS_TO_CATEGORIES . " where " .
						"id_product='$Artikel_ID' AND id_category='$Kategorie_ID'";			
				else
					$cmd = "select products_id,categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where " .
					"products_id='$Artikel_ID' and categories_id='$Kategorie_ID'";
		        fwrite($dateihandle, "/// 237 $cmd^1\n");

				// Nur eintragen, wenn diese Kategorie noch nicht zugeordnet ist
		        $desc_query = dmc_db_query($cmd);
		        if (!($desc = dmc_db_fetch_array($desc_query)))
		        { 
				  	if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
					// Kategoriezuordnung eintragen
					$insert_sql_data= array(
			            'products_id' => $Artikel_ID,
			            'categories_id' => $Kategorie_ID,
			            'master_link' => '1');
					} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
					// Kategoriezuordnung eintragen
					$insert_sql_data= array(
			            'id_product' => $Artikel_ID,
			            'id_category' => $Kategorie_ID,
						'position' => "SELECT MAX(position)+1 FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE id_category = $Kategorie_ID");
					} else {
						// Kategoriezuordnung eintragen
						$insert_sql_data= array(
				            'products_id' => $Artikel_ID,
				            'categories_id' => $Kategorie_ID);
					}

		          dmc_sql_insert_array(TABLE_PRODUCTS_TO_CATEGORIES, $insert_sql_data);
		         }
				
			} else if (is_numeric(str_replace(',','',$Kategorie_ID)) && $exists == 0) { 
				if (DEBUGGER>=1) fwrite($dateihandle, "/// SONDERKATEGORIE 2\n");
				// Kategorie_ID besteht aus MEHREREN  Zahlen, daher mehrere Kategorien zuordnen, z.B. 65,81				
				// Bis zu drei Kategorien
				list ($Kategorie_ID_1, $Kategorie_ID_2, $Kategorie_ID_3) = split ('[,]', $Kategorie_ID);
				if (DEBUGGER>=1) fwrite($dateihandle, "Kategorie_ID_1=".$Kategorie_ID_1." - Kategorie_ID_2=".$Kategorie_ID_2." - Kategorie_ID_3=".$Kategorie_ID_3." mit products_id=".$Artikel_ID." \n");				
					for ($i=1; $i<=3; $i++) {
						if ($i==1) $Kategorie_ID = $Kategorie_ID_1;
						if ($i==2) $Kategorie_ID = $Kategorie_ID_2;
						if ($i==3) $Kategorie_ID = $Kategorie_ID_3;
						if (DEBUGGER>=1) fwrite($dateihandle, "Kategorie_ID=".$Kategorie_ID." eintragen fuer products_id=".$Artikel_ID." \n");				
						if ($Kategorie_ID == "") break; // Abbruch, wenn keine Kategorie_ID
						if (DEBUGGER>=1) fwrite($dateihandle, "Kategorie_ID=".$Kategorie_ID." eintragen fuer products_id=".$Artikel_ID." \n");				
						// ueberpruefen, ob Produkt Kategorien zugeordnet sind
						if ($i==1) {
					        if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)	
								$cmd = "select id_product as products_id,id_category as categories_id, position from " . 
									TABLE_PRODUCTS_TO_CATEGORIES . " where " .
									"id_product='$Artikel_ID'";			
							else 
								$cmd = "select products_id,categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . 
								" where " . "products_id='$Artikel_ID'";
					        // Kategorie-Zuordnungen loschen, wenn bereits existent
					        $desc_query = dmc_db_query($cmd);
					        if (($desc = dmc_db_fetch_array($desc_query)))
					        { 
								if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)
									$cmd = "delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where " .
									"id_product='$Artikel_ID'";
								else 
									$cmd = "delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where " .
									"products_id='$Artikel_ID'";
								dmc_db_query($cmd);
					         } 
							  if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
								// Kategoriezuordnung eintragen
								$insert_sql_data= array(
									'products_id' => $Artikel_ID,
									'categories_id' => $Kategorie_ID,
									'master_link' => '1');
								}else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
								// Kategoriezuordnung eintragen
								$insert_sql_data= array(
									'id_product' => $Artikel_ID,
									'id_category' => $Kategorie_ID,
									'position' => "SELECT MAX(position)+1 FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE id_category = $Kategorie_ID");
								} else {
									// Kategoriezuordnung eintragen
									$insert_sql_data= array(
										'products_id' => $Artikel_ID,
										'categories_id' => $Kategorie_ID);
								}
								dmc_sql_insert_array(TABLE_PRODUCTS_TO_CATEGORIES, $insert_sql_data);
						} else {
							// Kategoriezuordnung eintragen 
							if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
								// Kategoriezuordnung eintragen
								$insert_sql_data= array(
					            'products_id' => $Artikel_ID,
					            'categories_id' => $Kategorie_ID,
					            'master_link' => '1');
							} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
								// Kategoriezuordnung eintragen
								$insert_sql_data= array(
								'id_product' => $Artikel_ID,
								'id_category' => $Kategorie_ID,
								'position' => "SELECT MAX(position)+1 FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE id_category = $Kategorie_ID");
							} else {
								// Kategoriezuordnung eintragen
								$insert_sql_data= array(
						            'products_id' => $Artikel_ID,
						            'categories_id' => $Kategorie_ID);
							}
					        dmc_sql_insert_array(TABLE_PRODUCTS_TO_CATEGORIES, $insert_sql_data);

							// Produkt der Kategorie  zuordnen 
							if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)	
								$cmd = "select id_product as products_id,id_category as categories_id, position from " . 
									TABLE_PRODUCTS_TO_CATEGORIES . " where " .
									"id_product='$Artikel_ID' AND id_category='$Kategorie_ID'";			
							else
								$cmd = "select products_id,categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where " .
								"products_id='$Artikel_ID' and categories_id='$Kategorie_ID'";
		       
							$sql_query = dmc_db_query($cmd);
							// Nur eintragen, wenn diese Kategorie noch nicht zugeordnet ist
							if (!($desc = dmc_db_fetch_array($sql_query)))
							{ 
								if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
									// Kategoriezuordnung eintragen
									$insert_sql_data= array(
									'products_id' => $Artikel_ID,
									'categories_id' => $Kategorie_ID,
									'master_link' => '1');
								} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
									// Kategoriezuordnung eintragen
									$insert_sql_data= array(
									'id_product' => $Artikel_ID,
									'id_category' => $Kategorie_ID,
									'position' => "SELECT MAX(position)+1 FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE id_category = $Kategorie_ID");
								} else {
									// Kategoriezuordnung eintragen
									$insert_sql_data= array(
										'products_id' => $Artikel_ID,
										'categories_id' => $Kategorie_ID);
								}
							       dmc_sql_insert_array(TABLE_PRODUCTS_TO_CATEGORIES, $insert_sql_data);
							}
						}
					} // end for				
			} else {
				if (DEBUGGER>=1) fwrite($dateihandle, "/// SONDERKATEGORIE 3\n");
				// Kategorie_ID ist keine reine Zahl, daher EINE Sonderkategorie, z.B. Heizung\Fussboden\Rohre			
				// Backslash entfernen
				$Kategorie_ID = str_replace(KATEGORIE_TRENNER,'/',utf8_decode($Kategorie_ID));
				 // Bestehende Kategorie laden ueber Zuweisung in Kategorie Description meta_desc
				 // Kategorie_id ermitteln
				$cmd = "select distinct categories_id from ".TABLE_CATEGORIES_DESCRIPTION." where " .
		          "categories_meta_description='$Kategorie_ID'";
				$sql_query = dmc_db_query($cmd);
				 
				if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)	
					$cmd = "select distinct id_category as categories_id from " . 
							TABLE_CATEGORIES_DESCRIPTION . " where " .
							"meta_description='$Kategorie_ID'";			
				else
					$cmd = "select distinct categories_id from ".TABLE_CATEGORIES_DESCRIPTION." where " .
						"categories_meta_description='$Kategorie_ID'";
				
				// Kategorie existent
				if (($result_query = dmc_db_fetch_array($sql_query))) {
					$Kategorie_ID = $result_query['categories_id'];		// "Korrekte" Kategorie ID
					// Produkt der Kategorie  zuordnen
			        // Produkt der Kategorie  zuordnen 
					if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)	
						$cmd = "select id_product as products_id,id_category as categories_id, position from " . 
						TABLE_PRODUCTS_TO_CATEGORIES . " where " .
						"id_product='$Artikel_ID' AND id_category='$Kategorie_ID'";			
					else
						$cmd = "select products_id,categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where " .
						"products_id='$Artikel_ID' and categories_id='$Kategorie_ID'";
					$sql_query = dmc_db_query($cmd);
					// Nur eintragen, wenn diese Kategorie noch nicht zugeordnet ist
			        if (!($desc = dmc_db_fetch_array($sql_query)))
			        {
						if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) { 
							// Kategoriezuordnung eintragen
							$insert_sql_data= array(
								'products_id' => $Artikel_ID,
								'categories_id' => $Kategorie_ID,
								'master_link' => '1');
						} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
							// Kategoriezuordnung eintragen
							$insert_sql_data= array(
								'id_product' => $Artikel_ID,
								'id_category' => $Kategorie_ID,
								'position' => "SELECT MAX(position)+1 FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE id_category = $Kategorie_ID");
						} else {
							// Kategoriezuordnung eintragen
							$insert_sql_data= array(
					            'products_id' => $Artikel_ID,
					            'categories_id' => $Kategorie_ID);
						}
						dmc_sql_insert_array(TABLE_PRODUCTS_TO_CATEGORIES, $insert_sql_data);
			        }
				} // endif Kategorie zuordnen 
				// todo -> else ... (Sonder)Kategorie existiert noch nicht, muss angelegt werden.
			} // endif sonderkategorie
	      } // ende function dmc_attach_category_to_product($Kategorie_ID, $Artikel_ID, $dateihandle)

	// Produkt loeschen
	function dmc_delete_product($Artikel_ID)
	{
		if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
			dmc_db_query("delete from ".TABLE_PRODUCTS." where products_id='$Artikel_ID'");
			dmc_db_query("delete from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='$Artikel_ID'");
			dmc_db_query("delete from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='$Artikel_ID'");
		} if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
			dmc_db_query("delete from ".TABLE_PRODUCTS." WHERE virtuemart_product_id='$Artikel_ID'");
			dmc_db_query("delete from ".TABLE_PRODUCTS_DESCRIPTION." WHERE virtuemart_product_id='$Artikel_ID'");
			dmc_db_query("delete from ".TABLE_PRODUCTS_PRICES." WHERE virtuemart_product_id='$Artikel_ID'");
			dmc_db_query("delete from ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE virtuemart_product_id='$Artikel_ID'");
		} else {
			 dmc_db_query("delete from ".TABLE_PRODUCTS." where products_id='$Artikel_ID'");
			 if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false)
				dmc_db_query("delete from ". TABLE_PRODUCTS_ATTRIBUTES ." where products_id='$Artikel_ID'");
			 if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false)
				dmc_db_query("delete from products_content where products_id='$Artikel_ID'");
			 dmc_db_query("delete from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='$Artikel_ID'");
			 dmc_db_query("delete from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='$Artikel_ID'");
			if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false)
				dmc_db_query("delete from products_xsell where products_id='$Artikel_ID'");
			if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false)
				dmc_db_query("DELETE FROM " . TABLE_SPECIALS . "  WHERE products_id='$Artikel_ID'");
			 if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false)
				dmc_db_query("delete from " . TABLE_CUSTOMERS_BASKET . "  where products_id='$Artikel_ID'");
			 if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false) 
				dmc_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . "  where products_id='$Artikel_ID'");
			 if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false) 
				dmc_db_query("delete from " . TABLE_REVIEWS . "  where products_id='$Artikel_ID'");
			 if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
				 dmc_db_query("delete from xt_seo_url  where link_id='$Artikel_ID'");
				 dmc_db_query("delete from xt_plg_products_to_attributes  where products_id='$Artikel_ID'");
			 }
				 
			 // SEO Tool von Bluegate
			 if (file_exists(DIR_FS_INC.'bluegate_seo.inc.php')) dmc_db_query("delete from bluegate_seo_url where products_id='$Artikel_ID'");
			 if (TABLE_PRICE1<>'') dmc_db_query("DELETE FROM ".TABLE_PRICE1." where products_id='$Artikel_ID'");
			 if (TABLE_PRICE2<>'') dmc_db_query("DELETE FROM ".TABLE_PRICE2." where products_id='$Artikel_ID'");
			 if (TABLE_PRICE3<>'') dmc_db_query("DELETE FROM ".TABLE_PRICE3." where products_id='$Artikel_ID'");
			 if (TABLE_PRICE4<>'') dmc_db_query("DELETE FROM ".TABLE_PRICE4." where products_id='$Artikel_ID'");
			 if (TABLE_PRICE5<>'') dmc_db_query("DELETE FROM ".TABLE_PRICE5." where products_id='$Artikel_ID'");
			 if (TABLE_PRICE6<>'') dmc_db_query("DELETE FROM ".TABLE_PRICE6." where products_id='$Artikel_ID'");
			 if (TABLE_PRICE7<>'') dmc_db_query("DELETE FROM ".TABLE_PRICE7." where products_id='$Artikel_ID'");
			 if (TABLE_PRICE8<>'') dmc_db_query("DELETE FROM ".TABLE_PRICE8." where products_id='$Artikel_ID'");
			 if (TABLE_PRICE9<>'') dmc_db_query("DELETE FROM ".TABLE_PRICE9." where products_id='$Artikel_ID'"); 
		} // end if
	} // end function dmc_delete_product
	
	// Suchmaschinen-URL fuer product oder category
	function dmc_prepare_seo($text,$typus,$cat_id,$language)
	{
		global  $dateihandle;
		fwrite($dateihandle, "*** dmc_art_functions dmc_prepare_seo\n");
				
		// id anhaengen, damit eindeutiger text
		if (strpos(strtolower(SHOPSYSTEM), 'veyton') === false 
			&& strpos(strtolower(SHOPSYSTEM), 'presta') === false
			&& strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false			
			&& strpos(strtolower(SHOPSYSTEM), 'gambiogx') === false) {
			$text = $cat_id."_".$text;
			// if (DEBUGGER>=1) fwrite($dateihandle, "/// seo 488=$text\n");
		} else { // bei Veyton weiterhin die categorie_struktur ergaenzen
			// bei produkten zunaechst die kategorie_id ermitteln
			// if (DEBUGGER>=1) fwrite($dateihandle, "/// seo 492 cat_id=$cat_id");
			// dmc_prepare_seo -> Unterstuetzung von Multi Kategorie Zuordnungen 
			if(strpos(strtolower(SHOPSYSTEM), 'presta') === false) {	// NOICHT BEI PRESTA
				$Kategorie_IDs=explode (KATEGORIE_TRENNER, $cat_id);
				$cat_id = $Kategorie_IDs[0];
			
				if (!is_numeric($cat_id) ) $cat_id=dmc_get_category_id($cat_id);
				// if (DEBUGGER>=1) fwrite($dateihandle, " gemappt auf $cat_id\n");
			}
			
			if ($typus == "product") { 
				// zunaechst die zugeordnete Kategorie-Bezeichnung ermitteln und "merken"
				if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
					$query = 	"select categories_name as categories_name from " . TABLE_CATEGORIES_DESCRIPTION .
								" where categories_id='$cat_id' AND language_code='$language' LIMIT 1";				
				} else if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
					//$query = 	"select name as categories_name from " . TABLE_CATEGORIES_DESCRIPTION .
					//			" where id_category='$cat_id' AND id_lang=$language LIMIT 1";
				} else {
					$query = 	"select categories_name as categories_name from " . TABLE_CATEGORIES_DESCRIPTION .
								" where categories_id='$cat_id' AND language_id=$language LIMIT 1";
				}
				
				if(strpos(strtolower(SHOPSYSTEM), 'presta') === false) {
					if (DEBUGGER>=1) fwrite($dateihandle, "/// seo 526=".$query."\n");
					$sql_query = dmc_db_query($query);
					
					if ($result_query = dmc_db_fetch_array($sql_query))
					{ 
						$bez_temp[0] = $result_query['categories_name'];
						// weiter mit parent cat
						$typus = "product_category";
					} else {
						$bez_temp[0] = "";
					}				
				} else { // BEI Presta keine Kategroie
					$bez_temp[0] = "";
				}
				
				//if (DEBUGGER>=1) fwrite($dateihandle, "/// seo 505=".$result_query['categories_name']."\n");
				//if (DEBUGGER>=1) fwrite($dateihandle, "/// seo 506=".$bez_temp[0]."\n");
				
			}  // if ($typus == "product")
			// Kategorie Struktur ermitteln
			if ($typus == "category" || $typus == "product_category") {
				// Shop Kategorie ID ermitteln
				//if (DEBUGGER>=1) fwrite($dateihandle, "/// seo 522 cat_id=$cat_id");
				// if ($typus == "category" || !is_numeric($cat_id) ) $cat_id=dmc_get_category_id($cat_id);
				if ( !is_numeric($cat_id) ) $cat_id=dmc_get_category_id($cat_id);
				//if (DEBUGGER>=1) fwrite($dateihandle, " gemappt auf $cat_id\n");
				if ($cat_id=='0')
					$exists = false;
				else 
					$exists = true;
				//if (DEBUGGER>=1) fwrite($dateihandle, "/// seo 527\n");
				// Solange Vater-Kategorie existent
				if ($exists)
				do { 
					if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
						$query = 	"select id_parent as parent_id from " . TABLE_CATEGORIES .
							" where id_category=" . $cat_id . " LIMIT 1";		
					} else {
						$query = 	"select parent_id as parent_id from " . TABLE_CATEGORIES .
							" where categories_id='" . $cat_id . "' LIMIT 1";	
					}
				//	if (DEBUGGER>=1) fwrite($dateihandle, "/// seo 539 =".$query."\n");
					$sql_query = dmc_db_query($query);
					if ($result_query = dmc_db_fetch_array($sql_query))
					{ 
						$cat_id=$parent_id=$result_query['parent_id'];
					//	if (DEBUGGER>=1) fwrite($dateihandle, "/// seo 543 parent_id =".$parent_id."\n");
						// Wenn nicht Hauptkategorie ->Bezeichnung von parent cat ermitteln und "merken"
						if($parent_id>0) {
							if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
								$query = 	"select categories_name as categories_name from " . TABLE_CATEGORIES_DESCRIPTION .
											" where categories_id='$parent_id' AND language_code='$language' LIMIT 1";				
							} else if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
								$query = 	"select name as categories_name from " . TABLE_CATEGORIES_DESCRIPTION .
											" where id_category='$parent_id' AND id_lang=$language LIMIT 1";
							} else {
								$query = 	"select categories_name as categories_name from " . TABLE_CATEGORIES_DESCRIPTION .
											" where categories_id='$parent_id' AND language_id=$language LIMIT 1";
							}
						//	if (DEBUGGER>=1) fwrite($dateihandle, "/// seo 557 =".$query."\n");
							$sql_query = dmc_db_query($query);
							if ($result_query = dmc_db_fetch_array($sql_query))
							{ 			
								$bez_temp[] = $result_query['categories_name'];
							} else {
								$bez_temp[] = "";
							}
						} else { // if($parent_id>0)
							$typus = "ende";
						}
						//if (DEBUGGER>=1) fwrite($dateihandle, "/// seo 566=".$bez_temp[1]."\n");
						//if (DEBUGGER>=1) fwrite($dateihandle, "/// seo 567=typus=".$typus."\n");
					} else {
						// Keine (weitere) Varterkategorie vorhanden
						$typus = "ende";
					} 
					$rettung++;
				} while ($typus == "category" || $typus == "product_category" || $rettung>10); // bis $typus = "ende" oder warten auf break;
			}  // if ($typus == "category") 
		
			// text aufbauen
		//	if (DEBUGGER>=1) fwrite($dateihandle, "/// seo anzahl texte =".count($bez_temp)."\n");
			// for($i = count($bez_temp); $i > 0; $i--) { 
			for($i = 0; $i < count($bez_temp); $i++) { 
				// Die Cat Struktur dem Text zuordnen
				//if (DEBUGGER>=1) fwrite($dateihandle, "/// seo 556/$i=".$bez_temp[$i-1]."\n");
				//$text = $bez_temp[$i-1]."/".$text;
				$text = $bez_temp[$i]."/".$text;
			//	if (DEBUGGER>=1) fwrite($dateihandle, "/// seo 558/$i=".$text."\n");
			}
		} // end if 
		
		// ID ergaenzen
		if ($typus == "category") 
			$text .= "_".$cat_id;
		// if (DEBUGGER>=1) fwrite($dateihandle, "/// 594=$text\n");
		
		//$seo_text = strtolower(utf8_normalize_nfc($text));
			// $seo_text = strtolower(($text));
		$seo_text = (($text));
//	if (DEBUGGER>=1) fwrite($dateihandle, "/// 594=$seo_text\n");

		$d1 = array(" ","Ä", "Ö", "Ü", "ä" , "ö", "ü", "ß","�", "�", "�","<",">","#","\"","'","´",",","&","²","?",";","\\","/","-");
		$d2 = array("-", "Ae","Oe","Ue","ae","oe","ue","ss","Ae","ae","ss","_","_","_","_","_","_","_","-","2","-","-","_","_","_");
		$seo_text = str_replace($d1, $d2, $seo_text);		 
		$seo_text = utf8_encode($seo_text);
		$d1 = array(" ","Ä", "Ö", "Ü", "ä" , "ö", "ü", "ß","�", "�", "�","<",">","#","\"","'","´",",","&","²","?",";","\\","/","-");
		$d2 = array("-", "Ae","Oe","Ue","ae","oe","ue","ss","Ae","ae","ss","_","_","_","_","_","_","_","-","2","-","-","_","_","_");
		$seo_text = str_replace($d1, $d2, $seo_text);		 
		$seo_text = utf8_decode($seo_text);
		//	if (DEBUGGER>=1) fwrite($dateihandle, "/// 615=$seo_text\n");
		$d1 =  array(' ', 'í', 'ý', 'ß', 'ö', 'ô', 'ó', 'ò', 'ä', 'â', 'à', 'á', 'é', 'è', 'ü', 'ú', 'ù', 'ñ', 'ß', '²', '³', '@', '€', '$','®','™');
		$d2 = array('-', 'i', 'y', 's', 'oe', 'o', 'o', 'o', 'ae', 'a', 'a', 'a', 'e', 'e', 'ue', 'u', 'u', 'n', 'ss', '2', '3', 'at', 'eur', 'usd','R','TM');
		$seo_text = str_replace($d1, $d2, $seo_text);
		//if (DEBUGGER>=1) fwrite($dateihandle, "///619=$seo_text\n");
		$d1 =  array('&amp;', '&quot;', '&', '"', "'", '¸', '`',  '(', ')', '[', ']', '<', '>', '{', '}', '.', ':', ',', ';', '!', '?', '+', '*', '=', 'µ', '#', '~', '"', '§', '%', '|', '°', '^');
		$seo_text = str_replace($d1, '', $seo_text);
		$seo_text = str_replace(array('----', '---', '--'), '-', $seo_text);
		// if (DEBUGGER>=1) fwrite($dateihandle, "/// 623=$seo_text\n");
		
		$seo_text = str_replace('-', '_', $seo_text);
		

// =========================

		$seo_text = str_replace(
			array('�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�'),
			array('n','N','' ,'i','a','A','e','E','i','I','o','O','u','U','c','a','o'),
			$seo_text
		);

// =========================		

		// $seo_text = substr($seo_text, 0, 50); // Max length for a title in URL
		if (DEBUGGER>=1) fwrite($dateihandle, "/// 631= einzutragen=$seo_text\n");
		
		// return urlencode($seo_text);
		return strtolower($seo_text);
	} // end function dmc_prepare_seo

	// Master Flag für Magento Varianten-Haupt-Produkt setzen
	function dmc_set_master_flag($Artikel_Variante_Von_id) 
	{
		
		if (DEBUGGER>=1) fwrite($dateihandle, "// dmc_set_master_flag - Artikel $Artikel_Variante_Von_id\n");
	     	
		// Hauptartikel Master Flag ´zuordnen
		$do_update_array['products_master_flag']=1;
		dmc_sql_update_array(TABLE_PRODUCTS, $do_update_array, "products_id = '$Artikel_Variante_Von_id'");
		
		return true;
	} // end function dmc_set_master_flag
	
	// VPE evtl setzen und ID ermitteln
	function dmc_prepare_vpe($Artikel_VPE)
	{
		GLOBAL $sql_data_array, $dateihandle;
		if (DEBUGGER>=1)  fwrite($dateihandle, "dmc_art_functions - products_vpe= ".$Artikel_VPE." .\n");

		// todo veyton presta
		if (isset($Artikel_VPE) && $Artikel_VPE<>"" && (strpos(strtolower(SHOPSYSTEM), 'veyton') === false) && strpos(strtolower(SHOPSYSTEM), 'presta') === false && strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false)
		{
				// Überprüfen, ob die  VPE übermittelt wurde
				if (is_numeric($Artikel_VPE)) {	
					$Artikel_VPE_ID = $Artikel_VPE;
					// VPE nur aktiv wenn grösser als 1
					if ($Artikel_VPE>1) $products_vpe_status = 1;
					else $products_vpe_status = 0;
					$products_vpe_value = 1;
				} else { // VPE als String übergeben
					// VPE-Wert und  VPE-Art ermitteln - werden als attribue1@attribe2@... übergeben
					// Überprüfen, ob eine VPE-Einheit angegeeben, oder wert und Einheit
					// $Artikel_VPE = "24@Stück";
					// VPE-Wert und  VPE-Art ermitteln - werden als attribue1@attribe2@... übergeben
					// Überprüfen, ob eine VPE-Einheit angegeben, oder wert und Einheit
					if (preg_match('/@/', $Artikel_VPE)) {
						// Wert + VPE
						// list ($Artikel_VPE_Wert, $Artikel_VPE) = split ("@", $Artikel_VPE);
						$werte = explode ( '@', $Artikel_VPE);
						$Artikel_VPE_Wert = $werte[0];
						$Artikel_VPE = $werte[1];
						// NEU mit Dividend getrennt mit : , z.B. (250:100@100 ml)
						if (preg_match('/:/', $Artikel_VPE_Wert)) {
							// list ($Artikel_VPE_Wert, $Dividend) = split (":", $Artikel_VPE_Wert);
							$werte = explode ( ':', $Artikel_VPE_Wert);
							$Artikel_VPE_Wert = $werte[0];
							$Dividend = $werte[1];
							$Dividend=$Artikel_VPE_Wert/$Dividend;
						}
						$verpackungseinheiten=true;
						// echo "VPE-Wert ".$Artikel_VPE_Wert;
						// echo "VPE: ".$Artikel_VPE;
					} else {
						$Artikel_VPE_Wert = 1;
						$verpackungseinheiten=false;
						// echo "VPE: ".$Artikel_VPE;
					}
		
					// VPE werden von zencatrt nicht unterstuiertzzt
					
					if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false && strpos(strtolower(SHOPSYSTEM), 'veyton') === false
					&& strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false) {
						// Überprüfen, ob VPE in Shop-DB existent				 
						$cmd = "select products_vpe_id from products_vpe".
								" where products_vpe_name = '".$Artikel_VPE."' LIMIT 1";
						$sql_query = dmc_db_query($cmd);
						if ($sql_result = dmc_db_fetch_array($sql_query)) {
							// VPE bereits vorhanden, daher nur die zugrhörige ID ermitteln und dem SQL Array zufügen
							$Artikel_VPE_ID = $sql_result['products_vpe_id'];
							// Nur anzeigen, wenn Wert <> 1					
							if ($verpackungseinheiten==false) $products_vpe_status = 0;
								else $products_vpe_status = 1; // $Artikel_VPE_Wert==1 &&
							$products_vpe_value = $Artikel_VPE_Wert;
							// fwrite($dateihandle, "Artikel_VPE_ID ermittelt $Artikel_VPE_ID\n");				      
						} else {
							 if (DEBUGGER>=1)  fwrite($dateihandle, "744\n");
							  // VPE in VPE-DB-Tabelle eintragen und neue ID dem SQL Array zufügen
							  // Höchste ID der Einträge aus  Tabelle ermitteln und um 1 inkrementieren	
							  $Artikel_VPE_ID = dmc_get_highest_id("products_vpe_id",DB_PREFIX."products_vpe");
							  $Artikel_VPE_ID++;
							  // VPE in DB schreiben
								if (DEBUGGER>=1) fwrite($dateihandle, "VPE_ID=".$Artikel_VPE_ID." vpe=".$Artikel_VPE."\n");						  
							  $sql_VPE_array = array(	'products_vpe_id' => $Artikel_VPE_ID,
														'language_id' => '2',					// deutsch
														'products_vpe_name' => $Artikel_VPE);				      
							  dmc_sql_insert_array("products_vpe", $sql_VPE_array);
							   // Daten für Produkt Tabelle
							  $products_vpe_status = 1;
							  if ($Artikel_VPE_Wert>0) $products_vpe_value = $Artikel_VPE_Wert;
							  else $products_vpe_value = 1;
						} 
					} // end  if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false) 
				} // endif VPE
		} else {
			// Keine VPE übergeben
			$Artikel_VPE=0;
			$Artikel_VPE_ID=0;
			$products_vpe_status=0;
			$products_vpe_value=0;
		}//  endif VPE
		
		 if (DEBUGGER>=1)  fwrite($dateihandle, "771 products_vpe= ".$Artikel_VPE." .\n");
		if (DEBUGGER>=1)  fwrite($dateihandle, "772 Artikel_VPE_ID= ".$Artikel_VPE_ID." .\n");
		//if (DEBUGGER>=1)  fwrite($dateihandle, "773 products_vpe_status= ".$products_vpe_status." .\n");
		//if (DEBUGGER>=1)  fwrite($dateihandle, "774 products_vpe_value= ".$products_vpe_value." .\n");
		$Ergebnis=$Artikel_VPE_ID."@".$products_vpe_status."@".$products_vpe_value;
		return $Ergebnis;
	} // end function
	
	// Suchmaschinen fuer product 
	function dmc_prepare_seo_name($text,$language)
	{
	
		global $dateihandle;
		//if (DEBUGGER>=1) fwrite($dateihandle, "dmc_prepare_seo_name=$text\n");
				
		//$seo_text = strtolower(utf8_normalize_nfc($text));
		$seo_text = strtolower(($text));
		//$seo_text = (($text));
		//if (DEBUGGER>=1) fwrite($dateihandle, "/// 738=$seo_text\n");
		/*
		$d1 = array(" ","Ä", "Ö", "Ü", "ä" , "ö", "ü", "ß","�", "o", "ue", "�" , "o", "ue", "�","<",">","#","\"","'","´",",","&","²","?",";","\\","/","-");
		$d2 = array("-", "Ae","Oe","Ue","ae","oe","ue","sz","Ae","Oe","Ue","ae","oe","ue","sz","_","_","_","_","_","_","_","-","2","-","-","_","_","_");
		$seo_text = str_replace($d1, $d2, $seo_text);		 
	//	if (DEBUGGER>=1) fwrite($dateihandle, "/// 615=$seo_text\n");
		$d1 =  array(' ', 'í', 'ý', 'ß', 'ö', 'ô', 'ó', 'ò', 'ä', 'â', 'à', 'á', 'é', 'è', 'ü', 'ú', 'ù', 'ñ', 'ß', '²', '³', '@', '€', '$','®','™');
		$d2 = array('-', 'i', 'y', 's', 'oe', 'o', 'o', 'o', 'ae', 'a', 'a', 'a', 'e', 'e', 'ue', 'u', 'u', 'n', 'ss', '2', '3', 'at', 'eur', 'usd','R','TM');
		$seo_text = str_replace($d1, $d2, $seo_text);
		//if (DEBUGGER>=1) fwrite($dateihandle, "///619=$seo_text\n");
		$d1 =  array('&amp;', '&quot;', '&', '"', "'", '¸', '`',  '(', ')', '[', ']', '<', '>', '{', '}', '.', ':', ',', ';', '!', '?', '+', '*', '=', 'µ', '#', '~', '"', '§', '%', '|', '°', '^');
		$seo_text = str_replace($d1, '', $seo_text);
		$seo_text = str_replace(array('----', '---', '--'), '-', $seo_text);
		//if (DEBUGGER>=1) fwrite($dateihandle, "/// 753=$seo_text\n");
		*/
		$seo_text = preg_replace('/[^0-9a-zA-Z-_]/', '', $seo_text);
    		
		// return urlencode($seo_text);
		return strtolower($seo_text);
	} // end function dmc_prepare_seo
	
	
	// Produkt deaktiveren
	function dmc_deactivate_product($Artikel_Artikelnr)
	{
		if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
			dmc_db_query("UPDATE ".TABLE_PRODUCTS." SET active = 0 WHERE reference='$Artikel_Artikelnr'");
		} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
			dmc_db_query("UPDATE ".TABLE_PRODUCTS." SET published = 0 WHERE product_sku='$Artikel_Artikelnr'");
		} else { 
			dmc_db_query("UPDATE ".TABLE_PRODUCTS." SET products_status = 0 WHERE products_model='$Artikel_Artikelnr'");
		} // end if
	} // end function dmc_deactivate_product
	
	// Bilddateiname preufen/ermitteln
	function dmc_prove_image_name($bilddatei, $Artikel_Artikelnr)
	{
		global $dateihandle;
		
		$bilder_pfad=DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES.'/';
		if (!is_dir($bilder_pfad))
			$bilder_pfad=PRODUCTS_EXTRA_PIC_PATH;
		
		//if (DEBUGGER>=1) fwrite($dateihandle, "dmc_prove_image_name=$bilder_pfad"."$bilddatei \n");
		$exists=false;
		// $bilddatei muss grösser ALS 4 sein, denn 4 wäre nur .jpg oder .gif
		if (isset($bilddatei) && strlen($bilddatei)>4)
	    {
			//if (DEBUGGER>=1) fwrite($dateihandle, "dmc_prove_image_name 814\n");
			if (is_file($bilder_pfad.$bilddatei)){
				//if (DEBUGGER>=1) fwrite($dateihandle, "Bilddatei ".$bilder_pfad.$bilddatei." vorhanden"."\n");
				$exists=true;
			} else {
				// GROSS/KLEINSCHREIBUNG
				$bilddatei =  str_replace('.jpg', '.jpg', $bilddatei);
				if (is_file($bilder_pfad.$bilddatei))
					$exists=true;
			}
			//if (DEBUGGER>=1) fwrite($dateihandle, "dmc_prove_image_name 824\n");// Pruefe auf Dateien beginnend mit Artikelnummer, wie "1234 toller artikel.jpg" - seit 200514
			// fwrite($dateihandle, "Pruefe auf Zusatzbilder\n");
			if ($exists==false && $handle = opendir($bilder_pfad)) {
				//if (DEBUGGER>=1) fwrite($dateihandle, "dmc_prove_image_name 827\n");
				while (false !== ($file = readdir($handle))) {				
					if ($file != "." && $file != "..") {
						//if (DEBUGGER>=1) fwrite($dateihandle, "dmc_prove_image_name 830\n");
						// fwrite($dateihandle, "Zusatzbild: $file\n");
						if  (strpos($file,$Artikel_Artikelnr)===0 && $exists==false) {		// entspricht startswith, d.h. Dateibezeichnung faengt mit Artikelnummer an.
							//if (DEBUGGER>=1) fwrite($dateihandle, " 833 gefunden: $file\n");
							$bilddatei=$file;
							//if (DEBUGGER>=1) fwrite($dateihandle, "Bilddatei ".$bilder_pfad.$bilddatei." vorhanden"."\n");
							$exists=true;
							break;
						}						
					}
				}
				closedir($handle);
			}
		}
		
		return $bilddatei;
    } // function dmc_prove_image_name
	
	// Artikel-Details-ID ermitteln - Shopware 
	function dmc_get_details_id_by_artno($artno) {
		global  $dateihandle;
		$query = "SELECT id AS id FROM s_articles_details";
		$query .= " WHERE ordernumber ='".$artno."'";
		
		$link=dmc_db_connect();
		//	$dateiname=LOG_FILE;	
		//$dateihandle = fopen($dateiname,"a");
		if (DEBUGGER==99)  
			fwrite($dateihandle, "dmc_get_details_id_by_artno-SQL= ".$query." .\n");
		$id = "";		
		$sql_query = mysqli_query($link,$query);				
		while ($TEMP_ID = mysqli_fetch_assoc($sql_query)) {
			if ($TEMP_ID['id']=='' || $TEMP_ID['id']=='null')
				// IF no ID -> Product not available
				$id = "";
			else
				$id  = $TEMP_ID['id'];
		}		
		return $id;	
	} // end function dmc_get_details_id_by_artno
	
// dmc_add_woocommerce_image Aritkelbilder wooCommerce zuordnen
	function dmc_add_woocommerce_image($Artikel_Bilddatei, $post_ID, $Artikel_SEO_Name)
	{
		global $dateihandle, $exits;
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');

		fwrite($dateihandle, "dmc_art_functions - dmc_add_woocommerce_image($Artikel_Bilddatei, $post_ID) $Artikel_SEO_Name\n");
		
		if ($exits==false)
			$neuerArtikel=true;//fwrite($dateihandle, "Neuer Artikel\n");
		else 
			$neuerArtikel=false;//fwrite($dateihandle, "ALTER Artikel\n");
			
		$Bilderverzeichnis = UPLOAD_IMAGES_FOLDER;						// "./upload_images/" 
					
		$Artikel_Bilddateien  = explode ( '@', $Artikel_Bilddatei);
		$anzahl_original_bildateien =  count($Artikel_Bilddateien);
		$neue_anzahl_bilddateien = $anzahl_original_bildateien;
		
		for($Anzahl = 0; $Anzahl < $anzahl_original_bildateien; $Anzahl++) { 
			// Wenn Bildextension fehlt, dann .jpg ergaenzen
			if (strpos($Artikel_Bilddateien[$Anzahl],'.')===false) {
				$Artikel_Bilddateien[$Anzahl]=$Artikel_Bilddateien[$Anzahl].".jpg";
			}
			$Artikel_Bilddateien[$neue_anzahl_bilddateien] = str_replace(".","_1.",$Artikel_Bilddateien[$Anzahl]);
			$Artikel_Bilddateien[$neue_anzahl_bilddateien] = str_replace("_0_1.","_1.",$Artikel_Bilddateien[$neue_anzahl_bilddateien]);	// SELECTLINE
			$neue_anzahl_bilddateien++;
			$Artikel_Bilddateien[$neue_anzahl_bilddateien] = str_replace(".","_2.",$Artikel_Bilddateien[$Anzahl]);
			$Artikel_Bilddateien[$neue_anzahl_bilddateien] = str_replace("_0_2.","_2.",$Artikel_Bilddateien[$neue_anzahl_bilddateien]);	// SELECTLINE
			$neue_anzahl_bilddateien++;
			$Artikel_Bilddateien[$neue_anzahl_bilddateien] = str_replace(".","_3.",$Artikel_Bilddateien[$Anzahl]);
			$Artikel_Bilddateien[$neue_anzahl_bilddateien] = str_replace("_0_3.","_3.",$Artikel_Bilddateien[$neue_anzahl_bilddateien]);	// SELECTLINE
			$neue_anzahl_bilddateien++;	
		}
		$ausgeben=print_r($Artikel_Bilddateien,true);
		fwrite($dateihandle, "Neues Bilddateien mit Hauptbild(".$Artikel_Bilddateien[0]."): $ausgeben \n");	
		$hauptbildexists=0;
			
		for($Anzahl = 0; $Anzahl <= 12; $Anzahl++) {   
			// $upload_dir = wp_upload_dir(); 
			if ($Anzahl>12) break;
			// entweder auf verschiedene Bilder (@) testen oder mit Suffix
			// if ($Artikel_Bilddateien[$Anzahl]=="") break;
			// Wenn Bildextension fehlt, dann .jpg ergaenzen
			if (strpos($Artikel_Bilddateien[$Anzahl],'.')===false) {
				$Artikel_Bilddateien[$Anzahl]=$Artikel_Bilddateien[$Anzahl].".jpg";
			}
			if (($Anzahl+1)>count($Artikel_Bilddateien)) {
				if ($Anzahl==1) {
					// Zweites Bild
					$Artikel_Bilddateien[$Anzahl]=str_replace('.','_1.',$Artikel_Bilddateien[$Anzahl-1]);
					$Artikel_Bilddateien[$Anzahl]=str_replace("_0_1.","_1.",$Artikel_Bilddateien[$Anzahl-1]);
					//fwrite($dateihandle, " Bild 968  ".(str_replace('.','_1.',$Artikel_Bilddateien[$Anzahl-1]))."\n" );
					// fwrite($dateihandle, " Bild 969 . zu _1. aus ".$Artikel_Bilddateien[$Anzahl-1] ." = ".$Artikel_Bilddateien[$Anzahl]."\n" );
					// Abfangroutine, wenn Bildname dem vorigen entspricht, nicht zuordnung
					if ($Artikel_Bilddateien[$Anzahl]==$Artikel_Bilddateien[$Anzahl-1])
						$Artikel_Bilddateien[$Anzahl]="";
				} else {
					//  Bild 3-12
					$Artikel_Bilddateien[$Anzahl]=str_replace("_".($Anzahl-1).".","_".$Anzahl.".",$Artikel_Bilddateien[$Anzahl-1]);
					//fwrite($dateihandle, " Bild Step xxx _".($Anzahl-1).". zu _".($Anzahl).". aus ".$Artikel_Bilddateien[$Anzahl-1] ."\n" );
					// Abfangroutine, wenn Bildname dem vorigen entspricht, nicht zuordnung
					if ($Artikel_Bilddateien[$Anzahl]==$Artikel_Bilddateien[$Anzahl-1])
						$Artikel_Bilddateien[$Anzahl]="";
				}
				if ($Anzahl<3)
					fwrite($dateihandle, " Bild Step xxxx ->".$Artikel_Bilddateien[$Anzahl]."\n" );
			}
			
			require_once ( ABSPATH . 'wp-admin/includes/image.php' );
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			// Get the path to the upload directory.
			$wp_upload_dir = wp_upload_dir();
			$upload_dir = $wp_upload_dir['url'].'/';
			$upload_images_dir = LOCAL_WOO_UPLOAD_IMAGES;
			$local_woo_upload_image = 	$wp_upload_dir['basedir'].'/'.$upload_images_dir;
			
			$Artikel_Bilddateien[$Anzahl] = trim($Artikel_Bilddateien[$Anzahl]);
			if ($Anzahl<3)
				fwrite($dateihandle, "Bild Step 1 - Suche Bilddatei Nr. ".($Anzahl+1).$Bilderverzeichnis.$Artikel_Bilddateien[$Anzahl]."\n" );
			// if (file_exists("../wp-content/uploads/".$Artikel_Bilddateien[$Anzahl])) {
			if ($Anzahl<3)
				fwrite($dateihandle, "Bild Step 1a PREUFE AUF VERZEICHNIS\n" );
			// GGFls Dateiaendung wechseln
			if ((!file_exists($Bilderverzeichnis.$Artikel_Bilddateien[$Anzahl]) 
				&& !file_exists(".".$upload_dir.$upload_images_dir.$Artikel_Bilddateien[$Anzahl]))){
					$Artikel_Bilddateien[$Anzahl] = str_replace(".jpg",".png",$Artikel_Bilddateien[$Anzahl]);
					if ((!file_exists($Bilderverzeichnis.$Artikel_Bilddateien[$Anzahl]) 
					&& !file_exists(".".$upload_dir.$upload_images_dir.$Artikel_Bilddateien[$Anzahl]))){
							$Artikel_Bilddateien[$Anzahl] = str_replace(".png",".gif",$Artikel_Bilddateien[$Anzahl]);
						if ((!file_exists($Bilderverzeichnis.$Artikel_Bilddateien[$Anzahl]) 
							&& !file_exists(".".$upload_dir.$upload_images_dir.$Artikel_Bilddateien[$Anzahl]))){
								$Artikel_Bilddateien[$Anzahl] = str_replace(".gif",".png",$Artikel_Bilddateien[$Anzahl]);
						}
					}
			}
			
			$attach_image=true;
			if ($Artikel_Bilddateien[$Anzahl]!="" &&
				(file_exists($Bilderverzeichnis.$Artikel_Bilddateien[$Anzahl]) 
				|| file_exists(".".$local_woo_upload_image.$Artikel_Bilddateien[$Anzahl]))){
				// Bilddatei ins Upload_Dir kopieren
				// Wenn $Artikel_SEO_Name angegeben, Bilder von Artikelnummer auf $Artikel_SEO_Name umbenennen
				if ($Artikel_SEO_Name!="") {
					// Neuer Name ist $Artikel_SEO_Name + _...jpg
					if (strpos($Artikel_Bilddateien[$Anzahl],"_")===false) {
						$neuer_bildname=str_replace(substr($Artikel_Bilddateien[$Anzahl],0,strpos($Artikel_Bilddateien[$Anzahl],".")),$Artikel_SEO_Name,$Artikel_Bilddateien[$Anzahl]);
					} else {
						$neuer_bildname=str_replace(substr($Artikel_Bilddateien[$Anzahl],0,strpos($Artikel_Bilddateien[$Anzahl],"_")),$Artikel_SEO_Name,$Artikel_Bilddateien[$Anzahl]);
					}
					fwrite($dateihandle, "Neuer Name=".$neuer_bildname."\n" );
					copy ($Bilderverzeichnis.$Artikel_Bilddateien[$Anzahl], $Bilderverzeichnis.$neuer_bildname);
					copy ($Bilderverzeichnis.$Artikel_Bilddateien[$Anzahl], $local_woo_upload_image.$neuer_bildname);
					// Nun auch neue Bilddatei verwenden
					$Artikel_Bilddateien[$Anzahl]=$neuer_bildname;
				}
				
				// 23.10.2017 - Nur Bilder zuordnen, wenn gleiches HAUPTBILD noch nicht zugeordnet ist
				if ($Anzahl==0  && $neuerArtikel==false) {
					// Bei bestehenden Artikeln pruefen, ob identisches Bilde bereits zugeordnet ist
					$image_folder1=$Bilderverzeichnis;				// "./upload_images/";
					$image_folder2="../wp-content/uploads/";
					// Prüfen, ob Bild schon zugeordnet ist
					$image_id=dmc_woo_get_image_id($post_ID);
					if ($image_id<>0) {
						// Es ist bereits ein Bild (_thumbnail_id) mit ID $image_id zugeordnet
						fwrite($dateihandle, "Es ist bereits ein Bild (_thumbnail_id) mit ID $image_id zugeordnet.\n");
						$image_folder2="../wp-content/uploads/";
						$image_name_woo=dmc_woo_get_image_name_by_id($image_id);
						$image_name1=$image_folder1.$Artikel_Bilddateien[$Anzahl];
						$image_name2=$image_folder2.$image_name_woo;
						// fwrite($dateihandle, "Aufrufen dmc_compare_files($image_name1,$image_name2)\n");
						if (dmc_compare_files($image_name1,$image_name2)==true) {
							fwrite($dateihandle, "Gleiches Bild ist bereits zugeordnet.\n");
							$attach_image=false;
						} else {
							fwrite($dateihandle, "Es ist ein anderes Bild zugeordnet.\n");
							$attach_image=true;
						}	
					} else {
						// Bilder zuordnen
						fwrite($dateihandle, "Es ist ein anderes Bild zugeordnet.\n");
					}
				} else {		// $neuerArtikel==true
					// Bei neuen Artikeln pruefen, ob das Bild bereits einem anderen Artikel zugeordnet ist
					$exiting_woo_image_id = 0;
					$woo_images_ids = dmc_woo_get_image_ids_by_name(LOCAL_WOO_UPLOAD_IMAGES.$Artikel_Bilddateien[$Anzahl]);
					if ($woo_images_ids!="" && file_exists($image_folder2.LOCAL_WOO_UPLOAD_IMAGES.$Artikel_Bilddateien[$Anzahl])) {
						fwrite($dateihandle, "Das Bild ".LOCAL_WOO_UPLOAD_IMAGES.$Artikel_Bilddateien[$Anzahl]." ist bereits mit IDs in wooCommerce vorhanden: ".$woo_images_ids."\n");
						$woo_images_ids_array = explode(",",$woo_images_ids);
						$exiting_woo_image_id = $woo_images_ids_array[0];
						/* foreach ($woo_images_ids_array as $einzelid) {
							echo "Bild mit ID=".$einzelid."\n";
						} */
						$attach_image=false;
						// Direkte Zuordnung (auf Datenbank) ohne das Bild neu anzulegen
						if ($hauptbildexists==0) {
							fwrite($dateihandle, "1035 Bestehendes Bild als Hauptbild zuordnen: ".$exiting_woo_image_id." \n");
							set_post_thumbnail( $post_ID, $exiting_woo_image_id );
							$hauptbildexists=1;
						} else {
							fwrite($dateihandle, "1039 Bestehendes Bild als Zusatz zuordnen: ".$exiting_woo_image_id." \n");
							$table = "postmeta";
							if ($Artikel_Variante_Von!='') {
								$where = "post_id=".$post_ID." AND meta_key='variation_image_gallery'";
								fwrite($dateihandle, "1041 pruefe postmeta: $where\n");
								$gallery_media_ids = dmc_sql_select_query("meta_value",$table,$where);
							} else {
								$where = "post_id=".$post_ID." AND meta_key='_product_image_gallery'";
								fwrite($dateihandle, "1045 pruefe postmeta: $where\n");
								$gallery_media_ids = dmc_sql_select_query("meta_value",$table,$where);
							}
							// Wenn noch nicht zugeordnert
							if ($gallery_media_ids=="" || $Anzahl<2) {
								if ($Artikel_Variante_Von!='') {
									update_post_meta($post_ID,'variation_image_gallery',$exiting_woo_image_id); // Gallery Image
								} else {
									update_post_meta($post_ID,'_product_image_gallery',$exiting_woo_image_id); // Gallery Image
								}
							} else if (strpos($gallery_media_ids, $exiting_woo_image_id)===false) {
								if ($Artikel_Variante_Von!='') {
									update_post_meta($post_ID,'variation_image_gallery',$gallery_media_ids.",".$exiting_woo_image_id); // Gallery Image
								} else {
									update_post_meta($post_ID,'_product_image_gallery',$gallery_media_ids.",".$exiting_woo_image_id); // Gallery Image
								}
								fwrite($dateihandle, " done \n");
							} else {
								fwrite($dateihandle, "1063 ".$exiting_woo_image_id." bereits in ".$gallery_media_ids." enthalten \n");
							}
										 
						}
						
					} else {
						fwrite($dateihandle,  "Das Bild ".LOCAL_WOO_UPLOAD_IMAGES.$Artikel_Bilddateien[$Anzahl]." ist noch nicht wooCommerce vorhanden: ".$woo_images_ids." , bzw liegt nicht in ".$image_folder2.LOCAL_WOO_UPLOAD_IMAGES.$Artikel_Bilddateien[$Anzahl]."\n");
						$attach_image=true;
					}
					fwrite($dateihandle,  "Hoechste exitierende wooCommerce Bild ID=".$exiting_woo_image_id."\n");
				}
		
				// Neue Artikel oder Artikel mit geaenderten Bildern
				if ($attach_image) {
					// NUR BILD kopieren, wenn noch nicht im Verzeichnis, d.h. wenn noch nicht zugeordnet
					fwrite($dateihandle, " Bild Step 1b copiere ggfls ".$Bilderverzeichnis.$Artikel_Bilddateien[$Anzahl]. " zu ".$local_woo_upload_image.$Artikel_Bilddateien[$Anzahl]. "\n" );
					if (!file_exists(".".$local_woo_upload_image.$Artikel_Bilddateien[$Anzahl])) {
						copy ($Bilderverzeichnis.$Artikel_Bilddateien[$Anzahl], $local_woo_upload_image.$Artikel_Bilddateien[$Anzahl]);
						
					}
								
					// $filename = $upload_dir.$upload_images_dir.$Artikel_Bilddateien[$Anzahl];
					$filename = $upload_images_dir.$Artikel_Bilddateien[$Anzahl];
					// Set attachment data
					$Artikel_SEO_Name_Bild=str_replace("_"," ",$Artikel_SEO_Name);
					fwrite($dateihandle,  "Artikel_SEO_Name_Bild=".$Artikel_SEO_Name_Bild."\n");
					$attachment = array(
        				'post_mime_type' => 'image/jpeg',
						//	'post_title'     => sanitize_file_name( $upload_images_dir.$Artikel_Bilddateien[$Anzahl] ),
						'post_title'     => $Artikel_SEO_Name_Bild ,
						'post_content'   => '',
						'post_status'    => 'inherit'
					);
					// Image with URL Path
					$file_url = $wp_upload_dir['url'] . '/' . basename($Artikel_Bilddateien[$Anzahl]);
					// Image with Absolute path 
				//	$filename = $wp_upload_dir['basedir'].'/'.basename($Artikel_Bilddateien[$Anzahl]);
					$filename = $local_woo_upload_image.basename($Artikel_Bilddateien[$Anzahl]);
					$file_type = wp_check_filetype(basename($Artikel_Bilddateien[$Anzahl]), null);
					//	$file_path=$file_url;
					// apply filters (important in some environments)
					//	apply_filters('wp_handle_upload', array('file' => $file_path, 'url' => $file_url, 'type' => $file_type), 'upload');
		
					// Create the attachment 
					$attach_id = wp_insert_attachment( $attachment, $filename, $post_ID );
							fwrite($dateihandle, " - Bild 1020 wp_insert_attachment( $attachment, $filename, $post_ID ) -> attach_id = $attach_id \n");
					$upload    =  apply_filters('wp_handle_upload', array('file' => $Artikel_Bilddateien[$Anzahl], 'url' => $file_url, 'type' => $file_type), 'upload');
					//	$ergebnis=print_r($upload,true);
					//	fwrite($dateihandle, " - Bild apply_filters ( 'file' => ".$upload['file'].", 'url' => ".$upload['url'].", 'type' => $file_type ) \n");
					//	fwrite($dateihandle, " -> upload -> $ergebnis\n");
					// Define attachment metadata
					
					$fullsizepath = get_attached_file( $attach_id );
					fwrite($dateihandle, " - Bild 1155 wp_generate_attachment_metadata( $attach_id, $fullsizepath )");
					
					if ($attach_data = wp_generate_attachment_metadata($attach_id, $fullsizepath)) {
						$ergebnis=print_r($attach_data,true);
					//	fwrite($dateihandle, "attach_data -> $ergebnis\n");
						wp_update_attachment_metadata($attach_id, $attach_data);
					} else {
						$ergebnis=print_r($attach_data,true); 
						fwrite($dateihandle, " Failed to create Image Meta-Data -> $ergebnis\n");
					}
					// Alte Mediathek Einträge löschen
					//$sql_query="DELETE FROM ".DB_PREFIX."postmeta WHERE meta_key='_wp_attached_file' AND meta_value='".$filename."' AND post_id<".$attach_id;
					// dmc_sql_query($sql_query);
					  // bzw Inhalt ist nur der Bildname, zb dt131.jpg fuer /var/www/clients/client2363/web4846/web/mhdinter/wp-content/uploads//dt131.jpg
					// $anfang=(strrpos($filename,'/')+1);
					// $sql_query="DELETE FROM ".DB_PREFIX."postmeta WHERE meta_key='_wp_attached_file' AND meta_value='".substr($filename, $anfang, 256)."' AND post_id<".$attach_id;
					// dmc_sql_query($sql_query);
					
					// $sql_query="DELETE FROM ".DB_PREFIX."posts where post_mime_type='image/jpeg' and id NOT IN (SELECT post_id FROM ".DB_PREFIX."postmeta WHERE meta_key='_wp_attached_file')";
					// dmc_sql_query($sql_query);
					// And finally assign featured image to post HAUPTBILD
					// And finally assign featured image to post HAUPTBILD, wenn noch nicht vorhanden
					if ($hauptbildexists==0) {
						set_post_thumbnail( $post_ID, $attach_id );
						$hauptbildexists=1;
					} else {
						$table = "postmeta";
						if ($Artikel_Variante_Von!='') {
							$where = "post_id=".$post_ID." AND meta_key='variation_image_gallery'";
							fwrite($dateihandle, "1106 pruefe postmeta: $where\n");
							$gallery_media_ids = dmc_sql_select_query("meta_value",$table,$where);
						} else {
							$where = "post_id=".$post_ID." AND meta_key='_product_image_gallery'";
							fwrite($dateihandle, "1110 pruefe postmeta: $where\n");
							$gallery_media_ids = dmc_sql_select_query("meta_value",$table,$where);
						}
						// Wenn noch nicht zugeordnert
						if ($gallery_media_ids=="" || $Anzahl<2) {
							if ($Artikel_Variante_Von!='') {
								update_post_meta($post_ID,'variation_image_gallery',$attach_id); // Gallery Image
							} else {
								update_post_meta($post_ID,'_product_image_gallery',$attach_id); // Gallery Image
							}
						} else if (strpos($gallery_media_ids, $attach_id)===false) {
							if ($Artikel_Variante_Von!='') {
								update_post_meta($post_ID,'variation_image_gallery',$gallery_media_ids.",".$attach_id); // Gallery Image
							} else {
								update_post_meta($post_ID,'_product_image_gallery',$gallery_media_ids.",".$attach_id); // Gallery Image
							}
							fwrite($dateihandle, " done \n");
						} else {
							fwrite($dateihandle, "1102 ".$attach_id." bereits in ".$gallery_media_ids." enthalten \n");
						}
									
					}
				}
			} else {
				if ($Anzahl<3)
					fwrite($dateihandle, " - Bild ".$Bilderverzeichnis.$Artikel_Bilddateien[$Anzahl]." bzw .".$local_woo_upload_image.$Artikel_Bilddateien[$Anzahl]." existiert nicht. \n");
			}
		}

		return true;
	} // end dmc_add_woocommerce_image($Artikel_Bilddatei, $post_id)
?>
