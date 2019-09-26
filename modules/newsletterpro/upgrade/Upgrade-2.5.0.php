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

function upgrade_module_2_5_0($module)
{
	$upgrade = $module->upgrade;
	// configuration update (no)
	$upgrade->deleteConfiguration('FUNC_MAIL_ACTIVE');
	$upgrade->deleteConfiguration('FUNC_MAIL_EMAIL');
	$upgrade->updateConfiguration('DEBUG_MODE', '0');
	// this option already exists, uncomment the line to override the settings acording of the new version standards
	// $upgrade->updateConfiguration('PRODUCT_LINK_REWRITE', (int)Configuration::get('PS_REWRITING_SETTINGS'));

	// database update (yes)
	$upgrade->addColumn('newsletter_pro_smtp', 'method', "`method` INT(1) NOT NULL DEFAULT '1'", 'id_newsletter_pro_smtp');

	if (!$upgrade->columnExists('newsletter_pro_smtp', 'from_name'))
		$upgrade->changeColumn('newsletter_pro_smtp', 'from', '`from_name` VARCHAR(255) NULL DEFAULT NULL');

	$upgrade->addColumn('newsletter_pro_smtp', 'from_email', '`from_email` VARCHAR(255) NULL DEFAULT NULL', 'from_name');
	$upgrade->addColumn('newsletter_pro_smtp', 'reply_to', '`reply_to` VARCHAR(255) NULL DEFAULT NULL', 'from_email');

	return $upgrade->success();
}