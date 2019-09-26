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

<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '{$GOOGLE_ANALYTICS_ID|escape:'html':'UTF-8'}']);
	{if $CAMPAIGN_ACTIVE == true} _gaq.push(['_setCampaignTrack', true]); {else} _gaq.push(['_setCampaignTrack', false]); {/if}
	{if isset($utm_source)} _gaq.push(['_setCampSourceKey', '{$utm_source|escape:'quotes':'UTF-8'}']); {/if}
	{if isset($utm_medium)} _gaq.push(['_setCampMediumKey', '{$utm_medium|escape:'quotes':'UTF-8'}']); {/if}
	{if isset($utm_campaign)} _gaq.push(['_setCampNameKey', '{$utm_campaign|escape:'quotes':'UTF-8'}']); {/if}
	{if isset($utm_content)} _gaq.push(['_setCampContentKey', '{$utm_content|escape:'quotes':'UTF-8'}']); {/if}
	_gaq.push(['_trackPageview']);
	{literal}
	(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
	{/literal}
</script>
