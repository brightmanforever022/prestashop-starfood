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
  *      @desc MIME type detection class
  *   @package KCFinder
  *   @version 3.12
  *    @author Pavel Tzonkov <sunhater@sunhater.com>
  * @copyright 2010-2014 KCFinder Project
  *   @license http://opensource.org/licenses/GPL-3.0 GPLv3
  *   @license http://opensource.org/licenses/LGPL-3.0 LGPLv3
  *      @link http://kcfinder.sunhater.com
  */

namespace kcfinder;

class type_mime {

    public function checkFile($file, array $config) {
        if (!class_exists("finfo"))
            return "Fileinfo PECL extension is missing.";

        if (!isset($config['params']))
            return "Undefined MIME types.";

        $finfo = strlen($config['mime_magic'])
            ? new \finfo(FILEINFO_MIME, $config['mime_magic'])
            : new \finfo(FILEINFO_MIME);
        if (!$finfo)
            return "Opening fileinfo database failed.";

        $type = $finfo->file($file);
        $type = substr($type, 0, strrpos($type, ";"));

        $mimes = $config['params'];
        if (substr($mimes, 0, 1) == "!") {
            $mimes = trim(substr($mimes, 1));
            return in_array($type , explode(" ", $mimes))
                ? "You can't upload such files."
                : true;
        }

        return !in_array($type , explode(" ", $mimes))
            ? "You can't upload such files."
            : true;
    }
}

?>