<?php
/************************************************
*                                            	*
*  dmConnector Export							*
*  dmc_xml_order_products.inc.php				*
*  XML Ausgabe bestellten Produkte in XML Datei	*
*  Einbinden in dmconnector_export.php			*
*  Copyright (C) 2011 DoubleM-GmbH.de			*
*                                               *
*************************************************/

		$products_pos =0;
		// produktzwischensummen
		$products_subtotal_amount_net=0;
		$products_subtotal_amount=0;
		$products_subtotal_discount_amount_net=0;
		$products_subtotal_discount_amount=0;
		$products_subtotal_tax_amount=0;
		$products_tax_percent_1=0;
		$products_tax_percent_2=0;
		
		if (SHOPSYSTEM == 'veyton')  {
	       $products_query = dmc_db_query("select orders_products_id, products_id, products_model, products_name, products_price, (products_price * products_quantity) AS final_price, products_quantity, products_tax, products_quantity, '' AS discount_percent, '' AS discount_amount from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . $orders['orders_id'] . "'");
		} else if (SHOPSYSTEM == 'presta') {
			//$products_query = dmc_db_query("select orders_products_id, products_id, products_model, products_name, products_price, (products_price * products_quantity) AS final_price, products_quantity, products_tax, products_quantity from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . $orders['orders_id'] . "'");
			$sqlquery  = 	"select op.id_order_detail AS orders_products_id, op.product_id AS products_id, ".
							"CASE WHEN p.reference ='' THEN p.ean13 ELSE p.reference END AS products_model, ".
							"op.product_name AS products_name, op.product_price AS products_price, ".
							"(op.product_price*op.product_quantity) as final_price, op.tax_rate AS products_tax, ". 
							"op.product_quantity AS products_quantity, op.reduction_percent AS discount_percent, ".
							"op.reduction_amount AS discount_amount ".
							"FROM " . TABLE_ORDERS_PRODUCTS . " op INNER JOIN ".TABLE_PRODUCTS." p ON (p.id_product = op.product_id) ".
							"WHERE op.id_order = '1' ";
			$products_query = dmc_db_query($sqlquery);
		} else {
			$products_query = dmc_db_query("select orders_products_id, products_id, products_model, products_name, products_price, final_price, products_tax, products_quantity, 0 AS discount_percent, 0 AS discount_amount from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . $orders['orders_id'] . "'");
		}
	//	fwrite($dateihandle, "products_query=  $products_query\n");	
		
		while ($products = dmc_db_fetch_array($products_query))
        {
			// products infos
			$products_id=$products['products_id'];
			$products_quantity=$products['products_quantity'];
			$products_name=$products['products_name'];
			$products_tax=$products['products_tax'];
			$products_type='Artikel';
			
			$products_pos++;
			// product position 3 stellig
			if ($products_pos<10) 
				$products_position = '00' . $products_pos;
			else if ($products_pos<100) 
				$products_position = '0' . $products_pos;
			else 
				$products_position = '' . $products_pos;

			// Preis von brutto auf netto umrechnen - Recalculate price from gross to net
			// if ($products['allow_tax']==1) $products['final_price']=$products['final_price']/(1+$products['products_tax']*0.01);
			// Preiskalkulation Brutto/Netto Shop
			if (!BRUTTO_SHOP) {
				// Netto im Shop
				$pro_price=$products['products_price']*(1+$products['products_tax']*0.01);
				$tax_amount=$pro_price-($products['products_price']/(1+$products['products_tax']*0.01));
				$products_price=$pro_price;
				$products_price_sum=$products_price*$products['products_quantity'];
				$products_price_net=($pro_price/(1+$products['products_tax']*0.01));
				$products_price_net_sum=($products_price_net*$products['products_quantity']);
				$products_discount_amount=$products['discount_amount']*(1+$products['discount_amount']*0.01);
				$products_discount_amount_net=$products['discount_amount'];
				$products_discount_percent=$products['discount_percent'];
			} else {
				// Brutto im Shop
				$tax_amount=$products['products_price']-($products['products_price']/(1+$products['products_tax']*0.01));
				$products_price=$products['products_price'];
				$products_price_sum=$products['final_price'];
				$products_price_net=($products['products_price']/(1+$products['products_tax']*0.01)) ;
				$products_price_net_sum=($products['final_price']/(1+$products['products_tax']*0.01)) ;
				$products_discount_amount=$products['discount_amount'];
				$products_discount_amount_net=$products['discount_amount']/(1+$products['products_tax']*0.01);
				$products_discount_percent=$products['discount_percent'];
				/*
				$tax_amount=$products['products_price']*(1+$products['products_tax']*0.01)-($products['products_price']);
				$products_price=$products['products_price']*(1+$products['products_tax']*0.01);
				$products_price_sum=$products['final_price']*(1+$products['products_tax']*0.01);
				$products_price_net=($products['products_price']) ;
				$products_price_net_sum=($products['final_price']);
				*/
			} // end if brutto netto
			
			// produktzwischensummen
			$products_subtotal_amount_net+=$products_price_net_sum;
			$products_subtotal_amount+=$products_price_sum;
			$products_subtotal_discount_amount_net+=$products_discount_amount_net;
			$products_subtotal_discount_amount+=$products_discount_amount;
			$products_subtotal_tax_amount+=$tax_amount;
			
			$products_tax=$products['products_tax'];
			// pruefe auf zweiten steuersatz -> $products_tax steuersatz von aktuellem produkt
			// $products_tax_percent_1=0 bei ersten produkt
			if ($products_tax_percent_1==0) {
				// steuersatz fuer erstes produkt setzen, wird im footer ggfls benoetigt
				$products_tax_percent_1=$products_tax;
			} else if ($products_tax_percent_1<>$products_tax) {
				// steuersatz fuer Produkt x anders als fuer y, wird im footer ggfls benoetigt
				$products_tax_percent_2=$products_tax_percent_1;
			}
			
			// Bei einem Variantenartikel die Artikelnummer ermitteln
			if (SHOPSYSTEM != 'veyton' && SHOPSYSTEM != 'presta') {
	           $art_variante = dmc_order_var_artnr($orders['orders_id'],$products['orders_products_id'], $products['products_model']  );
			} else {
				// Veyton
				$art_variante = "";
			}

			if ($art_variante!="")
				$products_model=$art_variante;
			else 
				$products_model= html2ascii($products['products_model']) ;
			
			// generate XML product schema
			include('dmc_xml_order_single_product.inc.php');
			// check for set products
			include('dmc_xml_order_stlist_products.inc.php');
			
		 
		} // end while products
       
		if (SHOPSYSTEM != 'presta') {
			// Sonderprodukte wie gutscheine
			include('dmc_xml_order_special_products.inc.php');
		}
			
		$schema .= '</ORDER_PRODUCTS>' . "\n"; 
	
?>