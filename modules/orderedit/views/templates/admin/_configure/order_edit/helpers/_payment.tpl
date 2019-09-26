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
<!-- Payments block -->
<div class="panel">
        <div class="panel-heading">
                <i class="icon-money"></i>
                {l s='Payment' mod='orderedit'} <span class="badge">{$order->getOrderPayments()|@count|escape:'html':'UTF-8'}</span>
        </div>
        {if count($order->getOrderPayments()) > 0}
                <p class="alert alert-danger" style="{if round($orders_total_paid_tax_incl, 2) == round($total_paid, 2) || $currentState->id == 6}display: none;{/if}">
                        {l s='Warning' mod='orderedit'}
                        <strong>{Tools::displayPrice($total_paid, (int)$currency->id)|escape:'htmlall':'UTF-8'}</strong>
                        {l s='paid instead of' mod='orderedit'}
                        <strong class="total_paid">{Tools::displayPrice($orders_total_paid_tax_incl, (int)$currency->id)|escape:'htmlall':'UTF-8'}</strong>
                        {foreach $order->getBrother() as $brother_order}
                                {if $brother_order@first}
                                        {if count($order->getBrother()) == 1}
                                                <br />{l s='This warning also concerns order ' mod='orderedit'}
                                        {else}
                                                <br />{l s='This warning also concerns the next orders:' mod='orderedit'}
                                        {/if}
                                {/if}
                                <a href="{$current_index|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$brother_order->id|escape:'html':'UTF-8'}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
                                        #{'%06d'|sprintf:$brother_order->id|escape:'html':'UTF-8'}
                                </a>
                        {/foreach}
                </p>
        {/if}
        <form id="formAddPayment"  method="post">
                <div class="table-responsive">
                        <table class="table">
                                <thead>
                                        <tr>
                                                <th><span class="title_box ">{l s='Date' mod='orderedit'}</span></th>
                                                <th><span class="title_box ">{l s='Payment method' mod='orderedit'}</span></th>
                                                <th><span class="title_box ">{l s='Transaction ID' mod='orderedit'}</span></th>
                                                <th><span class="title_box ">{l s='Amount' mod='orderedit'}</span></th>
                                                <th><span class="title_box ">{l s='Invoice' mod='orderedit'}</span></th>
                                                <th></th>
                                        </tr>
                                </thead>
                                <tbody>
                                        {foreach from=$order->getOrderPaymentCollection() item=payment}
                                        <tr class="payment_line">
                                                <td>
                                                    <div class="editable">
                                                        <input type="hidden" name="id_payment" rel="orderPayment" value="{$payment->id|escape:'html':'UTF-8'}" />
                                                        {if $can_edit}
                                                        <p class="customVal" style="display:none;">
                                                            <span></span>
                                                        </p>
                                                        {/if}
                                                        <p class="displayVal">
                                                            <span class="payment_date_show ">{$payment->date_add|escape:'html':'UTF-8'}</span>
                                                        </p>
                                                        {if $can_edit}
                                                        <p class="realVal" style="display:none;">
                                                            <span class="payment_date_edit">
                                                                <input type="text" class="datetime_pick" id="payment_date_{$payment->id|escape:'html':'UTF-8'}" rel="paymentDate" value="{$payment->date_add|escape:'html':'UTF-8'}" />
                                                            </span>
                                                        </p>
                                                        {/if}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="editable">
                                                        {if $can_edit}
                                                            <p class="customVal" style="display:none;">
                                                                <span></span>
                                                            </p>
                                                        {/if}
                                                        <p class="displayVal">
                                                            <span class="payment_name_show">{if (stristr($order->payment, $payment->payment_method))}{$order->payment|escape:'html':'UTF-8'}{else}{$payment->payment_method|escape:'html':'UTF-8'}{/if}</span>
                                                        </p>
                                                        {if $can_edit}
                                                            <p class="realVal" style="display:none;">
                                                            <span class="payment_name_edit">
                                                                <input type="text" id="payment_name_{$payment->id|escape:'html':'UTF-8'}" rel="paymentName" value="{if (stristr($order->payment, $payment->payment_method))}{$order->payment|escape:'html':'UTF-8'}{else}{$payment->payment_method|escape:'html':'UTF-8'}{/if}" />
                                                            </span>
                                                            </p>
                                                        {/if}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="editable">
                                                        {if $can_edit}
                                                        <p class="customVal" style="display:none;">
                                                            <span></span>
                                                        </p>
                                                        {/if}
                                                        <p class="displayVal">
                                                            <span class="payment_transaction_show">{$payment->transaction_id|escape:'html':'UTF-8'}</span>
                                                        </p>
                                                        {if $can_edit}
                                                        <p class="realVal" style="display:none;">
                                                            <span class="payment_transaction_edit">
                                                                <input type="text" id="payment_transaction_{$payment->id|escape:'html':'UTF-8'}" rel="paymentTransaction" value="{$payment->transaction_id|escape:'html':'UTF-8'}" />
                                                            </span>
                                                        </p>
                                                        {/if}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="editable">
                                                        {if $can_edit}
                                                        <p class="customVal" style="display:none;">
                                                            <span></span>
                                                        </p>
                                                        {/if}
                                                        <p class="displayVal">
                                                            <span class="payment_amount_show">{Tools::displayPrice($payment->amount, (int)$payment->id_currency)|escape:'htmlall':'UTF-8'}</span>
                                                        </p>
                                                        {if $can_edit}
                                                        <p class="realVal" style="display:none;">
                                                            <span class="payment_amount_edit">
                                                                <input type="text" id="payment_amount_{$payment->id|escape:'html':'UTF-8'}" rel="paymentAmountEdit" value="{$payment->amount|escape:'html':'UTF-8'}" />
                                                            </span>
                                                        </p>
                                                        {/if}
                                                    </div>
                                                </td>
                                                <td>
                                                    {if $invoice = $payment->getOrderInvoice($order->id)}
                                                        {$invoice->getInvoiceNumberFormatted($current_id_lang)|escape:'html':'UTF-8'}
                                                    {else}
                                                        {l s='No invoice' mod='orderedit'}
                                                    {/if}
                                                </td>
                                                <td class="actions">
                                                        <div class="btn-group btn-group-nowrap">
                                                                <button type="button" class="btn btn-default open_payment_information">
                                                                <i class="icon-search"></i>
                                                                {l s='Details' mod='orderedit'}
                                                                </button>
                                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                                        <span class="caret"></span>
                                                                </button>
                                                                <ul class="dropdown-menu" role="menu">
                                                                        <li>
                                                                            <a href="#" class="delete_payment_from_order btn btn-default" rel="{$payment->id|escape:'html':'UTF-8'}">
                                                                               <i class="icon-trash"></i> {l s='Delete' mod='orderedit'}
                                                                            </a>
                                                                        </li>
                                                                </ul>
                                                        </div>
                                                </td>
                                        </tr>
                                        <tr class="payment_information" style="display: none;">
                                                <td colspan="5">
                                                        <p>
                                                                <b>{l s='Card Number' mod='orderedit'}</b>&nbsp;
                                                                <div class="editable">
                                                                    <input type="hidden" name="id_payment" rel="orderPayment" value="{$payment->id|escape:'html':'UTF-8'}" />
                                                                    {if $can_edit}
                                                                    <p class="customVal" style="display:none;">
                                                                        <span></span>
                                                                    </p>
                                                                    {/if}
                                                                    <p class="displayVal">
                                                                        <span class="card_number_show">{if $payment->card_number}{$payment->card_number|escape:'html':'UTF-8'}{else}{l s='Not defined' mod='orderedit'}{/if}</span>
                                                                    </p>
                                                                    {if $can_edit}
                                                                    <p class="realVal" style="display:none;">
                                                                        <span class="card_number_edit">
                                                                            <input type="text" id="card_number_{$payment->id|escape:'html':'UTF-8'}" rel="cardNumber" value="{if $payment->card_number}{$payment->card_number|escape:'html':'UTF-8'}{/if}" />
                                                                        </span>
                                                                    </p>
                                                                    {/if}
                                                                </div>
                                                        </p>
                                                        <p>
                                                                <b>{l s='Card Brand' mod='orderedit'}</b>&nbsp;
                                                                <div class="editable">
                                                                    {if $can_edit}
                                                                    <p class="customVal" style="display:none;">
                                                                        <span></span>
                                                                    </p>
                                                                    {/if}
                                                                    <p class="displayVal">
                                                                        <span class="card_brand_show">{if $payment->card_brand}{$payment->card_brand|escape:'html':'UTF-8'}{else}{l s='Not defined' mod='orderedit'}{/if}</span>
                                                                    </p>
                                                                    {if $can_edit}
                                                                    <p class="realVal" style="display:none;">
                                                                        <span class="card_brand_edit">
                                                                            <input type="text" id="card_brand_{$payment->id|escape:'html':'UTF-8'}" rel="cardBrand" value="{if $payment->card_brand}{$payment->card_brand|escape:'html':'UTF-8'}{/if}" />
                                                                        </span>
                                                                    </p>
                                                                    {/if}
                                                                </div>
                                                        </p>
                                                        <p>
                                                                <b>{l s='Card Expiration' mod='orderedit'}</b>&nbsp;
                                                                <div class="editable">
                                                                    {if $can_edit}
                                                                    <p class="customVal" style="display:none;">
                                                                        <span></span>
                                                                    </p>
                                                                    {/if}
                                                                    <p class="displayVal">
                                                                        <span class="card_expiration_show">{if $payment->card_expiration}{$payment->card_expiration|escape:'html':'UTF-8'}{else}{l s='Not defined' mod='orderedit'}{/if}</span>
                                                                    </p>
                                                                    {if $can_edit}
                                                                    <p class="realVal" style="display:none;">
                                                                        <span class="card_expiration_edit">
                                                                            <input type="text" id="card_expiration_{$payment->id|escape:'html':'UTF-8'}" rel="cardExpiration" value="{if $payment->card_expiration}{$payment->card_expiration|escape:'html':'UTF-8'}{/if}" />
                                                                        </span>
                                                                    </p>
                                                                    {/if}
                                                                </div>
                                                        </p>
                                                        <p>
                                                                <b>{l s='Card Holder' mod='orderedit'}</b>&nbsp;
                                                                <div class="editable">
                                                                    {if $can_edit}
                                                                    <p class="customVal" style="display:none;">
                                                                        <span></span>
                                                                    </p>
                                                                    {/if}
                                                                    <p class="displayVal">
                                                                        <span class="card_holder_show">{if $payment->card_holder}{$payment->card_holder|escape:'html':'UTF-8'}{else}{l s='Not defined' mod='orderedit'}{/if}</span>
                                                                    </p>
                                                                    {if $can_edit}
                                                                    <p class="realVal" style="display:none;">
                                                                        <span class="card_holder_edit">
                                                                            <input type="text" id="card_expiration_{$payment->id|escape:'html':'UTF-8'}" rel="cardHolder" value="{if $payment->card_holder}{$payment->card_holder|escape:'html':'UTF-8'}{/if}" />
                                                                        </span>
                                                                    </p>
                                                                    {/if}
                                                                </div>
                                                        </p>
                                                </td>
                                        </tr>
                                        {foreachelse}
                                        <tr>
                                                <td class="list-empty hidden-print" colspan="6">
                                                        <div class="list-empty-msg">
                                                                <i class="icon-warning-sign list-empty-icon"></i>
                                                                {l s='No payment methods are available' mod='orderedit'}
                                                        </div>
                                                </td>
                                        </tr>
                                        {/foreach}
                                        <tr class="current-edit hidden-print">
                                                <td>
                                                        <div class="input-group fixed-width-xl">
                                                                <input type="text" name="payment_date" class="datepicker" value="{date('Y-m-d')|escape:'html':'UTF-8'}" />
                                                                <div class="input-group-addon">
                                                                        <i class="icon-calendar-o"></i>
                                                                </div>
                                                        </div>
                                                </td>
                                                <td>
                                                        <select name="payment_method" class="payment_method">
                                                        {foreach from=$payment_methods item=payment_method}
                                                                <option value="{$payment_method|escape:'html':'UTF-8'}">{$payment_method|escape:'html':'UTF-8'}</option>
                                                        {/foreach}
                                                        </select>
                                                </td>
                                                <td>
                                                        <input type="text" name="payment_transaction_id" value="" class="form-control fixed-width-sm"/>
                                                </td>
                                                <td>
                                                        <input type="text" name="payment_amount" value="" class="form-control fixed-width-sm pull-left" />
                                                        <select name="payment_currency" class="payment_currency form-control fixed-width-xs pull-left">
                                                                {foreach from=$currencies item=current_currency}
                                                                        <option value="{$current_currency['id_currency']|escape:'html':'UTF-8'}"{if $current_currency['id_currency'] == $currency->id} selected="selected"{/if}>{$current_currency['sign']|escape:'html':'UTF-8'}</option>
                                                                {/foreach}
                                                        </select>
                                                </td>
                                                <td>
                                                        {if count($invoices_collection) > 0}
                                                                <select name="payment_invoice" id="payment_invoice">
                                                                {foreach from=$invoices_collection item=invoice}
                                                                        <option value="{$invoice->id|escape:'html':'UTF-8'}" selected="selected">{$invoice->getInvoiceNumberFormatted($current_id_lang, $order->id_shop)|escape:'html':'UTF-8'}</option>
                                                                {/foreach}
                                                                </select>
                                                        {/if}
                                                </td>
                                                <td class="actions">
                                                        <button class="btn btn-primary btn-block" type="submit" name="submitAddPayment">
                                                                {l s='Add' mod='orderedit'}
                                                        </button>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                </div>
        </form>
        {if (!$order->valid && sizeof($currencies) > 1)}
                <form class="form-horizontal well" method="post">
                        <div class="row">
                                <label class="control-label col-lg-3">{l s='Change currency' mod='orderedit'}</label>
                                <div class="col-lg-6">
                                        <select name="new_currency">
                                        {foreach from=$currencies item=currency_change}
                                                {if $currency_change['id_currency'] != $order->id_currency}
                                                <option value="{$currency_change['id_currency']|escape:'html':'UTF-8'}">{$currency_change['name']|escape:'html':'UTF-8'} - {$currency_change['sign']|escape:'html':'UTF-8'}</option>
                                                {/if}
                                        {/foreach}
                                        </select>
                                        <p class="help-block">{l s='Do not forget to update your exchange rate before making this change.' mod='orderedit'}</p>
                                </div>
                                <div class="col-lg-3">
                                        <button type="submit" class="btn btn-default" name="submitChangeCurrency"><i class="icon-refresh"></i> {l s='Change' mod='orderedit'}</button>
                                </div>
                        </div>
                </form>
        {/if}
</div>