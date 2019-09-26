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
	$cntCategories=count(explode(',',$idlist));
	$used=array();

	function getRows()
	{
		global $idlist,$id_lang,$used, $cntCategories,$sc_agent;
		
		$multiple = false;
		if(strpos($idlist, ",") !== false)
			$multiple = true;

		$sql = "SELECT s.*
					FROM "._DB_PREFIX_."shop s
					".((!empty($sc_agent->id_employee))?" INNER JOIN "._DB_PREFIX_."employee_shop es ON (es.id_shop = s.id_shop AND es.id_employee = '".(int)$sc_agent->id_employee."') ":"")."
					WHERE
							s.deleted != '1'
					ORDER BY s.id_shop_group ASC, s.name ASC";
		$res = Db::getInstance()->ExecuteS($sql);
		
		if(!$multiple)
		{
			$category = new Category((int)$idlist);
			foreach($res as $shop)
			{
				$is_default = 0;
				if($category->id_shop_default == $shop['id_shop'])
					$is_default = 1;
					
				$used[$shop['id_shop']] = array(0,0, "", "", $is_default, "");
				
				$sql2 ="SELECT id_category
					FROM "._DB_PREFIX_."category_shop
					WHERE id_category IN (".psql($idlist).")
						AND id_shop = '".$shop['id_shop']."'";
				$res2 = Db::getInstance()->getRow($sql2);
				if(!empty($res2["id_category"]))
				{
					$used[$shop['id_shop']][0] = 1;
				}
			}
		}
		else
		{
			$sql3 ="SELECT id_shop_default
					FROM "._DB_PREFIX_."category
					WHERE id_category IN (".psql($idlist).")";
			$res3 = Db::getInstance()->ExecuteS($sql3);
			
			foreach($res as $shop)
			{
				$used[$shop['id_shop']] = array(0,0, "DDDDDD", "DDDDDD", 0, "DDDDDD");
				$nb_present = 0;
				$nb_active= 0;
				$nb_default= 0;
				
				$sql2 ="SELECT id_category
					FROM "._DB_PREFIX_."category_shop
					WHERE id_category IN (".psql($idlist).")
						AND id_shop = '".$shop['id_shop']."'";
				$res2 = Db::getInstance()->ExecuteS($sql2);
				foreach($res2 as $category)
				{
					if(!empty($category["id_category"]))
					{
						$nb_present++;
					}
				}
				
				foreach($res3 as $category)
				{
					if(!empty($category["id_shop_default"]) && $category["id_shop_default"] == $shop['id_shop'])
						$nb_default++;
				}

				if($nb_present==$cntCategories)
				{
					$used[$shop['id_shop']][0] = 1;
					$used[$shop['id_shop']][2] = "7777AA";
				}
				elseif($nb_present<$cntCategories && $nb_present>0)
				{
					$used[$shop['id_shop']][2] = "777777";
				}
				if($nb_active==$cntCategories)
				{
					$used[$shop['id_shop']][1] = 1;
					$used[$shop['id_shop']][3] = "7777AA";
				}
				elseif($nb_active<$cntCategories && $nb_active>0)
				{
					$used[$shop['id_shop']][3] = "777777";
				}
				if($nb_default==$cntCategories)
				{
					$used[$shop['id_shop']][4] = 1;
					$used[$shop['id_shop']][5] = "7777AA";
				}
				elseif($nb_default<$cntCategories && $nb_default>0)
				{
					$used[$shop['id_shop']][5] = "777777";
				}
			}
		}
		
		foreach($res as $row){
			echo "<row id=\"".$row['id_shop']."\">";
			echo 		"<cell><![CDATA[".$row['name']."]]></cell>";
			echo 		"<cell style=\"background-color:".((!empty($used[$row['id_shop']][5]))?"#".$used[$row['id_shop']][5]:"")."\">".$used[$row['id_shop']][4]."</cell>";
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
<column id="id" width="200" type="ro" align="left" sort="str"><?php echo _l('Shop')?></column>
<column id="is_default" width="80" type="ra" align="center" sort="str"><?php echo _l('Default')?></column>
<column id="present" width="80" type="ch" align="center" sort="int"><?php echo _l('Present')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_prop_shopshare_grid').'</userdata>'."\n";
	if(!empty($idlist))
		getRows();
	//echo '</rows>';
?>
</rows>