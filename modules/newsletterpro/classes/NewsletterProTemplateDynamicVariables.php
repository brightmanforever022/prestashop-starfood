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

class NewsletterProTemplateDynamicVariables
{
	private $html;

	private $user;

	private $variables_data;

	public function __construct($html, $user)
	{
		$this->html = $html;
		$this->user = $user;
	}

	public static function newInstance($html, $user)
	{
		return new self($html, $user);
	}

	public function render()
	{
		$this->initVariablesData();

		$render = $this->html;

		foreach ($render as $id_lang => $html)
		{
			if (preg_match('/\{\$\w+\}/', $html))
			{
				$current_encode = mb_detect_encoding($html, 'UTF-8, ISO-8859-1, ISO-8859-15, HTML-ENTITIES', true);
				// this will solve the trim last row bug in phpQuery plugin
				$html = html_entity_decode($html, ENT_COMPAT | ENT_HTML401 | ENT_HTML5, $current_encode);
				$html = mb_convert_encoding($html, 'UTF-8', $current_encode);

				$doc = NewsletterProphpQuery::newDocumentHTML($html);

				$dom_products = pq_newsletterpro('.newsletter-pro-product');
				// this part is not required on the send email process but at the moment is working
				$dom_body = pq_newsletterpro('body');

				$dom_container = pq_newsletterpro('.newsletter-pro-container');
				
				if (count($dom_container) > 0 && count($dom_body) > 0)
				{
					$style = $dom_container->attr('style');
					$exp = explode(';', $style);
					$gr = preg_grep('/background-color|background/', $exp);
					if (count($gr) > 0)
					{
						$bg = trim($gr[key($gr)]);

						$last_char = Tools::substr($bg, -1);
						if ($last_char != ';')

						$bg .= ';';
					}

					if (isset($bg))
					{
						$body_style = trim($dom_body->attr('style'));

						$last_char = Tools::substr($body_style, -1);
						if ($last_char != ';')
							$body_style .= ';';

						$body_style .= $bg;

						$dom_body->attr('style', ltrim($body_style, ';'));
					}
				}

				foreach ($dom_products as $dom_product)
				{
					$id_product = (int)pq_newsletterpro($dom_product)->attr('data-id');
					$html_product = pq_newsletterpro($dom_product)->html();

					preg_match_all('/\{(?P<var>\$[^}]+)\}/', $html_product, $available_vars);

					$variables = $this->variables($id_product, $available_vars['var'], $id_lang);

					// if the id_product doesn't exist in database, the template variables will become an empty string
					if (empty($variables) && !empty($available_vars['var']))
						$html_product_replace = preg_replace('/\{(?P<var>\$[^}]+)\}/', '', $html_product);
					else
						$html_product_replace = str_replace($this->getVariablesKyes($variables), array_values($variables), $html_product);

					pq_newsletterpro($dom_product)->html($html_product_replace);
				}

				$html = $doc->htmlOuter();

				$html = html_entity_decode(str_replace(array('%7B', '%7D', '%7C', '%2A'), array('{', '}', '|', '*'), $html));

				$render[$id_lang] = $html;
			}
		}

		return $render;
	}

