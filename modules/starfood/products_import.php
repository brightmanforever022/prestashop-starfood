<?php

define('SCRIPT_FOLDER', realpath(dirname(__FILE__)));
require SCRIPT_FOLDER . '/../../config/config.inc.php';

@ini_set('display_errors', 'on');
@ini_set('display_startup_errors', 'on');
@error_reporting(E_ALL | E_STRICT);

set_time_limit(60 * 120);
$csvFile = 'products.csv';

$fh = fopen($csvFile, 'r');

$csvProducts = array();

while( $csvData = fgetcsv($fh, 10000, ';', '"') ){
    $impData = array(
        'matchcode' => $csvData[0],
        'id' => $csvData[1],
        'category' => $csvData[2],
        'manufacturer' => $csvData[3],
        'name_en' => $csvData[4],
        'name_de' => $csvData[5],
        'country' => $csvData[6],
        'weight' => $csvData[7],
        'weight_unit' => $csvData[8],
        'package' => $csvData[9],
        'pcs_per_package' => $csvData[10],
        'margin_bulk_1' => $csvData[11],
        'margin_bulk_2' => $csvData[12],
        'margin_bulk_3' => $csvData[13],
        'tax_value' => $csvData[14],
        /*
        'box_code' => $csvData[21],
        'supply_currency' => $csvData[22],
        'profit_margin' => $csvData[23],
        'supply_cost' => $csvData[24],
        'customs_tax' => $csvData[25],
        'margin_retail' => $csvData[29],
        'margin_bulk_1' => $csvData[26],
        'margin_bulk_2' => $csvData[27],
        'margin_bulk_3' => $csvData[28],
        */
        //'quantity_bulk_1' => $csvData[],
    );
    
    
    foreach($impData as &$value){
        $value = str_replace('`', "'", $value);
        $value = str_replace('´', "'", $value);
        $value = trim($value);
        //$value = mb_convert_encoding(trim($value), 'UTF-8', 'ISO-8859-1');
    }
    unset($value);
    
    $csvProducts[] = $impData;
}

starfood_product_import($csvProducts);

