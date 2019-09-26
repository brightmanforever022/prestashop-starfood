{*
 *  Leo Theme for Prestashop 1.6.x
 *
 * @author    http://www.leotheme.com
 * @copyright Copyright (C) October 2013 LeoThemes.com <@emai:leotheme@gmail.com>
 *               <info@leotheme.com>.All rights reserved.
 * @license   GNU General Public License version 2
*}

{if isset($orderby) AND isset($orderway)}
<ul class="display hidden-xs">
	<li class="display-title">{l s='View:' mod='leoproductsearch'}</li>
    <li id="grid"><a rel="nofollow" href="#" title="{l s='Grid' mod='leoproductsearch'}"><i class="icon-th-large"></i>{l s='Grid' mod='leoproductsearch'}</a></li>
    <li id="list"><a rel="nofollow" href="#" title="{l s='List' mod='leoproductsearch'}"><i class="icon-th-list"></i>{l s='List' mod='leoproductsearch'}</a></li>
</ul>
{* On 1.5 the var request is setted on the front controller. The next lines assure the retrocompatibility with some modules *}
{if !isset($request)}
	<!-- Sort products -->
	{if isset($smarty.get.id_category) && $smarty.get.id_category}
		{assign var='request' value=$link->getPaginationLink('category', $category, false, true)}
	{elseif isset($smarty.get.id_manufacturer) && $smarty.get.id_manufacturer}
		{assign var='request' value=$link->getPaginationLink('manufacturer', $manufacturer, false, true)}
	{elseif isset($smarty.get.id_supplier) && $smarty.get.id_supplier}
		{assign var='request' value=$link->getPaginationLink('supplier', $supplier, false, true)}
	{else}
		{assign var='request' value=$link->getPaginationLink(false, false, false, true)}
	{/if}
{/if}
<form id="productsSortForm{if isset($paginationId)}_{$paginationId}{/if}" action="{$request|escape:'html':'UTF-8'}" class="productsSortForm">
	<div class="select selector1">
		<label for="selectProductSort{if isset($paginationId)}_{$paginationId}{/if}">{l s='Sort by' mod='leoproductsearch'}</label>
		<select id="selectProductSort{if isset($paginationId)}_{$paginationId}{/if}" class="selectProductSort form-control">
			<option value="{$orderbydefault|escape:'html':'UTF-8'}:{$orderwaydefault|escape:'html':'UTF-8'}" {if $orderby eq $orderbydefault}selected="selected"{/if}>--</option>
			{if !$PS_CATALOG_MODE}
				<option value="price:asc" {if $orderby eq 'price' AND $orderway eq 'asc'}selected="selected"{/if}>{l s='Price: Lowest first' mod='leoproductsearch'}</option>
				<option value="price:desc" {if $orderby eq 'price' AND $orderway eq 'desc'}selected="selected"{/if}>{l s='Price: Highest first' mod='leoproductsearch'}</option>
			{/if}
			<option value="name:asc" {if $orderby eq 'name' AND $orderway eq 'asc'}selected="selected"{/if}>{l s='Product Name: A to Z' mod='leoproductsearch'}</option>
			<option value="name:desc" {if $orderby eq 'name' AND $orderway eq 'desc'}selected="selected"{/if}>{l s='Product Name: Z to A' mod='leoproductsearch'}</option>
			{if $PS_STOCK_MANAGEMENT && !$PS_CATALOG_MODE}
				<option value="quantity:desc" {if $orderby eq 'quantity' AND $orderway eq 'desc'}selected="selected"{/if}>{l s='In stock' mod='leoproductsearch'}</option>
			{/if}
			<option value="reference:asc" {if $orderby eq 'reference' AND $orderway eq 'asc'}selected="selected"{/if}>{l s='Reference: Lowest first' mod='leoproductsearch'}</option>
			<option value="reference:desc" {if $orderby eq 'reference' AND $orderway eq 'desc'}selected="selected"{/if}>{l s='Reference: Highest first' mod='leoproductsearch'}</option>
		</select>
	</div>
</form>
<!-- /Sort products -->
	{if !isset($paginationId) || $paginationId == ''}
		{addJsDef request=$request}
	{/if}
{/if}
