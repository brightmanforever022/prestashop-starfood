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

<div class="form-group clearfix">
	<a class="btn btn-default pull-left" href="javascript:{}" onclick="NewsletterProComponents.objs.uploadCSV.geBack();"><i class="icon icon-chevron-left on-left" style="font-size: 11px;"></i> <span>{l s='Go Back' mod='newsletterpro'}</span></a>

	{if isset($rows)}
		<span class="import-info">{l s='There are' mod='newsletterpro'} {$count|escape:'html':'UTF-8'} {l s='rows out of which' mod='newsletterpro'} <span style="color: #5C8A2D;">{$valid|strval}</span> {l s='has valid emails.' mod='newsletterpro'}</span>
		<a class="btn btn-default pull-right" href="javascript:{}" onclick="NewsletterProControllers.NavigationController.viewImported();"><i class="icon icon-eye"></i> {l s='View Imported List' mod='newsletterpro'}</a>
	{else if isset($msg)}
		<span class="{if $status == true}success-msg{else}error-msg{/if}">{$msg|escape:'html':'UTF-8'}</span>
	{/if}
</div>

{if isset($rows)}
<table class="table">
	<thead>
		<tr>
			{foreach $header as $head}
				<th>{$head|escape:'html':'UTF-8'}</th>
			{/foreach}
		</tr>
	</thead>
	<tbody>			
		{foreach $rows as $row}
		<tr>
			{foreach $row as $line}
			<td>{$line|escape:'html':'UTF-8'}</td>
			{/foreach}	
		</tr>
		{/foreach}
	</tbody>
</table>
{/if}
<div class="clear"></div>