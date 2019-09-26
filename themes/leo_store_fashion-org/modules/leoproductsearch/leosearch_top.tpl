{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!-- Block search module -->
<div id="leo_search_block_top" class="col-lg-7 col-md-6 col-sm-8 col-xs-12 exclusive">
	<form method="get" action="{$link->getPageLink('productsearch', true)|escape:'html':'UTF-8'}" id="leosearchtopbox">
		<input type="hidden" name="fc" value="module" />
		<input type="hidden" name="module" value="leoproductsearch" />
		<input type="hidden" name="controller" value="productsearch" />
		<input type="hidden" name="orderby" value="position" />
		<input type="hidden" name="orderway" value="desc" />	
		<div class="clearfix">
			<div class="category_filter">	
				<select class="col-md-3 col-sm-4 col-xs-6" name="cate" id="cate">
					<option value="">{l s='All Categories' mod='leoproductsearch'}</option>
					 {foreach $cates item = cate key= "key"}
					 <option value="{$cate.id_category|escape:'htmlall':'UTF-8'|stripslashes}" {if isset($selectedCate) && $cate.id_category eq $selectedCate}selected{/if} >{$cate.name}</option>
					 {/foreach}
				</select>
				<span class="fa fa-angle-down"></span>
			</div>
			<input class="search_query col-md-9 col-sm-8 col-xs-6 grey" type="text" placeholder="{l s='Search' mod='leoproductsearch'}" id="leo_search_query_top" name="search_query" value="{$search_query|escape:'htmlall':'UTF-8'|stripslashes}" />
			<button type="submit" id="leo_search_top_button" class="btn btn-outline-inverse button button-small"><i class="fa fa-search"></i></button> 
		</div>
	</form>
</div>
<!-- /Block search module -->
