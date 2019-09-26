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

var NewsletterProControllers = ({
	init: function()
	{
		var self = this;
		$(function(){
			self.UpgradeController.init();
		});

		return this;
	},

	UpgradeController: {

		init : function() 
		{
			var self = this;
		},

		execute: function(element)
		{
			var box = NewsletterPro,
				responseBox = $('#update-module-response');

			box.showAjaxLoader(element);
			responseBox.hide().removeClass('error').removeClass('success');
			$.postAjax({'submit': 'updateModule'}).done(function(response) {
				if (response.status)
				{
					var message = response.message.join('<br>'),
						seconds = 5;

					responseBox.addClass('success').show();

					responseBox.html(message.replace('%s', seconds));

					var interval = setInterval(function(){
						responseBox.html(message.replace('%s', --seconds));
						if (seconds <= 0)
						{
							location.reload();
							clearInterval(interval);
						}
					}, 1000);

					element.hide();
				}
				else
				{
					var errors = box.displayAlert(response.errors.join('<br>'));
					responseBox.addClass('error').html(errors).show();
				}

			}).always(function(){
				box.hideAjaxLoader(element);
			});
		}
	}, // end of UpgradeController
}.init());