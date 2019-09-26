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
<div id="{$tab_id|escape:'html':'UTF-8'}" class="tab-settings" style="display: none;">
{else}
<script type="text/javascript"> 
	if(window.location.hash == '#settings') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" class="tab-settings" style="display: block;">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" class="tab-settings" style="display: none;">');
	{rdelim} 
</script>
{/if}
	<div class="settings">
		<h4>{l s='Settings' mod='newsletterpro'}</h4>
		<div class="separation"></div>

		{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" 
			title_name={l s='Display only subscribed emails' mod='newsletterpro'}
			description={l s='If this option is "yes", only users has subscribed at newsletter will be visible in "Send newsletters" tab, else all users will be visible.' mod='newsletterpro'}
			label_id='view_active_only' 
			label_name='' 
			input_onchange='NewsletterProControllers.SettingsController.viewActiveOnly( $(this) );'
			is_checked=$CONFIGURATION.VIEW_ACTIVE_ONLY
		}

		{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" 
			title_name={l s='Convert CSS to Inline Style' mod='newsletterpro'}
			description={l s='If this option is activated, the header style from the newsletter templates will be converted to inline style when you send a newsletter. This help the newsletter to be displayed properly into the client email.' mod='newsletterpro'}
			label_id='convert_css_to_inline_style' 
			label_name='' 
			input_onchange='NewsletterProControllers.SettingsController.convertCssToInlineStyle( $(this) );'
			is_checked=$CONFIGURATION.CONVERT_CSS_TO_INLINE_STYLE
		}

		{if $CONFIGURATION.PS_REWRITING_SETTINGS == 1}
			{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" 
				title_name={l s='Product Friendly URL' mod='newsletterpro'}
				description={l s='Enable only if your server allows URL rewriting. In some cases this option do not works properly ( disable is recommended ).' mod='newsletterpro'}
				label_id='product_friendly_url' 
				label_name='' 
				input_onchange='NewsletterProControllers.SettingsController.productFriendlyURL( $(this) );'
				is_checked=$CONFIGURATION.PRODUCT_LINK_REWRITE
			}
		{/if}

		{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" 
			title_name={l s='Display only active products' mod='newsletterpro'}
			description={l s='If the option is "yes" you will display only products that are active in the search list.' mod='newsletterpro'}
			label_id='display_only_active_products' 
			label_name='' 
			input_onchange='NewsletterProControllers.SettingsController.displayOnliActiveProducts( $(this) );'
			is_checked=$CONFIGURATION.ONLY_ACTIVE_PRODUCTS
		}

		{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" 
			title_name={l s='Customer Account Settings' mod='newsletterpro'}
			description={l s='Enable or disable the customer account newsletter pro settings.' mod='newsletterpro'}
			label_id='displya_my_account_np_settings' 
			label_name='' 
			input_onchange='NewsletterProControllers.SettingsController.displayCustomerAccountSettings( $(this) );'
			is_checked=$CONFIGURATION.DISPLYA_MY_ACCOUNT_NP_SETTINGS
		}

		{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" 
			title_name={l s='Subscribe by category' mod='newsletterpro'}
			description={l s='Allow each customer to subscribe by a category of interest. Then the employee can filter the customers by their categories of interests.' mod='newsletterpro'}
			label_id='subscribe_by_category' 
			label_name='' 
			input_onchange='NewsletterProControllers.SettingsController.subscribeByCategory( $(this) );'
			is_checked=$CONFIGURATION.SUBSCRIBE_BY_CATEGORY
		} 

		{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" 
			title_name={l s='Subscribe by List Of Interest' mod='newsletterpro'}
			description={l s='Allow each customer to subscribe by a category of interest. Then the employee can filter the customers by their list of interests.' mod='newsletterpro'}
			label_id='subscribe_by_c_list_of_interest' 
			label_name='' 
			input_onchange='NewsletterProControllers.SettingsController.subscribeByCListOfInterest( $(this) );'
			is_checked=$CONFIGURATION.CUSTOMER_SUBSCRIBE_BY_LOI
		} 

		{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" 
			title_name={l s='Send the last newsletter on subscribe' mod='newsletterpro'}
			description={l s='Send the last newsletter when a customer create a new account. If a newsletter is send more then 10 people then it becomes the last newsletter send.' mod='newsletterpro'}
			label_id='sendnewsletter_on_subscribe' 
			label_name='' 
			input_onchange='NewsletterProControllers.SettingsController.sendNewsletterOnSubscribe( $(this) );'
			is_checked=$CONFIGURATION.SEND_NEWSLETTER_ON_SUBSCRIBE
		} 

		{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" 
			title_name={l s='Forwarding Feature Active' mod='newsletterpro'}
			description={l s='This feature allow your customers to forward the newsletter to this friends. The friends can also forward the newsletter to other friends. Each recipient has a forward limit of %s friends. This option is available in the templates only with of use of the {forward} or {forward_link} variables.' sprintf=$fwd_limit mod='newsletterpro'}
			label_id='forwaring_on_subscribe' 
			label_name='' 
			input_onchange='NewsletterProControllers.SettingsController.forwardingFeatureActive( $(this) );'
			is_checked=$CONFIGURATION.FWD_FEATURE_ACTIVE
		} 

		{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" 
			title_name={l s='Embed Newsletter Images' mod='newsletterpro'}
			description={l s='Embed images in your newsletter. You can embed files from a URL if allow_url_fopen is on in php.ini.' mod='newsletterpro'}
			label_id='send_embeded_images' 
			label_name='' 
			input_onchange='NewsletterProControllers.SettingsController.sendEmbededImagesActive( $(this) );'
			is_checked=$CONFIGURATION.SEND_EMBEDED_IMAGES
		}

		<div class="form-group clearfix">
			<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Front Subscribe Feature Options' mod='newsletterpro'}</span></label>
			
			<div class="col-sm-9 table-bordered" style="border-left: none; border-right: none;">
				<div class="form-group clearfix">

					<div class="alert alert-info" style="margin-bottom: 0;">
						{l s='Here you can setup the subscription at the newsletter in the front office. This option allow you to collect more informations about your customers (first name, last name, language, birthday, gender).' mod='newsletterpro'}
					</div>

					<div class="form-group clearfix">
						<label class="control-label col-sm-3" style="padding-top: 14px;">{l s='Subscription by the module' mod='newsletterpro'}</label>
						<div class="col-sm-9">
							<div class="radio">
								<label for="newsletter-pro-subscribe-settings" class="control-label in-win">
									<input id="newsletter-pro-subscribe-settings" type="radio" name="newsletterproSubscriptionActive" onchange="NewsletterPro.modules.settings.newsletterproSubscriptionOption($(this));" value="1" {if $CONFIGURATION.SUBSCRIPTION_ACTIVE == 1} checked {/if}> 
									{l s='Newsletter Pro' mod='newsletterpro'}
								</label>
							</div>
							<div class="radio">
							 	<label for="block-newsletter-subscribe-settings" class="control-label in-win">
							 		<input id="block-newsletter-subscribe-settings" type="radio" name="newsletterproSubscriptionActive" onchange="NewsletterPro.modules.settings.newsletterproSubscriptionOption($(this));" value="0"  {if $CONFIGURATION.SUBSCRIPTION_ACTIVE == 0} checked {/if}>
							 		{l s='Block Newsletter (Prestashop Default)' mod='newsletterpro'}
							 	</label>
							</div>
						</div>
					</div>

					<div class="form-group clearfix">
						<div id="newsletter-pro-subscribe-options" style="{if $CONFIGURATION.SUBSCRIPTION_ACTIVE == 0} display:none; {/if}">
							{if count($subscribe_hooks) > 0}
								<div class="input-group clearfix">
									<p class="help-block">{l s='Check the hooks from the list in which you want to install the newsletter subscription option.' mod='newsletterpro'}</p>
								</div>
								{foreach $subscribe_hooks as $hook}
									<div class="clearfix">
										<div class="checkbox" style="margin-top: 0;">
											<label for="hook_{$hook.name|escape:'html':'UTF-8'}" class="control-label in-row radio-label">
												<input id="hook_{$hook.name|escape:'html':'UTF-8'}" class="checkbox-input" type="checkbox" name="hook_{$hook.name|escape:'html':'UTF-8'}" value="{$hook.name|escape:'html':'UTF-8'}" {if $hook.isRegistred == 1} checked {/if}> 
												{$hook.name|escape:'html':'UTF-8'}
											</label>
										</div>
									</div>
								{/foreach}
							{/if}
							<div class="clearfix">
								<p class="help-block">{l s='Don\'t forget to check, add and update the module front hooks position after you activate this option.'  mod='newsletterpro'}</p>
							</div>
							{if $blocknewsletter_info.isInstalled}
								<a href="javascript:{}" class="btn btn-default" onclick="NewsletterPro.modules.settings.importEmailsFromBlockNewsletter($(this));"><span class="btn-ajax-loader"></span><i class="icon icon-upload"></i> {l s='Import Email Addresses' mod='newsletterpro'}</a>
								<div class="clearfix">
									<p class="help-block">{l s='Import the email addresses from the Block Newsletter module to the Newsletter Pro Subscription feature.' mod='newsletterpro'}</p>
								</div>

								<div class="alert alert-info clearfix">
									<p style="margin-top: 0;" class="cron-link"><span style="color: black;">CRON URL:</span> <span class="icon-cron-link"></span>{$sync_newsletter_block|escape:'quotes':'UTF-8'}</p>
									<p style="margin-top: 0;">{l s='Synchronize all the users list before to setup the CRON job.' mod='newsletterpro'}</p>
								</div>
							{/if}

							{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" 
								title_name={l s='Email Confirmation on Subscribe (Secure Subscribe)' mod='newsletterpro'}
								description={l s='This option will send a confirmation email at, where the visitor will confirm the subscription.' mod='newsletterpro'}
								label_id='subscription_secure_subscribe' 
								label_name='' 
								input_onchange='NewsletterPro.modules.settings.subscriptionSecureSubscribe($(this));'
								is_checked=$CONFIGURATION.SUBSCRIPTION_SECURE_SUBSCRIBE
							} 
								
							<div class="clearfix">
								<a href="javascript:{}" class="btn btn-default" onclick="NewsletterPro.modules.settings.clearSubscribersTemp($(this));"><span class="btn-ajax-loader"></span><i class="icon icon-eraser"></i> {l s='Clear Emails' mod='newsletterpro'}</a>
							</div>
							<div class="clearfix">
								<p class="help-block">{l s='Clear the older emails that did not confirm the subscription at the newsletter.' mod='newsletterpro'}</p>
							</div>
						</div>

						<div class="clearfix">
							<div class="clearfix">
								<p class="help-block">{l s='You can\'t use the both methods. If you choose the option Newsletter Pro the Block Newsletter module will be disabled.'  mod='newsletterpro'}</p>
							</div>
							<a href="javascript:{}" class="btn btn-success" onclick="NewsletterPro.modules.settings.newsletterproSubscriptionActive();"><span class="btn-ajax-loader"></span> <i class="icon icon-save"></i> {l s='Save Subscription Settings' mod='newsletterpro'}</a>
							<div class="clearfix">
								<p class="help-block" style="margin-top: 5px;">{l s='Save the subscription settings. Press this button only if you change the subscription module option, or if you want to register the newsletterpro module to a new hook.'  mod='newsletterpro'}</p>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
		
		{if $isPS16}
		<div id="np-top-shortcuts" class="form-group">
			<label class="control-label col-sm-3" style="padding-top: 13px;">{l s='Top Shortcuts' mod='newsletterpro'}</label>
			<div class="col-sm-9">
				<div class="checkbox">
					<label class="control-label in-win">
						<input type="checkbox" value="CSV" {if $CONFIGURATION.PAGE_HEADER_TOOLBAR.CSV}checked{/if}>
						{l s='Import & Export' mod='newsletterpro'}
					</label>
				</div>

				<div class="checkbox">
					<label class="control-label in-win">
						<input type="checkbox" value="MANAGE_IMAGES" {if $CONFIGURATION.PAGE_HEADER_TOOLBAR.MANAGE_IMAGES}checked{/if}>
						{l s='Manage Images' mod='newsletterpro'}
					</label>
				</div>

				<div class="checkbox">
					<label class="control-label in-win">
						<input type="checkbox" value="SELECT_PRODUCTS" {if $CONFIGURATION.PAGE_HEADER_TOOLBAR.SELECT_PRODUCTS}checked{/if}>
						{l s='Select Products' mod='newsletterpro'}
					</label>
				</div>

				<div class="checkbox">
					<label class="control-label in-win">
						<input type="checkbox" value="CREATE_TEMPLATE" {if $CONFIGURATION.PAGE_HEADER_TOOLBAR.CREATE_TEMPLATE}checked{/if}>
						{l s='Create Template' mod='newsletterpro'}
					</label>
				</div>

				<div class="checkbox">
					<label class="control-label in-win">
						<input type="checkbox" value="SEND_NEWSLETTERS" {if $CONFIGURATION.PAGE_HEADER_TOOLBAR.SEND_NEWSLETTERS}checked{/if}>
						{l s='Send Newsletters' mod='newsletterpro'}
					</label>
				</div>

				<div class="checkbox">
					<label class="control-label in-win">
						<input type="checkbox" value="TASK" {if $CONFIGURATION.PAGE_HEADER_TOOLBAR.TASK}checked{/if}>
						{l s='Tasks' mod='newsletterpro'}
					</label>
				</div>

				<div class="checkbox">
					<label class="control-label in-win">
						<input type="checkbox" value="HISTORY" {if $CONFIGURATION.PAGE_HEADER_TOOLBAR.HISTORY}checked{/if}>
						{l s='History' mod='newsletterpro'}
					</label>
				</div>

				<div class="checkbox">
					<label class="control-label in-win">
						<input type="checkbox" value="STATISTICS" {if $CONFIGURATION.PAGE_HEADER_TOOLBAR.STATISTICS}checked{/if}>
						{l s='Statistics' mod='newsletterpro'}
					</label>
				</div>

				<div class="checkbox">
					<label class="control-label in-win">
						<input type="checkbox" value="CAMPAIGN" {if $CONFIGURATION.PAGE_HEADER_TOOLBAR.CAMPAIGN}checked{/if}>
						{l s='Campaign Statistics' mod='newsletterpro'}
					</label>
				</div>

				<div class="checkbox">
					<label class="control-label in-win">
						<input type="checkbox" value="SMTP" {if $CONFIGURATION.PAGE_HEADER_TOOLBAR.SMTP}checked{/if}>
						{l s='E-mail Configuration' mod='newsletterpro'}
					</label>
				</div>

				<div class="checkbox">
					<label class="control-label in-win">
						<input type="checkbox" value="MAILCHIMP" {if $CONFIGURATION.PAGE_HEADER_TOOLBAR.MAILCHIMP}checked{/if}>
						{l s='Mail Chimp' mod='newsletterpro'}
					</label>
				</div>

				<div class="checkbox">
					<label class="control-label in-win">
						<input type="checkbox" value="FORWARD" {if $CONFIGURATION.PAGE_HEADER_TOOLBAR.FORWARD}checked{/if}>
						{l s='Forwarders' mod='newsletterpro'}
					</label>
				</div>

				<div class="checkbox">
					<label class="control-label in-win">
						<input type="checkbox" value="FRONT_SUBSCRIPTION" {if $CONFIGURATION.PAGE_HEADER_TOOLBAR.FRONT_SUBSCRIPTION}checked{/if}>
						{l s='Front Subscription' mod='newsletterpro'}
					</label>
				</div>

				<div class="checkbox">
					<label class="control-label in-win">
						<input type="checkbox" value="SETTINGS" {if $CONFIGURATION.PAGE_HEADER_TOOLBAR.SETTINGS}checked{/if}>
						{l s='Settings' mod='newsletterpro'}
					</label>
				</div>

				<div class="checkbox">
					<label class="control-label in-win">
						<input type="checkbox" value="TUTORIALS" {if $CONFIGURATION.PAGE_HEADER_TOOLBAR.TUTORIALS}checked{/if}>
						{l s='Tutorials' mod='newsletterpro'}
					</label>
				</div>

			</div>
		</div>
		{/if}

		{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" 
			title_name={l s='Debug Mode' mod='newsletterpro'}
			description={l s='This option should be "No" in a production website. This option will enable/disable the smarty force compilation that can cause errors to the compilation process. This option will override the default force compilation option, only for the module pages.' mod='newsletterpro'}
			label_id='debug_mode' 
			label_name='' 
			input_onchange='NewsletterProControllers.SettingsController.debugMode( $(this) );'
			is_checked=$CONFIGURATION.DEBUG_MODE
		} 

		{if isset($clear_cache) && $clear_cache == true}
			<div class="form-group clearfix">
				<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Smarty' mod='newsletterpro'}</span></label>
				<div class="col-sm-9">
					<div class="clearfix">
						<a class="btn btn-default" href="{$controller_path|escape:'quotes':'UTF-8'}&recompileTemplates#settings"><span class="icon icon-eraser"></span> {l s='Clear Cache' mod='newsletterpro'}</a>
					</div>
					<p class="help-block">{l s='Press this button after you made an update to the module. This button will clear the shop cache.' mod='newsletterpro'}</p>
				</div>
			</div>
		{/if}

		{if !empty($log_files)}
			<div class="form-group clearfix">
				<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Logs' mod='newsletterpro'}</span></label>
				<div class="col-sm-9">
					<div class="clearfix">
						{foreach $log_files as $log}
							<a class="btn btn-default np-btn-open-log-file" href="{$log.path|escape:'quotes':'UTF-8'}" target="_blank" style="margin-top: 0; margin-right: 5px;">
								<span class="btn-ajax-loader"></span>
								<i class="icon icon-info"></i> {$log.name|escape:'html':'UTF-8'}
							</a>
						{/foreach}
						<a class="btn btn-default" href="javascript:{}" onclick="NewsletterPro.modules.settings.clearLogFiles($(this));" style="margin-top: 0; float: right;"><span class="btn-ajax-loader"></span> <span class="icon icon-eraser"></span> {l s='Clear Log' mod='newsletterpro'}</a>
					</div>
					<p class="help-block">{l s='View the log files.' mod='newsletterpro'}</p>
				</div>
			</div>
		{/if}

		<div class="form-group clearfix">
			<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Databse Backup' mod='newsletterpro'}</span></label>
			<div class="col-sm-9">
				<div class="clearfix">
					<a id="module-create-backup-button" class="btn btn-default" href="javascript:{}"><span class="btn-ajax-loader"></span> <span class="icon icon-database"></span> {l s='Create Backup' mod='newsletterpro'}</a>
					<a id="module-load-backup-button" class="btn btn-default" href="javascript:{}"><span class="btn-ajax-loader"></span> <span class="icon icon-database"></span> {l s='Restore' mod='newsletterpro'}</a>
				</div>
				<p class="help-block">{l s='Create a module database backup.' mod='newsletterpro'}</p>
			</div>
		</div>
	
		{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" 
			title_name={l s='Use Cache' mod='newsletterpro'}
			description={l s='Use Newsletter Pro cache.' mod='newsletterpro'}
			label_id='use_cache' 
			label_name='' 
			input_onchange='NewsletterProControllers.SettingsController.useCache( $(this) );'
			is_checked=$CONFIGURATION.USE_CACHE
		}

		<div class="form-group clearfix">
			<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Module Cache' mod='newsletterpro'}</span></label>
			<div class="col-sm-9">
				<div class="clearfix">
					<a id="pqnp-clear-module-cache" class="btn btn-default" href="javascript:{}"><span class="icon icon-eraser"></span> {l s='Clear Cache' mod='newsletterpro'}</a>
				</div>
				<p class="help-block">{l s='Clear the newsletter pro cache.' mod='newsletterpro'}</p>
			</div>
		</div>

	</div>
</div>