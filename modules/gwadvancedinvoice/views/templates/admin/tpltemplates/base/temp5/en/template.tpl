{*
* Do not edit the file if you want to upgrade in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}
<div id="wrap">
    <table style="margin-bottom: 20px; width: 100%; float: left;">
        <tbody>
            <tr>
                <td style="width: 31%;">{$logo|escape:'htmlall':'UTF-8'}</td>
                <td style="width: 23%;">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td style="width: 15%;"><img src="{$tpltemplate_dir|escape:'htmlall':'UTF-8'}/views/img/imgtemplates/temp5/phone.png" alt="" width="25" height="25" /></td>
                                <td style="width: 85%;">
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="font-size:7pt;">+ 84 1659 005 710</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:7pt;">+ 84 1655 068 980</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 23%;">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td style="width: 15%;"><img src="{$tpltemplate_dir|escape:'htmlall':'UTF-8'}/views/img/imgtemplates/temp5/location.png" alt="" width="25" height="25" /></td>
                                <td style="width: 85%;">
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="font-size:7pt;">180 Hoang Quoc Viet Street,</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:7pt;">Ha Noi, Viet Nam</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 23%;">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td style="width: 15%;"><img src="{$tpltemplate_dir|escape:'htmlall':'UTF-8'}/views/img/imgtemplates/temp5/email.png" alt="" width="25" height="25" /></td>
                                <td style="width: 85%;">
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="font-size:7pt;">support@greenwebteam.com</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:7pt;">www.greenwebteam.com</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <br />
    <table style="width: 100%; float: left;"  cellpadding="5" cellspacing="0">
        <tbody>
            <tr>
                <td style="width: 25%;">
                    <h4 class="header_title">BILLING ADDRESS</h4>
                    <p>{$billing_firstname|escape:'htmlall':'UTF-8'} {$billing_lastname|escape:'htmlall':'UTF-8'}
                        <br /> {$billing_address1|escape:'htmlall':'UTF-8'} {$billing_address2|escape:'htmlall':'UTF-8'}
                        <br /> {$billing_city|escape:'htmlall':'UTF-8'} - {$billing_postcode|escape:'htmlall':'UTF-8'} {$billing_state|escape:'htmlall':'UTF-8'}
                        <br /> {$billing_phone|escape:'htmlall':'UTF-8'}
                        <br /> {$billing_phone_mobile|escape:'htmlall':'UTF-8'}</p>
                </td>
                <td style="width: 25%;">
                    <h4 class="header_title">DELIVERY ADDRESS</h4>
                    <p>{$delivery_firstname|escape:'htmlall':'UTF-8'} {$delivery_lastname|escape:'htmlall':'UTF-8'}
                        <br /> {$delivery_address1|escape:'htmlall':'UTF-8'} {$delivery_address2|escape:'htmlall':'UTF-8'}
                        <br /> {$delivery_city|escape:'htmlall':'UTF-8'} - {$delivery_postcode|escape:'htmlall':'UTF-8'} {$delivery_state|escape:'htmlall':'UTF-8'}
                        <br /> {$delivery_phone|escape:'htmlall':'UTF-8'}
                        <br /> {$delivery_phone_mobile|escape:'htmlall':'UTF-8'}</p>
                </td>
                <td style="width: 10%;"></td>
                <td style="width: 40%;">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td colspan="2"  class="invoice_title">INVOICE</td>
                            </tr>
                            <tr>
                                <td colspan="2"  >{$barcode_invoice|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="border_bottom"><br /></td>
                            </tr>
                            <tr>
                                <td colspan="2"><br /></td>
                            </tr>
                            <tr>
                                <td style="width:50%">Invoice Number:</td>
                                <td style="width:50%">{$invoice_number|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td style="width:50%">Invoice Date:</td>
                                <td style="width:50%">{$invoice_date|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td style="width:50%">Order Reference:</td>
                                <td style="width:50%">{$reference|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td style="width:50%">Order Date:</td>
                                <td style="width:50%">{$date_add|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                
            </tr>
        </tbody>
    </table>
    <br /> {$products_list|escape:'htmlall':'UTF-8'}
    <table style="width: 100%;" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td style="width: 60%;">
                    <br />
                    <table style="width: 60%;" cellpadding="5" cellspacing="0">
                        <tbody>
                            <tr>
                                <td class="box_color">Payment Method:</td>
                                <td  class="box_color " style="text-align: center;">{$payment|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                        </tbody>
                    </table>
                    <br />
                    <table  style="width: 60%;" class="footer_background" cellpadding="5" cellspacing="0" >
                        <tbody>
                            <tr class="thanksfor_bottom">
                                <td colspan="2"  class="thanksfor">Thank for your bussiness !</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="color_footer" >Emporibus autem quibusdam et aut officiis debitis rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. </td>
                            </tr>
                            <tr>
                                <td style="width: 10%;"><img src="{$tpltemplate_dir|escape:'htmlall':'UTF-8'}/views/img/imgtemplates/temp5/facebook.png" alt="" width="25" height="25" /></td>
                                <td style="width: 90%;">
                                    <table cellpadding="3" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td style="font-size: 7pt; vertical-align: middle;">https://instagram.com/greenwebteam</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 10%;"><img src="{$tpltemplate_dir|escape:'htmlall':'UTF-8'}/views/img/imgtemplates/temp5/twitter.png" alt="" width="25" height="25" /></td>
                                <td style="width: 90%;">
                                    <table cellpadding="3" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td  style="font-size: 7pt; vertical-align: middle;">https://instagram.com/greenwebteam</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 10%;"><img src="{$tpltemplate_dir|escape:'htmlall':'UTF-8'}/views/img/imgtemplates/temp5/instagram.png" alt="" width="25" height="25" /></td>
                                <td style="width: 90%;">
                                    <table cellpadding="3" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td style="font-size: 7pt; vertical-align: middle;">https://instagram.com/greenwebteam</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 40%;">
                    <table style="width: 100%;" cellpadding="5" cellspacing="0">
                        <tbody>
                            <tr class="color_line_even">
                                <td style="text-align: right; width: 50%;"><strong class="strong_item">Total Product: </strong></td>
                                <td style="text-align: center; width: 50%;">{displayPrice:$total_products|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr class="color_line_odd">
                                <td style="text-align: right; width: 50%;"><strong class="strong_item">Total Discounts: </strong></td>
                                <td style="text-align: center; width: 50%;">-{displayPrice:$total_discounts_tax_excl|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr class="color_line_even">
                                <td style="text-align: right; width: 50%;"><strong class="strong_item">Shipping Cost : </strong></td>
                                <td style="text-align: center; width: 50%;">{displayPrice:$total_shipping_tax_excl|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr class="color_line_odd">
                                <td style="text-align: right; width: 50%;"><strong class="strong_item">Total Tax : </strong></td>
                                <td style="text-align: center; width: 50%;">{displayPrice:$footer.total_taxes|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr class="total_wp">
                                <td style="text-align: right; width: 50%;" class="total_text"><strong>Total: </strong></td>
                                <td style="text-align: center; width: 50%;" class="total_val">{displayPrice:$total_paid|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <br /><br />
                                    <table style="width: 100%;text-align:center; margin-left: auto; margin-right: auto;">
                                        <tbody>
                                            <tr>
                                                <td style="text-align:center;margin-left: auto; margin-right: auto;"><img src="{$tpltemplate_dir|escape:'htmlall':'UTF-8'}/views/img/imgtemplates/temp5/founder.png" alt="" width="314" height="71" /></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p>Nguyen Van Nham</p>
                                                    <p><strong class="strong_item">CEO & Founder</strong></p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>