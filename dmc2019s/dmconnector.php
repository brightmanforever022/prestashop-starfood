<?php
/************************************************
*                                            	*
*  dmConnector  for shops						*
*  dmconnector.php								*
*  Hauptprogramm								*
*  Copyright (C) 2008-2012 DoubleM-GmbH.de		*
*                                               *
*************************************************/ 
/*
Anderungen am 01.09.08
- Es kann auch keine Artikel_ID übergeben werden. Dann wird diese anhand der übergebenen Artikelnummer zugeordnet:
- Wenn keine Bilddatei übermittelt wird -> nopic.gif
- Bugbehebung Fehlerhafte Bilddartei bei nicht erfolgter Übergabe eines Dateinamens (z.B. Artikelnummer)
- Standard-Bilder nur zuordnen, wenn im Thumbnail-Ordner vorhanden, ansonsten nopic.gif
Änderungen am 21.11.08
- VPE-Wert und  VPE-Art ermitteln - werden als attribue1@attribe2@... übergeben
-  Als VPE kann nun neben der Einheit (Stück) ALTERNATIV auch der Wert getrennt mit @ übergeben werden (24@Stück)
-  BLUEGATE SUMA OPTIMIZER für Produkte und Kategorien eingebunden
Änderungen am 03.12.08
- Details können übertragen werden: Funktion dm_details (import)
Änderungen am 18.12.08
- Unterstütung von Produkt Templates (PRODUCT_TEMPLATE  und OPTIONS_TEMPLATE ) über defin es
Änderungen am 07.01.09
- Kategoriename auch als Fremdsprache anlegen.
Änderungen am 22.01.09
- writecustomers überarbeitet, u.a. md5-Verschlüsselung für Passwort Kundenexport 
Änderungen am 30.01.09
- Bildverarbeitung für Klein - und Gross geschriebene Bilder überarbeitet
Änderungen am 04.02.09
- Bildverarbeitung auch bei Update der Artikel
- Arikelnummer@EANnummer Unterstützung
Änderungen am 12.02.09
- Extrabilder können Details wie speziellen Ordner und Extensions besitzen.
Änderungen am 16.02.09
- FSK 18 kann über define mit true freigeschlatet werden
Bug am 17.02.09
- Fehlerhafte Preiszuordnung, wenn in WaWi Staffelpreise angegeben.
Änderungen am 25.02.09
Als customers_date_account_created wird die $customers_status übergeben
Änderungen am 27.05.09
- writecustomers überarbeitet für Anlage Auslandskunden Country und Zone
Änderung am 30.05.09
- Unterstützung Neue Varianten-Tabelle products_dmc
Änderung am 04.07.09
- Unterstützung Kategorie_IDS (000.005.008) von PCK
Änderung am 24.07.09
- Einfügen von KATEGORIE_TRENNER in definitions und hier
- utf8_decode für utf8 in latin1
Änderung am 05.05.2010
- Erweiterung für ZENCART
Änderung am 05.05.2010
- Erweiterung für HHG Multistore
Änderung am 06.06.2010
- Seperates Preis und Bestands-Update auch für Variantenartikel
Änderungen am 16.07.2010
-  Als VPE kann nun neben der Einheit (Stück) ALTERNATIV auch der Wert - NEU mit dividend getrennt mit : - getrennt mit @ übergeben werden (250:100@100 ml)
- Ergebnis: Preis fuer 250ml -> pro 100 ml = Preis/250*100, bei 5 Euro pro 250ml = 2 Euro pro 100 ml
Änderungen am 28.02.2011
-  Anpassungen an Veyton
Änderungen am 11.03.2011
- Anpassungen an Presta
- Sortierung kann als Ergänzung zum Aktiv Flag für Kategorien, Haupt- und Variantenartikel mit übergegeben werden, z.B. 1@90 as Aktiv_Sortierung
Erweiterungen am 07.12.2011
- Erweiterungen um diverse Kategorie Felder wie Kategorie_SEO 
- Bildunterstuetzung für Kategorien
Erweiterungen am 12.12.2011
- SHOPSYSTEM == 'gambiogx'  -> reset_categories_index
Erweiterung am 08.03.2012
- Erweiterte Level Unterstützung von Presta Kategorien
Erweiterung am 12.03.2012
- (Er)neu(te) Implementierung Multi Kategorie Unterstuertzung
Erweiterung am 13.03.2012
- Umstellung auf dmc_write_art
Erweiterung am 15.03.2012
- Umstellung auf dmc_write_cat
Erweiterung am 16.03.2012
- Umstellung auf dmc_set_details
- dynamische inkludierung von funktionen und conf dateien
Erweiterung 22.05.2012
- Installationsprogramm dmconnector.php?action=dmc_install&user=info@mobilize.de&password=dmconnector123
Erweiterung 10.08.2012
- Unterstuetzung mehrere Order Status mit ORDER_STATUS_GET durch @ getrennter Order Status
Erweiterung 06.02.2013
- Art_Update für Joomla vorbereitet
Erweiterung 24.7.2013
- Die Action Status wurd differenzierter auswertbar gemacht nach  <Status>write_artikel_begin</Status>, _categorie_begin etc
Änderungen am 24.08.2016
- writecustomers erweitert um die automatisch ergänzung von Kundengruppen für xtc, gambio etc.
*/
ini_set("display_errors", 1);
error_reporting(E_ERROR & ~E_NOTICE & ~E_DEPRECATED);

define('_DMC_ACCESSIBLE',true);
define('_VALID_XTC',true);
define('VALID_DMC',true);

// configurationen
include('conf/definitions.inc.php');
include('conf/configure_export.php');

if (strpos(strtolower(SHOPSYSTEM), 'zencart') !== false) {
	require('conf/configure_shop_zen.php');
} else if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false) {
	require('conf/configure_shop_hhg.php');
} else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
	require('conf/configure_shop_veyton.php');
	//require_once('../core/application_top_export.php');
} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
	require('conf/configure_shop_presta.php');
	//require('../core/application_top_export.php');
} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
	require('conf/configure_shop_virtuemart.php');
} else if (strpos(strtolower(SHOPSYSTEM), 'joomshopping') !== false) {
	require('conf/configure_shop_joomshopping.php');
	//require('../core/application_top_export.php');
} else if (strpos(strtolower(SHOPSYSTEM), 'woocommerce') !== false) {
	require('conf/configure_shop_woocommerce.php');
} else if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) {
	require('conf/configure_shop_shopware.php');
	include_once ('functions/shopware_api_client.php');			// Shopware API Definitions	
} else if (strpos(strtolower(SHOPSYSTEM), 'osc') !== false) {
	require('conf/configure_shop_osc.php');
} else { 
	//if (strpos(strtolower(SHOPSYSTEM), 'gambiogx') !== false)
	//	include('./inc/gm_get_env_info.inc.php');
	// if (is_file(DIR_FS_DOCUMENT_ROOT.'/includes/application_top_export.php')) require(DIR_FS_DOCUMENT_ROOT.'/includes/application_top_export.php');
	if (is_file('../includes/application_top_export.php')) require_once('../includes/application_top_export.php');
	else if (is_file('../../includes/application_top_export.php')) require_once('../../includes/application_top_export.php');
	else {
		if (is_file('./conf/configure_shop_presta.php')) require('./conf/configure_shop_presta.php');
		else if (is_file('./conf/configure_shop_hhg.php')) require('./conf/configure_shop_hhg.php');
		else if (is_file('./conf/configure_shop_zen.php')) require('./conf/configure_shop_zen.php');
		else if (is_file('./conf/configure_shop_veyton.php')) require('./conf/configure_shop_veyton.php');
		$action="dmc_install"; // wohl kein xtc Shop, daher Install		
	}
}

	if ( strpos(strtolower(SHOPSYSTEM), 'zencart') !== false || strpos(strtolower(SHOPSYSTEM), 'hhg') !== false 
		|| strpos(strtolower(SHOPSYSTEM), 'veyton') !== false || strpos(strtolower(SHOPSYSTEM), 'presta') !== false
		|| strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false || strpos(strtolower(SHOPSYSTEM), 'woocommerce') !== false
		|| strpos(strtolower(SHOPSYSTEM), 'osc') !== false || strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) {
		/*	require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
			require_once(DIR_FS_INC . 'xtc_redirect.inc.php');
			require_once(DIR_FS_INC . 'xtc_rand.inc.php');*/
			// connect to database
			//if ( strpos(strtolower(SHOPSYSTEM), 'shopware') === false)
	//		xtc_db_connect() or die('Unable to connect to database server!');
	} else {
		include(DIR_FS_DOCUMENT_ROOT.'admin/includes/classes/'.IMAGE_MANIPULATOR);
	}

	include('./functions/dmc_db_functions.php');
	include('./functions/dmc_functions.php');

	if (DEBUGGER>=1)
	{ 
		date_default_timezone_set('Europe/Berlin');
		$datum = date("d.m.Y");
		$uhrzeit = date("H:i");
		$daten = "\n***********************************************************************\n";
		$daten .= "************************* dmconnector Shop *****************************\n";
		$daten .= "***********************************************************************\n";
		$daten .= $datum." - ".$uhrzeit." Uhr\n";
		if (LOG_ROTATION=='size' && is_numeric(LOG_ROTATION_VALUE)) {
			if (!file_exists(LOG_DATEI)) {
				$dateihandle = fopen(LOG_DATEI,"w"); // LOG File erstellen
				fwrite($dateihandle, "<?php \n exit;");
			} else if ((filesize(LOG_DATEI)/1048576)>LOG_ROTATION_VALUE) {
				$dateihandle = fopen(LOG_DATEI,"w"); // LOG File neu erstellen
				fwrite($dateihandle, "<?php \n exit;");
			} else {
				$dateihandle = fopen(LOG_DATEI,"a");
			} 
		} else {
			if (!file_exists(LOG_DATEI)) {
				$dateihandle = fopen(LOG_DATEI,"w"); // LOG File erstellen
				fwrite($dateihandle, "<?php \n exit;");
			} else {
				$dateihandle = fopen(LOG_DATEI,"a");
			} 	
		}
		fwrite($dateihandle, $daten);	
		
	}
				// Uebergebene Daten loggen
	if (DEBUGGER>=1 && PRINT_POST) print_post($dateihandle);
	// Posts
	$action = (isset($_POST['action']) ?
		  $_POST['action'] : $_GET['action']);

	$user = (isset($_POST['user']) ?
		  $_POST['user'] : $_GET['user']);

	$password = (isset($_POST['password']) ?
		$_POST['password'] : $_GET['password']);

	// Pruefe auf Kodierung
	if (substr($password,0,3)=='rcm')
	  {
		$password=base64_decode(substr($password,3,40));
	  }

	// Default-Sprache (deutsch = 2)
	$language_id_std = 2;
	
	// GGfls API Verbindung herstellen
	if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) {
		$client=dmc_get_shopware_api_client(API_URL,$user,$password); // API Verbindung zu Shopware herstellen	
	
/*		if ($client->get('version') == '' ) {
			$status = "<XML><STATUS>0</STATUS><FEHLER>API Zugang verwehrt</FEHLER></XML>";
			echo $status;
			fwrite($dateihandle, $status);	
			exit;
		} */
	}
	// Aktionsauswahl
	switch ($action)
	{  
		// Verbindung zum Shop überprüfen.	 
		case 'check_status':
			if (CheckLogin($user,$password)) CheckStatus();
			exit;

		// Installationsprogramm starten zum Shop überprüfen.	 
		case 'dmc_install':
			if (CheckLogin($user,$password)) {
				if (strpos(strtolower(SHOPSYSTEM), 'veyton') === false)
					include('./install/install_table.php');
				else
					include('./install/install_table_veyton.php');
				include('./install/dmc_install.php');
				showDefinitions($user,$password);
			}
			exit;
			
		// Anzahl der Bestellungen prüfen
		case 'check_orders':
		  if (CheckLogin($user,$password)) echo CheckOrders(); // in functions dmc_db_functions
		  exit;

		case 'write_artikel':
			fwrite($dateihandle, "write_artikel");	
			
			if (CheckLogin($user,$password))
			{
				fwrite($dateihandle, " - 240 - ");	
				// Da veyton xtCore/main $_POST Werte zerschiessen kann, werden Funktions vor  xtCore/main.php eingebunden
				// Gepostete Werte ermitteln
				if (is_file('userfunctions/products/dmc_get_posts.php')) include ('userfunctions/products/dmc_get_posts.php');
				else include ('functions/products/dmc_get_posts.php');
				
				if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
					if (is_file('../../../xtCore/main.php')) include ('../../../xtCore/main.php');
					else if (is_file('../../xtCore/main.php')) include ('../../xtCore/main.php');
					else if (is_file('../xtCore/main.php')) include ('../xtCore/main.php');
					else fwrite($dateihandle, "class main not found \n");
					if (is_file('../../../xtFramework/classes/class.ImageProcessing.php')) include ('../../../xtFramework/classes/class.ImageProcessing.php');
					else if (is_file('../../xtFramework/classes/class.ImageProcessing.php')) include ('../../xtFramework/classes/class.ImageProcessing.php');
					else if (is_file('../xtFramework/classes/class.ImageProcessing.php')) include ('../xtFramework/classes/class.ImageProcessing.php');
					else fwrite($dateihandle, "class ImageProcessing not found \n");
				}
				
					// Standard Artikel anlegen
					// decrepated 12.02.2012  WriteArtikel(); 
					// Gepostete Werte ermitteln
					// Abweichend für woocommerce und shopware
				if (strpos(strtolower(SHOPSYSTEM), 'woo') !== false) {
					include ('dmc_write_art_woocommerce.php');		
				} else if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) {
					include ('dmc_write_art_shopware.php');	
				} else {
					include ('dmc_write_art.php');						
				}
				
				dmc_write_art(	$ExportModus, $Artikel_ID, $Kategorie_ID, $Hersteller_ID,$Artikel_Artikelnr,$Artikel_Menge,
										$Artikel_Preis,$Artikel_Preis1,$Artikel_Preis2,$Artikel_Preis3,$Artikel_Preis4,$Artikel_Gewicht,$Artikel_Status,$Artikel_Steuersatz,
										$Artikel_Bilddatei,$Artikel_VPE,$Artikel_Lieferstatus,$Artikel_Startseite,$SkipImages,$Aktiv,$Aenderungsdatum,$Artikel_Variante_Von,
										$Artikel_Merkmal,$Artikel_Auspraegung,$Artikel_Bezeichnung ,$Artikel_Langtext ,$Artikel_Kurztext,$Artikel_Sprache,$Artikel_MetaText,$Artikel_MetaDescription,$Artikel_MetaKeyword,$Artikel_MetaUrl);
				
		    }
			exit;
	  
		case 'setSpecials':
			if (CheckLogin($user,$password))
			{
				if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false) {
					include('functions/hhg_set_conf_specials.php');
					HHGSetConfSpecials();
				} else {
					include ('dmc_set_specials.php');
					dmc_set_specials();
				}
			}
			exit;
      
	 case 'setXsell':
     		 if (CheckLogin($user,$password))
     		 {
     	 	 	 include ('functions/products/dmc_set_xsell.php');		
			 dmc_set_xsell();
		}
      		exit;
      
     case 'Art_Update':
      if (CheckLogin($user,$password))
      {
		include('./functions/products/dmc_art_functions.php');
		Art_Update();
      }
      exit;

    case 'write_categorie':
      if (CheckLogin($user,$password))
      {
        // Standard Kategorie anlegen
		// decrepated 15.02.2012 - WriteCategorie(); 
		if (is_file('dmc_write_cat.php')) {
			include ('dmc_write_cat.php');
			dmc_write_cat();
		} else {
			WriteCategorie();
		}
      }
      exit;

    case 'read_artikel':
      if (CheckLogin($user,$password))
      {
        ReadArtikel();
      }
      exit;

    case 'get_artikel_image':
      if (CheckLogin($user,$password))
      {
        GetArtikelImage();
      }
      exit;

    case 'read_hersteller':
      if (CheckLogin($user,$password))
      {
        ReadHersteller();
      }
      exit;

    case 'write_hersteller':
      if (CheckLogin($user,$password))
      {
        WriteHersteller();
      }
      exit;

    case 'delete_artikel':
      if (CheckLogin($user,$password))
      {
        DeleteArtikel();
      }
      exit;

    case 'write_customer':
      if (CheckLogin($user,$password))
      {
        if (strpos(strtolower(SHOPSYSTEM), 'virtue') !== false) {
			include ('dmc_write_cust_virtuemart.php');		
			dmc_write_customers();
	    } else if (strpos(strtolower(SHOPSYSTEM), 'woo') !== false) {
			include ('dmc_write_cust_woocommerce.php');		
			dmc_write_customers();
	    } else {
			// WriteCustomers();
			include ('dmc_write_customer.php');		
			dmc_write_customers();
		}
	  }  
      exit;
      
    case 'order_update':
      if (CheckLogin($user,$password))
      {
        OrderUpdate();
      }
      exit;

	// Neuer Order Status aus ERP
	case 'setOrderStatus':
      if (CheckLogin($user,$password))
      {
		// in functions/dmc_functions.php
		if (strpos(strtolower(SHOPSYSTEM), 'shopware') === false && strpos(strtolower(SHOPSYSTEM), 'woo') === false && strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false)
			dmc_set_OrderStatus();
      }
      exit;
	  
	case 'setDetails':
	//	fwrite($dateihandle, "case=".$action." mit ".DIR_FS_DOCUMENT_ROOT.'admin/dm_details.php'."\n");
		if (CheckLogin($user,$password))
		{
			fwrite($dateihandle, "do=".$action.";\n");
			// Details anlegen
			// dm_details.php decrepated 16.02.2012 - WriteCategorie(); 
			if (is_file('dmc_set_details.php')) {
				include('./functions/products/dmc_art_functions.php');		
				include ('./dmc_set_details.php');
				dmc_set_details();
			} else {
				include ('./dm_details.php');
				SetDetails();
			}
		}
		exit;

	// Verarbeitung von bestimmten Status zB Beginn Artikelabgleich etc. # 24.7.2013
	case 'Status':
		  if (CheckLogin($user,$password))
		  {
			if (is_file('dmc_status.php')) {
				include ('dmc_status.php');
				dmc_status();
			} 
		  }
		  exit;

		default:
			Show_Version();
			exit;
	} // switch
	
	// Funktion wird fuer Kompatibilitaet u.a. mit xtc Bildfunktionalitaeten benoetigt
	function clear_string($value) {
	  $string=str_replace("'",'',$value);
	  $string=str_replace(')','',$string);
	  $string=str_replace('(','',$string);
	  $array=explode(',',$string);
	  return $array;
	}
