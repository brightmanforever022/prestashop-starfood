<?php
$id_lang=intval(Tools::getValue('id_lang'));
$action=(Tools::getValue('action'));

require_once ("lib/php/foulefactory/FfProject.php");

if($user_lang_iso=="fr")
require_once (dirname(__FILE__)."/".$user_lang_iso.'.php');

$return = array();

if(!empty($action) && $action=="insert")
{
    $tempId=(Tools::getValue('tempId'));
    $name=(Tools::getValue('name'));
    $autocreate=(Tools::getValue('autocreate',''));

    if(!empty($name))
    {
        $p = new FfProject();
        $p->name = $name;
        $p->type = "feature";
        $p->add();

        if(!empty($autocreate))
        {
            list($type,$values) = explode("-", $autocreate);
            list($type,$id_type) = explode("_", $type);

            if($type=="feature")
            {
                $p->type = "feature";

                $exp = explode("_", $values);
                if($exp[0]=="pdt")
                    $products = $exp[1];

                if(!empty($id_type) && !empty($products) && !empty($p->id_category))
                {
                    $params = array();
                    $params["id_feature"] = (int)$id_type;

                    $p->params = serialize($params);
                    $p->updated_at = date("Y-m-d");
                    $p->save();

                    $products = explode(",", $products);
                    foreach($products as $product)
                    {
                        if(!empty($product))
                        {
                            $sql = "INSERT INTO `"._DB_PREFIX_."category_product` (id_product,id_category,position)
                            VALUES ('".(int)$product."','".(int)$p->id_category."',0)";
                            Db::getInstance()->Execute($sql);
                        }
                    }
                }
            }
        }

        $return=array(
            "tempId"=>  $tempId,
            "newId"=>$p->id
        );
    }
}
elseif(!empty($action) && $action=="update")
{
    $id_project=intval(Tools::getValue('id_project'));
    $field=(Tools::getValue('field'));
    $val=(Tools::getValue('val'));

    if(!empty($id_project) && !empty($field))
    {
        if($field=="status_update")
            $field = "status";

        $p = new FfProject($id_project);
        $p->$field = $val;
        $p->updated_at = date("Y-m-d");
        $p->save();

        if($field=="name")
        {
            $cat = new Category((int)$p->id_category);
            foreach($cat->name as $id_lang=>$v)
                $cat->name[$id_lang] = $val;
            $cat->save();
        }
    }
}

echo json_encode($return);