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
    $forceDisplayAllCmsCategories=(int)Tools::getValue('forceDisplayAllCategories',0);
    $forExport=(int)Tools::getValue('forExport',0);

    require_once(SC_DIR.'lib/cms/cms_category_tools.php');

    /*
     * BIN Category
     */
    $sql = "SELECT c.id_cms_category, c.id_parent 
            FROM "._DB_PREFIX_."cms_category c
            LEFT JOIN "._DB_PREFIX_."cms_category_lang cl 
                ON (cl.id_cms_category=c.id_cms_category AND cl.id_lang=".(int)$sc_agent->id_lang.")
            WHERE cl.name LIKE '%SC Recycle Bin' 
                OR cl.name LIKE '%".psql(_l('SC Recycle Bin'))."'";
    $res=Db::getInstance()->ExecuteS($sql);

    $bincategory=0;
    if (count($res)==0)
    {
        $newCmsCategory=new CMSCategory();
        $newCmsCategory->id_parent=0;
        $newCmsCategory->level_depth=1;
        $newCmsCategory->active=0;
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $newCmsCategory->position=CMSCategory::getLastPosition(1,0);
        }elseif (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
        {
            // bug PS1.4 - set position
            $_GET['id_parent']=1;
            $newCmsCategory->position=CMSCategory::getLastPosition(1);
        }
        foreach($languages AS $lang)
        {
            $newCmsCategory->link_rewrite[$lang['id_lang']]='category';
            $newCmsCategory->name[$lang['id_lang']]='SC Recycle Bin';
        }
        $newCmsCategory->save();
        $bincategory=$newCmsCategory->id;
    }else{
        // fix bug in db
        if ($res[0]['id_cms_category'] == $res[0]['id_parent'])
            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'cms_category SET id_parent = 1 WHERE id_cms_category = '.(int)$res[0]['id_cms_category']);
        $bincategory=$res[0]['id_cms_category'];
    }
    $binPresent=false;

    /*
	 * Categories Home for MS
	 */

    $root_cat_cms = array();
    if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $shops = Shop::getShops(false);
        foreach ($shops as $shop)
        {
            $root_cat_cms[] = Db::getInstance()->getValue('SELECT id_cms_category FROM  '._DB_PREFIX_.'cms_category WHERE id_parent = 0 AND level_depth = 1');
        }
    }

    /*
     * Category CMS ROOT
     */
    $id_root_cms_cat=0;

    if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
        header("Content-type: application/xhtml+xml");
    } else {
        header("Content-type: text/xml");
    }

    echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
    echo '<tree id="0">';

    if(version_compare(_PS_VERSION_, '1.4.0.0', '>='))
    {
        /*
         * Get all cms_categories
         */
        $array_cats_cms = array();
        $array_children_cats_cms = array();

        if(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !SCMS)
        {
            $id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');
        }

        $sql = "SELECT c.*, cl.*
                    FROM "._DB_PREFIX_."cms_category c
                    LEFT JOIN "._DB_PREFIX_."cms_category_lang cl ON (cl.id_cms_category=c.id_cms_category AND cl.id_lang=".(int)$id_lang.(version_compare(_PS_VERSION_, '1.6.0.12', '>=') && !empty($id_shop)?" AND cl.id_shop='".(int)$id_shop."'":'').")
                    ".(version_compare(_PS_VERSION_, '1.6.0.12', '>=') && !empty($id_shop)?" INNER JOIN "._DB_PREFIX_."cms_category_shop cs ON (cs.id_cms_category=c.id_cms_category AND cs.id_shop='".(int)$id_shop."') ":"")."
                    ORDER BY c.position ASC, c.id_cms_category ASC";
        $res=Db::getInstance()->ExecuteS($sql);

        foreach($res as $k => $row)
        {
            $array_cats_cms[$row["id_cms_category"]]=$row;

            if(!isset($array_children_cats_cms[$row["id_parent"]]))
                $array_children_cats_cms[$row["id_parent"]] = array();
            $array_children_cats_cms[$row["id_parent"]][str_pad($row["position"], 5, "0", STR_PAD_LEFT).str_pad($row["id_cms_category"], 12, "0", STR_PAD_LEFT)] = $row["id_cms_category"];
        }

        getLevelFromDB_PHP($id_root_cms_cat, true);
    } else {
        getLevelFromDB($id_root_cms_cat, false);
    }

    /*
     * Display Bin in Root
     */
    if (SCMS && !$binPresent)
    {
        $icon='folder_delete.png';
        echo "<item ".
            " id=\"".$bincategory."\"".
            " text=\""._l('SC Recycle Bin')."\"".
            " im0=\"".$icon."\"".
            " im1=\"".$icon."\"".
            " im2=\"".$icon."\"".
            " tooltip=\""._l('CMS pages and CMS categories in recycle bin from all shops')."\">";
        echo '  	<userdata name="not_deletable">1</userdata>';
        echo '  	<userdata name="is_recycle_bin">1</userdata>';
        echo '  	<userdata name="is_home">0</userdata>';
        echo '  	<userdata name="is_root">0</userdata>';
        echo ' 		<userdata name="parent_root">'.$id_root_cms_cat.'</userdata>';

        if(version_compare(_PS_VERSION_, '1.4.0.0', '>='))
            getLevelFromDB_PHP($bincategory, true);
        else
            getLevelFromDB($bincategory);
        echo	"</item>\n";
    }

echo '</tree>';
