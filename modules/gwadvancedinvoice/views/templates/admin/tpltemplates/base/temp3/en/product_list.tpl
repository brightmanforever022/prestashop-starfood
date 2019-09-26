{*
* Do not edit the file if you want to upgrade in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}
{assign var="colCount" value=$contents|@count}
<table class="product" width="100%" cellpadding="5" cellspacing="0">
    <thead>
    	<tr>
            {foreach $titles as $key=>$value}
                <th class="product header small {if ($key+1) == $colCount} total_title_background {/if} {if $align[$key] !=''}  align{$align[$key]|escape:'html':'UTF-8'} {else} alignleft {/if}" width="{$widthtitles[$key]|escape:'html':'UTF-8'}%">{$titles[$key]|escape:'html':'UTF-8'}</th>
            {/foreach}
        </tr>
    </thead>
	<tbody>
    {literal}
    {assign var="colCount" value="{/literal}{$contents|@count|escape:'html':'UTF-8'}{literal}"}
	{foreach $order_details as $order_detail}
		{cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
		<tr class="product {$bgcolor_class}">
    {/literal}
			{foreach $contents  as $key =>$content}
               <td class="product {if ($key+1) == $colCount} totalitembackground {/if} {if $align[$key] !=''} align{$align[$key]|escape:'html':'UTF-8'} {else} alignleft {/if}"  width="{$widthtitles[$key]|escape:'html':'UTF-8'}%">{$content|string_format:{l s='%s' pdf='true' mod='gwadvancedinvoice'}}</td>
            {/foreach}
		</tr>
    {literal}
		{foreach $order_detail.customizedDatas as $customizationPerAddress}
			{foreach $customizationPerAddress as $customizationId => $customization}
				<tr class="product customization_data {$bgcolor_class}">
					{if $colCount > 1}
                    <td class="product center">
						( x{if $customization.quantity == 0}1{else}{$customization.quantity}{/if})
					</td>
                    {/if}
                    <td  class="product" colspan="{if $colCount > 1}{$colCount-1}{/if}">
                        {if $colCount == 1}(x {if $customization.quantity == 0}1{else}{$customization.quantity}{/if}){/if}
						{if isset($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) && count($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) > 0}
							<table style="width: 100%;">
								{foreach $customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_] as $customization_infos}
									<tr>
										<td style="width: 25%;">
											{$customization_infos.name|string_format:{l s='%s:' pdf='true' mod='gwadvancedinvoice'}}
										</td>
										<td>{$customization_infos.value}</td>
									</tr>
								{/foreach}
							</table>
						{/if}

						{if isset($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) && count($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) > 0}
							<table style="width: 100%;">
								<tr>
									<td style="width: 70%;">{l s='image(s):' pdf='true' mod='gwadvancedinvoice'}</td>
									<td>{count($customization.datas[$smarty.const._CUSTOMIZE_FILE_])}</td>
								</tr>
							</table>
						{/if}
					</td>
				</tr>
			{/foreach}
		{/foreach}
	{/foreach}
	{assign var="shipping_discount_tax_incl" value="0"}
	{foreach from=$cart_rules item=cart_rule name="cart_rules_loop"}
		{if $smarty.foreach.cart_rules_loop.first}
		<tr class="discount">
			<th class="header" colspan="{$colCount}">
				{l s='Discounts' pdf='true' mod='gwadvancedinvoice'}
			</th>
		</tr>
		{/if}
		<tr class="discount">
            {if $colCount > 1}
			<td class="white right" colspan="{$colCount - 1}">
				{$cart_rule.name}
			</td>
            {/if}
			<td class="right white">
                {if $colCount == 1}{$cart_rule.name}:{/if}
                {/literal}
                {if isset($discountval) && $discountval == 'include'}
                {literal}
				- {displayPrice currency=$order->id_currency price=$cart_rule.value}
                {/literal}
                {else}
                {literal}
                - {displayPrice currency=$order->id_currency price=$cart_rule.value_tax_excl}
                {/literal}
                {/if}
                {literal}
			</td>
		</tr>
	{/foreach}
    {/literal}
	</tbody>

</table>
