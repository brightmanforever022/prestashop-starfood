<?php
/**
 * Store Commander
 *
 * @category administration
 * @author Store Commander - support@storecommander.com
 * @version 2015-09-15
 * @uses Prestashop modules
 * @since 2009
 * @copyright Copyright &copy; 2009-2015, Store Commander
 * @license commercial
 * All rights reserved! Copying, duplication strictly prohibited
 *
 * *****************************************
 * *           STORE COMMANDER             *
 * *   http://www.StoreCommander.com       *
 * *            V 2015-09-15               *
 * *****************************************
 *
 * Compatibility: PS version: 1.1 to 1.6.1
 *
 **/
$grids=array(
		'grid_light' => 		'id_order,id_customer,firstname,lastname,email,company,total_paid,total_product_quantity,payment,status,status_date,invoice_number,delivery_number,date_add,pdf',
		'grid_large' => 		'id_order,id_customer,firstname,lastname,email,company,id_carrier,shipping_number,id_lang,id_cart,id_currency,'.((SCI::getConfigurationValue("PS_RECYCLABLE_PACK")==1)?'recyclable,':'').''.((SCI::getConfigurationValue("PS_GIFT_WRAPPING")==1)?'gift,gift_message,':'').'total_discounts,total_paid,total_paid_real,total_products,total_shipping,total_wrapping,total_product_quantity,invoice_date,invoice_number,delivery_date,delivery_number,payment,status,status_date,date_add,date_upd',
		'grid_picking' => 	'id_order,id_order_detail,id_customer,product_id,location,product_reference,product_supplier_reference,supplier_name,product_ean13,product_name,product_quantity,actual_quantity_in_stock,total_product_quantity,status,status_date,date_add,pdf,email',
		'grid_delivery' => 	'id_order,id_customer,firstname,lastname,email,id_carrier,shipping_number,delivery_number,order_weight,del_company,del_lastname,del_firstname,del_address1,del_address2,del_postcode,del_city,del_id_country,del_id_state,del_other,del_phone,del_phone_mobile,date_add,pdf'
);

