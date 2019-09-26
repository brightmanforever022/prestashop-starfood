<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_mappings.php															*
*  inkludiert von dmc_write_cat.php 										*				
*  Variablen Mappings durchfuehren											*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
15.03.2012
- neu
*/
fwrite($dateihandle, "dmc_mappings - 15\n");
	// Bilddateiname ueberpruefen und ggfls korregieren, wooCommerce benoetigt immer dmc_art_functions
	if ($Kategorie_Bild<>'' || strpos(strtolower(SHOPSYSTEM), 'woocommerce') !== false) {	
		if (is_file('userfunctions/products/dmc_art_functions.php')) include ('userfunctions/products/dmc_art_functions.php');
		else include ('functions/products/dmc_art_functions.php');	
		if(strpos(strtolower(SHOPSYSTEM), 'woocommerce') === false) 
			$Kategorie_Bild=dmc_validate_image($Kategorie_Bild);		// Nicht fuer woocommerce
	}

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
	
	if ($Kategorie_Sortierung<>'')
		$Sortierung= $Kategorie_Sortierung;
		
	if ($Aktiv =='') $Aktiv =1;
	
	if (WAWI=='pck') {
		// PCK aus Kategire ID 005.432.234 Zahl erstellen
		$Kategorie_ID = '1' . str_replace('.', '', $Kategorie_ID);
		$Kategorie_Vater_ID = '1' . str_replace('.', '', $Kategorie_Vater_ID);
		// Wenn Hauptkategorie
		if ($Kategorie_Vater_ID == '10') 
			$Kategorie_Vater_ID ='0';
	}
	
	// GSA HauptkategorieID generieren. Aufbau parentidpath /1/12/7/ soll zu cat7
	if (substr($Kategorie_Vater_ID,0,1)=='/') {
		// Letztes / entfernen
		$Kategorie_Vater_ID = substr($Kategorie_Vater_ID,0,-1);
		// Letzte Zahl bis zu nunmehr letztem / verwenden
		$Kategorie_Vater_ID = 'cat'.substr($Kategorie_Vater_ID,strrpos($Kategorie_Vater_ID,'/')+1,strrpos($Kategorie_Vater_ID,'/')+4);
		fwrite($dateihandle, " mapping neue Vaterid = ".($Kategorie_Vater_ID)."\n");
	}

	if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
		if ($Kategorie_Sprache_Store<>'' && !is_numeric($Kategorie_Sprache_Store))
			$language_id=$Kategorie_Sprache_Store;
		else
			$language_id='de';	// deutsch
	} else {
		if ($Kategorie_Sprache_Store<>'' && is_numeric($Kategorie_Sprache_Store))
			$language_id=$Kategorie_Sprache_Store;
		else
			$language_id=2;	// deutsch
	} 
	
	//$Kategorie_Bezeichnung = str_replace('\\', '', $Kategorie_Bezeichnung);


	//if(strpos(strtolower(SHOPSYSTEM), 'woocommerce') !== false) {
		// Fuer SEO werden dmc_art_functions.php benoetigt
	//	if (is_file('userfunctions/products/dmc_art_functions.php')) include ('userfunctions/products/dmc_art_functions.php');
	//	else include ('functions/products/dmc_art_functions.php');	

		//$Kategorie_ID = dmc_prepare_seo_name($Kategorie_ID,'DE');		// SEO Feld wird mit Kategorie_ID gefuellt
		//$Kategorie_Vater_ID = dmc_prepare_seo_name($Kategorie_Vater_ID,'DE');		// SEO Feld wird mit Kategorie_ID gefuellt
		
	//}		

?>
	