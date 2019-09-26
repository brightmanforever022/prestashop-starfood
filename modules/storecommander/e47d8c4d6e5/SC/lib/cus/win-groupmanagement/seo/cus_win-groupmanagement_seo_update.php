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
$ids=(Tools::getValue('ids',0));
$action=(Tools::getValue('action',0));
$value=(Tools::getValue('value',null));
$return = 'KO';


if(isset($action) && $action=="updated")
{
	if(!empty($value)) {
		$ids = explode(',',$ids);
		foreach($ids as $id) {
			$data = explode('_',$id);
			$sql = "UPDATE " . _DB_PREFIX_ . "group_lang 
					SET name = '".pSQL($value)."'
					WHERE id_group = " . (int)$data[0] . " 
					AND id_lang = " . (int)$data[1];
			if(Db::getInstance()->Execute($sql)){
				$return = 'OK';
			}
		}
	}
}

die($return);

