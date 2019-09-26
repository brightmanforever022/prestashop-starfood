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

	$id_product=(int)Tools::getValue('id_product',0);
	$id_product_attribute=(int)Tools::getValue('id_product_attribute',0);
	$id_warehouse=(int)Tools::getValue('id_warehouse',0);
	$id_lang=intval(Tools::getValue('id_lang'));
	
	$idlist = "";
	$idlist_temp=Tools::getValue('idlist',0);
	
	$multiple = false;
	if(strpos($idlist_temp, ",") !== false)
		$multiple = true;
	
	$exps = explode(",", $idlist_temp);
	foreach ($exps as $id)
	{
		$product = new Product((int)$id, false, (int)$id_lang);
		if(SCI::usesAdvancedStockManagement($id) && StockAvailable::dependsOnStock($id, (SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():$product->id_shop_default)))
		{
			if(!empty($idlist))
				$idlist .= ",";
			$idlist .= $id;
		}
	}
	
	$cntProducts=count(explode(',',$idlist));
	
	function getMovements()
	{
		global $idlist, $id_product, $id_product_attribute, $id_warehouse,$multiple,$id_lang,$used, $cntProducts;
		
		if(empty($idlist))
			return false;

		$product_combis = array();

		$where = " s.id_product IN (".psql($idlist).")";
		if(!empty($id_product) && !empty($id_warehouse))
		{
			$where = " s.id_product = '".$id_product."' AND s.id_warehouse = '".$id_warehouse."' ";
			if(!empty($id_product_attribute))
				$where .= " AND s.id_product_attribute = '".$id_product_attribute."' ";
			else
				$where .= " AND s.id_product_attribute = '0' ";
		}
		
		$sql ="SELECT s.*,sm.*
			FROM "._DB_PREFIX_."stock_mvt sm
				INNER JOIN "._DB_PREFIX_."stock s ON (sm.id_stock = s.id_stock)
			WHERE ".$where."
			ORDER BY sm.date_add DESC";
		$res = Db::getInstance()->executeS($sql);
		foreach($res as $mvt)
		{
			$name = "";
			
			$product = new Product((int)$mvt['id_product'], false, (int)$id_lang, (int)SCI::getSelectedShop());
			$name .= $product->name;
			
			if(!isset($product_combis[$product->id]))
				$product_combis[$product->id] = count(Product::getProductAttributesIds((int)$product->id));

			if(
					!empty($mvt['id_product_attribute'])
					||
					(empty($mvt['id_product_attribute']) && $product_combis[$product->id]==0)
			)
			{
				if(!empty($mvt['id_product_attribute']))
				{
					$sql_attr ="SELECT agl.name as gp, al.name
						FROM "._DB_PREFIX_."product_attribute_combination pac
							INNER JOIN "._DB_PREFIX_."attribute a ON pac.id_attribute = a.id_attribute
								INNER JOIN "._DB_PREFIX_."attribute_group_lang agl ON a.id_attribute_group = agl.id_attribute_group
							INNER JOIN "._DB_PREFIX_."attribute_lang al ON pac.id_attribute = al.id_attribute
						WHERE pac.id_product_attribute = '".$mvt['id_product_attribute']."'
							AND agl.id_lang = '".$id_lang."'
							AND al.id_lang = '".$id_lang."'
						GROUP BY a.id_attribute
						ORDER BY agl.name";
					$res_attr = Db::getInstance()->executeS($sql_attr);
					foreach($res_attr as $attr)
					{
						if(!empty($attr["gp"]) && !empty($attr["name"]))
						{
							if(!empty($name))
								$name .= ", ";
							$name .= $attr["gp"]." : ".$attr["name"];
						}
					}
				}
				
				$warehouse = new Warehouse((int)$mvt['id_warehouse']);
				
				$warehouseProductLocation = WarehouseProductLocation::getIdByProductAndWarehouse((int)$mvt['id_product'], (int)$mvt['id_product_attribute'], (int)$mvt['id_warehouse']);
				if(!empty($warehouseProductLocation))
				{
					$signe = "";
					if($mvt["sign"]>0)
						$signe = '<img src="lib/img/arrow_up.png" alt="" />';
					elseif($mvt["sign"]<0)
						$signe = '<img src="lib/img/arrow_down.png" alt="" />';		
					
					$reason = new StockMvtReason($mvt['id_stock_mvt_reason'], $id_lang);
					
					$employee = new Employee($mvt['id_employee']);
					
					echo "<row id=\"".$mvt['id_stock_mvt']."\">";
					echo		"<cell><![CDATA[".$warehouse->name."]]></cell>";
					echo		"<cell>".$mvt['id_product']."</cell>";
					echo		"<cell>".$mvt['id_product_attribute']."</cell>";
					echo		"<cell><![CDATA[".$name."]]></cell>";
					echo		"<cell><![CDATA[".$mvt['reference']."]]></cell>";
					echo		"<cell><![CDATA[".$signe."]]></cell>";
					echo		"<cell>".$mvt['physical_quantity']."</cell>";
					echo		"<cell>".number_format($mvt['price_te'],2, ".", "")."</cell>";
					echo		"<cell><![CDATA[".$mvt['date_add']."]]></cell>";
					echo		"<cell>".$mvt['id_order']."</cell>";
					echo		"<cell>".$mvt['id_supply_order']."</cell>";
					echo		"<cell><![CDATA[".$reason->name."]]></cell>";
					echo		"<cell><![CDATA[".$employee->firstname." ".$employee->lastname."]]></cell>";
					echo "</row>";
				}
			}
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
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#select_filter,#numeric_filter,#numeric_filter,#text_filter,#text_filter,,#numeric_filter,#numeric_filter,#text_filter,#numeric_filter,#numeric_filter,#select_filter,#select_filter]]></param></call>
</beforeInit>
<column id="warehouse" width="60" type="ro" align="left" sort="str"><?php echo _l('Warehouse')?></column>
<column id="id_product" width="40" type="ro" align="left" sort="int"><?php echo _l('Product ID')?></column>
<column id="id_product_attribute" width="40" type="ro" align="left" sort="int"><?php echo _l('Attr. ID')?></column>
<column id="name" width="160" type="ro" align="left" sort="str"><?php echo _l('Name')?></column>
<column id="reference" width="60" type="ro" align="center" sort="str"><?php echo _l('Ref.')?></column>
<column id="action" width="40" type="ro" align="center" sort="int"><?php echo _l('Action')?></column>
<column id="quantity" width="60" type="ro" align="right" sort="int"><?php echo _l('Qty')?></column>
<column id="price_te" width="60" type="ro" align="right" format="0.00" sort="int"><?php echo _l('Wholesale price')?></column>
<column id="date" width="120" type="ro" align="center" sort="str"><?php echo _l('Date')?></column>
<column id="id_order" width="80" type="ro" align="left" sort="int"><?php echo _l('Order ID')?></column>
<column id="id_supply_order" width="80" type="ro" align="left" sort="int"><?php echo _l('Supply order ID')?></column>
<column id="reason" width="140" type="ro" align="left" sort="str"><?php echo _l('Reason')?></column>
<column id="employee" width="100" type="ro" align="right" sort="str"><?php echo _l('Employee')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_warehousestock_movement').'</userdata>'."\n";
	getMovements();
	//echo '</rows>';
?>
</rows>