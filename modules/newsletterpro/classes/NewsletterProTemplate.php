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

class NewsletterProTemplate
{
	public $name;

	public $data;

	public $email;

	public $user;

	public $content;

	public $variables = array();

	public $load_id_lang;

	public $dir;

	public $dir_template;

	public $dir_template_lang;

	public $dir_template_lang_file;

	public $languages;

	public $languages_id;

	public $global_css_path;

	public $css_path;

	public $css = '';

	public $is_forwarder = false;

	public $forwarder_data = array();

	private $reserved_name = array(
		'global.html', 'default.html'
	);

	/**
	 *
	 * NewsletterProTemplate::newFile('template.html', $user);
	 * NewsletterProTemplate::newHistory($id_history, $user);
	 * NewsletterProTemplateString::newInstance(array('template.html', '<div>en</div>') $user)
	 * NewsletterProTemplateString::newInstance(array('template.html', array(1 => '<div>en</div>', 2 => '<div>fr></div>')) $user)
	 * 
	 * @param array/string $data
	 *
	 * string example: 'newsletter@email.com'
	 * array example: array('newsletter@emai.com')
	 * array example: array('newsletter@emai.com',  NewsletterProTemplateUser::USER_TYPE_EMPLOYEE)
	 * @param array/string $user
	 */
	public function __construct($data, $user = null)
	{
		if (!in_array(get_class($this), array('NewsletterProTemplateString', 'NewsletterProTemplateFile', 'NewsletterProTemplateHistory')))
			throw new NewsletterProTemplateException('The template must be a valid instance.');

		$this->data = $data;

		if (isset($user))
			$this->setUser($user);

		$this->dir = dirname(dirname(__FILE__)).'/mail_templates/newsletter/';

		$this->languages = Language::getLanguages(false);

		$this->global_css_path = NewsletterPro::getInstance()->dir_location.'views/css/mails/global.css';
	
		$this->languages_id = array();
		foreach ($this->languages as $lang)
			$this->languages_id[] = (int)$lang['id_lang'];


		if (isset($this->name))
			$this->setPaths();
	}

	public static function newHistory($data, $user = null)
	{
		return NewsletterProTemplateHistory::newInstance($data, $user);
	}

	public static function newString($data, $user = null)
	{
		return NewsletterProTemplateString::newInstance($data, $user);
	}

	public static function newFile($data, $user = null)
	{
		return NewsletterProTemplateFile::newInstance($data, $user);
	}

	public function setName($name)
	{
		$this->name = $name;

		if (!preg_match('/.html$/', $this->name))
			throw new NewsletterProTemplateException(sprintf(NewsletterPro::getInstance()->l('The filename "%s" must have "%s" extension.'), $this->name, '.html'));
			
		$this->setPaths();
	}

	public function setUser($user)
	{
		$user_type = null;
		if (is_array($user))
		{
			$this->email = $user[0];

			if (isset($user[1]))
				$user_type = $user[1];
		}
		else
			$this->email = $user;

		$this->user = NewsletterProTemplateUser::newInstance($this->email)->setTemplate($this)->create($user_type);

		if (!isset($this->email) && isset($this->user->email))
			$this->email = $this->user->email;
	}

	private function setPaths()
	{
		if (!isset($this->name))
			throw new NewsletterProTemplateException('The template name is not set.');

		$filename = pathinfo($this->name, PATHINFO_FILENAME);

		$this->dir_template = $this->dir.$filename.'/';

		$this->dir_template_lang = array();
		$this->dir_template_lang_file = array();

		foreach ($this->languages as $lang)
		{
			$this->dir_template_lang[$lang['id_lang']] = $this->dir_template.$lang['iso_code'].'/';
			$this->dir_template_lang_file[$lang['id_lang']] = $this->dir_template.$lang['iso_code'].'/'.$this->name;
		}

		$this->css_path = NewsletterPro::getInstance()->dir_location.'/views/css/mails/'.pathinfo($this->name, PATHINFO_FILENAME).'.css';

		$this->css = $this->getCSSFileContent();
	}

