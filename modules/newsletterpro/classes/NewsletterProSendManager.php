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

require_once dirname(__FILE__).'/NewsletterProPrepareNewsletter.php';

class NewsletterProSendManager
{
	public $prepare;

	public $fwd_success_count = 0;

	public static $instance;

	private $template_name;

	public function __construct()
	{
		$this->context = Context::getContext();
		$this->prepare = NewsletterProPrepareNewsletter::newInstance();
	}

	public static function newInstance()
	{
		return new self();
	}

	public static function getInstance()
	{
		if (!isset(self::$instance))
			self::$instance = self::newInstance();

		return self::$instance;
	}

	public function setTemplateNameForAttachment($template_name)
	{
		$this->template_name = $template_name;
	}
	/**
	 * Send newsletter
	 *
	 * @param  string  $subject
	 * @param  string  $template
	 * @param  string  $to
	 * @param  array   $data
	 * @param  array   $forward
	 * @param  boolean $shut_down_register This should be true only in the progress of sending newsletters in the bakcofice
	 * @return array
	 */
	public function sendNewsletter($subject, $template, $to, $data = array(), $forward = array(), $shut_down_register = true, $exclude_email = false)
	{
		if ($shut_down_register)
			register_shutdown_function(array($this, 'sendNewslettersShutdown'));

		$errors      = array();

		$id_smtp     = null;
		$send_method = null;
		$user        = null;

		// id_smtp, send_method, user
		extract($data);

		if (isset($user))
			$this->setUserContext($user);

		if (isset($send_method) && $send_method == 'mail')
			$mail = NewsletterProMail::getInstance(NewsletterProMail::getDefaultMail());
		else if (isset($id_smtp))
			$mail = NewsletterProMail::newInstance((int)$id_smtp);
		else
			$mail = NewsletterProMail::getInstanceByContext();

		if ($mail)
		{
			if (isset($this->template_name))
				$mail->setTemplateNameForAttachment($this->template_name);

			if ($exclude_email)
			{
				$errors[] = NewsletterPro::getInstance()->l('The email is excluded from the process of sending. The email exists into exclusion list.');
				return $errors;
			}
			$this->fwd_success_count = 0;

			if (NewsletterPro::getConfiguration('DEBUG_MODE'))
				$send = $mail->send($subject, $template, $to);
			else 
				$send = @$mail->send($subject, $template, $to);

			if ($send)
			{
				if ((bool)NewsletterPro::getConfiguration('FWD_FEATURE_ACTIVE') && self::isForward($forward))
				{
					$mail->sendForward($forward['data'], $forward['type'], $forward['from'], (int)NewsletterPro::getConfiguration('SLEEP'));
					$this->fwd_success_count += $mail->getSuccessFwdCount();
				}

				return true;
			}
			else
				$errors = array_merge($errors, $mail->getErrors());
		}
		else
			$errors[] = NewsletterPro::getInstance()->l('Cannot establish the email connection.');

		return $errors;
	}

	/**
	 * Register send newsletter shutdown function
	 *
	 * @return die
	 */
	public function sendNewslettersShutdown()
	{
		$errors = array();
		$response = array(
			'status' => false,
			'exit'   => false,
			'errors' => &$errors
		);

		$ob_content = ob_get_contents();
		@ob_end_clean();

		if (preg_match('/Fatal error/', $ob_content))
		{
			if (preg_match('/Maximum execution time/', $ob_content))
				$errors[] = NewsletterPro::getInstance()->l('PHP max execution time exceeded because of the followers. The newsletter will jump to the next email address.');
			else
				$errors[] = $ob_content;

			die(Tools::jsonEncode($response));
		}
		die($ob_content);
	}

	/**
	 * Set user context
	 *
	 * @param object $user
	 */
	public function setUserContext($user)
	{
		if (isset($user) && gettype($user) == 'object')
		{
			if ($user->user_type == 'customer')
				$this->context = NewsletterProCustomerContext::getContext((int)$user->id);
		}
	}

