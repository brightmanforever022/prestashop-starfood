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
                <td style="width: 30%;">
                </td>
                <td style="width: 30%;">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td style="text-align:right;">
                                    <h4 class="invoice_title">INVOICE</h4>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 9pt;text-align:right;">{$invoice_date|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 9pt;text-align:right;">{$invoice_number|escape:'htmlall':'UTF-8'}</td>
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
                <td style="width: 34%;">
                    <h4 class="shop_title">{$shopname|escape:'htmlall':'UTF-8'}</h4>
                </td>
                <td style="width: 33%;">
                    <h4 class="header_title">Delyvery Address</h4>
                    <p>{$delivery_firstname|escape:'htmlall':'UTF-8'} {$delivery_lastname|escape:'htmlall':'UTF-8'}
                        <br /> {$delivery_address1|escape:'htmlall':'UTF-8'} {$delivery_address2|escape:'htmlall':'UTF-8'}
                        <br /> {$delivery_city|escape:'htmlall':'UTF-8'} - {$delivery_postcode|escape:'htmlall':'UTF-8'} {$delivery_state|escape:'htmlall':'UTF-8'}
                        <br /> {$delivery_phone|escape:'htmlall':'UTF-8'}
                        <br /> {$delivery_phone_mobile|escape:'htmlall':'UTF-8'}</p>
                </td>
                <td style="width: 33%;">
                    <h4 class="header_title">Billing Address</h4>
                    <p>{$billing_firstname|escape:'htmlall':'UTF-8'} {$billing_lastname|escape:'htmlall':'UTF-8'}
                        <br /> {$billing_address1|escape:'htmlall':'UTF-8'} {$billing_address2|escape:'htmlall':'UTF-8'}
                        <br /> {$billing_city|escape:'htmlall':'UTF-8'} - {$billing_postcode|escape:'htmlall':'UTF-8'} {$billing_state|escape:'htmlall':'UTF-8'}
                        <br /> {$billing_phone|escape:'htmlall':'UTF-8'}
                        <br /> {$billing_phone_mobile|escape:'htmlall':'UTF-8'}</p>
                </td>
                
            </tr>
        </tbody>
    </table>
    <br />
    <table style="width: 100%; float: left;"  cellpadding="7" cellspacing="0">
        <thead>
            <tr>
                <th class="header small" style="width: 25%;text-align:center;">Invoice Number</th>
                <th class="header small"  style="width: 25%;text-align:center;">Invoice Date</th>
                <th class="header small"  style="width: 25%;text-align:center;">Order Reference</th>
                <th class="header small"  style="width: 25%;text-align:center;">Order date</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 25%;text-align:center;">{$invoice_number|escape:'htmlall':'UTF-8'}</td>
                <td style="width: 25%;text-align:center;">{$invoice_date|escape:'htmlall':'UTF-8'}</td>
                <td style="width: 25%;text-align:center;">{$reference|escape:'htmlall':'UTF-8'}</td>
                <td style="width: 25%;text-align:center;">{$date_add|escape:'htmlall':'UTF-8'}</td>
            </tr>
        </tbody>
    </table>
    <br /> {$products_list|escape:'htmlall':'UTF-8'}
    <br />
    <table style="float: left; width: 100%;">
        <tbody>
            <tr>
                <td style="float: left; width: 45%;">
                    {$tax_tab|escape:'htmlall':'UTF-8'}
                    <br />
                    <table class="total_box" style="width: 100%;" cellpadding="7" cellspacing="0">
                        <tbody>
                            <tr>
                                <td style="width:40%;"  class="total_left">Payment Method</td>
                                <td style="width:35%;">{$payment|escape:'htmlall':'UTF-8'}</td>
                                <td style="width:25%;">{displayPrice:$total_paid|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="float: right; width: 10%;"></td>
                <td style="float: right; width: 45%;">
                    <table class="total_box" style="width: 100%;" cellpadding="7" cellspacing="0">
                        <tbody>
                            <tr>
                                <td class="total_left" style="text-align: right; width: 60%;">Total Product:</td>
                                <td style="text-align: center; width: 40%;">{displayPrice:$total_products|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td class="total_left" style="text-align: right; width: 60%;">Total Discounts:</td>
                                <td style="text-align: center; width: 40%;">-{displayPrice:$total_discounts_tax_excl|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td class="total_left" style="text-align: right; width: 60%;">Shipping Cost :</td>
                                <td style="text-align: center; width: 40%;">{displayPrice:$total_shipping_tax_excl|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td class="total_left" style="text-align: right; width: 60%;">Total Tax :</td>
                                <td style="text-align: center; width: 40%;">{displayPrice:$footer.total_taxes|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                            <tr>
                                <td class="total_left" style="text-align: right; width: 60%;">Total:</td>
                                <td style="text-align: center; width: 40%;">{displayPrice:$total_paid|escape:'htmlall':'UTF-8'}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>