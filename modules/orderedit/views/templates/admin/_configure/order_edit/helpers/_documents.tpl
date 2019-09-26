{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @version  Release: $Revision: 9856 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<table class="table" width="100%;" cellspacing="0" cellpadding="0" id="documents_table">
	<thead>
		<tr>
			<th style="width:10%">{l s='Date' mod='orderedit'}</th>
			<th style="">{l s='Document' mod='orderedit'}</th>
			<th style="width:20%">{l s='Number' mod='orderedit'}</th>
			<th style="width:10%">{l s='Amount' mod='orderedit'}</th>
			<th style="width:1%"></th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$order->getDocuments() item=document}

		{if get_class($document) eq 'OrderInvoice'}
			{if isset($document->is_delivery)}
			<tr class="invoice_line" id="delivery_{$document->id|escape:'html':'UTF-8'}">
			{else}
			<tr class="invoice_line" id="invoice_{$document->id|escape:'html':'UTF-8'}">
			{/if}
		{elseif get_class($document) eq 'OrderSlip'}
			<tr class="invoice_line" id="orderslip_{$document->id|escape:'html':'UTF-8'}">
		{/if}

		<td class="document_date">{*$document->date_add}{dateFormat date=$document->date_add*}
			{if get_class($document) eq 'OrderInvoice'}
				{if isset($document->is_delivery)}
			<input type="hidden" name="documentId" rel="documentId" value="{$document->id|escape:'html':'UTF-8'}">
			<div class="editable">
				{if $can_edit}
					<p class="customVal" style="display:none;">
						<span></span>
					</p>
				{/if}
				<p class="displayVal">
					<span class="document_datedelivery_show ">{$document->date_add|escape:'html':'UTF-8'}</span>
				</p>
				{if $can_edit}
					<p class="realVal" style="display:none;">
                        <span class="document_datedelivery_edit">
                            <input type="text" class="datetime_pick" rel="documentDatedelivery" value="{$document->date_add|escape:'html':'UTF-8'}" />
                        </span>
					</p>
				{/if}
			</div>
				{else}
			<input type="hidden" name="documentId" rel="documentId" value="{$document->id|escape:'html':'UTF-8'}">
			<div class="editable">
				{if $can_edit}
					<p class="customVal" style="display:none;">
						<span></span>
					</p>
				{/if}
				<p class="displayVal">
					<span class="document_dateadd_show ">{$document->date_add|escape:'html':'UTF-8'}</span>
				</p>
				{if $can_edit}
					<p class="realVal" style="display:none;">
                        <span class="document_dateadd_edit">
                            <input type="text" class="datetime_pick" rel="documentDateadd" value="{$document->date_add|escape:'html':'UTF-8'}" />
                        </span>
					</p>
				{/if}
			</div>
				{/if}
			{else}
				{dateFormat date=$document->date_add}
			{/if}
		</td>
		<td class="document_type">
			{if get_class($document) eq 'OrderInvoice'}
				{if isset($document->is_delivery)}
					{l s='Delivery slip' mod='orderedit'}
				{else}
					{l s='Invoice' mod='orderedit'}
				{/if}
			{elseif get_class($document) eq 'OrderSlip'}
				{l s='Credit Slip' mod='orderedit'}
			{/if}</td>
		<td class="document_number">
			{if get_class($document) eq 'OrderInvoice'}
				{if isset($document->is_delivery)}
					<a target="_blank" href="{$link->getAdminLink('AdminPdf')|escape:'htmlall':'UTF-8'}&submitAction=generateDeliverySlipPDF&id_order_invoice={$document->id|escape:'html':'UTF-8'}">
			   	{else}
					<a target="_blank" href="{$link->getAdminLink('AdminPdf')|escape:'htmlall':'UTF-8'}&submitAction=generateInvoicePDF&id_order_invoice={$document->id|escape:'html':'UTF-8'}">
			   {/if}
			{elseif get_class($document) eq 'OrderSlip'}
				<a target="_blank" href="{$link->getAdminLink('AdminPdf')|escape:'htmlall':'UTF-8'}&submitAction=generateOrderSlipPDF&id_order_slip={$document->id|escape:'html':'UTF-8'}">
			{/if}
			{if get_class($document) eq 'OrderInvoice'}
				{if isset($document->is_delivery)}
					#{Configuration::get('PS_DELIVERY_PREFIX', $current_id_lang, null, $order->id_shop)|escape:'html':'UTF-8'}{'%06d'|sprintf:$document->delivery_number}
				{else}
					{$document->getInvoiceNumberFormatted($current_id_lang, $order->id_shop)|escape:'html':'UTF-8'}
				{/if}
			{elseif get_class($document) eq 'OrderSlip'}
				#{Configuration::get('PS_CREDIT_SLIP_PREFIX', $current_id_lang)|escape:'html':'UTF-8'}{'%06d'|sprintf:$document->id}
			{/if}</a></td>
		<td class="document_amount">
		{if get_class($document) eq 'OrderInvoice'}
			{if isset($document->is_delivery)}
				--
			{else}
				{Tools::displayPrice($document->total_paid_tax_incl, (int)$currency->id)|escape:'htmlall':'UTF-8'}&nbsp;
				{if $document->getTotalPaid()}
					<span style="color:red;font-weight:bold;">
					{if $document->getRestPaid() > 0}
						{Tools::displayPrice($document->getRestPaid(), (int)$currency->id)|escape:'htmlall':'UTF-8'} {l s='not paid' mod='orderedit'})
					{else if $document->getRestPaid() < 0}
						{Tools::displayPrice($document->getRestPaid(), (int)$currency->id)|escape:'htmlall':'UTF-8'} {l s='overpaid' mod='orderedit'})
					{/if}
					</span>
				{/if}
			{/if}
		{elseif get_class($document) eq 'OrderSlip'}
			{Tools::displayPrice($document->amount, (int)$currency->id)|escape:'htmlall':'UTF-8'}
		{/if}
		</td>
		<td class="text-right document_action">
			<div class="btn-group btn-group-nowrap" role="group">
				{if get_class($document) eq 'OrderInvoice'}
					{if !isset($document->is_delivery)}
						{if $document->getRestPaid()}
							<a href="#" class="js-set-payment btn btn-default" data-amount="{$document->getRestPaid()|escape:'html':'UTF-8'}" data-id-invoice="{$document->id|escape:'html':'UTF-8'}" title="{l s='Set payment form' mod='orderedit'}"><i class="icon-money" title="{l s='Enter payment' mod='orderedit'}"></i></a>
						{/if}
						<a href="#" class="btn btn-default" onclick="$('#invoiceNote{$document->id|escape:'html':'UTF-8'}').show(); return false;" title="{if $document->note eq ''}{l s='Add note' mod='orderedit'}{else}{l s='Edit note' mod='orderedit'}{/if}">
							{if $document->note eq ''}
								<i class="icon-plus-sign-alt" title="{l s='Add note' mod='orderedit'}"></i>
							{else}
								<i class="icon-pencil" title="{l s='Edit note' mod='orderedit'}"></i>
							{/if}
						</a>
					{/if}
				{/if}
				{if get_class($document) != 'OrderInvoice' || ! isset($document->is_delivery)}
				<a href="#" class="deleteDocument btn btn-default" rel="{get_class($document)|escape:'html':'UTF-8'}^{$document->id|escape:'html':'UTF-8'}" title="{l s='Delete document' mod='orderedit'}">
					<i class="icon-trash" alt="{l s='Delete document' mod='orderedit'}"></i>
				</a>
				{/if}
			</div>
		</td>
	</tr>
	{if get_class($document) eq 'OrderInvoice'}
		{if !isset($document->is_delivery)}
	<tr id="invoiceNote{$document->id|escape:'html':'UTF-8'}" style="display:none" class="current-edit">
		<td colspan="5">
			<form action="{$current_index|escape:'quotes':'UTF-8'}&viewOrder&id_order={$order->id|escape:'html':'UTF-8'}" method="post">
				<p>
					<label for="editNote{$document->id|escape:'html':'UTF-8'}" class="t">{l s='Note' mod='orderedit'}</label>
					<input type="hidden" name="id_order_invoice" value="{$document->id|escape:'html':'UTF-8'}" />
					<textarea name="note" id="editNote{$document->id|escape:'html':'UTF-8'}" class="edit-note textarea-autosize">{$document->note|escape:'html':'UTF-8'}</textarea>
				</p>
				<p class="right">
					<button type="submit" name="submitEditNote" class="btn btn-default">
						<i class="icon-save"></i>
						{l s='Save' mod='orderedit'}
					</button>
					<a href="#" class="btn btn-default" id="cancelNote" onclick="$('#invoiceNote{$document->id|escape:'html':'UTF-8'}').hide();return false;"><i class="icon-remove"></i> {l s='Cancel' mod='orderedit'}</a>
				</p>
			</form>
		</td>
	</tr>
		{/if}
	{/if}
	{foreachelse}
	<tr>
		<td colspan="5" class="list-empty">
			<div class="list-empty-msg">
				<i class="icon-warning-sign list-empty-icon"></i>
				{l s='There is no available document' mod='orderedit'}
			</div>
			{if isset($invoice_management_active) && $invoice_management_active}
				<a id="generateInvoiceBtn" class="btn btn-default" href="{$current_index|escape:'html':'UTF-8'}&amp;viewOrder&amp;submitGenerateInvoice&amp;id_order={$order->id|escape:'html':'UTF-8'}{if isset($smarty.get.token)}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}{/if}">
					<i class="icon-repeat"></i>
					{l s='Generate invoice' mod='orderedit'}
				</a>
			{/if}
		</td>
	</tr>
	{/foreach}
	</tbody>
</table>
