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

/** This file is part of KCFinder project
  *
  *      @desc Base configuration file
  *   @package KCFinder
  *   @version 3.12
  *    @author Pavel Tzonkov <sunhater@sunhater.com>
  * @copyright 2010-2014 KCFinder Project
  *   @license http://opensource.org/licenses/GPL-3.0 GPLv3
  *   @license http://opensource.org/licenses/LGPL-3.0 LGPLv3
  *      @link http://kcfinder.sunhater.com
  */

/* IMPORTANT!!! Do not comment or remove uncommented settings in this file
   even if you are using session configuration.
   See http://kcfinder.sunhater.com/install for setting descriptions */

define('SC_DIR',dirname(__file__).'/');
if (strpos(SC_DIR,'modules')===false) // installation in /adminXXX/
{
	define('SC_INSTALL_MODE',0);
}else{ // installation in /modules/
	define('SC_INSTALL_MODE',1);
}

$_CONFIG = array(

		/*
		 * 'types' => array(

    // (F)CKEditor types
        'files'   =>  "",
        'flash'   =>  "swf",
        'images'  =>  "*img",

    // TinyMCE types
        'file'    =>  "",
        'media'   =>  "swf flv avi mpg mpeg qt mov wmv asf rm",
        'image'   =>  "*img",
    )
		 */

// GENERAL SETTINGS

    'disabled' => false,
    'uploadURL' => (SC_INSTALL_MODE==1?"../../../../../../../../":"../../../../../../"),
    'uploadDir' => (SC_INSTALL_MODE==1?realpath("../../../../../../../../"):realpath("../../../../../../")),
    'theme' => "default",

    'types' => array(
        'img'  =>  "pdf doc docx xls xlsx zip jpeg jpg gif bmp png"
    ),


// IMAGE SETTINGS

    'imageDriversPriority' => "imagick gmagick gd",
    'jpegQuality' => 90,
    'thumbsDir' => ".thumbs",

    'maxImageWidth' => 0,
    'maxImageHeight' => 0,

    'thumbWidth' => 100,
    'thumbHeight' => 100,

    'watermark' => "",


// DISABLE / ENABLE SETTINGS

    'denyZipDownload' => false,
    'denyUpdateCheck' => false,
    'denyExtensionRename' => false,


// PERMISSION SETTINGS

    'dirPerms' => 0755,
    'filePerms' => 0644,

    'access' => array(

        'files' => array(
            'upload' => true,
            'delete' => true,
            'copy'   => true,
            'move'   => true,
            'rename' => true
        ),

        'dirs' => array(
            'create' => true,
            'delete' => true,
            'rename' => true
        )
    ),

    'deniedExts' => "exe com msi bat cgi pl php phps phtml php3 php4 php5 php6 py pyc pyo pcgi pcgi3 pcgi4 pcgi5 pchi6",


// MISC SETTINGS

    'filenameChangeChars' => array(/*
        ' ' => "_",
        ':' => "."
    */),

    'dirnameChangeChars' => array(/*
        ' ' => "_",
        ':' => "."
    */),

    'mime_magic' => "",

    'cookieDomain' => "",
    'cookiePath' => "",
    'cookiePrefix' => 'KCFINDER_',


// THE FOLLOWING SETTINGS CANNOT BE OVERRIDED WITH SESSION SETTINGS

    '_normalizeFilenames' => false,
    '_check4htaccess' => false,
    //'_tinyMCEPath' => "/tiny_mce",

    '_sessionVar' => "KCFINDER",
    //'_sessionLifetime' => 30,
    //'_sessionDir' => "/full/directory/path",
    //'_sessionDomain' => ".mysite.com",
    //'_sessionPath' => "/my/path",

    //'_cssMinCmd' => "java -jar /path/to/yuicompressor.jar --type css {file}",
    //'_jsMinCmd' => "java -jar /path/to/yuicompressor.jar --type js {file}",

);

?>