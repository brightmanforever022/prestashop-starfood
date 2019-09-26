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

class NewsletterProTemplateDecoratorReplacements implements NewsletterPro_Swift_Plugins_TemplateDecorator_Replacement
{
	public $template;

	public function __construct(NewsletterProTemplate $template)
	{
		$this->template = $template;
	}

	public static function newInstance(NewsletterProTemplate $template)
	{
		return new self($template);
	}

	public function getTemplate()
	{
		return $this->template;
	}

	public function getTemplateFor($email = null)
	{
		$message = $this->template->message($email);
		return $message;
	}	
}