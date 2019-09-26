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
<div style="float:left;width: 100%;">
    {if $can_edit}
    <div style="float: left;"><a href="#" class="add_product btn btn-default"><i class="icon-plus-sign"></i> {l s='Add a product' mod='orderedit'}</a></div>
    <div style="float: right; margin-right: 10px" id="refundForm">
    <!--
        <a href="#" class="standard_refund"><img src="../img/admin/add.gif" alt="{l s='Process a standard refund' mod='orderedit'}" /> {l s='Process a standard refund' mod='orderedit'}</a>
        <a href="#" class="partial_refund"><img src="../img/admin/add.gif" alt="{l s='Process a partial refund' mod='orderedit'}" /> {l s='Process a partial refund' mod='orderedit'}</a>
    -->
    </div>
    <br clear="left" /><br />
    {/if}
    {if $can_edit}
        {include file="$orderedit_tpl_dir/helpers/_new_product.tpl"}
    {/if}
    <table style="width: 100%;" cellspacing="0" cellpadding="0" class="table" id="orderProducts">
        <thead>
            <tr>
                <th height="39" align="center" style="width: 7%">&nbsp;</th>
                <th>{l s='Product' mod='orderedit'}</th>
                <th>{l s='Reference' mod='orderedit'}</th>
                <th style="width: 15%;">{l s='Supplier Reference' mod='orderedit'}</th>
                <th style="width: 8%;">{l s='Unit Weight' mod='orderedit'} <sup>*</sup></th>
                <th style="width: 8%; text-align: center">{l s='Reduction %' mod='orderedit'}</th>
                <th style="width: 8%; text-align: center">{l s='Unit Price' mod='orderedit'} <sup>*</sup></th>
                <th style="width: 8%; text-align: center">{l s='Tax rate' mod='orderedit'} <sup>*</sup></th>
                <th style="width: 4%; text-align: center">{l s='Qty' mod='orderedit'}</th>
                <th style="width: 10%; text-align: center">{l s='Total' mod='orderedit'} <sup>*</sup></th>
                <th colspan="2" style="display: none;" class="add_product_fields">&nbsp;</th>
                <th colspan="2" style="display: none;" class="edit_product_fields">&nbsp;</th>
                <th colspan="2" style="display: none;" class="standard_refund_fields">
                    <img src="../img/admin/delete.gif" alt="{l s='Products' mod='orderedit'}" />
                    {if ($order->hasBeenDelivered() || $order->hasBeenShipped())}
                        {l s='Return' mod='orderedit'}
                    {elseif ($order->hasBeenPaid())}
                        {l s='Refund' mod='orderedit'}
                    {else}
                        {l s='Cancel' mod='orderedit'}
                    {/if}
                </th>
                <th style="width: 12%;text-align:right;display:none" class="partial_refund_fields">
                    {l s='Partial refund' mod='orderedit'}
                </th>
                <th style="width: 8%;text-align:center;">
                    {l s='Action' mod='orderedit'}
                </th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$products item=product key=k name=i}
                {* Include customized datas partial *}
                {include file="$orderedit_tpl_dir/helpers/_customized_data.tpl" index=$smarty.foreach.i.index}

                {* Include product line partial *}
                {include file="$orderedit_tpl_dir/helpers/_product_line.tpl" index=$smarty.foreach.i.index}
            {/foreach}
        </tbody>
    </table>

    <div class="row">
        <div class="alert alert-warning">
            <sup>*</sup> {l s='For this customer group, prices are displayed as:' mod='orderedit'}
            {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                {l s='tax excluded.' mod='orderedit'}
            {else}
                {l s='tax included.' mod='orderedit'}
            {/if}
    
            {if !Configuration::get('PS_ORDER_RETURN')}
                <br /><br />{l s='Merchandise returns are disabled' mod='orderedit'}
            {/if}
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-6">
            <div id="discounts_wrapper">
                {include file="$orderedit_tpl_dir/helpers/_discounts.tpl"}
            </div>
        </div>
        <div class="col-xs-6">
            <div id="totals_wrapper">
                {include file="$orderedit_tpl_dir/helpers/_totals.tpl"}
            </div>
        </div>
    </div>
</div>