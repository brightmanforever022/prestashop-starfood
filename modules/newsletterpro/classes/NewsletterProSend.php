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

class NewsletterProSend extends ObjectModel
{
	public $id_newsletter_pro_tpl_history;

	public $template;

	public $active;

	public $state;

	public $emails_count;

	public $emails_success;

	public $emails_error;

	public $emails_completed;

	public $error_msg;

	public $date;

	/**
	 * The states are available in javascript
	 * If there are changes at this states, is required to change also the javascript file view/js/send_manager.js
	 */
	const STATE_DEFAULT = 0;

	const STATE_PAUSE = 1;

	const STATE_IN_PROGRESS = 2;

	const STATE_DONE = 3;

	/**
	 * Variables
	 */
	
	public $progress_success = 0;

	public $progress_errors = 0;

	public static $definition = array(
		'table'   => 'newsletter_pro_send',
		'primary' => 'id_newsletter_pro_send',
		'fields'  => array(
			'id_newsletter_pro_tpl_history' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'template'                      => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
			'active'                        => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'state'                         => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'emails_count'                  => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'emails_error'                  => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'emails_success'                => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'emails_completed'              => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'error_msg'                     => array('type' => self::TYPE_HTML, 'validate' => 'isString'),
			'date'                          => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
		)
	);

	public function __construct($id = null)
	{
		parent::__construct($id);
	}

	public static function newInstance($id = null)
	{
		return new self($id);
	}

	public static function getActiveId()
	{
		return (int)Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_send`
			FROM `'._DB_PREFIX_.'newsletter_pro_send`
			WHERE `active` = 1
			AND `id_newsletter_pro_send` = (
				SELECT MAX(`id_newsletter_pro_send`) 
				FROM `'._DB_PREFIX_.'newsletter_pro_send`
			)
		');
	}

	public function updateProgress()
	{
		$result = Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'newsletter_pro_send`
			SET 
				`emails_success` = `emails_success` + '.(int)$this->progress_success.',
				`emails_error` = `emails_error` + '.(int)$this->progress_errors.',
				`emails_completed` = `emails_completed` + '.((int)$this->progress_success + (int)$this->progress_errors).'
			WHERE `id_newsletter_pro_send` = '.(int)$this->id.'
		');

		$this->progress_success = 0;
		$this->progress_errors = 0;

		return $result;
	}

	public function updateDefaults($ignore = array())
	{
		$fields = array(
			'id_newsletter_pro_tpl_history' => (int)$this->id_newsletter_pro_tpl_history,
			'template'                      => pSQL($this->template),
			'active'                        => (int)$this->active,
			'state'                         => (int)$this->state,
			'emails_count'                  => (int)$this->emails_count,
			'date'                          => pSQL($this->date)
		);

		if (!empty($ignore))
		{
			foreach ($ignore as $key) 
			{
				if (isset($fields[$key]))
					unset($fields[$key]);
			}
		}

		return $this->updateFields($fields);
	}

	public function updateAll($ignore = array())
	{
		return ($this->updateProgress() && $this->updateDefaults($ignore));
	}

	public function progressSuccess()
	{
		$this->progress_success++;
	}

	public function progressErrors()
	{
		$this->progress_errors++;
	}
	
	public function statePause()
	{
		$this->state = self::STATE_PAUSE;
		$this->active = 1;
		
		return $this->updateFields(array(
			'state' => $this->state,
			'active' => $this->active,
		));
	}

	public function stateDone()
	{
		$this->state = self::STATE_DONE;
		$this->active = 0;

		return $this->updateFields(array(
			'state' => $this->state,
			'active' => $this->active,
		));
	}

	public function stateInProgress()
	{
		$this->state = self::STATE_IN_PROGRESS;
		$this->active = 1;

		return $this->updateFields(array(
			'state' => $this->state,
			'active' => $this->active,
		));
	}

	public function stateDefault()
	{
		$this->state = self::STATE_DEFAULT;
		$this->active = 1;

		return $this->updateFields(array(
			'state' => $this->state,
			'active' => $this->active,
		));
	}

	public function setState($state)
	{
		$this->state = $state;

		return $this->updateFields(array(
			'state' => $this->state
		));
	}

	private function getStepsIdsCallback($row)
	{
		return !empty($row) ? $row['id_newsletter_pro_send_step'] : false;
	}

