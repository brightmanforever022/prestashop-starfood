<?php

	/**
	 *
	 * @write details to shop database
	 * @param string $s
	 * @return string $s
	 *
	 * Version vom 22.05.2011
	 *
	 * 22.05.2011 - Neue Funktionalitäten für gambio_attributes
	 * 11.08.2011 - cat_languages
	 * 19.08.2011 - customer_matrix fuer Rechte auf artikel für hdkf eingespielt
	 * 11.10.2011 - dmc_handelsstueckliste für hdkf eingespielt
	 * 16.03.2012 - ab sofort nicht mehr dm_details sondern dmc_set_details
	 * 29.03.2012 - presta_pdf
	 * 11.01.2013 - Artikel_Preis_Rabatt bei Staffelpreisen möglich
	 * 18.03.2013 - csvlangtext - Lantexte > 255 Zeichen aus CSV Dateien empfangen
	 * 06.09.2013 - dmc_sage_cl_artikelnummern zur Übergabe insbesondere von Herstellernummern 
	 * 13.02.2015 - dmc_customer_prices - Exportmodus Kundenpreise für individuelle Tabellen
	 * 26.08.2015 - dmc_cl_artikel_details - Details zum Artikel wie Lieferantennummer der Sage Classic Line
	 * 17.12.2015 - dmc_lang_shopware - Fremdsprachen zum Artikel für Shopware
	 * 24.08.2016 - dmc_xtc_kundenpreise - Kundenpreise als Kundengruppenpreis efuer xtc, Gambio etc
	 * 29.11.2016 - dmc_staffelpreise_woocommerce Plugin "Woocommerce Dynamic Pricing & Discounts"
	 * 01.07.2017 - dmc_customer_prices_shopware
	 * 21.07.2017 - dmc_update_custno_by_email Kundennummer im Shop setzen / aktualisieren
	 * 02.03.2018 -	dmc_staffelpreise_woocommerce_2 - Exportmodus dmc_staffelpreise_woocommerce "anderes" Plugin
	 * 03.09.2018 - dmc_set_shopware_groupprices - separate Übergabe Kundengruppenpreise
		
	 */
	defined( '_DMC_ACCESSIBLE' ) or die( 'Direct Access to this location is not allowed.' );
	ini_set("display_errors", 1);
	error_reporting(E_ERROR);
	error_reporting(E_ALL);
 
	function dmc_set_details() {
		global $action, $version_major, $version_minor, $client;	
		
		if (DEBUGGER>=1) {
			$daten = "\n******************SetDetails******************\n";
			$dateiname=LOG_DATEI;	
			$dateihandle = fopen($dateiname,"a");
			fwrite($dateihandle, $daten);
			fwrite($dateihandle, "\n");
		} 
		if (DEBUGGER>=1) fwrite($dateihandle, "export = ".$_POST['ExportModus']."\n");
		
		
	 	  /* Details, z.b.
		* 	Fa JH2000:
		* Freifeld1 (Art) = pdfs, Freifeld2 = Artikelnummer, Freifeld3 = Upload Beschreibung1, Freifeld4 = Upload1, Freifeld5 = Upload Beschreibung2, Freifeld6 = Upload2, Freifeld7 = Upload Beschreibung3, Freifeld8 = Upload3
		*/
		
		for ($i=1;$i<=12;$i++) {
			$Freifeld{$i} = $_POST["Freifeld{$i}"];  
		}
		
		$ExportModusSpecial = $Freifeld{1};
		
		fwrite($dateihandle, "ExportModusSpecial=$ExportModusSpecial\n");
		
		if (DEBUGGER>=1) {
		  	for ($i=1;$i<=12;$i++) {
				fwrite($dateihandle, "Freifeld{$i} = ".$Freifeld{$i}."\n");	
			}
		}		
		if (DEBUGGER>=1) fwrite($dateihandle, "das Freifeld2 = ".$Freifeld{2}."\n");
		
		// Exportmodus product_to_categorie
		if ($ExportModusSpecial=='product_to_categorie') {
			$Artikel_Artikelnr = $Freifeld{2};
			$Artikel_Kategorie = $Freifeld{4};
		
			// nicht fuer virtuemart
			if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false) {
				// Kategorie_id aus meta
				$Artikel_Kategorie=dmc_get_category_id($Artikel_Kategorie);
				
				// Der Artikelnummer zugehörige products_id 
				// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
				$cmd = "select products_id from " . TABLE_PRODUCTS .
						" where products_model = '$Artikel_Artikelnr'";
						
				$sql_query = dmc_db_query($cmd);
					 
				if ($sql_result = dmc_db_fetch_array($sql_query))
				{      
					// ArtikelID von bestehendem Artikel ermitteln
					$Artikel_ID = $sql_result['products_id'];
					// Existiert
					$exists = 1;
				} //endif
				
				// nur fuer existente Artikel
				if ($exists == 1) { 
				
					// Überprüfen, ob Produkt der Kategorie zugeordnet ist
					$cmd = "select products_id, categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where " .
					  "products_id='$Artikel_ID' AND categories_id='$Artikel_Kategorie'";
					// Kategorie-Zuordnung löschen, wenn bereits existent
					$desc_query = dmc_db_query($cmd);
					if (($desc = dmc_db_fetch_array($desc_query)))
					{
						$cmd = "delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where " .
						"products_id='$Artikel_ID' AND categories_id='$Artikel_Kategorie'";
						dmc_db_query($cmd);
					 }
					
					// Kategoriezuordnung eintragen
					$insert_sql_data= array(
						'products_id' => $Artikel_ID,
						'categories_id' => $Artikel_Kategorie);
					dmc_sql_insert_array(TABLE_PRODUCTS_TO_CATEGORIES, $insert_sql_data);
					
					if (DEBUGGER>=1) fwrite($dateihandle, "Artikel_Kategorie=$Artikel_Kategorie Zuordnung eingetragen zu Artikel_ID=$Artikel_ID.\n");
					
				} // end if
			} else {
				// nicht unterstuetzte Shops				
				fwrite($dateihandle, "Funktion product_to_categorie nicht fuer ".SHOPSYSTEM." verfuegbar.\n");
			} // end if
		} // end exportmodus product_to_categorie

		// Exportmodus pdf
		if ($ExportModusSpecial=='pdfs') {
			$Artikel_Artikelnr = $Freifeld{2};
		
			// Der Artikelnummer zugehörige products_id und xsell_id ermitteln
			// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
			$cmd = "select products_id from " . TABLE_PRODUCTS .
		            " where products_model = '$Artikel_Artikelnr'";
					
			$sql_query = dmc_db_query($cmd);
		     	 
		    if ($sql_result = dmc_db_fetch_array($sql_query))
		    {      
				// ArtikelID von bestehendem Artikel ermitteln
				$Artikel_ID = $sql_result['products_id'];
				// Existiert
				$exists = 1;
			} //endif
			
			// wenn kompletter Dateipfad uebergeben, nur Datei selectieren
			$pos = strpos($Freifeld{4}, '\\');

			// Note our use of ===.  Simply == would not work as expected
			// because the position of 'a' was the 0th (first) character.
			if ($pos === false) {
			    $pdfDatei1 = $Freifeld{4};
			} else {
				$pdfDatei1= substr($Freifeld{4},$pos);  
			}

			// Wenn Artikel existiert, Details zuordnen 
			if ($exists == 1) {
				// Details eintragen : 
				// Freifeld1 (Art) = pdfs, Freifeld2 = Artikelnummer, Freifeld3 = Upload Beschreibung1, Freifeld4 = Upload1, Freifeld5 = Upload Beschreibung2, Freifeld6 = Upload2, Freifeld7 = Upload Beschreibung3, Freifeld8 = Upload3
				$sql_data_array = array(	'products_upload_title_1' => $Freifeld{3},
											'products_upload_file_1' => $pdfDatei1,
											'products_upload_title_2' => $Freifeld{5},
											'products_upload_file_2' => $Freifeld{6},
											'products_upload_title_3' => $Freifeld{7},
											'products_upload_file_3' => $Freifeld{8}
											);	
			
				dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, "products_id='$Artikel_ID'");

			  if (DEBUGGER>=1) fwrite($dateihandle, "Detail Zuordnung eingetragen.\n");
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Detail nicht eingetragen, da  Artikel nicht existent.\n");
			} //  endif Wenn Artikel existieren
		} // end exportmodus pdfs
		
		// Exportmodus pdfs_content für xtc - content manager
		if ($ExportModusSpecial=='pdfs_content') {
			//select 'pdfs' as ExportModus, p.ARTNR as Artikelnummer(Freifeld2),   concat('Mehr Informationen zu ',p.ARTNR) as pdf_title_de (Freifeld3),
			// p.Fld02 as pdf_file (Freifeld4), concat('more informations to ',p.ARTNR)  as pdf_title_en (Freifeld5),  '' as Freifeld6, 
			$Artikel_Artikelnr = $Freifeld{2};
			$PDF_FILE = $Freifeld{4};
			$PDF_TITLE_DE = $Freifeld{3};
			$PDF_TITLE_EN = $Freifeld{5};
		
			// wenn kompletter Dateipfad uebergeben, nur Datei selectieren
			$pos = strrpos($PDF_FILE, '\\');

			if (DEBUGGER>=1) fwrite($dateihandle, "PDF Datei:".$PDF_FILE." Zeichen \ an Position=$pos.\n");
		
			// Note our use of ===.  Simply == would not work as expected
			// because the position of 'a' was the 0th (first) character.
			if ($pos === false) {
			    $PDF_FILE = $Freifeld{4};
			} else {
				$PDF_FILE= substr($Freifeld{4},($pos+1));  
			}
		
		if (DEBUGGER>=1) fwrite($dateihandle, "PDF Datei:".$PDF_FILE."\n");
			// Der Artikelnummer zugehörige products_id ermitteln
			// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
			$cmd = "select products_id from " . TABLE_PRODUCTS .
		            " where products_model = '$Artikel_Artikelnr'";
					
			$sql_query = dmc_db_query($cmd);
		     	 
		    if ($sql_result = dmc_db_fetch_array($sql_query))
		    {      
				// ArtikelID von bestehendem Artikel ermitteln
				$Artikel_ID = $sql_result['products_id'];
				// Existiert
				$exists = 1;
			} //endif
			
			// Überprüfen ob Content bereits angelegt ist
			$cmd = "select products_id from products_content".
		            " where products_id = '$Artikel_ID'".
					 " and content_file = '$PDF_FILE'";
				//	 " and content_link = '$PDF_FILE'";
					
			$sql_query = dmc_db_query($cmd);
		     	 
		    if ($sql_result = dmc_db_fetch_array($sql_query))
		    {      
				// ArtikelID von bestehendem Artikel ermitteln
				$Artikel_ID = $sql_result['products_id'];
				// Existiert
				$exists_content = 1;
			} //endif
			
			// Wenn Artikel existiert, Details zuordnen 
			if ($exists == 1) {
				// Details eintragen : 
				// INSERT INTO `products_content` (`content_id`, `products_id`, `group_ids`, `content_name`, `content_file`, `content_link`, `languages_id`, `content_read`, `file_comment`) VALUES
				// (5, 824, '', 'pdf', '', 'http://www.loescher.com/PDF/10009.pdf', 2, 0, '');
				$sql_data_array = array(	'products_id' => $Artikel_ID,
											// 'group_ids' => '',
											'content_name' => $PDF_TITLE_DE,
											'content_file' => $PDF_FILE,
											// 'content_link' => $PDF_FILE,
											'content_link' => '',
											// 'languages_id' => 2,				// Deutsch
											'content_read' => 0,
											'file_comment' => $PDF_TITLE_DE
											);	
				$sql_data_array['languages_id']=2;	// Deutsch
				if ($exists_content==1)	// update
					dmc_sql_update_array(TABLE_PRODUCTS_CONTENT, $sql_data_array, "products_id='$Artikel_ID'");
				else // insert
					dmc_sql_insert_array(TABLE_PRODUCTS_CONTENT, $sql_data_array);
					
			/*	$sql_data_array['languages_id']=1;	// Englisch
				if ($exists_content==1)	// update
					dmc_sql_update_array(TABLE_PRODUCTS_CONTENT, $sql_data_array, "products_id='$Artikel_ID'");
				else // insert
					dmc_sql_insert_array(TABLE_PRODUCTS_CONTENT, $sql_data_array);
			*/
			  if (DEBUGGER>=1) fwrite($dateihandle, "Detail Zuordnung Content-Manager eingetragen.\n");
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Detail Content-Manager nicht eingetragen, da  Artikel nicht existent.\n");
			} //  endif Wenn Artikel existieren
		} // end exportmodus pdfs_content

		// Exportmodus pdf für gambiogx - content manager
		if ($ExportModusSpecial=='gambiogx_pdfs') {
			//select 'pdfs' as ExportModus, p.ARTNR as Artikelnummer(Freifeld2),   concat('Mehr Informationen zu ',p.ARTNR) as pdf_title_de (Freifeld3),
			// p.Fld02 as pdf_file (Freifeld4), concat('more informations to ',p.ARTNR)  as pdf_title_en (Freifeld5),  '' as Freifeld6, 
			$Artikel_Artikelnr = $Freifeld{2};
			$PDF_FILE = $Freifeld{4};
			$PDF_TITLE_DE = $Freifeld{3};
			$PDF_TITLE_EN = $Freifeld{5};
		
		
			// Der Artikelnummer zugehörige products_id ermitteln
			// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
			$cmd = "select products_id from " . TABLE_PRODUCTS .
		            " where products_model = '$Artikel_Artikelnr'";
					
			$sql_query = dmc_db_query($cmd);
		     	 
		    if ($sql_result = dmc_db_fetch_array($sql_query))
		    {      
				// ArtikelID von bestehendem Artikel ermitteln
				$Artikel_ID = $sql_result['products_id'];
				// Existiert
				$exists = 1;
			} //endif
			
			// Überprüfen ob Content bereits angelegt ist
			$cmd = "select products_id from products_content".
		            " where products_id = '$Artikel_ID'".
					 " and content_link = '$PDF_FILE'";
					
			$sql_query = dmc_db_query($cmd);
		     	 
		    if ($sql_result = dmc_db_fetch_array($sql_query))
		    {      
				// ArtikelID von bestehendem Artikel ermitteln
				$Artikel_ID = $sql_result['products_id'];
				// Existiert
				$exists_content = 1;
			} //endif
			
			// Wenn Artikel existiert, Details zuordnen 
			if ($exists == 1) {
				// Details eintragen : 
				// INSERT INTO `products_content` (`content_id`, `products_id`, `group_ids`, `content_name`, `content_file`, `content_link`, `languages_id`, `content_read`, `file_comment`) VALUES
				// (5, 824, '', 'pdf', '', 'http://www.loescher.com/PDF/10009.pdf', 2, 0, '');
				$sql_data_array = array(	'products_id' => $Artikel_ID,
											'group_ids' => '',
											'content_name' => $PDF_TITLE_DE,
											'content_file' => '',
											'content_link' => $PDF_FILE,
											// 'languages_id' => 2,				// Deutsch
											'content_read' => 0,
											'file_comment' => $PDF_TITLE_DE
											);	
				$sql_data_array['languages_id']=2;	// Deutsch
				if ($exists_content==1)	// update
					dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, "products_id='$Artikel_ID'");
				else // insert
					dmc_sql_insert_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
					
				$sql_data_array['languages_id']=1;	// Englisch
				if ($exists_content==1)	// update
					dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, "products_id='$Artikel_ID'");
				else // insert
					dmc_sql_insert_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);

			  if (DEBUGGER>=1) fwrite($dateihandle, "Detail Zuordnung Content-Manager eingetragen.\n");
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Detail Content-Manager nicht eingetragen, da  Artikel nicht existent.\n");
			} //  endif Wenn Artikel existieren
		} // end exportmodus pdfs
		
		// Exportmodus languages
		if ($ExportModusSpecial=='languages') {
			// Beispiel selectline: select 'languages' AS uebertragungsart,  ab.Artikelnummer AS Artikel_Artikelnr, 'es' AS SprachID, CASE WHEN (ISNULL(ab.Bezeichnung,p.Bezeichnung)='') THEN p.Bezeichnung ELSE ISNULL(ab.Bezeichnung,p.Bezeichnung) END AS Artikel_Bezeichnung, ISNULL(ab.Langtext,'') AS Artikel_Text, ISNULL(ab.Zusatz,'') AS Artikel_Kurztext, '' AS Meta_Title, '' AS Meta_Desc, '' AS Meta_Keyw, 'c'+p.Artikelgruppe AS Kategorie_id, '' AS FF11, '' AS FF12 FROM ART as p INNER JOIN ARTBEZ as ab ON (p.Artikelnummer = ab.Artikelnummer AND ab.Sprache='1') WHERE p.ShopAktiv = 'true'
 
			fwrite($dateihandle, "Export Sprachen\n");
			$Artikel_Artikelnr = $Freifeld{2};
			$Sprache_id = $Freifeld{3};
			$Artikel_Bezeichnung = sonderzeichen2html(true,$Freifeld{4});
			$Artikel_Text = sonderzeichen2html(true,$Freifeld{5});
			$Artikel_Kurztext =sonderzeichen2html(true,$Freifeld{6});
			$Kategorie_ID =sonderzeichen2html(true,$Freifeld{10});
			fwrite($dateihandle, "Artikel_Artikelnr = $Artikel_Artikelnr\n");
	
			// Der Artikelnummer zugehörige products_id und xsell_id ermitteln
			// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
			$exists = 1;
			$Artikel_ID=dmc_get_id_by_artno($Artikel_Artikelnr);
			if ($Artikel_ID == "") $exists = 0;		
			
			// Wenn Artikel existiert, Details zuordnen 
			if ($exists == 1 && strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
				// Presta
				$id_shop=1;
				$query = "UPDATE ps_product_lang SET ";
				if ($Artikel_Bezeichnung!="") $query .= "name='".$Artikel_Bezeichnung."', meta_title='".$Artikel_Bezeichnung."', ";
				if ($Artikel_Text!="") $query .= "description='".$Artikel_Text."', ";
				if ($Artikel_Kurztext!="") $query .= "description_short='".$Artikel_Kurztext."', ";
				$query .= "id_shop=$id_shop ";		// NICHT ENTFERNEN, auch wenn "sinnlos"
				$query .= " WHERE id_product=$Artikel_ID AND id_shop=$id_shop AND id_lang=$Sprache_id";
				if ($Artikel_Bezeichnung!="" || $Artikel_Kurztext!="" || $Artikel_Text!="")
					dmc_db_query($query);

			} else if ($exists == 1) {
				// Details eintragen : 
				// select 'languages' as ExportModus, p.ARTNR as Artikelnummer,  '1' as Language_ID, p.Fld01 as Artikel_Bezeichnung,  p.ZUSTEXT1 as Artikel_Text,  '' as Freifeld6, '' as Freifeld7,'
				// ' as Freifeld8,'' as Freifeld9,'' as Freifeld10,'' as Freifeld11,'' as Freifeld12 FROM SG_AUF_ARTIKEL as p where p.Fld01 <> '' or p.ZUSTEXT2 <> ''
				$sql_data_array = array(
					'products_name' => $Artikel_Bezeichnung,
					'products_description' => $Artikel_Text
					//'products_url' => str_replace('http://', '', $Artikel_MetaUrl) 
					);			// ohne http://
				
				// Keine Details etc in ZENCART 
				if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false && strpos(strtolower(SHOPSYSTEM), 'veyton') === false) {
					$sql_data_array['products_short_description'] = $Artikel_Kurztext;
					$sql_data_array['products_meta_title'] = $Artikel_Bezeichnung;
					$sql_data_array['products_meta_description'] = $Artikel_Kurztext;
					$sql_data_array['products_meta_keywords'] =  $Artikel_Bezeichnung;
				} 
				
				if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false) {
					if (PRODUCTS_DETAILS != "")
						$sql_data_array['products_details'] = PRODUCTS_DETAILS;
					if (PRODUCTS_SPECS != "")
						$sql_data_array['products_specs'] = PRODUCTS_SPECS;
				}
				
				// 
				if (strpos(strtolower(SHOPSYSTEM), 'veyton') === false) {
					// Update oder insert artikel beschreibung
					if (dmc_entry_exists(TABLE_PRODUCTS_DESCRIPTION,"products_id",$Artikel_ID,"and","language_id",$Sprache_id)) {
						dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, "products_id ='$Artikel_ID' and language_id = '" .$Sprache_id . "'");
					} else {
						$sql_data_array['language_id'] = $Sprache_id;
						$sql_data_array['products_id'] = $Artikel_ID;
						dmc_sql_insert_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
					}
				} else {
					// Update oder insert artikel beschreibung	Veyton
					$sql_data_array['products_short_description'] = $Artikel_Kurztext;
					$sql_data_array['products_keywords'] =  $Artikel_Bezeichnung;
					if (dmc_entry_exists(TABLE_PRODUCTS_DESCRIPTION,"products_id",$Artikel_ID,"and","language_code",$Sprache_id)) {
						fwrite($dateihandle, "Update languages fuer products_id ='$Artikel_ID' and language_code = '" . $Sprache_id . "'\n");
						dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, "products_id ='$Artikel_ID' and language_code = '" .$Sprache_id . "'");
					} else {
						fwrite($dateihandle, "Insert languages fuer products_id ='$Artikel_ID' and language_code = '" . $Sprache_id . "'\n");
						$sql_data_array['language_code'] = $Sprache_id;
						$sql_data_array['products_id'] = $Artikel_ID;
						dmc_sql_insert_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
						$Artikel_SEO_Bezeichnung =  dmc_prepare_seo ($Artikel_ID."_".$Artikel_Bezeichnung,"product",$Kategorie_ID,$Sprache_id);
						fwrite($dateihandle, "languages 4 SEO Veyton -> $Artikel_SEO_Bezeichnung\n");
						// Update oder insert SEO
						$seo_sql_data = array(				
							'url_md5' => md5($Sprache_id.'/'.$Artikel_SEO_Bezeichnung),
							'url_text' => $Sprache_id.'/'.$Artikel_SEO_Bezeichnung,
							'language_code' => $Sprache_id,
							'link_type' => '1',   			//2 = kategorie
							'meta_description' => $Artikel_Kurztext,
							'meta_title' => $Artikel_Bezeichnung,
							'meta_keywords' => $Artikel_Bezeichnung,
							'link_id' =>  $Artikel_ID);		
						
						if (SHOPSYSTEM_VERSION>=4.2) $seo_sql_data['store_id'] = SHOP_ID;		// Ab veyton 4.2 
							
						if (dmc_entry_exists(TABLE_SEO_URL,"link_id",$Artikel_ID,"and","language_code",$Sprache_id)) {
							dmc_sql_update_array(TABLE_SEO_URL, $seo_sql_data, "link_id ='$Artikel_ID' and language_code = '" .$Sprache_id . "' and link_type='1'");
						} else {
							dmc_sql_insert_array(TABLE_SEO_URL, $seo_sql_data);
						}
					}
					
				}
				if (DEBUGGER>=1) fwrite($dateihandle, "Detail Zuordnung eingetragen.\n");				
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Detail nicht eingetragen, da  Artikel nicht existent.\n");
			} //  endif Wenn Artikel existieren
		} // end exportmodus languages
		
		// Exportmodus languages
		if ($ExportModusSpecial=='cat_languages') {
		
			fwrite($dateihandle, "Export cat_languages\n");
			$WaWi_Kategorie_ID = $Freifeld{2};
			$Sprache_id = $Freifeld{3};
			$Kategorie_Bezeichnung = sonderzeichen2html(true,$Freifeld{4});
			$Kategorie_Text = sonderzeichen2html(true,$Freifeld{5});
			$Kategorie_Meta_Title = sonderzeichen2html(true,$Freifeld{6});
			$Kategorie_Meta_Desc = sonderzeichen2html(true,$Freifeld{7});
			$Kategorie_Meta_Keyw = sonderzeichen2html(true,$Freifeld{8});
			$Kategorie_ID=dmc_get_category_id($WaWi_Kategorie_ID);
			if ($Kategorie_ID=='0')
				$exists = false;
			else
				$exists = true;
			
			fwrite($dateihandle, "Kategorie_ID = $WaWi_Kategorie_ID (ShopID=$Kategorie_ID) fuer Sprache $Sprache_id\n");
			// Wenn Kategorie existiert, Details updaten  
			if ($exists) {
				// Details eintragen : 
				// select 'languages' as ExportModus, p.ARTNR as Artikelnummer,  '1' as Language_ID, p.Fld01 as Artikel_Bezeichnung,  p.ZUSTEXT1 as Artikel_Text,  '' as Freifeld6, '' as Freifeld7,'
				// ' as Freifeld8,'' as Freifeld9,'' as Freifeld10,'' as Freifeld11,'' as Freifeld12 FROM SG_AUF_ARTIKEL as p where p.Fld01 <> '' or p.ZUSTEXT2 <> ''
				// übermittelte Kategoriebezeichnung, z.B. Installation\Fittings\Lötfittings\Übergangsmuffen		
		
				if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
					$sql_data_array = 
							array(
								//'language_code' => $Sprache_id,
								'categories_name' => $Kategorie_Bezeichnung,
								// 'categories_heading_title' => $Kategorie_Meta_Title
								'categories_description' => $Kategorie_Text
							);
				
					$Kategorie_SEO_Bezeichnung = dmc_prepare_seo ($Kategorie_Bezeichnung,"category",$Kategorie_ID,$Sprache_id);
			
					$seo_data_array = array(		
					'url_md5' => md5($Sprache_id.'/'.$Kategorie_SEO_Bezeichnung),
					'url_text' => $Sprache_id.'/'.$Kategorie_SEO_Bezeichnung,
					'language_code' => $Sprache_id,
					'link_type' => '2',   			//2 = kategorie
					'meta_description' => $meta_desc,
					'meta_title' => $meta_desc,
					'meta_keywords' => $meta_desc,
					'link_id' =>  $Kategorie_ID);	
					if (SHOPSYSTEM_VERSION>=4.2) $seo_data_array['store_id'] = SHOP_ID;		// Ab veyton 4.2 
								
				} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false){
					$sql_data_array = array(	
								//'id_category' => $Kategorie_ID,
								//'id_lang' => $Sprache_id,
								'name' => $Kategorie_Bezeichnung,
								'description' =>  $Kategorie_Text,
								//'link_rewrite' => $language_id.'/'.$seo,
								'meta_description' => $Kategorie_Meta_Desc,
								'meta_title' => $Kategorie_Meta_Title,
								'meta_keywords' => $Kategorie_Meta_Keyw);	
				} else {
					$sql_data_array = 
							array(	//'categories_id' => $Kategorie_ID,
									//  'language_id' => $language_id,
									  'categories_name' => $Kategorie_Bezeichnung,
									  'categories_description' => $Kategorie_Text);
				}

				// Bestehende Daten laden
				if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
					if (dmc_entry_exists(TABLE_CATEGORIES_DESCRIPTION,"categories_id",$Kategorie_ID,"and","language_code",$Sprache_id)) {
						fwrite($dateihandle, "Update language fuer kategorie_id ='$Kategorie_ID' and language_code = '" . $Sprache_id . "'\n");
						dmc_sql_update_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, "categories_id ='$Kategorie_ID' and language_code = '" .$Sprache_id . "'");
						// VEYTON SEO
						dmc_sql_update_array(TABLE_SEO_URL, $seo_data_array, "link_id ='$Kategorie_ID' and language_code = '" .$Sprache_id . "' and link_type='2'");
						fwrite($dateihandle, "Update $Sprache_id.'/'.$Kategorie_SEO_Bezeichnung fuer kategorie_id ='$Kategorie_ID' and language_code = '" . $Sprache_id . "'\n");
					} else {
						$sql_data_array['categories_id']=$Kategorie_ID;
						$sql_data_array['language_code']=$Sprache_id;
						fwrite($dateihandle, "Insert $Sprache_id.'/'.$Kategorie_SEO_Bezeichnung fuer kategorie_id ='$Kategorie_ID' and language_code = '" . $Sprache_id . "'\n");
						dmc_sql_insert_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
						// VEYTON SEO
						dmc_sql_insert_array(TABLE_SEO_URL, $seo_data_array);
					}		
				} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
					fwrite($dateihandle, "Update languages fuer kategorie_id ='$id_category' and language_code = '" . $Sprache_id . "'\n");
					dmc_sql_update_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, "categories_id ='$Kategorie_ID' and id_lang = '" .$Sprache_id . "'");
				} else {
					fwrite($dateihandle, "Update languages fuer kategorie_id ='$Kategorie_ID' and language_id = '" . $Sprache_id . "'\n");
					dmc_sql_update_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, "categories_id ='$Kategorie_ID' and language_id = '" . $Sprache_id . "'");
				}
				
				if (DEBUGGER>=1) fwrite($dateihandle, "cat_languages Zuordnung eingetragen.\n");
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: cat_languages Zuordnung nicht eingetragen, da  Kategorie nicht existent.\n");
			} //  endif Wenn Artikel existieren
		} // end exportmodus cat_languages
			
	
	// Exportmodus staffelpreise
		if ($ExportModusSpecial=='staffelpreise' || $ExportModusSpecial=='staffelpreis') {
			
			if (DEBUGGER>=1)  fwrite($dateihandle, "details - staffelpreise\n");
			
			// select DISTINCT 'staffelpreis' as ExportModus, p.Artikelnummer AS Artikel_Artikelnr, vk.[Menge] AS Artikel_Preis_Ab_Menge, ISNULL(vk.[Preis],p.[PreisVK])-ISNULL(vk.[Abzug],0) AS Artikel_Preis, '' as Artikel_Preis2, '' as Artikel_Preis3, '' as Artikel_Preis4, '' as Artikel_Preis5, '' AS Artikel_Preis_Gruppe, ISNULL(vk.[Rabatt],'') AS RabattProzent, '' AS Waehrung,  '' AS Website_ID, p.GeändertAm as timestamp FROM ewa.Artikel AS p INNER JOIN [ewa].[ArtikelStaffel] AS vk ON p.GUID=vk.Artikel WHERE (p.WebShop1Artikel = 1) 	

			$Artikel_Artikelnr = $Freifeld{2};
			$abMenge = $Freifeld{3};
			$Artikel_Preis1 = $Freifeld{4};
			$Artikel_Preis2 = $Freifeld{5};
			$Artikel_Preis3 = $Freifeld{6};
			$Artikel_Preis4 = $Freifeld{7};
			$Artikel_Preis5 = $Freifeld{8};
		
			$Artikel_Preis_Gruppe= $Freifeld{9}; // Zur Zeit noch nicht verwendet
			$Artikel_Preis_Rabatt = $Freifeld{10};
				if ($Artikel_Preis_Rabatt=='') $Artikel_Preis_Rabatt=0;
			$Waehrung = $Freifeld{11}; // Zur Zeit noch nicht verwendet
			$websiteNr = $Freifeld{12}; // Zur Zeit noch nicht verwendet
			
			// Preisberechnung, wenn separater Rabatt (oder Aufpreis) uebermittelt
			if ($Artikel_Preis_Rabatt<>0) {
				$Artikel_Preis = $Artikel_Preis - ($Artikel_Preis*$Artikel_Preis_Rabatt/100);
			}
			
			// Der Artikelnummer zugehörige products_id und xsell_id ermitteln
			// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
			
			if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)
				$cmd = "select id_product AS products_id from " . TABLE_PRODUCTS .
		            " where reference = '$Artikel_Artikelnr'";
			else
				$cmd = "select products_id from " . TABLE_PRODUCTS .
		            " where products_model = '$Artikel_Artikelnr'";
					
			$sql_query = dmc_db_query($cmd);
		     	 
		    if ($sql_result = dmc_db_fetch_array($sql_query))
		    {      
				// ArtikelID von bestehendem Artikel ermitteln
				$Artikel_ID = $sql_result['products_id'];
				// Existiert
				$exists = 1;
			} //endif
			
			// Abweichend fuer presta -> alle kundengruppen
			
			if (strpos(strtolower(SHOPSYSTEM), 'osc') !== false && ($exists == 1) && ($Artikel_Preis1 !="") ) {
				// OSC Commerce Modul Pricebreak
				if ($Artikel_Preis1=='280273' || $abMenge=='280273') {
					if (DEBUGGER>=1) fwrite($dateihandle, "Staffelpreise OSC Commerce Modul Pricebreak LOESCHEN für Artikel_ID =".$Artikel_ID." \n");
					$query = "DELETE FROM products_price_break WHERE id_product=".$Artikel_ID;
					dmc_sql_query($query);
				} else {
					if (DEBUGGER>=1) fwrite($dateihandle, "Staffelpreis OSC Commerce Modul Pricebreak setzen AbMenge ".$abMenge." und Preis ".$Artikel_Preis1." für Artikel_ID =".$Artikel_ID." \n");
					$query = "INSERT INTO `products_price_break` (`products_id`,`products_price`,`products_qty`) "
							."VALUES ('".$Artikel_ID."','".$Artikel_Preis1."','".$abMenge."')";
					dmc_sql_query($query);
				}
			} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false && ($exists == 1) && ($Artikel_Preis1 !="") ) {
				/* BEISPIELABFRAGEN
				<!-- STAFFELPREISE loeschen -->
				select DISTINCT 'staffelpreis' as ExportModus, p.NUMBER_ELEMENT_COUNT_0 AS Artikel_Artikelnr, '280273' AS Artikel_Preis_Ab_Menge, '280273' AS Artikel_Preis, '' as Artikel_Preis2, '' as Artikel_Preis3, '' as Artikel_Preis4, '' as Artikel_Preis5, '' AS Artikel_Preis_Gruppe, '' AS RabattProzent, '' AS Waehrung,  '' AS Website_ID, '' as timestamp FROM ITEM_AS_CHILD_IN_ROOT p INNER JOIN STAFFELPREISE_AS_CHILD_IN_ROOT pr ON p.ROW_ID_XX = pr.PARENT_ELEMENT_ROW_ID_XX WHERE pr.PREIS_AB1_ELEMENT_COUNT_0<>'0' AND pr.MENGE_AB1_ELEMENT_COUNT_0<>'66' AND pr.PREIS_AB1_ELEMENT_COUNT_0<>'' AND pr.MENGE_AB1_ELEMENT_COUNT_0<>'0' AND pr.MENGE_AB1_ELEMENT_COUNT_0<>'' AND p.NUMBER_ELEMENT_COUNT_0 LIKE '$variable1%'
				<!-- STAFFELPREISE 1 -->
				select DISTINCT 'staffelpreis' as ExportModus, p.NUMBER_ELEMENT_COUNT_0 AS Artikel_Artikelnr, pr.MENGE_AB1_ELEMENT_COUNT_0 AS Artikel_Preis_Ab_Menge, pr.PREIS_AB1_ELEMENT_COUNT_0 AS Artikel_Preis, '' as Artikel_Preis2, '' as Artikel_Preis3, '' as Artikel_Preis4, '' as Artikel_Preis5, '' AS Artikel_Preis_Gruppe, '' AS RabattProzent, '' AS Waehrung,  '' AS Website_ID, '' as timestamp FROM ITEM_AS_CHILD_IN_ROOT p INNER JOIN STAFFELPREISE_AS_CHILD_IN_ROOT pr ON p.ROW_ID_XX = pr.PARENT_ELEMENT_ROW_ID_XX WHERE pr.PREIS_AB1_ELEMENT_COUNT_0<>'0' AND pr.MENGE_AB1_ELEMENT_COUNT_0<>'66' AND pr.PREIS_AB1_ELEMENT_COUNT_0<>'' AND pr.MENGE_AB1_ELEMENT_COUNT_0<>'0' AND pr.MENGE_AB1_ELEMENT_COUNT_0<>'' AND p.NUMBER_ELEMENT_COUNT_0 LIKE '$variable1%'
				*/
				if ($Artikel_Preis1=='280273' || $abMenge=='280273') {
					if (DEBUGGER>=1) fwrite($dateihandle, "Staffelpreise Presta LOESCHEN für Artikel_ID =".$Artikel_ID." \n");
					$query = "DELETE FROM ps_specific_price WHERE id_product=".$Artikel_ID;
					dmc_sql_query($query);
				} else {
					if (DEBUGGER>=1) fwrite($dateihandle, "Staffelpreis Presta setzen AbMenge ".$abMenge." und Preis ".$Artikel_Preis1." für Artikel_ID =".$Artikel_ID." \n");
					$query = "INSERT INTO `ps_specific_price` (`id_specific_price_rule`,`id_cart`,`id_product`,`id_shop`,`id_shop_group`,`id_currency`,`id_country`,`id_group`,`id_customer`,`id_product_attribute`,`price`,`from_quantity`,`reduction`,`reduction_tax`,`reduction_type`,`from`,`to`) VALUES ('0','0','".$Artikel_ID."','1','0','0','0','0','0','0','".$Artikel_Preis1."','".$abMenge."','0.000000','1','amount','0000-00-00 00:00:00','0000-00-00 00:00:00')";
					dmc_sql_query($query);
				}
				
			} else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false && ($Artikel_Preis1==$Artikel_Preis2 || $Artikel_Preis2=='')
									&& ($Artikel_Preis1==$Artikel_Preis3 || $Artikel_Preis3=='')
									&& ($Artikel_Preis1==$Artikel_Preis4 || $Artikel_Preis4=='')
									&& ($Artikel_Preis1==$Artikel_Preis5 || $Artikel_Preis5=='')) {
				// Abweichend fuer veyton -> alle kundengruppen
				if (DEBUGGER>=1) fwrite($dateihandle, "Alle Kundengruppen Veyton\n");
				$sql_data_price_array = array(
					'discount_quantity' => $abMenge,
					'price' => $Artikel_Preis1);
				// Wenn Preis-Zuordnung existiert -> update
				$cmd = "select id FROM " . TABLE_PRODUCTS_PRICE_GROUP."all ".
						" where products_id = '$Artikel_ID' and discount_quantity='$abMenge'";
								if (DEBUGGER>=1)  fwrite($dateihandle, "cmd=$cmd\n");
				$sql_query = dmc_db_query($cmd);
				if ($sql_result = dmc_db_fetch_array($sql_query)) {
					$temp_id=$sql_result['price_id'];
					$cmd = 	"select id FROM " . TABLE_PRODUCTS_PRICE_GROUP."all ".
							" where products_id = '$Artikel_ID' and discount_quantity='$abMenge'";	if (DEBUGGER>=1)  fwrite($dateihandle, "cmd=$cmd\n");
					dmc_sql_update_array(TABLE_PRODUCTS_PRICE_GROUP."all", $sql_data_price_array, "products_id = '$Artikel_ID' and discount_quantity='$abMenge'");
					dmc_sql_update(TABLE_PRODUCTS, "price_flag_graduated_all=1", "products_id=".$Artikel_ID."");
					if (DEBUGGER>=1)  fwrite($dateihandle, "update ".TABLE_PRODUCTS_PRICE_GROUP."all =  ArtID = '$Artikel_ID' and quantity='$abMenge' mit preis $Artikel_Preis1\n");
				}	else { // nicht existent
					// $sql_data_price_array['id'] = $NEUE_ID;
					$sql_data_price_array['products_id'] = $Artikel_ID;
					dmc_sql_insert_array(TABLE_PRODUCTS_PRICE_GROUP."all", $sql_data_price_array);
					// Veyton -> Abweichende Preise anzeigen Veyton
					dmc_sql_update(TABLE_PRODUCTS, "price_flag_graduated_all=1", "products_id=".$Artikel_ID."");
					if (DEBUGGER>=1)  fwrite($dateihandle, "insert(ed) in ".TABLE_PRODUCTS_PRICE_GROUP."all = ArtID $Artikel_ID and quantity='$abMenge' mit preis $Artikel_Preis1\n");
				}		
			} else { // nicht alle kundengruppen
				if ($exists==1) {	
				
					// Keine Kundenpreise in ZENCART
				if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false) {
					// Tabellen für Preis
					// Gastpreis -> personal_offers_by_customers_status_1
					// Neuer Kunde -> personal_offers_by_customers_status_2
					// Händler -> personal_offers_by_customers_status_3
					// Händler EU -> personal_offers_by_customers_status_4
					// Kundengruppenpreise setzen - Keine Kundenpreise in ZENCART virtuemart / todo presta
					$quantity=$abMenge;
					if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false && strpos(strtolower(SHOPSYSTEM), 'presta') === false && strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false)  {
						if (is_file('userfunctions/products/dmc_set_group_prices.php')) include ('userfunctions/products/dmc_set_group_prices.php');
						else include ('functions/products/dmc_set_group_prices.php');
					} // end if Preisupdate (strpos(strtolower(SHOPSYSTEM), 'zencart') === false) 
		
					/*
					for($Preisgruppe = 1; $Preisgruppe <= 15; $Preisgruppe++) {     //  durchlaufen	     
					
						if (DEBUGGER>=1) fwrite($dateihandle, "Details 590 TABLE_PRICE".$Preisgruppe." =".constant('TABLE_PRICE' . $Preisgruppe)." \n");
						
						if (defined('TABLE_PRICE' . $Preisgruppe) && constant('TABLE_PRICE' . $Preisgruppe) <> '')
						if ( ${"Artikel_Preis$Preisgruppe"} >0.01) {  	
							if (DEBUGGER>=1) fwrite($dateihandle, "Details 592=".constant('TABLE_PRICE' . $Preisgruppe)." mit preis=".${"Artikel_Preis$Preisgruppe"}." \n");	
							//Bei Veyton autoinc $temp_id_query = dmc_db_query("SELECT max(id) as total FROM " . constant('TABLE_PRICE' . $Preisgruppe) ."; ");	
							if (strpos(strtolower(SHOPSYSTEM), 'veyton') === false) {
								$abfrage="SELECT max(price_id) as total FROM " .constant('TABLE_PRICE' . $Preisgruppe) ." ";
								$temp_id_query = dmc_db_query($abfrage);	
								if ($TEMP_ID['total']!='null') { 
									try {
										$TEMP_ID = dmc_db_fetch_array($temp_id_query);
									} catch (MyException $e) {
										/* weiterwerfen der Exception */
										// throw $e;
										// Noch keine Einträge in Tablle vorhanden
						/*				$TEMP_ID['total']='null';
									}
								}
								if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
									$NEUE_ID = 1;
								 else
									$NEUE_ID = $TEMP_ID['total'] + 1;
							} // end if (strpos(strtolower(SHOPSYSTEM), 'veyton') === false) {
							
							// Zu setzenden Preis ermitteln
							$pricenumber = constant('GROUP_PRICE' . $Preisgruppe);
							$products_price=${"Artikel_Preis$pricenumber"};
							if (DEBUGGER>=1) fwrite($dateihandle, "616 == Kundengruppenpreis Artikel_Preis".$pricenumber." mit $products_price ab $abMenge Stueck setzen fuer Shop-Kundengruppe $pricenumber ");
							// Für (zusätzliche) Preise
							// price_id   	  products_id   	  quantity   	  personal_offer
							// quantity   =1 , da keine Staffelpreise	 
							if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false)
								$sql_data_price_array = array(
									'discount_quantity' => $abMenge,
									'price' => $products_price);
							else 
								$sql_data_price_array = array(
									'quantity' => $abMenge,
									'personal_offer' => $products_price);
									
							if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false) 
									if (STORE_ID != "")
										$sql_data_price_array['store_id'] = STORE_ID;
									else 
										$sql_data_price_array['store_id'] = 1;
							
							// Wenn Preis-Zuordnung existiert -> update
							if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false)
								$cmd = "select id FROM " . TABLE_PRODUCTS_PRICE_GROUP. $Preisgruppe .
												" where products_id = '$Artikel_ID' and discount_quantity='$abMenge'";
							else 
								$cmd = "select price_id from " . constant('TABLE_PRICE' . $Preisgruppe) .
											" where products_id = '$Artikel_ID' and quantity='$abMenge'";
							
							$sql_query = dmc_db_query($cmd);
							if ($sql_result = dmc_db_fetch_array($sql_query)) {
								$temp_id=$sql_result['price_id'];
								if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
									$cmd = 	"select id FROM " . TABLE_PRODUCTS_PRICE_GROUP. $Preisgruppe .
											" where products_id = '$Artikel_ID' and discount_quantity='$abMenge'";
									dmc_sql_update_array(TABLE_PRODUCTS_PRICE_GROUP. $Preisgruppe, $sql_data_price_array, "products_id = '$Artikel_ID' and discount_quantity='$abMenge'");
								} else {
									$cmd = 	"select price_id from " . constant('TABLE_PRICE' . $Preisgruppe) .
											" where products_id = '$Artikel_ID' and quantity='$abMenge'";
									dmc_sql_update_array(constant('TABLE_PRICE' . $Preisgruppe), $sql_data_price_array, "products_id = '$Artikel_ID' and quantity='$abMenge'");
								}
								if (DEBUGGER>=1)  fwrite($dateihandle, "\nupdate sql1 =  ArtID = '$Artikel_ID' and quantity='$abMenge' mit preis $products_price\n");
							}
							else { // nicht existent
								if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
									// $sql_data_price_array['id'] = $NEUE_ID;
									$sql_data_price_array['products_id'] = $Artikel_ID;
									dmc_sql_insert_array(TABLE_PRODUCTS_PRICE_GROUP. $Preisgruppe, $sql_data_price_array);
								} else {
									$sql_data_price_array['price_id'] = $NEUE_ID;
									$sql_data_price_array['products_id'] = $Artikel_ID;
									dmc_sql_insert_array(constant('TABLE_PRICE' . $Preisgruppe), $sql_data_price_array);
								}
								if (DEBUGGER>=1)  fwrite($dateihandle, "\ninsert(ed) sql1 in ".constant('TABLE_PRICE' . $Preisgruppe)."= ArtID $Artikel_ID and quantity='$abMenge' mit preis $products_price auf id= $NEUE_ID\n");
								// Veyton -> Abweichende Preise anzeigen Veyton
								dmc_sql_update(TABLE_PRODUCTS, "price_flag_graduated_".$Preisgruppe."=1", "products_id=".$Artikel_ID.";");
								
							}		
															
						} // end if 
					} // end for
					*/
				} // end if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false) 
			
					
				  if (DEBUGGER>=1) fwrite($dateihandle, "Detail Zuordnung eingetragen.\n");
				} else { 
					if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Detail nicht eingetragen, da  Artikel nicht existent.\n");
				} //  endif Wenn Artikel existieren
			} // end if  // nicht alle kundengruppen
			
		} // end exportmodus staffelpreise
		
		
		// Exportmodus staffelpreise
		if ($ExportModusSpecial=='staffelpreiseALT') {
			$Artikel_Artikelnr = $Freifeld{2};
			$abMenge = $Freifeld{3};
			$Artikel_Preis1 = $Freifeld{4};
			$Artikel_Preis2 = $Freifeld{5};
			$Artikel_Preis3 = $Freifeld{6};
			$Artikel_Preis4 = $Freifeld{7};
			$Artikel_Preis5 = $Freifeld{8};
	
			// Der Artikelnummer zugehörige products_id und xsell_id ermitteln
			// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
			$cmd = "select products_id from " . TABLE_PRODUCTS .
		            " where products_model = '$Artikel_Artikelnr'";
					
					if (DEBUGGER>=1)  fwrite($dateihandle, "details sql = ".$cmd);
						
					
			$sql_query = dmc_db_query($cmd);
		     	 
		    if ($sql_result = dmc_db_fetch_array($sql_query))
		    {      
				// ArtikelID von bestehendem Artikel ermitteln
				$Artikel_ID = $sql_result['products_id'];
				// Existiert
				$exists = 1;
			} //endif
			
			
		if ($exists ==1) {	
			if ( $Artikel_Preis1 >0.01 ){  	
				// Anzahl Einträge in Tabelle ermitteln
						 // $temp_id_query = dmc_db_query("select count(*) as total from " . TABLE_PRICE1 ." ");
						 $temp_id_query = dmc_db_query("SELECT max(price_id) as total from " . TABLE_PRICE1 ." ");				
						 $TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
							$NEUE_ID = 1;
						 else
							$NEUE_ID = $TEMP_ID['total'] + 1;
						 
						if (DEBUGGER>=1)  fwrite($dateihandle, "NEUE_ID ".$NEUE_ID." von dm_details tmp ".$TEMP_ID['total']."\n");
						// Für (zusätzliche) Preise  (Artikel_Preis1-4)
						$sql_data_price1_array = array(
					        'products_id' => $Artikel_ID,
					        'quantity' => $abMenge,
					        'personal_offer' => $Artikel_Preis1);
							// Wenn Preis-Zuornung existiert (Fehler von XTC beim Artikel loeschen) -> update
								$cmd = "select price_id from " . TABLE_PRICE1 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'";
								$sql_query = dmc_db_query($cmd);
								if ($sql_result = dmc_db_fetch_array($sql_query)) {
									$temp_id=$sql_result['price_id'];
									dmc_sql_update_array(TABLE_PRICE1, $sql_data_price1_array, "price_id = '$temp_id' and quantity='$abMenge'");	
										if (DEBUGGER>=1)  fwrite($dateihandle, "update sql1 = Nummer $Artikel_ID ID = '$temp_id' and quantity='$abMenge' mit preis $Artikel_Preis1");
									}
								else { // nicht existent
									$sql_data_price1_array['price_id'] = $NEUE_ID;
									dmc_sql_insert_array(TABLE_PRICE1, $sql_data_price1_array);
									if (DEBUGGER>=1)  fwrite($dateihandle, "insert sql1 = Nummer $Artikel_ID and quantity='$abMenge' mit preis $Artikel_Preis1");
								}
				}
				if ( $Artikel_Preis2 >0.01 ){  	
				// Anzahl Einträge in Tabelle ermitteln
						 // $temp_id_query = dmc_db_query("select count(*) as total from " . TABLE_PRICE2 ." ");
						 $temp_id_query = dmc_db_query("SELECT max(price_id) as total from " . TABLE_PRICE2 ." ");				
						 $TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
							$NEUE_ID = 1;
						 else
							$NEUE_ID = $TEMP_ID['total'] + 1;
						 
						if (DEBUGGER>=1)  fwrite($dateihandle, "NEUE_ID ".$NEUE_ID." von dm_details tmp ".$TEMP_ID['total']."\n");
						// Für (zusätzliche) Preise  (Artikel_Preis1-4)
						$sql_data_PRICE2_array = array(
					        'products_id' => $Artikel_ID,
					        'quantity' => $abMenge,
					        'personal_offer' => $Artikel_Preis2);
							// Wenn Preis-Zuornung existiert (Fehler von XTC beim Artikel loeschen) -> update
								$cmd = "select price_id from " . TABLE_PRICE2 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'";
								$sql_query = dmc_db_query($cmd);
								if ($sql_result = dmc_db_fetch_array($sql_query)) {
									$temp_id=$sql_result['price_id'];
									dmc_sql_update_array(TABLE_PRICE2, $sql_data_PRICE2_array, "price_id = '$temp_id' and quantity='$abMenge'");
if (DEBUGGER>=1)  fwrite($dateihandle, "update sql2 = Nummer $Artikel_ID and quantity='$abMenge' mit preis $Artikel_Preis1");
																		
									}
								else { // nicht existent
									$sql_data_PRICE2_array['price_id'] = $NEUE_ID;
									dmc_sql_insert_array(TABLE_PRICE2, $sql_data_PRICE2_array);
									if (DEBUGGER>=1)  fwrite($dateihandle, "insert sql2 = Nummer $Artikel_ID and quantity='$abMenge' mit preis $Artikel_Preis1");
									
									}
				}
				 
				if ( $Artikel_Preis3 >0.01 ){  	
				// Anzahl Einträge in Tabelle ermitteln
						 // $temp_id_query = dmc_db_query("select count(*) as total from " . TABLE_PRICE3 ." ");
						 $temp_id_query = dmc_db_query("SELECT max(price_id) as total from " . TABLE_PRICE3 ." ");				
						 $TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
							$NEUE_ID = 1;
						 else
							$NEUE_ID = $TEMP_ID['total'] + 1;
						 
						if (DEBUGGER>=1)  fwrite($dateihandle, "NEUE_ID ".$NEUE_ID." von dm_details tmp ".$TEMP_ID['total']."\n");
						// Für (zusätzliche) Preise  (Artikel_Preis1-4)
						$sql_data_PRICE3_array = array(
					        'products_id' => $Artikel_ID,
					        'quantity' => $abMenge,
					        'personal_offer' => $Artikel_Preis3);
							// Wenn Preis-Zuornung existiert (Fehler von XTC beim Artikel loeschen) -> update
								$cmd = "select price_id from " . TABLE_PRICE3 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'";
								$sql_query = dmc_db_query($cmd);
								if ($sql_result = dmc_db_fetch_array($sql_query)) {
									$temp_id=$sql_result['price_id'];
									dmc_sql_update_array(TABLE_PRICE3, $sql_data_PRICE3_array, "price_id = '$temp_id' and quantity='$abMenge'");	
									}
								else { // nicht existent
									$sql_data_PRICE3_array['price_id'] = $NEUE_ID;
									dmc_sql_insert_array(TABLE_PRICE3, $sql_data_PRICE3_array);
									}
				}
/*
				if ( $Artikel_Preis4 >0.01 ){  	
				// Anzahl Einträge in Tabelle ermitteln
						 // $temp_id_query = dmc_db_query("select count(*) as total from " . TABLE_PRICE4 ." ");
						 $temp_id_query = dmc_db_query("SELECT max(price_id) as total from " . TABLE_PRICE4 ." ");				
						 $TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
							$NEUE_ID = 1;
						 else
							$NEUE_ID = $TEMP_ID['total'] + 1;
						 
						if (DEBUGGER>=1)  fwrite($dateihandle, "NEUE_ID ".$NEUE_ID." von dm_details tmp ".$TEMP_ID['total']."\n");
						// Für (zusätzliche) Preise  (Artikel_Preis1-4)
						$sql_data_PRICE4_array = array(
					        'products_id' => $Artikel_ID,
					        'quantity' => $abMenge,
					        'personal_offer' => $Artikel_Preis4);
							// Wenn Preis-Zuornung existiert (Fehler von XTC beim Artikel loeschen) -> update
								$cmd = "select price_id from " . TABLE_PRICE4 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'";
								$sql_query = dmc_db_query($cmd);
								if ($sql_result = dmc_db_fetch_array($sql_query)) {
									$temp_id=$sql_result['price_id'];
									dmc_sql_update_array(TABLE_PRICE4, $sql_data_PRICE4_array, "price_id = '$temp_id' and quantity='$abMenge'");	
									}
								else { // nicht existent
									$sql_data_PRICE4_array['price_id'] = $NEUE_ID;
									dmc_sql_insert_array(TABLE_PRICE4, $sql_data_PRICE4_array);
									}
				}
				
				if ( $Artikel_Preis5 >0.01 ){  	
				// Anzahl Einträge in Tabelle ermitteln
						 // $temp_id_query = dmc_db_query("select count(*) as total from " . TABLE_PRICE5 ." ");
						 $temp_id_query = dmc_db_query("SELECT max(price_id) as total from " . TABLE_PRICE5 ." ");				
						 $TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
							$NEUE_ID = 1;
						 else
							$NEUE_ID = $TEMP_ID['total'] + 1;
						 
						if (DEBUGGER>=1)  fwrite($dateihandle, "NEUE_ID ".$NEUE_ID." von dm_details tmp ".$TEMP_ID['total']."\n");
						// Für (zusätzliche) Preise  (Artikel_Preis1-4)
						$sql_data_PRICE5_array = array(
					        'products_id' => $Artikel_ID,
					        'quantity' => $abMenge,
					        'personal_offer' => $Artikel_Preis5);
							// Wenn Preis-Zuornung existiert (Fehler von XTC beim Artikel loeschen) -> update
								$cmd = "select price_id from " . TABLE_PRICE5 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'";
								$sql_query = dmc_db_query($cmd);
								if ($sql_result = dmc_db_fetch_array($sql_query)) {
									$temp_id=$sql_result['price_id'];
									dmc_sql_update_array(TABLE_PRICE5, $sql_data_PRICE5_array, "price_id = '$temp_id' and quantity='$abMenge'");	
									}
								else { // nicht existent
									$sql_data_PRICE5_array['price_id'] = $NEUE_ID;
									dmc_sql_insert_array(TABLE_PRICE5, $sql_data_PRICE5_array);
									}
				}
				*/
			  if (DEBUGGER>=1) fwrite($dateihandle, "Detail Zuordnung eingetragen.\n");
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Detail nicht eingetragen, da  Artikel nicht existent.\n");
			} //  endif Wenn Artikel existieren
		} // end exportmodus staffelpreiseALT
		
		// Exportmodus selectline_kalkulat
		if ($ExportModusSpecial=='Selectline_kalkulat') {
			if (DEBUGGER>=1)  fwrite($dateihandle, "Exportmodus selectline_kalkulat");
		
			$Artikel_Artikelnr = $Freifeld{2};
			$Einstandspreis = $Freifeld{3};
			$Preistyp = $Freifeld{4};		// 0=Aufschlag als Marge prozentual, 2=Aufschlag prozentual, 1=Rabatt auf Listenpreis (Gelten nur fuer Kalk1-4)
			$AufESAbs = $Freifeld{5};
			$AufESRel = $Freifeld{6};
			$AufKPAbs = $Freifeld{7};
			$AufKPRel = $Freifeld{8};
			// Kundenspezifische Kalkulationsbasis (z.B. 60%)
			$Kalk1 = $Freifeld{9};
			$Kalk2 = $Freifeld{10};
			$Kalk3 = $Freifeld{11};
			$Kalk4 = $Freifeld{12};
			if (DEBUGGER>=1)  fwrite($dateihandle, "2");
		// Uebergabe:
		// select 'selectline_kalkulat' as Freifeld1, p.Artikelnummer as Freifeld2, vk.ESPreis as Einstandspreis, CASE WHEN p.SSVerkauf = 3 THEN 5 ELSE 8 END AS Artikel_Steuersatz,  kalk.AufESAbs as Freifeld5, kalk.AufESRel as Freifeld6, kalk.AufKPAbs as Freifeld7, kalk.AufKPRel AS Freifeld8, '' as Freifeld9,'' as Freifeld10,'' as Freifeld11,'' as Freifeld12 FROM ART AS p, ARKALK vk, KALKULAT kalk WHERE (p.Inaktiv = '0') AND (p.Bezeichnung <> '') AND (p.ShopAktiv = 'true') AND p.Artikelnummer=vk.Artikelnummer AND vk.kalkschema <>'' and vk.kalkschema is not null AND vk.kalkschema=kalk.Nummer AND kalk.preistyp=2
		
			// Der Artikelnummer zugehörige products_id und xsell_id ermitteln
			// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
			$cmd = "select products_id from " . TABLE_PRODUCTS .
		            " where products_model = '$Artikel_Artikelnr'";
					
					if (DEBUGGER>=1)  fwrite($dateihandle, "details sql = ".$cmd);
						
					
			$sql_query = dmc_db_query($cmd);
		     	 
		    if ($sql_result = dmc_db_fetch_array($sql_query))
		    {      
				// ArtikelID von bestehendem Artikel ermitteln
				$Artikel_ID = $sql_result['products_id'];
				// Existiert
				$exists = 1;
			} //endif
			
			
		if ($exists ==1) {	
		
			// Standardpreis
			// Preisberechnung durchführen
			$Artikel_Preis = $Einstandspreis;
			$Artikel_Preis = $Artikel_Preis + $AufESAbs;
			$Artikel_Preis = $Artikel_Preis + ($Artikel_Preis * $AufESRel  /100);
			$Artikel_Preis = $Artikel_Preis + $AufKPAbs;
			$Artikel_Preis = $Artikel_Preis + ($Artikel_Preis * $AufKPRel  /100);
		
			$update_sql_data = array(   'products_price' => $Artikel_Preis,
										'products_last_modified' => 'now()');
			
			dmc_sql_update_array(TABLE_PRODUCTS, $update_sql_data, "products_id = '$Artikel_ID'");
		 
			if ( $Kalk1 >=0.00 ){
						// Preisberechnung durchführen
						$Artikel_Preis = $Einstandspreis;
						$Artikel_Preis = $Artikel_Preis + $AufESAbs;
						$Artikel_Preis = $Artikel_Preis + ($Artikel_Preis * $AufESRel /100);
						// je nach Preistyp 0=Aufschlag als Marge prozentual, 2=Aufschlag prozentual, 1=Rabatt auf Listenpreis (Gelten nur fuer Kalk1-4)
						if ($Preistyp == 2) {
							$Artikel_Preis = $Artikel_Preis + ($Artikel_Preis * $Kalk1 /100);
						} else {
							// Im Zweifel Berechnung wie für Standardpreis
							$Artikel_Preis = $Artikel_Preis + $AufKPAbs;
							$Artikel_Preis = $Artikel_Preis + ($Artikel_Preis * $AufKPRel /100);
						}
				// Anzahl Einträge in Tabelle ermitteln
						 // $temp_id_query = dmc_db_query("select count(*) as total from " . TABLE_PRICE1 ." ");
						 $temp_id_query = dmc_db_query("SELECT max(price_id) as total from " . TABLE_PRICE1 ." ");				
						 $TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
							$NEUE_ID = 1;
						 else
							$NEUE_ID = $TEMP_ID['total'] + 1;
						 
						if (DEBUGGER>=1)  fwrite($dateihandle, "NEUE_ID ".$NEUE_ID." von dm_details tmp ".$TEMP_ID['total']."\n");
						// Für (zusätzliche) Preise  (Artikel_Preis1-4)
						$sql_data_price1_array = array(
					        'products_id' => $Artikel_ID,
					        'quantity' => $abMenge,
					        'personal_offer' => $Artikel_Preis1);
							// Wenn Preis-Zuornung existiert (Fehler von XTC beim Artikel loeschen) -> update
								$cmd = "select price_id from " . TABLE_PRICE1 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'";
								$sql_query = dmc_db_query($cmd);
								if ($sql_result = dmc_db_fetch_array($sql_query)) {
									$temp_id=$sql_result['price_id'];
									dmc_sql_update_array(TABLE_PRICE1, $sql_data_price1_array, "price_id = '$temp_id' and quantity='$abMenge'");	
										if (DEBUGGER>=1)  fwrite($dateihandle, "update sql1 = Nummer $Artikel_ID ID = '$temp_id' and quantity='$abMenge' mit preis $Artikel_Preis1");
									}
								else { // nicht existent
									$sql_data_price1_array['price_id'] = $NEUE_ID;
									dmc_sql_insert_array(TABLE_PRICE1, $sql_data_price1_array);
									if (DEBUGGER>=1)  fwrite($dateihandle, "insert sql1 = Nummer $Artikel_ID and quantity='$abMenge' mit preis $Artikel_Preis1");
								}
				}
				if ( $Artikel_Preis2 >0.01 ){  	
				// Anzahl Einträge in Tabelle ermitteln
						 // $temp_id_query = dmc_db_query("select count(*) as total from " . TABLE_PRICE2 ." ");
						 $temp_id_query = dmc_db_query("SELECT max(price_id) as total from " . TABLE_PRICE2 ." ");				
						 $TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
							$NEUE_ID = 1;
						 else
							$NEUE_ID = $TEMP_ID['total'] + 1;
						 
						if (DEBUGGER>=1)  fwrite($dateihandle, "NEUE_ID ".$NEUE_ID." von dm_details tmp ".$TEMP_ID['total']."\n");
						// Für (zusätzliche) Preise  (Artikel_Preis1-4)
						$sql_data_PRICE2_array = array(
					        'products_id' => $Artikel_ID,
					        'quantity' => $abMenge,
					        'personal_offer' => $Artikel_Preis2);
							// Wenn Preis-Zuornung existiert (Fehler von XTC beim Artikel loeschen) -> update
								$cmd = "select price_id from " . TABLE_PRICE2 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'";
								$sql_query = dmc_db_query($cmd);
								if ($sql_result = dmc_db_fetch_array($sql_query)) {
									$temp_id=$sql_result['price_id'];
									dmc_sql_update_array(TABLE_PRICE2, $sql_data_PRICE2_array, "price_id = '$temp_id' and quantity='$abMenge'");
									if (DEBUGGER>=1)  fwrite($dateihandle, "update sql2 = Nummer $Artikel_ID and quantity='$abMenge' mit preis $Artikel_Preis1");
																		
									}
								else { // nicht existent
									$sql_data_PRICE2_array['price_id'] = $NEUE_ID;
									dmc_sql_insert_array(TABLE_PRICE2, $sql_data_PRICE2_array);
									if (DEBUGGER>=1)  fwrite($dateihandle, "insert sql2 = Nummer $Artikel_ID and quantity='$abMenge' mit preis $Artikel_Preis1");
									
									}
				}
				 
				if ( $Artikel_Preis3 >0.01 ){  	
				// Anzahl Einträge in Tabelle ermitteln
						 // $temp_id_query = dmc_db_query("select count(*) as total from " . TABLE_PRICE3 ." ");
						 $temp_id_query = dmc_db_query("SELECT max(price_id) as total from " . TABLE_PRICE3 ." ");				
						 $TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
							$NEUE_ID = 1;
						 else
							$NEUE_ID = $TEMP_ID['total'] + 1;
						 
						if (DEBUGGER>=1)  fwrite($dateihandle, "NEUE_ID ".$NEUE_ID." von dm_details tmp ".$TEMP_ID['total']."\n");
						// Für (zusätzliche) Preise  (Artikel_Preis1-4)
						$sql_data_PRICE3_array = array(
					        'products_id' => $Artikel_ID,
					        'quantity' => $abMenge,
					        'personal_offer' => $Artikel_Preis3);
							// Wenn Preis-Zuornung existiert (Fehler von XTC beim Artikel loeschen) -> update
								$cmd = "select price_id from " . TABLE_PRICE3 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'";
								$sql_query = dmc_db_query($cmd);
								if ($sql_result = dmc_db_fetch_array($sql_query)) {
									$temp_id=$sql_result['price_id'];
									dmc_sql_update_array(TABLE_PRICE3, $sql_data_PRICE3_array, "price_id = '$temp_id' and quantity='$abMenge'");	
									}
								else { // nicht existent
									$sql_data_PRICE3_array['price_id'] = $NEUE_ID;
									dmc_sql_insert_array(TABLE_PRICE3, $sql_data_PRICE3_array);
									}
				}
/*
				if ( $Artikel_Preis4 >0.01 ){  	
				// Anzahl Einträge in Tabelle ermitteln
						 // $temp_id_query = dmc_db_query("select count(*) as total from " . TABLE_PRICE4 ." ");
						 $temp_id_query = dmc_db_query("SELECT max(price_id) as total from " . TABLE_PRICE4 ." ");				
						 $TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
							$NEUE_ID = 1;
						 else
							$NEUE_ID = $TEMP_ID['total'] + 1;
						 
						if (DEBUGGER>=1)  fwrite($dateihandle, "NEUE_ID ".$NEUE_ID." von dm_details tmp ".$TEMP_ID['total']."\n");
						// Für (zusätzliche) Preise  (Artikel_Preis1-4)
						$sql_data_PRICE4_array = array(
					        'products_id' => $Artikel_ID,
					        'quantity' => $abMenge,
					        'personal_offer' => $Artikel_Preis4);
							// Wenn Preis-Zuornung existiert (Fehler von XTC beim Artikel loeschen) -> update
								$cmd = "select price_id from " . TABLE_PRICE4 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'";
								$sql_query = dmc_db_query($cmd);
								if ($sql_result = dmc_db_fetch_array($sql_query)) {
									$temp_id=$sql_result['price_id'];
									dmc_sql_update_array(TABLE_PRICE4, $sql_data_PRICE4_array, "price_id = '$temp_id' and quantity='$abMenge'");	
									}
								else { // nicht existent
									$sql_data_PRICE4_array['price_id'] = $NEUE_ID;
									dmc_sql_insert_array(TABLE_PRICE4, $sql_data_PRICE4_array);
									}
				}
				
				if ( $Artikel_Preis5 >0.01 ){  	
				// Anzahl Einträge in Tabelle ermitteln
						 // $temp_id_query = dmc_db_query("select count(*) as total from " . TABLE_PRICE5 ." ");
						 $temp_id_query = dmc_db_query("SELECT max(price_id) as total from " . TABLE_PRICE5 ." ");				
						 $TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
							$NEUE_ID = 1;
						 else
							$NEUE_ID = $TEMP_ID['total'] + 1;
						 
						if (DEBUGGER>=1)  fwrite($dateihandle, "NEUE_ID ".$NEUE_ID." von dm_details tmp ".$TEMP_ID['total']."\n");
						// Für (zusätzliche) Preise  (Artikel_Preis1-4)
						$sql_data_PRICE5_array = array(
					        'products_id' => $Artikel_ID,
					        'quantity' => $abMenge,
					        'personal_offer' => $Artikel_Preis5);
							// Wenn Preis-Zuornung existiert (Fehler von XTC beim Artikel loeschen) -> update
								$cmd = "select price_id from " . TABLE_PRICE5 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'";
								$sql_query = dmc_db_query($cmd);
								if ($sql_result = dmc_db_fetch_array($sql_query)) {
									$temp_id=$sql_result['price_id'];
									dmc_sql_update_array(TABLE_PRICE5, $sql_data_PRICE5_array, "price_id = '$temp_id' and quantity='$abMenge'");	
									}
								else { // nicht existent
									$sql_data_PRICE5_array['price_id'] = $NEUE_ID;
									dmc_sql_insert_array(TABLE_PRICE5, $sql_data_PRICE5_array);
									}
				}
				*/
			  if (DEBUGGER>=1) fwrite($dateihandle, "Detail Zuordnung eingetragen.\n");
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Detail nicht eingetragen, da  Artikel nicht existent.\n");
			} //  endif Wenn Artikel existieren
		} // end exportmodus selectline_kalkulat
					  
		// Exportmodus rabattpreise
		if ($ExportModusSpecial=='rabattpreise') {
			// Zur Übergabe:
			// ARTNR  ABMENGE  pr01  pr02  pr03  pr04  pr05  RAB01  RAB02  RAB03  RAB04  RAB05 
			$Artikel_Artikelnr = $Freifeld{2};
			$abMenge = $Freifeld{3};
			$Artikel_Preis1 = $Freifeld{4};
			$Artikel_Preis2 = $Freifeld{5};
			$Artikel_Preis3 = $Freifeld{6};
			$Artikel_Preis4 = $Freifeld{7};
			$Artikel_Preis5 = $Freifeld{8};
			$Rabatt1 = $Freifeld{9};
			$Rabatt2 = $Freifeld{10};
			$Rabatt3 = $Freifeld{11};
			$Rabatt4 = $Freifeld{12};
			// $Rabatt_Preis5 = $Freifeld{13};
	
			// Preisberechnung durchführen
			// Rabatt von Preis abziehen
			if ($Rabatt1 != '' and $Rabatt1 > 0 and $abMenge==1)  {
				if (DEBUGGER>=1)  fwrite($dateihandle, "Artikel_Preis1 alt = ".$Artikel_Preis1."\n");
				if (DEBUGGER>=1)  fwrite($dateihandle, "Rabatt1  = ".$Rabatt1."\n");
				$Artikel_Preis1 = $Artikel_Preis1 - ($Artikel_Preis1*$Rabatt1/100);
				if (DEBUGGER>=1)  fwrite($dateihandle, "Artikel_Preis1  neu = ".$Artikel_Preis1."\n");
						
				}
				
			// Keine Rabattierung für Staffelpreise
			if ($Rabatt2 != '' and $Rabatt2 > 0 and $abMenge==1) $Artikel_Preis2 = $Artikel_Preis2 - ($Artikel_Preis2*$Rabatt2/100);
			if ($Rabatt3 != '' and $Rabatt3 > 0 and $abMenge==1) $Artikel_Preis3 = $Artikel_Preis3 - ($Artikel_Preis3*$Rabatt3/100);
			
			// Der Artikelnummer zugehörige products_id und xsell_id ermitteln
			// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
			$cmd = "select products_id from " . TABLE_PRODUCTS .
		            " where products_model = '$Artikel_Artikelnr'";
					
					if (DEBUGGER>=1)  fwrite($dateihandle, "details sql = ".$cmd."\n");
						
					
			$sql_query = dmc_db_query($cmd);
		     	 
		    if ($sql_result = dmc_db_fetch_array($sql_query))
		    {      
				// ArtikelID von bestehendem Artikel ermitteln
				$Artikel_ID = $sql_result['products_id'];
				// Existiert
				$exists = 1;
			} //endif
			
			
		if ($exists ==1) {	
			
			// Bei Abmenge = 1 -> Standardpreis setzen
			if ($abMenge == 1) {
				//Update durchführen
	     		// $sql_data_array = array(	'products_price' => $Artikel_Preis1
				//							);	
				//dmc_sql_update_array(TABLE_PRODUCTS, $sql_data_array, "products_id = '$Artikel_ID'");
				// if (DEBUGGER>=1) fwrite($dateihandle, "Standardpreis Update durchgeführt.\n");
			}
			
			if ( $Artikel_Preis1 >0.01 ){  	
				// Anzahl Einträge in Tabelle ermitteln
						 // $temp_id_query = dmc_db_query("select count(*) as total from " . TABLE_PRICE1 ." ");
						 $temp_id_query = dmc_db_query("SELECT max(price_id) as total from " . TABLE_PRICE1 ." ");				
						 $TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
							$NEUE_ID = 1;
						 else
							$NEUE_ID = $TEMP_ID['total'] + 1;
						 
						if (DEBUGGER>=1)  fwrite($dateihandle, "NEUE_ID ".$NEUE_ID." von dm_details tmp ".$TEMP_ID['total']."\n");
						// Für (zusätzliche) Preise  (Artikel_Preis1-4)
						$sql_data_price1_array = array(
					        'products_id' => $Artikel_ID,
					        'quantity' => $abMenge,
					        'personal_offer' => $Artikel_Preis1);
							// Wenn Preis-Zuornung existiert (Fehler von XTC beim Artikel loeschen) -> update
								$cmd = "select price_id from " . TABLE_PRICE1 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'";
								$sql_query = dmc_db_query($cmd);
								if ($sql_result = dmc_db_fetch_array($sql_query)) {
									/* $temp_id=$sql_result['price_id'];
									dmc_sql_update_array(TABLE_PRICE1, $sql_data_price1_array, "price_id = '$temp_id' and quantity='$abMenge'");	*/
									// ALte Preise zunächst löschen
									 dmc_db_query("delete from " . TABLE_PRICE1 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'");

										if (DEBUGGER>=1)  fwrite($dateihandle, "update sql1 = Nummer $Artikel_ID ID = '$temp_id' and quantity='$abMenge' mit preis $Artikel_Preis1");
									}
								 // neuen preis setzen
									$sql_data_price1_array['price_id'] = $NEUE_ID;
									dmc_sql_insert_array(TABLE_PRICE1, $sql_data_price1_array);
									if (DEBUGGER>=1)  fwrite($dateihandle, "insert sql1 = Nummer $Artikel_ID and quantity='$abMenge' mit preis $Artikel_Preis1");
								
				}
				if ( $Artikel_Preis2 >0.01 and $abMenge==1){  	
				// Anzahl Einträge in Tabelle ermitteln
						 // $temp_id_query = dmc_db_query("select count(*) as total from " . TABLE_PRICE2 ." ");
						 $temp_id_query = dmc_db_query("SELECT max(price_id) as total from " . TABLE_PRICE2 ." ");				
						 $TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
							$NEUE_ID = 1;
						 else
							$NEUE_ID = $TEMP_ID['total'] + 1;
						 
						if (DEBUGGER>=1)  fwrite($dateihandle, "NEUE_ID ".$NEUE_ID." von dm_details tmp ".$TEMP_ID['total']."\n");
						// Für (zusätzliche) Preise  (Artikel_Preis1-4)
						$sql_data_PRICE2_array = array(
					        'products_id' => $Artikel_ID,
					        'quantity' => $abMenge,
					        'personal_offer' => $Artikel_Preis2);
							// Wenn Preis-Zuornung existiert (Fehler von XTC beim Artikel loeschen) -> update
								$cmd = "select price_id from " . TABLE_PRICE2 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'";
								$sql_query = dmc_db_query($cmd);
								
								if ($sql_result = dmc_db_fetch_array($sql_query)) {
									/* $temp_id=$sql_result['price_id'];
									dmc_sql_update_array(TABLE_PRICE1, $sql_data_price1_array, "price_id = '$temp_id' and quantity='$abMenge'");	*/
									// ALte Preise zunächst löschen
									 dmc_db_query("delete from " . TABLE_PRICE2 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'");

										if (DEBUGGER>=1)  fwrite($dateihandle, "update sql2 = Nummer $Artikel_ID ID = '$temp_id' and quantity='$abMenge' mit preis $Artikel_Preis1");
									}
								 // neuen preis setzen
									$sql_data_PRICE2_array['price_id'] = $NEUE_ID;
									dmc_sql_insert_array(TABLE_PRICE2, $sql_data_PRICE2_array);
									if (DEBUGGER>=1)  fwrite($dateihandle, "insert sql2 = Nummer $Artikel_ID and quantity='$abMenge' mit preis $Artikel_Preis1");
								
				}
				 
				if ( $Artikel_Preis3 >0.01 and $abMenge==1){  	
				// Anzahl Einträge in Tabelle ermitteln
						 // $temp_id_query = dmc_db_query("select count(*) as total from " . TABLE_PRICE3 ." ");
						 $temp_id_query = dmc_db_query("SELECT max(price_id) as total from " . TABLE_PRICE3 ." ");				
						 $TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
							$NEUE_ID = 1;
						 else
							$NEUE_ID = $TEMP_ID['total'] + 1;
						 
						if (DEBUGGER>=1)  fwrite($dateihandle, "NEUE_ID ".$NEUE_ID." (Preis3) von dm_details tmp ".$TEMP_ID['total']."\n");
						// Für (zusätzliche) Preise  (Artikel_Preis1-4)
						$sql_data_PRICE3_array = array(
					        'products_id' => $Artikel_ID,
					        'quantity' => $abMenge,
					        'personal_offer' => $Artikel_Preis3);
							// Wenn Preis-Zuornung existiert (Fehler von XTC beim Artikel loeschen) -> update
								$cmd = "select price_id from " . TABLE_PRICE3 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'";
								$sql_query = dmc_db_query($cmd);
								
								if ($sql_result = dmc_db_fetch_array($sql_query)) {
									/* $temp_id=$sql_result['price_id'];
									dmc_sql_update_array(TABLE_PRICE1, $sql_data_price1_array, "price_id = '$temp_id' and quantity='$abMenge'");	*/
									// ALte Preise zunächst löschen
									 dmc_db_query("delete from " . TABLE_PRICE3 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'");

										if (DEBUGGER>=1)  fwrite($dateihandle, "update sql3 = Nummer $Artikel_ID ID = '$temp_id' and quantity='$abMenge' mit preis $Artikel_Preis1");
									}
								 // neuen preis setzen
									$sql_data_PRICE3_array['price_id'] = $NEUE_ID;
									dmc_sql_insert_array(TABLE_PRICE3, $sql_data_PRICE3_array);
									if (DEBUGGER>=1)  fwrite($dateihandle, "insert sql3 = Nummer $Artikel_ID and quantity='$abMenge' mit preis $Artikel_Preis1");
								
				}
/*
				if ( $Artikel_Preis4 >0.01 ){  	
				// Anzahl Einträge in Tabelle ermitteln
						 // $temp_id_query = dmc_db_query("select count(*) as total from " . TABLE_PRICE4 ." ");
						 $temp_id_query = dmc_db_query("SELECT max(price_id) as total from " . TABLE_PRICE4 ." ");				
						 $TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
							$NEUE_ID = 1;
						 else
							$NEUE_ID = $TEMP_ID['total'] + 1;
						 
						if (DEBUGGER>=1)  fwrite($dateihandle, "NEUE_ID ".$NEUE_ID." von dm_details tmp ".$TEMP_ID['total']."\n");
						// Für (zusätzliche) Preise  (Artikel_Preis1-4)
						$sql_data_PRICE4_array = array(
					        'products_id' => $Artikel_ID,
					        'quantity' => $abMenge,
					        'personal_offer' => $Artikel_Preis4);
							// Wenn Preis-Zuornung existiert (Fehler von XTC beim Artikel loeschen) -> update
								$cmd = "select price_id from " . TABLE_PRICE4 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'";
								$sql_query = dmc_db_query($cmd);
								if ($sql_result = dmc_db_fetch_array($sql_query)) {
									// $temp_id=$sql_result['price_id'];
									// dmc_sql_update_array(TABLE_PRICE1, $sql_data_price1_array, "price_id = '$temp_id' and quantity='$abMenge'");	
									// ALte Preise zunächst löschen
									 dmc_db_query("delete from " . TABLE_PRICE4 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'");

										if (DEBUGGER>=1)  fwrite($dateihandle, "update sql4 = Nummer $Artikel_ID ID = '$temp_id' and quantity='$abMenge' mit preis $Artikel_Preis1");
									}
								 // neuen preis setzen
									$sql_data_PRICE4_array['price_id'] = $NEUE_ID;
									dmc_sql_insert_array(TABLE_PRICE4, $sql_data_PRICE4_array);
									if (DEBUGGER>=1)  fwrite($dateihandle, "insert sql4 = Nummer $Artikel_ID and quantity='$abMenge' mit preis $Artikel_Preis1");
								
				}
				
				if ( $Artikel_Preis5 >0.01 ){  	
				// Anzahl Einträge in Tabelle ermitteln
						 // $temp_id_query = dmc_db_query("select count(*) as total from " . TABLE_PRICE5 ." ");
						 $temp_id_query = dmc_db_query("SELECT max(price_id) as total from " . TABLE_PRICE5 ." ");				
						 $TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
						if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
							$NEUE_ID = 1;
						 else
							$NEUE_ID = $TEMP_ID['total'] + 1;
						 
						if (DEBUGGER>=1)  fwrite($dateihandle, "NEUE_ID ".$NEUE_ID." von dm_details tmp ".$TEMP_ID['total']."\n");
						// Für (zusätzliche) Preise  (Artikel_Preis1-4)
						$sql_data_PRICE5_array = array(
					        'products_id' => $Artikel_ID,
					        'quantity' => $abMenge,
					        'personal_offer' => $Artikel_Preis5);
							// Wenn Preis-Zuornung existiert (Fehler von XTC beim Artikel loeschen) -> update
								$cmd = "select price_id from " . TABLE_PRICE5 .
										" where products_id = '$Artikel_ID' and quantity='$abMenge'";
								$sql_query = dmc_db_query($cmd);
								if ($sql_result = dmc_db_fetch_array($sql_query)) {
									$temp_id=$sql_result['price_id'];
									dmc_sql_insert_array(TABLE_PRICE5, $sql_data_PRICE5_array, "price_id = '$temp_id' and quantity='$abMenge'");	
									}
								else { // nicht existent
									$sql_data_PRICE5_array['price_id'] = $NEUE_ID;
									dmc_sql_insert_array(TABLE_PRICE5, $sql_data_PRICE5_array);
									}
				}
				*/
			  if (DEBUGGER>=1) fwrite($dateihandle, "Detail Zuordnung RABATTPREIS eingetragen.\n");
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Detail RABATTPREIS nicht eingetragen, da  Artikel nicht existent.\n");
			} //  endif Wenn Artikel existieren
		} // end exportmodus rabattliste
		
		// Exportmodus products_keywords
		if ($ExportModusSpecial=='products_keywords') {
			$Artikel_Artikelnr = $Freifeld{2};
			
						$products_keywords = html_entity_decode (sonderzeichen2html(true,$Freifeld{3}), ENT_NOQUOTES);
		
			// Der Artikelnummer zugehörige products_id und xsell_id ermitteln
			// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
			$cmd = "select products_id from " . TABLE_PRODUCTS .
		            " where products_model = '$Artikel_Artikelnr'";
					
			$sql_query = dmc_db_query($cmd);
		     	 
		    if ($sql_result = dmc_db_fetch_array($sql_query))
		    {      
				// ArtikelID von bestehendem Artikel ermitteln
				$Artikel_ID = $sql_result['products_id'];
				// Existiert
				$exists = 1;
			} //endif
			
			// Wenn Artikel existiert, Details zuordnen 
			if ($exists == 1) {
				// Details eintragen : 
				// Freifeld1 (Art) = pdfs, Freifeld2 = Artikelnummer, Freifeld3 = Upload Beschreibung1, Freifeld4 = Upload1, Freifeld5 = Upload Beschreibung2, Freifeld6 = Upload2, Freifeld7 = Upload Beschreibung3, Freifeld8 = Upload3
				$sql_data_array = array(	'products_keywords' => $products_keywords
											);	
			
				dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, "products_id='$Artikel_ID'");

			  if (DEBUGGER>=1) fwrite($dateihandle, "Detail Zuordnung products_keywords eingetragen.\n");
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Detail nicht eingetragen, da  Artikel nicht existent.\n");
			} //  endif Wenn Artikel existieren
		} // end exportmodus products_keywords
		
		
		// ****************************************************************************
		// Ändert den Auftragsstatus
		// ****************************************************************************
		if ($ExportModusSpecial=='order_update') 
		{
				$daten = "--- OrderUpdate ---";
				fwrite($dateihandle, $daten. "\n");
				/* select 'order_update' as Freifeld1,	angebot.IhrAuftrag AS Order_ID,	'3' as Status, paket.Paketnummer as Trackingnummer, '' as Versender, '' as Rechnungsnummer, Lieferschein.Belegnummer AS Lieferscheinnummer, '' as Rechnung_dok, '' as Lieferschein_dok, '' as Bemerkung, '' as Freifeld11, '' as Freifeld12 FROM BELEG AS angebot INNER JOIN BELEG AS Lieferschein ON angebot.Belegnummer = Lieferschein.LieferBelegNr INNER JOIN PAKET AS paket ON Lieferschein.Belegnummer = paket.Belegnummer WHERE (angebot.Belegtyp = 'A') AND (Lieferschein.Belegtyp = 'L') AND (angebot.IhrAuftrag <> '') AND (Lieferschein.BearbeitetAm > GETDATE() - 1)
				<!--  Abgleich nach Rechnung. (Lieferschein wurde zur Rechnung fortgefuehrt.)  Shopware Status Komplett abgeschlossen und Teilweise abgeschlossen -->
				select 'order_update' as Freifeld1, rechnung.AUFBEZ AS Order_ID, CASE WHEN rechnung.endprn=liefer.endprn THEN 'Komplett abgeschlossen' ELSE 'Teilweise abgeschlossen' END  as Status, '' as Trackingnummer, '' as Versender, '' as Rechnungsnummer, '' AS Lieferscheinnummer, '' as Rechnung_dok, '' as Lieferschein_dok, CASE WHEN rechnung.endprn=liefer.endprn THEN 'Komplettlieferung' ELSE 'Teillieferung' END as Bemerkung, '' as Verwende_Magento_Lieferschein, '' as Freifeld12 FROM mand3.sg_auf_fschrift AS rechnung INNER JOIN mand3.sg_auf_fschrift AS liefer ON rechnung.ALTAUFNR=liefer.AUFNR WHERE rechnung.AUSSHOP=1 AND rechnung.AUFNR like 'RE%' AND liefer.AUFNR like 'LI%' AND liefer.Fortgefuehrt=1 AND rechnung.GEDRUCKT=1 AND ((now()-date_add(liefer.DATUM ,interval 24 hour))<0)
				<!-- Ablgeich nach Lieferschein. (Auftrag wurde zu Lieferschein fortgefuehrt.) -->
				sselect 'order_update' as Freifeld1, auftrag.OBESTID AS Order_ID, 'processing' as Status, '' as Trackingnummer, '' as Versender, '' as Rechnungsnummer, '' AS Lieferscheinnummer, '' as Rechnung_dok, '' as Lieferschein_dok, CASE WHEN auftrag.endprn=liefer.endprn THEN 'Komplettlieferung' ELSE 'Teillieferung' END as Bemerkung, '' as Verwende_Magento_Lieferschein, '' as Freifeld12 FROM mand3.sg_auf_fschrift AS auftrag INNER JOIN mand3.sg_auf_fschrift AS liefer ON auftrag.AUFNR=liefer.ALTAUFNR WHERE auftrag.AUSSHOP=1 AND auftrag.AUFNR like 'AU%' AND liefer.AUFNR like 'LI%' AND auftrag.Fortgefuehrt=1 AND liefer.GEDRUCKT=1 AND ((now()-date_add(liefer.DATUM ,interval 24 hour))<0)
				*/
				$Order_ID = str_replace("Online-Bestellung ", "", $Freifeld{2});
				$Order_ID = str_replace("Shop-No ", "", $Order_ID);
				$Status = $Freifeld{3};
				$Trackingnummer = str_replace("#","",$Freifeld{4});
				$Versender = $Freifeld{5};
				$Rechnungsnummer = $Freifeld{6};
				$Lieferscheinnummer = $Freifeld{7};
				$Rechnung_dok = $Freifeld{8};
				$Lieferschein_dok = $Freifeld{9};
				$Bemerkung = $Freifeld{10};			 // Neu, wenn MailArt = alternativmail, dann diesen Text nicht mit Smarty sondern dmc_send_mail versenden
				$MailArt = $Freifeld{11};			 // Neu, wenn = alternativmail, dann nicht mit smarty sondern dmcsendmail
				// ggfls Standardtext bei alternativmail
				if ($MailArt =='alternativmail' && $Bemerkung == '')
					$Bemerkung= '<font face="Verdana, Arial, Helvetica, sans-serif" size="2"> <strong>Sehr geehrte(r) $NAME, </strong><br /><br /> Der Status Ihrer Bestellung vom $ORDER_DATE mit der Bestellnummer $ORDER_NR wurde ge&auml;ndert.<br />Neuer Status:  <strong>$ORDER_STATUS</strong> <br /> Bei Fragen zu Ihrer Bestellung antworten Sie bitte auf diese E-Mail. </font>';
				// Variablendeklarationen
				$LangID = 2;	// 2= deutsch
				// Text entwickeln
				$comments = '';
				if ($Rechnungsnummer != '')  $comments .= 'Rechnung erstellt mit Rechnungnummer: '.$Rechnungsnummer.'<br>';
				if ($Lieferscheinnummer != '')  $comments .= 'Lieferscheinnummer: '.$Lieferscheinnummer.'<br>';
				if ($Trackingnummer != '')  $comments .= 'Ware versendet mit Paketscheinnummer: '.$Trackingnummer.'<br>';
				if ($Trackingnummer != '')  $comments .= 'Sendungsverfolgung: <a href="http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc='.$Trackingnummer.'">>hier klicken<</a><br>';
				if ($Trackingnummer != '' && $Versender != '')  $comments .= 'Paketdienstleister: '.$Versender.'<br>';

				if (DEBUGGER>=1) fwrite($dateihandle, "order_update Order_ID=".$Order_ID." auf Status=".$Status." mit Bemerkung: <br>".$comments."\n");
	
	
	
				if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) {
					
					if ($Status=="Bestellung ist zur Lieferung bereit")
						$Status="Zur Lieferung bereit";
					else if ($Status=="Bestellung ist komplett ausgeliefert")
						$Status="Komplett abgeschlossen";
					
					$orderStatusId = dmc_sql_select_query("id","s_core_states","description='".$Status."'");
					$sw_order_id= dmc_sql_select_query("id","s_order","ordernumber='".$Order_ID."'"); 
					$swversion=$client->get('version');
					fwrite($dateihandle, "UPDATE_ORDER_STATUS shopware $swversion mit order_id=$sw_order_id and Status_id= $orderStatusId ... ");
					$date = new DateTime();
					$date = $date->format(DateTime::ISO8601);
					
					$sw_temp_order_id= dmc_sql_select_query("id","s_order","ordernumber='".$Order_ID."' AND status=".$orderStatusId); 
					
					if ($sw_temp_order_id=="") {
						// Order hat noch einen anderen Status
						if ($orderStatusId!="" && $sw_order_id !="") {
							$result=$client->put('orders/'.$sw_order_id,  array(
								// 'paymentStatusId' => 10,
								'orderStatusId' => $orderStatusId,
								'trackingCode' => $Trackingnummer,
								'comment' => $comments,
								// 'transactionId' => '0',
								'clearedDate' => $date
							));
							$ergebnis = print_r($result,true);
							fwrite($dateihandle, "durchgefuehrt: $ergebnis \n");
						} else {
							fwrite($dateihandle, "NICHT durchgefuehrt, orderStatusId ($orderStatusId) oder sw_order_id ($sw_order_id) nicht ermittelt  \n");
						}
					} else {
						fwrite($dateihandle, "NICHT durchgefuehrt, sw_order_id ($sw_order_id) HAT BEREITS orderStatusId ($orderStatusId).  \n");
					}
					// ALT: Update auf Datenbank
					/* $sql=	"UPDATE " . DB_TABLE_PREFIX . "s_order ".
								"SET status= ".
								 "(SELECT id FROM ".DB_TABLE_PREFIX ."s_core_states WHERE description='".$Status."'), ".
								 "trackingcode='".$Trackingnummer."' ".
								" WHERE ordernumber=".$Order_ID;
					fwrite($dateihandle, "UPDATE_ORDER_STATUS shopware $sql\n");	
					dmc_db_query($sql);	*/
				} else if (strpos(strtolower(SHOPSYSTEM), 'woocommerce') !== false) {
					// WP Modul https://docs.woothemes.com/document/shipment-tracking/
   					// Beispiel a:1:{i:0;a:6:{s:17:"tracking_provider";s:17:"deutsche-post-dhl";s:24:"custom_tracking_provider";s:0:"";s:20:"custom_tracking_link";s:0:"";s:15:"tracking_number";s:10:"1234567890";s:12:"date_shipped";s:10:"1459987200";s:11:"tracking_id";s:32:"e91bbe350600cdb7b04fba9766a651a6";}}
					$tracking_provider = "deutsche-post-dhl";
    				$tracking_id = md5( $tracking_provider."-".$Trackingnummer . microtime() );
    				$date = date_create();
    				$date_shipped = date_timestamp_get($date);		// date( 'Y-m-d', $date_shipped )
					$meta_value='a:1:{i:0;a:6:{'.
								's:17:"tracking_provider";s:17:"deutsche-post-dhl";'.
								's:24:"custom_tracking_provider";s:0:"";'.
								's:20:"custom_tracking_link";s:0:"";'.
								's:15:"tracking_number";s:'.strlen($Trackingnummer).':"'.$Trackingnummer.'";'.
								's:12:"date_shipped";s:10:"'.$date_shipped.'";'.
								's:11:"tracking_id";s:32:"'.$tracking_id.'";'.
								'}}';
					// Tracking Informationen ermitteln 
					$cmd = "SELECT meta_value ".
							"FROM ".DB_TABLE_PREFIX . "postmeta " .
							"WHERE meta_key='_wc_shipment_tracking_items' AND post_id = '" . $Order_ID . "' ";
					if (DEBUGGER>=1) fwrite($dateihandle, "Check id Tracking exists sql=".$cmd."\n");
					$Order_Query = dmc_db_query($cmd);
					// Tracking fuer Bestellung noch nicht vorhanden.						
					if ($Order = dmc_db_fetch_array($Order_Query))
					{
					  	// Tracking Informationen schon vorhanden
					  
					} else {
						// Tracking Informationen schreiben
					  	$sql=	"INSERT INTO " . DB_TABLE_PREFIX . "postmeta (post_id, meta_key, meta_value)".
								"VALUES (  ".$Order_ID.", '_wc_shipment_tracking_items', '".$meta_value."')";
						fwrite($dateihandle, "TRACKING INFO woocommerce $sql\n");	
						dmc_db_query($sql);	
					  // Neuen Status setzen, wenn Status ungleich dem uebergebenen ist
						/*  if ($Order['orders_status'] != $Status)
					  {
					  }*/
					}
					// Update Order Status
					// $Status='wc-completed'; // processing, on-hold, cancelled, completed
					// include WordPress' wp-load
					include_once ("../wp-load.php");
					// include_once (  '../wp-content/plugins/woocommerce/includes/wc-core-functions.php ');
					// include_once (  '../wp-content/plugins/woocommerce/includes/abstracts/abstract-wc-order.php' ); 
					$order = new WC_Order($Order_ID);
					// var_dump($order);
					if (!empty($order)) {
						// processing, on-hold, cancelled, completed
						$order->update_status($Status);
						fwrite($dateihandle, "UPDATE_ORDER_STATUS auf woo api erfolgt\n");	
					} else {
						// wenn $order leer, dann ist etwas mit der Einbindung nicht korrekt oder die Order nicht vorhanden
						// Update auf DB kann nicht schaden
						$sql=	"UPDATE " . DB_TABLE_PREFIX . "posts ".
								"SET post_status='".$Status."'".
								" WHERE post_type = 'shop_order' AND id=".$Order_ID;
						fwrite($dateihandle, "UPDATE_ORDER_STATUS woo db $sql\n");	
						dmc_db_query($sql);	
					}
					if ($Status=='wc-completed') {
						// the email we want to send
						//$email_class = 'WC_Email_Customer_Processing_Order';
						$email_class = 'WC_Email_Customer_Completed_Order';

						// load the WooCommerce Emails
						$wc_emails = new WC_Emails();
						$emails = $wc_emails->get_emails();

						// select the email we want & trigger it to send
						$new_email = $emails[$email_class];
						$new_email->trigger($order_id);
		
						// show the email content
						//echo $new_email->get_content();
					}
			
					// Alternativ auf REST-API
					// Update Order Status
					/*	1. WP Admin:  WP Dashboard -> WooCommerce -> Settings
						2. API tab and make sure the Enable the REST API is checked.
						3. Click on the Keys/Apps tab then the Add Key button.
						4. Enter the description then set the Permission to Read/Write then submit the form. Please note the generated Consumer Key and Consumer Secret for the next step.
						5. In the dmc_set_details.php please set the Store URL and paste in the Consumer Key and Consumer Secret from the API settings page.
						6. Extract the WC-REST-API.zip archive in the same directory as the dmc_set_details.php file so you'll have a lib directory alongside the dms_set_details.php file.
						7. Test the API. Try to run the script to make sure that orders are getting their statuses updated via the API.
						*/
					/*
					$storeUrl = 'http://www.meinshop.de';
					$consumerKey	= 'ck_xxxxxxxxxxxx';
					$consumerSecret	= 'cs_xxxxxxxxxxxx';
					require_once( 'lib/woocommerce-api.php' );

					$options = array(
					    'ssl_verify' => false,
					);

					try {
					    $client = new WC_API_Client( $storeUrl, $consumerKey, $consumerSecret, $options );
					    $client->orders->update_status( $Order_ID, 'completed' );
					} catch ( WC_API_Client_Exception $e ) {
					    die('Could not connect to the API. '. $e->getMessage());
					}
					*/
				} else {
					$orders_status_array = array();
					$cmd = "select orders_status_id, orders_status_name from " .
					TABLE_ORDERS_STATUS . " where language_id = '" . (int)$LangID . "'";
				 
					// fwrite($dateihandle, "Query 1060=$cmd\n");
				
					$orders_status_query = dmc_db_query($cmd);
					while ($orders_status = dmc_db_fetch_array($orders_status_query))
					{
						$orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
						//	fwrite($dateihandle, "Result 1060= ID=".$orders_status['orders_status_id']." Name=".$orders_status['orders_status_name']."\n");
					}

					if ($Order_ID != 0 && isset($orders_status_array[$Status]))
					{
						// Letzetn Status der Bestellung ermitteln 
						$cmd = "SELECT o.customers_name, o.customers_email_address, o.orders_status, o.date_purchased, oh.orders_status_id ".
							"FROM orders o, orders_status_history oh ".
							"WHERE o.orders_id=o.orders_id AND o.orders_id = '" . $Order_ID . "' ".
							"AND oh.date_added=(SELECT MAX(date_added) FROM orders_status_history WHERE orders_id = '" . $Order_ID . "')";
						if (DEBUGGER>=1) fwrite($dateihandle, "order_update sql=".$cmd."\n");

						$Order_Query = dmc_db_query($cmd);
						// Bestellung OHN den übergebenen Status noch nicht vorhanden.
						if ($Order = dmc_db_fetch_array($Order_Query))
						{
						  // Neuen Status setzen, wenn Status ungleich dem uebergebenen ist
						  if ($Order['orders_status'] != $Status)
						  {
								// Aktuellen Order Status aendern
								$update_sql_data = array(
									'orders_status' => $Status,
									'last_modified' => 'now()');
								dmc_sql_update_array('orders', $update_sql_data, "orders_id='" . $Order_ID . "'");
								if (DEBUGGER>=1) fwrite($dateihandle, "do update - $MailArt \n");
								
								// Kundeninformation per eMail senden
								if (ORDER_STATUS_NOTIFY_CUSTOMER && $MailArt == '') {
									// require functionblock for mails
									require_once(DIR_WS_CLASSES.'class.phpmailer.php');
									require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');
									require_once(DIR_FS_INC . 'xtc_add_tax.inc.php');
									require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
									require_once(DIR_FS_INC . 'changedataout.inc.php');
									require_once(DIR_FS_INC . 'xtc_href_link.inc.php');
									require_once(DIR_FS_INC . 'xtc_date_long.inc.php');
									require_once(DIR_FS_INC . 'xtc_check_agent.inc.php');
									require_once('../includes/application_top_export.php');
									$smarty = new Smarty;

									$smarty->assign('language', $Order['language']);
									$smarty->caching = false;
									$smarty->template_dir=DIR_FS_CATALOG.'templates';
									$smarty->compile_dir=DIR_FS_CATALOG.'templates_c';
									$smarty->config_dir=DIR_FS_CATALOG.'lang';
									$smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
									$smarty->assign('logo_path',HTTP_SERVER  . DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');
									$smarty->assign('NAME',$Order['customers_name']);
									$smarty->assign('ORDER_NR',$Order_ID);
									$smarty->assign('ORDER_LINK',xtc_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $Order_ID, 'SSL'));
									$smarty->assign('ORDER_DATE',xtc_date_long($Order['date_purchased']));
									$smarty->assign('NOTIFY_COMMENTS', '');
									$smarty->assign('ORDER_STATUS', $orders_status_array[$Status]);

									$html_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/mail/'.$Order['language'].'/change_order_mail.html');
									$txt_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/mail/'.$Order['language'].'/change_order_mail.txt');

									// send mail with html/txt template
									xtc_php_mail(EMAIL_BILLING_ADDRESS,
												 EMAIL_BILLING_NAME ,
												 $Order['customers_email_address'],
												 $Order['customers_name'],
												 '',
												 EMAIL_BILLING_REPLY_ADDRESS,
												 EMAIL_BILLING_REPLY_ADDRESS_NAME,
												 '',
												 '',
												 EMAIL_BILLING_SUBJECT,
												 $html_mail ,
												 $txt_mail);
									$notified=1;
								} else if (ORDER_STATUS_NOTIFY_CUSTOMER && $MailArt == 'alternativmail') {
									// $Bemerkung = $Freifeld{10};			 // Neu, wenn MailArt = alternativmail, dann diesen Text amit sondern dmc_send_mail versenden
									// Text aufbauen
								//		if (DEBUGGER>=1) fwrite($dateihandle, "1 \n");
									
									$Bemerkung = str_replace('$NAME',$Order['customers_name'],$Bemerkung);
									$Bemerkung = str_replace('$ORDER_NR',$Order_ID,$Bemerkung);
									$Bemerkung = str_replace('$ORDER_DATE',$Order['date_purchased'],$Bemerkung);
									$Bemerkung = str_replace('$ORDER_STATUS',$orders_status_array[$Status],$Bemerkung);
									//		if (DEBUGGER>=1) fwrite($dateihandle, "2 $Bemerkung \n");
									
									// send mail with html
									$empfaenger=$Order['customers_email_address'];
									$von_email=EMAIL_BILLING_ADDRESS;
									$von_name=EMAIL_BILLING_NAME;
									$betreff = 'Neuer Status Bestellung '.$Order_ID.' vom '.$Order['date_purchased'];
								//			if (DEBUGGER>=1) fwrite($dateihandle, "3 	dmc_send_email($empfaenger,$von_email,$von_name,$betreff,$Bemerkung) \n");
									
									dmc_send_email($empfaenger,$von_email,$von_name,$betreff,$Bemerkung);
								//			if (DEBUGGER>=1) fwrite($dateihandle, "4 \n");
									
									$notified=1;
								} else {
									$notified=0;
								} // endif Kunden Info Mail senden

								$insert_sql_data = array(
								  'orders_id' => $Order_ID,
								  'orders_status_id' => $Status,
								  'date_added' => 'now()',
								  'customer_notified' => $notified,
								  'comments' => '');
								dmc_sql_insert_array(TABLE_ORDERS_STATUS_HISTORY, $insert_sql_data);
							  }
							}
					}
				}
				
				echo '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
			       "<STATUS>\n" .
			       "  <STATUS_INFO>\n" .
			       "    <ACTION>$action</ACTION>\n" .
			       "    <CODE>0</CODE>\n" .
			       "    <MESSAGE>OK</MESSAGE>\n" .
			       "    <ORDER_ID>$Order_ID</ORDER_ID>\n" .
			       "    <ORDER_STATUS>$Status</ORDER_STATUS>\n" .
			       "    <SCRIPT_VERSION_MAJOR>Für $version_major</SCRIPT_VERSION_MAJOR>\n" .
			       "    <SCRIPT_VERSION_MINOR>$version_minor</SCRIPT_VERSION_MINOR>\n" .
			       "  </STATUS_INFO>\n" .
			       "</STATUS>\n\n";
		}	// end if order_update

		// Exportmodus gambio_attributes
		if ($ExportModusSpecial=='gambio_attributes') {
			// select 'gambio_attributes' as ExportModus, item.NUMBER_ELEMENT_COUNT_0  as Artikelnummer, attr.ID_ELEMENT_COUNT_0 as Merkmal, attr.VALUE_ELEMENT_COUNT_0 as Auspraegung, '' as Freifeld5,'' as Freifeld6,'' as Freifeld7,'' as Freifeld8,'' as Freifeld9,'' as Freifeld10,'' as Freifeld11,'' as Freifeld12 FROM ARTICLESYSTEM_AS_CHILD_IN_ITEMDATA p, ITEM_AS_CHILD_IN_ITEMDATA item, ATTRIBUTE_AS_CHILD_IN_ITEMDATA as attr WHERE p.ROW_ID_XX = item.ROW_ID_XX AND p.ROW_ID_XX = attr.PARENT_ELEMENT_ROW_ID_XX ORDER BY item.NUMBER_ELEMENT_COUNT_0

			$Artikel_Artikelnr = $Freifeld{2};
			$Artikel_Merkmal = html_entity_decode (sonderzeichen2html(true,$Freifeld{3}), ENT_NOQUOTES);
			$Artikel_Auspraegung = html_entity_decode (sonderzeichen2html(true,$Freifeld{4}), ENT_NOQUOTES);
		
			// Der Artikelnummer zugehörige products_id und xsell_id ermitteln
			// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
			$cmd = "select products_id from " . TABLE_PRODUCTS .
		           " where products_model = '$Artikel_Artikelnr'";
					
			$sql_query = dmc_db_query($cmd);
		     	 
		    if ($sql_result = dmc_db_fetch_array($sql_query))
		    {      
				// ArtikelID von bestehendem Artikel ermitteln
				$Artikel_ID = $sql_result['products_id'];
				// Existiert
				$exists = 1;
			} //endif
			
			// Wenn Artikel existiert, Details zuordnen 
			if ($exists == 1) {
				// Details eintragen, nachdem Mapping durchgeführt wurde
				// Attribute Einheit -> Update auf products
				if ($Artikel_Merkmal == 'Einheit') {
					$sql_data_array = array(	'products_vpe' => $Artikel_Auspraegung );
					dmc_sql_update_array(TABLE_PRODUCTS, $sql_data_array, "products_id='$Artikel_ID'");
				} else if ($Artikel_Merkmal == 'Artikel Name') {
					// Attribute Artikel Name -> Update auf products_description
					$sql_data_array = array(	'products_name' => $Artikel_Auspraegung );
					dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, "products_id='$Artikel_ID'");
				} else if ($Artikel_Merkmal == 'Art.Bez.') {
					// Attribute Artikel Name -> Update auf products_description
					$sql_data_array = array(	'products_name' => $Artikel_Auspraegung );
					dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, "products_id='$Artikel_ID'");
				} else if ($Artikel_Merkmal == 'Kurzbeschreibung') {
					// Attribute Kurzbeschreibung -> Update auf products_description
					$sql_data_array = array(	'products_short_description' => $Artikel_Auspraegung );
					dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, "products_id='$Artikel_ID'");
				} else if ($Artikel_Merkmal == 'Kurzbeschreibung') {
					// Attribute Kurzbeschreibung -> Update auf products_description
					$sql_data_array = array(	'products_short_description' => $Artikel_Auspraegung );
					dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, "products_id='$Artikel_ID'");
				} else if ($Artikel_Merkmal == 'Meta ueberschrift') {
					// Attribute Meta ueberschrift -> Update auf products_description
					$sql_data_array = array(	'products_meta_title' => $Artikel_Auspraegung );
					dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, "products_id='$Artikel_ID'");
				} else if ($Artikel_Merkmal == 'Meta Tag Index') {
					// Attribute Meta Tag Index -> Update auf products_description
					$sql_data_array = array(	'products_meta_description' => $Artikel_Auspraegung );
					dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, "products_id='$Artikel_ID'");
				} else if ($Artikel_Merkmal == 'Inhalt') {
					// Attribute Inhalt -> TODO Verpackungseinheiten
				// MAPPINGS AUF KATEGORIEN 
				} else if ($Artikel_Merkmal == 'Ballerina') {
					//Update oder Insert auf Procuts2Categories
					$cmd = "select products_id, categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where " .
							"products_id='$Artikel_ID' AND categories_id='$Artikel_Kategorie'";
					$desc_query = dmc_db_query($cmd);
					if (($desc = dmc_db_fetch_array($desc_query)))
					{
						$cmd = "delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where " .
						"products_id='$Artikel_ID' AND categories_id='$Artikel_Kategorie'";
						dmc_db_query($cmd);
					 }
					
					// Kategoriezuordnung eintragen
					$insert_sql_data= array(
						'products_id' => $Artikel_ID,
						'categories_id' => $Artikel_Kategorie);
					dmc_sql_insert_array(TABLE_PRODUCTS_TO_CATEGORIES, $insert_sql_data);
					
				} else {
					// Alle sonstigen Attribute in tabelle dmc_attributes schreiben
					// Überprüfen, ob attribute bereits angelegt
					$cmd = "select products_id from dmc_attributes where " .
							"products_id='$Artikel_ID' AND attribute='$Artikel_Merkmal' AND value='$Artikel_Auspraegung'";
					$desc_query = dmc_db_query($cmd);
					if (($desc = dmc_db_fetch_array($desc_query)))
					{
						$cmd = "delete from dmc_attributes where " .
						"products_id='$Artikel_ID' AND attribute='$Artikel_Merkmal' AND value='$Artikel_Auspraegung'";
						dmc_db_query($cmd);
					 }
					
					// Kategoriezuordnung eintragen
					$insert_sql_data= array(
						'products_id' => $Artikel_ID,
						'attribute' => $Artikel_Merkmal,
						'value' => $Artikel_Auspraegung
						);
					dmc_sql_insert_array('dmc_attributes', $insert_sql_data);
					
				}
				
			  if (DEBUGGER>=1) fwrite($dateihandle, "Detail Zuordnung products_keywords eingetragen.\n");
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Detail nicht eingetragen, da  Artikel nicht existent.\n");
			} //  endif Wenn Artikel existieren
		} // end exportmodus gambio_attributes
		
		// Exportmodus languages
		if ($ExportModusSpecial=='cat_languages') {
		
			fwrite($dateihandle, "Export cat_languages\n");
			// Beispiel selectline: select 'cat_languages' AS uebertragungsart,  'c'+g.Nummer AS Kat_ID, 'es' AS SprachID, ISNULL(lang.Bezeichnung,g.Bezeichnung) AS Kategorie_Bezeichnung, ISNULL(lang.Zusatz,'') AS Kategorie_Text, '' AS Kategorie_Meta_Title, '' AS Kategorie_Meta_Desc, '' AS Kategorie_Meta_keyw, '' AS FF9, '' AS FF10, '' AS FF11, '' AS FF12 FROM Gruppen AS g  LEFT OUTER JOIN  FREMDBEZ AS lang ON (g.Nummer = lang.Nummer AND lang.Sprache='1' AND lang.Typ='AG') 

			$WaWi_Kategorie_ID = $Freifeld{2};
			$Sprache_id = $Freifeld{3};
			$Kategorie_Bezeichnung = utf8_decode($Freifeld{4});
			$Kategorie_Text = sonderzeichen2html(true,$Freifeld{5});
			$Kategorie_Meta_Title = sonderzeichen2html(true,$Freifeld{6});
			$Kategorie_Meta_Desc = sonderzeichen2html(true,$Freifeld{7});
			$Kategorie_Meta_Keyw = sonderzeichen2html(true,$Freifeld{8});
			
			$Kategorie_ID=dmc_get_category_id($WaWi_Kategorie_ID);
			if ($Kategorie_ID=='0')
				$exists = false;
			else
				$exists = true;
			
			fwrite($dateihandle, "Kategorie_ID = $WaWi_Kategorie_ID (ShopID=$Kategorie_ID) fuer Sprache $Sprache_id\n");
			
			// Wenn Kategorie existiert, Details updaten 
			if ($exists) {
				// Details eintragen : 
				// select 'languages' as ExportModus, p.ARTNR as Artikelnummer,  '1' as Language_ID, p.Fld01 as Artikel_Bezeichnung,  p.ZUSTEXT1 as Artikel_Text,  '' as Freifeld6, '' as Freifeld7,'
				// ' as Freifeld8,'' as Freifeld9,'' as Freifeld10,'' as Freifeld11,'' as Freifeld12 FROM SG_AUF_ARTIKEL as p where p.Fld01 <> '' or p.ZUSTEXT2 <> ''
				// übermittelte Kategoriebezeichnung, z.B. Installation\Fittings\Lötfittings\Übergangsmuffen		
		
				if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
					$sql_data_array = 
							array(
								//'language_code' => $Sprache_id,
								'categories_name' => $Kategorie_Bezeichnung,
								// 'categories_heading_title' => $Kategorie_Meta_Title
								'categories_description' => $Kategorie_Text
							);
				
					$Kategorie_SEO_Bezeichnung = dmc_prepare_seo ($Kategorie_Bezeichnung,"category",$Kategorie_ID,$Sprache_id);
			
					$seo_data_array = array(		
					'url_md5' => md5($Sprache_id.'/'.$Kategorie_SEO_Bezeichnung),
					'url_text' => $Sprache_id.'/'.$Kategorie_SEO_Bezeichnung,
					'language_code' => $Sprache_id,
					'link_type' => '2',   			//2 = kategorie
					'meta_description' => $meta_desc,
					'meta_title' => $meta_desc,
					'meta_keywords' => $meta_desc,
					'link_id' =>  $Kategorie_ID);	
					if (SHOPSYSTEM_VERSION>=4.2) $seo_data_array['store_id'] = SHOP_ID;		// Ab veyton 4.2 
								
				} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false){
					$sql_data_array = array(	
								//'id_category' => $Kategorie_ID,
								//'id_lang' => $Sprache_id,
								'name' => $Kategorie_Bezeichnung,
								'description' =>  $Kategorie_Text,
								//'link_rewrite' => $language_id.'/'.$seo,
								'meta_description' => $Kategorie_Meta_Desc,
								'meta_title' => $Kategorie_Meta_Title,
								'meta_keywords' => $Kategorie_Meta_Keyw);	
				} else {
					$sql_data_array = 
							array(	//'categories_id' => $Kategorie_ID,
									//  'language_id' => $language_id,
									  'categories_name' => $Kategorie_Bezeichnung,
									  'categories_description' => $Kategorie_Text);
				}

				// Bestehende Daten laden
				if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
					if (dmc_entry_exists(TABLE_CATEGORIES_DESCRIPTION,"categories_id",$Kategorie_ID,"and","language_code",$Sprache_id)) {
						fwrite($dateihandle, "Update language fuer kategorie_id ='$Kategorie_ID' and language_code = '" . $Sprache_id . "'\n");
						dmc_sql_update_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, "categories_id ='$Kategorie_ID' and language_code = '" .$Sprache_id . "'");
						// VEYTON SEO
						dmc_sql_update_array(TABLE_SEO_URL, $seo_data_array, "link_id ='$Kategorie_ID' and language_code = '" .$Sprache_id . "' and link_type='2'");
						fwrite($dateihandle, "Update $Sprache_id.'/'.$Kategorie_SEO_Bezeichnung fuer kategorie_id ='$Kategorie_ID' and language_code = '" . $Sprache_id . "'\n");
					} else {
						$sql_data_array['categories_id']=$Kategorie_ID;
						$sql_data_array['language_code']=$Sprache_id;
						fwrite($dateihandle, "Insert $Sprache_id.'/'.$Kategorie_SEO_Bezeichnung fuer kategorie_id ='$Kategorie_ID' and language_code = '" . $Sprache_id . "'\n");
						dmc_sql_insert_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
						// VEYTON SEO
						dmc_sql_insert_array(TABLE_SEO_URL, $seo_data_array);
					}		
				} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
					fwrite($dateihandle, "Update languages fuer kategorie_id ='$id_category' and language_code = '" . $Sprache_id . "'\n");
					dmc_sql_update_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, "categories_id ='$Kategorie_ID' and id_lang = '" .$Sprache_id . "'");
				} else {
					fwrite($dateihandle, "Update languages fuer kategorie_id ='$Kategorie_ID' and language_id = '" . $Sprache_id . "'\n");
					dmc_sql_update_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, "categories_id ='$Kategorie_ID' and language_id = '" . $Sprache_id . "'");
				}
				
				if (DEBUGGER>=1) fwrite($dateihandle, "cat_languages Zuordnung eingetragen.\n");
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: cat_languages Zuordnung nicht eingetragen, da  Kategorie nicht existent.\n");
			} //  endif Wenn Artikel existieren
		} // end exportmodus cat_languages
			
		// Berechtigungen am Ende des Abgleiches einmalig setzen
		if ($ExportModusSpecial=='customer_matrix') {
			fwrite($dateihandle, "Export customer_matrix\n");
			
			$query = "UPDATE products a INNER JOIN products_to_categories b ON a.products_id = b.products_id".
			" SET a.group_permission_0=1, a.group_permission_1=0, a.group_permission_2=0,a.group_permission_3=0, a.group_permission_4=0, a.group_permission_5 = 1  WHERE b.categories_id=1";
			fwrite($dateihandle, "1490 query=".$query."\n");
			dmc_sql_query_alt($query);
			$query = "UPDATE products a INNER JOIN products_to_categories b ON a.products_id = b.products_id".
			" SET  a.group_permission_0=1, a.group_permission_1=0, a.group_permission_2=0,a.group_permission_3=0, a.group_permission_4=1, a.group_permission_5 = 1  WHERE b.categories_id=2";
			fwrite($dateihandle, "1494 query=".$query."\n");
			dmc_sql_query_alt($query);
			$query = "UPDATE products a INNER JOIN products_to_categories b ON a.products_id = b.products_id".
			" SET a.group_permission_0=1,  a.group_permission_1=1, a.group_permission_2=1,a.group_permission_3=1, a.group_permission_4=1, a.group_permission_5 = 1  WHERE b.categories_id=4";
			dmc_sql_query_alt($query);
			$query = "UPDATE products a INNER JOIN products_to_categories b ON a.products_id = b.products_id".
			" SET a.group_permission_0=1,  a.group_permission_1=1, a.group_permission_2=1,a.group_permission_3=1, a.group_permission_4=1, a.group_permission_5 = 1  WHERE b.categories_id=6";
			dmc_sql_query_alt($query);
			$query = "UPDATE products a INNER JOIN products_to_categories b ON a.products_id = b.products_id".
			" SET a.group_permission_0=1,  a.group_permission_1=0, a.group_permission_2=0,a.group_permission_3=0, a.group_permission_4=1, a.group_permission_5 = 1  WHERE b.categories_id=5";
			dmc_sql_query_alt($query);
			$query = "UPDATE products a INNER JOIN products_to_categories b ON a.products_id = b.products_id".
			" SET a.group_permission_0=1,  a.group_permission_1=0, a.group_permission_2=0,a.group_permission_3=0, a.group_permission_4=0, a.group_permission_5 = 1  WHERE b.categories_id=7";
			dmc_sql_query_alt($query);
			$query = "UPDATE products a INNER JOIN products_to_categories b ON a.products_id = b.products_id".
			" SET a.group_permission_0=1,  a.group_permission_1=0, a.group_permission_2=0,a.group_permission_3=0, a.group_permission_4=0, a.group_permission_5 = 0  WHERE b.categories_id=3";
			dmc_sql_query_alt($query);
			$query = "UPDATE products a INNER JOIN products_to_categories b ON a.products_id = b.products_id".
			" SET a.group_permission_0=1,  a.group_permission_1=1, a.group_permission_2=1,a.group_permission_3=1, a.group_permission_4=1, a.group_permission_5 = 1  WHERE b.categories_id=8";
			dmc_sql_query_alt($query); 
			 
		} // end exportmodus customer_matrix
		
		// Exportmodus dmc_handelsstueckliste 
		if ($ExportModusSpecial=='dmc_handelsstueckliste') {
			// select 'dmc_handelsstueckliste' AS uebertragungsart,  st.Artikelnummer AS Artikel_Artikelnr, (SELECT TOP (1) ART.Bezeichnung FROM ART WHERE (st.Artikelnummer = ART.Artikelnummer)) as Bezeichnung,  st.SetArtikelnummer Set_Artikelnr, st.[Position] AS HST_Position, st.Menge AS Menge, st.Mengeneinheit AS Einheit, st.Zielpreis AS Preis, '19' AS MwSt_Satz, '' AS FF9, '' AS FF10, '' AS FF11, '' AS FF12, '' AS FF13 FROM ART as p, ARTSET as st WHERE p.Artikelnummer = st.SetArtikelnummer AND p.ShopAktiv = 'true' AND p.Artikelnummer like '%' ORDER BY st.[Position]

			$Artikel_Artikelnr = html_entity_decode (sonderzeichen2html(true,$Freifeld{2}), ENT_NOQUOTES);
			$Bezeichnung=html_entity_decode (sonderzeichen2html(true,$Freifeld{3}), ENT_NOQUOTES);
			$Set_Artikelnr=html_entity_decode (sonderzeichen2html(true,$Freifeld{4}), ENT_NOQUOTES);
			$HST_Position=$Freifeld{5};
			$Menge=$Freifeld{6};
			$Einheit=html_entity_decode (sonderzeichen2html(true,$Freifeld{7}), ENT_NOQUOTES);
			$Preis=$Freifeld{8};
			$MwSt_Satz=$Freifeld{9};

			// Der Artikelnummer im der SetNummer ERSTE id ermitteln
			$cmd = "SELECT id FROM " . "dmc_handelsstueckliste" .
		           " WHERE artnr = '$Artikel_Artikelnr' AND set_artnr='$Set_Artikelnr' LIMIT 1";
				
			if (DEBUGGER>=1) fwrite($dateihandle, "1647 handelsstueckliste $cmd geupdated.\n");
			
			$sql_query = dmc_db_query($cmd);
		    if ($sql_result = dmc_db_fetch_array($sql_query))
		    {
				// Stueckliste leeren, wenn erster Artikel (Position=1)
				if ($HST_Position==1) {
					// Leeren / delete first
					$cmd = "DELETE FROM ". "dmc_handelsstueckliste" ." WHERE " .
						"artnr='$Artikel_Artikelnr' ";
						dmc_db_query($cmd);
						
					// Neu anlegen
					$exists = 0;
				} else {
					// Existiert
					$ID = $sql_result['id'];
					$exists = 1;
				} //end if else 
			} //end if
			if (DEBUGGER>=1) fwrite($dateihandle, "1555.\n");
		
			// Wenn Artikel in HSTL existiert, Update
			if ($exists == 1) {
				if (DEBUGGER>=1) fwrite($dateihandle, "1559.\n");
				$sql_data_array = array(	
					'bezeichnung' => $Bezeichnung,
					'set_position' => $HST_Position,
					'menge' => $Menge,
					'einheit' => $Einheit,
					'preis' => $Preis,
					'mwst' => $MwSt_Satz
				);
				dmc_sql_update_array('dmc_handelsstueckliste', $sql_data_array, " id=$ID");
				if (DEBUGGER>=1) fwrite($dateihandle, "Detail Zuordnung $Artikel_Artikelnr zu handelsstueckliste $Set_Artikelnr geupdated.\n");
			} else {
				// Zuordnung eintragen
				$sql_data_array = array(	
					'artnr' => $Artikel_Artikelnr,
					'bezeichnung' => $Bezeichnung,
					'set_artnr' => $Set_Artikelnr,
					'set_position' => $HST_Position,
					'menge' => $Menge,
					'einheit' => $Einheit,
					'preis' => $Preis,
					'mwst' => $MwSt_Satz
				);
				dmc_sql_insert_array('dmc_handelsstueckliste', $sql_data_array);
				if (DEBUGGER>=1) fwrite($dateihandle, "Detail Zuordnung $Artikel_Artikelnr zu handelsstueckliste $Set_Artikelnr eingetragen.\n");
			} // end if
		} // end exportmodus dmc_handelsstueckliste
		
		if ($ExportModusSpecial=='presta_pdf'){
		
			if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_details presta_pdf \n");			
			
			// select 'presta_pdf' AS uebertragungsart, p.Artikelnummer AS Artikel_Artikelnr, '5' as anzahlLang, 'application/pdf' AS  mime, '' AS FF5, '' AS FF6, '' AS FF7, '' AS FF8, '' AS FF9, '' AS FF10, '' AS Freifeld11, ArtikelNr+'.pdf' AS PDF_Upload FROM Artikel

			$Artikel_Artikelnr = html_entity_decode (sonderzeichen2html(true,$Freifeld{2}), ENT_NOQUOTES);
			$anzahlLang=html_entity_decode (sonderzeichen2html(true,$Freifeld{3}), ENT_NOQUOTES);
			$mime= html_entity_decode (sonderzeichen2html(true,$Freifeld{4}), ENT_NOQUOTES);
			
			$file_name = $Freifeld{12};
			// Wenn Datei Verzeichnis enthaelt, dann den Dateinamen separieren
		  	$file_name = str_replace(' ','',$file_name); 
			if (strpos($file_name, "\\") !== false) {
				$file_name=substr($file_name,(strrpos($file_name,"\\")+1),254); 
			} 
		
			if (DEBUGGER>=1) fwrite($dateihandle, "Artikel_Artikelnr = $Artikel_Artikelnr, anzahlLang = $anzahlLang, mime= $mime,file_name =$file_name  \n");			
			
			//  Überprüfen, ob Attachemnt existent
			$temp_id_query = dmc_db_query(	"SELECT id_attachment as id FROM ".TABLE_ATTACHEMENTS." WHERE file_name='".$file_name."' LIMIT 1;");
			$TEMP_ID = dmc_db_fetch_array($temp_id_query);			 
			$attID = $TEMP_ID['id'];
			// Wenn noch kein Eintrag
			if ($attID =='' || $TEMP_ID['id'] == null) {	
				//anhanginformationen [insert]
				$sql_attachment = array(
					//'id_attachment' => $attID+1, 		// autoincrement	
					'file' => md5($file_name),			
					'file_name' => $file_name,		
					'mime ' => $mime
				);				
				dmc_sql_insert_array(TABLE_ATTACHEMENTS, $sql_attachment);
				$attID = dmc_db_get_new_id();
			
				//beschreibung zum anhang [insert]
				for ( $Sprach_ID=1; $Sprach_ID <= $anzahlLang; $Sprach_ID++ ) {
					$sql_attachment_lang = array(
						'id_attachment' => $attID, 			
						'id_lang' => $Sprach_ID, 			
						'name' => $Artikel_Artikelnr,		
						'description ' => $Artikel_Artikelnr
					);
					dmc_sql_insert_array(TABLE_ATTACHEMENTS_LANG, $sql_attachment_lang);
				} 
				if (DEBUGGER>=1) fwrite($dateihandle, "1629 INSERT ERFOLGREICH\n");		

				// Datei in korrektes Verzeichnis unter Code kopieren
				copy (DIR_ORIGINAL_IMAGES.$file_name,SHOP_ROOT.'download'.md5($file_name));

			}
			
			// Dokument dem Artikel zuordnen
			//  Überprüfen, ob Attachment bereits zugeordnet (bei existenten)
			$temp_id_query = dmc_db_query(	"SELECT id_product as id FROM ".TABLE_PRODUCTS_ATTACHEMENTS." WHERE id_attachment='".$Artikel_Artikelnr."' LIMIT 1;");
			$TEMP_ID = dmc_db_fetch_array($temp_id_query);			 
			$attID = $TEMP_ID['id'];
			// Wenn noch kein Eintrag
			if ($attID =='' || $TEMP_ID['id'] == null) {	
				 	 
				$sql_product_attachment = array(
						'id_product' => $id_product, 			
						'id_attachment' => $id_attachment
				);
				dmc_sql_insert_array(TABLE_PRODUCTS_ATTACHEMENTS, $sql_product_attachment);
			} //endif
			if (DEBUGGER>=1) fwrite($dateihandle, "1647  ERFOLGREICH\n");			
		}//ende function pdf
		
		if ($ExportModusSpecial=='presta_img'){
			$cmd1 = "truncate table " . TABLE_IMAGES;
			dmc_db_query($cmd1);
			
			$cmd2 = "truncate table " . TABLE_IMAGES_DESCRIPTION;
			dmc_db_query($cmd2);
			if (DEBUGGER>=1) fwrite($dateihandle, "Images erfolgreich entfernt\n");			
		}
		
		// Exportmodus Dokument in Dokument Tabelle anlegen (neu ab 06062012)
		if ($ExportModusSpecial=='dmc_documents_header') {
			/* <!-- Auftraege NAV -->
				select 'dmc_documents_header' AS ExportModus, 'Auftrag' AS Belegart, b.[No_] AS document_no, b.[Order Date] AS document_date,(SELECT TOP 1 ad.[E-Mail] FROM [NAV].[dbo].[Herminghaus$Contact] AS ad  WHERE ad.[E-Mail] is not null AND ad.[E-Mail]<>''  AND ad.[External ID]=b.[Sell-to Customer No_]) AS customer_email_adress, b.[Bill-to Name] AS document_printed_name, b.[Bill-to Name] AS document_printed_name2, b.[Your Reference] AS document_referenz, 0.00 AS document_sum_net, b.[Payment Discount %] AS document_discount, '' AS Freifeld11, 'S:\System\M100\Archiv\RE_' + b.[No_]+'.PDF' AS PDF_Upload FROM [NAV].[dbo].[Herminghaus$Sales Header] AS b WHERE (b.[Order Date] > '2010-01-01') AND (b.[No_] like 'A%') AND (b.[No_] not like 'AG%')  ORDER BY b.[Order Date] ASC
			*/
			/* CREATE TABLE IF NOT EXISTS `dmc_documents_header` (
					 `document_id` int(11)  NULL auto_increment,
				  `customer_web_user_id` int(11)  NULL,
				  `customer_email_adress` varchar(80)  NULL,
					`document_type` varchar(100)  NULL,
				  `document_file_type` varchar(100)  NULL,
				  `document_link` varchar(100)  NULL,
					`document_no` varchar(20)  NULL,
					`document_date` varchar(150)  NULL,
				  `delivery_date` varchar(150)  NULL,
				  `document_printed_name` varchar(100)  NULL,
				  `document_printed_name2` varchar(100)  NULL,
				  `document_printed_name3` varchar(100)  NULL,
				  `document_printed_company` varchar(150)  NULL,
				  `document_printed_street` varchar(150)  NULL,
				  `document_printed_zip` varchar(50)  NULL,
				  `document_printed_city` varchar(150)  NULL,
				  `document_printed_country_code` varchar(30)  NULL,
				  `document_referenz` varchar(30)  NULL,
				  `document_sum_net` decimal(8,2)  NULL,
				  `document_vat` decimal(8,2)  NULL,
				  `document_sum_vat` decimal(8,2)  NULL,
				  `document_sum_gros` decimal(8,2)  NULL,
				  `document_discount` decimal(8,2)  NULL,
					  PRIMARY KEY  (`document_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; */
			$Belegart = $Freifeld{2};				
			$Belegnummer = $Freifeld{3};
			$Datum = $Freifeld{4};
			$EMail = $Freifeld{5};
			$Name = $Freifeld{6};
			$Vorname = $Freifeld{7};
			$Zusatz = $Freifeld{8};
			$GesamtpreisNetto = $Freifeld{9};
				if ($GesamtpreisNetto =='') $GesamtpreisNetto =0.00;
			$GesamtRabatt = $Freifeld{10};
				if ($GesamtRabatt =='') $GesamtRabatt =0.00;
			$pdf_datei = $Freifeld{12};
			
			if (!defined('IMAGE_FOLDER')) {
				define('IMAGE_FOLDER',$_SERVER['DOCUMENT_ROOT'].'/monapur.com/shop/images/product_images/original_images/'); 
			}
			if (!defined('PDF_FOLDER')) {
				define('PDF_FOLDER',$_SERVER['DOCUMENT_ROOT'].'/monapur.com/shop/dmc_document/pdfs/'); 
			}
			// Wenn Datei Verzeichnis enthaelt, dann den Dateinamen separieren
		  	$pdf_datei = str_replace(' ','',$pdf_datei); 
			if (strpos($pdf_datei, "\\") !== false) {
				$pdf_datei=substr($pdf_datei,(strrpos($pdf_datei,"\\")+1),254); 
			} 
			if (DEBUGGER>=1) fwrite($dateihandle, "dmc_documents_header - Belegnummer  $Belegnummer for $EMail  and belegdatei = $pdf_datei\n");
			if (file_exists(IMAGE_FOLDER . $pdf_datei)) {
					// PDF DAtei in korrektes Verzeichnis kopieren
				copy(IMAGE_FOLDER . $pdf_datei, PDF_FOLDER .$pdf_datei);
				
			}
					
			// get Magento customer ID 
			// $customer_shop_id=dmc_get_id_by_email($EMail);
			$customer_shop_id=dmc_get_customer_id('',$EMail);
			if (DEBUGGER) fwrite($dateihandle, "CustomerId=$customer_shop_id\n");			
			// Wenn Kunde existiert, Kundeninformationen zuordnen
			if ($customer_shop_id<> "") {
				// Strasse, Ort, PLZ etc
			} else {
				$customer_shop_id=0;
			}
			
			// Neue Datensaetze anlegen
			$where="document_no='".$Belegnummer."'";
			if (!dmc_entry_exists('document_no', 'dmc_documents_header', $where)) {
					// Insert
					dmc_sql_insert("dmc_documents_header", 
									"(customer_web_user_id , document_type ,document_no ,document_date ,customer_email_adress ,
									document_printed_name ,document_printed_name2 ,document_referenz, 
									document_sum_net, document_discount ,document_status)", 
									"($customer_shop_id, '$Belegart', '$Belegnummer', '$Datum', '$EMail', 
										'$Name', '$Vorname', '$Zusatz',$GesamtpreisNetto,$GesamtRabatt,'$document_status')");
			} // end if else
		} // end exportmodus dmc_documents_header
		
		
		// Exportmodus Dokumentpositionen in Dokument Tabelle anlegen (neu ab 06062012)
		if ($ExportModusSpecial=='dmc_documents_positions') {
			/* <!-- Auftraege NAV -->
				select 'dmc_documents_positions' AS ExportModus, 'Auftrag' AS Belegart, b.[No_] AS document_no, b.[Line No_] AS position_no, b.[No_] AS product_sku, b.['description'] AS product_name, b.[Quantity] AS product_qty, b.[Unit Price] AS product_price, b.[Line Discount %] AS product_discount,b.[Quantity] * b.[Unit Price] AS product_price_amount, b.[VAT %] AS product_vat_percent, '' AS FF12 FROM [NAV].[dbo].[Herminghaus$Sales Line] AS b WHERE (b.[Shipment Date] > '2010-01-01') AND (b.[No_] like 'A%') AND (b.[No_] not like 'AG%')  ORDER BY b.[No_] , b.[Line No_],b.[Shipment Date] ASC
				CREATE TABLE IF NOT EXISTS `dmc_documents_positions` (
				  `document_id` int(11)  NULL,
				  `document_no` varchar(20)  NULL,
				  `document_type` varchar(20)  NULL,
				  `pos` varchar(100)  NULL,
				  `product_sku` varchar(50)  NULL,
				  `product_name` varchar(200)  NULL,
				  `product_qty` int(11)  NULL,
				  `product_price` int(11)  NULL,
				  `product_discount` int(11)  NULL,
				  `product_price_amount` int(11)  NULL,
				  `product_vat_percent` int(11)  NULL,
				  `document_user` varchar(100)  NULL
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			*/
		
			$Belegart = $Freifeld{2};				
			$Belegnummer = $Freifeld{3};
			$position_no = $Freifeld{4};
			$product_sku_variant_vpe = $Freifeld{5};
			$product_name = $Freifeld{6};
			$product_qty = $Freifeld{7};
				if ($product_qty =='') $product_qty =0.00;
			$product_price = $Freifeld{8};
						if ($product_price =='') $product_price =0.00;
			$product_discount = $Freifeld{9};
						if ($product_discount =='') $product_discount =0.00;
			$product_price_amount = $Freifeld{10};  
						if ($product_price_amount =='') $product_price_amount =0.00;
			$product_vat_percent = $Freifeld{11};
						if ($product_vat_percent =='') $product_vat_percent =0.00;
			$product_referenz = $Freifeld{12};
			
			if (DEBUGGER>=1) fwrite($dateihandle, "dmc_documents_positions - Belegnummer  $Belegnummer \n");
			
			// sku + variantencode evtl
			$product_sku = explode ( '@', $product_sku_variant_vpe);
			// qty moeglicherweise mehrere, 
			$product_qties = explode ( '@', $product_qty);
		
			if (DEBUGGER>=1) fwrite($dateihandle, "SKU =".$product_sku[0]." \n");
			
			// Neue Datensaetze anlegen
			$where="document_no='".$Belegnummer."' AND pos='".$position_no."'"; 
			if (dmc_entry_exists('document_no', 'dmc_documents_positions', $where)) {
				if (DEBUGGER>=1) fwrite($dateihandle, "delete first\n");
				// Delete first, damit es keine doppelten Eintraege gibt
				dmc_sql_delete(DB_TABLE_PREFIX . 'dmc_documents_positions', $where);
				// Insert
				dmc_sql_insert("dmc_documents_positions", 
								"(document_no, document_type, pos, product_sku,product_variant,product_vpe, product_name ,
								product_qty,product_qty2,product_qty3, product_price, product_discount, 
								product_price_amount, product_vat_percent,product_referenz )", 
								"('$Belegnummer', '$Belegart', '$position_no', '".$product_sku[0]."', '".$product_sku[1]."', '".$product_sku[2]."', '$product_name', 
									'".$product_qties[0]."', '".$product_qties[1]."','".$product_qties[2]."', '$product_price', '$product_discount',$product_price_amount,$product_vat_percent, '$product_referenz')");
						// Pruefen ob der Kommissionseintrag etc auch in den Spalten document_kommissionen, (document_products,) document_skus sind 
						$where = "document_skus like '%".$product_sku[0]."%' AND document_kommissionen like '%".$product_referenz."%'";
						if (!dmc_entry_exists('document_no', 'dmc_documents_header', $where)) {
							// Insert document_kommissionen, (document_products,) document_skus
							$update = "document_skus = document_skus+'|'+'".$product_sku[0]."'";
							if ($product_referenz<>'') $update .= ", document_kommissionen = document_kommissionen+'|'+'".$product_referenz."'";
							dmc_sql_update("dmc_documents_header", $update, " document_no='".$Belegnummer."' AND document_type='".$Belegart."' ");
						} // end if else
			} else {
				// Insert
				dmc_sql_insert("dmc_documents_positions", 
								"(document_no, document_type, pos, product_sku,product_variant,product_vpe, product_name ,
								product_qty,product_qty2,product_qty3, product_price, product_discount, 
								product_price_amount, product_vat_percent,product_referenz )", 
								"('$Belegnummer', '$Belegart', '$position_no', '".$product_sku[0]."', '".$product_sku[1]."', '".$product_sku[2]."', '$product_name', 
									'".$product_qties[0]."', '".$product_qties[1]."','".$product_qties[2]."', '$product_price', '$product_discount',$product_price_amount,$product_vat_percent, '$product_referenz')");
				
				// Pruefen ob der Kommissionseintrag etc auch in den Spalten document_kommissionen, (document_products,) document_skus sind 
				$where = "document_skus like '%".$product_sku[0]."%' AND document_kommissionen like '%".$product_referenz."%'";
				if (!dmc_entry_exists('document_no', 'dmc_documents_header', $where)) {
				// Insert document_kommissionen, (document_products,) document_skus
					$update = "document_skus = document_skus+'|'+'".$product_sku[0]."'";
					if ($product_referenz<>'') $update .= ", document_kommissionen = document_kommissionen+'|'+'".$product_referenz."'";
					dmc_sql_update("dmc_documents_header", $update, " document_no='".$Belegnummer."' AND document_type='".$Belegart."' ");
				} // end if else
			} // end if else
				
		} // end exportmodus dmc_documents_positions

		// dmc_document_hub DOKUMENTENVERTEILUNG
		if ($ExportModusSpecial=='dmc_document_hub') {
			/* select 'dmc_document_hub' AS Freifeld1, '/media/pdfs' AS Online_Verzeichnis, 'add_pdf_to_cat_desc' as Features, t.RecID AS Features_id,
			'<a href="http://www.fantastisch.info/herminghaus/media/pdfs/" target="_blank">'+media.WebFileName+'</a>' as Feature_Name, 
			'' as FF6, '' as Freifeld7,'' as Freifeld8,'' as Freifeld9,'' as Freifeld10,'' as Freifeld11,
			media.WebFileName AS PDF_Upload FROM BELEG AS B INNER JOIN KUNDEN AS K ON B.Adressnummer = K.Nummer WHERE (B.Datum > '05.02.2012') AND (B.Belegtyp = 'R') AND K.EMail<>'' ORDER BY B.Datum DESC 
			select 'dmc_document_hub' AS Freifeld1, '/pdf/D0CUMENTS' AS Online_Verzeichnis, 'pdf_invoice_create' as Features, substring(IhrAuftrag,3,10)-20000 AS Features_id, B.Belegnummer AS Features_Name, B.Datum, K.EMail, B.Name, B.Vorname, B.EuroBrutto AS GesamtpreisBrutto, '' AS Freifeld11, 'Q:\System\M04\Archiv\2014\02\REC\' + B.Belegnummer+'*.PDF' AS PDF_Upload FROM BELEG AS B INNER JOIN KUNDEN AS K ON B.Adressnummer = K.Nummer WHERE (B.Belegtyp = 'F') AND K.EMail<>'' AND B.Adressnummer like 'SP%' ORDER BY B.Datum DESC */
			/* Anzeige Rechnung in Kundenbereich - bei presta ueber modifizierte themes/.../history.tpl */
			
			// Uebergebene Variablen
			$Online_Verzeichnis = "..".$Freifeld{2};
			$Features = $Freifeld{3};					// pdf_invoice_create
			$Features_ID = $Freifeld{4};				// ZB OrderID
			$Features_Name = $Freifeld{5};
			$datei_name = $Freifeld{12};
			
			// Platzhalter *. aus Dateiname entfernen
			$datei_name = str_replace("*.",".",$datei_name);
			
			// Pruefen ob Konstante definiert
			if (!defined('IMAGE_FOLDER') || IMAGE_FOLDER == '')
				$IMAGE_FOLDER = "./upload_images/";		
			else 
				$IMAGE_FOLDER = IMAGE_FOLDER;
				
			// Definitionen
			$store_id=0;
			
			// Wenn Datei Verzeichnis enthaelt, dann den Dateinamen separieren
		  	$datei_name = str_replace(' ','',$datei_name); 
			if (strpos($datei_name, "\\") !== false) {
				$datei_name=substr($datei_name,(strrpos($datei_name,"\\")+1),254); 
			} 
			
			if (DEBUGGER>=1) fwrite($dateihandle, "dmc_document_hub - Belegdatei = $datei_name in Dateiverzeichnis $Online_Verzeichnis\n");
			if (DEBUGGER>=1) fwrite($dateihandle, "PDF Verzeichnis ".PDF_FOLDER."\n");
			
			if (DEBUGGER>=1) fwrite($dateihandle, "Datei: ".$IMAGE_FOLDER ."$datei_name\n");
			if (DEBUGGER>=1) fwrite($dateihandle, "Dateiendung: ".substr($datei_name,-4)."\n");
			
			// Kopier-Operationen
			if ($Features=="pdf_invoice_create") {
				// Kopieren und umbenennen
				if (file_exists($IMAGE_FOLDER . $datei_name)) {
					// Dateinamen zB 2.323.143.PDF kopieren in  base64 verschluesselte OrderID+rcm und zusaetzlich .pdf ergaenzen
					$Order_ID=$Features_ID;
					$datei_neu=base64_encode( $Order_ID.'rcm' ).".pdf";
					copy($IMAGE_FOLDER . $datei_name, $Online_Verzeichnis.'/'.$datei_neu);
					if (DEBUGGER>=1) fwrite($dateihandle, "Kopiert -> copy(".$IMAGE_FOLDER ."$datei_name,$Online_Verzeichnis/$datei_neu.)\n");
				}	else {
					if (DEBUGGER>=1) fwrite($dateihandle, "Zu kopierende Datei ".$IMAGE_FOLDER ."$datei_name existiert nicht .\n");
				}
			} else {
				// Standard Kopie
				if (file_exists($IMAGE_FOLDER . $datei_name)) {
					if (substr($datei_name,-4) == '.pdf' || substr($datei_name,-4) == '.PDF') {
						copy($IMAGE_FOLDER . $datei_name, PDF_FOLDER.$datei_name);
						if (DEBUGGER>=1) fwrite($dateihandle, "Kopiert -> copy(".$IMAGE_FOLDER ."$datei_name,".PDF_FOLDER-".$datei_name)\n");
					} else {
						copy($IMAGE_FOLDER . $datei_name, $Online_Verzeichnis.'/'.$datei_name);
						if (DEBUGGER>=1) fwrite($dateihandle, "Kopiert -> copy(".$IMAGE_FOLDER ."$datei_name,$Online_Verzeichnis/$datei_name.)\n");
					}				
				}
			} // end if else
				
			// Pruefen, ob noch besondere Aktionen durchzufuehren sind
			if ($Features=="add_pdf_to_cat_desc") {
				// Definitionen
				$entity_type_id=3;
				$attribute_id=36;
				// Kategorie ID 
				$cat_id=dmc_get_category_id($Features_ID);
				if ($cat_id<>-1) {
					if (DEBUGGER>=1) fwrite($dateihandle, "add_pdf_to_cat_desc -> cat_id=$cat_id\n");
					// PDF Link zu Kategorie-Beschreibung 
					$table = "catalog_category_entity_text";  
					$columns = "(entity_type_id, attribute_id, store_id, entity_id, value)";
					$values = "($entity_type_id , $attribute_id, $store_id, $art_id,$value)";		
					// Alte Desciption ermitteln
					$value=dmc_sql_select_value('value', $table, "entity_id= $art_id AND attribute_id=$attribute_id");
					if (strpos($value, $Features_Name) === false) {
						// Link existiert noch nicht -> Anhaengen
						$value = $value."<br />\n".$Features_Name;
						dmc_sql_update($table, " value='$value' ", " entity_id=$art_id AND attribute_id=$attribute_id AND entity_type_id=$entity_type_id AND store_id=$store_id ");
					}
				}
			} // end add_pdf_to_cat_desc
			if ($Features=="add_pdf_to_product_desc") {
				// Definitionen
				$entity_type_id=4;
				$attribute_id=61;
				// Pruefen ob Artikel existent und ggfls magento id ermitteln
				// get Magento article ID 
				$art_id=dmc_get_id_by_artno($Features_ID);
				if ($art_id<>-1 && $art_id<>'') {
					if (DEBUGGER>=1) fwrite($dateihandle, "add_pdf_to_product_desc -> art_id=$art_id\n");
					// PDF Link zu Kategorie-Beschreibung 
					$table = "catalog_product_entity_text";  
					$columns = "(entity_type_id, attribute_id, store_id, entity_id, value)";
					$values = "($entity_type_id , $attribute_id, $store_id, $art_id,$value)";		
					// Alte Desciption ermitteln
					$value=dmc_sql_select_value('value', $table, "entity_id= $art_id AND attribute_id=$attribute_id");
					if (strpos($value, $Features_Name) === false) {
						// Link existiert noch nicht -> Anhaengen
						$value = $value."<br />\n".$Features_Name;
						dmc_sql_update($table, " value='$value' ", " entity_id=$art_id AND attribute_id=$attribute_id AND entity_type_id=$entity_type_id AND store_id=$store_id ");
					}
				}
			} // end 
		} // end exportmodus dmc_document_hub

			// Exportmodus staffelpreise
		if ($ExportModusSpecial=='kundenstaffelpreise') {
			// select 'kundenstaffelpreise' AS uebertragungsart, vk.P_MODEL AS Artikel_Artikelnr, vk.P_CUSTGROUP AS Kundengruppe, vk.P_PRICENOTAX AS Preis1, '' AS Preis2, '' AS Preis3, '' AS Preis4, '' AS Preis5, '' AS DatumAB, '' AS DatumBis, '' AS FF11, '' AS FF12, '' AS FF13 FROM preise.csv AS vk WHERE (vk.P_PRICENOTAX NOT LIKE '1.00:') 
		// oder Kundengruppenpreise select 'kundenstaffelpreise' AS uebertragungsart, p.Artikel AS Artikel_Artikelnr, '1@2@3@4@5@6@7@8@9' AS Kundengruppe, 1 AS Menge, p.Verkaufspreis1&'@'&p.Verkaufspreis2&'@'&p.Verkaufspreis3&'@'&p.Verkaufspreis4&'@'&p.Verkaufspreis5&'@'&p.Verkaufspreis6&'@'&p.Verkaufspreis7&'@'&p.Verkaufspreis8&'@'&p.Verkaufspreis9 AS Gruppenpreise, '' AS ff2, '' AS ff3, '' AS ff4, '' AS ff5, '' AS ff6, '' AS ff7, '' AS FF11, '' AS FF12, '' AS FF13 FROM Artikel AS p WHERE p.Artikel IS NOT NULL AND (p.Artikelgruppe = '010' OR p.Artikelgruppe = '020' OR p.Artikelgruppe = '030' OR p.Artikelgruppe = '040')
		 
			if (DEBUGGER>=1)  fwrite($dateihandle, "details - kundenstaffelpreise\n");
			
			$Artikel_Artikelnr = trim($Freifeld{2});
			$Kunden_Gruppen = $Freifeld{3};			// Mehrere durch @ getrennt
			$abMenge = $Freifeld{4};
			$Gruppen_Artikel_Preise = $Freifeld{5};			// Mehrere durch @ getrennt
			
			// Achtung: $abMenge kann auch menge und Preis enthalten, z.B. 100.00:55.99
			$pos = strpos($abMenge,':');
			if ($pos !== false) {
				$abMenge = substr($Freifeld{4},0,$pos);  
				$Artikel_Preis = substr($Freifeld{4},$pos+1);  
			}
			
			// Unterstuetzung, ob mehrere Preise uebergeben wurden - Kundengruppen 1-10
			$kundengruppen = explode ( '@', $Kunden_Gruppen);
			$Artikel_Preise  = explode ( '@', $Gruppen_Artikel_Preise);
		
			// Der Artikelnummer zugehörige products_id und xsell_id ermitteln
			// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
			$cmd = "select products_id from " . TABLE_PRODUCTS .
		            " where products_model = '$Artikel_Artikelnr'";
			$sql_query = dmc_db_query($cmd);
		     	 
		    if ($sql_result = dmc_db_fetch_array($sql_query))
		    {      
				// ArtikelID von bestehendem Artikel ermitteln
				$Artikel_ID = $sql_result['products_id'];
				// Existiert
				$exists = 1;
				if (DEBUGGER>=1) fwrite($dateihandle, "Artikel_ID =  $Artikel_ID \n");
			} else {  //endif
				if (DEBUGGER>=1) fwrite($dateihandle, "Artikel $cmd nicht gefunden \n");
			}
			
			for($Anzahl = 0; $Anzahl <= (count($kundengruppen)-1); $Anzahl++) {     
				// Kudnengruppen und zugehoerige Preise durchlaufen	     
				$kundengruppe = $kundengruppen[$Anzahl];
				$Artikel_Preis = $Artikel_Preise[$Anzahl];
		
				// Mapping 
				// if ($kundengruppe == '12345') $Preisgruppe=3;
				//else $Preisgruppe=1;
				// Kundengruppenid ermitteln und baiserend hierauf die zu fuellende Tabelle /Preisgruppe
				$cmd = "SELECT customers_status_id FROM `customers_status` where  customers_status_name = '$kundengruppe' LIMIT 1";
				$sql_query = dmc_db_query($cmd);
				if ($sql_result = dmc_db_fetch_array($sql_query))
				{      
					// ArtikelID von bestehendem Artikel ermitteln
					$Preisgruppe = $sql_result['customers_status_id'];
				} else { 
					// Kundengruppe nicht existent
					if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Kundengruppe $kundengruppe nicht in Datenbank vorhanden\n");
					$exists = 0;
				} //endif
				
				
				// Abweichend fuer veyton -> alle kundengruppen
				if ($exists==1)
				if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false && $kundengruppe == 'all') {
					if (DEBUGGER>=1) fwrite($dateihandle, "Alle Kundengruppen Veyton\n");
					$sql_data_price_array = array(
						'discount_quantity' => $abMenge,
						'price' => $Artikel_Preis);
					// Wenn Preis-Zuordnung existiert -> update
					$cmd = "select id FROM " . TABLE_PRODUCTS_PRICE_GROUP."all ".
							" where products_id = '$Artikel_ID' and discount_quantity='$abMenge'";
									if (DEBUGGER>=1)  fwrite($dateihandle, "cmd=$cmd\n");
					$sql_query = dmc_db_query($cmd);
					if ($sql_result = dmc_db_fetch_array($sql_query)) {
						$temp_id=$sql_result['price_id'];
						$cmd = 	"select id FROM " . TABLE_PRODUCTS_PRICE_GROUP."all ".
								" where products_id = '$Artikel_ID' and discount_quantity='$abMenge'";	if (DEBUGGER>=1)  fwrite($dateihandle, "cmd=$cmd\n");
						dmc_sql_update_array(TABLE_PRODUCTS_PRICE_GROUP."all", $sql_data_price_array, "products_id = '$Artikel_ID' and discount_quantity='$abMenge'");
						dmc_sql_update(TABLE_PRODUCTS, "price_flag_graduated_all=1", "products_id=".$Artikel_ID."");
						if (DEBUGGER>=1)  fwrite($dateihandle, "update ".TABLE_PRODUCTS_PRICE_GROUP."all =  ArtID = '$Artikel_ID' and quantity='$abMenge' mit preis $Artikel_Preis1\n");
					}	else { // nicht existent
						// $sql_data_price_array['id'] = $NEUE_ID;
						$sql_data_price_array['products_id'] = $Artikel_ID;
						dmc_sql_insert_array(TABLE_PRODUCTS_PRICE_GROUP."all", $sql_data_price_array);
						// Veyton -> Abweichende Preise anzeigen Veyton
						dmc_sql_update(TABLE_PRODUCTS, "price_flag_graduated_all=1", "products_id=".$Artikel_ID."");
						if (DEBUGGER>=1)  fwrite($dateihandle, "insert(ed) in ".TABLE_PRODUCTS_PRICE_GROUP."all = ArtID $Artikel_ID and quantity='$abMenge' mit preis $Artikel_Preis1\n");
					}		
				} else { // nicht alle kundengruppen
					if ($exists==1) {	
					
						// Keine Kundenpreise in ZENCART
					if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false) {
						// Tabellen für Preis
						// Gastpreis -> personal_offers_by_customers_status_1
						// Neuer Kunde -> personal_offers_by_customers_status_2
						// Händler -> personal_offers_by_customers_status_3
						// Händler EU -> personal_offers_by_customers_status_4
							if (DEBUGGER>=1) fwrite($dateihandle, "details 2225=".constant('TABLE_PRICE' . $Preisgruppe)." mit preis=".$Artikel_Preis."\n");	
							if (DEBUGGER>=1) fwrite($dateihandle, "2229=".$abMenge."\n");
							if (defined('TABLE_PRICE' . $Preisgruppe) && constant('TABLE_PRICE' . $Preisgruppe) <> '')
							if ( $Artikel_Preis >0.01) {  	
								//Bei Veyton autoinc $temp_id_query = dmc_db_query("SELECT max(id) as total FROM " . constant('TABLE_PRICE' . $Preisgruppe) ."; ");	
								if (strpos(strtolower(SHOPSYSTEM), 'veyton') === false) {
									$abfrage="SELECT max(price_id) as total FROM " .constant('TABLE_PRICE' . $Preisgruppe) ." ";
									$temp_id_query = dmc_db_query($abfrage);	
									if ($TEMP_ID['total']!='null') { 
										try {
											$TEMP_ID = dmc_db_fetch_array($temp_id_query);
										} catch (MyException $e) {
											/* weiterwerfen der Exception */
											// throw $e;
											// Noch keine Einträge in Tablle vorhanden
											$TEMP_ID['total']='null';
										}
									}
									if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
										$NEUE_ID = 1;
									 else
										$NEUE_ID = $TEMP_ID['total'] + 1;
								} // end if (strpos(strtolower(SHOPSYSTEM), 'veyton') === false) {
								
								// Zu setzenden Preis ermitteln
								$pricenumber = constant('GROUP_PRICE' . $Preisgruppe);
								$products_price=$Artikel_Preis;
								if (DEBUGGER>=1) fwrite($dateihandle, "products_price 2255=".$Artikel_Preis."\n");
								// Für (zusätzliche) Preise
								// price_id   	  products_id   	  quantity   	  personal_offer
								// quantity   =1 , da keine Staffelpreise	 
								if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false)
									$sql_data_price_array = array(
										'discount_quantity' => $abMenge,
										'price' => $products_price);
								else 
									$sql_data_price_array = array(
										'quantity' => $abMenge,
										'personal_offer' => $products_price);
										
								if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false) 
										if (STORE_ID != "")
											$sql_data_price_array['store_id'] = STORE_ID;
										else 
											$sql_data_price_array['store_id'] = 1;
								
								// Wenn Preis-Zuordnung existiert -> update
								if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false)
									$cmd = "select id FROM " . TABLE_PRODUCTS_PRICE_GROUP. $Preisgruppe .
													" where products_id = '$Artikel_ID' and discount_quantity='$abMenge'";
								else 
									$cmd = "select price_id from " . constant('TABLE_PRICE' . $Preisgruppe) .
												" where products_id = '$Artikel_ID' and quantity='$abMenge'";
								
								$sql_query = dmc_db_query($cmd);
								if ($sql_result = dmc_db_fetch_array($sql_query)) {
									$temp_id=$sql_result['price_id'];
									if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
										$cmd = 	"select id FROM " . TABLE_PRODUCTS_PRICE_GROUP. $Preisgruppe .
												" where products_id = '$Artikel_ID' and discount_quantity='$abMenge'";
										dmc_sql_update_array(TABLE_PRODUCTS_PRICE_GROUP. $Preisgruppe, $sql_data_price_array, "products_id = '$Artikel_ID' and discount_quantity='$abMenge'");
									} else {
										$cmd = 	"select price_id from " . constant('TABLE_PRICE' . $Preisgruppe) .
												" where products_id = '$Artikel_ID' and quantity='$abMenge'";
										dmc_sql_update_array(constant('TABLE_PRICE' . $Preisgruppe), $sql_data_price_array, "products_id = '$Artikel_ID' and quantity='$abMenge'");
									}
									if (DEBUGGER>=1)  fwrite($dateihandle, "\nupdate sql1 =  ArtID = '$Artikel_ID' and quantity='$abMenge' mit preis $products_price\n");
								}
								else { // nicht existent
									if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
										// $sql_data_price_array['id'] = $NEUE_ID;
										$sql_data_price_array['products_id'] = $Artikel_ID;
										dmc_sql_insert_array(TABLE_PRODUCTS_PRICE_GROUP. $Preisgruppe, $sql_data_price_array);
									} else {
										$sql_data_price_array['price_id'] = $NEUE_ID;
										$sql_data_price_array['products_id'] = $Artikel_ID;
										dmc_sql_insert_array(constant('TABLE_PRICE' . $Preisgruppe), $sql_data_price_array);
									}
									if (DEBUGGER>=1)  fwrite($dateihandle, "\ninsert(ed) sql1 in ".constant('TABLE_PRICE' . $Preisgruppe)."= ArtID $Artikel_ID and quantity='$abMenge' mit preis $products_price auf id= $NEUE_ID\n");
									// Veyton -> Abweichende Preise anzeigen Veyton
									dmc_sql_update(TABLE_PRODUCTS, "price_flag_graduated_".$Preisgruppe."=1", "products_id=".$Artikel_ID.";");
									
								}		
																
							} // end if 
						
					} // end if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false) 
				
						
					  if (DEBUGGER>=1) fwrite($dateihandle, "Detail Zuordnung eingetragen.\n");
					} else { 
						if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Detail nicht eingetragen, da  Artikel nicht existent.\n");
					} //  endif Wenn Artikel existieren
				} // end if  // nicht alle kundengruppen
			} // end for // nicht alle kundengruppen
			
		} // end exportmodus kundenstaffelpreise
		
		// Exportmodus dmc_sage_cl_artikelnummern 
		if ($ExportModusSpecial=='dmc_sage_cl_artikelnummern') {
			// select 'dmc_sage_cl_artikelnummern' AS uebertragungsart, p.Artikel as Artikel_Artikelnr,  CONCAT (p.Artikel,'             ', p.Hersteller) AS Artikel_Artikelnr_ERP, p.Hersteller as Hersteller_ID,  '' AS FF5, '' AS FF6, '' AS FF7, '' AS FF8,  '' AS FF9, '' AS FF10, '' AS FF11, '' AS FF12,  '' AS FF13 FROM Artikel AS p WHERE (p.Artikelgruppe <> '091' AND p.Artikelgruppe <> '092' AND p.Artikelgruppe <> '111' AND p.Artikelgruppe <> '999' AND p.Artikelgruppe <> '080' AND p.Artikelgruppe <> '090') AND p.Statuskennzeichen='' AND p.Artikel like '$variable1%'
			/*$sql = "CREATE TABLE IF NOT EXISTS `dmc_erp_products` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `artnr` varchar(20) NOT NULL DEFAULT '',
			  `artnr_erp` varchar(20) NOT NULL DEFAULT '',
			  `maufacturer_erp` varchar(63) NOT NULL DEFAULT '',
			  `variante_von` varchar(20) NOT NULL DEFAULT '',
			  `merkmale` varchar(255) NOT NULL DEFAULT '',
			  `auspraegungen` varchar(255) NOT NULL DEFAULT '',
			  `preis` double NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			";*/
			
			$Artikel_Artikelnr =  $Freifeld{2};
			$Artikel_Artikelnr_ERP =  $Freifeld{3};
			$Hersteller_ID= $Freifeld{4};
			
			// Der Artikelnummer ermitteln fuer Hersteller, wenn bereits vorhanden
			$cmd = "SELECT id FROM " . "dmc_erp_products" .
		           " WHERE artnr = '$Artikel_Artikelnr' AND maufacturer_erp = '$Hersteller_ID' LIMIT 1";
				
			if (DEBUGGER>=1) fwrite($dateihandle, "2417 dmc_sage_cl_artikelnummern $cmd. -> ");
			
			$sql_query = dmc_db_query($cmd);
		    if ($sql_result = dmc_db_fetch_array($sql_query))
		    {
				// Existiert bereits
				$exists == 1;
			} else {
				// Zuordnung eintragen
				$sql_data_array = array(	
					'artnr' => $Artikel_Artikelnr,
					'artnr_erp' => $Artikel_Artikelnr_ERP,
					'maufacturer_erp' => $Hersteller_ID
				);
				dmc_sql_insert_array('dmc_erp_products', $sql_data_array);
				if (DEBUGGER>=1) fwrite($dateihandle, " $Artikel_Artikelnr_ERP eingetragen.\n");
			} // end if
		} // end exportmodus dmc_sage_cl_artikelnummern

	// Exportmodus csvlangtext - Lantexte > 255 Zeichen aus CSV Dateien 
		if ($ExportModusSpecial=='csvlangtext') {
		
			fwrite($dateihandle, "Export csvlangtext\n");
			// select 'csvlangtext' AS uebertragungsart, p.P_MODEL AS Artikel_Artikelnr, left( p."P_DESC#DE",255) AS Artikel_Text, MID( p."P_DESC#DE",255,510) as Artikel_Text2, MID( p."P_DESC#DE",510,765) AS Artikel_Text3, MID( p."P_DESC#DE",765,1020) as Artikel_Text4, MID( p."P_DESC#DE",1020,1275) as Artikel_Text5, MID( p."P_DESC#DE",1275,1530) as Artikel_Text6, MID( p."P_DESC#DE",1530,1785) as Artikel_Text7, MID( p."P_DESC#DE",1785,2040) as Artikel_Text8, MID( p."P_DESC#DE",2040,2295) as Artikel_Text9, MID( p."P_DESC#DE",2295,2550) as Artikel_Text10 FROM artikel.csv AS p WHERE p.P_MODEL like '%' ORDER BY p.P_MODEL ASC

			$Artikel_Artikelnr = $Freifeld{2};
			$Sprache_id = 2; //$Freifeld{3};
			$Artikel_Text = $Freifeld{3};			
				
			for ($i=4;$i<=12;$i++) {
				$Artikel_Text .=  sonderzeichen2html(false,$_POST["Freifeld{$i}"]);  
			}
			
			$Artikel_Text = str_replace("<font face=¦Arial¦ size=¦2¦ color=¦#ffffff¦>enschafte<br></b></font>","<br>",$Artikel_Text);
			$Artikel_Text = str_replace("<font face=Â¦ArialÂ¦ size=Â¦2Â¦ color=Â¦#ffffffÂ¦>enschafte<br></b></font>","<br>",$Artikel_Text);
			// BUg Abfang Kabel Sterner
			$Artikel_Text = str_replace("00f000","ff0000",$Artikel_Text);
			$Artikel_Text = str_replace("#ff0000","red",$Artikel_Text);
			$Artikel_Text=str_replace('¦', '"', $Artikel_Text);
			
			// Überprüfen, ob Artikel existiert und  die ArtikelID von bestehendem Artikel ermitteln
			$cmd = "select products_id from " . TABLE_PRODUCTS . " where products_model = '$Artikel_Artikelnr'";
					
			$sql_query = dmc_db_query($cmd);
		     	 
		    if ($sql_result = dmc_db_fetch_array($sql_query))
		    {      
				// ArtikelID von bestehendem Artikel ermitteln
				$Artikel_ID = $sql_result['products_id'];
				// Existiert
				$exists = 1;
				fwrite($dateihandle, "Text fuer ArtikelID = $Artikel_ID fuer Sprache $Sprache_id: $Artikel_Text \n");
				
				$sql_data_array = array(
					//'products_name' => $Artikel_Bezeichnung,
					'products_description' => $Artikel_Text
				);		
							
				// Update artikel beschreibung
				//if (dmc_entry_exists(TABLE_PRODUCTS_DESCRIPTION,"products_id",$Artikel_ID,"and","language_id",$Sprache_id)) {
				dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, "products_id ='$Artikel_ID' and language_id = '" .$Sprache_id . "'");	
			} //endif
			
		} // end exportmodus csvlangtext
	
	// Exportmodus csvlangtext2 - Lantexte > 2550 Zeichen aus CSV Dateien empfangen
	// und ergänzen
		if ($ExportModusSpecial=='csvlangtext2') {
		
			fwrite($dateihandle, "Export csvlangtext2\n");
			// select 'csvlangtext2' AS uebertragungsart, p.P_MODEL AS Artikel_Artikelnr, MID( p."P_DESC#DE",2550,2805) AS Artikel_Text, MID( p."P_DESC#DE",2805,3060) as Artikel_Text2, MID( p."P_DESC#DE",3060,3315) AS Artikel_Text3, MID( p."P_DESC#DE",3315,3570) as Artikel_Text4, MID( p."P_DESC#DE",3570,3825) as Artikel_Text5, MID( p."P_DESC#DE",3825,4080) as Artikel_Text6, MID( p."P_DESC#DE",4080,4335) as Artikel_Text7, MID( p."P_DESC#DE",4335,4590) as Artikel_Text8, MID( p."P_DESC#DE",4590,4845) as Artikel_Text9, MID( p."P_DESC#DE",4845,5100) as Artikel_Text10 FROM artikel.csv AS p WHERE MID( p."P_DESC#DE",2550,2805)<>'' AND p.P_MODEL like '%' ORDER BY p.P_MODEL ASC
			//select 'csvlangtext2' AS uebertragungsart, p.P_MODEL AS Artikel_Artikelnr, MID( p."P_DESC#DE",5100,5355) AS Artikel_Text, MID( p."P_DESC#DE",5355,5610) as Artikel_Text2, MID( p."P_DESC#DE",5610,5865) AS Artikel_Text3, MID( p."P_DESC#DE",5865,6120) as Artikel_Text4, MID( p."P_DESC#DE",6120,6375) as Artikel_Text5, MID( p."P_DESC#DE",6375,6630) as Artikel_Text6, MID( p."P_DESC#DE",6630,6885) as Artikel_Text7, MID( p."P_DESC#DE",6885,7140) as Artikel_Text8, MID( p."P_DESC#DE",7140,7395) as Artikel_Text9, MID( p."P_DESC#DE",7395,7650) as Artikel_Text10 FROM artikel.csv AS p WHERE MID( p."P_DESC#DE",5100,5355) <>'' AND p.P_MODEL like '%' ORDER BY p.P_MODEL ASC
			//select 'csvlangtext2' AS uebertragungsart, p.P_MODEL AS Artikel_Artikelnr, MID( p."P_DESC#DE",7650,7905) AS Artikel_Text, MID( p."P_DESC#DE",8160,8415) as Artikel_Text2, MID( p."P_DESC#DE",8415,8670) AS Artikel_Text3, MID( p."P_DESC#DE",8670,8925) as Artikel_Text4, MID( p."P_DESC#DE",8925,9180) as Artikel_Text5, MID( p."P_DESC#DE",9180,9435) as Artikel_Text6, MID( p."P_DESC#DE",9435,9690) as Artikel_Text7, MID( p."P_DESC#DE",9690,9945) as Artikel_Text8, MID( p."P_DESC#DE",9945,10200) as Artikel_Text9, MID( p."P_DESC#DE",7395,7650) as Artikel_Text10 FROM artikel.csv AS p WHERE MID( p."P_DESC#DE",7650,7905)<>'' AND p.P_MODEL like '%' ORDER BY p.P_MODEL ASC
			// ...

			$Artikel_Artikelnr = $Freifeld{2};
			$Sprache_id = 2; //$Freifeld{3};
			$Artikel_Text = $Freifeld{3};			
				
			for ($i=4;$i<=12;$i++) {
				$Artikel_Text .=  sonderzeichen2html(false,$_POST["Freifeld{$i}"]);  
			}
			
			$Artikel_Text = str_replace("<font face=¦Arial¦ size=¦2¦ color=¦#ffffff¦>enschafte<br></b></font>","<br>",$Artikel_Text);
			$Artikel_Text = str_replace("<font face=Â¦ArialÂ¦ size=Â¦2Â¦ color=Â¦#ffffffÂ¦>enschafte<br></b></font>","<br>",$Artikel_Text);
			// BUg Abfang Kabel Sterner
			$Artikel_Text = str_replace("00f000","ff0000",$Artikel_Text);
			$Artikel_Text = str_replace("#ff0000","red",$Artikel_Text);
			$Artikel_Text = str_replace('¦', '"', $Artikel_Text);
			
			// Überprüfen, ob Artikel existiert und  die ArtikelID und bestehenden Text von bestehendem Artikel ermitteln
			$cmd = "select p.products_id, pd.products_description FROM " . TABLE_PRODUCTS . " AS p INNER JOIN ".TABLE_PRODUCTS_DESCRIPTION." AS pd ON p.products_id = pd.products_id WHERE p.products_model = '$Artikel_Artikelnr'";
					
			$sql_query = dmc_db_query($cmd);
		     	 
		    if ($sql_result = dmc_db_fetch_array($sql_query))
		    {      
				// ArtikelID von bestehendem Artikel ermitteln
				$Artikel_ID = $sql_result['products_id'];
				$Artikel_Beschreibung = $sql_result['products_description'];
				
				// Uebergebenen Text ergaenzen
				$Artikel_Text = $Artikel_Beschreibung.$Artikel_Text;
				
				// Existiert
				$exists = 1;
				fwrite($dateihandle, "Text fuer ArtikelID = $Artikel_ID fuer Sprache $Sprache_id: $Artikel_Text ergänzen.\n");
				
				$sql_data_array = array(
					//'products_name' => $Artikel_Bezeichnung,
					'products_description' => $Artikel_Text
				);		
							
				// Update artikel beschreibung
				//if (dmc_entry_exists(TABLE_PRODUCTS_DESCRIPTION,"products_id",$Artikel_ID,"and","language_id",$Sprache_id)) {
				dmc_sql_update_array(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, "products_id ='$Artikel_ID' and language_id = '" .$Sprache_id . "'");	
			} //endif
			
		} // end exportmodus csvlangtext2
			
		// Exportmodus Kundenpreise für individuelle Tabellen
		if ($ExportModusSpecial=='dmc_customer_prices') {
			//  select 'dmc_customer_prices' as ExportModus, ad.EMail as Kunden_EMAIL, p.Artikelnummer as Artikelnummer, p.AuspraegungID as Artikel_Variante, p.Einzelpreis AS Artikel_Preis, p.Rabattsatz as Rabattsatz, '1' as Menge, '0' as Website, '' as customer_discount_group,'' as Freifeld8,'' as Freifeld9,'' as Freifeld10,'' as Freifeld11,'' as Freifeld12 FROM KHKArtikelKunden as p, KHKAdressen as ad, KHKKontokorrent as deb WHERE p.Kunde=deb.Kto AND deb.Adresse=ad.Adresse AND p.Mandant='10' AND ad.EMail IS NOT NULL AND (p.Rabattsatz IS NULL OR p.Rabattsatz=0) AND p.Einzelpreis IS NOT NULL

			// Tabellen:
			/*   PREFIX BEACHTEN   
			CREATE TABLE IF NOT EXISTS `dmc_kundenpreise` (
				    id int(11) NOT NULL AUTO_INCREMENT,
				  customer_id int(11) NOT NULL DEFAULT 0,
				  customer_email varchar(20) NOT NULL DEFAULT '',
				  artnr varchar(20) NOT NULL DEFAULT '',
				  artvarnr varchar(20) NOT NULL DEFAULT '',
				  product_id int(11) NOT NULL DEFAULT 0,
				  abmenge double NOT NULL DEFAULT 1,
				  preis double NOT NULL DEFAULT 0,
				  rabattsatz double NOT NULL DEFAULT 0,
				  store varchar(20) NOT NULL DEFAULT '',
				  customer_discount_group varchar(32) NOT NULL DEFAULT '',
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; */
			
			$customers_email_address = trim($Freifeld{2});				// Store_Name
			$sku = trim($Freifeld{3});
			$var_id = $Freifeld{4}; 
			$price = trim($Freifeld{5});
			if ($price=="") $price=0;
			$discount = $Freifeld{6};
			if ($discount=="") $discount=0;
			$qty = $Freifeld{7};
			if ($qty=='') $qty=1;
			$store_id = $Freifeld{8};
			if ($store_id=='') $store_id=0;
			$customer_discount_group = trim($Freifeld{9});
 			
				// Laufzeit
			$beginn = microtime(true); 
			if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_details - dmc_customer_prices - for customer $customers_email_address and sku $sku -> $price  Laufzeit = ".(microtime(true) - $beginn)."\n");
			
			
			// get  customer ID 
			// $CustomerId =  dmc_get_customer_id('28021973',$customers_email_address);
			$CustomerId =  $customers_email_address;
			$art_id=dmc_get_id_by_artno($sku);	
			if (DEBUGGER) fwrite($dateihandle, "Shop ArtikelID = $art_id und CustomerID=$CustomerId ($customers_email_address) ... ");			
			// Wenn Kunde existiert, Kundenpreis zuordnen
			//if ($art_id<> "") {
			// Kundenrabatt
			if ($customer_discount_group=='') {
				$where="abmenge=".$qty." AND store='".$store_id."' AND customer_email='".$customers_email_address."' AND artnr='".$sku."' AND artvarnr='".$var_id."'";
				if (dmc_entry_exits('id', 'dmc_kundenpreise', $where)) {
					// Update der übergebenen Werte
					if ($price!='' && $discount!='') {
						$query="UPDATE ".DB_TABLE_PREFIX."dmc_kundenpreise SET preis=$price, rabattsatz=$discount WHERE ".$where;
					} else if ($price!='') {
						$query="UPDATE ".DB_TABLE_PREFIX."dmc_kundenpreise SET preis=$price WHERE ".$where;					
					} else {
						$query="UPDATE ".DB_TABLE_PREFIX."dmc_kundenpreise SET rabattsatz=$discount WHERE ".$where;
					}
					dmc_sql_query($query);
					if (DEBUGGER) fwrite($dateihandle, "Preis aktualisiert ...  Laufzeit = ".(microtime(true) - $beginn)."\n");
				} else {
					// Insert 
					dmc_sql_insert("dmc_kundenpreise", 
									"customer_id, customer_email, artnr, product_id, artvarnr, store, abmenge, preis, rabattsatz, customer_discount_group", 
									"'$CustomerId', '$customers_email_address', '$sku', $art_id, '$var_id', '$store_id', $qty, $price, $discount, ''");
					if (DEBUGGER) fwrite($dateihandle, "Preis eingetragen ...  Laufzeit = ".(microtime(true) - $beginn)."\n");
				} // end if else
			} else if ($customer_discount_group!='') {
				// Rabattgruppen-Rabatt
				$where="abmenge=".$qty." AND store='".$store_id."' AND trim(customer_discount_group) ='".$customer_discount_group."' AND artnr='".$sku."' AND artvarnr='".$var_id."'";
				if (dmc_entry_exits('id', 'dmc_kundenpreise', $where)) {
					// Update der übergebenen Werte
					if ($price!='' && $customer_discount_group!='') {
						$query="UPDATE ".DB_TABLE_PREFIX."dmc_kundenpreise SET preis=$price, customer_discount_group=$customer_discount_group WHERE ".$where;
					} else {
						$query="UPDATE ".DB_TABLE_PREFIX."dmc_kundenpreise SET customer_discount_group=$customer_discount_group WHERE ".$where;
					}
					dmc_sql_query($query);
					if (DEBUGGER) fwrite($dateihandle, "Rabattgruppen-Rabatt aktualisiert ...  Laufzeit = ".(microtime(true) - $beginn)."\n");
				} else {
					// Insert 
					dmc_sql_insert("dmc_kundenpreise", 
									"customer_id, customer_email, artnr, product_id, artvarnr, store, abmenge, preis, rabattsatz, customer_discount_group", 
									"'$CustomerId', '$customers_email_address', '$sku', $art_id, '$var_id', '$store_id', $qty, $price, $discount, 
									'$customer_discount_group'");
					if (DEBUGGER) fwrite($dateihandle, "Rabattgruppen-Rabatt eingetragen ...  Laufzeit = ".(microtime(true) - $beginn)."\n");
				} // end if else
			}
			//} //  endif Wenn Artikel existieren
		} // end exportmodus  dmc_customer_prices
	
		// Exportmodus Kundenpreise für individuelle Tabellen
		if ($ExportModusSpecial=='dmc_noegel_product_infos') {
			//  select 'dmc_noegel_product_infos' as ExportModus, isnull("Nr_" as FF2, isnull("Anzahl pro Paket",'') as FF3, isnull("Basiseinheitencode",'') AS FF4, isnull("Preiseinheit",'') as FF5, isnull("Bild Dateiname",'') as FF6, '' as FF7, '' as Freifeld8,'' as Freifeld9,'' as Freifeld10,'' as Freifeld11,'' as Freifeld12 ,'' as Freifeld13 FROM "Artikel"
			// Tabellen: isnull( ,'')
			/*   PREFIX BEACHTEN   
			CREATE TABLE IF NOT EXISTS `dmc_noegel_product_infos` (
				  id int(11) NOT NULL AUTO_INCREMENT,
				  artnr varchar(32) NOT NULL DEFAULT '',
				  anzahl_pro_paket varchar(32) NOT NULL DEFAULT '',
				  basiseinheitencode varchar(32) NOT NULL DEFAULT '',
				  preiseinheit varchar(32) NOT NULL DEFAULT '',
				  bild_dateiname varchar(32) NOT NULL DEFAULT '',
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; */
			
			$artnr = (trim($Freifeld{2}));				// Store_Name
			$anzahl_pro_paket = trim($Freifeld{3});
			$basiseinheitencode = trim($Freifeld{4}); 
			$preiseinheit = trim($Freifeld{5});
			$bild_dateiname = trim($Freifeld{6});
			$artikelrabattgruppe = trim($Freifeld{7});
			// Laufzeit
			$beginn = microtime(true); 
			if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_details - dmc_noegel_product_infos - for art $artnr and image $bild_dateiname -> Anzahl pr Paket $anzahl_pro_paket Laufzeit = ".(microtime(true) - $beginn)."\n");
			//$art_id=dmc_get_id_by_artno($sku);	
			//	if (DEBUGGER) fwrite($dateihandle, "Shop ArtikelID = $art_id  ... ");			
			// Wenn Kunde existiert, Kundenpreis zuordnen
			//if ($art_id<> "") {
			// Kundenrabatt
			if ($artnr!='') {
				$where="artnr='".$artnr."'";
				if (DEBUGGER) fwrite($dateihandle, "Artikel pruefen ...  Laufzeit = ".(microtime(true) - $beginn)."\n");
				if (dmc_entry_exits('artnr', 'dmc_noegel_product_infos', $where)) { 
					// Update der übergebenen Werte
					if ($artikelrabattgruppe!='')
						$query="UPDATE ".DB_TABLE_PREFIX."dmc_noegel_product_infos SET artikelrabattgruppe='$artikelrabattgruppe' WHERE ".$where;
					//else
//						$query="UPDATE ".DB_TABLE_PREFIX."dmc_noegel_product_infos SET rabattsatz=$discount WHERE ".$where;
					dmc_sql_query($query);
					if (DEBUGGER) fwrite($dateihandle, "Artikel aktualisiert ...  Laufzeit = ".(microtime(true) - $beginn)."\n");
				} else {
					// Insert 
					dmc_sql_insert("dmc_noegel_product_infos", 
									"artnr, anzahl_pro_paket, basiseinheitencode, preiseinheit, bild_dateiname, artikelrabattgruppe", 
									"'$artnr', '$anzahl_pro_paket', '$basiseinheitencode', '$preiseinheit', '$bild_dateiname','$artikelrabattgruppe'");
					if (DEBUGGER) fwrite($dateihandle, "Eingetragen ...  Laufzeit = ".(microtime(true) - $beginn)."\n");
				} // end if else
			}
		
		} // end exportmodus  dmc_noegel_product_infos
		
		if ($ExportModusSpecial=='product_discount_group') {
			$customers_email_address = trim($Freifeld{2});				
			$sku = trim($Freifeld{3});
			$var_id = $Freifeld{4}; 
			$price = trim($Freifeld{5});
			if ($price=="") $price=0;
			$discount = $Freifeld{6};
			if ($discount=="") $discount=0;
			$qty = $Freifeld{7};
			if ($qty=='') $qty=1;
			$store_id = $Freifeld{8};
			if ($store_id=='') $store_id=0;
			$customer_discount_group = $Freifeld{9};
 			$kategorie = trim($Freifeld{12});
			// Laufzeit
			$beginn = microtime(true); 
			if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_details - product_discount_group - for customer sku $sku -> $kategorie Laufzeit = ".(microtime(true) - $beginn)."\n");
			
			// get  customer ID 
			// $CustomerId =  dmc_get_customer_id('28021973',$customers_email_address);
			if ($sku!='') {
				$where="artnr='".$sku."'";
				if (dmc_entry_exits('id', 'dmc_kundenpreise', $where)) {
					// Update der übergebenen Werte
					$query="UPDATE ".DB_TABLE_PREFIX."dmc_kundenpreise SET product_discount_group='$kategorie' WHERE ".$where;
					dmc_sql_query($query);
					if (DEBUGGER) fwrite($dateihandle, "product_discount_group aktualisiert ...  Laufzeit = ".(microtime(true) - $beginn)."\n");
				} else {
					// Insert 
					dmc_sql_insert("dmc_kundenpreise", 
									"artnr, product_discount_group", 
									" '$sku', '$kategorie'");
					if (DEBUGGER) fwrite($dateihandle, "product_discount_group eingetragen ...  Laufzeit = ".(microtime(true) - $beginn)."\n");
				} // end if else
			} 
		
			//} //  endif Wenn Artikel existieren
		} // end exportmodus  product_discount_group
		
		// Exportmodus Details für Sage Classic Line
		if ($ExportModusSpecial=='dmc_cl_artikel_details') {
			//  select 'dmc_cl_artikel_details' as ExportModus, p.Artikel AS Artikelnummer, p.Hersteller AS Lieferantennummer, '' AS Amazonnummer, IFNULL(slp.Stuecklistennummer,'') AS Stuecklistennummer, IFNULL(slp.Menge,'') AS anzahl_in_stueckliste, '' as FF7, '' as Freifeld8,'' as Freifeld9,'' as Freifeld10,'' as Freifeld11,'' as Freifeld12 FROM  Artikel AS p LEFT OUTER JOIN stuecklistenposition AS slp ON p.Artikel=slp.Artikelnummer
			/*    
			CREATE TABLE IF NOT EXISTS dmc_cl_artikel_details (
				  id int(11) NOT NULL AUTO_INCREMENT,
				  artnr varchar(32) NULL DEFAULT '',
				  lieferantennummer varchar(32) NULL DEFAULT '',
				  amazonnummer varchar(32) NULL DEFAULT '',
				  stuecklistennummer varchar(32) NULL DEFAULT '',
				  anzahl_in_stueckliste varchar(32) NULL DEFAULT '',
				  PRIMARY KEY (id)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; */
			
			if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_details - dmc_cl_artikel_details - for art $artnr and lieferantennummer $lieferantennummer -> stuecklistennummert $stuecklistennummer Laufzeit = ".(microtime(true) - $beginn)."\n");
			
			$artnr = sonderzeichen2html(true,trim($Freifeld{2}));				// artnr
			$lieferantennummer = trim($Freifeld{3});
			$amazonnummer = (trim($Freifeld{4})); 
			$stuecklistennummer = (trim($Freifeld{5}));
			$anzahl_in_stueckliste = trim($Freifeld{6});
			
			// Laufzeit
			$beginn = microtime(true); 
			if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_details - dmc_cl_artikel_details - for art $artnr and lieferantennummer $lieferantennummer -> stuecklistennummert $stuecklistennummer Laufzeit = ".(microtime(true) - $beginn)."\n");
			if ($artnr!='') {
				$where="artnr='".$artnr."' AND lieferantennummer='".$lieferantennummer."' AND stuecklistennummer='".stuecklistennummer."'";
				if (DEBUGGER) fwrite($dateihandle, "Artikel pruefen ...  Laufzeit = ".(microtime(true) - $beginn)."\n");
				if (dmc_entry_exits('artnr', 'dmc_cl_artikel_details', $where)) { 
					// Update der übergebenen Werte
					$query="UPDATE dmc_cl_artikel_details SET amazonnummer='$amazonnummer', stuecklistennummer='$stuecklistennummer', anzahl_in_stueckliste='$anzahl_in_stueckliste' WHERE ".$where;
					dmc_sql_query($query);
					if (DEBUGGER) fwrite($dateihandle, "Artikel aktualisiert ...  Laufzeit = ".(microtime(true) - $beginn)."\n");
				} else {
					
					// Insert 
					dmc_sql_insert("dmc_cl_artikel_details", 
									"artnr, lieferantennummer, amazonnummer, stuecklistennummer, anzahl_in_stueckliste", 
									"'$artnr', '$lieferantennummer', '$amazonnummer', '$stuecklistennummer', '$anzahl_in_stueckliste'");
					if (DEBUGGER) fwrite($dateihandle, "Eingetragen ...  Laufzeit = ".(microtime(true) - $beginn)."\n");
				} // end if else
			}
		} // end exportmodus  dmc_cl_artikel_details
	
	// Exportmodus staffelpreiseshopware 
		if ($ExportModusSpecial=='staffelpreiseshopware') {
			// select 'staffelpreiseshopware' as ExportModus, Artikel_Artikelnr AS Artikel_Artikelnr, abMenge AS abMenge, 'EK' AS Artikel_Preisgruppe1, Artikel_Preis1 AS Artikel_Preis_Endkunde, 'H' AS Artikel_Preisgruppe1, Artikel_Preis2 AS Artikel_Preis_Haendler, '' as Freifeld8, '' as Freifeld9, '' as Freifeld10, '' as Freifeld11, '' as Freifeld12 FROM ArtikelPreise
			$Artikel_Artikelnr = $Freifeld{2};
			$abMenge = $Freifeld{3};
			$Artikel_Preisgruppe1 = $Freifeld{4};
			$Artikel_Preis1 = $Freifeld{5};
			$Artikel_Preisgruppe2 = $Freifeld{6};
			$Artikel_Preis2 = $Freifeld{7};
			$Artikel_Preisgruppe3 = $Freifeld{8};
			$Artikel_Preis3 = $Freifeld{9};
			
			// Der Artikelnummer zugehörige products_id und xsell_id ermitteln
			// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
			$cmd = "SELECT id, articleID FROM s_articles_details " . 
		            " WHERE ordernumber = '$Artikel_Artikelnr'";
			if (DEBUGGER>=1)  fwrite($dateihandle, "staffelpreiseshopware sql = ".$cmd."\n");
			$sql_query = dmc_db_query($cmd);
		    if ($sql_result = dmc_db_fetch_array($sql_query))
		    {      
				// ArtikelID von bestehendem Artikel ermitteln
				$articledetailsID = $sql_result['id'];
				$Artikel_ID = $sql_result['articleID'];
				// Existiert
				$exists = 1;
			} //endif
			
			
			if ($exists ==1) {	
				if ($Artikel_Preisgruppe1 <>'' && $Artikel_Preis1 >0 ){  
					if (DEBUGGER>=1)  fwrite($dateihandle, "preis fuer $Artikel_Preisgruppe1 ab $abMenge = $Artikel_Preis1 ... ");
					// Uber API wird NUR der übergbene Preis gesetzt, die anderen würden gelöscht.
					/*$sql_update_data_array =  array(
						'mainDetail' => array(
							'prices' => array(
								array(
									'customerGroupKey' => $Artikel_Preisgruppe1,		// Standard Kundengruppe Endkunden
									'price' => $Artikel_Preis1,		// Standard Preis
									'from' => $abMenge,		// Standard Preis
								),
								array(
									'customerGroupKey' => 'H',		// Standard Kundengruppe Haendler
									'price' => $Artikel_Preis2,		// Standard Preis
								),
							) 
						),
					);
					$result=$client->call('articles/' . $Artikel_ID , ApiClient::METHODE_PUT, $sql_update_data_array);
					if ($result=='') {
						fwrite($dateihandle, "# FEHLER: Staffelpreis Update fehlgeschlagen \n");
						$ausgabe = print_r($sql_update_data_array, true);
						fwrite($dateihandle, "# Artikeldaten: $ausgabe \n");
					};*/
					// pricegroup	 from	 to	 articleID	 articledetailsID	 price	 pseudoprice	 baseprice	 percent
					// Pruefen, ob bereits ein Staffelpreis mit geringerer $abMenge existiert 
					// Abgleich aus Wari erfolgt sortiert nach Menge, daher ggfls aktualsieren
						$cmd = "SELECT MAX(`from`) AS abmenge FROM s_articles_prices " . 
								"WHERE articleID = '$Artikel_ID' AND pricegroup='$Artikel_Preisgruppe1' AND `from`<$abMenge";
						$sql_query = dmc_db_query($cmd);
						if ($sql_result = dmc_db_fetch_array($sql_query)) {
							// Der Eintrag mit der kleineren Menge gilt nicht mehr bis "beliebig"
							if (DEBUGGER>=1)  fwrite($dateihandle, "\n");
							$cmd = 	"UPDATE s_articles_prices SET `to` = " .($abMenge-1). 
									" WHERE articleID = '$Artikel_ID' AND pricegroup='$Artikel_Preisgruppe1' AND `from`=".$sql_result['abmenge'];
							$sql_query = dmc_db_query($cmd);
						}
					// Neuen Preis ergaenzen
						$sql_data_price_array = array(
							'pricegroup' => $Artikel_Preisgruppe1, 
					        '`from`' => $abMenge, 
							'`to`' => 'beliebig',
							'articleID' => $Artikel_ID ,
							'articledetailsID' => $articledetailsID,
							'price' => $Artikel_Preis1,
					        'baseprice' => 0,
					        'pseudoprice' => 0,
					        //'percent' => $abMenge
							);
						// Wenn Preis-Zuornung existiert -> update, sonst insert
						$cmd = 	"SELECT price FROM s_articles_prices " . 
								"WHERE articleID = '$Artikel_ID' AND pricegroup='$Artikel_Preisgruppe1' AND `from`='$abMenge'";
						$sql_query = dmc_db_query($cmd);
						if ($sql_result = dmc_db_fetch_array($sql_query) &&  ($Artikel_Preis1>0)) {
							dmc_sql_update_array('s_articles_prices', $sql_data_price_array, "articleID = '$Artikel_ID' AND pricegroup='$Artikel_Preisgruppe1' AND `from`=$abMenge");	
							if (DEBUGGER>=1)  fwrite($dateihandle, "UPDATE erfolgt \n");
						} else if  ($Artikel_Preis1>0) { // nicht existent
							dmc_sql_insert_array('s_articles_prices', $sql_data_price_array);
							if (DEBUGGER>=1)  fwrite($dateihandle, "insert erfolgt \n");
						} 
					// Moeglicherweise veraltete Preise mit Megne groesser als altuelle loeschen,
					// Abgleich aus WaWi erfolgt sortiert nach Menge, d.h. es kommt ggfls noch eine ...
					$cmd = "DELETE FROM s_articles_prices " . 
								"WHERE articleID = '$Artikel_ID' AND pricegroup='$Artikel_Preisgruppe1' AND `from`>$abMenge";
					$sql_query = dmc_db_query($cmd);
				}
				if ($Artikel_Preisgruppe2 <>'' && $Artikel_Preis2 >0 ){  
					if (DEBUGGER>=1)  fwrite($dateihandle, "preis fuer $Artikel_Preisgruppe2 ab $abMenge = $Artikel_Preis2 ... ");
					
					// Pruefen, ob bereits ein Staffelpreis mit geringerer $abMenge existiert 
					// Abgleich aus Wari erfolgt sortiert nach Menge, daher ggfls aktualsieren
						$cmd = "SELECT MAX(`from`) AS abmenge FROM s_articles_prices " . 
								"WHERE articleID = '$Artikel_ID' AND pricegroup='$Artikel_Preisgruppe2' AND `from`<$abMenge";
						$sql_query = dmc_db_query($cmd);
						if ($sql_result = dmc_db_fetch_array($sql_query)) {
							// Der Eintrag mit der kleineren Menge gilt nicht mehr bis "beliebig"
							if (DEBUGGER>=1)  fwrite($dateihandle, "\n");
							$cmd = 	"UPDATE s_articles_prices SET `to` = " .($abMenge-1). 
									" WHERE articleID = '$Artikel_ID' AND pricegroup='$Artikel_Preisgruppe2' AND `from`=".$sql_result['abmenge'];
							$sql_query = dmc_db_query($cmd);
						}
					// Neuen Preis ergaenzen
						$sql_data_price_array = array(
							'pricegroup' => $Artikel_Preisgruppe2, 
					        '`from`' => $abMenge,
							'`to`' => 'beliebig',
							'articleID' => $Artikel_ID ,
							'articledetailsID' => $articledetailsID,
							'price' => $Artikel_Preis2,
					        'baseprice' => 0,
					        'pseudoprice' => 0,
					        //'percent' => $abMenge
							);
						// Wenn Preis-Zuornung existiert -> update, sonst insert
						$cmd = 	"SELECT price FROM s_articles_prices " . 
								"WHERE articleID = '$Artikel_ID' AND pricegroup='$Artikel_Preisgruppe2' AND `from`='$abMenge'";
						$sql_query = dmc_db_query($cmd);
						if ($sql_result = dmc_db_fetch_array($sql_query) &&  ($Artikel_Preis2>0)) {
							dmc_sql_update_array('s_articles_prices', $sql_data_price_array, "articleID = '$Artikel_ID' AND pricegroup='$Artikel_Preisgruppe2' AND `from`=$abMenge");	
							if (DEBUGGER>=1)  fwrite($dateihandle, "UPDATE erfolgt \n");
						} else if  ($Artikel_Preis2>0) { // nicht existent
							dmc_sql_insert_array('s_articles_prices', $sql_data_price_array);
							if (DEBUGGER>=1)  fwrite($dateihandle, "insert erfolgt \n");
						} 
					// Moeglicherweise veraltete Preise mit Megne groesser als altuelle loeschen,
					// Abgleich aus WaWi erfolgt sortiert nach Menge, d.h. es kommt ggfls noch eine ...
					$cmd = "DELETE FROM s_articles_prices " . 
								"WHERE articleID = '$Artikel_ID' AND pricegroup='$Artikel_Preisgruppe2' AND `from`>$abMenge";
					$sql_query = dmc_db_query($cmd);
				}
				if (DEBUGGER>=1) fwrite($dateihandle, "Detail Zuordnung eingetragen.\n");
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Detail nicht eingetragen, da  Artikel nicht existent.\n");
			} //  endif Wenn Artikel existieren
			
		} // end exportmodus staffelpreiseshopware
		
		// Alle Shopware Aritkel loeschen
		if ($ExportModusSpecial=='deleteallshopwareproducts') {
				fwrite($dateihandle, "deleteallshopwareproducts.\n");
				$cmd = "select id from s_articles where Datum >= '2015-11-12';";
					// Kategorie-Zuordnung löschen, wenn bereits existent
				fwrite($dateihandle, "$cmd.\n");
					$desc_query = dmc_db_query($cmd);
					while ($desc = dmc_db_fetch_array($desc_query))
					{
							
							$art_id = $desc['id'];
							fwrite($dateihandle, "art_id=$art_id\n");
						//	$client->delete('articles/'.$art_id);
							$client->call('articles/'.$art_id, ApiClient::METHODE_DELETE);
					}
					 	
		} // end exportmodus deleteallshopwareproducts
		
		if ($ExportModusSpecial=='ineedyoufiltergenerator') {
				fwrite($dateihandle, "ineedyoufiltergenerator.\n");
				$cmd = "select id, articleID, ordernumber, additionaltext FROM s_articles_details where articleID in (SELECT id FROM s_articles WHERE configurator_set_id IS NOT NULL) AND position > 0 ORDER BY  articleID, `ordernumber`, position ASC";
				// Alle Variantenunterartikel auswaehlen
				fwrite($dateihandle, "$cmd.\n");
				$main_art_id = '';
				$query = dmc_db_query($cmd);
				while ($ergebnis = dmc_db_fetch_array($query))
				{
						fwrite($dateihandle, "ineedyoufiltergenerator 2901 ".$ergebnis['ordernumber']." additionaltext-> ".$ergebnis['additionaltext']."\n");
							if ($main_art_id <> $ergebnis['articleID']) {
								// Bei erstem Artikel, (noch) keinen Filter setzen, nur wenn neuer Artikel
								fwrite($dateihandle, "ineedyoufiltergenerator 2904 \n");
								if ($main_art_id<>'') {
									// Filter setzen mit bereits ermittelten Werten
									
									$filterArray = array(
										'filterGroupId' => 3,				// ID 3 entnommen aus `s_filter` fuer Lesebrillen
										'propertyValues' => array(
												array(
													'option' => array('name' => "Dioptrien Min"),					//  s_filter_options
													'value' => $min_dioptrin
												),
												array(
													'option' => array('name' => "Dioptrien Max"),					// s_filter_options
													'value' => $max_dioptrin
												),
												array(
													'option' => array('name' => "Dioptrien Steps"),					// s_filter_options
													'value' => $step
												),
										)
									 
									);
									$result=$client->call('articles/'.$main_art_id, ApiClient::METHODE_PUT, $filterArray);
									if ($result!='') {
										$ausgeben=print_r($result, true);
										fwrite($dateihandle, " Filter angelegt fuer main_art_id $main_art_id angelegt  mit folgendem Ergebnis = $ausgeben  \n"); 
									} else {
										$ausgeben=print_r($filterArray, true);
										fwrite($dateihandle, " FEHLER: Filter main_art_id $main_art_id konnte nicht angelegt werden mit folgenden Inhalt = $ausgeben \n"); 
									}

								}
								// neuer Artikel 
								$main_art_id = $ergebnis['articleID'];
								$art_id = $ergebnis['id'];
								$min_dioptrin=$dioptrinwert=$ergebnis['additionaltext'];
								$step=0;
							} else {
								// fwrite($dateihandle, "ineedyoufiltergenerator 2942 \n");
								// weiterer Unterartikel
								$steptemp=abs($ergebnis['additionaltext']-$dioptrinwert);
								if ($step==0) {
									$step=$steptemp;		// Uebernahme, wenn Step noch nicht gesetzt
									//$min_dioptrin=$dioptrinwert;
									// Sonderfunktion, setze Ordernumer des ersten Artikels auf den Hauptartikel (kind=1)
									//$query="UPDATE s_articles_details SET ordernumber='".$ergebnis['ordernumber']."' WHERE kind=1 AND articleID =".$main_art_id;
									//dmc_sql_query($query);
								} else if ($step>$steptemp) {
									$step=$steptemp; 		// Uebernahme, wenn Step bisher groesser war
								}
								$max_dioptrin=$ergebnis['additionaltext'];	// Da Ubergabe aus OL soriert nach Dioptrin aufsteigend
							}
				}
				fwrite($dateihandle, "ineedyoufiltergenerator beendet \n");	 	
		 } // end exportmodus ineedyoufiltergenerator
		 
		 // Exportmodus pdfshopware
		if ($ExportModusSpecial=='pdfshopware') {
			// Standard, zB: select 'pdfshopware' as exportmodus, RTRIM(p.F066_01) as Artikel_Artikelnr, RTRIM(p.F066_146) as PDF_Datei, '../media/pdf/' as PDF_Verzeichnis, '' as Link, '' as Link_Feld, '' as Link_Feld, '' as ff8, '' as ff9, '' as ff10, '' as ff11, RTRIM(p.F066_146) as PDF_Dateiname FROM M010_T066 AS p WHERE p.[F066_01] LIKE '$variable1%' AND p.[F066_142] = 1 AND p.[F066_146] IS NOT NULL AND p.[F066_146] <> ''
			// Nur Upload, zB: select 'pdfshopware' as exportmodus, 'UPLOAD' as Artikel_Artikelnr, '' as FF3, '../media/pdf/' as PDF_Verzeichnis, '' as ff5, '' as ff6, '' as ff7, '' as ff8, '' as ff9, '' as ff10, '' as ff11, RTRIM(p.F066_146) as PDF_Dateiname FROM M010_T066 AS p WHERE p.[F066_01] LIKE '$variable1%' AND p.[F066_142] = 1 AND p.[F066_146] IS NOT NULL AND p.[F066_146] <> ''
			// Attributszuordnung 4-9, zB: select 'pdfshopware' as exportmodus, RTRIM(p.F066_01) as Artikel_Artikelnr, RTRIM(p.F066_146) as PDF_Datei, '../media/pdf/' as PDF_Verzeichnis, '' as Link, '' as Link_Feld, RTRIM(p.F066_146) as PDF_Dateiname2, RTRIM(p.F066_136) as PDF_Dateiname2, RTRIM(p.F066_147) as PDF_Dateiname3, RTRIM(p.F066_137) as PDF_Dateiname4, RTRIM(p.F066_148) as PDF_Dateiname5, RTRIM(p.F066_138) as PDF_Dateiname6 FROM M010_T066 AS p WHERE p.[F066_01] LIKE '$variable1%' AND p.[F066_142] = 1 AND (p.[F066_146] <> '' or p.[F066_147] <> '' or p.[F066_148] <> '' OR p.[F066_136] <> '' or p.[F066_137] <> '' or p.[F066_138] <> '')
			// SELECTLINE select 'pdfshopware' as exportmodus, RTRIM(p.Artikelnummer) as Artikel_Artikelnr, 'Datenblatt (PDF)' as PDF_Datei_Bezeichnung, 'media/pdf/' as PDF_Verzeichnis, '' as Link, '' as Link_Feld, '' as Link_Feld, '' as ff8, '' as ff9, '' as ff10, '' as ff11, '.\pdfs\'+p.Artikelnummer+'.pdf' as PDF_Dateiname FROM Art AS p WHERE p.Shopaktiv=1 AND p.Artikelnummer LIKE '$variable1%' 
			$Artikel_Artikelnr = $Freifeld{2};	// WENN = UPLOAD, dann reiner Dateiupload ohne Zuordnung
			$PDF_Dateibezeichnung = $Freifeld{3};
				if ($PDF_Dateibezeichnung == '') $PDF_Dateibezeichnung = 'Download (pdf)';
			$PDF_Verzeichnis = $Freifeld{4};
			if ($PDF_Verzeichnis=='')
				$PDF_Verzeichnis = 'media/pdf/';
			$Link = $Freifeld{5};
			$PDF_Dateiname = $Freifeld{12};
			// wenn kompletter Dateipfad uebergeben, nur Datei selectieren
			$pdfDatei{7} = $Freifeld{7};
			$pos = strrpos($pdfDatei{7}, '\\');
			if ($pos !== false) {
				$pdfDatei{7} = substr($Freifeld{7},($pos+1));  
			}
			$pdfDatei{8} = $Freifeld{8};
			$pos = strrpos($pdfDatei{8}, '\\');
			if ($pos !== false) {
				$pdfDatei{8} = substr($Freifeld{8},($pos+1));  
			}
			$pdfDatei{9} = $Freifeld{9};
			$pos = strrpos($pdfDatei{9}, '\\');
			if ($pos !== false) {
				$pdfDatei{9} = substr($Freifeld{9},($pos+1));  
			}
			$pdfDatei{10} = $Freifeld{10};
			$pos = strrpos($pdfDatei{10}, '\\');
			if ($pos !== false) {
				$pdfDatei{10} = substr($Freifeld{10},($pos+1));  
			}
			$pdfDatei{11} = $Freifeld{11};
			$pos = strrpos($pdfDatei{11}, '\\');
			if ($pos !== false) {
				$pdfDatei{11} = substr($Freifeld{11},($pos+1));  
			}
			$pdfDatei{12} = $Freifeld{12};
			$pos = strrpos($pdfDatei{12}, '\\');
			if ($pos !== false) {
				$pdfDatei{12} = substr($Freifeld{12},($pos+1));  
			}
			$Artikel_ID=dmc_get_id_by_artno($Artikel_Artikelnr);
			if ($Artikel_ID=="") $exists=0; else $exists=1;
			if ($exists == 1) { 
				fwrite($dateihandle, "*** pdfshopware UPLOAD *** ");
				// Datei in korrektes Verzeichnis unter Code kopieren
			/*	if ($PDF_Verzeichnis!='')
					copy ('./upload_images/'.$pdfDatei1,$PDF_Verzeichnis.$pdfDatei1); */
				fwrite($dateihandle, "-> copy (./upload_images/".$PDF_Dateiname.", $PDF_Verzeichnis".$PDF_Dateiname." ) ");	 
				if (file_exists('./upload_images/'.$PDF_Dateiname)) {
					copy ('./upload_images/'.$PDF_Dateiname, $PDF_Verzeichnis.$PDF_Dateiname); 
					fwrite($dateihandle, " -> done \n");	
				} else { 
					fwrite($dateihandle, "\n -> (P) *** Datei ".$PDF_Dateiname." nicht existent *** \n");
				}
		
				fwrite($dateihandle, "*** pdfshopware *** ");
				$groesse=filesize('../'.$PDF_Verzeichnis.$PDF_Datei);
				$sql_update_data_array['downloads'][0]['name'] = $PDF_Dateibezeichnung;	
				$sql_update_data_array['downloads'][0]['file'] = $PDF_Verzeichnis.$PDF_Datei;
				$sql_update_data_array['downloads'][0]['size'] = $groesse;
					
				$result=$client->call('articles/' . $Artikel_ID , ApiClient::METHODE_PUT, $sql_update_data_array);
				if (DEBUGGER>=1) fwrite($dateihandle, "PDF $PDF_Datei ($Bezeichnung) Zuordnungen zu $Artikel_ID  eingetragen.\n");
				
				 //  endif wenn Artikel existiert		
			} else { 
					if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Detail nicht eingetragen, da  Artikel nicht existent.\n");
			}
			
		} // end exportmodus pdfshopware
		
		// Exportmodus 	 dmc_lang_shopware - Fremdsprachen zum Artikel für Shopware
		if ($ExportModusSpecial=='dmc_lang_shopware') {
			// Beispiel selectline: select 'dmc_lang_shopware' AS uebertragungsart,  ab.Artikelnummer AS Artikel_Artikelnr, 'es' AS SprachID, CASE WHEN (ISNULL(ab.Bezeichnung,p.Bezeichnung)='') THEN p.Bezeichnung ELSE ISNULL(ab.Bezeichnung,p.Bezeichnung) END AS Artikel_Bezeichnung, ISNULL(ab.Langtext,'') AS Artikel_Text, ISNULL(ab.Zusatz,'') AS Artikel_Kurztext, '' AS Meta_Title, '' AS Meta_Desc, '' AS Meta_Keyw, 'c'+p.Artikelgruppe AS Kategorie_id, '' AS FF11, '' AS FF12 FROM ART as p INNER JOIN ARTBEZ as ab ON (p.Artikelnummer = ab.Artikelnummer AND ab.Sprache='1') WHERE p.ShopAktiv = 'true'
			// select 'dmc_lang_shopware' AS ExportModusSpecial, RTRIM(p.[F066_01]) AS Artikel_Artikelnr, '3' AS SprachID, RTRIM(p.[F066_08])+' '+ISNULL(RTRIM(p.[F066_09]),'') AS Artikel_Bezeichnung, ISNULL(p.F066_53,'') AS Artikel_Text, '' AS Artikel_Kurztext, '' AS Meta_Title, '' AS Meta_Desc, '' AS Meta_Keyw, 'EC'+RTRIM(p.F066_18)+'@ES'+RTRIM(p.F066_18)+'@EF'+RTRIM(p.F066_18)+'@EI'+RTRIM(p.F066_18)+'@EZ'+RTRIM(p.F066_18)+'@EO'+RTRIM(p.F066_18) AS Kategorie_id, '' AS FF11, '' AS FF12 FROM M010_T066 as p WHERE p.[F066_01] LIKE '%' AND p.[F066_142] = 1 AND RTRIM(p.[F066_08]) IS NOT NULL AND RTRIM(p.[F066_08]) <> ''
 
			fwrite($dateihandle, "Export Sprachen Shopware\n");
			$Artikel_Artikelnr = $Freifeld{2};
			$Sprache_id = $Freifeld{3};
			$Artikel_Bezeichnung = sonderzeichen2html(true,$Freifeld{4});
			$Artikel_Text = sonderzeichen2html(true,$Freifeld{5});
			$Artikel_Kurztext =sonderzeichen2html(true,$Freifeld{6});
			$Kategorie_ID =sonderzeichen2html(true,$Freifeld{10});
			fwrite($dateihandle, "Artikel_Artikelnr = $Artikel_Artikelnr\n");
			
			// Der Artikelnummer zugehörige products_id und xsell_id ermitteln
			// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
			$exists = 1;
			$Artikel_ID=dmc_get_id_by_artno($Artikel_Artikelnr);
			if ($Artikel_ID == "") $exists = 0;		
			// Wenn Artikel existiert, Details zuordnen 
			if ($exists == 1) {
					// Details eintragen : 
					if (DEBUGGER>=1) fwrite($dateihandle, "3102\n");

					// englisch = 3, franz = 2
					$uebersetzung = array(
							'key' => $Artikel_ID,            #  s_articles.id
							'type' => 'article',
							'shopId' => $Sprache_id,         # s_core_shops.id
							'metaTitle' => $Artikel_Bezeichnung,
							'data' => array(
								'name' => $Artikel_Bezeichnung,
								//	'description' => $Artikel_MetaDescription,
								'descriptionLong' => $Artikel_Text,
							//	'additionalText' => $Artikel_Kurztext,
								// 'keywords' => $Artikel_MetaKeyword,
								// 'packUnit' => $vpe,
							)
						);  
					$result=$client->post('translations', $uebersetzung);
					//if (DEBUGGER>=1) fwrite($dateihandle, "Sprache für Shop $Sprache_id eingetragen mit ArtBez $Artikel_Bezeichnung.\n");				
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Sprache nicht eingetragen, da  Artikel nicht existent.\n");
			} //  endif Wenn Artikel existieren
			// Fremdspachenkategorien zuordnen
			if ($Kategorie_ID != '') {
				if (is_file('userfunctions/products/dmc_generate_cat_id.php')) include ('userfunctions/products/dmc_generate_cat_id.php');
				else include ('functions/products/dmc_generate_cat_id.php');
				for ( $i = 0; $i < count ( $Kategorie_IDs ); $i++ ) {
						// KategorieIds ergaenzen
						if ($Kategorie_IDs[$i] !="" && $Kategorie_IDs[$i] !="0") {
							fwrite($dateihandle, " KategorieIds ergaenzen ... ".$Kategorie_IDs[$i]); 
							$sql_cat_update_data_array['categories'][$i]['id'] = $Kategorie_IDs[$i];			// !!! $Kategorie_ID wird geprueft
						}
				}	
				$result=$client->call('articles/' . $Artikel_ID , ApiClient::METHODE_PUT, $sql_cat_update_data_array);
				$ausgeben = print_r($result, true); 
				fwrite($dateihandle, "\n AKTUALISIERUNG Kategorien > ".$result); 
			}
		
		} // end dmc_lang_shopware languages
		
			// Exportmodus 	 dmc_lang_shopware_db - Fremdsprachen zum Artikel f¸r Shopware auf DATENBANK
		if ($ExportModusSpecial=='dmc_lang_shopware_db') {
			// Beispiel SOL: select 'dmc_lang_shopware' AS uebertragungsart, p.Artikelnummer AS Artikel_Artikelnr, 'E' AS SprachID, p.Bezeichnung1 AS Artikel_Bezeichnung, '' AS Artikel_Text, '' AS Artikel_Kurztext, '' AS Meta_Title, '' AS Meta_Desc, '' AS Meta_Keyw, '' AS Kategorie_id, '' AS FF11, '' AS FF12 FROM KHKArtikelBezeichnung as p WHERE p.Artikelnummer LIKE '$variable1%' AND p.Sprache='E' AND p.Artikelnummer IN (SELECT Artikelnummer from KHK_Artikel WHERE USER_Shopanzeiger=-1 AND USER_Shopkategorie IS NOT NULL)
			
			fwrite($dateihandle, "Export Sprachen Shopware DB\n");
			$Artikel_Artikelnr = $Freifeld{2};
			$Sprache_id = $Freifeld{3};
			$Artikel_BezeichnungORG=$Artikel_Bezeichnung = str_replace("'","\'",sonderzeichen2html(true,$Freifeld{4}));
			$Artikel_Text = str_replace("'","\'",sonderzeichen2html(true,$Freifeld{5}));
			$Artikel_Kurztext =str_replace("'","\'",sonderzeichen2html(true,$Freifeld{6}));
			$Kategorie_ID =sonderzeichen2html(true,$Freifeld{10});
			$attribut1 =sonderzeichen2html(true,$Freifeld{11});
		
			fwrite($dateihandle, "Artikel_Artikelnr = $Artikel_Artikelnr\n");
			// Uebersetzung, wenn vorhanden, auch fuer Hauptartikel erforderlich
			$Artikel_Variante_Von='';
			
			// Der Artikelnummer zugehoerige products_id und xsell_id ermitteln
			// ‹berpr¸fen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
			$exists = 1;
			$Artikel_ID=dmc_get_id_by_artno($Artikel_Artikelnr);
			fwrite($dateihandle, "Artikel_ID = $Artikel_ID in Sprache $Sprache_id\n");
			if ($Artikel_Variante_Von!='') {
				$HauptArtikel_ID=dmc_get_id_by_artno($Artikel_Variante_Von);
				fwrite($dateihandle, "HauptArtikel_ID = $HauptArtikel_ID in Sprache $Sprache_id\n");
			}
			if ($Artikel_ID == "") $exists = 0;		
			// Wenn Artikel existiert, Details zuordnen 
			if ($exists == 1) {
					if (DEBUGGER>=1) fwrite($dateihandle, "DB s_core_translations Sprache fuer Shop $Sprache_id eingetragen mit ArtBez $Artikel_Bezeichnung.\n");		
					// articleID	 languageID	 name	 keywords   [BB]	 description   [BB]	 description_long   [BB]	 description_clear   [BB]	 attr1	 attr2	 attr3	 attr4	 attr5
					// Wenn KEINE Variante
					if ($Artikel_Variante_Von=='') {
						$objectdata_array = array(
						'metaTitle' => $Artikel_Bezeichnung, 
						'attr1' => $attribut1,
					    'txtArtikel' => $Artikel_Bezeichnung, 
					    'txtlangbeschreibung' => $Artikel_Text,
					    'txtshortdescription' => $Artikel_Text,
					    'txtkeywords' => $Artikel_Bezeichnung,
					 	);
				 		$objectdata = serialize($objectdata_array);
						$s_articles_translations_array = array(
							'objecttype' => 'article',  
				   	     	'objectkey' => $Artikel_ID, 					        
				        	'objectdata' => $objectdata, 
						    'objectlanguage' => $Sprache_id,
					 	);
						
						// Wenn -Zuordnung existiert -> update, sonst insert
						$cmd = 	"SELECT id, objectdata FROM s_core_translations " . 
								"WHERE objectkey = '$Artikel_ID' AND objectlanguage='$Sprache_id' AND objecttype='article' LIMIT 1";
						$sql_query = dmc_db_query($cmd);
						if ($sql_result = dmc_db_fetch_array($sql_query)) {
							$objectdata_array = unserialize($sql_result['objectdata']);
							$objectdata_array['txtlangbeschreibung'] = str_replace("'","&apos;",$objectdata_array['txtlangbeschreibung']);
							$objectdata_array['attr1'] =$attribut1;
							$objectdata_array['txtArtikel'] =$Artikel_Bezeichnung;
							$objectdata_array['txtshortdescription'] = $Artikel_Text;
							$objectdata_array['txtkeywords'] = $Artikel_Bezeichnung;
				 			$objectdata = serialize($objectdata_array);
							$s_articles_translations_array_update = array(
					 	       'objectdata' => $objectdata, 
					 	  	 //    'description_long' => $Artikel_Text,
							);
							// ABFANGROUTINE, nur machen, wenn Text nicht gelöscht:
							if ($objectdata_array['txtlangbeschreibung']!='') 
								dmc_sql_update_array('s_core_translations', $s_articles_translations_array_update, "id = ".$sql_result['id']." ");	
							if (DEBUGGER>=1)  fwrite($dateihandle, "UPDATE erfolgt \n");
						} else { // nicht existent
							dmc_sql_insert_array('s_core_translations', $s_articles_translations_array);
							if (DEBUGGER>=1)  fwrite($dateihandle, "insert erfolgt \n");
						} 
					 }
						if ($Artikel_Variante_Von!='') {
							// Hauptartikel
							$Artikel_Bezeichnung_haupt=$Artikel_Bezeichnung;
							$Artikel_Text_haupt=$Artikel_Text;
							$objectdata_array = array(
								'metaTitle' => $Artikel_Bezeichnung_haupt, 
								'attr1' => $attribut1,
					   		    'txtArtikel' => $Artikel_Bezeichnung_haupt, 
							    'txtlangbeschreibung' => $Artikel_Text_haupt,
							    'txtshortdescription' => $Artikel_Text_haupt,
							    'txtkeywords' => $Artikel_Bezeichnung_haupt,
						 	);
						 	$objectdata = serialize($objectdata_array);
							$s_articles_translations_array = array(
								'objecttype' => 'article',  
						        'objectkey' => $HauptArtikel_ID, 					        
						        'objectdata' => $objectdata, 
							    'objectlanguage' => $Sprache_id,
						 	);
						
							//$s_articles_translations_array['articleID'] = $HauptArtikel_ID;
							// Wenn Preis-Zuornung existiert -> update, sonst insert
							$cmd = 	"SELECT id, objectdata FROM s_core_translations " . 
									"WHERE objectkey = '$HauptArtikel_ID' AND objectlanguage='$Sprache_id' AND objecttype='article' LIMIT 1";
								$sql_query = dmc_db_query($cmd);
							if ($sql_result = dmc_db_fetch_array($sql_query)) {
								$objectdata_array = unserialize($sql_result['objectdata']);
								if (DEBUGGER>=1)  fwrite($dateihandle, "txtlangbeschreibung ALT = ".$objectdata_array['txtlangbeschreibung']." \n");
								$objectdata_array['txtlangbeschreibung'] = str_replace("'","&apos;",$objectdata_array['txtlangbeschreibung']);
								if (DEBUGGER>=1)  fwrite($dateihandle, "txtlangbeschreibung neu = '".$objectdata_array['txtlangbeschreibung']."' \n");
								$objectdata_array['attr1'] =$attribut1;
								$objectdata_array['txtArtikel'] =$Artikel_Bezeichnung_haupt;
								$objectdata_array['txtshortdescription'] = $Artikel_Text_haupt;
								$objectdata_array['txtkeywords'] = $Artikel_Bezeichnung_haupt;
				 				$objectdata = serialize($objectdata_array);
								$s_articles_translations_array_update = array(
					 		       'objectdata' => $objectdata, 
						 	  	 //    'description_long' => $Artikel_Text,
								);
								// ABFANGROUTINE, nur machen, wenn Text nicht gelöscht:
								if ($objectdata_array['txtlangbeschreibung']!='') 
										dmc_sql_update_array('s_core_translations', $s_articles_translations_array_update, "id = ".$sql_result['id']." ");	
								if (DEBUGGER>=1)  fwrite($dateihandle, "UPDATE erfolgt \n");
							} else { // nicht existent
								dmc_sql_insert_array('s_core_translations', $s_articles_translations_array);
								if (DEBUGGER>=1)  fwrite($dateihandle, "insert erfolgt \n");
							}
						} 
			
					if (DEBUGGER>=1) fwrite($dateihandle, "DB s_articles_translations Sprache f¸r Shop $Sprache_id eingetragen mit ArtBez $Artikel_Bezeichnung.\n");		
					// articleID	 languageID	 name	 keywords   [BB]	 description   [BB]	 description_long   [BB]	 description_clear   [BB]	 attr1	 attr2	 attr3	 attr4	 attr5
					// Wenn KEINE Variante
					if ($Artikel_Variante_Von=='') {
						$s_articles_translations_array = array(
							'articleID' => $Artikel_ID, 
					    	'attr1' => $attribut1,
					       	'name' => $Artikel_Bezeichnung, 
					        'languageID' => $Sprache_id,
					        'description_long' => $Artikel_Text,
					        'description' => $Artikel_Text,
					        'keywords' => $Artikel_Bezeichnung,
					 	);
						$s_articles_translations_array_update = array(
							'name' => $Artikel_Bezeichnung, 
							'attr1' => $attribut1,
					   		'description' => $Artikel_Text,
					        'keywords' => $Artikel_Bezeichnung,
					    //    'description_long' => $Artikel_Text,
						);
						// Wenn Preis-Zuornung existiert -> update, sonst insert
						$cmd = 	"SELECT id FROM s_articles_translations " . 
								"WHERE articleID = '$Artikel_ID' AND languageID='$Sprache_id' LIMIT 1";
						$sql_query = dmc_db_query($cmd);
						if ($sql_result = dmc_db_fetch_array($sql_query)) {
							dmc_sql_update_array('s_articles_translations', $s_articles_translations_array_update, "id = ".$sql_result['id']." ");	
							if (DEBUGGER>=1)  fwrite($dateihandle, "UPDATE erfolgt \n");
						} else { // nicht existent
							dmc_sql_insert_array('s_articles_translations', $s_articles_translations_array);
							if (DEBUGGER>=1)  fwrite($dateihandle, "insert erfolgt \n");
						} 
					} // if ($Artikel_Variante_Von=='') 
						if ($Artikel_Variante_Von!='') {
							// Hauptartikel
							$Artikel_Bezeichnung_haupt=$Artikel_Bezeichnung;
							$Artikel_Text_haupt=$Artikel_Text;
							$s_articles_translations_array = array(
								'articleID' => $HauptArtikel_ID, 
						        'name' => $Artikel_Bezeichnung_haupt, 
						        'languageID' => $Sprache_id,
						        'description_long' => $Artikel_Text_haupt,
						        'description' => $Artikel_Text_haupt,
						        'keywords' => $Artikel_Bezeichnung_haupt,
				        		'attr1' => $attribut1,
						 	);
							$s_articles_translations_array_update = array(
								'articleID' => $HauptArtikel_ID, 
								'name' => $Artikel_Bezeichnung_haupt, 
								'description' => $Artikel_Text_haupt,
						        'keywords' => $Artikel_Bezeichnung_haupt,
					    		'attr1' => $attribut1,
					   		 //    'description_long' => $Artikel_Text,
							);
							// Wenn Preis-Zuornung existiert -> update, sonst insert
							$cmd = 	"SELECT id FROM s_articles_translations " . 
									"WHERE articleID = '$HauptArtikel_ID' AND languageID='$Sprache_id' LIMIT 1";
							$sql_query = dmc_db_query($cmd);
							if ($sql_result = dmc_db_fetch_array($sql_query)) {
								dmc_sql_update_array('s_articles_translations', $s_articles_translations_array_update, "id = ".$sql_result['id']." ");	
								if (DEBUGGER>=1)  fwrite($dateihandle, "UPDATE erfolgt \n");
							} else { // nicht existent
								dmc_sql_insert_array('s_articles_translations', $s_articles_translations_array);
								if (DEBUGGER>=1)  fwrite($dateihandle, "insert erfolgt \n");
							}
						} 
					
				
					if (DEBUGGER>=1) fwrite($dateihandle, "DB Sprache fuer Shop $Sprache_id eingetragen mit ArtBez $Artikel_Bezeichnung.\n");		
				/*	$ausgabe = print_r($result, true);
					fwrite($dateihandle, "# result Uebersetzung 3030: $ausgabe \n");
					$ausgabe = print_r($uebersetzung, true);
					fwrite($dateihandle, "# uebersetzung  array 3032: $ausgabe \n"); */
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Sprache nicht eingetragen, da  Artikel nicht existent.\n");
			} //  endif Wenn Artikel existieren
			
		} // end dmc_lang_shopware languages
		
		
		// Exportmodus Kundenpreise als Kundengruppenpreis fuer xtc, Gambio etc
		if ($ExportModusSpecial=='dmc_xtc_kundenpreise') {
			//  select 'dmc_xtc_kundenpreise' as ExportModus, Art.Artikelnummer AS Artikel_Artikelnr, '1' AS Preis_Ab_Menge, CASE WHEN dbo.SL_fnPreis(art.Artikelnummer, GETDATE(), 'EUR', Preisgruppe, 0, 100) = 0 THEN dbo.SL_fnListenPreis(art.Artikelnummer, GETDATE(), 'EUR', 0, 100) ELSE dbo.SL_fnPreis(art.Artikelnummer, GETDATE(), 'EUR', Preisgruppe, 0, 100) END AS Kunden_Preis, Kunden.Sonderrabatt AS Kunden_Sonderrabatt, dbo.SL_fnListenPreis(art.Artikelnummer, GETDATE(), 'EUR', 0, 100) as Listenpreis, Nummer as Kundennummer,EMAIL as Kunden_email, '' as Freifeld8, '' as Freifeld9,'' as Freifeld10,'' as Freifeld11,'' as Freifeld12 from Kunden, Art where Art.ShopAktiv = 1 and (Nummer + Art.Artikelnummer) not in (select Nummer + Art.Artikelnummer as ID from Kunden, ARPREIS, Art where (Nummer = TypNummer or Kunden.Preisverweis = TypNummer) and (Getdate() > vonDatum or vonDatum is null and GetDate() >= bisDatum or bisDatum is null) and ((arPreis.Artikelnummer = Art.Artikelnummer and Art.Shopaktiv = 1) or (arPreis.Artikelnummer = Art.Preisverweis and Art.Shopaktiv = 1))) AND Nummer like '%' AND EMAIL like '%@%' AND ShopPasswort is not null AND dbo.SL_fnPreis(art.Artikelnummer, GETDATE(), 'EUR', Preisgruppe, 0, 100) <> dbo.SL_fnListenPreis(art.Artikelnummer, GETDATE(), 'EUR', 0, 100) AND  dbo.SL_fnPreis(art.Artikelnummer, GETDATE(), 'EUR', Preisgruppe, 0, 100) <> 0  Order by Nummer, Art.Artikelnummer 
			// select 'dmc_xtc_kundenpreise' as ExportModus, Art.Artikelnummer AS Artikel_Artikelnr, '1' AS Preis_Ab_Menge, arPreis.Preis AS Kunden_Preis, Kunden.Sonderrabatt AS Kunden_Sonderrabatt, dbo.SL_fnListenPreis(art.Artikelnummer, GETDATE(), 'EUR', 0, 100) as Listenpreis, Nummer as Kundennummer,EMAIL as Kunden_email, '' as Freifeld8, '' as Freifeld9,'' as Freifeld10,'' as Freifeld11,'' as Freifeld12 FROM Kunden, ARPREIS, Art WHERE (Nummer = TypNummer or Kunden.Preisverweis = TypNummer) and (Getdate() > vonDatum or vonDatum is null and GetDate() >= bisDatum or bisDatum is null) and ((arPreis.Artikelnummer = Art.Artikelnummer and Art.Shopaktiv = 1) or (arPreis.Artikelnummer = Art.Preisverweis and Art.Shopaktiv = 1)) AND Nummer like '%' AND EMAIL like '%@%' AND ShopPasswort is not null AND arPreis.Preis <> dbo.SL_fnListenPreis(Art.Artikelnummer, GETDATE(), 'EUR', 0, 100) Order by Nummer, Art.Artikelnummer 

			$sku = trim($Freifeld{2});
			$qty = $Freifeld{3};
			if ($qty=='') $qty=1;
			$price = trim($Freifeld{4});
			if ($price=="") $price=0;
			$discount = $Freifeld{5};
			if ($discount=="") $discount=0;
			$customers_status = trim($Freifeld{7});
 			$customers_email_address = trim($Freifeld{8});				
				// Laufzeit
			$beginn = microtime(true); 
			if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_details - dmc_customer_prices - for group $customers_status (customer $customers_email_address) and sku $sku -> $price  Laufzeit = ".(microtime(true) - $beginn)."\n");
			
			//   Ermittlung der ID einer Kundengruppe sowie des Artikels
			// Rueckgabe "", wenn keine ID vorhanden
			$kundengruppen_id=dmc_get_customer_group_id($customers_status);
			$art_id=dmc_get_id_by_artno($sku);	
			$kundenpreistabelle = DB_TABLE_PREFIX."personal_offers_by_customers_status_".$kundengruppen_id;
			if ($kundengruppen_id=="" || $art_id=="") {
				// bei xtc, gambio etc ggfls Kundengruppe erst anlegen
				fwrite($dateihandle, "Abbruch, das KundengruppeID=".$kundengruppen_id." bzw ArtikelID=$art_id , d.h. wohl nicht vorhanden\n" );
			} else {
				// Prüfung, ob Preis schon eingetragen, dann Update, sonst Insert 
				$where="quantity=".$qty." AND products_id=".$art_id;
				if (dmc_entry_exits('price_id', $kundenpreistabelle, $where)) {
					// Update der übergebenen Werte
					$query="UPDATE ".$kundenpreistabelle." SET personal_offer=$price WHERE ".$where;
					dmc_sql_query($query);
					if (DEBUGGER) fwrite($dateihandle, "Preis aktualisiert ...  Laufzeit = ".(microtime(true) - $beginn)."\n");
				} else {
					// Insert 
					$query="INSERT INTO ".$kundenpreistabelle." (products_id,quantity,personal_offer) values ($art_id,$qty,$price)";
					dmc_sql_query($query);
					if (DEBUGGER) fwrite($dateihandle, "Preis eingetragen ...  Laufzeit = ".(microtime(true) - $beginn)."\n");
				} // end if else
			} //  endif Wenn Artikel und Kundengruppe existieren
			
		} // end exportmodus  dmc_xtc_kundenpreise
		
		// Exportmodus Dokument in Dokument Tabelle anlegen (neu ab 20102016)
		if ($ExportModusSpecial=='dmc_rahmenvertraege_header') {
			/* <!-- Auftraege SCL -->
				select 'dmc_rahmenvertraege_header' AS ExportModus, CASE WHEN b.Vorgang='B' THEN 'Rahmenbestellung' ELSE b.Vorgang END AS Belegart, b.Nummer AS document_no, b.Datum AS document_date, b.E_Mail AS customer_email_adress, b.Name1 AS document_printed_name, b.Name2 AS document_printed_name2, b.Referenznummer AS document_referenz, 0.00 AS document_sum_net, b.Rahmenbeginn AS document_beginn, b.Rahmenablauf AS document_ablauf, '' AS PDF_Upload FROM rahmenbestellungen AS b WHERE (b.Datum > '2016-01-01')

			*/
			/* CREATE TABLE IF NOT EXISTS `dmc_rahmenvertraege_header` (
					 `document_id` int(11)  NULL auto_increment,
				  `customer_web_user_id` int(11)  NULL,
				  `customer_email_adress` varchar(80)  NULL,
					`document_type` varchar(100)  NULL,
				  `document_file_type` varchar(100)  NULL,
				  `document_link` varchar(100)  NULL,
					`document_no` varchar(20)  NULL,
					`document_date` varchar(150)  NULL,
				  `delivery_date` varchar(150)  NULL,
				  `document_printed_name` varchar(100)  NULL,
				  `document_printed_name2` varchar(100)  NULL,
				  `document_printed_name3` varchar(100)  NULL,
				  `document_printed_company` varchar(150)  NULL,
				  `document_printed_street` varchar(150)  NULL,
				  `document_printed_zip` varchar(50)  NULL,
				  `document_printed_city` varchar(150)  NULL,
				  `document_printed_country_code` varchar(30)  NULL,
				  `document_referenz` varchar(30)  NULL,
				  `document_sum_net` double NULL,
				  `document_vat` double  NULL,
				  `document_sum_vat` double  NULL,
				  `document_sum_gros` double NULL,
				  `document_discount` double NULL,
				  `document_rahmenbeginn` DATE NOT NULL DEFAULT '0100-01-01',
				  `document_rahmenablauf` DATE NOT NULL DEFAULT '0100-01-01',
	
					  PRIMARY KEY  (`document_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; */
			$Belegart = $Freifeld{2};				
			$Belegnummer = $Freifeld{3};
			$Datum = $Freifeld{4};
			$EMail = $Freifeld{5};
			$Name = sonderzeichen2html(true,$Freifeld{6});
			$Vorname = sonderzeichen2html(true,$Freifeld{7});
			$Zusatz = sonderzeichen2html(true,$Freifeld{8});
			$GesamtpreisNetto = $Freifeld{9};
				if ($GesamtpreisNetto =='') $GesamtpreisNetto =0.00;
			//$GesamtRabatt = $Freifeld{10};
			//	if ($GesamtRabatt =='') $GesamtRabatt =0.00;
			$GesamtRabatt =0.00;
			$document_rahmenbeginn	= $Freifeld{10};
			$document_rahmenablauf	= $Freifeld{11};
			$pdf_datei = $Freifeld{12};
			
		/*	if (!defined('IMAGE_FOLDER')) {
				define('IMAGE_FOLDER',$_SERVER['DOCUMENT_ROOT'].'/testshop.com/shop/images/product_images/original_images/'); 
			}
			if (!defined('PDF_FOLDER')) {
				define('PDF_FOLDER',$_SERVER['DOCUMENT_ROOT'].'/testshop.com/shop/dmc_document/pdfs/'); 
			}
			// Wenn Datei Verzeichnis enthaelt, dann den Dateinamen separieren
		  	$pdf_datei = str_replace(' ','',$pdf_datei); 
			if (strpos($pdf_datei, "\\") !== false) {
				$pdf_datei=substr($pdf_datei,(strrpos($pdf_datei,"\\")+1),254); 
			} 
			if (DEBUGGER>=1) fwrite($dateihandle, "dmc_documents_header - Belegnummer  $Belegnummer for $EMail  and belegdatei = $pdf_datei\n");
			if (file_exists(IMAGE_FOLDER . $pdf_datei)) {
					// PDF DAtei in korrektes Verzeichnis kopieren
				copy(IMAGE_FOLDER . $pdf_datei, PDF_FOLDER .$pdf_datei);
				
			}
					*/
			// get  customer ID 
			// $customer_shop_id=dmc_get_id_by_email($EMail);
			$customer_shop_id=dmc_get_customer_id('',$EMail);
			if (DEBUGGER) fwrite($dateihandle, "CustomerId=$customer_shop_id\n");			
			// Wenn Kunde existiert, Kundeninformationen zuordnen
			if ($customer_shop_id<> "") {
				// Strasse, Ort, PLZ etc
			} else {
				$customer_shop_id=0;
			}
			
			// Neue Datensaetze anlegen
			$where="document_no='".$Belegnummer."'";
			if (!dmc_entry_exists( DB_TABLE_PREFIX .'dmc_rahmenvertraege_header', 'document_no', $Belegnummer)) {
					// Insert 
					dmc_sql_insert("dmc_rahmenvertraege_header", 
									"customer_web_user_id , document_type ,document_no ,document_date, customer_email_adress,
									document_printed_name ,document_printed_name2, document_referenz, 
									document_sum_net, document_discount, document_rahmenbeginn,document_rahmenablauf", 
									"$customer_shop_id, '$Belegart', '$Belegnummer', '$Datum', '$EMail', 
										'$Name', '$Vorname', '$Zusatz',$GesamtpreisNetto,$GesamtRabatt,
										'$document_rahmenbeginn','$document_rahmenablauf'");
			} // end if else
		} // end exportmodus dmc_rahmenvertraege_header
		
		
		// Exportmodus Dokumentpositionen in Dokument Tabelle anlegen (neu ab 20102016)
		if ($ExportModusSpecial=='dmc_rahmenvertraege_positions') {
			/* <!-- Auftraege SCL -->
				select distinct 'dmc_rahmenvertraege_positions' AS ExportModus, 'Auftrag' AS Belegart, b.VorgangsnummerTemporaer AS document_no, b.Positionsnummer AS position_no, CONCAT(b.Artikelnummer,'|',b.Hersteller)  AS product_sku, CONCAT(b.Artikelbezeichnung1,' ',b.Artikelbezeichnung2) AS product_name, b.Bestellmenge AS product_qty, b.Einkaufspreis AS product_price, '0' AS product_discount, b.Bestellwert AS product_price_amount, CASE WHEN b.Steuercode='000' THEN '19' ELSE CASE WHEN b.Steuercode='001' THEN '7' ELSE ''END END AS product_vat_percent, '' AS FF12 FROM rahmenbestellungenpositionen AS b WHERE b. Positionstyp=1 ORDER BY b.ID ASC
				CREATE TABLE IF NOT EXISTS `dmc_rahmenvertraege_positions` (
				  `document_id` int(11)  NULL,
				  `document_no` varchar(20)  NULL,
				  `document_type` varchar(20)  NULL,
				  `pos` varchar(100)  NULL,
				  `product_sku` varchar(50)  NULL,
				  `product_name` varchar(200)  NULL,
				  `product_qty` int(11)  NULL,
				  `product_price` double NULL,
				  `product_discount` double NULL,
				  `product_price_amount` double NULL,
				  `product_vat_percent` double NULL,
				  `product_delivery_date` date  NULL
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			*/
		
			$Belegart = $Freifeld{2};				
			$Belegnummer = $Freifeld{3};
			$position_no = $Freifeld{4};
			$product_sku = $Freifeld{5};
			$product_name = sonderzeichen2html(true,$Freifeld{6});
			$product_qty = $Freifeld{7};
				if ($product_qty =='') $product_qty =0.00;
			$product_price = $Freifeld{8};
						if ($product_price =='') $product_price =0.00;
			$product_discount = $Freifeld{9};
						if ($product_discount =='') $product_discount =0.00;
			$product_price_amount = $Freifeld{10};  
						if ($product_price_amount =='') $product_price_amount =0.00;
			$product_vat_percent = $Freifeld{11};
						if ($product_vat_percent =='') $product_vat_percent =0.00;
			$product_delivery_date = $Freifeld{12};
			
			if (DEBUGGER>=1) fwrite($dateihandle, "dmc_rahmenvertraege_positions - Belegnummer  $Belegnummer \n");
			
			// sku + variantencode evtl
			//$product_sku = explode ( '@', $product_sku_variant_vpe);
			// qty moeglicherweise mehrere, 
			//$product_qties = explode ( '@', $product_qty);
		
			if (DEBUGGER>=1) fwrite($dateihandle, "SKU =".$product_sku[0]." \n");
			
			// Neue Datensaetze anlegen
			if (dmc_entry_exists( DB_TABLE_PREFIX .'dmc_rahmenvertraege_positions', 'document_no', $Belegnummer,'AND','pos',$position_no)) {
				if (DEBUGGER>=1) fwrite($dateihandle, "delete first\n");
				// Delete first, damit es keine doppelten Eintraege gibt
				dmc_sql_delete(DB_TABLE_PREFIX . 'dmc_rahmenvertraege_positions', "document_no='$Belegnummer' AND pos='$position_no'");
			}
			// Insert
			dmc_sql_insert("dmc_rahmenvertraege_positions",  
								"document_no, document_type, pos, product_sku, product_name ,
								product_qty,product_price, product_discount, 
								product_price_amount, product_vat_percent, product_delivery_date ", 
								"'$Belegnummer', '$Belegart', '$position_no', '".$product_sku."',  '$product_name', '".$product_qty."', '$product_price', '$product_discount', $product_price_amount, $product_vat_percent, '$product_delivery_date'");
				
		} // end exportmodus dmc_rahmenvertraege_positions
		
		
		// Exportmodus dmc_staffelpreise_woocommerce Plugin "Woocommerce Dynamic Pricing & Discounts" 
		if ($ExportModusSpecial=='dmc_staffelpreise_woocommerce') {
	
   
			$Artikel_Artikelnr = $Freifeld{2};
			$abMenge = $Freifeld{3};			// Mehrere durch @ getrennt , zB 5,10,100
			// ggfls endendes @ entfernen
			if (substr($abMenge, -1))
				$abMenge=substr($abMenge, 0, -1);
			$Artikel_Preis1 = $Freifeld{4};		// Mehrere durch @ getrennt , zB 9.99,8.99,7.99
			if (substr($Artikel_Preis1, -1))
				$Artikel_Preis1=substr($Artikel_Preis1, 0, -1);
			$Artikel_Preis2 = $Freifeld{5};		// NICHT VERWENDET
			$Artikel_Preis3 = $Freifeld{6};		// NICHT VERWENDET
			$Artikel_Preis4 = $Freifeld{7};		// NICHT VERWENDET
			$Artikel_Preis5 = $Freifeld{8};		// NICHT VERWENDET
		
			$Artikel_Preis_Gruppe= $Freifeld{9}; 	// Zur Zeit noch nicht verwendet
			$Artikel_Preis_Rabatt = $Freifeld{10};	// NICHT VERWENDET
				if ($Artikel_Preis_Rabatt=='') $Artikel_Preis_Rabatt=0;
			$Waehrung = $Freifeld{11}; // Zur Zeit noch nicht verwendet
			$websiteNr = $Freifeld{12}; // Zur Zeit noch nicht verwendet
			
			// Preisberechnung, wenn separater Rabatt (oder Aufpreis) uebermittelt
			if ($Artikel_Preis_Rabatt<>0) {
				$Artikel_Preis = $Artikel_Preis - ($Artikel_Preis*$Artikel_Preis_Rabatt/100);
			}
			
			
			// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
			$post_ID=dmc_get_id_by_artno($Artikel_Artikelnr);
			if ($post_ID=="") 
				$exists=0; 
			else
				$exists=1;
			
			if ($exists==1) {

				// Preis Eintrag vom  Plugin "Woocommerce Dynamic Pricing & Discounts"  auslesen
				$cmd = "SELECT * FROM ".DB_PREFIX."options WHERE option_name = 'rp_wcdpd_options'";
				$sql_query = dmc_db_query($cmd);
				while ($r = dmc_db_fetch_array($sql_query)) {	
					extract ($r);
					// serialized data in Array umwandeln, muss zuvor UTF8 enkodiert werden da MYSQL Daten UTF8 sind und diese Datei nicht.             
					$unserialized = unserialize( utf8_encode($r[option_value]));
				}
				//end while
			
				if ($Artikel_Preis1 >0 ){  
					if (DEBUGGER>=1)  fwrite($dateihandle, "Preis fuer $Artikel_Artikelnr: $Artikel_Preis1 ab abMenge = $abMenge ... ");
					// NEUEN SERIALIZED STRING AUFBAUEN
					//WP produkt ID, alle im Array aufgeführten SKUs erhalten den angelegten Discount/Preisstaffelung
					$products_id = array($post_ID); 

					//dicsount und pricing arrays, es können soviel optionen wie nötig angelegt werden. 
					//min werte starten bei 1 
					//max muss größer sein als "min" und kann in der letzten discountoption "*" sein für alles über dem min wert 
					//es sind eine oder mehrere Dicountoptionen möglich, hier eine neue discountoption mit fortlaufender nummer anlegen falls notwendig, diese dann in $pricing mit aufführen
					$Mengen = explode ( '@', $abMenge);
					$Artikel_Preise  = explode ( '@', $Artikel_Preis1);
					if (DEBUGGER>=1)  fwrite($dateihandle, "Preis 1 fuer $Artikel_Artikelnr: ".$Artikel_Preise[0]." ab abMenge = ".$Mengen[0]." ... ");
					
					for($Anzahl = 1; $Anzahl <= count($Mengen); $Anzahl++) {
						$array_nummer=$Anzahl-1;
						
						if ($Anzahl == count($Mengen))
							$Mengenbis='*';							// Wenn letzter Preis
						else {
							$Mengenbis=$Mengen[$array_nummer+1]-1;		// Sonst Menge der folgenden Staffel -1
							$Mengenbis = round($Mengenbis);
						}
						
						$Mengen[$array_nummer] = round($Mengen[$array_nummer]);
						
						$pricing[$Anzahl] = array(  min => $Mengen[$array_nummer],
													max => $Mengenbis,
													type => 'fixed',
													value => $Artikel_Preise[$array_nummer] );
					}						
					
					//die folgenden können leer bleiben
					$categories = array();
					$roles = array();
					$capabilities = array();
					$users = array();
					$quantity_categories = array();
					$quantity_products = array();
					$special_categories = array();
					$special_products = array();

					//fertiges Datenarray für einen neuen Preisstaffelungsintrag
					$data = array(
								description => 'Preisstaffelung Artikel '.$Artikel_Artikelnr.'',
								method => 'quantity',
								quantities_based_on => 'exclusive_product',
								if_matched => 'all',
								valid_from => '',
								valid_until => '',
								selection_method => 'products_include',
								categories => $categories,
								products => $products_id,
								user_method => 'all',
								roles => $roles,
								capabilities => $capabilities,
								users => $users,
								quantity_products_to_adjust => 'matched',
								quantity_categories => $quantity_categories,               
								quantity_products => $quantity_products,               
								special_purchase => '',
								special_products_to_adjust => 'matched',
								special_categories => $special_categories,
								special_products => $special_products,
								special_adjust => '',
								special_type => 'percentage',
								special_value => '', 
								special_repeat => 0,
								pricing => $pricing                
					);

					// Wenn Artikel bereits vorhanden in "array", dann update, sonst ergaenzen
					$post_ID=dmc_get_id_by_artno($Artikel_Artikelnr);
			
					$position=0;
						//$ausgabe=print_r($unserialized[1]['pricing']['sets'][1],true);
						$ausgabe=print_r($unserialized,true);
					if (DEBUGGER>=1)  fwrite($dateihandle, "3891 - (altes) insert array 1 = $ausgabe \n");
					if (DEBUGGER>=1)  fwrite($dateihandle, "3892 - Anzahl=".sizeof( $unserialized[1]['pricing']['sets'])." \n");
						for ($i=1;$i<=sizeof( $unserialized[1]['pricing']['sets']);$i++) {
					//		if (DEBUGGER>=1)  fwrite($dateihandle, "\n DESC=".$unserialized[1]['pricing']['sets'][$i]['description']." ");
							if ($unserialized[1]['pricing']['sets'][$i]['description']=='Preisstaffelung Artikel '.$Artikel_Artikelnr) {
								$position=$i;
								if (DEBUGGER>=1)  fwrite($dateihandle, "XXXX".$position);
							}
						}
						//echo "\n<br> array_search=". array_search ('Preisstaffelung Artikel 100768',$unserialized[1]['pricing']['sets'][1]);
						 // echo "\n<br> Vorhanden an Position=".$position;
						if (DEBUGGER>=1)  fwrite($dateihandle, "\n<br> Vorhanden an Position=".$position);
						$ausgabe=print_r($unserialized[1]['pricing']['sets'][$position],true);
						if (DEBUGGER>=1)  fwrite($dateihandle, "3904 - altes array = $ausgabe \n");

					// den neuen Preistaffelung in das unserilaized Array an richtiger Stelle (am Ende des $unserialized[1]['pricing']['sets'] Array) einfügen
					if (DEBUGGER>=1)  fwrite($dateihandle, "3908 - zu erganzendes DATA array = $ausgabe \n");

					if ($position==0) {
						// Anfuegen
						array_push($unserialized[1]['pricing']['sets'], $data);
					} else {
						// Ersetzen
						$unserialized[1]['pricing']['sets'][$position]=$data;
					}
					
					$ausgabe=print_r($unserialized,true);
					if (DEBUGGER>=1)  fwrite($dateihandle, "\n 3919 - neues array = $ausgabe \n");

					$serialized = serialize($unserialized);
					/*echo "Preisstaffelungen re-serialized:";      
					echo "<hr><pre>";    
					echo utf8_decode($serialized);
					echo "</pre>";   
					 
					echo "Folgende Preisstaffelung wurde eingef&uuml;gt:<hr><pre>";
					print_r($data);
					echo "</pre>";*/


					// Update Datenbank
					$cmd = "UPDATE ".DB_PREFIX."options SET option_value = '".utf8_decode($serialized)."' WHERE option_name = 'rp_wcdpd_options'";
					$sql_query = dmc_db_query($cmd);
				}
				
				if (DEBUGGER>=1) fwrite($dateihandle, "Staffelpreise Zuordnung eingetragen.\n");
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Staffelpreise nicht eingetragen, da  Artikel nicht existent.\n");
			} //  endif Wenn Artikel existieren
			
		} // end exportmodus dmc_staffelpreise_woocommerce Plugin "Woocommerce Dynamic Pricing & Discounts"
		
		// Exportmodus dmc_product_to_category (woocommerce Grafe) 
		if ($ExportModusSpecial=='dmc_product_to_category') {
			
			$Artikel_Artikelnr = $Freifeld{2};				
			$Kategorie_ID = $Freifeld{3};
			
			if (strpos(strtolower(SHOPSYSTEM), 'woocommerce') !== false) {
				$post_ID=dmc_get_id_by_artno($Artikel_Artikelnr);
			
				// Kategorie zuordnen dmc_product_to_category
				if ($Kategorie_ID!='0' && $Kategorie_ID!='' && $post_ID!="") {
					$Kategorie_ID = dmc_prepare_seo_name($Kategorie_ID,'DE');		// SEO Feld ist mit Kategorie_ID gefuellt
					$Kategorie_ID_WOO = dmc_get_category_id($Kategorie_ID);
					 fwrite($dateihandle, "3714 WOO Kategorie_woo ID =".$Kategorie_ID_WOO.".\n");
					$tables='term_taxonomy AS TT, '.DB_PREFIX.'terms AS T';
					$where = "T.term_id = TT.term_id AND TT.taxonomy = 'product_cat' AND T.slug = '".$Kategorie_ID."'";
					$term_taxonomy_id = dmc_sql_select_query('term_taxonomy_id',$tables,$where);
					fwrite($dateihandle, "3718 term_taxonomy_id='".$term_taxonomy_id."'.\n");
						
					// Artikel-Kategoriezuordnung 
					$insert_sql_data = array(	'object_id' => $post_ID,
												'term_taxonomy_id' => $term_taxonomy_id,		
												'term_order' => 0
											);
					// Pruefen ob schon zugeordnet
					$tables='term_relationships ';
					$where = "object_id = ".$post_ID." AND term_taxonomy_id = '".$term_taxonomy_id."'";
					$object_id = dmc_sql_select_query('object_id',$tables,$where);
					 
					// term_relationships
					if ($object_id > 0) {
						fwrite($dateihandle, "Artikel ist bereits der Kategorie ".$Kategorie_ID_WOO." zugeordnet \n");
					} else {	
						fwrite($dateihandle, "Artikel-Kategoriezuordnung\n");
						dmc_sql_insert_array(DB_PREFIX."term_relationships", $insert_sql_data);
						 fwrite($dateihandle, "465\n");
						// Anzahl Produkte der Kategorie um 1 hochzaehlen.
						dmc_sql_update(DB_PREFIX.'term_taxonomy', 'count=count+1', "term_taxonomy_id = '".$term_taxonomy_id."'");
						 fwrite($dateihandle, "468\n");
					}
				}
			} else if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) {
				$art_id=dmc_get_id_by_artno($Artikel_Artikelnr);
				if ($art_id=="") {
					$exits=false; 
				} else {
					$exits=true;
					// fwrite($dateihandle, "DELETE FIRST ".$art_id." ******\n");
					fwrite($dateihandle, "\n AKTUALISIERUNG Kategoriezuordnungen ArtID: $art_id ... "); 
					$Kategorie_IDs = explode('@',$Kategorie_ID);
					for ( $i = 0; $i < count ( $Kategorie_IDs ); $i++ ) {
							// KategorieIds ergaenzen
							$Kategorie_IDs[$i] = dmc_get_category_id($Kategorie_IDs[$i]);
							fwrite($dateihandle, "KatID: ".$Kategorie_IDs[$i]." ... "); 
							if ($Kategorie_IDs[$i] !="" && $Kategorie_IDs[$i] !="0") {
								fwrite($dateihandle, "  ergaenzen ... "); 
								$sql_update_data_array['categories'][$i]['id'] = $Kategorie_IDs[$i];			// !!! $Kategorie_ID wird geprueft
							}
					}	
					$sql_update_data_array = array(
						'categories' => array(
							array('id' => '539')
						)
					);
					$ausgeben = print_r($sql_update_data_array, true);
					
					fwrite($dateihandle, "\n SCHREIBE ARRAY: ".$ausgeben." \n");
					$result=$client->call('articles/' . $art_id , ApiClient::METHODE_PUT, $sql_update_data_array);
					$ausgeben = print_r($result, true);
					fwrite($dateihandle, "\n Ergebnis Art ID $art_id: ".$ausgeben." \n"); 
					
					$result=$client->put('articles/'.$art_id, array(
						'categories' => array(
							array('id' => '539')
						)
					));
					$ausgeben = print_r($result, true);
					fwrite($dateihandle, "\n Ergebnis Art ID $art_id: ".$ausgeben." \n"); 
				
				}
			} else {
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: dmc_product_to_category für Shop ".SHOPSYSTEM." nicht verfuegbar.\n");
			}
			
		} // end exportmodus dmc_product_to_category
		
		// Exportmodus Kundenpreise für individuelle Tabellen
		if ($ExportModusSpecial=='dmc_customer_prices_shopware') {
			//  select 'dmc_customer_prices_shopware' as ExportModus, Kunde.cmp_e_mail as Kunden_EMAIL, Preise.[artcode] as Artikelnummer, '0' as Artikel_Variante, Preise.[bedr1] AS Artikel_Preis, '' as Rabattsatz, '1' as Menge, '0' as Website, '' as customer_discount_group,'' as Freifeld8,'' as Freifeld9,'' as Freifeld10,'' as Freifeld11,'' as Freifeld12 FROM [002].[dbo].[staffl] AS Preise INNER JOIN [002].[dbo].[cicmpy] AS kunde ON (kunde.PriceList = Preise.prijslijst AND  Preise.[prijs83]=669 AND Kunde.cmp_e_mail IS NOT NULL) WHERE Preise.artcode= '11 01 000 1' 


			// Tabellen:
			/*   PREFIX BEACHTEN   
			CREATE TABLE IF NOT EXISTS `dmc_kundenpreise` (
				    id int(11) NOT NULL AUTO_INCREMENT,
				  customer_id int(11) NOT NULL DEFAULT 0,
				  customer_email varchar(20) NOT NULL DEFAULT '',
				  artnr varchar(20) NOT NULL DEFAULT '',
				  artvarnr varchar(20) NOT NULL DEFAULT '',
				  product_id int(11) NOT NULL DEFAULT 0,
				  abmenge double NOT NULL DEFAULT 1,
				  preis double NOT NULL DEFAULT 0,
				  rabattsatz double NOT NULL DEFAULT 0,
				  store varchar(20) NOT NULL DEFAULT '',
				  customer_discount_group varchar(32) NOT NULL DEFAULT '',
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; */
			
			$customers_email_address = trim($Freifeld{2});				// Store_Name
			$sku = trim($Freifeld{3});
			// ACHTUNG, SHOPWAR Eakzeptiert keine Leerzeichen in SKUs
			$sku = str_replace(' ','_',$sku);
			$var_id = $Freifeld{4}; 
			$price = str_replace(',','.',trim($Freifeld{5}));
			if ($price=="") $price=0;
			$discount = str_replace(',','.',trim($Freifeld{6}));
			if ($discount=="") $discount=0;
			// Wenn Rabattsatz gesetzt, Preis neu berechnen
			if ($discount>0) 
				$price=$price-($price*$discount/100);
			$qty = $Freifeld{7};
			if ($qty=='') $qty=1;
			$store_id = $Freifeld{8};
			if ($store_id=='') $store_id=0;
			$customer_discount_group = trim($Freifeld{9});
			$customer_discount_group='';
				// Laufzeit
			$beginn = microtime(true); 
			//if (DEBUGGER>=1) 
				fwrite($dateihandle, "dmc_set_details - dmc_customer_prices_shopware - for customer $customers_email_address and sku $sku -> $price  Laufzeit = ".(microtime(true) - $beginn)."\n");
			
			
			// get  customer ID 
			$CustomerId=dmc_get_customer_id('28021973',$customers_email_address);
			// get ArtID
			$art_id=dmc_get_id_by_artno(trim($sku));	
			// get Art Details ID
			$art_details_id=dmc_get_details_id_by_artno(trim($sku));
			
			//if (DEBUGGER>1) 
				fwrite($dateihandle, "Shop ArtikelID = $art_id / $art_details_id und CustomerID=$CustomerId ($customers_email_address) ... ");			
			// Wenn Kunde existiert, Kundenpreis zuordnen
			if ($CustomerId!='' && $art_id!='' && $art_details_id!='') {
				// Tabelle s_plugin_users_prices
				// Spalten  id	 userID	 from	 to	 articleID	 articledetailsID	 price
				$where="userID=".$CustomerId." AND `from`='".$qty."' AND articleID='".$art_id."' AND articledetailsID='".$art_details_id."'";
				
				if (dmc_entry_exits('price', 's_plugin_users_prices', $where)) {
					// Update der übergebenen Werte
					$query="UPDATE s_plugin_users_prices SET price=$price WHERE ".$where;
					dmc_sql_query($query);
					if (DEBUGGER>1) fwrite($dateihandle, "Preis aktualisiert ...  Laufzeit = ".(microtime(true) - $beginn)."\n");
				} else {
					// Insert  
					dmc_sql_insert("s_plugin_users_prices", 
									"userID, `from`, `to`, articleID, articledetailsID, price", 
									"'$CustomerId', '$qty', 'beliebig', $art_id, '$art_details_id', $price");
					if (DEBUGGER>1) fwrite($dateihandle, "Preis $price eingetragen ...  Laufzeit = ".(microtime(true) - $beginn)."\n");
				} // end if else
			} else { 
				// NICHT EXISTENT
				if (DEBUGGER>1) fwrite($dateihandle, "Kunde ODER Artikel nicht existent\n");
			}
			//} //  endif Wenn Artikel existieren
		} // end exportmodus  dmc_customer_prices_shopware
		
		// Exportmodus dmc_update_custno_by_email Kundennummer im Shop setzen / aktualisieren 
		if ($ExportModusSpecial=='dmc_update_custno_by_email') {
			// Aktuell nur Showare 
			//  select 'dmc_update_custno_by_email' as ExportModus, Email as Kunden_EMAIL, Nummer as Kundennummer, '' as Freifeld2, '' AS Freifeld3, '' as Freifeld4, '1' as Freifeld5, '0' as Freifeld6, '' as Freifeld7,'' as Freifeld8,'' as Freifeld9,'' as Freifeld10,'' as Freifeld11,'' as Freifeld12 FROM Kunden WHERE Email LIKE '%d.kuehnemundt@%'

			
			$customers_email_address = trim($Freifeld{2});				
			$customers_no = trim($Freifeld{3});				
			// Laufzeit
			$beginn = microtime(true); 
			//if (DEBUGGER>=1) 
				fwrite($dateihandle, "dmc_set_details - dmc_update_custno_by_email - for customer $customers_email_address with No  $customers_no  Laufzeit = ".(microtime(true) - $beginn)."\n");
			
			// get  customer ID 
			$CustomerId=dmc_get_customer_id('28021973',$customers_email_address);
			//if (DEBUGGER>1) 
				fwrite($dateihandle, "CustomerID=$CustomerId ($customers_email_address) ... ");			
			// Wenn Kunde existiert
			if ($CustomerId!='') {
				// Update der übergebenen Werte
				$query="UPDATE s_user SET customernumber='".$customers_no."' WHERE email='".$customers_email_address."'";
				dmc_sql_query($query);
				if (DEBUGGER>1) fwrite($dateihandle, "Nummer aktualisiert ...  Laufzeit = ".(microtime(true) - $beginn)."\n");
			} else { 
				// NICHT EXISTENT
				if (DEBUGGER>1) fwrite($dateihandle, "Kunde nicht existent\n");
			}
		} // end exportmodus  dmc_update_custno_by_email Kundennummer im Shop setzen / aktualisieren
		
		// Exportmodus dmc_staffelpreise_woocommerce "anderes" Plugin
		if ($ExportModusSpecial=='dmc_staffelpreise_woocommerce_2') {
			// ZB Freifeld2 = 0099.0
			// ZB Freifeld3 = 50.0000@100
			// ZB Freifeld4 = 2.2900@1.99
			$Artikel_Artikelnr = $Freifeld{2};
			$abMenge = $Freifeld{3};			// Mehrere durch @ getrennt , zB 5,10,100
			// ggfls endendes @ entfernen
			if (substr($abMenge, -1)=='@')
				$abMenge=substr($abMenge, 0, -1);
			$Artikel_Preis1 = $Freifeld{4};		// Mehrere durch @ getrennt , zB 9.99,8.99,7.99
			if (substr($Artikel_Preis1, -1)=='@')
				$Artikel_Preis1=substr($Artikel_Preis1, 0, -1);
			$Artikel_Preis2 = $Freifeld{5};		// NICHT VERWENDET
			$Artikel_Preis3 = $Freifeld{6};		// NICHT VERWENDET
			$Artikel_Preis4 = $Freifeld{7};		// NICHT VERWENDET
			$Artikel_Preis5 = $Freifeld{8};		// NICHT VERWENDET
		
			$Artikel_Preis_Gruppe= $Freifeld{9}; 	// Zur Zeit noch nicht verwendet
			$Artikel_Preis_Rabatt = $Freifeld{10};	// NICHT VERWENDET
			if ($Artikel_Preis_Rabatt=='') $Artikel_Preis_Rabatt=0;
			$Waehrung = $Freifeld{11}; // Zur Zeit noch nicht verwendet
			$websiteNr = $Freifeld{12}; // Zur Zeit noch nicht verwendet
			
			// Preisberechnung, wenn separater Rabatt (oder Aufpreis) uebermittelt
			if ($Artikel_Preis_Rabatt<>0) {
				$Artikel_Preis = $Artikel_Preis - ($Artikel_Preis*$Artikel_Preis_Rabatt/100);
			}
			
			// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
			$post_ID=dmc_get_id_by_artno($Artikel_Artikelnr);
			if ($post_ID=="") 
				$exists=0; 
			else
				$exists=1;
			
			if ($exists==1) {
				if ($Artikel_Preis1 <>'' ) {  
					if (DEBUGGER>=1)  fwrite($dateihandle, "Preis fuer $Artikel_Artikelnr: $Artikel_Preis1 ab abMenge = $abMenge ... ");
					// NEUEN SERIALIZED STRING AUFBAUEN
					//WP produkt ID, alle im Array aufgeführten SKUs erhalten den angelegten Discount/Preisstaffelung
					$products_id = array($post_ID); 

					//dicsount und pricing arrays, es können soviel optionen wie nötig angelegt werden. 
					//min werte starten bei 1 
					//max muss größer sein als "min" und kann in der letzten discountoption "*" sein für alles über dem min wert 
					//es sind eine oder mehrere Dicountoptionen möglich, hier eine neue discountoption mit fortlaufender nummer anlegen falls notwendig, diese dann in $pricing mit aufführen
					$Mengen = explode ( '@', $abMenge);
					$Artikel_Preise  = explode ( '@', $Artikel_Preis1);
				
					$data = array (
							  0 => 
							  array (
								'min' => '1',
								'max' => round($Mengen-1),
								'val' => '100%',
							  ),
							);
					
					for($Anzahl = 1; $Anzahl <= count($Mengen); $Anzahl++) {
						$array_nummer=$Anzahl-1;
						
						if ($Anzahl == count($Mengen))
							$Mengenbis='*';							// Wenn letzter Preis
						else {
							$Mengenbis=$Mengen[$array_nummer+1]-1;		// Sonst Menge der folgenden Staffel -1
							$Mengenbis = round($Mengenbis);
						}
						
						// $Mengen[$array_nummer] = round($Mengen[$array_nummer]);
						
						$pricing[$Anzahl] = array(  min => round($Mengen[$array_nummer]), // $Mengen[$array_nummer],
													max => $Mengenbis,
													val => $Artikel_Preise[$array_nummer] );
						$data[$array_nummer] = $pricing[$Anzahl];
					}						
					
					//fertiges Datenarray für einen neuen Preisstaffelungsintrag
					$ausgabe=print_r($data,true);
					if (DEBUGGER>=1)  fwrite($dateihandle, "PreisArray=".$ausgabe."\n");
					$preise_serialized=serialize($data);
					if (DEBUGGER>=1)  fwrite($dateihandle, "preise_serialized=".$preise_serialized."\n");
					
					// Alte Eintraege loeschen
					
					$query="DELETE FROM ".DB_PREFIX."postmeta WHERE post_id=".$post_ID." and (meta_key='_wc_bulk_pricing_ruleset' or meta_key='_wc_bulk_pricing_custom_ruleset')";
					if (DEBUGGER>=1)  fwrite($dateihandle, "query=".$query."\n");
					// $sql_query = dmc_db_query($cmd);
					// Neue Eintrage setzten
					$query="INSERT INFO ".DB_PREFIX."postmeta (post_id,meta_key,meta_value) VALUES (".$post_ID.",'_wc_bulk_pricing_ruleset' ,'_custome')";
					if (DEBUGGER>=1)  fwrite($dateihandle, "query=".$query."\n");
					// $sql_query = dmc_db_query($cmd);
					$query="INSERT INFO ".DB_PREFIX."postmeta (post_id,meta_key,meta_value) VALUES (".$post_ID.",'_wc_bulk_pricing_custom_ruleset' ,'".$preise_serialized."')";
					if (DEBUGGER>=1)  fwrite($dateihandle, "query=".$query."\n");
					// $sql_query = dmc_db_query($cmd);
				}
				
				if (DEBUGGER>=1) fwrite($dateihandle, "Staffelpreise Zuordnung eingetragen.\n");
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Staffelpreise nicht eingetragen, da  Artikel nicht existent.\n");
			} //  endif Wenn Artikel existieren
			
		} // end exportmodus dmc_staffelpreise_woocommerce_2
		
		// Exportmodus dmc_set_shopware_groupprices
		if ($ExportModusSpecial=='dmc_set_shopware_groupprices') {
			// select 'dmc_set_shopware_groupprices' AS Freifeld1, "Productcode" AS Artikelnummer, "Shop DE"||'@EK' AS ShopDE, "eBay DE"||'@ebayd' AS eBayDE, "eBay UK"||'@ebayu' AS eBayUK, "Am DE"||'@amde' AS AmazonDE, "Am UK"||'@amuk' AS AmazonUK, "Am IT"||'@amit' AS AmazonIT, '' AS ff9, '' AS ff10,  '' AS ff11, '' AS ff12 FROM Excel_Tabelle where "Productcode" like '$variable1%' 

			if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_shopware_groupprices Artikel ".$Freifeld{2}."\n");
			$anzahl_preise=0;
			$Artikel_Artikelnr = $Freifeld{2};
			
			 $gartenzaunshop24=true;
	
			// Sonderfunktion fuer gartenzaunshop24
			if ($gartenzaunshop24==true)
			{
				for ( $i = 3; $i < 12; $i++ ) {
								$Artikel_Preis_zu_Gruppe = $Freifeld{$i};			// Preis und Gruppe durch @ getrennt zB 5.55@EK
								$Artikel_Preis_zu_Gruppe = str_replace(' "€"','',$Artikel_Preis_zu_Gruppe);
								$Artikel_Preis_zu_Gruppe = str_replace('"€"','',$Artikel_Preis_zu_Gruppe);
								$Artikel_Preis_zu_Gruppe = str_replace('"Û"','',$Artikel_Preis_zu_Gruppe);
								$Artikel_Preis_zu_Gruppe = str_replace('"Ã›"','',$Artikel_Preis_zu_Gruppe);
								$Artikel_Preis_zu_Gruppe = str_replace(' ','',$Artikel_Preis_zu_Gruppe);
								$Artikel_Preis_zu_Gruppe = str_replace(',','.',$Artikel_Preis_zu_Gruppe);
								if (DEBUGGER>=99) fwrite($dateihandle, "-> Artikel_Preis_zu_Gruppe $i = $Artikel_Preis_zu_Gruppe \n");
								if ($Artikel_Preis_zu_Gruppe!="" && $Artikel_Preis_zu_Gruppe!="@") {
									$Artikel_Preis = explode("@",$Artikel_Preis_zu_Gruppe);					// Preis und Gruppe durch @ getrennt zB 5.55@EK
									$sql_update_price_array['mainDetail']['prices'][$anzahl_preise]['customerGroupKey'] = $Artikel_Preis[1];	
									// $sql_update_price_array['mainDetail']['prices'][$anzahl_preise]['price'] = $Artikel_Preis[0]*0.8403361344537815;
									$sql_update_price_array['mainDetail']['prices'][$anzahl_preise]['price'] = $Artikel_Preis[0];
									$sql_update_var_price_array['prices'][$anzahl_preise]['customerGroupKey'] = $Artikel_Preis[1];	
									// $sql_update_price_array['mainDetail']['prices'][$anzahl_preise]['price'] = $Artikel_Preis[0]*0.8403361344537815;
									$sql_update_var_price_array['prices'][$anzahl_preise]['price'] = $Artikel_Preis[0];
									$anzahl_preise++;
								}
								
				}	// end if
				$ausgeben = print_r($sql_update_price_array, true);
			//	if (DEBUGGER>=99) fwrite($dateihandle, "sql_update_price_array  ".$ausgeben."\n");
							
				$query = "SELECT articleID, articledetailsID FROM s_articles_attributes";
				$query .= " WHERE attr2 ='".$Artikel_Artikelnr."'";
				
				$link=dmc_db_connect();
			//	if (DEBUGGER==99)  
					fwrite($dateihandle, "gartenzaunshop dmc_get_id_by_artno in dmconnector.php -SQL= ".$query." .\n");
				
				
				$sql_query = mysqli_query($link,$query);				
				while ($TEMP_ID = mysqli_fetch_assoc($sql_query)) {
					
					if ($TEMP_ID['articleID']=='' || $TEMP_ID['articleID']=='null')
						// IF no ID -> Product not available
						$art_id = "";
					else {
						$art_id  = $TEMP_ID['articleID'];
						$articledetailsID  = $TEMP_ID['articledetailsID'];
						if ($art_id=="") {
							if (DEBUGGER>=1) fwrite($dateihandle, "-> Artikel NICHT vorhanden\n");
						} else {
							// Wenn articleID = articledetailsID, dann Hauptartikel, sonst Variante
							if ($art_id==$articledetailsID) {
								fwrite($dateihandle, "Preisabgleich Artikel mit ID = ".$art_id." \n");
								// sql_update_var_price_array
								$result=$client->call('articles/' . $art_id , ApiClient::METHODE_PUT, $sql_update_price_array);
							} else {
								fwrite($dateihandle, "Preisabgleich Variante mit ID = ".$articledetailsID." \n");
								// sql_update_var_price_array
								$result=$client->call('variants/' . $articledetailsID , ApiClient::METHODE_PUT, $sql_update_var_price_array);
							}
							$ausgeben = print_r($result, true);
							if (DEBUGGER>=1) fwrite($dateihandle, "result  ".$ausgeben."\n");
							if ($ausgeben=='')
								$fehlermeldung="Eventuell Validation-Error";
							else
								$fehlermeldung="";
						}
					}
					
				}
			} else {
				$art_id=dmc_get_id_by_artno(trim($Artikel_Artikelnr));
				if ($art_id=="") {
					if (DEBUGGER>=1) fwrite($dateihandle, "-> Artikel NICHT vorhanden\n");
				} else {
					fwrite($dateihandle, "Preisabgleich Artikel mit ID = ".$art_id." \n");
					for ( $i = 3; $i < 12; $i++ ) {
						$Artikel_Preis_zu_Gruppe = $Freifeld{$i};			// Preis und Gruppe durch @ getrennt zB 5.55@EK
						$Artikel_Preis_zu_Gruppe = str_replace(' "€"','',$Artikel_Preis_zu_Gruppe);
						$Artikel_Preis_zu_Gruppe = str_replace('"€"','',$Artikel_Preis_zu_Gruppe);
						$Artikel_Preis_zu_Gruppe = str_replace('"Û"','',$Artikel_Preis_zu_Gruppe);
						$Artikel_Preis_zu_Gruppe = str_replace('"Ã›"','',$Artikel_Preis_zu_Gruppe);
						$Artikel_Preis_zu_Gruppe = str_replace(' ','',$Artikel_Preis_zu_Gruppe);
						$Artikel_Preis_zu_Gruppe = str_replace(',','.',$Artikel_Preis_zu_Gruppe);
						if (DEBUGGER>=99) fwrite($dateihandle, "-> Artikel_Preis_zu_Gruppe $i = $Artikel_Preis_zu_Gruppe \n");
						if ($Artikel_Preis_zu_Gruppe!="" && $Artikel_Preis_zu_Gruppe!="@") {
							$Artikel_Preis = explode("@",$Artikel_Preis_zu_Gruppe);					// Preis und Gruppe durch @ getrennt zB 5.55@EK
							$sql_update_price_array['mainDetail']['prices'][$anzahl_preise]['customerGroupKey'] = $Artikel_Preis[1];	
							// $sql_update_price_array['mainDetail']['prices'][$anzahl_preise]['price'] = $Artikel_Preis[0]*0.8403361344537815;
							$sql_update_price_array['mainDetail']['prices'][$anzahl_preise]['price'] = $Artikel_Preis[0];
							$anzahl_preise++;
						}
						
					}	// end if
					$ausgeben = print_r($sql_update_price_array, true);
					if (DEBUGGER>=99) fwrite($dateihandle, "sql_update_price_array  ".$ausgeben."\n");
					$result=$client->call('articles/' . $art_id , ApiClient::METHODE_PUT, $sql_update_price_array);
					$ausgeben = print_r($result, true);
					if (DEBUGGER>=1) fwrite($dateihandle, "result  ".$ausgeben."\n");
					if ($ausgeben=='')
						$fehlermeldung="Eventuell Validation-Error";
					else
						$fehlermeldung="";
				}
			}				
			
		} // end dmc_set_shopware_groupprices
		
	}// end function SetDetails	
	
?>