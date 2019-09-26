<?php
/*******************************************************************************
*                                                                        		*
*  dmConnector for Presta														*
*  dmc_presta_eigenschaften.php													*
*  inkludiert von dmc_write_art.php 											*				
*  Hauptartikel übergebene Variablen ermitteln und als Presta Features setzen	*
*  Copyright (C) 2016 DoubleM-GmbH.de											*
*                                                                       		*
********************************************************************************/
/*
15.12.2015-13.01.2016
- neu
*/
	ini_set("display_errors", 1);
	error_reporting(E_ERROR);
			// Standard
			$no_of_lang = 2;		// Anzahl Sprachen
			$id_lang = 1;		// Standard ID Sprache
		
			if (DEBUGGER>=1) fwrite($dateihandle, "dmc_presta_eigenschaften - presta product features id_lang=$id_lang  no_of_lang=$no_of_lang\n");
			
			// Übergebene Merkmale und Ausprägungen ermitteln - werden als attribue1@attribe2@... übergeben
			$Merkmale = explode ( '@', $Artikel_Merkmal);
			$Auspraegungen = explode ( '@', $Artikel_Auspraegung);
			
			$anzahl_features=0;
			// Einzelne Filter durchlaufen und setzen
			// Ggfls entfernen von wertlosen Attributen oder solchen, welche nicht gemappt werden sollen.
			for ( $Anz_Merkmale = 0; $Anz_Merkmale < count ( $Merkmale ); $Anz_Merkmale++ )
			{
				if (DEBUGGER>=1) fwrite($dateihandle, "\n dmc_presta_eigenschaften - Merkmale[Anz_Merkmale]=".$Merkmale[$Anz_Merkmale]." als Hauptartikel Eigenschaften mit Wert ".$Auspraegungen[$Anz_Merkmale]." zuordnen \n");
				if ($Auspraegungen[$Anz_Merkmale]!='') {
					$FeatureName = $Merkmale[$Anz_Merkmale];
					$FeatureWert = trim($Auspraegungen[$Anz_Merkmale]);
					//language_id	 titel	 status
					fwrite($dateihandle, "Step 1 $FeatureName => $FeatureWert ... ");
					$feature_table='feature';
					$feature_table_lang='feature_lang';
					$value_table='feature_value';
					$value_table_lang='feature_value_lang';
					$feature_product_table='feature_product';
					
					// ID des FeatureNamens ermitteln aus product_filter_categories
					if (strpos($FeatureName,"Kompatib")!==false) 
						$FeatureNameID=1;
					else
						$FeatureNameID = dmc_sql_select_query('id_feature', $feature_table_lang, "id_lang=$id_lang AND name='".$FeatureName."'"); 
					
					if ($FeatureNameID=="") {
						// $anzahl_features--;		// wenn nicht vorhanden -> TODO -> anlegen
						fwrite($dateihandle, " Eigenschaft nicht in Presta vorhanden ".$FeatureName."\n");				
					} else {
						fwrite($dateihandle, "Step 3 Eigenschaften und Werte zuordnen ");
						// Preufen aus multi Werte -> 
						$trenner=";";
						$feature_werte = explode ( $trenner, $FeatureWert);
						// $anzahl_features=0;
						// Einzelne Filter durchlaufen und setzen
						// Ggfls entfernen von wertlosen Attributen oder solchen, welche nicht gemappt werden sollen.
						for ( $Anz_Feature_Werte = 0; $Anz_Feature_Werte < count ( $feature_werte ); $Anz_Feature_Werte++ )
						{
							// Feature_id des Merkmals/Eigenschaftswertes ermitteln
							$FeatureID[$anzahl_features] = dmc_sql_select_query('id_feature_value', $value_table_lang,  "id_lang=$id_lang AND value='".$feature_werte[$Anz_Feature_Werte]."'"); 
							// Wenn nicht vorhanden, Eigenschaftswert zunaechst anlegen
							if ($FeatureID[$anzahl_features] == '') {
									// Naechst hoehere Feature ID
									$FeatureID[$anzahl_features]=dmc_sql_select_query('max(id_feature_value)+1', $value_table_lang,  "1"); 
									for ( $language_id = 1; $language_id <= $no_of_lang; $language_id++ )
									{
										fwrite($dateihandle, "Step 4 Eigenschaftwert anlegen ");
										dmc_sql_insert($value_table_lang, 
											'id_feature_value, id_lang, value',
											$FeatureID[$anzahl_features].", ".$language_id.", '".$feature_werte[$Anz_Feature_Werte]."'" );
										fwrite($dateihandle, "Done ");
									}
							
							} 
							
							// Merkmal - Auspraegung ZuordnungsID ermitteln
							$value_where="id_feature=".$FeatureNameID." AND id_feature_value=".$FeatureID[$anzahl_features];  // custom=0"; // custom=1";
							$FeatureValueID[$anzahl_features] = dmc_sql_select_query('id_feature_value', $value_table,$value_where); 
							fwrite($dateihandle, "Step 5 ");
							// Wenn bereits zugeordnet, Auspraegung update
							if ($FeatureValueID[$anzahl_features] != '') {
								fwrite($dateihandle, "Step 6 Zuordnung Auspraegung zum Merkmal EXISTIER");
								$values='id_feature_value='.$FeatureNameID;
								// Update auf Eigenschaftswert
								// dmc_sql_update($value_table, $values, $value_where) ;
							} else {
								// Zuordnung Auspraegung zum Merkmal erstmalig eintragen
								fwrite($dateihandle, "Step 7 Zuordnung Auspraegung zum Merkmal ");
							//	Keine Position ab 1.7
							//  dmc_sql_insert($value_table, 
							//					'id_feature_value, id_feature, custom, position',
							//					$FeatureID[$anzahl_features].", ".$FeatureNameID.", 1,".$anzahl_features );
								dmc_sql_insert($value_table, 
												'id_feature_value, id_feature, custom',
												$FeatureID[$anzahl_features].", ".$FeatureNameID.", 1" );
								// $FeatureValueID[$anzahl_features] = dmc_sql_select_query('id_feature_value',$value_table,$value_where); 
								$FeatureValueID[$anzahl_features] = $FeatureID[$anzahl_features];
								fwrite($dateihandle, "Done ID ".$FeatureValueID[$anzahl_features]." ");
							}
							
							// Artikel - Auspraegung ZuordnungsID ermitteln
							$value_where="id_feature=".$FeatureNameID." AND id_product='".$Artikel_ID."' AND id_feature_value=".$FeatureID[$anzahl_features];
							$FeatureValueIDProduct[$anzahl_features] = dmc_sql_select_query('id_feature_value',$feature_product_table,$value_where); 
							fwrite($dateihandle, "STEP 8 ");
							// Wenn bereits zugeordnet, Auspraegung update
							if ($FeatureValueIDProduct[$anzahl_features] != '') {
								fwrite($dateihandle, "STEP 9 Product2Feature Zurordnung existiert bereits ");
								$values='id_feature_value='.$FeatureID[$anzahl_features];
								// Update auf Eigenschaftswert
								// dmc_sql_update($feature_product_table, $values, $value_where) ;
							} else {
								// Zuordnung Auspraegung zum Merkmal erstmalig eintragen
								fwrite($dateihandle, "STEP 10 Product2Feature Zurordnung... ");
								dmc_sql_insert($feature_product_table, 
												'id_feature_value, id_feature, id_product',
												$FeatureID[$anzahl_features].", ".$FeatureNameID.", ".$Artikel_ID."" );
								fwrite($dateihandle, "STEP 10 Product2Feature Zurordnung... Done");
							}
							//$FeatureWertID[$anzahl_features] = dmc_sql_select_query('id_feature_value',$feature_table_lang,"id_lang=$id_lang AND value='".$feature_werte[$Anz_Feature_Werte]."'"); 
							$anzahl_features++;
						}	// end for	
					}
				} // end if
			}	// end for	
			
		
?>
	