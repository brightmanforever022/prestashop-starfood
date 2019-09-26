<?php
		
function HHGSetConfSpecials()
{
		
	global $dateihandle;
	// Aktionspreise
	if (DEBUGGER>=1) {
		$daten = "\n******************\HHGSetConfSpecials\n******************";
		$dateiname=LOG_DATEI;	
		$dateihandle = fopen($dateiname,"a");
		fwrite($dateihandle, $daten);
		fwrite($dateihandle, "\n");
	}
  
	
	$Artikel_ID = (integer)(xtc_db_prepare_input($_POST['Artikel_ID']));
	$Artikel_Artikelnr = xtc_db_prepare_input($_POST['Artikel_Artikelnr']);  
	$Aktionspreis = xtc_db_prepare_input($_POST['Aktionspreis']);
	$Artikel_Anzahl = (integer)(xtc_db_prepare_input($_POST['Artikel_Anzahl']));
	$DatumVon = xtc_db_prepare_input($_POST['DatumVon']);
	$DatumBis = xtc_db_prepare_input($_POST['DatumBis']);
	if ($DatumVon=='' || $DatumVon=='null') $DatumVon='now()';
	if ($DatumBis=='' || $DatumBis=='null') $DatumBis='2015-02-28 00:00:00.0';

	if (DEBUGGER>=1) {
	  	$daten = "Artikel-Aktionspreise $Aktionspreis zu Artikel-Nr=".$Artikel_Artikelnr." ab $DatumVon (".$_POST['DatumVon'].") bis $DatumBis\n";
		$dateiname=LOG_DATEI;
		$dateihandle = fopen($dateiname,"a");
		fwrite($dateihandle, $daten);
	}		

	
	// Voraussetzung: Artikel existiert
	//Ermitteln ob Hauptartikel oder Unterartikel und den Preis
	$sqlquery = "select products_master_model, products_price from " . TABLE_PRODUCTS . " where products_model='$Artikel_Artikelnr'";

	$artikel_query = dmc_db_query($sqlquery);
	if ($artikel = dmc_db_fetch_array($artikel_query))
	{
		$Artikel_Variante_Von=$artikel['products_master_model'];
		if ($Artikel_Variante_Von<>'') {
			$hauptartikel=false;
			// Preis des Hauptartikels ermitteln
			$sqlquery = "select products_price from " . TABLE_PRODUCTS . " where products_model='".$Artikel_Variante_Von."'";
			$artikel_query2 = dmc_db_query($sqlquery);
			if ($artikel2 = dmc_db_fetch_array($artikel_query2)) {
				$hauptpreis=$artikel2['products_price'];
				// Preisdifferenz
				$preis= $Aktionspreis - $hauptpreis;
			} else {
				$preis=0;
			}
		} else {
			$hauptartikel=true;
			$preis=$artikel['products_price'];
		}
	}
	else
	{
				// Kein Artikel vorhanden
				$preis = 0.00;
	}
	
	//(Sonder)Preis für Variante setzten, wenn kein Hauptartikel
	if (!$hauptartikel && $preis<>0) {
		// Für Var-Artikel
        if (DEBUGGER>=1) fwrite($dateihandle, "Variantenpreis Variantenartikel = ".$Artikel_Artikelnr." setzen (Differenz)= $preis\n");	
	    $sql_data_array['products_price'] = $preis;
		$mode='UPDATED';
		xtc_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_model = '$Artikel_Artikelnr'");
	} else if ($preis>0) {
		//SonderPreis für Hauptartikel setzen
		// Der Artikelnummer zugehörige products_id ermitteln
		// Überprüfen, ob Artikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
		$cmd = "select products_id from " . TABLE_PRODUCTS .
	            " where products_model = '$Artikel_Artikelnr'";
				
		$sql_query = dmc_db_query($cmd);
	     	 
	    if ($sql_result = dmc_db_fetch_array($sql_query))
	    {      
			// ArtikelID von bestehendem Artikel ermitteln
			$Artikel_ID = $sql_result['products_id'];		
			$exists = 1;	
		} //endif
		
		// Wenn Artikel  existiert
		if ($exists == 1) {		
			// Alte Zuordnungen löschen -> NUR WENN von Warenwirtschaft UND NICHT MANUELL eingetragen wurde		
			if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false)
				$cmd = "select products_id from " . TABLE_SPECIALS . " where " .
						"products_id='$Artikel_ID' and specials_date_added = '1973-02-28 05:00:00'";
			else
				$cmd = "select products_id from " . TABLE_SPECIALS . " where " .
						"products_id='$Artikel_ID' and date_status_change = '1973-02-28 05:00:00'";
					
			// Zuordnungen löschen, wenn bereits existent
			$desc_query = dmc_db_query($cmd);
			if (($desc = dmc_db_fetch_array($desc_query)))
			{
				$cmd = "delete from " . TABLE_SPECIALS . " where " .
							"products_id='$Artikel_ID'";
							dmc_db_query($cmd);
			}
						
			// Aktionspreis schreiben
			if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false) {
				$sql_data_array = array('products_id' => $Artikel_ID,
										'specials_quantity' => $Artikel_Anzahl,
										'date_status_change' => '1973-02-28 05:00:00',
										'specials_new_products_price' => $Aktionspreis,
										'expires_date' => $DatumBis,
										'status' => '1');	
				if (STORE_ID != "")
					$sql_data_array['store_id'] = STORE_ID;
				else 
					$sql_data_array['store_id'] = 1;
			} else {
				$sql_data_array = array('products_id' => $Artikel_ID,
										'specials_quantity' => $Artikel_Anzahl,
										'specials_date_added' => '1973-02-28 05:00:00',
										'specials_new_products_price' => $Aktionspreis,
										'expires_date' => $DatumBis,
										'status' => '1');
			}
										
			xtc_db_perform(TABLE_SPECIALS, $sql_data_array);
			if (DEBUGGER>=1) fwrite($dateihandle, "Aktionspreis eingetragen.\n");
		} else { 
			if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER: Aktionspreis nicht eingetragen, da Artikel nicht existent.\n");
		} //  endif Wenn Artikel  existieren
		
		// Abgelaufene Aktionspreise löschen (NUR wenn von WaWi übertragen
		$cmd = "delete from " . TABLE_SPECIALS . " where " .
							"expires_date < now() and specials_date_added = '1973-02-28 05:00:00'";
		dmc_db_query($cmd);
		
		// HHG -> Calkulationstabelle fuellen
		 if (DEBUGGER>=1) fwrite($dateihandle, " HHG -> Calkulationstabelle fuellen \n");	
		     
				// Standard products_id  store_id  multiplier  multiplier_0  multiplier_1  multiplier_2  multiplier_3 
				    $sql_data_array = array(
					          'products_id' => $Artikel_ID,
					          'multiplier' => '0.00',
							  'multiplier_1' => '0.00',
							  'multiplier_2' => '0.00',
							  'multiplier_3' => '0.00');
					
					if (STORE_ID != "")
							$sql_data_array['store_id'] = STORE_ID;
					else 
						$sql_data_array['store_id'] = 1;
							  
					if (!$exists) 
					    xtc_db_perform(TABLE_PRODUCTS_CALCULATION, $sql_data_array);
					else 
						xtc_db_perform(TABLE_PRODUCTS_CALCULATION, $sql_data_array, 'update', "products_id = '$Artikel_ID'");
		
	
	} // end if 
	
					  
}  // END FUNCTION

?>