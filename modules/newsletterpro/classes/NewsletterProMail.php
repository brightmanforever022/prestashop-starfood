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

/* Swfit Version 5.3.1 */
require_once dirname(__FILE__).'/../libraries/swift/swift_required.php';

if ((int)pqnp_ini_config('swift_init')) {
	require_once dirname(__FILE__).'/../libraries/swift/swift_init.php';
}

require_once dirname(__FILE__).'/NewsletterProTemplateDecoratorReplacements.php';

class NewsletterProMail extends NewsletterProMailSwift implements NewsletterProMailInterface 
{
	public $failed_recipients = array();

	public $charset = 'UTF-8';

	private $message;

	private $template_name;

	private $attachment_exists;

	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->context = Context::getContext();
		$this->module  = NewsletterPro::getInstance();
		$this->attachment_exists = false;

		if ($this->encryption != 'tls' && $this->encryption != 'ssl')
			$this->encryption = null;

		if (trim($this->from_name) == '')
			$this->from_name  = (string)$this->context->shop->name;

		if (trim($this->reply_to) == '')
			$this->reply_to  = (string)$this->from_email;
	}

	private function validateEmail(&$email)
	{
		$invalid_to_email_message = $this->module->l('The email address "%s" is not valid.');

		if (is_array($email))
		{
			foreach ($email as $address => $name) 
			{
				if (is_int($address))
				{
					// in this case name is email
					if (!NewsletterPro_Swift_Validate::email($name))
					{
						if (count($email) == 1)
						{
							$this->addError(sprintf($invalid_to_email_message, $name));
							return false;
						}
						else
						{
							$this->failed_recipients[] = $name;
							unset($email[$address]);
						}
					}
				}
				else
				{
					if (!NewsletterPro_Swift_Validate::email($address))
					{
						if (count($email) == 1)
						{
							$this->addError(sprintf($invalid_to_email_message, $address));
							return false;
						}
						else
						{
							$this->failed_recipients[] = $address;
							unset($email[$address]);
						}
					}
				}
			}
		}
		else if (!NewsletterPro_Swift_Validate::email($email))
		{
			$this->addError(sprintf($invalid_to_email_message, $email));
			return false;
		}
		
		return true;
	}

	private function validateSubject($subject)
	{
		if (!Validate::isMailSubject($subject))
		{
			$this->addError($this->module->l('Invalid email subject.'));
			return false;
		}
		return true;
	}

	public function newSwiftTransport()
	{
		if ($this->method == self::METHOD_MAIL)
			return NewsletterPro_Swift_MailTransport::newInstance();
		else if ($this->method == self::METHOD_SMTP)
			return NewsletterPro_Swift_SmtpTransport::newInstance()->setHost($this->server)->setPort($this->port)->setEncryption($this->encryption)->setUsername($this->user)->setPassword($this->passwd);

		throw new Exception(NewsletterPro::getInstance()->l('Invalid mail method.'));
	}

	public function newSwiftMessage()
	{
		$message = NewsletterPro_Swift_Message::newInstance()->setCharset($this->charset)->setFrom(array($this->from_email => $this->from_name))->setSender(array($this->from_email => $this->from_name))->setReplyTo(array($this->reply_to => $this->from_name));

		$type = $message->getHeaders()->get('Content-Type');
		$type->setValue('text/html');
		$type->setParameter('charset', 'utf-8');

		return $message;
	}

	public function newSwiftMailer($transport = null)
	{
		return NewsletterPro_Swift_Mailer::newInstance(isset($transport) ? $transport : $this->newSwiftTransport());
	}

	public function getSentFaildRecipients()
	{
		return $this->failed_recipients;
	}

	public function setTemplateNameForAttachment($template_name)
	{
		$this->template_name = $template_name;
	}

	/**
	 * Send one email
	 * This method accept also multiple to emails, but is not recommended
	 * 
	 * @param  string $subject  Email subject
	 * @param  string $template Email html template
	 * @param  array/string $to Email to
	 * @return boolean          Retrun true if the email was successfuly sent
	 */
	public function send($subject, $template, $to, &$failed_recipients = array())
	{
		$this->failed_recipients = $failed_recipients;
		$num_sent = 0;

		try
		{
			if (!$this->validateSubject($subject))
				return 0;

			if (!$this->validateEmail($to))
				return 0;

			$transport = $this->newSwiftTransport();

			$message = $this->message = NewsletterPro_Swift_Message::newInstance()->setCharset($this->charset)->setContentType('text/html')->setFrom(array($this->from_email => $this->from_name))->setSender(array($this->from_email => $this->from_name))->setReplyTo(array($this->reply_to => $this->from_name))->setTo($to)->setSubject($subject);

			if ((int)$this->list_unsubscribe_active) {
				self::setHeaderListUnsubscribe($message, self::getEmailFromTo($to), null, null, $this->list_unsubscribe_email);
			}

			if (isset($this->template_name) && (!($this->attachment_exists)))
			{
				$this->attachment_exists = true;
				NewsletterProAttachment::setAttachmentToMessage($this->template_name, $message);
			}

			$template = NewsletterPro::getInstance()->embedImages($message, $template);
			$message->setBody($template, 'text/html');

			$mailer   = NewsletterPro_Swift_Mailer::newInstance($transport);
			$num_sent = $mailer->send($message, $this->failed_recipients);

			if (!$num_sent)
			{
				if ($this->method == self::METHOD_MAIL)
					$this->addError(sprintf($this->module->l('Failed to send the email. The problem can be related with the %s function.'), 'php mail()'));
				else
				{
					if (!function_exists('proc_open') && $this->method == self::METHOD_SMTP)
						$this->addError(sprintf($this->module->l('Failed to send the email. The problem is related with the %s function and is happen only if you use the SMTP method. You need to contact your hosting provider to solve the problem with that function.'), 'php proc_open'));
					else
						$this->addError($this->module->l('Failed to send the email.'));
				}
			}
		}
		catch(NewsletterPro_Swift_RfcComplianceException $e)
		{
			$this->addError($e->getMessage());
		}

		return (int)$num_sent;
	}

	/**
	 * Send the newsletter to the forwarders
	 * If a forwarder exists in the database, will be deleted from the forwarder list
	 * @param  array/string $from The forwarder email
	 * @return boolean         Retrun true if the email was successfuly sent
	 */
	public function sendForward($data, $type, $from, $sleep = 1)
	{
		$errors_status = array();
		try
		{
			if ($forwards = NewsletterProForward::getForwarders($from))
			{
				$email_info       = self::getEmailInfo($from);

				$fwd_recipients = new NewsletterProForwardRecipients();
				$fwd_recipients->add(array($email_info['name'] => $email_info['email']), $forwards);

				$fwd_recipients->buildForwardersRecursive($email_info['email']);

				$recipients = $fwd_recipients->getRecipients();

				foreach ($recipients as $parent_email => $child_emails)
				{
					$this->from_name  = $fwd_recipients->getRecipientName($parent_email);
					$this->from_email = $parent_email;

					foreach ($child_emails as $email)
					{

						if ($template = $this->getTemplate($email, $data, $type))
						{
							$template->setForwarder(true);
							$template->setForwarderData(array(
								'is_forwarder' => 1,
								'forwarder_email' => $email_info['email'],
							));

							$template_content = $template->getContent();
							$subject          = $template_content['render']['title'];
							$render           = $template_content['render']['full'];

							if (NewsletterPro::getConfiguration('DEBUG_MODE'))
								$send = $this->send($subject, $render, $email);
							else
								$send = @$this->send($subject, $render, $email);

							if ($send)
								$this->addSuccessFwd($email);
							else
								$errors_status[] = true;

							sleep($sleep);
						}
					}
				}

				return (empty($errors_status));
			}
		}
		catch(Exception $e)
		{
			$this->addError($e->getMessage());
		}

		return !$this->hasErrors();
	}

	/**
	 * Get email info
	 * @param  array/string $email Name and Email address
	 * @return array          
	 */
	public static function getEmailInfo($email)
	{
		if (is_array($email))
		{
			reset($email);
			$from_email = key($email);
			$from_name = $email[$from_email];

			if (!Validate::isMailName($from_name))
				$from_name = '';
		}
		else
		{
			$from_name = '';
			$from_email = $email;
		}

		return array(
			'name'  => $from_name,
			'email' => $from_email,
		);
	}

	/**
	 * Extract the full name and email from the user object
	 * @param  object $user The user object
	 * @return array/string User full name and email address
	 */
	public static function getToFromUser($user)
	{
		$to = $user->email;
		if (Tools::strlen($user->firstname) > 0)
		{
			$current_encode_fn = mb_detect_encoding($user->firstname, 'UTF-8, ISO-8859-1, ISO-8859-15, HTML-ENTITIES', true);
			$firstname = mb_convert_encoding($user->firstname, 'UTF-8', $current_encode_fn);

			$current_encode_ln = mb_detect_encoding($user->lastname, 'UTF-8, ISO-8859-1, ISO-8859-15, HTML-ENTITIES', true);
			$lastname = mb_convert_encoding($user->lastname, 'UTF-8', $current_encode_ln);

			$to = array($user->email => $firstname.' '.$lastname);
		}
		return $to;
	}

	public static function getEmailFromTo($to)
	{
		if (is_array($to)) {

			$kyes = array_keys($to);
			$to_email = $kyes[0];

			if (is_int($to_email)) {
				$values = array_values($to);
				$to_email = $values[0];
			}

		} else {
			$to_email = $to;
		}

		return $to_email;
	}

	public static function setHeaderListUnsubscribe(&$message, $email, $id_lang, $id_shop = null, $mail_to = null)
	{
		$context = Context::getContext();
		$module = NewsletterPro::getInstance();

		$unsubscribe_link = urldecode($context->link->getModuleLink($module->name, 'unsubscribe', array(
			'email' => $email,
			'u_token' => Tools::encrypt($email),
			'msg' => false,
		), null, $id_lang, $id_shop));

		$headers =& $message->getHeaders();
			
		$u_header = '';

		if (isset($mail_to) && Tools::strlen(trim($mail_to)) > 0) {
			$u_header = '<mailto:'.$mail_to.'>, <'.$unsubscribe_link.'>';
		} else {
			$u_header = '<'.$unsubscribe_link.'>';
		}

		$li_un = $headers->get('List-Unsubscribe');

		if (!$li_un) {
			$headers->addTextHeader('List-Unsubscribe', $u_header);
		} else {
			$li_un->setValue($u_header);
		}
	}
}