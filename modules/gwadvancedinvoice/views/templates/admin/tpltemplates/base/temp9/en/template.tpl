{*
* Do not edit the file if you want to upgrade in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}
<table style="margin-bottom: 20px; width: 100%; float: left;" cellpadding="2" cellspacing="0">
    <tbody>
        <tr>
            <td style="width: 50%; text-align: center;">
                <table style="width: 100%;text-align: center;" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr><td class="shop_title">{$shopname|escape:'htmlall':'UTF-8'}</td></tr>
                        <tr><td>210 Hoang Quoc Viet Street</td></tr>
                        <tr><td>Invoice: {$invoice_number|escape:'htmlall':'UTF-8'}</td></tr>
                        <tr><td>Date: {$invoice_date|escape:'htmlall':'UTF-8'}</td></tr>
                    </tbody> 
                </table>
            </td>
            <td style="width: 50%; text-align: center;">
                <table style="width: 100%;" cellpadding="2" cellspacing="0">
                    <tbody>
                        <tr><td>{$barcode_invoice|escape:'htmlall':'UTF-8'}</td></tr>
                        <tr>
                            <td style="text-align: center;" class="total_text total_head">{displayPrice:$total_paid|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<table style="margin-bottom: 20px; width: 100%; float: left;" cellpadding="2" cellspacing="0">
    <tbody>
        <tr>
            <td>
                <table style="width: 100%; float: left;" cellpadding="2" cellspacing="0">
                    <tbody>
                        <tr><td class="header_title border_bottom">BILLING ADDRESS</td></tr>
                        <tr>
                            <td>{$billing_firstname|escape:'htmlall':'UTF-8'} {$billing_lastname|escape:'htmlall':'UTF-8'} {$billing_address1|escape:'htmlall':'UTF-8'} {$billing_address2|escape:'htmlall':'UTF-8'}
                                <br /> {$billing_city|escape:'htmlall':'UTF-8'} - {$billing_postcode|escape:'htmlall':'UTF-8'} {$billing_state|escape:'htmlall':'UTF-8'}
                                <br /> {$billing_phone|escape:'htmlall':'UTF-8'} {$billing_phone_mobile|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                        <tr><td class="header_title border_bottom">PAYMENT</td></tr>
                        <tr><td>{$payment|escape:'htmlall':'UTF-8'}</td></tr>
                    </tbody>
                </table>
            </td>
            <td>
                <table style="width: 100%; float: left;" cellpadding="2" cellspacing="0">
                    <tbody>
                        <tr><td class="header_title border_bottom">DELIVERY ADDRESS</td></tr>
                        <tr>
                            <td>{$delivery_firstname|escape:'htmlall':'UTF-8'} {$delivery_lastname|escape:'htmlall':'UTF-8'} {$delivery_address1|escape:'htmlall':'UTF-8'} {$delivery_address2|escape:'htmlall':'UTF-8'}
                                <br /> {$delivery_city|escape:'htmlall':'UTF-8'} - {$delivery_postcode|escape:'htmlall':'UTF-8'} {$delivery_state|escape:'htmlall':'UTF-8'}
                                <br /> {$delivery_phone|escape:'htmlall':'UTF-8'} {$delivery_phone_mobile|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                        <tr><td class="header_title border_bottom">SHIPPING</td></tr>
                        <tr><td>{$order_carrier_name|escape:'htmlall':'UTF-8'}</td></tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<table style="width: 100%;">
    <tbody>
        <tr>
            <td>{$products_list|escape:'htmlall':'UTF-8'}</td>
        </tr>
    </tbody>
</table>
<table style="width: 100%;" cellpadding="3" cellspacing="0">
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
    </tbody>
</table>
<table style="width: 100%;" cellpadding="3" cellspacing="0">
    <tbody>
        <tr>
            <td style="text-align: right; width: 50%;" class="total_text"><strong>Total: </strong></td>
            <td style="text-align: left; width: 50%;" class="total_text">{displayPrice:$total_paid|escape:'htmlall':'UTF-8'}</td>
        </tr>
    </tbody>
</table>
<br />
<table style="width: 100%;">
    <tbody>
        <tr>
            <td style="text-align: right;width: 50%;" class="thanksfor">THANKS FOR<br />YOUR BUSSINESS!</td>
            <td style="text-align: left;width: 50%;">
                <table style="width: 100%;" cellpadding="5" cellspacing="0">
                    <tbody>
                        <tr><td>www.greenwebteam.com<br/>greenwebteam@gmail.com</td></tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>