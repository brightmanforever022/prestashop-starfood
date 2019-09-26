<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_array_update_standard.php											*
*  inkludiert von dmc_write_cat.php 										*	
*  Kategorie Array mit Werten fuellen										*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
15.03.2012
- neu
*/
			$update_sql_data = array(
									//'parent_id' => $Kategorie_Vater_ID,
									'categories_status' => $Aktiv,
									// 'sort_order' => $Sortierung
									);
			// Details
			if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false) { 
				if (GM_SHOW_QTY_INFO != "" && GM_SHOW_QTY_INFO != "false" && (SHOPSYSTEM == 'gambiogx' OR SHOPSYSTEM == 'gambio'))
					$update_sql_data['gm_show_qty_info'] = GM_SHOW_QTY_INFO;
					for($gruppe = 0; $gruppe <= 15; $gruppe++) {     //  durchlaufen	     
						if (defined(constant('GROUP_PERMISSION_' . $gruppe)))
						if (constant('GROUP_PERMISSION_' . $gruppe)!=''){  	
							 $update_sql_data[constant('GROUP_PERMISSION_' . $gruppe)] = constant('GROUP_PERMISSION_' . $gruppe);	
						} // end if
					} // END FOR	
			}
			
			if (strpos(strtolower(SHOPSYSTEM), 'commerceseo') !== false) {
				for($gruppe = 0; $gruppe <= 10; $gruppe++) {     //  durchlaufen	     
						if (defined(constant('GROUP_PERMISSION_' . $gruppe)))
						if (constant('GROUP_PERMISSION_' . $gruppe)!=''){  	
							 $insert_sql_data[constant('GROUP_PERMISSION_' . $gruppe)] = constant('GROUP_PERMISSION_' . $gruppe);	
						} // end if
				} // END FOR	
			}
			
			// Weitere Details
			if(strpos(strtolower(SHOPSYSTEM), 'zencart') === false) {
				
				if (CATEGORIES_TEMPLATE != "")
					$update_sql_data['categories_template'] = CATEGORIES_TEMPLATE;
				if (LISTING_TEMPLATE != "")
					$update_sql_data['listing_template'] = LISTING_TEMPLATE;
				if (PRODUCTS_SORTING != "")
					$update_sql_data['products_sorting'] = PRODUCTS_SORTING;
				if (PRODUCTS_SORTING2 != "")
					$update_sql_data['products_sorting2'] = PRODUCTS_SORTING2;
			}
		
?>
	