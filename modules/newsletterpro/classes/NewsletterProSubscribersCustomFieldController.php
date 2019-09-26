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

class NewsletterProSubscribersCustomFieldController
{
	private $response;

	public function __construct()
	{
		$this->response = NewsletterProAjaxResponse::newInstance();
	}

	public static function newInstance()
	{
		return new self();
	}

	public function addField($variable_name, $type, $required)
	{
		try
		{
			$field = NewsletterProSubscribersCustomField::newInstance();
			$field->setVariableName($variable_name);
			$field->required = (int)$required;
			$field->type = (int)$type;
			$field->add();
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}

		return $this->response->display();
	}

	public function deleteField($id)
	{
		try
		{
			$field = NewsletterProSubscribersCustomField::newInstance($id);

			if (!Validate::isLoadedObject($field))
				throw new Exception(NewsletterPro::getInstance()->l('Invalid field id.'));


			$field->delete();
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}
		return $this->response->display();
	}

	public function addValue($id, $value)
	{
		try
		{
			$field = NewsletterProSubscribersCustomField::newInstance($id);

			if (!Validate::isLoadedObject($field))
				throw new Exception(NewsletterPro::getInstance()->l('Invalid field id.'));

			$field->addValue($value);
			$field->save();
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}
		return $this->response->display();
	}

	public function updateValue($id, $key, $value)
	{
		try
		{
			$field = NewsletterProSubscribersCustomField::newInstance($id);

			if (!Validate::isLoadedObject($field))
				throw new Exception(NewsletterPro::getInstance()->l('Invalid field id.'));

			if (!$field->hasKey($key))
				throw new Exception(NewsletterPro::getInstance()->l('The value no logner exists.'));

			$field->updateValue($key, $value);
			$field->save();
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}
		return $this->response->display();
	}

	public function removeValueByKey($id, $key)
	{
		try
		{
			$field = NewsletterProSubscribersCustomField::newInstance($id);

			if (!Validate::isLoadedObject($field))
				throw new Exception(NewsletterPro::getInstance()->l('Invalid field id.'));
			
			$field->removeValueByKey($key);
			$field->save();
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}
		return $this->response->display();
	}

	public function getValueByKey($id, $key)
	{
		try
		{
			$field = NewsletterProSubscribersCustomField::newInstance($id);

			if (!Validate::isLoadedObject($field))
				throw new Exception(NewsletterPro::getInstance()->l('Invalid field id.'));
			
			$value = $field->getValueByKey($key);

			if (!$value)
				throw new Exception(NewsletterPro::getInstance()->l('The value don\'t exists.'));

			$this->response->set('value', $value);
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}
		return $this->response->display();
	}

	public function getValuesList($id, $id_lang = null)
	{
		try
		{
			$field = NewsletterProSubscribersCustomField::newInstance($id);

			if (!Validate::isLoadedObject($field))
				throw new Exception(NewsletterPro::getInstance()->l('Invalid field id.'));

			if (!isset($id_lang) || !$id_lang)
				$id_lang = (int)Context::getContext()->language->id;

			$results = array();

			$values = $field->getValues($id_lang);

			foreach ($values as $key => $value) 
			{
				$results[] = array(
					'key' => $key,
					'value' => $value
				);
			}

			return Tools::jsonEncode($results);
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}
		return $this->response->display();
	}

	public function getFieldsList()
	{
		$results = Db::getInstance()->executeS('
			SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_subscribers_custom_field`
		');

		foreach ($results as $key => $row) 
			$results[$key]['type_name'] = NewsletterProSubscribersCustomField::getTypeName($row['type']);

		return Tools::jsonEncode($results);
	}

	public function changeFieldRequired($id, $bool)
	{
		try
		{
			$field = NewsletterProSubscribersCustomField::newInstance($id);

			if (!Validate::isLoadedObject($field))
				throw new Exception(NewsletterPro::getInstance()->l('Invalid field id.'));

			$field->required = (int)$bool;
			$field->save();
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}
		return $this->response->display();
	}

	public function saveShowColumns($colums)
	{
		try
		{
			$valid_columns = array();
			foreach ($colums as $colum) 
			{
				if (NewsletterProTools::columnExists('newsletter_pro_subscribers', $colum))
					$valid_columns[] = $colum;
			}

			if (!NewsletterPro::updateConfiguration('SHOW_CUSTOM_COLUMNS', $valid_columns))
				throw new Exception(NewsletterPro::getInstance()->l('Unable to save this configuration.'));
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}
		return $this->response->display();
	}

	public function getCustomColumns()
	{	
		try
		{
			$this->response->set('variables', NewsletterProSubscribersCustomField::getVariables());

		// 	'SHOW_CUSTOM_COLUMNS'   => $this->getConfiguration('SHOW_CUSTOM_COLUMNS'),
		// 'variables'        => NewsletterProSubscribersCustomField::getVariables(),

			// $valid_columns = array();
			// foreach ($colums as $colum) 
			// {
			// 	if (NewsletterProTools::columnExists('newsletter_pro_subscribers', $colum))
			// 		$valid_columns[] = $colum;
			// }

			// if (!NewsletterPro::updateConfiguration('SHOW_CUSTOM_COLUMNS', $valid_columns))
			// 	throw new Exception(NewsletterPro::getInstance()->l('Unable to save this configuration.'));
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}
		return $this->response->display();
	}
}