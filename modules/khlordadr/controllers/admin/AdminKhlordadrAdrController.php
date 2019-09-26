<?php

class AdminKhlordadrAdrController extends ModuleAdminController
{
    public $auth = true;
    
    public $bootstrap = true;
    
    public function init()
    {
        parent::init();
        $action = Tools::getValue('action', 'default');
        switch($action){
            case 'locations':
                $this->getLocations();
                break;
            case 'customer_info':
                $this->showCustomerData();
                break;
            default:
                $this->getIndex();
                break;
        }
    }
    
    public function getIndex()
    {
        $this->context->smarty->assign(array(
            'module_adr_url' => $this->context->link->getAdminLink('AdminKhlordadrAdr'),
            //'module_url' => $this->_path,
            //'map_url' => $this->context->link->getModuleLink($this->name, 'map')
        ));
        
        //return $this->setTemplate('finder.tpl');
        $this->content = $this->context->smarty->fetch($this->module->getTemplatePath('views/templates/admin/finder.tpl'));
    }
    
    public function getLocations()
    {
        $this->ajax = true;
        
        $radius = (int)Tools::getValue('radius');
        $start = Tools::getValue('start');
        $customerActive = (int)Tools::getValue('customer_active', 0);
         
        if(empty($radius) || empty($start)){
            echo 'Required parameters not set';
            return;
        }
        
        if($this->context->shop->domain != 'nsweb.server'){
            $start .= ', Deutschland';
        }
         
        $geoRequestUrl = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyDKouTLt8-gQrPWn47VusoDkzuTjJX_p2M&address='. urlencode($start);
         
        $geoResponseJson = Tools::file_get_contents($geoRequestUrl);
        $geoResponse = json_decode($geoResponseJson, true);
        //var_dump($geoResponse);die;
        if($geoResponse['status'] != 'OK'){
            echo 'Start address not geocoded';
            return;
        }
        $startLatitude = $geoResponse['results'][0]['geometry']['location']['lat'];
        $startLongitude = $geoResponse['results'][0]['geometry']['location']['lng'];
        //$radiusMeters = $radius * 1;
         
        $addressesQuery = '
            SELECT a.*, c.active,
            (6371*(ACOS(SIN(RADIANS(`a`.`latitude`))*SIN(RADIANS('. $startLatitude .'))
                +COS(RADIANS(`a`.`latitude`))*COS(RADIANS('. $startLatitude .'))
                *COS(RADIANS(`a`.`longitude` - '. $startLongitude .'))))) AS distance
            FROM `'._DB_PREFIX_.'address` a
            INNER JOIN `'._DB_PREFIX_.'customer` c ON c.id_customer = a.id_customer
            WHERE a.id_customer > 0
                '. ($customerActive ? ' AND c.active = 1' : '') .'
            HAVING distance <= '. $radius .'
            ORDER BY distance ASC
        ';
         
        $addresses = Db::getInstance()->executeS($addressesQuery);
        //var_dump($addresses);
         
        if(!count($addresses)){
            echo 'No addresses found';
            return;
        }
         
        $customersFound = array();
        $output = '<ul class="list-group">';
        $addressesOut = array();
        foreach($addresses as $addressData){
            // skip customers that are already listed
            if( in_array($addressData['id_customer'], $customersFound) ){
                continue;
            }
            $customersFound[] = $addressData['id_customer'];
            
            $customerUrl = Context::getContext()->link->getAdminLink('AdminCustomers')
                . '&viewcustomer&id_customer='. $addressData['id_customer'];
            $address = new Address($addressData['id_address'], Context::getContext()->language->id);
             
            $addressFields = AddressFormat::getOrderedAddressFields($address->id_country);
            $addressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($address, $addressFields);
            $addressText =
                round($addressData['distance']) .' km, '
                . (!empty($addressFormatedValues['company']) ? $addressFormatedValues['company'] .', ' : '') .''
                . $addressFormatedValues['address1'] .', '
                . $addressFormatedValues['postcode'] .', '
                . $addressFormatedValues['city'] .' '
                . (!empty($addressFormatedValues['State:name']) ? $addressFormatedValues['State:name'] .', ' : '')
                . $addressFormatedValues['Country:name'] .' '
            ;
            $addressText .= ', '. $this->l('Customer') .': '. ($addressData['active'] ? $this->l('Active') : $this->l('Disabled'));
        
            $output .= '<li class="list-group-item" id="address_id_'.$addressData['id_address'].'">'.
                '<a class="btn btn-default address_more" data-address_id="'.$addressData['id_address'].'" href="#"><strong>&oplus;</strong></a> &nbsp;'.
                '<a class="btn btn-link" href="'.$customerUrl.'" target="_blank">'. $addressText .'</a>'
                .'</li>'
            ;
        }
        $output .= '</ul>';
        echo $output;
        
    }

