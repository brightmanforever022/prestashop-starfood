{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if !isset($orderDocumentsOnlyTable)}
<div class="table-responsive">
{/if}
	<table class="table" id="documents_table">
		<thead>
			<tr>
				<th>
					<span class="title_box ">{l s='Date'}</span>
				</th>
				<th>
					<span class="title_box ">{l s='Document'}</span>
				</th>
                                <th>
					<span class="title_box ">{l s='Template'}</span>
				</th>
				<th>
					<span class="title_box ">{l s='Number'}</span>
				</th>
				{*<th>
					<span class="title_box ">{l s='Amount'}</span>
				</th>*}
                                <th>
					<span class="title_box ">{l s='Sum to pay'}</span>
				</th>
                                <th>
					<span class="title_box ">{l s='Paid'}</span>
				</th>
                                <th>
					<span class="title_box ">{l s='Due Date'}</span>
				</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$order->getDocuments() item=document}

				{if get_class($document) eq 'OrderInvoice'}
					{if isset($document->is_delivery)}
					<tr id="delivery_{$document->id}" documentId="{$document->id}">
					{else}
					<tr id="invoice_{$document->id}" documentId="{$document->id}">
					{/if}
				{elseif get_class($document) eq 'OrderSlip'}
					<tr id="orderslip_{$document->id}" documentId="{$document->id}">
				{/if}

						<td>{dateFormat date=$document->date_add}<br/>
                        {if isset($document->employee_name)}
                        {$document->employee_name}
                        {/if}
                        </td>
						<td>
							{if get_class($document) eq 'OrderInvoice'}
								{if isset($document->is_delivery)}
									{l s='Delivery slip'}
								{else}
									{l s='Invoice'}
								{/if}
							{elseif get_class($document) eq 'OrderSlip'}
								{l s='Credit Slip'}
							{/if}
						</td>
                                                <td>
                                                    {if get_class($document)!='OrderSlip'}
							{$document->getTemplateName($order->id_lang)}
						    {/if}
                                                </td>
						<td>
							{if get_class($document) eq 'OrderInvoice'}
                                                                    {if isset($document->is_delivery)}
                                                                        <a class="_blank" title="{l s='See the document'}" href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateDeliverySlipPDF&amp;id_order_invoice={$document->id}">
                                                                    {else}
                                                                        <a class="_blank" title="{l s='See the document'}" href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateInvoicePDF&amp;id_order_invoice={$document->id}">
                                                                    {/if}        
							{elseif get_class($document) eq 'OrderSlip'}
								<a class="_blank" title="{l s='See the document'}" href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateOrderSlipPDF&amp;id_order_slip={$document->id}">
							{/if}
							{if get_class($document) eq 'OrderInvoice'}
								{if isset($document->is_delivery)}
									{Configuration::get('PS_DELIVERY_PREFIX', $current_id_lang, null, $order->id_shop)}{'%06d'|sprintf:$document->delivery_number}
								{else}
									{$document->getInvoiceNumberFormatted($current_id_lang, $order->id_shop)}
								{/if}
							{elseif get_class($document) eq 'OrderSlip'}
								{Configuration::get('PS_CREDIT_SLIP_PREFIX', $current_id_lang)}{'%06d'|sprintf:$document->id}
							{/if}
							</a>
						</td>
                                               {*<td>
						{if get_class($document) eq 'OrderInvoice'}
							{if isset($document->is_delivery)}
								--
							{else}
								{displayPrice price=$document->total_paid_tax_incl currency=$currency->id}&nbsp;
								{if $document->getTotalPaid()}
									<span>
									{if $document->getRestPaid() > 0}
										({displayPrice price=$document->getRestPaid() currency=$currency->id} {l s='not paid'})
									{elseif $document->getRestPaid() < 0}
										({displayPrice price=-$document->getRestPaid() currency=$currency->id} {l s='overpaid'})
									{/if}
									</span>
								{/if}
							{/if}
						{elseif get_class($document) eq 'OrderSlip'}
							{displayPrice price=$document->amount+$document->shipping_cost_amount currency=$currency->id}
						{/if}
						</td>*}
						<td {if !isset($document->is_delivery)}class="sumToPay"{/if}>
							{if isset($document->is_delivery)}
								--
							{else}
								{displayPrice price=$document->sum_to_pay currency=$currency->id}
							{/if}
						</td>
                                                <td class="documentPaid">
                                                    <a href="#" class="documentPaidChangeLink list-action-enable {if $document->paid}action-enabled{else}action-disabled{/if}">{if $document->paid}<i class="icon-check"></i>{else}<i class="icon-remove"></i>{/if}</a>
                                                </td>
                                                <td class="dueDate">{dateFormat date=$document->due_date}</td>
						<td class="text-right document_action">
						{if get_class($document) eq 'OrderInvoice'}
							{if !isset($document->is_delivery)}

								{if $document->getRestPaid()}
									<a href="#formAddPaymentPanel" class="js-set-payment btn btn-default anchor" data-amount="{$document->getRestPaid()}" data-id-invoice="{$document->id}" title="{l s='Set payment form'}">
										<i class="icon-money"></i>
										{l s='Enter payment'}
									</a>
								{/if}

								<a href="#" class="btn btn-default" onclick="$('#invoiceNote{$document->id}').show(); return false;" title="{if $document->note eq ''}{l s='Add note'}{else}{l s='Edit note'}{/if}">
									{if $document->note eq ''}
										<i class="icon-plus-sign-alt"></i>
										{l s='Add note'}
									{else}
										<i class="icon-pencil"></i>
										{l s='Edit note'}
									{/if}
								</a>

							{/if}
						{/if}
						{if get_class($document) eq 'OrderSlip'}
						<a href="{$current_index}&amp;viewOrder&amp;submitDeleteSlip&amp;order_slip_id={$document->id}&amp;id_order={$order->id}{if isset($smarty.get.token)}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}{/if}">{l s='delete'}</a>
						{else}
                                                <a href="{$current_index}&amp;viewOrder&amp;submitDeleteInvoice&amp;order_invoice_id={$document->id}&amp;id_order={$order->id}{if isset($smarty.get.token)}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}{/if}">{l s='delete'}</a>
                                                {/if}
						</td>
					</tr>
				{if get_class($document) eq 'OrderInvoice'}
					{if !isset($document->is_delivery)}
					<tr id="invoiceNote{$document->id}" style="display:none">
						<td colspan="5">
							<form action="{$current_index}&amp;viewOrder&amp;id_order={$order->id}{if isset($smarty.get.token)}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}{/if}" method="post">
								<p>
									<label for="editNote{$document->id}" class="t">{l s='Note'}</label>
									<input type="hidden" name="id_order_invoice" value="{$document->id}" />
									<textarea name="note" id="editNote{$document->id}" class="edit-note textarea-autosize">{$document->note|escape:'html':'UTF-8'}</textarea>
								</p>
								<p>
									<button type="submit" name="submitEditNote" class="btn btn-default">
										<i class="icon-save"></i>
										{l s='Save'}
									</button>
									<a class="btn btn-default" href="#" id="cancelNote" onclick="$('#invoiceNote{$document->id}').hide();return false;">
										<i class="icon-remove"></i>
										{l s='Cancel'}
									</a>
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
							{l s='There is no available document'}
						</div>
						{if isset($invoice_management_active) && $invoice_management_active}
							<a class="btn btn-default" href="{$current_index}&amp;viewOrder&amp;submitGenerateInvoice&amp;id_order={$order->id}{if isset($smarty.get.token)}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}{/if}">
								<i class="icon-repeat"></i>
								{l s='Generate invoice'}
							</a>
						{/if}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{if !isset($orderDocumentsOnlyTable)}
        <h2>{l s='Add new invoice'}</h2>
        <form method="post" id="addInoiceForm" action="{$current_index}&amp;viewOrder&amp;submitAddInvoice&amp;id_order={$order->id}{if isset($smarty.get.token)}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}{/if}">
            <input type="hidden" name="product_ids" value="" id="addInvoiceProductIds" />
            <button id="submitAddInvoice" class="btn btn-primary pull-right" name="submitAddInvoice" {if count($customer_thread_message)}onClick="return confirm(readMessagesConfirmation)"{/if} >{l s='Add'}</button>
            <div style="overflow:auto">
            <select name="template_type" id="addInvoiceTypeSelector" autocomplete="off" style="width:auto; float:left;">
                <option value="1">{l s='Invoice'}</option>
                <option value="2">{l s='Delivery'}</option>
            </select>
            <select name="invoice_template_id" id="addInvoiceInvoiceTemplateSel" style="width:auto; float:left;" autocomplete="off">
                {foreach from=$invoiceTemplates item='invoiceCategory'}
                    <optgroup label="{$invoiceCategory.categoryName}">
                    {foreach from=$invoiceCategory.templates item='template'}
                        <option value="{$template.id}">{$template.name}</option>
                    {/foreach}
                    </optgroup>
                {/foreach}
            </select>&nbsp;
            <select name="delivery_template_id" id="addInvoiceDeliveryTemplateSel" style="display:none;width:auto; float:left;"  autocomplete="off">
                {foreach from=OrderInvoice::getDeliveryTemplateNames() item='invoiceTplName' key='invoiceTplId'}
                <option value="{$invoiceTplId}">{$invoiceTplName}</option>
                {/foreach}
            </select>
            </div>
        </form>
            
       <h4>{l s='Invoice text. Will be put in [orderInvoiceTxt] variable in invoice template'}</h4>
        <form method="post" id="setInoiceTextForm" action="{$current_index}&amp;viewOrder&amp;submitSetInvoiceText&amp;id_order={$order->id}{if isset($smarty.get.token)}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}{/if}">
            <textarea name="invoice_txt">{$order->invoice_txt|escape}</textarea>
            <br/>
            <button id="submitSetInvoiceText" class="btn btn-primary pull-right" name="submitSetInvoiceText">{l s='Save'}</button>
            <div class="clear"></div>
        </form>
</div>

<script type="text/javascript">    
//<![CDATA[
    var textSave = '{l s='Save' js='1'}';
    var textCancel = '{l s='Cancel' js='1'}';
//]]>
</script>
{/if}