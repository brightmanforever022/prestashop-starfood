<?php
  // Wheelronix Ltd. development team
  // site: http://www.wheelronix.com
  // mail: info@wheelronix.com
  //

class ShippingExporterCore
{
    const RowEnd = "\r\n";
    const Separator = '|';
    const DHLSaveFolder = '/dhl/';  // related with shop root
    const EUZoneId = 7;
    const DEZoneId = 6;
    
    public static $dhlCarrierIds = array(34, 41, 45, 44);
    public static $dhlExpressCarrierIds = array(38, 39);

    public static $germanIslandZips = array(18565,25846,25847,25849,25859,25863,25869,25929,25930,25931,25932,25933,25938,25939,25940,25941,25942,25946,25947,25948,25949,25952,
                                            25953,25954,25955,25961,25962,25963,25964,25965,25966,25967,25968,25969,25970,25980,25985,25986,25988,25989,25990,25992,25993,25994,
                                            25996,25997,25998,25999,25845,26465,26474,26486,26548,26571,26579,26757,27498,83209,83256);
    
    const GermanyCountryId = 1;
    // min quantity product should have to be shown in summary list in shipping info
    const ProductSummaryMinQty = 4; 
                                    
    
    /**
     * Generates csv list of shipping info/addresses for given orders. Format of
     * list is following:
     * Name	Vorname	Strasse	PLZ	Ort	Email	Land	Versandart OrderId
     * Outputs xls and exits.
     * @param $orderIds array of ids of order for that we need to generate list.
     */
    static function generateDHLList($orderIds)
    {
        if(count($orderIds)==0)
        {
            return;
        }


        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=package_info.zip");

        header('Pragma: no-cache',true);
        header('Expires: 0',true);

        //ini_set('display_errors', 'on');
        
        $saveFileNameDpd = _PS_ROOT_DIR_.self::DHLSaveFolder.'dpd.csv';
        $saveFileNameDhl = _PS_ROOT_DIR_.self::DHLSaveFolder.'dhl.csv';
        $saveFileNameForeign = _PS_ROOT_DIR_.self::DHLSaveFolder.'auslaender.csv';
        $saveFileNameZip = _PS_ROOT_DIR_.self::DHLSaveFolder.'package_info.zip';

        $dpdCsv = $dhlCsv = $foreignCsv = self::encodeField('Name').self::Separator.self::encodeField('Vorname').self::Separator.self::encodeField('Strasse').
            self::Separator.self::encodeField('PLZ').self::Separator.self::encodeField('Ort').self::Separator.
            self::encodeField('Email').self::Separator.self::encodeField('Land').self::Separator.self::encodeField('Versandart').self::Separator.
            self::encodeField('Strasse2').self::Separator.self::encodeField('Firma').self::Separator.
            self::encodeField('Order id').self::Separator.self::encodeField('serviceliste').self::Separator.self::encodeField('verfahren').self::Separator.
            self::encodeField('produkt').self::Separator.self::encodeField('teilnahme').self::Separator.self::encodeField('method').self::Separator.
            self::encodeField('cash on delivery amount').self::Separator.self::encodeField('Currency').self::Separator.self::encodeField('payment type').
            self::Separator.self::encodeField('phone').self::Separator.self::encodeField('zhd').self::Separator.self::encodeField('benachrichtigungsart').
            self::Separator.self::encodeField('email').self::Separator.self::encodeField('flex').self::Separator.self::encodeField('flex4').
            self::Separator.self::encodeField('Name').self::RowEnd;
        
        // reading orders
        $db = Db::getInstance();
        $sql = 'select ad.firstname, ad.lastname, ad.address1, ad.address2, ad.company, ad.postcode, ad.city, ad.other, c.email, country.name as country_name,'.
            ' o.id_order, source, o.module, o.id_carrier, o.total_paid, o.id_cart, o.payment, ad.phone, ad.phone_mobile, ad.id_country FROM '
            ._DB_PREFIX_.'orders o, '.
            _DB_PREFIX_.'customer c, '._DB_PREFIX_.'address ad, '._DB_PREFIX_.'country_lang country WHERE '.
            'o.id_customer=c.id_customer and id_address_delivery=ad.id_address and ad.id_country=country.id_country and country.id_lang= '.Configuration::get('PS_LANG_DEFAULT').
            ' and id_order in('.implode(',', $orderIds).')';

        $orders = $db->ExecuteS($sql);

        if ($orders)
        {
            self::sortOrders($orders);
        }

        foreach($orders as $order)
        {
            if ($order['id_country']!=self::GermanyCountryId && !($order['payment']=='Nachnahme' && $order['module']=='Ebay'))
            {
                $foreignCsv .= self::getDhlCsvLine($order, true);
            }
            else
            {
                $gluedAddress = $order['address1'].' '.$order['address2'].' '.$order['company'].$order['postcode'].' '.$order['city'].$order['other'].$order['lastname'].
                    $order['firstname'];
                if (stripos($gluedAddress, 'packstation')!==false)
                {
                    // cut numbers
                    if (preg_match('/\D(\d{3})\D/', 'a'.$gluedAddress, $num1) && preg_match('/\D(\d{7,12})\D/', 'a'.$gluedAddress, $num2))
                    {
                        $order['address1'] = 'Packstation '.$num1[1];
                        $order['address2'] = $num2[1];
                    }
                    $dhlCsv .= self::getDhlCsvLine($order);
                }
                elseif (self::isDhlOrder($order))
                {
                    $dhlCsv .= self::getDhlCsvLine($order);
                }
                else
                {
                    $dpdCsv .= self::getDhlCsvLine($order, true);
                }
            }
        }

        // convert to ANSI aka WIN-1252
        $dpdCsv = iconv("UTF8", "WINDOWS-1252//TRANSLIT", $dpdCsv);
        $dhlCsv = iconv("UTF8", "WINDOWS-1252//TRANSLIT", $dhlCsv);
        $foreignCsv = iconv("UTF8", "WINDOWS-1252//TRANSLIT", $foreignCsv);
        
        // save files
        file_put_contents($saveFileNameDpd, $dpdCsv);
        file_put_contents($saveFileNameDhl, $dhlCsv);
        file_put_contents($saveFileNameForeign, $foreignCsv);
        
        // output
        $zip = new ZipArchive();
        if ($zip->open($saveFileNameZip, ZIPARCHIVE::CREATE)!==TRUE) {
            exit('Can\'t open zip file');
        }
        $zip->addFromString('dpd.csv', $dpdCsv);
        $zip->addFromString('dhl.csv', $dhlCsv);
        $zip->addFromString('auslaender.csv', $foreignCsv);
        $zip->close();
        
        readfile($saveFileNameZip);
        exit;
    }


