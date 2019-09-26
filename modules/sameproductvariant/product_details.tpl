    <h3 class="spvTitle">{l s='Farbe' mod='sameproductvariant'}</h3>
    <ul class="productDetailVariantsList">
      {foreach from=$productVariants item=productVariant}
      <li {if $productVariant.product_id==$currentProductId}class="currentProduct"{/if}>
	<a href="{$link->getProductLink($productVariant.product_id, $productVariant.link_rewrite, $productVariant.category_link)}{$preselectedSizeAdd}">
	<img src="{$link->getImageLink($productVariant.link_rewrite, $productVariant.id_image, 'cart_default')}"  {*style="height:{$variantImageSize.height}px; width:{$variantImageSize.width}px"*} alt="{$productVariant.name|htmlspecialchars}" title="{$productVariant.name|htmlspecialchars}" />
	</a>
      </li>
      {/foreach}
    </ul>
    <br/>
