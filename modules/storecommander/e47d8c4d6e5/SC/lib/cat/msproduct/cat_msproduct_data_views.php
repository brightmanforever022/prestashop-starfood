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
$grids='id_product,reference,supplier_reference,name,id_shop,link_rewrite,active,visibility,on_sale,online_only,show_price,quantity,minimal_quantity,ecotax,wholesale_price,price,id_tax_rules_group,price_inc_tax,margin,unity,unit_price_ratio,additional_shipping_cost,available_for_order,available_date,condition,available_now,available_later';
if(SCAS)
	$grids=str_replace(",quantity,",",advanced_stock_management,quantity,",$grids);
if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
    $grids=str_replace(",condition,",",condition,show_condition,",$grids);