/* ausgegliedert in functions/dmc_db_functions.php
function CheckLogin($user,$password)
{

	 
		
	if (DEBUGGER>=1)
	{
		$daten = "CheckLogin";
		$dateiname=LOG_DATEI;	
		$dateihandle = fopen($dateiname,"a");
		fwrite($dateihandle, $daten);
		fwrite($dateihandle, "\n");
	}
  $ok=True;

  // Ist nicht md5 verschlüsselt, wenn mit %% und muss nachgeholt werden
  if (substr($password,0,2)=='%%')
  {
    $password=md5(substr($password,2,40));
  }

  // Wenn kein Username dann Abbruch
  if ($user=='')
  {
    $ok=False;
  }

  
	if (strpos(strtolower(SHOPSYSTEM), 'zencart') !== false)
			$customers_query=dmc_db_query("select customers_id,customers_password" .
                           " from " . TABLE_CUSTOMERS .
                           " where customers_email_address = '$user'");
	else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)
			$customers_query=dmc_db_query("select id_employee,passwd" .
                           " from " . TABLE_USERS .
                           " where email = '$user' and active=1");
	else 
			$customers_query=dmc_db_query("select customers_id,customers_status,customers_password" .
                           " from " . TABLE_CUSTOMERS .
                           " where customers_email_address = '$user'");
		


  if (!($customers = dmc_db_fetch_array($customers_query)))
  {
    $ok=False;
  }
  else
  {
    // check if customer is Admin
    if ($customers['customers_status']!='0' && strpos(strtolower(SHOPSYSTEM), 'zencart') === false && strpos(strtolower(SHOPSYSTEM), 'presta') === false)
    {
      $ok=False;
    }
  }

  if (!$ok)
  {
    // Nicht als XML ausgeben, da Textausgabe direkt als Fehler gesehen wird, während ein <Status> auch für die
    // Versionsnummer ausgewertet wird
    echo "Anmeldung: Name/Passwort ".$_GET['password']." nicht korrekt!";
	if (DEBUGGER>=1) fwrite($dateihandle, "Anmeldung: Name/Passwort nicht korrekt!\n");
	
  }

  return $ok;
}*/

function Show_Version()
{
	 
		
	if (DEBUGGER>=1) {
		$daten = "Show_Version";
		$dateiname=LOG_DATEI;	
		$dateihandle = fopen($dateiname,"a");
		fwrite($dateihandle, $daten);
		fwrite($dateihandle, "\n");
	}
	
  global $action;

 // $version_datum = '2009.01.31';

  echo '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
       "<STATUS>\n" .
       "  <STATUS_INFO>\n" .
       "    <ACTION>$action</ACTION>\n" .
       "    <CODE>Willkommen</CODE>\n" .
	   "    <SCRIPT_DEFAULTCHARSET>" . htmlspecialchars(ini_get('default_charset')) . "</SCRIPT_DEFAULTCHARSET>\n" .
       "  </STATUS_INFO>\n" .
       "</STATUS>\n\n";
}


 function CheckStatus()
{
	 
		
	if (DEBUGGER>=1) {
		$daten = "check_status";
		$dateiname=LOG_DATEI;	
		$dateihandle = fopen($dateiname,"a");
		fwrite($dateihandle, $daten);
		fwrite($dateihandle, "\n");
	}
	
	// Übergabe z.B. action=check_status&user=admin&passwort=admin
	
  global $action;
  

  $status= '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
       "<STATUS>\n" .
       "  <STATUS_INFO>\n" .
       "    <ACTION>$action</ACTION>\n" .
       "    <CODE>STATUS OK</CODE>\n" .
       
       
       
       "    <SCRIPT_DEFAULTCHARSET>" . htmlspecialchars(ini_get('default_charset')) . "</SCRIPT_DEFAULTCHARSET>\n" .
       "  </STATUS_INFO>\n" .
       "</STATUS>\n\n";
	   
	 echo $status;  
	 fwrite($dateihandle, $status);
	   
}

