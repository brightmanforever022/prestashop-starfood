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

class NewsletterProSubscribeModuleFrontController extends ModuleFrontController
{
	public $id_newsletter;

	public function __construct()
	{
		if ((bool)Configuration::get('PS_SSL_ENABLED'))
			$this->ssl = true;

		parent::__construct();
	}

	public function initContent()
	{
		parent::initContent();
		$this->setTemplate('subscribe.tpl');
	}

	public function postProcess()
	{
		$filename = pathinfo(__FILE__, PATHINFO_FILENAME);

		try
		{
			if (Tools::isSubmit('email') && ($email = Tools::getValue('email')))
			{
				$token = Tools::getValue('token');
				$token_ok = false;

				if (Tools::isSubmit('mc_token'))
					$token_ok = NewsletterProMailChimpToken::validateToken('mc_token');

				if (!$token_ok)
					$token_ok = $token == Tools::encrypt($email);

				if ($token_ok)
				{
					$errors = $this->module->subscribe($email);
					$this->errors = array_merge($this->errors, $errors);
				}
				else
					$this->errors[] = $this->module->l('Invalid token for subscription.', $filename);

			}
			else
				$this->errors[] = sprintf($this->module->l('The email %s is not valid.', $filename), (string)Tools::getValue('email'));

			if (empty($this->errors))
			{
				$this->context->smarty->assign(array(
					'success_message' => $this->module->l('You have successfully subscribed at our newsletter.', $filename),
				));
			}

		}
		catch(Exception $e)
		{
			$this->errors[] = $this->module->l('There is an error, please report this error to the website developer.', $filename);
			if (_PS_MODE_DEV_)
				$this->errors[] = $e->getMessage();

			NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
		}
	}

	public function getHistoryIdByToken($token)
	{
		return (int)Db::getInstance()->getValue('SELECT `id_newsletter_pro_tpl_history` FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history` WHERE `token` = "'.pSQL($token).'"');
	}
}
?>