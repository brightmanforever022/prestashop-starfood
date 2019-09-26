<?php
class PDFGenerator extends PDFGeneratorCore
{
    const DEFAULT_FONT = 'dejavusanscondensed';
    
    public $font_by_lang = [];
    
    /*
    * module: ba_prestashop_invoice
    * date: 2016-12-13 10:14:55
    * version: 1.1.16
    */
    public $mpdf=null;
    /*
    * module: ba_prestashop_invoice
    * date: 2016-12-13 10:14:55
    * version: 1.1.16
    */
    public function __construct($use_cache = false, $mode='',$format='A4',$default_font_size=0,$default_font='dejavusanscondensed',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P')
    {
        require_once(_PS_TOOL_DIR_ . "mpdf/mpdf.php");
        $this->mpdf = new mPDF($mode,$format,$default_font_size,$default_font,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf, $orientation);
        //$this->mpdf->debug = true;
        parent::__construct($use_cache);
    }
    
    /**
     * Change the font
     *
     * @param string $iso_lang
     */
    public function setFontForLang($iso_lang)
    {
        $this->font = PDFGenerator::DEFAULT_FONT;
        if (array_key_exists($iso_lang, $this->font_by_lang)) {
            $this->font = $this->font_by_lang[$iso_lang];
        }
        
        $this->mpdf->setFont($this->font);
        $this->mpdf->WriteHTML('body {     font-family: '.$this->font.'; }', 1);
    }

    
    /*
    * module: ba_prestashop_invoice
    * date: 2016-12-13 10:14:55
    * version: 1.1.16
    */
    public function writePage()
    {
        if (Module::isEnabled('ba_prestashop_invoice')==false) {
            return parent::writePage();
        }
        $this->mpdf->WriteHTML($this->content);
        
    }
    
    /*
    * module: ba_prestashop_invoice
    * date: 2016-12-13 10:14:55
    * version: 1.1.16
    */
    public function render($filename, $display = true)
    {
        if (Module::isEnabled('ba_prestashop_invoice')==false) {
            return parent::render($filename, $display);
        }
        
        if (empty($filename)) {
            throw new PrestaShopException('Missing filename.');
        }
        if ($display === true) {
            $output = 'D';
        } elseif ($display === false) {
            $output = 'S';
        } elseif ($display == 'D') {
            $output = 'D';
        } elseif ($display == 'S') {
            $output = 'S';
        } elseif ($display == 'F') {
            $output = 'F';
        } else {
            $output = 'I';
        }
       
        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }
        return $this->mpdf->Output($filename, $output);
    }
    
    
    /*
    * module: ba_prestashop_invoice
    * date: 2016-12-13 10:14:55
    * version: 1.1.16
    */
    public function createHeader($header)
    {
        if (Module::isEnabled('ba_prestashop_invoice')==false) {
            $this->header = $header;
        }
        if (!empty($header)) {
            $this->mpdf->setAutoTopMargin = 'stretch';
            $this->mpdf->SetHTMLHeader($header, '', true);
        }
    }
    
    /*
    * module: ba_prestashop_invoice
    * date: 2016-12-13 10:14:55
    * version: 1.1.16
    */
    public function createFooter($footer, $stratchOff=false)
    {
        if (Module::isEnabled('ba_prestashop_invoice')==false) {
            $this->footer = $footer;
        }
        if (!empty($footer)) {
            if (!$stratchOff)
            {
                //$this->mpdf->setAutoBottomMargin = 'stretch';
            }
            $this->mpdf->setAutoBottomMargin = false;
            $this->mpdf->autoMarginPadding = 0;
            $this->mpdf->SetHTMLFooter($footer);
        }
        
    }
}
