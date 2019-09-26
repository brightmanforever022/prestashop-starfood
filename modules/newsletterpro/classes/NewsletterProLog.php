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

class NewsletterProLog
{
	const MAX_FILE_SIZE = 10485760;
	const ERROR_FILE    = 'errors.log';
	const LOG_FILE      = 'info.log';
	const SEND_FILE     = 'send.log';

	private $visible = false;

	private $log = array();

	public static function newInstance()
	{
		return new self();
	}

	public function show()
	{
		$this->visible = true;
		return $this;
	}

	public function hide()
	{
		$this->visible = false;
		return $this;
	}

	public function visible($bool)
	{
		$this->visible = (bool)$bool;
		return $this;
	}

	public function add($string)
	{
		if ($this->visible)
			$this->log[] = $string;
		return $this;
	}

	public function display($separator = '<br>')
	{
		return implode($separator, $this->log);
	}

	public function clearLog()
	{
		$this->log = array();
		return $this;
	}

	public function displayFlush()
	{
		if ($this->visible)
		{
			@ini_set('output_buffering', 'on');
			@ini_set('zlib.output_compression', 0);
			@ob_implicit_flush(true);
			@ob_end_flush();

			echo $this->display("\n");
			$this->clearLog();

			@ob_flush();
			@flush();
		}
	}

	public function writeSend()
	{
		if ($this->visible)
		{
			$content = $this->display("\n");

			if (!empty($this->log))
				self::write($content, self::SEND_FILE);
			
			$this->clearLog();
		}
	}

	public static function clearSend()
	{
		self::clear(self::SEND_FILE);
	}

	public static function write($content, $filename = null, $filesize = null, $content_only = false)
	{
		$status = array();

		if (is_array($content))
		{
			foreach ($content as $cont)
				$status[] = @self::writeString($cont, $filename, $filesize, $content_only);
		}
		else
			$status[] = @self::writeString($content, $filename, $filesize, $content_only);

		return !in_array(false, $status);
	}

	public static function writeStrip($content, $filename = null, $filesize = null)
	{
		return @self::write(strip_tags($content), $filename, $filesize);
	}

	public static function writeString($content, $filename = null, $filesize = null, $content_only = false)
	{
		$success = false;

		if (!isset($filename))
			$filename = self::LOG_FILE;

		$filename = self::getLogDir().'/'.$filename;

		if (!isset($filesize))
			$filesize = self::MAX_FILE_SIZE;

		if (filesize($filename) > $filesize)
			@file_put_contents($filename, '');

		$handle = @fopen($filename, 'a+');
		if ($handle !== false)
		{
			if ($content_only)
			{
				$str = '';
				$str .= $content;
				$str .= "\r\n";
			}
			else
			{
				$str = '';
				if (isset($_SERVER['REMOTE_ADDR']))
					$str .= date('Y-m-d H:i:s').' ['.$_SERVER['REMOTE_ADDR'].'] > ';
				else
					$str .= date('Y-m-d H:i:s').' > ';
				$str .= $content;
				$str .= "\r\n";
			}

			if (@fwrite($handle, $str) !== false)
			$success = true;
		}

		@fclose($handle);

		return $success;
	}

	public static function clear($filename = null)
	{
		if (!isset($filename))
			$filename = self::LOG_FILE;

		$filename = self::getLogDir().'/'.$filename;
		if (file_exists($filename))
		{
			if (@file_put_contents($filename, '') === false)
				return false;
		}

		return true;
	}

	public static function getLogDir()
	{
		return realpath(dirname(__FILE__).'/../logs' );
	}

	public static function getLogFile($filename)
	{
		return self::getLogDir().'/'.$filename;
	}

	public static function getFiles()
	{
		return array(
			self::LOG_FILE,
			self::ERROR_FILE,
			self::SEND_FILE
		);
	}
}