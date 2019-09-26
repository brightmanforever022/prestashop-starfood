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

$root_dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

require_once($root_dir.'/config/config.inc.php');
require_once($root_dir.'/init.php');

$newsletterpro = Module::getInstanceByName('newsletterpro');

@ob_clean();
@ob_end_clean();
header('Content-Type: application/javascript');

if (!Validate::isLoadedObject($newsletterpro))
	die('console.error(\'Invalid NewsletterPro instance.\');');
