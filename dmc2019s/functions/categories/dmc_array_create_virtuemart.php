<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_array_create_virtuemart.php											*
*  inkludiert von dmc_write_cat.php 										*	
*  Kategorie Array mit Werten fuellen										*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
13.05.2015
- neu
*/

			// jo340_virtuemart_categories
			$insert_sql_data = array('virtuemart_category_id' => $new_cat_id,
									'virtuemart_vendor_id' => 1,
 									'category_template' => '0',
 									'category_layout' => '0',
 									'category_product_layout' => '0',
 									'products_per_row' => 0,
 									'limit_list_step' => '0',
 									'limit_list_initial' => 0,
 									'hits' => 0,
 									'metarobot' => '',
 									'metaauthor' => '',
 									'ordering' => $Kategorie_Sortierung,
 									'shared' => 0,
 									'published' => $Aktiv,
 									'created_on' => 'now()',
 									'created_by' => 585,
 									'modified_on' => 'now()',
 									'modified_by' => 585,
 									'locked_on' => 'now()',
 									'locked_by' => 0,
									);

			$slug=dmc_prepare_seo_name($Kategorie_Bezeichnung,'de');
			
			// jo340_virtuemart_categories_de_de
			$insert_sql_desc_data = array('virtuemart_category_id' => $new_cat_id,
									'category_name' => $Kategorie_Bezeichnung,
 									'category_description' => $Kategorie_Beschreibung,
									'metadesc' => $Kategorie_MetaD,
									'metakey' => $Kategorie_ID,
									// 'customtitle' => $Kategorie_Bezeichnung,
									'slug' => $slug,
									);
										 	 
			// jo340_virtuemart_category_categories
			$insert_sql_tree_data = array('id' => $new_cat_id,
									'category_parent_id' => $Kategorie_Vater_ID,
 									'category_child_id' => $new_cat_id,
									'ordering' => $Kategorie_Sortierung,
 									);			
echo "du";
									
									
?>
	