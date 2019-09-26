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
<div id="{$tab_id|escape:'html':'UTF-8'}" class="tab-csv" style="display: none;">
{else}
<script type="text/javascript"> 
	if(window.location.hash == '#csv') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" class="tab-csv" style="display: block;">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" class="tab-csv" style="display: none;">');
	{rdelim} 
</script>
{/if}
	<h4>{l s='Import and export emails addresses from CSV files' mod='newsletterpro'}</h4>
	<div class="separation"></div>

	<div id="import-export-container" class="form-group">
		<div class="col-lg-6">
			<h4>{l s='Import' mod='newsletterpro'}</h4>
			<div class="separation"></div>
			<div class="clear">&nbsp;</div>

			<div class="form-group clearfix">
				<form id="upload-csv-form" class="defaultForm" action="{$page_link|escape:'quotes':'UTF-8'}#csv" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'quotes':'UTF-8'}"{/if}>
					<label class="control-label col-sm-4">
						<span class="label-tooltip">{l s='CSV file' mod='newsletterpro'}</span>
					</label>
					<div class="col-sm-8 clearfix">
						<input id="upload-csv" class="form-control" type="file" name="upload_csv"/>
					</div>
					<div class="clear">&nbsp;</div>
					<div class="file-msg" id="upload-csv-message"><br></div>
				</form>

				<div class="clear">&nbsp;</div>
				
				<div class="form-group">
					<table id="upload-csv-files" class="table table-bordered upload-csv-files">
						<thead>
							<tr>
								<th class="name">{l s='Name' mod='newsletterpro'}</th>
								<th class="actions center">{l s='Actions' mod='newsletterpro'}</th>
							</tr>
						</thead>
						<tbody>
						{foreach $csv_import_files as $file}
							<tr data-name="{$file|escape:'quotes':'UTF-8'}">
								<td class="name"> {$file|escape:'quotes':'UTF-8'} </td>
								<td class="actions center" style="text-align: center !important;"> 
									<a class="delete-added" href="javascript:{}" style="text-align: center;" onclick="NewsletterProComponents.objs.uploadCSV.deleteItemByName('{$file|escape:'quotes':'UTF-8'}');">
										<i class="icon icon-remove cross-white"></i>
									</a> 
								</td>
							</tr>
						{/foreach}
						</tbody>
					</table>
				</div>
		
				<div class="form-group clearfix">
					<div class="input-group">
						<span class="input-group-addon">{l s='CSV separator' mod='newsletterpro'}</span>
						<input class="form-control text-center" id="import-separator" type="text" name="separator_csv" maxlength="1" value=";"/>
						<span class="input-group-addon">{l s='From line' mod='newsletterpro'}</span>
						<input class="form-control text-center" id="from-linenumber" type="text" name="from_linenumber" value="2"/>
						<span class="input-group-addon">{l s='To line' mod='newsletterpro'}</span>
						<input class="form-control text-center" id="to-linenumber" type="text" name="to_linenumber" value="0"/>
					</div>
					<p class="help-block" style="margin-top: 5px;">
						{l s='Fill a value below %s or %s into the "to line" input field if you want to import all the emails from the .csv file.' sprintf=['2','0'] mod='newsletterpro'}
					</p>
				</div>
				
				<div class="form-group clearfix">
					<a class="btn btn-default" href="javascript:{}" onclick="NewsletterProComponents.objs.uploadCSV.nextStep( $(this) );" data-bad-separator="{l s='Bad CSV separator.' mod='newsletterpro'}" data-no-file="{l s='You don\'t choose a CSV file.' mod='newsletterpro'}">
						<span>{l s='Next Step' mod='newsletterpro'} 
							<i class="icon icon-chevron-right on-right" style="font-size: 11px;"></i>
						</span>
					</a>
				</div>

				<div class="form-group clearfix">
					<div class="alert alert-info">
						<a href="{$page_link|escape:'quotes':'UTF-8'}&downloadImportSample#csv">{l s='Download Import Sample' mod='newsletterpro'}</a>
						<p>{l s='Use ; or , for separator.' mod='newsletterpro'}</p>
						<p>{l s='All email are imported in the tab "Send newsletters" on column' mod='newsletterpro'} <a href="#sendNewsletters" onclick="NewsletterProControllers.NavigationController.goToStep( 5, $('#added-list') );">{l s='"Added users"' mod='newsletterpro'}</a>.</p>
						<p>{l s='Available field to import' mod='newsletterpro'}: {l s='\"Email\", \"First Name\", \"Last Name\", \"Language ID\", \"Shop ID\", \"Shop Group ID\", \"Registration IP Address\", \"Active\"' mod='newsletterpro'}</p>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<h4>{l s='Export' mod='newsletterpro'}</h4>
			<div class="separation"></div>

			{if isset($export_email_addresses_errors) && count($export_email_addresses_errors)}
				<div class="alert alert-danger">
				{foreach $export_email_addresses_errors as $error}
					<div class="clearfix">{$error|escape:'quotes':'UTF-8'}</div>
				{/foreach}
				</div>
			{/if}

			{assign var=LIST_CUSTOMERS value=NewsletterPro::LIST_CUSTOMERS}
			{assign var=LIST_VISITORS value=NewsletterPro::LIST_VISITORS}
			{assign var=LIST_VISITORS_NP value=NewsletterPro::LIST_VISITORS_NP}
			{assign var=LIST_ADDED value=NewsletterPro::LIST_ADDED}

			<div class="form-group clearfix">
				<label class="control-label col-sm-4 np-control-lable-radio">{l s='Select List' mod='newsletterpro'}</label>
				<div class="col-sm-8">
					<div class="radio">
						<label class="control-label in-win">
							<input class="form-group" type="radio" name="exportEmailAddresses" value="{$LIST_CUSTOMERS|intval}" checked>
							{l s='Customers' mod='newsletterpro'}
						</label>
					</div>
					
					{if $newsletter_table_exists}
					<div class="radio">
						<label class="control-label in-win">
							<input class="form-group" type="radio" name="exportEmailAddresses" value="{$LIST_VISITORS|intval}">
							{l s='Visitors (Block Newsletter)' mod='newsletterpro'}
						</label>
					</div>
					{/if}

					<div class="radio">
						<label class="control-label in-win">
							<input class="form-group" type="radio" name="exportEmailAddresses" value="{$LIST_VISITORS_NP|intval}">
							{l s='Visitors (Newsletter Pro)' mod='newsletterpro'}
						</label>
					</div>

					<div class="radio">
						<label class="control-label in-win">
							<input class="form-group" type="radio" name="exportEmailAddresses" value="{$LIST_ADDED|intval}">
							{l s='Added' mod='newsletterpro'}
						</label>
					</div>
				</div>
			</div>

			<div class="form-group clearfix">
				<div class="col-sm-8 col-sm-offset-4">
					<a id="btn-export-email-addresses" href="#csv" class="btn btn-default">
						{l s='Next Step' mod='newsletterpro'}
						<i class="icon icon-chevron-right on-right"></i>
					</a>
				</div>
			</div>
		</div>
		<div class="clear">&nbsp;</div>
	</div>

	<div id="import-details" class="import-details" style="display: none;">
		{include file="$import_details_tpl"}
	</div>
	
	<div id="export-details" class="export-details" style="display: none;">
		{include file="$tpl_location"|cat:"templates/admin/export_details.tpl"}
	</div>
</div>