	/**
	 * Check if the email si forward
	 *
	 * @param  string  $forward
	 * @return boolean
	 */
	public static function isForward($forward)
	{
		if (is_array($forward) && !empty($forward) && isset($forward['data'], $forward['type'], $forward['from']))
			return true;
		return false;
	}

	/**
	 * Build forwarder
	 *
	 * @param  array $data
	 * @param  string $type
	 * @param  string $to
	 * @return array
	 */
	public static function buildForward($data, $type, $to)
	{
		return array(
			'data'  => $data,
			'type'  => $type,
			'from'  => $to,
		);
	}

	/**
	 * Send a test email
	 *
	 * @param  string $to
	 * @return json
	 */
	public function sendMailTest($to, $id_smtp = null)
	{
		$response = NewsletterProAjaxResponse::newInstance(array(
			'status' => false,
			'msg' => '',
		));

		try
		{
			if (isset($id_smtp) && $id_smtp > 0)
			{
				$mail = NewsletterProMail::newInstance((int)$id_smtp);

				if (!Validate::isLoadedObject($mail))
					$mail = false;
			}
			else
				$mail = NewsletterProMail::getInstanceByContext();

			if ($mail)
			{
				$subject  = (string)$this->context->shop->name.' - '.NewsletterPro::getInstance()->l('Test Email Connection');
				$template = '<h2>'.NewsletterPro::getInstance()->l('The connection is valid!').'</h2>';

				if (NewsletterPro::getConfiguration('DEBUG_MODE'))
					$send = $mail->send($subject, $template, $to); 
				else
					$send = @$mail->send($subject, $template, $to);

				if (!$send)
					$response->mergeErrors($mail->getErrors());
				else
					$response->set('status', true);
			}
			else
				$response->addError(NewsletterPro::getInstance()->l('Cannot establish the email connection.'));

		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		if (!$response->success())
			$response->set('msg', implode('<br>', $response->getErrors()));

		return $response->display();
	}

	/**
	 * Send a test email
	 *
	 * @param  string $email
	 * @param  string $template_name
	 * @param  int $id_smtp
	 * @param  boolean $send_method
	 * @return json
	 */
	public function sendTestNewsletter($email, $template_name = null, $id_smtp = null, $send_method = null, $id_lang = null)
	{
		$response = NewsletterProAjaxResponse::newInstance(array(
			'status' => false,
			'msg' => '',
		));

		try
		{
			if (Validate::isEmail($email))
			{
				$template_name = isset($template_name) ? $template_name : pqnp_config('NEWSLETTER_TEMPLATE');
	
				$template = NewsletterProTemplate::newFile($template_name, array($email, NewsletterProTemplateUser::USER_TYPE_EMPLOYEE))->load();

				$message = $template->message(null, false, $id_lang, true);

				$title = $message['title'];
				$body = $message['body'];

				if ($template->user)
				{
					if (!isset($id_smtp))
					{
						$connections = NewsletterProSendConnection::getConnections();
						if (count($connections))
							$id_smtp = $connections[0]['id_newsletter_pro_smtp'];
					}

					$this->setTemplateNameForAttachment($template->name);
					$send = $this->sendNewsletter($title, $body, $template->user->to(), array(
								'user'        => $template->user,
								'id_smtp'     => $id_smtp,
								'send_method' => $send_method,
							));

					if (!is_array($send) && $send == true)
						$response->set('msg', NewsletterPro::getInstance()->l('Email sent'));
					else if (is_array($send))
						$response->addError(implode('<br>', $send));
					else
						$response->addError(NewsletterPro::getInstance()->l('Error sending test email'));
				}
				else
					$response->addError(NewsletterPro::getInstance()->l('Invalid user creation!'));
			}
			else
				$response->addError(NewsletterPro::getInstance()->l('The email is not not valid'));

		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}
		
		if (!$response->success())
			$response->set('msg', implode('<br>', $response->getErrors()));

		return $response->display();
	}
}