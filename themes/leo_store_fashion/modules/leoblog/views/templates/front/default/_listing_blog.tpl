<div class="blog-item">
	{if $config->get('listing_show_title','1')}
	<h4 class="name"><a href="{$blog.link|escape:'html':'UTF-8'}" title="{$blog.title}">{$blog.title}</a></h4>
	{/if}
	<div class="blog-meta">
		{if $config->get('listing_show_author','1')&&!empty($blog.author)}
		<span class="blog-author">
			<span class="fa fa-user"> {l s='Posted By' mod='leoblog'}:</span> 
			<a href="{$blog.author_link|escape:'html':'UTF-8'}" title="{$blog.author}">{$blog.author}</a> 
		</span>
		{/if}
		
		{if $config->get('listing_show_category','1')}
		<span class="blog-cat"> 
			<span class="fa fa-list"> {l s='In' mod='leoblog'}:</span> 
			<a href="{$blog.category_link|escape:'html':'UTF-8'}" title="{$blog.category_title|escape:'html':'UTF-8'}">{$blog.category_title}</a>
		</span>
		{/if}
		
		{if $config->get('listing_show_created','1')}
		<span class="blog-created">
			<span class="fa fa-calendar"> {l s='On' mod='leoblog'}: </span> 
			{strtotime($blog.date_add)|date_format:"%A, %B %e, %Y"}
		</span>
		{/if}
		
		{if isset($blog.comment_count)&&$config->get('listing_show_counter','1')}	
		<span class="blog-ctncomment">
			<span class="fa fa-comment"> {l s='Comment' mod='leoblog'}:</span> 
			{$blog.comment_count}
		</span>
		{/if}

		{if $config->get('listing_show_hit','1')}	
		<span class="blog-hit">
			<span class="fa fa-heart"> {l s='Hit' mod='leoblog'}:</span> 
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
			{$blog.description|strip_tags:'UTF-8'|truncate:150:'...'}
		{/if}
		{if $config->get('listing_show_readmore',1)}
			<div class="read-more">
				<a href="{$blog.link}" title="{$blog.title|escape:'html':'UTF-8'}" class="button btn btn-outline">{l s='Read more' mod='leoblog'}</a>
			</div>
		{/if}		
	</div>
	
</div>