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

{if $result}
<div class="thd-step">
	<div style="width: 100%; float: left;">
		<table class="table table-unsubscribed" style="width: 100%;">
			<thead>
				<tr>
					<th class="x-icon">&nbsp;</th>
					<th style="width: 50%;">{l s='Email' mod='newsletterpro'}</th>
					<th class="item">{l s='Date Added' mod='newsletterpro'}</th>
					<th class="last-item">{l s='Actions' mod='newsletterpro'}</th>
				</tr>
			</thead>
			<tbody>
			{foreach $result as $item}
				<tr>
					<td class="x-icon">
						<span class="status"><span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span></span>
					</td>
					<td>
						<span class="email_text">{$item.to|escape:'html':'UTF-8'}</span>
					</td>
					<td class="item">
						<span> {$item.date_add|escape:'html':'UTF-8'} </span>
					</td>
					<td class="last-item">
						<span> <a href="javascript:{}" class="btn btn-default" data-email="{$item.to|escape:'html':'UTF-8'}" onclick="NewsletterPro.modules.forward.deleteForwardToEmail($(this))"><i class="icon icon-trash-o"></i> {l s='Delete' mod='newsletterpro'}</a></span>
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
	<div class="clear">&nbsp;</div>
</div>
{else}
<p>{l s='There are no details.' mod='newsletterpro'}</p>
{/if}