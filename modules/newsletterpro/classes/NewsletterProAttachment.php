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

class NewsletterProAttachment extends ObjectModel
{
	public $template_name;

	public $files;

	private $files_array;

	private $dir_mails;
	private $dir_attachments;


	public static $definition = array(
		'table'   => 'newsletter_pro_attachment',
		'primary' => 'id_newsletter_pro_attachment',
		'fields'  => array(
			'template_name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
			'files'         => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
		)
	);

	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->dir_mails = NewsletterPro::getInstance()->tpl_location;
		$this->dir_attachments = $this->dir_mails.'attachments/';

		$this->files_array = NewsletterProTools::unSerialize($this->files);
		
	}

	public static function newInstance($id = null)
	{
		return new self($id);
	}

	public static function getTemplateAttachmentId($template_name)
	{
		$id = (int)Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_attachment`
			FROM `'._DB_PREFIX_.'newsletter_pro_attachment`
			WHERE `template_name` = "'.pSQL($template_name).'"
		');

		return $id;
	}

	public static function newInstanceByTemplateName($template_name)
	{
		$id = self::getTemplateAttachmentId($template_name);
		$instance = new self($id);

		return Validate::isLoadedObject($instance) ? $instance : false;
	}

	public function template($template_name)
	{
		$this->template_name = $template_name;
		return $this;
	}

	public function attach($filename, $new_name = null, $copy = false)
	{
		if (!file_exists($filename))
			throw new NewsletterProAttachmentException(sprintf(NewsletterPro::getInstance()->l('The filename "%s" does not exists.'), $filename));

		if (!NewsletterProTemplate::templateExists($this->template_name))
			throw new NewsletterProAttachmentException(sprintf(NewsletterPro::getInstance()->l('The template "%s" does not exists.'), $this->template_name));

		if (!isset($new_name))
		{
			$basename = pathinfo($filename, PATHINFO_BASENAME);
			$attachment_name = uniqid().'_'.$basename;
		}
		else
			$attachment_name = uniqid().'_'.$new_name;

		$attachment_filename = $this->dir_attachments.$attachment_name;

		if (!$copy)
		{
			if (!move_uploaded_file($filename, $attachment_filename))
				throw new NewsletterProAttachmentException(sprintf(NewsletterPro::getInstance()->l('The file "%s" cannot be attached. Please check the CHMOD permissions.'), $filename));
		}
		else
		{
			if (!copy($filename, $attachment_filename))
				throw new NewsletterProAttachmentException(sprintf(NewsletterPro::getInstance()->l('The file "%s" cannot be attached. Please check the CHMOD permissions.'), $filename));
		}
		
		$this->files_array[] = $attachment_name;
		$this->files = serialize($this->files_array);

		if ($this->save())
			return true;
		else
		{
			if (is_file($attachment_filename))
				@unlink($attachment_filename);
			
			return false;
		}
	}

	public function detach($name)
	{
		$filename = $this->dir_attachments.$name;

		if (file_exists($filename) && is_file($filename))
			$unlink = unlink($filename);

		$key = array_search($name, $this->files_array);
		if (is_int($key))
		{
			unset($this->files_array[$key]);
			$this->files_array = array_values($this->files_array);
			$this->files = serialize($this->files_array);
			$this->save();
		}

		return $unlink;
	}

	public function hasFile($name)
	{
		return in_array($name, $this->files_array);
	}

	public function files()
	{
		return $this->files_array;
	}

	public function filesPath()
	{
		$filespath = array();

		foreach ($this->files() as $file) 
		{
			$path = $this->dir_attachments.$file;
			if (file_exists($path) && is_readable($path) && is_file($path))
				$filespath[] = $path;
		}

		return $filespath;
	}

	public function filesPathFilename()
	{
		$fpn = array();
		foreach ($this->filesPath() as $fp) 
		{

			$fpn[] = array(
				'path' => $fp,
				'name' => self::getRealName($fp)
			);
		}

		return $fpn;
	}

	public static function getRealName($file)
	{
		$name = pathinfo($file, PATHINFO_BASENAME);
		$real_name = $name;
		$pos = strpos($name, '_');
		if ($pos !== false)
			$real_name = Tools::substr($name, $pos + 1);

		return $real_name;
	}

	public static function getTemplatesName()
	{
		$results = Db::getInstance()->executeS('
			SELECT `template_name` FROM `'._DB_PREFIX_.'newsletter_pro_attachment`
		');

		$templates_name = array();

		foreach ($results as $row) 
			$templates_name[] = $row['template_name'];

		return $templates_name;
	}

	public static function ajaxGetAttachments($template_name)
	{
		$results = array();

		$attachment = self::newInstanceByTemplateName($template_name);
		if ($attachment)
		{
			foreach ($attachment->files() as $name) 
			{
				$results[] = array(
					'id_newsletter_pro_attachment' => $attachment->id,
					'filename' => $name
				);
			}
		}

		return Tools::jsonEncode($results);
	}

	public static function ajaxDeleteAttachment($id, $filename)
	{
		$response = NewsletterProAjaxResponse::newInstance();

		try
		{
			$attachment = self::newInstance($id);

			if (!Validate::isLoadedObject($attachment))
				throw new NewsletterProAttachmentException(sprintf(NewsletterPro::getInstance()->l('The attachment with id "%s" does not exists.'), $id));

			if (!$attachment->hasFile($filename))
				throw new NewsletterProAttachmentException(sprintf(NewsletterPro::getInstance()->l('The attachment filename "%s" does not exists.'), $filename));

			if (!$attachment->detach($filename))
				throw new NewsletterProAttachmentException(sprintf(NewsletterPro::getInstance()->l('An error occurred, please check the CHMOD permissions.')));

			$files = $attachment->files();

			if (empty($files))
				$attachment->delete();

		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();
	}

	public static function ajaxTemplateAttachFile($file, $template_name)
	{
		$response = NewsletterProAjaxResponse::newInstance();

		try
		{
			$name = $file['name'];

			if (!preg_match('/^((?!.*php$|.*js$).*)$/i', $name))
				throw new NewsletterProAttachmentException(sprintf(NewsletterPro::getInstance()->l('The file extension is not allowed.')));

			$message = NewsletterPro::getInstance()->verifyFileErros($file);
			if ($message === true)
			{
				$attachment = self::newInstanceByTemplateName($template_name);
				if (!$attachment)
				{
					$attachment = self::newInstance();
					$attachment->template($template_name);
				}

				$attachment->attach($file['tmp_name'], $name);
			}
			else
				$response->addError($message);
		}
		catch(Exception $e)
		{
			$response->addError($e->getMessage());
		}

		return $response->display();	
	}

	public static function setAttachmentToMessage($template_name, &$message)
	{
		// add attachments
		$attachment = self::newInstanceByTemplateName($template_name);

		if ($attachment)
		{
			$files = $attachment->filesPathFilename();

			if (!empty($files))
			{
				foreach ($files as $file)
				{
					$attach = NewsletterPro_Swift_Attachment::fromPath($file['path'])->setFilename($file['name']);
					$message->attach($attach);
				}
			}
		}
	}
}