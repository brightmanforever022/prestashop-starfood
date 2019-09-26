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

	$id_customer=(int)Tools::getValue('id_customer');

	function getRowsFromDB(){
		global $id_customer,$orderStatus;
/*		$sql = '
		SELECT m.*,e.lastname as e_lastname,e.firstname as e_firstname,c.lastname as c_lastname,c.firstname as c_firstname
		FROM '._DB_PREFIX_.'message m
		LEFT JOIN '._DB_PREFIX_.'employee e ON (m.id_employee=e.id_employee OR m.id_employee=0)
		LEFT JOIN '._DB_PREFIX_.'customer c ON (m.id_customer=c.id_customer)
		WHERE m.id_customer = '.intval($id_customer).'
		GROUP BY m.id_message
		ORDER BY m.date_add DESC';*/
		
		$sql = '
		SELECT m.*,e.lastname as e_lastname,e.firstname as e_firstname,c.lastname as c_lastname,c.firstname as c_firstname
		FROM '._DB_PREFIX_.'message m
		LEFT JOIN '._DB_PREFIX_.'employee e ON (m.id_employee=e.id_employee)
		LEFT JOIN '._DB_PREFIX_.'customer c ON (m.id_customer=c.id_customer)
		WHERE m.id_customer = '.(int)$id_customer.'
		ORDER BY m.date_add DESC';
		
		$res = Db::getInstance()->ExecuteS($sql);

		$sqlCm = 'SELECT cm.*, cm.message ,e.lastname as e_lastname,e.firstname as e_firstname,c.lastname as c_lastname,c.firstname as c_firstname
					FROM '._DB_PREFIX_.'customer_message cm
					LEFT JOIN '._DB_PREFIX_.'customer_thread ct ON (ct.id_customer_thread = cm.id_customer_thread)
					LEFT JOIN '._DB_PREFIX_.'employee e ON (e.id_employee=cm.id_employee)
					LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer=ct.id_customer)
					WHERE ct.id_customer = '.(int)$id_customer;
		$resCm = Db::getInstance()->ExecuteS($sqlCm);

		$xml='';
		foreach ($res AS $message)
		{
			$author = "";
			
			if($message['id_employee']!=0)
				$author = $message['e_firstname'][0].'. '.$message['e_lastname'];
			else
				$author = $message['c_firstname'][0].'. '.$message['c_lastname'];
			$xml.=("<row id='m_".$message['id_message']."'>");
			$xml.=("<cell><![CDATA[".$author."]]></cell>");
			$xml.=("<cell><![CDATA[".nl2br($message['message'])."]]></cell>");
			$xml.=("<cell>".$message['date_add']."</cell>");
			$xml.=("</row>\n");
		}
		foreach ($resCm AS $message)
		{
			$author = "";

			if($message['id_employee']!=0)
				$author = $message['e_firstname'][0].'. '.$message['e_lastname'];
			else
				$author = $message['c_firstname'][0].'. '.$message['c_lastname'];
			$xml.=("<row id='cm_".$message['id_customer_message']."'>");
			$xml.=("<cell><![CDATA[".$author."]]></cell>");
			$xml.=("<cell><![CDATA[".nl2br($message['message'])."]]></cell>");
			$xml.=("<cell>".$message['date_add']."</cell>");
			$xml.=("</row>\n");
		}
/*
		if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
		{
			$sql = '
			SELECT m.*,e.lastname as e_lastname,e.firstname as e_firstname,c.lastname as c_lastname,c.firstname as c_firstname
			FROM '._DB_PREFIX_.'customer_message m
			LEFT JOIN '._DB_PREFIX_.'employee e ON (m.id_employee=e.id_employee OR m.id_employee=0)
			INNER JOIN '._DB_PREFIX_.'customer c ON (m.id_customer_thread=c.id_customer)
			WHERE m.id_customer_thread = '.intval($id_customer).'
			GROUP BY m.id_customer_message
			ORDER BY m.date_add DESC';
			$res=Db::getInstance()->ExecuteS($sql);
			foreach ($res AS $message)
			{
				$author = "";
					
				if($message['id_employee']!=0)
					$author = $message['e_firstname'][0].'. '.$message['e_lastname'];
				else
					$author = $message['c_firstname'][0].'. '.$message['c_lastname'];
				$xml.=("<row id='cm_".$message['id_customer_message']."'>");
				$xml.=("<cell>".$author."</cell>");
				$xml.=("<cell><![CDATA[".nl2br($message['message'])."]]></cell>");
				$xml.=("<cell>".$message['date_add']."</cell>");
				$xml.=("</row>\n");
			}
		}*/
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
<call command="attachHeader"><param><![CDATA[#select_filter,#text_filter,#text_filter]]></param></call>
</beforeInit>
<column id="author" width="80" type="ro" align="left" sort="str"><?php echo _l('Author')?></column>
<column id="message" width="300" type="ro" align="left" sort="str"><?php echo _l('Message')?></column>
<column id="date_add" width="110" type="ro" align="left" sort="str"><?php echo _l('Creation date')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cus_message').'</userdata>'."\n";
	echo $xml;
?>
</rows>
