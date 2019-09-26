
{if $layout}
<div class="col-lg-12"><div class="panel"><div class="panel-body">
{/if}
{if $voucher_orders_info}
<table class="table table-condensed">
	<caption><strong>{l s='Voucher orders' mod='agentcomm'}</strong></caption>
	<thead>
		<tr>
			<th>{l s='Order reference' mod='agentcomm'}</th>
			<th>{l s='Order base total' mod='agentcomm'}</th>
			<th>{l s='Order discounts' mod='agentcomm'}</th>
			<th>{l s='Order final total' mod='agentcomm'}</th>
			<th>{l s='Order commision' mod='agentcomm'}
				{if $voucher_orders_info['current_voucher_info']['agent_commision_type'] == 1}
				({$voucher_orders_info['current_voucher_info']['agent_commision']}%)
				{else}
				({$voucher_orders_info['current_voucher_info']['agent_commision']} {$currency->iso_code})
				{/if}
			
			</th>
		</tr>
	</thead>
	<tbody>
{foreach $voucher_orders_info['orders_list'] AS $order}
	<tr>
		<td>{$order['reference']}</td>
		<td>{$order['total_products']}</td>
		<td>{$order['total_discounts']}</td>
		<td>{$order['total_products'] - $order['total_discounts']}</td>
		<td>{$order['order_commision']}</td>
	</tr>
{/foreach}
	</tbody>
	<tfoot>
		<tr>
			<td>{l s='Totals'  mod='agentcomm'}</td>
			<td></td>
			<td></td>
			<td>{$voucher_orders_info['orders_products_total']}</td>
			<td>{$voucher_orders_info['commision_total']} 
			</td>
		</tr>
	</tfoot>
</table>
{/if}

{if $layout}
</div></div></div>
{/if}
