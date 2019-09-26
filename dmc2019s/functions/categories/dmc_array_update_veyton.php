<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_array_update_veyton.php												*
*  inkludiert von dmc_write_cat.php 										*	
*  Veyton Kategorie Array mit Werten fuellen								*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
15.03.2012
- neu
*/
			$sql_data_desc_array = array(
						'categories_name' => $Kategorie_Bezeichnung,
						'categories_description' => $Kategorie_Beschreibung
						);
			
			/*$sql_data_seo_array = array(		
						'url_md5' => md5($language_id.'/'.$Kategorie_SEO_Bezeichnung),
						'url_text' => $language_id.'/'.$Kategorie_SEO_Bezeichnung,
						'language_code' => $language_id,
						'link_type' => '2',   			//2 = kategorie
						'meta_description' => $meta_desc,
						'meta_title' => $meta_desc,
						'meta_keywords' => $meta_desc,
						'link_id' =>  $new_cat_id);	*/
						
?>
	