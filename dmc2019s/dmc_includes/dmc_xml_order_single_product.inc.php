<?php
/************************************************
*                                            	*
*  dmConnector Export							*
*  dmc_xml_order_single_product.inc.php			*
*  XML Ausgabe bestelltes Produkte in XML Datei	*
*  Einbinden in dmc_xml_order_products.php		*
*  Copyright (C) 2011 DoubleM-GmbH.de			*
*                                               *
*************************************************/
			
			// generate product schema
			$schema .= '<PRODUCT>' . "\n";
			// product position 3 stellig
			$schema .= '<PRODUCTS_POS>' . $products_position . '</PRODUCTS_POS>' . "\n";
			
			$schema .= 	'<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
						'<PRODUCTS_QUANTITY>' . $products_quantity . '</PRODUCTS_QUANTITY>' . "\n" .
                       	'<ARTIKEL_ART>'.$products_type.'</ARTIKEL_ART>' . "\n".
						'<PRODUCTS_MODEL>' . $products_model . '</PRODUCTS_MODEL>' . "\n" .
						'<PRODUCTS_NAME>' . umlaute_order_export(html2ascii(substr($products_name,0,58))) . '</PRODUCTS_NAME>' . "\n" .
						'<PRODUCTS_PRICE>' . $products_price. '</PRODUCTS_PRICE>' . "\n" .
						'<PRODUCTS_PRICE_SUM>' . $products_price_sum . '</PRODUCTS_PRICE_SUM>' . "\n" .
						'<PRODUCTS_PRICE_NET>' . $products_price_net. '</PRODUCTS_PRICE_NET>' . "\n" .
						'<PRODUCTS_PRICE_NET_SUM>' . $products_price_net_sum . '</PRODUCTS_PRICE_NET_SUM>' . "\n" .
					    '<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>' . "\n" .
						'<DISCOUNT_AMOUNT_NET>0.0000</DISCOUNT_AMOUNT_NET>' . "\n" .
						'<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>' . "\n" .
						'<DISCOUNT_PRICE_AMOUNT_NET>' . $products_price_net. '</DISCOUNT_PRICE_AMOUNT_NET>' . "\n" .
						'<DISCOUNT_PRICE_LINE_AMOUNT_NET>' .$products_price_net_sum . '</DISCOUNT_PRICE_LINE_AMOUNT_NET>' . "\n" .
						'<DISCOUNT_PRICE_AMOUNT>' . $products_price. '</DISCOUNT_PRICE_AMOUNT>' . "\n" .
						'<DISCOUNT_PRICE_LINE_AMOUNT>' .$products_price_sum . '</DISCOUNT_PRICE_LINE_AMOUNT>' . "\n" .
						'<PRODUCTS_TAX>' .$products_tax . '</PRODUCTS_TAX>' . "\n".
						'<PRODUCTS_TAX_AMOUNT>' . $tax_amount . '</PRODUCTS_TAX_AMOUNT>' . "\n".
						'<PRODUCTS_TAX_LINE_AMOUNT>' . ($tax_amount*$products_quantity) . '</PRODUCTS_TAX_LINE_AMOUNT>' . "\n".
						'<PRODUCTS_TAX_FLAG>' . $products_tax_flag . '</PRODUCTS_TAX_FLAG>' . "\n".
						'<PRODUCTS_SHIPPING_CLASS>' . $shipping_class. '</PRODUCTS_SHIPPING_CLASS>'  . "\n"; 
			
            $schema .=  '</PRODUCT>' . "\n";
        
?>