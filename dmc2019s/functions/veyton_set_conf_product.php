<?php

	// Variantenhauptprodukt bekomment Variantenpreis, wenn Preis = 0
	dmc_main_price_not_0($Artikel_Variante_Von_id, $Artikel_Preis);
				
	$no_of_languages=2;
	
	$dateihandle = fopen(LOG_DATEI,"a");
	fwrite($dateihandle, "*** set_configurable_product ".$Artikel_Merkmal."\n");
				
	$Merkmale = explode ( '@', $Artikel_Merkmal);
	// Attribute anlegen und ids ermitteln
	for ( $Anz_Merkmale = 0; $Anz_Merkmale < count ( $Merkmale ); $Anz_Merkmale++ )
	{ 
		if (DEBUGGER>=1) fwrite($dateihandle, "Merkmal 15\n");
		$sql_data_array = array(
		    // 'attributes_id' => 1,
		    'attributes_parent' => 0,	// 0 fuer attibute, andere Werte fuer Auspraegungen
			'attributes_model' => $Merkmale[$Anz_Merkmale],
		   // 'attributes_image' => '',
		    'sort_order' => 0,
		    'status' => 1);
	
		// Bestehende Daten laden
	    $cmd = "select pa.attributes_id from xt_plg_products_attributes AS pa INNER JOIN xt_plg_products_attributes_description AS pad ON (pa.attributes_id=pad.attributes_id) where pa.attributes_model='".$Merkmale[$Anz_Merkmale]."' OR pad.attributes_name='".$Merkmale[$Anz_Merkmale]."' limit 1";

	    $desc_query = dmc_db_query($cmd);
	    if ($desc = dmc_db_fetch_array($desc_query))
	    { 
				$attribute_id[$Anz_Merkmale] = $desc['attributes_id'];
				//  Attribute update
				if (DEBUGGER>=1) fwrite($dateihandle, "Merkmal ".$Anz_Merkmale." von ".count ( $Merkmale )." = ".$Merkmale[$Anz_Merkmale]." bereits in Datenbank, Update auf deutsche Bezeichnung.\n");
				 // Description
				$update_sql_data = array(
				'attributes_name' => $Merkmale[$Anz_Merkmale],
				'attributes_desc' => $Merkmale[$Anz_Merkmale]);
				dmc_sql_update_array('xt_plg_products_attributes_description', $update_sql_data, "attributes_id ='" . $merkmal_id[$Anz_Merkmale] . "' AND language_code = 'de'"); // deutsch
		} else { 
			//  Attribute insert
			 if (DEBUGGER>=1) fwrite($dateihandle, "Merkmal ".$Anz_Merkmale." = ".$Merkmale[$Anz_Merkmale]." in Datenbank angelegt.\n");
			 $attribute_id[$Anz_Merkmale] = dmc_db_get_new_id();
			 // Description
			  $sql_data_array = array(
				'attributes_id' => $attribute_id[$Anz_Merkmale],
				'language_code' => 'de',	// 0 fuer attibute, andere Werte fuer Auspraegungen
				'attributes_name' => $Merkmale[$Anz_Merkmale],
				'attributes_desc' => $Merkmale[$Anz_Merkmale]); 
			 dmc_sql_insert_array('xt_plg_products_attributes_description', $sql_data_array); // deutsch
			 $sql_data_array['language_code']='en';
		} 
	}
	
	// Auspreageung ermitteln und anlegen - werden als Auspreageung1@Auspreageung2@... übergeben
	$Auspraegungen = explode ( '@', $Artikel_Auspraegung);
	for ( $Anz_Merkmale = 0; $Anz_Merkmale < count ( $Auspraegungen ); $Anz_Merkmale++ )
	{
		if (DEBUGGER>=1) fwrite($dateihandle, "Auspraegung ".$Anz_Merkmale." = ".$Auspraegungen[$Anz_Merkmale]."\n");
		$sql_data_array = array(
		    // 'attributes_id' => 1,
		    'attributes_parent' => $attribute_id[$Anz_Merkmale],	// 0 fuer attibute, andere Werte fuer Auspraegungen
			'attributes_model' => $Merkmale[$Anz_Merkmale].'-'.$Auspraegungen[$Anz_Merkmale],
		   // 'attributes_image' => '',
		    'sort_order' => 0,
		    'status' => 1);
	
		$attributes_model=$Merkmale[$Anz_Merkmale].'-'.$Auspraegungen[$Anz_Merkmale];
		// Bestehende Daten laden
	    $cmd = "select attributes_id from xt_plg_products_attributes".
	            " where attributes_model='".$attributes_model."' AND attributes_parent='".$attribute_id[$Anz_Merkmale]."'";
		if (DEBUGGER>=1) fwrite($dateihandle, "Auspraegung cmd=".$cmd ."\n");
		
	    $desc_query = dmc_db_query($cmd);
	    if ($desc = dmc_db_fetch_array($desc_query))
	    { 
				$merkmal_id[$Anz_Merkmale] = $desc['attributes_id'];
				//  Attribute update
				if (DEBUGGER>=1) fwrite($dateihandle, "Auspraegung ".$Anz_Merkmale." = ".$Auspraegungen[$Anz_Merkmale]." bereits in Datenbank, Update auf deutsche BezeIchnung.\n");
				 // Description
				$update_sql_data = array(
				'attributes_name' => $Auspraegungen[$Anz_Merkmale],
				'attributes_desc' => $Auspraegungen[$Anz_Merkmale]);
				dmc_sql_update_array('xt_plg_products_attributes_description', $update_sql_data, "attributes_id ='" . $merkmal_id[$Anz_Merkmale] . "' AND language_code = 'de'");	// deutsch
				
		} else { 
			if (DEBUGGER>=1) fwrite($dateihandle, "86 anlegen\n");
			//  Attribute insert 
			/*$sql_data_array_insert = array(
		    // 'attributes_id' => 1,
		    'attributes_parent' => $attribute_id[$Anz_Merkmale],	// 0 fuer attibute, andere Werte fuer Auspraegungen
			'attributes_model' => $Auspraegungen[$Anz_Merkmale],
		   // 'attributes_image' => '',
		    'sort_order' => 0,
		    'status' => 1);*/
			// Neue ID ermitteln
			 $query = dmc_db_query("SELECT max(attributes_id)+1 AS neueid FROM xt_plg_products_attributes");
			  if ($ergebnis =  dmc_db_fetch_array($query)) {
				$merkmal_id[$Anz_Merkmale]= $ergebnis['neueid'];
			  } else {
  				$merkmal_id[$Anz_Merkmale]= 9999;
			  }
			$cmd="INSERT INTO xt_plg_products_attributes (attributes_id,attributes_parent,attributes_model,sort_order,status) VALUES (".
			$merkmal_id[$Anz_Merkmale].", ". $attribute_id[$Anz_Merkmale].",'".$attributes_model."',0,1)";
			if (DEBUGGER>=1) fwrite($dateihandle, "105 cmd= $cmd\n");
			dmc_db_query($cmd);
			if (DEBUGGER>=1) fwrite($dateihandle, "Auspraegung ".$Anz_Merkmale." = ".$Auspraegungen[$Anz_Merkmale]." in Datenbank angelegt.\n");
			if (DEBUGGER>=1) fwrite($dateihandle, "neue ID = ".$merkmal_id[$Anz_Merkmale]."\n");
			 // Description
			  $sql_data_array = array(
				'attributes_id' => $merkmal_id[$Anz_Merkmale],
				'language_code' => 'de',	// 0 fuer attibute, andere Werte fuer Auspraegungen
				'attributes_name' => $Auspraegungen[$Anz_Merkmale],
				'attributes_desc' => $Auspraegungen[$Anz_Merkmale]);
			 dmc_sql_insert_array('xt_plg_products_attributes_description', $sql_data_array); // deutsch
		     $sql_data_array['language_code']='en';
			  dmc_sql_insert_array('xt_plg_products_attributes_description', $sql_data_array); // englisch
		}		
		
		// delete first if exists
		$cmd = "select products_id FROM " . 
				"xt_plg_products_to_attributes WHERE " .
				"products_id='".$Artikel_ID."' AND attributes_id='".$merkmal_id[$Anz_Merkmale]."'; ";			
		$desc_query = dmc_db_query($cmd);
		if (($desc = dmc_db_fetch_array($desc_query)))
		{
			$cmd = "delete from xt_plg_products_to_attributes where " .
					"products_id='".$Artikel_ID."' AND attributes_id='".$merkmal_id[$Anz_Merkmale]."'; ";	
			dmc_db_query($cmd);
		}
		// Produkt mit Attribut/Merkmal verknuepfen
		$sql_data_array = array(
				'products_id' =>  $Artikel_ID,
				'attributes_id' => $merkmal_id[$Anz_Merkmale],
				'attributes_parent_id' => $attribute_id[$Anz_Merkmale]);
			dmc_sql_insert_array('xt_plg_products_to_attributes', $sql_data_array); 
			 
	} // end for
			
	//		if (DEBUGGER>=1) fwrite($dateihandle, " HHG -> Bild $bilddatei zuordnen \n");	
			
		//	include('functions/hhg_set_images.php');
?>