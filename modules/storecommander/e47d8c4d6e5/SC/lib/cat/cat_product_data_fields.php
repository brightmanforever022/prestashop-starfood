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

global $cat_product_data_fields_condition;

$colSettings['id']=array('text' => _l('ID'),'width'=>40,'align'=>'left','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['name']=array('text' => _l('Name'),'width'=>200,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['position']=array('text' => _l('Pos.'),'width'=>35,'align'=>'right','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#text_filter');
$colSettings['reference']=array('text' => _l('Ref'),'width'=>80,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['quantity']=array('text' => _l('Stock available'),'width'=>60,'align'=>'right','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['minimal_quantity']=array('text' => _l('Minimum quantity'),'width'=>60,'align'=>'right','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['quantityupdate']=array('text' => _l('Stock available +/-'),'width'=>60,'align'=>'right','type'=>'ed','sort'=>'na','color'=>'#EFFAFF','filter'=>'#numeric_filter');
$colSettings['wholesale_price']=array('text' => _l('Wholesale price'),'width'=>55,'align'=>'right','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['price']=array('text' => _l('Price excl. Tax'),'width'=>65,'align'=>'right','type'=>'edn','sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00');
$colSettings['additional_shipping_cost']=array('text' => _l('Add. shipping cost'),'width'=>65,'align'=>'right','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['price_inc_tax']=array('text' => _l('Price incl. Tax'),'width'=>65,'align'=>'right','type'=>'edn','sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00');
$colSettings['unity']=array('text' => _l('Unit'),'width'=>50,'align'=>'right','type'=>'ed','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['unit_price_ratio']=array('text' => _l('Unit price'),'width'=>65,'align'=>'right','type'=>'edn','sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00');
$colSettings['unit_price_inc_tax']=array('text' => _l('Unit price tax incl.'),'width'=>65,'align'=>'right','type'=>'edn','sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00');
$colSettings['discountprice']=array('text' => _l('Discount price'),'width'=>150,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['ecotax']=array('text' => _l('EcoTax'),'width'=>50,'align'=>'right','type'=>'edn','sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00');
$colSettings['weight']=array('text' => _l('Weight'),'width'=>65,'align'=>'right','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['width']=array('text' => _l('Width'),'width'=>65,'align'=>'right','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['height']=array('text' => _l('Height'),'width'=>65,'align'=>'right','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['depth']=array('text' => _l('Depth'),'width'=>65,'align'=>'right','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['supplier_reference']=array('text' => _l('Supplier Ref.'),'width'=>80,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['id_manufacturer']=array('text' => _l('Manufacturer'),'width'=>100,'align'=>'left','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#select_filter_strict','options'=>$arrManufacturers);
$colSettings['id_supplier']=array('text' => _l('Supplier'),'width'=>100,'align'=>'left','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#select_filter_strict','options'=>$arrSuppliers);
$colSettings['id_tax_rules_group']=array('text' => _l('Tax rule'),'width'=>65,'align'=>'left','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#select_filter','options'=>$arrTax);
$colSettings['ean13']=array('text' => _l('EAN13'),'width'=>100,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['upc']=array('text' => _l('UPC'),'width'=>100,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
    $colSettings['isbn']=array('text' => _l('ISBN'),'width'=>100,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['location']=array('text' => _l('Location').(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?' '._l('(old)'):''),'width'=>100,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['out_of_stock']=array('text' => _l('If out of stock'),'width'=>100,'align'=>'left','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('Deny orders'),1=>_l('Allow orders'),2=>_l('Default(Pref)')));
$colSettings['available_now']=array('text' => _l('Msg available now'),'width'=>100,'align'=>'left','type'=>'co','sort'=>'str','color'=>'','filter'=>'#select_filter','options'=>$arrMsgAvailableNow);
$colSettings['available_later']=array('text' => _l('Msg available later'),'width'=>100,'align'=>'left','type'=>'co','sort'=>'str','color'=>'','filter'=>'#select_filter','options'=>$arrMsgAvailableLater);
$colSettings['reduction_price']=array('text' => _l('Reduction amount'),'width'=>60,'align'=>'right','type'=>'co','sort'=>'int','color'=>'','filter'=>'#numeric_filter','options'=>$arrReductionPrice);
$colSettings['price_with_reduction']=array('text' => _l('Price incl reduction'),'width'=>60,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['reduction_percent']=array('text' => _l('Reduction %'),'width'=>60,'align'=>'right','type'=>'co','sort'=>'int','color'=>'','filter'=>'#numeric_filter','options'=>$arrReductionPercent);
$colSettings['price_with_reduction_percent']=array('text' => _l('Price incl % reduction'),'width'=>60,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['reduction_from']=array('text' => _l('Discount starts'),'width'=>80,'align'=>'right','type'=>'dhxCalendarA','sort'=>'date','color'=>'','filter'=>'#select_filter');
$colSettings['reduction_to']=array('text' => _l('Discount ends'),'width'=>80,'align'=>'right','type'=>'dhxCalendarA','sort'=>'date','color'=>'','filter'=>'#select_filter');

$colSettings['margin_wt_amount_after_reduction']=array('text' => _l('Margin amount after reduction'),'width'=>60,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['margin_wt_percent_after_reduction']=array('text' => _l('Margin % after reduction'),'width'=>60,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['margin_after_reduction']=array('text' => _l('Margin after reduction'),'width'=>60,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['price_wt_with_reduction']=array('text' => _l('Price tax excl after reduction'),'width'=>60,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['price_it_with_reduction']=array('text' => _l('Price tax incl after reduction'),'width'=>60,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
if(version_compare(_PS_VERSION_, '1.6.0.11', '>=')) {
	$colSettings['reduction_tax']=array('text' => _l('Reduction tax'),'width'=>50,'align'=>'right','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('Excl. tax'),1=>_l('Incl. tax')));
}

if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
	$colSettings['id_tax']=array('text' => _l('Tax'),'width'=>50,'align'=>'right','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>$arrTax);


/*
 if (version_compare(_PS_VERSION_, '1.3.0.4', '>=')) // DATE => DATETIME field format
{
$colSettings['reduction_from']=array('text' => _l('Discount starts'),'width'=>120,'align'=>'right','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#select_filter');
$colSettings['reduction_to']=array('text' => _l('Discount ends'),'width'=>120,'align'=>'right','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#select_filter');
}	*/
$colSettings['on_sale']=array('text' => _l('On sale'),'width'=>45,'align'=>'center','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));

if (_r('ACT_CAT_ENABLE_PRODUCTS')) {
	$colSettings['active']=array('text' => _l('Active'),'width'=>45,'align'=>'center','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
}else{
	$colSettings['active']=array('text' => _l('Active'),'width'=>45,'align'=>'center','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
}

$colSettings['available_for_order']=array('text' => _l('Available for order'),'width'=>45,'align'=>'center','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
$colSettings['show_price']=array('text' => _l('Show price'),'width'=>45,'align'=>'center','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
$colSettings['online_only']=array('text' => _l('Online only'),'width'=>45,'align'=>'center','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
$colSettings['condition']=array('text' => _l('Condition'),'width'=>65,'align'=>'center','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>( isset($cat_product_data_fields_condition) ? $cat_product_data_fields_condition : array('new'=>_l('New'),'used'=>_l('Used'),'refurbished'=>_l('Refurbished'))));
if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
    $colSettings['show_condition']=array('text' => _l('Show condition'),'width'=>45,'align'=>'center','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
$colSettings['link_rewrite']=array('text' => _l('link_rewrite'),'width'=>200,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['meta_title']=array('text' => _l('meta_title'),'width'=>200,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['meta_description']=array('text' => _l('meta_description'),'width'=>200,'align'=>'left','type'=>'txttxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['meta_keywords']=array('text' => _l('meta_keywords'),'width'=>200,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['date_add']=array('text' => _l('Creation date'),'width'=>65,'align'=>'left','type'=>'dhxCalendarA','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['date_upd']=array('text' => _l('Modified date'),'width'=>110,'align'=>'right','type'=>'dhxCalendarA','sort'=>'str','color'=>'','filter'=>'#text_filter');
if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
	$colSettings['id_color_default']=array('text' => _l('Default color group'),'width'=>100,'align'=>'left','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#select_filter','options'=>$arrColorGroups);
$colSettings['margin']=array('text' => _l('Margin/Coef'),'width'=>45,'align'=>'right','type'=>'ron','sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00');
//$colSettings['margin']=array('text' => _l('Margin/Coef'),'width'=>45,'align'=>'right','type'=>'ron'.$marginMatrix[_s('CAT_PROD_GRID_MARGIN_OPERATION')],'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00');
$colSettings['description_short']=array('text' => _l('Short description'),'width'=>300,'align'=>'left','type'=>'txt','sort'=>'na','color'=>'','filter'=>'#text_filter');
$colSettings['description']=array('text' => _l('Description'),'width'=>300,'align'=>'left','type'=>'txt','sort'=>'na','color'=>'','filter'=>'#text_filter');
$colSettings['image']=array('text' => _l('Image'),'width'=>60,'align'=>'center','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['combinations']=array('text' => _l('Combinations (copy/paste)'),'width'=>100,'align'=>'left','type'=>'coro','sort'=>'na','color'=>'','filter'=>'#numeric_filter');
$colSettings['available_date']=array('text' => _l('Available date'),'width'=>80,'align'=>'right','type'=>'dhxCalendarA','sort'=>'date','color'=>'','filter'=>'#select_filter','format'=>'%Y-%m-%d');
$colSettings['visibility']=array('text' => _l('Visibility'),'width'=>65,'align'=>'center','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#select_filter','options'=>array('both'=>_l('Both'),'catalog'=>_l('Catalog'),'search'=>_l('Search'),'none'=>_l('None')));
//	$colSettings['tree']=array('text' => '','width'=>20,'align'=>'right','type'=>'tree','sort'=>'na','color'=>'','filter'=>'#numeric_filter');
$colSettings['id_product_redirected']=array('text' => _l('id_product_redirected'),'width'=>65,'align'=>'left','type'=>'edtxt','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['redirect_type']=array('text' => _l('Redirect'),'width'=>120,'align'=>'left','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array('-'=>'-',404=>_l('No redirect (404)'),301=>_l('Redirect permanently (301)'),302=>_l('Redirect temporarily (302)')));

$colSettings['advanced_stock_management']=array('text' => _l('Advanced Stock Mgmt.'),'width'=>100,'align'=>'left','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(1=>_l('Disabled'),2=>_l('Enabled'),3=>_l('Enabled + Manual Mgmt')));
$colSettings['quantity_physical']=array('text' => _l('Physical stock'),'width'=>60,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['quantity_usable']=array('text' => _l('Available stock'),'width'=>60,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['quantity_real']=array('text' => _l('Live stock'),'width'=>60,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');

$colSettings['last_order']=array('text' => _l('Last order'),'width'=>140,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');

$colSettings['is_virtual']=array('text' => _l('Is virtual'),'width'=>45,'align'=>'center','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));

$w_name = '';
if(SCAS)
{
	$id_w = SCI::getSelectedWarehouse();
	if(!empty($id_w))
	{
		$w = new Warehouse((int)$id_w);
		$w_name = ' '._l('('.$w->name.')');
	}
}
if(empty($w_name))
	$w_name = ' (warehouse)';
$colSettings['location_warehouse']=array('text' => _l('Location').$w_name,'width'=>100,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');

if(SC_UkooProductCompat_ACTIVE) {
    $colSettings['nb_compatibilities']=array('text' => _l('Nb. compat. associated'),'width'=>40,'align'=>'left','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
    $colSettings['compatibilities']=array('text' => _l('Compatibilities (copy/paste)'),'width'=>100,'align'=>'left','type'=>'coro','sort'=>'na','color'=>'','filter'=>'#text_filter');
}
