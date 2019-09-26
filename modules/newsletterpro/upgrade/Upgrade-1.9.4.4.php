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

function upgrade_module_1_9_4_4($module)
{
	$upgrade = $module->upgrade;

	// hooks update (no)
	// configuration update (no)
	$upgrade->updateConfiguration('FUNC_MAIL_ACTIVE', '0');
	// database update (no)
	$upgrade->addColumn('newsletter_pro_task', 'send_method', "`send_method` ENUM('mail','smtp') NOT NULL DEFAULT 'mail'", 'template');

	return $upgrade->success();
}