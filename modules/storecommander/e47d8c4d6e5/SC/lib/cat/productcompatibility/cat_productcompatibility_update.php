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

$action=Tools::getValue('action',null);
$languages = Language::getLanguages(false,false,true);
$default_id_lang = Configuration::get('PS_LANG_DEFAULT');
if(!empty($action)) {
    switch($action) {
        case 'compat_generate':
            $filter_values=Tools::getValue('filter_values',null);
            $product_ids=Tools::getValue('product_ids', null);
            $ids_product = explode(',',$product_ids);

            ## build only array of filters/values
            $filter_values = json_decode($filter_values, true);

            $arr_filters = array();
            $ids_filters = array();
            ## explode de tous les tableaux && enregistre les ids_filter
            foreach($filter_values as $id_filter => $criters)
            {
                $ids_filters[] = (int)$id_filter;
                $criters = str_replace('-1',0,$criters); ## options " Tou(te)s
                $arr_filters[] = explode(',',$criters);
            }

            ## permet d'obtenir le tableau de toutes les compatibilités
            $compat_array = build_compat_array($arr_filters);

            ## pour chaque critère on lui associe l'id du filtre et non pas une clé AI
            $compat_criterion_by_filter=array();
            foreach($compat_array as $k => $criterion_arr) {
                foreach($ids_filters as $key => $id_filter) {
                    $compat_criterion_by_filter[$k][$id_filter] = $criterion_arr[$key];
                }
            }

            $total_generated = 0;
            foreach($ids_product as $id_product) {
                check_compat_exist($id_product, $compat_criterion_by_filter);
                if(count($compat_criterion_by_filter) > 0) {
                    foreach($compat_criterion_by_filter as $compat) {
                        $total_generated++;
                        if(Db::getInstance()->insert('ukoocompat_compat', array('id_product' => (int)$id_product))) {
                            $id_compat = (int)Db::getInstance()->Insert_ID();
                            $sql = '';
                            foreach($compat as $id_filter => $id_criterion){
                                $sql .= 'INSERT INTO '._DB_PREFIX_.'ukoocompat_compat_criterion (id_ukoocompat_compat, id_ukoocompat_filter, id_ukoocompat_criterion) 
                                          VALUES ('.(int)$id_compat.', '.(int)$id_filter.', '.(int)$id_criterion.');';
                            }
                            if(!Db::getInstance()->execute($sql)) {
                                die('KOa');
                            }
                        } else {
                            die('KOb');
                        }
                    }
                }
            }
            die('OK:'.$total_generated);

            break;
        case 'compat_delete':
            $compats=Tools::getValue('compats',null);
            if(!empty($compats)) {
                $compatibilities = explode(',',$compats);
                foreach($compatibilities as $id_compat) {
                    $sql = 'DELETE FROM '._DB_PREFIX_.'ukoocompat_compat WHERE id_ukoocompat_compat = '.(int)$id_compat.';';
                    $sql .= 'DELETE FROM '._DB_PREFIX_.'ukoocompat_compat_criterion WHERE id_ukoocompat_compat = '.(int)$id_compat;
                    if(!DB::getInstance()->execute($sql)) {
                        die('KO');
                    };
                }
            }
            break;
        case 'criterion_update':
            $type = Tools::getValue('type',null);
            $id_lang = (int)Tools::getValue('id_lang',null);
            $row_id=(int)Tools::getValue('row_id',0);
            $value=Tools::getValue('value', null);

            $sql = 'UPDATE '._DB_PREFIX_.'ukoocompat_'.$type.'_lang
                                 SET '.($type == 'criterion' ? 'value' : 'name').' = "'.pSQL($value).'" 
                                 WHERE id_ukoocompat_'.$type.' = '.(int)$row_id.' 
                                 AND id_lang = '.(int)$id_lang;
            if(!DB::getInstance()->execute($sql)) {
                die(json_encode(array('message' => _l('Error updating data').' : '.$type.'_lang')));
            }
            break;
        case 'criterion_add':
            $type = Tools::getValue('type',null);
            $id_filter = Tools::getValue('filter_id', null);
            if(!empty($type))
            {
                $value = Tools::getValue('value', null);
                $value_title = 'name';
                if($type == "criterion") {
                    $lastPosition = DB::getInstance()->getValue('SELECT position 
                                                                      FROM ' . _DB_PREFIX_ . 'ukoocompat_' . $type . ' 
                                                                      WHERE id_ukoocompat_filter = '.(int)$id_filter.'
                                                                      ORDER BY position DESC');
                } else {
                    $lastPosition = DB::getInstance()->getValue('SELECT position FROM ' . _DB_PREFIX_ . 'ukoocompat_' . $type . ' ORDER BY id_ukoocompat_' . $type . ' DESC');
                }

                $insert = array(
                    'position' => (int)$lastPosition+1
                );

                if($type == "criterion") {
                    $value_title = 'value';
                    $insert['id_ukoocompat_filter'] = (int)$id_filter;
                }
                if(Db::getInstance()->insert('ukoocompat_'.$type, $insert))
                {
                    $lastInserted = (int)Db::getInstance()->Insert_ID();
                    $error = 0;
                    foreach($languages as $id_lang)
                    {
                        if(!Db::getInstance()->insert('ukoocompat_'.$type.'_lang', array(
                            'id_ukoocompat_'.$type => (int)$lastInserted,
                            'id_lang' => (int)$id_lang,
                            $value_title => pSQl($value),
                        )))
                        {
                            $error++;
                        }
                    }
                    if(empty($error)) {
                        die(json_encode(array('id_item' => $lastInserted)));
                    } else {
                        die(json_encode(array('message' => _l('Error creating data').' : '.$type.'_lang')));
                    }
                } else {
                    die(json_encode(array('message' => _l('Error creating data').' : '.$type)));
                }
            } else {
                die('KO');
            }
            break;
        case 'criterion_add_multiple':
            $filter_id = (int)Tools::getValue('filter_id',0);
            $criteria = Tools::getValue('criteria',null);
            if(!empty($filter_id) && !empty($criteria))
            {
                $sql = 'SELECT position 
                          FROM '._DB_PREFIX_.'ukoocompat_criterion 
                          WHERE id_ukoocompat_filter = '.(int)$filter_id.'
                          ORDER BY position DESC';
                $lastPosition = (int)Db::getInstance()->getValue($sql);

                if($lastPosition > 0) {
                    $lastPosition++;
                }

                $string = explode("\n",$criteria);
                $pos = $lastPosition;
                foreach($string as $criterion_value)
                {
                    if(Db::getInstance()->insert('ukoocompat_criterion', array(
                        'id_ukoocompat_filter' => (int)$filter_id,
                        'position' => (int)$pos
                    ))) {
                        $new_criterion_id = Db::getInstance()->Insert_ID();
                        foreach($languages as $id_lang) {
                            Db::getInstance()->insert('ukoocompat_criterion_lang', array(
                                'id_ukoocompat_criterion' => (int)$new_criterion_id,
                                'id_lang' => (int)$id_lang,
                                'value' => pSQL($criterion_value)));
                        }

                    }
                    $pos++;
                }
                die('OK:'.count($string));
            } else {
                die('KO');
            }
            break;
        case 'criterion_delete':
            $type = Tools::getValue('type',null);
            $ids = Tools::getValue('ids',null);
            if(!empty($type)) {
                $sql ='DELETE FROM '._DB_PREFIX_.'ukoocompat_'.$type.' WHERE id_ukoocompat_'.$type.' IN('.pSQL($ids).')';
                if(DB::getInstance()->execute($sql)) {
                    $sql ='DELETE FROM '._DB_PREFIX_.'ukoocompat_'.$type.'_lang 
                            WHERE id_ukoocompat_'.$type.' NOT IN ((SELECT id_ukoocompat_'.$type.' FROM '._DB_PREFIX_.'ukoocompat_'.$type.'))';
                    if(!DB::getInstance()->execute($sql)) {
                        die(json_encode(array('message' => _l('Error creating data').' : '.$type.'_lang')));
                    }
                } else {
                    die(json_encode(array('message' => _l('Error deleting data').' : '.$type)));
                }
            } else {
                die('KO');
            }
            break;
        case 'criterion_position_save':
            $type = Tools::getValue('type',null);
            $id_filter=(int)Tools::getValue('id_filter',0);

            $idSource=Tools::getValue('id_source',0);
            $idDrop=Tools::getValue('id_drop',0);
            $listTarget=Tools::getValue('listTarget',null);

            $list_source = explode(',',$idSource);
            $list_drop = explode(',',$idDrop);
            $listIdItems = explode(',',$listTarget);


            # unique ID pour la table temporaire
            $unique_ID = uniqid();

            $relation_arr = array_combine($list_source, $list_drop);

            # Si on envoie une position vers une autre colonne
            if($idSource != $idDrop) {
                foreach($relation_arr as $id_criterion => $tmp) {
                    $keyToDelete = array_search($id_criterion, $listIdItems);
                    if(!empty($keyToDelete)) {
                        unset($listIdItems[$keyToDelete]);
                    }
                    $keyToUpdate = array_search($tmp, $listIdItems);
                    if(!empty($keyToUpdate)) {
                        $listIdItems[$keyToUpdate] = $id_criterion;
                    }
                }
            }

            # Création des valeurs à envoyer dans l'insert
            $insert_values = array();
            foreach($listIdItems as $id_criterion) {
                $insert_values[] = '('.(int)$id_criterion.')';
            }
            
            # création table temp
            $sql = 'CREATE TEMPORARY TABLE '._DB_PREFIX_.'ukoocompat_criterion_temporary_'.$unique_ID.' (
                    id_ukoocompat_criterion INT,
                    position INT NOT NULL AUTO_INCREMENT PRIMARY KEY
                );';

            # on commence à 0
            $sql .= 'ALTER TABLE '._DB_PREFIX_.'ukoocompat_criterion_temporary_'.$unique_ID.' AUTO_INCREMENT = 0;';

            # on insert les données précédemment générées en tableau
            $sql .= 'INSERT INTO '._DB_PREFIX_.'ukoocompat_criterion_temporary_'.$unique_ID.' (id_ukoocompat_criterion)
                VALUES '.implode(',',$insert_values).';';

            # MAJ table des critères
            $sql .='UPDATE '._DB_PREFIX_.'ukoocompat_criterion uc
                INNER JOIN '._DB_PREFIX_.'ukoocompat_criterion_temporary_'.$unique_ID.' uct
                    ON uct.id_ukoocompat_criterion = uc.id_ukoocompat_criterion
                SET uc.position = uct.position-1;';

            # suppression table temp
            $sql .='DROP TABLE '._DB_PREFIX_.'ukoocompat_criterion_temporary_'.$unique_ID.';';

            if(!Db::getInstance()->Execute($sql)) {
                $errors[] = 1;
            }

            if(count($errors) > 0) {
                $err = implode('<br/>',$errors);
                die(json_encode(array('message' => _l('Error saving position').' : '.$err)));
            }
            break;
        case 'criterion_merge':
            $crtlist =explode(',',Tools::getValue('crtlist',0));
            sort($crtlist);
            $id_criterion=array_shift($crtlist);
            $errors = null;
            foreach($crtlist AS $id)
            {
                $sql = "UPDATE "._DB_PREFIX_."ukoocompat_compat_criterion SET id_ukoocompat_criterion=".(int)$id_criterion." WHERE id_ukoocompat_criterion=".(int)$id;
                if(!Db::getInstance()->Execute($sql)) {
                    $error++;
                };
                $sql = "DELETE FROM "._DB_PREFIX_."ukoocompat_criterion_lang WHERE id_ukoocompat_criterion=".(int)$id;
                if(!Db::getInstance()->Execute($sql)) {
                    $error++;
                };
                $sql = "DELETE FROM "._DB_PREFIX_."ukoocompat_criterion WHERE id_ukoocompat_criterion =".(int)$id;
                if(!Db::getInstance()->Execute($sql)) {
                    $error++;
                };
            }
            if(!empty($errors)) {
                die(json_encode(array('message' => _l('Error deleting data'))));
            }
            break;
        case 'clean_database':
            $nbCompat = Db::getInstance()->getValue('SELECT COUNT(*) 
                FROM '._DB_PREFIX_.'ukoocompat_compat 
                WHERE id_product NOT IN ((SELECT id_product FROM '._DB_PREFIX_.'product))');
            $sql = 'DELETE FROM '._DB_PREFIX_.'ukoocompat_compat 
                        WHERE id_product NOT IN ((SELECT id_product FROM '._DB_PREFIX_.'product))';
            if(Db::getInstance()->Execute($sql)) {
                $sql = 'DELETE FROM '._DB_PREFIX_.'ukoocompat_compat_criterion 
                        WHERE id_ukoocompat_compat NOT IN ((SELECT id_ukoocompat_compat FROM '._DB_PREFIX_.'ukoocompat_compat))';
                if(Db::getInstance()->Execute($sql)) {
                    die('OK:'.$nbCompat);
                }
            } else {
                die('KO');
            }
            break;
    }
    die('OK');
} else{
    die('KO');
}


function build_compat_array($arrays)
{
    if (!$arrays) {
        return array(array());
    }
    $subset = array_shift($arrays);
    $cartesianSubset = build_compat_array($arrays);
    $result = array();
    foreach ($subset as $value) {
        foreach ($cartesianSubset as $p) {
            array_unshift($p, $value);
            $result[] = $p;
        }
    }
    return $result;
}

function check_compat_exist($id_product, &$compats)
{
    foreach($compats as $key => $criterion_arr)
    {
        $sql = '
            SELECT COUNT(uc.`id_ukoocompat_compat`)
            FROM `'._DB_PREFIX_.'ukoocompat_compat` uc';
        $i = 0;
        foreach ($criterion_arr as $id_filter => $id_criterion) {
            $sql .= ' INNER JOIN `'._DB_PREFIX_.'ukoocompat_compat_criterion` ucc'.$i.'
            	ON (ucc'.$i.'.`id_ukoocompat_compat` = uc.`id_ukoocompat_compat`
                AND ucc'.$i.'.`id_ukoocompat_filter` = '.(int)$id_filter.'
                AND ucc'.$i.'.`id_ukoocompat_criterion` = '.(int)$id_criterion.')';
            $i++;
        }
        $sql .= ' WHERE uc.`id_product` = '.$id_product;
        if((int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql) > 0 ) {
            unset($compats[$key]);
        }
    }
}

