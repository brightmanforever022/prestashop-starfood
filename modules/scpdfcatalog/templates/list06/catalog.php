<?php

/*
 * * Format list06
 */

require_once(_PS_MODULE_DIR_ . 'scpdfcatalog/sctcpdf/tcpdf.php');
require_once(_PS_MODULE_DIR_ . 'scpdfcatalog/SCPDFCatalogCreator.php');

class catalog extends SCPDFCatalogCreator {

	public $title_length=600;
	public $start_y = 0;
	public $first_start_y = 0;
	public $offset = 0;
	
    public function createCatalog($format, $start, $limit, $orderBy, $orderWay) {
        $this->format = $format;
        $active = $this->conf['PS_SC_PDFCATALOG_ACTIVEPRODUCT'];
        
        $offset_nb = 0;
        if($this->vatexc == 1)
        	$offset_nb++;
        if($this->vatinc == 1)
        	$offset_nb++;
        if($this->emptycol == 1)
        	$offset_nb++;
        
        if($offset_nb==0)
        	$offset = -20;
        elseif($offset_nb==1)
        	$offset = 0;
        elseif($offset_nb==2)
        	$offset = 20;
        elseif($offset_nb==3)
       		$offset = 40;
        $this->offset = $offset;

        $this->showPageNum = true;
        if (!$this->conf['PS_SC_PDFCATALOG_FIRSTPAGE'])
            $this->Ln(9);
        if(!empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']))
       		$this->AddPage();
        foreach ($this->categories AS $id_category) {
       		$this->header_page=false;
            $this->currentCategory = $id_category;
            $category = new Category($id_category);
           /* if (!$category->active)
                continue;*/
            
		  $products = Product::getProducts($this->id_lang, $start, $limit, $orderBy, $orderWay, $id_category, $active);
			$products = $this->filtersByStockAndManufacturer($products);
        	if (count($products) > 0)
			{
				if ($this->conf['PS_SC_PDFCATALOG_CATEGCOVER'])
					$this->addCategoryCoverPage(parent::hideCategoryPosition($category->getName($this->id_lang)), $category->description[$this->id_lang], $id_category);
				else
				{
					$idx = 0;
					if(empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']))
					{
       				 	$this->header_page=true;
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
					if(empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']))
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
            foreach ($products AS $productrow) {
                $product = new Product((int)($productrow['id_product']));
                /*if (!$product->active)
                    continue;*/
                if (!isset($this->categ_names[$id_category])) {
                    if (count($this->categ_names) > 0) {
                       // $this->AddPage();
                    }
                    $category = new Category($id_category);
                    $this->categ_names[$id_category] = parent::hideCategoryPosition($category->getName($this->id_lang));
                    if (!array_key_exists($category->id_parent, $this->categ_names)) {
                        $pcategory = new Category($category->id_parent);
                        $this->categ_names[$category->id_parent] = parent::hideCategoryPosition($pcategory->getName($this->id_lang));
                    }
                    $this->addCategoryLinePhoto($category->getName($this->id_lang), ($category->id_parent != 1 ? $this->categ_names[$category->id_parent] . ' > ' : ''));
                }
                if (Validate::isLoadedObject($product)) {
                    $this->getProductLinePhoto($product);
                } else {
                    die('Invalid product');
                }
            }
        }
    }

    public function addCategoryLine($catname) {
		$this->first_start_y = $this->GetY();
		
		$posx = array(10, 30, 140, 160, 180, 200);
		$widths = array(20, 110, 20, 20, 20, 20);
		$this->SetFillColor(240, 240, 240);
		$this->SetTextColor(0, 0, 0);
		$this->SetFont($this->fontname(), 'B', 8);
		//$this->Ln(7);
		$this->setX($posx[0]);
		$this->Cell($widths[0]+$widths[1], 6, $catname, 0, 0, 'L', 0);
		
		$this->SetY($this->GetY()+6);
    }

     public function Header() {
    	parent::Header();
    	if (!$this->conf['PS_SC_PDFCATALOG_FIRSTPAGE'] || ($this->conf['PS_SC_PDFCATALOG_FIRSTPAGE'] && $this->getPage() != 1 )) 
    	{
    		if(!empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']) && $this->is_toc == false)
    		{
    			$this->setX($this->logo_height+3);
    			$this->addLibelleLine();
    		}
    	}
    }

    public function addLibelleLine() {
		$this->first_start_y = $this->GetY();
       
		$posx = array(10, 30, 160, 180, 200, 220);
        $widths = array(20, 130, 20, 20, 20);
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont($this->fontname(), 'B', 8);
        $this->Ln(7);
        $this->setX($posx[0]);
        $this->Cell($widths[0], 6, $this->mod->tt('Ref.'), 1, 0, 'L', 0);
        $this->setX($posx[1]);
        $this->Cell($widths[1] - $this->offset, 6, $this->mod->tt('Product'), 1, 0, 'L', 0);
        
            $this->setX($posx[2] - $this->offset);
            $this->Cell($widths[2], 6, $this->mod->tt('Stock'), 1, 0, 'C', 0);
            $this->setX($posx[3] - $this->offset);
            if ($this->vatexc == 1)
                $this->Cell($widths[3], 6, $this->mod->tt('Price Exc. Tax'), 1, 0, 'C', 0);

            if ($this->vatinc == 1)
            {
                if ($this->vatexc == 1)
                    $posCell = $posx[4] - $this->offset;
                else
                    $posCell = $posx[3] - $this->offset;

                $this->SetX($posCell);
                $this->Cell($widths[3], 6, $this->mod->tt('Price Inc. Tax'), 1, 0, 'C', 0);
    		}
    		
    	if($this->emptycol == 1)
    	{
    		$this->Cell($widths[3], 6, $this->emptycollibelle, 1, 0, 'C', 0, '', 1);
    	}
            
		if(!empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']) && $this->is_toc == false)
        {
	    	$this->SetTopMargin(($this->logo_height+6+20));
			$this->first_start_y = ($this->logo_height+6+20);
        }
        else
        	$this->SetY($this->GetY()+7);
    }

    public function addCategoryLinePhoto($categname, $prefix = "", $theme = 'big') {
         $this->Bookmark($prefix . parent::hideCategoryPosition($categname), 0, 0);
    }

    public function getProductLinePhoto($product, $theme = 'big') 
    {
        
    	if($this->combinationsinc == 1)
    		$this->setY($this->GetY()+4);
    	
   		$quantity = $product->quantity;
    	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    		$quantity = StockAvailable::getQuantityAvailableByProduct($product->id,0);
    	 
    	$infos_product = array(
			"id"=>$product->id,
			"name"=>$product->name[$this->id_lang],
			"description_short"=>$product->description_short[$this->id_lang],
			"reference"=>$product->reference,
			"quantity"=>$quantity,
			"weight"=>$product->weight,
			"vatexc"=>$this->getPrice($product->id,0,0),
			"vatinc"=>$this->getPrice($product->id,0,1)
		);

    	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    		$infos_product["link"] = $product->getLink();
    	else
    		$infos_product["link"] = SCPDFCatalog::getHttpHost(). __PS_BASE_URI__ . 'product.php?id_product=' . (int)($product->id);
	
		$has_conbination = false;
		if ($this->combinationsinc == 1) {
			
			$combinations_fields = array(
                    'reference' => array(
                        'label' => $this->mod->tt('Reference'),
                        'width' => 20,
                        'align' => 'left',
                        'fields' => array(
                            'reference'
                        )
                    ),
                    'name' => array(
                        'label' => $this->mod->tt('Name'),
                        'width' => 110 - $this->offset,
                        'align' => 'left',
                        'fields' => array(
                            'group_name',
                            'attribute_name')
                    ),
                    'image' => array(
                        'label' => '',
                        'width' => 42,
                        'align' => 'center',
                        'fields' => array('image')
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
			if(!empty($combinations) && count($combinations)>0)
				$has_conbination = true;
		}
		
		$this->_addLineWithLayout($infos_product, $product, false, $has_conbination);
		
		if ($this->combinationsinc == 1 && !empty($combinations) && $combinations>0) {
			$unique_combinations = array();
			foreach ($combinations as $group)
			{
				$name = "";
				$reference = "";
				$quantity = "";
				$weight = "";
				$vatexc = "";
				$vatinc = "";
				$id = "";
				$i = 0;
				foreach ($group as $attribut)
				{
					if(!empty($name))
						$name .= ", ";
					$name .= $attribut["group_name"]." ".$attribut["attribute_name"];
					
					if($i==0)
					{
						$id = $attribut['id_product_attribute'];
						$reference = $attribut["reference"];
						$vatexc = $this->getPrice($product->id, $attribut['id_product_attribute'],0);
						$vatinc = $this->getPrice($product->id, $attribut['id_product_attribute'],1);
	    	
				   		$quantity = $product->quantity;
				    	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				    		$quantity = StockAvailable::getQuantityAvailableByProduct($product->id,$attribut['id_product_attribute']);
					}
			    	$i++;
				}
				
				$infos_combination = array(
						"id"=>$product->id,
						"name"=>$name,
						"description_short"=>$product->description_short[$this->id_lang],
						"reference"=>$reference,
						"quantity"=>$quantity,
						"weight"=>$weight,
						"vatexc"=>$vatexc,
						"vatinc"=>$vatinc
				);
				if(!empty($id))
					$unique_combinations[$id] = $infos_combination;
				//$this->_addLineWithLayout($infos_combination, $product, true);
			}
			foreach($unique_combinations as $unique_combination)
			{
				$this->_addLineWithLayout($unique_combination, $product, true);
			}
			
			//$this->setY($this->GetY()+4);
		}
    }
    


    public function _addLineWithLayout($infos, $product, $is_combination=false, $has_combination=false)
    {
    	$offsetBR = 8;
    	//if ($this->getY() > (297 - 30))
    	if ($this->getY() > (280- 30)) 
    	{
            $this->Ln(23);
        }

		$this->start_y = $this->GetY();    	
    	$posx = array(10, 34, 50, 160, 180, 200);
    	$widths = array(20, 20, 110, 20, 20);
    	$this->SetFillColor(240, 240, 240);
    	$this->SetTextColor(0, 0, 0);
    	$this->SetFont($this->fontname(), '', 8);
        
    		
    	// Insertion du tableau
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
    	$product_label = $this->displayProductLabel($infos["name"], $infos["description_short"],$this->title_length,$is_combination, false, true);
    	if(!$is_combination)
    	{
	    	if($this->conf['PS_SC_PDFCATALOG_LINKS'] || (!$is_combination && $this->conf['PS_SC_PDFCATALOG_LINKS']))
	    		$this->writeHTML('<table border="0.5" cellpadding="5" cellspacing="0"><tr><td width="'.(($widths[0])/10).'cm"></td><td width="'.(($widths[1])/10).'cm" align="center">'.$image.'</td><td width="'.(($widths[2]-$this->offset)/10).'cm"><a href="'.$infos["link"].'" style="color: #000000; text-decoration: none;">'.$product_label.'</a></td></tr></table>', true, false, false, false, '');
	    	else
	    		$this->writeHTML('<table border="0.5" cellpadding="5" cellspacing="0"><tr><td width="'.(($widths[0])/10).'cm"></td><td width="'.(($widths[1])/10).'cm" align="center">'.$image.'</td><td width="'.(($widths[2]-$this->offset)/10).'cm">'.$product_label.'</td></tr></table>', true, false, false, false, '');
    	}
    	else
    	{
    		$this->writeHTML('<table border="0.5" cellpadding="5" cellspacing="0"><tr><td width="'.(($widths[0])/10).'cm"></td><td colspan="2" width="'.(($widths[2]-$this->offset+$widths[1])/10).'cm">'.$product_label.'</td></tr></table>', true, false, false, false, '');
    	}
	    $after_height = $this->GetY();
    	$height_line = ($after_height-$this->start_y-3.5);
    	if($height_line<0)
    	{
    		$this->start_y = $this->first_start_y;//+10;
    		$height_line = ($after_height-$this->start_y-3.5);
    	}
    		
    	// Insertion colone ref
    	$this->SetXY($posx[0], $this->start_y);
    	$this->Cell($widths[0],$height_line, $infos["reference"], 0, 0, 'C', 0, '', 1);
    	
    	// Insertion colone image
    	$this->Cell($widths[1]-$this->offset ,$height_line, "", 0, 0, 'L', 0, "", 0, true);
    	
    	
    	// Insertion colone nom
    	$this->setX($posx[2]);
    	$this->Cell($widths[2]-$this->offset ,$height_line, "", 0, 0, 'L', 0, "", 0, true);

    	// Insertion colone quantity
    	$this->setX($posx[3]-$this->offset);
		$this->Cell($widths[3], $height_line, $infos["quantity"], 1, 0, 'C', 0);
    
    	// Insertion colones prix
    	if (!$has_combination || $is_combination) {
    	 	if ($this->vatexc == 1) {
                   $this->Cell($widths[4], $height_line, $infos["vatexc"], 1, 0, 'R', 0, '', 1);
                }
                if ($this->vatinc == 1) {
                    if ($this->vatexc == 1)
                        $posCell = $posx[5] - $this->offset;
                    else
                        $posCell = $posx[4] - $this->offset;

                    $this->SetX($posCell);
                   $this->Cell($widths[4], $height_line,  $infos["vatinc"], 1, 0, 'R', 0, '', 1);
                } 
    	}
    	else
    	{
    		if ($this->vatexc == 1 && $this->vatinc == 1)
    			$this->Cell($widths[4]+$widths[4], $height_line, "", 1, 0, 'R', 1);
    		elseif ($this->vatexc == 1 || $this->vatinc == 1)
    			$this->Cell($widths[4], $height_line, "", 1, 0, 'R', 1);
    	}
    	
    	// Insertion colonne vide
    	if($this->emptycol == 1)
    	{
    		$this->Cell($widths[4], $height_line, "", 1, 0, 'R', 0);
    	}
    
    	$this->setY($after_height-2.5);
    }

}