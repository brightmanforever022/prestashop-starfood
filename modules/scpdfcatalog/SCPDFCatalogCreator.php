<?php
/**
 * PDF Catalog
 *
 * @category migration_tools
 * @author Store Commander <support@storecommander.com>
 * @copyright 2009-2015 Store Commander
 * @version 2.6.2
 * @license commercial
 *
 **************************************
 **           PDF Catalog             *
 **   http://www.StoreCommander.com   *
 **            V 2.6.2                *
 **************************************
 * +
 * +Languages: EN, FR
 * +PS version: 1.2
 * */

class SCPDFCatalogCreator extends SC_TCPDF {

    public $conf = array();
    public $currency = NULL;
    public $categ_names = array();
    public $categ_waiting = array();
    public $id_lang = 1;
    public $_iso;
    public $_pdfparams = array();
//	public $_fpdf_core_fonts = array('courier', 'helvetica', 'helveticab', 'helveticabi', 'helveticai', 'symbol', 'times', 'timesb', 'timesbi', 'timesi', 'zapfdingbats');
    public $_fpdf_core_fonts = array('helvetica');
    public $categories = array();
    public $category;
    public $mod;
    public $vatinc;
    public $vatexc;
    public $emptycol;
    public $emptycollibelle;
    public $kiddy_unit_price;
    public $kiddy_stock;
    public $combinationsinc;
    public $product_title_mode;
    public $currentCategory = 0;
    public $showPageNum = false;
    public $logo_height = 0;
    public $imageformat = 'large'; // default image format used from Prestashop to generate big images in PDF
    public $imageformatmedium = 'medium'; // default image format used from Prestashop to generate small images in PDF
    public $imageformatcategory = 'category'; // default image format used from Prestashop to generate small images in PDF
    public $groupReduction = -1; // cache for group reduction value
     public $header_page=false; //
     public $is_toc=false;
     
     public $PDFCatalogFromAdmin=false;

