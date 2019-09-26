<?php
/****************************************************************************
*                                                                        			*
*  dmConnector for all shops							*
*  dmc_set_xsell.php									*
*  Artikel-Cross-Selling schreiben							*
*  Copyright (C) 2016 DoubleM-GmbH.de					*
*                                                                       			*
*****************************************************************************/
/*
26.02.13
- Funktion aus dmconnector.php ausgegliedert (an Stelle von SetXSell() zu verwenden)
28.09.16
- Funktion für wooCommerce integriert
*/
 
defined( 'VALID_DMC' ) or die( 'Direct Access to this location is not allowed.' );
	
	function dmc_set_xsell() {
	
		global $dateihandle, $action;
		
		$Art_Xsell_Trenner = ".";		// Trenner zwischen Xsell Artikelnummern
		
		if (is_file('userfunctions/products/dmc_art_functions.php')) include ('userfunctions/products/dmc_art_functions.php');
		else include ('functions/products/dmc_art_functions.php');
		fwrite($dateihandle, "dmc_set_xsell - ArtNr ".$Artikel_Artikelnr." - ".date("l d of F Y h:i:s A")."\n");
		global $debugger, $action;; 		
	  
		/* Export Modus (overwrite, update), 
		* id as Xsell_Type, Artikel_Artikelnr, Xsell_Artikel_Artikelnr,
		* FreiFeld1, FreiFeld2, FreiFeld3, FreiFeld4 
		*/
	
  		$Xsell_Type = ($_POST['Xsell_Type']);
  		$Artikel_Artikelnr = ($_POST['Artikel_Artikelnr']);  
  		$Xsell_Artikel_Artikelnr = str_replace('|',',',$_POST['Xsell_Artikel_Artikelnr']);	// Unterstuetzung PIPE als Trenner
  		$Xsell_Artikel_Artikelnr = str_replace('@',',',$Xsell_Artikel_Artikelnr);	// Unterstuetzung @ als Trenner
  		$FreiFeld1 = ($_POST['FreiFeld1']);
  		$FreiFeld2 = ($_POST['FreiFeld2']);
  		$FreiFeld3 = ($_POST['FreiFeld3']);
  		$FreiFeld4 = ($_POST['FreiFeld4']);

		if ($Artikel_Artikelnr =="00082861")
			$Artikel_Artikelnr ="00163089";
		if (DEBUGGER>=1) fwrite($dateihandle, "Artikel-Cross-Sellingzuordung zu Artikel-Nr=".$Artikel_Artikelnr." -> Cross-Sell-Artikel-Nr=".$Xsell_Artikel_Artikelnr."\n");		
		
		
		$zubehoer_array = explode($Art_Xsell_Trenner, $Xsell_Artikel_Artikelnr);			
			
		for($Anzahl = 0; $Anzahl < count($zubehoer_array); $Anzahl++) {     // Artikelnummen durchlaufen	     
			$Xsell_Artikel_Artikelnr=$zubehoer_array[$Anzahl];
			// Der Artikelnummer zugehörige products_id und xsell_id ermitteln
			// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
			$Artikel_Artikelnr=trim($Artikel_Artikelnr);
			$Artikel_ID = dmc_get_id_by_artno($Artikel_Artikelnr); 
			$Xsell_Artikel_Artikelnr=trim($Xsell_Artikel_Artikelnr);
			$XSell_Artikel_ID = dmc_get_id_by_artno($Xsell_Artikel_Artikelnr); 
			if (DEBUGGER>=1)	fwrite($dateihandle, "Artikel_ID=".$Artikel_ID." -> Cross-Sell-XSell_Artikel_ID-Nr=".$XSell_Artikel_ID."\n");		
	
			// Wenn Artikel und XSell Artikel existieren
			if ($Artikel_ID != '' && $XSell_Artikel_ID != '') {		
				// Alte Zuordnungen löschen -> NUR die, welche auch aus der WaWi übertragen wurden
				// Überprüfen, ob Produkt XSell zugeordnet sind		
				if (strpos(strtolower(SHOPSYSTEM), 'woo') !== false) {
					// bei woocommerce abweichend -> erst array fuellen und dann absetzen.
					$XSell_Artikel_ID = intval($XSell_Artikel_ID);
					
				} else if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) {
					if ($Xsell_Type == 'cross_sell' || $Xsell_Type == 'similar') {
						// Ähnliche Artikel
						if (DEBUGGER>=1) fwrite($dateihandle, "cross_sell bzw similar Zuordnung eingetragen -> ");
						$query="REPLACE INTO s_articles_similar (articleID,relatedarticle) VALUES".
							" (".$Artikel_ID.", ".$XSell_Artikel_ID.")";
						dmc_sql_query($query);

					} else { // upsell
						if (DEBUGGER>=1) fwrite($dateihandle, "upsell bzw related Zuordnung eingetragen -> ");
						$query="REPLACE INTO s_articles_relationships (articleID,relatedarticle) VALUES".
							" (".$Artikel_ID.", ".$XSell_Artikel_ID.")";
						dmc_sql_query($query);
					}
				} else {	
					if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false)  	
						$cmd = "select products_id, products_id_cross_sell from ".TABLE_PRODUCTS_CROSS_SELL." where " .
				 		"products_id='$Artikel_ID' and products_id_cross_sell='$XSell_Artikel_ID'";
					else
						$cmd = "select products_id, xsell_id from products_xsell where " .
				 			"products_id='$Artikel_ID' and xsell_id='$XSell_Artikel_ID'";
				 	// XSell-Zuordnungen eintragen, wenn noch nicht bereits existent
					
					$sql_query = dmc_db_query($cmd);
					// Wenn exisitiert
			    		/*if ($Ergebnis = dmc_db_fetch_array($sql_query))
		    		{
		    				if (DEBUGGER>=1) fwrite($dateihandle, "X-Sell 6 \n");
					// existente verknüpfung nicht anfassen
					//$cmd = "delete from products_xsell where " .
					//			"products_id='$Artikel_ID' and xsell_id='$XSell_Artikel_ID'";
					//			dmc_db_query($cmd);
					if (DEBUGGER>=1) fwrite($dateihandle, "X-Sell Zuordnung BEREITS eingetragen -> \n");
						
					}  else {*/
					// XSell-Zuordnung eintragen : Tabellenspalten xtCommerce: ID 	products_id 	products_xsell_grp_name_id 	xsell_id 	sort_order
					if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false)  	{
						$sql_data_array = array(	'products_id' => $Artikel_ID,
												'products_id_cross_sell' => $XSell_Artikel_ID);				      
						dmc_sql_insert_array(TABLE_PRODUCTS_CROSS_SELL, $sql_data_array);
						if (DEBUGGER>=1) fwrite($dateihandle, "X-Sell Zuordnung eingetragen. $Artikel_ID zu $XSell_Artikel_ID\n");
					} else {
						$sql_data_array = array(	'products_id' => $Artikel_ID,
											'products_xsell_grp_name_id' => '0',
											'xsell_id' => $XSell_Artikel_ID);				      
						dmc_sql_insert_array('products_xsell', $sql_data_array);
						if (DEBUGGER>=1) fwrite($dateihandle, "X-Sell Zuordnung eingetragen. $Artikel_ID zu $XSell_Artikel_ID\n");
					}
				} // end if else woo oder andere
				
				if (strpos(strtolower(SHOPSYSTEM), 'woo') !== false) {
					
					if ($Xsell_Type == 'up_sell') {
							$meta_key='_upsell_ids';
					} else { // crosssell
							$meta_key='_crosssell_ids';
					}
						
					// Pruefen, ob schon Eintraege vorhanden
					$meta_value= 'a:0:{}';
					$meta_value_temp =	dmc_sql_select_query("meta_value","postmeta","post_id='$Artikel_ID' and meta_key='$meta_key'");
					if ($meta_value_temp!="")
						$meta_value=$meta_value_temp;
				
				
						if (DEBUGGER>=1) fwrite($dateihandle, "X-Sell 136 meta_value=$meta_value\n");
					
					/* $sql_query = dmc_db_query($cmd);
					
					if ($Ergebnis = dmc_db_fetch_array($sql_query))
					{
						if (DEBUGGER>=1) fwrite($dateihandle, "X-Sell $sql_query \n");
						$meta_value=$Ergebnis['meta_value'];	// ZB a:2:{i:0;i:312402;i:1;i:312432;}
						// existente verknüpfung nicht anfassen
						//$cmd = "delete from products_xsell where " .
						//			"products_id='$Artikel_ID' and xsell_id='$XSell_Artikel_ID'";
						//			dmc_db_query($cmd);
				 		if (DEBUGGER>=1) fwrite($dateihandle, "X-Sell Zuordnung BEREITS eingetragen -> \n");
					}  else {
						if (DEBUGGER>=1) fwrite($dateihandle, "Flaute\n");
						$meta_value= 'a:0:{}';
					}
					*/
					// NUR, wenn Verknuefung noch nicht vorhanden
					if (strpos($meta_value,strval($XSell_Artikel_ID))===false) {
						fwrite($dateihandle,  "id ".$XSell_Artikel_ID." NOCH NICHT bereits in ".$meta_value);
						  $meta_value_array=unserialize($meta_value);
						  array_push($meta_value_array, $XSell_Artikel_ID);
						  $meta_value=serialize($meta_value_array);
							$query="INSERT INTO ".DB_PREFIX."postmeta (meta_value,meta_key,post_id) VALUES".
							" ('".$meta_value."', '$meta_key' ,".$Artikel_ID.")";
							// Delete first
							dmc_sql_query("DELETE FROM ".DB_PREFIX."postmeta WHERE meta_key='$meta_key' AND post_id='".$Artikel_ID."'");
							if (DEBUGGER>=1) fwrite($dateihandle, "up_sell-query: $query \n");
							dmc_sql_query($query);
						
					} else {
						fwrite($dateihandle,  "id ".$XSell_Artikel_ID." bereits in ".$meta_value);
					}
				
				} // END if woocommerce
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: X-Sell nicht eingetragen, da entweder Artikel oder XSell-Artikel nicht existent.\n");
			} //  endif Wenn Artikel und XSell Artikel existieren
						// bei woocommerce abweichend ->  array erst nach Schleife absetzen.
						
			

		} // END FOR 	
		
		
		// Rueckgabe
		echo '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
		   "<STATUS>\n" .
		   "  <STATUS_INFO>\n" .
		   "    <ACTION>$action</ACTION>\n" .
		   "    <MESSAGE>OK</MESSAGE>\n" .
		   "    <MODE>$mode</MODE>\n" .
		   "    <ID>$Artikel_ID</ID>\n" .
		   "  </STATUS_INFO>\n" .
		   "</STATUS>\n\n";
		
		return $ergebnis;	
	} // end function

	
?>
	