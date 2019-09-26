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
$grids='id_specific_price,id_product,id_product_attribute,reference,name,id_group,from_quantity,price,reduction,from,to,id_country,id_currency';

if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    $grids='id_specific_price,id_product,id_product_attribute,reference,name,id_group,from_quantity,price,reduction,reduction_tax,from,to,id_country,id_currency,id_specific_price_rule,id_customer';

if(SCMS)
    $grids = str_replace(",id_group,", ",id_shop,id_shop_group,id_group,", $grids);
