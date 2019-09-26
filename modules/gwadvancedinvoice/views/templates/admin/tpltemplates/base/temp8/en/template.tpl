{*
* Do not edit the file if you want to upgrade in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}
<table style="margin-bottom: 20px; width: 100%; float: left;"   cellpadding="2" cellspacing="0">
    <tbody>
        <tr>
            <td style="width: 35%;text-align: center;">{$shopname|escape:'htmlall':'UTF-8'}</td>
            <td style="width: 65%;text-align: center;">210 Hoang Quoc Viet Street</td>
        </tr>
        <tr>
            <td style="width: 35%;text-align: center;" class="invoice_title">INVOICE</td>
            <td style="width: 65%;text-align: center;">{$barcode_invoice|escape:'htmlall':'UTF-8'}</td>
        </tr>
        <tr>
            <td style="text-align: center;">{$invoice_date|escape:'htmlall':'UTF-8'}</td>
            <td style="text-align: center;"></td>
        </tr>
    </tbody>
</table>
<table style="width: 100%;" cellpadding="3" cellspacing="0">
    <tbody>
        <tr>
            <td style="text-align: right; width: 30%;" class="total_text">{$invoice_number|escape:'htmlall':'UTF-8'}</td>
            <td style="text-align: right; width: 35%;" class="total_text"><strong>Total: </strong></td>
            <td style="text-align: left; width: 35%;" class="total_text">{displayPrice:$total_paid|escape:'htmlall':'UTF-8'}</td>
        </tr>
    </tbody>
</table>
<table style="margin-bottom: 20px; width: 100%; float: left;"   cellpadding="2" cellspacing="0">
    <tbody>
        <tr>
            <td colspan="2" class="header_title border_bottom">BILLING ADDRESS</td>
        </tr>
        <tr>
            <td colspan="2">{$billing_firstname|escape:'htmlall':'UTF-8'} {$billing_lastname|escape:'htmlall':'UTF-8'} {$billing_address1|escape:'htmlall':'UTF-8'} {$billing_address2|escape:'htmlall':'UTF-8'}
                <br /> {$billing_city|escape:'htmlall':'UTF-8'} - {$billing_postcode|escape:'htmlall':'UTF-8'} {$billing_state|escape:'htmlall':'UTF-8'}
                <br /> {$billing_phone|escape:'htmlall':'UTF-8'} {$billing_phone_mobile|escape:'htmlall':'UTF-8'}</td>
        </tr>
        <tr>
            <td colspan="2" class="header_title border_bottom">DELIVERY ADDRESS</td>
        </tr>
        <tr>
            <td colspan="2">{$delivery_firstname|escape:'htmlall':'UTF-8'} {$delivery_lastname|escape:'htmlall':'UTF-8'} {$delivery_address1|escape:'htmlall':'UTF-8'} {$delivery_address2|escape:'htmlall':'UTF-8'}
                <br /> {$delivery_city|escape:'htmlall':'UTF-8'} - {$delivery_postcode|escape:'htmlall':'UTF-8'} {$delivery_state|escape:'htmlall':'UTF-8'}
                <br /> {$delivery_phone|escape:'htmlall':'UTF-8'} {$delivery_phone_mobile|escape:'htmlall':'UTF-8'}</td>
        </tr>
        <tr>
            <td style="width: 50%;" class="header_title">PAYMENT:</td>
            <td style="width: 50%;">{$payment|escape:'htmlall':'UTF-8'}</td>
        </tr>
        <tr>
            <td style="width: 50%;" class="header_title">SHIPPING:</td>
            <td style="width: 50%;">{$order_carrier_name|escape:'htmlall':'UTF-8'}</td>
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
<table style="width: 100%;"  cellpadding="3" cellspacing="0">
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
<table style="width: 100%;"cellpadding="3" cellspacing="0">
    <tbody>
        <tr>
            <td style="text-align: center;width: 30%;">{$logo|escape:'htmlall':'UTF-8'}</td>
            <td style="text-align: center;width: 70%;" class="thanksfor">Thank for your bussiness !</td>
        </tr>
    </tbody>
</table>