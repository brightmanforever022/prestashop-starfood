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

require_once dirname(__FILE__).'/../../classes/NewsletterProForward.php';

class NewsletterProForwardModuleFrontController extends ModuleFrontController
{
	public $id_newsletter;

	public function __construct()
	{
		if ((bool)Configuration::get('PS_SSL_ENABLED'))
			$this->ssl = true;

		parent::__construct();
	}

	/**
	 * DEPRACTED
	 */
	public function npl($string)
	{
		return Translate::getModuleTranslation($this->module, $string, Tools::getValue('controller'));
	}

	public function initContent()
	{
		parent::initContent();
		$this->setTemplate('forward.tpl');
	}

	public function setMedia()
	{
		parent::setMedia();

		$css_path = $this->module->getCssPath();
		$this->addCss($css_path.'forward_front.css');

		$this->addJS(array(
			$this->module->uri_location.'views/js/front/forward.js'
		));
	}

	public function getEmailsFromPost()
	{
		$filename = pathinfo(__FILE__, PATHINFO_FILENAME);
		$emails = array();
		$grep_keys = preg_grep('/^email_\d+$/', array_keys($_POST));

		foreach ($grep_keys as $key)
		{
			$email = trim(Tools::getValue($key));
			if (Validate::isEmail($email) && !in_array($email, $emails))
				$emails[] = $email;
			else
				$this->errors = sprintf($this->module->l('The email %s is invalid.', $filename), $email);
		}
		return $emails;
	}

	public function getLink($params = array())
	{
		$email = (Tools::isSubmit('email') ? Tools::getValue('email') : '');
		$token = (Tools::isSubmit('token') ? Tools::getValue('token') : '');

		$params = array_merge($params, array(
			'email' => $email,
			'token' => $token,
		));

		return urldecode($this->context->link->getModuleLink($this->module->name, 'forward', $params));

		// $email = (Tools::isSubmit('email') ? '&email='.Tools::getValue('email'):'');
		// $token = (Tools::isSubmit('token') ? '&token='.Tools::getValue('token'):'');
		// return 'index.php?fc=module&module=newsletterpro&controller=forward'.$email.$token.(!empty($params) ? '&'.http_build_query($params) : '');
	}

