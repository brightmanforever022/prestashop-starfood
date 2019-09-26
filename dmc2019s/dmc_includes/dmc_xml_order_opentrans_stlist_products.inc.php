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

		$products_stlist_pos=0;

		// Stueckliste
		$stueckliste=$products_model;
		$products_quantity_haupt=$products_quantity;
		if (SHOPSYSTEM == 'veyton')  {
	  	} else if (SHOPSYSTEM == 'presta') {
		} else if (SHOPSYSTEM == 'woocommerce') {		
		} else if (SHOPSYSTEM == 'virtuemart') {		
		} else {
			
			// id	 artnr	 bezeichnung	 set_artnr	 set_position	 menge	 einheit	 preis	 mwst
			$query = "select id AS orders_products_id, id AS products_id, set_artnr AS products_model, bezeichnung AS products_name, '0' AS products_price, '0' AS final_price, '0' AS products_tax, menge AS products_quantity, 0 AS discount_percent, 0 AS discount_amount FROM dmc_handelsstueckliste WHERE artnr='" . $stueckliste . "'";
			fwrite($dateihandle, "dmc_xml_order_stlist_ products.inc.php query =  $query\n");	
			$products_query = dmc_db_query($query);
		}
		
		
		while ($products = dmc_db_fetch_array($products_query))
        {
			// Stuecklistenprodukte vorhanden
			$StuecklisteVorhanden=true;
			if (SHOPSYSTEM == 'veyton')  {
				// Achtung: der Preis ist der rabattierte Preis. D.h. wenn Rabatt, dann Preis umrechnen
				if ($products['products_discount']>0) { 
					$discount_percent=$products['products_discount'];
					$price_amount_discounted=$products['products_price'];
					$price_line_amount_discounted=$price_amount_discounted*$products['products_quantity'];
					$price_amount_discounted_gros=$products['products_price']*(1+$products['products_tax']/100);
					$price_line_amount_discounted_gros=$price_amount_discounted_gros*$products['products_quantity'];
					$products['products_price']=$products['products_price']*(1+$discount_percent/100);
					$products['final_price']=$products['products_price']*$products['products_quantity'];
					$discount_amount=$products['products_price']-$price_amount_discounted;
				} else { 
					$discount_percent=0;
					$price_amount_discounted=$products['products_price'];
					$price_line_amount_discounted=$price_amount_discounted*$products['products_quantity'];
					$price_amount_discounted_gros=$products['products_price']*(1+$products['products_tax']/100);
					$price_line_amount_discounted_gros=$price_amount_discounted_gros*$products['products_quantity'];
					// $products['products_price']=$products['products_price'];
					$products['final_price']=$products['products_price']*$products['products_quantity'];
					$discount_amount=0;
				}
			}
			$products_price=$products['products_price'];
			// products infos
			$products_id=$products['products_id'];
			$products_quantity=$products['products_quantity']*$products_quantity_haupt;
			$products_name=$products['products_name'];
			$products_tax_flag=$products['allow_tax'];
			$products_tax=$products['products_tax'];
			$products_type='Artikel';
			
			$products_tax_amount=($products_price*($products_tax/100)/$products_quantity);
		
			$products_stlist_pos++;
			// product position 3 stellig
		/*	if ($products_pos<10) 
				$products_position = '00' . $products_stlist_pos;
			else if ($products_pos<100) 
				$products_position = '0' . $products_stlist_pos;
			else */
				$products_position = '' . $products_stlist_pos;

			// Preis von brutto auf netto umrechnen - Recalculate price from gross to net
			// if ($products['allow_tax']==1) $products['final_price']=$products['final_price']/(1+$products['products_tax']*0.01);
			// Preiskalkulation Brutto/Netto Shop
			if (BRUTTO_SHOP) {
				// Bruttopreise im Shop
				$pro_price=$products['products_price']*(1+$products['products_tax']*0.01);
				$tax_amount=$pro_price-($products['products_price']/(1+$products['products_tax']*0.01));
				$products_price=$pro_price;
				$products_price_sum=$products_price*$products['products_quantity'];
				$products_price_net=($pro_price/(1+$products['products_tax']*0.01));
				$products_price_net_sum=($products_price_net*$products['products_quantity']);
			} else {
				// Nettopreise im Shop
				$tax_amount=$products['products_price']-($products['products_price']/(1+$products['products_tax']*0.01));
				$products_price=$products['products_price'];
				$products_price_sum=$products['final_price'];
				$products_price_net=($products['products_price']/(1+$products['products_tax']*0.01)) ;
				$products_price_net_sum=($products['final_price']/(1+$products['products_tax']*0.01)) ;
				/*
				$tax_amount=$products['products_price']*(1+$products['products_tax']*0.01)-($products['products_price']);
				$products_price=$products['products_price']*(1+$products['products_tax']*0.01);
				$products_price_sum=$products['final_price']*(1+$products['products_tax']*0.01);
				$products_price_net=($products['products_price']) ;
				$products_price_net_sum=($products['final_price']);
				*/
			} // end if brutto netto
		    
				// Bei einem Variantenartikel die Artikelnummer ermitteln sind nicht fuer stlist artikel relevant
		/*	if (SHOPSYSTEM != 'veyton' && SHOPSYSTEM != 'presta') {
	           $art_variante = dmc_order_var_artnr($orders['orders_id'],$products['orders_products_id'], $products['products_model']  );
			} else {
				// Veyton
				$art_variante = "";
			}

			if ($art_variante!="")
				$products_model=$art_variante;
			else 
				$products_model= html2ascii($products['products_model']) ;
			*/
			$products_model= html2ascii($products['products_model']) ;
		
			$products_position_text = $products_pos.".".$products_stlist_pos;

			// generate XML product schema
			include('dmc_xml_order_opentrans_single_product.inc.php');
			
		} // end while products // Bei einem Handelsstuecklistenartikel die zugehoerigen Artikel ermitteln
			$stueckliste="";
       
?>