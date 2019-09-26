<?php
/**
 * 2008-2014 Librasoft
 *
 *  For support feel free to contact us on our website at http://www.librasoft.fr/
 *
 *  @author    Librasoft <contact@librasoft.fr>
 *  @copyright 2008-2014 Librasoft
 *  @version   1.0
 *  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
 */

if (!defined('_PS_VERSION_'))
	exit;
if (!isset($result))
	$result = false;
if ((Tools::isSubmit('submitAccount') && Configuration::get('CAPTCHADD_BOOLCAPTCHA') == 1)
	|| (Tools::isSubmit('submitMessage') && Configuration::get('CAPTCHADD_BOOLCAPTCHA2') == 1)
	|| (Configuration::get('CAPTCHADD_BOOLCAPTCHA3') == 1 && $result))
{
	include_once(_PS_MODULE_DIR_.'captchadd/securimage/securimage.php');
	$securimage = new Securimage();
	if ($securimage->check(Tools::getValue('captcha_code')) == false)
	{
		if (!$result)	/* Cas normal (Contact ou Auth) */
			$this->errors[] = Tools::displayError('Captcha was wrong. Please try again!');
		else	/* Cas du Product Comments */
			$errors[] = Tools::displayError('Captcha was wrong. Please try again!');
	}
}