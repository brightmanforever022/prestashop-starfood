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

function upgrade_module_3_1_1($module)
{
	$upgrade = $module->upgrade;

	// hooks (yes)
	$upgrade->registerHook('actionShopDataDuplication');

	// configuration update (yes)
	$upgrade->updateConfiguration('SUBSCRIPTION_SECURE_SUBSCRIBE', '0');
	$upgrade->updateConfiguration('SUBSCRIPTION_ACTIVE', '0');

	if (!NewsletterProConfigurationShop::install())
	{
		$upgrade->mergeErrors(NewsletterProConfigurationShop::getErrors());
		return false;
	}

	// database update (yes)
	$upgrade->createTable('newsletter_pro_list_of_interest', "
		`id_newsletter_pro_list_of_interest` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`active` TINYINT(1) NOT NULL DEFAULT '1',
		`position` INT(11) NOT NULL DEFAULT '0',
		PRIMARY KEY (`id_newsletter_pro_list_of_interest`)
	");

	$upgrade->createTable('newsletter_pro_list_of_interest_lang', '
		`id_newsletter_pro_list_of_interest` INT(11) UNSIGNED NOT NULL,
		`id_lang` INT(11) UNSIGNED NOT NULL,
		`id_shop` INT(11) UNSIGNED NOT NULL,
		`name` VARCHAR(255) NULL DEFAULT NULL,
		PRIMARY KEY (`id_newsletter_pro_list_of_interest`, `id_lang`, `id_shop`)
	');

	$upgrade->createTable('newsletter_pro_list_of_interest_shop', "
		`id_newsletter_pro_list_of_interest` INT(10) UNSIGNED NOT NULL DEFAULT '0',
		`id_shop` INT(10) UNSIGNED NOT NULL DEFAULT '0',
		`active` TINYINT(1) NOT NULL DEFAULT '1',
		`position` INT(11) NOT NULL DEFAULT '0',
		PRIMARY KEY (`id_newsletter_pro_list_of_interest`, `id_shop`)
	");

	$upgrade->createTable('newsletter_pro_subscribers', "
		`id_newsletter_pro_subscribers` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_shop` INT(10) UNSIGNED NOT NULL DEFAULT '1',
		`id_shop_group` INT(10) UNSIGNED NOT NULL DEFAULT '1',
		`id_lang` INT(10) UNSIGNED NULL DEFAULT '1',
		`id_gender` INT(10) UNSIGNED NOT NULL,
		`firstname` VARCHAR(32) NULL DEFAULT NULL,
		`lastname` VARCHAR(32) NULL DEFAULT NULL,
		`email` VARCHAR(255) NOT NULL,
		`birthday` DATE NULL DEFAULT NULL,
		`ip_registration_newsletter` VARCHAR(15) NULL DEFAULT NULL,
		`list_of_interest` TEXT NULL,
		`date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`active` TINYINT(1) NOT NULL DEFAULT '1',
		PRIMARY KEY (`id_newsletter_pro_subscribers`),
		INDEX `id_shop` (`id_shop`),
		INDEX `id_lang` (`id_lang`),
		INDEX `id_shop_group` (`id_shop_group`),
		INDEX `id_gender` (`id_gender`)
	");

	$upgrade->createTable('newsletter_pro_subscribers_temp', "
		`id_newsletter_pro_subscribers_temp` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_newsletter_pro_subscription_tpl` INT(11) NOT NULL DEFAULT '0',
		`token` VARCHAR(32) NOT NULL,
		`email` VARCHAR(255) NOT NULL,
		`data` LONGTEXT NOT NULL,
		`date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (`id_newsletter_pro_subscribers_temp`, `id_newsletter_pro_subscription_tpl`)
	");

	$upgrade->createTable('newsletter_pro_subscription_tpl', "
		`id_newsletter_pro_subscription_tpl` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`name` VARCHAR(255) NOT NULL,
		`voucher` VARCHAR(255) NULL DEFAULT NULL,
		`active` TINYINT(1) NOT NULL DEFAULT '0',
		`display_gender` TINYINT(1) NOT NULL DEFAULT '0',
		`display_firstname` TINYINT(1) NOT NULL DEFAULT '0',
		`display_lastname` TINYINT(1) NOT NULL DEFAULT '0',
		`display_language` TINYINT(1) NOT NULL DEFAULT '0',
		`display_birthday` TINYINT(1) NOT NULL DEFAULT '0',
		`display_list_of_interest` TINYINT(1) NOT NULL DEFAULT '0',
		`list_of_interest_type` TINYINT(1) NOT NULL DEFAULT '0',
		`display_subscribe_message` TINYINT(1) NOT NULL DEFAULT '1',
		`body_width` VARCHAR(255) NOT NULL DEFAULT '40%',
		`body_min_width` INT(11) NOT NULL DEFAULT '0',
		`body_max_width` INT(11) NOT NULL DEFAULT '0',
		`body_top` INT(11) NOT NULL DEFAULT '100',
		`show_on_pages` VARCHAR(255) NOT NULL DEFAULT '0',
		`cookie_lifetime` FLOAT NOT NULL DEFAULT '366',
		`start_timer` INT(11) NOT NULL DEFAULT '0',
		`when_to_show` INT(11) NOT NULL DEFAULT '0',
		`date_add` DATETIME NULL DEFAULT NULL,
		`css_style` LONGTEXT NULL,
		PRIMARY KEY (`id_newsletter_pro_subscription_tpl`),
		UNIQUE INDEX `name` (`name`)
	");

	$upgrade->createTable('newsletter_pro_subscription_tpl_lang', "
		`id_newsletter_pro_subscription_tpl` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_lang` INT(10) UNSIGNED NOT NULL DEFAULT '0',
		`id_shop` INT(10) UNSIGNED NOT NULL DEFAULT '0',
		`content` LONGTEXT NULL,
		`subscribe_message` LONGTEXT NULL,
		`email_subscribe_voucher_message` LONGTEXT NULL,
		`email_subscribe_confirmation_message` LONGTEXT NULL,
		PRIMARY KEY (`id_newsletter_pro_subscription_tpl`, `id_lang`, `id_shop`)
	");

	$upgrade->createTable('newsletter_pro_subscription_tpl_shop', "
		`id_newsletter_pro_subscription_tpl` INT(10) UNSIGNED NOT NULL DEFAULT '0',
		`id_shop` INT(10) UNSIGNED NOT NULL DEFAULT '0',
		`active` TINYINT(1) NOT NULL DEFAULT '1',
		`css_style` LONGTEXT NULL,
		`show_on_pages` VARCHAR(255) NOT NULL DEFAULT '0',
		`cookie_lifetime` FLOAT NOT NULL DEFAULT '366',
		`start_timer` INT(11) NOT NULL DEFAULT '0',
		`when_to_show` INT(11) NOT NULL DEFAULT '0',
		PRIMARY KEY (`id_newsletter_pro_subscription_tpl`, `id_shop`)
	");

	$install = new NewsletterProInstall();


	if (!$install->execute())
	{
		$upgrade->mergeErrors($install->getErrors());
		return false;
	}

	// the install object is required
	require_once _NEWSLETTER_PRO_DIR_.'/install/Install-3.1.1.php';

	return $upgrade->success();
}