    /**
     * @returns true if order should be exported to dhl list
     */
    static function isDhlOrder(&$order)
    {
        $gluedAddress = $order['address1'].' '.$order['address2'].' '.$order['company'].$order['postcode'].' '.$order['city'].$order['other'].$order['lastname'].
            $order['firstname'];
        return $order['module']=='maofree_cashondeliveryfee' || stripos($gluedAddress, 'postfach')!==false || stripos($gluedAddress, 'postfiliale')
            || ($order['payment']=='Nachnahme' && $order['module']=='Ebay' && $order['id_country']==self::GermanyCountryId)
            || in_array($order['postcode'], self::$germanIslandZips) || stripos($gluedAddress, 'packstation')!==false
        	|| stripos($gluedAddress, 'postfiliale')!==false || 
            ($order['id_country']==self::GermanyCountryId && Configuration::get('PS_DHL_DEFAULT_DELIVERY'));
    }
    
    
    /**
     * Generates file with name dhl.csv or dpd.csv with single given order,
     * saves it on server and sends to browser
     * @param $orderId id of order that we export
     * @param $dpd flag that tells if file should be called dhl or dpd
     */
    static function genSingleDhlFile($orderId, $dpd=false)
    {
        // reading order
        $sql = 'select ad.firstname, ad.lastname, ad.address1, ad.address2, ad.company, ad.postcode, ad.city, ad.other, c.email, country.name as country_name,'.
            ' o.id_order, source, o.module, o.id_carrier, o.total_paid, o.id_cart, ad.phone, ad.phone_mobile, ad.id_country, o.payment FROM '
            ._DB_PREFIX_.'orders o, '.
            _DB_PREFIX_.'customer c, '._DB_PREFIX_.'address ad, '._DB_PREFIX_.'country_lang country WHERE '.
            'o.id_customer=c.id_customer and id_address_delivery=ad.id_address and ad.id_country=country.id_country and country.id_lang= '.Configuration::get('PS_LANG_DEFAULT').
            ' and id_order = '.$orderId;
        $order = Db::getInstance()->getRow($sql);

        // generate output
        $csvLine = self::encodeField('Name').self::Separator.self::encodeField('Vorname').self::Separator.self::encodeField('Strasse').
            self::Separator.self::encodeField('PLZ').self::Separator.self::encodeField('Ort').self::Separator.
            self::encodeField('Email').self::Separator.self::encodeField('Land').self::Separator.self::encodeField('Versandart').self::Separator.
            self::encodeField('Strasse2').self::Separator.self::encodeField('Firma').self::Separator.
            self::encodeField('Order id').self::Separator.self::encodeField('serviceliste').self::Separator.self::encodeField('verfahren').self::Separator.
            self::encodeField('produkt').self::Separator.self::encodeField('teilnahme').self::Separator.self::encodeField('method').self::Separator.
            self::encodeField('cash on delivery amount').self::Separator.self::encodeField('Currency').self::Separator.self::encodeField('payment type').
            self::Separator.self::encodeField('phone').self::Separator.self::encodeField('zhd').self::Separator.self::encodeField('benachrichtigungsart').
            self::Separator.self::encodeField('email').self::Separator.self::encodeField('flex').self::Separator.self::encodeField('flex4').
            self::Separator.self::encodeField('Name').self::RowEnd;
        $csvLine .= self::getDhlCsvLine($order, $dpd);
        $csvLine = iconv("UTF8", "WINDOWS-1252//TRANSLIT", $csvLine);
        $resultFileName = _PS_ROOT_DIR_.self::DHLSaveFolder.($dpd?'dpd.csv':'dhl.csv');

        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.($dpd?'dpd.csv':'dhl.csv'));
      
