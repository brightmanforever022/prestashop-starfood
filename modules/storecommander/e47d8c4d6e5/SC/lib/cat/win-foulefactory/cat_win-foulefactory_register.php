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
require_once ("lib/php/foulefactory/FfProject.php");
require_once ("lib/php/foulefactory/FFApi.php");

if($user_lang_iso=="fr")
require_once (dirname(__FILE__)."/".$user_lang_iso.'.php');

$action = Tools::getValue("action");

if(!empty($action) && $action=="login")
{
    $ID = Tools::getValue("ID");
    $APIKEY = Tools::getValue("APIKEY");

    if(!empty($ID) && !empty($APIKEY))
    {
        $api = new FFApi($ID,$APIKEY);
        $sub_url = "accounts";
        $FF_return = $api->queryGet($sub_url);

        if($FF_return['status_code']=='200')
        {
            SCI::updateConfigurationValue("SC_FOULEFACTORY_ID",$ID);
            SCI::updateConfigurationValue("SC_FOULEFACTORY_APIKEY",$APIKEY);

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

                SCI::updateConfigurationValue("SC_FOULEFACTORY_CATEGORY",$newcategory->id);
            }

            SCI::updateConfigurationValue("SC_FOULEFACTORY_ACTIVE","1");
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
                  `created_at` date NOT NULL,
                  `updated_at` date NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                Db::getInstance()->Execute($sql);
                if (!isTable('sc_ff_project'))
                    die(json_encode(array("status"=>"error", "message"=>_l('Store Commander cannot create the table %s, please contact your hosting support and ask: Can you please confirm that the MySQL user has the necessary permission to execute these commands: SHOW and CREATE TABLE. Upon confirmation, you can restart Store Commander.',0,_DB_PREFIX_.'sc_ff_project'))));
            }
            if(!file_exists(dirname(__FILE__)."/../../../../../return_bank.php"))
            {
                @copy(dirname(__FILE__)."/return_bank.php",dirname(__FILE__)."/../../../../../return_bank.php");
                if(!file_exists(dirname(__FILE__)."/../../../../../return_bank.php"))
                    die(json_encode(array("status"=>"error", "message"=>_l('The file return_bank.php can\'t be copied in the folder: modules/storecommander. Please check write permissions on this folder.'))));
            }


            // CREATION PROJET
            $exist_projects = FfProject::existProjects();
            if($exist_projects==false)
            {
                $project = new FfProject();
                $project->name = _l('First project');
                $project->save();
            }
            /*?>
            $.get('index.php?ajax=1&act=cat_win-foulefactory_init',function(data){
            $('#jsExecute').html(data);
            });
            <?php*/
            echo json_encode(array("status"=>"success", "message"=>""));
        }
        else
        {
            echo json_encode(array("status"=>"error", "message"=>$FF_return["message"]));
        }
    }
}
elseif(!empty($action) && $action=="register")
{
    $idGender = Tools::getValue("idGender");
    $firstName = Tools::getValue("firstName");
    $name = Tools::getValue("name");
    $email = Tools::getValue("email");
    $phone = Tools::getValue("phone");
    $birthday = Tools::getValue("birthday");
    $company = Tools::getValue("company","");
    $address1 = Tools::getValue("address1");
    $address2 = Tools::getValue("address2","");
    $city = Tools::getValue("city");
    $postalCode = Tools::getValue("postalCode");
    $countryCode = Tools::getValue("countryCode");

    $api = new FFApi();
    $sub_url = "accounts";
    $data = array(
        "idGender"=> $idGender,
        "firstName"=> $firstName,
        "name"=> $name,
        "email"=> $email,
        "phone"=> $phone,
        "birthday"=> $birthday,
        "company"=> $company,
        "address1"=> $address1,
        "address2"=> $address2,
        "city"=> $city,
        "postalCode"=> $postalCode,
        "countryCode"=> $countryCode,
        "billAddress1"=> $address1,
        "billAddress2"=> $address2,
        "billCity"=> $city,
        "billPostalCode"=> $postalCode,
        "nationality"=> $countryCode,
        "optin"=> true
    );
    $FF_return = $api->queryPost($sub_url, $data);
    if($FF_return['status_code']=='200')
    {
        $obj_account = $FF_return["response"];

        $FF_ID = $obj_account->ApiLogin;
        $FF_APIKEY = $obj_account->ApiPassPhrase;

        if(!empty($FF_ID) && !empty($FF_APIKEY))
        {
            SCI::updateConfigurationValue("SC_FOULEFACTORY_ID",$FF_ID);
            SCI::updateConfigurationValue("SC_FOULEFACTORY_APIKEY",$FF_APIKEY);

            echo json_encode(array("status"=>"success", "message"=>""));
        }
        else
        {
            echo json_encode(array("status"=>"error", "message"=>"Error during register. Try again later."));
        }
    }
    else
    {
        echo json_encode(array("status"=>"error", "message"=>$FF_return["message"]));
    }
}