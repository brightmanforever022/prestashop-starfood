<?php

if (!defined('_PS_VERSION_'))
    exit;

class Starfood extends Module
{
    const BULK_GROUPS_COUNT = 3;
    
    protected $config_keys;
    
    public $bootstrap = true;
    
    protected $currencies;
    
    protected function getCurrencies($id_currency = null)
    {
        if( empty($this->currencies) ){
            $this->currencies = Currency::getCurrencies(true, false);
        }
        
        if( !is_null($id_currency) ){
            foreach( $this->currencies as $currency ){
                if($currency->id == $id_currency){
                    return $currency;
                }
            }
        }
        
        return $this->currencies;
    }
    
    public function __construct()
    {
        $this->name = 'starfood';
        $this->tab = 'administration';
        $this->version = '0.0.1';
        //$this->author = '';
        $this->need_instance = 0;
        
        parent::__construct();
        
        $this->displayName = $this->l('General StarFoodImpex shop functions');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');
        
        $this->config_keys = array(
            'SFI_FEATURE_COUNTRY', 'SFI_FEATURE_UNIT', 'SFI_FEATURE_PACKAGE', 'SFI_FEATURE_UNITS_BOX',
            'SFI_MAJOR_CUST_GRP_ID'
        );
    }
    
    public function hookActionProductListModifier($params)
    {
        
        if( !count($params['cat_products']) ){
            return;
        }
        
        $countryFeatureId = intval( Configuration::get('SFI_FEATURE_COUNTRY') );
        
        $englishLanguageId = LanguageCore::getIdByIso('en');
        
        foreach( $params['cat_products'] as &$productData ){
            
            $product = new Product( intval( $productData['id_product'] ), false, $englishLanguageId );
            
            $countryValue = '';
            
            foreach($productData['features'] as $productFeature){
                if( $productFeature['id_feature'] == $countryFeatureId ){
                    $countryValue = $productFeature['value'];
                }
            }
            
            $productData['pieces_per_carton_text'] = $product->getPackageUnitInfo($this->context->language->id);
            $productData['country_of_origin'] = $countryValue;
            $productData['name_original'] = $product->name;
        }
        
    }
    
    public function hookDisplayAdminProductsExtra($params)
    {
        $id_product = (int) Tools::getValue('id_product');
        /*$product = new Product($id_product);
        for( $i = 1; $i <= self::BULK_GROUPS_COUNT; $i++ ){
            $marginFieldName = 'margin_bulk_'. $i;
            $quantityFieldName = 'quantity_bulk_'. $i;
            var_dump($product->{$marginFieldName}, $product->{$quantityFieldName});
        }*/
        if( $id_product ){
            $product = new Product($id_product);
            $this->context->smarty->assign(array(
                'product' => $product,
            ));
            
        }
        return $this->display(__FILE__, 'admin_products_extra.tpl');
    }
    
    public function hookActionObjectProductAddBefore($params)
    {
        $this->calculateRetailPrice($params['object']);
    }
    
    public function hookActionObjectProductAddAfter($params)
    {
        $this->calculateBulkPrices($params['object']);
    }

    public function hookActionObjectProductUpdateBefore($params)
    {
        $this->calculateRetailPrice($params['object']);
    }
    
    public function hookActionObjectProductUpdateAfter($params)
    {
        $this->calculateBulkPrices($params['object']);
    }
    
