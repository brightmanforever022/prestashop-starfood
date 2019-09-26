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
<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;">
{else}
<script type="text/javascript"> 
	if(window.location.hash == '#createTemplate') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: block;">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;">');
	{rdelim} 
</script>
{/if}

	<h4 style="float: left;">{l s='Create newsletter template' mod='newsletterpro'}</h4>
	<a id="newsletter_help" href="javascript:{}" class="btn btn-default newsletter-help" onclick="NewsletterProControllers.TemplateController.showNewsletterHelp();"><i class="icon icon-eye"></i> {l s='View available variables' mod='newsletterpro'}</a>
	<a href="javascript:{}" id="chimp-import-html" class="btn btn-default chimp-import-html" style="float: right; margin-right: 10px; display: none;"><img src="{$module_img_path|escape:'quotes':'UTF-8'}chimp16.png"><span>{l s='Import from Mail Chimp' mod='newsletterpro'}</span></a>
	<div class="clear"></div>
	<div class="separation"></div>

	<div class="data-grid-div">
		<table id="newsletter-template-list" class="table table-bordered newsletter-template-list">
			<thead>
				<tr>
					<th class="name" data-field="name">{l s='Template Name' mod='newsletterpro'}</th>
					<th class="date" data-field="date">{l s='Date Modified' mod='newsletterpro'}</th>
					<th class="attachment" data-template="attachment">{l s='Attachment' mod='newsletterpro'}</th>
					<th class="actions" data-template="actions">{l s='Actions' mod='newsletterpro'}</th>
				</tr>
			</thead>
		</table>
	</div>

	<br>
	<div>
		<h4>{l s='Template adjustments' mod='newsletterpro'}:</h4>
		<div class="separation"></div>

		<p class="help-block" style="width: auto;">{l s='The responsive templates are not adjustable, because the responsive layout can be damaged by the adjustments. You can adjust them by changing the CSS and HTML.' mod='newsletterpro'}</p>
		<div class="template-settings">
			<div class="ts-left">
				<div id="slider-container" class="slider-container" style="display: none;">
					<label>{l s='Template width:' mod='newsletterpro'}</label>
					<div id="template-width-slider"></div>
					<div class="clear"></div>
				</div>
			</div>
			<div class="ts-right">

				<div class="color-container">
					<div style="display: none;">
						<label>{l s='Template bg color:' mod='newsletterpro'}</label>
						<div class="clear" style="margin-bottom: 6px;"></div>
						<input id="template-container-color" class="gk-color" value="FFFFFF">
					</div>

					<div style="display: none;">
						<label>{l s='Content bg color:' mod='newsletterpro'}</label>
						<div class="clear" style="margin-bottom: 6px;"></div>
						<input id="template-content-color" class="gk-color" value="FFFFFF">
					</div>

					<div>
						<label>{l s='All links color:' mod='newsletterpro'}</label>
						<div class="clear" style="margin-bottom: 6px;"></div>
						<input id="links-color" class="gk-color" value="FFFFFF">
					</div>
				</div>

				<div class="color-container">
					<div style="display: none;">
						<label>{l s='Products bg color:' mod='newsletterpro'}</label>
						<div class="clear" style="margin-bottom: 6px;"></div>
						<input id="products-bg-color" class="gk-color" value="FFFFFF">
					</div>

					<div style="display: none;">
						<label>{l s='Products name color:' mod='newsletterpro'}</label>
						<div class="clear" style="margin-bottom: 6px;"></div>
						<input id="products-name-color" class="gk-color" value="FFFFFF">
					</div>

					<div style="display: none;">
						<label>{l s='Description color:' mod='newsletterpro'}</label>
						<div class="clear" style="margin-bottom: 6px;"></div>
						<input id="products-description-color" class="gk-color" value="FFFFFF">
					</div>
				</div>

				<div class="color-container">
					<div style="display: none;">
						<label>{l s='Products border color:' mod='newsletterpro'}</label>
						<div class="clear" style="margin-bottom: 6px;"></div>
						<input id="products-border-color" class="gk-color" value="FFFFFF">
					</div>

					<div style="display: none;">
						<label>{l s='Short description color:' mod='newsletterpro'}</label>
						<div class="clear" style="margin-bottom: 6px;"></div>
						<input id="products-s-description-color" class="gk-color" value="FFFFFF">
					</div>

					<div style="display: none;">
						<label>{l s='Price color:' mod='newsletterpro'}</label>
						<div class="clear" style="margin-bottom: 6px;"></div>
						<input id="products-price-color" class="gk-color" value="FFFFFF">
					</div>
				</div>

				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<div class="newsletter-template-div">
		<div class="clearfix">
			<div class="col-sm-1">
				<label class="control-label">
					<span class="label-tooltip">{l s='Title' mod='newsletterpro'}</span>
				</label>			
			</div>
			<div class="col-sm-11">
				
				<div class="form-inline">
					<div class="form-group">
						{foreach $newsletter_template.title as $id_lang => $title}
							<input id="page-title-{$id_lang|intval}" data-lang="{$id_lang|intval}" class="form-control pull-left fixed-width-xxl" type="text" name="page_title" value="{$title|escape:'html':'UTF-8'}" style="{if $id_lang == $default_lang}display: block;{else}display: none;{/if}"/>
						{/foreach}
					</div>
					<div class="form-group">
						<div class="gk_lang_select"></div>
					</div>
					<div class="form-group">
						<span id="page-title-message">&nbsp;</span>
					</div>
				</div>
			</div>
		</div>

		<div id="newsletter-template" style="display: inline-block;">
			<p class="help-block">{l s='Press the help button in the upper right corner to see full list of available variables.' mod='newsletterpro'}</p>
			
			<div id="newsletter-template-content" class="form-group clearfix">
				<div class="form-inline">
					<div class="form-group">
						<div id="tab_template" class="newsletter-template">
							<a id="tab_newsletter-template_0" href="#createTemplate" class="btn btn-default first_item"><i class="icon icon-edit"></i> {l s='Edit' mod='newsletterpro'}</a>
							<a id="tab_newsletter-template_1" href="#createTemplate" class="btn btn-default item"><i class="icon icon-eye"></i> {l s='View' mod='newsletterpro'}</a>
							<a id="tab_newsletter-template_3" href="#createTemplate" class="btn btn-default item"><i class="icon icon-code"></i> {l s='Header' mod='newsletterpro'}</a>
							<a id="tab_newsletter-template_4" href="#createTemplate" class="btn btn-default item"><i class="icon icon-code"></i> {l s='Footer' mod='newsletterpro'}</a>
							<a id="tab_newsletter-template_5" href="#createTemplate" class="btn btn-default item"><i class="icon icon-code"></i> {l s='CSS Style' mod='newsletterpro'}</a>
							<a id="tab_newsletter-template_2" href="#createTemplate" class="btn btn-default last_item tab-global-css"><i class="icon icon-code"></i> {l s='Global CSS ( for all templates )' mod='newsletterpro'}</a>
						</div>
					</div>
					<div class="form-group pull-right">
						<div id="newsletter-template-lang-select" class="gk_lang_select"></div>
					</div>
					<div class="form-group pull-right btn-margin">
						<a id="np-view-newsletter-template-in-browser" href="javascript:{}" target="_blank" class="btn btn-default item"><i class="icon icon-eye"></i> {l s='View in Browser' mod='newsletterpro'}</a>
					</div>
				</div>

				<div id="tab_template_content" class="newsletter-template clearfix">
					<div id="tab_content_newsletter-template_0">

						{include file="$tpl_location"|cat:"templates/admin/textarea_multilang_template.tpl" 
							class_name='newsletter_rte' 
							config='newsletter_config'
							content_name='newsletter_content' 
							input_name="newsletter_template_text" 
							input_value=$newsletter_template.body 
							content_css=$newsletter_template.css_link
							init_callback='NewsletterPro.modules.createTemplate.initTinyCallback'
						}
					</div>
					<div id="tab_content_newsletter-template_1" style="display: none;">
						<div class="view-content">
							<iframe id="view-newsletter-template-content" style="display: block; vertical-align: top;" scrolling="no" src="about:blank" class="view-newsletter-template-content"> </iframe>
							<div class="clear"></div>
						</div>
					</div>
					<div id="tab_content_newsletter-template_2" style="display: none;">
						<textarea id="template-css" class="template-css" wrap="off" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">{$newsletter_template.css_global_file[$default_lang]|escape:'html':'UTF-8'}</textarea>
					</div>
					<div id="tab_content_newsletter-template_3" style="display: none;">
						{foreach $newsletter_template.header as $id_lang => $header}
							<textarea id="template-header-{$id_lang|intval}" data-lang="{$id_lang|intval}" class="template-header" wrap="off" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" style="{if $id_lang == $default_lang}display: block;{else}display: none;{/if}">{$header|escape:'html':'UTF-8'}</textarea>
						{/foreach}
					</div>
					<div id="tab_content_newsletter-template_4" style="display: none;">
						{foreach $newsletter_template.footer as $id_lang => $footer}
							<textarea id="template-footer-{$id_lang|intval}" data-lang="{$id_lang|intval}" class="template-footer" wrap="off" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" style="{if $id_lang == $default_lang}display: block;{else}display: none;{/if}">{$footer|escape:'html':'UTF-8'}</textarea>
						{/foreach}
					</div>
					<div id="tab_content_newsletter-template_5" style="display: none;">
						<textarea id="template-css-style" class="template-css-style" wrap="off" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">{$newsletter_template.css_file[$default_lang]|escape:'html':'UTF-8'}</textarea>
					</div>
				</div>
			</div>

			<div class="form-group clearfix">
				<div class="col-sm-4">
					<div id="save-newsletter-template-message" style="display: none;">&nbsp;</div>
				</div>
				
				<div class="col-sm-8">
					<a id="save-newsletter-template" href="javascript:{}" class="btn btn-default pull-right btn-margin">
						<span class="btn-ajax-loader"></span>
						<i class="icon icon-save"></i> <span>{l s='Save' mod='newsletterpro'}</span>
					</a>

					<a id="save-as-newsletter-template" href="javascript:{}" class="btn btn-default pull-right btn-margin">
						<span class="btn-ajax-loader"></span>
						<i class="icon icon-save"></i> <span>{l s='Save As' mod='newsletterpro'}</span>
					</a>

					<form id="inputImportHTMLForm" class="defaultForm" action="{$page_link|escape:'quotes':'UTF-8'}#createTemplate" method="post" enctype="multipart/form-data">
						<div class="fileUpload btn btn-default pull-right btn-margin" style="margin-left: 2px; margin-right: 2px; margin-top: 0; margin-bottom: 0;">
							<i class="icon icon-upload"></i> <span>{l s='Import HTML' mod='newsletterpro'}</span>
							<input id="inputImportHTML" type="file" name="inputImportHTML" class="upload">
						</div>
					</form>

					<a id="export-html" href="{$page_link|escape:'quotes':'UTF-8'}&exportHTML#createTemplate" class="btn btn-default pull-right btn-margin">
					<i class="icon icon-download"></i> <span>{l s='Export HTML' mod='newsletterpro'}</span>
					</a>				

					<a id="chimp-export-html" href="javascript:{}" class="btn btn-default pull-right btn-margin">
						<i class="icon icon-download"></i> <span>{l s='Export to Mail Chimp' mod='newsletterpro'}</span>
					</a>
				</div>
			</div>

			<a id="setp1" href="#selectProducts" class="btn btn-primary pull-left  btn-margin" onclick="NewsletterProControllers.NavigationController.goToStep( 3 );">
				<span>&laquo; {l s='Previous Step' mod='newsletterpro'}</span>
			</a>
			<a id="setp2" href="#sendNewsletters" class="btn btn-primary pull-right btn-margin" onclick="NewsletterProControllers.NavigationController.goToStep( 5 );">
				<span>{l s='Next Step' mod='newsletterpro'} &raquo;</span>
			</a>
		</div>
	</div>
</div>