<?php

class PricesDropController extends PricesDropControllerCore
{
    public function initContent()
    {
        parent::initContent();
    
        $this->productSort();
        $nbProducts = Product::getPricesDrop($this->context->language->id, null, null, true);
        $this->pagination($nbProducts);
    
        $products = Product::getPricesDrop($this->context->language->id, (int)$this->p - 1, (int)$this->n, false, $this->orderBy, $this->orderWay);
        $this->addColorsToProductList($products);

        Hook::exec('actionProductListModifier', array(
            'nb_products'  => &$nbProducts,
            'cat_products' => &$products,
        ));
        
        $this->context->smarty->assign(array(
            'products' => $products,
            'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
            'nbProducts' => $nbProducts,
            'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
            'comparator_max_item' => Configuration::get('PS_COMPARATOR_MAX_ITEM')
        ));
        
        $this->setTemplate(_PS_THEME_DIR_.'prices-drop.tpl');
    }
    
}