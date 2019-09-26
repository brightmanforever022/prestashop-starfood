
<!-- MODULE Block blockleoblogstabs -->
<div class="block blogs_block exclusive blockleoblogs">
	{*<h4 class="page-subheading">{l s='Latest Blogs' mod='blockleoblogs'}</h4>*}
	<div class="block_content">	
		{if !empty($blogs )}
			<div class="slide row">
				<div class="carousel-inner" id="{$mytab}">
				{$mblogs=array_chunk($blogs,$owl_rows)}
				{foreach from=$mblogs item=blogs name=mypLoop}
					<div class="blog-item {if $smarty.foreach.mypLoop.first}active{/if}">
						<div class="blog-container">
							{foreach from=$blogs item=blog name=blogs}
								{if $blog.image && $config->get('blockleo_blogs_img',1)}
								<div class="image">
									<a href="{$blog.category_link}" title="{$blog.category_title|escape:'html':'UTF-8'}">
										<img src="{$blog.preview_url}" title="{$blog.title}" class="img-responsive" alt="{$blog.title}" />
									</a>
								</div>
								{/if}
								<div class="content clearfix">	
									{if $config->get('blockleo_blogs_title',1)}
									<h5><a href="{$blog.link}" title="{$blog.title}">{$blog.title}</a></h5>
									{/if}
									<div class="blog-meta">
										{if $config->get('blockleo_blogs_aut',1)} 
										<span class="author">
											{$blog.author}&nbsp;{l s='-' module='blockleoblogs'}
										</span>
										{/if}

										{if $config->get('blockleo_blogs_cre',1)} 
										<span class="blog-created">
											{strtotime($blog.date_add)|date_format:"%B %e, %Y"}
										</span>
										{/if}

										{if $config->get('blockleo_blogs_cat',1)}
										<span class="blog-cat"> 
											<span class="fa fa-list"></span>{l s='In' module='blockleoblogs'}&nbsp;
											<a href="{$blog.category_link}" title="{$blog.category_title|escape:'html':'UTF-8'}">{$blog.category_title}</a>
										</span>
										{/if}

										{if $config->get('blockleo_blogs_cout',1)} 
										<span class="blog-ctncomment">
											<span class="fa fa-comments-o"></span>{$blog.comment_count}&nbsp;{l s='Comment' mod='blockleoblogs'}
										</span>
										{/if}  

										{if $config->get('blockleo_blogs_hits',1)} 
										<span class="blog-hits">
											<span class="fa fa-heart"></span>{l s='Hits:' mod='blockleoblogs'}&nbsp;{$blog.hits}
										</span>	
										{/if}
									</div>
									
									{if $config->get('blockleo_blogs_des',1)}
									<div class="blog-shortinfo">
										{$blog.description}
									</div>
									{/if}
								</div> 
							{/foreach}
						</div>
					</div>		
				{/foreach}
				</div>
			</div>
		{/if}
	</div>
	{if $config->get('blockleo_blogs_show',1)}
	<div><a class="pull-right" href="{$view_all_link}" title="{l s='View All' mod='blockleoblogs'}">{l s='View All' mod='blockleoblogs'}</a></div>
	{/if}	
</div>
<!-- /MODULE Block blockleoblogstabs -->
