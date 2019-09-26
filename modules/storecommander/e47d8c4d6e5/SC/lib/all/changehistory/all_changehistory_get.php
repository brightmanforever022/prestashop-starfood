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



	function getHistory()
	{
		$sql="SELECT * FROM "._DB_PREFIX_."storecom_history
						ORDER BY date_add DESC";
		$res=Db::getInstance()->ExecuteS($sql);
		foreach($res as $row){
			echo "<row id=\"".$row['id_history']."\">";
			echo 		"<cell><![CDATA[".$row['id_history']."]]></cell>";
			echo 		"<cell><![CDATA[".$row['id_employee']."]]></cell>";
			echo 		"<cell><![CDATA[".$row['section']."]]></cell>";
			echo 		"<cell><![CDATA[".$row['action']."]]></cell>";
			echo 		"<cell><![CDATA[".$row['object']."]]></cell>";
			echo 		"<cell><![CDATA[".stripslashes($row['oldvalue'])."]]></cell>";
			echo 		"<cell><![CDATA[".stripslashes($row['newvalue'])."]]></cell>";
			echo 		"<cell><![CDATA[".$row['object_id']."]]></cell>";
			echo 		"<cell><![CDATA[".$row['lang_id']."]]></cell>";
			echo 		"<cell><![CDATA[".$row['dbtable']."]]></cell>";
			echo 		"<cell><![CDATA[".$row['date_add']."]]></cell>";
			if(SCMS)
			{
				$shops = str_replace(",0,",_l('All'),$row['shops']);
				if($shops=="0")
					$shops = "-";
				echo 		"<cell><![CDATA[".$shops."]]></cell>";
			}
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
	getHistory();
	echo '</rows>';
