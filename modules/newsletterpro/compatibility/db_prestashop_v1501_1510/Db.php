<?php
/**
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2011 PrestaShop SA
* @version   Release: $Revision: 8668 $
* @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*/

abstract class Db extends DbCore
{
	const INSERT = 1;
	const INSERT_IGNORE = 2;
	const REPLACE = 3;

	public function insert($table, $data, $null_values = false, $use_cache = true, $type = Db::INSERT, $add_prefix = true)
	{
		$this->refreshNewsletterPro();
		$keys_stringified = null;

		if (!$data && !$null_values)
			return true;

		if ($add_prefix)
			$table = _DB_PREFIX_.$table;

		if ($type == Db::INSERT)
			$insert_keyword = 'INSERT';
		else if ($type == Db::INSERT_IGNORE)
			$insert_keyword = 'INSERT IGNORE';
		else if ($type == Db::REPLACE)
			$insert_keyword = 'REPLACE';
		else
			throw new PrestaShopDatabaseException('Bad keyword, must be Db::INSERT or Db::INSERT_IGNORE or Db::REPLACE');

		// Check if $data is a list of row
		$current = current($data);
		if (!is_array($current) || isset($current['type']))
			$data = array($data);

		$keys = array();
		$values_stringified = array();
		foreach ($data as $row_data)
		{
			$values = array();
			foreach ($row_data as $key => $value)
			{
				if (isset($keys_stringified))
				{
					// Check if row array mapping are the same
					if (!in_array("`$key`", $keys))
						throw new PrestaShopDatabaseException('Keys form $data subarray don\'t match');
				}
				else
					$keys[] = "`$key`";

				if (!is_array($value))
					$value = array('type' => 'text', 'value' => $value);
				if ($value['type'] == 'sql')
					$values[] = $value['value'];
				else
					$values[] = $null_values && ($value['value'] === '' || is_null($value['value'])) ? 'NULL' : "'{$value['value']}'";
			}
			$keys_stringified = implode(', ', $keys);
			$values_stringified[] = '('.implode(', ', $values).')';
		}

		$sql = $insert_keyword.' INTO `'.$table.'` ('.$keys_stringified.') VALUES '.implode(', ', $values_stringified);
		return (bool)$this->q($sql, $use_cache);
	}

	public function update($table, $data, $where = '', $limit = 0, $null_values = false, $use_cache = true, $add_prefix = true)
	{
		$this->refreshNewsletterPro();
		if (!$data)
			return true;

		if ($add_prefix)
			$table = _DB_PREFIX_.$table;

		$sql = 'UPDATE `'.$table.'` SET ';
		foreach ($data as $key => $value)
		{
			if (!is_array($value))
				$value = array('type' => 'text', 'value' => $value);
			if ($value['type'] == 'sql')
				$sql .= "`$key` = {$value['value']},";
			else
				$sql .= ($null_values && ($value['value'] === '' || is_null($value['value']))) ? "`$key` = NULL," : "`$key` = '{$value['value']}',";
		}

		$sql = rtrim($sql, ',');
		if ($where)
			$sql .= ' WHERE '.$where;
		if ($limit)
			$sql .= ' LIMIT '.(int)$limit;
		return (bool)$this->q($sql, $use_cache);
	}

	public function delete($table, $where = '', $limit = 0, $use_cache = true, $add_prefix = true)
	{
		$this->refreshNewsletterPro();
		if (_DB_PREFIX_ && !preg_match('#^'._DB_PREFIX_.'#i', $table) && $add_prefix)
			$table = _DB_PREFIX_.$table;

		$this->result = false;
		$sql = 'DELETE FROM `'.bqSQL($table).'`'.($where ? ' WHERE '.$where : '').($limit ? ' LIMIT '.(int)$limit : '');
		$res = $this->query($sql);
		if ($use_cache && $this->is_cache_enabled)
			Cache::getInstance()->deleteQuery($sql);
		return (bool)$res;
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