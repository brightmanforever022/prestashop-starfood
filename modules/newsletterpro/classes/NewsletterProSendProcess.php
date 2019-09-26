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

class NewsletterProSendProcess
{
	private $current_key;

	private $current_email;
	
	private $current_status;

	private $set_called;
	
	private $emails_faild = array();

	private $emails_succeed = array();

	private $emails_to_send;

	private $emails_sent;

	private $failed_recipients_fwd = array();

	private $success_recipients_fwd = array();

	private $count_faild = 0;
	
	private $count_succeed = 0;

	private $step;

	const STATUS_FAILD = 0;

	const STATUS_SUCCEED = 1;

	public function __construct(&$step)
	{
		$this->step = $step;

		$this->emails_to_send = $this->step->getEmailsToSend();
		$this->emails_sent = $this->step->getEmailsSent();
	}

	public static function newInstance(&$step)
	{
		return new self($step);
	}

	public function set($key, $email)
	{
		$this->set_called = true;
		$this->current_key  = $key;
		$this->current_email = $email;

		return $this;
	}

	public function succeed()
	{
		if (!isset($this->set_called))
			throw new Exception('You must set the process, before to call the function succeed().');

		$this->current_status = self::STATUS_SUCCEED;

		// procced one time
		if (!in_array($this->current_email, $this->emails_succeed))
		{
			$this->emails_sent[] = array(
				'email'  => $this->current_email,
				'status' => true,
				'fwd'    => $this->succeedFwdCount($this->current_email),
			);

			$this->emails_succeed[] = $this->current_email;
			$this->count_succeed++;
		}

		$this->unsetEmailsToSend($this->current_email);
		$this->updateStep();
	}

	public function faild()
	{
		if (!isset($this->set_called))
			throw new Exception('You must set the process, before to call the function faild().');

		$this->current_status = self::STATUS_FAILD;
		
		// proceed one time
		if (!in_array($this->current_email, $this->emails_faild))
		{
			$this->emails_sent[] = array(
				'email'  => $this->current_email,
				'status' => false,
				'fwd'    => $this->succeedFwdCount($this->current_email),
			);

			$this->emails_faild[] = $this->current_email;
			$this->count_faild++;
		}

		$this->unsetEmailsToSend($this->current_email);
		$this->updateStep();
	}

	private function updateStep()
	{
		$this->step->setEmailsToSend($this->emails_to_send);
		$this->step->setEmailsSent($this->emails_sent);
		$this->step->step_active = (count($this->emails_to_send) > 0 ? 1 : 0);
	}

	public function isSent()
	{
		return ($this->current_status == self::STATUS_SUCCEED);
	}

	public function count()
	{
		return ($this->count_faild + $this->count_succeed);
	}

	public function countSucceed()
	{
		return $this->count_succeed;
	}

	public function countFaild()
	{
		return $this->count_faild;
	}

	public function clearCount()
	{
		$this->count_faild = 0;
		$this->count_succeed = 0;
		return $this;
	}

	public function getEmailsFaild()
	{
		return $this->emails_faild;
	}

	public function getEmailsSucceed()
	{
		return $this->emails_succeed;
	}

	public function getEmailsToSend()
	{
		return $this->emails_to_send;
	}

	public function getEmailsSent()
	{
		return $this->emails_sent;
	}

	public function getEmailsSentByEmail($email)
	{
		if (isset($this->emails_sent[$email]))
			return $this->emails_sent[$email];
		return true;

	}

	public function unsetEmailsToSend($email)
	{
		$key = array_search($email, $this->emails_to_send);
		if ($key !== false)
		{
			unset($this->emails_to_send[$key]);
			return true;
		}
		return false;
	}

	public function succeedFwd($parent_email, $fwd_email)
	{
		$this->success_recipients_fwd[$parent_email][] = $fwd_email;
	}

	public function faildFwd($parent_email, $fwd_email)
	{
		$this->failed_recipients_fwd[$parent_email][] = $fwd_email;
	}

	public function succeedFwdCount($parent_email)
	{
		return (isset($this->success_recipients_fwd[$parent_email]) ? count($this->success_recipients_fwd[$parent_email]) : 0);
	}

	public function limitExeed()
	{
		$limit = (int)pqnp_config('SEND_LIMIT_END_SCRIPT');
		return ($limit > 0 && $this->count() >= $limit);
	}

	public function dbLimitExeed()
	{
		return ($this->count() % $this->getDbLimit() == 0);
	}

	private function getDbLimit()
	{
		$db_limit = (int)NewsletterPro::getInstance()->ini_config['send_write_db_limit'];

		if (((int)NewsletterPro::getConfiguration('SEND_METHOD') == NewsletterPro::SEND_METHOD_DEFAULT && (int)NewsletterPro::getConfiguration('SLEEP') == 0) || ((int)NewsletterPro::getConfiguration('SEND_METHOD') == NewsletterPro::SEND_METHOD_ANTIFLOOD))
		{
			$count_connections = (int)NewsletterProSendConnection::countConnections();

			if ($count_connections >= 2 && $db_limit < $count_connections)
				$db_limit = $count_connections * 3;
		}

		return $db_limit;
	}

	public function countAllSucceed($parent_email)
	{
		return $this->countSucceed() + $this->succeedFwdCount($parent_email);
	}
}