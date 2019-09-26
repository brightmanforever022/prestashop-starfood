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
		
	$multiple = false;
	if(strpos($idlist, ",") !== false)
		$multiple = true;
	
	$cntProductAttrs=0;
	if(!empty($idlist))
		$cntProductAttrs=count(explode(',',$idlist));
	
	function getSuppliers()
	{
		global $idlist,$multiple,$id_lang,$id_product,$used, $cntProductAttrs;
		
		if(empty($idlist))
			return false;

		$shop = (int)SCI::getSelectedShop();
		if($shop == 0)
			$shop = null;
		
		$query = new DbQuery();
		$query->select('s.*, sl.`description`');
		$query->from('supplier', 's');
		$query->leftJoin('supplier_lang', 'sl', 's.`id_supplier` = sl.`id_supplier` AND sl.`id_lang` = '.(int)$id_lang);
		$query->leftJoin('supplier_shop', 'ss', 's.`id_supplier` = ss.`id_supplier` AND ss.`id_shop` = '.(int)$shop);
		$query->orderBy(' s.`name` ASC');
		$query->groupBy('s.id_supplier');
		
		$suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
		
		//$used[$id_supplier] = array("présent","couleur_present","ref","price","currency","default","couleur_default");
		
		if(!$multiple)
		{
			$product = new Product((int)$idlist);
			foreach($suppliers as $supplier)
			{					
				$used[$supplier['id_supplier']] = array(0,"", "", "", "",0,"");
				
				$sql = '
					SELECT *
					FROM `'._DB_PREFIX_.'product_supplier` ps
					WHERE ps.`id_supplier` = "'.(int)$supplier['id_supplier'].'"
					AND ps.`id_product` = "'.(int)$id_product.'"
					AND ps.`id_product_attribute` = "'.(int)$idlist.'"';
				$check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
				if(!empty($check_in_supplier[0]["id_product_supplier"]))
				{
					$used[$supplier['id_supplier']][0] = 1;

					$used[$supplier['id_supplier']][2] = $check_in_supplier[0]["product_supplier_reference"];
					$used[$supplier['id_supplier']][3] = $check_in_supplier[0]["product_supplier_price_te"];
					$used[$supplier['id_supplier']][4] = $check_in_supplier[0]["id_currency"];
					
					if($product->id_supplier==$supplier['id_supplier'])
						$used[$supplier['id_supplier']][5] = 1;
				}
			}
		}
		else
		{
			foreach($suppliers as $supplier)
			{
				$used[$supplier['id_supplier']] = array(0,"DDDDDD", "", "", "");
				$nb_present = 0;
				$nb_default = 0;
				
				$sql2 ="SELECT DISTINCT(ps.id_product_supplier), ps.id_product, p.id_supplier
					FROM "._DB_PREFIX_."product_supplier ps
						INNER JOIN "._DB_PREFIX_."product p ON (p.id_product=ps.id_product)
					WHERE ps.id_product_attribute IN (".psql($idlist).")
						AND ps.`id_product` = '".(int)$id_product."'
						AND ps.id_supplier = '".(int)$supplier['id_supplier']."'";
				$res2 = Db::getInstance()->ExecuteS($sql2);
				foreach($res2 as $product)
				{
					if(!empty($product["id_product"]))
					{
						$nb_present++;
						if(!empty($product["id_supplier"]) && $product["id_supplier"]==$supplier['id_supplier'])
							$nb_default++;
					}
				}

				if($nb_present==$cntProductAttrs)
				{
					$used[$supplier['id_supplier']][0] = 1;
					$used[$supplier['id_supplier']][1] = "7777AA";
				}
				elseif($nb_present<$cntProductAttrs && $nb_present>0)
				{
					$used[$supplier['id_supplier']][1] = "777777";
				}

				if($nb_default==$cntProductAttrs)
				{
					$used[$supplier['id_supplier']][5] = 1;
					$used[$supplier['id_supplier']][6] = "7777AA";
				}
				elseif($nb_default<$cntProductAttrs && $nb_default>0)
				{
					$used[$supplier['id_supplier']][6] = "777777";
				}
			}
		}
		
		foreach($suppliers as $row){
			echo "<row id=\"".$row['id_supplier']."\">";
			echo 		"<cell><![CDATA[".$row['name']."]]></cell>";
			echo 		"<cell style=\"background-color:".((!empty($used[$row['id_supplier']][1]))?"#".$used[$row['id_supplier']][1]:"")."\">".$used[$row['id_supplier']][0]."</cell>";
			if(!$multiple)
			{
				echo 		"<cell>".((!empty($used[$row['id_supplier']][2]))?$used[$row['id_supplier']][2]:"")."</cell>";
				echo 		"<cell>".((!empty($used[$row['id_supplier']][3]))?$used[$row['id_supplier']][3]:"")."</cell>";
				echo 		"<cell>".((!empty($used[$row['id_supplier']][4]))?$used[$row['id_supplier']][4]:"")."</cell>";
			}
			//echo 		"<cell style=\"background-color:".((!empty($used[$row['id_supplier']][6]))?"#".$used[$row['id_supplier']][6]:"")."\">".$used[$row['id_supplier']][5]."</cell>";
			echo "</row>";
		}
	}
	
	$sql = 'SELECT id_currency,iso_code
					FROM '._DB_PREFIX_.'currency
					WHERE active=1
					ORDER BY iso_code';
	$res=Db::getInstance()->ExecuteS($sql);
	$currencies='';
	foreach ($res AS $currency)
		$currencies.='<option value="'.$currency['id_currency'].'">'.$currency['iso_code'].'</option>';
	
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
<call command="attachHeader"><param><![CDATA[#text_filter,#select_filter<?php if(!$multiple) { ?>,#text_filter,#text_filter,#select_filter<?php } ?>]]></param></call>
</beforeInit>
<column id="id" width="200" type="ro" align="left" sort="str"><?php echo _l('Supplier')?></column>
<column id="present" width="80" type="ch" align="center" sort="int"><?php echo _l('Present')?></column>
<?php if(!$multiple) { ?>
<column id="product_supplier_reference" width="100" type="ed" align="left" sort="str"><?php echo _l('Supplier reference')?></column>
<column id="product_supplier_price_te" width="100" type="ed" align="right" sort="int"><?php echo _l('Wholesale price')?></column>
<column id="id_currency" width="80" type="coro" align="right" sort="int"><?php echo _l('Currency'); echo $currencies; ?></column>
<?php }/* ?>
<column id="default" width="50" type="ra" align="center" sort="str"><?php echo _l('Default')?></column>
*/ ?>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_supplier').'</userdata>'."\n";
	
	getSuppliers();
	//echo '</rows>';
 ?>
</rows>