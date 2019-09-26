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

	$id_lang = (int)Tools::getValue("id_lang","0");

	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); 
	} else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<tree id="0">';
	$icon='catalog.png';
	$icon_bis='blank.gif';

		// CONTACT
		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
						SELECT cl.`id_contact`, cl.`name`
						FROM `'._DB_PREFIX_.'contact_lang` cl
						'.(SCMS && SCI::getSelectedShop() ? 'INNER JOIN `'._DB_PREFIX_.'contact_shop` cs ON (cl.id_contact=cs.id_contact AND cs.id_shop='.(int)SCI::getSelectedShop().')':'').'
						WHERE cl.id_lang = "'.(int)$id_lang.'"
						ORDER BY cl.`name` ASC');
		if(!empty($results) && count($results)>0)
		{
			echo '<item id="contacts" text="'._l('By contact').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" open="1">';
			echo '  <userdata name="is_segment">0</userdata>';
			foreach ($results as $row)
				echo '<item id="ct_'.$row['id_contact'].'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"><itemtext><![CDATA['.$row['name'].']]></itemtext><userdata name="is_segment">0</userdata></item>';
			echo '</item>';
		}

		// EMPLOYEE
		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
						SELECT cl.`id_employee`, cl.`lastname`, cl.`firstname`
						FROM `'._DB_PREFIX_.'employee` cl
						'.(SCMS && SCI::getSelectedShop() ? 'INNER JOIN `'._DB_PREFIX_.'employee_shop` cs ON (cl.id_employee=cs.id_employee AND cs.id_shop='.(int)SCI::getSelectedShop().')':'').'
						ORDER BY cl.`firstname`, cl.`lastname` ASC');
		if(!empty($results) && count($results)>0)
		{
			echo '<item id="emloyees" text="'._l('By advisor').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" open="1">';
				echo '  <userdata name="is_segment">0</userdata>';
				echo '<item id="em_0" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"><itemtext><![CDATA['._l("None").']]></itemtext><userdata name="is_segment">0</userdata></item>';
				foreach ($results as $row)
					echo '<item id="em_'.$row['id_employee'].'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"><itemtext><![CDATA['.$row['firstname']." ".$row['lastname'].']]></itemtext><userdata name="is_segment">0</userdata></item>';
			echo '</item>';
		}
	
		// STATUT
		echo '<item id="status" text="'._l('By status').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" open="1">';
			echo '  <userdata name="is_segment">0</userdata>';
			echo '<item id="st_open" text="'._l('Open').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"><userdata name="is_segment">0</userdata></item>';
			echo '<item id="st_pending1" text="'._l('Waiting 1').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"><userdata name="is_segment">0</userdata></item>';
			echo '<item id="st_pending2" text="'._l('Waiting 2').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"><userdata name="is_segment">0</userdata></item>';
			echo '<item id="st_closed" text="'._l('Closed').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"><userdata name="is_segment">0</userdata></item>';
		echo '</item>';
	
		// LANG
		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT cl.`id_lang`, cl.`name`
				FROM `'._DB_PREFIX_.'lang` cl
				'.(SCMS && SCI::getSelectedShop() ? 'INNER JOIN `'._DB_PREFIX_.'lang_shop` cs ON (cl.id_lang=cs.id_lang AND cs.id_shop='.(int)SCI::getSelectedShop().')':'').'
				WHERE active = "1"
				ORDER BY cl.`name` ASC');
		if(!empty($results) && count($results)>1)
		{
			echo '<item id="langs" text="'._l('By lang').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" open="1">
					<userdata name="is_segment">0</userdata>';
			foreach ($results as $row)
				echo '<item id="lg_'.$row['id_lang'].'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"><itemtext><![CDATA['.$row['name'].']]></itemtext><userdata name="is_segment">0</userdata></item>';
			echo '</item>';
		}

		//PERIODE
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
			echo '  <userdata name="is_segment">0</userdata>';
		echo '</item>';

		
	if(SCSG)
		SegmentHook::getSegmentLevelFromDB(0, "customer_service");
		
	echo '</tree>';