// Update von Preis und Bestand
function Art_Update() 
{
	global $action, $dateihandle;

	$xtc_mehrlagermodul=false;
	
	$ExportModus = ($_POST['ExportModus']);
	$shop_id = (($_POST['Artikel_ID']));						// Anstelle der Artikel ID kann eine Shopid übergeben werden.
	$Artikel_Artikelnr = $_POST['Artikel_Artikelnr'];
	//$Artikel_Artikelnr = explode ( '@', $_POST['Artikel_Artikelnr'])[0];
	//$Artikel_ArtikelEAN = explode ( '@', $_POST['Artikel_Artikelnr'])[1];
	$Artikel_Menge = ($_POST['Artikel_Menge']);
	if ($Artikel_Menge=='0E-14')  $Artikel_Menge = 0;
	$Artikel_Preis = ($_POST['Artikel_Preis']);
	$Artikel_Status = ($_POST['Artikel_Status']);
	$Artikel_Steuersatz = ($_POST['Artikel_Steuersatz']);
	$Artikel_Lieferstatus = ($_POST['Artikel_Lieferstatus']);
    
	// Sonderfunktion Modified Matrix mit Übergabe Hauptartikelnummer, Farbe und Groesse
	$user_modified_martix=false;
	if ($user_modified_martix==true) {
		if (DEBUGGER>=1)
				fwrite($dateihandle, "Sonderfunktion Modified Matrix mit Übergabe Hauptartikelnummer, Farbe und Groesse $Artikel_Artikelnr \n");    
		$temp=explode("@",$Artikel_Artikelnr);
		$Artikel_Artikelnr=$temp[0];
		$farbe=$temp[1];
		$groesse=$temp[2];
		
		// Artikel laden
		$Artikel_ID=dmc_get_id_by_artno($Artikel_Artikelnr);
		
		if ($Artikel_ID=="") { 
			if (DEBUGGER>=1)
				fwrite($dateihandle, "Art_Update - ".date("l d of F Y h:i:s A")." - Artikel $Artikel_Artikelnr existiert NICHT\n");      
		}
		else
		{
			$exists = 1;      
			if (DEBUGGER>=1)
				fwrite($dateihandle, "Art_Update - ".date("l d of F Y h:i:s A")." - Artikel $Artikel_Artikelnr mit ID $Artikel_ID fuer Bestandupdate MATRIX existiert\n");      
			$language_id=1;
			
			// products_attributes_id ermitteln Groesse
			$query=	"select pa.products_attributes_id AS result from `products_attributes` pa INNER JOIN products_options_values pov".
					" ON pa.options_values_id = pov.products_options_values_id ".
					" WHERE  pa.products_id=".$Artikel_ID." AND pov.language_id=".$language_id." AND pov.products_options_values_name='".$groesse."' ";
			$sql_query = dmc_db_query($query);
			if ($sql_result = dmc_db_fetch_array($sql_query)) {
				$products_attributes_id_groesse=$sql_result['result'];
			} else {
				$products_attributes_id_groesse=0;
			}
			// products_attributes_id ermitteln Farbe
			$query=	"select pa.products_attributes_id AS result from `products_attributes` pa INNER JOIN products_options_values pov".
					" ON pa.options_values_id = pov.products_options_values_id ".
					" WHERE  pa.products_id=".$Artikel_ID." AND pov.language_id=".$language_id." ".
					" AND (pov.products_options_values_name='".$farbe."' OR pov.products_options_values_name='".utf8_decode($farbe)."' )";
			// echo "$query \n";
			$sql_query = dmc_db_query($query);
			if ($sql_result = dmc_db_fetch_array($sql_query)) {
				$products_attributes_id_farbe=$sql_result['result'];
			} else {
				$products_attributes_id_farbe=0;
			}
			if (DEBUGGER>=1)
				fwrite($dateihandle, "id groesse=$products_attributes_id_groesse und farbe=$products_attributes_id_farbe \n");  
			// matrix_value_id ermitteln
			$query ="select matrix_value_id AS result from `products_options_matrix_values` ".
					" where (horiz_attribute=".$products_attributes_id_groesse." AND vert_attribute=".$products_attributes_id_farbe.") ".
					" OR (horiz_attribute=".$products_attributes_id_farbe." AND vert_attribute=".$products_attributes_id_groesse.") ";
			//	 echo "$query \n";	
			$sql_query = dmc_db_query($query);
			if ($sql_result = dmc_db_fetch_array($sql_query)) {
				$matrix_value_id=$sql_result['result'];
			} else {
				$matrix_value_id=0;
			}
			
			if ($matrix_value_id==0) {
				echo "Fehler, Matrix fuer diesen Artikel nicht gefunden \n";
				if (DEBUGGER>=1)
					fwrite($dateihandle, "Fehler, Matrix fuer diesen Artikel nicht gefunden \n");  
				$fehler = "Fehler, Matrix fuer diesen Artikel nicht gefunden \n";
			} else {
				if (DEBUGGER>=1)
					fwrite($dateihandle, "id matrix_value_id=$matrix_value_id \n  \n");  
				
				// Update Bestand auf matrixtabelle
				$query ="UPDATE `products_options_matrix_values` SET stock=".$Artikel_Menge." WHERE matrix_value_id=".$matrix_value_id;
				dmc_db_query($query);
				if (DEBUGGER>=1)
					fwrite($dateihandle,  "Update erfolgt  \n ");
			}
		} 
	} else { 
	
		// Artikel laden
		$Artikel_ID=dmc_get_id_by_artno($Artikel_Artikelnr);
		
		if ($Artikel_ID!="") { 
			$exists = 1;      
			$hauptartikel = true;
			if (DEBUGGER>=1)
				fwrite($dateihandle, "Art_Update - ".date("l d of F Y h:i:s A")." - Artikel $Artikel_Artikelnr mit ID $Artikel_ID fuer Preisupdate auf Shop  $shop_id  existiert\n");      
		}
		else
		{
			// Artikel existiert nicht
			if (DEBUGGER>=1) 	fwrite($dateihandle, "Art_Update - ".date("l d of F Y h:i:s A")." - Artikel (als Hauptartikel) exisitert nicht...\n");
			$exists = 0;	
			$hauptartikel=false;
			
			if(strpos(strtolower(SHOPSYSTEM), 'gambio') !== false) {
				// Gambio
				// Überprüfen, ob Artikel als Variantenartikel existiert und ggfls die ArtikelID von bestehendem Artikel ermitteln
				$cmd = "select products_attributes_id from " . TABLE_PRODUCTS_ATTRIBUTES .
						" where attributes_model = '$Artikel_Artikelnr'";
							
				if (DEBUGGER>=99) fwrite($dateihandle, "Query...".$cmd."\n");
							
				$sql_query = dmc_db_query($cmd);
						 
				if ($sql_result = dmc_db_fetch_array($sql_query))
				{
					$exists = 1;
					// ArtikelID von bestehendem Artikel ermitteln
					$Artikel_ATTRIBUTE_ID = $sql_result['products_attributes_id'];
					if (DEBUGGER>=1) 	fwrite($dateihandle, "\n XXX Artikel hat Artikel_ATTRIBUTE_ID = $Artikel_ATTRIBUTE_ID ...\n");
				} else {
					// Artikel existiert  nicht
					if (DEBUGGER>=1) 	fwrite($dateihandle, date("l d of F Y h:i:s A")." - \n Artikel exisitert nicht...\n");
					$exists = 0;			
				}    
			} // end if shopsystem
						
		}
	  
		$mode='NONE';

		// woocommerce Giftgr... mit Variante ohne ArtNr
		$giftgr==false;
		if ($giftgr==true && $exists==1 && (strpos(strtolower(SHOPSYSTEM), 'woo') !== false))
		{	
			// Ermittlete Hauptartikel ID -> $Artikel_ID (siehe oben)
			// durchlaufe ALLE Unterartikel ID des Hauptartikel
			$cmd = "SELECT ID AS post_id FROM `".DB_PREFIX."posts` WHERE  `post_parent` = ".$Artikel_ID;
			$varianten_query = dmc_db_query($cmd);
			while ($varianten = dmc_db_fetch_array($varianten_query))
			{
				// Ermittle anhand Varianten 1 und 2 die korrekte ID des betreffenden Unterartikels
				// In Lieferstatus sind die Auspragungen wie blau@XXL enthalten
				$auspraegungen = explode ( '@', $Artikel_Lieferstatus);
				$cmd = "SELECT post_id FROM ltg_postmeta WHERE post_id = ".$varianten['post_id']." AND meta_value='".$auspraegungen[0]."' AND meta_value='".$auspraegungen[1]."' LIMIT 1";
					fwrite($dateihandle, "637 CMD ".$cmd." \n");
				
				$sql_query = dmc_db_query($cmd);
				// Variantenartikel ermitteln und aktualisieren
				if ($sql_result = dmc_db_fetch_array($sql_query)) {
					// Update price _price
					$cmd = "UPDATE ltg_postmeta SET meta_value='".$Artikel_Preis."' WHERE meta_value='_regular_price' AND post_id =".$sql_result['post_id'];
					fwrite($dateihandle, "644 CMD ".$cmd." \n");
					dmc_db_query($cmd);
					$cmd = "UPDATE ltg_postmeta SET meta_value='".$Artikel_Preis."' WHERE meta_value='_price' AND post_id =".$sql_result['post_id'];
					fwrite($dateihandle, "647 CMD ".$cmd." \n");
					dmc_db_query($cmd);
					// Update Bestand
					$cmd = "UPDATE ltg_postmeta SET meta_value='".$Artikel_Menge."' WHERE meta_value='_stock' AND post_id =".$sql_result['post_id'];
					fwrite($dateihandle, "651 CMD ".$cmd." \n");
					// _stock_status instock
					dmc_db_query($cmd);
				} else {
					$falscheVariante = true;
				}
			}
		}
		// Nur für existente Artikel einen Preisabgleich durchführen
		// -> sonst sinnlos und nicht redundante Daten
		else if ($exists==1 && ($hauptartikel || (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false)))
		{	
			if ($ExportModus=='PreisOnly')
			{
				// nur der Preis wird geändert
				if(strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
					$sql_data_array = array( 'published' => $Artikel_Status);	// in Produkt Tabelle
					if ($Artikel_Preis!='' && $Artikel_Preis>0)
						$sql_price_array = array( 'product_price' => $Artikel_Preis);	// in Preis Tabelle
				} else if(strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) {
					if ($Artikel_Status==true) $Artikel_Status=1;
					$preise = explode ( '@', $Artikel_Preis);
					$preisgruppen = array("EK", "EK", "EK", "EK","EK", "EK", "EK", "EK","EK");
					for($i = 0; $i < sizeof($preise);$i++)
					{
						$query="UPDATE s_articles_prices SET price=".$preise[$i]." WHERE pricegroup='".$preisgruppen[$i]."' AND `from`=1 ".
					//	"AND articledetailsID = ".$Artikel_ID;
						"AND articledetailsID = (select id from s_articles_details  WHERE  ordernumber ='".$Artikel_Artikelnr."')";
						if ($preise[$i]>0) dmc_sql_query($query);
					//	$query="UPDATE s_articles_details SET active=".$Artikel_Status." WHERE articleID =".$Artikel_ID;
					//	dmc_sql_query($query);
						fwrite($dateihandle, "Artikel ID ".$Artikel_ID." Shopware DB Preis ".$preise[$i]." (".$preisgruppen[$i].") aktualisiert (735).\n");
					}
					
				} else if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
					$sql_data_array = array('price' => $Artikel_Preis);
				} else if(strpos(strtolower(SHOPSYSTEM), 'woocommerce') !== false) {
					// Überprüfen, ob mehrere Preise angeben
					// woocommerce
					$preise = explode ( '@', $Artikel_Preis);
					for($i = 0; $i < sizeof($preise);$i++)
					{
						if ( $Artikel_Menge > 0) { 			// Artikel auf Lager
							$_stock_status= 'instock'; 		// Bestandsstatus
						} else { 							// Kein Bestand
							$_stock_status= 'outofstock'; 		// Bestandsstatus
						}	
						
						$Artikel_Status='visible';
						// update posts durchfuehren
						$table = "postmeta";
						// Status aendern
						// dmc_sql_update($table,  "meta_value='".$Artikel_Status."'" , "meta_key= '_visibility' AND post_id=".$Artikel_ID);	// (Sichtbarkeit wie visible)
						if ($i==0) {
							// Standardpreis
							// Preis und Bestandsupdate
							dmc_sql_update($table,  "meta_value='".$preise[0]."'" , "meta_key= '_price' AND post_id=".$Artikel_ID);			// (Preis) -> Wenn Aktionspreis, dann Aktionspreis, sonst regular price
							dmc_sql_update($table,  "meta_value='".$preise[0]."'" , "meta_key= '_regular_price' AND post_id=".$Artikel_ID);			// (Preis) -> Wenn Aktionspreis, dann Aktionspreis, sonst regular price
							//	dmc_sql_update($table,  "meta_value='".$Artikel_Menge."'" , "meta_key= '_stock' AND post_id=".$Artikel_ID);			// (Preis) -> Wenn Aktionspreis, dann Aktionspreis, sonst regular price
							//dmc_sql_update($table,  "meta_value='".$_stock_status."'" , "meta_key= '_stock_status' AND post_id=".$Artikel_ID);			// (Preis) -> Wenn Aktionspreis, dann Aktionspreis, sonst regular price	
						 } else if ($i==1) {
							// wooCommerce amazon Preis $preise[2]
							if ($preise[1]!='' && $preise[1]!='0') {
								dmc_sql_update($table,  "meta_value='".$preise[1]."'" , "meta_key= '_amazon_price' AND post_id=".$Artikel_ID);
								dmc_sql_update($table,  "meta_value='".$preise[1]."'" , "meta_key= '_amazon_minimum_price' AND post_id=".$Artikel_ID);
								dmc_sql_update($table,  "meta_value='".$preise[1]."'" , "meta_key= '_amazon_maximum_price' AND post_id=".$Artikel_ID);
								fwrite($dateihandle, "Artikel fuer WP-Lister freischalten (wplister_prepare_listing) \n");
								// Prepare a new listing from a WooCommerce product and apply a profile. This hook is available in version 1.5.0.5+.
								//Usage do_action('wplister_prepare_listing', $post_ID, $profile_id );
								// $profile_id = 1;
								// do_action('wplister_prepare_listing', $post_ID, $profile_id );
								do_action('wplister_relist_item', $Artikel_ID );
								fwrite($dateihandle, "Amazon UPDATE ELEDIGT  \n");

							}
						} else if ($i==2) {
							if ($preise[2]!='' && $preise[2]!='0') {
								// wooCommerce ebay Preis $preise[1]
								dmc_sql_update($table,  "meta_value='".$preise[2]."'" , "meta_key= '_ebay_start_price' AND post_id=".$Artikel_ID);
							}
						}
					}
						
				} else {
					if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false && USE_xt_plg_price_per_shop==true) {
						// Multishop veyton Zusatzmodul xt_plg_price_per_shop fuer Netto Preise nach Shop
					} else {
						// Standard
						$sql_data_array = array('price' => $Artikel_Preis);
					}
				}
				fwrite($dateihandle, "Artikel ID ".$Artikel_ID." soll neuen Preis bekommen.");
				fwrite($dateihandle, "\n");
			}
			if ($xtc_mehrlagermodul==true) {
				if ($ExportModus=='QuantityOnly')
				{
					// Store spezifischen Bestand setzen
					$sql_stock_data_array = array('stock' => $Artikel_Menge);
					if (DEBUGGER>=1) 	fwrite($dateihandle, "  Artikel  ".$Artikel_ID." bekommt neuen Bestand $Artikel_Menge für Store 1 ...\n");
					dmc_sql_update_array('xt_stocks', $sql_stock_data_array, "product_id = '$Artikel_ID' AND store_id=1");
					$cmd="select sum(stock) as gesamtbestand from xt_stocks where product_id = '$Artikel_ID'";
					$sql_query = dmc_db_query($cmd);
					// neuen Gesamt Bestand ermitteln
					if ($sql_result = dmc_db_fetch_array($sql_query))
						$gesamtbestand = $sql_result['gesamtbestand'];
					else 
						$gesamtbestand = 0;
					$sql_data_array = array('products_quantity' => $gesamtbestand);
					if (DEBUGGER>=1) 	fwrite($dateihandle, "  Artikel  ".$Artikel_ID." bekommt neuen Gesamtbestand $cmd = $gesamtbestand ...\n");
				}
				if ($ExportModus=='PreisQuantity')
				{
				
					 if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false && USE_xt_plg_price_per_shop==true) {
							$sql_stock_data_array = array('stock' => $Artikel_Menge);
							if (DEBUGGER>=1) 	fwrite($dateihandle, "  Artikel  ".$Artikel_ID." bekommt neuen Bestand $Artikel_Menge für Store 1 ...\n");
							dmc_sql_update_array('xt_stocks', $sql_stock_data_array, "product_id = '$Artikel_ID' AND store_id=1");
							$cmd="select sum(stock) as gesamtbestand from xt_stocks where product_id = '$Artikel_ID'";
							$sql_query = dmc_db_query($cmd);
							// neuen Gesamt Bestand ermitteln
							if ($sql_result = dmc_db_fetch_array($sql_query))
								$gesamtbestand = $sql_result['gesamtbestand'];
							else 
								$gesamtbestand = 0;
							if (DEBUGGER>=1) 	fwrite($dateihandle, "  Artikel  ".$Artikel_ID." bekommt neuen Gesamtbestand $cmd = $gesamtbestand ...\n");
							// Multishop veyton Zusatzmodul xt_plg_price_per_shop fuer Netto Preise nach Shop	
							$sql_data_array = array(
								'products_quantity' => $gesamtbestand,
								//   'price' => $Artikel_Preis 		-> Preis hier separat
							 );
					} else {
							$sql_stock_data_array = array('stock' => $Artikel_Menge);
							if (DEBUGGER>=1) 	fwrite($dateihandle, "  Artikel  ".$Artikel_ID." bekommt neuen Bestand $Artikel_Menge für Store 1 ...\n");
							dmc_sql_update_array('xt_stocks', $sql_stock_data_array, "product_id = '$Artikel_ID' AND store_id=1");
							$cmd="select sum(stock) as gesamtbestand from xt_stocks where product_id = '$Artikel_ID'";
							$sql_query = dmc_db_query($cmd);
							// neuen Gesamt Bestand ermitteln
							if ($sql_result = dmc_db_fetch_array($sql_query))
								$gesamtbestand = $sql_result['gesamtbestand'];
							else 
								$gesamtbestand = 0;
							$sql_data_array = array('quantity' => $gesamtbestand);
							if (DEBUGGER>=1) 	fwrite($dateihandle, "  Artikel  ".$Artikel_ID." bekommt neuen Gesamtbestand $cmd = $gesamtbestand ...\n");
						$sql_data_array = array(
							  'quantity' => $gesamtbestand,
							  'price' => $Artikel_Preis);
					}
					fwrite($dateihandle, "Artikel id ".$Artikel_ID." soll neuen PREIS $Artikel_Preis und Menge $Artikel_Menge und Status $Artikel_Status bekommen ...");			
				}
			} else {
				if ($ExportModus=='QuantityOnly')
				{
					// nur der Bestand wird geändert
					if(strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
						$sql_data_array = array( 	'product_in_stock' => $Artikel_Menge,
													'published' => $Artikel_Status);	// in Produkt Tabelle
					} else if(strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) {
						if ($Artikel_Status==true) $Artikel_Status=1;
						$query="UPDATE s_articles_details SET active=".$Artikel_Status.", instock=".$Artikel_Menge." WHERE ordernumber ='".$Artikel_Artikelnr."'";
						dmc_sql_query($query);
					} else if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
						$sql_data_array = array('quantity' => $Artikel_Menge,
												'status'=> $Artikel_Status);
					} else if(strpos(strtolower(SHOPSYSTEM), 'woocommerce') !== false) {
						if ( $Artikel_Menge > 0) { 			// Artikel auf Lager
							$_stock_status= 'instock'; 		// Bestandsstatus
						} else { 							// Kein Bestand
							$_stock_status= 'outofstock'; 		// Bestandsstatus
						}	
						$Artikel_Status='visible';
						// update posts durchfuehren
						$table = "postmeta";
						// Status aendern
						// dmc_sql_update($table,  "meta_value='".$Artikel_Status."'" , "meta_key= '_visibility' AND post_id=".$Artikel_ID);	// (Sichtbarkeit wie visible)
						//  Bestandsupdate
						dmc_sql_update($table,  "meta_value='".$Artikel_Menge."'" , "meta_key= '_stock' AND post_id=".$Artikel_ID);			// (Preis) -> Wenn Aktionspreis, dann Aktionspreis, sonst regular price
						//dmc_sql_update($table,  "meta_value='".$_stock_status."'" , "meta_key= '_stock_status' AND post_id=".$Artikel_ID);			// (Preis) -> Wenn Aktionspreis, dann Aktionspreis, sonst regular price
				
					} else {
						// Standard
						$sql_data_array = array('quantity' => $Artikel_Menge);
					}
					fwrite($dateihandle, "Artikel ID ".$Artikel_ID." hat neuen Bestand $Artikel_Menge bekommen.");
					fwrite($dateihandle, "\n");
				}
				if ($ExportModus=='PreisQuantity')
				{
					// Preis und die Menge wird geändert
					if(strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
						$sql_data_array = array( 	'product_in_stock' => $Artikel_Menge,
													'published' => $Artikel_Status);	// in Produkt Tabelle
						if ($Artikel_Preis!='' && $Artikel_Preis>0)
							$sql_data_array = array( 'product_price' => $Artikel_Preis);	// in Preis Tabelle
				
					} else if(strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) {
						if ($Artikel_Status==true) $Artikel_Status=1;
						$preise = explode ( '@', $Artikel_Preis);
						$preisgruppen = array("EK", "EK", "EK", "EK","EK", "EK", "EK", "EK","EK");
						for($i = 0; $i < sizeof($preise);$i++)
						{
							$query="UPDATE s_articles_prices SET price=".$preise[$i]." WHERE pricegroup='".$preisgruppen[$i]."' AND `from`=1 ".
						//	"AND articledetailsID = ".$Artikel_ID;
							"AND articledetailsID = (select id from s_articles_details  WHERE  ordernumber ='".$Artikel_Artikelnr."')";
							if ($preise[$i]>0) dmc_sql_query($query);
						//	$query="UPDATE s_articles_details SET active=".$Artikel_Status." WHERE articleID =".$Artikel_ID;
						//	dmc_sql_query($query);
							fwrite($dateihandle, "Artikel ID ".$Artikel_ID." Shopware DB Preis ".$preise[$i]." (".$preisgruppen[$i].") aktualisiert (735).\n");
						}
						$query="UPDATE s_articles_details SET active=".$Artikel_Status.", instock=".$Artikel_Menge." WHERE  ordernumber ='".$Artikel_Artikelnr."'";
						dmc_sql_query($query);
						fwrite($dateihandle, "Artikel ID ".$Artikel_ID." Shopware DB aktualisiert (783).");
					} else if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
						$sql_data_array = array(
							  'quantity' => $Artikel_Menge,
							  'price' => $Artikel_Preis);
					} else if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false && USE_xt_plg_price_per_shop==true) {
						// Multishop veyton Zusatzmodul xt_plg_price_per_shop fuer Netto Preise nach Shop	
						$sql_data_array = array(
							  'quantity' => $Artikel_Menge,
						   //   'price' => $Artikel_Preis 		-> Preis hier separat
							 );
					} else if(strpos(strtolower(SHOPSYSTEM), 'woocommerce') !== false) {
						if ( $Artikel_Menge > 0) { 			// Artikel auf Lager
							$_stock_status= 'instock'; 		// Bestandsstatus
						} else { 							// Kein Bestand
							$_stock_status= 'outofstock'; 		// Bestandsstatus
						}	
						$Artikel_Status='visible';
						// update posts durchfuehren
						$table = "postmeta";
						// Status aendern
						// dmc_sql_update($table,  "meta_value='".$Artikel_Status."'" , "meta_key= '_visibility' AND post_id=".$Artikel_ID);	// (Sichtbarkeit wie visible)
						// Preis und Bestandsupdate
						dmc_sql_update($table,  "meta_value='".$Artikel_Menge."'" , "meta_key= '_stock' AND post_id=".$Artikel_ID);			// (Preis) -> Wenn Aktionspreis, dann Aktionspreis, sonst regular price
						dmc_sql_update($table,  "meta_value='".$Artikel_Preis."'" , "meta_key= '_price' AND post_id=".$Artikel_ID);			// (Preis) -> Wenn Aktionspreis, dann Aktionspreis, sonst regular price
						dmc_sql_update($table,  "meta_value='".$Artikel_Preis."'" , "meta_key= '_regular_price' AND post_id=".$Artikel_ID);			// (Preis) -> Wenn Aktionspreis, dann Aktionspreis, sonst regular price
						//	dmc_sql_update($table,  "meta_value='".$Artikel_Menge."'" , "meta_key= '_stock' AND post_id=".$Artikel_ID);			// (Preis) -> Wenn Aktionspreis, dann Aktionspreis, sonst regular price
						//dmc_sql_update($table,  "meta_value='".$_stock_status."'" , "meta_key= '_stock_status' AND post_id=".$Artikel_ID);			// (Preis) -> Wenn Aktionspreis, dann Aktionspreis, sonst regular price
					} else {
						$sql_data_array = array(
							  'quantity' => $Artikel_Menge,
							  'price' => $Artikel_Preis);
					}
					fwrite($dateihandle, "Artikel id ".$Artikel_ID." soll neuen PREIS $Artikel_Preis und Menge $Artikel_Menge und Status $Artikel_Status bekommen ...");			
				}
			}
					  
			//Update durchführen
			if(strpos(strtolower(SHOPSYSTEM), 'woocommerce') !== false) {
				// Update erfolgte bereits weiter oben
			} else if(strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
				$mode='UPDATED';
				$sql_data_array['modified_on'] = 'now()';
				dmc_sql_update_array(TABLE_PRODUCTS, $sql_data_array, "virtuemart_product_id = '$Artikel_ID'");	// Produkt Tabelle
				dmc_sql_update_array(TABLE_PRODUCTS_PRICES, $sql_price_array, 
				"virtuemart_product_id = '$Artikel_ID' AND (virtuemart_shoppergroup_id IS NULL OR virtuemart_shoppergroup_id ='')");	// Preis Tabelle	
				fwrite($dateihandle, " Update ist erfolgt \n");
			} else if(strpos(strtolower(SHOPSYSTEM), 'presta') === false 
				&& strpos(strtolower(SHOPSYSTEM), 'shopware') === false) {
				$mode='UPDATED';
				if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false)
					$sql_data_array['products_last_modified'] = 'now()';
				dmc_sql_update_array(TABLE_PRODUCTS, $sql_data_array, "products_id = '$Artikel_ID'");
				fwrite($dateihandle, " Update erfolgt \n");
			} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false){
				// PRESTA
				$mode='UPDATED';
				$sql_data_array['date_upd'] = 'now()';
				dmc_sql_update_array(TABLE_PRODUCTS, $sql_data_array, "id_product = '$Artikel_ID'");
				if ($ExportModus=='QuantityOnly') {
					// Mengenupdate auf der product_shop
					$cmd = 	"UPDATE ".DB_PREFIX."stock_available SET quantity=".$Artikel_Menge.
							" WHERE id_product = '$Artikel_ID'";
					$sql_query = dmc_db_query($cmd);
						// Erweiterung Rieger zru Erkennung Lexware Artikel Feld location in ps_product = L
					$cmd = 	"UPDATE ".DB_PREFIX."product SET location='L'".
							" WHERE id_product = '$Artikel_ID'";
					$sql_query = dmc_db_query($cmd);
				
				} else if ($ExportModus=='PreisOnly') {
					// Preisupdate auf der product_shop
					$cmd = 	"UPDATE ".DB_PREFIX."product_shop SET price=".$Artikel_Preis.
							" WHERE id_product = '$Artikel_ID'";
					$sql_query = dmc_db_query($cmd);
						// Erweiterung Rieger zru Erkennung Lexware Artikel Feld location in ps_product = L
					$cmd = 	"UPDATE ".DB_PREFIX."product SET location='L'".
							" WHERE id_product = '$Artikel_ID'";
					$sql_query = dmc_db_query($cmd);
				
				} else  {
					// Preisupdate und Qty auf der product_shop
					$cmd = 	"UPDATE ".DB_PREFIX."product_shop SET price=".$Artikel_Preis.
							" WHERE id_product = '$Artikel_ID'";
					$sql_query = dmc_db_query($cmd);
					// Preisupdate auf der product_shop
					$cmd = 	"UPDATE ".DB_PREFIX."stock_available SET quantity=".$Artikel_Menge.
							" WHERE id_product = '$Artikel_ID'";
					$sql_query = dmc_db_query($cmd);
					// Erweiterung Rieger zru Erkennung Lexware Artikel Feld location in ps_product = L
					$cmd = 	"UPDATE ".DB_PREFIX."product SET location='L'".
							" WHERE id_product = '$Artikel_ID'";
					$sql_query = dmc_db_query($cmd);
				}
				
				fwrite($dateihandle, " Update erfolgt \n");
			}	 else {
				fwrite($dateihandle, " KEIN Update erfolgt, Shop nicht definiert (884) \n");
			}
			
			// Multishop veyton Zusatzmodul xt_plg_price_per_shop fuer Netto Preise nach Shop	
			if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false && USE_xt_plg_price_per_shop==true) {
				$sql_data_price_array['products_price'] = $Artikel_Preis;
				dmc_sql_update_array('xt_plg_price_per_shop', $sql_data_price_array, "products_id = '$Artikel_ID' AND shop_id='$shop_id'");		
			}		
		}  
		
		if ($exists==1 && (strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false) 
				&& (strpos(strtolower(SHOPSYSTEM), 'shopware') === false)
				&& (strpos(strtolower(SHOPSYSTEM), 'woocommerce') === false)
				&& (strpos(strtolower(SHOPSYSTEM), 'presta') === false)) {		 
				// Update der Variante
				// Prreisdifferenz ermittln
				$Artikel_Preis_Differenz=dmc_get_attribute_price($Artikel_ATTRIBUTE_ID, $Artikel_Preis);
				// Nachlass
				$price_prefix = '+';
				if ($options_values_price<0) {
					$price_prefix = '-';
					$options_values_price = $options_values_price * -1;
				} // endif
				if ($ExportModus=='PreisOnly')
				{
						// nur der Preis wird geändert
						 $sql_data_array = array(  	'options_values_price' => $Artikel_Preis_Differenz,		
													'price_prefix' => $price_prefix);	
						fwrite($dateihandle,  "Artikel Attribute ID ".$Artikel_ATTRIBUTE_ID." soll neuen Preis bekommen.");
						fwrite($dateihandle, "\n");
				}
				if ($ExportModus=='QuantityOnly')
				{
						// nur die Menge wird geändert
						$sql_data_array = array(  	'attributes_stock' => $Artikel_Menge);	
						fwrite($dateihandle,  "Artikel Attribute ID ".$Artikel_ATTRIBUTE_ID." soll neue Menge bekommen.");
						fwrite($dateihandle, "\n");
				}
				if ($ExportModus=='PreisQuantity')
				{
						 // der Preis und die Menge wird geändert
						$sql_data_array = array(  	'options_values_price' => $Artikel_Preis_Differenz,		
													'price_prefix' => $price_prefix,						
													'attributes_stock' => $Artikel_Menge);
						fwrite($dateihandle, "Artikel Attribute ID ".$Artikel_ATTRIBUTE_ID." soll neuen PREIS $Artikel_Preis_Differenz und Menge $Artikel_Menge  bekommen.");
						fwrite($dateihandle, "\n");
				}
			
					  
				//Update durchführen // nicht VEYTON
				$mode='UPDATED';
				if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false)  
					dmc_sql_update_array(TABLE_PRODUCTS_ATTRIBUTES, $sql_data_array, "products_attributes_id = '$Artikel_ATTRIBUTE_ID'");
		} // END IF
	} // END IF ELSE Modified Sondermodul
	
	// SEO Tool von Bluegate
	// Einbinden, wenn existiert
	if (file_exists(DIR_FS_INC.'bluegate_seo.inc.php')) {  
		if (DEBUGGER>=1) fwrite($dateihandle, "*SEO Tool von Bluegate initialisieren* (".$Artikel_ID.") \n");
		// *************************** BLUEGATE SUMA OPTIMIZER ************************* //
		require_once (DIR_FS_INC.'bluegate_seo.inc.php');
		$bluegateSeo = new BluegateSeo();
		// Update bluegate_seo_url Table
		$bluegateSeo->updateSeoDBTable('product', $Artikel_ID);	
		if (DEBUGGER>=1) fwrite($dateihandle, ".... für Artikel mit ID=".$Artikel_ID." durchgeführt.\n");		
		
	} // end if // SEO Tool von Bluegate
	 
	 if ($Artikel_ID!="") { 
		$massage='OK';
	 } else {
		$massage='FEHLER, Artikel evtl nicht existent';
	 }

	echo '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
		"<STATUS>\n" .
       "  <STATUS_INFO>\n" .
       "    <ACTION>$action</ACTION>\n" .
       "    <CODE>0</CODE>\n" .
       "    <MESSAGE>$massage</MESSAGE>\n" .
       "    <MODE>$mode</MODE>\n" .
       "    <ID>$Artikel_ID</ID>\n" .
       "  </STATUS_INFO>\n" .
       "</STATUS>\n\n";
}

