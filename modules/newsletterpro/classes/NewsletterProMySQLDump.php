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

class NewsletterProMySQLDump
{
	public $tabels;

	public $hex_value;

	public $insert_ignore;

	public $header;

	public function __construct($hex_value = false, $insert_ignore = true)
	{
		$this->tables    = array();
		$this->hex_value = $hex_value;
		$this->insert_ignore = $insert_ignore;
	}

	public function setHeader($header)
	{
		$this->header = $header;
	}

	public function addTable($table_name)
	{
		$this->tables[] = _DB_PREFIX_.$table_name;
	}

	public function saveTableData($table_name)
	{
		$success = array();

		$data = "\n-- start statement\n\n";
		$data .= "-- \n";
		$data .= "-- Dumping data for table `{$table_name}` \n";
		$data .= "-- \n\n";

		$records = Db::getInstance()->executeS('SHOW FIELDS FROM `'.pSQL($table_name).'`');

		if (!$records)
			return false;

		$select_statement = 'SELECT ';

		$insert_statement = 'INSERT '.($this->insert_ignore ? 'IGNORE' : '').' INTO `'.pSQL($table_name).'` (';

		$hex_field = array();
		$i = 0;
		foreach ($records as $key => $record)
		{
			if ($this->hex_value && $this->isTextValue($record['Type']))
			{
				$select_statement .= 'HEX(`'.$record['Field'].'`)';
				$hex_field[$i] = true;
			}
			else
				$select_statement .= '`'.$record['Field'].'`';

			$insert_statement .= '`'.$record['Field'].'`';
			$insert_statement .= ', ';
			$select_statement .= ', ';

			$i++;
		}

		$insert_statement = Tools::substr($insert_statement, 0, -2).') VALUES';
		$select_statement = Tools::substr($select_statement, 0, -2).' FROM `'.pSQL($table_name).'`';

		$records = Db::getInstance()->executeS($select_statement);
		$num_rows = count($records);

		if ($num_rows > 0)
		{
			$data .= $insert_statement;

			foreach ($records as $key => $record)
			{
				$data .= ' (';
				$j = 0;
				foreach ($record as $value)
				{
					if (isset($hex_field[$j]) && $hex_field[$j] && (Tools::strlen($value) > 0))
						$data .= '0x'.$value;
					else if (is_null($value))
						$data .= 'NULL';
					else
						$data .= "'".str_replace('\"', '"', addcslashes($value, "\x00\n\r\\'\"\x1a"))."'";
					$data .= ',';

					$j++;
				}

				$data = Tools::substr($data, 0, -1).')';
				$data .= ($key < ($num_rows - 1)) ? ',' : ';';
				$data .= "\n";

				if (Tools::strlen($data) > 1048576)
				{
					$success[] = $this->saveToFile($data);
					$data = '';
				}
			}
			$data .= "\n-- end statement\n\n";

			$success[] = $this->saveToFile($data);
		}

		return !in_array(false, $success);
	}

	public function save($filename)
	{
		$success = array();

		$this->file = fopen($filename, 'w');

		if (isset($this->header))
			$success[] = $this->saveToFile($this->header);

		foreach ($this->tables as $table_name)
			$success[] = $this->saveTableData($table_name);

		fclose($this->file);
		return !in_array(false, $success);
	}

	public function saveToFile($data)
	{
		return fwrite($this->file, $data);
	}

	public function isTextValue($field_type)
	{
		switch ($field_type)
		{
			case 'tinytext':
			case 'text':
			case 'mediumtext':
			case 'longtext':
			case 'binary':
			case 'varbinary':
			case 'tinyblob':
			case 'blob':
			case 'mediumblob':
			case 'longblob':
				return true;
			default:
				return false;
		}
	}
}