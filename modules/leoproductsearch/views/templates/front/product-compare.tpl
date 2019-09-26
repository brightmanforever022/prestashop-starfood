{*
 *  Leo Theme for Prestashop 1.6.x
 *
 * @author    http://www.leotheme.com
 * @copyright Copyright (C) October 2013 LeoThemes.com <@emai:leotheme@gmail.com>
 *               <info@leotheme.com>.All rights reserved.
 * @license   GNU General Public License version 2
*}

{if $comparator_max_item}
	<form method="post" action="{$link->getPageLink('products-comparison')|escape:'html':'UTF-8'}" class="compare-form">
		<button type="submit" class="btn btn-default button button-medium bt_compare bt_compare{if isset($paginationId)}_{$paginationId}{/if}" disabled="disabled">
			<span>{l s='Compare' mod='leoproductsearch'} (<strong class="total-compare-val">{count($compared_products)}</strong>)<i class="icon-chevron-right right"></i></span>
		</button>
		<input type="hidden" name="compare_product_count" class="compare_product_count" value="{count($compared_products)}" />
		<input type="hidden" name="compare_product_list" class="compare_product_list" value="" />
	</form>
	{if !isset($paginationId) || $paginationId == ''}
		{addJsDefL name=min_item}{l s='Please select at least one product' js=1 mod='leoproductsearch'}{/addJsDefL}
		{addJsDefL name=max_item}{l s='You cannot add more than %d product(s) to the product comparison' mod='leoproductsearch' sprintf=$comparator_max_item js=1}{/addJsDefL}
		{addJsDef comparator_max_item=$comparator_max_item}
		{addJsDef comparedProductsIds=$compared_products}
	{/if}
{/if}