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
{*
*** LICENSE ***
Copyright (c) Cimpean Andrei . All Rights Reserved
*** LICENSE ***
*}

<div class="our-modules-content clearfix">
	{foreach $items as $item}
		<div class="module-item">
			<div class="module-icon">
				{if $display_details == true && $item->displayDetails|intval == true}
					<a target="_blank" href="{$item->details|escape:'quotes':'UTF-8'}"><img class="m-icon" src="{$item->icon|escape:'quotes':'UTF-8'}"></a>
				{else}
					<img class="m-icon" src="{$item->icon|escape:'quotes':'UTF-8'}">
				{/if}

				{if $display_badge == true && $item->displayBadge|intval == true}
					<img class="m-badge" src="{$item->badge|escape:'quotes':'UTF-8'}"><br>
					{$item->downloads|escape:'html':'UTF-8'}
				{/if}
			</div>
			<div class="module-content">
				<div class="module-header clearfix">
					<h3 class="module-name">{$item->name|escape:'html':'UTF-8'}</h3>
					{if $display_version == true}
					<span class="module-version">{$item->version|escape:'html':'UTF-8'}</span>
					{/if}
					<div class="clear" style="clear: both;"></div>
				</div>
				<div class="module-info clearfix">
					{if $display_rating == true && $item->displayRating|intval == true}
					<div class="module-rating"><img src="{$item->rating|escape:'quotes':'UTF-8'}"></div>
					{/if}
					<div class="module-links">
						<ul>
							{if $display_details == true && $item->displayDetails|intval == true}
							<li><a class="module-details" target="_blank" href="{$item->details|escape:'quotes':'UTF-8'}">{$item->detailsHTML|escape:'html':'UTF-8'}</a> | </li>
							{/if}
							{if $display_video == true && $item->displayVideo|intval == true}
							<li><a class="module-video" target="_blank" href="{$item->video|escape:'quotes':'UTF-8'}">{$item->videoHTML|escape:'html':'UTF-8'}</a> | </li>
							{/if}
							{if $display_demo == true && $item->displayDemo|intval == true}
							<li><a class="module-demo" target="_blank" href="{$item->demo|escape:'quotes':'UTF-8'}">{$item->demoHTML|escape:'html':'UTF-8'}</a></li>
							{/if}
						</ul>
					</div>
					<div class="clear" style="clear: both;"></div>
				</div>
				<div class="module-description clearfix">
					<p>{$item->description|escape:'html':'UTF-8'|truncate:160:"..."}</p>
				</div>
				<div class="module-price clearfix">
					{if $display_price == true && $item->price|floatval > 0}
					<a class="btn btn-default text-decoration-none" target="_blank" href="{if $display_details == true && $item->displayDetails|intval == true}{$item->details|escape:'quotes':'UTF-8'}{else}javascript:{ldelim}{rdelim}{/if}" >
						<span class="icon-shopping-cart fa fa-shopping-cart"></span>
						{$item->price|escape:'html':'UTF-8'}
					</a>
					{/if}
					{if $display_new == true && $item->displayNew|intval == 1}
					<span class="module-new"> {$item->newHTML|escape:'quotes':'UTF-8'} </span>
					{/if}
				</div>
			</div>
		</div>
	{/foreach}
	<div class="clear" style="clear: both;"></div>
</div>

<script type="text/javascript">
	(function(){
		try
		{
			var countNew = parseInt('{$count_new|escape:'html':'UTF-8'}');
			if (countNew > 0)
				$('.count-new').show().html(countNew + ' {$item->newHTML|escape:'quotes':'UTF-8'}');
		}
		catch (e)
		{
			
		}
	}());
</script>