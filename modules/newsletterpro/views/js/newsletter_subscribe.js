/**
* 2013-2015 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright 2013-2015 Ovidiu Cimpean
* @version   Release: 4
* @license   Do not edit, modify or copy this file
*/

;(function($){
	NewsletterPro.namespace('modules.newsletterSubscribe');
	NewsletterPro.modules.newsletterSubscribe = ({
		box: null,
		dom: null,
		popup: null,
		l: function (name)
		{
			var translations = NewsletterPro.dataStorage.get('translations_subscribe');

			if (translations.hasOwnProperty(name))
			{
				return translations[name].replace(/\&quot;/g, '"');
			}
			return name;
		},
		init: function(box)
		{
			var self = this,
				uniformInit = false,
				popup,
				viewport = new box.components.Viewport(),
				smClasses = ['np-footer-section-sm'],
				xsClasses = ['np-footer-section-xs'];

			self.box = box;

			self.ready(function(dom) {

				var updateClasses = function()
				{
					if (viewport.is('xs'))
					{
						for(var i = 0; i < smClasses.length; i++)
						{
							dom.npsb.removeClass(smClasses[i]);
							dom.npsbId.removeClass(smClasses[i]);
						}

						for(var i = 0; i < xsClasses.length; i++)
						{
							dom.npsb.addClass(xsClasses[i]);
							dom.npsbId.addClass(xsClasses[i]);
						}
					}
					else
					{
						for(var i = 0; i < xsClasses.length; i++)
						{
							dom.npsb.removeClass(xsClasses[i]);
							dom.npsbId.removeClass(xsClasses[i]);
						}

						for(var i = 0; i < smClasses.length; i++)
						{
							dom.npsb.addClass(smClasses[i]);
							dom.npsbId.addClass(smClasses[i]);
						}
					}
				}

				updateClasses();

				$(window).resize(function(){
					updateClasses();
				});

				var define = {
					emailText :	$('.np-email').val(),
				};

				var target;

				var info = box.dataStorage.get('subscription_template_front_info');

				popup = self.popup = new box.components.FrontWindow({
					className: 'np-popup-subscription-window',
					width: info.body_width,
					minWidth: (info.body_min_width > 0 ? info.body_min_width : null ),
					maxWidth: (info.body_max_width > 0 ? info.body_max_width : null ),
					top: info.body_top,
					selector: $('#nps-popup'),
					show: function(pup)
					{
						$('#nps-popup .np-cross').removeClass('cross-message');

						if (!uniformInit)
						{
							uniformInit = true;
							if ($.hasOwnProperty('uniform'))
								$('.np-select-option, .np-input-radio, .np-input-checkbox').uniform();
						}
						else
						{
							if ($.hasOwnProperty('uniform'))
								$.uniform.update('.np-select-option, .np-input-radio, .np-input-checkbox');
						}
					},
					close: function(pup)
					{
						var closeForever = box.dataStorage.getNumber('subscription_template_front_info.close_forever');

						if (closeForever)
							box.modules.newsletterSubscribe.closeForever();
					}
				});

				if (/(\&|\?)newsletterproSubscribe/i.test(window.location.href))
					popup.show();

				setTimeout(function(){
					if (info.display_popup)
						popup.show();
				}, info.start_timer * 1000);

				$(window).resize(function () {
					if ($.hasOwnProperty('uniform'))
						$.uniform.update('.np-select-option, .np-input-radio, .np-input-checkbox');
				});

				var showPopUpCallback = function(event){
					event.preventDefault();

					var ajaxError = $('#ajax-errors-subscribe'),
						ajaxSuccess = $('#ajax-success-subscribe'),
						email = $('.np-email').val();

					if (typeof target !== 'undefined')
						 email = target.val();

					ajaxError.hide();
					ajaxSuccess.hide();

					$('#nps-popup-content').show();
					$('#nps-popup-response').hide();

					if (email !== define.emailText)
						$('#np-popup-email').val(email);

					popup.show();

					$('.np-email').val(define.emailText);
				};

				$('.np-email').on('keydown', function(event){
					if (event.keyCode == 13)
					{
						event.stopPropagation()
						showPopUpCallback(event);
					}
				});

				dom.openSubscribe.on('click tap', showPopUpCallback);

				$('.np-email').focusin(function(event){
					target = $(event.currentTarget);

					if (target.val() === define.emailText)
						target.val('');
				});

				$('.np-email').blur(function(event){
					target = $(event.currentTarget);

					if ($.trim(target.val()) === '')
						target.val(define.emailText);
				});

				$('#submit-newsletterpro-subscribe').on('click tap', function(){

					var ajaxError = $('#ajax-errors-subscribe'),
						ajaxSuccess = $('#ajax-success-subscribe');

					ajaxError.hide();
					ajaxSuccess.hide();

					var terms = $('#np-terms-and-conditions-checkbox');
					if (terms.length)
					{
						if (!terms.is(':checked'))
						{
							ajaxError.show().html(self.l('You must agree to the terms of service before subscribing.'));
							return;
						}
					}

					var submitNewsletterProSubscribeCallbackDone = function(response) 
					{
						if (response.status)
						{
							if (response.displaySubscribeMessage == true)
							{
								$('#nps-popup-content').hide();
								$('#nps-popup-response').empty().show().html(response.msg);
								$('#nps-popup .np-cross').addClass('cross-message');
							}
							else
							{
								ajaxSuccess.show().html(response.msg);
								self.clearForm();
								setTimeout(function(){
									// if the email have been subscribed successfully
									// close the popup untill the cookie expire
									self.closeForever();
									
									// close the popup
									// popup.close();
								}, 5000);
							}
						}
						else
						{
							var errors = box.displayAlert(response.errors.join('<br>'));
							ajaxError.show().html(errors);
						}

						// resize backoffice  preview if exists
						self.resizeBackofficeView();

					};

					var submitNewsletterProSubscribeCallbackFail = function()
					{
						ajaxError.show().html(self.l('ajax request error'));

						// resize backoffice  preview if exists
						self.resizeBackofficeView();
					};

					$.submitAjax({'submit': 'submitNewsletterProSubscribe', form: $('#np-subscribe-form')}, 'json', false)
						.done(submitNewsletterProSubscribeCallbackDone)
						.fail(submitNewsletterProSubscribeCallbackFail);
				});

				$('#newsletterpro-subscribe-close-forever').on('click tap', function(){
					self.closeForever();
				});

				$('#ajax-errors-subscribe').on('click tap', function(){
					$(this).hide();
				});

				$('#ajax-success-subscribe').on('click tap', function(){
					$(this).hide();
				});

			});

			return this;
		},

		closeForever: function()
		{
			var self = this,
				box = self.box;

			var ajaxError = $('#ajax-errors-subscribe'),
				ajaxSuccess = $('#ajax-success-subscribe');

			ajaxError.hide();
			ajaxSuccess.hide();

			$.postAjax({'submit': 'submitNewsletterProSubscribeCloseForever'}).done(function(response){
				if (response.status)
					self.popup.hide();
				else
				{
					var errors = box.displayAlert(response.errors.join('<br>'));
					ajaxError.show().html(errors);

					setTimeout(function(){
						popup.close();
					}, 5000);
				}
			});
		},

		clearForm: function()
		{
			var self = this;
			$('#nps-popup-content').find('[name=firstname]').val('');
			$('#nps-popup-content').find('[name=lastname]').val('');
			$('#nps-popup-content').find('#np-popup-email').val('');

			$('#nps-popup-content').find('#np-days').prop('selectedIndex', 0);
			$('#nps-popup-content').find('#np-months').prop('selectedIndex', 0);
			$('#nps-popup-content').find('#np-years').prop('selectedIndex', 0);
			$('#nps-popup-content').find('#np-list-of-interest').prop('selectedIndex', 0);

			if ($.hasOwnProperty('uniform'))
				$.uniform.update('.np-select-option, .np-input-radio, .np-input-checkbox');
		},

		resizeBackofficeView: function()
		{
			// this in only for backoffice, for the preview
			if (typeof parent.NewsletterPro.modules.frontSubscription !== 'undefined')
			{
				var frontSubscription = parent.NewsletterPro.modules.frontSubscription;
				frontSubscription.resizeView();
			}
		},

		ready: function(func) 
		{
			var self = this;

			$(document).ready(function() {
				self.dom = {
					openSubscribe: $('.newsletterpro-subscribe-button-popup'),
					npsb: $('.newsletter_pro_subscribe_block'),
					npsbId: $('#newsletter_pro_subscribe_block')
				};

				func(self.dom);
			});
		},

	}.init(NewsletterPro));
}(jQueryNewsletterProNew));