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

class NewsletterProTemplateContent
{
	public $html;

	private $variables = array();

	private $variables_chimp = array();

	private $template;

	const CONTENT_TITLE = 100;

	const CONTENT_HEADER = 101;

	const CONTENT_BODY = 102;

	const CONTENT_FOOTER = 103;

	const CONTENT_HTML = 104;

	const CONTENT_FULL = 105;

	const CONTENT_MESSAGE = 106;

	const CONTENT_CSS = 107;

	const CONTENT_CSS_LINK = 108;

	const CONTENT_CSS_FILE = 109;

	const CONTENT_CSS_GLOBAL_FILE = 110;

	const COMPARE_TYPE_FUNCTION = 100;

	const COMPARE_TYPE_OPERATION = 101;

	const COMPARE_RIGHT_TYPE_STRING = 100;

	const COMPARE_RIGHT_TYPE_INTEGER = 101;

	const COMPARE_RIGHT_TYPE_VARIABLE = 102;

	private $compare_type_functions = array(
		'isset'
	);

	private $compare_type_operations = array(
		'==', '!=', '<=', '>=', '==='
	);

	public static function newInstance()
	{
		return new self();
	}

	public function setTemplate($template)
	{
		$this->template = $template;
		return $this;
	}

	public static function getContentNames()
	{
		return array(
			self::CONTENT_TITLE => 'title',
			self::CONTENT_HEADER => 'header',
			self::CONTENT_BODY => 'body',
			self::CONTENT_FOOTER => 'footer',
			self::CONTENT_HTML => 'html',
			self::CONTENT_FULL => 'full',
			self::CONTENT_MESSAGE => 'message',
			self::CONTENT_CSS => 'css',
			self::CONTENT_CSS_LINK => 'css_link',
			self::CONTENT_CSS_FILE => 'css_file',
			self::CONTENT_CSS_GLOBAL_FILE => 'css_global_file'
		);
	}

	public function setVariables($variables)
	{
		$this->variables = array_merge($this->variables, $variables);
		return $this;
	}

	public function setChimpVariables($variables)
	{
		$this->variables_chimp = array_merge($this->variables_chimp, $variables);
		return $this;
	}

	public function variables()
	{
		return $this->variables;
	}

	public function variablesChimp()
	{
		return $this->variables_chimp;
	}

	public function setContent($html)
	{
		$this->html = $html;
		return $this;
	}

	public function setContentByIdLang($id_lang, $html)
	{
		$this->html[$id_lang] = $html;
		return $this;
	}

	public function getTitle($html)
	{
		if (preg_match('/<\s*?(title)\s*?.*?>(?P<content>.*?)<\s*?\/\s*?\1\s*?>/', $html, $match))
			return $match['content'];

		return false;
	}

	public function getHeader($html)
	{
		if (preg_match('/(?P<content>^[\s\S]*?<\s*?body\s*?.*?>)/i', $html, $match))
			return $match['content'];

		return false;
	}

	public function getBody($html)
	{
		if (preg_match('/<\s*?(body)\s*.*?>(?P<content>[\s\S]*)<s*?\/\s*?\1\s*?>/i', $html, $match))
			return $match['content'];

		return false;
	}

	public function getFooter($html)
	{
		if (preg_match('/(?P<content><\s*?\/\s*?\s*?body\s*?>[\s\S]*)/i', $html, $match))
			return $match['content'];

		return false;
	}

	public function getCSS($html)
	{
		$style = '';

		$css_content_array = $this->getFilesCSS();

		if (!empty($css_content_array))
			$style .= join("\n\n", $css_content_array);

		$header = $this->getHeader($html);

		if (preg_match_all('/<(?:\s*?)style[^>]*>(?P<content>[\s\S]*?)(<(?:\s*?)\/(?:\s*?)style(?:\s*?)>)/', $header, $match))
			$style .= implode("\n\n", $match['content']);


		return $style;
	}

