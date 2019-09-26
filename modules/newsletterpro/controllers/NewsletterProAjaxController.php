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

class NewsletterProAjaxController
{
	public $context;

	public $controller;

	public $module;

	public $token;

	public function __construct()
	{
		$this->context = Context::getContext();
		$this->controller =& $this->context->controller;
		$this->module = NewsletterPro::getInstance();

		// the token is only for the admin section
		if (isset($this->context->employee))
			$this->token = Tools::getAdminTokenLite('AdminNewsletterPro');

		@ini_set('max_execution_time', '2880');
		@ob_clean();
		@ob_end_clean();
	}

	public static function isXHR()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
	}

	public static function disableForceCompile($smarty)
	{
		$smarty->force_compile = false;
		$smarty->compile_check = false;
	}

	public static function enableForceCompile($smarty)
	{
		$smarty->force_compile = true;
		$smarty->compile_check = true;
	}

	public function process($action)
	{
		if (Tools::getValue('token') != $this->token)
			$this->display('Invalid Token!');

		switch ($action)
		{
			case 'getOurModules':
				$this->display($this->module->getOurModules());
				break;
			case 'getCustomers':
				$this->display($this->module->getCustomers());
				break;
			case 'updateCustomer':
				$this->display($this->module->updateCustomer((int)Tools::getValue('id')));
				break;
			case 'deleteCustomer':
				$this->display($this->module->deleteCustomer((int)Tools::getValue('id')));
				break;

			case 'deleteForwardToEmail':
				$this->display($this->module->deleteForwardToEmail(Tools::getValue('email')));
				break;

			case 'deleteForwardFromEmail':
				$this->display($this->module->deleteForwardFromEmail(Tools::getValue('from')));
				break;

			case 'clearForwarders':
				$this->display($this->module->clearForwarders());
				break;

			case 'searchForwarder':
				$this->display($this->module->searchForwarder(Tools::getValue('value')));
				break;

			case 'searchCustomer':
				$this->display($this->module->searchCustomer(Tools::getValue('value')));
				break;

			case 'filterCustomer':
				$this->display($this->module->filterCustomer($_POST), true);
				break;
			case 'getVisitors':
				$this->display($this->module->getVisitors());
				break;
			case 'updateVisitor':
				$this->display($this->module->updateVisitor((int)Tools::getValue('id')));
				break;
			case 'deleteVisitor':
				$this->display($this->module->deleteVisitor((int)Tools::getValue('id')));
				break;
			case 'searchVisitor':
				$this->display($this->module->searchVisitor(Tools::getValue('value')));
				break;
			case 'filterVisitor':
				$this->display($this->module->filterVisitor($_POST));
				break;
			case 'getVisitorsNP':
				$this->display($this->module->getVisitorsNP(), true);
				break;
			case 'updateVisitorNP':
				$this->display($this->module->updateVisitorNP((int)Tools::getValue('id')));
				break;
			case 'deleteVisitorNP':
				$this->display($this->module->deleteVisitorNP((int)Tools::getValue('id')));
				break;
			case 'searchVisitorNP':
				$conditions = Tools::getValue('conditions');
				$this->display($this->module->searchVisitorNP(Tools::getValue('value'), $conditions), true);
				break;
			case 'filterVisitorNP':
				$this->display($this->module->filterVisitorNP($_POST));
				break;
			case 'getAdded':
				$this->display($this->module->getAdded());
				break;

			case 'deleteProductTemplate':
				$path = Tools::getValue('path');
				$this->display($this->module->deleteProductTemplate($path));
				break;

			case 'updateAdded':
				$this->display($this->module->updateAdded((int)Tools::getValue('id')));
				break;
			case 'deleteAdded':
				$this->display($this->module->deleteAdded((int)Tools::getValue('id')));
				break;
			case 'searchAdded':
				$this->display($this->module->searchAdded(Tools::getValue('value')));
				break;
			case 'filterAdded':
				$this->display($this->module->filterAdded($_POST));
				break;
			case 'createAdded':
				$this->display($this->module->createAdded($_POST));
				break;
			case 'emptyAddedEmails':
				$this->display($this->module->emptyAddedEmails());
				break;

			case 'getProductTemplates':
				$this->display($this->module->getProductTemplates());
				break;

			case 'deleteImage':
				$this->display($this->module->deleteImage($_POST));
				break;
			case 'updateModule':
				$this->display($this->module->updateModule());
				break;

			case 'getHistoryExclusion':
				$this->display($this->module->getHistoryExclusion());
				break;
			case 'getExclusionList':
			    $this->display($this->module->getExclusionList());
			    break;

			case 'addFilterSelection':
				$name = Tools::getValue('name');
				$filters = Tools::getValue('filters');
				$this->display($this->module->addFilterSelection($name, $filters));
				break;

			case 'deleteFilterSelection':
				$id = Tools::getValue('id');
				$this->display($this->module->deleteFilterSelection($id));
				break;

			case 'startSendNewslettersWithLog':
				$emails = Tools::getValue('emails');
				$limit = Tools::isSubmit('limit') ? (int)Tools::getValue('limit') : 100;
				$this->display($this->module->startSendNewslettersWithLog($emails, $limit));
				break;

			case 'openLogFIle':
				$filename = Tools::getValue('filename');
				$this->display($this->module->openLogFIle($filename));
				break;

			case 'jsUpdateConfiguration':
				$name = Tools::getValue('name');
				$value = Tools::getValue('value');
				$this->display($this->module->jsUpdateConfiguration($name, $value));
				break;

			case 'getExportOptions':
				$value = Tools::getValue('value');
				$this->display($this->module->getExportOptions($value));
				break;

			case 'getFilterSelectionById':
				$id = (int)Tools::getValue('id');
				$this->display($this->module->getFilterSelectionById($id));
				break;

			case 'ajaxGetAttachments':
				$template_name = Tools::getValue('template_name');
				$this->display(NewsletterProAttachment::ajaxGetAttachments($template_name));
				break;

			case 'ajaxDeleteAttachment':
				$id = Tools::getValue('id');
				$filename = Tools::getValue('filename');
				$this->display(NewsletterProAttachment::ajaxDeleteAttachment($id, $filename));
				break;

			case 'ajaxTemplateAttachFile':
				$template_name = Tools::getValue('template_name');
				$this->display(NewsletterProAttachment::ajaxTemplateAttachFile($_FILES['template_attachment'], $template_name));
				break;

			case 'ajaxGetConnections':
				$this->display(NewsletterProSendConnection::ajaxGetConnections());
				break;

			case 'ajaxAddConnection':
				$id_smtp = (int)Tools::getValue('id_smtp');
				$this->display(NewsletterProSendConnection::ajaxAddConnection($id_smtp));
				break;

			case 'ajaxDeleteConnection':
				$id = (int)Tools::getValue('id');
				$this->display(NewsletterProSendConnection::ajaxDeleteConnection($id));
				break;

			case 'updateTopShortcuts':
				$name = Tools::getValue('name');
				$value = (int)Tools::getValue('value');
				$this->display($this->module->updateTopShortcuts($name, $value));
				break;

			case 'addHistoryEmailsToExclusion':
				$data = Tools::getValue('data');
				$remaining_email = Tools::getValue('remainingEmails');
				$sent_email = Tools::getValue('sentEmails');
				$this->display($this->module->addHistoryEmailsToExclusion($data, (int)$remaining_email, (int)$sent_email));
				break;

			case 'getTemplateContent':
				$data = Tools::getValue('data');
				$header = ( Tools::isSubmit('header') ? (bool)Tools::getValue('header') : true );
				$readcontent = ( Tools::isSubmit('readcontent') ? (bool)Tools::getValue('readcontent') : true );
				$this->display($this->module->getTemplateContent($data, $header, $readcontent));
				break;

			case 'getProductTemplateContent':
				$data = Tools::getValue('data');
				$readcontent = ( Tools::isSubmit('readcontent') ? (bool)Tools::getValue('readcontent') : true );
				$this->display($this->module->getProductTemplateContent($data, $readcontent));
				break;

			case 'saveProductNumberPerRow':
				$this->display($this->module->saveProductNumberPerRow((int)Tools::getValue('number')));
				break;

			case 'changeProductImageSize':
				$this->display($this->module->changeProductImageSize(Tools::getValue('value')));
				break;

			case 'changeProductCurrency':
				$this->display($this->module->changeProductCurrency((int)Tools::getValue('value')));
				break;

			case 'changeProductLanguage':
				$this->display($this->module->changeProductLanguage((int)Tools::getValue('value')));
				break;
			case 'getProductsById':
				$this->display($this->module->getProductsById(Tools::getValue('ids')));
				break;
			case 'getImagesOfProducts':
				$this->display($this->module->getImagesOfProducts(Tools::getValue('ids'), (string)Tools::getValue('image_type')));
				break;
			case 'getImageOfProduct':
				$this->display($this->module->getImageOfProduct(Tools::getValue('id'), (string)Tools::getValue('image_type')));
				break;
			case 'continueTaskAjax':
				$this->display($this->module->continueTaskAjax((int)Tools::getValue('id')));
				break;
			case 'pauseTask':
				$this->display($this->module->pauseTask((int)Tools::getValue('id')));
				break;
			case 'getCategoryTree':
				$this->display($this->module->getCategoryTree());
				break;

			case 'syncNewsletters':
				$id = ((int)Tools::getValue('id') ? (int)Tools::getValue('id') : null);
				$limit = (int)Tools::getValue('limit');
				$get_last_id = (bool)Tools::getValue('getLastId');
				$this->display($this->module->syncNewsletters($id, $limit, $get_last_id));
				break;

			case 'startSendNewsletters':
				$trigger = (int)Tools::getValue('trigger');
				$this->display($this->module->startSendNewsletters($trigger));
				break;

			case 'connectionAvailable':
				$this->display($this->module->connectionAvailable());
				break;

			case 'stopSendNewsletters':
				$this->display($this->module->stopSendNewsletters());
				break;

			case 'pauseSendNewsletters':
				$this->display($this->module->pauseSendNewsletters());
				break;

			case 'continueSendNewsletters':
				$trigger = (int)Tools::getValue('trigger');
				$this->display($this->module->continueSendNewsletters($trigger));
				break;

			case 'checkIfCampaignIsRunning':
				$this->display($this->module->checkIfCampaignIsRunning());
				break;
			case 'showNewsletterHelp':
				$this->display($this->module->showNewsletterHelp());
				break;
			case 'showProductHelp':
				$this->display($this->module->showProductHelp());
				break;
			case 'forwardingFeatureActive':
				$this->display($this->module->forwardingFeatureActive(Tools::getValue('value')));
				break;
			case 'chimpSyncUnsubscribed':
				$this->display($this->module->chimpSyncUnsubscribed(Tools::getValue('value')));
				break;
			case 'sendEmbededImagesActive':
				$this->display($this->module->sendEmbededImagesActive(Tools::getValue('value')));
				break;
			case 'subscribeByCategory':
				$this->display($this->module->subscribeByCategory(Tools::getValue('value')));
				break;
			case 'subscribeByCListOfInterest':
				$this->display($this->module->subscribeByCListOfInterest(Tools::getValue('value')));
				break;
			case 'displayCustomerAccountSettings':
				$this->display($this->module->displayCustomerAccountSettings(Tools::getValue('value')));
				break;
			case 'sendNewsletterOnSubscribe':
				$this->display($this->module->sendNewsletterOnSubscribe(Tools::getValue('value')));
				break;
			case 'getStatistics':
				$this->display($this->module->getStatistics());
				break;
			case 'clearStatistics':
				$this->display($this->module->clearStatistics());
				break;
			case 'getImages':
				$this->display($this->module->getImages());
				break;
			case 'uploadImage':
				$file = isset($_FILES['upload_image']) ? $_FILES['upload_image'] : array();
				$width = Tools::getValue('width');
				$this->display($this->module->uploadImage($file, $width));
				break;
			case 'getFilterByPurchaseContent':
				$this->display($this->module->getFilterByPurchaseContent());
				break;
			case 'searchByPurchase':
				$query = Tools::getValue('query');
				$this->display($this->module->searchByPurchase($query));
				break;
			case 'getFilterByBirthdayContent':
				$fbb_class = Tools::getValue('fbb_class');
				$this->display($this->module->getFilterByBirthdayContent($fbb_class));
				break;
			case 'getRangeSelectionContent':
				$this->display($this->module->getRangeSelectionContent());
				break;
			// ------------------------------------------------------------------------
			case 'viewNewsletterTemplate':
				$email = Configuration::get('PS_SHOP_EMAIL');
				$this->display($this->module->getNewsletterContent($email));
				break;

			case 'getMaxTotalSpent':
				$this->display($this->module->getMaxTotalSpent());
				break;

			case 'saveProductTemplate':
				$name = Tools::getValue('saveProductTemplate');
				$nb = Tools::getValue('numberPerRow');
				$this->display($this->module->saveProductTemplate($name, $nb));
				break;

			case 'viewProductTemplate':
				$this->display($this->module->getProductContent(true));
				break;

			case 'getProductContent':
				$this->display($this->module->getProductContent());
				break;

			case 'clearExclusionEmails':
				$this->display($this->module->clearExclusionEmails());
				break;

			case 'getProducts':
				$id_product = (int)Tools::getValue('getProducts');
				$this->display($this->module->getProducts($id_product), true);
				break;

			case 'prepareEmails':
				$this->display($this->module->prepareEmails());
				break;

			case 'isSendNewsletterInProgress':
				$this->display($this->module->isSendNewsletterInProgress());
				break;

			case 'addEmail':
				$email = trim(Tools::getValue('addEmail'));
				$this->display($this->module->addEmail($email));
				break;

			case 'leftMenuActive':
				$bool = (int)Tools::getValue('leftMenuActive');
				$this->display($this->module->leftMenuActive($bool));
				break;

			case 'viewActiveOnly':
				$bool = (int)Tools::getValue('viewActiveOnly');
				$this->display($this->module->viewActiveOnly($bool));
				break;

			case 'convertCssToInlineStyle':
				$bool = (int)Tools::getValue('convertCssToInlineStyle');
				$this->display($this->module->convertCssToInlineStyle($bool));
				break;

			case 'runMultimpleTasks':
				$bool = (int)Tools::getValue('runMultimpleTasks');
				$this->display($this->module->runMultimpleTasks($bool));
				break;

			case 'displayOnliActiveProducts':
				$bool = (int)Tools::getValue('displayOnliActiveProducts');
				$this->display($this->module->displayOnliActiveProducts($bool));
				break;

			case 'productFriendlyURL':
				$bool = (int)Tools::getValue('productFriendlyURL');
				$this->display($this->module->productFriendlyURL($bool));
				break;

			case 'debugMode':
				$bool = (int)Tools::getValue('debugMode');
				$this->display($this->module->debugMode($bool));
				break;

			case 'useCache':
				$bool = (int)Tools::getValue('value');
				$this->display($this->module->useCache($bool));
				break;

			case 'subscriptionSecureSubscribe':
				$bool = (int)Tools::getValue('subscriptionSecureSubscribe');
				$this->display($this->module->subscriptionSecureSubscribe($bool));
				break;

			case 'clearSubscribersTemp':
				$this->display($this->module->clearSubscribersTemp());
				break;

			case 'clearLogFiles':
				$this->display($this->module->clearLogFiles());
				break;

			case 'clearModuleCache':
				$this->display($this->module->clearModuleCache());
				break;

			case 'importEmailsFromBlockNewsletter':
				$this->display($this->module->importEmailsFromBlockNewsletter());
				break;

			case 'newsletterproSubscriptionActive':
				$bool = (int)Tools::getValue('newsletterproSubscriptionActive');
				$hooks = Tools::getValue('hooks');

				if (!is_array($hooks))
					$hooks = array();

				$this->display($this->module->newsletterproSubscriptionActive($bool, $hooks));
				break;

			case 'changeNewsletterTemplate':
				$bool = trim(Tools::getValue('changeNewsletterTemplate'));
				$this->display($this->module->changeNewsletterTemplate($bool));
				break;

			case 'changeProductTemplate':
				$bool = trim(Tools::getValue('changeProductTemplate'));
				$this->display($this->module->changeProductTemplate($bool));
				break;

			case 'changeProductImageSize':
				$bool = trim(Tools::getValue('changeProductImageSize'));
				$this->display($this->module->changeProductImageSize($bool));
				break;

			case 'changeProductCurrency':
				$currency = trim(Tools::getValue('changeProductCurrency'));
				$this->display($this->module->changeProductCurrency($currency));
				break;

			case 'changeProductLanguage':
				$lang = (int)Tools::getValue('changeProductLanguage');
				$this->display($this->module->changeProductLanguage($lang));
				break;

			case 'saveAsProductTemplate':
				$content = Tools::getValue('content');
				$npr = Tools::getValue('numberPerRow');
				$save = trim(Tools::getValue('saveAsProductTemplate'));
				$this->display($this->module->saveAsProductTemplate($save, $content, $npr));
				break;

			case 'getHistory':
				$id_history = (int)Tools::getValue('getHistory');
				$this->display($this->module->getHistory($id_history));
				break;

			case 'sleepNewsletter':
				$seconds = (int)Tools::getValue('sleepNewsletter');
				$this->display($this->module->sleepNewsletter($seconds));
				break;

			case 'search_emails':
				$this->display($this->module->searchEmails());
				break;

			case 'saveProductNumberPerRow':
				$npr = (int)Tools::getValue('saveProductNumberPerRow');
				$this->display($this->module->saveProductNumberPerRow($npr));
				break;

			case 'sendTestEmail':
				$email         = trim(Tools::getValue('sendTestEmail'));
				$smtp_id       = ( Tools::isSubmit('smtpId') == true ? Tools::getValue('smtpId') : null );
				$template_name = ( Tools::isSubmit('templateName') == true ? Tools::getValue('templateName') : null );
				$send_method   = ( Tools::isSubmit('sendMethod') == true ? Tools::getValue('sendMethod') : null );
				$id_lang = (Tools::isSubmit('idLang') == true ? Tools::getValue('idLang') : null);
				$this->display(NewsletterProSendManager::getInstance()->sendTestNewsletter($email, $template_name, $smtp_id, $send_method, $id_lang));
				break;

			case 'clearSendHistoryDetails':
				$this->display($this->module->clearSendHistoryDetails());
				break;

			case 'clearTaskHistoryDetails':
				$this->display($this->module->clearTaskHistoryDetails());
				break;

			case 'sendMailTest':
				$test = trim(Tools::getValue('sendMailTest'));
				$id_smtp = (Tools::isSubmit('id_smtp') && (int)Tools::getValue('id_smtp') ? (int)Tools::getValue('id_smtp') : null);
				$this->display(NewsletterProSendManager::getInstance()->sendMailTest($test, $id_smtp));
				break;

			case 'selectAllCustomers':
				$this->display($this->module->selectAllCustomers());
				break;

			case 'displayProductImage':
				$image = trim(Tools::getValue('displayProductImage'));
				$this->display($this->module->displayProductImage($image));
				break;

			case 'searchProducts':
				$products = trim(Tools::getValue('searchProducts'));
				$this->display($this->module->searchProducts($products));
				break;

			case 'clearHistory':
				$this->display($this->module->clearHistory());
				break;

			case 'saveSMTP':
				$this->display($this->module->saveSMTP($_POST));
				break;

			case 'addSMTP':
				$this->display($this->module->addSMTP($_POST));
				break;

			case 'getCountries':
				$this->display($this->module->getCountries(), true);
				break;

			case 'searchCountries':
				$value = Tools::getValue('value');
				$this->display($this->module->searchCountries($value), true);
				break;

			case 'smtpActive':
				$bool = Tools::getValue('smtpActive');
				$this->display($this->module->smtpActive($bool));
				break;

			case 'updateGAnalyticsID':
				$id = trim(Tools::getValue('updateGAnalyticsID'));
				$this->display($this->module->updateGAnalyticsID($id));
				break;

			case 'activeGAnalytics':
				$bool = Tools::getValue('activeGAnalytics');
				$this->display($this->module->activeGAnalytics($bool));
				break;

			case 'universalAnaliytics':
				$bool = Tools::getValue('universalAnaliytics');
				$this->display($this->module->universalAnaliytics($bool));
				break;

			case 'activeCampaign':
				$bool = Tools::getValue('activeCampaign');
				$this->display($this->module->activeCampaign($bool));
				break;

			case 'makeDefaultParameteres':
				$this->display($this->module->makeDefaultParameteres());
				break;

			case 'uploadCSV':
				if (isset($_FILES['upload_csv']))
					$this->display($this->module->uploadCSV($_FILES['upload_csv']));
				break;

			case 'deleteBouncedEmails':
				$bounced_emails = isset($_FILES['bounced_emails']) ? $_FILES['bounced_emails'] : null;
				$this->display($this->module->deleteBouncedEmails($bounced_emails));
				break;

			case 'addCsvEmailsToExclusion':
				$csv = isset($_FILES['exclusion_emails_emails']) ? $_FILES['exclusion_emails_emails'] : null;
				$this->display($this->module->addCsvEmailsToExclusion($csv));
				break;

			case 'deleteCSVByName':
				$name = Tools::getValue('deleteCSVByName');
				$this->display($this->module->deleteCSVByName($name));
				break;

			case 'loadCSV':
				$csv = Tools::getValue('loadCSV');
				$delimiter = Tools::getValue('delimiter');
				$line = Tools::getValue('line');
				$this->display($this->module->loadCSV($csv, $delimiter, $line));
				break;

			case 'importCSV':
				$csv = Tools::getValue('importCSV');
				$delimiter = Tools::getValue('delimiter');
				$fields = Tools::getValue('fields');
				$line = Tools::getValue('line');
				$filter_name = Tools::getValue('filter_name');
				$this->display($this->module->importCSV($csv, $delimiter, $fields, $line, $filter_name));
				break;

			case 'saveCampaign':
				$this->display($this->module->saveCampaign($_POST));
				break;

			case 'getTaskTemplate':
				$this->display($this->module->getTaskTemplate());
				break;

			case 'changeSMTP':
				$id = (int)Tools::getValue('changeSMTP');
				$this->display($this->module->changeSMTP($id));
				break;

			case 'deleteSMTP':
				$tpl = (int)Tools::getValue('deleteSMTP');
				$this->display($this->module->deleteSMTP($tpl));
				break;

			case 'updateTask':
				$task = (int)Tools::getValue('updateTask');
				$this->display($this->module->updateTask($task));
				break;

			case 'deleteTask':
				$id_task = (int)Tools::getValue('deleteTask');
				$this->display($this->module->deleteTask($id_task));
				break;

			case 'deleteSendHistory':
				$id_history = (int)Tools::getValue('deleteSendHistory');
				$this->display($this->module->deleteSendHistory($id_history));
				break;

			case 'getTasks':
				$this->display($this->module->getTasks());
				break;

			case 'addTask':
				$task = Tools::getValue('addTask');
				$this->display($this->module->addTask($task));
				break;

			case 'sendTaskAjax':
				// don't need $this->display()
				$task = (int)Tools::getValue('sendTaskAjax');
				$this->module->sendTaskAjax($task);
				break;

			case 'getTasksInProgress':
				$progress_ids = Tools::getValue('progressIds');
				$this->display($this->module->getTasksInProgress($progress_ids));
				break;

			case 'getSendHistory':
				$this->display($this->module->getSendHistory());
				break;

			case 'getForwardList':
				$this->display($this->module->getForwardList());
				break;

			case 'getTasksHistory':
				$this->display($this->module->getTasksHistory());
				break;

			case 'getUnsubscribedDetails':
				$id_newsletter = (int)Tools::getValue('id_newsletter');
				$this->display($this->module->getUnsubscribedDetails($id_newsletter));
				break;

			case 'getFwdUnsubscribedDetails':
				$id_newsletter = (int)Tools::getValue('id_newsletter');
				$this->display($this->module->getFwdUnsubscribedDetails($id_newsletter));
				break;

			case 'getTaskUnsubscribedDetails':
				$id_newsletter = (int)Tools::getValue('id_newsletter');
				$this->display($this->module->getTaskUnsubscribedDetails($id_newsletter));
				break;

			case 'getTaskFwdUnsubscribedDetails':
				$id_newsletter = (int)Tools::getValue('id_newsletter');
				$this->display($this->module->getTaskFwdUnsubscribedDetails($id_newsletter));
				break;

			case 'getForwarderDetails':
				$email = Tools::getValue('email');
				$this->display($this->module->getForwarderDetails($email));
				break;

			case 'getTasksHistoryDetail':
				$detail = (int)Tools::getValue('getTasksHistoryDetail');
				$this->display($this->module->getTasksHistoryDetail($detail));
				break;

			case 'getSendHistoryDetail':
				$detail = (int)Tools::getValue('getSendHistoryDetail');
				$this->display($this->module->getSendHistoryDetail($detail));
				break;

			case 'clearTaskHistory':
				$this->display($this->module->clearTaskHistory());
				break;

			case 'clearSendHistory':
				$this->display($this->module->clearSendHistory());
				break;

			case 'getAllSMTPJson':
				$this->display($this->module->getAllSMTPJson());
				break;

			case 'renderTemplateHistory':
				$id_history = (int)Tools::getValue('renderTemplateHistory');
				$this->display($this->module->renderTemplateHistory($id_history));
				break;

			case 'ajaxCreateBackup':
				$name = Tools::getValue('name');
				$check_duplicate = Tools::isSubmit('check_duplicate') ? (bool)Tools::getValue('check_duplicate') : true;
				$this->display($this->module->ajaxCreateBackup($name, $check_duplicate));
				break;

			case 'resendSendHistory':
				$left_list = Tools::getValue('resendLeft');
				$right_list = Tools::getValue('resendUndelivered');
				$id = (int)Tools::getValue('id');
				$this->display($this->module->resendSendHistory($id, $left_list, $right_list));
				break;

			case 'showLoadBackup':
				$this->display($this->module->showLoadBackup());
				break;

			case 'ajaxGetBackup':
				$this->display($this->module->ajaxGetBackup());
				break;

			case 'ajaxDeleteBackup':
				$this->display($this->module->ajaxDeleteBackup(Tools::getValue('name')));
				break;

			case 'ajaxLoadBackup':
				$this->display($this->module->ajaxLoadBackup(Tools::getValue('name')));
				break;
		}
		exit;
	}

	public function processChimp($action)
	{
		if (!isset($this->module->chimp))
			exit;

		$chimp =& $this->module->chimp;

		switch ($action)
		{
			case 'pingChimp':
				$this->display($chimp->pingChimp());

			case 'installChimp':
				$api_key = Tools::getValue('api_key');
				$list_id = Tools::getValue('list_id');
				$this->display($chimp->installChimp($api_key, $list_id));

			case 'uninstallChimp':
				$this->display($chimp->uninstallChimp());

			case 'updateSyncCheckbox':
				$name  = Tools::getValue('name');
				$value = (int)Tools::getValue('value');
				$this->display($chimp->updateSyncCheckbox($name, $value));

			case 'setSyncLists':
				$data  = Tools::getValue('data');
				$this->display($chimp->setSyncLists($data));

			case 'deleteChimpOrders':
				$this->display($chimp->deleteChimpOrders());

			case 'resetSyncOrderDate':
				$this->display($chimp->resetSyncOrderDate());

			case 'getSyncListsStatus':
				$this->display($chimp->getSyncListsStatus());

			case 'startSyncLists':
				$this->display($chimp->startSyncLists());

			case 'stopSync':
				$this->display($chimp->stopSync());

			case 'syncListsBack':
				$start = Tools::getValue('start');
				$limit = Tools::getValue('limit');
				$this->display($chimp->syncListsBack($start, $limit));

			case 'getAllTemplates':
				$this->display($chimp->getAllTemplates());

			case 'getTemplateSource':
				$template_id = Tools::getValue('template_id');
				$type        = Tools::getValue('type');
				$this->display($chimp->getTemplateSource($template_id, $type));

			case 'importTemplate':
				$name     = Tools::getValue('name');
				$content  = Tools::getValue('content');
				$override = (bool)Tools::getValue('override');
				$this->display($chimp->importTemplate($name, $content, $override));

			case 'exportTemplate':
				$name     = Tools::getValue('name');
				$id_lang  = (int)Tools::getValue('id_lang');
				$filename = Tools::getValue('filename');
				$override = (bool)Tools::getValue('override');
				$this->display($chimp->exportTemplate($name, $id_lang, $filename, $override));
		}

		exit;
	}

	public function processCustomField($action)
	{
		$controller = NewsletterProSubscribersCustomFieldController::newInstance();

		switch ($action)
		{
			case 'addField':
				$variable_name = Tools::getValue('variable_name');
				$type = Tools::getValue('type');
				$required = Tools::getValue('required');
				$this->display($controller->addField($variable_name, $type, (int)$required), true);

			case 'deleteField':
				$id = Tools::getValue('id');
				$this->display($controller->deleteField((int)$id), true);

			case 'addValue':
				$id = Tools::getValue('id');
				$value = Tools::getValue('value');
				$this->display($controller->addValue((int)$id, $value), true);

			case 'removeValueByKey':
				$id = Tools::getValue('id');
				$key = Tools::getValue('key');
				$this->display($controller->removeValueByKey((int)$id, $key), true);

			case 'updateValue':
				$id = Tools::getValue('id');
				$key = Tools::getValue('key');
				$value = Tools::getValue('value');
				$this->display($controller->updateValue((int)$id, $key, $value), true);

			case 'getValueByKey':
				$id = Tools::getValue('id');
				$key = Tools::getValue('key');
				$this->display($controller->getValueByKey((int)$id, $key), true);

			case 'getValuesList':
				$id = Tools::getValue('id');
				$id_lang = Tools::getValue('id_lang');
				$this->display($controller->getValuesList((int)$id, $id_lang), true);

			case 'getFieldsList':
				$this->display($controller->getFieldsList(), true);

			case 'changeFieldRequired':
				$id = Tools::getValue('id');
				$value = Tools::getValue('value');
				$this->display($controller->changeFieldRequired((int)$id, (int)$value), true);

			case 'saveShowColumns':
				$columns = Tools::getValue('columns');
				$this->display($controller->saveShowColumns($columns), true);

			case 'getCustomColumns':
				$this->display($controller->getCustomColumns(), true);
		}

		exit;
	}

	private function mapGetIdCategories($row)
	{
		return isset($row['id_category']) ? $row['id_category'] : false;
	}

	public function processFront($action)
	{
		switch ($action)
		{
			case 'submitNewsletterProSubscribe':
				$this->display($this->module->submitNewsletterProSubscribe());

			case 'submitNewsletterProSubscribeCloseForever':
				$this->display($this->module->submitNewsletterProSubscribeCloseForever());
		}

		if (Tools::isSubmit('getChildrenCategories') && Tools::isSubmit('id_category_parent'))
		{
			if (method_exists('Category', 'getChildrenWithNbSelectedSubCat'))
			{
				$children_categories_result = Category::getChildrenWithNbSelectedSubCat(Tools::getValue('id_category_parent'), Tools::getValue('selectedCat'), Context::getContext()->language->id, null, Tools::getValue('use_shop_context'));

				$children_categories_ids = array_map(array($this, 'mapGetIdCategories'), $children_categories_result);
				// get only the active categories
				$active_categories = Db::getInstance()->executeS('
					SELECT `id_category` 
					FROM `'._DB_PREFIX_.'category` 
					WHERE `id_category` IN ('.pSQL(implode(',', $children_categories_ids)).') AND `active` = 1'
				);
				$active_categories_ids = array_map(array($this, 'mapGetIdCategories'), $active_categories);
				
				$children_categories = array();
				foreach ($children_categories_result as $category) 
				{
					if (in_array($category['id_category'], $active_categories_ids))
						$children_categories[] = $category;
				}				
			}
			else if (class_exists('NewsletterPro'))
				$children_categories = NewsletterPro::getChildrenWithNbSelectedSubCat(Tools::getValue('id_category_parent'), Tools::getValue('selectedCat'), Context::getContext()->language->id, null, Tools::getValue('use_shop_context'));

			$this->display(Tools::jsonEncode($children_categories));
		}

		if (Tools::isSubmit('searchCategory'))
		{
			$q = Tools::getValue('q');
			$limit = Tools::getValue('limit');
			$results = Db::getInstance()->executeS(
				'SELECT c.`id_category`, cl.`name`
				FROM `'._DB_PREFIX_.'category` c
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`'.NewsletterPro::addSqlRestrictionOnLang('cl').')
				WHERE cl.`id_lang` = '.(int)$this->context->language->id.' AND c.`level_depth` <> 0
				AND cl.`name` LIKE \'%'.pSQL($q).'%\'
				GROUP BY c.id_category
				ORDER BY c.`position`
				LIMIT '.(int)$limit);

			if ($results)
			foreach ($results as $result)
				echo trim($result['name']).'|'.(int)$result['id_category']."\n";
		}

		if (Tools::isSubmit('getParentCategoriesId') && $id_category = Tools::getValue('id_category'))
		{
			$category = new Category((int)$id_category);
			$results = Db::getInstance()->executeS('SELECT `id_category` FROM `'._DB_PREFIX_.'category` c WHERE c.`nleft` < '.(int)$category->nleft.' AND c.`nright` > '.(int)$category->nright.'');
			$output = array();
			foreach ($results as $result)
				$output[] = $result;

			$this->display(Tools::jsonEncode($output));
		}

		exit;
	}

	public function display($str, $json = false)
	{
		if ($json)
			@header('Content-Type: application/json');

		echo $str;
		exit;
	}
}
?>