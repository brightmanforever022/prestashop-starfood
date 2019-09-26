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

class NewsletterProTemplateController extends NewsletterProController
{
	public function newInstance()
	{
		return new self();
	}

	public function initContent()
	{
		return parent::initContent();
	}

	public function postProcess()
	{
		parent::postProcess();

		$action = 'submit_template_controller';

		if (Tools::isSubmit($action))
		{
			@ini_set('max_execution_time', '2880');
			ob_clean();
			ob_end_clean();

			if (Tools::getValue('token') != $this->token)
				$this->display('Invalid Token!');

			switch (Tools::getValue($action))
			{
				case 'getNewsletterTemplates':
					$this->display($this->getNewsletterTemplates(), true);
				break;

				case 'deleteTemplate':
					$this->display($this->deleteTemplate($_POST), true);
				break;

				case 'saveNewsletterPageTitle':
					$title = Tools::getValue('saveNewsletterPageTitle');
					$id_lang = (int)Tools::getValue('id_lang');
					$this->display($this->saveNewsletterPageTitle($title, $id_lang));
				break;

				case 'saveNewsletterTemplate':
					$name = Tools::getValue('name');
					$content = Tools::getValue('content');
					$css = Tools::getValue('css');
					$css_global = Tools::getValue('css_global');
					$this->display($this->saveNewsletterTemplate($name, $content, $css, $css_global), true);
				break;

				case 'saveAsNewsletterTemplate':
					$name = Tools::getValue('name');
					$content = Tools::getValue('content');
					$css = Tools::getValue('css');
					$css_global = Tools::getValue('css_global');
					$this->display($this->saveAsNewsletterTemplate($name, $content, $css, $css_global), true);
				break;

				case 'renderTemplate':
					$name = Tools::getValue('name');
					$id_lang = (int)Tools::getValue('id_lang');
					$this->display($this->renderTemplate($name, $id_lang), true);
				break;

				case 'viewTemplate':
					$name = Tools::getValue('name');
					$id_lang = (int)Tools::getValue('id_lang');
					die($this->viewTemplate($name, $id_lang));
				break;

				case 'changeTemplate':
					$name = Tools::getValue('name');
					$this->display($this->changeTemplate($name));
				break;

				case 'inputImportHTML':
					if (isset($_FILES['inputImportHTML']))
						$this->display($this->importHTML($_FILES['inputImportHTML']), true);
				break;
			}
		}
	}

	/**
	 * Get newsletter templates
	 *
	 * @return json
	 */
	public function getNewsletterTemplates($json_encode = true)
	{
		$list = array();
		$path = $this->module->dir_location.'mail_templates/newsletter/';

		$result = NewsletterProTools::getDirectoryIterator($path, '/.+/');

		$attachments = NewsletterProAttachment::getTemplatesName();
		$default_lang = new Language((int)pqnp_config('PS_LANG_DEFAULT'));

		$id = 1;
		$i = 0;
		foreach ($result as $file)
		{
		    
			if ($file->isDir())
			{
				$name = pathinfo($file->getFilename(), PATHINFO_FILENAME);
				$filename = $name.'.html';
				$default_template = $file->getPathName().'/'.$default_lang->iso_code.'/'.$filename;

				$default_template_iso = $file->getPathName().'/'.$default_lang->iso_code;

				// copy from the en files
				if (!file_exists($default_template))
				{
					$en_template = $file->getPathName().'/en/'.$filename;

					if (file_exists($en_template))
					{
						$newsletter_dir = $this->module->tpl_location.'newsletter/';
						$np_index = $newsletter_dir.'index.php';

						if (!file_exists($default_template_iso))
						{
							if (@mkdir($default_template_iso, 0777))
							{
								if (file_exists($np_index))
									@copy($np_index, $default_template_iso.'/index.php');

								@copy($en_template, $default_template);
							}
						}
					}
				}

				// add the template to the list
				if (file_exists($default_template) && is_file($default_template))
				{
					$list[$i]['id'] = $id;
					$list[$i]['name'] = Tools::ucfirst(str_replace('_', ' ', $name));
					$list[$i]['filename'] = $filename;
					$list[$i]['path'] = $file->getPathName();
					$list[$i]['date'] = date($this->context->language->date_format_full, filemtime($default_template));
					$list[$i]['mtime'] = $file->getMTime();			
					$list[$i]['filemtime'] = filemtime($default_template);
					$list[$i]['selected'] = false;
					$list[$i]['attachments'] = array();

					if (in_array($filename, $attachments))
					{
						$attachment = NewsletterProAttachment::newInstanceByTemplateName($filename);
						if ($attachment)
							$list[$i]['attachments'] = $attachment->files();
					}

					if ($list[$i]['filename'] == pqnp_config('NEWSLETTER_TEMPLATE'))
						$list[$i]['selected'] = true;

					$id++;
					$i++;
				}
			}
		}

		usort($list, array($this, 'sortByMTime'));

		if ($json_encode)
			return NewsletterProAjaxResponse::jsonEncode($list);

		return $list;
	}

