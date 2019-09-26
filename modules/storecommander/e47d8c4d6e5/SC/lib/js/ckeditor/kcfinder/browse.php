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

/** This file is part of KCFinder project
  *
  *      @desc Browser calling script
  *   @package KCFinder
  *   @version 3.12
  *    @author Pavel Tzonkov <sunhater@sunhater.com>
  * @copyright 2010-2014 KCFinder Project
  *   @license http://opensource.org/licenses/GPL-3.0 GPLv3
  *   @license http://opensource.org/licenses/LGPL-3.0 LGPLv3
  *      @link http://kcfinder.sunhater.com
  */

/* SC */
define('SC_DIR',dirname(__file__).'/../../../../');
define('_PS_ADMIN_DIR_', 1); // for PS1.5
if (strpos(SC_DIR,'modules')===false) // installation in /adminXXX/
{
    define('SC_INSTALL_MODE',0);
    define('SC_PS_PATH_DIR',realpath(SC_DIR.'../../').'/');
    define('SC_PS_PATH_REL','../../');
}else{ // installation in /modules/
    define('SC_INSTALL_MODE',1);
    define('SC_PS_PATH_DIR',realpath(SC_DIR.'../../../../').'/');
    define('SC_PS_PATH_REL','../../../../');
}
require_once(SC_PS_PATH_DIR.'config/config.inc.php');
require_once(SC_DIR.'lib/php/agent.php');

$sc_agent = new SC_Agent();

// Test if employee is connected
$ajax = Tools::getValue('ajax', 0);
if (!$sc_agent->isLoggedBack()) {
    die('You must be logged to use CKEDITOR.');
}
/* END SC */

require "core/bootstrap.php";
$browser = "kcfinder\\browser"; // To execute core/bootstrap.php on older
$browser = new $browser();      // PHP versions (even PHP 4)
$browser->action();

?>