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

if (!function_exists('pqnp_log'))
{
	function pqnp_log()
	{
		return NewsletterProLog::newInstance();
	}
}

if (!function_exists('pqnp_config'))
{
	function pqnp_config($name, $value = null, $write = false)
	{
		if (!isset($value) && !$write)
			return NewsletterPro::getConfiguration($name);
		else if ($write)
			return NewsletterPro::writeConfiguration($name, $value);
		else
			return NewsletterPro::updateConfiguration($name, $value);
	}
}

if (!function_exists('pqnp_ini_config'))
{
	function pqnp_ini_config($name)
	{
		return NewsletterPro::getInstance()->ini_config[$name];
	}
}

if (!function_exists('pqnp_demo_mode'))
{
	function pqnp_demo_mode($name)
	{
		return NewsletterPro::getInstance()->demo_mode[$name];
	}
}

if (!function_exists('pqnp_addcslashes'))
{
	function pqnp_addcslashes($str)
	{
		return NewsletterProTools::addCShashes($str);
	}
}

if (!function_exists('pqnp_template_path'))
{
	function pqnp_template_path($path)
	{
		return NewsletterProTools::getTemplatePath($path);
	}
}