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

header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
@ini_set('upload_max_filesize', '100M');
@ini_set('default_charset', 'utf-8');
@ini_set('max_execution_time', 0);
@ini_set('auto_detect_line_endings', '1'); // correct Mac error on eof
define('MAX_LINE_SIZE', 8192 );

define('SC_DIR',dirname(__file__).'/');
if (strpos(SC_DIR,'modules')===false) // installation in /adminXXX/
{
	define('SC_INSTALL_MODE',0);
	define('SC_PS_PATH_DIR',realpath(SC_DIR.'../../').'/');
	define('SC_PS_PATH_REL','../../');
}else{ // installation in /modules/
	define('SC_INSTALL_MODE',1);
	define('SC_PS_PATH_DIR',realpath(SC_DIR.'../../../../').'/');
	define('SC_PS_PATH_REL','../../../../');
	define('SC_PS_MODULE_PATH_DIR',realpath(SC_DIR.'../../').'/'); // ..../modules/storecommander/
	define('SC_PS_MODULE_PATH_REL','../../');
}
@define('SC_COPYRIGHT','Store Commander Copyright 2009-'.date('Y').' Sarl Mise En Prod');
define('PS_WEB_PATH',$_SERVER['SERVER_NAME']);

define('_PS_ADMIN_DIR_', 1); // for PS1.5
define('SC_JQUERY','lib/js/jquery-1.7.1.min.js');
define('SC_JSFUNCTIONS','lib/js/functions_025.js');
define('SC_JSDHTMLX','lib/js/dhtmlx_008.js');
define('SC_CSSSTYLE','lib/css/style_011.css');
define('SC_PLUPLOAD','lib/all/upload/'); // 1.5.2
define('SC_UISETTINGS_VERSION','4');
define('SC_EXPORT_VERSION','3');
define('SC_EXTENSION_VERSION','2');
ob_start();
require_once(SC_PS_PATH_DIR.'config/config.inc.php');
ob_end_clean();
require_once(SC_DIR.'lib/php/agent.php');
require_once(SC_DIR.'lib/php/uisettings.php');
require_once(SC_DIR.'lib/php/uisettings_convert.php');
require_once(SC_DIR.'lib/php/extension_convert.php');
require_once(SC_DIR.'lib/php/extension.php');
require_once(SC_DIR.'lib/php/help.php');
require_once(SC_DIR.'lib/php/db_update.php');
require_once(SC_DIR.'lib/php/utf8.php');
require_once(SC_DIR.'lib/php/queue_log.php');
require_once(SC_DIR.'lib/php/export_convert.php');
require_once(SC_DIR.'lib/php/import_convert.php');
require_once(SC_DIR.'lib/php/custom_settings.php');

define('SC_CSSDHTMLX','lib/js/dhtmlx_012_'._s('APP_SKIN_INTERFACE').'.css');



