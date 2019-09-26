<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_array_update_desc_standard.php										*
*  inkludiert von dmc_write_cat.php 										*	
*  Kategorie Beschreibung Array mit Werten fuellen							*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
15.03.2012
- neu
*/
		
			// 4 Description
			$sql_update_desc_array = array(
					  //'categories_id' => $new_cat_id,
					  //'language_id' => $language_id,
					  'categories_name' => $Kategorie_Bezeichnung,
					  'categories_description' => $Kategorie_Beschreibung,
					  'categories_meta_description'=> $Kategorie_ID
					  );
	
		
			// Gambio ab GX2 kategroei Überschrift 
			if (strpos(strtolower(SHOPSYSTEM_VERSION), 'gx2') !== false) {
				$sql_update_desc_array['categories_heading_title'] = $categories_heading_title;
			}
						
	
	
?>
	