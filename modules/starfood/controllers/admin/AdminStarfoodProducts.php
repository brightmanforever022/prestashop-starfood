<?php

require_once _PS_TOOL_DIR_ . 'php_excel/PHPExcel.php';

class AdminStarfoodProductsController extends ModuleAdminController
{
    public $auth = true;
    
    public $bootstrap = true;
    
    protected $_category;
    
    protected $id_current_category;
    
    protected $name_aliases = array();
    
    public function __construct()
    {
        $this->table = 'product';
        $this->className = 'Product';
        $this->lang = true;
        $this->explicitSelect = true;
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
        
        parent::__construct();
        
        $this->allow_export = true;
        $this->list_no_link = true;
        
        if (Tools::getValue('reset_filter_category')) {
            $this->context->cookie->id_category_products_filter = false;
        }
        
        /* Join categories table */
        if ($id_category = (int)Tools::getValue('productFilter_cl!name')) {
            $this->_category = new Category((int)$id_category);
            $_POST['productFilter_cl!name'] = $this->_category->name[$this->context->language->id];
        } else {
            if ($id_category = (int)Tools::getValue('id_category')) {
                $this->id_current_category = $id_category;
                $this->context->cookie->id_category_products_filter = $id_category;
            } elseif ($id_category = $this->context->cookie->id_category_products_filter) {
                $this->id_current_category = $id_category;
            }
            if( !empty($this->id_current_category) ) {
                $this->_category = new Category((int)$this->id_current_category);
            } else {
                $this->_category = new Category();
            }
        }
        
        $join_category = false;
        if (Validate::isLoadedObject($this->_category) && empty($this->_filter)) {
            $join_category = true;
        }
        
        $languagesOther = array();
        foreach( Language::getLanguages(true) as $language ){
            if( $language['id_lang'] != $this->context->language->id ){
                $languagesOther[] = $language;
            }
        }
        
        $this->_join .= '
            LEFT JOIN `'._DB_PREFIX_.'stock_available` sav ON (sav.`id_product` = a.`id_product` AND sav.`id_product_attribute` = 0
            '.StockAvailable::addSqlShopRestriction(null, null, 'sav').') ';
        
        $alias = 'sa';
        $alias_image = 'image_shop';
        
        $id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP? (int)$this->context->shop->id : 'a.id_shop_default';
        $this->_join .= ' 
            JOIN `'._DB_PREFIX_.'product_shop` sa ON (a.`id_product` = sa.`id_product` AND sa.id_shop = '.$id_shop.')
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON ('.$alias.'.`id_category_default` = cl.`id_category` AND b.`id_lang` = cl.`id_lang` AND cl.id_shop = '.$id_shop.')
			LEFT JOIN `'._DB_PREFIX_.'shop` shop ON (shop.id_shop = '.$id_shop.')
			LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (image_shop.`id_product` = a.`id_product` AND image_shop.`cover` = 1 AND image_shop.id_shop = '.$id_shop.')
			LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_image` = image_shop.`id_image`)
			LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = a.`id_manufacturer`)
			LEFT JOIN `'._DB_PREFIX_.'tax_rules_group` trg ON (trg.`id_tax_rules_group` = a.`id_tax_rules_group`)
            LEFT JOIN `'._DB_PREFIX_.'measure_unit` mu ON (mu.`id_measure_unit` = a.`id_measure_unit`)
        ';
        
        $this->_select .=
            'a.id_product AS id, 
            '. $alias_image.'.`id_image` AS `id_image`, 
            cl.`name` AS `name_category`, 
            '.$alias.'.`price`, 
            0 AS `price_final`, 0 AS `bulk_price`,
            a.`is_virtual`, 
            sav.`quantity` AS `sav_quantity`, 
            '.$alias.'.`active`, 
            IF(sav.`quantity`<=0, 1, 0) AS `badge_danger`,
            m.name AS manufacturer_name,
            trg.name AS tax_name,
            a.unit_value AS unit, mu.name AS measure_unit_name
        ';
        
        if ($join_category) {
            $this->_join .= ' INNER JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = a.`id_product` AND cp.`id_category` = '.(int)$this->_category->id.') ';
            $this->_select .= ' , cp.`position` ';
        }
        
        foreach( $languagesOther as $languageOther ){
            if( empty($languageOther['id_lang']) ){
                continue;
            }
            $langTblSqlAlias = 'pl_'. $languageOther['iso_code'];
            $this->name_aliases[] = $langTblSqlAlias;
            $this->_select .= ', '. $langTblSqlAlias.'.name AS product_name_'.$langTblSqlAlias;
            $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'product_lang` AS '. $langTblSqlAlias .'
                ON '. $langTblSqlAlias .'.id_product = a.id_product AND '.$langTblSqlAlias.'.id_lang = '. intval($languageOther['id_lang']);
        }

