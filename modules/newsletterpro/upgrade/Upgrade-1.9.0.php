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

function upgrade_module_1_9_0($module)
{
	$upgrade = $module->upgrade;

	// hooks update (no)
	// configuration update (no)
	// uncoment this settings to override the customer sleep time
	// $upgrade->updateConfiguration('SLEEP', '3');

	$upgrade->updateConfiguration('TOKEN', $module->token);
	$upgrade->updateConfiguration('RUN_MULTIPLE_TASKS', '0');
	$upgrade->updateConfiguration('SMTP', '0');

	// database update (no)
	$upgrade->createTable('newsletter_pro_smtp', '
		`id_newsletter_pro_smtp` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`name` VARCHAR(64) NOT NULL,
		`domain` VARCHAR(255) NULL DEFAULT NULL,
		`server` VARCHAR(255) NULL DEFAULT NULL,
		`user` VARCHAR(255) NOT NULL,
		`passwd` VARCHAR(255) NULL DEFAULT NULL,
		`encryption` VARCHAR(255) NULL DEFAULT NULL,
		`port` VARCHAR(255) NULL DEFAULT NULL,
		PRIMARY KEY (`id_newsletter_pro_smtp`),
		UNIQUE INDEX `name` (`name`)
	');

	$upgrade->createTable('newsletter_pro_task', "
		`id_newsletter_pro_task` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_newsletter_pro_smtp` INT(10) UNSIGNED NOT NULL DEFAULT '0',
		`date_start` DATE NULL DEFAULT NULL,
		`active` INT(1) NOT NULL DEFAULT '0',
		`template` VARCHAR(128) NOT NULL,
		`status` INT(10) NOT NULL DEFAULT '0',
		`sleep` INT(10) NOT NULL DEFAULT '3',
		`emails_count` INT(10) NOT NULL DEFAULT '0',
		`emails_error` INT(10) NOT NULL DEFAULT '0',
		`emails_success` INT(10) NOT NULL DEFAULT '0',
		`emails_completed` INT(10) NOT NULL DEFAULT '0',
		`done` INT(10) NOT NULL DEFAULT '0',
		`error_msg` LONGTEXT NOT NULL,
		PRIMARY KEY (`id_newsletter_pro_task`)
	");

	$upgrade->createTable('newsletter_pro_task_step', "
		`id_newsletter_pro_task_step` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_newsletter_pro_task` INT(11) UNSIGNED NOT NULL DEFAULT '0',
		`step` INT(11) UNSIGNED NOT NULL DEFAULT '0',
		`step_active` INT(1) NOT NULL DEFAULT '0',
		`emails_to_send` LONGTEXT NULL,
		`emails_sent` LONGTEXT NULL,
		`date` DATETIME NULL DEFAULT NULL,
		PRIMARY KEY (`id_newsletter_pro_task_step`),
		INDEX `id_step` (`step`),
		INDEX `id_task` (`id_newsletter_pro_task`)
	");

	return $upgrade->success();
}