if (version_compare(_PS_VERSION_, '1.2.0.0', '>='))
{
	$grids=array(
			'grid_light' => 		'id_order,id_customer,firstname,lastname,email,company,total_paid,total_product_quantity,payment,status,status_date,invoice_number,delivery_number,date_add,pdf',
			'grid_large' => 		'id_order,id_customer,firstname,lastname,email,company,id_carrier,shipping_number,id_lang,id_cart,id_currency,'.((SCI::getConfigurationValue("PS_RECYCLABLE_PACK")==1)?'recyclable,':'').''.((SCI::getConfigurationValue("PS_GIFT_WRAPPING")==1)?'gift,gift_message,':'').'total_discounts,total_paid,total_paid_real,total_products,total_shipping,total_wrapping,total_product_quantity,invoice_date,invoice_number,delivery_date,delivery_number,valid,payment,status,status_date,date_add,date_upd',
			'grid_picking' => 	'id_order,id_order_detail,id_customer,product_id,location,product_reference,product_supplier_reference,supplier_name,product_ean13,product_name,product_quantity,actual_quantity_in_stock,total_product_quantity,status,status_date,date_add,pdf,email',
			'grid_delivery' => 	'id_order,id_customer,firstname,lastname,email,id_carrier,shipping_number,delivery_number,order_weight,del_company,del_lastname,del_firstname,del_address1,del_address2,del_postcode,del_city,del_id_country,del_id_state,del_other,del_phone,del_phone_mobile,date_add,pdf'
	);
}
if (version_compare(_PS_VERSION_, '1.3.0.0', '>='))
{
	$grids=array(
			'grid_light' => 		'id_order,id_customer,firstname,lastname,email,company,total_paid,total_product_quantity,payment,status,status_date,invoice_number,delivery_number,date_add,pdf',
			'grid_large' => 		'id_order,id_customer,firstname,lastname,email,company,id_carrier,shipping_number,id_lang,id_cart,id_currency,'.((SCI::getConfigurationValue("PS_RECYCLABLE_PACK")==1)?'recyclable,':'').''.((SCI::getConfigurationValue("PS_GIFT_WRAPPING")==1)?'gift,gift_message,':'').'total_discounts,total_paid,total_paid_real,total_products,total_products_wt,total_shipping,total_wrapping,total_product_quantity,invoice_date,invoice_number,delivery_date,delivery_number,valid,payment,status,status_date,date_add,date_upd',
			'grid_picking' => 	'id_order,id_order_detail,id_customer,product_id,location,product_reference,product_supplier_reference,supplier_name,product_ean13,product_name,product_quantity,actual_quantity_in_stock,total_product_quantity,status,status_date,date_add,pdf,email',
			'grid_delivery' => 	'id_order,id_customer,firstname,lastname,email,id_carrier,shipping_number,delivery_number,order_weight,del_company,del_lastname,del_firstname,del_address1,del_address2,del_postcode,del_city,del_id_country,del_id_state,del_other,del_phone,del_phone_mobile,date_add,pdf'
	);
}
if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
{
	$grids=array(
			'grid_light' => 		'id_order,id_customer,firstname,lastname,email,inv_vat_number,company,total_paid,total_product_quantity,payment,status,status_date,invoice_number,delivery_number,date_add,instock,pdf,msg',
			'grid_large' => 		'id_order,id_customer,firstname,lastname,email,inv_vat_number,company,id_carrier,shipping_number,id_lang,id_cart,id_currency,conversion_rate,'.((SCI::getConfigurationValue("PS_RECYCLABLE_PACK")==1)?'recyclable,':'').''.((SCI::getConfigurationValue("PS_GIFT_WRAPPING")==1)?'gift,gift_message,':'').'total_discounts,total_paid,total_paid_real,total_remaining_paid,total_products,total_products_wt,total_shipping,carrier_tax_rate,total_wrapping,total_product_quantity,invoice_date,invoice_number,delivery_date,delivery_number,valid,payment,status,status_date,date_add,date_upd',
			'grid_picking' => 	'id_order,id_order_detail,id_customer,product_id,location,product_reference,product_supplier_reference,supplier_name,product_ean13,product_upc,product_name,product_quantity,actual_quantity_in_stock,total_product_quantity,status,status_date,date_add,instock,pdf,msg,email',
			'grid_delivery' => 	'id_order,id_customer,firstname,lastname,email,inv_vat_number,id_carrier,shipping_number,delivery_number,order_weight,del_company,del_lastname,del_firstname,del_address1,del_address2,del_postcode,del_city,del_id_country,del_id_state,del_other,del_phone,del_phone_mobile,date_add,pdf,msg'
	);
}
if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
	$grids=array(
			'grid_light' => 		'id_order,'.(SCMS?'id_shop,':'').'reference,id_customer,firstname,lastname,email,inv_vat_number,company,total_paid,total_product_quantity,payment,status,status_date,invoice_number,delivery_number,date_add,instock,pdf,msg',
			'grid_large' => 		'id_order,'.(SCMS?'id_shop,':'').'reference,id_customer,firstname,lastname,email,inv_vat_number,company,id_carrier,shipping_number,id_lang,id_cart,id_currency,conversion_rate,'.((SCI::getConfigurationValue("PS_RECYCLABLE_PACK")==1)?'recyclable,':'').''.((SCI::getConfigurationValue("PS_GIFT_WRAPPING")==1)?'gift,gift_message,':'').'total_discounts_tax_incl,total_discounts_tax_excl,total_paid_tax_incl,total_paid_tax_excl,total_remaining_paid,total_products,total_products_wt,total_shipping_tax_incl,total_shipping_tax_excl,carrier_tax_rate,total_wrapping_tax_incl,total_wrapping_tax_excl,total_product_quantity,invoice_date,invoice_number,delivery_date,delivery_number,valid,payment,status,status_date,date_add,date_upd',
			'grid_picking' => 	'id_order,'.(SCMS?'id_shop,':'').'id_order_detail,id_customer,product_id,'.( SCAS ?'id_warehouse,':'').'location,product_reference,product_supplier_reference,supplier_name,product_ean13,product_upc,product_name,product_quantity,actual_quantity_in_stock,total_product_quantity,status,status_date,date_add,instock,pdf,msg,email',
			'grid_delivery' => 	'id_order,'.(SCMS?'id_shop,':'').'id_customer,firstname,lastname,email,inv_vat_number,id_carrier,shipping_number,delivery_number,order_weight,del_company,del_firstname,del_lastname,del_address1,del_address2,del_postcode,del_city,del_id_country,del_id_state,del_other,del_phone,del_phone_mobile,date_add,pdf,msg'
	);
	
	if(SCI::getConfigurationValue("SC_DELIVERYDATE_INSTALLED")=="1")
	{
		$grids['grid_delivery'] = 'id_order,'.(SCMS?'id_shop,':'').'id_customer,firstname,lastname,email,inv_vat_number,id_carrier,shipping_number,delivery_number,delivery_date_standard,delivery_date_limit,order_weight,del_company,del_firstname,del_lastname,del_address1,del_address2,del_postcode,del_city,del_id_country,del_id_state,del_other,del_phone,del_phone_mobile,date_add,pdf,msg';
	}
}
if (version_compare(_PS_VERSION_, '1.6.1.1', '>='))
{
	$grids['grid_picking'] = 'id_order,id_order_detail,id_customer,product_id,location,product_reference,product_supplier_reference,supplier_name,product_ean13,product_upc,product_name,product_quantity,actual_quantity_in_stock,total_product_quantity,original_wholesale_price,status,status_date,date_add,instock,pdf,msg,email';
}


if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
{
    $grids['grid_picking'] = 'id_order,id_order_detail,id_customer,product_id,location,product_reference,product_supplier_reference,supplier_name,product_ean13,product_upc,product_isbn,product_name,product_quantity,actual_quantity_in_stock,total_product_quantity,original_wholesale_price,status,status_date,date_add,instock,pdf,msg,email';
}