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

	$id_order=intval(Tools::getValue('id_order',0));
	$id_order_detail=intval(Tools::getValue('gr_id',0));

	if(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="updated"){
		
		$fields=array('product_name','product_quantity','product_price','product_ean13','product_upc','product_isbn','product_reference','product_supplier_reference','product_weight','tax_name','tax_rate');
		$todo=array();
		foreach($fields AS $field)
		{
			if (isset($_GET[$field]) || isset($_POST[$field]))
			{
				if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && ($field=='product_price' || $field=='product_quantity'))
				{

					$order = new Order((int)$id_order);
					$order_detail = new OrderDetail((int)$id_order_detail);
					$order_detail->product_price = (float)Tools::getValue($field);
			
					$tax_rate = $order_detail->total_price_tax_incl / $order_detail->total_price_tax_excl;

					if ($field=='product_quantity')
					{
						$product_quantity = (int)Tools::getValue($field);
						$order_detail->product_quantity = $product_quantity;
					}else{
						$product_quantity = $order_detail->product_quantity;
					}
					if ($field=='product_price')
					{
						$product_price_tax_excl = Tools::ps_round((float)Tools::getValue($field), 2);
						$product_price_tax_incl = Tools::ps_round($product_price_tax_excl * $tax_rate, 2);
					}else{
						$product_price_tax_excl = $order_detail->unit_price_tax_excl;
						$product_price_tax_incl = $order_detail->unit_price_tax_incl;
					}
					$total_products_tax_incl = $product_price_tax_incl * $product_quantity;
					$total_products_tax_excl = $product_price_tax_excl * $product_quantity;
			
					// Calculate differences of price (Before / After)
					$diff_price_tax_incl = $total_products_tax_incl - $order_detail->total_price_tax_incl;
					$diff_price_tax_excl = $total_products_tax_excl - $order_detail->total_price_tax_excl;
			
					// Apply change on OrderInvoice
					if ($order_detail->id_order_invoice)
						$order_invoice = new OrderInvoice($order_detail->id_order_invoice);

					if ($diff_price_tax_incl != 0 && $diff_price_tax_excl != 0)
					{
						$order_detail->unit_price_tax_excl = $product_price_tax_excl;
						$order_detail->unit_price_tax_incl = $product_price_tax_incl;
			
						$order_detail->total_price_tax_incl += $diff_price_tax_incl;
						$order_detail->total_price_tax_excl += $diff_price_tax_excl;
			
						if (isset($order_invoice))
						{
							// Apply changes on OrderInvoice
							$order_invoice->total_products += $diff_price_tax_excl;
							$order_invoice->total_products_wt += $diff_price_tax_incl;
			
							$order_invoice->total_paid_tax_excl += $diff_price_tax_excl;
							$order_invoice->total_paid_tax_incl += $diff_price_tax_incl;
						}
			
						// Apply changes on Order
						$order = new Order($order_detail->id_order);
						$order->total_products += $diff_price_tax_excl;
						$order->total_products_wt += $diff_price_tax_incl;
			
						$order->total_paid += $diff_price_tax_incl;
						$order->total_paid_tax_excl += $diff_price_tax_excl;
						$order->total_paid_tax_incl += $diff_price_tax_incl;
			
						$order->update();
					}
			
					// Save order detail
					$order_detail->update();
					// Save order invoice
					if (isset($order_invoice))
						 $order_invoice->update();

					addToHistory('order_detail','modification',$field,intval($id_order),$id_lang,_DB_PREFIX_."order_detail",psql(Tools::getValue($field)));

				}else{
					$todo[]=$field."='".psql(html_entity_decode( Tools::getValue($field)))."'";
					addToHistory('order_detail','modification',$field,intval($id_order),$id_lang,_DB_PREFIX_."order_detail",psql(Tools::getValue($field)));
				}
			}
		}
		if (count($todo))
		{
			$sql = "UPDATE "._DB_PREFIX_."order_detail SET ".join(' , ',$todo)." WHERE id_order_detail=".intval($id_order_detail);
			Db::getInstance()->Execute($sql);
		}
		$newId = $_POST["gr_id"];
		$action = "update";
	}
	
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<data>';
	echo "<action type='".$action."' sid='".$_POST["gr_id"]."' tid='".$newId."'/>";
	echo ($debug && isset($sql) ? '<sql><![CDATA['.$sql.']]></sql>':'');
	echo ($debug && isset($sql2) ? '<sql><![CDATA['.$sql2.']]></sql>':'');
	echo ($debug && isset($sql3) ? '<sql><![CDATA['.$sql3.']]></sql>':'');
	echo '</data>';
?>