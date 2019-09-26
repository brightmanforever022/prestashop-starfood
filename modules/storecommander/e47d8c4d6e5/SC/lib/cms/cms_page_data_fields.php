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

$colSettings['id_cms']=array('text' => _l('id'),'width'=>70,'align'=>'left','type'=>'ro','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['meta_title']=array('text' => _l('meta_title'),'width'=>200,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['meta_description']=array('text' => _l('meta_description'),'width'=>200,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['meta_keywords']=array('text' => _l('meta_keywords'),'width'=>200,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['link_rewrite']=array('text' => _l('link_rewrite'),'width'=>200,'align'=>'left','type'=>'edtxt','sort'=>'str','color'=>'','filter'=>'#text_filter');
$colSettings['position']=array('text' => _l('position'),'width'=>70,'align'=>'left','type'=>'ed','sort'=>'int','color'=>'','filter'=>'#numeric_filter');
$colSettings['active']=array('text' => _l('active'),'width'=>70,'align'=>'left','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
if(version_compare(_PS_VERSION_, '1.5.6.1', '>=')) {
	$colSettings['indexation']=array('text' => _l('indexation'),'width'=>70,'align'=>'left','type'=>'coro','sort'=>'int','color'=>'','filter'=>'#select_filter','options'=>array(0=>_l('No'),1=>_l('Yes')));
}
