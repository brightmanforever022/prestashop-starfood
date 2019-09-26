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
	<p>{l s='Those variables are available for all the subscription templates located in the tabs "Edit Template" and "Edit Messages".' mod='newsletterpro'}</p>
	<br>
	<p>{ldelim}shop_name{rdelim} - {l s='Display the current shop name.' mod='newsletterpro'}</p>

	<p>{ldelim}voucher{rdelim} - {l s='Display the template voucher if exists.' mod='newsletterpro'}</p>
	<p>{ldelim}voucher_name{rdelim} - {l s='Display the voucher name.' mod='newsletterpro'}</p>
	<p>{ldelim}voucher_quantity{rdelim} - {l s='Display the voucher quantity.' mod='newsletterpro'}</p>
	<p>{ldelim}voucher_value{rdelim} - {l s='Display the voucher volue in percent or amount.' mod='newsletterpro'}</p>

	<p>{ldelim}shop_url{rdelim} - {l s='Display for shop url.' mod='newsletterpro'}</p>
	<p>{ldelim}shop_name{rdelim} - {l s='Display the shop name.' mod='newsletterpro'}</p>
	<p>{ldelim}shop_logo_url{rdelim} - {l s='Display the shop logo url.' mod='newsletterpro'}</p>
	<p>{ldelim}shop_logo{rdelim} - {l s='Display the shop logo url.' mod='newsletterpro'}</p>

	<p>{ldelim}module_url{rdelim} - {l s='Display the shop url.' mod='newsletterpro'}</p>
	<p>{ldelim}module_path{rdelim} - {l s='Display the shop path.' mod='newsletterpro'}</p>
	<br>
	<p>{ldelim}close_forever_onclick_function{rdelim} - {l s='Display a javascript function that can be used for the html tag attribute onclick. The function will close the popup window and save a cookie. If the client will click on this button the popup window will not appear until the cookie expire.' mod='newsletterpro'}</p>
	<br>
	<p>{ldelim}close_forever{rdelim} - {l s='Display a button that will close the popup window and save a cookie. If the client will click on this button the popup window will not appear until the cookie expire.' mod='newsletterpro'}</p>
	<div class="npro-hint">
	<pre class="help-scrollbar">{$close_forever|escape:'htmlall':'UTF-8'}</pre>
	</div>

	<br>
	<p>{ldelim}displayInfo{rdelim} - {l s='Display the registration at the newsletter status.' mod='newsletterpro'}</p>
	<div class="npro-hint">
	<pre class="help-scrollbar">{$displayInfo|escape:'htmlall':'UTF-8'}</pre>
	</div>

	<br>
	<p>{ldelim}displayGender{rdelim} - {l s='Display the gender options.' mod='newsletterpro'}</p>
	<div class="npro-hint">
	<pre class="help-scrollbar">{$displayGender|escape:'htmlall':'UTF-8'}</pre>
	</div>
	<br>

	<p>{ldelim}displayFirstName{rdelim} - {l s='Display the firstname input.' mod='newsletterpro'}</p>
	<div class="npro-hint">
	<pre class="help-scrollbar">{$displayFirstName|escape:'htmlall':'UTF-8'}</pre>
	</div>
	<br>

	<p>{ldelim}displayLastName{rdelim} - {l s='Display the lastname input.' mod='newsletterpro'}</p>
	<div class="npro-hint">
	<pre class="help-scrollbar">{$displayLastName|escape:'htmlall':'UTF-8'}</pre>
	</div>
	<br>

	<p>{ldelim}displayEmail{rdelim} - {l s='Display the email input.' mod='newsletterpro'}</p>
	<div class="npro-hint">
	<pre class="help-scrollbar">{$displayEmail|escape:'htmlall':'UTF-8'}</pre>
	</div>
	<br>

	<p>{ldelim}displayTermsAndConditionsLink{rdelim} - {l s='Display the email input.' mod='newsletterpro'}</p>
	<div class="npro-hint">
	<pre class="help-scrollbar">{$displayTermsAndConditionsLink|escape:'htmlall':'UTF-8'}</pre>
	</div>
	<br>

	<p>{ldelim}displayTermsAndConditionsCheckbox{rdelim} - {l s='Display the email input.' mod='newsletterpro'}</p>
	<div class="npro-hint">
	<pre class="help-scrollbar">{$displayTermsAndConditionsCheckbox|escape:'htmlall':'UTF-8'}</pre>
	</div>
	<br>

	<p>{ldelim}displayTermsAndConditionsFull{rdelim} - {l s='Display the email input.' mod='newsletterpro'}</p>
	<div class="npro-hint">
	<pre class="help-scrollbar">{$displayTermsAndConditionsFull|escape:'htmlall':'UTF-8'}</pre>
	</div>
	<br>

	<p>{ldelim}displayLanguages{rdelim} - {l s='Display the languages options.' mod='newsletterpro'}</p>
	<div class="npro-hint">
	<pre class="help-scrollbar">{$displayLanguages|escape:'htmlall':'UTF-8'}</pre>
	</div>
	<br>

	<p>{ldelim}displayBirthday{rdelim} - {l s='Display the birthday options.' mod='newsletterpro'}</p>
	<div class="npro-hint">
	<pre class="help-scrollbar">{$displayBirthday|escape:'htmlall':'UTF-8'}</pre>
	</div>
	<br>

	<p>{ldelim}displayListOfInterest{rdelim} - {l s='Display the list of interest options. There are two types available: select options or checkboxes.' mod='newsletterpro'}</p>
	<div class="npro-hint">
	<pre class="help-scrollbar">{$displayListOfInterest|escape:'htmlall':'UTF-8'}</pre>
	</div>
	<br>

	<p>{ldelim}submitButton{rdelim} - {l s='Display the submit button.' mod='newsletterpro'}</p>
	<div class="npro-hint">
	<pre class="help-scrollbar">{$submitButton|escape:'htmlall':'UTF-8'}</pre>
	</div>
	<br>
</div>