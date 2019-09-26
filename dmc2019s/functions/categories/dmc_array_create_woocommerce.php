<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_array_create_woocommerce.php												*
*  inkludiert von dmc_write_cat.php 										*	
*  Presta Kategorie Array mit Werten fuellen								*
*  Copyright (C) 2014 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
10.01.2014
- neu
*/
			$insert_sql_data = array(	// term_id -> create
										'name' => $Kategorie_Bezeichnung,
										'slug' => $Kategorie_ID,		// seo wird hier mit der KategorieID gefuellt
										'term_group' => 0
									);
			
			$insert_sql_grp_data = array(	// term_taxonomy_id -> create
											// term_id -> is created before
										'taxonomy' => 'product_cat',
										'description' => $Kategorie_Bezeichnung.'<br>'.$Kategorie_Beschreibung,
										'parent' => $Kategorie_Vater_ID,
										'count' => 0
										);			
									 
			/*$insert_sql_data = array(	
										'id_parent' => $Kategorie_Vater_ID,
										'level_depth' => $kat_ebene,
										'active' => 1,
										'date_add' => 'now()',
										'date_upd' => 'now()'
									);
			
			// Category Group
			$insert_sql_grp_data = array(	'id_category' => $new_cat_id,
											'id_group' => 1);
			
			// Shop
			$insert_sql_shop_data = array(	'id_category' => $new_cat_id,
											'id_shop' => 1,
											'position' => 1);
			
			// 4 Description
			if ($meta_desc=='')
			$meta_desc = $Kategorie_Bezeichnung;
			$insert_sql_desc_data = array(	
										'id_category' => $new_cat_id,
										'id_lang' => $language_id,
										'name' => $Kategorie_Bezeichnung,
										'description' => $Kategorie_Beschreibung,
										'link_rewrite' => $language_id.'/'.$seo,
										'meta_description' => $Kategorie_ID,
										'meta_title' => $meta_desc,
										'meta_keywords' => $meta_desc);	
				*/						
			
			
?>
	