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

$id_lang = Tools::getValue("id_lang");

/*SCI::updateConfigurationValue("SC_FOULEFACTORY_ID","");
SCI::updateConfigurationValue("SC_FOULEFACTORY_APIKEY","");*/

$FF_ID = SCI::getConfigurationValue("SC_FOULEFACTORY_ID");
$FF_APIKEY = SCI::getConfigurationValue("SC_FOULEFACTORY_APIKEY");

//Configuration::set("SC_FOULEFACTORY_CATEGORY","81");

if($user_lang_iso=="fr")
    require_once (dirname(__FILE__)."/".$user_lang_iso.'.php');

if(empty($FF_ID) || empty($FF_APIKEY))
{
    require_once (dirname(__FILE__)."/cat_win-foulefactory_login.php");
}
else
{
    // SI NON ACTIVE MAIS TOUTES LES INFOS
    $FF_active = SCI::getConfigurationValue("SC_FOULEFACTORY_ACTIVE");
    if(empty($FF_active))
    {
        SCI::updateConfigurationValue("SC_FOULEFACTORY_ACTIVE","1");
    }
    if (!isTable('sc_ff_project'))
    {
        $sql="
                    CREATE TABLE `"._DB_PREFIX_."sc_ff_project` (
                      `id_project` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                      `id_ff_project` int(11) NOT NULL DEFAULT '0',
                      `name` varchar(255) NOT NULL,
                      `instructions` text,
                      `type` varchar(100) DEFAULT NULL,
                      `started_at` datetime DEFAULT NULL,
                      `duration` int(11) NOT NULL DEFAULT '0',
                      `tarif` decimal(10,2) NOT NULL DEFAULT '0.00',
                      `status` varchar(100) NOT NULL DEFAULT 'created',
                      `percent` int(11) NOT NULL DEFAULT '0',
                      `source` varchar(255) DEFAULT NULL,
                      `id_category` int(11) NOT NULL,
                      `params` text,
                      `nb_product` INT NOT NULL DEFAULT '0',
                      `created_at` date NOT NULL,
                      `updated_at` date NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        Db::getInstance()->Execute($sql);
        if (!isTable('sc_ff_project'))
            die('<script>wCatFoulefactory.close();dhtmlx.message({text:\''._l('Store Commander cannot create the table %s, please contact your hosting support and ask: Can you please confirm that the MySQL user has the necessary permission to execute these commands: SHOW and CREATE TABLE. Upon confirmation, you can restart Store Commander.',1,_DB_PREFIX_.'sc_ff_project').'\',type:"error",expire:-1});</script>');
    }
    if(!file_exists(dirname(__FILE__)."/../../../../../return_bank.php"))
    {
        @copy(dirname(__FILE__)."/return_bank.php",dirname(__FILE__)."/../../../../../return_bank.php");
        if(!file_exists(dirname(__FILE__)."/../../../../../return_bank.php"))
            die('<script>wCatFoulefactory.close();dhtmlx.message({text:\''._l('The file return_bank.php can\'t be copied in the folder: modules/storecommander. Please check write permissions on this folder.',1).'\',type:"error",expire:-1});</script>');
    }

    // SI PAS DE CATEGORY FOULEFACTORY
    $FF_cat = SCI::getConfigurationValue("SC_FOULEFACTORY_CATEGORY");
    if(!empty($FF_cat))
    {
        if(!Category::existsInDatabase((int)$FF_cat, "category"))
            $FF_cat = null;
    }
    if(empty($FF_cat))
    {
        if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            $id_parent=SCI::getConfigurationValue("PS_HOME_CATEGORY");
        else
            $id_parent=1;
        $name="FouleFactory";

        $newcategory=new Category();
        $newcategory->id_parent=$id_parent;
        $newcategory->level_depth=$newcategory->calcLevelDepth();
        $newcategory->active=0;

        if (SCMS)
        {
            $shops = Shop::getShops(false,null,true);
            $newcategory->id_shop_list = $shops;
        }

        $languages = Language::getLanguages(true);
        foreach($languages AS $lang)
        {
            $newcategory->link_rewrite[$lang['id_lang']]=link_rewrite($name);
            $newcategory->name[$lang['id_lang']]=$name;
        }
        $newcategory->add();

        if (!in_array(1,$newcategory->getGroups()))
            $newcategory->addGroups(array(1));
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $shops=Category::getShopsByCategory((int)$id_parent);
            foreach($shops AS $shop)
            {
                $position = Category::getLastPosition((int)$id_parent, $shop['id_shop']);
                if (!$position)
                    $position = 1;
                $newcategory->addPosition($position, $shop['id_shop']);
            }
        }
        $FF_cat = $newcategory->id;
        SCI::updateConfigurationValue("SC_FOULEFACTORY_CATEGORY",$newcategory->id);
    }

    if(!empty($FF_cat))
    {
        $FF_cat_archived = SCI::getConfigurationValue("SC_FOULEFACTORY_CATEGORYARCHIVED");
        if(!empty($FF_cat_archived))
        {
            if(!Category::existsInDatabase((int)$FF_cat_archived, "category"))
                $FF_cat_archived = null;

        }
        if(empty($FF_cat_archived))
        {
            $id_parent=$FF_cat;
            $name="ARCHIVED";

            $newcategory=new Category();
            $newcategory->id_parent=$id_parent;
            $newcategory->level_depth=$newcategory->calcLevelDepth();
            $newcategory->active=0;

            if (SCMS)
            {
                $shops = Shop::getShops(false,null,true);
                $newcategory->id_shop_list = $shops;
            }

            $languages = Language::getLanguages(true);
            foreach($languages AS $lang)
            {
                $newcategory->link_rewrite[$lang['id_lang']]=link_rewrite($name);
                $newcategory->name[$lang['id_lang']]=$name;
            }
            $newcategory->add();

            if (!in_array(1,$newcategory->getGroups()))
                $newcategory->addGroups(array(1));
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $shops=Category::getShopsByCategory((int)$id_parent);
                foreach($shops AS $shop)
                {
                    $position = Category::getLastPosition((int)$id_parent, $shop['id_shop']);
                    if (!$position)
                        $position = 1;
                    $newcategory->addPosition($position, $shop['id_shop']);
                }
            }
            $FF_cat_archived = $newcategory->id;
            SCI::updateConfigurationValue("SC_FOULEFACTORY_CATEGORYARCHIVED",$newcategory->id);
        }
        if(!empty($FF_cat_archived))
        {
            $sql = "SELECT * FROM "._DB_PREFIX_."sc_ff_project WHERE status='archived' ORDER BY id_project DESC";
            $res=Db::getInstance()->ExecuteS($sql);

            foreach($res as $project)
            {
                $cat = new Category((int)$project["id_category"]);
                if($cat->id_parent!=$FF_cat_archived)
                {
                    $cat->id_parent=$FF_cat_archived;
                    $cat->save();
                }
            }
        }
    }

    require_once (dirname(__FILE__)."/cat_win-foulefactory_manage.php");
}