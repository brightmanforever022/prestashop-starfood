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

function upgrade_module_4_3_0($module)
{
	$upgrade = $module->upgrade;

	$upgrade->updateConfiguration('SHOW_CLEAR_CACHE', 1);

	$upgrade->addColumn('newsletter_pro_subscription_tpl', 'allow_multiple_time_subscription', '`allow_multiple_time_subscription` INT(11) NOT NULL DEFAULT "1"', 'when_to_show');
	$upgrade->addColumn('newsletter_pro_subscription_tpl', 'mandatory_fields', '`mandatory_fields` VARCHAR(255) NULL DEFAULT NULL', 'allow_multiple_time_subscription');

	return $upgrade->success();
}