    public function showCustomerData()
    {
        $this->ajax = true;
        $id_address = (int)Tools::getValue('id_address');
        
        $address = new Address($id_address, Context::getContext()->language->id);
        
        $customerOrdersNb = Order::getCustomerNbOrders($address->id_customer);
        
        $query = new DbQuery();
        $query->select('SUM(o.total_paid_tax_excl)');
        $query->from('orders', 'o');
        $query->where('o.id_customer = '.(int)$address->id_customer);
        $query->where('o.valid = 1');
        $ordersTotalTE = (float)Db::getInstance()->getValue($query->build());
        
        $lastOrder = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
    		SELECT o.*
    		FROM `'._DB_PREFIX_.'orders` o
    		WHERE o.`id_customer` = '.(int)$address->id_customer.'
    		ORDER BY o.`date_add` DESC
        ');
        
        
        echo $this->l('Orders number') .': '. $customerOrdersNb .'. '
            . $this->l('Orders total w/t') .': '. Tools::displayPrice($ordersTotalTE) .'. '
            . $this->l('Last order') .': '. Tools::displayDate($lastOrder['date_add']) .'. '
        ;
    }
    
    public function getSearch()
    {
        $id_product = (int)Tools::getValue('id_product');
        $radius = (int)Tools::getValue('radius');
        $start = Tools::getValue('start');
        
        if(empty($id_product) || empty($radius) || empty($start)){
            $this->context->smarty->assign(array(
                'error' => 'Required parameters not set'
            ));
            return $this->setTemplate('map.tpl');
        }
        
        $geoRequestUrl = 'https://maps.googleapis.com/maps/api/geocode/json?address='. urlencode($start);
        
        $geoResponseJson = Tools::file_get_contents($geoRequestUrl);
        $geoResponse = json_decode($geoResponseJson, true);
        //var_dump($geoResponse);die;
        if($geoResponse['status'] != 'OK'){
            $this->context->smarty->assign(array(
                'error' => 'Start address not geocoded'
            ));
            return $this->setTemplate('map.tpl');
        }
        $startLatitude = $geoResponse['results'][0]['geometry']['location']['lat'];
        $startLongitude = $geoResponse['results'][0]['geometry']['location']['lng'];
        //$radiusMeters = $radius * 1;

        $addressesQuery = '
            SELECT a.*, 
            (6371*(ACOS(SIN(RADIANS(`a`.`latitude`))*SIN(RADIANS('. $startLatitude .'))
                +COS(RADIANS(`a`.`latitude`))*COS(RADIANS('. $startLatitude .'))
                *COS(RADIANS(`a`.`longitude` - '. $startLongitude .'))))) AS distance
            FROM `'._DB_PREFIX_.'address` a
            INNER JOIN `'._DB_PREFIX_.'orders` o ON o.id_address_delivery = a.id_address
            INNER JOIN `'._DB_PREFIX_.'order_detail` od ON od.id_order = o.id_order
            WHERE od.product_id = '. (int)$id_product .'
                AND o.valid = 1
            GROUP BY a.id_address
            HAVING distance <= '. $radius .'
        ';
        
        $addresses = Db::getInstance()->executeS($addressesQuery);
        //var_dump($addresses);
        
        if(!count($addresses)){
            $this->context->smarty->assign(array(
                'error' => 'No addresses found'
            ));
            return $this->setTemplate('map.tpl');
        }

        $addressesOut = array();
        foreach($addresses as $address){
            $addressesOut[] = array(
                'latitude' => $address['latitude'],
                'longitude' => $address['longitude'],
            );
        }
        $this->context->smarty->assign(array(
            'addresses' => json_encode($addressesOut),
            'start' => json_encode(array('lat' => $startLatitude, 'lng' => $startLongitude))
        ));

        
        return $this->setTemplate('map.tpl');
    }
    
}
