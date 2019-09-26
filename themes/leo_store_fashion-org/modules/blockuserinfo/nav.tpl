
<!-- Block user information module NAV  -->


<div class="header_user_info topbar-box">
	<div data-toggle="dropdown" class="dropdown-toggle btn-group">{l s='Account' mod='blockuserinfo'}</div>
	<div class="quick-setting dropdown-menu">
		<ul class="list">
			{if $is_logged}
				<li class="header_user_info">
					<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow">
						<span>{l s='Welcome' mod='blockuserinfo'}, {$cookie->customer_firstname} {$cookie->customer_lastname}</span>
					</a>
				</li>
			{/if}
			<li class="first">
				<a id="wishlist-total" href="{$link->getModuleLink('blockwishlist', 'mywishlist', array(), true)|addslashes}" title="{l s='My wishlists' mod='blockuserinfo'}">{l s='Wish List' mod='blockuserinfo'}</a>
			</li>	
			<li>
				<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='My account' mod='blockuserinfo'}">{l s='My Account' mod='blockuserinfo'}</a>
			</li>
			<li>
				<a href="{$link->getPageLink(order, true)|escape:'html':'UTF-8'}" title="{l s='Checkout' mod='blockuserinfo'}" class="last">{l s='Checkout' mod='blockuserinfo'}</a>
			</li>
			<li>
				<a href="{$link->getPageLink(order, true)|escape:'html'}" title="{l s='Shopping Cart' mod='blockuserinfo'}" rel="nofollow">
					{l s='Shopping Cart' mod='blockuserinfo'}
				</a>
			</li>
			{if $is_logged}
				<li>
					<a class="logout" href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Log me out' mod='blockuserinfo'}">{l s='Sign out' mod='blockuserinfo'}</a>
				</li>
			{/if}
		</ul>
	</div>
</div>	