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

	$email = trim(Tools::getValue('email'));

	if (!$email)
		die($newsletterpro->l('The email address field is empty.'));

	$bounce_action = Tools::getValue('action');
	$bounce_action = (($bounce_action != 'delete' && $bounce_action != 'unsubscribe') ? 'delete' : $bounce_action);
	$bounce_method = $bounce_action == 'delete' ? (int)('-1') : 0;

	echo '<pre>';
	$action_msg = ($bounce_method == -1 ? $newsletterpro->l('removed') : $newsletterpro->l('unsubscribed'));

	if ($newsletterpro->executeBouncedEmail($email, array(), $bounce_method))
		die($newsletterpro->l(sprintf('The bounced email %s has been %s from the database.', $email, $action_msg)));
	else
		die($newsletterpro->l(sprintf('The bounced email %s has not been %s from the database. Maybe the email does not exists into database.', $email, $action_msg)));
}
else
	die($newsletterpro->l('Invalid token!'));
?>