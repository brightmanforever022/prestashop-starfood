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
$sc_active=SCI::getConfigurationValue('SC_PLUG_DISABLECOMBINATIONS',0);

$grids='id_product_attribute,'.($sc_active?'sc_active,':'').'reference,supplier_reference,ean13,location,ATTR,quantity,quantityupdate,wholesale_price,pprice,price,ppriceextax,priceextax,margin,ecotax,pweight,weight,default_on';
if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
	$grids='id_product_attribute,'.($sc_active?'sc_active,':'').'reference,supplier_reference,ean13,upc,location,ATTR,quantity,quantityupdate,minimal_quantity,wholesale_price,pprice,price,ppriceextax,priceextax,margin,ecotax,pweight,weight,unit_price_impact,unit_price_impact_inc_tax,default_on';
if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
	//$grids=str_replace('location,','',$grids);
	$grids=str_replace(',default_on',',available_date,default_on',$grids);
	//$grids=str_replace('supplier_reference,','',$grids);
	//$grids=str_replace('wholesale_price,','',$grids);
}
if(SCAS)
	$grids='id_product_attribute,'.($sc_active?'sc_active,':'').'reference,supplier_reference,ean13,location,upc,ATTR,quantity,quantity_physical,quantity_usable,quantity_real,quantityupdate,minimal_quantity,wholesale_price,pprice,price,ppriceextax,priceextax,margin,ecotax,pweight,weight,unit_price_impact,unit_price_impact_inc_tax,available_date,default_on';

if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && (int)SCI::getConfigurationValue('PS_USE_ECOTAX', null, 0, SCI::getSelectedShop())==0)
	$grids=str_replace(',ecotax','',$grids);
elseif ((version_compare(_PS_VERSION_, '1.4.0.0', '>=') && version_compare(_PS_VERSION_, '1.5.0.0', '<')) && (int)SCI::getConfigurationValue('PS_USE_ECOTAX')==0)
	$grids=str_replace(',ecotax','',$grids);
if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
{
    $grids=str_replace(',upc',',upc,isbn',$grids);
}

if(SCI::getConfigurationValue("SC_DELIVERYDATE_INSTALLED")=="1")
	$grids=str_replace(',default_on',',available_later,default_on',$grids);