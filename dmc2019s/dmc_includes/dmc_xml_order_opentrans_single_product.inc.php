<?php
/********************************************************
*                                            			*
*  dmConnector Export									*
*  dmc_xml_order_opentrans_single_product.inc.php		*
*  XML Ausgabe bestelltes Produkte in XML Datei			*
*  Einbinden in dmc_xml_opentrans_order_products.php	*
*  Copyright (C) 2011-2015 DoubleM-GmbH.de					*
*                                              	 		*
*********************************************************/
		// Abfangroutine
	// if ($products_tax=="") $products_tax="19";
	// generate product schema
	// Abfangroutine, wenn in Gamvio Preis und Discount angegeben, ist dieser Preis in der Datenbank bereits rabattiert
	/*if ($discount_percent>0.0000 && $discount_percent!="") {
		$products_price_net = $products_price_net * 100 / (100-$discount_percent);
		$products_price_net_sum = $products_price_net_sum * 100 / (100-$discount_percent);
		$products_tax_amount = $products_tax_amount * 100 / (100-$discount_percent);
	}
	*/
	$schema .='<ORDER_ITEM>' . "\n".
			// 0 and the inc
			'<LINE_ITEM_ID>' .$products_position.'</LINE_ITEM_ID>' . "\n".
			'<LINE_ITEM_ID_TEXT>' .$products_position_text.'</LINE_ITEM_ID_TEXT>' . "\n".
			'<ARTICLE_ID>' . "\n".
			'<SUPPLIER_AID>' . umlaute_order_export($products_model).'</SUPPLIER_AID>' . "\n".
			'<SUPPLIER_AID_CLASSIC_LINE>' . ($cl_art_nr).'</SUPPLIER_AID_CLASSIC_LINE>' . "\n".
			// '<SUPPLIER_SET_AID>' . ($stueckliste).'</SUPPLIER_SET_AID>' . "\n".
			'<ATTRIBUTE_ID>' .  umlaute_order_export($products_model_attributes[1]).'</ATTRIBUTE_ID>' . "\n".
			'<ATTRIBUTE_ID_2>' . $products_model_attributes[2].'</ATTRIBUTE_ID_2>' . "\n".
			'<ATTRIBUTE_ID_3>' . $products_model_attributes[3].'</ATTRIBUTE_ID_3>' . "\n".
			'<ATTRIBUTE_ID_4>' . $products_model_attributes[4].'</ATTRIBUTE_ID_4>' . "\n".
			'<ATTRIBUTE_ID_5>' . $products_model_attributes[5].'</ATTRIBUTE_ID_5>' . "\n".
			'<PRODUCTS_WAREHOUSE>' . $products_warehouse.'</PRODUCTS_WAREHOUSE>' . "\n".
			'<DESCRIPTION_SHORT>' . umlaute_order_export($products_name).'</DESCRIPTION_SHORT>' . "\n".
			'<DESCRIPTION_LONG>' .  umlaute_order_export($products_name2) .'</DESCRIPTION_LONG>' . "\n".
			'<OPTIONS>' .  umlaute_order_export($optionen) .'</OPTIONS>' . "\n";
		// Woocommerce size und color
	if (SHOPSYSTEM == 'virtuemart' || SHOPSYSTEM == 'woocommerce') {	
		$schema .='<DESCRIPTION_SHORT_SC>' . umlaute_order_export($products_name.' / '.$products_color.' / '.$products_size).'</DESCRIPTION_SHORT_SC>' . "\n";
		$schema .=	'<ARTICLE_SIZE>'.$products_size.'</ARTICLE_SIZE>' . "\n";
		$schema .=	'<ARTICLE_COLOR>'.$products_color.'</ARTICLE_COLOR>' . "\n";
	}
		// EUROPA3000 M=Manueller Artikel, N=Standardartikel, B=weiterer Text zu Artikel, T=Text mit Inhalt in _F40, D=Text mit Inhalt in _F40
	$schema .=	'<ARTICLE_TYPE>'.$products_type.'</ARTICLE_TYPE>' . "\n".
			'</ARTICLE_ID>' . "\n".	
			'<QUANTITY>' . $products_quantity.'</QUANTITY>' . "\n". 	
			'<ORDER_UNIT>' . '1'.'</ORDER_UNIT>' . "\n". 	// Bestelleinheit, Z.b. "1"
			// Typs: net_list (netto Liste), gros_list (brutto Liste), net_customer (Kundenspezifischer Endpreis ohne Umsatzsteuer), 
			//nrp (UVP), udp_XXX (weitere selbstdefinierte Preise, Bsp: udp_aircargo_price)
			'<ARTICLE_PRICE_NET>' . "\n".
				'<PRICE_AMOUNT>' . number_format($products_price_net, 4, '.', '').'</PRICE_AMOUNT>' . "\n".			// Einzelpreis, zB. 399.99
				'<PRICE_LINE_AMOUNT>' . number_format($products_price_net_sum, 4, '.', '').'</PRICE_LINE_AMOUNT>' . "\n".	// Gesamtpreis=PRICE_AMOUNT*QUANTITY
				'<PRICE_FLAG/>' .  "\n".	// Typs: incl_freight, incl_packing, incl_assurance, incl_duty
			
				'<TAX>' . $products_tax.'</TAX>' . "\n".							// z.B. 19.0
				'<TAX_ID>' . $products_tax_id.'</TAX_ID>' . "\n".							// z.B. 1, 3
				'<TAX_AMOUNT>' . number_format($products_tax_amount, 4, '.', '').'</TAX_AMOUNT>' . "\n".				// Steuerbetrag	
				'<TAX_LINE_AMOUNT>' .number_format(($products_tax_amount*$products_quantity), 4, '.', '').'</TAX_LINE_AMOUNT>' . "\n".				// Steuerbetrag		
				'<DISCOUNT_AMOUNT>' . number_format(($discount_amount+0), 4, '.', '').'</DISCOUNT_AMOUNT>' . "\n".			
				'<DISCOUNT_PERCENT>' . number_format($discount_percent, 4, '.', '') .'</DISCOUNT_PERCENT>' . "\n".		// z.B. 20.0
				'<DISCOUNT_PRICE_AMOUNT>' .number_format($price_amount_discounted, 4, '.', '').'</DISCOUNT_PRICE_AMOUNT>' . "\n".			// Einzelpreis, zB. 399.99
				'<DISCOUNT_PRICE_LINE_AMOUNT>'. number_format($price_line_amount_discounted, 4, '.', '').'</DISCOUNT_PRICE_LINE_AMOUNT>' . "\n".	
			'</ARTICLE_PRICE_NET>' . "\n".	
			'<ARTICLE_PRICE_GROS>' . "\n".
				'<PRICE_AMOUNT>' . number_format(($products_price_net + $products_tax_amount), 4, '.', '').'</PRICE_AMOUNT>' . "\n".			// Einzelpreis, zB. 399.99
				'<PRICE_LINE_AMOUNT>' . number_format(($products_price_net + $products_tax_amount)*$products_quantity, 4, '.', '').'</PRICE_LINE_AMOUNT>' . "\n".	// Gesamtpreis=PRICE_AMOUNT*QUANTITY
				'<PRICE_FLAG/>' .  "\n".	// Typs: incl_freight, incl_packing, incl_assurance, incl_duty
				'<TAX_AMOUNT>' . number_format($products_tax_amount, 4, '.', '').'</TAX_AMOUNT>' . "\n".				// Steuerbetrag	
				'<TAX_LINE_AMOUNT>' .number_format(($products_tax_amount*$products_quantity), 4, '.', '').'</TAX_LINE_AMOUNT>' . "\n".				// Steuerbetrag		
				'<DISCOUNT_AMOUNT>' .number_format((($products_price_net+$products_tax_amount)-$price_amount_discounted_gros), 4, '.', '').'</DISCOUNT_AMOUNT>' . "\n".			
				'<DISCOUNT_PERCENT>'.number_format($discount_percent, 4, '.', '').'</DISCOUNT_PERCENT>'."\n".
				'<DISCOUNT_PRICE_AMOUNT>' .number_format($price_amount_discounted_gros, 4, '.', '').'</DISCOUNT_PRICE_AMOUNT>' . "\n".			// Einzelpreis, zB. 399.99
				'<DISCOUNT_PRICE_LINE_AMOUNT>' .number_format($price_line_amount_discounted_gros, 4, '.', '').'</DISCOUNT_PRICE_LINE_AMOUNT>' . "\n".	
			'</ARTICLE_PRICE_GROS>' . "\n".							
			'</ORDER_ITEM>' . "\n";
			
			// Get and ADD products short description	EUROPA3000 - Short_Description as single product
			$WAWI_NAME=="standard";			
			if($WAWI_NAME=="europa3000") {
				$line_item_id++;
				$products_shortdescription = dmc_get_shortdescription($order_infos['items'][$product_no]['product_id']);
				 $schema .='<ORDER_ITEM>' . "\n".
					// 0 and the inc
					'<LINE_ITEM_ID>' .$line_item_id.'</LINE_ITEM_ID>' . "\n".
					'<ARTICLE_ID>' . "\n".
						'<SUPPLIER_AID></SUPPLIER_AID>' . "\n".
						'<DESCRIPTION_SHORT>' . umlaute_order_export(trim($products_shortdescription)).'</DESCRIPTION_SHORT>' . "\n".
						'<DESCRIPTION_LONG></DESCRIPTION_LONG>' . "\n".
						// EUROPA3000 M=Manueller Artikel, N=Standardartikel, B=weiterer Text zu Artikel, T=Text mit Inhalt in _F40, D=Text mit Inhalt in _F40
						'<ARTICLE_TYPE>B</ARTICLE_TYPE>' . "\n".
					'</ARTICLE_ID>' . "\n".	
					'<QUANTITY></QUANTITY>' . "\n". 	
					'<ORDER_UNIT></ORDER_UNIT>' . "\n". 	// Bestelleinheit, Z.b. "1"
					// Typs: net_list (netto Liste), gros_list (brutto Liste), net_customer (Kundenspezifischer Endpreis ohne Umsatzsteuer), nrp (UVP), udp_XXX (weitere selbstdefinierte Preise, Bsp: udp_aircargo_price)
					'<ARTICLE_PRICE_NET>' . "\n".
						'<PRICE_AMOUNT></PRICE_AMOUNT>' . "\n".			// Einzelpreis, zB. 399.99
						'<PRICE_LINE_AMOUNT></PRICE_LINE_AMOUNT>' . "\n".	// Gesamtpreis=PRICE_AMOUNT*QUANTITY
						'<PRICE_FLAG/>' .  "\n".	// Typs: incl_freight, incl_packing, incl_assurance, incl_duty
						'<TAX></TAX>' . "\n".								// z.B. 19.0
						'<TAX_AMOUNT></TAX_AMOUNT>' . "\n".				// Steuerbetrag	
						'<TAX_LINE_AMOUNT></TAX_LINE_AMOUNT>' . "\n".				// Steuerbetrag		
						'<DISCOUNT_AMOUNT></DISCOUNT_AMOUNT>' . "\n".			
						'<DISCOUNT_PERCENT></DISCOUNT_PERCENT>' . "\n".								// z.B. 19.0
					'</ARTICLE_PRICE_NET>' . "\n".	
					'<ARTICLE_PRICE_GROS>' . "\n".
						'<PRICE_AMOUNT></PRICE_AMOUNT>' . "\n".			// Einzelpreis, zB. 399.99
						'<PRICE_LINE_AMOUNT></PRICE_LINE_AMOUNT>' . "\n".	// Gesamtpreis=PRICE_AMOUNT*QUANTITY
						'<PRICE_FLAG/>' .  "\n".	// Typs: incl_freight, incl_packing, incl_assurance, incl_duty
						'<TAX></TAX>' . "\n".								// z.B. 19.0
						'<TAX_AMOUNT></TAX_AMOUNT>' . "\n".				// Steuerbetrag	
						'<TAX_LINE_AMOUNT></TAX_LINE_AMOUNT>' . "\n".				// Steuerbetrag		
						'<DISCOUNT_AMOUNT></DISCOUNT_AMOUNT>' . "\n".			
						'<DISCOUNT_PERCENT></DISCOUNT_PERCENT>' . "\n".								// z.B. 19.0						
					'</ARTICLE_PRICE_GROS>' . "\n".							
					'</ORDER_ITEM>' . "\n";
			}
      
?>