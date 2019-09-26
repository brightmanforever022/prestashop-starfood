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

function upgrade_module_4_0_0($module)
{
	$upgrade = $module->upgrade;

	$upgrade->updateConfiguration('CROSS_TYPE_CLASS', 'np-icon-cross_5');

	// the installed SEND_METHOD is 1, for the update is 0. For not changing the previous settings
	$upgrade->updateConfiguration('SEND_METHOD', NewsletterPro::SEND_METHOD_DEFAULT);

	$upgrade->updateConfiguration('SEND_ANTIFLOOD_ACTIVE', '1');
	$upgrade->updateConfiguration('SEND_ANTIFLOOD_EMAILS', '100');
	$upgrade->updateConfiguration('SEND_ANTIFLOOD_SLEEP', '10');

	$upgrade->updateConfiguration('SEND_THROTTLER_ACTIVE', '0');
	$upgrade->updateConfiguration('SEND_THROTTLER_LIMIT', '100');
	$upgrade->updateConfiguration('SEND_THROTTLER_TYPE', NewsletterPro::SEND_THROTTLER_TYPE_EMAILS);

	$upgrade->updateConfiguration('PAGE_HEADER_TOOLBAR', array(
		'CSV'                => 1,
		'MANAGE_IMAGES'      => 1,
		'SELECT_PRODUCTS'    => 1,
		'CREATE_TEMPLATE'    => 1,
		'SEND_NEWSLETTERS'   => 1,
		'TASK'               => 1,
		'HISTORY'            => 1,
		'STATISTICS'         => 1,
		'CAMPAIGN'           => 0,
		'SMTP'               => 1,
		'MAILCHIMP'          => 0,
		'FORWARD'            => 0,
		'FRONT_SUBSCRIPTION' => 1,
		'SETTINGS'           => 0,
		'TUTORIALS'          => 1,
		));

	$upgrade->updateConfiguration('SHOW_CLEAR_CACHE', 1);
	$upgrade->updateConfiguration('SEND_EMBEDED_IMAGES', 0);

	$upgrade->createTable('newsletter_pro_send_connection', "
		`id_newsletter_pro_send_connection` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_newsletter_pro_smtp` INT(10) UNSIGNED NOT NULL,
		`state` INT(1) NOT NULL DEFAULT '0',
		PRIMARY KEY (`id_newsletter_pro_send_connection`, `id_newsletter_pro_smtp`)
	");

	$upgrade->createTable('newsletter_pro_filters_selection', '
		`id_newsletter_pro_filters_selection` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`name` VARCHAR(255) NOT NULL,
		`value` TEXT NULL,
		PRIMARY KEY (`id_newsletter_pro_filters_selection`),
		UNIQUE INDEX `name` (`name`)
	');

	$upgrade->addColumn('newsletter_pro_send_step', 'id_newsletter_pro_send_connection', "`id_newsletter_pro_send_connection` INT(11) UNSIGNED NOT NULL DEFAULT '0'", 'id_newsletter_pro_send');

	return $upgrade->success();
}