	/**
	 * This function is executeing after the content was loaded
	 *
	 * example of the child load
	 * $obj->load() - load all languages
	 * $obj->load(1) - load a specific language
	 * $obj->load(null, true) - load the user language
	 */
	public function load()
	{
		$this->content->setTemplate($this);
	}

	public function html($content_type = NewsletterProTemplateContent::CONTENT_FULL, $add_css = false)
	{
		$content = $this->content->html($content_type, $add_css);

		if (isset($this->load_id_lang))
		{
			if (!isset($content[$this->load_id_lang]))
				throw new NewsletterProTemplateException(sprintf(NewsletterPro::getInstance()->l('The template don\'t have the language id "%s".'), $this->load_id_lang));

			return $content[$this->load_id_lang];
		}

		return $content;
	}

	public function htmlInvert($content_type = NewsletterProTemplateContent::CONTENT_FULL, $add_css = false)
	{
		return $this->invert($this->html($content_type, $add_css), $content_type);
	}

	public function renderInvert($content_type = NewsletterProTemplateContent::CONTENT_FULL)
	{
		return $this->invert($this->render($content_type), $content_type);
	}

	public function invert($array, $content_type)
	{
		$result = array();

		switch ($content_type) 
		{
			case NewsletterProTemplateContent::CONTENT_FULL:
			case NewsletterProTemplateContent::CONTENT_MESSAGE:

				if (isset($this->load_id_lang))
					return $array;
				else
				{
					foreach ($array as $id_lang => $value)
						foreach ($value as $name => $content)
							$result[$name][$id_lang] = $content;
				}
				break;

			default:
				$result = $array;
				break;
		}

		return $result;
	}

	public function render($content_type = NewsletterProTemplateContent::CONTENT_FULL, $user = null, $load_user_lang = false, $id_lang = null, $force_template_language = false)
	{
		if (isset($user))
			$this->setUser($user);

		if (!isset($this->user))
			throw new NewsletterProTemplateException('The template user is not set.');
		
		if ($load_user_lang)
			$this->load_id_lang = (int)$this->user->id_lang;
		else if (isset($id_lang))
			$this->load_id_lang = $id_lang;

		if (isset($this->load_id_lang) && (!$this->load_id_lang || !NewsletterProTools::languageExists($this->load_id_lang)))
			$this->load_id_lang = (int)pqnp_config('PS_LANG_DEFAULT');
		
		// make the user language to be the same as the template language
		if ($force_template_language && $this->user->id_lang != $this->load_id_lang) {
			$this->user->refresh($this->load_id_lang);
		}

		$this->content->setChimpVariables($this->user->variablesChimp());
		$this->content->setVariables($this->user->variables());
		$this->content->setVariables($this->variables);

		$render = $this->content->render($content_type);

		if (isset($this->load_id_lang))
		{

			if (!isset($render[$this->load_id_lang]))
				throw new NewsletterProTemplateException(sprintf(NewsletterPro::getInstance()->l('The template don\'t have the language id "%s".'), $this->load_id_lang));

			return $render[$this->load_id_lang];
		}

		return $render;
	}

