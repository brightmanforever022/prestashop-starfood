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

$root = dirname(dirname(getcwd()));

require_once($root.'/config/config.inc.php');
require_once($root.'/init.php');

$newsletterpro = Module::getInstanceByName('newsletterpro');

if (!$newsletterpro)
	die(Tools::displayError('Cannot create instance of the newsletterpro module!'));

if (Tools::isSubmit('token'))
{
	$db_token = NewsletterPro::getNewsletterProToken();
	$token = trim(Tools::getValue('token'));

	if ($token !== trim($db_token))
		die($newsletterpro->l('Invalid token!'));

	echo '<pre>';
	try
	{
		$process = NewsletterProMailChimpWebhooks::newInstance()->process();
		die($process);
	}
	catch(Exception $e)
	{
		NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
		die($e->getMessage());
	}
}
else
	die($newsletterpro->l('Invalid token!'));