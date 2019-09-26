<?php
class Product extends ProductCore
{
    public $box_code;
    public $supply_price;
    public $supply_currency_id;
    public $supply_cost;
    public $customs_tax;
    public $profit_margin;
    public $margin_retail;
    public $margin_bulk_1;
    public $margin_bulk_2;
    public $margin_bulk_3;
    public $quantity_bulk_1;
    public $quantity_bulk_2;
    public $quantity_bulk_3;
    
    public $id_measure_unit;
    
    public $unit_value;
    
    public $id_liquid_density;
    
    public $pieces_unit;
    
    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, Context $context = null)
    {
        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
        
        $this->def['fields']['upc']['validate'] = null;
        $this->def['fields']['upc']['size'] = 32;
        
        $this->def['fields']['box_code'] = array('type' => self::TYPE_STRING);
        $this->def['fields']['supply_price'] = array('type' => self::TYPE_FLOAT);
        $this->def['fields']['supply_currency_id'] = array('type' => self::TYPE_INT);
        $this->def['fields']['supply_cost'] = array('type' => self::TYPE_INT);
        $this->def['fields']['customs_tax'] = array('type' => self::TYPE_INT);
        $this->def['fields']['profit_margin'] = array('type' => self::TYPE_INT);
        $this->def['fields']['margin_retail'] = array('type' => self::TYPE_INT);
        $this->def['fields']['margin_bulk_1'] = array('type' => self::TYPE_INT);
        $this->def['fields']['margin_bulk_2'] = array('type' => self::TYPE_INT);
        $this->def['fields']['margin_bulk_3'] = array('type' => self::TYPE_INT);
        $this->def['fields']['quantity_bulk_1'] = array('type' => self::TYPE_INT);
        $this->def['fields']['quantity_bulk_2'] = array('type' => self::TYPE_INT);
        $this->def['fields']['quantity_bulk_3'] = array('type' => self::TYPE_INT);
        
        $this->def['fields']['id_measure_unit'] = array('type' => self::TYPE_INT);
        $this->def['fields']['unit_value'] = array('type' => self::TYPE_FLOAT);
        $this->def['fields']['id_liquid_density'] = array('type' => self::TYPE_INT);
        $this->def['fields']['pieces_unit'] = array('type' => self::TYPE_INT);
    }
    
    public static function getCMSContent($id_cms, $id_lang)
    {
        $cms = new CMS($id_cms, $id_lang);
        return $cms->content;
    }
    
    
    public static function getProducts($id_lang, $start, $limit, $order_by, $order_way, $id_category = false,
        $only_active = false, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'p';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        } elseif ($order_by == 'position') {
            $order_by_prefix = 'c';
        }
        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }
        
        
        $sql = 'SELECT p.*, product_shop.*, pl.* , m.`name` AS manufacturer_name, s.`name` AS supplier_name
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)'.
                ($id_category ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '').'
				WHERE pl.`id_lang` = '.(int)$id_lang;
        
        if (is_array($id_category))
        {
            $sql .= ' AND c.`id_category` in ('.implode(',', $id_category).')';
        }
        else
        {
            $sql .= ($id_category ? ' AND c.`id_category` = '.(int)$id_category : '');
        }
        $sql .= ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
                    ($only_active ? ' AND product_shop.`active` = 1' : '').
                ' group by p.id_product '.
                ' ORDER BY '.(isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').'`'.pSQL($order_by).'` '.pSQL($order_way).
                ($limit > 0 ? ' LIMIT '.(int)$start.','.(int)$limit : '');
        $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if ($order_by == 'price') {
            Tools::orderbyPrice($rq, $order_way);
        }
        foreach ($rq as &$row) {
            $row = Product::getTaxesInformations($row);
        }
        return ($rq);
    }
    
    public function getAttributesGroups($id_lang, $addSku=0)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }
        $sql = 'SELECT ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, agl.`public_name` AS public_group_name,
					a.`id_attribute`, al.`name` AS attribute_name, a.`color` AS attribute_color, product_attribute_shop.`id_product_attribute`,
					IFNULL(stock.quantity, 0) as quantity, product_attribute_shop.`price`, product_attribute_shop.`ecotax`, product_attribute_shop.`weight`,
					product_attribute_shop.`default_on`, pa.`reference`, product_attribute_shop.`unit_price_impact`,
					product_attribute_shop.`minimal_quantity`, product_attribute_shop.`available_date`, ag.`group_type`'.
                                        ($addSku?', ps.product_supplier_reference ':'').
				'FROM `'._DB_PREFIX_.'product_attribute` pa
				'.Shop::addSqlAssociation('product_attribute', 'pa').'
				'.Product::sqlStock('pa', 'pa').
                                ($addSku?' left join '._DB_PREFIX_.'product_supplier ps on pa.id_product=ps.id_product and
                                    pa.id_product_attribute=ps.id_product_attribute and ps.id_supplier='.intval($addSku):'').
				' LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
				LEFT JOIN `'._DB_PREFIX_.'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
				LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`)
				LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute`)
				LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group`)
				'.Shop::addSqlAssociation('attribute', 'a').'
				WHERE pa.`id_product` = '.(int)$this->id.'
					AND al.`id_lang` = '.(int)$id_lang.'
					AND agl.`id_lang` = '.(int)$id_lang.'
				GROUP BY id_attribute_group, id_product_attribute
				ORDER BY ag.`position` ASC, a.`position` ASC, agl.`name` ASC';
        return Db::getInstance()->executeS($sql);
    }
    
    public function getMeasureUnit()
    {
        if( $this->id_measure_unit > 0 ){
            $measureUnit = new MeasureUnit($this->id_measure_unit);
            return $measureUnit;
        }
        else{
            return null;
        }
    }

    public function getPackageUnitInfo($id_lang)
    {
        $packageFeatureId = intval( Configuration::get('SFI_FEATURE_PACKAGE') );
        $unitsBoxFeatureId = intval( Configuration::get('SFI_FEATURE_UNITS_BOX') );
        
        $productFeatures = $this->getFrontFeatures($id_lang);
        $piecesPerCartonText = '';
        $unitsBoxValue = 1;
        $packageValue = 'n/a';
        $unitValue = 'n/a';
        
        $productMeasureUnit = $this->getMeasureUnit();
        if( is_object($productMeasureUnit) ){
            if( ($this->unit_value > 0) ){
                $unitValue = sprintf('%.3g', $this->unit_value) .' '. $productMeasureUnit->name;
            }
            else{
                $unitValue = $this->pieces_unit .' '. $productMeasureUnit->name;
            }
        }
        
        foreach($productFeatures as $productFeature){
            if( $productFeature['id_feature'] == $unitsBoxFeatureId ){
                $unitsBoxValue = $productFeature['value'];
            }
            if( $productFeature['id_feature'] == $packageFeatureId ){
                $packageValue = $productFeature['value'];
            }
        }
        
        $piecesPerCartonText = $unitsBoxValue .' ('.$packageValue.') &#x00D7; '. $unitValue ;
        return $piecesPerCartonText;
    }
}
