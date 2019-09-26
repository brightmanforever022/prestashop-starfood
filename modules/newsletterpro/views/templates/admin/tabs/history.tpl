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
	if(window.location.hash == '#history') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: block;">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;">');
	{rdelim} 
</script>
{/if}

	<div class="clear">&nbsp;</div>
	<h4>{l s='History' mod='newsletterpro'}</h4>
	<div class="separation"></div>
	<div style="margin-bottom: 5px;">
		<h4 style="float: left;">{l s='Send history' mod='newsletterpro'}</h4>
		<a  href="javascript:{}" id="clear-send-history" class="btn btn-default pull-right btn-margin"><i class="icon icon-eraser"></i> {l s='Clear Send History' mod='newsletterpro'}</a>
		<a  href="javascript:{}" id="clear-send-details" class="btn btn-default pull-right btn-margin"><span class="btn-ajax-loader" style="display: none;"></span><i class="icon icon-eraser"></i> {l s='Clear Details' mod='newsletterpro'}</a>
		<div class="clear"></div>
		<div class="separation"></div>
	</div>

	<table id="send-history" class="table table-bordered send-history">
		<thead>
			<tr>
				<th class="template" data-field="template">{l s='Template' mod='newsletterpro'}</th>
				<th class="date" data-field="date">{l s='Date' mod='newsletterpro'}</th>
				<th class="emails-count" data-field="emails_count">{l s='Total emails' mod='newsletterpro'}</th>
				<th class="emails-success" data-field="emails_success">{l s='Succeed' mod='newsletterpro'}</th>
				<th class="emails-error" data-field="emails_error">{l s='Errors' mod='newsletterpro'}</th>
				<th class="clicks" data-field="clicks">{l s='Clicks' mod='newsletterpro'}</th>
				<th class="opened" data-field="opened">{l s='Read' mod='newsletterpro'}</th>
				<th class="unsubscribed" data-field="unsubscribed">{l s='Unsubscribed' mod='newsletterpro'}</th>
				<th class="fwd_unsubscribed" data-field="fwd_unsubscribed">{l s='Forward Uns' mod='newsletterpro'}</th>
				<th class="emails-msg" data-field="error_msg">{l s='Messages' mod='newsletterpro'}</th>
				<th class="actions" data-template="actions">{l s='Actions' mod='newsletterpro'}</th>
			</tr>
		</thead>
	</table>
	<br>
	<div style="margin-bottom: 5px;">
		<h4 style="float: left;">{l s='Task history' mod='newsletterpro'}</h4>

		<a  href="javascript:{}" id="clear-task-history" class="btn btn-default pull-right btn-margin"><i class="icon icon-eraser"></i> {l s='Clear Task History' mod='newsletterpro'}</a>
		<a  href="javascript:{}" id="clear-task-details" class="btn btn-default pull-right btn-margin"><span class="btn-ajax-loader" style="display: none;"></span><i class="icon icon-eraser"></i> {l s='Clear Details' mod='newsletterpro'}</a>
		<div class="clear"></div>
		<div class="separation"></div>
	</div>
	<table id="task-history" class="table table-bordered task-history">
		<thead>
			<tr>
				<th class="template" data-field="template">{l s='Template' mod='newsletterpro'}</th>
				<th class="date-start" data-field="date_start">{l s='Start Date' mod='newsletterpro'}</th>
				<th class="emails-count" data-field="emails_count">{l s='Total emails' mod='newsletterpro'}</th>
				<th class="emails-success" data-field="emails_success">{l s='Succeed' mod='newsletterpro'}</th>
				<th class="emails-error" data-field="emails_error">{l s='Errors' mod='newsletterpro'}</th>
				<th class="clicks" data-field="clicks">{l s='Clicks' mod='newsletterpro'}</th>
				<th class="opened" data-field="opened">{l s='Read' mod='newsletterpro'}</th>
				<th class="unsubscribed" data-field="unsubscribed">{l s='Unsubscribed' mod='newsletterpro'}</th>
				<th class="fwd_unsubscribed" data-field="fwd_unsubscribed">{l s='Forward Uns.' mod='newsletterpro'}</th>
				<th class="emails-msg" data-field="error_msg">{l s='Messages' mod='newsletterpro'}</th>
				<th class="actions" data-template="actions">{l s='Actions' mod='newsletterpro'}</th>
			</tr>
		</thead>
	</table>
</div>