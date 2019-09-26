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

function upgrade_module_1_8_1($module)
{
	$upgrade = $module->upgrade;

	// hooks update (no)
	$upgrade->unregisterHook('displayHeader');
	$upgrade->registerHook('header');
	// configuration update (no)
	$upgrade->updateConfiguration('GOOGLE_ANALYTICS_ID', '');
	$upgrade->updateConfiguration('GOOGLE_ANALYTICS_ACTIVE', '0');
	$upgrade->updateConfiguration('CAMPAIGN_ACTIVE', '0');
	$upgrade->updateConfiguration('NO_DISPLAY_NUMBER', '5000');
	$upgrade->updateConfiguration('PRODUCT_LINK_REWRITE', '1');

	$upgrade->updatePSConfiguration('NEWSLETTER_PRO_CAMPAIGN', "utm_source=Newsletter\nutm_medium=email\nutm_campaign={newsletter_title}\nutm_content={product_name}", false, 0, 0);
	// database update (no)
	return $upgrade->success();
}