    private function calculateBulkPrices(&$product)
    {
        if( empty($product->wholesale_price) ){
            return;
        }
        
        $productSpecPrices = SpecificPrice::getByProductId($product->id);
        $majorCustomerGroupId = intval( Configuration::get('SFI_MAJOR_CUST_GRP_ID') );
        
        for( $i = 1; $i <= self::BULK_GROUPS_COUNT; $i++ ){
            $marginFieldName = 'margin_bulk_'. $i;
            $quantityFieldName = 'quantity_bulk_'. $i;
            if( empty($product->{$marginFieldName}) || empty($product->{$quantityFieldName}) ){
                continue;
            }
        
            $bulkPrice = $product->wholesale_price;
        
            $marginValue = $product->{$marginFieldName};
        
            $bulkPrice += $bulkPrice / 100 * $product->{$marginFieldName};
        
            $bulkPriceSpecificPriceId = null;
            if(is_array($productSpecPrices) && count($productSpecPrices)){
                foreach($productSpecPrices as $prodSpecificPrice){
                    if( intval($prodSpecificPrice['from_quantity']) == intval($product->{$quantityFieldName}) ){
                        $bulkPriceSpecificPriceId = intval($prodSpecificPrice['id_specific_price']);
                    }
                }
            }

            if( is_null($bulkPriceSpecificPriceId) ){
                $specificPrice = new SpecificPrice();
                $specificPrice->id_product = $product->id;
                $specificPrice->reduction_type = 'amount';
                $specificPrice->id_shop = 0;
                $specificPrice->id_shop_group = 0;
                $specificPrice->id_currency = 0;
                $specificPrice->id_country = 0;
                $specificPrice->id_customer = 0;
                $specificPrice->id_group = $majorCustomerGroupId;
                $specificPrice->id_product_attribute = 0;
                $specificPrice->reduction = 0;
                $specificPrice->reduction_tax = 0;
                $specificPrice->from = '0000-00-00';
                $specificPrice->to = '0000-00-00';
                $specificPrice->comment = $marginValue .'%';
            }
            else{
                $specificPrice = new SpecificPrice($bulkPriceSpecificPriceId);
            }
        
            $specificPrice->from_quantity = $product->{$quantityFieldName};
            $specificPrice->price = Tools::ceilf($bulkPrice, 6);
        
            try{
                $specificPrice->save();
            }
            catch(Exception $e){
                PrestaShopLoggerCore::addLog($e->getMessage());
            }
        }

    }
    
    /**
     * 
     * @param Product $product
     */
    private function calculateRetailPrice(&$product)
    {
        $productSupplyPrice = floatval($product->supply_price);
        if( ($productSupplyPrice > 0) && !empty($product->supply_currency_id) ){
            $defaultCurrencyId = intval(Configuration::get('PS_CURRENCY_DEFAULT'));
            
            if( $product->supply_currency_id != $defaultCurrencyId ){
                $product->wholesale_price = Tools::ceilf( Tools::convertPriceFull(
                    $productSupplyPrice,
                    $this->getCurrencies($product->supply_currency_id),
                    $this->getCurrencies($defaultCurrencyId)
                    ), 6);
            }
            else{
                $product->wholesale_price = $productSupplyPrice;
            }

            if( !empty($product->customs_tax) ){
                $product->wholesale_price += $product->wholesale_price / 100 * $product->customs_tax;
            }
            
            if( !empty($product->supply_cost) ){
                $product->wholesale_price += $product->wholesale_price / 100 * $product->supply_cost;
            }
        }
        
        if( empty($product->wholesale_price) ){
            return;
        }
        
        $retailPrice = $product->wholesale_price;
        
        if( !empty($product->margin_retail) ){
            $retailPrice += $retailPrice / 100 * $product->margin_retail;
        }
        
        $retailPrice = Tools::ceilf($retailPrice, 6);

        $address = Address::initialize(null);
        $address->id_country = intval(ConfigurationCore::get('PS_COUNTRY_DEFAULT'));
        $tax_manager = TaxManagerFactory::getManager($address, $product->id_tax_rules_group);
        $taxesRate = $tax_manager->getTaxCalculator()->getTotalRate();

        $retailPriceWithTaxes = $retailPrice + $tax_manager->getTaxCalculator()->getTaxesTotalAmount($retailPrice);
        $retailPriceWithTaxes = Tools::ps_round($retailPriceWithTaxes, 1, PS_ROUND_HALF_UP);

        // retail price without taxes where taxed price is rounded
        $retailPrice = $retailPriceWithTaxes - ($retailPriceWithTaxes * $taxesRate / (100 + $taxesRate));

        $product->price = $retailPrice;
    }
    
    public function getModuleDir()
    {
        return $this->local_path;
    }
    
    public function getContent()
    {
        /*
        $tab = new Tab();
        $tab->class_name = 'AdminStarfoodProducts';
        $tab->module = $this->name;
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminCatalog');
        foreach( Language::getLanguages() as $lang )
            $tab->name[(int) $lang['id_lang']] = 'Products quick edit';
        $tab->save();
        */
        //return 'OK';
        $html = '';
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
            foreach($this->config_keys as $key){
                Configuration::updateValue($key, Tools::getValue($key));
            }
            
            $html .= $this->displayConfirmation($this->l('Settings updated'));
        }
        