	private function variables($id_product, $available_vars, $id_lang)
	{
		$module = NewsletterPro::getInstance();

		$variables = array();

		if (!empty($available_vars))
		{
			if (!isset($this->variables_data))
				throw new NewsletterProTemplateDynamicVariablesException(sprintf('The variabl "%s" is not defined.', 'variables_data'));

			$dinamyc_vars = array();

			$len = 250;
			$end = '...';

			foreach ($available_vars as $value)
			{
				$exp_currency = explode('|', Tools::strtoupper($value));
				$exp_language = explode('|', Tools::strtolower($value));
				$exp = explode('|', $value);
				$var = $exp[0];

				$current_currency = array_intersect($this->variables_data['currencies_iso'], $exp_currency);

				$dinamyc_vars[$value]['replace']     = $value;
				$dinamyc_vars[$value]['var']         = $var;
				$dinamyc_vars[$value]['id_currency'] = $this->variables_data['id_currency'];
				$dinamyc_vars[$value]['id_lang']     = $id_lang;

				if (!empty($current_currency))
				{
					$dinamyc_vars[$value]['type'] = 'price';

					$current_currency = array_values($current_currency);
					$iso_code = $current_currency[0];
					$get_currency = array_search($iso_code, $this->variables_data['currencies_iso']);
					$dinamyc_vars[$value]['id_currency'] = ($get_currency !== false ? (int)$get_currency : $this->variables_data['id_currency']);

					$dinamyc_vars[$value]['type'] = 'string';
					$dinamyc_vars[$value]['trim_length'] = $len;
					$dinamyc_vars[$value]['trim_end'] = $end;

				}
				else
					$dinamyc_vars[$value]['type'] = null;

				$current_lang = array_intersect($this->variables_data['languages_iso'], $exp_language);

				if (!empty($current_lang))
				{
					$current_lang = array_values($current_lang);
					$iso_code = $current_lang[0];

					$get_lang = array_search($iso_code, $this->variables_data['languages_iso']);

					$dinamyc_vars[$value]['id_lang'] = ($get_lang !== false ? (int)$get_lang : $id_lang);
				}
			}

			$product = $module->getProductById($id_product, $id_lang);

			if ($product && isset($product['dynamic_vars']))
			{
				$product_lang = $this->getProductLang($id_product, $this->variables_data, $product);

				$variables =& $product['dynamic_vars'];

				$variables['$currency']                = $this->variables_data['currencies_key_ids'][$this->variables_data['id_currency']]['sign'];
				$variables['$price_convert']           = (pqnp_ini_config('price_convert') 
															? Tools::convertPrice((float)$product['price'], $this->variables_data['id_currency'])
															: (float)$product['price']
														);
				$variables['$price_display']           = Tools::displayPrice((float)$variables['$price_convert'], $this->variables_data['id_currency']);

				$variables['$price_without_reduction']         = $product['price_without_reduction'];
				$variables['$price_without_reduction_convert'] = (pqnp_ini_config('price_convert') 
																	? Tools::convertPrice((float)$variables['$price_without_reduction'], (int)$this->variables_data['id_currency'])
																	: (float)$variables['$price_without_reduction']
																);
				$variables['$price_without_reduction_display'] = Tools::displayPrice((float)$variables['$price_without_reduction_convert'], (int)$this->variables_data['id_currency']);

				$variables['$price_tax_exc']         = $product['price_tax_exc'];
				$variables['$price_tax_exc_convert'] = (pqnp_ini_config('price_convert')
															? Tools::convertPrice((float)$variables['$price_tax_exc'], (int)$this->variables_data['id_currency']) 
															: (float)$variables['$price_tax_exc']
														);
				$variables['$price_tax_exc_display'] = Tools::displayPrice((float)$variables['$price_tax_exc_convert'], (int)$this->variables_data['id_currency']);

				$variables['$wholesale_price']         = $product['wholesale_price'];
				$variables['$wholesale_price_convert'] = (pqnp_ini_config('price_convert')
															? Tools::convertPrice((float)$variables['$wholesale_price'], (int)$this->variables_data['id_currency'])
															: (float)$variables['$wholesale_price']
														);
				$variables['$wholesale_price_display'] = Tools::displayPrice((float)$variables['$wholesale_price_convert'], (int)$this->variables_data['id_currency']);

				$decimal = 2;
				foreach ($dinamyc_vars as $replace => $settings)
				{
					$target = $settings['var'];
					switch ($target)
					{
						case '$currency':
								$variables[$replace] = $this->variables_data['currencies_key_ids'][$settings['id_currency']]['sign'];
							break;

						case '$price_convert':
								$variables[$replace] = number_format(pqnp_ini_config('price_convert')
															? Tools::convertPrice((float)$product['price'], $settings['id_currency'])
															: (float)$product['price'],
														$decimal);
							break;

						case '$price_display':
								$price_convert  = (pqnp_ini_config('price_convert') 
														? Tools::convertPrice((float)$product['price'], $settings['id_currency'])
														: (float)$product['price']
													);
								$variables[$replace] = Tools::displayPrice((float)$price_convert, $settings['id_currency']);
							break;

						case '$price_without_reduction_convert':
								$variables[$replace] = number_format(pqnp_ini_config('price_convert') 
															? Tools::convertPrice((float)$variables['$price_without_reduction'], (int)$settings['id_currency'])
															: (float)$variables['$price_without_reduction'],
														$decimal);
							break;

						case '$price_without_reduction_display':
								$price_without_reduction_convert = (pqnp_ini_config('price_convert')
																		? Tools::convertPrice((float)$variables['$price_without_reduction'], (int)$settings['id_currency'])
																		: (float)$variables['$price_without_reduction']
																	);
								$variables[$replace]                  = Tools::displayPrice((float)$price_without_reduction_convert, (int)$settings['id_currency']);
							break;

						case '$price_tax_exc_convert':
								$variables[$replace] = number_format(pqnp_ini_config('price_convert')
															? Tools::convertPrice((float)$variables['$price_tax_exc'], (int)$settings['id_currency'])
															: (float)$variables['$price_tax_exc'],
														$decimal);
							break;

						case '$price_tax_exc_display':
								$price_tax_exc_convert = Tools::convertPrice((float)$variables['$price_tax_exc'], (int)$settings['id_currency']);
								$variables[$replace]        = Tools::displayPrice((float)$price_tax_exc_convert, (int)$settings['id_currency']);
							break;

						case '$wholesale_price_convert':
								$variables[$replace] = number_format(pqnp_ini_config('price_convert')
															? Tools::convertPrice((float)$variables['$wholesale_price'], (int)$settings['id_currency'])
															: (float)$variables['$wholesale_price'],
														$decimal);
							break;

						case '$wholesale_price_display':
								$wholesale_price_convert = (pqnp_ini_config('price_convert')
																? Tools::convertPrice((float)$variables['$wholesale_price'], (int)$settings['id_currency'])
																: (float)$variables['$wholesale_price']
															);
								$variables[$replace]          = Tools::displayPrice((float)$wholesale_price_convert, (int)$settings['id_currency']);
							break;

						case '$name':
								$this->setVariablesString($replace, 'name', $variables, $product_lang, $settings);
							break;

						case '$description':
								$this->setVariablesString($replace, 'description', $variables, $product_lang, $settings);
							break;

						case '$description_short':
								$this->setVariablesString($replace, 'description_short', $variables, $product_lang, $settings);
							break;

						case '$manufacturer_name':
							$this->setVariablesString($replace, 'manufacturer_name', $variables, $product_lang, $settings);
							break;

						default:
							break;
					}
				}
			}
		}

		return $variables;
	}

