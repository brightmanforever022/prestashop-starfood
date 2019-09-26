{*
* 2013-2015 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright 2013-2015 Ovidiu Cimpean
* @version   Release: 4
*}

<script type="text/javascript">

	{* ESCAPED CONTENT *}
	NewsletterPro.dataStorage.addObject(jQuery.parseJSON('{$jsData|strval}'));

	// script alias, for the websites that have cache, this variables are not required, they can be deleted
	var CATEGORIES_LIST = NewsletterPro.dataStorage.get('categories_list'),
		NPRO_CONFIGURATION = {
			'SUBSCRIPTION_ACTIVE': NewsletterPro.dataStorage.getNumber('configuration.SUBSCRIPTION_ACTIVE'),
			'VIEW_ACTIVE_ONLY': NewsletterPro.dataStorage.getNumber('configuration.VIEW_ACTIVE_ONLY'),
			'PS_SHOP_EMAIL': NewsletterPro.dataStorage.get('configuration.PS_SHOP_EMAIL')
		},
		NEWSLETTER_PRO_IMG_PATH = NewsletterPro.dataStorage.get('module_img_path'),
		DISPLAY_PRODUCT_IMAGE = NewsletterPro.dataStorage.getNumber('configuration.DISPLAY_PRODUCT_IMAGE'),
		NPRO_AJAX_URL = NewsletterPro.dataStorage.get('ajax_url'),
		NPRO_TRANSLATIONS = {
			'add': "{l s='add' mod='newsletterpro'}",
			'remove': "{l s='remove' mod='newsletterpro'}",
		};

	{if $href_replace == true}
		window.location.href = window.location.href.replace(/&downloadImportSample/, '');
	{/if}
</script>