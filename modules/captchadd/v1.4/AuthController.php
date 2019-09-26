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
class AuthController extends AuthControllerCore
{
	public function preProcess()
	{
		include(_PS_MODULE_DIR_.'captchadd/testcaptcha.php');
		parent::preProcess();
	}
}
