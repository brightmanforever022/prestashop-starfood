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

class NewsletterProPrepareNewsletter
{
	public $emails;

	private $template;

	private $set_template_called;

	private $id_newsletter_pro_send;

	public function __construct()
	{
		$this->set_template_called = false;
	}

	public static function newInstance()
	{
		return new self();
	}

	public function setTemplate($template)
	{
		$this->set_template_called = true;
		$this->template = $template;
		return $this;
	}

	public function setEmails($emails)
	{
		$this->emails = $this->sanitizeEmails($emails);
		return $this;
	}

	public function add()
	{
		if (!isset($this->set_template_called) || (isset($this->set_template_called) && !$this->set_template_called))
			throw new Exception('The function "setTemplate" must be called before the "add" function.');

		if (!isset($this->emails))
			throw new Exception('The function "setEmails" must be called before the "add" function.');

		$history = NewsletterProTplHistory::newFromTemplate($this->template);
		$history->add();

		$this->id_newsletter_pro_send = 0;

		if (Db::getInstance()->insert('newsletter_pro_send', array(
			'id_newsletter_pro_tpl_history' => (int)$history->id,
			'active'                        => 1,
			'template'                      => pSQL(NewsletterPro::getInstance()->getConfiguration('NEWSLETTER_TEMPLATE')),
			'date'                          => date('Y-m-d H:i:s'),
			'emails_count'                  => (int)count($this->emails),
		)))
			$this->id_newsletter_pro_send = Db::getInstance()->Insert_ID();
		else
			throw new Exception(sprintf($this->l('Fail to insert the records into "%s" table!'), 'newsletter_pro_send'));

		$emails_break_step = $this->breakEmailsStep();

		$connections = array();

		if ($this->hasConnections())
			$connections = NewsletterProSendConnection::getConnections();

		$index = 0;
		$step = 1;
		foreach ($emails_break_step as $emails)
		{
			$id_newsletter_pro_send_connection = 0;

			if (!empty($connections))
				$id_newsletter_pro_send_connection = $connections[$index % count($connections)]['id_newsletter_pro_send_connection'];

			if (!Db::getInstance()->insert('newsletter_pro_send_step', array(
				'emails_to_send'                    => NewsletterProTools::dbSerialize($emails),
				'id_newsletter_pro_send'            => (int)$this->id_newsletter_pro_send,
				'id_newsletter_pro_send_connection' => $id_newsletter_pro_send_connection,
				'step'                              => (int)$step++,
				'step_active'                       => 1,
				'date'								=> date('Y-m-d H:i:s'),
				'emails_sent'                       => NewsletterProTools::dbSerialize(array()),
			)))
				throw new Exception(sprintf(NewsletterPro::getInstance()->l('Fail to insert the records into "%s" table!'), 'newsletter_pro_send_step'));

			$index++;
		}

		return true;
	}

	private function sanitizeEmails($emails)
	{
		$valid_emails = array();

		foreach ($emails as $email) 
		{
			$email = trim($email);

			if (!in_array($email, $valid_emails) && Validate::isEmail($email))
				$valid_emails[] = $email;
		}

		return $valid_emails;
	}

	private function breakEmailsStep($break_limit = null)
	{
		if (!isset($break_limit))
			$break_limit = NewsletterPro::getInstance()->step;

		// If there are connections and the send booster plugin is activated the break limit will take another value
		if ($this->hasConnections())
		{
			$divide = ceil(count($this->emails) / NewsletterProSendConnection::countConnections());

			if ($divide < $break_limit)
				$break_limit = $divide;
		}

		return array_chunk($this->emails, $break_limit, true);
	}

	private function hasConnections()
	{
		return NewsletterProSendConnection::countConnections();
	}
}