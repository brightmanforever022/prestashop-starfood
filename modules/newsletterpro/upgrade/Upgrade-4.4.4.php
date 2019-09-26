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

function upgrade_module_4_4_4($module)
{
	$upgrade = $module->upgrade;

	$upgrade->updateConfiguration('SHOW_CLEAR_CACHE', 1);

	$old_mails_dir = $module->dir_location.'mails/';
	$new_mails_dir = $module->dir_location.'mail_templates/';
	$temp_mails_dir = $module->dir_location.'mail_templates_temp/';

	if (file_exists($old_mails_dir) && is_writable($old_mails_dir) && file_exists($new_mails_dir) && is_writable($new_mails_dir)) {
		@NewsletterProTools::recurseCopy($new_mails_dir, $old_mails_dir);

		if (@rename($new_mails_dir, $temp_mails_dir)) {

			if (@rename($old_mails_dir, $new_mails_dir)) {
				// for security
				if (strpos(str_replace('\\', '/', $temp_mails_dir), str_replace('\\', '/', '/modules/newsletterpro/mail_templates_temp')) !== false) {
					@NewsletterProTools::deleteDirAndFiles($temp_mails_dir);
				}
			} else {
				@rename($temp_mails_dir, $new_mails_dir);
			}
		}
	}

	return $upgrade->success();
}