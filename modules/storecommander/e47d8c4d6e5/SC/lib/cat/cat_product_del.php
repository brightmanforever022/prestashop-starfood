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

	if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
		require_once(SC_PS_PATH_DIR.'images.inc.php');

	$productlist=Tools::getValue('productlist','');
	if ($productlist!='')
	{
		$productlistarray=explode(",",$productlist);
		foreach($productlistarray AS $idproduct)
		{
			$product=new Product(intval($idproduct));
			if (SCMS)
			{
				$id_shop_list_array = Product::getShopsByProduct($product->id);
				$id_shop_list = array();
				foreach ($id_shop_list_array as $array_shop)
					$id_shop_list[] = $array_shop['id_shop'];
				$product->id_shop_list = $id_shop_list;
			}
			$product->delete();
			addToHistory('catalog_tree','delete',"product",intval($product->id),null,_DB_PREFIX_."product",null,null);
				
		}
	}
