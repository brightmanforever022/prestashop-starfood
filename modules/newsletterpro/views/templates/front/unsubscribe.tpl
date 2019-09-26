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

{capture name=path}
<span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>{l s='Unsubscribe'  mod='newsletterpro'}
{/capture}

<div id="newsletterpro-unsubscribe">
	{if isset($unsubscribe)}
	<p class="success">{l s='You have successfully unsubscribed from our newsletter.' mod='newsletterpro'}</p>
	{elseif isset($email_not_found)}
	<p class="error">{l s='You are not subscribed at our newsletter.' mod='newsletterpro'}</p>
	{elseif isset($email_not_valid)}
	<p class="error">{l s='Your email is not valid.' mod='newsletterpro'}</p>
	{elseif isset($token_not_valid)}
	<p class="error">{l s='Invalid unsubscription token.' mod='newsletterpro'}</p>
	{else}
	&nbsp;
	{/if}
</div>