{**
* OrderEdit
*
* @category  Module
* @author    silbersaiten <info@silbersaiten.de>
* @support   silbersaiten <support@silbersaiten.de>
* @copyright 2016 silbersaiten
* @version   1.2.7
* @link      http://www.silbersaiten.de
* @license   See joined file licence.txt
*}
<div class="row">
	<ul class="nav nav-tabs" id="tabAddresses">
		<li class="active">
			<a href="#addressShipping">
				<i class="icon-truck"></i>
				{l s='Shipping address' mod='orderedit'}
			</a>
		</li>
		<li>
			<a href="#addressInvoice">
				<i class="icon-file-text"></i>
				{l s='Invoice address' mod='orderedit'}
			</a>
		</li>
	</ul>
	<div class="tab-content panel">
		<!-- Tab status -->
		<div class="tab-pane  in active" id="addressShipping">
			<!-- Addresses -->
			<h4 class="visible-print">{l s='Shipping address' mod='orderedit'}</h4>
			{if !$order->isVirtual()}
			<!-- Shipping address -->
				{if $can_edit}
					<form class="form-horizontal hidden-print" method="post" id="addressShippingSubmitForm" action="{$current_index|escape:'html':'UTF-8'}&vieworder&token={$token|escape:'html':'UTF-8'}&id_order={$order->id|escape:'html':'UTF-8'}">
						<div class="form-group">
							<div class="col-lg-9">
								<select name="id_address" id="id_address_shipping">
									{foreach from=$customer_addresses item=address}
									<option value="{$address['id_address']|escape:'html':'UTF-8'}"
										{if $address['id_address'] == $order->id_address_delivery}
											selected="selected"
										{/if}>
										{$address['alias']|escape:'html':'UTF-8'} -
										{$address['address1']|escape:'html':'UTF-8'}
										{$address['postcode']|escape:'html':'UTF-8'}
										{$address['city']|escape:'html':'UTF-8'}
										{if !empty($address['state'])}
											{$address['state']|escape:'html':'UTF-8'}
										{/if},
										{$address['country']|escape:'html':'UTF-8'}
									</option>
									{/foreach}
								</select>
							</div>
							<div class="col-lg-3">
								<button class="btn btn-default" type="submit" name="submitAddressShipping"><i class="icon-refresh"></i> {l s='Change' mod='orderedit'}</button>
							</div>
						</div>
					</form>
				{/if}
				<div class="well">
					<div class="row">
						<div class="col-sm-6">
							<a class="btn btn-default pull-right" href="?tab=AdminAddresses&amp;id_address={$addresses.delivery->id|escape:'html':'UTF-8'}&amp;addaddress&realedit=1&amp;id_order={$order->id|escape:'html':'UTF-8'}{if ($addresses.delivery->id == $addresses.invoice->id)}&amp;address_type=1{/if}&amp;token={getAdminToken tab='AdminAddresses'}&back={$smarty.server.REQUEST_URI|urlencode}">
								<i class="icon-pencil"></i>
								{l s='Edit' mod='orderedit'}
							</a>
							{displayAddressDetail address=$addresses.delivery newLine='<br />'}
							{if $addresses.delivery->other}
								<hr />{$addresses.delivery->other|escape:'html':'UTF-8'}<br />
							{/if}
						</div>
						<div class="col-sm-6 hidden-print">
							<div id="map-delivery-canvas" style="height: 190px"></div>
						</div>
					</div>
				</div>
			{/if}
		</div>
		<div class="tab-pane " id="addressInvoice">
			<!-- Invoice address -->
			<h4 class="visible-print">{l s='Invoice address' mod='orderedit'}</h4>
			{if $can_edit}
				<form class="form-horizontal hidden-print" method="post" id="addressInvoiceSubmitForm" action="{$current_index|escape:'html':'UTF-8'}&vieworder&token={$token|escape:'html':'UTF-8'}&id_order={$order->id|escape:'html':'UTF-8'}">
					<div class="form-group">
						<div class="col-lg-9">
							<select name="id_address" id="id_address_invoice">
								{foreach from=$customer_addresses item=address}
								<option value="{$address['id_address']|escape:'html':'UTF-8'}"
									{if $address['id_address'] == $order->id_address_invoice}
									selected="selected"
									{/if}>
									{$address['alias']|escape:'html':'UTF-8'} -
									{$address['address1']|escape:'html':'UTF-8'}
									{$address['postcode']|escape:'html':'UTF-8'}
									{$address['city']|escape:'html':'UTF-8'}
									{if !empty($address['state'])}
										{$address['state']|escape:'html':'UTF-8'}
									{/if},
									{$address['country']|escape:'html':'UTF-8'}
								</option>
								{/foreach}
							</select>
						</div>
						<div class="col-lg-3">
							<button class="btn btn-default" type="submit" name="submitAddressInvoice"><i class="icon-refresh"></i> {l s='Change' mod='orderedit'}</button>
						</div>
					</div>
				</form>
			{/if}
			<div class="well">
				<div class="row">
					<div class="col-sm-6">
						<a class="btn btn-default pull-right" href="?tab=AdminAddresses&amp;id_address={$addresses.invoice->id|escape:'html':'UTF-8'}&amp;addaddress&amp;realedit=1&amp;id_order={$order->id|escape:'html':'UTF-8'}{if ($addresses.delivery->id == $addresses.invoice->id)}&amp;address_type=2{/if}&amp;back={$smarty.server.REQUEST_URI|urlencode|escape:'html':'UTF-8'}&amp;token={getAdminToken tab='AdminAddresses'}">
							<i class="icon-pencil"></i>
							{l s='Edit' mod='orderedit'}
						</a>
						{displayAddressDetail address=$addresses.invoice newLine='<br />'}
						{if $addresses.invoice->other}
							<hr />{$addresses.invoice->other|escape:'html':'UTF-8'}<br />
						{/if}
					</div>
					<div class="col-sm-6 hidden-print">
						<div id="map-invoice-canvas" style="height: 190px"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$('#tabAddresses a').click(function (e) {
		e.preventDefault()
		$(this).tab('show')
	})
</script>
