{*
* Do not edit the file if you want to upgrade in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

{if $version == 160}
{if $tax_exempt || ((isset($product_tax_breakdown) && $product_tax_breakdown|@count > 0) || (isset($ecotax_tax_breakdown) && $ecotax_tax_breakdown|@count > 0))}
{if $tax_exempt}
{l s='Exempt of VAT according section 259B of the General Tax Code.' mod='gwadvancedinvoice'}
{else}<table id="tax-tab" width="100%" cellpadding="5" cellspacing="0">
		<thead>
        <tr style="line-height:5px;">
			<th class="header small">{l s='Tax Detail' mod='gwadvancedinvoice'}</th>
			<th class="header small">{l s='Tax Rate' mod='gwadvancedinvoice'}</th>
			{if !$use_one_after_another_method}
				<th class="header small">{l s='Total Tax Excl' mod='gwadvancedinvoice'}</th>
			{/if}
			<th class="header-right small">{l s='Total Tax' mod='gwadvancedinvoice'}</th>
		</tr>
        </thead>
        <tbody>
		{if isset($product_tax_breakdown)}
			{foreach $product_tax_breakdown as $rate => $product_tax_infos}
            {cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
			<tr class="product {$bgcolor_class|escape:'htmlall':'UTF-8'}" style="line-height:6px;">
			 <td>
				{if !isset($pdf_product_tax_written)}
					{l s='Products' mod='gwadvancedinvoice'}
					{assign var=pdf_product_tax_written value=1}
				{/if}
			</td>
			 <td style="text-align: right;">{$rate|escape:'htmlall':'UTF-8'} %</td>
			{if !$use_one_after_another_method}
			 <td style=" text-align: right;">
				 {if isset($is_order_slip) && $is_order_slip}- {/if}{displayPrice currency=$order->id_currency price=$product_tax_infos.total_price_tax_excl}
			 </td>
			{/if}
			 <td style="text-align: right;">{if isset($is_order_slip) && $is_order_slip}- {/if}{displayPrice currency=$order->id_currency price=$product_tax_infos.total_amount}</td>
			</tr>
			{/foreach}
			{/if}

			{if isset($shipping_tax_breakdown)}
			{foreach $shipping_tax_breakdown as $shipping_tax_infos}
            {cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
			<tr class="product {$bgcolor_class|escape:'htmlall':'UTF-8'}" style="line-height:6px;">
			 <td>
				{if !isset($pdf_shipping_tax_written)}
					{l s='Shipping' mod='gwadvancedinvoice'}
					{assign var=pdf_shipping_tax_written value=1}
				{/if}
			 </td>
			 <td style=" text-align: right;">{$shipping_tax_infos.rate|escape:'htmlall':'UTF-8'} %</td>
			{if !$use_one_after_another_method}
				 <td style=" text-align: right;">{if isset($is_order_slip) && $is_order_slip}- {/if}{displayPrice currency=$order->id_currency price=$shipping_tax_infos.total_tax_excl}</td>
			{/if}
			 <td style=" text-align: right;">{if isset($is_order_slip) && $is_order_slip}- {/if}{displayPrice currency=$order->id_currency price=$shipping_tax_infos.total_amount}</td>
			</tr>
			{/foreach}
		{/if}

		{if isset($ecotax_tax_breakdown)}
			{foreach $ecotax_tax_breakdown as $ecotax_tax_infos}
				{if $ecotax_tax_infos.ecotax_tax_excl > 0}
                {cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
				<tr class="product {$bgcolor_class|escape:'htmlall':'UTF-8'}" style="line-height:6px;">
					<td>{l s='Ecotax' mod='gwadvancedinvoice'}</td>
					<td style="text-align: right;">{$ecotax_tax_infos.rate|escape:'htmlall':'UTF-8'} %</td>
					{if !$use_one_after_another_method}
						<td style=" text-align: right;">{if isset($is_order_slip) && $is_order_slip}- {/if}{displayPrice currency=$order->id_currency price=$ecotax_tax_infos.ecotax_tax_excl}</td>
					{/if}
					<td style=" text-align: right;">{if isset($is_order_slip) && $is_order_slip}- {/if}{displayPrice currency=$order->id_currency price=($ecotax_tax_infos.ecotax_tax_incl - $ecotax_tax_infos.ecotax_tax_excl)}</td>
				</tr>
				{/if}
			{/foreach}
		{/if}
        </tbody>
	</table>
	{/if}
    <!--  / TAX DETAILS -->
    {/if}
{else}
    {if $tax_exempt}
    
    	{l s='Exempt of VAT according to section 259B of the General Tax Code.' mod='gwadvancedinvoice'}
    
    {elseif (isset($tax_breakdowns) && $tax_breakdowns)}
    	<table id="tax-tab" width="100%" cellpadding="5" cellspacing="0">
    		<thead>
    			<tr>
    				<th class="header small">{l s='Tax Detail' mod='gwadvancedinvoice'}</th>
    				<th class="header small">{l s='Tax Rate' mod='gwadvancedinvoice'}</th>
    				{if $display_tax_bases_in_breakdowns}
    					<th class="header small">{l s='Base price' mod='gwadvancedinvoice'}</th>
    				{/if}
    				<th class="header-right small">{l s='Total Tax' mod='gwadvancedinvoice'}</th>
    			</tr>
    		</thead>
    		<tbody>
    		{assign var=has_line value=false}
    		{foreach $tax_breakdowns as $label => $bd}
    			{assign var=label_printed value=false}
    			{foreach $bd as $line}
    				{if $line.rate == 0}
    					{continue}
    				{/if}
    				{assign var=has_line value=true}
                    {cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
    				<tr class="{$bgcolor_class|escape:'htmlall':'UTF-8'}" >
    					<td class="white">
    						{if !$label_printed}
    							{if $label == 'product_tax'}
    								{l s='Products' mod='gwadvancedinvoice'}
    							{elseif $label == 'shipping_tax'}
    								{l s='Shipping' mod='gwadvancedinvoice'}
    							{elseif $label == 'ecotax_tax'}
    								{l s='Ecotax' mod='gwadvancedinvoice'}
    							{elseif $label == 'wrapping_tax'}
    								{l s='Wrapping' mod='gwadvancedinvoice'}
    							{/if}
    							{assign var=label_printed value=true}
    						{/if}
    					</td>
    
    					<td class="center white">
    						{$line.rate|escape:'htmlall':'UTF-8'} %
    					</td>
    
    					{if $display_tax_bases_in_breakdowns}
    						<td class="right white">
    							{if isset($is_order_slip) && $is_order_slip}- {/if}
    							{displayPrice currency=$order->id_currency price=$line.total_tax_excl}
    						</td>
    					{/if}
    
    					<td class="right white">
    						{if isset($is_order_slip) && $is_order_slip}- {/if}
    						{displayPrice currency=$order->id_currency price=$line.total_amount}
    					</td>
    				</tr>
    			{/foreach}
    		{/foreach}
    
    		{if !$has_line}
    		<tr>
    			<td class="white center" colspan="{if $display_tax_bases_in_breakdowns}4{else}3{/if}">
    				{l s='No taxes' mod='gwadvancedinvoice'}
    			</td>
    		</tr>
    		{/if}
    
    		</tbody>
    	</table>
    
    {/if}
{/if}