{**
 *  Leo Prestashop Theme Framework for Prestashop 1.5.x
 *
 * @package   leotempcp
 * @version   3.0
 * @author    http://www.leotheme.com
 * @copyright Copyright (C) October 2013 LeoThemes.com <@emai:leotheme@gmail.com>
 *               <info@leotheme.com>.All rights reserved.
 * @license   GNU General Public License version 2
 *
 **}
<!-- Block languages module -->
<div id="leo_block_top">
	<i class="fa fa-user"></i>
	<i class="fa fa-angle-down"></i>
	{if count($languages) > 1}
		<div id="countries">
			{* @todo fix display current languages, removing the first foreach loop *}
			{l s='Language' mod='blockgrouptop'}
			<ul id="first-languages" class="countries_ul">
			{foreach from=$languages key=k item=language name="languages"}
				<li {if $language.iso_code == $lang_iso}class="selected_language"{/if}>
				{if $language.iso_code != $lang_iso}
					{assign var=indice_lang value=$language.id_lang}
					{if isset($lang_rewrite_urls.$indice_lang)}
						<a href="{$lang_rewrite_urls.$indice_lang|escape:htmlall}" title="{$language.name}">
					{else}
						<a href="{$link->getLanguageLink($language.id_lang)|escape:htmlall}" title="{$language.name}">

					{/if}
				{/if}
						<img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code}" width="16" height="11" />
				{if $language.iso_code != $lang_iso}
					</a>
				{/if}
				</li>
			{/foreach}
			</ul>
		</div>
		<script type="text/javascript">
			$(document).ready(function () {
				$('ul#first-currencies li:not(.selected)').css('opacity', 0.3);
				$('ul#first-currencies li:not(.selected)').hover(function(){
					$(this).css('opacity', 1);
				}, function(){
					$(this).css('opacity', 0.3);
				});
			});
		</script>
	{/if}
	{if !$catalog_mode}
	<form id="setCurrency" action="{$request_uri}" method="post">
		<p>
			<input type="hidden" name="id_currency" id="id_currency" value=""/>
			<input type="hidden" name="SubmitCurrency" value="" />
			{l s='Currency' mod='blockgrouptop'}
		</p>
		<ul id="first-currencies" class="currencies_ul">
			{foreach from=$currencies key=k item=f_currency}
				<li {if $cookie->id_currency == $f_currency.id_currency}class="selected"{/if}>
					<a href="javascript:setCurrency({$f_currency.id_currency});" title="{$f_currency.name}" rel="nofollow">{$f_currency.sign}</a>
				</li>
			{/foreach}
		</ul>
	</form>
	<script type="text/javascript">
		$(document).ready(function () {
			$("#setCurrency").mouseover(function(){
				$(this).addClass("countries_hover");
				$(".currencies_ul").addClass("currencies_ul_hover");
			});
			$("#setCurrency").mouseout(function(){
				$(this).removeClass("countries_hover");
				$(".currencies_ul").removeClass("currencies_ul_hover");
			});
		});
	</script>
	{/if}
</div>
<script type="text/javascript">
$(document).ready(function () {
	$("#countries").mouseover(function(){
		$(this).addClass("countries_hover");
		$(".countries_ul").addClass("countries_ul_hover");
	});
	$("#countries").mouseout(function(){
		$(this).removeClass("countries_hover");
		$(".countries_ul").removeClass("countries_ul_hover");
	});
});
</script>
<!-- /Block languages module -->
