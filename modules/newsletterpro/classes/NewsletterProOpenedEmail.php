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

class NewsletterProOpenedEmail
{
	public $id_newsletter;

	public $email;

	public $table;

	public $id_table;

	public static function newsletterExists($id_newsletter)
	{
		return Db::getInstance()->getValue(
			'SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history`
			WHERE `id_newsletter_pro_tpl_history` = '.(int)$id_newsletter
		);
	}

	public static function emailExists($email)
	{
		if (!Validate::isEmail($email))
			return false;

		$query = array();
		$query['customer']             = 'SELECT `id_customer` FROM `'._DB_PREFIX_.'customer` WHERE `email` = "'.pSQL($email).'"';
		$query['newsletter']           = 'SELECT `id` FROM `'._DB_PREFIX_.'newsletter` WHERE `email` = "'.pSQL($email).'"';
		$query['newsletter_pro_email'] = 'SELECT `id_newsletter_pro_email` FROM `'._DB_PREFIX_.'newsletter_pro_email` WHERE `email` = "'.pSQL($email).'"';

		foreach ($query as $table => $sql)
		{
			if ($id_table = Db::getInstance()->getValue($sql))
				return array(
						'table'    => $table,
						'id_table' => $id_table,
					);

		}
		return false;
	}

	public function isValid($id_newsletter, $email)
	{
		$email_result = self::emailExists($email);
		if (self::newsletterExists($id_newsletter) && $email_result)
		{
			$this->id_newsletter = (int)$id_newsletter;
			$this->email         = (int)$email;
			$this->table         = $email_result['table'];
			$this->id_table      = $email_result['id_table'];
			return true;
		}
		return false;
	}

	public function wasOpened()
	{
		$cookie = new NewsletterProCookie('opened_email', time() + 259200);

		if (!$cookie->get('opened_email'))
			$cookie->set('opened_email', array());

		$opened_email = $cookie->get('opened_email');

		if (!in_array($this->id_newsletter, $opened_email))
		{
			$cookie->append('opened_email', (int)$this->id_newsletter);
			return false;
		}
		return true;
	}

	public function update()
	{
		$sql = 'UPDATE `'._DB_PREFIX_.'newsletter_pro_tpl_history`
				SET `opened` = opened + 1 
				WHERE `id_newsletter_pro_tpl_history` = '.(int)$this->id_newsletter.';';

		return Db::getInstance()->execute($sql);
	}
}