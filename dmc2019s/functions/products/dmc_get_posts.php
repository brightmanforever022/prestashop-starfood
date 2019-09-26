<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for alls shop												*
*  dmc_get_posts.php														*
*  inkludiert von dmc_write_art.php 										*								
*  Artikel übergebene Variablen ermitteln									*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
12.03.2012
- neu
*/
 
    // TRIM (fuer e3000)
	foreach ($_POST as $Key => $Value)
	{
		$_POST[$Key]=trim($Value);
	}
	// Post ermitteln
	$ExportModus = sonderzeichen2html(true,$_POST['ExportModus']);	// wird nicht mehr verwendet
	$Artikel_ID = sonderzeichen2html(true,$_POST['Artikel_ID']);	// wird nicht mehr verwendet
 	$Kategorie_ID = sonderzeichen2html(true,$_POST['Artikel_Kategorie_ID']);
	
	// HQS - spezielle Erkennung, ZB 010001010³Zubehör^Ständer^Notenständerµ
	if (strpos($Kategorie_ID,'³')!== false || strpos($Kategorie_ID,'^')!== false) {
		fwrite($dateihandle, "HQS Kategorieerkennung $Kategorie_ID \n");
		$beginn_cat=strpos($Kategorie_ID,'³');
		$ende_cat=strpos($Kategorie_ID,'µ');
		$laenge=$ende_cat-$beginn_cat-2;
		fwrite($dateihandle, "ende_cat -> ".$ende_cat."\n");
		$Kategorie_ID2=substr($Kategorie_ID,$ende_cat+1);
		$Kategorie_ID=str_replace('³','',$Kategorie_ID);
		$Kategorie_ID=str_replace('µ','',$Kategorie_ID);
		$Kategorie_ID=substr($Kategorie_ID, $beginn_cat, $laenge);
		$Kategorie_ID=str_replace('^','___',$Kategorie_ID);						// Trennzeichen --- muss URL Konform sein
		$Kategorie_ID=dmc_convert_umlaute($Kategorie_ID);
		$Kategorie_ID=str_replace(' ','_',$Kategorie_ID);
		$Kategorie_ID=str_replace(',','',$Kategorie_ID);			
		$Kategorie_ID=str_replace('--','-_',$Kategorie_ID);
		fwrite($dateihandle, "Kategorien beginnen an Stelle $beginn_cat -> ".$Kategorie_ID."\n");
		fwrite($dateihandle, "Rest -> ".$Kategorie_ID2."\n");
		if (strlen($Kategorie_ID2)>10) {
			fwrite($dateihandle, "Zweite Kategorie -> ".$Kategorie_ID2."\n");
			$beginn_cat=strpos($Kategorie_ID2,'³');
			$ende_cat=strpos($Kategorie_ID2,'µ');
			$laenge=$ende_cat-$beginn_cat-2;
			$rest=substr($Kategorie_ID2,$ende_cat+1);
			$Kategorie_ID2=str_replace('³','',$Kategorie_ID2);
			$Kategorie_ID2=str_replace('µ','',$Kategorie_ID2);
			$Kategorie_ID2=substr($Kategorie_ID2, $beginn_cat, $laenge);
			$Kategorie_ID2=str_replace('^','___',$Kategorie_ID2);						// Trennzeichen --- muss URL Konform sein
			$Kategorie_ID2=dmc_convert_umlaute($Kategorie_ID2);
			$Kategorie_ID2=str_replace(' ','_',$Kategorie_ID2);	
			$Kategorie_ID2=str_replace(',','',$Kategorie_ID2);	
			$Kategorie_ID2=str_replace('--','-_',$Kategorie_ID2);
			fwrite($dateihandle, "Zweite Kategorie -> ".$Kategorie_ID2."\n");
			$Kategorie_ID=$Kategorie_ID.'@'.$Kategorie_ID2;
		}
		
	}
	if (substr($Kategorie_ID,0,1)=="@") $Kategorie_ID = substr($Kategorie_ID,1,strlen($Kategorie_ID));
	if (substr($Kategorie_ID,-1)=="@") $Kategorie_ID = substr($Kategorie_ID,0,-1);
	
	$Hersteller_ID = sonderzeichen2html(true,$_POST['Hersteller_ID']);
	if (preg_match('/@/', $Hersteller_ID)) {
		// list ($Artikel_Artikelnr, $Artikel_EAN) = split ("@", $Artikel_Artikelnr);
		$werte = explode ( '@', $Hersteller_ID);
		$Hersteller_ID = $werte[0];
		$Artikel_Herstellernummer = $werte[1];
			if (DEBUGGER>=1) fwrite($dateihandle, "Artikel_Herstellernummer extrahiert ".$Artikel_Herstellernummer);
	} // endif herstellerid und herstellernummer
  
	// Bei Virtuemart den Varianten keinen Hersteller oder Kategroie zuweisen.
	if (strpos(SHOPSYSTEM, 'virtuemart') !== false && $Artikel_Variante_Von != '') {
		$Kategorie_ID = sonderzeichen2html(true,$_POST['Artikel_Kategorie_ID']);
		$Hersteller_ID = sonderzeichen2html(true,$_POST['Hersteller_ID']);
	}
	$Artikel_Artikelnr = sonderzeichen2html(true,$_POST['Artikel_Artikelnr']);
	// Abfangroutine Shopware
	$Artikel_Artikelnr = trim(str_replace("/", "_", $_POST['Artikel_Artikelnr']));
	
	$Artikel_Menge = sonderzeichen2html(true,$_POST['Artikel_Menge']);
	if (!is_numeric($Artikel_Menge)) $Artikel_Menge =0;
	$Artikel_Menge=round($Artikel_Menge);
	//$Artikel_Menge = trim(str_replace('0E-10', "0", $_POST['Artikel_Menge']));
	if ($Artikel_Menge <0) $Artikel_Menge =0;
	$Artikel_Preis = trim(str_replace(',', ".", $_POST['Artikel_Preis']));
	$Artikel_Preis1 = trim(str_replace(",", ".", $_POST['Artikel_Preis1']));
	// Shopware muesste Preis für Kundengruppe H auch vorhanden sein.
	if ($Artikel_Preis1 == '')
		$Artikel_Preis1 = $Artikel_Preis;
	$Artikel_Preis2 = trim(str_replace(",", ".", $_POST['Artikel_Preis2']));
	$Artikel_Preis3 = trim(str_replace(",", ".", $_POST['Artikel_Preis3']));
	$Artikel_Preis4 = trim(str_replace(",", ".", $_POST['Artikel_Preis4']));
	$Artikel_Preis = str_replace('0E-20', "0.00", $Artikel_Preis);
	$Artikel_Preis1 = str_replace('0E-20', "0.00", $Artikel_Preis1);
	$Artikel_Preis2 = str_replace('0E-20', "0.00", $Artikel_Preis2);
	$Artikel_Preis3 = str_replace('0E-20', "0.00", $Artikel_Preis3);
	$Artikel_Preis4 = str_replace('0E-20', "0.00", $Artikel_Preis4);
	
	$Artikel_Gewicht = sonderzeichen2html(true,$_POST['Artikel_Gewicht']);
	$Artikel_Status = sonderzeichen2html(true,$_POST['Artikel_Status']);
	if ($Artikel_Status=="4")
		$Artikel_Status="1";
	$Artikel_Steuersatz = sonderzeichen2html(true,$_POST['Artikel_Steuersatz']);
	$Artikel_Bilddatei = sonderzeichen2html(true,$_POST['Artikel_Bilddatei']);
	// HQS - Trennzeichen :
	$Artikel_Bilddatei = str_replace(':','@',$Artikel_Bilddatei);				
	// Wenn Bilddatei Verzeichnis enthaelt und keine URL ist, dann das Bild separieren
	//if (strpos($Artikel_Bilddatei, "\\") !== false) {
	//	$Artikel_Bilddatei=substr($Artikel_Bilddatei,(strrpos($Artikel_Bilddatei,"\\")+1),254); 
	//} else if (strpos($Artikel_Bilddatei, "/") !== false && strpos($Artikel_Bilddatei, "http") === false ) {
	//	$Artikel_Bilddatei=substr($Artikel_Bilddatei,(strrpos($Artikel_Bilddatei,"/")+1),254); 
	//} 
	$Artikel_VPE = sonderzeichen2html(true,$_POST['Artikel_VPE']);
	$Artikel_Lieferstatus = sonderzeichen2html(true,$_POST['Artikel_Lieferstatus']);
	// Gambio GX
	/*if ($Artikel_Menge <1) $Artikel_Lieferstatus =5;	// Auf Anfrage
	else $Artikel_Lieferstatus=4;	// Auf Lager
	*/
	//$Artikel_Startseite = sonderzeichen2html(true,$_POST['Artikel_Startseite']);
	// Statt Artikelstartseite können bei Shopware übergeben werden durch @ getrennte Atrributewerte. d.h. 5@xsxsx@73423
	// wuerde attr1 bis attr3 mit den o.g. Werten fuellen
	$Artikel_Attr_Werte_123 = sonderzeichen2html(true,$_POST['Artikel_Startseite']);
	
	// Funktion fuer Multishop Presta
	// $Artikel_Presta_Multishop_iD = sonderzeichen2html(true,$_POST['Artikel_Startseite']);
	
	$SkipImages = sonderzeichen2html(true,$_POST['SkipImages']);
	$Aktiv = ABS($_POST['Aktiv']);
	$Aenderungsdatum = sonderzeichen2html(true,$_POST['Aenderungsdatum']);
	$Artikel_Variante_Von = sonderzeichen2html(true,$_POST['Artikel_Variante_Von']);	
	// Abfangroutine Shopware
	$Artikel_Variante_Von = trim(str_replace("/", "_", $_POST['Artikel_Variante_Von']));
	
	$Artikel_Merkmal = html_entity_decode (sonderzeichen2html(true,$_POST["Artikel_Merkmal"]), ENT_NOQUOTES);  
	$Artikel_Auspraegung =  sonderzeichen2html(true,$_POST["Artikel_Auspraegung"]);
	
	// Sonderzeichen beücksichtigen
    $Artikel_Bezeichnung = html_entity_decode (sonderzeichen2html(true,$_POST["Artikel_Bezeichnung1"]), ENT_NOQUOTES);
    $Artikel_Langtext = sonderzeichen2html(true,$_POST["Artikel_Text1"]);
    $Artikel_Kurztext = html_entity_decode (sonderzeichen2html(true,$_POST["Artikel_Kurztext1"]), ENT_NOQUOTES);
    $Artikel_Sprache = sonderzeichen2html(true,$_POST["Artikel_TextLanguage1"]);
	if ($Artikel_Sprache == '0') $Artikel_Sprache='1';
	
	$Artikel_MetaText = html_entity_decode (sonderzeichen2html(true,$_POST["Artikel_MetaTitle1"]), ENT_NOQUOTES); 
    $Artikel_MetaDescription = html_entity_decode (sonderzeichen2html(true,$_POST["Artikel_MetaDescription1"]), ENT_NOQUOTES); 
    $Artikel_MetaKeyword = html_entity_decode (sonderzeichen2html(true,$_POST["Artikel_MetaKeywords1"]), ENT_NOQUOTES);
    $Artikel_MetaUrl = sonderzeichen2html(true,$_POST["Artikel_URL1"]);	
	
	// Shopware Pflichtfeld
	$Sortierung=0;
	
?> 
	