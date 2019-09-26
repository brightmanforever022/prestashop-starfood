<?php

	defined( '_DMC_ACCESSIBLE' ) or die( 'Direct Access to this location configure_shop_veyton is not allowed.' );
	// set the level of error reporting
	error_reporting(E_ALL & ~E_NOTICE);
	//  error_reporting(E_ALL);
	define('_VALID_CALL',true);
 
  include('../includes/configure.php');	// Datebase Definitions
  
 // define('DB_SERVER',_SYSTEM_DATABASE_HOST); // eg, localhost - should not be empty for productive servers
 // define('DB_SERVER_USERNAME', _SYSTEM_DATABASE_USER);
 // define('DB_SERVER_PASSWORD', _SYSTEM_DATABASE_PWD);
  //define('DB_DATABASE',_SYSTEM_DATABASE_DATABASE);
  // define('DB_TABLE_PREFIX','xt_');
  define('LOGIN_USER', 'dmctopmedia');	
	define('LOGIN_PASSWORD', 'rcm_2018XX');	
DEFINE('DB_PREFIX', DB_TABLE_PREFIX);
 
 // Store DB-Querys in a Log File
  define('STORE_DB_TRANSACTIONS', 'false');
  // include the list of project database tables
  require('../includes/database_tables.php');
  
  $docRoot = getenv("DOCUMENT_ROOT");
  $shopRoot = "";						// Shop Rootverzeichnis relativ zum DOCUMENT ROOT
  
  DEFINE ('DIR_FS_INC',$docRoot.'/'.$shopRoot.'/dmc2018s/inc/');
  // define('DIR_FS_CATALOG','/xtAdmin/dmc0511/');
 //  define('DIR_FS_CATALOG',$docRoot.'/'.$shopRoot.'/');
  define('DIR_FS_DOCUMENT_ROOT',DIR_FS_CATALOG);
  define('DIR_WS_ORIGINAL_IMAGES','/media/images/org/');
  define('DIR_FS_CATALOG_IMAGES',$docRoot.'/'.$shopRoot.'/media/images');
  //define('DIR_FS_CATALOG_IMAGES',$docRoot.'/'.$shopRoot.'/media/images/product');
  define('DIR_FS_CATALOG_ICON_IMAGES',DIR_FS_CATALOG_IMAGES.'/icon');
 // define('DIR_WS_IMAGES', 'media/images/product/');
  define('DIR_WS_IMAGES', 'media/images/');
  define('DIR_WS_THUMBNAIL_IMAGES',DIR_WS_IMAGES.'thumb/');
  define('DIR_WS_INFO_IMAGES',DIR_WS_IMAGES.'info/');
  define('DIR_WS_POPUP_IMAGES',DIR_WS_IMAGES.'popup/');
  define('DIR_WS_ICON_IMAGES',DIR_WS_IMAGES.'icon/');
 
	// include('inc/image_manipulator_GD2.php');
	  		         
 
 // Bildbearbeitung
	define('PRODUCT_IMAGE_ICON_WIDTH',100);
	define('PRODUCT_IMAGE_ICON_HEIGHT',100);
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
	define('DIR_FS_CATALOG', $shopRoot);
	define('DIR_RCM_IMAGES', '../img/p/');
	DEFINE ('DIR_WS_ORIGINAL_IMAGES','../img/p/');
 
 // include needed functions
 # include('inc/image_manipulator_GD2.php');
 
  

?>