
{function name=menu level=0}
  <div class="level{$level} col-md-4 col-lg-4 col-sm-4 col-xs-4 col-sp-12">
    {foreach $data as $category}
    {if isset($category.children) && is_array($category.children)}
    <div class="cate_{$category.id_category} cate" >
      {if isset($category.image)}
      <div class="c-image" {if  {$showicons} == 0 || ({$level} gt 0 && {$showicons} == 2)} style="display:none"{/if}>
        <img class="img-responsive" src='{$category["image"]}' alt='{$category["name"]}'>
      </div>
      {/if}
      <a href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)|escape:'html':'UTF-8'}">
        <span {if {$category.id_category} == {$id_root}} style="display:none"{/if}>
          {$category.name}
        </span>          
      </a>
      {menu data=$category.children level=$level+1}
    </div>
    {else}
    <div class="cate_{$category.id_category} cate">
      {if isset($category.image)}
      <div class="c-image" {if {$showicons} == 0 || ({$level} gt 0 && {$showicons} == 2)} style="display:none"{/if}>
        <img class="img-responsive" src='{$category["image"]}' alt='{$category["name"]}'>
      </div>
      {/if}
      <div class="cate-view">
        <a class="cate-link" href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)|escape:'html':'UTF-8'}">
          <span>{$category.name}</span>
        </a>
        {*<a class="btn button button-outline cateview" href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)|escape:'html':'UTF-8'}">
          <i class="fa fa-eye"></i>&nbsp;&nbsp;{l s='View all' mod='leomanagewidgets'}          
        </a>*}
      </div>

    </div>
    {/if}
  {/foreach}
  </div>
{/function}


{if isset($categories)}
<div class="widget-category_image block">
	{if isset($widget_heading)&&!empty($widget_heading)}
	<h4 class="page-subheading">
		{$widget_heading}
	</h4>
	{/if}
	<div class="block_content">
    <div class="row">
      {foreach from = $categories   key=key item =cate}
  			{menu data=$cate}
      {/foreach}
    </div>
    <div id="view_all_wapper" style="display:none">
        <span class ="view_all"><a href="javascript:void(0)">{l s='View all' mod='leomanagewidgets'}</a></span>
    </div> 
	</div>
</div>
{/if}
<script type="text/javascript">
{literal} 
    jQuery(document).ready(function(){      
      var limit = {/literal}{$limit}{literal};
      var level = {/literal}{$cate_depth}{literal};
      $("ul.level0").each(function(){
          var element = $(this).find("ul.level" + level + " >li").length;
          var count = 0;
          $(this).find("ul.level" + level + " >li").each(function(){
            count = count + 1;
            if(count > limit){
              $(this).remove();
            }

          });
          
          if(element > limit){
              view = $(".view_all","#view_all_wapper").clone(1);
              view.appendTo($(this).find("ul.level" + level));
              var href = $(this).find('a:first').attr('href');
              $("a",view).attr("href", href);
          }
    })

});
{/literal}
</script>

