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
//ini_set("display_errors", "ON");

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
	$product = new Product((int)$id, false, (int)$id_lang);
	if(SCI::usesAdvancedStockManagement($id) && StockAvailable::dependsOnStock($id, (SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():$product->id_shop_default)))
	{
		if(!empty($idlist))
			$idlist .= ",";
		$idlist .= $id;
	}
}
$cntProducts=count(explode(',',$idlist));

function getStocks()
{
	global $idlist,$multiple,$id_lang,$used, $cntProducts, $sc_agent;

	$return = "";

	if(empty($idlist))
		return false;

	$product_combis = array();

	/*$sql ="SELECT *
        FROM "._DB_PREFIX_."stock
        WHERE id_product IN (".psql($idlist).")
        ORDER BY id_product, id_product_attribute, id_warehouse";*/
	$sql ="SELECT wpl.*
			FROM "._DB_PREFIX_."warehouse_product_location wpl
				INNER JOIN "._DB_PREFIX_."warehouse w ON (w.id_warehouse = wpl.id_warehouse)
			WHERE wpl.id_product IN (".psql($idlist).")
				AND w.deleted=0
			GROUP BY wpl.id_product, wpl.id_product_attribute, wpl.id_warehouse
			ORDER BY wpl.id_product, wpl.id_product_attribute, wpl.id_warehouse";
	if(SCMS && SCI::getSelectedShop() && !empty($sc_agent->id_employee))
	{
		$sql ="SELECT wpl.*
				FROM "._DB_PREFIX_."warehouse_product_location wpl
					INNER JOIN "._DB_PREFIX_."warehouse w ON (w.id_warehouse = wpl.id_warehouse)
					INNER JOIN "._DB_PREFIX_."warehouse_shop ws ON (ws.id_warehouse = wpl.id_warehouse)
						INNER JOIN "._DB_PREFIX_."employee_shop es ON (es.id_shop = ws.id_shop AND es.id_employee = '".(int)$sc_agent->id_employee."') 
				WHERE wpl.id_product IN (".psql($idlist).")
					AND w.deleted=0
				GROUP BY wpl.id_product, wpl.id_product_attribute, wpl.id_warehouse
				ORDER BY wpl.id_product, wpl.id_product_attribute, wpl.id_warehouse";
	}
	$res = Db::getInstance()->executeS($sql);
	foreach($res as $warehouseProductLocation)
	{
		$name = "";

		$product = new Product((int)$warehouseProductLocation['id_product'], false, (int)$id_lang, (int)SCI::getSelectedShop());
		$name .= $product->name;

		if(!isset($product_combis[$product->id]))
			$product_combis[$product->id] = count(Product::getProductAttributesIds((int)$product->id));

		if(
			!empty($warehouseProductLocation['id_product_attribute'])
			||
			(empty($warehouseProductLocation['id_product_attribute']) && $product_combis[$product->id]==0)
		)
		{

			if(!empty($warehouseProductLocation['id_product_attribute']))
			{
				$sql_attr ="SELECT agl.name as gp, al.name
						FROM "._DB_PREFIX_."product_attribute_combination pac
							INNER JOIN "._DB_PREFIX_."attribute a ON pac.id_attribute = a.id_attribute
								INNER JOIN "._DB_PREFIX_."attribute_group_lang agl ON a.id_attribute_group = agl.id_attribute_group
							INNER JOIN "._DB_PREFIX_."attribute_lang al ON pac.id_attribute = al.id_attribute
						WHERE pac.id_product_attribute = '".$warehouseProductLocation['id_product_attribute']."'
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

			$warehouse = new Warehouse((int)$warehouseProductLocation['id_warehouse']);


			$sql ="SELECT *
				FROM "._DB_PREFIX_."stock
				WHERE id_product = '".$warehouseProductLocation['id_product']."'
					AND id_product_attribute = '".$warehouseProductLocation['id_product_attribute']."'
					AND id_warehouse = '".$warehouseProductLocation['id_warehouse']."'";
			$stock = Db::getInstance()->getRow($sql);
			if(!empty($stock["id_stock"]))
			{
				$valuation = number_format($stock['price_te'] * $stock['physical_quantity'],2, ".", "");
				$price_te = number_format($stock['price_te'],2, ".", "");
				$ean = $stock['ean13'];
				$upc = $stock['upc'];
				$ref = $stock['reference'];
				$phy = $stock['physical_quantity'];
				$use = $stock['usable_quantity'];
				if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
					$isbn = $stock['isbn'];
			}
			else
			{
				$valuation = "";
				$price_te = "";
				$ean = "";
				$upc = "";
				$isbn = "";
				$ref = "";
				$phy = 0;
				$use = 0;
				$real = 0;
			}

			$real = SCI::getProductRealQuantities((int)$warehouseProductLocation['id_product'],
				(int)$warehouseProductLocation['id_product_attribute'],
				(int)$warehouseProductLocation['id_warehouse'],
				true);
			if(empty($real)/* || $real<=0*/)
				$real = 0;

			$return .= "<row id=\"".$warehouseProductLocation['id_product']."_".$warehouseProductLocation['id_product_attribute']."_".$warehouseProductLocation['id_warehouse']."\">";
			$return .=		"<cell><![CDATA[".$warehouse->name."]]></cell>";
			$return .=		"<cell>".$warehouseProductLocation['id_product']."</cell>";
			$return .=		"<cell>".$warehouseProductLocation['id_product_attribute']."</cell>";
			$return .=		"<cell><![CDATA[".$name."]]></cell>";
			$return .=		"<cell><![CDATA[".$ean."]]></cell>";
			$return .=		"<cell><![CDATA[".$upc."]]></cell>";
			if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
			{
				$return .=		"<cell><![CDATA[".$isbn."]]></cell>";
			}
			$return .=		"<cell><![CDATA[".$ref."]]></cell>";
			$return .=		"<cell><![CDATA[".$warehouseProductLocation['location']."]]></cell>";
			$return .=		"<cell>".$phy."</cell>";
			$return .=		"<cell>".$use."</cell>";
			$return .=		"<cell>".$real."</cell>";
			$return .=		"<cell>".$valuation."</cell>";
			$return .=		"<cell>".$price_te."</cell>";
			$return .= "</row>";
		}
	}

	return $return;
}

if(stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")){
	header("Content-type: application/xhtml+xml");
}else{
	header("Content-type: text/xml");
}
echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
?>
<rows>
	<?php if(!empty($idlist)) {
		$stocks = getStocks();
		if(!empty($stocks)) { ?>
			<head>

				<beforeInit>
					<call command="attachHeader"><param><![CDATA[#select_filter,#numeric_filter,#numeric_filter,#text_filter,#text_filter,#text_filter<?php if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) { ?>,#text_filter<?php }?>,#text_filter,#text_filter,#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter]]></param></call>
				</beforeInit>
				<afterInit>
					<call command="attachFooter"><param><![CDATA[,,,,,<?php if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) { ?>,<?php }?>,,,#stat_total,#stat_total,#stat_total,,]]></param></call>
				</afterInit>
				<column id="warehouse" width="60" type="ro" align="left" sort="str"><?php echo _l('Warehouse')?></column>
				<column id="id_product" width="40" type="ro" align="left" sort="int"><?php echo _l('Product ID')?></column>
				<column id="id_product_attribute" width="40" type="ro" align="left" sort="int"><?php echo _l('Attr. ID')?></column>
				<column id="name" width="160" type="ro" align="left" sort="str"><?php echo _l('Name')?></column>
				<column id="ean" width="60" type="ro" align="center" sort="str"><?php echo _l('EAN13')?></column>
				<column id="upc" width="60" type="ro" align="center" sort="str"><?php echo _l('UPC')?></column>
				<?php if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) { ?>
					<column id="isbn" width="60" type="ro" align="center" sort="str"><?php echo _l('ISBN')?></column>
				<?php } ?>
				<column id="reference" width="60" type="ro" align="center" sort="str"><?php echo _l('Ref.')?></column>
				<column id="location" width="100" type="ro" align="left" sort="str"><?php echo _l('Location')?></column>
				<column id="quantity_physical" width="60" type="ro" align="right" sort="int"><?php echo _l('Physical stock')?></column>
				<column id="quantity_usable" width="60" type="ro" align="right" sort="int"><?php echo _l('Available stock')?></column>
				<column id="quantity_real" width="60" type="ro" align="right" sort="int"><?php echo _l('Live stock')?></column>
				<column id="valuation" width="60" type="ro" align="right" sort="int"><?php echo _l('Valuation')?></column>
				<column id="price_te" width="60" type="ro" align="right" format="0.00" sort="int"><?php echo _l('Wholesale price')?></column>
			</head>
			<?php
			echo '<userdata name="uisettings">'.uisettings::getSetting('cat_warehousestock').'</userdata>'."\n";
			echo $stocks;
			?>
		<?php } else {?>
			<head>
				<beforeInit>
					<call command="attachHeader"><param><![CDATA[#text_filter]]></param></call>
				</beforeInit>
				<column id="temp" width="*" type="ro" align="center" sort="str"><?php echo _l('Advanced stocks')?></column>
			</head>
			<?php
			if($multiple)
				$message = _l('The products are associated with no warehouse');
			else
				$message = _l('The product is associated with no warehouse');
			?>
			<row id="warehouseshare_msg">
				<cell><![CDATA[<?php echo $message ?>]]></cell>
			</row>
		<?php }
	}
	else { ?>
		<head>
			<beforeInit>
				<call command="attachHeader"><param><![CDATA[#text_filter]]></param></call>
			</beforeInit>
			<column id="temp" width="*" type="ro" align="center" sort="str"><?php echo _l('Advanced stocks')?></column>
		</head>
		<?php
		if($empty_list)
			$message = _l('You should select products');
		elseif($multiple)
			$message = _l('The selected products have no Advanced Stock Management or have Manual mgmt activated');
		else
			$message = _l('The selected product do not have the Advanced Stock Management option activated or have Manual mgmt activated');
		?>
		<row id="warehouseshare_msg">
			<cell><![CDATA[<?php echo $message ?>]]></cell>
		</row>
	<?php } ?>
</rows>