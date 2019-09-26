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

class NewsletterProTemplateHistory extends NewsletterProTemplate
{
	public $id_history;

	public function __construct($data, $user = null)
	{
		$this->id_history = (int)$data;

		if (!$this->id_history)
			throw new NewsletterProTemplateException(sprintf(NewsletterPro::getInstance()->l('The template id history "%s" is not valid.'), $this->id_history));

		$name = NewsletterProTplHistory::getTemplateName($this->id_history);

		if (Tools::strlen($name) > 0)
			$this->name = $name;

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

		$history = NewsletterProTplHistory::newInstance($this->id_history, $id_lang);

		if (!Validate::isLoadedObject($history))
			throw new NewsletterProTemplateException(NewsletterPro::getInstance()->l('Cannot get the templat from the database.'));

		$this->content = NewsletterProTemplateContent::newInstance();						

		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		if (!isset($id_lang))
		{
			if (!isset($history->template[$default_lang]))
				throw new NewsletterProTemplateException(NewsletterPro::getInstance()->l('The template don\'t have the default language.'));

			// load all languages
			foreach ($this->dir_template_lang_file as $id_lang => $path) 
			{
				if (isset($history->template[$id_lang]))				
					$this->content->setContentByIdLang($id_lang, $history->template[$id_lang]);
				else
					$this->content->setContentByIdLang($id_lang, $history->template[$default_lang]);
			}
		}
		else
		{
			if (!in_array($id_lang, $this->languages_id))
				throw new NewsletterProTemplateException(NewsletterPro::getInstance()->l('The template language does not exists.'));

			$this->content->setContentByIdLang($id_lang, $history->template);
		}

		parent::load();

		return $this;
	}
}