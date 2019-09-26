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
		// Stuecklistenprodukte vorhanden
		$StuecklisteVorhanden=false;
		
		// Shopware Extension für Lagerhaltung scha1_warehouse
		$use_shopware_extension=false;
		
		// Attribute in Produkte einpflegen
		$use_attributes=true;
		// new 290716 - Attribute als Produkte einpflegen
		$use_attributes_as_products=false;
		$products_pos =0;
		if (SHOPSYSTEM == 'veyton')  {
			$products_query_sql = "select orders_products_id, '1' AS allow_tax, products_id, products_model, products_name, products_price, (products_price * products_quantity) AS final_price, products_quantity, products_tax, products_discount,  products_data AS Optionen FROM " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . $orders_id . "'";
	   } else if (SHOPSYSTEM == 'presta') {
			// mit Netto Preisen total_price_tax_excl 	 	unit_price_tax_excl , sonst total_price_tax_incl und unit_price_tax_incl
			// $products_query_sql = "select id_order_detail AS orders_products_id, '1' AS allow_tax, product_id AS products_id, product_reference AS products_model, product_name AS products_name, unit_price_tax_incl AS products_price, total_price_tax_incl AS final_price, product_quantity AS products_quantity, (unit_price_tax_incl-unit_price_tax_excl) AS products_tax from " . TABLE_ORDERS_PRODUCTS . " where id_order = '" . $orders_id . "'";
			// Erweiterung der Erkennung, ob Artikel aus Wawi (dann Feld location in ps_product = L), ansonsten Artikelnummer=shopartikel
			// $products_query_sql = "select o.id_order_detail AS orders_products_id, '1' AS allow_tax, o.product_id AS products_id, CASE WHEN ((SELECT reference FROM ps_product WHERE reference=o.product_reference AND reference!='' AND location='L' LIMIT 1) IS NULL) THEN 'shopartikel' ELSE o.product_reference END AS products_model, product_name AS products_name, o.unit_price_tax_incl AS products_price, o.total_price_tax_incl AS final_price, o.product_quantity AS products_quantity, (o.unit_price_tax_incl-o.unit_price_tax_excl) AS products_tax FROM " . TABLE_ORDERS_PRODUCTS . " AS o where o.id_order = '" . $orders_id . "'";
			// MIT ABFANGROUTINE PROBLEME ARTIKELNUMMER 
			// $products_query_sql = "select o.id_order_detail AS orders_products_id, '1' AS allow_tax, o.product_id AS products_id, CASE WHEN ((SELECT reference FROM ps_product WHERE reference=o.product_reference AND reference!='' LIMIT 1) IS NULL) THEN 'shopartikel' ELSE o.product_reference END AS products_model, product_name AS products_name, o.unit_price_tax_incl AS products_price, o.total_price_tax_incl AS final_price, o.product_quantity AS products_quantity, (o.unit_price_tax_incl-o.unit_price_tax_excl) AS products_tax FROM " . TABLE_ORDERS_PRODUCTS . " AS o where o.id_order = '" . $orders_id . "'";
			// Satdnard
		// ALT 	$products_query_sql = "select o.id_order_detail AS orders_products_id, '1' AS allow_tax, o.product_id AS products_id, o.product_reference AS products_model, product_name AS products_name, o.unit_price_tax_incl AS products_price, o.total_price_tax_incl AS final_price, o.product_quantity AS products_quantity, (o.unit_price_tax_incl-o.unit_price_tax_excl) AS products_tax FROM " . TABLE_ORDERS_PRODUCTS . " AS o where o.id_order = '" . $orders_id . "'";
			$products_query_sql = "select o.id_order_detail AS orders_products_id, '1' AS allow_tax, o.product_id AS products_id, o.product_reference AS products_model, product_name AS products_name, o.unit_price_tax_incl AS products_price, o.total_price_tax_incl AS final_price, o.product_quantity AS products_quantity, (o.unit_price_tax_incl-o.unit_price_tax_excl) AS products_tax_amount, (o.id_tax_rules_group) AS products_tax_id, o.tax_rate AS tax_rate FROM " . TABLE_ORDERS_PRODUCTS . " AS o where o.id_order = '" . $orders_id . "'";
		} else if (SHOPSYSTEM == 'virtuemart') {		 	  	 	 	 	 	 	 	 	
			$products_query_sql = "SELECT virtuemart_order_item_id AS orders_products_id, '0' AS allow_tax, virtuemart_product_id AS products_id, order_item_sku AS products_model, order_item_name AS products_name, product_final_price AS products_price, (product_final_price * product_quantity) AS final_price, product_quantity AS products_quantity, product_tax AS products_tax, product_attribute AS product_attribute FROM " . TABLE_ORDERS_PRODUCTS . " WHERE virtuemart_order_id = '" . $orders_id . "'";
		} else if (SHOPSYSTEM == 'shopware') {		 	  	 	 	 	 	 	 	 	
						
			if ($use_shopware_extension) {	
				$products_query_sql = "SELECT o.id AS orders_products_id, '0' AS allow_tax, o.articleID AS products_id, o.articleordernumber AS products_model, o.name AS products_name, o.price AS products_price, (o.price * o.quantity) AS final_price, o.quantity AS products_quantity, o.tax_rate AS products_tax_rate,  a.scha1_warehouse AS products_warehouse, '' AS product_attribute FROM s_order_details AS o INNER JOIN s_articles_details AS ad ON o.articleordernumber=ad.ordernumber INNER JOIN s_articles_attributes AS a ON ad.ID=a.articledetailsID WHERE o.orderID = '" . $orders_id . "'";	// bzw  ordernumber = '" . $orders_id . "'";			
			} else {
				$products_query_sql = "SELECT id AS orders_products_id, '0' AS allow_tax, articleID AS products_id, articleordernumber AS products_model, name AS products_name, price AS products_price, (price * quantity) AS final_price, quantity AS products_quantity, tax_rate AS products_tax_rate,  '' AS products_warehouse, ".
				// "CONCAT(od_attr1, od_attr2, od_attr3, od_attr4, od_attr5, od_attr6) AS product_attribute ".
				"'' AS product_attribute ".
				"FROM " . DB_TABLE_PREFIX . "s_order_details WHERE orderID = '" . $orders_id . "'";	// bzw  ordernumber = '" . $orders_id . "'";		
				
				
			}
		} else if (SHOPSYSTEM == 'woocommerce') {	
			$products_query_sql = "SELECT oi.order_item_id AS orders_products_id, '0' AS allow_tax, (SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_product_id' AND order_item_id=oi.order_item_id) AS products_id, ".
			"CASE WHEN (SELECT meta_value FROM `".DB_TABLE_PREFIX."postmeta` where meta_key='_sku' AND post_id=(SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE meta_key='_product_id' AND order_item_id=oi.order_item_id)) = '' THEN (SELECT meta_value FROM `".DB_TABLE_PREFIX."postmeta` where meta_key='_sku' AND post_id=(SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_variation_id' AND order_item_id=oi.order_item_id)) ELSE (SELECT meta_value FROM `".DB_TABLE_PREFIX."postmeta` where meta_key='_sku' AND post_id=(SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE meta_key='_product_id' AND order_item_id=oi.order_item_id)) END AS products_model, oi.order_item_name AS products_name, ".
			" ((SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_line_total' AND order_item_id=oi.order_item_id)/(SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_qty' AND order_item_id=oi.order_item_id)) AS products_price, (SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_line_total' AND order_item_id=oi.order_item_id) AS final_price, (SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_qty' AND order_item_id=oi.order_item_id) AS products_quantity, ".
			"CASE WHEN (SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_line_total' AND order_item_id=oi.order_item_id)=0 THEN 0 ELSE ((SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_line_subtotal_tax' AND order_item_id=oi.order_item_id)/(SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_qty' AND order_item_id=oi.order_item_id)) END AS products_tax,".
			// Wibben
			" (SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_line_tax' AND order_item_id=oi.order_item_id) AS products_tax_amount, ".
			" (SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_tax_class' AND order_item_id=oi.order_item_id) AS products_tax_id, ".
			//" (SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='tax_amount' AND order_item_id=oi.order_item_id) AS products_tax, ".
			//" (SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='shipping_tax_amount' AND order_item_id=oi.order_item_id) AS shipping_tax_amount, ".
			// Wibben ENDE
			"(SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_variation_id' AND order_item_id=oi.order_item_id) AS product_attribute FROM ".DB_TABLE_PREFIX."woocommerce_order_items AS oi WHERE oi.order_item_type='line_item' AND oi.order_id = '" . $orders_id . "'";
			
		/*
			$products_query_sql = "SELECT oi.order_item_id AS orders_products_id, '0' AS allow_tax, 
			(SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE meta_key='_product_id' AND order_item_id=oi.order_item_id) AS products_id, 
			(SELECT meta_value FROM `wp_postmeta` where meta_key='_sku' AND post_id=(SELECT meta_value FROM wp_woocommerce_order_itemmeta WHERE  meta_key='_product_id' AND order_item_id=oi.order_item_id)) (SELECT meta_value FROM `wp_postmeta` where meta_key='_sku' AND post_id=(SELECT meta_value FROM wp_woocommerce_order_itemmeta WHERE  meta_key='_product_id' AND order_item_id=oi.order_item_id)) AS products_model, 
			oi.order_item_name AS products_name, 
			((SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_line_total' AND order_item_id=oi.order_item_id)/(SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE meta_key='_qty' AND order_item_id=oi.order_item_id)) AS products_price, 
			(SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_line_total' AND order_item_id=oi.order_item_id) AS final_price, 
			(SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_qty' AND order_item_id=oi.order_item_id) AS products_quantity, 
			CASE WHEN (SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_line_total' AND order_item_id=oi.order_item_id)=0 THEN 0 ELSE ((SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_line_subtotal_tax' AND order_item_id=oi.order_item_id)/(SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_qty' AND order_item_id=oi.order_item_id)) END AS products_tax, 
			(SELECT meta_value FROM ".DB_TABLE_PREFIX."woocommerce_order_itemmeta WHERE  meta_key='_variation_id' AND order_item_id=oi.order_item_id) AS product_attribute 
			FROM ".DB_TABLE_PREFIX."woocommerce_order_items AS oi WHERE oi.order_item_type='line_item' AND oi.order_id = '" . $orders_id . "'"; */
		} else {
			$products_query_sql = "select orders_products_id, '1' AS  allow_tax, products_id, products_model, products_name, products_price, final_price, products_tax, products_quantity from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . $orders_id . "'";
		}
		fwrite($dateihandle, "\ndmc_xml_order_opentrans_prod... products_query=  $products_query_sql\n");
	
		$products_query = dmc_db_query($products_query_sql);
		while ($products = dmc_db_fetch_array($products_query))
        {
			// Wenn _ in Artikel, dann SETArikel -> Trennen bei _ und einzelne Artikel generieren
			$useSetArtikel=true;
			if ($useSetArtikel==true)
				$artikelnummer = explode("_", $products['products_model']);
			else 
				$artikelnummer[0] = $products['products_model'];
			for ($anzahl=0; $anzahl<sizeof($artikelnummer) ;$anzahl++ ) {
				
				$products['products_model'] = $artikelnummer[$anzahl];
				// Bei Zusatzsetartikel hat nur der erste einen Preis
				if ($anzahl>0) {
					$products['products_price']=0;
					$products['final_price']=0;
				}
					
				$products_price=$products['products_price'];
				// products infos
				$products_id=$products['products_id'];
				$products_quantity=$products['products_quantity'];
				$products_name=$products['products_name'];
				$products_tax_flag=$products['allow_tax'];
				if (SHOPSYSTEM == 'shopware') {
					$products_tax_rate = $products['products_tax_rate'];								// 19
					$products_price_net = $products['products_price'] / (1 + $products['products_tax_rate'] * 0.01);	
					$products_tax_amount = $products_tax = $products['products_price'] - $products_price_net;				// 	3,99 bei 21
					$products['products_price']=$products_price_net;
					$products['products_tax'] = $products_tax = $products_tax_rate;
					if ($use_shopware_extension) {	
						$products_warehouse = $products['products_warehouse'];		
					} else {
						$products_warehouse = "";
					}
				} else {
					//$products_tax=$products['products_tax']/$products_quantity;
					$products_warehouse = "";
				}
				$products_type='Artikel';
			
				$products_tax_id =  $products['products_tax_id'];
				// Wibben Steuern berechnen
				/*if ($products['products_tax_id']==1) { // 7%
					if (is_numeric($products['shipping_tax_amount']))
						$shipping_tax_amount_7 .= $products['shipping_tax_amount'];
				} else {
					if (is_numeric($products['shipping_tax_amount']))
						$shipping_tax_amount_19 .= $products['shipping_tax_amount'];
				}*/
				
				// Wibben Ende
			
				// Attribute in Produkte - gambio
				if ($use_attributes==true && 
					(SHOPSYSTEM != 'shopware' && SHOPSYSTEM != 'veyton' && SHOPSYSTEM != 'virtuemart'  && SHOPSYSTEM != 'shopware' && SHOPSYSTEM != 'presta' && SHOPSYSTEM != 'woocommerce' && SHOPSYSTEM != 'osc') ) {
					$bezeichnungneu = '';
					$query = "SELECT orders_products_attributes_id,orders_id,orders_products_id,products_options,products_options_values,options_values_price, price_prefix FROM orders_products_attributes WHERE orders_products_id=".$products['orders_products_id'];
					fwrite($dateihandle, "* dmc_xml_order_opentrans_prod ... products_attributes_query = $query \n");	
					$products_attributes_query = dmc_db_query($query);
					while ($products_attributes = dmc_db_fetch_array($products_attributes_query ))
					{
						fwrite($dateihandle, "* Option=".$products_attributes['products_options_values']." \n");	 
						// Wenn Optionen vorhanden, dann Artikelnummer um die orders_products_id erganzen, da der Artikel nicht eindeutig ist
						$products['products_model'] = $products['products_model'].'-'.$products_attributes['products_options_values'];
						// $products['products_model'] = $products['products_model'].'-'.substr($products_name,1,15).'-'.$products['orders_products_id'];
						// Bezeichnung und Ausprägung auf jeweils max 12 Zeichen kürzen
						$bezeichnungneu .= $products_name." ".substr($products_attributes['products_options'],0,2).':'.substr($products_attributes['products_options_values'],0,8).'_';					
					}
					// Wenn Optionen vorhhanden waren, Produkt Name austauschen
					if ($products_name!=$bezeichnungneu) {
						$products_name=substr($bezeichnungneu,0,-1);
					}
				}
				
				if (SHOPSYSTEM == 'veyton') {
					$products_options=$products['Optionen'];
					// Wenn Optionen vorhanden, dann Artikelnummer um die orders_products_id erganzen, da der Artikel nicht eindeutig ist
					if ($products_options!="" && $products_options!="null") {
						$products['products_model'] = $products['products_model'].'-'.$products['orders_products_id'];
					}
				} else {
					$products_options="";
				}
				if (SHOPSYSTEM == 'virtuemart' || SHOPSYSTEM == 'woocommerce') {	
					// virtuemart abweichemd products_tax ist summe nicht satz
					$products_tax_amount=$tax_amount=$products['products_tax'];
					if ($products['products_price']>0)
						$products_tax=$products['products_tax']=round($products_tax_amount/$products['products_price']*100);
					else
						$products_tax=0;
				}
				$products_pos++;
				// product position 3 stellig
			/*	if ($products_pos<10) 
					$products_position = '00' . $products_pos;
				else if ($products_pos<100) 
					$products_position = '0' . $products_pos;
				else */
				$products_position = '' . $products_pos;
				$products_position_text = $products_pos;

				// Preis von brutto auf netto umrechnen - Recalculate price from gross to net
				// if ($products['allow_tax']==1) $products['final_price']=$products['final_price']/(1+$products['products_tax']*0.01);
				// Preiskalkulation Brutto/Netto Shop
				$BRUTTO_SHOP=BRUTTO_SHOP;
				// $BRUTTO_SHOP=false;
			
			
				if (SHOPSYSTEM == 'presta') {
					// Bruttopreise im Shop
					$products_tax_amount = $products['products_tax_amount'];	
					$products_price=$products['products_price'];
					$products_price_sum=$products['final_price'];
					$products_price_net=$products['products_price']-$products_tax_amount;
					$products_price_net_sum=$products_price_net*$products['products_quantity']; 	// ($products['final_price']/(1+$products['products_tax']*0.01)) ;
					
					// zB Steuersatz, zB 19
					// $products_tax = $products_tax_rate = floor(($products_price/$products_price_net - 1) * 100);
					$products_tax = $products_tax_rate = $products['tax_rate'];	
					
				} else if ($BRUTTO_SHOP) {
					// Bruttopreise im Shop
					if (SHOPSYSTEM != 'virtuemart' && SHOPSYSTEM != 'woocommerce')  $products_tax = $products_tax_rate	= $products['products_tax'];												// zB 19
					// Brutto zu netto -> $products_tax_amount=$tax_amount=$products['products_price']-($products['products_price']/(1+$products['products_tax']*0.01));
					// Netto zu Brutto -> $products_tax_amount=$tax_amount=($products['products_price']*(1+$products['products_tax']*0.01))-$products['products_price'];
					if (SHOPSYSTEM != 'virtuemart' && SHOPSYSTEM != 'woocommerce')  $products_tax_amount=$tax_amount=$products['products_price']-($products['products_price']/(1+$products['products_tax']*0.01));
					$products_price=$products['products_price'];
					$products_price_sum=$products['final_price'];
					$products_price_net=$products['products_price']-$products_tax_amount;
					$products_price_net_sum=$products_price_net*$products['products_quantity']; 	// ($products['final_price']/(1+$products['products_tax']*0.01)) ;
				} else {
					// Nettopreise im Shop
					// $tax_amount=$products['products_price']-($products['products_price']/(1+$products['products_tax']*0.01));
					if (SHOPSYSTEM != 'virtuemart' && SHOPSYSTEM != 'woocommerce') $products_tax = $products_tax_rate	= $products['products_tax'];												// zB 19
					// Brutto zu netto -> $products_tax_amount=$tax_amount=$products['products_price']-($products['products_price']/(1+$products['products_tax']*0.01));
					// Netto zu Brutto -> $products_tax_amount=$tax_amount=($products['products_price']*(1+$products['products_tax']*0.01))-$products['products_price'];
					if (SHOPSYSTEM != 'virtuemart' && SHOPSYSTEM != 'woocommerce') $products_tax_amount=$tax_amount=($products['products_price']*(1+$products['products_tax']*0.01))-$products['products_price'];
					$products_price=$products['products_price']+$products_tax_amount;	// /(1+$products['products_tax']*0.01)) ;
					$products_price_sum=$products['final_price'];
					$products_price_net=$products['products_price'];
					$products_price_net_sum=$products_price_net*$products['products_quantity']; 	// ($products['final_price']/(1+$products['products_tax']*0.01)) ;
					/*
					$tax_amount=$products['products_price']*(1+$products['products_tax']*0.01)-($products['products_price']);
					$products_price=$products['products_price']*(1+$products['products_tax']*0.01);
					$products_price_sum=$products['final_price']*(1+$products['products_tax']*0.01);
					$products_price_net=($products['products_price']) ;
					$products_price_net_sum=($products['final_price']);
					*/
					
				} // end if brutto netto
				
				if (SHOPSYSTEM == 'veyton')  {
					// Achtung: der Preis ist der rabattierte Preis. D.h. wenn Rabatt, dann Preis umrechnen
					if ($products['products_discount']>0) { 
						$discount_percent=$products['products_discount'];
						$price_amount_discounted=$products_price_net;
						$price_line_amount_discounted=$price_amount_discounted*$products['products_quantity'];
						$price_amount_discounted_gros=$products_price;
						$price_line_amount_discounted_gros=$price_amount_discounted_gros*$products['products_quantity'];
						$products['products_price']=$products_price*(1+$discount_percent/100);
						//$products_price=$products_price*(1+$discount_percent/100);
						$products['final_price']=$products['products_price']*$products['products_quantity'];
						$discount_amount=$products['products_price']-$price_amount_discounted;
					} else { 
						$discount_percent=0;
						$price_amount_discounted=$products_price_net;
						$price_line_amount_discounted=$products_price_net_sum;
						$price_amount_discounted_gros=$products_price;
						$price_line_amount_discounted_gros=$products_price_sum;
						// $products['products_price']=$products['products_price'];
						$products['final_price']=$products_price*$products['products_quantity'];
						$discount_amount=0;
					}
				} else { 
						$discount_percent=0;
						$price_amount_discounted=$products_price_net;
						$price_line_amount_discounted=$products_price_net_sum;
						$price_amount_discounted_gros=$products_price;
						$price_line_amount_discounted_gros=$products_price_sum;
						// $products['products_price']=$products['products_price'];
						$products['final_price']=$products_price*$products['products_quantity'];
						$discount_amount=0;				
				}
				
				// Bei einem Variantenartikel die Artikelnummer ermitteln
				if (SHOPSYSTEM != 'veyton' && SHOPSYSTEM != 'virtuemart'  && SHOPSYSTEM != 'shopware' && SHOPSYSTEM != 'presta' && SHOPSYSTEM != 'woocommerce' && SHOPSYSTEM != 'osc') {
				   // ALT $art_variante = dmc_order_var_artnr($orders_id,$products['orders_products_id'], $products['products_model']  );
				   // Artikelnummer der Variante ermitteln
					$art_variante = dmc_order_var_artnr($orders_id,$products['orders_products_id'], $products_id, $products['products_model']);
				} else {
					// Veyton
					$art_variante = "";
				}

				if ($art_variante!="")
					$products_model=$art_variante;
				else 
					$products_model=$products['products_model'] ;
				
				$cl_lieferantennummer='';
				$stuecklistennummer='';
				$artnrtemp='';
				// Attribute inistialisieren
				$products_model_attributes[1]=""; $products_model_attributes[2]=""; $products_model_attributes[3]=""; $products_model_attributes[4]=""; $products_model_attributes[5]="";
				// Wenn Artikelnummer aus Artikelnummer und Attributen zusammengesetzt ist mit Standard Trenner | 
				// WaWi Artikelnummer sowie AttributIDs ermitteln. (max 5)
				// Bei der Classic Line sollte das "Attribute" die Lieferantennummer im Standard sein.
				$products_model_attributes = explode("|", $products_model);			
				$products_model=$products_model_attributes[0];
				// ALTERNATIV (gglfs auskommentieren): In der Tabelle dmc_cl_artikel_details steht die Lieferantennummer der Classic Line (dmc_set_details.php -> function dmc_cl_artikel_details
				// WENN Info aus TABELLE dmc_cl_artikel_details
			/*	$table='dmc_cl_artikel_details';
					// Pruefen ob Stueckliste 
				$col='stuecklistennummer';
				$where="stuecklistennummer='$products_model' ";
				$stuecklistennummer=dmc_sql_select_query($col,$table,$where);
				// Pruefen ob amazonnummer zu Artikelnummer umgewendelt werden soll.
				$col='artnr';
				$where="amazonnummer='$products_model' ";
				$artnrtemp=dmc_sql_select_query($col,$table,$where);
				if ($artnrtemp!='') 
					$products_model=$artnrtemp;
				// Lieferantennummer
				$col='lieferantennummer';
				$where="artnr='$products_model' ";
				$cl_lieferantennummer=dmc_sql_select_query($col,$table,$where);
					
				// for($Anzahl = 1; $Anzahl < count($products_model_attributes); $Anzahl++)     // Artikelattribute durchlaufen	     
				*/
				// Artikelnummer Sage Classic Line -> Aufbau Schlüssel A + 20Stellinge Artikelnummer + Lieferanten 0000000000
				// oder bei Stueckliste S+Artikelnummer
				if ($stuecklistennummer!='') {
					$cl_art_nr = "S".$products_model;
				} else {
					$cl_art_nr = $products_model;
					//for ($ii=strlen($products_model);$ii<20;$ii++)
					//	$cl_art_nr .= ' '; // Artikelnummer Classic Line auffuellen
					$cl_art_nr =  str_pad ( $cl_art_nr, 20, ' ', STR_PAD_RIGHT );
					$cl_art_nr = "A".$cl_art_nr;
					
					if ($cl_lieferantennummer!='') {
						$cl_lieferantennummer =  str_pad ( $cl_lieferantennummer, 20, ' ', STR_PAD_RIGHT );
						$cl_art_nr .= $cl_lieferantennummer;
					} else if ($products_model_attributes[1]!="")
						$cl_art_nr .= str_pad ( $products_model_attributes[1], 20, ' ', STR_PAD_RIGHT ); //'00000000000'; // Lieferantennummer Classic Line  auffuellen	
					else 
						$cl_art_nr .= '0000000000          '; // Lieferantennummer Classic Line  auffuellen	
				}
						
				// generate XML product schema
				include('dmc_xml_order_opentrans_single_product.inc.php');
			} // end for
			// Attribute als Produkte - gambio
			if ($use_attributes_as_products==true && 
				(SHOPSYSTEM != 'shopware' && SHOPSYSTEM != 'veyton' && SHOPSYSTEM != 'virtuemart'  && SHOPSYSTEM != 'shopware' && SHOPSYSTEM != 'presta' && SHOPSYSTEM != 'woocommerce' && SHOPSYSTEM != 'osc') ) {
				$bezeichnungneu = '';
				// opa.orders_products_attributes_id, opa.orders_id, opa.orders_products_id, opa.products_options, opa.products_options_values, opa.price_prefix, opa.options_values_price, opa.price_prefix,  opa.options_id, opa.options_values_id, (SELECT attributes_model FROM products_attributes WHERE  options_id=opa.options_id AND options_values_id=opa.options_values_id AND products_id=136 ORDER BY products_id DESC limit 1) AS Attribut_Art_Nr
				$query = "SELECT opa.orders_products_attributes_id, opa.orders_id, opa.orders_products_id, opa.products_options, opa.products_options_values, opa.price_prefix, opa.options_values_price, opa.price_prefix,  opa.options_id, opa.options_values_id, ".
				" (SELECT attributes_model FROM products_attributes WHERE  options_id=opa.options_id AND options_values_id=opa.options_values_id AND products_id=".$products['products_id']." ORDER BY products_id DESC limit 1) AS Attribut_Art_Nr ".
				" FROM orders_products_attributes AS opa WHERE opa.orders_products_id=".$products['orders_products_id'];
				fwrite($dateihandle, "\ndmc_xml_order_opentrans_prod... products_attributes_query = $query \n");	
				$products_attributes_query = dmc_db_query($query);
				while ($products_attributes = dmc_db_fetch_array($products_attributes_query ))
				{
					$products_name=$products_attributes['products_options'].': '.$products_attributes['products_options_values'];
					if ($products_attributes['Attribut_Art_Nr']=="") 
						$products_attributes['Attribut_Art_Nr'] = $products_name;
					fwrite($dateihandle, "Attributs-Artikel(Stuecklistenartikel) ergaenzen = ".$products_attributes['Attribut_Art_Nr']." \n");	
					// Wenn Optionen vorhanden, dann diese als Produkt ergänzen
					$products_model=$products['products_model']=$cl_art_nr=$products_attributes['Attribut_Art_Nr'];
					$products_model_attributes[1]=$products_attributes['products_options'];
					$products_model_attributes[2]=$products_model_attributes[3]=$products_model_attributes[4]= $products_model_attributes[5]="";
						$products_name2 = "";
					$optionen=$products_attributes['products_options_values'];
					$products_type="Attribute";
					$products_quantity=1; 	
					// Preissberechnung
					$products_tax=$products_tax_id=19;
					$attribute_steuersatz=$products_tax/100+1;
					if ( $products_attributes['price_prefix']=='-')
						$products_attributes['options_values_price']=$products_attributes['options_values_price']*-1;
					$products_price_net_sum=$products_price_net=$products_attributes['options_values_price'];
					$products_price_sum=$products_price=$products_price_net*1.19;
					$products_tax_amount=$products_price-$products_price_net;
					$discount_amount=$price_amount_discounted=$price_line_amount_discounted=0;
		
					$products_pos++;
					// product position 3 stellig
					/*	if ($products_pos<10) 
						$products_position = '00' . $products_pos;
						else if ($products_pos<100) 
						$products_position = '0' . $products_pos;
					else */
					$products_position = '' . $products_pos;
					$products_position_text = $products_pos;
					include('dmc_xml_order_opentrans_single_product.inc.php');
				}
				
			}
			// Bei einem Handelsstuecklistenartikel die zugehoerigen Artikel ermitteln
			//include('dmc_xml_order_opentrans_stlist_products.inc.php');
		} // end while products
       
	   // Gutschein als Produkt
		if (EXPORT_GV_AS_PRODUCT && SHOPSYSTEM != 'presta'  && SHOPSYSTEM != 'virtuemart'  && SHOPSYSTEM != 'shopware' && SHOPSYSTEM != 'woocommerce') {
		   // Gutschein ermitteln
			if (SHOPSYSTEM == 'veyton') {
				$versand_sql = "SELECT * FROM ".TABLE_ORDERS_TOTAL.
					" WHERE (orders_total_key=\"ot_gv\") ".
					"AND orders_id = " . $orders_id;
			} else {
				$versand_sql = 
					"SELECT * FROM ".TABLE_ORDERS_TOTAL.
					" WHERE (class=\"ot_gv\" OR class=\"ot_coupon\" OR class=\"ot_discount\") ".
					"AND orders_id = " . $orders_id;
					fwrite($dateihandle, "\n293 dmc_xml_order_opentrans_prod... p$versand_sql \n");
		
			}
		
			$versand_query = dmc_db_query($versand_sql);
	        if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
					$rcm_versandart=$versanddata['title'];
					
					if (BRUTTO_SHOP) {
						// Bruttopreise im Shop
						$rcm_versandkosten = $versanddata['value']; //*TAX_DISCOUNT;	 
						$rcm_versandkosten_net = $rcm_versandkosten;
					} else {
						$rcm_versandkosten = $versanddata['value'];	 
						$rcm_versandkosten_net = $rcm_versandkosten; // /TAX_DISCOUNT;
						
					}
				
					// products infos
					$products_id='30000';
					$products_quantity=1;
					$products_model=EXPORT_GV_AS_PRODUCT_SKU;
					$products_name="Rabatt-Coupon";
					$products_tax_flag=1;
					$products_type='Zu-/Abschlag (Artikel)';
					$products_price=$rcm_versandkosten*1;
					$products_price_sum=$rcm_versandkosten*1;
					$products_price_net=$rcm_versandkosten_net*1;
					$products_price_net_sum=$rcm_versandkosten_net*1;
					$products_tax=0;
					$products_tax_amount=0;
										 
					$products_pos++;
					// product position 3 stellig
				/*	if ($products_pos<10) 
						$products_position = '00' . $products_pos;
					else if ($products_pos<100) 
						$products_position = '0' . $products_pos;
					else */
						$products_position = '' . $products_pos;

					$products_position_text = $products_pos;
					// generate XML product schema
					include('dmc_xml_order_opentrans_single_product.inc.php');
								
			} // end if (($versand_query) - gutschein 
		} // end  if (EXPORT_GV_AS_PRODUCT) 
		//$orders_id=1;
		// Bonus als Produkt
		if (EXPORT_BONUS_AS_PRODUCT && SHOPSYSTEM != 'presta' && SHOPSYSTEM != 'virtuemart'  && SHOPSYSTEM != 'shopware' && SHOPSYSTEM != 'woocommerce') {
		   // Gutschein ermitteln
		    if (SHOPSYSTEM == 'veyton') {
				$versand_sql = "SELECT * FROM ".TABLE_ORDERS_TOTAL.
					" WHERE (orders_total_key=\"ot_coupon\"  OR orders_total_key=\"ot_discount\") ".
					"AND orders_id = " . $orders_id;
			} else {
				$versand_sql = 
					"SELECT * FROM ".TABLE_ORDERS_TOTAL.
					" WHERE (class=\"ot_bonus_fee\") ".
					"AND orders_id = " . $orders_id;
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
					$products_tax_flag=$products['allow_tax'];
					$products_type='Zu-/Abschlag (Artikel)';
					$products_price=$rcm_versandkosten*-1;
					$products_price_sum=$rcm_versandkosten*-1;
					$products_price_net=$rcm_versandkosten_net*-1;
					$products_price_net_sum=$rcm_versandkosten_net*-1;
					$products_tax='19';
					$products_amount=$products_price_sum*($products_tax/100);
					
					$products_pos++;
					// product position 3 stellig
				/*	if ($products_pos<10) 
						$products_position = '00' . $products_pos;
					else if ($products_pos<100) 
						$products_position = '0' . $products_pos;
					else  */
						$products_position = '' . $products_pos;

					$products_position_text = $products_pos;

					// generate XML product schema
					include('dmc_xml_order_opentrans_single_product.inc.php');
								
			} // end if (($versand_query) - gutschein 
		} // end  if (EXPORT_BONUS_AS_PRODUCT) 
		
		if (EXPORT_BONUS_AS_PRODUCT && SHOPSYSTEM != 'presta' && SHOPSYSTEM != 'virtuemart'  && SHOPSYSTEM != 'shopware' && SHOPSYSTEM != 'woocommerce') {
		// Bonus class=\"ot_discount\"
				if (SHOPSYSTEM == 'veyton') {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (orders_total_key=\"ot_gv\") and orders_id = " . $orders_id;
				} else {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (class=\"ot_bonus_fee\") and orders_id = " . $orders_id;
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
					$schema .= 	"<ORDER_ITEM>". "\n";
					  
					    $products_pos++;
					  if ($products_pos<10) $schema .= '<LINE_ITEM_ID>00' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					  else if ($products_pos<100) $schema .= '<LINE_ITEM_ID>0' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					  else $schema .= '<LINE_ITEM_ID>' . $products_pos . '</LINE_ITEM_ID>' . "\n";

					// Artikelnummer Sage Classic Line -> Aufbau Schlüssel A + 20Stellinge Artikelnummer + Lieferanten 0000000000
					$cl_art_nr = "A".EXPORT_BONUS_AS_PRODUCT_SKU;
					for ($ii=strlen(EXPORT_BONUS_AS_PRODUCT_SKU);$ii<20;$ii++)
						$cl_art_nr .= ' '; // Artikelnummer Classic Line auffuellen
													
					$cl_art_nr .= '0000000000          '; // Lieferantennummer Classic Line  auffuellen	
					$cl_art_nr =  str_pad ( $cl_art_nr, 20, ' ', STR_PAD_RIGHT );
  
					$schema .=		'<PRODUCTS_ORDER_ID>30001</PRODUCTS_ORDER_ID>'  . "\n" .
									"<ARTICLE_ID>". "\n" .
										'<SUPPLIER_AID>'.EXPORT_BONUS_AS_PRODUCT_SKU.'</SUPPLIER_AID>'  . "\n".	// Artikelnummer Gutschein
										"<SUPPLIER_AID_CLASSIC_LINE>".$cl_art_nr."</SUPPLIER_AID_CLASSIC_LINE>". "\n".
										"<PRODUCTS_ATTRIBUTE_ID></PRODUCTS_ATTRIBUTE_ID>". "\n".
										"<OPTIONS></OPTIONS>". "\n".
										'<ARTIKEL_ART>Zu-/Abschlag (Artikel)</ARTIKEL_ART>' . "\n".
										'<ARTICLE_TYPE>N</ARTICLE_TYPE>' . "\n".
										'<DESCRIPTION_SHORT>' . umlaute_order_export(($rcm_versandart)) . '</DESCRIPTION_SHORT>'  . "\n" .
										"<DESCRIPTION_LONG/>". "\n".
									"</ARTICLE_ID>". "\n" .
									"<QUANTITY>-1</QUANTITY>". "\n".
									"<ORDER_UNIT>-1</ORDER_UNIT>". "\n".
									"<ARTICLE_PRICE_NET>". "\n" .
										"<PRICE_AMOUNT>". $rcm_versandkosten_net . "</PRICE_AMOUNT>". "\n" .
										"<PRICE_LINE_AMOUNT>". $rcm_versandkosten_net . "</PRICE_LINE_AMOUNT>". "\n" .
										"<PRICE_FLAG/>". "\n" .
										"<TAX>19.0000</TAX>". "\n" .
										"<TAX_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_AMOUNT>". "\n" .
										"<TAX_LINE_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_LINE_AMOUNT>". "\n" .
										"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
										"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
										"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
										"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
									"</ARTICLE_PRICE_NET>". "\n" .
									"<ARTICLE_PRICE_GROS>". "\n" .
										"<PRICE_AMOUNT>". $rcm_versandkosten . "</PRICE_AMOUNT>". "\n" .
										"<PRICE_LINE_AMOUNT>". $rcm_versandkosten . "</PRICE_LINE_AMOUNT>". "\n" .
										"<PRICE_FLAG/>". "\n" .
										"<TAX>19.0000</TAX>". "\n" .
										"<TAX_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_AMOUNT>". "\n" .
										"<TAX_LINE_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_LINE_AMOUNT>". "\n" .
										"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
										"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
										"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
										"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
									"</ARTICLE_PRICE_GROS>". "\n" .
									"<ARTICLE_WEIGHT>0</ARTICLE_WEIGHT>". "\n".
									'<ORDER_CURRENCY_CODE>' .$currency_code.'</ORDER_CURRENCY_CODE>' . "\n".		
									'<ORDER_CURRENCY_RATE>' .$currency_value.'</ORDER_CURRENCY_RATE>' . "\n".		
									'<ORDER_CURRENCY_RATE_INVERS>' .(1/$currency_value).'</ORDER_CURRENCY_RATE_INVERS>' . "\n".		
								
								"</ORDER_ITEM>". "\n";
				} // endif Bonus		  
		} //endif EXPORT_BONUS_AS_PRODUCT
			
		if(EXPORT_DISCOUNT_PAYMENT_AS_PRODUCT && SHOPSYSTEM != 'presta' && SHOPSYSTEM != 'virtuemart'  && SHOPSYSTEM != 'shopware' && SHOPSYSTEM != 'woocommerce') {
				// Discount  ot_payment
				if (SHOPSYSTEM == 'veyton') {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (orders_total_key=\"ot_discount\") and orders_id = " . $orders_id;
				} else {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (class=\"ot_discount\") and orders_id = " . $orders_id;
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
					$schema .= 	"<ORDER_ITEM>". "\n";
					  
					  $products_pos++;
					  if ($products_pos<10) $schema .= '<LINE_ITEM_ID>00' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					  else if ($products_pos<100) $schema .= '<LINE_ITEM_ID>0' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					  else $schema .= '<LINE_ITEM_ID>' . $products_pos . '</LINE_ITEM_ID>' . "\n";

					  // Artikelnummer Sage Classic Line -> Aufbau Schlüssel A + 20Stellinge Artikelnummer + Lieferanten 0000000000
					$cl_art_nr = "A".EXPORT_DISCOUNT_AS_PRODUCT_SKU;
					for ($ii=strlen(EXPORT_DISCOUNT_AS_PRODUCT_SKU);$ii<20;$ii++)
						$cl_art_nr .= ' '; // Artikelnummer Classic Line auffuellen
													
					$cl_art_nr .= '0000000000          '; // Lieferantennummer Classic Line  auffuellen	
					$cl_art_nr =  str_pad ( $cl_art_nr, 20, ' ', STR_PAD_RIGHT );

					$schema .=		'<PRODUCTS_ORDER_ID>30002</PRODUCTS_ORDER_ID>'  . "\n" .
									"<ARTICLE_ID>". "\n" .
										'<SUPPLIER_AID>'.EXPORT_DISCOUNT_AS_PRODUCT_SKU.'</SUPPLIER_AID>'  . "\n".	// Artikelnummer Gutschein
										"<SUPPLIER_AID_CLASSIC_LINE>".$cl_art_nr."</SUPPLIER_AID_CLASSIC_LINE>". "\n".
										"<PRODUCTS_ATTRIBUTE_ID></PRODUCTS_ATTRIBUTE_ID>". "\n".
										"<OPTIONS></OPTIONS>". "\n".
										'<ARTIKEL_ART>Zu-/Abschlag (Artikel)</ARTIKEL_ART>' . "\n".
										'<ARTICLE_TYPE>N</ARTICLE_TYPE>' . "\n".
										'<DESCRIPTION_SHORT>' . umlaute_order_export(($rcm_versandart)) . '</DESCRIPTION_SHORT>'  . "\n" .
										"<DESCRIPTION_LONG/>". "\n".
									"</ARTICLE_ID>". "\n" .
									"<QUANTITY>-1</QUANTITY>". "\n".
									"<ORDER_UNIT>-1</ORDER_UNIT>". "\n".
									"<ARTICLE_PRICE_NET>". "\n" .
										"<PRICE_AMOUNT>". $rcm_versandkosten_net . "</PRICE_AMOUNT>". "\n" .
										"<PRICE_LINE_AMOUNT>". $rcm_versandkosten_net . "</PRICE_LINE_AMOUNT>". "\n" .
										"<PRICE_FLAG/>". "\n" .
										"<TAX>19.0000</TAX>". "\n" .
										"<TAX_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_AMOUNT>". "\n" .
										"<TAX_LINE_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_LINE_AMOUNT>". "\n" .
										"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
										"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
										"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
										"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
									"</ARTICLE_PRICE_NET>". "\n" .
									"<ARTICLE_PRICE_GROS>". "\n" .
										"<PRICE_AMOUNT>". $rcm_versandkosten . "</PRICE_AMOUNT>". "\n" .
										"<PRICE_LINE_AMOUNT>". $rcm_versandkosten . "</PRICE_LINE_AMOUNT>". "\n" .
										"<PRICE_FLAG/>". "\n" .
										"<TAX>19.0000</TAX>". "\n" .
										"<TAX_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_AMOUNT>". "\n" .
										"<TAX_LINE_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_LINE_AMOUNT>". "\n" .
										"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
										"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
										"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
										"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
									"</ARTICLE_PRICE_GROS>". "\n" .
									"<ARTICLE_WEIGHT>0</ARTICLE_WEIGHT>". "\n".
									'<ORDER_CURRENCY_CODE>' .$currency_code.'</ORDER_CURRENCY_CODE>' . "\n".		
									'<ORDER_CURRENCY_RATE>' .$currency_value.'</ORDER_CURRENCY_RATE>' . "\n".		
									'<ORDER_CURRENCY_RATE_INVERS>' .(1/$currency_value).'</ORDER_CURRENCY_RATE_INVERS>' . "\n".	
								"</ORDER_ITEM>". "\n";
				} // endif Discount	
		}//endif EXPORT_DISCOUNT_PAYMENT_AS_PRODUCT
			
		if(EXPORT_PREPAYMENT_AS_PRODUCT && SHOPSYSTEM != 'presta' && SHOPSYSTEM != 'virtuemart'  && SHOPSYSTEM != 'shopware' && SHOPSYSTEM != 'woocommerce') {
				// Vorkasse Rabatt  ot_payment
				if (SHOPSYSTEM == 'veyton') {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (orders_total_key=\"payment\") and orders_id = " . $orders_id;
				} else {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (class=\"ot_payment\") and orders_id = " . $orders_id;
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
					$schema .= 	"<ORDER_ITEM>". "\n";
					  
					  $products_pos++;
					  if ($products_pos<10) $schema .= '<LINE_ITEM_ID>00' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					  else if ($products_pos<100) $schema .= '<LINE_ITEM_ID>0' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					  else $schema .= '<LINE_ITEM_ID>' . $products_pos . '</LINE_ITEM_ID>' . "\n";
	
					// Artikelnummer Sage Classic Line -> Aufbau Schlüssel A + 20Stellinge Artikelnummer + Lieferanten 0000000000
					$cl_art_nr = "A".EXPORT_PREPAYMENT_AS_PRODUCT_SKU;
					for ($ii=strlen(EXPORT_DISCOUNT_AS_PRODUCT_SKU);$ii<20;$ii++)
						$cl_art_nr .= ' '; // Artikelnummer Classic Line auffuellen
													
					$cl_art_nr .= '0000000000          '; // Lieferantennummer Classic Line  auffuellen	
					$cl_art_nr =  str_pad ( $cl_art_nr, 20, ' ', STR_PAD_RIGHT );

	
					$schema .=		'<PRODUCTS_ORDER_ID>30003</PRODUCTS_ORDER_ID>'  . "\n" .
									"<ARTICLE_ID>". "\n" .
										'<SUPPLIER_AID>'.EXPORT_PREPAYMENT_AS_PRODUCT_SKU.'</SUPPLIER_AID>'  . "\n".	// Artikelnummer Gutschein
										"<SUPPLIER_AID_CLASSIC_LINE>".$cl_art_nr."</SUPPLIER_AID_CLASSIC_LINE>". "\n".
										"<PRODUCTS_ATTRIBUTE_ID></PRODUCTS_ATTRIBUTE_ID>". "\n".
										"<OPTIONS></OPTIONS>". "\n".
										'<ARTIKEL_ART>Zu-/Abschlag (Artikel)</ARTIKEL_ART>' . "\n".
										'<ARTICLE_TYPE>N</ARTICLE_TYPE>' . "\n".
										'<DESCRIPTION_SHORT>' . umlaute_order_export(($rcm_versandart)) . '</DESCRIPTION_SHORT>'  . "\n" .
										"<DESCRIPTION_LONG/>". "\n".
									"</ARTICLE_ID>". "\n" .
									"<QUANTITY>-1</QUANTITY>". "\n".
									"<ORDER_UNIT>-1</ORDER_UNIT>". "\n".
									"<ARTICLE_PRICE_NET>". "\n" .
										"<PRICE_AMOUNT>". $rcm_versandkosten_net . "</PRICE_AMOUNT>". "\n" .
										"<PRICE_LINE_AMOUNT>". $rcm_versandkosten_net . "</PRICE_LINE_AMOUNT>". "\n" .
										"<PRICE_FLAG/>". "\n" .
										"<TAX>19.0000</TAX>". "\n" .
										"<TAX_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_AMOUNT>". "\n" .
										"<TAX_LINE_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_LINE_AMOUNT>". "\n" .
										"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
										"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
										"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
										"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
									"</ARTICLE_PRICE_NET>". "\n" .
									"<ARTICLE_PRICE_GROS>". "\n" .
										"<PRICE_AMOUNT>". $rcm_versandkosten . "</PRICE_AMOUNT>". "\n" .
										"<PRICE_LINE_AMOUNT>". $rcm_versandkosten . "</PRICE_LINE_AMOUNT>". "\n" .
										"<PRICE_FLAG/>". "\n" .
										"<TAX>19.0000</TAX>". "\n" .
										"<TAX_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_AMOUNT>". "\n" .
										"<TAX_LINE_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_LINE_AMOUNT>". "\n" .
										"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
										"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
										"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
										"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
									"</ARTICLE_PRICE_GROS>". "\n" .
									"<ARTICLE_WEIGHT>0</ARTICLE_WEIGHT>". "\n".
								"</ORDER_ITEM>". "\n";
								
				} // endif Bonus	
		}//endif EXPORT_PREPAYMENT_AS_PRODUCT
			
		if(EXPORT_PAYPAL_AS_PRODUCT && SHOPSYSTEM != 'presta' && SHOPSYSTEM != 'virtuemart'  && SHOPSYSTEM != 'shopware' && SHOPSYSTEM != 'woocommerce') {
		    // Paypal Gebühren ermitteln
				if (SHOPSYSTEM == 'veyton') {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (orders_total_key=\"payment\") and orders_id = " . $orders_id;
				} else {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (class=\"ot_paypal_fee\") and orders_id = " . $orders_id;
				}
		      $versand_query = dmc_db_query($versand_sql);
	            if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
					$rcm_versandart=$versanddata['title'];
					
					if (BRUTTO_SHOP) {
						// Bruttopreise im Shop
						$rcm_versandkosten_tax_rate=TAX_SURCHARGE;
						$rcm_versandkosten = $versanddata['value'];	 
						$rcm_versandkosten_net = $rcm_versandkosten/((100+$rcm_versandkosten_tax_rate)/100);
						
					  } else {
						$rcm_versandkosten_tax_rate=TAX_SURCHARGE;
						$rcm_versandkosten = $versanddata['value']*((100+$rcm_versandkosten_tax_rate)/100);	 
						$rcm_versandkosten_net = $rcm_versandkosten;
					  }
					 
					 if (SHOPSYSTEM == 'veyton') {
						$rcm_versandart='Zuschlag '.$versanddata['orders_total_name'];
						$rcm_versandkosten_tax_rate	= $versanddata['orders_total_tax'];					
						$rcm_versandkosten = $versanddata['orders_total_price']*((100+$rcm_versandkosten_tax_rate)/100);	
						$rcm_versandkosten_net = $versanddata['orders_total_price'];
					 }
					   	 	
					   
					   
					// Paypal Gebühren - als Artikel anfügen
					$schema .= 	"<ORDER_ITEM>". "\n";
					 
					  $products_pos++;
					  if ($products_pos<10) $schema .= '<LINE_ITEM_ID>00' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					  else if ($products_pos<100) $schema .= '<LINE_ITEM_ID>0' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					  else $schema .= '<LINE_ITEM_ID>' . $products_pos . '</LINE_ITEM_ID>' . "\n";

					// Artikelnummer Sage Classic Line -> Aufbau Schlüssel A + 20Stellinge Artikelnummer + Lieferanten 0000000000
					$cl_art_nr = "A".EXPORT_PAYPAL_AS_PRODUCT_SKU;
					for ($ii=strlen(EXPORT_PAYPAL_AS_PRODUCT_SKU);$ii<20;$ii++)
						$cl_art_nr .= ' '; // Artikelnummer Classic Line auffuellen
													
					$cl_art_nr .= '0000000000          '; // Lieferantennummer Classic Line  auffuellen	
					$cl_art_nr =  str_pad ( $cl_art_nr, 20, ' ', STR_PAD_RIGHT );

						$schema .=		'<PRODUCTS_ORDER_ID>40000</PRODUCTS_ORDER_ID>'  . "\n" .
									"<ARTICLE_ID>". "\n" .
										'<SUPPLIER_AID>'.EXPORT_PAYPAL_AS_PRODUCT_SKU.'</SUPPLIER_AID>'  . "\n".	// Artikelnummer Paypal
										"<SUPPLIER_AID_CLASSIC_LINE>".$cl_art_nr."</SUPPLIER_AID_CLASSIC_LINE>". "\n".
										"<PRODUCTS_ATTRIBUTE_ID></PRODUCTS_ATTRIBUTE_ID>". "\n".
										"<OPTIONS></OPTIONS>". "\n".
										'<ARTIKEL_ART>Zu-/Abschlag (Artikel)</ARTIKEL_ART>' . "\n".
										'<ARTICLE_TYPE>N</ARTICLE_TYPE>' . "\n".
										'<DESCRIPTION_SHORT>' . umlaute_order_export(($rcm_versandart)) . '</DESCRIPTION_SHORT>'  . "\n" .
										"<DESCRIPTION_LONG/>". "\n".
									"</ARTICLE_ID>". "\n" .
									"<QUANTITY>1</QUANTITY>". "\n".
									"<ORDER_UNIT>1</ORDER_UNIT>". "\n".
									"<ARTICLE_PRICE_NET>". "\n" .
										"<PRICE_AMOUNT>". $rcm_versandkosten_net . "</PRICE_AMOUNT>". "\n" .
										"<PRICE_LINE_AMOUNT>". $rcm_versandkosten_net . "</PRICE_LINE_AMOUNT>". "\n" .
										"<PRICE_FLAG/>". "\n" .
										"<TAX>". $rcm_versandkosten_tax_rate . "</TAX>". "\n" .
										"<TAX_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_AMOUNT>". "\n" .
										"<TAX_LINE_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_LINE_AMOUNT>". "\n" .
										"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
										"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
										"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
										"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
									"</ARTICLE_PRICE_NET>". "\n" .
									"<ARTICLE_PRICE_GROS>". "\n" .
										"<PRICE_AMOUNT>". $rcm_versandkosten . "</PRICE_AMOUNT>". "\n" .
										"<PRICE_LINE_AMOUNT>". $rcm_versandkosten . "</PRICE_LINE_AMOUNT>". "\n" .
										"<PRICE_FLAG/>". "\n" .
										"<TAX>". $rcm_versandkosten_tax_rate . "</TAX>". "\n" .
										"<TAX_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_AMOUNT>". "\n" .
										"<TAX_LINE_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_LINE_AMOUNT>". "\n" .
										"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
										"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
										"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
										"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
									"</ARTICLE_PRICE_GROS>". "\n" .
									"<ARTICLE_WEIGHT>0</ARTICLE_WEIGHT>". "\n".
								"</ORDER_ITEM>". "\n";
								
								
				} // endif Paypal Gebühren 
		}//endif if(EXPORT_PAYPAL_AS_PRODUCT)
		
		if (EXPORT_COD_AS_PRODUCT && SHOPSYSTEM != 'presta' && SHOPSYSTEM != 'virtuemart'  && SHOPSYSTEM != 'shopware' && SHOPSYSTEM != 'woocommerce') {
				// Nachnahme Gebühren ermitteln
				if (SHOPSYSTEM == 'veyton') {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (orders_total_key=\"ot_cod_fee\") and orders_id = " . $orders_id;
				} else {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (class=\"ot_cod_fee\") and orders_id = " . $orders_id;
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
					$schema .= 	"<ORDER_ITEM>". "\n";
					  
					$products_pos++;
					if ($products_pos<10) $schema .= '<LINE_ITEM_ID>00' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					else if ($products_pos<100) $schema .= '<LINE_ITEM_ID>0' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					else $schema .= '<LINE_ITEM_ID>' . $products_pos . '</LINE_ITEM_ID>' . "\n";

					 // Artikelnummer Sage Classic Line -> Aufbau Schlüssel A + 20Stellinge Artikelnummer + Lieferanten 0000000000
					$cl_art_nr = "A".EXPORT_COD_AS_PRODUCT_SKU;
					for ($ii=strlen(EXPORT_COD_AS_PRODUCT_SKU);$ii<20;$ii++)
						$cl_art_nr .= ' '; // Artikelnummer Classic Line auffuellen
													
					$cl_art_nr .= '0000000000          '; // Lieferantennummer Classic Line  auffuellen	
					$cl_art_nr =  str_pad ( $cl_art_nr, 20, ' ', STR_PAD_RIGHT );

					$schema .=		'<PRODUCTS_ORDER_ID>50000</PRODUCTS_ORDER_ID>'  . "\n" .
									"<ARTICLE_ID>". "\n" .
										"<SUPPLIER_AID>".EXPORT_COD_AS_PRODUCT_SKU."</SUPPLIER_AID>"  . "\n".	// Artikelnummer Nachnahme
										"<SUPPLIER_AID_CLASSIC_LINE>".$cl_art_nr."</SUPPLIER_AID_CLASSIC_LINE>". "\n".
										"<PRODUCTS_ATTRIBUTE_ID></PRODUCTS_ATTRIBUTE_ID>". "\n".
										"<OPTIONS></OPTIONS>". "\n".
										'<ARTIKEL_ART>Zu-/Abschlag (Artikel)</ARTIKEL_ART>' . "\n".
										'<ARTICLE_TYPE>N</ARTICLE_TYPE>' . "\n".
										'<DESCRIPTION_SHORT>' . umlaute_order_export(($rcm_versandart)) . '</DESCRIPTION_SHORT>'  . "\n" .
										"<DESCRIPTION_LONG/>". "\n".
									"</ARTICLE_ID>". "\n" .
									"<QUANTITY>-1</QUANTITY>". "\n".
									"<ORDER_UNIT>-1</ORDER_UNIT>". "\n".
									"<ARTICLE_PRICE_NET>". "\n" .
										"<PRICE_AMOUNT>". $rcm_versandkosten_net . "</PRICE_AMOUNT>". "\n" .
										"<PRICE_LINE_AMOUNT>". $rcm_versandkosten_net . "</PRICE_LINE_AMOUNT>". "\n" .
										"<PRICE_FLAG/>". "\n" .
										"<TAX>19.0000</TAX>". "\n" .
										"<TAX_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_AMOUNT>". "\n" .
										"<TAX_LINE_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_LINE_AMOUNT>". "\n" .
										"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
										"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
										"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
										"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
									"</ARTICLE_PRICE_NET>". "\n" .
									"<ARTICLE_PRICE_GROS>". "\n" .
										"<PRICE_AMOUNT>". $rcm_versandkosten . "</PRICE_AMOUNT>". "\n" .
										"<PRICE_LINE_AMOUNT>". $rcm_versandkosten . "</PRICE_LINE_AMOUNT>". "\n" .
										"<PRICE_FLAG/>". "\n" .
										"<TAX>19.0000</TAX>". "\n" .
										"<TAX_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_AMOUNT>". "\n" .
										"<TAX_LINE_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_LINE_AMOUNT>". "\n" .
										"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
										"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
										"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
										"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
									"</ARTICLE_PRICE_GROS>". "\n" .
									"<ARTICLE_WEIGHT>0</ARTICLE_WEIGHT>". "\n".
								"</ORDER_ITEM>". "\n";
								
				} // endif Nachnahme Gebühren 
		} // end if (EXPORT_COD_AS_PRODUCT) 
		
		fwrite($dateihandle, "\851 Versandkosten als Produkt :".EXPORT_SHIPPING_AS_PRODUCT." ($shipping_method) SHOPSYSTEM= ".SHOPSYSTEM." Versandkosten:".$shipping_amount."\n");
		// Versandkosten als Produkt woocommerce und Shopware
		if (EXPORT_SHIPPING_AS_PRODUCT && (SHOPSYSTEM == 'virtuemart' || SHOPSYSTEM == 'shopware'  || SHOPSYSTEM == 'woocommerce') && $shipping_amount > 0) {
				// VERSANDKOSTEN - als Artikel anfügen, wenn > 0
				// VERSANDKOSTEN - als Artikel anfügen, wenn > 0
				if ($rcm_versandkosten>0) {
					$schema .= 	"<ORDER_ITEM>". "\n";
					  
					$products_pos++;
					if ($products_pos<10) $schema .= '<LINE_ITEM_ID>00' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					else if ($products_pos<100) $schema .= '<LINE_ITEM_ID>0' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					else $schema .= '<LINE_ITEM_ID>' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					
					// Mapping Versandart auf VersandArtikelnummer 
					// Ewertz Paket EU,Paket innerdeutsch,Paket weltweit,Brief innerdeutsch, Maxibrief innerdeutsch,Brief weltweit,Maxibrief weltweit
					if ($shipping_method=='Paket EU') $EXPORT_SHIPPING_AS_PRODUCT_SKU='0,0008 UPS';
					else if ($shipping_method=='Paket innerdeutsch') $EXPORT_SHIPPING_AS_PRODUCT_SKU='0,0006A UPS';
					else if ($shipping_method=='Paket weltweit') $EXPORT_SHIPPING_AS_PRODUCT_SKU='0,0012 UPS';
					else if ($shipping_method=='Brief innerdeutsch') $EXPORT_SHIPPING_AS_PRODUCT_SKU='0,0004B Inl.'; //Brief innerdeutsch (€ 3,00) nur Deutschland
					else if ($shipping_method=='Maxibrief innerdeutsch') $EXPORT_SHIPPING_AS_PRODUCT_SKU='0,0004C Inl.'; // Maxibrief innerdeutsch (€ 4,00) nur Deutschland
					else if ($shipping_method=='Brief weltweit') $EXPORT_SHIPPING_AS_PRODUCT_SKU='0,0004B Ausl.'; // Brief weltweit (€ 8,00) alle außer Deutschland
					else if ($shipping_method=='Maxibrief weltweit') $EXPORT_SHIPPING_AS_PRODUCT_SKU='0,0004C Ausl.'; //Maxibrief weltweit (€ 18,00) alle außer Deutschland
					else $EXPORT_SHIPPING_AS_PRODUCT_SKU=EXPORT_SHIPPING_AS_PRODUCT_SKU;

					if ($shipping_method=='') $shipping_method="Versandkosten";
					
					// Artikelnummer Sage Classic Line -> Aufbau Schlüssel A + 20stellige Artikelnummer + Lieferanten 0000000000
					$cl_art_nr = "A".$EXPORT_SHIPPING_AS_PRODUCT_SKU;
					for ($ii=strlen($EXPORT_SHIPPING_AS_PRODUCT_SKU);$ii<20;$ii++)
						$cl_art_nr .= ' '; // Artikelnummer Classic Line auffuellen
													
					$cl_art_nr .= '0000000000          '; // Lieferantennummer Classic Line  auffuellen	
					$cl_art_nr =  str_pad ( $cl_art_nr, 20, ' ', STR_PAD_RIGHT );

					$schema .=		'<PRODUCTS_ORDER_ID>'.html2ascii($shipping_method).'</PRODUCTS_ORDER_ID>'  . "\n" .
								"<ARTICLE_ID>". "\n" .
									'<SUPPLIER_AID>'.$EXPORT_SHIPPING_AS_PRODUCT_SKU.'</SUPPLIER_AID>'  . "\n".	// Artikelnummer Versandkosten
									"<SUPPLIER_AID_CLASSIC_LINE>".$cl_art_nr."</SUPPLIER_AID_CLASSIC_LINE>". "\n".
									"<PRODUCTS_ATTRIBUTE_ID></PRODUCTS_ATTRIBUTE_ID>". "\n".
									"<OPTIONS></OPTIONS>". "\n".
									'<ARTIKEL_ART>Zu-/Abschlag (Artikel)</ARTIKEL_ART>' . "\n".
									'<ARTICLE_TYPE>N</ARTICLE_TYPE>' . "\n".
									'<DESCRIPTION_SHORT>' . umlaute_order_export(($shipping_method)) . '</DESCRIPTION_SHORT>'  . "\n" .
									"<DESCRIPTION_LONG/>". "\n".
								"</ARTICLE_ID>". "\n" .
								"<QUANTITY>1</QUANTITY>". "\n".
								"<ORDER_UNIT>1</ORDER_UNIT>". "\n".
								"<ARTICLE_PRICE_NET>". "\n" .
									"<PRICE_AMOUNT>". number_format($shipping_amount, 4, '.', '') . "</PRICE_AMOUNT>". "\n" .
									"<PRICE_LINE_AMOUNT>". number_format($shipping_amount, 4, '.', '') . "</PRICE_LINE_AMOUNT>". "\n" .
									"<PRICE_FLAG/>". "\n" .
									"<TAX>". number_format($versandkosten_steuersatz_prozent, 4, '.', '') . "</TAX>". "\n" . 
									"<TAX_AMOUNT>".number_format($shipping_tax_amount, 4, '.', '')."</TAX_AMOUNT>". "\n" .
									"<TAX_LINE_AMOUNT>".number_format($shipping_tax_amount, 4, '.', '')."</TAX_LINE_AMOUNT>". "\n" .
									"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
									"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
									"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
									"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
								"</ARTICLE_PRICE_NET>". "\n" .
								"<ARTICLE_PRICE_GROS>". "\n" .
									"<PRICE_AMOUNT>". number_format(($shipping_amount+$shipping_tax_amount), 4, '.', ''). "</PRICE_AMOUNT>". "\n" .
									"<PRICE_LINE_AMOUNT>". number_format(($shipping_amount+$shipping_tax_amount), 4, '.', ''). "</PRICE_LINE_AMOUNT>". "\n" .
									"<PRICE_FLAG/>". "\n" .
									"<TAX>". number_format($versandkosten_steuersatz_prozent, 4, '.', '') . "</TAX>". "\n" . 
									"<TAX_AMOUNT>".number_format($shipping_tax_amount, 4, '.', '')."</TAX_AMOUNT>". "\n" .
									"<TAX_LINE_AMOUNT>".number_format($shipping_tax_amount, 4, '.', '')."</TAX_LINE_AMOUNT>". "\n" .
									"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
									"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
									"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
									"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
								"</ARTICLE_PRICE_GROS>". "\n" .
								"<ARTICLE_WEIGHT>0</ARTICLE_WEIGHT>". "\n".
							"</ORDER_ITEM>". "\n";
							
						} // if ($rcm_versandkosten>0) 
		} // end if (EXPORT_SHIPPING_AS_PRODUCT) 
	
		if (EXPORT_SHIPPING_AS_PRODUCT && SHOPSYSTEM != 'virtuemart'  && SHOPSYSTEM != 'shopware' && SHOPSYSTEM != 'woocommerce' && SHOPSYSTEM != 'shopware') {
				
				// Versand ggfls abweichend, d.h Brutto statt netto hinterlegt
				$BRUTTO_SHOP = BRUTTO_SHOP;
				$EXPORT_SHIPPING_AS_PRODUCT_SKU=EXPORT_SHIPPING_AS_PRODUCT_SKU;
				$STEUERSATZ='19';
				// Versandkosten ermitteln
		  		if (SHOPSYSTEM == 'veyton') {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (orders_total_key=\"shipping\") and orders_id = " . $orders_id;
				} else if (SHOPSYSTEM != 'presta' && SHOPSYSTEM != 'virtuemart') {
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (class=\"ot_shipping\") and orders_id = " . $orders_id;
				}
				if (SHOPSYSTEM != 'presta' && SHOPSYSTEM != 'virtuemart') {				
					$versand_query = dmc_db_query($versand_sql);
					if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
						$rcm_versandart=$versanddata['title'];
						if ($BRUTTO_SHOP) {
							// Bruttopreise im Shop  orders_total_price  orders_total_tax 
							if (SHOPSYSTEM != 'veyton') {
								$rcm_versandkosten_net =$versanddata['value']/TAX_SHIPPING;
								$rcm_versandkosten = $versanddata['value'];	
							} else {
								$rcm_versandkosten = $versanddata['orders_total_price'];
								$rcm_versandkosten_net = $versanddata['orders_total_price']/TAX_SHIPPING;	
								$rcm_versandkosten_tax=TAX_SHIPPING;
							}
						} else {
							if (SHOPSYSTEM != 'veyton') {
								$rcm_versandkosten_net =$versanddata['value'];
								$rcm_versandkosten = $versanddata['value']*TAX_SHIPPING;	
							} else {
								$rcm_versandkosten_net =$versanddata['orders_total_price'];
								$rcm_versandkosten = $versanddata['orders_total_price']*TAX_SHIPPING;	
								$rcm_versandkosten_tax=TAX_SHIPPING;
							}
						}
																		}
				} else {
					// virtuemart oder presta
					$rcm_versandkosten_net =$shipping_amount;
					$rcm_versandkosten = $shipping_amount+$shipping_tax_amount;					
					if ($rcm_versandkosten>0)
						$rcm_versandkosten_tax=$shipping_tax_amount/$rcm_versandkosten*100;	
					else
						$rcm_versandkosten_tax=0;
				}
				
				//  GGFls Merhrwertsteuersatz der Versandkosten ermitteln
				if (SHOPSYSTEM != 'presta' && SHOPSYSTEM != 'virtuemart' && SHOPSYSTEM != 'veyton') {	
					$rcm_versandkosten=$rcm_versandkosten7=$steuerbetrag7prozent=$steuerbetrag19prozent=0;
					//$versand_sql = "select value from ".TABLE_ORDERS_TOTAL." where class='ot_tax' AND title like '%7%' and orders_id = " . $orders_id;
					// Versandkosten Brutto
					$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where (class=\"ot_shipping\") and orders_id = " . $orders_id;
					$versand_query = dmc_db_query($versand_sql);
					if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
						$versandkosten=$versanddata['value'];
					} else {
						$versandkosten=0;
					}
					// Nur wenn vorhanden, ggfls aufteilen	
					if ($versandkosten>0) {
					// Basis Gesamtsumme aller Artikel
					$versand_sql = "SELECT sum(final_price) AS value FROM `orders_products` WHERE orders_id= " . $orders_id;
					$versand_query = dmc_db_query($versand_sql);
					if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
						$gesamtsummeartikel=$versanddata['value'];
					} else {
						$gesamtsummeartikel=0;
					}	
					// Gesamtsumme 7% Artikel
					$versand_sql = "SELECT sum(final_price)  AS value FROM `orders_products` where products_tax=7 AND orders_id= " . $orders_id;
					$versand_query = dmc_db_query($versand_sql);
					if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
						$gesamtsummeartikel7prozent=$versanddata['value'];
						$prozentanteilgesamtsummeartikel7prozent=100*$gesamtsummeartikel7prozent/$gesamtsummeartikel;
						$rcm_versandkosten7=$versandkosten*$prozentanteilgesamtsummeartikel7prozent/100;
						$rcm_versandkosten_net7=$rcm_versandkosten7/1.07;
						$steuerbetrag7prozent=$rcm_versandkosten7-$rcm_versandkosten_net7;
						$EXPORT_SHIPPING_AS_PRODUCT_SKU7='99998';
						$STEUERSATZ7='7';
					} else {
						$steuerbetrag7prozent=0;
						$rcm_versandkosten7=0;
						$rcm_versandkosten_net7=0;
					}
					// $versand_sql = "select value from ".TABLE_ORDERS_TOTAL." where class='ot_tax' AND title like '%19%' and orders_id = " . $orders_id;
					// Gesamtsumme 19% Artikel
					$versand_sql = "SELECT sum(final_price)  AS value FROM `orders_products` where products_tax=19 AND orders_id= " . $orders_id;
					$versand_query = dmc_db_query($versand_sql);
					if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
						$gesamtsummeartikel19prozent=$versanddata['value'];
						$prozentanteilgesamtsummeartikel19prozent=100*$gesamtsummeartikel19prozent/$gesamtsummeartikel;
						$rcm_versandkosten19=$versandkosten*$prozentanteilgesamtsummeartikel19prozent/100;
						$rcm_versandkosten_net19=$rcm_versandkosten19/1.19;
						$steuerbetrag19prozent=$rcm_versandkosten19-$rcm_versandkosten_net19;
						$EXPORT_SHIPPING_AS_PRODUCT_SKU19='99999';
						$STEUERSATZ19='19';
					} else {
						$steuerbetrag7prozent=0;
						$rcm_versandkosten7=0;
						$rcm_versandkosten_net7=0;
					}
					} // ende wenn > 0
					
				}		
				
				// Standard VERSANDKOSTEN - als Artikel anfügen, wenn > 0
				if ($rcm_versandkosten>0) {
					$schema .= 	"<ORDER_ITEM>". "\n";
					  
					$products_pos++;
					if ($products_pos<10) $schema .= '<LINE_ITEM_ID>00' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					else if ($products_pos<100) $schema .= '<LINE_ITEM_ID>0' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					else $schema .= '<LINE_ITEM_ID>' . $products_pos . '</LINE_ITEM_ID>' . "\n";

					// Artikelnummer Sage Classic Line -> Aufbau Schlüssel A + 20Stellinge Artikelnummer + Lieferanten 0000000000
					$cl_art_nr = "A".$EXPORT_SHIPPING_AS_PRODUCT_SKU;
					for ($ii=strlen($EXPORT_SHIPPING_AS_PRODUCT_SKU);$ii<20;$ii++)
						$cl_art_nr .= ' '; // Artikelnummer Classic Line auffuellen

					$cl_art_nr .= '0000000000          '; // Lieferantennummer Classic Line  auffuellen	
					$cl_art_nr =  str_pad ( $cl_art_nr, 20, ' ', STR_PAD_RIGHT );

					$schema .=		'<PRODUCTS_ORDER_ID>'.html2ascii($orders['shipping_class']).'</PRODUCTS_ORDER_ID>'  . "\n" .
								"<ARTICLE_ID>". "\n" .
									'<SUPPLIER_AID>'.$EXPORT_SHIPPING_AS_PRODUCT_SKU.'</SUPPLIER_AID>'  . "\n".	// Artikelnummer Versandkosten
									"<SUPPLIER_AID_CLASSIC_LINE>".$cl_art_nr."</SUPPLIER_AID_CLASSIC_LINE>". "\n".
									"<PRODUCTS_ATTRIBUTE_ID></PRODUCTS_ATTRIBUTE_ID>". "\n".
									"<OPTIONS></OPTIONS>". "\n".
									'<ARTIKEL_ART>Zu-/Abschlag (Artikel)</ARTIKEL_ART>' . "\n".
									'<ARTICLE_TYPE>N</ARTICLE_TYPE>' . "\n".
									'<DESCRIPTION_SHORT>' . umlaute_order_export(($rcm_versandart)) . '</DESCRIPTION_SHORT>'  . "\n" .
									"<DESCRIPTION_LONG/>". "\n".
								"</ARTICLE_ID>". "\n" .
								"<QUANTITY>1</QUANTITY>". "\n".
								"<ORDER_UNIT>1</ORDER_UNIT>". "\n".
								"<ARTICLE_PRICE_NET>". "\n" .
									"<PRICE_AMOUNT>". $rcm_versandkosten_net . "</PRICE_AMOUNT>". "\n" .
									"<PRICE_LINE_AMOUNT>". $rcm_versandkosten_net . "</PRICE_LINE_AMOUNT>". "\n" .
									"<PRICE_FLAG/>". "\n" .
									"<TAX>".$STEUERSATZ."</TAX>". "\n" .
									"<TAX_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_AMOUNT>". "\n" .
									"<TAX_LINE_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_LINE_AMOUNT>". "\n" .
									"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
									"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
									"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
									"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
								"</ARTICLE_PRICE_NET>". "\n" .
								"<ARTICLE_PRICE_GROS>". "\n" .
									"<PRICE_AMOUNT>". $rcm_versandkosten . "</PRICE_AMOUNT>". "\n" .
									"<PRICE_LINE_AMOUNT>". $rcm_versandkosten . "</PRICE_LINE_AMOUNT>". "\n" .
									"<PRICE_FLAG/>". "\n" .
									"<TAX>".$STEUERSATZ."</TAX>". "\n" .
									"<TAX_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_AMOUNT>". "\n" .
									"<TAX_LINE_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net)."</TAX_LINE_AMOUNT>". "\n" .
									"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
									"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
									"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
									"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
								"</ARTICLE_PRICE_GROS>". "\n" .
								"<ARTICLE_WEIGHT>0</ARTICLE_WEIGHT>". "\n".
							"</ORDER_ITEM>". "\n";
							
						} // if ($rcm_versandkosten>0) 
						
				if ($rcm_versandkosten7>0) {
					$schema .= 	"<ORDER_ITEM>". "\n";
					  
					$products_pos++;
					if ($products_pos<10) $schema .= '<LINE_ITEM_ID>00' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					else if ($products_pos<100) $schema .= '<LINE_ITEM_ID>0' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					else $schema .= '<LINE_ITEM_ID>' . $products_pos . '</LINE_ITEM_ID>' . "\n";

					// Artikelnummer Sage Classic Line -> Aufbau Schlüssel A + 20Stellinge Artikelnummer + Lieferanten 0000000000
					$cl_art_nr = "A".$EXPORT_SHIPPING_AS_PRODUCT_SKU7;
					for ($ii=strlen($EXPORT_SHIPPING_AS_PRODUCT_SKU7);$ii<20;$ii++)
						$cl_art_nr .= ' '; // Artikelnummer Classic Line auffuellen

					$cl_art_nr .= '0000000000          '; // Lieferantennummer Classic Line  auffuellen	
					$cl_art_nr =  str_pad ( $cl_art_nr, 20, ' ', STR_PAD_RIGHT );

					$schema .=		'<PRODUCTS_ORDER_ID>'.html2ascii($orders['shipping_class']).'</PRODUCTS_ORDER_ID>'  . "\n" .
								"<ARTICLE_ID>". "\n" .
									'<SUPPLIER_AID>'.$EXPORT_SHIPPING_AS_PRODUCT_SKU7.'</SUPPLIER_AID>'  . "\n".	// Artikelnummer Versandkosten
									"<SUPPLIER_AID_CLASSIC_LINE>".$cl_art_nr."</SUPPLIER_AID_CLASSIC_LINE>". "\n".
									"<PRODUCTS_ATTRIBUTE_ID></PRODUCTS_ATTRIBUTE_ID>". "\n".
									"<OPTIONS></OPTIONS>". "\n". 
									'<ARTIKEL_ART>Zu-/Abschlag (Artikel)</ARTIKEL_ART>' . "\n".
									'<ARTICLE_TYPE>N</ARTICLE_TYPE>' . "\n".
									'<DESCRIPTION_SHORT>' . umlaute_order_export(($rcm_versandart)) . '</DESCRIPTION_SHORT>'  . "\n" .
									"<DESCRIPTION_LONG/>". "\n".
								"</ARTICLE_ID>". "\n" .
								"<QUANTITY>1</QUANTITY>". "\n".
								"<ORDER_UNIT>1</ORDER_UNIT>". "\n".
								"<ARTICLE_PRICE_NET>". "\n" .
									"<PRICE_AMOUNT>". $rcm_versandkosten_net7 . "</PRICE_AMOUNT>". "\n" .
									"<PRICE_LINE_AMOUNT>". $rcm_versandkosten_net7 . "</PRICE_LINE_AMOUNT>". "\n" .
									"<PRICE_FLAG/>". "\n" .
									"<TAX>".$STEUERSATZ7."</TAX>". "\n" .
									"<TAX_AMOUNT>".($steuerbetrag7prozent)."</TAX_AMOUNT>". "\n" .
									"<TAX_LINE_AMOUNT>".($rcm_versandkosten7-$rcm_versandkosten_net7)."</TAX_LINE_AMOUNT>". "\n" .
									"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
									"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
									"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
									"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
								"</ARTICLE_PRICE_NET>". "\n" .
								"<ARTICLE_PRICE_GROS>". "\n" .
									"<PRICE_AMOUNT>". $rcm_versandkosten7 . "</PRICE_AMOUNT>". "\n" .
									"<PRICE_LINE_AMOUNT>". $rcm_versandkosten7 . "</PRICE_LINE_AMOUNT>". "\n" .
									"<PRICE_FLAG/>". "\n" .
									"<TAX>".$STEUERSATZ7."</TAX>". "\n" .
									"<TAX_AMOUNT>".($steuerbetrag7prozent)."</TAX_AMOUNT>". "\n" .
									"<TAX_LINE_AMOUNT>".($rcm_versandkosten-$rcm_versandkosten_net7)."</TAX_LINE_AMOUNT>". "\n" .
									"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
									"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
									"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
									"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
								"</ARTICLE_PRICE_GROS>". "\n" .
								"<ARTICLE_WEIGHT>0</ARTICLE_WEIGHT>". "\n".
							"</ORDER_ITEM>". "\n";
							
						} // if ($rcm_versandkosten7>0) 
						
				if ($rcm_versandkosten19>0) {
					$schema .= 	"<ORDER_ITEM>". "\n";
					  
					$products_pos++;
					if ($products_pos<10) $schema .= '<LINE_ITEM_ID>00' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					else if ($products_pos<100) $schema .= '<LINE_ITEM_ID>0' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					else $schema .= '<LINE_ITEM_ID>' . $products_pos . '</LINE_ITEM_ID>' . "\n";

					// Artikelnummer Sage Classic Line -> Aufbau Schlüssel A + 20Stellinge Artikelnummer + Lieferanten 0000000000
					$cl_art_nr = "A".$EXPORT_SHIPPING_AS_PRODUCT_SKU19;
					for ($ii=strlen($EXPORT_SHIPPING_AS_PRODUCT_SKU19);$ii<20;$ii++)
						$cl_art_nr .= ' '; // Artikelnummer Classic Line auffuellen

					$cl_art_nr .= '0000000000          '; // Lieferantennummer Classic Line  auffuellen	
					$cl_art_nr =  str_pad ( $cl_art_nr, 20, ' ', STR_PAD_RIGHT );

					$schema .=		'<PRODUCTS_ORDER_ID>'.html2ascii($orders['shipping_class']).'</PRODUCTS_ORDER_ID>'  . "\n" .
								"<ARTICLE_ID>". "\n" .
									'<SUPPLIER_AID>'.$EXPORT_SHIPPING_AS_PRODUCT_SKU19.'</SUPPLIER_AID>'  . "\n".	// Artikelnummer Versandkosten
									"<SUPPLIER_AID_CLASSIC_LINE>".$cl_art_nr."</SUPPLIER_AID_CLASSIC_LINE>". "\n".
									"<PRODUCTS_ATTRIBUTE_ID></PRODUCTS_ATTRIBUTE_ID>". "\n".
									"<OPTIONS></OPTIONS>". "\n".
									'<ARTIKEL_ART>Zu-/Abschlag (Artikel)</ARTIKEL_ART>' . "\n".
									'<ARTICLE_TYPE>N</ARTICLE_TYPE>' . "\n".
									'<DESCRIPTION_SHORT>' . umlaute_order_export(($rcm_versandart)) . '</DESCRIPTION_SHORT>'  . "\n" .
									"<DESCRIPTION_LONG/>". "\n".
								"</ARTICLE_ID>". "\n" .
								"<QUANTITY>1</QUANTITY>". "\n".
								"<ORDER_UNIT>1</ORDER_UNIT>". "\n".
								"<ARTICLE_PRICE_NET>". "\n" .
									"<PRICE_AMOUNT>". $rcm_versandkosten_net19 . "</PRICE_AMOUNT>". "\n" .
									"<PRICE_LINE_AMOUNT>". $rcm_versandkosten_net19 . "</PRICE_LINE_AMOUNT>". "\n" .
									"<PRICE_FLAG/>". "\n" .
									"<TAX>".$STEUERSATZ19."</TAX>". "\n" .
									"<TAX_AMOUNT>".($steuerbetrag19prozent)."</TAX_AMOUNT>". "\n" .
									"<TAX_LINE_AMOUNT>".($rcm_versandkosten19-$rcm_versandkosten_net19)."</TAX_LINE_AMOUNT>". "\n" .
									"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
									"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
									"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
									"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
								"</ARTICLE_PRICE_NET>". "\n" .
								"<ARTICLE_PRICE_GROS>". "\n" .
									"<PRICE_AMOUNT>". $rcm_versandkosten19 . "</PRICE_AMOUNT>". "\n" .
									"<PRICE_LINE_AMOUNT>". $rcm_versandkosten19 . "</PRICE_LINE_AMOUNT>". "\n" .
									"<PRICE_FLAG/>". "\n" .
									"<TAX>".$STEUERSATZ19."</TAX>". "\n" .
									"<TAX_AMOUNT>".($steuerbetrag19prozent)."</TAX_AMOUNT>". "\n" .
									"<TAX_LINE_AMOUNT>".($rcm_versandkosten19-$rcm_versandkosten_net19)."</TAX_LINE_AMOUNT>". "\n" .
									"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
									"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
									"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
									"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
								"</ARTICLE_PRICE_GROS>". "\n" .
								"<ARTICLE_WEIGHT>0</ARTICLE_WEIGHT>". "\n".
							"</ORDER_ITEM>". "\n";
							
						} // if ($rcm_versandkosten19>0) 
						
				
		} // end if (EXPORT_SHIPPING_AS_PRODUCT) 
		
			// discount als Produkt woocommerce order shopware
		if (EXPORT_BONUS_AS_PRODUCT && (SHOPSYSTEM == 'woocommerce' || SHOPSYSTEM == 'shopware') && $order_total_discount_amount > 0) {
		
					// Bonus - als Artikel anfügen
					$schema .= 	"<ORDER_ITEM>". "\n";
					$products_pos++;
					if ($products_pos<10) $schema .= '<LINE_ITEM_ID>00' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					else if ($products_pos<100) $schema .= '<LINE_ITEM_ID>0' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					else $schema .= '<LINE_ITEM_ID>' . $products_pos . '</LINE_ITEM_ID>' . "\n";

					// Artikelnummer Sage Classic Line -> Aufbau Schlüssel A + 20Stellinge Artikelnummer + Lieferanten 0000000000
					$cl_art_nr = "A".EXPORT_BONUS_AS_PRODUCT_SKU;
					for ($ii=strlen(EXPORT_BONUS_AS_PRODUCT_SKU);$ii<20;$ii++)
						$cl_art_nr .= ' '; // Artikelnummer Classic Line auffuellen
													 
					$cl_art_nr .= '0000000000          '; // Lieferantennummer Classic Line  auffuellen	
					$cl_art_nr =  str_pad ( $cl_art_nr, 20, ' ', STR_PAD_RIGHT );
	  
					$schema .=		'<PRODUCTS_ORDER_ID>30001</PRODUCTS_ORDER_ID>'  . "\n" .
									"<ARTICLE_ID>". "\n" .
										'<SUPPLIER_AID>'.EXPORT_BONUS_AS_PRODUCT_SKU.'</SUPPLIER_AID>'  . "\n".	// Artikelnummer Gutschein
										"<SUPPLIER_AID_CLASSIC_LINE>".$cl_art_nr."</SUPPLIER_AID_CLASSIC_LINE>". "\n".
										"<PRODUCTS_ATTRIBUTE_ID></PRODUCTS_ATTRIBUTE_ID>". "\n".
										"<OPTIONS></OPTIONS>". "\n".
										'<ARTIKEL_ART>Zu-/Abschlag (Artikel)</ARTIKEL_ART>' . "\n".
										'<ARTICLE_TYPE>N</ARTICLE_TYPE>' . "\n".
										'<DESCRIPTION_SHORT>' . umlaute_order_export(($rcm_versandart)) . '</DESCRIPTION_SHORT>'  . "\n" .
										"<DESCRIPTION_LONG/>". "\n".
									"</ARTICLE_ID>". "\n" .
									"<QUANTITY>-1</QUANTITY>". "\n".
									"<ORDER_UNIT>-1</ORDER_UNIT>". "\n".
									"<ARTICLE_PRICE_NET>". "\n" .
										"<PRICE_AMOUNT>". ($order_total_discount_amount/1.19) . "</PRICE_AMOUNT>". "\n" .
										"<PRICE_LINE_AMOUNT>". ($order_total_discount_amount/1.19) . "</PRICE_LINE_AMOUNT>". "\n" .
										"<PRICE_FLAG/>". "\n" .
										"<TAX>19.0000</TAX>". "\n" .
										"<TAX_AMOUNT>".($order_total_discount_amount-($order_total_discount_amount/1.19))."</TAX_AMOUNT>". "\n" .
										"<TAX_LINE_AMOUNT>".($order_total_discount_amount-($order_total_discount_amount/1.19))."</TAX_LINE_AMOUNT>". "\n" .
										"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
										"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
										"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
										"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
									"</ARTICLE_PRICE_NET>". "\n" .
									"<ARTICLE_PRICE_GROS>". "\n" .
										"<PRICE_AMOUNT>". $order_total_discount_amount . "</PRICE_AMOUNT>". "\n" .
										"<PRICE_LINE_AMOUNT>". $order_total_discount_amount . "</PRICE_LINE_AMOUNT>". "\n" .
										"<PRICE_FLAG/>". "\n" .
										"<TAX>19.0000</TAX>". "\n" .
										"<TAX_AMOUNT>".($order_total_discount_amount-($order_total_discount_amount/1.19))."</TAX_AMOUNT>". "\n" .
										"<TAX_LINE_AMOUNT>".($order_total_discount_amount-($order_total_discount_amount/1.19))."</TAX_LINE_AMOUNT>". "\n" .
										"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
										"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
										"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
										"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
									"</ARTICLE_PRICE_GROS>". "\n" .
									"<ARTICLE_WEIGHT>0</ARTICLE_WEIGHT>". "\n".
								"</ORDER_ITEM>". "\n";
					  
			} //endif EXPORT_BONUS_AS_PRODUCT woocommerce
			
			// Payments mit Mappings 
			if (EXPORT_PAYMENTS_AS_PRODUCT && $payment_amount>0 && (SHOPSYSTEM == 'virtuemart' || SHOPSYSTEM == 'woocommerce' || SHOPSYSTEM == 'shopware')) {
				// Zahlunsarten - als Artikel 
					$schema .= 	"<ORDER_ITEM>". "\n";
					  
					$products_pos++;
					if ($products_pos<10) $schema .= '<LINE_ITEM_ID>00' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					else if ($products_pos<100) $schema .= '<LINE_ITEM_ID>0' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					else $schema .= '<LINE_ITEM_ID>' . $products_pos . '</LINE_ITEM_ID>' . "\n";
					
					// Mapping Versandart auf VersandArtikelnummer 
					// Ewertz Paket EU,Paket innerdeutsch,Paket weltweit,Brief innerdeutsch, Maxibrief innerdeutsch,Brief weltweit,Maxibrief weltweit
					if ($payment_method=='Nachnahme') $EXPORT_PAYMENT_AS_PRODUCT_SKU='0,0007C';
					else if ($payment_method=='Kreditkarte') $EXPORT_PAYMENT_AS_PRODUCT_SKU='0,0007A';
					else if ($payment_method=='Vorkasse') $EXPORT_PAYMENT_AS_PRODUCT_SKU='0,0007B';
					else if ($payment_method=='Lastschrift') $EXPORT_PAYMENT_AS_PRODUCT_SKU='0,0007F';
					else if ($payment_method=='PayPal') $EXPORT_PAYMENT_AS_PRODUCT_SKU='0,0007D';
					else if ($payment_method=='SOFORT Überweisung') $EXPORT_PAYMENT_AS_PRODUCT_SKU='0,0007E';
					else $EXPORT_PAYMENT_AS_PRODUCT_SKU='vorkasse';
	
					$payment_amount=0;
					$payment_tax_amount=0;
					
					// Artikelnummer Sage Classic Line -> Aufbau Schlüssel A + 20stellige Artikelnummer + Lieferanten 0000000000
					$cl_art_nr = "A".$EXPORT_PAYMENT_AS_PRODUCT_SKU;
					for ($ii=strlen($EXPORT_PAYMENT_AS_PRODUCT_SKU);$ii<20;$ii++)
						$cl_art_nr .= ' '; // Artikelnummer Classic Line auffuellen
													
					$cl_art_nr .= '0000000000          '; // Lieferantennummer Classic Line  auffuellen	
					$cl_art_nr =  str_pad ( $cl_art_nr, 20, ' ', STR_PAD_RIGHT );

					$schema .=		'<PRODUCTS_ORDER_ID>'.html2ascii($shipping_method).'</PRODUCTS_ORDER_ID>'  . "\n" .
								"<ARTICLE_ID>". "\n" .
									'<SUPPLIER_AID>'.$EXPORT_PAYMENT_AS_PRODUCT_SKU.'</SUPPLIER_AID>'  . "\n".	// Artikelnummer Versandkosten
									"<SUPPLIER_AID_CLASSIC_LINE>".$cl_art_nr."</SUPPLIER_AID_CLASSIC_LINE>". "\n".
									"<PRODUCTS_ATTRIBUTE_ID></PRODUCTS_ATTRIBUTE_ID>". "\n".
									"<OPTIONS></OPTIONS>". "\n".
									'<ARTIKEL_ART>Zu-/Abschlag (Artikel)</ARTIKEL_ART>' . "\n".
									'<ARTICLE_TYPE>N</ARTICLE_TYPE>' . "\n".
									'<DESCRIPTION_SHORT>' . umlaute_order_export(($payment_method)) . '</DESCRIPTION_SHORT>'  . "\n" .
									"<DESCRIPTION_LONG/>". "\n".
								"</ARTICLE_ID>". "\n" .
								"<QUANTITY>1</QUANTITY>". "\n".
								"<ORDER_UNIT>1</ORDER_UNIT>". "\n".
								"<ARTICLE_PRICE_NET>". "\n" .
									"<PRICE_AMOUNT>". number_format($payment_amount, 4, '.', '') . "</PRICE_AMOUNT>". "\n" .
									"<PRICE_LINE_AMOUNT>". number_format($payment_amount, 4, '.', '') . "</PRICE_LINE_AMOUNT>". "\n" .
									"<PRICE_FLAG/>". "\n" .
									"<TAX>19.00</TAX>". "\n" .
									"<TAX_AMOUNT>".number_format($payment_tax_amount, 4, '.', '')."</TAX_AMOUNT>". "\n" .
									"<TAX_LINE_AMOUNT>".number_format($payment_tax_amount, 4, '.', '')."</TAX_LINE_AMOUNT>". "\n" .
									"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
									"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
									"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
									"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
								"</ARTICLE_PRICE_NET>". "\n" .
								"<ARTICLE_PRICE_GROS>". "\n" .
									"<PRICE_AMOUNT>". number_format(($payment_amount+$payment_tax_amount), 4, '.', ''). "</PRICE_AMOUNT>". "\n" .
									"<PRICE_LINE_AMOUNT>". number_format(($payment_amount+$payment_tax_amount), 4, '.', ''). "</PRICE_LINE_AMOUNT>". "\n" .
									"<PRICE_FLAG/>". "\n" .
									"<TAX>19.00</TAX>". "\n" .
									"<TAX_AMOUNT>".number_format($payment_tax_amount, 4, '.', '')."</TAX_AMOUNT>". "\n" .
									"<TAX_LINE_AMOUNT>".number_format($payment_tax_amount, 4, '.', '')."</TAX_LINE_AMOUNT>". "\n" .
									"<DISCOUNT_AMOUNT>0.0000</DISCOUNT_AMOUNT>". "\n" .
									"<DISCOUNT_PERCENT>0.0000</DISCOUNT_PERCENT>". "\n" .
									"<DISCOUNT_PRICE_AMOUNT>0.0000</DISCOUNT_PRICE_AMOUNT>". "\n" .
									"<DISCOUNT_PRICE_LINE_AMOUNT>0.0000</DISCOUNT_PRICE_LINE_AMOUNT>". "\n" .
								"</ARTICLE_PRICE_GROS>". "\n" .
								"<ARTICLE_WEIGHT>0</ARTICLE_WEIGHT>". "\n".
							"</ORDER_ITEM>". "\n";
							
			} // end if (EXPORT_PAYMENTS_AS_PRODUCT) 
				
		    $schema .= '</ORDER_ITEM_LIST>' . "\n"; 	 
?>