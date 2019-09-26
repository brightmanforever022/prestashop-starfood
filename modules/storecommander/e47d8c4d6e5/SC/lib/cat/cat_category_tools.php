<?php

function getLevelFromDB($parent_id,$FF_parent=0)
{
    global $id_lang,$id_shop,$binPresent,$forceDisplayAllCategories,$root_cat,$id_root,$FF_id,$FF_cat_archived;
    if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
    {
        $sql = "SELECT c.active,c.id_category,name, c.id_parent FROM "._DB_PREFIX_."category c
							LEFT JOIN "._DB_PREFIX_."category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang=".intval($id_lang).")
							WHERE c.id_parent=".(int)$parent_id."
							ORDER BY cl.name";
    }elseif (version_compare(_PS_VERSION_, '1.5.0.0', '<')){
        $sql = "SELECT c.active,c.id_category,name, c.id_parent FROM "._DB_PREFIX_."category c
							LEFT JOIN "._DB_PREFIX_."category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang=".intval($id_lang).")
							WHERE c.id_parent=".(int)$parent_id."
							GROUP BY c.id_category
							ORDER BY c.position";
    }else{
        $sql = "SELECT c.active,c.id_category,name, c.id_parent FROM "._DB_PREFIX_."category c
							LEFT JOIN "._DB_PREFIX_."category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang=".intval($id_lang)." ".($id_shop>0?"AND cl.id_shop=".(int)$id_shop:"").")
							".( !$forceDisplayAllCategories && $id_shop && (int)$parent_id > 0 ? "LEFT JOIN "._DB_PREFIX_."category_shop cs ON (cs.id_category=c.id_category)" : '')."
							WHERE c.id_parent=".(int)$parent_id."
							".( !$forceDisplayAllCategories && $id_shop && (int)$parent_id > 0 ? " AND cs.id_shop=".(int)$id_shop : '')."
							GROUP BY c.id_category
							".( !$forceDisplayAllCategories && $id_shop && (int)$parent_id > 0 ? "ORDER BY cs.position" : 'ORDER BY c.position')."
							";
    }
    $res=Db::getInstance()->ExecuteS($sql);
    foreach($res as $k => $row){
        $style='';
        if (hideCategoryPosition($row['name'])=='SoColissimo')
            continue;
        if (hideCategoryPosition($row['name'])=='')
        {
            $sql2 = "SELECT name FROM "._DB_PREFIX_."category_lang
									WHERE id_lang=".intval(Configuration::get('PS_LANG_DEFAULT'))."
										AND id_category=".(int)$row['id_category'];
            $res2=Db::getInstance()->getRow($sql2);
            $style='style="background:lightblue" ';
        }
        $icon=($row['active']?'catalog.png':'folder_grey.png');
        if (hideCategoryPosition($row['name'])=='SC Recycle Bin')
        {
            $icon='folder_delete.png';
            $binPresent=true;
            if (!_r("ACT_CAT_DELETE_PRODUCT_COMBI"))
                continue;
        }

        $is_root = false;
        if($row["id_parent"]==0)
            $is_root = true;

        $is_home = false;
        if(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && sc_in_array($row['id_category'], $root_cat,"catCategoryGet_rootcatgetLevelFromDB"))
        {
            $icon='folder_table.png';
            $is_home = true;
        }

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

        $not_deletable = false;
        if($is_home || $is_root)
            $not_deletable = true;

        echo "<item ".($style!='' ? $style:'').
            " id=\"".$row['id_category']."\"".($parent_id==0 || $icon=='folder_table.png'?' open="1"':'').
            " im0=\"".$icon."\"".
            " im1=\"".$icon."\"".
            " im2=\"".$icon."\"".
            (hideCategoryPosition($row['name'])=='SC Recycle Bin' ? " tooltip=\""._l('Products and categories in recycle bin from all shops')."\"":"").
            "><itemtext><![CDATA[".(hideCategoryPosition($row['name'])=='SC Recycle Bin'?_l('SC Recycle Bin'):($style==''?formatText(hideCategoryPosition($row['name'])):_l('To Translate:').' '.formatText(hideCategoryPosition($res2['name']))))."]]></itemtext>";
        echo '  	<userdata name="not_deletable">'.intval($not_deletable).'</userdata>';
        if(hideCategoryPosition($row['name'])=='SC Recycle Bin')
            echo '  	<userdata name="is_recycle_bin">1</userdata>';
        else
            echo '  	<userdata name="is_recycle_bin">0</userdata>';
        echo '  	<userdata name="is_home">'.intval($is_home).'</userdata>';
        echo '  	<userdata name="is_root">'.intval($is_root).'</userdata>';
        echo ' 		<userdata name="is_segment">0</userdata>';
        echo ' 		<userdata name="parent_root">'.$id_root.'</userdata>';
        echo ' 		<userdata name="is_FF">'.$in_FF.'</userdata>';
        echo ' 		<userdata name="not_associate_FF">'.$not_associate_FF.'</userdata>';
            getLevelFromDB($row['id_category'],$in_FF);
        echo '</item>'."\n";
    }
}