	public function renderMailChimp($content_type = NewsletterProTemplateContent::CONTENT_FULL, $user = null, $load_user_lang = false, $id_lang = null)
	{
		if (isset($user))
			$this->setUser($user);

		if (!isset($this->user))
			throw new NewsletterProTemplateException('The template user is not set.');

		$context = Context::getContext();
		$module = NewsletterPro::getInstance();

		// create a valid token for mailchimp
		$mc_token = NewsletterProMailChimpToken::generateDayToken();
		$mc_token_obj = NewsletterProMailChimpToken::getInstanceByToken($mc_token);

		if (!$mc_token_obj)
		{
			$mc_token_obj = NewsletterProMailChimpToken::newInstance();
			$mc_token_obj->token = $mc_token;
			$mc_token_obj->add();
		}


		$unsubscribe_link = urldecode($context->link->getModuleLink($module->name, 'unsubscribe', array(
			'email' => '{email}',
			'mc_token' => $mc_token_obj->token,
		), null, $this->user->id_lang, $this->user->id_shop));

		$unsubscribe_link_redirect = urldecode($context->link->getModuleLink($module->name, 'unsubscribe', array(
			'email' => '{email}',
			'mc_token' => $mc_token_obj->token,
			'msg' => false,
		), null, $this->user->id_lang, $this->user->id_shop));

		$subscribe_link = urldecode($context->link->getModuleLink($module->name, 'subscribe', array(
			'email' => '{email}',
			'mc_token' => $mc_token_obj->token,
		), null, $this->user->id_lang, $this->user->id_shop));

		$forward_link = urldecode($context->link->getModuleLink($module->name, 'forward', array(
			'email' => '{email}',
			'mc_token' => $mc_token_obj->token,
		), null, $this->user->id_lang, $this->user->id_shop));

		// $unsubscribe_link = $this->user->shop_url.'index.php?fc=module&module=newsletterpro&controller=unsubscribe&email={email}&mc_token='.$mc_token_obj->token;
		// $unsubscribe_link_redirect = $this->user->shop_url.'index.php?fc=module&module=newsletterpro&controller=unsubscribe&email={email}&mc_token='.$mc_token_obj->token.'&msg=false';
		// $subscribe_link = $this->user->shop_url.'index.php?fc=module&module=newsletterpro&controller=subscribe&email={email}&mc_token='.$mc_token_obj->token;
		// $forward_link = $this->user->shop_url.'index.php?fc=module&module=newsletterpro&controller=forward&email={email}&mc_token='.$mc_token_obj->token;

		// change links
		$this->user->setVariables(array(
			'email' => '*|EMAIL|*',
			'firstname' => '*|FNAME|*',
			'lastname' => '*|LNAME|*',
			'active' => '*|SUBSCRIBED|*',
			'shop_name' => '*|SHOP|*',
			'language' => '*|LANGUAGE|*',
			'user_type' => '*|USER_TYPE|*',
			'unsubscribe_link' => $unsubscribe_link,
			'unsubscribe_link_redirect' => $unsubscribe_link_redirect,
			'subscribe_link' => $unsubscribe_link,
			'forward_link' => $forward_link,
			'unsubscribe' => '<a href="'.$unsubscribe_link.'" target="_blank">'.$module->l('unsubscribe').'</a>',
			'unsubscribe_redirect' => '<a href="'.$unsubscribe_link_redirect.'" target="_blank">'.$module->l('unsubscribe').'</a>',
			'subscribe' => '<a href="'.$subscribe_link.'" target="_blank">'.$module->l('subscribe').'</a>',
			'forward' => '<a href="'.$forward_link.'" target="_blank">'.$module->l('forward').'</a>',
		));

		$this->user->setChimpVariables(array(
			'EMAIL' => '*|EMAIL|*',
			'FNAME' => '*|FNAME|*',
			'LNAME' => '*|LNAME|*',
			'SHOP' => '*|SHOP|*',
			'SUBSCRIBED' => '*|SUBSCRIBED|*',
			'LANGUAGE' => '*|LANGUAGE|*',
		));

		if ($this instanceof NewsletterProTemplateHistory)
		{
			$view_in_browser_link = $module->url_location.'newsletter.php?&email={email}&token_tpl='.$module->getTokenByIdHistory((int)$this->id_history);
			$view_in_browser_link_share = urlencode($module->url_location.'newsletter.php?&email={email}&token_tpl='.$module->getTokenByIdHistory((int)$this->id_history));

			$this->user->setVariables(array(
				'unsubscribe_link' => $unsubscribe_link.'&token='.$module->getTokenByIdHistory((int)$this->id_history),
				'unsubscribe_link_redirect' => $unsubscribe_link_redirect.'&token='.$module->getTokenByIdHistory((int)$this->id_history),
				'view_in_browser_link' => $view_in_browser_link,
				'view_in_browser' => '<a href="'.$view_in_browser_link.'" target="_blank">'.$module->l('Click here').'</a>',
				'view_in_browser_link_share' => $view_in_browser_link_share,
				'view_in_browser_share' => '<a href="'.$view_in_browser_link_share.'" target="_blank">'.$module->l('Click here').'</a>',
			));
		}

		return $this->render($content_type, $user, $load_user_lang, $id_lang);
	}

