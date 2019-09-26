<?php
/************************************************
*                                            	*
*  dmConnector Export fuer Handelsstuecklisten		*
*  dmc_xml_order_stlist_ products.inc.php				*
*  XML Ausgabe bestellten Produkte in XML Datei	*
*  Einbinden in dmconnector_export.php			*
*  Copyright (C) 2011 DoubleM-GmbH.de			*
*                                               *
*************************************************/

		$products_stlist_pos=$products_pos*100;
			
		
		if (SHOPSYSTEM == 'veyton')  {
	  	} else if (SHOPSYSTEM == 'presta') {
		} else {
			fwrite($dateihandle, "dmc_xml_order_stlist_ products.inc.php query =  $query\n");	
			
			// id	 artnr	 bezeichnung	 set_artnr	 set_position	 menge	 einheit	 preis	 mwst
			$query = "select id AS orders_products_id, id AS products_id, artnr AS products_model, bezeichnung AS products_name, '0' AS products_price, '0' AS final_price, '0' AS products_tax, menge AS products_quantity, 0 AS discount_percent, 0 AS discount_amount FROM dmc_handelsstueckliste WHERE set_artnr='" . $products_model . "'";
			$products_st_query = dmc_db_query($query);
		}
		
		while ($products_st = dmc_db_fetch_array($products_st_query))
        {
			// products infos
			$products_id=$products_st['products_id'];
			$products_quantity=$products_st['products_quantity'];
			$products_name=$products_st['products_name'];
			$products_tax=$products_st['products_tax'];
			$products_type='StListArtikel';
			
			$products_stlist_pos++;
			$products_pos++;
			// product position 3 stellig
			if ($products_pos<10) 
				$products_position = '00' . $products_pos;
			else if ($products_pos<100) 
				$products_position = '0' . $products_pos;
			else 
				$products_position = '' . $products_pos;

			
			// Preis von brutto auf netto umrechnen - Recalculate price from gross to net
			// if ($products_st['allow_tax']==1) $products_st['final_price']=$products_st['final_price']/(1+$products_st['products_tax']*0.01);
			// Preiskalkulation Brutto/Netto Shop
			if (!BRUTTO_SHOP) {
				// Netto im Shop
				$pro_price=$products_st['products_price']*(1+$products_st['products_tax']*0.01);
				$tax_amount=$pro_price-($products_st['products_price']/(1+$products_st['products_tax']*0.01));
				$products_price=$pro_price;
				$products_price_sum=$products_price*$products_st['products_quantity'];
				$products_price_net=($pro_price/(1+$products_st['products_tax']*0.01));
				$products_price_net_sum=($products_price_net*$products_st['products_quantity']);
				$products_discount_amount=$products_st['discount_amount']*(1+$products_st['discount_amount']*0.01);
				$products_discount_amount_net=$products_st['discount_amount'];
				$products_discount_percent=$products_st['discount_percent'];
			} else {
				// Brutto im Shop
				$tax_amount=$products_st['products_price']-($products_st['products_price']/(1+$products_st['products_tax']*0.01));
				$products_price=$products_st['products_price'];
				$products_price_sum=$products_st['final_price'];
				$products_price_net=($products_st['products_price']/(1+$products_st['products_tax']*0.01)) ;
				$products_price_net_sum=($products_st['final_price']/(1+$products_st['products_tax']*0.01)) ;
				$products_discount_amount=$products_st['discount_amount'];
				$products_discount_amount_net=$products_st['discount_amount']/(1+$products_st['products_tax']*0.01);
				$products_discount_percent=$products_st['discount_percent'];
				/*
				$tax_amount=$products_st['products_price']*(1+$products_st['products_tax']*0.01)-($products_st['products_price']);
				$products_price=$products_st['products_price']*(1+$products_st['products_tax']*0.01);
				$products_price_sum=$products_st['final_price']*(1+$products_st['products_tax']*0.01);
				$products_price_net=($products_st['products_price']) ;
				$products_price_net_sum=($products_st['final_price']);
				*/
			} // end if brutto netto
			
			// produktzwischensummen sind nicht fuer stlistartikel relevant
			//$products_subtotal_amount_net+=$products_price_net_sum;
			//$products_subtotal_amount+=$products_price_sum;
			//$products_subtotal_discount_amount_net+=$products_discount_amount_net;
			//$products_subtotal_discount_amount+=$products_discount_amount;
			//$products_subtotal_tax_amount+=$tax_amount;
			
			$products_tax=$products_st['products_tax'];
			// pruefe auf zweiten steuersatz -> $products_tax steuersatz von aktuellem produkt
			// $products_tax_percent_1=0 bei ersten produkt
			if ($products_tax_percent_1==0) {
				// steuersatz fuer erstes produkt setzen, wird im footer ggfls benoetigt
				$products_tax_percent_1=$products_tax;
			} else if ($products_tax_percent_1<>$products_tax) {
				// steuersatz fuer Produkt x anders als fuer y, wird im footer ggfls benoetigt
				$products_tax_percent_2=$products_tax_percent_1;
			}
			
			// Bei einem Variantenartikel die Artikelnummer ermitteln sind nicht fuer stlist artikel relevant
		/*	if (SHOPSYSTEM != 'veyton' && SHOPSYSTEM != 'presta') {
	           $art_variante = dmc_order_var_artnr($orders['orders_id'],$products_st['orders_products_id'], $products_st['products_model']  );
			} else {
				// Veyton
				$art_variante = "";
			}

			if ($art_variante!="")
				$products_model=$art_variante;
			else 
				$products_model= html2ascii($products_st['products_model']) ;
			*/
			$products_model= html2ascii($products_st['products_model']) ;
			// generate XML product schema
			include('dmc_xml_order_single_product.inc.php');
		
		} // end while products
       
		
	
?>