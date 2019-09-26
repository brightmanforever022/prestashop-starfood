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

class NewsletterProFrontSubscriptionController extends NewsletterProController
{
	public static $instance;

	public function newInstance()
	{
		return new self();
	}
	
	public static function getInstance()
	{
		if (!isset(self::$instance))
			self::$instance = new NewsletterProFrontSubscriptionController();
		return self::$instance;
	}

	public function initContent()
	{
		parent::initContent();

		$template = $this->getSubscriptionTemplate();

		$this->context->smarty->assign(array(
			'tab_id'                                   => 'tab_newsletter_content_15',
			'subscription_template'                    => $template,
			'subscription_template_view_in_front'      => NewsletterProSubscriptionTpl::getFrontLink(),
			'subscription_template_view_in_front_lang' => $this->getViewInFrontLinks(),
			'front_pages'							   => NewsletterProSubscriptionTpl::getPages(),
			'show_on_pages'							   => array(
				NewsletterProSubscriptionTpl::SHOW_ON_PAGES_NONE => $this->module->l('none'),
				NewsletterProSubscriptionTpl::SHOW_ON_PAGES_ALL  => $this->module->l('all'),
			),
			'when_to_show'                             => array(
				NewsletterProSubscriptionTpl::WHEN_TO_SHOW_POPUP_COOKIE => $this->module->l('Cookie Lifetime'),
				NewsletterProSubscriptionTpl::WHEN_TO_SHOW_POPUP_ALWAYS => $this->module->l('Always (for testing)'),
			),
		));

		$this->js_data = array(
			'subscription_template'           => NewsletterProConfigurationShop::get('SUBSCRIPTION_TEMPLATE'),
			'subscription_template_id'        => (int)$template['id'],
			'subscription_template_body_info' => array(
				'body_width'     => $template['body_width'],
				'body_max_width' => $template['body_max_width'],
				'body_min_width' => $template['body_min_width'],
				'body_top'       => $template['body_top'],
			),
			'filter_list_of_interest' => $this->getFilterListOfInterest(),
			'mandatory_fields'        => $template['mandatory_fields'],
		);

		$this->content .= $this->context->smarty->fetch(pqnp_template_path($this->module->dir_location.'views/templates/admin/tabs/front_subscription.tpl'));
	}

	public function setMedia()
	{
		parent::setMedia();

		$css_path = $this->module->getCssPath();

		$this->controller->addCss(array(
			$this->module->uri_location.'views/css/admin_front_subscription.css',
			// correct css
			$css_path.'admin_front_subscription.css',
		));

		$this->controller->addJS(array(
			$this->module->uri_location.'views/js/subscription_template.js',
			$this->module->uri_location.'views/js/front_subscription.js',
		));
	}

