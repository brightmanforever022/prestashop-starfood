<?php

	defined( '_DMC_ACCESSIBLE' ) or die( 'Direct Access to this location configure_shop_presta is not allowed.' );
	// set the level of error reporting
	error_reporting(E_ALL & ~E_NOTICE);
	//  error_reporting(E_ALL);

	// Zugangsdaten fuer lokale Schnittstelle
	DEFINE('LOGIN_USER', 'dmConnect0r');													// ******* WICHTIG *********
	DEFINE('LOGIN_PASSWORD', 'dmc_2504');															// ******* WICHTIG *********
	// Spezielle Pfade
	DEFINE('SHOP_ROOT', getcwd().'/../');
	DEFINE('_PS_ADMIN_DIR_', SHOP_ROOT.'admin054dllptf/');																// ******* WICHTIG *********
	DEFINE('SITE_PATH', SHOP_ROOT);
	DEFINE ('DIR_FS_INC',getcwd().'/inc/');
	DEFINE ('DIR_IMAGES',SHOP_ROOT.'img/p/');
	DEFINE ('DIR_ORIGINAL_IMAGES',SHOP_ROOT.'dmc2018f/upload_images/');													// ******* WICHTIG *********

	
	if (SHOPSYSTEM_VERSION=="1.7") {
		//Informationen aus der /config/settings.inc.php nehmen
		$array_dbdata =	include ('../app/config/parameters.php');
		
		define('DB_SERVER',$array_dbdata['parameters']['database_host']);
		define('DB_SERVER_USERNAME',$array_dbdata['parameters']['database_user']);
		define('DB_SERVER_PASSWORD',$array_dbdata['parameters']['database_password']);
		//define('DB_PORT',$array_dbdata['parameters']['database_port']);
		define('DB_PORT','');
		define('DB_DATABASE',$array_dbdata['parameters']['database_name']);
		define('DB_PREFIX',$array_dbdata['parameters']['database_prefix']);
		define('DB_TABLE_PREFIX',DB_PREFIX);
	} else {
		//Informationen aus der /config/settings.inc.php nehmen
		include('../config/settings.inc.php');	// Datebase Definitions
	  
		DEFINE('DB_SERVER',_DB_SERVER_); 
		DEFINE('DB_SERVER_USERNAME', _DB_USER_);
		DEFINE('DB_SERVER_PASSWORD', _DB_PASSWD_);
		DEFINE('DB_DATABASE',_DB_NAME_);
		DEFINE('DB_PREFIX', _DB_PREFIX_);
		define('DB_TABLE_PREFIX',_DB_PREFIX_);
		
	}
	
	
	
	// Presta Spezifische Informationen
	DEFINE('STD_LANGUAGE_ID', 1);			// ID der Standardsprache
	
	//database tables
	DEFINE('TABLE_PRODUCTS', DB_PREFIX.'product');
	DEFINE('TABLE_PRODUCTS_DESCRIPTION', DB_PREFIX.'product_lang');
	DEFINE('TABLE_LANGUAGES', DB_PREFIX.'lang');
	DEFINE('TABLE_MANUFACTURERS', DB_PREFIX.'manufacturer');
	DEFINE('TABLE_MANUFACTURERS_LANG', DB_PREFIX.'manufacturer_lang');
	DEFINE('TABLE_USERS', DB_PREFIX.'employee');
	DEFINE('TABLE_CATEGORIES', DB_PREFIX.'category');
	DEFINE('TABLE_CATEGORIES_DESCRIPTION', DB_PREFIX.'category_lang');
	DEFINE('TABLE_CATEGORIES_GROUP', DB_PREFIX.'category_group');
	DEFINE('TABLE_CATEGORIES_PRODUCTS', DB_PREFIX.'product');
	DEFINE('TABLE_PRODUCTS_TO_CATEGORIES', DB_PREFIX.'category_product');
	DEFINE('TABLE_IMAGES', DB_PREFIX.'image');
	DEFINE('TABLE_IMAGES_DESCRIPTION', DB_PREFIX.'image_lang');
	// tables used for export
	DEFINE('TABLE_ORDERS', DB_PREFIX.'orders');
	DEFINE('TABLE_ORDERS_STATUS_HISTORY', DB_PREFIX.'order_history');
	DEFINE('TABLE_ORDERS_PRODUCTS', DB_PREFIX.'order_detail');
	DEFINE('TABLE_CUSTOMERS', DB_PREFIX.'customer');
	DEFINE('TABLE_ADDRESS_BOOK', DB_PREFIX.'address');
	DEFINE('TABLE_CUSTOMERS_ADDRESSES', DB_PREFIX.'address');
	DEFINE('TABLE_CUSTOMERS_GROUP', DB_PREFIX.'customer_group');
	DEFINE('TABLE_GROUP_DESC', DB_PREFIX.'group_lang');
	DEFINE('TABLE_COUNTRIES', DB_PREFIX.'country');
	DEFINE('TABLE_COUNTRIES_DESC', DB_PREFIX.'country_lang');
	DEFINE('TABLE_STATE', DB_PREFIX.'state');
	DEFINE('TABLE_CURRENCY', DB_PREFIX.'currency');
	
	
	// Bildbearbeitung
	define('PRODUCT_IMAGE_THUMBNAIL_WIDTH',100);
	define('PRODUCT_IMAGE_THUMBNAIL_HEIGHT',100);
	define('PRODUCT_IMAGE_INFO_WIDTH',250);
	define('PRODUCT_IMAGE_INFO_HEIGHT',200);
	define('PRODUCT_IMAGE_POPUP_WIDTH',450);
	define('PRODUCT_IMAGE_POPUP_HEIGHT',450);
	define('IMAGE_QUALITY',90);
	define('PRODUCT_IMAGE_ICON_MERGE', '(overlay.gif,10,-50,60,FF0000)');
	define('PRODUCT_IMAGE_THUMBNAIL_MERGE', '(overlay.gif,10,-50,60,FF0000)');
	define('PRODUCT_IMAGE_INFO_MERGE' , '(overlay.gif,10,-50,60,FF0000)');
	define('PRODUCT_IMAGE_POPUP_MERGE' , '(overlay.gif,10,-50,60,FF0000)');
	define('DIR_FS_CATALOG', '/web/');
	define('DIR_WS_IMAGES', '../img/p/');
	define('DIR_RCM_IMAGES', '../img/p/');
	DEFINE ('DIR_WS_ORIGINAL_IMAGES','../img/p/');
 
   define('DIR_WS_INCLUDES', 'core/');
    // include needed functions
	//include('inc/image_manipulator_GD2.php');
	
	define('DIR_FS_DOCUMENT_ROOT',DIR_FS_CATALOG);
 
	

?>