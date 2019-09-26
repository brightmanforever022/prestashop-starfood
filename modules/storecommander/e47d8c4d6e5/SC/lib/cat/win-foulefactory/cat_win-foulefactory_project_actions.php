<?php

ini_set("display_errors", "ON");

$id_lang=intval(Tools::getValue('id_lang'));
$action=(Tools::getValue('action'));
require_once("lib/php/foulefactory/FfProject.php");
require_once("lib/php/foulefactory/FFApi.php");

if($user_lang_iso=="fr")
require_once (dirname(__FILE__)."/".$user_lang_iso.'.php');

$FF_ID = SCI::getConfigurationValue("SC_FOULEFACTORY_ID");
$FF_APIKEY =SCI::getConfigurationValue("SC_FOULEFACTORY_APIKEY");

$link = new Link();

if($action=="get_quote")
{
    $projects=(Tools::getValue('id_project'));
    if(!empty($projects))
    {
        $projects = explode(",",trim(trim($projects,",")));
        foreach($projects as $id_project)
        {
            $project = new FfProject((int)$id_project);
            $params = unserialize($project->params);

            if(in_array($project->status, array("configured","to_pay")))
            {
                $error = null;

                $id_project_reseller = FFApi::getIdProjectReseller($params["quality"], $project->type);
                if(!empty($id_project_reseller))
                {
                    // FF GET PRICE
                    $cat = new Category((int)$project->id_category);
                    $nb = $cat->getProducts($id_lang,1,1,null,null,true,false);
                    if(!empty($nb) && $nb>0)
                    {
                        $api = new FFApi($FF_ID,$FF_APIKEY);
                        $sub_url = "projectResellers/".$id_project_reseller."/quote/".$nb;
                        $FF_return = $api->queryGet($sub_url);
                        if($FF_return['status_code']=='200')
                        {
                            $obj_price = $FF_return["response"];

                            if(!empty($obj_price->AmountWithoutTax) && $obj_price->AmountWithoutTax>0)
                            {
                                $price = $obj_price->AmountWithoutTax/100;
                                if(!empty($obj_price->AmountTaxes) && $obj_price->AmountTaxes>0)
                                    $price = $price + ($obj_price->AmountTaxes/100);

                                if(!empty($price) && $price>0)
                                {
                                    $project->tarif = $price;
                                    $project->status = "to_pay";
                                    $project->save();
                                }
                                else
                                    $error = "005";
                            }
                            else
                                $error = "004";
                        }
                        else
                            die(json_encode(array("status"=>"error", "message"=>$FF_return["message"])));
                    }
                    else
                        die(json_encode(array("status"=>"error", "message"=>_l("You must add products in Project Category"))));
                }
                else
                    $error = "001";

                if(empty($error))
                    die(json_encode(array("status"=>"success", "message"=>"")));
                else
                    die(json_encode(array("status"=>"error", "message"=>"Error during get price (#".$error.")")));
            }
        }
    }
}
elseif($action=="pay")
{
    $id_project=intval(Tools::getValue('id_project'));
    if(!empty($id_project))
    {
        $project = new FfProject((int)$id_project);
        $params = unserialize($project->params);

        if(in_array($project->status, array("to_pay","error_payment","waiting_payment")))
        {
            $cat = new Category((int)$project->id_category);
            $nb = $cat->getProducts($id_lang,1,1,null,null,true,false);
            $project->nb_product = $nb;

            $url = null;
            $error = null;

            /* Server Params */
            $server_host = Tools::getHttpHost(true);
            $protocol = 'http://';
            $protocol_ssl = 'https://';
            $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? $protocol_ssl : $protocol;
            if (SCMS)
            {
                $selected_shops_id = (int)Configuration::get('PS_SHOP_DEFAULT');
                $shop=new Shop((int)$selected_shops_id);
                $url_return = $protocol_link.$shop->domain.$shop->getBaseURI().'modules/storecommander/return_bank.php?id_project='.$id_project;
            }else{
                $url_return = $protocol_link.Tools::getShopDomain(false).__PS_BASE_URI__."modules/storecommander/return_bank.php?id_project=".$id_project;
            }

            $api = new FFApi($FF_ID,$FF_APIKEY);
            $sub_url = "accounts/payin";
            $curl_post_data = array(
                'Amount' => $project->tarif*100,
                'ReturnUrl' => $url_return
            );
            $FF_return = $api->queryPost($sub_url, $curl_post_data);

            if($FF_return['status_code']=='200')
            {
                $obj_payment = $FF_return["response"];

                if(!empty($obj_payment->RedirectionURL))
                    $url = $obj_payment->RedirectionURL;
                else
                    $error = "001";
            }
            else
                die(json_encode(array("status"=>"error", "message"=>$FF_return["message"])));

            if(empty($url))
                $error = "002";

            if(empty($error))
            {
                $project->status = "waiting_payment";//"paid";
                $project->save();

                die(json_encode(array("status"=>"success_toPay", "message"=>"", "url"=>$url)));
            }
            else
                die(json_encode(array("status"=>"error", "message"=>"Error during payment (#".$error.")")));
        }
    }
}
elseif($action=="start")
{

    $id_project=intval(Tools::getValue('id_project'));
    if(!empty($id_project))
    {
        $project = new FfProject((int)$id_project);
        $params = unserialize($project->params);

        $cat = new Category((int)$project->id_category);
        $products = $cat->getProducts((int)$id_lang,1,100000000,null,null,false,false);

        if(in_array($project->status, array("paid")) && $project->nb_product==count($products))
        {
            $api = new FFApi($FF_ID,$FF_APIKEY);

            // FF CREATE PROJECT
            if(empty($project->id_ff_project))
            {
                $id_project_reseller = FFApi::getIdProjectReseller($params["quality"], $project->type);
                $sub_url = "projectResellers";
                $curl_post_data = array(
                    'idProjectReseller' => $id_project_reseller
                );
                $FF_return = $api->queryPost($sub_url, $curl_post_data);
                if($FF_return['status_code']=='200')
                {
                    $obj_project = $FF_return["response"];

                    $id_ff_project = $obj_project->IdProject;

                    if(!empty($id_ff_project))
                    {
                        $project->id_ff_project = $id_ff_project;
                        $project->save();

                    }
                    else
                    {
                        die(json_encode(array("status"=>"error", "message"=>"Error during FouleFactory project creation.")));
                    }
                }
                else
                {
                    die(json_encode(array("status"=>"error", "message"=>$FF_return["message"])));
                }
            }

            // FF SEND CSV
            if(!empty($project->id_ff_project))
            {
                if($project->type=="desc_short" || $project->type=="desc_long")
                {
                    // CSV CREATE
                    $csv = '"ID";"Nom Produit";"URL";"Instructions"'."\n";

                    // INSTRUCTIONS
                    $instructions = '"';
                    if($project->type=="desc_short")
                    {
                        $instructions .= _l("Write short description");
                        $id_lang_french = $id_lang;
                        $id_lang_temp = Language::getIdByIso("fr");
                        if(!empty($id_lang_temp))
                            $id_lang_french = $id_lang_temp;
                    }
                    elseif($project->type=="desc_long")
                    {
                        $instructions .= _l("Write long description");
                        $id_lang_french = $id_lang;
                        $id_lang_temp = Language::getIdByIso("fr");
                        if(!empty($id_lang_temp))
                            $id_lang_french = $id_lang_temp;
                    }

                    if(!empty($project->instructions))
                        $instructions .= ' '.$project->instructions;
                    $instructions .= '"';
                }
                elseif($project->type=="feature")
                {
                    // CSV CREATE
                    $csv = '"ID";"Source texte 01";"Source texte 02";"Source texte 03";"Source texte 04";"Source texte 05";"Source image 01";"Source image 02";"Source image 03";"Source image 04";"Source image 05";"Instructions"'."\n";

                    $sources = explode("-",trim(trim($project->source,"-")));
                    $has_shortdesc = false;
                    if(in_array("shortdesc",$sources))
                        $has_shortdesc = true;
                    $has_desc = false;
                    if(in_array("desc",$sources))
                        $has_desc = true;
                    $has_pdt_url = false;
                    if(in_array("none",$sources))
                        $has_pdt_url = true;

                    // IMAGES
                    $has_img = false;
                    if(in_array("img",$sources))
                    {
                        $server_host = Tools::getHttpHost(true);
                        $protocol = 'http://';
                        $protocol_ssl = 'https://';
                        $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? $protocol_ssl : $protocol;
                        if (SCMS)
                        {
                            $selected_shops_id = (int)Configuration::get('PS_SHOP_DEFAULT');
                            $shop=new Shop((int)$selected_shops_id);
                            $_PS_BASE_URL_ = $protocol_link.$shop->domain.$shop->getBaseURI().'img/p/';
                        }else{
                            $_PS_BASE_URL_ = $protocol_link.Tools::getShopDomain(false)._THEME_PROD_DIR_;
                        }
                        $has_img = true;
                    }

                    // INSTRUCTIONS
                    $instructions = '';

                    if(!empty($params["id_feature"]))
                    {
                        $feature = new Feature((int)$params["id_feature"], (int)$id_lang);

                        $instructions .= '"'._l("The feature to enter is:").' '.str_replace('"','""',$feature->name).'. ';

                        if(!empty($params["feature_values"]))
                        {
                            $feature_values = array();
                            $id_feature_values = explode("-",trim(trim($params["feature_values"],"-")));
                            foreach($id_feature_values as $id_feature_value)
                            {
                                if(!empty($id_feature_value))
                                {
                                    $feature_value = new FeatureValue((int)$id_feature_value, (int)$id_lang);
                                    $feature_values[] = str_replace('"','""',$feature_value->value);
                                }
                            }
                            if(!empty($feature_values) && count($feature_values)>0)
                                $instructions .= "\n"."\n"._l('Possible values for this features are:')."\n".implode(",",$feature_values).'. ';
                        }

                        if(!empty($project->instructions))
                            $instructions .= "\n"."\n"._l('Additionnal instructions').': '.$project->instructions;
                        //$instructions .= "\n"."\n"._l('If the value is not found, enter \'NOTFOUND\'.').'"';
                    }
                    $instructions .= '"';
                }
                else
                    die(json_encode(array("status"=>"error", "message"=>"Error during project start (#002)")));

                foreach($products as $product)
                {
                    $row = '"'.$product["id_product"].'"';

                    if($project->type=="desc_short" || $project->type=="desc_long")
                    {
                        $row .= ';"'.str_replace('"','""',$product['name']).'"';

                        $p = new Product((int)$product["id_product"]);
                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            $url = $link->getProductLink($p, null,null,null, $id_lang_french, $p->id_shop_default);
                        else
                            $url = $link->getProductLink($p, null,null,null, $id_lang_french);
                        $row .= ';"'.$url.'"';
                    }
                    elseif($project->type=="feature")
                    {
                        // SOURCES
                        $row_sources = array("","","","","");
                        $i_sources = 0;

                        $row_sources[$i_sources]=str_replace('"','""',$product['name']);
                        $i_sources++;

                        if(!empty($has_shortdesc))
                        {
                            $text = $product['description_short'];
                            $text = str_replace("<br/>","\n", $text);
                            $text = str_replace("<br />","\n", $text);
                            $text = str_replace("<br>","\n", $text);
                            $text = str_replace("<br >","\n", $text);
                            $text = str_replace("</p>","\n", $text);
                            $text = str_replace("</p>","\n", $text);
                            $text = str_replace('"','""',strip_tags($text));
                            $text = html_entity_decode($text);
                            $row_sources[$i_sources]=$text;
                            $i_sources++;
                        }
                        if(!empty($has_desc))
                        {
                            $text = $product['description'];
                            $text = str_replace("<br/>","\n", $text);
                            $text = str_replace("<br />","\n", $text);
                            $text = str_replace("<br>","\n", $text);
                            $text = str_replace("<br >","\n", $text);
                            $text = str_replace("</p>","\n", $text);
                            $text = str_replace("</p>","\n", $text);
                            $text = str_replace('"','""',strip_tags($text));
                            $text = html_entity_decode($text);
                            $row_sources[$i_sources]=$text;
                            $i_sources++;
                        }
                        if(!empty($has_pdt_url))
                        {
                            $p = new Product((int)$product["id_product"]);
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                $url = $link->getProductLink($p, null,null,null, $id_lang_french, $p->id_shop_default);
                            else
                                $url = $link->getProductLink($p, null,null,null, $id_lang_french);
                            $row_sources[$i_sources]=$url;
                            $i_sources++;
                        }

                        foreach($row_sources as $row_source)
                            $row .= ';"'.$row_source.'"';

                        // IMAGES
                        $row_images = array("","","","","");
                        $i_images = 0;

                        if(!empty($has_img))
                        {
                            $sql = ' SELECT * FROM `'._DB_PREFIX_.'image` WHERE `id_product` = "'.(int)$product["id_product"].'" ORDER BY position LIMIT 5';
                            $imgs=Db::getInstance()->ExecuteS($sql);
                            foreach($imgs as $img)
                            {
                                $row_images[$i_images]=$_PS_BASE_URL_.getImgPath((int)$product["id_product"],(int)$img['id_image'],_s("CAT_EXPORT_IMAGE_FORMAT"),'jpg');
                                $i_images++;
                            }
                        }

                        foreach($row_images as $row_image)
                            $row .= ';'.$row_image.'';
                    }

                    $row .= ';'.$instructions;

                    $row=trim(preg_replace('/ {2,}/', ' ', str_replace("\n", " ", str_replace("\r", " ", preg_replace ('/<[^>]*>/', ' ', $row)))));

                    if(!empty($row))
                        $csv .= $row."\n";
                }

                // SENT TO FF
                if(!empty($csv))
                {
                    $sub_url = "csvFiles";
                    $curl_post_data = array(
                        'IdProject' => $project->id_ff_project,
                        "Header" => true,
                        "Separator" => ";",
                        "File" => base64_encode($csv)
                    );
                    $FF_return = $api->queryPost($sub_url, $curl_post_data);
                    if($FF_return['status_code']!='200')
                    {
                        die(json_encode(array("status"=>"error", "message"=>$FF_return["message"])));
                    }
                }
            }
            else
            {
                die(json_encode(array("status"=>"error", "message"=>"Error during project start (#001)")));
            }

            // DURATION PROJECT
            $duration = 120;
            $cat = new Category((int)$project->id_category);
            $nb = $cat->getProducts($id_lang,1,1,null,null,true,false);
            $calculation = $nb * (1+$params["quality"]) /10;
            if($calculation>$duration)
                $duration = $calculation;

            $project->duration = $duration;
            $project->started_at = date("Y-m-d H:i:s");

            $project->status = "processing";
            $project->save();
        }
        elseif(in_array($project->status, array("paid")) && $project->nb_product!=count($products))
        {
            die(json_encode(array("status"=>"error", "message"=>_l("You modified the number of products to be handled, the paid price is not correct any more. You paid for ").$project->nb_product._l(" products."))));
        }
    }
}
elseif($action=="put_archived")
{
    $projects=(Tools::getValue('id_project'));
    if(!empty($projects))
    {
        $FF_cat_archived = SCI::getConfigurationValue("SC_FOULEFACTORY_CATEGORYARCHIVED");
        $projects = explode(",",trim(trim($projects,",")));
        foreach($projects as $id_project)
        {
            $project = new FfProject((int)$id_project);
            $project->status = "archived";
            $project->save();

            $cat = new Category((int)$project->id_category);
            $cat->id_parent=$FF_cat_archived;
            $cat->save();
        }
    }
}
elseif($action=="imported")
{
    $projects=(Tools::getValue('id_project'));
    if(!empty($projects))
    {
        $col_id = 0;
        $col_value = 1;
        $notfound_value = "notfound";
        $found_value = "found";

        $projects = explode(",",trim(trim($projects,",")));
        foreach($projects as $id_project)
        {
            $project = new FfProject((int)$id_project);
            $params = unserialize($project->params);

            $sql = "SELECT id_product FROM "._DB_PREFIX_."category_product WHERE id_category='".(int)$project->id_category."'";
            $products_temp = Db::getInstance()->ExecuteS($sql);
            $products = array();
            foreach($products_temp as $p)
                $products[] = $p["id_product"];

            // INDETERMINATED CATEGORY
            $indeterminated_id_cat = 0;
            $sql = "SELECT id_category FROM "._DB_PREFIX_."category WHERE id_parent='".(int)$project->id_category."'";
            $indeterminated_cat = Db::getInstance()->ExecuteS($sql);
            if(empty($indeterminated_cat[0]["id_category"]))
            {
                $newcategory=new Category();
                $newcategory->id_parent=(int)$project->id_category;
                $newcategory->level_depth=$newcategory->calcLevelDepth();
                $newcategory->active=0;

                if (SCMS)
                {
                    $shops = Shop::getShops(false,null,true);
                    $newcategory->id_shop_list = $shops;
                }

                $languages = Language::getLanguages(true);
                $name = _l("Indeterminated");
                foreach($languages AS $lang)
                {
                    $newcategory->link_rewrite[$lang['id_lang']]=link_rewrite($name);
                    $newcategory->name[$lang['id_lang']]=$name;
                }
                $newcategory->add();

                if (!in_array(1,$newcategory->getGroups()))
                    $newcategory->addGroups(array(1));

                $indeterminated_id_cat = $newcategory->id;
            }
            else
                $indeterminated_id_cat = $indeterminated_cat[0]["id_category"];

            // GET ARRAY
            $ff_infos = array();
            $api = new FFApi($FF_ID,$FF_APIKEY);
            $sub_url = "projects/".$project->id_ff_project."/taskLines";
            $FF_return = $api->queryGet($sub_url);
            if($FF_return['status_code']=='200')
            {
                if(!empty($FF_return['response']) && count($FF_return['response'])>0)
                {
                    foreach($FF_return['response'] as $taskline)
                    {
                        //if(!empty($taskline->TaskLinesAnswers) && count($taskline->TaskLinesAnswers)==2)
                        if($project->type=="feature" && !empty($taskline->TaskLinesAnswers) && count($taskline->TaskLinesAnswers)==2)
                        {
                            if(!empty($taskline->TaskColumns[0]))
                            {
                                $id_product = str_replace("&quot;", "", $taskline->TaskColumns[0]);
                                $founded = trim(strtolower($taskline->TaskLinesAnswers[0][1]));
                                if(!empty($founded) && $founded==$found_value && !empty($taskline->TaskLinesAnswers[1][1]))
                                    $ff_infos[] = array($id_product, trim($taskline->TaskLinesAnswers[1][1]));
                                else
                                    $ff_infos[] = array($id_product, $notfound_value);
                            }


                        }
                        elseif(($project->type=="desc_short" || $this->$project=="desc_long") && !empty($taskline->TaskLinesAnswers[0][1]))
                        {
                            if(!empty($taskline->TaskColumns[0]))
                            {
                                $id_product = str_replace("&quot;", "", $taskline->TaskColumns[0]);
                                $ff_infos[] = array($id_product, trim($taskline->TaskLinesAnswers[0][1]));
                            }


                        }
                    }
                }
            }

            if($project->type=="desc_short" || $project->type=="desc_long")
            {
                $id_lang_french = $id_lang;
                $id_lang_temp = Language::getIdByIso("fr");
                if(!empty($id_lang_temp))
                    $id_lang_french = $id_lang_temp;
            }

            // TREATMENT
            foreach($ff_infos as $infos_row)
            {
                if(!empty($infos_row[$col_id]))
                {
                    $id_product = $infos_row[$col_id];
                    if(in_array($id_product, $products))
                    {
                        $value = trim($infos_row[$col_value]);

                        // IF NO VALUE
                        if(empty($value) || strtolower($value)==$notfound_value)
                        {
                            if(!empty($params["undefined"]) && $params["undefined"]=="remove")
                            {
                                $sql = "DELETE FROM "._DB_PREFIX_."category_product WHERE id_category='".(int)$project->id_category."' AND id_product='".(int)$id_product."'";
                                Db::getInstance()->Execute($sql);
                            }
                            elseif(!empty($params["undefined"]) && $params["undefined"]=="subcat" && !empty($indeterminated_id_cat))
                            {
                                $sql = "UPDATE "._DB_PREFIX_."category_product SET id_category=".(int)$indeterminated_id_cat." WHERE id_category='".(int)$project->id_category."' AND id_product='".(int)$id_product."'";
                                Db::getInstance()->Execute($sql);
                            }
                        }
                        // INSERT VALUE
                        else
                        {
                            // FEATURE
                            if($project->type=="feature" && !empty($params["id_feature"]))
                            {
                                $id_feature_value = 0;

                                // CHECK IF FEATURE VALUE ALREADY EXIST
                                $sql = "SELECT fv.id_feature_value
                                FROM "._DB_PREFIX_."feature_value fv
                                    INNER JOIN "._DB_PREFIX_."feature_value_lang fvl ON (fv.id_feature_value=fvl.id_feature_value)
                                WHERE
                                    fv.custom = 0
                                    AND fv.id_feature='".(int)$params["id_feature"]."'
                                    AND LOWER(fvl.value) = '".pSQL(strtolower($value))."'";
                                $fv_exist = Db::getInstance()->ExecuteS($sql);
                                if(!empty($fv_exist[0]["id_feature_value"]))
                                {
                                    $id_feature_value = $fv_exist[0]["id_feature_value"];
                                }
                                else
                                {
                                    if(!empty($params["feature_after_process"]) && $params["feature_after_process"]=="add_feature_value")
                                    {
                                        $new_fv = new FeatureValue();
                                        $new_fv->id_feature = (int)$params["id_feature"];
                                        $new_fv->custom = 0;
                                        foreach($languages AS $lang)
                                            $new_fv->value[$lang['id_lang']]=$value;
                                        $new_fv->add();
                                        $id_feature_value = $new_fv->id;
                                    }
                                    elseif(!empty($params["feature_after_process"]) && $params["feature_after_process"]=="add_custom_feature_value")
                                    {
                                        $new_fv = new FeatureValue();
                                        $new_fv->id_feature = (int)$params["id_feature"];
                                        $new_fv->custom = 1;
                                        foreach($languages AS $lang)
                                            $new_fv->value[$lang['id_lang']]=$value;
                                        $new_fv->add();
                                        $id_feature_value = $new_fv->id;
                                    }
                                }

                                // ADD FEATURE VALUE TO PRODUCT
                                if(!empty($id_feature_value))
                                {
                                    $sql = "SELECT * FROM "._DB_PREFIX_."feature_product WHERE id_feature='".(int)$params["id_feature"]."' AND id_product='".(int)$id_product."'";
                                    $fp_exist = Db::getInstance()->ExecuteS($sql);
                                    if(!empty($fp_exist[0]["id_feature"]))
                                    {
                                        $sql = "UPDATE "._DB_PREFIX_."feature_product SET id_feature_value=".(int)$id_feature_value." WHERE  id_feature='".(int)$params["id_feature"]."' AND id_product='".(int)$id_product."'";
                                        Db::getInstance()->Execute($sql);
                                    }
                                    else
                                    {
                                        $sql = "INSERT INTO "._DB_PREFIX_."feature_product (id_feature,id_product,id_feature_value)
                                        VALUES ('".(int)$params["id_feature"]."', '".(int)$id_product."', '".(int)$id_feature_value."')";
                                        Db::getInstance()->Execute($sql);
                                    }
                                }
                            }
                            // DESCRIPTION COURTE
                            elseif($project->type=="desc_short")
                            {
                                $sql = "SELECT * FROM "._DB_PREFIX_."product_lang WHERE id_lang='".(int)$id_lang_french."' AND id_product='".(int)$id_product."'";
                                $pl_exist = Db::getInstance()->ExecuteS($sql);
                                if(SCMS)
                                {
                                    if(!empty($pl_exist) && count($pl_exist)>0)
                                    {
                                        foreach ($pl_exist as $pl)
                                        {
                                            $sql = "UPDATE "._DB_PREFIX_."product_lang SET description_short='".pSQL($value)."' WHERE id_lang='".(int)$id_lang_french."' AND id_product='".(int)$id_product."' AND id_shop='".(int)$pl["id_shop"]."'";
                                            Db::getInstance()->Execute($sql);
                                            addToHistory('cat_prop','modification','description_short',(int)$id_product,intval($id_lang_french),_DB_PREFIX_."product_lang",$value,$pl['description_short'],(int)$pl["id_shop"]);
                                        }
                                      }
                                }
                                else
                                {
                                    if(!empty($pl_exist[0]["id_product"]))
                                    {
                                        $sql = "UPDATE "._DB_PREFIX_."product_lang SET description_short='".pSQL($value)."' WHERE id_lang='".(int)$id_lang_french."' AND id_product='".(int)$id_product."'";
                                        Db::getInstance()->Execute($sql);
                                        addToHistory('cat_prop','modification','description_short',(int)$id_product,intval($id_lang_french),_DB_PREFIX_."product_lang",$value,$pl_exist[0]['description_short']);
                                    }
                                }
                            }
                            // DESCRIPTION LONGUE
                            elseif($project->type=="desc_long")
                            {
                                $sql = "SELECT * FROM "._DB_PREFIX_."product_lang WHERE id_lang='".(int)$id_lang_french."' AND id_product='".(int)$id_product."'";
                                $pl_exist = Db::getInstance()->ExecuteS($sql);
                                if(SCMS)
                                {
                                    if(!empty($pl_exist) && count($pl_exist)>0)
                                    {
                                        foreach ($pl_exist as $pl)
                                        {
                                            $sql = "UPDATE "._DB_PREFIX_."product_lang SET description='".pSQL($value)."' WHERE id_lang='".(int)$id_lang_french."' AND id_product='".(int)$id_product."' AND id_shop='".(int)$pl["id_shop"]."'";
                                            Db::getInstance()->Execute($sql);
                                            addToHistory('cat_prop','modification','description',(int)$id_product,intval($id_lang_french),_DB_PREFIX_."product_lang",$value,$pl['description'],(int)$pl["id_shop"]);
                                        }
                                    }
                                }
                                else
                                {
                                    if(!empty($pl_exist[0]["id_product"]))
                                    {
                                        $sql = "UPDATE "._DB_PREFIX_."product_lang SET description='".pSQL($value)."' WHERE id_lang='".(int)$id_lang_french."' AND id_product='".(int)$id_product."'";
                                        Db::getInstance()->Execute($sql);
                                        addToHistory('cat_prop','modification','description',(int)$id_product,intval($id_lang_french),_DB_PREFIX_."product_lang",$value,$pl_exist[0]['description']);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // CHECK INDERTERMINATED
            $sql = "SELECT id_product FROM "._DB_PREFIX_."category_product WHERE id_category='".(int)$indeterminated_id_cat."'";
            $products_inde = Db::getInstance()->ExecuteS($sql);
            if(empty($products_inde) || count($products_inde)==0)
            {
                $indeterminated_cat = new Category((int)$indeterminated_id_cat);
                $indeterminated_cat->delete();
            }

            // PROJECT
            $project->status = "imported";
            $project->save();
            die(json_encode(array("status"=>"imported", "message"=>"")));
        }
    }
}