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

class NewsletterProSendConnection extends ObjectModel
{
	public $id_newsletter_pro_smtp;

	public $state;

	public $script_uid;

	const STATE_CLOSE = 0;

	const STATE_OPEN = 1;

	public static $cache_script_uid;

	public static $definition = array(
		'table'   => 'newsletter_pro_send_connection',
		'primary' => 'id_newsletter_pro_send_connection',
		'fields'  => array(
			'id_newsletter_pro_smtp' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'state'                  => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'script_uid'             => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
		)
	);


	public function __construct($id = null)
	{
		parent::__construct($id);

		if (!isset(self::$cache_script_uid))
			self::$cache_script_uid = uniqid();
	}

	public static function newInstance($id = null)
	{
		return new self($id);
	}

	public function getState()
	{
		return (int)Db::getInstance()->getValue('
			SELECT `state`
			FROM `'._DB_PREFIX_.'newsletter_pro_send_connection`
			WHERE `id_newsletter_pro_send_connection` = '.(int)$this->id.'
		');
	}

	public function isState($state_constant)
	{
		return ((int)$this->getState() === (int)$state_constant);
	}

	public function isOpen()
	{
		return $this->isState(self::STATE_OPEN);
	}

	public function isClose()
	{
		return $this->isState(self::STATE_CLOSE);
	}

	public static function isConnectionOpen($id)
	{
		$state = Db::getInstance()->getValue('SELECT `state` FROM `'._DB_PREFIX_.'newsletter_pro_send_connection` WHERE `id_newsletter_pro_send_connection` = '.(int)$id);
		return ($state == self::STATE_OPEN);
	}

	public static function isConnectionClose($id)
	{
		$state = Db::getInstance()->getValue('SELECT `state` FROM `'._DB_PREFIX_.'newsletter_pro_send_connection` WHERE `id_newsletter_pro_send_connection` = '.(int)$id);
		return ($state == self::STATE_CLOSE);
	}

	public static function getConnections()
	{
		return Db::getInstance()->executeS('
			SELECT sc.`id_newsletter_pro_send_connection`,
				sc.`id_newsletter_pro_smtp`,
				sc.`state`,
				s.`method`,
				s.`name`
			FROM `'._DB_PREFIX_.'newsletter_pro_send_connection` sc
			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_smtp` s ON (sc.`id_newsletter_pro_smtp` = s.`id_newsletter_pro_smtp`)
		');
	}

	public static function ajaxGetConnections()
	{
		$module = NewsletterPro::getInstance();
		$response = self::getConnections();

		foreach ($response as &$connection) 
		{
			if ((int)$connection['method'] == NewsletterProMail::METHOD_MAIL)
				$connection['connection_type'] = $module->l('Mail');
			else
				$connection['connection_type'] = $module->l('SMTP');
		}

		return Tools::jsonEncode($response);
	}

	public static function ajaxAddConnection($id_smtp)
	{
		$response = NewsletterProAjaxResponse::newInstance();

		try
		{
			// verify if the send newsletter method is in progress
			if (NewsletterProSend::getActiveId())
				return $response->addError(NewsletterPro::getInstance()->l('Unable to add a new connection, because the newsletter sending is in progress. You must stop the sending process to make this action.'))->display();

			if (!self::connectionIdExists($id_smtp))
				$response->addError(sprintf(NewsletterPro::getInstance()->l('Invalid connection id "%s"'), $id_smtp));

			$connection = NewsletterProSendConnection::newInstance();
			$connection->id_newsletter_pro_smtp = (int)$id_smtp;
			$connection->state = NewsletterProSendConnection::STATE_CLOSE;

			if (!$connection->add())
				$response->addError(NewsletterPro::getInstance()->l('An error occurred when adding the connection.'));
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	public static function ajaxDeleteConnection($id)
	{
		$response = NewsletterProAjaxResponse::newInstance();
		$module = NewsletterPro::getInstance();

		try
		{
			$connection = NewsletterProSendConnection::newInstance((int)$id);

			if (!Validate::isLoadedObject($connection))
				return $response->addError($module->l('The connection does not exit.'))->display();

			// verify if the send newsletter method is in progress
			if (NewsletterProSend::getActiveId())
				return $response->addError($module->l('The connection cannot be deleted, because the newsletter sending is in progress. You must stop the sending process to make this action.'))->display();

			if (!$connection->delete())
				$response->addError($module->l('The connection cannot be deleted.'));
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	public static function deleteBySmtpId($id_smtp)
	{
		return Db::getInstance()->delete('newsletter_pro_send_connection', '`id_newsletter_pro_smtp` = '.(int)$id_smtp);
	}

	public static function countConnections()
	{
		return (int)Db::getInstance()->getValue('
			SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_send_connection`
		');
	}

	public static function getNextFreeConnectionId()
	{
		return (int)Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_send_connection` FROM `'._DB_PREFIX_.'newsletter_pro_send_connection` where state = '.(int)NewsletterProSendConnection::STATE_CLOSE.'
		');
	}

	private static function connectionIdExists($id_smtp)
	{
		return (int)Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_smtp` 
			FROM `'._DB_PREFIX_.'newsletter_pro_smtp`
			WHERE `id_newsletter_pro_smtp` = '.(int)$id_smtp.'
		');
	}

	private static function connectionIdIsDuplicate($id_smtp)
	{
		return (int)Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_smtp` 
			FROM `'._DB_PREFIX_.'newsletter_pro_send_connection`
			WHERE `id_newsletter_pro_smtp` = '.(int)$id_smtp.'
		');
	}

	public function updateFields($fields = array(), $override_values = true)
	{
		if ($override_values)
		{
			foreach ($fields as $field => $value) 
				$this->{$field} = $value;
		}

		return Db::getInstance()->update('newsletter_pro_send_connection', $fields, '`id_newsletter_pro_send_connection` = '.(int)$this->id, 0, true);
	}

	public function open()
	{
		return $this->updateFields(array(
			'state' => self::STATE_OPEN,
			'script_uid' => NewsletterProSendConnection::getScriptUid()
		));
	}

	public function close()
	{
		return $this->updateFields(array(
			'state' => self::STATE_CLOSE,
			'script_uid' => null
		));
	}

	public static function getScriptUid()
	{
		if (!isset(self::$cache_script_uid))
			self::$cache_script_uid = uniqid();

		return self::$cache_script_uid;
	}

	public static function countConnectionsAvailable()
	{
		return (int)Db::getInstance()->getValue('
			SELECT COUNT(*) 
			FROM `'._DB_PREFIX_.'newsletter_pro_send_connection`
			WHERE `state` = '.(int)self::STATE_CLOSE.'
		');
	}

	public static function clearAll()
	{
		return Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'newsletter_pro_send_connection`
			SET `state` = 0, `script_uid` = NULL
		');
	}
}