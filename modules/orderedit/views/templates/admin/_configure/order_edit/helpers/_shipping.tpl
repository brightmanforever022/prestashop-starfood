{**
* OrderEdit
*
* @category  Module
* @author    silbersaiten <info@silbersaiten.de>
* @support   silbersaiten <support@silbersaiten.de>
* @copyright 2015 silbersaiten
* @version   1.0.0
* @link      http://www.silbersaiten.de
* @license   See joined file licence.txt
*}
<div class="form-wrapper form-horizontal">
	<div class="form-group ">
		<label class="control-label col-lg-3">{l s='Recycled packaging:' mod='orderedit'}</label>
		<div class="col-lg-9">
			<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="recyclable" id="recyclable_on" value="1" {if $order->recyclable} checked="checked"{/if}>
				<label for="recyclable_on" class="radioCheck">
					{l s='Yes' mod='orderedit'}
				</label>
				<input type="radio" name="recyclable" id="recyclable_off" value="0" {if !$order->recyclable} checked="checked"{/if}>
				<label for="recyclable_off" class="radioCheck">
					{l s='No' mod='orderedit'}
				</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-lg-3">{l s='Gift-wrapping:' mod='orderedit'}</label>
		<div class="col-lg-9">
			<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="gift" id="gift_on" value="1" {if $order->gift} checked="checked"{/if}>
				<label for="gift_on" class="radioCheck">
					{l s='Yes' mod='orderedit'}
				</label>
				<input type="radio" name="gift" id="gift_off" value="0" {if !$order->gift} checked="checked"{/if}>
				<label for="gift_off" class="radioCheck">
					{l s='No' mod='orderedit'}
				</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>
</div>
<div class="form-wrapper form-horizontal" id="giftWrapper" {if ! $order->gift}style="display: none;"{/if}>
	<div class="form-group">
		<label class="control-label col-lg-3">{l s='Wrapping price:' mod='orderedit'}</label>
		<div class="col-lg-9">
			<div id="giftPriceWrapper">
				<div class="editable">
					{if $can_edit}
					<p class="customVal" style="display:none;">
						<span></span>
					</p>
					{/if}
					<p class="displayVal">
						<span class="wrapping_price_show">{Tools::displayPrice($order->total_wrapping_tax_incl, (int)$currency->id)|escape:'htmlall':'UTF-8'}</span>
					</p>
					
					{if $can_edit}
					<p class="realVal" style="display:none;">
						<span class="wrapping_price_edit">
							{if $currency->sign % 2}{$currency->sign|escape:'html':'UTF-8'}{/if}
							<input type="text" name="wrapping_tax_excl" class="edit_wrapping_price_tax_excl edit_wrapping_price" rel="wrappingPriceEdit" value="{Tools::ps_round($order->total_wrapping_tax_excl, 2)|escape:'html':'UTF-8'}" size="5" /> {if !($currency->sign % 2)}{$currency->sign|escape:'html':'UTF-8'}{/if} {l s='tax excl.' mod='orderedit'}<br />
							{if $currency->sign % 2}{$currency->sign|escape:'html':'UTF-8'}{/if}
							<input type="text" name="wrapping_tax_incl" class="edit_wrapping_price_tax_incl edit_wrapping_price" rel="wrappingPriceWtEdit" value="{Tools::ps_round($order->total_wrapping_tax_incl, 2)|escape:'html':'UTF-8'}" size="5" /> {if !($currency->sign % 2)}{$currency->sign|escape:'html':'UTF-8'}{/if} {l s='tax incl.' mod='orderedit'}
						</span>
					</p>
					{/if}
				</div>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<label class="control-label col-lg-3">{l s='Wrapping tax:' mod='orderedit'}</label>
		<div class="col-lg-9">
			<div id="giftTaxWrapper">
				{if $order->total_wrapping_tax_incl == 0}
				{assign var='wrapping_tax_rate' value=0}
				{else}
				{assign var='wrapping_tax_rate' value=(($order->total_wrapping_tax_incl - $order->total_wrapping_tax_excl) / $order->total_wrapping_tax_excl) * 100}
				{/if}
				<div class="editable">
					{if $can_edit}
					<p class="customVal" style="display:none;">
						<span></span>
					</p>
					{/if}
					<p class="displayVal">
						<span class="wrapping_price_show">{Tools::ps_round($wrapping_tax_rate, 2)|escape:'html':'UTF-8'}%</span>
					</p>
					
					{if $can_edit}
					<p class="realVal" style="display:none;">
						<span class="wrapping_price_edit">
							<input type="text" id="wrappingTaxRate" name="wrappingTaxRate" class="edit_wrapping_tax_rate edit_wrapping_tax" rel="wrappingTaxRateEdit" value="{Tools::ps_round($wrapping_tax_rate, 2)|escape:'html':'UTF-8'}" size="5" />
						</span>
					</p>
					{/if}
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-9 col-md-offset-3">
		<a href="#" class="btn btn-default" id="wrappingAutoCalculate" rel="wrappingAutoCalculate">
			<i class="icon-refresh"></i>
			{l s='Calculate wrapping price automatically' mod='orderedit'}
		</a>
	</div>
	
	<div class="form-group">
		<label class="control-label col-lg-3">{l s='Gift message:' mod='orderedit'}</label>
		<div class="col-lg-9">
			<div class="editable">
				{if $can_edit}
				<p class="customVal" style="display:none;">
					<span></span>
				</p>
				{/if}
				<p class="displayVal">
					<span class="gift_message_show">{$order->gift_message|escape:'html':'UTF-8'}</span>
				</p>
				
				{if $can_edit}
				<p class="realVal" style="display:none;">
					<span class="wrapping_price_edit">
						<textarea name="gift_message" class="gift_message" rel="giftMessageEdit">{$order->gift_message|escape:'html':'UTF-8'}</textarea>
					</span>
				</p>
				{/if}
			</div>
		</div>
	</div>
