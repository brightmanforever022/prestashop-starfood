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
	$used=array();
	$idlist = "";
	$empty_list = false;
	
	if(empty($idlist_temp))
		$empty_list = true;
		
	$multiple = false;
	if(strpos($idlist_temp, ",") !== false)
		$multiple = true;
	
	$exps = explode(",", $idlist_temp);
	foreach ($exps as $id)
	{
		$c = new Combination((int)$id);
		if(SCI::usesAdvancedStockManagement($c->id_product))
		{
			if(!empty($idlist))
				$idlist .= ",";
			$idlist .= $id;
		}
	}
	$cntCombis=count(explode(',',$idlist));

	$id_product = 0;
	if(!$multiple)
	{
		$combination = new Combination((int)$idlist);
		$id_product = $combination->id_product;
	}
	
	function getWarehouses()
	{
		global $idlist,$multiple,$id_lang,$used, $cntCombis,$id_product;
		
		if(empty($idlist))
			return false;

		$shop = (int)SCI::getSelectedShop();
		if($shop == 0)
			$shop = null;
		
		$warehouses = Warehouse::getWarehouses(false, $shop);
		
		
		//$used[$id_warehouse] = array("présent","couleur_present","quantité","emplacement");
		
		if(!$multiple)
		{
			$combination = new Combination((int)$idlist);
			foreach($warehouses as $warehouse)
			{					
				$used[$warehouse['id_warehouse']] = array(0,"", 0, "", 0);
				
				$check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$combination->id_product, (int)$idlist, (int)$warehouse['id_warehouse']);
				if(!empty($check_in_warehouse))
				{
					$used[$warehouse['id_warehouse']][0] = 1;

					$warehouse_combination = new WarehouseProductLocation((int)$check_in_warehouse);
					if($warehouse_combination->location!="")
					{
						$used[$warehouse['id_warehouse']][3] = $warehouse_combination->location;
					}
					
					$query = new DbQuery();
					$query->select('SUM(usable_quantity) as usable_quantity');
					$query->from('stock');
					$query->where('id_product = '.(int)$combination->id_product.'');
					$query->where('id_product_attribute = '.(int)$idlist.'');
					$query->where('id_warehouse = '.(int)$warehouse['id_warehouse'].'');
					$avanced_quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
					if(!empty($avanced_quantities["usable_quantity"]))
						$used[$warehouse['id_warehouse']][2] = $avanced_quantities["usable_quantity"];	

					$used[$warehouse['id_warehouse']][4] = SCI::getProductRealQuantities((int)$combination->id_product,
							(int)$combination->id,
							(int)$warehouse['id_warehouse'],
							true,
							true);
				}
			}
		}
		else
		{
			foreach($warehouses as $warehouse)
			{
				$used[$warehouse['id_warehouse']] = array(0,"DDDDDD", 0, "", 0);
				$nb_present = 0;
				
				$sql2 ="SELECT *
					FROM "._DB_PREFIX_."warehouse_product_location
					WHERE id_product_attribute IN (".psql($idlist).")
						AND id_warehouse = '".(int)$warehouse['id_warehouse']."'";
				$res2 = Db::getInstance()->ExecuteS($sql2);
				foreach($res2 as $combination)
				{
					if(!empty($combination["id_product_attribute"]))
					{
						$nb_present++;

						if(StockAvailable::dependsOnStock((int)$combination["id_product"], (int)SCI::getSelectedShop()))
						{
							$query = new DbQuery();
							$query->select('SUM(usable_quantity) as usable_quantity');
							$query->from('stock');
							$query->where('id_product_attribute = "'.(int)$combination["id_product_attribute"].'"');
							$query->where('id_warehouse = '.(int)$warehouse['id_warehouse'].'');
							$avanced_quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
							if(!empty($avanced_quantities["usable_quantity"]))
								$used[$warehouse['id_warehouse']][2] += $avanced_quantities["usable_quantity"];
							
							$used[$warehouse['id_warehouse']][4] += SCI::getProductRealQuantities((int)$combination["id_product"],
									$combination["id_product_attribute"],
									(int)$warehouse['id_warehouse'],
									true,
									true);
						}
					}
				}

				if($nb_present==$cntCombis)
				{
					$used[$warehouse['id_warehouse']][0] = 1;
					$used[$warehouse['id_warehouse']][1] = "7777AA";
				}
				elseif($nb_present<$cntCombis && $nb_present>0)
				{
					$used[$warehouse['id_warehouse']][1] = "777777";
				}
			}
		}
		
		foreach($warehouses as $row){
			echo "<row id=\"".$row['id_warehouse']."\">";
			echo 		"<cell><![CDATA[".$row['name']."]]></cell>";
			echo 		"<cell style=\"background-color:".((!empty($used[$row['id_warehouse']][1]))?"#".$used[$row['id_warehouse']][1]:"")."\"><![CDATA[".$used[$row['id_warehouse']][0]."]]></cell>";
			if($multiple || (!$multiple && StockAvailable::dependsOnStock((int)$id_product, (int)SCI::getSelectedShop())))
			{
				echo 		"<cell><![CDATA[".((!empty($used[$row['id_warehouse']][2]))?$used[$row['id_warehouse']][2]:"0")."]]></cell>";
				echo 		"<cell><![CDATA[".((!empty($used[$row['id_warehouse']][4]))?$used[$row['id_warehouse']][4]:"0")."]]></cell>";
			}
			if(!$multiple)
				echo 		"<cell><![CDATA[".((!empty($used[$row['id_warehouse']][3]))?$used[$row['id_warehouse']][3]:"")."]]></cell>";
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
<?php if($multiple || (!$multiple && StockAvailable::dependsOnStock((int)$id_product, (int)SCI::getSelectedShop()))) { ?>
<column id="quantity" width="100" type="ro" align="center" sort="int"><?php echo _l('Available stock')?></column>
<column id="real_quantity" width="100" type="ro" align="center" sort="int"><?php echo _l('Live stock')?></column>
<?php }
 if(!$multiple) { ?>
<column id="location" width="100" type="ed" align="left" sort="str"><?php echo _l('Location')?></column>
<?php } ?>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_combination_warehouseshare').'</userdata>'."\n";
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
	$message = _l('You should select combinations');
elseif($multiple)
	$message = _l('The selected combinations do not have the Advanced Stock Management option activated');
else
	$message = _l('The selected combination do not have the Advanced Stock Management option activated');
?>
<row id="warehouseshare_msg">
	<cell><![CDATA[<?php echo $message ?>]]></cell>
</row>
<?php } ?>
</rows>