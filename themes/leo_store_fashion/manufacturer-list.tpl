
{capture name=path}{l s='Brands'}{/capture}

<h1 class="page-heading product-listing">
	{l s='Brands'}
</h1>


{if isset($errors) AND $errors}
	{include file="$tpl_dir./errors.tpl"}
{/if}

<div class="manufacturers-abc-links">
{foreach $manufacturers_abc as $letter => $manufacturers}
	<a href="#Brands-{$letter}" class="btn btn-outline btn-sm">{$letter}</a>
{/foreach}
</div>

<div class="manufacturers-abc">
{foreach $manufacturers_abc as $letter => $manufacturers}
	<h2 id="Brands-{$letter}" >{$letter}</h2>
	<div class="row">
	{foreach $manufacturers as $manufacturer}
		<div class="col-lg-4 col-sm-12">
			<div class="manufacturer-abc-item">
			<a class="lnk_img"
				href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'html':'UTF-8'}"
				title="{$manufacturer.name|escape:'html':'UTF-8'}" >
			<img src="{$img_manu_dir}{$manufacturer.image|escape:'html':'UTF-8'}-medium_default.jpg" alt="" />
			</a>
			</div>
		</div>
	{/foreach}
	</div>
{/foreach}
</div>