function DeleteArtikel()
{

	 
		
	if (DEBUGGER>=1) {
		$daten = "DeleteArtikel";
		$dateiname=LOG_DATEI;	
		$dateihandle = fopen($dateiname,"a");
		fwrite($dateihandle, $daten);
		fwrite($dateihandle, "\n");
	}

  global $action;

  $Artikel_ID = (integer)(($_POST['Artikel_ID']));

  $daten = "DeleteArtikel";
	$dateiname=LOG_DATEI;	
	$dateihandle = fopen($dateiname,"a");
	fwrite($dateihandle, $daten);
	fwrite($dateihandle, "\n");
	
   dmc_delete_product($Artikel_ID);

  echo '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
       "<STATUS>\n" .
       "  <STATUS_INFO>\n" .
       "    <ACTION>$action</ACTION>\n" .
       "    <CODE>0</CODE>\n" .
       "    <MESSAGE>OK</MESSAGE>\n" .
       "    <ID>$Artikel_ID</ID>\n" .
       
       
       "  </STATUS_INFO>\n" .
       "</STATUS>\n\n";
}

// ****************************************************************************
// Trägt eine neue Kategorie mit Bild zu einem Vaterknoten ein
// ****************************************************************************
function WriteCategorie()
{
	 		
	if (DEBUGGER>=1) {
		$daten = "WriteCategorie";
		$dateiname=LOG_DATEI;	
		$dateihandle = fopen($dateiname,"a");
		fwrite($dateihandle, $daten);
		fwrite($dateihandle, "\n");
	}
	
	global $action;

	$exists = False;
	$sonderkategorie=FALSE;

	$Kategorie_ID = $_POST['Artikel_Kategorie_ID'];
	$Kategorie_Vater_ID =  ($_POST['Kategorie_Vater_ID']);
	$Kategorie_Bezeichnung =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_Name1"]), ENT_NOQUOTES);
	$Kategorie_Beschreibung =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_Beschreibung1"]), ENT_NOQUOTES);
	$Aktiv =  $_POST["Aktiv"];
	$Kategorie_Bild =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_Bild"]), ENT_NOQUOTES);
	$Kategorie_Sortierung =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_Sortierung"]), ENT_NOQUOTES);
	$Kategorie_MetaK =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_MetaK"]), ENT_NOQUOTES);
	$Kategorie_MetaD =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_MetaD"]), ENT_NOQUOTES);
	$Kategorie_MetaT =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_MetaT"]), ENT_NOQUOTES);
	$Kategorie_Suchbegriffe =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_Suchbegriffe"]), ENT_NOQUOTES);
	$Kategorie_SEO =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_SEO"]), ENT_NOQUOTES);
	$Kategorie_Sprache_Store =  html_entity_decode (sonderzeichen2html(true,$_POST["Kategorie_Sprache_Store"]), ENT_NOQUOTES);
	$KategorieFF1 =  html_entity_decode (sonderzeichen2html(true,$_POST["KategorieFF1"]), ENT_NOQUOTES);
	$KategorieFF2=  html_entity_decode (sonderzeichen2html(true,$_POST["KategorieFF2"]), ENT_NOQUOTES);
	
	// Überprüfen, ob eine Sortierreihenfolge angegeben
	if (preg_match('/@/', $Aktiv)) {
		//list ($Aktiv, $Sortierung) = split ("@", $Sortierung);
		$werte = explode ( '@', $Aktiv);
		$Aktiv = $werte[0];
		$Sortierung = $werte[1];
	} else {
		// Standard = keine besondere Sortierung
		$Sortierung=0;
	} // endif
	
	if ($Kategorie_Sortierung<>'')
		$Sortierung= $Kategorie_Sortierung;
		
	if ($Aktiv =='') $Aktiv =1;
	
	if (WAWI=='PCK') {
		// PCK aus Kategire ID 005.432.234 Zahl erstellen
		$Kategorie_ID = '1' . str_replace('.', '', $Kategorie_ID);
		$Kategorie_Vater_ID = '1' . str_replace('.', '', $Kategorie_Vater_ID);
		// Wenn Hauptkategorie
		if ($Kategorie_Vater_ID == '10') 
			$Kategorie_Vater_ID ='0';
	}
 
	if (DEBUGGER>=1) fwrite($dateihandle, "Kat_id=".$Kategorie_ID." - Kat=".$Kategorie_Bezeichnung." mit Status =$Aktiv und Vater=$Kategorie_Vater_ID\n");

	// Anzahl der Fremdsprachen
	$no_of_languages=dmc_count_languages();
	if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
		$language_id="de";
	} else {
		$language_id=2;	// deutsch
	}
	
	// Überprüfung, ob Kategorien NICHTStandard, sondern verarbeitet müssen
	// bei Format z.B.: Installation\Fittings\Lötfittings\Übergangsmuffen
	// Definition: ist der Fall, wenn Kategorie ID = 280873 	rcm
	if ($Kategorie_ID=="280273"){
		// Kategorie vorhanden?
		// Voller Kategoriename sollte im Feld categories_meta_description von cartegories_description abgelegt sein
		// Sonderfall für Kategorietrenner \
		$Kategorie_Bezeichnung = str_replace('\\\\', '\\', $Kategorie_Bezeichnung);		// Backslash (\) durch slash (/) ersetzen
		$tmp = str_replace(KATEGORIE_TRENNER, '/', $Kategorie_Bezeichnung);		// Backslash (\) durch slash (/) ersetzen
		fwrite($dateihandle, "Neue Kategoriebezeichnung:".$tmp);
		if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
			$cmd = "select id_category as categories_id from " . TABLE_CATEGORIES_DESCRIPTION .
					" where meta_description='$tmp'";
		} else {
			$cmd = "select categories_id from " . TABLE_CATEGORIES_DESCRIPTION .
					" where categories_meta_description='$tmp'";
		}
     
		$sql_query = dmc_db_query($cmd);
		if ($result_query = dmc_db_fetch_array($sql_query))
		{ // Kategorie existiert bereits: Bestehende categories_id ermitteln	
		    $Kategorie_ID=$result_query['categories_id'];			
			fwrite($dateihandle, "Kategorie existiert bereits: Bestehende categories_id:".$Kategorie_id);
		} 
		else  
		{ // Kategorie existiert nicht 
		    // Kategorie-Bezeichnung ermitteln -> Einzelne Kategorien in Array
			// Kategorien aus $Kategorie_Bezeichnung getrennt durch \ 	rcm
			fwrite($dateihandle, "KATEGORIE_TRENNER:".KATEGORIE_TRENNER." für Kategorie_Bezeichnung ".$Kategorie_Bezeichnung);
			$Kategorie_array = explode(KATEGORIE_TRENNER, $Kategorie_Bezeichnung);			
			// Backslash entfernen (siehe oben)
			$Kategorie_Bezeichnung = $tmp;
			
			for($Anzahl = 0; $Anzahl < count($Kategorie_array); $Anzahl++) {     // Kategorien und Unterkategorien durchlaufen	     			
				// Überprüfen, ob Kategorie existiert			
				// ÜK logik gaendert
				if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
					if ($Kategorie_Vater_ID	== "1") // Hauptcategorie
						$cmd = "select c.id_category from " . TABLE_CATEGORIES ." as c, ". TABLE_CATEGORIES_DESCRIPTION ." as cd ".
						" where c.id_category = cd.id_category AND c.id_parent = 0 and cd.name='$Kategorie_array[$Anzahl]'";
					else // eine der Unterkategorien mit soeben ermittelter parent_id
						$cmd = "select c.id_category from " . TABLE_CATEGORIES ." as c, ". TABLE_CATEGORIES_DESCRIPTION ." as cd ".
						" where c.id_category = cd.id_category AND c.id_parent = $Kategorie_Vater_ID and cd.name='$Kategorie_array[$Anzahl]'";					
				} else {
					if ($Kategorie_Vater_ID	== "280273") // Hauptcategorie
						$cmd = "select categories_id from " . TABLE_CATEGORIES ." as c, ". TABLE_CATEGORIES_DESCRIPTION ." as cd ".
						" where c.categories_id = cd.categories_id and c.parent_id = '0' and cd.categories_name='$Kategorie_array[$Anzahl]'";
					else // eine der Unterkategorien mit soeben ermittelter parent_id
						$cmd = "select c.categories_id from " . TABLE_CATEGORIES ." as c, ". TABLE_CATEGORIES_DESCRIPTION ." as cd ".
						" where c.categories_id = cd.categories_id and c.parent_id = '".$Kategorie_Vater_ID."' and cd.categories_name='$Kategorie_array[$Anzahl]'";
				}
			
				$sql_query = dmc_db_query($cmd);
				if ($result_query = dmc_db_fetch_array($sql_query))
				{ // Kategorie existiert bereits: Bestehende categories_id als Vater_id (für nächsten Schritt) ermitteln	
					if ($Kategorie_Vater_ID==0) // NICHT AENDERN
						$Kategorie_Vater_ID=0;
					else
						$Kategorie_Vater_ID=$result_query['categories_id'];					
				} else { // Kategorie noch nicht existent
				
				// Kategorie anlegen und ID ermitteln   								
				if ($Anzahl == 0) $Kategorie_Vater_ID = 0;   // Erste Kategorie ist Hauptkategorie

				if(strpos(strtolower(SHOPSYSTEM), 'presta') === false) {
					$insert_sql_data = array('parent_id' => $Kategorie_Vater_ID,
									'categories_status' => $Aktiv,
									'sort_order' => $Sortierung);
									
					// Details
					if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false) {
						if (GM_SHOW_QTY_INFO != "" && GM_SHOW_QTY_INFO != "false" && (strtolower(SHOPSYSTEM) == 'gambio' || strpos(strtolower(SHOPSYSTEM), 'gambiogx') !== false))
							$insert_sql_data['gm_show_qty_info'] = GM_SHOW_QTY_INFO;
						for($gruppe = 0; $gruppe <= 10; $gruppe++) {     //  durchlaufen	     
							if (defined(constant('GROUP_PERMISSION_' . $gruppe)))
							if (constant('GROUP_PERMISSION_' . $gruppe)!=''){  	
								 $insert_sql_data[constant('GROUP_PERMISSION_' . $gruppe)] = constant('GROUP_PERMISSION_' . $gruppe);	
							} // end if
						} // END FOR	
					}
					// Weitere Details
					if (CATEGORIES_TEMPLATE != "")
					$insert_sql_data['categories_template'] = CATEGORIES_TEMPLATE;
					if (LISTING_TEMPLATE != "")
					$insert_sql_data['listing_template'] = LISTING_TEMPLATE;
					if (PRODUCTS_SORTING != "")
					$insert_sql_data['products_sorting'] = PRODUCTS_SORTING;
					if (PRODUCTS_SORTING2 != "")
					$insert_sql_data['products_sorting2'] = PRODUCTS_SORTING2;
					
					fwrite($dateihandle, "2970 KATEGORIE_ ANLEGEN parent_id:".$insert_sql_data[parent_id]." categories_status ".$insert_sql_data[categories_status]." sort_order ".$insert_sql_data[sort_order]);
			
					dmc_sql_insert_array(TABLE_CATEGORIES, $insert_sql_data);
					$Kategorie_ID = dmc_db_get_new_id();  // ID wird auf Basis der letzten per autoincrement eingefügten id (+1) ermittelt
 
					
				} else { // presta
					// todo kat_ebene optimieren
					//logik geaendert
					if(strpos(strtolower(SHOPSYSTEM), 'presta') === false && $Anzahl == 0) {
						$Kategorie_Vater_ID = 0;   // Erste Kategorie ist Hauptkategorie
					} else { 
						$Kategorie_Vater_ID = 1;
					}
					//logik gaendert
					/*if ($Kategorie_Vater_ID == 0 || $Kategorie_Vater_ID == 1) {
						$Kategorie_Vater_ID = 1;
						$kat_ebene=1;
					} else {
						$kat_ebene=$kat_ebene;
						$Kategorie_Vater_ID = dmc_get_category_id($Kategorie_Vater_ID);
					}
					
					$insert_sql_data = array(	'id_parent' => $Kategorie_Vater_ID,
												'level_depth' => $kat_ebene,
												'active' => $Aktiv,
												'date_add' => 'now()',
												'date_upd' => 'now()'
												//'position' => $Sortierung
											);
					*/
					
					if($kat_ebene != 1) {
						$Kategorie_Vater_ID = $Kategorie_ID;
						fwrite($dateihandle, "2911 katebene!=1: ".$Kategorie_Vater_ID."\n");
					} else {
						$Kategorie_Vater_ID = 1;			
						fwrite($dateihandle, "id_category=$Kategorie_ID\n");
						fwrite($dateihandle, "id_parent => $Kategorie_Vater_ID\n");
					}
					
					$insert_sql_data = array(	
									'id_category' => $Kategorie_ID,
									'id_parent' => $Kategorie_Vater_ID,
									'level_depth' => $kat_ebene,
									'active' => 1,
									'date_add' => 'now()',
									'date_upd' => 'now()'
								);
								
					dmc_sql_insert_array(TABLE_CATEGORIES, $insert_sql_data);
					$Kategorie_ID = dmc_db_get_new_id();  // ID wird auf Basis der letzten per autoincrement eingefügten id (+1) ermittelt
 
				} // end if presta
						
				// Category Group
				if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
					$insert_sql_data = array(	'id_category' => $Kategorie_ID,
												'id_group' => 1);
					dmc_sql_insert_array(TABLE_CATEGORIES_GROUP, $insert_sql_data);							
				}
				
				// Kategorie Beschreibung anlegen mit Sprache deutsch = 2				
				if(strpos(strtolower(SHOPSYSTEM), 'presta') === false) {
					if (($Anzahl+1) == count($Kategorie_array)) {	
						// übermittelte Kategoriebezeichnung, z.B. Installation\Fittings\Lötfittings\Übergangsmuffen						
						if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
								$insert_sql_data = 
									array(
										'categories_id' => $Kategorie_ID,
										'language_code' => $language_id,
										'categories_name' => $Kategorie_array[$Anzahl],
										'categories_description' => $Kategorie_Beschreibung,
										'categories_meta_description' => $Kategorie_ID);
						} else {
								$insert_sql_data = 
									array('categories_id' => $Kategorie_ID,
											  'language_id' => $language_id,
											  'categories_name' => $Kategorie_array[$Anzahl],
											  'categories_description' => $Kategorie_Beschreibung,
											  'categories_meta_description' => $Kategorie_ID);
						}

					} else {
						// extrahierte Kategorie
						if ($meta_desc=='')
							$meta_desc = $Kategorie_array[$Anzahl];
						else
							$meta_desc .= '/'.$Kategorie_array[$Anzahl];

						if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
							$insert_sql_data = array(
											'categories_id' => $Kategorie_ID,
											'language_code' => $language_id,
											'categories_name' => $Kategorie_array[$Anzahl]);
						} else {
							$insert_sql_data = array('categories_id' => $Kategorie_ID,
											  'language_id' => $language_id,
											  'categories_name' => $Kategorie_array[$Anzahl],
											  'categories_meta_description' => $meta_desc);
						}
					} //  endif
					dmc_sql_insert_array(TABLE_CATEGORIES_DESCRIPTION, $insert_sql_data);
				} else { // presta
					$seo = dmc_prepare_seo ($Kategorie_array[$Anzahl],"category",$Kategorie_ID,'de');
						// extrahierte Kategorie
						if ($meta_desc=='')
							$meta_desc = $Kategorie_array[$Anzahl];
						else
							$meta_desc .= '/'.$Kategorie_array[$Anzahl];
						for ( $language_id = 1; $language_id <= $no_of_languages; $language_id++ ) {
							$insert_sql_data = array(	
									'id_category' => $Kategorie_ID,
									'id_lang' => $language_id,
									'name' => $Kategorie_array[$Anzahl],
									'description' =>  $Kategorie_Beschreibung,
									'link_rewrite' => $language_id.'/'.$seo,
									'meta_description' => $meta_desc,
									'meta_title' => $meta_desc,
									'meta_keywords' => $meta_desc);	
							dmc_sql_insert_array(TABLE_CATEGORIES_DESCRIPTION, $insert_sql_data);
						} // end for
				} // end if presta
				
				if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
				
					$Kategorie_SEO_Bezeichnung = dmc_prepare_seo ($Kategorie_array[$Anzahl],"category",$Kategorie_ID,'de');
					fwrite($dateihandle, "2871 SEO alt=".$Kategorie_array[$Anzahl]." / neu=".$Kategorie_SEO_Bezeichnung.".\n");
					
					
					$insert_sql_data = array(				
								'url_md5' => md5($language_id.'/'.$Kategorie_SEO_Bezeichnung),
								'url_text' => $language_id.'/'.$Kategorie_SEO_Bezeichnung,
								'language_code' => $language_id,
								'link_type' => '2',   			//2 = kategorie
								'meta_description' => $meta_desc,
								'meta_title' => $meta_desc,
								'meta_keywords' => $meta_desc,
								'link_id' =>  $Kategorie_ID);		
					
					dmc_sql_insert_array(TABLE_SEO_URL, $insert_sql_data);			
				}  // extension fuer Vayton -> SEO TABELLE MUSS GEFUELLT WERDEN, sonst wird es nicht angezeigt
				
				// für nächsten Svchritt wird die Kategorie_ID als Kategorie_Vater_ID benötigt
				$Kategorie_Vater_ID = $Kategorie_ID;
				fwrite($dateihandle, "kat_id=".$Kategorie_ID." / Kategorie=".$Kategorie_array[$Anzahl]." angelegt.\n");
				} // endif result_query else 
				fwrite($dateihandle, "Kategorie aus Array:".$Kategorie_array[$Anzahl]."\n");
			} // for schleife
			}		// endif result_query else  	
			$sonderkategorie=true;
			$new_cat_id=$Kategorie_ID;
	} // endif
	
	//logik gaendert
	if (($Kategorie_Vater_ID == 0 || $Kategorie_Vater_ID == 1) && strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
		$Kategorie_Vater_ID = 1;
		$kat_ebene=1;
	} else {
		$kat_ebene=$kat_ebene;
		$Kategorie_Vater_ID = dmc_get_category_id($Kategorie_Vater_ID);
		//fwrite($dateihandle, "3009 katebene -> ".$kat_ebene."\n");
		//fwrite($dateihandle, "3010 Kategorie_Vater_ID -> ".$Kategorie_Vater_ID."\n");
	}

	// fwrite($dateihandle, "writeCategory 2944\n");
	// prüfen auf kategorie_ID (kategorie vorhanden?)
	// prüfen auf kategorie_ID (kategorie vorhanden?)
	// logik geaendert
	if ($Kategorie_ID!='0' && SHOPSYSTEM != 'presta') {
		$new_cat_id=dmc_get_category_id($Kategorie_ID);
		// fwrite($dateihandle, "kategory vorhanden (<>presta): ".$new_cat_id."\n");  //logik geaendert
		
		if ($new_cat_id=='0')
			$exists = false;
		else
			$exists = true;
	} else {
		//presta
		//$new_cat_id=dmc_get_category_id($Kategorie_ID);
		//logik geaendert
		$new_cat_id=$Kategorie_ID;
		if ($new_cat_id=='1')
			$exists = false;
		else
			$exists = true; 
		// fwrite($dateihandle, "kategory vorhanden (=presta): ".$new_cat_id."\n");
	}
		
	// fwrite($dateihandle, "3042 KatID=$Kategorie_ID und VaterID=$Kategorie_Vater_ID \n");
	
	// ÜK - Sonderfunktion für Presta
	//logik geaendert
	if ($exists && strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
	/*	fwrite($dateihandle, "3048\n");
	
		fwrite($dateihandle, "Kategorie_ID: ".$new_cat_id."\n");
		fwrite($dateihandle, "Kategorie_Vater_ID: ".$Kategorie_Vater_ID."\n");
		fwrite($dateihandle, "kat_ebene: ".$kat_ebene."\n"); */
		
		if($kat_ebene != 1) {
			//$Kategorie_Vater_ID = $new_cat_id;
			// fwrite($dateihandle, "3073 katebene!=1: ".$Kategorie_Vater_ID."\n");
		} else {
			$Kategorie_Vater_ID = 1;			
		//	fwrite($dateihandle, "id_category=$new_cat_id\n");
		//	fwrite($dateihandle, "id_parent => $Kategorie_Vater_ID\n");
		}
		/*Kategorie_ID: 110
		Kategorie_Vater_ID: 100
		kat_ebene: 2
		3073 katebene!=1: 110
		*/
		$update_sql_data = array(	
							//'id_category' => $new_cat_id,
							'id_parent' => $Kategorie_Vater_ID,
							'level_depth' => $kat_ebene,
							'active' => 1,
							'date_add' => 'now()',
							'date_upd' => 'now()'
						);
		dmc_sql_update_array(TABLE_CATEGORIES, $update_sql_data, "id_category=$new_cat_id");
		// fwrite($dateihandle, "update ".TABLE_CATEGORIES." set 'id_parent' = $Kategorie_Vater_ID where id_category=$new_cat_id\n");
	}
	
	if (!$exists && !$sonderkategorie)
	{
   		if(strpos(strtolower(SHOPSYSTEM), 'presta') === false) // Standard
		{
			$insert_sql_data = array('parent_id' => $Kategorie_Vater_ID,
									'categories_status' => $Aktiv,
									'sort_order' => $Sortierung);
			// Details
			if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false) { 
				if (GM_SHOW_QTY_INFO != "" && GM_SHOW_QTY_INFO != "false" && (SHOPSYSTEM == 'gambiogx' OR SHOPSYSTEM == 'gambio'))
					$insert_sql_data['gm_show_qty_info'] = GM_SHOW_QTY_INFO;
					for($gruppe = 0; $gruppe <= 10; $gruppe++) {     //  durchlaufen	     
						if (defined(constant('GROUP_PERMISSION_' . $gruppe)))
						if (constant('GROUP_PERMISSION_' . $gruppe)!=''){  	
							 $insert_sql_data[constant('GROUP_PERMISSION_' . $gruppe)] = constant('GROUP_PERMISSION_' . $gruppe);	
						} // end if
					} // END FOR	
			}
			
			// Weitere Details
			if (CATEGORIES_TEMPLATE != "")
				$insert_sql_data['categories_template'] = CATEGORIES_TEMPLATE;
			if (LISTING_TEMPLATE != "")
				$insert_sql_data['listing_template'] = LISTING_TEMPLATE;
			if (PRODUCTS_SORTING != "")
				$insert_sql_data['products_sorting'] = PRODUCTS_SORTING;
			if (PRODUCTS_SORTING2 != "")
				$insert_sql_data['products_sorting2'] = PRODUCTS_SORTING2;
		} else { // presta
			$insert_sql_data = array(	'id_parent' => $Kategorie_Vater_ID,
										'level_depth' => $kat_ebene,
										'active' => 1,
										'date_add' => 'now()',
										'date_upd' => 'now()'
									);
		} // end if presta
			
		if ($exists && UPDATE_CATEGORY){
			if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
				// UPDATE 
				dmc_sql_update_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array,
					"id_category=$Kategorie_ID");
			} else { 
				// UPDATE 
				dmc_sql_update_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array,
					"categories_id=$Kategorie_ID");
			}
		} else {
			if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
				$new_cat_id=dmc_get_highest_id('id_category', TABLE_CATEGORIES)+1;
				$insert_sql_data['id_category']=$new_cat_id;
				// Update auf Kategorie Name
				$sql_data_array = array('categories_name' => $Kategorie_Bezeichnung);
				dmc_sql_insert_array(TABLE_CATEGORIES, $insert_sql_data);
			} else { 
				// bei neuer Kategorie -> neue ID ermitteln
				$new_cat_id=dmc_get_highest_id('categories_id', TABLE_CATEGORIES)+1;
			    $insert_sql_data['categories_id']=$new_cat_id;
				fwrite($dateihandle, "insert new id_category=$new_cat_id\n");
				dmc_sql_insert_array(TABLE_CATEGORIES, $insert_sql_data);
			}
		}
		
		// $new_cat_id = dmc_db_get_new_id();
				// Categorx Group
		if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
			$insert_sql_data = array(	'id_category' => $Kategorie_ID,
										'id_group' => 1);
			dmc_sql_insert_array(TABLE_CATEGORIES_GROUP, $insert_sql_data);							
		}
	
  }
					
  // Namen eintragen, wenn keine Sonderkategorie
  if (!$sonderkategorie && UPDATE_CATEGORY_DESC) {
 
		fwrite($dateihandle, "Standard-Kategorie Text\n");
					
      // Bestehende Daten prüfen
      if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
		$cmd = "select categories_id from " . TABLE_CATEGORIES_DESCRIPTION .
        " where categories_meta_description='$Kategorie_ID' AND language_code='" . $language_id . "'";
	  } else if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
		$cmd = "select id_category from " . TABLE_CATEGORIES_DESCRIPTION .
        " where meta_description='$Kategorie_ID' AND id_lang='" . $language_id . "'";
	  } else {
		$cmd = "select categories_id from " . TABLE_CATEGORIES_DESCRIPTION .
        " where categories_meta_description='$Kategorie_ID' and language_id='" . $language_id . "'";
	  }
      $desc_query = dmc_db_query($cmd);
	 
		// existierende description
	  if ($desc = dmc_db_fetch_array($desc_query))
      {
		if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
			// Update auf Kategorie Name
			$sql_data_array = array('categories_name' => $Kategorie_Bezeichnung);
	        dmc_sql_update_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array,
	          "categories_id='$new_cat_id' and language_code = '" . $language_id . "'");
		} else if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
			for ( $language_id = 1; $language_id <= $no_of_languages; $language_id++ ) {
				// Update auf Kategorie Name
				$sql_data_array = array('categories_name' => $Kategorie_Bezeichnung);
				dmc_sql_update_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array,
					"id_category='$new_cat_id' and id_lang = '" . $language_id . "'");
			} // end for
		} else { 
			// Update auf Kategorie Name
			$sql_data_array = array('categories_name' => $Kategorie_Bezeichnung);
			dmc_sql_update_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array,
				"categories_id='$new_cat_id' and language_id = '" . $language_id . "'");
		}

		if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) { 
		/*
				$Kategorie_SEO_Bezeichnung = dmc_prepare_seo ($Kategorie_Bezeichnung,"category",$Kategorie_ID,'de');
			
				// Update auf URL Key
				$sql_data_array = array(				
							'url_md5' => md5($language_id.'/'.$Kategorie_SEO_Bezeichnung),
							'url_text' => $language_id.'/'.$Kategorie_SEO_Bezeichnung,
							'language_code' => $language_id,
							'link_type' => '2',   			//2 = kategorie
							'meta_description' => $meta_desc,
							'meta_title' => $meta_desc,
							'meta_keywords' => $meta_desc,
							'link_id' =>  $new_cat_id);		
										
				dmc_sql_insert_array(TABLE_SEO_URL, $sql_data_array,
					"link_id='$new_cat_id' and language_code = '" . $language_id . "'");				
					*/
		}  // extension fuer Veyton -> SEO TABELLE MUSS GEFUELLT WERDEN, sonst wird es nicht angezeigt

      }
      else
      {
	  	 fwrite($dateihandle, "Kategorie Text neu anlegen\n");
		// Create Kategorie
		if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
		    $sql_data_array = array(
	          'categories_id' => $new_cat_id,
	          'language_code' => $language_id,
	          'categories_name' => $Kategorie_Bezeichnung,
			  'categories_description' => $Kategorie_Beschreibung,
			  'categories_meta_description'=> $Kategorie_ID);
			
			dmc_sql_insert_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);	
			  
			$Kategorie_SEO_Bezeichnung = dmc_prepare_seo ($Kategorie_Bezeichnung,"category",$new_cat_id,'de');
			fwrite($dateihandle, "3102 SEO alt=".$Kategorie_Bezeichnung." / neu=".$Kategorie_SEO_Bezeichnung.".\n");
					
			$sql_data_array = array(		
				'url_md5' => md5($language_id.'/'.$Kategorie_SEO_Bezeichnung),
				'url_text' => $language_id.'/'.$Kategorie_SEO_Bezeichnung,
				'language_code' => $language_id,
				'link_type' => '2',   			//2 = kategorie
				'meta_description' => $meta_desc,
				'meta_title' => $meta_desc,
				'meta_keywords' => $meta_desc,
				'link_id' =>  $new_cat_id);	
			dmc_sql_insert_array(TABLE_SEO_URL, $sql_data_array);
			
			// wenn fremdsprachen voranden
			if ($no_of_languages>1) {
				$language_id='en';
				$sql_data_array = array(
				  'categories_id' => $new_cat_id,
				  'language_code' => $language_id,
				  'categories_name' => $Kategorie_Bezeichnung,
				  'categories_description' => $Kategorie_Beschreibung,
				  'categories_meta_description'=> $Kategorie_ID);
				
				dmc_sql_insert_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);	
				 
				$Kategorie_SEO_Bezeichnung = dmc_prepare_seo ($Kategorie_Bezeichnung,"category",$new_cat_id,'de');
			
						
				$sql_data_array = array(		
					'url_md5' => md5($language_id.'/'.$Kategorie_SEO_Bezeichnung),
					'url_text' => $language_id.'/'.$Kategorie_SEO_Bezeichnung,
					'language_code' => $language_id,
					'link_type' => '2',   			//2 = kategorie
					'meta_description' => $meta_desc,
					'meta_title' => $meta_desc,
					'meta_keywords' => $meta_desc,
					'link_id' =>  $new_cat_id);	
				dmc_sql_insert_array(TABLE_SEO_URL, $sql_data_array);
			}
		} else if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) { // presta
		
			$seo = dmc_prepare_seo ($Kategorie_Bezeichnung,"category",$new_cat_id,'de');
					
			// extrahierte Kategorie
			if ($meta_desc=='')
				$meta_desc = $Kategorie_Bezeichnung;
			fwrite($dateihandle, "presta mit meta_desc $meta_desc und $no_of_languages languages\n");
			
			for ( $language_id = 1; $language_id <= $no_of_languages; $language_id++ ) {
							$insert_sql_data = array(	
									'id_category' => $new_cat_id,
									'id_lang' => $language_id,
									'name' => $Kategorie_Bezeichnung,
									'description' => $Kategorie_Beschreibung,
									'link_rewrite' => $language_id.'/'.$seo,
									'meta_description' => $Kategorie_ID,
									'meta_title' => $meta_desc,
									'meta_keywords' => $meta_desc);	
							dmc_sql_insert_array(TABLE_CATEGORIES_DESCRIPTION, $insert_sql_data);
			} // end for
				
		} else { // end else if presta
			$sql_data_array = array(
	          'categories_id' => $new_cat_id,
	          'language_id' => $language_id,
	          'categories_name' => $Kategorie_Bezeichnung,
			  'categories_description' => $Kategorie_Beschreibung,
			  'categories_meta_description'=> $Kategorie_ID);
			dmc_sql_insert_array(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
		} // end if
	 } 	
  } // endif  !$sonderkategorie
  
  //HHG -> Kategorie dem Shop zuordnen
  if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false) {
		if (STORE_ALL)
					 $sql_data_array = array(
				          'categories_id' => $Kategorie_ID,
				          'store_all' => '1',
				          'store_1' => 0);
		else
					 $sql_data_array = array(
				          'categories_id' => $Kategorie_ID,
				          'store_all' => '0',
				          'store_1' => 1);
	    
		if (!$exists) 
		    dmc_sql_insert_array(TABLE_MS_CATEGORIES_TO_STORE, $sql_data_array);
		else 
			dmc_sql_update_array(TABLE_MS_CATEGORIES_TO_STORE, $sql_data_array, "categories_id = '$Kategorie_ID'");
     
  }
  
  // ggfls Bildzuordung
  if ($Kategorie_Bild!="") {
	include_once('functions/set_images.php');
	// Bilddateiname ueberpruefen und ggfls korregieren
	$Kategorie_Bild=dmc_validate_image($Kategorie_Bild);
	if ($Kategorie_Bild!="" && (is_file(DIR_FS_CATALOG.DIR_WS_THUMBNAIL_IMAGES.$Kategorie_Bild) 
		|| is_file(DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES.$Kategorie_Bild))) 
		attach_images_to_category($new_cat_id, $Kategorie_Bild, $dateihandle);
  }
  
  // SEO Tool von Bluegate
	// Einbinden, wenn existiert
	if (file_exists(DIR_FS_INC.'bluegate_seo.inc.php')) { 
		if (DEBUGGER>=1) fwrite($dateihandle, "*SEO Tool von Bluegate initialisieren* \n");
		// *************************** BLUEGATE SUMA OPTIMIZER ************************* //
		include_once(DIR_FS_INC . 'xtc_db_error.inc.php');
  
		require_once (DIR_FS_INC.'bluegate_seo.inc.php');
		!$bluegateSeo ? $bluegateSeo = new BluegateSeo() : false;
		// Update bluegate_seo_url Table
		$bluegateSeo->updateSeoDBTable('category', $Kategorie_ID);	
	} // end if // SEO Tool von Bluegate
	
  $mode='INSERTED';

  echo '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
       "<STATUS>\n" .
       "  <STATUS_INFO>\n" .
       "    <ACTION>$action</ACTION>\n" .
       "    <CODE>0</CODE>\n" .
       "    <MESSAGE>OK</MESSAGE>\n" .
       "    <MODE>$mode</MODE>\n" .
       "    <ID>$Kategorie_ID</ID>\n" .
       
       
       "  </STATUS_INFO>\n" .
       "</STATUS>\n\n";
}

