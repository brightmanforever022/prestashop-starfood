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

function upgrade_module_1_9_3_1($module)
{
	$upgrade = $module->upgrade;

	// hooks update (no)
	// configuration update (no)
	// database update (no)

	$upgrade->createTable('newsletter_pro_tpl_history', "
		`id_newsletter_pro_tpl_history` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`template` LONGTEXT NULL,
		`active` INT(1) NULL DEFAULT '1',
		PRIMARY KEY (`id_newsletter_pro_tpl_history`)
	");

	return $upgrade->success();
}