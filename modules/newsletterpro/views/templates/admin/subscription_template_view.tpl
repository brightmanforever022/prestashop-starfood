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

<!DOCTYPE html>
<html>
<head>
	<title>{l s='Front Subscription View' mod='newsletterpro'}</title>
	{* HTML CONTENT *}
	{$head|strval}
</head>
<body>
	{include file="$tpl_location"|cat:"templates/hook/newsletter_subscribe.tpl" render_template=$render_template class_name="in-preview" subscription_template_front_info=$subscription_template_front_info}
</body>
</html>