	public function getConditions()
	{
		$conditions = array();
		foreach ($this->html as $id_lang => $value) 
		{
			preg_match_all('/\{(?:\s+)?if\s[\s\S]*?(\{(?:\s+)?\/(?:\s+)?if(?:\s+)?\})/', $value, $matches);
			$conditions[$id_lang] = $matches[0];
		}

		return $conditions;
	}

	public function getRenderConditions()
	{
		if (!isset($this->variables))
			throw new NewsletterProTemplateContentException('You must set the variables first.');
			
		$module = NewsletterPro::getInstance();

		$conditions = $this->getConditions();
		$conditions_render = array();

		foreach ($conditions as $id_lang => $conditions_array)
		{
			foreach ($conditions_array as $key => $condition) 
			{
				preg_match('/\{if[^}]+}(.*)\{\/if\}/', $condition, $condition_content);
				$condition_content = explode('{else}', $condition_content[1]);

				preg_match('/\{(?:\s+)?if\s(?:\s+)?(?P<content>[^}]+)\}/i', $condition, $compare);
				$compare = trim($compare['content']);

				$match_compare_functions = '/('.join('|', $this->compare_type_functions).')\((\w+)\)/';
				$match_compare_operations = '/(\w+)(?:\s+)?('.join('|', $this->compare_type_operations).')(?:\s+)?(.*)/';
				
				$compare_type = null;
				$compare_left = null;
				$compare_function = null;
				$compare_operation = null;
				$compare_rigt = null;
				$compare_rigt_type = null;

				if (preg_match($match_compare_functions, $compare, $compare_type_match))
				{
					$compare_type = self::COMPARE_TYPE_FUNCTION;
					$compare_function = $compare_type_match[1];
					$compare_rigt = $compare_type_match[2];
				}
				else if (preg_match($match_compare_operations, $compare, $compare_type_match))
				{
					$compare_type = self::COMPARE_TYPE_OPERATION;
					$compare_left = $compare_type_match[1];
					$compare_operation = $compare_type_match[2];
					$compare_rigt = $compare_type_match[3];

					if (preg_match('/^("|\')(.*)\1$/', $compare_rigt))
					{
						$compare_rigt_type = self::COMPARE_RIGHT_TYPE_STRING;
						$compare_rigt = trim($compare_rigt, '\'"');
					}
					else if (preg_match('/^[A-Za-z]+\w+$/', $compare_rigt))
						$compare_rigt_type = self::COMPARE_RIGHT_TYPE_VARIABLE;
					else
						$compare_rigt_type = self::COMPARE_RIGHT_TYPE_INTEGER;
				}
				else
					throw new NewsletterProTemplateContentException(sprintf($module->l('The condition "%s" is not valid.'), $compare));

				if ($compare_type == self::COMPARE_TYPE_FUNCTION)
				{
					if (!in_array($compare_function, $this->compare_type_functions))
						throw new NewsletterProTemplateContentException(sprintf($module->l('The condition function "%" is not valid.'), $compare_function));
					
					switch ($compare_function) 
					{
						case 'isset':

							if (isset($this->variables[$compare_rigt]) && Tools::strlen($this->variables[$compare_rigt]) > 0)
								$conditions_render[$id_lang][$condition] = $condition_content[0];
							else
							{
								if (isset($condition_content[1]))
									$conditions_render[$id_lang][$condition] = $condition_content[1];
								else
									$conditions_render[$id_lang][$condition] = '';
							}

							break;
						
						default:
							throw new NewsletterProTemplateContentException(sprintf($module->l('The condition function "%" is not defined'), $compare_function));
							break;
					}
				}
				else
				{
					if (!in_array($compare_operation, $this->compare_type_operations))
						throw new NewsletterProTemplateContentException(sprintf($module->l('The condition operator "%" is not valid.'), $compare_operation));


					if ($compare_rigt_type == self::COMPARE_RIGHT_TYPE_STRING)
						$compare_rigt = (string)$compare_rigt;
					else if ($compare_rigt_type == self::COMPARE_RIGHT_TYPE_VARIABLE)
					{
						if (isset($this->variables[$compare_rigt]))
							$compare_rigt = $this->variables[$compare_rigt];
						else
							$compare_rigt = null;
					}
					else
						$compare_rigt = (int)$compare_rigt;

					$variable_exists = (isset($this->variables[$compare_left]));

					if ($variable_exists)
					{
						$compare_left_value = $this->variables[$compare_left];

						switch ($compare_operation)
						{
							case '==':

								if ($compare_left_value == $compare_rigt)
									$conditions_render[$id_lang][$condition] = $condition_content[0];
								else
								{
									if (isset($condition_content[1]))
										$conditions_render[$id_lang][$condition] = $condition_content[1];
									else
										$conditions_render[$id_lang][$condition] = '';
								}

								break;
								
							case '!=':
								
								if ($compare_left_value != $compare_rigt)
									$conditions_render[$id_lang][$condition] = $condition_content[0];
								else
								{
									if (isset($condition_content[1]))
										$conditions_render[$id_lang][$condition] = $condition_content[1];
									else
										$conditions_render[$id_lang][$condition] = '';
								}

								break;
								
							case '<=':

								if ($compare_left_value <= $compare_rigt)
									$conditions_render[$id_lang][$condition] = $condition_content[0];
								else
								{
									if (isset($condition_content[1]))
										$conditions_render[$id_lang][$condition] = $condition_content[1];
									else
										$conditions_render[$id_lang][$condition] = '';
								}

								break;
								
							case '>=':

								if ($compare_left_value >= $compare_rigt)
									$conditions_render[$id_lang][$condition] = $condition_content[0];
								else
								{
									if (isset($condition_content[1]))
										$conditions_render[$id_lang][$condition] = $condition_content[1];
									else
										$conditions_render[$id_lang][$condition] = '';
								}

								break;
								
							case '===':
								if ($compare_left_value === $compare_rigt)
									$conditions_render[$id_lang][$condition] = $condition_content[0];
								else
								{
									if (isset($condition_content[1]))
										$conditions_render[$id_lang][$condition] = $condition_content[1];
									else
										$conditions_render[$id_lang][$condition] = '';
								}
								break;
							
							default:
								throw new NewsletterProTemplateContentException(sprintf($module->l('The condition operator "%" is not defined'), $compare_operation));
								break;
						}
					}
					else
					{
						if (isset($condition_content[1]))
							$conditions_render[$id_lang][$condition] = $condition_content[1];
						else
							$conditions_render[$id_lang][$condition] = '';
					}
				}
			}
		}

		return $conditions_render;
	}

