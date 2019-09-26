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

$tab=Tools::getValue('tab');
$tab=str_replace('_import','', $tab);
$id_lang=(int)Tools::getValue('id_lang');
$content=$_POST['content'];
$languages = Language::getLanguages(true);
$return = '';
$id_item = 0;
$error = false;

$content = trim($content,'"');

if(!empty($content)) {

    $data_languages = [];
    $i=1;
    foreach($languages as $lang) {
        $data_languages[$i] = (int)$lang['id_lang'];
        $i++;
    }
    $data_languages_count = count($data_languages);

    $content = explode('\n',$content);
    
    ## si la dernière entrée est vide,
    ## on supprime la ligne pour éviter tout problème
    foreach($content as $k => $row) {
        if(empty($row)) {
            unset($content[$k]);
        }
    }
    
    $data_to_import = [];
    foreach($content as $key => $row) {
        if($key > 0) {
            $first_row_count_col = substr_count($content[0], '\t');
            $actual_row_count_col = substr_count($row, '\t');

            if($actual_row_count_col !== $first_row_count_col) {
                $error = true;
                $return = _l('The number of fields does not match the export',1);
            }
        }

        if(!empty($row)) {
            $row = explode('\t', $row);
            $count_data_row = count($row) - 1;
            if($tab == 'group_attribute') {
                $count_data_row = $count_data_row/2;
            }
            if($count_data_row !== $data_languages_count) {
                $error = true;
                $return = _l('The number of fields does not match the number of languages',1);
            }
            $data_to_import[] = $row;
        }
    }

    if (!$error) {
        unset($data_to_import[0]);

        switch($tab) {
            case 'group_feature':
                $sql = '';
                foreach($data_to_import as $row) {
                    foreach($data_languages as $key => $id_lang) {
                        if(!empty($row[0])) {
                            $sql .= "UPDATE " . _DB_PREFIX_ . "feature_lang 
                                SET name='" . pSQL($row[$key]) . "'
                                WHERE id_feature = " . (int)$row[0] . "
                                AND id_lang = " . (int)$id_lang . ";";
                        }
                    }
                }
                if(Db::getInstance()->execute($sql)){
                    $return = _l('Translation for feature groups updated',1);
                    $id_item = 0;
                }
            break;
            case 'feature_value':
                $sql = '';
                foreach($data_to_import as $row) {
                    foreach($data_languages as $key => $id_lang) {
                        if(!empty($row[0])) {
                            $sql .= "UPDATE " . _DB_PREFIX_ . "feature_value_lang
                                SET value='" . pSQL($row[$key]) . "'
                                WHERE id_feature_value = " . (int)$row[0] . "
                                AND id_lang = " . (int)$id_lang . ";";
                        }
                    }
                }
                if(Db::getInstance()->execute($sql)){
                    $return = _l('Translation for feature values updated',1);
                    $id_item = 1;
                }
                break;
            case 'group_attribute':
                $sql = '';
                foreach($data_to_import as $row) {
                    foreach($data_languages as $key => $id_lang) {
                        if(!empty($row[0])) {
                            $newKey = $key + ($key - 1);
                            $sql .= "UPDATE " . _DB_PREFIX_ . "attribute_group_lang 
                                SET name='" . pSQL($row[$newKey]) . "', public_name='" . pSQL($row[$newKey + 1]) . "'
                                WHERE id_attribute_group = " . (int)$row[0] . "
                                AND id_lang = " . (int)$id_lang . ";";
                        }
                    }
                }
                if(Db::getInstance()->execute($sql)){
                    $return = _l('Translation for combination groups updated',1);
                    $id_item = 2;
                }
            break;
            case 'attribute_value':
                $sql = '';
                foreach($data_to_import as $row) {
                    foreach($data_languages as $key => $id_lang) {
                        if(!empty($row[0])) {
                            $sql .= "UPDATE " . _DB_PREFIX_ . "attribute_lang
                                SET name='" . pSQL($row[$key]) . "'
                                WHERE id_attribute = " . (int)$row[0] . "
                                AND id_lang = " . (int)$id_lang . ";";
                        }
                    }
                }
                if(Db::getInstance()->execute($sql)){
                    $return = _l('Translation for combination attributes updated',1);
                    $id_item = 3;
                }
            break;
        }
    }
} else {
    $error = true;
    $return = _l('Empty data',1);
}

die(json_encode(array(
    'error' => $error,
    'id_item' => $id_item,
    'message' => $return
)));

