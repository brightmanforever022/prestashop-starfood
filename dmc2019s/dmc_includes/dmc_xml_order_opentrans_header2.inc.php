<?php
/************************************************
*                                            	*
*  dmConnector Export							*
*  dmc_xml_order_header.inc.php					*
*  XML Ausgabe des Anfangs der XML Datei		*
*  Einbinden in dmconnector_export.php			*
*  Copyright (C) 2011 DoubleM-GmbH.de			*
*                                               *
* 07.02.2013 - 	// Lieferanschrift Werte nur angeben, wenn gefüllt, sonnst komplett leer *
*************************************************/
//		fwrite($dateihandle, "11 dmc_xml_order_header\n");

	if (SHOPSYSTEM == 'shopware') {
		
		$bestellnummer=$orders['order_number'];
		$delivery_zusatz_1=$orders['delivery_zusatz_1'];
		$delivery_zusatz_2=$orders['delivery_zusatz_2'];
		$billing_zusatz_1=$orders['billing_zusatz_1'];
		$billing_zusatz_2=$orders['billing_zusatz_2'];
		// Shopware additional_address_lines als Zusatzinformationen
	} else {
		$bestellnummer=$orders_id;
		$delivery_zusatz_1="";
		$delivery_zusatz_2="";
		$billing_zusatz_1="";
		$billing_zusatz_2="";
	}
	
	// Bei woocommerce multistore mit unterschiedlichen DB Prefixes koennten die orderids gleich sein, daher Unterscheidung notwendig
	 if ($durchlauf>1 && SHOPSYSTEM == 'woocommerce') {
		$nummernkreis=1000000*$durchlauf;
		$bestellnummer = $nummernkreis+$bestellnummer;
		$orders_no = $nummernkreis+$orders_no;
	 }
	 
	$schema .= 	'<ORDER  version="1.0" type="standard">'."\n".
			 	'<ORDER_HEADER>' . "\n".
				'<CONTROL_INFO>' . "\n".					  
				'<GENERATOR_INFO>' . "dmconnector".'</GENERATOR_INFO>' . "\n".
				'<GENERATOR_VERSION>' . "dmc-".$version_datum.'</GENERATOR_VERSION>' . "\n".
				// todo 	aktuelles datum
				'<GENERATION_DATE>' . $order_infos['created_at'] .'</GENERATION_DATE>' . "\n".	
				'<ORDER_NUMBERS>' . (($rcm*10)+($i+1)) .' of '.$BestellAnzahl .'</ORDER_NUMBERS>' . "\n".							
				'</CONTROL_INFO>' . "\n".
				'<ORDER_INFO>' . "\n".	  
				'<ORDER_ID>' . $bestellnummer.'</ORDER_ID>' . "\n".
				'<ORDER_CID>' .$orders_no.'</ORDER_CID>' . "\n".
				'<ORDER_IP>' . $orders_ip.'</ORDER_IP>' . "\n".
				'<ORDER_DATE_KW>' .  date("W",time()) . '</ORDER_DATE_KW>' . "\n" .
				'<ORDER_DATE>' . $orders_date .'</ORDER_DATE>' . "\n".
				'<ORDER_STATUS>' . $orders_status .'</ORDER_STATUS>' . "\n".
				'<ORDER_IS_PAID>' .'</ORDER_IS_PAID>' . "\n".
				'<ORDER_DATA>' .umlaute_order_export($transaktionsnummern).'</ORDER_DATA>' . "\n".
				'<ORDER_STORE_ID>' .$customer_shop_ID.'</ORDER_STORE_ID>' . "\n". 
				'<ORDER_STORE_NAME>'  .'</ORDER_STORE_NAME>' . "\n".
				'<ORDER_SURCHARGE_TITLE>'.$mindermengenzuschlagstitel .'</ORDER_SURCHARGE_TITLE>' . "\n".
				'<ORDER_SURCHARGE>'.$rcm_mindermengenzuschlag .'</ORDER_SURCHARGE>' . "\n".
				'<ORDER_SURCHARGE_NET>'.$rcm_mindermengenzuschlag_net .'</ORDER_SURCHARGE_NET>' . "\n".
				'<ORDER_SURCHARGE_TAX_RATE>'. $rcm_mindermengenzuschlag_tax_rate.'</ORDER_SURCHARGE_TAX_RATE>' . "\n".
				'<ORDER_SURCHARGE_TAX_ID>'.$rcm_mindermengenzuschlag_tax_id.'</ORDER_SURCHARGE_TAX_ID>' . "\n".
				'<CUSTOMER_FOREIGN>' . $ausland . '</CUSTOMER_FOREIGN>' . "\n" .
				'<CUSTOMER_EU>' . $eg_ausland . '</CUSTOMER_EU>' . "\n" .
				'<CUSTOMER_NET>' . $nettokunde . '</CUSTOMER_NET>' . "\n" .
				'<CUSTOMER_ID>'.$customers_id.'</CUSTOMER_ID>'."\n".
				// SONDERFUNKTION wooCOmmerce bei Bedarf - usermeta Felder SOL_ADRESSNUMMER und SOL_DEBITORENNUMMER
				'<SOL_ADRESSNUMMER>'.$SOL_ADRESSNUMMER.'</SOL_ADRESSNUMMER>'."\n".
				'<SOL_DEBITORENNUMMER>'.$SOL_DEBITORENNUMMER.'</SOL_DEBITORENNUMMER>'."\n".
				
				'<CUSTOMER_DISCOUNT_PERCENT>'.$customers_status_discount.'</CUSTOMER_DISCOUNT_PERCENT>'."\n".
				'<CUSTOMER_DOB>'.$customers_dob.'</CUSTOMER_DOB>'."\n".
				'<ORDER_DISCOUNT_AMOUNT>'.$order_discount_amount.'</ORDER_DISCOUNT_AMOUNT>'."\n".
				'<INVOICE_ID>' .$invoice_id.'</INVOICE_ID>' . "\n".
				'<INVOICE_CID>' .$invoice_no.'</INVOICE_CID>' . "\n".
				'<INVOICE_DATE>' . $invoice_date.'</INVOICE_DATE>' . "\n".
				'<CUSTOMER_ADDRESS_CID>' .$customers_cid.'</CUSTOMER_ADDRESS_CID>' . "\n";	
				
				// woocommerce Sonderprogrammierung
				/*
	$schema .= 	'<DONATION_AMOUNT>' . $donation_amount.'</DONATION_AMOUNT>' . "\n".
				'<DONATION_TAX_STATUS>' . $donation_tax_status.'</DONATION_TAX_STATUS>' . "\n".		// donation_tax_status
				'<COMPLETED_DATE>' . $completed_date.'</COMPLETED_DATE>' . "\n".
				'<PAID_DATE>' . $paid_date.'</PAID_DATE>' . "\n".
				'<ZAHLUNGSDATUM>' . $zahlungsdatum.'</ZAHLUNGSDATUM>' . "\n".
				'<ZAHLUNGSSUMME>' . (round($zahlungssumme*100)*0.01) .'</ZAHLUNGSSUMME>' . "\n".
				'<SPENDER_SPONSOR_NUMMER>' . $donation_beneficiary.'</SPENDER_SPONSOR_NUMMER>' . "\n";*/
	
				// 23.10.2017 PayPal Informationen von Gambio
	$schema .=	'<ORDER_PAYPAL_FACTORING_INFO>' .umlaute_order_export($order_paypal_factoring_info).'</ORDER_PAYPAL_FACTORING_INFO>' . "\n".
				'<ORDER_PAYPAL_FINANZIERUNGS_INFO>' .umlaute_order_export($order_paypal_finanzierungs_info).'</ORDER_PAYPAL_FINANZIERUNGS_INFO>' . "\n";
			
	if ($customers_cid=='') $customers_cid=$customers_id;
	$schema .= '<ORDER_PARTIES>' . "\n".
					'<BUYER_PARTY>' . "\n".
						'<PARTY>' . "\n".
						'<PARTY_ID type="buyer_specific">'.$customers_cid.'</PARTY_ID>'."\n".
						'<ADDRESS>' . "\n".
							'<ADDRESS_TYPE>'.$Address_type.'</ADDRESS_TYPE>'. "\n".
							'<ADDRESS_ID>' .  $customers_address_id .'</ADDRESS_ID>' . "\n".
							'<PREFIX>' . umlaute_order_export($order_infos['shipping_address']['prefix']).'</PREFIX>' . "\n".
							'<TITLE>' . umlaute_order_export($customers_title).'</TITLE>' . "\n".
							'<GENDER>' . umlaute_order_export($customers_title).'</GENDER>' . "\n".
							'<NAME>' . (umlaute_order_export($customers_firstname)).'</NAME>' . "\n".
							'<NAME2>' .  (umlaute_order_export($customers_lastname)).'</NAME2>' . "\n".
							'<NAME3>' . umlaute_order_export($customers_company2).'</NAME3>' . "\n".
							'<COMPANY>' . umlaute_order_export($customers_company).'</COMPANY>' . "\n".
							//'<COMPANY2>' . umlaute_order_export($customers_company2).'</COMPANY2>' . "\n".
							'<STREET>' . umlaute_order_export($customers_address1).'</STREET>' . "\n".
							'<STREET2>' . umlaute_order_export($customers_address2).'</STREET2>' . "\n".
							'<ZUSATZ></ZUSATZ>' . "\n".
							'<ZUSATZ2></ZUSATZ2>' . "\n". 
							'<ZIP>' . umlaute_order_export($customers_zip).'</ZIP>' . "\n".
							'<CITY>' . umlaute_order_export($customers_city).'</CITY>' . "\n".
							'<COUNTRY>' . umlaute_order_export($customers_country).'</COUNTRY>' . "\n".
							'<COUNTRY_ISO_CODE>' . umlaute_order_export($customers_country_iso_code).'</COUNTRY_ISO_CODE>' . "\n".
							'<VAT_ID>' . '' .'</VAT_ID>' . "\n".
							'<PHONE>' .umlaute_order_export($customers_phone).'</PHONE>' . "\n".
							'<PHONE2>'.'</PHONE2>' . "\n".
							'<FAX>' . $order_infos['shipping_address']['fax'].'</FAX>' . "\n".
							'<EMAIL>' . umlaute_order_export($customers_email_address).'</EMAIL>' . "\n".
						'</ADDRESS>' . "\n".
						'</PARTY>' . "\n".
					'</BUYER_PARTY>' . "\n".
					'<INVOICE_PARTY>' . "\n".
						'<PARTY>' . "\n".
							'<ADDRESS>' . "\n".
								'<ADDRESS_ID>' . $billing_address_id .'</ADDRESS_ID>' . "\n".
								'<PREFIX></PREFIX>' . "\n".
								'<TITLE>' . umlaute_order_export($billing_title).'</TITLE>' . "\n".
								'<GENDER>' . umlaute_order_export($billing_gender).'</GENDER>' . "\n".
								'<NAME>' . umlaute_order_export($billing_firstname).'</NAME>' . "\n".
								'<NAME2>' . (umlaute_order_export($billing_lastname)).'</NAME2>' . "\n".
								'<NAME3>' . umlaute_order_export($billing_company2).'</NAME3>' . "\n".
								'<COMPANY>' . umlaute_order_export($billing_company).'</COMPANY>' . "\n".
								//'<COMPANY2>' . umlaute_order_export($customers_company2).'</COMPANY2>' . "\n".
								'<STREET>' . umlaute_order_export($billing_address1).'</STREET>' . "\n".
								'<ZUSATZ>' . umlaute_order_export($billing_zusatz_1).'</ZUSATZ>' . "\n".
								'<ZUSATZ2>' . umlaute_order_export($billing_zusatz_2).'</ZUSATZ2>' . "\n".
								'<ZIP>' . umlaute_order_export($billing_zip).'</ZIP>' . "\n".
								'<CITY>' . umlaute_order_export($billing_city).'</CITY>' . "\n".
								'<COUNTRY>' . umlaute_order_export($billing_country).'</COUNTRY>' . "\n".
								'<COUNTRY_ISO_CODE>' . umlaute_order_export($billing_country_iso_code).'</COUNTRY_ISO_CODE>' . "\n".
								'<VAT_ID>' . $customers_ustid .'</VAT_ID>' . "\n".	
								'<PHONE>' .umlaute_order_export($billing_phone).'</PHONE>' . "\n".
								'<PHONE2>'.$billing_phone_2.'</PHONE2>' . "\n".
								'<FAX>' . $billing_fax.'</FAX>' . "\n".
								'<EMAIL>' . umlaute_order_export($billing_email_address).'</EMAIL>' . "\n".
							'</ADDRESS>' . "\n". 
						'</PARTY>' . "\n".
					'</INVOICE_PARTY>' . "\n";
					// Lieferanschrift Werte nur angeben, wenn gefüllt, sonnst komplett leer
					if ($delivery_address1!="") {
					$schema .=  '<DELIVERY_PARTY>' . "\n".
						'<PARTY>' . "\n".
							'<ADDRESS>' . "\n".
							'<ADDRESS_ID>' . $delivery_address_id .'</ADDRESS_ID>' . "\n".
							'<PREFIX></PREFIX>' . "\n".
							'<TITLE>' . umlaute_order_export($delivery_title).'</TITLE>' . "\n".
							'<GENDER>' . umlaute_order_export($delivery_gender).'</GENDER>' . "\n".
							'<NAME>' . umlaute_order_export($delivery_firstname).'</NAME>' . "\n".
							'<NAME2>' . (umlaute_order_export($delivery_lastname)).'</NAME2>' . "\n".
							'<NAME3>' . umlaute_order_export($delivery_company2).'</NAME3>' . "\n".
							'<COMPANY>' . umlaute_order_export($delivery_company).'</COMPANY>' . "\n".
							//'<COMPANY2>' . umlaute_order_export($delivery_company2).'</COMPANY2>' . "\n".
							'<STREET>' . umlaute_order_export($delivery_address1).'</STREET>' . "\n".
							'<STREET2>' . umlaute_order_export($delivery_address2).'</STREET2>' . "\n".
							'<ZUSATZ>' . umlaute_order_export($delivery_zusatz_1).'</ZUSATZ>' . "\n".
							'<ZUSATZ2>' . umlaute_order_export($delivery_zusatz_2).'</ZUSATZ2>' . "\n".
							'<ZIP>' . umlaute_order_export($delivery_zip).'</ZIP>' . "\n".
							'<CITY>' . umlaute_order_export($delivery_city).'</CITY>' . "\n".
							'<COUNTRY>' . umlaute_order_export($delivery_country).'</COUNTRY>' . "\n".
							'<COUNTRY_ISO_CODE>' . umlaute_order_export($delivery_country_iso_code).'</COUNTRY_ISO_CODE>' . "\n".
							'<VAT_ID>' . $customers_ustid .'</VAT_ID>' . "\n".
							'<PHONE>' .'</PHONE>' . "\n".
							'<PHONE2>'.'</PHONE2>' . "\n".
							'<FAX>' . $delivery_fax.'</FAX>' . "\n".
							'<EMAIL>' . umlaute_order_export($delivery_email_address).'</EMAIL>' . "\n".
						'</ADDRESS>' . "\n".
						'</PARTY>' . "\n".
					'</DELIVERY_PARTY>' . "\n";
					} else {
					$schema .=  '<DELIVERY_PARTY>' . "\n".
						'<PARTY>' . "\n".
							'<ADDRESS>' . "\n".
							'<ADDRESS_ID></ADDRESS_ID>' . "\n".
							'<ADDRESS_CID></ADDRESS_CID>' . "\n".
							'<PREFIX></PREFIX>' . "\n".
							'<TITLE></TITLE>' . "\n".
							'<GENDER></GENDER>' . "\n".
							'<NAME></NAME>' . "\n".
							'<NAME2></NAME2>' . "\n".
							'<NAME3></NAME3>' . "\n".
							'<COMPANY></COMPANY>' . "\n".
							//'<COMPANY2>' . umlaute_order_export($delivery_company2).'</COMPANY2>' . "\n".
							'<STREET></STREET>' . "\n".
							'<STREET2></STREET2>' . "\n".
							'<ZIP></ZIP>' . "\n".
							'<CITY></CITY>' . "\n".
							'<COUNTRY></COUNTRY>' . "\n".
							'<COUNTRY_ISO_CODE></COUNTRY_ISO_CODE>' . "\n".
							'<VAT_ID>' . '' .'</VAT_ID>' . "\n".
							'<PHONE></PHONE>' . "\n".
							'<PHONE2></PHONE2>' . "\n".
							'<FAX></FAX>' . "\n".
							'<EMAIL></EMAIL>' . "\n".
						'</ADDRESS>' . "\n".
						'</PARTY>' . "\n".
					'</DELIVERY_PARTY>' . "\n";
					}
					// Shop Adresse
				$schema .= 	'<SUPPLIER_PARTY>' . "\n".
						'<PARTY>' . "\n".
						'<ADDRESS>' . "\n".
							'<NAME>' . "DoubleM Neue Medien GmbH".'</NAME>' . "\n".
							'<NAME2>' . ''.'</NAME2>' . "\n".
							'<NAME3>' . ''.'</NAME3>' . "\n".
							'<STREET>' . ''.'</STREET>' . "\n".
							'<ZIP>' . ''.'</ZIP>' . "\n".
							'<CITY>' . ''.'</CITY>' . "\n".
							'<COUNTRY>' . ''.'</COUNTRY>' . "\n".
							'<VAT_ID>' . ''.'</VAT_ID>' . "\n".
							'<PHONE type="other">' . ''.'</PHONE>' . "\n".
							'<PHONE></PHONE>' . "\n".
							'<PHONE2></PHONE2>' . "\n".
							'<FAX>' . ''.'</FAX>' . "\n".
							'<EMAIL>' . ''.'</EMAIL>' . "\n".
						'</ADDRESS>' . "\n".
						'</PARTY>' . "\n".
					'</SUPPLIER_PARTY>' . "\n".
				'</ORDER_PARTIES>' . "\n".
				'<PAYMENT>' . "\n";
					if (strpos(strtolower($payment_method), 'sofort') !== false) {
						$payment_method = "sofortueberweisung";
					}
	
					if ($payment_method == "todo") {
						// DEBIT
						$schema .= '	<PAYMENT_TERM>Debit</PAYMENT_TERM>' . "\n";
					} else if ($payment_method == "bankpayment") {
						// CHECK
						$schema .= '	<PAYMENT_TERM>Vorkasse</PAYMENT_TERM>' . "\n";
					} else {
						// CASH
						$schema .= '	<PAYMENT_TERM>' . umlaute_order_export($payment_method) .'</PAYMENT_TERM>' . "\n";
					} // endif payment
					
					$schema .= '	<PAYMENT_CLASS>' . $orders['payment_class'] .'</PAYMENT_CLASS>' . "\n";
					$schema .= '	<PAYMENT_TRANSACTION_ID>' . $payment_transactionID .'</PAYMENT_TRANSACTION_ID>' . "\n";
							
				$schema .=
						'	<CARD_NUM>' . $order_infos['payment']['po_number'].'</CARD_NUM>' . "\n".
						/*	'<CARD_AUTH_CODE>' . $order_infos[payment][cc_number_enc].'</CARD_AUTH_CODE>' . "\n".
						'<CARD_EXPIRATION_DATE>' . $order_infos[payment][cc_exp_month].'/'.$order_infos[payment][cc_exp_year].'</CARD_EXPIRATION_DATE>' . "\n".
						// Typs: AMEX, Visa, MC (Master Card), JCB, Diners (and others (Maestro?))
						'<CARD_TYPE>' . $order_infos[payment][cc_type].'</CARD_TYPE>' . "\n".
						'<CARD_HOLDER_NAME>' . umlaute_order_export2($order_infos[payment][cc_owner]).'</CARD_HOLDER_NAME>' . "\n".
						*/
						'<CARD_AUTH_CODE></CARD_AUTH_CODE>' . "\n".
						'<CARD_EXPIRATION_DATE></CARD_EXPIRATION_DATE>' . "\n".
						// Typs: AMEX, Visa, MC (Master Card), JCB, Diners (and others (Maestro?))
						'<CARD_TYPE></CARD_TYPE>' . "\n".
						'<CARD_HOLDER_NAME></CARD_HOLDER_NAME>' . "\n".
						'	<ACCOUNT_HOLDER>' . umlaute_order_export($bank_inh).'</ACCOUNT_HOLDER>' . "\n".
						'	<ACCOUNT_BANK_NAME>' . umlaute_order_export($bank_name).'</ACCOUNT_BANK_NAME>' . "\n".
						'	<ACCOUNT_BANK_COUNTRY>' . umlaute_order_export($bank_stat).'</ACCOUNT_BANK_COUNTRY>' . "\n".							
						'	<ACCOUNT_BANK_CODE>' . umlaute_order_export($bank_blz).'</ACCOUNT_BANK_CODE>' . "\n".
						'	<ACCOUNT_BANK_ACCOUNT>' .umlaute_order_export( $bank_kto).'</ACCOUNT_BANK_ACCOUNT>' . "\n".
						'	<ACCOUNT_BANK_BIC>' . umlaute_order_export($bank_bic).'</ACCOUNT_BANK_BIC>' . "\n".
						'	<ACCOUNT_BANK_IBAN>' .umlaute_order_export( $bank_iban).'</ACCOUNT_BANK_IBAN>' . "\n".
						'	<PAYMENT_FEE>' . $payment_amount.'</PAYMENT_FEE>' . "\n".	
						'	<PAYMENT_FEE_TAX>' . $payment_tax_amount.'</PAYMENT_FEE_TAX>' . "\n".
						'	<PAYMENT_FEE_GROS>' . ($payment_amount+$payment_tax_amount).'</PAYMENT_FEE_GROS>' . "\n";
					
				$schema .='</PAYMENT>' . "\n".
					'<DELIVERY_METHOD>' .($shipping_method). '</DELIVERY_METHOD>' . "\n".
					'<DELIVERY_CLASS>' .umlaute_order_export($shipping_class). '</DELIVERY_CLASS>' . "\n".
					'<DELIVERY_FEE>' . number_format($shipping_amount, 4, '.', '').'</DELIVERY_FEE>' . "\n".	
					'<DELIVERY_FEE_TAX>' . number_format($shipping_tax_amount, 4, '.', '') .'</DELIVERY_FEE_TAX>' . "\n".
					'<DELIVERY_FEE_TAX_RATE>' . number_format($shipping_tax_rate, 4, '.', '') .'</DELIVERY_FEE_TAX_RATE>' . "\n".
					'<DELIVERY_FEE_GROS>' . number_format(($shipping_amount+$shipping_tax_amount), 4, '.', '').'</DELIVERY_FEE_GROS>' . "\n".
					'<DELIVERY_WEIGHT>' . $shipping_weight .'</DELIVERY_WEIGHT>' . "\n".
					'<DISCOUNT_AMOUNT>' . number_format($discount_amount, 4, '.', '').'</DISCOUNT_AMOUNT>' . "\n".	
					'<SHOP_ID>' . $shop_id.'</SHOP_ID>' . "\n".	
					'<LANGUAGE_CODE>' . $language_code.'</LANGUAGE_CODE>' . "\n";	
					
				// Shopspezifische Zusatzinformationen
				$schema .=
					'<DELIVERY_DATE>' .umlaute_order_export($orders_delivery_date).'</DELIVERY_DATE>' . "\n".
					'<DELIVERY_ANONYM>' .umlaute_order_export($orders_delivery_anonym).'</DELIVERY_ANONYM>' . "\n".
					'<DELIVERY_GREETINGS_TEXT>' .umlaute_order_export($orders_greetings_text).'</DELIVERY_GREETINGS_TEXT>' . "\n";
				
				// 23.10.2017 PayPal Informationen von Gambio
				$schema .=
					'<ORDER_PAYPAL_FACTORING_INFO>' . "\n".
						'<OPFI_ALL>'.$order_paypal_factoring_info.'</OPFI_ALL>'. "\n".
						'<OPFI_BETRAG>'.$order_paypal_factoring_info_betrag.'</OPFI_BETRAG>'. "\n".
						'<OPFI_IBAN>'.$order_paypal_factoring_info_iban.'</OPFI_IBAN>'. "\n".
						'<OPFI_BIC>'.$order_paypal_factoring_info_bic.'</OPFI_BIC>'. "\n".
						'<OPFI_KREDITINSTITUT>'.$order_paypal_factoring_info_kreditinstitut.'</OPFI_KREDITINSTITUT>'. "\n".
						'<OPFI_ZWECK>'.$order_paypal_factoring_info_verwendungszweck.'</OPFI_ZWECK>'. "\n".
						'<OPFI_ZAHLBAR>'.$order_paypal_factoring_info_zahlbar.'</OPFI_ZAHLBAR>'. "\n".
					'</ORDER_PAYPAL_FACTORING_INFO>' . "\n".
					'<ORDER_PAYPAL_FINANZIERUNGS_INFO>' .umlaute_order_export($order_paypal_finanzierungs_info).'</ORDER_PAYPAL_FINANZIERUNGS_INFO>' . "\n";
				
				// etc
			$schema .='</ORDER_INFO>' . "\n".
			'</ORDER_HEADER>' . "\n".
			'<ORDER_ITEM_LIST>' . "\n";
	
?>