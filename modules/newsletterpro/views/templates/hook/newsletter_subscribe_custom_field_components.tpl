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

{if $type == $types.TYPE_SELECT}
<div class="select-group">
	<input type="hidden" name="hidden_custom_variable_{$variable_name|escape:'html':'UTF-8'}">
	<select id="custom_variable_{$variable_name|escape:'html':'UTF-8'}" name="custom_variable_{$variable_name|escape:'html':'UTF-8'}" class="np-select-option">
		<option value="">- {l s='none' mod='newsletterpro'} -</option>
		{foreach from=$value item=item}
			<option value="{$item|escape:'htmlall':'UTF-8'}">{$item|escape:'htmlall':'UTF-8'}</option>
		{/foreach}
	</select>
</div>
{else if $type == $types.TYPE_CHECKBOX}
<ul class="ul_custom_variable_{$variable_name|escape:'html':'UTF-8'}">
	<input type="hidden" name="hidden_custom_variable_{$variable_name|escape:'html':'UTF-8'}">
	{foreach from=$value key=key item=item}
	<li class="clearfix">
		<div class="np-fs-checkbox">
			<input type="checkbox" class="custom_variable_{$variable_name|escape:'html':'UTF-8'} np-fs-checkbox" id="custom_variable_{$variable_name|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}" name="custom_variable_{$variable_name|escape:'html':'UTF-8'}[{$key|escape:'html':'UTF-8'}]" value="{$item|escape:'htmlall':'UTF-8'}"> 
		</div>
		<div class="np-fs-checkbox-label">
			<label for="custom_variable_{$variable_name|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}" class="np-fs-checkbox-label" style="font-weight: normal;">{$item|escape:'htmlall':'UTF-8'}</label>
		</div>
	</li>
	{/foreach}
</ul>
{else if $type == $types.TYPE_RADIO}
<ul class="ul_custom_variable_{$variable_name|escape:'html':'UTF-8'}">
	<input type="hidden" name="hidden_custom_variable_{$variable_name|escape:'html':'UTF-8'}">
	{foreach from=$value key=key item=item}
	<li class="clearfix">
		<div class="np-fs-checkbox">
			<input type="radio" class="custom_variable_{$variable_name|escape:'html':'UTF-8'}" id="custom_variable_{$variable_name|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}" name="custom_variable_{$variable_name|escape:'html':'UTF-8'}" value="{$item|escape:'htmlall':'UTF-8'}"> 
		</div>
		<div class="np-fs-checkbox-label">
			<label for="custom_variable_{$variable_name|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}" class="" style="font-weight: normal;">{$item|escape:'htmlall':'UTF-8'}</label>
		</div>
	</li>
	{/foreach}
</ul>
{else if $type == $types.TYPE_INPUT_TEXT}
<input type="hidden" name="hidden_custom_variable_{$variable_name|escape:'html':'UTF-8'}">
<input class="form-control" type="text" name="custom_variable_{$variable_name|escape:'html':'UTF-8'}" value="">
{else if $type == $types.TYPE_TEXTAREA}
<input type="hidden" name="hidden_custom_variable_{$variable_name|escape:'html':'UTF-8'}">
<textarea class="form-control-textarea" name="custom_variable_{$variable_name|escape:'html':'UTF-8'}"></textarea>
{/if}