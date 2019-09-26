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

$action = Tools::getValue('action', '');
if ($action == 'dates') {
    $date = (date('m') - 1 > 0 ? date('Y') : date('Y') - 1) . '-' . (date('m') - 1 > 0 ? date('m') - 1 : 12) . '-01';
    $date2 = (date('m') - 1 > 0 ? date('Y') : date('Y') - 1) . '-' . (date('m') - 1 > 0 ? date('m') - 1 : 12) . '-02';
    if (version_compare(_PS_VERSION_, '1.4.0.0', '<')) {
        $sql = "UPDATE " . _DB_PREFIX_ . "product SET reduction_from='" . $date . "',reduction_to='" . $date2 . "'";
        Db::getInstance()->Execute($sql);
    } else {
        $sql = "UPDATE " . _DB_PREFIX_ . "specific_price SET `from`='" . $date . "',`to`='" . $date2 . "' WHERE id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1";
        Db::getInstance()->Execute($sql);
    }
}
if ($action == 'reductions') {
    if (version_compare(_PS_VERSION_, '1.4.0.0', '<')) {
        $sql = "UPDATE " . _DB_PREFIX_ . "product SET reduction_price=0, reduction_percent=0";
        Db::getInstance()->Execute($sql);
    } else {
        $sql = "UPDATE " . _DB_PREFIX_ . "specific_price SET reduction=0
					WHERE id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1";
        Db::getInstance()->Execute($sql);
    }
}
if ($action == 'delsales') {
    if (version_compare(_PS_VERSION_, '1.4.0.0', '>=')) {
        $sql = "DELETE FROM " . _DB_PREFIX_ . "specific_price
					WHERE id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1";
        Db::getInstance()->Execute($sql);
    }
}
