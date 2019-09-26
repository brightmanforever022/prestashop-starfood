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

{if isset($errors) && !empty($errors)}
<div style="color: red; font-family: Arial, sans-serif; font-size: 12px;">
	{foreach $errors as $error}
		<p>{$error|escape:'quotes':'UTF-8'}</p>	
	{/foreach}
</div>
{else}
<div style="color: red; font-family: Arial, sans-serif; font-size: 12px;">
	<p>{l s='There is an error regards Menu (ps_tab), the (ps_tab_lang) is not found!' mod='newsletterpro'}</p>
	<p>{l s='To solve this error follow the steps:' mod='newsletterpro'}</p>
	<ol>
		<li>{l s='Go the the "Administration  > Menus"' mod='newsletterpro'}</li>
		<li>{l s='Find the row with the "ID" 11 ( this should have the "Name" of "Customers" )' mod='newsletterpro'}</li>
		<li>{l s='Click on the plus sign on the right of the row' mod='newsletterpro'}</li>
		<li>{l s='If at the column "Module" you find the row with "newsletterpro", delete that row.' mod='newsletterpro'}</li>
		<li>{l s='Now you have to add a new menu, click on the button at top of the page "Add new"' mod='newsletterpro'}</li>
		<li>{l s='Fill the fields:' mod='newsletterpro'}</li>
		<li>{l s='Name -> Newsletter Pro' mod='newsletterpro'}</li>
		<li>{l s='Class -> AdminNewsletterPro' mod='newsletterpro'}</li>
		<li>{l s='Module -> newsletterpro' mod='newsletterpro'}</li>
		<li>{l s='Status -> yes' mod='newsletterpro'}</li>
		<li>{l s='Parent -> Customers' mod='newsletterpro'}</li>
	</ol>
	<p>{l s='All done!' mod='newsletterpro'}</p>
</div>
{/if}