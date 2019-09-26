<?php
/************************************************
*                                            	*
*  dmConnector for shops						*
*  attribute.php								*
*  Funktionen fuer attribute					*
*  Copyright (C) 2011 DoubleM-GmbH.de			*
*                                               *
*************************************************/

		$Artikel_Merkmal = $_GET["Artikel_Merkmal"]; 
		$Artikel_Auspraegung = $_GET["Artikel_Auspraegung"];
		$Artikel_Price = $_GET["Artikel_Price"];
		
		$Artikel_Auspraegungen = $Artikel_Auspraegung;
 
			// Aufbau XS|S+3.5|M=24|L|XL@gruen....
			// Ermitteln erstes Vorkommnis von @
			$pos=strpos($Artikel_Auspraegung, '@');
			$groessen=substr($Artikel_Auspraegung, 0, $pos);
			
			// Zugehoerige Varianten anlegen
			$groesse=explode ('|', $groessen);
			// Wenn Groessen  vorhanden
			
			// "Leere" Attribute eleminieren
			$tempArtikel_Merkmal=explode ('@', $Artikel_Merkmal);
			$tempArtikel_Auspraegung=explode ('@', $Artikel_Auspraegung);
			echo "35 - Artikel_Merkmal (alt)=".$Artikel_Merkmal.".<br>";
			echo "36 - Artikel_Auspraegung (alt)=".$Artikel_Auspraegung."<br>";
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
					echo "* Artikel_Merkmal=".$Artikel_Merkmal."<br>";

					// Wenn mehrere Auspraegungen pro Merkmal, mehrere Artikel anlegen (z.B. XS|S+3.5|M=24|L|XL
					if (strpos($tempArtikel_Auspraegung[$i], '|') !== false) {
						$tempEinzelneAuspraegungen=explode ('|', $tempArtikel_Auspraegung[$i]);
						for ( $j = 0; $j < count ( $tempEinzelneAuspraegungen ); $j++ ) {
							for ( $k = 0; $k < count ( $Artikel_Auspraegung ); $k++ ) {
								$Artikel_Auspraegung[$j] = $Artikel_Auspraegung[$k].'@'.$tempEinzelneAuspraegungen[$j];
								echo "* tempEinzelneAuspraegungen[$j]=".$tempEinzelneAuspraegungen[$j]." -> Artikel_Auspraegung[$j]=".$Artikel_Auspraegung[$j]."<br>";
							}
						}
						$ausprID++;
					} else {
						for ( $k = 0; $k < count ( $Artikel_Auspraegung ); $k++ ) {
								$Artikel_Auspraegung[$k] = $Artikel_Auspraegung[$k].'@'.$tempArtikel_Auspraegung[$i];
								echo "* tempEinzelneAuspraegungen[$j]=".$tempEinzelneAuspraegungen[$j]." -> Artikel_Auspraegung[$j]=".$Artikel_Auspraegung[$j]."<br>";
							}
					}
					$ausprID++;
				} // end if
			} // end for
			// Merkmale und Auspraegungen loggen
			echo "*groesse Artikel_Auspraegung=".(count ($Artikel_Auspraegung))."<br>";
		
		
?>
	
	