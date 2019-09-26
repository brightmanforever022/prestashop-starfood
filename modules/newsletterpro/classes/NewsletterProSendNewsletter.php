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

class NewsletterProSendNewsletter
{
	private $send;

	private $step;

	private $process;

	private $count_connections;

	private $sleep = 0;

	private $mail;

	private $mailer;

	private $template;

	private $template_decorator;

	private $response;

	private $log;

	private $attachment_exists;

	const USE_TEMPLATE_DECORATOR = true;

	/**
	 * value 1 (default) - close the browser connection after the first newsletter sent
	 * value 0 (for tests) - don't close the browser connection
	 */
	const CLOSE_BROWSER_CONNECTION = 0;

	const APACHE_RESTART_TIME = 300;

	const MAX_EXECUTION_TIME = 0;

	public function __construct()
	{
		@ignore_user_abort(true);
		@set_time_limit(0);
		@ini_set('max_execution_time', self::MAX_EXECUTION_TIME);

		$this->sleep = (int)NewsletterPro::getConfiguration('SLEEP');
		$this->count_connections = (int)NewsletterProSendConnection::countConnections();
		$this->response = NewsletterProAjaxResponse::newInstance();
		$this->attachment_exists = false;
	
		$write_send_log = (int)NewsletterPro::getInstance()->ini_config['write_send_log'];
		$this->log = NewsletterProLog::newInstance()->visible($write_send_log);

		// solve sleep method for multiple connections
		if ($this->isSleepDefaultMethodActive() && $this->count_connections > 0)
			$this->sleep = $this->sleep * $this->count_connections;
	}

	public static function newInstance()
	{
		return new self();
	}

	public function send($continue = false)
	{
		$this->response->setArray(array(
			'active'             => 0,
			'state'              => NewsletterProSend::STATE_DONE,
			'require_connection' => 0,
			'message'            => '',
		));

		try
		{
			NewsletterProShutdown::register(array($this, 'registerShutdownLog'));

			$this->send = $this->getActiveSend();

			if ($continue)
				$this->send->stateDefault();

			// end the script if is the case
			if ($this->send->isPause())
				throw new NewsletterProSendNewsletterException(NewsletterPro::getInstance()->l('The process is paused.'), NewsletterProSendNewsletterException::CODE_SEND_IS_PAUSED);

			if ((int)$this->send->emails_completed > (int)$this->send->emails_count)
				throw new NewsletterProSendNewsletterException(NewsletterPro::getInstance()->l('The sending process is completed.'), NewsletterProSendNewsletterException::CODE_SEND_COMPLETE);

			$id_step = null;

			if ($this->count_connections)
			{
				$id_step = $this->send->getFirstFreeStepIdAndOpenConnection();
				
				if (!$id_step)
					throw new NewsletterProSendNewsletterException(NewsletterPro::getInstance()->l('There are no connections available.'), NewsletterProSendNewsletterException::CODE_NO_CONNECTIONS_AVAILABLE);
			}
			else
			{
				$id_step = $this->send->getCurrentStepId();

				if (!$id_step)
					throw new NewsletterProSendNewsletterException(NewsletterPro::getInstance()->l('There are no steps available.'), NewsletterProSendNewsletterException::CODE_NO_STEPS_AVAILABLE);
			}

			$this->step = NewsletterProSendStep::newInstance($id_step);

			if (!$this->step->hasConnection() && $this->send->isInProgress())
				throw new NewsletterProSendNewsletterException(NewsletterPro::getInstance()->l('The send is in progress.'), NewsletterProSendNewsletterException::CODE_SEND_IN_PROGRESS);

			if (!$this->step->hasConnection() && $this->send->isPause())
				throw new NewsletterProSendNewsletterException(NewsletterPro::getInstance()->l('The process is paused.'), NewsletterProSendNewsletterException::CODE_SEND_IS_PAUSED);

			$send_success_count = $this->start();

			$this->response->setArray(array(
				'active'             => $this->send->isActive(),
				'state'              => $this->send->getState(),
				'require_connection' => 1,
				'message'            => sprintf(NewsletterPro::getInstance()->l('%s newsletters was sent.'), $send_success_count)
			));
		}
		catch (Exception $e)
		{
			if ($e instanceof NewsletterProSendNewsletterException)
			{
				switch ($e->getCode())
				{
					case NewsletterProSendNewsletterException::CODE_NO_ACTIVE_SEND:
						$this->response->setArray(array(
							'active'             => 0,
							'state'              => NewsletterProSend::STATE_DONE,
							'require_connection' => 0,
							'message'            => $e->getMessage()
						));
						break;
					case NewsletterProSendNewsletterException::CODE_SEND_COMPLETE:
						$this->send->state = NewsletterProSend::STATE_DONE;
						$this->send->active = 0;
						$this->send->update();
						break;
					case NewsletterProSendNewsletterException::CODE_SEND_IS_PAUSED:
						$this->response->setArray(array(
							'active'             => $this->send->isActive(),
							'state'              => $this->send->getState(),
							'require_connection' => 0,
							'message'            => $e->getMessage()
						));
						break;

					case NewsletterProSendNewsletterException::CODE_NO_CONNECTIONS_AVAILABLE:
					case NewsletterProSendNewsletterException::CODE_NO_STEPS_AVAILABLE:

						if (count($this->send->getStepsIds(true)) == 0)
						{
							$this->send->state = NewsletterProSend::STATE_DONE;
							$this->send->active = 0;
							$this->send->update();
						}

						$this->response->setArray(array(
							'active'             => $this->send->isActive(),
							'state'              => $this->send->getState(),
							'require_connection' => 0,
							'message'            => $e->getMessage()
						));

						break;

					// this is for the normal send, without connections
					case NewsletterProSendNewsletterException::CODE_SEND_IN_PROGRESS:
						$this->response->setArray(array(
							'active'             => $this->send->isActive(),
							'state'              => $this->send->getState(),
							'require_connection' => 0,
							'message'            => $e->getMessage()
						));
						break;

					default:
						$this->response->addError($e->getMessage());
						break;
				}
			}
			else
				$this->response->addError($e->getMessage());
		}

		return $this->response->display();
	}

