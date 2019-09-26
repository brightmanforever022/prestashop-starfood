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

	function dmc_write_art_extended() {
		
		global $dateihandle;
		if (DEBUGGER>=1) fwrite($dateihandle, "function dmc_write_art_extended\n");
		// Einzelne Artikel ermitteln und generieren
		$Artikel_Artikelnr=$_POST['Artikel_Artikelnr'];
		$Artikel_Bezeichnung = $_POST["Artikel_Bezeichnung1"];
		$_POST["Artikel_Kurztext1"]= str_replace('#', '<li>', $_POST["Artikel_Kurztext1"]);
		if (DEBUGGER>=1) fwrite($dateihandle, "Artikel_Kurztext1=".$_POST["Artikel_Kurztext1"]."\n");
		
		$Artikel_Preis=$_POST['Artikel_Preis'];
		$Artikel_Variante_Von = xtc_db_prepare_input($_POST['Artikel_Variante_Von']);			
		$Artikel_Merkmal = html_entity_decode (sonderzeichen2html(true,$_POST["Artikel_Merkmal"]), ENT_NOQUOTES);  
		$_POST["Artikel_Auspraegung"]=str_replace("\n", "", $_POST["Artikel_Auspraegung"]);
		$_POST["Artikel_Auspraegung"]=str_replace("\r", "", $_POST["Artikel_Auspraegung"]);
		$Artikel_Auspraegung = html_entity_decode (sonderzeichen2html(true,$_POST["Artikel_Auspraegung"]), ENT_NOQUOTES);
		$Artikel_Auspraegung =  $_POST["Artikel_Auspraegung"];
		$Artikel_Auspraegungen = $Artikel_Auspraegung;
 		
		// echo $Artikel_Merkmal.'<br>'.$Artikel_Auspraegung.'<br>'.$Artikel_Price;
		
		$Merkmale            = explode('@',$Artikel_Merkmal);
		$Auspraegungen       = explode('@',$Artikel_Auspraegung);
		$Auspraegungen_array = array(array());
		$Ergebnisse      = array();
				
		// empty vars
		foreach($Merkmale as $val) {
			if (!empty($val)) $Merkmale_trimmed[] = trim($val);
		}
		foreach($Auspraegungen as $val) {
			if (!empty($val)) $Auspraegungen_trimmed[] = trim($val);
		}
		
		//jetzt haben wir 2-dimensional array von Auspraegungen generieren
		for($i=0; $i<sizeof($Merkmale_trimmed); $i++) {
	
			$Auspraegungen_2nd = explode('|',$Auspraegungen_trimmed[$i]);
			
			for($j=0; $j<sizeof($Auspraegungen_2nd); $j++) {
				$temp = explode(':',$Auspraegungen_2nd[$j]);
				if(isset($temp[1])) { $Auspraegungen_array[$i][$j]= $temp[0]; $price[$i][0] = $temp[0]; $price[$i][1] = $temp[1]; }
				else $Auspraegungen_array[$i][$j] = $Auspraegungen_2nd[$j];
				if (DEBUGGER>=1) fwrite($dateihandle, "56 - preis ".$i.'.'.$j.': '.$Auspraegungen_array[$i][$j]." = ".$price[$i][0]." und Preis1 ".$price[$i][1]."\n");
						
				// echo $i.'.'.$j.': '.$Auspraegungen_array[$i][$j].'<br>';
			}
					//echo $i.'.'.$Auspraegungen_array[$i].'<br>';
		}//for i
		 
		// Varianten-Artikel?
		if ($Artikel_Merkmal == '') {
		    // keine Groessen vorhanden
						if (DEBUGGER>=1) fwrite($dateihandle, "keine Varianten vorhanden -> Normales simple product\n");
						//	echo  "keine Groessen vorhanden -> Normales  product\n";
						WriteArtikel();
		} else {
			// Attribute ermitteln und extrahieren
			if (DEBUGGER>=1) fwrite($dateihandle, "Attribute ermitteln und extrahieren - Artikel_Artikelnr=$Artikel_Artikelnr\n");
			// Aufbau XS|S+3.5|M=24|L|XL@gruen....
			
			//jetzt haben wir 2-dimensional array von Auspraegungen. gehen wir weiter und machen Ergebnisse		
			$count=1;
			for($i=0; $i<sizeof($Auspraegungen_array); $i++) {
				$count*=sizeof($Auspraegungen_array[$i]);
			}
					
			$Ergebnisse      = array();
			$Price_Exception = array();
			$c = 0;
			

			for($i=0; $i<sizeof($Merkmale_trimmed); $i++) {
				$Ergebnisse=addiere($Ergebnisse,$Auspraegungen_array[$i]);
			}
			
			// Merkmale
			$Merkmale_neu=implode('@',$Merkmale_trimmed);
			// echo '$Artikel_Merkmal = '. implode('@',$Merkmale_trimmed);
			// echo '<Br><Br>';
			// Auspraegungen
			 for($i = 0; $i<sizeof($Ergebnisse); $i++) {
			//	echo '$Artikel_Auspraegung['.$i.'] = '. $Ergebnisse[$i].'<br>';
			if (DEBUGGER>=1) fwrite($dateihandle, '94-'.$Artikel_Auspraegung[$i] .' = '. $Ergebnisse[$i]."\n");
			}
			// Preise
			for($j = 0; $j<sizeof($Ergebnisse); $j++) { 
				for($i=0; $i<sizeof($Merkmale_trimmed); $i++) {	
					// Attribute
					if(isset($price[$i][0])) {
						if (DEBUGGER>=1) fwrite($dateihandle, '105  Ergebnisse = '.$Ergebnisse[$j].' <-> @'.$price[$i][0].'@'."\n");
					 	if(strstr($Ergebnisse[$j],'@'.$price[$i][0].'@')) {
							$Price_Exception[$j]=$Artikel_Preis+$price[$i][1];
							if (DEBUGGER>=1) fwrite($dateihandle, '107 Price_Exception = '.$Price_Exception[$j]."\n");
						}
						else $Price_Exception[$j]=$Artikel_Preis;
						break;
					}
					else $Price_Exception[$j]=$Artikel_Preis;			
				}
				// echo '$Artikel_Preis['.$j.'] = '.$Price_Exception[$j].'<br>';
				if (DEBUGGER>=1) fwrite($dateihandle, '116 - '.$j.' Preis ='.$Artikel_Preis .' und Price_Exception = '.$Price_Exception[$j]."\n");
			
			}
			// Merkmale = $Merkmale_neu
			// Auspraegungen= $Ergebnisse[$i]
			// Preise = $Price_Exception[$j]
			
			// Artikel anlegen
			// Hauptartikel (Configurable Product) anlegen
			$_POST['Artikel_Variante_Von']='';
			$_POST['Artikel_URL1']='';   // $Superattribut
			$_POST['Artikel_Merkmal']='';			
			$_POST['Artikel_Auspraegung']='';
			if (DEBUGGER>=1) fwrite($dateihandle, "Hauptartikel anlegen Artikelnummer=$Artikel_Artikelnr\n");
			WriteArtikel();
			// Zugehoerige Varianten anlegen
			// Durchlaufen nach Anzahl der unterschiedlichen Auspraegungen
			$Artikel_Artikelnr = $_POST['Artikel_Artikelnr'];
			for ( $i = 0; $i < count ( $Ergebnisse ); $i++ ) {
				// Erstes  @ entfernen
				// Merkmale = $Merkmale_neu
				// Auspraegungen= $Ergebnisse[$i]
				// Preise = $Price_Exception[$j]
				$_POST['Artikel_Artikelnr'] = $Artikel_Artikelnr.'_'.$i;
				$_POST['Artikel_Preis']=$Price_Exception[$i];
				$_POST['Artikel_Preis1']=$Price_Exception[$i];;
				$_POST['Artikel_Preis2']=$Price_Exception[$i];;
				$_POST['Artikel_Preis3']=$Price_Exception[$i];;
				$_POST['Artikel_Preis4']=$Price_Exception[$i];;
				$_POST['Artikel_Variante_Von']=$Artikel_Artikelnr;
				$_POST['Artikel_Merkmal']= $Merkmale_neu;			
				$_POST['Artikel_Auspraegung']=  substr($Ergebnisse[$i], 0, -1); ;
			
				if (DEBUGGER>=1) fwrite($dateihandle, "**** 134 Variante $i Nr:".$_POST['Artikel_Variante_Von']." anlegen von ".$_POST['Artikel_Artikelnr']." mit Artikel_Merkmal =".$_POST['Artikel_Merkmal']." mit Artikel_Auspraegung=".$_POST['Artikel_Auspraegung']." und Preis ".$_POST['Artikel_Preis']."\n");
				WriteArtikel(); 
			}
			
			
		} // end if
	} // end function
	
	// Spezialfunktion fuer Art Extended 
function addiere ($ErgebnisseAlt, $Auspraegungen) {
// echo "86<br>";
	$c=0;
	$durchlauf=sizeof($ErgebnisseAlt);	// 3
	if ($durchlauf==0) $durchlauf=1;
	for($k=0; $k<$durchlauf; $k++) {
	// echo "101 - durchlauf = $durchlauf<br>";
		for($i=0; $i<sizeof($Auspraegungen); $i++) {	// 2
		     $ErgebnisseNEU[$c] = $ErgebnisseAlt[$k].$Auspraegungen[$i].'@';
		// 	 echo "103 - ".$ErgebnisseNEU[$c]."<br>";
		
			 $c++;
			}
	}
	return $ErgebnisseNEU;
}
?>
	
	