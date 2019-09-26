<?php
/************************************************************************************************
*                                                         										*
*  dmConnector Definitionen for shop															*
*  Copyright (C) 2015 DoubleM-GmbH.de															*
*                                                                           					*
* 24.7.2013 - Zusaetzlicher Bereich fuer Definitionen für Status wie Beginn Artikelabgleich 	*
*************************************************************************************************/
	
// SHOPSYSTEM ist hier anzugeben, da ansonsten die Konfigurationen moeglicherweise nicht aufrufbar sind.
	DEFINE('SHOP_ROOT', getcwd().'/../');
	
	// ALternativ wird auch auf diese Daten abgefragt, beim Zugriff der lokalen Schnittstelle
	DEFINE('DMCONNECTOR_LOGIN_NAME', 'dmConnect0r');
	DEFINE('DMCONNECTOR_PASSWORT', 'dmC_01042019#');
	
	if (is_file(getcwd().'/../wp-config.php')) {
		define('SHOPSYSTEM' , 'woocommerce');
		define('SHOPSYSTEM_VERSION' , '');
		define('SHOP_ID' , '1');
	} else if (is_file('../config.php')) { 
		define('SHOPSYSTEM' , 'shopware');
		define('SHOPSYSTEM_VERSION' , '');
		define('SHOP_ID' , '1');
	} else if (is_file('../xtAdmin/login.php')) {
		define('SHOPSYSTEM' , 'veyton');
		define('SHOPSYSTEM_VERSION' , '4.2');
		define('SHOP_ID' , '1');
	} else if (is_file('../app/config/parameters.php')) {
		define('SHOPSYSTEM' , 'presta');
		define('SHOPSYSTEM_VERSION' , '1.7');
		define('SHOP_ID' , '1');
	} else if (is_file('../config/settings.inc.php')) {
		define('SHOPSYSTEM' , 'presta');
		define('SHOPSYSTEM_VERSION' , '');
		define('SHOP_ID' , '1');
	} else if (is_file('../core/config/paths.php')) {
		define('SHOPSYSTEM' , 'hhg');
		define('SHOPSYSTEM_VERSION' , '');
		define('SHOP_ID' , '1');
	} else if (is_file('../configuration.php')) {
		define('SHOPSYSTEM' , 'virtuemart');
		define('SHOPSYSTEM_VERSION' , '');
		define('SHOP_ID' , '1');
	} else {
		define('SHOPSYSTEM' , 'xtc');			// osc, hhg, myoos, gambio, zencart etc 
		define('SHOPSYSTEM_VERSION' , 'modified');
		define('SHOP_ID' , '1');
	}
	
//define('FIRST_ORDER_ID',1); // NEU wird eingelesen über /definitions/FIRST_ORDER_ID.dmc
	
$files = array ( /*'SHOPSYSTEM.dmc', 'SHOPSYSTEM_VERSION.dmc',*/ 'WAWI.dmc', 'DMC_FOLDER.dmc', 'CHARSET.dmc','PRODUCT_TEMPLATE.dmc',
				'OPTIONS_TEMPLATE.dmc', 'GENERATE_CAT_ID.dmc','CAT_DEVIDER.dmc', 'KATEGORIE_TRENNER.dmc','STANDARD_CAT_ID.dmc', 
				'UPDATE_ORDER_STATUS_ERP.dmc', 'NEW_ORDER_STATUS_ERP.dmc','NEW_ORDER_STATUS_FAILED.dmc','NOTIFY_CUSTOMER_ERP.dmc',
				'GM_OPTIONS_TEMPLATE.dmc', 'LISTING_TEMPLATE.dmc','PRODUCTS_SORTING.dmc','PRODUCTS_SORTING2.dmc',
				'CATEGORIES_TEMPLATE.dmc', 'GM_SITEMAP_ENTRY.dmc','GM_SHOW_weight.dmc','GM_SHOW_QTY_INFO.dmc',
				'PRODUCTS_EXTRA_PIC_EXTENSION.dmc', 'GROUP_PERMISSION_0.dmc','GROUP_PERMISSION_1.dmc','GROUP_PERMISSION_2.dmc',
				'GROUP_PERMISSION_3.dmc', 'GROUP_PERMISSION_4.dmc','GROUP_PERMISSION_5.dmc','GROUP_PERMISSION_6.dmc',
				'GROUP_PERMISSION_7.dmc', 'GROUP_PERMISSION_8.dmc','GROUP_PERMISSION_9.dmc','GROUP_PERMISSION_10.dmc',
				'GROUP_PERMISSION_11.dmc','GROUP_PERMISSION_12.dmc','GROUP_PERMISSION_13.dmc','GROUP_PERMISSION_14.dmc','GROUP_PERMISSION_15.dmc',
				'FSK18.dmc', 'UPDATE_DESC.dmc','UPDATE_PROD_TO_CAT.dmc','UPDATE_CATEGORY.dmc','UPDATE_CATEGORY_DESC.dmc',
				'DELETE_INACTIVE_PRODUCT.dmc','SONDERZEICHEN.dmc', 'FIRST_ORDER_ID.dmc',
				'UPDATE_ORDER_STATUS.dmc', 'ORDER_STATUS_SET.dmc', 'ORDER_STATUS_GET.dmc', 'STORE_ID_EXPORT.dmc',		// Bestellstatus
				'SPECIAL_PRICE_CATEGORY.dmc');
