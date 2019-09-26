{*
 *  Leo Theme for Prestashop 1.6.x
 *
 * @author    http://www.leotheme.com
 * @copyright Copyright (C) October 2013 LeoThemes.com <@emai:leotheme@gmail.com>
 *               <info@leotheme.com>.All rights reserved.
 * @license   GNU General Public License version 2
*}

{if !empty($products)}
		{$mproducts=array_chunk($products,$owl_rows)}
		{foreach from=$mproducts item=products name=mypLoop}
			<div class="item {if $smarty.foreach.mypLoop.first}active{/if}">
					<div class="grid">
						{foreach from=$products item=product name=products}
						{if $product@iteration%$columnspage==1&&$columnspage>1}
							<div class="">
						{/if}
							<div class=" ajax_block_product {if $smarty.foreach.products.first}first_item{elseif $smarty.foreach.products.last}last_item{/if}">
								{include file="$tpl_dir./product-item.tpl"}
							</div>
						{if ($product@iteration%$columnspage==0||$smarty.foreach.products.last)&&$columnspage>1}
							</div>
						{/if}
						{/foreach}
					</div>
			</div>
	{/foreach}







{addJsDefL name=min_item}{l s='Please select at least one product' mod='leomanagewidgets' js=1}{/addJsDefL}
{addJsDefL name=max_item}{l s='You cannot add more than %d product(s) to the product comparison' mod='leomanagewidgets' sprintf=$comparator_max_item js=1}{/addJsDefL}
{addJsDef comparator_max_item=$comparator_max_item}
{addJsDef comparedProductsIds=$compared_products}
{/if}