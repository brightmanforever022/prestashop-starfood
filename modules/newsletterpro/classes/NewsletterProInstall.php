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

class NewsletterProInstall
{
	public $query;

	public $errors;

	public function __construct()
	{
		$this->module = NewsletterPro::getInstance();
		$this->query  = array();
		$this->errors = array();
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function addError($error)
	{
		$this->errors[] = $error;
	}

	public function createTable($table_name, $sql)
	{
		$this->addQuery('execute', array(
			'table_name' => $table_name,
			'sql'        => $sql,
			'callback'   => 'createTableCallback',
		));
	}

	public function insert($table_name, $sql)
	{
		$this->addQuery('execute', array(
			'table_name' => $table_name,
			'sql'        => $sql,
			'callback'   => 'insertCallback',
		));
	}

	public function delete($table_name, $sql)
	{
		$this->addQuery('execute', array(
			'table_name' => $table_name,
			'sql'        => $sql,
			'callback'   => 'deleteCallback',
		));
	}

	public function update($table_name, $sql)
	{
		$this->addQuery('execute', array(
			'table_name' => $table_name,
			'sql'        => $sql,
			'callback'   => 'updateCallback',
		));
	}

	public function addQuery($name, $data)
	{
		$this->query[$name][] = $data;
	}

	public function getQuery($name)
	{
		if (isset($this->query[$name]))
			return $this->query[$name];
		return false;
	}

	public function queryExists($name)
	{
		return isset($this->query[$name]);
	}

	public function execute()
	{
		if (!empty($this->errors))
			return false;

		// create tables
		if ($this->queryExists('execute'))
		{
			$execute = $this->getQuery('execute');

			foreach ($execute as $data)
				if (!call_user_func_array(array($this, $data['callback']), array($data)))
					return false; // stop the exescution if some of the queries fail
		}

		return true;
	}

	public function displayQuery($display_array = true)
	{
		if ($this->queryExists('execute'))
		{
			$execute = $this->getQuery('execute');
			if ($display_array)
			{
				echo '<pre>';
				print_r($execute);
				echo '</pre>';
			}
			else
			{
				echo '<pre>';
				foreach ($execute as $data)
					echo $data['sql'].'<br><br>';
				echo '</pre>';
			}
		}
	}

	public function createTableCallback($data)
	{
		if (!Db::getInstance()->execute($data['sql']))
		{
			$this->errors[] = sprintf($this->module->l('Cannot create the table "%s".'), _DB_PREFIX_.$data['table_name']);
			return false;
		}
		return true;
	}

	public function insertCallback($data)
	{
		if (!Db::getInstance()->execute($data['sql']))
		{
			$this->errors[] = sprintf($this->module->l('Cannot insert the data into the table "%s".'), _DB_PREFIX_.$data['table_name']);
			return false;
		}
		return true;
	}

	public function deleteCallback($data)
	{
		if (!Db::getInstance()->execute($data['sql']))
		{
			$this->errors[] = sprintf($this->module->l('Cannot delete the records from the table "%s".'), _DB_PREFIX_.$data['table_name']);
			return false;
		}
		return true;
	}

	public function updateCallback($data)
	{
		if (!Db::getInstance()->execute($data['sql']))
		{
			$this->errors[] = sprintf($this->module->l('Cannot dupdate the records from the table "%s".'), _DB_PREFIX_.$data['table_name']);
			return false;
		}
		return true;
	}
}