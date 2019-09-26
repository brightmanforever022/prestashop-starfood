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
$id_lang = Tools::getValue("id_lang","");
$license_key = SCI::getConfigurationValue('SC_LICENSE_KEY', '');

$content = "No data";

if(!empty($id_lang) && !empty($license_key))
{
    $iso = strtolower($user_lang_iso);//strtolower(Language::getIsoById((int)$id_lang));
    $content = sc_file_get_contents("http://www.storecommander.com/trial/getTrialInfo.php?license=".$license_key."&lang_iso=".$iso);
}

echo $content;