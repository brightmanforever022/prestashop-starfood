<?php
/************************************************
*                                            	*
*  dmConnector Export							*
*  dmc_xml_order_special_products.inc.php		*
*  XML Ausgabe Versandkosten Produkte etc in XML Datei	*
*  Einbinden in dmconnector_export.php			*
*  Copyright (C) 2011 DoubleM-GmbH.de			*
*                                               *
*************************************************/

			// Gutschein als Produkt
			if (EXPORT_GV_AS_PRODUCT && SHOPSYSTEM != 'presta'  && SHOPSYSTEM != 'virtuemart' && SHOPSYSTEM != 'woocommerce') {
		   // Gutschein ermitteln
			if (SHOPSYSTEM == 'veyton') {
				$versand_sql = "SELECT * FROM ".TABLE_ORDERS_TOTAL.
					" WHERE (orders_total_key=\"ot_gv\") ".
					"AND orders_id = " . $orders['orders_id'];
			} else {
				$versand_sql = 
					"SELECT * FROM ".TABLE_ORDERS_TOTAL.
					" WHERE (class=\"ot_gv\" OR class=\"ot_coupon\") ".
					"AND orders_id = " . $orders['orders_id'];
			}
		
			$versand_query = dmc_db_query($versand_sql);
	        if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
					$rcm_versandart=$versanddata['title'];
					if (BRUTTO_SHOP) {
						// Bruttopreise im Shop
						$rcm_versandkosten = $versanddata['value'];	 
						$rcm_versandkosten_net = $rcm_versandkosten/TAX_DISCOUNT;
					} else {
						$rcm_versandkosten = $versanddata['value']*TAX_DISCOUNT;	 
						$rcm_versandkosten_net = $rcm_versandkosten;
					}
				
					// products infos
					$products_id='30000';
					$products_quantity=1;
					$products_model=EXPORT_GV_AS_PRODUCT_SKU;
					$products_name=$products['products_name'];
					$products_type='Zu-/Abschlag (Artikel)';
					$products_price=$rcm_versandkosten*-1;
					$products_price_sum=$rcm_versandkosten*-1;
					$products_price_net=$rcm_versandkosten_net*-1;
					$products_price_net_sum=$rcm_versandkosten_net*-1;
					$products_tax='19';
					
					$products_pos++;
					// product position 3 stellig
					if ($products_pos<10) 
						$products_position .= '00' . $products_pos;
					else if ($products_pos<100) 
						$products_position .= '0' . $products_pos;
					else 
						$products_position .= '' . $products_pos;

					// generate XML product schema
					include('dmc_xml_order_single_product.inc.php');
								
			} // end if (($versand_query) - gutschein 
		} // end  if (EXPORT_GV_AS_PRODUCT) 
		//$orders['orders_id']=1;
		// Bonus als Produkt
		if (EXPORT_BONUS_AS_PRODUCT && SHOPSYSTEM != 'woocommerce') {
		   // Gutschein ermitteln
		    if (SHOPSYSTEM == 'veyton') {
				$versand_sql = "SELECT * FROM ".TABLE_ORDERS_TOTAL.
					" WHERE (orders_total_key=\"ot_coupon\") ".
					"AND orders_id = " . $orders['orders_id'];
			} else {
				$versand_sql = 
					"SELECT * FROM ".TABLE_ORDERS_TOTAL.
					" WHERE (class=\"ot_bonus_fee\") ".
					"AND orders_id = " . $orders['orders_id'];
			}
		
			$versand_query = dmc_db_query($versand_sql);
	        if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
					$rcm_versandart=$versanddata['title'];
					if (BRUTTO_SHOP) {
						// Bruttopreise im Shop
						$rcm_versandkosten = $versanddata['value'];	 
						$rcm_versandkosten_net = $rcm_versandkosten/TAX_DISCOUNT;
					} else {
						$rcm_versandkosten = $versanddata['value']*TAX_DISCOUNT;	 
						$rcm_versandkosten_net = $rcm_versandkosten;
					}
				
					// products infos
					$products_id='30000';
					$products_quantity=1;
					$products_model=EXPORT_GV_AS_PRODUCT_SKU;
					$products_name=$products['products_name'];
					$products_type='Zu-/Abschlag (Artikel)';
					$products_price=$rcm_versandkosten*-1;
					$products_price_sum=$rcm_versandkosten*-1;
					$products_price_net=$rcm_versandkosten_net*-1;
					$products_price_net_sum=$rcm_versandkosten_net*-1;
					$products_tax='19';
					$products_amount=$products_price_sum*($products_tax/100);
					
					$products_pos++;
					// product position 3 stellig
					if ($products_pos<10) 
						$products_position .= '00' . $products_pos;
					else if ($products_pos<100) 
						$products_position .= '0' . $products_pos;
					else 
						$products_position .= '' . $products_pos;

					// generate XML product schema
					include('dmc_xml_order_single_product.inc.php');
								
			} // end if (($versand_query) - gutschein 
		} // end  if (EXPORT_BONUS_AS_PRODUCT) 
		
		if (EXPORT_BONUS_AS_PRODUCT && SHOPSYSTEM != 'woocommerce') {
		// Bonus class=\"ot_discount\"
				if (SHOPSYSTEM == 'veyton') {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (orders_total_key=\"ot_gv\") and orders_id = " . $orders['orders_id'];
				} else {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (class=\"ot_bonus_fee\") and orders_id = " . $orders['orders_id'];
				}
				$versand_query = dmc_db_query($versand_sql);
	            if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
					$rcm_versandart=$versanddata['title'];
					  if (BRUTTO_SHOP) {
						// Bruttopreise im Shop
						$rcm_versandkosten = $versanddata['value'];	 
						$rcm_versandkosten_net = $rcm_versandkosten/TAX_DISCOUNT;
					  } else {
						$rcm_versandkosten = $versanddata['value']*TAX_DISCOUNT;	 
						$rcm_versandkosten_net = $rcm_versandkosten;
					  }    
					// Bonus - als Artikel anfügen
					$schema .= 	"<PRODUCT>". "\n";
					  
					  $products_pos++;
					  if ($products_pos<10) $schema .= '<PRODUCTS_POS>00' . $products_pos . '</PRODUCTS_POS>' . "\n";
					  else if ($products_pos<100) $schema .= '<PRODUCTS_POS>0' . $products_pos . '</PRODUCTS_POS>' . "\n";
					  else $schema .= '<PRODUCTS_POS>' . $products_pos . '</PRODUCTS_POS>' . "\n";

					$schema .=		'<PRODUCTS_ID>30001</PRODUCTS_ID>'  . "\n" .
									"<PRODUCTS_QUANTITY>-1</PRODUCTS_QUANTITY>". "\n".
									'<PRODUCTS_MODEL>'.EXPORT_BONUS_AS_PRODUCT_SKU.'</PRODUCTS_MODEL>'  . "\n".	// Artikelnummer Gutschein
										'<ARTIKEL_ART>Zu-/Abschlag (Artikel)</ARTIKEL_ART>' . "\n".
										'<PRODUCTS_NAME>' . umlaute_order_export(html2ascii($rcm_versandart)) . '</PRODUCTS_NAME>'  . "\n" .
									"<PRODUCTS_SHORTDESC/>". "\n".
									"<PRODUCTS_PRICE>" .abs($rcm_versandkosten). "</PRODUCTS_PRICE>". "\n".
									'<PRODUCTS_PRICE_SUM>' . $rcm_versandkosten. '</PRODUCTS_PRICE_SUM>' . "\n" .
									'<PRODUCTS_PRICE_NET>' . $rcm_versandkosten_net . '</PRODUCTS_PRICE_NET>' . "\n" .
									'<PRODUCTS_PRICE_NET_SUM>' . $rcm_versandkosten_net . '</PRODUCTS_PRICE_NET_SUM>' . "\n" .
									  '<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>' . "\n" .
									  '<DISCOUNT_AMOUNT_NET>0.0000</DISCOUNT_AMOUNT_NET>' . "\n" .
									  '<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>' . "\n" .
									  '<DISCOUNT_PRICE_AMOUNT_NET>' . ($rcm_versandkosten_net). '</DISCOUNT_PRICE_AMOUNT_NET>' . "\n" .
									  '<DISCOUNT_PRICE_LINE_AMOUNT_NET>' .($rcm_versandkosten_net) . '</DISCOUNT_PRICE_LINE_AMOUNT_NET>' . "\n" .
									  '<DISCOUNT_PRICE_AMOUNT>' . ($rcm_versandkosten). '</DISCOUNT_PRICE_AMOUNT>' . "\n" .
									  '<DISCOUNT_PRICE_LINE_AMOUNT>' .($rcm_versandkosten) . '</DISCOUNT_PRICE_LINE_AMOUNT>' . "\n" .
									"<PRODUCTS_WEIGHT>0</PRODUCTS_WEIGHT>". "\n".
									"<PRODUCTS_TAX>19</PRODUCTS_TAX>". "\n".
									'<PRODUCTS_TAX_AMOUNT>' . ($rcm_versandkosten-$rcm_versandkosten_net) . '</PRODUCTS_TAX_AMOUNT>' . "\n".
								    '<PRODUCTS_TAX_LINE_AMOUNT>' . ($rcm_versandkosten-$rcm_versandkosten_net)  . '</PRODUCTS_TAX_LINE_AMOUNT>' . "\n".
			                        '<PRODUCTS_TAX_FLAG>1</PRODUCTS_TAX_FLAG>' . "\n".
								"</PRODUCT>". "\n";
				} // endif Bonus		  
		} //endif EXPORT_BONUS_AS_PRODUCT
			
		if(EXPORT_DISCOUNT_PAYMENT_AS_PRODUCT && SHOPSYSTEM != 'woocommerce') {
				// Discount  ot_payment
				if (SHOPSYSTEM == 'veyton') {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (orders_total_key=\"ot_discount\") and orders_id = " . $orders['orders_id'];
				} else {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (class=\"ot_discount\") and orders_id = " . $orders['orders_id'];
				}
			  $versand_query = dmc_db_query($versand_sql);
	            if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
					$rcm_versandart=$versanddata['title'];
					  
					  if (BRUTTO_SHOP) {
						// Bruttopreise im Shop
						$rcm_versandkosten = $versanddata['value'];	 
						$rcm_versandkosten_net = $rcm_versandkosten/TAX_DISCOUNT;
					  } else {
						$rcm_versandkosten = $versanddata['value']*TAX_DISCOUNT;	 
						$rcm_versandkosten_net = $rcm_versandkosten;
					  }
					  
					// discount - als Artikel anfügen
					$schema .= 	"<PRODUCT>". "\n";
					  
					  $products_pos++;
					  if ($products_pos<10) $schema .= '<PRODUCTS_POS>00' . $products_pos . '</PRODUCTS_POS>' . "\n";
					  else if ($products_pos<100) $schema .= '<PRODUCTS_POS>0' . $products_pos . '</PRODUCTS_POS>' . "\n";
					  else $schema .= '<PRODUCTS_POS>' . $products_pos . '</PRODUCTS_POS>' . "\n";

					$schema .=		'<PRODUCTS_ID>30002</PRODUCTS_ID>'  . "\n" .
									"<PRODUCTS_QUANTITY>-1</PRODUCTS_QUANTITY>". "\n".
									'<PRODUCTS_MODEL>'.EXPORT_DISCOUNT_AS_PRODUCT_SKU.'</PRODUCTS_MODEL>'  . "\n".  //Artikelnummer  Zahlungsdiscount
										'<ARTIKEL_ART>Zu-/Abschlag (Artikel)</ARTIKEL_ART>' . "\n".
										'<PRODUCTS_NAME>' . umlaute_order_export(html2ascii($rcm_versandart)) . '</PRODUCTS_NAME>'  . "\n" .
									"<PRODUCTS_SHORTDESC/>". "\n".
									"<PRODUCTS_PRICE>" .abs($rcm_versandkosten). "</PRODUCTS_PRICE>". "\n".
									'<PRODUCTS_PRICE_SUM>' . $rcm_versandkosten. '</PRODUCTS_PRICE_SUM>' . "\n" .
									'<PRODUCTS_PRICE_NET>' . $rcm_versandkosten_net . '</PRODUCTS_PRICE_NET>' . "\n" .
									'<PRODUCTS_PRICE_NET_SUM>' . $rcm_versandkosten_net . '</PRODUCTS_PRICE_NET_SUM>' . "\n" .
									  '<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>' . "\n" .
									  '<DISCOUNT_AMOUNT_NET>0.0000</DISCOUNT_AMOUNT_NET>' . "\n" .
									  '<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>' . "\n" .
									  '<DISCOUNT_PRICE_AMOUNT_NET>' . ($rcm_versandkosten_net). '</DISCOUNT_PRICE_AMOUNT_NET>' . "\n" .
									  '<DISCOUNT_PRICE_LINE_AMOUNT_NET>' .($rcm_versandkosten_net) . '</DISCOUNT_PRICE_LINE_AMOUNT_NET>' . "\n" .
									  '<DISCOUNT_PRICE_AMOUNT>' . ($rcm_versandkosten). '</DISCOUNT_PRICE_AMOUNT>' . "\n" .
									  '<DISCOUNT_PRICE_LINE_AMOUNT>' .($rcm_versandkosten) . '</DISCOUNT_PRICE_LINE_AMOUNT>' . "\n" .
									"<PRODUCTS_WEIGHT>0</PRODUCTS_WEIGHT>". "\n".
									"<PRODUCTS_TAX>19</PRODUCTS_TAX>". "\n".
									'<PRODUCTS_TAX_AMOUNT>' . ($rcm_versandkosten-$rcm_versandkosten_net) . '</PRODUCTS_TAX_AMOUNT>' . "\n".
								    '<PRODUCTS_TAX_LINE_AMOUNT>' . ($rcm_versandkosten-$rcm_versandkosten_net)  . '</PRODUCTS_TAX_LINE_AMOUNT>' . "\n".
			                        '<PRODUCTS_TAX_FLAG>1</PRODUCTS_TAX_FLAG>' . "\n".
								"</PRODUCT>". "\n";
				} // endif Discount	
		}//endif EXPORT_DISCOUNT_PAYMENT_AS_PRODUCT
			
		if(EXPORT_PREPAYMENT_AS_PRODUCT && SHOPSYSTEM != 'woocommerce') {
				// Vorkasse Rabatt  ot_payment
				if (SHOPSYSTEM == 'veyton') {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (orders_total_key=\"payment\") and orders_id = " . $orders['orders_id'];
				} else {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (class=\"ot_payment\") and orders_id = " . $orders['orders_id'];
				}
		      $versand_query = dmc_db_query($versand_sql);
	            if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
					$rcm_versandart=$versanddata['title'];
					
					  if (BRUTTO_SHOP) {
						// Bruttopreise im Shop
						$rcm_versandkosten = $versanddata['value'];	 
						$rcm_versandkosten_net = $rcm_versandkosten/TAX_DISCOUNT;
					  } else {
						$rcm_versandkosten = $versanddata['value']*TAX_DISCOUNT;	 
						$rcm_versandkosten_net = $rcm_versandkosten;
					  }
					
					// vorkasse rabatt - als Artikel anfügen
					$schema .= 	"<PRODUCT>". "\n";
					  
					  $products_pos++;
					  if ($products_pos<10) $schema .= '<PRODUCTS_POS>00' . $products_pos . '</PRODUCTS_POS>' . "\n";
					  else if ($products_pos<100) $schema .= '<PRODUCTS_POS>0' . $products_pos . '</PRODUCTS_POS>' . "\n";
					  else $schema .= '<PRODUCTS_POS>' . $products_pos . '</PRODUCTS_POS>' . "\n";

					$schema .=		'<PRODUCTS_ID>30003</PRODUCTS_ID>'  . "\n" .
									"<PRODUCTS_QUANTITY>-1</PRODUCTS_QUANTITY>". "\n".
									'<PRODUCTS_MODEL>'.EXPORT_PREPAYMENT_AS_PRODUCT_SKU.'</PRODUCTS_MODEL>'  . "\n".	 //Artikelnummer Vorkasse 
										'<ARTIKEL_ART>Zu-/Abschlag (Artikel)</ARTIKEL_ART>' . "\n".
									'<PRODUCTS_NAME>' . umlaute_order_export(html2ascii($rcm_versandart)) . '</PRODUCTS_NAME>'  . "\n" .
									"<PRODUCTS_SHORTDESC/>". "\n".
									"<PRODUCTS_PRICE>" .abs($rcm_versandkosten). "</PRODUCTS_PRICE>". "\n".
									'<PRODUCTS_PRICE_SUM>' . $rcm_versandkosten. '</PRODUCTS_PRICE_SUM>' . "\n" .
									'<PRODUCTS_PRICE_NET>' . $rcm_versandkosten_net . '</PRODUCTS_PRICE_NET>' . "\n" .
									'<PRODUCTS_PRICE_NET_SUM>' . $rcm_versandkosten_net . '</PRODUCTS_PRICE_NET_SUM>' . "\n" .
									  '<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>' . "\n" .
									  '<DISCOUNT_AMOUNT_NET>0.0000</DISCOUNT_AMOUNT_NET>' . "\n" .
									  '<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>' . "\n" .
									  '<DISCOUNT_PRICE_AMOUNT_NET>' . ($rcm_versandkosten_net). '</DISCOUNT_PRICE_AMOUNT_NET>' . "\n" .
									  '<DISCOUNT_PRICE_LINE_AMOUNT_NET>' .($rcm_versandkosten_net) . '</DISCOUNT_PRICE_LINE_AMOUNT_NET>' . "\n" .
									  '<DISCOUNT_PRICE_AMOUNT>' . ($rcm_versandkosten). '</DISCOUNT_PRICE_AMOUNT>' . "\n" .
									  '<DISCOUNT_PRICE_LINE_AMOUNT>' .($rcm_versandkosten) . '</DISCOUNT_PRICE_LINE_AMOUNT>' . "\n" .
									"<PRODUCTS_WEIGHT>0</PRODUCTS_WEIGHT>". "\n".
									"<PRODUCTS_TAX>19</PRODUCTS_TAX>". "\n".
									'<PRODUCTS_TAX_AMOUNT>' . ($rcm_versandkosten-$rcm_versandkosten_net) . '</PRODUCTS_TAX_AMOUNT>' . "\n".
								    '<PRODUCTS_TAX_LINE_AMOUNT>' . ($rcm_versandkosten-$rcm_versandkosten_net)  . '</PRODUCTS_TAX_LINE_AMOUNT>' . "\n".
			                        '<PRODUCTS_TAX_FLAG>1</PRODUCTS_TAX_FLAG>' . "\n".
								"</PRODUCT>". "\n";
				} // endif Bonus	
		}//endif EXPORT_PREPAYMENT_AS_PRODUCT
			
		if(EXPORT_PAYPAL_AS_PRODUCT && SHOPSYSTEM != 'woocommerce'){
		    // Paypal Gebühren ermitteln
				if (SHOPSYSTEM == 'veyton') {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (orders_total_key=\"ot_paypal_fee\") and orders_id = " . $orders['orders_id'];
				} else {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (class=\"ot_paypal_fee\") and orders_id = " . $orders['orders_id'];
				}
		      $versand_query = dmc_db_query($versand_sql);
	            if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
					$rcm_versandart=$versanddata['title'];
					if (BRUTTO_SHOP) {
						// Bruttopreise im Shop
						$rcm_versandkosten = $versanddata['value'];	 
						$rcm_versandkosten_net = $rcm_versandkosten/TAX_SURCHARGE;
					  } else {
						$rcm_versandkosten = $versanddata['value']*TAX_SURCHARGE;	 
						$rcm_versandkosten_net = $rcm_versandkosten;
					  }
					// Paypal Gebühren - als Artikel anfügen
					$schema .= 	"<PRODUCT>". "\n";
					  
					  $products_pos++;
					  if ($products_pos<10) $schema .= '<PRODUCTS_POS>00' . $products_pos . '</PRODUCTS_POS>' . "\n";
					  else if ($products_pos<100) $schema .= '<PRODUCTS_POS>0' . $products_pos . '</PRODUCTS_POS>' . "\n";
					  else $schema .= '<PRODUCTS_POS>' . $products_pos . '</PRODUCTS_POS>' . "\n";

					$schema .=		'<PRODUCTS_ID>40000</PRODUCTS_ID>'  . "\n" .				"<PRODUCTS_QUANTITY>1</PRODUCTS_QUANTITY>". "\n".
									'<PRODUCTS_MODEL>'.EXPORT_PAYPAL_AS_PRODUCT_SKU.'</PRODUCTS_MODEL>'  . "\n".	// Artikelnummer Paypal
									'<PRODUCTS_NAME>Bearbeitungsgebühr</PRODUCTS_NAME>'  . "\n" .
										'<ARTIKEL_ART>Zu-/Abschlag (Artikel)</ARTIKEL_ART>' . "\n".
										"<PRODUCTS_SHORTDESC/>". "\n".
									"<PRODUCTS_PRICE>" .$rcm_versandkosten. "</PRODUCTS_PRICE>". "\n".
									'<PRODUCTS_PRICE_SUM>' . $rcm_versandkosten. '</PRODUCTS_PRICE_SUM>' . "\n" .
									'<PRODUCTS_PRICE_NET>' . $rcm_versandkosten_net . '</PRODUCTS_PRICE_NET>' . "\n" .
									'<PRODUCTS_PRICE_NET_SUM>' . $rcm_versandkosten_net . '</PRODUCTS_PRICE_NET_SUM>' . "\n" .
									  '<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>' . "\n" .
									  '<DISCOUNT_AMOUNT_NET>0.0000</DISCOUNT_AMOUNT_NET>' . "\n" .
									  '<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>' . "\n" .
									  '<DISCOUNT_PRICE_AMOUNT_NET>' . ($rcm_versandkosten_net). '</DISCOUNT_PRICE_AMOUNT_NET>' . "\n" .
									  '<DISCOUNT_PRICE_LINE_AMOUNT_NET>' .($rcm_versandkosten_net) . '</DISCOUNT_PRICE_LINE_AMOUNT_NET>' . "\n" .
									  '<DISCOUNT_PRICE_AMOUNT>' . ($rcm_versandkosten). '</DISCOUNT_PRICE_AMOUNT>' . "\n" .
									  '<DISCOUNT_PRICE_LINE_AMOUNT>' .($rcm_versandkosten) . '</DISCOUNT_PRICE_LINE_AMOUNT>' . "\n" .
									"<PRODUCTS_WEIGHT>0</PRODUCTS_WEIGHT>". "\n".
									"<PRODUCTS_TAX>19</PRODUCTS_TAX>". "\n".
									'<PRODUCTS_TAX_AMOUNT>' . ($rcm_versandkosten-$rcm_versandkosten_net) . '</PRODUCTS_TAX_AMOUNT>' . "\n".
								    '<PRODUCTS_TAX_LINE_AMOUNT>' . ($rcm_versandkosten-$rcm_versandkosten_net)  . '</PRODUCTS_TAX_LINE_AMOUNT>' . "\n".
			                        '<PRODUCTS_TAX_FLAG>1</PRODUCTS_TAX_FLAG>' . "\n".
								"</PRODUCT>". "\n";
				} // endif Paypal Gebühren 
		}//endif if(EXPORT_PAYPAL_AS_PRODUCT)
		
		if (EXPORT_COD_AS_PRODUCT && SHOPSYSTEM != 'woocommerce') {		
				// Nachnahme Gebühren ermitteln
				if (SHOPSYSTEM == 'veyton') {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (orders_total_key=\"ot_cod_fee\") and orders_id = " . $orders['orders_id'];
				} else {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (class=\"ot_cod_fee\") and orders_id = " . $orders['orders_id'];
				}
		      $versand_query = dmc_db_query($versand_sql);
	            if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
					$rcm_versandart=$versanddata['title'];
					if (BRUTTO_SHOP) {
						// Bruttopreise im Shop
						$rcm_versandkosten = $versanddata['value'];	 
						$rcm_versandkosten_net = $rcm_versandkosten/TAX_SURCHARGE;
					  } else {
						$rcm_versandkosten = $versanddata['value']*TAX_SURCHARGE;	 
						$rcm_versandkosten_net = $rcm_versandkosten;
					  }
					// Nachnahme - als Artikel anfügen
					$schema .= 	"<PRODUCT>". "\n";
					  
					  $products_pos++;
					  if ($products_pos<10) $schema .= '<PRODUCTS_POS>00' . $products_pos . '</PRODUCTS_POS>' . "\n";
					  else if ($products_pos<100) $schema .= '<PRODUCTS_POS>0' . $products_pos . '</PRODUCTS_POS>' . "\n";
					  else $schema .= '<PRODUCTS_POS>' . $products_pos . '</PRODUCTS_POS>' . "\n";

					$schema .=		'<PRODUCTS_ID>50000</PRODUCTS_ID>'  . "\n" .				
									"<PRODUCTS_QUANTITY>1</PRODUCTS_QUANTITY>". "\n".
									'<PRODUCTS_MODEL>'.EXPORT_COD_AS_PRODUCT_SKU.'</PRODUCTS_MODEL>'  . "\n".	// Artikelnummer Nachnahme
										'<ARTIKEL_ART>Zu-/Abschlag (Artikel)</ARTIKEL_ART>' . "\n".'<PRODUCTS_NAME>Nachnahme</PRODUCTS_NAME>'  . "\n" .
									"<PRODUCTS_SHORTDESC/>". "\n".
									"<PRODUCTS_PRICE>" .$rcm_versandkosten. "</PRODUCTS_PRICE>". "\n".
									'<PRODUCTS_PRICE_SUM>' . $rcm_versandkosten. '</PRODUCTS_PRICE_SUM>' . "\n" .
									'<PRODUCTS_PRICE_NET>' . $rcm_versandkosten_net . '</PRODUCTS_PRICE_NET>' . "\n" .
									'<PRODUCTS_PRICE_NET_SUM>' . $rcm_versandkosten_net . '</PRODUCTS_PRICE_NET_SUM>' . "\n" .
									  '<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>' . "\n" .
									  '<DISCOUNT_AMOUNT_NET>0.0000</DISCOUNT_AMOUNT_NET>' . "\n" .
									  '<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>' . "\n" .
									  '<DISCOUNT_PRICE_AMOUNT_NET>' . ($rcm_versandkosten_net). '</DISCOUNT_PRICE_AMOUNT_NET>' . "\n" .
									  '<DISCOUNT_PRICE_LINE_AMOUNT_NET>' .($rcm_versandkosten_net) . '</DISCOUNT_PRICE_LINE_AMOUNT_NET>' . "\n" .
									  '<DISCOUNT_PRICE_AMOUNT>' . ($rcm_versandkosten). '</DISCOUNT_PRICE_AMOUNT>' . "\n" .
									  '<DISCOUNT_PRICE_LINE_AMOUNT>' .($rcm_versandkosten) . '</DISCOUNT_PRICE_LINE_AMOUNT>' . "\n" .
									"<PRODUCTS_WEIGHT>0</PRODUCTS_WEIGHT>". "\n".
									"<PRODUCTS_TAX>19</PRODUCTS_TAX>". "\n".
									'<PRODUCTS_TAX_AMOUNT>' . ($rcm_versandkosten-$rcm_versandkosten_net) . '</PRODUCTS_TAX_AMOUNT>' . "\n".
								    '<PRODUCTS_TAX_LINE_AMOUNT>' . ($rcm_versandkosten-$rcm_versandkosten_net)  . '</PRODUCTS_TAX_LINE_AMOUNT>' . "\n".
			                        '<PRODUCTS_TAX_FLAG>1</PRODUCTS_TAX_FLAG>' . "\n".
								"</PRODUCT>". "\n";
				} // endif Nachnahme Gebühren 
		} // end if (EXPORT_COD_AS_PRODUCT) 
			
		if (EXPORT_SHIPPING_AS_PRODUCT && SHOPSYSTEM != 'woocommerce') {
				
			// Versandkosten ermitteln
		  		if (SHOPSYSTEM == 'veyton') {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (orders_total_name=\"Versandkosten\") and orders_id = " . $orders['orders_id'];
				} else {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (class=\"ot_shipping\") and orders_id = " . $orders['orders_id'];
				}
		     $versand_query = dmc_db_query($versand_sql);
	            if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
					$rcm_versandart=$versanddata['title'];
					/* if (BRUTTO_SHOP) {
						// Bruttopreise im Shop  orders_total_price  orders_total_tax 
						if (SHOPSYSTEM != 'veyton') $rcm_versandkosten = $versanddata['value'];	 
						else $rcm_versandkosten = $versanddata['orders_total_price'];	 
						$rcm_versandkosten_net = $rcm_versandkosten/TAX_SURCHARGE;
					  } else { */
						$rcm_versandkosten_net = $rcm_versandkosten;
						if (SHOPSYSTEM != 'veyton') $rcm_versandkosten = $versanddata['value']*TAX_SURCHARGE;	
						else $rcm_versandkosten = $versanddata['orders_total_price']*TAX_SURCHARGE;	
						
					 /* } */
					// VERSANDKOSTEN - als Artikel anfügen, wenn > 0
					if ($rcm_versandkosten>0) {
						$schema .= 	"<PRODUCT>". "\n";
						  
						  $products_pos++;
						  if ($products_pos<10) $schema .= '<PRODUCTS_POS>00' . $products_pos . '</PRODUCTS_POS>' . "\n";
						  else if ($products_pos<100) $schema .= '<PRODUCTS_POS>0' . $products_pos . '</PRODUCTS_POS>' . "\n";
						  else $schema .= '<PRODUCTS_POS>' . $products_pos . '</PRODUCTS_POS>' . "\n";

						$schema .=		'<PRODUCTS_ID>' . html2ascii($orders['shipping_class']) . '</PRODUCTS_ID>'  . "\n" .
										"<PRODUCTS_QUANTITY>1</PRODUCTS_QUANTITY>". "\n".
									/*	'<PRODUCTS_MODEL> '. html2ascii($orders['shipping_class']) . '</PRODUCTS_MODEL>'  . "\n". */
										'<PRODUCTS_MODEL>'.EXPORT_SHIPPING_AS_PRODUCT_SKU.'</PRODUCTS_MODEL>'  . "\n".	// Artikelnummer Versandkosten
										'<ARTIKEL_ART>Zu-/Abschlag (Artikel)</ARTIKEL_ART>' . "\n".
										'<PRODUCTS_NAME>' . umlaute_order_export(html2ascii($rcm_versandart)) . '</PRODUCTS_NAME>'  . "\n" .
										"<PRODUCTS_SHORTDESC/>". "\n".
										"<PRODUCTS_PRICE>" .$rcm_versandkosten. "</PRODUCTS_PRICE>". "\n".
										'<PRODUCTS_PRICE_SUM>' . $rcm_versandkosten. '</PRODUCTS_PRICE_SUM>' . "\n" .
										'<PRODUCTS_PRICE_NET>' . $rcm_versandkosten_net . '</PRODUCTS_PRICE_NET>' . "\n" .
										'<PRODUCTS_PRICE_NET_SUM>' . $rcm_versandkosten_net . '</PRODUCTS_PRICE_NET_SUM>' . "\n" .
		       							"<PRODUCTS_WEIGHT>0</PRODUCTS_WEIGHT>". "\n".
										"<PRODUCTS_TAX>19</PRODUCTS_TAX>". "\n".
										'<PRODUCTS_TAX_AMOUNT>' . ($rcm_versandkosten-$rcm_versandkosten_net) . '</PRODUCTS_TAX_AMOUNT>' . "\n".
									    '<PRODUCTS_TAX_LINE_AMOUNT>' . ($rcm_versandkosten-$rcm_versandkosten_net)  . '</PRODUCTS_TAX_LINE_AMOUNT>' . "\n".
										'<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>' . "\n" .
										'<DISCOUNT_AMOUNT_NET>0.0000</DISCOUNT_AMOUNT_NET>' . "\n" .
										'<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>' . "\n" .
										'<DISCOUNT_PRICE_AMOUNT_NET>' . ($rcm_versandkosten_net). '</DISCOUNT_PRICE_AMOUNT_NET>' . "\n" .
										'<DISCOUNT_PRICE_LINE_AMOUNT_NET>' .($rcm_versandkosten_net) . '</DISCOUNT_PRICE_LINE_AMOUNT_NET>' . "\n" .
										'<DISCOUNT_PRICE_AMOUNT>' . ($rcm_versandkosten). '</DISCOUNT_PRICE_AMOUNT>' . "\n" .
										'<DISCOUNT_PRICE_LINE_AMOUNT>' .($rcm_versandkosten) . '</DISCOUNT_PRICE_LINE_AMOUNT>' . "\n" .
									    '<PRODUCTS_TAX_FLAG>1</PRODUCTS_TAX_FLAG>' . "\n".
									"</PRODUCT>". "\n";	
					} // if ($rcm_versandkosten>0) 
				}
		} // end if (EXPORT_SHIPPING_AS_PRODUCT) {
	
?>