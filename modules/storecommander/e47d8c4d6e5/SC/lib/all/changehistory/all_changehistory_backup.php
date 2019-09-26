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

$ids=Tools::getValue('ids');
$result=0;

    $cat_product_authorized_list = array("id_supplier","id_manufacturer",
        "id_tax_rules_group","on_sale","online_only","ean13","upc","ecotax",
        "minimal_quantity","price","wholesale_price","unity","unit_price_ratio",
        "additional_shipping_cost","reference","supplier_reference","location",
        "width","height","depth","weight","out_of_stock","quantity_discount",
        "active","redirect_type","id_product_redirected","available_for_order",
        "available_date","condition","show_price","indexed","visibility","date_add");

    $cat_product_lang_authorized_list = array("description","description_short","link_rewrite","meta_description","meta_keywords","meta_title","name","available_now","available_later");

    $cat_category_lang_authorized_list = array(
        "name","description","link_rewrite","meta_title","meta_keywords","meta_description");

    $cat_category_authorized_list = array("active","position");

    $cat_category_shop_authorized_list = array("position");

    $cat_product_shop_authorized_list = array("id_tax_rules_group","on_sale",
        "online_only","ecotax","minimal_quantity","price","wholesale_price","unity","unit_price_ratio",
        "additional_shipping_cost","active","redirect_type","id_product_redirected","available_for_order",
        "available_date","condition","show_price","indexed","visibility");

    $cat_merge_category_product_list = array_merge($cat_product_authorized_list, $cat_category_authorized_list);
        $cat_merge_category_product_shop_list = array_merge($cat_category_shop_authorized_list, $cat_product_shop_authorized_list);
    $cat_merge_category_product_lang_list = array_merge($cat_category_lang_authorized_list, $cat_product_lang_authorized_list);

    $cat_merge_all_authorized_arrays = array_merge($cat_merge_category_product_shop_list, $cat_merge_category_product_lang_list, $cat_merge_category_product_list);

    $sql="SELECT * FROM "._DB_PREFIX_."storecom_history WHERE id_history IN (".pSQL($ids).") ORDER BY date_add DESC";
    $res=Db::getInstance()->ExecuteS($sql);

    foreach($res as $row)
    {
        if (in_array(($row['object'] && $row['object_id'] && $row['dbtable']), $res)) {

            if (($row['dbtable'] == _DB_PREFIX_ . "product"))// || ($row['dbtable'] == _DB_PREFIX_ . "category"))
            {
                if (in_array($row['object'], $cat_merge_category_product_list))
                {
                    $res = "UPDATE " . pSQL($row['dbtable']) . " SET " . pSQL($row['object']) . "='" . pSQL($row['oldvalue']) . "' WHERE id_" . substr($row['dbtable'],
                            strlen(_DB_PREFIX_)) . " = '" . intval($row['object_id']) . "'";
                    $result = Db::getInstance()->Execute($res);

                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        if (in_array($row['object'], $cat_merge_category_product_shop_list))
                        {
                            if (SCMS && !empty($row['shops'])) {
                                $res = "UPDATE " . pSQL($row['dbtable']) . "_shop SET " . pSQL($row['object']) . "='" . pSQL($row['oldvalue']) . "' WHERE id_" . substr($row['dbtable'],
                                        strlen(_DB_PREFIX_)) . " = '" . intval($row['object_id']) . "' AND id_shop IN (" . pSQL($row['shops']) . ")";
                                $result = Db::getInstance()->Execute($res);
                            } elseif (!SCMS) {
                                $res = "UPDATE " . pSQL($row['dbtable']) . "_shop SET " . pSQL($row['object']) . "='" . pSQL($row['oldvalue']) . "' WHERE id_" . substr($row['dbtable'],
                                        strlen(_DB_PREFIX_)) . " = '" . intval($row['object_id']) . "'";
                                $result = Db::getInstance()->Execute($res);
                            }
                        }
                    }
                    addToHistory($row['section'],$row['action'],$row['object'],$row['object_id'],$row['lang_id'],_DB_PREFIX_."product",$row['oldvalue'],$row['newvalue']);
                    echo $result;
                }
            }

            if (($row['dbtable'] == _DB_PREFIX_ . "category_lang") || ($row['dbtable'] == _DB_PREFIX_ . "product_lang"))
            {
                if (in_array($row['object'], $cat_category_lang_authorized_list))
                {
                    if(SCMS && !empty($row['shops']))
                    {
                        $res = "UPDATE " . pSQL($row['dbtable']) . " SET " . pSQL($row['object']) . "='" . pSQL($row['oldvalue']) . "' WHERE id_" . substr($row['dbtable'],
                                strlen(_DB_PREFIX_), -5) . " = '" . intval($row['object_id']) . "' and id_shop IN (".pSQL($row['shops']).")";
                        $result = Db::getInstance()->Execute($res);
                    }
                    elseif(!SCMS)
                    {
                        $res = "UPDATE " . pSQL($row['dbtable']) . " SET " . pSQL($row['object']) . "='" . pSQL($row['oldvalue']) . "' WHERE id_" . substr($row['dbtable'],
                                strlen(_DB_PREFIX_), -5) . " = '" . intval($row['object_id']) . "'";
                        $result = Db::getInstance()->Execute($res);
                    }
                    addToHistory($row['section'],$row['action'],$row['object'],$row['object_id'],$row['lang_id'],_DB_PREFIX_."category_lang",$row['oldvalue'],$row['newvalue']);
                    echo $result;
                }
            }
        }
    }