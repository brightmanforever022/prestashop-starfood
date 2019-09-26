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

{if $step}
<div class="thd-step">
	<div style="width: 50%; float: left;">
		<div>
			<h4>{l s='Remaining email' mod='newsletterpro'}</h4>
			<div class="form-inline clearfix">
				<input id="resend-left-list-send" class="form-control np-valign-middle resend-left-list-send" type="checkbox">
				<label class="control-label in-win" style="padding-top: 0;">{l s='(select all)' mod='newsletterpro'}</label>
				
				<div class="form-group pull-right">
					<a id="np-btn-export-send-history-rem" href="javascript:{}" class="btn btn-default np-btn-export-send-history-rem">
						<i class="icon icon-download"></i>
						{l s='Export' mod='newsletterpro'}
					</a>
				</div>
			</div>

			<div class="clear">&nbsp;</div>
			<ul class="first_item">
			{foreach $step.emails_to_send as $value}
				<li> <span class="email_text">{$value|escape:'html':'UTF-8'}</span> </li>
			{/foreach}
			</ul>
		</div>
	</div>
	<div style="width: 50%; float: left;">
		<div>
			<h4>{l s='Sent emails' mod='newsletterpro'}</h4>
			<div class="form-inline clearfix">
				<div class="form-group pull-left">
					<input id="resend-undelivered-list-send" class="form-control np-valign-middle resend-undelivered-list-send" type="checkbox">
					<label class="control-label in-win" style="padding-top: 0;">{l s='(select only faild)' mod='newsletterpro'}</label>
				</div>
				<div class="form-group pull-right">
					<a id="np-btn-export-send-history" href="javascript:{}" class="btn btn-default np-btn-export-send-history">
						<i class="icon icon-download"></i>
						{l s='Export' mod='newsletterpro'}
					</a>

					<a id="np-btn-resend-send" href="javascript:{}" class="btn btn-default np-btn-resend">
						<span class="btn-ajax-loader"></span>
						<i class="icon icon-send"></i> 
						{l s='Resend' mod='newsletterpro'}
					</a>
				</div>
			</div>

			<div class="clear">&nbsp;</div>
			<ul class="last_item">
			{foreach $step.emails_sent as $value}
				{if isset($value.status) && isset($value.email)}
					{if $value.status == true}
						<li>
							<span class="email_text" style="margin-top: 3px; display: inline-block;">{$value.email|escape:'html':'UTF-8'}</span>
							<span class="status pull-left">
								<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>
							</span>
						</li>
					{else}
						<li>
							<span class="email_text" style="margin-top: 3px; display: inline-block;">{$value.email|escape:'html':'UTF-8'}</span>
							<span class="status pull-left">
								<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>
							</span>
						</li>
					{/if}
				{/if}
			{/foreach}
			</ul>
		</div>
	</div>
	<div class="clear">&nbsp;</div>
</div>
{/if}