	private static function sortByMTime($a, $b)
	{
		return (int)$a['filemtime'] < (int)$b['filemtime'];
	}

	/**
	 * Delete template
	 *
	 * @param  array $data
	 * @return json
	 */
	public function deleteTemplate($data)
	{
		try
		{
			$module = NewsletterPro::getInstance();
			$name = $data['filename'];
			$path = $data['path'];

			if ((int)pqnp_ini_config('demo_mode'))
			{
				$demo_return = NewsletterProDemoMode::deleteTemplate($name);

				if ($demo_return)
					return NewsletterProDemoMode::deleteTemplate($name);
			}

			if (!file_exists($path))
				throw new Exception($module->l('File not exists!'));

			$template = NewsletterProTemplate::newFile($name)->load();
			$template->delete();
		}
		catch (Exception $e)
		{
			$this->response->addError($e->getMessage());
		}

		return $this->response->display();
	}

	/**
	 * Save the newsletter template page title
	 *
	 * @param  string $title
	 * @return boolean
	 */
	public function saveNewsletterPageTitle($title, $id_lang)
	{
		try
		{
			$template_name = pqnp_config('NEWSLETTER_TEMPLATE');

			if ($template_name == 'default.html')
				throw new Exception($this->l('Save a copy of the default template to change the title.'));

			$template = NewsletterProTemplate::newFile($template_name)->load($id_lang);
			$template->changeTitle(array($id_lang => $title));
			$template->save();
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}

		return $this->response->display();
	}

	public function saveNewsletterTemplate($template_name, $content, $css, $css_global)
	{
		try
		{
			if ($template_name === 'default.html')
				throw new Exception(sprintf($this->l('The default template cannot be overridden. You can only create a copy of this template.'), $template_name));
				
			$template = NewsletterProTemplate::newFile($template_name)->load();
			$template->setContent($content);
			$template->css = $css;
			$template->save();
			$template->saveGlobalCSS($css_global);

			$this->response->set('message', $this->l('Template saved successfully.'));
			$this->response->set('html', $template->html());
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}

		return $this->response->display();
	}

	public function saveAsNewsletterTemplate($template_name, $content, $css, $css_global)
	{
		try
		{
			$template_name = preg_replace('/[\s\.\\/]+/i', '_', $template_name);

			$message = NewsletterPro::getInstance()->verifyName($template_name);

			if ($message !== true)
				throw new Exception($message);

			$template_name = $template_name.'.html';

			if ($template_name === 'default.html')
				throw new Exception(sprintf($this->l('The default template cannot be overridden. You can only create a copy of this template.'), $template_name));

			if (NewsletterProTemplate::templateExists($template_name))
				throw new Exception(sprintf($this->l('The template name "%s" already exists.'), $template_name));

			$content_build = NewsletterProTemplate::buildContent($content);

			$template = NewsletterProTemplate::newString(array($template_name, $content_build))->load();
			$template->css = $css;
			$template->save();
			$template->saveGlobalCSS($css_global);

			pqnp_config('NEWSLETTER_TEMPLATE', $template->name);

			$this->response->set('template_name', $template->name);
			$this->response->set('message', $this->l('Template saved successfully.'));
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}

		return $this->response->display();
	}

	public function viewTemplate($template_name, $id_lang)
	{
		try
		{
			$template = NewsletterProTemplate::newFile($template_name, array(null, NewsletterProTemplateUser::USER_TYPE_EMPLOYEE))->load($id_lang);
			return $template->render(NewsletterProTemplateContent::CONTENT_HTML);
		}
		catch(Exception $e)
		{
			return $e->getMessage();
		}
	}

