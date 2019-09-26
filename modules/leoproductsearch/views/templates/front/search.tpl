{*
 *  Leo Theme for Prestashop 1.6.x
 *
 * @author    http://www.leotheme.com
 * @copyright Copyright (C) October 2013 LeoThemes.com <@emai:leotheme@gmail.com>
 *               <info@leotheme.com>.All rights reserved.
 * @license   GNU General Public License version 2
*}

{capture name=path}{l s='Search' mod='leoproductsearch'}{/capture}

<h1 
{if isset($instant_search) && $instant_search}id="instant_search_results"{/if} 
class="page-heading {if !isset($instant_search) || (isset($instant_search) && !$instant_search)} product-listing{/if}">
    {l s='Search' mod='leoproductsearch'}&nbsp;
    {if $nbProducts > 0}
        <span class="lighter">
            "{if isset($search_query) && $search_query}{$search_query|escape:'html':'UTF-8'}{elseif $search_tag}{$search_tag|escape:'html':'UTF-8'}{elseif $ref}{$ref|escape:'html':'UTF-8'}{/if}"
        </span>
    {/if}
    {if isset($instant_search) && $instant_search}
        <a href="#" class="close">
            {l s='Return to the previous page' mod='leoproductsearch'}
        </a>
    {else}
        <span class="heading-counter">
            {if $nbProducts == 1}{l s='%d result has been found.' sprintf=$nbProducts|intval mod='leoproductsearch'}{else}{l s='%d results have been found.' sprintf=$nbProducts|intval mod='leoproductsearch'}{/if}
        </span>
    {/if}
</h1>

{include file="$tpl_dir./errors.tpl"}
{if !$nbProducts}
	<p class="alert alert-warning">
		{if isset($search_query) && $search_query}
			{l s='No results were found for your search' mod='leoproductsearch'}&nbsp;"{if isset($search_query)}{$search_query|escape:'html':'UTF-8'}{/if}"
		{elseif isset($search_tag) && $search_tag}
			{l s='No results were found for your search' mod='leoproductsearch'}&nbsp;"{$search_tag|escape:'html':'UTF-8'}"
		{else}
			{l s='Please enter a search keyword' mod='leoproductsearch'}
		{/if}
	</p>
{else}
	{if isset($instant_search) && $instant_search}
        <p class="alert alert-info">
            {if $nbProducts == 1}{l s='%d result has been found.' sprintf=$nbProducts|intval mod='leoproductsearch'}{else}{l s='%d results have been found.' sprintf=$nbProducts|intval mod='leoproductsearch'}{/if}
        </p>
    {/if}
    <div class="content_sortPagiBar">
        <div class="sortPagiBar clearfix {if isset($instant_search) && $instant_search} instant_search{/if}">
            {include file="$tpl_dir./product-sort.tpl"}
            {if !isset($instant_search) || (isset($instant_search) && !$instant_search)}
                	{include file="./nbr-product-page.tpl"}
            {/if}
				</div>
    	<div class="top-pagination-content clearfix">
                	{include file="./product-compare.tpl"}
            {if !isset($instant_search) || (isset($instant_search) && !$instant_search)}
                {include file="./pagination.tpl"}
            {/if}
                </div>
			</div>
	{include file="$tpl_dir./product-list.tpl" products=$search_products}
    <div class="content_sortPagiBar">
    	<div class="bottom-pagination-content clearfix">
        	{include file="./product-compare.tpl"}
        	{if !isset($instant_search) || (isset($instant_search) && !$instant_search)}
                {include file="./pagination.tpl" paginationId='bottom'}
            {/if}
        </div>
    </div>
{/if}
