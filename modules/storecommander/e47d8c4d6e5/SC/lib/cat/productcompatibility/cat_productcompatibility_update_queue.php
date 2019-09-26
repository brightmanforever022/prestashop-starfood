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
if (isset($_POST['compatibilities']) && substr($_POST['compatibilities'],0,16)=='compatibilities_') {
    $prefixlen=strlen('compatibilities_');
    $id_productsource=substr($_POST['compatibilities'],$prefixlen,strlen($_POST['compatibilities']));

    if ($id_productsource!=$id_product)
    {
        $sql = 'SELECT ucc.*
                    FROM ps_ukoocompat_compat uc
                    RIGHT JOIN ps_ukoocompat_compat_criterion ucc
                    ON (ucc.id_ukoocompat_compat = uc.id_ukoocompat_compat)
                    WHERE uc.id_product = '.(int)$id_productsource;
        $res = Db::getInstance()->ExecuteS($sql);
        if(!empty($res)) {
            $compat_array_to_DB = array();
            foreach($res as $data) {
                $compat_array_to_DB[$data['id_ukoocompat_compat']][] = $data;
            }
            foreach($compat_array_to_DB as $compat) {
                if(Db::getInstance()->insert('ukoocompat_compat', array('id_product' => (int)$id_product))) {
                    $id_compat = (int)Db::getInstance()->Insert_ID();
                    $sql = '';
                    foreach($compat as $in_data){
                        $sql .= 'INSERT INTO '._DB_PREFIX_.'ukoocompat_compat_criterion (id_ukoocompat_compat, id_ukoocompat_filter, id_ukoocompat_criterion)
                        VALUES ('.(int)$id_compat.', '.(int)$in_data['id_ukoocompat_filter'].', '.(int)$in_data['id_ukoocompat_criterion'].');';
                    }
                    Db::getInstance()->execute($sql);
                }
            }
        }
    }
}