	public function renderTemplate($template_name, $id_lang)
	{
		try
		{
			$this->response->set('render', $this->viewTemplate($template_name, $id_lang));
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}

		return $this->response->display();
	}

	public function changeTemplate($template_name)
	{	
		try
		{
			$template = NewsletterProTemplate::newFile($template_name, array(null, NewsletterProTemplateUser::USER_TYPE_EMPLOYEE))->load();
			$this->response->set('html', $template->htmlInvert());
			pqnp_config('NEWSLETTER_TEMPLATE', $template_name);
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}

		return $this->response->display();
	}

	public function exportHTML($use_render = false)
	{
		$template = NewsletterProTemplate::newFile(pqnp_config('NEWSLETTER_TEMPLATE'), array(null, NewsletterProTemplateUser::USER_TYPE_EMPLOYEE))->load();
		$template->export($use_render);
	}

	public function importHTML($file)
	{
		try
		{
			$module = NewsletterPro::getInstance();
			$validate = $module->verifyFileErros($file);

			if ($validate !== true)
				throw new Exception($validate);

			$name = NewsletterProTemplate::formatName($file['name']);
			$filename = pathinfo($name, PATHINFO_FILENAME);
			$tmp_name = $file['tmp_name'];
			$extension = pathinfo($name, PATHINFO_EXTENSION);

			if (!preg_match('/html|htm|zip/i', $extension))
				throw new Exception(sprintf($module->l('Only the file extension "%s" are allowed.'), 'html, htm, zip'));

			$validate_name = $module->verifyName($filename);

			if ($validate_name !== true)
				throw new Exception($validate_name);
			
			$template_path = 'mail_templates/newsletter/';
			$path = $module->dir_location.$template_path;

			if (!file_exists($path))
				throw new Exception(sprintf($module->l('The file "%s" does not exists.'), $path));

			$full_path = $path.$filename;
			$full_path = NewsletterProTools::getFileNameIncrement($full_path);
			$name = pathinfo($full_path, PATHINFO_BASENAME);


			if ($extension == 'zip')
			{
				$languages = Language::getLanguages(false);
				$languages_iso = array();
				

				foreach ($languages as $language)
					$languages_iso[$language['iso_code']] = $language;

				$content = array();

				$zip = new ZipArchive;

				if ($zip->open($tmp_name) === true)
				{
					$zip_name = pathinfo($tmp_name, PATHINFO_FILENAME);

					for ($i = 0; $i < $zip->numFiles; $i++)
					{  
						$file = $zip->getNameIndex($i);

						if (preg_match('/.html$/', $file))
						{
							if (preg_match('/^\w+/', $file, $match))
							{
								$iso_code = $match[0];

								if (isset($languages_iso[$iso_code]))
								{
									$file_stream = $zip->getStream($file);
									
									if (!$file_stream)
										throw new Exception(sprintf($module->l('Cannot read the content of the .zip file "%s".'), $file));
									
									$html = '';

									while (!feof($file_stream))
										$html .= fread($file_stream, 2);

									$content[$languages_iso[$iso_code]['id_lang']] = $html;
								}
							}
						}
					}

					$zip->close();
				}
				else
					throw new Exception(sprintf($module->l('Cannot open the file "%s". Please check the CHMOD permissions.'), $tmp_name));

				if (empty($content))
					throw new Exception('This .zip file is not compatible for import.');
				

				$id_default_lang = pqnp_config('PS_LANG_DEFAULT');

				if (!isset($content[$id_default_lang]))
					$content[$id_default_lang] = $content[key($content)];

				$template = NewsletterProTemplate::newString(array($name.'.html', $content))->load();
				$template->save();
			}
			else
			{
				$content = Tools::file_get_contents($tmp_name);

				if ($content === false)
					throw new Exception(sprintf($module->l('Cannot get the content of the file "%s". Please check the CHMOD permissions.'), $tmp_name));

				$template = NewsletterProTemplate::newString(array($name.'.html', $content))->load();
				$template->save();
			}

			$this->response->set('name', $name.'.html');
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}

		return $this->response->display();
	}
}