	public function html($content_type = NewsletterProTemplateContent::CONTENT_FULL, $add_css = false)
	{
		$content_lang = $this->html;

		if ($add_css)
		{
			foreach ($content_lang as $id_lang => $content) 
				$content_lang[$id_lang] = $this->addDefaultCSS($content);
		}

		return $this->content($content_lang, $content_type);
	}

	public function render($content_type = NewsletterProTemplateContent::CONTENT_FULL)
	{
		if (!isset($this->template))
			throw new NewsletterProTemplateContentException(sprintf('The variable "%s" is not set.', 'template'));

		$render_condition = $this->getRenderConditions();
		$render = $this->html;

		foreach ($render as $id_lang => $content)
		{
			// execute condtions
			if (isset($render_condition[$id_lang]))
				$content = str_replace(array_keys($render_condition[$id_lang]), array_values($render_condition[$id_lang]), $content);

			// render variables, I've made this for mailchimp
			foreach ($this->variables as $variable_name => $value) 
				$this->variables[$variable_name] = preg_replace_callback('/\{(\w+)\}/', array($this, 'replaceVariables'), $value);
	
			// render chimp variables, I've made this for mailchimp
			foreach ($this->variables_chimp as $variable_name => $value) 
				$this->variables_chimp[$variable_name] = preg_replace_callback('/\{(\w+)\}/', array($this, 'replaceVariables'), $value);

			// replace variables
			$content = preg_replace_callback('/\{(\w+)\}/', array($this, 'replaceVariables'), $content);
			// replace chimp variables
			$content = preg_replace_callback('/\*\|(\w+)\|\*/', array($this, 'replaceChimpVariables'), $content);

			$content = $this->replaceTemplateVariables($content);

			$content = $this->addDefaultCSS($content);

			$render[$id_lang] = $content;
		}

		// replace dynamic variables
		$render = NewsletterProTemplateDynamicVariables::newInstance($render, $this->template->user)->render();


		$header_lang = array();
		foreach ($render as $id_lang => $render_value)
		{
			$header = array();

			if (preg_match('/<!-- start header -->[\s\S]*?<!--([\s\S]*)?-->[\s\S]*?<!-- end header -->/', $render_value, $match))
			{
					
				$match = explode(';', $match[1]);

				foreach ($match as $value)
				{
					$value = trim($value);
					if (Tools::strlen($value) > 0)
					{
						$exp = explode('=', $value);
						$result = trim($exp[1]);
						$header[trim($exp[0])] = $result;
					}
				}
			}

			$header_lang[$id_lang] = $header;
		}

			foreach ($render as $id_lang => $render_value)
			{

				// place the convert css to inline style afther the dynamic variables to prevent a template error
				if (isset($header_lang[$id_lang]['convertInlineCss']))
				{
					if ($header_lang[$id_lang]['convertInlineCss'] == 'true')
						$render[$id_lang] = NewsletterProCSStoInlineStyle::convert($render_value);
				}
				else if ((bool)pqnp_config('CONVERT_CSS_TO_INLINE_STYLE'))
					$render[$id_lang] = NewsletterProCSStoInlineStyle::convert($render_value);
			}

		return $this->content($render, $content_type);
	}

