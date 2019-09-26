<?php
/************************************************
*                                            	*
*  dmConnector Export							*
*  dmc_order_shipping.inc.php					*
*  Skript zur Ermittlung der Versandkosten zum	*
*  Einbinden in dmconnector_export.php			*
*  Copyright (C) 2011 DoubleM-GmbH.de			*
*                                               *
*************************************************/

	// Standard values for shipping
			$shipping_method="Abholer";
			$shipping_class="Abholer";
			$rcm_versandkosten = 0;	 
			$rcm_versandkosten_net = 0;
			$rcm_versandkosten_tax = 0;
		  
		   // Versandkosten ermitteln
		   if (SHOPSYSTEM == 'veyton') {
				$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where orders_total_key=\"shipping\" and orders_id = " . $orders['orders_id'];
				$versand_query = dmc_db_query($versand_sql);
	            if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
					$shipping_method=umlaute_order_export(html2ascii($versanddata['orders_total_name'])); 
					$shipping_class=html2ascii($versanddata['orders_total_model']);
					if (BRUTTO_SHOP) {
						$rcm_versandkosten = $versanddata['orders_total_price']*TAX_SHIPPING;	 
						$rcm_versandkosten_net = $versanddata['orders_total_price'];
					/*
						$rcm_versandkosten = $versanddata['orders_total_price'];	 
						$rcm_versandkosten_net = $rcm_versandkosten/TAX_SHIPPING;
					*/
					} else {
						$rcm_versandkosten = $versanddata['orders_total_price'];	 
						$rcm_versandkosten_net = $versanddata['orders_total_price'];
					/*
						$rcm_versandkosten = $versanddata['orders_total_price']*TAX_SHIPPING;	 
						$rcm_versandkosten_net = $rcm_versandkosten;
					*/
					}
					$rcm_versandkosten_tax =  $rcm_versandkosten-$rcm_versandkosten_net;
				}
			} else {
				$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where class=\"ot_shipping\" and orders_id = " . $orders['orders_id'];
				
				$versand_query = dmc_db_query($versand_sql);
	            if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
					$shipping_method=umlaute_order_export(html2ascii($versanddata['title']));
					$shipping_class=html2ascii($versanddata['class']);
					if (BRUTTO_SHOP) {
						$rcm_versandkosten = $versanddata['value'];	 
						$rcm_versandkosten_net = $rcm_versandkosten/TAX_SHIPPING;
					} else {
						$rcm_versandkosten = $versanddata['value']*TAX_SHIPPING;	 
						$rcm_versandkosten_net = $rcm_versandkosten;
					}
					$rcm_versandkosten_tax =  $rcm_versandkosten-$rcm_versandkosten_net;
				}
			}//end else
			
	
?>