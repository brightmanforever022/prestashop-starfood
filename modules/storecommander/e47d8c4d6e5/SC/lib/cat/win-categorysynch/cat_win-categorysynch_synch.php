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

$global_all_cats = array();
function getLevelFromDB($parent_id, $id_shop)
{
	global $global_all_cats, $id_lang;
	
	$return_tree_cats = array();
	
	$sql = "SELECT cs.*, cl.name, c.id_parent
				FROM "._DB_PREFIX_."category_shop cs
					INNER JOIN "._DB_PREFIX_."category_lang cl ON (cs.id_category = cl.id_category AND cs.id_shop = '".(int)$id_shop."' AND cl.id_lang = '".(int)$id_lang."')
					INNER JOIN "._DB_PREFIX_."category c ON (cs.id_category = c.id_category)
				WHERE cs.id_shop = '".(int)$id_shop."'
					AND c.id_parent=".(int)$parent_id."
				GROUP BY cs.id_category
				ORDER BY cs.position";
	$cats = Db::getInstance()->executeS($sql);
	
	foreach($cats as $cat)
	{
		$cat_array = array("id_category"=>$cat["id_category"], "name"=>$cat["name"], "position"=>$cat["position"], "id_parent"=>$cat["id_parent"]);
		
		$global_all_cats[$cat["id_category"]] = $cat_array;
		
		$cat_array["items"] = getLevelFromDB($cat["id_category"], $id_shop);
		
		$return_tree_cats[$cat["id_category"]] = $cat_array;
	}
	
	return $return_tree_cats;
}

function replacePositionInTree($ref_tree_cats, $checked_all_cats)
{
	global $ref_all_cats;
	
	$return = array();
	
	usort($checked_all_cats, "triByPosition");
	
	$i = 0;
	foreach($checked_all_cats as $checked_cat)
	{
		if(!empty($ref_all_cats[$checked_cat["id_category"]]))
		{
			$return[$i] = $ref_all_cats[$checked_cat["id_category"]];
		}
		else
		{
			$checked_cat["position"] = $checked_cat["position"]+1000;
			$return[$i] = $checked_cat;
		}
		
		if(!empty($checked_cat["items"]))
		{
			$ref_cat_items = array();
			if(!empty($ref_all_cats[$checked_cat["id_category"]]["items"]))
				$ref_cat_items = $ref_all_cats[$checked_cat["id_category"]]["items"];
			$return[$i]["items"] = replacePositionInTree($ref_cat_items, $checked_cat["items"]);
		}
		$i++;
	}
	
	usort($return, "triByPosition");
	
	return $return;
}

function updatePosition($id_shop, $tree_cats)
{
	if(!empty($tree_cats) && count($tree_cats)>0)
	{
		foreach($tree_cats as $temp_position=>$cat)
		{
			$position = $temp_position+1;
			$sql = 'UPDATE '._DB_PREFIX_.'category_shop SET position = "'.(int)$position.'" WHERE id_category = "'.(int)$cat["id_category"].'" AND id_shop = "'.(int)$id_shop.'"';
			Db::getInstance()->execute($sql);
			
			if(!empty($cat["items"]))
			{
				updatePosition($id_shop, $cat["items"]);
			}
		}
	}
}

function triByPosition($a, $b)
{
	if ($a["position"] == $b["position"]) {
		return 0;
	}
	return ($a["position"] < $b["position"]) ? -1 : 1;
}

$todo = "";
$messages = "";
$errors = false;

$id_shop_selected = Tools::getValue("selected_shop",0);
$checked_shops = Tools::getValue("checked_shops",0);

if(empty($id_shop_selected))
	$errors = true;
if(empty($checked_shops))
	$errors = true;

if(!$errors)
{
	// Positions dans la boutique sélectionnée
	$selected_shop = new Shop($id_shop_selected);
	$parent_category = new Category($selected_shop->id_category,$id_lang,$id_shop_selected);
	
	$ref_tree_cats = getLevelFromDB($selected_shop->id_category, $id_shop_selected);
	$ref_all_cats = $global_all_cats;
	$nb_ref_cats = count($ref_all_cats);
	
	// Création de requêtes de dupplication sur les boutiques cochées
	$checked_shops = explode(",",$checked_shops);
	
	foreach ($checked_shops as $id_shop_checked)
	{
		if($id_shop_checked!=$id_shop_selected)
		{
			$checked_shop = new Shop($id_shop_checked);
				
			$global_all_cats = array();
			$checked_tree_cats = getLevelFromDB($selected_shop->id_category, $id_shop_checked);
			$checked_all_cats = $global_all_cats;
			$nb_checked_cats = count($ref_checked_cats);
			
			$new_tree_cats = replacePositionInTree($ref_tree_cats, $checked_tree_cats);
				
			if($checked_shop->id_category != $selected_shop->id_category)
			{
				$errors = true;
			}
				
			$finded_cats = $nb_checked_cats;
			foreach($ref_all_cats as $ref_cat)
			{
				if(!isset($checked_all_cats[$ref_cat["id_category"]]))	// Non présente sur la boutique cochée
				{
					$errors = true;
					$finded_cats++;
				}
			}
			
			if(!$errors)
			{
				updatePosition($id_shop_checked, $new_tree_cats);
			}
		}
	}
}
?>
<script type="text/javascript" src="<?php echo SC_JQUERY;?>"></script>
<br/><br/><br/><br/><br/><br/><br><br>
<center><img src="lib/img/loading.gif" alt="loading" title="loading" /></center>
<script type="text/javascript">
$.get("index.php?ajax=1&act=cat_rebuildleveldepth",function(data){
	parent.wSynchroCatsPos.close();
});
</script>