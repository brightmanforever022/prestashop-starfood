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

class NewsletterProCreateXML extends DOMDocument
{
	public $root_name;
	public $root;

	const USE_CDATA = false;

	public function __construct($root_name)
	{
		parent::__construct('1.0', 'UTF-8');

		$this->root_name = $root_name;
		$this->root = $this->createElement($this->root_name);
		$this->root = $this->appendChild($this->root);
	}

	public function getContent($node = null)
	{
		$this->preserveWhiteSpace = false;
		$this->formatOutput = true;
		$xml = $this->saveXML($node);
		return $xml;
	}

	public function getRoot()
	{
		return $this->root;
	}

	public function display($node = null)
	{
		$this->preserveWhiteSpace = false;
		$this->formatOutput = true;
		echo '<pre>';
		echo htmlentities($this->saveXML($node));
		echo '</pre>';
	}

	public function append($to, $name, $value = '')
	{
		$value_format = htmlspecialchars($value);
		// $value_format = pSQL($value, true);
		// $value_format = htmlentities($value, ENT_COMPAT | ENT_XML1, 'UTF-8');
		// $value_format = htmlspecialchars(stripslashes(trim($value)));

		if (self::USE_CDATA)
		{
			$element = $this->createElement($name);
			$element->appendChild($this->createCDATASection($value_format));
		}
		else
			$element = $this->createElement($name, $value_format);

		return $to->appendChild($element);
	}

	public function attribute($to, $name, $value = '')
	{
		$attribute = $this->createAttribute($name);
		$attribute->value = $value;
		return $to->appendChild( $attribute );
	}

	public function create($to, $name, $a_name = '', $a_value = '', $value = '')
	{
		$child = $this->append($to, $name, $value);
		if ($a_name != '')
			$this->attribute($child, $a_name, $a_value);
		return $child;
	}
}
?>