// ****************************************************************************
// Liest alle Artikel aus
// ****************************************************************************
function ReadArtikel()
{

	$daten = "ReadArtikel";
	$dateiname=LOG_DATEI;	
	$dateihandle = fopen($dateiname,"a");
	fwrite($dateihandle, $daten);
	fwrite($dateihandle, "\n");
	
  global $action;

  $SkipImages = (bool)(($_GET['SkipImages']));

  if (defined('SET_TIME_LIMIT')) { xtc_set_time_limit(0); }

  echo '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
       "<ARTIKEL>\n";

  $cmd =
    "select products_id,products_quantity,products_model,products_image," .
    "products_price,products_weight,products_ean,products_status,products_tax_class_id," .
    "manufacturers_id,products_shippingtime,products_startpage,products_vpe_value," .
    "products_vpe_status from " . TABLE_PRODUCTS;

  if (isset($_GET['AbDatum']))
  {
    $cmd .= " where products_last_modified>='" . (($_GET['AbDatum'])) ."'";
  }

  $artikel_query = dmc_db_query($cmd);
  while ($artikel = dmc_db_fetch_array($artikel_query))
  {
    $cmdcat = "select categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = $artikel[products_id] LIMIT 1";
    $cat_query = dmc_db_query($cmdcat);
    $cat = dmc_db_fetch_array($cat_query);

    // Bild auslesen, wenn vorhanden
    $bildname = $artikel['products_image'];
    $bild = '';
    if ($bildname!='' && !$SkipImages && file_exists(DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES . $bildname) && $bildname!='no_picture.gif')
    {
      $bild = @implode("",@file(DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES . $bildname));
    }

    echo "<ARTIKEL_DATA>\n" .
         "<ID>$artikel[products_id]</ID>\n" .
         "<ARTIKELNR>" . htmlspecialchars($artikel['products_model']) . "</ARTIKELNR>\n" .
         "<TEXTE>\n";

    if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false) {
		$cmd = "select language_id, products_name, products_description, products_short_description, products_meta_title," .
		" products_meta_description, products_meta_keywords, products_url from " . TABLE_PRODUCTS_DESCRIPTION .
		" where products_id=" . $artikel['products_id'];
		
		$texte_query = dmc_db_query($cmd);
		
		while ($texte = dmc_db_fetch_array($texte_query))
		{
		  echo "<TEXT>\n" .
			   "<LANGUAGEID>$texte[language_id]</LANGUAGEID>\n" .
			   "<NAME>" . htmlspecialchars($texte['products_name']) ."</NAME>\n" .
			   "<DESCRIPTION>" . htmlspecialchars($texte['products_description']) . "</DESCRIPTION>\n" .
			   "<SHORTDESCRIPTION>" . htmlspecialchars($texte['products_short_description']) . "</SHORTDESCRIPTION>\n" .
			   "<METATITLE>" . htmlspecialchars($texte['products_meta_title']) . "</METATITLE>\n" .
			   "<METADESCRIPTION>" . htmlspecialchars($texte['products_meta_description']) . "</METADESCRIPTION>\n" .
			   "<METAKEYWORDS>" . htmlspecialchars($texte['products_meta_keywords']) . "</METAKEYWORDS>\n" .
			   "<URL>" . htmlspecialchars($texte['products_url']) . "</URL>\n" .
			   "</TEXT>\n";
		}	 
	} else {
		$cmd = "SELECT language_code, products_name, products_description, products_short_description, products_keywords, products_url FROM ". TABLE_PRODUCTS_DESCRIPTION .	" where products_id=" . $artikel['products_id'];
		
		$texte_query = dmc_db_query($cmd);
		
	    while ($texte = dmc_db_fetch_array($texte_query))
	    {
	      echo "<TEXT>\n" .
	           "<LANGUAGEID>$texte[language_id]</LANGUAGEID>\n" .
	           "<NAME>" . htmlspecialchars($texte['products_name']) ."</NAME>\n" .
	           "<DESCRIPTION>" . htmlspecialchars($texte['products_description']) . "</DESCRIPTION>\n" .
	           "<SHORTDESCRIPTION>" . htmlspecialchars($texte['products_short_description']) . "</SHORTDESCRIPTION>\n" .
	           "<METATITLE>" . "METATITLE - Veyton" . "</METATITLE>\n" .
	           "<METADESCRIPTION>" . "METATITLE - Veyton" . "</METADESCRIPTION>\n" .
	           "<METAKEYWORDS>" . htmlspecialchars($texte['products_keywords']) . "</METAKEYWORDS>\n" .
	           "<URL>" . htmlspecialchars($texte['products_url']) . "</URL>\n" .
	           "</TEXT>\n";
	    }
	}//ende else
	
	echo "</TEXTE>\n" .
		 "<GEWICHT>$artikel[products_weight]</GEWICHT>\n" .
		 "<EAN>" . htmlspecialchars($artikel['products_ean']) . "</EAN>\n" .
		 "<PREIS>$artikel[products_price]</PREIS>\n" .
		 "<MENGE>$artikel[products_quantity]</MENGE>\n" .
		 "<STATUS>$artikel[products_status]</STATUS>\n" .
		 "<STEUERSATZ>$artikel[products_tax_class_id]</STEUERSATZ>\n"  .
		 "<HERSTELLER_ID>$artikel[manufacturers_id]</HERSTELLER_ID>\n" .
		 "<KATEGORIE>$cat[categories_id]</KATEGORIE>\n" .
		 "<BILDDATEI>" . htmlspecialchars($artikel['products_image']) . "</BILDDATEI>\n" .
		 "<BILD>" . base64_encode($bild) . "</BILD>\n" .
		 "<IMAGES>\n";

    $lastbild = $bild;
    if (!$SkipImages)
    {
      $cmd = "select image_name from " . TABLE_PRODUCTS_IMAGES .
        " where products_id=" . $artikel['products_id'];
      $images_query = dmc_db_query($cmd);
      while ($images = dmc_db_fetch_array($images_query))
      {
        $bildname = $images['image_name'];
        $bild = '';
        if ($bildname!='' && file_exists(DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES . $bildname) && $bildname!='no_picture.gif')
        {
          $bild = @implode("",@file(DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES . $bildname));
        }

        if ($bild != $lastbild)
        {
          echo "<IMAGE>\n" .
               "<NAME>" . htmlspecialchars($bildname) . "</NAME>" .
               "<BILD>" . base64_encode($bild) . "</BILD>\n" .
               "</IMAGE>\n";
          $lastbild = $bild;
        }
      }
    }

    echo "</IMAGES>\n" .
         "<LIEFERSTATUS>$artikel[products_shippingtime]</LIEFERSTATUS>\n" .
         "<STARTSEITE>$artikel[products_startpage]</STARTSEITE>\n";
    if ($artikel['products_vpe_status'] == 1)
    {
      echo "<VPE>$artikel[products_vpe_value]</VPE>";
    }
    echo "</ARTIKEL_DATA>\n";
  }

  echo "</ARTIKEL>\n";
}

