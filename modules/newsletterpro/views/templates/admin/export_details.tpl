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
	<a id="np-export-email-addresses-back" class="btn btn-default pull-left" href="javascript:{}">
		<i class="icon icon-chevron-left on-left" style="font-size: 11px;"></i>
		<span>{l s='Go Back' mod='newsletterpro'}</span>
	</a>
</div>

<div class="clearfix">
	<form id="export-csv-form" class="defaultForm" action="{$page_link|escape:'quotes':'UTF-8'}#csv" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'html':'UTF-8'}"{/if}>
		<input type="hidden" name="export_email_addresses" value="1">
		<input id="export-csv-list-ref" type="hidden" name="list_ref" value="0">

		<div class="form-group clearfix">
			<label class="control-label col-sm-2">
				<span class="label-tooltip">{l s='CSV separator' mod='newsletterpro'}</span>
			</label>
			<div class="col-sm-3">
				<input class="form-control fixed-width-xs text-center" type="text" name="csv_separator" maxlength="1" value=";"/>
			</div>
			<div class="col-sm-7">
				<a id="btn-export-csv" href="javascript:{}" class="btn btn-default">
					<i class="icon icon-download"></i>
					{l s='Export' mod='newsletterpro'}
				</a>
			</div>
		</div>

		<div class="form-group clearfix">
			<label class="control-label col-sm-2 np-control-lable-checkbox">{l s='Columns' mod='newsletterpro'}</label>
			<div id="np-export-email-options" class="col-sm-3">
			</div>
			<div class="col-sm-7">
				<a id="btn-export-csv-checkall" href="javascript:{}" class="btn btn-default">
					<i class="icon icon-check-circle"></i>
					{l s='Check All' mod='newsletterpro'}
				</a>
			</div>
		</div>
	</form>
</div> 
