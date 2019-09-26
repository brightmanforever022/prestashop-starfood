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

	$action=Tools::getValue('action');
	$name=Tools::getValue('name');
	$id_lang=Tools::getValue('id_lang');
	
	$id = 0;
	
	if(!empty($action) && $action=="insert" && !empty($name))
	{
		$group = new Group();
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && version_compare(_PS_VERSION_, '1.6.0.0', '<'))
			$group->name = $name;
		else
			$group->name = array($id_lang=>$name);
		if(SCMS)
			$group->id_shop_list = SCI::getSelectedShopActionList();
		$group->date_add = date("Y-m-d H:i:s");
		$group->date_upd = date("Y-m-d H:i:s");
		$group->price_display_method = "0";
		$group->save();
		$id = $group->id;
	}
	
	echo $id;
