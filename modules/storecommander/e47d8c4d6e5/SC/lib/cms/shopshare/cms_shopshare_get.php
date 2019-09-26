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
	$idList=Tools::getValue('idlist',0);
	$idLang=(int)Tools::getValue('id_lang');
	$cntCMS=count(explode(',',$idList));
	$used=array();

	function getCmsShops()
	{
		global $idList,$idLang,$used, $cntCMS;
		
		$multiple = false;
		if(strpos($idList, ",") !== false)
			$multiple = true;

		$sql = "SELECT *
					FROM "._DB_PREFIX_."shop
					WHERE deleted != '1'
					ORDER BY id_shop_group ASC, name ASC";
		$res = Db::getInstance()->ExecuteS($sql);
		
		if(!$multiple)
		{
			foreach($res as $shop)
			{
				$sql2 ="SELECT cs.id_cms, c.active
						FROM "._DB_PREFIX_."cms c
						LEFT JOIN "._DB_PREFIX_."cms_shop cs ON (cs.id_cms = c.id_cms)
						WHERE c.id_cms IN (".psql($idList).")
						AND cs.id_shop = '".(int)$shop['id_shop']."'";
				$res2 = Db::getInstance()->getRow($sql2);
				if(!empty($res2["id_cms"]))
				{
					$used[$shop['id_shop']][0] = 1;
				}
			}
		}
		else
		{
			$sql3 ="SELECT id_shop
					FROM "._DB_PREFIX_."cms_shop
					WHERE id_cms IN (".psql($idList).")";
			$res3 = Db::getInstance()->executeS($sql3);
			
			foreach($res as $shop)
			{
				$used[$shop['id_shop']] = array(0,0,"DDDDDD");
				$nb_present = 0;
				
				$sql2 ="SELECT cs.id_cms, c.active
						FROM "._DB_PREFIX_."cms c
						LEFT JOIN "._DB_PREFIX_."cms_shop cs ON (cs.id_cms = c.id_cms)
						WHERE c.id_cms IN (".psql($idList).")
						AND cs.id_shop = '".(int)$shop['id_shop']."'";
				$res2 = Db::getInstance()->ExecuteS($sql2);
				foreach($res2 as $cms)
				{
					if(!empty($cms["id_cms"]))
					{
						$nb_present++;
					}
				}

				if($nb_present==$cntCMS)
				{
					$used[$shop['id_shop']][0] = 1;
					$used[$shop['id_shop']][2] = "7777AA";
				}
				elseif($nb_present<$cntCMS && $nb_present>0)
				{
					$used[$shop['id_shop']][2] = "777777";
				}
			}
		}
		
		foreach($res as $row){
			echo "<row id=\"".$row['id_shop']."\">";
			echo 		"<cell><![CDATA[".$row['id_shop']."]]></cell>";
			echo 		"<cell><![CDATA[".$row['name']."]]></cell>";
			echo 		"<cell style=\"background-color:".((!empty($used[$row['id_shop']][1]))?"#".$used[$row['id_shop']][1]:"")."\">".((!empty($used[$row['id_shop']][0]))?"1":"0")."</cell>";
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
	echo '<userdata name="uisettings">'.uisettings::getSetting('cms_shopshare').'</userdata>'."\n";
	getCmsShops();
?>
</rows>
