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
<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: block;">
{else}
<script type="text/javascript"> 
	if(window.location.hash == '#selectProducts') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: block;">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;">');
	{rdelim} 
</script>
{/if}

	<h4 style="float: left;">{l s='Select products to insert into template' mod='newsletterpro'}</h4>

	<a id="product_help" href="javascript:{}" class="btn btn-default product-help" onclick="NewsletterProControllers.TemplateController.showProductHelp();"><i class="icon icon-eye"></i> {l s='View available variables' mod='newsletterpro'}</a>
	<div class="clear"></div>
	<div class="separation" style="clear: both;"></div>

	<div class="data-grid-div">
		<table id="product-template-list" class="table table-bordered product-template-list">
			<thead>
				<tr>
					<th class="name" data-field="name">{l s='Template Name' mod='newsletterpro'}</th>
					<th class="date" data-field="date">{l s='Date Modified' mod='newsletterpro'}</th>
					<th class="np-info" data-template="info">{l s='For Newsletter Template' mod='newsletterpro'}</th>
					<th class="actions" data-template="actions">{l s='Actions' mod='newsletterpro'}</th>
				</tr>
			</thead>
		</table>
	</div>
	<br>
	<div>
		<a href="javascript:{}" class="btn btn-default pull-left" onclick="NewsletterProControllers.TemplateController.toggleShowProductTpl( $(this) )" data-name='{ldelim}"show":"{l s='Show product template' mod='newsletterpro'}","hide":"{l s='Hide product template' mod='newsletterpro'}"{rdelim}'>
			<i class="icon icon-eye"></i>
			<span>{l s='Show product template' mod='newsletterpro'}</span>
		</a>
		<a href="javascript:{}" class="button" style="margin-left: 15px; display: none;" onclick="NewsletterProControllers.TemplateController.loadProductTemplate()">{l s='Load product template' mod='newsletterpro'}</a>
		<div class="clear" style="height: 0;"></div>

		<div id="product-template" style="display: none;">
			<div>
				<div class="br">&nbsp;</div>
				<p class="help-block">{l s='Press the help button in the upper right corner to see full list of available variables.' mod='newsletterpro'}</p>
				<div id="product-template-content">
					<textarea style="display: none;" id="product-template-content-textarea" class="template-css">{$product_tpl_content|escape:'html':'UTF-8'}</textarea>
					<div id="product-content-box">
						{include file="$textarea_tpl" class_name='product_rte' content_name='product_content' config='product_config' input_name='product_template_text' input_value=$product_tpl_content}
					</div>
				</div>
			</div>

			<div class="view-content">
				<div id="view-product-template-content" style="display: none;">&nbsp;</div>
				<div class="clear"></div>
			</div>
			<br />
			<div class="form-group">
				<div class="col-sm-8">
					<div id="save-product-template-message" style="display: none;">&nbsp;</div>
				</div>
				<div class="col-sm-4">
					<a id="save-product-template" href="javascript:{}" class="btn btn-default pull-right" onclick="NewsletterProControllers.TemplateController.saveToggleProductTemplate( $(this) )" data-name='{ldelim}"view":"{l s='Save and View' mod='newsletterpro'}","edit":"{l s='Edit' mod='newsletterpro'}"{rdelim}'>
						<i class="icon icon-edit"></i>
						<span>{l s='Save and view' mod='newsletterpro'}</span>
					</a>
					<a href="javascript:{}" class="btn btn-default pull-right btn-margin-right" onclick="NewsletterProControllers.TemplateController.saveAsProductTemplate( $(this) );"  data-message="{l s='Please insert the name of the new product template:' mod='newsletterpro'}">
						<i class="icon icon-save"></i>
						<span>{l s='Save as' mod='newsletterpro'}</span>
					</a>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<br>
	<div class="clear">&nbsp;</div>

	<div style="position: relative;">
		<div id="categories-list" class="div_userlist categories-list">
			<h4>{l s='Categories' mod='newsletterpro'}</h4>
			<div class="separation"></div>

			<div class="clear">&nbsp;</div>
		<ul class="userlist"></ul>
		<div class="clear">&nbsp;</div>
		</div>
		<div id="product-list" class="div_userlist products-list">
			<h4 style="margin-left: 10px;">{l s='Products' mod='newsletterpro'}</h4>
			<div class="separation"></div>

			<div class="clear">&nbsp;</div>
			<div class="poduct-search-container">
				<span class="product-search-span ajax-loader" style="display: none;">&nbsp;</span>
				<input id="poduct-search" class="search-bar empty" type="text" value="{l s='search products by:' mod='newsletterpro'} {l s='name, reference, category or type:' mod='newsletterpro'} {l s='new products' mod='newsletterpro'} {l s='or' mod='newsletterpro'} {l s='price drop' mod='newsletterpro'}">
				<select id="product-sort">
					<option value="reference">Reference</option>
					<option value="quantity">Quantity</option>
				</select>
			</div>
			<div class="clear">&nbsp;</div>
			<a id="toggle-categories" href="javascript:{}" class="slide-toggle" onclick="NewsletterProControllers.NavigationController.toggleCategories( $(this) )">&nbsp;</a>
			<div class="userlist">
			<table>
				<tbody>
				</tbody>
			</table>
			</div>
			<div class="clear">&nbsp;</div>
		</div>
		<div class="clear">&nbsp;</div>
		<div id="display-product-image-container" class="display-product-image-container">
			<label for="display-product-image" class="control-label">
				<input id="display-product-image" type="checkbox" {if $display_product_image == true} checked {/if} onclick="NewsletterProControllers.NavigationController.displayProductImage( $(this) )"> {l s='Display product image' mod='newsletterpro'}
			</label>
			<span id="display-product-image-message">&nbsp;</span>
			<div class="clear">&nbsp;</div>
		</div>
		<div class="margin-top" style="float: right;">
			<a href="javascript:{}" style="float: right;" onclick="NewsletterPro.components.Product.removeAllProducts();" class="btn btn-default">
				<i class="icon icon-minus-circle"></i> {l s='remove all' mod='newsletterpro'}
			</a> 
			<div class="clear">&nbsp;</div>
		</div>
	</div>

	<div class="clear">&nbsp;</div>
	<br>

	<div id="products-adjustments-div">
		<h4>{l s='Products adjustments' mod='newsletterpro'}</h4>
		<div class="separation"></div>

		<p class="help-block" style="width: auto;">{l s='The responsive templates are not adjustable, because the responsive layout can be damaged by the adjustments. You can adjust them by changing the CSS and HTML.' mod='newsletterpro'}</p>
		<div id="products-adjustments" class="products-adjustments" style="background: #FFF; border: solid thin #d0d0d0; padding: 10px; display: block; margin: 0 auto;">

			<table>
				<tr>
					<td class="first-item">
						<div class="slider-container">
							<label>{l s='Products per row:' mod='newsletterpro'}</label>
							<div id="slider-products-per-row"></div>
						</div>
					</td>
					<td>
						<div class="slider-container" style="display: none;">
							<label>{l s='Image size:' mod='newsletterpro'}</label>
							<div id="slider-image-size"></div>
						</div>
					</td>
					<td>
						<div class="slider-container" style="display: none;">
							<label>{l s='Products width:' mod='newsletterpro'}</label>
							<div id="slider-product-width"></div>
						</div>
					</td>
				</tr>
				<tr>
					<td class="first-item">
						<div class="slider-container" style="display: none;">
							<label>{l s='Trim product name:' mod='newsletterpro'}</label>
							<div id="slider-name"></div>
						</div>
					</td>
					<td class="item">
						<div class="slider-container" style="display: none;">
							<label>{l s='Trim product short description:' mod='newsletterpro'}</label>
							<div id="slider-description-short"></div>
						</div>
					</td>
					<td class="last-item">
						<div class="slider-container" style="display: none;">
							<label>{l s='Trim product description:' mod='newsletterpro'}</label>
							<div id="slider-description"></div>
						</div>
					</td>
				</tr>
			</table>

			<div class="clear"></div>
		</div>
	</div>

	<div class="clear">&nbsp;</div>
	<br>

	<div id="np-view-products-box" class="clearfix" style="display: block;">
		<div class="form-group clearfix">
			<div class="form-inline">
				<div class="form-group pull-left">
					<h4>{l s='View selected products' mod='newsletterpro'} <i id="slider-ppr-loading" class="icon icon-refresh icon-spin slider-ppr-loading" style="display: none;"></i><span style="font-size: 12px; color: #3586AE;margin-left: 10px;">{l s='width' mod='newsletterpro'}: <span id="sp-width">0</span>px</span></h4>
				</div>
				<div class="form-group pull-right">
					<label class="control-label np-margin-5"><span class="label-tooltip">{l s='Language' mod='newsletterpro'}</span></label>
					<div id="np-change-view-template-lang" class="gk_lang_select pull-right np-margin-5-left"></div>
				</div>
				<div class="form-group pull-right">
					<label class="control-label np-margin-5"><span class="label-tooltip">{l s='Currency for this language' mod='newsletterpro'}</span></label>
					<div id="products-currency-change" class="gk_currency_select pull-right np-margin-5"></div>
				</div>
				<div class="form-group pull-right np-margin-5">
					<select id="np-selected-products-sort-order" class="gk-select form-control" style="margin: 0;">
						<option value="1">{l s='asc' mod='newsletterpro'}</option>
						<option value="0">{l s='desc' mod='newsletterpro'}</option>
					</select>
				</div>
				<div class="form-group pull-right np-margin-5">
					<label class="control-label np-margin-5"><span class="label-tooltip">{l s='Sort by' mod='newsletterpro'}</span></label>
					<select id="np-selected-products-sort" class="gk-select form-control" style="margin: 0;">
						<option value="0">- {l s='none' mod='newsletterpro'} -</option>
						<option value="name">{l s='Name' mod='newsletterpro'}</option>
						<option value="price">{l s='Price' mod='newsletterpro'}</option>
						<option value="reduction">{l s='Reduction' mod='newsletterpro'}</option>
						<option value="discount">{l s='Discount' mod='newsletterpro'}</option>
					</select>
				</div>
			</div>
			<div class="separation" style="clear: both;"></div>
		</div>

		<div style="background: #FFF; border: solid thin #d0d0d0; padding: 10px; display: block; margin: 0 auto;" class="view-selected-products">
			<div id="selected-products" class="clearfix" style="margin: 0 auto; display: block; position: relative; width: 100%;"></div>
		</div>
	</div>

	<div class="clear">&nbsp;</div>
	<br>

	<a id="setp1" href="#createTemplate" class="btn btn-primary pull-right" onclick="NewsletterProControllers.NavigationController.goToStep( 4 );" >
	<span>{l s='Next Step' mod='newsletterpro'} &raquo;</span></a>
</div>