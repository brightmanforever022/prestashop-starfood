
<form method="post" action="{$form_url}" class="form-horizontal" id="invoiceEditForm">
<div class="row">
	<div class="col-xs-12 text-right">
		<a href="{$orders_url}&id_order={$order->id}&vieworder" class="btn btn-primary">{l s='Edit order' mod='product_list'}</a>
	</div>
</div>



<div class="panel form-horizontal" id="invoice_products">
	<div class="panel-heading">{l s='Products'  mod='product_list'}</div>
	<div class="row">
	<div class="col-lg-7">
	<table class="table" id="invoiceEditOrderProductsTable">
		<thead>
			<tr>
				<th>{l s='Name'}</th>
				<th>{l s='Supplier reference'}</th>
				<th>{l s='Quantity'}</th>
				<th>{l s='Price tax excl.'}</th>
				<!--th>{l s='Price tax incl.'}</th-->
				<th>{l s='Total tax excl.'}</th>
				<!-- th>{l s='Total tax incl.'}</th-->
				<th></th>
			</tr>
		</thead>
		<tbody>
		{*foreach $invoice_products as $product}
			<tr data-id="{$product['id_order_detail']}">
				<td>{$product['product_name']}</td>
				<td>{$product['product_supplier_reference']}</td>
				<td>{$product['product_quantity']}</td>
				<td>{displayPrice price=$product['unit_price_tax_excl']}</td>
				<td>{displayPrice price=$product['unit_price_tax_incl']}</td>
				<td>{displayPrice price=(Tools::ps_round($product['unit_price_tax_excl'], 2) * ($product['product_quantity'])) currency=$currency->id}</td>
				<td>{displayPrice price=(Tools::ps_round($product['unit_price_tax_incl'], 2) * ($product['product_quantity'])) currency=$currency->id}</td>
				<td>
					<button type="button" class="btn btn-danger btn-xs product_remove">{l s='Remove'}</button>
					<input type="hidden" name="id_order_detail[]" value="{$product['id_order_detail']}"/>
				</td>
			</tr>
		{/foreach*}
		</tbody>
		<tfoot>
			<tr>
				{*<td colspan="6" class="text-right">{l s='Shipping'}</td>
				<td>{displayPrice price=$invoice->total_shipping_tax_incl}</td>
				<td></td>*}
			</tr>
		</tfoot>
	</table>
	
	</div>
	<div class="col-lg-5">
		<div >
			{l s='Categories'}
			<div id="category_block" style="max-height: 400px;">
				{$category_tree}
			</div>
		</div>
		<div class="">
			<div id="products_found">
			<hr/>
			<table class="table table-condensed" id="products_table">
				<tr>
					<th>{l s='Spl.ref.'}</th>
					
					<th>{l s='Name'}</th>
					<th>{l s='Price'}</th>
					<th>{l s='Qty.'}</th>
					<th></th>
				</tr>
			</table>
			</div>
			<div id="products_err" class="hide alert alert-danger"></div>
		</div>
	
	</div>
	</div>
	{*<div class="form-group">
		<div class="col-lg-2">
			<select id="order_products" class="form-control">
			<option value="0">{l s='Add product to invoice' mod='product_list'}</option>
			{foreach $order_products as $order_product}
			<option value="{$order_product['id_order_detail']}">{$order_product['product_name']}</option>
			{/foreach}
			</select>
		</div>
		<div class="col-lg-1">
			<button id="order_product_add" type="button" class="btn btn-success">{l s='Add' mod='product_list'}</button>
		</div>
	</div>*}
	<div class="form-group">
		<button name="submit_new_invoice" value="1" class="btn btn-primary" type="submit">{l s='Save invoice' mod='product_list'}</button>
	</div>
</div>

