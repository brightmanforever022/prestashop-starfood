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
    <table style="margin-bottom: 20px; width: 100%; float: left;" class="border_bottom">
        <tbody>
            <tr>
                <td style="width: 40%;">{$logo|escape:'htmlall':'UTF-8'}</td>
                <td style="width: 20%;"></td>
                <td style="width: 40%;">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td><h4 style="font-size: 10pt;">Office Address</h4></td>
                            </tr>
                            <tr>
                                <td style="font-size: 8pt;">210 Hoang Quoc Viet Street, Cau Giay Dist, Ha Noi, Viet Nam</td>
                            </tr>
                            <tr>
                                <td style="font-size: 8pt;">Phone: + 84 1659 005 710</td>
                            </tr>
                            <tr>
                                <td style="font-size: 8pt;">Email: support@greenwebteam.com</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%; float: left;">
        <tbody>
            <tr>
                <td style="width: 35%; text-align: left;"><br />
                    <h4 class="invoice_title">INVOICE</h4>
                    <br /> {$barcode_invoice|escape:'htmlall':'UTF-8'}</td>
                <td style="width: 30%;"></td>
                <td style="width: 35%;">
                    <h4 class="header_title">Total Purchase</h4>
                    <table style="width:100%;" class="total_wp"  cellpadding="5" cellspacing="0">
                        <tr>
                            <td class="total_paid" style="text-align: center;font-size:13pt;">{displayPrice:$total_paid|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%; float: left;">
        <tbody>
            <tr>
                <td style="width: 21%;">
                    <h4 class="header_title">BILLING ADDRESS</h4>
                    <p>{$billing_firstname|escape:'htmlall':'UTF-8'} {$billing_lastname|escape:'htmlall':'UTF-8'}
                        <br /> {$billing_address1|escape:'htmlall':'UTF-8'} {$billing_address2|escape:'htmlall':'UTF-8'}
                        <br /> {$billing_city|escape:'htmlall':'UTF-8'} - {$billing_postcode|escape:'htmlall':'UTF-8'} {$billing_state|escape:'htmlall':'UTF-8'}
                        <br /> {$billing_phone|escape:'htmlall':'UTF-8'}
                        <br /> {$billing_phone_mobile|escape:'htmlall':'UTF-8'}</p>
                </td>
                <td style="width: 21%;">
                    <h4 class="header_title">DELIVERY ADDRESS</h4>
                    <p>{$delivery_firstname|escape:'htmlall':'UTF-8'} {$delivery_lastname|escape:'htmlall':'UTF-8'}
                        <br /> {$delivery_address1|escape:'htmlall':'UTF-8'} {$delivery_address2|escape:'htmlall':'UTF-8'}
                        <br /> {$delivery_city|escape:'htmlall':'UTF-8'} - {$delivery_postcode|escape:'htmlall':'UTF-8'} {$delivery_state|escape:'htmlall':'UTF-8'}
                        <br /> {$delivery_phone|escape:'htmlall':'UTF-8'}
                        <br /> {$delivery_phone_mobile|escape:'htmlall':'UTF-8'}</p>
                </td>
                <td style="width: 20%;"></td>
                <td style="width: 38%;">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td><strong class="strong_item">Invoice No: </strong>{$invoice_number|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td><strong class="strong_item">Carier: </strong>{$order_carrier_name|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td><strong class="strong_item">Invoice Date: </strong>{$invoice_date|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    {$products_list|escape:'htmlall':'UTF-8'}
    <table style="width: 100%;" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td style="width: 60%;">
                    <br /><br /><br />
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                  <td class="payment_method"><strong class="strong_item">Payment Method</strong> : {$payment|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr><td></td></tr>
                            <tr>
                                <td><strong class="strong_item">Terms & Conditions</strong><br />Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt dolore magnam aliquam quaerat.</td>
                            </tr>
                            
                        </tbody>
                    </table>
                </td>
                <td style="width: 40%;">
                    <table style="width: 100%;" cellpadding="5" cellspacing="0">
                        <tbody>
                            <tr class="product color_line_even">
                                <td style="text-align: right; width: 60%;"><strong class="strong_item">Total Product: </strong></td>
                                <td style="text-align: center; width: 40%;">{displayPrice:$total_products|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr  class="product color_line_odd">
                                <td style="text-align: right; width: 60%;"><strong class="strong_item">Total Discounts: </strong></td>
                                <td style="text-align: center; width: 40%;">-{displayPrice:$total_discounts_tax_excl|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr class="product color_line_even">
                                <td style="text-align: right; width: 60%;"><strong class="strong_item">Shipping Cost : </strong></td>
                                <td style="text-align: center; width: 40%;">{displayPrice:$total_shipping_tax_excl|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr  class="product color_line_odd">
                                <td style="text-align: right; width: 60%;"><strong class="strong_item">Total Tax : </strong></td>
                                <td style="text-align: center; width: 40%;">{displayPrice:$footer.total_taxes|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr class="total_wp">
                                <td colspan="2">
                                    <table style="width: 100%;" cellpadding="5" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td style="text-align: right; width: 60%;" class="total_text"><strong>Total: </strong></td>
                                                <td style="text-align: center; width: 40%;" class="total_val">{displayPrice:$total_paid|escape:'htmlall':'UTF-8'}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center; width: 100%;">
                                    <table style="width: 100%; margin-left: auto; margin-right: auto;">
                                        <tbody>
                                            <tr>
                                                <td><br /><img src="{$tpltemplate_dir|escape:'htmlall':'UTF-8'}/views/img/imgtemplates/temp4/founder.png" alt="" width="170px" height="auto" /></td>
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
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="width: 100%;text-align: center;">
                    <br />
                    <h4 class="thanksfor">Thank for your bussiness !</h4>
                </td>
            </tr>
        </tbody>
    </table>
</div>