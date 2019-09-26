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
	$id_lang=Tools::getValue('id_lang',0);
	$statusMsg=array();
	$sql = '
	SELECT os.id_order_state, os.name
	FROM `'._DB_PREFIX_.'order_state_lang` os
	WHERE id_lang='.(int)$id_lang;
	$res=Db::getInstance()->ExecuteS($sql);
	foreach($res AS $row)
		$statusMsg[$row['id_order_state']]=$row['name'];

	
	$date_start = "";
	$temp_date = _s("CAT_PROPERTIES_CUSTOMERS_START_DATE");
	list($temp_year, $temp_month, $temp_day) = explode("-",trim($temp_date));
	if(!empty($temp_date) && checkdate($temp_month, $temp_day, $temp_year))
		$date_start = trim($temp_date);
		
	$ids=Tools::getValue('ids',0);
		
	$multiple = false;
	if(strpos($ids, ",") !== false)
		$multiple = true;
	
	$sql = '
	SELECT o.id_order, o.date_add, c.firstname, c.lastname, c.id_customer, c.email, c.id_lang as cus_lang, o.payment, od.product_name, od.product_id, od.product_attribute_id, od.product_quantity';
	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		$sql.= ",o.current_state AS id_order_state, c.company";
	}else{
		$sql.= ",(SELECT oh.id_order_state FROM "._DB_PREFIX_."order_history oh WHERE oh.id_order=o.id_order ORDER BY oh.id_order_history DESC LIMIT 1)  AS id_order_state ";
	}
	$sql.= '
	FROM `'._DB_PREFIX_.'orders` o
	LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (o.id_order=od.id_order)
	LEFT JOIN `'._DB_PREFIX_.'customer` c ON (o.id_customer=c.id_customer)
		'.((SCMS && SCI::getSelectedShop()) && (!empty($sc_agent->id_employee))?" INNER JOIN "._DB_PREFIX_."employee_shop es ON (es.id_shop = o.id_shop AND es.id_employee = '".(int)$sc_agent->id_employee."') ":"").'
	WHERE od.product_id IN ('.psql($ids).')
		'.(!empty($date_start)?' AND o.date_add >= "'.pSQL($date_start).' 00:00:00" ':'').'
	GROUP BY o.id_order
	ORDER BY o.id_order DESC';
	$res=Db::getInstance()->ExecuteS($sql);

	$languages = Language::getLanguages(true);
	$language_arr = array();
	foreach($languages as $language) {
		$language_arr[$language['id_lang']] = $language['name'];
	}

	foreach($res AS $row)
	{
			$lang = $language_arr[$row['cus_lang']];
			$xml.=("<row id='".$row['id_order']."'>");
				$xml.=("<cell>".$row['id_order']."</cell>");
				$xml.=("<cell>".$row['id_customer']."</cell>");
				$xml.=("<cell><![CDATA[".$lang."]]></cell>");
				$xml.=("<cell style=\"color:#999999\"><![CDATA[".$row['firstname']."]]></cell>");
				$xml.=("<cell style=\"color:#999999\"><![CDATA[".$row['lastname']."]]></cell>");
				$xml.=("<cell><![CDATA[".$row['email']."]]></cell>");
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
				$xml.=("<cell><![CDATA[".$row['company']."]]></cell>");
                }
				//if($multiple) {
					$id = $row["product_id"];
					if(!empty($row["product_attribute_id"]))
						$id .= "_".$row["product_attribute_id"];
					$xml.=("<cell><![CDATA[".$id."]]></cell>");
					$xml.=("<cell><![CDATA[".$row["product_name"]."]]></cell>");
				//}
				$xml.=("<cell><![CDATA[".$row['product_quantity']."]]></cell>");
				$xml.=("<cell><![CDATA[".$statusMsg[$row['id_order_state']]."]]></cell>");
				$xml.=("<cell><![CDATA[".str_replace('&','-',$row['payment'])."]]></cell>");
				$xml.=("<cell><![CDATA[".$row['date_add']."]]></cell>");
			$xml.=("</row>");
	}

	//XML HEADER

	//include XML Header (as response will be in xml format)
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");

?>
<rows id="0">
<head>
<beforeInit>
	<call command="attachHeader"><param><![CDATA[#numeric_filter,#numeric_filter,#text_filter,#text_filter,#text_filter,#text_filter,<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>#text_filter,<?php }?>#numeric_filter,#text_filter,#numeric_filter,#select_filter,#text_filter,#text_filter]]></param></call>
	<call command="attachFooter"><param><![CDATA[,,,,,,<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>,<?php }?>,,#stat_total,,,]]></param></call>
</beforeInit>
<column id="id_order" width="40" type="ro" align="right" sort="int"><?php echo _l('id order')?></column>
<column id="id_customer" width="40" type="ro" align="right" sort="int"><?php echo _l('Customer ID')?></column>
<column id="language" width="100" type="ro" align="center" sort="int"><?php echo _l('Customer language')?></column>
<column id="firstname" width="80" type="ro" align="left" sort="str"><?php echo _l('Firstname')?></column>
<column id="lastname" width="80" type="ro" align="left" sort="str"><?php echo _l('Lastname')?></column>
<column id="email" width="150" type="ro" align="left" sort="str"><?php echo _l('Email')?></column>
<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
<column id="company" width="150" type="ro" align="left" sort="str"><?php echo _l('Company')?></column>
<?php } ?>
<?php /*if($multiple) {*/ ?>
<column id="product_id" width="60" type="ro" align="right" sort="int"><?php echo _l('Product ID')?></column>
<column id="product_name" width="180" type="ro" align="left" sort="str"><?php echo _l('Product name')?></column>
<?php /*}*/ ?>
<column id="quantity" width="40" type="ro" align="right" sort="int"><?php echo _l('Quantity')?></column>
<column id="id_order_state" width="200" type="ro" align="left" sort="str"><?php echo _l('Status')?></column>
<column id="payment" width="80" type="ro" align="left" sort="str"><?php echo _l('Payment')?></column>
<column id="date_add" width="120" type="ro" align="left" sort="str"><?php echo _l('Date')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_customerbyproduct').'</userdata>'."\n";
	echo 	$xml;
?>
</rows>
