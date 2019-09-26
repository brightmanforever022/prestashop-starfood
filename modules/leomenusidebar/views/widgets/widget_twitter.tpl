{*
 *  Leo Theme for Prestashop 1.6.x
 *
 * @author    http://www.leotheme.com
 * @copyright Copyright (C) October 2013 LeoThemes.com <@emai:leotheme@gmail.com>
 *               <info@leotheme.com>.All rights reserved.
 * @license   GNU General Public License version 2
*}

{if isset($username)}
<div class="widget-twitter block">
	{if isset($widget_heading)&&!empty($widget_heading)}
	<div class="widget-heading title_block">
		{$widget_heading|escape:'html':'UTF-8'}
	</div>
	{/if}
	<div class="widget-inner block_content">
		<a class="twitter-timeline" data-dnt="true" data-theme="{$theme|escape:'html':'UTF-8'}" data-link-color="#FFFFFF" data-chrome="{$chrome|escape:'html':'UTF-8'}" data-border-color="#{$border_color|escape:'html':'UTF-8'}" lang="EN" data-tweet-limit="{$count|escape:'html':'UTF-8'}" data-show-replies="{$show_replies|escape:'html':'UTF-8'}" href="https://twitter.com/{$username|escape:'html':'UTF-8'}"  data-widget-id="{$twidget_id|escape:'html':'UTF-8'}">Tweets by @{$username|escape:'html':'UTF-8'}</a>
		{$js}{* HTML form , no escape necessary *}
	</div>
</div>
{/if} 