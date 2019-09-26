<?php
/**
* 2013-2015 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright 2013-2015 Ovidiu Cimpean
* @version   Release: 4
* @license   Do not edit, modify or copy this file
*/

header('Access-Control-Allow-Origin: *');

$root_dir = dirname(dirname(dirname(dirname(__FILE__))));

require_once($root_dir.'/config/config.inc.php');
require_once($root_dir.'/init.php');

$newsletterpro = Module::getInstanceByName('newsletterpro');

if (!Validate::isLoadedObject($newsletterpro))
	die('Invalid NewsletterPro instance.');

if (_PS_MAGIC_QUOTES_GPC_ && class_exists('NewsletterPro'))
{
	$_POST = NewsletterPro::strip($_POST);
	$_GET  = NewsletterPro::strip($_GET);
}

$newsletterpro->ajaxProcess();
?>