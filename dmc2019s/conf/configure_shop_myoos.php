<?php
	defined( '_DMC_ACCESSIBLE' ) or die( 'Direct Access to this location configure_shop_myoos is not allowed.' );
	// set the level of error reporting
	error_reporting(E_ALL & ~E_NOTICE);
	//  error_reporting(E_ALL);

	// Include myoos configuration
	DEFINE('OOS_VALID_MOD', true);
	include ('../includes/config.php');
	include ('../includes/oos_tables.php');
	 
	 //Informationen aus der /config.php nehmen, bis auf username und password
	define('DB_SERVER', OOS_DB_SERVER);
	define('DB_DATABASE', OOS_DB_DATABASE);
	define('DB_SERVER_USERNAME', 'root');
	define('DB_SERVER_PASSWORD', 'niconico');
	define('DB_PREFIX', OOS_DB_PREFIX);
	DEFINE('DB_TABLE_PREFIX', DB_PREFIX);
 
	// Spezielle Pfade
	DEFINE('SHOP_ROOT', OOS_SHOP);
	DEFINE('SITE_PATH', SHOP_ROOT);
	DEFINE ('DIR_FS_INC',getcwd().'/inc/');
	
	
	// Veyton Spezifische Informationen
	 define('STD_LANGUAGE_ID', 1);			// ID der Standardsprache
	
	DEFINE ('DIR_IMAGES',OOS_IMAGES);
	DEFINE ('DIR_ORIGINAL_IMAGES',getcwd().'/original_images/');
	
	//database tables
	DEFINE('TABLE_ADMIN', $oosDBTable['admin']);
	DEFINE('TABLE_PRODUCTS', DB_PREFIX.'virtuemart_products');
	DEFINE('TABLE_PRODUCTS_DESCRIPTION', DB_PREFIX.'virtuemart_products_de_de');
	DEFINE('TABLE_PRODUCTS_PRICES', DB_PREFIX.'virtuemart_product_prices');
	DEFINE('TABLE_LANGUAGES', DB_PREFIX.'lang');
	DEFINE('TABLE_MANUFACTURERS', DB_PREFIX.'virtuemart_manufacturers');
	DEFINE('TABLE_MANUFACTURERS_LANG', DB_PREFIX.'virtuemart_manufacturers_de_de');
	DEFINE('TABLE_USERS', $oosDBTable['customers']);
	DEFINE('TABLE_CATEGORIES', $oosDBTable['categories']);
	DEFINE('TABLE_CATEGORIES_DESCRIPTION', $oosDBTable['categories_description']);
	DEFINE('TABLE_CATEGORIES_GROUP', DB_PREFIX.'category_group');
	DEFINE('TABLE_CATEGORIES_PRODUCTS',  $oosDBTable['products']);
	DEFINE('TABLE_PRODUCTS_TO_CATEGORIES', $oosDBTable['products_to_categories']);
	DEFINE('TABLE_IMAGES', DB_PREFIX.'image');
	// tables used for export
	DEFINE('TABLE_ORDERS', $oosDBTable['orders']);
	DEFINE('TABLE_ORDERS_STATUS', $oosDBTable['orders_status']);
	DEFINE('TABLE_ORDERS_STATUS_HISTORY', $oosDBTable['orders_status_history']);
	DEFINE('TABLE_ORDERS_PRODUCTS', $oosDBTable['orders_products']);
	DEFINE('TABLE_CUSTOMERS', $oosDBTable['customers']);
	DEFINE('TABLE_CUSTOMERS_ADDRESSES',  $oosDBTable['address_book'] );
	DEFINE('TABLE_CUSTOMERS_GROUP',   $oosDBTable['customers_info'] );
	DEFINE('TABLE_COUNTRIES',  $oosDBTable['countries']);
	DEFINE('TABLE_STATE', DB_PREFIX.'virtuemart_states');
	
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

		/*
	define('DIR_FS_CATALOG', '/web/');
	define('DIR_WS_IMAGES', '../img/p/');
	define('DIR_RCM_IMAGES', '../img/p/');
	DEFINE ('DIR_WS_ORIGINAL_IMAGES','../img/p/');
 
	
	define('DIR_FS_DOCUMENT_ROOT',DIR_FS_CATALOG);
	*/


?>