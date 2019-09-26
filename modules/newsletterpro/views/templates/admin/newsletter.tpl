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

<div id="newsletterpro" class="row">
	{include file="$tpl_location"|cat:"templates/admin/module_info.tpl"}
	
	{if isset($np_errors) && !empty($np_errors)}
	<div class="form-group clearfix">
		<div class="alert alert-danger" style="margin-bottom: 0;">
			{foreach $np_errors as $error}
				<div class="clearfix">{$error|escape:'quotes':'UTF-8'}</div>
			{/foreach}
		</div>
	</div>
	{/if}

	<div id="np-left-side" class="np-left-side {if isset($CONFIGURATION.LEFT_MENU_ACTIVE) && $CONFIGURATION.LEFT_MENU_ACTIVE == 0}col-sm-12{else}col-sm-2{/if} clearfix">
		<div id="tab" class="newsletter {if isset($CONFIGURATION.LEFT_MENU_ACTIVE) && $CONFIGURATION.LEFT_MENU_ACTIVE == 0}np-menu-top{/if}">
				{if $isPS16}
				<div class="menu-type">
					<span id="menu-toggle" class="menu-toggle"><i class="icon icon-bars" style="font-size: 18px;"></i> <span class="toggle-menu-text">{if isset($CONFIGURATION.LEFT_MENU_ACTIVE) && $CONFIGURATION.LEFT_MENU_ACTIVE == 0}{l s='Left Menu' mod='newsletterpro'}{else}{l s='Top Menu' mod='newsletterpro'}{/if}</span></span>
				</div>
				{/if}
				<a id="tab_newsletter_0" href="#csv" class="first_item"><span class="icon-import-csv"></span>{l s='Import & Export' mod='newsletterpro'}</a>
				<a id="tab_newsletter_1" href="#manageImages" class="item"><span class="icon-manage-iamges"></span>{l s='Manage Images' mod='newsletterpro'}</a>
				<a id="tab_newsletter_3" href="#selectProducts" class="item"><span class="icon-select-products"></span><span class="menu-text">{l s='Select Products' mod='newsletterpro'}</span> <span class="step">1</span></a>
				<a id="tab_newsletter_4" href="#createTemplate" class="item"><span class="icon-create-template"></span><span class="menu-text">{l s='Create Template' mod='newsletterpro'}</span> <span class="step">2</span></a>
				<a id="tab_newsletter_5" href="#sendNewsletters" class="item"><span class="icon-send-newsletters"></span><span class="menu-text">{l s='Send Newsletters' mod='newsletterpro'}</span> <span id="np-step-3" class="step">3</span></a>
				<a id="tab_newsletter_10" href="#task" class="item"><span class="icon-task"></span>{l s='Tasks' mod='newsletterpro'}</a>
				<a id="tab_newsletter_6" href="#history" class="item"><span class="icon-history-span"></span>{l s='History' mod='newsletterpro'}</a>
				<a id="tab_newsletter_11" href="#statistics" class="item"><span class="icon-statistics"></span>{l s='Statistics' mod='newsletterpro'}</a>
				<a id="tab_newsletter_8" href="#campaign" class="item"><span class="icon-campaign"></span>{l s='Campaign Statistics' mod='newsletterpro'}</a>
				<a id="tab_newsletter_9" href="#smtp" class="item"><span class="icon-smtp"></span>{l s='E-mail Configuration' mod='newsletterpro'}</a>
				<a id="tab_newsletter_13" href="#mailchimp" class="item"><span class="icon-mailchimp"></span>{l s='Mail Chimp' mod='newsletterpro'}</a>
				<a id="tab_newsletter_14" href="#forward" class="item"><span class="icon-fwd"></span>{l s='Forwarders' mod='newsletterpro'}</a>
				<a id="tab_newsletter_15" href="#frontSubscription" class="item"><span class="icon-f-subscription"></span>{l s='Front Subscription' mod='newsletterpro'} <span id="fs-vouchers-alert" class="vouchers-alert"></span></a>
				<a id="tab_newsletter_7" href="#settings" class="item"><span class="icon-settings"></span>{l s='Settings' mod='newsletterpro'}</a>
				<a id="tab_newsletter_2" href="#tutorials" class="item"><span class="icon-tutorials"></span>{l s='Tutorials' mod='newsletterpro'}</a>
				<a id="tab_newsletter_12" href="#ourModules" class="last_item count-new-parent"><span class="icon-puzzle"></span><span class="np-menu-text-ourmodules">{l s='Our Modules' mod='newsletterpro'}</span></a>
		</div>
	</div> <!-- end of col-log-2  -->

 	<div id="np-right-side" class="np-right-side {if isset($CONFIGURATION.LEFT_MENU_ACTIVE) && $CONFIGURATION.LEFT_MENU_ACTIVE == 0}col-sm-12{else}col-sm-10{/if}">
		<div id="tab_content" class="newsletter clearfix {if isset($CONFIGURATION.LEFT_MENU_ACTIVE) && $CONFIGURATION.LEFT_MENU_ACTIVE == 0}np-menu-top-content{/if}" style="position: relative;">
			<div id="tab_content_loading" class="tab-content-loading">
				<div class="tab-content-loading-content">
					<i class="icon icon-refresh icon-spin"></i> {l s='loading...' mod='newsletterpro'}
				</div>
				<div class="tab-content-loading-bg"></div>
			</div>

			{include file="$tpl_location"|cat:"templates/admin/tabs/csv.tpl" tab_id="tab_newsletter_content_0"}
			{include file="$tpl_location"|cat:"templates/admin/tabs/manage_images.tpl" tab_id="tab_newsletter_content_1"}
			{include file="$tpl_location"|cat:"templates/admin/tabs/select_products.tpl" tab_id="tab_newsletter_content_3"}
			{include file="$tpl_location"|cat:"templates/admin/tabs/create_template.tpl" tab_id="tab_newsletter_content_4"}
			{include file="$tpl_location"|cat:"templates/admin/tabs/send_newsletters.tpl" tab_id="tab_newsletter_content_5"}
			{include file="$tpl_location"|cat:"templates/admin/tabs/history.tpl" tab_id="tab_newsletter_content_6"}
			{include file="$tpl_location"|cat:"templates/admin/tabs/statistics.tpl" tab_id="tab_newsletter_content_11"}
			{include file="$tpl_location"|cat:"templates/admin/tabs/settings.tpl" tab_id="tab_newsletter_content_7"}
			{include file="$tpl_location"|cat:"templates/admin/tabs/campaign.tpl" tab_id="tab_newsletter_content_8"}
			{include file="$tpl_location"|cat:"templates/admin/tabs/smtp.tpl" tab_id="tab_newsletter_content_9"}
			{include file="$tpl_location"|cat:"templates/admin/tabs/task.tpl" tab_id="tab_newsletter_content_10"}
			{include file="$tpl_location"|cat:"templates/admin/tabs/tutorials.tpl" tab_id="tab_newsletter_content_2"}
			{include file="$tpl_location"|cat:"templates/admin/tabs/mailchimp.tpl" tab_id="tab_newsletter_content_13"}
			{include file="$tpl_location"|cat:"templates/admin/tabs/forward.tpl" tab_id="tab_newsletter_content_14"}
			
			{* HTML CONTENT *}
			{$CONTROLLER_FRONT_SUBSCRIPTION|strval}

			{include file="$tpl_location"|cat:"templates/admin/tabs/our_modules.tpl" tab_id="tab_newsletter_content_12"}
		</div>
		<div class="clear" style="clear: both;"></div>
	</div>

	<div class="clear" style="clear: both;"></div>
</div>

{include file="$tpl_location"|cat:"templates/admin/javascript/js_settings.tpl"}

{include file="$tpl_location"|cat:"templates/admin/task/template.tpl"}
{include file="$tpl_location"|cat:"templates/admin/tiny_init.tpl"}
{include file="$tpl_location"|cat:"templates/admin/javascript/js_translations.tpl"}