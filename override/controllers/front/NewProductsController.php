<?php

class NewProductsController extends NewProductsControllerCore
{
    public function initContent()
    {
        parent::initContent();
    
        $this->productSort();
    
        // Override default configuration values: cause the new products page must display latest products first.
        if (!Tools::getIsset('orderway') || !Tools::getIsset('orderby')) {
            $this->orderBy = 'date_add';
            $this->orderWay = 'DESC';
        }
    
        $nb_products = (int)Product::getNewProducts(
            $this->context->language->id,
            (isset($this->p) ? (int)$this->p - 1 : null),
            (isset($this->n) ? (int)$this->n : null),
            true
            );
    
        $this->pagination($nb_products);
    
        $products = Product::getNewProducts($this->context->language->id, (int)$this->p - 1, (int)$this->n, false, $this->orderBy, $this->orderWay);
        $this->addColorsToProductList($products);

        Hook::exec('actionProductListModifier', array(
            'nb_products'  => &$nb_products,
            'cat_products' => &$products,
        ));
        
        $this->context->smarty->assign(array(
            'products' => $products,
            'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
            'nbProducts' => (int)$nb_products,
            'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
            'comparator_max_item' => Configuration::get('PS_COMPARATOR_MAX_ITEM')
        ));
    
        $this->setTemplate(_PS_THEME_DIR_.'new-products.tpl');
    }
    
}