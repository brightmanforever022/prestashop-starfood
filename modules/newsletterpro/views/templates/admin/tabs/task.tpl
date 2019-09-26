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
	if(window.location.hash == '#task') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: block;">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;">');
	{rdelim} 
</script>
{/if}

	<h4>{l s='Tasks' mod='newsletterpro'}</h4>
	<div class="separation"></div>
	<table id="task-list" class="table table-bordered task-list">
		<thead>
			<tr>
				<th class="template" data-field="template">{l s='Template' mod='newsletterpro'}</th>
				<th class="date-start" data-field="date_start">{l s='Start Date' mod='newsletterpro'}</th>
				<th class="smtp-select" data-template="smtp">{l s='Send Method' mod='newsletterpro'}</th>
				<th class="task-active" data-field="active">{l s='Active' mod='newsletterpro'}</th>
				<th class="task-status" data-field="status">{l s='Status' mod='newsletterpro'}</th>
				<th class="actions" data-template="actions">{l s='Actions' mod='newsletterpro'}</th>
			</tr>
		</thead>
	</table>
	<br>
	<div style="display: block; height: auto; background-position: 5px; padding-top: 10px; padding-bottom: 10px;" class="alert alert-info">
		<p style="margin-top: 0;" class="cron-link"><span style="color: black;">{l s='CRON URL:' mod='newsletterpro'}</span> <span class="icon icon-cron-link"></span>{$cron_link|escape:'quotes':'UTF-8'}</p>
		<p style="margin-bottom: 0;">{l s='To make tasks to run automatically every day set the CRON job from your website control panel (Plesk, cPanel, DirectAdmin, etc.). Run this script every %s minutes.' sprintf=['1'] mod='newsletterpro'}</p>
		<div id="task-more-info" style="display: none;">
			<div class="clear" style="height: 5px;"></div>
			<p>
				{l s='Cron jobs allow you to automate certain commands or scripts on your site. You can set a command or script to run at a specific time every day, week, etc.' mod='newsletterpro'}
			</p>
			<p>
				<span style="color: red;">{l s='Warning:' mod='newsletterpro'}</span> {l s='A good knowledge of Linux commands may be necessary before you can use cron jobs effectively. Check your script with your hosting administrator before adding a cron job.' mod='newsletterpro'}
			</p>
			<div class="clear" style="height: 5px;"></div>
			<p>{l s='If your server sends you a CRON "ERROR 406: Not Acceptable", add an htaccess file in the module containing the following information:' mod='newsletterpro'}</p>
			<span>
				&lt;IfModule mod_security.c&gt;<br>SecFilterEngine Off<br>SecFilterScanPOST Off<br>&lt;/IfModule&gt;
			</span>
		</div>
		<a id="task-more-info-button" class="pull-right" href="javascript:{}" style="height: 8px; overflow: visible;">{l s='more info' mod='newsletterpro'}</a>
		<div class="clear"></div>
	</div>
</div>