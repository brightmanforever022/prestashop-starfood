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

{if !empty($errors)}
<pre>
<span style="color: red;">{foreach $errors as $error}{$error|escape:'html':'UTF-8'}<br>{/foreach}</span>
</pre>
{/if}
{if isset($couts.added)}
<pre>
{l s='Added List' mod='newsletterpro'} : ({$last_date_chimp_sync|escape:'html':'UTF-8'} - {l s='last lists synchronization date' mod='newsletterpro'})
-------------------------------------------------------------
( <span style="color: green; font-weight: bold;">{$couts.added.adds|intval} )</span> {l s='emails created' mod='newsletterpro'}, ( <span style="color: green; font-weight: bold;">{$couts.added.updates|intval}</span> ) {l s='emails updated' mod='newsletterpro'}, ( <span style="color: red; font-weight: bold;">{$couts.added.errors|intval}</span> ) {l s='emails error' mod='newsletterpro'}
</pre>
{/if}

{if isset($couts.visitors)}
<pre>
{l s='Visitors List' mod='newsletterpro'} {if isset($subscription_active) && $subscription_active == true}{l s='(subscribed at the Newsletter Pro module)' mod='newsletterpro'}{else}{l s='(subscribed at the Block Newsletter module)' mod='newsletterpro'}{/if} : ({$last_date_chimp_sync|escape:'html':'UTF-8'} - {l s='last lists synchronization date' mod='newsletterpro'})
-------------------------------------------------------------
( <span style="color: green; font-weight: bold;">{$couts.visitors.adds|intval}</span> ) {l s='emails created' mod='newsletterpro'}, <span style="color: green; font-weight: bold;">( {$couts.visitors.updates|intval}</span> ) {l s='emails updated' mod='newsletterpro'}, ( <span style="color: red; font-weight: bold;">{$couts.visitors.errors|intval}</span> ) {l s='emails error' mod='newsletterpro'}
</pre>
{/if}

{if isset($couts.customers)}
<pre>
{l s='Customers List' mod='newsletterpro'} : ({$last_date_chimp_sync|escape:'html':'UTF-8'} - {l s='last lists synchronization date' mod='newsletterpro'})
-------------------------------------------------------------
( <span style="color: green; font-weight: bold;">{$couts.customers.adds|intval}</span> ) {l s='emails created' mod='newsletterpro'}, ( <span style="color: green; font-weight: bold;">{$couts.customers.updates|intval}</span> ) {l s='emails updated' mod='newsletterpro'}, ( <span style="color: red; font-weight: bold;">{$couts.customers.errors|intval}</span> ) {l s='emails error' mod='newsletterpro'}
</pre>
{/if}

{if isset($couts.orders)}
<pre>
{l s='Orders' mod='newsletterpro'} : ({$chimp_last_date_sync_orders|escape:'html':'UTF-8'} - {l s='last orders synchronization date' mod='newsletterpro'})
-------------------------------------------------------------
( <span style="color: green; font-weight: bold;">{$couts.orders.adds|intval}</span> ) {l s='emails created' mod='newsletterpro'}, <span style="display: none;">( <span style="color: green; font-weight: bold;">{$couts.orders.updates|intval}</span> ) {l s='emails updated' mod='newsletterpro'},</span> ( <span style="color: red; font-weight: bold;">{$couts.orders.errors|intval}</span> ) {l s='emails error' mod='newsletterpro'} ({l s='The orders already exists in MailChimp' mod='newsletterpro'})
</pre>
{/if}