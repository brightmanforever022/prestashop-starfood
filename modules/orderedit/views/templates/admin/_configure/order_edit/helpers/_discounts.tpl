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
<div class="panel panel-vouchers">
    <input type="hidden" id="discountsTotal" value="{$order->total_discounts_tax_incl|escape:'html':'UTF-8'}" />
    <table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
        <tr>
            <th><img src="../img/admin/coupon.gif" alt="{l s='Discounts' mod='orderedit'}" />{l s='Discount name' mod='orderedit'}</th>
            <th align="center" style="width: 100px">{l s='Value' mod='orderedit'}</th>
            <th align="center" style="width: 30px">{l s='Action' mod='orderedit'}</th>
        </tr>
        {foreach from=$discounts item=discount}
        <tr>
            <td>
                <div class="editable">
                    {if $can_edit}
                    <p class="customVal" style="display:none;">
                        <span></span>
                    </p>
                    {/if}
                    <p class="displayVal">
                        <span class="discountName">{$discount['name']|escape:'html':'UTF-8'}</span>
                    </p>
                    {if $can_edit}
                    <p class="realVal" style="display:none;">
                        <span class="discount_name_edit">
                            <input type="text" name="discount_name" class="edit_discount_name" rel="discountNameEdit" value="{$discount['name']|escape:'html':'UTF-8'}" />
                        </span>
                    </p>
                    {/if}
                </div>
            </td>
            <td align="center">
                {if $discount['value'] != 0.00}
                    -
                {/if}
                <div class="editable">
                    <input type="hidden" name="id_order_discount" rel="orderDiscountId" value="{$discount.id_order_cart_rule|escape:'html':'UTF-8'}" />
                    <input type="hidden" name="discount_tax_excl_original" rel="orderDiscountTaxExclOriginal" value="{$discount['value_tax_excl']|escape:'html':'UTF-8'}" />
                    <input type="hidden" name="discount_tax_incl_original" rel="orderDiscountTaxInclOriginal" value="{$discount['value']|escape:'html':'UTF-8'}" />
                    <input type="hidden" name="discount_id_invoice" rel="orderDiscountInvoiceId" value="{$discount['id_order_invoice']|escape:'html':'UTF-8'}" />
                    {if $can_edit}
                    <p class="customVal" style="display:none;">
                        <span></span>
                    </p>
                    {/if}
                    <p class="displayVal">
                        <span class="displayVal order_discount_show">{Tools::displayPrice($discount['value'], (int)$currency->id)|escape:'htmlall':'UTF-8'}</span>
                    </p>
                    {if $can_edit}
                    <p class="realVal" style="display:none;">
                        <span class="order_discount_edit">
                            {if $currency->sign % 2}{$currency->sign|escape:'html':'UTF-8'}{/if}
                            <input type="text" name="discount_tax_excl" class="edit_discount_price_tax_excl edit_discount_price" rel="discountPriceEdit" value="{Tools::ps_round($discount['value_tax_excl'], 2)|escape:'html':'UTF-8'}" size="5" /> {if !($currency->sign % 2)}{$currency->sign|escape:'html':'UTF-8'}{/if} {l s='tax excl.' mod='orderedit'}<br />
                            {if $currency->sign % 2}{$currency->sign|escape:'html':'UTF-8'}{/if}
                            <input type="text" name="discount_tax_incl" class="edit_discount_price_tax_incl edit_discount_price" rel="discountPriceWtEdit" value="{Tools::ps_round($discount['value'], 2)|escape:'html':'UTF-8'}" size="5" /> {if !($currency->sign % 2)}{$currency->sign|escape:'html':'UTF-8'}{/if} {l s='tax incl.' mod='orderedit'}
                        </span>
                    </p>
                    {/if}
                </div>
            </td>
            <td class="center">
                <a href="#" rel="{$discount['id_order_cart_rule']|escape:'html':'UTF-8'}" class="deleteDiscount"><img src="../img/admin/delete.gif" alt="{l s='Delete voucher' mod='orderedit'}" /></a>
            </td>
        </tr>
        {/foreach}
        <tr>
            <td colspan="3" class="center">
                <a class="btn btn-default" href="#" id="add_voucher"><i class="icon-ticket"></i> {l s='Add a new discount' mod='orderedit'}</a>
            </td>
        </tr>
        <tr style="display: none" >
            <td colspan="3" class="current-edit" id="voucher_form">
                {include file="$orderedit_tpl_dir/helpers/_discount_form.tpl"}
            </td>
        </tr>
    </table>
</div>
