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

function upgrade_module_2_4_0($module)
{
	$upgrade = $module->upgrade;
	// configuration update (no)
	// database update (yes)
	$upgrade->changeColumn('newsletter_pro_send', 'date', '`date` DATETIME NULL DEFAULT NULL');
	$upgrade->changeColumn('newsletter_pro_task', 'date_start', '`date_start` DATETIME NULL DEFAULT NULL');
	$upgrade->addColumn('newsletter_pro_tpl_history', 'token', '`token` VARCHAR(32) NOT NULL', 'id_newsletter_pro_tpl_history');
	$upgrade->changeColumn('newsletter_pro_tpl_history', 'clicks', "`clicks` INT(10) NOT NULL DEFAULT '0'");
	$upgrade->addColumn('newsletter_pro_tpl_history', 'opened', "`opened` INT(10) NOT NULL DEFAULT '0'", 'clicks');
	$upgrade->addColumn('newsletter_pro_tpl_history', 'unsubscribed', "`unsubscribed` INT(10) NOT NULL DEFAULT '0'", 'opened');
	$upgrade->createTable('newsletter_pro_unsibscribed', "
		`id_newsletter_pro_unsibscribed` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_newsletter_pro_tpl_history` INT(10) UNSIGNED NOT NULL DEFAULT '0',
		`email` VARCHAR(255) NOT NULL,
		`date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (`id_newsletter_pro_unsibscribed`)
	");

	return $upgrade->success();
}