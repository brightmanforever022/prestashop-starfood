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
$grids=array(
		'grid_light' => 		'id,image,active,position,reference,name,quantity,quantityupdate,price,id_tax,price_inc_tax,ecotax',
		'grid_large' => 		'id,image,reference,name,quantity,quantityupdate,wholesale_price,price,id_tax,price_inc_tax,ecotax,weight,supplier_reference,id_manufacturer,id_supplier,ean13,location,date_add,out_of_stock,available_now,available_later,reduction_price,reduction_percent,reduction_from,reduction_to,on_sale,position,active',
		'grid_delivery' => 	'id,image,reference,name,quantity,quantityupdate,weight,out_of_stock,available_now,available_later,id_manufacturer,id_supplier,active',
		'grid_price' => 		'id,image,reference,name,wholesale_price,price,margin,id_tax,price_inc_tax,ecotax,id_manufacturer,id_supplier,discountprice,active',
		'grid_discount' => 	'id,image,reference,name,wholesale_price,price,id_tax,price_inc_tax,ecotax,date_add,out_of_stock,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,on_sale,quantity,id_manufacturer,id_supplier,position,active',
		'grid_seo' => 			'id,image,reference,supplier_reference,name,meta_title,meta_description,meta_keywords,link_rewrite,active',
		'grid_reference' => 'id,image,reference,supplier_reference,name,date_upd,date_add,id_manufacturer,id_supplier,ean13,location,id_color_default,combinations,active',
		'grid_description' => 'id,image,reference,name,description_short,description,active',
		'grid_combination_price' =>	'id,reference,name,wholesale_price,price,id_tax,price_inc_tax,ecotax,id_manufacturer,id_supplier,active',
		'grid_discount_2' => 	'id,image,reference,name,wholesale_price,price,id_tax,price_inc_tax,ecotax,date_add,out_of_stock,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_wt_with_reduction,price_it_with_reduction,reduction_from,reduction_to,on_sale,quantity,last_order,id_manufacturer,id_supplier,position,active'
);

