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

interface NewsletterProMailInterface
{
	public static function newInstance($id = null);
	public function setFromName($name);
	public function addError($error);
	public function getErrors();
	public function hasErrors();
	public function send($subject, $template, $to);
	public function sendForward($data, $type, $from, $sleep = 1);
	public function addSuccessFwd($email);
	public function getSuccessFwdCount();
	public function getTemplate($email, $data, $type);
	public static function getEmailInfo($email);
	public static function getInstance($connection = array());
	public static function getDefaultSMTP();
	public static function getDefaultMail();
}