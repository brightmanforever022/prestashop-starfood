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

function upgrade_module_3_2_8($module)
{
	$upgrade = $module->upgrade;

	$upgrade->createTable('newsletter_pro_email_exclusion', "
		`id_newsletter_pro_email_exclusion` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`email` VARCHAR(255) NOT NULL DEFAULT '0',
		PRIMARY KEY (`id_newsletter_pro_email_exclusion`),
		UNIQUE INDEX `email` (`email`)
	");

	return $upgrade->success();
}