<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_delete_veyton_slaves.php												*
*  inkludiert von dmc_write_art.php 										*	
*  Artikel  Variablen-Unterartikel loeschen									*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
12.03.2012
- neu
*/
	
		if (DEBUGGER>=1) fwrite($dateihandle, "dmc_delete_veyton_slaves\n");
		$cmd = "select products_id from " . TABLE_PRODUCTS." WHERE " . "products_master_model='".$Artikel_Artikelnr."'; ";  ;
		$products_query = dmc_db_query($cmd);
		while ($products = dmc_db_fetch_array($products_query))
		{
			dmc_delete_product($products['products_id']);
		}
	
?>
	