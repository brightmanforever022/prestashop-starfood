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

{if $customer_subscribe_by_loi_active}
<div class="newsletterpro-list-of-interests">
	<h2>{l s='Are you interested in:' mod='newsletterpro'}</h2>
	<div class="clearfix">
		<ul>
		{foreach $list_of_interest as $list}
			<li>
				<div class="checkbox">
					<label class="control-label">
						<input type="checkbox" class="input-group" name="list_of_interest[]" value="{$list.id_newsletter_pro_list_of_interest|intval}" {if $list.checked} checked="checked" {/if}>
						{$list.name|escape:'html':'UTF-8'}
					</label>
				</div>
			</li>
		{/foreach}
		</ul>
	</div>
</div>
{/if}