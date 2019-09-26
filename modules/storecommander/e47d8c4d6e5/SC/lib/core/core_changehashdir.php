<?php

$current_url = Tools::getValue("current_url");

$newHash = Tools::substr(md5(date("YmdHis") . _COOKIE_KEY_), 0, 11);
$oldHash = Configuration::get('SC_FOLDER_HASH');

$exp = explode($oldHash,SC_DIR);
$dir_base = $exp[0];

$old_dir = $dir_base.$oldHash;
$new_dir = $dir_base.$newHash;

if(rename($old_dir, $new_dir))
{
    Configuration::updateValue('SC_FOLDER_HASH', $newHash);

    echo str_replace($oldHash,$newHash, $current_url);
}