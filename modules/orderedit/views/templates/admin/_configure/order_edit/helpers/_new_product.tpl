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
*  @version  Release: $Revision: 9856 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div id="new_product" style="display:none;">
	<div id="new_product_wrapper" class="panel">
		<input type="hidden" id="add_product_product_id" name="add_product[product_id]" value="0" />
		<input type="hidden" id="add_product_product_tax_rate" name="add_product_product_tax_rate" value="0" />
		<input type="hidden" id="add_product_name_nc" />
		<div class="panel-heading">{l s='Product:' mod='orderedit'}</div>
		<input type="text" id="add_product_product_name" value="" size="42" />
		<div id="add_product_product_attribute_area" style="margin-top: 5px;display: none;">
			<label>{l s='Combinations:' mod='orderedit'}</label>
			<table width="100%" cellpadding="0" cellspacing="0" class="table" id="new_product_combinations_table">
				<thead>
					<th></th>
					<th>{l s='Combination' mod='orderedit'}</th>
					<th>{l s='Quantity in stock' mod='orderedit'}</th>
					<th>{l s='Price (tax excl.)' mod='orderedit'}</th>
					<th>{l s='Price (tax incl.)' mod='orderedit'}</th>
				</thead>
				<tbody></tbody>
			</table>
		</div>
		<div id="add_product_product_warehouse_area" style="margin-top: 5px; display: none;">
            <label>{l s='Warehouse:' mod='orderedit'}</label>
			<select  id="add_product_warehouse" name="add_product_warehouse">
			</select>
		</div>
		<div id="add_product_price_block" class="show_on_product_select" style="margin-top: 5px; display: none;">
            <label>{l s='Price:' mod='orderedit'}</label>
			<span class="form-inline">
				{if $currency->sign % 2}{$currency->sign|escape:'html':'UTF-8'}{/if}<input class="fixed-width-xl" type="text" name="add_product[product_price_tax_excl]" id="add_product_product_price_tax_excl" value="" size="4" disabled="disabled" /> {if !($currency->sign % 2)}{$currency->sign|escape:'html':'UTF-8'}{/if} {l s='tax excl.' mod='orderedit'}
			</span>
			<span class="form-inline">
			{if $currency->sign % 2}{$currency->sign|escape:'html':'UTF-8'}{/if}<input class="fixed-width-xl" type="text" name="add_product[product_price_tax_incl]" id="add_product_product_price_tax_incl" value="" size="4" disabled="disabled" /> {if !($currency->sign % 2)}{$currency->sign|escape:'html':'UTF-8'}{/if} {l s='tax incl.' mod='orderedit'}
			</span>
		</div>
		<div id="product_quantity_block" class="productQuantity show_on_product_select" style="margin-top: 5px; display: none;">
            <label>{l s='Quantity:' mod='orderedit'}</label>
            <span class="form-inline">
			    <input class="fixed-width-xl" type="text" name="add_product[product_quantity]" id="add_product_product_quantity" value="1" size="3" disabled="disabled" />
            </span>
		</div>
		{if ($order->hasBeenPaid())}
		<div style="display:none;" class="productQuantity">&nbsp;</div>
		{/if}
		{if ($order->hasBeenDelivered())}
		<div style="display:none;" class="productQuantity">&nbsp;</div>
		{/if}
		<div id="add_product_stock_wrapper" class="show_on_product_select" style="margin-top: 5px; display: none;">
            <label>{l s='Amount in stock:' mod='orderedit'}</label>
			<div class="productQuantity" id="add_product_product_stock">0</div>
		</div>
		<div id="add_product_product_total_wrapper" class="show_on_product_select" style="margin-top: 5px; display: none;">
            <label>{l s='Final retail price:' mod='orderedit'}</label>
			<div id="add_product_product_total">{Tools::displayPrice(0, (int)$currency->id)|escape:'htmlall':'UTF-8'}</div>
		</div>
		<div id="addProductWrapper" class="show_on_product_select" style="margin-top: 5px; display: none;">
			<input type="button" class="btn btn-success" id="submitAddProduct" value="{l s='Add product' mod='orderedit'}" disabled="disabled" />

            <input type="button" class="btn btn-default" id="cancelAddProduct" value="{l s='Cancel' mod='orderedit'}" disabled="disabled" />
		</div>
	</div>
	<div id="new_invoice" style="display:none;background-color:#e9f1f6;">
        <label>{l s='New invoice information' mod='orderedit'}</label>
		<label>{l s='Carrier:' mod='orderedit'}</label>
		<div class="margin-form">
			{$carrier->name|escape:'html':'UTF-8'}
		</div>
		<div class="margin-form">
			<input type="checkbox" name="add_invoice[free_shipping]" value="1" />
			<label class="t">{l s='Free shipping' mod='orderedit'}</label>
			<p>{l s='If you don\'t select "Free shipping," the normal shipping cost will be applied' mod='orderedit'}</p>
		</div>
	</div>
</div>