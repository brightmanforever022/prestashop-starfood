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

class NewsletterProConfigurationShop
{
	public static $name = 'NEWSLETTER_PRO_SHOP';

	public static $configuration = array();

	public static $errors = array();

	public static $init_id_shop;

	public static $init_id_shop_group;

	public static function install($all_shops = true)
	{
		$module = NewsletterPro::getInstance();
		// at least one value should be available
		self::$configuration = self::getDefaultConfiguration();

		if ($all_shops)
		{
			foreach (NewsletterProTools::getActiveShops() as $shop)
			{
				if (!self::updateDb((int)$shop['id_shop'], (int)$shop['id_shop_group']))
				{
					self::$errors[] = sprintf($module->l('The configuration "%s" cannot be installed into "%s" table.'), self::$name, _DB_PREFIX_.'configuration');
					return false;
				}
			}

			foreach (NewsletterPro::getShopGroups() as $shop_group)
			{
				if (!self::updateDb(null, (int)$shop_group->id))
				{
					self::$errors[] = sprintf($module->l('The configuration "%s" cannot be installed into "%s" table.'), self::$name, _DB_PREFIX_.'configuration');
					return false;
				}
			}
		}
		else
		{
			if (!self::updateDb())
			{
				self::$errors[] = sprintf($module->l('The configuration "%s" cannot be installed into "%s" table.'), self::$name, _DB_PREFIX_.'configuration');
				return false;
			}
		}

		return true;
	}

	public static function getDefaultConfiguration()
	{
		return array(
			'SUBSCRIPTION_TEMPLATE' => 'default'
		);
	}

	public static function uninstall()
	{
		return Configuration::deleteByName(self::$name);
	}

	public static function getErrors()
	{
		return self::$errors;
	}

	public static function init($id_shop = null, $id_shop_group = null)
	{
		if (!isset($id_shop))
			$id_shop = NewsletterPro::getContextShopID(true);
		self::$init_id_shop = $id_shop;

		if (!isset($id_shop_group))
			$id_shop_group = NewsletterPro::getContextShopGroupID(true);
		self::$init_id_shop_group = $id_shop_group;

		$cfg = NewsletterProTools::unSerialize(Configuration::get(self::$name, null, self::$init_id_shop_group, self::$init_id_shop));

		if (!empty($cfg))
			self::$configuration = $cfg;
		else
			self::$configuration = self::getDefaultConfiguration();
	}

	public static function get($name = null)
	{
		if (isset($name))
		{
			if (isset(self::$configuration[$name]))
				return self::$configuration[$name];
			return false;
		}

		return self::$configuration;
	}

	public static function getAll()
	{
		return self::get();
	}

	public static function updateValue($name, $value)
	{
		if (isset(self::$configuration[$name]))
			return self::set($name, $value);
		else
			return false;
	}

	public static function set($name, $value)
	{
		self::$configuration[$name] = $value;
		return self::updateDb();
	}

	public static function deleteByName($name)
	{
		if (isset(self::$configuration[$name]))
			unset(self::$configuration[$name]);

		return self::updateDb();
	}

	public static function updateDb($id_shop = null, $id_shop_group = null)
	{
		if (!isset($id_shop))
			$id_shop = NewsletterPro::getContextShopID(true);
		self::$init_id_shop = $id_shop;

		if (!isset($id_shop_group))
			$id_shop_group = NewsletterPro::getContextShopGroupID(true);
		self::$init_id_shop_group = $id_shop_group;

		return Configuration::updateValue(self::$name, serialize(self::$configuration), false, (int)$id_shop_group, (int)$id_shop);
	}

	public static function getDbConfigurations()
	{
		$recoreds = Db::getInstance()->executeS('
			SELECT * FROM `'._DB_PREFIX_.'configuration` 
			WHERE `name` = "'.pSQL(self::$name).'"
		');
		foreach ($recoreds as &$recored)
		{
			if (!array_key_exists('id_shop_group', $recored) && array_key_exists('id_group_shop', $recored))
				$recored['id_shop_group'] = $recored['id_group_shop'];
		}
		return $recoreds;
	}

	public static function replaceShopsCondiguration($to_replace = array())
	{
		$configs = self::getDbConfigurations();
		$success = array();

		$column_name = NewsletterProTools::getInShopGroupColumnName();

		foreach ($configs as &$config)
		{
			foreach ($to_replace as &$to_confg)
			{

				if ($config['id_shop'] == $to_confg['id_shop'] && $config['id_shop_group'] == $to_confg['id_shop_group'])
				{
					$current_config = NewsletterProTools::unSerialize($config['value']);

					$current_config = array_merge($current_config, $to_confg['value']);
					$config['value'] = serialize($current_config);

					$success[] = Db::getInstance()->update('configuration', array(
						'value' => $config['value']
					), '`name` = "'.pSQL(self::$name).'" AND `id_shop` '.(is_null($config['id_shop']) ? 'IS NULL' : '='.(int)$config['id_shop']).' AND `'.$column_name.'` '.(is_null($config['id_shop_group']) ? 'IS NULL' : '='.$config['id_shop_group']).' ', 0, true);
				}
			}
		}

		return !in_array(false, $success);
	}

	public static function getAllShopsConfiguration($filter = array())
	{
		$result = array();

		foreach (self::getDbConfigurations() as $config)
		{
			$value = NewsletterProTools::unSerialize($config['value']);

			$config_keys = array_keys($value);

			$intersection = array_intersect($filter, $config_keys);

			$value_filter = array();

			foreach ($intersection as $cfg_name)
			{
				if (isset($value[$cfg_name]))
					$value_filter[$cfg_name] = $value[$cfg_name];
			}

			$result[] = array(
				'id_shop'       => $config['id_shop'],
				'id_shop_group' => $config['id_shop_group'],
				'value'         => empty($filter) ? $value : $value_filter,
			);
		}

		return $result;
	}
}