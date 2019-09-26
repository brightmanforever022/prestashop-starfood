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

function upgrade_module_2_7_1($module)
{
	$upgrade = $module->upgrade;
	// configuration update (no)
	$upgrade->updateConfiguration('FWD_FEATURE_ACTIVE', '1');
	// database update (yes)
	$upgrade->addColumn('newsletter_pro_tpl_history', 'fwd_unsubscribed', "`fwd_unsubscribed` INT(10) NOT NULL DEFAULT '0'", 'unsubscribed');

	$upgrade->createTable('newsletter_pro_fwd_unsibscribed', "
		`id_newsletter_pro_fwd_unsibscribed` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_newsletter_pro_tpl_history` INT(10) UNSIGNED NOT NULL DEFAULT '0',
		`email` VARCHAR(255) NOT NULL,
		`date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (`id_newsletter_pro_fwd_unsibscribed`)
	");

	$upgrade->createTable('newsletter_pro_forward', '
		`id_newsletter_pro_forward` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`from` VARCHAR(128) NOT NULL,
		`to` VARCHAR(128) NOT NULL,
		`date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (`id_newsletter_pro_forward`),
		UNIQUE INDEX `to` (`to`)
	');

	return $upgrade->success();
}