	public function content($content_lang, $content_type)
	{
		$render_content = array();
		$content_names = $this->getContentNames();
		
		switch ($content_type) 
		{
			case self::CONTENT_TITLE:
				foreach ($content_lang as $id_lang => $content)
					$render_content[$id_lang] = $this->getTitle($content);
				break;

			case self::CONTENT_HEADER:
				foreach ($content_lang as $id_lang => $content)
					$render_content[$id_lang] = $this->getHeader($content);
				break;

			case self::CONTENT_BODY:
				foreach ($content_lang as $id_lang => $content)
					$render_content[$id_lang] = $this->getBody($content);
				break;

			case self::CONTENT_FOOTER:
				foreach ($content_lang as $id_lang => $content)
					$render_content[$id_lang] = $this->getFooter($content);
				break;

			case self::CONTENT_HTML:
				foreach ($content_lang as $id_lang => $content)
					$render_content[$id_lang] = $content;
				break;

			case self::CONTENT_FULL:

				foreach ($content_lang as $id_lang => $content)
				{
					$render_content[$id_lang][$content_names[self::CONTENT_TITLE]] = $this->getTitle($content);
					$render_content[$id_lang][$content_names[self::CONTENT_HEADER]] = $this->getHeader($content);
					$render_content[$id_lang][$content_names[self::CONTENT_BODY]] = $this->getBody($content);
					$render_content[$id_lang][$content_names[self::CONTENT_FOOTER]] = $this->getFooter($content);
					$render_content[$id_lang][$content_names[self::CONTENT_HTML]] = $content;
					$render_content[$id_lang][$content_names[self::CONTENT_CSS]] = $this->getCSS($content);
					$render_content[$id_lang][$content_names[self::CONTENT_CSS_LINK]] = $this->cssLink($id_lang);
					$render_content[$id_lang][$content_names[self::CONTENT_CSS_FILE]] = $this->template->css;
					$render_content[$id_lang][$content_names[self::CONTENT_CSS_GLOBAL_FILE]] = $this->template->getCSSGlobalFileContent();
				}
				break;

			case self::CONTENT_MESSAGE:

				foreach ($content_lang as $id_lang => $content)
				{
					$render_content[$id_lang][$content_names[self::CONTENT_TITLE]] = $this->getTitle($content);
					$render_content[$id_lang][$content_names[self::CONTENT_BODY]] = $content;
				}
				break;
			
			case self::CONTENT_CSS:
				foreach ($content_lang as $id_lang => $content)
					$render_content[$id_lang] = $this->getCSS($content);

				break;

			case self::CONTENT_CSS_LINK:
				foreach (array_keys($content_lang) as $id_lang)
					$render_content[$id_lang] = $this->cssLink($id_lang);


			case self::CONTENT_CSS_FILE:
				foreach (array_keys($content_lang) as $id_lang)
					$render_content[$id_lang] = $this->template->css;

			case self::CONTENT_CSS_GLOBAL_FILE:
				foreach (array_keys($content_lang) as $id_lang)
					$render_content[$id_lang] = $this->template->getCSSGlobalFileContent();

				break;

			default:
				throw new NewsletterProTemplateContentException('Invalid content type.');
				break;
		}

		return $render_content;
	}

