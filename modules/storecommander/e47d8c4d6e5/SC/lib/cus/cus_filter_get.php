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

	function getGroupList()
	{
		global $sc_agent;
		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT DISTINCT g.`id_group`, gl.`name`
				FROM `'._DB_PREFIX_.'group` g
				LEFT JOIN `'._DB_PREFIX_.'group_lang` AS gl ON (g.`id_group` = gl.`id_group` AND gl.`id_lang` = '.(int)$sc_agent->id_lang.')
				'.(SCMS && SCI::getSelectedShop() ? 'INNER JOIN `'._DB_PREFIX_.'group_shop` gs ON (g.id_group=gs.id_group AND gs.id_shop='.(int)SCI::getSelectedShop().')':'').'
				ORDER BY g.`id_group` ASC');
		
		$icon='blank.gif';
		foreach ($results as $row)
			echo '<item id="gr'.$row['id_group'].'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" imwidth="0" imheight="0"><itemtext><![CDATA['.$row['name'].']]></itemtext><userdata name="is_segment">0</userdata></item>';
	}

	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); 
	} else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<tree id="0">';
	$icon='catalog.png';
	if (version_compare(_PS_VERSION_, '1.2.0.0', '>='))
	{
		if (
				(version_compare(_PS_VERSION_, '1.6.0.0', '<'))
				||
				(version_compare(_PS_VERSION_, '1.6.0.0', '>=') && SCI::getConfigurationValue("PS_GROUP_FEATURE_ACTIVE") > 0)
		)
		{
			echo '<item id="groups" text="'._l('Groups').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" open="1">';
			getGroupList();
		}
		else
			echo '<item id="groups" text="'._l('Groups').'" disabled="1" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" open="1">';
	}else{
		echo '<item id="groups" text="'._l('Customers groups are available from Prestashop 1.2').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" open="1">';
	}
	echo '  <userdata name="is_segment">0</userdata>';
	echo '</item>';

	
	if(SCSG)
		SegmentHook::getSegmentLevelFromDB(0, "customers");
	echo '</tree>';