	public function css()
	{
		return $this->html(NewsletterProTemplateContent::CONTENT_CSS);
	}

	public function message($user = null, $load_user_lang = true, $id_lang = null, $force_template_language = false)
	{
		return $this->render(NewsletterProTemplateContent::CONTENT_MESSAGE, $user, $load_user_lang, $id_lang, $force_template_language);
	}

	public function setVariables($variables)
	{
		$this->variables = array_merge($this->variables, $variables);
		return $this;
	}

	public function variables()
	{
		return $this->variables;
	}

	private function getIsoId($iso_code = 'en')
	{
		$id = 0;
		foreach ($this->languages as $lang)
		{
			if ($lang['iso_code'] == $iso_code)
				return (int)$lang['id_lang'];
		}
		return $id;
	}

	private function saveEn()
	{
		$module = NewsletterPro::getInstance();
		$en_id = $this->getIsoId('en');

		// the the shop don't have the english language create and save it
		if (!array_key_exists($en_id, $this->dir_template_lang_file))
		{
			if (isset($this->content->html[pqnp_config('PS_LANG_DEFAULT')]))
			{
				$en_html = $this->content->html[pqnp_config('PS_LANG_DEFAULT')];

				if (!file_exists($this->dir_template))
				{
					if (!mkdir($this->dir_template, 0777))
						throw new NewsletterProTemplateException(sprintf($module->l('The file cannot be created. Please check the CHMOD permissions.'), $this->dir_template));
				}

				$en_dir_lang = $this->dir_template.'en/';
				$en_file = $en_dir_lang.$this->name;


				if (!file_exists($en_dir_lang))
				{
					if (!mkdir($en_dir_lang, 0777))
						throw new NewsletterProTemplateException(sprintf($module->l('The file cannot be created. Please check the CHMOD permissions.'), $en_dir_lang));
				}

				$np_index = $module->dir_location.'index.php';
				$en_dir_lang_index = $en_dir_lang.'index.php';

				if (!file_exists($en_dir_lang_index))
					@copy($np_index, $en_dir_lang_index);

				$handle = fopen($en_file, 'w');

				if ($handle === false)
					throw new NewsletterProTemplateException(sprintf($module->l('The file cannot be opened. Please check the CHMOD permissions.'), $en_file));

				if (fwrite($handle, $en_html) === false)
					throw new NewsletterProTemplateException(sprintf($module->l('The file cannot be saved. Please check the CHMOD permissions.'), $en_file));

				fclose($handle);
			}			
		}
	}

