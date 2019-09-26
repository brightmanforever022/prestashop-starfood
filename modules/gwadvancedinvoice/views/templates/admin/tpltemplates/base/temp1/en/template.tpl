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
                <td style="float: left; width: 40%;">{$logo|escape:'htmlall':'UTF-8'}</td>
                <td style="width: 20%;">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td>
                                    <h4 style="font-size: 7pt;" class="header_title">TELEPHONE & FAX</h4>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 7pt;">+84 1234567890</td>
                            </tr>
                            <tr>
                                <td style="font-size: 7pt;">+84 9876543212</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 20%;">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td>
                                    <h4 style="font-size: 7pt;" class="header_title">ADDRESS</h4>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 7pt;">210 Hoang Quoc Viet Street</td>
                            </tr>
                            <tr>
                                <td style="font-size: 7pt;">Ha Noi, Viet Nam</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 20%;">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td>
                                    <h4 style="font-size: 7pt;" class="header_title">WEBSITE</h4>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 7pt;">support@greenwebcorp.com</td>
                            </tr>
                            <tr>
                                <td style="font-size: 7pt;">www.greenwebteam.com</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <br />
    <table style="width: 100%; float: left;">
        <tbody>
            <tr>
                <td style="width: 37%;">
                    <h4 class="invoice_title">  INVOICE</h4>
                    <br /> {$barcode_invoice|escape:'htmlall':'UTF-8'}</td>
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
                <td style="width: 21%;"><strong class="strong_item">Invoice No: </strong>{$invoice_number|escape:'htmlall':'UTF-8'}
                    <br /><strong class="strong_item">Carier: </strong>{$order_carrier_name|escape:'htmlall':'UTF-8'}
                    <br /><strong class="strong_item">Invoice Date: </strong>{$invoice_date|escape:'htmlall':'UTF-8'}
                    <br /><strong class="strong_item">Total Due: </strong>{displayPrice:$total_paid|escape:'htmlall':'UTF-8'}</td>
            </tr>
        </tbody>
    </table>
    <br /> {$products_list|escape:'htmlall':'UTF-8'}
    <table style="width: 100%;" cellpadding="7" cellspacing="0">
        <tbody>
            <tr>
                <td style="float: right; width: 60%;"></td>
                <td style="float: right; width: 40%;">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td style="text-align: right; width: 60%;"><strong class="strong_item">Total Product: </strong></td>
                                <td style="text-align: center; width: 40%;">{displayPrice:$total_products|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 60%;"><strong class="strong_item">Total Discounts: </strong></td>
                                <td style="text-align: center; width: 40%;">-{displayPrice:$total_discounts_tax_excl|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 60%;"><strong class="strong_item">Shipping Cost : </strong></td>
                                <td style="text-align: center; width: 40%;">{displayPrice:$total_shipping_tax_excl|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 60%;"><strong class="strong_item">Total Tax : </strong></td>
                                <td style="text-align: center; width: 40%;">{displayPrice:$footer.total_taxes|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr class="total_wp">
                <td style="float: right; width: 60%;"></td>
                <td style="float: right; width: 40%;">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td style="text-align: right; width: 60%;" class="total_text"><strong>Total: </strong></td>
                                <td style="text-align: center; width: 40%;" class="total_text">{displayPrice:$total_paid|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <br />
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="width: 50%;">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td><strong class="strong_item">Payment Method</strong> : {$payment|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td>
                                    <p><strong class="strong_item">Terms & Conditions</strong></p>
                                    <p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt dolore magnam aliquam quaerat.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="text-align: center; width: 10%;"></td>
                <td style="text-align: center; width: 40%;">
                    <table style="width: 80%; margin-left: auto; margin-right: auto;">
                        <tbody>
                            <tr>
                                <td><img src="{$tpltemplate_dir|escape:'htmlall':'UTF-8'}/views/img/imgtemplates/temp1/founder.png" alt="" width="314" height="71" /></td>
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
            <tr>
                <td style="text-align: center;" colspan="3">
                    <br />
                    <br />
                    <h4 class="thanksfor">Thank for your bussiness !</h4>
                </td>
            </tr>
        </tbody>
    </table>
</div>