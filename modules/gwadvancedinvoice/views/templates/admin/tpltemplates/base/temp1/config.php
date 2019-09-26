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

class Temp1{
    public static function getTemplate(){
        $template =  array(
            'id'=>'temp1',
            'name'=>'Template base 1',
            'thumbnail'=>'views/img/imgtemplates/temp1/temp1.png',
            'large_thumbnail'=>'views/img/imgtemplates/temp1/temp1.png',
            'pagesize'=>array('A4','A5'),
            'activefooter'=>'1',
            'activeheader'=>'0',
            'mgheader'=>'5',
            'mgfooter'=>'18',
            'mgcontent'=>'10-10-10-20',
            'barcodetype'=>'C128',
            'pageorientation'=>'P',
            'barcodeformat'=>'INVOICE {$invoice_number}',
            'template_config'=>array(
                                array(
                                    'title'=>'Color Text Default',
                                    'type'=>'color',
                                    'name'=>'bodytextcolor',
                                    'value'=>'#908c89',
                                    'hint'=>''
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
                                    'value'=>'#ffffff'
                                ),
                                array(
                                    'title'=>'Font Heading Text',
                                    'type'=>'font',
                                    'name'=>'font_title',
                                    'value'=>'robotomedium'
                                ),
                                array(
                                    'title'=>'Block Title Color',
                                    'type'=>'color',
                                    'name'=>'color_title',
                                    'value'=>'#00709e'
                                ),
                                array(
                                    'title'=>'Strong Color',
                                    'type'=>'color',
                                    'name'=>'color_strong',
                                    'value'=>'#474241'
                                ),
                                array(
                                    'title'=>'Heading Block Background',
                                    'type'=>'color',
                                    'name'=>'headingblockbackground',
                                    'value'=>'#00709e'
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
                                    'value'=>'#474241'
                                ),
                                array(
                                    'title'=>'Background line even',
                                    'type'=>'color',
                                    'name'=>'color_line_even',
                                    'value'=>'#f1efef'
                                ),
                                array(
                                    'title'=>'Background line odd',
                                    'type'=>'color',
                                    'name'=>'color_line_odd',
                                    'value'=>'#fcfcfb'
                                ),
                                array(
                                    'title'=>'Color border',
                                    'type'=>'color',
                                    'name'=>'color_border',
                                    'value'=>'#d6d2cf'
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
                                    'value'=>'#474241'
                                ),
                                array(
                                    'title'=>'Invoice Title Font',
                                    'type'=>'font',
                                    'name'=>'font_invoice',
                                    'value'=>'robotolight'
                                ),
                                array(
                                    'title'=>'Thanks for ...',
                                    'type'=>'text',
                                    'name'=>'size_thank_for',
                                    'value'=>'30pt'
                                ),
                                array(
                                    'title'=>'Thanks for ... color',
                                    'type'=>'color',
                                    'name'=>'color_thank_for',
                                    'value'=>'#00709e'
                                ),
                                array(
                                    'title'=>'Thanks for ... font',
                                    'type'=>'font',
                                    'name'=>'font_thank_for',
                                    'value'=>'robotolight'
                                ),
                            ),
            'productcolumns'=> array(
                            'widthtitle' => array(
                                    '0' => '10',
                                    '1' => '10',
                                    '2' => '40',
                                    '3' => '15',
                                    '4' => '12',
                                    '5' => '13',
                                ),
                            'title' => array
                                (
                                    '0' => 'Barcode',
                                    '1' => 'Image',
                                    '2' => 'Product',
                                    '3' => 'Unit Price (Tax Excl.)',
                                    '4' => 'Qty',
                                    '5' => 'Total',
                                ),
                            'content' => array
                                (
                                    '0' => '{$order_detail.barcode}',
                                    '1' => '{$order_detail.image_tag}',
                                    '2' => '{$order_detail.product_name}{$order_detail.description_short}',
                                    '3' => '{displayPrice:$order_detail.unit_price_tax_excl_including_ecotax}',
                                    '4' => '{$order_detail.product_quantity}',
                                    '5' => '{displayPrice:$order_detail.total_price_tax_excl_including_ecotax}'
                                ),
                            'align' => array(
                                    '0' => 'center',
                                    '1' => 'center',
                                    '2' => 'left',
                                    '3' => 'center',
                                    '4' => 'center',
                                    '5' => 'center',
                            )
                        ),
        );
        return $template;
    }
}