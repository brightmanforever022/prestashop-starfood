{if !empty($products)}
<div class="carousel slide carousel-fade" id="{$tabname}">
	{if count($products)>$itemsperpage}	 
	 	<a class="carousel-control left" href="#{$tabname}"   data-slide="prev"></a>
		<a class="carousel-control right" href="#{$tabname}"  data-slide="next"></a>
	{/if}

	<div class="carousel-inner">
		{$mproducts=array_chunk($products,$itemsperpage)}
		{foreach from=$mproducts item=products name=mypLoop}
			<div class="item {if $smarty.foreach.mypLoop.first}active{/if}">
				<div class="grid">
					{foreach from=$products item=product name=products}
					{if $product@iteration%$columnspage==1&&$columnspage>1}
						<div class="row">
					{/if}
						<div class="ajax_block_product {if $columnspage == 5}col-md-2-4 col-lg-2-4{else}col-md-{$scolumn}{/if} col-xs-6 col-sp-12 {if $smarty.foreach.products.first}first_item{elseif $smarty.foreach.products.last}last_item{/if}">
							{include file="$tpl_dir./sub/product-item/product-item.tpl"}
						</div>		
					{if ($product@iteration%$columnspage==0||$smarty.foreach.products.last)&&$columnspage>1}
						</div>
					{/if}	
					{/foreach}
				</div>
		    </div>		
	    {/foreach}
	</div>
</div>
{addJsDefL name=min_item}{l s='Please select at least one product' js=1}{/addJsDefL}
{addJsDefL name=max_item}{l s='You cannot add more than %d product(s) to the product comparison' sprintf=$comparator_max_item js=1}{/addJsDefL}
{addJsDef comparator_max_item=$comparator_max_item}
{addJsDef comparedProductsIds=$compared_products}
{/if}