</div>

<div class="clear" style="margin-bottom: 10px;"></div>
<table class="table" width="100%" cellspacing="0" cellpadding="0" id="shipping_table">
<colgroup>
	<col width="15%">
	<col width="15%">
	<col width="">
	<col width="10%">
	<col width="20%">
</colgroup>
	<thead>
	<tr>
		<th>{l s='Date:' mod='orderedit'}</th>
		<th>{l s='Type' mod='orderedit'}</th>
		<th>{l s='Carrier' mod='orderedit'}</th>
		<th>{l s='Weight' mod='orderedit'}</th>
		<th>{l s='Shipping cost' mod='orderedit'}</th>
		<th>{l s='Tax rate' mod='orderedit'}</th>
		<th>{l s='Tracking number' mod='orderedit'}</th>
	</tr>
	</thead>
	<tbody>
	{foreach from=$order->getShipping() item=line}
	<tr class="shipping_line">
		<td class="sh_date">
			<div class="editable">
			{if $line.can_edit}
			<p class="customVal" style="display:none;">
				<span></span>
			</p>
			{/if}
			<p class="displayVal">
				<span class="shipping_date_show">{$line.date_add|escape:'html':'UTF-8'}</span>
			</p>
			{if $line.can_edit}
			<p class="realVal" style="display:none;">
					<span class="shipping_date_edit">
						<input type="text" class="datetime_pick" id="edit_date_{$line.id_order_carrier|escape:'html':'UTF-8'}" rel="shippingDate" value="{$line.date_add|escape:'html':'UTF-8'}" />
					</span>
			</p>
			{/if}
			</div>
		</td>
		<td class="sh_type">{$line.type|escape:'html':'UTF-8'}</td>
		<td class="sh_id">
			{if $line.can_edit && isset($carriers) && $carriers|@count > 0}
				<select id="carrier_{$line.id_order_carrier|escape:'html':'UTF-8'}" rel="shippingCarrierId" autocomplete="off">
				<option value="0">--</option>
				{foreach from=$carriers item=carrier}
				<option value="{$carrier.id_carrier|escape:'html':'UTF-8'}"{if $carrier.id_carrier == $line.id_carrier} selected="selected"{/if}>{$carrier.name|escape:'html':'UTF-8'}</option>
				{/foreach}
				</select>
			{else}
			{$line.state_name|escape:'html':'UTF-8'}
			{/if}
		</td>
		<td class="sh_weight">
			<div class="editable">
				{if $line.can_edit}
				<p class="customVal" style="display:none;">
					<span></span>
				</p>
				{/if}
				<p class="displayVal">
					<span class="shipping_weight">
						{if $line.weight}{$line.weight|string_format:"%.3f"|escape:'html':'UTF-8'}{/if}
					</span>
					<span class="weight_unit">
					{Configuration::get('PS_WEIGHT_UNIT')|escape:'html':'UTF-8'}
					</span>
				</p>
				{if $line.can_edit}
				<p class="realVal" style="display:none;">
					<span class="shipping_weight_edit">
						<input type="text" name="shipping_weight" class="edit_weight" rel="shippingWeightEdit" value="{$line.weight|escape:'html':'UTF-8'}" />
					</span>
				</p>
				{/if}
			</div>
		</td>
		<td class="sh_price">
			<div class="editable">
				<input type="hidden" name="id_order_carrier" rel="orderShippingCarrier" value="{$line.id_order_carrier|escape:'html':'UTF-8'}" />
				{if $line.can_edit}
				<p class="customVal" style="display:none;">
					<span></span>
				</p>
				{/if}
				<p class="displayVal">
					<span class="product_price_show">{Tools::displayPrice($line.shipping_cost_tax_incl, (int)$currency->id)|escape:'htmlall':'UTF-8'}</span>
				</p>
				{if $line.can_edit}
				<p class="realVal" style="display:none;">
					<span class="product_price_edit">
						{if $currency->sign % 2}{$currency->sign|escape:'html':'UTF-8'}{/if}
						<input type="text" name="shipping_tax_excl" class="edit_shipping_price_tax_excl edit_shipping_price is_price_input" rel="shippingPriceEdit" value="{Tools::ps_round($line.shipping_cost_tax_excl, 2)|escape:'html':'UTF-8'}" size="5" /> {if !($currency->sign % 2)}{$currency->sign|escape:'html':'UTF-8'}{/if} {l s='tax excl.' mod='orderedit'}<br />
						{if $currency->sign % 2}{$currency->sign|escape:'html':'UTF-8'}{/if}
						<input type="text" name="shipping_tax_incl" class="edit_shipping_price_tax_incl edit_shipping_price is_price_input" rel="shippingPriceWtEdit" value="{Tools::ps_round($line.shipping_cost_tax_incl, 2)|escape:'html':'UTF-8'}" size="5" /> {if !($currency->sign % 2)}{$currency->sign|escape:'html':'UTF-8'}{/if} {l s='tax incl.' mod='orderedit'}
					</span>
				</p>
				{/if}
			</div>
		</td>
		<td class="sh_tax">
			{if $line.shipping_cost_tax_excl == 0}
			{assign var='carrier_tax_rate' value=0}
			{else}
			{assign var='carrier_tax_rate' value=(($line.shipping_cost_tax_incl - $line.shipping_cost_tax_excl) / $line.shipping_cost_tax_excl) * 100}
			{/if}
			<div class="editable">
				{if $line.can_edit}
				<p class="customVal" style="display:none;">
					<span></span>
				</p>
				{/if}
				<p class="displayVal">
					<span class="shipping_tax_rate_show">
						{$carrier_tax_rate|string_format:"%.2F"|escape:'html':'UTF-8'}%
					</span>
				</p>
				{if $line.can_edit}
				<p class="realVal" style="display:none;">
					<span class="shipping_tax_rate_edit">
						<input type="text" name="shipping_tax_rate" class="edit_shipping_tax_rate is_price_input" rel="shippingTaxRateEdit" value="{$carrier_tax_rate|string_format:"%.2F"|escape:'html':'UTF-8'}" />
					</span>
				</p>
				{/if}
			</div>
		</td>
		<td class="sh_tracking">
			<div class="editable">
				{if $line.can_edit}
				<p class="customVal" style="display:none;">
					<span></span>
				</p>
				{/if}
				<p class="displayVal">
					<span class="shipping_tracking_number_show">
						{if $line.url && $line.tracking_number}<a href="{$line.url|replace:'@':$line.tracking_number|escape:'html':'UTF-8'}" target="_blank">{$line.tracking_number|escape:'html':'UTF-8'}</a>{else if $line.tracking_number}{$line.tracking_number|escape:'html':'UTF-8'}{else}{l s='Add tracking number' mod='orderedit'}{/if}
					</span>
				</p>
				{if $line.can_edit}
				<p class="realVal" style="display:none;">
					<span class="shipping_tracking_number_edit">
						<input type="text" name="shipping_tracking_number" class="edit_tracking_number" rel="shippingTrackingNumberEdit" value="{$line.tracking_number|escape:'html':'UTF-8'}" />
					</span>
				</p>
				{/if}
			</div>
		</td>
	</tr>
	{/foreach}
	</tbody>
</table>

{if $carrierModuleCall}
	{$carrierModuleCall|escape:'html':'UTF-8'}
{/if}