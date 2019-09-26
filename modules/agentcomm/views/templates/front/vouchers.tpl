
{capture name=path}{l s='My commisions' mod='agentcomm'}{/capture}

<h1 class="page-heading">{l s='My commisions' mod='agentcomm'}</h1>

<div class="row addresses-lists">
	<div class="col-xs-12 col-sm-8 col-lg-8">
	{if isset($agent_current_voucher)}
		<h2>{l s='View voucher' mod='agentcomm'}</h2>
		<p>
			{l s='Code' mod='agentcomm'}: {$agent_current_voucher->code}<br> 
			{l s='Date from' mod='agentcomm'}: {$agent_current_voucher->date_from}<br>
			{l s='Date to' mod='agentcomm'}: {$agent_current_voucher->date_to}<br>
			{l s='Status' mod='agentcomm'}:
			{if $agent_current_voucher->active} 
			<span class="label label-success">{l s='Active' mod='agentcomm'}</span>
			{else}
			<span class="label label-default">{l s='Closed' mod='agentcomm'}</span>
			{/if}
			<br>
		</p>
		
		{if $voucher_orders_info}
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>{l s='Reference' mod='agentcomm'}</th>
					<th>{l s='Base' mod='agentcomm'}</th>
					<th>{l s='Discounts' mod='agentcomm'}</th>
					<th>{l s='Final' mod='agentcomm'}</th>
					<th>{l s='Commision' mod='agentcomm'}</th>
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
					<td>Totals</td>
					<td></td>
					<td></td>
					<td>{$voucher_orders_info['orders_products_total']}</td>
					<td>{$voucher_orders_info['commision_total']}</td>
				</tr>
			</tfoot>
		</table>
		{/if}
	{else}
		<p class="alert alert-warning">{l s='No active/current vouchers created at this time' mod='agentcomm'}</p>
	{/if}
	</div>
	<div class="col-xs-12 col-sm-4 col-lg-4">
		<h2>{l s='Past vouchers' mod='agentcomm'}</h2>

		{if isset($vouchers_past)}
		<ul id="agentcommVouchersPastList">
		{foreach $vouchers_past as $voucher_past}
			<li>
				<a href="{$link->getModuleLink('agentcomm', 'commisions')|escape:'html':'UTF-8'}?action=vouchers&voucher_code={$voucher_past['code']}"
					>{$voucher_past['code']}: {$voucher_past['date_from']} - {$voucher_past['date_to']}</a><br><br>
			</li>
		{/foreach}
		</ul>
		{/if}

    </div>
</div>
