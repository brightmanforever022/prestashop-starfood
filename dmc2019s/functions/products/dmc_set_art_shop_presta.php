<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_set_art_shop_presta.php												*
*  inkludiert von dmc_write_art.php 										*	
*  Artikel Beschreibung für Presta anlegen									*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
28.05.2013
- neu
10.12.2013
- Erweitert um $Artikel_Einkaufspreis
10.12.2013
- Erweitert um die Befuellung der Tabelle ps_stock_available zur Anzeige von Bestaenden bei Multishops
23.10.2017
- Erweitert um Multishop Funktionen
*/

			$SHOP_ID=STORE_ID;	// Standard
			
			//$SHOP_ID=STORE_ID;	// Standard
			//Multishop mit Shop_IDs aus ARTIKEL_STARTSEITE = $Artikel_Presta_Multishop_iD
			if (preg_match('/@/', $Artikel_Presta_Multishop_iD)) {
				$shop_ids = explode ( "@", $Artikel_Presta_Multishop_iD);
				
				
			} else {
				$shop_ids[0] = $SHOP_ID;
			}
	        
			for ($anzahl=0;$anzahl< sizeof($shop_ids);$anzahl++) {
				if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_art_shop_presta - Presta Shop-Details fuer SHOP_ID ".$shop_ids[$anzahl]." \n");
				// Shop zuordnen
				$sql_data_array = array(
					'id_product' => $Artikel_ID,
					'id_shop' => $shop_ids[$anzahl],
					'id_category_default' => $Kategorie_ID,
					'id_tax_rules_group' => 1,
					'on_sale' => 0,
					'online_only' => 0,
					'ecotax' => 0,
					'minimal_quantity' => 1,
					'price' => $Artikel_Preis,
					'wholesale_price' => $Artikel_Einkaufspreis,
					'unity' => $Artikel_VPE,
					'unit_price_ratio' => $Artikel_VPE_Value,					// 0 fuer Einheit NICHT anzeigen, sonst 1 fuer Einheit anzeigen oder Umrechnungsfaktor (0.5 wenn 2Qm pro qm)
					'additional_shipping_cost' => 0,
					'customizable' => 0,
					'uploadable_files' => 0,
					'text_fields' => 0,
					'active' => $Aktiv,
					'redirect_type' => '404',
				// 	'id_product_redirected' => 0,			ALT
				 	// 'id_type_redirected' => 0,			
					'available_for_order' => 1,
					//'available_date' => '0000-00-00',
					//'condition' => 'new',
					'show_price' => 1,
					// 'indexed' => 1,
					'visibility' => 'both',
					'cache_default_attribute' => 0,
					'advanced_stock_management' => 0,
						//	 'price_1' => $Artikel_Preis1,
					//	 'price_2' => $Artikel_Preis2,
					//	 'price_3' => $Artikel_Preis3					 
				
					'date_add' => 'now()',
					'date_upd' => 'now()'					 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	
				);
				
				// delete first  
				dmc_sql_delete(DB_TABLE_PREFIX."product_shop", "id_shop = ".$shop_ids[$anzahl]." AND id_product='".$Artikel_ID."'");
				dmc_sql_insert_array(DB_TABLE_PREFIX."product_shop", $sql_data_array);
				$query="UPDATE ".DB_TABLE_PREFIX."product_shop SET id_category_default = (SELECT id_category FROM ".DB_TABLE_PREFIX."category_product WHERE id_product='".$Artikel_ID."' ORDER BY id_category ASC LIMIT 1) WHERE id_product='".$Artikel_ID."' AND id_shop=".$shop_ids[$anzahl];
				dmc_sql_query($query);
				
				// Bestandstabelle ps_stock_available
				$sql_data_array = array(
					'id_product' => $Artikel_ID,
					 'id_product_attribute' => 0,
					'id_shop' => $shop_ids[$anzahl],
					'id_shop_group' => 0,
					'quantity' => $Artikel_Menge,
					'depends_on_stock' => 0,
					'out_of_stock' => 2			 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	
				);
				
				// delete first  	 	 	 id_tax_rules_group	 on_sale
				dmc_sql_delete(DB_TABLE_PREFIX."stock_available", "id_shop = ".$shop_ids[$anzahl]." AND id_product='".$Artikel_ID."'");
				dmc_sql_insert_array(DB_TABLE_PREFIX."stock_available", $sql_data_array);
			}
			
			
?>
	