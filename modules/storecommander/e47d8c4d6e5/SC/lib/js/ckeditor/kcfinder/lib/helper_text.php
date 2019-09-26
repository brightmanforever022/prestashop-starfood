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
  *      @desc Text processing helper class
  *   @package KCFinder
  *   @version 3.12
  *    @author Pavel Tzonkov <sunhater@sunhater.com>
  * @copyright 2010-2014 KCFinder Project
  *   @license http://opensource.org/licenses/GPL-3.0 GPLv3
  *   @license http://opensource.org/licenses/LGPL-3.0 LGPLv3
  *      @link http://kcfinder.sunhater.com
  */

namespace kcfinder;

class text {

/** Replace repeated white spaces to single space
  * @param string $string
  * @return string */

    static function clearWhitespaces($string) {
        return trim(preg_replace('/\s+/s', " ", $string));
    }

/** Normalize the string for HTML attribute value
  * @param string $string
  * @return string */

    static function htmlValue($string) {
        return
            str_replace('"', "&quot;",
            str_replace("'", '&#39;',
            str_replace('<', '&lt;',
            str_replace('&', "&amp;",
        $string))));
    }

/** Normalize the string for JavaScript string value
  * @param string $string
  * @return string */

    static function jsValue($string) {
        return
            preg_replace('/\r?\n/', "\\n",
            str_replace('"', "\\\"",
            str_replace("'", "\\'",
            str_replace("\\", "\\\\",
        $string))));
    }

}

?>