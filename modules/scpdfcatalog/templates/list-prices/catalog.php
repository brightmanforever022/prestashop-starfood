<?php

/*
 * * Format list-prices
 */

require_once(_PS_MODULE_DIR_ . 'scpdfcatalog/sctcpdf/tcpdf.php');
require_once(_PS_MODULE_DIR_ . 'scpdfcatalog/SCPDFCatalogCreator.php');

class catalog extends SCPDFCatalogCreator {

	public $title_length=600;
	public $start_y = 0;
	public $first_start_y = 0;	
	public $offset = 0;
	public $show_col_1 = 0;
	public $show_col_2 = 0;
	
    public function createCatalog($format, $start, $limit, $orderBy, $orderWay) {
        $this->format = $format;
        $active = $this->conf['PS_SC_PDFCATALOG_ACTIVEPRODUCT'];
        
        $offset_nb = 0;
        if($this->pc_pricecolumns_1 == "ttc_ht")
        	$offset_nb+=2;
        else
        	$offset_nb++;
        if($this->pc_pricecolumns_2 == "ttc_ht")
        	$offset_nb+=2;
        else
        	$offset_nb++;
        if($this->emptycol == 1)
        	$offset_nb++;
        
        $this->offset = $offset_nb*20;

        $this->showPageNum = true;
        if (!$this->conf['PS_SC_PDFCATALOG_FIRSTPAGE'])
            $this->Ln(9);
        if(!empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']))
       		$this->AddPage();
        foreach ($this->categories AS $id_category) {
       		$this->header_page=false;
            $this->currentCategory = $id_category;
            $category = new Category($id_category);
            /*if (!$category->active)
                continue;*/
            
           $groups = array_flip($category->getGroups());
           if(isset($groups[(int)$this->pc_usecustomergroup_1]))
           		$this->show_col_1 = 1;
           else
           		$this->show_col_1 = 0;
           if(isset($groups[(int)$this->pc_usecustomergroup_2]))
           		$this->show_col_2 = 1;
           else
           		$this->show_col_2 = 0;
           
            
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
		  
		  $rows = "";
		  
            foreach ($products AS $productrow) {
                $product = new Product((int)($productrow['id_product']));
                /*if (!$product->active)
                    continue;*/
                if (Validate::isLoadedObject($product)) {
                    $this->getProductLinePhoto($product, 'small');
                } else {
                    die('Invalid product');
                }
            }
            /*$tbl = '<table cellspacing="0" cellpadding="5" border="1" width="700">'.$rows.'</table>';
            $this->writeHTML($tbl, true, false, false, false, '');*/
        }
    }

    public function addCategoryLine($catname) {
		
		$posx = array(10, 30);
        $widths = array(20, 170, 20);
		$this->SetFillColor(240, 240, 240);
		$this->SetTextColor(0, 0, 0);
		$this->SetFont($this->fontname(), 'B', 8);
		//$this->Ln(7);
		$this->setX($posx[0]);
		$this->Cell($widths[0]+$widths[1], 6, $catname, 0, 0, 'L', 0);
		
		$this->SetY($this->GetY()+6);
		
    	if(empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']))
    		$this->first_start_y = ($this->logo_height+6+20);
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

        $posx = array(10, 30);
        $widths = array(20, 170, 20);
        $height = 6;
        if(!empty($this->pc_collibelle_1) || !empty($this->pc_collibelle_2))
       		$height = 12;
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont($this->fontname(), 'B', 8);
        $this->Ln(7);
        $this->setX($posx[0]);
        $this->Cell($widths[0], $height, $this->mod->tt('Ref.'), 1, 0, 'L', 0);
        $this->setX($posx[1]);
        $this->Cell($widths[1]-$this->offset, $height,"*". $this->mod->tt('Product'), 1, 0, 'L', 0);
            
        	$pos_start = $posx[1]+$widths[1]-$this->offset;
        	$y_start = $this->GetY();
        	$y_start_middle = $y_start+($height/2);
        
        	$prefix = "";
        	if(!empty($this->pc_collibelle_1))
        		$prefix = $this->pc_collibelle_1;
            if($this->pc_pricecolumns_1 == "ttc_ht")
            {
            	if(!empty($prefix))
            	{
            		$this->Cell($widths[2], $height, "", 1, 0, 'C', 0, '', 1);
            		$this->setX($pos_start);
            		$this->Cell($widths[2], $height/2, $prefix, 0, 0, 'C', 0, '', 1);
            		$this->setXY($pos_start, $y_start_middle);
            		$this->Cell($widths[2], $height/2, $this->mod->tt('Price Exc. Tax'), 0, 0, 'C', 0, '', 1);
            		$this->setXY($pos_start+$widths[2],$y_start);
            		
            		$this->Cell($widths[2], $height, "", 1, 0, 'C', 0, '', 1);
            		$this->setX($pos_start+$widths[2]);
            		$this->Cell($widths[2], $height/2, $prefix, 0, 0, 'C', 0, '', 1);
            		$this->setXY($pos_start+$widths[2], $y_start_middle);
            		$this->Cell($widths[2], $height/2, $this->mod->tt('Price Inc. Tax'), 0, 0, 'C', 0, '', 1);
            		$this->setY($y_start);
            		
            		$this->setX($pos_start+($widths[2]*2));
            	}
            	else
            	{
            		$this->Cell($widths[2], $height, $this->mod->tt('Price Exc. Tax'), 1, 0, 'C', 0, '', 1);
            		$this->Cell($widths[2], $height, $this->mod->tt('Price Inc. Tax'), 1, 0, 'C', 0, '', 1);
            	}
            }
            elseif($this->pc_pricecolumns_1 == "ht")
            {
            	if(!empty($prefix))
            	{
            		$this->Cell($widths[2], $height, "", 1, 0, 'C', 0, '', 1);
            		$this->setX($pos_start);
            		$this->Cell($widths[2], $height/2, $prefix, 0, 0, 'C', 0, '', 1);
            		$this->setXY($pos_start, $y_start_middle);
            		$this->Cell($widths[2], $height/2, $this->mod->tt('Price Exc. Tax'), 0, 0, 'C', 0, '', 1);
            		$this->setXY($pos_start+$widths[2],$y_start);
            	}
            	else
            		$this->Cell($widths[2], $height, $this->mod->tt('Price Exc. Tax'), 1, 0, 'C', 0, '', 1);
            }
            elseif($this->pc_pricecolumns_1 == "ttc")
            {
            	if(!empty($prefix))
            	{
            		$this->Cell($widths[2], $height, "", 1, 0, 'C', 0, '', 1);
            		$this->setX($pos_start);
            		$this->Cell($widths[2], $height/2, $prefix, 0, 0, 'C', 0, '', 1);
            		$this->setXY($pos_start, $y_start_middle);
            		$this->Cell($widths[2], $height/2, $this->mod->tt('Price Inc. Tax'), 0, 0, 'C', 0, '', 1);
            		$this->setXY($pos_start+$widths[2],$y_start);
            	}
            	else
            		$this->Cell($widths[2], $height, $this->mod->tt('Price Inc. Tax'), 1, 0, 'C', 0, '', 1);
            }

            $prefix = "";
            if(!empty($this->pc_collibelle_2))
            	$prefix = $this->pc_collibelle_2;
            $pos_start = $posx[1]+$widths[1]-$this->offset;
            if($this->pc_pricecolumns_1 == "ttc_ht")
            	$pos_start+=($widths[2]*2);
            else
            	$pos_start+=($widths[2]);
    		if($this->pc_pricecolumns_2 == "ttc_ht")
            {
            	if(!empty($prefix))
            	{
            		$this->Cell($widths[2], $height, "", 1, 0, 'C', 0, '', 1);
            		$this->setX($pos_start);
            		$this->Cell($widths[2], $height/2, $prefix, 0, 0, 'C', 0, '', 1);
            		$this->setXY($pos_start, $y_start_middle);
            		$this->Cell($widths[2], $height/2, $this->mod->tt('Price Exc. Tax'), 0, 0, 'C', 0, '', 1);
            		$this->setXY($pos_start+$widths[2],$y_start);
            		
            		$this->Cell($widths[2], $height, "", 1, 0, 'C', 0, '', 1);
            		$this->setX($pos_start+$widths[2]);
            		$this->Cell($widths[2], $height/2, $prefix, 0, 0, 'C', 0, '', 1);
            		$this->setXY($pos_start+$widths[2], $y_start_middle);
            		$this->Cell($widths[2], $height/2, $this->mod->tt('Price Inc. Tax'), 0, 0, 'C', 0, '', 1);
            		$this->setY($y_start);
            		
            		$this->setX($pos_start+($widths[2]*2));
            	}
            	else
            	{
            		$this->Cell($widths[2], $height, $this->mod->tt('Price Exc. Tax'), 1, 0, 'C', 0, '', 1);
            		$this->Cell($widths[2], $height, $this->mod->tt('Price Inc. Tax'), 1, 0, 'C', 0, '', 1);
            	}
            }
            elseif($this->pc_pricecolumns_2 == "ht")
            {
            	if(!empty($prefix))
            	{
            		$this->Cell($widths[2], $height, "", 1, 0, 'C', 0, '', 1);
            		$this->setX($pos_start);
            		$this->Cell($widths[2], $height/2, $prefix, 0, 0, 'C', 0, '', 1);
            		$this->setXY($pos_start, $y_start_middle);
            		$this->Cell($widths[2], $height/2, $this->mod->tt('Price Exc. Tax'), 0, 0, 'C', 0, '', 1);
            		$this->setXY($pos_start+$widths[2],$y_start);
            	}
            	else
            		$this->Cell($widths[2], $height, $this->mod->tt('Price Exc. Tax'), 1, 0, 'C', 0, '', 1);
            }
            elseif($this->pc_pricecolumns_2 == "ttc")
            {
            	if(!empty($prefix))
            	{
            		$this->Cell($widths[2], $height, "", 1, 0, 'C', 0, '', 1);
            		$this->setX($pos_start);
            		$this->Cell($widths[2], $height/2, $prefix, 0, 0, 'C', 0, '', 1);
            		$this->setXY($pos_start, $y_start_middle);
            		$this->Cell($widths[2], $height/2, $this->mod->tt('Price Inc. Tax'), 0, 0, 'C', 0, '', 1);
            		$this->setXY($pos_start+$widths[2],$y_start);
            	}
            	else
            		$this->Cell($widths[2], $height, $this->mod->tt('Price Inc. Tax'), 1, 0, 'C', 0, '', 1);
            }
            
            if($this->emptycol == 1)
            {
            	$this->Cell($widths[2], $height, $this->emptycollibelle, 1, 0, 'C', 0, '', 1);
            }


		if(!empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']) && $this->is_toc == false)
        {
	    	$this->SetTopMargin(($this->logo_height+$height+20));
			$this->first_start_y = ($this->logo_height+$height+20);
        }
        else
        	$this->SetY($this->GetY()+$height+1);
    }

    public function getProductLinePhoto($product, $theme = 'big') {
    	
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
			"vatexc_1"=>($this->show_col_1?$this->getPriceForGroup($product->id,0,0, "1"):""),
			"vatinc_1"=>($this->show_col_1?$this->getPriceForGroup($product->id,0,1, "1"):""),
			"vatexc_2"=>($this->show_col_2?$this->getPriceForGroup($product->id,0,0, "2"):""),
			"vatinc_2"=>($this->show_col_2?$this->getPriceForGroup($product->id,0,1, "2"):"")
		);
    	
    	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    		$infos_product["link"] = $product->getLink();
    	else
    		$infos_product["link"] = SCPDFCatalog::getHttpHost() . __PS_BASE_URI__ . 'product.php?id_product=' . (int)($product->id);
	
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
                        'width' => 120 - $this->offset,
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
			/*echo "<pre>";
			print_r($combinations);
			echo "</pre>";*/
			foreach ($combinations as $group)
			{
				$name = "";
				$reference = "";
				$ean13 = "";
				$quantity = "";
				$vatexc_1 = "";
				$vatinc_1 = "";
				$vatexc_2 = "";
				$vatinc_2 = "";
				$id = "";
				foreach ($group as $attribut)
				{
					if(!empty($name))
						$name .= ", ";
					$id = $attribut['id_product_attribute'];
					$name .= $attribut["group_name"]." ".$attribut["attribute_name"];
					$reference = $attribut["reference"];
					$vatexc_1 = $this->getPriceForGroup($product->id, $attribut['id_product_attribute'],0, $this->pc_usecustomergroup_1);
					$vatinc_1 = $this->getPriceForGroup($product->id, $attribut['id_product_attribute'],1, $this->pc_usecustomergroup_1);
					$vatexc_2 = $this->getPriceForGroup($product->id, $attribut['id_product_attribute'],0, $this->pc_usecustomergroup_2);
					$vatinc_2 = $this->getPriceForGroup($product->id, $attribut['id_product_attribute'],1, $this->pc_usecustomergroup_2);
				}
				
				$infos_combination = array(
						"id"=>$product->id,
						"name"=>$name,
						"description_short"=>$product->description_short[$this->id_lang],
						"reference"=>$reference,
						"quantity"=>$quantity,
						"vatexc_1"=>$vatexc_1,
						"vatinc_1"=>$vatinc_1,
						"vatex_2"=>$vatexc_2,
						"vatinc_2"=>$vatinc_2
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
           // $this->SetY(297+($this->logo_height+5+5));
           $this->AddPage();
    	}
    	/*elseif($this->GetY()==0)
    	{
    		$this->SetY($this->logo_height+5+5);
    	}*/
    	
    	/*if($product->id=="123")
    	{echo 297+($this->logo_height+5+5);die();}*/
    	$this->start_y = $this->GetY();
        $posx = array(10, 34, 50);
        $widths = array(20, 20, 150, 20);
        
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
	    	if($this->conf['PS_SC_PDFCATALOG_LINKS'])
	    	{
	    		$this->writeHTML('<table border="0.5" cellpadding="5" cellspacing="0"><tr><td width="'.(($widths[0])/10).'cm"></td><td width="'.(($widths[1])/10).'cm" align="center">'.$image.'</td><td width="'.(($widths[2]-$this->offset)/10).'cm"><a href="'.$infos["link"].'" style="color: #000000; text-decoration: none;">'.$product_label.'</a></td></tr></table>', true, false, false, false, '');
	    	}
	    	else
	    		$this->writeHTML('<table border="0.5" cellpadding="5" cellspacing="0"><tr><td width="'.(($widths[0])/10).'cm"></td><td width="'.(($widths[1])/10).'cm" align="center">'.$image.'</td><td width="'.(($widths[2]-$this->offset)/10).'cm">'.$product_label.'</td></tr></table>', true, false, false, false, '');
    	}
    	else
    	{
    		$this->writeHTML('<table border="0.5" cellpadding="5" cellspacing="0"><tr><td width="'.(($widths[0])/10).'cm"></td><td colspan="2" width="'.(($widths[1]+($widths[2]-$this->offset))/10).'cm">'.$product_label.'</td></tr></table>', true, false, false, false, '');
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
    
    	// Insertion colones prix
    	if (!$has_combination || $is_combination) 
    	{
    		if(strpos($this->pc_pricecolumns_1, "ht")!==false)
    		{
    			$this->Cell($widths[3], $height_line, $infos["vatexc_1"], 1, 0, 'R', 0, '', 1);
    		}
    		if(strpos($this->pc_pricecolumns_1, "ttc")!==false)
    		{
    			$this->Cell($widths[3], $height_line,  $infos["vatinc_1"], 1, 0, 'R', 0, '', 1);
    		}

    		if(strpos($this->pc_pricecolumns_2, "ht")!==false)
    		{
    			$this->Cell($widths[3], $height_line, $infos["vatexc_2"], 1, 0, 'R', 0, '', 1);
    		}
    		if(strpos($this->pc_pricecolumns_2, "ttc")!==false)
    		{
    			$this->Cell($widths[3], $height_line,  $infos["vatinc_2"], 1, 0, 'R', 0, '', 1);
    		}
    	}
    	else
    	{
    		if($this->pc_pricecolumns_1 == "ttc_ht")
    		{
    			$this->Cell($widths[3], $height_line, "", 1, 0, 'R', 1, '', 1);
    			$this->Cell($widths[3], $height_line, "", 1, 0, 'R', 1);
    		}
    		else
    			$this->Cell($widths[3], $height_line, "", 1, 0, 'R', 1, '', 1);

    		if($this->pc_pricecolumns_2 == "ttc_ht")
    		{
    			$this->Cell($widths[3], $height_line, "", 1, 0, 'R', 1, '', 1);
    			$this->Cell($widths[3], $height_line, "", 1, 0, 'R', 1, '', 1);
    		}
    		else
    			$this->Cell($widths[3], $height_line, "", 1, 0, 'R', 1, '', 1);
    	}
    	
    	// Insertion colonne vide
    	if($this->emptycol == 1)
    	{
    		$this->Cell($widths[3], $height_line, "", 1, 0, 'R', 0);
    	}
    	
    	$this->setY($after_height-2.5);
    }
    

    public function getPriceForGroup($id_product, $id_product_attribute = null, $usetax = true,$id_col=1) 
    {
    	if($id_col==2)
    	{
    		$id_group = $this->pc_usecustomergroup_2;
    		$currency = new Currency((int)($this->pc_currency_2));
    	}
    	else
    	{
    		$id_group = $this->pc_usecustomergroup_1;
    		$currency = new Currency((int)($this->pc_currency_1));
    	}
    	$currency->sign = $currency->iso_code;
    	
    	$with_ecotax = true;
    	$usereduc = true; 
    	$quantity = 1;
    	$decimals = 2;
    	$divisor = null;
    	$only_reduc = false;
    	$force_associated_tax = false;
    	$id_cart = null;
    	$id_address = null;
    	$specific_price_output = null;
    	$use_group_reduction = true;
    	$context = null;
    	$use_customer_price = true;
    	// get id_group for versions >= 1.3
    	if (version_compare(_PS_VERSION_, '1.3.0.0', '>=')) {
    		if (!isset($this->idCustomerFromGroup[$id_group]))
    			$this->idCustomerFromGroup[$id_group] = (int)(Db::getInstance()->getValue('SELECT id_customer FROM `' . _DB_PREFIX_ . 'customer` WHERE id_default_group=' . (int) $id_group));
    		$id_customer = $this->idCustomerFromGroup[$id_group];
    	}
    	// get price
    	/*
    	 1.5
    	public static function getPriceStatic($id_product, $usetax = true, $id_product_attribute = null, $decimals = 6, $divisor = null,
    			$only_reduc = false, $usereduc = true, $quantity = 1, $force_associated_tax = false, $id_customer = null, $id_cart = null,
    			$id_address = null, &$specific_price_output = null, $with_ecotax = true, $use_group_reduction = true, Context $context = null,
    			$use_customer_price = true)
    	1.4
    	public static function getPriceStatic($id_product, $usetax = true, $id_product_attribute = null, $decimals = 6, $divisor = null,
    			$only_reduc = false, $usereduc = true, $quantity = 1, $forceAssociatedTax = false, $id_customer = null, $id_cart = null,
    			$id_address = null, &$specificPriceOutput = null, $with_ecotax = true, $use_groupReduction = true)
    	1.3
    	public static function getPriceStatic($id_product, $usetax = true, $id_product_attribute = NULL, $decimals = 6, $divisor = NULL,
    			$only_reduc = false, $usereduc = true, $quantity = 1, $forceAssociatedTax = false, $id_customer = NULL, $id_cart = NULL,
    	$id_address_delivery = NULL)
    	1.2
    	public static function getPriceStatic($id_product, $usetax = true, $id_product_attribute = NULL, $decimals = 6, $divisor = NULL,
    	$only_reduc = false, $usereduc = true, $quantity = 1, $forceAssociatedTax = false)
    	1.1
    	public static function getPriceStatic($id_product, $usetax = true, $id_product_attribute = NULL, $decimals = 6, $divisor = NULL,
    	$only_reduc = false, $usereduc = true, $quantity = 1)
    	*/
    	if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
    		$price = self::getPriceStatic_1_5($id_product, $usetax, $id_product_attribute, $decimals, $divisor, $only_reduc, $usereduc, $quantity, $force_associated_tax, $id_customer, $id_cart, $id_address, $specific_price_output, $usetax, $use_group_reduction, $context, $use_customer_price);
    	} elseif (version_compare(_PS_VERSION_, '1.4.0.0', '>=')) {
    		$price = Product::getPriceStatic($id_product, $usetax, $id_product_attribute, $decimals, $divisor, $only_reduc, $usereduc, $quantity, $force_associated_tax, $id_customer, $id_cart, $id_address, $specific_price_output, $usetax, $use_group_reduction);
    	} elseif (version_compare(_PS_VERSION_, '1.3.0.0', '>=')) {
    		$price = Product::getPriceStatic($id_product, $usetax, $id_product_attribute, $decimals, $divisor, $only_reduc, $usereduc, $quantity, $force_associated_tax, $id_customer, $id_cart, $id_address);
    	} elseif (version_compare(_PS_VERSION_, '1.2.0.0', '>=')) {
    		$price = Product::getPriceStatic($id_product, $usetax, $id_product_attribute, $decimals, $divisor, $only_reduc, $usereduc, $quantity, $force_associated_tax);
    	} elseif (version_compare(_PS_VERSION_, '1.1.0.0', '>=')) {
    		$price = Product::getPriceStatic($id_product, $usetax, $id_product_attribute, $decimals, $divisor, $only_reduc, $usereduc, $quantity);
    	}
    	
    	$return = $price * $currency->conversion_rate;
    	$return = number_format($return, (!empty($this->conf['PS_SC_PDFCATALOG_HIDE_DECIMALS'])?0:$decimals), $this->conf['PS_SC_PDFCATALOG_DECIMALS_SEP'], $this->conf['PS_SC_PDFCATALOG_THOUSANDS_SEP']);
    	if(empty($this->conf['PS_SC_PDFCATALOG_HIDE_CURRENCY']))
    		$return = $this->convertSign($currency->getSign('left')).$return.$this->convertSign($currency->getSign('right'));

    	return $return;
    }
}