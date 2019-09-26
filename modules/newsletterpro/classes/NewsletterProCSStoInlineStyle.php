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

class NewsletterProCSStoInlineStyle extends NewsletterProEmogrifier
{
	private $np_head_style;

	public function __construct($html = '', $css = '')
	{
		$current_encode = mb_detect_encoding($html, 'UTF-8, ISO-8859-1, ISO-8859-15, HTML-ENTITIES', true);
		// this will solve the trim last row bug in phpQuery plugin
		$html = html_entity_decode($html, ENT_COMPAT | ENT_HTML401 | ENT_HTML5, $current_encode);
		$html = mb_convert_encoding($html, 'UTF-8', $current_encode);
		
		if ((bool)pqnp_ini_config('add_style_to_header'))
		{
			// grep the heaser style
			if (preg_match_all('/(?P<style><(style).*>?[\s\S]*?<\/\2>)/', $html, $match))
			{
				if (!empty($match['style']))
					$this->np_head_style = implode("\n", $match['style']);
			}
		}

		parent::__construct($html, $css);
	}

	public function output()
	{
		$html = $this->emogrify();
		$html_return = html_entity_decode(str_replace(array('%7B', '%7D'), array('{', '}'), $html));
			
		if (isset($this->np_head_style))
		{
			if (preg_match('/<head.*?>/', $html_return, $match))
			{
				$head_text = $match[0];
				$head_start = strpos($html_return, $head_text);
				if ($head_start !== false)
				{
					$head_end = $head_start + Tools::strlen($head_text);
					$html_return = substr_replace($html_return, "\n\n".$this->np_head_style, $head_end, 0);
				}
			}
		}

		return $html_return;
	}

	public static function convert($html = '', $css = '')
	{
		try
		{
			$obj = new NewsletterProCSStoInlineStyle($html, $css);
			return $obj->output();
		}
		catch(Exception $e)
		{
			NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
		}
		return $html;
	}
}