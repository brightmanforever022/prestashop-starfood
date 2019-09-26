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
$id_lang=(int)Tools::getValue('id_lang');

	function getwarehouse()
	{
		global $sc_agent;
		$tree = array();
		
		$shop = (int)SCI::getSelectedShop();
		if($shop == 0)
			$shop = null;
		
		$results = Warehouse::getWarehouses((!empty($shop)?false:true), $shop);
	
		$icon='building.png';
		foreach ($results as $key=>$warehouse)
		{
			$selected = "";
			/*if($key==0)
			{
				$selected = "select=\"1\"";
				setcookie("sc_warehouse_selected", $warehouse["id_warehouse"], time()+3600);
			}*/
			echo " <item ".$selected.
									" id=\"".$warehouse["id_warehouse"]."\"".
									" text=\"".formatText(str_replace('&',_l('and'),$warehouse["name"]))."\"".
									" im0=\"".$icon."\"".
									" im1=\"".$icon."\"".
									" im2=\"".$icon."\">
									<itemtext><![CDATA[".formatText($warehouse["name"])."]]></itemtext>\n";
			echo '</item>'."\n";
		}
	}

	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); 
	} else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<tree id="0">';
	getwarehouse();
	echo '</tree>';