        $countryFeatureId = intval( Configuration::get('SFI_FEATURE_COUNTRY') );
        $unitFeatureId = intval( Configuration::get('SFI_FEATURE_UNIT') );
        $packageFeatureId = intval( Configuration::get('SFI_FEATURE_PACKAGE') );
        $unitsBoxFeatureId = intval( Configuration::get('SFI_FEATURE_UNITS_BOX') );
        
        if(!empty($countryFeatureId)){
            $this->_select .= ', fvl_cntr.value AS country';
            $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'feature_product` fp_cntr 
                ON fp_cntr.id_product = a.id_product AND fp_cntr.id_feature = '. $countryFeatureId
                . ' LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl_cntr 
                ON fvl_cntr.id_feature_value = fp_cntr.id_feature_value AND fvl_cntr.id_lang = '. intval($this->context->language->id)
            ;
            $productCountryOptions = FeatureValueCore::getFeatureValuesWithLang($this->context->language->id, $countryFeatureId);
            $productCountryFilterOptions = array();
            foreach( $productCountryOptions as $productCountryOption ){
                $productCountryFilterOptions[ $productCountryOption['id_feature_value'] ] = 
                    $productCountryOption['value'];
            }
        }
        /*if(!empty($unitFeatureId)){
            $this->_select .= ', fvl_unit.value AS unit';
            $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'feature_product` fp_unit
                ON fp_unit.id_product = a.id_product AND fp_unit.id_feature = '. $unitFeatureId
                .' LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl_unit
                ON fvl_unit.id_feature_value = fp_unit.id_feature_value AND fvl_unit.id_lang = '. intval($this->context->language->id)
            ;
        }*/
        if(!empty($packageFeatureId)){
            $this->_select .= ', fvl_pack.value AS package';
            $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'feature_product` fp_pack
                ON fp_pack.id_product = a.id_product AND fp_pack.id_feature = '. $packageFeatureId
                . ' LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl_pack
                ON fvl_pack.id_feature_value = fp_pack.id_feature_value AND fvl_pack.id_lang = '. intval($this->context->language->id)
            ;
        }
        if(!empty($unitsBoxFeatureId)){
            $this->_select .= ', fvl_unbx.value AS units_per_package';
            $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'feature_product` fp_unbx
                ON fp_unbx.id_product = a.id_product AND fp_unbx.id_feature = '. $unitsBoxFeatureId
                . ' LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl_unbx
                ON fvl_unbx.id_feature_value = fp_unbx.id_feature_value AND fvl_unbx.id_lang = '. intval($this->context->language->id)
            ;
        }
        
        
        $this->_use_found_rows = false;
        $this->_group = '';
        
