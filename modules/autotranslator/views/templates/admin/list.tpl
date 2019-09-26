{*
* 2007-2017 Amazzing
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*
*  @author    Amazzing <mail@amazzing.ru>
*  @copyright 2007-2017 Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*}

<table class="table resource-list">
	<tbody class="dynamic-rows">
	{if $items|count}
		{foreach $items as $item}
		<tr data-identifier="{$item.$identifier|escape:'html':'UTF-8'}">
			{foreach array_keys($fields_list) as $i => $prop}
				<td>
					<label>
						{if !$i}
							<input type="checkbox" name="items[]" class="item-checkbox" value="{$item.$identifier|escape:'html':'UTF-8'}">
							{if $item.$identifier|intval}
								<span class="item-identifier">
									{$item.$identifier|intval}
									{if isset($item.identifier_extension)}{$item.identifier_extension|escape:'html':'UTF-8'}{/if}
								</span>
							{/if}
						{/if}
						<span class="item-value">
							{$item.$prop|escape:'html':'UTF-8'}
							{if !empty($item.is_custom_value)}<span class="i">({l s='custom value' mod='autotranslator'})</span>{/if}
						</span>
						<span class="ajax-response"></span>
					</label>
					{if !$i && $order.by != 'name' && $order.by != 'id' && isset($item[$order.by])}
						<span class="item-sorting-value">{$item[$order.by]|escape:'html':'UTF-8'}</span>
					{/if}
				</td>
			{/foreach}
			<td width="200">
				<div class="btn-group pull-right">
					<a href="#" class="default btn btn-default t-text">
						<span class="stop-txt"><i class="icon-refresh icon-spin"></i> {l s='Stop' mod='autotranslator'}</span>
						<span class="main-txt">{l s='Translate to' mod='autotranslator'}</span>
					</a>
					<button class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="icon-caret-down"></i></button>
					<ul class="dropdown-menu">
						{foreach $languages as $iso => $name}
							{if $iso == $current_lang_iso}{continue}{/if}
							<li><a href="#" class="translate" data-to="{$iso|escape:'html':'UTF-8'}"> <i class="icon-globe"></i> {$name|escape:'html':'UTF-8'}</a></li>
						{/foreach}
					</ul>
				</div>
			</td>
		</tr>
		{/foreach}
	{else}
		<tr>
			<td class="list-empty" colspan="{$fields_list|count + 1}">
				<div class="list-empty-msg">
					<i class="icon-warning-sign list-empty-icon"></i>
					{l s='No items found' mod='autotranslator'}
				</div>
			</td>
		</tr>
	{/if}
	</tbody>
</table>
<div class="pull-right">
	{include file="./pagination.tpl" npp=$pagination.npp p=$pagination.p total=$total}
</div>
<div class="list-actions clearfix">
	<div class="pull-left">
		<div class="btn-group dropup">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				{l s='Bulk actions' mod='autotranslator'} <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				{foreach $bulk_checkbox_actions as $class => $action}
					<li><a href="#" class="chk-action"><i class="{$class|escape:'html':'UTF-8'}"></i> {$action|escape:'html':'UTF-8'}</a></li>
				{/foreach}
				<li class="divider"></li>
				{foreach $languages as $iso => $name}
					{if $iso == $current_lang_iso}{continue}{/if}
					<li>
						<a href="#" class="bulk-translate" data-to="{$iso|escape:'html':'UTF-8'}">
							<i class="icon-globe"></i> {l s='Translate to %s' mod='autotranslator' sprintf=[$name]}
						</a>
					</li>
				{/foreach}
			</ul>
		</div>
	</div>
</div>
{* since 2.7.0 *}