<div class="panel" id="invoice_meta">
	<div class="panel-heading">{l s='Meta' mod='product_list'}</div>
	<div class="form-group">
		<label class="control-label col-lg-2" for="invoice_percent">{l s='Invoice template' mod='product_list'}</label>
		<div class="col-lg-6">
            <select name="invoice_template_id" id="addInvoiceInvoiceTemplateSel" >
                {foreach from=$invoice_templates item='invoiceCategory'}
                    <optgroup label="{$invoiceCategory.categoryName}">
                    {foreach from=$invoiceCategory.templates item='template'}
                        <option value="{$template.id}" {if $invoice->template_id == $template.id}selected{/if}
                        	>{$template.name}</option>
                    {/foreach}
                    </optgroup>
                {/foreach}
            </select>&nbsp;

		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-2" for="printed">{l s='Printed' mod='product_list'}</label>
		<div class="col-lg-2">
			<input type="checkbox" name="printed" id="printed" value="1" {if $invoice->printed}checked="checked"{/if} class=""/>
		</div>
	</div>
	
	<div class="form-group">
		<label class="control-label col-lg-2" for="invoice_percent">{l s='Payment date' mod='product_list'}</label>
		<div class="col-lg-2">
			<input type="text" name="payment_date" id="payment_date" value="{$invoice->payment_date}" class="datepicker form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-lg-2" for="paid">{l s='Paid' mod='product_list'}</label>
		<div class="col-lg-2">
			<input type="checkbox" name="paid" id="paid" value="1" {if $invoice->paid}checked="checked"{/if} class=""/>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-2">{l s='Comment' mod='product_list'}</label>
		<div class="col-lg-6">
			<textarea id="invoice_comment" name="comment"  class="form-control">{$invoice->comment}</textarea>
		</div>
	</div>
	
	
	<div class="panel-footer">
		<button type="submit" class="btn btn-default pull-right" name="updateorder_invoiceSubmit" value="1"
			><i class="process-icon-save"></i>{l s='Save'}</button>
		<a href="{$cancel_url}" class="btn btn-default" 
			><i class="process-icon-cancel"></i>{l s='Cancel'}</a>
	</div>
</div>
</form>


