<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for Magento shop												*
*  dmc_group_prices_arrays.php												*
*  inkludiert von dmc_write_art.php 										*
*  Arrays fuer Kundengruppenpreise setzen									*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
02.03.2012
- neu
*/
 
		// Kundenpreise setzen?
		$Kundengruppenpreise = Array();
		if (CUST_PRICE_GROUP1 <> '' && $Artikel_Preis1<>'' && $Artikel_Preis1<>'0') { 
			$Kundengruppenpreise[] = array(
										    'website'           => 'all',
										    'customer_group_id' => CUST_PRICE_GROUP1,
										    'qty'               => 1,
										    'price'             => $Artikel_Preis1
										);
		}
		if (CUST_PRICE_GROUP2 <> '' && $Artikel_Preis2<>'' && $Artikel_Preis2<>'0') {
			$Kundengruppenpreise[] = array(
										    'website'           => 'all',
										    'customer_group_id' => CUST_PRICE_GROUP2,
										    'qty'               => 1,
										    'price'             => $Artikel_Preis2
										);
		}
		if (CUST_PRICE_GROUP3 <> '' && $Artikel_Preis3<>'' && $Artikel_Preis3<>'0') {
			$Kundengruppenpreise[] = array(
										    'website'           => 'all',
										    'customer_group_id' => CUST_PRICE_GROUP3,
										    'qty'               => 1,
										    'price'             => $Artikel_Preis3
										);
		}
		if (CUST_PRICE_GROUP4 <> '' && $Artikel_Preis4<>'' && $Artikel_Preis4<>'0') {
			$Kundengruppenpreise[] = array(
										    'website'           => 'all',
										    'customer_group_id' => CUST_PRICE_GROUP4,
										    'qty'               => 1,
										    'price'             => $Artikel_Preis4
										);	
		}
		
	
?>
	