        return $html.$this->renderForm();
    }
    
    public function renderForm()
    {
        $features = Feature::getFeatures($this->context->language->id);
        array_unshift($features, array('id_feature' => 0, 'name' => 'Select'));
        //var_dump($features);
        
        $fields_form = array();
        
        $fields_form[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Feature fields'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Feature for Country'),
                        'name' => 'SFI_FEATURE_COUNTRY',
                        'options' => array(
                            'query' => $features,
                            'id' => 'id_feature',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Feature for Units'),
                        'name' => 'SFI_FEATURE_UNIT',
                        'options' => array(
                            'query' => $features,
                            'id' => 'id_feature',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Feature for Package'),
                        'name' => 'SFI_FEATURE_PACKAGE',
                        'options' => array(
                            'query' => $features,
                            'id' => 'id_feature',
                            'name' => 'name'
                        ),
                        
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Feature for Units per box'),
                        'name' => 'SFI_FEATURE_UNITS_BOX',
                        'options' => array(
                            'query' => $features,
                            'id' => 'id_feature',
                            'name' => 'name'
                        ),
                        
                    ),
                    /*array(
                        'type' => 'text',
                        'label' => $this->l('Do not display events older than'),
                        'name' => 'PS_PTOOLTIP_DAYS',
                        'suffix' => $this->l('days')
                    ),*/
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        
        $customerGroups = GroupCore::getGroups($this->context->language->id);
        $fields_form[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Prices'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Major customers group'),
                        'name' => 'SFI_MAJOR_CUST_GRP_ID',
                        'options' => array(
                            'query' => $customerGroups,
                            'id' => 'id_group',
                            'name' => 'name'
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        
    
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
    
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'SubmitStarfood';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
    
        return $helper->generateForm($fields_form);
    }
    
    public function getConfigFieldsValues()
    {
        $config = array();
        foreach( $this->config_keys as $key ){
            $config[ $key ] = Tools::getValue($key, Configuration::get($key));
        }
        
        return $config;
    }
    
    
    public function install()
    {
        if( ! parent::install() ){
            return false;
        }
        
        $checkHooks = array(
            'displayAdminProductsExtra',
            'actionObjectProductAddBefore',
            'actionObjectProductAddAfter',
            
        );
        foreach($checkHooks as $hookName){
            $hookData = Db::getInstance()->ExecuteS('
            	SELECT * FROM `' . _DB_PREFIX_ . 'hook` WHERE `name` = "'. $hookName .'"
            ');
            if( empty($hookData) ){
                $hook = new Hook();
                $hook->name = $hookName;
                $hook->title = $hookName;
                $hook->position = 1;
                $hook->add();
            }
            if(!$this->registerHook($hookName)){
                return false;
            }
        }
        
        return true;
    }
}

/*
ALTER TABLE `prs_product` 
ADD `box_code` VARCHAR(16) NOT NULL AFTER `pack_stock_type`, 
ADD `supply_currency` CHAR(3) NOT NULL AFTER `box_code`, 
ADD `supply_cost` INT NOT NULL AFTER `supply_currency`, 
ADD `customs_tax` INT NOT NULL AFTER `supply_cost`, 
ADD `profit_margin` INT NOT NULL AFTER `customs_tax`, 
ADD `margin_retail` INT NOT NULL AFTER `profit_margin`, 
ADD `margin_bulk_1` INT NOT NULL AFTER `margin_retail`, 
ADD `margin_bulk_2` INT NOT NULL AFTER `margin_bulk_1`, 
ADD `margin_bulk_3` INT NOT NULL AFTER `margin_bulk_2`
ADD `quantity_bulk_1` INT NOT NULL AFTER `margin_bulk_3`, 
ADD `quantity_bulk_2` INT NOT NULL AFTER `quantity_bulk_1`, 
ADD `quantity_bulk_3` INT NOT NULL AFTER `quantity_bulk_2`
;
ALTER TABLE `prs_specific_price` ADD `comment` VARCHAR(64) NOT NULL AFTER `to`;

ALTER TABLE `prs_product` 
ADD `country` VARCHAR(32) NOT NULL AFTER `quantity_bulk_3`, 
ADD `unit` VARCHAR(16) NOT NULL AFTER `country`, 
ADD `package` VARCHAR(16) NOT NULL AFTER `unit`, 
ADD `units_per_package` INT NOT NULL AFTER `package`;

ALTER TABLE `prs_product` CHANGE `supply_currency` `supply_currency_id` INT NOT NULL;
ALTER TABLE `prs_product` ADD `supply_price` DECIMAL(20,2) NOT NULL AFTER `box_code`;

 */