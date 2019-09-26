<?php

/*
 * * Format 1x1
 */

require_once(_PS_MODULE_DIR_ . 'scpdfcatalog/sctcpdf/tcpdf.php');
require_once(_PS_MODULE_DIR_ . 'scpdfcatalog/SCPDFCatalogCreator.php');

class catalog extends SCPDFCatalogCreator {

    public $title_length = 1500;

    public function createCatalog($format, $start, $limit, $orderBy, $orderWay) {
        $this->format = $format;
        $active = $this->conf['PS_SC_PDFCATALOG_ACTIVEPRODUCT'];
        $this->showPageNum = true;
        foreach ($this->categories AS $id_category) {
            $products = Product::getProducts($this->id_lang, $start, $limit, $orderBy, $orderWay, $id_category,$active);
			$products = $this->filtersByStockAndManufacturer($products);
            $category = new Category($id_category);
            
        	if (count($products) > 0)
			{
				if ($this->conf['PS_SC_PDFCATALOG_CATEGCOVER'])
				{
					$this->addCategoryCoverPage(parent::hideCategoryPosition($category->getName($this->id_lang)), $category->description[$this->id_lang], $id_category);
					$this->addBookmark($id_category, $category);
				}
			}
			else
			{
				$this->categ_waiting[] = $id_category;
			}
			$this->calculPrefix($id_category, $category);
            
			$i = 0;
            foreach ($products AS $productrow) {
                $product = new Product((int)($productrow['id_product']));
                /*if (!$product->active)
                    continue;*/
                if (Validate::isLoadedObject($product)) {
                	if($i==0 && !$this->conf['PS_SC_PDFCATALOG_CATEGCOVER'])
                	{
                		$this->calculPrefix($id_category, $category);
                		$this->addBookmark($id_category, $category,true,$this->getPage()+1);
                	}
                    $this->addProductPage($product, $id_category);
                    $i++;
                } else {
                    die('Invalid product');
                }
            }
        }
    }

