{extends file='controllers/orders/form.tpl'}

{block name='leadin'}
<div class="panel" id="searchByLetterPanel">
	<div class="panel-heading">{l s='Search by letter'}</div>
	<div class="panel-body">
		<div class="btn-group" role="group" aria-label="Search">
		{foreach $searchbar_letters as $letter}
			<button type="button" class="btn btn-default search-by-letter" data-char="{$letter}">{$letter}</button>
		{/foreach}
			<button type="button" class="btn btn-default search-by-letter" data-char="-">*</button>
		
		</div>
		<br><br>
		<div id="searchByLetterResults"></div>
	</div>
	
</div>

<script type="text/javascript">
$(function(){
	$('#searchByLetterPanel').on('click', 'button.search-by-letter', function(event){
		var searchChar = $(this).data('char');
		$('#searchByLetterResults').html('<div class="alert alert-info">&nbsp;{l s='Searching'}</div>');
		$.ajax({
			type:"POST",
			url : "{$link->getAdminLink('AdminOrders')}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				tab: "AdminOrders",
				action: "searchCustomersByFirstChar",
				query: searchChar
			},
			success : function(response){
				
				var html = '';
				if(response.data.length)
				{
					var tableHtml = $('<table class="table table-bordered"></table>');
					$(tableHtml).append('<thead><tr><th>{l s='Name'}</th><th>{l s='Company'}</th><th>{l s='Action'}</th></tr></thead>');
					$.each(response.data, function() {
						var tdHtml = '<tr>';
						
						tdHtml += '<td>'+this.firstname+' '+this.lastname +'</td>';
						tdHtml += '<td>'+this.company+'</td>';
						//html += '<span>'+this.email+'</span><br/>';
						//html += '<span class="text-muted">'+((this.birthday != '0000-00-00') ? this.birthday : '')+'</span><br/>';
						//html += '<div class="panel-footer">';
						//html += '<a href="{$link->getAdminLink('AdminCustomers')}&id_customer='+this.id_customer+'&viewcustomer&liteDisplaying=1" class="btn btn-default fancybox"><i class="icon-search"></i> {l s='Details'}</a>';
						tdHtml += '<td><button type="button" data-customer="'+this.id_customer+'" class="setup-customer btn btn-default btn-xs pull-right"><i class="icon-arrow-right"></i> {l s='Choose'}</button></td>';
						//html += '</div>';
						//html += '</div>';
						tdHtml += '</tr>';
						
						$(tableHtml).append(tdHtml);
					});
					
					html = tableHtml;
				}
				else
					html = '<div class="alert alert-warning"><i class="icon-warning-sign"></i>&nbsp;{l s='No customers found'}</div>';
				$('#searchByLetterResults').html(html);
			}
		});
	});
	$('#searchByLetterResults').on('click', 'button.setup-customer', function(){
		setupCustomer( $(this).data('customer') );
	});
});


</script>
{/block}