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

class NewsletterProSubscribeConfirmationModuleFrontController extends ModuleFrontController
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
		$this->setTemplate('subscribeconfirmation.tpl');
	}

	public function getLink($params = array())
	{
		$params = array_merge($params, array(
			'token' => Tools::getValue('token')
		));

		return urldecode($this->context->link->getModuleLink($this->module->name, 'subscribeconfirmation', $params));

		// $token = (Tools::isSubmit('token') ? '&token='.Tools::getValue('token'):'');
		// return 'index.php?fc=module&module=newsletterpro&controller=subscribeconfirmation'.$token.(!empty($params) ? '&'.http_build_query($params) : '');
	}

	public function postProcess()
	{
		$filename = pathinfo(__FILE__, PATHINFO_FILENAME);

		try
		{
			$token = Tools::getValue('token');
			$id    = NewsletterProSubscribersTemp::getIdByToken($token);
			$subscriber = new NewsletterProSubscribersTemp($id);

			if (Validate::isLoadedObject($subscriber))
			{
				if (!$subscriber->moveToSubscribers())
					$this->errors = array_merge($this->errors, $subscriber->getErrors());
			}
			else
				$this->errors[] = $this->module->l('This link has expired or the token in invalid.', $filename);

			if (empty($this->errors))
			{
				$success_message = array();

				$success_message[] = $this->module->l('You have successfully subscribed at our newsletter.', $filename);

				if ((int)$subscriber->id_newsletter_pro_subscription_tpl)
				{
					$subscrbtion_template = new NewsletterProSubscriptionTpl((int)$subscriber->id_newsletter_pro_subscription_tpl);
					if (Validate::isLoadedObject($subscrbtion_template))
					{
						if (trim($subscrbtion_template->voucher))
						{
							$subscribe_voucher_msg = trim($subscrbtion_template->renderEmailSubscribeVoucherMessage((int)$this->context->language->id));
							$subscribe_voucher_msg_strip = trim(strip_tags($subscribe_voucher_msg));

							$success_message[] = sprintf($this->module->l('You can use this voucher %s.', $filename), $subscrbtion_template->voucher);

							if (!empty($subscribe_voucher_msg_strip))
							{
								NewsletterProSendManager::getInstance()->sendNewsletter(
									$this->module->l('Newsletter Subscription Voucher', $filename),
									$subscribe_voucher_msg,
									$subscriber->email,
									array(),
									array(),
									false
								);
							}
						}
					}
				}

				$this->context->smarty->assign(array(
					'success_message' => $success_message,
				));
			}
		}
		catch(Exception $e)
		{
			if (_PS_MODE_DEV_)
				$this->errors[] = $e->getMessage();
			else
				$this->errors[] = $this->module->l('There is an error, please report this error to the website developer.', $filename);

			NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
		}
	}
}
?>