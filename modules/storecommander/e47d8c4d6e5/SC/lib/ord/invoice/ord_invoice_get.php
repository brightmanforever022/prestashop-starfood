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

	$id_customer=Tools::getValue('id_customer');
	$id_lang=intval(Tools::getValue('id_lang'));

	function getRowsFromDB(){
		global $id_customer,$id_lang;
		
		$sql = "SELECT o.id_shop, o.id_customer, o.reference ,oi.*";
		$sql.= " FROM "._DB_PREFIX_."order_invoice oi";
		$sql.= " LEFT JOIN "._DB_PREFIX_."orders o ON (o.id_order=oi.id_order)";
		$sql.= " WHERE o.id_customer IN (".(int)$id_customer.")";
		$sql.= (SCMS && SCI::getSelectedShop() > 0 ? " AND o.id_shop = ".(int)SCI::getSelectedShop():'');
		$sql.= " ORDER BY oi.id_order DESC";
		
		$customers = array();
		$shops = array();
		
		$res=Db::getInstance()->ExecuteS($sql);
		$xml='';
		foreach ($res AS $slip)
		{
			if(SCMS)
			{
				$shop = "";
				if(empty($shops[$slip['id_shop']]))
				{
					$shopObj = new Shop($slip['id_shop']);
					$shops[$slip['id_shop']] = $shopObj->name;
					$shop = $shops[$slip['id_shop']];
				}
				else
					$shop = $shops[$slip['id_shop']];
			}			
			
			if(empty($customers[$slip['id_customer']]))
			{
				$customer = new Customer($slip['id_customer']);
				$customers[$slip['id_customer']] = $customer;
			}
			else
				$customer = $customers[$slip['id_customer']];

			if($slip['delivery_date']=="0000-00-00 00:00:00")
				$slip['delivery_date'] = "";

			$xml.=("<row id='".$slip['id_order_invoice']."'>");
				$xml.=("<cell>".$slip['id_order']."</cell>");
				$xml.=("<cell>".$slip['reference']."</cell>");
				$xml.=("<cell>".$slip['id_order_invoice']."</cell>");
				if(SCMS)
					$xml.=("<cell>".$shop."</cell>");
				$xml.=("<cell>".$slip['id_customer']."</cell>");
				$xml.=("<cell><![CDATA[".$customer->firstname."]]></cell>");
				$xml.=("<cell><![CDATA[".$customer->lastname."]]></cell>");
				$xml.=("<cell><![CDATA[".$customer->email."]]></cell>");

				$xml.=("<cell><![CDATA[".$slip['delivery_date']."]]></cell>");

				$xml.=("<cell><![CDATA[".$slip['total_discount_tax_excl']."]]></cell>");
				$xml.=("<cell><![CDATA[".$slip['total_discount_tax_incl']."]]></cell>");
				$xml.=("<cell><![CDATA[".$slip['total_paid_tax_excl']."]]></cell>");
				$xml.=("<cell><![CDATA[".$slip['total_paid_tax_incl']."]]></cell>");
				$xml.=("<cell><![CDATA[".$slip['total_products']."]]></cell>");
				$xml.=("<cell><![CDATA[".$slip['total_products_wt']."]]></cell>");
				$xml.=("<cell><![CDATA[".$slip['total_shipping_tax_excl']."]]></cell>");
				$xml.=("<cell><![CDATA[".$slip['total_shipping_tax_incl']."]]></cell>");
				$xml.=("<cell><![CDATA[".$slip['total_wrapping_tax_excl']."]]></cell>");
				$xml.=("<cell><![CDATA[".$slip['total_wrapping_tax_incl']."]]></cell>");

				$xml.=("<cell><![CDATA[".$slip['note']."]]></cell>");

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
<call command="attachHeader"><param><![CDATA[#numeric_filter,#text_filter,#numeric_filter,<?php if (SCMS){ ?>#numeric_filter,<?php } ?>#numeric_filter,#text_filter,#text_filter,#text_filter,#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter,#text_filter]]></param></call>
</beforeInit>
<column id="id_order" width="45" type="ro" align="right" sort="int"><?php echo _l('id order')?></column>
<column id="reference" width="70" type="ro" align="left" sort="str"><?php echo _l('Reference')?></column>
<column id="id_order_slip" width="45" type="ro" align="right" sort="int"><?php echo _l('id order invoice')?></column>
<?php if (SCMS){ ?><column id="id_shop" width="45" type="ro" align="right" sort="int"><?php echo _l('Shop')?></column><?php } ?>
<column id="id_customer" width="45" type="ro" align="right" sort="int"><?php echo _l('id customer')?></column>
<column id="firstname" width="70" type="ro" align="left" sort="str"><?php echo _l('Firstname')?></column>
<column id="lastname" width="70" type="ro" align="left" sort="str"><?php echo _l('Lastname')?></column>
<column id="email" width="100" type="ro" align="left" sort="str"><?php echo _l('Email')?></column>
<column id="delivery_date" width="110" type="dhxCalendarA" align="right" sort="date"><?php echo _l('Delivery date')?></column>
<column id="total_discount_tax_excl" width="80" type="edn" format="0.00" align="right" sort="int"><?php echo _l('Total discount Tax excl')?></column>
<column id="total_discount_tax_incl" width="80" type="edn" format="0.00" align="right" sort="int"><?php echo _l('Total discount Tax incl')?></column>
<column id="total_paid_tax_excl" width="80" type="edn" format="0.00" align="right" sort="int"><?php echo _l('Total paid Tax excl')?></column>
<column id="total_paid_tax_incl" width="80" type="edn" format="0.00" align="right" sort="int"><?php echo _l('Total paid Tax incl')?></column>
<column id="total_products" width="80" type="edn" format="0.00" align="right" sort="int"><?php echo _l('Total products')?></column>
<column id="total_products_wt" width="80" type="edn" format="0.00" align="right" sort="int"><?php echo _l('Total products WT')?></column>
<column id="total_shipping_tax_excl" width="80" type="edn" format="0.00" align="right" sort="int"><?php echo _l('Total shipping Tax excl')?></column>
<column id="total_shipping_tax_incl" width="80" type="edn" format="0.00" align="right" sort="int"><?php echo _l('Total shipping Tax incl')?></column>
<column id="total_wrapping_tax_excl" width="80" type="edn" format="0.00" align="right" sort="int"><?php echo _l('Total wrapping Tax excl')?></column>
<column id="total_wrapping_tax_incl" width="80" type="edn" format="0.00" align="right" sort="int"><?php echo _l('Total wrapping Tax incl')?></column>
<column id="note" width="150" type="edtxt" align="left" sort="str"><?php echo _l('Note')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('ord_invoice').'</userdata>'."\n";
	echo $xml;
?>
</rows>
