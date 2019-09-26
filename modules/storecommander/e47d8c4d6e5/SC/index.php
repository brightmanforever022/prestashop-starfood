<?php

/**
 * Store Commander
 *
 * @category administration
 * @author Store Commander - support@storecommander.com
 * @version 2015-09-15
 * @uses Prestashop modules
 * @since 2009
 * @copyright Copyright &copy; 2009-2015, Store Commander
 * @license commercial
 * All rights reserved! Copying, duplication strictly prohibited
 *
 * *****************************************
 * *           STORE COMMANDER             *
 * *   http://www.StoreCommander.com       *
 * *            V 2015-09-15               *
 * *****************************************
 *
 * Compatibility: PS version: 1.1 to 1.6.1
 *
 **/
if (isset($_GET['DEBUG'])) {
    error_reporting(E_ALL ^ E_NOTICE);
    @ini_set('display_errors', 'on');
    @ini_set('log_errors', 'on');
} else {
    error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT ^ E_DEPRECATED);
    @ini_set('display_errors', 'off');
}

// Ready! Go!
require('index2.php');
