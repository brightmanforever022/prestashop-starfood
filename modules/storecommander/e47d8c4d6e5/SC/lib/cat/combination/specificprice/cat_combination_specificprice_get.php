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

	$id_lang=intval(Tools::getValue('id_lang',0));
	$id_product_attribute=(Tools::getValue('id_product_attribute',0));

	function getRowsFromDB(){
		global $id_lang,$id_product_attribute;

		$sql = '
		SELECT *
		FROM '._DB_PREFIX_.'specific_price
		WHERE id_product_attribute IN ('.pSQL($id_product_attribute).')
		ORDER BY from_quantity';
		$res=Db::getInstance()->ExecuteS($sql);
		$xml='';
		foreach ($res AS $specific_price)
		{
			if ($specific_price['from']==$specific_price['to'])
			{
				$specific_price['from']=date('Y-01-01 00:00:00');
				$specific_price['to']=(date('Y')+1).date('-m-d 00:00:00');
			}
			if ($specific_price['from']=='0000-00-00 00:00:00') $specific_price['from']=date('Y-01-01 00:00:00');
			if ($specific_price['to']=='0000-00-00 00:00:00') $specific_price['to']=(date('Y')+1).date('-m-d 00:00:00'); 
			$xml.=("<row id='".$specific_price['id_specific_price']."'>");
				$xml.=("<cell style=\"color:#999999\">".$specific_price['id_specific_price']."</cell>");
				$xml.=("<cell>".$specific_price['id_product_attribute']."</cell>");
				if (SCMS)
				{
					$xml.=("<cell>".$specific_price['id_shop']."</cell>");
					$xml.=("<cell>".$specific_price['id_shop_group']."</cell>");
				}
				$xml.=("<cell>".$specific_price['id_group']."</cell>");
				$xml.=("<cell>".$specific_price['from_quantity']."</cell>");
				$xml.=("<cell>".($specific_price['price'] != -1 || version_compare(_PS_VERSION_, '1.5.0.0', '<') ? number_format($specific_price['price'],2):"-1")."</cell>");
				if(version_compare(_PS_VERSION_, '1.6.0.11', '>='))
					$xml.=("<cell>".$specific_price['reduction_tax']."</cell>");
				$xml.=("<cell>".($specific_price['reduction_type']=='percentage'?(number_format($specific_price['reduction']*100,2)).'%':number_format($specific_price['reduction'],2))."</cell>");
				$xml.=("<cell>".$specific_price['from']."</cell>");
				$xml.=("<cell>".$specific_price['to']."</cell>");
				$xml.=("<cell>".$specific_price['id_country']."</cell>");
				$xml.=("<cell>".$specific_price['id_currency']."</cell>");
                if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    if ($specific_price['id_customer'] > 0 ){
                        $sql = 'SELECT firstname, lastname FROM '._DB_PREFIX_.'customer WHERE id_customer = '.(int)$specific_price['id_customer'];
                        $customer = Db::getInstance()->getRow($sql);
                        $xml.= '<cell><![CDATA['.$customer['firstname'].' '.$customer['lastname'].']]></cell>';
                    } else {
                        $xml.= '<cell><![CDATA['._l('All').']]></cell>';
                    }
                }
			$xml.=("</row>");
		}
		return $xml;
	}
	if (SCMS)
	{
		$sql = 'SELECT *
						FROM '._DB_PREFIX_.'shop
						ORDER BY name';
		$res=Db::getInstance()->ExecuteS($sql);
		$shops='';
		foreach ($res AS $shop)
		{
			$shop['name'] = str_replace("&", _l('and'), $shop['name']);
			$shops.='<option value="'.$shop['id_shop'].'">'.$shop['name'].'</option>';
		}
		
		$sql = 'SELECT *
						FROM '._DB_PREFIX_.'shop_group
						ORDER BY name';
		$res=Db::getInstance()->ExecuteS($sql);
		$group_shops='';
		foreach ($res AS $group)
			$group_shops.='<option value="'.$group['id_shop_group'].'">'.$group['name'].'</option>';
	}

	$sql = 'SELECT *
					FROM '._DB_PREFIX_.'group_lang
					WHERE id_lang='.(int)$id_lang.'
					ORDER BY id_group';
	$res=Db::getInstance()->ExecuteS($sql);
	$groups='';
	foreach ($res AS $group)
	{
		$group['name'] = str_replace("&", _l('and'), $group['name']);
		$groups.='<option value="'.$group['id_group'].'">'.$group['name'].'</option>';
	}

	$sql = 'SELECT cl.id_country,cl.name
					FROM '._DB_PREFIX_.'country_lang cl
					LEFT JOIN '._DB_PREFIX_.'country c ON (c.id_country=cl.id_country)
					WHERE cl.id_lang='.(int)$id_lang.' AND c.active=1
					ORDER BY cl.name';
	$res=Db::getInstance()->ExecuteS($sql);
	$countries='';
	foreach ($res AS $country)
	{
		$country['name'] = str_replace("&", _l('and'), $country['name']);
		$countries.='<option value="'.$country['id_country'].'">'.$country['name'].'</option>';
	}
	
	$sql = 'SELECT id_currency,iso_code
					FROM '._DB_PREFIX_.'currency
					WHERE active=1
					ORDER BY iso_code';
	$res=Db::getInstance()->ExecuteS($sql);
	$currencies='';
	foreach ($res AS $currency)
		$currencies.='<option value="'.$currency['id_currency'].'">'.$currency['iso_code'].'</option>';


	//XML HEADER
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");

	$xml = "";
	if(!empty($id_product_attribute))
		$xml=getRowsFromDB();
