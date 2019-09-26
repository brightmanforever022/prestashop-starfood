<?php
/************************************************
*                                            	*
*  dmConnector  for magento shop				*
*  dmc_mapping.php								*
*  Definitionen für Mappings					*
*  Copyright (C) 2010 DoubleM-GmbH.de			*
*                                               *
*************************************************/

defined( '_DMC_ACCESSIBLE') or die( 'Direct Access to this location is not allowed.');


function dmc_map_merkmale($attribut) {

        global $dateihandle;
        if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_merkmale($attribut)\n");
        // Mapping von WaWi ID auf Magento ID

        // Standard, wenn nichts vorhanden
			if ($attribut == 'Größe') $attribut = 'size';
            else if ($attribut == 'Grösse') $attribut = 'size';
            else if ($attribut == 'Farbe') $attribut = 'color';
           
            // Hersteller
            else if ($attribut == 'Deps') $attribut = 35;
            else if ($attribut == 'BioEdge') $attribut = 38;
         

        // $MapType suberattribut
        return $attribut;
    } // end function


	function dmc_map_customer_group($id) {
		global $dateihandle;
		 if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_customer_group\n");
		
		// Mapping von WaWi ID auf Magento ID
		
		//***    Kunden   ***//
		
		switch($id) {
			case 1: { $id = 1; break; }
			case 2: { $id = 2; break; }
			case 3: { $id = 3; break; }
			case 4: { $id = 4; break; }
			case 5: { $id = 5; break; }
		}//end Switch
			
		return $id;
	}//ende function Kunden
	
	function dmc_map_manufacturer_id ($hersteller) {
		global $dateihandle;
		 if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_manufacturer_id\n");
		
		// Mapping von WaWi ID auf Magento ID
		
		//***    hersteller   ***//
		
		Switch($hersteller) {
			case 70008: { $hersteller = '129'; break; }
			case 70010: { $hersteller = '1221'; break; }
			case 70009: { $hersteller = '1220'; break; }
			case 70012: { $hersteller = '239'; break; }
			case 70013: { $hersteller = '568'; break; }	// Erima
			case 70135: { $hersteller = '360'; break; }
			case 70007: { $hersteller = '1216'; break; }
			case 70302: { $hersteller = '1219'; break; }
			case 70141: { $hersteller = '1218'; break; }
			case 70090: { $hersteller = '1217'; break; }
		}//end Switch
			
		return $hersteller;
	}//ende function hersteller
	
	function dmc_map_color_id ($Farbnummer) {
		global $dateihandle;
		 if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_color_id\n");
		
		// Mapping von WaWi ID auf Magento ID
		
		//***    Farbe   ***//
	
		Switch($Farbnummer) {
			case 1: { $Farbe = 'weiss'; break; }
			case 2: { $Farbe = 'schwarz'; break; }
			case 3: { $Farbe = 'rot'; break; }
			case 4: { $Farbe = 'blau'; break; }
			case 5: { $Farbe = 'grün'; break; }
			case 6: { $Farbe = 'gelb'; break; }
			case 7: { $Farbe = 'beige'; break; }
			case 8: { $Farbe = 'grau'; break; }
			case 9: { $Farbe = 'rosa'; break; }
			case 10: { $Farbe = 'braun'; break; }
			case 11: { $Farbe = 'silber'; break; }
			case 12: { $Farbe = 'gold'; break; }
			case 13: { $Farbe = 'orange'; break; }
			case 14: { $Farbe = 'camouflage'; break; }
			case 15: { $Farbe = 'lila'; break; }
			case 16: { $Farbe = 'gestreift'; break; }
			case 17: { $Farbe = 'meliert'; break; }
			case 18: { $Farbe = 'graumeliert'; break; }
			case 19: { $Farbe = 'hellblau'; break; }
			case 20: { $Farbe = 'dunkelblau'; break; }
			case 21: { $Farbe = 'olive'; break; }
		}//end Switch Farbe
		
		return $Farbe;
	}//ende function Farbe
	
	function dmc_map_manufacturer ($hersteller) {
	global $dateihandle;
		 if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_manufacturer\n");
		
		// Mapping von WaWi ID auf Magento ID
		
		//***    hersteller   ***//
	
		Switch($hersteller) {
			case 70008: { $hersteller = 'Hakro'; break; }
			case 70010: { $hersteller = 'Master Italia'; break; }
			case 70009: { $hersteller = 'ID Line'; break; }
			case 70012: { $hersteller = 'Switcher'; break; }
			case 70013: { $hersteller = 'Erima'; break; }
			case 70135: { $hersteller = 'BP'; break; }
			case 70007: { $hersteller = 'Continental'; break; }
			case 70302: { $hersteller = 'CG Workwear'; break; }
			case 70141: { $hersteller = 'Olymp'; break; }
			case 70090: { $hersteller = 'American Apparel'; break; }
		}//end Switch
		
		return $hersteller;
	}//ende function hersteller
	
	function dmc_map_grp($GroupID) {
		global $dateihandle;
		 if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_grp\n");
		
		// Mapping von WaWi ID auf Magento ID
		
	
		return $ergebnis;	
	}

	function dmc_map_category_id($GroupID) {
		global $dateihandle;
		 if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_category_id $GroupID\n");
		
		// Mapping von WaWi ID auf Magento ID
		//****   DAMEN   ***//
		if ($GroupID == '100000') { $prodGrp = 'Damen'; }
		if ($GroupID == '110000') { $prodGrp = 'A-Z'; }
		if ($GroupID == '110100') { $prodGrp = 'T-Shirts'; }
		if ($GroupID == '110101') { $prodGrp = 'Rundhals'; }
		if ($GroupID == '110102') { $prodGrp = 'V-Ausschnitt'; }
		if ($GroupID == '110200') { $prodGrp = 'Polos'; }
		if ($GroupID == '110201') { $prodGrp = 'Polos Kurzarm'; }
		if ($GroupID == '110202') { $prodGrp = 'Polos Langarm'; }
		if ($GroupID == '110203') { $prodGrp = 'Polos mit Brusttasche'; }
		if ($GroupID == '110300') { $prodGrp = 'Shirts'; }
		if ($GroupID == '110301') { $prodGrp = 'Shirts Ärmellos'; }
		if ($GroupID == '110302') { $prodGrp = 'Shirts Kurzarm'; }
		if ($GroupID == '110303') { $prodGrp = 'Shirts Langarm'; }
		if ($GroupID == '110400') { $prodGrp = 'Sweats & Pullover'; }
		if ($GroupID == '110401') { $prodGrp = 'Sweatshirt'; }
		if ($GroupID == '110402') { $prodGrp = 'Polosweats'; }
		if ($GroupID == '110403') { $prodGrp = 'Kapuzensweats'; }
		if ($GroupID == '110404') { $prodGrp = 'Zipsweats'; }
		if ($GroupID == '110404') { $prodGrp = 'Strickpullover'; }
		if ($GroupID == '110500') { $prodGrp = 'Blusen'; }
		if ($GroupID == '110501') { $prodGrp = 'Blusen Langarm'; }
		if ($GroupID == '110502') { $prodGrp = 'Blusen Kurzarm'; }
		if ($GroupID == '110600') { $prodGrp = 'Hosen'; }
		if ($GroupID == '110601') { $prodGrp = 'Freizeithosen'; }
		if ($GroupID == '110602') { $prodGrp = 'Sporthosen'; }
		if ($GroupID == '110603') { $prodGrp = 'Jogginghosen'; }
		if ($GroupID == '110604') { $prodGrp = '3/4 Hosen'; }
		if ($GroupID == '110605') { $prodGrp = 'Kurzehosen'; }
		if ($GroupID == '110700') { $prodGrp = 'Westen'; }
		if ($GroupID == '110701') { $prodGrp = 'Fleecewesten'; }
		if ($GroupID == '110702') { $prodGrp = 'Bodywarmer'; }
		if ($GroupID == '110703') { $prodGrp = 'Softshellwesten'; }
		if ($GroupID == '110800') { $prodGrp = 'Jacken'; }
		if ($GroupID == '110801') { $prodGrp = 'Allwetterjacken'; }
		if ($GroupID == '110802') { $prodGrp = 'Softshelljacken'; }
		if ($GroupID == '110803') { $prodGrp = 'Winterjacken'; }
		if ($GroupID == '110804') { $prodGrp = 'Funktionsjacken'; }
		if ($GroupID == '110805') { $prodGrp = 'Fleecejacken'; }
		if ($GroupID == '110806') { $prodGrp = 'Sweatjacken'; }

		//****   HERREN  ***//
		if ($GroupID == '200000') { $prodGrp = 'Herren'; }
		if ($GroupID == '210000') { $prodGrp = 'A-Z'; }
		if ($GroupID == '210100') { $prodGrp = 'T-Shirts'; }
		if ($GroupID == '210101') { $prodGrp = 'T-Shirts Rundhals'; }
		if ($GroupID == '210102') { $prodGrp = 'T-Shirts V-Ausschnitt'; }
		if ($GroupID == '210200') { $prodGrp = 'Polos'; }
		if ($GroupID == '210201') { $prodGrp = 'Polos Kurzarm'; }
		if ($GroupID == '210202') { $prodGrp = 'Polos Langarm'; }
		if ($GroupID == '210203') { $prodGrp = 'Polos mit Brusttasche'; }
		if ($GroupID == '210300') { $prodGrp = 'Shirts'; }
		if ($GroupID == '210301') { $prodGrp = 'Shirts Ärmellos'; }
		if ($GroupID == '210302') { $prodGrp = 'Shirts Kurzarm'; }
		if ($GroupID == '210303') { $prodGrp = 'Shirts Langarm'; }
		if ($GroupID == '210400') { $prodGrp = 'Sweats & Pullover'; }
		if ($GroupID == '210401') { $prodGrp = 'Sweatshirt'; }
		if ($GroupID == '210402') { $prodGrp = 'Polosweats'; }
		if ($GroupID == '210403') { $prodGrp = 'Kapuzensweats'; }
		if ($GroupID == '210404') { $prodGrp = 'Zipsweats'; }
		if ($GroupID == '210405') { $prodGrp = 'Strickpullover'; }
		if ($GroupID == '210500') { $prodGrp = 'Hemden'; }
		if ($GroupID == '210501') { $prodGrp = 'Hemden Langarm'; }
		if ($GroupID == '210502') { $prodGrp = 'Hemden Kurzarm'; }
		if ($GroupID == '210600') { $prodGrp = 'Hosen'; }
		if ($GroupID == '210601') { $prodGrp = 'Freizeithosen'; }
		if ($GroupID == '210602') { $prodGrp = 'Sporthosen'; }
		if ($GroupID == '210603') { $prodGrp = 'Jogginghosen'; }
		if ($GroupID == '210604') { $prodGrp = 'Kurzehosen'; }
		if ($GroupID == '210700') { $prodGrp = 'Westen'; }
		if ($GroupID == '210701') { $prodGrp = 'Fleecewesten'; }
		if ($GroupID == '210702') { $prodGrp = 'Bodywarmer'; }
		if ($GroupID == '210703') { $prodGrp = 'Softshellwesten'; }
		if ($GroupID == '210800') { $prodGrp = 'Jacken'; }
		if ($GroupID == '210801') { $prodGrp = 'Allwetterjacken'; }
		if ($GroupID == '210802') { $prodGrp = 'Softshelljacken'; }
		if ($GroupID == '210803') { $prodGrp = 'Winterjacken'; }
		if ($GroupID == '210804') { $prodGrp = 'Funktionsjacken'; }
		if ($GroupID == '210805') { $prodGrp = 'Fleecejacken'; }
		if ($GroupID == '210806') { $prodGrp = 'Sweatjacken'; }

		//***    KINDER   ***//
		if ($GroupID == '300000') { $prodGrp = 'Kinder'; }
		if ($GroupID == '310000') { $prodGrp = 'A-Z'; }
		if ($GroupID == '310100') { $prodGrp = 'T-Shirts'; }
		if ($GroupID == '310101') { $prodGrp = 'T-Shirts Rundhals'; }
		if ($GroupID == '310102') { $prodGrp = 'T-Shirts V-Ausschnitt'; }
		if ($GroupID == '310200') { $prodGrp = 'Polos'; }
		if ($GroupID == '310201') { $prodGrp = 'Polos Kurzarm'; }
		if ($GroupID == '310202') { $prodGrp = 'Polos Langarm'; }
		if ($GroupID == '310203') { $prodGrp = 'Polos mit Brusttasche'; }
		if ($GroupID == '310300') { $prodGrp = 'Shirts'; }
		if ($GroupID == '310301') { $prodGrp = 'Shirts Ärmellos'; }
		if ($GroupID == '310302') { $prodGrp = 'Shirts Kurzarm'; }
		if ($GroupID == '310303') { $prodGrp = 'Shirts Langarm'; }
		if ($GroupID == '310400') { $prodGrp = 'Sweats & Pullover'; }
		if ($GroupID == '310401') { $prodGrp = 'Sweatshirt'; }
		if ($GroupID == '310402') { $prodGrp = 'Polosweats'; }
		if ($GroupID == '310403') { $prodGrp = 'Kapuzensweats'; }
		if ($GroupID == '310404') { $prodGrp = 'Zipsweats'; }
		if ($GroupID == '310405') { $prodGrp = 'Strickpullover'; }
		if ($GroupID == '310500') { $prodGrp = 'Hosen'; }
		if ($GroupID == '310501') { $prodGrp = 'Freizeithosen'; }
		if ($GroupID == '310502') { $prodGrp = 'Sporthosen'; }
		if ($GroupID == '310503') { $prodGrp = 'Jogginghosen'; }
		if ($GroupID == '310504') { $prodGrp = 'Kurzehosen'; }
		if ($GroupID == '310600') { $prodGrp = 'Westen'; }
		if ($GroupID == '310601') { $prodGrp = 'Fleecewesten'; }
		if ($GroupID == '310602') { $prodGrp = 'Bodywarmer'; }
		if ($GroupID == '310603') { $prodGrp = 'Softshellwesten'; }
		if ($GroupID == '310700') { $prodGrp = 'Jacken'; }
		if ($GroupID == '310701') { $prodGrp = 'Allwetterjacken'; }
		if ($GroupID == '310702') { $prodGrp = 'Softshelljacken'; }
		if ($GroupID == '310703') { $prodGrp = 'Winterjacken'; }
		if ($GroupID == '310704') { $prodGrp = 'Funktionsjacken'; }
		if ($GroupID == '310705') { $prodGrp = 'Fleecejacken'; }
		if ($GroupID == '310706') { $prodGrp = 'Sweatjacken'; }
		
			//***    Caps   ***//
		if ($GroupID == '400000') { $prodGrp = 'Caps'; }
		if ($GroupID == '410000') { $prodGrp = 'A-Z'; }
		if ($GroupID == '410100') { $prodGrp = 'Caps'; }
		if ($GroupID == '410200') { $prodGrp = 'Mützen'; }
		if ($GroupID == '410300') { $prodGrp = 'Hüte'; }
		if ($GroupID == '410400') { $prodGrp = 'Stirnbänder'; }
		if ($GroupID == '410500') { $prodGrp = 'Bandanas'; }
		if ($GroupID == '410600') { $prodGrp = 'Sonnenblende'; }

		//***   ACCESSOIRES   ***//
		if ($GroupID == '500000') { $prodGrp = 'Accessoires'; }
		if ($GroupID == '510000') { $prodGrp = 'A-Z'; }
		if ($GroupID == '510100') { $prodGrp = 'Krawatten'; }
		if ($GroupID == '510200') { $prodGrp = 'Tücher'; }
		if ($GroupID == '510300') { $prodGrp = 'Frottee'; }
		if ($GroupID == '510400') { $prodGrp = 'Taschen'; }
		if ($GroupID == '510500') { $prodGrp = 'Schals'; }

		//***   SPORT   ***//
		if ($GroupID == ' 600000') { $prodGrp = 'Sport'; }
		if ($GroupID == ' 610000') { $prodGrp = 'A-Z'; }
		if ($GroupID == ' 610100') { $prodGrp = 'Funktions T-Shirts'; }
		if ($GroupID == ' 610101') { $prodGrp = 'T-Shirts Rundhals'; }
		if ($GroupID == ' 610102') { $prodGrp = 'T-Shirts V-Ausschnitt'; }
		if ($GroupID == ' 610200') { $prodGrp = 'Funktionspolos'; }
		if ($GroupID == ' 610201') { $prodGrp = 'Polos Kurzarm'; }
		if ($GroupID == ' 610202') { $prodGrp = 'Polos Langarm'; }
		if ($GroupID == ' 610300') { $prodGrp = 'Funktionsshirts'; }
		if ($GroupID == ' 610301') { $prodGrp = 'Shirts Ärmellos'; }
		if ($GroupID == ' 610302') { $prodGrp = 'Shirts Langarm'; }
		if ($GroupID == ' 610303') { $prodGrp = 'Shirts Kurzarm'; }
		if ($GroupID == ' 610400') { $prodGrp = 'Trikots'; }
		if ($GroupID == ' 610401') { $prodGrp = 'Trikots Kurzarm'; }
		if ($GroupID == ' 610402') { $prodGrp = 'Trikots Langarm'; }
		if ($GroupID == ' 610403') { $prodGrp = 'Torwarttrikots'; }
		if ($GroupID == ' 610500') { $prodGrp = 'Sporthosen'; }
		if ($GroupID == ' 610501') { $prodGrp = 'Trainingshosen'; }
		if ($GroupID == ' 610502') { $prodGrp = 'Lauftights'; }
		if ($GroupID == ' 610503') { $prodGrp = 'Jogginghosen'; }
		if ($GroupID == ' 610504') { $prodGrp = 'Shorts'; }
		if ($GroupID == ' 610600') { $prodGrp = 'Trainingsanzüge'; }
		if ($GroupID == ' 610601') { $prodGrp = 'Polyesteranzüge'; }
		if ($GroupID == ' 610602') { $prodGrp = 'Präsentationsanzüge'; }
		if ($GroupID == ' 610700') { $prodGrp = 'Jacken & Westen'; }
		if ($GroupID == ' 610701') { $prodGrp = 'Trainingsjacken'; }
		if ($GroupID == ' 610702') { $prodGrp = 'Allwetterjacken'; }
		if ($GroupID == ' 610703') { $prodGrp = 'Regenjacken'; }
		if ($GroupID == ' 610704') { $prodGrp = 'Funktionsjacken'; }
		if ($GroupID == ' 610705') { $prodGrp = 'Softshelljacken'; }
		if ($GroupID == ' 610706') { $prodGrp = 'Winterjacken'; }
		if ($GroupID == ' 610707') { $prodGrp = 'Westen'; }
		if ($GroupID == ' 610800') { $prodGrp = 'Sporttaschen'; }
		if ($GroupID == ' 610900') { $prodGrp = 'Accessoires'; }
		if ($GroupID == ' 610901') { $prodGrp = 'Handschuhe'; }
		if ($GroupID == ' 610902') { $prodGrp = 'Schienbeinschoner'; }
		if ($GroupID == ' 610903') { $prodGrp = 'Socken'; }
		if ($GroupID == ' 610904') { $prodGrp = 'Bänder'; }
		if ($GroupID == ' 610905') { $prodGrp = 'Stutzen'; }
		if ($GroupID == ' 611000') { $prodGrp = 'Röcke & Kleider'; }
		if ($GroupID == ' 620000') { $prodGrp = 'Sportart'; }
		if ($GroupID == ' 620100') { $prodGrp = 'Teamsport'; }
		if ($GroupID == ' 620101') { $prodGrp = 'T-Shirts'; }
		if ($GroupID == ' 620102') { $prodGrp = 'Polos'; }
		if ($GroupID == ' 620103') { $prodGrp = 'Shirts'; }
		if ($GroupID == ' 620104') { $prodGrp = 'Trikots'; }
		if ($GroupID == ' 620105') { $prodGrp = 'Sporthosen'; }
		if ($GroupID == ' 620106') { $prodGrp = 'Shorts'; }
		if ($GroupID == ' 620107') { $prodGrp = 'Trainingsanzüge'; }
		if ($GroupID == ' 620108') { $prodGrp = 'Jacken'; }
		if ($GroupID == ' 620109') { $prodGrp = 'Sporttaschen'; }

		//***   BERUF  ***//
		if ($GroupID == ' 700000') { $prodGrp = 'Beruf'; }
		if ($GroupID == ' 710000') { $prodGrp = 'A-Z'; }
		if ($GroupID == ' 710100') { $prodGrp = 'T-Shirts'; }
		if ($GroupID == ' 710101') { $prodGrp = 'T-Shirts Rundhals'; }
		if ($GroupID == ' 710101') { $prodGrp = 'T-Shirts V-Ausschnitt'; }
		if ($GroupID == ' 710200') { $prodGrp = 'Polos'; }
		if ($GroupID == ' 710201') { $prodGrp = 'Polos Kurzarm'; }
		if ($GroupID == ' 710202') { $prodGrp = 'Polos Langarm'; }
		if ($GroupID == ' 710203') { $prodGrp = 'Polos mit Brusttasche'; }
		if ($GroupID == ' 710300') { $prodGrp = 'Shirts'; }
		if ($GroupID == ' 710301') { $prodGrp = 'Shirts Ärmellos'; }
		if ($GroupID == ' 710302') { $prodGrp = 'Shirts Kurzarm'; }
		if ($GroupID == ' 710303') { $prodGrp = 'Shirts Langarm'; }
		if ($GroupID == ' 710400') { $prodGrp = 'Sweats & Pullover'; }
		if ($GroupID == ' 710401') { $prodGrp = 'Sweatshirt'; }
		if ($GroupID == ' 710402') { $prodGrp = 'Polosweats'; }
		if ($GroupID == ' 710403') { $prodGrp = 'Kapuzensweats'; }
		if ($GroupID == ' 710404') { $prodGrp = 'Zipsweats'; }
		if ($GroupID == ' 710405') { $prodGrp = 'Strickpullover'; }
		if ($GroupID == ' 710500') { $prodGrp = 'Blusen'; }
		if ($GroupID == ' 710501') { $prodGrp = 'Blusen Langarm'; }
		if ($GroupID == ' 710502') { $prodGrp = 'Blusen Kurzarm'; }
		if ($GroupID == ' 710600') { $prodGrp = 'Hemden'; }
		if ($GroupID == ' 710601') { $prodGrp = 'Hemden Langarm'; }
		if ($GroupID == ' 710602') { $prodGrp = 'Hemden Kurzarm'; }
		if ($GroupID == ' 710700') { $prodGrp = 'Hosen'; }
		if ($GroupID == ' 710701') { $prodGrp = 'Signalhosen'; }
		if ($GroupID == ' 710702') { $prodGrp = 'Regenhosen'; }
		if ($GroupID == ' 710703') { $prodGrp = 'Bundhosen'; }
		if ($GroupID == ' 710704') { $prodGrp = 'Latzhosen'; }
		if ($GroupID == ' 710705') { $prodGrp = 'Faserpelzhosen'; }
		if ($GroupID == ' 710706') { $prodGrp = '3/4 Hosen'; }
		if ($GroupID == ' 710707') { $prodGrp = 'Kurzehosen'; }
		if ($GroupID == ' 710708') { $prodGrp = 'Gefütterte Hosen'; }
		if ($GroupID == ' 710800') { $prodGrp = 'Westen'; }
		if ($GroupID == ' 710801') { $prodGrp = 'Fleecewesten'; }
		if ($GroupID == ' 710802') { $prodGrp = 'Bodywarmer'; }
		if ($GroupID == ' 710803') { $prodGrp = 'Winterwesten'; }
		if ($GroupID == ' 710900') { $prodGrp = 'Jacken'; }
		if ($GroupID == ' 710901') { $prodGrp = 'Allwetterjacken'; }
		if ($GroupID == ' 710902') { $prodGrp = 'Softshelljacken'; }
		if ($GroupID == ' 710903') { $prodGrp = 'Winterjacken'; }
		if ($GroupID == ' 710904') { $prodGrp = 'Signaljacken'; }
		if ($GroupID == ' 710905') { $prodGrp = 'Fleecejacken'; }
		if ($GroupID == ' 710906') { $prodGrp = 'Faserpelzjacken'; }
		if ($GroupID == ' 710907') { $prodGrp = 'Parkas'; }
		if ($GroupID == ' 710908') { $prodGrp = 'Bundjacken'; }
		if ($GroupID == ' 710909') { $prodGrp = 'Kochjacken'; }
		if ($GroupID == ' 711100') { $prodGrp = 'Schürzen'; }
		if ($GroupID == ' 711101') { $prodGrp = 'Latzschürzen'; }
		if ($GroupID == ' 711102') { $prodGrp = 'Bistroschürzen'; }
		if ($GroupID == ' 711103') { $prodGrp = 'Vorbinder'; }
		if ($GroupID == ' 711200') { $prodGrp = 'Kasacks'; }
		if ($GroupID == ' 711300') { $prodGrp = 'OP-Bekleidung'; }
		if ($GroupID == ' 711400') { $prodGrp = 'Mäntel'; }
		if ($GroupID == ' 711401') { $prodGrp = 'Ärztmäntel'; }
		if ($GroupID == ' 711402') { $prodGrp = 'Arbeitsmäntel'; }
		if ($GroupID == ' 711500') { $prodGrp = 'Overalls'; }
		if ($GroupID == ' 711600') { $prodGrp = 'Zubehör'; }
		if ($GroupID == ' 720000') { $prodGrp = 'Branchen'; }
		if ($GroupID == ' 720100') { $prodGrp = 'Medizin & Gesundheit'; }
		if ($GroupID == ' 720101') { $prodGrp = 'Shirts'; }
		if ($GroupID == ' 720102') { $prodGrp = 'Polos'; }
		if ($GroupID == ' 720103') { $prodGrp = 'Sweats & Pullover'; }
		if ($GroupID == ' 720104') { $prodGrp = 'Hosen'; }
		if ($GroupID == ' 720105') { $prodGrp = 'Blusen'; }
		if ($GroupID == ' 720106') { $prodGrp = 'Hemden'; }
		if ($GroupID == ' 720107') { $prodGrp = 'Kasacks'; }
		if ($GroupID == ' 720108') { $prodGrp = 'OP-Bekleidung'; }
		if ($GroupID == ' 720109') { $prodGrp = 'Mäntel'; }
		if ($GroupID == ' 720110') { $prodGrp = 'Jacken'; }
		if ($GroupID == ' 720200') { $prodGrp = 'Handwerk'; }
		if ($GroupID == ' 720201') { $prodGrp = 'Shirts'; }
		if ($GroupID == ' 720202') { $prodGrp = 'Polos'; }
		if ($GroupID == ' 720203') { $prodGrp = 'Sweats & Pullover'; }
		if ($GroupID == ' 720204') { $prodGrp = 'Hosen'; }
		if ($GroupID == ' 720205') { $prodGrp = 'Westen '; }
		if ($GroupID == ' 720206') { $prodGrp = 'Jacken'; }
		if ($GroupID == ' 720207') { $prodGrp = 'Mäntel'; }
		if ($GroupID == ' 720300') { $prodGrp = 'Bau'; }
		if ($GroupID == ' 720301') { $prodGrp = 'Shirts'; }
		if ($GroupID == ' 720302') { $prodGrp = 'Polos'; }
		if ($GroupID == ' 720303') { $prodGrp = 'Sweats & Pullover'; }
		if ($GroupID == ' 720304') { $prodGrp = 'Hosen'; }
		if ($GroupID == ' 720305') { $prodGrp = 'Jacken & Westen'; }
		if ($GroupID == ' 720306') { $prodGrp = 'Overalls'; }
		if ($GroupID == ' 720400') { $prodGrp = 'Industrie'; }
		if ($GroupID == ' 720401') { $prodGrp = 'Shirts'; }
		if ($GroupID == ' 720402') { $prodGrp = 'Polos'; }
		if ($GroupID == ' 720403') { $prodGrp = 'Sweats & Pullover'; }
		if ($GroupID == ' 720404') { $prodGrp = 'Hosen'; }
		if ($GroupID == ' 720405') { $prodGrp = 'Jacken & Westen'; }
		if ($GroupID == ' 720406') { $prodGrp = 'Overalls'; }
		if ($GroupID == ' 720407') { $prodGrp = 'Mäntel'; }
		if ($GroupID == ' 720500') { $prodGrp = 'Gastronomie'; }
		if ($GroupID == ' 720501') { $prodGrp = 'Shirts'; }
		if ($GroupID == ' 720502') { $prodGrp = 'Polos'; }
		if ($GroupID == ' 720503') { $prodGrp = 'Sweats & Pullover'; }
		if ($GroupID == ' 720504') { $prodGrp = 'Blusen'; }
		if ($GroupID == ' 720505') { $prodGrp = 'Hemden'; }
		if ($GroupID == ' 720506') { $prodGrp = 'Hosen'; }
		if ($GroupID == ' 720507') { $prodGrp = 'Kochjacken'; }
		if ($GroupID == ' 720508') { $prodGrp = 'Westen'; }
		if ($GroupID == ' 720509') { $prodGrp = 'Schürzen'; }
		if ($GroupID == ' 720510') { $prodGrp = 'Kochmützen'; }
		if ($GroupID == ' 720511') { $prodGrp = 'Halstücher'; }
		if ($GroupID == ' 720600') { $prodGrp = 'Dienstbekleidung'; }
		if ($GroupID == ' 720601') { $prodGrp = 'Shirts'; }
		if ($GroupID == ' 720602') { $prodGrp = 'Polos'; }
		if ($GroupID == ' 720603') { $prodGrp = 'Sweats & Pullover'; }
		if ($GroupID == ' 720604') { $prodGrp = 'Blusen'; }
		if ($GroupID == ' 720605') { $prodGrp = 'Hemden'; }
		if ($GroupID == ' 720606') { $prodGrp = 'Hosen'; }
		if ($GroupID == ' 720607') { $prodGrp = 'Jacken & Westen'; }
		if ($GroupID == ' 720608') { $prodGrp = 'Krawatten'; }
		if ($GroupID == ' 720700') { $prodGrp = 'Forst.- Landwirtschaft'; }
		if ($GroupID == ' 720701') { $prodGrp = 'Shirts'; }
		if ($GroupID == ' 720702') { $prodGrp = 'Polos'; }
		if ($GroupID == ' 720703') { $prodGrp = 'Sweats & Pullover'; }
		if ($GroupID == ' 720704') { $prodGrp = 'Hosen'; }
		if ($GroupID == ' 720705') { $prodGrp = 'Jacken & Westen'; }
		if ($GroupID == ' 720706') { $prodGrp = 'Overalls'; }
		if ($GroupID == ' 720707') { $prodGrp = 'Mäntel'; }
	
		if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_category_id result = $prodGrp\n");
		
		return $prodGrp;	 
	} // end function dmc_map_customer_group

	function dmc_map_color($Hersteller, $Farbnummer, $MapType) {
		
		global $dateihandle;
		
		if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_color $Hersteller, $Farbnummer, $MapType\n");
		// $MapType is color or colorcode
		// Mapping von WaWi ID auf Magento ID
		
		// Standard, wenn nichts vorhanden
		$Farbe = 'unbekannt'; $IntFarb = '99'; $FarbCode = '#000000'; 
		
		
		/**  $Hersteller == 'Olymp'**/
		if ($Hersteller == 'Olymp' && $Farbnummer == '00') { $Farbe = 'weiss'; $IntFarb = '1'; $FarbCode = '#FFFFFF'; }
			else if ($Hersteller == 'Olymp' && $Farbnummer == '11') { $Farbe = 'hellblau'; $IntFarb = '19'; $FarbCode = '#99CCFF'; }
			else if ($Hersteller == 'Olymp' && $Farbnummer == '15') { $Farbe = 'dunkelblau'; $IntFarb = '20'; $FarbCode = '#66CCFF'; }
			else if ($Hersteller == 'Olymp' && $Farbnummer == '19') { $Farbe = 'royalblau'; $IntFarb = '4'; $FarbCode = '#003399'; }
			else if ($Hersteller == 'Olymp' && $Farbnummer == '62') { $Farbe = 'dunkelgrau'; $IntFarb = '8'; $FarbCode = '#999999'; }
			else if ($Hersteller == 'Olymp' && $Farbnummer == '63') { $Farbe = 'grau uni/chambray'; $IntFarb = '8'; $FarbCode = '#CCCCCC'; }
			else if ($Hersteller == 'Olymp' && $Farbnummer == '67') { $Farbe = 'anthrazit'; $IntFarb = '8'; $FarbCode = '#333333'; }
			else if ($Hersteller == 'Olymp' && $Farbnummer == '68') { $Farbe = 'schwarz'; $IntFarb = '2'; $FarbCode = '#000000'; }
		else if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_color Farbeeinstellung für Olymp stimmen nicht mit der Mapping ueberein!");

		
		/**  $Hersteller == 'Continental'**/
		if ($Hersteller == 'Continental' && $Farbnummer == '1') { $Farbe = 'white'; $IntFarb = '1'; $FarbCode = '#FFFFFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '2') { $Farbe = 'light lemon '; $IntFarb = '6'; $FarbCode = '#FFFF99'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '3') { $Farbe = 'gold'; $IntFarb = '12'; $FarbCode = '#FFCC00'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '4') { $Farbe = 'burnt orange'; $IntFarb = '13'; $FarbCode = '#FF6633'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '5') { $Farbe = 'red'; $IntFarb = '3'; $FarbCode = '#FF0033'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '6') { $Farbe = 'stereo red'; $IntFarb = '3'; $FarbCode = '#993300'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '7') { $Farbe = 'kelly green'; $IntFarb = '5'; $FarbCode = '#339933'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '8') { $Farbe = 'black'; $IntFarb = '2'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '9') { $Farbe = 'sport grey'; $IntFarb = '8'; $FarbCode = '#CCCCCC'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '10') { $Farbe = 'charcoal grey'; $IntFarb = '8'; $FarbCode = '#333300'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '11') { $Farbe = 'olive green'; $IntFarb = '21'; $FarbCode = '#333300'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '12') { $Farbe = 'army green'; $IntFarb = '5'; $FarbCode = '#666633'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '13') { $Farbe = 'chestnut'; $IntFarb = '10'; $FarbCode = '#663333'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '14') { $Farbe = 'dark brown'; $IntFarb = '10'; $FarbCode = '#660000'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '15') { $Farbe = 'ice blue'; $IntFarb = '19'; $FarbCode = '#99CCCC'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '16') { $Farbe = 'light blue'; $IntFarb = '19'; $FarbCode = '#99CCFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '17') { $Farbe = 'denim blue'; $IntFarb = '4'; $FarbCode = '#006699'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '18') { $Farbe = 'light navy'; $IntFarb = '4'; $FarbCode = '#000066'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '19') { $Farbe = 'navy blue'; $IntFarb = '20'; $FarbCode = '#330033'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '20') { $Farbe = 'cocoa'; $IntFarb = '10'; $FarbCode = '#993333'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '21') { $Farbe = 'bitter chocolate'; $IntFarb = '10'; $FarbCode = '#330000'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '22') { $Farbe = 'baby pink'; $IntFarb = '9'; $FarbCode = '#FFCCFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '23') { $Farbe = 'grapefruit'; $IntFarb = '9'; $FarbCode = '#FF9966'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '24') { $Farbe = 'rasperry'; $IntFarb = '9'; $FarbCode = '#CC3366'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '25') { $Farbe = 'cherry'; $IntFarb = '3'; $FarbCode = '#993333'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '26') { $Farbe = 'lilac'; $IntFarb = '15'; $FarbCode = '#CCCCFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '27') { $Farbe = 'aqua'; $IntFarb = '19'; $FarbCode = '#00CCFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '28') { $Farbe = 'indigo'; $IntFarb = '4'; $FarbCode = '#003366'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '29') { $Farbe = 'brazilian yellow'; $IntFarb = '6'; $FarbCode = '#FFFF00'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '30') { $Farbe = 'spearmint'; $IntFarb = '19'; $FarbCode = '#99FFFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '31') { $Farbe = 'tropical green'; $IntFarb = '5'; $FarbCode = '#00CC66'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '32') { $Farbe = 'pastel vanilla'; $IntFarb = '1'; $FarbCode = '#FFFFCC'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '33') { $Farbe = 'pastel pink'; $IntFarb = '9'; $FarbCode = '#FFCCFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '34') { $Farbe = 'pastel green'; $IntFarb = '5'; $FarbCode = '#CCFFCC'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '35') { $Farbe = 'pastel blue'; $IntFarb = '19'; $FarbCode = '#99CCFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '36') { $Farbe = 'pastel lilac'; $IntFarb = '15'; $FarbCode = '#CCCCFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '37') { $Farbe = 'dark pink'; $IntFarb = '9'; $FarbCode = '#CC0066'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '38') { $Farbe = 'mustard'; $IntFarb = '6'; $FarbCode = '#FF9900'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '39') { $Farbe = 'crimson red'; $IntFarb = '3'; $FarbCode = '#FF6666'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '40') { $Farbe = 'plum'; $IntFarb = '15'; $FarbCode = '#993366'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '41') { $Farbe = 'electric blue'; $IntFarb = '4'; $FarbCode = '#3366CC'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '42') { $Farbe = 'graphite'; $IntFarb = '8'; $FarbCode = '#333333'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '43') { $Farbe = 'yellow'; $IntFarb = '6'; $FarbCode = '#FFFF00'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '44') { $Farbe = 'orange'; $IntFarb = '13'; $FarbCode = '#FF6600'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '45') { $Farbe = 'aubergine'; $IntFarb = '15'; $FarbCode = '#660033'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '46') { $Farbe = 'hot pink'; $IntFarb = '9'; $FarbCode = '#FF0099'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '47') { $Farbe = 'hawai blue'; $IntFarb = '4'; $FarbCode = '#0099CC'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '48') { $Farbe = 'sky blue'; $IntFarb = '19'; $FarbCode = '#99CCFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '49') { $Farbe = 'royal blue'; $IntFarb = '4'; $FarbCode = '#003399'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '50') { $Farbe = 'grass green'; $IntFarb = '5'; $FarbCode = '#009933'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '51') { $Farbe = 'mens pink'; $IntFarb = '9'; $FarbCode = '#FF3366'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '52') { $Farbe = 'marshmallow'; $IntFarb = '9'; $FarbCode = '#FFCCCC'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '53') { $Farbe = 'charcoal '; $IntFarb = '8'; $FarbCode = '#333333'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '54') { $Farbe = 'peacock'; $IntFarb = '4'; $FarbCode = '#009999'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '55') { $Farbe = 'melange black'; $IntFarb = '17'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '56') { $Farbe = 'melange grey'; $IntFarb = '18'; $FarbCode = '#999999'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '57') { $Farbe = 'melange red'; $IntFarb = '17'; $FarbCode = '#FF0033'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '58') { $Farbe = 'melange brown'; $IntFarb = '17'; $FarbCode = '#996600'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '59') { $Farbe = 'melange hot pink '; $IntFarb = '17'; $FarbCode = '#CC0066'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '60') { $Farbe = 'melange sky'; $IntFarb = '17'; $FarbCode = '#99CCFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '61') { $Farbe = 'melange charcoal'; $IntFarb = '17'; $FarbCode = '#333333'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '62') { $Farbe = 'melange green'; $IntFarb = '17'; $FarbCode = '#669966'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '63') { $Farbe = 'spring green'; $IntFarb = '5'; $FarbCode = '#99CC66'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '64') { $Farbe = 'vanilla'; $IntFarb = '1'; $FarbCode = '#FFFFFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '65') { $Farbe = 'lipstick red'; $IntFarb = '3'; $FarbCode = '#CC3300'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '67') { $Farbe = 'red-white'; $IntFarb = '3'; $FarbCode = '#FF0033,#FFFFFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '68') { $Farbe = 'moss'; $IntFarb = '5'; $FarbCode = '#006600'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '69') { $Farbe = 'leaf green'; $IntFarb = '5'; $FarbCode = '#669933'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '70') { $Farbe = 'organic white'; $IntFarb = '1'; $FarbCode = '#FFFFFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '71') { $Farbe = 'organic black'; $IntFarb = '2'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '72') { $Farbe = 'organic light blue'; $IntFarb = '19'; $FarbCode = '#99CCFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '73') { $Farbe = 'organic red'; $IntFarb = '3'; $FarbCode = '#FF0033'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '74') { $Farbe = 'organic baby pink'; $IntFarb = '9'; $FarbCode = '#FFCCFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '75') { $Farbe = 'moss green'; $IntFarb = '4'; $FarbCode = '#006600'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '76') { $Farbe = 'light heather'; $IntFarb = '8'; $FarbCode = '#999999'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '77') { $Farbe = 'dark violet'; $IntFarb = '15'; $FarbCode = '#663399'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '78') { $Farbe = 'dark heather'; $IntFarb = '8'; $FarbCode = '#333333'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '79') { $Farbe = 'pale yellow'; $IntFarb = '6'; $FarbCode = '#FFFFCC'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '80') { $Farbe = 'washed black'; $IntFarb = '1'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '81') { $Farbe = 'washed brown'; $IntFarb = '10'; $FarbCode = '#663300'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '82') { $Farbe = 'washed navy blue'; $IntFarb = '20'; $FarbCode = '#333366'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '83') { $Farbe = 'washed moss'; $IntFarb = '5'; $FarbCode = '#006600'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '84') { $Farbe = 'washed hawai blue'; $IntFarb = '4'; $FarbCode = '#0099CC'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '85') { $Farbe = 'washed kelly green'; $IntFarb = '5'; $FarbCode = '#00CC99'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '86') { $Farbe = 'washed yellow'; $IntFarb = '6'; $FarbCode = '#FFFFCC'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '87') { $Farbe = 'washed pink'; $IntFarb = '9'; $FarbCode = '#FFCCCC'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '88') { $Farbe = 'washed green'; $IntFarb = '5'; $FarbCode = '#006633'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '89') { $Farbe = 'pigment dyed black'; $IntFarb = '2'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '90') { $Farbe = 'vintage white'; $IntFarb = '1'; $FarbCode = '#FFFFFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '91') { $Farbe = 'vintage black'; $IntFarb = '2'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '92') { $Farbe = 'vintage pale pink '; $IntFarb = '9'; $FarbCode = '#FFCCFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '93') { $Farbe = 'vintage cherry'; $IntFarb = '3'; $FarbCode = '#CC3333'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '94') { $Farbe = 'vintage plum'; $IntFarb = '15'; $FarbCode = '#660066'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '95') { $Farbe = 'vintage lemon'; $IntFarb = '6'; $FarbCode = '#FFFF99'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '96') { $Farbe = 'vintage gras'; $IntFarb = '5'; $FarbCode = '#669933'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '97') { $Farbe = 'vintage navy'; $IntFarb = '20'; $FarbCode = '#003366'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '98') { $Farbe = 'vintage cocoa'; $IntFarb = '10'; $FarbCode = '#993300'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '99') { $Farbe = 'vintage hot pink'; $IntFarb = '9'; $FarbCode = '#FF6699'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '100') { $Farbe = 'vintage brown'; $IntFarb = '10'; $FarbCode = '#663300'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '101') { $Farbe = 'vintage bottle'; $IntFarb = '5'; $FarbCode = '#006666'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '102') { $Farbe = 'vintage denim'; $IntFarb = '4'; $FarbCode = '#003366'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '103') { $Farbe = 'vintage pink'; $IntFarb = '9'; $FarbCode = '#FF6699'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '104') { $Farbe = 'vintage red'; $IntFarb = '3'; $FarbCode = '#FF6666'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '105') { $Farbe = 'vintage burgundy'; $IntFarb = '15'; $FarbCode = '#990066'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '106') { $Farbe = 'vintage graphit'; $IntFarb = '8'; $FarbCode = '#006666'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '107') { $Farbe = 'vintage mud brown'; $IntFarb = '10'; $FarbCode = '#660000'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '108') { $Farbe = 'vintage mustard'; $IntFarb = '6'; $FarbCode = '#FF9900'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '109') { $Farbe = 'melange grey-black'; $IntFarb = '17'; $FarbCode = '#999999,#000000'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '110') { $Farbe = 'black-white'; $IntFarb = '2'; $FarbCode = '#000000,#FFFFFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '111') { $Farbe = 'red-black'; $IntFarb = '3'; $FarbCode = '#FF0033,#000000'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '112') { $Farbe = 'hot pink-black'; $IntFarb = '9'; $FarbCode = '#FF0099,#000000'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '113') { $Farbe = 'green-black'; $IntFarb = '5'; $FarbCode = '#339933,#000000'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '114') { $Farbe = 'fluorescent yellow'; $IntFarb = '6'; $FarbCode = '#FFFF33'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '115') { $Farbe = 'fluorescent orange'; $IntFarb = '13'; $FarbCode = '#FF9933'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '116') { $Farbe = 'fluorescent lime'; $IntFarb = '6'; $FarbCode = '#CCFF00'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '117') { $Farbe = 'fluorescent pink'; $IntFarb = '9'; $FarbCode = '#FF66FF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '118') { $Farbe = 'white-red'; $IntFarb = '16'; $FarbCode = '#FFFFFF,#FF0033'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '119') { $Farbe = 'dove-ash'; $IntFarb = '16'; $FarbCode = '#CCCCCC,#666666'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '120') { $Farbe = 'marshmallow-cocoa'; $IntFarb = '16'; $FarbCode = '#FFCCCC,#993333'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '121') { $Farbe = 'teal-bluebell'; $IntFarb = '16'; $FarbCode = '#CCCCFF,#6699CC'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '122') { $Farbe = 'brunout white'; $IntFarb = '17'; $FarbCode = '#FFFFFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '123') { $Farbe = 'brunout black'; $IntFarb = '17'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '124') { $Farbe = 'brunout brown'; $IntFarb = '17'; $FarbCode = '#663300'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '125') { $Farbe = 'brunout charcoal'; $IntFarb = '17'; $FarbCode = '#333333'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '126') { $Farbe = 'brunout pink'; $IntFarb = '17'; $FarbCode = '#FF6699'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '127') { $Farbe = 'brunout spring green'; $IntFarb = '17'; $FarbCode = '#99CC66'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '128') { $Farbe = 'brunout sky blue'; $IntFarb = '17'; $FarbCode = '#99CCFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '129') { $Farbe = 'khaki green'; $IntFarb = '21'; $FarbCode = '#333300'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '130') { $Farbe = 'soft blue'; $IntFarb = '19'; $FarbCode = '#99CCFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '131') { $Farbe = 'powder pink'; $IntFarb = '9'; $FarbCode = '#FFCCFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '132') { $Farbe = 'ecru'; $IntFarb = '1'; $FarbCode = '#FFFFCC'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '133') { $Farbe = 'candy pink'; $IntFarb = '9'; $FarbCode = '#FFCCCC'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '134') { $Farbe = 'white-black'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#000000'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '135') { $Farbe = 'white-khaki green'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#333300'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '136') { $Farbe = 'white-soft blue'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#99CCFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '137') { $Farbe = 'white-powder pink'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#FFCCFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '138') { $Farbe = 'dark grey'; $IntFarb = '8'; $FarbCode = '#666666'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '139') { $Farbe = 'light grey'; $IntFarb = '8'; $FarbCode = '#999999'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '140') { $Farbe = 'bright blue'; $IntFarb = '4'; $FarbCode = '#336699'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '141') { $Farbe = 'light green'; $IntFarb = '5'; $FarbCode = '#99CC33'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '142') { $Farbe = 'dark red'; $IntFarb = '3'; $FarbCode = '#CC0033'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '143') { $Farbe = 'brown'; $IntFarb = '10'; $FarbCode = '#663300'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '144') { $Farbe = 'pink'; $IntFarb = '9'; $FarbCode = '#FF6699'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '145') { $Farbe = 'purple'; $IntFarb = '15'; $FarbCode = '#CC6699'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '146') { $Farbe = 'natural'; $IntFarb = '1'; $FarbCode = '#FFFFFF'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '147') { $Farbe = 'white-gold'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#FFCC00'; }
			else if ($Hersteller == 'Continental' && $Farbnummer == '148') { $Farbe = 'black-gold'; $IntFarb = '2'; $FarbCode = '#000000,#FFCC00'; }
		else if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_color Farbeeinstellung für Continental stimmen nicht mit der Mapping ueberein!");
		
		/**  $Hersteller == 'BP'**/
		if ($Hersteller == 'BP' && $Farbnummer == '10') { $Farbe = 'dunkelblau'; $IntFarb = '20'; $FarbCode = '#000066'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '11') { $Farbe = 'hellblau'; $IntFarb = '19'; $FarbCode = '#6699FF'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '110') { $Farbe = 'nachtblau'; $IntFarb = '20'; $FarbCode = '#000033'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '113') { $Farbe = 'königsblau/dunkelblau'; $IntFarb = '4'; $FarbCode = '#333366,#000066'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '13') { $Farbe = 'königsblau'; $IntFarb = '4'; $FarbCode = '#0033FF'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '144') { $Farbe = 'sand/dunkelblau'; $IntFarb = '10'; $FarbCode = '#996633,#000066'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '153') { $Farbe = 'dunkelgrau/dunkelblau'; $IntFarb = '8'; $FarbCode = '#666666,#000066'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '16') { $Farbe = 'stahlblau/schwarz'; $IntFarb = ''; $FarbCode = '#336699,#000000'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '18') { $Farbe = 'blau/weiß'; $IntFarb = '4'; $FarbCode = '#6666CC,#FFFFFF'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '2') { $Farbe = 'denim'; $IntFarb = '4'; $FarbCode = '#3366CC'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '20') { $Farbe = 'blau/weiß'; $IntFarb = '4'; $FarbCode = '#6666CC,#FFFFFF'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '21') { $Farbe = 'weiß'; $IntFarb = '1'; $FarbCode = '#FFFFFF'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '2113') { $Farbe = 'weiß/königsblau'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#333366'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '2132') { $Farbe = 'weiß/schwarz'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#000000'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '22') { $Farbe = 'hellblau/weiß'; $IntFarb = '19'; $FarbCode = '#6699FF,#FFFFFF'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '31') { $Farbe = 'schwarz/weiß'; $IntFarb = '16'; $FarbCode = '#000000,#FFFFFF'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '32') { $Farbe = 'schwarz '; $IntFarb = '2'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '3281') { $Farbe = 'schwarz/rot'; $IntFarb = '2'; $FarbCode = '#000000,#990000'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '33') { $Farbe = 'schwarz/weiß'; $IntFarb = '2'; $FarbCode = '#000000,#FFFFFF'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '34') { $Farbe = 'schwarz-weiß'; $IntFarb = '2'; $FarbCode = '#000000,#FFFFFF'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '36') { $Farbe = 'schwarz-weiß'; $IntFarb = '2'; $FarbCode = '#000000,#FFFFFF'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '4') { $Farbe = 'deep blue stone'; $IntFarb = '4'; $FarbCode = '#336699'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '41') { $Farbe = 'havanna'; $IntFarb = '10'; $FarbCode = '#996666'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '42') { $Farbe = 'beige'; $IntFarb = '7'; $FarbCode = '#CC9966'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '43') { $Farbe = 'chocolate'; $IntFarb = '10'; $FarbCode = '#330000'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '4347') { $Farbe = 'chocolate/ecru'; $IntFarb = '10'; $FarbCode = '#330000,#CC9966'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '44') { $Farbe = 'sand'; $IntFarb = '10'; $FarbCode = '#996633'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '46') { $Farbe = 'cognac'; $IntFarb = '10'; $FarbCode = '#CC6600'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '47') { $Farbe = 'ecru'; $IntFarb = '8'; $FarbCode = '#CC9966'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '5') { $Farbe = 'heavy blue used'; $IntFarb = '4'; $FarbCode = '#003399'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '51') { $Farbe = 'hellgrau'; $IntFarb = '8'; $FarbCode = '#999999'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '52') { $Farbe = 'mittelgrau'; $IntFarb = '8'; $FarbCode = '#333333'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '53') { $Farbe = 'dunkelgrau'; $IntFarb = '20'; $FarbCode = '#666666'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '71') { $Farbe = 'mint'; $IntFarb = '5'; $FarbCode = '#CCFFFF'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '72') { $Farbe = 'dunkelgrün'; $IntFarb = '5'; $FarbCode = '#003333'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '73') { $Farbe = 'olive'; $IntFarb = '21'; $FarbCode = '#666633'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '74') { $Farbe = 'mittelgrün/hellgrau'; $IntFarb = '5'; $FarbCode = '#006633'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '81') { $Farbe = 'rot'; $IntFarb = '3'; $FarbCode = '#990000'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '82') { $Farbe = 'bordeaux'; $IntFarb = '3'; $FarbCode = '#993333'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '83') { $Farbe = 'rosa'; $IntFarb = '9'; $FarbCode = '#CC9999'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '85') { $Farbe = 'orange'; $IntFarb = '13'; $FarbCode = '#FF6600'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '86') { $Farbe = 'gelb'; $IntFarb = '6'; $FarbCode = '#FFCC33'; }
			else if ($Hersteller == 'BP' && $Farbnummer == '98') { $Farbe = 'küchendruck'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#000000'; }
		else if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_color Farbeeinstellung für BP stimmen nicht mit der Mapping ueberein!");
		
		/**  $Hersteller == 'CGWorkwear'**/
		if ($Hersteller == 'CG Workwear' && $Farbnummer == '1') { $Farbe = 'schwarz'; $IntFarb = '2'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '2') { $Farbe = 'toffee'; $IntFarb = '10'; $FarbCode = '#663300'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '3') { $Farbe = 'chocolate'; $IntFarb = '10'; $FarbCode = '#330000'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '4') { $Farbe = 'sand'; $IntFarb = '7'; $FarbCode = '#CC9966'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '5') { $Farbe = 'khaki'; $IntFarb = '7'; $FarbCode = '#FFCC99'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '8') { $Farbe = 'elefantengrau'; $IntFarb = '8'; $FarbCode = '#999999'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '10') { $Farbe = 'weiß'; $IntFarb = '1'; $FarbCode = '#FFFFFF'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '11') { $Farbe = 'marine'; $IntFarb = '20'; $FarbCode = '#003366'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '12') { $Farbe = 'bugatti'; $IntFarb = '4'; $FarbCode = '#0066FF'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '13') { $Farbe = 'hellblau'; $IntFarb = '19'; $FarbCode = '#99CCFF'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '14') { $Farbe = 'grün'; $IntFarb = '5'; $FarbCode = '#006666'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '17') { $Farbe = 'kirschrot'; $IntFarb = '3'; $FarbCode = '#993333'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '18') { $Farbe = 'rot'; $IntFarb = '3'; $FarbCode = '#FF3333'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '22') { $Farbe = 'pepita'; $IntFarb = '1'; $FarbCode = '#000000,#FFFFFF'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '23') { $Farbe = 'bordeaux'; $IntFarb = '3'; $FarbCode = '#990000'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '24') { $Farbe = 'beige'; $IntFarb = '7'; $FarbCode = '#CCCCFF'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '25') { $Farbe = 'royalblau'; $IntFarb = '4'; $FarbCode = '#6699FF'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '26') { $Farbe = 'dunkelbraun'; $IntFarb = '10'; $FarbCode = '#330000'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '27') { $Farbe = 'champagner'; $IntFarb = '1'; $FarbCode = '#FFFFCC'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '29') { $Farbe = 'navy'; $IntFarb = '20'; $FarbCode = '#333366'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '30') { $Farbe = 'orange'; $IntFarb = '13'; $FarbCode = '#FF6633'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '31') { $Farbe = 'grau'; $IntFarb = '8'; $FarbCode = '#9999CC'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '32') { $Farbe = 'taupe'; $IntFarb = '10'; $FarbCode = '#663300'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '33') { $Farbe = 'sahara'; $IntFarb = '7'; $FarbCode = '#CC9966'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '34') { $Farbe = 'türkis'; $IntFarb = '4'; $FarbCode = '#66CCFF'; }
			else if ($Hersteller == 'CG Workwear' && $Farbnummer == '35') { $Farbe = 'kupfer'; $IntFarb = '3'; $FarbCode = '#993300'; }
		else if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_color Farbeeinstellung für CG Workwear stimmen nicht mit der Mapping ueberein!");
		
		
		/**  $Hersteller == 'Erima'**/
		if ($Hersteller == 'Erima' && $Farbnummer == '1') { $Farbe = 'anthrazit/rose/schwarz'; $IntFarb = '8'; $FarbCode = '#666666,#FFCCFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '2') { $Farbe = 'apple green/marine'; $IntFarb = '5'; $FarbCode = '#CCCC00,#003366'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '3') { $Farbe = 'apple green/schwarz'; $IntFarb = '5'; $FarbCode = '#CCCC00,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '4') { $Farbe = 'aqua'; $IntFarb = '4'; $FarbCode = '#00CCCC'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '5') { $Farbe = 'aqua/schwarz'; $IntFarb = '4'; $FarbCode = '#00CCCC,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '6') { $Farbe = 'aqua/schwarz/weiß'; $IntFarb = '4'; $FarbCode = '#00CCCC,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '7') { $Farbe = 'aqua/weiß'; $IntFarb = '4'; $FarbCode = '#00CCCC,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '8') { $Farbe = 'azur/weiß'; $IntFarb = '4'; $FarbCode = '#336699'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '9') { $Farbe = 'berry'; $IntFarb = '9'; $FarbCode = '#FF0099'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '10') { $Farbe = 'berry'; $IntFarb = '9'; $FarbCode = '#FF0099'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '11') { $Farbe = 'blau'; $IntFarb = '4'; $FarbCode = '#000099'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '12') { $Farbe = 'blau/schwarz/weiß'; $IntFarb = '4'; $FarbCode = '#000099,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '13') { $Farbe = 'bordeaux/weiß'; $IntFarb = '3'; $FarbCode = '#990000,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '14') { $Farbe = 'denim'; $IntFarb = '4'; $FarbCode = '#3366CC'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '15') { $Farbe = 'denim/schwarz'; $IntFarb = '4'; $FarbCode = '#3366CC,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '16') { $Farbe = 'denim/weiß'; $IntFarb = '4'; $FarbCode = '#3366CC,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '17') { $Farbe = 'gelb'; $IntFarb = '6'; $FarbCode = ',#FFCC00'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '18') { $Farbe = 'gelb/graphit/schwarz'; $IntFarb = '6'; $FarbCode = ',#FFCC00,#999999'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '19') { $Farbe = 'gelb/new royal'; $IntFarb = '6'; $FarbCode = ',#FFCC00,#0066FF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '20') { $Farbe = 'gelb/rot'; $IntFarb = '6'; $FarbCode = ',#FFCC00'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '21') { $Farbe = 'gelb/rot/schwarz'; $IntFarb = '6'; $FarbCode = ',#FFCC00,#FF0033'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '22') { $Farbe = 'gelb/schwarz'; $IntFarb = '6'; $FarbCode = ',#FFCC00,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '23') { $Farbe = 'gelb/schwarz/silber'; $IntFarb = '6'; $FarbCode = '#FFCC00,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '24') { $Farbe = 'gelb/silber/schwarz'; $IntFarb = '6'; $FarbCode = '#FFCC00,#CCCCCC'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '25') { $Farbe = 'grau'; $IntFarb = '8'; $FarbCode = '#999999'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '26') { $Farbe = 'green'; $IntFarb = '5'; $FarbCode = '#009933'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '27') { $Farbe = 'green/graphit/schwarz'; $IntFarb = '5'; $FarbCode = '#009933,#999999'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '28') { $Farbe = 'green/grau'; $IntFarb = '5'; $FarbCode = '#009933,#999999'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '29') { $Farbe = 'green/schwarz'; $IntFarb = '5'; $FarbCode = '#009933,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '30') { $Farbe = 'green/weiß'; $IntFarb = '5'; $FarbCode = '#009933,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '31') { $Farbe = 'grey melange'; $IntFarb = '18'; $FarbCode = '#CCCCCC'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '32') { $Farbe = 'lila/weiß'; $IntFarb = '15'; $FarbCode = '#663399,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '33') { $Farbe = 'mandarine/schwarz'; $IntFarb = '3'; $FarbCode = '#FF6633,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '34') { $Farbe = 'marine'; $IntFarb = '20'; $FarbCode = '#003366'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '35') { $Farbe = 'navy/weiß'; $IntFarb = '20'; $FarbCode = '#0066CC,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '36') { $Farbe = 'new navy'; $IntFarb = '4'; $FarbCode = '#333366'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '37') { $Farbe = 'new navy/cyan/silber'; $IntFarb = '4'; $FarbCode = '#333366,#000066'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '38') { $Farbe = 'new navy/new royal'; $IntFarb = '4'; $FarbCode = '#333366,#0066FF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '39') { $Farbe = 'new navy/weiß'; $IntFarb = '4'; $FarbCode = '#333366,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '40') { $Farbe = 'new navy/weiß/silber'; $IntFarb = '4'; $FarbCode = '#333366,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '41') { $Farbe = 'new royal'; $IntFarb = '4'; $FarbCode = '#0066FF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '42') { $Farbe = 'new royal/gelb'; $IntFarb = '4'; $FarbCode = '#0066FF,#FFCC00'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '43') { $Farbe = 'new royal/gelb/rot/smaragd'; $IntFarb = '4'; $FarbCode = '#0066FF,#FFCC00'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '44') { $Farbe = 'new royal/new navy'; $IntFarb = '4'; $FarbCode = '#0066FF,#333366'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '45') { $Farbe = 'new royal/schwarz'; $IntFarb = '4'; $FarbCode = '#0066FF,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '46') { $Farbe = 'new royal/schwarz/weiß'; $IntFarb = '4'; $FarbCode = '#0066FF,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '47') { $Farbe = 'new royal/weiß'; $IntFarb = '4'; $FarbCode = '#0066FF,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '48') { $Farbe = 'new royal/weiß/schwarz'; $IntFarb = '4'; $FarbCode = '#0066FF,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '49') { $Farbe = 'new sky'; $IntFarb = '19'; $FarbCode = '#6699FF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '50') { $Farbe = 'new sky/weiß'; $IntFarb = '19'; $FarbCode = '#6699FF,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '51') { $Farbe = 'orange'; $IntFarb = '13'; $FarbCode = '#FF9900'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '52') { $Farbe = 'orange/schwarz'; $IntFarb = '13'; $FarbCode = '#FF9900,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '53') { $Farbe = 'orange/schwarz/weiß'; $IntFarb = '13'; $FarbCode = '#FF9900,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '54') { $Farbe = 'orange/silber/schwarz'; $IntFarb = '13'; $FarbCode = '#FF9900,#CCCCCC'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '55') { $Farbe = 'orange/weiß'; $IntFarb = '13'; $FarbCode = '#FF9900,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '56') { $Farbe = 'plum'; $IntFarb = '15'; $FarbCode = '#996699'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '57') { $Farbe = 'rose'; $IntFarb = '9'; $FarbCode = '#FFCCFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '58') { $Farbe = 'rose/anthrazit/schwarz'; $IntFarb = '9'; $FarbCode = '#FFCCFF,#666666'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '59') { $Farbe = 'rose/schwarz'; $IntFarb = '9'; $FarbCode = '#FFCCFF,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '60') { $Farbe = 'rot'; $IntFarb = '3'; $FarbCode = '#FF0033'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '61') { $Farbe = 'rot/gelb/schwarz'; $IntFarb = '3'; $FarbCode = '#FF0033,#FFCC00'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '62') { $Farbe = 'rot/new navy'; $IntFarb = '3'; $FarbCode = '#FF0033,#333366'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '63') { $Farbe = 'rot/new royal'; $IntFarb = '3'; $FarbCode = '#FF0033,#0066FF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '64') { $Farbe = 'rot/schwarz'; $IntFarb = '3'; $FarbCode = '#FF0033,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '65') { $Farbe = 'rot/schwarz/weiß'; $IntFarb = '3'; $FarbCode = '#FF0033,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '66') { $Farbe = 'rot/weiß'; $IntFarb = '3'; $FarbCode = '#FF0033,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '67') { $Farbe = 'rot/weiß/schwarz'; $IntFarb = '3'; $FarbCode = '#FF0033,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '68') { $Farbe = 'royal'; $IntFarb = '4'; $FarbCode = '#0066CC'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '69') { $Farbe = 'schwarz'; $IntFarb = '2'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '70') { $Farbe = 'schwarz/aqua/weiß'; $IntFarb = '2'; $FarbCode = '#000000,#00CCCC'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '71') { $Farbe = 'schwarz/gelb'; $IntFarb = '2'; $FarbCode = '#000000,#FFCC00'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '72') { $Farbe = 'schwarz/gelb/schwarz'; $IntFarb = '2'; $FarbCode = '#000000,#FFCC00'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '73') { $Farbe = 'schwarz/gelb/silber'; $IntFarb = '2'; $FarbCode = '#000000,#FFCC00'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '74') { $Farbe = 'schwarz/gold'; $IntFarb = '2'; $FarbCode = '#000000,#CC9933'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '75') { $Farbe = 'schwarz/graphit/rot'; $IntFarb = '2'; $FarbCode = '#000000,#999999'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '76') { $Farbe = 'schwarz/grau'; $IntFarb = '2'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '77') { $Farbe = 'schwarz/green'; $IntFarb = '2'; $FarbCode = '#000000,#009933'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '78') { $Farbe = 'schwarz/green/weiß'; $IntFarb = '2'; $FarbCode = '#000000,#009933'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '79') { $Farbe = 'schwarz/new royal'; $IntFarb = '2'; $FarbCode = '#000000,#0066FF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '80') { $Farbe = 'schwarz/new sky'; $IntFarb = '2'; $FarbCode = '#000000,#6699FF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '81') { $Farbe = 'schwarz/orange'; $IntFarb = '2'; $FarbCode = '#000000,#FF9900'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '82') { $Farbe = 'schwarz/rot'; $IntFarb = '2'; $FarbCode = '#000000,#FF0033'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '83') { $Farbe = 'schwarz/silber'; $IntFarb = '2'; $FarbCode = '#000000,#CCCCCC'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '84') { $Farbe = 'schwarz/silber/schwarz'; $IntFarb = '2'; $FarbCode = '#000000,#CCCCCC'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '85') { $Farbe = 'schwarz/silber/weiß'; $IntFarb = '2'; $FarbCode = '#000000,#CCCCCC'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '86') { $Farbe = 'schwarz/vanilla'; $IntFarb = '2'; $FarbCode = '#000000,#FFFFCC'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '87') { $Farbe = 'schwarz/weiß'; $IntFarb = '2'; $FarbCode = '#000000,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '88') { $Farbe = 'schwarz/weiß/blau/green/rot'; $IntFarb = '2'; $FarbCode = '#000000,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '89') { $Farbe = 'schwarz/weiß/orange'; $IntFarb = '2'; $FarbCode = '#000000,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '90') { $Farbe = 'schwarz/weiß/silber'; $IntFarb = '2'; $FarbCode = '#000000,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '91') { $Farbe = 'silber/schwarz'; $IntFarb = '8'; $FarbCode = '#CCCCCC,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '92') { $Farbe = 'silver'; $IntFarb = '8'; $FarbCode = '#CCCCCC'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '93') { $Farbe = 'sky'; $IntFarb = '19'; $FarbCode = '#99CCFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '94') { $Farbe = 'sky/marine'; $IntFarb = '19'; $FarbCode = '#99CCFF,#003366'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '95') { $Farbe = 'smaragd'; $IntFarb = '5'; $FarbCode = '#006633'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '96') { $Farbe = 'smaragd/schwarz'; $IntFarb = '5'; $FarbCode = '#006633,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '97') { $Farbe = 'smaragd/weiß'; $IntFarb = '5'; $FarbCode = '#006633,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '98') { $Farbe = 'smaragd/weiß/schwarz'; $IntFarb = '5'; $FarbCode = '#006633,#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '99') { $Farbe = 'vanilla/schwarz'; $IntFarb = '6'; $FarbCode = '#FFFFCC,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '100') { $Farbe = 'violett'; $IntFarb = '15'; $FarbCode = '#6600CC'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '101') { $Farbe = 'weiß'; $IntFarb = '1'; $FarbCode = '#FFFFFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '102') { $Farbe = 'weiß/berry'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#FF0099'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '103') { $Farbe = 'weiß/blau'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#000099'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '104') { $Farbe = 'weiß/blau/schwarz'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#000099'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '105') { $Farbe = 'weiß/blau/silber'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#000099'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '106') { $Farbe = 'weiß/denim'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#3366CC'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '107') { $Farbe = 'weiß/gold'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#CC9933'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '108') { $Farbe = 'weiß/gold/schwarz'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#CC9933'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '109') { $Farbe = 'weiß/green'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#009933'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '110') { $Farbe = 'weiß/lila/silber'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#663399'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '111') { $Farbe = 'weiß/lime/silber'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#99CC33'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '112') { $Farbe = 'weiß/marine/grau'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#003366'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '113') { $Farbe = 'weiß/new royal'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#0066FF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '114') { $Farbe = 'weiß/ocean/hellgrau'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#99CCFF'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '115') { $Farbe = 'weiß/rot'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#FF0033'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '116') { $Farbe = 'weiß/rot/silber'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#FF0033'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '117') { $Farbe = 'weiß/schwarz'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '118') { $Farbe = 'weiß/schwarz/blau'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '119') { $Farbe = 'weiß/schwarz/green'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '120') { $Farbe = 'weiß/schwarz/rot'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '121') { $Farbe = 'weiß/schwarz/silber'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '122') { $Farbe = 'weiß/schwarz/weiß'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#000000'; }
			else if ($Hersteller == 'Erima' && $Farbnummer == '123') { $Farbe = 'weiß/silber/schwarz'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#CCCCCC'; }
		else if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_color Farbeeinstellung für Erima stimmen nicht mit der Mapping ueberein!");

		
		/**  $Hersteller == 'Hakro'**/
		if ($Hersteller == 'Hakro' && $Farbnummer == '01') { $Farbe = 'weiss'; $IntFarb = '1'; $FarbCode = '#FFFFFF'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '02') { $Farbe = 'rot'; $IntFarb = '3'; $FarbCode = '#FF0000'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '03') { $Farbe = 'marine'; $IntFarb = '20'; $FarbCode = '#000066'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '04') { $Farbe = 'lemon'; $IntFarb = '6'; $FarbCode = '#FFFF99'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '05') { $Farbe = 'schwarz'; $IntFarb = '2'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '07') { $Farbe = 'sand'; $IntFarb = '10'; $FarbCode = '#999966'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '08') { $Farbe = 'forest'; $IntFarb = '5'; $FarbCode = '#003300'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '09') { $Farbe = 'rosa'; $IntFarb = '9'; $FarbCode = '#FFCCCC'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '10') { $Farbe = 'royal'; $IntFarb = '4'; $FarbCode = '#0000CC'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '11') { $Farbe = 'azur'; $IntFarb = '19'; $FarbCode = '#0033CC'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '12') { $Farbe = 'smaragd'; $IntFarb = '4'; $FarbCode = '#006666'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '14') { $Farbe = 'gold'; $IntFarb = '12'; $FarbCode = '#CC9900'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '15') { $Farbe = 'grau meliert'; $IntFarb = '18'; $FarbCode = '#999999'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '16') { $Farbe = 'natur'; $IntFarb = '1'; $FarbCode = '#FFCC99'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '17') { $Farbe = 'weinrot'; $IntFarb = '3'; $FarbCode = '#660000'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '19') { $Farbe = 'moos'; $IntFarb = '5'; $FarbCode = '#006633'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '20') { $Farbe = 'ice-blue'; $IntFarb = '19'; $FarbCode = '#99CCFF'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '22') { $Farbe = 'chocolate'; $IntFarb = '10'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '24') { $Farbe = 'ash meliert'; $IntFarb = '8'; $FarbCode = '#CCCCCC'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '25') { $Farbe = 'sky'; $IntFarb = '19'; $FarbCode = '#CCCCFF'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '26') { $Farbe = 'silber'; $IntFarb = '11'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '27') { $Farbe = 'orange'; $IntFarb = '13'; $FarbCode = '#FF6600'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '28') { $Farbe = 'anthrazit'; $IntFarb = '2'; $FarbCode = '#333333'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '29') { $Farbe = 'kellygreen'; $IntFarb = '5'; $FarbCode = '#006600'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '30') { $Farbe = 'apfel'; $IntFarb = '5'; $FarbCode = '#66FF66'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '32') { $Farbe = 'aqua'; $IntFarb = '19'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '34') { $Farbe = 'tinte'; $IntFarb = '20'; $FarbCode = '#000033'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '35') { $Farbe = 'sonne'; $IntFarb = '6'; $FarbCode = '#FFCC00'; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '36') { $Farbe = 'tundra'; $IntFarb = '10'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '38') { $Farbe = 'bodeaux'; $IntFarb = '3'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '39') { $Farbe = 'coffee'; $IntFarb = '10'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '40') { $Farbe = 'kiwi'; $IntFarb = '5'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '41') { $Farbe = 'malibu-blue'; $IntFarb = '19'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '42') { $Farbe = 'graphit'; $IntFarb = '8'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '43') { $Farbe = 'titan'; $IntFarb = '8'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '45') { $Farbe = 'cardinal red'; $IntFarb = '3'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '46') { $Farbe = 'petrol'; $IntFarb = '5'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '48') { $Farbe = 'off-white'; $IntFarb = '1'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '56') { $Farbe = 'olive'; $IntFarb = '5'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '58') { $Farbe = 'stone'; $IntFarb = '8'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '60') { $Farbe = 'apricot'; $IntFarb = '13'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '72') { $Farbe = 'tanne'; $IntFarb = '5'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '118') { $Farbe = 'aubergine'; $IntFarb = '15'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '120') { $Farbe = 'fresh-green'; $IntFarb = '5'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '121') { $Farbe = 'purple'; $IntFarb = '15'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '122') { $Farbe = 'magenta'; $IntFarb = '3'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '123') { $Farbe = 'cherry'; $IntFarb = '3'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '125') { $Farbe = 'granadine'; $IntFarb = '3'; $FarbCode = ''; }
			else if ($Hersteller == 'Hakro' && $Farbnummer == '130') { $Farbe = 'turquoise'; $IntFarb = '4'; $FarbCode = ''; }
		else if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_color Farbeeinstellung für Hakro stimmen nicht mit der Mapping ueberein!");

		
		/**  $Hersteller == 'Mascot'**/
		if ($Hersteller == 'Mascot' && $Farbnummer == '01') { $Farbe = 'marine'; $IntFarb = '20'; $FarbCode = '#333366'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '010') { $Farbe = 'schwarzblau'; $IntFarb = '20'; $FarbCode = '#000066'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '01009') { $Farbe = 'schwarzblau/schwarz'; $IntFarb = '20'; $FarbCode = '#000066,#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '010150') { $Farbe = 'schwarzblau/hellbraun gestreift'; $IntFarb = '20'; $FarbCode = '#000066,#CC9966'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0106') { $Farbe = 'marine/weiss gestreift'; $IntFarb = '20'; $FarbCode = '#333366,#FFFFFF'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '01088') { $Farbe = 'schwarzblau/hellgrau'; $IntFarb = '2'; $FarbCode = '#000066,#CC9966'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0109') { $Farbe = 'marine/schwarz'; $IntFarb = '20'; $FarbCode = '#333366,#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '01888') { $Farbe = 'marine/anthrazit'; $IntFarb = '20'; $FarbCode = '#333366,#999999'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '02') { $Farbe = 'rot'; $IntFarb = '3'; $FarbCode = '#CC0033'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '02888') { $Farbe = 'rot/anthrazit'; $IntFarb = '3'; $FarbCode = '#CC0033,#999999'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '03') { $Farbe = 'grün'; $IntFarb = '5'; $FarbCode = '#336600'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '04') { $Farbe = 'oliv'; $IntFarb = '21'; $FarbCode = '#666600'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '05') { $Farbe = 'khaki'; $IntFarb = '7'; $FarbCode = '#CCCC99'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0509') { $Farbe = 'khaki/schwarz'; $IntFarb = '7'; $FarbCode = '#CCCC99,#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '06') { $Farbe = 'weiss'; $IntFarb = '1'; $FarbCode = '#FFFFFF'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '06888') { $Farbe = 'weiss/anthrazit gestreift'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#999999'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '09') { $Farbe = 'schwarz'; $IntFarb = '2'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '08') { $Farbe = 'graumeliert '; $IntFarb = '8'; $FarbCode = '#CCCCCC'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0902') { $Farbe = 'schwarz/rot'; $IntFarb = '2'; $FarbCode = '#000000,#CC0033'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0907') { $Farbe = 'schwarz/gelb'; $IntFarb = '2'; $FarbCode = '#000000,#FFFF00'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0909') { $Farbe = 'schwarz/schwarz'; $IntFarb = '2'; $FarbCode = '#000000,#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0914') { $Farbe = 'schwarz/flourezierendes orange'; $IntFarb = '2'; $FarbCode = '#000000,#FF6600'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0914') { $Farbe = 'schwarz/floureszierendes gelb'; $IntFarb = '2'; $FarbCode = '#CCFF00,#CCFF00'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0918') { $Farbe = 'schwarz/dunkelanthrazit'; $IntFarb = '2'; $FarbCode = '#000000,#666666'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0919') { $Farbe = 'schwarz/dunkeloliv'; $IntFarb = '2'; $FarbCode = '#000000,#666633'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0988') { $Farbe = 'schwarz/hellgrau'; $IntFarb = '2'; $FarbCode = '#000000,#CCCCCC'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '10509') { $Farbe = 'dunkelkhaki/schwarz'; $IntFarb = '10'; $FarbCode = '#996633,#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '11') { $Farbe = 'kornblau'; $IntFarb = '4'; $FarbCode = '#3333FF'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '11001') { $Farbe = 'marineabgestuft/marine'; $IntFarb = '20'; $FarbCode = '#333366,#333366'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '1101') { $Farbe = 'kornblau/marine'; $IntFarb = '4'; $FarbCode = '#3333FF,#333366'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '110188') { $Farbe = 'kornblau/marine/hellgrau'; $IntFarb = '4'; $FarbCode = '#3333FF,#333366,#CCCCCC'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '111') { $Farbe = 'marine/kornblau'; $IntFarb = '20'; $FarbCode = '#333366,#3333FF'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '118') { $Farbe = 'hellanthrazit'; $IntFarb = '8'; $FarbCode = '#999999'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '11818') { $Farbe = 'dunkelanthrazit/hellgrau meliert'; $IntFarb = '8'; $FarbCode = '#666666,#CC9966'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '119') { $Farbe = 'helloliv'; $IntFarb = '21'; $FarbCode = '#CCCC66'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '12') { $Farbe = 'marine/rot'; $IntFarb = '20'; $FarbCode = '#333366,#CC0033'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '13') { $Farbe = 'marine/grün'; $IntFarb = '20'; $FarbCode = '#333366,#336600'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '14') { $Farbe = 'orange'; $IntFarb = '13'; $FarbCode = '#FF6633'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '140') { $Farbe = 'dunkelorange'; $IntFarb = '13'; $FarbCode = '#CC6600'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '1403') { $Farbe = 'orange/grün'; $IntFarb = '13'; $FarbCode = '#FF6633,#336600'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '141') { $Farbe = 'orange/marine'; $IntFarb = '13'; $FarbCode = '#FF6633,#333366'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '1411') { $Farbe = 'orange/kornblau'; $IntFarb = '13'; $FarbCode = '#FF6633,#3333FF'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '148888') { $Farbe = 'orange/anthrazit'; $IntFarb = '13'; $FarbCode = '#FF6633,#999999'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '15') { $Farbe = 'marine/khaki'; $IntFarb = '20'; $FarbCode = '#333366,#CCCC99'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '17') { $Farbe = 'gelb'; $IntFarb = '6'; $FarbCode = '#FFFF00'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '1703') { $Farbe = 'gelb/grün'; $IntFarb = '6'; $FarbCode = '#FFFF00,#336600'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '171') { $Farbe = 'gelb/marine'; $IntFarb = '6'; $FarbCode = '#FFFF00,#333366'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '1711') { $Farbe = 'gelb/kornblau'; $IntFarb = '6'; $FarbCode = '#FFFF00,#3333FF'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '17888') { $Farbe = 'gelb/anthrazit'; $IntFarb = '6'; $FarbCode = '#FFFF00,#999999'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '18') { $Farbe = 'dunkelanthrazit'; $IntFarb = '8'; $FarbCode = '#666666'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '180') { $Farbe = 'blaugrau'; $IntFarb = '19'; $FarbCode = '#CCCCFF'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '1809') { $Farbe = 'dunkelanthrazit/schwarz'; $IntFarb = '8'; $FarbCode = '#666666,#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '1860') { $Farbe = 'dunkelanthrazit/weiss gestreift'; $IntFarb = '8'; $FarbCode = '#666666,#FFFFFF'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '188') { $Farbe = 'marine/hellgrau'; $IntFarb = '20'; $FarbCode = '#333366,#CCCCCC'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '18809') { $Farbe = 'grau-abgestuft/schwarz'; $IntFarb = '8'; $FarbCode = '#999999,#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '188902') { $Farbe = 'grau-abgestuft/schwarz/rot'; $IntFarb = '8'; $FarbCode = '#999999,#000000,#CC0033'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '188907') { $Farbe = 'grau-abgestuft/schwarz/gelb'; $IntFarb = '8'; $FarbCode = '#999999,#000000,#FFFF00'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '19') { $Farbe = 'dunkeloliv'; $IntFarb = '21'; $FarbCode = '#666633'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '1909') { $Farbe = 'dunkeloliv/schwarz'; $IntFarb = '21'; $FarbCode = '#666633,#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '19150') { $Farbe = 'dunkeloliv/helbraun gestreift'; $IntFarb = '21'; $FarbCode = '#666633,#CC9966'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '202') { $Farbe = 'verkehrsrot'; $IntFarb = '3'; $FarbCode = '#CC0000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '20209') { $Farbe = 'verkerhsrot/schwarz'; $IntFarb = '3'; $FarbCode = '#CC0000,#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '21') { $Farbe = 'rot/marine'; $IntFarb = '3'; $FarbCode = '#CC0033,#333366'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '22') { $Farbe = 'bordeaux'; $IntFarb = '3'; $FarbCode = '#660000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '31') { $Farbe = 'grün/marine'; $IntFarb = '5'; $FarbCode = '#336600,#333366'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '50') { $Farbe = 'dunkelbraun'; $IntFarb = '10'; $FarbCode = '#996633'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '5009') { $Farbe = 'dunkelbraun/schwarz gestreift'; $IntFarb = '10'; $FarbCode = '#996633,#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '51') { $Farbe = 'khaki/marine'; $IntFarb = '7'; $FarbCode = '#CCCC99;#333366'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '5188') { $Farbe = 'khaki/marine/hellgrau'; $IntFarb = '7'; $FarbCode = '#CCCC99;#333366,#CCCCCC'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '61') { $Farbe = 'weiss/marine'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#333366'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '61') { $Farbe = 'weiss/marinegestreift'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#333366'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '70709') { $Farbe = 'verkehrsgelb/schwarz'; $IntFarb = '6'; $FarbCode = '#FFCC33,#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '80809') { $Farbe = 'kittgrau/schwarz'; $IntFarb = '8'; $FarbCode = '#CCCCCC,#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '88') { $Farbe = 'hellgrau'; $IntFarb = '8'; $FarbCode = '#CCCCCC'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '88') { $Farbe = 'transparent'; $IntFarb = '1'; $FarbCode = '#FFFFFF'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '8809') { $Farbe = 'hellgrau/schwarz'; $IntFarb = '8'; $FarbCode = '#CCCCCC,#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '880902') { $Farbe = 'silber/schwarz/rot'; $IntFarb = '11'; $FarbCode = '#CCCCCC,#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '881') { $Farbe = 'hellgrau/marine'; $IntFarb = '8'; $FarbCode = '#CCCCCC,#333366'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '888') { $Farbe = 'anthrazit'; $IntFarb = '8'; $FarbCode = '#999999'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '88802') { $Farbe = 'anthrazit/rot'; $IntFarb = '8'; $FarbCode = '#999999,#CC0033'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '8889') { $Farbe = 'anthrazit/aschwarz'; $IntFarb = '8'; $FarbCode = '#999999,#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '901') { $Farbe = 'schwarz/marine'; $IntFarb = '2'; $FarbCode = '#000000,#333366'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '9888') { $Farbe = 'schwarz/anthrazit'; $IntFarb = '2'; $FarbCode = '#000000#999999'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '9888') { $Farbe = 'schwarz/anthrazit gestreift'; $IntFarb = '2'; $FarbCode = '#000000#999999'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A09') { $Farbe = 'verkehrsrot/marine'; $IntFarb = '3'; $FarbCode = '#CC0000,#333366'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A10') { $Farbe = 'anthrazit/verkehrsrot'; $IntFarb = '8'; $FarbCode = '#999999,#CC0000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A11') { $Farbe = 'dunkeloliv mit Druck'; $IntFarb = '21'; $FarbCode = '#666633'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A12') { $Farbe = 'schwarz mit Druck'; $IntFarb = '2'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A13') { $Farbe = 'schwarzblau mit Druck'; $IntFarb = '20'; $FarbCode = '#000066'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A14') { $Farbe = 'dunkelanthrazit mit Druck'; $IntFarb = '8'; $FarbCode = '#666666'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A32') { $Farbe = 'dunkles denimblau'; $IntFarb = '20'; $FarbCode = '#336699'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A33') { $Farbe = 'schwarz kariertbedruckt'; $IntFarb = '2'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A34') { $Farbe = 'schwarzblau kariertbedruckt'; $IntFarb = '20'; $FarbCode = '#000066'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A35') { $Farbe = 'dunkelanthrazit kariertbedruckt'; $IntFarb = '8'; $FarbCode = '#666666'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A36') { $Farbe = 'dunkeloliv kariertbedruckt'; $IntFarb = '21'; $FarbCode = '#666633'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A37') { $Farbe = 'dunkelbraun kariertbedruckt'; $IntFarb = '10'; $FarbCode = '#996633'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A42') { $Farbe = 'dunkelanthrazit meliert'; $IntFarb = '8'; $FarbCode = '#666666'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A43') { $Farbe = 'schwarzblaumeliert'; $IntFarb = '17'; $FarbCode = '#000066'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A49') { $Farbe = 'rot/anthrazit'; $IntFarb = '3'; $FarbCode = '#CC0033,#999999'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A52') { $Farbe = 'schwarzblua/schwarz'; $IntFarb = '20'; $FarbCode = '#000066,#000000'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A55') { $Farbe = 'hellblau'; $IntFarb = '19'; $FarbCode = '#99CCFF'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A70') { $Farbe = 'sonnengelb'; $IntFarb = '6'; $FarbCode = '#FFCC00'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A71') { $Farbe = 'Zyanblau'; $IntFarb = '4'; $FarbCode = '#66CCFF'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A72') { $Farbe = 'Limonengrün'; $IntFarb = '5'; $FarbCode = '#CCCC00'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A78') { $Farbe = 'graumeliert gestreift'; $IntFarb = '18'; $FarbCode = '#CC9966'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A82') { $Farbe = 'dunkelanthrazitmeliert'; $IntFarb = '18'; $FarbCode = '#666666'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A83') { $Farbe = 'schwarz/anthrazit/gelb'; $IntFarb = '2'; $FarbCode = '#000000,#999999,#FFFF00'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A84') { $Farbe = 'schwarz/anthrazit/rot'; $IntFarb = '2'; $FarbCode = '#000000,#999999,#CC0033'; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '01') { $Farbe = 'marine'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '06') { $Farbe = 'weiss'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '08') { $Farbe = 'schwarz'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '010') { $Farbe = 'schwarzblau'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '18') { $Farbe = 'dunkelanthrazit'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '19') { $Farbe = 'dunkeloliv'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '50') { $Farbe = 'dunkelbraun'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '88') { $Farbe = 'hellgrau'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '140') { $Farbe = 'dunkelorange'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0918') { $Farbe = 'schwarz/dunkelanthrazit'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '1809') { $Farbe = 'dunkelanthrazit/schwarz'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '1860') { $Farbe = 'dunkelanthrazit/weiss gestreift'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '1909') { $Farbe = 'dunkeloliv/schwarz'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '5009') { $Farbe = 'dunkelbraun/schwarz gestreift'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '01009') { $Farbe = 'schwarzblau/schwarz'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '11818') { $Farbe = 'dunkelanthrazit/hellgrau meliert'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '19150') { $Farbe = 'dunkeloliv/helbraun gestreift'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '20209') { $Farbe = 'verkerhsrot/schwarz'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '70709') { $Farbe = 'verkehrsgelb/schwarz'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '80809') { $Farbe = 'kittgrau/schwarz'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '010150') { $Farbe = 'schwarzblau/hellbraun gestreift'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A11') { $Farbe = 'dunkeloliv mit Druck'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A12') { $Farbe = 'schwarz mit Druck'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A13') { $Farbe = 'schwarzblau mit Druck'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A14') { $Farbe = 'Dunkelanthrazit mit Druck'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A32') { $Farbe = 'dunkles denimblau'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A33') { $Farbe = 'schwarz kariertbedruckt'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A34') { $Farbe = 'schwarzblau kariertbedruckt'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A35') { $Farbe = 'dunkelanthrazit kariertbedruckt'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A36') { $Farbe = 'dunkeloliv kariertbedruckt'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A37') { $Farbe = 'dunkelbraun kariertbedruckt'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A70') { $Farbe = 'sonnengelb'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A71') { $Farbe = 'Zyanblau'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A72') { $Farbe = 'Limonengrün'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A78') { $Farbe = 'graumeliert gestreift'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '05') { $Farbe = 'khaki'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '11') { $Farbe = 'kornblau'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '888') { $Farbe = 'anthrazit'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0106') { $Farbe = 'marine/weiss gestreift'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0109') { $Farbe = 'marine/schwarz'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0509') { $Farbe = 'khaki/schwarz'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '8889') { $Farbe = 'anthrazit/aschwarz'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '9888') { $Farbe = 'schwarz/anthrazit'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '9888') { $Farbe = 'schwarz/anthrazit gestreift'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '01888') { $Farbe = 'marine/anthrazit'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '06888') { $Farbe = 'weiss/anthrazit gestreift'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '118') { $Farbe = 'hellanthrazit'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '119') { $Farbe = 'helloliv'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '180') { $Farbe = 'blaugrau'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '202') { $Farbe = 'verkehrsrot'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A82') { $Farbe = 'dunkelanthrazitmeliert'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '12') { $Farbe = 'marine/rot'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '13') { $Farbe = 'marine/grün'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '15') { $Farbe = 'marine/khaki'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '21') { $Farbe = 'rotZmarine'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '31') { $Farbe = 'grün/marine'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '51') { $Farbe = 'khaki/marine'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '61') { $Farbe = 'weiss/marine'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '111') { $Farbe = 'marine/kornblau'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '188') { $Farbe = 'marine/hellgrau'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '881') { $Farbe = 'hellgrau/marine'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '1101') { $Farbe = 'kornblau/marine'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '02888') { $Farbe = 'rot/anthrazit'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '88802') { $Farbe = 'anthrazit/rot'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A09') { $Farbe = 'verkehrsrot/marine'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A10') { $Farbe = 'anthrazit/verkehrsrot'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '02') { $Farbe = 'rot'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '03') { $Farbe = 'grün/marine'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '08') { $Farbe = 'stahlgrau'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '22') { $Farbe = 'bordeaux'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '61') { $Farbe = 'weiss/marinegestreift'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A55') { $Farbe = 'hellbalu'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '5188') { $Farbe = 'khaki/marine/hellgrau'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '110188') { $Farbe = 'kornblau/marine/hellgrau'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '17') { $Farbe = 'gelb'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '14') { $Farbe = 'orange'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '141') { $Farbe = 'orange/marine'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '1403') { $Farbe = 'orange/grün'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '1411') { $Farbe = 'orange/kornblau'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '148888') { $Farbe = 'orange/anthrazit'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '171') { $Farbe = 'gelbe/marine'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '1703') { $Farbe = 'gelb/grün'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '1711') { $Farbe = 'gelb/kornblau'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '17888') { $Farbe = 'gelb/nathrazit'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A49') { $Farbe = 'rot/anthrazit'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '88') { $Farbe = 'transparent'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '901') { $Farbe = 'schwarz/marine'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0509') { $Farbe = 'khaki/schwarz'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0902') { $Farbe = 'schwarz/rot'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0907') { $Farbe = 'schwarz/gelb'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0909') { $Farbe = 'schwarz/schwarz'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0914') { $Farbe = 'schwarz/flourezierendes orange'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0914') { $Farbe = 'schwarz/floureszierendes gelb'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0919') { $Farbe = 'schwarz/oliv'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '0988') { $Farbe = 'schwarz/hellgrau'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '8809') { $Farbe = 'hellgrau/schwarz'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '01088') { $Farbe = 'schwarzblau/hellgrau'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '10509') { $Farbe = 'dunkelkhaki/schwarz'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '11001') { $Farbe = 'marineabgestuft/marine'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '18809') { $Farbe = 'grau/abgestuft/schwarz'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '188902') { $Farbe = 'grauabgestuft/schwarz/rot'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '188907') { $Farbe = 'grauabgestuft/schwarz/gelb'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '880902') { $Farbe = 'silber/schwarz/rot'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A83') { $Farbe = 'schwarz/anthrazit/gelb'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A84') { $Farbe = 'schwarz/anthrazit/rot'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == '04') { $Farbe = 'oliv'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A42') { $Farbe = 'dunkelanthrazit meliert'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A43') { $Farbe = 'schwarzblaumeliert'; $IntFarb = ''; $FarbCode = ''; }
			else if ($Hersteller == 'Mascot' && $Farbnummer == 'A52') { $Farbe = 'schwarzblua/schwarz'; $IntFarb = ''; $FarbCode = ''; }
		else if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_color Farbeeinstellung für Mascot stimmen nicht mit der Mapping ueberein!");
		
		/**  $Hersteller == 'Master Italia'**/
		if ($Hersteller == 'Master Italia' && $Farbnummer == '01') { $Farbe = 'weiss'; $IntFarb = '1'; $FarbCode = '#FFFFFF'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '02') { $Farbe = 'schwarz'; $IntFarb = '2'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '03') { $Farbe = 'rot'; $IntFarb = '3'; $FarbCode = '#FF0033'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '04') { $Farbe = 'royal'; $IntFarb = '4'; $FarbCode = '#3300FF'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '05') { $Farbe = 'grün'; $IntFarb = '5'; $FarbCode = '#CCCC00'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '07') { $Farbe = 'beige'; $IntFarb = '7'; $FarbCode = '#996633'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '08') { $Farbe = 'bordeaux'; $IntFarb = '3'; $FarbCode = '#990033'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '09') { $Farbe = 'braun'; $IntFarb = '10'; $FarbCode = '#663333'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '10') { $Farbe = 'camouflage'; $IntFarb = '14'; $FarbCode = '#999966'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '12') { $Farbe = 'ecrù'; $IntFarb = '4'; $FarbCode = '#FFFFCC'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '14') { $Farbe = 'gelb'; $IntFarb = '6'; $FarbCode = '#FFCC00'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '16') { $Farbe = 'grau'; $IntFarb = '8'; $FarbCode = '#666666'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '17') { $Farbe = 'hellblau'; $IntFarb = '19'; $FarbCode = '#66CCFF'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '20') { $Farbe = 'jeans'; $IntFarb = '19'; $FarbCode = '#003366'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '22') { $Farbe = 'khaki'; $IntFarb = '7'; $FarbCode = '#CC9966'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '23') { $Farbe = 'natural'; $IntFarb = '4'; $FarbCode = '#FFFFCC'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '24') { $Farbe = 'navy'; $IntFarb = '20'; $FarbCode = '#000066'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '26') { $Farbe = 'orange'; $IntFarb = '13'; $FarbCode = '#FF6600'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '27') { $Farbe = 'pink'; $IntFarb = '9'; $FarbCode = '#FF99CC'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '28') { $Farbe = 'fuchsia'; $IntFarb = '9'; $FarbCode = '#FF0099'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '32') { $Farbe = 'stein'; $IntFarb = '8'; $FarbCode = '#CCCCCC'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '33') { $Farbe = 'türkis'; $IntFarb = '4'; $FarbCode = '#00CCFF'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '35') { $Farbe = 'olive'; $IntFarb = '21'; $FarbCode = '#333300'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '37') { $Farbe = 'blue clear'; $IntFarb = '4'; $FarbCode = '#6699CC'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '38') { $Farbe = 'blue wasched'; $IntFarb = '4'; $FarbCode = '#336699'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '42') { $Farbe = 'natural/navy'; $IntFarb = '1'; $FarbCode = '#FFFFCC,#000066'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '43') { $Farbe = 'natural/grün'; $IntFarb = '1'; $FarbCode = '#FFFFCC,#CCCC00'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '44') { $Farbe = 'natural/schwarz'; $IntFarb = '1'; $FarbCode = '#FFFFCC,#000000'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '45') { $Farbe = 'natural/rot'; $IntFarb = '1'; $FarbCode = '#FFFFCC,#FF0033'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '46') { $Farbe = 'natural/braun'; $IntFarb = '1'; $FarbCode = '#FFFFCC,#663333'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '47') { $Farbe = 'schwarz/grau'; $IntFarb = '2'; $FarbCode = '#000000,#666666'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '48') { $Farbe = 'stein/braun'; $IntFarb = '1'; $FarbCode = '#CCCCCC,#663333'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '49') { $Farbe = 'navy/weiss'; $IntFarb = '20'; $FarbCode = '#000066,#FFFFFF'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '50') { $Farbe = 'gelb/navy'; $IntFarb = '6'; $FarbCode = '#FFCC00,#000066'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '51') { $Farbe = 'rot/grau'; $IntFarb = '3'; $FarbCode = '#FF0033,#666666'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '52') { $Farbe = 'weiss/navy'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#000066'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '53') { $Farbe = 'grau/schwarz'; $IntFarb = '8'; $FarbCode = '#666666,#000000'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '54') { $Farbe = 'khaki/navy'; $IntFarb = '7'; $FarbCode = '#CC9966,#000066'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '55') { $Farbe = 'navy/hellblau'; $IntFarb = '20'; $FarbCode = '#000066,#66CCFF'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '56') { $Farbe = 'navy/rot'; $IntFarb = '20'; $FarbCode = '#000066,#FF0033'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '57') { $Farbe = 'orange/grau'; $IntFarb = '13'; $FarbCode = '#FF6600,#666666'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '58') { $Farbe = 'rot/navy'; $IntFarb = '3'; $FarbCode = '#FF0033,#000066'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '59') { $Farbe = 'hellblau/navy'; $IntFarb = '19'; $FarbCode = '#66CCFF,#000066'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '60') { $Farbe = 'weiss/schwarz'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#000000'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '61') { $Farbe = 'navy/khaki'; $IntFarb = '20'; $FarbCode = '#000066,#CC9966'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '62') { $Farbe = 'weiss/royal'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#3300FF'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '63') { $Farbe = 'rot/royal'; $IntFarb = '3'; $FarbCode = '#FF0033,#3300FF'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '64') { $Farbe = 'schwarz/gelb'; $IntFarb = '2'; $FarbCode = '#000000,#FFCC00'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '65') { $Farbe = 'weiss/gelb'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#FFCC00'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '66') { $Farbe = 'orange/schwarz'; $IntFarb = '13'; $FarbCode = '#FF6600,#000000'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '67') { $Farbe = 'schwarz/weiss'; $IntFarb = '2'; $FarbCode = '#000000,#FFFFFF'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '68') { $Farbe = 'schwarz/rot'; $IntFarb = '2'; $FarbCode = '#000000,#FF0033'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '69') { $Farbe = 'pink/grau'; $IntFarb = '9'; $FarbCode = '#FF99CC,#666666'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '70') { $Farbe = 'olive/camouflage'; $IntFarb = '21'; $FarbCode = '#333300,#999966'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '71') { $Farbe = 'orange/camouflage'; $IntFarb = '13'; $FarbCode = '#FF6600,#999966'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '72') { $Farbe = 'weiss/jeans'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#003366'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '73') { $Farbe = 'blau/jeans'; $IntFarb = '4'; $FarbCode = '#333366,#003366'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '74') { $Farbe = 'schwarz/jeans'; $IntFarb = '2'; $FarbCode = '#000000,#003366'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '75') { $Farbe = 'weiss/beige'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#996633'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '76') { $Farbe = 'camouflage/weiss'; $IntFarb = '14'; $FarbCode = '#999966,#FFFFFF'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '77') { $Farbe = 'camouflage/khaki'; $IntFarb = '14'; $FarbCode = '#999966,#CC9966'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '78') { $Farbe = 'camouflage/olive'; $IntFarb = '14'; $FarbCode = '#999966,#333300'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '79') { $Farbe = 'camoruflage/braun'; $IntFarb = '14'; $FarbCode = '#999966,#663333'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '18') { $Farbe = 'hellgrau'; $IntFarb = '8'; $FarbCode = '#999999'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '19') { $Farbe = 'hellgrün'; $IntFarb = '5'; $FarbCode = '#99CC33'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '21') { $Farbe = 'kamel'; $IntFarb = '7'; $FarbCode = '#CC9933'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '40') { $Farbe = 'avio'; $IntFarb = '19'; $FarbCode = '#CCCCFF'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '80') { $Farbe = 'navy/grün'; $IntFarb = '20'; $FarbCode = '#000066,#CCCC00'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '81') { $Farbe = 'schwarz/schwarz'; $IntFarb = '2'; $FarbCode = '#000000,#000000'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '82') { $Farbe = 'grau/navy'; $IntFarb = '8'; $FarbCode = '#666666,#000066'; }
			else if ($Hersteller == 'Master Italia' && $Farbnummer == '83') { $Farbe = 'navy/gelb'; $IntFarb = '20'; $FarbCode = '#000066,#FFCC00'; }
		else if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_color Farbeeinstellung für Master Italia stimmen nicht mit der Mapping ueberein!");
		
		/**  $Hersteller == 'Switcher'**/
		if ($Hersteller == 'Switcher' && $Farbnummer == '01') { $Farbe = 'weiß'; $IntFarb = '1'; $FarbCode = '#FFFFFF'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '10') { $Farbe = 'rot'; $IntFarb = '3'; $FarbCode = '#FF0000'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '107') { $Farbe = 'ketchuprot'; $IntFarb = '3'; $FarbCode = '#FF3300'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '11') { $Farbe = 'rosa'; $IntFarb = '9'; $FarbCode = '#FFCCCC'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '117') { $Farbe = 'flieder'; $IntFarb = '15'; $FarbCode = '#9999FF'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '158') { $Farbe = 'weinrot'; $IntFarb = '3'; $FarbCode = '#660000'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '170') { $Farbe = 'pink'; $IntFarb = '9'; $FarbCode = '#FF6699'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '182') { $Farbe = 'orangerot'; $IntFarb = '13'; $FarbCode = '#FF9900'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '184') { $Farbe = 'rubin'; $IntFarb = '9'; $FarbCode = '#990000'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '197') { $Farbe = 'alpenveilchen'; $IntFarb = '3'; $FarbCode = '#CC0066'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '198') { $Farbe = 'lila'; $IntFarb = '15'; $FarbCode = '#9900CC'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '20') { $Farbe = 'marine'; $IntFarb = '20'; $FarbCode = '#000066'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '204') { $Farbe = 'engelsblau'; $IntFarb = '19'; $FarbCode = '#66CCFF'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2227') { $Farbe = 'rot/weiß'; $IntFarb = '3'; $FarbCode = '#FF0000,#FFFFFF'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2237') { $Farbe = 'grau/weiß'; $IntFarb = '8'; $FarbCode = '#999999,#FFFFFF'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2238') { $Farbe = 'dunkelgrau/grau'; $IntFarb = '8'; $FarbCode = '#666666,#999999'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2340') { $Farbe = 'ocean/weiß'; $IntFarb = '4'; $FarbCode = '#0066CC,#FFFFFF'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2341') { $Farbe = 'marine/weiß'; $IntFarb = '4'; $FarbCode = '#000066,#FFFFFF'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2345') { $Farbe = 'schwarz/grau'; $IntFarb = '2'; $FarbCode = '#000000,#999999'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2346') { $Farbe = 'tinte/grau'; $IntFarb = '20'; $FarbCode = '#000033,#999999'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2347') { $Farbe = 'rot/schwarz'; $IntFarb = '3'; $FarbCode = '#FF0000,#000000'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2350') { $Farbe = 'tinte/ocean'; $IntFarb = '20'; $FarbCode = '#000033,#0066CC'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2395') { $Farbe = 'eisblau/dunkelgrau'; $IntFarb = '19'; $FarbCode = '#0099FF,#666666'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2405') { $Farbe = 'schwarz/weiß'; $IntFarb = '2'; $FarbCode = '#000000,#FFFFFF'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2474') { $Farbe = 'dunkelgrau/schwarz'; $IntFarb = '8'; $FarbCode = '#666666,#000000'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2475') { $Farbe = 'dunkelgrau/tinte'; $IntFarb = '8'; $FarbCode = '#666666,#0066CC'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '248') { $Farbe = 'adriablau'; $IntFarb = ''; $FarbCode = '#006699'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '259') { $Farbe = 'ocean'; $IntFarb = '20'; $FarbCode = '#0066CC'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '262') { $Farbe = 'hellblau'; $IntFarb = '19'; $FarbCode = '#99CCFF'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '276') { $Farbe = 'eisblau'; $IntFarb = '19'; $FarbCode = '#0099FF'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '282') { $Farbe = 'blaugrau'; $IntFarb = '4'; $FarbCode = '#669999'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '286') { $Farbe = 'wittwenblau'; $IntFarb = '4'; $FarbCode = '#003366'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '288') { $Farbe = 'blau bay'; $IntFarb = '4'; $FarbCode = '#0099CC'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '31') { $Farbe = 'grün'; $IntFarb = '5'; $FarbCode = '#009933'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '331') { $Farbe = 'helles khaki'; $IntFarb = '5'; $FarbCode = '#999933'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '336') { $Farbe = 'jade'; $IntFarb = '5'; $FarbCode = '#CCFFFF'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '338') { $Farbe = 'mintgrün'; $IntFarb = '5'; $FarbCode = '#33CC99'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '3380') { $Farbe = 'schwarz/rot'; $IntFarb = '2'; $FarbCode = '#000000,#FF0000'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '339') { $Farbe = 'helles mintgrün'; $IntFarb = '5'; $FarbCode = '#66CC99'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '340') { $Farbe = 'Ahorn'; $IntFarb = '5'; $FarbCode = '#003300'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '341') { $Farbe = 'grüner pfeffer'; $IntFarb = '5'; $FarbCode = '#339966'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '342') { $Farbe = 'zypressengrün'; $IntFarb = '21'; $FarbCode = '#333300'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '364') { $Farbe = 'limette'; $IntFarb = '5'; $FarbCode = '#99CC33'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '370') { $Farbe = 'türkis'; $IntFarb = '5'; $FarbCode = '#00CCCC'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '375') { $Farbe = 'renngrün'; $IntFarb = '5'; $FarbCode = '#006633'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '40') { $Farbe = 'schwarz'; $IntFarb = '2'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '402') { $Farbe = 'grau'; $IntFarb = '8'; $FarbCode = '#999999'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '41') { $Farbe = 'schwarz melliert'; $IntFarb = '2'; $FarbCode = '#333333'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '410') { $Farbe = 'dunkelgrau'; $IntFarb = '8'; $FarbCode = '#666666'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '42') { $Farbe = 'graumelliert'; $IntFarb = '18'; $FarbCode = '#CCCCCC'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '423') { $Farbe = 'benzingrau'; $IntFarb = '8'; $FarbCode = '#CCCCFF'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '424') { $Farbe = 'rauchgrau'; $IntFarb = '8'; $FarbCode = '#999999'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '425') { $Farbe = 'steingrau'; $IntFarb = '8'; $FarbCode = '#330033'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '43') { $Farbe = 'weißmelliert'; $IntFarb = '1'; $FarbCode = '#CCCCFF'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '506') { $Farbe = 'paprikarot'; $IntFarb = '13'; $FarbCode = '#FF6600'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '53') { $Farbe = 'gelb'; $IntFarb = '6'; $FarbCode = '#FFCC00'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '531') { $Farbe = 'hellgrün'; $IntFarb = '5'; $FarbCode = '#FFFF99'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '532') { $Farbe = 'senf'; $IntFarb = '6'; $FarbCode = '#CC9900'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '534') { $Farbe = 'mango'; $IntFarb = '13'; $FarbCode = '#FF9966'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '601') { $Farbe = 'dunkelweiß'; $IntFarb = '1'; $FarbCode = '#FFFFCC'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '646') { $Farbe = 'marmor'; $IntFarb = '8'; $FarbCode = '#663333'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '655') { $Farbe = 'mandel'; $IntFarb = '7'; $FarbCode = '#CC9966'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '658') { $Farbe = 'elefantengrau'; $IntFarb = '8'; $FarbCode = '#999966'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '672') { $Farbe = 'dunkelgrau melliert'; $IntFarb = '8'; $FarbCode = '#666699'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '680') { $Farbe = 'café'; $IntFarb = '10'; $FarbCode = '#330000'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '704') { $Farbe = 'kamelienrosa'; $IntFarb = '9'; $FarbCode = '#FF9999'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '705') { $Farbe = 'gerberapink'; $IntFarb = '9'; $FarbCode = '#FF6666'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '800') { $Farbe = 'himmelblau'; $IntFarb = '19'; $FarbCode = '#0099FF'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '345') { $Farbe = 'forest'; $IntFarb = '5'; $FarbCode = '#339966'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '533') { $Farbe = 'jaffa'; $IntFarb = '13'; $FarbCode = '#FFCC33'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '645') { $Farbe = 'kakao'; $IntFarb = '10'; $FarbCode = '#663300'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2259') { $Farbe = 'weiss/rot'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#FF0000'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2251') { $Farbe = 'weiss/schwarz'; $IntFarb = '1'; $FarbCode = '#FFFFFF,#000000'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2701') { $Farbe = 'schwarz/gelb'; $IntFarb = '2'; $FarbCode = '#000000,#FFCC00'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2702') { $Farbe = 'gelb/schwarz'; $IntFarb = '6'; $FarbCode = '#FFCC00,#000000'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '2550') { $Farbe = 'creme/kakao'; $IntFarb = '1'; $FarbCode = '#FFFFCC,#663300'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '806') { $Farbe = 'marine/grau'; $IntFarb = '4'; $FarbCode = '#000066;#999999'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '668') { $Farbe = 'creme'; $IntFarb = '1'; $FarbCode = '#FFFFCC'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '900') { $Farbe = 'vat weiss'; $IntFarb = '1'; $FarbCode = '#FFFFFF'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '440') { $Farbe = 'vat schwarz'; $IntFarb = '2'; $FarbCode = '#000000'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '710') { $Farbe = 'vat rot '; $IntFarb = '3'; $FarbCode = '#FF0000'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '820') { $Farbe = 'vat marine'; $IntFarb = '20'; $FarbCode = '#000066'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '859') { $Farbe = 'royal blau'; $IntFarb = '4'; $FarbCode = '#000099'; }
			else if ($Hersteller == 'Switcher' && $Farbnummer == '442') { $Farbe = 'vat grau'; $IntFarb = '8'; $FarbCode = '#CCCCCC'; }
		else if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_color Farbeeinstellung für Switcher stimmen nicht mit der Mapping ueberein!");
		
		/**  $Hersteller == 'ID Line'**/
		if ($Hersteller == 'ID Line' && $Farbnummer == '1') { $Farbe = 'weiß'; $IntFarb = '1'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '2') { $Farbe = 'schwarz'; $IntFarb = '2'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '3') { $Farbe = 'kitt'; $IntFarb = '7'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '4') { $Farbe = 'sand'; $IntFarb = '7'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '5') { $Farbe = 'gelb'; $IntFarb = '6'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '6') { $Farbe = 'orange'; $IntFarb = '13'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '7') { $Farbe = 'pink'; $IntFarb = '9'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '8') { $Farbe = 'rot'; $IntFarb = '3'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '9') { $Farbe = 'bordeaux'; $IntFarb = '3'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '10') { $Farbe = 'hellblau'; $IntFarb = '19'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '11') { $Farbe = 'tükis'; $IntFarb = '4'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '12') { $Farbe = 'königs-blau'; $IntFarb = '4'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '13') { $Farbe = 'navy'; $IntFarb = '20'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '14') { $Farbe = 'indigo'; $IntFarb = '4'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '15') { $Farbe = 'lime'; $IntFarb = '5'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '16') { $Farbe = 'apple'; $IntFarb = '5'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '17') { $Farbe = 'olive'; $IntFarb = '21'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '18') { $Farbe = 'grün'; $IntFarb = '5'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '19') { $Farbe = 'koks'; $IntFarb = '8'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '20') { $Farbe = 'snow meliert'; $IntFarb = '1'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '21') { $Farbe = 'grau meliert'; $IntFarb = '18'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '22') { $Farbe = 'graphit meliert'; $IntFarb = '18'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '23') { $Farbe = 'khaki'; $IntFarb = '21'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '24') { $Farbe = 'hellgrau'; $IntFarb = '8'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '25') { $Farbe = 'stahlgrau'; $IntFarb = '8'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '26') { $Farbe = 'denim'; $IntFarb = '4'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '27') { $Farbe = 'cerise'; $IntFarb = '9'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '28') { $Farbe = 'blau'; $IntFarb = '4'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '29') { $Farbe = 'azurblau'; $IntFarb = '19'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '30') { $Farbe = 'mokka'; $IntFarb = '10'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '31') { $Farbe = 'grau'; $IntFarb = '8'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '32') { $Farbe = 'mint'; $IntFarb = '5'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '33') { $Farbe = 'lila'; $IntFarb = '15'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '34') { $Farbe = 'off-white'; $IntFarb = '1'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '35') { $Farbe = 'braun'; $IntFarb = '10'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '36') { $Farbe = 'flaschengrün'; $IntFarb = '5'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '37') { $Farbe = 'kiwigrün'; $IntFarb = '5'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '38') { $Farbe = 'rosa'; $IntFarb = '9'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '39') { $Farbe = 'avocado'; $IntFarb = '5'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '40') { $Farbe = 'natur'; $IntFarb = '1'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '41') { $Farbe = 'dunkelgrau'; $IntFarb = '8'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '42') { $Farbe = 'sandhell'; $IntFarb = '7'; }
			else if ($Hersteller == 'ID Line' && $Farbnummer == '43') { $Farbe = 'cyan'; $IntFarb = '19'; }
		else if (DEBUGGER>=1) fwrite($dateihandle, "dmc_map_color Farbeeinstellung für ID Line stimmen nicht mit der Mapping ueberein!"); 
	
		// $MapType is color or colorcode
		if ($MapType=='colorcode')
			return $FarbCode;
		if ($MapType=='intcolorcode')
			return $IntFarb;
		else 
			return $Farbe;
	} // end function dmc_map_color 

?>	