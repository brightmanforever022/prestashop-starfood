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

class Temp8{
    public static function getTemplate(){
        $template =  array(
            'id'=>'temp8',
            'name'=>'Template base 8',
            'thumbnail'=>'views/img/imgtemplates/temp8/temp8.png',
            'large_thumbnail'=>'views/img/imgtemplates/temp8/temp8.png',
            'pagesize'=>array('A6','A7'),
            'activefooter'=>'0',
            'activeheader'=>'0',
            'mgheader'=>'0',
            'mgfooter'=>'0',
            'mgcontent'=>'2-2-2-2',
            'barcodetype'=>'C128',
            'pageorientation'=>'P',
            'barcodeformat'=>'INVOICE {$invoice_number}',
            'template_config'=>array(
                                array(
                                    'title'=>'Color Text Default',
                                    'type'=>'color',
                                    'name'=>'bodytextcolor',
                                    'value'=>'#64615e'
                                ),
                                array(
                                    'title'=>'Font Size Default',
                                    'type'=>'text',
                                    'name'=>'font_size_text',
                                    'value'=>'8pt'
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
                                    'value'=>'#444444'
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
                                    'value'=>'#444444'
                                ),
                                array(
                                    'title'=>'Font size product',
                                    'type'=>'text',
                                    'name'=>'font_size_product',
                                    'value'=>'8pt'
                                ),
                                array(
                                    'title'=>'Color Text product',
                                    'type'=>'color',
                                    'name'=>'color_product',
                                    'value'=>'#64615e'
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
                                    'value'=>'#f1f0ef'
                                ),
                                array(
                                    'title'=>'Color border',
                                    'type'=>'color',
                                    'name'=>'color_border',
                                    'value'=>'#ffffff'
                                ),
                                array(
                                    'title'=>'Invoice Title Size',
                                    'type'=>'text',
                                    'name'=>'size_invoice',
                                    'value'=>'15pt'
                                ),
                                array(
                                    'title'=>'Invoice Title Color',
                                    'type'=>'color',
                                    'name'=>'color_invoice',
                                    'value'=>'#444444'
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
                                    'value'=>'14pt'
                                ),
                                array(
                                    'title'=>'Thanks for ... color',
                                    'type'=>'color',
                                    'name'=>'color_thank_for',
                                    'value'=>'#444444'
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
                                    '0' => '50',
                                    '1' => '30',
                                    '2' => '20',
                                ),
                            'title' => array
                                (
                                    '0' => 'Product',
                                    '1' => 'Unit Price',
                                    '2' => 'Total',
                                ),
                            'content' => array
                                (
                                    '0' => '{$order_detail.product_name}',
                                    '1' => '{displayPrice:$order_detail.unit_price_tax_excl_including_ecotax}(x{$order_detail.product_quantity})',
                                    '2' => '{displayPrice:$order_detail.total_price_tax_excl_including_ecotax}'
                                ),
                            'align' => array(
                                    '0' => 'left',
                                    '1' => 'center',
                                    '2' => 'center',
                            )
                        ),
        );
        return $template;
    }
}

?>