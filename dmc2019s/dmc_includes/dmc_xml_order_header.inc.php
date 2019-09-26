<?php
/************************************************
*                                            	*
*  dmConnector Export							*
*  dmc_xml_order_header.inc.php					*
*  XML Ausgabe des Anfangs der XML Datei		*
*  Einbinden in dmconnector_export.php			*
*  Copyright (C) 2011 DoubleM-GmbH.de			*
*                                               *
*************************************************/

	if ($billing_country=='Germany' || substr($billing_country,0,7)=='Deutsch') $billing_country_iso_code="DE"; 
	if ($customers_country=='Germany' || substr($billing_country,0,7)=='Deutsch') $customers_country_iso_code="DE";
	if ($delivery_country=='Germany' || substr($billing_country,0,7)=='Deutsch') $delivery_country_iso_code="DE"; 
					
	$schema  .= '<ORDER version="1.0" type="standard">'. "\n" .
				'<ORDER_INFO>' . "\n" .
				 '<ORDER_HEADER>' . "\n" .
					 '<ORDER_ID>' . $orders_id . '</ORDER_ID>' . "\n" .
					 '<CUSTOMER_ID>' . $customers_id . '</CUSTOMER_ID>' . "\n" .
					 '<CUSTOMER_CID>' . $customers_cid . '</CUSTOMER_CID>' . "\n" .
					 '<CUSTOMER_GROUP>' . $customers_group . '</CUSTOMER_GROUP>' . "\n" .
					 '<CUSTOMER_FOREIGN>' . $ausland . '</CUSTOMER_FOREIGN>' . "\n" .
					 '<CUSTOMER_EU>' . $eg_ausland . '</CUSTOMER_EU>' . "\n" .
					 '<CUSTOMER_NET>' . $nettokunde . '</CUSTOMER_NET>' . "\n" .
					 '<ACTUAL_DATE>' . date("d.m.Y") . '</ACTUAL_DATE>' . "\n" .
					 '<ACTUAL_DATE_YMD>' . date("Y-m-d") . '</ACTUAL_DATE_YMD>' . "\n" .
					 '<ORDER_DATE_KW>' .  date("W",time()) . '</ORDER_DATE_KW>' . "\n" .
					 '<ORDER_DATE>' . $orders_date . '</ORDER_DATE>' . "\n" .
					 '<ORDER_STATUS>' . $orders_status . '</ORDER_STATUS>' . "\n" .
					 '<ORDER_IP>' . $orders_ip . '</ORDER_IP>' . "\n" .
					 '<ORDER_CURRENCY>' . $currency_code . '</ORDER_CURRENCY>' . "\n" .
					 '<ORDER_CURRENCY_VALUE>' . $currency_value . '</ORDER_CURRENCY_VALUE>' . "\n" .
				 '</ORDER_HEADER>' . "\n" .					 
				 '<CUSTOMERS_ADDRESS>' . "\n" .
					 '<SALUTATION>' . $customers_title . '</SALUTATION>' . "\n" .
					 '<CUSTOMER_ID>' . $customers_id . '</CUSTOMER_ID>' . "\n" .
					 '<CUSTOMER_ADDRESS_ID>' . $customers_address_id . '</CUSTOMER_ADDRESS_ID>' . "\n" .
					 '<COMPANY>' . umlaute_order_export(html2ascii($customers_company)) . '</COMPANY>' . "\n" .
					 '<VATID>' . $customers_ustid . '</VATID>' . "\n" .
					 '<FIRSTNAME>' . umlaute_order_export(html2ascii($customers_firstname)) . '</FIRSTNAME>' . "\n" .
					 '<LASTNAME>' . umlaute_order_export(html2ascii($customers_lastname)) . '</LASTNAME>' . "\n" .
					 '<STREET>' . umlaute_order_export(html2ascii($customers_address1)) . '</STREET>' . "\n" .
					 '<STREET2>' . umlaute_order_export(html2ascii($customers_address2)) . '</STREET2>' . "\n" .
					 '<ZIP>' . $customers_zip . '</ZIP>' . "\n" .
					 '<CITY>' . umlaute_order_export(html2ascii($customers_city)) . '</CITY>' . "\n" .
					 '<SUBURB>' . umlaute_order_export(html2ascii($customers_subur)) . '</SUBURB>' . "\n" .
					 '<STATE>' . umlaute_order_export(html2ascii($customers_state)) . '</STATE>' . "\n" .
					 '<COUNTRY>' . umlaute_order_export(html2ascii($customers_country)) . '</COUNTRY>' . "\n" .
					 '<COUNTRY_ISO_CODE>' . html2ascii($customers_country_iso_code) . '</COUNTRY_ISO_CODE>' . "\n" .
					 '<TELEPHONE>' . $customers_phone . '</TELEPHONE>' . "\n" . 
					 '<TELEPHONE2>' . $customers_phone_mobile . '</TELEPHONE2>' . "\n" . 
					 '<TELEFAX>'.$customers_fax.'</TELEFAX>' . "\n" . 
					 '<EMAIL>' . umlaute_order_export(html2ascii($customers_email_address)) . '</EMAIL>' . "\n" . 
					 '<BIRTHDAY>' . $customers_dob . '</BIRTHDAY>' . "\n" .
				 '</CUSTOMERS_ADDRESS>' . "\n" .					 
				 '<BILLING_ADDRESS>' . "\n" .
					 '<BILLING_ADDRESS_ID>' . $billing_address_id . '</BILLING_ADDRESS_ID>' . "\n" .
					 '<SALUTATION>' . $billing_title . '</SALUTATION>' . "\n" .
					 '<COMPANY>' . umlaute_order_export(html2ascii($billing_company)) . '</COMPANY>' . "\n" .
					 '<VATID>' . $billing_ustid . '</VATID>' . "\n" .
					 '<FIRSTNAME>' . umlaute_order_export(html2ascii($billing_firstname)) . '</FIRSTNAME>' . "\n" .
					 '<LASTNAME>' . umlaute_order_export(html2ascii($billing_lastname)) . '</LASTNAME>' . "\n" .
					 '<STREET>' . umlaute_order_export(html2ascii($billing_address1)) . '</STREET>' . "\n" .
					 '<STREET2>' . umlaute_order_export(html2ascii($billing_address2)) . '</STREET2>' . "\n" .
					 '<ZIP>' . $billing_zip . '</ZIP>' . "\n" .
					 '<CITY>' . umlaute_order_export(html2ascii($billing_city)) . '</CITY>' . "\n" .
					 '<SUBURB>' . umlaute_order_export(html2ascii($billing_subur)) . '</SUBURB>' . "\n" .
					 '<STATE>' . umlaute_order_export(html2ascii($billing_state)) . '</STATE>' . "\n" .
					 '<COUNTRY>' . umlaute_order_export(html2ascii($billing_country)) . '</COUNTRY>' . "\n" .
					 '<COUNTRY_ISO_CODE>' . html2ascii($billing_country_iso_code) . '</COUNTRY_ISO_CODE>' . "\n" .
					 '<TELEPHONE>' . $billing_phone . '</TELEPHONE>' . "\n" . 
					 '<TELEPHONE2>' . $billing_phone_mobile . '</TELEPHONE2>' . "\n" . 
					 '<TELEFAX></TELEFAX>' . "\n" . 
					 '<EMAIL>' . umlaute_order_export(html2ascii($billing_email_address)) . '</EMAIL>' . "\n" . 
				 '</BILLING_ADDRESS>' . "\n" .
				 '<DELIVERY_ADDRESS>' . "\n" .
					 '<DELIVERY_ADRESS_TYPE>' . $delivery_adress_type . '</DELIVERY_ADRESS_TYPE>' . "\n" .
					 '<delivery_ADDRESS_ID>' . $delivery_address_id . '</delivery_ADDRESS_ID>' . "\n" .
					 '<SALUTATION>' . $delivery_title . '</SALUTATION>' . "\n" .
					 '<COMPANY>' . umlaute_order_export(html2ascii($delivery_company)) . '</COMPANY>' . "\n" .
					 '<VATID>' . $delivery_ustid . '</VATID>' . "\n" .
					 '<FIRSTNAME>' . umlaute_order_export(html2ascii($delivery_firstname)) . '</FIRSTNAME>' . "\n" .
					 '<LASTNAME>' . umlaute_order_export(html2ascii($delivery_lastname)) . '</LASTNAME>' . "\n" .
					 '<STREET>' . umlaute_order_export(html2ascii($delivery_address1)) . '</STREET>' . "\n" .
					 '<STREET2>' . umlaute_order_export(html2ascii($delivery_address2)) . '</STREET2>' . "\n" .
					 '<ZIP>' . $delivery_zip . '</ZIP>' . "\n" .
					 '<CITY>' . umlaute_order_export(html2ascii($delivery_city)) . '</CITY>' . "\n" .
					 '<SUBURB>' . umlaute_order_export(html2ascii($delivery_subur)) . '</SUBURB>' . "\n" .
					 '<STATE>' . umlaute_order_export(html2ascii($delivery_state)) . '</STATE>' . "\n" .
					 '<COUNTRY>' . umlaute_order_export(html2ascii($delivery_country)) . '</COUNTRY>' . "\n" .
					 '<COUNTRY_ISO_CODE>' . html2ascii($delivery_country_iso_code) . '</COUNTRY_ISO_CODE>' . "\n" .
					 '<TELEPHONE>' . $delivery_phone . '</TELEPHONE>' . "\n" . 
					 '<TELEPHONE2>' . $delivery_phone_mobile . '</TELEPHONE2>' . "\n" . 
					 '<TELEFAX></TELEFAX>' . "\n" . 
					 '<EMAIL>' . umlaute_order_export(html2ascii($delivery_email_address)) . '</EMAIL>' . "\n" . 
				 '</DELIVERY_ADDRESS>' . "\n" .
				 '<PAYMENT>' . "\n" .
					'<PAYMENT_METHOD>' .  $payment_method  . '</PAYMENT_METHOD>'  . "\n" .
					'<PAYMENT_CLASS>' . $payment_class . '</PAYMENT_CLASS>'  . "\n".
					'<PAYMENT_COST>' . (0+$payment_costs). '</PAYMENT_COST>'  . "\n".
					'<PAYMENT_BANKTRANS_BNAME>' . umlaute_order_export(html2ascii($bank_name)) . '</PAYMENT_BANKTRANS_BNAME>' . "\n" .
					'<PAYMENT_BANKTRANS_BLZ>' . html2ascii($bank_blz) . '</PAYMENT_BANKTRANS_BLZ>' . "\n" .
					'<PAYMENT_BANKTRANS_NUMBER>' . html2ascii($bank_kto) . '</PAYMENT_BANKTRANS_NUMBER>' . "\n" .
					'<PAYMENT_BANKTRANS_OWNER>' . umlaute_order_export(html2ascii($bank_inh)) . '</PAYMENT_BANKTRANS_OWNER>' . "\n" .
					'<PAYMENT_BANKTRANS_STATUS>' . umlaute_order_export(html2ascii($bank_stat)) . '</PAYMENT_BANKTRANS_STATUS>' . "\n".
				 '</PAYMENT>' . "\n" . 
				 '<SHIPPING>' . "\n" . 
					 '<SHIPPING_METHOD>' .  $shipping_method. '</SHIPPING_METHOD>'  . "\n" .
					 '<SHIPPING_CLASS>' . $shipping_class. '</SHIPPING_CLASS>'  . "\n" .
					 '<SHIPPING_COSTS>' .(0+$rcm_versandkosten). '</SHIPPING_COSTS>'  . "\n".
					 '<SHIPPING_COSTS_NET>' .(0+$rcm_versandkosten_net). '</SHIPPING_COSTS_NET>'  . "\n".
					 '<SHIPPING_TOTAL_VAT>'.(0+$rcm_versandkosten_tax).'</SHIPPING_TOTAL_VAT>'  . "\n".
					 '<SHIPPING_VAT>19</SHIPPING_VAT>'  . "\n".
				 '</SHIPPING>' . "\n" .                      
				 '<ORDER_PRODUCTS>' . "\n";
	
	
?>