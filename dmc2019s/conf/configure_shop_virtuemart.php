<?php
	defined( '_DMC_ACCESSIBLE' ) or die( 'Direct Access to this location configure_shop_presta is not allowed.' );
	// set the level of error reporting
	error_reporting(E_ALL & ~E_NOTICE);
	//  error_reporting(E_ALL);

	 
	// Spezielle Pfade
	DEFINE('SHOP_ROOT', getcwd().'/../');
	DEFINE('SITE_PATH', SHOP_ROOT);
	DEFINE ('DIR_FS_INC',getcwd().'/inc/');
	
	// Include amnd initialise joomla configuration
	include (SHOP_ROOT.'configuration.php');
	$config = new JConfig();

	// Veyton Spezifische Informationen
	 define('STD_LANGUAGE_ID', 1);			// ID der Standardsprache
	
	// DEFINE ('DIR_IMAGES',SHOP_ROOT.'images/stories/virtuemart/');
	DEFINE ('DIR_IMAGES',SHOP_ROOT.'images/stories/virtuemart/product/');
	DEFINE ('DIR_ORIGINAL_IMAGES',getcwd().'/original_images/');
	
	/* aus configuration.php : 
	 $host =
	 $user = ;
	 $db = ;
	 $dbprefix = ;
	 $password = ; 
	 aufgerufen über $config->... zB password */

	 //Informationen aus der /configuration.php nehmen
	define('DB_SERVER', $config->host);
	define('DB_DATABASE', $config->db);
	define('DB_SERVER_USERNAME', $config->user);
	define('DB_SERVER_PASSWORD', $config->password);
	define('DB_PREFIX', $config->dbprefix);
	define('DB_TABLE_PREFIX', $config->dbprefix);
	
	//database tables
	DEFINE('TABLE_PRODUCTS', DB_PREFIX.'virtuemart_products');
	DEFINE('TABLE_PRODUCTS_DESCRIPTION', DB_PREFIX.'virtuemart_products_de_de');
	DEFINE('TABLE_PRODUCTS_PRICES', DB_PREFIX.'virtuemart_product_prices');
	DEFINE('TABLE_LANGUAGES', DB_PREFIX.'lang');
	DEFINE('TABLE_MANUFACTURERS', DB_PREFIX.'virtuemart_manufacturers');
	DEFINE('TABLE_MANUFACTURERS_LANG', DB_PREFIX.'virtuemart_manufacturers_de_de');
	DEFINE('TABLE_USERS', DB_PREFIX.'virtuemart_userinfos');
	DEFINE('TABLE_CATEGORIES', DB_PREFIX.'virtuemart_categories');
	DEFINE('TABLE_CATEGORIES_DESCRIPTION', DB_PREFIX.'virtuemart_categories_de_de');
	DEFINE('TABLE_CATEGORIES_GROUP', DB_PREFIX.'category_group');
	DEFINE('TABLE_CATEGORIES_PRODUCTS', DB_PREFIX.'virtuemart_product_categories');
	DEFINE('TABLE_PRODUCTS_TO_CATEGORIES', DB_PREFIX.'virtuemart_product_categories');
	DEFINE('TABLE_IMAGES', DB_PREFIX.'image');
	DEFINE('TABLE_IMAGES_DESCRIPTION', DB_PREFIX.'image_lang');
	// tables used for export
	DEFINE('TABLE_ORDERS', DB_PREFIX.'virtuemart_orders');
	DEFINE('TABLE_ORDERS_STATUS_HISTORY', DB_PREFIX.'virtuemart_order_histories');
	DEFINE('TABLE_ORDERS_PRODUCTS', DB_PREFIX.'virtuemart_order_items');
	DEFINE('TABLE_CUSTOMERS', DB_PREFIX.'virtuemart_order_userinfos');
	DEFINE('TABLE_CUSTOMERS_ADDRESSES', DB_PREFIX.'address');
	DEFINE('TABLE_CUSTOMERS_GROUP', DB_PREFIX.'customer_group');
	DEFINE('TABLE_GROUP_DESC', DB_PREFIX.'group_lang');
	DEFINE('TABLE_COUNTRIES', DB_PREFIX.'virtuemart_countries');
	DEFINE('TABLE_COUNTRIES_DESC', DB_PREFIX.'country_lang');
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
	define('DIR_FS_CATALOG', '/web/');
	define('DIR_WS_IMAGES', '../img/p/');
	define('DIR_RCM_IMAGES', '../img/p/');
	DEFINE ('DIR_WS_ORIGINAL_IMAGES','../img/p/');
 
    define('DIR_WS_INCLUDES', 'core/');
    // include needed functions
	include('inc/image_manipulator_GD2.php');
	
	define('DIR_FS_DOCUMENT_ROOT',DIR_FS_CATALOG);
 
	
?>