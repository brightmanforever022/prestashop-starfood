{*
 *  Leo Theme for Prestashop 1.6.x
 *
 * @author    http://www.leotheme.com
 * @copyright Copyright (C) October 2013 LeoThemes.com <@emai:leotheme@gmail.com>
 *               <info@leotheme.com>.All rights reserved.
 * @license   GNU General Public License version 2
*}

{if isset($subcategories)}
<div class="widget-subcategories">
	{if isset($widget_heading)&&!empty($widget_heading)}
	<div class="widget-heading">
		{$widget_heading}
	</div>
	{/if}
	<div class="widget-inner">
		<div class="widget-heading">{$title}</div>
		<ul>
			{foreach from=$subcategories item=subcategory}
				<li class="clearfix">
					<a href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$subcategory.name|escape:'htmlall':'UTF-8'}" class="img">
						{$subcategory.name|escape:'htmlall':'UTF-8'} 
					</a>
				</li>
			{/foreach}

		</ul>
	</div>
</div>
{/if} 