	public function postProcess()
	{
		parent::postProcess();

		$action = 'submitSubscriptionController';

		if (Tools::isSubmit($action))
		{
			@ini_set('max_execution_time', '2880');
			@ob_clean();
			@ob_end_clean();

			if (Tools::getValue('token') != $this->token)
				$this->display('Invalid Token!');

			switch (Tools::getValue($action))
			{
				case 'addNewListOfInterest':
					$this->display($this->addNewListOfInterest(Tools::getValue('value')));
					break;

				case 'deleteListOfInterestRecord':
					$this->display($this->deleteListOfInterestRecord((int)Tools::getValue('id')));
					break;

				case 'updateListOfInterestRecord':
					$this->display($this->updateListOfInterestRecord($_POST));
					break;

				case 'getListOfInterest':
					$id_lang = Tools::getValue('id_lang');
					$this->display($this->getListOfInterest($id_lang));
					break;

				case 'getListOfInterestNameLang':
					$this->display($this->getListOfInterestNameLang((int)Tools::getValue('id')));
					break;

				case 'getTemplatesDataGrid':
					$this->display($this->getTemplatesDataGrid());
					break;

				case 'updateTemplatesDataGrid':
					$this->display($this->updateTemplatesDataGrid($_POST));
					break;

				case 'deleteTemplatesDataGrid':
					$this->display($this->deleteTemplatesDataGrid(Tools::getValue('id')));
					break;

				case 'ajaxSetTemplateById':
					$this->display($this->ajaxSetTemplateById((int)Tools::getValue('id'), (int)Tools::getValue('id_lang')));
					break;

				case 'saveDisplayGender':
					$this->display($this->saveDisplayGender((int)Tools::getValue('id_template'), (int)Tools::getValue('value')));
					break;

				case 'saveDisplayFirstName':
					$this->display($this->saveDisplayFirstName((int)Tools::getValue('id_template'), (int)Tools::getValue('value')));
					break;

				case 'saveDisplayLastName':
					$this->display($this->saveDisplayLastName((int)Tools::getValue('id_template'), (int)Tools::getValue('value')));
					break;

				case 'saveDisplayLanguage':
					$this->display($this->saveDisplayLanguage((int)Tools::getValue('id_template'), (int)Tools::getValue('value')));
					break;

				case 'saveDisplayBirthday':
					$this->display($this->saveDisplayBirthday((int)Tools::getValue('id_template'), (int)Tools::getValue('value')));
					break;

				case 'saveDisplayListOfInterest':
					$this->display($this->saveDisplayListOfInterest((int)Tools::getValue('id_template'), (int)Tools::getValue('value')));
					break;

				case 'saveDisplaySubscribeMessage':
					$this->display($this->saveDisplaySubscribeMessage((int)Tools::getValue('id_template'), (int)Tools::getValue('value')));
					break;

				case 'saveListOfInterestType':
					$this->display($this->saveListOfInterestType((int)Tools::getValue('id_template'), (int)Tools::getValue('value')));
					break;

				case 'saveAllowMultipleTimeSubscription':
					$this->display($this->saveAllowMultipleTimeSubscription((int)Tools::getValue('id_template'), (int)Tools::getValue('value')));
					break;

				case 'saveActivateTemplate':
					$this->display($this->saveActivateTemplate((int)Tools::getValue('id_template'), (int)Tools::getValue('value')));
					break;

				case 'ajaxSaveTemplate':
					$this->display($this->ajaxSaveTemplate((int)Tools::getValue('id_template'), Tools::getValue('value'), (int)Tools::getValue('id_lang')));
					break;

				case 'ajaxSaveAsTemplate':
					$this->display($this->ajaxSaveAsTemplate(Tools::getValue('name'), Tools::getValue('value'), (int)Tools::getValue('id_lang')));
					break;

				case 'ajaxGetViewLink':
					$this->display($this->ajaxGetViewLink((int)Tools::getValue('id_template'), (int)Tools::getValue('id_lang')));
					break;

				case 'ajaxSetSetting':
					$this->display($this->ajaxSetSetting((int)Tools::getValue('id_template'), Tools::getValue('field'), Tools::getValue('value')));
					break;

				case 'ajaxSetMandatory':
					$this->display($this->ajaxSetMandatory((int)Tools::getValue('id_template'), Tools::getValue('field'), (bool)Tools::getValue('checked')));
					break;

				case 'showSubscriptionHelp':
					$this->display($this->showSubscriptionHelp());
					break;

				case 'showLoadBackup':
					$this->display($this->showLoadBackup());
					break;

				case 'ajaxGetActiveTemplatesVouchersErrors':
					$this->display($this->ajaxGetActiveTemplatesVouchersErrors());
					break;

				case 'ajaxCreateBackupSubscriptionTemplate':
					$this->display($this->ajaxCreateBackupSubscriptionTemplate(Tools::getValue('name')));
					break;

				case 'getSubscriptionTemplateBackup':
					$this->display($this->getSubscriptionTemplateBackup());
					break;

				case 'ajaxDeleteBackupSubscriptionTemplate':
					$this->display($this->ajaxDeleteBackupSubscriptionTemplate(Tools::getValue('name')));
					break;

				case 'ajaxLoadBackupSubscriptionTemplate':
					$this->display($this->ajaxLoadBackupSubscriptionTemplate(Tools::getValue('name')));
					break;

			}
		}
		else if (Tools::isSubmit('getSubscriptionTemplateView'))
		{
			@ob_clean();
			@ob_end_clean();

			$id      = (int)Tools::getValue('idTemplate');
			$id_lang = (int)Tools::getValue('idLangTemplate');

			if (!$this->isValidLanguageId($id_lang))
				$this->display($this->module->l('Invalid language id.'));

			$template = new NewsletterProSubscriptionTpl($id);
			if (Validate::isLoadedObject($template))
			{

				// createTemplate
				$tpl = $this->context->smarty->createTemplate(pqnp_template_path($this->module->dir_location.'views/templates/admin/subscription_template_view.tpl'));

				$render = $template->render($id_lang);
				// for tests
				// $content = Tools::file_get_contents('filename_example.html');
				// $render = $template->getRenderedContent($content);

				$tpl->assign(array(
					'head'                             => $template->getGlobalHeader('/modules\/newsletterpro|jquery|bootstrap\.min\.js|jquery\.uniform\.min\.js/'), // filter the undesired scripts that can cause an error
					'render_template'                  => $render,
					'link'                             => new Link(),
					'tpl_location'                     => $this->module->dir_location.'views/',
					'subscription_template_front_info' => $template->getJSData(),
				));

				$content = $tpl->fetch();

				$this->display($content);
			}
			else
				$this->display($this->module->l('Invalid template id.'));
		}
	}

