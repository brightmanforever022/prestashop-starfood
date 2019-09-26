<?php
/**
* This file to install template
* 
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/

class Temp10{
    public static function getTemplate(){
        $template =  array(
            'id'=>'temp10',
            'name'=>'Template base default Prestashop',
            'thumbnail'=>'views/img/imgtemplates/temp10/temp10.png',
            'large_thumbnail'=>'views/img/imgtemplates/temp10/temp10.png',
            'pagesize'=>array('A4','A5'),
            'activefooter'=>'1',
            'activeheader'=>'0',
            'mgheader'=>'0',
            'mgfooter'=>'20',
            'mgcontent'=>'5-5-5-20',
            'barcodetype'=>'C128',
            'pageorientation'=>'P',
            'barcodeformat'=>'INVOICE {$invoice_number}',
            'template_config'=>array(
                                array(
                                    'title'=>'Color Text Default',
                                    'type'=>'color',
                                    'name'=>'bodytextcolor',
                                    'value'=>'#333'
                                ),
                                array(
                                    'title'=>'Font Size Default',
                                    'type'=>'text',
                                    'name'=>'font_size_text',
                                    'value'=>'9pt'
                                ),
                                array(
                                    'title'=>'Font Text Default',
                                    'type'=>'font',
                                    'name'=>'bodytextfont',
                                    'value'=>'robotolight'
                                ),
                                array(
                                    'title'=>'Heading Text Color',
                                    'type'=>'color',
                                    'name'=>'headingtextcolor',
                                    'value'=>'#333'
                                ),
                                array(
                                    'title'=>'Font Heading Text',
                                    'type'=>'font',
                                    'name'=>'font_title',
                                    'value'=>'robotomedium'
                                ),
                                array(
                                    'title'=>'Invoice Title Size',
                                    'type'=>'text',
                                    'name'=>'size_invoice',
                                    'value'=>'30pt'
                                ),
                                array(
                                    'title'=>'Invoice Title Color',
                                    'type'=>'color',
                                    'name'=>'color_invoice',
                                    'value'=>'#333'
                                ),
                                array(
                                    'title'=>'Invoice Title Font',
                                    'type'=>'font',
                                    'name'=>'font_invoice',
                                    'value'=>'robotolight'
                                ),
                                array(
                                    'title'=>'Block Title Color',
                                    'type'=>'color',
                                    'name'=>'color_title',
                                    'value'=>'#333'
                                ),
                                array(
                                    'title'=>'Heading Block Background',
                                    'type'=>'color',
                                    'name'=>'headingblockbackground',
                                    'value'=>'#afafaf'
                                ),
                                array(
                                    'title'=>'Font size product',
                                    'type'=>'text',
                                    'name'=>'font_size_product',
                                    'value'=>'9pt'
                                ),
                                array(
                                    'title'=>'Color Text product',
                                    'type'=>'color',
                                    'name'=>'color_product',
                                    'value'=>'#333'
                                ),
                                array(
                                    'title'=>'Background line even',
                                    'type'=>'color',
                                    'name'=>'color_line_even',
                                    'value'=>'#fcfcfc'
                                ),
                                array(
                                    'title'=>'Background line odd',
                                    'type'=>'color',
                                    'name'=>'color_line_odd',
                                    'value'=>'#f3f3f3'
                                ),
                                array(
                                    'title'=>'Color border',
                                    'type'=>'color',
                                    'name'=>'color_border',
                                    'value'=>'#ffffff'
                                ),
                                array(
                                    'title'=>'Shop Name Size',
                                    'type'=>'text',
                                    'name'=>'size_shoptitle',
                                    'value'=>'13pt'
                                ),
                                array(
                                    'title'=>'Shop Name Color',
                                    'type'=>'color',
                                    'name'=>'color_shoptitle',
                                    'value'=>'#333'
                                ),
                                array(
                                    'title'=>'Shop Name Font',
                                    'type'=>'font',
                                    'name'=>'font_shoptitle',
                                    'value'=>'robotolight'
                                ),
                            ),
            'productcolumns'=> array(
                            'widthtitle' => array(
                                    '0' => '10',
                                    '1' => '40',
                                    '2' => '10',
                                    '3' => '15',
                                    '4' => '10',
                                    '5' => '15',
                                ),
                            'title' => array
                                (
                                    '0' => 'Reference',
                                    '1' => 'Product',
                                    '2' => 'Tax Rate',
                                    '3' => 'Unit Price (Tax excl.)',
                                    '4' => 'Qty',
                                    '5' => 'Total (Tax excl.)',
                                ),
                            'content' => array
                                (
                                    '0' => '{$order_detail.reference}',
                                    '1' => '{$order_detail.product_name}',
                                    '2' => '{displayPrice:$order_detail.order_detail_tax_label}',
                                    '3' => '{displayPrice:$order_detail.unit_price_tax_excl_including_ecotax}',
                                    '4' => '{$order_detail.product_quantity}',
                                    '5' => '{displayPrice:$order_detail.total_price_tax_excl_including_ecotax}',
                                ),
                            'align' => array(
                                    '0' => 'center',
                                    '1' => 'center',
                                    '2' => 'center',
                                    '3' => 'center',
                                    '4' => 'center',
                                    '5' => 'right',
                            )
                        ),
        );
        return $template;
    }
}

?>