	private function start()
	{
		$this->send->shutdown()->stateInProgress();

		if ($this->step->hasConnection())
		{
			$this->step->shutdown();
			$this->setMail(NewsletterProMail::newInstance((int)$this->step->connection->id_newsletter_pro_smtp));
		}
		else
		{
			$this->step->shutdown();
			$this->setMail(NewsletterProMail::getInstanceByContext());
		}

		// set the connection mail, mailer and message
		$this->setMailer($this->mail->newSwiftMailer());
		$this->setMessage($this->mail->newSwiftMessage());
		$this->setTemplateByIdHistory((int)$this->send->id_newsletter_pro_tpl_history);
		$this->registerTemplateDecorator();
		$this->registerMethod();

		// sending process
		// pass the send object to the process object
		$this->process = NewsletterProSendProcess::newInstance($this->step);

		$log_content = '';

		foreach ($this->process->getEmailsToSend() as $key => $email) 
		{
			$this->process->set($key, $email);

			try
			{
				$this->checkStatusExit();

				$log_content .= $email;

				if (NewsletterProEmailExclusion::newInstance()->emailExists($email))
				{
					$this->sendForward($email);
					// skip this email and go to the next one
					throw new Exception(NewsletterPro::getInstance()->l('The email is excluded from the process of sending. The email exists into exclusion list.'));
				}

				$this->setBody($email);
				$this->template->setForwarder(false);

				if ($this->mailer->send($this->message))
				{
					$log_content .= ' [status 1]';

					$this->sendForward($email);
					$this->process->succeed();
				}
				else
				{
					$error_message = '';

					if ($this->mail->method == NewsletterProMail::METHOD_MAIL)
						$error_message = sprintf(NewsletterPro::getInstance()->l('Failed to send the email. The problem can be related with the %s function.'), 'php mail()');
					else
					{
						if (!function_exists('proc_open') && $this->mail->method == NewsletterProMail::METHOD_SMTP)
							$error_message = sprintf(NewsletterPro::getInstance()->l('Failed to send the email. The problem is related with the %s function and is happen only if you use the SMTP method. You need to contact your hosting provider to solve the problem with that function.'), 'php proc_open');
						else
							$error_message = NewsletterPro::getInstance()->l('Failed to send the email.');
					}

					$this->step->appendError($email, $error_message, NewsletterProSendStep::ERROR_SMTP, $this->process->dbLimitExeed());

					$log_content .= ' [status 0] [Error : '.$error_message.']';

					$this->process->faild();
				}
			}
			catch(Exception $e)
			{
				$this->response->addError($e->getMessage());
				$this->step->appendError($email, $e->getMessage(), NewsletterProSendStep::ERROR_EXCEPTION, $this->process->dbLimitExeed());

				$log_content .= ' [status 0] [Error : '.$e->getMessage().']';

				$this->process->faild();
			}

			$this->log->add($log_content);
			$log_content = '';

			// this is for the update of the fields emails_success, emails_errors and emails_completed 
			if ($this->process->isSent())
				$this->send->progressSuccess();
			else
				$this->send->progressErrors();

			// update db if the limit exeed
			if ($this->process->dbLimitExeed())
			{
				$this->send->updateAll(array('active', 'state'));
				$this->step->update();
			}

			// update if the send exit the emails limit
			if ($this->process->limitExeed())
			{
				$this->send->state = NewsletterProSend::STATE_DEFAULT;
				$this->send->updateAll(array('active', 'state'));
				$this->step->update();

				// count succeed and the fwd succeed
				return $this->process->countAllSucceed($email);
			}

			if (self::CLOSE_BROWSER_CONNECTION > 0 && self::CLOSE_BROWSER_CONNECTION >= $this->process->count())
			{
				$this->response->setArray(array(
					'active' => $this->send->isActive(),
					'state' => $this->send->getState(),
					'require_connection' => 1
				));

				NewsletterProTools::closeConnection($this->response->display());
			}

			if ($this->isSleepDefaultMethodActive() && $this->sleep > 0)
				sleep($this->sleep);
		} // end foreach

		if (isset($email))
			return $this->process->countAllSucceed($email);

		return $this->process->countSucceed();
	}

