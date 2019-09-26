<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_gambio_filter.php													*
*  inkludiert von dmc_write_art.php 										*				
*  Hauptartikel übergebene Variablen ermitteln und als Gambio  Filter setzen*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
21.06.2012
- neu
*/
	
				if (DEBUGGER>=1) fwrite($dateihandle, "dmc_gambio_filter - gambio produkt filter  \n");
				// stad deutsch = 2
				$language_id=2;
				// Übergebene Merkmale und Ausprägungen ermitteln - werden als attribue1@attribe2@... übergeben
				$Merkmale = explode ( '@', $Artikel_Merkmal );
				$Auspraegungen = explode ( '@', $Artikel_Auspraegung );
				$Filter_Merkmale = explode ( '@', GAMBIO_FILTER_ATTRIBUTES );				// zB DEFINE( 'GAMBIO_FILTER_ATTRIBUTES','Größe@Farbe@Material' );
				$Filter_Merkmale_intern = explode ( '@', GAMBIO_FILTER_ATTRIBUTES_INTERN );	// zB DEFINE( 'GAMBIO_FILTER_ATTRIBUTES_GAMBIO','size_herren@Farbe@Material' );
				$filteranzahl=0;
						
				// Ggfls entfernen von wertlosen Attributen oder solchen, welche nicht gemappt werden sollen.
				for ( $Anz_Merkmale = 0; $Anz_Merkmale < count ( $Merkmale ); $Anz_Merkmale++ )
				{
					$key = array_search($Merkmale[$Anz_Merkmale], $Filter_Merkmale); // $key = zahl oder === false
					if (DEBUGGER>=1) fwrite($dateihandle, "dmc_gambio_filter - Merkmale[Anz_Merkmale]=".$Merkmale[$Anz_Merkmale]." \n");
					if (DEBUGGER>=1) fwrite($dateihandle, "$Filter_Merkmale STEHT AN POSITION=".$key." \n");

					if ($Auspraegungen[$Anz_Merkmale]==''  || $key === false ) {
					//	$key = array_search($Merkmale[$Anz_Merkmale], $Filter_Merkmale); 
						// elemente loeschen
					//	if (DEBUGGER>=1) fwrite($dateihandle, "LOESCHE ELEMENT=".$Merkmale[$Anz_Merkmale]." \n");
					//	unset($Merkmale[$Anz_Merkmale]);
					//	unset($Auspraegungen[$Anz_Merkmale]);
						// indexe neu aufbauen
					//	$Merkmale = array_values($Merkmale);
					//	$Auspraegungen = array_values($Auspraegungen);
					} else if ($key !== false) {	// Merkmal soll gemappt werden
						// Filter_Id dem Merkmal zuweisen, Zb Groesse zu size (Gambio Name Intern)
						$Filter_Merkmal[$filteranzahl] = $Filter_Merkmale_intern [$key];	
						$Filter_Auspraegung[$filteranzahl] = $Auspraegungen[$filteranzahl];
						// Ermitteln der ID des Filters (wenn vorhanden)
						$Filter_Merkmal_Ids[$filteranzahl] = dmc_sql_select_query('feature_id','feature_description',"language_id=$language_id AND feature_admin_name='".$Filter_Merkmal[$filteranzahl]."'"); 
						// Ermitteln der ID des Filter-Werts/Auspraegung (wenn vorhanden)
						$Filter_Auspraegung_Ids[$filteranzahl] = dmc_sql_select_query('fv.feature_value_id','feature_value AS fv, feature_value_description AS fvd ',"fv.feature_value_id=fvd.feature_value_id AND fvd.language_id=$language_id AND fvd.feature_value_text='".$Filter_Auspraegung[$filteranzahl]."' AND fv.feature_id=".$Filter_Merkmal_Ids[$filteranzahl]); 
						$filteranzahl++;
					}
				}	

				// Filter Attribute durchlaufen
				for ( $Anz_Merkmale = 0; $Anz_Merkmale < count ( $Filter_Merkmal ); $Anz_Merkmale++ )
				{
					if (DEBUGGER>=1) fwrite($dateihandle, "dmc_gambio_filter - filter Filter_Merkmal[$Anz_Merkmale]=".$Filter_Merkmal[$Anz_Merkmale]." \n");
					if (DEBUGGER>=1) fwrite($dateihandle, "dmc_gambio_filter - filter Filter_Merkmal_Ids[$Anz_Merkmale]=".$Filter_Merkmal_Ids[$Anz_Merkmale]." \n");
					if (DEBUGGER>=1) fwrite($dateihandle, "dmc_gambio_filter - filter Filter_Auspraegung[$Anz_Merkmale]=".$Filter_Auspraegung[$Anz_Merkmale]." \n");
					if (DEBUGGER>=1) fwrite($dateihandle, "dmc_gambio_filter - filter Filter_Auspraegung_Ids[$Anz_Merkmale]=".$Filter_Auspraegung_Ids[$Anz_Merkmale]." \n");
					
					// Wenn Filter nicht vorhanden, anlegen
					if ($Filter_Merkmal_Ids[$Anz_Merkmale] == '' ) {
						// ID in table feature ergaenzen
						$Filter_Merkmal_Ids[$Anz_Merkmale]=dmc_get_highest_id('feature_id','feature') + 1;
						dmc_sql_insert('feature', 'feature_id',$Filter_Merkmal_Ids[$Anz_Merkmale]);
						// bezeichnung in table feature_description
						$values=$Filter_Merkmal_Ids[$Anz_Merkmale].", $language_id, '".$Filter_Merkmal[$Anz_Merkmale]."', '".$Filter_Merkmal[$Anz_Merkmale]."'";
						dmc_sql_insert('feature_description', 'feature_id, language_id,feature_name,feature_admin_name', $values);
					}
					
					// Wenn Filter-Wert/Auspraegung nicht vorhanden, anlegen
					if ($Filter_Auspraegung_Ids[$Anz_Merkmale] == '' ) {
						// ID in table feature ergaenzen
						$Filter_Auspraegung_Ids[$Anz_Merkmale]=dmc_get_highest_id('feature_value_id','feature_value_description') + 1;
						$values="$Filter_Auspraegung_Ids[$Anz_Merkmale],$Filter_Merkmal_Ids[$Anz_Merkmale],0";
						dmc_sql_insert('feature_value', 'feature_value_id,feature_id,sort_order', $values);
						//bezeichnung in table feature_description 
						$values=$Filter_Auspraegung_Ids[$Anz_Merkmale].", $language_id, '".$Filter_Auspraegung[$Anz_Merkmale]."'";
						dmc_sql_insert('feature_value_description', 'feature_value_id, language_id, feature_value_text', $values);
					}
					
				}
		
?>
	