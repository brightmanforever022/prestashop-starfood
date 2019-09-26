<?php

	defined( '_DMC_ACCESSIBLE' ) or die( 'Direct Access to this location configure_shop_shopware is not allowed.' );
	// set the level of error reporting
	error_reporting(E_ALL & ~E_NOTICE);
	//  error_reporting(E_ALL);

	// Eventuell Unterverzeichnis des Shop angeben, wenn vorhanden, SONST IST API NICHT ERREICHBAR
    $Shop_Unterverzeichnis="";
	// $Shop_Unterverzeichnis="/shop";
	DEFINE('API_URL', 'https://'.$_SERVER["SERVER_NAME"].$Shop_Unterverzeichnis.'/api' );
	DEFINE('SHOP_URL', 'https://'.$_SERVER["SERVER_NAME"].$Shop_Unterverzeichnis );
	
	//define('API_URL', 'http://shopware.meinshop.de/api');		// API URL des Shopware Rest Servers
	//define('SHOP_URL', 'http://shopware.meinshop.de/');	

	DEFINE ('DIR_ORIGINAL_IMAGES', '/dmc2019f/upload_images/');		


	if (is_file('../config.php')) 
		$array_dbdata =	include ('../config.php');
	else echo '../config.php nicht vorhanden';
	
	define('DB_SERVER',$array_dbdata['db']['host']);
	define('DB_SERVER_USERNAME',$array_dbdata['db']['username']);
	define('DB_SERVER_PASSWORD',$array_dbdata['db']['password']);
	define('DB_PORT',$array_dbdata['db']['port']);
	define('DB_DATABASE',$array_dbdata['db']['dbname']);
	define('DB_PREFIX','');
 	define('DB_TABLE_PREFIX',DB_PREFIX);
 
	// Shopware Spezifische Informationen 
	 define('STD_LANGUAGE_ID', 1);			// ID der Standardsprache
	
	// Spezielle Pfade
	DEFINE('SHOP_ROOT', getcwd().'/../');
	DEFINE('_PS_ADMIN_DIR_', SHOP_ROOT.'backend/');					
	DEFINE('SITE_PATH', SHOP_ROOT);
	DEFINE ('DIR_FS_INC',getcwd().'/inc/');
	DEFINE ('DIR_IMAGES',SHOP_ROOT.'img/p/');
	
	//database tables
	/*DEFINE('TABLE_PRODUCTS', DB_PREFIX.'product');
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
	DEFINE('TABLE_CUSTOMERS_ADDRESSES', DB_PREFIX.'address');
	DEFINE('TABLE_CUSTOMERS_GROUP', DB_PREFIX.'customer_group');
	DEFINE('TABLE_GROUP_DESC', DB_PREFIX.'group_lang');
	DEFINE('TABLE_COUNTRIES', DB_PREFIX.'country');
	DEFINE('TABLE_COUNTRIES_DESC', DB_PREFIX.'country_lang');
	DEFINE('TABLE_STATE', DB_PREFIX.'state');
	DEFINE('TABLE_CURRENCY', DB_PREFIX.'currency');
	*/
	
	// Bildbearbeitung
	/*
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
	DEFINE ('DIR_WS_ORIGINAL_IMAGES','../img/p/');*/
 
   define('DIR_WS_INCLUDES', 'core/');
    // include needed functions
	
	define('DIR_FS_DOCUMENT_ROOT',DIR_FS_CATALOG);
 
	  // include used functions
/*	include('inc/image_manipulator_GD2.php');
	  require_once('inc/xtc_db_connect.inc.php');
	  require_once('inc/xtc_db_close.inc.php');
	  // require_once('inc/xtc_db_error.inc.php');
	 require_once('inc/xtc_db_perform.inc.php');
	  require_once('inc/dmc_db_query.inc.php');
	  require_once('inc/dmc_db_fetch_array.inc.php');
	  require_once('inc/xtc_db_num_rows.inc.php');
	  require_once('inc/xtc_db_data_seek.inc.php');
	  require_once('inc/xtc_db_insert_id.inc.php');
	  require_once('inc/xtc_db_free_result.inc.php');
	  require_once('inc/xtc_db_fetch_fields.inc.php');
	  require_once('inc/xtc_db_output.inc.php');
	  require_once('inc/xtc_db_input.inc.php');
	  require_once('inc/xtc_db_prepare_input.inc.php');
	  require_once('inc/xtc_set_time_limit.inc.php');
	  require_once('inc/xtc_not_null.inc.php');
	  require_once('inc/xtc_redirect.inc.php');
	  require_once('inc/xtc_rand.inc.php'); 
*/
?>