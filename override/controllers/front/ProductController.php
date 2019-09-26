<?php

class ProductController extends ProductControllerCore
{
    public function initContent()
    {
        parent::initContent();
        
        $englishLanguageId = LanguageCore::getIdByIso('en');
        
        $productEnglishName = Db::getInstance()->getValue('
            select name from `'._DB_PREFIX_.'product_lang`
            where id_product = '. $this->product->id .'
                and id_lang = '. $englishLanguageId .'
        ');
        
        $unitsBoxValue = 1;
        
        $this->context->smarty->assign('units_per_box', $unitsBoxValue);

        $this->context->smarty->assign(array(
            'pieces_per_carton_text' => $this->product->getPackageUnitInfo($this->context->language->id),
            'product_english_name' => $productEnglishName
        ));
    }
}