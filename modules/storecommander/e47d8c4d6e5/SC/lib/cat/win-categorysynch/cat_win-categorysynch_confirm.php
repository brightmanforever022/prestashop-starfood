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
?>
<style type="text/css">
.btn {
	background: linear-gradient(#e2efff, #d3e7ff) repeat scroll 0 0 rgba(0, 0, 0, 0);
    border: 1px solid #a4bed4;
    color: #34404b;
    font-size: 11px;
    height: 27px;
    overflow: hidden;
    position: relative;
	font-weight: bold;
	cursor: pointer;
	float: right;
	margin-top: 6px;
}
</style>
<div style="border: 1px solid #A4BED4; display: block;  height: 416px;  margin-top: 22px;margin-left: 12px; width: 738px;">
	
	<div style="display: block; width: 718px; height: 358px; padding: 10px; overflow: auto;">
	<?php 
	$msg_info = ''._l('You want to synchronize the categories positions on the Shop');
	
	//$todo = "";
	$messages = "";
	$errors = false;
	$previous = false;
	
	//
	$id_shop_selected = Tools::getValue("selected_shop",0);
	$checked_shops = Tools::getValue("checked_shops",array());

	if(empty($id_shop_selected))
	{
		$messages .= '<span style="color: #ca0000;">'._l('You must select one shop to synchronize the positions').'<span><br/>';
		$errors = true;
		$previous = true;
	}
	if(empty($checked_shops))
	{
		$messages .= '<span style="color: #ca0000;">'._l('You must tick shops to synchronize the positions').'<span><br/>';
		$errors = true;
		$previous = true;
	}
	
	if(!$errors)
	{
		// Positions dans la boutique sélectionnée
		$selected_shop = new Shop($id_shop_selected);
		$parent_category = new Category($selected_shop->id_category,$id_lang,$id_shop_selected);
		
		$ref_tree_cats = getLevelFromDB($selected_shop->id_category, $id_shop_selected);
		$ref_all_cats = $global_all_cats;
		$nb_ref_cats = count($ref_all_cats);	
		
		$msg_info .= ' <strong>"'.$selected_shop->name.'" (#'.$id_shop_selected.')</strong> '._l('with').':<br/><strong>';
			
		// Création de requêtes de duplication sur les boutiques cochées
		
		foreach ($checked_shops as $i=>$id_shop_checked)
		{
			if($id_shop_checked!=$id_shop_selected)
			{
				$checked_shop = new Shop($id_shop_checked);
				
				$global_all_cats = array();
				$checked_tree_cats = getLevelFromDB($selected_shop->id_category, $id_shop_checked);
				$checked_all_cats = $global_all_cats;
				$nb_checked_cats = count($ref_checked_cats);
				
				if($checked_shop->id_category != $selected_shop->id_category)
				{
					$messages .= '<span style="color: #ca0000;"><strong>'._l('Shop')." \"".$checked_shop->name."\" (#".$id_shop_checked.")</strong> - "._l('Error:')." "._l('It must have category')." ".$parent_category->name." (#".$selected_shop->id_category.") "._l('as parent category.').'<span><br/>';
					$errors = true;
				}
				
				if( $i>0)
					$msg_info .= ', ';
				$msg_info .= '"'.$checked_shop->name.'" (#'.$id_shop_checked.')';
				
				$finded_cats = $nb_checked_cats;
				foreach($ref_all_cats as $ref_cat)
				{				
					if(!isset($checked_all_cats[$ref_cat["id_category"]]))	// Non présente sur la boutique cochée
					{
						$messages .= '<span style="color: #ca0000;"><strong>'._l('Category')." \"".$ref_cat["name"]."\" (#".$ref_cat["id_category"].")</strong> - "._l('Error:')." "._l('This category is not shared with the shop')." \"".$checked_shop->name."\" (#".$id_shop_checked.").".'<span><br/>';
						$errors = true;
						$finded_cats++;
					}
					/*else // Présente sur la boutique cochée
					{
						$todo .= 'UPDATE '._DB_PREFIX_.'category_shop SET position = "'.(int)$position.'" WHERE id_category = "'.(int)$id_category.'" AND id_shop = "'.(int)$id_shop_checked.'";'."\n";
					}*/
				}
				
				if($finded_cats!=$nb_ref_cats)
				{
					$cats_not_in_ref = $result = array_diff_assoc($checked_all_cats, $ref_all_cats);
					foreach ($cats_not_in_ref as $checked_cat)
					{
						$messages .= '<span style="color: #cc8b00;"><strong>'._l('Category')." ".$checked_cat["name"]." (#".$checked_cat["id_category"].")</strong> - "._l('Warning:')." "._l('This category is not shared with the shop')." \"".$selected_shop->name."\" (#".$id_shop_selected.").<span><br/>";
					}
				}
			}
		}
	
		$msg_info .= '.</strong><br/><br/>';
		echo $msg_info;
	}
	
	if(!empty($messages))
		echo $messages;
	else
		echo '<br/><br/><center style="color: #659f11;">'._l('Ready to synchronize positions.').'</center>';
	?>
	</div>
	
	<div style="display: block; width: 733px; height: 38px;">
		<?php if(!$errors) { ?>
		<button class="btn" onclick="synchronize()"><?php echo _l('Synchronize');?></button>
		<?php } ?>
		<?php //if($previous) { ?>
		<button class="btn" style="float: left; margin-left: 10px;" onclick="previous()"><?php echo _l('Last step');?></button>
		<?php //} ?>
	</div>
	
</div>
<script type="text/javascript">
function synchronize()
{
	parent.dhxlSynchroCatsPos.cells('a').attachURL("index.php?ajax=1&act=cat_win-categorysynch_synch&selected_shop=<?php echo $id_shop_selected ?>&checked_shops=<?php echo implode(",", $checked_shops) ?>&id_lang="+parent.SC_ID_LANG+"&"+new Date().getTime(),function(data){});
}
function previous()
{
	parent.dhxlSynchroCatsPos.cells('a').attachURL("index.php?ajax=1&act=cat_win-categorysynch_choice&id_lang="+parent.SC_ID_LANG+"&"+new Date().getTime(),function(data){});
}
</script>