<?php

$id_lang=intval(Tools::getValue('id_lang'));
$id_project=intval(Tools::getValue('id_project'));

if($user_lang_iso=="fr")
require_once (dirname(__FILE__)."/".$user_lang_iso.'.php');

if(!empty($id_project))
{
    require_once ("lib/php/foulefactory/FfProject.php");

    $project = new FfProject((int)$id_project);
    $params = unserialize($project->params);
    $default_params = array();
    $SC_FOULEFACTORY_DEFAULT_VALUES = SCI::getConfigurationValue("SC_FOULEFACTORY_DEFAULT_VALUES");
    if(!empty($SC_FOULEFACTORY_DEFAULT_VALUES))
        $default_params = unserialize($SC_FOULEFACTORY_DEFAULT_VALUES);

    if(empty($project->source) && !empty($default_params["source"]))
        $project->source = $default_params["source"];
    if(empty($params["undefined"]) && !empty($default_params["undefined"]))
        $params["undefined"] = $default_params["undefined"];
    if(empty($params["quality"]) && !empty($default_params["quality"]))
        $params["quality"] = $default_params["quality"];

    if($project->type=="feature")
        require_once(dirname(__FILE__)."/projecttypes/feature.php");
    elseif($project->type=="desc_short")
        require_once(dirname(__FILE__)."/projecttypes/desc_short.php");
    elseif($project->type=="desc_long")
        require_once(dirname(__FILE__)."/projecttypes/desc_long.php");
}