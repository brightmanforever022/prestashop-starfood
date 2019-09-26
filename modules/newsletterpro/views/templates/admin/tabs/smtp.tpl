{*
* 2013-2015 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright 2013-2015 Ovidiu Cimpean
* @version   Release: 4
*}

{if isset($fix_document_write) && $fix_document_write == 1}
<div id="{$tab_id|escape:'html':'UTF-8'}" class="tab-smtp" style="display: none;">
{else}
<script type="text/javascript"> 
	if(window.location.hash == '#smtp') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" class="tab-smtp" style="display: block;">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" class="tab-smtp" style="display: none;">');
	{rdelim} 
</script>
{/if}
	<h4>{l s='SMTP Configuration' mod='newsletterpro'}</h4>
	<div class="separation"></div>

	<div class="clearfix">

		<div class="form-group clearfix">
			<div class="col-sm-9 col-sm-offset-3">
				<div class="checkbox">
					<label for="smtp-active" class="in-win control-label">
						<input class="smtp-checkbox" type="checkbox" id="smtp-active" {if $CONFIGURATION.SMTP_ACTIVE == 1}checked="checked"{/if}>
						{l s='Activate Connection' mod='newsletterpro'}
					</label>
				</div>
			</div>
		</div>

		<div class="form-group clearfix">
			<div class="col-sm-9 col-sm-offset-3">
				<div class="clearfix">
					<select id="select-smtp" class="fixed-width-xxl pull-left select-smtp" style="display: none;" {if $CONFIGURATION.SMTP_ACTIVE == 0}disabled="disabled"{/if}></select>
				</div>
				<p class="help-block" style="margin-top: 10px;">{l s='Don\'t activate this option if you want to send newsletter with the default shop email configuration. By activating this option you will have to configure a new SMTP email.' mod='newsletterpro'}</p>										
				<div class="clearfix">
					<span id="change-smtp-message"></span>
				</div>
			</div>
		</div>

		<div id="smpt-config-box" class="clearfix smpt-config-box" style="display: none;">

			<form id="smtpForm" method="POST">
				<input type="hidden" id="smpt-id" name="id_newsletter_pro_smtp" value="0">
				
				<div class="form-group clearfix" style="margin-bottom: 0;">
					<label class="required control-label col-sm-3"><span class="label-tooltip">{l s='Name' mod='newsletterpro'}</span></label>
					<div class="col-sm-9">
						<div class="clearfix">
							<input class="form-control fixed-width-xxl" type="text" size="30" id="smtp-name" name="name"> 
						</div>
						<p class="help-block">{l s='Alias name.' mod='newsletterpro'}</p>										
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-sm-3" style="padding-top: 14px;">{l s='Method' mod='newsletterpro'}</label>
					<div class="col-sm-9">
						<div class="clearfix">
							<div class="radio">
								<label for="method-smtp" class="in-win control-label">
									<input id="method-smtp" type="radio" name="method" value="2" checked>
									{l s='SMTP' mod='newsletterpro'}
								</label>
							</div>
							<div class="radio">
								<label for="method-mail" class="in-win control-label">
									<input id="method-mail" type="radio" name="method" value="1">
									{l s='PHP mail() function' mod='newsletterpro'}
								</label>
							</div>
						</div>
						<p class="help-block">{l s='Send method.' mod='newsletterpro'}</p>										
					</div>
				</div>

				{include file="$tpl_location"|cat:"templates/admin/settings_option.tpl" 
					title_name={l s='List-Unsubscribe' mod='newsletterpro'}
					description={l s='Add the list unsubscribe header.' mod='newsletterpro'}
					label_id='list_unsubscribe_active' 
					label_name='list_unsubscribe_active' 
					input_onchange=''
					is_checked=0
				}

				<div id="smtp-list-unsubscribe-email-box" class="form-group clearfix" style="display: none;">
					<label class="control-label col-sm-3"><span class="label-tooltip">{l s='List-Unsubscribe Email' mod='newsletterpro'}</span></label>
					<div class="col-sm-9">
						<div class="clearfix">
							<input class="form-control fixed-width-xxl" type="text" size="30" id="smtp-list-unsubscribe-email" name="list_unsubscribe_email"> 
						</div>
						<p class="help-block">{l s='Email address.' mod='newsletterpro'}</p>
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-sm-3"><span class="label-tooltip">{l s='From name' mod='newsletterpro'}</span></label>
					<div class="col-sm-9">
						<div class="clearfix">
							<input class="form-control fixed-width-xxl" type="text" size="30" id="smtp-from-name" name="from_name"> 
						</div>
						<p class="help-block">{l s='Email from name. If it is empty the email from name will take the value of the current shop name.' mod='newsletterpro'}</p>
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="required control-label col-sm-3"><span class="label-tooltip">{l s='From email' mod='newsletterpro'}</span></label>
					<div class="col-sm-9">
						<div class="clearfix">
							<input class="form-control fixed-width-xxl" type="text" size="30" id="smtp-from-email" name="from_email"> 
						</div>
						<p class="help-block">{l s='From email address.' mod='newsletterpro'}</p>										
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Reply to email' mod='newsletterpro'}</span></label>
					<div class="col-sm-9">
						<div class="clearfix">
							<input class="form-control fixed-width-xxl" type="text" size="30" id="smtp-reply-to" name="reply_to"> 
						</div>
						<p class="help-block">{l s='The customer can reply to your newsletter at the this email address.' mod='newsletterpro'}</p>										
					</div>
				</div>

				<div id="smtp-only">

					<div class="form-group clearfix">
						<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Mail domain name' mod='newsletterpro'}</span></label>
						<div class="col-sm-9">
							<div class="clearfix">
								<input class="form-control fixed-width-xxl" type="text" size="30" id="smtp-domain" name="domain"> 
							</div>
							<p class="help-block">{l s='Fully qualified domain name (keep this field empty if you don\'t know).' mod='newsletterpro'}</p>										
						</div>
					</div>

					<div class="form-group clearfix">
						<label class="required control-label col-sm-3"><span class="label-tooltip">{l s='SMTP server' mod='newsletterpro'}</span></label>
						<div class="col-sm-9">
							<div class="clearfix">
								<input class="form-control fixed-width-xxl" type="text" size="30" id="smtp-server" name="server"> 
							</div>
							<p class="help-block">{l s='IP address or server name (e.g. smtp.mydomain.com)' mod='newsletterpro'}</p>										
						</div>
					</div>

					<div class="form-group clearfix">
						<label class="required control-label col-sm-3"><span class="label-tooltip">{l s='SMTP user' mod='newsletterpro'}</span></label>
						<div class="col-sm-9">
							<div class="clearfix">
								<input class="form-control fixed-width-xxl" type="text" size="30" id="smtp-user" name="user"> 
							</div>
							<p class="help-block">{l s='Put your email here (office@mywebsite.com).' mod='newsletterpro'}</p>										
						</div>
					</div>

					<div class="form-group clearfix">
						<label class="control-label col-sm-3"><span class="label-tooltip">{l s='SMTP password' mod='newsletterpro'}</span></label>
						<div class="col-sm-9">
							<div class="clearfix">
								<input class="form-control fixed-width-xxl" type="password" size="30" id="smtp-passwd" name="passwd" value="" autocomplete="off"> 
							</div>
							<p class="help-block">{l s='Leave blank if not applicable.' mod='newsletterpro'}</p>										
						</div>
					</div>

					<div class="form-group clearfix">
						<label class="required control-label col-sm-3"><span class="label-tooltip">{l s='Encryption' mod='newsletterpro'}</span></label>
						<div class="col-sm-9">
							<div class="clearfix">
								<select id="smtp-encryption" class="fixed-width-xxl gk-select" style="width: 45%" name="encryption" autocomplete="off"> 
									<option value="off">None</option> 
									<option value="tls">TLS</option> 
									<option value="ssl">SSL</option>
								</select>
							</div>
							<p class="help-block">{l s='Use an encrypt protocol' mod='newsletterpro'}</p>										
						</div>
					</div>

					<div class="form-group clearfix">
						<label class="required control-label col-sm-3"><span class="label-tooltip">{l s='Port' mod='newsletterpro'}</span></label>
						<div class="col-sm-9">
							<div class="clearfix">
								<input class="form-control fixed-width-xxl" type="text" size="5" id="smtp-port" name="port">
							</div>
							<p class="help-block">{l s='Port number to use' mod='newsletterpro'}</p>										
						</div>
					</div>

				</div>
	
				<div class="form-group clearfix">
					<div class="col-sm-9 col-sm-offset-3">
						<div class="form-group clearfix">
							<a id="save-smtp" href="javascript:{}" class="btn btn-default btn-margin pull-left">
								<i class="icon icon-save"></i> {l s='Save' mod='newsletterpro'}
							</a>
							<a id="add-smtp" href="javascript:{}" class="btn btn-default btn-margin pull-left">
								<i class="icon icon-plus-square"></i> {l s='Add' mod='newsletterpro'}
							</a>
							<a id="delete-smtp" href="javascript:{}" class="btn btn-default btn-margin pull-left">
								<i class="icon icon-remove"></i> {l s='Delete' mod='newsletterpro'}
							</a>
							<span id="save-smtp-success" style="margin-left: 0;"></span>
						</div>
						<div class="clearfix">
							<div class="alert alert-danger" id="save-smtp-message" style="display: none;"></div>
						</div>
					</div>
				</div>

			</form>
		</div>

		<h4>{l s='Test Connection' mod='newsletterpro'}</h4>
		<div class="separation"></div>

		<div class="form-group clearfix">
			<label class="control-label col-sm-3"><span class="label-tooltip">{l s='Send a test email to' mod='newsletterpro'}</span></label>
			<div class="col-sm-9">
				<div class="form-group form-inline">
					<div class="clearfix">
						<div class="form-group">
							<input class="form-control fixed-width-xxl" type="text" id="smtp-test-email" size="40" value="{$shop_email|escape:'html':'UTF-8'}">
						</div>
						<div class="form-group">
							<a id="smtp-test-email-button" class="btn btn-default pull-left" href="javascript:{}">
								<span class="btn-ajax-loader"></span>
								<i class="icon icon-envelope"></i>
								{l s='Send an email test' mod='newsletterpro'}
							</a>
						</div>
					</div>
					<div class="clearix">
						<p class="help-block">
							{l s='Send a test with the current connection. When you send the newsletter don\'t forget the verify the used connection by clicking on the button \"Performances & Limits\".' mod='newsletterpro'}
						</p>
					</div>
					<span id="smtp-test-email-success" style="display: none;"></span>
				</div>
				<div class="clearfix">
					<span id="smtp-test-email-message"></span>
				</div>
			</div>
		</div>

	</div>
</div>