if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
{
	$grids=array(
			'grid_light' => 		'id,image,active,position,reference,name,quantity,quantityupdate,minimal_quantity,price,id_tax_rules_group,price_inc_tax,ecotax',
			'grid_large' => 		'id,image,reference,name,quantity,quantityupdate,minimal_quantity,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,weight,supplier_reference,id_manufacturer,id_supplier,ean13,upc,location,date_add,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,out_of_stock,available_now,available_later,on_sale,available_for_order,show_price,online_only,condition,position,active',
			'grid_delivery' => 	'id,image,reference,name,quantity,quantityupdate,minimal_quantity,additional_shipping_cost,weight,width,height,depth,out_of_stock,available_now,available_later,id_manufacturer,id_supplier,active',
			'grid_price' => 		'id,image,reference,name,wholesale_price,price,margin,id_tax_rules_group,price_inc_tax,unit_price_ratio,unit_price_inc_tax,unity,reduction_price,ecotax,discountprice,id_manufacturer,id_supplier,active',
			'grid_discount' => 	'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,quantity,date_add,id_manufacturer,id_supplier,position,active',
			'grid_seo' => 			'id,image,reference,supplier_reference,name,meta_title,meta_description,meta_keywords,link_rewrite,active',
			'grid_reference' => 'id,image,reference,supplier_reference,name,available_for_order,show_price,online_only,condition,date_upd,date_add,id_manufacturer,id_supplier,ean13,upc,location,id_color_default,combinations,active',
			'grid_description' => 'id,image,reference,name,description_short,description,active',
			'grid_combination_price' =>	'id,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,id_manufacturer,id_supplier,active',
			'grid_discount_2' => 	'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_wt_with_reduction,price_it_with_reduction,reduction_from,reduction_to,quantity,last_order,date_add,id_manufacturer,id_supplier,position,active'
	);
}
if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
	$grids=array(
			'grid_light' => 		'id,image,active,position,reference,name,quantity,quantityupdate,minimal_quantity,price,id_tax_rules_group,price_inc_tax,ecotax',
			'grid_large' => 		'id,image,reference,name,quantity,quantityupdate,minimal_quantity,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,weight,supplier_reference,id_manufacturer,id_supplier,ean13,upc,location,date_add,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,out_of_stock,available_now,available_later,on_sale,available_for_order,available_date,visibility,show_price,online_only,condition,position,active',
			'grid_delivery' => 	'id,image,reference,name,quantity,quantityupdate,minimal_quantity,additional_shipping_cost,weight,width,height,depth,out_of_stock,available_now,available_later,id_manufacturer,id_supplier,active',
			'grid_price' => 		'id,image,reference,name,wholesale_price,price,margin,id_tax_rules_group,price_inc_tax,unit_price_ratio,unit_price_inc_tax,unity,reduction_price,ecotax,discountprice,id_manufacturer,id_supplier,active',
			'grid_discount' => 	'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,quantity,date_add,id_manufacturer,id_supplier,position,active',
			'grid_seo' => 			'id,image,reference,supplier_reference,name,meta_title,meta_description,meta_keywords,link_rewrite,redirect_type,id_product_redirected,active',
			'grid_reference' => 'id,image,reference,supplier_reference,name,available_for_order,available_date,visibility,show_price,online_only,condition,date_upd,date_add,id_manufacturer,id_supplier,ean13,upc,location,combinations,active',
			'grid_description' => 'id,image,reference,name,description_short,description,active',
			'grid_combination_price' =>	'id,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,id_manufacturer,id_supplier,active',
			'grid_discount_2' => 	'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_wt_with_reduction,price_it_with_reduction,reduction_from,reduction_to,quantity,last_order,date_add,id_manufacturer,id_supplier,position,active'
	);
}
if (SCAS)
{
	$grids=array(
			'grid_light' => 		'id,image,active,position,reference,name,quantity,advanced_stock_management,quantity_physical,quantity_usable,quantity_real,quantityupdate,minimal_quantity,price,id_tax_rules_group,price_inc_tax,ecotax',
			'grid_large' => 		'id,image,reference,name,quantity,advanced_stock_management,quantity_physical,quantity_usable,quantity_real,quantityupdate,minimal_quantity,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,weight,supplier_reference,id_manufacturer,id_supplier,ean13,upc,location,date_add,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,out_of_stock,available_now,available_later,on_sale,available_for_order,available_date,visibility,show_price,online_only,condition,position,active',
			'grid_delivery' => 	'id,image,reference,name,quantity,advanced_stock_management,quantity_physical,quantity_usable,quantity_real,quantityupdate,minimal_quantity,additional_shipping_cost,weight,width,height,depth,out_of_stock,available_now,available_later,id_manufacturer,id_supplier,active',
			'grid_price' => 		'id,image,reference,name,wholesale_price,price,margin,id_tax_rules_group,price_inc_tax,unit_price_ratio,unit_price_inc_tax,unity,reduction_price,ecotax,discountprice,id_manufacturer,id_supplier,active',
			'grid_discount' => 	'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,quantity,advanced_stock_management,quantity_physical,quantity_usable,quantity_real,quantityupdate,date_add,id_manufacturer,id_supplier,position,active',
			'grid_seo' => 			'id,image,reference,supplier_reference,name,meta_title,meta_description,meta_keywords,link_rewrite,redirect_type,id_product_redirected,active',
			'grid_reference' => 'id,image,reference,supplier_reference,name,available_for_order,available_date,visibility,show_price,online_only,condition,date_upd,date_add,id_manufacturer,id_supplier,ean13,upc,location,combinations,active',
			'grid_description' => 'id,image,reference,name,description_short,description,active',
			'grid_combination_price' =>	'id,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,id_manufacturer,id_supplier,active',
			'grid_discount_2' => 	'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_wt_with_reduction,price_it_with_reduction,reduction_from,reduction_to,quantity,advanced_stock_management,quantity_physical,quantity_usable,quantity_real,quantityupdate,last_order,date_add,id_manufacturer,id_supplier,position,active'
		);

}

/*if(version_compare(_PS_VERSION_, '1.6.0.11', '>='))
{
	$grids["grid_discount"]=str_replace(",reduction_from,", ",reduction_tax,reduction_from,", $grids["grid_discount"]);
	$grids["grid_discount_2"]=str_replace(",reduction_percent,", ",reduction_percent,reduction_tax,", $grids["grid_discount_2"]);
}*/

