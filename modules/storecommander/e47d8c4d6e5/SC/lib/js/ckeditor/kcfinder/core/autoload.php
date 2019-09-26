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
  *      @desc Autoload Classes
  *   @package KCFinder
  *   @version 3.12
  *    @author Pavel Tzonkov <sunhater@sunhater.com>
  * @copyright 2010-2014 KCFinder Project
  *   @license http://opensource.org/licenses/GPL-3.0 GPLv3
  *   @license http://opensource.org/licenses/LGPL-3.0 LGPLv3
  *      @link http://kcfinder.sunhater.com
  */

spl_autoload_register(function($path) {
    $path = explode("\\", $path);

    if (count($path) == 1)
        return;

    list($ns, $class) = $path;

    if ($ns == "kcfinder") {

        if ($class == "uploader")
            require "core/class/uploader.php";
        elseif ($class == "browser")
            require "core/class/browser.php";
        elseif ($class == "minifier")
            require "core/class/minifier.php";

        elseif (file_exists("core/types/$class.php"))
            require "core/types/$class.php";
        elseif (file_exists("lib/class_$class.php"))
            require "lib/class_$class.php";
        elseif (file_exists("lib/helper_$class.php"))
            require "lib/helper_$class.php";
    }
});

?>