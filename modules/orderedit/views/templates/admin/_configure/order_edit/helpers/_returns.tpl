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
<h4 class="visible-print">{l s='Merchandise Returns' mod='orderedit'} <span class="badge">({$order->getReturn()|@count|escape:'html':'UTF-8'})</span></h4>
{if !$order->isVirtual()}
<!-- Return block -->
	{if $order->getReturn()|count > 0}
	<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th><span class="title_box ">{l s='Date' mod='orderedit'}</span></th>
					<th><span class="title_box ">{l s='Type' mod='orderedit'}</span></th>
					<th><span class="title_box ">{l s='Carrier' mod='orderedit'}</span></th>
					<th><span class="title_box ">{l s='Tracking Number' mod='orderedit'}</span></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$order->getReturn() item=line}
				<tr>
					<td>{$line.date_add|escape:'html':'UTF-8'}</td>
					<td>{$line.type|escape:'html':'UTF-8'}</td>
					<td>{$line.state_name|escape:'html':'UTF-8'}</td>
					<td class="actions">
						<span id="shipping_number_show">{if isset($line.url) && isset($line.tracking_number)}<a href="{$line.url|replace:'@':$line.tracking_number|escape:'html':'UTF-8'}">{$line.tracking_number|escape:'html':'UTF-8'}</a>{elseif isset($line.tracking_number)}{$line.tracking_number|escape:'html':'UTF-8'}{/if}</span>
						{if $line.can_edit}
						<form method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&vieworder&id_order={$order->id|escape:'html':'UTF-8'}&id_order_invoice={if $line.id_order_invoice}{$line.id_order_invoice|escape:'html':'UTF-8'}{else}0{/if}&id_carrier={if $line.id_carrier}{$line.id_carrier|escape:'html':'UTF-8'}{else}0{/if}">
							<span class="shipping_number_edit" style="display:none;">
								<button type="button" name="tracking_number">
									{$line.tracking_number|htmlentities|escape:'html':'UTF-8'}
								</button>
								<button type="submit" class="btn btn-default" name="submitShippingNumber">
									{l s='Update' mod='orderedit'}
								</button>
							</span>
							<button href="#" class="edit_shipping_number_link">
								<i class="icon-pencil"></i>
								{l s='Edit' mod='orderedit'}
							</button>
							<button href="#" class="cancel_shipping_number_link" style="display: none;">
								<i class="icon-remove"></i>
								{l s='Cancel' mod='orderedit'}
							</button>
						</form>
						{/if}
					</td>
				</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
	{else}
	<div class="list-empty hidden-print">
		<div class="list-empty-msg">
			<i class="icon-warning-sign list-empty-icon"></i>
			{l s='No merchandise returned yet' mod='orderedit'}
		</div>
	</div>
	{/if}
	{if $carrierModuleCall}
		{$carrierModuleCall|escape:'html':'UTF-8'}
	{/if}
{/if}