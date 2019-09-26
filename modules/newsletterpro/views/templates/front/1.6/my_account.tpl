{*
* 2013-2015 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright 2013-2015 Ovidiu Cimpean
* @version   Release: 4
*}

<div id="newsletterpro-my-account" class="newsletterpro-my-account">
	{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'quotes':'UTF-8'}">{l s='My account' mod='newsletterpro'}</a><span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>{l s='Newsletter Pro Settings' mod='newsletterpro'}{/capture}
	{include file="$tpl_dir./errors.tpl"}

	<div class="box">
		<h1 class="page-heading">{l s='Newsletter Pro Settings' mod='newsletterpro'}</h1>
		<h2>{l s='Set up your newsletter preferences.' mod='newsletterpro'}</h2>

		<div>
			<form action="{$my_account_url|escape:'quotes':'UTF-8'}" method="post" class="std">
				<div class="checkbox">
					<input type="checkbox" id="newsletter" name="newsletter" value="1" {if $is_subscribed == 1} checked="checked" {/if} autocomplete="off">
					<label for="newsletter">{l s='Sign up for our newsletter!' mod='newsletterpro'}</label>
				</div>

				<div class="clearfix">	
					{include file="$tpl_location"|cat:"templates/front/list_of_interests.tpl"}
				</div>
				
				{if $subscribe_by_category_active}
				<div class="clearfix">
					<h2>{l s='Choose your categories of interest:' mod='newsletterpro'}</h2>

					<div id="category-tree" class="category-tree">
						{* HTML CONTENT *}
						{$category_tree|strval}
					</div>
				</div>
				{/if}

				<div class="clearfix submit">
	                <button id="submitNewsletterProSettings"class="btn btn-default button button-medium"type="submit"name="submitNewsletterProSettings">
	                	<span>{l s='Save' mod='newsletterpro'}<i class="icon icon-chevron-right right"></i></span>
	                </button>
				</div>
			</form>
		</div>
	</div>

	<ul class="footer_links clearfix">
		<li>
			<a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'quotes':'UTF-8'}">
				<span>
					<i class="icon icon-chevron-left"></i> {l s='Back to Your Account' mod='newsletterpro'}
				</span>
			</a>
		</li>
		<li>
			<a class="btn btn-default button button-small" href="{$base_dir|escape:'quotes':'UTF-8'}">
				<span>
					<i class="icon icon-chevron-left"></i> {l s='Home' mod='newsletterpro'}
				</span>
			</a>
		</li>
	</ul>
</div>