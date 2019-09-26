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

	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
	
		$sql="SELECT DISTINCT id_product FROM "._DB_PREFIX_."product_attribute";
		$res=Db::getInstance()->ExecuteS($sql);
	
		if (count($res))
		{
			$updated_products=array();
			foreach ($res AS $r) {
				SCI::qtySumStockAvailable((int)$r['id_product']);
				$updated_products[$r["id_product"]] = $r["id_product"];
			}
			if(!empty($updated_products))
				ExtensionPMCM::clearFromIdsProduct($updated_products);
		}
		echo 'Ok';
		
	}else{
		
		echo 'Bad Prestashop version';
		
	}