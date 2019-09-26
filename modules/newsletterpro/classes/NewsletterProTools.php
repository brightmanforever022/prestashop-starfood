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

class NewsletterProTools
{
	public static function getActiveShops()
	{
		$context = Context::getContext();

		$shop_list = array();

		if (!method_exists('Shop', 'getContextListShopID'))
			$shops_id = array( '1'=> (int)$context->shop->id);
		else
			$shops_id = Shop::getContextListShopID();

		foreach ($shops_id as $key => $shop_id)
		{
			$shop_list[$key] = Shop::getShop((int)$shop_id);
			if (!isset($shop_list[$key]['id_shop_group']))
				$shop_list[$key]['id_shop_group'] = 1;
		}
		return $shop_list;
	}

	public static function getActiveShopsId()
	{
		$context = Context::getContext();

		if (!method_exists('Shop', 'getContextListShopID'))
			return array( '1' => (int)$context->shop->id);
		else
			return Shop::getContextListShopID();
	}

	public static function isEmpty($str)
	{
		$str_trim = trim($str);
		return empty($str_trim);
	}

	public static function addTableAssociationArray($array)
	{
		foreach ($array as $table_name => $value)
		{
			if (method_exists('Shop', 'addTableAssociation'))
			{
				if (!NewsletterPro::isTableAssociated($table_name))
					Shop::addTableAssociation($table_name, $value);
			}
			else if (method_exists('Shop', 'addTableAssociationNewsletterPro'))
			{
				if (!NewsletterPro::isTableAssociated($table_name))
					Shop::addTableAssociationNewsletterPro($table_name, $value);
			}
			else
			{
				$module = NewsletterPro::getInstance();
				die(Tools::displayError(sprintf($module->l('The functions "%s" or "%s" does not exists. Please override the Shop.php file.'), 'Shop::addTableAssociation', 'Shop::addTableAssociationNewsletterPro' )));
			}

		}
	}

	public static function isFileName($name)
	{
		return (preg_match('/^[a-zA-Z0-9%àâçéèêëîïôûùüÿñæœčšđžćČŠĐĆŽİıÖöÜüÇçĞğŞş₤\s_-]*$/', $name));
	}

	public static function getFileNameIncrement($filename, $new_filename = null, $increment = 0)
	{
		if ($increment > 0 && isset($new_filename))
			$filename = $new_filename;

		if (!file_exists($filename))
			return $filename;

		$increment++;

		$info = pathinfo($filename);
		$fn = preg_replace('/_\d+$/', '', $info['filename']);

		if (is_dir($filename))
		{
			$nfn = $info['dirname'].'/'.$fn.'_'.$increment;
			return self::getFileNameIncrement($filename, $nfn, $increment);
		}
		else
		{

			if (isset($info['extension']))
			{
				$nfn = $info['dirname'].'/'.$fn.'_'.$increment.'.'.$info['extension'];
				return self::getFileNameIncrement($filename, $nfn, $increment);
			}
			else
			{
				$nfn = $info['dirname'].'/'.$fn.'_'.$increment;
				return self::getFileNameIncrement($filename, $nfn, $increment);
			}
		}
	}

	public static function unSerialize($serialized)
	{
		if (is_string($serialized) && preg_match('/a:[0-9]+:\{.*\}/', $serialized))
		{
			$return = @unserialize($serialized);
			if ($return === false)
				return array();
			return $return;
		}
		return array();
	}

	public static function dbSerialize($value)
	{
		return addcslashes(serialize($value), "\x00..\x2C./:;<=>?@[\\]^`{|}~");
	}

	public static function addCShashes($str)
	{
		return addcslashes($str, "\x00..\x2C./:;<=>?@[\\]^`{|}~");
	}

	public static function strSize($string)
	{
		if (function_exists('mb_strlen'))
			$size = mb_strlen($string, '8bit');
		else
			$size = Tools::strlen($string);

		return $size;
	}

	/**
	 * Get xml errors as string
	 * @param  object $error An xml error object
	 * @param  object $xml   An intance of the xml object
	 * @return string
	 */
	public static function displayXMLError($error, $xml)
	{
		$return = $xml[$error->line - 1];

		switch ($error->level)
		{
			case LIBXML_ERR_WARNING:
				$return .= "Warning $error->code: ";
				break;
			case LIBXML_ERR_ERROR:
				$return .= "Error $error->code: ";
				break;
			case LIBXML_ERR_FATAL:
				$return .= "Fatal Error $error->code: ";
				break;
		}

		$return .= trim($error->message)." Line: $error->line Column: $error->column";

		if ($error->file)
			$return .= " File: $error->file";

		return $return;
	}

	public static function normalizePath($path)
	{
		$parts    = array();
		$path     = str_replace('\\', '/', $path);
		$path     = preg_replace('/\/+/', '/', $path);
		$segments = explode('/', $path);
		$test     = '';

		foreach ($segments as $segment)
		{
			if ($segment != '.')
			{
				$test = array_pop($parts);
				if (is_null($test))
					$parts[] = $segment;
				else if ($segment == '..')
				{
					if ($test == '..')
					$parts[] = $test;

					if ($test == '..' || $test == '')
					$parts[] = $segment;
				}
				else
				{
					$parts[] = $test;
					$parts[] = $segment;
				}
			}
		}
		return implode('/', $parts);
	}

