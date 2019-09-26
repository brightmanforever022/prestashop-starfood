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
	$id_shop=(int)Tools::getValue('id_shop',SCI::getSelectedShop());
	$with_segment=(int)Tools::getValue('with_segment', "1");
	$forceDisplayAllCategories=(int)Tools::getValue('forceDisplayAllCategories',0);
	$forExport=(int)Tools::getValue('forExport',0);

	require_once(SC_DIR.'lib/cat/cat_category_tools.php');

	/*
	 * BIN Category
	 */
	$sql = "SELECT c.id_category,c.id_parent FROM "._DB_PREFIX_."category c
					LEFT JOIN "._DB_PREFIX_."category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang=".intval($sc_agent->id_lang).")
					WHERE cl.name LIKE '%SC Recycle Bin' OR cl.name LIKE '%".psql(_l('SC Recycle Bin'))."'";
	$res=Db::getInstance()->ExecuteS($sql);
	$bincategory=0;
	if (count($res)==0)
	{
		$newcategory=new Category();
		$newcategory->id_parent=1;
		$newcategory->level_depth=1;
		$newcategory->active=0;
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$newcategory->position=Category::getLastPosition(1,0);
		}elseif (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
		{
			// bug PS1.4 - set position
			$_GET['id_parent']=1;
			$newcategory->position=Category::getLastPosition(1);
		}
		foreach($languages AS $lang)
		{
			$newcategory->link_rewrite[$lang['id_lang']]='category';
			$newcategory->name[$lang['id_lang']]='SC Recycle Bin';
		}
		$newcategory->save();
		$bincategory=$newcategory->id;
	}else{
		// fix bug in db
		if ($res[0]['id_category'] == $res[0]['id_parent'])
			Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'category SET id_parent = 1 WHERE id_category = '.(int)$res[0]['id_category']);
		$bincategory=$res[0]['id_category'];
	}
	$binPresent=false;

	/*
	 * Categories Home for MS
	 */

	$root_cat = array();
	if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		$shops = Shop::getShops(false);
		foreach ($shops as $shop)
		{
			$root_cat[] = $shop['id_category'];
		}
	}

	/*
	 * Category ROOT
	 */
	$id_root=0;
	$ps_root = 0;//SCI::getConfigurationValue("PS_ROOT_CATEGORY");
	if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		$sql_root = "SELECT *
					FROM "._DB_PREFIX_."category
					WHERE id_parent = 0";
		$res_root=Db::getInstance()->ExecuteS($sql_root);
		if(!empty($res_root[0]["id_category"]))
			$ps_root = $res_root[0]["id_category"];
	}
	if(!empty($ps_root))
		$id_root = $ps_root;
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

	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); 
	} else {
	 		header("Content-type: text/xml");
	}

	
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<tree id="0">';
	//echo ' 		<userdata name="parent_root">'.$id_root.'</userdata>';
	
	if(version_compare(_PS_VERSION_, '1.4.0.0', '>='))
	{
		/*
		 * Get all categories
		 */
		$array_cats = array();
		$array_children_cats = array();
		
		if(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !SCMS)
		{
			$id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');
		}
		
		$sql = "SELECT c.*, cl.name, c.position ".(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop)?", cs.position":"")."
				FROM "._DB_PREFIX_."category c
				LEFT JOIN "._DB_PREFIX_."category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang=".intval($id_lang).(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop)?" AND cl.id_shop='".(int)$id_shop."'":'').")
				".(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop)?" INNER JOIN "._DB_PREFIX_."category_shop cs ON (cs.id_category=c.id_category AND cs.id_shop='".(int)$id_shop."') ":"")."
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
		/*echo $id_root."\n";
		print_r($array_children_cats);die();*/

		
		getLevelFromDB_PHP($id_root, true);
	}
	else
		getLevelFromDB($id_root, false);

	/*
	 * Display Bin in Root
	 */
	if (SCMS && !$binPresent && _r("ACT_CAT_DELETE_PRODUCT_COMBI"))
	{
		$icon='folder_delete.png';
		echo "<item ".
								" id=\"".$bincategory."\"".
								" text=\""._l('SC Recycle Bin')."\"".
								" im0=\"".$icon."\"".
								" im1=\"".$icon."\"".
								" im2=\"".$icon."\"".
								" tooltip=\""._l('Products and categories in recycle bin from all shops')."\">";
			echo '  	<userdata name="not_deletable">1</userdata>';
			echo '  	<userdata name="is_recycle_bin">1</userdata>';
			echo '  	<userdata name="is_home">0</userdata>';
			echo '  	<userdata name="is_root">0</userdata>';
			echo ' 		<userdata name="is_segment">0</userdata>';
			echo ' 		<userdata name="parent_root">'.$id_root.'</userdata>';
			echo ' 		<userdata name="is_FF">0</userdata>';
			if(version_compare(_PS_VERSION_, '1.4.0.0', '>='))
				getLevelFromDB_PHP($bincategory, true);
			else
				getLevelFromDB($bincategory);
		echo	"</item>\n";
	}

	/*
	 * Display Segments
	 */
	if(SCSG && $with_segment)
		SegmentHook::getSegmentLevelFromDB(0, "catalog");

	echo '</tree>';
