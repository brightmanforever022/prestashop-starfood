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

class NewsletterProEmailExclusion
{
	public static function newInstance()
	{
		return new self();
	}

	public function add($emails)
	{
		$added = 0;
		$duplicate = 0;

		if (is_array($emails))
		{
			foreach ($emails as $email)
				if ($this->emailExists($email))
					$duplicate++;
				else
					$added += $this->addStr($email);
		}
		else
		{
			if ($this->emailExists($emails))
				$duplicate++;
			else
				$added += $this->addStr($emails);
		}

		return array($added, $duplicate);
	}

	private function addStr($email)
	{
		try
		{
			return (int)Db::getInstance()->insert('newsletter_pro_email_exclusion', array(
				'email' => $email
			));
		}
		catch(Exception $e)
		{
			return 0;
		}
	}

	public function emailExists($email)
	{
		return Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'newsletter_pro_email_exclusion`
			WHERE `email` = "'.pSQL($email).'"
		');
	}

	public function emptyList()
	{
		return Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'newsletter_pro_email_exclusion` WHERE 1
		');
	}

	/**
	 * Get emails from table
	 * @param  array $tables [0] - table name [1] - left join table name
	 * @param  array $ids
	 * @param  bool $bool_remaining_emails
	 * @param  bool $bool_sent_emails
	 * @return array
	 */
	private function getEmailsFromTable($tables, $ids, $bool_remaining_emails, $bool_sent_emails)
	{
		if (empty($ids) || (!$bool_remaining_emails && !$bool_sent_emails))
			return array();

		$table = $tables[0];
		$join = $tables[1];

		$sql = 'SELECT ts.`emails_to_send`, ts.`emails_sent` FROM `'._DB_PREFIX_.pSQL($table).'` t
			LEFT JOIN `'._DB_PREFIX_.pSQL($join).'` ts ON (ts.`id_'.pSQL($table).'` = t.`id_'.pSQL($table).'`)
			WHERE t.`id_'.pSQL($table).'` IN (';

		foreach ($ids as $id)
			$sql .= (int)$id.',';
		$sql = rtrim($sql, ',').')';

		$result = Db::getInstance()->executeS($sql);

		$emails_to_send = array();
		$emails_sent = array();

		foreach ($result as $value)
		{
			if ($bool_remaining_emails)
			{
				$em = NewsletterProTools::unSerialize($value['emails_to_send']);
				$emails_to_send = array_merge($emails_to_send, $em);
			}

			if ($bool_sent_emails)
				$em = NewsletterProTools::unSerialize($value['emails_sent']);
				$emails_m = array();
				foreach ($em as $val)
					$emails_m[] = $val['email'];

				$emails_sent = array_merge($emails_sent, $emails_m);
		}

		return array_unique(array_merge($emails_to_send, $emails_sent));
	}

	/**
	 * Get emails from task
	 * @param  array $ids
	 * @param  bool $bool_remaining_emails
	 * @param  bool $bool_sent_emails
	 * @return array
	 */
	public function getEmailsFromTask($ids, $bool_remaining_emails, $bool_sent_emails)
	{
		return $this->getEmailsFromTable(array('newsletter_pro_task', 'newsletter_pro_task_step'), $ids, $bool_remaining_emails, $bool_sent_emails);
	}

	/**
	 * Get emails from sent
	 * @param  array $ids
	 * @param  bool $bool_remaining_emails
	 * @param  bool $bool_sent_emails
	 * @return array
	 */
	public function getEmailsFromSend($ids, $bool_remaining_emails, $bool_sent_emails)
	{
		return $this->getEmailsFromTable(array('newsletter_pro_send', 'newsletter_pro_send_step'), $ids, $bool_remaining_emails, $bool_sent_emails);
	}

	public function countList()
	{
		return Db::getInstance()->getValue('
			SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_email_exclusion`
		');
	}
}