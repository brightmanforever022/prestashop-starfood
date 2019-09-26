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

	$id_order=(int)Tools::getValue('id_order');

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
		SELECT oh.*,e.lastname,e.firstname
		FROM '._DB_PREFIX_.'order_history oh
		LEFT JOIN '._DB_PREFIX_.'employee e ON (oh.id_employee=e.id_employee)
		WHERE oh.id_order = '.intval($id_order).'
		ORDER BY oh.date_add DESC, oh.id_order_history DESC';
		$res=Db::getInstance()->ExecuteS($sql);
		$xml='';
		foreach ($res AS $history)
		{
			$xml.=("<row id='".$history['id_order_history']."'>");
				$xml.=("<cell style=\"color:#999999\">".$history['id_order_history']."</cell>");
				$xml.=("<cell><![CDATA[".($history['id_employee']!=0 ? $history['firstname'][0].'. '.$history['lastname']:'')."]]></cell>");
			$xml.=("<cell><![CDATA[".$orderStatus[$history['id_order_state']]['name']."]]></cell>");
				$xml.=("<cell>".$history['date_add']."</cell>");
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
<column id="id_order_history" width="45" type="ro" align="right" sort="int"><?php echo _l('ID')?></column>
<column id="id_employee" width="80" type="ro" align="left" sort="str"><?php echo _l('Employee')?></column>
<column id="id_order_state" width="120" type="ro" align="left" sort="str"><?php echo _l('Order status')?></column>
<column id="date_add" width="110" type="ro" align="left" sort="str"><?php echo _l('Creation date')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('ord_history').'</userdata>'."\n";
	echo $xml;
?>
</rows>
