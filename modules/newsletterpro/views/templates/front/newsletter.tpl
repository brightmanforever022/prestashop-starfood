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

{if isset($template)}
	{* HTML CONTENT *}
	{$template|strval}
	{if !$jquery_no_conflict && isset($jquery_url_exists)}
	<script type="text/javascript" src="{$jquery_url|escape:'quotes':'UTF-8'}"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			var body = $('body'),
				table = body.find('table').first();
			if (body.length && table.length) {
				body.css({
					'background-color': table.css('background-color')
				});
			}
		});
	</script>
	{/if}
{/if}