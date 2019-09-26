<?php

define('SCRIPT_FOLDER', realpath(dirname(__FILE__)));
require SCRIPT_FOLDER . '/../../config/config.inc.php';

//$s = 'Nomi´s Pizza Lieferservice';
//echo sf_customers_import_filter_name($s);die;
//var_dump(Validate::isAddress($s));
//die;

$csvFile = 'customers.csv';

$fh = fopen('./'.$csvFile, 'r');
$customersData = array();

while( $csvData = fgetcsv($fh, 10000, ';', '"') ){
    $impData = array(
        'client_number' => $csvData[0],
        'customer_id' => $csvData[1],
        'gender' => $csvData[2],
        'lastname' => $csvData[3],
        'firstname' => $csvData[4],
        'company' => $csvData[5],
        'address' => $csvData[7] .' '. $csvData[8],
        'postcode' => $csvData[9],
        'city' => $csvData[10],
        'country' => $csvData[11],
        'phone_land' => $csvData[12],
        'phone_mob' => $csvData[13],
        'phone_fax' => $csvData[14],
        'email' => $csvData[15],
        'website' => $csvData[16],
        'vat_id' => $csvData[17],
    );
    
    foreach($impData as &$value){
        $value = str_replace('`', "'", $value);
        $value = str_replace('´', "'", $value);
        //$value = mb_convert_encoding(trim($value), 'UTF-8', 'ISO-8859-1');
    }
    unset($value);
    
    $customersData[] = $impData;
}

sf_customers_import($customersData);

function sf_customers_import($dataList)
{
    $languageId = LanguageCore::getIdByIso('DE');
    
    foreach( $dataList as $i => $data ){
        if($i == 0)continue;
        
        $data['company'] = str_replace('`', "'", $data['company']);
        
        if( empty($data['lastname']) ){
            $data['lastname'] = sf_customers_import_filter_name( $data['company'] );
        }
        if( empty($data['firstname']) && !empty($data['lastname']) ){
            $data['firstname'] = $data['lastname'];
        }
        else{
            $data['firstname'] = sf_customers_import_filter_name($data['firstname']);
        }
        //if( empty($data['firstname']) && $data['lastname'] ){
        //    $data['firstname'] = $data['client_number'];
        //    $data['lastname'] = $data['client_number'];
        //}
        
        $data['phone_land'] = sf_customers_import_filter_phone($data['phone_land']);
        $data['phone_mob'] = sf_customers_import_filter_phone($data['phone_mob']);
        $data['phone_fax'] = sf_customers_import_filter_phone($data['phone_fax']);
        
        
        
        if( empty($data['email']) || !Validate::isEmail($data['email']) ){
            $data['email'] = "no_email_{$data['client_number']}@starfoodimpex.de";
        }
        
        //var_dump($dataList);continue;
        $customer = new Customer();
        $customer->force_id = true;
        $customer->id = $data['customer_id'];
        //$customer->id_customer
        $customer->client_number = $data['client_number'];
        $customer->id_gender = ($data['gender'] == 'Herr' ? 1 : 2);
        $customer->firstname = $data['firstname'];
        $customer->lastname = $data['lastname'];
        $customer->company = $data['company'];
        $customer->phone = $data['phone_land'];
        $customer->phone_mobile = $data['phone_mob'];
        $customer->fax = $data['phone_fax'];
        $customer->email = $data['email'];
        $customer->passwd = md5($data['email']);
        $customer->website = $data['website'];
        $customer->siret = $data['vat_id'];
        
        $customerCountryId = CountryCore::getIdByName($languageId, $data['country']);
        if( empty($customerCountryId) ){
            echo 'Country not found: "'. $data['country'] .'"<br>';
            //$customerCountryId = Context::getContext()->country->id;
        }
        
        $customer->id_country = $customerCountryId;
        $customer->address1 = $data['address'];
        $customer->city = $data['city'];
        $customer->postcode = $data['postcode'];
        //continue;
        try{
            $customer->add();
        }
        catch(Exception $e){
            echo 'Customer ('.$data['client_number'].') not saved: '. $e->getMessage() .'<br>';
            var_dump($data);
        }
        
        /**
         * 
         * @var AddressCore $address
         */
        $address = new Address();
        $address->id_customer = $customer->id;
        $address->id_country = $customerCountryId;
        $address->address1 = $data['address'];
        $address->city = $data['city'];
        $address->postcode = $data['postcode'];
        
        $address->firstname = $data['firstname'];
        $address->lastname = $data['lastname'];
        $address->company = $data['company'];
        $address->phone = $data['phone_land'];
        $address->phone_mobile = $data['phone_mob'];
        $address->alias = 'Default';
        
        try{
            $address->add();
        }
        catch(Exception $e){
            echo 'Address ('.$data['client_number'].') not saved: '. $e->getMessage() .'<br>';
            var_dump($data);
        }
        
        if($i > 200){
            //return;
        }
    }
}

function sf_customers_import_filter_phone($str)
{
    $str = str_replace(array('/', ' '), array('-', ''), $str);
    $str = preg_replace('#[^\d\-\(\)\+\. ]#', '', $str);
    
    return Validate::isPhoneNumber($str) ? $str : '';
}
function sf_customers_import_filter_name($str)
{
    $str = preg_replace('#[0-9!<>,;?=+()@\#"°{}_$%:]#', '', $str);
    $str = substr( $str, 0, 32);
    return $str;
}