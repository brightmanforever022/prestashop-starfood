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
	<div class="form-inline clearfix">
		<div class="form-group">
			<a class="btn btn-default pull-left" href="javascript:{}" onclick="NewsletterProComponents.objs.uploadCSV.geBack();"><i class="icon icon-chevron-left on-left" style="font-size: 11px;"></i> <span>{l s='Go Back' mod='newsletterpro'}</span></a>
		</div>
		<div class="form-group">
			<span class="import-info">{l s='There are' mod='newsletterpro'} {$count|escape:'html':'UTF-8'} {l s='rows' mod='newsletterpro'}</span>
		</div>
		<div class="form-group pull-right">
			<div class="form-group">
				<label class="control-label"><span class="label-tooltip">{l s='Filter Name' mod='newsletterpro'}</span></label>
			</div>
			<div class="form-group">
				<input id="import-csv-filter-name" class="form-control fixed-width-xxl" value="">
			</div>

			<div class="form-group">
				<a class="btn btn-default" href="javascript:{}" onclick="NewsletterProComponents.objs.uploadCSV.importCSV( $(this) );" data-no-fields="{l s='There are no fields assigned to the column.' mod='newsletterpro'}" data-no-email="{l s='You have to set the email field!' mod='newsletterpro'}">
					<i class="icon icon-upload"></i> {l s='Import' mod='newsletterpro'}
				</a>
			</div>
		</div>
	</div>
</div>

<table class="table">
	<thead>
		<tr>
			{foreach $header as $head}
				<th>{$head|escape:'html':'UTF-8'}</th>
			{/foreach}
		</tr>
		<tr class="second">
			{foreach $header as $head}
			<th>
				<select autocomplete="off" data-field="{$head|escape:'html':'UTF-8'}" onchange="NewsletterProComponents.objs.uploadCSV.addField( $(this) );">
					<option value="0">-</option>
					{foreach $db_fields key=key item=name}
					<option value="{$key|escape:'html':'UTF-8'}">{$name|escape:'html':'UTF-8'}</option>
					{/foreach}
				</select>
			</th>
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
<div class="clear"></div>