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

class NewsletterProFiltersSelection extends ObjectModel
{
	public $name;

	public $value;

	public static $definition = array(
		'table'   => 'newsletter_pro_filters_selection',
		'primary' => 'id_newsletter_pro_filters_selection',
		'fields'  => array(
			'name'  => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
			'value' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
		)
	);
	
	public static function newInstance($id = null)
	{
		return new self($id);
	}

	public function nameExists()
	{
		return (bool)Db::getInstance()->getValue('
			SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_filters_selection`
			WHERE `name` = "'.pSQL($this->name).'"
		');
	}

	public static function getFilters()
	{
		return Db::getInstance()->executeS('
			SELECT `id_newsletter_pro_filters_selection`, `name` 
			FROM `'._DB_PREFIX_.'newsletter_pro_filters_selection`
		');
	}
}