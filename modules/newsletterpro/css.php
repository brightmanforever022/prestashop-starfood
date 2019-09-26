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

header('Access-Control-Allow-Origin: *');
$root = dirname(dirname(getcwd()));

require_once($root.'/config/config.inc.php');
require_once($root.'/init.php');

$newsletterpro = Module::getInstanceByName('newsletterpro');
header('Content-Type: text/css');
if (Validate::isLoadedObject($newsletterpro))
{
	@ob_clean();
	@ob_end_clean();

	$context = Context::getContext();

	if (Tools::isSubmit('getSubscriptionCSS'))
	{
		$id_template = Tools::getValue('idTemplate');

		if (Tools::isSubmit('idShop'))
		{
			$id_shop = Tools::getValue('idShop');
			$shop = Shop::getShop($id_shop);

			if ($shop)
				$context->shop = new Shop((int)$shop['id_shop']);
		}

		$template = new NewsletterProSubscriptionTpl((int)$id_template);
		if (Validate::isLoadedObject($template))
			die((string)$template->css_style);
	}
	else if (Tools::isSubmit('getNewsletterTemplateCSS') && Tools::isSubmit('name'))
	{
		$template_name = Tools::getValue('name');
		$id_lang = (int)Tools::getValue('id_lang');

		try
		{
			$template = NewsletterProTemplate::newFile($template_name)->load($id_lang);
			die($template->css());
		}
		catch(Exception $e)
		{
			pqnp_log()->write($e->__toString(), NewsletterProLog::ERROR_FILE);
		}
	}

}