function ReadHersteller()
{

$daten = "ReadHersteller";
	$dateiname=LOG_DATEI;	
	$dateihandle = fopen($dateiname,"a");
	fwrite($dateihandle, $daten);
	fwrite($dateihandle, "\n");
	
  global $action;

  echo '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
       "<MANUFACTURERS>\n";

  $cmd = "select manufacturers_id,manufacturers_name from " . TABLE_MANUFACTURERS;
  $manufacturers_query = dmc_db_query($cmd);
  while ($manufacturers = dmc_db_fetch_array($manufacturers_query))
  {
    echo "  <MANUFACTURERS_DATA>\n" .
         "    <ID>$manufacturers[manufacturers_id]</ID>\n" .
         "    <NAME>" . htmlspecialchars($manufacturers["manufacturers_name"]) . "</NAME>\n" .
         "  </MANUFACTURERS_DATA>\n";
  }

  echo "</MANUFACTURERS>\n";
}

function WriteHersteller()
{

$daten = "WriteHersteller";
	$dateiname=LOG_DATEI;	
	$dateihandle = fopen($dateiname,"a");
	fwrite($dateihandle, $daten);
	fwrite($dateihandle, "\n");
	
  global $action;

  $Hersteller_Name = ($_POST['Hersteller_Name']);
  $mode='NONE';

  $cmd = "select manufacturers_id,manufacturers_name from " . TABLE_MANUFACTURERS .
         " where manufacturers_name='$Hersteller_Name'";
  $manufacturers_query = dmc_db_query($cmd);
  if ($manufacturers = dmc_db_fetch_array($manufacturers_query))
  {
    $Hersteller_ID=$manufacturers['manufacturers_id'];
  }
  else
  {
    $mode='INSERTED';
    $insert_sql_data = array('manufacturers_name' => $Hersteller_Name);
    dmc_sql_insert_array(TABLE_MANUFACTURERS, $insert_sql_data);
    $Hersteller_ID = dmc_db_get_new_id();
  }

  echo '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
       "<STATUS>\n" .
       "  <STATUS_INFO>\n" .
       "    <ACTION>$action</ACTION>\n" .
       "    <CODE>0</CODE>\n" .
       "    <MESSAGE>OK</MESSAGE>\n" .
       "    <MODE>$mode</MODE>\n" .
       "    <ID>$Hersteller_ID</ID>\n" .
       
       
       "  </STATUS_INFO>\n" .
       "</STATUS>\n\n";
}


