{*
* Do not edit the file if you want to upgrade in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}
<table style="margin-bottom: 20px; width: 100%; float: left;">
    <tbody>
        <tr>
            <td style="width: 40%;text-align: center;">{$logo|escape:'htmlall':'UTF-8'}<br />210 Hoang Quoc Viet Street</td>
            <td style="width: 10%;text-align: center;"></td>
            <td style="width: 50%;text-align: center;">
                <table style="width: 100%;">
                    <tr><td class="invoice_title">INVOICE</td></tr>
                    <tr><td>{$invoice_date|escape:'htmlall':'UTF-8'}</td></tr>
                </table>
            </td>
        </tr>
        <tr><td colspan="3"></td></tr>
        <tr>
            <td colspan="3" style="text-align: center;">
                <table style="width: 100%;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="width: 25%;"></td>
                        <td style="width: 50%;">{$barcode_invoice|escape:'htmlall':'UTF-8'}</td>
                        <td style="width: 25%;"></td>
                    </tr>
                    <tr><td colspan="3"  style="text-align: center;">{$invoice_number|escape:'htmlall':'UTF-8'}</td></tr>
                </table>
            
            
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table style="width: 100%;" cellpadding="5" cellspacing="0">
                    <tr>
                        <td style="width: 50%;">
                            <table style="width: 100%;" cellpadding="3" cellspacing="0">
                                <tr><td class="header_title" >BILLING ADDRESS</td></tr>
                                <tr><td>{$billing_firstname|escape:'htmlall':'UTF-8'} {$billing_lastname|escape:'htmlall':'UTF-8'}
                                <br /> {$billing_address1|escape:'htmlall':'UTF-8'} {$billing_address2|escape:'htmlall':'UTF-8'}
                                <br /> {$billing_city|escape:'htmlall':'UTF-8'} - {$billing_postcode|escape:'htmlall':'UTF-8'} {$billing_state|escape:'htmlall':'UTF-8'}
                                <br /> {$billing_phone|escape:'htmlall':'UTF-8'}
                                <br /> {$billing_phone_mobile|escape:'htmlall':'UTF-8'}</td></tr>
                                <tr><td class="header_title" >PAYMENT METHOD</td></tr>
                                <tr><td>{$payment|escape:'htmlall':'UTF-8'}</td></tr>
                            </table>
                        </td>
                        <td style="width: 50%;">
                            <table style="width: 100%;" cellpadding="3" cellspacing="0">
                                <tr><td class="header_title" >DELIVERY ADDRESS</td></tr>
                                <tr><td>{$delivery_firstname|escape:'htmlall':'UTF-8'} {$delivery_lastname|escape:'htmlall':'UTF-8'}
                                <br /> {$delivery_address1|escape:'htmlall':'UTF-8'} {$delivery_address2|escape:'htmlall':'UTF-8'}
                                <br /> {$delivery_city|escape:'htmlall':'UTF-8'} - {$delivery_postcode|escape:'htmlall':'UTF-8'} {$delivery_state|escape:'htmlall':'UTF-8'}
                                <br /> {$delivery_phone|escape:'htmlall':'UTF-8'}
                                <br /> {$delivery_phone_mobile|escape:'htmlall':'UTF-8'}</td></tr>
                                <tr><td class="header_title" >SHIPPING METHOD</td></tr>
                                <tr><td>{$order_carrier_name|escape:'htmlall':'UTF-8'}</td></tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td colspan="3"></td></tr>
    </tbody>
</table>
<table style="width: 100%;">
    <tbody>
        <tr><td>{$products_list|escape:'htmlall':'UTF-8'}</td></tr>
    </tbody>
</table>
<table style="width: 100%;">
    <tbody>
        <tr>
            <td style="width: 50%;" class="thanksfor"><br/><br/>Thank for your bussiness !</td>
            <td style="width: 49%;" class="total_box_wp">
                <table style="width: 100%;" cellpadding="2" cellspacing="0">
                    <tbody>
                        <tr class="color_line_even">
                            <td style="text-align: right; width: 60%;"><strong class="strong_item">Total Product: </strong></td>
                            <td style="text-align: right; width: 40%;">{displayPrice:$total_products|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                        <tr class="color_line_odd">
                            <td style="text-align: right; width: 60%;"><strong class="strong_item">Total Discounts: </strong></td>
                            <td style="text-align: right; width: 40%;">-{displayPrice:$total_discounts_tax_excl|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                        <tr class="color_line_even">
                            <td style="text-align: right; width: 60%;"><strong class="strong_item">Shipping Cost : </strong></td>
                            <td style="text-align: right; width: 40%;">{displayPrice:$total_shipping_tax_excl|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                        <tr class="color_line_odd">
                            <td style="text-align: right; width: 60%;"><strong class="strong_item">Total Tax : </strong></td>
                            <td style="text-align: right; width: 40%;">{displayPrice:$footer.total_taxes|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                        <tr class="total_box">
                            <td style="text-align: right; width: 60%;" class="total_text"><strong>Total: </strong></td>
                            <td style="text-align: right; width: 40%;" class="total_text">{displayPrice:$total_paid|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>