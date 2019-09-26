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

class NewsletterProTemplateFile extends NewsletterProTemplate
{
	public function __construct($data, $user = null)
	{
		$this->name = $data;

		parent::__construct($data, $user);
	}

	public static function newInstance($data, $user = null)
	{
		return new self($data, $user);
	}

	public function load($id_lang = null, $load_user_lang = false)
	{
		$module = NewsletterPro::getInstance();
		
		if ($load_user_lang && isset($this->user))
			$id_lang = $this->user->id_lang;

		$this->load_id_lang = $id_lang;

		$filename = pathinfo($this->name, PATHINFO_FILENAME);

		if (!file_exists($this->dir_template))
			throw new NewsletterProTemplateException(sprintf(NewsletterPro::getInstance()->l('The template "%s" does not exists.'), $filename));

		if (!is_dir($this->dir_template))
			throw new NewsletterProTemplateException(sprintf(NewsletterPro::getInstance()->l('The template "%s" is not a directory.'), $filename));

		$default_template_lang_file = $this->dir_template_lang_file[(int)Configuration::get('PS_LANG_DEFAULT')];

		if (!file_exists($default_template_lang_file))
		{
			$dir_template_lang_file_en = $this->dir_template.'en/'.$this->name;

			// copy the file from english if exists
			if (!file_exists($dir_template_lang_file_en))
				throw new NewsletterProTemplateException(sprintf(NewsletterPro::getInstance()->l('The default language template "%s" does not exists.'), $filename));
			else
			{
				$folder_name = pathinfo($default_template_lang_file, PATHINFO_DIRNAME);

				if (!file_exists($folder_name))
					mkdir($folder_name, 0777);

				if (!file_exists($$default_template_lang_file))
					copy($dir_template_lang_file_en, $default_template_lang_file);

				if (!file_exists($default_template_lang_file))
					throw new NewsletterProTemplateException(sprintf(NewsletterPro::getInstance()->l('The default language template "%s" does not exists.'), $filename));
			}
		}

		$this->content = NewsletterProTemplateContent::newInstance();						

		if (!isset($id_lang))
		{
			// load all languages
			foreach ($this->dir_template_lang_file as $id_lang => $path) 
			{
				if (file_exists($path))
				{
					$content = Tools::file_get_contents($path);

					if ($content === false)
						throw new NewsletterProTemplateException(sprintf($module->l('The file "%s" cannot be read, check the CHMOD permissions.'), $path));

					$this->content->setContentByIdLang($id_lang, $content);
				}
				else
				{
					$default_lang_content = Tools::file_get_contents($default_template_lang_file);

					if ($default_lang_content === false)
						throw new NewsletterProTemplateException(sprintf($module->l('The file "%s" cannot be read, check the CHMOD permissions.'), $path));

					$this->content->setContentByIdLang($id_lang, $default_lang_content);
				}
			}
		}
		else
		{
			// load one lang
			if (!isset($this->dir_template_lang_file[$id_lang]))
				throw new NewsletterProTemplateException(NewsletterPro::getInstance()->l('The template language does not exists.'));
			
			$path = $this->dir_template_lang_file[$id_lang];

			// if the language does not exists get the content from the default language
			if (!file_exists($path))
				$path = $this->dir_template.'en/'.$this->name;

			$content = Tools::file_get_contents($path);

			if ($content === false)
				throw new NewsletterProTemplateException(sprintf($module->l('The file "%s" cannot be read, check the CHMOD permissions.'), $path));

			$this->content->setContentByIdLang($id_lang, $content);
		}

		parent::load();

		return $this;
	}
}