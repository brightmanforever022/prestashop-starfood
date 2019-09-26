{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if !$simple_header}

	<script type="text/javascript">
		$(document).ready(function() {
			$('table.{$table|escape:'html':'UTF-8'} .filter').keypress(function(event){
				formSubmit(event, 'submitFilterButton{$table|escape:'html':'UTF-8'}')
			})
		});
	</script>
	{* Display column names and arrows for ordering (ASC, DESC) *}
	{if $is_order_position}
		<script type="text/javascript" src="../js/jquery/plugins/jquery.tablednd.js"></script>
		<script type="text/javascript">
			var token = '{$token|escape:'html':'UTF-8'}';
			var come_from = '{$table|escape:'html':'UTF-8'}';
			var alternate = {if $order_way == 'DESC'}'1'{else}'0'{/if};
		</script>
		<script type="text/javascript" src="../js/admin-dnd.js"></script>
	{/if}

	<script type="text/javascript">
		$(function() {
			if ($("table.{$table|escape:'html':'UTF-8'} .datepicker").length > 0)
				$("table.{$table|escape:'html':'UTF-8'} .datepicker").datepicker({
					prevText: '',
					nextText: '',
					dateFormat: 'yy-mm-dd'
				});
		});
	</script>


{/if}{* End if simple_header *}

