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
	$idlist = Tools::getValue('idlist','0');
	$id_lang = intval(Tools::getValue('id_lang'));
	$cntProducts = count(explode(',',$idlist));
	$used = array();

	$multiple = false;
	if(strpos($idlist, ",") !== false)
		$multiple = true;

	$sql = "SELECT *
				FROM "._DB_PREFIX_."shop
				WHERE
					deleted != '1'
				ORDER BY id_shop_group ASC, name ASC";
	$res = Db::getInstance()->ExecuteS($sql);
	
	if(!$multiple)
	{
		$product = new Product((int)$idlist);
		foreach($res as $shop)
		{
			$is_default = 0;
			if($product->id_shop_default == $shop['id_shop'])
				$is_default = 1;
				
			$used[$shop['id_shop']] = array(0,0, $is_default);
			$sql2 ="SELECT id_product, active
				FROM "._DB_PREFIX_."product_shop
				WHERE id_product IN (".psql($idlist).")
					AND id_shop = '".$shop['id_shop']."'";
			$res2 = Db::getInstance()->getRow($sql2);
			if(!empty($res2["id_product"]))
			{
				$used[$shop['id_shop']][0] = 1;
				
				if($res2['active']==1)
					$used[$shop['id_shop']][1] = 1;
			}
		}
	}
	else
	{
		$sql3 ="SELECT id_shop_default
				FROM "._DB_PREFIX_."product
				WHERE id_product IN (".psql($idlist).")";
		$res3 = Db::getInstance()->ExecuteS($sql3);
			
		foreach($res as $shop)
		{
			$used[$shop['id_shop']] = array(0,0, 0);
			$nb_present = 0;
			$nb_active= 0;
			$nb_default= 0;
			
			$sql2 ="SELECT id_product, active
				FROM "._DB_PREFIX_."product_shop
				WHERE id_product IN (".psql($idlist).")
					AND id_shop = '".$shop['id_shop']."'";
			$res2 = Db::getInstance()->ExecuteS($sql2);
			foreach($res2 as $product)
			{
				if(!empty($product["id_product"]))
				{
					$nb_present++;
						
					if($product['active']==1)
						$nb_active++;
				}
			}
				
			foreach($res3 as $product)
			{
				if(!empty($product["id_shop_default"]) && $product["id_shop_default"] == $shop['id_shop'])
					$nb_default++;
			}

			if($nb_present==$cntProducts)
				$used[$shop['id_shop']][0] = 1;
			if($nb_active==$cntProducts)
				$used[$shop['id_shop']][1] = 1;
			if($nb_default==$cntProducts)
				$used[$shop['id_shop']][2] = 1;
		}
	}
	
	$i= 0;
	foreach($used as $id_shop=>$values)
	{
		if($i>0)
			echo ";";
		echo $id_shop.",".$values[0].",".$values[1].",".$values[2];
		$i++;
	}