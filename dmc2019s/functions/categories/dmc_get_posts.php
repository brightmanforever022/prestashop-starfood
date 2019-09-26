<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for alls shop												*
*  dmc_get_posts.php														*
*  inkludiert von dmc_write_cat.php 										*								
*  Übergebene Variablen ermitteln										*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
15.03.2012
- neu
*/
// TRIM (fuer e3000)
	foreach ($_POST as $Key => $Value)
	{
		$_POST[$Key]=trim($Value);
	}
	// Post ermitteln
	$exists = False;
	$sonderkategorie=FALSE;

	// Unterstuetzung von 01 durch erganzung von _
	$Kategorie_ID = $_POST["Artikel_Kategorie_ID"];
	
		// HQS - spezielle Erkennung, ZB 010001010³Zubehör^Ständer^Notenständerµ
	if (strpos($Kategorie_ID,'³')!== false || strpos($Kategorie_ID,'^')!== false) {
		fwrite($dateihandle, "HQS Kategorieerkennung $Kategorie_ID \n");
		$beginn_cat=strpos($Kategorie_ID,'³');
		$woo_cat_sortierung=substr($Kategorie_ID, 0, $beginn_cat);
		$ende_cat=strpos($Kategorie_ID,'µ');
		$laenge=$ende_cat-$beginn_cat-2;
		$Kategorie_ID=str_replace('³','',$Kategorie_ID);
		$Kategorie_ID=str_replace('µ','',$Kategorie_ID);
		$Kategorie_ID=substr($Kategorie_ID, $beginn_cat, $laenge);
		$Kategorie_ID=str_replace('^','___',$Kategorie_ID);						// Trennzeichen --- muss URL Konform sein
		$Kategorie_ID=str_replace(' ','_',$Kategorie_ID);	
		$Kategorie_ID=str_replace('--','-_',$Kategorie_ID);
		$Kategorie_ID=dmc_convert_umlaute($Kategorie_ID);
		fwrite($dateihandle, "Kategorien beginnen an Stelle $beginn_cat -> ".$Kategorie_ID."\n");
	}
	$Kategorie_ID = dmc_convert_umlaute($Kategorie_ID);
	$Kategorie_ID = dmc_generate_seo($Kategorie_ID);
	$Kategorie_Vater_ID = $_POST["Kategorie_Vater_ID"]; 
	$Kategorie_Vater_ID = dmc_convert_umlaute($Kategorie_Vater_ID);
	$Kategorie_Vater_ID = dmc_generate_seo($Kategorie_Vater_ID);
	// HQS - spezielle Erkennung, ZB 010001010³Zubehör^Ständer^Notenständerµ
	if (strpos($_POST["Kategorie_Name1"],'³')!== false || strpos($_POST["Kategorie_Name1"],'^')!== false) {
		fwrite($dateihandle, "HQS Kategorieerkennung 2\n");
		$Kategorie_Bezeichnung = $_POST["Kategorie_Name1"];
		fwrite($dateihandle, "HQS Kategorieerkennung Name $Kategorie_Bezeichnung \n");
		$beginn_cat=strpos($Kategorie_Bezeichnung,'³');
		$ende_cat=strpos($Kategorie_Bezeichnung,'µ');
		$laenge=$ende_cat-$beginn_cat-2;
		$Kategorie_Bezeichnung=str_replace('³','',$Kategorie_Bezeichnung);
		$Kategorie_Bezeichnung=str_replace('µ','',$Kategorie_Bezeichnung);
		$Kategorie_Bezeichnung=substr($Kategorie_Bezeichnung, $beginn_cat,  $laenge);
		$Kategorie_Bezeichnung=str_replace('^','___',$Kategorie_Bezeichnung);			// Trennzeichen --- muss URL Konform sein
		// Name hinter letztem Trennzeichen ist korrenkt
		//$Kategorie_Bezeichnung=substr($Kategorie_Bezeichnung, strrpos($Kategorie_Bezeichnung,"___")+3,  256);
		fwrite($dateihandle, "Kategorie_Bezeichnung  -> ".$Kategorie_Bezeichnung."\n");
	} else {
		$Kategorie_Bezeichnung =  $_POST["Kategorie_Name1"];
	}
	
	$Kategorie_Bezeichnung =  sonderzeichen2html(true,$Kategorie_Bezeichnung);
	
	// Gambio ab GX2 Kategorie Bezeichnung und Überschrift , getrennt durch @
	if (preg_match('/@/', $Kategorie_Bezeichnung)) {
		$werte = explode ( '@', $Kategorie_Bezeichnung);
		$Kategorie_Bezeichnung = $werte[0];
		$categories_heading_title = $werte[1];
	} else  {
		$categories_heading_title='';
	} 
		
	$Kategorie_Beschreibung = sonderzeichen2html(true,$_POST["Kategorie_Beschreibung1"]);
	$Aktiv =  $_POST["Kategorie_Aktiv"];
	if ($Aktiv =='') $Aktiv = '0';
	$Kategorie_Bild =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_Bild"]), ENT_NOQUOTES);
	$Kategorie_Sortierung =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_Sortierung"]), ENT_NOQUOTES);
	$Kategorie_MetaK =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_MetaK"]), ENT_NOQUOTES);
	// KategorieID ergaenzen
	if ($Kategorie_ID=='')
		$Kategorie_MetaK = $Kategorie_ID.', ';
	else 
		$Kategorie_MetaK = $Kategorie_ID.', '.$Kategorie_MetaK;
	
	
	$Kategorie_MetaD =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_MetaD"]), ENT_NOQUOTES);
	$Kategorie_MetaT =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_MetaT"]), ENT_NOQUOTES);
	$Kategorie_Suchbegriffe =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_Suchbegriffe"]), ENT_NOQUOTES);
	$Kategorie_SEO =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_SEO"]), ENT_NOQUOTES);
	$Kategorie_Sprache_Store =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_Sprache_Store"]), ENT_NOQUOTES);
	$KategorieFF1 =  html_entity_decode (sonderzeichen2html(true,$_POST["KategorieFF1"]), ENT_NOQUOTES);
	$KategorieFF2=  html_entity_decode (sonderzeichen2html(true,$_POST["KategorieFF2"]), ENT_NOQUOTES);
	
	
?>
	