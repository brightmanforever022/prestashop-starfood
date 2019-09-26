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

class NewsletterProListOfInterest extends ObjectModel
{
	public $active;

	public $name;

	public $position;

	/* defined variables */

	public $errors = array();

	public static $definition = array(
		'table' => 'newsletter_pro_list_of_interest',
		'primary' => 'id_newsletter_pro_list_of_interest',
		'multilang' => true,
		'multilang_shop' => true,
		'fields' => array(
			/* Lang fields */
			'name'     => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 255),

			/* Shop fields */
			'active'   => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'shop' => true),
			'position' => array('type' => self::TYPE_INT, 'shop' => true),
		),
	);

	public function __construct($id = null, $id_lang = null, $id_shop = null)
	{
		self::initAssoTables();

		parent::__construct($id, $id_lang, $id_shop);

		$this->context = Context::getContext();
		$this->module  = NewsletterPro::getInstance();
	}

	public static function initAssoTables()
	{
		NewsletterProTools::addTableAssociationArray(self::getAssoTables());
	}

	public static function getAssoTables()
	{
		return array(
			'newsletter_pro_list_of_interest' => array('type' => 'shop'),
			// if it si liltiland multishop the fk_shop is requered, all the values will be availalbe in all the shop
			'newsletter_pro_list_of_interest_lang' => array('type' => 'shop')
		);
	}

	public function add($autodate = true, $null_values = false)
	{
		try
		{
			$position = (int)Db::getInstance()->getValue('SELECT MAX(`position`) FROM `'._DB_PREFIX_.'newsletter_pro_list_of_interest` WHERE 1');
			$this->position = ++$position;

			$return = parent::add($autodate, $null_values);

			if (!$return)
				$this->addError($this->module->l('An error occurred when adding the record into database.'));

			return $return;
		}
		catch(Exception $e)
		{
			if (_PS_MODE_DEV_)
				$this->addError($e->getMessage());
			else
				$this->addError($this->module->l('An error occurred when adding the record into database.'));
		}

		return false;
	}

	public function addError($error)
	{
		$this->errors[] = $error;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function hasErrors()
	{
		return !empty($this->errors);
	}

	public static function getListSql($cfg = array())
	{
		$context = Context::getContext();

		if (!isset($cfg['id_lang']))
			$cfg['id_lang'] = $context->language->id;

		if (!isset($cfg['id_shop']))
			$cfg['id_shop'] = $context->shop->id;

		$sql = array();

		$sql[] = 'SELECT i.`id_newsletter_pro_list_of_interest`, il.`name`, iss.`id_shop`, iss.`active`, iss.`position` 
			FROM `'._DB_PREFIX_.'newsletter_pro_list_of_interest` i
			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_list_of_interest_lang` il 
				ON (i.`id_newsletter_pro_list_of_interest` = il.`id_newsletter_pro_list_of_interest`)
			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_list_of_interest_shop` iss
				ON (i.`id_newsletter_pro_list_of_interest` = iss.`id_newsletter_pro_list_of_interest`
					AND il.`id_shop` = iss.`id_shop`)
			WHERE il.`id_lang` = '.(int)$cfg['id_lang'].'
			AND il.`id_shop` = '.(int)$cfg['id_shop'];

		if (isset($cfg['and']))
			$sql[] = $cfg['and'];

		$sql[] = ' ORDER BY iss.`position`';
		return implode(' ', $sql);
	}

	public static function getList($id_lang = null, $id_shop = null)
	{
		$id_lang = (isset($id_lang) ? $id_lang : Context::getContext()->language->id);
		$id_shop = (isset($id_shop) ? $id_shop : Context::getContext()->shop->id);

		$sql = self::getListSql(array(
			'id_lang' => (int)$id_lang,
			'id_shop' => (int)$id_shop,
		));

		return Db::getInstance()->executeS($sql);
	}

	public static function getListActive($id_lang = null, $id_shop = null)
	{
		$id_lang = (!isset($id_lang) ? Context::getContext()->language->id : $id_lang);
		$id_shop = (!isset($id_shop) ? Context::getContext()->shop->id : $id_shop);

		$sql = self::getListSql(array(
			'id_lang' => (int)$id_lang,
			'id_shop' => (int)$id_shop,
			'and'     => ' AND iss.`active` = 1 ',
		));

		return Db::getInstance()->executeS($sql);
	}

	public static function getListActiveCustomer($id_customer, $id_lang = null, $id_shop = null)
	{
		$list = self::getListActive($id_lang, $id_shop);

		$row = Db::getInstance()->getRow('
			SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_customer_list_of_interests`
			WHERE `id_customer` = '.(int)$id_customer.'
		');

		if (!empty($row) && !empty($row['categories'])) {
			$categories = explode(',', $row['categories']);

			foreach ($list as $key => $value) {
				$id = $value['id_newsletter_pro_list_of_interest'];
				$list[$key]['checked'] = false;
				
				if (in_array($id, $categories)) {
					$list[$key]['checked'] = true;	
				}
			}

		} else {
			foreach ($list as $key => $value) {
				$list[$key]['checked'] = false;
			}
		}

		return $list;
	}

	public function fillField($field_name, $values)
	{
		$default_lang = $this->module->getConfiguration('PS_SHOP_DEFAULT');

		foreach (Language::getLanguages(true) as $lang)
		{
			$id_lang = $lang['id_lang'];
			$this->{$field_name}[$id_lang] = (isset($values[$id_lang]) ? $values[$id_lang] : $values[$default_lang]);
		}
	}
}