    public function addProductPage($product, $id_category) {
        $category_title_height = 10;
        $imageHeight = 75;

        if ($this->logo_height == 0)
            $this->getLogoHeight();
        $imageOffset = $category_title_height + $this->logo_height + 5;

        $deltapage = $this->getNumPages();
        $this->AddPage();
        $this->SetTextColor(0, 0, 0);
        $this->SetFont($this->fontname(), '', 14);
        $this->SetXY(10, $this->logo_height);
        $this->WriteHTMLCell(190, 10, '', '', $this->categ_names[$id_category]["name"] . ' : ' . $product->name[$this->id_lang]); //,1,2,1);
        $this->SetFont($this->fontname(), '', 10);

        $coverid = Product::getCover($product->id);
        
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    		$pdt_link = $product->getLink();
    	else
    		$pdt_link = SCPDFCatalog::getHttpHost(). __PS_BASE_URI__ . 'product.php?id_product=' . (int)($product->id);

        if (file_exists(_PS_IMG_DIR_ . 'p/' . $this->getImgFolderStatic($coverid['id_image']) . $coverid['id_image'] . '-' . $this->imageformat . '.jpg')) {
                $this->Image(_PS_IMG_DIR_ . 'p/' . $this->getImgFolderStatic($coverid['id_image']) . $coverid['id_image'] . '-' . $this->imageformat . '.jpg', 10, $imageOffset, 0, $imageHeight, '', ($this->conf['PS_SC_PDFCATALOG_LINKS'] ? $pdt_link : ''));
            /*if (Configuration::get('PS_LEGACY_IMAGES') && !file_exists(_PS_IMG_DIR_ . 'p/' . $this->getImgFolderStatic($coverid['id_image']) . $coverid['id_image'] . '-' . $this->imageformat . '.jpg'))
                $this->Image(_PS_IMG_DIR_ . 'p/' . $product->id . '-' . $coverid['id_image'] . '-' . $this->imageformat . '.jpg', 10, $imageOffset, 0, $imageHeight, '', ($this->conf['PS_SC_PDFCATALOG_LINKS'] ? $pdt_link : ''));*/
        }else {
            if (file_exists(_PS_IMG_DIR_ . 'p/' . $product->id . '-' . $coverid['id_image'] . '-' . $this->imageformat . '.jpg'))
                $this->Image(_PS_IMG_DIR_ . 'p/' . $product->id . '-' . $coverid['id_image'] . '-' . $this->imageformat . '.jpg', 10, $imageOffset, 0, $imageHeight, '', ($this->conf['PS_SC_PDFCATALOG_LINKS'] ? $pdt_link : ''));
        }
        $this->SetXY(100, $imageOffset);

        /*
         * Displays the title or the short desc or both according to  $this->product_title_mode
         */
        $product_label = $this->displayProductLabel($product->name[$this->id_lang], $product->description_short[$this->id_lang], $this->title_length, false, true);

        $this->WriteHTMLCell(100, 80, 100, $imageOffset, $product_label);

        $this->SetXY(10, $imageOffset + 75);
        $features = $product->getFrontFeaturesStatic($this->id_lang, $product->id);
        $y = $this->getY();
        $y2 = $y;
        // Features
        $htmlfeatures = '<br/>';
        if (count($features)) {
            $htmlfeatures.= '<b>' . $this->mod->tt('Product features:') . '</b><br/><br/>';
        }
    	$display_features = array();
        foreach ($features AS $feature) 
        {
        	if(empty($display_features[$feature['name']]))
        		$display_features[$feature['name']]=array();
        	
        	$display_features[$feature['name']][] = $feature['value'];
        }
        foreach ($display_features AS $name=>$values) {
            //$y += 5;
            $htmlfeatures.= $name.$this->mod->tt(':').' '.implode(", ", $values).'<br/>';
        }
        // Attributes
        $attributes = Db::getInstance()->ExecuteS('
		SELECT pa.*, ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, al.`name` AS attribute_name, a.`id_attribute`
		FROM `' . _DB_PREFIX_ . 'product_attribute` pa
		LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
		LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
		LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
		LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int)($this->id_lang) . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int)($this->id_lang) . ')
		WHERE pa.`id_product` = ' . (int)($product->id) . '
		ORDER BY agl.`name`,al.`name`');
        $combinations = array();
        foreach ($attributes AS $attribute) {
            // Combinations
            $result = Db::getInstance()->ExecuteS('
			SELECT agl.`name` AS group_name, al.`name` AS attribute_name, pa.reference, pa.price, pa.id_product_attribute
			FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
			LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.`id_product_attribute` = pac.`id_product_attribute`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int)($this->id_lang) . ')
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int)($this->id_lang) . ')
			WHERE pac.`id_product_attribute` = pa.`id_product_attribute`
			AND pac.`id_product_attribute` = ' . $attribute['id_product_attribute'] . '
			ORDER BY agl.`name`,al.`name`');
            if (count($result))
                $combinations[] = $result;
        }
        $htmlpricetable = '<table width="100%" border="1" cellpadding="2">';
        $htmlpricetable.= '<tr><td width="50%" bgcolor="#EEEEEE">' . $this->mod->tt('Name') . '</td>
        		<td width="20%" bgcolor="#EEEEEE" style="text-align:center;">' . $this->mod->tt('Reference') . '</td>';

        if ($this->vatexc == 1)
            $htmlpricetable.='<td width="15%" bgcolor="#EEEEEE" style="text-align:right;">' . $this->mod->tt('Price Exc. Tax') . '</td>';

        if ($this->vatinc == 1)
            $htmlpricetable.='<td width="15%" bgcolor="#EEEEEE" style="text-align:right;">' . $this->mod->tt('Price Inc. Tax') . '</td>';

        $htmlpricetable.='</tr>';

        if (count($combinations)) {
            $tabDisplayedCombinations = array();
            $colorFlag = 0;
            if (version_compare(_PS_VERSION_, '1.4.0.0', '>=')) {
                $tax = new Tax((int)($product->id_tax_rules_group), (int)($this->id_lang));
            } else {
                $tax = new Tax((int)($product->id_tax), (int)($this->id_lang));
            }

            foreach ($combinations AS $combination) {
                $colorFlag++;
                $text = '';
                foreach ($combination AS $attribute) {
                    $text.= $attribute['group_name'] . ' ' . $attribute['attribute_name'] . ', ';
                }
                
                $text = Tools::substr($text, 0, Tools::strlen($text) - 2) . '</td>
                  <td width="20%">' . $attribute['reference'] . '</td>';

                    if ($this->vatexc == 1)
                        $text.='<td width="15%" align="right">' . $this->getPrice($product->id, $attribute['id_product_attribute'], 0) . '</td>';

                    if ($this->vatinc == 1)
                        $text.='<td width="15%" align="right">' . $this->getPrice($product->id, $attribute['id_product_attribute'], 1) . '</td>';
              
                if (!in_array($text, $tabDisplayedCombinations)) {
                    $htmlpricetable.= '<tr' . ($colorFlag % 2 == 0 ? ' bgcolor="#FAFAFA"' : '') . '><td width="50%">' . $text . '</tr>';
                    $tabDisplayedCombinations[] = $text;
                }
            }
        } else {
            $htmlpricetable.= '<tr>
                    <td width="50%">' . $product->name[$this->id_lang] . '</td>
                    <td width="20%">' . $product->reference . '</td>';
            if ($this->vatexc == 1)
                $htmlpricetable.='<td width="15%" align="right">' . $this->getPrice($product->id, 0, 0) . '</td>';

            if ($this->vatinc == 1)
                $htmlpricetable.='<td width="15%" bgcolor="#EEEEEE" align="right">' . $this->getPrice($product->id, 0, 1) . '</td>';

            $htmlpricetable.='</tr>';
        }
        $htmlpricetable.= '</table>';

        $description = $product->description[$this->id_lang];
        $description = preg_replace('/<\/div>/', '</p>', $description);
        $description = preg_replace('<div style="padding-bottom:3px">', '<p>', $description);
        $description = strip_tags($description, '<br/><br><br /><em><strong><p><div>');

        $this->WriteHTMLCell(0, 80, '', '', '<br/>' . $description . $htmlfeatures . '<br/><br/>' . $htmlpricetable, '', 0);

        $deltapage2 = $this->getNumPages() - 1;
        while (($deltapage2 - $deltapage) > 0) {
            $this->AddPage();
            $deltapage++;
        }
    }

}
