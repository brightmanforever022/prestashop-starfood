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
<!-- Newsletter Pro Subscribe -->
<div id="newsletter_pro_subscribe">
	<form id="np-subscribe-form" style="margin: 0; padding: 0;" action="{$link->getPageLink('index')|escape:'quotes':'UTF-8'}" method="post">
		<div id="nps-popup" class="nps-popup {if isset($class_name)}{$class_name|escape:'html':'UTF-8'}{/if}" style="display:none;">
			{* HTML CONTENT *}
			<div id="nps-popup-content">{$render_template|strval}</div>
			<div id="nps-popup-response" style="display:none;"></div>
		</div>
	</form>
</div>
<!-- /Newsletter Pro Subscribe -->

<script type="text/javascript">
	if (typeof NewsletterPro !== 'undefined')
	{
		NewsletterPro.dataStorage.add('translations_subscribe', {
			'ajax request error' : "{l s='An error occurred at the ajax request!' mod='newsletterpro'}",
			'You must agree to the terms of service before subscribing.' : "{l s='You must agree to the terms of service before subscribing.' mod='newsletterpro'}"
		});

		try
		{

			{* ESCAPED CONTENT *}
			NewsletterPro.dataStorage.addObject(jQuery.parseJSON('{$subscription_template_front_info|strval}'));
		}
		catch (e)
		{
			console.error(e.message);
		}
	}
	else
		console.error("{l s='An error occurred, please disable the force compilation option.' mod='newsletterpro'}");
</script>