	public function isValidLanguageId($id_lang)
	{
		foreach (Language::getLanguages(false) as $lang)
		{
			if ((int)$id_lang == (int)$lang['id_lang'])
				return true;
		}
		return false;
	}

	public function getListOfInterest($id_lang = null)
	{
		if (!isset($id_lang) || $id_lang == false)
			$id_lang = $this->context->language->id;

		$list = NewsletterProListOfInterest::getList($id_lang, $this->context->shop->id);

		return Tools::jsonEncode($list);
	}

	public function addNewListOfInterest($values)
	{
		$errors = array();
		$response = array(
				'status' => false,
				'errors' => &$errors,
			);

		$default_lang = $this->getConfiguration('PS_LANG_DEFAULT');

		$fields = $this->sanitizeLangFields($values);

		if (!isset($fields[$default_lang]))
			$errors[] = $this->module->l('De default language field is missing.');
		else if (empty($fields[$default_lang]))
			$errors[] = $this->module->l('De default language field cannot be empty.');

		if (empty($errors))
		{
			$loi = new NewsletterProListOfInterest();
			$loi->fillField('name', $fields);
			$loi->active = 1;
			if (!$loi->add())
				$errors = array_merge($errors, $loi->getErrors());
			else
				$response['status'] = true;
		}

		return Tools::jsonEncode($response);
	}

	public function sanitizeLangFields($fields)
	{
		$default_lang = $this->getConfiguration('PS_SHOP_DEFAULT');

		foreach ($fields as $id_lang => $value)
		{
			$fields[$id_lang] = trim($value);

			if (isset($fields[$default_lang]) && !empty($fields[$default_lang]) && empty($value))
				$fields[$id_lang] = $fields[$default_lang];
		}

		return $fields;
	}

	public function deleteListOfInterestRecord($id)
	{
		$loi = new NewsletterProListOfInterest($id);

		if (Validate::isLoadedObject($loi))
		{
			try
			{
				return (int)$loi->delete();
			}
			catch(Exception $e)
			{
				return 0;
			}
		}

		return 0;
	}

	public function updateListOfInterestRecord($post)
	{
		$loi = new NewsletterProListOfInterest((int)$post['data']['id_newsletter_pro_list_of_interest']);

		if (Validate::isLoadedObject($loi))
		{
			try
			{
				if (isset($post['name']))
					$loi->name = $post['name'];

				$loi->active   = (int)$post['data']['active'];
				$loi->position = (int)$post['data']['position'];

				return (int)$loi->update();
			}
			catch(Exception $e)
			{
				return 0;
			}
		}

		return 0;
	}

	public function getListOfInterestNameLang($id)
	{
		$response = $this->response();

		$loi = new NewsletterProListOfInterest($id);
		if (Validate::isLoadedObject($loi))
			$response->set('name', $loi->name);
		else
			$response->addError($this->module->l('An error occurred.'));

		return $response->display();
	}

	public function getSubscriptionTemplate()
	{
		$current_template_name = NewsletterProConfigurationShop::get('SUBSCRIPTION_TEMPLATE');

		$template = NewsletterProSubscriptionTpl::getTemplateByName($current_template_name);
		// if the template not exists, the default template will be loaded
		if (!$template)
		{
			if (NewsletterProConfigurationShop::updateValue('SUBSCRIPTION_TEMPLATE', 'default'))
				$template = NewsletterProSubscriptionTpl::getTemplateByName('default');
			else
				$this->controller->errors[] = $this->module->l(sprintf('The template with the name "%s" cannot be loaded.', 'defalut'));
		}

		if (!$template)
		{
			$template = new NewsletterProSubscriptionTpl();
			$this->controller->errors[] = $this->module->l('Invalid subscription template for loading.');
		}

		return $this->getTemplateFields($template);
	}

