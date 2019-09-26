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
	<div class="col-sm-12">
		<div class="alert alert-danger error clearfix">
			<div class="clearfix">
				{l s='You need to update the module, some of the features cannot run properly.' mod='newsletterpro'} {l s='You\'re have installed the version %s and the last version is %s.' sprintf=[$update.db_version,$update.version] mod='newsletterpro'}
			</div>
			<div class="clearfix">
				{l s='Press on the update button to make this update. You will receive a confirmation message after the update process. If the update failed you need to reset the module, but all the database data related to this module will be lost (the templates will be safe).' mod='newsletterpro'}
			</div>
			<div class="clearfix">
				{l s='If you press on the update button, and nothing happen, you don\'t receive any message you need to do: enable the force compilation option, clear the prestashop cache, and also the browser cache, then refresh the page and press on the update button again.' mod='newsletterpro'}
			</div>
			<a href="javascript:{}" class="btn btn-default" onclick="NewsletterProControllers.UpgradeController.execute($(this));"><span class="btn-ajax-loader" style="display: none;"></span>{l s='Update to' mod='newsletterpro'} {$update.version|escape:'html':'UTF-8'}</a>
			<div id="update-module-response" class="update-module-response" style="display: none;"> </div>
		</div>
	</div>
</div>