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

	$act=Tools::getValue('act',0);
	$action=Tools::getValue('action',0);
	$col=Tools::getValue('col',0);
	$val=Tools::getValue('val',0);
	$id_order_slip__id_order_detail=Tools::getValue('id_order_slip__id_order_detail',0);
	$tmp = explode('__',$id_order_slip__id_order_detail);
	$id_order_slip = $tmp[0];
	$id_order_detail = $tmp[1];

	if($act=="ord_slip_detail_update" && $action=="update"){

		$fields=array('product_quantity','unit_price_tax_excl','unit_price_tax_incl','total_price_tax_excl','total_price_tax_incl','amount_tax_excl','amount_tax_incl');
		$todo=array();
		foreach($fields AS $field)
		{
			if ($col == $field)
			{
				$todo[]=$field."='".psql( $val )."'";
				//addToHistory('order_detail','modification',$field,intval($id_order),$id_lang,_DB_PREFIX_."order_detail",psql(Tools::getValue($field)));
			}
		}
		if (count($todo))
		{
			$sql = "UPDATE "._DB_PREFIX_."order_slip_detail SET ".join(' , ',$todo)." WHERE id_order_slip=".intval($id_order_slip)." AND id_order_detail=".intval($id_order_detail);
			Db::getInstance()->Execute($sql);
		}
		$action = "update";
	}
	
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
