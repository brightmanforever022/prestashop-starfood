<?php
/*******************************************************************************************
*                                                                                          									*
*  dmConnector  for magento shop												*
*  dmc_write_art.php														*
*  Artikel schreiben														*
*  Copyright (C) 2008 DoubleM-GmbH.de											*
*                                                                                          									*
*******************************************************************************************/
/*

*/

// defined( '_DMC_ACCESSIBLE' ) or die( 'Direct Access to this location is not allowed.' );

ini_set("display_errors", 1);
error_reporting(E_ALL);

	function dmc_write_art_extended_sizes() {
		
		global $dateihandle;
		if (DEBUGGER>=1) fwrite($dateihandle, "function dmc_write_art_extended_sizes\n");
		// Einzelne Artikel ermitteln und generieren
		$Artikel_Artikelnr=$_POST['Artikel_Artikelnr'];
		$Artikel_Bezeichnung = $_POST["Artikel_Bezeichnung1"];
		$Artikel_Preis=$_POST['Artikel_Preis'];
		$Artikel_Variante_Von = xtc_db_prepare_input($_POST['Artikel_Variante_Von']);			
		$Artikel_Merkmal = html_entity_decode (sonderzeichen2html(true,$_POST["Artikel_Merkmal"]), ENT_NOQUOTES);  
		$Artikel_Auspraegung = html_entity_decode (sonderzeichen2html(true,$_POST["Artikel_Auspraegung"]), ENT_NOQUOTES);
		$Artikel_Auspraegungen = $Artikel_Auspraegung;
 
		 
		// Groessen-Artikel?
		if (strpos($Artikel_Merkmal, 'Groessen') === false) {
		    // keine Groessen vorhanden
						if (DEBUGGER>=1) fwrite($dateihandle, "keine Groessen vorhanden -> Normales simple product\n");
						//	echo  "keine Groessen vorhanden -> Normales  product\n";
						WriteArtikel();
		} else {
			// Groessen ermitteln und extrahieren
			if (DEBUGGER>=1) fwrite($dateihandle, "Groessen ermitteln und extrahieren - Artikel_Artikelnr=$Artikel_Artikelnr\n");
			// Aufbau XS|S+3.5|M=24|L|XL@gruen....
			// Ermitteln erstes Vorkommnis von @
			$pos=strpos($Artikel_Auspraegung, '@');
			$groessen=substr($Artikel_Auspraegung, 0, $pos);
			if (DEBUGGER>=1) fwrite($dateihandle, "Groessen =$groessen\n");
			
			// Configurable Product anlegen
			$_POST['Artikel_Variante_Von']='';
			$_POST['Artikel_URL1']='';   // $Superattribut
			$_POST['Artikel_Merkmal']='';			
			$_POST['Artikel_Auspraegung']='';
			if (DEBUGGER>=1) fwrite($dateihandle, "Hauptartikel anlegen Artikelnummer=$Artikel_Artikelnr\n");
			WriteArtikel();
			// Zugehoerige Varianten anlegen
			$groesse=explode ('|', $groessen);
			// Wenn Groessen  vorhanden
			if (DEBUGGER>=1) fwrite($dateihandle, "groesse[0]=".$groesse[0]."\n");
			// "Leere" Attribute eleminieren
			$tempArtikel_Merkmal=explode ('@', $Artikel_Merkmal);
			$tempArtikel_Auspraegung=explode ('@', $Artikel_Auspraegung);
			if (DEBUGGER>=1) fwrite($dateihandle, "62 - Artikel_Merkmal (alt)=".$Artikel_Merkmal."\n");
			if (DEBUGGER>=1) fwrite($dateihandle, "63 - Artikel_Auspraegung (alt)=".$Artikel_Auspraegung."\n");
			$Artikel_Merkmal='';
			$Artikel_Auspraegung='';
			for ( $i = 0; $i < count ( $tempArtikel_Merkmal ); $i++ ) {
				// Wenn keine Auspraegung vorhanden, attribut nicht beachten
				if ($tempArtikel_Auspraegung[$i]<>'') {
					$ausprID=0;
					if ( $Artikel_Merkmal =='')
						$Artikel_Merkmal = $tempArtikel_Merkmal[$i];
					else 
						$Artikel_Merkmal = $Artikel_Merkmal .'@'.$tempArtikel_Merkmal[$i];
					if (DEBUGGER>=1) fwrite($dateihandle, "84-* Artikel_Merkmal=".$Artikel_Merkmal."\n");

					// Wenn mehrere Auspraegungen pro Merkmal, mehrere Artikel anlegen (z.B. XS|S+3.5|M=24|L|XL
					if (strpos($tempArtikel_Auspraegung[$i], '|') !== false) {
						$tempEinzelneAuspraegungen=explode ('|', $tempArtikel_Auspraegung[$i]);
						for ( $j = 0; $j < count ( $tempEinzelneAuspraegungen ); $j++ ) {
							for ( $k = 0; $k < count ( $Artikel_Auspraegung ); $k++ ) {
								$Artikel_Auspraegung[$j] = $Artikel_Auspraegung[$k].'@'.$tempEinzelneAuspraegungen[$j];
								if (DEBUGGER>=1) fwrite($dateihandle, "82-* tempEinzelneAuspraegungen[$j]=".$tempEinzelneAuspraegungen[$j]." -> Artikel_Auspraegung[$j]=".$Artikel_Auspraegung[$j]."\n");
							}
						}
						$ausprID++;
					} else {
						for ( $k = 0; $k < count ( $Artikel_Auspraegung ); $k++ ) {
								$Artikel_Auspraegung[$k] = $Artikel_Auspraegung[$k].'@'.$tempArtikel_Auspraegung[$i];
								if (DEBUGGER>=1) fwrite($dateihandle, "89-* tempEinzelneAuspraegungen[$j]=".$tempEinzelneAuspraegungen[$j]." -> Artikel_Auspraegung[$j]=".$Artikel_Auspraegung[$j]."\n");
							}
					}
					$ausprID++;
				} // end if
			} // end for
			// Merkmale und Auspraegungen loggen
			if (DEBUGGER>=1) fwrite($dateihandle, "96-*groesse Artikel_Auspraegung=".(count ($Artikel_Auspraegung))."\n");
			if (DEBUGGER>=1) fwrite($dateihandle, "*groesse Artikel_Auspraegung2=".(count ($Artikel_Auspraegung[2]))."\n");
			// Erstes  @ entfernen
			$Artikel_Merkmal = substr($Artikel_Merkmal,0,-1);
			// Durchlaufen nach Anzahl der unterschiedlichen Auspraegungen
			for ( $i = 0; $i < count ( $Artikel_Auspraegung ); $i++ ) {
				// Erstes  @ entfernen
				$Artikel_Auspraegung[$i] = substr($Artikel_Auspraegung[$i],0,-1);
				if (DEBUGGER>=1) fwrite($dateihandle, "104 - Artikel_Merkmal =".$Artikel_Merkmal." mit Artikel_Auspraegung$i=".$Artikel_Auspraegung[$i]."\n");
			}
			
			/*
			if (count ( $groesse )>0 && $groesse[0]<>'') {
				for ( $i = 0; $i < count ( $groesse ); $i++ ) {
					// Aufpreis ?
					if (DEBUGGER>=1) fwrite($dateihandle, "groesse[$i]=".$groesse[$i]."\n");
					if (strpos($groesse[$i], '+') !== false) {
						$pos=strpos($groesse[$i], '+');
						$aufpreis=substr($groesse[$i], $pos+1, strlen($groesse[$i]));
						if (DEBUGGER>=1) fwrite($dateihandle, "Aufpreis=$aufpreis\n");
						$Artikel_Preis += $aufpreis;
					}
			
					$Artikel_Auspraegung=str_replace($groessen, $groesse, $Artikel_Auspraegungen);
					$_POST['Artikel_Artikelnr']=$Artikel_Artikelnr;
					$_POST["Artikel_Bezeichnung1"]=$Artikel_Bezeichnung;
					$_POST['Artikel_Preis']=$Artikel_Preis;
					$_POST['Artikel_Variante_Von']=$Artikel_Artikelnr;
					$_POST['Artikel_URL1']='';   // $Superattribut
					$_POST['Artikel_Merkmal']=$Artikel_Merkmal;			
					$_POST['Artikel_Auspraegung']=$Artikel_Auspraegung;
					if (DEBUGGER>=1) fwrite($dateihandle, "Configurable Produkt anlegen Artikel_Auspraegung=$Artikel_Auspraegung, Artikel_Artikelnr=$Artikel_Artikelnr\n");
					//	echo "Simple anlegen Groesse=$groesse, Artikel_Artikelnr=$Artikel_Artikelnr\n";
					WriteArtikel();
				} // end for 
			// Wenn KEINE Groessen  vorhanden
			} else {
					$_POST['Artikel_Artikelnr']=$Artikel_Artikelnr."-1";
					$_POST["Artikel_Bezeichnung1"]=$Artikel_Bezeichnung;
					$_POST['Artikel_Preis']=$Artikel_Preis;
					$_POST['Artikel_Variante_Von']=$Artikel_Artikelnr;
					$_POST['Artikel_URL1']='';   // $Superattribut
					$_POST['Artikel_Merkmal']=$Artikel_Merkmal;			
					$_POST['Artikel_Auspraegung']=$Artikel_Auspraegung;
					if (DEBUGGER>=1) fwrite($dateihandle, "Simple Produkt anlegen mit Artikel_Merkmale=$Artikel_Merkmal,  Artikel_Auspraegung=$Artikel_Auspraegung, Artikel_Artikelnr=$Artikel_Artikelnr\n");
					//	echo "Simple anlegen Groesse=$groesse, Artikel_Artikelnr=$Artikel_Artikelnr\n";
					WriteArtikel();
			} // end if
			*/
		} // end if
	} // end function
	
?>
	
	