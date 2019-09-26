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

class NewsletterProMailChimpApi
{
	public $key;
	public $url = 'https://<dc>.api.mailchimp.com/2.0/';
	public $verify_ssl = false;

	public $errors = array();
	public $module;

	public function __construct($key, $url = null)
	{
		$this->module = NewsletterPro::getInstance();

		if (isset($url))
			$this->url = $url;

		$this->key = $key;
		$exp = explode('-', $this->key);

		if (count($exp) > 1)
			list(, $dc) = explode('-', $this->key);
		else
			$dc = '';

		$this->url = str_replace('<dc>', $dc, $this->url);
	}

	public function clearErrors()
	{
		$this->errors = array();
	}

	public function call($method, $params = array())
	{
		$this->clearErrors();
		$content = $this->request($method, $params);

		if ($content === false)
			return false;

		if ($this->hasErrors())
			return false;

		$content = Tools::jsonDecode($content['content'], true);

		if (empty($content))
		{
			$this->addError('MailChimp response is empty.');
			return false;
		}
		else if (isset($content['status']) && $content['status'] == 'error')
		{
			$this->addError($content['error']);
			return false;
		}

		if (isset($content['errors']) && !empty($content['errors']))
			$this->addResponseErrors($content['errors']);

		return $content;
	}

	public function request($method, $params = array())
	{
		if (function_exists('curl_init'))
		{
			$params['apikey'] = $this->key;
			$url = $this->url.$method.'.json';

			$options = array(
				CURLOPT_HTTPHEADER     => array('Content-Type: application/json'),
				CURLOPT_USERAGENT      => 'PHP-MCAPI/2.0',
				CURLOPT_CUSTOMREQUEST  => 'POST',
				CURLOPT_POSTFIELDS     => Tools::jsonEncode($params),
				CURLOPT_POST           => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER         => false,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_ENCODING       => '',
				CURLOPT_AUTOREFERER    => true,
				CURLOPT_CONNECTTIMEOUT => 120,
				CURLOPT_TIMEOUT        => 120,
				CURLOPT_MAXREDIRS      => 10,
				CURLOPT_SSL_VERIFYPEER => $this->verify_ssl,
			);

			$ch      = curl_init( $url );
			curl_setopt_array( $ch, $options );
			$content = curl_exec( $ch );
			$err     = curl_errno( $ch );
			$errmsg  = curl_error( $ch );
			$info    = curl_getinfo( $ch );

			$info['errno']   = $err;
			$info['errmsg']  = $errmsg;
			$info['content'] = $content;

			if ((int)$info['http_code'] != 200)
			{
				$response = Tools::jsonDecode($content, true);

				if (isset($response['status']) && $response['status'] == 'error')
					$this->addError($response['error']);
				else
					$this->addError('The HTTP response code is not 200.');
			}

			return $info;
		}
		else
			$this->addError(sprintf($this->module->l('The availability of php %s library is not available on your server. You can talk with the hosting provider to enable it.'), 'curl'));

		return false;
	}

	public function addError($error, $code = null)
	{
		$add_error = array(
			'code' => $code,
			'error' => Tools::displayError($error),
		);

		$this->errors[] = $add_error;
	}

	public function hasErrors()
	{
		return !empty($this->errors);
	}

	public function getErrors($only_errors = false, $collapse_same_code = false)
	{
		$errors = $this->errors;

		if ($collapse_same_code)
		{
			$errors_collapse = array();
			$errors_coldes = array();

			foreach ($errors as $error)
				if (!in_array($error['code'], $errors_coldes))
				{
					$errors_collapse[] = $error;
					$errors_coldes[] = $error['code'];
				}

			$errors = $errors_collapse;
		}

		$return_errors = array();
		if ($only_errors)
		{
			foreach ($errors as $error)
			{
				if (is_array($error) && isset($error['error']))
					$return_errors[] = $error['error'];
				else
					$return_errors[] = $error;
			}

		}
		else
			$return_errors = $errors;

		return $return_errors;
	}

	public function addResponseErrors($errors)
	{
		foreach ($errors as $error)
			$this->addError($error['error'], $error['code']);
	}

	public function addParams(&$params, $data)
	{
		foreach ($data as $key => $item)
			$this->addParam($params, $key, $item);
	}

	public function addParam(&$params, $key, $name)
	{
		$params[$key] = $name;
	}

	public function mergeErrors(&$errors)
	{
		$errors = array_merge($errors, $this->getErrors(true));
	}

	public static function grep($array, $name)
	{
		$return_array = array();
		foreach ($array as $value)
			if (isset($value[$name]))
				$return_array[] = $value[$name];
		return $return_array;
	}

	public static function makeDate($date, $format = 'm/d/Y')
	{
		return date($format, strtotime($date));
	}

	public static function formatPhone($phone)
	{
		$phone = explode(' ', trim(preg_replace('/[()\s.-]+/', ' ', $phone)));
		$result = '';
		$len = count($phone);
		$i = 0;
		foreach ($phone as $value)
		{
			$result .= $value;

			if (($i >= $len - 3) && ($i < $len - 1))
				$result .= '-';
			else
				$result .= ' ';
			$i++;
		}
		return $result;
	}

	public static function searchFind($respose)
	{
		if ($respose === false)
			return false;
		return true;
	}

	public static function arrayMerge(&$array1, $array2)
	{
		foreach ($array2 as $key => $value)
			$array1[$key] = $value;
	}
}
?>