<?php
	defined( '_DMC_ACCESSIBLE' ) or die( 'Direct Access to this location configure_shop_woocommerce is not allowed.' );
	// set the level of error reporting
	error_reporting(E_ALL & ~E_NOTICE);
	//  error_reporting(E_ALL);

	// LOGIN_USER und LOGIN_PASSWORD (evtl der WP AUTH_KEY) wird als Login Passwort geprüft, welche im lokalen dmconnector eingegeben sind
	DEFINE('LOGIN_USER', 'info@mobilize.de');
	DEFINE('LOGIN_PASSWORD', 'RCM#2019?');

	// Spezielle Pfade
	DEFINE('SHOP_URL', 'https://'.$_SERVER["SERVER_NAME"] );
	// DEFINE('SHOP_URL', 'http://www.meinshop.de');
	DEFINE('SHOP_ROOT', getcwd().'/../');
	DEFINE('SITE_PATH', SHOP_ROOT);
	DEFINE ('DIR_FS_INC',getcwd().'/inc/');
	
	// woocommerce Bilder liegen in upload verzeichnis ./wp-content/uploads/... 
	DEFINE ('LOCAL_WOO_UPLOAD_IMAGES','Artikelbilder/');

	DEFINE ('UPLOAD_IMAGES_FOLDER',"./upload_images/");	// In der Regel "./upload_images/"

	DEFINE('SHOP_VERSION', '2');				// bei 1 mit Verknüfung über terms_taxamony etc oder Version 2 mit dem order status im post_status von wp_posts
	
	
	// Wenn Multistore Extension Ÿber zusŠtzliche Prefixes, diese hier fŸr Bestellimport dur @ getrennt hinterlegen
	//DEFINE('MULTI_STORE_PREFIXES', 'wp_5_@wp_6_@wp_8_');
	DEFINE('MULTI_STORE_PREFIXES', '');
	// Include amnd initialise wordpress configuration
	include (SHOP_ROOT.'wp-config.php');
	
	 //Informationen aus der Konfigurationsdatei nehmen
	define('DB_SERVER', DB_HOST);
	define('DB_DATABASE',DB_NAME);
	define('DB_SERVER_USERNAME', DB_USER);
	define('DB_SERVER_PASSWORD', DB_PASSWORD);
	define('DB_PREFIX', $table_prefix);
	define('DB_TABLE_PREFIX', $table_prefix);
	// Definiert in conf/definitions define('DB_TABLE_PREFIX', DB_PREFIX);
	
	//  Modul-Spezifische Informationen
	define('STD_LANGUAGE_ID', 'DE');			// ID/NAME der Standardsprache
	define('USE_WPML', false);					// WPML Fremdsprachenmodul verwenden
	
	// DEFINE ('DIR_IMAGES',SHOP_ROOT.'images/stories/virtuemart/');
//	DEFINE ('DIR_IMAGES',SHOP_ROOT.'images/stories/virtuemart/product/');
//	DEFINE ('DIR_ORIGINAL_IMAGES',getcwd().'/original_images/');
	
	//database tables
	// DEFINE('TABLE_PRODUCTS', DB_PREFIX.'virtuemart_products');
	// DEFINE('TABLE_PRODUCTS_DESCRIPTION', DB_PREFIX.'virtuemart_products_de_de');
	// DEFINE('TABLE_PRODUCTS_PRICES', DB_PREFIX.'virtuemart_product_prices');
	// DEFINE('TABLE_LANGUAGES', DB_PREFIX.'lang');
	// DEFINE('TABLE_MANUFACTURERS', DB_PREFIX.'virtuemart_manufacturers');
	// DEFINE('TABLE_MANUFACTURERS_LANG', DB_PREFIX.'virtuemart_manufacturers_de_de');
	// DEFINE('TABLE_USERS', DB_PREFIX.'virtuemart_userinfos');
	// DEFINE('TABLE_CATEGORIES', DB_PREFIX.'virtuemart_categories');
	// DEFINE('TABLE_CATEGORIES_DESCRIPTION', DB_PREFIX.'virtuemart_categories_de_de');
	// DEFINE('TABLE_CATEGORIES_GROUP', DB_PREFIX.'category_group');
	// DEFINE('TABLE_CATEGORIES_PRODUCTS', DB_PREFIX.'virtuemart_product_categories');
	// DEFINE('TABLE_PRODUCTS_TO_CATEGORIES', DB_PREFIX.'virtuemart_product_categories');
	// DEFINE('TABLE_IMAGES', DB_PREFIX.'image');
	// DEFINE('TABLE_IMAGES_DESCRIPTION', DB_PREFIX.'image_lang');
	// tables used for export
	// DEFINE('TABLE_ORDERS', DB_PREFIX.'virtuemart_orders');
	// DEFINE('TABLE_ORDERS_STATUS_HISTORY', DB_PREFIX.'virtuemart_order_histories');
	DEFINE('TABLE_ORDERS_PRODUCTS', DB_PREFIX.'woocommerce_order_items');
	// DEFINE('TABLE_CUSTOMERS', DB_PREFIX.'virtuemart_order_userinfos');
	// DEFINE('TABLE_CUSTOMERS_ADDRESSES', DB_PREFIX.'address');
	// DEFINE('TABLE_CUSTOMERS_GROUP', DB_PREFIX.'customer_group');
	// DEFINE('TABLE_GROUP_DESC', DB_PREFIX.'group_lang');
	// DEFINE('TABLE_COUNTRIES', DB_PREFIX.'virtuemart_countries');
	// DEFINE('TABLE_COUNTRIES_DESC', DB_PREFIX.'country_lang');
	// DEFINE('TABLE_STATE', DB_PREFIX.'virtuemart_states');
	
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
//	define('DIR_FS_CATALOG', '/web/');
//	define('DIR_WS_IMAGES', '../img/p/');
//	define('DIR_RCM_IMAGES', '../img/p/');
//	DEFINE ('DIR_WS_ORIGINAL_IMAGES','../img/p/');
 
//    define('DIR_WS_INCLUDES', 'core/');
    // include needed functions
	/*include('inc/image_manipulator_GD2.php');
	
//	define('DIR_FS_DOCUMENT_ROOT',DIR_FS_CATALOG);
 
	  // include used functions
	  require_once('inc/xtc_db_connect.inc.php');
	  require_once('inc/xtc_db_close.inc.php');
	  // require_once('inc/xtc_db_error.inc.php');
	 require_once('inc/xtc_db_perform.inc.php');
	  require_once('inc/dmc_db_query.inc.php');
	  require_once('inc/dmc_db_fetch_array.inc.php');
	  require_once('inc/xtc_db_num_rows.inc.php');
	  require_once('inc/xtc_db_data_seek.inc.php');
	 // // require_once('inc/dmc_db_get_new_id.inc.php');
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