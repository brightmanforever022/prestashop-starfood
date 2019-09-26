<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_array_create_desc_standard.php										*
*  inkludiert von dmc_write_cat.php 										*	
*  Kategorie Beschreibung Array mit Werten fuellen							*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
15.03.2012
- neu
*/
		
					fwrite($dateihandle, "dmc_array_create_desc_standard fuer SHop ".SHOPSYSTEM_VERSION."\n");
					  
			// Gambio ab GX2 Kategorie Überschrift 
			if (strpos(strtolower(SHOPSYSTEM_VERSION), 'gx2') !== false ||
				strpos(strtolower(SHOPSYSTEM_VERSION), 'gx3') !== false) {
				$d1 = array(" ","Ä", "Ö", "Ü", "ä" , "ö", "ü", "ß","<",">","#","\"","'","�",",","&","�","?",";","\\","/","---");
				$d2 = array("-", "Ae","Oe","Ue","ae","oe","ue","sz","_","_","_","_","_","_","_","-","2","-","-","_","_","-");
				$seo_text_cat = str_replace($d1, $d2, $Kategorie_Bezeichnung);		 
				$seo_text_cat = preg_replace('/[^0-9a-zA-Z-_]/', '', $seo_text_cat);
							
				// 4 Description
				$sql_insert_desc_array = array(
					  'categories_id' => $new_cat_id,
					  'language_id' => $language_id,
					  'categories_name' => $Kategorie_Bezeichnung,
					  'categories_heading_title' => $Kategorie_Bezeichnung,
					  'categories_description' => $Kategorie_Beschreibung,
					  'categories_meta_title'=> $Kategorie_Bezeichnung,
					  'categories_meta_description'=> $Kategorie_ID,
					  'categories_meta_keywords' => '',
					  'gm_alt_text'	 => '',
					  'gm_url_keywords' => $seo_text_cat
					  );
					  
			} else if (strpos(strtolower(SHOPSYSTEM), 'osc') !== false) {
				// 4 Description
				$sql_insert_desc_array = array(
					  'categories_id' => $new_cat_id,
					  'language_id' => $language_id,
					  'categories_name' => $Kategorie_Bezeichnung,
					  'categories_meta_keywords'=> $Kategorie_ID.','		// ACHTUNG: KEIN STANDARD FELD, DAHER in OSC anlegen ALTER TABLE categories_description ADD categories_meta_keywords VARCHAR(50);
					  );
			} else {
				// 4 Description
				$sql_insert_desc_array = array(
					  'categories_id' => $new_cat_id,
					  'language_id' => $language_id,
					  'categories_name' => $Kategorie_Bezeichnung,
					  'categories_description' => $Kategorie_Beschreibung,
					  'categories_meta_keywords'=> $Kategorie_ID.','
					  );
			}
					  
					  
	
?>
	