<?php
define('SCRIPT_FOLDER', realpath(dirname(__FILE__)));
require SCRIPT_FOLDER . '/../../config/config.inc.php';
Dispatcher::getInstance()->dispatch();
set_time_limit(300);

switch($_GET['action']){
		
	case 'search':
		$query = Tools::getValue('q', false);
		//$context = Context::getContext();
		
		$sql = '
		SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, MAX(image_shop.`id_image`) id_image,
			s.name AS supplier_name, p.price
		FROM `'._DB_PREFIX_.'product` p
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = '.(int)Context::getContext()->language->id.Shop::addSqlRestrictionOnLang('pl').')
		LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
		LEFT JOIN `'._DB_PREFIX_.'supplier` s ON p.id_supplier = s.id_supplier
		WHERE (pl.name LIKE \'%'.pSQL($query).'%\' OR p.reference LIKE \'%'.pSQL($query).'%\' OR p.supplier_reference LIKE \'%'.pSQL($query).'%\')'.
				' GROUP BY p.id_product';
		
		$items = Db::getInstance()->executeS($sql);
		//
		$results = array();
		foreach ($items AS $item){
			$results[] = array(
				'id' => (int)($item['id_product']),
				'name' => $item['name'],
				'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
				//'image' => str_replace('http://', Tools::getShopProtocol(), Context::getContext()->link->getImageLink($item['link_rewrite'], $item['id_image'], 'home_default')),
				//'price' => Product::getPriceStatic($item['id_product'], true, null, 2, null, false, true, 1, true, null, null, null, null, true, true, $context),
				//'price' => Product::priceCalculation($context->shop->id, $item['id_product'], null, null, null, null, 1, 1, 1, true, 2, false, false, false, false, false, null, false, null, null),
				'price' => number_format($item['price'] + ($item['price'] / 100 * 19), 2),
				'supplier_name' => $item['supplier_name']
			);
		}
		
		echo Tools::jsonEncode($results);
		break;
	case 'find_locations':
	    $query = '
	        SELECT id_address 
	        FROM `'._DB_PREFIX_.'address` 
	        WHERE id_customer > 0 AND (ISNULL(latitude) OR ISNULL(longitude))
	            AND id_address NOT IN(5,6,8,9,21,318,10,53,60,63,64,71,73,75,77,81,90,31,100,115,130,145,159,160,169,180)
	        LIMIT 50
	    ';
	    
	    foreach( Db::getInstance()->executeS($query) as $addrData ){
	        
	        $address = new Address($addrData['id_address'], Context::getContext()->language->id);
	        if( !empty($address->latitude) && !empty($address->longitude) ){
	            continue;
	        }

	        $addressFields = AddressFormat::getOrderedAddressFields($address->id_country);
	        $addressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($address, $addressFields);
	        $addressText =
                $addressFormatedValues['address1'] .', '
	            . $addressFormatedValues['city'] .' '
                . (!empty($addressFormatedValues['State:name']) ? $addressFormatedValues['State:name'] .', ' : '')
                . $addressFormatedValues['postcode'] .', '
                . $addressFormatedValues['Country:name'] .' '
            ;
	        echo $address->id .' - '. $addressText.'<br>';
            $geoRequestUrl = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyDKouTLt8-gQrPWn47VusoDkzuTjJX_p2M&address='. urlencode($addressText);

            $geoResponseJson = Tools::file_get_contents($geoRequestUrl);
            $geoResponse = json_decode($geoResponseJson, true);
            //var_dump($geoResponse);die;
            if($geoResponse['status'] != 'OK'){
                echo 'Status: '. $geoResponse['status'] .'. Address not geocoded: '. $addressText.'<br>';
                continue;
            }

            $updateData = array(
                'latitude' => $geoResponse['results'][0]['geometry']['location']['lat'],
                'longitude' => $geoResponse['results'][0]['geometry']['location']['lng']
            );

            try{
                Db::getInstance()->update('address', $updateData, '`id_address` = '. $address->id);
            }
            catch(Exception $e){
                return $e->getMessage();
            }
	        
            echo 'Geocoded: '. $addressText.'<br>';
            sleep(2);
	    }
	    break;
	/*case 'products_locations':
	    //$id_product = (int)Tools::getValue('id_product');
	    $radius = (int)Tools::getValue('radius');
	    $start = Tools::getValue('start');
	    
	    if(empty($radius) || empty($start)){
	        echo 'Required parameters not set';
	        break;
	    }
	    
	    $geoRequestUrl = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyDKouTLt8-gQrPWn47VusoDkzuTjJX_p2M&address='. urlencode($start);
	    
	    $geoResponseJson = Tools::file_get_contents($geoRequestUrl);
	    $geoResponse = json_decode($geoResponseJson, true);
	    //var_dump($geoResponse);die;
	    if($geoResponse['status'] != 'OK'){
	        echo 'Start address not geocoded';
	        break;
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
            WHERE a.id_customer > 0
            HAVING distance <= '. $radius .'
        ';
	    
	    $addresses = Db::getInstance()->executeS($addressesQuery);
	    //var_dump($addresses);
	    
	    if(!count($addresses)){
	        echo 'No addresses found';
	        break;
	    }
	    
	    $output = '<ul class="list-group">';
	    $addressesOut = array();
	    foreach($addresses as $address){
	        $customerUrl = Context::getContext()->link->getAdminLink('AdminCustomers')
	           . '&viewcustomer&id_customer='. $address['id_customer'];
	        $address = new Address($address['id_address'], Context::getContext()->language->id);
	        
	        $addressFields = AddressFormat::getOrderedAddressFields($address->id_country);
	        $addressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($address, $addressFields);
	        $addressText =
	           $addressFormatedValues['company'] .', '
	           . $addressFormatedValues['address1'] .', '
	           . $addressFormatedValues['city'] .' '
	           . (!empty($addressFormatedValues['State:name']) ? $addressFormatedValues['State:name'] .', ' : '')
	           . $addressFormatedValues['postcode'] .', '
	           . $addressFormatedValues['Country:name'] .' '
	        ;
	                         
	        $output .= '<li class="list-group-item">'.
	   	        '<i >&oplus;</i>'.
	   	        '<a href="'.$customerUrl.'" target="_blank">'. $addressText .'</a>'
                .'</li>'
            ;
	    }
	    $output .= '</ul>';
	    echo $output;
	    break;*/
}

