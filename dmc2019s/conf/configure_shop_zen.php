<?php

defined( '_DMC_ACCESSIBLE' ) or die( 'Direct Access to this location configure_shop_zen is not allowed.' );
  // set the level of error reporting
  error_reporting(E_ALL & ~E_NOTICE);
//  error_reporting(E_ALL);

  // Set the local configuration parameters - mainly for developers - if exists else the mainconfigure
  if (file_exists('../includes/local/configure.php')) {
    include('../includes/local/configure.php');
  } else {
    include('../includes/configure.php');
  }
  
  DEFINE ('DIR_FS_INC','inc/');
   define('DIR_WS_INCLUDES', 'includes/');
    // include needed functions
	include('inc/image_manipulator_GD2.php');
	
define('DIR_FS_DOCUMENT_ROOT',DIR_FS_CATALOG);

  // include the list of project filenames
  require('../'.DIR_WS_INCLUDES . 'filenames.php');

  // include the list of project database tables
  require('../'.DIR_WS_INCLUDES . 'database_tables.php');

  // Store DB-Querys in a Log File
  define('STORE_DB_TRANSACTIONS', 'false');

  // include used functions

    // Include Template Engine
 //  require('../'.DIR_WS_CLASSES . 'Smarty_2.6.10/Smarty.class.php');


?>