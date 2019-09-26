{*
 *  Leo Theme for Prestashop 1.6.x
 *
 * @author    http://www.leotheme.com
 * @copyright Copyright (C) October 2013 LeoThemes.com <@emai:leotheme@gmail.com>
 *               <info@leotheme.com>.All rights reserved.
 * @license   GNU General Public License version 2
*}

{if isset($html)}
<div class="widget-html block">
	{if isset($widget_heading)&&!empty($widget_heading)}
	<div class="widget-heading title_block">
		{$widget_heading|escape:'html':'UTF-8'}
	</div>
	{/if}
	<div class="widget-inner block_content">
		{$html}{* HTML form , no escape necessary *}
	</div>
</div>
{/if}