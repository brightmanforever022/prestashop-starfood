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

{if $update.needs_update == true}
{include file="$tpl_location"|cat:"templates/admin/module_update.tpl"}
{elseif $CONFIGURATION.SHOW_CLEAR_CACHE == true}
<div id="clear-cache-box" class="form-group clearfix">
	<div class="col-sm-12">
		<div class="alert alert-danger error clearfix">
			<div class="clearfix">
				{l s='The module has been updated. It\'s required to clear the prestashop cache from "Advanced Parameters" > "Performance", and also the web browser cache.'  mod='newsletterpro'}
			</div>
			<div class="clearfix">
				{l s='Click on the "I Agree" button for hiding this message in the future.' mod='newsletterpro'}
			</div>
			<a href="javascript:{}" class="btn btn-default" onclick="NewsletterProControllers.ClearCacheController.clear($(this));">
				<i class="icon icon-check-circle"></i>
				{l s='I Agree' mod='newsletterpro'}
			</a>
		</div>
	</div>
</div>
{/if}