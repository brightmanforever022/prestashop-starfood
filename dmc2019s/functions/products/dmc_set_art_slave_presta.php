<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_set_art_slave_presta.php												*
*  inkludiert von dmc_write_art.php 										*	
*  Artikel Variante für Shop anlegen -> Presta								*
*  ehemals in hhg_set_conf_products.php										*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
13.03.2012
- neu
*/	
		// Standard
		$id_shop = 1;
		$id_lang = 1;
		
	    if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_art_slave_presta - produkt variante anlegen \n");
        
		// Ermittelung der Haupt Produkt ID
		// $main_product_id =  dmc_get_id_by_artno($Artikel_Variante_Von);
		$main_product_id =  $Artikel_Variante_Von_id;
		// Ermitteung, ob Hauptartikel bereits einer Variante zugeordnet hat, wenn ja ist cache_default_attribute not null
		if (dmc_sql_select_query('cache_default_attribute','product',"id_product='$main_product_id'") != "0" ) {
			$first_variante = false;
			if (DEBUGGER>=1) fwrite($dateihandle, "... first_variante = false \n");
		} else {
			$first_variante = true;
			if (DEBUGGER>=1) fwrite($dateihandle, "... first_variante = true \n");
		}		
		// ggfls Produkt Attribute ID ermitteln
		$id_product_attribute =	dmc_sql_select_query('id_product_attribute','product_attribute',"reference='$Artikel_Artikelnr'");
		if ($id_product_attribute == "") {
			// Variante einfuegen - insert
			if ($first_variante==true) $is_default=1; else $is_default=null;
			// product_attribute tabelle -> Variantenartikel mit Artikelnummer, Preis etc.
			$table = "product_attribute";
			$columns="id_product, reference, supplier_reference, location, ean13, upc, wholesale_price, price, ecotax, quantity, weight,  minimal_quantity, available_date";
			$values ="$main_product_id, '$Artikel_Artikelnr', '', '', '$Artikel_EAN','',0.00,$Artikel_Preis,0.00,$Artikel_Menge,$Artikel_Gewicht,1,'0000-00-00'";
			dmc_sql_insert($table, $columns, $values);
			
			// neue Produkt Attribute ID ermitteln
			$id_product_attribute=dmc_sql_select_query('id_product_attribute',$table,"reference='$Artikel_Artikelnr'");
			
			// product_attribute_shop fuer Shopspezifische werte
			$table = "product_attribute_shop";
			$columns="id_product_attribute, id_product, id_shop, wholesale_price, price, ecotax, weight, unit_price_impact, default_on, minimal_quantity, available_date";
			if ($first_variante==true)  
				$values ="$id_product_attribute, $main_product_id, $id_shop, 0.00,$Artikel_Preis,0.00,$Artikel_Gewicht,0.00,$is_default,1,'0000-00-00'";
			else
				$values ="$id_product_attribute, $main_product_id, $id_shop, 0.00,$Artikel_Preis,0.00,$Artikel_Gewicht,0.00,null,1,'0000-00-00'";
			dmc_sql_insert($table, $columns, $values);
			
			// Merkmale durchlaufen
			$Merkmale = explode ( '@', $Artikel_Merkmal);			
			$Auspraegungen = explode ( '@', $Artikel_Auspraegung);
			for ( $Anz_Merkmale = 0; $Anz_Merkmale < count ( $Merkmale ); $Anz_Merkmale++ )
			{
				// pruefen ob Merkmal (attribute_group) schon existiert und ggfls anlegen
				$id_attribute_group=dmc_sql_select_query('id_attribute_group','attribute_group_lang',"name='$Merkmale[$Anz_Merkmale]'");
				if ($id_attribute_group=="") {
					// Merkmal anlegen
					$table = "attribute_group"; 
					if (strpos(strtolower($Merkmale[$Anz_Merkmale]), 'color') !== false || strpos(strtolower($Merkmale[$Anz_Merkmale]), 'farb') !== false) {
						// Farbattribute
						$is_color_group=1;  
						$group_type="color";
					} else {
						$is_color_group=0; 
						//$group_type="radio";
						$group_type="select";
					}
					$position = dmc_get_highest_id("position",DB_TABLE_PREFIX.$table)+1;
					$columns="is_color_group, group_type, position";
					$values ="$is_color_group, '$group_type', $position";
					dmc_sql_insert($table, $columns, $values);
					// neue Merkmal (Attribute Gruppen) ID ermitteln
					$id_attribute_group = dmc_get_highest_id("id_attribute_group",DB_TABLE_PREFIX.$table); 	
			 		// Merkmal Text anlegen attribute_group_lang
					$table = "attribute_group_lang";
					$columns="id_attribute_group, id_lang, name, public_name";
					$values ="".$id_attribute_group.", $id_lang, '".$Merkmale[$Anz_Merkmale]."', '".$Merkmale[$Anz_Merkmale]."'";					
					dmc_sql_insert($table, $columns, $values);					
				} else {
					// ggfls Merkmal update
				}
				// Auspraegung zu Merkmal mit ID $id_attribute_group anlegen, wenn noch nicht existent
				$id_attribute=dmc_sql_select_query('id_attribute','attribute_lang',"name='".$Auspraegungen[$Anz_Merkmale]."'");
				if ($id_attribute=="") {
					// Auspraegung Text anlegen attribute_lang
					$color_code = "";
					if ($Auspraegungen[$Anz_Merkmale]=="white" || $Auspraegungen[$Anz_Merkmale]=="weiss" || $Auspraegungen[$Anz_Merkmale]=="weiß") $color_code = "#FFFFFF"; 
					else if ($Auspraegungen[$Anz_Merkmale]=="black" || $Auspraegungen[$Anz_Merkmale]=="schwarz") $color_code = "#000000";
					// neue id_attribute ermitteln
					$id_attribute = dmc_get_highest_id("id_attribute",DB_TABLE_PREFIX.$table); 
					$id_attribute = $id_attribute + 1;
					// Position muss vergeben sein
					$position=$id_attribute;
					$table = "attribute";
					$columns="id_attribute, id_attribute_group, color, position";
					$values ="$id_attribute, ".$id_attribute_group.", '".$color_code."', $position";								// color waere hier HEX Code			
					dmc_sql_insert($table, $columns, $values);	
					
					// Attribute Text
					$table = "attribute_lang";
					$columns="id_attribute, id_lang, name";
					$values ="".$id_attribute.", '".$id_lang."', '".$Auspraegungen[$Anz_Merkmale]."'";	// color waere hier HEX Code		
					dmc_sql_insert($table, $columns, $values);
					
					// Sprache 3
					$table = "attribute_lang";
					$columns="id_attribute, id_lang, name";
					$values ="".$id_attribute.", '3', '".$Auspraegungen[$Anz_Merkmale]."'";	// color waere hier HEX Code		
					dmc_sql_insert($table, $columns, $values);
				// Auspraegung dem Shop zuweisen
					 
					// Merkmal Text anlegen attribute_group_lang
					$table = "attribute_shop";
					$columns="id_attribute, id_shop";
					$values ="".$id_attribute.", ".$id_shop."";					
					dmc_sql_insert($table, $columns, $values);
				}
				// Verknuepfungen anlegen
				$table = "attribute_impact";
				$columns="id_product, id_attribute,weight,price";
				$values ="".$main_product_id.", ".$id_attribute.",0,0";					
				dmc_sql_insert($table, $columns, $values);
				$query="INSERT INTO `".DB_TABLE_PREFIX."product_attribute_combination` (`id_attribute`, `id_product_attribute`) VALUES ('$id_attribute', '$id_product_attribute')";
				dmc_sql_query($query); 
			} // end for 

			
			// Bild zuordnen ps_product_attribute_image
			
			// Bei erster Variante Update auf Artikeltabelle
			if ($first_variante==true) {
				dmc_sql_update('product', "cache_default_attribute=$id_product_attribute", "id_product=$main_product_id");	
				dmc_sql_update('product_attribute', "default_on=1", "id_product_attribute=$id_product_attribute");	
				dmc_sql_update('product_attribute_shop', "default_on=1", "id_product_attribute=$id_product_attribute");				
			}
			// Bestandstabelle ps_stock_available
				$sql_data_array = array(
					'id_product' => $main_product_id,
					'id_product_attribute' => $id_product_attribute,
					'id_shop' => 1,
					'id_shop_group' => 0,
					'quantity' => $Artikel_Menge,
					'depends_on_stock' => 0,
					'out_of_stock' => 2			 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	
				);
				
				// delete first  	 	 	 id_tax_rules_group	 on_sale
				dmc_sql_delete(DB_TABLE_PREFIX."stock_available", "id_shop = $id_shop AND id_product_attribute='".$id_product_attribute."'");
				dmc_sql_insert_array(DB_TABLE_PREFIX."stock_available", $sql_data_array);
		} else {
			// Variante aktualisieren - update auf Menge
		//	dmc_sql_update('product_attribute', "quantity=$Artikel_Menge", "reference='$Artikel_Artikelnr'");	
				// Bestandstabelle ps_stock_available
				$sql_data_array = array(
					'id_product' => $main_product_id,
					'id_product_attribute' => $id_product_attribute,
					'id_shop' => 1,
					'id_shop_group' => 0,
					'quantity' => $Artikel_Menge,
					'depends_on_stock' => 0,
					'out_of_stock' => 2			 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	
				); 
				
				// delete first  	 	 	 id_tax_rules_group	 on_sale
				dmc_sql_delete(DB_TABLE_PREFIX."stock_available", "id_shop = $id_shop AND id_product_attribute='".$id_product_attribute."'");
				dmc_sql_insert_array(DB_TABLE_PREFIX."stock_available", $sql_data_array);
			
		}
		
?>
	