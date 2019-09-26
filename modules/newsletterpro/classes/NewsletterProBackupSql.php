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

class NewsletterProBackupSql
{
	public $load_configuration;

	public $load_configuration_shop;

	public $load_tables;

	public $load_data;

	public $module;

	public $header;

	public function __construct()
	{
		$this->module = NewsletterPro::getInstance();
		$this->header = array();
	}

	public static function path()
	{
		return realpath(dirname(__FILE__).'/../backup');
	}

	public static function formatName($path_name, $timestamp = false)
	{
		return preg_replace('/\s+/', '_', trim(Tools::strtolower($path_name))).($timestamp ? '_'.time() : '').'.php';
	}

	public static function pathNameExists($path_name, $md5 = false)
	{
		if ($md5)
			$path_name = self::formatMD5($path_name);

		return file_exists(self::path().'/'.$path_name);
	}

	public static function pathName($path_name)
	{
		return self::path().'/'.$path_name;
	}

	public static function formatMD5($path_name)
	{
		$info_name      = pathinfo($path_name, PATHINFO_FILENAME);
		$info_extension = pathinfo($path_name, PATHINFO_EXTENSION);
		$info_dirname   = pathinfo($path_name, PATHINFO_DIRNAME);

		$name = md5($info_name).($info_extension ? '.'.$info_extension : '' );
		$path_name = $info_dirname.'/'.$name;
		return $path_name;
	}

	public function save($path_name, $format = true, $md5 = true)
	{
		$name = basename($path_name);
		$name_display = $name;

		if ($md5)
			$path_name = self::formatMD5($path_name);

		if ($format)
			$path_filename = self::formatName($path_name);
		else
			$path_filename = $path_name;

		$filename = basename($path_filename);

		$this->setHeader('filename', $filename);
		$this->setHeader('date', date('Y-m-d H:i:s', time()));
		$this->setHeader('module_version', $this->module->version);
		$this->setHeader('name', $name_display);

		if (!isset($this->header['configuration_shop']))
			$this->setHeader('configuration_shop', serialize(array()));

		if (!isset($this->header['configuration']))
			$this->setHeader('configuration', serialize(array()));

		$header = '<?php exit; ?>'."\n";
		$header .= $this->displayHeader();

		$mysql = new NewsletterProMySQLDump(false);
		$mysql->setHeader($header);

		foreach ($this->tables as $table_name)
			$mysql->addTable($table_name);

		return $mysql->save(self::path().'/'.$path_filename);
	}

	public function setHeader($name, $value)
	{
		$this->header[$name] = $value;
	}

	public function displayHeader()
	{
		$header = '';
		$header .= "-- begin header\n";
		foreach (array_reverse($this->header) as $name => $value)
			$header .= '-- '.$name.': '.$value."\n";
		$header .= "-- end header\n";
		$header .= "\n";
		return $header;
	}

	public function create(array $tables)
	{
		$this->tables = $tables;
		$tables_header = array();
		foreach ($this->tables as $table_name)
			$tables_header[] = _DB_PREFIX_.$table_name;
		$this->setHeader('tables', implode(',', $tables_header));
	}

	public static function getList($path = '')
	{
		$dirs = new DirectoryIterator(self::path().'/'.$path);
		$files = new RegexIterator($dirs, '/.php$/', RecursiveRegexIterator::MATCH);
		$results = array();

		foreach ($files as $file)
		{
			$file_basename = '';
			if (method_exists($file, 'getBasename'))
				$file_basename = $file->getBasename();
			else
				$file_basename = basename($file->getFilename());

			if ($file_basename !== 'index.php')
			{
				$header = self::getLoadDataHeaderFilename($file->getPathname());

				$header_name = 'Undefined';
				if (isset($header['name']))
					$header_name = $header['name'];

				$item = array();
				$item['m_date']       = date('Y-m-d H:i:s', $file->getMTime());
				$item['name']         = $file_basename;
				$item['name_display'] = Tools::ucfirst(str_replace('_', ' ', preg_replace('/\.php$/', '', $header_name)));
				$item['filename']     = $file->getPathname();
				$item['module_version']	  = (isset($header['module_version']) ? $header['module_version'] : '0.00');
				$results[] = $item;
			}
		}

		return $results;
	}

	public static function getLoadDataHeaderFilename($filename)
	{
		$header = array();
		if (file_exists($filename))
		{
			if ($file = fopen($filename, 'r'))
			{
				$working_data = '';
				while (!feof($file) && (connection_status() == 0))
				{
					$working_data_temp = fread($file, 1024);
					$working_data .= $working_data_temp;
					$start_string = '-- end header';
					$start = strpos($working_data, $start_string);

					if ($start !== false)
						break;

					flush();
				}

				fclose($file);

				$header = self::getLoadDataHeader($working_data);
			}
		}
		return $header;
	}

	public function load($path_name)
	{
		if (!self::pathNameExists($path_name))
			throw new Exception(sprintf($this->module->l('The filename "%s" does not exists anymore.'), basename($path_name)));

		$this->load_data = @Tools::file_get_contents(self::pathName($path_name));

		if ($this->load_data === false)
			throw new Exception(sprintf($this->module->l('The filename "%s" is not readable. Please check the CHMOD permissions.'), basename($path_name)));

		// if the file is php will remove the exit statement
		$start_str = '<?php exit; ?>';
		$start = strpos($this->load_data, $start_str);
		if ($start !== false)
			$this->load_data = trim(Tools::substr($this->load_data, Tools::strlen($start_str)));

		$header = self::getLoadDataHeader($this->getLoadDataHeaderString());

		$this->load_tables = array();
		if (isset($header['tables']))
			$this->load_tables = explode(',', $header['tables']);

		$this->load_configuration_shop = array();
		if (isset($header['configuration_shop']))
			$this->load_configuration_shop = NewsletterProTools::unSerialize($header['configuration_shop']);

		$this->load_configuration = array();
		if (isset($header['configuration']))
			$this->load_configuration = NewsletterProTools::unSerialize($header['configuration']);
	}

	public function execute()
	{
		if (!$this->emptyTables())
			throw new Exception($this->module->l('An error occurred when deleting the current data.'));

		if (!NewsletterProConfigurationShop::replaceShopsCondiguration($this->load_configuration_shop))
			throw new Exception($this->module->l('An error occurred when updating the configuration.'));

		return Db::getInstance()->execute($this->load_data);
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

	public function emptyTables()
	{
		$success = array();
		foreach ($this->load_tables as $table_name)
			$success[] = Db::getInstance()->execute('DELETE FROM `'.pSQL($table_name).'` WHERE 1');
		return !in_array(false, $success);
	}

	public function getLoadDataHeaderString()
	{
		$header = '';

		$start_match = '-- begin header';
		$end_match = '-- end header';
		$start = strpos($this->load_data, $start_match);
		$end = strpos($this->load_data, $end_match);
		if ($start !== false && $end !== false)
			$header = trim(Tools::substr($this->load_data, ($start + Tools::strlen($start_match)), ($end - Tools::strlen($start_match))));

		return $header;
	}

	public static function getLoadDataHeader($header_str)
	{
		$header = array();

		$header_lines = preg_split('/--\s/m', $header_str);

		foreach ($header_lines as $line)
		{
			if (preg_match('/^(?P<key>\w+):\s(?P<value>.*)$/', $line, $match))
				$header[$match['key']] = trim($match['value']);
		}

		return $header;
	}
}