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

{if isset($fix_document_write) && $fix_document_write == 1}
<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;">
{else}
<script type="text/javascript"> 
	if(window.location.hash == '#statistics') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: block;">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;">');
	{rdelim} 
</script>
{/if}
	<h4>{l s='Statistics' mod='newsletterpro'}</h4>
	<div class="separation"></div>
	<div style="margin-bottom: 5px;">
		<h4 style="float: left;">{l s='Top clicked products from the newsletter' mod='newsletterpro'}</h4>
		<a  href="javascript:{}" id="clear-statistics" class="btn btn-default pull-right">
			<i class="icon icon-eraser"></i> {l s='Clear Statistics' mod='newsletterpro'}
		</a>
		<div class="clear"></div>
		<div class="separation"></div>
	</div>
	<table id="statistics-table" class="table table-bordered statistics-table">
		<thead>
			<tr>
				<th class="top" data-field="top">{l s='Top' mod='newsletterpro'}</th>
				<th class="clicks" data-field="clicks">{l s='Clicks' mod='newsletterpro'}</th>
				<th class="image" data-template="image">{l s='Image' mod='newsletterpro'}</th>
				<th class="name" data-field="name">{l s='Name' mod='newsletterpro'}</th>
				<th class="price_display" data-field="price_display">{l s='Price' mod='newsletterpro'}</th>
			</tr>
		</thead>
	</table>
	<br>
</div>