/*'SOAP_CLIENT.dmc', 'ATTRIBUTE_SET.dmc','CAT_ROOT.dmc', 'MAX_CAT.dmc','STORE_ID.dmc',
					'WEBSITE_ID.dmc', 'ORDER_STATUS.dmc','ORDER_STATUS2.dmc','UPDATE_ORDER_STATUS.dmc','STANDARD_QUANTITY.dmc');*/

// Definitionen einlesen
for ( $i = 0; $i < count ( $files  ); $i++ ) {
		$defName = substr($files[$i],0,-4);
		$dateihandle = fopen("./conf/definitions/".$files[$i],"r");
		$defValue = fread($dateihandle, 100);
		// echo"$defName=$defValue<br>";
		define($defName , trim($defValue));
		fclose($dateihandle);
} // end for

// Kundengruppenpreise		
$files_prices = array ( 'TABLE_PRICE1.dmc', 'GROUP_PRICE1.dmc','TABLE_PRICE2.dmc', 'GROUP_PRICE2.dmc', 'TABLE_PRICE3.dmc', 'GROUP_PRICE3.dmc',
						'TABLE_PRICE4.dmc', 'GROUP_PRICE4.dmc','TABLE_PRICE5.dmc', 'GROUP_PRICE5.dmc', 'TABLE_PRICE6.dmc', 'GROUP_PRICE6.dmc',
						'TABLE_PRICE7.dmc', 'GROUP_PRICE7.dmc','TABLE_PRICE8.dmc', 'GROUP_PRICE8.dmc', 'TABLE_PRICE9.dmc', 'GROUP_PRICE9.dmc',
						'TABLE_PRICE10.dmc', 'GROUP_PRICE10.dmc','TABLE_PRICE11.dmc', 'GROUP_PRICE11.dmc','TABLE_PRICE12.dmc', 'GROUP_PRICE12.dmc',
						'TABLE_PRICE13.dmc', 'GROUP_PRICE13.dmc','TABLE_PRICE14.dmc', 'GROUP_PRICE14.dmc','TABLE_PRICE15.dmc', 'GROUP_PRICE15.dmc');

// Definitionen einlesen
for ( $i = 0; $i < count ( $files_prices  ); $i++ ) {
		$defName = substr($files_prices[$i],0,-4);
		$dateihandle = fopen("./conf/definitions/".$files_prices[$i],"r");
		$defValue = fread($dateihandle, 100);
		
		// echo"$defName=$defValue<br>";
		define($defName , trim($defValue));
		fclose($dateihandle);
} // end for
				
// Log und Debug		
$files_debug = array ( 'DEBUGGER.dmc', 'LOG_DATEI.dmc','IMAGE_LOG_FILE.dmc', 'PRINT_POST.dmc', 'LOG_ROTATION.dmc', 'LOG_ROTATION_VALUE.dmc');
		
