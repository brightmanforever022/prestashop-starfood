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

	$schema .= '<ORDER_SUMMARY>' . "\n";
	
			// list totals
			if (SHOPSYSTEM == 'veyton') {
						$discount_total_net=dmc_get_orders_total("discount_total",true,$orders_id);
						$discount_total_gros=dmc_get_orders_total("discount_total",false,$orders_id);
						
						$schema .=	// '<ORDER_TOTAL>' . "\n". 
								'<TOTAL_ITEM_NUM>' . $line_item_id .'</TOTAL_ITEM_NUM>' . "\n".
								//'<OD_ID>' . $orders_id .'</O_ID>' . "\n".
								'<SUBTOTAL_AMOUNT_NET>' . dmc_get_orders_total("subtotal",true,$orders_id) .'</SUBTOTAL_AMOUNT_NET>' . "\n". 	// subtotal = without shipping 
								'<TOTAL_AMOUNT_NET>' . dmc_get_orders_total("total",true,$orders_id).'</TOTAL_AMOUNT_NET>' . "\n". 	
								'<TOTAL_TAX_AMOUNT>'.(dmc_get_orders_total("total",false,$orders_id)-dmc_get_orders_total("total",false,$orders_id)).'</TOTAL_TAX_AMOUNT>' . "\n".					
								'<SUBTOTAL_AMOUNT>' .dmc_get_orders_total("subtotal",false,$orders_id) .'</SUBTOTAL_AMOUNT>' . "\n". 
								'<TOTAL_AMOUNT>' . dmc_get_orders_total("total",false,$orders_id).'</TOTAL_AMOUNT>' . "\n". 
								'<ORDER_CURRENCY_CODE>' .$currency_code.'</ORDER_CURRENCY_CODE>' . "\n".		
								'<ORDER_CURRENCY_RATE>' .$currency_value.'</ORDER_CURRENCY_RATE>' . "\n".		
								'<ORDER_CURRENCY_RATE_INVERS>' .(1/$currency_value).'</ORDER_CURRENCY_RATE_INVERS>' . "\n".		
								'<DISCOUNT_TOTAL_AMOUNT_NET>' .$discount_total_net.'</DISCOUNT_TOTAL_AMOUNT_NET>' . "\n". 	
								'<DISCOUNT_TOTAL_AMOUNT>' . $discount_total_gros.'</DISCOUNT_TOTAL_AMOUNT>' . "\n". 
								'<DISCOUNT_TOTAL_AMOUNT_TAX>' .($discount_total_gros-$discount_total_net).'</DISCOUNT_TOTAL_AMOUNT_TAX>' . "\n". 	
								//'</ORDER_TOTAL>' . 
								"\n";
			} else if (SHOPSYSTEM == 'presta') {
				
							$zwischensumme_tax_amount = $zwischensumme  - $zwischensumme_net ;
							
								$schema .=	// '<ORDER_TOTAL>' . "\n". 
									'<TOTAL_ITEM_NUM>' . $line_item_id .'</TOTAL_ITEM_NUM>' . "\n".
									'<SUBTOTAL_AMOUNT_NET>' . $zwischensumme_net.'</SUBTOTAL_AMOUNT_NET>' . "\n". 	// subtotal = without shipping 
									'<SUBTOTAL_TAX_AMOUNT>' .$zwischensumme_tax_amount.'</SUBTOTAL_TAX_AMOUNT>' . "\n".					
									'<SUBTOTAL_AMOUNT>' .($zwischensumme).'</SUBTOTAL_AMOUNT>' . "\n". 
									'<TOTAL_AMOUNT_NET>' . ($order_total_net).'</TOTAL_AMOUNT_NET>' . "\n". 	
									'<TOTAL_TAX_AMOUNT>' .$order_tax_amount.'</TOTAL_TAX_AMOUNT>' . "\n".					
									'<TOTAL_AMOUNT>' . $order_total_gros.'</TOTAL_AMOUNT>' . "\n". 
									'<ORDER_CURRENCY_CODE>' .$currency_code.'</ORDER_CURRENCY_CODE>' . "\n".		
									'<DISCOUNT_TOTAL_AMOUNT_NET>' . $order_total_net.'</DISCOUNT_TOTAL_AMOUNT_NET>' . "\n". 	
									'<DISCOUNT_TOTAL_AMOUNT>' . $order_total_gros.'</DISCOUNT_TOTAL_AMOUNT>' . "\n". 
									'<DISCOUNT_AMOUNT_TOTAL>' . $order_total_discount_amount.'</DISCOUNT_AMOUNT_TOTAL>' . "\n".
									'<DISCOUNT_TOTAL_AMOUNT_TAX>' . ($order_tax_amount).'</DISCOUNT_TOTAL_AMOUNT_TAX>' . "\n";
								//	'</ORDER_TOTAL>' . "\n";
							
			} else if (SHOPSYSTEM == 'virtuemart' || SHOPSYSTEM == 'shopware' || SHOPSYSTEM == 'joomshopping' || SHOPSYSTEM == 'woocommerce') {
				 
				$schema .=	// '<ORDER_TOTAL>' . "\n". 
									'<TOTAL_ITEM_NUM>' . $line_item_id .'</TOTAL_ITEM_NUM>' . "\n".
									'<SUBTOTAL_AMOUNT_NET>' . $zwischensumme.'</SUBTOTAL_AMOUNT_NET>' . "\n". 	// subtotal = without shipping 
									'<TOTAL_AMOUNT_NET>' . ($order_total_net).'</TOTAL_AMOUNT_NET>' . "\n". 	
									'<TOTAL_TAX_AMOUNT>' .$order_tax_amount.'</TOTAL_TAX_AMOUNT>' . "\n".					
									'<SUBTOTAL_AMOUNT>' .($zwischensumme+$order_tax_amount).'</SUBTOTAL_AMOUNT>' . "\n". 
									'<TOTAL_AMOUNT>' . $order_total_gros.'</TOTAL_AMOUNT>' . "\n". 
									'<ORDER_CURRENCY_CODE>' .$currency_code.'</ORDER_CURRENCY_CODE>' . "\n".		
									'<DISCOUNT_TOTAL_AMOUNT_NET>' . $order_total_net.'</DISCOUNT_TOTAL_AMOUNT_NET>' . "\n". 	
									'<DISCOUNT_TOTAL_AMOUNT>' . $order_total_gros.'</DISCOUNT_TOTAL_AMOUNT>' . "\n". 
									'<DISCOUNT_AMOUNT_TOTAL>' . $order_total_discount_amount.'</DISCOUNT_AMOUNT_TOTAL>' . "\n".
									// Wibben
									'<SHIPPING_AMOUNT_7_GROS>' . ($shipping_amount_7+$shipping_tax_amount_7) .'</SHIPPING_AMOUNT_7_GROS>' . "\n".
									'<SHIPPING_TAX_AMOUNT_7>' . $shipping_tax_amount_7 .'</SHIPPING_TAX_AMOUNT_7>' . "\n".
									'<SHIPPING_AMOUNT_7_NET>' . ($shipping_amount_7) .'</SHIPPING_AMOUNT_7_NET>' . "\n".
									'<SHIPPING_AMOUNT_19_GROS>' . ($shipping_amount_19+$shipping_tax_amount_19) .'</SHIPPING_AMOUNT_19_GROS>' . "\n".
									'<SHIPPING_TAX_AMOUNT_19>' . $shipping_tax_amount_19 .'</SHIPPING_TAX_AMOUNT_19>' . "\n".
									'<SHIPPING_AMOUNT_19_NET>' . ($shipping_amount_19) .'</SHIPPING_AMOUNT_19_NET>' . "\n".
									// WIbben ende
									'<DISCOUNT_TOTAL_AMOUNT_TAX>' . ($order_tax_amount).'</DISCOUNT_TOTAL_AMOUNT_TAX>' . "\n";
								//	'</ORDER_TOTAL>' . "\n";
			} else {
					$totals_query = dmc_db_query("SELECT title, value, class, sort_order ".
									"FROM " . TABLE_ORDERS_TOTAL . 
									" WHERE orders_id = '" . $orders_id . "' ORDER BY sort_order");
					$total_tax =0;
					$order_total_gros=0;
					$order_total_net=0;
					$total_tax = 0;
					$total_tax_7 = 0;
					$total_tax_19 = 0;
					while ($totals = dmc_db_fetch_array($totals_query))
					{
						$total_prefix = "";
						$total_prefix = $order_total_class[$totals['class']]['prefix'];
						// Unterstuetzung unterschiedlicher MwSt Saetze in einer Bestellung
						if ($totals['class']=="ot_tax") {	
							if (strpos ( $totals['title'], "19" ) !== false) {
								// Tax mit 19% MwSt
								$total_tax_19 = $totals['value'];
							} else if (strpos ( $totals['title'], "7" ) !== false) {
								// Tax mit 7% MwSt
								$total_tax_7 = $totals['value'];
							} else {
								// Tax mit ? MwSt
								$total_tax = $totals['value'];
							}
						} 
						// Summe inkl Versand
						if ($totals['class']=="ot_total") {
							// Wenn nicht steuerfrei
							if ($total_tax == 0) {
								$total_tax = $total_tax_19+$total_tax_7;
							}
							$order_total_gros=$totals['value'];
							$netto=htmlspecialchars($totals['value']-$total_tax);
							$order_total_net=$netto;
							$order_tax_amount=$total_tax;
						} else {
							$netto=htmlspecialchars($totals['value']);
							$order_tax_amount=0;
						}
						// Prozentsatz fuer ot_discount
						// Zwischensumme exkl Versand
						if ( $totals['class'] == 'ot_subtotal')
							$zwischensumme=$totals['value'];	// zwischensummemerken
						if ( $totals['class'] == 'ot_discount')
							$rabattProzent=$totals['value']/$zwischensumme*-100;	// zwischensummemerken
						else 
							$rabattProzent=0;
							 
						$totals['title']= str_replace("&uuml;","ü",$totals['title']);
						
					} // end while (totals)
						
					
					// Berechnung der Bestellsummen basierend auf 7% und 19% Steueranteilen (fuer Splitbuchungen)
					/* ALT, DA RUNDUNGSFHELER
					if ($total_tax_7 > 0)  { 									// Bsp 0,85
						$order_total_gros_7=$total_tax_7*100/7;					// Bsp 12,14
						$order_total_net_7=$order_total_gros_7-$total_tax_7;	// Bsp 11,29
					} else {
						$order_total_gros_7=0;
						$order_total_net_7=0;
					}
					
					if ($total_tax_19 > 0)  { 									// Bsp 1,27
						$order_total_gros_19=$total_tax_19*100/19;				// Bsp 6,68
						$order_total_net_19=$order_total_gros_19-$total_tax_19;	// Bsp 5,49
					} else {
						$order_total_gros_19=0;
						$order_total_net_19=0;
					}
																				// Bsp Gesamt 18,82
					*/
					$order_total_gros_0=0;
					$order_total_net_0=0;
					$order_total_gros_7=0;
					$order_total_net_7=0;
					$order_total_gros_19=0;
					$order_total_net_19=0;
					//$shipping_tax_amount=
					// NEU: Berechnung der Bestellsummen basierend auf 7% und 19% Steueranteilen (fuer Splitbuchungen)
					// 0% Steuerfrei 
					$summen_query = dmc_db_query("SELECT IFNULL(SUM(final_price),0) AS gesamtpreis ".
									"FROM " . TABLE_ORDERS_PRODUCTS . 
									" WHERE orders_id = '" . $orders_id . "' AND products_tax = 0 ");
					$result = dmc_db_fetch_array($summen_query);
					$order_total_gros_0 = $result['gesamtpreis'];
					$order_total_net_0 = $order_total_gros_0;
					$total_tax_0 = 0;
					// 7%
					$summen_query = dmc_db_query("SELECT IFNULL(SUM(final_price),0) AS gesamtpreis ".
									"FROM " . TABLE_ORDERS_PRODUCTS . 
									" WHERE orders_id = '" . $orders_id . "' AND products_tax = 7 ");
					$result = dmc_db_fetch_array($summen_query);
					$order_total_gros_7 = $result['gesamtpreis'];
					$order_total_net_7 = $order_total_gros_7/1.07;
					$total_tax_7 = $order_total_gros_7 - $order_total_net_7;
					// 19%
					$summen_query = dmc_db_query("SELECT IFNULL(SUM(final_price),0)  AS gesamtpreis ".
									"FROM " . TABLE_ORDERS_PRODUCTS . 
									" WHERE orders_id = '" . $orders_id . "' AND products_tax = 19 ");
					$result = dmc_db_fetch_array($summen_query);
					$summe19 = $result['gesamtpreis'];
					// Rabatt ermitteln
					$totals_query = dmc_db_query("SELECT title, value, class, sort_order ".
									"FROM " . TABLE_ORDERS_TOTAL . 
									" WHERE orders_id = '" . $orders_id . "' AND class='ot_coupon' ORDER BY sort_order");
					$result = dmc_db_fetch_array($totals_query);
					$discout = $result['value'];
					if ($discout=='' || $discout=='null')
						$discout="0.0000";
					else
						$discout = $discout*-1;
					
					// $order_total_gros=$order_total_gros+$discout;
					
					// Versandkosten sind 19%, wenn NICHT Auslandssendung daher:
					if ( $order_total_gros_0 > 0) {
						// Auslandsverand auch mit 0%
						$order_total_gros_19 = $summe19;// +$shipping_tax_amount;
						$order_total_net_19 = $order_total_gros_19/1.19;
						$total_tax_19 = $order_total_gros_19 - $order_total_net_19;	
						$order_total_gros_0 = $order_total_gros_0+$shipping_amount;
						$order_total_net_0 = $order_total_gros_0;											
					} else {
						// Inlandsverand mit 19%
						$order_total_gros_19 = $summe19+$shipping_amount;// +$shipping_tax_amount;
						$order_total_net_19 = $order_total_gros_19/1.19;
						$total_tax_19 = $order_total_gros_19 - $order_total_net_19;										
					}
					$order_total_gros=round($order_total_gros,2);
					$order_total_net=round($order_total_net,2);
					$order_total_gros_0=round($order_total_gros_0,2);
					$order_total_gros_7=round($order_total_gros_7,2);
					$order_total_net_7=round($order_total_net_7,2);
					$order_total_gros_19=round($order_total_gros_19,2);
					$order_total_net_19=round($order_total_net_19,2);
					$discout=round($discout,2)*-1;
					// Splitbuchung ???
					if ($order_total_net_0!=0)
						$gegenkonto=8200;	// Keine Splitbuchung, Buchung gegen Auslandskonto
					else if ($order_total_net_7 != 0 && $order_total_net_19 == 0)
						$gegenkonto=8301; // Keine Splitbuchung, Buchung gegen 7% Konto
					else if ($order_total_net_7 == 0 && $order_total_net_19 != 0)
						$gegenkonto=8401; // Keine Splitbuchung, Buchung gegen 7% Konto
					else // if ($order_total_net_7 != 0 && $order_total_net_19 != 0)
						$gegenkonto=0; // Splitbuchung, Buchung gegen 0 
				
					// Es sei denn Rabatt, dann doch Splitbuchung
					if ($discout!="0.0000")
						$gegenkonto=0;
				
					$schema .=	// '<ORDER_TOTAL>' . "\n". 
									'<TOTAL_ITEM_NUM>' . $line_item_id .'</TOTAL_ITEM_NUM>' . "\n".
									'<SUBTOTAL_AMOUNT_NET>' . number_format($zwischensumme, 4, '.', '').'</SUBTOTAL_AMOUNT_NET>' . "\n". 	// subtotal = without shipping 
									'<TOTAL_AMOUNT_NET>' . number_format($order_total_net, 4, '.', '').'</TOTAL_AMOUNT_NET>' . "\n". 	
									'<TOTAL_TAX_AMOUNT>' .number_format($order_tax_amount, 4, '.', '').'</TOTAL_TAX_AMOUNT>' . "\n".					
									'<SUBTOTAL_AMOUNT>' .number_format(round(($zwischensumme+$order_tax_amount),2), 4, '.', '').'</SUBTOTAL_AMOUNT>' . "\n". 
									'<TOTAL_AMOUNT>' . number_format($order_total_gros, 4, '.', '').'</TOTAL_AMOUNT>' . "\n". 
									'<TOTAL_AMOUNT_NET_0>' . number_format($order_total_net_0, 4, '.', '').'</TOTAL_AMOUNT_NET_0>' . "\n". 	
									'<TOTAL_AMOUNT_0>' . number_format($order_total_gros_0, 4, '.', '') .'</TOTAL_AMOUNT_0>' . "\n". 
									'<TOTAL_TAX_AMOUNT_0>0</TOTAL_TAX_AMOUNT_0>' . "\n".					
									'<TOTAL_AMOUNT_NET_7>' . number_format($order_total_net_7, 4, '.', '').'</TOTAL_AMOUNT_NET_7>' . "\n". 	
									'<TOTAL_TAX_AMOUNT_7>' .number_format($total_tax_7, 4, '.', '').'</TOTAL_TAX_AMOUNT_7>' . "\n".					
									'<TOTAL_AMOUNT_7>' . number_format($order_total_gros_7, 4, '.', '').'</TOTAL_AMOUNT_7>' . "\n". 
									'<TOTAL_AMOUNT_NET_19>' . number_format($order_total_net_19, 4, '.', '').'</TOTAL_AMOUNT_NET_19>' . "\n". 	
									'<TOTAL_TAX_AMOUNT_19>' .number_format($total_tax_19, 4, '.', '').'</TOTAL_TAX_AMOUNT_19>' . "\n".					
									'<TOTAL_AMOUNT_19>' . number_format($order_total_gros_19, 4, '.', '').'</TOTAL_AMOUNT_19>' . "\n". 
									'<GEGENKONTO>' . $gegenkonto .'</GEGENKONTO>' . "\n". 
								
									'<TOTAL_AMOUNT_DISCOUNT>' . number_format($discout, 4, '.', '').'</TOTAL_AMOUNT_DISCOUNT>' . "\n". 
									'<ORDER_CURRENCY_CODE>' .$currency_code.'</ORDER_CURRENCY_CODE>' . "\n".		
									'<DISCOUNT_TOTAL_AMOUNT_NET>' .number_format($order_total_discount_net, 4, '.', '').'</DISCOUNT_TOTAL_AMOUNT_NET>' . "\n". 	
									'<DISCOUNT_TOTAL_AMOUNT>' . number_format($order_total_discount_gros, 4, '.', '').'</DISCOUNT_TOTAL_AMOUNT>' . "\n". 
									'<DISCOUNT_TOTAL_AMOUNT_TAX>' . number_format($order_discount_tax_amount, 4, '.', '').'</DISCOUNT_TOTAL_AMOUNT_TAX>' . "\n";
								//	'</ORDER_TOTAL>' . "\n";							
			} // end if
			
			// Sind stuecklistenartikelvorhanden?
			if ($StuecklisteVorhanden==true) {
				$schema .= '<STLIST_EXISTS>1</STLIST_EXISTS>' . "\n";
			} else {
				$schema .= '<STLIST_EXISTS>0</STLIST_EXISTS>' . "\n";
			}
			
			$schema .= "</ORDER_SUMMARY>" . "\n";
		
		// get order comments
			if (SHOPSYSTEM == 'virtuemart') 
				$comments=$orders['customer_note'];
			else if ( SHOPSYSTEM == 'gambiogx' || SHOPSYSTEM == 'gambiogx2') 
					$comments=$orders['comments'];
			else if (SHOPSYSTEM != 'presta' && SHOPSYSTEM != 'shopware' && SHOPSYSTEM != 'virtuemart' && SHOPSYSTEM != 'woocommerce' &&  SHOPSYSTEM != 'joomshopping') 
				$comments=dmc_get_order_comment($orders_id, $orders['orders_status']);
			else if (SHOPSYSTEM == 'shopware' || SHOPSYSTEM == 'joomshopping'  || SHOPSYSTEM == 'woocommerce'  ) 
				$comments= $orders['order_comment'];
			else
				$comments="";
	
			$schema .=  '<ORDER_COMMENTS>' . umlaute_order_export($comments) . '</ORDER_COMMENTS>' . "\n";
			
			/* $comments_query = dmc_db_query("select comments from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . $orders_id . "' and 		orders_status_id = '" . $orders['orders_status'] . "' ");
			if ($comments =  dmc_db_fetch_array($comments_query)) {
				$schema .=  '<ORDER_COMMENTS>' . umlaute_order_export(($comments['comments'])) . '</ORDER_COMMENTS>' . "\n";
			}
			*/
			
			// 21.03.2017 - Ergaenzung um Details zu Verkaufsplattformen Magnalister
			// 11.08.2017 Unterstützung WP Lister
			$plattform="shop";
			$PlattformOrderID = $orders_id;
			$PlattformUser = ""; 
			$PlattformDatum = $orders_date;

			if (SHOPSYSTEM == 'woocommerce'  ) {
				// WP Lister
				if (strpos($comments,"eBay")!==false) {
					$plattform="ebay";
					/*	"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_ebay_order_id' AND post_id=o.ID LIMIT 1) AS ebay_order_id, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_ebay_user_id' AND post_id=o.ID LIMIT 1) AS ebay_user_id, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_ebay_account_id' AND post_id=o.ID LIMIT 1) AS ebay_account_id, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_ebay_site_id' AND post_id=o.ID LIMIT 1) AS ebay_site_id, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_ebay_account_name' AND post_id=o.ID LIMIT 1) AS ebay_account_name, ". */
					$PlattformOrderID = $orders['ebay_order_id'];
					$PlattformUser = $orders['ebay_user_id'];
					$PlattformDatum = "";
				} else if (strpos($comments,"Amazon")!==false) {
					$plattform="amazon";
					/*	"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_wpla_amazon_order_id' AND post_id=o.ID LIMIT 1) AS amazon_order_id, ". */
					$PlattformOrderID = $orders['amazon_order_id'];
					$PlattformUser = "";
					$PlattformDatum = "";
				} else if (strpos($comments,"Rakuten")!==false) {
					$plattform="rakuten";
					$PlattformOrderID = "";
					$PlattformUser = "";
					$PlattformDatum = "";
				} 
			} else {
				/* $beispiel= "magnalister-Verarbeitung (eBay)
				eBayOrderID: 282184809591-1643881072011
				eBay User:   peetje2201Datum: 2017-02-23 14:05:39";
				$beispiel= "magnalister-Verarbeitung (Amazon)
				AmazonOrderID: 203-1970930-1920351Datum: 2017-02-23 13:12:2"; */
				// Wenn eBay Kommentar eBayOrderID und eBay User sowie eBay Datum ermitteln
				if (strpos($comments,"eBay")!==false) {
					$plattform="ebay";
					$PlattformOrderID = trim(substr($comments,strpos($comments,"eBayOrderID")+12,strpos($comments,"eBay User")-strpos($comments,"eBayOrderID")-12));
					$PlattformUser = trim(substr($comments,strpos($comments,"eBay User")+10,strpos($comments,"Datum:")-strpos($comments,"eBay User")-10));
					$PlattformDatum = trim(substr($comments,strpos($comments,"Datum:")+7));
				} else if (strpos($comments,"Amazon")!==false) {
					$plattform="amazon";
					$PlattformOrderID = trim(substr($comments,strpos($comments,"AmazonOrderID")+14,strpos($comments,"Datum:")-strpos($comments,"AmazonOrderID")-14));
					$PlattformUser = "";
					$PlattformDatum = trim(substr($comments,strpos($comments,"Datum:")+7));
				} else if (strpos($comments,"Rakuten")!==false) {
					$plattform="rakuten";
					$PlattformOrderID = trim(substr($comments,strpos($comments,"RakutenOrderID")+14,strpos($comments,"Datum:")-strpos($comments,"RakutenOrderID")-14));
					$PlattformUser = "";
					$PlattformDatum = trim(substr($comments,strpos($comments,"Datum:")+7));
				}
			}
			
			$schema .=  '<PLATFORM>' . umlaute_order_export($plattform) . '</PLATFORM>' . "\n";	
			$schema .=  '<PLATFORM_ORDER_ID>' . umlaute_order_export($PlattformOrderID) . '</PLATFORM_ORDER_ID>' . "\n";	
			$schema .=  '<PLATFORM_USER>' . umlaute_order_export($PlattformUser) . '</PLATFORM_USER>' . "\n";	
			$schema .=  '<PLATFORM_DATUM>' . umlaute_order_export($PlattformDatum) . '</PLATFORM_DATUM>' . "\n";	
			
			
		//	$schema .= 	'</ORDER_INFO>' . "\n\n";

			
?>