	private function replaceVariables($match)
	{
		$variable_name = $match[1];
		return (isset($this->variables[$variable_name]) ? $this->variables[$variable_name] : '{'.$variable_name.'}');
	}

	private function replaceChimpVariables($match)
	{
		$variable_name = $match[1];
		return (isset($this->variables_chimp[$variable_name]) ? $this->variables_chimp[$variable_name] : '*|'.$variable_name.'|*');
	}

	private function replaceTemplateVariables($content)
	{
		$title = $this->getTitle($content);
		if ($title)
		{
			$content = str_replace('{newsletter_title}', urlencode($title), $content);

			if (Tools::isSubmit('testCampaign'))
				ddd($content);
		}

		if ($this->template instanceof NewsletterProTemplateHistory)
		{
			$module = NewsletterPro::getInstance();

			$content = str_replace('{id_newsletter_pro_tpl_history}', $this->template->id_history, $content);

			// viewInBrowser will remove the opened email tracking if the browser is viewed by admin
			if (!Tools::isSubmit('viewInBrowser'))
			{
				$tracking_link = $module->url_location.'opened_email.php?email='.(string)$this->variables['email'].'&token='.$module->getTokenByIdHistory((int)$this->template->id_history);
				$tracking_img = '<img style="display: none; margin: 0; padding: 0; float: left; position: absolute;" src="'.$tracking_link.'" height="1" width="1" data-embed="0"/>';

				if ($body_end = $this->getEndBodyStrpos($content))
					$content = substr_replace($content, $tracking_img, $body_end, 0);
				else
					$content .= $tracking_img;
			}
		}
		else
			$content = str_replace('&id_newsletter={id_newsletter_pro_tpl_history}', '', $content);

		return $content;
	}

	private function getEndBodyStrpos($content)
	{
		if (preg_match('/(?P<body>\<\s*?\/\s*?body\s*?\>)/', $content, $match))
			return (int)strpos($content, $match['body']);
		return false;
	}

	public function cssLink($id_lang = null)
	{
		$links = array();
		$link = _MODULE_DIR_.'newsletterpro/css.php?getNewsletterTemplateCSS&name='.$this->template->name.'&id_lang=%d&uid='.uniqid();

		foreach (array_keys($this->template->dir_template_lang_file) as $id_l) 
			$links[$id_l] = sprintf($link, $id_l);

		if (isset($this->template->load_id_lang))
			return $links[$this->template->load_id_lang];

		if (isset($id_lang))
			return $links[$id_lang];

		return $links;
	}

	private function addDefaultCSS($content)
	{
		$open_head_tag = '<head>';
		$head_pos = strpos($content, $open_head_tag);

		if ($head_pos === false)
			return $content;

		$after_head_post = $head_pos + Tools::strlen($open_head_tag);

		$css_content_array = $this->getFilesCSS();

		if (!empty($css_content_array))
			$content = substr_replace($content, "\n".'<style type="text/css">'."\n".join("\n\n", $css_content_array)."\n".'</style>'."\n", $head_pos, 0);

		return $content;
	}

	private function getFilesCSS()
	{
		$css_content_array = array();

		$css_global = $this->template->getCSSGlobalFileContent();
		
		if ($css_global)
			$css_content_array[] = $css_global;

		if ($this->template->css)
			$css_content_array[] = $this->template->css;

		return $css_content_array;
	}
}