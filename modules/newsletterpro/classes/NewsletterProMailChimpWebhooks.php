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

class NewsletterProMailChimpWebhooks
{
	private $module;

	public function __construct()
	{
		$this->module = NewsletterPro::getInstance();
	}

	public static function newInstance()
	{
		return new self();
	}

	public function process()
	{
		$type = Tools::getValue('type');
		$data = Tools::getValue('data');
		switch ($type)
		{
			case 'subscribe':
				return $this->subscribe($data);

			case 'unsubscribe':
				return $this->unsubscribe($data);

			case 'cleaned':
				return $this->cleaned($data);

			case 'upemail':
				return $this->upemail();

			case 'profile':
				return $this->profile();

			default:
				throw new Exception(sprintf($this->module->l('Invalid MailChimp webhook request type.'), $type));
		}
		return false;
	}

	private function subscribe($data)
	{
		$email = $data['email'];
		$errors = $this->module->subscribe($email, Configuration::get('PS_LANG_DEFAULT'), Configuration::get('PS_SHOP_DEFAULT'));

		if (!empty($errors))
		{
			$errors_msg = implode("\n", $errors);
			NewsletterProLog::writeStrip($errors_msg, NewsletterProLog::ERROR_FILE);
			return implode('<br>', $errors);
		}
		else
			return sprintf($this->module->l('The email address "%s" has been subscribed at the newsletter.'), $email);
	}

	private function unsubscribe($data)
	{
		$email = $data['email'];
		$action = $data['action'];

		switch ($action) 
		{
			case 'delete':
				if ($this->module->ini_config['mailchimp_allow_unsubscribe_delete'])
					return $this->deleteEmail($email);
				else
					return $this->unsubscribeEmail($email);
			
			case 'unsub':
			default:
				return $this->unsubscribeEmail($email);
		}

		return false;
	}

	private function cleaned($data)
	{
		$email = $data['email'];
		if ($this->module->ini_config['mailchimp_allow_cleaned_emails'])
			return $this->deleteEmail($email);
		return $this->module->l('The mailchimp webhook "cleaned emails" feature is not activated.');
	}

	private function upemail()
	{
		return $this->featureUnavailableMessage();
	}

	private function profile()
	{
		return $this->featureUnavailableMessage();
	}

	private function deleteEmail($email)
	{
		if ($this->module->executeBouncedEmail($email, array(), -1))
			return sprintf($this->module->l('The email address "%s" has been removed from the newsletter.'), $email);
		else
			return sprintf($this->module->l('The email address "%s" has not been removed from the newsletter. Maybe the email does not exists into database.'), $email);
	}

	private function unsubscribeEmail($email)
	{
		if ($this->module->executeBouncedEmail($email, array(), 0))
			return sprintf($this->module->l('The email address "%s" has been unsubscribed from the newsletter.'), $email);
		else
			return sprintf($this->module->l('The email address "%s" has not been unsubscribed from the newsletter. Maybe the email does not exists into database.'), $email);
	}

	private function featureUnavailableMessage()
	{
		return $this->module->l('This webhook feature is unavailable.');
	}
}