
			<header id="header">
			<a class="open-menu-creative" href="#"><span class="line-btn">&nbsp;</span></a>
				<section class="header-container">
					<div class="banner">
						{hook h="displayBanner"}
					</div>
					<div id="topbar">
						<div class="container">
							<div class="inner">
									<nav>{hook h="displayNav"}</nav>
							</div>
						</div>
					</div>
					<div id="header-main" class="header-left">
						<div class="container">
							<div class="inner">
								<!-- <p class="no-margin text-center"><a href="#" class="open-menu-creative"><span></span></a></p> -->
								<div id="header_logo">
									<a href="{$base_dir}" title="{$shop_name|escape:'html':'UTF-8'}">
										<img class="logo img-responsive" src="{$logo_url}" alt="{$shop_name|escape:'html':'UTF-8'}"{if isset($logo_image_width) && $logo_image_width} width="{$logo_image_width}"{/if}{if isset($logo_image_height) && $logo_image_height} height="{$logo_image_height}"{/if}/>
									</a>
								</div>
								{if isset($HOOK_TOP)}{$HOOK_TOP}{/if}
							</div>
						</div>
					</div>
				</section>
			</header>
			{if in_array($page_name,array('index'))}
				<div id="slideshow" class="clearfix">
					<div class="">
						{hook h="displayTopColumn"}
					</div>
				</div>
			{/if}