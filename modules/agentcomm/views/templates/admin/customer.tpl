
<div class="col-lg-12">
	<div class="panel">
		<div class="panel-heading">
			{l s='Agents vouchers' mod='agentcomm'}
		</div>
		<div class="row">
			<div class="col-lg-8">
			{if $agent_current_voucher}
				{l s='Current agent\'s voucher' mod='agentcomm'}: 
				<a href="{$cart_rule_admin_link|escape:'link'}">{$agent_current_voucher->code}</a>: 
				{$agent_current_voucher->date_from} - {$agent_current_voucher->date_to} 
			{/if}
			<div id="agentcommAdminVoucherContent"></div>
			</div>
			<div class="col-lg-4">
				<form action="{$agents_controller_url}&action=start_voucher" method="post">
					<input type="hidden" name="id_customer" value="{$id_customer}">
					<button type="submit" class="btn btn-primary">{l s='Start new voucher' mod='agentcomm'}</button>
				</form>
			{if isset($vouchers_past)}
				<hr>
				<h2 class="clearfix">{l s='Past vouchers' mod='agentcomm'}</h2>
				<ul id="agentcommVouchersPastList">
				{foreach $vouchers_past as $voucher_past}
					<li>
						<button class="btn btn-link" data-voucher="{$voucher_past['id_cart_rule']}">{$voucher_past['code']}: {$voucher_past['date_from']} - {$voucher_past['date_to']}</button>
					</li>
				{/foreach}
				</ul>
			{/if}
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

var agentsControllerUrl = '{$agents_controller_url}';
{if $agent_current_voucher}
var voucherCurrentId = {$agent_current_voucher->id};
{/if}
if(typeof voucherCurrentId != 'undefined'){
	agentcommGetAgentsVoucher(voucherCurrentId);
}

</script>