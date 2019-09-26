<?php

defined( '_DMC_ACCESSIBLE' ) or die( 'Direct Access to this location configure_shop_hhg is not allowed.' );
  // set the level of error reporting
  error_reporting(E_ALL & ~E_NOTICE);
//  error_reporting(E_ALL);

  // Set the local configuration parameters - mainly for developers - if exists else the mainconfigure
  if (file_exists('../core/config/local/configure.php')) {
    include('../core/config/local/configure.php');
  } else {
    include('../core/config/configure.php');
  }

define('SITE_PATH', '../');



  include_once ('../core/config/paths.php');

require_once (DIR_FS_CORE . 'config/filenames.php');
require_once (DIR_FS_CORE . 'config/database_tables.php');
require_once (DIR_FS_CORE . 'config/functions.php');

// Kategorie ID der Optionen (z.B. Grφίen oder Farben Kategorie)
 DEFINE ('HHG_OPTIONS_CATEGORIE1',808);


  DEFINE ('DIR_FS_INC','inc/');
  DEFINE ('DIR_IMAGES','../../store_files/1/images/product_images/');
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
	define('DIR_WS_IMAGES', '../store_files/1/images/product_images/');
	define('DIR_RCM_IMAGES', '../store_files/1/images/product_images/');
	DEFINE ('DIR_WS_ORIGINAL_IMAGES','../store_files/1/images/product_images/original_images/');
 
   define('DIR_WS_INCLUDES', 'core/');
    // include needed functions
	include('inc/image_manipulator_GD2.php');
	
define('DIR_FS_DOCUMENT_ROOT',DIR_FS_CATALOG);

  // include the list of project filenames
 /* require('../'.DIR_WS_INCLUDES . 'filenames.php');

  // include the list of project database tables
  require('../'.DIR_WS_INCLUDES . 'database_tables.php');

  // Store DB-Querys in a Log File
  define('STORE_DB_TRANSACTIONS', 'false');
*/
  // include used functions
 
    // Include Template Engine
 //  require('../'.DIR_WS_CLASSES . 'Smarty_2.6.10/Smarty.class.php');


?>