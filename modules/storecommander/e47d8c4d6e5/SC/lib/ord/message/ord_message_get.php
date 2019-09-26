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

	function getRowsFromDB(){
		global $id_order,$orderStatus;
		$sql = '
		SELECT m.*,e.lastname,e.firstname
		FROM '._DB_PREFIX_.'message m
		LEFT JOIN '._DB_PREFIX_.'employee e ON (m.id_employee=e.id_employee)
		WHERE m.id_order = '.intval($id_order).'
		ORDER BY m.date_add DESC';
		$res=Db::getInstance()->ExecuteS($sql);
		$xml='';
		foreach ($res AS $message)
		{
			$xml.=("<row id='".$message['id_message']."'>");
				$xml.=("<cell style=\"color:#999999\">".$message['id_message']."</cell>");
				$xml.=("<cell><![CDATA[".($message['id_employee']!=0 ? $message['firstname'][0].'. '.$message['lastname']:'')."]]></cell>");
				$xml.=("<cell><![CDATA[".nl2br($message['message'])."]]></cell>");
				$xml.=("<cell>".$message['date_add']."</cell>");
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
<column id="id_message" width="45" type="ro" align="right" sort="int"><?php echo _l('ID')?></column>
<column id="author" width="80" type="ro" align="left" sort="str"><?php echo _l('Author')?></column>
<column id="message" width="300" type="ro" align="left" sort="str"><?php echo _l('Message')?></column>
<column id="date_add" width="110" type="ro" align="left" sort="str"><?php echo _l('Creation date')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('ord_message').'</userdata>'."\n";
	echo $xml;
?>
</rows>
