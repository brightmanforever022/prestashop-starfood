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

<div id="filter-by-birthday-box" class="form-group filter-by-birthday-box {if isset($fbb_class)}{$fbb_class|escape:'html':'UTF-8'}{/if}">
	<h4 class="title">{l s='Select the birthday date:' mod='newsletterpro'}</h4>
	<div class="col-sm-12 row">
		<div class="form-inline">
			<div class="col-sm-12 pull-left">
				<label class="control-label" style="padding-top: 0;"><span class="label-tooltip">{l s='From:' mod='newsletterpro'}</span></label>
				<input id="fbb-from-{if isset($fbb_class)}{$fbb_class|escape:'html':'UTF-8'}{/if}" type="text" class="form-control fbb-from {if isset($fbb_class)}{$fbb_class|escape:'html':'UTF-8'}{/if}">

				<label class="control-label" style="padding-top: 0;"><span class="label-tooltip">{l s='To:' mod='newsletterpro'}</span></label>
				<input id="fbb-to-{if isset($fbb_class)}{$fbb_class|escape:'html':'UTF-8'}{/if}" type="text" class="form-control fbb-to {if isset($fbb_class)}{$fbb_class|escape:'html':'UTF-8'}{/if}">
				<a href="javascript:{}" class="btn btn-default btn-margin fbb-clear {if isset($fbb_class)}{$fbb_class|escape:'html':'UTF-8'}{/if}" id="fbb-clear-{if isset($fbb_class)}{$fbb_class|escape:'html':'UTF-8'}{/if}">{l s='Clear' mod='newsletterpro'}</a>
			</div>
		</div>
	</div>
</div>