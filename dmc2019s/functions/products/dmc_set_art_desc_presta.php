<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_set_art_desc_presta.php												*
*  inkludiert von dmc_write_art.php 										*	
*  Artikel Beschreibung für Presta anlegen									*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
13.03.2012
- neu
*/
			$shop_id=STORE_ID; // Standard
			
			//$SHOP_ID=STORE_ID;	// Standard
			//Multishop mit Shop_IDs aus ARTIKEL_STARTSEITE = $Artikel_Presta_Multishop_iD
			if (preg_match('/@/', $Artikel_Presta_Multishop_iD)) {
				$shop_ids = explode ( "@", $Artikel_Presta_Multishop_iD);
			} else {
				$shop_ids[0] = $shop_id;
			}
	        
			for ($anzahl=0;$anzahl< sizeof($shop_ids);$anzahl++) {
	       
				if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_art_desc_presta - Presta Product-Details ArtID $Artikel_ID fuer Shop ".$shop_ids[$anzahl]."\n");
				// Insert Description
                // entry_ex TABLE_PRODUCTS_DESCRIPTION
                // Fill table
				// $seo = dmc_prepare_seo ($Artikel_ID."_".$Artikel_Bezeichnung,"product",$Kategorie_ID,'de');
				//logik geaendert -> keine sonderzeichen
				//presta seo.. funktion fuehrt zur endlosschleife
				$seo = dmc_prepare_seo_name ($Artikel_ID."_".$Artikel_Bezeichnung,'de');
				//$seo = $Artikel_Bezeichnung;
				
				$sql_data_array = array( 
                            'id_product' => $Artikel_ID,
							'id_shop' => $shop_ids[$anzahl],
                            'name' => $Artikel_Bezeichnung,
                            'description' => $Artikel_Langtext,
                            'description_short' => $Artikel_Kurztext,
                            'link_rewrite' => $seo,
                            'description' => $Artikel_Langtext,
                            'meta_description' => $Artikel_MetaDescription,
                            'meta_keywords' => $Artikel_MetaKeyword,
                            'meta_title' => $Artikel_MetaText,
                            'available_now' => $Artikel_Lieferstatus,        // Uebergabe z.B. Auf Lager
                            'available_later' => ''
                );
				// Beschreibung Std setzen fuer alle Sprachen
                if ($no_of_languages==0) $no_of_languages=1;
				for ( $language_id = 1; $language_id <= $no_of_languages; $language_id++ ) {
                    if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_art_desc_presta Beschreibung Sprache ".$language_id."\n");
                    $sql_data_array['id_lang'] = $language_id;
					// delete first
					// dmc_sql_delete(TABLE_PRODUCTS_DESCRIPTION, "id_lang = ".$language_id." AND id_shop=".$shop_id." AND id_product='".$Artikel_ID."'");
					dmc_sql_insert_array(DB_TABLE_PREFIX . "product_lang", $sql_data_array);
                }
				// wenn standard sprache nicht in for schleife
				if (STD_LANGUAGE_ID>$no_of_languages) {
					$sql_data_array['id_lang'] = STD_LANGUAGE_ID;
					// delete first
					// dmc_sql_delete(TABLE_PRODUCTS_DESCRIPTION, "id_lang = ".STD_LANGUAGE_ID." AND id_shop=".$shop_id." AND id_product='".$Artikel_ID."'");
					dmc_sql_insert_array(DB_TABLE_PREFIX . "product_lang", $sql_data_array);
				}
			} // end for shopids	
?>
	