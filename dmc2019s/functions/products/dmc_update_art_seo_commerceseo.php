<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_set_art_seo_commerceseo.php											*
*  inkludiert von dmc_write_art.php 										*	
*  Artikel SEOs aktualisieren commerce:SEO									*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
21.03.2012
- neu
*/
	
					if (DEBUGGER>=1) fwrite($dateihandle, "dmc_update_art_seo_commerceseo - ");
			
					// In der Regel soll hier kein Update auf bestehende Eintraege erfolgen
					/*$Artikel_SEO_Bezeichnung =  dmc_prepare_seo ($Artikel_ID."_".$Artikel_Bezeichnung,"product",$Kategorie_ID,'de');
					if (DEBUGGER>=1) fwrite($dateihandle, "SEO commerce:SEO -> $Artikel_SEO_Bezeichnung Sprache=$Artikel_Sprache, TAbelle=".commerce_seo_url."\n");
					$insert_sql_data = array(				
						'url_md5' => md5($Artikel_Sprache.'/'.$Artikel_SEO_Bezeichnung),
						'url_text' => $Artikel_Sprache.'/'.$Artikel_SEO_Bezeichnung,
						'products_id' =>  $Artikel_ID,
						//'categories_id' => $Kategorie_ID,
						//'blog_id' => '',
						//'blog_cat' => '',
						//'content_group' => '1',   			
						'language_id' => $Artikel_Sprache
						);		

					xtc_db_perform('commerce_seo_url', $insert_sql_data);			
					
					// Wenn Fremdsprachen vorhanden
					if ($no_of_languages>1) {
						// Englisch
						$Artikel_Sprache=$Artikel_Sprache+1;
						if (DEBUGGER>=50) fwrite($dateihandle, "seo Beschreibung insert $Artikel_Sprache Englisch als Zusatzsprache\n");
						$insert_sql_data = array(				
						'url_md5' => md5($Artikel_Sprache.'/'.$Artikel_SEO_Bezeichnung),
						'url_text' => $Artikel_Sprache.'/'.$Artikel_SEO_Bezeichnung,
						'products_id' =>  $Artikel_ID,
						//'categories_id' => $Kategorie_ID,
						//'blog_id' => '',
						//'blog_cat' => '',
						//'content_group' => '1',   			
						'language_id' => $Artikel_Sprache
						);		
						xtc_db_perform('commerce_seo_url', $insert_sql_data);
					}*/
			
?>
	