<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_run_extensions.php													*
*  inkludiert von dmc_write_art.php 										*	
*  extensions der shops ausfuehren								 			*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
13.03.2012
- neu
*/
		if (DEBUGGER>=1) fwrite($dateihandle, "dmc_run_extensions \n");
		
		// SEO Tool von Bluegate
		// Einbinden, wenn existiert
		if (file_exists(DIR_FS_INC.'bluegate_seo.inc.php')) { 
			if (DEBUGGER>=1) fwrite($dateihandle, "*SEO Tool von Bluegate initialisieren* (".$Artikel_ID.") \n");
			// *************************** BLUEGATE SUMA OPTIMIZER ************************* //
			require_once (DIR_FS_INC.'bluegate_seo.inc.php');
			!$bluegateSeo ? $bluegateSeo = new BluegateSeo() : false;
			// Wenn Artikel existiert Insert  bluegate_seo_url Table
			//if (exists==0) 
				// Update bluegate_seo_url Table
			//	$bluegateSeo->updateSeoDBTable('product','insert', $Artikel_ID);	
			//else
				$bluegateSeo->updateSeoDBTable('product','update', $Artikel_ID);	
				if (DEBUGGER>=1) fwrite($dateihandle, " ... für Artikel ".$Artikel_ID."* \n");			
		} // end if // SEO Tool von Bluegate
		
?>
	