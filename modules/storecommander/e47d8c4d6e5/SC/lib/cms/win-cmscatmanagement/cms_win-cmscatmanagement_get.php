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
$id_lang=(int)Tools::getValue('id_lang');
$id_shop=(int)Tools::getValue('id_shop',0);

$root_cms_cat = array();
if(SCMS)
{
	$shops = Shop::getShops(false, null, true);
	$sql = 'SELECT id_cms_category
			FROM '._DB_PREFIX_.'cms_category
			WHERE id_parent = 0
			AND level_depth = '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && version_compare(_PS_VERSION_, '1.6.0.12', '<') ? 0 :1).'
			AND position = 0';
	$idCmsCategRoot=Db::getInstance()->getValue($sql);
	
	foreach ($shops as $shop_id)
	{
		$shopObjet = new Shop($shop_id);
		if(version_compare(_PS_VERSION_, '1.6.0.12', '>=')) {
			$root_cms_cat_temp = new CMSCategory($idCmsCategRoot, $id_lang, $shop_id);
		} else {
			$root_cms_cat_temp = new CMSCategory($idCmsCategRoot, $id_lang);
		}			
		$root_cms_cat[] = $root_cms_cat_temp->id;
	}
}

// parent_id => catégorie parente
// others : true => on veut toutes les catégories situées dans la catégorie parente
// id_start : si others=false, on ne veut que la catégorie id_start située dans la catégorie parente
function getLevelFromDB($parent_id,$others=true,$id_start=0, $is_bin=false)
{
	global $id_lang,$id_shop,$root_cms_cat,$user_lang_iso;

	if(!empty($parent_id) || $parent_id===0)
	{
		$where  = "";
		if(!$others && !empty($id_start))
		{
			$where .= " AND c.id_cms_category='".(int)$id_start."'";
		}
		
		if(version_compare(_PS_VERSION_, '1.6.0.12', '>=') && SCMS && !empty($id_shop) && !$is_bin)
		{
			$sql = "SELECT cl.*,c.*
				FROM "._DB_PREFIX_."cms_category c
					LEFT JOIN "._DB_PREFIX_."cms_category_lang cl ON (cl.id_cms_category=c.id_cms_category AND cl.id_lang=".(int)$id_lang." AND cl.id_shop=".(int)$id_shop.")
					INNER JOIN "._DB_PREFIX_."cms_category_shop cs ON (cs.id_cms_category=c.id_cms_category AND cs.id_shop=".(int)$id_shop.")
				WHERE c.id_parent=".(int)$parent_id."
					".$where."
				GROUP BY c.id_cms_category
				ORDER BY c.position";
		}
		else
		{
			$sql = "SELECT cl.*,c.*
				FROM "._DB_PREFIX_."cms_category c
					LEFT JOIN "._DB_PREFIX_."cms_category_lang cl ON (cl.id_cms_category=c.id_cms_category AND cl.id_lang=".(int)$id_lang.")
				WHERE c.id_parent=".(int)$parent_id."
					".$where."
				GROUP BY c.id_cms_category
				ORDER BY cl.position";
		}
		$res=Db::getInstance()->ExecuteS($sql);
		
		if(!empty($res))
		{
			foreach($res as $k => $row){
				if(!empty($row["id_cms_category"]))
				{
					$style='';
					
					if (hideCategoryPosition($row['name'])=='')
					{
						$sql2 = "SELECT name FROM "._DB_PREFIX_."cms_category_lang 
								WHERE id_lang=".(int)Configuration::get('PS_LANG_DEFAULT')." 
									AND id_cms_category=".(int)$row['id_cms_category'];
						$res2=Db::getInstance()->getRow($sql2);
						$style='background:lightblue';
					}
					
					$description = strip_tags($row['description']);
					
					if(SCMS)
					{
						$shops = "";
						if (version_compare(_PS_VERSION_, '1.6.0.12', '>='))
						{
							$sql_shop = "SELECT s.name
								FROM "._DB_PREFIX_."cms_category_shop cs
									INNER JOIN "._DB_PREFIX_."shop s ON (cs.id_shop=s.id_shop)
								WHERE cs.id_cms_category=".(int)$row['id_cms_category']."
								ORDER BY s.name";
							$res_shop=Db::getInstance()->executeS($sql_shop);
							foreach($res_shop as $shop)
							{
								if(!empty($shop["name"]))
								{
									if(!empty($shops))
										$shops .= ",";
									$shops .= $shop["name"];
								}
							}
						}
					}
					
					$nb_cms = 0;
					$sql_nb_cms = "SELECT id_cms
							FROM "._DB_PREFIX_."cms
							WHERE id_cms_category=".(int)$row['id_cms_category']."";
					$res_nb_cms=Db::getInstance()->executeS($sql_nb_cms);
					if(!empty($res_nb_cms))
						$nb_cms = count($res_nb_cms);

					$is_root = false;
					if($row["id_parent"]==0)
						$is_root = true;
					
					$is_home = false;
					if(SCMS && sc_in_array($row['id_cms_category'], $root_cms_cat,"cmsWinCmsCatManagGet_rootcatgetLevelFromDB"))
						$is_home = true;
						
					$not_deletable = false;
					if($is_home || $is_root)
						$not_deletable = true;
					
					$is_recycle_bin = false;
					if(hideCategoryPosition($row['name'])==_l('SC Recycle Bin') || hideCategoryPosition($row['name'])==('SC Recycle Bin'))
						$is_recycle_bin = true;

					$icon=($row['active']?'catalog.png':'folder_grey.png');

					if($is_recycle_bin)
					{
						$row['name']=_l('SC Recycle Bin');
						$icon='folder_delete.png';
					}

					if($is_home)
						$icon='folder_table.png';
					
					echo "<row style=\"".$style."\"".
										" id=\"".$row['id_cms_category']."\"".($parent_id==0?' open="1"':'').">".
										"<cell image=\"../../".$icon."\"><![CDATA[ ".($style==''?formatText(hideCategoryPosition($row['name'])):_l('To Translate:').' '.formatText(hideCategoryPosition($res2['name'])))."]]></cell>";
					if(SCMS) {
						echo "<cell><![CDATA[" . $shops . "]]></cell>";
					}
					echo				"<cell>".$row['id_cms_category']."</cell>"
										."<cell><![CDATA[".($style==''?formatText(hideCategoryPosition($row['name'])):_l('To Translate:').' '.formatText(hideCategoryPosition($res2['name'])))."]]></cell>"
										."<cell><![CDATA[".$description."]]></cell>"
										."<cell>".$nb_cms."</cell>"
										."<cell>".$row['active']."</cell>"
										."";

					echo '  	<userdata name="not_deletable">'.(int)$not_deletable.'</userdata>';
					echo '  	<userdata name="is_recycle_bin">'.(int)$is_recycle_bin.'</userdata>';
					echo '  	<userdata name="is_home">'.(int)$is_home.'</userdata>';
					echo '  	<userdata name="is_root">'.(int)$is_root.'</userdata>';
					
					getLevelFromDB($row['id_cms_category'],true,0, false);
					echo '</row>'."\n";
				}
			}
		}
	}
	
}

