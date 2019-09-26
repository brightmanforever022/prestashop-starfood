<div id="trackshipPanel" class="panel">
	<div class="panel-heading"><i class="icon-truck"></i> {l s='Shipping numbers' mod='trackship'}</div>
	<div class="panel-body">
	{*<form id="trackshipAdminForm" action="" method="post" class="form-horizontal well">
		<div class="row">
			<label class="control-label col-lg-3">{l s='New number' mod='trackship'}</label>
			<div class="col-lg-6">
				<input name="number" type="text">
			</div>
			<div class="col-lg-3">
				<button type="submit" class="btn btn-default" name="submitChangeCurrency">{l s='Add' mod=''}</button>
			</div>

		</div>
	</form>*}
	<div class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th>{l s='Date' mod='trackship'}</th>
				<th>{l s='Number' mod='trackship'}</th>
				<th>{l s='Action' mod='trackship'}</th>
			</tr>
		</thead>
		<tbody>
		{foreach $order_tracking_numbers as $number}
		<tr data-id="{$number['id']}">
			<td>{dateFormat date=$number['date_added'] full=true}</td>
			<td><a target="_blank" href="https://wwwapps.ups.com/WebTracking/track?track=yes&loc=de_DE&trackNums={$number['code']}">{$number['code']}</a></td>
			<td><button class="btn btn-danger btn-xs trackshipNumberRemove" data-id="{$number['id']}">{l s='Remove' mod='trackship'}</button></td>
		</tr>
		{foreachelse}
		<tr>
			<td colspan="10">{l s='No tracking numbers attached at the moment' mod='trackship'}</td>
		</tr>
		{/foreach}
		</tbody>
	</table>
	</div>
	</div>
</div>

<script type="text/javascript">
$(function(){
	$('#trackshipPanel').on('click', 'button.trackshipNumberRemove', function(){
		if(!confirm("{l s='Confirm deleting?'}")){
			return;
		}
		var params = { tkn: "{$trackship_token}", id: $(this).data('id') };
		$.ajax({
			url: "{$link->getModuleLink('trackship','numbers')}?action=remove",
			method: 'POST',
			dataType: 'json',
			data: params
		})
		.done(function(response){
			if(response.success){
				$('#trackshipPanel table tr[data-id="'+ response.id +'"]').remove();
			}
		});
	});
});
</script>