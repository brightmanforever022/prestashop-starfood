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
		$yesno=array(0=>_l('No'),1=>_l('Yes'));
		$sql = '
			SELECT od.*,ps.id_category_default
			FROM '._DB_PREFIX_.'order_detail od
			LEFT JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = od.product_id AND ps.id_shop = od.id_shop)
			WHERE od.id_order IN ('.pSQL($id_order).')
			ORDER BY od.id_order_detail';
		$res=Db::getInstance()->ExecuteS($sql);
		$xml='';
		foreach ($res AS $history)
		{
			$actual_quantity_in_stock = SCI::getProductQty($history['product_id'], $history['product_attribute_id'],  $history['id_warehouse']);
			/*if($history['product_id']==7)
				$actual_quantity_in_stock = 1;*/
			// Dans le cas où le stock au moment de la commande
			// est négatif, il faut utiliser la différence
			// de stock pour savoir combien de produits il y a
			// actuellement par rapport au passage de la commande
			// Exemple : -15 à la commande et -10 actuellement => 5 en stock
//			if($history['product_quantity_in_stock']<0 && $actual_quantity_in_stock>=$history['product_quantity_in_stock'])
//				$actual_quantity_in_stock -= $history['product_quantity_in_stock'];
			
			// IN STOCK
			if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
			{
				$history['instock']=0;
				$color_instock = "";
				$order_in_stock=($history['product_quantity_in_stock'] >= $history['product_quantity'] ? 1 : 0);
				if($order_in_stock==1)
					$history['instock']=1;
				else
				{
					$total_qty_wanted = 0;
					if(!empty($history['product_id']))
					{
						$sql_details = "SELECT product_quantity FROM "._DB_PREFIX_."order_detail WHERE product_id='".intval($history['product_id'])."' AND product_attribute_id='".intval($history['product_attribute_id'])."'";
						$res_details=Db::getInstance()->ExecuteS($sql_details);
						foreach($res_details as $res_detail)
						{
							$total_qty_wanted += $res_detail["product_quantity"];
						}
				
						if($actual_quantity_in_stock >= $history['product_quantity'])
							$history['instock']=1;
						if($actual_quantity_in_stock<$total_qty_wanted && $actual_quantity_in_stock>0)
						{
							$history['instock']=3;
							$color_instock = "#FF9900";
						}
					}
				}
				if($history['instock']==0 && empty($color_instock))
					$color_instock = "#FF0000";
			}
			
			if($history['instock']==2)
				$instock = _l("Insufficient current total stock");
			elseif($history['instock']==3)
				$instock = _l("Partial");
			else
				$instock = $yesno[$history['instock']];
			
			$xml.=("<row id='".$history['id_order_detail']."'>");
				$xml.="<userdata name=\"open_cat_grid\">".$history['id_category_default']."-".$history['product_id']."</userdata>";
				$xml.=("<cell style=\"color:#999999\">".$history['id_order_detail']."</cell>");
				$xml.=("<cell>".$history['id_order']."</cell>");
				$xml.=("<cell>".$history['product_id']."</cell>");
				$xml.=("<cell>".$history['product_attribute_id']."</cell>");
				$xml.=("<cell><![CDATA[".$history['product_name']."]]></cell>");
				$xml.=("<cell>".$history['product_quantity']."</cell>");
				$xml.=("<cell>".$actual_quantity_in_stock."</cell>");
				if (version_compare(_PS_VERSION_, '1.2.0.0', '>='))
					$xml.=("<cell>".$history['product_quantity_in_stock']."</cell>");
				if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
					$xml.="<cell".(!empty($color_instock)?' bgColor="'.$color_instock.'"  style="color:#FFFFFF"':'').">".$instock."</cell>";
				if (version_compare(_PS_VERSION_, '1.2.0.0', '>='))
					$xml.=("<cell>".$history['product_quantity_refunded']."</cell>");
				$xml.=("<cell>".$history['product_quantity_return']."</cell>");
				$xml.=("<cell>".number_format($history['product_price'], 6, '.', '')."</cell>");
				$xml.=("<cell><![CDATA[".$history['product_ean13']."]]></cell>");
				if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
					$xml.=("<cell><![CDATA[".$history['product_upc']."]]></cell>");
				if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
					$xml.=("<cell><![CDATA[".$history['product_isbn']."]]></cell>");
				$xml.=("<cell><![CDATA[".$history['product_reference']."]]></cell>");
				$xml.=("<cell><![CDATA[".$history['product_supplier_reference']."]]></cell>");
				$xml.=("<cell>".number_format($history['product_weight'], 6, '.', '')."</cell>");
				if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
				{
					$xml.=("<cell><![CDATA[".$history['tax_name']."]]></cell>");
					$xml.=("<cell>".$history['tax_rate']."</cell>");
				}
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
<call command="attachHeader"><param><![CDATA[#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter,#text_filter,#numeric_filter,#numeric_filter<?php if (version_compare(_PS_VERSION_, '1.2.0.0', '>=')){ ?>,#numeric_filter<?php } ?><?php if (version_compare(_PS_VERSION_, '1.4.0.0', '>=')){ ?>,#select_filter<?php } ?><?php if (version_compare(_PS_VERSION_, '1.2.0.0', '>=')){ ?>,#numeric_filter<?php } ?>,#numeric_filter,#numeric_filter,#text_filter<?php if (version_compare(_PS_VERSION_, '1.4.0.0', '>=')){ ?>,#text_filter<?php } ?>,#text_filter,#text_filter,#numeric_filter<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '<')){ ?>,#numeric_filter,#numeric_filter<?php } ?>]]></param></call>
<call command="attachFooter"><param><![CDATA[,,,,,#stat_total]]></param></call>
</beforeInit>
<column id="id_order_detail" width="45" type="ro" align="right" sort="int"><?php echo _l('id order detail')?></column>
<column id="id_order" width="45" type="ro" align="right" sort="int"><?php echo _l('id order')?></column>
<column id="product_id" width="45" type="ro" align="right" sort="int"><?php echo _l('id product')?></column>
<column id="product_attribute_id" width="45" type="ro" align="right" sort="int"><?php echo _l('id product attribute')?></column>
<column id="product_name" width="150" type="edtxt" align="left" sort="str"><?php echo _l('Name')?></column>
<column id="product_quantity" width="50" type="edtxt" align="right" sort="int"><?php echo _l('Quantity orded')?></column>
<column id="actual_quantity_in_stock" width="50" type="ro" align="right" sort="int"><?php echo _l('Current qty in stock')?></column>
<?php if (version_compare(_PS_VERSION_, '1.2.0.0', '>=')) { ?>
<column id="product_quantity_in_stock" width="50" type="ro" align="right" sort="int"><?php echo _l('Qty in stock at time of order')?></column>
<?php } ?>
<?php if (version_compare(_PS_VERSION_, '1.4.0.0', '>=')) { ?>
<column id="instock" width="45" type="ro" align="right" sort="int"><?php echo _l('In stock')?></column>
<?php } ?>
<?php if (version_compare(_PS_VERSION_, '1.2.0.0', '>=')) { ?>
<column id="product_quantity_refunded" width="50" type="ro" align="right" sort="int"><?php echo _l('Qty refunded')?></column>
<?php } ?>
<column id="product_quantity_return" width="50" type="ro" align="right" sort="int"><?php echo _l('Qty returned')?></column>
<column id="product_price" width="60" type="edn" align="right" sort="int" format="0.00"><?php echo _l('Price excl. Tax')?></column>
<column id="product_ean13" width="70" type="edtxt" align="left" sort="str"><?php echo _l('EAN13')?></column>
<?php
if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
{
	?>
	<column id="product_upc" width="70" type="edtxt" align="left" sort="str"><?php echo _l('UPC')?></column>
	<?php
}
if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
{
	?>
	<column id="product_isbn" width="70" type="edtxt" align="left" sort="str"><?php echo _l('ISBN')?></column>
	<?php
}
?>
<column id="product_reference" width="70" type="edtxt" align="left" sort="str"><?php echo _l('Reference')?></column>
<column id="product_supplier_reference" width="70" type="edtxt" align="left" sort="str"><?php echo _l('Supplier reference')?></column>
<column id="product_weight" width="70" type="edn" align="right" sort="str" format="0.00"><?php echo _l('Weight')?></column>
<?php
if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
{
?>
<column id="tax_name" width="70" type="edtxt" align="left" sort="str"><?php echo _l('Tax')?></column>
<column id="tax_rate" width="70" type="edn" align="right" sort="str"><?php echo _l('Tax rate')?></column>
<?php
}
?>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('ord_product').'</userdata>'."\n";
	echo $xml;
?>
</rows>
