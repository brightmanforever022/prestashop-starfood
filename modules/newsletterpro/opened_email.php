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
require_once(dirname(__FILE__).'/classes/NewsletterProOpenedEmail.php');
require_once(dirname(__FILE__).'/classes/NewsletterProCookie.php');

$newsletterpro = Module::getInstanceByName('newsletterpro');

if (!$newsletterpro)
	die(Tools::displayError('Cannot create instance of the newsletterpro module!'));

if (Tools::isSubmit('token'))
{
	$id_newsletter = $newsletterpro->getHistoryIdByToken(Tools::getValue('token'));
	$email = Tools::getValue('email');

	$opened_email = new NewsletterProOpenedEmail();
	if ($opened_email->isValid($id_newsletter, $email))
	{
		if (!$opened_email->wasOpened())
			$opened_email->update();
		else
			die($newsletterpro->l('You already opened the template!'));
	}
	else
		die($newsletterpro->l('Invalid token or email address!'));
}
else
	die($newsletterpro->l('Invalid token!'));
?>