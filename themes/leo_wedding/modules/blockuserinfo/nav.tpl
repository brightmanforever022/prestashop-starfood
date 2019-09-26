
<!-- Block user information module NAV  -->
<div class="displaynav">
		{if $is_logged}
	<div class="header_info pull-left" style="display:none;">
		<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow">
			<i class="fa fa-user"></i>
			<span>{l s='Hello' mod='blockuserinfo'}, {$cookie->customer_firstname} {$cookie->customer_lastname}</span></a>
	</div>
	{else}
		<div class="header_info pull-left hidden-sm hidden-xs hidden-sp">{l s='Welcome visitor you can ' mod='blockuserinfo'}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='login' mod='blockuserinfo'}</a>{l s=' or ' mod='blockuserinfo'}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='create an account' mod='blockuserinfo'}</a></div>
	{/if}
	<div class="header_user_info pull-right e-scale popup-over">
		<div class="popup-title"><i class="fa fa-user"></i>{l s='Account' mod='blockuserinfo'}</div>	
		<div class="popup-content">
			<ul class="links-block">
				{if $is_logged}
					<li class="first">
						<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow">
							<span>{l s='Hello' mod='blockuserinfo'}, {$cookie->customer_firstname} {$cookie->customer_lastname}</span>
						</a>
					</li>
				{/if}

				{if $is_logged}
					<li><a class="logout" href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Log me out' mod='blockuserinfo'}">
						{l s='Sign out' mod='blockuserinfo'}
					</a></li>
				{else}
					<li class="first"><a class="login" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Login to your customer account' mod='blockuserinfo'}">
						{l s='Sign in' mod='blockuserinfo'}
					</a></li>
				{/if}  

				<li>
					<a id="wishlist-total" href="{$link->getModuleLink('blockwishlist', 'mywishlist', array(), true)|addslashes}" title="{l s='My wishlists' mod='blockuserinfo'}">
					{l s='Wish List' mod='blockuserinfo'}</a>
				</li>

				<li>
					<a href="{$link->getPageLink('products-comparison')|escape:'html':'UTF-8'}" title="{l s='Compare' mod='blockuserinfo'}" rel="nofollow">
						{l s='Compare' mod='blockuserinfo'}
					</a>
				</li>
			</ul>
		</div>
	</div>	
</div>