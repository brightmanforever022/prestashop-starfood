<?php
/****************************************************************************************
*                                                                       			 	*
*  dmConnector for all shop																*
*  dmc_mappings.php																		*
*  inkludiert von dmc_write_art.php 													*				
*  Artikel übergebene Variablen ermitteln												*
*  Copyright (C) 2012 DoubleM-GmbH.de													*
*                                                                       				*
*  24.7.2013 - Erweitert um Attributs Mapping zur Aussonderung von Datenbankfeldern 	*
*****************************************************************************************/
/*
12.03.2012
- neu
*/
	
	// Letztes @ entfernen
	if (substr($Kategorie_ID, -1)=='@')
		$Kategorie_ID=substr($Kategorie_ID, 0, -1);
	// Preis auseinanderfriemeln Kabelhandel WaWis
	// Achtung: $abMenge kann auch menge und Preis enthalten, z.B. 100.00:55.99
	$pos = strpos($Artikel_Preis,':');
	if ($pos !== false) {
		$abMenge = substr($Artikel_Preis,0,$pos-1);  
		$Artikel_Preis = substr($Artikel_Preis,$pos+1);  
	}
	// ggfls Einkaufspreis
	$pos = strpos($Artikel_Preis,'@');
	if ($pos !== false) {
		$Artikel_Preis_neu = substr($Artikel_Preis,0,$pos-1);  
		$Artikel_Einkaufspreis = substr($Artikel_Preis,$pos+1); 	
		$Artikel_Preis = $Artikel_Preis_neu;
	} else {
		$Artikel_Einkaufspreis =1;
	}
	$pos = strpos($Artikel_Preis1,':');
	if ($pos !== false) {
		$abMenge = substr($Artikel_Preis1,0,$pos-1);  
		$Artikel_Preis1 = substr($Artikel_Preis1,$pos+1);  
	}
	$pos = strpos($Artikel_Preis2,':');
	if ($pos !== false) {
		$abMenge = substr($Artikel_Preis2,0,$pos-1);  
		$Artikel_Preis2 = substr($Artikel_Preis2,$pos+1);  
	}
	$pos = strpos($Artikel_Preis3,':');
	if ($pos !== false) {
		$abMenge = substr($Artikel_Preis3,0,$pos-1);  
		$Artikel_Preis3 = substr($Artikel_Preis3,$pos+1);  
	}
	$pos = strpos($Artikel_Preis4,':');
	if ($pos !== false) {
		$abMenge = substr($Artikel_Preis4,0,$pos-1);  
		$Artikel_Preis4 = substr($Artikel_Preis4,$pos+1);  
	}		

	// Texte 
	$Artikel_Bezeichnung = str_replace("'","´",$Artikel_Bezeichnung);
    $Artikel_Bezeichnung = str_replace("\\","",$Artikel_Bezeichnung);
	$Artikel_MetaText = str_replace("'","´",$Artikel_MetaText);
    $Artikel_MetaText = str_replace("\\","",$Artikel_MetaText);
	
	// PC Kaufmann Kompatibilitaet
	if (WAWI=='pck') {
		// PCK aus Kategroie ID 005.432.234 Zahl erstellen
		$Kategorie_ID = '1' . str_replace('.', '', $Kategorie_ID);
		// PCK und CSL erfordert eine Konvertierung der RTF Langtexte in HTML
		// Unterstuetzung ist nun über die Funktion sonderzeichen2html gegeben / ausgelgiedert	
	}
	
	// Magento-Kompatibilitaet
	if ($Artikel_Status==4) $Artikel_Status=1;
	
	// Leere Werte 
	if ($Aktiv=='') $Aktiv=1;
	
	// Wenn deaktiviert, dann auch nicht mehr sichtbar
	if ($Aktiv==0) $Artikel_Status=0;
	
	// Zuordung Product Template für Tennis Heine
	$OPTIONS_TEMPLATE=OPTIONS_TEMPLATE;
	// Beispiel
	if (substr($_POST["Artikel_MetaKeywords1"],0,10)== 'Tennisschl') {
			$PRODUCT_TEMPLATE='product_info_racket.html'; 
			$OPTIONS_TEMPLATE='product_options_dropdownRacket.html';	// standard= product_options_selection.html
	}	 else 
		$PRODUCT_TEMPLATE=PRODUCT_TEMPLATE; 
		
	// VPE 
	$Artikel_VPE = str_replace('meter', 'Meter', $Artikel_VPE);
	
	if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
		$Artikel_Sprache='de';
	}
	
	if (strpos(strtolower(SHOPSYSTEM), 'commerceseo') !== false) {
		$Artikel_Sprache='1';
	}
	
	// $Artikel_Artikelnr = "Art2131224@EAN1233123";
	// Artikel_Artikelnr-Wert und  Artikel_EAN  ermitteln - werden als attribue1@attribe2 übergeben
	// Überprüfen, ob eine VPE-Einheit angegeben, oder wert und Einheit
	$Artikel_EAN = "";
	if (preg_match('/@/', $Artikel_Artikelnr)) {
		// list ($Artikel_Artikelnr, $Artikel_EAN) = split ("@", $Artikel_Artikelnr);
		$werte = explode ( '@', $Artikel_Artikelnr);
		$Artikel_Artikelnr = $werte[0];
		$Artikel_EAN = $werte[1];
		if (DEBUGGER>=1) fwrite($dateihandle, "Artikel_Artikelnr extrahiert ".$Artikel_Artikelnr);
		if (DEBUGGER>=1) fwrite($dateihandle, "EAN-Nummer extrahiert ".$Artikel_EAN);
		// Neu 21072015 rcm - unterstuetzung Gambio Tabelle products_item_codes für MPN Herstellernummer ISBN Marke etc
		if (strpos(strtolower(SHOPSYSTEM), 'gambiogx') !== false) {
			$Artikel_code_mpn = $werte[2];					// Herstellernummer
			$Artikel_code_isbn = $werte[3];
			$Artikel_code_upc = $werte[4]; 
			$Artikel_code_jan = $werte[5];	
			$Artikel_google_export_condition = $werte[6];
			$Artikel_brand_name = $werte[7];				// Marke
		}
		
	} // endif artikelnummer und EAN products_ean
  
	// Überprüfen, ob eine Sortierreihenfolge angegeben
	if (preg_match('/@/', $Aktiv)) {
		//list ($Aktiv, $Sortierung) = split ("@", $Sortierung);
		$werte = explode ( '@', $Aktiv);
		$Aktiv = $werte[0];
		$Sortierung = $werte[1];
	} else {
		// Standard = keine besondere Sortierung
		$Sortierung=0;
	} // endif
	
	// Anzahl der im Shop verfuegbaren Sprachen 
	$no_of_languages=dmc_count_languages();
	//$no_of_languages=1;
	// Bilddateiname ueberpruefen und ggfls korregieren
	//$Artikel_Bilddatei=dmc_validate_image($Artikel_Bilddatei);
	
	if (strpos(strtolower(SHOPSYSTEM), 'veyton') === false 
		&& strpos(strtolower(SHOPSYSTEM), 'shopware') === false
		&& strpos(strtolower(SHOPSYSTEM), 'woo') === false
		&& strpos(strtolower(SHOPSYSTEM), 'osc') === false) {
			$Artikel_VPE=dmc_prepare_vpe($Artikel_VPE);
	} else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false ) {
		// veyton Standard Mappings
		if (strtolower($Artikel_VPE)=='kilogramm' || strtolower($Artikel_VPE)=='kg') $Artikel_VPE='9';
		else if (strtolower($Artikel_VPE)=='liter' || strtolower($Artikel_VPE)=='l') $Artikel_VPE='10';
		else if (strtolower($Artikel_VPE)=='kubikmeter' || strtolower($Artikel_VPE)=='m3') $Artikel_VPE='11';
		else if (strtolower($Artikel_VPE)=='meter' || strtolower($Artikel_VPE)=='m') $Artikel_VPE='12';
		else if (strtolower($Artikel_VPE)=='quadratmeter' || strtolower($Artikel_VPE)=='qm') $Artikel_VPE='13';
		else if (strtolower($Artikel_VPE)=='gramm' || strtolower($Artikel_VPE)=='g') $Artikel_VPE='14';
		else if (strtolower($Artikel_VPE)=='millimeter' || strtolower($Artikel_VPE)=='mm') $Artikel_VPE='15';
		else if (strtolower($Artikel_VPE)=='FL' || strtolower($Artikel_VPE)=='fl') $Artikel_VPE='40';
		else  $Artikel_VPE='39'; // Stück
	}
	
	// Von dmc_prepare vpe $Ergebnis=$Artikel_VPE_ID."@".$products_vpe_status."@".$products_vpe_value;
	// VPE Ids etc von function wieder auseinanderfriemeln  			
	if (preg_match('/@/', $Artikel_VPE)) {
		$werte = explode ( '@', $Artikel_VPE);
		$Artikel_VPE = $werte[0];
		$Artikel_VPE_ID = $werte[0];
		$Artikel_VPE_Status = $werte[1];
		$Artikel_VPE_Value =  $werte[2];
	} else {
		// Standard = kein Status etc
		$Artikel_VPE_Status = '0';
		$Artikel_VPE_Value = '1';
	} // endif
	
	// Überprüfen, ob Artikel_Startseite oder ist neu angegeben LOGIN: 0 oder 1 Artikel_Startseite bzw 1@1 Artikel_Startseite und neu 0@1 nicht Artikel_Startseite aber neu
	if (preg_match('/@/', $Artikel_Startseite)) {
		$werte = explode ( '@', $Artikel_Startseite);
		$Artikel_Startseite = $werte[0];
		$Artikel_Neu = $werte[1];	// fuer Update als   `products` . `products_date_added` 
	}  
	
	if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
		if ($Artikel_Lieferstatus==1) $Artikel_Lieferstatus="Auf Lager";
        else if ($Artikel_Lieferstatus==0) $Artikel_Lieferstatus="Nicht verfuegbar";
		if ($Artikel_VPE_Value == '0') $Artikel_VPE_Value = '1';
	}
	
	// Zahlen
	$Artikel_Menge = str_replace("0E-14","0",$Artikel_Menge);
	$Artikel_Gewicht = str_replace("0E-14","0",$Artikel_Gewicht);
	
           
	// Wenn html header exitiert, nur Body Bereich 
	$suchstring='<body>';
	$endstring='</body>';
	if (strpos(strtolower($Artikel_Langtext), $suchstring) !== false) {
		$Artikel_Langtext=substr($Artikel_Langtext, strlen($suchstring)+strpos($Artikel_Langtext, $suchstring), (strlen($Artikel_Langtext) - strpos($Artikel_Langtext, $endstring))*(-1));
	}
	if ($Artikel_Langtext=='') $Artikel_Langtext='&nbsp;';
	if ($Artikel_Kurztext=='') $Artikel_Kurztext='&nbsp;';
	
	// Attributs Mapping zur Aussonderung von Datenbankfeldern
	if ($Artikel_Merkmal != "" && $Artikel_Auspraegung != "")
	{
		// fwrite($dateihandle, "(Filter)\n");
		// ID des Attributes ermitteln // Getrennt durch @
		$Artikel_Merkmale = explode("@",$Artikel_Merkmal);
		$Artikel_Auspraegungen = explode("@",$Artikel_Auspraegung);
		$anzahlalt=count ( $Artikel_Merkmale );
		for ( $i = 0; $i < $anzahlalt; $i++ )
		{			
			// Einzelne Attribute verknüpfen, wenn Werte gesetzt
			if ($Artikel_Merkmale[$i] != "" && $Artikel_Auspraegungen[$i] != "")
			{
				
				// Merkmale ermitteln // Getrennt durch @
				// Solche, die mit xt_products_ oder products_ anfangen entsprechen den Tabellenspalten des 
				// Shops. z.b. xt_products_ fuer individualisierbar und oxarticles_color 
				// fuer die Farbunterstuetzung
				$pos = strpos($Artikel_Merkmale[$i], 'xt_products_');
				if ($pos !== false) {
					$tabellen_spalte=substr($Artikel_Merkmale[$i],12,256);
					$sql_attribute_array[$tabellen_spalte] = $Artikel_Auspraegungen[$i];
					fwrite($dateihandle, "xt_products_ Spalte $tabellen_spalte mit Wert ".$Artikel_Auspraegungen[$i]." fuellen\n");
					// Wert aus Standard Merkmal / Auspraegung loeschen
					unset($Artikel_Merkmale[$i]);
					unset($Artikel_Auspraegungen[$i]);
				} 
			} // end if
			// Merkmal und Auspaegung wieder neu aufbauen ohne Tabellenspalten
			if (count ( $Artikel_Merkmale )>0) {
				$Artikel_Merkmal = "";
				$Artikel_Auspraegung ="";
				for ( $i = 0; $i < count ( $Artikel_Merkmale ); $i++ ) {
					$Artikel_Merkmal .= $Artikel_Merkmale[$i].'@';
					$Artikel_Auspraegung .= $Artikel_Auspraegungen[$i].'@';
				}
				$Artikel_Merkmal =  substr($Artikel_Merkmal, 0, -1); 
				$Artikel_Auspraegung =  substr($Artikel_Auspraegung, 0, -1); 
			} else {
				$Artikel_Merkmale = "";
				$Artikel_Auspraegungen = "";
			}
		} // end for	
	}  // end Tabellenspalten Funktion
		
?>
	