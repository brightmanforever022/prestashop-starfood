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

class NewsletterProXMLToSQL
{
	public $db_tables;

	public $xml;

	public $sql_array;

	public $sql_tables;

	const SQL_SIZE = 32768; /* 1024*32 bytes */

	public function __construct(SimpleXMLElement $xml)
	{
		$this->module     = NewsletterPro::getInstance();
		$this->sql_array  = array();
		$this->sql_tables = array();
		$this->xml        = $xml;
		$this->createSql();
	}

	public function createSql()
	{
		$this->setDbTables();
		$this->buildInsertStatement();
	}

	private function setDbTables()
	{
		$this->db_tables  = array();

		foreach (explode(',', $this->xml->header->tables) as $table_name)
			if ($this->tableExists($table_name))
				$this->db_tables[] = $table_name;
	}

	private function buildInsertStatement()
	{
		$tables = $this->xml->tables->table;
		foreach ($tables as $table)
		{
			$this->emptyBuffer();

			$table_attributes = $table->attributes();
			$table_name = (string)$table_attributes['name'];
			$this->current_table_name = $table_name;

			if (in_array($table_name, $this->db_tables))
			{

				$xml_records = $this->getXMLRecords($table->records->record);

				if (!empty($xml_records))
				{
					$db_fields = array_keys($this->getTableFields($table_name));

					reset($xml_records);
					// get the fields for the record
					$xml_fields    = array_keys($xml_records[key($xml_records)]);
					$fields        = array_intersect($db_fields, $xml_fields);
					$header_fields = $this->getXMLHeaderFields($table->fields->field);

					$sql_insert = 'INSERT IGNORE INTO `'.$table_name.'` '."\n\t".'(`'.implode('`,`', $fields).'`) '."\n\tVALUES\n";
					$this->appendSQL($sql_insert);

					$sql_temp = '';
					foreach ($xml_records as $record)
					{

						$sql_temp .= "\t(";
						foreach ($record as $fields_name => $value)
						{
							if (isset($header_fields[$fields_name]))
							{
								$current_field_header = $header_fields[$fields_name];

								if ((bool)$current_field_header['allow_null'] && (Tools::strtoupper($value) == 'NULL'))
									$sql_temp .= 'NULL';
								else if ((bool)$current_field_header['is_hex'])
									$sql_temp .= (!empty($value) ? $value : '\'\'');
								else
									$sql_temp .= '\''.str_replace('\"', '"', addcslashes($value, "\x00\n\r\\'\"\x1a")).'\'';

								$end_trim = ', ';
								$sql_temp .= $end_trim;
							}
						}
						$sql_temp = rtrim($sql_temp, $end_trim);
						$sql_temp .= "),\n";

						if (NewsletterProTools::strSize($sql_temp) > self::SQL_SIZE)
						{
							$this->appendSQL(rtrim($sql_temp, "\n,"));
							$this->appendSQL(";\n\n");
							$this->addSQLByTable($table_name, $this->getSQLEmpty());
							$sql_temp = '';
							$this->appendSQL($sql_insert);
						}
					}

					if (!empty($sql_temp))
					{
						$this->appendSQL(rtrim($sql_temp, "\n,"));
						$this->appendSQL(";\n\n");

						// add sql by table
						$this->addSQLByTable($table_name, $this->getSQLEmpty());
					}
				}
			}
		}
	}

	private function appendSQL($value)
	{
		$this->sql_array[] = $value;
	}

	private function addSQLByTable($table_name, $sql)
	{
		$this->sql_tables[$table_name][] = $sql;
	}

	public function getSQLEmpty()
	{
		$sql = $this->getSQLBuffer();
		$this->sql_array = array();
		return $sql;
	}

	public function emptyBuffer()
	{
		$this->sql_array = array();
	}

	public function getSQLBuffer()
	{
		return implode('', $this->sql_array);
	}

	public function getSQL($table_name = null)
	{
		if (isset($table_name))
			return $this->sql_tables[$table_name];
		return $this->sql_tables;
	}

	private function getXMLHeaderFields($xml_fields, $compare_with_db_fields = true)
	{
		$fields = array();

		foreach ($xml_fields as $field)
		{
			$field_attributes = $field->attributes();

			$f_array = array();

			foreach ($field_attributes as $key => $value)
				$f_array[$key] = (string)$value;

			$fields[(string)$field_attributes['name']] = $f_array;
		}

		if ($compare_with_db_fields)
		{
			if (!isset($this->current_table_name))
				throw new Exception(sprintf($this->module->l('This function cannot be used because the variable "%s" is not defined.'), '$this->current_table_name'));

			$xml_header_fields = $fields;
			$db_fields = array_keys($this->getTableFields($this->current_table_name));
			$header_fields_availalbe = array_intersect($db_fields, array_keys($xml_header_fields));
			$header_fields = array_intersect_key($xml_header_fields, array_fill_keys($header_fields_availalbe, null));
			return $header_fields;
		}

		return $fields;
	}

	private function getXMLRecords($xml_records)
	{
		$records = array();
		if ($xml_records)
		{
			foreach ($xml_records as $record)
			{
				$item = array();
				foreach ($record as $fields_name => $value)
					$item[$fields_name] = (string)$value;

				$records[] = $item;
			}
		}
		return $records;
	}

	public function tableExists($table_name)
	{
		return count(Db::getInstance()->executeS('SHOW TABLES LIKE "'.pSQL($table_name).'"'));
	}

	public function getTableFields($table_name)
	{
		$fields = array();
		$result = Db::getInstance()->executeS('SHOW FIELDS FROM `'.pSQL($table_name).'`');
		foreach ($result as $value)
			$fields[$value['Field']] = $value['Default'];

		return $fields;
	}
}