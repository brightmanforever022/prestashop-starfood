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

	if (isset($debug) && $debug)
	{
		error_reporting(E_ALL ^ E_NOTICE);
		@ini_set('display_errors', 'on');
	}

	if( ! ini_get('date.timezone') )
		date_default_timezone_set('GMT');

	define("BASE_PATH", dirname(__FILE__)."/../");
	define("BASE_CONTENT", dirname(__FILE__)."/../content/");
	
	define("PATH_TEMPLATE", BASE_PATH."theme/template/");
	define("PATH_CONTROLLER", BASE_PATH."controllers/");
	define("PATH_CLASSES", BASE_PATH."classes/");
	define("PATH_INI_DEFAULT", BASE_PATH."ini/");
	define("PATH_INI", BASE_PATH."../../SC_TOOLS/tips/");
	
	// URLs
	define("PATH_CONTENT", "tips/content/");
	
	function __autoload($class_name) {
		include PATH_CLASSES.$class_name . '.php';
	}

	function psql($str)
	{
		global $db;
		return trim($db->quote($str),'\'');
	}
	
	// id_employee
	if (isset($_GET['id_employee'])){ // fr ou en
		setcookie("tips_id_employee",$_GET['id_employee'],time()+60*60*24*180);
		$_COOKIE["tips_id_employee"] = $_GET['id_employee'];
	}
	if (empty($_COOKIE["tips_id_employee"]))
		die('Unable to detect id_employee. Please contact the support team.');
	$user_id = $_COOKIE["tips_id_employee"];
	
	// LANGUAGE
	if (isset($_GET['lang'])){ // fr ou en
		setcookie("tips_lang",$_GET['lang'],time()+60*60*24*180);
		$_COOKIE["tips_lang"] = $_GET['lang'];
	}
	if (empty($_COOKIE["tips_lang"]))
		$_COOKIE["tips_lang"] = 'en';
	$lang_iso = $_COOKIE["tips_lang"];
