{*
* 2007-2016 PrestaShop
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
{if $product['customizedDatas']}
{* Assign product price *}
{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
	{assign var=product_price value=($product['unit_price_tax_excl'] + $product['ecotax'])}
{else}
	{assign var=product_price value=$product['unit_price_tax_incl']}
{/if}
{*var_dump($product)*}
	<tr class="customized customized-{$product['id_order_detail']|intval} product-line-row product_line customized-main" id="line_{$index|escape:'html':'UTF-8'}" data-custom-main="{$product['id_order_detail']|intval}"  data-pr="{$product.product_id|escape:'html':'UTF-8'}-{$product.product_attribute_id|escape:'html':'UTF-8'}">
		<td>
			<input type="hidden" class="edit_product_id_order_detail" value="{$product['id_order_detail']|intval}" />
			{if isset($product['image']) && $product['image']->id|intval}{$product['image_tag']|escape:'quotes':'UTF-8'}{else}--{/if}
		</td>
		<td>
			<input type="hidden" name="taxRulesGroupId" rel="taxRulesGroupId" value="{$product.id_tax_rules_group|escape:'html':'UTF-8'}" />
			<input type="hidden" name="productId" rel="productId" value="{$product.product_id|escape:'html':'UTF-8'}" />
			<input type="hidden" name="productAttributeId" rel="productAttributeId" value="{$product.product_attribute_id|escape:'html':'UTF-8'}" />
			<input type="hidden" name="productWarehouseId" rel="productWarehouseId" value="{$product.id_warehouse|escape:'html':'UTF-8'}" />
			<input type="hidden" name="isDeleted" class="isDeleted" rel="isDeleted" value="0" />
			<input type="hidden" name="productIndex" class="productIndex" rel="productIndex" value="{$index|escape:'html':'UTF-8'}" />
			{if isset($product.invoice_selected) && ! sizeof($invoices_collection)}
			<input type="hidden" name="product_invoice" rel="editProductInvoice" value="{$product.invoice_selected|escape:'html':'UTF-8'}" />
			{/if}

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
					<span class="displayVal product_price_show" wt="{Tools::ps_round($product['unit_price_tax_incl'], 2)|escape:'html':'UTF-8'}" pwt="{Product::getPriceStatic($product['product_id'], true, $product['product_attribute_id'])|escape:'html':'UTF-8'}">{Tools::displayPrice($product_price, (int)$currency->id)|escape:'htmlall':'UTF-8'}</span>
				</p>
				{if $can_edit}
				<p class="realVal" style="display:none;">
					<span class="product_price_edit">
						<input type="hidden" name="product_id_order_detail" class="edit_product_id_order_detail" rel="idOrderDetail" value="{$product['id_order_detail']|escape:'html':'UTF-8'}" />
						{if $currency->sign % 2}{$currency->sign|escape:'html':'UTF-8'}{/if}
						<input type="text" name="product_price_tax_excl" class="edit_product_price_tax_excl edit_product_price" rel="productPriceEdit" value="{Tools::ps_round($product['unit_price_tax_excl'], 2)|escape:'html':'UTF-8'}" size="5" /> {if !($currency->sign % 2)}{$currency->sign|escape:'html':'UTF-8'}{/if} {l s='tax excl.' mod='orderedit'}<br />
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
				<p class="displayVal" style="margin: 0;">
					<span class="product_quantity_show customQ">{$product['customizationQuantityTotal']|escape:'html':'UTF-8'}</span>
				</p>
				{if $can_edit}
				<p class="realVal" style="display:none;">
					<span class="realVal product_quantity_edit" style="display:none;">
						<input type="text" rel="productCustomAllQtyEdit" class="edit_product_quantity" value="{$product['customizationQuantityTotal']|escape:'html':'UTF-8'}" size="2" />
					</span>
				</p>
				{/if}
		</td>

		<td align="center" class="total_product">
			{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
				{Tools::displayPrice(Tools::ps_round($product['product_price'] * $product['customizationQuantityTotal'], 2), (int)$currency->id)|escape:'htmlall':'UTF-8'}
			{else}
				{Tools::displayPrice(Tools::ps_round($product['product_price_wt'] * $product['customizationQuantityTotal'], 2), (int)$currency->id)|escape:'htmlall':'UTF-8'}
			{/if}
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
		</td>
		<td class="product_action" style="text-align:right" q>
			<a href="#" class="cancel_product_change_link" style="display: none;"><img src="../img/admin/disabled.gif" alt="{l s='Cancel' mod='orderedit'}" /></a>
		</td>

	</tr>

	{foreach $product['customizedDatas'] as $customizationPerAddress}
		{foreach $customizationPerAddress as $customizationId => $customization}
			<tr class="customized customized-{$product['id_order_detail']|intval} customized-prop" id="customline_{$index|escape:'html':'UTF-8'}" data-custom-prop="{$product['id_order_detail']|intval}">
				<td colspan="2">
				<input type="hidden" class="edit_product_id_order_detail" value="{$product['id_order_detail']|intval}" />
					<div class="form-horizontal">
						{foreach $customization.datas as $type => $datas}
							{if ($type == Product::CUSTOMIZE_FILE)}
								{foreach from=$datas item=data}
									<div class="form-group">
										<span class="col-lg-4 control-label"><strong>{if $data['name']}{$data['name']|escape:'html':'UTF-8'}{else}{l s='Picture #' mod='orderedit'}{$data@iteration|escape:'html':'UTF-8'}{/if}</strong></span>
										<div class="col-lg-8">
											<a href="displayImage.php?img={$data['value']|intval}&amp;name={$order->id|escape:'quotes':'UTF-8'}-file{$data@iteration|escape:'html':'UTF-8'}" class="_blank">
												<img class="img-thumbnail" src="{$smarty.const._THEME_PROD_PIC_DIR_|escape:'quotes':'UTF-8'}{$data['value']|escape:'html':'UTF-8'}_small" alt=""/>
											</a>
										</div>
									</div>
								{/foreach}
							{elseif ($type == Product::CUSTOMIZE_TEXTFIELD)}
								{foreach from=$datas item=data}
									<div class="form-group">
										<span class="col-lg-4 control-label"><strong>{if $data['name']}{l s='%s' sprintf=$data['name'] mod='orderedit'}{else}{l s='Text #%s' sprintf=$data@iteration mod='orderedit'}{/if}</strong></span>
										<div class="col-lg-8">

											<div class="editable">
												{if $can_edit}
												<p class="customVal" style="display:none;">
													<span></span>
												</p>
												{/if}
												<p class="displayVal">
													<span class="customdata_{$data['id_customization']|escape:'html':'UTF-8'}-{$data['index']|escape:'html':'UTF-8'}_show">{if $data['value'] == ''}--{else}{$data['value']|escape:'html':'UTF-8'}{/if}</span>
												</p>
												{if $can_edit}
												<p class="realVal" style="display:none;">
													<span class="customdata_{$data['id_customization']|escape:'html':'UTF-8'}-{$data['index']|escape:'html':'UTF-8'}_edit">
														<input type="text" name="customdata_{$data['id_customization']|escape:'html':'UTF-8'}-{$data['index']|escape:'html':'UTF-8'}" class="edit_customdata edit_customdata_{$data['id_customization']|escape:'html':'UTF-8'}-{$data['index']|escape:'html':'UTF-8'}" rel="customdataEdit" id-cus="{$data['id_customization']|escape:'html':'UTF-8'}" id-index="{$data['index']|escape:'html':'UTF-8'}" value="{$data['value']|escape:'html':'UTF-8'}" />
													</span>
												</p>
												{/if}
											</div>

											<!-- <p class="form-control-static">{$data['value']|escape:'html':'UTF-8'}</p> -->
										</div>
									</div>
								{/foreach}
							{/if}
						{/foreach}
					</div>
				</td>
				<td align="center">-</td>
				<td>-</td>
				<td align="center">-</td>
				<td align="center">-</td>
				<td align="center">-</td>
				<td align="center">-</td>
				<td class="productQuantity text-center">
					<div class="editable">
						{if $can_edit}
						<p class="customVal" style="display:none;">
							<span></span>
						</p>
						{/if}
						<p class="displayVal">
							<span class="product_quantity_show{if (int)$customization['quantity'] > 1} red bold{/if}">{$customization['quantity']|escape:'html':'UTF-8'}</span>
						</p>
						{if $can_edit}
						<p class="realVal" style="display:none;">
							<span class="realVal product_quantity_edit" style="display:none;">
								<input type="text" name="product_quantity[{$customizationId|intval}]" id-cus="{$data['id_customization']|escape:'html':'UTF-8'}" rel="productCustomQtyEdit" class="edit_product_quantity edit_customdata" value="{$customization['quantity']|htmlentities}" size="2" />
							</span>
						</p>
						{/if}
					</div>
				</td>

				<td class="total_product" align="center">
					{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
						{Tools::displayPrice(Tools::ps_round($product['product_price'] * $customization['quantity'], 2), (int)$currency->id)|escape:'htmlall':'UTF-8'}
					{else}
						{Tools::displayPrice(Tools::ps_round($product['product_price_wt'] * $customization['quantity'], 2), (int)$currency->id)|escape:'htmlall':'UTF-8'}
					{/if}
				</td>
				<td class="cancelCheck standard_refund_fields current-edit" style="display:none">
					<input type="hidden" name="totalQtyReturn" id="totalQtyReturn" value="{$customization['quantity_returned']|intval}" />
					<input type="hidden" name="totalQty" id="totalQty" value="{$customization['quantity']|intval}" />
					<input type="hidden" name="productName" id="productName" value="{$product['product_name']|escape:'html':'UTF-8'}" />
					{if ((!$order->hasBeenDelivered() OR Configuration::get('PS_ORDER_RETURN')) AND (int)($customization['quantity_returned']) < (int)($customization['quantity']))}
						<input type="checkbox" name="id_customization[{$customizationId|intval}]" id="id_customization[{$customizationId|intval}]" value="{$product['id_order_detail']|intval}" onchange="setCancelQuantity(this, {$customizationId|intval}, {($customization['quantity'] - $product['customizationQuantityTotal'] - $product['product_quantity_reinjected'])|escape:'htmlall':'UTF-8'})" {if ($product['product_quantity_return'] + $product['product_quantity_refunded'] >= $product['product_quantity'])}disabled="disabled" {/if}/>
					{else}
					--
				{/if}
				</td>
				<td class="cancelQuantity standard_refund_fields current-edit" style="display:none">
				{if ($customization['quantity_returned'] + $customization['quantity_refunded'] >= $customization['quantity'])}
					<input type="hidden" name="cancelCustomizationQuantity[{$customizationId|intval}]" value="0" />
				{elseif (!$order->hasBeenDelivered() OR Configuration::get('PS_ORDER_RETURN'))}
					<input type="text" id="cancelQuantity_{$customizationId|intval}" name="cancelCustomizationQuantity[{$customizationId|intval}]" size="2" onclick="selectCheckbox(this);" value="" />0/{($customization['quantity']-$customization['quantity_refunded'])|escape:'htmlall':'UTF-8'}
				{/if}
				</td>

				{if ($can_edit && !$order->hasBeenDelivered())}
					<td class="edit_product_fields" colspan="2" style="display:none"></td>
					<td class="product_action" style="text-align:right" as>
						<a href="#" class="delete_product_line btn btn-default"><i class="icon-trash"></i> {l s='Delete' mod='orderedit'}</a>
					</td>
				{/if}
			</tr>
		{/foreach}
	{/foreach}
{/if}
