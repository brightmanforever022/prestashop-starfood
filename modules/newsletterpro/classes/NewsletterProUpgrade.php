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

class NewsletterProUpgrade
{
	public $context;

	public $module;

	public $errors = array();

	public function __construct()
	{
		$this->context = Context::getContext();
		$this->module  = NewsletterPro::getInstance();
	}

	/**
	 * Update the database configuration
	 * If the configuration not exist the value will be created
	 * @param  string $name  Condiguration name
	 * @param  string $value Configuration value
	 * @return boolean       Return true if success
	 */
	public function updateConfiguration($name, $value)
	{
		try
		{
			if (!$this->module->writeConfiguration($name, $value))
			{
				$this->addError(sprintf($this->module->l('Cannot update the configuration with the name "%s".'), $name));
				return false;
			}
		}
		catch(Exception $e)
		{
			$this->addError($e->getMessage());
			return false;
		}

		return true;
	}

	public function deleteConfiguration($name)
	{
		try
		{
			if (!$this->module->deleteConfiguration($name))
			{
				$this->addError(sprintf($this->module->l('Cannot delete the configuration with the name "%s".'), $name));
				return false;
			}
		}
		catch(Exception $e)
		{
			$this->addError($e->getMessage());
			return false;
		}

		return true;
	}

	public function deletePSConfiguration($name)
	{
		try
		{
			if (Configuration::hasKey($name))
			{
				if (!Configuration::deleteByName($name))
				{
					$this->addError(sprintf($this->module->l('Cannot delete the ps configuration with the name "%s".'), $name));
					return false;
				}
			}
		}
		catch(Exception $e)
		{
			$this->addError($e->getMessage());
			return false;
		}

		return true;
	}

	public function updatePSConfiguration($name, $value, $html = false, $id_shop_group = null, $id_shop = null)
	{
		try
		{
			if (!Configuration::updateValue($name, $value, $html, $id_shop_group, $id_shop))
			{
				$this->addError(sprintf($this->module->l('Cannot update the ps configuration with the name "%s".'), $name));
				return false;
			}
		}
		catch(Exception $e)
		{
			$this->addError($e->getMessage());
			return false;
		}

		return true;
	}

	public function createTable($name, $body)
	{
		try
		{
			$this->refreshDb();

			$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.$name.'` (';
			$sql .= ' '.$body.' ';
			$sql .= ') ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8; ';

			if (!Db::getInstance()->execute($sql))
			{
				$this->addError(sprintf($this->module->l('Cannot create the table "%s".'), _DB_PREFIX_.$name));
				return false;
			}
		}
		catch(Exception $e)
		{
			$this->addError($e->getMessage());
			return false;
		}

		return true;
	}

	public function addColumn($table, $name, $value, $after = null)
	{
		try
		{
			if ($this->tableExists($table) && !$this->columnExists($table, $name))
			{
				$this->refreshDb();

				$sql = '';
				$sql .= 'ALTER TABLE `'._DB_PREFIX_.$table.'` ';
				$sql .= 'ADD COLUMN ';
				$sql .= $value.' ';

				if (isset($after))
					$sql .= 'AFTER `'.$after.'` ';

				if (!Db::getInstance()->execute($sql))
				{
					$this->addError(sprintf($this->module->l('Cannot add the column "%s" in the table "%s".'), $name, $table));
					return false;
				}
			}
		}
		catch(Exception $e)
		{
			$this->addError($e->getMessage());
			return false;
		}
		return true;
	}

	public function renameTable($table, $new_name)
	{
		try
		{
			if ($this->tableExists($table))
			{
				$this->refreshDb();

				$sql = 'RENAME TABLE `'._DB_PREFIX_.$table.'` TO `'._DB_PREFIX_.$new_name.'`';
				if (!Db::getInstance()->execute($sql))
				{
					$this->addError(sprintf($this->module->l('Cannot rename the table "%s" to "%s".'), $table, $new_name));
					return false;
				}
			}
		}
		catch(Exception $e)
		{
			$this->addError($e->getMessage());
			return false;
		}
		return true;
	}