function getLevelFromDB_PHP($id_parent,$others=true,$id_start=0, $limit_to_shop=false)
{
	global $id_lang,$id_shop,$root_cms_cat,$array_cats_cms,$array_children_cats_cms;
	if(!empty($array_children_cats_cms[$id_parent]))
	{
		ksort($array_children_cats_cms[$id_parent]);

		foreach($array_children_cats_cms[$id_parent] as $k => $id)
		{
			$row = $array_cats_cms[$id];

			if(!$others && !empty($id_start) && $id_start!=$row['id_cms_category'])
				continue;
			if(empty($row['id_cms_category']))
				continue;

			$style='';
				
			if (hideCategoryPosition($row['name'])=='')
			{
				$sql2 = "SELECT name FROM "._DB_PREFIX_."cms_category_lang
								WHERE id_lang=".(int)Configuration::get('PS_LANG_DEFAULT')."
									AND id_cms_category=".(int)$row['id_cms_category'];
				$res2=Db::getInstance()->getRow($sql2);
				$style='background:lightblue';
			}
				
			$description = strip_tags($row['description']);
				
			if(SCMS)
			{
				$in_shop = false;
				$shops = "";
				if (version_compare(_PS_VERSION_, '1.6.0.12', '>='))
				{
					$sql_shop = "SELECT s.name, s.id_shop
								FROM "._DB_PREFIX_."cms_category_shop cs
									INNER JOIN "._DB_PREFIX_."shop s ON (cs.id_shop=s.id_shop)
								WHERE cs.id_cms_category=".(int)$row['id_cms_category']."
								ORDER BY s.name";
					$res_shop=Db::getInstance()->executeS($sql_shop);
					foreach($res_shop as $shop)
					{
						if(!empty($shop["name"]))
						{
							if(!empty($shops))
								$shops .= ",";
							$shops .= $shop["name"];
						}
						if(!empty($shop["id_shop"]) && !empty($id_shop) && $shop["id_shop"]==$id_shop)
							$in_shop = true;
					}
				}
				if(!$in_shop && !empty($limit_to_shop))
					continue;
			}
				
			$nb_cms = 0;
			$sql_nb_cms = "SELECT id_cms
							FROM "._DB_PREFIX_."cms
							WHERE id_cms_category=".(int)$row['id_cms_category']."";
			$res_nb_cms=Db::getInstance()->executeS($sql_nb_cms);
			
			if(!empty($res_nb_cms))
				$nb_cms = count($res_nb_cms);

			$is_root = false;
			if($row["id_parent"]==0)
				$is_root = true;
				
			$is_home = false;
			if(SCMS && sc_in_array($row['id_cms_category'], $root_cms_cat,"cmsWinCmsCatManagGet_rootcatgetLevelFromDB_PHP"))
				$is_home = true;
			
			$not_deletable = false;
			if($is_home || $is_root)
				$not_deletable = true;
				
			$is_recycle_bin = false;
			if(hideCategoryPosition($row['name'])==_l('SC Recycle Bin') || hideCategoryPosition($row['name'])==('SC Recycle Bin'))
				$is_recycle_bin = true;
			
			$icon=($row['active']?'catalog.png':'folder_grey.png');

			if($is_recycle_bin)
			{
				$row['name']=_l('SC Recycle Bin');
				$icon='folder_delete.png';
			}
			if($is_home)
				$icon='folder_table.png';

			echo "<row style=\"".$style."\"".
					" id=\"".$row['id_cms_category']."\"".($row["id_parent"]==0?' open="1"':'').">".
					"<cell image=\"../../".$icon."\"><![CDATA[ ".($style==''?formatText(hideCategoryPosition($row['name'])):_l('To Translate:').' '.formatText(hideCategoryPosition($res2['name'])))."]]></cell>";
			if(version_compare(_PS_VERSION_, '1.6.0.12', '>=') && SCMS) {
				echo "<cell><![CDATA[" . $shops . "]]></cell>";
			}
			echo	"<cell>".$row['id_cms_category']."</cell>"
					."<cell><![CDATA[".($style==''?formatText(hideCategoryPosition($row['name'])):_l('To Translate:').' '.formatText(hideCategoryPosition($res2['name'])))."]]></cell>"
					."<cell><![CDATA[".$row['description']."]]></cell>"
					."<cell>".$nb_cms."</cell>"
					."<cell>".$row['active']."</cell>";
			echo '  	<userdata name="not_deletable">'.(int)$not_deletable.'</userdata>';
			echo '  	<userdata name="is_recycle_bin">'.(int)$is_recycle_bin.'</userdata>';
			echo '  	<userdata name="is_home">'.(int)$is_home.'</userdata>';
			echo '  	<userdata name="is_root">'.(int)$is_root.'</userdata>';

			getLevelFromDB_PHP($row['id_cms_category'], true, 0, $limit_to_shop);
			echo '</row>'."\n";
		}
	}	
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
<call command="attachHeader"><param><![CDATA[#text_filter<?php if(version_compare(_PS_VERSION_, '1.6.0.12', '>=') && SCMS) { ?>,#text_filter<?php } ?>,#numeric_filter,#text_filter,#text_filter,#numeric_filter,#select_filter]]></param></call>
</beforeInit>
<column id="tree" width="250" type="tree" align="left" sort="na"><?php echo _l('Categories')?></column>
<?php if(version_compare(_PS_VERSION_, '1.6.0.12', '>=') && SCMS) { ?>
<column id="shops" width="100" type="ro" align="left" sort="na"><?php echo _l('Shops')?></column>
<?php } ?>
<column id="id_cms_category" width="40" type="ro" align="right" sort="na"><?php echo _l('ID')?></column>
<column id="name" width="120" type="ed" align="left" sort="na"><?php echo _l('Name')?></column>
<column id="description" width="200" type="ro" align="left" sort="na"><?php echo _l('Description')?></column>
<column id="nb_cms" width="40" type="ro" align="right" sort="na"><?php echo _l('Cms nb')?></column>
<column id="active" width="45" type="coro" align="center" sort="na"><?php echo _l('Active')?>
	<option value="0"><?php echo _l('No')?></option>
	<option value="1"><?php echo _l('Yes')?></option>
</column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cms_cmscatmanagement_treegrid').'</userdata>'."\n";
	$init = 0;
	$ps_root = 0;//SCI::getConfigurationValue("PS_ROOT_CATEGORY");
	$sql_root = "SELECT *
			FROM "._DB_PREFIX_."cms_category
			WHERE id_parent = 0";
	$res_root=Db::getInstance()->ExecuteS($sql_root);
	if(!empty($res_root[0]["id_cms_category"]))
		$ps_root = $res_root[0]["id_cms_category"];
	$others = true;
	$id_start = 0;

	if(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !SCMS)
	{
		$id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');
	}

	if (version_compare(_PS_VERSION_, '1.6.0.12', '>=') && !empty($id_shop))
	{
		$shop = new Shop($id_shop);
		$sql = 'SELECT c.id_parent, cs.id_cms_category
					FROM `'._DB_PREFIX_.'cms_category` c
					LEFT JOIN `'._DB_PREFIX_.'cms_category_shop` cs ON (cs.id_cms_category = c.id_cms_category)
					WHERE cs.id_shop = '.(int)$id_shop;
		$res=Db::getInstance()->getRow($sql);
		$init=$res['id_parent'];
		$others = false;
		$id_start = $res['id_cms_category'];
	}
	echo '<userdata name="parent_root">'.$init.'</userdata>'."\n";
	
	if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
	{
		$array_cats_cms = array();
		$array_children_cats_cms = array();

		$sql = "SELECT c.*, cl.*
				FROM "._DB_PREFIX_."cms_category c
				LEFT JOIN "._DB_PREFIX_."cms_category_lang cl ON (cl.id_cms_category=c.id_cms_category AND cl.id_lang=".(int)$id_lang.(version_compare(_PS_VERSION_, '1.6.0.12', '>=') && !empty($id_shop)?" AND cl.id_shop='".(int)$id_shop."'":'').")
				".(version_compare(_PS_VERSION_, '1.6.0.12', '>=') && !empty($id_shop)?" INNER JOIN "._DB_PREFIX_."cms_category_shop cs ON (cs.id_cms_category=c.id_cms_category AND cs.id_shop='".(int)$id_shop."') ":"")."
				GROUP BY c.id_cms_category
				ORDER BY c.`position` ASC";
		$res=Db::getInstance()->ExecuteS($sql);
		foreach($res as $k => $row)
		{
			$array_cats_cms[$row["id_cms_category"]]=$row;
				
			if(!isset($array_children_cats_cms[$row["id_parent"]]))
				$array_children_cats_cms[$row["id_parent"]] = array();
			$array_children_cats_cms[$row["id_parent"]][str_pad($row["position"], 5, "0", STR_PAD_LEFT).str_pad($row["id_cms_category"], 12, "0", STR_PAD_LEFT)] = $row["id_cms_category"];
		}
		if(version_compare(_PS_VERSION_, '1.6.0.12', '>=')) {
			getLevelFromDB_PHP($init,$others,$id_start, true);
		} else {
			getLevelFromDB_PHP($init,$others,$id_start);
		}
	}
	else
	{
		getLevelFromDB($init, $others, $id_start);
	}
	
	// BIN
	$sql = "SELECT c.*, cl.*
			FROM "._DB_PREFIX_."cms_category c
			LEFT JOIN "._DB_PREFIX_."cms_category_lang cl ON (cl.id_cms_category=c.id_cms_category AND cl.id_lang=".(int)$sc_agent->id_lang.")
			WHERE cl.name LIKE '%SC Recycle Bin' OR cl.name LIKE '%".psql(_l('SC Recycle Bin'))."'
			GROUP BY c.id_cms_category";
	$res=Db::getInstance()->ExecuteS($sql);

	$bincategory=0;
	$bincategory_parent=0;
	if (count($res)>0)
	{
		$bincategory=$res[0]['id_cms_category'];
		$bincategory_parent=$res[0]['id_parent'];
	
		if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
		{
			getLevelFromDB_PHP($bincategory_parent,false,$bincategory, false);
		}
		else
		{
			getLevelFromDB($bincategory_parent,false,$bincategory, true);
		}
	}
	
?>
</rows>
