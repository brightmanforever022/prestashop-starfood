{extends file="helpers/list/list_header.tpl"}
{block name='override_header'}
{if $submit_form_ajax}
	<script type="text/javascript">
		parent.$.fancybox.close();
	</script>
{/if}
{/block}
<div class="panel">
	<div class="panel-heading">
		{l s='Vouchers statistics' mod='agentcomm'}: 
		{$stats_date_from} - {$stats_date_to}
	</div>
	<div class="panel-content">
	{if isset($agents_stats)}
		<table class="table">
			<thead>
				<tr>
					<th>{l s='Agent' mod='agentcomm'}</th>
					<th>{l s='Voucher code' mod='agentcomm'}</th>
					<th>{l s='Voucher date' mod='agentcomm'}</th>
					<th>{l s='Orders count' mod='agentcomm'}</th>
					<th>{l s='Orders total' mod='agentcomm'}</th>
					<th>{l s='Commision, %' mod='agentcomm'}</th>
					<th>{l s='Commision, abs' mod='agentcomm'}</th>
				</tr>
			</thead>

		{foreach $agents_stats['vouchers'] as $si => $agent_stat}
			<tr>
				<td>{$agent_stat['agent_name']}</td>
				<td>{$agent_stat['voucher_code']}</td>
				<td>{$agent_stat['voucher_date']}</td>
				<td>{$agent_stat['orders_count']}</td>
				<td>{$agent_stat['orders_total']}</td>
				<td>{$agent_stat['agent_commision']}</td>
				<td>{$agent_stat['commisions_total']}</td>
			</tr>
		{/foreach}
			<tfoot>
				<tr>
					<td>{l s='Totals' mod='agentcomm'}</td>
					<td></td>
					<td></td>
					<td></td>
					<td>{$agents_stats['agents_orders_total']}</td>
					<td></td>
					<td>{$agents_stats['agents_commisions_total']}</td>
				</tr>
			</tfoot>

		</table>
	{/if}
	</div>
</div>
