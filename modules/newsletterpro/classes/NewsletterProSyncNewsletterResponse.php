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

class NewsletterProSyncNewsletterResponse extends NewsletterProAjaxResponse
{
	public function __construct($default_variables = array())
	{
		$default_variables = array_merge($default_variables, array(
			'id'			 => 0,
			'active'         => false,
			'state'          => NewsletterProSend::STATE_DONE,
			'remaining'      => 0,
			'emails_error'   => 0,
			'emails_success' => 0,
			'emails_count'   => 0,
			'emails_to_send' => array(),
			'emails_sent'    => array(),
		));

		parent::__construct($default_variables);
	}

	public static function newInstance($default_variables = array())
	{
		return new self($default_variables);
	}

	public function setObject($send, $limit = null, $get_last_id = false)
	{
		if ($send)
		{
			$this->set('id', (int)$send->id);
			$this->set('active', (bool)$send->active);
			$this->set('state', (int)$send->state);
			$this->set('remaining', (int)$send->getRemaining());
			$this->set('emails_error', (int)$send->emails_error);
			$this->set('emails_success', (int)$send->emails_success);
			$this->set('emails_count', (int)$send->emails_count);
			$this->set('emails_to_send', $send->getEmailsToSend($limit));

			$emails_sent = $send->getEmailsSent($limit, true, true, true);

			$this->set('emails_sent', $emails_sent);
		}
		else if ($get_last_id)
			$this->set('id', (int)NewsletterProSend::getLastId());

		return $this;
	}
}