// ****************************************************************************
// Ändert den Auftragsstatus
// ****************************************************************************
function OrderUpdate()
{

$daten = "OrderUpdate";
	$dateiname=LOG_DATEI;	
	
	$dateihandle = fopen($dateiname,"a");
	fwrite($dateihandle, $daten);
	fwrite($dateihandle, "\n");
	
  global $action, $language_id_std;

  $Order_ID = (integer)($_POST['Order_id']);
  $Status = (integer)($_POST['Status']);

  $orders_status_array = array();
  $cmd = "select orders_status_id, orders_status_name from " .
    TABLE_ORDERS_STATUS . " where language_id = '" . (int)$language_id_std . "'";
  $orders_status_query = dmc_db_query($cmd);
  while ($orders_status = dmc_db_fetch_array($orders_status_query))
  {
    $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
  }

  if ($Order_ID != 0 && isset($orders_status_array[$Status]))
  {
    $cmd = "select customers_name, customers_email_address, orders_status, date_purchased, language from " .
      TABLE_ORDERS . " where orders_id = '" . $Order_ID . "'";
    $Order_Query = dmc_db_query($cmd);
    if ($Order = dmc_db_fetch_array($Order_Query))
    {
      if ($Order['orders_status'] != $Status)
      {
        $update_sql_data = array(
          'orders_status' => $Status,
          'last_modified' => 'now()');
        dmc_sql_update_array(TABLE_ORDERS, $update_sql_data, "orders_id='" . $Order_ID . "'");

        // require functionblock for mails
        require_once(DIR_WS_CLASSES.'class.phpmailer.php');
        require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');
        require_once(DIR_FS_INC . 'xtc_add_tax.inc.php');
        require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
        require_once(DIR_FS_INC . 'changedataout.inc.php');
        require_once(DIR_FS_INC . 'xtc_href_link.inc.php');
        require_once(DIR_FS_INC . 'xtc_date_long.inc.php');
        require_once(DIR_FS_INC . 'xtc_check_agent.inc.php');
        $smarty = new Smarty;

        $smarty->assign('language', $Order['language']);
        $smarty->caching = false;
        $smarty->template_dir=DIR_FS_CATALOG.'templates';
        $smarty->compile_dir=DIR_FS_CATALOG.'templates_c';
        $smarty->config_dir=DIR_FS_CATALOG.'lang';
        $smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
        $smarty->assign('logo_path',HTTP_SERVER  . DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');
        $smarty->assign('NAME',$Order['customers_name']);
        $smarty->assign('ORDER_NR',$Order_ID);
        $smarty->assign('ORDER_LINK',xtc_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $Order_ID, 'SSL'));
        $smarty->assign('ORDER_DATE',xtc_date_long($Order['date_purchased']));
        $smarty->assign('NOTIFY_COMMENTS', '');
        $smarty->assign('ORDER_STATUS', $orders_status_array[$Status]);

        $html_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/mail/'.$Order['language'].'/change_order_mail.html');
        $txt_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/mail/'.$Order['language'].'/change_order_mail.txt');

        // send mail with html/txt template
        xtc_php_mail(EMAIL_BILLING_ADDRESS,
                     EMAIL_BILLING_NAME ,
                     $Order['customers_email_address'],
                     $Order['customers_name'],
                     '',
                     EMAIL_BILLING_REPLY_ADDRESS,
                     EMAIL_BILLING_REPLY_ADDRESS_NAME,
                     '',
                     '',
                     EMAIL_BILLING_SUBJECT,
                     $html_mail ,
                     $txt_mail);

        $insert_sql_data = array(
          'orders_id' => $Order_ID,
          'orders_status_id' => $Status,
          'date_added' => 'now()',
          'customer_notified' => '1',
          'comments' => '');
        dmc_sql_insert_array(TABLE_ORDERS_STATUS_HISTORY, $insert_sql_data);
      }
    }
  }

  echo '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
       "<STATUS>\n" .
       "  <STATUS_INFO>\n" .
       "    <ACTION>$action</ACTION>\n" .
       "    <CODE>0</CODE>\n" .
       "    <MESSAGE>OK</MESSAGE>\n" .
       "    <ORDER_ID>$Order_ID</ORDER_ID>\n" .
       "    <ORDER_STATUS>$Status</ORDER_STATUS>\n" .
       
       
       "  </STATUS_INFO>\n" .
       "</STATUS>\n\n";
}

xtc_db_close();

?>