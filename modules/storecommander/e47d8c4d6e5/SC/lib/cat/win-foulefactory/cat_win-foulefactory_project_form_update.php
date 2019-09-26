<?php
require_once ("lib/php/foulefactory/FfProject.php");
if($user_lang_iso=="fr")
require_once (dirname(__FILE__)."/".$user_lang_iso.'.php');

$id_project=intval(Tools::getValue('id_project'));

if(!empty($id_project))
{
    $project = new FfProject((int)$id_project);
    $params = unserialize($project->params);

    $instructions=(Tools::getValue('instructions'));
    if(!empty($instructions))
        $project->instructions = $instructions;

    $source=Tools::getValue('source',"-");
    $project->source = $source;

    $params["undefined"]=Tools::getValue('undefined',"");

    if($project->type=="feature")
    {
        $params["quality"]=Tools::getValue('quality',"good");

        $id_feature=intval(Tools::getValue('id_feature'));
        if(!empty($id_feature))
            $params["id_feature"] = $id_feature;

        $feature_values_array=(Tools::getValue('feature_values'));
        if(!empty($feature_values_array))
        {
            $feature_values = "-";
            foreach($feature_values_array as $value)
                $feature_values .= $value."-";
            $params["feature_values"] = $feature_values;
        }

        $feature_after_process=(Tools::getValue('feature_after_process'));
        if(!empty($feature_after_process))
            $params["feature_after_process"] = $feature_after_process;
    }
    elseif($project->type=="desc_short")
    {
        $params["quality"]=Tools::getValue('quality',"10");
    }
    elseif($project->type=="desc_long")
    {
        $params["quality"]=Tools::getValue('quality',"50");
    }

    $project->status = "configured";
    $project->tarif = "";
    $project->params = serialize($params);
    $project->updated_at = date("Y-m-d");
    $project->save();
}