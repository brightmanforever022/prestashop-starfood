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

<script type="text/javascript">

	var inputName = '{$input_name|escape:'html':'UTF-8'}';
	var selectedCat = '{$selected_cat|escape:'html':'UTF-8'}';
	var selectedLabel = '{$selected_label|escape:'html':'UTF-8'}';
	var home = '{$home|escape:'html':'UTF-8'}';
	var use_radio = Number('{$use_radio|intval}');
	var ajaxRequestUrl = '{$ajax_request_url|escape:'quotes':'UTF-8'}';

	{if !$use_in_popup}
		$(document).ready(function(){
			buildTreeView(Number('{$use_shop_context|intval}'));
		});
	{else}
		buildTreeView(Number('{$use_shop_context|intval}'));
	{/if}
</script>

<div class="form-group clearfix category-filter">

	<div class="form-group col-sm-12 clearfix">
		<span><a href="#" id="collapse_all" class="btn btn-default">{l s='Collapse All' mod='newsletterpro'}</a>&nbsp;|&nbsp;</span>	
		<span><a href="#" id="expand_all" class="btn btn-default">{l s='Expand All' mod='newsletterpro'}</a>&nbsp;|&nbsp;</span>
		{if !$use_radio}
			<span><a href="#" id="check_all" class="btn btn-default">{l s='Check All' mod='newsletterpro'}</a>&nbsp;|&nbsp;</span>
			<span><a href="#" id="uncheck_all" class="btn btn-default">{l s='Uncheck All' mod='newsletterpro'}</a></span>
		{/if}
	</div>

	<div class="form-group col-sm-12 clearfix">
		{if $use_search}
			<div class="form-inline">
				<div class="form-group">
					<label class="control-label" style="padding-top: 0;"><span class="label-tooltip">{l s='search' mod='newsletterpro'}</span></label>
					<input type="text" name="search_cat" id="search_cat" class="form-control">
				</div>
			</div>
		{/if}
	</div>

	{* HTML CONTENT *}
	{$content|strval}

	{if $option_no_decide}
	<ul class="filetree" style="list-style: none;">
		<li class="hasChildren">
			<input type="{if !$use_radio}checkbox{else}radio{/if}" name="{$input_name|escape:'html':'UTF-8'}" value="-1" onclick="clickOnCategoryBox($(this));" />
			<span class="category_label">{l s='Customers who have not chosen any category' mod='newsletterpro'}</span>
		</li>
	</ul>
	{/if}

	<ul id="categories-treeview" class="filetree">

		<li id="{$root.id_category|escape:'html':'UTF-8'}" class="hasChildren">
			<span class="folder">
			{if $root_input}
				<input type="{if !$use_radio}checkbox{else}radio{/if}" name="{$input_name|escape:'html':'UTF-8'}" value="{$root.id_category|escape:'html':'UTF-8'}" {if $home_is_selected} checked {/if} onclick="clickOnCategoryBox($(this));" />
				<span class="category_label"> {$root.name|escape:'html':'UTF-8'} </span>
			{else}
				&nbsp;
			{/if}
			</span>
			<ul>
				<li><span class="placeholder">&nbsp;</span></li>
		  	</ul>
		</li>
	</ul>
</div>

{if $use_search}
	<script type="text/javascript">
		$(document).ready(function(){
			searchCategory();
		});
	</script>
{/if}