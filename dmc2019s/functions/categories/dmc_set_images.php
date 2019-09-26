<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_set_images.php														*
*  inkludiert von dmc_write_cat.php 										*	
*  Bilder zuordnen												 			*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
15.03.2012
- neu
*/
			if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_images \n");
		
			include_once(DMC_FOLDER.'/functions/set_images.php');
			// Bilddateiname ueberpruefen und ggfls korregieren
			$Kategorie_Bild=dmc_validate_image($Kategorie_Bild);
			if ($Kategorie_Bild!="" && (is_file(DIR_FS_CATALOG.DIR_WS_THUMBNAIL_IMAGES.$Kategorie_Bild) 
				|| is_file(DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES.$Kategorie_Bild))) 
				attach_images_to_category($new_cat_id, $Kategorie_Bild, $dateihandle);
		
?>
	