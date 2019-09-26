<?php

/*
 * * Format 3x4
 */

require_once(_PS_MODULE_DIR_ . 'scpdfcatalog/sctcpdf/tcpdf.php');
require_once(_PS_MODULE_DIR_ . 'scpdfcatalog/SCPDFCatalogCreator.php');

class catalog extends SCPDFCatalogCreator
{

    public $last_y1 = 0;
    public $last_y2 = 0;
    public $cell_width = 67;
    public $title_length = 150;
    public $logo_height = -1;

    /*
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4')
    {
        parent::__construct($orientation, $unit, $format);
        $this->SetAutoPageBreak(true, 20);
    }
     */
    
    public function Header()
    {
        global $paramFormat;
        if (!$this->conf['PS_SC_PDFCATALOG_FIRSTPAGE'] || ($this->conf['PS_SC_PDFCATALOG_FIRSTPAGE'] && $this->getPage() != 1 ))
        {
            $this->SetTopMargin(20);
            /* if (file_exists(_PS_MODULE_DIR_.'scpdfcatalog/templates/'.$paramFormat.'/logo_pdfcatalog.jpg')){
              $this->Image(_PS_MODULE_DIR_.'scpdfcatalog/templates/'.$paramFormat.'/logo_pdfcatalog.jpg', 10, 8, 0, 15);
              }elseif (file_exists(_PS_IMG_DIR_.'/logo_pdfcatalog.jpg')){
              $this->Image(_PS_IMG_DIR_.'/logo_pdfcatalog.jpg', 10, 8, 0, 15);
              }else{
              if (file_exists(_PS_IMG_DIR_.'/logo.jpg'))
              $this->Image(_PS_IMG_DIR_.'/logo.jpg', 10, 8, 0, 15);
              } */

            //$this->getLogoHeight();
            if ($this->logo_height == -1)
                $this->getLogoHeight();

            $this->SetFont($this->fontname(), 'B', 15);
            // $this->Cell(115);

            if ($this->conf['PS_SC_PDFCATALOG_CATEGHEADER'] && $this->currentCategory != 0 &&
                    $this->format != '1x1' && $this->showPageNum && $this->header_page == true)
            {
                $fil = '';
                $pipe = Configuration::get('PS_NAVIGATION_PIPE');
                $cat = new Category((int) ($this->currentCategory), $this->id_lang);
                $cat_m = new Category((int) ($cat->id_parent), $this->id_lang);
                while ($cat_m->id_parent > 1) {
                    $fil = $this->hideCategoryPosition($cat_m->name) . ' ' . $pipe . ' ' . $fil;
                    $cat_m = new Category((int) ($cat_m->id_parent), $this->id_lang);
                }
                $this->SetTextColor(180, 180, 180);
                $this->SetFont($this->fontname(), '', 10);
                //$this->WriteHTMLCell(190,$this->getY(),10,25,$fil,0,0,0,'C');
                $fil = $this->hideCategoryPosition($cat->name);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont($this->fontname(), '', 12);

                //$this->WriteHTMLCell(190,$this->getY(),10,30, ($fil),0,0,0,'C'); //Tools::strtoupper
                $this->WriteHTMLCell(190, 5, 10, ($this->logo_height + 3), ($fil), 0, 0, 0, 'C'); //Tools::strtoupper
                $this->SetTopMargin(($this->logo_height + 5 + 5));
            }
        }
    }

