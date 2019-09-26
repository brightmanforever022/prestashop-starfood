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
$colSettings['id']=array('text' => _l('Supplier'),'width'=>200,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['present']=array('text' => _l('Present'),'width'=>80,'align'=>'center','type'=>'ch','sort'=>'int','color'=>'','filter'=>'#select_filter');
$colSettings['default']=array('text' => _l('Default'),'width'=>50,'align'=>'center','type'=>'ra','sort'=>'str','color'=>'','filter'=>'#select_filter');

$colSettings['product_supplier_reference']=array('text' => _l('Supplier reference'),'width'=>100,'align'=>'left','type'=>'ed','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['product_supplier_price_te']=array('text' => _l('Wholesale price'),'width'=>100,'align'=>'right','type'=>'ed','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['id_currency']=array('text' => _l('Currency'),'width'=>80,'align'=>'right','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter', 'options'=>$currencies);

// GE
/*$colSettings['image']=array('text' => _l('Image'),'width'=>60,'align'=>'center','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['supplier_reference']=array('text' => _l('Supplier Ref.'),'width'=>80,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['ean13']=array('text' => _l('EAN13'),'width'=>100,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['upc']=array('text' => _l('UPC'),'width'=>100,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['active']=array('text' => _l('Active'),'width'=>45,'align'=>'center','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
$colSettings['price_exl_tax']=array('text' => _l('Price excl. Tax'),'width'=>65,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00');
$colSettings['price_inc_tax']=array('text' => _l('Price incl. Tax'),'width'=>65,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter','format'=>'0.00');
$colSettings['id_manufacturer']=array('text' => _l('Manufacturer'),'width'=>100,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#select_filter');
$colSettings['id_supplier']=array('text' => _l('Supplier'),'width'=>100,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#select_filter');*/
