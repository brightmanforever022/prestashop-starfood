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

header('Access-Control-Allow-Origin: *');
$root = dirname(dirname(getcwd()));

require_once($root.'/config/config.inc.php');
require_once($root.'/init.php');

$newsletterpro = Module::getInstanceByName('newsletterpro');

if (Tools::isSubmit('token'))
{
	$db_token = NewsletterPro::getNewsletterProToken();
	$token = trim(Tools::getValue('token'));

	if ($token !== trim($db_token))
		die('Invalid Token!');
}
else
	die('Invalid Token!');

function newsletterpro_send_task($module)
{
	$today = date('Y-m-d H:i:s');

	echo '<pre>';
	echo 'Date : '.$today."\n\n";

	try
	{
		if (NewsletterProTask::taskInProgress())
		{
			$task = NewsletterProTask::getTaskInProgress();

			$task_exit = true;
			$msg = "\n".$module->l('The task is in progress');
			if ($task)
			{
				if ($task->isTaskPaused())
				{
					$task->displayLog("\n")->emptyLog();
					$msg = "\n".$module->l('The task is in paused');
				}
				else if ((strtotime($task->date_modified)) + 120 <= strtotime(date('Y-m-d H:i:s')))
				{
					// start the task again after 2 minutes (300 seconds) if the date has not changes and the task status is showing in progress
					echo $module->l('Task was forced to continue.');
					$task->emptyLog();
					$task_exit = false;
				}
				else
					$task->displayLog("\n")->emptyLog();
			}

			if ($task_exit)
			{
				echo $msg;
				exit;
			}
		}

		$task = NewsletterProTask::getTask($today);

		if ($task)
		{
			$task->displayLog("\n")->emptyLog();
			$num_sent = $task->send();
			echo "\n".sprintf($module->l('This script execution has sent %s emails.'), $num_sent);
		}
		else
			echo $module->l('There are no active task scheduled for today.');
	}
	catch(Exception $e)
	{
		echo $e->getMessage();
	}
}

newsletterpro_send_task($newsletterpro);
?>