<!-- Customer informations -->
<div class="panel">
	{if $customer->id}
	<div class="panel-heading">
		<i class="icon-user"></i>
		{l s='Customer'}
		<span class="badge">
			<a href="?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;viewcustomer&amp;token={getAdminToken tab='AdminCustomers'}">
				{if Configuration::get('PS_B2B_ENABLE')}{$customer->company} - {/if}
				{$gender->name|escape:'html':'UTF-8'}
				{$customer->firstname}
				{$customer->lastname}
			</a>
		</span>
		<span class="badge">
			{l s='#'}{$customer->id}
		</span>
	</div>
	{/if}
	<div class="row">
		{if $customer->id}
		<div class="col-xs-4">
			{if ($customer->isGuest())}
				{l s='This order has been placed by a guest.'}
				{if (!Customer::customerExists($customer->email))}
					<form method="post" action="index.php?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;id_order={$order->id|intval}&amp;token={getAdminToken tab='AdminCustomers'}">
						<input type="hidden" name="id_lang" value="{$order->id_lang}" />
						<input class="btn btn-default" type="submit" name="submitGuestToCustomer" value="{l s='Transform a guest into a customer'}" />
						<p class="help-block">{l s='This feature will generate a random password and send an email to the customer.'}</p>
					</form>
				{else}
					<div class="alert alert-warning">
						{l s='A registered customer account has already claimed this email address'}
					</div>
				{/if}
			{else}
				<dl class="well list-detail">
					<dt>{l s='Email'}</dt>
						<dd><a href="mailto:{$customer->email}"><i class="icon-envelope-o"></i> {$customer->email}</a></dd>
					<dt>{l s='Account registered'}</dt>
						<dd class="text-muted"><i class="icon-calendar-o"></i> {dateFormat date=$customer->date_add full=true}</dd>
					<dt>{l s='Valid orders placed'}</dt>
						<dd><span class="badge">{$customerStats['nb_orders']|intval}</span></dd>
					<dt>{l s='Total spent since registration'}</dt>
						<dd><span class="badge badge-success">{displayPrice price=Tools::ps_round(Tools::convertPrice($customerStats['total_orders'], $currency), 2) currency=$currency->id}</span></dd>
					{*if Configuration::get('PS_B2B_ENABLE')}
						<dt>{l s='Siret'}</dt>
							<dd>{$customer->siret}</dd>
						<dt>{l s='APE'}</dt>
							<dd>{$customer->ape}</dd>
					{/if*}
				</dl>
			{/if}
		</div>
		{/if}
		<div class="col-xs-8">
			<ul class="nav nav-tabs" id="tabAddresses">
				<li class="active">
					<a href="#addressShipping">
						<i class="icon-truck"></i>
						{l s='Shipping address'}
					</a>
				</li>
				<li>
					<a href="#addressInvoice">
						<i class="icon-file-text"></i>
						{l s='Invoice address'}
					</a>
				</li>
			</ul>
			<!-- Tab content -->
			<div class="tab-content panel">
				<!-- Tab status -->
				<div class="tab-pane  in active" id="addressShipping">
					<!-- Addresses -->
					<h4 class="visible-print">{l s='Shipping address'}</h4>
					{if !$order->isVirtual()}
					{*
					<!-- Shipping address -->
					<form class="form-horizontal hidden-print" method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}">
						<div class="form-group">
							<div class="col-lg-9">
								<select name="id_address">
									{foreach from=$customer_addresses item=address}
									<option value="{$address['id_address']}"
										{if $address['id_address'] == $order->id_address_delivery}
											selected="selected"
										{/if}>
										{$address['alias']} -
										{$address['address1']}
										{$address['postcode']}
										{$address['city']}
										{if !empty($address['state'])}
											{$address['state']}
										{/if},
										{$address['country']}
									</option>
									{/foreach}
								</select>
							</div>
							<div class="col-lg-3">
								<button class="btn btn-default" type="submit" name="submitAddressShipping"><i class="icon-refresh"></i> {l s='Change'}</button>
							</div>
						</div>
					</form>
					*}
					<div class="well">
						<div class="row">
							<div class="col-sm-12">
								{*<a class="btn btn-default pull-right" href="?tab=AdminAddresses&amp;id_address={$addresses.delivery->id}&amp;addaddress&amp;realedit=1&amp;id_order={$order->id}&amp;address_type=1&amp;token={getAdminToken tab='AdminAddresses'}&amp;back={$smarty.server.REQUEST_URI|urlencode}">
									<i class="icon-pencil"></i>
									{l s='Edit'}
								</a>*}
								{displayAddressDetail address=$addresses.delivery newLine='<br />'}
								{if $addresses.delivery->other}
									<hr />{$addresses.delivery->other}<br />
								{/if}
							</div>
						</div>
					</div>
					{/if}
				</div>
				<div class="tab-pane " id="addressInvoice">
					<!-- Invoice address -->
					<h4 class="visible-print">{l s='Invoice address'}</h4>
					{*
					<form class="form-horizontal hidden-print" method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}">
						<div class="form-group">
							<div class="col-lg-9">
								<select name="id_address">
									{foreach from=$customer_addresses item=address}
									<option value="{$address['id_address']}"
										{if $address['id_address'] == $order->id_address_invoice}
										selected="selected"
										{/if}>
										{$address['alias']} -
										{$address['address1']}
										{$address['postcode']}
										{$address['city']}
										{if !empty($address['state'])}
											{$address['state']}
										{/if},
										{$address['country']}
									</option>
									{/foreach}
								</select>
							</div>
							<div class="col-lg-3">
								<button class="btn btn-default" type="submit" name="submitAddressInvoice"><i class="icon-refresh"></i> {l s='Change'}</button>
							</div>
						</div>
					</form>
					*}
					<div class="well">
						<div class="row">
							<div class="col-sm-6">
								{*<a class="btn btn-default pull-right" href="?tab=AdminAddresses&amp;id_address={$addresses.invoice->id}&amp;addaddress&amp;realedit=1&amp;id_order={$order->id}&amp;address_type=2&amp;back={$smarty.server.REQUEST_URI|urlencode}&amp;token={getAdminToken tab='AdminAddresses'}">
									<i class="icon-pencil"></i>
									{l s='Edit'}
								</a>*}
								{displayAddressDetail address=$addresses.invoice newLine='<br />'}
								{if $addresses.invoice->other}
									<hr />{$addresses.invoice->other}<br />
								{/if}
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
	
</div>


<form action="{$change_customer_form_url}" method="post">
<div class="panel">
		<div id="search-customer-form-group" class="form-group">
			<label class="control-label col-lg-3">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Search for an existing customer by typing the first letters of his/her name.'}">
					{l s='Change order customer'}
				</span>
			</label>
			<div class="col-lg-9">
				<div class="input-group">
					<input type="text" id="customer" value="" />
					<span class="input-group-addon">
						<i class="icon-search"></i>
					</span>
				</div>
			</div>
		</div>
		<div class="row">
			<div id="customers"></div>
		</div>
		<input type="hidden" id="customer_id" name="customer_id" value="" />
		<input type="hidden" id="order_id" name="order_id" value="{$order->id}" />
	<div class="panel-footer">
		<button type="submit" class="btn btn-default pull-right" name="action" value="change_customer"
			><i class="process-icon-save"></i>{l s='Change customer'}</button>
	</div>

