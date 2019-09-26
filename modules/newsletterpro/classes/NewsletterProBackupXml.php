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

class NewsletterProBackupXml
{
	public $load_configuration;

	public $load_configuration_campaign;

	public $load_configuration_shop;

	public $load_hooks;

	public $load_tables;

	public $load_data;

	public $load_xml;

	public $module;

	public $headers;

	public $tables;

	public $hex;

	public $default_path;

	const PHP_HEADER = '<?php exit; ?>';

	public function __construct($default_path = null)
	{
		$this->module    = NewsletterPro::getInstance();
		$this->headers   = array();

		if (!isset($default_path))
			$this->default_path = self::path();
		else
			$this->default_path = $default_path;
	}

	/**
	 * Get the backup folder dirname
	 * @return string Folder dirname
	 */
	public static function path()
	{
		return realpath(dirname(__FILE__).'/../backup');
	}

	/**
	 * Format pathname
	 * Ex: subscription/file name 123 -> subscription/file_name_123.php
	 * @param  string  $path_name Pathname
	 * @param  boolean $timestamp Append a timestamp at the end of the filename
	 * @return string             Formated path name
	 */
	public static function formatName($path_name, $timestamp = false)
	{
		return preg_replace('/\s+/', '_', trim(Tools::strtolower($path_name))).($timestamp ? '_'.time() : '').'.php';
	}

	/**
	 * Check if the path name exists
	 * @param  string $path_name Pathname
	 * @return boolean
	 */
	public static function pathNameExists($path_name, $md5 = false)
	{
		if ($md5)
			$path_name = self::formatMD5($path_name);

		return file_exists(self::path().'/'.$path_name);
	}

	/**
	 * Get the path dirname
	 * @param  string $path_name Pathname
	 * @return string
	 */
	public static function pathName($path_name)
	{
		return self::path().'/'.$path_name;
	}

	/**
	 * Setup the backup header information
	 * @param string $name  Header hey
	 * @param string $value Header value
	 */
	public function addHeader($name, $value)
	{
		$this->headers[$name] = $value;
	}

	/**
	 * Add tables for backup creation
	 * @param  array  $tables Tables without the prefix
	 */
	public function create(array $tables, $hex = false)
	{
		$this->hex = $hex;
		$this->tables = $tables;
		$tables_header = array();
		foreach ($this->tables as $table_name)
			$tables_header[] = _DB_PREFIX_.$table_name;

		$this->addHeader('tables', implode(',', $tables_header));
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

	/**
	 * Save the backup xml
	 * @param  string  $path_name Pathname
	 * @param  boolean $format    Format the name
	 * @param  boolean $md5       Md5 format
	 * @return boolean            Success
	 */
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

		$this->addHeader('filename', $filename);
		$this->addHeader('date', date('Y-m-d H:i:s', time()));
		$this->addHeader('module_version', $this->module->version);
		$this->addHeader('name', $name_display);

		if (!isset($this->headers['configuration_shop']))
			$this->addHeader('configuration_shop', serialize(array()));

		if (!isset($this->headers['configuration']))
			$this->addHeader('configuration', serialize(array()));

		if (!isset($this->headers['configuration_campaign']))
			$this->addHeader('configuration_campaign', null);

		if (!isset($this->headers['hooks']))
			$this->addHeader('hooks', serialize(array()));

		$mysql = new NewsletterProMySQLDumpXml($this->hex);
		$mysql->prependString(self::PHP_HEADER."\n");
		$mysql->addTables($this->tables);
		$mysql->setHeader($this->headers);

		return $mysql->save($this->default_path.'/'.$path_filename);
	}

	/**
	 * Det the backup files list
	 * @param  string $path Backup path
	 * @return array
	 */
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
				$header = self::getHeaderFromFilename($file->getPathname());

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

	/**
	 * Cet the header as array from a file
	 * @param  string $filename Filename path
	 * @return array
	 */
	public static function getHeaderFromFilename($filename)
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
					$end_string = '</header>';
					$end = strpos($working_data, $end_string);

					if ($end !== false)
						break;

