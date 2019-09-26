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

class NewsletterProCustomerContext
{
	public static $currencies;

	public static function getContext($id_customer = null)
	{
		$context = Context::getContext();

		$context->customer = new Customer((int)$id_customer);
		if (Validate::isLoadedObject($context->customer))
		{
			if (isset($context->customer->id_shop))
				$context->shop = new Shop((int)$context->customer->id_shop);

			if (isset($context->customer->id_lang))
				$context->language = new Language((int)$context->customer->id_lang);

			$currency = Currency::getDefaultCurrency();
			if (Validate::isLoadedObject($currency))
				$context->currency = $currency;

			if (!isset($context->cart))
				$context->cart = new Cart();

			$id_country = (int)Customer::getCurrentCountry((int)$context->customer->id);

			if ($id_country)
				$context->country = new Country($id_country);
		}

		return $context;
	}

	public static function getCurrenciesByIdShop($id_shop = 0)
	{
		if (!isset(self::$currencies))
			self::$currencies = NewsletterPro::getCurrenciesByIdShop($id_shop);

		return self::$currencies;
	}
}