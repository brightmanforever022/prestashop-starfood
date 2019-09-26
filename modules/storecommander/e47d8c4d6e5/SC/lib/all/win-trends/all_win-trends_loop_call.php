<?php

$return = array();
/*
$return = array("stop"=>"1");
echo json_encode($return);die();*/

$licence = SCI::getConfigurationValue('SC_LICENSE_KEY');
if(empty($licence))
    $licence = "demo";

$idShops = SCI::getConfigurationValue('SC_TRENDS_ID_SHOPS');
if(!empty($idShops))
    $idShops = json_decode($idShops, TRUE);

if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    $shops = ShopCore::getShops(false);
else
    $shops = array(array('id_shop'=>0));
$has_results = false;
$force_stop = false;

/*
 * Functions
 */
function getOrderDetailsBySegmentByShop($first,$last,$segment, $shop, $limit_order)
{
    $return = array();
    if(!empty($first) && !empty($last) && !empty($limit_order))
    {
        $start_order_id = $first;
        $end_order_id = $first+$limit_order-1;
        if($end_order_id>$last)
            $end_order_id = $last;

        $return = _getOrderDetailsBySegmentByShop($segment, $shop, $start_order_id, $end_order_id, $last, $limit_order);
    }
    return $return;
}
function _getOrderDetailsBySegmentByShop($segment, $shop, $start_order_id, $end_order_id, $last, $limit_order)
{
    $return = array();
    if(!empty($segment) && !empty($shop) && !empty($start_order_id) && !empty($end_order_id) && !empty($limit_order))
    {
        if($segment['id_segment']=="1")
        {
            $where = "";
            if (!empty($segment['id_start']))
                $where .= ' AND od.id_order_detail > "' . intval($segment['id_start']) . '" ';
            if (!empty($segment['id_end']))
                $where .= ' AND od.id_order_detail <= "' . intval($segment['id_end']) . '" ';
            if (!empty($segment['dateStart']))
                $where .= ' AND "' . pSQL($segment['dateStart']) . '" <= o.date_add ';
            if (!empty($segment['dateEnd']))
                $where .= ' AND o.date_add <= "' . pSQL($segment['dateEnd']) . '" ';
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                $where .= ' AND o.id_shop = "' . intval($shop['id_shop']) . '" ';

            $where .= ' AND o.id_order >= "' . intval($start_order_id) . '" ';
            $where .= ' AND o.id_order <= "' . intval($end_order_id) . '" ';

            $sql = 'SELECT  o.id_order as order_id, od.id_order_detail as order_detail_id, od.product_quantity as quantity,
                            a_d.postcode as delivery_postcode, c_d.iso_code as delivery_country, a_d.company as delivery_company,
                            ca.name as carrier,
                            a_s.postcode as invoice_postcode,
                            o.total_shipping as shipping_cost,MAX(oh.date_add) as delivery_date, o.delivery_date as shipping_date,
                            p.width as product_width, p.height as product_height, p.depth as product_depth, p.weight as weight_kg
                        FROM ' . _DB_PREFIX_ . 'order_detail od
                            INNER JOIN ' . _DB_PREFIX_ . 'orders o ON (o.id_order = od.id_order)
                                INNER JOIN ' . _DB_PREFIX_ . 'carrier ca ON (ca.id_carrier = o.id_carrier)
                                INNER JOIN ' . _DB_PREFIX_ . 'address a_s ON (a_s.id_address = o.id_address_invoice)
                                INNER JOIN ' . _DB_PREFIX_ . 'address a_d ON (a_d.id_address = o.id_address_delivery)
                                    INNER JOIN ' . _DB_PREFIX_ . 'country c_d ON (a_d.id_country = c_d.id_country)
                                INNER JOIN ' . _DB_PREFIX_ . 'order_history oh ON (o.id_order = oh.id_order)
                            INNER JOIN ' . _DB_PREFIX_ . 'product p ON (od.product_id = p.id_product)
                        WHERE 1=1
                          AND o.valid = 1
                          ' . $where . '
                        GROUP BY od.id_order_detail
                        ORDER BY od.id_order_detail ASC';
            $order_details = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if (!empty($order_details) && count($order_details) > 0)
            {
                $datas = array();
                foreach ($order_details as $order_detail)
                {
                    if (!empty($order_detail['delivery_company']))
                        $order_detail['delivery_company'] = 1;
                    else
                        $order_detail['delivery_company'] = 0;
                    $datas[] = $order_detail;
                }
                $return = $datas;
            }
            elseif($end_order_id<$last)
            {
                $start_order_id = $end_order_id+1;
                $end_order_id = $start_order_id+$limit_order-1;
                if($end_order_id>$last)
                    $end_order_id = $last;

                $return = _getOrderDetailsBySegmentByShop($segment, $shop, $start_order_id, $end_order_id, $last, $limit_order);
            }
        }
        elseif($segment['id_segment']=="3")
        {
            /*
            order detail infos
            */
            $where = "";
            if (!empty($segment['id_start']))
                $where .= ' AND od.id_order_detail > "' . intval($segment['id_start']) . '" ';
            if (!empty($segment['id_end']))
                $where .= ' AND od.id_order_detail <= "' . intval($segment['id_end']) . '" ';
            if (!empty($segment['dateStart']))
                $where .= ' AND "' . pSQL($segment['dateStart']) . '" <= o.date_add ';
            if (!empty($segment['dateEnd']))
                $where .= ' AND o.date_add <= "' . pSQL($segment['dateEnd']) . '" ';
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                $where .= ' AND o.id_shop = "' . intval($shop['id_shop']) . '" ';

            $where .= ' AND o.id_order >= "' . intval($start_order_id) . '" ';
            $where .= ' AND o.id_order <= "' . intval($end_order_id) . '" ';

            $sql = 'SELECT  od.id_order as order_id, od.id_order_detail as order_detail_id,
                            p.width as product_width, p.height as product_height, p.depth as product_depth
                        FROM ' . _DB_PREFIX_ . 'order_detail od
                            INNER JOIN ' . _DB_PREFIX_ . 'orders o ON (o.id_order = od.id_order)
                            INNER JOIN ' . _DB_PREFIX_ . 'product p ON (od.product_id = p.id_product)
                        WHERE 1=1
                          AND o.valid = 1
                          ' . $where . '
                        GROUP BY od.id_order_detail
                        ORDER BY od.id_order_detail ASC';
            $order_details = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if (!empty($order_details) && count($order_details) > 0)
            {
                $datas = array();
                foreach ($order_details as $order_detail)
                {
                    $datas[] = $order_detail;
                }
                $return = $datas;
            }
            elseif($end_order_id<$last)
            {
                $start_order_id = $end_order_id+1;
                $end_order_id = $start_order_id+$limit_order-1;
                if($end_order_id>$last)
                    $end_order_id = $last;

                $return = _getOrderDetailsBySegmentByShop($segment, $shop, $start_order_id, $end_order_id, $last, $limit_order);
            }
        }
    }
    return $return;
}

