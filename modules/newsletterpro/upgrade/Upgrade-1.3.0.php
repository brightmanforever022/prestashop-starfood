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

function upgrade_module_1_3_0($module)
{
	$upgrade = $module->upgrade;

	// hooks update (yes)
	$upgrade->registerHook('displayHeader');
	$upgrade->registerHook('actionCustomerAccountAdd');

	// configuration update (yes)

	$upgrade->deletePSConfiguration('PS_NEWSLETTER_PRO_TEMPLATE');
	$upgrade->deletePSConfiguration('PS_NEWSLETTER_PRO_P_TEMPLATE');
	$upgrade->deletePSConfiguration('PS_NEWSLETTER_PRO_IMAGE_TYPE');
	$upgrade->deletePSConfiguration('PS_NEWSLETTER_PRO_SLEEP');
	$upgrade->deletePSConfiguration('PS_NEWSLETTER_PRO_CURRENCY');
	$upgrade->deletePSConfiguration('PS_NEWSLETTER_PRO_LANG');

	$upgrade->updateConfiguration('NEWSLETTER_TEMPLATE', 'sample.html');
	$upgrade->updateConfiguration('PRODUCT_TEMPLATE', 'sample.html');

	$type_home = 'home';
	$upgrade->updateConfiguration('IMAGE_TYPE', ($module->isLowerVersion('1.5.1.0') ? $type_home : $type_home.'_default'));
	$upgrade->updateConfiguration('SLEEP', '3');
	$upgrade->updateConfiguration('CURRENCY', (int)Configuration::get('PS_CURRENCY_DEFAULT'));
	$upgrade->updateConfiguration('LANG', (int)Configuration::get('PS_LANG_DEFAULT'));
	$upgrade->updateConfiguration('CATEGORIES_DEPTH', ($module->isLowerVersion('1.5.0.5') ? 1 : 2));

	// database update (yes)
	$upgrade->addColumn('newsletter_pro_email', 'id_shop', "`id_shop` INT(10) UNSIGNED NOT NULL DEFAULT '1'", 'id_newsletter_pro_email');
	$upgrade->addColumn('newsletter_pro_email', 'id_shop_group', "`id_shop_group` INT(10) UNSIGNED NOT NULL DEFAULT '1'", 'id_shop');

	return $upgrade->success();
}