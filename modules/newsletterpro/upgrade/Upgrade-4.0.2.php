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

function upgrade_module_4_0_2($module)
{
	$upgrade = $module->upgrade;

	$upgrade->updateConfiguration('SHOW_CLEAR_CACHE', 1);
	$upgrade->updateConfiguration('CHIMP_SYNC_UNSUBSCRIBED', 1);

	$upgrade->addColumn('newsletter_pro_email', 'filter_name', '`filter_name` VARCHAR(255) NULL DEFAULT NULL', 'ip_registration_newsletter');

	return $upgrade->success();
}