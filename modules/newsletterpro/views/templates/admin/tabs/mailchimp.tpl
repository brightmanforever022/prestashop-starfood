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
<div id="{$tab_id|escape:'html':'UTF-8'}" class="tab-mailchimp" style="display: none;">
{else}
<script type="text/javascript"> 
	if(window.location.hash == '#mailchimp') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" class="tab-mailchimp" style="display: block;">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" class="tab-mailchimp" style="display: none;">');
	{rdelim} 
</script>
{/if}
	<h4>{l s='Mail Chimp' mod='newsletterpro'}</h4>
	<div class="separation"></div>

	<form id="chimp-settings">

		<div class="form-group clearfix">
			<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Api Key' mod='newsletterpro'}</span></label>
			<div class="col-sm-9">
				<div class="clearfix">
					<input class="form-control fixed-width-xxl" type="text" size="30" id="chimp-api-key" name="chimp_api_key" value="{if isset($CONFIGURATION.CHIMP.API_KYE)}{$CONFIGURATION.CHIMP.API_KYE|escape:'html':'UTF-8'}{/if}"> 
				</div>
				<p class="help-block">{l s='Insert your Mail Chimp Api Key. Log-in into your Mail Chimp account and go to the section "My Account" > "Extras" > "Api Key" and then copy the Api Key.' mod='newsletterpro'}</p>										
			</div>
		</div>

		<div class="form-group clearfix">
			<label class="control-label col-sm-3"><span class="label-tooltip">{l s='List ID' mod='newsletterpro'}</span></label>
			<div class="col-sm-9">
				<div class="clearfix">
					<input class="form-control fixed-width-xxl" type="text" size="30" id="chimp-list-id" name="chimp_list_id" value="{if isset($CONFIGURATION.CHIMP.ID_LIST)}{$CONFIGURATION.CHIMP.ID_LIST|escape:'html':'UTF-8'}{/if}"> 
				</div>
				<p class="help-block">{l s='Insert your Mail Chimp List ID. Log-in into your Mail Chimp account and go to the "Lists" section. Create a new list with the name "Newsletter Pro". After the list was created, go to the section "Settings" > "List name & defaults" and then copy List ID.' mod='newsletterpro'}</p>
			</div>
		</div>

		<div class="clearfix">

			<div class="form-group clearfix">
				<div class="col-sm-9 col-sm-offset-3">
					<a id="install-chimp" href="javascript:{}" class="btn btn-default btn-margin" style="display: none;">
						<span id="install-chimp-loading"></span>
						<i class="icon icon-plug"></i>
						{l s='Install Mail Chimp' mod='newsletterpro'}
					</a>

					<a id="uninstall-chimp" href="javascript:{}" class="btn btn-default btn-margin" style="display: none;">
						<span id="uninstall-chimp-loading"></span>
						<i class="icon icon-remove"></i>
						{l s='Uninstall Mail Chimp' mod='newsletterpro'}
					</a>

					<a id="ping-chimp" href="javascript:{}" class="btn btn-default btn-margin ping-chimp">
						<i class="icon icon-refresh"></i>
						{l s='Ping' mod='newsletterpro'}
					</a>
				</div>
			</div>

			<div id="chimp-menu" class="form-group clearfix chimp-menu" style="display: none;">
				<h4>{l s='Synchronization' mod='newsletterpro'}</h4>
				<div class="separation"></div>
				
				<div class="clearfix">
					<div class="col-sm-9 col-sm-offset-3">
						<span style="font-weight: normal;"><span id="last-sync-lists">{if isset($last_date_chimp_sync)}{$last_date_chimp_sync|escape:'html':'UTF-8'}{/if}</span> - {l s='last lists synchronization date' mod='newsletterpro'}</span>
					</div>
				</div>				

				<div class="form-group clearfix">
					<label class="control-label col-sm-3" style="padding-top: 13px;">{l s='Select Lists' mod='newsletterpro'}</label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label for="sync-customers" class="in-win control-label chimp-label">
								<input type="checkbox" id="sync-customers" name="sync_customers">
								{l s='Sync Customers List' mod='newsletterpro'}
							</label>
						</div>
						<div class="checkbox">
							<label for="sync-visitors" class="in-win control-label chimp-label">
								<input type="checkbox" id="sync-visitors" name="sync_visitors">
								{l s='Sync Visitors List' mod='newsletterpro'}
								{if $CONFIGURATION.SUBSCRIPTION_ACTIVE == true}
									{l s='(subscribed at the Newsletter Pro module)' mod='newsletterpro'}
								{else}
									{l s='(subscribed at the Block Newsletter module)' mod='newsletterpro'} 
								{/if}
							</label>
						</div>
						<div class="checkbox">
							<label for="sync-added" class="in-win control-label chimp-label">
								<input type="checkbox" id="sync-added" name="sync_added">
								{l s='Sync Added List' mod='newsletterpro'}
							</label>
						</div>
					</div>
				</div>

				<div class="clearfix">
					<div class="col-sm-9 col-sm-offset-3">
						<span style="font-weight: normal;"><span id="last-sync-orders">{if isset($chimp_last_date_sync_orders)}{$chimp_last_date_sync_orders|escape:'html':'UTF-8'}{/if}</span> - {l s='last orders synchronization date' mod='newsletterpro'} (<a id="reset-sync-order-date" href="javascript:{}" style="color: #49B2FF;">{l s='reset date' mod='newsletterpro'}</a>)</span>
					</div>
				</div>	
				
				<div class="form-group clearfix">
					<div class="col-sm-9 col-sm-offset-3">
						<div class="checkbox">
							<label for="sync-orders" class="in-win control-label chimp-label">
								<input type="checkbox" id="sync-orders" name="sync_added">
								{l s='Sync Orders' mod='newsletterpro'}
							</label>
						</div>
					</div>
				</div>

				<div class="form-group clearfix">
					<div class="col-sm-9 col-sm-offset-3">
						<a id="sync-lists" href="javascript:{}" class="btn btn-default sync-lists" style="display: none;">
							<span class="ajax-loader" style="display: none; float: left; margin-right: 6px; margin-top: 0px; margin-left: 0;"></span>
							<i class="icon icon-refresh"></i>
							<span>{l s='Sync Lists' mod='newsletterpro'}</span>
							<span id="sync-orders-button-text">& {l s='Sync Orders' mod='newsletterpro'}</span>
						</a>

						<a id="stop-sync-lists" href="javascript:{}" class="btn btn-default stop-sync-lists" style="display: none;">
							<i class="icon icon-remove"></i>
							{l s='Stop Sync Lists' mod='newsletterpro'}
						</a>
						<a id="delete-chimp-orders" href="javascript:{}" class="btn btn-default delete-chimp-orders" style="display: none;">
							<span class="ajax-loader" style="display: none; float: left; margin-right: 6px; margin-top: 0px; margin-left: 0;"></span>
							<i class="icon icon-trash-o"></i>
							<span>{l s='Delete Orders from MailChimp' mod='newsletterpro'}</span>
						</a>
					</div>
				</div>
			
				<div class="form-group clearfix">
					<div class="col-sm-9 col-sm-offset-3">
						<div id="sync-lists-progress-box" class="sync-lists-progress-box" style="display: none;">
							<div id="sync-error-message-box" style="display: none;">
								<span class="sync-error-message" style="color: red;"></span>
							</div>
							<div id="sync-added-progress" class="sync-added-progress" style="display: none;">
								<h4>{l s='Added List' mod='newsletterpro'} ( <span class="sync-emails-total">0</span> ) <span class="ajax-loader" style="display: none;"></span></h4>
								<p>( <span class="sync-emails-created">0</span> ) {l s='emails created' mod='newsletterpro'},  ( <span class="sync-emails-updated">0</span> ) {l s='emails updated' mod='newsletterpro'}, ( <span class="sync-emails-errors">0</span> ) {l s='emails errors' mod='newsletterpro'}</p>
								<div class="clear"></div>
							</div>
							<div id="sync-visitors-progress" class="sync-visitors-progress" style="display: none;">
								<h4>{l s='Visitors List' mod='newsletterpro'}  ( <span class="sync-emails-total">0</span> )  <span class="ajax-loader" style="display: none;"></span></h4>
								<p>( <span class="sync-emails-created">0</span> ) {l s='emails created' mod='newsletterpro'},  ( <span class="sync-emails-updated">0</span> ) {l s='emails updated' mod='newsletterpro'}, ( <span class="sync-emails-errors">0</span> ) {l s='emails errors' mod='newsletterpro'}</p>
								<div class="clear"></div>
							</div>
							<div id="sync-customers-progress" class="sync-customers-progress" style="display: none;">
								<h4>{l s='Customers List' mod='newsletterpro'}  ( <span class="sync-emails-total">0</span> )  <span class="ajax-loader" style="display: none;"></span></h4>
								<p>( <span class="sync-emails-created">0</span> ) {l s='emails created' mod='newsletterpro'},  ( <span class="sync-emails-updated">0</span> ) {l s='emails updated' mod='newsletterpro'}, ( <span class="sync-emails-errors">0</span> ) {l s='emails errors' mod='newsletterpro'}</p>
								<div class="clear"></div>
							</div>

							<div id="sync-orders-progress" class="sync-orders-progress" style="display: none;">
								<h4>{l s='Orders' mod='newsletterpro'}  ( <span class="sync-emails-total">0</span> )  <span class="ajax-loader" style="display: none;"></span></h4>
								<p>( <span class="sync-emails-created">0</span> ) {l s='orders created' mod='newsletterpro'},  <span style="display: none;">( <span class="sync-emails-updated">0</span> ) {l s='orders updated' mod='newsletterpro'},</span> ( <span class="sync-emails-errors">0</span> ) {l s='orders errors' mod='newsletterpro'} <span style="font-weight: normal;">({l s='The orders already exists in MailChimp' mod='newsletterpro'})</span></p>
								<div class="clear"></div>
							</div>
						</div>
						<div id="sync-chimp-errors-message" class="alert alert-danger" style="display: none;">
						</div>
					</div>
				</div>
			</div>

			<div id="sync-back-chimp-content" class="form-group clearfix" style="display: none;">
				<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Import emails from MailChimp' mod='newsletterpro'}</span></label>
				<div class="col-sm-9">
					<a id="sync-chimp-lists-back" href="javascript:{}" class="btn btn-default">
						<span class="btn-ajax-loader"></span>
						<i class="icon icon-refresh"></i>
						<span>{l s='Sync Back' mod='newsletterpro'}</span>
					</a>
				</div>
			</div>

			<div class="form-group clearfix">
				<div class="col-sm-9 col-sm-offset-3">
					<div id="sync-lists-back-progress-box" class="sync-lists-progress-box" style="display: none;">
						<div id="sync-list-back-error-message-box" style="display: none;">
							<span class="sync-error-message" style="color: red;"></span>
						</div>
						<div id="sync-list-back-progress" class="sync-added-progress" style="display: none;">
							<h4>{l s='Import emails from MailChimp' mod='newsletterpro'} ( <span class="sync-emails-total">0</span> ) <span class="ajax-loader" style="display: none;"></span></h4>
							<p>( <span class="sync-emails-created">0</span> ) {l s='emails created' mod='newsletterpro'},  ( <span class="sync-emails-updated">0</span> ) {l s='emails updated' mod='newsletterpro'}, ( <span class="sync-emails-errors">0</span> ) {l s='emails errors' mod='newsletterpro'}</p>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>

			<div class="from-group clearfix">
				{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" 
					title_name={l s='Synchronize unsubscribed users' mod='newsletterpro'}
					description={l s='Synchronize the unsubscribed users to MailChimp.' mod='newsletterpro'}
					label_id='mailchimp_synchronize_users' 
					label_name='' 
					input_onchange='NewsletterProControllers.SettingsController.chimpSyncUnsubscribed($(this));'
					is_checked=$CONFIGURATION.CHIMP_SYNC_UNSUBSCRIBED
				}
			</div>

			<div class="alert alert-info clearfix chimp-cron-job">
				<p style="margin-top: 0;" class="cron-link"><span style="color: black;">CRON URL:</span> <span class="icon-cron-link"></span>{$sync_chimp_link|escape:'quotes':'UTF-8'}</p>
				<p style="margin-top: 0;">{l s='To make the Mail Chimp synchronization to run automatically every day set the CRON job from your website control panel (Plesk, cPanel, DirectAdmin, etc.). Run this script every day.' mod='newsletterpro'}</p>
				<p style="margin-top: 0;">{l s='Synchronize all the users list before to setup the CRON job.' mod='newsletterpro'}</p>
			</div>

			<div class="alert alert-info clearfix chimp-cron-job">
				<p style="margin-top: 0;" class="cron-link"><span style="color: black;">WEBHOOK URL:</span> <span class="icon-cron-link"></span>{$webhook_chimp_link|escape:'quotes':'UTF-8'}</p>
				<p style="margin-top: 0;">{l s='With this link you can create webhooks in MailChimp account.' mod='newsletterpro'}</p>
				<p style="margin-top: 0;">{l s='The webhook features supported by the module are: \"Subscribe\", \"Unsubscribes\" and \"Cleaned Emails\".' mod='newsletterpro'}</p>
				<p style="margin-top: 0;">{l s='For unexpected behavior the \"Cleaned Emails\" feature is deactivated by default. The webhook features \"Unsubscribes\" and \"Cleaned Emails\" can be configured in the file \"%s\".' sprintf="config.ini" mod='newsletterpro'}</p>
			</div>

		</div>
	</form>
</div>