<?php
/************************************************
*                                            	*
*  dmConnector Export							*
*  dmc_xml_order_footer.inc.php					*
*  XML Ausgabe des Endes der XML Datei			*
*  Einbinden in dmconnector_export.php			*
*  Copyright (C) 2011 DoubleM-GmbH.de			*
*                                               *
*************************************************/

	$schema .= '<ORDER_TOTAL>' . "\n";
	
		if (SHOPSYSTEM != 'presta') {
			// list totals
			if (SHOPSYSTEM == 'veyton') {
					$totals_query = dmc_db_query("SELECT orders_total_name AS title, ".
									"(orders_total_price * 1.19) AS value, orders_total_key AS class, ".
									"orders_total_id AS sort_order from " . TABLE_ORDERS_TOTAL . 
									" WHERE orders_id = '" . $orders['orders_id'] . "' ORDER BY sort_order");
			} else {
					$totals_query = dmc_db_query("SELECT title, value, class, sort_order ".
									"FROM " . TABLE_ORDERS_TOTAL . 
									" WHERE orders_id = '" . $orders['orders_id'] . "' ORDER BY sort_order");
			} // end if
			
						while ($totals = dmc_db_fetch_array($totals_query))
			{
				$total_prefix = "";
			    $total_prefix = $order_total_class[$totals['class']]['prefix'];
				
				if ($totals['class']=="ot_tax")
					$total_tax = $totals['value'];
				if ($totals['class']=="ot_total") {
					$order_total_gros=$totals['value'];
					$netto=htmlspecialchars($totals['value']-$total_tax);
					$order_total_net=$netto;
					$mwst=$total_tax;
				} else {
					$netto=htmlspecialchars($totals['value']);
					$mwst=0;
				}
				// Prozentsatz fuer ot_discount
				if ( $totals['class'] == 'ot_subtotal')
					$zwischensumme=$totals['value'];	// zwischensummemerken
				if ( $totals['class'] == 'ot_discount')
					$rabattProzent=$totals['value']/$zwischensumme*-100;	// zwischensummemerken
				else 
					$rabattProzent=0;
					
				$totals['title']= str_replace("&uuml;","ü",$totals['title']);
				$schema .= 	'<TOTAL>' . "\n" .
								'<TOTAL_TITLE>' . umlaute_order_export(html2ascii(($totals['title']))) . '</TOTAL_TITLE>' . "\n" .
								'<TOTAL_VALUE>' . htmlspecialchars($totals['value']) . '</TOTAL_VALUE>' . "\n" .
								'<TOTAL_VALUE_NET>' . $netto . '</TOTAL_VALUE_NET>' . "\n".
								'<TOTAL_VALUE_DISCOUNT_PERCENT>' . $rabattProzent . '</TOTAL_VALUE_DISCOUNT_PERCENT>' . "\n";
								if ($rabattProzent > 0) {
									$schema .= '<DISCOUNT_TOTAL_AMOUNT_NET>' . ($netto - ($netto*$rabattProzent/100)) .'</DISCOUNT_TOTAL_AMOUNT_NET>' . "\n". 	
									'<DISCOUNT_TOTAL_AMOUNT>' . ($totals['value']-($totals['value'] - ($totals['value']*$rabattProzent/100))) .'</DISCOUNT_TOTAL_AMOUNT>' . "\n". 
									'<DISCOUNT_TOTAL_AMOUNT_TAX>' . (($totals['value']-($totals['value'] - ($totals['value']*$rabattProzent/100)))-($netto - ($netto*$rabattProzent/100))).'</DISCOUNT_TOTAL_AMOUNT_TAX>' . "\n";
								} else {
									$schema .= '<DISCOUNT_TOTAL_AMOUNT_NET>' .$netto .'</DISCOUNT_TOTAL_AMOUNT_NET>' . "\n". 	
									'<DISCOUNT_TOTAL_AMOUNT>' . $totals['value'] .'</DISCOUNT_TOTAL_AMOUNT>' . "\n". 
									'<DISCOUNT_TOTAL_AMOUNT_TAX>' . $mwst .'</DISCOUNT_TOTAL_AMOUNT_TAX>' . "\n";
								} // end if 
								$schema .= '<TOTAL_CLASS>' . htmlspecialchars($totals['class']) . '</TOTAL_CLASS>' . "\n" .
								'<TOTAL_SORT_ORDER>' . htmlspecialchars($totals['sort_order']) . '</TOTAL_SORT_ORDER>' . "\n" .
								'<TOTAL_PREFIX>' . htmlspecialchars($total_prefix) . '</TOTAL_PREFIX>' . "\n" .
								'<TOTAL_TAX>' . $mwst . '</TOTAL_TAX>' . "\n" . 
							'</TOTAL>' . "\n";
			} // end while (totals)
			
			$schema .= 	'<ORDER_TOTAL_NET>' .  $order_total_net . '</ORDER_TOTAL_NET>' . "\n".
						'<ORDER_TOTAL_GROS>'   .$order_total_gros . '</ORDER_TOTAL_GROS>' . "\n";
		} else {
			// Bei Presta werden die Werte direkt aus der Orders Tabelle verwendet.
			/*id_order 	id_carrier 	id_lang 	id_customer 	id_cart 	id_currency 	id_address_delivery 	
			id_address_invoice 	secure_key 	payment 	conversion_rate 	module 	recyclable 	gift 	
			gift_message 	shipping_number 	total_discounts 	total_paid 	total_paid_real 	
			total_products 	total_products_wt 	total_shipping 	carrier_tax_rate 	total_wrapping 	
			invoice_number 	delivery_number 	invoice_date 	delivery_date 	valid 	date_add 	date_upd */
				$mwst = 19;
				$brutto = $orders['total_products'];
				$netto = $brutto/1.19;
				$rabattProzent=0;
				$total_art="Zwischensumme";
				$total_art_id="Zwischensumme";
				$sort=10;
				
				// produktzwischensummen aus dmc_xml_order_producs.inc.php übernehmen
	
				$schema .= 	'<TOTAL>' . "\n" .
								'<TOTAL_TITLE>'.$total_art.'</TOTAL_TITLE>' . "\n" .
								'<TOTAL_VALUE>' . $products_subtotal_amount . '</TOTAL_VALUE>' . "\n" .
								'<TOTAL_VALUE_NET>' . $products_subtotal_amount_net . '</TOTAL_VALUE_NET>' . "\n".
								'<TOTAL_VALUE_DISCOUNT_PERCENT>' . $rabattProzent . '</TOTAL_VALUE_DISCOUNT_PERCENT>' . "\n";
								if ($rabattProzent > 0) {
									$schema .= '<DISCOUNT_TOTAL_AMOUNT_NET>' . ($products_subtotal_amount_net - ($products_subtotal_amount_net*$rabattProzent/100)) .'</DISCOUNT_TOTAL_AMOUNT_NET>' . "\n". 	
									'<DISCOUNT_TOTAL_AMOUNT>' . ($products_subtotal_amount-($products_subtotal_amount - ($products_subtotal_amount*$rabattProzent/100))) .'</DISCOUNT_TOTAL_AMOUNT>' . "\n". 
									'<DISCOUNT_TOTAL_AMOUNT_TAX>' . (($products_subtotal_amount-($products_subtotal_amount - ($products_subtotal_amount*$rabattProzent/100)))-($products_subtotal_amount_net - ($products_subtotal_amount_net*$rabattProzent/100))).'</DISCOUNT_TOTAL_AMOUNT_TAX>' . "\n";
								} else {
									$schema .= '<DISCOUNT_TOTAL_AMOUNT_NET>' .$products_subtotal_amount_net.'</DISCOUNT_TOTAL_AMOUNT_NET>' . "\n". 	
									'<DISCOUNT_TOTAL_AMOUNT>' . $products_subtotal_amount .'</DISCOUNT_TOTAL_AMOUNT>' . "\n". 
									'<DISCOUNT_TOTAL_AMOUNT_TAX>0</DISCOUNT_TOTAL_AMOUNT_TAX>' . "\n";
								} // end if 
								$schema .= '<TOTAL_CLASS>' .$total_art_id . '</TOTAL_CLASS>' . "\n" .
								'<TOTAL_SORT_ORDER>' . $sort . '</TOTAL_SORT_ORDER>' . "\n" .
								'<TOTAL_TAX_AMOUNT>' . $products_subtotal_tax_amount . '</TOTAL_TAX>' . "\n" . 
 								'<TOTAL_TAX>' .$products_tax_percent_1 . '</TOTAL_TAX>' . "\n" . 
								'<TOTAL_TAX2>' . $products_tax_percent_2 . '</TOTAL_TAX2>' . "\n" . 
							'</TOTAL>' . "\n";
		} // end if presta
		
		$schema .= 	'</ORDER_TOTAL>' . "\n".
					'<ORDER_COMMENTS>' . umlaute_order_export(html2ascii($comments)) . '</ORDER_COMMENTS>' . "\n";
					//'</ORDER_INFO>' . "\n\n";
	
?>