<?php
// if (!defined('_PS_VERSION_'))
//     exit;
  include_once('../../config/config.inc.php');
  include_once('../../init.php');
  include_once('inc_php/revslider_globals.class.php');
// defined('_PS_VERSION_') OR die('No Direct Script Access Allowed');
$action = Tools::getValue('action');
$mod_url = context::getcontext()->shop->getBaseURL()."modules/revsliderprestashop/";  
switch($action){
    case 'revsliderprestashop_show_image':
        $imgsrc = Tools::getValue('img');
        if($imgsrc){
            $imgsrc = str_replace('../','',  urldecode($imgsrc));
            if(strpos($imgsrc,'uploads') !== FALSE){
                $file = @getimagesize($imgsrc);

                if(!empty($file) && isset($file['mime'])){
                    $size = GlobalsRevSlider::IMAGE_SIZE_MEDIUM;
                    $filename = basename($imgsrc);
                    $filetitle = substr($filename,0,strrpos($filename,'.'));
                    $fileext = substr($filename,strrpos($filename,'.'));
                    
                    $newfile = "uploads/{$filetitle}-{$size}x{$size}{$fileext}";
                    
                    if($newfilesize = @getimagesize($newfile)){
                        $file = $newfilesize;
                        $imgsrc = $newfile;
                    }
                    header('Content-Type:'.$file['mime']);
                    echo file_get_contents($mod_url.$imgsrc);
                } 
            }
        }
        break;
}
die();
