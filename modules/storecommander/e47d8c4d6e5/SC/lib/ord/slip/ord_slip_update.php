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
	$id_order=intval(Tools::getValue('id_order',0));
	$id_order_slip=intval(Tools::getValue('id_order_slip',0));

	if($act=="ord_slip_update" && $action=="insert" && $id_order){
		
		$order = new Order($id_order);
					$amount = 0;
					$order_detail_list = array();
					$plist = $order->getProductsDetail();
					foreach($plist as $row)
					{
						$plist_formated[$row['id_order_detail']] = 0;
					}
					foreach ($plist_formated as $id_order_detail => $amount_detail)
					{

						$order_detail_list[$id_order_detail] = array(
							'quantity' => 1,
							'id_order_detail' => (int)$id_order_detail
						);

						//$order_detail = new OrderDetail((int)$id_order_detail);
						$order_detail_list[$id_order_detail]['unit_price'] = 0;
						$order_detail_list[$id_order_detail]['amount'] = 0;
					}
		
		OrderSlip::create($order, $order_detail_list);
	}
	
	if($act=="ord_slip_update" && $action=="update"){
		
		$fields=array('total_products_tax_excl','total_products_tax_incl','total_shipping_tax_excl','total_shipping_tax_incl','conversion_rate','shipping_cost','amount','shipping_cost_amount');
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
			$sql = "UPDATE "._DB_PREFIX_."order_slip SET ".join(' , ',$todo)." WHERE id_order_slip=".intval($id_order_slip);
			Db::getInstance()->Execute($sql);
		}
		$action = "update";
	}
	
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo $sql;
