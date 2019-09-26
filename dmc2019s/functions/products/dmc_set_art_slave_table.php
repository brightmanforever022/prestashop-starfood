<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_set_art_slave_table.php												*
*  inkludiert von dmc_write_art.php 										*	
*  Variante für Bestellimport in extra Tabelle einfügen.		 			*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
13.03.2012
- neu
28.08.2013
- decerpated
*/
			if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_art_slave_table \n");
		
		/*	$sql_data= array(
					  'artnr' => $Artikel_Artikelnr,					// ab 1
					  'variante_von' => $Artikel_Variante_Von, 			// ab 1
					  'merkmale' => $Artikel_Merkmal, 					// ab 1
					  'auspraegungen' => $Artikel_Auspraegung, 			// ab 1
					  'preis' => $Artikel_Preis
			); */
			fwrite($dateihandle, "dmc_set_art_slave_table 24 SELECT id as total from products_dmc WHERE artnr='".$Artikel_Artikelnr."'\n");
			//  Überprüfen, ob Eintragung existent
			$id = dmc_sql_select_query("id","products_dmc","artnr='".$Artikel_Artikelnr."'");
			// Wenn noch kein Eintrag
			fwrite($dateihandle, "dmc_set_art_slave_table 29 id = $id \n");
			if ($id=='') {
				// $sql_data['id'] = $Artikel_ID;					
				fwrite($dateihandle, "dmc_set_art_slave_table 32 \n");
				dmc_sql_insert("products_dmc", " artnr, variante_von, merkmale, auspraegungen, preis", 
					"'$Artikel_Artikelnr','$Artikel_Variante_Von','$Artikel_Merkmal','$Artikel_Auspraegung','$Artikel_Preis'");
				// xtc_db_perform("products_dmc", $sql_data);
			} else {
				fwrite($dateihandle, "dmc_set_art_slave_table 35\n");
					dmc_sql_update("variante_von='$Artikel_Variante_Von', merkmale='$Artikel_Merkmal', auspraegungen='$Artikel_Auspraegung', preis='$Artikel_Preis'","artnr ='$Artikel_Artikelnr'");
					//xtc_db_perform("products_dmc", $sql_data, 'update', "artnr ='$Artikel_Artikelnr'");
			}
			fwrite($dateihandle, "dmc_set_art_slave_table 38\n");
			// END Variante für Bestellimport in extra Tabelle einfügen.
?>
	