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
		<h4 style="margin-bottom: 5px; margin-left: 8px;">{l s='Remaining email' mod='newsletterpro'}</h4>
		<div class="clear">&nbsp;</div>
		<ul class="first_item">
		{foreach $step.emails_to_send as $value}
			<li> <span class="email_text">{$value.email|escape:'html':'UTF-8'}</span> </li>
		{/foreach}
		</ul>
	</div>
	<div style="width: 50%; float: left;">
		<h4 style="margin-bottom: 5px; margin-left: 8px;">{l s='Sent emails' mod='newsletterpro'}</h4>
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
	<div class="clear">&nbsp;</div>
</div>
{/if}