<div class="productVariants">
	     <ul class="productVariantsList">
	     {foreach from=$productVariants item=productVariant}
	      <li bigImage="{$link->getImageLink($productVariant.link_rewrite, $productVariant.id_image, 'home_default')}" id="variant_{$productVariant.product_id}">
		<a href="{$link->getProductLink($productVariant.product_id, $productVariant.link_rewrite, $productVariant.category_link)}{$preselectedSizeAdd}">
		  <img src="{$link->getImageLink($productVariant.link_rewrite, $productVariant.id_image, 'pr_details_thumb')}"  height="{$variantImageSize.height}" width="{$variantImageSize.width}" alt="{$productVariant.name|htmlspecialchars}" />
		</a>
	      </li>
	     {/foreach}
	     </ul>
	  </div>
	  <div class="spvCatOtherBigImage"><img class="img-responsive" itemprop="image" title="{$productVariant.name|htmlspecialchars}" src="{$link->getImageLink($productVariants.0.link_rewrite, $productVariants.0.id_image, 'home_default')}"></div>