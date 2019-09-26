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
	if(window.location.hash == '#sendNewsletters') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: block;">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;">');
	{rdelim} 
</script>
{/if}
	<div style="0px 0px 15px -5px;">
		<h4 style="float: left;">{l s='Select Customers & Send newsletters' mod='newsletterpro'}</h4>
		<a id="btn-bounced-emails" class="btn btn-default btn-bounced-emails" href="javascript:{}"><i class="icon icon-eraser"></i> {l s='Manage Bounced Emails' mod='newsletterpro'}</a>
		<div class="clear" style="clear: both;"></div>
		<div class="separation"></div>
	</div>
	<div class="clear" style="clear: both;"></div>

	<div class="form-group clearfix">
		<div class="clearfix">
			<label class="control-label col-sm-2"><span class="label-tooltip">{l s='Filter Selection' mod='newsletterpro'}</span></label>
			<div class="col-sm-10">
				<div class="form-inline">
					<div class="form-group">
						<select id="np-filter-selection" class="fixed-width-xxl gk-select">
							<option value="0">- {l s='none' mod='newsletterpro'} -</option>
							{foreach $filters_selection as $filter}
								<option value="{$filter.id_newsletter_pro_filters_selection|intval}">{$filter.name|escape:'html':'UTF-8'}</option>
							{/foreach}
						</select>
					</div>
					<div class="form-group">
						<a id="np-delete-filter-selection" href="javascript:{}" class="btn btn-default btn-margin" style="display: none;">
							<i class="icon icon-trash-o"></i>
							{l s='Delete' mod='newsletterpro'}
						</a>
					</div>
					<div class="form-group pull-right">
						<label class="control-label"><span class="label-tooltip">{l s='Filter Name' mod='newsletterpro'}</span></label>
						<input id="np-name-filter-selection" class="form-control fixed-width-xxl" type="text">
						<a id="np-add-filter-selection" href="javascript:{}" class="btn btn-default btn-margin">
							<i class="icon icon-plus-square"></i>
							{l s='Add' mod='newsletterpro'}
						</a>
					</div>
				</div>
			</div>
		</div>

		<div class="col-sm-10 col-sm-offset-2">
			<p class="help-block">{l s='Select you users with your predefined filters. The filter selection can be applied only to the drop down filters.' mod='newsletterpro'}</p>
		</div>
	</div>

	<div class="label-on-row">
		<h4>
			{if $CONFIGURATION.VIEW_ACTIVE_ONLY == true}
				{l s='Users subscribed' mod='newsletterpro'}
			{else}
				{l s='Users' mod='newsletterpro'}
			{/if}
			: <span id="customers-count">0</span> <span id="customers_filtered"></span>
			{if $CONFIGURATION.PS_MULTISHOP_FEATURE_ACTIVE == true}{$users_lists_shop_count_message|escape:'html':'UTF-8'}{/if}
		</h4>
		<span id="users-ajax-loader" class="ajax-loader" style="display: none; margin: 0; margin-left: 6px;"></span>
		<div class="clear">&nbsp;</div>
		<div class="separation"></div>
	</div>

	<div class="data-grid-div customers-list-box">
		<table id="customers-list" class="table table-bordered customers-list">
			<thead>
				<tr>
					<th class="chackbox" data-template="chackbox">&nbsp;</th>
					<th class="image" data-field="img_path">&nbsp;</th>
					<th class="company" data-field="company">{l s='Company' mod='newsletterpro'}</th>
					<th class="firstname" data-field="firstname">{l s='First Name' mod='newsletterpro'}</th>
					<th class="lastname" data-field="lastname">{l s='Last Name' mod='newsletterpro'}</th>
					<th class="email" data-field="email">{l s='Email' mod='newsletterpro'}</th>
					<th class="shop_name" data-field="shop_name">{l s='Shop Name' mod='newsletterpro'}</th>
					<th class="newsletter" data-field="newsletter">{l s='Subscribed' mod='newsletterpro'}</th>
					<th class="actions" data-template="actions">{l s='Actions' mod='newsletterpro'}</th>
				</tr>
			</thead>
		</table>
	</div>

	<div id="visitors-list-display" {if $CONFIGURATION.SUBSCRIPTION_ACTIVE == false}style="display:block;"{else}style="display:none;"{/if}>
		<br>
		<div class="label-on-row">
			<h4>
				{if $CONFIGURATION.VIEW_ACTIVE_ONLY == true}
					{l s='Visitors subscribed' mod='newsletterpro'} <span style="font-weight: normal;">{l s='(at the Block Newsletter module)' mod='newsletterpro'}</span> 
				{else}
					{l s='Visitors' mod='newsletterpro'} <span style="font-weight: normal;">{l s='(at the Block Newsletter module)' mod='newsletterpro'}</span> 
				{/if}
				: <span id="visitors-count">0</span>
				{if $CONFIGURATION.PS_MULTISHOP_FEATURE_ACTIVE == true}{$users_lists_shop_count_message|escape:'html':'UTF-8'}{/if}
			</h4>
			<span id="visitors-ajax-loader" class="ajax-loader" style="display: none; margin: 0; margin-left: 6px;"></span>
			<div class="clear"></div>
			<div class="separation"></div>
		</div>

		<div class="data-grid-div visitors-list-box">
			<table id="visitors-list" class="table table-bordered visitors-list">
				<thead>
					<tr>
						<th class="chackbox" data-template="chackbox">&nbsp;</th>
						<th class="image" data-field="img_path">&nbsp;</th>
						<th class="email" data-field="email">{l s='Email' mod='newsletterpro'}</th>
						<th class="shop_name" data-field="shop_name">{l s='Shop Name' mod='newsletterpro'}</th>
						<th class="ip" data-field="ip_registration_newsletter">{l s='IP' mod='newsletterpro'}</th>
						<th class="np-active" data-field="active">{l s='Subscribed' mod='newsletterpro'}</th>
						<th class="actions" data-template="actions">{l s='Actions' mod='newsletterpro'}</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>

	<div id="visitors-np-list-display" {if $CONFIGURATION.SUBSCRIPTION_ACTIVE == true}style="display:block;"{else}style="display:none;"{/if}>
		<br>
		<div class="label-on-row">
			<h4>
				{if $CONFIGURATION.VIEW_ACTIVE_ONLY == true}
					{l s='Visitors subscribed' mod='newsletterpro'} <span style="font-weight: normal;">{l s='(at the Newsletter Pro module)' mod='newsletterpro'}</span>
				{else}
					{l s='Visitors' mod='newsletterpro'} <span style="font-weight: normal;">{l s='(at the Newsletter Pro module)' mod='newsletterpro'}</span>
				{/if}
				 : <span id="visitors-np-count">0</span>
				{if $CONFIGURATION.PS_MULTISHOP_FEATURE_ACTIVE == true}{$users_lists_shop_count_message|escape:'html':'UTF-8'}{/if}
			</h4>
			<span id="visitors-np-ajax-loader" class="ajax-loader" style="display: none; margin: 0; margin-left: 6px;"></span>
			<div class="clear"></div>
			<div class="separation"></div>
		</div>

		<div class="data-grid-div visitors-np-list-box">
			<table id="visitors-np-list" class="table table-bordered visitors-np-list">
				<thead>
					<tr>
						<th class="chackbox" data-template="chackbox">&nbsp;</th>
						<th class="image" data-field="img_path">&nbsp;</th>
						<th class="firstname" data-field="firstname">{l s='First Name' mod='newsletterpro'}</th>
						<th class="lastname" data-field="lastname">{l s='Last Name' mod='newsletterpro'}</th>
						<th class="email" data-field="email">{l s='Email' mod='newsletterpro'}</th>
						<th class="shop_name" data-field="shop_name">{l s='Shop Name' mod='newsletterpro'}</th>
						<th class="np-active" data-field="active">{l s='Subscribed' mod='newsletterpro'}</th>
						{if isset($show_custom_columns_format) && !empty($show_custom_columns_format)}
							{foreach $show_custom_columns_format as $column => $name}
								<th class="head_custom_column_{$column|escape:'html':'UTF-8'}" data-field="{$column|escape:'html':'UTF-8'}">{$name|escape:'htmlall':'UTF-8'}</th>
							{/foreach}
						{/if}
						<th class="actions" data-template="actions">{l s='Actions' mod='newsletterpro'}</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>

	<br>
	<div class="label-on-row">
		<h4> 
			{if $CONFIGURATION.VIEW_ACTIVE_ONLY == true}
				{l s='Added users subscribed' mod='newsletterpro'}
			{else}
				{l s='Added users' mod='newsletterpro'}
			{/if}
			: <span id="added-count">0</span>
			{if $CONFIGURATION.PS_MULTISHOP_FEATURE_ACTIVE == true}{$users_lists_shop_count_message|escape:'html':'UTF-8'}{/if}
		</h4>
		<span id="added-ajax-loader" class="ajax-loader" style="display: none; margin: 0; margin-left: 6px;"></span>
		<div class="clear"></div>
		<div class="separation"></div>
	</div>

	<div class="data-grid-div added-list-box">
		<table id="added-list" class="table table-bordered added-list">
			<thead>
				<tr>
					<th class="chackbox" data-template="chackbox">&nbsp;</th>
					<th class="image" data-field="img_path">&nbsp;</th>
					<th class="firstname" data-field="firstname">{l s='First Name' mod='newsletterpro'}</th>
					<th class="lastname" data-field="lastname">{l s='Last Name' mod='newsletterpro'}</th>
					<th class="email" data-field="email">{l s='Email' mod='newsletterpro'}</th>
					<th class="shop_name" data-field="shop_name">{l s='Shop Name' mod='newsletterpro'}</th>
					<th class="np-active" data-field="active">{l s='Subscribed' mod='newsletterpro'}</th>
					<th class="actions" data-template="actions">{l s='Actions' mod='newsletterpro'}</th>
				</tr>
			</thead>
		</table>
	</div>
	
	<br>

	<div class="add-exclusion-box">
		<div class="form-group clearfix">
			<span>{l s='There are' mod='newsletterpro'} <span style="color: red;" id="exclusion-emails-count">{$exclusion_emails_count|intval}</span> {l s='excluded emails from the process of sending newsletters.' mod='newsletterpro'}</span>
		</div>
		<div>
			<a id="btn-add-exclusion" href="javascript:{}" class="btn btn-default btn-add-exclusion"><i class="icon icon-plus-square"></i> {l s='Add Exclusion Emails' mod='newsletterpro'}</a>
            <a id="btn-view-exclusion" href="javascript:{}" class="btn btn-default btn-view-exclusion"><i class="icon icon-list"></i> {l s='View Exclusion Emails' mod='newsletterpro'}</a>
			<a id="btn-clear-exclusion" href="javascript:{}" class="btn btn-danger btn-add-exclusion"><span class="btn-ajax-loader"></span><i class="icon icon-eraser"></i> {l s='Clear Exclusion Emails' mod='newsletterpro'}</a>
		</div>
	</div>

	<br>
	
	<h4>{l s='Send newsletters' mod='newsletterpro'}</h4>
	<div class="separation"></div>

	<div class="div_userlist">

		<div id="emails-to-send"></div>

		<div id="emails-sent" class="div_userlist"></div>

		<div class="clear">&nbsp;</div>
		
		<div class="form-group clearfix">
			<div class="send-progressbar-box">
				<div id="send-progressbar"></div>
			</div>
		</div>

		<div class="form-group clearfix">
			<div id="last-send-error-div" class="last-send-error-div" style="display: none;">
				<div class="col-sm-12 clearfix">
					<span class="waring-icon pull-left"></span>
					<label class="control-label" style="padding-top: 0; margin-left: 10px;">{l s='Last error message:' mod='newsletterpro'}</label>
				</div>
				<div class="col-sm-12 clearfix row">
					<div id="last-send-error" class="alert alert-danger" style="display: none;"></div>
				</div>
			</div>
		</div>

		<div class="form-group clearfix">
			<div id="test-email" class="test-email br-space">
				<div class="form-group clearfix">
					<div class="col-sm-8">
						<div class="col-sm-4">
							<input id="test-email-checkbox" type="checkbox"/>
							<label class="control-label" style="width: auto;" for="test-email-checkbox"><span class="label-tooltip">{l s='Send a test email' mod='newsletterpro'}</span></label>
						</div>
						<div class="col-sm-8">
							<input id="test-email-input" class="form-control fixed-width-xxl" type="text" value="{$shop_email|escape:'html':'UTF-8'}"/>
							<span id="test-email-success-message" class="test-email-success-message" style="display: none;"></span>
						</div>
					</div>
					<div class="col-sm-4">
						<div id="test-send-email-box" class="form-inline" style="display: none;">
							<div class="form-group pull-right btn-margin-left">
								<a id="test-email-button" class="btn btn-default pull-right" href="javascript:{}" onclick="NewsletterProControllers.SendController.sendTestEmail($(this))">
									<span class="btn-ajax-loader"></span>
									<i class="icon icon-envelope"></i> {l s='Send test' mod='newsletterpro'}
								</a>
							</div>
							<div class="form-group pull-right btn-margin-right">
								<div id="send-test-email-language-switcher"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-12 clearfix">
					<span id="test-email-message" class="test-email-message">&nbsp;</span>
				</div>
			</div>
		</div>

		<div class="br-space np-send-method-box-">
			<div class="np-send-method-box-left">
				<div id="np-send-method-display" class="np-send-method-display"></div>
				<span id="email-sleep-message">&nbsp;</span>
			</div>
			<div class="np-send-method-box-right">
				<a id="np-btn-performances" href="javascript:{}" class="btn btn-warning pull-right np-btn-performances">
				<i class="icon icon-bolt"></i> <span class="np-button-text">{l s='Performances & Limits' mod='newsletterpro'}</span></a>
			</div>
			<div class="clear">&nbsp;</div>
		</div>

		<div class="clear" height="0;">&nbsp;</div>
		<br />

		<a id="previous-send-newsletters" href="#createTemplate" class="btn btn-primary pull-left np-send-previous-step" onclick="NewsletterProControllers.NavigationController.goToStep( 4 );">
			&laquo; {l s='Previous Step' mod='newsletterpro'}</span>
		</a>

		<a id="send-newsletters" href="javascript:{}" class="btn btn-success pull-right btn-margin np-send-newsletters" style="display: none;">
			<i class="icon icon-send"></i> {l s='Send' mod='newsletterpro'}
		</a>

		<a id="new-task" href="javascript:{}" class="btn btn-success pull-right task-button btn-margin np-send-new-task" data-trans-noemail="{l s='You have not select any email for this task!' mod='newsletterpro'}" style="display: none;">
			<i class="icon icon-clock-o"></i> {l s='New Task' mod='newsletterpro'}
		</a>

		<a id="stop-send-newsletters" href="javascript:{}" class="btn btn-danger pull-left btn-margin np-send-stop-newsletters" style="display: none;">
			<i class="icon icon-remove"></i> {l s='Cancel' mod='newsletterpro'}
		</a>

		<a id="continue-send-newsletters" href="javascript:{}" class="btn btn-success pull-right btn-margin np-send-continue-newsletters" style="display: none;">
			<i class="icon icon-refresh"></i> {l s='Continue' mod='newsletterpro'}
		</a>

		<a id="pause-send-newsletters" href="javascript:{}" class="btn btn-primary pull-right btn-margin np-send-pause-newsletter" style="display: none; text-transform: capitalize;">
			<i class="icon icon-pause"></i> {l s='Pause' mod='newsletterpro'}
		</a>
		<div class="clear">&nbsp;</div>
	</div>
</div>