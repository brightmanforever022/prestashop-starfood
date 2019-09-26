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

class NewsletterProMySQLDumpXml
{
	public $tables;

	public $hex_value;

	public $insert_ignore;

	public $headers;

	public $xml;

	public $filename;

	public $prepend_string;

	public function __construct($hex_value = false, $insert_ignore = true)
	{
		$this->headers        = array();
		$this->tables         = array();
		$this->prepend_string = array();
		$this->hex_value      = $hex_value;
		$this->insert_ignore  = $insert_ignore;
		$this->xml            = new NewsletterProCreateXML('database');

		$this->xml->attribute($this->xml->root, 'name', _DB_NAME_);
	}

	/**
	 * Add header
	 * @param string $name  Header name
	 * @param string $value Header value
	 */
	public function addHeader($name, $value)
	{
		$this->headers[$name] = $value;
	}

	/**
	 * Get header by name
	 * @param  string $name Header name
	 * @return string/false Header value
	 */
	public function getHeaderByName($name)
	{
		if (isset($this->headers[$name]))
			return $this->headers[$name];
		return false;
	}

	/**
	 * Add tables
	 * @param array/string $table_name Table name 
	 */
	public function addTables($table_name)
	{
		if (is_array($table_name))
		{
			foreach ($table_name as $table_n)
				$this->tables[] = _DB_PREFIX_.$table_n;
		}
		else
			$this->tables[] = _DB_PREFIX_.$table_name;
	}

	/**
	 * Get all tables
	 * @return  array
	 */
	public function getTables()
	{
		return $this->tables;
	}

	public function setHeader(array $headers)
	{
		$this->headers = $headers;
	}

	public function prependString($string)
	{
		$this->prepend_string[] = $string;
	}

	public function save($filename)
	{
		$success = array();

		$this->filename = $filename;

		$this->xml->header = $this->xml->create($this->xml->root, 'header');
		foreach ($this->headers as $name => $value)
			$this->xml->append($this->xml->header, $name, $value);

		$this->xml->tables = $this->xml->create($this->xml->root, 'tables');

		if ($this->file = fopen($filename, 'w'))
		{
			$success[] = $this->saveToFile(implode('', $this->prepend_string));

			foreach ($this->tables as $table_name)
				if ($this->tableExists($table_name))
					$success[] = $this->createTable($table_name);

			$success[] = $this->saveToFile($this->xml->getContent($this->xml->root));
			@fclose($this->file);
		}
		else
			throw new Exception($this->module->l('Cannot create the backup. Please check the CHMOD permissions.'));

		return !in_array(false, $success);
	}

	public function tableExists($table_name)
	{
		return count(Db::getInstance()->executeS('SHOW TABLES LIKE "'.pSQL($table_name).'"'));
	}

	public function saveToFile($data)
	{
		return @fwrite($this->file, $data);
	}

	public function createTable($table_name)
	{
		$success = array();

		$records = Db::getInstance()->executeS('SHOW FIELDS FROM `'.pSQL($table_name).'`');

		if (!$records)
			return false;

		$table = $this->xml->create($this->xml->tables, 'table', 'name', pSQL($table_name));

		$fields = $this->xml->append($table, 'fields');

		$select_statement = 'SELECT ';
		$hex_field = array();
		$i = 0;
		foreach ($records as $record)
		{
			$is_hex = ($this->hex_value && $this->isTextValue($record['Type']));

			if ($is_hex)
			{
				$select_statement .= 'HEX(`'.$record['Field'].'`) AS '.$record['Field'].'';
				$hex_field[$i] = true;
			}
			else
				$select_statement .= '`'.$record['Field'].'`';

			$field = $this->xml->append($fields, 'field');
			$this->xml->attribute($field, 'name', $record['Field']);

			$type = $this->getFieldType($record['Type']);

			$this->xml->attribute($field, 'datatype', $type['type']);
			$this->xml->attribute($field, 'length_set', $type['size']);
			$this->xml->attribute($field, 'unsigned', $type['unsigned']);
			$this->xml->attribute($field, 'allow_null', $this->getFieldNull($record['Null']));
			$this->xml->attribute($field, 'key', $this->getFieldKey($record['Key']));
			$this->xml->attribute($field, 'default', $this->getFieldDefault($record['Default']));
			$this->xml->attribute($field, 'default_is_null', $this->getFieldIsDefault($record['Default']));
			$this->xml->attribute($field, 'extra', $this->getFieldExtra($record['Extra']));
			$this->xml->attribute($field, 'is_hex', (int)$is_hex);

			$select_statement .= ', ';

			$i++;
		}

		$select_statement = Tools::substr($select_statement, 0, -2).' FROM `'.pSQL($table_name).'`';
		$records = Db::getInstance()->executeS($select_statement);

		$num_rows = count($records);

		if ($num_rows > 0)
		{
			$records_xml = $this->xml->append($table, 'records');
			foreach ($records as $record)
			{
				$record_xml = $this->xml->append($records_xml, 'record');

				$j = 0;
				foreach ($record as $field_name => $value)
				{
					if (isset($hex_field[$j]) && $hex_field[$j] && (Tools::strlen($value) > 0))
						$field_value = '0x'.$value;
					else if (is_null($value))
						$field_value = 'NULL';
					else
						$field_value = $value;

					$this->xml->append($record_xml, $field_name, $field_value);

					$j++;
				}
			}
		}
		return !in_array(false, $success);
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

	public function getFieldType($value)
	{
		preg_match('/(?P<type>\w+)(\((?P<size>.*)\))?\s?(?P<unsigned>\w+)?/i', $value, $match);

		return array(
			'type'     => isset($match['type']) ? $match['type'] : false,
			'size'     => isset($match['size']) ? $match['size'] : 0,
			'unsigned' => isset($match['unsigned']) ? $match['unsigned'] : 0,
		);
	}

	public function getFieldNull($value)
	{
		switch (Tools::strtolower($value))
		{
			case 'yes':
				return 1;

			case 'no':
			default:
				return 0;
		}
		return 0;
	}

	public function getFieldKey($value)
	{
		switch (Tools::strtolower($value))
		{
			case 'pri':
				return 'PRI';

			case 'uni':
				return 'UNI';

			case 'mul':
				return 'MUL';

			default:
				return '';
		}
		return '';
	}

	public function getFieldExtra($value)
	{
		switch (Tools::strtolower($value))
		{
			case 'auto_increment':
				return 'auto_increment';

			default:
				return '';
		}
		return '';
	}

	public function getFieldDefault($value)
	{
		if (is_null($value))
			return null;
		else
			return $value;
	}

	public function getFieldIsDefault($value)
	{
		return (int)is_null($value);
	}
}