</div>
</form>

<div class="row" style="display:none;" id="product_edit_inputs">
	<div class="form-group">
		<label class="control-label col-lg-2" >{l s='Qnt.' mod='product_list'}</label>
		<div class="col-lg-2">
			<input type="text" id="edit_product_quantity" value="" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-lg-2" >{l s='Price' mod='product_list'}</label>
		<div class="col-lg-2">
			<input type="text" id="edit_product_price" value="" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<button type="button" id="edit_product_save" >{l s='Save'}</button>
	</div>
</div>

<script>
var priceDisplayPrecision = 2;
var orderCurrency = {$order_currency_json};
{*var orderProductsArray = {$order_products_json};*}
var orderInvoiceProducts = {$invoice_products|json_encode};
var orderProductsTable = $('#invoiceEditOrderProductsTable');
var orderInvoiceForm = $('#invoiceEditForm');
var catalogProducts = [];
$(function(){
	/*orderInvoiceForm.on('click', '#order_product_add', function(){
		invoiceEditProductAdd();
	});*/
	orderProductsTable.on('click', 'button.remove-product', function(){
		invoiceEditProductRemove(this);
	});
	$('#products_table').on('click', 'button.add-product', function(e){
		invoiceEditProductAdd($(this).data('product_id'), $(this).data('attribute_id'));
	});
	orderProductsTable.on('click', 'button.edit-product', function(e){
		invoiceEditProductEdit($(this).data('product_id'), $(this).data('attribute_id'));
	});
	orderProductsTable.on('click', '#edit_product_save', function(e){
		invoiceEditProductUpdate();
	});
	for( var pi in orderInvoiceProducts ){
		var product = {
			orderDetailId: orderInvoiceProducts[pi].id_order_detail,
			productId: orderInvoiceProducts[pi].id_product,
			attributeId: orderInvoiceProducts[pi].product_attribute_id,
			name: orderInvoiceProducts[pi].product_name,
			splRef: orderInvoiceProducts[pi].product_supplier_reference,
			quantity: orderInvoiceProducts[pi].product_quantity,
			priceTE: orderInvoiceProducts[pi].unit_price_tax_excl,
			productId: orderInvoiceProducts[pi].id_product,
			deletable: true
		};
		invoiceEditOrderProductRender(product);
	}
	$('#customer').typeWatch({
		captureLength: 2,
		highlight: true,
		wait: 100,
		callback: function(){ searchCustomers(); }
	});
	$('#customers').live('click', 'button.setup-customer', function(e){
		$('#customer_id').val( parseInt( $(e.target).data('customer') ) );
		$('#customers button.setup-customer').each(function(){
			$(this).removeClass('btn-success');
		});
		$(e.target).addClass('btn-success');
	});
	$('#tabAddresses a').click(function (e) {
		e.preventDefault()
		$(this).tab('show')
	})
});
function searchCustomers(){
	$.ajax({
		type:"POST",
		url : "{$link->getAdminLink('AdminCustomers')}",
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			tab: "AdminCustomers",
			action: "searchCustomers",
			customer_search: $('#customer').val()},
		success : function(res)
		{
			if(res.found)
			{
				var html = '';
				$.each(res.customers, function() {
					html += '<div class="customerCard col-lg-4">';
					html += '<div class="panel">';
					html += '<div class="panel-heading">'+this.firstname+' '+this.lastname;
					html += '<span class="pull-right">#'+this.id_customer+'</span></div>';
					html += '<span>'+this.email+'</span><br/>';
					html += '<span class="text-muted">'+((this.birthday != '0000-00-00') ? this.birthday : '')+'</span><br/>';
					html += '<div class="panel-footer">';
					//html += '<a href="{$link->getAdminLink('AdminCustomers')}&id_customer='+this.id_customer+'&viewcustomer&liteDisplaying=1" class="btn btn-default fancybox"><i class="icon-search"></i> {l s='Details'}</a>';
					html += '<button type="button" data-customer="'+this.id_customer+'" class="setup-customer btn btn-default pull-right"><i class="icon-arrow-right"></i> {l s='Choose'}</button>';
					html += '</div>';
					html += '</div>';
					html += '</div>';
				});
			}
			else
				html = '<div class="alert alert-warning"><i class="icon-warning-sign"></i>&nbsp;{l s='No customers found'}</div>';
			$('#customers').html(html);
			//resetBind();
		}
	});
}