	public function getTemplateFields($template, $id_lang = null)
	{
		if (!isset($id_lang))
			$id_lang = (int)$this->context->language->id;

		return array(
			'id'                                   => $template->id,
			'name'                                 => $template->name,
			'voucher'                              => $template->voucher,
			'content'                              => $template->content,
			'subscribe_message'                    => $template->subscribe_message,
			'email_subscribe_voucher_message'      => $template->email_subscribe_voucher_message,
			'email_subscribe_confirmation_message' => $template->email_subscribe_confirmation_message,

			'css_style'                            => $template->css_style,
			'css_style_global'                     => $template->getCssGlobalStyleContent(),
			'css_links'                            => $template->getGlobalStyleLinks(true),
			'view'                                 => $this->getSubscriptionTemplateViewLink($template->id, $id_lang),

			'display_gender'                       => (int)$template->display_gender,
			'display_firstname'                    => (int)$template->display_firstname,
			'display_lastname'                     => (int)$template->display_lastname,
			'display_language'                     => (int)$template->display_language,
			'display_birthday'                     => (int)$template->display_birthday,
			'display_list_of_interest'             => (int)$template->display_list_of_interest,
			'display_subscribe_message'            => (int)$template->display_subscribe_message,
			'list_of_interest_type'                => (int)$template->list_of_interest_type,
			'active'                               => (int)$template->active,

			'allow_multiple_time_subscription'     => (int)$template->allow_multiple_time_subscription,

			'body_width'                           => $this->formatBodyWidth($template->body_width),
			'body_max_width'                       => (int)$template->body_max_width,
			'body_min_width'                       => (int)$template->body_min_width,
			'body_top'                             => (int)$template->body_top,

			'show_on_pages'                        => $template->show_on_pages,
			'cookie_lifetime'                      => isset($template->cookie_lifetime) ? $template->cookie_lifetime : 366, // this verification is added bacuse of the smarty math function
			'start_timer'                          => (int)$template->start_timer,
			'when_to_show'                         => (int)$template->when_to_show,

			'terms_and_conditions_url'             => $template->terms_and_conditions_url,

			'mandatory_fields'                     => NewsletterProTools::unSerialize($template->mandatory_fields),
		);
	}

	public function formatBodyWidth($width_value)
	{
		$width = trim($width_value);
		if (!empty($width))
			return $width;

		return NewsletterProSubscriptionTpl::DEFAULT_BODY_WIDTH;
	}

	public function getSubscriptionTemplateViewLink($id_template, $id_lang = null)
	{
		if (!isset($id_lang))
			$id_lang = $this->context->language->id;

		return $this->controller->getLink(array(
			'getSubscriptionTemplateView' => 1,
			'idTemplate'                  => (int)$id_template,
			'idLangTemplate'              => (int)$id_lang,
		));
	}

	public function getTemplatesDataGrid()
	{
		$templates = NewsletterProSubscriptionTpl::getTemplatesDataGrid();

		$selected_template = NewsletterProConfigurationShop::get('SUBSCRIPTION_TEMPLATE');

		$default_template = false;
		$active_template_exists = false;

		foreach ($templates as &$template)
		{
			if ($template['name'] == $selected_template)
				$template['selected'] = true;
			else
				$template['selected'] = false;

			if ($template['name'] == 'default')
				$default_template =& $template;

			if ($template['active'])
				$active_template_exists = true;

			// format date
			$template['date_add'] = date($this->context->language->date_format_lite, strtotime($template['date_add']));
		}

		// corret ative templates
		if (!$active_template_exists && $default_template)
		{
			NewsletterProSubscriptionTpl::setActive((int)$default_template['id_newsletter_pro_subscription_tpl']);
			$default_template['active'] = 1;
		}

		return Tools::jsonEncode($templates);
	}

	public function updateTemplatesDataGrid($post)
	{
		try
		{
			$template = new NewsletterProSubscriptionTpl((int)$post['id']);
			if (Validate::isLoadedObject($template))
			{
				$template->active = (int)$post['active'];

				$template->display_gender            = (int)$post['display_gender'];
				$template->display_firstname         = (int)$post['display_firstname'];
				$template->display_lastname          = (int)$post['display_lastname'];
				$template->display_language          = (int)$post['display_language'];
				$template->display_birthday          = (int)$post['display_birthday'];
				$template->display_list_of_interest  = (int)$post['display_list_of_interest'];
				$template->display_subscribe_message = (int)$post['display_subscribe_message'];
				$template->list_of_interest_type     = (int)$post['list_of_interest_type'];

				if ($template->update())
					return 1;
			}
		}
		catch (Exception $e)
		{
			return 0;
		}
		return 0;
	}

	public function deleteTemplatesDataGrid($id)
	{
		try
		{
			$template = new NewsletterProSubscriptionTpl((int)$id);
			if (Validate::isLoadedObject($template))
			{
				// if the template name is default, the template cannot be deleted
				if ($template->name == 'default')
					return 0;
				else if ($template->name == NewsletterProConfigurationShop::get('SUBSCRIPTION_TEMPLATE'))
				{
					// if you delete the current template, the default template will be selected
					if (!NewsletterProConfigurationShop::updateValue('SUBSCRIPTION_TEMPLATE', 'default'))
						return 0;
				}

				if ($template->delete())
					return 1;
			}
		}
		catch (Exception $e)
		{
			return 0;
		}
		return 0;
	}

