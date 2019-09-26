<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_set_images.php														*
*  inkludiert von dmc_write_art.php 										*	
*  Bilder zuordnen												 			*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
13.03.2012
- neu
*/
		if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_images ... ");
		
		if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) { // presta Shops
			// Bildbearbeitung
			if ($Artikel_Variante_Von == "" && !$SkipImages) {
				if (DEBUGGER>=1) fwrite($dateihandle, "presta ...");
				if (is_file('functions/presta_set_images.php')) include_once('functions/presta_set_images.php');	// muesste eigentlich korrekt sein
				else if (is_file('../functions/presta_set_images.php')) include_once('../functions/presta_set_images.php');
				else fwrite($dateihandle, "Fehler in dmc_set_images: Finde functions/presta_set_images.php nicht \n");
				presta_attach_images_to_product($Artikel_Artikelnr, $Artikel_ID, $Artikel_Bezeichung, $no_of_languages, $Artikel_Bilddatei, $dateihandle);
				if (DEBUGGER>=1) fwrite($dateihandle, "done \n");
			}
		} else if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false) { // HHG
			if (DEBUGGER>=1) fwrite($dateihandle, "hhg ...");
			if (is_file('functions/hhg_set_images.php')) include_once('functions/hhg_set_images.php');	// muesste eigentlich korrekt sein
			else if (is_file('../functions/hhg_set_images.php')) include_once('../functions/hhg_set_images.php');
			else fwrite($dateihandle, "Fehler in dmc_set_images: Finde functions/hhg_set_images.php nicht \n");
			hhg_attach_images_to_product($Artikel_Artikelnr, $Artikel_ID, $bilddatei, $dateihandle);
			if (DEBUGGER>=1) fwrite($dateihandle, "done \n");
		} else { // Andere Shops
			// Bildbearbeitung
			if (($Artikel_Variante_Von == "" || strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) && !$SkipImages) {
				$Artikel_ID=dmc_get_id_by_artno($Artikel_Artikelnr);
				if (is_file('functions/set_images.php')) include_once('functions/set_images.php');	// muesste eigentlich korrekt sein
				else if (is_file('../functions/set_images.php')) include_once('../functions/set_images.php');
				else fwrite($dateihandle, "Fehler in dmc_set_images: Finde functions/set_images.php nicht \n");
				//attach_images_to_product($Artikel_Artikelnr, $Artikel_ID, $Artikel_Bilddatei, $dateihandle);
				attach_images_to_product($Artikel_Artikelnr, $Artikel_ID, $Artikel_Bilddatei, dmc_prepare_seo_name($Artikel_Bezeichnung,'de'),$dateihandle);
				if (DEBUGGER>=1) fwrite($dateihandle, "done \n");
			}
		}
		
?>
	