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

class NewsletterProTplHistory extends ObjectModel
{
	public $token;

	public $template_name;

	public $active;

	public $clicks;

	public $opened;

	public $unsubscribed;

	public $fwd_unsubscribed;

	public $template;

	public static $definition = array(
		'table'     => 'newsletter_pro_tpl_history',
		'primary'   => 'id_newsletter_pro_tpl_history',
		'multilang' => true,
		'fields' => array(
			'token'            => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'template_name'    => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'active'           => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'clicks'           => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'opened'           => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'unsubscribed'     => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'fwd_unsubscribed' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),

			/* Lang fields */
			'template' => array('type' => self::TYPE_HTML, 'validate' => 'isString', 'lang' => true),
		)
	);

	public function __construct($id = null, $id_lang = null)
	{
		return parent::__construct($id, $id_lang);
	}

	public static function newInstance($id = null, $id_lang = null)
	{
		return new self($id, $id_lang);
	}

	public static function newFromTemplate(NewsletterProTemplate $template)
	{
		$html = $template->html(NewsletterProTemplateContent::CONTENT_HTML, true);

		$hisotry = self::newInstance();

		foreach ($html as $id_lang => $content)
			$hisotry->template[$id_lang] = $content;

		$hisotry->active = true;
		$hisotry->token = Tools::encrypt(time().uniqid());
		$hisotry->template_name = $template->name;

		return $hisotry;
	}

	public static function getTemplateName($id)
	{
		return Db::getInstance()->getValue('
			SELECT `template_name`
			FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history`
			WHERE `id_newsletter_pro_tpl_history` = '.(int)$id.'
		');
	}

	public function getSendId()
	{
		if (!((int)$this->id)) {
			return 0;
		}

		return (int)Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_send`
			FROM `'._DB_PREFIX_.'newsletter_pro_send`
			WHERE `id_newsletter_pro_tpl_history` = '.(int)$this->id.'
		');
	}

	public function getTaskId()
	{
		if (!((int)$this->id)) {
			return 0;
		}

		return (int)Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_task`
			FROM `'._DB_PREFIX_.'newsletter_pro_task`
			WHERE `id_newsletter_pro_tpl_history` = '.(int)$this->id.'
		');
	}
}