function invoiceEditOrderProductRender(product){
	var productId = product.productId;
	var attributeId = product.attributeId;
	var productCmpsId = product.orderDetailId +'_'+ product.productId +'_'+ product.attributeId;
	if( orderProductsTable.find('tr[data-composed_id="'+productCmpsId+'"]').length ){
		return;
	}
	var productRow = $('<tr/>');
	productRow.data('order_detail_id', product.orderDetailId);
	productRow.data('product_id', product.productId);
	productRow.data('attribute_id',product.attributeId);
	productRow.attr('data-composed_id',productCmpsId);
	productRow.append($('<td></td>').html(product.name));
	productRow.append($('<td></td>').html(product.splRef));
	qntInp = $('<input>').attr({ name:'product_quantity['+productCmpsId+']', type:'text',size:5,
		value:product.quantity, class:'form-control product-quantity', id:'product_quantity__'+productCmpsId
	});
	productRow.append($('<td class="cell-product-quantity"></td>').append(qntInp));
	priceInp = $('<input>').attr({ name:'product_price_te['+productCmpsId+']', type:'text',size:8,
		value:parseFloat(product.priceTE).toFixed(priceDisplayPrecision), class:'form-control product-price-te',id:'product_price_te__'+productCmpsId
	});
	//productRow.append($('<td></td>').html(formatCurrency(priceTE, orderCurrency.format, orderCurrency.sign, orderCurrency.blank)));
	productRow.append($('<td class="cell-product-price-te"></td>').append(priceInp));
	//productRow.append($('<td class="cell-product-price-ti"></td>').html(formatCurrency(priceTI, orderCurrency.format, orderCurrency.sign, orderCurrency.blank)));
	productRow.append($('<td class="cell-product-total-te"></td>').html(formatCurrency(product.priceTE*product.quantity, orderCurrency.format, orderCurrency.sign, orderCurrency.blank)));
	//productRow.append($('<td class="cell-product-total-ti"></td>').html(formatCurrency(priceTI*1, orderCurrency.format, orderCurrency.sign, orderCurrency.blank)));
	var productActionsCell = $('<td></td>');
	if(product.deletable){
		productActionsCell.append(
			$('<button/>').attr({ type:'button',class:'btn btn-danger btn-xs  remove-product' })
			.html('<b>X</b>')
			.data({ order_detail_id:product.orderDetailId, product_id:product.productId, attribute_id:product.attributeId })
		);
	}
	productActionsCell.append(
		$('<input/>').attr({ type:'hidden',name:'products['+productCmpsId+']',value:productCmpsId })
	);
	/*productActionsCell.append(
		$('<input/>').attr({ type:'hidden',name:'product_order_detail_id['+product.productId+'_'+product.attributeId+']',value:product.productId+'_'+product.attributeId })
	);*/

	productRow.append(productActionsCell);
	orderProductsTable.append(productRow).promise().done(function(){
		$('#product_quantity__'+productCmpsId+',#product_price_te__'+productCmpsId).typeWatch({
			captureLength: 0,
			highlight: false,
			wait: 0,
			callback: function(e){
				var input = $(this.el);
				ids = input.attr('id').match(/(\d+)_(\d+)_(\d+)/);
				//var product = input.attr('rel').split('_');
				var parentTr = input.parents('tr');
				var priceInput = orderProductsTable.find('#product_price_te__'+ids[1]+'_'+ids[2]+'_'+ids[3]);
				var qntInput = orderProductsTable.find('#product_quantity__'+ids[1]+'_'+ids[2]+'_'+ids[3]);
				//var discount = new Number( input.val().replace(",",".") );
				var price = new Number( priceInput.val() );
				if( isNaN(price) ){
					parentTr.find('.cell-product-price-te').addClass('has-error');
					return;
				}
				parentTr.find('.cell-product-price-te').removeClass('has-error');
				
				var qnt = new Number( qntInput.val() );
				if( isNaN(qnt) ){
					parentTr.find('.cell-product-quantity').addClass('has-error');
					return;
				}
				parentTr.find('.cell-product-quantity').removeClass('has-error');

				var total = formatCurrency( price * qnt, orderCurrency.format, orderCurrency.sign, orderCurrency.blank);
				parentTr.find('.cell-product-total-te').html(total);
				//priceInput.val( price - discount );
			}
		});

	});

}
function invoiceEditProductAdd(productId, attributeId){
	var combination = { id_product_attribute:0 };
	productId = parseInt(productId);
	attributeId = parseInt(attributeId);
	for( var pi in catalogProducts ){
		if( catalogProducts[pi].id_product != productId ){
			continue;
		}
		
		if( catalogProducts[pi].combinations.length && attributeId ){
			for( var pci in catalogProducts[pi].combinations ){
				if( catalogProducts[pi].combinations[pci].id_product_attribute == attributeId ){
					combination = catalogProducts[pi].combinations[pci];
				}
			}
		}
		var product = {
			deletable: true,
			orderDetailId: 0,
			productId: productId,
			attributeId: attributeId,
			name: catalogProducts[pi].name,
			splRef: catalogProducts[pi].supplier_reference,
			quantity: 1,
			priceTE: new Number(catalogProducts[pi].price_tax_excl)
		};
		
		if(combination.id_product_attribute){
			product.name += ' '+ combination.attributes;
			product.splRef = combination.supplier_reference;
			product.priceTE = new Number(combination.price_tax_excl);
			//priceTI = new Number(combination.price_tax_incl);
		}
		invoiceEditOrderProductRender(product);
	}
}
function invoiceEditProductRemove(buttonObj){
	$(buttonObj).parents('tr').remove();
}
var treeClickFunc = function() {
	
	$.ajax({
		url: 'index.php?controller=AdminOrders&token='+token_admin_orders,
		dataType: "json",
		data : {
			ajax: "1",
			token: token_admin_orders,
			tab: "AdminOrders",
			action: "searchProductsByCategory",
			id_category: $(this).val()
		},
		success: function(res){
			var products_found = '';
			var attributes_html = '';
			var customization_html = '';
			stock = {};
            supplierReferences = {};

			if(!res.found){
				$('#products_found').hide();
				$('#products_err').html('No products found');
				$('#products_err').removeClass('hide');
				return;
			}
			
			catalogProducts = res.products;
			$('#products_found').show();
			var productsTable = $('#products_table');
			productsTable.find('tr.product-row').remove();
			$.each(res.products, function(){
				var combinationDefault = null;
				
				if(this.combinations.length){
					for(var ci in this.combinations){
						/*if(this.combinations[ci].default_on == "1"){
							combinationDefault = this.combinations[ci];
						}*/
						var productRow = $('<tr/>').data('product_id', this.id_product)
							.data('attribute_id', this.combinations[ci].id_product_attribute)
							.addClass('product-row');
						productRow.append($('<td/>').html(this.combinations[ci].supplier_reference));
						productRow.append( $('<td/>').html(this.name +': '+ this.combinations[ci].attributes) );
						productRow.append( $('<td/>').html(
							formatCurrency(new Number(this.combinations[ci].price_tax_excl), orderCurrency.format, orderCurrency.sign, orderCurrency.blank)) 
						);
						productRow.append( $('<td/>').html(this.combinations[ci].qty_in_stock) );
						productRow.append($('<td/>').append(
							$('<button/>').attr({ type:'button',class:'btn btn-xs add-product' })
							.text('Add').data({ product_id:this.id_product,attribute_id:this.combinations[ci].id_product_attribute })
						));
						productsTable.append(productRow);

					}
				}
				else{
					productName = this.name;
					productAttributeId = 0;
					var productRow = $('<tr/>').data('product_id', this.id_product)
						.data('attribute_id', productAttributeId).addClass('product-row');
					productRow.append($('<td/>').html(this.supplier_reference));

					productRow.append( $('<td/>').html(productName) );
					productRow.append( $('<td/>').html(
						formatCurrency(new Number(this.price_tax_excl), orderCurrency.format, orderCurrency.sign, orderCurrency.blank)
					) );
					productRow.append( $('<td/>').html(this.quantity) );
					productRow.append($('<td/>').append(
						$('<button/>').attr({ type:'button',class:'btn btn-xs add-product' })
						.text('Add').data({ product_id:this.id_product,attribute_id:0 })
					));

					productsTable.append(productRow);
					
				}
			});
			
			$('#id_product').change();
		}
	});
};

</script>