	public function changeColumn($table, $name, $value)
	{
		try
		{
			if ($this->tableExists($table) && $this->columnExists($table, $name))
			{
				$this->refreshDb();

				$sql = '';
				$sql .= 'ALTER TABLE `'._DB_PREFIX_.$table.'` ';
				$sql .= 'CHANGE COLUMN `'.$name.'` ';
				$sql .= $value;

				if (!Db::getInstance()->execute($sql))
				{
					$this->addError(sprintf($this->module->l('Cannot change the column "%s" from the table "%s".'), $name, $table));
					return false;
				}
			}
		}
		catch(Exception $e)
		{
			$this->addError($e->getMessage());
			return false;
		}
		return true;
	}

	public function deleteColumn($table, $name)
	{
		try
		{
			if ($this->tableExists($table) && $this->columnExists($table, $name))
			{
				$this->refreshDb();

				$sql = 'ALTER TABLE `'._DB_PREFIX_.$table.'` DROP COLUMN `'.$name.'`';
				if (!Db::getInstance()->execute($sql))
				{
					$this->addError(sprintf($this->module->l('Cannot delete the column "%s" from the table "%s".'), $name, $table));
					return false;
				}
			}
		}
		catch(Exception $e)
		{
			$this->addError($e->getMessage());
			return false;
		}
		return true;
	}

	public function deleteTable($table)
	{
		try
		{
			if ($this->tableExists($table))
			{
				$this->refreshDb();

				$sql = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.$table.'`;';

				if (!Db::getInstance()->execute($sql))
				{
					$this->addError(sprintf($this->module->l('Cannot delete the table "%s".'), $table));
					return false;
				}
			}
		}
		catch(Exception $e)
		{
			$this->addError($e->getMessage());
			return false;
		}
		return true;
	}

	public function addIndex($table, $name)
	{
		try
		{
			if ($this->tableExists($table) && !$this->hasIndex($table, $name))
			{
				$this->refreshDb();

				$sql = '';
				$sql .= 'ALTER TABLE `'._DB_PREFIX_.$table.'` ';
				$sql .= 'ADD INDEX `'.$name.'` (`'.$name.'`)';

				if (!Db::getInstance()->execute($sql))
				{
					$this->addError(sprintf($this->module->l('Cannot add the index "%s" to the table "%s".'), $name, $table));
					return false;
				}
			}
		}
		catch(Exception $e)
		{
			$this->addError($e->getMessage());
			return false;
		}
		return true;
	}

	public function registerHook($name)
	{
		try
		{
			if (!$this->module->registerHook($name))
			{
				$this->addError(sprintf($this->module->l('Cannot register the hook "%s".'), $name));
				return false;
			}
		}
		catch(Exception $e)
		{
			$this->addError($e->getMessage());
			return false;
		}
		return true;
	}

	public function unregisterHook($name)
	{
		try
		{
			if (!$this->module->unregisterHook($name))
			{
				$this->addError(sprintf($this->module->l('Cannot unregister the hook "%s".'), $name));
				return false;
			}
		}
		catch(Exception $e)
		{
			$this->addError($e->getMessage());
			return false;
		}
		return true;
	}

	public function columnExists($table, $name)
	{
		$this->refreshDb();

		return Db::getInstance()->getValue("
			SELECT COUNT(*)
			FROM INFORMATION_SCHEMA.COLUMNS 
			WHERE 
				TABLE_SCHEMA = '"._DB_NAME_."' 
			AND TABLE_NAME = '"._DB_PREFIX_.pSQL($table)."' 
			AND COLUMN_NAME = '".pSQL($name)."'"
		);
	}

	public function addError($error)
	{
		$this->errors[] = $error;
	}

