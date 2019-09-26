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

	$sql="SELECT DISTINCT i.id_product,i.id_image FROM "._DB_PREFIX_."image i
				WHERE NOT EXISTS (SELECT * FROM "._DB_PREFIX_."image ii WHERE i.id_product=ii.id_product AND ii.cover=1)
				AND i.position=1
				GROUP BY i.id_product";
	$res=Db::getInstance()->ExecuteS($sql);
	$updated_products = array();
	if (count($res))
	{
		foreach($res AS $i)
		{
			$sql="UPDATE "._DB_PREFIX_."image SET cover=1 WHERE id_product=".$i['id_product']." AND id_image=".$i['id_image'];
			Db::getInstance()->Execute($sql);
			$updated_products[$i['id_product']] = $i['id_product'];
		}
	}
	if(!empty($updated_products))
		ExtensionPMCM::clearFromIdsProduct($updated_products);
	echo 'Ok';