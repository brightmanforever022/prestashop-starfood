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

{if isset($get_gender) && $get_gender}
	{foreach from=$genders key=k item=gender}
		<div class="radio-inline" style="margin-bottom: 0; height: 20px;">
			<label for="id_gender{$gender->id|intval}" class="top" style="margin-bottom: 0;">
				<input type="radio" name="id_gender" id="id_gender{$gender->id|intval}" class="np-input-radio" value="{$gender->id|intval}" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id}checked="checked"{/if} />
				{$gender->name|escape:'html':'UTF-8'}
			</label>
		</div>
	{/foreach}
{else if isset($get_firstname) && $get_firstname}
<input class="form-control" type="text" name="firstname" value="">
{else if isset($get_lastname) && $get_lastname}
<input class="form-control" type="text" name="lastname" value="">
{else if isset($get_languages) && $get_languages}
<div class="select-group">
<select id="np-lang-select" class="np-select-option" name="id_lang">
	{foreach $langs_sub as $lang}
		<option value="{$lang.id_lang|intval}" {if $lang.selected == true}selected="selected"{/if}>{$lang.name|escape:'html':'UTF-8'}</option>
	{/foreach}
</select>
</div>
{else if isset($get_birthday) && $get_birthday}
<div class="np-col-4">
	<div class="select-group">
	<select id="np-days" name="days" class="np-select-option">
			<option value="">-</option>
			{foreach from=$days item=day}
				<option value="{$day|intval}">{$day|intval}&nbsp;&nbsp;</option>
			{/foreach}
	</select>
	</div>
</div>
<div class="np-col-4">
	<div class="select-group">
	<select id="np-months" name="months" class="np-select-option">
		<option value="">-</option>
		{foreach from=$months key=k item=month}
			<option value="{$k|escape:'html':'UTF-8'}">{$month|escape:'html':'UTF-8'}&nbsp;</option>
		{/foreach}
	</select>
	</div>
</div>
<div class="np-col-4">
	<div class="select-group">
	<select id="np-years" name="years" class="np-select-option">
		<option value="">-</option>
		{foreach from=$years item=year}
			<option value="{$year|intval}">{$year|intval}&nbsp;&nbsp;</option>
		{/foreach}
	</select>
	</div>
</div>
{else if isset($get_list_of_interest) && $get_list_of_interest}
	{if $list_of_interest_type == $LIST_OF_INTEREST_TYPE_SELECT}
	<div class="select-group">
	<select id="np-list-of-interest" name="list_of_interest" class="np-select-option">
		<option value="">-</option>
		{foreach from=$list_of_interest item=item}
			<option value="{$item.id_newsletter_pro_list_of_interest|intval}">{$item.name|escape:'html':'UTF-8'}&nbsp;&nbsp;</option>
		{/foreach}
	</select>
	</div>
	{else}
	<ul class="np-list-of-interest-checkbox">
	{foreach from=$list_of_interest item=item}
	<li>
		<input type="checkbox" class="np-input-checkbox" id="np-list-of-interest-{$item.id_newsletter_pro_list_of_interest|intval}" name="list_of_interest_{$item.id_newsletter_pro_list_of_interest|intval}" value="{$item.id_newsletter_pro_list_of_interest|intval}"> 
		<label for="np-list-of-interest-{$item.id_newsletter_pro_list_of_interest|intval}" style="font-weight: normal;">{$item.name|escape:'html':'UTF-8'}</label>
	</li>
	{/foreach}
	</ul>
	{/if}
{else if isset($get_email) && $get_email}
<input id="np-popup-email" class="form-control" type="text" name="email" value="">
{else if isset($get_submit) && $get_submit}
<a href="#" id="submit-newsletterpro-subscribe" class="np-button submit-newsletterpro-subscribe">
	<span>{l s='Subscribe' mod='newsletterpro'}</span>
</a>
{else if isset($get_close_forever) && $get_close_forever}
<a href="javascript:{}" id="newsletterpro-subscribe-close-forever" class="close-forever">
	<span>{l s='Don\'t show next time.'  mod='newsletterpro'}</span>
</a>
{else if isset($get_info) && $get_info}
<div id="ajax-errors-subscribe" class="alert alert-danger" style="display: none;"></div>
<div class="clear"></div>
<div id="ajax-success-subscribe" class="alert alert-success" style="display: none;"></div>
<div class="clear"></div>
{else if isset($get_terms_and_conditions_link) && $get_terms_and_conditions_link}
<a href="{$terms_and_conditions_url|escape:'quotes':'UTF-8'}" target="_blank" id="np-terms-and-conditions-link" class="np-link">{l s='terms and conditions' mod='newsletterpro'}</a>
{else if isset($get_terms_and_conditions_checkbox) && $get_terms_and_conditions_checkbox}
<input type="checkbox" id="np-terms-and-conditions-checkbox">
{else if isset($get_terms_and_conditions_full) && $get_terms_and_conditions_full}
<div class="np-terms-and-conditions-full">
{* HTML CONTENT *}
{$gtac_checkbox|strval}
<span class="np-terms-and-concitions-text">{l s='I agree to the' mod='newsletterpro'} 
{* HTML CONTENT *}
{$gtac_link|strval}.
</span>
<div class="clear"></div>
</div>
{/if}