	public function save()
	{
		$module = NewsletterPro::getInstance();

		if (in_array($this->name, $this->reserved_name))
			throw new NewsletterProTemplateException(sprintf($module->l('The name "%s" is reserved, please use another name.'), $this->name));

		// save english if not exists
		$this->saveEn();

		// save template
		foreach ($this->dir_template_lang_file as $id_lang => $file)
		{
			if (isset($this->content->html[$id_lang]))
			{
				$html  = $this->content->html[$id_lang];

				$template_dir_lang = dirname($file);
				$template_dir = dirname($template_dir_lang);
				$np_index = $module->dir_location.'index.php';

				if (!file_exists($template_dir))
				{
					if (!mkdir($template_dir, 0777))
						throw new NewsletterProTemplateException(sprintf($module->l('The file cannot be created. Please check the CHMOD permissions.'), $template_dir));
				}

				$dir_index = $template_dir.'/index.php';
				if (!file_exists($dir_index))
				{
					if (file_exists($np_index))
						@copy($np_index, $dir_index);
				}

				if (!file_exists($template_dir_lang))
				{
					if (!mkdir($template_dir_lang, 0777))
						throw new NewsletterProTemplateException(sprintf($module->l('The file cannot be created. Please check the CHMOD permissions.'), $template_dir_lang));
				}

				$handle = fopen($file, 'w');

				if ($handle === false)
					throw new NewsletterProTemplateException(sprintf($module->l('The file cannot be opened. Please check the CHMOD permissions.'), $file));

				if (fwrite($handle, $html) === false)
					throw new NewsletterProTemplateException(sprintf($module->l('The file cannot be saved. Please check the CHMOD permissions.'), $file));

				fclose($handle);

				$index = $template_dir_lang.'/index.php';

				if (!file_exists($index))
				{
					if (file_exists($np_index))
						@copy($np_index, $index);
				}
			}
		}

		if (file_put_contents($this->css_path, $this->css) === false)
			throw new NewsletterProTemplateException(sprintf($module->l('The file cannot be saved. Please check the CHMOD permissions.'), $this->css_path));

		return true;
	}

	public function saveAs($new_name)
	{
		$this->setName($new_name);
		return $this->save();
	}

	public function cssLink($id_lang = null)
	{
		return $this->content->cssLink($id_lang);
	}

	public function hasLanguage($id_lang)
	{
		return isset($this->dir_template_lang_file[$id_lang]);
	}

	public function changeTitle(array $new_title)
	{
		foreach ($this->content->html as $id_lang => $html)
		{
			if (isset($new_title[$id_lang]))
			{
				$title = htmlentities($new_title[$id_lang]);

				$tile_pos = strpos($html, '<title>');
				if ($tile_pos === false)
				{
					$head_str = '<head>';
					$head_pos = strpos($html, $head_str);

					if ($head_pos !== false)
						$this->content->html[$id_lang] = substr_replace($html, "\n".'<title>'.$title.'</title>', $head_pos + Tools::strlen($head_str), 0);
				}
				else
					$this->content->html[$id_lang] = preg_replace('/<\s*?title\s*?.*?>.*?<\s*?\/\s*?title\s*?>/', '<title>'.$title.'</title>', $html);
			}
		}
	}

	public function setContent($content)
	{
		foreach ($content as $id_lang => $html) 
		{
			$this->changeTitle(array($id_lang => $html['title']));

			$html_full = array();

			$html_full[] = $html['header'];
			$html_full[] = $html['body'];
			$html_full[] = $html['footer'];

			$html_string = join('', $html_full);

			$this->content->html[$id_lang] = $html_string;
		}
	}

	public static function buildContent($content)
	{
		$html_content = array();

		foreach ($content as $id_lang => $html) 
		{
			$html_full = array();

			$html_full[] = $html['header'];
			$html_full[] = $html['body'];
			$html_full[] = $html['footer'];

			$html_string = join('', $html_full);

			$html_content[$id_lang] = $html_string;
		}

		return $html_content;
	}

	public function getCSSFileContent()
	{
		if (file_exists($this->css_path))
		{
			$css = Tools::file_get_contents($this->css_path);

			if ($css !== false)
				return $css;
		}

		return '';
	}

	public function getCSSGlobalFileContent()
	{
		if (file_exists($this->global_css_path))
		{
			$css = Tools::file_get_contents($this->global_css_path);

			if ($css !== false)
				return $css;
		}

		return '';
	}

	public function saveGlobalCSS($content)
	{
		if (file_put_contents($this->global_css_path, $content) === false)
			throw new NewsletterProTemplateException(sprintf('The file cannot be saved. Please check the CHMOD permissions.', $this->global_css_path));

		return true;
	}

