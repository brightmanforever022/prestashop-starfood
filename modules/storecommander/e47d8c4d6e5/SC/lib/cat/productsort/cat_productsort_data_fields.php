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
$colSettings['id_product']=array('text' => _l('id_product'),'width'=>40,'align'=>'right','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['position']=array('text' => _l('Pos.'),'width'=>40,'align'=>'right','type'=>'edtxt','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['reference']=array('text' => _l('Ref'),'width'=>80,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['supplier_reference']=array('text' => _l('Supplier Ref.'),'width'=>80,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['name']=array('text' => _l('Name'),'width'=>120,'align'=>'left','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');

$colSettings['ean13']=array('text' => _l('EAN13'),'width'=>100,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['upc']=array('text' => _l('UPC'),'width'=>100,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['active']=array('text' => _l('Active'),'width'=>45,'align'=>'center','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
$colSettings['image']=array('text' => _l('Image'),'width'=>60,'align'=>'center','type'=>'ro','sort'=>'str','color'=>'','filter'=>'#text_filter');