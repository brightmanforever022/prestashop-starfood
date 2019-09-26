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

function upgrade_module_3_2_7($module)
{
	$upgrade = $module->upgrade;

	if (isset($module->configuration['CHIMP']) && !isset($module->configuration['CHIMP']['ORDERS_CHECKBOX']))
	{
		$module->configuration['CHIMP']['ORDERS_CHECKBOX'] = '1';
		if (!$module->updateDbConfiguration())
		{
			$upgrade->addError(sprintf('Cannot update the configuration with the name "%s".', 'ORDERS_CHECKBOX'));
			return false;
		}
	}

	if (!$upgrade->valueExists('newsletter_pro_config', 'name', 'CHIMP_LAST_DATE_SYNC_ORDERS'))
		$upgrade->insertValue('newsletter_pro_config', array(
			'name' => 'CHIMP_LAST_DATE_SYNC_ORDERS',
			'value' => '0000-00-00 00:00:00',
		));

	return $upgrade->success();
}