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

function upgrade_module_4_4_0($module)
{
	$upgrade = $module->upgrade;

	$upgrade->updateConfiguration('SHOW_CLEAR_CACHE', 1);

	$upgrade->updateConfiguration('CUSTOMER_SUBSCRIBE_BY_LOI', 1);

	$subscribe_by_category = (int)pqnp_config('SUBSCRIBE_BY_CATEGORY');
	$upgrade->updateConfiguration('DISPLYA_MY_ACCOUNT_NP_SETTINGS', $subscribe_by_category);

	$upgrade->createTable('newsletter_pro_customer_list_of_interests', '
		`id_customer` INT(10) UNSIGNED NOT NULL DEFAULT "0",
		`categories` TEXT NULL,
		UNIQUE INDEX `id_customer` (`id_customer`)
	');

	return $upgrade->success();
}