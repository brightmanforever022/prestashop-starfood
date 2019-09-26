{**
* OrderEdit
*
* @category  Module
* @author    silbersaiten <info@silbersaiten.de>
* @support   silbersaiten <support@silbersaiten.de>
* @copyright 2015 silbersaiten
* @version   1.0.0
* @link      http://www.silbersaiten.de
* @license   See joined file licence.txt
*}
<div class="panel panel-total">
    <table class="table" width="450px;" style="border-radius:0px;"cellspacing="0" cellpadding="0">
        <tr id="total_products">
            <td width="150px;"><b>{l s='Products' mod='orderedit'}</b></td>
            <td class="amount" align="right">{Tools::displayPrice($order->total_products_wt, (int)$currency->id)|escape:'htmlall':'UTF-8'}</td>
            <td class="partial_refund_fields current-edit" style="display:none;">&nbsp;</td>
        </tr>
        <tr id="total_discounts">
            <td><b>{l s='Discounts' mod='orderedit'}</b></td>
            <td class="amount" align="right">-{Tools::displayPrice($order->total_discounts_tax_incl, (int)$currency->id)|escape:'htmlall':'UTF-8'}</td>
            <td class="partial_refund_fields current-edit" style="display:none;">&nbsp;</td>
        </tr>
        <tr id="total_wrapping">
            <td><b>{l s='Wrapping' mod='orderedit'}</b></td>
            <td class="amount" align="right">{Tools::displayPrice($order->total_wrapping_tax_incl, (int)$currency->id)|escape:'htmlall':'UTF-8'}</td>
            <td class="partial_refund_fields current-edit" style="display:none;">&nbsp;</td>
        </tr>
        <tr id="total_shipping">
            <td><b>{l s='Shipping' mod='orderedit'}</b></td>
            <td class="amount" align="right">{Tools::displayPrice($order->total_shipping_tax_incl, (int)$currency->id)|escape:'htmlall':'UTF-8'}</td>
            <td class="partial_refund_fields current-edit" style="display:none;">{$currency->prefix|escape:'html':'UTF-8'}<input type="text" size="3" name="partialRefundShippingCost" value="0" />{$currency->suffix|escape:'html':'UTF-8'}</td>
        </tr>
        <tr style="font-size: 20px" id="total_order">
            <td style="font-size: 20px">{l s='Total' mod='orderedit'}</td>
            <td class="amount" style="font-size: 20px" align="right">
                {Tools::displayPrice($order->total_paid_tax_incl, (int)$currency->id)|escape:'htmlall':'UTF-8'}
            </td>
            <td class="partial_refund_fields current-edit" style="display:none;">&nbsp;</td>
        </tr>
    </table>
</div>