        $this->fields_list = array();
        $this->fields_list['id_product'] = array(
            'title' => $this->l('ID'),
            'align' => 'center',
            'class' => 'fixed-width-xs fancybox',
            'type' => 'int'
        );
        $this->fields_list['id_image'] = array(
            'title' => $this->l('Image'),
            'align' => 'center',
            //'type' => 'image',
            'image' => 'p',
            'orderby' => false,
            'filter' => false,
            'search' => false,
            'class' => 'fixed-width-sm',
        );
        $this->fields_list['name'] = array(
            'title' => $this->l('Name'),
            'filter_key' => 'b!name',
            'callback' => 'showName'
        );
        $this->fields_list['manufacturer_name'] = array(
            'title' => $this->l('Manufacturer'),
            'filter_key' => 'm!name',
            //'align' => 'left',
        );
        $this->fields_list['weight'] = array(
            'title' => $this->l('Weight'),
            'align' => 'left',
            'callback' => 'showWeight'
        );
        $this->fields_list['unit'] = array(
            'title' => $this->l('Unit'),
            'align' => 'left',
            'orderby' => false,
            'search' => false,
            'callback' => 'listFormatUnit'
        );
        
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->fields_list['shopname'] = array(
                'title' => $this->l('Default shop'),
                'filter_key' => 'shop!name',
            );
        } else {
            $this->fields_list['name_category'] = array(
                'title' => $this->l('Category'),
                'filter_key' => 'cl!name',
            );
        }
        $this->fields_list['package'] = array(
            'title' => $this->l('Package'),
            'align' => 'left',
            'orderby' => false,
            'search' => false,
            
        );
        $this->fields_list['units_per_package'] = array(
            'title' => $this->l('U/pack'),
            'align' => 'left',
            'orderby' => false,
            'search' => false,
            
        );
        
        
        /*$this->fields_list['price'] = array(
            'title' => $this->l('Base price'),
            'type' => 'price',
            'align' => 'text-right',
            'filter_key' => 'a!price'
        );*/
        $this->fields_list['reference'] = array(
            'title' => $this->l('Artikelnummer'),
            'align' => 'left')       
        ;
        $this->fields_list['wholesale_price'] = array(
            'title' => $this->l('Buy price'),
            'type' => 'price',
            'align' => 'text-right',
            'filter_key' => 'a!wholesale_price',
            //'callback' => 'showWholesalePrice'
        );
        $this->fields_list['bulk_price'] = array(
            'title' => $this->l('Bulk price'),
            'type' => 'price',
            'align' => 'text-right',
            //'filter_key' => 'a!wholesale_price',
            'orderby' => false,
            'search' => false,
            
            'callback' => 'showBulkPrice'
        );
        
        $this->fields_list['price'] = array(
            'title' => $this->l('Retail price'),
            //'type' => 'price',
            'align' => 'text-right',
            'havingFilter' => true,
            'orderby' => false,
            'search' => false,
            'callback' => 'showFinalPrice'
        );
        
        $this->fields_list['tax_name'] = array(
            'title' => $this->l('Tax'),
            //'type' => 'price',
            'align' => 'text-right',
            //'filter_key' => 'a!price'
        );
        $this->fields_list['country'] = array(
            'title' => $this->l('Country'),
            'align' => 'left',
            'type' => 'select',
            'list' => $productCountryFilterOptions,
            'filter_key' => 'fp_cntr!id_feature_value',
            'filter_type' => 'int',
            
        );
        
        if (Configuration::get('PS_STOCK_MANAGEMENT')) {
            $this->fields_list['sav_quantity'] = array(
                'title' => $this->l('Quantity'),
                'type' => 'int',
                'align' => 'text-right',
                'filter_key' => 'sav!quantity',
                'orderby' => true,
                'badge_danger' => true,
                //'hint' => $this->l('This is the quantity available in the current shop/group.'),
            );
        }
        
        $this->fields_list['active'] = array(
            'title' => $this->l('Status'),
            'active' => 'status',
            'filter_key' => $alias.'!active',
            'align' => 'text-center',
            'type' => 'bool',
            'class' => 'fixed-width-xs',
            'orderby' => false
        );
        
        $this->fields_list['available_for_order'] = array(
            'title' => $this->l('Orderable'),
            'available_for_order' => 'status',
            'filter_key' => $alias.'!available_for_order',
            'align' => 'text-center',
            'type' => 'bool',
            'class' => 'fixed-width-xs',
            'orderby' => false
        );
        
        $this->addRowAction('delete');
    }
    
    public function listFormatUnit($value, $row)
    {
        return sprintf('%.3g', $value) .' '. $row['measure_unit_name'];
    }
    
    public function showWeight($value)
    {
        return number_format($value, 3);
    }

    public function initContent()
    {
        parent::initContent();
        
        
        if(empty($this->display) && !$this->ajax){
            $tplData = array(
                
            );
            
            if ($id_category = (int)$this->id_current_category) {
                self::$currentIndex .= '&id_category='.(int)$this->id_current_category;
            }
            
            // If products from all categories are displayed, we don't want to use sorting by position
            if (!$id_category) {
                $this->_defaultOrderBy = $this->identifier;
                if ($this->context->cookie->{$this->table.'Orderby'} == 'position') {
                    unset($this->context->cookie->{$this->table.'Orderby'});
                    unset($this->context->cookie->{$this->table.'Orderway'});
                }
            }
            if (!$id_category) {
                $id_category = Configuration::get('PS_ROOT_CATEGORY');
            }
            $tplData['is_category_filter'] = (bool)$this->id_current_category;
            
            $tree = new HelperTreeCategories('categories-tree', $this->l('Filter by category'));
            $tree->setAttribute('is_category_filter', (bool)$this->id_current_category)
            ->setAttribute('base_url', preg_replace('#&id_category=[0-9]*#', '', self::$currentIndex).'&token='.$this->token)
            ->setInputName('id-category')
            ->setRootCategory(Configuration::get('PS_ROOT_CATEGORY'))
            ->setSelectedCategories(array((int)$id_category))
            ->setTemplateDirectory( $this->module->getModuleDir().'views/templates/admin/products/helpers/tree' )
            ;
            $tplData['category_tree'] = $tree->render();
            
            // used to build the new url when changing category
            $tplData['base_url'] = preg_replace('#&id_category=[0-9]*#', '', self::$currentIndex).'&token='.$this->token;
            
            $tplData['currentToken'] = Tools::getAdminTokenLite('AdminStarfoodProducts');
            $this->context->smarty->assign($tplData);
            $this->content = 
                $this->context->smarty->fetch($this->module->getTemplatePath('views/templates/admin/products/list_top.tpl'))
                .$this->content 
            ;
            $this->content .= $this->context->smarty->fetch($this->module->getTemplatePath('views/templates/admin/products/popup.tpl'));
            $this->context->smarty->assign('content', $this->content);
            
        }
        
        switch(Tools::getValue('action')){
            case 'quick_edit':
                $this->actionQuickEdit();
                break;
            case 'updateQuick':
                
                $errors = array();
                $countryFeatureId = intval( Configuration::get('SFI_FEATURE_COUNTRY') );
                //$unitFeatureId = intval( Configuration::get('SFI_FEATURE_UNIT') );
                $packageFeatureId = intval( Configuration::get('SFI_FEATURE_PACKAGE') );
                $unitsBoxFeatureId = intval( Configuration::get('SFI_FEATURE_UNITS_BOX') );
                
                    $id_product = Tools::getValue('id_product');
                    $product = new Product($id_product);
                    $productFeatures = Product::getFeaturesStatic($product->id);
                    
                    foreach(LanguageCore::getLanguages(false) as $language){
                        //var_dump($language['id_lang'], Tools::getValue('name'), Tools::str2url( Tools::getValue('name') ));
                        $product->name[ $language['id_lang'] ] = Tools::getValue('name_'. $language['id_lang']);
                        $product->link_rewrite[ $language['id_lang'] ] = trim(Tools::str2url( Tools::getValue('name_'. $language['id_lang'])));
                    }
                    
                    $product->weight = floatval(Tools::getValue('weight'));
                    $product->id_manufacturer = intval(Tools::getValue('id_manufacturer'));
                    $product->supply_price = Tools::ceilf( floatval( Tools::getValue('supply_price') ), 6);
                    $product->supply_currency_id = Tools::getValue('supply_currency_id');
                    $product->customs_tax = Tools::getValue('customs_tax');
                    $product->supply_cost = Tools::getValue('supply_cost');
                    $product->price = Tools::ceilf( floatval(Tools::getValue('base_price')), 6);
                    $product->available_for_order = intval(Tools::getValue('available_for_order'));
                    $product->active = intval(Tools::getValue('active'));
                    $product->reference = Tools::getValue('reference');
                    $product->wholesale_price = Tools::ceilf( floatval(Tools::getValue('wholesale_price')), 6 );
                    $product->id_tax_rules_group = intval(Tools::getValue('id_tax_rules_group'));
                    $product->available_for_order = intval(Tools::getValue('available_for_order'));
                    
                    $product->id_measure_unit = intval(Tools::getValue('id_measure_unit'));
                    $product->unit_value = floatval(Tools::getValue('unit_value'));
                    $product->id_liquid_density = intval(Tools::getValue('id_liquid_density'));
                    $product->pieces_unit = intval(Tools::getValue('pieces_unit'));
                    $product->updateCategories(Tools::getValue('categoryBox'));
                    try{
                        $product->update();
                    }
                    catch(Exception $e){
                        $errors[] = $e->getMessage();
                    }
                    
                    try{
                        StockAvailableCore::setQuantity($product->id, 0, Tools::getValue('quantity'));
                    }
                    catch(Exception $e){
                        $errors[] = $e->getMessage();
                    }
                    
                    $postSpecPrices = Tools::getValue('specific_price');
                    if( is_array($postSpecPrices) && count($postSpecPrices) && $product->wholesale_price ){
                        foreach($postSpecPrices as $postSpecPriceId => $postSpecPriceData){
                            $bulkPrice = $product->wholesale_price;
                            $bulkPrice += $bulkPrice / 100 * $postSpecPriceData['margin'];
        
                            $postSpecPriceId = intval($postSpecPriceId);
                            $specialPrice = new SpecificPrice($postSpecPriceId);
                            $specialPrice->price = Tools::ceilf($bulkPrice, 6);// Tools::ceilf( floatval($postSpecPriceData['price']), 6) ;
                            $specialPrice->from_quantity = intval($postSpecPriceData['from_quantity']);
                            $specialPrice->comment = $postSpecPriceData['margin'] .'%';
                            try{
                                $specialPrice->update();
                            }
                            catch(Exception $e){
                                $errors[] = $e->getMessage();
                            }
                        }
                    }
                    
                    //$product_unit = intval( Tools::getValue('unit') );
                    $product_package = intval( Tools::getValue('package') );
                    $product_units_per_package = intval( Tools::getValue('units_per_package') );
                    $product_country = intval( Tools::getValue('country') );
                    
                    /*if(!empty($product_unit)){
                        Db::getInstance()->execute('
                            DELETE FROM `'._DB_PREFIX_.'feature_product`
                            WHERE `id_product` = '.(int)$product->id .' AND `id_feature` = '. $unitFeatureId
                        );
                        $product->addFeaturesToDB($unitFeatureId, $product_unit);
                    }*/
                    if(!empty($product_package)){
                        Db::getInstance()->execute('
                            DELETE FROM `'._DB_PREFIX_.'feature_product`
                            WHERE `id_product` = '.(int)$product->id .' AND `id_feature` = '. $packageFeatureId
                        );
                        $product->addFeaturesToDB($packageFeatureId, $product_package);
                    }
                    if(!empty($product_country)){
                        Db::getInstance()->execute('
                            DELETE FROM `'._DB_PREFIX_.'feature_product`
                            WHERE `id_product` = '.(int)$product->id .' AND `id_feature` = '. $countryFeatureId
                        );
                        $product->addFeaturesToDB($countryFeatureId, $product_country);
                    }
                    if(!empty($product_units_per_package)){
                        Db::getInstance()->execute('
                            DELETE FROM `'._DB_PREFIX_.'feature_product`
                            WHERE `id_product` = '.(int)$product->id .' AND `id_feature` = '. $unitsBoxFeatureId
                            );
                        $product->addFeaturesToDB($unitsBoxFeatureId, $product_units_per_package);
                    }
                    Tools::redirectAdmin(AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminStarfoodProducts'));
                    //Tools::redirect('/admin33870qkeov/'.$this->context->link->getAdminLink('AdminStarfoodProducts').'&token='.Tools::getValue('token'));
            default:
                break;
        }
    }
    
    public function setMedia()
    {
        parent::setMedia();
        
        $this->addJs($this->module->getTemplatePath('views/js/admin.js'));
        
        $bo_theme = 'default';
        
        
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.iframe-transport.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-process.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-validate.js');
        
        $this->addJs(__PS_BASE_URI__.'js/vendor/spin.js');
        $this->addJs(__PS_BASE_URI__.'js/vendor/ladda.js');
        $this->addJs(__PS_BASE_URI__.'js/jquery/plugins/jquery.tablednd.js');
    }
    
    public function showBulkPrice($field, $row)
    {
        $specificPrices = SpecificPrice::getByProductId($row['id_product'], 0, 0);
        $html = '';// '<strong>'. Tools::displayPrice( $field ) .'</strong><br>';
        if( is_array($specificPrices) ){
            foreach($specificPrices as $specificPrice){
                if( ($specificPrice['reduction_type'] != 'amount') || !empty($specificPrice['id_specific_price_rule']) ){
                    continue;
                }
                $address = new Address();
                // Tax
                $address->id_country = Configuration::get('PS_COUNTRY_DEFAULT');
                $address->id_state = 0;
                $address->postcode = 0;
                
                $tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$row['id_product'], $this->context));
                $product_tax_calculator = $tax_manager->getTaxCalculator();
                
                // Add Tax
                $priceSpecificWT = $product_tax_calculator->addTaxes($specificPrice['price']);
                
                //$specificPriceRef = $specificPrice;
                //$priceSpecificWT = Product::getPriceStatic($row['id_product'], true, null,
                //    (int)Configuration::get('PS_PRICE_DISPLAY_PRECISION'), null, true, true, 1, false, null, null, null, $specificPriceRef, false, false);
                $html .= '<span>'. 
                    $specificPrice['comment'].':'. 
                    //$specificPrice['from_quantity'] .'/'.
                    str_replace(' ', '', Tools::displayPrice( $specificPrice['price'] )) .'/'.
                    str_replace(' ', '', Tools::displayPrice($priceSpecificWT)).
                '</span><br>';
            }
        }
        return $html;
    }
    
    public function showFinalPrice($field, $row)
    {
        $priceWT = Product::getPriceStatic($row['id_product'], true, null,
            (int)Configuration::get('PS_PRICE_DISPLAY_PRECISION'), null, false, true, 1, true, null, null, null, $fake, true, true);
        return Tools::displayPrice($field) .' / '. Tools::displayPrice($priceWT);
    }
    
    public function showName($field, $row)
    {
        $content = $field;
        if( is_array($this->name_aliases) ){
            foreach($this->name_aliases as $name_alias){
                $content .= '<br>'. $row['product_name_'. $name_alias];
            }
        }
        return $content;
    }
    
    public function actionQuickEdit()
    {
        $this->ajax = true;
        $reload = false;
        $errors = array();
        $id_product = intval( Tools::getValue('id_product') );
        
        $countryFeatureId = intval( Configuration::get('SFI_FEATURE_COUNTRY') );
        //$unitFeatureId = intval( Configuration::get('SFI_FEATURE_UNIT') );
        $packageFeatureId = intval( Configuration::get('SFI_FEATURE_PACKAGE') );
        $unitsBoxFeatureId = intval( Configuration::get('SFI_FEATURE_UNITS_BOX') );
        
        if(!$id_product){
            echo 'Product can not be found';
        }
        
        
        
        $product = new Product($id_product, true);

        $quantity = StockAvailableCore::getQuantityAvailableByProduct($product->id, 0);
        
        $specPriceQuery = '
            SELECT * 
            FROM '._DB_PREFIX_.'specific_price 
            WHERE id_product = '. $product->id .'
                AND id_specific_price_rule = 0
            ORDER BY `comment` DESC
        ';
        $specPrices = Db::getInstance()->executeS($specPriceQuery);
        
        foreach($specPrices as $si => $specPrice){
            $prcMrgnCmmnt = array();
            if( preg_match('#(\d+)\%#', $specPrice['comment'], $prcMrgnCmmnt) ){
                $specPrices[$si]['margin'] = $prcMrgnCmmnt[1];
            }
        }
        //$images = $product->getImages($this->context->language->id);
        $images = Image::getImages($this->context->language->id, $product->id);
        foreach ($images as $k => $image) {
                    $images[$k] = new Image($image['id_image']);
                }
        $countryOptions = FeatureValueCore::getFeatureValuesWithLang($this->context->language->id, $countryFeatureId);
        //var_dump($countryOptions);
        //$unitOptions = FeatureValueCore::getFeatureValuesWithLang($this->context->language->id, $unitFeatureId);
        $packageOptions = FeatureValueCore::getFeatureValuesWithLang($this->context->language->id, $packageFeatureId);
        $unitsBoxOptions = FeatureValueCore::getFeatureValuesWithLang($this->context->language->id, $unitsBoxFeatureId);
        
        $productFeatures = Product::getFeaturesStatic($product->id);
        foreach($productFeatures as $productFeature){
            if( $productFeature['id_feature'] == $countryFeatureId ){
                for($i = 0; $i < count($countryOptions); $i++){
                    if( $countryOptions[$i]['id_feature_value'] == $productFeature['id_feature_value'] ){
                        $countryOptions[$i]['selected'] = true;
                    }
                }
            }
            /*if( $productFeature['id_feature'] == $unitFeatureId ){
                for($i = 0; $i < count($unitOptions); $i++){
                    if( $unitOptions[$i]['id_feature_value'] == $productFeature['id_feature_value'] ){
                        $unitOptions[$i]['selected'] = true;
                    }
                }
            }*/
            if( $productFeature['id_feature'] == $packageFeatureId ){
                for($i = 0; $i < count($packageOptions); $i++){
                    if( $packageOptions[$i]['id_feature_value'] == $productFeature['id_feature_value'] ){
                        $packageOptions[$i]['selected'] = true;
                    }
                }
            }
            if( $productFeature['id_feature'] == $unitsBoxFeatureId ){
                for($i = 0; $i < count($unitsBoxOptions); $i++){
                    if( $unitsBoxOptions[$i]['id_feature_value'] == $productFeature['id_feature_value'] ){
                        $unitsBoxOptions[$i]['selected'] = true;
                    }
                }
            }
        }
        
        $currencies = Currency::getCurrencies(false, false);
        
        $priceWT = Tools::displayPrice(Product::getPriceStatic($product->id, true, null,
                (int)Configuration::get('PS_PRICE_DISPLAY_PRECISION'), null, false, true, 1, true, null, null, null, $fake, true, true));
        //var_dump($stock);
        
        $image_uploader = new HelperImageUploader('file');
        $image_uploader->setMultiple(false)
            ->setUseAjax(true)
            ->setUrl(Context::getContext()->link->getAdminLink('AdminProducts')
                .'&ajax=1&id_product='.(int)$product->id.'&action=addProductImage')
        ;
        
        //$categories = Category::getCategories((int)Configuration::get('PS_LANG_DEFAULT'));
        $id_cateogies = Product::getProductCategories(Tools::getValue('id_product'));
        $tree_categories_helper = new HelperTreeCategories('categories-treeview');
        $tree_categories_helper->setRootCategory((Shop::getContext() == Shop::CONTEXT_SHOP ? Category::getRootCategory()->id_category : 0))
				->setUseCheckBox(true);
        $tree_categories_helper->setSelectedCategories($id_cateogies);
        if ($this->context->shop->getContext() == Shop::CONTEXT_SHOP) {
            $current_shop_id = (int)$this->context->shop->id;
        } else {
            $current_shop_id = 0;
        }
        $shops = Shop::getShops(); 
        $languages = Language::getLanguages(true);
        
        $measureUnitList = Db::getInstance()->executeS('
            SELECT * FROM `'._DB_PREFIX_.'measure_unit` ORDER BY name
        ');
        
        $liquidDensityList = Db::getInstance()->executeS('
            SELECT * FROM `'._DB_PREFIX_.'liquid_density` ORDER BY name
        ');
        
        $tplData = array(
            'manufacturers' => Manufacturer::getManufacturers(false, $this->context->language->id),
            'taxes' => TaxRulesGroup::getTaxRulesGroups(true),
            'product' => $product,
            'quantity' => $quantity,
            'images' => $images,
            'shop' =>  $this->context->shop,
            'shops' => $shops,
            'current_shop_id' => $current_shop_id,
            'token' => Tools::getAdminTokenLite('AdminProducts'),
            
            //'categories' =>$categories,
            'tree' =>$tree_categories_helper->render(),
            'specific_prices' => $specPrices,
            'form_action' => $this->context->link->getAdminLink('AdminStarfoodProducts'). '&action=updateQuick&id_product='. $product->id,
            'errors' => count($errors) ? $errors : null,
            'country_options' => $countryOptions,
            'unit_options' => $measureUnitList,
            'liquid_density_list' => $liquidDensityList,
            'package_options' => $packageOptions,
            'units_box_options' => $unitsBoxOptions,
            'currencies' => $currencies,
            'price_wt' => $priceWT,
            'iso_lang' => $languages[0]['iso_code'],
            'languages' => $languages,
            'image_uploader' => $image_uploader->render()
        );
        $type = ImageType::getByNameNType('%', 'products', 'height');
                if (isset($type['name'])) {
                    $this->context->smarty->assign('imageType', $type['name']);
                } else {
                    $this->context->smarty->assign('imageType', ImageType::getFormatedName('small'));
                }
        
        $this->context->smarty->assign($tplData);
        
        $html = $this->context->smarty->fetch($this->module->getTemplatePath('views/templates/admin/products/quick_edit.tpl'));
        echo json_encode(array(
            'html' => $html,
            'reload' => $reload,
            'error' =>$errors
        ));
    }
    
    public function processExport($text_delimiter = '"')
    {
        // clean buffer
        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }
        $this->getList($this->context->language->id, null, null, 0, false);
        if (!count($this->_list)) {
            return;
        }
        
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        
        // Set document properties
        $objPHPExcel->getProperties()
            ->setCreator($this->context->shop->name .' ('. $this->context->shop->domain .')')
            ->setLastModifiedBy($this->context->shop->name.' ('. $this->context->shop->domain .')')
            ->setTitle("Products")
        ;
        
        $objPHPExcel->setActiveSheetIndex(0);
        $rowNumber = 0;
        $rowNumber++;
        $colCharNum = ord('@');
        foreach($this->fields_list as $fieldName => $fieldOptions){
            $colCharNum++;
            $objPHPExcel
                ->getActiveSheet()
                ->setCellValue( (chr($colCharNum).$rowNumber), $fieldOptions['title'])
            ;
            if( $fieldName == 'id_image' ){
                $objPHPExcel->getActiveSheet()->getColumnDimension(chr($colCharNum))->setWidth(50);
            }
            else{
                $objPHPExcel->getActiveSheet()->getColumnDimension(chr($colCharNum))->setAutoSize(true);
            }
        }
        
        foreach($this->_list as $dbRec){
            $rowNumber++;
            $colCharNum = ord('A');
        
            foreach($this->fields_list as $fieldName => $fieldOptions){
                if( $fieldName == 'id_image' ){
                    $imagePath = _PS_PROD_IMG_DIR_.Image::getImgFolderStatic($dbRec['id_image']).$dbRec['id_image'].'-home_default.jpg';
                    if (file_exists($imagePath))
                    {
                        $xlsDraw = new PHPExcel_Worksheet_Drawing();
                        $xlsDraw->setPath($imagePath);
                        $xlsDraw->setCoordinates((chr($colCharNum++).$rowNumber));
                        $xlsDraw->setOffsetX(1);
                        $xlsDraw->setOffsetY(1);
                        $objPHPExcel->getActiveSheet()->getRowDimension($rowNumber)->setRowHeight(220);
                        $xlsDraw->setWorksheet($objPHPExcel->getActiveSheet());
                    }
                    else{
                        $objPHPExcel
                            ->getActiveSheet()
                            ->setCellValue( (chr($colCharNum++).$rowNumber), '')
                        ;
                        
                    }
                }
                else{
                    $objPHPExcel
                        ->getActiveSheet()
                        ->setCellValue( (chr($colCharNum++).$rowNumber), $dbRec[$fieldName])
                    ;
                    
                }
            }
        }
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Products');
        
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        
        
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="products_'.date('Y-m-d_His').'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
        
    }
}