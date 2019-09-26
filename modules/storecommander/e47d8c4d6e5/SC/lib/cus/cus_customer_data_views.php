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
	 'grid_light' => 	'id_customer,id_gender,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,":"").'firstname,lastname,email,active,newsletter,optin,cart_lang,date_add,date_connection',
	 'grid_large' => 	'id_customer,id_gender,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,siret,ape,":"").'firstname,lastname,email,birthday,active,newsletter,optin,cart_lang,date_add,date_connection,last_delivery_address',
	 'grid_address' => 	'id_address,id_customer,firstname,lastname,email,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,":"").'address1,address2,postcode,city,id_state,id_country,phone,phone_mobile,invoice,delivery',
	 'grid_convert' => 	'id_customer,id_gender,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,":"").'firstname,lastname,email,discount_codes,last_date_order'
);

if (version_compare(_PS_VERSION_, '1.2.0.0', '>='))
{
	$grids=array(
		'grid_light' => 	'id_customer,id_gender,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,":"").'firstname,lastname,email,active,newsletter,optin,cart_lang,date_add,date_connection',
		'grid_large' => 	'id_customer,id_gender,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,siret,ape,":"").'firstname,lastname,email,birthday,active,newsletter,optin,cart_lang,date_add,date_connection,valid_orders,last_delivery_address',
		'grid_address' => 	'id_address,id_customer,firstname,lastname,email,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,":"").'address1,address2,postcode,city,id_state,id_country,phone,phone_mobile,invoice,delivery',
		'grid_convert' => 	'id_customer,id_gender,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,":"").'firstname,lastname,email,discount_codes,valid_orders,total_valid_orders,last_date_order,nb_cart_product,total_cart_product'
	);
}

if (version_compare(_PS_VERSION_, '1.3.0.0', '>='))
{
	$grids=array(
		'grid_light' => 	'id_customer,id_gender,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,":"").'firstname,lastname,email,active,newsletter,optin,cart_lang,date_add,date_connection',
		'grid_large' => 	'id_customer,id_gender,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,siret,ape,":"").'firstname,lastname,email,birthday,active,newsletter,optin,cart_lang,date_add,date_connection,id_default_group,groups,valid_orders,last_delivery_address',
		'grid_address' => 	'id_address,id_customer,firstname,lastname,email,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,":"").'address1,address2,postcode,city,id_state,id_country,phone,phone_mobile,invoice,delivery',
		'grid_convert' => 	'id_customer,id_gender,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,":"").'firstname,lastname,email,id_default_group,groups,discount_codes,valid_orders,total_valid_orders,last_date_order,nb_cart_product,total_cart_product'
	);
}

if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
	$grids=array(
			'grid_light' => 	'id_customer,'.(SCMS?'id_shop,':'').'id_gender,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,":"").'firstname,lastname,email,active,newsletter,optin,cart_lang,date_add,date_connection',
			'grid_large' => 	'id_customer,'.(SCMS?'id_shop,':'').'id_gender,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,siret,ape,":"").'firstname,lastname,email,birthday,active,newsletter,optin,cart_lang,date_add,date_connection,id_default_group,groups,note,valid_orders,last_delivery_address',
			'grid_address' => 	'id_address,'.(SCMS?'id_shop,':'').'id_customer,firstname,lastname,email,vat_number,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,":"").'address1,address2,postcode,city,id_state,id_country,phone,phone_mobile,invoice,delivery',
			'grid_convert' => 	'id_customer,'.(SCMS?'id_shop,':'').'id_gender,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,":"").'firstname,lastname,email,id_default_group,groups,discount_codes,valid_orders,total_valid_orders,last_date_order,nb_cart_product,total_cart_product'
	);
}

if (version_compare(_PS_VERSION_, '1.5.4.0', '>='))
{
	$grids=array(
			'grid_light' => 	'id_customer,'.(SCMS?'id_shop,':'').'id_gender,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,":"").'firstname,lastname,email,active,newsletter,optin,id_lang,date_add,date_connection',
			'grid_large' => 	'id_customer,'.(SCMS?'id_shop,':'').'id_gender,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,siret,ape,":"").'firstname,lastname,email,birthday,active,newsletter,optin,id_lang,date_add,date_connection,id_default_group,groups,note,valid_orders,last_delivery_address',
			'grid_address' => 	'id_address,'.(SCMS?'id_shop,':'').'id_customer,firstname,lastname,email,vat_number,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,":"").'address1,address2,postcode,city,id_state,id_country,phone,phone_mobile,invoice,delivery',
			'grid_convert' => 	'id_customer,'.(SCMS?'id_shop,':'').'id_gender,'.(_s('CUS_USE_COMPANY_FIELDS')?"company,":"").'firstname,lastname,email,id_default_group,groups,discount_codes,valid_orders,total_valid_orders,last_date_order,nb_cart_product,total_cart_product'
	);
}