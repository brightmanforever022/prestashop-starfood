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
	$forceAllProducts=intval(Tools::getValue('forceAllProducts',0));
	$cntProducts=count(explode(',',$idlist));
	$accessory_filter=intval(Tools::getValue('accessory_filter',0));
	$id_category=Tools::getValue('id_category',0);
	$used=array();

	function getAccessories()
	{
		global $idlist,$id_lang,$forceAllProducts,$used,$accessory_filter,$id_category;
		if($forceAllProducts==0)
		{
			$sql = "SELECT a.id_product_2,p.reference,pl.name,p.active,s.name AS supName,m.name AS manName, p.id_category_default ".((SCMS)?", p.id_shop_default, ps.id_category_default":"")."
					FROM "._DB_PREFIX_."accessory a
					LEFT JOIN "._DB_PREFIX_."product p ON (a.id_product_2=p.id_product)
					LEFT JOIN "._DB_PREFIX_."product_lang pl ON (a.id_product_2=pl.id_product AND pl.id_lang=".intval($id_lang)." ".(SCMS?(SCI::getSelectedShop()>0?' AND pl.id_shop='.(int)SCI::getSelectedShop():' AND pl.id_shop=p.id_shop_default '):'').")
					".((SCMS)?"INNER JOIN "._DB_PREFIX_."product_shop ps ON (p.id_product=ps.id_product AND ps.id_shop=".(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').")":"")."
					".(SCMS && (!empty($sc_agent->id_employee))?" INNER JOIN "._DB_PREFIX_."employee_shop es ON (es.id_shop = ps.id_shop AND es.id_employee = '".(int)$sc_agent->id_employee."') ":"")."
					LEFT JOIN "._DB_PREFIX_."supplier s ON (p.id_supplier=s.id_supplier)
					LEFT JOIN "._DB_PREFIX_."supplier_lang sl ON (s.id_supplier=sl.id_supplier AND sl.id_lang=".intval($id_lang).")
					LEFT JOIN "._DB_PREFIX_."manufacturer m ON (p.id_manufacturer=m.id_manufacturer)
					LEFT JOIN "._DB_PREFIX_."manufacturer_lang ml ON (m.id_manufacturer=ml.id_manufacturer AND ml.id_lang=".intval($id_lang).")
					".(($accessory_filter)?"LEFT JOIN "._DB_PREFIX_."category_product cp ON (a.id_product_1=cp.id_product)
							WHERE cp.id_category =".intval($id_category):"")."
					GROUP BY a.id_product_2
					ORDER BY pl.name ASC";
			$res = Db::getInstance()->ExecuteS($sql);
			$sql2 ="SELECT DISTINCT a.id_product_2
					FROM "._DB_PREFIX_."accessory a
					WHERE a.id_product_1 IN (".psql($idlist).")";
			$res2 = Db::getInstance()->ExecuteS($sql2);
			foreach($res2 as $row2){
				$used[$row2['id_product_2']]=1;
			}
		}else{
			$sql = "SELECT p.id_product AS id_product_2,p.reference,pl.name,p.active,s.name AS supName,m.name AS manName, p.id_category_default ".((SCMS)?", p.id_shop_default, ps.id_category_default":"")."
					FROM "._DB_PREFIX_."product p
					LEFT JOIN "._DB_PREFIX_."product_lang pl ON (p.id_product=pl.id_product AND pl.id_lang=".intval($id_lang)." ".(SCMS?(SCI::getSelectedShop()>0?' AND pl.id_shop='.(int)SCI::getSelectedShop():' AND pl.id_shop=p.id_shop_default '):'').")
					".((SCMS)?"INNER JOIN "._DB_PREFIX_."product_shop ps ON (p.id_product=ps.id_product AND ps.id_shop=".(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').")":"")."
					".(SCMS && (!empty($sc_agent->id_employee))?" INNER JOIN "._DB_PREFIX_."employee_shop es ON (es.id_shop = ps.id_shop AND es.id_employee = '".(int)$sc_agent->id_employee."') ":"")."
					LEFT JOIN "._DB_PREFIX_."supplier s ON (p.id_supplier=s.id_supplier)
					LEFT JOIN "._DB_PREFIX_."supplier_lang sl ON (s.id_supplier=sl.id_supplier AND sl.id_lang=".intval($id_lang).")
					LEFT JOIN "._DB_PREFIX_."manufacturer m ON (p.id_manufacturer=m.id_manufacturer)
					LEFT JOIN "._DB_PREFIX_."manufacturer_lang ml ON (m.id_manufacturer=ml.id_manufacturer AND ml.id_lang=".intval($id_lang).")
					".(($accessory_filter)?"LEFT JOIN "._DB_PREFIX_."category_product cp ON (p.id_product=cp.id_product)
							WHERE cp.id_category =".intval($id_category):"")."
					GROUP BY id_product_2
					ORDER BY pl.name ASC";
			$res = Db::getInstance()->ExecuteS($sql);
			$sql2 ="SELECT DISTINCT a.id_product_2
					FROM "._DB_PREFIX_."accessory a
					WHERE a.id_product_1 IN (".psql($idlist).")";
			$res2 = Db::getInstance()->ExecuteS($sql2);
			foreach($res2 as $row2){
				$used[$row2['id_product_2']]=1;
			}
		}
		foreach($res as $row){
			echo "<row id=\"".$row['id_product_2']."\">";
			echo		'<userdata name="id_category_default">'.$row['id_category_default'].'</userdata>';
			if(SCMS)
				echo 	'<userdata name="id_shop_default">'.$row['id_shop_default'].'</userdata>';
			echo 		"<cell>".$row['id_product_2']."</cell>";
			echo 		"<cell>".(sc_array_key_exists($row['id_product_2'],$used)?1:0)."</cell>";
			echo 		"<cell><![CDATA[".$row['active']."]]></cell>";
			echo 		"<cell><![CDATA[".$row['reference']."]]></cell>";
			echo 		"<cell style=\"color:".($row['active']?'#000000':'#888888')."\"><![CDATA[".$row['name']."]]></cell>";
			echo 		"<cell><![CDATA[".$row['supName']."]]></cell>";
			echo 		"<cell><![CDATA[".$row['manName']."]]></cell>";
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
<call command="attachHeader"><param><![CDATA[#text_filter,,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter]]></param></call>
</beforeInit>
<column id="id" width="50" type="ro" align="right" sort="int"><?php echo _l('ID')?></column>
<column id="used" width="50" type="ch" align="center" sort="int"><?php echo _l('Used')?></column>
<column id="active" width="45" type="coro" align="center" sort="int"><?php echo _l('Active')?>
	<option value="0"><?php echo _l('No')?></option>
	<option value="1"><?php echo _l('Yes')?></option>
</column>
<column id="reference" width="120" type="ro" align="left" sort="str"><?php echo _l('Reference')?></column>
<column id="name" width="200" type="ro" align="left" sort="str"><?php echo _l('Name')?></column>
<column id="supplier" width="120" type="ro" align="left" sort="str"><?php echo _l('Supplier')?></column>
<column id="manufacturer" width="200" type="ro" align="left" sort="str"><?php echo _l('Manufacturer')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_accessory').'</userdata>'."\n";
	getAccessories();
	//echo '</rows>';
?>
</rows>