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

class NewsletterProShutdown
{
	private static $call_registred = false;

	public static $callbacks = array();

	const KEY_CALLBACK = 0;

	const KEY_FUNCTION_NAME = 1;

	const KEY_FUNCTION_ARGS = 3;

	/**
	 * @param  Don't use exit in the registred shut down functions
	 */
	public static function register($array, $args = array())
	{
		if (!is_callable($array))
			throw new InvalidArgumentException('Invalid method arguments.');

		$callback = $array[0];
		$function_name = $array[1];

		$key = self::getKey($array);

		self::$callbacks[$key] = array(
			self::KEY_CALLBACK      => $callback, 
			self::KEY_FUNCTION_NAME => $function_name, 
			self::KEY_FUNCTION_ARGS => $args,
		);


		if (!self::$call_registred)
			register_shutdown_function(array('NewsletterProShutdown', 'call'));
	}

	public static function unregister($array)
	{
		$key = self::getKey($array);

		if (isset(self::$callbacks[$key]))
			unset(self::$callbacks[$key]);
	}

	private static function getKey($array)
	{
		if (!is_callable($array))
			throw new InvalidArgumentException('Invalid method arguments.');

		$callback = $array[0];
		$function_name = $array[1];

		$key = get_class($callback).'.'.$function_name;
		return $key;
	}

	public static function call()
	{
		if (count(self::$callbacks))
		{
			foreach (self::$callbacks as $key => $params) 
			{
				call_user_func_array(array($params[self::KEY_CALLBACK], $params[self::KEY_FUNCTION_NAME]), $params[self::KEY_FUNCTION_ARGS]);
				unset(self::$callbacks[$key]);
			}
		}
	}
}