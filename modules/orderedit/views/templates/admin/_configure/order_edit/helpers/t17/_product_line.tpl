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
*  @version  Release: $Revision: 17728 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{* Assign product price *}
{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
	{assign var=product_price value=($product['unit_price_tax_excl'] + $product['ecotax'])}
{else}
	{assign var=product_price value=$product['unit_price_tax_incl']}
{/if}
{if ($product['product_quantity'] > $product['customizationQuantityTotal'])}
{*var_dump($product)*}
<tr {if isset($product.image) && $product.image->id && isset($product.image_size)} height="{$product['image_size'][1]|escape:'html':'UTF-8' + 7}"{/if} id="line_{$index|escape:'html':'UTF-8'}" class="product_line{if isset($unsaved) && $unsaved} unsaved{/if}" data-pr="{$product.product_id|escape:'html':'UTF-8'}-{$product.product_attribute_id|escape:'html':'UTF-8'}">
	<td align="center">{if isset($product.image) && $product.image->id}{$product.image_tag nofilter}{/if}</td>
	<td>
		<input type="hidden" name="taxRulesGroupId" rel="taxRulesGroupId" value="{$product.id_tax_rules_group|escape:'html':'UTF-8'}" />
		<input type="hidden" name="productId" rel="productId" value="{$product.product_id|escape:'html':'UTF-8'}" />
		<input type="hidden" name="productAttributeId" rel="productAttributeId" value="{$product.product_attribute_id|escape:'html':'UTF-8'}" />
		<input type="hidden" name="productWarehouseId" rel="productWarehouseId" value="{$product.id_warehouse|escape:'html':'UTF-8'}" />
		<input type="hidden" name="isDeleted" class="isDeleted" rel="isDeleted" value="0" />
		<input type="hidden" name="productIndex" class="productIndex" rel="productIndex" value="{$index|escape:'html':'UTF-8'}" />
		{*if isset($product.invoice_selected) && ! sizeof($invoices_collection)}
		<input type="hidden" name="product_invoice" rel="editProductInvoice" value="{$product.invoice_selected|escape:'html':'UTF-8'}" />
		{/if*}
		<div class="editable">
			{if $can_edit}
			<p class="customVal" style="display:none;">
				<span></span>
			</p>
			{/if}
			<p class="displayVal">
				<span class="productName">{$product.product_name|escape:'html':'UTF-8'}</span>
			</p>
			{if $can_edit}
			<p class="realVal" style="display:none;">
				<span class="product_name_edit">
					<input type="text" name="product_name" class="edit_product_name" rel="productNameEdit" value="{$product['product_name']|escape:'html':'UTF-8'}" />
				</span>
			</p>
			{/if}
		</div>
	</td>
	<td>
		<div class="editable">
			{if $can_edit}
			<p class="customVal" style="display:none;">
				<span></span>
			</p>
			{/if}
			<p class="displayVal">
				<span class="productRef">{if $product['product_reference'] == ''}--{else}{$product['product_reference']|escape:'html':'UTF-8'}{/if}</span>
			</p>
			{if $can_edit}
			<p class="realVal" style="display:none;">
				<span class="product_ref_edit">
					<input type="text" name="product_ref" class="edit_ref_name" rel="productReferenceEdit" value="{$product['product_reference']|escape:'html':'UTF-8'}" />
				</span>
			</p>
			{/if}
		</div>
	</td>
	<td>
		<div class="editable">
			{if $can_edit}
			<p class="customVal" style="display:none;">
				<span></span>
			</p>
			{/if}
			<p class="displayVal">
				<span class="product_sup_ref_show">{if $product['product_supplier_reference'] == ''}--{else}{$product['product_supplier_reference']|escape:'html':'UTF-8'}{/if}</span>
			</p>
			{if $can_edit}
			<p class="realVal" style="display:none;">
				<span class="product_sup_ref_edit">
					<input type="text" name="product_sup_ref" class="edit_sup_ref_name" rel="productSupplierReferenceEdit" value="{$product['product_supplier_reference']|escape:'html':'UTF-8'}" />
				</span>
			</p>
			{/if}
		</div>
	</td>
	<td>
		<div class="editable">
			{if $can_edit}
			<p class="customVal" style="display:none;">
				<span></span>
			</p>
			{/if}
			<p class="displayVal">
				<span class="productWeight">{$product.product_weight|escape:'html':'UTF-8'}</span>
			</p>
			{if $can_edit}
			<p class="realVal" style="display:none;">
				<span class="product_weight_edit">
					<input type="text" name="product_weight" class="edit_product_weight" rel="productWeightEdit" value="{$product['product_weight']|escape:'html':'UTF-8'}" />
				</span>
			</p>
			{/if}
		</div>
	</td>
	<td align="center">
		<div class="editable">
			{if $can_edit}
			<p class="customVal" style="display:none;">
				<span></span>
			</p>
			{/if}
			<p class="displayVal">
				<span class="product_reduction_per_show">{$product['reduction_percent']|escape:'html':'UTF-8'}</span>
			</p>
			{if $can_edit}
			<p class="realVal" style="display:none;">
				<span class="realVal product__reduction_per_edit" style="display:none;">
					<input type="text" name="product_reduction_per" class="edit_product_reduction_per" rel="productReductionPerEdit" data-opp="{Tools::ps_round($product['unit_price_tax_excl'], 6)|escape:'html':'UTF-8'}" value="{$product['reduction_percent']|htmlentities|escape:'html':'UTF-8'}" size="2" />
				</span>
			</p>
			{/if}
		</div>
	</td>
	<td align="center">
		<div class="editable">
			{if $can_edit}
			<p class="customVal" style="display:none;">
				<span></span>
			</p>
			{/if}
			<p class="displayVal">
				<span class="displayVal product_price_show">{Tools::displayPrice($product_price, (int)$currency->id)|escape:'htmlall':'UTF-8'}</span>
			</p>
			{if $can_edit}
			<p class="realVal" style="display:none;">
				<span class="product_price_edit">
					<input type="hidden" name="product_id_order_detail" class="edit_product_id_order_detail" rel="idOrderDetail" value="{$product['id_order_detail']|escape:'html':'UTF-8'}" />
					{if $currency->sign % 2}{$currency->sign|escape:'html':'UTF-8'}{/if}
					<input type="text" name="product_price_tax_excl" class="edit_product_price_tax_excl edit_product_price" rel="productPriceEdit" value="{Tools::ps_round($product['unit_price_tax_excl'], 6)|escape:'html':'UTF-8'}" size="5" /> {if !($currency->sign % 2)}{$currency->sign|escape:'html':'UTF-8'}{/if} {l s='tax excl.' mod='orderedit'}<br />
					{if $currency->sign % 2}{$currency->sign|escape:'html':'UTF-8'}{/if}
					<input type="text" name="product_price_tax_incl" class="edit_product_price_tax_incl edit_product_price" rel="productPriceWtEdit" pwt="{Product::getPriceStatic($product['product_id'], true, $product['product_attribute_id'])|escape:'html':'UTF-8'}" value="{Tools::ps_round($product['unit_price_tax_incl'], 2)|escape:'html':'UTF-8'}" size="5" /> {if !($currency->sign % 2)}{$currency->sign|escape:'html':'UTF-8'}{/if} {l s='tax incl.' mod='orderedit'}
				</span>
			</p>
			{/if}
		</div>
	</td>
	<td align="center">
		<div class="editable">
			{if $can_edit}
			<p class="customVal" style="display:none;">
				<span></span>
			</p>
			{/if}
			<p class="displayVal">
				<span class="product_tax_rate_show">{Tools::ps_round($product['tax_rate'], 2)|escape:'html':'UTF-8'}%</span>
			</p>
			{if $can_edit}
			<p class="realVal" style="display:none;">
				<span class="product_tax_rate_edit">
					{*<input type="hidden" name="product_tax_rate" class="edit_product_tax_rate edit_product_price" rel="productTaxEdit" value="{Tools::ps_round($product['tax_rate'], 2)|escape:'html':'UTF-8'}" size="5" />*}
                    <select name="product_tax_rate" class="edit_product_tax_rate edit_product_price" rel="productTaxEdit">
                        <option value="0:0">0 %</option>
                        {foreach from=$taxes item=tax key=tax_key}
                            <option value="{$tax.id_tax|escape:'html':'UTF-8'}:{$tax.rate|escape:'html':'UTF-8'}"{if $tax.id_tax == $product.id_tax}selected{/if}>{$tax.name|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    </select>
				</span>
			</p>
			{/if}
		</div>
	</td>
	<td align="center" class="productQuantity">
		<div class="editable">
			{if $can_edit}
			<p class="customVal" style="display:none;">
				<span></span>
			</p>
			{/if}
			<p class="displayVal">
				<span class="product_quantity_show">{if array_key_exists('customized_product_quantity', $product)}{((int)$product['product_quantity'] - (int)$product['customized_product_quantity'])|escape:'html':'UTF-8'}{else}{(int)$product['product_quantity']|escape:'html':'UTF-8'}{/if}</span>
			</p>
			{if $can_edit}
			<p class="realVal" style="display:none;">
				<span class="realVal product_quantity_edit" style="display:none;">
					<input type="text" name="product_quantity" class="edit_product_quantity" rel="productQtyEdit" value="{if array_key_exists('customized_product_quantity', $product)}{((int)$product['product_quantity'] - (int)$product['customized_product_quantity'])|escape:'html':'UTF-8'}{else}{(int)$product['product_quantity']|escape:'html':'UTF-8'}{/if}" size="2" autocomplete="off" />
				</span>
			</p>
			{/if}
		</div>
	</td>
	<td align="center" class="total_product">
		{Tools::displayPrice((Tools::ps_round($product_price, 2) * ($product['product_quantity'] - $product['customizationQuantityTotal'])), (int)$currency->id)|escape:'htmlall':'UTF-8'}
	</td>
	<td colspan="2" style="display: none;" class="add_product_fields">&nbsp;</td>
	<td align="center" class="cancelCheck standard_refund_fields current-edit" style="display:none">
		<input type="hidden" name="totalQtyReturn" id="totalQtyReturn" rel="totalQtyReturn" value="{$product['product_quantity_return']|escape:'html':'UTF-8'}" />
		<input type="hidden" name="totalQty" id="totalQty" rel="totalQty" value="{$product['product_quantity']|escape:'html':'UTF-8'}" />
		<input type="hidden" name="productName" id="productName" rel="productName" value="{$product['product_name']|escape:'html':'UTF-8'}" />
	{if ((!$order->hasBeenDelivered() OR Configuration::get('PS_ORDER_RETURN')) AND (int)($product['product_quantity_return']) < (int)($product['product_quantity']) AND isset($product['product_quantity_in_stock']) AND isset($product['customizationQuantityTotal']) AND isset($product['product_quantity_reinjected']))}
		<input type="checkbox" rel="id_order_detail" name="id_order_detail[{$product['id_order_detail']|escape:'html':'UTF-8'}]" id="id_order_detail[{$product['id_order_detail']|escape:'html':'UTF-8'}]" value="{$product['id_order_detail']|escape:'html':'UTF-8'}" onchange="setCancelQuantity(this, {$product['id_order_detail']|escape:'html':'UTF-8'}, {($product['product_quantity_in_stock'] - $product['customizationQuantityTotal'] - $product['product_quantity_reinjected'])|escape:'html':'UTF-8'})" {if ($product['product_quantity_return'] + $product['product_quantity_refunded'] >= $product['product_quantity'])}disabled="disabled" {/if}/>
	{else}
		--
	{/if}
	</td>
	<td class="cancelQuantity standard_refund_fields current-edit" style="display:none">
	{if ($product['product_quantity_return'] + $product['product_quantity_refunded'] >= $product['product_quantity'])}
		<input type="hidden" rel="cancelQuantity" name="cancelQuantity[{$product['id_order_detail']|escape:'html':'UTF-8'}]" value="0" />
	{elseif (!$order->hasBeenDelivered() OR Configuration::get('PS_ORDER_RETURN'))}
		<input type="text" id="cancelQuantity_{$product['id_order_detail']|escape:'html':'UTF-8'}" rel="cancelQuantity" name="cancelQuantity[{$product['id_order_detail']|escape:'html':'UTF-8'}]" size="2" onclick="selectCheckbox(this);" value="" />
	{/if}

	{if $product['customizationQuantityTotal']}
		{assign var=productQuantity value=($product['product_quantity']-$product['customizationQuantityTotal'])}
	{else}
		{assign var=productQuantity value=$product['product_quantity']}
	{/if}

	{if ($order->hasBeenDelivered())}
		{$product['product_quantity_refunded']|escape:'html':'UTF-8'}/{$productQuantity-$product['product_quantity_refunded']|escape:'html':'UTF-8'}
	{elseif ($order->hasBeenPaid())}
		{$product['product_quantity_return']|escape:'html':'UTF-8'}/{$productQuantity|escape:'html':'UTF-8'}
	{else}
		0/{$productQuantity|escape:'html':'UTF-8'}
	{/if}
	</td>
	<td class="partial_refund_fields current-edit" style="text-align:left;display:none">
		<div style="width:40%;margin-top:5px;float:left">{l s='Quantity:' mod='orderedit'}</div> <div style="width:60%;margin-top:2px;float:left"><input onchange="checkPartialRefundProductQuantity(this)" type="text" size="3" name="partialRefundProductQuantity[{$product['id_order_detail']|escape:'html':'UTF-8'}]" value="0" /> 0/{$productQuantity-$product['product_quantity_refunded']|escape:'html':'UTF-8'}</div>
		<div style="width:40%;margin-top:5px;float:left">{l s='Amount:' mod='orderedit'}</div> <div style="width:60%;margin-top:2px;float:left">{$currency->prefix|escape:'html':'UTF-8'}<input onchange="checkPartialRefundProductAmount(this)" type="text" size="3" name="partialRefundProduct[{$product['id_order_detail']|escape:'html':'UTF-8'}]" /> {$currency->suffix|escape:'html':'UTF-8'}</div> {if !empty($product['amount_refund']) && $product['amount_refund'] > 0}({l s='%s refund' sprintf=$product['amount_refund'] mod='orderedit'}){/if}
		<input type="hidden" value="{$product['quantity_refundable']|escape:'html':'UTF-8'}" rel="quantity_refundable" class="partialRefundProductQuantity" />
		<input type="hidden" value="{$product['amount_refundable']|escape:'html':'UTF-8'}" rel="amount_refundable" class="partialRefundProductAmount" />
	</td>
        {*
	<td class="product_invoice" colspan="2" style="display: none;text-align:center;">
		{if sizeof($invoices_collection)}
		<select name="product_invoice" class="edit_product_invoice" rel="editProductInvoice">
			{foreach from=$invoices_collection item=invoice}
			<option value="{$invoice->id|escape:'html':'UTF-8'}" {if $invoice->id == $product['id_order_invoice']}selected="selected"{/if}>#{Configuration::get('PS_INVOICE_PREFIX', $current_id_lang)|escape:'html':'UTF-8'}{'%06d'|sprintf:$invoice->number}</option>
			{/foreach}
		</select>
		{else}
		&nbsp;
		{/if}
	</td>*}
	<td class="product_action" style="text-align:right">
		<a href="#" class="cancel_product_change_link" style="display: none;"><img src="../img/admin/disabled.gif" alt="{l s='Cancel' mod='orderedit'}" /></a>
		<a href="#" class="delete_product_line btn btn-default"><i class="icon-trash"></i> {l s='Delete' mod='orderedit'}</a>
	</td>
</tr>
{/if}
