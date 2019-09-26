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
	<h4>{l s='Dynamic Vars' mod='newsletterpro'}</h4>
	<p>{l s='The dynamic vars are render when the newsletter is send and take a different value for each customer.' mod='newsletterpro'}</p>
	<br>
	<p>{l s='Type String' mod='newsletterpro'}</p>
	<p>{ldelim}$name{rdelim} - {l s='Product name' mod='newsletterpro'}</p>
	<p>{ldelim}$currency{rdelim} - {l s='Display currency' mod='newsletterpro'}</p>
	<p>{ldelim}$description{rdelim} - {l s='Full description of product' mod='newsletterpro'}</p>
	<p>{ldelim}$description_short{rdelim} - {l s='Short description of product' mod='newsletterpro'}</p>
	<p>{ldelim}$manufacturer_name{rdelim} - {l s='Manufacturer name' mod='newsletterpro'}</p>
	<br>
	<div class="npro-hint">
	<p>{l s='Example:' mod='newsletterpro'}</p>
	<p>{ldelim}$name|15(...)|en{rdelim} - {l s='This will display the name of the product in English and if the name has the length bigger the 15 characters the name will be trim down at 15 characters and the end of the name will have the value of 3 dots (...).' mod='newsletterpro'}</p>
	<p>{l s='The parameters after the "|" character are optional. The variable $name can be written is 3 different ways:' mod='newsletterpro'}</p>
	<p>{ldelim}$name{rdelim} - {l s='This will display the product name in the customer language. If the newsletter receiver is not a customer and is a visitor he will receive the newsletter in the template selected language.' mod='newsletterpro'}</p> 
	<p>{ldelim}$name|15{rdelim} - {l s='This will display the product name with a length of characters at 15.' mod='newsletterpro'}</p>
	<p>{ldelim}$name|15(...){rdelim} - {l s='This will display the product name with a length of characters at 15 and will have at the end of the name 3 dots (...).' mod='newsletterpro'}</p>
	<p>{ldelim}$name|en{rdelim} - {l s='This will display the product name in English.' mod='newsletterpro'}</p>
	<p>{l s='The languages iso codes available in this shop are: ' mod='newsletterpro'} <span style="font-weight: bold;">{$lang_iso|escape:'html':'UTF-8'}</span></p>
	</div>

	<br>
	<p>{l s='Type Currency' mod='newsletterpro'}</p>
	<p>{ldelim}$currency{rdelim} - {l s='Display currency' mod='newsletterpro'}</p>
	<p>{ldelim}$price_display{rdelim} - {l s='Display converted price with currency' mod='newsletterpro'} <strong>{l s='(recommended)' mod='newsletterpro'}</strong></p>
	<p>{ldelim}$price_convert{rdelim} - {l s='Display converted price' mod='newsletterpro'}</p>
	<p>{ldelim}$price_without_reduction{rdelim}</p>
	<p>{ldelim}$price_without_reduction_convert{rdelim}</p>
	<p>{ldelim}$price_without_reduction_display{rdelim}</p>
	<p>{ldelim}$price_tax_exc{rdelim}</p>
	<p>{ldelim}$price_tax_exc_convert{rdelim}</p>
	<p>{ldelim}$price_tax_exc_display{rdelim}</p>
	<p>{ldelim}$price_tax_exc{rdelim}</p>
	<p>{ldelim}$wholesale_price_convert{rdelim}</p>
	<p>{ldelim}$wholesale_price_display{rdelim}</p>
	<br>
	<div class="npro-hint">
	<p>{l s='Example:' mod='newsletterpro'}</p>
	<p>{ldelim}$price_display{rdelim} - {l s='This will display the converted price with currency. The price will differ depends of the customer. For example if the shop has a different price of a specific group, the price will be different beside the default group.' mod='newsletterpro'} <strong>{l s='(recommended)' mod='newsletterpro'}</strong></p>
	<p>{ldelim}$price_display|EUR{rdelim} - {l s='This is almost the same as the previous example but the price will has the EURO currency.' mod='newsletterpro'}</p>
	<p>{l s='The currencies iso codes available in this shop are: ' mod='newsletterpro'} <span style="font-weight: bold;">{$currencies_iso|escape:'html':'UTF-8'}</span></p>
	</div>
	<br>
	<h4>{l s='Static Vars' mod='newsletterpro'}</h4>
	<p>{ldelim}name{rdelim} - {l s='Product name' mod='newsletterpro'}</p>
	<p>{ldelim}price_display{rdelim} - {l s='Display converted price with currency' mod='newsletterpro'} <strong>{l s='(recommended)' mod='newsletterpro'}</strong></p>
	<p>{ldelim}currency{rdelim} - {l s='Display currency' mod='newsletterpro'}</p>
	<p>{ldelim}price{rdelim} - {l s='Product price (default currency)' mod='newsletterpro'}</p>
	<p>{ldelim}link{rdelim} - {l s='Product url' mod='newsletterpro'}</p>
	<p>{ldelim}reference{rdelim} - {l s='Product reference' mod='newsletterpro'}</p>
	<p>{ldelim}description{rdelim} - {l s='Full description of product' mod='newsletterpro'}</p>
	<p>{ldelim}description_short{rdelim} - {l s='Short description of product' mod='newsletterpro'}</p>
	<p>{ldelim}quantity{rdelim} - {l s='Quantity of product' mod='newsletterpro'}</p>
	<p>{ldelim}wholesale_price{rdelim} - {l s='The wholesale price at which you bought this product' mod='newsletterpro'}</p>
	<p>{ldelim}wholesale_price_display{rdelim}</p>
	<p>{ldelim}width{rdelim} - {l s='Product width' mod='newsletterpro'}</p>
	<p>{ldelim}height{rdelim} - {l s='Product height' mod='newsletterpro'}</p>
	<p>{ldelim}depth{rdelim} - {l s='Product depth' mod='newsletterpro'}</p>
	<p>{ldelim}weight{rdelim} - {l s='Product weight' mod='newsletterpro'}</p>
	<p>{ldelim}link_rewrite{rdelim} - {l s='Rewrite url name' mod='newsletterpro'}</p>
	<p>{ldelim}manufacturer_name{rdelim} - {l s='Manufacturer name' mod='newsletterpro'}</p>
	<p>{ldelim}price_tax_exc{rdelim} - {l s='Product price exclude taxes' mod='newsletterpro'}</p>
	<p>{ldelim}price_without_reduction{rdelim} - {l s='Product price without reduction' mod='newsletterpro'}</p>
	<p>{ldelim}reduction{rdelim} - {l s='Product reduction price' mod='newsletterpro'}</p>
	<p>{ldelim}reduction_display{rdelim}</p>
	<p>{ldelim}discount{rdelim}</p>
	<p>{ldelim}discount_decimals{rdelim}</p>
	<p>{ldelim}price_without_reduction{rdelim}</p>
	<p>{ldelim}price_without_reduction_display{rdelim}</p>
	<p>{ldelim}price_tax_exc{rdelim}</p>
	<p>{ldelim}price_tax_exc_display{rdelim}</p>
	<p>{ldelim}unity{rdelim} - {l s='Display the product unit.' mod='newsletterpro'}</p>
	<p>{ldelim}unit_price{rdelim} - {l s='Display the unit price in the shop default currency.' mod='newsletterpro'}</p>
	<p>{ldelim}unit_price_display{rdelim} - {l s='Display the unit price with the currency.' mod='newsletterpro'}</p>
	<p>{ldelim}unit_price_tax_exc{rdelim} - {l s='Display the unit price in the shop default currency without taxes.' mod='newsletterpro'}</p>
	<p>{ldelim}unit_price_tax_exc_display{rdelim} - {l s='Display the unit price with the currency without taxes.' mod='newsletterpro'}</p>
	<p>{ldelim}unity_price_bo{rdelim}</p>
	<p>{ldelim}unity_price_bo_display{rdelim}</p>

	<p>{ldelim}module_images_path{rdelim}</p>
	<p>{ldelim}id_product{rdelim}</p>
	<p>{ldelim}id_supplier{rdelim}</p>
	<p>{ldelim}id_manufacturer{rdelim}</p>
	<p>{ldelim}id_category_default{rdelim}</p>
	<p>{ldelim}id_shop_default{rdelim}</p>
	<p>{ldelim}tax_name{rdelim}</p>
	<p>{ldelim}tax_rate{rdelim}</p>
	<p>{ldelim}unit_price_ratio{rdelim}</p>
	<p>{ldelim}on_sale{rdelim}</p>
	<p>{ldelim}online_only{rdelim}</p>
	<p>{ldelim}minimal_quantity{rdelim}</p>
	<p>{ldelim}supplier_reference{rdelim}</p>
	<p>{ldelim}quantity_discount{rdelim}</p>
	<p>{ldelim}condition{rdelim}</p>
	<p>{ldelim}date_add{rdelim}</p>
	<p>{ldelim}date_upd{rdelim}</p>
	<p>{ldelim}price_tax_inc{rdelim}</p>
	<p>{ldelim}price_tax_inc_display{rdelim}</p>
	<p>{ldelim}price_without_reduction_tax_inc{rdelim}</p>
	<p>{ldelim}price_without_reduction_tax_inc_display{rdelim}</p>
	<p>{ldelim}price_without_reduction_tax_exc{rdelim}</p>
	<p>{ldelim}price_without_reduction_tax_exc_display{rdelim}</p>
	<p>{ldelim}pre_tax_retail_price{rdelim}</p>
	<p>{ldelim}ecotax{rdelim}</p>
	<p>{ldelim}ecotax_display{rdelim}</p>
	<p>{ldelim}additional_shipping_cost{rdelim}</p>
	<p>{ldelim}additional_shipping_cost_display{rdelim}</p>
	<p>{ldelim}manufacturer_img_link{rdelim}</p>
	<p>{ldelim}manufacturer_img{rdelim}</p>
</div>