if(version_compare(_PS_VERSION_, '1.7.0.0', '>='))
{
    $grids=array(
        'grid_light' => 		'id,image,active,position,reference,name,quantity,quantityupdate,minimal_quantity,price,id_tax_rules_group,price_inc_tax,ecotax',
        'grid_large' => 		'id,image,reference,name,quantity,quantityupdate,minimal_quantity,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,weight,supplier_reference,id_manufacturer,id_supplier,ean13,upc,isbn,location,date_add,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,out_of_stock,available_now,available_later,on_sale,available_for_order,available_date,visibility,show_price,online_only,condition,show_condition,position,active',
        'grid_delivery' => 	'id,image,reference,name,quantity,quantityupdate,minimal_quantity,additional_shipping_cost,weight,width,height,depth,out_of_stock,available_now,available_later,id_manufacturer,id_supplier,active',
        'grid_price' => 		'id,image,reference,name,wholesale_price,price,margin,id_tax_rules_group,price_inc_tax,unit_price_ratio,unit_price_inc_tax,unity,reduction_price,ecotax,discountprice,id_manufacturer,id_supplier,active',
        'grid_discount' => 	'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,quantity,date_add,id_manufacturer,id_supplier,position,active',
        'grid_seo' => 			'id,image,reference,supplier_reference,name,meta_title,meta_description,meta_keywords,link_rewrite,redirect_type,id_product_redirected,active',
        'grid_reference' => 'id,image,reference,supplier_reference,name,available_for_order,available_date,visibility,show_price,online_only,condition,show_condition,date_upd,date_add,id_manufacturer,id_supplier,ean13,upc,isbn,location,combinations,active',
        'grid_description' => 'id,image,reference,name,description_short,description,active',
        'grid_combination_price' =>	'id,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,id_manufacturer,id_supplier,active',
        'grid_discount_2' => 	'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_wt_with_reduction,price_it_with_reduction,reduction_from,reduction_to,quantity,last_order,date_add,id_manufacturer,id_supplier,position,active'
    );
    if (SCAS)
    {
        $grids=array(
            'grid_light' => 		'id,image,active,position,reference,name,quantity,advanced_stock_management,quantity_physical,quantity_usable,quantity_real,quantityupdate,minimal_quantity,price,id_tax_rules_group,price_inc_tax,ecotax',
            'grid_large' => 		'id,image,reference,name,quantity,advanced_stock_management,quantity_physical,quantity_usable,quantity_real,quantityupdate,minimal_quantity,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,weight,supplier_reference,id_manufacturer,id_supplier,ean13,upc,isbn,location,date_add,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,out_of_stock,available_now,available_later,on_sale,available_for_order,available_date,visibility,show_price,online_only,condition,show_condition,position,active',
            'grid_delivery' => 	'id,image,reference,name,quantity,advanced_stock_management,quantity_physical,quantity_usable,quantity_real,quantityupdate,minimal_quantity,additional_shipping_cost,weight,width,height,depth,out_of_stock,available_now,available_later,id_manufacturer,id_supplier,active',
            'grid_price' => 		'id,image,reference,name,wholesale_price,price,margin,id_tax_rules_group,price_inc_tax,unit_price_ratio,unit_price_inc_tax,unity,reduction_price,ecotax,discountprice,id_manufacturer,id_supplier,active',
            'grid_discount' => 	'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,quantity,advanced_stock_management,quantity_physical,quantity_usable,quantity_real,quantityupdate,date_add,id_manufacturer,id_supplier,position,active',
            'grid_seo' => 			'id,image,reference,supplier_reference,name,meta_title,meta_description,meta_keywords,link_rewrite,redirect_type,id_product_redirected,active',
            'grid_reference' => 'id,image,reference,supplier_reference,name,available_for_order,available_date,visibility,show_price,online_only,condition,show_condition,date_upd,date_add,id_manufacturer,id_supplier,ean13,upc,isbn,location,combinations,active',
            'grid_description' => 'id,image,reference,name,description_short,description,active',
            'grid_combination_price' =>	'id,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,id_manufacturer,id_supplier,active',
            'grid_discount_2' => 	'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_wt_with_reduction,price_it_with_reduction,reduction_from,reduction_to,quantity,advanced_stock_management,quantity_physical,quantity_usable,quantity_real,quantityupdate,last_order,date_add,id_manufacturer,id_supplier,position,active'
        );

    }
}

if(SC_UkooProductCompat_ACTIVE) {
    $grids['grid_reference'] .= ',nb_compatibilities,compatibilities';
}
