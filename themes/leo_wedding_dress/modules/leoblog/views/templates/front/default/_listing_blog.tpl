<div class="blog-item">
	{if $config->get('listing_show_title','1')}
	<h4><a href="{$blog.link}" title="{$blog.title}">{$blog.title}</a></h4>
	{/if}
	<div class="blog-meta">
		{if $config->get('listing_show_author','1')&&!empty($blog.author)}
		<span class="blog-author">
			<span class="fa fa-user"></span>{l s='Posted By:' mod='leoblog'} 
			<a href="{$blog.author_link}" title="{$blog.author}">{$blog.author}</a> 
		</span>
		{/if}
		
		{if $config->get('listing_show_category','1')}
		<span class="blog-cat"> 
			<span class="fa fa-list"></span>{l s='In:' mod='leoblog'} 
			<a href="{$blog.category_link}" title="{$blog.category_title|escape:'html':'UTF-8'}">{$blog.category_title}</a>
		</span>
		{/if}
		
		{if $config->get('listing_show_created','1')}
		<span class="blog-created">
			<span class="fa fa-calendar"></span>{l s='On:' mod='leoblog'} 
			{strtotime($blog.date_add)|date_format:"%B %e, %Y"}
		</span>
		{/if}
		
		{if isset($blog.comment_count)&&$config->get('listing_show_counter','1')}	
		<span class="blog-ctncomment">
			<span class="fa fa-comments-o"></span>{l s='Comment:' mod='leoblog'}
			{$blog.comment_count}
		</span>
		{/if}

		{if $config->get('listing_show_hit','1')}	
		<span class="blog-hit">
			<span class="fa fa-heart"></span>{l s='Hit:' mod='leoblog'} 
			{$blog.hits}
		</span>
		{/if}
	</div>
	{if $blog.image && $config->get('listing_show_image',1)}
	<div class="blog-image">
		<img src="{$blog.preview_url}" title="{$blog.title}"/>
	</div>
	{/if}

	<div class="blog-shortinfo">
		{if $config->get('listing_show_description','1')}
		{$blog.description}
		{/if}
		{if $config->get('listing_show_readmore',1)}
		<p>
			<a href="{$blog.link}" title="{$blog.title|escape:'html':'UTF-8'}" class="btn btn-default">{l s='Read more' mod='leoblog'}</a>
		</p>
		{/if}
	</div>
</div>