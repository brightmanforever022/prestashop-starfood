<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_deactivate_veyton_slaves.php											*
*  inkludiert von dmc_write_art.php 										*	
*  Artikel  Variablen-Unterartikel deaktivieren								*
*  Copyright (C) 2018 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
27.06.2018
- neu
*/
	
		if (DEBUGGER>=1) fwrite($dateihandle, "dmc_deactivate_veyton_slaves\n");
		$cmd = "select products_id from " . TABLE_PRODUCTS." WHERE " . "products_master_model='".$Artikel_Artikelnr."'; ";  ;
		$products_query = dmc_db_query($cmd);
		while ($products = dmc_db_fetch_array($products_query))
		{
			$query="UPDATE TABLE_PRODUCTS SET products_status=0 WHERE products_id=".$products['products_id'];
			dmc_db_query($query);
		}
	
?>
	