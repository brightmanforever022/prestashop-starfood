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
		global $id_customer;
		$sql = "SELECT a.*";
		$sql.= " FROM "._DB_PREFIX_."address a ";
		if (SCMS && SCI::getSelectedShop()>0)
			$sql .= " INNER JOIN "._DB_PREFIX_."customer c ON (a.id_customer = c.id_customer AND c.id_shop = '".(int)SCI::getSelectedShop()."') ";
		$sql.= " WHERE a.active = 1 
					AND a.deleted = 0 
					AND a.id_customer IN (".$id_customer.")";
		$sql.= " GROUP BY a.id_address
				ORDER BY a.id_customer DESC, a.id_address ASC
				";
		$res=Db::getInstance()->ExecuteS($sql);
		$xml='';
		foreach ($res AS $address)
		{
			$invoice = _l('No');
			$invoice_sql = Db::getInstance()->executeS('
							SELECT o.id_order
							FROM '._DB_PREFIX_.'orders o
							WHERE o.valid = 1 AND o.`id_address_invoice` = '.(int)$address['id_address'].'');
			if(!empty($invoice_sql) && count($invoice_sql)>0)
				$invoice = _l('Yes');
			$address['invoice'] = $invoice;
			
			$delivery = _l('No');
			$delivery_sql = Db::getInstance()->executeS('
							SELECT o.id_order
							FROM '._DB_PREFIX_.'orders o
							WHERE o.valid = 1 AND o.`id_address_delivery` = '.(int)$address['id_address'].'');
			if(!empty($delivery_sql) && count($delivery_sql)>0)
				$delivery = _l('Yes');
			$address['delivery'] = $delivery;
			
			$xml.=("<row id='".$address['id_address']."'>");
				$xml.=("<cell>".$address['id_address']."</cell>");
				$xml.=("<cell>".$address['id_customer']."</cell>");
				$xml.=("<cell><![CDATA[".$address['firstname']."]]></cell>");
				$xml.=("<cell><![CDATA[".$address['lastname']."]]></cell>");
				$xml.=("<cell><![CDATA[".$address['alias']."]]></cell>");
				if (_s('CUS_USE_COMPANY_FIELDS'))
				{
					$xml.=("<cell><![CDATA[".$address['company']."]]></cell>");
				}
				$xml.=("<cell><![CDATA[".$address['address1']."]]></cell>");
				$xml.=("<cell><![CDATA[".$address['address2']."]]></cell>");
				$xml.=("<cell><![CDATA[".$address['postcode']."]]></cell>");
				$xml.=("<cell><![CDATA[".$address['city']."]]></cell>");
				$xml.=("<cell>".$address['id_state']."</cell>");
				$xml.=("<cell>".$address['id_country']."</cell>");
				$xml.=("<cell><![CDATA[".$address['phone']."]]></cell>");
				$xml.=("<cell><![CDATA[".$address['phone_mobile']."]]></cell>");
				$xml.=("<cell><![CDATA[".$address['invoice']."]]></cell>");
				$xml.=("<cell><![CDATA[".$address['delivery']."]]></cell>");
			$xml.=("</row>");
		}
		return $xml;
	}



	// Country
	$arrCountrys=array();
	$inner = "";
	if (SCMS && SCI::getSelectedShop()>0)
		$inner = " INNER JOIN "._DB_PREFIX_."country_shop gs ON (gs.id_country = g.id_country AND gs.id_shop = '".(int)SCI::getSelectedShop()."') ";

	$sql = "SELECT g.id_country, gl.name
			FROM "._DB_PREFIX_."country g
				INNER JOIN "._DB_PREFIX_."country_lang gl ON (gl.id_country = g.id_country AND gl.id_lang = '".(int)$id_lang."')
				".$inner."
			ORDER BY gl.name";
	$res=Db::getInstance()->ExecuteS($sql);
	foreach($res as $row){
		if ($row['name']=='') $row['name']=' ';
		$arrCountrys[$row['id_country']]=$row['name'];
	}
	
	// State
	$arrStates=array();
	$arrStates[0]='-';
	$inner = "";
	if (SCMS && SCI::getSelectedShop()>0)
		$inner = " INNER JOIN "._DB_PREFIX_."country_shop cs ON (cs.id_country = g.id_country AND cs.id_shop = '".(int)SCI::getSelectedShop()."') ";

	$sql = "SELECT g.id_state, g.name, g.id_country
			FROM "._DB_PREFIX_."state g
				INNER JOIN "._DB_PREFIX_."country_lang cl ON (cl.id_country = g.id_country AND cl.id_lang = '".(int)$id_lang."')
				".$inner."
			ORDER BY  cl.name ASC, g.name ASC";
	$res=Db::getInstance()->ExecuteS($sql);
	foreach($res as $row){
		if ($row['name']=='') $row['name']=' ';
		$arrStates[$row['id_state']]=$arrCountrys[$row['id_country']]." - ".$row['name'];
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
<call command="attachHeader"><param><![CDATA[#numeric_filter,#numeric_filter,#text_filter,#text_filter,<?php if (_s('CUS_USE_COMPANY_FIELDS')){ ?>#text_filter,<?php } ?>#text_filter,#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,#text_filter,#select_filter,#select_filter]]></param></call>
</beforeInit>
<column id="id_address" width="45" type="ro" align="right" sort="int"><?php echo _l('id address')?></column>
<column id="id_customer" width="45" type="ro" align="right" sort="int"><?php echo _l('id customer')?></column>
<column id="firstname" width="70" type="ed" align="left" sort="str"><?php echo _l('Firstname')?></column>
<column id="lastname" width="70" type="ed" align="left" sort="str"><?php echo _l('Lastname')?></column>
<column id="alias" width="70" type="ed" align="left" sort="str"><?php echo _l('Alias')?></column>
<?php if (_s('CUS_USE_COMPANY_FIELDS')){ ?>
<column id="company" width="70" type="ed" align="left" sort="str"><?php echo _l('Company')?></column>
<?php } ?>
<column id="address1" width="100" type="ed" align="left" sort="str"><?php echo _l('Address')?></column>
<column id="address2" width="70" type="ed" align="left" sort="str"><?php echo _l('Address Line 2')?></column>
<column id="postcode" width="70" type="ed" align="left" sort="str"><?php echo _l('Postcode')?></column>
<column id="city" width="70" type="ed" align="left" sort="str"><?php echo _l('City')?></column>
<column id="id_state" width="70" type="coro" align="left" sort="str"><?php echo _l('State')?>
	<?php 
	foreach ($arrStates as $id=>$value)
	echo '<option value="'.$id.'"><![CDATA['.$value.']]></option>';
	?>
</column>
<column id="id_country" width="70" type="coro" align="left" sort="str"><?php echo _l('Country')?>
	<?php 
	foreach ($arrCountrys as $id=>$value)
	echo '<option value="'.$id.'"><![CDATA['.$value.']]></option>';
	?>
</column>
<column id="phone" width="100" type="ed" align="left" sort="str"><?php echo _l('Phone')?></column>
<column id="phone_mobile" width="100" type="ed" align="left" sort="str"><?php echo _l('Phone mobile')?></column>
<column id="invoice" width="80" type="ro" align="center" sort="str"><?php echo _l('Invoice?')?></column>
<column id="delivery" width="70" type="ro" align="center" sort="str"><?php echo _l('Delivery?')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cus_addresses').'</userdata>'."\n";
	echo $xml;
?>
</rows>
