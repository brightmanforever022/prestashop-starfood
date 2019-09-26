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
if (Tools::getValue('save') && Tools::getValue('save') == 1) {

    include_once(SC_DIR.'lib/php/parsecsv.lib.php');
    require_once(SC_DIR.'lib/cat/win-import/cat_win-import_tools.php');

    $file = Tools::getValue('url');
    $fieldSep = Tools::getValue('fieldsep');
    $dataFromUrl = Tools::getValue('data');
    $type = Tools::getValue('type');
    $forceUTF8 = Tools::getValue('utf8');
    $nbRowStart = Tools::getValue('nbrowstart');
    $nbRowEnd = Tools::getValue('nbrowend');
    $errors = array();
    
    if (!file_exists($file)) {
        die(_l('File not found',1));
    }

    $fileToRead = new parseCSV($file);

    $fileToWrite = new parseCSV();
// Si on force utf-8 alors il faut enregistrer les valeurs en ISO avant que le traitement ne reconvertisse le fichier en UTF-8
    $fileToWrite->parse(($forceUTF8 == 1 ? utf8_decode($dataFromUrl) : $dataFromUrl));
    $firstRow = (!empty($type) && $type == 'grid' ? $fileToRead->titles : $fileToWrite->titles);

    for($i=$nbRowStart ; $i<=($nbRowEnd-1) ; $i++)
    {
        if(!empty($fileToWrite->data[$i])) {
            $fileToRead->data[$i] = $fileToWrite->data[$i];
        } else {
            unset($fileToRead->data[$i]);
        }

    }

// Si deux colonne de même nom
    if (count($firstRow)!=count(array_unique($firstRow))) {
        die(_l('Error : at least 2 columns have the same name in CSV file. You must use a unique name by column in the first line of your CSV file.'));
    } else {
        try {
            $fileSaved = new parseCSV($file);
            $fileSaved->linefeed = "\n";
            $fileSaved->titles = $firstRow;
            $fileSaved->data = $fileToRead->data;
            $fileSaved->save();

            echo _l('Updated file', 1);
        } catch (Exception $e) {
            echo $e->getMessage(), "\n";
        }
    }
}
exit();
