<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
* @since 1.6
*/

class PDFGenerator extends PDFGeneratorCore
{
    public $mpdf=null;
    public function __construct($use_cache = false)
    {
        require_once(_PS_MODULE_DIR_ . "ba_prestashop_invoice/mpdf/mpdf.php");
        $this->mpdf = new mPDF();
        $this->mpdf->debug = true;
        parent::__construct($use_cache);
    }
    
    public function writePage()
    {
        if (Module::isEnabled('ba_prestashop_invoice')==false) {
            return parent::writePage();
        }
        //$this->mpdf->AddPage();
        $this->mpdf->WriteHTML($this->content);
        
    }
    
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
    
    /**
     *
     * set the PDF header
     * @param string $header HTML
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

    /**
     *
     * set the PDF footer
     * @param string $footer HTML
     */
    public function createFooter($footer)
    {
        if (Module::isEnabled('ba_prestashop_invoice')==false) {
            $this->footer = $footer;
        }
        if (!empty($footer)) {
            $this->mpdf->setAutoBottomMargin = 'stretch';
            $this->mpdf->SetHTMLFooter($footer);
        }
        
    }
}
