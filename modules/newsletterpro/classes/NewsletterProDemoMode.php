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

class NewsletterProDemoMode
{
	/**
	 * DISABLED
	 */
	public static function saveProductTemplate($template)
	{
		$data = pqnp_demo_mode('demo_product_default_templates');

		if (in_array($template, $data))
		{
			$data = array();
			$data['message'] = NewsletterPro::getInstance()->l('You cannot override the default templates in this demo. You must create a new template by pressing the "Save As" button, after that you can save it.');
			$data['type'] = false;
			return Tools::jsonEncode($data);
		}

		return false;
	}

	public static function deleteProductTemplate($path)
	{
		$response = NewsletterProAjaxResponse::newInstance();
		$template = pathinfo($path, PATHINFO_BASENAME);

		$data = pqnp_demo_mode('demo_product_default_templates');

		if (in_array($template, $data))
		{
			$response->addError(NewsletterPro::getInstance()->l('This is a demo, you cannot delete the default templats.'));
			return $response->display();
		}
	
		return false;
	}

	public static function deleteTemplate($name)
	{
		$response = NewsletterProAjaxResponse::newInstance();
		$template = pathinfo($name, PATHINFO_FILENAME);

		$data = pqnp_demo_mode('demo_newsletter_default_templates');

		if (in_array($template, $data))
		{
			$response->addError(NewsletterPro::getInstance()->l('This is a demo, you cannot delete the default templats.'));
			return $response->display();
		}

		return false;
	}

	public static function deleteSMTP($name)
	{
		$name = Tools::strtolower($name);
		$name_demo = Tools::strtolower(pqnp_demo_mode('demo_freeze_smtp_name'));

		if ($name == $name_demo)
		{
			$response = NewsletterProAjaxResponse::newInstance(array(
				'demo_mode' => true
			));

			$response->addError(NewsletterPro::getInstance()->l('This is a demo, you cannot delete this SMTP connection.'));
			return $response->display();
		}

		return false;
	}
}