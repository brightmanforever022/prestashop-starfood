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

class NewsletterProForwardRecipients
{
	public $recipients = array();

	public $recipients_name = array();

	public static function newInstance()
	{
		return new self();
	}

	public function add($from, $to)
	{
		if (is_array($to))
		{
			foreach ($to as $email)
				$this->addRecipient($from, $email);
		}
		else
			$this->addRecipient($from, $to);
	}

	private function addRecipient($from, $to)
	{
		if (is_array($from))
		{
			reset($from);

			$name = key($from);
			$email = $from[$name];

			$this->recipients_name[$email]  = $name;
			$this->recipients[$email][] = $to;
		}
		else
			$this->recipients[$from][] = $to;
	}

	public function buildForwardersRecursive($parent_email, $level = 100)
	{
		// this is for security
		if ($level > 0)
		{
			if ($child_emails = $this->getRecipient($parent_email))
			{
				foreach ($child_emails as $email)
				{
					if ($forwards = NewsletterProForward::getForwarders($email))
					{
						$this->add($email, $forwards);
						return $this->buildForwardersRecursive($email, --$level);
					}
				}
			}
		}
		return false;
	}

	public function getRecipients()
	{
		return $this->recipients;
	}

	public function getRecipientsName()
	{
		return $this->recipients_name;
	}

	public function getRecipient($email)
	{
		if (isset($this->recipients[$email]))
			return $this->recipients[$email];
		return false;
	}

	public function getRecipientName($email)
	{
		if (isset($this->recipients_name[$email]))
			return $this->recipients_name[$email];
		return '';
	}
}
?>