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
$filter_view_name = Tools::getValue('filter_view_name','');
$filter_view_encoded = Tools::getValue('filter_view_encoded','');
$period_selection = Tools::getValue('periodselection','');
$action = Tools::getValue('action','');

switch($action) {
	case 'add':
		if(!empty($filter_view_name) && !empty($filter_view_encoded) && !empty($period_selection)){
			$data = [
				'name' => $filter_view_name,
				'value' => $filter_view_encoded,
				'periodselection' => $period_selection
			];
			if($return = CustomSettings::addCustomSetting('ord', 'filters', $data)) {
				die($filter_view_name);
			}
		}
	break;
	case 'delete':
		$filter_used = Tools::getValue('filter_used','');
        if (!empty($filter_used)) {
	        if($return = CustomSettings::deleteCustomSetting('ord', 'filters', $filter_used)) {
			    die($filter_used);
		    }
        }
	break;
}
die('KO');

