<?php
/************************************************
*                                            	*
*  dmConnector for presta						*
*  presta_functions.php							*
*  Allgemeine Funktionen speziell fuer Presta 	*
*  Copyright (C) 2011 DoubleM-GmbH.de			*
*                                               *
*************************************************/

	//  presta Laender ISO Code nach Presta Country ID ermitteln	
	function presta_get_isocode_by_id($country_id, $dateihandle)
	{	
		if (DEBUGGER>=1) fwrite($dateihandle, " presta_get_isocode_by_id fuer country_id $country_id \n");	
		$temp_sql = "SELECT ca.iso_code ".
					"FROM " . TABLE_COUNTRIES . " AS ca ".
					"WHERE ca.id_country=".$country_id;
		$temp_query = dmc_db_query ($temp_sql);
	    if (($temp_query) && ($temp_data = dmc_db_fetch_array($temp_query)))
	    {
			$isocode=$temp_data['iso_code'];
		} else {
			// Standard
			$isocode='DE';
		}
		return $isocode; 
	} // end function presta_get_isocode_by_id
	
	//  presta Laender Bezeichnung nach Presta Country ID ermitteln	
	function presta_get_country_by_id($country_id, $language_id, $dateihandle)
	{	
		if (DEBUGGER>=1) fwrite($dateihandle, " presta_get_country_by_id fuer country_id $country_id = ");	
		$temp_sql = "SELECT ca.name ".
					"FROM " . TABLE_COUNTRIES_DESC . " AS ca ".
					"WHERE ca.id_country=".$country_id." AND id_lang=".$language_id;
		$temp_query = dmc_db_query ($temp_sql);
	    if (($temp_query) && ($temp_data = dmc_db_fetch_array($temp_query)))
	    {
			$country=$temp_data['name'];
		} else {
			// Standard
			$country='Germany';
		}
		if (DEBUGGER>=1) fwrite($dateihandle, " $country \n");	
		
		return $country; 
	} // end function presta_get_country_by_id
	
	//  presta Currency Bezeichung nach Presta Currency ID ermitteln	
	function presta_get_currency_by_id($currency_id, $dateihandle)
	{	
		if (DEBUGGER>=1) fwrite($dateihandle, " presta_get_currency_by_id fuer country_id $currency_id \n");	
		$temp_sql = "SELECT ca.name ".
					"FROM " . TABLE_CURRENCY . " AS ca ".
					"WHERE ca.id_currency=".$currency_id;
		$temp_query = dmc_db_query ($temp_sql);
	    if (($temp_query) && ($temp_data = dmc_db_fetch_array($temp_query)))
	    {
			$currency=$temp_data['name'];
		} else {
			// Standard
			$currency='EUR';
		}
		return $currency; 
	} // end function presta_get_currency_by_id
	
	//  presta Currency Umrechnungsfaktor nach Presta Currency ID ermitteln	
	function presta_get_currency_rate_by_id($currency_id, $dateihandle)
	{	
		if (DEBUGGER>=1) fwrite($dateihandle, " presta_get_currency_rate_by_id fuer country_id $currency_id \n");	
		$temp_sql = "SELECT ca.conversion_rate ".
					"FROM " . TABLE_CURRENCY . " AS ca ".
					"WHERE ca.id_currency=".$currency_id;
		$temp_query = dmc_db_query ($temp_sql);
	    if (($temp_query) && ($temp_data = dmc_db_fetch_array($temp_query)))
	    {
			$currency=$temp_data['conversion_rate'];
		} else {
			// Standard
			$currency=1;
		}
		return $currency; 
	} // end function presta_get_currency_rate_by_id
	
?>