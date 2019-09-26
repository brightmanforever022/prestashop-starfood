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

	$id_lang=intval(Tools::getValue('id_lang'));
	$id_group=intval(Tools::getValue('id_group'));
	$for_mb = Tools::getValue('for_mb', 1);

	function getLevelFromDB($parent_id,$FF_parent=0)
	{
		global $id_lang, $for_mb, $shops,$FF_id,$FF_cat_archived;

		if(SCMS && SCI::getSelectedShop()>0 && $for_mb==1)
		{
			$sql = "SELECT c.active,c.id_category,name FROM "._DB_PREFIX_."category c
						LEFT JOIN "._DB_PREFIX_."category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang=".(int)$id_lang." AND cl.id_shop=".(int)SCI::getSelectedShop().")
						LEFT JOIN "._DB_PREFIX_."category_shop cs ON cs.id_category=c.id_category
						WHERE c.id_parent=".(int)$parent_id."
							 AND cs.id_shop=".(int)SCI::getSelectedShop()."
						GROUP BY c.id_category
						ORDER BY cs.position, cl.name";
		}
		elseif(version_compare(_PS_VERSION_, '1.3.0.0', '>='))
		{
			$sql = "SELECT c.active,c.id_category,name FROM "._DB_PREFIX_."category c
						LEFT JOIN "._DB_PREFIX_."category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang=".(int)$id_lang.")
						WHERE c.id_parent=".(int)$parent_id."
						GROUP BY c.id_category
						ORDER BY cl.name";
		}
		else
		{
			$sql = "SELECT c.active,c.id_category,name FROM "._DB_PREFIX_."category c
						LEFT JOIN "._DB_PREFIX_."category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang=".(int)$id_lang.")
						WHERE c.id_parent=".(int)$parent_id."
						GROUP BY c.id_category
						ORDER BY cl.name";
		}
		$res=Db::getInstance()->ExecuteS($sql);
		if(!empty($res))
		{
			foreach($res as $k => $row)
			{
				$style='';
				if (hideCategoryPosition($row['name'])=='')
				{
					$sql2 = "SELECT name FROM "._DB_PREFIX_."category_lang 
										WHERE id_lang=".intval(Configuration::get('PS_LANG_DEFAULT'))." 
											AND id_category=".$row['id_category'];
					$res2=Db::getInstance()->getRow($sql2);
					$style='style="background:lightblue" ';
				}
				$icon=($row['active']?'catalog.png':'folder_grey.png');

				$is_FF = 0;
				$in_FF = 0;
				if($FF_id==$row['id_category'])
				{
					$icon='foulefactory_icon.png';
					$is_FF = 1;
					$in_FF = 1;
				}
				else
				{
					if(!empty($FF_parent))
					{
						$in_FF = 1;
					}
				}

				if (sc_in_array(hideCategoryPosition($row['name']),array('SC Recycle Bin', 'SC Corbeille'),"catCategorypanel_corbeille"))
					$icon='folder_delete.png';
				/*if (!in_array(hideCategoryPosition($row['name']),array('SC Recycle Bin', 'SC Corbeille')))
				{*/
					echo "<row ".($style!='' ? $style:'').
					" id=\"".$row['id_category']."\"".($parent_id==0?' open="1"':'').">".
					"<cell>".$row['id_category']."</cell>".
					"<cell ".($is_FF?"type=\"ro\"":"").">".($in_FF?"":"0")."</cell>".
					"<cell image=\"../../".$icon."\"><![CDATA[".($style==''?formatText(hideCategoryPosition($row['name'])):_l('To Translate:').' '.formatText(hideCategoryPosition($res2['name'])))."]]></cell>";
					if (SCMS)
					{
						foreach ($shops as $idS=>$nameS)
						{
							echo "<cell ".($in_FF?"type=\"ro\"":"").">".($in_FF?"":"3")."</cell>";
						}
					}else
					{
						echo "<cell ".($in_FF?"type=\"ro\"":"").">".($in_FF?"":"3")."</cell>";
					}
					getLevelFromDB($row['id_category'],$in_FF);
					echo '</row>'."\n";
				//}
			}
		}
	}
	
	function getLevelFromDB_PHP($id_parent,$FF_parent=0)
	{
		global $id_lang,$for_mb,$array_cats,$array_children_cats,$shops ,$FF_id,$FF_cat_archived;
		
		if(!empty($array_children_cats[$id_parent]))
		{
			ksort($array_children_cats[$id_parent]);
			foreach($array_children_cats[$id_parent] as $k => $id)
			{
				$row = $array_cats[$id];
				$style='';
				if (hideCategoryPosition($row['name'])=='SoColissimo')
					continue;
				if (hideCategoryPosition($row['name'])=='')
				{
					$sql2 = "SELECT name FROM "._DB_PREFIX_."category_lang
										WHERE id_lang=".intval(Configuration::get('PS_LANG_DEFAULT'))."
											AND id_category=".$row['id_category'];
					$res2=Db::getInstance()->getRow($sql2);
					$style='style="background:lightblue" ';
				}
				

				$icon=($row['active']?'catalog.png':'folder_grey.png');

				$is_FF = 0;
				$in_FF = 0;
				if($FF_id==$row['id_category'])
				{
					$icon='foulefactory_icon.png';
					$is_FF = 1;
					$in_FF = 1;
				}
				else
				{
					if(!empty($FF_parent))
					{
						$in_FF = 1;
					}
				}
                $not_associate_FF = 0;
                if(!empty($in_FF))
                {
                    require_once("lib/php/foulefactory/FFApi.php");
                    require_once("lib/php/foulefactory/FfProject.php");
                    if(!empty($is_FF) || $FF_cat_archived==$row['id_category'])
                        $not_associate_FF = 1;
                    else
                    {
                        $ff_project = FfProject::getByIdCategory((int)$row['id_category']);
                        if(!empty($ff_project->id))
                        {
                            $good_status = array("created","configured","to_pay");
                            if(!in_array($ff_project->status, $good_status))
                                $not_associate_FF = 1;
                        }
                    }
                }

				if (sc_in_array(hideCategoryPosition($row['name']),array('SC Recycle Bin', 'SC Corbeille'),"catCategorypanel_corbeille"))
					$icon='folder_delete.png';

				/*if (!in_array(hideCategoryPosition($row['name']),array('SC Recycle Bin', 'SC Corbeille')))
				{*/

				echo "<row ".($style!='' ? $style:'').
				" id=\"".$row['id_category']."\"".($row["id_parent"]==0?' open="1"':'').">".
				"<cell>".$row['id_category']."</cell>".
                ' 	<userdata name="not_associate_FF">'.$not_associate_FF.'</userdata>'.
					"<cell ".($not_associate_FF?"type=\"ro\"":"").">".($in_FF?"":"0")."</cell>".
				"<cell image=\"../../".$icon."\"><![CDATA[".($style==''?formatText(hideCategoryPosition($row['name'])):_l('To Translate:').' '.formatText(hideCategoryPosition($res2['name'])))."]]></cell>";
				if (SCMS)
				{
					foreach ($shops as $idS=>$nameS)
					{
						echo "<cell ".($in_FF?"type=\"ro\"":"").">".($in_FF?"":"3")."</cell>";
					}
				}else
				{
					echo "<cell ".($in_FF?"type=\"ro\"":"").">".($in_FF?"":"3")."</cell>";
				}
				getLevelFromDB_PHP($row['id_category'],$in_FF);
				echo '</row>'."\n";
			}
		}
	}

