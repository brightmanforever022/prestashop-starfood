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

$idlist = Tools::getValue('idlist');
$id_lang = intval(Tools::getValue('id_lang'));
$cntGroups = count(explode(',', $idlist));
$used = array();


$sql = "SELECT distinct cg.id_category 
        FROM " . _DB_PREFIX_ . "category_group cg
        WHERE cg.id_group IN (" . psql($idlist) . ")";
$res = Db::getInstance()->ExecuteS($sql);

foreach ($res as $row) {
    $used[] = $row['id_category'];
}

echo join(',', $used);
