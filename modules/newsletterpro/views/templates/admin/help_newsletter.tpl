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
<div class="newsletter-pro-help-box">
	<p>{ldelim}default_shop_url{rdelim} - {l s='Display the main url of your shop' mod='newsletterpro'}</p>
	<p>{ldelim}shop_url{rdelim} - {l s='Display the main url of you shop or if the current user is registered on multistore it will display the multistore url' mod='newsletterpro'}</p>
	<p>{ldelim}shop_logo_url{rdelim} - {l s='Display the link of the image logo of your default shop' mod='newsletterpro'}</p>
	<p>{ldelim}shop_logo{rdelim} - {l s='Display the image logo of your shop or if the current user is registered on multistore it will display the current multistore logo' mod='newsletterpro'}</p>
	<p>{ldelim}active{rdelim} - {l s='Return true if the current user is subscribed at the newsletter' mod='newsletterpro'}</p>
	<p>*|SUBSCRIBED|* - {l s='Return true if the current user is subscribed at the newsletter' mod='newsletterpro'}</p>
	<p>{ldelim}email{rdelim} - {l s='Display current user email address' mod='newsletterpro'}</p>
	<p>*|EMAIL|* - {l s='Display current user email address' mod='newsletterpro'}</p>
	<p>{ldelim}user_type{rdelim} - {l s='Return the type of the current user' mod='newsletterpro'}</p>
	<br />
	<div class="npro-hint">
	<p>{l s='The user_type it can have the values \'customer\', \'registred\', \'registred_np\', \'added\' or \'employee\'' mod='newsletterpro'}</p>
	<p>{l s='Only customers have the {firstname} and {lastname} variables, if the current user that you send the newsletter dose not have those variables you can use a conditional statement like:' mod='newsletterpro'}</p>
	<p>{ldelim}if user_type=='customer'{rdelim}{l s='Hi' mod='newsletterpro'} {ldelim}firstname{rdelim} {ldelim}lastname{rdelim} {ldelim}/if{rdelim}</p>
	<p>{l s='or you can use:' mod='newsletterpro'}</p>
	<br />
	<p>{l s='If the forward feature is active and the recipient received the forwarded newsletter you can use this statement to inform him:' mod='newsletterpro'}</p>
	<p>{ldelim}if isset(variable_name){rdelim} {l s='Hi' mod='newsletterpro'} {ldelim}variable_name{rdelim} {ldelim}/if{rdelim}</p>
	<p>{ldelim}if is_forwarder==1{rdelim}{l s='Your friend with the email address {forwarder_email} has forwarded this newsletter! If you no loger want to receive this newsletter you can {unsubscribe}.' mod='newsletterpro'}{ldelim}/if{rdelim}</p>
	</div>
	<br />
	<p>{ldelim}firstname{rdelim} - {l s='Display the current customer Firstname' mod='newsletterpro'}</p>
	<p>*|FNAME|* - {l s='Display the current customer Firstname' mod='newsletterpro'}</p>
	<p>{ldelim}lastname{rdelim} - {l s='Display the current customer Lastname' mod='newsletterpro'}</p>
	<p>*|LNAME|* - {l s='Display the current customer Lastname' mod='newsletterpro'}</p>
	<p>{ldelim}id{rdelim} - {l s='Return the id of the current customers' mod='newsletterpro'}</p>
	<p>{ldelim}id_default_group{rdelim} - {l s='Return the default registration group id of the current user' mod='newsletterpro'}</p>
	<p>{ldelim}id_lang{rdelim} - {l s='Return the language id of the current user ' mod='newsletterpro'}</p>
	<p>{ldelim}id_shop{rdelim} - {l s='Return the shop id that current user is registered' mod='newsletterpro'}</p>
	<p>{ldelim}shop_name{rdelim} - {l s='Display the shop name' mod='newsletterpro'}</p>
	<p>*|SHOP|* - {l s='Display the shop name' mod='newsletterpro'}</p>
	<p>{ldelim}img_path{rdelim} - {l s='Display the link of the current user language flag' mod='newsletterpro'}</p>
	<p>{ldelim}ip{rdelim} - {l s='Display the current user newsletter registration ip' mod='newsletterpro'}</p>
	<p>{ldelim}language{rdelim} - {l s='Display the name of the current user language' mod='newsletterpro'}</p>
	<p>*|LANGUAGE|* - {l s='Display the name of the current user language' mod='newsletterpro'}</p>
	<p>{ldelim}newsletter_date_add{rdelim} - {l s='Display the current user newsletter registration date' mod='newsletterpro'}</p>
	<p>{ldelim}unsubscribe_link{rdelim} - {l s='Display the current user unsubscribe link (the link will unsubscribe the current user and then the page will display a message)' mod='newsletterpro'}</p>
	<p>{ldelim}unsubscribe_link_redirect{rdelim} - {l s='Display the current user unsubscribe link (the link will unsubscribe the current user and then the page will redirect to the index shop url)' mod='newsletterpro'}</p>
	<p>{ldelim}unsubscribe{rdelim} - {l s='Display the current user unsubscribe button (the button link will unsubscribe the current user and then the page will display a message) - the button can be renamed on the module translation panel' mod='newsletterpro'}</p>
	<p>{ldelim}unsubscribe_redirect{rdelim} - {l s='Display the current user unsubscribe button (the button link will unsubscribe the current user and then the page will redirect to the index shop url) - the button can be renamed on the module translation panel' mod='newsletterpro'}</p>
	<p>{ldelim}subscribe_link{rdelim} - {l s='Display the current user subscribe link. By clicking on the link the user can subscribe again to the newsletter.' mod='newsletterpro'}</p>
	<p>{ldelim}subscribe{rdelim} - {l s='Display the current user subscribe button. By clicking on the link the user can subscribe again to the newsletter.' mod='newsletterpro'}</p>
	<p>{ldelim}date{rdelim} - {l s='Display the current date' mod='newsletterpro'}</p>
	<p>{ldelim}date_full{rdelim} - {l s='Display the current date with the current hour' mod='newsletterpro'}</p>
	<p>{ldelim}view_in_browser_link{rdelim} - {l s='Display the link where the customers and visitors view the newsletter in browser.' mod='newsletterpro'}</p>
	<p>{ldelim}view_in_browser{rdelim} - {l s='Display the button where the customers and visitors view the newsletter in browser.' mod='newsletterpro'}</p>
	<p>{ldelim}view_in_browser_link_share{rdelim} - {l s='Display the link where the customers and visitors view the newsletter in browser.' mod='newsletterpro'} {l s='This link is urlencoded.' mod='newsletterpro'}</p>
	<p>{ldelim}view_in_browser_share{rdelim} - {l s='Display the button where the customers and visitors view the newsletter in browser.' mod='newsletterpro'} {l s='This link is urlencoded.' mod='newsletterpro'}</p>
	<p>{ldelim}forward_link{rdelim} - {l s='Display the link where the customers and visitors can manage their forward emails.' mod='newsletterpro'}</p>
	<p>{ldelim}forward{rdelim} - {l s='Display the button where the customers and visitors can manage their forward emails.' mod='newsletterpro'}</p>
	<p>{ldelim}is_forwarder{rdelim} - {l s='Dispaly 1 if the recipient received a forwarded newsletter, else the value will be 0.' mod='newsletterpro'}</p>

	<p>{ldelim}page_index_link{rdelim}</p>
	<p>{ldelim}page_contact_link{rdelim}</p>
	<p>{ldelim}page_new_products{rdelim}</p>
	<p>{ldelim}page_best_sales{rdelim}</p>
	<p>{ldelim}page_sitemap{rdelim}</p>
	<p>{ldelim}page_my_account{rdelim}</p>
	<p>{ldelim}page_my_orders{rdelim}</p>
	<p>{ldelim}page_my_order_slip{rdelim}</p>
	<p>{ldelim}page_my_addresses{rdelim}</p>
	<p>{ldelim}page_my_personal_info{rdelim}</p>
	<p>{ldelim}page_my_vouchers{rdelim}</p>

	<p>{ldelim}module_url{rdelim}</p>
	<p>{ldelim}date_text{rdelim}</p>
	<p>{ldelim}date_year{rdelim}</p>
	<p>{ldelim}domain{rdelim}</p>
	<p>{ldelim}domain_ssl{rdelim}</p>
	<p>{ldelim}date_day{rdelim}</p>
	<p>{ldelim}date_month_text{rdelim}</p>
	<p>{ldelim}shop_email{rdelim}</p>

	{if isset($help_vars)}
	{foreach $help_vars as $var}
		<p>{$var|escape:'html':'UTF-8'}</p>
	{/foreach}
	{/if}
	<br />
	<p style="color:red">Target tag container for products list must have class="np-products-target"</p>
	<div class="npro-hint">
	<p>{l s='If you are a developer you can create new template variables (related to the current user) in the file' mod='newsletterpro'} 'newsletterpro\classes\NewsletterProExtendTemplateVars.php'</p>
	</div>
</div>