	private function sendForward($email)
	{
		// send to the forwarders
		if ((bool)NewsletterPro::getConfiguration('FWD_FEATURE_ACTIVE') && ($forwards = NewsletterProForward::getForwarders($email)))
		{
			$email_info     = NewsletterProMail::getEmailInfo($email);
			$fwd_recipients = NewsletterProForwardRecipients::newInstance();

			$fwd_recipients->add(array($email_info['name'] => $email_info['email']), $forwards);
			$fwd_recipients->buildForwardersRecursive($email_info['email']);

			$recipients = $fwd_recipients->getRecipients();

			foreach ($recipients as $parent_email => $child_emails)
			{
				$this->from_name  = $fwd_recipients->getRecipientName($parent_email);
				$this->from_email = $parent_email;

				foreach ($child_emails as $fwd_email)
				{
					try
					{
						if (NewsletterProEmailExclusion::newInstance()->emailExists($fwd_email))
							throw new Exception(NewsletterPro::getInstance()->l('The email is excluded from the process of sending. The email exists into exclusion list.'));

						$this->setBody($fwd_email);
						$this->template->setForwarder(true);
						$this->template->setForwarderData(array(
							'forwarder_email' => $email_info['email'],
						));

						if ($this->mailer->send($this->message))
							$this->process->succeedFwd($parent_email, $fwd_email);
						else
							$this->process->faildFwd($parent_email, $fwd_email);
					}
					catch(Exception $e)
					{
						$this->process->faildFwd($parent_email, $fwd_email);
					}

					if ($this->isSleepDefaultMethodActive() && $this->sleep > 0)
						sleep($this->sleep);

				} // end of foreach
			}
		}
	}

	private function mapIdNewsletterProSendConnection($row)
	{
		return !empty($row) ? $row['id_newsletter_pro_send_connection'] : false;
	}

