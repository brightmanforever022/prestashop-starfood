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
class ContactController extends ContactControllerCore
{
	/*
    * module: captchadd
    * date: 2017-06-09 10:56:16
    * version: 1.0
    */
    public function postProcess()
	{
		include(_PS_MODULE_DIR_.'captchadd/testcaptcha.php');
		if (count($this->errors) <> 0)
			array_unique($this->errors);
		else
			parent::postProcess();
	}
}
