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

if (Tools::isSubmit('token_tpl'))
{
	$id_history = (int)Db::getInstance()->getValue('SELECT `id_newsletter_pro_tpl_history` FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history` WHERE `token` = "'.pSQL(Tools::getValue('token_tpl')).'"');

	$token_is_valid = false;
	if (!$id_history)
	{
		/**
		 * If there is a valid token an in the url, will load the selected template from backoffice menu
		 * This is used only when te user send a test email ( not in function at this time )
		 */

		if (Tools::isSubmit('token'))
		{
			$db_token = NewsletterPro::getNewsletterProToken();
			$token = trim(Tools::getValue('token'));

			if ($token === trim($db_token))
				$token_is_valid = true;
			else
				die($newsletterpro->l('Invalid template id!'));
		}
		else
			die($newsletterpro->l('Invalid template id!'));
	}
	else
	{
		$sql = 'SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history` WHERE `id_newsletter_pro_tpl_history`='.(int)$id_history;
		$row = Db::getInstance()->getRow($sql);
		if (!$row)
			die($newsletterpro->l('Invalid template id!'));
	}

	$cfg = array(
		'email' => Tools::getValue('email'),
		'id_history' => $id_history,
		'token_is_valid' => $token_is_valid,
		'jquery_no_conflict' => Tools::isSubmit('jQueryNoConflict'),
	);

	newsletterproRenderTemplate($newsletterpro, $cfg);
}
else
	die($newsletterpro->l('Invalid token!'));

function newsletterproRenderTemplate($module, $cfg)
{
	$email = null;
	$id_history = null;
	$jquery_no_conflict = null;

	extract($cfg);

	$template = NewsletterProTemplate::newHistory((int)$id_history, $email)->load();
	$message = $template->message();

	$context = Context::getContext();
	@ob_end_clean();

	$context->smarty->assign(array(
		'template' => $message['body'],
		'page_title' => $message['title'],
		'jquery_url' => $module->url_location.'views/js/jquery-1.7.2.min.js',
		'jquery_url_exists' => file_exists($module->dir_location.'views/js/jquery-1.7.2.min.js'),
		'jquery_no_conflict' => (int)$jquery_no_conflict,
	));
	return $context->smarty->display(pqnp_template_path($module->dir_location.'views/templates/front/newsletter.tpl'));
}
?>