	public function ajaxSetTemplateById($id, $id_lang = null)
	{
		$response = $this->response();

		if (!isset($id_lang) || !$id_lang)
			$id_lang = (int)$this->context->language->id;

		$template = new NewsletterProSubscriptionTpl($id);
		if (Validate::isLoadedObject($template))
		{

			$template_data = $this->getTemplateFields($template, $id_lang = null);

			NewsletterProConfigurationShop::updateValue('SUBSCRIPTION_TEMPLATE', $template->name);

			$response->set('template', $template_data);
		}
		else
			$response->addError($this->module->l(sprintf('Cannot get the template content. Invalid template id "%s".', $id)));

		return $response->display();
	}

	public function saveDisplayGender($id_template, $bool)
	{
		return $this->changeSetting('display_gender', $id_template, $bool);
	}

	public function saveDisplayFirstName($id_template, $bool)
	{
		return $this->changeSetting('display_firstname', $id_template, $bool);
	}

	public function saveDisplayLastName($id_template, $bool)
	{
		return $this->changeSetting('display_lastname', $id_template, $bool);
	}

	public function saveDisplayLanguage($id_template, $bool)
	{
		return $this->changeSetting('display_language', $id_template, $bool);
	}

	public function saveDisplayBirthday($id_template, $bool)
	{
		return $this->changeSetting('display_birthday', $id_template, $bool);
	}

	public function saveDisplayListOfInterest($id_template, $bool)
	{
		return $this->changeSetting('display_list_of_interest', $id_template, $bool);
	}

	public function saveDisplaySubscribeMessage($id_template, $bool)
	{
		return $this->changeSetting('display_subscribe_message', $id_template, $bool);
	}

	public function saveListOfInterestType($id_template, $bool)
	{
		return $this->changeSetting('list_of_interest_type', $id_template, $bool);
	}

	public function saveAllowMultipleTimeSubscription($id_template, $bool)
	{
		return $this->changeSetting('allow_multiple_time_subscription', $id_template, $bool);
	}

	public function saveActivateTemplate($id_template, $bool)
	{
		return $this->changeSetting('active', $id_template, $bool);
	}

	private function changeSetting($db_field, $id_template, $value)
	{
		$response = $this->response();
		$template = new NewsletterProSubscriptionTpl((int)$id_template);
		if (Validate::isLoadedObject($template))
		{
			try
			{
				$template->{$db_field} = $value;
				if (!$template->update())
					$response->addError($this->module->l('An error occurred when updateing the template settings.'));
			}
			catch (Exception $e)
			{
				$response->addError($e->getMessage());
			}
		}
		else
			$response->addError($this->module->l(sprintf('The template settings cannot be changed. Invalid template id "%s".', $id_template)));
		return $response->display();
	}

	public function ajaxSaveTemplate($id_template, $content, $id_lang = 0)
	{
		$response = $this->response(array(
			'message' => '',
			'view'    => '',
		));

		if (!isset($id_lang) || $id_lang == false)
			$id_lang = (int)$this->context->language->id;

		$template = new NewsletterProSubscriptionTpl((int)$id_template);
		if (Validate::isLoadedObject($template))
		{
			try
			{
				$template->css_style                            = $content['css_style'];
				$template->content                              = $content['tiny'];
				$template->subscribe_message                    = $content['tinySubscribeMessage'];
				$template->email_subscribe_voucher_message      = $content['tinyEmailSubscribeVoucherMessage'];
				$template->email_subscribe_confirmation_message = $content['tinyEmailSubscribeConfirmationMessage'];

				$global_info = $template->getCssGlobalStyleInfo();

				if (@file_put_contents($global_info['dir_path_full'], $content['css_style_global']) === false)
					$response->addError(sprintf($this->module->l('The filename "%s" cannot be written. Please check the CHMOD permissions.'), $global_info['basename']));

				if (!$template->update())
					$response->addError($this->module->l('An error occurred when updateing the template.'));
				else
				{
					$response->set('message', $this->module->l('Template saved successfully.'));
					$response->set('view', $this->getSubscriptionTemplateViewLink($template->id, (int)$id_lang));
				}
			}
			catch(Exception $e)
			{
				$response->addError($e->getMessage());
			}
		}
		else
			$response->addError($this->module->l(sprintf('An error occurred, invalid template id "%s".', $id_template)));

		return $response->display();
	}

