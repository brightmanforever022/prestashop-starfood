
<div class="col-lg-12">
	<div class="panel">
		<div class="panel-heading">
			{l s='Agent' mod='agentsales'}
		</div>
		<div class="panel-body">
		<h4>{l s='Associate order with agent' mod='agentsales'}</h4>
		<form action="" method="post" id="agentsalesOrderToAgentForm" class="form-inline">
			<div class="form-group">
			<select name="id_agent" >
				<option>{l s='No agent' mod='agentsales'}</option>
				{foreach $agents as $agent}
				<option value="{$agent['id_customer']}">{$agent['lastname']} {$agent['firstname']} ({$agent['company']})</option>
				{/foreach}
			</select>
			</div>
		</form>
		</div>
	</div>
</div>
<script type="text/javascript">
</script>