<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_array_create_standard.php											*
*  inkludiert von dmc_write_cat.php 										*	
*  Kategorie Array mit Werten fuellen										*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
15.03.2012
- neu
*/

			fwrite($dateihandle, "dmc_array_create_standard \n");
				
			if(strpos(strtolower(SHOPSYSTEM), 'osc') !== false) { 
				// oscommerce
				$insert_sql_data = array('parent_id' => $Kategorie_Vater_ID,
										'sort_order' => $Sortierung);
			} else {
				$insert_sql_data = array('parent_id' => $Kategorie_Vater_ID,
										'categories_status' => $Aktiv,
										'sort_order' => $Sortierung);
			}
			// Details
			if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false && strpos(strtolower(SHOPSYSTEM), 'osc') === false) { 
				fwrite($dateihandle, "dmc_array_create_standard GROUP_PERMISSION_\n");

				if (GM_SHOW_QTY_INFO != "" && GM_SHOW_QTY_INFO != "false" && (SHOPSYSTEM == 'gambiogx' OR SHOPSYSTEM == 'gambio'))
					$insert_sql_data['gm_show_qty_info'] = GM_SHOW_QTY_INFO;
					for($gruppe = 0; $gruppe <= 10; $gruppe++) {     //  durchlaufen	     
						if (defined(constant('GROUP_PERMISSION_' . $gruppe)))
						if (constant('GROUP_PERMISSION_' . $gruppe)!=''){  	
							 $insert_sql_data[constant('GROUP_PERMISSION_' . $gruppe)] = constant('GROUP_PERMISSION_' . $gruppe);	
						} // end if
					} // END FOR	
			}
			
			if (strpos(strtolower(SHOPSYSTEM), 'commerceseo') !== false) {
				// Zugriffsrechte setzen
				for($gruppe = 0; $gruppe <= 15; $gruppe++) {     //  durchlaufen	     
						if (defined(constant('GROUP_PERMISSION_' . $gruppe)))
						if (constant('GROUP_PERMISSION_' . $gruppe)!=''){  	
							 $insert_sql_data[constant('GROUP_PERMISSION_' . $gruppe)] = constant('GROUP_PERMISSION_' . $gruppe);	
						} // end if
				} // END FOR	
			}
			
			// Weitere Details nicht fuer zencart und oscommerce
			if(strpos(strtolower(SHOPSYSTEM), 'zencart') === false  && strpos(strtolower(SHOPSYSTEM), 'osc') === false) { 
				if (CATEGORIES_TEMPLATE != "")
					$insert_sql_data['categories_template'] = CATEGORIES_TEMPLATE;
				if (LISTING_TEMPLATE != "")
					$insert_sql_data['listing_template'] = LISTING_TEMPLATE;
				if (PRODUCTS_SORTING != "")
					$insert_sql_data['products_sorting'] = PRODUCTS_SORTING;
				if (PRODUCTS_SORTING2 != "")
					$insert_sql_data['products_sorting2'] = PRODUCTS_SORTING2;
			}

			if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) // Standard array fuellen
			{
				// wohl ab 4.1
				if ($Kategorie_Vater_ID==0) 
					$is_top = 1;
				else 
					$is_top = 0;
					
				$insert_sql_data['top_category']=$is_top´; 	// wohl ab 4.1
			};
						
?>
	