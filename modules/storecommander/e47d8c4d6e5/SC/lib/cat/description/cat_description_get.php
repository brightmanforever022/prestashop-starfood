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

	$contentType=Tools::getValue('content','');
	if(Tools::getValue('act','')=='cat_description_get' && sc_in_array($contentType,array('description_short','description'),"catDescGet_fields"))
	{
		$id_product=Tools::getValue('id_product','0');
		$id_lang=Tools::getValue('id_lang','0');
		
		if (SCMS)
		{
			$id_shop = SCI::getSelectedShop();
			if(empty($id_shop))
			{
				$product = new Product($id_product);
				$id_shop = $product->id_shop_default;
			}
		}
		
		$sql = "SELECT ".psql($contentType)." FROM "._DB_PREFIX_."product_lang WHERE id_product='".intval($id_product)."' AND id_lang='".intval($id_lang)."'";
		if (SCMS)
			$sql.=" AND id_shop=".(int)$id_shop;
		$row=Db::getInstance()->getRow($sql);
		echo $row[$contentType];
	}
