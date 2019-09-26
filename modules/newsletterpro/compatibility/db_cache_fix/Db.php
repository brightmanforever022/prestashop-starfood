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

abstract class Db extends DbCore
{
	public function update($table, $data, $where = '', $limit = 0, $null_values = false, $use_cache = true, $add_prefix = true)
	{
		$this->refreshNewsletterPro();
		if (method_exists('DbCore', 'update'))
			return parent::update($table, $data, $where, $limit, $null_values, $use_cache, $add_prefix);

	}

	public function insert($table, $data, $null_values = false, $use_cache = true, $type = Db::INSERT, $add_prefix = true)
	{
		$this->refreshNewsletterPro();
		if (method_exists('DbCore', 'insert'))
			return parent::insert($table, $data, $null_values, $use_cache, $type, $add_prefix);
	}

	public function delete($table, $where = '', $limit = 0, $use_cache = true, $add_prefix = true)
	{
		$this->refreshNewsletterPro();
		if (method_exists('DbCore', 'delete'))
			return parent::delete($table, $where, $limit, $use_cache, $add_prefix);
	}

	public function query($sql)
	{
		$this->refreshNewsletterPro();
		if (method_exists('DbCore', 'query'))
			return parent::query($sql);
	}

	public function execute($sql, $use_cache = true)
	{
		$this->refreshNewsletterPro();
		if (method_exists('DbCore', 'execute'))
			return parent::execute($sql, $use_cache);
	}

	public function executeS($sql, $array = true, $use_cache = true)
	{
		$this->refreshNewsletterPro();
		if (method_exists('DbCore', 'executeS'))
			return parent::executeS($sql, $array, $use_cache);
	}

	public function refreshNewsletterPro()
	{
		$this->disconnect();
		$this->connect();
	}
}