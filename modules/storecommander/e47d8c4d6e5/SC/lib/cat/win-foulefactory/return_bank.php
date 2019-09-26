<?php
include_once(dirname(__FILE__) . '/../../config/config.inc.php');
include_once(dirname(__FILE__) . '/../../init.php');

require_once(Configuration::get('SC_FOLDER_HASH')."/SC/lib/php/foulefactory/FfProject.php");
require_once(Configuration::get('SC_FOLDER_HASH')."/SC/lib/php/foulefactory/FFApi.php");


$id_project = intval(Tools::getValue("id_project", ""));
$ResultCode = (Tools::getValue("ResultCode", ""));

if(!empty($id_project))
{
    //ResultCode
    $errors = true;

    $FFProject = new FfProject((int)$id_project);
    if(isset($ResultCode) && $ResultCode=="000000")
        $errors = false;

    if($errors==false)
    {
        $FFProject->status = "paid";

        $amount = $FFProject->tarif;

        Configuration::updateValue("SC_FOULEFACTORY_AMOUNT", $amount);

        $params = unserialize($FFProject->params);
        $nb_pdt = 0;
        $cat = new Category((int)$FFProject->id_category);
        $nb = $cat->getProducts($id_lang,1,1,null,null,true,false);
        if(!empty($nb))
            $nb_pdt = $nb;

        $content = "Boutique : ".$_SERVER['HTTP_HOST']."\n".
            "Projet : ".$FFProject->name."\n".
            "Qualité : ".$params["quality"]."\n" .
            "Nb. produits : ".$nb_pdt."\n".
            "Montant payé : ".$amount."\n";
        //mail("foulefactory@storecommander.com", "Paiement validé ".$_SERVER['HTTP_HOST'], utf8_decode($content));
    }
    else
        $FFProject->status = "error_payment";
    $FFProject->save();

    if($errors==false)
    {
        ?>
        <div style="width: 90%; margin: 20px; border: 1px solid #588c07; color: #588c07; padding: 20px; background: #f8ffe8;">
            <strong>Votre paiement a bien été accepté, vous pouvez fermer la fenêtre !</strong>
        </div>
        <?php
    }
    else
    {

        ?>
        <div style="width: 90%; margin: 20px; border: 1px solid #8a0000; color: #8a0000; padding: 20px; background: #ffe8e8;">
            <strong>!!! Attention !!! Une erreur s'est produite durant le paiement !</strong>
        </div>
        <?php
    }
}
else
{

    ?>
    <div style="width: 90%; margin: 20px; border: 1px solid #8a0000; color: #8a0000; padding: 20px; background: #ffe8e8;">
        <strong>!!! Attention !!! Une erreur s'est produite durant le paiement !</strong>
    </div>
    <?php
}