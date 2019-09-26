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

$(function(){
	NewsletterPro.namespace('modules.frontForward');
	NewsletterPro.modules.frontForward = ({

		l: function (name)
		{
			var translations = NewsletterPro.dataStorage.get('translations');
			
			if (translations.hasOwnProperty(name))
			{
				return translations[name].replace(/\&quot;/g, '"');
			}
			return name;
		},

		init: function(Box)
		{
			if ($('#dispalyForm').length > 0)
			{
				var self         = this,
					limit        = parseInt(NewsletterPro.dataStorage.get('fwdLimit')),
					ajaxLink     = NewsletterPro.dataStorage.get('ajaxLink'),
					emailsJs     = NewsletterPro.dataStorage.get('emailsJs'),
					fwdLimitText = $('#fwd-limit').html(),
					currentInput = $('#first-email');
				
				if (limit == 0)
					$('#fwd-left-side').hide();
				else
					$('#fwd-left-side').show();

				writeRemaining(limit);
				writeEmails(emailsJs);

				function writeEmails(emails)
				{
					if (emails.length > 0)
						$('#fwd-right-side').show();
					else
						$('#fwd-right-side').hide();

					$('#friends-emails-list').empty();
					$.each(emails, function(i, email){
						var tpl = $('<tr data-email="'+email+'"> \
										<td class="item-email">'+email+'</td> \
										<td class="item-close text-right"><span class="icon-close close-email"></span></td> \
									</tr>');
						
						tpl.find('.close-email').on('click tap', function(){
							var email = tpl.data('email');

							$.ajax({
								url: ajaxLink,
								type: 'POST',
								dataType: 'json',
								data: {'action': 'deleteEmail', 'delete_email': email},
								success: function(response)
								{
									if (response.status)
									{
										$('#fwd-left-side').show();
										var ajaxError = $('#ajax-errors');
										ajaxError.hide();

										writeEmails(response.emails);
										limit++;
										writeRemaining(limit);
									}
									else
										$('#ajax-errors').show().html(response.errors.join('<br>'));
								},
								error: function(error)
								{
									alert(self.l('ajax request error'));
								}
							});

						}); // end of click

						$('#friends-emails-list').append(tpl);
					});
				}
			
				$('#ajax-errors').on('click tap', function(){
					var ajaxError = $(this);
					if (ajaxError.find('a.subscription').length == 0)
						ajaxError.hide();
				})

				$('#ajax-success').on('click tap', function(){
					$(this).hide();
				});

				$('#add-new-email').on('click tap', function(){
					$('#ajax-success').hide();
					var inputVal = $.trim(currentInput.val());
					$.ajax({
						url: ajaxLink,
						type: 'POST',
						dataType: 'json',
						data: {'action': 'submitForward', 'to_email': inputVal},
						success: function(response)
						{
							if (response.status)
							{
								$('#ajax-errors').hide();
								currentInput.val('');
								writeEmails(response.emails);

								if (limit > 1)
								{
									limit--;
									writeRemaining(limit);
								}
								else if (limit <= 1)
								{
									$('#fwd-left-side').hide();
									writeRemaining(0);
								}
							}
							else
								$('#ajax-errors').show().html(response.errors.join('<br>'));
						},
						error: function(error)
						{
							alert(self.l('ajax request error'));
						}
					});
				});

				function writeRemaining(value)
				{
					$('#fwd-limit').html(fwdLimitText.replace('%s', value));
				}
			}
			return this;
		},

		requestFriendSubscription: function(element, token, fromEmail, friendEmail)
		{
			var self = this;

			$.ajax({
				url: NewsletterPro.dataStorage.get('ajaxLink'),
				type: 'POST',
				dataType: 'json',
				data: {'action': 'requestFriendSubscription', 'token': token, 'from_email': fromEmail, 'friend_email': friendEmail},
				success: function(response)
				{
					if (response.status)
					{
						$('#ajax-errors').hide();
						$('#first-email').val('');
						$('#ajax-success').html(response.message).show();
					}
					else
						$('#ajax-errors').show().html(response.errors.join('<br>'));
				},
				error: function(error)
				{
					alert(self.l('ajax request error'));
				}
			});
		}

	}.init(NewsletterPro));	
});