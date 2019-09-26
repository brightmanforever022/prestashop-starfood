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
	<div class="panel" style="float:left;width:100%;padding:0;">
		<div class="row"  id="sample_product_template" style="display:none;">
			<div class='product_template col-sm-12'>
				<div class="product_template_head"><i class="icon-th"></i> <span class="header_text"></span></div>
				<div class='form-group col-sm-6'>
					<label class='col-sm-3 control-label' style='text-align:right;'>Name column</label>
					<div class='col-sm-9'>
						<input type='text' name="colums_title[]"/>
					</div>
				</div>
				<div class='form-group col-sm-6'>
					<label class='col-sm-3 control-label' style='text-align:right;'>Content column</label>
					<div class='col-sm-9'>
						<select name="colums_content[]">
							<option value="1">{l s='Product name' mod='ba_prestashop_invoice'}</option>
							<option value="13">{l s='Product Name with Customization' mod='ba_prestashop_invoice'}</option>
							<option value="2">{l s='SKU' mod='ba_prestashop_invoice'}</option>
							<option value="3">{l s='Unit Price(Tax Excl)' mod='ba_prestashop_invoice'}</option>
							<option value="4">{l s='Unit Price(Tax Incl) - Without Old Price' mod='ba_prestashop_invoice'}</option>
							<option value="11">{l s='Unit Price(Tax Incl) - With Old Price' mod='ba_prestashop_invoice'}</option>
							<option value="5">{l s='Product Tax' mod='ba_prestashop_invoice'}</option>
							<option value="6">{l s='Discounted' mod='ba_prestashop_invoice'}</option>
							<option value="7">{l s='Qty' mod='ba_prestashop_invoice'}</option>
							<option value="14">{l s='Total(Tax excl)' mod='ba_prestashop_invoice'}</option>
							<option value="8">{l s='Total(Tax Incl)' mod='ba_prestashop_invoice'}</option>
							<option value="9">{l s='Product Image' mod='ba_prestashop_invoice'}</option>
							<option value="10">{l s='Tax Rate' mod='ba_prestashop_invoice'}</option>
							<option value="12">{l s='Product Reference' mod='ba_prestashop_invoice'}</option>
							<option value="15">{l s='Tax Name' mod='ba_prestashop_invoice'}</option>
							<option value="16">{l s='Unit Price(Tax Excl) not Discount' mod='ba_prestashop_invoice'}</option>
							<option value="17">{l s='Total(Tax Excl) not Discount' mod='ba_prestashop_invoice'}</option>
							<option value="18">{l s='Product Warehouse Location' mod='ba_prestashop_invoice'}</option>
						</select>
					</div>
				</div>
				<div class='form-group col-sm-6'>
					<label class='col-sm-3 control-label' style='text-align:right;'>Color title column</label>
					<div class='col-sm-2'>
						<input type='text' class='color' name="colums_color[]"/>
					</div>
				</div>
				<div class='form-group col-sm-6'>
					<label class='col-sm-3 control-label' style='text-align:right;'>Color background title column</label>
					<div class='col-sm-2'>
						<input type='text' class='color' name="colums_bgcolor[]"/>
					</div>
				</div>
			</div>
		</div>
		
		<form class="form-horizontal"  method="POST">
			
			<div class="" style="padding:0 10px;width:100%;float:left;">
				<h3 class="col-sm-12" style="margin:0 !important;">
					<i class="icon-wrench"></i> {l s='General' mod='ba_prestashop_invoice'}
					<span class="panel-heading-action">
						{foreach from=$toolBarBtn item=btn key=k}
							<a id="desc-delivery-{$table|escape:'htmlall':'UTF-8'}-{if isset($btn.imgclass)}{$btn.imgclass|escape:'htmlall':'UTF-8'}{else}{$k|escape:'htmlall':'UTF-8'}{/if}" class="list-toolbar-btn" {if isset($btn.href)}href="{$btn.href|escape:'htmlall':'UTF-8'}"{/if} {if isset($btn.target) && $btn.target}target="_blank"{/if}{if isset($btn.js) && $btn.js}onclick="{$btn.js|escape:'htmlall':'UTF-8'}"{/if}>
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s=$btn.desc mod='ba_prestashop_invoice'}" data-html="true">
									<i class="process-icon-{if isset($btn.imgclass)}{$btn.imgclass|escape:'htmlall':'UTF-8'}{else}{$k|escape:'htmlall':'UTF-8'}{/if} {if isset($btn.class)}{$btn.class|escape:'htmlall':'UTF-8'}{/if}" ></i>
								</span>
							</a>
						{/foreach}
					</span>
				</h3>
				
				<div class="col-sm-12" style="margin-top:7px;">
					<div class="col-sm-12" style="margin-bottom: 10px;">
						<div class="col-sm-4 control-label" style="padding:0;font-size: 13px;">{l s='Invoice Name' mod='ba_prestashop_invoice'}: </div>
						<div class="col-sm-3"><input type="text" name="nameInvoice" value=""></div>
					</div>
					<div class="col-sm-12" style="margin-bottom: 10px;">
						<div class="col-sm-4 control-label" style="padding:0;font-size: 13px;">{l s='Invoice Description' mod='ba_prestashop_invoice'}: </div>
						<div class="col-sm-3"><textarea name="descriptionInvoice"></textarea></div>
					</div>
                                        <div class="col-sm-12" style="margin-bottom: 10px;">
						<div class="col-sm-4 control-label" style="padding:0;font-size: 13px;">{l s='Auto set paid' mod='ba_prestashop_invoice'}: </div>
						<div class="col-sm-5"><input type="checkbox" name="auto_set_paid" value="1" autocomplete="off"/></div>
					</div>
					<div class="col-sm-12" style="margin-bottom: 10px;">
						<div class="col-sm-4 control-label" style="padding:0;font-size: 13px;">{l s='Show shipping in product list' mod='ba_prestashop_invoice'}: </div>
						<div class="col-sm-5"><input type="checkbox" name="showShippingInProductList" value="1"/></div>
					</div>
					<div class="col-sm-12" style="margin-bottom: 10px;">
						<div class="col-sm-4 control-label" style="padding:0;font-size: 13px;">{l s='Show discount in product list' mod='ba_prestashop_invoice'}: </div>
						<div class="col-sm-5"><input type="checkbox" name="showDiscountInProductList" value="1"/></div>
					</div>
					<div class="col-sm-12" style="margin-bottom: 10px;">
						<div class="col-sm-4 control-label" style="padding:0;font-size: 13px;">{l s='Enable Landscape PDF' mod='ba_prestashop_invoice'}: </div>
						<div class="col-sm-5"><input type="checkbox" name="baInvoiceEnableLandscape" value="1"/></div>
					</div>
					<div class="col-sm-12" style="margin-bottom: 10px;">
						<div class="col-sm-4 control-label" style="padding:0;font-size: 13px;">{l s='Show Pagination' mod='ba_prestashop_invoice'}: </div>
						<div class="col-sm-5"><input type="checkbox" name="showPagination" value="1"/></div>
					</div>
					<div class="col-sm-12" style="margin-bottom: 10px;">
						<div class="col-sm-4 control-label" style="padding:0;font-size: 13px;">{l s='Language' mod='ba_prestashop_invoice'}: </div>
						<div class="col-sm-2">
							{include file="$_PS_MODULE_DIR_/ba_prestashop_invoice/views/templates/admin/select_language.tpl"}
						</div>
					</div>
					<div class="col-sm-12" style="margin-bottom: 10px;">
						<div class="col-sm-4 control-label" style="padding:0;font-size: 13px;">{l s='Default' mod='ba_prestashop_invoice'}: </div>
						<div class="col-sm-5">
							<span class="switch prestashop-switch fixed-width-lg">
								<input onclick="toggleDraftWarning(false);showOptions(true);showRedirectProductOptions(false);" type="radio" name="status" id="active_on" value="1" checked>
								<label for="active_on" class="radioCheck">
									{l s='Yes' mod='ba_prestashop_invoice'}
								</label>
								<input onclick="toggleDraftWarning(true);showOptions(false);showRedirectProductOptions(true);" type="radio" name="status" id="active_off" value="0">
								<label for="active_off" class="radioCheck">
									{l s='No' mod='ba_prestashop_invoice'}
								</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
					</div>
				</div>
				<h3 class="col-sm-12" style="margin:0;"><i class="icon-code"></i>{l s='Tokens' mod='ba_prestashop_invoice'}</h3>
				<div class="row list_token col-sm-12" rev="close" style="height:100px;overflow:hidden;margin-bottom: 20px;">
                                        <div class="ba_invoice_token_wrapper">
						<label>[customerId]: </label>
						<label class="control-label">{l s='id of customer (account number)' mod='ba_prestashop_invoice'}</label>
					</div>    
                                        <div class="ba_invoice_token_wrapper">
						<label>[today+10]: </label>
						<label class="control-label">{l s='Date 10 days in future from today' mod='ba_prestashop_invoice'}</label>
					</div>
                                        <div class="ba_invoice_token_wrapper">
						<label>[orderInvoiceTxt]: </label>
						<label class="control-label">{l s='Text from order details page' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="ba_invoice_token_wrapper">
						<label>[displayPDFInvoice]: </label>
						<label class="control-label">{l s='load Data from displayPDFInvoice HOOK' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="ba_invoice_token_wrapper">
						<label>[displayPDFDeliverySlip]: </label>
						<label class="control-label">{l s='load Data from displayPDFDeliverySlip HOOK' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="ba_invoice_token_wrapper">
						<label>[invoice_date]: </label>
						<label class="control-label">{l s='the date for create Invoice' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="ba_invoice_token_wrapper">
						<label>[delivery_number]: </label>
						<label class="control-label">{l s='Delivery Number, ex: 000001' mod='ba_prestashop_invoice'}</label>
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
						<label>[order_total_not_discount_excl]: </label>
						<label class="control-label">{l s='Order total (exclude Tax, Shipping Cost, No Discounted)' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="ba_invoice_token_wrapper">
						<label>[order_total_not_discount_incl]: </label>
						<label class="control-label">{l s='Order total (include Tax, Shipping Cost, No Discounted)' mod='ba_prestashop_invoice'}</label>
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
						<label>[customer_outstanding_amount]: </label>
						<label class="control-label">{l s='Allowed outstanding amount of Customer.' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="ba_invoice_token_wrapper">
						<label>[customer_max_payment_days]: </label>
						<label class="control-label">{l s='Maximum number of payment days of Customer.' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="ba_invoice_token_wrapper">
						<label>[customer_risk_rating]: </label>
						<label class="control-label">{l s='Risk rating of Customer.' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="ba_invoice_token_wrapper">
						<label>[customer_company]: </label>
						<label class="control-label">{l s='Customer Company field.' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="ba_invoice_token_wrapper">
						<label>[customer_siret]: </label>
						<label class="control-label">{l s='Customer SIRET field.' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="ba_invoice_token_wrapper">
						<label>[customer_ape]: </label>
						<label class="control-label">{l s='Customer APE field.' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="ba_invoice_token_wrapper">
						<label>[customer_website]: </label>
						<label class="control-label">{l s='Customer Website field.' mod='ba_prestashop_invoice'}</label>
					</div>
					<br/>
					<div class="ba_invoice_token_wrapper">
						<label>[billing_due_date]: </label>
						<label class="control-label">{l s='Due Date of Invoice.' mod='ba_prestashop_invoice'}</label>
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
					<div class="ba_invoice_token_wrapper">
						<label>[tax_table]: </label>
						<label class="control-label">{l s='Show Tax Table' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="ba_invoice_token_wrapper">
						<label>[individual_tax_table]: </label>
						<label class="control-label">{l s='Show Individual Tax Table' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="ba_invoice_token_wrapper">
						<label>[newsletter_table]: </label>
						<label class="control-label">{l s='display Table Yes/No if newsletter is enabled' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="ba_invoice_token_wrapper">
						<label>[discount_table]: </label>
						<label class="control-label">{l s='Display Table all Discount in order' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="ba_invoice_token_wrapper">
						<label>[total_voucher_amount_tax_incl]: </label>
						<label class="control-label">{l s='Total all amount of Coupon/Voucher include TAX' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="ba_invoice_token_wrapper">
						<label>[total_voucher_amount_tax_excl]: </label>
						<label class="control-label">{l s='Total all amount of Coupon/Voucher exclude TAX' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="payment_fee_incl">
						<label>[payment_fee_incl]: </label>
						<label class="control-label">{l s='Payment Fees include Tax' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="payment_fee_incl">
						<label>[payment_fee_excl]: </label>
						<label class="control-label">{l s='Payment Fees exclude Tax' mod='ba_prestashop_invoice'}</label>
					</div>
					<div class="payment_fee_incl">
						<label>[payment_fee_tax_amount]: </label>
						<label class="control-label">{l s='Tax Amount applied to Payment Fees' mod='ba_prestashop_invoice'}</label>
					</div>
				</div>
				<div style="" id="show_list_token">
					More<br/>
					<i class="icon-double-angle-down"></i>
				</div>
				<h3 class="col-sm-12" style="margin:0 0 20px;">{l s='Template' mod='ba_prestashop_invoice'}</h3>
				<div class="row" style="margin-bottom:10px">
					<div>
						<label class="col-sm-1 control-label" style="padding:0;">{l s='Header Template' mod='ba_prestashop_invoice'}</label>
						<div class="col-sm-10">
							<textarea class="rte" name="header_invoice_template"></textarea>
						</div>
					</div>
				</div>
				<div class="row" style="margin-bottom:10px">
					<div>
						<label class="col-sm-1 control-label">{l s='Invoice Content' mod='ba_prestashop_invoice'}</label>
						<div class="col-sm-10">
							<textarea class="rte" name="invoice_template"></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div>
						<label class="col-sm-1 control-label">{l s='Footer Template' mod='ba_prestashop_invoice'}</label>
						<div class="col-sm-10">
							<textarea class="rte" name="footer_invoice_template"></textarea>
						</div>
					</div>
				</div>
				<div class="row" style="margin-top:10px;">
					<div>
						<label class="col-sm-1 control-label">{l s='Customize CSS' mod='ba_prestashop_invoice'}</label>
						<div class="col-sm-10">
							<textarea style="height:155px;" name="customize_css"></textarea>
						</div>
					</div>
				</div>
				
				<div class="product_template">
					<h3 style="float:left;width:100%;margin:0 0 10px;margin: 10px 0 15px;"><i class="icon-table"></i> Products list template</h3>
					<div class="number_column">
						<div class="form-group">
							<label class="col-sm-1 control-label">{l s='Numbber of table' mod='ba_prestashop_invoice'}</label>
							<div class="col-sm-10">
								<input type="text" id="numberColumnOfTableTemplaterPro" value="0" name="numberColumnOfTableTemplaterPro" onchange="addColumn()">
							</div>
						</div>
					</div>
					
					<div id="product_list_columns">
						{for $i=0 to 0-1}
						<div class='product_template product_template_{$i+1|escape:'htmlall':'UTF-8'} col-sm-12'>
							<div class="product_template_head"><i class="icon-th"></i> <span class="header_text">Column {$i+1|escape:'htmlall':'UTF-8'}</span></div>
							<div class='form-group col-sm-6'>
								<label class='col-sm-3 control-label' style='text-align:right;'>Name column</label>
								<div class='col-sm-9'>
									<input type='text' name="colums_title[]" value=""/>
								</div>
							</div>
							<div class='form-group col-sm-6'>
								<label class='col-sm-3 control-label' style='text-align:right;'>Content column</label>
								<div class='col-sm-9'>
									<select name="colums_content[]">
										<option value="1" {if $invoiceTemplate.columsContentJson.$i=="1"}selected{/if}>{l s='Product name' mod='ba_prestashop_invoice'}</option>
										<option value="13" {if $invoiceTemplate.columsContentJson.$i=="13"}selected{/if}>{l s='Product Name with Customization' mod='ba_prestashop_invoice'}</option>
										<option value="2" {if $invoiceTemplate.columsContentJson.$i=="2"}selected{/if}>{l s='SKU' mod='ba_prestashop_invoice'}</option>
										<option value="3" {if $invoiceTemplate.columsContentJson.$i=="3"}selected{/if}>{l s='Unit Price(Tax Excl)' mod='ba_prestashop_invoice'}</option>
										<option value="4" {if $invoiceTemplate.columsContentJson.$i=="4"}selected{/if}>{l s='Unit Price(Tax Incl) - Without Old Price' mod='ba_prestashop_invoice'}</option>
										<option value="11" {if $invoiceTemplate.columsContentJson.$i=="11"}selected{/if}>{l s='Unit Price(Tax Incl) - With Old Price' mod='ba_prestashop_invoice'}</option>
										<option value="5" {if $invoiceTemplate.columsContentJson.$i=="5"}selected{/if}>{l s='Product Tax' mod='ba_prestashop_invoice'}</option>
										<option value="6" {if $invoiceTemplate.columsContentJson.$i=="6"}selected{/if}>{l s='Discounted' mod='ba_prestashop_invoice'}</option>
										<option value="7" {if $invoiceTemplate.columsContentJson.$i=="7"}selected{/if}>{l s='Qty' mod='ba_prestashop_invoice'}</option>
										<option value="14" {if $invoiceTemplate.columsContentJson.$i=="14"}selected{/if}>{l s='Total(Tax Excl)' mod='ba_prestashop_invoice'}</option>
										<option value="8" {if $invoiceTemplate.columsContentJson.$i=="8"}selected{/if}>{l s='Total(Tax Incl)' mod='ba_prestashop_invoice'}</option>
										<option value="9" {if $invoiceTemplate.columsContentJson.$i=="9"}selected{/if}>{l s='Product Image' mod='ba_prestashop_invoice'}</option>
										<option value="10" {if $invoiceTemplate.columsContentJson.$i=="10"}selected{/if}>{l s='Tax Rate' mod='ba_prestashop_invoice'}</option>
										<option value="12" {if $invoiceTemplate.columsContentJson.$i=="12"}selected{/if}>{l s='Product Reference' mod='ba_prestashop_invoice'}</option>
										<option value="15" {if $invoiceTemplate.columsContentJson.$i=="15"}selected{/if}>{l s='Tax Name' mod='ba_prestashop_invoice'}</option>
										<option value="16" {if $invoiceTemplate.columsContentJson.$i=="16"}selected{/if}>{l s='Unit Price(Tax Excl) not Discount' mod='ba_prestashop_invoice'}</option>
										<option value="17" {if $invoiceTemplate.columsContentJson.$i=="17"}selected{/if}>{l s='Total(Tax Excl) not Discount' mod='ba_prestashop_invoice'}</option>
										<option value="18" {if $invoiceTemplate.columsContentJson.$i=="18"}selected{/if}>{l s='Product Warehouse Location' mod='ba_prestashop_invoice'}</option>
									</select>
								</div>
							</div>
							<div class='form-group col-sm-6'>
								<label class='col-sm-3 control-label' style='text-align:right;'>Color title column</label>
								<div class='col-sm-2'>
									<input type='text' class='color' name="colums_color[]" value=""/>
								</div>
							</div>
							<div class='form-group col-sm-6'>
								<label class='col-sm-3 control-label' style='text-align:right;'>Color background title column</label>
								<div class='col-sm-2'>
									<input type='text' class='color' name="colums_bgcolor[]" value=""/>
								</div>
							</div>
						</div>
						{/for}
					</div>
					<script>
						var sample_product;
						var init_table_colums=0;
						function addColumn(){
							var numberColumn = parseInt(jQuery("#numberColumnOfTableTemplaterPro").val());	
							if(numberColumn>init_table_colums){
								//console.log(numberColumn);
								for(var i=init_table_colums+1; i<=numberColumn; i++){
									sample_product=$("#sample_product_template").clone();
									sample_product.find(".product_template").addClass("product_template_"+i);
									sample_product.find(".header_text").text("Columns "+i);
									$("#product_list_columns").append(sample_product.html());	
								}
								jscolor.bind();
							}else{
								for(var i=numberColumn+1; i<=init_table_colums; i++){
									$(".product_template_"+i).remove();
								}
							}
							////////////
							init_table_colums=numberColumn;
						}
						
						//Open and close token
						
						
					</script>
				</div>
				
			</div>
			<div class="panel-footer">
				<button type="submit" value="1" id="module_form_submit_btn" name="submitBaCancel" class="btn btn-default pull-left">
					<i class="process-icon-cancel"></i> {l s='Cancel' mod='ba_prestashop_invoice'}
				</button>
				<button type="submit" value="1" id="module_form_submit_btn" name="submitBaSave" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> {l s='Save' mod='ba_prestashop_invoice'}
				</button>
				<button type="submit" value="1" name="submitBaSaveAndStay" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> {l s='Save And Stay' mod='ba_prestashop_invoice'}
				</button>
				<a id="preview_delivery" class="btn btn-default pull-right">
					<i class="process-icon-preview"></i> {l s='Preview' mod='ba_prestashop_invoice'}
				</a>
			</div>
		</form>
	</div>
</div>