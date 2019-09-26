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
$id_discussion=intval(Tools::getValue('id',0));

function getRowsFromDB(){
	global $id_lang,$id_discussion;
	
	if(!empty($id_discussion))
	{
		$sql = '
			SELECT cm.*, CONCAT(c.`firstname`," ",c.`lastname`) as customer
			FROM '._DB_PREFIX_.'customer_message cm
				LEFT JOIN `'._DB_PREFIX_.'customer_thread` ct ON cm.`id_customer_thread` = ct.`id_customer_thread`
					LEFT JOIN `'._DB_PREFIX_.'customer` c ON c.`id_customer` = ct.`id_customer`
			WHERE cm.`id_customer_thread` = "'.(int)$id_discussion.'"
			GROUP BY cm.`id_customer_message`
			ORDER BY cm.date_add DESC
			LIMIT 2';
		$res=Db::getInstance()->ExecuteS($sql);
		$xml='';
		$res = array_reverse($res);
		foreach ($res AS $row)
		{
			$background_color = "e1ffe1";
			$name = $row['customer']." ("._l("Customer").")";
			if(!empty($row['id_employee']))
			{
				$employee = new Employee($row['id_employee']);
				$name = $employee->firstname." ".$employee->lastname." ("._l("Advisor").")";
				$background_color = "f8dfff";
			}

			$text_color = "";
			if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				if($row['private']) {
					$row['private'] = _l('Yes');
					$text_color = "666666";
				} else {
					$row['private'] = _l('No');
					$text_color = "000000";
				}
			}
			if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
				$row['message'] = html_entity_decode($row['message'], ENT_COMPAT, 'UTF-8');
				
			$xml.=("<row id='".$row['id_customer_message']."' style='color: #".$text_color.";background-color: #".$background_color."; border-bottom: 1px solid #9ca0a8;'>");
			$xml.=("<cell><![CDATA[".$name."]]></cell>");
			$xml.=("<cell><![CDATA[".$row['date_add']."]]></cell>");
			if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				$xml.=("<cell><![CDATA[".$row['private']."]]></cell>");
			$xml.=("<cell style='white-space: normal;color: #".$text_color.";background-color: #".$background_color.";border-bottom: 1px solid #9ca0a8;'><![CDATA[<br/>".nl2br($row['message'])."<br/><br/>]]></cell>");
			$xml.=("</row>");
		}
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
<call command="attachHeader"><param><![CDATA[#select_filter,#text_filter,#select_filter,#text_filter]]></param></call>
</beforeInit>
<column id="customer_name" width="140" type="ro" align="left" sort="str"><?php echo _l('Sender')?></column>
<column id="date_add" width="120" type="ro" align="left" sort="date"><?php echo _l('Date add')?></column>
<?php if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
<column id="private" width="40" type="ro" align="center" sort="str"><?php echo _l('Private')?></column>
<?php } ?>
<column id="message" width="*" type="ro" align="left" sort="str"><?php echo _l('Message')?></column>
<afterInit>
<call command="enableMultiselect"><param>1</param></call>
</afterInit>
</head>
<?php
echo $xml;
?>
</rows>
