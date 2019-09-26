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
*  @version  Release: $Revision: 17822 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
	<script type="text/javascript">
	var admin_order_tab_link = "{$link->getAdminLink('AdminOrders')|escape:'quotes':'UTF-8'}";
	var id_order = {$order->id|escape:'html':'UTF-8'};
	var id_lang = {$current_id_lang|escape:'html':'UTF-8'};
	var id_currency = {$order->id_currency|escape:'html':'UTF-8'};
	var id_customer = {$order->id_customer|escape:'html':'UTF-8'};
	var id_shop = {$order->id_shop|escape:'html':'UTF-8'};
	{assign var=PS_TAX_ADDRESS_TYPE value=Configuration::get('PS_TAX_ADDRESS_TYPE')}
	var id_address = {$order->$PS_TAX_ADDRESS_TYPE|escape:'html':'UTF-8'};
	var currency_sign = "{$currency->sign|escape:'html':'UTF-8'}";
	var currency_format = "{$currency->format|escape:'html':'UTF-8'}";
	var currency_blank = "{$currency->blank|escape:'html':'UTF-8'}";
	var priceDisplayPrecision = 2;
	var use_taxes = {if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}true{else}false{/if};
	{if isset($stock_management)}
	var stock_management = {$stock_management|escape:'htmlall':'UTF-8'};
	{/if}
	var token = "{$smarty.get.token|escape:'htmlall':'UTF-8'}";
	var weightUnit = "{Configuration::get('PS_WEIGHT_UNIT')|escape:'html':'UTF-8'}";

	var txt_add_product_stock_issue = "{l s='You want to add more product than are available in stock, are you sure you want to add this quantity?' mod='orderedit' js=1}";
	var txt_add_product_new_invoice = "{l s='Are you sure you want to create a new invoice?' mod='orderedit' js=1}";
	var txt_add_product_no_product = "{l s='Error: No product has been selected' mod='orderedit' js=1}";
	var txt_add_product_no_product_quantity = "{l s='Error: Quantity of product must be set' mod='orderedit' js=1}";
	var txt_add_product_no_product_price = "{l s='Error: Price of product must be set' mod='orderedit' js=1}";
	var txt_confirm = "{l s='Are you sure?' js=1 mod='orderedit'}";
	var iem = {$iem|escape:'html':'UTF-8'};
	var iemp = "{$iemp|escape:'html':'UTF-8'}";
	var ajaxPath = "{$ajax_path|escape:'html':'UTF-8'}";
	var emailNotifyLabel = "{l s='Do you want to notify a customer that his/her order has been changed?' mod='orderedit' js=1}";
        var duplicateProductWarning = "{l s='Selected product is already in order. Please edit existing instance.' mod='orderedit' js=1}";
	var labelYes = "{l s='Yes' mod='orderedit' js=1}";
	var labelNo = "{l s='No' mod='orderedit' js=1}";
        var labelOk = "{l s='Ok' mod='orderedit' js=1}";
	var ordereditTranslate = {
		"This order already has an invoice": "{l s='This order already has an invoice' mod='orderedit' js=1}"
	};

	var statesShipped = new Array();
	{foreach from=$states item=state}
		{if (!$currentState->shipped && $state['shipped'])}
			statesShipped.push({$state['id_order_state']|escape:'html':'UTF-8'});
		{/if}
	{/foreach}
	</script>

	{assign var="hook_invoice" value={hook h="displayInvoice" id_order=$order->id}}
	{if ($hook_invoice)}
	<div style="float: right; margin: -40px 40px 10px 0;">{$hook_invoice|escape:'html':'UTF-8'}</div><br class="clear" />
	{/if}
	{literal}
	<style type="text/css">
		.fader {background: #eee;}
	</style>
	{/literal}
	<div id="global_message_wrapper">
		{include file="$orderedit_tpl_dir/helpers/message_placeholders/_global_message.tpl"}
	</div>
	
	<div class="panel kpi-container">
		<div class="row">
			<div class="col-xs-6 col-sm-3 box-stats color3" >
				<div class="kpi-content">
					<i class="icon-calendar-empty"></i>
					<span class="title">{l s='Date' mod='orderedit'}</span>
					{*<span class="value">{dateFormat date=$order->date_add full=true}</span>*}
					<div id="orderDateAdd" class="editable">
						{if $can_edit}
							<p class="customVal" style="display:none;">
								<span></span>
							</p>
						{/if}
						<p class="displayVal">
							<span class="order_dateadd_show ">{$order->date_add|escape:'html':'UTF-8'}</span>
						</p>
						{if $can_edit}
							<p class="realVal" style="display:none;">
                                <span class="order_dateadd_edit">
                                    <input type="text" class="datetime_pick" id="order_dateadd" rel="orderDateadd" value="{$order->date_add|escape:'html':'UTF-8'}" />
                                </span>
							</p>
						{/if}
					</div>
				</div>
			</div>
			<div class="col-xs-6 col-sm-3 box-stats color4" >
				<div class="kpi-content">
					<i class="icon-money"></i>
					<span class="title">{l s='Total' mod='orderedit'}</span>
					<span class="value">{Tools::displayPrice($order->total_paid_tax_incl, (int)$currency->id)|escape:'htmlall':'UTF-8'}</span>
				</div>
			</div>
			<div class="col-xs-6 col-sm-3 box-stats color2" >
				<div class="kpi-content">
					<i class="icon-comments"></i>
					<span class="title">{l s='Messages' mod='orderedit'}</span>
					<span class="value"><a href="{$link->getAdminLink('AdminCustomerThreads')|escape:'html':'UTF-8'}">{sizeof($customer_thread_message)|escape:'html':'UTF-8'}</a></span>
				</div>
			</div>
			<div class="col-xs-6 col-sm-3 box-stats color1" >
				<div class="kpi-content">
					<i class="icon-book"></i>
					<span class="title">{l s='Products' mod='orderedit'}</span>
					<span class="value">{sizeof($products)|escape:'html':'UTF-8'}</span>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-lg-7">
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-credit-card"></i>
					{l s='Order' mod='orderedit'}
					<span class="badge">{$order->reference|escape:'html':'UTF-8'}</span>
					<span class="badge">{l s='#' mod='orderedit'}{$order->id|escape:'html':'UTF-8'}</span>
					<div class="panel-heading-action">
						<div class="btn-group">
							<a class="btn btn-default" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&vieworder&id_order={$previousOrder|escape:'html':'UTF-8'}" {if !$previousOrder}disabled{/if}>
								<i class="icon-backward"></i>
							</a>
							<a class="btn btn-default" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&vieworder&id_order={$nextOrder|escape:'html':'UTF-8'}" {if !$nextOrder}disabled{/if}>
								<i class="icon-forward"></i>
							</a>
						</div>
					</div>
				</div>
				<ul class="nav nav-tabs" id="tabOrder">
					<li class="active">
						<a href="#status">
							<i class="icon-time"></i>
							{l s='Status' mod='orderedit'} <span class="badge">{$history|@count|escape:'html':'UTF-8'}</span>
						</a>
					</li>
					<li>
						<a href="#documents">
							<i class="icon-file-text"></i>
							{l s='Documents' mod='orderedit'} <span class="badge">{$order->getDocuments()|@count|escape:'html':'UTF-8'}</span>
						</a>
					</li>
				</ul>
				<div class="tab-content panel">
					<!-- Tab status -->
					<div class="tab-pane active" id="status">
						<h4 class="visible-print">{l s='Status' mod='orderedit'} <span class="badge">({$history|@count|escape:'html':'UTF-8'})</span></h4>
						<!-- History of status -->
						<div class="table-responsive">
							<table class="table history-status row-margin-bottom">
								<tbody>
									{foreach from=$history item=row key=key}
										{if ($key == 0)}
											<tr>
												<td style="background-color:{$row['color']|escape:'html':'UTF-8'}"><img src="../img/os/{$row['id_order_state']|intval}.gif" /></td>
												<td style="background-color:{$row['color']|escape:'html':'UTF-8'};color:{$row['text-color']|escape:'html':'UTF-8'}">{$row['ostate_name']|stripslashes}</td>
												<td style="background-color:{$row['color']|escape:'html':'UTF-8'};color:{$row['text-color']|escape:'html':'UTF-8'}">{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes} {$row['employee_lastname']|stripslashes}{/if}</td>
												<td style="background-color:{$row['color']|escape:'html':'UTF-8'};color:{$row['text-color']|escape:'html':'UTF-8'}">{dateFormat date=$row['date_add'] full=true}</td>
											</tr>
										{else}
											<tr>
												<td><img src="../img/os/{$row['id_order_state']|intval}.gif" /></td>
												<td>{$row['ostate_name']|stripslashes|escape:'html':'UTF-8'}</td>
												<td>{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes|escape:'html':'UTF-8'} {$row['employee_lastname']|stripslashes|escape:'html':'UTF-8'}{else}&nbsp;{/if}</td>
												<td>{dateFormat date=$row['date_add'] full=true}</td>
											</tr>
										{/if}
									{/foreach}
								</tbody>
							</table>
						</div>
						<div id="status_wrapper">
							{include file="$orderedit_tpl_dir/helpers/_status.tpl"}
						</div>
					</div>
					<!-- Tab documents -->
					<div class="tab-pane" id="documents">
						<h4 class="visible-print">{l s='Documents' mod='orderedit'} <span class="badge">({$order->getDocuments()|@count|escape:'html':'UTF-8'})</span></h4>
						<div id="documents_wrapper">
							{include file="$orderedit_tpl_dir/helpers/_documents.tpl"}
						</div>
					</div>
				</div>
				<script>
					$('#tabOrder a').click(function (e) {
						e.preventDefault()
						$(this).tab('show')
					})
				</script>
				<hr>
				<!-- Tab nav -->
				<ul class="nav nav-tabs" id="myTab">
					<li class="active">
						<a href="#shipping">
							<i class="icon-truck "></i>
							{l s='Shipping' mod='orderedit'} <span class="badge">{$order->getShipping()|@count|escape:'html':'UTF-8'}</span>
						</a>
					</li>
					<li>
						<a href="#returns">
							<i class="icon-undo"></i>
							{l s='Merchandise Returns' mod='orderedit'} <span class="badge">{$order->getReturn()|@count|escape:'html':'UTF-8'}</span>
						</a>
					</li>
				</ul>
				<!-- Tab content -->
				<div class="tab-content panel">
					<!-- Tab shipping -->
					<div class="tab-pane active" id="shipping">
						<h4 class="visible-print">{l s='Shipping' mod='orderedit'} <span class="badge">({$order->getShipping()|@count|escape:'html':'UTF-8'})</span></h4>
						<!-- Shipping block -->
						{if !$order->isVirtual()}
						<div class="form-horizontal">
							{if $order->gift_message}
							<div class="form-group">
								<label class="control-label col-lg-3">{l s='Message' mod='orderedit'}</label>
								<div class="col-lg-9">
									<p class="form-control-static">{$order->gift_message|nl2br|escape:'html':'UTF-8'}</p>
								</div>
							</div>
							{/if}
							{include file="$orderedit_tpl_dir/helpers/_shipping.tpl"}
							{if $carrierModuleCall}
								{$carrierModuleCall|escape:'html':'UTF-8'}
							{/if}
						</div>
						{/if}
					</div>
					<!-- Tab returns -->
					<div class="tab-pane" id="returns">
						<h4 class="visible-print">{l s='Merchandise Returns' mod='orderedit'} <span class="badge">({$order->getReturn()|@count|escape:'html':'UTF-8'})</span></h4>
						{if !$order->isVirtual()}
						<!-- Return block -->
							{if $order->getReturn()|count > 0}
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th><span class="title_box ">Date</span></th>
											<th><span class="title_box ">Type</span></th>
											<th><span class="title_box ">Carrier</span></th>
											<th><span class="title_box ">Tracking number</span></th>
										</tr>
									</thead>
									<tbody>
										{foreach from=$order->getReturn() item=line}
										<tr>
											<td>{$line.date_add|escape:'html':'UTF-8'}</td>
											<td>{$line.type|escape:'html':'UTF-8'}</td>
											<td>{$line.state_name|escape:'html':'UTF-8'}</td>
											<td class="actions">
												<span id="shipping_number_show">{if isset($line.url) && isset($line.tracking_number)}<a href="{$line.url|replace:'@':$line.tracking_number|escape:'html':'UTF-8'}">{$line.tracking_number|escape:'html':'UTF-8'}</a>{elseif isset($line.tracking_number)}{$line.tracking_number|escape:'html':'UTF-8'}{/if}</span>
												{if $line.can_edit}
												<form method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&vieworder&id_order={$order->id|escape:'html':'UTF-8'}&id_order_invoice={if $line.id_order_invoice}{$line.id_order_invoice|escape:'html':'UTF-8'}{else}0{/if}&id_carrier={if $line.id_carrier}{$line.id_carrier|escape:'html':'UTF-8'}{else}0{/if}">
													<span class="shipping_number_edit" style="display:none;">
														<button type="button" name="tracking_number">
															{$line.tracking_number|htmlentities|escape:'html':'UTF-8'}
														</button>
														<button type="submit" class="btn btn-default" name="submitShippingNumber">
															{l s='Update' mod='orderedit'}
														</button>
													</span>
													<button href="#" class="edit_shipping_number_link">
														<i class="icon-pencil"></i>
														{l s='Edit' mod='orderedit'}
													</button>
													<button href="#" class="cancel_shipping_number_link" style="display: none;">
														<i class="icon-remove"></i>
														{l s='Cancel' mod='orderedit'}
													</button>
												</form>
												{/if}
											</td>
										</tr>
										{/foreach}
									</tbody>
								</table>
							</div>
							{else}
							<div class="list-empty hidden-print">
								<div class="list-empty-msg">
									<i class="icon-warning-sign list-empty-icon"></i>
									{l s='No merchandise returned yet' mod='orderedit'}
								</div>
							</div>
							{/if}
							{if $carrierModuleCall}
								{$carrierModuleCall|escape:'html':'UTF-8'}
							{/if}
						{/if}
					</div>
				</div>
				<script>
					$('#myTab a').click(function (e) {
						e.preventDefault()
						$(this).tab('show')
					})
				</script>
			</div>
			<div id="payment_wrapper">
				{include file="$orderedit_tpl_dir/helpers/_payment.tpl"}
			</div>
			{assign var="or_cur" value=Currency::getCurrency($order->id_currency)}
			{assign var="or_lang" value=Language::getLanguage($order->id_lang)}

			<div>
				<div class="panel">
        			<div class="panel-heading">
                		<i class="icon-money"></i>
                		{l s='Order settings' mod='orderedit'}
                	</div>

                	<div class="form-horizontal">
		                <div class="form-group">
							<label class="control-label col-lg-2" for="id_tax_rules_group">
								{l s='Order currency' mod='orderedit'}
							</label>
							<div class="col-lg-8">
							<div class="row">
								<div class="editable">
									{if $can_edit}
									<p class="customVal" style="display:none;">
										<span></span>
									</p>
									{/if}
									<p class="displayVal">
										<span class="order_currency_show">{$or_cur.iso_code|escape:'html':'UTF-8'} {if array_key_exists('sign', $or_cur)}{$or_cur.sign|escape:'html':'UTF-8'}{/if}</span>
									</p>
									{if $can_edit}
									<p class="realVal" style="display:none;">
										<span class="order_currency_edit">
											<select name="order_currency" class="edit_order_currency payment_currency form-control fixed-width-xs pull-left" rel="productCurrencyEdit">
                                            {foreach from=$currencies item=current_currency}
                                                <option value="{$current_currency['id_currency']|escape:'html':'UTF-8'}"{if $current_currency['id_currency'] == $currency->id} selected="selected"{/if}>{$current_currency['sign']|escape:'html':'UTF-8'}</option>
                                            {/foreach}
                                    		</select>
										</span>
									</p>
									{/if}
								</div>
							</div>
							</div>
						</div>
					</div>

					<div class="form-horizontal">
		                <div class="form-group">
							<label class="control-label col-lg-2" for="id_tax_rules_group">
								{l s='Order language' mod='orderedit'}
							</label>
							<div class="col-lg-8">
							<div class="row">
								<div class="editable">
									{if $can_edit}
									<p class="customVal" style="display:none;">
										<span></span>
									</p>
									{/if}
									<p class="displayVal">
										<span class="order_language_show">{$or_lang.name|escape:'html':'UTF-8'}</span>
									</p>
									{if $can_edit}
									<p class="realVal" style="display:none;">
										<span class="order_language_edit">
											<select name="order_language" class="edit_order_language form-control fixed-width-lg pull-left" rel="productLanguageEdit">
                                            {foreach from=Language::getLanguages() item=current_language}
                                                <option value="{$current_language['id_lang']|escape:'html':'UTF-8'}"{if $current_language['id_lang'] == $order->id_lang} selected="selected"{/if}>{$current_language['name']|escape:'html':'UTF-8'}</option>
                                            {/foreach}
                                    		</select>
										</span>
									</p>
									{/if}
								</div>
							</div>
							</div>
						</div>
					</div>

        		</div>
        	</div>

		</div>
		<div class="col-lg-5">
			<!-- Customer informations -->
			<div class="panel">
				{if $customer->id}
					<div class="panel-heading">
						<i class="icon-user"></i>
						{l s='Customer' mod='orderedit'}
						<span class="badge">
							<a href="?tab=AdminCustomers&amp;id_customer={$customer->id|escape:'html':'UTF-8'}&amp;viewcustomer&amp;token={getAdminToken tab='AdminCustomers'}">
								{if Configuration::get('PS_B2B_ENABLE')}{$customer->company|escape:'html':'UTF-8'} - {/if}
								{$gender->name|escape:'html':'UTF-8'}
								{$customer->firstname|escape:'html':'UTF-8'}
								{$customer->lastname|escape:'html':'UTF-8'}
							</a>
						</span>
						<span class="badge">
							{l s='#' mod='orderedit'}{$customer->id|escape:'html':'UTF-8'}
						</span>
					</div>
					<div class="row">
						<div class="col-xs-6">
							{if ($customer->isGuest())}
								{l s='This order has been placed by a guest.' mod='orderedit'}
								{if (!Customer::customerExists($customer->email))}
									<form method="post" action="index.php?tab=AdminCustomers&amp;id_customer={$customer->id|escape:'html':'UTF-8'}&amp;token={getAdminToken tab='AdminCustomers'}">
										<input type="hidden" name="id_lang" value="{$order->id_lang|escape:'html':'UTF-8'}" />
										<input class="btn btn-default" type="submit" name="submitGuestToCustomer" value="{l s='Transform a guest into a customer' mod='orderedit'}" />
										<p class="help-block">{l s='This feature will generate a random password and send an email to the customer.' mod='orderedit'}</p>
									</form>
								{else}
									<div class="alert alert-warning">
										{l s='A registered customer account has already claimed this email address' mod='orderedit'}
									</div>
								{/if}
							{else}
								<dl class="well list-detail">
									<dt>{l s='Email' mod='orderedit'}</dt>
										<dd><a href="mailto:{$customer->email|escape:'html':'UTF-8'}"><i class="icon-envelope-o"></i> {$customer->email|escape:'html':'UTF-8'}</a></dd>
									<dt>{l s='Account registered' mod='orderedit'}</dt>
										<dd class="text-muted"><i class="icon-calendar-o"></i> {dateFormat date=$customer->date_add full=true}</dd>
									<dt>{l s='Valid orders placed' mod='orderedit'}</dt>
										<dd><span class="badge">{$customerStats['nb_orders']|escape:'html':'UTF-8'}</span></dd>
									<dt>{l s='Total spent since registration' mod='orderedit'}</dt>
										<dd><span class="badge badge-success">{Tools::displayPrice(Tools::ps_round(Tools::convertPrice($customerStats['total_orders'], $currency)), (int)$currency->id)|escape:'htmlall':'UTF-8'}</span></dd>
									{if Configuration::get('PS_B2B_ENABLE')}
										<dt>{l s='Siret' mod='orderedit'}</dt>
											<dd>{$customer->siret|escape:'html':'UTF-8'}</dd>
										<dt>{l s='APE' mod='orderedit'}</dt>
											<dd>{$customer->ape|escape:'html':'UTF-8'}</dd>
									{/if}
								</dl>
							{/if}
						</div>

						<div class="col-xs-6">
							<div class="form-group hidden-print">
								<a href="?tab=AdminCustomers&amp;id_customer={$customer->id|escape:'html':'UTF-8'}&amp;viewcustomer&amp;token={getAdminToken tab='AdminCustomers'}" class="btn btn-default btn-block">{l s='View full details...' mod='orderedit'}</a>
							</div>
							<div class="panel panel-sm">
								<div class="panel-heading">
									<i class="icon-eye-slash"></i>
									{l s='Private note' mod='orderedit'}
								</div>
								<form id="customer_note" class="form-horizontal" action="ajax.php" method="post" onsubmit="saveCustomerNote({$customer->id|escape:'html':'UTF-8'});return false;" >
									<div class="form-group">
										<div class="col-lg-12">
											<textarea name="note" id="noteContent" class="textarea-autosize" onkeyup="$(this).val().length > 0 ? $('#submitCustomerNote').removeAttr('disabled') : $('#submitCustomerNote').attr('disabled', 'disabled')">{$customer->note|escape:'html':'UTF-8'}</textarea>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-12">
											<button type="submit" id="submitCustomerNote" class="btn btn-default pull-right" disabled="disabled" />
												<i class="icon-save"></i>
												{l s='Save' mod='orderedit'}
											</button>
										</div>
									</div>
									<span id="note_feedback"></span>
								</form>
							</div>
						</div>
					</div>
				{/if}
				<!-- Tab nav -->
				<div id="address_wrapper">
				
					<!-- Tab content -->
					
					{include file="$orderedit_tpl_dir/helpers/_address.tpl"}

				</div>
			</div>
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-envelope"></i> {l s='Messages' mod='orderedit'} <span class="badge">{sizeof($customer_thread_message)|escape:'html':'UTF-8'}</span>
				</div>
				{if (sizeof($messages))}
					<div class="panel panel-highlighted">
						<div class="message-item">
							{foreach from=$messages item=message}
								<div class="message-avatar">
									<div class="avatar-md">
										<i class="icon-user icon-2x"></i>
									</div>
								</div>
								<div class="message-body">
									
									<span class="message-date">&nbsp;<i class="icon-calendar"></i>
										{dateFormat date=$message['date_add']} - 
									</span>
									<h4 class="message-item-heading">
										{if ($message['elastname']|escape:'html':'UTF-8')}{$message['efirstname']|escape:'html':'UTF-8'}
											{$message['elastname']|escape:'html':'UTF-8'}{else}{$message['cfirstname']|escape:'html':'UTF-8'} {$message['clastname']|escape:'html':'UTF-8'}
										{/if}
										{if ($message['private'] == 1)}
											<span class="badge badge-info">{l s='Private' mod='orderedit'}</span>
										{/if}
									</h4>
									<p class="message-item-text">
										{$message['message']|escape:'html':'UTF-8'|nl2br}
									</p>
								</div>
								{*if ($message['is_new_for_me'])}
									<a class="new_message" title="{l s='Mark this message as \'viewed\'' mod='orderedit'}" href="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}&amp;messageReaded={$message['id_message']|escape:'html':'UTF-8'}">
										<i class="icon-ok"></i>
									</a>
								{/if*}
							{/foreach}
						</div>
					</div>
				{/if}
				<div id="messages" class="well hidden-print">
					<form action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}" method="post" onsubmit="if (getE('visibility').checked == true) return confirm('{l s='Do you want to send this message to the customer?' mod='orderedit'}');">
						<div id="message" class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-lg-3">{l s='Choose a standard message' mod='orderedit'}</label>
								<div class="col-lg-9">
									<select class="chosen form-control" name="order_message" id="order_message" onchange="orderOverwriteMessage(this, '{l s='Do you want to overwrite your existing message?' mod='orderedit'}')">
										<option value="0" selected="selected">-</option>
										{foreach from=$orderMessages item=orderMessage}
										<option value="{$orderMessage['message']|escape:'html':'UTF-8'}">{$orderMessage['name']|escape:'html':'UTF-8'}</option>
										{/foreach}
									</select>
									<p class="help-block">
										<a href="{$link->getAdminLink('AdminOrderMessage')|escape:'html':'UTF-8'}">
											{l s='Configure predefined messages' mod='orderedit'}
											<i class="icon-external-link"></i>
										</a>
									</p>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-lg-3">{l s='Display to customer?' mod='orderedit'}</label>
								<div class="col-lg-9">
									<span class="switch prestashop-switch fixed-width-lg">
										<input type="radio" name="visibility" id="visibility_on" value="0" />
										<label for="visibility_on">
											{l s='Yes' mod='orderedit'}
										</label>
										<input type="radio" name="visibility" id="visibility_off" value="1" checked="checked" /> 
										<label for="visibility_off">
											{l s='No' mod='orderedit'}
										</label>
										<a class="slide-button btn"></a>
									</span>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-lg-3">{l s='Message' mod='orderedit'}</label>
								<div class="col-lg-9">
									<textarea id="txt_msg" class="textarea-autosize" name="message">{Tools::getValue('message')|escape:'html':'UTF-8'}</textarea>
									<p id="nbchars"></p>
								</div>
							</div>


							<input type="hidden" name="id_order" value="{$order->id|escape:'html':'UTF-8'}" />
							<input type="hidden" name="id_customer" value="{$order->id_customer|escape:'html':'UTF-8'}" />
							<button type="submit" id="submitMessage" class="btn btn-primary pull-right" name="submitMessage">
								{l s='Send message' mod='orderedit'}
							</button>
							<a class="btn btn-default" href="{$link->getAdminLink('AdminCustomerThreads')|escape:'html':'UTF-8'}">
								{l s='Show all messages' mod='orderedit'}
								<i class="icon-external-link"></i>
							</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	{if isset($ORLIQUE_HOOK_TOP)}
	{$ORLIQUE_HOOK_TOP|escape:'html':'UTF-8'}
	{/if}

	{if isset($ORLIQUE_HOOK_BEFORE_PRODUCT_LIST)}
	{$ORLIQUE_HOOK_BEFORE_PRODUCT_LIST|escape:'html':'UTF-8'}
	{/if}
	<input type="hidden" id="notify_email_send" value="1" />
	<form class="container-command-top-spacing" action="{$current_index|escape:'html':'UTF-8'}&vieworder&token={$smarty.get.token|escape:'html':'UTF-8'}&id_order={$order->id|escape:'html':'UTF-8'}" method="post" onsubmit="return orderDeleteProduct('{l s='Cannot return this product' mod='orderedit'}', '{l s='Quantity to cancel is greater than quantity available' mod='orderedit'}');">
		<input type="hidden" name="id_order" value="{$order->id|escape:'html':'UTF-8'}" />
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-shopping-cart"></i>
				{l s='Products' mod='orderedit'}
			</div>
			<div style="display: none">
				<input type="hidden" value="{$order->getWarehouseList()|implode|escape:'html':'UTF-8'}" id="warehouse_list" />
			</div>
			<div id="product_list_errors_wrapper">
				{include file="$orderedit_tpl_dir/helpers/message_placeholders/_product_list_errors.tpl"}
			</div>
			{include file="$orderedit_tpl_dir/helpers/_product_list.tpl"}

			<div style="clear:both; height:15px;">&nbsp;</div>
			<div style="float: right; width: 160px; display: none;" class="standard_refund_fields">
				{if ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN'))}
					<input type="checkbox" name="reinjectQuantities" class="button" />&nbsp;<label for="reinjectQuantities" style="float:none; font-weight:normal;">{l s='Re-stock products' mod='orderedit'}</label><br />
				{/if}
				{if ((!$order->hasBeenDelivered() && $order->hasBeenPaid()) || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
					<input type="checkbox" id="generateCreditSlip" name="generateCreditSlip" class="button" onclick="toggleShippingCost(this)" />&nbsp;<label for="generateCreditSlip" style="float:none; font-weight:normal;">{l s='Generate a credit slip' mod='orderedit'}</label><br />
					<input type="checkbox" id="generateDiscount" name="generateDiscount" class="button" onclick="toggleShippingCost(this)" />&nbsp;<label for="generateDiscount" style="float:none; font-weight:normal;">{l s='Generate a voucher' mod='orderedit'}</label><br />
					<span id="spanShippingBack" style="display:none;"><input type="checkbox" id="shippingBack" name="shippingBack" class="button" />&nbsp;<label for="shippingBack" style="float:none; font-weight:normal;">{l s='Repay shipping costs' mod='orderedit'}</label><br /></span>
				{/if}
				{if (!$order->hasBeenDelivered() || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
					<div style="text-align:center; margin-top:5px;">
						<input type="submit" name="cancelProduct" value="{if $order->hasBeenDelivered()}{l s='Return products' mod='orderedit'}{elseif $order->hasBeenPaid()}{l s='Refund products' mod='orderedit'}{else}{l s='Cancel products' mod='orderedit'}{/if}" class="button" style="margin-top:8px;" />
					</div>
				{/if}
			</div>
			<div style="float: right; width: 160px; display:none;" class="partial_refund_fields">
				<input type="checkbox" name="reinjectQuantities" class="button" />&nbsp;<label for="reinjectQuantities" style="float:none; font-weight:normal;">{l s='Re-stock products' mod='orderedit'}</label><br />
				<input type="checkbox" id="generateDiscountRefund" name="generateDiscountRefund" class="button" onclick="toggleShippingCost(this)" />&nbsp;<label for="generateDiscount" style="float:none; font-weight:normal;">{l s='Generate a voucher' mod='orderedit'}</label><br />
				<input type="submit" name="partialRefund" value="{l s='Partial refund' mod='orderedit'}" class="button" style="margin-top:8px;" />
			</div>
			
			<div class="panel-footer">
				<button type="button" class="btn btn-default pull-right" name="ordereditOrderSave">
					<i class="process-icon-save"></i> {l s='Save' mod='orderedit'}
				</button>
			</div>
		</div>
	</form>
{/block}
