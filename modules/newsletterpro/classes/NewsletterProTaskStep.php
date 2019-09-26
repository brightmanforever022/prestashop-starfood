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

class NewsletterProTaskStep extends ObjectModel
{
	public $id_newsletter_pro_task;

	public $step;

	public $step_active;

	public $emails_to_send;

	public $emails_to_send_unserialized;

	public $emails_sent;

	public $emails_sent_unserialized;

	public $date;

	public static $definition = array(
		'table'   => 'newsletter_pro_task_step',
		'primary' => 'id_newsletter_pro_task_step',
		'fields'  => array(
			'id_newsletter_pro_task' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'step'                   => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'step_active'            => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'emails_to_send'         => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'emails_sent'            => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'date'                   => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
		)
	);

	public function __construct($id = null)
	{
		// set defaults values
		$this->emails_to_send = serialize(array());
		$this->emails_sent = serialize(array());

		parent::__construct($id);

		if (!isset($this->emails_to_send_unserialized))
			$this->emails_to_send_unserialized = NewsletterProTools::unSerialize($this->emails_to_send);

		if (!isset($this->emails_sent_unserialized))
			$this->emails_sent_unserialized = NewsletterProTools::unSerialize($this->emails_sent);
	}

	public static function newInstance($id = null)
	{
		return new self($id);
	}

	public function add($autodate = true, $null_values = false)
	{
		$this->emails_to_send = serialize($this->emails_to_send_unserialized);
		$this->emails_sent    = serialize($this->emails_sent_unserialized);

		return parent::add($autodate, $null_values);
	}

	public function update($null_values = false)
	{
		$this->emails_to_send = serialize($this->emails_to_send_unserialized);
		$this->emails_sent    = serialize($this->emails_sent_unserialized);

		return parent::update($null_values);
	}

	public function updateFields($fields = array(), $override_values = true)
	{
		if ($override_values)
		{
			foreach ($fields as $field => $value) 
				$this->{$field} = $value;
		}
		
		return Db::getInstance()->update('newsletter_pro_task_step', $fields, '`id_newsletter_pro_task_step` = '.(int)$this->id);
	}

	public function delete()
	{
		return parent::delete();
	}

	public function setEmailsToSend($value)
	{
		$this->emails_to_send_unserialized = $value;
		$this->emails_to_send = serialize($value);
	}

	public function getEmailsToSend()
	{
		return $this->emails_to_send_unserialized;
	}

	public function setEmailsSent($value)
	{
		$this->emails_sent_unserialized = $value;
		$this->emails_sent = serialize($value);
	}

	public function getEmailsSent()
	{
		return $this->emails_sent_unserialized;
	}
}