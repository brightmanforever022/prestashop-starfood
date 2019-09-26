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

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_3_1_2($module)
{
	$upgrade = $module->upgrade;

	$upgrade->changeColumn('newsletter_pro_subscribers', 'id_gender', "`id_gender` INT(10) UNSIGNED NULL DEFAULT '0'");

	// change the menu name back to Newsletter Pro 
	$upgrade->updateTabName('Newsletter Pro');
	return $upgrade->success();
}