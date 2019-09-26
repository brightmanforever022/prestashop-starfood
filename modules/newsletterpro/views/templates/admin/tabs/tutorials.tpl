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

{if isset($fix_document_write) && $fix_document_write == 1}
<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;">
{else}
<script type="text/javascript"> 
	if(window.location.hash == '#tutorials') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: block;">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;">');
	{rdelim} 
</script>
{/if}
	<h4>{l s='Tutorials' mod='newsletterpro'}</h4>
	<div class="separation"></div>
	<div>
		<div class="clearfix tutorial-video">
			<a class="tutorial-button" href="{$tutorial_link|escape:'quotes':'UTF-8'}" target="_blank">
				<img class="tutorial-img" src="{$module_img_path|escape:'quotes':'UTF-8'}full.jpg">
			</a>
			<div class="description">
				<h4>{l s='How to' mod='newsletterpro'}</h4>
				<div class="separation"></div>

				<p> {l s='Create a custom template with Newsletter Pro.' mod='newsletterpro'} </p>
				<p> {l s='Insert selected products into template.' mod='newsletterpro'} </p>
				<p> {l s='Upload and add images into template.' mod='newsletterpro'} </p>
				<p> {l s='Select the customers with filters and then send newsletters.' mod='newsletterpro'} </p>
				<p> {l s='Select the customers with filters and then schedule multiple tasks.' mod='newsletterpro'} </p>
				<p> {l s='View newsletter history.' mod='newsletterpro'} </p>
				<p> {l s='Newsletter Statistics. View top 100 clicked products.' mod='newsletterpro'} </p>
				<p> {l s='Setup the newsletter statistics for Google Analytics.' mod='newsletterpro'}
				<p> {l s='Create multiple SMTP configurations (Gmail, Mailjet, Mandrill, ...)' mod='newsletterpro'}
				<p> {l s='Mail Chimp synchronization. (The Customers, Visitors and Personal list)' mod='newsletterpro'}
				<p> {l s='Import & Export templates from Mail Chimp.' mod='newsletterpro'}
				<p> {l s='Change template appearance using CSS style.' mod='newsletterpro'}
				<p> {l s='Import email addresses from a CSV file.' mod='newsletterpro'}
				<p> {l s='Allow customers to subscribe at multiple categories.' mod='newsletterpro'}
				<p> {l s='The power of dynamic variables.' mod='newsletterpro'}
				<p> {l s='Create new variables related to the customers.' mod='newsletterpro'}
				<p> {l s='Select the new products.' mod='newsletterpro'}
				<p> {l s='Preview the products on multiple templates in real time.' mod='newsletterpro'}
			</div>
		</div>
	</div>
</div>