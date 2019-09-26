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

class NewsletterProExtendProductVariables
{

	private $module;

	private $images;

	private $attributes_groups;

	private $attributes_combinations;

	private $prices;

	private $prices_attributes;

	private $variables;

	private $variables_lang;

	public function __construct(&$params, $product, $context)
	{
		$this->module                  = NewsletterPro::getInstance();

		$this->images                  = &$params['images'];
		$this->attributes_groups       = &$params['attributes_groups'];
		$this->attributes_combinations = &$params['attributes_combinations'];
		$this->prices                  = &$params['prices'];
		$this->prices_attributes       = &$params['prices_attributes'];
		$this->variables               = &$params['variables'];
		$this->variables_lang          = &$params['variables_lang'];

		// Variables
		$new_variables = $this->extendVariables($this->variables);

		if (!empty($new_variables)) {
			foreach ($new_variables as $key => $value) {
				$this->variables[$key] = $value;
			}
		}

		// Variables Lang
		$languages = Language::getLanguages(false);

		$new_variables = array();
		foreach ($languages as $lang) {
			$new_variables[$lang['id_lang']] = $this->extendVariablesLang($lang);
		}

		if (!empty($new_variables)) {
			foreach ($new_variables as $id_lang => $content) {
				foreach ($content as $variable_name => $value) {
					$this->variables_lang[$variable_name][$id_lang] = $value;
				}
			}
		}

		// Prices
		$currencies = Currency::getCurrencies(true, false, true);

		$new_variables = array();
		foreach ($currencies as $currency) {
			$new_variables[$currency->id] = $this->extendPrices($currency, $this->prices[$currency->id]);
		}

		if (!empty($new_variables)) {
			foreach ($new_variables as $id_currency => $content) {
				foreach ($content as $variable_name => $value) {
					$this->prices[$id_currency][$variable_name] = $value;
				}
			}
		}
	}

	public static function newInstance(&$params, $product, $context)
	{
		return new self($params, $product, $context);
	}

	/**
	 * Here you can extend the variables
	 */
	private function extendVariables()
	{
		$new_variables = array();

		$new_variables['hello_world'] = 'Hello World!';

		return $new_variables;
	}

	/**
	 * Here you can extend the language variables
	 */
	private function extendVariablesLang(array $lang)
	{
		$new_variables = array();

		if ($lang['iso_code'] == 'en') {
			$new_variables['hello_world_lang'] = 'Hello World!';
		} else {
			$new_variables['hello_world_lang'] = 'Hello World Translated!';
		}

		return $new_variables;
	}


	/**
	 * Here you can extend the price variables
	 */
	private function extendPrices(Currency $currency, $price_variables)
	{
		$new_variables = array();

		$new_variables['hello_world_price'] = 'Just for test - '.$price_variables['price_display'];

		return $new_variables;
	}

}