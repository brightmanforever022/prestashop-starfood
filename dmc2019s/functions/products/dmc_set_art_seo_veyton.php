<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_set_art_seo_veyton.php												*
*  inkludiert von dmc_write_art.php 										*	
*  Artikel SEOs anlegen	Veyton												*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
13.03.2012
- neu
*/
					if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_art_seo_veyton - ");
					$Artikel_SEO_Bezeichnung =  dmc_prepare_seo ($Artikel_ID."_".$Artikel_Bezeichnung,"product",$Kategorie_ID,'de');
					if (DEBUGGER>=1) fwrite($dateihandle, "SEO Veyton -> $Artikel_SEO_Bezeichnung\n");
					$insert_sql_data = array(				
						'url_md5' => md5($Artikel_Sprache.'/'.$Artikel_SEO_Bezeichnung),
						'url_text' => $Artikel_Sprache.'/'.$Artikel_SEO_Bezeichnung,
						'language_code' => $Artikel_Sprache,
						'link_type' => '1',   			//2 = kategorie
						'meta_description' => $Artikel_MetaDescription,
						'meta_title' => $Artikel_MetaText,
						'meta_keywords' => $Artikel_MetaKeyword,
						'link_id' =>  $Artikel_ID);		
					
					if (SHOPSYSTEM_VERSION>=4.2) $insert_sql_data['store_id'] = SHOP_ID;		// Ab veyton 4.2 
			
					dmc_sql_insert_array(TABLE_SEO_URL, $insert_sql_data);			
					
					// Wenn Fremdsprachen vorhanden
					if ($no_of_languages>1) {
						// Englisch
						$Artikel_Sprache='en';
						if (DEBUGGER>=50) fwrite($dateihandle, "seo Beschreibung insert $Artikel_Sprache Englisch als Zusatzsprache\n");
						$insert_sql_data = array(				
							'url_md5' => md5($Artikel_Sprache.'/'.$Artikel_SEO_Bezeichnung),
							'url_text' => $Artikel_Sprache.'/'.$Artikel_SEO_Bezeichnung,
							'language_code' => $Artikel_Sprache,
							'link_type' => '1',   			//2 = kategorie
							'meta_description' => $Artikel_MetaDescription,
							'meta_title' => $Artikel_MetaText,
							'meta_keywords' => $Artikel_MetaKeyword,
							'link_id' =>  $Artikel_ID);		

						dmc_sql_insert_array(TABLE_SEO_URL, $insert_sql_data);
					}
			
?>
	