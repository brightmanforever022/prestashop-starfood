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

class NewsletterProSubscribersCustomField extends ObjectModel
{
	public $variable_name;

	public $type;

	public $value;

	public $required;

	const TYPE_SELECT = 101;

	const TYPE_CHECKBOX = 102;

	const TYPE_RADIO = 103;

	const TYPE_INPUT_TEXT = 104;

	const TYPE_TEXTAREA = 105;

	private $type_values;

	public static $definition = array(
		'table'     => 'newsletter_pro_subscribers_custom_field',
		'primary'   => 'id_newsletter_pro_subscribers_custom_field',
		'multilang' => true,
		'fields' => array(
			'variable_name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
			'type'          => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
			'required'      => array('type' => self::TYPE_INT, 'validate' => 'isInt'),

			/* Lang fields */
			'value' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true),
		)
	);

	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->setTypeValues($this->value);
	}

	public static function newInstance($id = null)
	{
		return new self($id);
	}

	private function setTypeValues($input)
	{
		foreach ($input as $id_lang => $value)
		{
			$this->type_values[$id_lang] = array();

			foreach (NewsletterProTools::unSerialize($value) as $id_l => $v)
			{
				if ($id_l == $id_lang)
					$this->type_values[$id_lang] = $v;
			}
		}
	}

	public static function getTypes()
	{
		$module = NewsletterPro::getInstance();

		return array(
			self::TYPE_SELECT => $module->l('Select'),
			self::TYPE_CHECKBOX => $module->l('Checkbox'),
			self::TYPE_RADIO => $module->l('Radio'),
			self::TYPE_INPUT_TEXT => $module->l('Input Text'),
			self::TYPE_TEXTAREA => $module->l('Textarea'),
		);
	}

	public static function getEditableTypes()
	{
		$editable_types = array(self::TYPE_SELECT, self::TYPE_CHECKBOX, self::TYPE_RADIO);

		$types = self::getTypes();
		foreach (array_keys($types) as $type) 
		{
			if (!in_array($type, $editable_types))
				unset($types[$type]);
		}

		return $types;
	}

	public static function getTypesJs()
	{
		return array(
			'TYPE_SELECT' => self::TYPE_SELECT,
			'TYPE_CHECKBOX' => self::TYPE_CHECKBOX,
			'TYPE_RADIO' => self::TYPE_RADIO,
			'TYPE_INPUT_TEXT' => self::TYPE_INPUT_TEXT,
			'TYPE_TEXTAREA' => self::TYPE_TEXTAREA,
		);
	}

	public static function getTypeName($type)
	{
		$types = self::getTypes();
		if (isset($types[$type]))
			return $types[$type];

		return 'Undefined';
	}

	public function getValues($id_lang = null)
	{
		if (isset($id_lang))
			return $this->type_values[$id_lang];

		return $this->type_values;
	}

	public function addValue($value)
	{
		if (!is_array($value))
			throw new NewsletterProSubscribersCustomFieldException(NewsletterPro::getInstance()->l('The value should be an multilanguage array.'));
					
		foreach ($value as $id_lang => $v) 
		{
			if (Tools::strlen($v) == 0)
			{
				$lang = new Language($id_lang);
				throw new NewsletterProSubscribersCustomFieldException(sprintf(NewsletterPro::getInstance()->l('The value should cannot be empty for "%s".'), Tools::strtoupper($lang->iso_code)));
			}

			$this->type_values[$id_lang][] = $v;
		}

		$this->value = serialize($this->type_values);
	}

	public function removeValueByKey($key)
	{
		foreach ($this->type_values as &$value) 
		{
			if (isset($value[$key]))
				unset($value[$key]);
		}

		$this->value = serialize($this->type_values);
	}

	public function updateValue($key, $new_value)
	{
		foreach ($this->type_values as $id_lang => &$value) 
		{

			if (isset($value[$key]) && isset($new_value[$id_lang]))
			{
				if (Tools::strlen($new_value[$id_lang]) == 0)
				{
					$lang = new Language($id_lang);
					throw new NewsletterProSubscribersCustomFieldException(sprintf(NewsletterPro::getInstance()->l('The value should cannot be empty for "%s".'), Tools::strtoupper($lang->iso_code)));
				}

				$value[$key] = $new_value[$id_lang];
			}
		}

		$this->value = serialize($this->type_values);
	}

	public function hasKey($key)
	{
		foreach ($this->type_values as &$value) 
		{
			if (isset($value[$key]))
				return true;
		}
		return false;
	}

	public function getValueByKey($key)
	{
		$return = array();
		foreach ($this->type_values as $id_lang => $value) 
		{
			if (isset($value[$key]))
				$return[$id_lang] = $value[$key];
		}

		return $return;
	}

	public function setVariableName($variable_name)
	{
		$variable_name = 'np_'.preg_replace('/\s+/', '_', trim(Tools::strtolower($variable_name)));
		$this->variable_name = $variable_name;
	}

	public function add($auto_date = true, $null_values = false)
	{
		$this->saveValidation();
		
		$variable_name_exists = Db::getInstance()->getValue('
			SELECT COUNT(*) 
			FROM `'._DB_PREFIX_.'newsletter_pro_subscribers_custom_field`
			WHERE `variable_name` = "'.pSQL($this->variable_name).'"
		');

		if ($variable_name_exists)
			throw new NewsletterProSubscribersCustomFieldException(sprintf(NewsletterPro::getInstance()->l('A variable with the name "{%s}" already exists. Please use a different variable name.'), $this->variable_name));
		
		$this->addColumn('newsletter_pro_subscribers', $this->variable_name, ' `'.pSQL($this->variable_name).'` TEXT NULL');

		return parent::add($auto_date, $null_values);
	}

	public function delete()
	{
		$show_custom_colums = pqnp_config('SHOW_CUSTOM_COLUMNS');

		if (in_array($this->variable_name, $show_custom_colums))
		{
			$key = array_search($this->variable_name, $show_custom_colums);

			if ($key !== false)
			{
				unset($show_custom_colums[$key]);
				$show_custom_colums = array_values($show_custom_colums);
			}
		}

		pqnp_config('SHOW_CUSTOM_COLUMNS', $show_custom_colums);

		if ($this->columnExists('newsletter_pro_subscribers', $this->variable_name))
			$this->deleteColumn('newsletter_pro_subscribers', $this->variable_name);

		parent::delete();
	}

	private function columnExists($table, $name)
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

	private function addColumn($table, $name, $value, $after = null)
	{
		if ($this->columnExists('newsletter_pro_subscribers', $name))
			throw new NewsletterProSubscribersCustomFieldException(sprintf(NewsletterPro::getInstance()->l('A table column with the name "%s" already exists. Please use a different variable name.'), $name));

		$sql = '';
		$sql .= 'ALTER TABLE `'._DB_PREFIX_.$table.'` ';
		$sql .= 'ADD COLUMN ';
		$sql .= $value.' ';

		if (isset($after))
			$sql .= 'AFTER `'.$after.'` ';

		if (!Db::getInstance()->execute($sql))
			throw new NewsletterProSubscribersCustomFieldException(sprintf(NewsletterPro::getInstance()->l('Cannot add the column "%s" in the table "%s".'), $name, $table));
	}

	public function deleteColumn($table, $name)
	{
		$sql = 'ALTER TABLE `'._DB_PREFIX_.$table.'` DROP COLUMN `'.$name.'`';
		if (!Db::getInstance()->execute($sql))
			throw new NewsletterProSubscribersCustomFieldException(sprintf(NewsletterPro::getInstance()->l('Cannot delete the column "%s" from the table "%s".'), $name, $table));
	}

	public function update($null_values = false)
	{
		$this->saveValidation();
		return parent::update($null_values);
	}

	private function saveValidation()
	{
		if (!preg_match('/^np_[A-Za-z_]+$/', $this->variable_name))
			throw new NewsletterProSubscribersCustomFieldException(sprintf(NewsletterPro::getInstance()->l('The variable name "{%s}" is not valid.'), $this->variable_name));

		$types = array_keys(self::getTypes());

		if (!in_array($this->type, $types))
			throw new NewsletterProSubscribersCustomFieldException(sprintf(NewsletterPro::getInstance()->l('Invalid field type "%s" for creation.'), $this->type));

		// 5 characters + prefix = 8 characters
		if (Tools::strlen($this->variable_name) < 8)
			throw new NewsletterProSubscribersCustomFieldException(sprintf(NewsletterPro::getInstance()->l('The variable must contains at least %s characters.'), 5));
	}

	public function render($id_lang = null)
	{
		$context = Context::getContext();

		if (!isset($id_lang))
			$id_lang = $context->language->id;

		$filename = pqnp_template_path(NewsletterPro::getInstance()->dir_location.'views/templates/hook/newsletter_subscribe_custom_field_components.tpl');
		$template = $context->smarty->createTemplate($filename);
		$template->assign(array(
			'types' => array(
				'TYPE_SELECT'     => self::TYPE_SELECT,
				'TYPE_CHECKBOX'   => self::TYPE_CHECKBOX,
				'TYPE_RADIO'      => self::TYPE_RADIO,
				'TYPE_INPUT_TEXT' => self::TYPE_INPUT_TEXT,
				'TYPE_TEXTAREA'   => self::TYPE_TEXTAREA,
			),
			'type' => $this->type,
			'variable_name' => $this->variable_name,
			'value' => $this->getValues($id_lang),
			'required' => $this->required,
		));

		return $template->fetch();
	}

	public static function getVariables()
	{
		$result = Db::getInstance()->executeS('
			SELECT `variable_name`
			FROM `'._DB_PREFIX_.'newsletter_pro_subscribers_custom_field`
		');

		$variables = array();

		if ($result)
		{
			foreach ($result as $row)
				$variables[] = $row['variable_name'];
		}

		return $variables;
	}

	public static function getVariablesWithTypes()
	{
		$result = Db::getInstance()->executeS('
			SELECT `variable_name`, `type`
			FROM `'._DB_PREFIX_.'newsletter_pro_subscribers_custom_field`
		');

		return $result;
	}

	public static function getInstanceByVariableName($variable_name)
	{
		$id = (int)Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_subscribers_custom_field`
			FROM `'._DB_PREFIX_.'newsletter_pro_subscribers_custom_field`
			WHERE `variable_name` = "'.pSQL($variable_name).'"
		');

		$instance = self::newInstance($id);

		return Validate::isLoadedObject($instance) ? $instance : false;
	}

	public function isRequired()
	{
		return (bool)$this->required;
	}
}