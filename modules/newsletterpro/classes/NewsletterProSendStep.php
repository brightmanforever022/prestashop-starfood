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

class NewsletterProSendStep extends ObjectModel
{
	public $id_newsletter_pro_send;

	public $id_newsletter_pro_send_connection;

	public $step;

	public $step_active;

	public $emails_to_send;

	public $emails_sent;

	public $error_msg;

	public $date;

	public $date_modified;

	/**
	 * Constants
	 */

	const ERROR_NO_USER = 100;

	const ERROR_TEMPLATE = 101;

	const ERROR_SMTP = 102;

	const ERROR_EXCEPTION = 103;

	/**
	 * Variables
	 */

	public $emails_to_send_unserialized;

	public $emails_sent_unserialized;

	public $connection;

	public static $definition = array(
		'table'   => 'newsletter_pro_send_step',
		'primary' => 'id_newsletter_pro_send_step',
		'fields'  => array(
			'id_newsletter_pro_send'            => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_newsletter_pro_send_connection' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'step'                              => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'step_active'                       => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'emails_to_send'                    => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'emails_sent'                       => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'error_msg'                         => array('type' => self::TYPE_HTML, 'validate' => 'isString'),
			'date'                              => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
			'date_modified'                     => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
		)
	);

	public static function newInstance($id = null)
	{
		return new self($id);
	}

	public function __construct($id = null)
	{
		// set defaults values
		$this->emails_to_send = serialize(array());
		$this->emails_sent = serialize(array());
		$this->date_modified = date('Y-m-d H:i:s');

		$this->error_msg = serialize(array());

		parent::__construct($id);

		if ((int)$this->id_newsletter_pro_send_connection)
		{
			$this->connection = NewsletterProSendConnection::newInstance($this->id_newsletter_pro_send_connection);
			if (!Validate::isLoadedObject($this->connection))
				$this->connection = null;
		}

		if (!isset($this->emails_to_send_unserialized))
			$this->emails_to_send_unserialized = NewsletterProTools::unSerialize($this->emails_to_send);

		if (!isset($this->emails_sent_unserialized))
			$this->emails_sent_unserialized = NewsletterProTools::unSerialize($this->emails_sent);
	}

	public function add($autodate = true, $null_values = false)
	{
		$this->emails_to_send = serialize($this->emails_to_send_unserialized);
		$this->emails_sent    = serialize($this->emails_sent_unserialized);

		$this->date_modified = date('Y-m-d H:i:s');
		$this->date = date('Y-m-d H:i:s');

		return parent::add($autodate, $null_values);
	}

	public function update($null_values = false)
	{
		$this->emails_to_send = serialize($this->emails_to_send_unserialized);
		$this->emails_sent    = serialize($this->emails_sent_unserialized);

		$this->date_modified = date('Y-m-d H:i:s');

		return parent::update($null_values);
	}

	public function setEmailsToSend($value)
	{
		$this->emails_to_send_unserialized = $value;
		$this->emails_to_send = serialize($value);
	}

	public function getEmailsToSend($limit = 0)
	{
		if ($limit)
			return array_slice($this->emails_to_send_unserialized, 0, 10);

		return $this->emails_to_send_unserialized;
	}

	public function getEmailsToSendDb()
	{
		$result = Db::getInstance()->getValue('
			SELECT `emails_to_send` 
			FROM `'._DB_PREFIX_.'newsletter_pro_send_step` 
			WHERE `id_newsletter_pro_send_step` = '.(int)$this->id.'
		');

		if (!$result)
			$result = serialize(array());

		return NewsletterProTools::unSerialize($result);
	}

	public function getEmailsSent($limit = 0, $reverse = false)
	{
		if ($reverse)
			$result = array_reverse($this->emails_sent_unserialized);
		else
			$result = $this->emails_sent_unserialized;

		if ($limit)
			return array_slice($result, 0, 10);

		return $result;
	}

	public function getEmailsSentDb()
	{
		$result = Db::getInstance()->getValue('
			SELECT `emails_sent` 
			FROM `'._DB_PREFIX_.'newsletter_pro_send_step` 
			WHERE `id_newsletter_pro_send_step` = '.(int)$this->id.'
		');

		if (!$result)
			$result = serialize(array());

		return NewsletterProTools::unSerialize($result);
	}

	public function setEmailsSent($value)
	{
		$this->emails_sent_unserialized = $value;
		$this->emails_sent = serialize($value);
	}

	public function getErrorMsg()
	{
		return NewsletterProTools::unSerialize($this->error_msg);
	}

	/**
	 * Add error to database
	 * @param  string $email
	 * @param  array/string $errors_array
	 * @param  integer $code
	 * @return boolean
	 */
	public function appendError($email, $errors_array, $code, $write_db_limit = true)
	{
		$error_msg_db = NewsletterProTools::unSerialize($this->error_msg);

		$errors_join = is_array($errors_array) ? join('<br>', $errors_array) : $errors_array;

		if (!isset($error_msg_db[$code]))
			$error_msg_db[$code] = array();

		$error_msg_db[$code][$errors_join][] = $email;
		$error_msg_db[$code][$errors_join] = array_unique($error_msg_db[$code][$errors_join]);
		$this->error_msg = serialize($error_msg_db);

		if ($write_db_limit)
		{
			return $this->updateFields(array(
				'error_msg' => serialize($error_msg_db)
			));
		}
	}

	public function updateFields($fields = array(), $override_values = true)
	{
		if ($override_values)
		{
			foreach ($fields as $field => $value) 
				$this->{$field} = $value;
		}

		$fields['date_modified'] = date('Y-m-d H:i:s');

		return Db::getInstance()->update('newsletter_pro_send_step', $fields, '`id_newsletter_pro_send_step` = '.(int)$this->id);
	}

	public function hasConnection()
	{
		return isset($this->connection);
	}

	public function shutdown()
	{
		NewsletterProShutdown::register(array($this, 'registerShutdown'));
		return $this;
	}

	public function registerShutdown()
	{
		$this->step_active = (count($this->getEmailsToSend()) > 0 ? 1 : 0);
		$this->update();
	}
}