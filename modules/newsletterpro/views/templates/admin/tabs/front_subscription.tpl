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

{capture name="voucher"}
<input id="subscription-template-voucher" class="form-control form-control fixed-width-xxl subscription-template-voucher" type="text" value="{$subscription_template.voucher|escape:'quotes':'UTF-8'}" name="subscriptionVoucher">
{/capture}
{capture name="voucher_description"}
{l s='Leave this empty if there is not voucher. If it\'s empty the voucher message will not be displayed.' mod='newsletterpro'}
{/capture}

{if isset($fix_document_write) && $fix_document_write == 1}
<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;" class="front-subscription">
{else}
<script type="text/javascript"> 
	if(window.location.hash == '#frontSubscription') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: block;" class="front-subscription">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;" class="front-subscription">');
	{rdelim} 
</script>
{/if}

	<div class="clear">&nbsp;</div>

	<div class="title-box clearfix">
		<h4 class="title pull-left">{l s='Front Subscription Configuration' mod='newsletterpro'}</h4>
		<a href="javascript:{}" id="np-create-custom-field" class="btn btn-default pull-right">
			<i class="icon icon-plus-square"></i>
			{l s='Create Custom Fields' mod='newsletterpro'}
		</a>
		<div class="separation"></div>
	</div>

	{if $CONFIGURATION.SUBSCRIPTION_ACTIVE == 0}
	<div style="margin-bottom: 5px;" class="alert alert-warning clearfix">
		{l s='The front subscription feature is not activated. Go to the settings tab to activate it.' mod='newsletterpro'}
		<div class="clear"></div>
	</div>
	{/if}

	<div class="form-group clearfix content-box">
		<div class="form-group clearfix">
			<div class="form-inline">
				<div class="clearfix">
					<h4 class="pull-left">{l s='Templates List' mod='newsletterpro'}</h4>
					<a id="subscription_template_help" href="javascript:{}" class="btn btn-default pull-right  btn-margin subscription-template-help" onclick="NewsletterPro.modules.frontSubscription.showSubscriptionHelp();"><i class="icon icon-eye"></i> {l s='View available variables' mod='newsletterpro'}</a>
					<a id="load-subscription-template-backup" href="javascript:{}"  class="btn btn-default pull-right btn-margin"><i class="icon icon-database"></i> {l s='Restore' mod='newsletterpro'}</a>
					<a id="create-subscription-template-backup" href="javascript:{}" class="btn btn-default pull-right btn-margin"><span class="btn-ajax-loader"></span> <i class="icon icon-database"></i> {l s='Create backup' mod='newsletterpro'}</a>
				</div>
				<div class="separation"></div>
			</div>
		</div>

		<table id="subscription-templates-table" class="table table-bordered subscription-templates-table">
			<thead>
				<tr>
					<th class="name" data-field="name">{l s='Name' mod='newsletterpro'}</th>
					<th class="date_add" data-field="date_add">{l s='Date Add' mod='newsletterpro'}</th>
					<th class="display_gender" data-field="display_gender">{l s='Gender' mod='newsletterpro'}</th>
					<th class="display_firstname" data-field="display_firstname">{l s='First Name' mod='newsletterpro'}</th>
					<th class="display_lastname" data-field="display_lastname">{l s='Last Name' mod='newsletterpro'}</th>
					<th class="display_language" data-field="display_language">{l s='Language' mod='newsletterpro'}</th>
					<th class="display_birthday" data-field="display_birthday">{l s='Birthday' mod='newsletterpro'}</th>
					<th class="display_list_of_interest" data-field="display_list_of_interest">{l s='List Of Interest' mod='newsletterpro'}</th>
					<th class="list_of_interest_type" data-field="list_of_interest_type">{l s='List Type Checkbox' mod='newsletterpro'}</th>
					<th class="np-active" data-field="active">{l s='Active' mod='newsletterpro'}</th>
					<th class="actions" data-template="actions">{l s='Actions' mod='newsletterpro'}</th>
				</tr>
			</thead>
		</table>
	</div>

	<div class="form-group clearfix content-box">
		<div class="form-group clearfix content-box">
			<h4>{l s='Subscription Template' mod='newsletterpro'}</h4>
			<div class="separation"></div>
		</div>

		<div id="subscription-template-box" class="subscription-template-box">
			<div>
				<div id="tab_subscription_template" class="tab-subacription-template" style="float: left;">
					<a id="tab_subscription_template-template_0" class="btn btn-default first_item">
						<i class="icon icon-edit"></i> {l s='Edit Template' mod='newsletterpro'}
					</a>
					<a id="tab_subscription_template-template_6" class="btn btn-default item">
						<i class="icon icon-edit"></i> {l s='Edit Messages' mod='newsletterpro'}
					</a>

					{if $load_subscription_hook_header == true}
						<a id="tab_subscription_template-template_1" class="btn btn-default item">
							<i class="icon icon-eye"></i> {l s='View' mod='newsletterpro'}
						</a>
					{/if}

					<a id="tab_subscription_template-template_2" class="btn btn-default item">
						<i class="icon icon-code"></i> {l s='CSS Style' mod='newsletterpro'}
					</a>
					<a id="tab_subscription_template-template_4" class="btn btn-default item">
						<i class="icon icon-code"></i> {l s='CSS Style (Global)' mod='newsletterpro'}
					</a>
					<a id="tab_subscription_template-template_3" class="btn btn-default item">
						<i class="icon icon-gear"></i> {l s='Template Settings' mod='newsletterpro'}
					</a>
					<a id="tab_subscription_template-template_7" class="btn btn-default item">
						<i class="icon icon-gear"></i> {l s='Popup Settings' mod='newsletterpro'}
					</a>
					<a id="tab_subscription_template-template_5" class="btn btn-default item">
						<i class="icon icon-info-circle"></i> {l s='Info' mod='newsletterpro'}
					</a>
				</div>

				<div class="language-box" style="float: right;">
					<div id="subscription-template-language" class="gk_lang_select"></div>
				</div>
				<div class="clear">&nbsp;</div>
			</div>
			<div id="tab_subscription_template_content" class="tab-subacription-template-content">
				<div id="tab_subscription_template_content-template_0">
					{include file="$textarea_tpl_multilang" class_name='subscription_rte' content_name='subscription_content' input_name="subscription_template" input_value=$subscription_template.content content_css=$subscription_template.css_links init_callback='NewsletterPro.modules.frontSubscription.initTinyCallback'}
				</div>
	
				{if $load_subscription_hook_header == true}
					<div id="tab_subscription_template_content-template_1" style="display: none;">
						<div class="view-content">
						 	<iframe id="subscription-template-view" style="display: block; vertical-align: top;" scrolling="no" src="{$subscription_template.view|escape:'quotes':'UTF-8'}" class="view-newsletter-template-content"> </iframe>
							<div class="clear"></div>
						</div>
					</div>
				{/if}

				<div id="tab_subscription_template_content-template_2" style="display: none;">
					<div class="settings-tab">
						<div class="clearfix">
							<h4>{l s='Setup the CSS for this template' mod='newsletterpro'}</h4>
							<div class="alert alert-info">
								{l s='Use the class ".np-front-subscription" in front of the selection. If you don\'t respect this, the global style of the website will be affected.' mod='newsletterpro'}
							</div>
						</div>
						<textarea id="subscription-template-css" class="template-css" style="box-sizing: border-box; width: 100%;" wrap="off" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">{$subscription_template.css_style|escape:'html':'UTF-8'}</textarea>
					</div>
				</div>

				<div id="tab_subscription_template_content-template_4" style="display: none;">
					<div class="settings-tab clearfix">
						<div class="clearfix">
							<h4>{l s='Setup the global CSS for all the templates' mod='newsletterpro'}</h4>
							<div class="alert alert-info">
								{l s='This style is applied to all the templates.' mod='newsletterpro'} {l s='Use the class ".np-front-subscription" in front of the selection. If you don\'t respect this, the global style of the website will be affected.' mod='newsletterpro'}
							</div>
						</div>
						<textarea id="subscription-template-css-global" class="template-css" style="box-sizing: border-box; width: 100%;" wrap="off" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">{$subscription_template.css_style_global|escape:'html':'UTF-8'}</textarea>
					</div>
				</div>

				<div id="tab_subscription_template_content-template_3" style="display: none;">
					<div class="settings-tab clearfix">
						<div class="clearfix">
							<h4>{l s='Template Settings' mod='newsletterpro'}</h4>
						</div>

						<div class="slider-box clearfix">
							<label class="control-label col-sm-3" style="padding-top: 27px;"><span class="label-tooltip">{l s='Template Width' mod='newsletterpro'}</span></label>
							<div class="col-sm-9">
								<div class="col-sm-9">
									<div id="slider-fs-template-width"></div>
								</div>
								<div class="col-sm-3">
									<div class="radio">
										<label for="radio-fs-percent" class="in-win control-label">
											<input id="radio-fs-percent" type="radio" value="0" name="sliderFsTemplateWidth">
											{l s='percent' mod='newsletterpro'}
										</label>
									</div>
									
									<div class="radio">
										<label for="radio-fs-pixels" class="in-win control-label">
											<input id="radio-fs-pixels" type="radio" value="1" name="sliderFsTemplateWidth">
											{l s='pixels' mod='newsletterpro'}
										</label>
									</div>
								</div>
							</div>

						</div>

						<div class="slider-box clearfix">
							<label class="control-label col-sm-3" style="padding-top: 27px;"><span class="label-tooltip">{l s='Template Max-Min Width' mod='newsletterpro'}</span></label>
							<div class="col-sm-9">
								<div class="col-sm-9">
									<div id="slider-fs-template-maxmin-width"></div>
								</div>
								<div class="col-sm-3">
									<label class="in-win control-label" style="padding-top: 25px;">{l s='pixels' mod='newsletterpro'}</label>
								</div>
							</div>

						</div>

						<div class="slider-box clearfix">
							<label class="control-label col-sm-3" style="padding-top: 27px;"><span class="label-tooltip">{l s='Template Top' mod='newsletterpro'}</label>
							<div class="col-sm-9">
								<div class="col-sm-9">
									<div id="slider-fs-template-top"></div>
								</div>

								<div class="col-sm-3">
									<label class="in-win control-label" style="padding-top: 25px;">{l s='pixels' mod='newsletterpro'}</label>
								</div>
							</div>

						</div>

						{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" 
							title_name={l s='Show Message After Subscribe' mod='newsletterpro'}
							description={l s='Show a message after a visitor has subscribed to the newsletter in a new windows. If this option is \"No\" the message will be also displayed but in the same window. Empty the message if you want to display the standard message.' mod='newsletterpro'}
							label_id='display_subscribe_message' 
							label_name='displaySubscribeMessage' 
							input_onchange="" 
							is_checked=$subscription_template.display_subscribe_message}

						<div class="form-group clearfix">
							<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Voucher' mod='newsletterpro'}</span></label>
							<div class="col-sm-9">
								<div class="clearfix">
									{$smarty.capture.voucher|escape:'quotes':'UTF-8'}
								</div>
								<p class="help-block">{$smarty.capture.voucher_description|escape:'html':'UTF-8'}</p>
							</div>
						</div>

						<div class="form-group clearfix">
							<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Terms and conditions url' mod='newsletterpro'}</span></label>
							<div class="col-sm-9">
								<div class="clearfix">
									<input type="text" id="np-terms-and-conditions" name="np_terms_and_conditions" class="form-control fixed-width-xxl" value="{$subscription_template.terms_and_conditions_url|escape:'quotes':'UTF-8'}">
								</div>
								<p class="help-block">{l s='Insert the terms and conditions url.' mod='newsletterpro'}</p>
							</div>
						</div>

						{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" title_name={l s='Display Gender' mod='newsletterpro'} description={l s='Display gender options in the front office subscription.' mod='newsletterpro'} label_id='display_gender' label_name='displayGender' input_onchange="" is_checked=$subscription_template.display_gender}
						{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" title_name={l s='Display First Name' mod='newsletterpro'} description={l s='Display the First Name input field in the front office subscription.' mod='newsletterpro'} label_id='display_firstname' label_name='displayFirstName' input_onchange="" is_checked=$subscription_template.display_firstname}
						{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" title_name={l s='Display Last Name' mod='newsletterpro'} description={l s='Display the Last Name input field in the front office subscription.' mod='newsletterpro'} label_id='display_lastname' label_name='displayLastName' input_onchange="" is_checked=$subscription_template.display_lastname}
						{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" title_name={l s='Display Language' mod='newsletterpro'} description={l s='Display language options in the front office subscription.' mod='newsletterpro'} label_id='display_language' label_name='displayLanguage' input_onchange="" is_checked=$subscription_template.display_language}
						{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" title_name={l s='Display Birthday' mod='newsletterpro'} description={l s='Display birthday options in the front office subscription.' mod='newsletterpro'} label_id='display_birthday' label_name='displayBirthday' input_onchange="" is_checked=$subscription_template.display_birthday}
						{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" title_name={l s='Display List Of Interest' mod='newsletterpro'} description={l s='Display list of interest options in the front office subscription.' mod='newsletterpro'} label_id='display_list_of_interest' label_name='displayListOfInterest' input_onchange="" is_checked=$subscription_template.display_list_of_interest}
						{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" title_name={l s='Display List Of Interest Type Checkbox' mod='newsletterpro'} description={l s='Display the list of interest as a checkbox type or as a selected option type.' mod='newsletterpro'} label_id='list_of_interest_type' label_name='listOfInterestType' input_onchange="" is_checked=$subscription_template.list_of_interest_type}
						{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" title_name={l s='Multiple time subscription' mod='newsletterpro'} description={l s='Allow the users to subscribe multiple times at the newsletter with the save email address.' mod='newsletterpro'} label_id='allow_multiple_time_subscription' label_name='allowMultipleTimeSubscription' input_onchange="" is_checked=$subscription_template.allow_multiple_time_subscription}
						<div id="activate-template-box" class="activate-template-box" style="{if $subscription_template.active}display: none;{else}display: block;{/if}">
							<a href="javascript:{}" id="activate-template" class="btn btn-default"><span class="icon incon-power-off"></span>{l s='Activate Template' mod='newsletterpro'}</a>
						</div>
						
						<div class="form-group clearfix">
							<label class="control-label col-sm-3" style="padding-top: 13px;">{l s='Mandatory Fields' mod='newsletterpro'}</label>
							<div class="col-sm-9">
								<div class="checkbox">
									<label class="control-label in-win">
										<input type="checkbox" name="newsletter_pro_subscription_mandatory_firstname">
										{l s='First Name' mod='newsletterpro'}
									</label>
								</div>

								<div class="checkbox">
									<label class="control-label in-win">
										<input type="checkbox" name="newsletter_pro_subscription_mandatory_lastname">
										{l s='Last Name' mod='newsletterpro'}
									</label>
								</div>
							</div>
						</div>
						
						<div class="col-sm-9 col-sm-offset-3">
							<div class="clearfix">
								<h4>{l s='Global Configuration' mod='newsletterpro'}</h4>
							</div>
						</div>

						<div class="form-group clearfix">
							<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Cross Type' mod='newsletterpro'}</span></label>
							<div class="col-sm-9">
								<div class="np-subscription-cross clearfix">
									<span id="np-cross" class="np-icon-cross"></span>
									<span id="np-cross1" class="np-icon-cross_1"></span>
									<span id="np-cross2" class="np-icon-cross_2"></span>
									<span id="np-cross3" class="np-icon-cross_3"></span>
									<span id="np-cross4" class="np-icon-cross_4"></span>
									<span id="np-cross5" class="np-icon-cross_5"></span>
								</div>
								<p class="help-block">{l s='Choose the popup corss type.' mod='newsletterpro'}</p>
							</div>
						</div>
						
					</div>
				</div>

				<div id="tab_subscription_template_content-template_5" style="display: none;">
					<div class="settings-tab">
						<div class="clearfix">
							<h4>{l s='Informations' mod='newsletterpro'}</h4>
						</div>

						<div class="clearfix">
							<div class="alert alert-info">
								<p>{l s='For the "email confirmation on subscribe option" or if you want to import emails addresses from the default prestashop subscription module visit the settings tab.' mod='newsletterpro'}</p>
								<p>{l s='The template input attribute class and name are very important, don\'t change them in order the subscription to work properly.' mod='newsletterpro'}</p>
								<p>{l s='View the available variabes by clicking ' mod='newsletterpro'} <a id="subscription_template_help" href="javascript:{}" style="color: #49B2FF;" onclick="NewsletterPro.modules.frontSubscription.showSubscriptionHelp();">{l s='here' mod='newsletterpro'}</a>. </p>
							</div>

							<div class="form-group clearfix">
								<div>
									<h4 style="margin-bottom: 0; border: none;">{l s='Front Subscription Popup Links by Language' mod='newsletterpro'}</h4>
								</div>
								<table class="table front-view-links">
									<thead>
										<tr>
											<th>{l s='Language Name' mod='newsletterpro'}</th>
											<th>{l s='Link' mod='newsletterpro'}</th>
										</tr>
									</thead>
									<tbody>
									{foreach $subscription_template_view_in_front_lang as $result}			
										<tr>
											<td>{$result.name|escape:'html':'UTF-8'}</td> 
											<td>{$result.link|escape:'quotes':'UTF-8'}</td>
										</tr>
									{/foreach}
									</tbody>
								</table>
							</div>

							<div class="alert alert-info">
								{l s='The subscription link can is available in the newsletter template by using the variable %s or %s.' sprintf=['{front_subscription}','{front_subscription_link}'] mod='newsletterpro'}
							</div>
						</div>
					</div>
				</div>

				<div id="tab_subscription_template_content-template_6" style="display: none;">
					<div class="settings-tab">
						<div class="clearfix">
							<h4>{l s='After Subscribe Success Message' mod='newsletterpro'}</h4>
						</div>
						<div class="alert alert-info">
							{l s='Don\'t forget to check if you want to display this message in a new window or not from "Template Settings" tab. Empty the message if you want to display a standard message.'  mod='newsletterpro'}
						</div>

						<div class="form-group clearfix">
							{include file="$textarea_tpl_multilang" class_name='subscription_rte_sm' content_name='content_name_s_subscribe_message' input_name="s_subscribe_message" input_value=$subscription_template.subscribe_message content_css=$subscription_template.css_links init_callback='NewsletterPro.modules.frontSubscription.initTinyCallback'}
						</div>

						<div class="clearfix">
							<h4>{l s='Email Subscribe Voucher Message' mod='newsletterpro'}</h4>
						</div>

						<div class="form-group clearfix">
							<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Voucher' mod='newsletterpro'}</span></label> 
							<div class="col-sm-9">
								{$smarty.capture.voucher|escape:'quotes':'UTF-8'}

							</div>
						</div>		

						<div class="alert alert-info clearfix">
							{$smarty.capture.voucher_description|escape:'html':'UTF-8'}
							{l s='The content style is important to be setup as inline style, because this template will be shown in the the subscriber email account.' mod='newsletterpro'}
						</div>

						<div class="form-group clearfix">
							{include file="$textarea_tpl_multilang" class_name='subscription_rte_esvm' content_name='content_name_s_email_subscribe_voucher_message' input_name="s_email_subscribe_voucher_message" input_value=$subscription_template.email_subscribe_voucher_message init_callback='NewsletterPro.modules.frontSubscription.initTinyCallback' plugins="fullpage"}
						</div>

						<div class="clearfix">
							<h4>{l s='Email Subscribe Confirmation Message' mod='newsletterpro'}</h4>
						</div>

						<div class="alert alert-info">
							<p>{l s='The email subscribe confirmation message option is global for all the templates and only the template messages are different. The option can be changes from the settings tab.' mod='newsletterpro'}</p>
							<p>{l s='The content style is important to be setup as inline style, because this template will be shown in the the subscriber email account.' mod='newsletterpro'}</p>
							<p>{l s='Additional variables on this templates are: %s and %s.' sprintf=['{email_confirmation}','{email_confirmation_link}'] mod='newsletterpro'}</p>
						</div>

						<div class="form-group clearfix">
							{include file="$textarea_tpl_multilang" class_name='subscription_rte_escm' content_name='content_name_s_email_subscribe_confirmation_message' input_name="s_email_subscribe_confirmation_message" input_value=$subscription_template.email_subscribe_confirmation_message init_callback='NewsletterPro.modules.frontSubscription.initTinyCallback' plugins="fullpage"}
						</div>
					</div>
				</div>

				<div id="tab_subscription_template_content-template_7" style="display: none;">
					<div class="clearfix settings-tab">
						<div class="clearfix">
							<h4>{l s='Auto popup settings' mod='newsletterpro'}</h4>
						</div>

						<div class="form-group clearfix">
							<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Show on Pages' mod='newsletterpro'}</span></label>
							<div class="col-sm-9">
								<div class="clearfix">
									<select id="show-on-pages" name="show_on_pages" class="fixed-width-xxl gk-select">
										{foreach $show_on_pages as $value => $title}
											<option value="{$value|escape:'html':'UTF-8'}" {if $subscription_template.show_on_pages == $value}selected="selected"{/if}> - {$title|escape:'html':'UTF-8'} - </option>
										{/foreach}
										{foreach $front_pages as $page}
											<option value="{$page.value|escape:'html':'UTF-8'}" {if $subscription_template.show_on_pages == $page.value}selected="selected"{/if}>{$page.title|escape:'html':'UTF-8'}</option>
										{/foreach}
									</select>
								</div>
								<p class="help-block">{l s='Set none if you want to disable the auto popup. The popup will be availabe when the user will click on the subscription button.' mod='newsletterpro'}</p>
							</div>
						</div>

						<div class="form-group clearfix">
							<label class="control-label col-sm-3"><span class="label-tooltip">{l s='When to show popup to user' mod='newsletterpro'}</span></label>
							<div class="col-sm-9">
								<div class="clearfix">
									<select id="when-shop-popup" name="when_shop_popup" class="fixed-width-xxl gk-select">
										{foreach $when_to_show as $key => $value}
										<option value="{$key|escape:'html':'UTF-8'}" {if $subscription_template.when_to_show|intval == $key}selected="selected"{/if}>{$value|escape:'html':'UTF-8'}</option>
										{/foreach}
									</select>
								</div>
							</div>
						</div>

						<div class="form-group clearfix">
							<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Start Timer' mod='newsletterpro'}</span></label>
							<div class="col-sm-9">
								<div class="clearfix">
									<input id="start-timer" type="text" name="start_timer" class="fixed-width-xxl gk-input" value="{$subscription_template.start_timer|intval}">
								</div>
								<p class="help-block">{l s='After how many seconds the popup will be shown. Set 0 to start immediately.' mod='newsletterpro'}</p>								
							</div>
						</div>

						<div class="form-group clearfix">
							<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Cookie Lifetime (in days)' mod='newsletterpro'}</label>
							<div class="col-sm-9">
								<div class="clearfix">
									<input id="cookie-lifetime" type="text" name="cookie_lifetime" class="fixed-width-xxl gk-input" value="{$subscription_template.cookie_lifetime|escape:'html':'UTF-8'}" style="float: left;"> 
									<label class="control-label in-win pull-left seconds-text" style="margin-left: 10px;">
										<span id="cookie-lifetime-seconds"> {math equation="round(time * 60 * 60 * 24)" time=$subscription_template.cookie_lifetime}</span> {l s='seconds' mod='newsletterpro'}
									</label>
								</div>
								<p class="help-block">{l s='How long should be cookie started (in days). (0 = when your browser closes). This field also accept math if you want a cookie that is less at one day. (1/24/60/60 * 5 = 5 seconds)' mod='newsletterpro'}</p>								
							</div>
						</div>

					</div>
				</div>
			</div>

		</div>
		<div class="clear"></div>
	</div>

	<div class="form-group clearfix content-box">
		<div class="form-inline">
			<div class="col-sm-4">
				<div id="save-subscription-template-message" class="save-subscription-template-message" style="display: none; float: left;">&nbsp;</div>
			</div>
			<div class="col-sm-8">
				<a id="save-subscription-template" href="javascript:{}" class="btn btn-default btn-margin pull-right">
					<i class="icon icon-save"></i> <span>{l s='Save' mod='newsletterpro'}</span>
				</a>
				<a id="save-as-subscription-template" href="javascript:{}" class="btn btn-default btn-margin pull-right">
					<i class="icon icon-save"></i> <span>{l s='Save As' mod='newsletterpro'}</span>
				</a>
				{if $load_subscription_hook_header == true}
					<a id="subscription-view-in-a-new-window" href="{$subscription_template.view|escape:'quotes':'UTF-8'}" target="_blank" class="btn btn-default btn-margin pull-right">
						<i class="icon icon-eye"></i> <span>{l s='View in a New Window' mod='newsletterpro'}</span>
					</a>
				{/if}
				<a href="{$subscription_template_view_in_front|escape:'quotes':'UTF-8'}" target="_blank" class="btn btn-default btn-margin pull-right">
					<i class="icon icon-eye"></i> <span>{l s='View in Front' mod='newsletterpro'}</span>
				</a>
			</div>
		</div>
	</div>

	<div class="from-group clearfix content-box"> 
		<div class="form-group clearfix">
			<div class="clearfix">
				<h4 class="pull-left">{l s='Manage List of Interest' mod='newsletterpro'}</h4>
				<div class="language-box">
					<div id="front-subscription-lang" class="front-subscription-lang"> </div>
				</div>
			</div>
			<div class="separation"></div>
		</div>

		<table id="list-of-interest-table" class="table table-bordered list-of-interest-table">
			<thead>
				<tr>
					<th class="name" data-field="name">{l s='Name' mod='newsletterpro'}</th>
					<th class="np-active" data-field="active">{l s='Active' mod='newsletterpro'}</th>
					<th class="position" data-field="position">{l s='Position' mod='newsletterpro'}</th>
					<th class="actions" data-template="actions">{l s='Actions' mod='newsletterpro'}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