{if $show_toolbar}
	{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
{/if}

<img src="../modules/orderedit/img/orderedit-banner_10.jpg" />

{if !$simple_header}
	<div class="leadin">{block name="leadin"}{/block}</div>
{/if}

{block name="override_header"}{/block}


{hook h='displayAdminListBefore'}
{if isset($name_controller)}
	{capture name=hookName assign=hookName}display{$name_controller|ucfirst|escape:'html':'UTF-8'}ListBefore{/capture}
	{hook h=$hookName}
{elseif isset($smarty.get.controller)}
	{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities|escape:'html':'UTF-8'}ListBefore{/capture}
	{hook h=$hookName}
{/if}


{if !$simple_header}
<form method="post" action="{$action|escape:'html':'UTF-8'}" class="form">
	<input type="hidden" id="submitFilter{$table|escape:'html':'UTF-8'}" name="submitFilter{$table|escape:'html':'UTF-8'}" value="0"/>
{/if}
	<table class="table_grid" name="list_table">
		{if !$simple_header}
			<tr>
				<td style="vertical-align: bottom;">
					<span style="float: left;">
						{if $page > 1}
							<input type="image" src="../img/admin/list-prev2.gif" onclick="getE('submitFilter{$table|escape:'html':'UTF-8'}').value=1"/>&nbsp;
							<input type="image" src="../img/admin/list-prev.gif" onclick="getE('submitFilter{$table|escape:'html':'UTF-8'}').value={$page - 1|escape:'html':'UTF-8'}"/>
						{/if}
						{l s='Page' mod='orderedit'} <b>{$page|escape:'html':'UTF-8'}</b> / {$total_pages|escape:'html':'UTF-8'}
						{if $page < $total_pages}
							<input type="image" src="../img/admin/list-next.gif" onclick="getE('submitFilter{$table|escape:'html':'UTF-8'}').value={$page + 1|escape:'html':'UTF-8'}"/>&nbsp;
							<input type="image" src="../img/admin/list-next2.gif" onclick="getE('submitFilter{$table|escape:'html':'UTF-8'}').value={$total_pages|escape:'html':'UTF-8'}"/>
						{/if}
						| {l s='Display' mod='orderedit'}
						<select name="pagination" onchange="submit()">
							{* Choose number of results per page *}
							{foreach $pagination AS $value}
								<option value="{$value|intval}"{if $selected_pagination == $value} selected="selected" {elseif $selected_pagination == NULL && $value == $pagination[1]} selected="selected2"{/if}>{$value|intval}</option>
							{/foreach}
						</select>
						/ {$list_total|escape:'html':'UTF-8'} {l s='result(s)' mod='orderedit'}
					</span>
					<span style="float: right;">
						<input type="submit" name="submitReset{$table|escape:'html':'UTF-8'}" value="{l s='Reset' mod='orderedit'}" class="button" />
						<input type="submit" id="submitFilterButton{$table|escape:'html':'UTF-8'}" name="submitFilter" value="{l s='Filter' mod='orderedit'}" class="button" />
					</span>
					<span class="clear"></span>
				</td>
			</tr>
		{/if}
		<tr>
			<td{if $simple_header} style="border:none;"{/if}>
				<table
				{if $table_id} id={$table_id|escape:'html':'UTF-8'}{/if}
				class="table {if $table_dnd}tableDnD{/if} {$table|escape:'html':'UTF-8'}"
				cellpadding="0" cellspacing="0"
				style="width: 100%; margin-bottom:10px;"
				>
					<col width="10px" />
					{foreach $fields_display AS $key => $params}
						<col {if isset($params.width) && $params.width != 'auto'}width="{$params.width|escape:'html':'UTF-8'}px"{/if}/>
					{/foreach}
					{if $shop_link_type}
						<col width="80px" />
					{/if}
					{if $has_actions}
						<col width="52px" />
					{/if}
					<thead>
						<tr class="nodrag nodrop" style="height: 40px">
							<th class="center">
								{if $has_bulk_actions}
									<input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, '{$table|escape:'html':'UTF-8'}Box[]', this.checked)" />
								{/if}
							</th>
							{foreach $fields_display AS $key => $params}
								<th {if isset($params.align)} class="{$params.align|escape:'html':'UTF-8'}"{/if}>
									{if isset($params.hint)}<span class="hint" name="help_box">{$params.hint|escape:'html':'UTF-8'}<span class="hint-pointer">&nbsp;</span></span>{/if}
									<span class="title_box">
										{$params.title|escape:'html':'UTF-8'}
									</span>
									{if (!isset($params.orderby) || $params.orderby) && !$simple_header}
										<br />
										<a href="{$currentIndex|escape:'html':'UTF-8'}&{$table|escape:'html':'UTF-8'}Orderby={$key|urlencode|escape:'html':'UTF-8'}&{$table|escape:'html':'UTF-8'}Orderway=desc&token={$token|escape:'html':'UTF-8'}"><img border="0" src="../img/admin/down{if isset($order_by) && ($key == $order_by) && ($order_way == 'DESC')}_d{/if}.gif" /></a>
										<a href="{$currentIndex|escape:'html':'UTF-8'}&{$table|escape:'html':'UTF-8'}Orderby={$key|urlencode|escape:'html':'UTF-8'}&{$table|escape:'html':'UTF-8'}Orderway=asc&token={$token|escape:'html':'UTF-8'}"><img border="0" src="../img/admin/up{if isset($order_by) && ($key == $order_by) && ($order_way == 'ASC')}_d{/if}.gif" /></a>
									{elseif !$simple_header}
										<br />&nbsp;
									{/if}
								</th>
							{/foreach}
							{if $shop_link_type}
								<th>
									{if $shop_link_type == 'shop'}
										{l s='Shop' mod='orderedit'}
									{else}
										{l s='Group shop' mod='orderedit'}
									{/if}
									<br />&nbsp;
								</th>
							{/if}
							{if $has_actions}
								<th class="center">{l s='Actions' mod='orderedit'}{if !$simple_header}<br />&nbsp;{/if}</th>
							{/if}
						</tr>
 						{if !$simple_header}
						<tr class="nodrag nodrop filter {if $row_hover}row_hover{/if}" style="height: 35px;">
							<td class="center">
								{if $has_bulk_actions}
									--
								{/if}
							</td>

							{* Filters (input, select, date or bool) *}
							{foreach $fields_display AS $key => $params}
								<td {if isset($params.align)} class="{$params.align|escape:'html':'UTF-8'}" {/if}>
									{if isset($params.search) && !$params.search}
										--
									{else}
										{if $params.type == 'bool'}
											<select onchange="$('#submitFilterButton{$table|escape:'html':'UTF-8'}').focus();$('#submitFilterButton{$table|escape:'html':'UTF-8'}').click();" name="{$table|escape:'html':'UTF-8'}Filter_{$key|escape:'html':'UTF-8'}">
												<option value="">--</option>
												<option value="1" {if $params.value == 1} selected="selected" {/if}>{l s='Yes' mod='orderedit'}</option>
												<option value="0" {if $params.value == 0 && $params.value != ''} selected="selected" {/if}>{l s='No' mod='orderedit'}</option>
											</select>
										{elseif $params.type == 'date' || $params.type == 'datetime'}
											{l s='From' mod='orderedit'} <input type="text" class="filter datepicker" id="{$params.id_date|escape:'html':'UTF-8'}_0" name="{$params.name_date|escape:'html':'UTF-8'}[0]" value="{if isset($value.0)}$value.0{/if}"{if isset($params.width)} style="width:70px"{/if}/><br />
											{l s='To' mod='orderedit'} <input type="text" class="filter datepicker" id="{$params.id_date|escape:'html':'UTF-8'}_1" name="{$params.name_date|escape:'html':'UTF-8'}[1]" value="{if isset($value.1)}$value.1{/if}"{if isset($params.width)} style="width:70px"{/if}/>
										{elseif $params.type == 'select'}
											{if isset($params.filter_key)}
												<select onchange="$('#submitFilterButton{$table|escape:'html':'UTF-8'}').focus();$('#submitFilterButton{$table|escape:'html':'UTF-8'}').click();" name="{$table|escape:'html':'UTF-8'}Filter_{$params.filter_key|escape:'html':'UTF-8'}" {if isset($params.width)} style="width:{$params.width|escape:'html':'UTF-8'}px"{/if}>
													<option value="" {if $params.value == ''} selected="selected" {/if}>--</option>
													{if isset($params.list) && is_array($params.list)}
														{foreach $params.list AS $option_value => $option_display}
															<option value="{$option_value|escape:'html':'UTF-8'}" {if $option_display == $params.value ||  $option_value == $params.value} selected="selected"{/if}>{$option_display|escape:'html':'UTF-8'}</option>
														{/foreach}
													{/if}
												</select>
											{/if}
										{else}
											<input type="text" class="filter" name="{$table|escape:'html':'UTF-8'}Filter_{if isset($params.filter_key)}{$params.filter_key|escape:'html':'UTF-8'}{else}{$key|escape:'html':'UTF-8'}{/if}" value="{$params.value|escape:'htmlall':'UTF-8'}" {if isset($params.width) && $params.width != 'auto'} style="width:{$params.width|escape:'html':'UTF-8'}px"{else}style="width:95%"{/if} />
										{/if}
									{/if}
								</td>
							{/foreach}

							{if $shop_link_type}
								<td>--</td>
							{/if}
							{if $has_actions}
								<td class="center">--</td>
							{/if}
							</tr>
							<tr class="nodrag nodrop {if $row_hover}row_hover{/if}">
								<td colspan="{$fields_display|@count|escape:'html':'UTF-8' + 1}">
									<div id="advanced_search">
										<div id="advanced_search_left">
											<label>{l s='Product Name' mod='orderedit'}</label>
											<div class="margin-form">
												<input type="text" name="advanced_search[name]" value="{if isset($smarty.post.advanced_search) && isset($smarty.post.advanced_search.name)}{$smarty.post.advanced_search.name|escape:'html':'UTF-8'}{/if}" />
												<p class="clear">{l s='Search orders by name of one of the products in it' mod='orderedit'}</p>
											</div>
											<label>{l s='Product Reference' mod='orderedit'}</label>
											<div class="margin-form">
												<input type="text" name="advanced_search[reference]" value="{if isset($smarty.post.advanced_search) && isset($smarty.post.advanced_search.reference)}{$smarty.post.advanced_search.reference|escape:'html':'UTF-8'}{/if}" />
												<p class="clear">{l s='Search orders using reference of one of it\'s products' mod='orderedit'}</p>
											</div>
											<label>{l s='Product Supplier Reference' mod='orderedit'}</label>
											<div class="margin-form">
												<input type="text" name="advanced_search[supplier_reference]" value="{if isset($smarty.post.advanced_search) && isset($smarty.post.advanced_search.supplier_reference)}{$smarty.post.advanced_search.supplier_reference|escape:'html':'UTF-8'}{/if}" />
												<p class="clear">{l s='Search orders using supplier reference of one of it\'s products' mod='orderedit'}</p>
											</div>
											<label>{l s='Invoice Number' mod='orderedit'}</label>
											<div class="margin-form">
												<input type="text" name="advanced_search[invoice_num]" value="{if isset($smarty.post.advanced_search) && isset($smarty.post.advanced_search.invoice_num)}{$smarty.post.advanced_search.invoice_num|escape:'html':'UTF-8'}{/if}" />
											</div>
										</div>
										<div id="advanced_search_right">
										</div>
									</div>
								<td>
							</tr>
						{/if}
						</thead>
