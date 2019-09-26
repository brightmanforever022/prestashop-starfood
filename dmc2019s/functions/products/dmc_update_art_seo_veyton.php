<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_update_art_seo_veyton.php											*
*  inkludiert von dmc_write_art.php 										*	
*  Artikel SEOs updaten	Veyton												*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
13.03.2012
- neu
*/
					if (DEBUGGER>=1) fwrite($dateihandle, "dmc_update_art_seo_veyton - ");
					$Artikel_SEO_Bezeichnung =  dmc_prepare_seo ($Artikel_ID."_".$Artikel_Bezeichnung,"product",$Kategorie_ID,'de');
					if (DEBUGGER>=1) fwrite($dateihandle, "SEO Veyton -> $Artikel_SEO_Bezeichnung\n");
					
					// In der Regel soll hier kein Update auf bestehende Eintraege erfolgen
					
				/*	$insert_sql_data = array(				
						'url_md5' => md5($Artikel_Sprache.'/'.$Artikel_SEO_Bezeichnung),
						'url_text' => $Artikel_Sprache.'/'.$Artikel_SEO_Bezeichnung,
						'language_code' => $Artikel_Sprache,
						'link_type' => '1',   			/2 = kategorie
						'meta_description' => $Artikel_MetaDescription,
						'meta_title' => $Artikel_MetaText,
						'meta_keywords' => $Artikel_MetaKeyword,
						'link_id' =>  $Artikel_ID);		

					xtc_db_perform(TABLE_SEO_URL, $insert_sql_data);			
					
					/ Wenn Fremdsprachen vorhanden
					if ($no_of_languages>1) {
						/ Englisch
						$Artikel_Sprache='en';
						if (DEBUGGER>=50) fwrite($dateihandle, "seo Beschreibung insert $Artikel_Sprache Englisch als Zusatzsprache\n");
						$insert_sql_data = array(				
							'url_md5' => md5($Artikel_Sprache.'/'.$Artikel_SEO_Bezeichnung),
							'url_text' => $Artikel_Sprache.'/'.$Artikel_SEO_Bezeichnung,
							'language_code' => $Artikel_Sprache,
							'link_type' => '1',   			/2 = kategorie
							'meta_description' => $Artikel_MetaDescription,
							'meta_title' => $Artikel_MetaText,
							'meta_keywords' => $Artikel_MetaKeyword,
							'link_id' =>  $Artikel_ID);		

						xtc_db_perform(TABLE_SEO_URL, $insert_sql_data);
					}
					*/
					
					// Veyton update auf SEO
					/*if (is_file('../../../xtCore/main.php')) include ('../../../xtCore/main.php');
					else if (is_file('../../xtCore/main.php')) include ('../../xtCore/main.php');
					else if (is_file('../xtCore/main.php')) include ('../xtCore/main.php');
					else fwrite($dateihandle, "class main not found \n");*/
				/*	if (is_file('../../../xtFramework/classes/class.seo.php')) include ('../../../xtFramework/classes/class.seo.php');
					else if (is_file('../../xtFramework/classes/class.seo.php')) include ('../../xtFramework/classes/class.seo.php');
					else if (is_file('../xtFramework/classes/class.seo.php')) include ('../xtFramework/classes/class.seo.php');
					else fwrite($dateihandle, "class class.seo.php not found \n");

					$data['mgID']=$Bild_ID;
					$seo = new Seo_modRewrite();
					$seo->_UpdateRecord($class,$id,$language_code,$data,$auto_generate=false, $tmp_copy='false');
				*/
				
				
			
?>
	