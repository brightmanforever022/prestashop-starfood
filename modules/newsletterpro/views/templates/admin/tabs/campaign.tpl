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
<div id="{$tab_id|escape:'html':'UTF-8'}" class="tab-campaign" style="display: none;">
{else}
<script type="text/javascript"> 
	if(window.location.hash == '#campaign') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" class="tab-campaign" style="display: block;">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" class="tab-campaign" style="display: none;">');
	{rdelim} 
</script>
{/if}
	<h4>{l s='Campaign Statistics' mod='newsletterpro'}</h4>
	<div class="separation"></div>

	<div class="form-group clearfix">
		<form id="campaignForm" method="POST" action="{$page_link|escape:'quotes':'UTF-8'}#campaign">
			<div class="form-group clearfix">
				<div class="col-sm-9 col-sm-offset-3">
					<div class="checkbox">
						<label for="set-universal-analytics" class="in-win control-label">
							<input class="smtp-checkbox" id="set-universal-analytics" type="checkbox"  onclick="NewsletterProControllers.SettingsController.universalAnaliytics( $(this) );" {if $CONFIGURATION.GOOGLE_UNIVERSAL_ANALYTICS_ACTIVE == 1}checked="checked"{/if}>
							{l s='Universal Analytics' mod='newsletterpro'}
						</label>
					</div>
					<p class="help-block" style="margin-top: 10px; width: 100%;">
						{l s='Connect to your Google Analytics account and check if the Universal Analytics is activated. If the answer is yes then check this option. Also if you use this option and you have installed the ganalytics module please check if it is compatible with the Google Universal Analytics.' mod='newsletterpro'}
					</p>
				</div>
			</div>

			<div class="form-group clearfix">
				<div class="col-sm-9 col-sm-offset-3">
					<div class="checkbox">
						<label for="set-campaign" class="in-win control-label">
							<input class="smtp-checkbox" id="set-campaign" type="checkbox"  onclick="NewsletterProControllers.SettingsController.activeCampaign( $(this) );" {if $CONFIGURATION.CAMPAIGN_ACTIVE == 1}checked="checked"{/if}>
							{l s='Activate Campaign' mod='newsletterpro'}
						</label>
					</div>
					<p class="help-block" style="margin-top: 10px; width: 100%;">
						{l s='This campaign works with Google Analytics, so the Google Analytics must to run in to your website.' mod='newsletterpro'}
					</p>
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Source' mod='newsletterpro'}</span></label>
				<div class="col-sm-9">
					<input class="form-control fixed-width-xxl" type="text" id="utm_source" name="utm_source" value="{$CONFIGURATION.CAMPAIGN.UTM_SOURCE|escape:'htmlall':'UTF-8'}" {if $CONFIGURATION.CAMPAIGN_ACTIVE == 0}disabled="disabled"{/if}>
				</div>
			</div>
		
			<div class="form-group clearfix">
				<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Medium' mod='newsletterpro'}</span></label>
				<div class="col-sm-9">
					<input class="form-control fixed-width-xxl" type="text" id="utm_medium" name="utm_medium" value="{$CONFIGURATION.CAMPAIGN.UTM_MEDIUM|escape:'htmlall':'UTF-8'}" {if $CONFIGURATION.CAMPAIGN_ACTIVE == 0}disabled="disabled"{/if}>
				</div>
			</div>

			<div class="form-group clearfix">
				<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Campaign Name' mod='newsletterpro'}</span></label>
				<div class="col-sm-9">
					<input class="form-control fixed-width-xxl" type="text" id="utm_campaign" name="utm_campaign" value="{$CONFIGURATION.CAMPAIGN.UTM_CAMPAIGN|escape:'htmlall':'UTF-8'}" {if $CONFIGURATION.CAMPAIGN_ACTIVE == 0}disabled="disabled"{/if}>
				</div>
			</div>

			<div class="form-group clearfix">
				<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Product Name' mod='newsletterpro'}</span></label>
				<div class="col-sm-9">
					<input class="form-control fixed-width-xxl" type="text" id="utm_content" name="utm_content" value="{$CONFIGURATION.CAMPAIGN.UTM_CONTENT|escape:'htmlall':'UTF-8'}" {if $CONFIGURATION.CAMPAIGN_ACTIVE == 0}disabled="disabled"{/if}>
				</div>
			</div>

			<div class="form-group clearfix">
				<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Parameters' mod='newsletterpro'}</span></label>
				<div class="col-sm-9">
					<div class="form-group clearfix">
						<textarea class="form-control fixed-width-xxl gk-textarea" id="set-params" name="params" spellcheck="false" {if $CONFIGURATION.CAMPAIGN_ACTIVE == 0}disabled="disabled"{/if}>{$CAMPAIGN_PARAMETERS|escape:'html':'UTF-8'}</textarea>
					</div>
						<div class="clearfix">
						<a id="set-params-default" href="javascript:{}" class="btn btn-default btn-margin pull-left {if $CONFIGURATION.CAMPAIGN_ACTIVE == 0}disabled{/if}" onclick="NewsletterProControllers.SettingsController.makeDefaultParameteres();">
							<i class="icon icon-eraser"></i> {l s='Default' mod='newsletterpro'}
						</a>
						<a id="set-params-save" href="javascript:{}" class="btn btn-default btn-margin pull-left {if $CONFIGURATION.CAMPAIGN_ACTIVE == 0}disabled{/if}" onclick="NewsletterProControllers.SettingsController.saveCampaign();">
							<i class="icon icon-save"></i> {l s='Save' mod='newsletterpro'}
						</a>
						<div class="form-group clearfix">
							<span id="set-params-save-message" style="display: none;"></span>
						</div>
						<div class="clearfix">
							<p class="help-block">{l s='One parameter per line' mod='newsletterpro'}</p>
						</div>
					</div>
				</div>
			</div>
		</form>

		<div class="form-group clearfix">
			<div class="col-sm-9 col-sm-offset-3">
				<a id="campaign-is-running" href="javascript:{}" class="btn btn-default" onclick="NewsletterProControllers.SettingsController.checkIfCampaignIsRunning($(this));">
					<span class="btn-ajax-loader"></span>
					<i class="icon icon-check-circle"></i>
					<span>{l s='Check Campaign' mod='newsletterpro'}</span>
				</a>
			</div>
		</div>

		<div class="form-group clearfix">
			<div class="col-sm-9 col-sm-offset-3">
				<div class="checkbox">
					<label for="set-ganalytics" class="in-win control-label">
						<input class="smtp-checkbox" id="set-ganalytics" type="checkbox" onclick="NewsletterProControllers.SettingsController.activeGAnalytics( $(this) );" {if $CONFIGURATION.GOOGLE_ANALYTICS_ACTIVE == 1}checked="checked"{/if}>
						{l s='Activate Google Analytics' mod='newsletterpro'}
					</label>
				</div>
			</div>
		</div>

		<div class="form-group clearfix">
			<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Tracking ID' mod='newsletterpro'}</span></label>
			<div class="col-sm-9">
				<div class="clearfix">
					<input class="from-control fixed-width-xxl" id="ganalytics-id" type="text" value="{$CONFIGURATION.GOOGLE_ANALYTICS_ID|escape:'html':'UTF-8'}" onblur="NewsletterProControllers.SettingsController.updateGAnalyticsID( $(this) );" {if $CONFIGURATION.GOOGLE_ANALYTICS_ACTIVE == 0}disabled="disabled"{/if}>
				</div>
				<div class="form-group clearfix">
					<span id="ganalytics-id-message" style="display: none;"></span>
				</div>
				<div class="clearfix">
					<p class="help-block">{l s='Example: UA-1234567-1' mod='newsletterpro'}</p>
				</div>
			</div>
		</div>

	</div>

	<div class="alert alert-info">
		{l s='If you already configured your google analytics account with another module as "ganalytics", don\'t enable this option and follow the instructions.' mod='newsletterpro'}
		<br/>
		<span style="color: red;">{l s='It\'s important to have a little knowledge of php in order to proceed to the next step. I\'ts important to not generate errors in the script.' mod='newsletterpro'}</span>
		<br/>
		<br/>
		1. {l s='Find the "ganalytics" module folder and open the "ganalytics.php".' mod='newsletterpro'}
		<br>
		2. {l s='If you find the line: ' mod='newsletterpro'} <span style="color: #222; font-style: normal;"> ga(\'create\', ... </span> {l s='paste after that line, on a new row, the following code: ' mod='newsletterpro'} <span style="color: #222; font-style: normal; font-weight: bold;"> '.(class_exists('NewsletterPro') ? NewsletterPro::getNewsletterCampaign() : '').' </span>
		<br>
		<br>
		{l s='If you find that line of code at the step 2, you don\'t need follow the next steps.' mod='newsletterpro'}
		<br>
		<br>
		3. {l s='Find the "ganalytics" module folder and open the "header.tpl".' mod='newsletterpro'}
		<br/>
		4. {l s='Find the line:' mod='newsletterpro'} <span style="color: #222; font-style: normal;"> ga('create', ... </span>
		<br/>
		5. {l s='Find the line:' mod='newsletterpro'} <span style="color: #222; font-style: normal;"> _gaq.push(['_setAccount', ... </span>
		<br/>
		6. {l s='Paste after this line the following code:' mod='newsletterpro'}
		<span style="color: #222; font-style: normal; font-weight: bold;"> {ldelim}if isset($NEWSLETTER_CAMPAIGN){rdelim}{ldelim}$NEWSLETTER_CAMPAIGN{rdelim}{ldelim}/if{rdelim} </span>
		<br/>
		7. {l s='Save the file "header.tpl".' mod='newsletterpro'}
		<br/>
		8. {l s='Make sure the position of module "Newsletter Pro" is lower than module "ganalytics" on the page "Modules  > Positions" tab "Pages header".' mod='newsletterpro'}
	</div>

</div>