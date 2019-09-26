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

class NewsletterProMailChimpFields
{
	public $errors = array();
	public $data = array();
	public $context;
	public $list_vars;

	public static $fields = array();

	public function __construct($list_vars)
	{
		$this->context = Context::getContext();
		$this->list_vars = $list_vars;

		$this->defineData();
		$this->defineFields();
	}

	public function defineFields()
	{
		$this->addField('FNAME', array(
			'name' => 'First Name',
			'options' => array(
				'field_type' => 'text',
				'req' => false,
			),
		));

		$this->addField('LNAME', array(
			'name' => 'Last Name',
			'options' => array(
				'field_type' => 'text',
				'req' => false,
			),
		));

		$this->addField('SHOP', array(
			'name' => 'Shop',
			'options' => array(
				'field_type' => 'dropdown',
				'default_value' => $this->getData('default_shop_name'),
				'choices' => ( $this->getData('shops') ? $this->getData('shops_name') : $this->getData('default_shop_name') ),
				'req' => false,
			),
		));

		$this->addField('SUBSCRIBED', array(
			'name' => 'Subscribed',
			'options' => array(
				'field_type' => 'dropdown',
				'default_value' => 'yes',
				'choices' => array('yes', 'no'),
				'req' => false,
			),
		));

		$this->addField('USER_TYPE', array(
			'name' => 'User Type',
			'options' => array(
				'field_type' => 'dropdown',
				'default_value' => 'Added',
				'choices' => array('Customer', 'Visitor', 'Added'),
				'req' => false,
			),
		));

		$this->addField('LANGUAGE', array(
			'name' => 'Language',
			'options' => array(
				'field_type' => 'text',
				'default_value' => $this->getData('default_language_name'),
				'req' => false,
			),
		));

		$this->addField('LAST_ORDER', array(
			'name' => 'Last Order',
			'options' => array(
				'field_type' => 'date',
				'dateformat' => 'MM/DD/YYYY',
				'req' => false,
			),
		));

		$this->addField('OPTIN_IP', array(
			'name' => 'Ip',
			'options' => array(
				'field_type' => 'text',
				'req' => false,
			),
		));

		$this->addField('BIRTHDAY', array(
			'name' => 'Birthday',
			'options' => array(
				'field_type' => 'birthday',
				'dateformat' => 'MM/DD',
				'req' => false,
			),
		));

		$this->addField('ADDRESS', array(
			'name' => 'Address',
			'options' => array(
				'field_type' => 'address',
				'req' => false,
			),
		));

		$this->addField('COMPANY', array(
			'name' => 'Company',
			'options' => array(
				'field_type' => 'text',
				'req' => false,
			),
		));

		$this->addField('DATE_ADD', array(
			'name' => 'Registration Date',
			'options' => array(
				'field_type' => 'date',
				'dateformat' => 'MM/DD/YYYY',
				'req' => false,
			),
		));

		$this->addField('PHONE', array(
			'name' => 'Phone',
			'options' => array(
				'field_type' => 'phone',
				'req' => false,
			),
		));

		$this->addField('PHONE_MOB', array(
			'name' => 'Phone Mobile',
			'options' => array(
				'field_type' => 'phone',
				'req' => false,
			),
		));
	}

	public function addField($name, $field)
	{
		self::$fields[$name] = $field;
	}

	public function defineData()
	{
		$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
		$this->addData('id_lang_default', $id_lang_default);
		$this->addData('locale_country', Tools::strtoupper(Configuration::get('PS_LOCALE_COUNTRY')));
		$this->addData('id_shop_default', (int)Configuration::get('PS_SHOP_DEFAULT'));

		$groups = Group::getGroups($id_lang_default);
		$this->addData('groups', $groups);
		$this->addData('groups_name', self::grep($groups, 'name'));

		$shops = Shop::getShops(false);
		$this->addData('shops', $shops);
		$this->addData('shops_name', self::grep($shops, 'name'));
		$this->addData('default_shop_name', Configuration::get('PS_SHOP_NAME'));

		$default_language = Language::getLanguage($id_lang_default);
		$this->addData('default_language', $default_language);
		$this->addData('default_language_name', $default_language['name']);

		$languages = Language::getLanguages(false);
		$this->addData('languages', $languages);
		$this->addData('languages_name', self::grep($languages, 'name'));
	}

	public function getData($name)
	{
		if (isset($this->data[$name]))
			return $this->data[$name];
		return false;
	}

	public function addData($name, $data)
	{
		$this->data[$name] = $data;
	}

	public function getSyncVars()
	{
		$save_tags = array();

		$fields = $this->getFields();
		$vars_tags = self::grep($this->list_vars, 'tag');

		foreach ($fields as $tag => $value)
		{
			if (in_array($tag, $vars_tags))
				$save_tags['update'][$tag] = $value;
			else
				$save_tags['add'][$tag] = $value;
		}

		return $save_tags;
	}

	public function getRestVars()
	{
		$fields = $this->getFields();
		$vars_tags = self::grep($this->list_vars, 'tag');
		$vars_tags_rest = array_diff($vars_tags, array_keys($fields));

		$searched_key = array_search('EMAIL', $vars_tags_rest);
		if ($searched_key !== false)
			unset($vars_tags_rest[$searched_key]);

		return $vars_tags_rest;
	}

	public static function grep($array, $name)
	{
		$return_array = array();
		foreach ($array as $value)
			if (isset($value[$name]))
				$return_array[] = $value[$name];
		return $return_array;
	}

	public function getFields()
	{
		return self::$fields;
	}

	public function addError($error, $code = null)
	{
		$add_error = array(
			'code' => $code,
			'error' => Tools::displayError($error),
		);

		$this->errors[] = $add_error;
	}

	public function hasErrors()
	{
		return !empty($this->errors);
	}

	public function getErrors($only_errors = false, $collapse_same_code = false)
	{
		$errors = $this->errors;

		if ($collapse_same_code)
		{
			$errors_collapse = array();
			$errors_coldes = array();

			foreach ($errors as $error)
				if (!in_array($error['code'], $errors_coldes))
				{
					$errors_collapse[] = $error;
					$errors_coldes[] = $error['code'];
				}

			$errors = $errors_collapse;
		}

		$return_errors = array();
		if ($only_errors)
		{
			foreach ($errors as $error)
				$return_errors[] = $error['error'];
		}
		else
			$return_errors = $this->errors;

		return $return_errors;
	}
}
?>