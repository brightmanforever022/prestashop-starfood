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

class NewsletterProTask extends ObjectModel
{
	public $id_newsletter_pro_smtp;

	public $id_newsletter_pro_tpl_history;

	public $date_start;

	public $date_modified;

	public $active;

	public $template;

	public $send_method;

	public $status;

	public $sleep;

	public $pause;

	public $emails_count;

	public $emails_error;

	public $emails_success;

	public $emails_completed;

	public $done;

	public $error_msg;

	/**
	* variables
	*/

	private $log;

	private $num_sent = 0;

	/**
	 * This variable is used into the function send proccess
	 * @var object
	 */
	private $task_step;

	/**
	 * 24000
	 */
	const MAX_EXECUTION_TIME = 24000;

	const STATUS_DEFAULT = 0;

	const STATUS_IN_PROGRESS = 1;

	public static $definition = array(
		'table'   => 'newsletter_pro_task',
		'primary' => 'id_newsletter_pro_task',
		'fields'  => array(
			'id_newsletter_pro_smtp'        => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_newsletter_pro_tpl_history' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'date_start'                    => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
			'date_modified'                 => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
			'active'                        => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'template'                      => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
			'send_method'                   => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
			'status'                        => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'sleep'                         => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'pause'                         => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'emails_count'                  => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'emails_error'                  => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'emails_success'                => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'emails_completed'              => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'done'                          => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'error_msg'                     => array('type' => self::TYPE_HTML, 'validate' => 'isString'),
		)
	);

	public function __construct($id = null)
	{
		// set defaults values
		$this->active = 1;
		$this->error_msg = serialize(array());
		$this->log = array();

		$this->one_mb = 1048576;
		$this->memory_limit = (int)ini_get('memory_limit') * $this->one_mb;

		parent::__construct($id);

		if (Validate::isLoadedObject($this))
		{
			$this->log(sprintf(NewsletterPro::getInstance()->l('Task id : %s'), $this->id));
			$this->log(sprintf(NewsletterPro::getInstance()->l('Send method : %s'), $this->send_method));
			$this->log(sprintf(NewsletterPro::getInstance()->l('Total emails : %s'), $this->emails_count));
			$this->log(sprintf(NewsletterPro::getInstance()->l('Sent success : %s'), $this->emails_success));
			$this->log(sprintf(NewsletterPro::getInstance()->l('Sent errors : %s'), $this->emails_error));
			$this->log(sprintf(NewsletterPro::getInstance()->l('Sent completed : %s')."\n", $this->emails_completed));
		}
		else
			$this->log(NewsletterPro::getInstance()->l('No task has been loaded.'));
	}

	public static function newInstance($id = null)
	{
		return new self($id);
	}

	public function add($autodate = true, $null_values = false)
	{
		return parent::add($autodate, $null_values);
	}

	public function update($null_values = false)
	{
		$this->date_modified = date('Y-m-d H:i:s');
		return parent::update($null_values);
	}

	public function updateFields($fields = array())
	{
		$fields_default = array(
			'date_modified' => pSQL(date('Y-m-d H:i:s'))
		);
		return Db::getInstance()->update('newsletter_pro_task', array_merge($fields_default, $fields), '`id_newsletter_pro_task` = '.(int)$this->id);
	}

	public function delete()
	{
		$evaluate = NewsletterProEvaluate::newInstance();

		foreach ($this->getStepsIds() as $id)
		{
			$task_step = NewsletterProTaskStep::newInstance($id);
			$evaluate->add($task_step->delete());
		}

		return parent::delete() && $evaluate->success();
	}

	public function log($value)
	{
		$this->log[] = $value;
	}

	public function displayLog($separator = "\n")
	{
		echo '<pre>';
		echo join($separator, $this->log).$separator;
		echo '</pre>';
		return $this;
	}

	public function uniqueLog()
	{
		$this->log = array_unique($this->log);
		return $this;
	}

	public function emptyLog()
	{
		$this->log = array();
		return $this;
	}

