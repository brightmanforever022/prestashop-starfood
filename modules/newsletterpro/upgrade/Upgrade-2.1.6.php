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

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_1_6($module)
{
	$upgrade = $module->upgrade;

	// hooks update (no)
	$upgrade->registerHook('customerAccount');
	$upgrade->registerHook('createAccount');

	// configuration update (no)
	$upgrade->deleteConfiguration('NO_DISPLAY_NUMBER');
	$upgrade->deleteConfiguration('CUSTOMER_CONFIRM_ON_DELETE');
	$upgrade->deleteConfiguration('DISPLAY_ACTIVE_COLUMN');
	$upgrade->deleteConfiguration('DISPLAY_ACTIONS_COLUMN');

	$upgrade->updateConfiguration('SUBSCRIBE_BY_CATEGORY', '1');
	$upgrade->updateConfiguration('SEND_NEWSLETTER_ON_SUBSCRIBE', '0');
	$upgrade->updateConfiguration('FUNC_MAIL_EMAIL', Configuration::get('PS_SHOP_EMAIL'));

	// chimp update (no)
	// chimp database don't need update because is intalled separatly
	$upgrade->createTable('newsletter_pro_config', '
		`id_newsletter_pro_config` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`name` VARCHAR(50) NOT NULL,
		`value` TEXT NULL,
		PRIMARY KEY (`id_newsletter_pro_config`),
		UNIQUE INDEX `name` (`name`)
	');

	if (!$upgrade->success())
		return false;

	if (!$upgrade->valueExists('newsletter_pro_config', 'name', 'CHIMP_SYNC'))
		$upgrade->insertValue('newsletter_pro_config', array(
			'name' => 'CHIMP_SYNC',
			'value' => serialize(array()),
		));

	if (!$upgrade->valueExists('newsletter_pro_config', 'name', 'LAST_DATE_CHIMP_SYNC'))
		$upgrade->insertValue('newsletter_pro_config', array(
			'name' => 'LAST_DATE_CHIMP_SYNC',
			'value' => '0000-00-00 00:00:00',
		));

	// database update (no)
	$upgrade->createTable('newsletter_pro_send', "
		`id_newsletter_pro_send` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_newsletter_pro_tpl_history` INT(11) UNSIGNED NOT NULL DEFAULT '0',
		`template` VARCHAR(50) NULL DEFAULT NULL,
		`active` INT(1) NOT NULL DEFAULT '0',
		`emails_count` INT(11) UNSIGNED NOT NULL DEFAULT '0',
		`emails_success` INT(11) UNSIGNED NOT NULL DEFAULT '0',
		`emails_error` INT(11) UNSIGNED NOT NULL DEFAULT '0',
		`emails_completed` INT(11) UNSIGNED NOT NULL DEFAULT '0',
		`error_msg` LONGTEXT NULL,
		`date` DATE NULL DEFAULT NULL,
		PRIMARY KEY (`id_newsletter_pro_send`),
		INDEX `id_newsletter_pro_tpl_history` (`id_newsletter_pro_tpl_history`)
	");

	$upgrade->createTable('newsletter_pro_send_step', "
		`id_newsletter_pro_send_step` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_newsletter_pro_send` INT(11) UNSIGNED NOT NULL DEFAULT '0',
		`step` INT(11) UNSIGNED NOT NULL DEFAULT '0',
		`step_active` INT(1) NOT NULL DEFAULT '0',
		`emails_to_send` LONGTEXT NULL,
		`emails_sent` LONGTEXT NULL,
		`date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`id_newsletter_pro_send_step`),
		INDEX `id_step` (`step`),
		INDEX `id_task` (`id_newsletter_pro_send`)
	");

	if (!$upgrade->success())
		return false;

	$table_newsletter_pro = 'newsletter_pro';
	$result = get_newsletter_pro_data_2_1_6($upgrade, $table_newsletter_pro);
	if (is_array($result))
	{
		$group_by_steps = array();

		foreach ($result as $row)
			$group_by_steps[$row['id_step']][] = $row;

		$id_newsletter_pro_send = 0;
		foreach ($group_by_steps as $row)
		{
			$id_newsletter_pro_send++;

			reset($row);
			$first_key = key($row);
			end($row);
			$last_key = key($row);

			$emails_success   = $row[$last_key]['count_sent_succ'];
			$emails_error     = $row[$last_key]['count_sent_err'];
			$emails_count     = (int)$emails_success + (int)$emails_error;
			// this option is not a real option
			$emails_completed = $emails_count;

			if (Db::getInstance()->insert('newsletter_pro_send', array(
				'id_newsletter_pro_send'        => (int)$id_newsletter_pro_send,
				'id_newsletter_pro_tpl_history' => (int)$row[$first_key]['id_newsletter_pro_tpl_history'],
				'template'                      => '',
				'active'                        => 0,
				'emails_count'                  => (int)$emails_count,
				'emails_success'                => (int)$emails_success,
				'emails_error'                  => (int)$emails_error,
				'emails_completed'              => (int)$emails_completed,
				'error_msg'                     => 'a:0:{}',
				'date'                          => pSQL($row[$first_key]['date']),
			)))
			{
				foreach ($row as $step_row)
				{
					if (!Db::getInstance()->insert('newsletter_pro_send_step', array(
						'id_newsletter_pro_send' => (int)$id_newsletter_pro_send,
						'step'                   => (int)$step_row['id_step'],
						'step_active'            => (int)$step_row['active'],
						'emails_to_send'         => $step_row['emails_to_send'],
						'emails_sent'            => $step_row['emails_sent'],
						'date'                   => pSQL($step_row['date']), 
					)))
						$upgrade->addError(sprintf($module->l('Cannot insert the data into the table "%s".'), 'newsletter_pro_send_step'));
				}
			}
			else
				$upgrade->addError(sprintf($module->l('Cannot insert the data into the table "%s".'), 'id_newsletter_pro_send'));
		}
	}
	else
		$upgrade->addError(sprintf($module->l('An error occurred on getting the data from the database table "%s".'), $table_newsletter_pro));

	if (!$upgrade->success())
		return false;

	$upgrade->deleteTable('newsletter_pro');

	$upgrade->createTable('newsletter_pro_statistics', "
		`id_product` INT(10) UNSIGNED NOT NULL DEFAULT '0',
		`clicks` INT(10) NOT NULL DEFAULT '0',
		UNIQUE INDEX `id_product` (`id_product`)
	");

	$upgrade->createTable('newsletter_pro_customer_category', "
		`id_customer` INT(10) UNSIGNED NOT NULL DEFAULT '0',
		`categories` TEXT NULL,
		UNIQUE INDEX `id_customer` (`id_customer`)
	");

	$lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
	$shop_default = (int)Configuration::get('PS_SHOP_DEFAULT');

	$upgrade->changeColumn('newsletter_pro_email', 'id_shop', "`id_shop` INT(10) UNSIGNED NOT NULL DEFAULT '".$shop_default."'");
	$upgrade->changeColumn('newsletter_pro_email', 'id_shop_group', "`id_shop_group` INT(10) UNSIGNED NOT NULL DEFAULT '".$shop_default."'");
	$upgrade->changeColumn('newsletter_pro_email', 'id_lang', "`id_lang` INT(10) UNSIGNED NULL DEFAULT '".$lang_default."'");
	$upgrade->changeColumn('newsletter_pro_email', 'date_add', '`date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');

	$upgrade->deleteTable('newsletter_pro_image');

	$upgrade->addColumn('newsletter_pro_smtp', 'from', '`from` VARCHAR(255) NULL DEFAULT NULL', 'name');

	$upgrade->addColumn('newsletter_pro_task', 'pause', "`pause` INT(10) NOT NULL DEFAULT '0'", 'sleep');
	$upgrade->changeColumn('newsletter_pro_task', 'error_msg', '`error_msg` LONGTEXT NULL');

	$upgrade->changeColumn('newsletter_pro_task_step', 'date', '`date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');

	$upgrade->addColumn('newsletter_pro_tpl_history', 'clicks', "`clicks` INT(1) NOT NULL DEFAULT '0'");

	return $upgrade->success();
}

function count_emails_2_1_6($row, $field)
{
	$count = 0;
	foreach ($row as $value)
		$count += $value[$field];
	return $count;
}

function get_newsletter_pro_data_2_1_6($upgrade, $table)
{
	try
	{
		if ($upgrade->tableExists($table))
		{
			$result = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.$table.'` WHERE 1');
			return $result;
		}
		return array();
	}
	catch(Exception $e)
	{
		return false;
	}

	return false;
}