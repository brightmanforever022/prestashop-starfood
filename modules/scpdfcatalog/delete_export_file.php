<?php
include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');
$sec_key_tocheck = md5(_COOKIE_KEY_ . date('Ymd'));

$get_sk = Tools::getValue('sk');
if ($get_sk != $sec_key_tocheck)
    return;

$dirname = dirname(__FILE__)."/export/";
$get_f =  Tools::getValue('f');
$file_to_delete = $dirname . $get_f;

if (!file_exists($file_to_delete)) {   
    return;
} else {
  
    if (@(unlink($file_to_delete))) {
        echo 1;
    } else {
        echo 0;
    }
}
?>