	public function getStepsIds($step_active = false, $limit = 0)
	{
		$results = Db::getInstance()->executeS('
			SELECT `id_newsletter_pro_task_step` FROM `'._DB_PREFIX_.'newsletter_pro_task_step`
			WHERE `id_newsletter_pro_task` = '.(int)$this->id.'
			'.($step_active ? ' AND `step_active` = 1' : '').'
			ORDER BY `id_newsletter_pro_task_step`
			'.($limit > 0 ? ' LIMIT '.(int)$limit : '').'
		');

		return array_map(array($this, 'getStepsIdsCallback'), $results);
	}

	private function getStepsIdsCallback($row)
	{
		return !empty($row) ? $row['id_newsletter_pro_task_step'] : false;
	}

	public static function getTask($date = null)
	{
		$date = isset($date) ? $date : date('Y-m-d H:i:s');

		$date_day = date('Y-m-d', strtotime($date));

		$id = (int)Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_task` FROM `'._DB_PREFIX_.'newsletter_pro_task`
			WHERE `date_start` <= "'.pSQL($date).'"
			AND DATE(`date_start`) = "'.pSQL($date_day).'"
			AND `active` = 1
			AND `done` = 0
			ORDER BY `date_start` ASC;
		');

		$task = self::newInstance($id);
		return Validate::isLoadedObject($task) ? $task : false;
	}

	/**
	 * Sent the task
	 * @return integer Number of newsletters sent
	 */
	public function send()
	{
		ignore_user_abort(true);
		set_time_limit(0);
		@ini_set('max_execution_time', self::MAX_EXECUTION_TIME);

		register_shutdown_function(array($this, 'sendShutdownFunctionCallback'));

		// update task status
		$this->status = self::STATUS_IN_PROGRESS;
		$this->update();

		$this->task_step = $this->getCurrentStep();

		if ($this->task_step)
		{
			$emails_to_send = $this->task_step->getEmailsToSend();
			$emails_sent    = $this->task_step->getEmailsSent();

			foreach ($emails_to_send as $index => $email)
			{
				$current_mem = memory_get_usage(true);

				if ($current_mem + $this->one_mb * 24 >= $this->memory_limit)
					exit;

				if (!$this->taskExists())
					exit;

				if ($this->isTaskPaused())
					exit;

				$template = NewsletterProTemplate::newHistory($this->id_newsletter_pro_tpl_history, $email)->load();

				if ($template->user)
				{
					// exclude emails
					$exclude_email = false;
					$to_info       = NewsletterProMail::getEmailInfo($template->user->to());
					$to_email      = $to_info['email'];


					if (NewsletterProEmailExclusion::newInstance()->emailExists($to_email))
						$exclude_email = true;

					// build forwarders
					$forward = NewsletterProSendManager::buildForward($this->id_newsletter_pro_tpl_history, 'history', $template->user->to());

					$message = $template->message();
					$title = $message['title'];
					$body = $message['body'];


					$send_manager = NewsletterProSendManager::getInstance();
					$send_manager->setTemplateNameForAttachment($template->name);
					$send = $send_manager->sendNewsletter($title, $body, $template->user->to(), array(
							'user'        => $template->user,
							'id_smtp'     => (int)$this->id_newsletter_pro_smtp,
							'send_method' => $this->send_method,
						), $forward, false, $exclude_email);

					if (!is_array($send) && $send == true)
					{
						$emails_sent[] = array(
							'email' => $template->user->email, 
							'status' => true
						);
						$this->emails_success++;
						$this->num_sent++;

						$this->log(NewsletterPro::getInstance()->l('Success').' : '.(string)$template->user->email);
					}
					else
					{
						$emails_sent[] = array(
							'email' => $template->user->email, 
							'status' => false
						);

						$this->emails_error++;

						$this->appendError(array(
							'smtp' => join('<br>', $send)
						));

						$this->log(NewsletterPro::getInstance()->l('Error').' : '.join("\n", $send).' : '.(string)$template->user->email);
					}

					unset($emails_to_send[$index]);
					$this->task_step->setEmailsToSend($emails_to_send);
					$this->task_step->setEmailsSent($emails_sent);
					$this->task_step->step_active = (count($emails_to_send) > 0 ? 1 : 0);

					if (!$exclude_email && (int)$this->sleep > 0)
						sleep((int)$this->sleep);
				}
				else
				{
					$this->appendError(array(
						'email' => pSQL(sprintf(NewsletterPro::getInstance()->l('The email %s does not exists in the database.'), $email))
					));
				}

				$this->emails_completed++;

				if ($this->emails_completed % (int)NewsletterPro::getInstance()->ini_config['task_write_db_limit'] == 0)
				{
					$this->task_step->update();
					$this->updateFields(array(
						'emails_completed' => (int)$this->emails_completed,
						'emails_success'   => (int)$this->emails_success,
						'emails_error'     => (int)$this->emails_error
					));
				}
			}

			// end the script if it's the case
			if ($this->emails_completed > $this->emails_count)
				return $this->endSend();

			// update when the step is done
			$this->task_step->update();
			$this->updateFields(array(
				'emails_completed' => (int)$this->emails_completed,
				'emails_success'   => (int)$this->emails_success,
				'emails_error'     => (int)$this->emails_error
			));

			// get next step emails
			return $this->send();
		}
		
		return $this->endSend();
	}

	private function endSend()
	{
		$this->done = 1;
		$this->update();
		return $this->num_sent;	
	}

	public function sendShutdownFunctionCallback()
	{
		$ob_content = ob_get_contents();
		@ob_end_clean();

		if (preg_match('/Fatal error/', $ob_content) && preg_match('/Maximum execution time/', $ob_content))
		{
			$this->displayLog()->emptyLog();
			echo '<pre>';
			echo "\n";
			echo NewsletterPro::getInstance()->l('PHP max execution time exceeded. Access the script again to continue the sending process.');
			echo '</pre>';
		}
		else
			echo $ob_content;

		if (Validate::isLoadedObject($this))
		{
			$this->status = self::STATUS_DEFAULT;
			$this->update();
		}

		if (isset($this->task_step) && Validate::isLoadedObject($this->task_step))
			$this->task_step->update();
	}

	public function sendTaskAjax()
	{
		$errors = array();

		if (NewsletterProTask::taskInProgress())
			$errors[] = NewsletterPro::getInstance()->l('Cannot send multiple tasks in the same time.');

		@ob_end_clean();
		@ob_start();
		echo Tools::jsonEncode(array(
			'status' => empty($errors),
			'errors' => $errors,
		));
		$size = ob_get_length();
		header("Content-Length: $size");
		header('Connection: close');
		@ob_end_flush();
		@ob_flush();
		flush();
		if (session_id())
			session_write_close();

		if (empty($errors))
		{
			// run in background
			$this->send();
		}
	}

	public function isInProgress()
	{
		return $this->status;
	}

	/**
	 * The the current task step or the next step if the current step does not have emails addresses
	 * @return object/boolean
	 */
	public function getCurrentStep()
	{
		$steps_id = $this->getStepsIds(true, 1);

		if ($steps_id)
		{
			$task_step = NewsletterProTaskStep::newInstance($steps_id[0]);

			$emails_to_send = $task_step->getEmailsToSend();
			if (empty($emails_to_send))
			{
				$task_step->step_active = 0;
				// if there are no step emails, write step_active = 0
				if ($task_step->update())
					return $this->getCurrentStep();
			}
			else
				return $task_step;
		}
		else
		{
			// if there are no steps, siable the task
			$this->activ = 0;
			$this->update();
		}

		return false;
	}

	/**
	 * Task exist
	 *
	 * @param  int $id
	 * @return int
	 */
	public function taskExists()
	{
		return Db::getInstance()->getValue('
			SELECT count(*) FROM `'._DB_PREFIX_.'newsletter_pro_task` WHERE `id_newsletter_pro_task`='.(int)$this->id
		);
	}

	/**
	 * Is task paused
	 *
	 * @param  int  $id
	 * @return boolean
	 */
	public function isTaskPaused()
	{
		$this->pause = (int)Db::getInstance()->getValue('
			SELECT `pause` FROM `'._DB_PREFIX_.'newsletter_pro_task` WHERE `id_newsletter_pro_task`='.(int)$this->id
		);

		return $this->pause;
	}

	private function appendError($error_msg)
	{
		$error_msg_db = NewsletterProTools::unSerialize($this->error_msg);
		$error_msg_write = array_merge($error_msg_db, $error_msg);
		$this->error_msg = serialize($error_msg_write);
		return $this->update();
	}

	/**
	 * Task in progress
	 *
	 * @return boolean
	 */
	public static function taskInProgress()
	{
		return (Db::getInstance()->getValue('
			SELECT count(*) FROM `'._DB_PREFIX_.'newsletter_pro_task`
			WHERE `status` = '.(int)self::STATUS_IN_PROGRESS.'
			AND `done` = 0
		') ? true : false);
	}

	public static function getTaskInProgress()
	{
		$task_id = (int)Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_task` FROM `'._DB_PREFIX_.'newsletter_pro_task`
			WHERE `status` = '.(int)self::STATUS_IN_PROGRESS.'
			AND `done` = 0
		');

		$task = NewsletterProTask::newInstance($task_id);
		if (Validate::isLoadedObject($task))
		{
			if ($task->getCurrentStep())
				return $task;
		}

		return false;
	}

	public function pauseTask()
	{
		return (int)Db::getInstance()->update('newsletter_pro_task', array(
			'pause' => 1,
		), '`id_newsletter_pro_task` = '.(int)$this->id, 1);
	}
}
