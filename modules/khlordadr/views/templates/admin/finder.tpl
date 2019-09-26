<form method="get" class="defaultForm form-horizontal" id="khlordadr_form" action="">
<div class="panel">
	<div class="panel-heading">{l s='Search customers' mod='khlordadr'}</div>
	<div class="form-wrapper">
		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Radius' mod='khlordadr'}</label>
			<div class="col-lg-9">
				<input type="text" name="radius"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Start location' mod='khlordadr'}</label>
			<div class="col-lg-9">
				<input type="text" name="start"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Active customers only' mod='khlordadr'}</label>
			<div class="col-lg-9">
				<input type="checkbox" name="customer_active" value="1"/>
			</div>
		</div>

	</div>
	<div class="panel-footer">
		<button type="submit" name="save" class="btn btn-default pull-right">{l s='Search' mod='khlordadr'}</button>
	</div>
	
</div>
</form>
<div class="panel">
	<div class="panel-heading">{l s='Addresses' mod='khlordadr'}</div>

	<div class="panel-body" id="khlordadr_locations"></div>
</div>

<script type="text/javascript">

$(function(){
	$('#content').on('submit', '#khlordadr_form', function(event){
		event.preventDefault();
		$.ajax({
			url: "{$module_adr_url}&action=locations",
			dataType: 'html',
			data: $('#khlordadr_form').serialize(),
			beforeSend: function(){
				$('#khlordadr_locations').html('Loading...');
			}
		})
		.done(function(response){
			$('#khlordadr_locations').html(response);
		});
	});
	$('#khlordadr_locations').on('click', 'a.address_more', function(event){
		event.preventDefault();
		var addressId = $(this).attr('data-address_id');
		$.ajax({
			url: "{$module_adr_url}&action=customer_info",
			dataType: 'html',
			data: { id_address: addressId },
			beforeSend: function(){
				$('#address_id_'+addressId).append('<br><p>Loading...</p>');
			}
		})
		.done(function(response){
			//$('#khlordadr_locations').html();
			$('#address_id_'+addressId+' p').html(response);
		});
		return false;
	});
});



</script>

