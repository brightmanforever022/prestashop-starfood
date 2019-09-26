<?php

function getLevelFromDB($parent_id)
{
    global $id_lang,$id_shop,$binPresent,$forceDisplayAllCmsCategories,$root_cat_cms,$id_root_cms_cat;

    $sql = "SELECT c.active,c.id_cms_category,name, c.id_parent FROM "._DB_PREFIX_."cms_category c
            LEFT JOIN "._DB_PREFIX_."cms_category_lang cl ON (cl.id_cms_category=c.id_cms_category AND cl.id_lang=".(int)$id_lang." ".( version_compare(_PS_VERSION_, '1.6.0.12', '>=') && $id_shop>0?"AND cl.id_shop=".(int)$id_shop:"").")
            ".( version_compare(_PS_VERSION_, '1.6.0.12', '>=') && !$forceDisplayAllCmsCategories && $id_shop && (int)$parent_id > 0 ? "LEFT JOIN "._DB_PREFIX_."cms_category_shop cs ON (cs.id_cms_category=c.id_cms_category)" : '')."
            WHERE c.id_parent=".(int)$parent_id."
            ".( version_compare(_PS_VERSION_, '1.6.0.12', '>=') && !$forceDisplayAllCmsCategories && $id_shop && (int)$parent_id > 0 ? " AND cs.id_shop=".(int)$id_shop : '')."
            GROUP BY c.id_cms_category
            ORDER BY c.position";
    $res=Db::getInstance()->ExecuteS($sql);
    foreach($res as $k => $row){
        $style='';
        if (hideCategoryPosition($row['name'])=='')
        {
            $sql2 = "SELECT name FROM "._DB_PREFIX_."cms_category_lang
                     WHERE id_lang=".(int)Configuration::get('PS_LANG_DEFAULT')."
                        AND id_cms_category=".(int)$row['id_cms_category'];
            $res2=Db::getInstance()->getRow($sql2);
            $style='style="background:lightblue" ';
        }
        $icon=($row['active']?'catalog.png':'folder_grey.png');
        if (hideCategoryPosition($row['name'])=='SC Recycle Bin')
        {
            $icon='folder_delete.png';
            $binPresent=true;
        }

        $is_root = false;
        if($row["id_parent"]==0)
            $is_root = true;

        $is_home = false;

        if(version_compare(_PS_VERSION_, '1.4.0.17', '>=') && sc_in_array($row['id_cms_category'], $root_cat_cms,"cmsCategoryGet_rootcatgetLevelFromDB"))
        {
            $icon='folder_table.png';
            $is_home = true;
        }

        $not_deletable = false;
        if($is_home || $is_root)
            $not_deletable = true;

        echo "<item ".($style!='' ? $style:'').
            " id=\"".$row['id_cms_category']."\"".($parent_id==0 || $icon=='folder_table.png'?' open="1"':'').
            " im0=\"".$icon."\"".
            " im1=\"".$icon."\"".
            " im2=\"".$icon."\"".
            (hideCategoryPosition($row['name'])=='SC Recycle Bin' ? " tooltip=\""._l('CMS pages and CMS categories in recycle bin from all shops')."\"":"").
            "><itemtext><![CDATA[".(hideCategoryPosition($row['name'])=='SC Recycle Bin'?_l('SC Recycle Bin'):($style==''?formatText(hideCategoryPosition($row['name'])):_l('To Translate:').' '.formatText(hideCategoryPosition($res2['name']))))."]]></itemtext>";
        echo '  	<userdata name="not_deletable">'.(int)$not_deletable.'</userdata>';
        if(hideCategoryPosition($row['name'])=='SC Recycle Bin')
            echo '  	<userdata name="is_recycle_bin">1</userdata>';
        else
            echo '  	<userdata name="is_recycle_bin">0</userdata>';
        echo '  	<userdata name="is_home">'.(int)$is_home.'</userdata>';
        echo '  	<userdata name="is_root">'.(int)is_root.'</userdata>';
        echo ' 		<userdata name="parent_root">'.$id_root_cms_cat.'</userdata>';
            getLevelFromDB($row['id_cms_category']);
        echo '</item>'."\n";
    }
}

