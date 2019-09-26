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
$type = Tools::getValue('type');
$infos = Tools::getValue('infos');

if(!empty($type))
{
    $licence = SCI::getConfigurationValue('SC_LICENSE_KEY');
    if(empty($licence))
        $licence = "demo";
    $post = array(
        "licence" => $licence
        , "email" => $sc_agent->email
        , "type" => $type
    );
    if(!empty($infos))
        $post['infos'] = $infos;
    $headers = array();
    sc_file_post_contents('http://api.storecommander.com/Tracking/InsertRow', $post, $headers);
}