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

class NewsletterProAutoload
{
	private static $instance;

	protected $classes_path;

	protected $controllers_path;
	
	protected $libraries_path;

	public function __construct()
	{
		if (!isset(self::$instance)) {
			self::$instance =& $this;
		}

		$this->classes_path = _NEWSLETTER_PRO_DIR_.'/classes/';
		$this->controllers_path = _NEWSLETTER_PRO_DIR_.'/controllers/';
		$this->libraries_path = _NEWSLETTER_PRO_DIR_.'/libraries/';
		$this->exceptions_path = _NEWSLETTER_PRO_DIR_.'/classes/exceptions/';
	}

	public static function newInstance()
	{
		return new self();
	}

	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init()
	{
		spl_autoload_register(array($this, 'loadClasses'));
		spl_autoload_register(array($this, 'loadControllers'));
		spl_autoload_register(array($this, 'loadLibraries'));
		spl_autoload_register(array($this, 'loadExceptions'));
	}

	public function loadClasses($class)
	{	
		if ($class) {
			$filename = $this->classes_path.$class.'.php';
			if (file_exists($filename)) {
				require_once $filename;
			}
		}
	}

	public function loadControllers($class)
	{
		if ($class) {
			$filename = $this->controllers_path.$class.'.php';
			if (file_exists($filename)) {
				require_once $filename;
			}
		}
	}

	public function loadLibraries($class)
	{
		if ($class) {
			$filename = $this->libraries_path.$class.'.php';
			if (file_exists($filename)) {
				require_once $filename;
			}
		}
	}

	public function loadExceptions($class)
	{
		if ($class) {
			$filename = $this->exceptions_path.$class.'.php';
			if (file_exists($filename)) {
				require_once $filename;
			}
		}
	}
}