function getLevelFromDB_PHP($id_parent, $limit_to_shop=false, $FF_parent=0)
{
    global $id_lang,$id_shop,$binPresent,$forceDisplayAllCategories,$root_cat,$array_cats,$array_children_cats,$id_root,$FF_id,$FF_cat_archived;
    /*	echo $id_parent."\n";
        print_r($array_children_cats[$id_parent]);die();*/

    if(!empty($array_children_cats[$id_parent]))
    {
        ksort($array_children_cats[$id_parent]);
        foreach($array_children_cats[$id_parent] as $k => $id)
        {
            $row = $array_cats[$id];

            if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                if(!SCMS)
                    $id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');
                if(!empty($id_shop))
                {
                    $in_shop = false;
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $sql_shop = "SELECT s.name, s.id_shop
									FROM "._DB_PREFIX_."category_shop cs
										INNER JOIN "._DB_PREFIX_."shop s ON (cs.id_shop=s.id_shop)
									WHERE cs.id_category=".(int)$row['id_category']."
									ORDER BY s.name";
                        $res_shop=Db::getInstance()->executeS($sql_shop);
                        foreach($res_shop as $shop)
                        {
                            if(!empty($shop["id_shop"]) && !empty($id_shop) && $shop["id_shop"]==$id_shop)
                                $in_shop = true;
                        }
                    }
                    if(!$in_shop && !empty($limit_to_shop) /*&& SCI::getSelectedShop()>0*/)
                        continue;
                }
            }

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
            if (hideCategoryPosition($row['name'])=='SC Recycle Bin')
            {
                $icon='folder_delete.png';
                $binPresent=true;
                if (!_r("ACT_CAT_DELETE_PRODUCT_COMBI"))
                    continue;
            }

            $is_root = false;
            if($row["id_parent"]==0)
                $is_root = true;

            $is_home = false;
            if(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && sc_in_array($row['id_category'], $root_cat,"catCategoryGet_rootcatgetLevelFromDB_PHP"))
            {
                $icon='folder_table.png';
                $is_home = true;
            }

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

            $not_deletable = false;
            if($is_home || $is_root)
                $not_deletable = true;


            echo "<item ".($style!='' ? $style:'').
                " id=\"".$row['id_category']."\"".($row["id_parent"]==0 || $icon=='folder_table.png'?' open="1"':'').
                " im0=\"".$icon."\"".
                " im1=\"".$icon."\"".
                " im2=\"".$icon."\"".
                (hideCategoryPosition($row['name'])=='SC Recycle Bin' ? " tooltip=\""._l('Products and categories in recycle bin from all shops')."\"":"").
                ">\n<itemtext><![CDATA[".(hideCategoryPosition($row['name'])=='SC Recycle Bin'?_l('SC Recycle Bin'):($style==''?formatText(hideCategoryPosition($row['name'])):_l('To Translate:').' '.formatText(hideCategoryPosition($res2['name']))))."]]></itemtext>\n";
            echo '  	<userdata name="not_deletable">'.intval($not_deletable).'</userdata>'."\n";
            if(hideCategoryPosition($row['name'])=='SC Recycle Bin')
                echo '  	<userdata name="is_recycle_bin">1</userdata>'."\n";
            else
                echo '  	<userdata name="is_recycle_bin">0</userdata>'."\n";
            echo '  	<userdata name="is_home">'.intval($is_home).'</userdata>'."\n";
            echo '  	<userdata name="is_root">'.intval($is_root).'</userdata>'."\n";
            echo ' 		<userdata name="is_segment">0</userdata>';
            echo ' 		<userdata name="parent_root">'.$id_root.'</userdata>';
            echo ' 		<userdata name="is_FF">'.$in_FF.'</userdata>';
            echo ' 		<userdata name="not_associate_FF">'.$not_associate_FF.'</userdata>';
            getLevelFromDB_PHP($row['id_category'], $limit_to_shop, $in_FF);
            echo '</item>'."\n";
        }
    }
}