	public static function strToHex($str)
	{
		return unpack('H*', $str);
	}

	public static function hexToStr($str)
	{
		if (preg_match('/^0x/i', $str))
			$str = preg_replace('/^0x/i', '', $str);
		return pack('H*', $str);
	}

	public static function recurseCopy($src, $dst)
	{
		$dir = opendir($src);
		@mkdir($dst);
		while (false !== ( $file = readdir($dir)))
		{
			if (( $file != '.' ) && ( $file != '..' ))
			{
				if (is_dir($src.'/'.$file))
					self::recurseCopy($src.'/'.$file, $dst.'/'.$file);
				else
					copy($src.'/'.$file, $dst.'/'.$file);
			}
		}
		closedir($dir);
	}

	public static function getIdShopGroup($context = null)
	{
		if (!isset($context))
			$context = Context::getContext();

		if (class_exists('ShopGroup'))
			return $context->shop->id_shop_group;
		else
			return $context->shop->id_group_shop;
	}

	public static function getInShopGroupColumnName()
	{
		if (class_exists('ShopGroup'))
			return 'id_shop_group';
		return 'id_group_shop';
	}

	public static function createFolder($path = false, $path_thumbs = false)
	{
		$oldumask = umask(0);
		if ($path && !file_exists($path))
			mkdir($path, 0777, true);
		if ($path_thumbs && !file_exists($path_thumbs))
			mkdir($path_thumbs, 0777, true);
		umask($oldumask);
	}

	public static function blockNewsletterExists()
	{
		$sql = "SELECT COUNT(*) AS `count`
		FROM INFORMATION_SCHEMA.TABLES
		WHERE  TABLE_SCHEMA = '"._DB_NAME_."' 
		AND TABLE_NAME = '"._DB_PREFIX_."newsletter';";

		return Db::getInstance()->getValue($sql);
	}

	public static function closeConnection($content = null)
	{
		@ob_implicit_flush(true);
		@ob_end_clean();
		@ob_start();
		echo $content;
		$size = ob_get_length();
		header("Content-Length: $size");
		header('Connection: close');
		ob_end_flush();
		ob_flush();
		flush();

		if (session_id())
			session_write_close();
	}

	public static function getTableColumns($table_name)
	{
		$columns_db = Db::getInstance()->executeS('
			SELECT `column_name` 
			FROM `information_schema`.`columns` 
			WHERE `table_schema`="'._DB_NAME_.'" 
			AND `table_name`="'._DB_PREFIX_.pSQL($table_name).'";
		');

		$columns = array();
		foreach ($columns_db as $column) 
			$columns[] = $column['column_name'];

		return $columns;
	}

	public static function tableExists($table_name)
	{
		$table_name = _DB_PREFIX_.$table_name;
		return count(Db::getInstance()->executeS('SHOW TABLES LIKE "'.pSQL($table_name).'"'));
	}

	public static function columnExists($table, $name)
	{
		return Db::getInstance()->getValue("
			SELECT COUNT(*)
			FROM INFORMATION_SCHEMA.COLUMNS 
			WHERE 
				TABLE_SCHEMA = '"._DB_NAME_."' 
			AND TABLE_NAME = '"._DB_PREFIX_.pSQL($table)."' 
			AND COLUMN_NAME = '".pSQL($name)."'"
		);
	}

	public static function getDbColumns($table_name)
	{
		$columns_array = array();
		$columns = Db::getInstance()->executeS("
			SELECT COLUMN_NAME
			FROM INFORMATION_SCHEMA.COLUMNS 
			WHERE 
				TABLE_SCHEMA = '"._DB_NAME_."' 
			AND TABLE_NAME = '"._DB_PREFIX_.pSQL($table_name)."'"
		);

		foreach ($columns as $row) 
			$columns_array[] = $row['COLUMN_NAME'];

		return $columns_array;
	}

	/**
	 * List files from folder
	 *
	 * @param  string $path
	 * @param  string $regex
	 * @return objects
	 */
	public static function getDirectoryIterator($path, $regex)
	{
		$directory = new DirectoryIterator($path);
		$result = new RegexIterator($directory, $regex, RecursiveRegexIterator::MATCH);
		return $result;
	}

	public static function deleteDirAndFiles($path)
	{
		$succeed = array();

		$it = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
	
		foreach ($files as $file)
		{
			if ($file->isDir())
				$succeed[] = rmdir($file->getRealPath());
			else
				$succeed[] = unlink($file->getRealPath());
		}

		$succeed[] = rmdir($path);

		return !in_array(false, $succeed);
	}

	public static function languageExists($id_lang, $check_active = false)
	{
		return (int)Db::getInstance()->getValue('
			SELECT `id_lang`
			FROM `'._DB_PREFIX_.'lang`
			WHERE `id_lang` = "'.(int)$id_lang.'"
			'.($check_active ? ' AND `active` = 1 ' : '').'
		');
	}

	public static function getTemplatePath($path)
	{
		$module = NewsletterPro::getInstance();

		$template_name = pathinfo($path, PATHINFO_BASENAME);
		$rel_path = preg_replace('/^.*'.$module->name.'(\/|\\\)/', '', $path);

		$template = _PS_THEME_DIR_.'modules/'.$module->name.'/'.$rel_path;

		if (file_exists($template))
			return $template;
		else
			return $path;
	}
}