<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_update_art_seo.php													*
*  inkludiert von dmc_write_art.php 										*	
*  Artikel SEOs Tabellen aktualisieren										*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
13.03.2012
- neu
21.03.2012
- commerceSEO ergänzt
*/
			if (strpos(strtolower(SHOPVERSION), 'veyton') !== false || strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
				// Veyton -> SEO TABELLE MUSS GEFUELLT WERDEN, sonst wird es nicht angezeigt
				if (is_file('userfunctions/products/dmc_update_art_seo_veyton.php')) include ('functions/products/dmc_update_art_seo_veyton.php');
				else include ('functions/products/dmc_update_art_seo_veyton.php');	
			} 
			
			if (strpos(strtolower(SHOPVERSION), 'commerceseo') !== false || strpos(strtolower(SHOPSYSTEM), 'commerceseo') !== false) {
				// commerce:SEO -> SEO TABELLE MUSS GEFUELLT WERDEN, sonst wird es nicht angezeigt
				if (is_file('userfunctions/products/dmc_update_art_seo_commerceseo.php')) include ('functions/products/dmc_update_art_seo_commerceseo.php');
				else include ('functions/products/dmc_update_art_seo_commerceseo.php');	
			}

?>
	