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

class NewsletterProTemplateString extends NewsletterProTemplate
{
	private $data_content;

	public function __construct($data, $user = null)
	{
		if (!is_array($data))
			throw new NewsletterProTemplateException('The data must by array type.');
			
		if ($data[0] != null && Tools::strlen($data[0]));
			$this->name = $data[0];

		if (!isset($data[1]))
			throw new NewsletterProTemplateException('The data content is not set.');

		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$content = array();
		if (is_string($data[1]))
			$content[$default_lang] = $data[1];
		else
			$content = $data[1];

		if (!isset($content[$default_lang]))
			throw new NewsletterProTemplateException(NewsletterPro::getInstance()->l('The default language is not set.'));

		$this->data_content = $content;

		parent::__construct($data, $user);
	}

	public static function newInstance($data, $user = null)
	{
		return new self($data, $user);
	}

	public function load($id_lang = null, $load_user_lang = false)
	{
		if ($load_user_lang && isset($this->user))
			$id_lang = $this->user->id_lang;

		$this->load_id_lang = $id_lang;

		$this->content = NewsletterProTemplateContent::newInstance();

		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		if (!isset($id_lang))
		{
			if (!isset($this->data_content[$default_lang]))
				throw new NewsletterProTemplateException(NewsletterPro::getInstance()->l('The template don\'t have the default language.'));

			foreach ($this->dir_template_lang_file as $id_lang => $path) 
			{
				if (isset($this->data_content[$id_lang]))				
					$this->content->setContentByIdLang($id_lang, $this->data_content[$id_lang]);
				else
					$this->content->setContentByIdLang($id_lang, $this->data_content[$default_lang]);
			}
		}
		else
		{
			if (!isset($this->data_content[$id_lang]))
				throw new NewsletterProTemplateException(NewsletterPro::getInstance()->l('The template language does not exists.'));

			$this->content->setContentByIdLang($id_lang, $this->data_content[$id_lang]);
		}

		parent::load();

		return $this;
	}
}