	public function mergeErrors($errors)
	{
		$this->errors = array_merge($this->errors, $errors);
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function hasErrors()
	{
		return (!empty($this->errors));
	}

	public function success()
	{
		return empty($this->errors);
	}

	public function execute()
	{
		$files = $this->getUpgradeFiles();

		foreach ($files as $file)
		{
			if (file_exists($file['path']))
			{
				require_once $file['path'];
				$function_name = 'upgrade_module_'.str_replace('.', '_', $file['version']);

				if (function_exists($function_name))
				{
					if (!call_user_func($function_name, $this->module))
						$this->addError(sprintf($this->module->l('Update failed at the version "%s".'), $file['version']));
				}
				else
					$this->addError(sprintf($this->module->l('The update function "%s" not exists.'), $function_name));
			}
			else
				$this->addError(sprintf($this->module->l('The file "%s" not exists.'), $file['name']));
		}

		if ($this->success())
		{
			if (!$this->updateDbVersion($this->module->version))
			{
				$this->addError($this->module->l('Cannot update the database version.'));
				return false;
			}

			try
			{
				if (NewsletterPro::getConfiguration('DEBUG_MODE'))
					$this->module->clearCache();
				else
					@$this->module->clearCache();
			}
			catch (Exception $e)
			{
				NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
			}

			return true;
		}
		return false;
	}

	public function versionToFloat($version)
	{
		return (float)('0.'.trim(str_replace('.', '', $version)));
	}

	public function getUpgradeFiles()
	{
		$files = array();

		$db_version = $this->getDbVersion();

		$directory = new DirectoryIterator(dirname(__FILE__).'/../upgrade/');
		$iterator = new RegexIterator($directory, '/^Upgrade-/', RecursiveRegexIterator::MATCH);

		foreach ($iterator as $key => $file)
		{

			if (preg_match('/^Upgrade-(?P<version>.*)\.php/', $file->getFilename(), $match))
			{
				if (version_compare($match['version'], $db_version) == 1)
				{
					$files[$key]['name']    = $file->getFilename();
					$files[$key]['path']    = $file->getPathName();
					$files[$key]['version'] = trim($match['version']);
				}
			}
		}

		usort($files, 'NewsletterProUpgrade::sortFilesByVersion');

		return $files;
	}

	public static function sortFilesByVersion($a, $b)
	{
		return version_compare($a['version'], $b['version']);
	}

	public function getDbVersion()
	{
		return Db::getInstance()->getValue('SELECT `version` FROM `'._DB_PREFIX_.'module` WHERE `name` = "'.$this->module->name.'"');
	}

	public function updateDbVersion($version)
	{
		return Db::getInstance()->update('module', array(
					'version' => trim($version),
				), '`name` = "'.pSQL($this->module->name).'"');
	}

	public function updateTabName($new_name = 'Newsletter Pro')
	{
		if ($id = (int)Tab::getIdFromClassName($this->module->class_name))
		{
			return Db::getInstance()->update('tab_lang', array(
				'name' => pSQL($new_name),
			), '`id_tab` = '.(int)$id);
		}
		return false;
	}

	public function tableExists($name)
	{
		$this->refreshDb();

		$sql = "SELECT COUNT(*) AS `count`
				FROM INFORMATION_SCHEMA.TABLES
				WHERE  TABLE_SCHEMA = '"._DB_NAME_."' 
				AND TABLE_NAME = '"._DB_PREFIX_.$name."';";

		return Db::getInstance()->getValue($sql);
	}

	public function hasIndex($table, $key_name)
	{
		$result = Db::getInstance()->executeS('SHOW INDEX FROM `'._DB_PREFIX_.$table.'` WHERE `Key_name` = "'.$key_name.'"');
		return (!empty($result));
	}

	public function valueExists($table, $field, $value)
	{
		$sql = 'SELECT COUNT(*) FROM `'._DB_PREFIX_.$table.'` WHERE `'.$field.'` = "'.$value.'"';
		return Db::getInstance()->getValue($sql);
	}

	public function insertValue($table, $insert)
	{
		try
		{
			$this->refreshDb();

			if (!Db::getInstance()->insert($table, $insert))
			{
				$this->addError(sprintf($this->module->l('Cannot insert the value into the table table "%s".'), $table));
				return false;
			}
		}
		catch(Exception $e)
		{
			$this->addError($e->getMessage());
			return false;
		}
		return true;
	}

	public function refreshDb()
	{
		$db = Db::getInstance();
		$db->disconnect();
		$db->connect();
	}
}