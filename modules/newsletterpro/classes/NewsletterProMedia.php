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

class NewsletterProMedia
{

	const CACHE_NAME = 'cache_name';

	const FILES = 'files';

	const FOLDER_NAME = 'cache';

	private $controller;

	private $css;

	private $js;

	public function __construct()
	{
		$this->css = array();
		$this->js = array();
	}

	public static function newInstance()
	{
		return new self();
	}

	public function setController($controller)
	{
		$this->controller = $controller;

		return $this;
	}

	public function addCSS($cache_name, $files)
	{

		$this->css[] = array(
			self::CACHE_NAME => $cache_name,
			self::FILES => $files,
		);

		return $this;
	}

	public function addJS($cache_name, $files)
	{
		$this->js[] = array(
			self::CACHE_NAME => $cache_name,
			self::FILES => $files,
		);

		return $this;
	}

	/**
	 * Load the media files
	 */
	public function load($load_cache = false, $create_cache = false, $css = true, $js = true)
	{

		$module = NewsletterPro::getInstance();

		$dir_css = $module->dir_location.'views/css';
		$path_css = $module->uri_location.'views/css';
		$dir_js = $module->dir_location.'views/js';
		$path_js = $module->uri_location.'views/js';

		if (!isset($this->controller)) {
			throw new Exception('The media controller is not set.');
		}

		if ($create_cache) {
			$this->cache(false);
		}

		// $this->loadFile($load_cache, $this->css, PQCRP_CSS.'/', PQCRP_CSS_PATH, 'addCSS');
		// $this->loadFile($load_cache, $this->js, PQCRP_JS.'/', PQCRP_JS_PATH, 'addJS');
		
		if ($css) {
			$this->loadFile($load_cache, $this->css, $dir_css, $path_css, 'addCSS');
		}

		if ($js) {
			$this->loadFile($load_cache, $this->js, $dir_js, $path_js, 'addJS');
		}

		return $this;
	}

	/**
	 * Create the cache files
	 */
	public function cache($force = false, $css = true, $js = true)
	{
		$module = NewsletterPro::getInstance();

		$dir_css = $module->dir_location.'views/css';
		$path_css = $module->uri_location.'views/css';
		$dir_js = $module->dir_location.'views/js';
		$path_js = $module->uri_location.'views/js';
		// $this->writeCache($this->css, PQCRP_CSS.'/', PQCRP_CSS_PATH, 'addCSS', $force);
		// $this->writeCache($this->js, PQCRP_JS.'/', PQCRP_JS_PATH, 'addJS', $force);
		
		if ($css) {
			$this->writeCache($this->css, $dir_css, $path_css, 'addCSS', $force);
		}

		if ($js) {
			$this->writeCache($this->js, $dir_js, $path_js, 'addJS', $force);
		}

		return $this;
	}

	private function writeCache($data, $dir, $path, $func_name, $force = false)
	{
		$base_name = $_SERVER['DOCUMENT_ROOT'];

		foreach ($data as $data_value) {

			$data_cache_content = array();

			foreach ($data_value[self::FILES] as $file) {


				$data_filename = $base_name.$file;


				if (file_exists($data_filename)) {

					$content = Tools::file_get_contents($data_filename);

					if ($func_name == 'addCSS') {
						$file_trim = ltrim(str_replace(array('/', '//', '\\', '\\\\'), '/', $file), '/');
						$depth = substr_count($file_trim, '/');

						if ($depth == 0) {
							$content = preg_replace('/(url\()((.*)?\))/', '$1../$2', $content);
						} else if ($depth >= 2) {
							$content = preg_replace('/(url\()(\.\.\/)+(.*)?(\))/', '$1../../$3$4', $content);							
						}
					}

					$data_cache_content[] = $content;
				}
			}

			$data_cache_content = implode("\n", $data_cache_content);

			$cache_filename = $this->getCacheFilename($dir, $data_value[self::CACHE_NAME]);
			$cache_path = $this->getCachePath($path, $data_value[self::CACHE_NAME]);

			if (function_exists('mb_strlen')) {
				if (!$this->cacheFileExists($cache_filename)) {
					file_put_contents($cache_filename, $data_cache_content);
				} else if (mb_strlen($data_cache_content, '8bit') !== filesize($cache_filename)) {
					file_put_contents($cache_filename, $data_cache_content);
				} else if ($force) {
					file_put_contents($cache_filename, $data_cache_content);
				}
			} else {
				if (!$this->cacheFileExists($cache_filename)) {
					file_put_contents($cache_filename, $data_cache_content);
				} else if ($force) {
					file_put_contents($cache_filename, $data_cache_content);
				}
			}
		}
	}

	private function loadFile($load_cache, $data, $dir, $path, $func_name)
	{
		if ($load_cache) {
			foreach ($data as $data_value) {

				$cache_filename = $this->getCacheFilename($dir, $data_value[self::CACHE_NAME]);
				$cache_path = $this->getCachePath($path, $data_value[self::CACHE_NAME]);

				$file_exists = $this->cacheFileExists($cache_filename);

				if ($file_exists && is_readable($cache_filename)) {

					$info = pathinfo($cache_filename);

					$min_name = $info['filename'].'.min.'.$info['extension'];
					$min_filename = $info['dirname'].'/'.$min_name;

					if (file_exists($min_filename) && is_readable($min_filename) && filemtime($min_filename) >= filemtime($cache_filename)) {
						$min_cache_path = $this->getCachePath($path, $min_name);

						$this->controller->{$func_name}($min_cache_path);
					} else {
						$this->controller->{$func_name}($cache_path);
					}
				} else {
					foreach ($data_value[self::FILES] as $file) {
						$this->controller->{$func_name}($file);
					}
				}
			}
		} else {
			foreach ($data as $data_value) {
				foreach ($data_value[self::FILES] as $file) {
					$this->controller->{$func_name}($file);
				}
			}
		}

	}

	private function cacheFileExists($filename)
	{
		return file_exists($filename);
	}

	private function getCacheFilename($dir, $cache_name)
	{
		return $dir.'/'.self::FOLDER_NAME.'/'.$cache_name;
	}

	private function getCachePath($path, $cache_name)
	{
		return $path.'/'.self::FOLDER_NAME.'/'.$cache_name;
	}
}

