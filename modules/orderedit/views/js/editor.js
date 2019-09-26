/**
 * OrderEdit
 *
 * @category  Module
 * @author    silbersaiten <info@silbersaiten.de>
 * @support   silbersaiten <support@silbersaiten.de>
 * @copyright 2015 silbersaiten
 * @version   1.0.0
 * @link      http://www.silbersaiten.de
 * @license   See joined file licence.txt
 */
var orderedit = {
	init: function() {
		orderedit.setHandlers();
		
		orderedit.updateTotalsBlock();
		
		$('.edit_product_price_tax_excl').unbind('keyup');
		$('.edit_product_price_tax_incl').unbind('keyup');

		var lines = $('#orderProducts tbody tr.product_line:not(.customized)');
		lines.each(function() {
			var pr = $(this).attr('data-pr'),
				cus = $('#orderProducts tbody tr.product_line.customized[data-pr = '+pr+']');

			if (cus.length > 0) {
				cus.addClass('dis-cus');
				cus.find('p.displayVal').removeClass('displayVal');
				cus.find('input, select, textarea').remove();
				cus.find('td').css('background-color','rgba(120, 120, 120, 0.5)');
			}
		})
	},
	
	cns: {
		'DISCOUNT_PERCENT': 1,
		'DISCOUNT_VALUE': 2,
		'DISCOUNT_FREE_SHIPPING': 3,
		'CLASS_CUSTOM_VAL': 'customVal',
		'CLASS_REAL_VAL': 'realVal',
		'CLASS_DISPLAY_VAL': 'displayVal',
		'CLASS_PRODUCT_LINE': 'product_line',
		'CLASS_STOCK_EXCEEDED': 'stock_exceeded',
		'CLASS_DEFAULT_COMBINATION': 'default_combination'
	},
	
	l: function(str) {
		if (typeof(ordereditTranslate) != 'undefined') {
			return str in ordereditTranslate ? ordereditTranslate[str] : str;
		}
		
		return str;
	},
	
	setHandlers: function() {
		$('.open_payment_information').click(function(evt){
			evt.preventDefault();
			
			$(this).parents('tr:first').next('.payment_information').slideToggle('fast');
			
			return false;
		});
		
		$('input[name=gift]').change(function() {
			if (parseInt($(this).val()) == 1) {
				$('#giftWrapper').slideDown('fast');
			}
			else {
				$('#giftWrapper').slideUp('fast');
			}
		});
		
		$(document).on('click', 'a#wrappingAutoCalculate', function(evt){
			evt.preventDefault();
			
			orderedit.wrappingCalculate();
			
			return false;
		});
		
		$(document).on('click', 'button[name=ordereditOrderSave]', function(evt) {
			evt.preventDefault();
			
			var fancyContent = $(document.createElement('div')).addClass('bootstrap').attr('id', 'emailNotifyContent');
			
			fancyContent
			.append(
				$(document.createElement('h3')).html(emailNotifyLabel)
			)
			.append(
				$(document.createElement('div')).attr('id', 'emailNotifyClientWrapper').css({'text-align': 'center'})
				.append($(document.createElement('input')).attr({'type': 'button', 'name': 'notify_customer', 'id': 'notify_customer_on', 'value': labelYes}).addClass('btn btn-primary'))
				.append($(document.createElement('input')).attr({'type': 'button', 'name': 'notify_customer', 'id': 'notify_customer_off', 'value': labelNo}).addClass('btn btn-default'))
			);
				
			$.fancybox({
			'modal':true,
			'content': $(document.createElement('div')).html(fancyContent),
			'onComplete': function(){
				$('input[name=notify_customer]').bind('click', function(evt){
				evt.preventDefault();
				
				var notify_value = $(this).is('#notify_customer_on') ? 1 : 0;
				
				$('input#notify_email_send').val(notify_value);
	
				$.fancybox.close();
				orderedit.saveOrder();
				
				return false;
				});
			},
			'afterShow': function(){
				$('input[name=notify_customer]').bind('click', function(evt){
				evt.preventDefault();
				
				var notify_value = $(this).is('#notify_customer_on') ? 1 : 0;
				
				$('input#notify_email_send').val(notify_value);
	
				$.fancybox.close();
				orderedit.saveOrder();
				
				return false;
				});
			}
			});
			
			return false;
		});
		
		$(document).on('click', 'a.deleteDocument', function(evt){
			evt.preventDefault();
			if (confirm(confirm_delete_invoice)) {
				var document_data = $(this).attr('rel').split('^');			
				orderedit.deleteDocument(document_data[0], document_data[1]);
			}			
		});
		
		$(document).on('click', '#generateInvoiceBtn', function(evt){
			evt.preventDefault();
			
			orderedit.addInvoice();
			
			return false;
		});
		
		$(document).on('click', '.js-set-payment', function(evt){
			evt.preventDefault();
			var amount = $(this).attr('data-amount');
			$('input[name=payment_amount]').val(amount);
			var id_invoice = $(this).attr('data-id-invoice');
			$('select[name=payment_invoice] option[value=' + id_invoice + ']').attr('selected', true);
	
			$.scrollTo('#formAddPayment', 1000, {offset: -100});
	
			return false;
		});
		
		$(document).on('submit', 'form#formAddPayment', function(){
			orderedit.addPayment($(this));
			
			return false;
		});
		
		$(document).on('click', 'a.delete_payment_from_order', function(evt){
			evt.preventDefault();

			orderedit.deletePayment($(this).attr('rel'));
		});
		
		$(document).on('submit', 'form#stateSubmitForm', function() {
			orderedit.addOrderStatus($('select#id_order_state').val());
			
			return false;
		});

		$(document).on('submit', 'form#addressInvoiceSubmitForm', function() {
			orderedit.changeAddressInvoice($('select#id_address_invoice').val());
			
			return false;
		});

		$(document).on('submit', 'form#addressShippingSubmitForm', function() {
			orderedit.changeAddressShipping($('select#id_address_shipping').val());
			
			return false;
		});
		
		$(document).on('click', 'a.delete_order_status', function(evt){
			evt.preventDefault();

			orderedit.deleteOrderStatus($(this).attr('rel'));
		});
		
		$(document).on('click', 'input[name=submitNewVoucher]', function(evt){
			evt.preventDefault();
			
			orderedit.addDiscount($('#voucher_form'));
			
			return false;
		});
		
		$(document).on('click', 'a.deleteDiscount', function(evt){
			evt.preventDefault();
			
			orderedit.deleteDiscount(parseInt($(this).attr('rel')));
			
			return false;
		});
		
		$('.date_pick').datepicker({
			buttonImage: '../img/calendar.png',
			prevText:"",
			nextText:"",
			dateFormat: "yy-mm-dd",
			showSecond: true
		});

		$(document).on('click', '#add_voucher', function(evt){
			evt.preventDefault();
			
			$(this).parent().parent().hide();
			$('#voucher_form').parent().show();
	
			return false;
		});
		
		$(document).on('click', '.' + orderedit.cns.CLASS_DISPLAY_VAL + ' span', function(){
			orderedit.toggleProductField($(this).parent('p'));
		});
		
		orderedit.bindProductAddBtn();
		orderedit.bindProductAutocomplete();
		orderedit.bindProductCombinations();
		orderedit.bindProductQtyChange();
		
		$(document).on('click', '#cancel_add_voucher', function(evt){
			evt.preventDefault();
			$('#voucher_form').parent().hide();
			$('#add_voucher').parent().parent().show();
	
			return false;
		});
		
		$(document).on('change', '#discount_type', function(evt){
			// Percent type
			if ($(this).val() == orderedit.cns.DISCOUNT_PERCENT)
			{
				$('#discount_value_field').show();
				$('#discount_currency_sign').hide();
				$('#discount_value_help').hide();
				$('#discount_percent_symbol').show();
			}
			// Amount type
			else if ($(this).val() == orderedit.cns.DISCOUNT_VALUE)
			{
				$('#discount_value_field').show();
				$('#discount_percent_symbol').hide();
				$('#discount_value_help').show();
				$('#discount_currency_sign').show();
			}
			// Free shipping
			else if ($(this).val() == orderedit.cns.DISCOUNT_FREE_SHIPPING)
			{
				$('#discount_value_field').hide();
			}
		});
		
                $('input#submitAddProduct').off();
		$(document).on('click', 'input#submitAddProduct', function(evt){
			evt.preventDefault();
			
			orderedit.addProduct();
		});

        $(document).on('click', 'input#cancelAddProduct', function(evt){
            evt.preventDefault();
            $('div#new_product').slideUp('fast');
            $.scrollTo('div#new_product', 1200, {offset: -200});
        });
		
		$(document).on('click', 'a.delete_product_line', function(evt){
			evt.preventDefault();
			
			$(this).parents('tr.' + orderedit.cns.CLASS_PRODUCT_LINE).slideUp('fast', function() {
                            
                            if ($(this).hasClass('unsaved'))
                            {
                                $(this).remove();
                            }
                            else
                            {
				$(this).find('input.isDeleted').val(1);
                            }
				
				orderedit.updateTotalsBlock();
			});
		});
	},
	
	getEditableBlock: function(elm) {
		return elm.parents('.editable:first');
	},
	
	toggleProductField: function(elm) {
		var parent = orderedit.getEditableBlock(elm),
			customVal = parent.find('.' + orderedit.cns.CLASS_CUSTOM_VAL),
			realVal = parent.find('.' + orderedit.cns.CLASS_REAL_VAL);
		
		elm.stop().fadeTo(250, 0, function(){
			realVal.stop().fadeTo(250, 1, function(){
				
				realVal.find('input, textarea').blur(function() {
					orderedit.processFieldChange($(this), customVal, realVal, elm);
				});
				
				realVal.find('select').change(function() {
                    orderedit.processFieldChange($(this), customVal, realVal, elm);
                });
			});
		}).hide();
	},
	
	processFieldChange: function(changedFieldInput, customVal, changedField, displayField) {

		var fieldName = changedFieldInput.attr('rel');
		
		fieldName = (typeof(fieldName) == 'undefined' || fieldName.length == 0) ? false : fieldName;
		
		//if (fieldName != 'productPriceEdit') {
			customVal.find('span').html(changedFieldInput.val()).end().fadeIn('fast');
		//}
		
		changedField.stop().fadeTo(250, 0, function(){
			displayField.stop().fadeTo(250, 1, function(){
				var fieldName = changedFieldInput.attr('rel');

				if (typeof(fieldName) != 'undefined' && fieldName.length > 0 && fieldName in orderedit.fieldHandlers) {
					orderedit.fieldHandlers[fieldName].call(this, changedFieldInput, changedField, customVal, displayField);
				}
			});
		}).hide();
		
		orderedit.updateTotalsBlock();
	},
	
	calcPrice: function(price, price_wt, tax_rate, withTax)
	{
		var newprice = null;
			
		if ( ! withTax) {
			newprice = price * (1 + tax_rate * 0.01);
		}
		else if (withTax) {
			newprice = price_wt / (1 + tax_rate * 0.01);
		}
		
		return (isNaN(newprice) || newprice < 0) ? 0 : newprice;
	},
	
	collectProducts: function() {
		var products = $('table#orderProducts').find('.product_line');
		
		if (products.length > 0)
		{
			var result = {};
			
			products.each(function(){
				var product = {},
					index = parseInt($(this).attr('id').split('_')[1])
					inputCollection = $(this).find('input, select, textarea');
					
				if (inputCollection.length > 0)
				{
					inputCollection.each(function(){
						if (typeof($(this).attr('rel')) != 'undefined')
						{
							product[$(this).attr('rel')] = $(this).val();
						}
					});
				}
				
				result[index] = product;
			});
			
			return result;
		}
		
		return false;
	},
	
	collectShippingInfo: function() {
		
	},
	
	collectDiscountsInfo: function() {
		var discounts = $('#discounts_wrapper').find('.editable').find('input');
		
		if (discounts.length > 0) {
			var result = {},
				discount = {},
				index = false;
			
			discounts.each(function(){
				relAttr = $(this).attr('rel'),
				index = false;
					
				relAttr = typeof(relAttr) == 'undefined' ? false : relAttr;
				
				if (relAttr) {
					if (relAttr != 'orderDiscountId') {
						discount[relAttr] = $(this).val();
					}
					else {
						index = relAttr;
					}
				}
			});
			
			if (index) {
				result[index] = discount;
			}
			
			return result;
		}
		
		return false;
	},
	
	collectDataFromBlock: function(block, loop_over) {
		if (typeof(block) == 'object' && block != null && block.length > 0)
		{
			var lines = block.find(loop_over),
				result = {};

			if (lines.length > 0)
			{
				lines.each(function(i) {

					if (!$(this).hasClass('dis-cus')) {

						var inputs = $(this).find('input, select, textarea');

						var pr = $(this).attr('data-pr');
						var cus = $('#orderProducts tbody tr.product_line.customized[data-pr = '+pr+']');
						if (cus.length > 0) {
							var cus_id = cus.attr('data-custom-main');
							var cus_props = $('#orderProducts tbody tr.customized[data-custom-prop = '+cus_id+']');

							inputs = inputs.add(cus_props.find('input.edit_customdata'));
						}

						if (inputs.length > 0)
						{
							inputs.each(function() {
								var relAttr = $(this).attr('rel');

								relAttr = typeof(relAttr) == 'undefined' ? false : relAttr;
								// console.log(relAttr + ' - ' + $(this).val());
								if (relAttr) {
									if (typeof(result[i]) == 'undefined') {
										result[i] = {};
									}
									
									if (relAttr == 'customdataEdit') {
										var cus = $(this).attr('id-cus');
										var cusindex = $(this).attr('id-index');

										if (typeof(result[i][relAttr]) == 'undefined') {
											result[i][relAttr] = {};
										}
										if (typeof(result[i][relAttr][cus]) == 'undefined') {
											result[i][relAttr][cus] = {};
										}

										result[i][relAttr][cus][cusindex] = $(this).val();
									} else if (relAttr == 'productCustomQtyEdit') {
										var cus = $(this).attr('id-cus');

										if (typeof(result[i][relAttr]) == 'undefined') {
											result[i][relAttr] = {};
										}

										result[i][relAttr][cus] = $(this).val();
									} else if (relAttr == 'productQtyEdit') {
										var q_c = 0,
											pr = $(this).closest('tr').attr('data-pr'),
											cus = $('#orderProducts tbody tr.product_line.customized[data-pr = '+pr+']');

										if (cus.length > 0) {
											q_c+= parseInt(cus.find('span.customQ').html());
										}

										result[i][relAttr] = parseInt($(this).val()) + q_c;
									} else {
										result[i][relAttr] = $(this).val();
									}
								}
							});
						}
					}
				});
			}
		}

		return result;
	},
	
	formatToPrice: function(price) {
		price = parseFloat(price);
		
		if (isNaN(price)) {
			price = 0;
		}
		
		return formatCurrency(price, currency_format, currency_sign, currency_blank);
	},

	roundPrice: function(or, wt, q) {
		var total = 0;
		switch (PS_ROUND_TYPE) {
			case 2:
				total = ps_round((or * q), priceDisplayPrecision);
				break;
			case 3:
				total = or * q;
				break;
			case 1:
			default:
				total = wt * q;
				break;
		}

		return total;
	},
	
	formatToPercentage: function(numToFormat) {
		return parseFloat(numToFormat).toFixed(2) + '%';
	},
	
	saveOrder: function() {
		var shippingCollection = $('#shipping_table').find('.shipping_line');
		
		if (shippingCollection.length) {
			shippingCollection.each(function(){
				if ($(this).find('select[rel=shippingCarrierId]').val() == 0) {
					var editableBlocks = $(this).find('.editable');
					
					if (editableBlocks.length) {
						editableBlocks.each(function(){
							$(this).find('.customVal span, .displayVal span').html('');
							var inputs = $(this).find('input').not('[rel=orderShippingCarrier]');
							
							if (inputs.length) {
								inputs.each(function(){
									if ($(this).hasClass('is_price_input')) {
										$(this).val(0);
									}
									else {
										$(this).val('');
									}
								});
							}
						});
					}
				}
			});
		}

		orderedit.sendRequest(
			'saveOrder',
			{
				'notify_customer': $('input#notify_email_send').val(),
				'order_currency': $('select.edit_order_currency').val(),
				'id_lang': $('select.edit_order_language').val(),
				'products': orderedit.collectDataFromBlock($('#orderProducts'), 'tr.product_line'),
				'documents': orderedit.collectDataFromBlock($('#documents_table'), 'tr.invoice_line'),
				'discounts': orderedit.collectDataFromBlock($('#discounts_wrapper'), 'tr'),
				'shipping': orderedit.collectDataFromBlock($('#shipping_table'), 'tr'),
				'payment_data': orderedit.collectDataFromBlock($('#formAddPayment'), 'tr.payment_line'),
                'order_data_modified': { 'date_add': $('input#order_dateadd').val()},
				'is_recyclable': $('input#recyclable_on:checked').length ? 1 : 0,
				'wrapping': {
					'is_gift': $('input#gift_on:checked').length ? 1 : 0,
					'price': parseFloat($('input[rel=wrappingPriceEdit]').val()),
					'price_wt': parseFloat($('input[rel=wrappingPriceWtEdit]').val()),
					'tax_rate': parseFloat($('input[rel=wrappingTaxRateEdit]').val()),
					'gift_message': $('textarea[rel=giftMessageEdit]').val()
				}
				
			},
			function(data) {
				//if (typeof(data.success) != 'undefined') {
					var products = $('tr.' + orderedit.cns.CLASS_PRODUCT_LINE);
					
					if (products.length > 0) {
						products.each(function(){
							if (parseInt($(this).find('input[rel=isDeleted]').val()) == 1) {
								$(this).remove();
							}
							else
							{
								var productIndex = parseInt($(this).find('input[rel=productIndex]').val());
								
								if (typeof(data.index_order_detail) != 'undefined' && productIndex in data.index_order_detail) {
									$(this).find('input[rel=idOrderDetail]').val(parseInt(data.index_order_detail[productIndex]));
									$(this).removeClass('unsaved');
								}
							}
						});
					}
                                    //window.location.reload(true);
				//}
			}, 'json'
		);
	},
	
	wrappingCalculate: function() {
		orderedit.sendRequest('wrappingCalculate', {}, function(data) {
			if (typeof(data) != 'undefined' && 'wrapping_price' in data) {
				$('input#wrappingTaxRate').val(parseFloat(data.tax_rate));
				
				var parent = $('div#giftPriceWrapper').find('.editable'),
					customVal = parent.find('.' + orderedit.cns.CLASS_CUSTOM_VAL),
					realVal = parent.find('.' + orderedit.cns.CLASS_REAL_VAL),
					displayVal = parent.find('.' + orderedit.cns.CLASS_DISPLAY_VAL),
					changedField = $('input#wrapping_tax_incl');

				orderedit.fieldHandlers.wrappingPriceWtEdit(changedField, realVal, customVal, displayVal);
			}
		}, 'json');
	},
	
	addInvoice: function() {
		orderedit.sendRequest('addInvoice', {}, false, 'json');
	},
	
	deleteDocument: function(document_class, document_id) {
		orderedit.sendRequest('deleteDocument', {document_class: document_class, id_document: document_id}, false, 'json');
	},
	
	addPayment: function(paymentForm) {
		orderedit.sendRequest('addPayment', orderedit.serializeContainerToObject(paymentForm), false, 'json');
	},
	
	deletePayment: function(paymentId) {
		orderedit.sendRequest('deletePayment', {id_payment: parseInt(paymentId)}, false, 'json');
	},
	
	addOrderStatus: function(order_state_id) {
		orderedit.sendRequest('addOrderStatus', {id_order_state: parseInt(order_state_id)}, false, 'json');
	},

	changeAddressInvoice: function(id_address_invoice) {
		orderedit.sendRequest('changeAddressInvoice', {id_address_invoice: parseInt(id_address_invoice)}, false, 'json');
	},

	changeAddressShipping: function(id_address_shipping) {
		orderedit.sendRequest('changeAddressShipping', {id_address_shipping: parseInt(id_address_shipping)}, false, 'json');
	},
	
	deleteOrderStatus: function(order_history_id) {
		orderedit.sendRequest('deleteOrderStatus', {id_order_history: parseInt(order_history_id)}, false, 'json');
	},
	
	addDiscount: function(discountBlock) {
		orderedit.sendRequest('addDiscount', orderedit.serializeContainerToObject(discountBlock), false, 'json');
	},
	
	deleteDiscount: function(order_cart_rule_id) {
		orderedit.sendRequest('deleteDiscount', {id_order_cart_rule: parseInt(order_cart_rule_id)}, false, 'json');
	},
	
	addProduct: function(evt) {
            
                // check if product is already added
                if($('.product_line[data-pr='+$('input#add_product_product_id').val()+'-'+$('input.product_combination_switch:checked').val()
                        +']').length && $('.product_line[data-pr='+$('input#add_product_product_id').val()+'-'+$('input.product_combination_switch:checked').val()
                        +'] .isDeleted').val()==0)
                {
                    // show warning popup
                    var fancyContent = $(document.createElement('div')).addClass('bootstrap').attr('id', 'emailNotifyContent');
			
			fancyContent
			.append(
				$(document.createElement('h3')).html(duplicateProductWarning)
			)
			.append(
				$(document.createElement('div')).attr('id', 'emailNotifyClientWrapper').css({'text-align': 'center'})
				.append($(document.createElement('input')).attr({'type': 'button', 'name': 'notify_customer', 'id': 'notify_customer_off', 'value': labelOk}).addClass('btn btn-default'))
			);
				
			$.fancybox({
			'modal':true,
			'content': $(document.createElement('div')).html(fancyContent),
			'afterShow': function(){
				$('input[name=notify_customer]').bind('click', function(evt){
				evt.preventDefault();
				$.fancybox.close();
				return false;
				});
                            }});
                    // exit
                    evt.stopImmediatePropagation();
                    return false;
                }
		var newProductContainer = $('div#new_product');
		
		if (newProductContainer.length > 0)
		{
			var last_line = orderedit.getLastLineIndex(),
				id_warehouse = parseInt($('select#add_product_warehouse').val()),
				product_data = {
					'index': last_line + 1,
					'product_id':	 	   parseInt($('input#add_product_product_id').val()),
					'product_name': $('input#add_product_product_name').val(),
					'product_name_nc': $('input#add_product_name_nc').val(),
					'product_attribute_id': parseInt($('input.product_combination_switch:checked').val()),
					'unit_price_tax_excl':  parseFloat($('input#add_product_product_price_tax_excl').val()),
					'unit_price_tax_incl':  parseFloat($('input#add_product_product_price_tax_incl').val()),
					'tax_rate': parseFloat($('input#add_product_product_tax_rate').val()),
					'product_quantity': parseInt($('input#add_product_product_quantity').val()),
					//'product_invoice': parseInt($('select#add_product_product_invoice').val()),
					'id_warehouse': isNaN(id_warehouse) ? 0 : id_warehouse
				};
			
			if (isNaN(product_data.product_attribute_id)) {
				product_data.product_attribute_id = 0;
			}
			
			if (isNaN(product_data.product_quantity)) {
				product_data.product_quantity = 1;
			}
			
			orderedit.sendRequest('addProduct', product_data, function(data) {
				if (data != null && 'product_line' in data) {
					$(data.product_line).find('tr.' + orderedit.cns.CLASS_PRODUCT_LINE).attr('id', 'line_' + last_line);
					
					$('table#orderProducts').find('tbody').append($(data.product_line));
					
					orderedit.updateTotalsBlock();
					
					newProductContainer.slideUp('fast');
				}
			}, 'json');
		}
		
		return false;
	},
	
	sendRequest: function(action, params, callback, format)
	{
		action = action || false;
		format = format || 'json';
		params = params || {};
		
		if (typeof (params) == 'object') {
			// params.id_lang  = id_lang;
			params.iem = iem;
			params.iemp = iemp;
			params.id_order = id_order;
			params.id_shop = id_shop;
			params.current_index = admin_order_tab_link;
			params.token = token;
			
			if (action) {
				params.action = action;
			}
		}
		
		$.post(ajaxPath, params, function (data) {
			$('.orderedit_msg').slideUp('fast', function() {
				$(this).remove()
			});
			
			if (data != null && 'tpls' in data) {
				for (var i in data.tpls) {
					var tpl = data.tpls[i],
						template_wrapper = $('#' + tpl.template_wrapper),
						template_content = tpl.template_content;

					if (template_wrapper.length > 0) {
						template_wrapper.html(template_content);
					}
				}
			}
			
			if (typeof(callback) == 'function') {
				callback.apply(data, arguments);
			}
		}, format);
		
		return true;
	},
	
	serializeContainerToObject: function(containerToSerialize) {
		var prepared = {},
			containerData = $.map(containerToSerialize.find('input, select, textarea'), function(n, i) {
			return {'k': n.name, 'v': $(n).val()};
		});
		
		if (typeof(containerData) == 'object' && containerData != null) {
			for (var i in containerData) {
				prepared[containerData[i].k] = containerData[i].v;
			}
			
			return prepared;
		}
		
		return false;
	},
	
	getCurrentLineIndex: function(lineElm) {
		return lineElm.parents('tr.' + orderedit.cns.CLASS_PRODUCT_LINE).attr('id');
	},
	
	getLastLineIndex: function() {
		var lastLine = $('tr.' + orderedit.cns.CLASS_PRODUCT_LINE + ':last');
		
		if (lastLine.length > 0) {
			return parseInt(lastLine.attr('id').split('_')[1]);
		}
		
		return 0;
	},
	
	getFieldDataByRel: function(index, inputRel) {
		var inputField = $('tr#' + index).find('*[rel=' + inputRel + ']');
		
		if (inputField.length > 0) {
			var realField = inputField.parents('.' + orderedit.cns.CLASS_REAL_VAL + ':first'),
				displayField = realField.prev('.' + orderedit.cns.CLASS_DISPLAY_VAL);
				
			return {'inputField': inputField, 'realField': realField, 'displayField': displayField};
		}
		
		return false;
	},
	
	addProductRefreshTotal: function() {
		var quantity = parseInt($('#add_product_product_quantity').val());
		
		if (quantity < 1 || isNaN(quantity)) {
			quantity = 1;
		}
		
		if (use_taxes) {
			var price = parseFloat($('#add_product_product_price_tax_incl').val());
		}
		else {
			var price = parseFloat($('#add_product_product_price_tax_excl').val());
		}
	
		if (price < 0 || isNaN(price)) {
			price = 0;
		}
		
		var total = orderedit.makeTotalProductCaculation(quantity, price);
		
		$('#add_product_product_total').html(orderedit.formatToPrice(total));
	},
	
	makeTotalProductCaculation: function(quantity, price) {
		return Math.round(quantity * price * 100) / 100;
	},
	
	refreshOrderInfo: function()
	{
		
	},
	
	populateWarehouseList: function(warehouse_list)
	{
		$('#add_product_product_warehouse_area').hide();
		
		if (warehouse_list.length > 1) {
			$('#add_product_product_warehouse_area').show();
		}
		
		var order_warehouse_list = $('#warehouse_list').val().split(',');
		
		$('#add_product_warehouse').html('');
		var warehouse_selected = false;
		
		$.each(warehouse_list, function() {
			if (warehouse_selected == false && $.inArray(this.id_warehouse, order_warehouse_list)) {
				warehouse_selected = this.id_warehouse;
			}
	
			$('#add_product_warehouse').append($('<option value="' + this.id_warehouse + '">' + this.name + '</option>'));
		});
		
		if (warehouse_selected) {
			$('#add_product_warehouse').val(warehouse_selected);
		}
	},
	
	closeAddProduct: function()
	{
		$('tr#new_invoice').hide();
		$('tr#new_product').hide();
	
		// Initialize fields
		$('tr#new_product select, tr#new_product input').each(function() {
			if ( ! $(this).is('.button')) {
				$(this).val('')
			}
		});
		
		$('tr#new_invoice select, tr#new_invoice input').val('');
		$('#add_product_product_quantity').val('1');
		$('#add_product_product_attribute_id option').remove();
		$('#add_product_product_attribute_area').hide();
		$('#add_product_product_stock').html('0');
		current_product = null;
	},
	
	bindProductAddBtn: function() {
		$('.add_product').unbind('click');
		$('.add_product').click(function() {
			$('.cancel_product_change_link:visible').trigger('click');
			// $('.add_product_fields').show();
			$('.edit_product_fields').hide();
			$('.standard_refund_fields').hide();
			$('.partial_refund_fields').hide();
			$('div#new_product').slideDown('fast');
		
			$.scrollTo('div#new_product', 1200, {offset: -100});
		
			return false;
		});
	},
	
	bindProductAutocomplete: function() {
		$("#add_product_product_name").autocomplete(admin_order_tab_link,
		{
			minChars: 3,
			max: 10,
			width: 500,
			selectFirst: false,
			scroll: false,
			dataType: "json",
			highlightItem: true,
			formatItem: function(data, i, max, value, term) {
				return value;
			},
			parse: function(data) {

				var products = new Array();

                if (data.found) {
                    for (var i = 0; i < data.products.length; i++) {
                        products[i] = { data: data.products[i], value: data.products[i].name };
                    }
                }
				return products;
			},
			extraParams: {
				ajax: true,
				token: token_admin_orders,
				action: 'searchProducts',
				id_lang: id_lang,
				id_currency: id_currency,
				id_address: id_address,
				product_search: function() {
					return $('#add_product_product_name').val();
				}
			}
		})
		.result(function(event, data, formatted) {
			if ( ! data) {
				$('div#new_product input, div#new_product select').each(function() {
					if ($(this).attr('id') != 'add_product_product_name') {
						$('div#new_product input, div#new_product select').attr('disabled', true);
					}
				});
			}
			else {
				$('div#new_product input, div#new_product select').removeAttr('disabled');
				// Keep product variable
				current_product = data;
				$('#add_product_product_id').val(data.id_product);
				$('#add_product_product_name').val(data.name);
				$('#add_product_product_tax_rate').val(data.tax_rate);
				$('#add_product_name_nc').val(data.name);
				$('#add_product_product_price_tax_incl').val(data.price_tax_incl);
				$('#add_product_product_price_tax_excl').val(data.price_tax_excl);
				orderedit.addProductRefreshTotal();
				$('#add_product_product_stock').html(data.stock[0]);
				$('.show_on_product_select').slideDown('fast');
	
				if (current_product.combinations.length !== 0) {
					// Reset combinations list
					$('table#new_product_combinations_table').find('tbody').empty();
					var defaultAttribute = 0;
					$.each(current_product.combinations, function() {
						var combWrapper = $(document.createElement('tr')).attr('id', 'combination_' + this.id_product_attribute);
						
						var cells = {
								'radio_btn': $(document.createElement('input')).addClass('product_combination_switch').attr({'type': 'radio', 'name': 'add_product_product_attribute_id', 'value': this.id_product_attribute, 'checked': this.default_on == 1}),
								'name': this.attributes,
								'qty': this.qty_in_stock,
								'price': orderedit.formatToPrice(this.price_tax_excl),
								'price_wt':  orderedit.formatToPrice(this.price_tax_incl),
							};
							
						for (var i in cells)
						{
							combWrapper.append($(document.createElement('td')).append(cells[i]));
						}

						$('#new_product_combinations_table').find('tbody').append(combWrapper);
						
						if (this.default_on == 1) {
							$('tr#combination_' + this.id_product_attribute).addClass(orderedit.cns.CLASS_DEFAULT_COMBINATION);
							$('input#add_product_product_name').val($('#add_product_name_nc').val() + ', ' + this.attributes);
							$('#add_product_product_stock').html(this.qty_in_stock);
							defaultAttribute = this.id_product_attribute;
						}
					});
					// Show select list
					$('#add_product_product_attribute_area').show();
	
					orderedit.populateWarehouseList(current_product.warehouse_list[defaultAttribute]);
				}
				else {
					// Reset combinations list
					$('select#add_product_product_attribute_id').html('');
					// Hide select list
					$('#add_product_product_attribute_area').hide();
	
					orderedit.populateWarehouseList(current_product.warehouse_list[0]);
				}
			}
		});
	},
	
	bindProductCombinations: function() {
		$(document).on('click', 'input.product_combination_switch', function(){
			$('#add_product_product_name').val($('#add_product_name_nc').val() + ', ' + current_product.combinations[$(this).val()].attributes);
			$('#add_product_product_price_tax_incl').val(current_product.combinations[$(this).val()].price_tax_incl);
			$('#add_product_product_price_tax_excl').val(current_product.combinations[$(this).val()].price_tax_excl);
	
			orderedit.populateWarehouseList(current_product.warehouse_list[$(this).val()]);
	
			orderedit.addProductRefreshTotal();
	
			$('#add_product_product_stock').html(current_product.combinations[$(this).val()].qty_in_stock);
		});
	},
	
	bindProductQtyChange: function(){
		$('input#add_product_product_quantity').unbind('keyup');
		$('input#add_product_product_quantity').keyup(function() {
			var quantity = parseInt($(this).val());
			
			if (quantity < 1 || isNaN(quantity)) {
				quantity = 1;
			}
			
			var stock_available = parseInt($('#add_product_product_stock').html());
			// total update
			orderedit.addProductRefreshTotal();
	
			// stock status update
			if (quantity > stock_available) {
				$('#add_product_product_stock').addClass(orderedit.cns.CLASS_STOCK_EXCEEDED);
			}
			else {
				$('#add_product_product_stock').removeClass(orderedit.cns.CLASS_STOCK_EXCEEDED);
			}
		});
	},
	
	processProductAddToOrder: function(){
		var go = true;
		
		if ($('input#add_product_product_id').val() == 0) {
			jAlert(txt_add_product_no_product);
			go = false;
		}

		if ($('input#add_product_product_quantity').val() == 0) {
			jAlert(txt_add_product_no_product_quantity);
			go = false;
		}

		if ($('input#add_product_product_price_excl').val() == 0) {
			jAlert(txt_add_product_no_product_price);
			go = false;
		}

		if (go) {
			if (parseInt($('input#add_product_product_quantity').val()) > parseInt($('#add_product_product_stock').html())) {
				go = confirm(txt_add_product_stock_issue);
			}

			if (go && $('select#add_product_product_invoice').val() == 0) {
				go = confirm(txt_add_product_new_invoice);
			}

			if (go) {
				var query = 'ajax=1&token='+token+'&action=addProductOnOrder&id_order='+id_order+'&';

				query += $('#add_product_warehouse').serialize()+'&';
				query += $('tr#new_product select, tr#new_product input').serialize();
				if ($('select#add_product_product_invoice').val() == 0)
					query += '&'+$('tr#new_invoice select, tr#new_invoice input').serialize();

				var ajax_query = $.ajax({
					type: 'POST',
					url: admin_order_tab_link,
					cache: false,
					dataType: 'json',
					data : query,
					success : function(data)
					{
						if (data.result)
						{
							go = false;
							addViewOrderDetailRow(data.view);
							updateAmounts(data.order);
							updateInvoice(data.invoices);
							updateDocuments(data.documents_html);
							updateShipping(data.shipping_html);
							updateDiscountForm(data.discount_form_html);

							// Initialize all events
							init();

							$('.standard_refund_fields').hide();
							$('.partial_refund_fields').hide();
						}
						else
							jAlert(data.error);
					},
					error : function(XMLHttpRequest, textStatus, errorThrown)
					{
						jAlert("Impossible to add the product to the cart.\n\ntextStatus: '" + textStatus + "'\nerrorThrown: '" + errorThrown + "'\nresponseText:\n" + XMLHttpRequest.responseText);
					}
				});
				ajaxQueries.push(ajax_query);
			}
		}
	},
	
	updateTotalsBlock: function() {
		var totals_block = $('#totals_wrapper');
		var shipping_weight = 0;
		if (totals_block.length > 0) {
			var products_collection = $('tr.' + orderedit.cns.CLASS_PRODUCT_LINE).not('.dis-cus'),
				shipping_collection = $('#shipping_table').find('tbody').find('tr'),
				wrapping_price = parseFloat($('input[rel=wrappingPriceWtEdit]').val()),

				update_data = {
					'total_products': 0,
					'total_discounts': $('#discountsTotal').val(),
					'total_wrapping': 0,
					'total_shipping': 0,
					'total_order': 0
				},
				products_in_order = 0;

			if (products_collection.length > 0) {
				products_collection.each(function() {
					var pr = $(this).attr('data-pr');
					if ($(this).hasClass('customized')) {
						var cusp = $('#orderProducts tbody tr.product_line:not(.customized)[data-pr = '+pr+']');
					}
			
					if (typeof(cusp) == 'undefined' || cusp.length == 0) {

						var status = $(this).find('input.isDeleted');
					
						if (!status.length || parseInt(status.val()) == 0) {
							var q = parseInt($(this).find('input[rel=productQtyEdit]').val());
								q_c = 0,
								pr = $(this).attr('data-pr'),
								cus = $('#orderProducts tbody tr.product_line.customized[data-pr = '+pr+']');

							if (cus.length > 0) {
								var cus_id = cus.attr('data-custom-main'),
									cus_props = $('#orderProducts tbody tr.customized[data-custom-prop = '+cus_id+']');
								cus_props.each(function() {
									q_c+= parseInt($(this).find('input[rel=productCustomQtyEdit]').val());
								})								
							}

							q = q + q_c;

							if ($(this).hasClass('customized')) {
								var cus_id = $(this).attr('data-custom-main'),
									q_c = 0,
									cus_props = $('#orderProducts tbody tr.customized[data-custom-prop = '+cus_id+']');
								cus_props.each(function() {
									q_c+= parseInt($(this).find('input[rel=productCustomQtyEdit]').val());
								})	
								q = parseInt(q_c);
							}

							products_in_order++;

							if (ps_round(parseFloat($(this).find('input[rel=productPriceWtEdit]').attr('pwt')), priceDisplayPrecision) == parseFloat($(this).find('input[rel=productPriceWtEdit]').val())) {
								var pri = parseFloat($(this).find('input[rel=productPriceWtEdit]').attr('pwt'));
							} else {
								var pri = parseFloat($(this).find('input[rel=productPriceWtEdit]').val());
							}

							if (PS_ROUND_TYPE == 1) {
								update_data.total_products+= parseFloat($(this).find('input[rel=productPriceWtEdit]').val()) * q;
							} else if (PS_ROUND_TYPE == 2) {							
								update_data.total_products+= ps_round(pri * q, priceDisplayPrecision);
							} else {
								update_data.total_products+= pri * q;
							}
							shipping_weight = shipping_weight + (parseFloat($(this).find('input[rel=productWeightEdit]').val()) * q)
						}
					}
				});
			}
			
			if (parseFloat($('td.sh_weight .shipping_weight').html()) != ps_round(shipping_weight, priceDisplayPrecision)) {
				$('td.sh_weight .shipping_weight').html(ps_round(shipping_weight, priceDisplayPrecision));
			}

			if (shipping_collection.length > 0) {
				shipping_collection.each(function() {
					update_data.total_shipping+= parseFloat($(this).find('input[rel=shippingPriceWtEdit]').val());
				});
			}
			
			if ( ! isNaN(wrapping_price)) {
				update_data.total_wrapping = wrapping_price;
			}
			
			update_data.total_order = update_data.total_products + update_data.total_shipping + update_data.total_wrapping - update_data.total_discounts;
			for (var i in update_data) {
				if (typeof(totals_block.find('#' + i)) != 'undefined') {
					totals_block.find('#' + i).find('.amount').html(orderedit.formatToPrice(update_data[i]));
				}
			}
		
			var upper_totals_block = $('.metadata-command').find('dd.total_paid'),
			upper_products_block = $('.metadata-command').find('dd#product_number');
			
			if (upper_totals_block.length > 0) {
				upper_totals_block.html(orderedit.formatToPrice(update_data.total_order));
			}
			
			if (upper_products_block) {
				upper_products_block.html(products_in_order);
			}
		}
	},
	
	fieldHandlers: {
		productCurrencyEdit: function(changedFieldInput, changedField, customVal, displayField){
			customVal.find('span').html(changedFieldInput.find("option:selected").text());
		},
		productLanguageEdit: function(changedFieldInput, changedField, customVal, displayField){
			customVal.find('span').html(changedFieldInput.find("option:selected").text());
		},
        orderDateadd: function(changedFieldInput, changedField, customVal, displayField){
            customVal.find('span').html(changedFieldInput.val());
        },

        paymentDate: function(changedFieldInput, changedField, customVal, displayField){
            customVal.find('span').html(changedFieldInput.val());
        },

		paymentAmountEdit: function(changedFieldInput, changedField, customVal, displayField){
			customVal.find('span').html(orderedit.formatToPrice(parseFloat(changedFieldInput.val())));
		},
		
		productNameEdit: function(changedFieldInput, changedField, customVal, displayField) {
			console.log(changedField, displayField);
		},
		
		productPriceEdit: function(changedFieldInput, changedField, customVal, displayField) {
            var parent_row = $(changedFieldInput.parents('tr.' + orderedit.cns.CLASS_PRODUCT_LINE));

			var price_wt = parseFloat(changedField.find('input[name=product_price_tax_incl]').val()),
				tax_data = $('select[name=product_tax_rate]').val().split(':'),
                tax_rate = parseFloat(tax_data[1]),
				newPrice = orderedit.calcPrice(parseFloat(changedFieldInput.val()), price_wt, tax_rate, false),
                total_product_price = parent_row.find('td.total_product'),
                q = parseInt(parent_row.find('input[rel=productQtyEdit]').val());

			if (parent_row.hasClass('customized')) {
				q = parseInt(parent_row.find('span.customQ').html());
			}

            total_product_price.html(orderedit.formatToPrice(parseFloat(newPrice) * q));
			changedField.find('input[name=product_price_tax_incl]').val(newPrice);
			parent_row.find('input[name=product_reduction_per]').val(0.00).attr('data-opp', changedFieldInput.val());
			parent_row.find('span.product_reduction_per_show').html(0.00).closest('.editable').find('.customVal > span').html(0.00);
			customVal.find('span').html(orderedit.formatToPrice(newPrice));
		},
		
		productPriceWtEdit: function(changedFieldInput, changedField, customVal, displayField) {
            var parent_row = $(changedFieldInput.parents('tr.' + orderedit.cns.CLASS_PRODUCT_LINE));

			var price = parseFloat(changedField.find('input[name=product_price_tax_excl]').val()),
				tax_data = $('select[name=product_tax_rate]').val().split(':'),
                tax_rate = parseFloat(tax_data[1]),
				newPrice = orderedit.calcPrice(price, parseFloat(changedFieldInput.val()), tax_rate, true),
                total_product_price = parent_row.find('td.total_product'),
                q = parseInt(parent_row.find('input[rel=productQtyEdit]').val());

			if (parent_row.hasClass('customized')) {
				q = parseInt(parent_row.find('span.customQ').html());
			}

            total_product_price.html(orderedit.formatToPrice(parseFloat(changedFieldInput.val()) * q));

			changedField.find('input[name=product_price_tax_excl]').val(newPrice);
			parent_row.find('input[name=product_reduction_per]').val(0.00).attr('data-opp', newPrice);
			parent_row.find('span.product_reduction_per_show').html(0.00).closest('.editable').find('.customVal > span').html(0.00);
			customVal.find('span').html(orderedit.formatToPrice(parseFloat(changedFieldInput.val())));
		},

		productReductionPerEdit: function(changedFieldInput, changedField, customVal, displayField) {
			var parent_row = $(changedFieldInput.parents('tr.' + orderedit.cns.CLASS_PRODUCT_LINE));

			var new_reduction_percent = parseFloat(changedFieldInput.val()),
				old_reduction = parseFloat(displayField.find('span').html()),
				price = parseFloat(parent_row.find('input[name=product_price_tax_excl]').val()),
				price_wt = parseFloat(parent_row.find('input[name=product_price_tax_incl]').val()),
				tax_data = $('select[name=product_tax_rate]').val().split(':'),
                tax_rate = parseFloat(tax_data[1]),
				old_price = changedFieldInput.attr('data-opp'),
				original_price = (100 * old_price) / (100 - old_reduction),
				reduction = ((original_price / 100) * new_reduction_percent),
				newprice = original_price - reduction,
				newpricewt = orderedit.calcPrice(parseFloat(newprice), price_wt, tax_rate, false),
				total_product_price = parent_row.find('td.total_product'),
                q = parseInt(parent_row.find('input[rel=productQtyEdit]').val());

			if (parent_row.hasClass('customized')) {
				q = parseInt(parent_row.find('span.customQ').html());
			}

			parent_row.find('input[name=product_price_tax_excl]').val(newprice);
			parent_row.find('input[name=product_price_tax_incl]').val(newpricewt);
			parent_row.find('span.product_price_show').html(orderedit.formatToPrice(newpricewt));

			total_product_price.html(orderedit.formatToPrice(parseFloat(newpricewt) * q));
			orderedit.updateTotalsBlock();
		},
		
		productTaxEdit: function(changedFieldInput, changedField, customVal, displayField) {
			var parent_row = $(changedFieldInput.parents('tr.' + orderedit.cns.CLASS_PRODUCT_LINE));
			
			if (parent_row.length > 0) {
				var priceInput = parent_row.find('input[rel=productPriceEdit]'),
					priceWtInput = parent_row.find('input[rel=productPriceWtEdit]'),
                    total_product_price = parent_row.find('td.total_product');
					editableParent = orderedit.getEditableBlock(priceInput),
					priceDisplayField = editableParent.find('.' + orderedit.cns.CLASS_DISPLAY_VAL),
					priceCustomVal = editableParent.find('.' + orderedit.cns.CLASS_CUSTOM_VAL),
					priceRealVal = editableParent.find('.' + orderedit.cns.CLASS_REAL_VAL);
					price = parseFloat(priceInput.val()),
					price_wt = parseFloat(priceWtInput.val());
                    tax_data = changedFieldInput.val().split(':');
					tax_rate = parseFloat(tax_data[1]),
                	q = parseInt(parent_row.find('input[rel=productQtyEdit]').val());

				if (parent_row.hasClass('customized')) {
					q = parseInt(parent_row.find('span.customQ').html());
				}

                    customVal.find('span').html(orderedit.formatToPercentage(tax_rate));
					
				priceInput.val(orderedit.calcPrice(price, price_wt, tax_rate, true));
                total_product_price.html(orderedit.formatToPrice(price_wt * q) + 99);
					
				orderedit.processFieldChange(priceWtInput, priceCustomVal, priceRealVal, priceDisplayField);
			}
		},
		
		productQtyEdit: function(changedFieldInput, changedField, customVal, displayField) {
			var parent_row = $(changedFieldInput.parents('tr.' + orderedit.cns.CLASS_PRODUCT_LINE));
			
			if (parent_row.length > 0) {
				var total_product_price = parent_row.find('td.total_product'),
					price_wt = parseFloat(parent_row.find('input[rel=productPriceWtEdit]').val())
					pwt_or = parseFloat(parent_row.find('input[rel=productPriceWtEdit]').attr('pwt')),
					total_s = 0;

				if (ps_round(pwt_or, priceDisplayPrecision) == price_wt) {
					var pri = pwt_or;
				} else {
					var pri = price_wt;
				}

				total_product_price.html(orderedit.formatToPrice(orderedit.roundPrice(pri, price_wt, parseInt(changedFieldInput.val()))));
			}
		},

		productCustomQtyEdit: function(changedFieldInput, changedField, customVal, displayField) {
			var parent_row = $(changedFieldInput.parents('tr.customized')),
				id_custom = parent_row.attr('data-custom-prop'),
				custom_props = $('#orderProducts tbody').find('tr.customized-prop.customized-'+id_custom),
				custom_main = $('#orderProducts tbody').find('tr.customized-main.customized-'+id_custom),
				q_all = 0;

			custom_props.each(function() {
				var q = parseInt($(this).find('input[rel=productCustomQtyEdit]').val());
				q_all+= q;
			});

			custom_main.find('span.customQ').html(q_all);
			custom_main.find('input.edit_product_quantity').val(q_all);
			
			if (custom_main.length > 0) {
				var total_product_price = custom_main.find('td.total_product'),
					total_product_price_custom = parent_row.find('td.total_product'),
					price_wt = parseFloat(custom_main.find('input[rel=productPriceWtEdit]').val())
					pwt_or = parseFloat(custom_main.find('input[rel=productPriceWtEdit]').attr('pwt')),
					total_s = 0;

				if (custom_main.hasClass('dis-cus')) {
					price_wt = parseFloat(custom_main.find('span.product_price_show').attr('wt'));
					pwt_or = parseFloat(custom_main.find('span.product_price_show').attr('pwt'));
				}

				if (ps_round(pwt_or, priceDisplayPrecision) == price_wt) {
					var pri = pwt_or;
				} else {
					var pri = price_wt;
				}

				total_product_price.html(orderedit.formatToPrice(orderedit.roundPrice(pri, price_wt, q_all)));
				total_product_price_custom.html(orderedit.formatToPrice(orderedit.roundPrice(pri, price_wt, parseInt(changedFieldInput.val()))));
			}
		},

        shippingDate: function(changedFieldInput, changedField, customVal, displayField){
            customVal.find('span').html(changedFieldInput.val());
        },

		shippingPriceEdit: function(changedFieldInput, changedField, customVal, displayField) {
			var price_wt = parseFloat(changedField.find('input[name=shipping_tax_incl]').val()),
				tax_rate = parseFloat($('input[name=shipping_tax_rate]').val()),
				newPrice = orderedit.calcPrice(parseFloat(changedFieldInput.val()), price_wt, tax_rate, false);

			changedField.find('input[name=shipping_tax_incl]').val(newPrice);
			customVal.find('span').html(orderedit.formatToPrice(newPrice));

            //orderedit.updateTotalsBlock();
		},
		
		shippingPriceWtEdit: function(changedFieldInput, changedField, customVal, displayField) {
			var price = parseFloat(changedField.find('input[name=shipping_tax_excl]').val()),
				tax_rate = parseFloat($('input[name=shipping_tax_rate]').val()),
				newPrice = orderedit.calcPrice(price, parseFloat(changedFieldInput.val()), tax_rate, true);

			changedField.find('input[name=shipping_tax_excl]').val(newPrice);
			customVal.find('span').html(orderedit.formatToPrice(parseFloat(changedFieldInput.val())));


            //orderedit.updateTotalsBlock();
		},
		
		shippingTaxRateEdit: function(changedFieldInput, changedField, customVal, displayField) {
			customVal.find('span').html(orderedit.formatToPercentage(parseFloat(changedFieldInput.val())));
			
			var parent_row = $(changedFieldInput.parents('tr:first'));
			
			if (parent_row.length > 0) {
				var priceInput = parent_row.find('input[rel=shippingPriceEdit]'),
					priceWtInput = parent_row.find('input[rel=shippingPriceWtEdit]'),
					editableParent = orderedit.getEditableBlock(priceInput),
					priceDisplayField = editableParent.find('.' + orderedit.cns.CLASS_DISPLAY_VAL),
					priceCustomVal = editableParent.find('.' + orderedit.cns.CLASS_CUSTOM_VAL),
					priceRealVal = editableParent.find('.' + orderedit.cns.CLASS_REAL_VAL);
					price = parseFloat(priceInput.val()),
					price_wt = parseFloat(priceWtInput.val()),
					tax_rate = parseFloat(changedFieldInput.val());
					
				priceInput.val(orderedit.calcPrice(price, price_wt, tax_rate, true));
					
				orderedit.processFieldChange(priceWtInput, priceCustomVal, priceRealVal, priceDisplayField);
			}
		},
		
		wrappingPriceEdit: function(changedFieldInput, changedField, customVal, displayField) {
			var price_wt = parseFloat(changedField.find('input[name=wrapping_tax_incl]').val()),
				tax_rate = parseFloat($('input#wrappingTaxRate').val()),
				newPrice = orderedit.calcPrice(parseFloat(changedFieldInput.val()), price_wt, tax_rate, false);

			changedField.find('input[name=wrapping_tax_incl]').val(newPrice);
			customVal.find('span').html(orderedit.formatToPrice(newPrice));

            orderedit.updateTotalsBlock();
		},
		
		wrappingPriceWtEdit: function(changedFieldInput, changedField, customVal, displayField) {
			var price	= parseFloat(changedField.find('input[name=wrapping_tax_excl]').val()),
				tax_rate = parseFloat($('input#wrappingTaxRate').val()),
				newPrice = orderedit.calcPrice(price, parseFloat(changedFieldInput.val()), tax_rate, true);

			changedField.find('input[name=wrapping_tax_excl]').val(newPrice);
			customVal.find('span').html(orderedit.formatToPrice(parseFloat(changedFieldInput.val())));

            orderedit.updateTotalsBlock();
		},
		
		wrappingTaxRateEdit: function(changedFieldInput, changedField, customVal, displayField) {
			customVal.find('span').html(orderedit.formatToPercentage(parseFloat(changedFieldInput.val())));
			
			var parent_row = $(changedFieldInput.parents('tr:first'));
			
			if (parent_row.length > 0) {
				var priceInput = parent_row.find('input[rel=wrappingPriceEdit]'),
					priceWtInput = parent_row.find('input[rel=wrappingPriceWtEdit]'),
					editableParent = orderedit.getEditableBlock(priceInput),
					priceDisplayField = editableParent.find('.' + orderedit.cns.CLASS_DISPLAY_VAL),
					priceCustomVal = editableParent.find('.' + orderedit.cns.CLASS_CUSTOM_VAL),
					priceRealVal = editableParent.find('.' + orderedit.cns.CLASS_REAL_VAL);
					price = parseFloat(priceInput.val()),
					price_wt = parseFloat(priceWtInput.val()),
					tax_rate = parseFloat(changedFieldInput.val());
					
				priceInput.val(orderedit.calcPrice(price, price_wt, tax_rate, true));
					
				orderedit.processFieldChange(priceWtInput, priceCustomVal, priceRealVal, priceDisplayField);
			}
		},
	},
};

$(function() {
	orderedit.init();
	$('.datetime_pick').datetimepicker({
		dateFormat: 'yy-mm-dd',
		timeFormat: 'hh:mm:ss',
		onSelect: function(){
			$(this).trigger('blur');
		}
	});
});
