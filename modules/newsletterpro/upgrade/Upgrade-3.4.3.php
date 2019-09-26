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

function upgrade_module_3_4_3($module)
{
	$upgrade = $module->upgrade;

	$upgrade->addColumn('newsletter_pro_subscription_tpl', 'terms_and_conditions_url', '`terms_and_conditions_url` TEXT NULL', 'when_to_show');
	$upgrade->addColumn('newsletter_pro_subscription_tpl_shop', 'terms_and_conditions_url', '`terms_and_conditions_url` TEXT NULL', 'when_to_show');

	return $upgrade->success();
}