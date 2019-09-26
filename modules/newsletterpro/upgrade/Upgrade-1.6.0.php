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

function upgrade_module_1_6_0($module)
{
	$upgrade = $module->upgrade;

	// hooks update (no)

	// configuration update (yes)
	// the last value was 3, uncomment the line if you want to chage it to 1
	// $upgrade->updateConfiguration('SLEEP', '1');
	$upgrade->updateConfiguration('DISPLAY_PRODUCT_IMAGE', '1');

	// database update (yes)

	$upgrade->addColumn('newsletter_pro', 'id_step', "`id_step` INT(11) UNSIGNED NOT NULL DEFAULT '0'", 'id_newsletter_pro');
	$upgrade->addColumn('newsletter_pro', 'count_to_send', "`count_to_send` INT(11) UNSIGNED NOT NULL DEFAULT '0'", 'active');
	$upgrade->addColumn('newsletter_pro', 'count_sent_succ', "`count_sent_succ` INT(11) UNSIGNED NOT NULL DEFAULT '0'", 'count_to_send');
	$upgrade->addColumn('newsletter_pro', 'count_sent_err', "`count_sent_err` INT(11) UNSIGNED NOT NULL DEFAULT '0'", 'count_sent_succ');
	$upgrade->addColumn('newsletter_pro_email', 'active', "`active` TINYINT(1) NOT NULL DEFAULT '1'", 'date_add');

	return $upgrade->success();
}