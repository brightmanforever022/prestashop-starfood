<?php

/*
** Format 2x2
*/

require_once(_PS_MODULE_DIR_.'scpdfcatalog/sctcpdf/tcpdf.php');
require_once(_PS_MODULE_DIR_.'scpdfcatalog/SCPDFCatalogCreator.php');

class catalog extends SCPDFCatalogCreator
{
    public $last_y1=0;
    public $last_y2=0;
	 
	public $cell_width = 80;
	public $title_length=170;
	 
	public function createCatalog($format, $start, $limit, $orderBy, $orderWay)
	{
		$image_height = 60;
		$image_width = 60;
		
		
		$this->format = $format;
        $active = $this->conf['PS_SC_PDFCATALOG_ACTIVEPRODUCT'];
		$this->showPageNum = true;
		$posx = array(27,122,27,122);
		$posy = array(50,50,180,180);
		$idx = 0;
        if(!empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']))
       		$this->AddPage();
		foreach($this->categories AS $id_category){
        	$this->header_page=false;
			$this->currentCategory = $id_category;
			$category = new Category($id_category);
			//if (!$category->active) continue;
			
			$products = Product::getProductsAndCombinations($this->id_lang, $start, $limit, $orderBy, $orderWay, $id_category,$active, 
                            $this->conf['dontShowFakeCombinations'], $this->conf['PS_SC_PDFCATALOG_WITHSTOCKPRODUCT']);
			$products = $this->filtersByStockAndManufacturer($products);
			if (count($products) > 0)
			{
				if ($this->conf['PS_SC_PDFCATALOG_CATEGCOVER'])
					$this->addCategoryCoverPage(parent::hideCategoryPosition($category->getName($this->id_lang)), $category->description[$this->id_lang], $id_category);
				else
				{
					if(empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']))
					{
						$idx = 0;
       				 	$this->header_page=true;
						$this->AddPage();
						$actual_pos_y = $this->GetY();
					}
				}
			
				$this->calculPrefix($id_category, $category);
				$this->addBookmark($id_category, $category);
			
				if ($this->conf['PS_SC_PDFCATALOG_CATEGCOVER'])
				{
					if(empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']))
					{
						$idx = 0;
       				 	$this->header_page=true;
						$this->AddPage();
						$actual_pos_y = $this->GetY();
					}
				}
			}
			else
			{
				$this->calculPrefix($id_category, $category);
				$this->categ_waiting[] = $id_category;
			}
			
			foreach ($products AS $productrow){
				$product = new Product((int)($productrow['id_product']));
				//if (!$product->active) continue;
				if (Validate::isLoadedObject($product)){
					if ($idx == 4){
						$idx = 0;
						$this->AddPage();
					}
                          $table=false;
                         if($idx>1){                            
                             if(($idx%2)==0){
                                  $newy=max(array($this->last_y1,$this->last_y2))+20;
                          $table=false;
					    }
					}
                         else {
                              $newy=$posy[$idx];
                              $table=true;
					}
					
					if($idx==0 || $idx==2)
						$actual_pos_y = $this->GetY()+15;
					
					$this->addProductBox($product, $productrow['combinations'], $image_width,$image_height,$posx[$idx],$actual_pos_y/*$posy[$idx]*/,'big',$newy,$idx);
					$idx++;
				}else{
					die('Invalid product');
				}
			}
		}
	}

	public function addProductBox($product, $productCombinations, $width,$height,$posx,$posy,$theme,$newy,$idx)
	{
         /* print_r($product);
          echo '<hr/>';
          die();
          */
		$this->SetFillColor(240, 240, 240);
		$this->SetTextColor(0, 0, 0);
		$this->SetFont($this->fontname(), '', 9);
		$this->SetXY($posx,$posy);
		$coverid = Product::getCover($product->id);
		if(version_compare(_PS_VERSION_, '1.4.3', '>=')){
			$imagefile = _PS_IMG_DIR_.'p/'.$this->getImgFolderStatic($coverid['id_image']).$coverid['id_image'].'-'.$this->imageformat.'.jpg';
			if (Configuration::get('PS_LEGACY_IMAGES') && !file_exists($imagefile))
				$imagefile = _PS_IMG_DIR_.'p/'.$product->id.'-'.$coverid['id_image'].'-'.$this->imageformat.'.jpg';
		}else{
			$imagefile = _PS_IMG_DIR_.'p/'.$product->id.'-'.$coverid['id_image'].'-'.$this->imageformat.'.jpg';
		}
		
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			$pdt_link = $product->getLink();
		else
			$pdt_link = SCPDFCatalog::getHttpHost() . __PS_BASE_URI__ . 'product.php?id_product=' . (int)($product->id);
		
		//if ($theme == 'big'){
			if (file_exists($imagefile)){
				$iwh = getimagesize($imagefile);
				if($iwh[0]>$iwh[1])
					$height = $width * $iwh[1] / $iwh[0];
				else
					$width = $height * $iwh[0] / $iwh[1];
				$offset_center=floor(($this->cell_width-$width)/2);
				
				$this->Image($imagefile, $posx+$offset_center, $posy, $width, $height, '', ($this->conf['PS_SC_PDFCATALOG_LINKS'] ? $pdt_link : ''));
			}
			$this->SetXY($posx,$posy+$height+1);
               $product_label=$this->displayProductLabel($product->name[$this->id_lang], $product->description_short[$this->id_lang],$this->title_length);
               
               if ($this->conf['showSupplierReference'])
                {
                    //$this->SetXY($posx, $this->getY()+1);
                    //$this->Cell(20, 6, $this->mod->tt('Supplier reference:') . ' ' . $product->supplier_reference, 0, 0, 'R', 0);
                   $product_label .= "\n".$this->mod->tt('Artikelnr.') . ' ' . $product->supplier_reference;
                }
        $this->MultiCell($this->cell_width, 3,$product_label, '', 'L', 0);
        
        if ($this->conf['showStock'])
        {
            if ($productCombinations)
            {
                $combNum = count($productCombinations);
                $chaineHTML .= '<br/><table style="font-size:18px; padding:0">';
                if ($combNum > 8)
                {
                    // 3 columns
                    $chaineHTML = '<br/><table style="font-size:18px">';
                    $thirdIndex = ceil($combNum / 3);
                    for ($i = 0; $i < $thirdIndex; $i++)
                    {
                        $chaineHTML .= '<tr><td>' . $productCombinations[$i]['attr_group_public_name'] . ' ' . $productCombinations[$i]['attr_name'] . ' x ' .
                                $productCombinations[$i]['quantity'] . '</td>';
                        if (isset($productCombinations[$thirdIndex + $i]))
                        {
                            $chaineHTML .= '<td>' . $productCombinations[$thirdIndex + $i]['attr_group_public_name'] . ' ' .
                                    $productCombinations[$thirdIndex + $i]['attr_name'] . ' x ' . $productCombinations[$thirdIndex + $i]['quantity'] . '</td>';
                        }
                        $index23 = $thirdIndex * 2;
                        if (isset($productCombinations[$index23 + $i]))
                        {
                            $chaineHTML .= '<td>' . $productCombinations[$index23 + $i]['attr_group_public_name'] . ' ' .
                                    $productCombinations[$index23 + $i]['attr_name'] . ' x ' . $productCombinations[$index23 + $i]['quantity'] . '</td>';
                        }
                        $chaineHTML .= '</tr>';
                    }
                }
                else
                {
                    // 2 columns
                    $halfIndex = ceil($combNum / 2);
                    for ($i = 0; $i < $halfIndex; $i++)
                    {
                        $chaineHTML .= '<tr><td>' . $productCombinations[$i]['attr_group_public_name'] . ' ' . $productCombinations[$i]['attr_name'] . ' x ' .
                                $productCombinations[$i]['quantity'] . '</td>';
                        if (isset($productCombinations[$halfIndex + $i]))
                        {
                            $chaineHTML .= '<td>' . $productCombinations[$halfIndex + $i]['attr_group_public_name'] . ' ' .
                                    $productCombinations[$halfIndex + $i]['attr_name'] . ' x ' . $productCombinations[$halfIndex + $i]['quantity'] . '</td>';
                        }
                        $chaineHTML .= '</tr>';
                    }
                }
                $chaineHTML .= '</table>';
            }
            else
            {
                $chaineHTML = '<span style="font-size:18px">' . $this->mod->tt('Stock:') . ' ' . $product->quantity.'</span>';
            }
        }
        $this->WriteHTMLCell($this->cell_width, '', $posx, $this->getY()+1, $chaineHTML, 0, 1, 0, true, 'L');
        //$this->MultiCell($this->cell_width, 3,$product_label."\n".'Ref. '.$product->reference, '', 'L', 0);
			$this->SetXY($posx,$this->getY()+1);
			$this->SetFont($this->fontname(), 'B', 10);
			if($this->vatexc==1) 
			{
				$this->SetXY($posx,$this->getY()+1);
				$cell_content=$this->mod->tt('Price Exc. Tax').": ";
				$this->Cell(20, 6,$cell_content, 0, 0, 'R', 0);
				$cell_contentprice=$this->getPrice($product->id,0,0);
				$this->Cell(30, 6,$cell_contentprice, 0, 0, 'R', 0);
			}
			if($this->vatinc==1)
			{
				if($this->vatexc==1)
					$this->SetXY($posx,$this->getY()+4);
				else
					$this->SetXY($posx,$this->getY()+1);
				$cell_content=$this->mod->tt('Price Inc. Tax').": ";
				$this->Cell(20, 6,$cell_content, 0, 0, 'R', 0);
				$cell_contentprice=$this->getPrice($product->id,0,1);
				$this->Cell(30, 6,$cell_contentprice,0, 0, 'R', 0);
			}
                        
        $this->SetXY($posx,$this->getY()+4);
			$this->SetFont($this->fontname(), '', 8);
			if ($this->conf['PS_SC_PDFCATALOG_LINKS']) 
                            $this->Cell($width, 6,$this->mod->tt('See online'), 0, 0, 'C', 0, $pdt_link);
		/*}else{
			if (file_exists($imagefile)){
				$iwh = getimagesize($imagefile);
				if($iwh[0]>$iwh[1])
					$height = $width * $iwh[1] / $iwh[0];
				else
					$width = $height * $iwh[0] / $iwh[1];
				$offset_center=floor(($this->cell_width-$width)/2);
				$this->Image($imagefile,$posx+$offset_center , $newy,$width, $height, '', ($this->conf['PS_SC_PDFCATALOG_LINKS'] ? $pdt_link : ''));
			}
			
			$product_label=$this->displayProductLabel($product->name[$this->id_lang], $product->description_short[$this->id_lang],$this->title_length);
			
                $chaineHTML=$product_label;
               if(Tools::strlen($product->reference)>0)
                    $chaineHTML.="<br/>Ref. ".$product->reference ;
               if($this->vatexc==1) :                    
                    $chaineHTML.="<br/>".$this->mod->tt('Price Exc. Tax').str_repeat('.',20-Tools::strlen(trim($this->mod->tt('Price Exc. Tax'))));
                    $chaineHTML.=$this->getPrice($product->id,0,0);
               endif;
               if($this->vatinc==1) :                    
                    $chaineHTML.="<br/>".$this->mod->tt('Price Inc. Tax').str_repeat('.',20-Tools::strlen(trim($this->mod->tt('Price Inc. Tax'))));
                    $chaineHTML.=$this->getPrice($product->id,0,1);
               endif;
               $this->WriteHTMLCell($this->cell_width,3,$posx, $newy+$height-20,$chaineHTML,0,0,0,1,'L');
               if(($idx%2)==0)
                    $this->last_y1=$this->GetY ();
               else
                    $this->last_y2=$this->GetY ();
		}*/
	}
}
