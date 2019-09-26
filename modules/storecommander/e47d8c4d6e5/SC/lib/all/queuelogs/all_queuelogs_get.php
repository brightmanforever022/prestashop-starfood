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

	function getLogs()
	{
		$sql="SELECT * FROM "._DB_PREFIX_."sc_queue_log
						ORDER BY date_add ASC";
		$res=Db::getInstance()->ExecuteS($sql);
		foreach($res as $row){
			echo "<row id=\"".$row['id_sc_queue_log']."\">";
			echo 		"<cell><![CDATA[".$row['id_sc_queue_log']."]]></cell>";
			echo 		"<cell><![CDATA[".$row['id_employee']."]]></cell>";
			echo 		"<cell><![CDATA[".$row['date_add']."]]></cell>";
			echo 		"<cell><![CDATA[".$row['name']."]]></cell>";
			echo 		"<cell><![CDATA[".$row['action']."]]></cell>";
			echo 		"<cell><![CDATA[".$row['row']."]]></cell>";
			echo 		"<cell><![CDATA[".print_r(json_decode($row['params']),true)."]]></cell>";
			echo "</row>";
		}
	}

	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); 
	} else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<rows>';
	getLogs();
	echo '</rows>';
