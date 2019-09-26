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
                <td style="width: 25%;">{$logo|escape:'htmlall':'UTF-8'}
                    <br />
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td style="width: 15%;"><img src="{$tpltemplate_dir|escape:'htmlall':'UTF-8'}/views/img/imgtemplates/temp6/phone.png" alt="" width="25" height="25" /></td>
                                <td style="width: 85%;">
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="font-size: 8pt;">+ 84 1659 005 710</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 8pt;">+ 84 1655 068 980</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td style="width: 15%;"><img src="{$tpltemplate_dir|escape:'htmlall':'UTF-8'}/views/img/imgtemplates/temp6/location.png" alt="" width="25" height="25" /></td>
                                <td style="width: 85%;">
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="font-size: 8pt;">180 Hoang Quoc Viet Street,</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 8pt;">Ha Noi, Viet Nam</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td style="width: 15%;"><img src="{$tpltemplate_dir|escape:'htmlall':'UTF-8'}/views/img/imgtemplates/temp6/location.png" alt="" width="25" height="25" /></td>
                                <td style="width: 85%;">
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="font-size: 8pt;">180 Hoang Quoc Viet Street,</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 8pt;">Ha Noi, Viet Nam</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td style="width: 15%;"><img src="{$tpltemplate_dir|escape:'htmlall':'UTF-8'}/views/img/imgtemplates/temp6/email.png" alt="" width="25" height="25" /></td>
                                <td style="width: 85%;">
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="font-size: 8pt;">support@greenwebteam.com</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 8pt;">www.greenwebteam.com</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 6%;"></td>
                <td style="width: 38%;">
                    <h4 class="border_bottom invoice_title" style="width: 100%; text-align: center;">INVOICE {$invoice_number|escape:'htmlall':'UTF-8'}</h4>
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td colspan="2" style="width: 100%; text-align: center;">{$barcode_invoice|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td style="width: 50%; text-align: right;">Invoice Date:</td>
                                <td style="width: 50%; text-align: left;">{$invoice_date|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td style="width: 50%; text-align: right;">Order Reference:</td>
                                <td style="width: 50%; text-align: left;">{$reference|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td style="width: 50%; text-align: right;">Order Date:</td>
                                <td style="width: 50%; text-align: left;">{$date_add|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 6%;"></td>
                <td style="width: 25%;">
                    <table>
                        <tbody>
                            <tr>
                                <td class="header_title">DELIVERY ADDRESS</td>
                            </tr>
                            <tr>
                                <td>{$delivery_firstname|escape:'htmlall':'UTF-8'} {$delivery_lastname|escape:'htmlall':'UTF-8'}
                                    <br /> {$delivery_address1|escape:'htmlall':'UTF-8'} {$delivery_address2|escape:'htmlall':'UTF-8'}
                                    <br /> {$delivery_city|escape:'htmlall':'UTF-8'} - {$delivery_postcode|escape:'htmlall':'UTF-8'} {$delivery_state|escape:'htmlall':'UTF-8'}
                                    <br /> {$delivery_phone|escape:'htmlall':'UTF-8'}
                                    <br /> {$delivery_phone_mobile|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td class="header_title">BILLING ADDRESS</td>
                            </tr>
                            <tr>
                                <td>{$billing_firstname|escape:'htmlall':'UTF-8'} {$billing_lastname|escape:'htmlall':'UTF-8'}
                                    <br /> {$billing_address1|escape:'htmlall':'UTF-8'} {$billing_address2|escape:'htmlall':'UTF-8'}
                                    <br /> {$billing_city|escape:'htmlall':'UTF-8'} - {$billing_postcode|escape:'htmlall':'UTF-8'} {$billing_state|escape:'htmlall':'UTF-8'}
                                    <br /> {$billing_phone|escape:'htmlall':'UTF-8'}
                                    <br /> {$billing_phone_mobile|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <br /> {$products_list|escape:'htmlall':'UTF-8'}
    <br />
    <table style="width: 100%;" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td style="width: 60%;">
                    <table style="width: 100%;" cellpadding="5" cellspacing="0">
                        <tbody>
                            <tr>
                                <td>
                                    <table style="width: 50%;" cellpadding="5" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td class="box_color">Payment Method:</td>
                                                <td class="box_color" style="text-align: center;">{$payment|escape:'htmlall':'UTF-8'}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr><td></td></tr>
                            <tr><td></td></tr>
                            <tr>
                                <td>
                                    <table style="width: 80%;" class="footer_background" cellpadding="5" cellspacing="0" >
                                        <tbody>
                                            <tr class="thanksfor_bottom">
                                                <td colspan="2"  class="thanksfor">Thank for your bussiness !</td>
                                            </tr>
                                            <tr>
                                                <td style="width: 20%;" class="color_footer" >Terms :</td>
                                                <td style="width: 80%;" class="color_footer" >Emporibus autem quibusdam et aut officiis debitis rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. </td>
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
                            <tr>
                                <td style="text-align: right; width: 50%;"><strong class="strong_item">Total Product: </strong></td>
                                <td style="text-align: center; width: 50%;">{displayPrice:$total_products|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 50%;"><strong class="strong_item">Total Discounts: </strong></td>
                                <td style="text-align: center; width: 50%;">-{displayPrice:$total_discounts_tax_excl|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 50%;"><strong class="strong_item">Shipping Cost : </strong></td>
                                <td style="text-align: center; width: 50%;">{displayPrice:$total_shipping_tax_excl|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 50%;"><strong class="strong_item">Total Tax : </strong></td>
                                <td style="text-align: center; width: 50%;">{displayPrice:$footer.total_taxes|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <table style="width: 100%;" class="total_wp" cellpadding="5" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td style="text-align: right; width: 50%;" class="total_text"><strong>Total: </strong></td>
                                                <td style="text-align: center; width: 50%;" class="total_val">{displayPrice:$total_paid|escape:'htmlall':'UTF-8'}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <br />
                                    <br />
                                    <table style="width: 100%; text-align: center; margin-left: auto; margin-right: auto;">
                                        <tbody>
                                            <tr>
                                                <td style="text-align: center; margin-left: auto; margin-right: auto;"><img src="{$tpltemplate_dir|escape:'htmlall':'UTF-8'}/views/img/imgtemplates/temp6/founder.png" alt="" width="314" height="71" /></td>
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