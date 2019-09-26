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

	$idlist_temp=Tools::getValue('idlist',0);
	$id_lang=intval(Tools::getValue('id_lang'));
	$with_combi=0;//Tools::getValue('with_combi',0);
	$used=array();
	$idlist = "";
	$empty_list = false;
	$has_combi = false;
	$not_activated = false;
	
	if(empty($idlist_temp))
		$empty_list = true;
		
	$multiple = false;
	if(strpos($idlist_temp, ",") !== false)
		$multiple = true;
	
	$CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE = _s('CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE');
	if(empty($CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE))
		$CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE = "0";
	
	$exps = explode(",", $idlist_temp);
	foreach ($exps as $id)
	{
		$combis = Product::getProductAttributesIds($id);
		if(
			(
				($with_combi==0 && count($combis)==0) // NA PAS DE DECLINAISONS
				||
				$with_combi==1
			)
			&& 
			(
				SCI::usesAdvancedStockManagement($id) // STOCK AVANCEE ACTIVEE
				||
				(
					$CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE!="0" // MODIF DEFAUT
					&&
					!SCI::usesAdvancedStockManagement($id) // STOCK AVANCEE DESACTIVEE
				)
			) 
		)
		{
			if(!empty($idlist))
				$idlist .= ",";
			$idlist .= $id;
		}
		else
		{
			if(
				($with_combi==0 && count($combis)>0 ) // NA PAS DE DECLINAISONS
			)
				$has_combi = true;
			
			if($CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE=="0" // MODIF DEFAUT
				&&
				!SCI::usesAdvancedStockManagement($id) // STOCK AVANCEE DESACTIVEE
			)
				$not_activated = true;
		}
	}
	//echo intval(($with_combi==0 && count($combis)==0))." || ".intval($with_combi==1)." => ".intval(($with_combi==0 && count($combis)==0)||($with_combi==1));
	$cntProducts=0;
	if(!empty($idlist))
		$cntProducts=count(explode(',',$idlist));
	
	if(!$multiple)
		$product_inst = new Product((int)$idlist);
	
	function getWarehouses()
	{
		global $idlist,$multiple,$id_lang,$used, $cntProducts, $product_inst;
		
		if(empty($idlist))
			return false;

		$shop = (int)SCI::getSelectedShop();
		if($shop == 0)
			$shop = null;
		
		$warehouses = Warehouse::getWarehouses(false, $shop);
		
		//$used[$id_warehouse] = array("présent","couleur_present","quantité","emplacement");
		
		if(!$multiple)
		{
			$product = new $product_inst;
			foreach($warehouses as $warehouse)
			{					
				$used[$warehouse['id_warehouse']] = array(0,"", "", "",0);
				
				$check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$idlist, 0, (int)$warehouse['id_warehouse']);
				if(!empty($check_in_warehouse) && SCI::usesAdvancedStockManagement((int)$idlist))
				{
					$used[$warehouse['id_warehouse']][0] = 1;

					$warehouse_product = new WarehouseProductLocation((int)$check_in_warehouse);
					if($warehouse_product->location!="")
					{
						$used[$warehouse['id_warehouse']][3] = $warehouse_product->location;
					}	
					$query = new DbQuery();
					$query->select('SUM(usable_quantity) as usable_quantity');
					$query->from('stock');
					$query->where('id_product = '.(int)$idlist.'');
					$query->where('id_warehouse = '.(int)$warehouse['id_warehouse'].'');
					$avanced_quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
					if(!empty($avanced_quantities["usable_quantity"]))
						$used[$warehouse['id_warehouse']][2] = $avanced_quantities["usable_quantity"];
					
					$used[$warehouse['id_warehouse']][4] = SCI::getProductRealQuantities((int)$idlist,
							null,
							(int)$warehouse['id_warehouse'],
							true,
							$product->hasAttributes());
				}
			}
		}
		else
		{
			foreach($warehouses as $warehouse)
			{
				$used[$warehouse['id_warehouse']] = array(0,"DDDDDD", 0, "",0);
				$nb_present = 0;
				
				$sql2 ="SELECT *
					FROM "._DB_PREFIX_."warehouse_product_location
					WHERE id_product IN (".psql($idlist).")
						AND id_warehouse = '".(int)$warehouse['id_warehouse']."'
						AND id_product_attribute = 0";
				$res2 = Db::getInstance()->ExecuteS($sql2);
				foreach($res2 as $product)
				{
					if(!empty($product["id_product"]) && SCI::usesAdvancedStockManagement($product["id_product"]))
					{
						$nb_present++;

						$product_inst = new Product((int)$product["id_product"]);
						if(StockAvailable::dependsOnStock((int)$product["id_product"], (SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():$product_inst->id_shop_default)))
						{
							$query = new DbQuery();
							$query->select('SUM(usable_quantity) as usable_quantity');
							$query->from('stock');
							$query->where('id_product = "'.intval($product["id_product"]).'"');
							$query->where('id_warehouse = '.(int)$warehouse['id_warehouse'].'');
							$avanced_quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
							if(!empty($avanced_quantities["usable_quantity"]))
								$used[$warehouse['id_warehouse']][2] += $avanced_quantities["usable_quantity"];
							
							$used[$warehouse['id_warehouse']][4] += SCI::getProductRealQuantities((int)$product["id_product"],
									null,
									(int)$warehouse['id_warehouse'],
									true,
									$product_inst->hasAttributes());
						}
					}
				}

				if($nb_present==$cntProducts)
				{
					$used[$warehouse['id_warehouse']][0] = 1;
					$used[$warehouse['id_warehouse']][1] = "7777AA";
				}
				elseif($nb_present<$cntProducts && $nb_present>0)
				{
					$used[$warehouse['id_warehouse']][1] = "777777";
				}
			}
		}
		
		foreach($warehouses as $row){
			echo "<row id=\"".$row['id_warehouse']."\">";
			echo 		"<cell><![CDATA[".$row['name']."]]></cell>";
			echo 		"<cell style=\"background-color:".((!empty($used[$row['id_warehouse']][1]))?"#".$used[$row['id_warehouse']][1]:"")."\">".$used[$row['id_warehouse']][0]."</cell>";
			if($multiple || (!$multiple && StockAvailable::dependsOnStock((int)$idlist, (SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():$product_inst->id_shop_default))))
			{
				echo 		"<cell>".((!empty($used[$row['id_warehouse']][2]))?$used[$row['id_warehouse']][2]:"0")."</cell>";
				echo 		"<cell>".((!empty($used[$row['id_warehouse']][4]))?$used[$row['id_warehouse']][4]:"0")."</cell>";
			}
			if(!$multiple)
				echo 		"<cell>".((!empty($used[$row['id_warehouse']][3]))?$used[$row['id_warehouse']][3]:"")."</cell>";
			echo "</row>";
		}
	}

	if(stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")){
	 		header("Content-type: application/xhtml+xml");
	}else{
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
?>
<rows>
<?php if(!empty($idlist)) { ?>
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#select_filter,#select_filter,#numeric_filter,#numeric_filter<?php if(!$multiple) { ?>,#text_filter<?php } ?>]]></param></call>
</beforeInit>
<column id="id" width="200" type="ro" align="left" sort="str"><?php echo _l('Warehouse')?></column>
<column id="present" width="80" type="ch" align="center" sort="int"><?php echo _l('Present')?></column>
<?php if($multiple || (!$multiple && StockAvailable::dependsOnStock((int)$idlist, (SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():$product_inst->id_shop_default)))) { ?>
<column id="quantity" width="100" type="ro" align="center" sort="int"><?php echo _l('Available stock')?></column>
<column id="real_quantity" width="100" type="ro" align="center" sort="int"><?php echo _l('Live stock')?></column>
<?php }
 if(!$multiple) { ?>
<column id="location" width="100" type="ed" align="left" sort="str"><?php echo _l('Location')?></column>
<?php } ?>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_warehouseshare').'</userdata>'."\n";
	if($has_combi)
		echo '<userdata name="has_combi">1</userdata>';
	else
		echo '<userdata name="has_combi">0</userdata>';
	if($not_activated)
		echo '<userdata name="not_activated">1</userdata>';
	else
		echo '<userdata name="not_activated">0</userdata>';

	getWarehouses();
	//echo '</rows>';
}
else { ?>
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#text_filter]]></param></call>
</beforeInit>
<column id="temp" width="*" type="ro" align="center" sort="str"><?php echo _l('Warehouses')?></column>
</head>
<?php 
if($empty_list)
	$message = _l('You should select products');
elseif($CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE=="0")
{
	if($multiple)
		$message = _l('The selected products have no Advanced Stock Management activated nor possess combinations');
	else
		$message = _l('The selected product do not have the Advanced Stock Management option activated nor possess combinations');
}
else
{
	if($multiple)
		$message = _l('The selected products possess combinations');
	else
		$message = _l('The selected product possess combinations');
}
?>
<row id="warehouseshare_msg">
	<cell><![CDATA[<?php echo $message ?>]]></cell>
</row>
<?php } ?>
</rows>