    public function getLogoHeight()
    {
        global $paramFormat;

        $lelogo = $this->conf['PS_SC_PDFCATALOG_DOCLOGO'];
        if (file_exists(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/catalog/' . $lelogo))
        {
            $sizes = getimagesize(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/catalog/' . $lelogo);

            $width_mm = ceil($sizes[0] / 2.837);
            $height_mm = ceil($sizes[1] / 2.837);

            if ($width_mm > 210)
            {
                $newWidth = 210;
            }
            else
            {
                $newWidth = $width_mm;
            }

            if ($height_mm > 40)
            {
                $newHeight = 40;
                $newWidth = floor($width_mm * 40 / $height_mm);
                if ($newWidth > 210)
                {
                    $newHeight = floor($newHeight * 210 / $newWidth);
                    $newWidth = 210;
                }
            }
            else
            {
                $newHeight = $height_mm;
            }

            $this->logo_height = $newHeight + 3;
            if ($this->logo_height < 20)
                $this->logo_height = 20;
            $this->SetTopMargin($this->logo_height);
            $this->Image(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/catalog/' . $lelogo, 0, 0, $newWidth, $newHeight, '', '', '', FALSE);
        }
        else
        {
            $this->logo_height = 10;
            $this->SetTopMargin($this->logo_height);
        }
    }

    public function createCatalog($format, $start, $limit, $orderBy, $orderWay)
    {

        $image_height = 30;
        $image_width = 30;


        $this->format = $format;
        $active = $this->conf['PS_SC_PDFCATALOG_ACTIVEPRODUCT'];
        $this->showPageNum = true;
        $this->header_page = false;
        $posx = array(7, 75, 141, 7, 75, 141, 7, 75, 141, 7, 75, 141);
        //$posx = array(5, 76, 140, 10, 78, 142, 10, 78, 142, 10, 78, 142);
        //$posy = array(40, 40, 40, 96, 96, 96, 151, 151, 151, 208, 208, 208);
        $posy = array(32, 32, 32, 88, 88, 88, 143, 143, 143, 200, 200, 200);
        $idx = 0;
        if (!empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']))
            $this->AddPage();
        foreach ($this->categories AS $id_category)
        {
            $this->header_page = false;
            $this->currentCategory = $id_category;
            $category = new Category($id_category);
            /* if (!$category->active)
              continue; */

            $products = Product::getProductsAndCombinations($this->id_lang, $start, $limit, $orderBy, $orderWay, $id_category, $active, $this->conf['dontShowFakeCombinations'], $this->conf['PS_SC_PDFCATALOG_WITHSTOCKPRODUCT']);
            $products = $this->filtersByStockAndManufacturer($products);
            if (count($products) > 0)
            {
                if ($this->conf['PS_SC_PDFCATALOG_CATEGCOVER'])
                    $this->addCategoryCoverPage(parent::hideCategoryPosition($category->getName($this->id_lang)), $category->description[$this->id_lang], $id_category);
                else
                {
                    if (empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']))
                    {
                        $idx = 0;
                        $this->header_page = true;
                        $this->AddPage();
                        $actual_pos_y = $this->GetY();
                    }
                }

                $this->calculPrefix($id_category, $category);
                $this->addBookmark($id_category, $category);

                if ($this->conf['PS_SC_PDFCATALOG_CATEGCOVER'])
                {
                    if (empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']))
                    {
                        $idx = 0;
                        $this->header_page = true;
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

            $this->fhPt+=11;
            foreach ($products AS $productrow)
            {
                $product = new Product((int) ($productrow['id_product']));
                /* if (!$product->active)
                  continue; */
                if (Validate::isLoadedObject($product))
                {
                    if ($idx == 12)
                    {
                        $idx = 0;

                        $this->AddPage();
                    }

                    $table = false;

                    if ($idx == 0 || $idx == 3 || $idx == 6 || $idx == 9)
                        $actual_pos_y = $this->GetY() + 5;

                    $this->addProductBox($product, $productrow['combinations'], $image_width, $image_height, $posx[$idx], $actual_pos_y/* $posy[$idx] */, 'small', "", $idx);
                    $idx++;
                }else
                {
                    die('Invalid product');
                }
            }
        }
    }

    public function addProductBox($product, $productCombinations, $width, $height, $posx, $posy, $theme, $newy = "", $idx)
    {
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont($this->fontname(), '', 9);
        $this->SetXY($posx, $posy);
        $coverid = Product::getCover($product->id);
        if (version_compare(_PS_VERSION_, '1.4.3', '>='))
        {
            $imagefile = _PS_IMG_DIR_ . 'p/' . $this->getImgFolderStatic($coverid['id_image']) . $coverid['id_image'] . '-' . $this->imageformat . '.jpg';
            if (Configuration::get('PS_LEGACY_IMAGES') && !file_exists($imagefile))
                $imagefile = _PS_IMG_DIR_ . 'p/' . $product->id . '-' . $coverid['id_image'] . '-' . $this->imageformat . '.jpg';
        }else
        {
            $imagefile = _PS_IMG_DIR_ . 'p/' . $product->id . '-' . $coverid['id_image'] . '-' . $this->imageformat . '.jpg';
        }

        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            $pdt_link = $product->getLink();
        else
            $pdt_link = SCPDFCatalog::getHttpHost() . __PS_BASE_URI__ . 'product.php?id_product=' . (int) ($product->id);

        if ($theme == 'big')
        {
            if (file_exists($imagefile))
            {
                $iwh = getimagesize($imagefile);
                if ($iwh[0] > $iwh[1])
                    $height = $width * $iwh[1] / $iwh[0];
                else
                    $width = $height * $iwh[0] / $iwh[1];
                $this->Image($imagefile, $posx, $posy, $width, $height, '', ($this->conf['PS_SC_PDFCATALOG_LINKS'] ? $pdt_link : ''));
            }
            $this->SetXY($posx, $posy + $height + 6);
            //$this->MultiCell($width, 3,html_entity_decode(strip_tags($product->name[$this->id_lang]."\n".'Ref. '.$product->reference), ENT_NOQUOTES, $this->encoding()), 0, 'C', 0);
            //$this->SetXY($posx,$this->getY()+3);
            //$this->SetFont($this->fontname(), 'B', 10);
            //$this->Cell($width, 6,$this->convertSign($this->currency->getSign('left')).number_format($product->getPrice($this->vatinc, NULL, 2)*$this->currency->conversion_rate, 2, '.', '').$this->convertSign($this->currency->getSign('right')), 0, 0, 'C', 0);
            if ($this->vatexc == 1 && $this->vatinc == 1)
                $this->title_length = 65;
            elseif ($this->vatexc == 1 || $this->vatinc == 1)
                $this->title_length = 80;
            $product_label = $this->displayProductLabel($product->name[$this->id_lang], $product->description_short[$this->id_lang], $this->title_length);


            $this->MultiCell($width, 3, html_entity_decode(strip_tags($product_label . "\n" . 'Ref. ' . $product->reference), ENT_NOQUOTES, $this->encoding()), 0, 'C', 0);
            $this->SetXY($posx, $this->getY() + 3);
            $this->SetFont($this->fontname(), 'B', 10);
            if ($this->vatexc == 1)
            {

                $this->SetXY($posx, $this->getY() + 3);
                $cell_content = $this->mod->tt('Price Exc. Tax') . $this->mod->tt(':') . ' ';
                $this->Cell(20, 6, $cell_content, 0, 0, 'R', 0);
                $cell_contentprice = $this->getPrice($product->id, 0, 0);
                $this->Cell(30, 6, $cell_contentprice, 0, 0, 'R', 0);
            }

            if ($this->vatinc == 1)
            {
                $this->SetXY($posx, $this->getY() + 4);
                $cell_content = $this->mod->tt('Price Inc. Tax') . $this->mod->tt(':') . ' ';
                $this->Cell(20, 6, $cell_content, 0, 0, 'R', 0);
                $cell_contentprice = $this->getPrice($product->id, 0, 1);
                $this->Cell(30, 6, $cell_contentprice, 0, 0, 'R', 0);
            }


            $this->SetXY($posx, $this->getY() + 3);
            $this->SetFont($this->fontname(), '', 8);
            if ($this->conf['PS_SC_PDFCATALOG_LINKS'])
                $this->Cell($width, 6, $this->mod->tt('See online'), 0, 0, 'C', 0, $pdt_link);
        }else
        {
            if (file_exists($imagefile))
            {
                $iwh = getimagesize($imagefile);
                if ($iwh[0] > $iwh[1])
                    $height = $width * $iwh[1] / $iwh[0];
                else
                    $width = $height * $iwh[0] / $iwh[1];
                //center the image
                $offset_center = floor(($this->cell_width - $width) / 2);


                $this->Image($imagefile, $posx + $offset_center, $posy, $width, $height, '', ($this->conf['PS_SC_PDFCATALOG_LINKS'] ? $pdt_link : ''));
            }
            if ($this->vatexc == 1 && $this->vatinc == 1)
                $this->title_length = 65;
            elseif ($this->vatexc == 1 || $this->vatinc == 1)
                $this->title_length = 80;
            $product_label = $this->displayProductLabel($product->name[$this->id_lang], $product->description_short[$this->id_lang], $this->title_length);

            $chaineHTML = $product_label;
            if (Tools::strlen($product->reference) > 0)
                $chaineHTML .= "<br/>Ref. " . $product->reference;

            if ($this->vatexc == 1)
            {
                $chaineHTML .= "<br/>" . $this->mod->tt('Price Exc. Tax') . str_repeat('.', 20 - Tools::strlen(trim($this->mod->tt('Price Exc. Tax'))));
                $chaineHTML .= $this->getPrice($product->id, 0, 0);
            }

            if ($this->vatinc == 1)
            {
                $chaineHTML .= "<br/>" . $this->mod->tt('Price Inc. Tax') . str_repeat('.', 20 - Tools::strlen(trim($this->mod->tt('Price Inc. Tax'))));
                $chaineHTML .= $this->getPrice($product->id, 0, 1);
            }


            if ($this->conf['showSupplierReference'])
            {
                $chaineHTML .= "<br/>" . $this->mod->tt('Artikelnr.') . ' ' . $product->supplier_reference;
            }

            if ($this->conf['showStock'])
            {
                if ($productCombinations)
                {
                    $combNum = count($productCombinations);
                    $chaineHTML .= '<br/><table style="font-size:18px; padding:0">';
                    if ($combNum > 8)
                    {
                        // 3 columns
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
                    $chaineHTML .= '<br/><span style="font-size:18px">' . $this->mod->tt('Stock:') . ' ' . $product->quantity . '</span>';
                }
            }
            $this->WriteHTMLCell($this->cell_width, '', $posx, $posy + $height + 2, $chaineHTML, 0, 1, 0, true, 'L');
            // $this->SetXY($posx, $newy+$height-20);
            //$this->WriteHTML($chaineHTML,true,false,true);

            /* if (($idx % 3) == 0)
              $this->last_y1 = ceil($this->GetY());
              else
              $this->last_y2 = ceil($this->GetY()); */
        }
    }

}
