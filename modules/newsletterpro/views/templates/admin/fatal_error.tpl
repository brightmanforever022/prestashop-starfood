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

<div>
	<h2>{l s='You\'re module needs an update or a fresh installation by clicking on the uninstall and the install buttons!' mod='newsletterpro'}</h2>
	<h3>{l s='Please read the instructions below.' mod='newsletterpro'}</h3>
	{include file="$tpl_location"|cat:"templates/admin/module_update.tpl"}
	{if $update.needs_update != true}
		<div class="panel-box error">
			<p>{l s='You need to uninstall and install the module, if you will have the same error contact the developer of the module.' mod='newsletterpro'}</p>
		</div>
	{/if}
	<p>{l s='Please check if the settings "uninstall_all_tables" from the file "config.ini" has the value true.' mod='newsletterpro'}</p>
</div>
<div class="panel-box error">
{l s='The error is:' mod='newsletterpro'}
<br>
{$errors|escape:'quotes':'UTF-8'}
</div>


<script type="text/javascript">
	{* ESCAPED CONTENT *}
	NewsletterPro.dataStorage.addObject(jQuery.parseJSON('{$jsData|strval}'));

	// script alias, for the websites that have cache, this variables are not required, they can be deleted
	var NPRO_AJAX_URL = NewsletterPro.dataStorage.get('ajax_url');
</script>

<script type="text/javascript" src="{$module_url|escape:'quotes':'UTF-8'}views/js/fatal_error.js"></script>