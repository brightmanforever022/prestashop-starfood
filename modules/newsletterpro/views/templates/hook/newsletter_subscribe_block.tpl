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

<!-- Newsletter Pro Subscribe Footer-->
<section class="footer-block col-xs-12 col-sm-6 clearfix newsletter_pro_subscribe_block {if isset($display_hook)} {$display_hook|escape:'html':'UTF-8'} {/if} np-footer-section-sm">
	<h4>{l s='Newsletter' mod='newsletterpro'}</h4>
	<div id="newsletter_pro_subscribe_block" class="col-sm-8 category_footer toggle-footer">
		<div class="block_content">
			<div class="form-group np-input-email clearfix">
				<input id="np-email" class="inputNew form-control grey newsletter-input np-email" type="text" name="email" size="18" value="{l s='Enter your e-mail' mod='newsletterpro'}">
	            <a href="javascript:{}" id="newsletterpro-subscribe-button-popup" name="newsletterProSubscribe" class="newsletterpro-subscribe-button-popup">
	            	<i class="fa fa-chevron-circle-right icon icon-chevron-circle-right"></i>
	            </a>
				<input type="hidden" name="action" value="0">
			</div>
		</div>
	</div>
</section>
<!-- /Newsletter Pro Subscribe Footer -->