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

header('Access-Control-Allow-Origin: *');
$root = dirname(dirname(getcwd()));

require_once($root.'/config/config.inc.php');
require_once($root.'/init.php');

$newsletterpro = Module::getInstanceByName('newsletterpro');

if (Tools::isSubmit('token'))
{
	$db_token = NewsletterPro::getNewsletterProToken();
	$token = trim(Tools::getValue('token'));

	if ($token !== trim($db_token))
		die('Invalid Token!');
}
else
	die('Invalid Token!');

ignore_user_abort(true);
set_time_limit(0);
@ini_set('max_execution_time', '0');
/* @ini_set('max_execution_time', '24000'); */

function newsletterpro_sync_newsletter_block($module)
{
	$response = Tools::jsonDecode($module->importEmailsFromBlockNewsletterCron(pqnp_config('LAST_DATE_NEWSLETTER_BLOCK_SYNC')),  true);

	echo '<pre>';
	if (!empty($response['errors']))
	{
		echo $module->l('Errors');
		echo '<br>';
		echo '<br>';
		die(implode('<br>', $response['errors']));
	}
	else
	{
		pqnp_config('LAST_DATE_NEWSLETTER_BLOCK_SYNC', date('Y-m-d H:i:s'));
		die($response['msg']);
	}
}

newsletterpro_sync_newsletter_block($newsletterpro);
?>