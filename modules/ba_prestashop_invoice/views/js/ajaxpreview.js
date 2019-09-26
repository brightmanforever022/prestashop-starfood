/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@buy-addons.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Buy-Addons <hatt@buy-addons.com>
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
* @since 1.6
*/

jQuery(document).ready(function(){
	jQuery('#desc-configuration-preview').click(function(){
		ajaxPreviewInvoice();
	});
	jQuery('#preview_invoice').click(function(){
		ajaxPreviewInvoice();
	});
	jQuery('#desc-delivery-configuration-preview').click(function(){
		ajaxPreviewDelivery();
	});
	jQuery('#preview_delivery').click(function(){
		ajaxPreviewDelivery();
	});
});
function ajaxPreviewInvoice() {
	var header_invoice_template = tinymce.get('header_invoice_template').getContent();
	jQuery('#header_invoice_template').html(header_invoice_template);
	
	var invoice_template = tinymce.get('invoice_template').getContent();
	jQuery('#invoice_template').html(invoice_template);
	
	var footer_invoice_template = tinymce.get('footer_invoice_template').getContent();
	jQuery('#footer_invoice_template').html(footer_invoice_template);
	var templateData = jQuery("#form_template").serialize();
	// console.log(id);
	jQuery.ajax({
		url		: baseUrl+'index.php?controller=ajaxpreview&fc=module&module=ba_prestashop_invoice',
		data	: templateData,
		type	: 'POST',
		success: function(result){
				// console.log(result);
				window.open(baseUrl+"ba_invoice_preview.pdf");
		}
	});
}

function ajaxPreviewDelivery() {
	var header_invoice_template = tinymce.get('header_invoice_template').getContent();
	jQuery('#header_invoice_template').html(header_invoice_template);
	
	var invoice_template = tinymce.get('invoice_template').getContent();
	jQuery('#invoice_template').html(invoice_template);
	
	var footer_invoice_template = tinymce.get('footer_invoice_template').getContent();
	jQuery('#footer_invoice_template').html(footer_invoice_template);
	var templateData = jQuery("#form_template").serialize();
	// console.log(id);
	jQuery.ajax({
		url		: baseUrl+'index.php?controller=ajaxpreviewdelivery&fc=module&module=ba_prestashop_invoice',
		data	: templateData,
		type	: 'POST',
		success: function(result){
			// console.log(result);
			window.open(baseUrl+"ba_delivery_preview.pdf");
		}
	});
}