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
$colSettings['id_product']=array('text' => _l('ID'),'width'=>40,'align'=>'left','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['id_shop']=array('text' => _l('Shop'),'width'=>100,'align'=>'left','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter', 'options'=>$shops);
$colSettings['reference']=array('text' => _l('Ref'),'width'=>80,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['name']=array('text' => _l('Name'),'width'=>200,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['quantity']=array('text' => _l('Stock available'),'width'=>60,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['advanced_stock_management']=array('text' => _l('Advanced Stock Mgmt.'),'width'=>100,'align'=>'left','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(1=>_l('Disabled'),2=>_l('Enabled'),3=>_l('Enabled + Manual Mgmt')));
$colSettings['link_rewrite']=array('text' => _l('link_rewrite'),'width'=>200,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['active']=array('text' => _l('Active'),'width'=>45,'align'=>'center','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
$colSettings['visibility']=array('text' => _l('Visibility'),'width'=>65,'align'=>'center','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#select_filter','options'=>array('both'=>_l('Both'),'catalog'=>_l('Catalog'),'search'=>_l('Search'),'none'=>_l('None')));
$colSettings['on_sale']=array('text' => _l('On sale'),'width'=>45,'align'=>'center','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
$colSettings['online_only']=array('text' => _l('Online only'),'width'=>45,'align'=>'center','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
$colSettings['minimal_quantity']=array('text' => _l('Minimum quantity'),'width'=>60,'align'=>'right','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['ecotax']=array('text' => _l('EcoTax'),'width'=>50,'align'=>'right','type'=>'edn','sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00');
$colSettings['id_tax_rules_group']=array('text' => _l('Tax rule'),'width'=>65,'align'=>'left','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#select_filter_strict','options'=>$arrTax);
$colSettings['price']=array('text' => _l('Price excl. Tax'),'width'=>65,'align'=>'right','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00');
$colSettings['wholesale_price']=array('text' => _l('Wholesale price'),'width'=>55,'align'=>'right','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['unity']=array('text' => _l('Unit'),'width'=>50,'align'=>'right','type'=>'ed','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['unit_price_ratio']=array('text' => _l('Unit price'),'width'=>65,'align'=>'right','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['additional_shipping_cost']=array('text' => _l('Add. shipping cost'),'width'=>65,'align'=>'right','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['show_price']=array('text' => _l('Show price'),'width'=>45,'align'=>'center','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
$colSettings['available_for_order']=array('text' => _l('Available for order'),'width'=>45,'align'=>'center','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
$colSettings['available_date']=array('text' => _l('Available date'),'width'=>80,'align'=>'right','type'=>'dhxCalendarA','sort'=>'date','color'=>'','filter'=>'#select_filter','format'=>'%Y-%m-%d');
$colSettings['condition']=array('text' => _l('Condition'),'width'=>65,'align'=>'center','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array('new'=>_l('New'),'used'=>_l('Used'),'refurbished'=>_l('Refurbished')));
if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
    $colSettings['show_condition']=array('text' => _l('Show condition'),'width'=>45,'align'=>'center','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
$colSettings['available_now']=array('text' => _l('Msg available now'),'width'=>150,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['available_later']=array('text' => _l('Msg available later'),'width'=>150,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['price_inc_tax']=array('text' => _l('Price incl. Tax'),'width'=>65,'align'=>'right','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00');
$colSettings['margin']=array('text' => _l('Margin/Coef'),'width'=>45,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00');
//$colSettings['margin']=array('text' => _l('Margin/Coef'),'width'=>45,'align'=>'right','type'=>'ron'.$marginMatrix[_s('CAT_PROD_GRID_MARGIN_OPERATION')],'sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00');
$colSettings['supplier_reference']=array('text' => _l('Supplier Ref.'),'width'=>80,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');

// GE
$colSettings['ean13']=array('text' => _l('EAN13'),'width'=>100,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['upc']=array('text' => _l('UPC'),'width'=>100,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['location']=array('text' => _l('Location').(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?' '._l('(old)'):''),'width'=>100,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');

$colSettings['out_of_stock']=array('text' => _l('If out of stock'),'width'=>100,'align'=>'left','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('Deny orders'),1=>_l('Allow orders'),2=>_l('Default(Pref)')));