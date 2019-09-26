<?php

/*
 * * Format list03
 */

require_once(_PS_MODULE_DIR_ . 'scpdfcatalog/sctcpdf/tcpdf.php');
require_once(_PS_MODULE_DIR_ . 'scpdfcatalog/SCPDFCatalogCreator.php');

class catalog extends SCPDFCatalogCreator
{

    public $title_length = 600;
    public $start_y = 0;
    public $first_start_y = 0;
    public $offset = 0;
    public $lastPage = false;

    public function createCatalog($format, $start, $limit, $orderBy, $orderWay)
    {
        $this->format = $format;
        $active = $this->conf['PS_SC_PDFCATALOG_ACTIVEPRODUCT'];

        $offset_nb = 0;
        if ($this->vatexc == 1)
            $offset_nb++;
        if ($this->vatinc == 1)
            $offset_nb++;
        if ($this->emptycol == 1)
            $offset_nb++;

        if ($offset_nb == 0)
            $offset = -20;
        elseif ($offset_nb == 1)
            $offset = 0;
        elseif ($offset_nb == 2)
            $offset = 20;
        elseif ($offset_nb == 3)
            $offset = 40;
        $this->offset = $offset;

        $this->showPageNum = true;
        
//        if (!$this->conf['PS_SC_PDFCATALOG_FIRSTPAGE'])
//            $this->Ln(9);
        
        $this->AddPage();
        $shownProductIds = array();
        foreach ($this->categories AS $id_category)
        {
            $this->header_page = false;
            
            //$this->currentCategory = $id_category;
            //$category = new Category($id_category);
            /* if (!$category->active)
              continue; */

            $products = Product::getProducts($this->id_lang, $start, $limit, $orderBy, $orderWay, $id_category, $active);
            $products = $this->filtersByStockAndManufacturer($products);
            /*
            if (count($products) > 0)
            {
                if ($this->conf['PS_SC_PDFCATALOG_CATEGCOVER'])
                    $this->addCategoryCoverPage(parent::hideCategoryPosition($category->getName($this->id_lang)), $category->description[$this->id_lang], $id_category);
                else
                {
                    $idx = 0;
                    if (empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']))
                    {
                        $this->header_page = true;
                        $this->AddPage();
                        $this->addLibelleLine();
                    }
                    else
                    {
                        $this->addCategoryLine(parent::hideCategoryPosition($category->getName($this->id_lang)));
                    }
                }
                  
                 

                $this->calculPrefix($id_category, $category);
                $this->addBookmark($id_category, $category);

                if ($this->conf['PS_SC_PDFCATALOG_CATEGCOVER'])
                {
                    $idx = 0;
                    if (empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']))
                    {
                        $this->AddPage();
                        $this->addLibelleLine();
                    }
                }
            }
            else
            {
                $this->calculPrefix($id_category, $category);
                $this->categ_waiting[] = $id_category;
            }
             * 
             */
            
            //$this->addLibelleLine();
            $rows = "";

            $product = new Product((int) ($products[0]['id_product']));
            
//            for($i=20; $i>=0; $i--)
//            {
//                $this->getProductLineUPC($product, 'small');
//            }
            foreach ($products AS $productrow)
            {
                if (in_array($productrow['id_product'], $shownProductIds))
                {
                    continue;
                }
                $shownProductIds []= $productrow['id_product'];
                
                $product = new Product((int) ($productrow['id_product']));
                /* if (!$product->active)
                  continue; */
                if (Validate::isLoadedObject($product))
                {
                    $this->getProductLineUPC($product, 'small');
                }
                else
                {
                    die('Invalid product');
                }
            }
            
            /*
              foreach ($products AS $productrow) {
              $product = new Product((int)($productrow['id_product']));
              /*if (!$product->active)
              continue; */
            /*
              if (!isset($this->categ_names[$id_category])) {
              $category = new Category($id_category);
              $this->categ_names[$id_category] = parent::hideCategoryPosition($category->getName($this->id_lang));
              if (!array_key_exists($category->id_parent, $this->categ_names)) {
              $pcategory = new Category($category->id_parent);
              $this->categ_names[$category->id_parent] = parent::hideCategoryPosition($pcategory->getName($this->id_lang));
              }
              $this->Ln(2);
              $this->addCategoryLineMark($category->getName($this->id_lang), ($category->id_parent != 1 ? $this->categ_names[$category->id_parent] . ' > ' : ''));
              }
              if (Validate::isLoadedObject($product)) {
              $this->getProductLineUPC($product);
              } else {
              die('Invalid product');
              }
              }
             * 
             */
        }
        // add last page
        $this->addLastPage();
    }

    
    /**
     * add last image image if image exists
     */
    function addLastPage()
    {
        global $smarty, $paramFormat;
        
        if (file_exists(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/lastpage.jpg'))
            $image = _PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/lastpage.jpg';
        if (file_exists(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/lastpage.jpeg'))
            $image = '/modules/scpdfcatalog/templates/' . $paramFormat . '/lastpage.jpeg';
        if (file_exists(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/lastpage.png'))
            $image = '/modules/scpdfcatalog/templates/' . $paramFormat . '/lastpage.png';
        if (!empty($image))
        {
            $this->AddPage();
            $this->lastPage = true;
            // get the current page break margin
            $bMargin = $this->getBreakMargin();
            // get current auto-page-break mode
            $auto_page_break = $this->AutoPageBreak;
            // disable auto-page-break
            $this->SetAutoPageBreak(false, 0);
            // set bacground image
            $img_file = $image;
            $this->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
            // restore auto-page-break status
            $this->SetAutoPageBreak($auto_page_break, $bMargin);
        }
    }
    
    
    public function Footer() 
    {
        
        global $paramFormat;
          
        if (!$this->lastPage && (!$this->conf['PS_SC_PDFCATALOG_FIRSTPAGE'] || ($this->conf['PS_SC_PDFCATALOG_FIRSTPAGE'] && $this->getPage() != 1))
                && file_exists(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/footer_logo.jpg')) 
        {
            if($this->conf['PS_SC_PDFCATALOG_PAGENUMBER']==1 )
            {
                $this->setXY(197, 280);
                $this->Cell(0, 5,$this->getAliasNumPage());
            }
            
            $this->Image(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/footer_logo.jpg', 
                    81, 280, 48, 7, '', '', '', true, 300, '', false, false, 0);
        }
        
        /**
        $this->SetY(-20);
        $this->Ln(9);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont($this->fontname(), '', 8);
        if (!$this->conf['PS_SC_PDFCATALOG_FIRSTPAGE'] || ($this->conf['PS_SC_PDFCATALOG_FIRSTPAGE'] && $this->getPage() != 1 )) {
        	
        	$content_footer = "";
        	if(!empty($this->conf['PS_SC_PDFCATALOG_FOOTER']))
        		$content_footer = $this->conf['PS_SC_PDFCATALOG_FOOTER'];
        
			$pattern = '/\[([^\]]*)\]/';//$pattern = '/\[date:(.*)\]/';
			preg_match_all($pattern, $content_footer, $dates);
			//echo "<pre>";print_r($dates);echo "</pre><br/><br/>";
        	foreach ($dates[1] as $date)
        	{
        		$exp = explode(":",$date);
        		$replace = $exp[1];
        		$replace = str_replace("d", Date("d"), $replace);
        		$replace = str_replace("m", Date("m"), $replace);
        		$replace = str_replace("y", Date("Y"), $replace);
        		
        		$content_footer = str_replace("[".$date."]", $replace, $content_footer);
        	}
        	//$content_footer = str_replace("[date:d/m/y]", date("d/m/Y"), $content_footer);
        	
            $this->WriteHTMLCell(180, 15, 10,280, $content_footer);
            
            if($this->conf['PS_SC_PDFCATALOG_PAGENUMBER']==1 ){
                $this->setXY(195,$this->getY());
                $this->Cell(0, 5,$this->getAliasNumPage().'/'.$this->getAliasNbPages());            
            }
            
       }
         * 
         * @param type $catname
         */
    }
    
    public function addCategoryLine($catname)
    {
        $this->first_start_y = $this->GetY();

        $posx = array(10, 30, 65, 70);
        $widths = array(20, 35, 25, 35);
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont($this->fontname(), 'B', 8);
        //$this->Ln(7);
        $this->setX($posx[0]);
        $this->Cell($widths[0] + $widths[1], 6, $catname, 0, 0, 'L', 0);

        $this->SetY($this->GetY() + 6);
    }

    public function Header()
    {
        if (!$this->lastPage && (!$this->conf['PS_SC_PDFCATALOG_FIRSTPAGE'] || ($this->conf['PS_SC_PDFCATALOG_FIRSTPAGE'] && $this->getPage() != 1)))
        {
            $this->addLibelleLine();
            if ($this->GetY() < 11)
            {
                $this->setY(13);
            }
            /*
            if (!empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']) && $this->is_toc == false)
            {
                $this->setX($this->logo_height + 3);
                $this->addLibelleLine();
            }
             * 
             */
        }
    }

    /**
     * Renders list title
     */
    public function addLibelleLine()
    {
        $this->first_start_y = $this->GetY()+14;

        $posx = array(10, 30, 65, 90, 115, 140);
        $widths = array(20, 35, 25, 25, 25, 25);
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont($this->fontname(), 'B', 8);
        $this->Ln(7);
        $this->setX($posx[0]);
        $this->Cell($widths[0], 6, $this->mod->tt('Photo / Foto'), array('TLRB' => array('width' => 0.1)), 0, 'C', 0);
        $this->setX($posx[1] - $this->offset);
        $this->Cell($widths[1], 6, $this->mod->tt('Style / Artikel'), array('TLRB' => array('width' => 0.1)), 0, 'C', 0);
        $this->setX($posx[2] - $this->offset);
        $this->Cell($widths[2], 6, $this->mod->tt('Color / Foto'), array('TLRB' => array('width' => 0.1)), 0, 'C', 0);
        $this->setX($posx[3] - $this->offset);
        $this->Cell($widths[3], 6, $this->mod->tt('Size / Größe'), array('TLRB' => array('width' => 0.1)), 0, 'C', 0);
        $this->setX($posx[4] - $this->offset);
        $this->Cell($widths[4], 6, $this->mod->tt('Price / Preis'), array('TLRB' => array('width' => 0.1)), 0, 'C', 0);
        $this->setX($posx[5] - $this->offset);
        $this->Cell($widths[5], 6, $this->mod->tt('UVP. / RRP.'), array('TLRB' => array('width' => 0.1)), 0, 'C', 0);

        /*
        if (!empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']) && $this->is_toc == false)
        {
            $this->SetTopMargin(($this->logo_height + 6 + 20));
            $this->first_start_y = ($this->logo_height + 6 + 20);
        }
        else
            $this->SetY($this->GetY() + 7);
         * 
         */
    }

    public function addCategoryLineMark($categname, $prefix = "")
    {
        $this->Bookmark($prefix . parent::hideCategoryPosition($categname), 0, 0);
    }

    
    /**
     * Prepares and renders product line
     * @param type $product product object
     */
    public function getProductLineUPC($product)
    {
        if ($this->GetY()<11)
        {
            $this->setY(13);
        }
        
        if ($this->combinationsinc == 1)
            $this->setY($this->GetY() + 4);

        $quantity = $product->quantity;
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            $quantity = StockAvailable::getQuantityAvailableByProduct($product->id, 0);

        if (isset($this->context))
        {
            $cookie = $this->context->cookie;
        }
        else
        {
            global $cookie;
        }
        
        $infos_product = array(
            "id" => $product->id,
            "name" => $product->name[$this->id_lang],
            "description_short" => $product->description_short[$this->id_lang],
            "sku" => $product->supplier_reference,
            "upc" => $product->upc,
            "quantity" => $quantity,
            "vatexc" => $this->getPrice($product->id, 0, 0),
            "vatinc" => $this->getPrice($product->id, 0, 1),
            'colors' => $product->available_now[$cookie->id_lang],
            'size' => $product->available_later[$cookie->id_lang],
        );
        
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            $infos_product["link"] = $product->getLink();
        else
            $infos_product["link"] = SCPDFCatalog::getHttpHost() . __PS_BASE_URI__ . 'product.php?id_product=' . (int) ($product->id);

        $has_conbination = false;
        if ($this->combinationsinc == 1)
        {

            $combinations_fields = array(
                'sku' => array(
                    'label' => $this->mod->tt('SKU'),
                    'width' => 20,
                    'align' => 'left',
                    'fields' => array(
                        'supplier_reference'
                    )
                ),
                'name' => array(
                    'label' => $this->mod->tt('Name'),
                    'width' => 80 - $this->offset,
                    'align' => 'left',
                    'fields' => array(
                        'group_name',
                        'attribute_name')
                ),
                'upc' => array(
                    'label' => $this->mod->tt('UPC'),
                    'width' => 42,
                    'align' => 'center',
                    'fields' => array('upc')
                ),
                'quantity' => array(
                    'label' => $this->mod->tt('Stock'),
                    'width' => 20,
                    'align' => 'center',
                    'fields' => array('quantity')),
                'price' => array(
                    'width' => 20,
                    'align' => 'right',
                    'fields' => array('price')),
                'id_product_attribute' => 'id_product_attribute'
            );
            $combinations = $this->hasCombinations($product, $combinations_fields);
            if (!empty($combinations) && count($combinations) > 0)
                $has_conbination = true;
        }

        $this->_addLineWithLayout($infos_product, $product, false, $has_conbination);

        if ($this->combinationsinc == 1 && !empty($combinations) && $combinations > 0)
        {
            $unique_combinations = array();
            foreach ($combinations as $group)
            {
                $name = "";
                $reference = "";
                $upc = "";
                $quantity = "";
                $vatexc = "";
                $vatinc = "";
                foreach ($group as $attribut)
                {
                    if (!empty($name))
                        $name .= ", ";
                    $name .= $attribut["group_name"] . " " . $attribut["attribute_name"];
                    $id = $attribut['id_product_attribute'];
                    $supplierReference = $attribut["supplier_reference"];
                    $upc = $attribut["upc"];
                    $quantity = $attribut["quantity"];
                    $vatexc = $this->getPrice($product->id, $attribut['id_product_attribute'], 0);
                    $vatinc = $this->getPrice($product->id, $attribut['id_product_attribute'], 1);
                }

                $infos_combination = array(
                    "id" => $product->id,
                    "name" => $name,
                    "sku" => $supplierReference,
                    "upc" => $upc,
                    "quantity" => $quantity,
                    "vatexc" => $vatexc,
                    "vatinc" => $vatinc
                );
                if (!empty($id))
                    $unique_combinations[$id] = $infos_combination;
                //$this->_addLineWithLayout($infos_combination, $product, true);
            }
            foreach ($unique_combinations as $unique_combination)
            {
                $this->_addLineWithLayout($unique_combination, $product, true);
            }

            //$this->setY($this->GetY()+4);
        }
    }

    public function _addLineWithLayout($infos, $product, $is_combination = false, $has_combination = false)
    {
        $offsetBR = 0;
        //if ($this->getY() > (297 - 30))
        if ($this->getY() > (285 - 30))
        {
//            if ($this->getPage()==3)
//            {
//            echo $this->getY();
//            exit;
//            }
            //$this->Ln(14);
            $this->AddPage();
            if ($this->GetY() < 11)
            {
                $this->setY(13);
            }
        }

        $this->start_y = $this->GetY();
//        if ($this->getPage()==3)
//        {
//            echo 'y0: '.$this->getY().' ';
//        }
        
        $posx = array(10, 30, 65, 90, 115, 140);
        $widths = array(20, 35, 25, 25, 25, 25);
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont($this->fontname(), '', 8);

        // Insertion du tableau
       // $product_label = $this->displayProductLabel($infos["name"], $infos["description_short"], $this->title_length, $is_combination, false, true);
        $height = "";
        
        $image = "";
    	if(!$is_combination)
    	{
	    	$coverid = Product::getCover($product->id);
	        if(!$this->PDFCatalogFromAdmin)
	        	$base_url = '../../img/';
	    	else
	       		$base_url = '../img/';
	        if (file_exists(_PS_IMG_DIR_ . 'p/' . $product->id . '-' . $coverid['id_image'] . '-' . $this->imageformatmedium . '.jpg')) {
	        	$image = $base_url . 'p/' . $product->id . '-' . $coverid['id_image'] . '-' . $this->imageformatmedium . '.jpg';
	        } elseif (file_exists(_PS_IMG_DIR_ . 'p/' . $this->getImgFolderStatic($coverid['id_image']) . $coverid['id_image'] . '-' . $this->imageformatmedium . '.jpg')) {
	        	$image = $base_url . 'p/' . $this->getImgFolderStatic($coverid['id_image']) . $coverid['id_image'] . '-' . $this->imageformatmedium . '.jpg';
	        }
	    	if(!empty($image))
	    		$image = '<img src="'.$image.'" width="1.2cm" />';
    	}
//        if (!empty($infos["upc"]))
//            $height = "height: 1.6cm";
        if (!$is_combination && $this->conf['PS_SC_PDFCATALOG_LINKS'])
            $this->writeHTML('<table border="0" cellpadding="5" cellspacing="0"><tr><td style="' . $height . '" width="' . (($widths[0]) / 10) . 'cm">'.$image.'</td>'
                .'</tr></table>', true, false, false, false, '');
        else
            $this->writeHTML('<table border="0" cellpadding="5" cellspacing="0"><tr><td style="' . $height . '" width="' . (($widths[0]) / 10) . 'cm"></td></tr></table>', true, false, false, false, '');

        $after_height = $this->GetY();
//        if ($this->getPage()==3)
//        {
//            echo 'afetr height: '.$after_height.' ';
//        }
        //$height_line = !empty($image)?21.2611 : ($after_height - $this->start_y - 3.5);
        $height_line = ($after_height - $this->start_y - 3.5);
        //echo 'height line: '.$height_line.' ';
        if ($height_line < 0)
        {
            $this->start_y = $this->first_start_y; //+10;
            $height_line = ($after_height - $this->start_y - 3.5);
        }

        // Insertion colone image
        $this->SetXY($posx[0], $this->start_y);
    	$this->Cell($widths[0], $height_line, "", array('LRB' => array('width' => 0.1)), 0, 'L', 0, "", 0, true);
        
        // Insertion colone sku
        $this->setX($posx[1]);
        $this->Cell($widths[1]-$this->offset, $height_line, $infos["sku"], array('LRB' => array('width' => 0.1)), 0, 'C', 0, '', 1);

        // price
        $this->SetXY($posx[2], $this->start_y);
        $this->Cell($widths[2]- $this->offset, $height_line, $infos["colors"], array('LRB' => array('width' => 0.1)), 0, 'C', 0, '', 1);
        
        // recommended price
        $this->SetXY($posx[3] - $this->offset, $this->start_y);
        $this->Cell($widths[3], $height_line, $infos["size"], array('LRB' => array('width' => 0.1)), 0, 'C', 0, '', 1);
        
        // price
        $this->SetXY($posx[4], $this->start_y);
        $this->Cell($widths[4]- $this->offset, $height_line, Tools::displayPrice($infos["vatexc"]), array('LRB' => array('width' => 0.1)), 0, 'C', 0, '', 1);
        
        // recommended price
        $this->SetXY($posx[5] - $this->offset, $this->start_y);
        $this->Cell($widths[5], $height_line, Tools::displayPrice($infos["upc"]), array('LRB' => array('width' => 0.1)), 0, 'C', 0, '', 1);
        
        // Insertion colonne vide
        if ($this->emptycol == 1)
        {
            $this->Cell($widths[4], $height_line, "", 1, 0, 'R', 0);
        }

        $this->setY($after_height - 3.5);
    }

}