/*
 *
 */
foreach ($shops as $shop)
{
    $maintenance = SCI::getConfigurationValue('SC_TRENDS_ID_SHOPS', null, 0, $shop["id_shop"]);
    if($maintenance=="1")
        continue;
    /*
     * Suscribe shop
     * (in case new shop)
     */
    $url = "";
    if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $protocol = (version_compare(_PS_VERSION_, '1.5.0.2', '>=') ? Tools::getShopProtocol() : (SCI::getConfigurationValue('PS_SSL_ENABLED') ? 'https://' : 'http://'));
        $urlSql = Db::getInstance()->ExecuteS('SELECT CONCAT(domain, physical_uri, virtual_uri) AS url
					FROM '._DB_PREFIX_.'shop_url
					WHERE id_shop = '.(int)$shop["id_shop"].'
					ORDER BY main DESC
					LIMIT 1');
        if(!empty($urlSql[0]["url"]))
            $url = $protocol.$urlSql[0]["url"];
    }
    else
        $url = Tools::getShopDomain(true).__PS_BASE_URI__;
    $headers = array();
    $headers[] = "SCLICENSE: " . $licence;
    $headers[] = "EMAIL: " . $sc_agent->email;
    $headers[] = "SHOPID: " . $shop['id_shop'];
    $headers[] = "SHOPURL: " .$url;
    $headers[] = "SCVERSION: " .SC_VERSION;
    if(!empty($idShops[$shop['id_shop']]))
        $headers[] = "ID_SHOP: " .$idShops[$shop['id_shop']];
    $return_register = sc_file_post_contents('http://api.storecommander.com/Trends/RegisterShop', '', $headers);
    $return_register = json_decode($return_register, true);
    if(!empty($return_register['result']) && $return_register['result']=="OK" && !empty($return_register['code']) && $return_register['code']=="200" && !empty($return_register['id']))
    {
        if(empty($idShops))
            $idShops = array();
        $exp = explode("_", $return_register['id']);
        $idShops[$exp[0]] = $exp[1];
        $idShops_encoded = json_encode($idShops);
        SCI::updateConfigurationValue('SC_TRENDS_ID_SHOPS', $idShops_encoded);
    }

    if(empty($idShops[$shop['id_shop']]))
        continue;
    /*
     * Get wanted segments
     */
    $headers = array();
    $headers[] = "SCLICENSE: " . $licence;
    $headers[] = "SHOPID: " . $shop['id_shop'];
    $headers[] = "SCVERSION: " .SC_VERSION;
    $headers[] = "ID_SHOP: " .$idShops[$shop['id_shop']];
    $ask = sc_file_post_contents('http://api.storecommander.com/Trends/GetShopDataRequest', '', $headers);
    $return_segments = json_decode($ask);
    if(empty($return_segments->result)) {
        foreach ($return_segments as $segment) {
            $segment = (array)$segment;
            /*
             * Segment data logistic
             */
            if ($segment['id_segment'] == "1" || $segment['id_segment'] == "3") {
                $limit_order = (!empty($segment['limitCount']) ? intval($segment['limitCount']) : "300");

                $where = "";
                if (!empty($segment['id_start']))
                    $where .= ' AND od.id_order_detail > "' . intval($segment['id_start']) . '" ';
                if (!empty($segment['id_end']))
                    $where .= ' AND od.id_order_detail <= "' . intval($segment['id_end']) . '" ';
                if (!empty($segment['dateStart']))
                    $where .= ' AND "' . pSQL($segment['dateStart']) . '" <= o.date_add ';
                if (!empty($segment['dateEnd']))
                    $where .= ' AND o.date_add <= "' . pSQL($segment['dateEnd']) . '" ';
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    $where .= ' AND o.id_shop = "' . intval($shop['id_shop']) . '" ';

                /*
                 * Get Last order
                 * for asked period
                 */
                $last_order = 0;
                $sql = 'SELECT  o.id_order as order_id, od.id_order_detail as order_detail_id
                    FROM ' . _DB_PREFIX_ . 'order_detail od
                        INNER JOIN ' . _DB_PREFIX_ . 'orders o ON (o.id_order = od.id_order)
                    WHERE 1=1
                      AND o.valid = 1
                      ' . $where . '
                    GROUP BY od.id_order_detail
                    ORDER BY od.id_order_detail DESC
                    LIMIT 1';
                $last_order_result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                if (!empty($last_order_result[0]['order_id']))
                    $last_order = $last_order_result[0]['order_id'];

                /*
                 * Get first order
                 * for asked period
                 */
                $first_order = 0;
                $sql = 'SELECT  o.id_order as order_id, od.id_order_detail as order_detail_id
                    FROM ' . _DB_PREFIX_ . 'order_detail od
                        INNER JOIN ' . _DB_PREFIX_ . 'orders o ON (o.id_order = od.id_order)
                    WHERE 1=1
                      AND o.valid = 1
                      ' . $where . '
                    GROUP BY od.id_order_detail
                    ORDER BY od.id_order_detail ASC
                    LIMIT 1';
                $first_order_result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                if (!empty($first_order_result[0]['order_id']))
                    $first_order = $first_order_result[0]['order_id'];

                if (!empty($first_order) && !empty($last_order)) {
                    $datas = getOrderDetailsBySegmentByShop($first_order, $last_order, $segment, $shop, $limit_order);
                    if (!empty($datas)) {
                        $post = array("id_segment" => $segment['id_segment'], "data" => array());
                        $post['data'] = json_encode($datas);
                        $headers = array();
                        $headers[] = "SCLICENSE: " . $licence;
                        $headers[] = "SHOPID: " . $shop['id_shop'];
                        $headers[] = "SCVERSION: " . SC_VERSION;
                        $headers[] = "ID_SHOP: " . $idShops[$shop['id_shop']];
                        $ret = sc_file_post_contents('http://api.storecommander.com/Trends/SendShopData', $post, $headers);
                        $ret = (array)json_decode($ret);
                        if (!empty($ret['code']) && $ret['code'] == "200")
                            $has_results = true;
                    }
                }

            } /*
         * Segment info shop
         */
            elseif ($segment['id_segment'] == "6" || $segment['id_segment'] == "7" || $segment['id_segment'] == "8" || $segment['id_segment'] == "10") {
                if ($segment['id_segment'] == "6") {
                    $datas = array();

                    /*
                     * Post code shop
                     */
                    $postcode = "";
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                        $sql = 'SELECT `value` FROM ' . _DB_PREFIX_ . 'configuration WHERE `name`="PS_SHOP_CODE" AND id_shop = "' . intval($shop['id_shop']) . '" ';
                        $postcode_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (!empty($postcode_query[0]["value"]))
                            $postcode = $postcode_query[0]["value"];
                        else {
                            $sql = 'SELECT c.`value` 
                              FROM ' . _DB_PREFIX_ . 'configuration c
                                INNER JOIN ' . _DB_PREFIX_ . 'shop s ON (c.id_shop_group = s.id_shop_group AND s.id_shop = "' . intval($shop['id_shop']) . ')
                              WHERE `name`="PS_SHOP_CODE" ';
                            $postcode_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                            if (!empty($postcode_query[0]["value"]))
                                $postcode = $postcode_query[0]["value"];
                            else {
                                $sql = 'SELECT `value` FROM ' . _DB_PREFIX_ . 'configuration WHERE `name`="PS_SHOP_CODE" AND (id_shop IS NULL OR id_shop=0) AND (id_shop_group IS NULL OR id_shop_group=0) ';
                                $postcode_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                                if (!empty($postcode_query[0]["value"]))
                                    $postcode = $postcode_query[0]["value"];
                            }
                        }
                    } else {
                        $sql = 'SELECT `value` FROM ' . _DB_PREFIX_ . 'configuration WHERE `name`="PS_SHOP_CODE"';
                        $postcode_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (!empty($postcode_query[0]["value"]))
                            $postcode = $postcode_query[0]["value"];
                    }

                    /*
                     * Country shop
                     */
                    $country = "";
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                        $sql = 'SELECT ct.`iso_code` 
                            FROM ' . _DB_PREFIX_ . 'configuration c
                                INNER JOIN ' . _DB_PREFIX_ . 'country ct ON (c.`value`=ct.id_country)
                            WHERE c.`name`="PS_SHOP_COUNTRY_ID" 
                            AND c.id_shop = "' . intval($shop['id_shop']) . '" ';
                        $country_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (!empty($country_query[0]["iso_code"]))
                            $country = $country_query[0]["iso_code"];
                        else {
                            $sql = 'SELECT ct.`iso_code` 
                                    FROM ' . _DB_PREFIX_ . 'configuration c
                                        INNER JOIN ' . _DB_PREFIX_ . 'country ct ON (c.`value`=ct.id_country)
                                        INNER JOIN ' . _DB_PREFIX_ . 'shop s ON (c.id_shop_group = s.id_shop_group AND s.id_shop = "' . intval($shop['id_shop']) . ')
                                 WHERE c.`name`="PS_SHOP_COUNTRY_ID" ';
                            $country_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                            if (!empty($country_query[0]["iso_code"]))
                                $country = $country_query[0]["iso_code"];
                            else {
                                $sql = 'SELECT ct.`iso_code` 
                                    FROM ' . _DB_PREFIX_ . 'configuration c
                                        INNER JOIN ' . _DB_PREFIX_ . 'country ct ON (c.`value`=ct.id_country)
                                    WHERE c.`name`="PS_SHOP_COUNTRY_ID" 
                                     AND (c.id_shop IS NULL OR c.id_shop=0) AND (c.id_shop_group IS NULL OR c.id_shop_group=0) ';
                                $country_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                                if (!empty($country_query[0]["iso_code"]))
                                    $country = $country_query[0]["iso_code"];
                            }
                        }
                    } else {
                        $sql = 'SELECT ct.`iso_code` 
                            FROM ' . _DB_PREFIX_ . 'configuration c
                                INNER JOIN ' . _DB_PREFIX_ . 'country ct ON (c.`value`=ct.id_country)
                            WHERE c.`name`="PS_SHOP_COUNTRY_ID" ';
                        $country_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (!empty($country_query[0]["iso_code"]))
                            $country = $country_query[0]["iso_code"];
                    }

                    /*
                     * Business Industry
                     */
                    $busIndus = "";
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                        $sql = 'SELECT `value` FROM ' . _DB_PREFIX_ . 'configuration WHERE `name`="PS_SHOP_ACTIVITY" AND id_shop = "' . intval($shop['id_shop']) . '" ';
                        $busIndus_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (!empty($busIndus_query[0]["value"]))
                            $busIndus = $busIndus_query[0]["value"];
                        else {
                            $sql = 'SELECT c.`value` 
                                  FROM ' . _DB_PREFIX_ . 'configuration c
                                    INNER JOIN ' . _DB_PREFIX_ . 'shop s ON (c.id_shop_group = s.id_shop_group AND s.id_shop = "' . intval($shop['id_shop']) . ')
                                  WHERE `name`="PS_SHOP_ACTIVITY" ';
                            $busIndus_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                            if (!empty($busIndus_query[0]["value"]))
                                $busIndus = $busIndus_query[0]["value"];
                            else {
                                $sql = 'SELECT `value` FROM ' . _DB_PREFIX_ . 'configuration WHERE `name`="PS_SHOP_ACTIVITY" AND (id_shop IS NULL OR id_shop=0) AND (id_shop_group IS NULL OR id_shop_group=0) ';
                                $busIndus_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                                if (!empty($busIndus_query[0]["value"]))
                                    $busIndus = $busIndus_query[0]["value"];
                            }
                        }
                    } else {
                        $sql = 'SELECT `value` FROM ' . _DB_PREFIX_ . 'configuration WHERE `name`="PS_SHOP_ACTIVITY"';
                        $busIndus_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (!empty($busIndus_query[0]["value"]))
                            $busIndus = $busIndus_query[0]["value"];
                    }

                    /*
                     * Weight unit
                     */
                    $weightUnit = "";
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                        $sql = 'SELECT `value` FROM ' . _DB_PREFIX_ . 'configuration WHERE `name`="PS_WEIGHT_UNIT" AND id_shop = "' . intval($shop['id_shop']) . '" ';
                        $weightUnit_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (!empty($weightUnit_query[0]["value"]))
                            $weightUnit = $weightUnit_query[0]["value"];
                        else {
                            $sql = 'SELECT c.`value` 
                                  FROM ' . _DB_PREFIX_ . 'configuration c
                                    INNER JOIN ' . _DB_PREFIX_ . 'shop s ON (c.id_shop_group = s.id_shop_group AND s.id_shop = "' . intval($shop['id_shop']) . ')
                                  WHERE `name`="PS_WEIGHT_UNIT" ';
                            $weightUnit_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                            if (!empty($weightUnit_query[0]["value"]))
                                $weightUnit = $weightUnit_query[0]["value"];
                            else {
                                $sql = 'SELECT `value` FROM ' . _DB_PREFIX_ . 'configuration WHERE `name`="PS_WEIGHT_UNIT" AND (id_shop IS NULL OR id_shop=0) AND (id_shop_group IS NULL OR id_shop_group=0) ';
                                $weightUnit_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                                if (!empty($weightUnit_query[0]["value"]))
                                    $weightUnit = $weightUnit_query[0]["value"];
                            }
                        }
                    } else {
                        $sql = 'SELECT `value` FROM ' . _DB_PREFIX_ . 'configuration WHERE `name`="PS_WEIGHT_UNIT"';
                        $weightUnit_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (!empty($weightUnit_query[0]["value"]))
                            $weightUnit = $weightUnit_query[0]["value"];
                    }

                    /*
                     * Dimension unit
                     */
                    $dimensionUnit = "";
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                        $sql = 'SELECT `value` FROM ' . _DB_PREFIX_ . 'configuration WHERE `name`="PS_DIMENSION_UNIT" AND id_shop = "' . intval($shop['id_shop']) . '" ';
                        $dimensionUnit_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (!empty($dimensionUnit_query[0]["value"]))
                            $dimensionUnit = $dimensionUnit_query[0]["value"];
                        else {
                            $sql = 'SELECT c.`value` 
                                  FROM ' . _DB_PREFIX_ . 'configuration c
                                    INNER JOIN ' . _DB_PREFIX_ . 'shop s ON (c.id_shop_group = s.id_shop_group AND s.id_shop = "' . intval($shop['id_shop']) . ')
                                  WHERE `name`="PS_DIMENSION_UNIT" ';
                            $dimensionUnit_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                            if (!empty($dimensionUnit_query[0]["value"]))
                                $dimensionUnit = $dimensionUnit_query[0]["value"];
                            else {
                                $sql = 'SELECT `value` FROM ' . _DB_PREFIX_ . 'configuration WHERE `name`="PS_DIMENSION_UNIT" AND (id_shop IS NULL OR id_shop=0) AND (id_shop_group IS NULL OR id_shop_group=0) ';
                                $dimensionUnit_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                                if (!empty($dimensionUnit_query[0]["value"]))
                                    $dimensionUnit = $dimensionUnit_query[0]["value"];
                            }
                        }
                    } else {
                        $sql = 'SELECT `value` FROM ' . _DB_PREFIX_ . 'configuration WHERE `name`="PS_DIMENSION_UNIT"';
                        $dimensionUnit_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (!empty($dimensionUnit_query[0]["value"]))
                            $dimensionUnit = $dimensionUnit_query[0]["value"];
                    }

                    /*
                     * Put in datas
                     */
                    $datas['shop_country'] = $country;
                    $datas['shop_postcode'] = $postcode;
                    $datas['shop_business_industry'] = $busIndus;
                    $datas['shop_weight_unit'] = $weightUnit;
                    $datas['shop_dimension_unit'] = $dimensionUnit;

                    if (!empty($datas)) {
                        $post = array("id_segment" => $segment['id_segment'], "data" => array());
                        $post['data'] = json_encode($datas);
                        $headers = array();
                        $headers[] = "SCLICENSE: " . $licence;
                        $headers[] = "SHOPID: " . $shop['id_shop'];
                        $headers[] = "SCVERSION: " . SC_VERSION;
                        $headers[] = "ID_SHOP: " . $idShops[$shop['id_shop']];
                        $ret = sc_file_post_contents('http://api.storecommander.com/Trends/SendShopData', $post, $headers);
                        $ret = (array)json_decode($ret);
                        if (!empty($ret['code']) && $ret['code'] == "200")
                            $force_stop = true;
                        else
                            $has_results = true;
                    }
                }
                elseif ($segment['id_segment'] == "7") {
                    $datas = array();

                    /*
                     * Nb products
                     */
                    $nb_products = 0;
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                        $sql = 'SELECT p.id_product 
                              FROM ' . _DB_PREFIX_ . 'product p 
                              INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON (ps.id_product=p.id_product AND ps.id_shop="' . (int)$shop['id_shop'] . '")
                            GROUP BY p.id_product';
                        $nb_products_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (!empty($nb_products_query) && count($nb_products_query) > 0)
                            $nb_products = count($nb_products_query);
                    } else {
                        $sql = 'SELECT id_product FROM ' . _DB_PREFIX_ . 'product ';
                        $nb_products_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (!empty($nb_products_query) && count($nb_products_query) > 0)
                            $nb_products = count($nb_products_query);
                    }

                    /*
                     * Nb combinations
                     */
                    $nb_combis = 0;
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                        $sql = 'SELECT pa.id_product_attribute 
                        FROM ' . _DB_PREFIX_ . 'product_attribute pa 
                        INNER JOIN ' . _DB_PREFIX_ . 'product_attribute_shop pas ON (pas.id_product_attribute=pa.id_product_attribute AND pas.id_shop="' . (int)$shop['id_shop'] . '")
                            GROUP BY pa.id_product_attribute';
                        $nb_combis_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (!empty($nb_combis_query) && count($nb_combis_query) > 0)
                            $nb_combis = count($nb_combis_query);
                    } else {
                        $sql = 'SELECT id_product_attribute FROM ' . _DB_PREFIX_ . 'product_attribute ';
                        $nb_combis_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (!empty($nb_combis_query) && count($nb_combis_query) > 0)
                            $nb_combis = count($nb_combis_query);
                    }

                    /*
                     * Nb categories
                     */
                    $nb_cats = 0;
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                        $sql = 'SELECT c.id_category 
                        FROM ' . _DB_PREFIX_ . 'category c 
                        INNER JOIN ' . _DB_PREFIX_ . 'category_shop cs ON (c.id_category=cs.id_category AND cs.id_shop="' . (int)$shop['id_shop'] . '")
                        WHERE active=1
                        GROUP BY c.id_category';
                        $nb_cats_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (!empty($nb_cats_query) && count($nb_cats_query) > 0)
                            $nb_cats = count($nb_cats_query);
                    } else {
                        $sql = 'SELECT id_category FROM ' . _DB_PREFIX_ . 'category WHERE active=1';
                        $nb_cats_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (!empty($nb_cats_query) && count($nb_cats_query) > 0)
                            $nb_cats = count($nb_cats_query);
                    }

                    /*
                     * Tips mode
                     */
                    $tips_mode = 0;
                    if (file_exists(SC_TOOLS_DIR . "tips/display_user_" . $sc_agent->id_employee . ".ini")) {
                        $ini_file = parse_ini_file(SC_TOOLS_DIR . "tips/display_user_" . $sc_agent->id_employee . ".ini");
                        if (!empty($ini_file['mode']))
                            $tips_mode = $ini_file['mode'];
                    }

                    /*
                     * Put in datas
                     */
                    $datas['shop_nb_products'] = $nb_products;
                    $datas['shop_nb_combis'] = $nb_combis;
                    $datas['shop_nb_categories'] = $nb_cats;
                    $datas['email'] = $sc_agent->email;
                    $datas['tips_mode'] = $tips_mode;
                    if (!empty($datas)) {
                        $post = array("id_segment" => $segment['id_segment'], "data" => array());
                        $post['data'] = json_encode($datas);
                        $headers = array();
                        $headers[] = "SCLICENSE: " . $licence;
                        $headers[] = "SHOPID: " . $shop['id_shop'];
                        $headers[] = "SCVERSION: " . SC_VERSION;
                        $headers[] = "ID_SHOP: " . $idShops[$shop['id_shop']];
                        $ret = sc_file_post_contents('http://api.storecommander.com/Trends/SendShopData', $post, $headers);
                        $ret = (array)json_decode($ret);
                        /*if (!empty($ret['code']) && $ret['code'] == "200")
                            $force_stop = true;*/
                        $has_results = true;
                    }
                }
                elseif ($segment['id_segment'] == "8") {
                    $datas = array();

                    /*
                     * FF active
                     */
                    $FF_active = SCI::getConfigurationValue("SC_FOULEFACTORY_ACTIVE");
                    if(empty($FF_active))
                        $FF_active = 0;

                    /*
                     * Put in datas
                     */
                    $datas['ff_active'] = $FF_active;
                    $datas['email'] = $sc_agent->email;

                    if (!empty($datas)) {
                        $post = array("id_segment" => $segment['id_segment'], "data" => array());
                        $post['data'] = json_encode($datas);
                        $headers = array();
                        $headers[] = "SCLICENSE: " . $licence;
                        $headers[] = "SHOPID: " . $shop['id_shop'];
                        $headers[] = "SCVERSION: " . SC_VERSION;
                        $headers[] = "ID_SHOP: " . $idShops[$shop['id_shop']];
                        $ret = sc_file_post_contents('http://api.storecommander.com/Trends/SendShopData', $post, $headers);
                        $ret = (array)json_decode($ret);
                        /*if (!empty($ret['code']) && $ret['code'] == "200")
                            $force_stop = true;*/
                        $has_results = true;
                    }
                }
                elseif ($segment['id_segment'] == "10")
                {
                    $datas = array();

                    /*
                     * Rich Editor
                     */
                    $richeditor = "ckeditor";
                    if(_s("APP_RICH_EDITOR")==1)
                        $richeditor = "tinymce";

                    /*
                     * MODULES
                     */
                    $modules = "-";
                    $sql = 'SELECT name FROM ' . _DB_PREFIX_ . 'module WHERE active=1';
                    $modules_query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                    if(!empty($modules_query))
                    {
                        foreach ($modules_query as $module)
                            $modules .= $module["name"]."-";
                    }

                    /*
                     * Put in datas
                     */
                    $datas['richeditor'] = $richeditor;
                    $datas['modules'] = $modules;
                    $datas['email'] = $sc_agent->email;

                    if (!empty($datas)) {
                        $post = array("id_segment" => $segment['id_segment'], "data" => array());
                        $post['data'] = json_encode($datas);
                        $headers = array();
                        $headers[] = "SCLICENSE: " . $licence;
                        $headers[] = "SHOPID: " . $shop['id_shop'];
                        $headers[] = "SCVERSION: " . SC_VERSION;
                        $headers[] = "ID_SHOP: " . $idShops[$shop['id_shop']];
                        $ret = sc_file_post_contents('http://api.storecommander.com/Trends/SendShopData', $post, $headers);
                        $ret = (array)json_decode($ret);
                        /*if (!empty($ret['code']) && $ret['code'] == "200")
                            $force_stop = true;*/
                        $has_results = true;
                    }
                }
            }elseif ($segment['id_segment'] == "9") {
                $datas = array();

                /*
                 * FF active
                 */
                $FF_active = SCI::getConfigurationValue("SC_FOULEFACTORY_ACTIVE");
                if(!empty($FF_active))
                {
                    $is_default_shop = 1;
                    if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $default_shop = SCI::getConfigurationValue('PS_SHOP_DEFAULT');
                        if($default_shop!=$shop['id_shop'])
                            $is_default_shop = 0;
                    }
                    if($is_default_shop)
                    {
                        require_once("lib/php/foulefactory/FFApi.php");
                        require_once("lib/php/foulefactory/FfProject.php");
                        $projects = array();
                        /*
                         * Get projects
                         */
                        $sql = "SELECT * FROM "._DB_PREFIX_."sc_ff_project ORDER BY id_project DESC";
                        $res=Db::getInstance()->ExecuteS($sql);
                        foreach($res AS $row)
                        {
                            $nb_pdt = 0;
                            $cat = new Category((int)$row["id_category"]);
                            $nb = $cat->getProducts(null,1,1,null,null,true,false);
                            if(!empty($nb))
                                $nb_pdt = $nb;

                            $quality = "";
                            if(!empty($row['params']))
                            {
                                $params = unserialize($row['params']);
                                if(!empty($params["quality"]))
                                    $quality = $params["quality"];
                            }

                            $projects[] = array(
                                "id_project" => $row["id_project"],
                                "name" => $row["name"],
                                "type" => $row["type"],
                                "quality" => $quality,
                                "nb_products" => $nb_pdt,
                                "amount" => $row["tarif"],
                                "status" => $row["status"]
                            );
                        }

                        /*
                         * Put in datas
                         */
                        if(!empty($projects))
                        {
                            $datas['projects'] = $projects;
                            $datas['email'] = $sc_agent->email;
                            if (!empty($datas)) {
                                $post = array("id_segment" => $segment['id_segment'], "data" => array());
                                $post['data'] = json_encode($datas);
                                $headers = array();
                                $headers[] = "SCLICENSE: " . $licence;
                                $headers[] = "SHOPID: " . $shop['id_shop'];
                                $headers[] = "SCVERSION: " . SC_VERSION;
                                $headers[] = "ID_SHOP: " . $idShops[$shop['id_shop']];
                                $ret = sc_file_post_contents('http://api.storecommander.com/Trends/SendShopData', $post, $headers);
                                $ret = (array)json_decode($ret);
                                /*if (!empty($ret['code']) && $ret['code'] == "200")
                                    $force_stop = true;*/
                                $has_results = true;
                            }
                        }
                    }
                }
            }

            if ($has_results)
                break;
        }
    }
    if($has_results)
        break;
}

if(!$has_results || $force_stop)
    $return = array("stop"=>"1");

echo json_encode($return);