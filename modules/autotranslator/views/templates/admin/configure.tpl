{*
* 2007-2017 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="bootstrap panel main-block">
	<div class="stats-block">
		{l s='Number of characters processed today' mod='autotranslator'}: <span class="day-stats">{$stats_data.day|intval}</span>,
		{l s='this month' mod='autotranslator'}: <span class="month-stats">{$stats_data.month|intval}</span>
		<i class="icon-question-circle label-tooltip" data-toggle="tooltip" title="{l s='Including HTML tags and service characters' mod='autotranslator'}"></i>
	</div>
	<form method="post" action="" class="form-horizontal list-params clearfix">
		<select name="at_ct" class="inline-block">
			{foreach $content_types as $val => $name}
				<option value="{$val|escape:'html':'UTF-8'}"{if $current_ct == $val} selected{/if}>{$name|escape:'html':'UTF-8'}</option>
			{/foreach}
		</select>
		{foreach $special_params as $ct => $param}
			{foreach $param as $name => $options}
				<select name="{$name|escape:'html':'UTF-8'}" class="update-list inline-block special-param {$ct|escape:'html':'UTF-8'}{if $ct != $current_ct} hidden{/if}">
					{foreach $options as $opt_name => $display_name}
						<option value="{$opt_name|escape:'html':'UTF-8'}">{$display_name|escape:'html':'UTF-8'}</option>
					{/foreach}
				</select>
			{/foreach}
		{/foreach}
		<select name="at_lang" class="update-list inline-block{if $current_ct == 'theme' || $current_ct == 'module' } hidden{/if}">
			{foreach $languages as $iso => $name}
				{if $iso != 'all'}
					<option value="{$iso|escape:'html':'UTF-8'}"{if $iso == $current_lang_iso} selected{/if}>{$iso|escape:'html':'UTF-8'}</option>
				{/if}
			{/foreach}
		</select>
		<div class="inline-block sorting">
			<label class="label-inline"><span>{l s='Sort by' mod='autotranslator'}</span></label>
			<select name="order_by" class="update-list inline-block order-by">
				{foreach $sorting_options as $opt_name => $o}
					<option value="{$opt_name|escape:'html':'UTF-8'}" class="{if !empty($o.class)}special-option {$o.class|escape:'html':'utf-8'}{/if}"{if $order.by == $opt_name} selected{/if}>{$o.name|escape:'html':'UTF-8'}</option>
				{/foreach}
			</select>
			{$way_options = ['DESC' => 'icon-long-arrow-down', 'ASC' => 'icon-long-arrow-up']}
			{foreach $way_options as $value => $icon_class}
				<a href="#" class="{$icon_class|escape:'html':'UTF-8'} order-way-label{if $order.way == $value} active{/if}" data-way="{$value|escape:'html':'UTF-8'}"></a>
			{/foreach}
			<input type="hidden" name="order_way" value="{$order.way|escape:'html':'UTF-8'}" class="order-way update-list">
		</div>
		<div class="pull-right">
			<label class="alert-info"><input type="checkbox" class="overwrite_existing" name="overwrite_existing"{if !empty($overwrite_existing)} checked{/if}> {l s='Overwrite existing translations' mod='autotranslator'}</label>
		</div>
	</form>
	<div class="dynamic-list">
		{include file="./list.tpl"}
	</div>
</div>
{* since 2.7.0 *}
