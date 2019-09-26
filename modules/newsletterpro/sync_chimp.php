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
if (isset($newsletterpro->chimp))
	$chimp =& $newsletterpro->chimp;

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

function newsletterpro_sync_chimp($module, $chimp)
{
	if (!NewsletterProConfig::test('LAST_DATE_CHIMP_SYNC'))
		NewsletterProConfig::save('LAST_DATE_CHIMP_SYNC', date('Y-m-d H:i:s') );

	$db_from = NewsletterProConfig::get('LAST_DATE_CHIMP_SYNC');

	if (strtotime($db_from))
		$from = $db_from;
	else
	{
		$from = date('Y-m-d H:i:s');
		NewsletterProConfig::save('LAST_DATE_CHIMP_SYNC', $from );
	}

	$to = date('Y-m-d', strtotime('+1 days'));

	if ($chimp->ping())
		NewsletterProConfig::save('LAST_DATE_CHIMP_SYNC', date('Y-m-d H:i:s') );

	if (Tools::isSubmit('forceSync'))
		$from = '0000-00-00 00:00:00';

	$sync = $chimp->syncLists($from, $to);

	$module->context->smarty->assign($sync);
	$module->context->smarty->assign(array(
		'last_date_chimp_sync'        => NewsletterProConfig::get('LAST_DATE_CHIMP_SYNC'),
		'chimp_last_date_sync_orders' => NewsletterProConfig::get('CHIMP_LAST_DATE_SYNC_ORDERS'),
		'subscription_active'         => (bool)$module->getConfiguration('SUBSCRIPTION_ACTIVE'),
	));
	die($module->context->smarty->fetch(pqnp_template_path($module->dir_location.'views/templates/admin/sync_chimp.tpl')));
}

if (isset($chimp))
	newsletterpro_sync_chimp($newsletterpro, $chimp);
?>