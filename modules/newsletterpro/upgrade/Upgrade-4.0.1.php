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

function upgrade_module_4_0_1($module)
{
	$upgrade = $module->upgrade;

	$upgrade->updateConfiguration('SHOW_CLEAR_CACHE', 1);
	$upgrade->updateConfiguration('SHOW_CUSTOM_COLUMNS', array());

	$upgrade->addColumn('newsletter_pro_send_step', 'date_modified', '`date_modified` TIMESTAMP NULL DEFAULT NULL', 'date');
	$upgrade->addColumn('newsletter_pro_send_connection', 'script_uid', '`script_uid` VARCHAR(50) NULL DEFAULT NULL', 'state');
	$upgrade->addColumn('newsletter_pro_tpl_history', 'template_name', '`template_name` VARCHAR(255) NOT NULL', 'token');

	$upgrade->createTable('newsletter_pro_attachment', '
		`id_newsletter_pro_attachment` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`template_name` VARCHAR(255) NOT NULL,
		`files` LONGTEXT NOT NULL,
		PRIMARY KEY (`id_newsletter_pro_attachment`),
		UNIQUE INDEX `template_name` (`template_name`)
	');

	$upgrade->createTable('newsletter_pro_subscribers_custom_field', "
		`id_newsletter_pro_subscribers_custom_field` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`variable_name` VARCHAR(50) NOT NULL,
		`type` INT(10) NOT NULL,
		`required` INT(10) NOT NULL DEFAULT '0',
		PRIMARY KEY (`id_newsletter_pro_subscribers_custom_field`),
		UNIQUE INDEX `variable_name` (`variable_name`)
	");

	$upgrade->createTable('newsletter_pro_subscribers_custom_field_lang', "
		`id_newsletter_pro_subscribers_custom_field` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_lang` INT(10) UNSIGNED NOT NULL DEFAULT '0',
		`value` LONGTEXT NOT NULL,
		PRIMARY KEY (`id_newsletter_pro_subscribers_custom_field`, `id_lang`)
	");
	
	$upgrade->deleteColumn('newsletter_pro_tpl_history', 'template');

	$upgrade->createTable('newsletter_pro_tpl_history_lang', '
		`id_newsletter_pro_tpl_history` INT(10) UNSIGNED NOT NULL,
		`id_lang` INT(10) UNSIGNED NOT NULL,
		`template` LONGTEXT NULL,
		PRIMARY KEY (`id_newsletter_pro_tpl_history`, `id_lang`)
	');

	$upgrade->createTable('newsletter_pro_mailchimp_token', "
		`id_newsletter_pro_mailchimp_token` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`token` varchar(50) NOT NULL,
		`creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		`modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		`expiration_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY (`id_newsletter_pro_mailchimp_token`),
		UNIQUE KEY `token` (`token`)
	");

	try
	{
		$languages = Language::getLanguages(false);
		$languages_iso = array();
		foreach ($languages as $lang)
			$languages_iso[] = $lang['iso_code'];

		if (!in_array('en', $languages_iso))
			$languages_iso[] = 'en';

		$newsletter_dir = $module->tpl_location.'newsletter/';
		$np_index = $newsletter_dir.'index.php';

		$files = NewsletterProTools::getDirectoryIterator($newsletter_dir, '/\.html$/');
		foreach ($files as $file) 
		{

			$path = $file->getPathName();
			$basename = pathinfo($path, PATHINFO_BASENAME);
			$dir_path = pathinfo($path, PATHINFO_DIRNAME).'/';
			$file_path = $dir_path.pathinfo($path, PATHINFO_FILENAME).'/';

			if (!file_exists($file_path))
			{
				if (!mkdir($file_path, 0777))
					throw new Exception(sprintf($module->l('Unable the create the template "%s". Please check the CHMOD permissions.'), $file_path));
			}

			if (file_exists($np_index))
				@copy($np_index, $file_path.'index.php');

			foreach ($languages_iso as $iso)
			{
				$lang_path = $file_path.$iso.'/';

				if (!file_exists($lang_path))
				{
					if (!mkdir($lang_path, 0777))
						throw new Exception(sprintf($module->l('Unable the create the template "%s". Please check the CHMOD permissions.'), $lang_path));
				}

				if (file_exists($np_index))
					@copy($np_index, $lang_path.'index.php');

				$tpl_path = $lang_path.$basename;
				if (!copy($path, $tpl_path))
					throw new Exception(sprintf($module->l('Unable the create the template "%s". Please check the CHMOD permissions.'), $tpl_path));
			}

			@unlink($path);
		}
	}
	catch(Exception $e)
	{
		$upgrade->addError($e->getMessage());
	}

	return $upgrade->success();
}