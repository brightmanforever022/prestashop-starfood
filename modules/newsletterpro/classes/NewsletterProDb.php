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

class NewsletterProDb
{
	public static function getInstance($master = true)
	{
		$db = Db::getInstance($master);
		$db->disconnect();
		$db->connect();
		return $db;
	}

	public static function refresh()
	{
		$db = Db::getInstance();
		$db->disconnect();
		$db->connect();
	}

	public static function clear()
	{
		return Db::getInstance()->execute('RESET QUERY CACHE');
	}
}