					flush();
				}
				fclose($file);

				$start_string = '<header>';
				$start = strpos($working_data, $start_string);
				if ($start !== false)
				{
					$working_data = Tools::substr($working_data, $start);

					$end_string = '</header>';
					$end = strpos($working_data, $end_string);
					if ($end !== false)
					{
						$header_xml = Tools::substr($working_data, 0, $end + Tools::strlen($end_string));

						libxml_use_internal_errors(true);
						$xml_obj = simplexml_load_string($header_xml);

						if ($xml_obj === false)
						{
							$xml_errors = '';
							foreach (libxml_get_errors() as $error)
								$xml_errors .= NewsletterProTools::displayXMLError($error, $xml_obj)."\n";
							libxml_clear_errors();
							// skip error if exits
						}
						else
						{
							foreach ($xml_obj as $header_name => $value)
								$header[$header_name] = trim((string)$value);
						}
					}
				}
			}
		}
		return $header;
	}

	/**
	 * Load the backup from a file
	 * @param  string $path_name Pathname
	 */
	public function load($path_name, $is_file = true)
	{
		if ($is_file)
		{
			$full_path = isset($this->default_path) ? $this->default_path.'/'.$path_name : self::pathName($path_name);

			if (!file_exists($full_path) || !is_file($full_path))
				throw new Exception(sprintf($this->module->l('The filename "%s" does not exists anymore.'), basename($path_name)));

			$this->load_data = @Tools::file_get_contents($full_path);

			if ($this->load_data === false)
				throw new Exception(sprintf($this->module->l('The filename "%s" is not readable. Please check the CHMOD permissions.'), basename($path_name)));
		}
		else
			$this->load_data = $path_name; // is this case the $path_name is a string content

		// remove the php exit
		$this->removePHPHeader();

		libxml_use_internal_errors(true);
		$this->load_xml = simplexml_load_string($this->load_data);

		if ($this->load_xml === false)
		{
			$xml_errors = '';
			foreach (libxml_get_errors() as $error)
				$xml_errors .= NewsletterProTools::displayXMLError($error, $this->load_xml)."\n";
			libxml_clear_errors();

			throw new Exception($xml_errors);
		}

		$header_xml = $this->load_xml->header;

		$this->load_tables = array();
		if (isset($header_xml->tables))
			$this->load_tables = explode(',', (string)$header_xml->tables);

		$this->load_configuration = array();
		if (isset($header_xml->configuration))
			$this->load_configuration = NewsletterProTools::unSerialize(trim((string)$header_xml->configuration));

		$this->load_configuration_campaign = null;
		if (isset($header_xml->configuration_campaign))
			$this->load_configuration_campaign = $header_xml->configuration_campaign;

		$this->load_configuration_shop = array();
		if (isset($header_xml->configuration_shop))
			$this->load_configuration_shop = NewsletterProTools::unSerialize(trim((string)$header_xml->configuration_shop));

		$this->load_hooks = array();
		if (isset($header_xml->hooks))
			$this->load_hooks = NewsletterProTools::unSerialize(trim((string)$header_xml->hooks));
	}

	/**
	 * Remove the <?php exit; ?> from the begining of the line from the load_data
	 */
	private function removePHPHeader()
	{
		$start = strpos($this->load_data, self::PHP_HEADER);
		if ($start !== false)
			$this->load_data = trim(Tools::substr($this->load_data, Tools::strlen(self::PHP_HEADER)));
	}

	/**
	 * Execute a backup after is loaded
	 * @return boolean Reutrn true if success
	 */
	public function execute()
	{
		$success = array();

		$success[] = NewsletterPro::executeHooksByInfo($this->load_hooks);

		$configuration = array_merge($this->module->configuration, $this->load_configuration);
		$success[] = Configuration::updateValue('NEWSLETTER_PRO', serialize($configuration), false, 0, 0);

		$success[] = Configuration::updateValue( 'NEWSLETTER_PRO_CAMPAIGN', $this->load_configuration_campaign, false, 0, 0 );

		$success[] = NewsletterProConfigurationShop::replaceShopsCondiguration($this->load_configuration_shop);

		$xml_to_sql = new NewsletterProXMLToSQL($this->load_xml);
		foreach ($xml_to_sql->getSQL() as $table_name => $sql_array)
		{
			$success[] = $this->emptyTable($table_name);

			foreach ($sql_array as $sql)
				$success[] = @Db::getInstance()->execute($sql);
		}

		return !in_array(false, $success);
	}

	/**
	 * Get the loaded xml
	 * @return object
	 */
	public function getXML()
	{
		return $this->load_xml;
	}

	/**
	 * Set the load xml object
	 * @param object $xml
	 */
	public function setXML($xml)
	{
		$this->load_xml = $xml;
	}

	public function emptyTable($table_name)
	{
		return Db::getInstance()->execute('DELETE FROM `'.pSQL($table_name).'` WHERE 1');
	}
}