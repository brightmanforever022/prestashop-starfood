{if $params.type|escape:'htmlall':'UTF-8' == 'text'}
	{if isset($params.lang) && $params.lang == true}
		<div style="overflow:hidden">
			<label for="{$params.name|escape:'htmlall':'UTF-8'}">{$params.label|escape:'htmlall':'UTF-8'}</label>
			<div class="margin-form">
				<div style="float:left">
					{foreach $languages as $language}
						<div>
							<input type="text" name="{$params.name|escape:'htmlall':'UTF-8'}_{$language['id_lang']|escape:'htmlall':'UTF-8'}" value="{$fields_value[$params.name|escape:'htmlall':'UTF-8'][$language['id_lang']]|escape:'htmlall':'UTF-8'}" />
							<img src="{$THEME_LANG_DIR|escape:'htmlall':'UTF-8'}{$language['id_lang']|escape:'htmlall':'UTF-8'}.jpg" alt="{$language['iso_code']|escape:'htmlall':'UTF-8'}" title="{$language['name']|escape:'htmlall':'UTF-8'}" />
						</div>
					{/foreach}
				</div>
			</div>
		</div>
		<br />
	{else}
		<div style="overflow:hidden">
			<label for="{$params.name|escape:'htmlall':'UTF-8'}">{$params.label|escape:'htmlall':'UTF-8'}</label>
			<div class="margin-form">
				<input type="text" name="{$params.name|escape:'htmlall':'UTF-8'}" value="{$fields_value[$params.name|escape:'htmlall':'UTF-8']|escape:'htmlall':'UTF-8'}" />
			</div>
		</div>
		<br />
	{/if}
{elseif $params.type|escape:'htmlall':'UTF-8' == 'switch' || $params.type|escape:'htmlall':'UTF-8' == 'radio'}
	<div style="overflow:hidden">
		<label for="{$params.name|escape:'htmlall':'UTF-8'}">{$params.label|escape:'htmlall':'UTF-8'}</label>
		<div class="margin-form">
			{foreach $params.values as $value}
				<input type="radio" name="{$params.name|escape:'html':'UTF-8'}" id="{$value.id|intval}" value="{$value.value|escape:'html':'UTF-8'}"
						{if $fields_value[$params.name] == $value.value}checked="checked"{/if}
						{if isset($params.disabled) && $params.disabled}disabled="disabled"{/if} />
				<label class="t" for="{$value.id|intval}">
				 {if isset($params.is_bool) && $params.is_bool == true}
					{if $value.value == 1}
						<img src="../img/admin/enabled.gif" alt="{$value.label|escape:'htmlall':'UTF-8'}" title="{$value.label|escape:'html':'UTF-8'}" />
					{else}
						<img src="../img/admin/disabled.gif" alt="{$value.label|escape:'htmlall':'UTF-8'}" title="{$value.label|escape:'html':'UTF-8'}" />
					{/if}
				 {else}
					{$value.label|escape:'html':'UTF-8'}
				 {/if}
				</label>
				{if isset($params.br) && $params.br}<br />{/if}
				{if isset($value.p) && $value.p}<p>{$value.p|escape:'htmlall':'UTF-8'}</p>{/if}
			{/foreach}
		</div>
	</div>
	<br />
{elseif $params.type|escape:'htmlall':'UTF-8' == 'submit'}
	<center>
		<input class="button" type="submit" name="{$params.name|escape:'htmlall':'UTF-8'}" />
	</center>
{elseif $params.type|escape:'htmlall':'UTF-8' == 'select'}
	<label for="{$params.name|escape:'htmlall':'UTF-8'}">{$params.label|escape:'htmlall':'UTF-8'}</label>
	<div class="margin-form">
		<select name="{$params.name|escape:'htmlall':'UTF-8'}">
		{foreach $params.options.query as $option}
			<option value="{$option.id|escape:'htmlall':'UTF-8'}" {if $fields_value[$params.name] == $option.id}selected{/if}>{$option.name|escape:'htmlall':'UTF-8'}</option>
		{/foreach}
		</select>
	</div>
	<br />
{elseif $params.type|escape:'htmlall':'UTF-8' == 'file'}
	<label for="{$params.name|escape:'htmlall':'UTF-8'}">{$params.label|escape:'htmlall':'UTF-8'}</label>
	<div class="margin-form">
		<input type="file" name="{$params.name|escape:'htmlall':'UTF-8'}" />
	</div>
	<br />
{elseif $params.type|escape:'htmlall':'UTF-8' == 'checkbox'}
	<label for="{$params.name|escape:'htmlall':'UTF-8'}">{$params.label|escape:'htmlall':'UTF-8'}</label>
	<div class="margin-form">
		{foreach $params.values.query as $value}
			<input type="checkbox" name="{$params.name|escape:'htmlall':'UTF-8'}_{$value.id|escape:'htmlall':'UTF-8'}" id="{$params.name|escape:'htmlall':'UTF-8'}_{$value.id|escape:'htmlall':'UTF-8'}" {if isset($fields_value[$params.name|escape:'htmlall':'UTF-8'|cat:_|cat:$value.id|escape:'htmlall':'UTF-8']) && $fields_value[$params.name|escape:'htmlall':'UTF-8'|cat:_|cat:$value.id|escape:'htmlall':'UTF-8'] == 'on'}checked="checked"{/if}>{$value.name|escape:'htmlall':'UTF-8'}</option>
			<br />
		{/foreach}
	</div>
	<br />
{elseif $params.type|escape:'htmlall':'UTF-8' == 'free'}
	{if $fields_value[$params.name]|escape:'htmlall':'UTF-8'}{$fields_value[$params.name]|escape:'htmlall':'UTF-8'}{/if}
{/if}