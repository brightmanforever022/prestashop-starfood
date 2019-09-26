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

{$p_max = ceil($total/$npp)}
{$prev = $p - 1}
{$next = $p + 1}

<form class="list-pagination{if $npp > $total && $p == 1} hidden{/if}">
	<div class="npp-holder pull-left">
		<select name="npp" class="npp inline-block update-list">
			{$npp_values = [5,10,20,50,100,1000]}
			{foreach $npp_values as $val}
				<option value="{$val|intval}"{if $npp == $val} selected{/if}>{$val|intval}</option>
			{/foreach}
			{* not using $total because it can be different for different resources *}
			<option value="100000"{if $npp == 100000} selected{/if}>{l s='All' mod='autotranslator'}</option>
		</select> {l s='of %s' mod='autotranslator' sprintf=[$total]}
	</div>
	{if $p_max > 1}
	<div class="pages-holder pull-right">
		<a href="#" class="go-to-page prev" data-page="{if $prev}{$prev|intval}{else}1{/if}"><i class="icon-angle-left"></i></a>
		{if $prev}
			<a href="#" class="go-to-page first" data-page="1">1</a>
			{if $prev > 1}
				{if $prev > 2}<span class="p-dots">...</span>{/if}
				<a href="#" class="go-to-page" data-page="{$prev|intval}">{$prev|intval}</a>
			{/if}
		{/if}
		<span href="#" class="current-page" data-page="{$p|intval}">{$p|intval}</span>
		{if $next <= $p_max}
			{if $next < $p_max}
				<a href="#" class="go-to-page" data-page="{$next|intval}">{$next|intval}</a>
				{if $next < $p_max - 1}<span class="p-dots">...</span>{/if}
			{/if}
			<a href="#" class="go-to-page last" data-page="{$p_max|intval}">{$p_max|intval}</a>
		{else}
			{$next = $p_max}
		{/if}
		<a href="#" class="go-to-page next" data-page="{$next|intval}"><i class="icon-angle-right"></i></a>
	</div>
	{/if}
</form>