        header('Pragma: no-cache',true);
        header('Expires: 0',true);
        
        file_put_contents($resultFileName, $csvLine);
        readfile($resultFileName);
        exit;
    }
    

    /**
     * @param $packstation flag tells to format "packstation" addresses
     */
    static function &getDhlCsvLine($order, $dpd=false)
    {
        if (Order::isAmazonOrderSt($order['source']) && !Configuration::get('PS_AMAZON_CSV_EMAIL_EXPORT'))
        {
            $exportEmail = false;
        }
        else
        {
            $exportEmail = true;
        }
        
        $csv = self::encodeField($order['firstname']).self::Separator.self::encodeField($order['lastname']).self::Separator.
            self::encodeField($order['address1']).self::Separator.self::encodeField($order['postcode']).self::Separator.self::encodeField($order['city']).
            self::Separator.self::encodeField(($exportEmail)?$order['email']:'').self::Separator.self::encodeField($order['country_name']).
            self::Separator.self::encodeField('DHL Paket').self::Separator.self::encodeField($order['address2']).self::Separator.
            self::encodeField($order['company']).self::Separator.self::encodeField($order['id_order']);

        $dpdMethodTail = $dpd?self::getDpdMethodFieldTail($order):'';
        $expressOrder = false;
        if (in_array($order['id_carrier'], self::$dhlExpressCarrierIds))
        {
            $expressOrder = true;
            if ($order['module']=='maofree_cashondeliveryfee' || $order['source']==Order::SourceEbay && $order['payment']=='Nachnahme')
            {
                $orderTotal = number_format($order['total_paid'], 2, ',', '');
                $csv .= self::Separator.self::encodeField('7210;7224='.$orderTotal)
                    .self::Separator.self::encodeField('72').self::Separator.self::encodeField('7202').self::Separator.self::encodeField('01').
                    self::Separator.self::encodeField('Express cash on delivery').$dpdMethodTail.self::Separator.self::encodeField($orderTotal);
            }
            else
            {
                $csv .= self::Separator.self::encodeField('7210').self::Separator.self::encodeField('72').self::Separator.
                    self::encodeField('7205').self::Separator.self::encodeField('01').self::Separator.self::encodeField('Express standard payment').
                    $dpdMethodTail.self::Separator.self::encodeField('');
            }
        }
        elseif(in_array($order['id_carrier'], self::$dhlCarrierIds))
        {
            if ($order['module']=='maofree_cashondeliveryfee' || $order['source']==Order::SourceEbay && $order['payment']=='Nachnahme')
            {
                $orderTotal = number_format($order['total_paid'], 2, ',', '');
                    
                $csv .= self::Separator.self::encodeField('134='.$orderTotal).self::Separator.self::encodeField('1').
                    self::Separator.self::encodeField('101').self::Separator.self::encodeField('02').self::Separator.self::encodeField('Standard cash on delivery').
                    $dpdMethodTail.self::Separator.self::encodeField($orderTotal);
            }
            else
            {
                $csv .= self::Separator.self::encodeField('').self::Separator.self::encodeField('1').self::Separator.self::encodeField('101').
                    self::Separator.self::encodeField('02').self::Separator.($dpdMethodTail?trim($dpdMethodTail):self::encodeField('Standard')).
                    self::Separator.self::encodeField('');
            }
        }
        else
        {
            // empty values for columns that we don't know how to fill
            $csv .= self::Separator.self::encodeField('').self::Separator.self::encodeField('').self::Separator.self::encodeField('').
                self::Separator.self::encodeField('').self::Separator.self::encodeField('').($dpd?self::getDpdMethodFieldTail($order):'').self::Separator.self::encodeField('');
        }

        $phone = empty($order['phone'])?$order['phone_mobile']:$order['phone'];
        $csv .= self::Separator.self::encodeField('EUR').self::Separator.self::encodeField('cash').self::Separator.self::encodeField($phone).
            self::Separator.self::encodeField($order['firstname'].' '.$order['lastname']);

        if ($expressOrder)
        {
            $csv .= self::Separator.self::encodeField('').self::Separator.self::encodeField('').self::Separator.self::encodeField('').self::Separator.self::encodeField('');
        }
        else
        {
            $csv .= self::Separator.self::encodeField('E').
                self::Separator.self::encodeField($exportEmail?$order['email']:'').
                self::Separator.self::encodeField('904').self::Separator.self::encodeField('DE');
        }
        
        $csv .= self::Separator.self::encodeField($order['firstname'].' '.$order['lastname']).self::RowEnd;

        return $csv;
    }


    /**
     * @param $order assoc array with order data. Must be dpd order.
     * @returns tail (string) that should be added to method field of csv file. 
     */
    static function getDpdMethodFieldTail(&$order)
    {
        if ((Configuration::get('PS_AMAZON_CSV_EMAIL_EXPORT') || !Order::isAmazonOrderSt($order['source'])) &&
            !in_array($order['id_carrier'], ShippingExporter::$dhlExpressCarrierIds) && !empty($order['email']))
        {
            return ' SCP,PRO';
        }
    }

    /**
     * Prints information about given orders as html
     */
    static function exportHtmlInfo($orderIds)
    {
        $db = Db::getInstance();
        
        // reading orders
        $sql = 'select o.id_order, o.id_cart, o.id_carrier, o.payment, o.module, ad.firstname, ad.lastname, ad.address1, ad.address2, ad.company, ad.postcode,'.
            ' ad.city, ad.other, '.
            ' c.email, ad.id_country, cr.name as carrier_name, country.name as country_name, (select oh.date_add from '.
            _DB_PREFIX_.'order_history oh  where oh.id_order=o.id_order and id_order_state='._PS_OS_PAYMENT_.' order by date_add desc limit 1) as paid_date'.
            ' FROM '._DB_PREFIX_.'orders o left join '._DB_PREFIX_.'carrier cr on o.id_carrier=cr.id_carrier, '.
            _DB_PREFIX_.'customer c, '._DB_PREFIX_.'address ad, '._DB_PREFIX_.'country_lang country WHERE '.
            'o.id_customer=c.id_customer and id_address_delivery=ad.id_address and ad.id_country=country.id_country and country.id_lang='.Configuration::get('PS_LANG_DEFAULT').
            ' and o.id_order in('.implode(',', $orderIds).')';

        //echo $sql;
        $orders = $db->s($sql);

        if ($orders)
        {
            self::sortOrders($orders);
        }

        // prepare data
        $summaryProducts = array();
        foreach($orders as $key => $order)
        {
            // reading messages
            $messages = '';
            $dbMessages = $db->s('select message from '._DB_PREFIX_.'message where id_order='.$order['id_order']);
            for($i=0; $i<count($dbMessages); $i++)
            {
                if ($i>0)
                {
                    $messages .= "<hr>\n";
                }
                $messages .= $dbMessages[$i]['message'];
            }
            $orders[$key]['messages'] = $messages;
            
            // output order
            $orders[$key]['products'] = self::getProductsInfo($order['id_order']);

            // prepare summary products
            foreach($orders[$key]['products'] as $product)
            {
                // combine products for summary list
                $productId = $product['product_id'].'-'.$product['product_attribute_id'];
                if (isset($summaryProducts[$productId]))
                {
                    $summaryProducts[$productId]['product_quantity'] += $product['product_quantity'];
                }
                else
                {
                    $summaryProducts[$productId] = $product;
                }
            }
        }

        // copy to preserve keys
        $summaryProductsUnsorted = $summaryProducts;
        
        // sort summary products by quantity
        usort($summaryProducts, function($product1, $product2){

                return $product1['product_quantity']<$product2['product_quantity'];
            });


        // output orders, table header
        echo '<html>
              <head>
              <link href="themes/default/css/admin-theme.css" rel="stylesheet" type="text/css">
              <style type="text/css" media="all">
               * {font-size: 16px;}
               body{background-color: #fff;}
               .icon-check {color: #72C279;}
               .icon-remove {color: #E08F95;}
               table {border-collapse: collapse; border:1px solid black}
               table th {background-color: #ccc;} 
               table td {border: 1px solid black; padding:10px; border-spacing:0px}
               table.products {border: none;}
               table.products td {border: none; padding:3px;}
               .bold { font-weight: bold;}
               .boldRed { font-weight: bold; color:red }
               .redBg {background-color: red;}
               .supplierReference{font-size: 18px;}
                .summaryReference{color: red}
              </style>
              </head>
              <body>';
        
        //show summary products list
        $link = new Link();

        $summaryProductsList = '';
        foreach($summaryProducts as $product)
        {
            if ($product['product_quantity']>=self::ProductSummaryMinQty)
            {
                $summaryProductsList .= '<tr><td '.($product['product_quantity']!=1?'class="boldRed"':'').'>'.$product['product_quantity'].
                    '</td><td>'.($product['quantity_in_stock']>0?'<i class="icon-check"></i>':'<i class="icon-remove"></i>').
                    '</td><td><img src="//'.$link->getImageLink('aaa', $product['product_id'].'-'.$product['id_image'], 'cart_default').
                    '"></td><td>'.$product['product_name'];
                if(!empty($product['attribute_location']))
                {
                    $productLocation = ', '.$product['attribute_location'];
                }
                elseif(!empty($product['location']))
                {
                    $productLocation = ', '.$product['location'];
                }
                else
                {
                    $productLocation = '';
                }
            
                $summaryProductsList .= '</td><td class="bold supplierReference">'.$product['product_supplier_reference'].$productLocation.'</td></tr>';
            }
        }

        if (!empty($summaryProductsList))
        {
            echo '<h1>Summary products</h1><table><tr><th>Anzahl</th><th>Auf Lager</th><th>Foto</th><th>Artikel</th><th>Artikelnummer</th></tr>';
            echo $summaryProductsList;
            echo '</table>';
        }
        

        echo '<h1>Orders:</h1><table>
              <tr>
                 <th>Bestellnummer</th><th>Produkte</th><th>Kommentare</th><th>Anschrift</th>
              </tr>';
        foreach($orders as $order)
        {
            // prepare product details
            $warningQuantity = false;
            $details = '<table class="products"><tr><th>Anzahl</th><th>Auf Lager</th><th>Shipped</th><th>Foto</th><th>Artikel</th><th>Artikelnummer</th></tr>';
            foreach($order['products'] as $product)
            {
                if ($product['product_quantity']>1)
                {
                    $warningQuantity = true;
                }
            
                $details .= '<tr><td '.($product['product_quantity']!=1?'class="boldRed"':'').'>'.$product['product_quantity'].
                    '</td><td>'.($product['quantity_in_stock']>0?'<i class="icon-check"></i>':'<i class="icon-remove"></i>').
                    '</td><td>'.($product['shipped']==1?'<i class="icon-check"></i>':'<i class="icon-remove"></i>').'</td><td><img src="//'.$link->getImageLink('aaa', $product['product_id'].'-'.$product['id_image'], 'cart_default').'"></td>'.
                    '<td>'.$product['product_name'];

                /*
                 if(!empty($product['attribute_value']))
                 {
                 $result .= '<br>'.$product['attribute_name'].': '.$product['attribute_value'];
                 }
                */
            
                if(!empty($product['attribute_location']))
                {
                    $productLocation = ', '.$product['attribute_location'];
                }
                elseif(!empty($product['location']))
                {
                    $productLocation = ', '.$product['location'];
                }
                else
                {
                    $productLocation = '';
                }

                // deal with supplier reference highlight for summaty products
                $productId = $product['product_id'].'-'.$product['product_attribute_id'];
                
                if ($summaryProductsUnsorted[$productId]['product_quantity']>=self::ProductSummaryMinQty)
                {
                    $details .= '</td><td class="bold supplierReference summaryReference">';
                }
                else
                {
                    $details .= '</td><td class="bold supplierReference">';
                }
                $details .= $product['product_supplier_reference'].$productLocation.'</td></tr>';
            }
            if(count($order['products'])==0)
            {
                $details .= '<tr><td colspan="6"><em>There is no not shipped products in this order</em></td></tr>';
            }
            $details .= '</table>';

            
            // dealing with customer column class
            $customerColumnClass = '';
            if ($order['id_country'] != self::GermanyCountryId)
            {
                $customerColumnClass = 'class="boldRed"';
            }
            if ($warningQuantity)
            {
                $customerColumnClass = 'class="redBg"';
            }

            if (in_array($order['id_carrier'], self::$dhlExpressCarrierIds))
            {
                $carrier = '<span class="boldRed">'.$order['carrier_name'].'</span>';
            }
            else
            {
                $carrier = $order['carrier_name'];
            }
            
            echo '<tr><td>'.$order['id_order'].'<br/>'.$carrier.
                '</td><td>'.$details
                .'</td><td>'.$order['messages'].'</td>'.
                '<td '.$customerColumnClass.'>'.
                $order['firstname'].' '.$order['lastname'].'<br>'.$order['address1'].' '.$order['address2'].'<br> '.$order['postcode'].' '.$order['city'].
                '<br> '.$order['country_name'].(!empty($order['paid_date'])?'<br><br>Paid: '.strftime('%c', strtotime($order['paid_date'])):'').'</td></tr>';
        }

        // output footer
        echo '</table></body></html>';
    }

    
    /**
     * Prints information about given orders as html
     */
    static function exportPdfInfo($orderIds)
    {
        $db = Db::getInstance();
        
        // reading orders
        $sql = 'select o.id_order, o.id_cart, o.id_carrier, o.payment, o.module, ad.firstname, ad.lastname, ad.address1, ad.address2, ad.company, ad.postcode,'.
            ' ad.city, ad.other, '.
            ' c.email, ad.id_country, cr.name as carrier_name, country.name as country_name, (select oh.date_add from '.
            _DB_PREFIX_.'order_history oh  where oh.id_order=o.id_order and id_order_state='._PS_OS_PAYMENT_.' order by date_add desc limit 1) as paid_date'.
            ' FROM '._DB_PREFIX_.'orders o left join '._DB_PREFIX_.'carrier cr on o.id_carrier=cr.id_carrier, '.
            _DB_PREFIX_.'customer c, '._DB_PREFIX_.'address ad, '._DB_PREFIX_.'country_lang country WHERE '.
            'o.id_customer=c.id_customer and id_address_delivery=ad.id_address and ad.id_country=country.id_country and country.id_lang='.Configuration::get('PS_LANG_DEFAULT').
            ' and o.id_order in('.implode(',', $orderIds).')';

        //echo $sql;
        $orders = $db->s($sql);

        if ($orders)
        {
            self::sortOrders($orders);
        }

        // prepare data
        $summaryProducts = array();
        foreach($orders as $key => $order)
        {
            // reading messages
            $messages = '';
            $dbMessages = $db->s('select message from '._DB_PREFIX_.'message where id_order='.$order['id_order']);
            for($i=0; $i<count($dbMessages); $i++)
            {
                if ($i>0)
                {
                    $messages .= "<hr>\n";
                }
                $messages .= $dbMessages[$i]['message'];
            }
            $orders[$key]['messages'] = $messages;
            
            // output order
            $orders[$key]['products'] = self::getProductsInfo($order['id_order']);

            // prepare summary products
            foreach($orders[$key]['products'] as $product)
            {
                // combine products for summary list
                $productId = $product['product_id'].'-'.$product['product_attribute_id'];
                if (isset($summaryProducts[$productId]))
                {
                    $summaryProducts[$productId]['product_quantity'] += $product['product_quantity'];
                }
                else
                {
                    $summaryProducts[$productId] = $product;
                }
            }
        }

        // copy to preserve keys
        $summaryProductsUnsorted = $summaryProducts;
        
        // sort summary products by quantity
        usort($summaryProducts, function($product1, $product2){

                return $product1['product_quantity']<$product2['product_quantity'];
            });


        // output orders, table header
            
        $result = '<html>
              <head>
              <link href="themes/default/css/admin-theme.css" rel="stylesheet" type="text/css">
              <style type="text/css" media="all">
               * {font-size: 16px;}
               body{background-color: #fff;}
               .icon-check {color: #72C279; font-family: fontawesome; }
               .icon-remove {color: #E08F95; font-family: fontawesome;}
               table {border-collapse: collapse; border:1px solid black}
               table th {background-color: #ccc;} 
               table td {border: 1px solid black; padding:10px; border-spacing:0px}
               table.products {border: none;}
               table.products td {border: none; padding:3px;}
               .bold { font-weight: bold;}
               .boldRed { font-weight: bold; color:red }
               .redBg {background-color: red;}
               .supplierReference{font-size: 18px;}
                .summaryReference{color: red}
              </style>
              </head>
              <body>';
        
        //show summary products list
        $link = new Link();

        $summaryProductsList = '';
        foreach($summaryProducts as $product)
        {
            if ($product['product_quantity']>=self::ProductSummaryMinQty)
            {
                $summaryProductsList .= '<tr><td '.($product['product_quantity']!=1?'class="boldRed"':'').'>'.$product['product_quantity'].
                    '</td><td>'.($product['quantity_in_stock']>0?'<i class="icon-check"></i>':'<i class="icon-remove"></i>').
                    '</td><td><img src="//'.$link->getImageLink('aaa', $product['product_id'].'-'.$product['id_image'], 'cart_default').
                    '"></td><td>'.$product['product_name'];
                if(!empty($product['attribute_location']))
                {
                    $productLocation = ', '.$product['attribute_location'];
                }
                elseif(!empty($product['location']))
                {
                    $productLocation = ', '.$product['location'];
                }
                else
                {
                    $productLocation = '';
                }
            
                $summaryProductsList .= '</td><td class="bold supplierReference">'.$product['product_supplier_reference'].$productLocation.'</td></tr>';
            }
        }

        if (!empty($summaryProductsList))
        {
            $result .=  '<h1>Summary products</h1><table><tr><th>Anzahl</th><th>Auf Lager</th><th>Foto</th><th>Artikel</th><th>Artikelnummer</th></tr>';
            $result .=  $summaryProductsList;
            $result .=  '</table>';
        }
        

        $result .=  '<h1>Bestellungen:</h1>';
        foreach($orders as $i=>$order)
        {
            if($i>0)
            {
                $result .= '<pagebreak />';
            }
            if (in_array($order['id_carrier'], self::$dhlExpressCarrierIds))
            {
                $carrier = '<span class="boldRed">'.$order['carrier_name'].'</span>';
            }
            else
            {
                $carrier = $order['carrier_name'];
            }
            $result .=  'Bestellnummer: '.$order['id_order'].'<br/>'.$carrier.
                '
                <table autosize="1">
              <tr>
                 <th>Produkte</th><th>Kommentare</th><th>Anschrift</th>
              </tr>';
            // prepare product details
            $warningQuantity = false;
            $detailsParts = []; //'<table class="products"><tr><th>Anzahl</th><th>Auf Lager</th><th>Shipped</th><th>Foto</th><th>Artikel</th><th>Artikelnummer</th></tr>';
            $details = '';
            foreach($order['products'] as $productI=>$product)
            {
                if ($product['product_quantity']>1)
                {
                    $warningQuantity = true;
                }
                /*
                $heigth = '';
                if (count($detailsParts)==0 && count($order['products'])>6)
                {
                    if ($i==0)
                    {
                        $heigth = 'height="115px"';
                    }
                    else
                    {
                        $heigth = 'height="120px"';
                    }
                }*/
                $details .= '<tr><td '.($product['product_quantity']!=1?'class="boldRed"':'').'>'.$product['product_quantity'].
                    '</td><td>'.($product['quantity_in_stock']>0?'<span class="icon-check">&#xf00c;</span>':'<span class="icon-remove">&#xf00d;</span>').
                    '</td><td>'.($product['shipped']==1?'<span class="icon-check">&#xf00c;</span>':'<span class="icon-remove">&#xf00d;</span>').
                    '</td><td><img src="//'.$link->getImageLink('aaa', $product['product_id'].'-'.$product['id_image'], 'cart_default').'"></td>'.
                    '<td>'.$product['product_name'];

                /*
                 if(!empty($product['attribute_value']))
                 {
                 $result .= '<br>'.$product['attribute_name'].': '.$product['attribute_value'];
                 }
                */
            
                if(!empty($product['attribute_location']))
                {
                    $productLocation = ', '.$product['attribute_location'];
                }
                elseif(!empty($product['location']))
                {
                    $productLocation = ', '.$product['location'];
                }
                else
                {
                    $productLocation = '';
                }

                // deal with supplier reference highlight for summaty products
                $productId = $product['product_id'].'-'.$product['product_attribute_id'];
                
                if ($summaryProductsUnsorted[$productId]['product_quantity']>=self::ProductSummaryMinQty)
                {
                    $details .= '</td><td class="bold supplierReference summaryReference">';
                }
                else
                {
                    $details .= '</td><td class="bold supplierReference">';
                }
                $details .= $product['product_supplier_reference'].$productLocation.'</td></tr>';
                
                
                // less products in first page
                if ($productI==5 || $productI>8 && ($productI-5)%8 == 0)
                {
                    $detailsParts []= $details;
                    $details = '';
                }
            }
            if(count($order['products'])==0)
            {
                $detailsParts[0] = '<tr><td colspan="6"><em>There is no not shipped products in this order</em></td></tr>';
            }
            elseif(!empty ($details))
            {
                    $detailsParts []= $details;
            }
            
            // dealing with customer column class
            $customerColumnClass = '';
            if ($order['id_country'] != self::GermanyCountryId)
            {
                $customerColumnClass = 'class="boldRed"';
            }
            if ($warningQuantity)
            {
                $customerColumnClass = 'class="redBg"';
            }

            $result .=  '<tr><td><table class="products"><tr><th>Anzahl</th><th>Auf Lager</th><th>Versendet</th><th>Foto</th><th>Artikel</th>'
                    . '<th>Artikelnummer</th></tr>'.$detailsParts[0].'</table>'.
                '</td><td>'.$order['messages'].'</td>'.
                '<td '.$customerColumnClass.'>'.
                $order['firstname'].' '.$order['lastname'].'<br>'.$order['address1'].' '.$order['address2'].'<br> '.$order['postcode'].' '.$order['city'].
                '<br> '.$order['country_name'].(!empty($order['paid_date'])?'<br><br>Paid: '.strftime('%c', strtotime($order['paid_date'])):'').'</td></tr>';
            
            
            if (count($detailsParts)>1)
            {
                for($partsI=1; $partsI<count($detailsParts); $partsI++)
                {
                    $result .= '<tr><td><table class="products"><tr><th>Anzahl</th><th>Auf Lager</th><th>Versendet</th><th>Foto</th><th>Artikel</th>'
                    . '<th>Artikelnummer</th></tr>'.$detailsParts[$partsI].'</table>'.
                '</td><td></td><td></td></tr>';
                }
            }
            $result .=  '</table>';
        }

        // html is complete
        $result .=  '</body></html>';
        //echo $result;
        //exit;
        // generate pdf
        $pdfRenderer = new PDFGenerator((bool) Configuration::get('PS_PDF_USE_CACHE'), '','A4');
        $pdfRenderer->mpdf->use_kwt = true;
        $pdfRenderer->createContent($result);
        $pdfRenderer->writePage();
        $pdfRenderer->render('shipping_info.pdf', 'D');
    }
   
    /**
     * @returns information about orders products as html string
     */
    static function &getProductsInfo($orderId)
    {
        $db = Db::getInstance();
        $sql = 'select distinct(id_order_detail), od.product_name, od.product_quantity, od.product_price, od.product_id, od.shipped, i.id_image, p.location,'.
        'ps.product_supplier_reference, od.in_stock as quantity_in_stock, pa.location as attribute_location, od.product_attribute_id from '.
            _DB_PREFIX_.'order_detail od left join '._DB_PREFIX_.'product p on od.product_id = p.id_product left join '.
            _DB_PREFIX_.'product_attribute pa on od.product_attribute_id=pa.id_product_attribute '.
            'left join '._DB_PREFIX_.'image i on (i.`id_product` = od.product_id AND i.`cover`= 1) '.
            'left join '._DB_PREFIX_.'product_supplier ps on ps.id_product = od.product_id AND ps.id_product_attribute=od.product_attribute_id '.
            ' where od.id_order='.$orderId.' and product_quantity>product_quantity_return+product_quantity_refunded and od.shipped=0';
        /* al.name as attribute_value, agl.public_name as attribute_name
           _DB_PREFIX_.'product_attribute_combination pac on(od.product_attribute_id = pac.id_product_attribute) '.
            'left join '._DB_PREFIX_.'attribute a on (pac.id_attribute = a.id_attribute) '.
            'left join '._DB_PREFIX_.'attribute_lang al on(pac.id_attribute=al.id_attribute and al.id_lang=3) '.
            'left join '._DB_PREFIX_.'attribute_group_lang agl on (agl.id_lang=3 and a.id_attribute_group=agl.id_attribute_group) '.
         */
        //echo $sql;
        $products = $db->s($sql);

        return $products;
    }


    static function calculateOrderWeight(&$order)
    {
        // express go first
        if (in_array($order['id_carrier'], ShippingExporter::$dhlExpressCarrierIds))
        {
            return 100;
        }

        // not germany orders second
        if ($order['id_country']!=ShippingExporter::GermanyCountryId)
        {
            return 90;
        }

        // dhl orders third
        if (self::isDhlOrder($order))
        {
            return 80;
        }

        return 0;
    }
    
    /**
     * Sorts orders array, makes DHL express orders go firt, makes germany
     * orders go first.
     */
    static function sortOrders(&$orders)
    {
        usort($orders, function($order1, $order2){

                // calculate orders weights and compare order by them
                $weight1 = ShippingExporter::calculateOrderWeight($order1);
                $weight2 = ShippingExporter::calculateOrderWeight($order2);
                if ($weight1==$weight2)
                {
                    if($order1['id_order']<$order2['id_order'])
                    {
                        return -1;
                    }
                    else
                    {
                        return 1;
                    }
                }
                elseif ($weight1 > $weight2)
                {
                    return -1;
                }
                else
                {
                    return 1;
                }
              });
    }


    /**
     * Prepares field to be palced in csv row. Adds quotes to it
     */
    static function encodeField($field)
    {
        $field = str_replace(array("\r", "\n"), array('\r', '\n'), $field);
        return $field;
    }


    static function isEUZoneId($zoneId)
    {
        return $zoneId == self::DEZoneId || $zoneId == self::EUZoneId;
    }
}

