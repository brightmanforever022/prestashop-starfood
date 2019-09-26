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

	$idlist=Tools::getValue('idlist',0);
	$id_lang=intval(Tools::getValue('id_lang'));
	$id_product=intval(Tools::getValue('id_product'));
	$used=array();
	$empty_list = false;
	
	if(empty($idlist_temp))
		$empty_list = true;
		
	$multiple = false;
	if(strpos($idlist, ",") !== false)
		$multiple = true;
	
	$cntCombis=count(explode(',',$idlist));
	
	function getshops()
	{
		global $idlist,$multiple,$id_lang,$used, $cntCombis,$id_product;
		
		if(empty($idlist))
			return false;

		$shop = (int)SCI::getSelectedShop();
		if($shop == 0)
			$shop = null;
		
		$sql_shop ="SELECT ps.id_shop, s.name
			FROM "._DB_PREFIX_."product_shop ps
				INNER JOIN  "._DB_PREFIX_."shop s ON (ps.id_shop = s.id_shop)
			WHERE ps.id_product = '".(int)$id_product."'
			GROUP BY ps.id_shop
			ORDER BY s.name";
		$shops = Db::getInstance()->ExecuteS($sql_shop);

		$product = new Product($id_product);
		
		//$used[$id_shop] = array("présent","couleur_present");
		
		if(!$multiple)
		{
			foreach($shops as $shop)
			{					
				$used[$shop['id_shop']] = array(0,"");

				$sql_in_shop ="SELECT id_product_attribute
					FROM "._DB_PREFIX_."product_attribute_shop
					WHERE id_product_attribute = '".(int)$idlist."'
						AND  id_shop = '".(int)$shop["id_shop"]."'";
				$in_shop = Db::getInstance()->ExecuteS($sql_in_shop);
				if(!empty($in_shop[0]["id_product_attribute"]))
				{
					$used[$shop['id_shop']][0] = 1;
				}	
			}
		}
		else
		{
			foreach($shops as $shop)
			{
				$used[$shop['id_shop']] = array(0,"DDDDDD");
				$nb_present = 0;
				
				$sql2 ="SELECT *
					FROM "._DB_PREFIX_."product_attribute_shop
					WHERE id_product_attribute IN (".psql($idlist).")
						AND id_shop = '".(int)$shop['id_shop']."'";
				$res2 = Db::getInstance()->ExecuteS($sql2);
				foreach($res2 as $combination)
				{
					if(!empty($combination["id_product_attribute"]))
					{
						$nb_present++;
					}
				}

				if($nb_present==$cntCombis)
				{
					$used[$shop['id_shop']][0] = 1;
					$used[$shop['id_shop']][1] = "7777AA";
				}
				elseif($nb_present<$cntCombis && $nb_present>0)
				{
					$used[$shop['id_shop']][1] = "777777";
				}
			}
		}
		
		foreach($shops as $row){
			echo "<row id=\"".$row['id_shop']."\">";
			echo 		'<userdata name="disabled">'.($product->id_shop_default==$row['id_shop']?"1":"0").'</userdata>'."\n";
			echo 		"<cell><![CDATA[".$row['name']."]]></cell>";
			echo 		"<cell style=\"background-color:".((!empty($used[$row['id_shop']][1]))?"#".$used[$row['id_shop']][1]:"")."\">".$used[$row['id_shop']][0]."</cell>";
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
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#select_filter_strict,#select_filter]]></param></call>
</beforeInit>
<column id="id" width="200" type="ro" align="left" sort="str"><?php echo _l('Shop')?></column>
<column id="present" width="80" type="ch" align="center" sort="int"><?php echo _l('Present')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_combination_shopshare').'</userdata>'."\n";
	getshops();
	//echo '</rows>';
?>
</rows>