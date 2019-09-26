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

class Shop extends ShopCore
{
	public static function addTableAssociationNewsletterPro($table_name, $table_details)
	{
		if (!isset(Shop::$asso_tables[$table_name]))
			Shop::$asso_tables[$table_name] = $table_details;
		else
			return false;
		return true;
	}

	public static function getContextShopIDNewsletterPro($null_value_without_multishop = false)
	{
		if ($null_value_without_multishop && !Shop::isFeatureActive())
			return null;

		return Context::getContext()->shop->id;
	}

	public static function getContextShopGroupIDNewsletterPro($null_value_without_multishop = false)
	{
		if ($null_value_without_multishop && !Shop::isFeatureActive())
			return null;

		return Context::getContext()->shop->id_shop_group;
	}

	public static function isTableAssociatedNewsletterPro($table)
	{
		return isset(Shop::$asso_tables[$table]) && Shop::$asso_tables[$table]['type'] == 'shop';
	}
}