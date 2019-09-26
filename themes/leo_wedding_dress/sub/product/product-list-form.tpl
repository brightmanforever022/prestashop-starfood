{if $nb_products > $products_per_page && $start!=$stop}
<div class="content_sortPagiBar clearfix">
	<div class="sortPagiBar clearfix row">					
			<div class="col-md-12 col-sm-12 col-xs-12">				
				<div class="sort top_pagi">
                {include file="$tpl_dir./nbr-product-page.tpl"}	
				{include file="$tpl_dir./pagination.tpl" no_follow=1}
												
				</div>
			</div>
			<div class="product-compare col-md-2 col-sm-4 col-xs-6">
				{include file="$tpl_dir./product-compare.tpl"}
			</div>
    </div>
</div>
{/if}
{include file="$tpl_dir./product-list.tpl" products=$products}
{if $nb_products > $products_per_page && $start!=$stop}
<div class="content_sortPagiBar">
	<div class="bottom-pagination-content clearfix row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			{include file="$tpl_dir./pagination.tpl" no_follow=1 paginationId='bottom'}
		</div>
		<div class="product-compare col-md-2 col-sm-4 col-xs-6">
			{include file="$tpl_dir./product-compare.tpl" paginationId='bottom'}
		</div>
	</div>
</div>
{/if}