{*
* Do not edit the file if you want to upgrade in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}
<div class="panel">
	<div id="intro_gwadvancedinvoice">
        <div class="row">
    		<div id="left_intro" class="col-lg-8 col-md-8">
    			<h4>{l s='Advanced Invoice Template Builder' mod='gwadvancedinvoice'}</h4><br/>
    			<table class="table">
                    <thead>
                        <tr>
                            <td class="variable_colunm"><h2>{l s='Variable' mod='gwadvancedinvoice'}</h2></td>
                            <td><h2>{l s='Note' mod='gwadvancedinvoice'}</h2></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>{l s='{$shopname}' mod='gwadvancedinvoice'}</td><td>{l s='Shop name' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$shopurl}' mod='gwadvancedinvoice'}</td><td>{l s='Shop Url' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$logo}' mod='gwadvancedinvoice'}</td><td>{l s='Shop logo image' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$customeremail}' mod='gwadvancedinvoice'}</td><td>{l s='Customer email' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$customerfirstname}' mod='gwadvancedinvoice'}</td><td>{l s='Customer firstname' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$customerlastname}' mod='gwadvancedinvoice'}</td><td>{l s='Customer lastname' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$id_order}' mod='gwadvancedinvoice'}</td><td>{l s='Order Id, Order Number' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$id_cart}' mod='gwadvancedinvoice'}</td><td>{l s='Cart Id' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$barcode_invoice}' mod='gwadvancedinvoice'}</td><td>{l s='Display Invoice barcode' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$reference}' mod='gwadvancedinvoice'}</td><td>{l s='Order reference - E.g: DHSEPTLQH' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$invoice_number}' mod='gwadvancedinvoice'}</td><td>{l s='Invoice number - E.g: #IN000001' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$delivery_number}' mod='gwadvancedinvoice'}</td><td>{l s='Delivery number - E.g: #D000001' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$invoice_date}' mod='gwadvancedinvoice'}</td><td>{l s='Date create of invoice' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$date_add}' mod='gwadvancedinvoice'}</td><td>{l s='Date create of order' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$date_upd}' mod='gwadvancedinvoice'}</td><td>{l s='Date update of order' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$payment}' mod='gwadvancedinvoice'}</td><td>{l s='Payment method - E.g: Paypal' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$order_carrier_name}' mod='gwadvancedinvoice'}</td><td>{l s='Name of phipping method - E.g: Fedex, UPS' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$order_carrier_logo}' mod='gwadvancedinvoice'}</td><td>{l s='Logo of shipping method' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$tax_tab}' mod='gwadvancedinvoice'}</td><td>{l s='Display Tax table detail' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$HOOK_DISPLAY_PDF}' mod='gwadvancedinvoice'}</td><td>{l s='The hook supports other modules display content in pdf. Note: Do not work in preview' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$order_notes}' mod='gwadvancedinvoice'}</td><td>{l s='Order message' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$order_status}' mod='gwadvancedinvoice'}</td><td>{l s='Order status' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$gift_message}' mod='gwadvancedinvoice'}</td><td>{l s='Gift message of Order' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                        <tr class="group_variable_title"><td colspan="2"><strong>{l s='Billing address variable' mod='gwadvancedinvoice'}</strong></strong></td></tr>
                        <tr><td>{l s='{$billing_state}' mod='gwadvancedinvoice'}</td><td>{l s='Customer state. E.g: Florida' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$billing_country}' mod='gwadvancedinvoice'}</td><td>{l s='Customer country. E.g: United States' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$billing_alias}' mod='gwadvancedinvoice'}</td><td>{l s='Address title. E.g: My address' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$billing_company}' mod='gwadvancedinvoice'}</td><td>{l s='Customer company name. E.g:  Globo Software Solution jsc' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$billing_lastname}' mod='gwadvancedinvoice'}</td><td>{l s='Customer last name. E.g: DOE' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$billing_firstname}' mod='gwadvancedinvoice'}</td><td>{l s='Customer first name. E.g: John' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$billing_address1}' mod='gwadvancedinvoice'}</td><td>{l s='Customer Address 1. E.g: 16, Main street' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$billing_address2}' mod='gwadvancedinvoice'}</td><td>{l s='Customer Address 2. E.g: 2nd floor' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$billing_postcode}' mod='gwadvancedinvoice'}</td><td>{l s='Customer Postcode. E.g: 33133' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$billing_city}' mod='gwadvancedinvoice'}</td><td>{l s='Customer city. E.g: Miami' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$billing_other}' mod='gwadvancedinvoice'}</td><td>{l s='Customer other information.' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$billing_phone}' mod='gwadvancedinvoice'}</td><td>{l s='Customer phone number. E.g: 0102030405' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$billing_phone_mobile}' mod='gwadvancedinvoice'}</td><td>{l s='Customer mobile phone number. E.g: 01234567899' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$billing_vat_number}' mod='gwadvancedinvoice'}</td><td>{l s='Customer VAT number. E.g: 12345' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$billing_dni}' mod='gwadvancedinvoice'}</td><td>{l s='Identification Number. E.g: 1234' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                        <tr class="group_variable_title"><td colspan="2"><strong>{l s='Delivery address variables' mod='gwadvancedinvoice'}</strong></td></tr>
                        <tr><td>{l s='{$delivery_state}' mod='gwadvancedinvoice'}</td><td>{l s='Customer State. E.g: Florida' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$delivery_country}' mod='gwadvancedinvoice'}</td><td>{l s='Customer Country. E.g: Singapore, Vietnam' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$delivery_alias}' mod='gwadvancedinvoice'}</td><td>{l s='Address title. E.g: My address' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$delivery_company}' mod='gwadvancedinvoice'}</td><td>{l s='Customer company name, E.g: Globo Software Solution jsc' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$delivery_lastname}' mod='gwadvancedinvoice'}</td><td>{l s='Customer last name. E.g: DOE' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$delivery_firstname}' mod='gwadvancedinvoice'}</td><td>{l s='Customer first name E.g: John' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$delivery_address1}' mod='gwadvancedinvoice'}</td><td>{l s='Customer address 1. E.g: No 45C, 210 Hoang Quoc Viet Street, Hanoi, Vietnam' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$delivery_address2}' mod='gwadvancedinvoice'}</td><td>{l s='Customer address 2' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$delivery_postcode}' mod='gwadvancedinvoice'}</td><td>{l s='Customer postcode. E.g: 33133' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$delivery_city}' mod='gwadvancedinvoice'}</td><td>{l s='Customer City. E.g: Miami' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$delivery_other}' mod='gwadvancedinvoice'}</td><td>{l s='Customer other information' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$delivery_phone}' mod='gwadvancedinvoice'}</td><td>{l s='Customer phone number. E.g: +84 944 262 010' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$delivery_phone_mobile}' mod='gwadvancedinvoice'}</td><td>{l s='Customer mobile phone number. E.g: 01234567899' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$delivery_vat_number}' mod='gwadvancedinvoice'}</td><td>{l s='Customer vat number. E.g: 12345' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$delivery_dni}' mod='gwadvancedinvoice'}</td><td>{l s='Identification Number. E.g: 1234' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                        <tr class="group_variable_title"><td colspan="2"><strong>{l s='Price variables' mod='gwadvancedinvoice'}</strong></td></tr>
                        <tr><td>{l s='{displayPrice:$total_discounts_tax_incl}' mod='gwadvancedinvoice'}</td><td>{l s='Display total discounts tax include.' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{displayPrice:$total_discounts_tax_excl}' mod='gwadvancedinvoice'}</td><td>{l s='Display total discounts tax exclude.' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{displayPrice:$total_paid_tax_incl}' mod='gwadvancedinvoice'}</td><td>{l s='Display total paid tax include.' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{displayPrice:$total_paid_tax_excl}' mod='gwadvancedinvoice'}</td><td>{l s='Display total paid tax exclude.' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{displayPrice:$total_paid_real}' mod='gwadvancedinvoice'}</td><td>{l s='Display total real price include tax but do not include discount.' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{displayPrice:$total_products}' mod='gwadvancedinvoice'}</td><td>{l s='Display total products price exclude tax.' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{displayPrice:$total_products_wt}' mod='gwadvancedinvoice'}</td><td>{l s='Display total products price include tax' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{displayPrice:$total_shipping_tax_incl}' mod='gwadvancedinvoice'}</td><td>{l s='Display total shipping tax include.' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{displayPrice:$total_shipping_tax_excl}' mod='gwadvancedinvoice'}</td><td>{l s='Display total shipping tax exclude' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{displayPrice:$carrier_tax_rate}' mod='gwadvancedinvoice'}</td><td>{l s='Display carrier tax rate' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{displayPrice:$total_wrapping_tax_incl}' mod='gwadvancedinvoice'}</td><td>{l s='Display gift wrapping price tax include' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{displayPrice:$total_wrapping_tax_excl}' mod='gwadvancedinvoice'}</td><td>{l s='Display gift wrapping price tax exclude' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{displayPrice:$footer.total_taxes}' mod='gwadvancedinvoice'}</td><td>{l s='Display total taxes price.' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                        <tr class="group_variable_title"><td colspan="2"><strong>{l s='Product variables' mod='gwadvancedinvoice'}</strong></td></tr>
                        <tr><td>{l s='{$products_list}' mod='gwadvancedinvoice'}</td><td>{l s='Display list of product as table' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$order_detail.product_id}' mod='gwadvancedinvoice'}</td><td>{l s='Product Id' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$order_detail.product_attribute_id}' mod='gwadvancedinvoice'}</td><td>{l s='Product attribute id' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$order_detail.product_name}' mod='gwadvancedinvoice'}</td><td>{l s='Product name' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$order_detail.description_short}' mod='gwadvancedinvoice'}</td><td>{l s='Product short description' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$order_detail.product_quantity}' mod='gwadvancedinvoice'}</td><td>{l s='Product quantity' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$order_detail.image_tag}' mod='gwadvancedinvoice'}</td><td>{l s='Product image' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$order_detail.barcode}' mod='gwadvancedinvoice'}</td><td>{l s='Product barcode' mod='gwadvancedinvoice'}</td></tr>
                        {*<!--tr><td>{l s='{$order_detail.product_price}' mod='gwadvancedinvoice'}</td><td>{l s='Product unit price' mod='gwadvancedinvoice'}</td></tr-->*}
                        <tr><td>{l s='{$order_detail.reduction_percent}' mod='gwadvancedinvoice'}</td><td>{l s='Product reduction by percent. E.g: 5%' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$order_detail.reduction_amount}' mod='gwadvancedinvoice'}</td><td>{l s='Product reduction by amount. E.g: $5' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$order_detail.ean13}' mod='gwadvancedinvoice'}</td><td>{l s='Ean13 number' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$order_detail.upc}' mod='gwadvancedinvoice'}</td><td>{l s='Upc number' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$order_detail.price}' mod='gwadvancedinvoice'}</td><td>{l s='' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$order_detail.reference}' mod='gwadvancedinvoice'}</td><td>{l s='Product Reference' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{displayPrice:$order_detail.unit_price_tax_excl_including_ecotax}' mod='gwadvancedinvoice'}</td><td>{l s='Product unit price without tax' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{displayPrice:$order_detail.unit_price_tax_incl_including_ecotax}' mod='gwadvancedinvoice'}</td><td>{l s='Product unit price include tax' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{displayPrice:$order_detail.total_price_tax_excl_including_ecotax}' mod='gwadvancedinvoice'}</td><td>{l s='Total price without tax of a product. This is unit price multiplication quantity.' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{displayPrice:$order_detail.total_price_tax_incl_including_ecotax}' mod='gwadvancedinvoice'}</td><td>{l s='Total price include tax of a product. This is unit price multiplication quantity.' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{displayPrice:$order_detail.total_tax}' mod='gwadvancedinvoice'}</td><td>{l s='Total tax price of a product.' mod='gwadvancedinvoice'}</td></tr>
                        <tr><td>{l s='{$order_detail.order_detail_tax_label}' mod='gwadvancedinvoice'}</td><td>{l s='Product tax rate' mod='gwadvancedinvoice'}</td></tr>
                    </tbody>
                </table>
    		</div>
    		<div id="right_intro" class="col-lg-4 col-md-4">
    			<h4>{l s='Our team is available for customisation work or support. Contact us: ' mod='gwadvancedinvoice'}</h4><br/>
    			<ul>
    				<li>
    					<span>{l s='Skype: ' mod='gwadvancedinvoice'}globosoftware</span>
    				</li>
    				<li>
    					<span>{l s='Web: ' mod='gwadvancedinvoice'}</span><a href="http://www.globosoftware.net/">{l s='www.globosoftware.net/' mod='gwadvancedinvoice'}</a>
    				</li>
    			</ul>
                <div class="clear"><br/></div>
                <div id="other_globo_module">
                    <iframe style="width:100%;max-height:500px;" frameBorder="0" src="http://www.globosoftware.net/prestashop-modules-rss.html"></iframe>
                </div>
    		</div>
            <div class="clear"><br/></div>
        </div>
	</div>
</div>
<div class="clear"><br/></div>