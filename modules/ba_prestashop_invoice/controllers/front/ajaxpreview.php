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
* to license@buy-addons.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Buy-Addons <hatt@buy-addons.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
* @since 1.6
*/

class Ba_prestashop_invoiceAjaxPreviewModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public $pdf_renderer;
    
    public function run()
    {
        $this->ajaxPreview();
    }
    private function ajaxPreview()
    {
        $productsArr = array (
            '134' => array (
                '1' => 'Faded Short Sleeve Tshirts - Color : Orange, Size : S',
                '13' => 'Faded Short Sleeve T-shirts - Color : Orange, Size : S',
                '2' => '',
                '3' => '$16.51',
                '4' => '$17.17',
                '11' => '<span style="text-decoration: line-through;">$17,17</span><br/>$17,17',
                '5' => '$0.66',
                '6' => '--',
                '7' => '1',
                '14' => '$16.51',
                '8' => '$17.17',
                '9' => '1.jpg',
                '10' => '10%',
                '12' => 'demo_12',
                '15' => 'GTGT VN 10%',
                '19' => '1',
                '20' => '$16.51',
                '21' => 'Encona',
                '22' => '"200g',
            ),
            '135' => array (
                '1' => 'Blouse - Color : Black, Size : S',
                '13' => 'Blouse - Color : Black, Size :S',
                '2' => '',
                '3' => '$27.00',
                '4' => '$28.08',
                '11' => '<span style="text-decoration: line-through;">$28.08</span><br/>$28.08',
                '5' => '$1.08',
                '6' => '--',
                '7' => '2',
                '14' => '$27.00',
                '8' => '$28.08',
                '9' => '7.jpg',
                '10' => '5%',
                '12' => 'demo_14',
                '15' => 'GTGT VN 5%',
                '19' => '2',
                '20' => '$16.51',
                '21' => 'Encona',
                '22' => '"200g',
            ),
            '136' => array (
                '1' => 'Printed Dress - Color :Orange, Size : S',
                '13' => 'Printed Dress - Color :Orange, Size : S',
                '2' => '',
                '3' => '$26.00',
                '4' => '$27.04',
                '11' => '<span style="text-decoration: line-through;">$27.04</span><br/>$27.04',
                '5' => '$1.04',
                '6' => '--',
                '7' => '4',
                '14' => '$26.00',
                '8' => '$27.04',
                '9' => '8.jpg',
                '10' => '15%',
                '12' => 'demo_20',
                '15' => 'GTGT 15%',
                '19' => '3',
                '20' => '$16.51',
                '21' => 'Encona',
                '22' => '"200g',
            )
            
        );
        $ordersArray = array(
            '[invoice_date]' => '2016-01-28',
            '[invoice_number]' => 'IN000083',
            '[barcode_invoice_number]' => '000083',
            '[payment_transaction_id]' => '',
            '[gift_message]' => '',
            '[gift_wrapping_cost]' => '$0.00',
            '[cart_id]' => '107',
            '[reference]' => 'IPIGMFSHR',
            '[order_date]' => '2801-01-28',
            '[order_payment_method]' => 'Payment by check',
            '[order_carrier]' => 'Carrier 01',
            '[order_subtotal]' => '$69.51',
            '[order_shipping_cost]' => '$0.00',
            '[order_tax]' => '$6.90',
            '[order_discounted]' => '$0.00',
            '[order_total]' => '$76.41',
            '[order_total_not_discount_excl]' => '$69.51',
            '[order_total_not_discount_incl]' => '$76.41',
            '[order_notes]' => '',
            '[order_message]' => '',
            '[customer_email]' => 'demo@gmail.com',
            '[customer_outstanding_amount]' => '$10.05',
            '[customer_max_payment_days]' => '7',
            '[customer_risk_rating]' => 'Low',
            '[customer_company]' => 'Prestashop Store',
            '[customer_siret]' => 'SIRET',
            '[customer_ape]' => 'APE',
            '[customer_website]' => 'http://prestashop.com',
            '[billing_due_date]' => '2016-02-04',
            
            '[billing_firstname]' => 'John',
            '[billing_lastname]' => 'DOE',
            '[billing_company]' => 'Demo',
            '[billing_address]' => 'Hà Nội Việt Nam',
            '[billing_address_line_2]' => '',
            '[billing_zipcode]' => '100000',
            '[billing_city]' => 'Hà Nội',
            '[billing_state]' => '',
            '[billing_country]' => 'Việt Nam',
            '[billing_homephone]' => '123456789',
            '[billing_mobile_phone]' => '987654321',
            '[billing_additional_infomation]' => '',
            '[billing_vat_number]' => '12345',
            '[billing_dni]' => '',
            
            '[delivery_firstname]' => 'John',
            '[delivery_lastname]' => 'DOE',
            '[delivery_company]' => 'Demo',
            '[delivery_address]' => 'Hà Nội Việt Nam',
            '[delivery_address_line_2]' => '',
            '[delivery_zipcode]' => '100000',
            '[delivery_city]' => 'Hà Nội',
            '[delivery_state]' => '',
            '[delivery_country]' => 'Việt Nam',
            '[delivery_homephone]' => '123456789',
            '[delivery_mobile_phone]' => '987654321',
            '[delivery_additional_infomation]' => '',
            '[delivery_vat_number]' => '12345',
            '[delivery_dni]' => '',
            '[delivery_date]' => '2016-01-29',
            
            '[products_list]' => '',
            
            '[total_product_excl_tax]' => '$69.51',
            '[total_product_tax_rate]' => '10%',
            '[total_product_tax_amount]' => '$6.90',
            '[total_product_incl_tax]' => '$76.41',
            '[shipping_cost_excl_tax]' => '$0.00',
            '[shipping_cost_tax_rate]' => '0%',
            '[shipping_cost_tax_amount]' => '$0.00',
            '[shipping_cost_incl_tax]' => '$0.00',
            '[total_order_excl_tax]' => '$69.51',
            '[total_order_tax_amount]' => '$6.90',
            '[total_order_incl_tax]' => '$76.41',
            
            '[COD_fees_include]' => '',
            '[COD_fees_exclude]' => '',
            '[COD_tax]' => '',
            '[tax_table]' => '
                <table id="table_tax" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="table_tax_title">Tax Detail</th>
                            <th class="table_tax_title">Tax %</th>
                            <th class="table_tax_title">Pre-Tax Total</th>
                            <th class="table_tax_title">Total Tax</th>
                            <th class="table_tax_title">Total with Tax</th>
                        </tr>
                    </thead>
                    <tbody><tr>
                            <td class="table_tax_content">Products</td>
                        <td class="table_tax_content">10%</td><td class="table_tax_content">$16.51</td>
                        <td class="table_tax_content">$1.65</td><td class="table_tax_content">$18.16</td>
                        </tr><tr>
                            <td class="table_tax_content">Products</td>
                        <td class="table_tax_content">5%</td><td class="table_tax_content">$27.00</td>
                        <td class="table_tax_content">$1.35</td><td class="table_tax_content">$28.35</td>
                        </tr><tr>
                            <td class="table_tax_content">Products</td>
                        <td class="table_tax_content">15%</td><td class="table_tax_content">$26.00</td>
                        <td class="table_tax_content">$3.90</td><td class="table_tax_content">$29.90</td>
                        </tr></tbody>
                </table>
                ',
            '[individual_tax_table]' => '
                <table id="table_tax_group_by_id_tax" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th class="title_tax">Individual Taxes</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <tr><td class="content_tax">GTGT VN 10%</td><td class="content_tax">10%</td>
                <td class="content_tax">$16.51</td><td class="content_tax">$1.65</td>
                <td class="content_tax">$18.16</td></tr>
                <tr><td class="content_tax">GTGT VN 5%</td><td class="content_tax">5%</td>
                <td class="content_tax">$27.00</td><td class="content_tax">$1.35</td>
                <td class="content_tax">$28.35</td></tr>
                <tr><td class="content_tax">GTGT 15%</td>
                <td class="content_tax">15%</td>
                <td class="content_tax">$26.00</td>
                <td class="content_tax">$3.90</td>
                <td class="content_tax">$29.90</td></tr>
                <tr><td class="total content_tax">Total</td>
                <td class="total content_tax"> </td>
                <td class="total content_tax">$69.51</td>
                <td class="total content_tax">$6.90</td>
                <td class="total content_tax">$76.41</td>
                </tr></tbody></table>
            ',
            
        );
        
        
        @unlink(_PS_ROOT_DIR_.'/ba_invoice_preview.pdf');

        //$sel_language = Tools::getValue('sel_language');
        $invoice_template=Tools::getValue('invoice_template');
        $header_invoice_template=Tools::getValue('header_invoice_template');
        $footer_invoice_template=Tools::getValue('footer_invoice_template');
        $customize_css=Tools::getValue('customize_css');
        $numberColumn = (int) Tools::getValue('numberColumnOfTableTemplaterPro');
        $colums_title = Tools::getValue('colums_title');
        
        $colums_content = Tools::getValue('colums_content');
        
        $colums_color = Tools::getValue('colums_color');
        
        $colums_bgcolor = Tools::getValue('colums_bgcolor');
        
        //$showShippingInProductList=(Tools::getIsset("showShippingInProductList")==true)?'Y':'N';
        //$showDiscountInProductList=(Tools::getIsset("showDiscountInProductList")==true)?'Y':'N';
        $baInvoiceEnableLandscape=(Tools::getIsset("baInvoiceEnableLandscape")==true)?'Y':'N';
        $showPagination=(Tools::getIsset("showPagination")==true)?'Y':'N';


        

        $this->pdf_renderer = new PDFGenerator(false);
        $html_product_list = '
            <style>
                '.$customize_css.'
            </style>
            <table 
            id="product_list_tempalte_invoice" 
            style="width:100%;margin-top:27pt;" 
            cellpadding="0" cellspacing="0">
            
        ';
        $html_product_list.="<tr>";
        for ($i = 0; $i < $numberColumn; $i++) {
            if ($colums_content[$i]=="7" || $colums_content[$i]=="6") {
                $html_product_list.=
                "<th style='color:#" . $colums_color[$i] . ";
                background-color:#" . $colums_bgcolor[$i] . ";' class='product_list_title product_list_col_"
                .($i+1)." product_list_title_".($i+1)."'>
                " . $colums_title[$i] . "</th>";
            } elseif ($colums_content[$i]=="11" || $colums_content[$i]=="8") {
                $html_product_list.=
                "<th style='color:#" . $colums_color[$i] . ";
                background-color:#" . $colums_bgcolor[$i] . ";' class='product_list_title product_list_col_"
                .($i+1)." product_list_title_".($i+1)."'>
                " . $colums_title[$i] . "</th>";
            } else {
                $html_product_list.=
                "<th style='color:#" . $colums_color[$i] . ";
                background-color:#" . $colums_bgcolor[$i] . ";' class='product_list_title product_list_col_"
                .($i+1)." product_list_title_".($i+1)."'>
                " . $colums_title[$i] . "</th>";
            }
           
        }
        $html_product_list.="</tr>";
        // var_dump($colums_content);die;
        foreach ($productsArr as $pro) {
            $html_product_list.='<tr>';
            for ($j = 0; $j < $numberColumn; $j++) {
                $html_product_list.= $this->checkContentType($colums_content[$j], $pro, $j);
            }
            $html_product_list.='</tr>';
            
        }
        
        $html_product_list.="</table>";
        // var_dump($html_product_list);die;
        foreach ($ordersArray as $key => $orders) {
            if ($key=="[products_list]") {
                $invoice_template = str_replace($key, $html_product_list, $invoice_template);
                $header_invoice_template = str_replace($key, $html_product_list, $header_invoice_template);
                $footer_invoice_template = str_replace($key, $html_product_list, $footer_invoice_template);
            }
            if ($key=="[barcode_invoice_number]") {
                $invoiceNumberBarcode='<barcode code="'.$orders.'" type="C128C" class="barcode" />';
                $invoice_template = str_replace($key, $invoiceNumberBarcode, $invoice_template);
                $header_invoice_template = str_replace($key, $invoiceNumberBarcode, $header_invoice_template);
                $footer_invoice_template = str_replace($key, $invoiceNumberBarcode, $footer_invoice_template);
            }
            $invoice_template = str_replace($key, $orders, $invoice_template);
            $header_invoice_template = str_replace($key, $orders, $header_invoice_template);
            $footer_invoice_template = str_replace($key, $orders, $footer_invoice_template);

        }

        //$showPagination = 'N';
        //$template = new HTMLTemplateInvoice($object, Context::getContext()->smarty);
        $this->pdf_renderer->setFontForLang(Context::getContext()->language->iso_code);
        $this->pdf_renderer->createHeader($header_invoice_template);
        $this->pdf_renderer->createFooter($footer_invoice_template);
        $this->pdf_renderer->createContent($invoice_template);
        $this->configMPDF($baInvoiceEnableLandscape, $showPagination);
        $this->pdf_renderer->writePage();
   
        // clean the output buffer
        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }
        echo $this->pdf_renderer->render("ba_invoice_preview.pdf", 'F');
        die;
        
        
        //$this->generatProductList($productsArr);die;
    }
    
    public function configMPDF($landscape = null, $showPagination = 'N')
    {
        if ($landscape=="Y") {
            $this->pdf_renderer->mpdf->AddPage("L");
        } else {
            $this->pdf_renderer->mpdf->AddPage("P");
        }
        
        if ($showPagination=="Y") {
            $this->pdf_renderer->mpdf->setFooter($this->module->returnFooterText());
        }
    }
    
    
    public function checkContentType($content_type, $product, $j)
    {
        //var_dump($product[$content_type[$j]])."<br/>";
        $html = "";
        switch ($content_type)
        {
            case "1":
                $html.='<td class="product_list_content product_name product_list_content_'.($j+1)
                .' product_list_col_'.($j+1).'" >';
                $html.= $product[1];
                $html.='</td>';
                break;
            case "2":
                $html.='<td class="product_list_content product_list_content_'.($j+1).' product_list_col_'.($j+1).'" >';
                $html .= $product[2];
                $html.='</td>';
                break;
            case "3":
                $html.='<td class="product_list_content product_list_content_'.($j+1).' product_list_col_'.($j+1).'">';
                $html .= $product[3];
                $html.='</td>';
                break;
            case "4":
                $html.='<td class="product_list_content product_price_without_old_price product_list_content_'.($j+1)
                .' product_list_col_'.($j+1).'" 
                >';
                $html.= $product[4];
                $html.='</td>';
                break;
            case "5":
                $html.='<td class="product_list_content product_list_content_'.($j+1).' product_list_col_'.($j+1).'" 
                >';
                $html.= $product[5];
                $html.='</td>';
                break;
            case "6":
                $html.='<td align="center" class="product_list_content product_list_content_'.($j+1)
                .' product_list_col_'.($j+1).'" >';
                $html.= $product[6];

                $html.='</td>';
                break;
            case "7":
                $html.='<td class="product_list_content content_QTY product_list_content_'.($j+1)
                .' product_list_col_'.($j+1).'" >';
                $html.= $product[7];
                $html.='</td>';
                break;
            case "8":
                $html.='<td class="product_list_content product_total product_list_content_'.($j+1)
                .' product_list_col_'.($j+1).'" >';
                $html.= $product[8];
                $html.='</td>';
                break;
            case "9":
                $html.='<td class="product_list_content product_list_content_'.($j+1)
                .' product_list_col_'.($j+1).'" >';
                $html.='<img class="product_img" src="'.__PS_BASE_URI__
                .'modules/ba_prestashop_invoice/views/img/'.$product[9].'" alt=""/>';
                $html.='</td>';
                break;
            case "10":
                $html.='<td class="product_list_content product_list_content_'.($j+1).' product_list_col_'.($j+1).'" >';
                $html.= $product[10];
                $html.='</td>';
                break;
            case "11":
                $html.='<td class="product_list_content product_list_content_'.($j+1).' product_list_col_'.($j+1).'" >';
                $html.=$product[11];
                $html.='</td>';
                break;
            case "12":
                $html.='<td class="product_list_content product_list_content_'.($j+1).' product_list_col_'.($j+1).'" >';
                $html.=$product[12];
                $html.='</td>';
                break;
            case "13":
                $html.='<td class="product_list_content product_list_content_'.($j+1).' product_list_col_'.($j+1).'">';
                $html.=$product[13];
                $html.='</td>';
                break;
            case "14":
                $html.='<td class="produc_total_tax_excl product_list_content product_list_content_'
                .($j+1).' product_list_col_'.($j+1).'">';
                $html .= $product[14];
                $html.='</td>';
                break;
            case "15":
                $html.='<td class="product_tax_name product_list_content product_list_content_'
                .($j+1).' product_list_col_'.($j+1).'">';
                $html.= $product[15];
                $html.='</td>';
                break;
            case "19": 
                $html.='<td class="product_tax_name product_list_content product_list_content_'
                .($j+1).' product_list_col_'.($j+1).'">'.$product[19].'</td>';
                break;
             case "20": 
                $html.='<td class="product_tax_name product_list_content product_list_content_'
                .($j+1).' product_list_col_'.($j+1).'">'.$product[20].'</td>';
                break;
              case "21": 
                $html.='<td class="product_tax_name product_list_content product_list_content_'
                .($j+1).' product_list_col_'.($j+1).'">'.$product[21].'</td>';
                break;
              case "22": 
                $html.='<td class="product_tax_name product_list_content product_list_content_'
                .($j+1).' product_list_col_'.($j+1).'">'.$product[22].'</td>';
                break;
        }
        return ($html);
    }
}
