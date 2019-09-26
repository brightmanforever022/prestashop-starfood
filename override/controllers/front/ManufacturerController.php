<?php

class ManufacturerController extends ManufacturerControllerCore
{
    protected function assignAll()
    {
        $manufacturersList = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
    		SELECT m.*, ml.`description`, ml.`short_description`
    		FROM `'._DB_PREFIX_.'manufacturer` m
    		'.Shop::addSqlAssociation('manufacturer', 'm').'
    		INNER JOIN `'._DB_PREFIX_.'manufacturer_lang` ml 
                ON (m.`id_manufacturer` = ml.`id_manufacturer` AND ml.`id_lang` = '.(int)$this->context->language->id.')
    		WHERE m.`active` = 1
    		ORDER BY m.`name` ASC
        ');
        
        $manufacturersABC = array();
        
        foreach( $manufacturersList as $manufacturerData ){
            $mnfFirstLetter = strtoupper( $manufacturerData['name'][0] );
            
            if( !isset($manufacturersABC[ $mnfFirstLetter ]) ){
                $manufacturersABC[ $mnfFirstLetter ] = array();
            }
            
            $manufacturerData['image'] = (!file_exists(_PS_MANU_IMG_DIR_.$manufacturerData['id_manufacturer'].'-'.ImageType::getFormatedName('medium').'.jpg')) ? $this->context->language->iso_code.'-default' : $manufacturerData['id_manufacturer'];
            $manufacturerData['link_rewrite'] = Tools::link_rewrite($manufacturerData['name']);
            
            $manufacturersABC[ $mnfFirstLetter ][] = $manufacturerData;
        }

        $this->context->smarty->assign(array(
            'manufacturers_abc' => $manufacturersABC
        ));

    }
    
    protected function assignOne()
    {
        $this->manufacturer->description = Tools::nl2br(trim($this->manufacturer->description));
        $nbProducts = $this->manufacturer->getProducts($this->manufacturer->id, null, null, null, $this->orderBy, $this->orderWay, true);
        $this->pagination((int)$nbProducts);
    
        $products = $this->manufacturer->getProducts($this->manufacturer->id, $this->context->language->id, (int)$this->p, (int)$this->n, $this->orderBy, $this->orderWay);
        $this->addColorsToProductList($products);
        
        Hook::exec('actionProductListModifier', array(
            'nb_products'  => &$nbProducts,
            'cat_products' => &$products,
        ));
    
        $this->context->smarty->assign(array(
            'nb_products' => $nbProducts,
            'products' => $products,
            'path' => ($this->manufacturer->active ? Tools::safeOutput($this->manufacturer->name) : ''),
            'manufacturer' => $this->manufacturer,
            'comparator_max_item' => Configuration::get('PS_COMPARATOR_MAX_ITEM'),
            'body_classes' => array($this->php_self.'-'.$this->manufacturer->id, $this->php_self.'-'.$this->manufacturer->link_rewrite)
        ));
    }
    
}