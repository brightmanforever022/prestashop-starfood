<?php

/************************************************
*                                            	*
*  dmConnector for shops						*
*  hhg_special_functions.php					*
*  HHG Funktionen am Ende der Produktanlage 	*
*  Copyright (C) 2011 DoubleM-GmbH.de			*
*                                               *
*************************************************/

	 if (DEBUGGER>=1) fwrite($dateihandle, " hhg_special_functions.php fuer artikelanlage \n");	
	
	//HHG -> Artikel  dem Shop zuordnen 
	function HHGSetProduct2Store($Artikel_ID)
	{
		global $dateihandle;
		 	if (STORE_ALL)
					 $sql_data_array = array(
			          'products_id' => $Artikel_ID,
			          'store_all' => '1',
			          'store_1' => 0);
			else
					 $sql_data_array = array(
			          'products_id' => $Artikel_ID,
			          'store_all' => 0,
			          'store_1' => 1);
			
			if (!$exists) 
			    xtc_db_perform(TABLE_MS_PRODUCTS_TO_STORE, $sql_data_array);
			else 
				xtc_db_perform(TABLE_MS_PRODUCTS_TO_STORE, $sql_data_array, 'update', "products_id = '$Artikel_ID'");
	} // end function HHGSetProduct2Store()
	  
	//HHG -> Produkt Calkulationstabelle fuellen 
	function HHGSetProductsCalculation($Artikel_ID)
	{
		global $dateihandle;
		// Standard products_id  store_id  multiplier  multiplier_0  multiplier_1  multiplier_2  multiplier_3 
		    $sql_data_array = array(
			          'products_id' => $Artikel_ID,
			          'multiplier' => '0.00',
					  'multiplier_1' => '0.00',
					  'multiplier_2' => '0.00',
					  'multiplier_3' => '0.00');
			if (STORE_ID != "")
					$sql_data_array['store_id'] = STORE_ID;
			else 
				$sql_data_array['store_id'] = 1;
					  
			if (!$exists) 
			    xtc_db_perform(TABLE_PRODUCTS_CALCULATION, $sql_data_array);
			else 
				xtc_db_perform(TABLE_PRODUCTS_CALCULATION, $sql_data_array, 'update', "products_id = '$Artikel_ID'");
	} // end function HHGSetProductsCalculation($Artikel_ID)
	
?>