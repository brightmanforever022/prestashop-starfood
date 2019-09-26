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

class NewsletterProExtendTemplateVars
{
	public static $external_vars = array(
		'variables/last_delivery_address/last_delivery_address.php' => false,
		'variables/gender/gender.php' => false,
	);

	public static function newInstance()
	{
		return new self();
	}

	/**
	* Here you can process the current user information to create your newsletter template variables
	* You can work aslo with database
	*
	* assign variables as object :
	* $user->hello_world = 'Hello World!';
	* The variable name will be {hello_world}
	*/

	public function set($user)
	{
		$user->hello_world = 'Hello World!';
		$this->loadExternalVars($user);
		return $this;
	}

	/**
	* The user parameter is used in the include path
	*/
	public function loadExternalVars(&$user)
	{
		try
		{
			$declared_classes = get_declared_classes();

			foreach (self::$external_vars as $path => $to_load)
			{
				$path = dirname(dirname(__FILE__)).'/'.$path;

				if (file_exists($path) && $to_load)
				{
					$pathinfo = pathinfo($path);
					$variable_name = $pathinfo['filename'];
					$class_name_array = explode('_', $variable_name);
					$class_name = '';
					foreach ($class_name_array as $value)
						if (trim($value) !== '')
							$class_name .= Tools::ucfirst($value);

					$class_name = 'NewsletterProTemplateVariable'.$class_name;

					if (preg_match('/^[A-Za-z0-9-]+$/', $class_name) && !class_exists($class_name) && !in_array($class_name, $declared_classes))
					{
						include $path;

						if (class_exists($class_name))
						{
							$declared_classes[] = $class_name;
							new $class_name($user);
						}
					}

				}
			}
		}
		catch(Exception $e)
		{
			NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
		}
	}
}
?>