	private function initVariablesData()
	{
		$module = NewsletterPro::getInstance();
		$context = Context::getContext();

		$id_currency = (int)$module->getConfiguration('CURRENCY');
		$id_lang     = (int)$module->getConfiguration('LANG');
		$id_shop     = (int)$context->shop->id;

		if (property_exists($context, 'customer') && isset($context->customer) && $context->customer)
		{
			if (property_exists($context->customer, 'id_lang') && isset($this->customer->id_lang))
				$id_lang = (int)$context->customer->id_lang;
			else
			{
				$c_id_lang = $module->getCustomerIdLang($context->customer->id);

				if ($c_id_lang)
					$id_lang = $c_id_lang;
			}

			$id_shop = (int)$context->customer->id_shop;
		}

		// override the id lang
		$id_lang = (int)$this->user->id_lang;

		// override the id shop
		$id_shop = (int)$this->user->id_shop;

		$currencies = NewsletterPro::getCurrenciesByIdShop($context->shop->id);

		$currencies_iso = array();
		$currencies_key_ids = array();

		foreach ($currencies as $currency)
		{
			$currencies_iso[$currency['id_currency']]     = $currency['iso_code'];
			$currencies_key_ids[$currency['id_currency']] = $currency;
		}

		$languages = Language::getLanguages(false);
		$languages_iso = array();
		$languages_key_ids = array();

		foreach ($languages as $language)
		{
			$languages_iso[$language['id_lang']]     = $language['iso_code'];
			$languages_key_ids[$language['id_lang']] = $language;
		}

		$this->variables_data = array(
			'id_currency'        => $id_currency,
			'id_shop'            => $id_shop,
			'currencies'         => $currencies,
			'currencies_iso'     => $currencies_iso,
			'currencies_key_ids' => $currencies_key_ids,
			'languages'          => $languages,
			'languages_iso'      => $languages_iso,
			'languages_key_ids'  => $languages_key_ids,
		);

		return $this;
	}

	private function getProductLang($id_product, $product)
	{
		$product_db_lang = Db::getInstance()->executeS('
			SELECT 
				p.`id_product`, 
				pl.`id_lang`, 
				pl.`name`, 
				pl.`description`, 
				pl.`description_short`, 
				m.`name` AS manufacturer_name
			FROM `'._DB_PREFIX_.'product` p
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product`)
			LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (p.`id_manufacturer` = m.`id_manufacturer`)
			WHERE p.`id_product` = '.(int)$id_product.'
			AND pl.`id_shop` = '.(int)$this->variables_data['id_shop'].'
		');

		$product_lang = array();

		if (!empty($product_db_lang))
		{
			foreach ($product_db_lang as $pl)
				$product_lang[$pl['id_lang']] = $pl;
		}
		else
		{
			foreach ($this->variables_data['languages_key_ids'] as $pl)
			{
				$product_lang[$pl['id_lang']] = array(
					'name'              => $product['name'],
					'description'       => $product['description'],
					'description_short' => $product['description_short'],
					'manufacturer_name' => $product['manufacturer_name'],
				);
			}
		}

		return $product_lang;
	}

	private function getVariablesKyes($variables)
	{
		$result = array();
		foreach (array_keys($variables) as $var_name)
			$result[] = '{'.$var_name.'}';
		return $result;
	}

	public function setVariablesString($name, $product_name, &$variables, $product_lang, $settings)
	{
		$variables[$name] = strip_tags($product_lang[$settings['id_lang']][$product_name]);

		if ($settings['type'] == 'string')
		{
			$init_name = $variables[$name];
			if (isset($settings['trim_length']))
				$variables[$name] = Tools::substr($variables[$name], 0, $settings['trim_length']);

			if (Tools::strlen($init_name) >= $settings['trim_length'])
				$variables[$name] = $variables[$name].$settings['trim_end'];
		}
	}
}