// Definitionen einlesen
for ( $i = 0; $i < count ( $files_debug  ); $i++ ) {
		$defName = substr($files_debug[$i],0,-4);
		$dateihandle = fopen("./conf/definitions/".$files_debug[$i],"r");
		$defValue = fread($dateihandle, 100);
		// echo"$defName=$defValue<br>";
		define($defName , trim($defValue));
		fclose($dateihandle);
} // end for

// Spezielle Statusoperationen
$files_status = array ( 'STATUS_WRITE_ART_BEGIN_DETELE_ART.dmc', 'STATUS_WRITE_ART_BEGIN_DEAKTIVATE_ART.dmc', 'STATUS_WRITE_ART_BEGIN_DETELE_ART_VARIANTS.dmc', 'STATUS_WRITE_ART_BEGIN_DEAKTIVATE_ART_VARIANTS.dmc'); //, 'STATUS_WRITE_ART_DETAILS_BEGIN.dmc','STATUS_WRITE_ART_END.dmc', 'STATUS_WRITE_ART_DETAILS_END.dmc');
		
// Definitionen einlesen
for ( $i = 0; $i < count ( $files_status  ); $i++ ) {
		$defName = substr($files_status[$i],0,-4);
		$dateihandle = fopen("./conf/definitions/".$files_status[$i],"r");
		$defValue = fread($dateihandle, 100);
		// echo"$defName=$defValue<br>";
		define($defName , trim($defValue));
		fclose($dateihandle);
} // end for
		
// dmconnector version
$version_year    = '2015';
$version_month    = '05';
$version_datum = '2013.05.05'; 
$version_major = 2015;
$version_minor = 05;

define('TABLE_PRODUCTS_XSELL','personal_xsell');
define('TABLE_SPECIALS','specials');

define('EXTENDED_PRODUCTS_SIZE',false);

// Nur hhg
define('HHG_OPTION_SELECT_TEMPLATE','slave_products_selection.html');		// standard= product_options_selection.html
define('HHG_OPTION_PRODUCT_TEMPLATE','slave_products_dropdown.html');		// standard= product_options_selection.html
define('PRODUCTS_OWNER','1');
define('PRODUCTS_DETAILS','Details');
define('PRODUCTS_SPECS','Artikeldaten');
define('STORE_ALL',true);					// true/false
define('STORE_ID',1);

// Bildverarbeitung 
// define('DO_IMAGE_PROCESSING','false');		
define('PRODUCTS_EXTRA_PIC_PATH','/images/product_images/original_images/');				// ausgehend von "root" / standard =  /images/product_images/original_images/
define('PRODUCTS_EXTRA_PIC_NAME','ARTIKELNUMMER');	// ARTIKELBILD, wenn basierend auf Name des Artikelbildes, ARTIKELNUMMER, wenn basierend auf Artrikelnummer

if (strpos(strtolower(SHOPSYSTEM), 'zencart') !== false) {
	require('conf/configure_shop_zen.php');
} else if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false) {
	require('conf/configure_shop_hhg.php');
} else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
	require('conf/configure_shop_veyton.php');
	//require('../core/application_top_export.php');
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
	if (is_file('../includes/application_top_export.php')) require('../includes/application_top_export.php');
	else if (is_file('../../includes/application_top_export.php')) require('../../includes/application_top_export.php');
	else {
		if (is_file('./conf/configure_shop_presta.php')) require('./conf/configure_shop_presta.php');
		else if (is_file('./conf/configure_shop_hhg.php')) require('./conf/configure_shop_hhg.php');
		else if (is_file('./conf/configure_shop_zen.php')) require('./conf/configure_shop_zen.php');
		else if (is_file('./conf/configure_shop_veyton.php')) require('./conf/configure_shop_veyton.php');
		$action="dmc_install"; // wohl kein xtc Shop, daher Install		
	}
}
	defined('DB_PREFIX') or define('DB_PREFIX', '');
	defined('DB_TABLE_PREFIX') or define('DB_TABLE_PREFIX', DB_PREFIX);
	

?>