	private function getResetConnectionsId($id_send)
	{
		$ids = array_map(array($this, 'mapIdNewsletterProSendConnection'), Db::getInstance()->executeS('
			SELECT sc.`id_newsletter_pro_send_connection`
			FROM `'._DB_PREFIX_.'newsletter_pro_send` s

			INNER JOIN `'._DB_PREFIX_.'newsletter_pro_send_step` ss
				ON (
					s.`id_newsletter_pro_send` = ss.`id_newsletter_pro_send` AND
					ss.`id_newsletter_pro_send_connection` > 0
				)

			INNER JOIN `'._DB_PREFIX_.'newsletter_pro_send_connection` sc
				ON (
					ss.`id_newsletter_pro_send_connection` = sc.`id_newsletter_pro_send_connection` AND
					sc.`state` = 1
				)

			WHERE s.`id_newsletter_pro_send` = '.(int)$id_send.'

			AND s.`active` = 1
			AND s.`state` != '.(int)NewsletterProSend::STATE_PAUSE.'
			AND (
				DATE_ADD(ss.`date_modified`, INTERVAL '.(int)self::APACHE_RESTART_TIME.' SECOND) <= "'.date('Y-m-d H:i:s').'" OR
				ss.`date_modified` = NULL		
			)
		'));

		return array_unique($ids);
	}

	public function sync($id = null, $limit = null, $get_last_id = false)
	{
		$response = NewsletterProSyncNewsletterResponse::newInstance();

		try
		{
			if (!isset($limit))
				$limit = NewsletterPro::getInstance()->step;

			if (isset($id) && (int)$id > 0)
				$send = NewsletterProSend::newInstance($id);
			else
				$send = $this->getActiveSend();
			// start sending if the apache has been restarted
			if ($send && Validate::isLoadedObject($send))
			{
				$ids = $this->getResetConnectionsId($send->id);

				if (empty($ids))
				{
					$count = (int)Db::getInstance()->getValue('
						SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_send_step` ss
						LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_send` s
							ON (s.`id_newsletter_pro_send` = ss.`id_newsletter_pro_send`)
						WHERE ss.`id_newsletter_pro_send` = '.(int)$send->id.'
						AND s.`active` = '.(int)NewsletterProSend::STATE_PAUSE.'
						AND (
							DATE_ADD(ss.`date_modified`, INTERVAL '.(int)self::APACHE_RESTART_TIME.' SECOND) <= "'.date('Y-m-d H:i:s').'" OR
							ss.`date_modified` = NULL
						)
					');

					if ($count > 0)
						$send->stateDefault();
				}
				else
				{
					foreach ($ids as $id_connection) 
					{
						$connection = NewsletterProSendConnection::newInstance($id_connection);
						if (Validate::isLoadedObject($connection))
						{
							$connection->close();
							$send->stateDefault();
						}
					}
				}
			}

			$response->setObject($send, $limit, $get_last_id);
		}
		catch (Exception $e)
		{
			if ($e instanceof NewsletterProSendNewsletterException)
			{
				switch ($e->getCode())
				{
					case NewsletterProSendNewsletterException::CODE_NO_ACTIVE_SEND:
						return $response->display();
					break;

					default:
						$response->addError($e->getMessage());
					break;
				}
			}
			else
				$response->addError($e->getMessage());
		}

		return $response->display();
	}

	public function stop()
	{
		try
		{
			$this->send = $this->getActiveSend();
			
			if (!$this->send->stateDone())
				$this->response->addError(NewsletterPro::getInstance()->l('An error occurred, the seinding proccess cannot be stopped.'));
		}
		catch (Exception $e)
		{
			if (!($e instanceof NewsletterProSendNewsletterException))
				$this->response->addError($e->getMessage());
		}

		NewsletterProSendConnection::clearAll();

		return $this->response->display();
	}

	public function pause()
	{
		try
		{
			$this->send = $this->getActiveSend();

			if (!$this->send->statePause())
				$this->response->addError(NewsletterPro::getInstance()->l('An error occurred, the seinding proccess cannot be paused.'));
		}
		catch (Exception $e)
		{
			if (!($e instanceof NewsletterProSendNewsletterException))
				$this->response->addError($e->getMessage());
		}

		return $this->response->display();
	}

	private function setBody($email)
	{
		if (!isset($this->template_decorator))
		{
			$render = $this->template->message($email);
			$to     = $this->template->user->to();

			$this->message->setTo($to);
			$this->message->setSubject($render['title']);

			$body_final = $render['body'];
			$body_final = NewsletterPro::getInstance()->embedImages($this->message, $body_final);

			$this->message->setBody($body_final, 'text/html');
		}
		else
			$this->message->setTo($email);

		if ((int)$this->mail->list_unsubscribe_active) {
			NewsletterProMail::setHeaderListUnsubscribe($this->message, $email, null, null, $this->mail->list_unsubscribe_email);
		}

		if (!($this->attachment_exists)) {
			$this->attachment_exists = true;
			NewsletterProAttachment::setAttachmentToMessage($this->template->name, $this->message);
		}
	}

	private function checkStatusExit()
	{
		$info = $this->send->getInfo();
		
		if (!$info)
		{
			NewsletterProShutdown::unregister(array($this->send, 'registerShutdown'));

			$this->send->state = NewsletterProSend::STATE_DONE;
			$this->send->active = 0;
			$this->send->updateAll();
			exit;
		}
		
		$active = (int)$info['active'];
		$state  = (int)$info['state'];

		if (!$active || $this->send->isDone($state))
		{
			NewsletterProShutdown::unregister(array($this->send, 'registerShutdown'));
			$this->send->state = NewsletterProSend::STATE_DONE;
			$this->send->active = 0;
			$this->send->updateAll();
			exit;
		}

		if ($this->send->isPause($state))
		{
			NewsletterProShutdown::unregister(array($this->send, 'registerShutdown'));

			$this->send->state = NewsletterProSend::STATE_PAUSE;
			$this->send->updateAll();
			exit;
		}
	}

	public function setMail($mail)
	{
		$this->mail = $mail;

		if ($this->mail->hasErrors())
			throw new Exception(implode('<br>', $this->mail->getErrors()));
			
		return $this;
	}

	public function setMailer($mailer)
	{
		$this->mailer = $this->mail->newSwiftMailer();
		return $this;
	}

	public function setMessage($message)
	{
		$this->message = $message;
		return $this;
	}

	public function setTemplateByIdHistory($id_history)
	{
		$this->template = NewsletterProTemplate::newHistory((int)$id_history)->load();

		if (self::USE_TEMPLATE_DECORATOR)
			$this->template_decorator = NewsletterPro_Swift_Plugins_TemplateDecoratorPlugin::newInstance(NewsletterProTemplateDecoratorReplacements::newInstance($this->template));

		return $this;
	}

	/**
	 * Get an instance of the send object
	 * 
	 * @return object/exception
	 */
	private function getActiveSend()
	{
		$send = NewsletterProSend::newInstance(NewsletterProSend::getActiveId());

		if (Validate::isLoadedObject($send))
			return $send;

		throw new NewsletterProSendNewsletterException(NewsletterPro::getInstance()->l('There is nothing to send.'), NewsletterProSendNewsletterException::CODE_NO_ACTIVE_SEND);
	}

	private static function isSleepDefaultMethodActive()
	{
		return ((int)NewsletterPro::getConfiguration('SEND_METHOD') == (int)NewsletterPro::SEND_METHOD_DEFAULT);
	}

	private static function isAntifloodMethodActive()
	{
		return ((int)NewsletterPro::getConfiguration('SEND_METHOD') == (int)NewsletterPro::SEND_METHOD_ANTIFLOOD);
	}

	private function registerAntiflood($emails, $sleep)
	{
		if ($sleep > 0)
			$this->mailer->registerPlugin(new NewsletterPro_Swift_Plugins_AntiFloodPlugin($emails, $sleep));
		else
			$this->mailer->registerPlugin(new NewsletterPro_Swift_Plugins_AntiFloodPlugin($emails));
	}

	private function registerThrottler($type, $limit)
	{
		if ($type == (int)NewsletterPro::SEND_THROTTLER_TYPE_EMAILS)
		{
			$this->mailer->registerPlugin(new NewsletterPro_Swift_Plugins_ThrottlerPlugin(
				$limit, NewsletterPro_Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE
			));
		}
		else
		{
			$this->mailer->registerPlugin(new NewsletterPro_Swift_Plugins_ThrottlerPlugin(
				1024 * 1024 * $limit, NewsletterPro_Swift_Plugins_ThrottlerPlugin::BYTES_PER_MINUTE
			));
		}
	}

	private function registerTemplateDecorator()
	{
		if (isset($this->template_decorator) && $this->template_decorator instanceof NewsletterPro_Swift_Plugins_TemplateDecoratorPlugin)
			$this->mailer->registerPlugin($this->template_decorator);
		return $this;
	}

	private function registerMethod()
	{
		if ($this->isAntifloodMethodActive())
		{
			$methods = array();

			$antiflood_emails = (int)NewsletterPro::getConfiguration('SEND_ANTIFLOOD_EMAILS');
			$antiflood_sleep  = (int)NewsletterPro::getConfiguration('SEND_ANTIFLOOD_SLEEP');
			$throttle_type    = (int)NewsletterPro::getConfiguration('SEND_THROTTLER_TYPE');
			$throttle_limit   = (int)NewsletterPro::getConfiguration('SEND_THROTTLER_LIMIT');

			if ((int)NewsletterPro::getConfiguration('SEND_ANTIFLOOD_ACTIVE'))
			{
				$methods[] = 1;
				$this->registerAntiflood($antiflood_emails, $antiflood_sleep);
			}

			if ((int)NewsletterPro::getConfiguration('SEND_THROTTLER_ACTIVE'))
			{
				$methods[] = 1;
				$this->registerThrottler($throttle_type, $throttle_limit);
			}

			if (empty($methods))
			{
				// the default value is antiflood if not method is activated
				$this->registerAntiflood($antiflood_emails, $antiflood_sleep);
			}
		}
		return $this;
	}

	public function registerShutdownLog()
	{
		$this->log->writeSend();
	}
}