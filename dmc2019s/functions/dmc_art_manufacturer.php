<?php

	function dmc_art_manufacturer($Hersteller_ID,$dateihandle){ 
	
		
		if (DEBUGGER) fwrite($dateihandle, "dmc_art_manufacturer - Hersteller ".$Hersteller_ID." ...\n"); 
		// Nur ausführen, wenn Hersteller_ID keine Zahl, d.h. EINE WIRKLICHE ID
			$cmd = "select OXID from " . TABLE_MANUFACTURERS .
			" where OXTITLE='$Hersteller_ID' OR OXTITLE_1='$Hersteller_ID' OR OXID = '$Hersteller_ID' LIMIT 1";

		    $sql_query = dmc_db_query($cmd);
			// Wenn exisitiert
		    if ($Hersteller = dmc_db_fetch_array($sql_query))
		    {
		  	// Hersteller exisitert bereits, ID aus Datenbabnk zuordnen
				$Hersteller_ID = $Hersteller['OXID'];
				if (DEBUGGER) fwrite($dateihandle, "Ermittelte HerstellerID = ".$Hersteller_ID .".\n"); 
			} else {
				// Hersteller mit neuer  ID anlegen   		
				$Hersteller_Name=$Hersteller_ID; 
				$Hersteller_ID=str_replace(' ','',$Hersteller_ID);
				// neue ID ermitteln
				$insert_sql_data = array('OXID'=> $Hersteller_ID, 
						'OXSHOPID'=>'oxbaseshop', 
						'OXACTIVE' =>1, 
						'OXTITLE' => $Hersteller_Name);
				xtc_db_perform(TABLE_MANUFACTURERS, $insert_sql_data);
				
				if (DEBUGGER) fwrite($dateihandle, "Hersteller ".$Hersteller_Name." mit id=".$Hersteller_ID." wurde angelegt "); 
				// TODO
				// Übergabe mehrerer Parameter aus WaWi, wie Link, Bild etc.
				// z.B. $Hersteller_ID = "DoubleM\www.doublem-gmbh.de\dm.jpg";                         
				// Berücksichtigung von Fremdsprachen in Tabelle manufacturers_info
				// list ($manufacturers_name, $manufacturers_link, $manufacturers_image) = split ('[\]', $Hersteller_ID);
				// echo "Name: $manufacturers_name; Link: $manufacturers_link; Bild: $manufacturers_image<br>\n";
			}
		
		 // return the manufacturer_id
		 return $Hersteller_ID;
	}// end function    dmc_art_manufacturer
	
	
?>