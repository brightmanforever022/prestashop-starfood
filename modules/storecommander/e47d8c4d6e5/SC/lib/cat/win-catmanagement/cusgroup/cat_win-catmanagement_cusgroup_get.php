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

		$sql = "SELECT g.*, gl.name
					FROM `"._DB_PREFIX_."group` g
					INNER JOIN "._DB_PREFIX_."group_lang gl ON (gl.id_group = g.id_group AND gl.id_lang = '".(int)$id_lang."') 
					ORDER BY gl.name ASC";
		$res = Db::getInstance()->ExecuteS($sql);
		
		if(!$multiple)
		{
			$category = new Category((int)$idlist);
			foreach($res as $row)
			{
				$used[$row['id_group']] = array(0,0, "", "");
				
				$sql2 ="SELECT id_category
					FROM "._DB_PREFIX_."category_group
					WHERE id_category IN (".psql($idlist).")
						AND id_group = '".$row['id_group']."'";
				$res2 = Db::getInstance()->getRow($sql2);
				if(!empty($res2["id_category"]))
				{
					$used[$row['id_group']][0] = 1;
				}
			}
		}
		else
		{
			foreach($res as $row)
			{
				$used[$row['id_group']] = array(0,0, "DDDDDD", "DDDDDD");
				$nb_present = 0;
				$nb_active= 0;
				
				$sql2 ="SELECT id_category
					FROM "._DB_PREFIX_."category_group
					WHERE id_category IN (".psql($idlist).")
						AND id_group = '".$row['id_group']."'";
				$res2 = Db::getInstance()->ExecuteS($sql2);
				foreach($res2 as $category)
				{
					if(!empty($category["id_category"]))
					{
						$nb_present++;
					}
				}

				if($nb_present==$cntCategories)
				{
					$used[$row['id_group']][0] = 1;
					$used[$row['id_group']][2] = "7777AA";
				}
				elseif($nb_present<$cntCategories && $nb_present>0)
				{
					$used[$row['id_group']][2] = "777777";
				}
				if($nb_active==$cntCategories)
				{
					$used[$row['id_group']][1] = 1;
					$used[$row['id_group']][3] = "7777AA";
				}
				elseif($nb_active<$cntCategories && $nb_active>0)
				{
					$used[$row['id_group']][3] = "777777";
				}
			}
		}
		
		foreach($res as $row){
			echo "<row id=\"".$row['id_group']."\">";
			echo 		"<cell><![CDATA[".$row['name']."]]></cell>";
			echo 		"<cell style=\"background-color:".((!empty($used[$row['id_group']][2]))?"#".$used[$row['id_group']][2]:"")."\">".((!empty($used[$row['id_group']][0]))?"1":"0")."</cell>";
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
<call command="attachHeader"><param><![CDATA[#select_filter,#select_filter]]></param></call>
</beforeInit>
<column id="id" width="200" type="ro" align="left" sort="str"><?php echo _l('Group')?></column>
<column id="present" width="80" type="ch" align="center" sort="int"><?php echo _l('Present')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_prop_cusgroup_grid').'</userdata>'."\n";
	if(!empty($idlist))
		getRows();
	//echo '</rows>';
?>
</rows>