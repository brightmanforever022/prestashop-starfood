<?php
ini_set("display_errors", 1);
error_reporting(E_ERROR & ~E_NOTICE & ~E_DEPRECATED);

/****************************************************************************
*                                                                        	*
*  dmConnector for all shops												*
*  dmc_generate_cat_id.php													*
*  inkludiert von dmc_write_art.php 										*
*  Kategorie ID basierend auf Metas mappen									*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
12.03.2012
- neu
*/
 
		// Ueberpruefen, ob Konfigurationsdateien gesetzt
		if (!defined('GENERATE_CAT_ID' )) define('GENERATE_CAT_ID',true);	// Kategorie_ID basierend auf WaWi ID ermitteln
		if (!defined('CAT_DEVIDER' )) define('CAT_DEVIDER','@');	// Trenner für Multi - Kategorie_ID Zuordnung		

		if (GENERATE_CAT_ID && $Kategorie_ID<>"") {
			if (DEBUGGER>=50) fwrite($dateihandle, "dmc_generate_cat_id - KategorieId alt = ".$Kategorie_ID." \n");	
			// Zugehörige Kategorie über WaWi Kategorie ID aus Keyword Eintrag der Katgorien ermitteln:
			if($Kategorie_ID<>'0') {
				// Ueberpruefen auf multiple Kategorien  <Artikel_Kategorie_ID>210802,610704,710902</Artikel_Kategorie_ID>
				if (strpos($Kategorie_ID, CAT_DEVIDER) === false) {
					// Kategorie_id aus meta
					//$Kategorie_IDs[0]=dmc_get_category_id($Kategorie_ID);
					// Neu ab 10.10.2017, es koennen mehrere durch @ getrennte IDs zurueckkommen
					$anzahl=0;
					$shop_cat_id = dmc_get_category_id($Kategorie_ID);
			//		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_generate_cat_id KEIN CAT_DEVIDER - shop_cat_ids = ".$shop_cat_id." \n");
					$shop_cat_ids = explode(CAT_DEVIDER,$shop_cat_id);
					for ( $j = 0; $j < count ( $shop_cat_ids ); $j++ )
					{
						if ($shop_cat_ids[$j]<>'0' && $shop_cat_ids[$j]<>'') {
							$Kategorie_IDs[$anzahl] = $shop_cat_ids[$j];
				//			if (DEBUGGER>=50) fwrite($dateihandle, "dmc_generate_cat_id - zugewiesen Kategorie_IDs [".$anzahl."] = shop_cat_ids[$j] = ".$shop_cat_ids[$j]." \n");	
							$anzahl++;
						}
						if ($j>10)
								break;
					}
					// Standard Kategorie ID 
					if ($Kategorie_IDs[0]==-1 || $Kategorie_IDs[0]==0) {
						if (is_numeric($Kategorie_ID))
							$Kategorie_IDs[0]=$Kategorie_ID; // Standard Zahl behalten
					} else {
						$Kategorie_ID=$Kategorie_IDs[0];
					}
					$multicat=false;
				} else {
					$Kategorie_IDs = $Kategorie_IDs_ORIG = explode(CAT_DEVIDER,$Kategorie_ID);
					$Kategorie_IDs_temp = array();
					$anzahl=0;
					if (DEBUGGER>=50) fwrite($dateihandle, "Anzahl Kategorie_IDs Step 1 ".count ( $Kategorie_IDs )." \n");
					$durchlaeufe=count($Kategorie_IDs);			
					for ( $i = 0; $i < $durchlaeufe; $i++ )
					{			
				//		if (DEBUGGER>=50) fwrite($dateihandle, "Durchlauf ".$i." \n");	
						// KategorieIds ermitteln
						// Neu ab 10.10.2017, es koennen mehrere durch @ getrennte IDs zurueckkommen
						if ($Kategorie_IDs_ORIG[$i]<>"") {
							$shop_cat_id=dmc_get_category_id($Kategorie_IDs_ORIG[$i]);
					//		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_generate_cat_id - shop_cat_id = ".$shop_cat_id." \n");	
							$shop_cat_ids = explode(CAT_DEVIDER,$shop_cat_id);
							for ( $j = 0; $j < count ( $shop_cat_ids ); $j++ )
							{
								if ($shop_cat_ids[$j]<>'0' && $shop_cat_ids[$j]<>'') {
									
									// if (DEBUGGER>=50) fwrite($dateihandle, "dmc_generate_cat_id - zugewiesen Kategorie_IDs [".$anzahl."] = shop_cat_ids[$j] = ".$shop_cat_ids[$j]." \n");	
									// $Kategorie_IDs[$anzahl] = $shop_cat_ids[$j];
									if (DEBUGGER>=50) fwrite($dateihandle, "dmc_generate_cat_id - Kategorie_IDs [".$anzahl."] = shop_cat_ids[$j] = ".$shop_cat_ids[$j]." ... ");	
									if (!in_array($shop_cat_ids[$j],$Kategorie_IDs_temp)) {
										if (DEBUGGER>1)  fwrite($dateihandle, "  KategorieID an Array $Kategorie_IDs_temp anhaengen .\n");
										array_push($Kategorie_IDs_temp, $shop_cat_ids[$j]);
										$anzahl++;
									} else {
										if (DEBUGGER>1)  fwrite($dateihandle, "  KategorieID ist bereits in Array .\n");
									}
									
								}
								if ($j>10)
									break;
							}
							//$Kategorie_IDs[$i] = dmc_get_category_id($Kategorie_IDs[$i]);
						}
						if ($i>10)
									break;
					} // end for	
					$multicat=true;
					$Kategorie_IDs=$Kategorie_IDs_temp;
				}
				
			}
			// Wenn Kategorie nicht vorhanden, nehme keine Kategorie ... 
			if ($Kategorie_IDs[0] == -1) {
				// wenn numerisch, dann uebergebene Kategorie direkt verwenden
				if (!is_numeric($Kategorie_ID))
					$Kategorie_ID = '';	
				else 
					$Kategorie_IDs[0] = $Kategorie_ID;	
				$Kategorie_IDs[0] = '';	
			}
		} else if ($Kategorie_ID <> "") {	
			//***     MultiCatgegorien getrennt durch ein Komma (  ,  )  bzw CAT_DEVIDER   ***\\
			if (strpos($Kategorie_ID, CAT_DEVIDER) === false) {
				$Kategorie_IDs = explode(CAT_DEVIDER,$Kategorie_ID);
				$multicat=true;
				
			} else {
				$Kategorie_IDs[0] = $Kategorie_ID;
				$multicat=false;
			}
		} else {	// Keine Kategorie uebergeben
				$Kategorie_ID = '';	
				$Kategorie_IDs[0] = '';	
			
		}//end else
		
		// Standard aendern
		if ($Kategorie_IDs[0] <> '')
				$Kategorie_ID=$Kategorie_IDs[0];
		
		// Standard KategorieID Shopware ist die 3
		if(strpos(strtolower(SHOPSYSTEM), 'shopware') !== false && ($Kategorie_ID == '' || $Kategorie_ID == 0)) {
			$Kategorie_ID = 3;	
		} else if ($Kategorie_ID == '') {
			$Kategorie_ID = STANDARD_CAT_ID;	
		}
		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_generate_cat_id - KategorieId neu = ".$Kategorie_ID." \n");	
		if (DEBUGGER>=50 && is_array($Kategorie_IDs)) fwrite($dateihandle, "weitere KategorieIds vorhanden = ".$Kategorie_IDs[1]."...\n");
		
		// Bestehende KategorieIDs ermitteln und ergaenzen, wenn in neuen nicht zugeordnet.
		$use_existing_cat_ids=false;
		if ($use_existing_cat_ids && $art_id!="") {
			$link=dmc_db_connect();
			$query="SELECT categoryID FROM s_articles_categories WHERE articleID=".$art_id;
			if (DEBUGGER>1)  fwrite($dateihandle, "Bestehende KategorieIDs ermitteln und ergaenzen, wenn in neuen nicht zugeordnet. = ".$query." ... ");
			$sql_query = mysqli_query($link,$query);				
			while ($TEMP_ID = mysqli_fetch_assoc($sql_query)) {
		//		if (DEBUGGER>1)  fwrite($dateihandle, "  ".$TEMP_ID['categoryID']." ... ");
				if ($TEMP_ID['categoryID']!='' && $TEMP_ID['categoryID']!='null' && $TEMP_ID['categoryID']!='0') {
					if (!in_array($TEMP_ID['categoryID'], $Kategorie_IDs)) {
						if (DEBUGGER>1)  fwrite($dateihandle, " Bestehende KategroieID an Array anhaengen = ".$TEMP_ID['categoryID']." .\n");
						array_push($Kategorie_IDs, $TEMP_ID['categoryID']);
					}
				}
			}		
			if (DEBUGGER>1)  fwrite($dateihandle, " .\n ");
			dmc_db_disconnect($link);
		}
?>