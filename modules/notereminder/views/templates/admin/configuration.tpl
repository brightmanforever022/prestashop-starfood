<div class="panel">
<div class="panel-body">
<table class="table">
	<tr>
		<th>{l s='Order' mod='notereminder'}</th>
		<th>{l s='Date' mod='notereminder'}</th>
		<th>{l s='Message' mod='notereminder'}</th>
	</tr>
	{foreach $reminders as $reminder}
	<tr>
		<td><a href="{$reminder['order_link']}">{$reminder['id_order']}</a></td>
		<td>{$reminder['remind_date']}</td>
		<td>{$reminder['message']}</td>
	</tr>
	{/foreach}
</table>
</div>
</div>