	public function postProcess()
	{
		$filename = pathinfo(__FILE__, PATHINFO_FILENAME);

		try
		{
			if ($this->module->getConfiguration('FWD_FEATURE_ACTIVE'))
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
						$this->context->smarty->assign(array(
							'dispalyForm' => true,
							'email'       => $email,
							'emails_js'   => Tools::jsonEncode(NewsletterProForward::getEmailsToByEmailFrom($email)),
							'fwd_limit'   => NewsletterProForward::FOREWORD_LIMIT - count(NewsletterProForward::getEmailsToByEmailFrom($email)),
							'self_link'   => $this->getLink(),
							'ajax_link'   => $this->getLink(),
						));

						// this is ajax request
						if (Tools::getValue('action') == 'submitForward')
						{
							$response = array('status' => false, 'errors' => array(), 'emails' => array());

							$to_email = Tools::getValue('to_email');
							if (Validate::isEmail($to_email))
							{
								$forward = new NewsletterProForward();
								$forward->from = $email;
								$forward->to   = $to_email;

								if ($info = $this->getUserTableByEmail($forward->to))
								{
									$subscribed = (int)Db::getInstance()->getValue('SELECT `'.$info['newsletter'].'` FROM `'._DB_PREFIX_.$info['table'].'` WHERE `'.$info['email'].'` = "'.pSQL($forward->to).'"');
									if ($subscribed)
										$this->errors[] = sprintf($this->module->l('The email %s is already subscribed at our newsletter.', $filename), $forward->to);
									else
									{
										$output = sprintf($this->module->l('The email %s is already registered in our database, but is not subscribed at our newsletter. You can send him a subcription request by clicking ', $filename), $forward->to);
										$output .= '<a class="subscription" href="javascript:{}" style="color: blue;" onclick="NewsletterPro.modules.frontForward.requestFriendSubscription($(this), \''.$token.'\', \''.addcslashes($email, "'").'\', \''.addcslashes($forward->to, "'").'\');">'.$this->module->l('here', $filename).'</a>';
										$output .= '.';
										$this->errors[] = $output;
									}
								}
								else
								{
									if ($forward->from != $forward->to)
									{
										if (!$forward->add())
											$this->errors = array_merge($forward->getErrors(), $this->errors);
									}
									else
										$this->errors[] = $this->module->l('You cannot add your own email address.', $filename);
								}
							}
							else
								$this->errors[] = sprintf($this->module->l('The email address %s is invalid.', $filename), $to_email);

							if (empty($this->errors))
								$response['status'] = true;
							else
								$response['errors'] = array_merge($response['errors'], $this->errors);

							$response['emails'] = NewsletterProForward::getEmailsToByEmailFrom($email);

							die(Tools::jsonEncode($response));
						}
						else if (Tools::getValue('action') == 'deleteEmail')
						{
							$response = array('status' => false, 'errors' => array(), 'emails' => array());

							$delete_email = Tools::getValue('delete_email');

							if ($forward = NewsletterProForward::getInstanceByTo($delete_email))
							{
								if ($forward->from == $email)
								{
									if (!$forward->delete())
										$this->errors = array_merge($forward->getErrors(), $this->errors);
								}
								else
									$this->errors[] = $this->module->l('Permission denied. You cannot delete this email address.', $filename);
							}
							else
								$this->errors[] = sprintf($this->module->l('The email %s cannot be deleted.', $filename), $delete_email);

							if (empty($this->errors))
								$response['status'] = true;
							else
								$response['errors'] = array_merge($response['errors'], $this->errors);

							$response['emails'] = NewsletterProForward::getEmailsToByEmailFrom($email);
							die(Tools::jsonEncode($response));
						}
						else if (Tools::getValue('action') == 'requestFriendSubscription')
						{
							$response = array('status' => false, 'errors' => array(), 'message' => '');

							$post_token   = Tools::getValue('token');
							$from_email   = Tools::getValue('from_email');
							$friend_email = Tools::getValue('friend_email');

							if ($post_token == Tools::encrypt($from_email))
							{
								try
								{
									$file_tpl = dirname(__FILE__).'/../../views/templates/front/forward_subscribe.tpl';
									$this->context->smarty->assign(array(
										'from_email' => $from_email,
									));
									$content = $this->context->smarty->fetch($file_tpl);

									$template = NewsletterProTemplate::newString(array('', $content), $friend_email)->load()->setVariables(array(
										'friend_email' => $from_email
									));

									$message = $template->message();

									$send = NewsletterProSendManager::getInstance()->sendNewsletter(
										$this->module->l('Your friend want to subscribe!', $filename),
										$message['body'],
										$friend_email,
										array('user' => $template->user),
										array(),
										false
									);

									if (!is_array($send) && $send == true)
										$response['message'] = $this->module->l('Your request was successfully sent.', $filename);
									else
										$this->errors = $this->module->l('An error occurred when sending the email.', $filename);
								}
								catch(Exception $e)
								{
									$this->errors[] = $e->getMessage();
								}
							}
							else
								$this->errors[] = $this->module->l('You cannot make this action because the token is not valid.', $filename);

							if (empty($this->errors))
								$response['status'] = true;
							else
								$response['errors'] = array_merge($response['errors'], $this->errors);

							die(Tools::jsonEncode($response));
						}
					}
					else
						$this->errors[] = $this->module->l('Invalid token.', $filename);

				}
				else
					$this->errors[] = sprintf($this->module->l('The email %s is not valid.', $filename), (string)Tools::getValue('email'));
			}
			else
				$this->errors[] = $this->module->l('This feature is no longer active.', $filename);

		}
		catch(Exception $e)
		{
			$this->errors[] = $this->module->l('There is an error, please report this error to the website developer.', $filename);
			if (_PS_MODE_DEV_)
				$this->errors[] = $e->getMessage();

			NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
		}
	}

	public function getUserTableByEmail($email)
	{
		$definition = array(
			'customer'                   => array('email' => 'email', 'newsletter' => 'newsletter'),
			'newsletter_pro_email'       => array('email' => 'email', 'newsletter' => 'active'),
		);

		if ((bool)$this->module->getConfiguration('SUBSCRIPTION_ACTIVE'))
			$definition['newsletter_pro_subscribers'] = array('email' => 'email', 'newsletter' => 'active');
		else
			$definition['newsletter'] = array('email' => 'email', 'newsletter' => 'active');

		foreach ($definition as $table => $fields)
		{
			$sql = 'SELECT COUNT(*) FROM `'._DB_PREFIX_.$table.'` WHERE `'.$fields['email'].'` = "'.pSQL($email).'"';
			if (Db::getInstance()->getValue($sql))
				return array(
						'table'      => $table,
						'email'      => $fields['email'],
						'newsletter' => $fields['newsletter']
						);
		}

		return false;
	}

	public function getHistoryIdByToken($token)
	{
		return (int)Db::getInstance()->getValue('SELECT `id_newsletter_pro_tpl_history` FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history` WHERE `token` = "'.pSQL($token).'"');
	}
}
?>