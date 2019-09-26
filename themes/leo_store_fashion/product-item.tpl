{*
	************************
		Creat by leo themes
	*************************
*}
{if !isset($callFromModule) || $callFromModule==0}
{include file="$tpl_dir./layout/setting.tpl"}
{/if}
<div class="product-container text-center product-block" itemscope itemtype="https://schema.org/Product">
	<div class="left-block">
		<div class="product-image-container image">
		   <div class="leo-more-info" data-idproduct="{$product.id_product}"></div>
			<a class="product_img_link"	href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
				<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" itemprop="image" />
				<span class="product-additional" data-idproduct="{$product.id_product}"></span>
			</a>
			
			{if isset($product.new) && $product.new == 1}
				<a class="new-box product-box" href="{$product.link|escape:'html':'UTF-8'}">
					<span class="new-label product-label">{l s='New'}</span>
				</a>
			{/if}
			{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
				<a class="sale-box product-box" href="{$product.link|escape:'html':'UTF-8'}">
					<span class="sale-label product-label">{l s='Sale!'}</span>
				</a>
			{/if}
			{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
			<div class="content_price">
				{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
					{hook h="displayProductPriceBlock" product=$product type='before_price'}
					<span class="price product-price">
						{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
					</span>
					{if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
						{hook h="displayProductPriceBlock" product=$product type="old_price"}
						<span class="old-price product-price">
							{displayWtPrice p=$product.price_without_reduction}
						</span>
						{hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
						{if $product.specific_prices.reduction_type == 'percentage'}
							<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
						{/if}
					{/if}
					{hook h="displayProductPriceBlock" product=$product type="price"}
					{hook h="displayProductPriceBlock" product=$product type="unit_price"}
					{hook h="displayProductPriceBlock" product=$product type='after_price'}
				{/if}
			</div>
			{/if}
			<div class="box-button">
				{if isset($quick_view) && $quick_view}
					<div class="quickview">
						<a class="quick-view" href="{$product.link|escape:'html':'UTF-8'}" rel="{$product.link|escape:'html':'UTF-8'}" title="{l s='Quick view'}" >
							<i class="fa fa-eye"></i>
						</a>
					</div>
				{/if}
				{if $ENABLE_WISHLIST}
					{hook h='displayProductListFunctionalButtons' product=$product}
				{/if}
				{if $page_name !='product'}
					{if isset($comparator_max_item) && $comparator_max_item}
						<div class="compare">
							<a class="add_to_compare compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}" title="{l s='Add to compare'}" >
								<i class="fa fa-refresh"></i>
								<span>{l s='Add to compare'}</span>
							</a>						
						</div>	
					{/if}
				{/if}
				{if $page_name =='product'}
					<div class="view">
						<a itemprop="url" class="lnk_view" href="{$product.link|escape:'html':'UTF-8'}" title="{l s='View'}">
							{if (isset($product.customization_required) && $product.customization_required)}<i class="fa fa-pencil-square-o"></i><span>{l s='Customize'}</span>{else}<i class="fa fa-info"></i><span>{l s='More'}{/if}</span>
						</a>
					</div>
				{/if}
			</div>
		</div>
		{hook h="displayProductDeliveryTime" product=$product}
		{hook h="displayProductPriceBlock" product=$product type="weight"}
		
		<div class="product-manufacturer-image">
			<img src="{$img_manu_dir}{$product.id_manufacturer|escape:'html':'UTF-8'}-small_default.jpg" alt="" />
		</div>
	</div>
	<div class="right-block">
		<div class="product-meta">			
			<h5 itemprop="name" class="name">
				{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
				<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
					{$product.name|truncate:45:'...'|escape:'html':'UTF-8'}
				</a>
			</h5>
			<h6 class="small name-second">
			{if isset($product.name_original)}
				{$product.name_original|truncate:45:'...'|escape:'html':'UTF-8'}
			{/if}
			</h6>
			{if !isset($moduleCalling) || $moduleCalling != "productcategory"}
				{capture name='displayProductListReviews'}{hook h='displayProductListReviews' product=$product}{/capture}
				{if $smarty.capture.displayProductListReviews}
					<div class="hook-reviews">
					{hook h='displayProductListReviews' product=$product}
					</div>
				{/if}
			{/if}
			<div class="product-desc" itemprop="description">
				{*$product.description_short|strip_tags:'UTF-8'|truncate:360:'...'*}
				{if isset($product.country_of_origin)}
				{$product.country_of_origin}<br>
				{/if}
				
				{if isset($product.pieces_per_carton_text)}
				{$product.pieces_per_carton_text}
				{/if}
			</div>
			{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
			<div class="content_price text-right">
				{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
					{hook h="displayProductPriceBlock" product=$product type='before_price'}
					<span class="price product-price">
						{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
					</span>
					{if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
						{hook h="displayProductPriceBlock" product=$product type="old_price"}
						<span class="old-price product-price">
							{displayWtPrice p=$product.price_without_reduction}
						</span>
						{hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
						{if $product.specific_prices.reduction_type == 'percentage'}
							<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
						{/if}
					{/if}
					{hook h="displayProductPriceBlock" product=$product type="price"}
					{hook h="displayProductPriceBlock" product=$product type="unit_price"}
					{hook h="displayProductPriceBlock" product=$product type='after_price'}
				{/if}
			</div>
			{/if}
			{if isset($product.color_list) && $ENABLE_COLOR}
				<div class="color-list-container">{$product.color_list} </div>
			{/if}

			<div class="product-flags">
				{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
					{if isset($product.online_only) && $product.online_only}
						<span class="online_only label label-warning">{l s='Online only'}</span>
					{/if}
				{/if}
				{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
					{elseif isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
						<span class="discount label label-danger">{l s='Reduced price!'}</span>
					{/if}
			</div>

			{if (!$PS_CATALOG_MODE && $PS_STOCK_MANAGEMENT && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
				{if isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
					<span class="availability">
						{if ($product.allow_oosp || $product.quantity > 0)}
							<span class="label{if $product.quantity <= 0 && isset($product.allow_oosp) && !$product.allow_oosp} label-danger{elseif $product.quantity <= 0} out-of-stock label-warning{else} available-now label-success{/if}">
								{if $product.quantity <= 0}{if $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later}{else}{l s='In Stock'}{/if}{else}{l s='Out of stock'}{/if}{else}{if isset($product.available_now) && $product.available_now}{$product.available_now}{else}{l s='In Stock'}{/if}{/if}
							</span>
						{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
							<span class="label-warning label available-dif">
								{l s='Product available with different options'}
							</span>
						{else}
							<span class="label-danger label out-of-stock">
								{l s='Out of stock'}
							</span>
						{/if}
					</span>
				{/if}
			{/if}
		
			<div class="functional-buttons clearfix">				
				<div class="cart">
				{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.customizable != 2 && !$PS_CATALOG_MODE}
					{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
					{capture}add=1&amp;id_product={$product.id_product|intval}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
					<form action="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" method="post" class="product_list_add_to_cart_form">
					<div class="row">
						<div class="col-md-4">
							
						</div>

						<div class="form-group col-md-8">
						<div class="input-group ">
							<span class="input-group-btn">
							<button type="button" data-operation="down" class="btn btn-default">-</button>
							</span>
							<input type="text" name="qty" value="1" class="form-control">
							<span class="input-group-btn">
							<button type="button" data-operation="up" class="btn btn-default">+</button>
							</span>
						</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">

						<button type="submit" class="button ajax_add_to_cart_button btn btn-outline btn-block" title="{l s='Add to cart'}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
							<span>{l s='Add to cart'}</span>
						</button>
						</div>
					</div>
					</form>
						{*
						{capture}add=1&amp;id_product={$product.id_product|intval}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
						<a class="button ajax_add_to_cart_button btn btn-outline btn-sm" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
							<span>{l s='Add to cart'}</span>
						</a>
						*}
					{else}
						<span class="button ajax_add_to_cart_button btn btn-outline btn-sm disabled">
							<span>{l s='Out of stock'}</span>
						</span>
					{/if}
				{/if}
				</div>	
			</div>
			
		</div>
	</div>
</div>
<!-- .product-container> -->

