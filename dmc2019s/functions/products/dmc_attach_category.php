<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_attach_category.php													*
*  inkludiert von dmc_write_art.php 										*	
*  Spezielle Funktionen zur Kategoriezuordnung 								*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
12.03.2012
- neu
*/

			// ÜK logik geaendert
			if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
				$artikel_to_category_sql_data_array = array(
					'id_category' => $Kategorie_ID,
					'id_product' => $Artikel_ID,
					//'position' => $position
					'position' => 0
				);
					if (DEBUGGER>=1) fwrite($dateihandle, "presta dmc_attach_cat in ".TABLE_CATEGORIES_PRODUCTS."\n");
			// ÜK in der configurable_shop_presta.php constante (TABLE_CATEGORIES_PRODUCTS) geaendert
				xtc_db_perform(TABLE_CATEGORIES_PRODUCTS, $artikel_to_category_sql_data_array);
			}
	
?>
	