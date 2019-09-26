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

ini_set('max_execution_time', '259200');

if (!defined('ENT_HTML5'))
	define('ENT_HTML5', 48);
if (!defined('ENT_HTML401'))
	define('ENT_HTML401', 0);

define('_NEWSLETTER_PRO_DIR_', realpath(dirname(__FILE__).'/../'));

define('_NEWSLETTER_PRO_AUTOLOAD_', true);

require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProAutoload.php';

if (_NEWSLETTER_PRO_AUTOLOAD_) {
	NewsletterProAutoload::getInstance()->init();
} else {
	require_once _NEWSLETTER_PRO_DIR_.'/libraries/NewsletterProEmogrifier.php';

	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProCSStoInlineStyle.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProDb.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProCreateXML.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProXMLToSQL.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProBackupXml.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProMySQLDumpXml.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProBackupSql.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProMySQLDump.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProInstall.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProCurl.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProTools.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProLog.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProConfigurationShop.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProEvaluate.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProTask.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProTaskStep.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProSend.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProSendProcess.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProSendConnection.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProSendNewsletter.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProSendStep.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProSendManager.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProFiltersSelection.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProDemoMode.php';

	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProExtendTemplateVars.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProTemplateUser.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProTemplate.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProTemplateContent.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProTemplateString.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProTemplateFile.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProTemplateHistory.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProTemplateDynamicVariables.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProExtendProductVariables.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProProduct.php';

	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProMailChimpApi.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProMailChimp.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProMailChimpController.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProMailChimpFields.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProMailChimpUsers.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProMailChimpOrder.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProMailChimpWebhooks.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProMailChimpUserImport.php';
	
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProBlockNewsletter.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProOurModules.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProEmailExclusion.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProConfig.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProRSS.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProCookie.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProMailInterface.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProMailSwift.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProForward.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProForwardRecipients.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProMail.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProCustomerContext.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProUpgrade.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProEmail.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProSubscribers.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProShutdown.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProSubscribersTemp.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProListOfInterest.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProAjaxResponse.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProSyncNewsletterResponse.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProSubscriptionTpl.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProGenerateCustomers.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProGenerateOrders.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProAttachment.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProSubscribersCustomField.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProSubscribersCustomFieldController.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProTplHistory.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProMailChimpToken.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProMedia.php';

	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProAttachmentException.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProSendNewsletterException.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProSubscribersCustomFieldException.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProTemplateException.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProTemplateUserException.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProTemplateContentException.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProTemplateDynamicVariablesException.php';
	require_once _NEWSLETTER_PRO_DIR_.'/classes/NewsletterProProductException.php';

	require_once _NEWSLETTER_PRO_DIR_.'/controllers/NewsletterProController.php';
	require_once _NEWSLETTER_PRO_DIR_.'/controllers/NewsletterProFrontSubscriptionController.php';
	require_once _NEWSLETTER_PRO_DIR_.'/controllers/NewsletterProAjaxController.php';
	require_once _NEWSLETTER_PRO_DIR_.'/controllers/NewsletterProTemplateController.php';
	require_once _NEWSLETTER_PRO_DIR_.'/controllers/NewsletterProProductSelectionController.php';
}

require_once _NEWSLETTER_PRO_DIR_.'/libraries/phpQuery.php';
require_once _NEWSLETTER_PRO_DIR_.'/classes/helpers.php';