	public static function templateExists($template_name)
	{
		$name = trim(pathinfo($template_name, PATHINFO_FILENAME));
		
		if (Tools::strlen($name) == 0)
			return false;

		$path = NewsletterPro::getInstance()->dir_location.'/mail_templates/newsletter/'.$name;

		if (file_exists($path))
			return true;

		return false;
	}

	public function export($use_render = false)
	{
		if ($use_render)
			$content = $this->render(NewsletterProTemplateContent::CONTENT_HTML);
		else
			$content = $this->html(NewsletterProTemplateContent::CONTENT_HTML, true);

		$id_lang_default = pqnp_config('PS_LANG_DEFAULT');
		$languages = Language::getLanguages(false);

		$zip = new ZipArchive;
		$name = pathinfo($this->name, PATHINFO_FILENAME).'.zip';

		$export_dir = NewsletterPro::getInstance()->dir_location.'/mail_templates/export/';

		if (!file_exists($export_dir) || !is_dir($export_dir))
			throw new NewsletterProTemplateException(NewsletterPro::getInstance()->l('The export directory does not exists.'));
			
		$filename = $export_dir.$name;

		if (file_exists($filename) && is_file($filename))
			@unlink($filename);

		if ($zip->open($filename, ZipArchive::CREATE) === true)
		{
			foreach ($languages as $lang) 
			{
				$dir = $lang['iso_code'];
			
				if (!$zip->addEmptyDir($dir))
					throw new NewsletterProTemplateException(sprintf(NewsletterPro::getInstance()->l('The directory "%s" cannot be archived.'), $dir));

				$html = '';

				if (!isset($content[$lang['id_lang']]))
					$html = $content[$id_lang_default];
				else
					$html = $content[$lang['id_lang']];

				if (!$zip->addFromString($dir.'/'.$this->name, $html))
					throw new NewsletterProTemplateException(sprintf(NewsletterPro::getInstance()->l('The file "%s" cannot be archived.'), $dir.'/'.$this->name));
			}

			$zip->close();

			@ob_clean();
			@ob_end_clean();
			header('Content-type: application/zip');
			header('Content-Disposition: attachment; filename='.$name);
			header('Pragma: no-cache');
			readfile($filename);

			if (file_exists($filename) && is_file($filename))
				@unlink($filename);

			exit;
		}
		else
			throw new NewsletterProTemplateException(NewsletterPro::getInstance()->l('An error occurred at the creation of the .zip file. Please check the CHMOD permissions.'));
	}

	public static function formatName($name)
	{
		return str_replace(array('(', ')'), '', Tools::strtolower(preg_replace('/\s+|(%20)+/i', '_', $name)));
	}

	public function delete()
	{
		$succeed = array();

		$module = NewsletterPro::getInstance();

		$secure_path = $module->tpl_location.'newsletter/'.pathinfo($this->dir_template, PATHINFO_FILENAME).'/';
		$secure_path = str_replace('\\', '/', $secure_path);
		$path = str_replace('\\', '/', $this->dir_template);

		if ($secure_path != $path)
			throw new NewsletterProTemplateException(sprintf($module->l('The file path "%s" is not the same as the secure path.'), $path));

		if (file_exists($this->css_path) && is_file($this->css_path))
			$succeed[] = unlink($this->css_path);

		$succeed[] = NewsletterProTools::deleteDirAndFiles($path);
				
		if (pqnp_config('NEWSLETTER_TEMPLATE') == $this->name)
			pqnp_config('NEWSLETTER_TEMPLATE', 'default.html');

		return !in_array(false, $succeed);
	}

	public function setForwarder($value)
	{
		$this->is_forwarder = (bool)$value;
	}

	public function getForwarderData($name)
	{
		if (isset($this->forwarder_data[$name]))
			return $this->forwarder_data[$name];
		return '';
	}

	public function setForwarderData($value)
	{
		$this->forwarder_data = $value;
	}
}