$shops = array();
if(SCMS)
{

		$sql ="SELECT s.id_shop, s.name
				FROM "._DB_PREFIX_."shop s
					INNER JOIN "._DB_PREFIX_."product_shop ps ON ps.id_shop = s.id_shop
					".((!empty($sc_agent->id_employee))?" INNER JOIN "._DB_PREFIX_."employee_shop es ON (es.id_shop = s.id_shop AND es.id_employee = '".(int)$sc_agent->id_employee."') ":"")."
				WHERE s.deleted!='1'
				GROUP BY s.id_shop
				ORDER BY s.name";

	$res = Db::getInstance()->executeS($sql);
	foreach($res as $shop)
		$shops[ $shop["id_shop"] ] = str_replace("&", _l("and"), $shop["name"])." (#".$shop["id_shop"].")";
}

	//XML HEADER

	//include XML Header (as response will be in xml format)
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
?>
<rows parent="0">
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#text_filter,,#text_filter]]></param></call>
</beforeInit>
<column id="id_category" width="40" type="ro" align="right" sort="int"><?php echo _l('ID')?></column>
<column id="used" width="50" type="ch" align="center" sort="str"><?php echo _l('Used')?></column>
<column id="name" width="250" type="tree" align="left" sort="str"><?php echo _l('Name')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_categorypanel').'</userdata>'."\n";
	if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
		echo "<row ".
						" id=\"1\">".
						"<cell>1</cell>".
						"<cell>0</cell>".
						"<cell image=\"catalog.png\"><![CDATA["._l('Home')."]]></cell>".
						"<cell>0</cell></row>";
	$id_root=0;
	$ps_root = 0;//SCI::getConfigurationValue("PS_ROOT_CATEGORY");
	if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		$sql_root = "SELECT *
				FROM " . _DB_PREFIX_ . "category
				WHERE id_parent = 0";
		$res_root = Db::getInstance()->ExecuteS($sql_root);
		if (!empty($res_root[0]["id_category"]))
			$ps_root = $res_root[0]["id_category"];
	}
	if(!empty($ps_root))
		$id_root = $ps_root;
	$id_shop=SCI::getSelectedShop();
	if (SCMS && $id_shop > 0)
	{
		$shop = new Shop($id_shop);
		$categ = new Category($shop->id_category);
		$id_root = $categ->id_parent;
	}

    $FF_id = -1;
    $FF_parent = 0;
    $FF_cat_archived = 0;
    if(_s('APP_FOULEFACTORY') && SCI::getFFActive())
    {
        $FF_cat_archived = SCI::getConfigurationValue("SC_FOULEFACTORY_CATEGORYARCHIVED");
        $FF_id = SCI::getConfigurationValue("SC_FOULEFACTORY_CATEGORY");
    }

	if(version_compare(_PS_VERSION_, '1.4.0.0', '>='))
	{
		$array_cats = array();
		$array_children_cats = array();
	
		if(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !SCMS)
		{
			$id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');
		}
		
		$sql = "SELECT c.*, cl.name, c.position ".(( (SCMS && $for_mb==1) || (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !SCMS) ) && !empty($id_shop)?", cs.position":"")."
				FROM "._DB_PREFIX_."category c
				LEFT JOIN "._DB_PREFIX_."category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang=".intval($id_lang).")
				".(( (SCMS && $for_mb==1) || (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !SCMS) ) && !empty($id_shop)?" INNER JOIN "._DB_PREFIX_."category_shop cs ON (cs.id_category=c.id_category AND cs.id_shop=".(int)$id_shop.") ":"")."
				GROUP BY c.id_category
				ORDER BY c.`nleft` ASC";
		$res=Db::getInstance()->ExecuteS($sql);
		foreach($res as $k => $row)
		{
			$array_cats[$row["id_category"]]=$row;
				
			if(!isset($array_children_cats[$row["id_parent"]]))
				$array_children_cats[$row["id_parent"]] = array();
			$array_children_cats[$row["id_parent"]][str_pad($row["position"], 5, "0", STR_PAD_LEFT).str_pad($row["id_category"], 12, "0", STR_PAD_LEFT)] = $row["id_category"];
		}

		getLevelFromDB_PHP($id_root);
	}
	else
		getLevelFromDB($id_root);
?>
</rows>
