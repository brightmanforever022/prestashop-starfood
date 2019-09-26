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

	$id_order=Tools::getValue('id_order');

	// get order status
	$orderStatusPS = OrderState::getOrderStates($sc_agent->id_lang);
	$orderStatus=array();
	foreach($orderStatusPS AS $status)
	{
		$orderStatus[$status['id_order_state']]=$status;
	}

	function getRowsFromDB(){
		global $id_order,$orderStatus;
		$sql = '
			SELECT od.*
			FROM '._DB_PREFIX_.'order_detail od
			WHERE od.id_order IN ('.($id_order).')
			ORDER BY od.id_order_detail';
		$res=Db::getInstance()->ExecuteS($sql);
		$xml='';
		foreach ($res AS $history)
		{
			$xml.=("<row id='".$history['id_order_detail']."'>");
				$xml.=("<cell style=\"color:#999999\">".$history['id_order_detail']."</cell>");
				$xml.=("<cell>".$history['id_order']."</cell>");
				$xml.=("<cell>".$history['product_id']."</cell>");
				$xml.=("<cell>".$history['product_attribute_id']."</cell>");
				$xml.=("<cell><![CDATA[".$history['product_name']."]]></cell>");
				$xml.=("<cell>".$history['product_quantity']."</cell>");
				if (version_compare(_PS_VERSION_, '1.2.0.0', '>='))
				{
					$xml.=("<cell>".$history['product_quantity_in_stock']."</cell>");
					$xml.=("<cell>".$history['product_quantity_refunded']."</cell>");
				}
				$xml.=("<cell>".$history['product_quantity_return']."</cell>");
				$xml.=("<cell>".number_format($history['product_price'], 2, '.', '')."</cell>");
				$xml.=("<cell>".$history['product_ean13']."</cell>");
				if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
					$xml.=("<cell>".$history['product_upc']."</cell>");
				$xml.=("<cell><![CDATA[".$history['product_reference']."]]></cell>");
				$xml.=("<cell><![CDATA[".$history['product_supplier_reference']."]]></cell>");
				$xml.=("<cell>".number_format($history['product_weight'], 6, '.', '')."</cell>");
				$xml.=("<cell><![CDATA[".$history['tax_name']."]]></cell>");
				$xml.=("<cell>".$history['tax_rate']."</cell>");
			$xml.=("</row>");
		}
		return $xml;
	}

	//XML HEADER
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");

	$xml=getRowsFromDB();
?>
<rows id="0">
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter,#text_filter,#numeric_filter,<?php if (version_compare(_PS_VERSION_, '1.2.0.0', '>=')){ ?>#numeric_filter,#numeric_filter,<?php } ?>#numeric_filter,#numeric_filter,#text_filter<?php if (version_compare(_PS_VERSION_, '1.4.0.0', '>=')){ ?>,#text_filter<?php } ?>,#text_filter,#text_filter,#numeric_filter,#numeric_filter,#numeric_filter]]></param></call>
<call command="attachFooter"><param><![CDATA[,,,,,<?php if (version_compare(_PS_VERSION_, '1.2.0.0', '>=')){ ?>,,<?php } ?>,,#stat_total]]></param></call>
</beforeInit>
<column id="id_order_detail" width="45" type="ro" align="right" sort="int"><?php echo _l('id order detail')?></column>
<column id="id_order" width="45" type="ro" align="right" sort="int"><?php echo _l('id order')?></column>
<column id="product_id" width="45" type="ro" align="right" sort="int"><?php echo _l('id product')?></column>
<column id="product_attribute_id" width="45" type="ro" align="right" sort="int"><?php echo _l('id product attribute')?></column>
<column id="product_name" width="150" type="ro" align="left" sort="str"><?php echo _l('Name')?></column>
<column id="product_quantity" width="50" type="ro" align="right" sort="int"><?php echo _l('Quantity')?></column>
<?php 
if (version_compare(_PS_VERSION_, '1.2.0.0', '>='))
{
?>
<column id="product_quantity_in_stock" width="50" type="ro" align="right" sort="int"><?php echo _l('Qty in stock')?></column>
<column id="product_quantity_refunded" width="50" type="ro" align="right" sort="int"><?php echo _l('Qty refunded')?></column>
<?php 
}
?>
<column id="product_quantity_return" width="50" type="ro" align="right" sort="int"><?php echo _l('Qty returned')?></column>
<column id="product_price" width="60" type="ro" align="right" sort="int" format="0.00"><?php echo _l('Price excl. Tax')?></column>
<column id="product_ean13" width="70" type="ro" align="left" sort="str"><?php echo _l('EAN13')?></column>
<?php 
if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
{
?>
<column id="product_upc" width="70" type="ro" align="left" sort="str"><?php echo _l('UPC')?></column>
<?php 
}
?>
<column id="product_reference" width="70" type="ro" align="left" sort="str"><?php echo _l('Reference')?></column>
<column id="product_supplier_reference" width="70" type="ro" align="left" sort="str"><?php echo _l('Supplier reference')?></column>
<column id="product_weight" width="70" type="ro" align="right" sort="str" format="0.00"><?php echo _l('Weight')?></column>
<column id="tax_name" width="70" type="ro" align="left" sort="str"><?php echo _l('Tax')?></column>
<column id="tax_rate" width="70" type="ro" align="right" sort="str"><?php echo _l('Tax rate')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cus_orders_products').'</userdata>'."\n";
	echo $xml;
?>
</rows>
