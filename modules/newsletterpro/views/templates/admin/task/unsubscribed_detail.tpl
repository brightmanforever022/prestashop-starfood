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
					<th class="email">{l s='Email' mod='newsletterpro'}</th>
					<th class="last-item">{l s='Unsubscribed Date' mod='newsletterpro'}</th>
				</tr>
			</thead>
			<tbody>
			{foreach $result as $item}
				<tr>
					<td class="x-icon">
						<span class="status"> 
							<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>
						</span>
					</td>
					<td>
						<span class="email_text">{$item.email|escape:'html':'UTF-8'}</span>
					</td>
					<td class="last-item">
						<span> {$item.date_add|escape:'html':'UTF-8'} </span>
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
	<div class="clear">&nbsp;</div>
</div>
{/if}