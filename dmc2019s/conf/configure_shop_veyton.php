<?php

	defined( '_DMC_ACCESSIBLE' ) or die( 'Direct Access to this location configure_shop_veyton is not allowed.' );
	// set the level of error reporting
	error_reporting(E_ALL & ~E_NOTICE);
	//  error_reporting(E_ALL);
	define('_VALID_CALL',true);
 
  include('../conf/config.php');	// Datebase Definitions
  
  define('DB_SERVER',_SYSTEM_DATABASE_HOST); // eg, localhost - should not be empty for productive servers
  define('DB_SERVER_USERNAME', _SYSTEM_DATABASE_USER);
  define('DB_SERVER_PASSWORD', _SYSTEM_DATABASE_PWD);
  define('DB_DATABASE',_SYSTEM_DATABASE_DATABASE);
  define('DB_TABLE_PREFIX','xt_');
 	define('DB_PREFIX', DB_TABLE_PREFIX);
	
 // Store DB-Querys in a Log File
  define('STORE_DB_TRANSACTIONS', 'false');
  // include the list of project database tables
  require('../conf/database.php');
  
  $docRoot = getenv("DOCUMENT_ROOT");
  $shopRoot = "";						// Shop Rootverzeichnis relativ zum DOCUMENT ROOT
  $dmcVerzeichnis = "dmc2018s";
  
  DEFINE ('DIR_FS_INC',$docRoot.'/'.$shopRoot.'/'.$dmcVerzeichnis.'/inc/');
  // define('DIR_FS_CATALOG','/xtAdmin/dmc0511/');
  define('DIR_FS_CATALOG',$docRoot.'/'.$shopRoot.'/');
  define('DIR_FS_DOCUMENT_ROOT',DIR_FS_CATALOG);
  define('DIR_WS_ORIGINAL_IMAGES','media/images/org');
  //define('DIR_FS_CATALOG_IMAGES',$docRoot.'/'.$shopRoot.'/media/images/product'); 
  define('DIR_FS_CATALOG_IMAGES',$docRoot.'/'.$shopRoot.'/media/images');
  define('DIR_FS_CATALOG_ICON_IMAGES',DIR_FS_CATALOG_IMAGES.'/smallproduct/');
  //define('DIR_WS_THUMBNAIL_IMAGES','media/images/product/thumb/');
  //define('DIR_WS_INFO_IMAGES','media/images/product/info/');
  //define('DIR_WS_POPUP_IMAGES','media/images/product/popup/');
  //define('DIR_WS_ICON_IMAGES','media/images/product/icon/');
  define('DIR_WS_THUMBNAIL_IMAGES','media/images/thumb/');
  define('DIR_WS_INFO_IMAGES','media/images/info/');
  define('DIR_WS_POPUP_IMAGES','media/images/popup/');
  define('DIR_WS_ICON_IMAGES','media/images/smallproduct/');
  		         
 
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
	define('DIR_WS_IMAGES', '../img/p/');
	define('DIR_RCM_IMAGES', '../img/p/');
	DEFINE ('DIR_WS_ORIGINAL_IMAGES','../img/p/');
 
 // include needed functions
  // include('inc/image_manipulator_GD2.php');
 
  // include the list of project filenames
  // require('dmc_includes/veyton_filenames.php');
 
?>