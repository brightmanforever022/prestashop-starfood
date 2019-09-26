<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_array_create_veyton.php												*
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
						'categories_id' => $new_cat_id,
						'language_code' => $language_id,
						'categories_name' => $Kategorie_Bezeichnung,
						'categories_description' => $Kategorie_Beschreibung,
			//			'categories_meta_description'=> $Kategorie_ID
						);
			
			if (SHOPSYSTEM_VERSION>=4.2) $sql_data_desc_array['categories_store_id'] = SHOP_ID;		// Ab veyton 4.2 
			
			$sql_data_seo_array = array(		
						'url_md5' => md5($language_id.'/'.$Kategorie_SEO_Bezeichnung.$new_cat_id),
						'url_text' => $language_id.'/'.$Kategorie_SEO_Bezeichnung.$new_cat_id,
						'language_code' => $language_id,
						'link_type' => '2',   			//2 = kategorie
						'meta_description' => $Kategorie_ID,
						'meta_title' => $meta_desc,
						'meta_keywords' => $Kategorie_ID,
						'link_id' =>  $new_cat_id);	
			
			if (SHOPSYSTEM_VERSION>=4.2) $sql_data_seo_array['store_id'] = SHOP_ID;		// Ab veyton 4.2 
				
?>
	