?>
<rows id="0">
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#text_filter,#text_filter<?php if(SCMS){ ?>,#select_filter,#select_filter<?php } ?>,#select_filter,#text_filter,#text_filter<?php if(version_compare(_PS_VERSION_, '1.6.0.11', '>=')) { ?>,#select_filter<?php } ?>,#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter]]></param></call>
</beforeInit>
<column id="id_specific_price" width="40" type="ro" align="right" sort="int"><?php echo _l('ID')?></column>
<column id="id_product_attribute" width="40" type="ro" align="right" sort="int"><?php echo _l('id_product_attr')?></column>
<?php if(SCMS){ ?>
<column id="id_shop" width="50" type="coro" align="right" sort="int"><?php echo _l('Shop')?><option value="0"><?php echo _l('All')?></option><?php echo $shops?></column>
<column id="id_shop_group" width="50" type="coro" align="right" sort="int"><?php echo _l('Shop group')?><option value="0"><?php echo _l('All')?></option><?php echo $group_shops?></column>
<?php } ?>
<column id="id_group" width="50" type="coro" align="right" sort="int"><?php echo _l('Customer group')?><option value="0"><?php echo _l('All')?></option><?php echo $groups?></column>
<column id="from_quantity" width="50" type="ed" align="right" sort="int"><?php echo _l('Minimum quantity')?></column>
<column id="price" width="50" type="ed" align="right" sort="int"><?php echo _l('Fixed price')?></column>
<?php if(version_compare(_PS_VERSION_, '1.6.0.11', '>=')) { ?>
	<column id="reduction_tax" width="50" type="coro" align="right" sort="int"><?php echo _l('Reduction tax')?>
		<option value="0"><?php echo _l('Excl. tax'); ?></option>
		<option value="1"><?php echo _l('Incl. tax'); ?></option>
	</column>
<?php } ?>
<column id="reduction" width="50" type="ed" align="right" sort="int"><?php echo _l('Reduction')?></column>
<column id="from" width="90" type="dhxCalendarA" align="left" sort="date"><?php echo _l('Reduction from')?></column>
<column id="to" width="90" type="dhxCalendarA" align="left" sort="date"><?php echo _l('Reduction to')?></column>
<column id="id_country" width="50" type="coro" align="right" sort="int"><?php echo _l('Country')?><option value="0"><?php echo _l('All')?></option><?php echo $countries?></column>
<column id="id_currency" width="50" type="coro" align="right" sort="int"><?php echo _l('Currency')?><option value="0"><?php echo _l('All')?></option><?php echo $currencies?></column>
<?php if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
<column id="id_customer" width="100" type="combo" align="left" sort="str" source="index.php?ajax=1&amp;act=cat_specificprice_customer_get&amp;ajaxCall=1" auto="true" cache="false"><?php echo _l('Customer')?></column>
<?php } ?>
<afterInit>
<call command="enableMultiselect"><param>1</param></call>
</afterInit>
</head>
<?php
//  format="%Y-%m-%d 00:00:00"
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_combination_specificprice').'</userdata>'."\n";
	echo $xml;
?>
</rows>
