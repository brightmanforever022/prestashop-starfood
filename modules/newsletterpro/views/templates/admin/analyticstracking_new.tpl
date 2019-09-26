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

<script>
	(function(i,s,o,g,r,a,m){ldelim}i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){ldelim}
	(i[r].q=i[r].q||[]).push(arguments){rdelim},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	{rdelim})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	{if isset($utm_source)} ga('set', 'campaignSource', '{$utm_source|escape:'quotes':'UTF-8'}'); {/if}
	{if isset($utm_source)} ga('set', 'campaignMedium', '{$utm_medium|escape:'quotes':'UTF-8'}'); {/if}
	{if isset($utm_source)} ga('set', 'campaignName', '{$utm_campaign|escape:'quotes':'UTF-8'}'); {/if}
	{if isset($utm_source)} ga('set', 'campaignContent', '{$utm_content|escape:'quotes':'UTF-8'}'); {/if}

	ga('create', '{$GOOGLE_ANALYTICS_ID|escape:'html':'UTF-8'}', '{$HOST|escape:'html':'UTF-8'}');
	ga('send', 'pageview');
</script>