function getLevelFromDB_PHP($id_parent, $limit_to_shop=false)
{
    global $id_shop,$binPresent,$root_cat_cms,$array_cats_cms,$array_children_cats_cms,$id_root_cms_cat;
    
    if(!empty($array_children_cats_cms[$id_parent]))
    {
        ksort($array_children_cats_cms[$id_parent]);
        foreach($array_children_cats_cms[$id_parent] as $k => $id)
        {
            $row = $array_cats_cms[$id];

            if(version_compare(_PS_VERSION_, '1.6.0.12', '>='))
            {
                if(!SCMS)
                    $id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');
                if(!empty($id_shop))
                {
                    $in_shop = false;
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $sql_shop = "SELECT s.name, s.id_shop
									FROM "._DB_PREFIX_."cms_category_shop cs
                                    INNER JOIN "._DB_PREFIX_."shop s 
                                        ON (cs.id_shop=s.id_shop)
									WHERE cs.id_cms_category=".(int)$row['id_cms_category']."
									AND cs.id_shop = ".(int)$id_shop."
									ORDER BY s.name";
                        $res_shop=Db::getInstance()->executeS($sql_shop);
                        foreach($res_shop as $shop)
                        {
                            if(!empty($shop["id_shop"]) && !empty($id_shop) && $shop["id_shop"]==$id_shop)
                                $in_shop = true;
                        }
                    }
                    if(!$in_shop && !empty($limit_to_shop))
                        continue;
                }
            }

            $style='';
            if (hideCategoryPosition($row['name'])=='')
            {
                $sql2 = "SELECT name 
                        FROM "._DB_PREFIX_."cms_category_lang
                        WHERE id_lang=".(int)Configuration::get('PS_LANG_DEFAULT')."
                            AND id_cms_category=".(int)$row['id_cms_category'].( version_compare(_PS_VERSION_, '1.6.0.12', '>=') ? " AND id_shop=".(int)$row['$id_shop'] : '');
                $res2=Db::getInstance()->getRow($sql2);
                $style='style="background:lightblue" ';
            }
            $icon=($row['active']?'catalog.png':'folder_grey.png');
            if (hideCategoryPosition($row['name'])=='SC Recycle Bin')
            {
                $icon='folder_delete.png';
                $binPresent=true;
            }

            $is_root = false;
            if($row["id_parent"]==0)
                $is_root = true;

            $is_home = false;

            if(version_compare(_PS_VERSION_, '1.4.0.17', '>=') && sc_in_array($row['id_cms_category'], $root_cat_cms,"cmsCategoryGet_rootcatgetLevelFromDB_PHP"))
            {
                $icon='folder_table.png';
                $is_home = true;
            }

            $not_deletable = false;
            if($is_home || $is_root)
                $not_deletable = true;

            echo "<item ".($style!='' ? $style:'').
                " id=\"".$row['id_cms_category']."\"".($row["id_parent"]==0 || $icon=='folder_table.png'?' open="1"':'').
                " im0=\"".$icon."\"".
                " im1=\"".$icon."\"".
                " im2=\"".$icon."\"".
                (hideCategoryPosition($row['name'])=='SC Recycle Bin' ? " tooltip=\""._l('Cms and categories in recycle bin from all shops')."\"":"").
                ">\n<itemtext><![CDATA[".(hideCategoryPosition($row['name'])=='SC Recycle Bin'?_l('SC Recycle Bin'):($style==''?formatText(hideCategoryPosition($row['name'])):_l('To Translate:').' '.formatText(hideCategoryPosition($res2['name']))))."]]></itemtext>\n";
            echo '  	<userdata name="not_deletable">'.(int)$not_deletable.'</userdata>'."\n";
            if(hideCategoryPosition($row['name'])=='SC Recycle Bin')
                echo '  	<userdata name="is_recycle_bin">1</userdata>'."\n";
            else
                echo '  	<userdata name="is_recycle_bin">0</userdata>'."\n";
            echo '  	<userdata name="is_home">'.(int)$is_home.'</userdata>'."\n";
            echo '  	<userdata name="is_root">'.(int)$is_root.'</userdata>'."\n";
            echo ' 		<userdata name="parent_root">'.$id_root_cms_cat.'</userdata>';
            getLevelFromDB_PHP($row['id_cms_category'], $limit_to_shop);
            echo '</item>'."\n";
        }
    }
}
