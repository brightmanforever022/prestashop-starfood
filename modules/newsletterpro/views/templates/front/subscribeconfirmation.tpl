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

{capture name=path}
<span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>{l s='Subscribe'  mod='newsletterpro'}
{/capture}

<div id="newsletterpro-subscribe">
{include file="$tpl_dir./errors.tpl"}

{if isset($success_message)}
	<div class="alert alert-success success">
	{foreach $success_message as $value}
		{$value|escape:'html':'UTF-8'} <br>
	{/foreach}
	</div>
{/if}
</div>
