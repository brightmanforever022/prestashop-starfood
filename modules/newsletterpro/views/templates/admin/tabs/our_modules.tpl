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
	if(window.location.hash == '#ourModules') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: block;">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;">');
	{rdelim} 
</script>
{/if}
	<h4>{l s='Modules developed by us' mod='newsletterpro'}</h4>
	<div class="separation"></div>

	<p>{l s='The modules are available on the prestashop official website.' mod='newsletterpro'}</p>
	<a href="http://addons.prestashop.com/{$lang_iso_code|escape:'quotes':'UTF-8'}/93_proquality" target="_blank" class="btn btn-success">
		<i class="icon icon-puzzle-piece"></i>
		{l s='View Catalog' mod='newsletterpro'}
	</a>
</div>