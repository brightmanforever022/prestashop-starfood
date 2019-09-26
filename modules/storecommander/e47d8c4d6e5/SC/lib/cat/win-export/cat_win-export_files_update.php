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


    $id_lang = intval(Tools::getValue('id_lang'));
    $filename = (Tools::getValue('filenamexport'));

    if (isset($_POST["filenamexport"]))
    {
        $exporttitle = explode(',', Tools::getValue('filenamexport', ''));

        foreach ($exporttitle as $exportvalue)

        $dir = '../../export/';
        $opendir = opendir($dir);

        if ($exportvalue != '')
        {
            $path = $dir . $exportvalue;

            while ($filelist = @readdir($opendir))
            {
                if (!is_dir($dir . '/' . $filelist) && $filelist != '.' && $filelist != '..' && $filelist != 'index.php')
                {
                    unlink($path);
                }
            }

            closedir($open_dir);

        } else {
            echo "Files-update error";
        }
    }
    else
    {
        echo "Files-update error";
    }
?>
