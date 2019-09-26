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
	$cntManufacturers=count(explode(',',$idlist));
	$used=array();

	function getShopsManufacturers()
	{
		global $idlist,$id_lang,$used, $cntManufacturers;
		
		$multiple = false;
		if(strpos($idlist, ",") !== false)
			$multiple = true;

		$sql = "SELECT *
					FROM "._DB_PREFIX_."shop
					WHERE deleted != '1'
					ORDER BY id_shop_group ASC, name ASC";
		$res = Db::getInstance()->ExecuteS($sql);
		
		if(!$multiple)
		{
			$manufacturer = new Manufacturer((int)$idlist);
			foreach($res as $shop)
			{
				
				$sql2 ="SELECT id_manufacturer
					FROM "._DB_PREFIX_."manufacturer_shop
					WHERE id_manufacturer IN (".psql($idlist).")
						AND id_shop = '".$shop['id_shop']."'";
				$res2 = Db::getInstance()->getRow($sql2);
				if(!empty($res2["id_manufacturer"]))
				{
					$used[$shop['id_shop']][0] = 1;
				}
			}
		}
		else
		{
			foreach($res as $shop)
			{
				$used[$shop['id_shop']] = array(0,0, "DDDDDD", "DDDDDD", 0, "DDDDDD");
				$nb_present = 0;

				$sql2 ="SELECT id_manufacturer
					FROM "._DB_PREFIX_."manufacturer_shop
					WHERE id_manufacturer IN (".psql($idlist).")
						AND id_shop = '".$shop['id_shop']."'";
				$res2 = Db::getInstance()->ExecuteS($sql2);
				foreach($res2 as $manufacturer)
				{
					if(!empty($manufacturer["id_manufacturer"]))
					{
						$nb_present++;
					}
				}

				if($nb_present==$cntManufacturers)
				{
					$used[$shop['id_shop']][0] = 1;
					$used[$shop['id_shop']][2] = "7777AA";
				}
				elseif($nb_present<$cntManufacturers && $nb_present>0)
				{
					$used[$shop['id_shop']][2] = "777777";
				}
			}
		}
		
		foreach($res as $row){
			echo "<row id=\"".$row['id_shop']."\">";
			echo 		"<cell><![CDATA[".$row['id_shop']."]]></cell>";
			echo 		"<cell><![CDATA[".$row['name']."]]></cell>";
			echo 		"<cell style=\"background-color:".((!empty($used[$row['id_shop']][2]))?"#".$used[$row['id_shop']][2]:"")."\">".((!empty($used[$row['id_shop']][0]))?"1":"0")."</cell>";

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
<call command="attachHeader"><param><![CDATA[#select_filter,#select_filter,,]]></param></call>
</beforeInit>
<column id="id" width="80" type="ro" align="right" sort="str"><?php echo _l('ID')?></column>
<column id="shop" width="200" type="ro" align="left" sort="str"><?php echo _l('Shop')?></column>
<column id="present" width="80" type="ch" align="center" sort="int"><?php echo _l('Present')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('man_manufacturer_langshopshare').'</userdata>'."\n";
	getShopsManufacturers();
	//echo '</rows>';
?>
</rows>