	public function ajaxSaveAsTemplate($name, $data, $id_lang = 0)
	{
		$response = $this->response(array(
			'id_template' => 0,
			'name'        => '',
			'message'     => '',
			'view'        => '',
		));

		if (!isset($id_lang) || $id_lang == false)
			$id_lang = (int)$this->context->language->id;

		try
		{
			$content  = $data['content'];
			$settings = $data['settings'];
			$sliders  = $data['sliders'];

			$template = new NewsletterProSubscriptionTpl();
			// the name will be formated by the function add
			$template->name = $name;
			$template->active = 0;

			$template->display_gender            = (int)$settings['display_gender'];
			$template->display_firstname         = (int)$settings['display_firstname'];
			$template->display_lastname          = (int)$settings['display_lastname'];
			$template->display_language          = (int)$settings['display_language'];
			$template->display_birthday          = (int)$settings['display_birthday'];
			$template->display_list_of_interest  = (int)$settings['display_list_of_interest'];
			$template->display_subscribe_message = (int)$settings['display_subscribe_message'];

			$template->show_on_pages             = $settings['show_on_pages'];
			$template->cookie_lifetime           = (float)$settings['cookie_lifetime'];
			$template->start_timer               = (int)$settings['start_timer'];
			$template->when_to_show              = (int)$settings['when_to_show'];

			$template->terms_and_conditions_url  = $settings['terms_and_conditions_url'];

			$template->voucher                   = $settings['voucher'];

			$template->body_width     = $sliders['body_width'];
			$template->body_min_width = (int)$sliders['body_min_width'];
			$template->body_max_width = (int)$sliders['body_max_width'];
			$template->body_top       = (int)$sliders['body_top'];

			$template->content                              = $content['tiny'];
			$template->subscribe_message                    = $content['tinySubscribeMessage'];
			$template->email_subscribe_voucher_message      = $content['tinyEmailSubscribeVoucherMessage'];
			$template->email_subscribe_confirmation_message = $content['tinyEmailSubscribeConfirmationMessage'];
			$template->css_style                            = $content['css_style'];

			$global_info = $template->getCssGlobalStyleInfo();
			if (@file_put_contents($global_info['dir_path_full'], $content['css_style_global']) === false)
				$response->addError(sprintf($this->module->l('The filename "%s" cannot be written. Please check the CHMOD permissions.'), $global_info['basename']));
			else
			{
				if (!$template->add())
					$response->mergeErrors($template->getErrors());
				else
				{
					// the template will have an id and a new name after it's saved
					$this->updateConfiguration('SUBSCRIPTION_TEMPLATE', $template->name);
					$response->set('id_template', $template->id);
					$response->set('name', $template->name);
					$response->set('message', $this->module->l('Template saved successfully.'));
					$response->set('view', $this->getSubscriptionTemplateViewLink($template->id, (int)$id_lang));
				}
			}
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	public function ajaxGetViewLink($id_template, $id_lang)
	{
		$response = $this->response(array(
			'view' => $this->getSubscriptionTemplateViewLink((int)$id_template, (int)$id_lang),
		));
		return $response->display();
	}

	public function ajaxSetSetting($id_template, $db_field, $value)
	{
		return $this->changeSetting($db_field, $id_template, $value);
	}

	public function ajaxSetMandatory($id_template, $field, $checked)
	{		
		$response = $this->response(array(

		));

		try
		{
			$tpl = new NewsletterProSubscriptionTpl($id_template);

			if (!Validate::isLoadedObject($tpl))
				throw new Exception($this->module->l('The subscription template cannot be loaded.'));
			
			$mandatory_fields = NewsletterProTools::unSerialize($tpl->mandatory_fields);

			if ($checked)
			{
				if (!in_array($field, $mandatory_fields))
					$mandatory_fields[] = $field;
			}
			else
			{
				$key = array_search($field, $mandatory_fields);
				if ($key !== false)
					unset($mandatory_fields[$key]);
			}

			$tpl->mandatory_fields = serialize($mandatory_fields);
			
			if (!$tpl->save())
				throw new Exception($this->module->l('The option cannot be saved.'));
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	public function showSubscriptionHelp()
	{
		$generic_template = new NewsletterProSubscriptionTpl();

		$tpl = $this->context->smarty->createTemplate(pqnp_template_path($this->module->dir_location.'views/templates/admin/help_subscription.tpl'));
		$tpl->assign(array(
			'displayInfo'           => self::trimPre($generic_template->getInfoHTML()),
			'displayGender'         => self::trimPre($generic_template->getGenderHTML()),
			'displayFirstName'      => self::trimPre($generic_template->getFirstNameHTML()),
			'displayLastName'       => self::trimPre($generic_template->getLastNameHTML()),
			'displayEmail'          => self::trimPre($generic_template->getEmailHTML()),
			'displayLanguages'      => self::trimPre($generic_template->getLanguagesHTML()),
			'displayBirthday'       => self::trimPre($generic_template->getBirthdayHTML()),
			'displayListOfInterest' => self::trimPre($generic_template->getListOfInterestHTML()),
			'submitButton'          => self::trimPre($generic_template->getSubmitHTML()),
			'close_forever'			=> self::trimPre($generic_template->getCloseForeverHTML()),

			'displayTermsAndConditionsLink'     => $generic_template->getTermsAndConditionsLinkHTML(),
			'displayTermsAndConditionsCheckbox' => $generic_template->getTermsAndConditionsCheckboxHTML(),
			'displayTermsAndConditionsFull'     => $generic_template->getTermsAndConditionsFullHTML(),
		));
		return $tpl->fetch();
	}

	public function showLoadBackup()
	{
		$tpl = $this->context->smarty->createTemplate(pqnp_template_path($this->module->dir_location.'views/templates/admin/subscription_template_create_backup.tpl'));
		return $tpl->fetch();
	}

	public function getSubscriptionTemplateBackup()
	{
		$list = array();
		$date = array();

		if (NewsletterPro::getBackupType() == NewsletterPro::BACKUP_TYPE_XML)
		{
			$index = 1;
			foreach (NewsletterProBackupXml::getList('subscription_template/xml') as $item)
			{
				$item['id'] = $index++;
				$list[$index] = $item;
				$date[$index] = $item['m_date'];
			}
		}
		else
		{
			$index = 1;
			foreach (NewsletterProBackupSql::getList('subscription_template/sql') as $item)
			{
				$item['id'] = $index++;
				$list[$index] = $item;
				$date[$index] = $item['m_date'];
			}
		}

		// srot array by last modification
		array_multisort($date, SORT_DESC, $list);
		return Tools::jsonEncode($list);
	}

	public function ajaxDeleteBackupSubscriptionTemplate($basename)
	{
		$response = $this->response();

		if (NewsletterPro::getBackupType() == NewsletterPro::BACKUP_TYPE_XML)
			$path = NewsletterProBackupXml::path().'/subscription_template/xml/'.$basename;
		else
			$path = NewsletterProBackupSql::path().'/subscription_template/sql/'.$basename;

		if (file_exists($path))
		{
			if (@unlink($path) === false)
				$this->addError($this->module->l('Cannot delete the record. Please check the CHMOD permissions.'));
		}
		else
			$this->addError($this->module->l('The file does not exists anymore.'));

		return $response->display();
	}

	public function ajaxLoadBackupSubscriptionTemplate($basename)
	{
		$response = $this->response(array(
			'msg' => '',
		));

		try
		{
			if (NewsletterPro::getBackupType() == NewsletterPro::BACKUP_TYPE_XML)
			{
				$backup = new NewsletterProBackupXml();
				$backup->load('/subscription_template/xml/'.$basename);
				if (!$backup->execute())
					$response->addError($this->module->l('The restore process has faild.'));
				else
					$response->set('msg', $this->module->l('The backup was restored successfully.'));
			}
			else
			{
				$backup = new NewsletterProBackupSql();
				$backup->load('/subscription_template/sql/'.$basename);
				if (!$backup->execute())
					$response->addError($this->module->l('The restore process has faild.'));
				else
					$response->set('msg', $this->module->l('The backup was restored successfully.'));
			}

		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	public static function trimPre($str)
	{
		return preg_replace('/^([\s\n\t]+)|\1$/m', '', $str);
	}

	public function getViewInFrontLinks()
	{
		$result = array();
		foreach (Language::getLanguages(false) as $lang)
		{
			$result[$lang['id_lang']] = array(
				'name' => $lang['name'],
				'link' => NewsletterProSubscriptionTpl::getFrontLink((int)$lang['id_lang']),
			);
		}

		return $result;
	}

	public function getFilterListOfInterest()
	{
		$result = array();

		foreach (NewsletterProListOfInterest::getList() as $item)
		{
			$result[] = array(
				'title'  => $item['name'],
				'value'  => $item['id_newsletter_pro_list_of_interest'],
				'active' => $item['active'],
			);
		}

		return $result;
	}

	public function getActiveTemplatesVouchers()
	{
		$vouchers = array();

		foreach (NewsletterProSubscriptionTpl::getActiveTemplatesAllShops() as $key => $value)
		{
			if (!NewsletterProTools::isEmpty($value['voucher']))
			{
				$vouchers[$key] = $value;
				$vouchers[$key]['is_valid'] = false;
				$vouchers[$key]['errors'] = array();
				$errors   =& $vouchers[$key]['errors'];

				$id_cart_rule = CartRule::getIdByCode($value['voucher']);

				$template_name = Tools::ucfirst(str_replace('_', ' ', $value['name']));
				$voucher_name  = $value['voucher'];
				$shop          = new Shop($value['id_shop']);
				$shop_name     = '';

				if (Validate::isLoadedObject($shop))
					$shop_name = $shop->name;

				if ($id_cart_rule)
				{
					$cart_rule = new CartRule((int)$id_cart_rule);

					if (!$cart_rule->active)
						$errors[] = sprintf($this->module->l('The voucher "%s" in the template "%s" from the shop "%s" is disabled.'), $voucher_name, $template_name, $shop_name);
					if (!$cart_rule->quantity)
						$errors[] = sprintf($this->module->l('This voucher "%s" has already been used in the template "%s" from the shop "%s".'), $voucher_name, $template_name, $shop_name);
					if (strtotime($cart_rule->date_from) > time())
						$errors[] = sprintf($this->module->l('This voucher "%s" is not valid yet in the template "%s" from the shop "%s".'), $voucher_name, $template_name, $shop_name);
					if (strtotime($cart_rule->date_to) < time())
						$errors[] = sprintf($this->module->l('This voucher "%s" has expired in the template "%s" from the shop "%s".'), $voucher_name, $template_name, $shop_name);
					if ($cart_rule->id_customer > 0)
						$errors[] = sprintf($this->module->l('This voucher "%s" is applied only to a specific customer in the template "%s" from the shop "%s".'), $voucher_name, $template_name, $shop_name);
				}
				else
					$errors[] = sprintf($this->module->l('The voucher code "%s" does no longer exists in the template "%s" from the shop "%s".'), $voucher_name, $template_name, $shop_name);

				if (empty($errors))
					$vouchers[$key]['is_valid'] = true;
			}
		}
		return $vouchers;
	}

	public function ajaxGetActiveTemplatesVouchersErrors()
	{
		$vouchers = $this->getActiveTemplatesVouchers();
		$voucher_errors = array();
		foreach ($vouchers as $voucher)
			if (!$voucher['is_valid'])
				$voucher_errors[] = implode('<br>', $voucher['errors']);

		return Tools::jsonEncode($voucher_errors);
	}

	public function ajaxCreateBackupSubscriptionTemplate($name, $check_duplicate = true)
	{
		$response = $this->response(array(
			'msg' => ''
		));

		try
		{
			$backup_tables = array(
				'newsletter_pro_subscription_tpl',
				'newsletter_pro_subscription_tpl_lang',
				'newsletter_pro_subscription_tpl_shop',
				'newsletter_pro_list_of_interest',
				'newsletter_pro_list_of_interest_lang',
				'newsletter_pro_list_of_interest_shop',
			);

			$required_config = NewsletterProConfigurationShop::getAllShopsConfiguration(array('SUBSCRIPTION_TEMPLATE'));

			$name = trim($name);
			if (empty($name))
				$response->addError($this->module->l('The name cannot be empty.'));
			else if (!NewsletterProTools::isFileName($name))
				$response->addError($this->module->l('Some of the name characters are not allowed.'));

			if (NewsletterPro::getBackupType() == NewsletterPro::BACKUP_TYPE_XML)
			{

				$backup = new NewsletterProBackupXml();
				$backup->create($backup_tables, false);

				$backup->addHeader('configuration_shop', serialize($required_config));

				$path_name = 'subscription_template/xml/'.NewsletterProBackupXml::formatName($name);

				if (NewsletterProBackupXml::pathNameExists($path_name, true) && $check_duplicate)
					$response->addError($this->module->l('The backup name already exists. Try with a different name.'));

				if ($response->success())
				{
					if (!$backup->save($path_name, false))
						$response->addError($this->module->l('An error occurred at the backup creation. Please check the CHMOD permissions.'));
					else
						$response->set('msg', $this->module->l('The backup has made successfully.'));
				}
			}
			else
			{
				$backup = new NewsletterProBackupSql();
				$backup->create($backup_tables);

				$backup->setHeader('configuration_shop', serialize($required_config));

				$path_name = 'subscription_template/sql/'.NewsletterProBackupSql::formatName($name);

				if (NewsletterProBackupSql::pathNameExists($path_name, true) && $check_duplicate)
					$response->addError($this->module->l('The backup name already exists.'));

				if ($response->success())
				{
					if (!$backup->save($path_name, false))
						$response->addError($this->module->l('An error occurred at the backup creation. Please check the CHMOD permissions.'));
					else
						$response->set('msg', $this->module->l('The backup has made successfully.'));
				}
			}
		}
		catch (Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}
}