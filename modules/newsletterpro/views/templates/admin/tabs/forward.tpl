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
	if(window.location.hash == '#forward') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: block;">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;">');
	{rdelim} 
</script>
{/if}
	<div class="clear">&nbsp;</div>
	<h4>{l s='Forward' mod='newsletterpro'}</h4>
	<div class="separation"></div>

	{if $CONFIGURATION.FWD_FEATURE_ACTIVE == 0}
	<div style="margin-bottom: 5px;" class="alert alert-warning clearfix">
		{l s='The forward feature is not activated for you customers. Go to the settings tab to activate it.' mod='newsletterpro'}
		<div class="clear"></div>
	</div>
	{/if}

	<div class="form-group clearfix">
		<h4 style="float: left;">{l s='Active forwarders' mod='newsletterpro'}</h4>

		<a href="javascript:{}" id="clear-forwarders" class="btn btn-default" style="float: right;">
			<i class="icon icon-eraser"></i>
			{l s='Clear Forwarders' mod='newsletterpro'}
		</a>
		<div class="clear"></div>
		<div class="separation"></div>
	</div>

	<table id="forward-list" class="table table-bordered forward-list">
		<thead>
			<tr>
				<th class="count" data-template="count">{l s='Forwarders' mod='newsletterpro'}</th>
				<th class="from" data-field="from">{l s='Forwarder Email' mod='newsletterpro'}</th>
				<th class="date_add" data-field="date_add">{l s='Date Added' mod='newsletterpro'}</th>
				<th class="actions" data-template="actions">{l s='Actions' mod='newsletterpro'}</th>
			</tr>
		</thead>
	</table>

</div>