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

	function getStatusList()
	{
		global $sc_agent;
		$results = OrderState::getOrderStates($sc_agent->id_lang);
		$icon='blank.gif';
		foreach ($results as $row)
		{
			echo '<item id="'.$row['id_order_state'].'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0" '.(SCI::getBrightness($row['color'])<128?'aCol="White"':'aCol="rgb(100, 100, 100)"').' style="background-color:'.$row['color'].'"><itemtext><![CDATA['.$row['name'].']]></itemtext><userdata name="is_segment">0</userdata></item>';
		}
	}

	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); 
	} else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<tree id="0">';
	$icon='catalog.png';
	echo '<item id="status" text="'._l('Order status').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" open="1">';
	getStatusList();
	echo '</item>';
	echo '<item id="period" text="'._l('Period').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" nocheckbox="1" open="1">';
	$icon='blank.gif';
        echo '<item id="1days" text="'._l('Today').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"/>';
        echo '<item id="2days" text="'._l('2 days').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"/>';
        echo '<item id="3days" text="'._l('3 days').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"/>';
		echo '<item id="5days" text="'._l('5 days').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"/>';
		echo '<item id="10days" text="'._l('10 days').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"/>';
		echo '<item id="15days" text="'._l('15 days').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"/>';
		echo '<item id="30days" text="'._l('30 days').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"/>';
		echo '<item id="3months" text="'._l('3 months').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"/>';
		echo '<item id="6months" text="'._l('6 months').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"/>';
		echo '<item id="1year" text="'._l('1 year').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"/>';
		echo '<item id="all" text="'._l('All').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"/>';
		echo '<item id="from_to" text="'._l('Ord from').' '._l('[date]').' '._l('to').' '._l('[date]').' " im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"/>';
		echo '<item id="inv_from_to" text="'._l('Inv from').' '._l('[date]').' '._l('to').' '._l('[date]').' " im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"/>';
	echo '  <userdata name="is_segment">0</userdata>';
	echo '</item>';

	
	if(SCSG)
		SegmentHook::getSegmentLevelFromDB(0, "orders");
	echo '</tree>';
