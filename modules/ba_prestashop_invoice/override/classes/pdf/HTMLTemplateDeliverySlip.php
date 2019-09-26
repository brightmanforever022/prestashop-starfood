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
 */

class HTMLTemplateDeliverySlip extends HTMLTemplateDeliverySlipCore
{
 
    public $order;
    public $htmlTemplate;

    
    public function __construct(OrderInvoice $order_invoice, $smarty)
    {
        if (Module::isEnabled('ba_prestashop_invoice')==false) {
            return parent::__construct($order_invoice, $smarty);
        }
        $this->order_invoice = $order_invoice;
        $this->order = new Order($this->order_invoice->id_order);
        $this->smarty = $smarty;
        $this->date = $this->displayDate($this->order->invoice_date);
        $this->title = HTMLTemplateDeliverySlip::l('Delivery')
        .' #'.Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id)
        .sprintf('%06d', $this->order_invoice->delivery_number);
        $this->shop = new Shop((int)$this->order->id_shop);
        $db = Db::getInstance();
        $sql='SELECT * FROM '._DB_PREFIX_.'ba_prestashop_delivery_slip WHERE id_lang='
        .(int)$this->order->id_lang.' AND status=1 AND id_shop='.$this->order->id_shop;
        $this->htmlTemplate = $db->ExecuteS($sql);
    }
    
    public function taxGroup()
    {
        $orderIdCurrency=(int)$this->order->id_currency;
        $sql = 'SELECT * FROM '._DB_PREFIX_.'ba_prestashop_invoice_tax WHERE id_order='.(int)$this->order->id;
        $taxGroup = Db::getInstance()->ExecuteS($sql);
        if (empty($taxGroup)) {
            return;
        }
        $taxArr=array();
        for ($i = 0; $i < count($taxGroup); $i ++) {
            $id_tax = $taxGroup[$i]['id_tax'];
            if (isset($taxArr[$id_tax])) {
                $taxArr[$id_tax]['tax_amount']+=$taxGroup[$i]['tax_amount']*$taxGroup[$i]['product_qty'];
                $taxArr[$id_tax]['unit_price_tax_excl']+=
                $taxGroup[$i]['unit_price_tax_excl']*$taxGroup[$i]['product_qty'];
                $taxArr[$id_tax]['unit_price_tax_incl']+=
                $taxGroup[$i]['unit_price_tax_incl']*$taxGroup[$i]['product_qty'];
            } else {
                $taxArr[$id_tax]=array(
                    'id_order'=>$taxGroup[$i]['id_order'],
                    'id_tax'=>$taxGroup[$i]['id_tax'],
                    'tax_name'=>$taxGroup[$i]['tax_name'],
                    'tax_rate'=>$taxGroup[$i]['tax_rate'],
                    'tax_amount'=>$taxGroup[$i]['tax_amount']*$taxGroup[$i]['product_qty'],
                    'unit_price_tax_excl'=>$taxGroup[$i]['unit_price_tax_excl']*$taxGroup[$i]['product_qty'],
                    'unit_price_tax_incl'=>$taxGroup[$i]['unit_price_tax_incl']*$taxGroup[$i]['product_qty'],
                );
            }
        }
        $html='
        <table id="table_tax_group_by_id_tax" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="title_tax">'.HTMLTemplateInvoice::l('Individual Taxes').'</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            ';
        $total_product_tax_excl = 0;
        $total_product_tax_amount = 0;
        $total_product_tax_incl = 0;
        foreach ($taxArr as $tax) {
            $total_product_tax_excl +=$tax['unit_price_tax_excl'];
            $total_product_tax_amount +=$tax['tax_amount'];
            $total_product_tax_incl +=$tax['unit_price_tax_incl'];
            $html.='<tr>';
                $html.='<td class="content_tax">'.$tax['tax_name'].'</td>';
                $html.='<td class="content_tax">'.round($tax['tax_rate'], 2).'%</td>';
                $html.='<td class="content_tax">'
                .Tools::displayPrice($tax['unit_price_tax_excl'], $orderIdCurrency).'</td>';
                $html.='<td class="content_tax">'.Tools::displayPrice($tax['tax_amount'], $orderIdCurrency).'</td>';
                $html.='<td class="content_tax">'
                .Tools::displayPrice($tax['unit_price_tax_incl'], $orderIdCurrency).'</td>';
            $html.='</tr>';
            
        }
        $html.='<tr>';
            $html.='<td class="total content_tax">'.HTMLTemplateInvoice::l('Total').'</td>';
            $html.='<td class="total content_tax"> </td>';
            $html.='<td class="total content_tax">'
            .Tools::displayPrice($total_product_tax_excl, $orderIdCurrency).'</td>';
            $html.='<td class="total content_tax">'
            .Tools::displayPrice($total_product_tax_amount, $orderIdCurrency).'</td>';
            $html.='<td class="total content_tax">'
            .Tools::displayPrice($total_product_tax_incl, $orderIdCurrency).'</td>';
        $html.='</tr>';
        $html.='</tbody></table>';
        return $html;
        
    }
   

    
    public function getContent()
    {
        if (Module::isEnabled('ba_prestashop_invoice')==false || get_class($this)!='HTMLTemplateDeliverySlip') {
            return parent::getContent();
            
        }
        
        $html = HTMLTemplateDeliverySlip::l('Do not Invoice Template actived for this store');
        if (!empty($this->htmlTemplate)) {
            $html = Tools::htmlentitiesDecodeUTF8($this->htmlTemplate[0]['invoice_template']);
        }
        return $this->replaceToken($html);
       // return $html;
    }

    private function checkContentType($content_type, $product, $j)
    {
        
        $html = "";
        switch ($content_type)
        {
            case "1":
                $html.='<td class="product_list_content product_name product_list_content_'.($j+1)
                .' product_list_col_'.($j+1).'" >';
               
                if (Pack::isPack($product['product_id'])) {
                    $html.= "<strong>".$product['product_name']
                    ."</strong><span style='color:#555;font-family:arial;font-size:10pt;'>"
                    .$product['product_supplier_reference']
                    .'</span>';
                    $itemPack = Pack::getItems($product['product_id'], $this->order->id_lang);
                    foreach ($itemPack as $item) {
                        $sku="";
                        if (!empty($item->ean13)) {
                            $sku=HTMLTemplateInvoice::l('SKU').': '.$item->ean13;
                        }
                        $html.='<p>'.$sku.' ['.$item->name.'] '.HTMLTemplateInvoice::l('x').' '
                        .$item->pack_quantity.'</p>';
                    }
                } else {
                    $html.= $product['product_name']."<br/><span style='color:#555;font-family:arial;font-size:10pt;'>"
                    .$product['product_supplier_reference']
                    .'</span>';
                }
                $html.='</td>';
                break;
            case "2":
                $html.='<td class="product_list_content product_list_content_'.($j+1).' product_list_col_'.($j+1).'" >';
                $html .= $product['product_ean13'];
                $html.='</td>';
                break;
            case "3":
                //echo '<pre>';var_dump ($product);die;
                $html.='<td class="product_list_content product_list_content_'.($j+1).' product_list_col_'.($j+1).'">';
                $html .= Tools::displayPrice($product['unit_price_tax_excl'], (int) $this->order->id_currency);
                if (isset($product['ecotax_tax_excl']) && $product['ecotax_tax_excl']>0) {
                    $html .= '<div>'.HTMLTemplateInvoice::l('Ecotax: ').
                        Tools::displayPrice($product['ecotax_tax_excl'], (int) $this->order->id_currency).'</div>';
                }
                $html.='</td>';
                break;
            case "4":
                $html.='<td class="product_list_content product_price_without_old_price product_list_content_'.($j+1)
                .' product_list_col_'.($j+1).'" 
                >';
                $html .= Tools::displayPrice($product['unit_price_tax_incl'], (int) $this->order->id_currency);
                if (isset($product['ecotax_tax_incl']) && $product['ecotax_tax_incl']>0) {
                    $html .= '<div>'.HTMLTemplateInvoice::l('Ecotax: ').
                        Tools::displayPrice($product['ecotax_tax_incl'], (int) $this->order->id_currency).'</div>';
                }
                $html.='</td>';
                break;
            case "5":
                $html.='<td class="product_list_content product_list_content_'.($j+1).' product_list_col_'.($j+1).'" 
                >';
                $total_price_tax_incl=$product['total_price_tax_incl'];
                $total_price_tax_excl=$product['total_price_tax_excl'];
                $product_tax=$total_price_tax_incl-$total_price_tax_excl;
                $html .= Tools::displayPrice($product_tax, (int) $this->order->id_currency);
                $html.='</td>';
                break;
            case "6":
                $html.='<td align="center" class="product_list_content product_list_content_'.($j+1)
                .' product_list_col_'.($j+1).'" >';
                if (isset($product['reduction_amount']) && $product['reduction_amount']>0) {
                    $html .= "-".Tools::displayPrice($product['reduction_amount'], (int) $this->order->id_currency);
                } elseif (isset($product['reduction_percent']) && $product['reduction_percent']>0) {
                    $html .= "-".$product['reduction_percent']."%";
                } else {
                    $html .= "--";
                }
                $html.='</td>';
                break;
            case "7":
                $html.='<td class="product_list_content content_QTY product_list_content_'.($j+1)
                .' product_list_col_'.($j+1).'" >';
                $html .= $product['product_quantity'];
                $html.='</td>';
                break;
            case "8":
                $html.='<td class="product_list_content product_total product_list_content_'.($j+1)
                .' product_list_col_'.($j+1).'" >';
                $html .= Tools::displayPrice($product['total_price_tax_incl'], (int) $this->order->id_currency);
                $html.='</td>';
                break;
            case "9":
                if ($product['product_id']!="") {
                    $id_image = Product::getCover($product['product_id']);
                    $image = new Image($id_image['id_image']);
                    $image_url=_PS_PROD_IMG_DIR_.$image->getImgPath().'-'.ImageType::getFormatedName("small").'.jpg';
                    $html.='<td class="product_list_content product_list_content_'.($j+1)
                    .' product_list_col_'.($j+1).'" >';
                    if (file_exists($image_url)) {
                        $html.="<img class='product_img' src='".$image_url."' alt='' >";
                    } else {
                        $srcNoImges=_PS_ROOT_DIR_."/modules/ba_prestashop_invoice/views/img/noimage.jpg";
                        $html.="<img class='product_img' src='".$srcNoImges."' alt=''>";
                    }
                } else {
                    $html.='<td class="product_list_content product_list_content_'.($j+1)
                    .' product_list_col_'.($j+1).'" >';
                    $srcNoImges=_PS_ROOT_DIR_."/modules/ba_prestashop_invoice/views/img/noimage.jpg";
                    $html.="<p style='width:100%;'>
                    <img class='product_img' src='".$srcNoImges."' alt=''>
                    </p>";
                }
                $html.='</td>';
                break;
            case "10":
                $html.='<td class="product_list_content product_list_content_'.($j+1).' product_list_col_'.($j+1).'" >';
                $total_price_tax_incl=$product['total_price_tax_incl'];
                $total_price_tax_excl=$product['total_price_tax_excl'];
                $taxRate = (($total_price_tax_incl-$total_price_tax_excl)/$total_price_tax_excl)*100;
                $html .= round($taxRate, 2)."%";
                $html.='</td>';
                break;
            case "11":
                $html.='<td class="product_list_content product_list_content_'.($j+1).' product_list_col_'.($j+1).'" >';
                $unit_price_tax_incl=$product['unit_price_tax_incl'];
                $unit_price_tax_excl=$product['unit_price_tax_excl'];
                $taxRate = (($unit_price_tax_incl-$unit_price_tax_excl)/$unit_price_tax_excl)*100;
                $productPriceOld = $product['product_price'] + ($product['product_price']*($taxRate/100));
                if ($productPriceOld>$product['unit_price_tax_incl']) {
                    $html.="<span style='text-decoration: line-through;'>"
                        .Tools::displayPrice($productPriceOld, (int) $this->order->id_currency)
                        .'</span><br/>';
                }
                
                $html.="<span>"
                .Tools::displayPrice($product['unit_price_tax_incl'], (int) $this->order->id_currency)
                .'</span>';
                if (isset($product['ecotax_tax_incl']) && $product['ecotax_tax_incl']>0) {
                    $html .= '<div>'.HTMLTemplateInvoice::l('Ecotax: ').
                        Tools::displayPrice($product['ecotax_tax_incl'], (int) $this->order->id_currency).'</div>';
                }
                $html.='</td>';
                break;
            case "12":
                $html.='<td class="product_list_content product_list_content_'.($j+1).' product_list_col_'.($j+1).'" >';
                $html.=$product['product_reference'];
                $html.='</td>';
                break;
            case "13":
                $html.='<td class="product_list_content product_list_content_'.($j+1).' product_list_col_'.($j+1).'">';
                $productName=$product['product_name'];
                $html.=$productName."<br/>";
                foreach ($product['customizedDatas'] as $customizationPerAddress) {
                    foreach ($customizationPerAddress as $customization) {
                        if (isset($customization['datas'][_CUSTOMIZE_TEXTFIELD_])
                        && count($customization['datas'][_CUSTOMIZE_TEXTFIELD_]) > 0) {
                            foreach ($customization['datas'][_CUSTOMIZE_TEXTFIELD_] as $customization_infos) {
                                $html.=$customization_infos['name'].": ";
                                $html.=$customization_infos['value']."<br/>";
                            }
                        }
                        if (isset($customization['datas'][_CUSTOMIZE_FILE_])
                        && count($customization['datas'][_CUSTOMIZE_FILE_]) > 0) {
                            $html.= "image(s): ";
                            $html.=count($customization['datas'][_CUSTOMIZE_FILE_]);
                        }
                    }
                }
                $html.='</td>';
                break;
            case "14":
                $html.='<td class="product_list_content product_list_content_'.($j+1).' product_list_col_'.($j+1).'">';
                $html .= Tools::displayPrice($product['total_price_tax_excl'], (int) $this->order->id_currency);
                $html.='</td>';
                break;
            case "15":
                $html.='<td class="product_tax_name product_list_content product_list_content_'.($j+1)
                .' product_list_col_'.($j+1).'">';
                $taxObj = new Tax((int)$product['id_tax_rules_group'], (int)$this->order->id_lang);
                $html .= $taxObj->name;
                $html.='</td>';
                break;
            case "16":
                $html.='<td class="product_tax_name product_list_content product_list_content_'.($j+1)
                .' product_list_col_'.($j+1).'">';
                $unit_price_tax_incl=$product['unit_price_tax_incl'];
                $unit_price_tax_excl=$product['unit_price_tax_excl'];
                $taxRate = (($unit_price_tax_incl-$unit_price_tax_excl)/$unit_price_tax_excl)*100;
                $productPriceOld = $product['product_price'] + ($product['product_price']*($taxRate/100));
                $discount = $productPriceOld - $unit_price_tax_incl;
                $priceTaxExclNotDiscount=$discount+$unit_price_tax_excl;
                $html .= Tools::displayPrice($priceTaxExclNotDiscount, (int) $this->order->id_currency);
                if (isset($product['ecotax_tax_excl']) && $product['ecotax_tax_excl']>0) {
                    $html .= '<div>'.HTMLTemplateInvoice::l('Ecotax: ').
                        Tools::displayPrice($product['ecotax_tax_excl'], (int) $this->order->id_currency).'</div>';
                }
                $html.='</td>';
                break;
            case "17":
                $html.='<td class="product_tax_name product_list_content product_list_content_'.($j+1)
                .' product_list_col_'.($j+1).'">';
                $unit_price_tax_incl=$product['unit_price_tax_incl'];
                $unit_price_tax_excl=$product['unit_price_tax_excl'];
                $taxRate = (($unit_price_tax_incl-$unit_price_tax_excl)/$unit_price_tax_excl)*100;
                $productPriceOld = $product['product_price'] + ($product['product_price']*($taxRate/100));
                $discount = $productPriceOld - $unit_price_tax_incl;
                $totalPriceTaxExclNotDiscount=($discount+$unit_price_tax_excl) * $product['product_quantity'];
                $html .= Tools::displayPrice($totalPriceTaxExclNotDiscount, (int) $this->order->id_currency);
                $html.='</td>';
                break;
            case "18": // Product Warehouse Location
                require_once(_PS_MODULE_DIR_ . "ba_prestashop_invoice/ba_prestashop_invoice.php");
                $product_id = $product['product_id'];
                $attribute_id = $product['product_attribute_id'];
                $ws_locations = ba_prestashop_invoice::getWarehousesByProductId($product_id, $attribute_id);
                $warehouse_locations = array();
                if (!empty($ws_locations)) {
                    foreach ($ws_locations as $ws) {
                        if (empty($ws['location'])) {
                            $warehouse_locations[] = $ws['name'];
                        } else {
                            $warehouse_locations[] = $ws['name'].' ('.$ws['location'].')';
                        }
                    }
                }
                //echo '<pre>';print_r($warehouse_locations);die;
                $html.='<td class="product_tax_name product_list_content product_list_content_'.($j+1)
                .' product_list_col_'.($j+1).'">';
                $html.=implode(", ", $warehouse_locations);
                $html.='</td>';
                break;
        }
        return $html;
    }
   

    
    private function isUrlExist($url)
    {
        if (is_callable('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($code == 200) {
                $status = true;
            } else {
                $status = false;
            }
            curl_close($ch);
            return $status;
        }
        return true;
    }
    
   

   
    public function getFooter()
    {
        if (Module::isEnabled('ba_prestashop_invoice')==false || get_class($this)!='HTMLTemplateDeliverySlip') {
            return parent::getFooter();
        }
        $footerInvoiceTemplate="";
        if (!empty($this->htmlTemplate)) {
            $footerInvoiceTemplate=$this->htmlTemplate[0]['footer_invoice_template'];
            $footerInvoiceTemplate=Tools::htmlentitiesDecodeUTF8($footerInvoiceTemplate);
        }
        
        return $this->replaceToken($footerInvoiceTemplate);
    }
    
    
    public function getHeader()
    {
        if (Module::isEnabled('ba_prestashop_invoice')==false || get_class($this)!='HTMLTemplateDeliverySlip') {
            return parent::getHeader();
        }
        $headerInvoiceTemplate="";
        if (!empty($this->htmlTemplate)) {
            $headerInvoiceTemplate=$this->htmlTemplate[0]['header_invoice_template'];
            $headerInvoiceTemplate=Tools::htmlentitiesDecodeUTF8($headerInvoiceTemplate);
        }
        return $this->replaceToken($headerInvoiceTemplate);
    }
    

    
    private function baGetTaxBreakdown()
    {
        $breakdowns = array(
            'product_tax' => $this->order_invoice->getProductTaxesBreakdown($this->order),
            'shipping_tax' => $this->bagetShippingTaxes($this->order),
        );
        foreach ($breakdowns as $type => $bd) {
            if (empty($bd)) {
                unset($breakdowns[$type]);
            }
        }
        if (empty($breakdowns)) {
            $breakdowns = false;
        }
        if (isset($breakdowns['product_tax'])) {
            foreach ($breakdowns['product_tax'] as $key => &$bd) {
                $bd['total_tax_excl'] = $bd['total_price_tax_excl'];
                if (empty($bd['rate'])) {
                    $bd['rate'] = $key;
                }
            }
        }
        foreach ($breakdowns as &$breakdown) {
            foreach ($breakdown as &$bd) {
                $bd['total_tax_incl'] = $bd['total_tax_excl'] + $bd['total_amount'];
            }
        }
        return $breakdowns;
    }
    
    

   
    public function tableTax()
    {
        $tax_breakdowns = $this->baGetTaxBreakdown();
        if (empty($tax_breakdowns)) {
            return;
        }
        $html='
        <table id="table_tax" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="table_tax_title">'.HTMLTemplateInvoice::l('Tax Detail').'</th>
                    <th class="table_tax_title">'.HTMLTemplateInvoice::l('Tax %').'</th>
                    <th class="table_tax_title">'.HTMLTemplateInvoice::l('Pre-Tax Total').'</th>
                    <th class="table_tax_title">'.HTMLTemplateInvoice::l('Total Tax').'</th>
                    <th class="table_tax_title">'.HTMLTemplateInvoice::l('Total with Tax').'</th>
                </tr>
            </thead>
            <tbody>';
        foreach ($tax_breakdowns as $label => $bd) {
            foreach ($bd as $line) {
                if ($line['rate'] == 0) {
                    continue;
                }
                $html.='<tr>
                    <td class="table_tax_content">';
                if ($label == 'product_tax') {
                    $html.=HTMLTemplateInvoice::l('Products');
                } elseif ($label == 'shipping_tax') {
                    $html.=HTMLTemplateInvoice::l('Shipping');
                } elseif ($label == 'ecotax_tax') {
                    $html.=HTMLTemplateInvoice::l('Ecotax');
                } elseif ($label == 'wrapping_tax') {
                    $html.=HTMLTemplateInvoice::l('Wrapping');
                }
                        
                $html.='</td>
                <td class="table_tax_content">';
                    $html.=round($line['rate'], 2).'%';
                $html.='</td>';
                $html.='<td class="table_tax_content">';
                    $html.=Tools::displayPrice($line['total_tax_excl'], (int)$this->order->id_currency);
                $html.='</td>';
                $html.='<td class="table_tax_content">';
                    $html.=Tools::displayPrice($line['total_amount'], (int)$this->order->id_currency);
                $html.='</td>';
                $html.='<td class="table_tax_content">';
                    $html.=Tools::displayPrice($line['total_tax_incl'], (int)$this->order->id_currency);
                $html.='</td>
            </tr>';
            }
        }
        $html.='</tbody>
        </table>';
        return $html;
    }
    
   

    
    public function isNewsletterRegistered($customer_email)
    {
        $sql = 'SELECT `email`
                FROM '._DB_PREFIX_.'newsletter
                WHERE `email` = \''.pSQL($customer_email).'\'
                AND id_shop = '.Context::getContext()->shop->id;
        if (Db::getInstance()->getRow($sql)) {
            return true;
        }
        $sql = 'SELECT `newsletter`
                FROM '._DB_PREFIX_.'customer
                WHERE `email` = \''.pSQL($customer_email).'\'
                AND id_shop = '.Context::getContext()->shop->id;
        if (!$registered = Db::getInstance()->getRow($sql)) {
            return false;
        }
        if ($registered['newsletter'] == '1') {
            return true;
        }
        return false;
    }
    
   

    
    public function tableNewsletter()
    {
        $customer = new CustomerCore($this->order->id_customer);
        $newsletter=HTMLTemplateInvoice::l('No');
        if ($this->isNewsletterRegistered($customer->email)) {
            $newsletter=HTMLTemplateInvoice::l('Yes');
        }
        $html='
        <table id="table_newsletter" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="table_newsletter_title">'.HTMLTemplateInvoice::l('Newsletters').'</th>
                </tr>
            </thead>
            <tbody>
        ';
        $html.='<tr>';
        $html.='<td class="table_newsletter_content">'.HTMLTemplateInvoice::l('Sign up for our newsletter').'</td>';
        $html.='<td class="table_newsletter_content">'.$newsletter.'</td></tr>';
        $html.='</tbody>
        </table>';
        return $html;
    }
    
    

    
    public function tableDiscount()
    {
        $cartRulesArr = $this->order->getCartRules();
        $html='
        <table id="table_discount" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="table_discount_title">'.HTMLTemplateInvoice::l('Discount').'</th>
                </tr>
            </thead>
            <tbody>
        ';
        foreach ($cartRulesArr as $cartRules) {
            
            $html.='<tr>';
                $html.='<td class="table_discount_content">';
                    $html.=$cartRules['name'];
                $html.='</td>';
                $html.='<td class="table_discount_content">';
                    $html.=Tools::displayPrice($cartRules['value'], (int)$this->order->id_currency);
                $html.='</td>
            </tr>';
        }
        $html.='</tbody>
        </table>';
        return $html;
        
    }

    
    public function replaceToken($html)
    {
        // Hook
        $displayPDFInvoice = Hook::exec('displayPDFInvoice', array('object' => $this->order_invoice));
        $html = str_replace("[displayPDFInvoice]", $displayPDFInvoice, $html);
        
        $displayPDFDeliverySlip = Hook::exec('displayPDFDeliverySlip', array('object' => $this->order_invoice));
        $html = str_replace("[displayPDFDeliverySlip]", $displayPDFDeliverySlip, $html);
        
        $orderIdCurrency=(int)$this->order->id_currency;
        $this->date = $this->displayDate($this->order_invoice->date_add);
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $search_order = 'SELECT * FROM ' . _DB_PREFIX_ . 'orders WHERE id_order = ' . $this->order_invoice->id_order;
        $order_an = $db->ExecuteS($search_order);
        $this->date2 = $this->displayDate($order_an['0']['delivery_date']);
        $COD_fees_include=0;
        $COD_fees_exclude=0;
        $taxCodAmount=0;
        $taxCodRate=0;
        if ($this->order->module == "bacodwithfees") {
            $sql = "SELECT * FROM "._DB_PREFIX_."bacodwithfees WHERE id_order=".(int)$this->order->id;
            $codFeesArr = Db::getInstance()->ExecuteS($sql);
            $COD_fees_include = $codFeesArr[0]['amount_fees'];
            $idTaxCodFees = (int)Configuration::get('taxOfFees');
            $taxObj = new Tax($idTaxCodFees);
            $taxCodRate = $taxObj->rate;
            $COD_fees_exclude = $COD_fees_include/(1+ ($taxCodRate/100));
            $taxCodAmount=$COD_fees_include-$COD_fees_exclude;
        }
        $html = str_replace("[individual_tax_table]", $this->taxGroup(), $html);
        $html = str_replace("[tax_table]", $this->tableTax(), $html);
        $html = str_replace("[COD_fees_include]", Tools::displayPrice($COD_fees_include, $orderIdCurrency), $html);
        $html = str_replace("[COD_fees_exclude]", Tools::displayPrice($COD_fees_exclude, $orderIdCurrency), $html);
        $html = str_replace("[taxCodAmount]", Tools::displayPrice($taxCodAmount, $orderIdCurrency), $html);
        $html = str_replace("[order_id]", $this->order->id, $html);
        $cart_id=$this->order->getCartIdStatic($this->order->id);
        $html = str_replace("[cart_id]", $cart_id, $html);
        
        $payment_fee_incl=0;
        $payment_fee_excl=0;
        $payment_fee_tax_amount=0;
        if ($this->order->payment_fee) {
            $payment_fee_incl = $this->order->payment_fee;
            $payment_fee_excl = $payment_fee_incl/(1+($this->order->payment_fee_rate/100));
            $payment_fee_tax_amount = $payment_fee_excl*($this->order->payment_fee_rate/100);
        }
        $payment_fee_incl+=$COD_fees_include;
        $payment_fee_excl+=$COD_fees_exclude;
        $payment_fee_tax_amount+=$taxCodAmount;
        
        $html = str_replace("[payment_fee_incl]", Tools::displayPrice($payment_fee_incl, $orderIdCurrency), $html);
        $html = str_replace("[payment_fee_excl]", Tools::displayPrice($payment_fee_excl, $orderIdCurrency), $html);
        $payment_fee_tax_amount=Tools::displayPrice($payment_fee_tax_amount, $orderIdCurrency);
        $html = str_replace("[payment_fee_tax_amount]", $payment_fee_tax_amount, $html);
        /*
        * Order message
        */
        $messagesArr=CustomerMessage::getMessagesByOrderId((int)$this->order->id, false);
        $htmlMessage=null;
        if (count($messagesArr)) {
            foreach ($messagesArr as $message) {
                $htmlMessage.="<p class='order_message'>";
                $htmlMessage.=$this->displayDate($message['date_add'])." / ";
                if (isset($message['elastname']) && $message['elastname']) {
                    $htmlMessage.=$message['efirstname']." ".$message['elastname'];
                } elseif ($message.clastname) {
                    $htmlMessage.=$message['cfirstname']." ".$message['clastname'];
                } else {
                    $htmlMessage.=Configuration::get('PS_SHOP_NAME');
                }
                $htmlMessage.="<br/> ".$message['message'];
                $htmlMessage.="</p>";
            }
        }
        $html = str_replace("[order_message]", $htmlMessage, $html);
        
        $total_paid_tax_excl = $this->order->total_products;
        $total_paid_tax_incl = $this->order->total_products_wt;
        $tokenTotalProductExclTax=Tools::displayPrice($total_paid_tax_excl, $orderIdCurrency);
        $html = str_replace("[total_product_excl_tax]", $tokenTotalProductExclTax, $html);
        $tokenTotalProductInclTax=Tools::displayPrice($total_paid_tax_incl, $orderIdCurrency);
        $html = str_replace("[total_product_incl_tax]", $tokenTotalProductInclTax, $html);
        $taxRateProduct=0;
        if ($total_paid_tax_excl>0) {
            $taxRateProduct=100*($total_paid_tax_incl - $total_paid_tax_excl)/$total_paid_tax_excl;
        }
        $html = str_replace("[total_product_tax_rate]", round($taxRateProduct, 2)."%", $html);
        $taxAmountProduct=$total_paid_tax_incl - $total_paid_tax_excl;
        $taxAmountProduct= Tools::displayPrice($taxAmountProduct, $orderIdCurrency);
        $html = str_replace("[total_product_tax_amount]", $taxAmountProduct, $html);
        
        $total_shipping_tax_incl=$this->order->total_shipping_tax_incl;
        $total_shipping_tax_excl=$this->order->total_shipping_tax_excl;
        $tokenShippingCostExclTax=Tools::displayPrice($total_shipping_tax_excl, $orderIdCurrency);
        $html = str_replace("[shipping_cost_excl_tax]", $tokenShippingCostExclTax, $html);
        $tokenShippingCostInclTax=Tools::displayPrice($total_shipping_tax_incl, $orderIdCurrency);
        $html = str_replace("[shipping_cost_incl_tax]", $tokenShippingCostInclTax, $html);
        $taxRateShipping=0;
        if ($total_shipping_tax_excl>0) {
            $taxRateShipping=100*($total_shipping_tax_incl - $total_shipping_tax_excl)/$total_shipping_tax_excl;
        }
        $html = str_replace("[shipping_cost_tax_rate]", round($taxRateShipping, 2)."%", $html);
        $taxAmountShipping=$total_shipping_tax_incl - $total_shipping_tax_excl;
        $taxAmountShipping= Tools::displayPrice($taxAmountShipping, $orderIdCurrency);
        $html = str_replace("[shipping_cost_tax_amount]", $taxAmountShipping, $html);
        
        
        $total_order_excl_tax=Tools::displayPrice($total_paid_tax_excl+$total_shipping_tax_excl, $orderIdCurrency);
        $html = str_replace("[total_order_excl_tax]", $total_order_excl_tax, $html);
        $taxAmountShipping=$total_shipping_tax_incl - $total_shipping_tax_excl;
        $taxAmountProduct=$total_paid_tax_incl - $total_paid_tax_excl;
        $total_order_tax_amount=$taxAmountShipping+$taxAmountProduct;
        $total_order_tax_amount=Tools::displayPrice($total_order_tax_amount, $orderIdCurrency);
        $html = str_replace("[total_order_tax_amount]", $total_order_tax_amount, $html);
        $total_order_incl_tax=Tools::displayPrice(($total_shipping_tax_incl+$total_paid_tax_incl), $orderIdCurrency);
        $html = str_replace("[total_order_incl_tax]", $total_order_incl_tax, $html);
        
        $html = str_replace("[invoice_date]", $this->date, $html);
        $id_lang = Context::getContext()->language->id;
        $deliveryNumber=Configuration::get('PS_DELIVERY_PREFIX', $id_lang)
        .sprintf('%06d', $this->order_invoice->delivery_number);
        $html = str_replace("[delivery_number]", $deliveryNumber, $html);
        $html = str_replace("[order_number]", $this->order->reference, $html);
        $html = str_replace("[order_date]", $this->displayDate($this->order->date_add), $html);
        $html = str_replace("[gift_message]", $this->order->gift_message, $html);
        $html = str_replace("[delivery_date]", $this->date2, $html);
        $gift_wrapping_cost=Tools::displayPrice($this->order->total_wrapping, $orderIdCurrency);
        $html = str_replace("[gift_wrapping_cost]", $gift_wrapping_cost, $html);
        $orderPaymentCollection = $this->order_invoice->getOrderPaymentCollection();
        if (!empty($orderPaymentCollection)) {
            foreach ($this->order_invoice->getOrderPaymentCollection() as $payment_method) {
                $html = str_replace("[order_payment_method]", $payment_method->payment_method, $html);
            }
        } else {
            $html = str_replace("[order_payment_method]", "", $html);
        }
        $carrier = new Carrier($this->order->id_carrier);
        $html = str_replace("[order_carrier]", $carrier->name, $html);
        
        $order_subtotal=$this->order->total_products + $this->order->total_shipping_tax_excl;
        $order_subtotal = Tools::displayPrice($order_subtotal, $orderIdCurrency);
        $html= str_replace("[order_subtotal]", $order_subtotal, $html);
        $order_shipping_cost=Tools::displayPrice($this->order_invoice->total_shipping_tax_incl, $orderIdCurrency);
        $html = str_replace("[order_shipping_cost]", $order_shipping_cost, $html);
        $totalTax = ($this->order_invoice->total_paid_tax_incl - $this->order_invoice->total_paid_tax_excl);
        $html = str_replace("[order_tax]", Tools::displayPrice($totalTax, $orderIdCurrency), $html);
        $productListObj = $this->order_invoice->getProducts();
        $orderDiscountedTotal=0;
        foreach ($productListObj as $productList) {
            $unit_price_tax_incl=round($productList['unit_price_tax_incl'], 2);
            $discountProduct=0;
            if (isset($productList['reduction_amount']) && $productList['reduction_amount']>0) {
                $discountProduct=$productList['reduction_amount']*$productList['product_quantity'];
            } elseif (isset($productList['reduction_percent']) && $productList['reduction_percent']>0) {
                $reduction_percent=$productList['reduction_percent'];
                $priceProductBase=$unit_price_tax_incl/(1-($reduction_percent/100));
                $discountProduct=($priceProductBase-$unit_price_tax_incl)*$productList['product_quantity'];
            }
            $orderDiscountedTotal+=$discountProduct;
        }
        $orderDiscountedTotal += $this->order_invoice->total_discount_tax_incl;
        
        
        $total_paid_tax_incl = $this->order->total_paid_tax_incl;
        $orderTotalNoDiscountIncl = $total_paid_tax_incl+$orderDiscountedTotal;
        $orderTotalNoDiscountIncl = Tools::displayPrice($orderTotalNoDiscountIncl, $orderIdCurrency);
        $html = str_replace("[order_total_not_discount_incl]", $orderTotalNoDiscountIncl, $html);
        
        $total_paid_tax_excl = $this->order->total_paid_tax_excl;
        $orderTotalNoDiscountExcl = $total_paid_tax_excl+$orderDiscountedTotal;
        $orderTotalNoDiscountExcl = Tools::displayPrice($orderTotalNoDiscountExcl, $orderIdCurrency);
        $html = str_replace("[order_total_not_discount_excl]", $orderTotalNoDiscountExcl, $html);
        
        $orderDiscountedTotal = Tools::displayPrice($orderDiscountedTotal, $orderIdCurrency);
        $html = str_replace("[order_discounted]", $orderDiscountedTotal, $html);
        $order_total= Tools::displayPrice($this->order_invoice->total_paid_tax_incl, $orderIdCurrency);
        $html = str_replace("[order_total]", $order_total, $html);
        $customer = new CustomerCore($this->order->id_customer);
        $html = str_replace("[customer_email]", $customer->email, $html);
        //////////
        $max_payment_days = (int) $customer->max_payment_days;
        $cus_amount = Tools::displayPrice($customer->outstanding_allow_amount, $orderIdCurrency);
        $html = str_replace("[customer_outstanding_amount]", $cus_amount, $html);
        $html = str_replace("[customer_max_payment_days]", $max_payment_days, $html);
        $html = str_replace("[customer_risk_rating]", $this->getRiskText($customer->id_risk), $html);
        $html = str_replace("[customer_company]", $customer->company, $html);
        $html = str_replace("[customer_siret]", $customer->siret, $html);
        $html = str_replace("[customer_ape]", $customer->ape, $html);
        $html = str_replace("[customer_website]", $customer->website, $html);
        //calc due date
        
        $billing_due_date = strtotime($this->order_invoice->date_add)+$max_payment_days*24*60*60;
        $billing_due_date = date("Y-m-d H:i:s", $billing_due_date);
        $billing_due_date = $this->displayDate($billing_due_date);
        
        $html = str_replace("[billing_due_date]", $billing_due_date, $html);
        ///////////
        $invoice_address = new Address((int) $this->order->id_address_invoice);
        $billingStateName = "";
        if (State::getNameById((int)$invoice_address->id_state) != false) {
            $billingStateName = State::getNameById((int)$invoice_address->id_state);
        }
        $html = str_replace("[billing_state]", $billingStateName, $html);
        $html = str_replace("[billing_firstname]", $invoice_address->firstname, $html);
        $html = str_replace("[billing_lastname]", $invoice_address->lastname, $html);
        $html = str_replace("[billing_company]", $invoice_address->company, $html);
        $html = str_replace("[billing_address]", $invoice_address->address1, $html);
        $html = str_replace("[billing_address_line_2]", $invoice_address->address2, $html);
        $html = str_replace("[billing_zipcode]", $invoice_address->postcode, $html);
        $billing_city=$invoice_address->city;
        $html = str_replace("[billing_city]", $billing_city, $html);
        $html = str_replace("[billing_country]", $invoice_address->country, $html);
        $html = str_replace("[billing_homephone]", $invoice_address->phone, $html);
        $html = str_replace("[billing_mobile_phone]", $invoice_address->phone_mobile, $html);
        $html = str_replace("[billing_additional_infomation]", $invoice_address->other, $html);
        $html = str_replace("[billing_vat_number]", $invoice_address->vat_number, $html);
        $html = str_replace("[billing_dni]", $invoice_address->dni, $html);
        $delivery_address = new Address((int) $this->order->id_address_delivery);
        $deliveryStateName = "";
        if (State::getNameById((int)$delivery_address->id_state) != false) {
            $deliveryStateName = State::getNameById((int)$delivery_address->id_state);
        }
        $html = str_replace("[delivery_state]", $deliveryStateName, $html);
        
        $html = str_replace("[delivery_firstname]", $delivery_address->firstname, $html);
        $html = str_replace("[delivery_lastname]", $delivery_address->lastname, $html);
        $html = str_replace("[delivery_company]", $delivery_address->company, $html);
        $html = str_replace("[delivery_address]", $delivery_address->address1, $html);
        $html = str_replace("[delivery_address_line_2]", $delivery_address->address2, $html);
        $html = str_replace("[delivery_zipcode]", $delivery_address->postcode, $html);
        $html = str_replace("[delivery_city]", $delivery_address->city, $html);
        $html = str_replace("[delivery_country]", $delivery_address->country, $html);
        $html = str_replace("[delivery_homephone]", $delivery_address->phone, $html);
        $html = str_replace("[delivery_mobile_phone]", $delivery_address->phone_mobile, $html);
        $html = str_replace("[delivery_additional_infomation]", $delivery_address->other, $html);
        $html = str_replace("[delivery_vat_number]", $delivery_address->vat_number, $html);
        $html = str_replace("[delivery_dni]", $delivery_address->dni, $html);
        
        $html = str_replace("[order_notes]", $this->order_invoice->note, $html);
        
        $invoiceNumberBarcode=sprintf('%06d', $this->order_invoice->number);
        $barcode='<barcode code="'.$invoiceNumberBarcode.'" type="C128C" class="barcode" />';
        $html = str_replace("[barcode_invoice_number]", $barcode, $html);
        foreach ($this->order->getOrderPaymentCollection() as $pament) {
            $html = str_replace("[payment_transaction_id]", $pament->transaction_id, $html);
            break;
        }
        $product_list = $this->order_invoice->getProducts();
        $showDiscountInProductList="N";
        if (!empty($this->htmlTemplate)) {
            $showDiscountInProductList=$this->htmlTemplate[0]['showDiscountInProductList'];
        }
        if ($showDiscountInProductList=="Y") {
            $discountList = array();
            foreach ($product_list as $productDiscount) {
                $discountList["product_id"] = "";
                $discountList["product_name"] = HTMLTemplateInvoice::l('Discount for')
                                                ." [".$productDiscount['product_name']."]";
                $discountList["product_ean13"] = "--";
                $discountList["unit_price_tax_excl"] = 0;
                $discountList["unit_price_tax_incl"] = 0;
                if (isset($productDiscount['reduction_amount']) && $productDiscount['reduction_amount']>0) {
                    $discountList["unit_price_tax_excl"] = -$productDiscount['reduction_amount'];
                    $discountList["unit_price_tax_incl"] = -$productDiscount['reduction_amount'];
                    $discountList["total_price_tax_incl"] = -$productDiscount['reduction_amount'];
                    $discountList["total_price_tax_excl"] = -$productDiscount['reduction_amount'];
                } elseif (isset($productDiscount['reduction_percent']) && $productDiscount['reduction_percent']>0) {
                    $reductionPercentRest = 100-$productDiscount['reduction_percent'];
                    $priceOld=($productDiscount['unit_price_tax_incl']*100)/$reductionPercentRest;
                    $discountAmount = $priceOld - $productDiscount['unit_price_tax_incl'];
                    
                    $discountList["unit_price_tax_excl"] = -$discountAmount;
                    $discountList["unit_price_tax_incl"] = -$discountAmount;
                    $discountList["total_price_tax_incl"] = -$discountAmount;
                    $discountList["total_price_tax_excl"] = -$discountAmount;
                    
                    
                } else {
                    $discountList["unit_price_tax_excl"] = 0;
                    $discountList["unit_price_tax_incl"] = 0;
                    $discountList["total_price_tax_incl"] = 0;
                    $discountList["total_price_tax_excl"] = 0;
                }
                $discountList["reduction_amount"]=0;
                $discountList["reduction_percent"]=0;
                $discountList["product_quantity"] = "1";
                $discountList["product_price"] = 0;
                $discountList["product_reference"] = "--";
                if ($discountList["unit_price_tax_incl"] != "0") {
                    array_push($product_list, $discountList);
                }
            }
            $discountOrder=array();
            $discountOrder["product_id"] = "";
            $discountOrder["product_name"] = HTMLTemplateInvoice::l('Discount for')." ".$this->order->reference;
            $discountOrder["product_ean13"] = "--";
            $discountOrder["unit_price_tax_excl"] = -$this->order_invoice->total_discount_tax_excl;
            $discountOrder["unit_price_tax_incl"] = -$this->order_invoice->total_discount_tax_incl;
            $discountOrder["total_price_tax_incl"] = -$this->order_invoice->total_discount_tax_incl;
            $discountOrder["total_price_tax_excl"] = -$this->order_invoice->total_discount_tax_excl;
            $discountOrder["reduction_amount"]=0;
            $discountOrder["reduction_percent"]=0;
            $discountOrder["product_quantity"] = "1";
            $discountOrder["product_price"] = 0;
            $discountOrder["product_reference"] = "--";
            if ($discountOrder["total_price_tax_incl"] != "0") {
                array_push($product_list, $discountOrder);
            }
        }
        $showShippingInProductList="N";
        if (!empty($this->htmlTemplate)) {
            $showShippingInProductList=$this->htmlTemplate[0]['showShippingInProductList'];
        }
        if ($showShippingInProductList=="Y") {
            $shipping = array();
            $shipping["product_id"] = "";
            $shipping["product_name"] = HTMLTemplateInvoice::l('Shipping Cost')." [".$carrier->name."]";
            $shipping["product_ean13"] = "--";
            $shipping["unit_price_tax_excl"] = $this->order_invoice->total_shipping_tax_excl;
            $shipping["unit_price_tax_incl"] = $this->order_invoice->total_shipping_tax_incl;
            $shipping["reduction_percent"] = null;
            $shipping["reduction_amount"] = null;
            $shipping["product_quantity"] = "1";
            $shipping["product_price"] = $this->order_invoice->total_shipping_tax_excl;
            $shipping["product_reference"] = "--";
            $shipping["total_price_tax_incl"] = $this->order_invoice->total_shipping_tax_incl;
            $shipping["total_price_tax_excl"] = $this->order_invoice->total_shipping_tax_excl;
            if ($shipping["unit_price_tax_incl"] != "0") {
                array_push($product_list, $shipping);
            }
        }
        require_once(_PS_MODULE_DIR_ . "ba_prestashop_invoice/ba_prestashop_invoice.php");
        $columns_title = ba_prestashop_invoice::deNonlatin($this->htmlTemplate[0]['columsTitleJson']);
        $columns_content = Tools::jsonDecode($this->htmlTemplate[0]['columsContentJson']);
        $columns_bgcolor = Tools::jsonDecode($this->htmlTemplate[0]['columsColorBgJson']);
        $columns_color = Tools::jsonDecode($this->htmlTemplate[0]['columsColorJson']);
        $customize_css=null;
        if (!empty($this->htmlTemplate)) {
            $customize_css = $this->htmlTemplate[0]['customize_css'];
        }
        $html_product_list = '
            <style>
                '.Tools::htmlentitiesDecodeUTF8($customize_css).'
            </style>
            <table 
            id="product_list_tempalte_invoice" 
            style="width:100%;margin-top:27pt;" 
            cellpadding="0" cellspacing="0">
            
        ';
        $numberColumnOfTableTemplaterPro = 0;
        if (!empty($this->htmlTemplate)) {
            $numberColumnOfTableTemplaterPro=$this->htmlTemplate[0]['numberColumnOfTableTemplaterPro'];
        }
        $html_product_list.="<tr style=''>";
        for ($i = 0; $i < $numberColumnOfTableTemplaterPro; $i++) {
            if ($columns_content[$i]=="7" || $columns_content[$i]=="6") {
                $html_product_list.=
                "<th style='color:#" . $columns_color[$i] . ";
                background-color:#" . $columns_bgcolor[$i] . ";' class='product_list_title product_list_col_"
                .($i+1)." product_list_title_".($i+1)."'>
                " . $columns_title[$i] . "</th>";
            } elseif ($columns_content[$i]=="11" || $columns_content[$i]=="8") {
                $html_product_list.=
                "<th style='color:#" . $columns_color[$i] . ";
                background-color:#" . $columns_bgcolor[$i] . ";' class='product_list_title product_list_col_"
                .($i+1)." product_list_title_".($i+1)."'>
                " . $columns_title[$i] . "</th>";
            } else {
                $html_product_list.=
                "<th style='color:#" . $columns_color[$i] . ";
                background-color:#" . $columns_bgcolor[$i] . ";' class='product_list_title product_list_col_"
                .($i+1)." product_list_title_".($i+1)."'>
                " . $columns_title[$i] . "</th>";
            }
           
        }
        $html_product_list.="</tr>";
        foreach ($product_list as $pro) {
            $html_product_list.='<tr>';
            for ($j = 0; $j < $numberColumnOfTableTemplaterPro; $j++) {
                $html_product_list.= $this->checkContentType($columns_content[$j], $pro, $j);
                
            }
            $html_product_list.='</tr>';
        }
        
        $html_product_list.="</table>";
        $html = str_replace("[products_list]", $html_product_list, $html);
        $html = str_replace("[newsletter_table]", $this->tableNewsletter(), $html);
        $html = str_replace("[discount_table]", $this->tableDiscount(), $html);
        $voucherAmountTaxIncl = Tools::displayPrice($this->order->total_discounts_tax_incl, $orderIdCurrency);
        $html = str_replace("[total_voucher_amount_tax_incl]", $voucherAmountTaxIncl, $html);
        $voucherAmountTaxExcl = Tools::displayPrice($this->order->total_discounts_tax_excl, $orderIdCurrency);
        $html = str_replace("[total_voucher_amount_tax_excl]", $voucherAmountTaxExcl, $html);
        return $html;
    }

   
    public function getRiskText($id)
    {
        switch($id){
            case '1':
                return HTMLTemplateInvoice::l('None');
            case '2':
                return HTMLTemplateInvoice::l('Low');
            case '3':
                return HTMLTemplateInvoice::l('Medium');
            case '4':
                return HTMLTemplateInvoice::l('High');
            default:
                return HTMLTemplateInvoice::l('None');
        }
    }

    
    public function bagetShippingTaxes($order)
    {
        $taxes_breakdown = array();
        $order_invoice=$this->order_invoice;
        // No shipping breakdown if it's free!
        foreach ($order->getCartRules() as $cart_rule) {
            if ($cart_rule['free_shipping']) {
                return $taxes_breakdown;
            }
            
        }
        
        $shipping_tax_amount = $order_invoice->total_shipping_tax_incl - $order_invoice->total_shipping_tax_excl;
        if ($shipping_tax_amount > 0) {
            $taxes_breakdown[] = array(
                'rate' => $order->carrier_tax_rate,
                'total_amount' => $shipping_tax_amount,
                'total_tax_excl' => $order_invoice->total_shipping_tax_excl
            );
        }
        return $taxes_breakdown;
    }
    public function displayDate($date, $id_lang = null)
    {
        if (!$date || !($time = strtotime($date))) {
            return $date;
        }
        if ($date == '0000-00-00 00:00:00' || $date == '0000-00-00') {
            return '';
        }

        if (!Validate::isDate($date)) {
            return $date;
        }
        if ($id_lang == null) {
            $id_lang = $this->order->id_lang;
        }
        $context = Context::getContext();
        $lang = empty($id_lang) ? $context->language : new Language($id_lang);
        $date_format = $lang->date_format_lite;
        return date($date_format, $time);
    }
}
