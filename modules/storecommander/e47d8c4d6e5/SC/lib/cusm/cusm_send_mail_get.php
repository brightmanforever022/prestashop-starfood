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


$searches = explode(' ', Tools::getValue('customer_search'));
$customers = array();
$searches = array_unique($searches);
foreach ($searches as $search) {
    if (!empty($search) && $results = searchByName($search, 50)) {
        foreach ($results as $result) {
            if ($result['active']) {
                $result['fullname_and_email'] = $result['firstname'].' '.$result['lastname'].' - '.$result['email'];
                $customers[$result['id_customer']] = $result;
            }
        }
    }
}

if (count($customers)) {
    $to_return = array(
        'customers' => $customers,
        'found' => true
    );
} else {
    $to_return = array(
        'found' => false
    );
}
die(json_encode($to_return));


function searchByName($query, $limit = null)
{
    $sql_base = 'SELECT *
			FROM `'._DB_PREFIX_.'customer`';
    $sql = '('.$sql_base.' WHERE `email` LIKE \'%'.pSQL($query).'%\')';
    $sql .= ' UNION ('.$sql_base.' WHERE `id_customer` = '.(int)$query.')';
    $sql .= ' UNION ('.$sql_base.' WHERE `lastname` LIKE \'%'.pSQL($query).'%\')';
    $sql .= ' UNION ('.$sql_base.' WHERE `firstname` LIKE \'%'.pSQL($query).'%\')';

    if ($limit) {
        $sql .= ' LIMIT 0, '.(int)$limit;
    }

    return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
}