	public function getStepsIds($step_active = false, $limit = 0)
	{
		$results = Db::getInstance()->executeS('
			SELECT `id_newsletter_pro_send_step` 
			FROM `'._DB_PREFIX_.'newsletter_pro_send_step`
			WHERE `id_newsletter_pro_send` = '.(int)$this->id.'
			'.($step_active ? ' AND `step_active` = 1' : '').'
			ORDER BY `id_newsletter_pro_send_step`
			'.($limit > 0 ? ' LIMIT '.(int)$limit : '').'
		');

		return array_map(array($this, 'getStepsIdsCallback'), $results);
	}

	public function getCurrentStepId()
	{
		$id = (int)Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_send_step` 
			FROM `'._DB_PREFIX_.'newsletter_pro_send_step`
			WHERE `id_newsletter_pro_send` = '.(int)$this->id.'
			 AND `step_active` = 1
			ORDER BY `id_newsletter_pro_send_step`
		');

		return $id;
	}

	public function getStepsIdAndConnectionsId($step_active = false, $closed_connection = false, $limit = 0)
	{
		$results = Db::getInstance()->executeS('
			SELECT 
				ss.`id_newsletter_pro_send_step`, 
				sc.`id_newsletter_pro_send_connection`,
				sc.`state`
			FROM `'._DB_PREFIX_.'newsletter_pro_send_step` ss
			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_send_connection` sc
				ON (
					ss.`id_newsletter_pro_send_connection` = sc.`id_newsletter_pro_send_connection`
					)
			WHERE ss.`id_newsletter_pro_send` = '.(int)$this->id.'
			'.($step_active ? ' AND ss.`step_active` = 1' : '').'
			ORDER BY ss.`id_newsletter_pro_send_step`
		');

		$ids = array();

		foreach ($results as $row) 
		{
			if ($closed_connection)
			{
				if ((int)$row['state'] == NewsletterProSendConnection::STATE_CLOSE)
					$ids[(int)$row['id_newsletter_pro_send_step']] = (int)$row['id_newsletter_pro_send_connection'];
			}
			else
				$ids[(int)$row['id_newsletter_pro_send_step']] = (int)$row['id_newsletter_pro_send_connection'];
		}

		if ($limit > 0)
		{
			$slice_ids = array();
			foreach ($ids as $id_step => $id_connection) 
			{
				if (count($slice_ids) < $limit)
				{
					$slice_ids[$id_step] = $id_connection;
					break;
				}
			}

			return $slice_ids;
		}

		return $ids;
	}

	public function getFirstFreeStepIdAndOpenConnection()
	{
		$result = Db::getInstance()->getRow('
			SELECT ss.`id_newsletter_pro_send_step`,
					sc.`id_newsletter_pro_send_connection`
			FROM `'._DB_PREFIX_.'newsletter_pro_send_step` ss
			INNER JOIN `'._DB_PREFIX_.'newsletter_pro_send_connection` sc
				ON (
					ss.`id_newsletter_pro_send_connection` = sc.`id_newsletter_pro_send_connection`
					AND sc.`state` = 0
					)
			WHERE ss.`id_newsletter_pro_send` = '.(int)$this->id.'
			AND ss.`step_active` = 1
			AND sc.`script_uid` IS NULL
			ORDER BY ss.`id_newsletter_pro_send_step`
		');

		if (empty($result))
			return 0;

		Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'newsletter_pro_send_connection` 
			SET `state` = 1, `script_uid` = "'.pSQL(NewsletterProSendConnection::getScriptUid()).'"
			WHERE id_newsletter_pro_send_connection = '.(int)$result['id_newsletter_pro_send_connection'].'
			AND `state` = 0
			AND `script_uid` IS NULL;
		');

		if (!Db::getInstance()->Affected_Rows())
			return 0;

		NewsletterProShutdown::register(array($this, 'shutdownDbCloseConnection'), array((int)$result['id_newsletter_pro_send_connection']));

		return (int)$result['id_newsletter_pro_send_step'];
	}

	public function shutdownDbCloseConnection($id_connection)
	{
		Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'newsletter_pro_send_connection` 
			SET `state` = 0, `script_uid` = NULL
			WHERE id_newsletter_pro_send_connection = '.(int)$id_connection.'
			AND `state` = 1
			AND `script_uid` = "'.pSQL(NewsletterProSendConnection::getScriptUid()).'"
		');

		if (Db::getInstance()->Affected_Rows())
			return true;
		return false;
	}

	public function getInfo()
	{
		return Db::getInstance()->getRow('
			SELECT `active`, `state`
			FROM `'._DB_PREFIX_.'newsletter_pro_send`
			WHERE `id_newsletter_pro_send` = '.(int)$this->id.'
		');
	}

	public function isActive()
	{
		$value = (int)Db::getInstance()->getValue('
			SELECT `active` 
			FROM `'._DB_PREFIX_.'newsletter_pro_send`
			WHERE `id_newsletter_pro_send` = '.(int)$this->id.'
		');

		$this->active = $value;
		return $value;
	}

	public function getState()
	{
		$value = (int)Db::getInstance()->getValue('
			SELECT `state` 
			FROM `'._DB_PREFIX_.'newsletter_pro_send`
			WHERE `id_newsletter_pro_send` = '.(int)$this->id.'
		');

		$this->state = (int)$value;
		return $this->state;
	}

	public function isPause($state = null)
	{
		$state = isset($state) ? $state : $this->getState();
		return ($state == self::STATE_PAUSE);
	}

	public function isDefault($state = null)
	{
		$state = isset($state) ? $state : $this->getState();
		return ($state == self::STATE_DEFAULT);
	}

	public function isInProgress($state = null)
	{
		$state = isset($state) ? $state : $this->getState();
		return ($state == self::STATE_IN_PROGRESS);
	}

	public function isDone($state = null)
	{
		$state = isset($state) ? $state : $this->getState();
		return ($state == self::STATE_DONE);
	}

	public function updateFields($fields = array(), $override_values = true)
	{
		if ($override_values)
		{
			foreach ($fields as $field => $value) 
				$this->{$field} = $value;
		}

		return Db::getInstance()->update('newsletter_pro_send', $fields, '`id_newsletter_pro_send` = '.(int)$this->id, 0, true);
	}

	public function getEmailsToSend($limit = null)
	{
		if (!isset($limit))
			$limit = (int)NewsletterPro::getInstance()->step;

		$steps_id = $this->getStepsIds(true);
		$emails = array();

		if ($steps_id)
		{
			foreach ($steps_id as $id) 
			{
				$send_step = NewsletterProSendStep::newInstance($id);
				$emails_to_send = $send_step->getEmailsToSend();

				$emails = array_merge($emails, $emails_to_send);

				if (count($emails) >= $limit)
				{
					$emails = array_slice($emails, 0, $limit);
					break;
				}
			}
		}

		return $emails;
	}

	public function getEmailsSent($limit = null, $get_errors = true, $reverse = false, $get_last_emails = false)
	{
		if (!isset($limit))
			$limit = (int)NewsletterPro::getInstance()->step;

		$steps_id = $this->getStepsIds(true);

		if ($get_last_emails)
			$steps_id = array_reverse($this->getStepsIds(false));

		return $this->getEmailsSentDefault($steps_id, $limit, $get_errors, $reverse);
	}

	private function getEmailsSentDefault($steps_id, $limit = null, $get_errors = true, $reverse = false)
	{
		$emails = array();
		$emails_error = array();

		if ($steps_id)
		{
			foreach ($steps_id as $id) 
			{
				$send_step = NewsletterProSendStep::newInstance($id);
				$emails_sent = $send_step->getEmailsSent(0, $reverse);

				if ($get_errors)
				{
					$error_msg = $send_step->getErrorMsg();
					foreach ($error_msg as $errors) 
					{
						foreach ($errors as $error => $emails_e) 
						{
							foreach ($emails_e as $email_e) 
							{
								$emails_error[$email_e][] = $error;
								$emails_error[$email_e] = array_unique($emails_error[$email_e]);
							}
						}
					}
				}

				$emails = array_merge($emails, $emails_sent);

				if (count($emails) >= $limit)
				{
					$emails = array_slice($emails, 0, $limit);
					break;
				}
			}
		}

		if ($get_errors)
		{
			foreach ($emails as $key => $item) 
			{
				if (isset($emails_error[$item['email']]))
					$emails[$key]['errors'] = $emails_error[$item['email']];
				else
					$emails[$key]['errors'] = array();
			}
		}

		return $emails;
	}

	public function getRemaining()
	{
		$remaining = (int)$this->emails_count - (int)$this->emails_completed;

		if ($remaining >= 0)
			return $remaining;

		return 0;
	}

	public static function getLastId()
	{
		return (int)Db::getInstance()->getValue('
			SELECT MAX(`id_newsletter_pro_send`)
			FROM `'._DB_PREFIX_.'newsletter_pro_send`
			WHERE 1
		');
	}

	public function shutdown()
	{
		NewsletterProShutdown::register(array($this, 'registerShutdown'));
		return $this;
	}

	public function registerShutdown()
	{
		if (count($this->getStepsIds(true)) == 0)
		{
			$this->state = NewsletterProSend::STATE_DONE;
			$this->active = 0;
			$this->updateAll();
		}
		else if ($this->isInProgress())
		{
			$this->state = NewsletterProSend::STATE_DEFAULT;
			$this->updateAll();
		
		}
		else
			$this->updateAll(array('state', 'active'));
	}
}