function starfood_product_import($productsData)
{
    $languageId = LanguageCore::getIdByIso('DE');
    
    $tax_groups = array(
        7 => 3,
        19 => 58
    );
    if( Context::getContext()->shop->domain == 'starfoodimpex.de' ){
        $countriesFeatureId = 1;
        $unitFeatureId = 2;
        $pcsPerPackageFeatureId = 4;
        $packageFeatureId = 3;
        
    }
    else{
        $countriesFeatureId = 1;
        $unitFeatureId = 2;
        $pcsPerPackageFeatureId = 3;
        $packageFeatureId = 4;
        
    }
    
    foreach( $productsData as $pi => $productData ){
        if($pi == 0){
            continue;
        }
        //var_dump($productData);
        
        /*$productFound = Db::getInstance()->getRow('
            SELECT id_product
            FROM `'._DB_PREFIX_.'product`
            WHERE `upc` = "'.pSQL($productData['matchcode']).'"
        ');
        
        if( !empty($productFound['id_product']) ){
            continue;
        }*/
        
        
        $product = new Product();
        /*if( is_numeric($productData['id']) ){
            $csvProductId = intval($productData['id']);
            if( $csvProductId < 100000 ){
                $product->force_id = true;
                
            }
        }*/
        
        $product->reference = $productData['id'];
        //$categoryFound = Category::searchByName($languageId, $productData['category'], true);
        $categoryFound = Db::getInstance()->getRow('
            SELECT c.*, cl.*
            FROM `'._DB_PREFIX_.'category` c
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` '.Shop::addSqlRestrictionOnLang('cl').')
            WHERE `name` LIKE \''.pSQL($productData['category']).'\'
        ');
        if( is_array($categoryFound) && !empty($categoryFound['id_category']) ){
            $product->id_category_default = intval($categoryFound['id_category']);
            $product->id_category = intval($categoryFound['id_category']);
        }
        else{
            $category = new Category();
            $category->name = starfoodimport_make_multilang($productData['category']);
            $catLinkRewr = Tools::str2url($productData['category']);
            $category->link_rewrite = starfoodimport_make_multilang($catLinkRewr);
            $category->id_parent = Category::getRootCategory()->id_category;
            $category->save();
            
            $product->id_category_default = $category->id;
            $product->id_category = $category->id;
        }
        
        $product->upc = $productData['matchcode'];
        
        if( !empty($productData['manufacturer']) ){
            $product->id_manufacturer = starfoodimport_get_manufacturer_id($productData['manufacturer']);
        }
        
        if( empty($productData['name_de']) && !empty($productData['name_en']) ){
            $productData['name_de'] = $productData['name_en'];
        }
        
        foreach(LanguageCore::getLanguages() as $language){
            if( $language['iso_code'] == 'en' ){
                $product->name[ $language['id_lang'] ] = $productData['name_en'];
                $product->link_rewrite[ $language['id_lang'] ] = Tools::str2url($productData['name_en']);
            }
            if( $language['iso_code'] == 'de' ){
                $product->name[ $language['id_lang'] ] = $productData['name_de'];
                $product->link_rewrite[ $language['id_lang'] ] = Tools::str2url($productData['name_de']);
            }
        }
        
        $product->id_tax_rules_group = $tax_groups[ intval($productData['tax_value']) ];
        
        $product->active = 1;
        $product->available_for_order = 1;
        $product->show_price = 1;
        $product->online_only = 0;
        $product->out_of_stock = 0;
        
        $product->margin_bulk_1 = $productData['margin_bulk_1'];
        $product->margin_bulk_2 = $productData['margin_bulk_2'];
        $product->margin_bulk_3 = $productData['margin_bulk_3'];
        
        try{
            $product->add();
            echo 'Imported: '. $productData['id'] .' <br>';
        }
        catch(Exception $e){
            echo $productData['id'] .' : '. $e->getMessage();
            continue;
        }
        //var_dump($product);
        $product->updateCategories(array($product->id_category));

        if( !empty($productData['country']) ){
            starfoodimport_set_feature_value($product, $countriesFeatureId, $productData['country']);
        }
        
        if( !empty($productData['weight']) && !empty($productData['weight_unit']) ){
            $unitFeatureValueName = $productData['weight'] . $productData['weight_unit'];
            starfoodimport_set_feature_value($product, $unitFeatureId, $unitFeatureValueName);
        }
        if( !empty($productData['package']) ){
            starfoodimport_set_feature_value($product, $packageFeatureId, $productData['package']);
        }
        if( !empty($productData['pcs_per_package']) ){
            starfoodimport_set_feature_value($product, $pcsPerPackageFeatureId, $productData['pcs_per_package']);
        }
        unset($product);
        
        if($pi > 50){
            //return;
        }
    }
}
die;

function starfoodimport_set_feature_value(&$product, $featureId, $featureValueName)
{
    
    $featureValueId = Db::getInstance()->getRow('
        SELECT fv.id_feature_value
        FROM `'._DB_PREFIX_.'feature_value_lang` fvl
        INNER JOIN `'._DB_PREFIX_.'feature_value` fv ON fv.id_feature_value = fvl.id_feature_value
        WHERE fv.id_feature = '. $featureId .'
            AND fvl.value LIKE "'. pSQL($featureValueName) .'"
        
    ');
    //var_dump($featureId, $featureValueName, $featureValueId);
    if(!is_array($featureValueId) || empty($featureValueId['id_feature_value'])){
        $featureValue = new FeatureValueCore();
        $featureValue->id_feature = $featureId;
        $featureValue->custom = false;
        $featureValue->value = starfoodimport_make_multilang($featureValueName);
        $featureValue->add();
        //var_dump($featureValue);
        $product->addFeaturesToDB ( $featureId, $featureValue->id, false );
    }
    else{
        $product->addFeaturesToDB ( $featureId, $featureValueId['id_feature_value'], false );
    }
}



function starfoodimport_get_manufacturer_id($name)
{
    $result = Db::getInstance()->getRow('
			SELECT `id_manufacturer`
			FROM `'._DB_PREFIX_.'manufacturer`
			WHERE `name` LIKE \''.pSQL($name).'\''
        );
    if( empty($result['id_manufacturer']) ){
        $manufacturer = new ManufacturerCore();
        $manufacturer->name = $name;
        $manufacturer->active = 1;
        $manufacturer->link_rewrite = Tools::str2url($name);
        $manufacturer->add();
        return $manufacturer->id;
    }
    else{
        return (int)$result['id_manufacturer'];
    }
}

function starfoodimport_make_multilang($name){
    $names = array();
    foreach(LanguageCore::getLanguages() as $language){
        $names[ $language['id_lang'] ] = $name;
    }
    return $names;
}