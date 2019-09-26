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

class NewsletterProCurl
{
	public $url;

	public $response;

	public $headers;

	public $config;

	public $request_params;

	public $format;

	public $errors;

	public $module;

	public function __construct($config = array())
	{
		$this->module = NewsletterPro::getInstance();
		$this->errors = array();

		if (!extension_loaded('curl'))
			throw new Exception(sprintf($this->module->l('The availability of php %s library is not available on your server. You can talk with the hosting provider to enable it.'), 'curl'));

		if (!isset($config['url']))
			$config['url'] = '';

		$this->config = $config;
	}

	public function curlit()
	{
		if (!function_exists('curl_init'))
			throw new Exception(sprintf($this->module->l('The availability of php %s library is not available on your server. You can talk with the hosting provider to enable it.'), 'curl'));
		else
		{
			$c = curl_init();

			curl_setopt_array($c, array(
				CURLOPT_USERAGENT      => 'spider',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER         => false,

				CURLOPT_HEADERFUNCTION => array($this, 'curlHeader'),

				CURLOPT_FOLLOWLOCATION => false,
				CURLOPT_ENCODING       => '',
				CURLOPT_URL            => $this->url,
				CURLOPT_AUTOREFERER    => true,
				CURLOPT_CONNECTTIMEOUT => 60,
				CURLOPT_TIMEOUT        => 45,
				CURLOPT_MAXREDIRS      => 10,
				CURLOPT_SSL_VERIFYPEER => false,
			));

			switch ($this->method)
			{
				case 'GET':
					$content_length = 0;
					break;

				case 'POST':
					curl_setopt($c, CURLOPT_POST, true);
					$post_body = $this->safeEncode($this->xml);
					curl_setopt ($c, CURLOPT_POSTFIELDS, $post_body);
					$this->request_params['xml'] = $post_body;
					$content_length = Tools::strlen($post_body);
					break;

				case 'PUT':
					$fh = tmpfile();
					if ($this->format == 'file')
						$put_body = $this->xml;
					else
						$put_body = $this->safeEncode($this->xml);

					fwrite($fh, $put_body);
					rewind($fh);
					curl_setopt($c, CURLOPT_PUT, true);
					curl_setopt($c, CURLOPT_INFILE, $fh);
					curl_setopt($c, CURLOPT_INFILESIZE, Tools::strlen($put_body));
					$content_length = Tools::strlen($put_body);
					break;

				default:
					$content_length = 0;
					curl_setopt($c, CURLOPT_CUSTOMREQUEST, $this->method);
					break;
			}

			if (!empty($this->request_params))
			{
				if (!$this->config['multipart'])
				{
					$ps = array();
					foreach ($this->request_params as $k => $v)
						$ps[] = "{$k}={$v}";
					$this->request_params = implode('&', $ps);
				}
				curl_setopt($c, CURLOPT_POSTFIELDS, $this->request_params);
			}
			else
			{
				$this->headers['Content-Type'] = '';
				$this->headers['Content-Length'] = $content_length;
			}

			$this->headers['Expect'] = '';

			if (!empty($this->headers))
			{
				$headers = array();
				foreach ($this->headers as $k => $v)
					$headers[] = trim($k.': '.$v);

				curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
			}

			if (isset($this->config['prevent_request']) && false == $this->config['prevent_request'])
				return;

			$response = curl_exec($c);
			if ($response === false)
			{
				$response = 'Curl error: '.curl_error($c);
				$code = 1;
			}
			else
				$code = curl_getinfo($c, CURLINFO_HTTP_CODE);

			$info = curl_getinfo ($c);

			curl_close ($c);
			if (isset($fh))
				fclose($fh);

			$this->response['code']     = $code;
			$this->response['response'] = $response;
			$this->response['info']     = $info;
			$this->response['format']   = $this->format;
			return $code;
		}
	}

	public function url($request = '', $format = 'json')
	{
		if (isset($format))
		{
			switch ($format)
			{
				case 'html':
					$this->headers['Accept'] = 'text/plain';
					break;
				case 'pdf':
					$this->headers['Accept'] = 'application/pdf';
					break;
				case 'json':
					$this->headers['Accept'] = 'application/json';
					break;
				case 'xml' :
				default :
					$this->headers['Accept'] = 'application/xml';
					break;
			}
		}

		$this->format = $format;

		return implode(array(
			$this->config['url'],
			$request,
		));
	}

	public function request($method, $url, $params = array(), $xml = '')
	{
		$multipart = false;

		if ($xml !== '')
			$this->xml = $xml;

		if ($method == 'xml')
			$params['xml'] = $xml;

		$this->prepareMethod($method);
		$this->config['multipart'] = $multipart;

		$this->url = $url.(strpos($url, '?') !== false ? '&' : '?').$this->buildQuery($params);

		$this->curlit();

		return $this->response;
	}

	public function curlHeader($ch, $header)
	{
		$i = strpos($header, ':');

		if (!empty($i))
		{
			$key = str_replace('-', '_', Tools::strtolower(Tools::substr($header, 0, $i)));
			$value = trim(Tools::substr($header, $i + 2));
			$this->response['headers'][$key] = $value;
		}
		return Tools::strlen($header);
	}

	private function prepareMethod($method)
	{
		$this->method = Tools::strtoupper($method);
	}

	public function buildQuery($params)
	{
		return http_build_query($params) != '' ? http_build_query($params) : '';
	}

	public function parseResponse($response, $format)
	{
		if (isset($format))
		{
			switch ($format)
			{
				case 'html':
					$the_response = $response;
					break;
				case 'pdf' :
					$the_response = $response;
					break;
				case 'json' :
					$the_response = Tools::jsonDecode($response);
					break;
				default :
					$the_response = simplexml_load_string($response);
					break;
			}
		}
		return $the_response;
	}

	public function success()
	{
		return empty($this->errors);
	}

	public function getErrors()
	{
		return $this->errors;
	}
}