     /**
      * 
      * @global type $paramFormat
      * @global type $divsaveparam
      * @param type $orientation
      * @param type $unit
      * @param type $format
      * @param type $specialOptionNames array of names of special options saved in xml settings file. Options will be assigned in object
      *   with their given names
      */
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $specialOptionNames=false) {
        global $paramFormat, $divsaveparam;
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
            $imagesTypes = ImageType::getImagesTypes('products');
            foreach ($imagesTypes AS $imageType) {
                if ($imageType['name'] == 'large_default')
                    $this->imageformat = 'large_default';
                elseif ($imageType['name'] == 'medium_default')
                    $this->imageformatmedium = 'medium_default';
            }
            $this->imageformatcategory = 'category_default';
        }

        $pdfConfig = simplexml_load_file(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/catalog/' . $divsaveparam . '.xml');

        // assign special options if they exists
        if (is_array($specialOptionNames))
        {
            foreach ($specialOptionNames as $soName)
            {
                $this->conf[$soName] = (string) $pdfConfig->$soName;
            }
        }
        
        $saveparam = (string) $pdfConfig->name;
        $this->conf['PS_SC_PDFCATALOG_TITLE'] = (string) $pdfConfig->title;
        $this->conf['PS_SC_PDFCATALOG_LEGALNOTICE'] = (string) $pdfConfig->legalnotice;
        $this->conf['PS_SC_PDFCATALOG_FOOTER'] = (string) $pdfConfig->footer;
        $this->conf['PS_SC_PDFCATALOG_FILENAME'] = (string) $pdfConfig->filename;
        $this->conf['PS_SC_PDFCATALOG_LANG'] = (int) $pdfConfig->idlang;
        $this->id_lang = (int)($this->conf['PS_SC_PDFCATALOG_LANG']);
        $this->conf['PS_SC_PDFCATALOG_FIRSTPAGE'] = (int) $pdfConfig->firstpage;
        $this->conf['PS_SC_PDFCATALOG_TOCDISPLAY'] = (int) $pdfConfig->tocdisplay;
        $this->conf['PS_SC_PDFCATALOG_PAGENUMBER'] = (int) $pdfConfig->pagenumber;
        $this->conf['PS_SC_PDFCATALOG_FORMAT'] = (string) $pdfConfig->format;
        $this->conf['PS_SC_PDFCATALOG_LINKS'] = (int) $pdfConfig->uselinks;
        $this->conf['PS_SC_PDFCATALOG_CATEGCOVER'] = (int) $pdfConfig->usecategcover;
        $this->conf['PS_SC_PDFCATALOG_CATEGHEADER'] = (int) $pdfConfig->usecategheader;
        $this->conf['PS_SC_PDFCATALOG_ACTIVEPRODUCT'] = (int) $pdfConfig->activeproduct;
        $this->conf['PS_SC_PDFCATALOG_WITHSTOCKPRODUCT'] = (int) $pdfConfig->withstockproduct;
        $this->conf['PS_SC_PDFCATALOG_FILTERBYBRAND'] = (int) $pdfConfig->filterbybrand;
        $this->conf['PS_SC_PDFCATALOG_ORDERBY'] = (string) $pdfConfig->orderby;
        $this->conf['PS_SC_PDFCATALOG_XDAYSAGO'] = (int) $pdfConfig->xdaysago;
        $this->conf['PS_SC_PDFCATALOG_DOCTITLE'] = (string) $pdfConfig->doctitle;
        $this->conf['PS_SC_PDFCATALOG_DOCSUBJECT'] = (string) $pdfConfig->docsubject;
        $this->conf['PS_SC_PDFCATALOG_DOCCREATOR'] = (string) $pdfConfig->doccreator;
        $this->conf['PS_SC_PDFCATALOG_AUTHOR'] = (string) $pdfConfig->author;
        $this->conf['PS_SC_PDFCATALOG_CATEGORIES'] = explode(',', (string) $pdfConfig->categlist);
        $this->conf['PS_SC_PDFCATALOG_THOUSANDS_SEP'] = (string) $pdfConfig->thousandssep;
        $this->conf['PS_SC_PDFCATALOG_DECIMALS_SEP'] = (string) $pdfConfig->decimalssep;
        $this->conf['PS_SC_PDFCATALOG_HIDE_CURRENCY'] = (int) $pdfConfig->hidecurrency;
        $this->conf['PS_SC_PDFCATALOG_HIDE_DECIMALS'] = (int) $pdfConfig->hidedecimals;
        $this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE'] = (int) $pdfConfig->categorynewpage;
        
        $this->conf['showSupplierReference'] = (int) $pdfConfig->showSupplierReference;
        $this->conf['showStock'] = (int) $pdfConfig->showStock;
        $this->conf['dontShowFakeCombinations'] = (int) $pdfConfig->dontShowFakeCombinations;
        
        if(!empty($this->conf['PS_SC_PDFCATALOG_CATEGORY_NEW_PAGE']))
        {
        	$this->conf['PS_SC_PDFCATALOG_CATEGCOVER'] = 0;
        	$this->conf['PS_SC_PDFCATALOG_CATEGHEADER'] = 0;
        }
        
        if(!empty($this->conf['PS_SC_PDFCATALOG_XDAYSAGO']) && is_numeric($this->conf['PS_SC_PDFCATALOG_XDAYSAGO']))
        {
        	$date_limit = Db::getInstance()->ExecuteS('SELECT DATE_ADD("'.date("Y-m-d").'", INTERVAL - '.$this->conf['PS_SC_PDFCATALOG_XDAYSAGO'].') AS date_limit');
        	if(!empty($date_limit[0]["date_limit"]))
        		$this->conf['PS_SC_PDFCATALOG_XDAYSAGO_DATE'] = $date_limit[0]["date_limit"];
        }
        
        $this->_iso = Tools::strtoupper(Language::getIsoById($this->id_lang));

        // KIDDYSTORES
        $this->conf['PS_SC_kiddy_unit_price'] = (int) $pdfConfig->kiddy_unit_price;
        $this->conf['PS_SC_kiddy_stock'] = (int) $pdfConfig->kiddy_stock;
        
        /*$this->categories = $this->conf['PS_SC_PDFCATALOG_CATEGORIES'];
        // skip the categories not available for the selected group
        $groupCategories = Db::getInstance()->getValue('SELECT GROUP_CONCAT(cg.`id_category`) FROM `' . _DB_PREFIX_ . 'category_group` cg WHERE id_group=' . (int) $pdfConfig->usecustomergroup);
        $groupCategoriesArray = explode(',', $groupCategories);
        $this->categories = array_intersect($this->categories, $groupCategoriesArray);
        $this->conf['PS_SC_PDFCATALOG_CURRENCY'] = (int) $pdfConfig->currency;*/
        
        $this->categories = $this->conf['PS_SC_PDFCATALOG_CATEGORIES'];
        if(!empty($pdfConfig->usecustomergroup))
        {
	        // skip the categories not available for the selected group
	        $groupCategories = Db::getInstance()->ExecuteS('SELECT cg.`id_category` FROM `' . _DB_PREFIX_ . 'category_group` cg WHERE id_group=' . (int) $pdfConfig->usecustomergroup);
	        $groupCategoriesArray=array();
	        foreach($groupCategories as $r)
	        {
	        	$groupCategoriesArray[]=$r['id_category'];
	        }
	        // $groupCategoriesArray = explode(',', $groupCategories);
	       // print_r($this->categories);
	        $this->categories = array_intersect($this->categories, $groupCategoriesArray);
        }
        //print_r($groupCategoriesArray);
        $this->conf['PS_SC_PDFCATALOG_CURRENCY'] = (int) $pdfConfig->currency;

        $this->conf['PS_SC_PDFCATALOG_USECUSTOMERGROUP'] = (int) $pdfConfig->usecustomergroup;
        $this->conf['PS_SC_PDFCATALOG_PRODUCTIMAGEFORMAT'] = $pdfConfig->productimageformat;
        $this->conf['fontname'] = $pdfConfig->fontname;
        if(!empty($this->conf['PS_SC_PDFCATALOG_PRODUCTIMAGEFORMAT']))
        {
        	$this->imageformatmedium = $this->conf['PS_SC_PDFCATALOG_PRODUCTIMAGEFORMAT'];
        	$this->imageformat = $this->conf['PS_SC_PDFCATALOG_PRODUCTIMAGEFORMAT'];
        }
        $this->conf['PS_SC_PDFCATALOG_VATINC'] = (int) $pdfConfig->vatinc;
        $this->conf['PS_SC_PDFCATALOG_HTINC'] = (int) $pdfConfig->vatexc;
        $this->conf['PS_SC_PDFCATALOG_EMPTYCOL'] = (int) $pdfConfig->emptycol;
        $this->conf['PS_SC_PDFCATALOG_EMPTYCOL_LIBELLE'] = (string) $pdfConfig->emptycollibelle;
        $this->conf['PS_SC_PDFCATALOG_COMBINATIONSINC'] = (int) $pdfConfig->combinationsinc;
        $this->conf['PS_SC_PDFCATALOG_DOCLOGO'] = (string) $pdfConfig->doclogo;
        $this->conf['PS_SC_PDFCATALOG_PRODUCT_TITLE'] = (string) $pdfConfig->product_title;
        $this->conf = array_merge($this->conf, Configuration::getMultiple(array('PS_PDF_ENCODING_' . $this->_iso,
        		'PS_PDF_FONT_' . $this->_iso,
        		'PS_SHOP_NAME',
        		'PS_SHOP_ADDR1',
        		'PS_SHOP_CODE',
        		'PS_SHOP_CITY',
        		'PS_SHOP_COUNTRY',
        		'PS_SHOP_STATE'
        )));
        if (version_compare(_PS_VERSION_,'1.5.0.0','>='))
        {
        	$this->context = Context::getContext();
        	$this->conf['PS_SHOP_NAME'] = $this->context->shop->name;
        }
        $this->conf['PS_SHOP_NAME'] = isset($this->conf['PS_SHOP_NAME']) ? $this->conf['PS_SHOP_NAME'] : 'Your company';
        $this->conf['PS_SHOP_ADDR1'] = isset($this->conf['PS_SHOP_ADDR1']) ? $this->conf['PS_SHOP_ADDR1'] : 'Your company';
        $this->conf['PS_SHOP_CODE'] = isset($this->conf['PS_SHOP_CODE']) ? $this->conf['PS_SHOP_CODE'] : 'Postcode';
        $this->conf['PS_SHOP_CITY'] = isset($this->conf['PS_SHOP_CITY']) ? $this->conf['PS_SHOP_CITY'] : 'City';
        $this->conf['PS_SHOP_COUNTRY'] = isset($this->conf['PS_SHOP_COUNTRY']) ? $this->conf['PS_SHOP_COUNTRY'] : 'Country';
        $this->conf['PS_SHOP_STATE'] = isset($this->conf['PS_SHOP_STATE']) ? $this->conf['PS_SHOP_STATE'] : '';
        $this->_pdfparams[$this->_iso] = array(
            'encoding' => (isset($this->conf['PS_PDF_ENCODING_' . $this->_iso]) AND $this->conf['PS_PDF_ENCODING_' . $this->_iso] == true) ? $this->conf['PS_PDF_ENCODING_' . $this->_iso] : 'iso-8859-1',
            'font' => (isset($this->conf['PS_PDF_FONT_' . $this->_iso]) AND $this->conf['PS_PDF_FONT_' . $this->_iso] == true) ? $this->conf['PS_PDF_FONT_' . $this->_iso] : 'helvetica');
        $this->vatinc = (int)($this->conf['PS_SC_PDFCATALOG_VATINC']);
        $this->vatexc = (int)($this->conf['PS_SC_PDFCATALOG_HTINC']);
        $this->emptycol = (int)($this->conf['PS_SC_PDFCATALOG_EMPTYCOL']);
        $this->emptycollibelle = (string) ($this->conf['PS_SC_PDFCATALOG_EMPTYCOL_LIBELLE']);
        $this->combinationsinc = (int)($this->conf['PS_SC_PDFCATALOG_COMBINATIONSINC']);
        $this->product_title_mode = (string) $pdfConfig->product_title;
        
        $this->pc_collibelle_1 = (string)$pdfConfig->pc_collibelle_1;
    	$this->pc_collibelle_2 = (string)$pdfConfig->pc_collibelle_2;
    	$this->pc_currency_1 = (int)$pdfConfig->pc_currency_1;
    	$this->pc_currency_2 = (int)$pdfConfig->pc_currency_2;
    	$this->pc_usecustomergroup_1 = (int)$pdfConfig->pc_usecustomergroup_1;
    	$this->pc_usecustomergroup_2 = (int)$pdfConfig->pc_usecustomergroup_2;
    	$this->pc_pricecolumns_1 = (string)$pdfConfig->pc_pricecolumns_1;
    	$this->pc_pricecolumns_2 = (string)$pdfConfig->pc_pricecolumns_2;
        
        // KIDDYSTORES
        $this->kiddy_unit_price = (int)($this->conf['PS_SC_kiddy_unit_price']);
        $this->kiddy_stock = (int)($this->conf['PS_SC_kiddy_stock']);

        parent::__construct($orientation, $unit, $format);
        if ($font = $this->embedfont()) {
            $this->AddFont($font);
            $this->AddFont($font, 'B');
        }
        
        if ($this->conf['PS_SC_PDFCATALOG_PAGENUMBER']) {
            $this->AliasNbPages('{nnbb}');
            $this->AliasNumPage('{pnb}');
        }
        $this->currency = new Currency((int)($this->conf['PS_SC_PDFCATALOG_CURRENCY']));
        $this->currency->sign = $this->currency->iso_code;
        $this->mod = Module::getInstanceByName('scpdfcatalog');
        $this->mod->cataloglang = $this->id_lang;
        $this->format = ''; // pdf catalog format
        $this->showPageNum = false;

        $this->SetAutoPageBreak(true, 20);
    }
    
    public function isInAdmin()
    {
    	$this->PDFCatalogFromAdmin = true;
    }

    public function Header() {
        global $paramFormat;
        if (!$this->conf['PS_SC_PDFCATALOG_FIRSTPAGE'] || ($this->conf['PS_SC_PDFCATALOG_FIRSTPAGE'] && $this->getPage() != 1 )) {
        	$this->SetTopMargin(30);
            /* if (file_exists(_PS_MODULE_DIR_.'scpdfcatalog/templates/'.$paramFormat.'/logo_pdfcatalog.jpg')){
              $this->Image(_PS_MODULE_DIR_.'scpdfcatalog/templates/'.$paramFormat.'/logo_pdfcatalog.jpg', 10, 8, 0, 15);
              }elseif (file_exists(_PS_IMG_DIR_.'/logo_pdfcatalog.jpg')){
              $this->Image(_PS_IMG_DIR_.'/logo_pdfcatalog.jpg', 10, 8, 0, 15);
              }else{
              if (file_exists(_PS_IMG_DIR_.'/logo.jpg'))
              $this->Image(_PS_IMG_DIR_.'/logo.jpg', 10, 8, 0, 15);
              } */

            //$this->getLogoHeight();
            if ($this->logo_height == 0)
                $this->getLogoHeight();

            $this->SetFont($this->fontname(), 'B', 15);
            $this->Cell(115);
            
            if ($this->conf['PS_SC_PDFCATALOG_CATEGHEADER'] && $this->currentCategory != 0 && $this->format != '1x1' && $this->showPageNum && $this->header_page==true) {
                 $fil = '';
                  $pipe = Configuration::get('PS_NAVIGATION_PIPE');
                  $cat = new Category((int)($this->currentCategory), $this->id_lang);
                  $cat_m = new Category((int)($cat->id_parent), $this->id_lang);
                  while ($cat_m->id_parent > 1){
                  $fil = $this->hideCategoryPosition($cat_m->name).' '.$pipe.' '.$fil;
                  $cat_m = new Category((int)($cat_m->id_parent), $this->id_lang);
                  } 
                $this->SetTextColor(180,180,180);
                $this->SetFont($this->fontname(), '', 10);
                //$this->WriteHTMLCell(190,$this->getY(),10,25,$fil,0,0,0,'C');
                $fil = $this->hideCategoryPosition($cat->name);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont($this->fontname(), '', 12);
                
                //$this->WriteHTMLCell(190,$this->getY(),10,30, ($fil),0,0,0,'C'); //Tools::strtoupper
                $this->WriteHTMLCell(190, 5 ,10,($this->logo_height+3), ($fil),0,0,0,'C'); //Tools::strtoupper
        		$this->SetTopMargin(($this->logo_height+5+5));
            }
        }
    }

   public function Footer() {
        
          global $paramFormat;
          
          
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
    }

    public function addFirstPage($firstpage, $title, $legalnotice) {
        global $smarty, $paramFormat;
		$smarty->force_compile = 1;
        $this->showPageNum = false;
        $this->AddPage();
        
        $pattern = '/\[([^\]]*)\]/';
        preg_match_all($pattern, $legalnotice, $dates);
        foreach ($dates[1] as $date)
        {
        	$exp = explode(":",$date);
        	$replace = $exp[1];
        	$replace = str_replace("d", Date("d"), $replace);
        	$replace = str_replace("m", Date("m"), $replace);
        	$replace = str_replace("y", Date("Y"), $replace);
        
        	$legalnotice = str_replace("[".$date."]", $replace, $legalnotice);
        }
        
        if ($firstpage == 3)
        {
        	if(file_exists(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/firstpage.jpg'))
        		$image = _PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/firstpage.jpg';
        	if(file_exists(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/firstpage.jpeg'))
        		$image = '/modules/scpdfcatalog/templates/' . $paramFormat . '/firstpage.jpeg';
        	if(file_exists(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/firstpage.png'))
        		$image = '/modules/scpdfcatalog/templates/' . $paramFormat . '/firstpage.png';
        	if(!empty($image))
        	{
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
        elseif ($firstpage == 2 && file_exists(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/firstpage.tpl')) {

            $smarty->assign('scpdfpath', '../');
            if (Tools::getValue('key') == md5(_COOKIE_KEY_))
                $smarty->assign('scpdfpath', '../../');


            $image = "";
            if (file_exists(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/catalog/' . $this->conf['PS_SC_PDFCATALOG_DOCLOGO'])) {
                $image = _PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/catalog/' . $this->conf['PS_SC_PDFCATALOG_DOCLOGO'];
            }

            $smarty->assign('image', $image);
            $smarty->assign('title', $title);
            $smarty->assign('legalnotice', $legalnotice);

            $result = $smarty->fetch(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/firstpage.tpl');
            $pattern = '/\[([^\]]*)\]/';
            preg_match_all($pattern, $result, $dates);
            foreach ($dates[1] as $date)
            {
            	$exp = explode(":",$date);
            	$replace = $exp[1];
            	$replace = str_replace("d", Date("d"), $replace);
            	$replace = str_replace("m", Date("m"), $replace);
            	$replace = str_replace("y", Date("Y"), $replace);
            
            	$result = str_replace("[".$date."]", $replace, $result);
            }
            
            $this->WriteHTMLCell(190, 250, 10, $this->logo_height, $result, 0, 0, 0, 1, 'C');
        } else {
            $this->SetTextColor(0, 0, 0);
            $this->SetFont($this->fontname(), '', 16);
            $this->WriteHTMLCell(190, 10, 10, 90, Tools::strtoupper($this->conf['PS_SHOP_NAME']), 0, 0, 0, 1, 'C');
            $this->WriteHTMLCell(190, 10, 10, 150, $title, 0, 0, 0, 1, 'C');
            $this->SetFont($this->fontname(), '', 8);
            $this->WriteHTMLCell(190, 20, 10, 235, $legalnotice);
        }
    }

    public function addCategoryCoverPage($title, $subtitle, $id_category) {
        global $smarty, $paramFormat;
		$smarty->force_compile = 1;
        $this->header_page=false;
        if ($this->conf['PS_SC_PDFCATALOG_CATEGCOVER'] == 3 && file_exists(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/categorypage.tpl')) {

            $this->AddPage();
            $smarty->assign('scpdfpath', '../');
            if (Tools::getValue('key') == md5(_COOKIE_KEY_))
                $smarty->assign('scpdfpath', '../../');


            $smarty->assign('title', $title);
            $smarty->assign('subtitle', $subtitle);

            $image = "";
            if (file_exists(_PS_CAT_IMG_DIR_ . $id_category . '-' . $this->imageformat . '.jpg')) {
                $image = _PS_CAT_IMG_DIR_ . $id_category . '-' . $this->imageformat . '.jpg';
            } else if (file_exists(_PS_CAT_IMG_DIR_ . $id_category . '-' . $this->imageformatcategory . '.jpg')) {
                $image = _PS_CAT_IMG_DIR_ . $id_category . '-' . $this->imageformatcategory . '.jpg';
            }
            $smarty->assign('image', $image);

            $template = _PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/categorypage.tpl';
            $result = $smarty->fetch($template);
            $pattern = '/\[([^\]]*)\]/';
            preg_match_all($pattern, $result, $dates);
            foreach ($dates[1] as $date)
            {
            	$exp = explode(":",$date);
            	$replace = $exp[1];
            	$replace = str_replace("d", Date("d"), $replace);
            	$replace = str_replace("m", Date("m"), $replace);
            	$replace = str_replace("y", Date("Y"), $replace);
            
            	$result = str_replace("[".$date."]", $replace, $result);
            }
            $this->WriteHTMLCell(190, 250, 10, $this->logo_height, $result, 0, 0, 0, 1, 'C');
        } else {
            $this->AddPage();
            $this->SetTextColor(0, 0, 0);
            $this->SetFont($this->fontname(), '', 16);
            //$this->WriteHTMLCell(190, 0,10,$this->logo_height,($title),0,0,0,1,'C');
            $offset_center=40;
            $cell_height=10;
            if ($this->conf['PS_SC_PDFCATALOG_CATEGCOVER'] != 2) {	
                
                $offset_center=297-$this->logo_height-$cell_height;
                $offset_center=floor($offset_center/2);			
			
                $this->SetXY(5, $offset_center);
                
            }
            $this->Cell(0, $cell_height, $title, 'TB', 1, 'C');
            if ($this->conf['PS_SC_PDFCATALOG_CATEGCOVER'] == 2) {
                $this->SetFont($this->fontname(), '', 12);
                //$this->Cell(190,15,)
                $this->WriteHTMLCell(190, 0, 15, $this->GetY() + 15, $subtitle, 0, 2, 0, 1, 'L');
                $temp_y=$this->GetY();
                
                $image = "";
                if (file_exists(_PS_CAT_IMG_DIR_ . $id_category . '-' . $this->imageformat . '.jpg')) {
                    $image = _PS_CAT_IMG_DIR_ . $id_category . '-' . $this->imageformat . '.jpg';
                } else if (file_exists(_PS_CAT_IMG_DIR_ . $id_category . '-' . $this->imageformatcategory . '.jpg')) {
                    $image = _PS_CAT_IMG_DIR_ . $id_category . '-' . $this->imageformatcategory . '.jpg';
                }
                if(!empty($image))
                {
	                $sizes = getimagesize($image);
	                $width_mm = ceil($sizes[0] / 2.837);
	                $height_mm = ceil($sizes[1] / 2.837);
	           
	                $image_offset_center=floor((210-$width_mm)/2);
	                $this->Image($image, $image_offset_center, $temp_y + 25);
                }
            }
        }
        $this->header_page=true;
    }

    public function convertSign($s) {
        return str_replace('&yen;', chr(165), str_replace('&pound;', chr(163), str_replace('&euro;', chr(128), $s)));
    }

    public function l($string) {
        if (@!include(_PS_TRANSLATIONS_DIR_ . Language::getIsoById($this->id_lang) . '/pdf.php'))
            die('Cannot include PDF translation language file : ' . _PS_TRANSLATIONS_DIR_ . Language::getIsoById($this->id_lang) . '/pdf.php');
        if (!is_array($_LANGPDF))
            return str_replace('"', '&quot;', $string);
        $key = md5(str_replace('\'', '\\\'', $string));
        $str = (key_exists('PDF_invoice' . $key, $_LANGPDF) ? $_LANGPDF['PDF_invoice' . $key] : $string);
        return $str;
    }

    public function encoding() {
        return (isset($this->_pdfparams[$this->_iso]) AND is_array($this->_pdfparams[$this->_iso]) AND $this->_pdfparams[$this->_iso]['encoding']) ? $this->_pdfparams[$this->_iso]['encoding'] : 'iso-8859-1';
    }

    public function embedfont() {
        return (((isset($this->_pdfparams[$this->_iso]) AND is_array($this->_pdfparams[$this->_iso]) AND $this->_pdfparams[$this->_iso]['font']) AND !in_array($this->_pdfparams[$this->_iso]['font'], $this->_fpdf_core_fonts)) ? $this->_pdfparams[$this->_iso]['font'] : false);
    }

    public function fontname() {
    	$font = $this->embedfont();
        if(!empty($this->conf['fontname']))
        	$font = $this->conf['fontname'];
        return $font ? $font : 'Helvetica';
    }

    public function EAN13($x, $y, $barcode, $h = 16, $w = .35) {
        return $this->Barcode($x, $y, $barcode, $h, $w, 13);
    }

    public function UPC_A($x, $y, $barcode, $h = 16, $w = .35) {
        return $this->Barcode($x, $y, $barcode, $h, $w, 12);
    }

    public function GetCheckDigit($barcode) {
        //Compute the check digit
        $sum = 0;
        for ($i = 1; $i <= 11; $i += 2)
            $sum += 3 * $barcode{$i};
        for ($i = 0; $i <= 10; $i += 2)
            $sum += $barcode{$i};
        $r = $sum % 10;
        if ($r > 0)
            $r = 10 - $r;
        return $r;
    }

    public function TestCheckDigit($barcode) {
        //Test validity of check digit
        $sum = 0;
        for ($i = 1; $i <= 11; $i += 2)
            $sum += 3 * $barcode{$i};
        for ($i = 0; $i <= 10; $i += 2)
            $sum += $barcode{$i};
        return ($sum + $barcode{12}) % 10 == 0;
    }

    public function Barcode($x, $y, $barcode, $h, $w, $len) {
        $this->SetFillColor(0, 0, 0);
        //Padding
        $barcode = str_pad($barcode, $len - 1, '0', STR_PAD_LEFT);
        if ($len == 12)
            $barcode = '0' . $barcode;
        //Add or control the check digit
        if (Tools::strlen($barcode) == 12)
            $barcode.= $this->GetCheckDigit($barcode);
        elseif (!$this->TestCheckDigit($barcode))
            $this->Error('Incorrect check digit');
        //Convert digits to bars
        $codes = array(
            'A' => array(
                '0' => '0001101', '1' => '0011001', '2' => '0010011', '3' => '0111101', '4' => '0100011',
                '5' => '0110001', '6' => '0101111', '7' => '0111011', '8' => '0110111', '9' => '0001011'),
            'B' => array(
                '0' => '0100111', '1' => '0110011', '2' => '0011011', '3' => '0100001', '4' => '0011101',
                '5' => '0111001', '6' => '0000101', '7' => '0010001', '8' => '0001001', '9' => '0010111'),
            'C' => array(
                '0' => '1110010', '1' => '1100110', '2' => '1101100', '3' => '1000010', '4' => '1011100',
                '5' => '1001110', '6' => '1010000', '7' => '1000100', '8' => '1001000', '9' => '1110100')
        );
        $parities = array(
            '0' => array('A', 'A', 'A', 'A', 'A', 'A'),
            '1' => array('A', 'A', 'B', 'A', 'B', 'B'),
            '2' => array('A', 'A', 'B', 'B', 'A', 'B'),
            '3' => array('A', 'A', 'B', 'B', 'B', 'A'),
            '4' => array('A', 'B', 'A', 'A', 'B', 'B'),
            '5' => array('A', 'B', 'B', 'A', 'A', 'B'),
            '6' => array('A', 'B', 'B', 'B', 'A', 'A'),
            '7' => array('A', 'B', 'A', 'B', 'A', 'B'),
            '8' => array('A', 'B', 'A', 'B', 'B', 'A'),
            '9' => array('A', 'B', 'B', 'A', 'B', 'A')
        );
        $code = '101';
        $p = $parities[$barcode{0}];
        for ($i = 1; $i <= 6; $i++)
            $code.= $codes[$p[$i - 1]][$barcode{$i}];
        $code.= '01010';
        for ($i = 7; $i <= 12; $i++)
            $code.= $codes['C'][$barcode{$i}];
        $code.= '101';
        //Draw bars
        for ($i = 0; $i < Tools::strlen($code); $i++) {
            if ($code{$i} == '1')
                $this->Rect($x + $i * $w, $y, $w, $h, 'F');
        }
        //Print text uder barcode
        //$this->SetFont('Helvetica', '', 12);
        $this->SetFont($this->fontname(), '', 12);
        $this->Text($x, $y + $h + 11 / $this->k, Tools::substr($barcode, -$len));
        $this->SetFont(self::fontname(), '', 8);
		
		if(Tools::strlen($barcode)>0)
			return true;
		else 
			return false;
		
		
    }

    // useless
    public function pbr2nl($text) {
        return preg_replace("=<br */?>=i", "\n", preg_replace("=<p>=i", "\n<p>", $text));
    }

    public static function getImgFolderStatic($id_image) {
        if (!is_numeric($id_image))
            return false;
        $folders = str_split((string) $id_image);
        return implode('/', $folders) . '/';
    }

    static public function hideCategoryPosition($name) {
        if (version_compare(_PS_VERSION_, '1.4.0.0', '<')) {
            return preg_replace('/^[0-9]+\./', '', $name);
        } else {
            return $name;
        }
    }

    public function displayProductLabel($title, $shortDesc, $title_length = 0, $combination=false, $html=false, $br=false) {

		//the number of characters for the title rely on 
		// * how many columns have to be displayed
		// * what is the length of these columns (barcode columns are wider)
		
		
		
		if ($title_length == 0)
			$title_length = 95;

		$title_length_offset = 0;

		/*if ($with_barcode == true)
			$title_length_offset = 20;
		*/
		if ($this->vatexc == 1)
			$title_length_offset+=15;
		if ($this->vatinc == 1)
			$title_length_offset+=15;

		$title_length-=$title_length_offset;

		$product_label = "";
		if(!$html)
		{
			$shortDesc = str_replace("<br/>"," ",str_replace("<br>"," ",str_replace("<br />"," ",$shortDesc)));
			$shortDesc =  html_entity_decode(str_replace("\n","",strip_tags($shortDesc)),ENT_COMPAT,'UTF-8');
			$title =  html_entity_decode(str_replace("\n","",strip_tags($title)),ENT_COMPAT,'UTF-8');
		}
		
		$temp_product_title_mode = $this->product_title_mode;
		if($combination)
			$temp_product_title_mode = "title";
		
		switch ($temp_product_title_mode) {

			case 'title':
				if(Tools::strlen($title)>$title_length)
					$product_label = Tools::substr($title, 0, $title_length-3)."...";
				else
					$product_label = $title;
				break;
			case 'shortDesc':
				//First of all, one must check if there's a short description for this product
				if (Tools::strlen($shortDesc) > 0) {
					if(Tools::strlen($shortDesc)>$title_length)
						$shortDesc = Tools::substr($shortDesc, 0, $title_length-3)."...";
					else
						$shortDesc = $shortDesc;
					$product_label =$shortDesc;
				}
				else
				{
					//$product_label = Tools::substr($title, 0, $title_length);
					if(Tools::strlen($title)>$title_length)
						$product_label = Tools::substr($title, 0, $title_length-3)."...";
					else
						$product_label = $title;
				}
				break;
			case 'both-space':
				if(Tools::strlen($title." ".$shortDesc)>$title_length)
					$product_label = Tools::substr($title . " " . $shortDesc, 0, $title_length-3)."...";
				else
					$product_label = $title . " " . $shortDesc;
				break;
			case 'both-comma':				
				if(Tools::strlen($title." ".$shortDesc)>$title_length)
					$product_label = Tools::substr($title . ", " . $shortDesc, 0, $title_length-3)."...";
				else
					$product_label = $title . ", " . $shortDesc;
				break;

			case 'both-linebreak':
			  /*//the shortDesc will be displayed inside the catalog in a specific MultiCell
			  $product_label = Tools::substr($title, 0, $title_length); // . "<br/>" . strip_tags($shortDesc,'<a><i><u><em><strong>');*/
				if(Tools::strlen($title." ".$shortDesc)>$title_length)
					$product_label = Tools::substr($title . " " . $shortDesc, 0, $title_length-3)."...";
				elseif(!$html && !$br)
					$product_label = $title . "\n" . $shortDesc;
				else
					$product_label = $title . "<br/>" . $shortDesc;
			  break;
			 
			default:
				$product_label = $product_label = Tools::substr($title, 0, $title_length);
		}
		
		$product_label = trim($product_label);
		
		return $product_label;
	}

    public function getLogoHeight() {
        global $paramFormat;

        $lelogo = $this->conf['PS_SC_PDFCATALOG_DOCLOGO'];
        if(file_exists(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/catalog/' . $lelogo))
        {
	        $sizes = getimagesize(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/catalog/' . $lelogo);
	
	        $width_mm = ceil($sizes[0] / 2.837);
	        $height_mm = ceil($sizes[1] / 2.837);
	
	        if ($width_mm > 210) {
	            $newWidth = 210;
	        } else {
	            $newWidth = $width_mm;
	        }
	
	        if ($height_mm > 40) {
	            $newHeight = 40;
	            $newWidth = floor($width_mm * 40 / $height_mm);
	            if ($newWidth > 210) {
	                $newHeight = floor($newHeight * 210 / $newWidth);
	                $newWidth = 210;
	            }
	        } else {
	            $newHeight = $height_mm;
	        }
	
	        $this->logo_height = $newHeight + 3;
	        if($this->logo_height<20)
	        	$this->logo_height = 20;
	        $this->SetTopMargin($this->logo_height);
	        $this->Image(_PS_MODULE_DIR_ . 'scpdfcatalog/templates/' . $paramFormat . '/catalog/' . $lelogo, 0, 0, $newWidth, $newHeight, '', '', '', FALSE);
        }
        else
        {
        	$this->logo_height = 20;
        	$this->SetTopMargin($this->logo_height);
        }
    }

    public function getCombinations($product, $combinations_fields,$combinations,$title_length,$displayPromo=false) {

        $idproduct = $product->id;
		
		if ($title_length == 0)
			$title_length = 95;
		
		if ($this->vatexc == 1)
			$title_length_offset+=15;
		if ($this->vatinc == 1)
			$title_length_offset+=15;
		
		
		
        //create the sql fields select according to the combination fields
        $str_fields = "";


        while (list($c, $v) = each($combinations_fields)) {
            if (!preg_match('/name/', $c)) {
                $str_fields.="pa." . $c . ",";
            }
        }

        $str_fields = Tools::substr($str_fields, 0, Tools::strlen($str_fields) - 1);
        //the image field must be removed as it needs a special request
        $str_fields = preg_replace('/pa\.image,/', '', $str_fields, -1, $image_field_exists);

        if ($image_field_exists == TRUE) {
            $product_attribute_ids = array_map(array($this, 'getIdProductAttribute'), $combinations); //              

            if (!is_array($product_attribute_ids) || count($product_attribute_ids) > 0) {
                $str_product_attribute_ids = implode(',', $product_attribute_ids);
                $str_product_attribute_ids = '(' . $str_product_attribute_ids . ')';

                //the next step is to get the image_id for each id_product_attribute. Then the image could be displayed
                $sql_product_attributes_images = "SELECT * FROM " . _DB_PREFIX_ . 'product_attribute_image 
                    WHERE id_product_attribute IN ' . $str_product_attribute_ids;

                $temp_images_ids = Db::getInstance()->ExecuteS($sql_product_attributes_images);

                //create an array width id_product_attribute as a key and id image as a value
                $pa_images_ids = array();
                while (list($kkk, $vvv) = each($temp_images_ids)) {

                    $pa_images_ids[$vvv['id_product_attribute']] = $vvv['id_image'];
                }
            }
        }

        $this->setXY(10, $this->getY() + 9);
        //display the table header
        reset($combinations_fields);
        while (list($k, $v) = each($combinations_fields)) {

            if (is_array($v) && isset($v['label'])) {
                if ($k == "name")
                    $width = $v['width'];
                else
                    $width = $v['width'];
            }
        }
        
        //the first cell is group_name & attribute_name   
        $already_displayed_combinations = array();
        $compteur = 1;
        $line_offset = 7;
        //if there's an ean13 or an ups field the length of label must be shortened        
        $fields_keys = implode(',', array_keys($combinations_fields));

        if (preg_match('/ean13|upc/', $fields_keys)) {
          //  $title_length_offset+= 35;
            $line_offset = 17;
        }
        else
            $label_width = 50;
		
		$combinations_counter=1;
		
		
        foreach ($combinations as $a_combination) {

            //loop throught the group name
            $label = "";
            while (list($k, $v) = each($a_combination)) {
                $label.=$v['group_name'] . ' ' . $v['attribute_name'] . ', ';
                $line = $v; // gets the last value 
            }
            //format the label of the line               
            $label = Tools::substr(rtrim($label), 0, Tools::strlen(rtrim($label)) - 1);

            if (!in_array($label, $already_displayed_combinations)) {
                $already_displayed_combinations[] = $label;
                $cell_label_width = $combinations_fields['name']['width'];
                $cell_label_align = $combinations_fields['name']['align'];

                $label = Tools::substr(strip_tags($label), 0, $title_length);

                //loop through the fields of the line
				
				
				
                while (list($field, $an_attribute) = each($line)) {
					
					$with_barcode=false;
					
					
					
					
                    if (!preg_match('/name|price|id_product/', $field)) {
                        $cell_width = $combinations_fields[$field]['width'];
                        $cell_align = Tools::strtoupper(Tools::substr($combinations_fields[$field]['align'], 0, 1));
						
						
						
						
                        //some specific tasks according to the fields
                        switch ($field) {
							
							case 'weight':
								$combination_weight=$an_attribute+$product->weight;
								
								 $this->Cell($cell_width, 6, $combination_weight, 1, 0, $cell_align);
								break;
							
							
                            case "ean13";
                                $cell_width+=3;
                               
                                if (Tools::strlen($an_attribute) > 0){
                                     
                                    $temp_x=$this->GetX()+7;
                                    if($this->EAN13($this->GetX()+7, $this->GetY(), $an_attribute)==true){
										$line_offset+=8;
										$with_barcode=true;
									}
									else
									{
										$line_offset=0;
									}
                                    $this->setY($this->GetY()-20);
                                    $this->setX($temp_x+38);
                                }
                                else
                                    $this->Cell($cell_width, 6, '', 0, 0, $cell_align);

                                break;

                            case "upc";
                                $cell_width+=3;
                                $line_offset+=2;
                                if (Tools::strlen($an_attribute) > 0){
                                    $line_offset+=8;
                                    $temp_x = $this->GetX() + 7;
                                    if($this->UPC_A($this->GetX(), $this->GetY(), $an_attribute)==true){
										$line_offset+=8;
										$with_barcode=true;
									}
									else {
										$line_offset=0;
									}
                                    $this->setY($this->GetY() - 20);
                                    $this->setX($temp_x + 38);
                                }
                                else
                                    $this->Cell($cell_width, 6, '', 0, 0, $cell_align);

                                break;

                            default:
                                 $this->Cell($cell_width, 6, $an_attribute, 1, 0, $cell_align);
                              
                                if ($field == 'reference') {
                                    //if the image field is required, the image must be displayed before the label
                                    if ($image_field_exists == TRUE) {
                                        $current_id_product_attribute = $line['id_product_attribute'];

                                        if (array_key_exists($current_id_product_attribute, $pa_images_ids)) {
                                            //displays the image
                                            $current_image_id = $pa_images_ids[$current_id_product_attribute];

                                            $image_file = _PS_PROD_IMG_DIR_ . $product->id . '-' . $current_image_id . '-small.jpg';
                                            if (file_exists($image_file))
                                                $this->Image($image_file, $this->GetX() + 4, $this->GetY(), 10, 10);
                                        }
                                       $this->SetXY($this->GetX() + 20, $this->GetY());
                                    }
                                     
                                    $this->Cell($cell_label_width, 6, $label, 1, 0, $cell_label_align);
                                }
                               
                        } //end switch on $field
                    }
                    elseif (preg_match('/id_product/', $field)) {
                        //the prices should be displayed
                        if ($this->vatexc == 1) {
							$price = $this->getPrice($product->id, $line['id_product_attribute'],0);
                            //$price = $this->getPrice($product->id, $an_attribute['id_product_attribute'], 0);
                            $this->Cell(20, 6, $price, 1, 0, 'R', 0);
                        }

                        if ($this->vatinc == 1) {
							$price = $this->getPrice($product->id, $line['id_product_attribute'],1);
                            //$price = $this->getPrice($product->id, $an_attribute['id_product_attribute'], 1);
							
                            $this->Cell(20, 6, $price, 1, 0, 'R', 0);
                        }
                    }
                }
                 if($displayPromo==true) {
                        $image_y = $this->GetY()-4;
                        $this->Image(_PS_MODULE_DIR_ . 'scpdfcatalog/medias/onsale_' . Tools::strtolower($this->_iso) . '.png', $this->getX() + 2, $image_y, 12, 0);                    
                    }
					
				if($with_barcode==true)	
					$this->Ln($line_offset+2);
				else
					$this->Ln(7);
				//if(count($combinations)!=$combinations_counter)	
					/*$this->Ln($line_offset+2);
				else
					$this->Ln($line_offset+2);
				*/
				//$combinations_counter++;
				
            }
        } //end of loop on combinations

        return count($combinations);
    }

    public function _parseArray($array) {

        echo '<ul>';
        while (list($c, $v) = each($array)) {

            echo '<li>';
            echo $c;
            if (is_array($v)) {
                if (count($v) > 0)
                    $this->_parseArray($v);
                else
                    echo ' => vide';
            }
            else {
                echo ' => ' . $v;
            }
            echo '</li>';
        }

        echo '</ul>';
    }

    public function getIdProductAttribute($array) {

        if (isset($array[0]['id_product_attribute'])) {
            return $array[0]['id_product_attribute'];
        }
    }

    public function getPrice($id_product, $id_product_attribute = null, $usetax = true, $with_ecotax = true, $usereduc = true, $quantity = 1) {
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
            $id_group = (int) $this->conf['PS_SC_PDFCATALOG_USECUSTOMERGROUP'];
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
        
        $return = $price * $this->currency->conversion_rate;
        $return = Tools::displayPrice($return);
        /*
        $return = number_format($return, (!empty($this->conf['PS_SC_PDFCATALOG_HIDE_DECIMALS'])?0:$decimals), $this->conf['PS_SC_PDFCATALOG_DECIMALS_SEP'], $this->conf['PS_SC_PDFCATALOG_THOUSANDS_SEP']);
       	if(empty($this->conf['PS_SC_PDFCATALOG_HIDE_CURRENCY']))
       		$return = $this->convertSign($this->currency->getSign('left')).$return.$this->convertSign($this->currency->getSign('right'));
        */
        return $return;
    }
    
	public static function getPriceStatic_1_5($id_product, $usetax = true, $id_product_attribute = null, $decimals = 6, $divisor = null,
		$only_reduc = false, $usereduc = true, $quantity = 1, $force_associated_tax = false, $id_customer = null, $id_cart = null,
		$id_address = null, &$specific_price_output = null, $with_ecotax = true, $use_group_reduction = true, Context $context = null,
		$use_customer_price = true)
	{
		if (!$context)
			$context = Context::getContext();

		$cur_cart = $context->cart;

		if ($divisor !== null)
			Tools::displayParameterAsDeprecated('divisor');

		if (!Validate::isBool($usetax) || !Validate::isUnsignedId($id_product))
			die(Tools::displayError());
		// Initializations
		if(!empty($id_customer))
			$id_group = Customer::getDefaultGroupId($id_customer);
		if(empty($id_group))
			$id_group = (isset($context->customer) ? $context->customer->id_default_group : _PS_DEFAULT_CUSTOMER_GROUP_);
		
		// If there is cart in context or if the specified id_cart is different from the context cart id
		if (!is_object($cur_cart) || (Validate::isUnsignedInt($id_cart) && $id_cart && $cur_cart->id != $id_cart))
		{
			/*
			* When a user (e.g., guest, customer, Google...) is on PrestaShop, he has already its cart as the global (see /init.php)
			* When a non-user calls directly this method (e.g., payment module...) is on PrestaShop, he does not have already it BUT knows the cart ID
			* When called from the back office, cart ID can be inexistant
			*/
			if (!$id_cart && !isset($context->employee))
				die(Tools::displayError());
			$cur_cart = new Cart($id_cart);
			// Store cart in context to avoid multiple instantiations in BO
			if (!Validate::isLoadedObject($context->cart))
				$context->cart = $cur_cart;
		}

		$cart_quantity = 0;
		/*if ((int)$id_cart)
		{
			$condition = '';
			$cache_name = (int)$id_cart.'_'.(int)$id_product;
			if (!isset(self::$_cart_quantity[$cache_name]) || self::$_cart_quantity[$cache_name] != (int)$quantity)
				self::$_cart_quantity[$cache_name] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT SUM(`quantity`)
				FROM `'._DB_PREFIX_.'cart_product`
				WHERE `id_product` = '.(int)$id_product.'
				AND `id_cart` = '.(int)$id_cart);
			$cart_quantity = self::$_cart_quantity[$cache_name];
		}*/

		$id_currency = (int)/*Validate::isLoadedObject($context->currency) ? $context->currency->id : */Configuration::get('PS_CURRENCY_DEFAULT');

		// retrieve address informations
		$id_country = (int)$context->country->id;
		$id_state = 0;
		$zipcode = 0;

		if (!$id_address)
			$id_address = $cur_cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};

		if ($id_address)
		{
			$address_infos = Address::getCountryAndState($id_address);
			if ($address_infos['id_country'])
			{
				$id_country = (int)$address_infos['id_country'];
				$id_state = (int)$address_infos['id_state'];
				$zipcode = $address_infos['postcode'];
			}
		}
		else if (isset($context->customer->geoloc_id_country))
		{
			$id_country = (int)$context->customer->geoloc_id_country;
			$id_state = (int)$context->customer->id_state;
			$zipcode = (int)$context->customer->postcode;
		}

		if (Tax::excludeTaxeOption())
			$usetax = false;

		if ($usetax != false
			&& !empty($address_infos['vat_number'])
			&& $address_infos['id_country'] != Configuration::get('VATNUMBER_COUNTRY')
			&& Configuration::get('VATNUMBER_MANAGEMENT'))
			$usetax = false;

		if (is_null($id_customer) && Validate::isLoadedObject($context->customer))
			$id_customer = $context->customer->id;

		return Product::priceCalculation(
			$context->shop->id,
			$id_product,
			$id_product_attribute,
			$id_country,
			$id_state,
			$zipcode,
			$id_currency,
			$id_group,
			$cart_quantity,
			$usetax,
			$decimals,
			$only_reduc,
			$usereduc,
			$with_ecotax,
			$specific_price_output,
			$use_group_reduction,
			$id_customer,
			$use_customer_price,
			$id_cart, 
			$quantity
		);
	}

    public function displayShortDesc($shortDesc, $cellWidth, $cellHeight, $x) {

//         if($this->vatexc==1)
//            $shorDesc_length_offset+=5;
//         if($this->vatinc==1)
//             $shorDesc_length_offset+=5;
//        
//        $shorDesc_length_offset=0;
        
        $shortDesc= $shortDesc;
         $shortDesc_length=$cellWidth;
           $shortDesc=  html_entity_decode($shortDesc,ENT_COMPAT,'UTF-8');
        $getY = $this->GetY();
        //the second cell
        $this->MultiCell(
                $shortDesc_length, $cellHeight, $shortDesc, 1, 'L', 1, 0, $x, $this->GetY() + $cellHeight + 2
        );
        //the Y must be set to the previous ordinate before this cell is displayed
        $this->SetY($getY);
        //the new line must be 2ln
        //so the height of the muticell is required			
        $offsetBR = 6 * $this->getNumLines($shortDesc, $cellWidth);

        $offsetBR+=1;
        return $offsetBR;
    }

    public function hasCombinations($product, $combinations_fields)
    {
    	$withstockproduct = $this->conf['PS_SC_PDFCATALOG_WITHSTOCKPRODUCT'];
    	
         $idproduct = $product->id;
        if ($this->vatexc == 1 && $this->vatinc == 1)
            $offset = 20;
        else
            $offset = 0;
        //create the sql fields select according to the combination fields
        $str_fields = "";


        while (list($c, $v) = each($combinations_fields)) {
            if (!preg_match('/name/', $c)) {
                $str_fields.="pa." . $c . ",";
            }
        }

        $str_fields = Tools::substr($str_fields, 0, Tools::strlen($str_fields) - 1);
        //the image field must be removed as it needs a special request
        $str_fields = preg_replace('/pa\.image,/', '', $str_fields, -1, $image_field_exists);
        
        $attributes = Db::getInstance()->ExecuteS('
		SELECT pa.*, ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, al.`name` AS attribute_name, a.`id_attribute`
		FROM `' . _DB_PREFIX_ . 'product_attribute` pa
		LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
		LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
		LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
		LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int)($this->id_lang) . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int)($this->id_lang) . ')
		WHERE pa.`id_product` = ' . (int)($idproduct) . '
        GROUP BY pa.`id_product_attribute`
		ORDER BY agl.`name`,al.`name`');
        
        $combinations = array();
        foreach ($attributes AS $attribute) {
        	
        	$name = "";
        	$put = true;
        	
            // Combinations
            $sql_string = '
			SELECT agl.`name` AS group_name, al.`name` AS attribute_name, ' . $str_fields . '
			FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
			LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.`id_product_attribute` = pac.`id_product_attribute`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int)($this->id_lang) . ')
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int)($this->id_lang) . ')
			WHERE pac.`id_product_attribute` = pa.`id_product_attribute`
			AND pac.`id_product_attribute` = ' . $attribute['id_product_attribute'] . '
			ORDER BY agl.`name`,al.`name`';

            $result = Db::getInstance()->ExecuteS($sql_string);

            /*foreach($result as $i=>$rslt)
            {
	            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && isset($result[$i]["quantity"]))
	            	$result[$i]["quantity"] = StockAvailable::getQuantityAvailableByProduct($idproduct,$attribute['id_product_attribute']);
            }*/
            
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            	$attribute["quantity"] = StockAvailable::getQuantityAvailableByProduct($idproduct,$attribute['id_product_attribute']);

            if($withstockproduct && empty($attribute["quantity"]))
            	$put = false;
            
            foreach($result as $row)
            	$name .= $row["group_name"]." ".$row["attribute_name"].",";
            
            if (count($result) && $put)
                $combinations[$name] = $result;
        }
        ksort($combinations);

        if (count($combinations) == 0)
            return 0;
        else 
            return $combinations;
    }

    public function addBookmark($id_category, $category,$categ_waiting=true,$page=null)
    {
    	$prefix = "";
    	if($category->id_parent != 1)
    	{
    		$prefix = $this->categ_names[$id_category]["prefix"];
    	}
    	if(!empty($this->categ_waiting) && $categ_waiting==true)
    	{
    		foreach ($this->categ_waiting as $cat_id)
    		{
    			$temp_cat = new Category($cat_id);
    			$temp_prefix = "";
    			if($temp_cat->id_parent != 1)
    			{
    				if(!empty($this->categ_names[$cat_id]["prefix"]))
    					$temp_prefix = $this->categ_names[$cat_id]["prefix"];
    			}
    			if($prefix!=$temp_prefix)
    				$this->addBookmark($cat_id, $temp_cat, false,$page);
    		}
    		$this->categ_waiting = array();
    	}
    	$this->Bookmark($prefix . self::hideCategoryPosition($category->getName($this->id_lang)), 0, 0,$page);
    }
    
    public function calculPrefix($id_category, $category)
    {
    	if (!isset($this->categ_names[$id_category]["name"]))
    	{
    		$this->categ_names[$id_category]["name"] = self::hideCategoryPosition($category->getName($this->id_lang));
    		$this->categ_names[$id_category]["prefix"] = "";
    		if (!array_key_exists($category->id_parent, $this->categ_names))
    		{
    			$pcategory = new Category($category->id_parent);
    			$this->categ_names[$category->id_parent]["name"] = self::hideCategoryPosition($pcategory->getName($this->id_lang));
    			$this->categ_names[$category->id_parent]["prefix"] = "";
    		}
    		elseif(empty($this->categ_names[$id_category]["prefix"]) && $category->id_parent != 1)
    		{
    			$this->categ_names[$id_category]["prefix"] = $this->categ_names[$category->id_parent]["prefix"];
    		}
    	}
    	if($category->id_parent != 1)
    	{
    		$this->categ_names[$id_category]["prefix"] = $this->categ_names[$id_category]["prefix"]." - ";
    	}
    }

	public function getFloatVal($price)
	{
		$return = 0;
		preg_match('/[1-9.]/', $price, $matches);
		if(!empty($matches[0]))
		{
			$firstletter = strpos($price, $matches[0]);
			if($firstletter!==false)
			{
				$return = (float)(Tools::substr($price, $firstletter));
			}
		}
		return $return;
	}
	
	public function filtersByStockAndManufacturer($products)
	{
		$withstockproduct = $this->conf['PS_SC_PDFCATALOG_WITHSTOCKPRODUCT'];
		$filterbybrand = $this->conf['PS_SC_PDFCATALOG_FILTERBYBRAND'];
		
		$xdaysago = null;
		if(!empty($this->conf['PS_SC_PDFCATALOG_XDAYSAGO_DATE']))
		{
			$xdaysago = 1;
		}
		elseif(!empty($this->conf['PS_SC_PDFCATALOG_XDAYSAGO']) && is_numeric($this->conf['PS_SC_PDFCATALOG_XDAYSAGO']) && empty($this->conf['PS_SC_PDFCATALOG_XDAYSAGO_DATE']))
		{
			$date_limit = Db::getInstance()->ExecuteS('SELECT DATE_ADD("'.date("Y-m-d").'", INTERVAL -'.$this->conf['PS_SC_PDFCATALOG_XDAYSAGO'].' DAY) AS date_limit');
			if(!empty($date_limit[0]["date_limit"]))
			{
				$this->conf['PS_SC_PDFCATALOG_XDAYSAGO_DATE'] = $date_limit[0]["date_limit"];
				$xdaysago = 1;
			}
		}
		
		if(!empty($withstockproduct) || !empty($filterbybrand) || !empty($xdaysago))
		{
			$products_temp = $products;
			$products = array();
			foreach($products_temp as $product)
			{
				$put = true;
				if(!empty($withstockproduct))
				{
					$quantity = $product['quantity'];
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
						$quantity = StockAvailable::getQuantityAvailableByProduct($product['id_product'],0);
					if(empty($quantity) || (is_numeric($quantity) && $quantity<=0))
						$put = false;
				}
					
				if(!empty($filterbybrand))
				{
					if($filterbybrand=="-" && !empty($product['id_manufacturer']))
						$put = false;
					elseif($product['id_manufacturer']!=$filterbybrand)
						$put = false;
				}
				
				if(!empty($xdaysago))
				{
					if($product['date_add']<$this->conf['PS_SC_PDFCATALOG_XDAYSAGO_DATE'])
						$put = false;
				}

				if($put==true)
					$products[] = $product;
			}
		}
		
		return $products;
	}
}
