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
	<title>{l s='Your friend want to subscribe!' mod='newsletterpro'}</title>
	<style type="text/css">
			.main {
				width: 600px;
				margin: 0 auto;
				background-color: #fff;
				min-height: 100px;
				padding: 10px;
			}

			body { 
				background-color: #F5F5F5;
				font-family: Arial, Helvetica, sans-serif; 
				font-size: 13px; 
				margin: 0; 
				padding: 0;
			}
			table {
				border-collapse: collapse;
				border-spacing: 0;
			}

			table.newsletter-pro-content td {
			    font-size: 13px;
			}

			a.link, 
			a.link:link, 
			a.link:visited { 
				border: none; 
				color: #337ed0; 
				font-size: 12px; 
				font-weight: bold; 
				text-align: center; 
				text-decoration: none;
				cursor: pointer; 
				display: block; 
				line-height: 22px; 
				width: 100%; 
				height: 22px; 
			}

			a img { 
				border: none; border-style: none; 
			}
			h2 {
				margin-bottom: 5px;
				margin-top: 5px;
			}
		</style>
</head>
<body>
	<div class="main">
		<table class="table-content">
			<tr>
				<td><div>{ldelim}shop_logo{rdelim}</div></td>
			</tr>
			<tr>
				<td><h2>{l s='Subscribe at our newsletter!' mod='newsletterpro'}</h2></td>
			</tr>
			<tr>
				<td>
				{l s='You\'re friend with the email address %s sent you a subscribe request at our newsletter.' sprintf=$from_email mod='newsletterpro'}
				</td>
			</tr>
			<tr>
				<td>{l s='You can subscribe by clicking' mod='newsletterpro'} <a href="{ldelim}subscribe_link{rdelim}" style="color: blue;">{l s='here' mod='newsletterpro'}</a>.</td>
			</tr>
			<tr>
				<td><br>{l s='Thank you,' mod='newsletterpro'} <br> {ldelim}shop_name{rdelim}</td>
			</tr>
		</table>
	</div>
</body>
</html>