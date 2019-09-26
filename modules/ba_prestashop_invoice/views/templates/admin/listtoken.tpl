{*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="panel">
	<h3 class="col-sm-12"><i class="icon-code"></i>{l s='Tokens' mod='ba_prestashop_invoice'}</h3>
	<div class="row list_token col-sm-12" rev="close" style="height:100px;overflow:hidden;margin-bottom: 20px;">
		<div class="ba_invoice_token_wrapper">
			<label>[invoice_date]: </label>
			<label class="control-label">{l s='the date for create Invoice' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[invoice_number]: </label>
			<label class="control-label">{l s='Invoice Number, ex: 000001' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[barcode_invoice_number]: </label>
			<label class="control-label">{l s='invoice number barcode' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[payment_transaction_id]: </label>
			<label class="control-label">{l s='Transaction ID of invoice' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[gift_message]: </label>
			<label class="control-label">{l s='Gift message of Order' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[gift_wrapping_cost]: </label>
			<label class="control-label">{l s='Gift Wrapping of Order' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[cart_id]: </label>
			<label class="control-label">{l s='Cart ID of Order' mod='ba_prestashop_invoice'}</label>
		</div>
		<br/>
		
		<div class="ba_invoice_token_wrapper">
			<label>[order_number]: </label>
			<label class="control-label">{l s='The number of Order, ex: #OHSATSERP' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[order_date]</label>
			<label class="control-label">{l s='The date for checkout' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[order_payment_method]: </label>
			<label class="control-label">{l s='Name of Payment method for this Order' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[order_carrier]: </label>
			<label class="control-label">{l s='Name of Carrier' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[order_subtotal]: </label>
			<label class="control-label">{l s='Subtotal of Order include currency, ex:&nbsp;$73.90' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[order_shipping_cost]: </label>
			<label class="control-label">{l s='Shipping Cost of Order, ex:&nbsp;$2.00' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[order_tax]: </label>
			<label class="control-label">{l s='Tax of Order, ex: $1.50' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[order_discounted]: </label>
			<label class="control-label">{l s='The number of discounted Price' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[order_total]: </label>
			<label class="control-label">{l s='Order total (include Tax, Shipping Cost, Discounted)' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[order_notes]: </label>
			<label class="control-label">{l s='The notes of Order, ex: Lorem ipsum' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[order_message]: </label>
			<label class="control-label">{l s='The message of Order' mod='ba_prestashop_invoice'}</label>
		</div>
		
		<!--<div class="ba_invoice_token_wrapper">
			<label>[barcode_order_number]: </label>
			<label class="control-label">{l s='order number barcode' mod='ba_prestashop_invoice'}</label>
		</div>-->
		<br/>
		
		<div class="ba_invoice_token_wrapper">
			<label>[customer_email]: </label>
			<label class="control-label">{l s='Email of Customer.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[billing_firstname]: </label>
			<label class="control-label">{l s='Firstname of Billing Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[billing_lastname]: </label>
			<label class="control-label">{l s='Lastname of Billing Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[billing_company]: </label>
			<label class="control-label">{l s='Company of Billing Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[billing_address]: </label>
			<label class="control-label">{l s='Address of Billing Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[billing_address_line_2]: </label>
			<label class="control-label">{l s='Address 2 of Billing Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[billing_zipcode]: </label>
			<label class="control-label">{l s='Postcode/zipcode of Billing Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[billing_city]: </label>
			<label class="control-label">{l s='City of Billing Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[billing_state]: </label>
			<label class="control-label">{l s='State of billing address.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[billing_country]: </label>
			<label class="control-label">{l s='Country of Billing Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[billing_homephone]: </label>
			<label class="control-label">{l s='Homephone of Billing Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[billing_mobile_phone]: </label>
			<label class="control-label">{l s='Mobile phone of Billing Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[billing_additional_infomation]: </label>
			<label class="control-label">{l s='Additional Infomation of Billing Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[billing_vat_number]: </label>
			<label class="control-label">{l s='VAT Number of Billing Address' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[billing_dni]: </label>
			<label class="control-label">{l s='DNI / NIF / NIE of Billing Address' mod='ba_prestashop_invoice'}</label>
		</div>
		<br/>
		
		<div class="ba_invoice_token_wrapper">
			<label>[delivery_firstname]: </label>
			<label class="control-label">{l s='Firstname of Delivery (Shipping) Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[delivery_lastname]: </label>
			<label class="control-label">{l s='Lastname of Delivery (Shipping) Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[delivery_company]: </label>
			<label class="control-label">{l s='Company of Delivery (Shipping) Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[delivery_address]: </label>
			<label class="control-label">{l s='Address of Delivery (Shipping) Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[delivery_address_line_2]: </label>
			<label class="control-label">{l s='Address_2 of Delivery (Shipping) Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[delivery_zipcode]: </label>
			<label class="control-label">{l s='Zipcode/postcode of Delivery (Shipping) Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[delivery_city]: </label>
			<label class="control-label">{l s='City of Delivery (Shipping) Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[delivery_state]: </label>
			<label class="control-label">{l s='State of delivery address.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[delivery_country]: </label>
			<label class="control-label">{l s='Country of Delivery (Shipping) Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[delivery_homephone]: </label>
			<label class="control-label">{l s='Homephone of Delivery (Shipping) Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[delivery_mobile_phone]: </label>
			<label class="control-label">{l s='Mobile of Delivery (Shipping) Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[delivery_additional_infomation]: </label>
			<label class="control-label">{l s='Additional Infomation of Delivery (Shipping) Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[delivery_vat_number]: </label>
			<label class="control-label">{l s='VAT Number of Delivery (Shipping) Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[delivery_dni]: </label>
			<label class="control-label">{l s='DNI / NIF / NIE of Delivery (Shipping) Address when checkout.' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[delivery_date]: </label>
			<label class="control-label">{l s='Delivery (Shipping) date.' mod='ba_prestashop_invoice'}</label>
		</div>
		<br/>
		<div class="ba_invoice_token_wrapper">
			<label>[products_list]: </label>
			<label class="control-label">{l s='This token will be automatic replaced with products table which you can change columns, title,color... in Product Template section of our module.' mod='ba_prestashop_invoice'}</label>
		</div>
		<br/>
		<div class="ba_invoice_token_wrapper">
			<label>[total_product_excl_tax]: </label>
			<label class="control-label">{l s='Total products in Order exclude Tax' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[total_product_tax_rate]: </label>
			<label class="control-label">{l s='Tax Rate of an Order' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[total_product_tax_amount]: </label>
			<label class="control-label">{l s='Total Tax Amount of an Order' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[total_product_incl_tax]: </label>
			<label class="control-label">{l s='Total products in Order include Tax' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[shipping_cost_excl_tax]: </label>
			<label class="control-label">{l s='Total Shipping Cost exclude Tax' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[shipping_cost_tax_rate]: </label>
			<label class="control-label">{l s='Tax Rate of Shipping Cost' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[shipping_cost_tax_amount]: </label>
			<label class="control-label">{l s='Total Tax Amount of Shipping Cost' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[shipping_cost_incl_tax]: </label>
			<label class="control-label">{l s='Total Shipping Cost include Tax' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[total_order_excl_tax]: </label>
			<label class="control-label">{l s='Total Order (amount) exclude Tax' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[total_order_tax_amount]: </label>
			<label class="control-label">{l s='Total Tax (amount) = Products\'s Tax = Shipping\'s Tax' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[total_order_incl_tax]: </label>
			<label class="control-label">{l s='Total Order (amount) include Tax' mod='ba_prestashop_invoice'}</label>
		</div>
		<br/>
		<div class="ba_invoice_token_wrapper">
			<label>[COD_fees_include]: </label>
			<label class="control-label">{l s='Total Order (amount) include Tax' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[COD_fees_exclude]: </label>
			<label class="control-label">{l s='Total Order (amount) include Tax' mod='ba_prestashop_invoice'}</label>
		</div>
		<div class="ba_invoice_token_wrapper">
			<label>[COD_tax]: </label>
			<label class="control-label">{l s='Total Order (amount) include Tax' mod='ba_prestashop_invoice'}</label>
		</div>
		
	</div>
	<div style="" id="show_list_token">
		More<br/>
		<i class="icon-double-angle-down"></i>
	</div>
</div>
<script type="text/javascript">
	jQuery('#show_list_token').click(function(){
		var checkListToken = jQuery('.row.list_token').attr('rev');
		if(checkListToken=="close"){
			jQuery('.row.list_token').css('height','auto');
			jQuery('.row.list_token').attr('rev','open');
		}else if(checkListToken=="open"){
			jQuery('.row.list_token').css('height','100px');
			jQuery('.row.list_token').attr('rev','close');
		}
	});
</script>