{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if !isset($content_only) || !$content_only}
						{include file="$tpl_dir./layout/{$LEO_LAYOUT_DIRECTION}/footer.tpl"  }

                
                	</div>
				</div>
				
            </section>
<!-- Footer -->
            {if isset($HOOK_BOTTOM) && !empty($HOOK_BOTTOM)}
			
				<section id="bottom">
				<div class="container">	
				{if isset($left_column_size) && isset($right_column_size)}{assign var='cols' value=(12 - $left_column_size - $right_column_size)}{else}{assign var='cols' value=12}{/if}
				<div class="{if {$cols|intval} != 12}row{else}inner{/if}">
					{$HOOK_BOTTOM}
				</div>
				</div>
				</section>
			{/if}
		<!-- Footer -->
			<footer id="footer" class="footer-container">       
				<div class="container"> 
					<div class="inner">	
						{$HOOK_FOOTER}							
					</div> 
				</div>
				{if isset($HOOK_FOOTNAV)&&!empty($HOOK_FOOTNAV)}
				<div class="footer-nav">
					<div class="container">
						<div class="inner">
							{$HOOK_FOOTNAV}
						</div>
					</div>
				</div>
				{/if}
            </footer>
		</section><!-- #page -->
{/if}
<p id="back-top">
<a href="#top" title="{l s='Scroll To Top'}"></a>
</p>
{include file="$tpl_dir./global.tpl"}
{if isset($LEO_PANELTOOL) && $LEO_PANELTOOL}
    {include file="$tpl_dir./info/paneltool.tpl"}
{/if}
	</body>
</html>