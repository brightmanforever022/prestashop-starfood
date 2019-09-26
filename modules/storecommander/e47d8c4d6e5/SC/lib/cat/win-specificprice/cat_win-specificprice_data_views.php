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
$grids='id_specific_price,id_product,id_product_attribute,reference,name,manufacturer,supplier,id_group,from_quantity,price,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_wt_with_reduction,price_it_with_reduction,from,to,from_num,to_num,id_country,id_currency,on_sale';

if(SCMS)
	$grids='id_specific_price,id_product,id_product_attribute,reference,name,manufacturer,supplier,shop_id,id_shop,id_shop_group,id_group,from_quantity,price,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_wt_with_reduction,price_it_with_reduction,from,to,from_num,to_num,id_country,id_currency,on_sale';
	
if(SCMS && version_compare(_PS_VERSION_, '1.6.0.11', '>='))
	$grids='id_specific_price,id_product,id_product_attribute,reference,name,manufacturer,supplier,shop_id,id_shop,id_shop_group,id_group,from_quantity,price,reduction_tax,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_wt_with_reduction,price_it_with_reduction,from,to,from_num,to_num,id_country,id_currency,on_sale,id_customer';
elseif(version_compare(_PS_VERSION_, '1.6.0.11', '>='))
	$grids='id_specific_price,id_product,id_product_attribute,reference,name,manufacturer,supplier,id_group,from_quantity,price,reduction_tax,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_wt_with_reduction,price_it_with_reduction,from,to,from_num,to_num,id_country,id_currency,on_sale,id_customer';
elseif(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	$grids='id_specific_price,id_product,id_product_attribute,reference,name,manufacturer,supplier,id_group,from_quantity,price,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_wt_with_reduction,price_it_with_reduction,from,to,from_num,to_num,id_country,id_currency,on_sale,id_customer';
