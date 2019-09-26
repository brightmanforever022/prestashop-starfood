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

$colSettings['id_order']=array('text' => _l('id order'),'width'=>45,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['id_shop']=array('text' => _l('id shop'),'width'=>45,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['id_order_detail']=array('text' => _l('id order detail'),'width'=>45,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['id_customer']=array('text' => _l('id customer'),'width'=>45,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['id_carrier']=array('text' => _l('Carrier'),'width'=>70,'align'=>'left','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#select_filter_strict','options'=>$arrCarrier);
$colSettings['id_lang']=array('text' => _l('Cart language'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['id_cart']=array('text' => _l('id cart'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');

$colSettings['id_currency']=array('text' => _l('Currency'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['conversion_rate']=array('text' => _l('Conversion rate'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'str','color'=>'','filter'=>'#text_filter');

$colSettings['reference']=array('text' => _l('Reference'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['firstname']=array('text' => _l('Firstname'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['lastname']=array('text' => _l('Lastname'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['email']=array('text' => _l('Email'),'width'=>100,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['company']=array('text' => _l('Company'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['default_group']=array('text' => _l('Group'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#select_filter');

$colSettings['total_discounts']=array('text' => _l('Total discounts'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');
$colSettings['total_discounts_tax_incl']=array('text' => _l('Total discounts inc. tax'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');
$colSettings['total_discounts_tax_excl']=array('text' => _l('Total discounts exc. tax'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');

$colSettings['total_paid_tax_incl']=array('text' => _l('Total paid inc. tax'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');
$colSettings['total_paid_tax_excl']=array('text' => _l('Total paid exc. tax'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');
$colSettings['total_paid_real']=array('text' => _l('Total paid (module)'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');
$colSettings['total_paid']=array('text' => _l('Total paid'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');
$colSettings['total_remaining_paid']=array('text' => _l('Total left to pay'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');

$colSettings['total_products']=array('text' => _l('Total products'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');
$colSettings['total_products_wt']=array('text' => _l('Total products inc. tax'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');

$colSettings['total_shipping']=array('text' => _l('Total shipping'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');
$colSettings['total_shipping_tax_incl']=array('text' => _l('Total shipping inc. tax'),'width'=>70,'align'=>'right','type'=>'edn','sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');
$colSettings['total_shipping_tax_excl']=array('text' => _l('Total shipping exc. tax'),'width'=>70,'align'=>'right','type'=>'edn','sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');

$colSettings['carrier_tax_rate']=array('text' => _l('Carrier tax rate'),'width'=>70,'align'=>'right','type'=>'edn','sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');

$colSettings['total_wrapping']=array('text' => _l('Total wrapping'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');
$colSettings['total_wrapping_tax_incl']=array('text' => _l('Total wrapping inc. tax'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');
$colSettings['total_wrapping_tax_excl']=array('text' => _l('Total wrapping exc. tax'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');

$colSettings['valid']=array('text' => _l('Valid'),'width'=>45,'align'=>'center','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
$colSettings['recyclable']=array('text' => _l('Recyclable'),'width'=>45,'align'=>'center','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
$colSettings['gift']=array('text' => _l('Gift'),'width'=>45,'align'=>'center','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
$colSettings['gift_message']=array('text' => _l('Gift message'),'width'=>100,'align'=>'center','type'=>'txt','sort'=>'str','color'=>'','filter'=>'#text_filter');


$colSettings['payment']=array('text' => _l('Payment'),'width'=>80,'align'=>'left','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#select_filter_strict','options'=>$arrPayments);
if ($view=='grid_picking' || (is_array($cols) && sc_in_array("id_order_detail", $cols,"ordDataFields_cols")))
	$colSettings['payment']['type']='ro';
$colSettings['status']=array('text' => _l('Order status'),'width'=>200,'align'=>'left','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#select_filter_strict','options'=>$arrStatus);
if ($view=='grid_picking' || (is_array($cols) && sc_in_array("id_order_detail", $cols,"ordDataFields_cols")) || _r("ACT_ORD_UPDATE_STATUS")!="1")
	$colSettings['status']['type']='ro';
$colSettings['status_date']=array('text' => _l('Status update'),'width'=>110,'align'=>'left','type'=>'ro'/*'edtxt'*/,'sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['invoice_number']=array('text' => _l('Invoice No'),'width'=>45,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['invoice_date']=array('text' => _l('Invoice date'),'width'=>110,'align'=>'left','type'=>'ro'/*'edtxt'*/,'sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['delivery_number']=array('text' => _l('Delivery No'),'width'=>45,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['delivery_date']=array('text' => _l('Delivery slip date'),'width'=>110,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['date_add']=array('text' => _l('Creation date'),'width'=>110,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['date_upd']=array('text' => _l('Modified date'),'width'=>110,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['instock']=array('text' => _l('In stock'),'width'=>45,'align'=>'center','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#select_filter');
$colSettings['pdf']=array('text' => _l('PDF'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>' ');
$colSettings['msg']=array('text' => _l('Message'),'width'=>45,'align'=>'right','type'=>'ro','sort'=>'str','color'=>'','filter'=>' ');
$colSettings['shipping_number']=array('text' => _l('Shipping n°'),'width'=>90,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');

$colSettings['total_product_quantity']=array('text' => _l('Total number of products'),'width'=>80,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['order_weight']=array('text' => _l('Order weight'),'width'=>90,'align'=>'right','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter', 'onlyforgrids');

$colSettings['del_company']=array('text' => _l('Del.').' '._l('Company'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['del_firstname']=array('text' => _l('Del.').' '._l('Firstname'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['del_lastname']=array('text' => _l('Del.').' '._l('Lastname'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['del_address1']=array('text' => _l('Del.').' '._l('Address').' 1','width'=>70,'align'=>'left','type'=>'ed','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['del_address2']=array('text' => _l('Del.').' '._l('Address').' 2','width'=>70,'align'=>'left','type'=>'ed','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['del_postcode']=array('text' => _l('Del.').' '._l('Postcode'),'width'=>70,'align'=>'left','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#text_filter');
$colSettings['del_city']=array('text' => _l('Del.').' '._l('City'),'width'=>70,'align'=>'left','type'=>'ed','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['del_id_country']=array('text' => _l('Del.').' '._l('Country'),'width'=>70,'align'=>'left','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#text_filter','options'=>$orderCountry);
$colSettings['del_id_state']=array('text' => _l('Del.').' '._l('State'),'width'=>70,'align'=>'left','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#text_filter','options'=>$orderState);
$colSettings['del_other']=array('text' => _l('Del.').' '._l('Other'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['del_phone']=array('text' => _l('Del.').' '._l('Phone'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['del_phone_mobile']=array('text' => _l('Del.').' '._l('Mobile'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');

$colSettings['inv_company']=array('text' => _l('Inv.').' '._l('Company'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['inv_firstname']=array('text' => _l('Inv.').' '._l('Firstname'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['inv_lastname']=array('text' => _l('Inv.').' '._l('Lastname'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['inv_address1']=array('text' => _l('Inv.').' '._l('Address').' 1','width'=>70,'align'=>'left','type'=>'ed','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['inv_address2']=array('text' => _l('Inv.').' '._l('Address').' 2','width'=>70,'align'=>'left','type'=>'ed','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['inv_postcode']=array('text' => _l('Inv.').' '._l('Postcode'),'width'=>70,'align'=>'left','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#text_filter');
$colSettings['inv_city']=array('text' => _l('Inv.').' '._l('City'),'width'=>70,'align'=>'left','type'=>'ed','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['inv_id_country']=array('text' => _l('Inv.').' '._l('Country'),'width'=>70,'align'=>'left','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#text_filter','options'=>$orderCountry);
$colSettings['inv_id_state']=array('text' => _l('Inv.').' '._l('State'),'width'=>70,'align'=>'left','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#text_filter','options'=>$orderState);
$colSettings['inv_other']=array('text' => _l('Inv.').' '._l('Other'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['inv_phone']=array('text' => _l('Inv.').' '._l('Phone'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['inv_phone_mobile']=array('text' => _l('Inv.').' '._l('Mobile'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['inv_vat_number']=array('text' => _l('Vat number'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');

// SUPP
$colSettings['wholesale_price']=array('text' => _l('Wholesale price'),'width'=>55,'align'=>'right','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['product_price']=array('text' => _l('Product price excl. tax'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');

if(SCI::getConfigurationValue("SC_DELIVERYDATE_INSTALLED")=="1")
{
	$colSettings['delivery_info']=array('text' => _l('Delivery date'),'width'=>100,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');	
	$colSettings['delivery_date_standard']=array('text' => _l('Delivery date - Standard'),'width'=>90,'align'=>'left','type'=>'ro'/*'edtxt'*/,'sort'=>'str','color'=>'','filter'=>'#numeric_filter');
	$colSettings['delivery_date_limit']=array('text' => _l('Delivery date - Limit'),'width'=>90,'align'=>'left','type'=>'ro'/*'edtxt'*/,'sort'=>'str','color'=>'','filter'=>'#numeric_filter');
}

$colSettings['quantity_physical']=array('text' => _l('Physical stock'),'width'=>60,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['quantity_usable']=array('text' => _l('Available stock'),'width'=>60,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['quantity_real']=array('text' => _l('Live stock'),'width'=>60,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');

$colSettings['product_quantity_in_stock']=array('text' => _l('Quantity in stock at the time of the order'),'width'=>50,'align'=>'right','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter', 'onlyforgrids'=>array('grid_picking'));

$colSettings['customer_note']=array('text' => _l('Private note'),'width'=>150,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');

$colSettings['total_assets']=array('text' => _l('Total credit notes'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');

$colSettings['actual_product_price_wt']=array('text' => _l('Actual product price tax excl'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');
$colSettings['actual_product_price_it']=array('text' => _l('Actual product price tax incl'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');
$colSettings['actual_product_price_reduction_wt']=array('text' => _l('Actual product price tax excl after reduction'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');
$colSettings['actual_product_price_reduction_it']=array('text' => _l('Actual product price tax incl after reduction'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');

$colSettings['default_category']=array('text' => _l('Default category'),'width'=>150,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');

$colSettings['total_wholesale_price']=array('text' => _l('Total wholesale price'),'width'=>55,'align'=>'right','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#numeric_filter');

// PICKING
$colSettings['product_id']=array('text' => _l('id product'),'width'=>45,'align'=>'right','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter', 'onlyforgrids'=>array('grid_picking'));
$colSettings['product_reference']=array('text' => _l('Product ref'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter', 'onlyforgrids'=>array('grid_picking'));
$colSettings['product_supplier_reference']=array('text' => _l('Supplier reference'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter', 'onlyforgrids'=>array('grid_picking'));
$colSettings['supplier_name']=array('text' => _l('Supplier'),'width'=>100,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#select_filter', 'onlyforgrids'=>array('grid_picking'));
$colSettings['product_name']=array('text' => _l('Product name'),'width'=>200,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter', 'onlyforgrids'=>array('grid_picking'));
$colSettings['product_quantity']=array('text' => _l('Qty'),'width'=>45,'align'=>'right','type'=>'ron','sort'=>'int','color'=>'','filter'=>'#numeric_filter', 'onlyforgrids'=>array('grid_picking'));
$colSettings['product_ean13']=array('text' => _l('EAN13'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter', 'onlyforgrids'=>array('grid_picking'));
$colSettings['product_upc']=array('text' => _l('UPC'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter', 'onlyforgrids'=>array('grid_picking'));
$colSettings['location']=array('text' => _l('Location'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter', 'onlyforgrids'=>array('grid_picking'));
$colSettings['id_warehouse']=array('text' => _l('Warehouse'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter', 'onlyforgrids'=>array('grid_picking'));
$colSettings['actual_quantity_in_stock']=array('text' => _l('Current stock'),'width'=>50,'align'=>'right','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter', 'onlyforgrids'=>array('grid_picking'));
$colSettings['category_name']=array('text' => _l('Category'),'width'=>100,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#select_filter', 'onlyforgrids'=>array('grid_picking'));
if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	$colSettings['location_old']=array('text' => _l('Location').' '._l('(old)'),'width'=>100,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
if(version_compare(_PS_VERSION_, '1.6.1.1', '>='))
	$colSettings['original_wholesale_price']=array('text' => _l('Original wholesale price'),'width'=>70,'align'=>'right','type'=>'ro'/*'edn'*/,'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00','footer'=>'#stat_total');
if(version_compare(_PS_VERSION_, '1.7.0.0', '>='))
    $colSettings['product_isbn']=array('text' => _l('ISBN'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter', 'onlyforgrids'=>array('grid_picking'));
