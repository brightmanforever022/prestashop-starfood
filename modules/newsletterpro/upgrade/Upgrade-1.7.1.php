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

function upgrade_module_1_7_1($module)
{
	$upgrade = $module->upgrade;

	// hooks update (no)
	// configuration update (yes)
	$upgrade->updateConfiguration('VIEW_ACTIVE_ONLY', '1');
	$upgrade->updateConfiguration('CUSTOMER_CONFIRM_ON_DELETE', '1');
	$upgrade->updateConfiguration('DISPLAY_ACTIVE_COLUMN', '1');
	$upgrade->updateConfiguration('DISPLAY_ACTIONS_COLUMN', '0');

	// database update (yes)
	$upgrade->changeColumn('newsletter_pro', 'id_newsletter_pro', '`id_newsletter_pro` int(11) UNSIGNED NOT NULL AUTO_INCREMENT');
	$upgrade->changeColumn('newsletter_pro', 'emails_to_send', '`emails_to_send` LONGTEXT');
	$upgrade->changeColumn('newsletter_pro', 'emails_sent', '`emails_sent` LONGTEXT');
	$upgrade->addIndex('newsletter_pro', 'id_step');

	$upgrade->changeColumn('newsletter_pro_email', 'id_newsletter_pro_email', '`id_newsletter_pro_email` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT');
	$upgrade->addColumn('newsletter_pro_email', 'id_lang', '`id_lang` INT(10) UNSIGNED NULL DEFAULT NULL', 'id_shop_group');
	$upgrade->addColumn('newsletter_pro_email', 'firstname', '`firstname` VARCHAR(32) NULL DEFAULT NULL', 'id_lang');
	$upgrade->addColumn('newsletter_pro_email', 'lastname', '`lastname` VARCHAR(32) NULL DEFAULT NULL', 'firstname');
	$upgrade->changeColumn('newsletter_pro_email', 'email', '`email` VARCHAR(255) NOT NULL');
	$upgrade->addColumn('newsletter_pro_email', 'ip_registration_newsletter', '`ip_registration_newsletter` VARCHAR(15) NULL DEFAULT NULL', 'email');
	$upgrade->changeColumn('newsletter_pro_email', 'date_add', '`date_add` DATETIME DEFAULT NULL');
	$upgrade->addIndex('newsletter_pro_email', 'id_shop');
	$upgrade->addIndex('newsletter_pro_email', 'id_lang');

	return $upgrade->success();
}