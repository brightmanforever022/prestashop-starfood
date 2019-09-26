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

class NewsletterProProduct
{
	public $product;

	public $images;

	public $attributes_groups;

	public $attributes_combinations;

	public $prices;

	public $prices_attributes;

	public $variables;

	public $variables_lang;

	private $context;

	private $decimals = 2;

	public function __construct($id_product, $id_customer = null, $id_currency = null)
	{
		$this->product = new Product((int)$id_product, true);

		if (!Validate::isLoadedObject($this->product))
			throw new NewsletterProProductException(NewsletterPro::getInstance()->l('The product does not exist.'));

		$this->context = Context::getContext();

		$this->context->currency = (isset($id_currency) ? new Currency((int)$id_currency) : new Currency((int)Configuration::get('CURRENCY')));

		if (!Validate::isLoadedObject($this->context->currency))
			$this->context->currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));

		if (!Validate::isLoadedObject($this->context->currency))
			throw new NewsletterProProductException(NewsletterPro::getInstance()->l('Invalid currency id.'));

		if (isset($id_customer))
			$this->context->customer = new Customer((int)$id_customer);

		$this->images = $this->getImages();
		$this->attributes_groups = $this->getAttributesGroups();
		$this->attributes_combinations = $this->getAttributesCombinations();
		$this->prices = $this->getPrices();
		$this->prices_attributes = $this->getPricesAttribute();
		$this->variables = $this->getVariables();
		$this->variables_lang = $this->getVariablesLang();
	}

	public static function newInstance($id_product, $id_customer = null, $id_currency = null)
	{
		return new self($id_product, $id_customer, $id_currency);
	}

	protected function getPrices()
	{
		$prices = $this->getPricesDefault();

		return $prices;
	}

	protected function getPricesAttribute()
	{
		$prices = array();

		if (isset($this->attributes_groups['attributes_groups']) && isset($this->attributes_groups['attributes_groups'][(int)Configuration::get('PS_LANG_DEFAULT')]))
		{
			$attributes = $this->attributes_groups['attributes_groups'][(int)Configuration::get('PS_LANG_DEFAULT')];
			
			if (is_array($attributes) && $attributes)
			{
				foreach ($attributes as $attribute) 
					$prices[$attribute['id_product_attribute']] = $this->getPricesDefault($attribute['id_product_attribute']);
			}
		}

		return $prices;
	}

	protected function getPricesDefault($id_product_attribute = null)
	{		
		if (isset($this->context->customer) && (int)$this->context->customer->id > 0)
			$price_calculation = Product::getTaxCalculationMethod($this->context->customer->id);
		else
			$price_calculation = Product::getTaxCalculationMethod();

		$currencies = Currency::getCurrencies(true, false, true);

		$prices = array();

		$current_currency = $this->context->currency;

		foreach ($currencies as $currency) 
		{
			$this->context->currency = $currency;

			$price = 0;
			$price_without_reduction = 0;

			if (!$price_calculation || $price_calculation == 2)
			{
				$price = (float)$this->product->getPrice(true, $id_product_attribute);
				$price_without_reduction = (float)$this->product->getPriceWithoutReduct(false, $id_product_attribute);
			}
			else if ($price_calculation == 1)
			{
				$price = (float)$this->product->getPrice(false, $id_product_attribute);
				$price_without_reduction = (float)$this->product->getPriceWithoutReduct(true, $id_product_attribute);
			}

			$price_tax_inc = (float)$this->product->getPrice(true, $id_product_attribute);
			$price_tax_exc = (float)$this->product->getPrice(false, $id_product_attribute);
			$price_without_reduction_tax_inc = (float)$this->product->getPriceWithoutReduct(false, $id_product_attribute);
			$price_without_reduction_tax_exc = (float)$this->product->getPriceWithoutReduct(true, $id_product_attribute);

			$id_country = Configuration::get('PS_COUNTRY_DEFAULT');
			$id_group = Configuration::get('PS_UNIDENTIFIED_GROUP');
			$quantity = 1;

			$this->product->specificPrice = SpecificPrice::getSpecificPrice($this->product->id, $this->context->shop->id, $this->context->currency->id, $id_country, $id_group, $quantity);

			$discount = '';
			$discount_decimals = '';
			$reduction_amount = 0;

			if ($this->product->specificPrice && isset($this->product->specificPrice['reduction_type']))
			{
				$reduction_type = $this->product->specificPrice['reduction_type'];
				$reduction = (float)$this->product->specificPrice['reduction'];

				$reduction = Tools::convertPrice($reduction, $currency->id);

				if ($reduction_type == 'percentage')
				{
					$discount = number_format(abs($reduction * 100), 0).'%';
					$discount_decimals = number_format(abs($reduction * 100), $this->decimals).'%';
					$reduction_amount = $price_without_reduction * $reduction;
				}
				else if ($reduction_type == 'amount')
				{
					$discount = number_format(abs((($reduction / $price_without_reduction) * 100)), 0).'%';
					$discount_decimals = number_format(abs((($reduction / $price_without_reduction) * 100)), $this->decimals).'%';
					$reduction_amount = $reduction;
				}
			}

			$unit_price = ((float)$this->product->unit_price_ratio > 0 ? $price / (float)$this->product->unit_price_ratio : $price);
			$pre_tax_retail_price = $price_tax_exc + $reduction_amount;

			$unity = ((int)$this->product->unity <= 0 ? 1 : (int)$this->product->unity);

			$unity_price_bo = $pre_tax_retail_price / $unity;
			$wholesale_price = Tools::convertPrice($this->product->wholesale_price, $currency->id);
			$ecotax = Tools::convertPrice($this->product->ecotax, $currency->id);
			$additional_shipping_cost = Tools::convertPrice($this->product->additional_shipping_cost, $currency->id);

			$prices[$currency->id] = array(
				'price' => $price,
				'id_currency' => (int)$currency->id,
				'currency_iso_code' => $currency->iso_code,
				'price_display' => Tools::displayPrice($price, $currency->id),
				'price_without_reduction' => $price_without_reduction,
				'price_without_reduction_display' => Tools::displayPrice($price_without_reduction, $currency->id),
				'discount' => $discount,
				'discount_decimals' => $discount_decimals,
				'reduction' => $reduction_amount,
				'reduction_display' => Tools::displayPrice($reduction_amount),
				'price_tax_inc' => $price_tax_inc,
				'price_tax_inc_display' => Tools::displayPrice($price_tax_inc, $currency->id),
				'price_tax_exc' => $price_tax_exc,
				'price_tax_exc_display' => Tools::displayPrice($price_tax_exc, $currency->id),
				'price_without_reduction_tax_inc' => $price_without_reduction_tax_inc,
				'price_without_reduction_tax_inc_display' => Tools::displayPrice($price_without_reduction_tax_inc, $currency->id),
				'price_without_reduction_tax_exc' => $price_without_reduction_tax_exc,
				'price_without_reduction_tax_exc_display' => Tools::displayPrice($price_without_reduction_tax_exc, $currency->id),
				'unit_price' => $unit_price,
				'unit_price_display' => Tools::displayPrice($unit_price, $currency->id),
				'pre_tax_retail_price' => $pre_tax_retail_price,
				'unity_price_bo' => $unity_price_bo,
				'unity_price_bo_display' => Tools::displayPrice($unity_price_bo, $currency->id),
				'wholesale_price' => $wholesale_price,
				'wholesale_price_display' => Tools::displayPrice($wholesale_price, $currency->id),
				'ecotax' => $ecotax,
				'ecotax_display' => Tools::displayPrice($ecotax, $currency->id),
				'currency' => $currency->sign,
				'additional_shipping_cost' => $additional_shipping_cost,
				'additional_shipping_cost_display' => Tools::displayPrice($additional_shipping_cost),
			);
		}

		$this->context->currency = $current_currency;

		return $prices;
	}

	protected function getVariables()
	{
		$id_manufacturer = (int)$this->product->id_manufacturer;

		$variables = array(
			'module_images_path' => NewsletterPro::getInstance()->url_location.'views/img/',
			'id_product' => (int)$this->product->id,
			'id_supplier' => (int)$this->product->id_supplier,
			'id_manufacturer' => (int)$this->product->id_manufacturer,
			'id_category_default' => (int)$this->product->id_category_default,
			'id_shop_default' => (int)$this->product->id_shop_default,
			'manufacturer_name' => $this->product->manufacturer_name,
			'manufacturer_img_link' => '',
			'manufacturer_img' => '',
			'tax_name' => $this->product->tax_name,
			'tax_rate' => $this->product->tax_rate,
			'unit_price_ratio' => (float)$this->product->unit_price_ratio,
			'on_sale' => (int)$this->product->on_sale,
			'online_only' => (int)$this->product->online_only,
			'quantity' => (int)$this->product->quantity,
			'minimal_quantity' => (int)$this->product->minimal_quantity,
			'supplier_reference' => $this->product->supplier_reference,
			'reference' => $this->product->reference,
			'width' => (float)$this->product->width,
			'height' => (float)$this->product->height,
			'depth' => (float)$this->product->depth,
			'weight' => (float)$this->product->weight,
			'quantity_discount' => (int)$this->product->quantity_discount,
			'condition' => $this->product->condition,
			'date_add' => $this->product->date_add,
			'date_upd' => $this->product->date_upd,
		);

		if (method_exists('ImageType', 'getFormatedName'))
		{
			$manu_url = Tools::getHttpHost(true)._THEME_MANU_DIR_;
			$image_name = (!file_exists(_PS_MANU_IMG_DIR_.$id_manufacturer.'-'.ImageType::getFormatedName('medium').'.jpg')) ? $this->context->language->iso_code.'-default-'.ImageType::getFormatedName('medium').'.jpg' : $id_manufacturer.'-'.ImageType::getFormatedName('medium').'.jpg';
			$manufacturer_img_link = $manu_url.$image_name;

			$variables['manufacturer_img_link'] = $manufacturer_img_link;
			$variables['manufacturer_img'] = '<img src="'.$manufacturer_img_link.'" alt="'.$this->product->manufacturer_name.'"/>';
		}

		return $variables;
	}

	protected function getVariablesLang()
	{
		$module = NewsletterPro::getInstance();

		$link = array();
		
		foreach (array_keys($this->product->link_rewrite) as $id_lang)
		{
			$link[$id_lang] = $this->context->link->getProductLink($this->product->id, null, null, null, $id_lang);

			if ((int)pqnp_config('CAMPAIGN_ACTIVE'))
				$link[$id_lang] = $module->setCampaignVariables($link[$id_lang], array('product_name' => $this->product->name[$id_lang]));

			$link[$id_lang] = $module->setStatisticsVariables($link[$id_lang]);
		}

		$description = array();

		foreach ($this->product->description as $id_lang => $value)
			$description[$id_lang] = strip_tags($value);
		
		$description_short = array();

		foreach ($this->product->description_short as $id_lang => $value)
			$description_short[$id_lang] = strip_tags($value);

		$variables = array(
			'description' => $description,
			'description_short' => $description_short,
			'available_now' => $this->product->available_now,
			'available_later' => $this->product->available_later,
			'link_rewrite' => $this->product->link_rewrite,
			'name' => $this->product->name,
			'link' => $link,
		);

		return $variables;
	}

	protected function getImages()
	{
		$languages = Language::getLanguages(false);
		$products_images_type = ImageType::getImagesTypes('products');

		$images = array();
		$product_images = array();
		$cover_images = array();
		$cover = array();

		foreach ($languages as $language)
		{
			$id_lang = (int)$language['id_lang'];
			$images[$id_lang] = $this->product->getImages($id_lang);

			foreach ($images[$id_lang] as $k => $image) 
			{
				if (isset($image['cover']) && (int)$image['cover'])
				{
					$cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($this->product->id.'-'.$image['id_image']) : $image['id_image']);
					$cover['id_image_only'] = (int)$image['id_image'];
				}

				$product_images[$id_lang][(int)$image['id_image']] = $image;
			}

			if (!$cover)
			{
				if (isset($images[$id_lang][0]))
				{
					$cover = $images[$id_lang][0];
					$cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($this->product->id.'-'.$images[$id_lang][0]['id_image']) : $images[$id_lang][0]['id_image']);
					$cover['id_image_only'] = (int)$images[$id_lang][0]['id_image'];
				}
				else 
				{
					$cover = array(
						'id_image' => $language['iso_code'].'-default',
						'legend' => 'No picture',
						'title' => 'No picture'
					);
				}
			}

			foreach ($products_images_type as $type) 
			{
				$cover_images[$id_lang][$type['name']] = Image::getSize($type['name']);
				$cover_images[$id_lang][$type['name']]['id_image'] = $cover['id_image'];
				$cover_images[$id_lang][$type['name']]['link'] = $this->context->link->getImageLink($this->product->link_rewrite[$id_lang], $cover['id_image'], $type['name']);
			}
		}

		$other_images = array();
		foreach ($product_images as $id_lang => $item) 
		{
			foreach ($item as $id_image => $image) 
			{
				if (!$image['cover'])
				{
					foreach ($products_images_type as $type) 
					{
						$other_images[$id_image][$type['name']] = Image::getSize($type['name']);
						$other_images[$id_image][$type['name']]['id_image'] = $image['id_image'];
						$other_images[$id_image][$type['name']]['link'] = $this->context->link->getImageLink($this->product->link_rewrite[$id_lang], $image['id_image'], $type['name']);
					}
				}
			}
		}

		$image_type = pqnp_config('IMAGE_TYPE');

		$image_width_small = null;
		foreach ($products_images_type as $image)
		{
			if (!isset($image_width_small))
			{
				$image_width_small = $image;
				continue;
			}

			if ((int)$image['width'] < $image_width_small['width'])
				$image_width_small = $image;
		}

		return array(
			'image_type_small' => $image_width_small['name'],
			'selected_type' => $image_type,
			'cover_images' => $cover_images,
			'other_images' => $other_images,
		);
	}

	protected function getAttributesGroups()
	{
		$colors = array();
		$groups = array();

		$languages = Language::getLanguages(false);
		$products_images_type = ImageType::getImagesTypes('products');

		$attributes_groups = array();
		$combination_images = array();

		foreach ($languages as $language) 
		{
			$id_lang = (int)$language['id_lang'];
			
			$attributes_groups[$id_lang] = $this->product->getAttributesGroups($id_lang);

			if (is_array($attributes_groups[$id_lang]) && $attributes_groups[$id_lang])
			{
				$combination_images[$id_lang] = $this->product->getCombinationImages($id_lang);

				if (is_array($combination_images[$id_lang]) && $combination_images[$id_lang])
				{
					foreach ($combination_images[$id_lang] as $id_product_attribute => $value) 
					{
						foreach ($value as $key => $v) 
						{
							foreach ($products_images_type as $type) 
							{
								$combination_images[$id_lang][$id_product_attribute][$key]['images'][$type['name']] = Image::getSize($type['name']);
								$combination_images[$id_lang][$id_product_attribute][$key]['images'][$type['name']]['link'] = $this->context->link->getImageLink($this->product->link_rewrite[$id_lang], $v['id_image'], $type['name']);
							}
						}
					}
				}
			}
		}

		return array(
			'attributes_groups' => $attributes_groups,
			'combination_images' => $combination_images,
		);
	}

	protected function getAttributesCombinations()
	{
		$attributes_combinations = Product::getAttributesInformationsByProduct($this->product->id);

		if (is_array($attributes_combinations) && count($attributes_combinations)) 
		{
			foreach ($attributes_combinations as &$ac)
			{
				foreach ($ac as &$val)
					$val = str_replace(Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'), '_', Tools::link_rewrite(str_replace(array(',', '.'), '-', $val)));
			}
		} 
		else 
			$attributes_combinations = array();

		$spearator = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR') ? Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR') : '-';

		return array(
			'attributes_combinations' =>  $attributes_combinations,
			'attribute_anchor_separator' => $spearator
		);
	}

	public function toArray($id_lang = null, $id_currency = null, $id_product_attribute = null)
	{
		if (isset($id_lang))
		{
			$images = array();

			foreach ($this->images as $key => $image) 
			{
				if (in_array($key, array('cover_images', 'other_images')))
				{
					if (isset($image[$id_lang]))
						$images[$key] = $image[$id_lang];
				}
				else
					$images[$key] = $image;
			}

			$attributes_groups = array();
			foreach ($this->attributes_groups as $key => $attribute_group)
			{
				if (isset($attribute_group[$id_lang]))
					$attributes_groups[$key] = $attribute_group[$id_lang];
			}

			$variables_lang = array();

			foreach ($this->variables_lang as $key => $variable_lang)
			{
				if (isset($variable_lang[$id_lang]))
					$variables_lang[$key] = $variable_lang[$id_lang];
			}
		}
		else
		{
			$images = $this->images;
			$attributes_groups = $this->attributes_groups;
			$variables_lang = $this->variables_lang;
		}

		if (isset($id_currency))
		{
			$prices = array();

			if (isset($this->prices[$id_currency]))
				$prices = $this->prices[$id_currency];

			$prices_attributes = array();

			foreach ($this->prices_attributes as $id_product_attribute => $price_attr) 
			{
				if (isset($price_attr[$id_currency]))
					$prices_attributes[$id_product_attribute] = $price_attr[$id_currency];
			}

		}
		else
		{
			$prices = $this->prices;
			$prices_attributes = $this->prices_attributes;
		}

		if (isset($id_product_attribute))
		{
			if (isset($prices_attributes[$id_product_attribute]))
				$prices_attributes = $prices_attributes[$id_product_attribute];
		}

		$variables = array(
			'images' => $images,
			'attributes_groups' => $attributes_groups,
			'attributes_combinations' => $this->attributes_combinations,
			'prices' => $prices,
			'prices_attributes' => $prices_attributes,
			'variables' => $this->variables,
			'variables_lang' => $variables_lang,
		);

		NewsletterProExtendProductVariables::newInstance($variables, $this->product, $this->context);

		return $variables;
	}
}
