{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/list/list_header.tpl"}

{block name=leadin}
{if !empty($smarty.request.orders_search)}
    {assign var=show_filters value=1}    
    {assign var=filters_has_value value=1}    
{/if}
{if isset($updateOrderStatus_mode) && $updateOrderStatus_mode}
	<div class="panel">
		<div class="panel-heading">
			{l s='Choose an order status'}
		</div>
		<form action="{$REQUEST_URI}" method="post">
			<div class="radio">
				<label for="id_order_state">
					<select id="id_order_state" name="id_order_state">
{foreach from=$order_statuses item=order_status_name key=id_order_state}
						<option value="{$id_order_state|intval}">{$order_status_name|escape}</option>
{/foreach}
					</select>
				</label>
			</div>
{foreach $POST as $key => $value}
	{if is_array($value)}
		{foreach $value as $val}
			<input type="hidden" name="{$key|escape:'html':'UTF-8'}[]" value="{$val|escape:'html':'UTF-8'}" />
		{/foreach}
	{elseif strtolower($key) != 'id_order_state'}
			<input type="hidden" name="{$key|escape:'html':'UTF-8'}" value="{$value|escape:'html':'UTF-8'}" />

	{/if}
{/foreach}
			<div class="panel-footer">
				<button type="submit" name="cancel" class="btn btn-default">
					<i class="icon-remove"></i>
					{l s='Cancel'}
				</button>
				<button type="submit" class="btn btn-default" name="submitUpdateOrderStatus">
					<i class="icon-check"></i>
					{l s='Update Order Status'}
				</button>
			</div>
		</form>
	</div>
{/if}
{/block}
{block name="preTable"}
    <table style="width:100%">
        <tr>
            <td>{l s='Search:'}&nbsp;&nbsp;</td>
            <td style="width:100%"><input type="text" name="orders_search" value="{if isset($smarty.request.orders_search)}{$smarty.request.orders_search}{/if}" autocomplete="off" />
                <a href="#" onClick="$('#searchOptions').slideToggle();">{l s='Search options'}</a>
            </td>
        </tr>
        <tr id="searchOptions" style="display:none">
              <td colspan="2">{l s='Search in fileds:'}
                  <input type="hidden" name="scfg[customer_name]" value="0" />
              <input type="checkbox" name="scfg[customer_name]" value="1" {if !isset($smarty.request.scfg.customer_name) || !empty($smarty.request.scfg.customer_name)}checked="checked"{/if} autocomplete="off"> {l s='Customer Name'}&nbsp;&nbsp;
              <input type="hidden" name="scfg[customer_email]" value="0" />
              <input type="checkbox" name="scfg[customer_email]" value="1" {if !isset($smarty.request.scfg.customer_email) || !empty($smarty.request.scfg.customer_email)}checked="checked"{/if} autocomplete="off"> {l s='Customer Email'}&nbsp;&nbsp;
              <input type="hidden" name="scfg[customer_address]" value="0" />
              <input type="checkbox" name="scfg[customer_address]" value="1" {if !isset($smarty.request.scfg.customer_address) || !empty($smarty.request.scfg.customer_address)}checked="checked"{/if} autocomplete="off"> {l s='Customer address'}&nbsp;&nbsp;
              <input type="hidden" name="scfg[customer_phone]" value="0" />
               <input type="checkbox" name="scfg[customer_phone]" value="1" {if !isset($smarty.request.scfg.customer_phone) || !empty($smarty.request.scfg.customer_phone)}checked="checked"{/if} autocomplete="off"> {l s='Customer Phone'}&nbsp;&nbsp;
               {*<input type="checkbox" name="scfg[order_id]" value="1" {if !isset($smarty.request.scfg.order_id) || !empty($smarty.request.scfg.order_id)}checked="checked"{/if} autocomplete="off"> {l s='Order id'}&nbsp;&nbsp;*}
               <input type="hidden" name="scfg[product_id]" value="0" />
               <input type="checkbox" name="scfg[product_id]" value="1" {if !isset($smarty.request.scfg.product_id) || !empty($smarty.request.scfg.product_id)}checked="checked"{/if} autocomplete="off"> {l s='Product id'}&nbsp;&nbsp;
               <input type="hidden" name="scfg[supplier_reference]" value="0" />
               <input type="checkbox" name="scfg[supplier_reference]" value="1" {if !isset($smarty.request.scfg.supplier_reference) || !empty($smarty.request.scfg.supplier_reference)}checked="checked"{/if} autocomplete="off"> {l s='Supplier reference'}&nbsp;&nbsp;
               <input type="hidden" name="scfg[product_name]" value="0" />
               <input type="checkbox" name="scfg[product_name]" value="1" {if !isset($smarty.request.scfg.product_name) || !empty($smarty.request.scfg.product_name)}checked="checked"{/if} autocomplete="off"> {l s='Product name'}&nbsp;&nbsp;
               <input type="hidden" name="scfg[invoice_id]" value="0" />
               <input type="checkbox" name="scfg[invoice_id]" value="1" {if !isset($smarty.request.scfg.invoice_id) || !empty($smarty.request.scfg.invoice_id)}checked="checked"{/if} autocomplete="off"> {l s='Invoice id'}&nbsp;&nbsp;
               <input type="hidden" name="scfg[supplier_name]" value="0" />
               <input type="checkbox" name="scfg[supplier_name]" value="1" {if !isset($smarty.request.scfg.supplier_name) || !empty($smarty.request.scfg.supplier_name)}checked="checked"{/if} autocomplete="off"> {l s='Supplier name'}&nbsp;&nbsp;
               <input type="hidden" name="scfg[country_name]" value="0" />
               <input type="checkbox" name="scfg[country_name]" value="1" {if !isset($smarty.request.scfg.country_name) || !empty($smarty.request.scfg.country_name)}checked="checked"{/if} autocomplete="off"> {l s='Country name'}&nbsp;&nbsp;
               
               <input type="hidden" name="scfg[company_name]" value="0" />
               <input type="checkbox" name="scfg[company_name]" value="1" {if !isset($smarty.request.scfg.company_name) || !empty($smarty.request.scfg.company_name)}checked="checked"{/if} autocomplete="off"> {l s='Company name'}&nbsp;&nbsp;

               {*<input type="checkbox" name="scfg[tracking_number]" value="1" {if !isset($smarty.request.scfg.tracking_number) || !empty($smarty.request.scfg.tracking_number)}checked="checked"{/if} autocomplete="off"> {l s='Tracking number'}*}
              </td>
        </tr>
    </table>
    <br><br>
<div id="searchByLetterPanel">
	<div class="btn-group" role="group" aria-label="Search" data-toggle="buttons">
	{foreach $searchbar_letters as $letter}
	{if isset($search_char_selected) && ($search_char_selected == $letter)}
	{$input_checked=true}
	{else}
	{$input_checked=false}
	{/if}
		<label class="btn {if $input_checked}btn-primary active{else}btn-default{/if}">
		<input {if $input_checked}checked="checked"{/if} type="radio" name="search_char" value="{$letter}" class="search-by-letter" data-char="{$letter}"> {$letter}
		</label>
	{/foreach}
		<label class="btn btn-default">
		<input type="radio" name="search_char" value="-" class="search-by-letter"> *
		</label>
	
	</div>
</div>
<br><br>
<script>
$(function(){
	$('#searchByLetterPanel').on('change', 'input.search-by-letter', function(){
		$(this).parents('form').submit();
	});
});
</script>
    
{/block}