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

var NewsletterProControllers = {
	init : function() {
		var self = this,
			box = NewsletterPro;

		jQuery(document).ready(function($) {

			// init currencyes
			box.components.CurrencySelect.initBySelection($('.gk_currency_select'));

			self.l = NewsletterPro.translations.l(NewsletterPro.translations.constrollers);
			
			self.IndexController.init({
			});

			self.NavigationController.init();
			self.SendController.init();
			self.SettingsController.init();
			self.TemplateController.init();
			self.UpgradeController.init();
			self.ClearCacheController.init();
			
		});
	},

	elem : {},
	vars : {},
	objs : {},
	fcts : {},
	addElement : function( name, elem ) {
		this.elem[name] = elem;
		return this;
	},
	addVariable : function( name, varsiable ) {
		this.vars[name] = varsiable;
		return this;
	},
	addObject : function( name, obj ) {
		this.objs[name] = obj;
		return this;
	}, 
	addFunction : function( name, fct ) {

	},

	IndexController:
	{
		init: function(dom)
		{
			
		}
	},

	SendController :
	{
		vars : {},
		elem : {},
		addVariable : function( name, varsiable ) {
			this.vars[name] = varsiable;
			return this;
		},
		addElement : function( name, elem ) {
			this.elem[name] = elem;
			return this;
		},
		init : function() {
			var self = this;

				 // function sendTestEmail() 
			self.addElement( 'testEmail', $('#test-email') )
				.addElement( 'testEmailButton', self.elem.testEmail.find('#test-email-button') )
				.addElement( 'testEmailInput', self.elem.testEmail.find('#test-email-input') )
				.addElement( 'testEmailCheckbox', self.elem.testEmail.find('#test-email-checkbox') )
				.addElement( 'testEmailMessage', self.elem.testEmail.find('#test-email-message') )
				.addElement( 'smtpTestEmail', $('#smtp-test-email') )
				.addElement( 'smtpTestEmailMessage', $('#smtp-test-email-message') )
				 // function selectAllCustomers
				.addElement( 'selectAllCustomersUserList',$('#user-list') )
				.addElement( 'selectAllCustomersFilters', $('.dropdown-menu') )
				.addElement( 'selectAllCustomersCount', $('#select-all-customers-count') )
				.addElement('testSendEmailBox', $('#test-send-email-box'))
				.addElement('sendTestEmailLangSwitcher', $('#send-test-email-language-switcher'))
				 // function addEmail 
				.addElement( 'addEmailButton', $('#add-email-button') )
				.addElement( 'addEmailInput', $('#add-email-input') )
				.addElement( 'addEmailMessage', $('#add-email-message') )
				.addElement( 'addedSubscribedList', $('#added-subscribed ul') )

				 // function deleteEmail 
				.addElement( 'usersMessage', $('#users-subscribed-message') )
				.addElement( 'visitorsMessage', $('#visitors-subscribed-message') )
				.addElement( 'addedMessage', $('#add-email-message') )
				 // function sleepNewsletter 
				.addElement( 'sleepNewsletterMessage', $('#email-sleep-message') )
				 // function sendNewsletters 
				.addElement( 'sendNewsletters', $('#send-newsletters') )
				.addElement( 'newTask', $('#new-task') )
				.addElement( 'stopSendNewsletters', $('#stop-send-newsletters') )
				.addElement( 'continueSendNewsletters', $('#continue-send-newsletters') )
				.addElement( 'pauseSendNewsletters', $('#pause-send-newsletters') )
				.addElement( 'emailsToSendCount', $('#emails-to-send-count') )
				.addElement( 'emailsSentCountSucc', $('#emails-sent-count-succ') )
				.addElement( 'emailsSentCountErr', $('#emails-sent-count-err') )
				.addElement( 'previousSendNewsletters', $('#previous-send-newsletters') )

				.addElement( 'lastSendErrorDiv', $('#last-send-error-div') )
				.addElement( 'lastSendError', $('#last-send-error') )

			self.addVariable( 'pauseNewsletters', false )
				.addVariable( 'stopNewsletters', false )
				.addVariable( 'emailsSentCountSucc', 0 )
				.addVariable( 'emailsSentCountErr', 0 )

			self.elem.testEmailCheckbox.on( 'change', function( event ) {
				if ( $(this).is(':checked') )
					self.elem.testSendEmailBox.show();
				else
					self.elem.testSendEmailBox.hide();
			});

			var langSelect = new NewsletterPro.components.LanguageSelect({
				selector: self.elem.sendTestEmailLangSwitcher,
				languages: NewsletterPro.dataStorage.get('all_languages'),
				click: function(lang, key) {
					var idLang = Number(lang.id_lang);
				},
			});
		},

		sendTestEmail : function(element)
		{
			var self = this,
				box = NewsletterPro,
				messageElement = self.elem.testEmailMessage,
				messageSuccessElement = $('#test-email-success-message'),
				email = self.elem.testEmailInput.val(),
				idLang = box.dataStorage.get('id_selected_lang');

			messageSuccessElement.hide();
			messageElement.hide();
			box.showAjaxLoader(element);
			$.postAjax({ 'submit': 'sendTestEmail', sendTestEmail: email, idLang: idLang }).done(function( data ) 
			{
				if (data.status)
					messageSuccessElement.show().html('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
				else
					messageElement.empty().show().append('<div class="alert alert-danger">'+data.msg+'</div>');

				setTimeout( function() { 
					messageSuccessElement.hide();
					messageElement.hide();
				}, 5000);
			}).always(function(){
				box.hideAjaxLoader(element);
			});
		}, // end of sendTestEmail

		addEmail : function() {
			var self = this;

			$.postAjax({ 'submit': 'addEmail', addEmail : self.elem.addEmailInput.val() }).done(function( data ) {

				if( data.status == true ) {
					var cls = self.elem.addedSubscribedList.find('li:last').hasClass('odd') ? 'even' : 'odd';

					var template = '';
						template += '<li class="'+cls+'">';
						template += data.template;
						template += '</li>';

					self.elem.addedSubscribedList.append(template);

					var eItem = self.elem.addedSubscribedList.find('li').last();
					NewsletterProComponents.objs.addedSubscribed.createItems( eItem );
					self.elem.addEmailMessage.empty().show().append('<span class="success-msg">'+data.msg+'</span>');
				} else 
					self.elem.addEmailMessage.empty().show().append('<span class="error-msg">'+data.msg+'</span>');

				setTimeout( function() { self.elem.addEmailMessage.fadeOut('slow'); }, 5000);
			});
		}, // end of addEmail

		sleepNewsletter : function( element ) {
			var self = this;
			$.postAjax({ 'submit': 'sleepNewsletter', sleepNewsletter: element.val() }).done(function( data ) {
				if  ( data.status )
					self.elem.sleepNewsletterMessage.empty().show().append('<span class="success-icon">&nbsp;</span>');
				else
					self.elem.sleepNewsletterMessage.empty().show().append('<span class="error-msg">'+data.msg+'</span>');

				setTimeout( function() { self.elem.sleepNewsletterMessage.hide(); }, 5000);
			});
		}, // end of sleepNewsletter

		// Select all customers emails from database  
		allCustomers : [],
		selectAllCustomers : function( element ) {
			var self = this;

			if (element.is(':checked') )
			{
				self.elem.selectAllCustomersFilters.css({ position : 'relative', zoom : '1' });
				self.elem.selectAllCustomersUserList.slideUp( 'slow' ) ;

				$.postAjax({ 'submit': 'selectAllCustomers', selectAllCustomers: true }).done(function( data ) {
					self.elem.selectAllCustomersCount.empty().append(data.length);
					self.allCustomers = data;
				});
			}
			else
			{
				self.elem.selectAllCustomersCount.empty().append('0');
				self.elem.selectAllCustomersUserList.slideDown( 'slow' , function() {
					self.elem.selectAllCustomersFilters.css({position : 'absolute', zoom : '1'});
					self.allCustomers = [];
				});
			}

		}, // end of selectAllCustomers

		getSelectedEmails : function() {
			var self = this,
				customerAllDbEmails = [],
				visitorsAllDbEmails = [],
				addedAllDbEmails = [],
				customers = NewsletterPro.modules.sendNewsletters.vars.customers,
				visitors,
				added = NewsletterPro.modules.sendNewsletters.vars.added,
				selectedEmails = [],
				emails;

			if (NewsletterPro.modules.sendNewsletters.isNewsletterProSubscriptionActive())
				visitors = NewsletterPro.modules.sendNewsletters.vars.visitorsNP;
			else
				visitors = NewsletterPro.modules.sendNewsletters.vars.visitors;

			if (customerAllDbEmails.length) {
				console.warn('this feature is not active');
			} else {
				emails = customers.getSelectedEmails();
				selectedEmails = selectedEmails.concat(emails);
			}

			if (visitorsAllDbEmails.length) {
				console.warn('this feature is not active');
			} else {
				emails = visitors.getSelectedEmails();
				selectedEmails = selectedEmails.concat(emails);
			}

			if (addedAllDbEmails.length) {
				 console.warn('this feature is not active');
			} else {
				emails = added.getSelectedEmails();
				selectedEmails = selectedEmails.concat(emails);
			}

			return selectedEmails;
		}, // end of getSelectedEmails

		/**
		 * Prepare emails for sending
		 * @param  {[array]} emails This parameter is optional
		 */
		prepareEmails : function(emails) 
		{
			var box = NewsletterPro,
				self = this,
				selected;

			if (typeof emails !== 'undefined')
				selected = emails;
			else
				selected = self.getSelectedEmails();

			if( selected.length == 0 )
			{
				alert(NewsletterProControllers.l('no email selected'));
				return;
			}

			var users = JSON.stringify(selected);
			return $.postAjax({'submit' : 'prepareEmails', prepareEmails:users}).done(function(response) {
				if (!response.status)
					NewsletterPro.alertErrors(response.errors.join("\n"));
				else 
				{
					NewsletterPro.modules.sendManager.startSendNewsletters(true);
				}
			}).promise();

		}, // end of prepareEmails

		goToNextStep : function() {
			var self = this;
			var emailsToSend = NewsletterProComponents.objs.emailsToSend;
			var emailsSent = NewsletterProComponents.objs.emailsSent;

			$.postAjax({'submit': 'goToNextStep',goToNextStep: true}).done(function( data ) {
				if( data.exit == true || self.vars.stopSendNewsletters == true ) {
					self.exitAction();
					return;
				}

				if( self.vars.pauseNewsletters == true )
					return;

				emailsToSend.createItems( data.emails );
				emailsSent.removeAllItems();

				self.sendNewsletters();
			});
		}, // end of goToNextStep

		sendNewsletters : function() 
		{
			console.warn('This function "sendNewsletters" is no longer used.');
			return;

			var self = this;
			var emailsToSend = NewsletterProComponents.objs.emailsToSend,
				dom = self.elem,
				l = NewsletterProControllers.l;

			if( emailsToSend.getLength() == 0 ) {
					self.goToNextStep();
			} else {

				self.setStop(false);
				self.setPause(false);

				if( self.elem.sendNewsletters.is(':visible') ) {
					self.elem.sendNewsletters.hide();
					self.elem.newTask.hide();
				}

				if( !self.elem.stopSendNewsletters.is(':visible') )
					self.elem.stopSendNewsletters.show();

				if( self.elem.previousSendNewsletters.is(':visible') )
					self.elem.previousSendNewsletters.hide();

				$.postAjax({'submit': 'sendNewsletters', sendNewsletters: emailsToSend.getFirstItem().email, exit : false}, 'json', false)
					.done(function( data ) {

					}).always( function( data ) {
						try 
						{
							if( typeof data.responseText != 'undefined' )
								var data = $.parseJSON( data.responseText );

						} 
						catch (e) 
						{
							console.warn(e.message);

							var first = emailsToSend.moveFirst();
							if ( typeof first !== 'undefined' && first != false ) {
								var template = '';
								template += '<span class="error-icon" style="float: right;"> </span>';
								first.instance.append( template );

								self.sendNewsletters();
							}

							return;
						}

						if ( data == null )
							return;

						if (typeof data.errors === 'object' && data.errors.hasOwnProperty('template')) {
							alert(data.errors.template);
						} else if (data.errors.length) {
							if (!dom.lastSendErrorDiv.is(':visible'))
								dom.lastSendErrorDiv.slideDown('slow');
							dom.lastSendError.html(data.errors.join('<br>'));
						}

						if( data.exit == true || self.vars.stopSendNewsletters == true ) {
							self.exitAction();
							return;
						}

						var first = emailsToSend.moveFirst();
						if ( typeof first !== 'undefined' && first != false ) {
							var template = '';

							if (data.hasOwnProperty('fwd_emails_success') && parseInt(data.fwd_emails_success) > 0)
							{
								var fwdCount = parseInt(data.fwd_emails_success);
								var fwsTrans = l('forwards');
								if (fwdCount == 1)
									fwsTrans = l('forward');

								template += '<span style="color: green; margin-left: 25px;">+'+fwdCount+' '+fwsTrans+'</span>';
							}

							template += '<span class="'+( data.status == true ? 'success-icon' : 'error-icon' )+'" style="float: right;"> </span>';
							first.instance.append( template );
						}

						if( self.vars.pauseNewsletters == true )
							return;

						self.elem.emailsSentCountSucc.text(data.emails_success);
						self.elem.emailsSentCountErr.text(data.emails_error);
						var remaining = parseInt(data.emails_count) - (parseInt(data.emails_success) + parseInt(data.emails_error));
						self.elem.emailsToSendCount.html( remaining );

						self.sendNewsletters();
					});
				}
		}, // end of sendNewsletters

		exitAction : function() {
			var self = this,
				dom = self.elem;

			self.setPause(true);

			if ( !self.elem.sendNewsletters.is(':visible') ) {
				self.elem.sendNewsletters.show();
				self.elem.newTask.show();
			}

			if ( self.elem.stopSendNewsletters.is(':visible') ) {
				self.elem.stopSendNewsletters.hide();
			}

			if ( self.elem.continueSendNewsletters.is(':visible') )
				self.elem.continueSendNewsletters.hide();

			if( !self.elem.previousSendNewsletters.is(':visible') )
					self.elem.previousSendNewsletters.show();

			setTimeout(function(){
				dom.lastSendErrorDiv.slideUp();
			}, 7000);

			NewsletterPro.modules.task.ui.components.sendHistory.sync();
		}, // end of exitAction

		setPause : function( bool ) {
			var self = this;
			self.vars.pauseNewsletters = bool;

			if ( bool == true ) {
				self.elem.pauseSendNewsletters.hide();
				self.elem.continueSendNewsletters.show();
			} else {
				self.elem.pauseSendNewsletters.show();
				self.elem.continueSendNewsletters.hide();
			}
		}, // end of setPause

		setStop : function( bool ) {
			var self = this;

			self.vars.stopSendNewsletters =  bool;
		}, // end of setStop

		stopNewsletters : function() {
			var dfd = $.Deferred();
			var self = this;
			var emailsToSend = NewsletterProComponents.objs.emailsToSend;
			var emailsSent = NewsletterProComponents.objs.emailsSent;

			self.setStop( true );

			$.postAjax({'submit': 'stopNewsletters', stopNewsletters: true}).done(function(){
				NewsletterPro.modules.task.ui.components.sendHistory.sync();
				dfd.resolve();
			});

			emailsToSend.removeAllItems();
			emailsSent.removeAllItems();

			self.elem.sendNewsletters.show();
			self.elem.newTask.show();

			self.elem.stopSendNewsletters.hide();

			self.elem.continueSendNewsletters.hide();

			self.elem.pauseSendNewsletters.hide();

			self.elem.previousSendNewsletters.show();

			self.elem.emailsSentCountSucc.text('0');
			self.elem.emailsSentCountErr.text('0');
			self.elem.emailsToSendCount.html('0');

			return dfd.promise();
		}, // end of stopNewsletters

	}, // end of SendController

	SettingsController : 
	{
		init : function() {
			var self = this;

			self.addElement('smtpForm', $('#smtpForm') );

			self.addElement('smtpName', $('#smtp-name') );
			self.addElement('smtpDomain', $('#smtp-domain') );
			self.addElement('smtpPasswd', $('#smtp-passwd') );
			self.addElement('smtpServer', $('#smtp-server') );
			self.addElement('smtpEncryption', $('#smtp-encryption') );
			self.addElement('smtpPort', $('#smtp-port') );
			self.addElement('smtpUser', $('#smtp-user') );
			self.addElement('saveSmtp', $('#save-smtp') );
			self.addElement('addSmtp', $('#add-smtp') );
			self.addElement('deleteSmtp', $('#delete-smtp') );

			self.addElement('saveSmtpMessage', $('#save-smtp-message') );

			self.addElement( 'ganalyticsId', $('#ganalytics-id') );
			self.addElement( 'ganalyticsIdMessage', $('#ganalytics-id-message') );

			self.addElement( 'setParams', $('#set-params') );
			self.addElement( 'setParamsDefault', $('#set-params-default') );
			self.addElement( 'setParamsSave', $('#set-params-save') );
			self.addElement( 'setParamsSaveMessage', $('#set-params-save-message') );

			self.addElement( 'campaignForm', $('#campaignForm') );

			self.addElement('utm_source', $('#utm_source') );
			self.addElement('utm_medium', $('#utm_medium') );
			self.addElement('utm_campaign', $('#utm_campaign') );
			self.addElement('utm_content', $('#utm_content') );

		},
		vars : {},
		addVariable : function( name, varsiable ) {
			this.vars[name] = varsiable;
			return this;
		},
		elem : {},
		addElement : function( name, elem ) {
			this.elem[name] = elem;
			return this;
		},
		objs : {},
		addObject : function( name, obj ) {
			this.objs[name] = obj;
			return this;
		},

		viewActiveOnly : function( elem ) {
			$.postAjax({'submit': 'viewActiveOnly', viewActiveOnly : elem.val()}).done(function(data) {

				if ( data.status )
					location.reload();

			});
		},

		convertCssToInlineStyle : function( elem ) {
			$.postAjax({'submit': 'convertCssToInlineStyle', convertCssToInlineStyle : elem.val()}).done(function(data) {

				if ( data.status )
					location.reload();

			});
		},

		productFriendlyURL : function( elem ) {
			$.postAjax({'submit': 'productFriendlyURL', productFriendlyURL : elem.val()}).done(function(data) {

				if ( data.status )
					location.reload();
			});
		},

		debugMode : function( elem )
		{
			$.postAjax({'submit': 'debugMode', debugMode : elem.val()}).done(function(data) {

				if ( data.status )
					location.reload();
			});
		},

		useCache : function( elem ) 
		{
			$.postAjax({'submit': 'useCache', value : elem.val()}).done(function(data) {

				if ( data.status )
					location.reload();
			});
		},

		runMultimpleTasks : function( elem ) {
			$.postAjax({'submit': 'runMultimpleTasks', runMultimpleTasks : elem.val()}).done(function(data) {

				if ( data.status )
					location.reload();
			});
		},

		displayCustomerAccountSettings : function( elem ) {
			$.postAjax({'submit': 'displayCustomerAccountSettings', 'value' : elem.val()}).done(function(response) {

				if ( response.status )
					location.reload();
			});
		},

		subscribeByCategory : function( elem ) {
			$.postAjax({'submit': 'subscribeByCategory', 'value' : elem.val()}).done(function(response) {

				if ( response.status )
					location.reload();
			});
		},

		subscribeByCListOfInterest : function( elem ) {
			$.postAjax({'submit': 'subscribeByCListOfInterest', 'value' : elem.val()}).done(function(response) {

				if ( response.status )
					location.reload();
			});
		},

		sendNewsletterOnSubscribe : function( elem ) {
			$.postAjax({'submit': 'sendNewsletterOnSubscribe', 'value' : elem.val()}).done(function(response) {

				if ( response.status )
					location.reload();
			});
		},

		forwardingFeatureActive : function( elem ) 
		{
			$.postAjax({'submit': 'forwardingFeatureActive', 'value' : elem.val()}).done(function(response) {

				if ( response.status )
					location.reload();
			});
		},

		sendEmbededImagesActive : function( elem ) 
		{
			$.postAjax({'submit': 'sendEmbededImagesActive', 'value' : elem.val()}).done(function(response) {

				if (response.status)
					location.reload();
				else
					NewsletterPro.alertErrors(response.errors);
			});
		},

		chimpSyncUnsubscribed : function(elem)
		{
			$.postAjax({'submit': 'chimpSyncUnsubscribed', 'value' : elem.val()}).done(function(response) {

				if (response.status)
					location.reload();
				else
					NewsletterPro.alertErrors(response.errors);
			});
		},

		displayOnliActiveProducts : function( elem ) {
			$.postAjax({'submit': 'displayOnliActiveProducts', displayOnliActiveProducts : elem.val()}).done(function(data) {

				if ( data.status )
					location.reload();
			});
		},

		emptyAddedEmails : function() {
			$.postAjax({ 'submit': 'emptyAddedEmails', emptyAddedEmails : true}).done(function(data) {
				if( data.status == true )
					location.reload();
			});
		},

		updateGAnalyticsID : function( elem ) {
			var self = this;
			var val = elem.val();

			$.postAjax({'submit': 'updateGAnalyticsID', updateGAnalyticsID:val}).done(function(data) {
				if(data.status == true ) {
					self.elem.ganalyticsIdMessage.empty().show().append('<span class="success-icon">&nbsp;</span>');
				} else {
					self.elem.ganalyticsIdMessage.empty().show().append('<span class="error-icon">&nbsp;</span>');
				}
				setTimeout( function() { self.elem.ganalyticsIdMessage.hide(); }, 5000);
			});
		},

		checkIfCampaignIsRunning: function(elem) {
			NewsletterPro.showAjaxLoader(elem);
			$.postAjax({'submit': 'checkIfCampaignIsRunning'}).done(function(response) {
				if (response.status) {
					alert(response.msg);
				} else {
					alert(response.errors.join("\n"));
				}
			}).always(function(){
				NewsletterPro.hideAjaxLoader(elem);
			});

		},

		activeGAnalytics : function( elem ) {
			var self = this;
			if( elem.is(':checked') ) {
				$.postAjax({'submit': 'activeGAnalytics', activeGAnalytics:true}).done(function(data) {
					if(data.status == true ) {
						self.elem.ganalyticsId.prop('disabled', false);
					}
				});
			} else {
				$.postAjax({'submit': 'activeGAnalytics', activeGAnalytics:false}).done(function(data) {
					if(data.status == true ) {
						self.elem.ganalyticsId.prop('disabled', true);
					}
				});
			}
		},

		universalAnaliytics: function(elem)
		{
			var self = this;
			if( elem.is(':checked') ) 
				$.postAjax({'submit': 'universalAnaliytics', universalAnaliytics:true});
			else
				$.postAjax({'submit': 'universalAnaliytics', universalAnaliytics:false});
		},

		activeCampaign : function( elem ) {
			var self = this;
			if( elem.is(':checked') ) {
				$.postAjax({'submit': 'activeCampaign', activeCampaign:true}).done(function(data) {
					if(data.status == true ) {
						NewsletterProComponents.objs.selectedProducts.removeItems();
						NewsletterProComponents.objs.productList.element.empty();

						self.elem.utm_source.prop('disabled', false);
						self.elem.utm_medium.prop('disabled', false);
						self.elem.utm_campaign.prop('disabled', false);
						self.elem.utm_content.prop('disabled', false);

						self.elem.setParams.prop('disabled', false);

						if( self.elem.setParamsDefault.hasClass('disabled') )
							self.elem.setParamsDefault.removeClass('disabled');
						self.elem.setParamsDefault.attr('onclick', 'NewsletterProControllers.SettingsController.makeDefaultParameteres();');

						if( self.elem.setParamsSave.hasClass('disabled') )
							self.elem.setParamsSave.removeClass('disabled');
						self.elem.setParamsSave.attr('onclick', 'NewsletterProControllers.SettingsController.saveCampaign();');
					}
				});
			} else {
				$.postAjax({'submit': 'activeCampaign', activeCampaign:false}).done(function(data) {
					if(data.status == true ) {
						NewsletterProComponents.objs.selectedProducts.removeItems();
						NewsletterProComponents.objs.productList.element.empty();

						self.elem.utm_source.prop('disabled', true);
						self.elem.utm_medium.prop('disabled', true);
						self.elem.utm_campaign.prop('disabled', true);
						self.elem.utm_content.prop('disabled', true);

						self.elem.setParams.prop('disabled', true);

						if( !self.elem.setParamsDefault.hasClass('disabled') )
							self.elem.setParamsDefault.addClass('disabled');
						self.elem.setParamsDefault.prop('onclick', false);

						if( !self.elem.setParamsSave.hasClass('disabled') )
							self.elem.setParamsSave.addClass('disabled');
						self.elem.setParamsSave.prop('onclick', false);
					}
				});
			}
		},

		makeDefaultParameteres : function() {
			var self = this;
			$.postAjax({'submit': 'makeDefaultParameteres', makeDefaultParameteres:true}).done(function(data) {
				if(data.status == true ) {
					self.elem.setParams.val(data.params);

					self.elem.utm_source.val(data.campaign.UTM_SOURCE);
					self.elem.utm_medium.val(data.campaign.UTM_MEDIUM);
					self.elem.utm_campaign.val(data.campaign.UTM_CAMPAIGN);
					self.elem.utm_content.val(data.campaign.UTM_CONTENT);
				}
			});
		},

		saveCampaign : function() {
			var self = this;

			var utmSource = self.elem.utm_source;
			var utmMedium = self.elem.utm_medium;
			var utmCampaign = self.elem.utm_campaign;
			var utmContent = self.elem.utm_content;

			utmSource.val( utmSource.val().replace(/[&?]/, '') );
			utmMedium.val( utmMedium.val().replace(/[&?]/, '') );
			utmCampaign.val( utmCampaign.val().replace(/[&?]/, '') );
			utmContent.val( utmContent.val().replace(/[&?]/, '') );

			$.submitAjax( {'submit': 'saveCampaign', name: 'saveCampaign', form: self.elem.campaignForm} ).done(function(data) {
				if ( data.status )
					self.elem.setParamsSaveMessage.empty().show().append('<span class="success-icon">&nbsp;</span>');
				else if( data.status === false )
					self.elem.setParamsSaveMessage.empty().show().append('<span class="error-icon">&nbsp;</span>');

				setTimeout( function() { self.elem.setParamsSaveMessage.hide(); }, 5000);
			});
		},
	},

	TemplateController : 
	{
		init : function() {
			var self = this;

				 // function saveNesletterTemplate 
			self.addElement( 'newsletterTemplateTitle', $('#page-title') )
				.addElement( 'saveNewsletterTemplateMessage', $('#save-newsletter-template-message') )
				 // function viewNewsletterTemplate 
				.addElement( 'viewNewsletterTemplateContent', $('#view-newsletter-template-content') )
				.addElement( 'newsletterTemplateContent', $('#newsletter-template-content') )
				 // function toggleShowProductTpl 
				.addElement( 'productTemplate', $('#product-template') )
				 // function saveProductTemplate 
				.addElement( 'saveProductTemplateMessage', $('#save-product-template-message') )
				 // function viewProductTemplate 
				.addElement( 'productTemplateContent', $('#product-template-content') )
				.addElement( 'viewProductTemplateContent', $('#view-product-template-content') )
				 // function deleteImage 
				.addElement( 'deleteImageMessage', $('#delete-image-message') )
				.addElement( 'deleteImage', $('#images') )
				.addElement( 'deleteImageEmptyShow', $('.images-empty-show') )
				.addElement( 'deleteImageEmptyHide', $('.images-empty-hide') )
				.addElement( 'deleteImageNavigation', $('.images-navigation') )
		},
		vars : {},
		addVariable : function( name, varsiable ) {
			this.vars[name] = varsiable;
			return this;
		},
		elem : {},
		addElement : function( name, elem ) {
			this.elem[name] = elem;
			return this;
		},
		objs : {},
		addObject : function( name, obj ) {
			this.objs[name] = obj;
			return this;
		},

		viewNewsletterTemplate : function() {
			var self = this;
			return $.postAjax({'submit': 'viewNewsletterTemplate', viewNewsletterTemplate: true}, 'html').done(function(data) {
				var content = $(data);
				self.elem.viewNewsletterTemplateContent.show().empty().append(content);

				var createTemplate = NewsletterPro.modules.createTemplate;
				createTemplate.updateBoth();
			});
		}, // end of viewNewsletterTemplate

		saveToggleNewsletterTemplate : function( element ) {
			var self = this;

			var buttonName = element.find('span');

			if( self.elem.newsletterTemplateContent.is(':visible') ) {
				self.saveNewsletterTemplate();
				buttonName.html(element.data('name').edit);

			} else {
				self.elem.newsletterTemplateContent.show();
				self.elem.viewNewsletterTemplateContent.hide();
				buttonName.html(element.data('name').view);

			}
		}, // end of saveToggleNewsletterTemplate

		saveAsNewsletterTemplate : function ( element )
		{
			var self = this;
			var name = prompt(element.data('message'), '');

			if ( name == '' || name == null )
				return false;

			$.postAjax({ 'submit': 'saveAsNewsletterTemplate', saveAsNewsletterTemplate : name, content : tinyMCE.get('newsletter_template_text').getContent(), title : self.elem.newsletterTemplateTitle.val() }).done(function(data) {
					var content = '';
					if ( data.status )
						location.reload();
					else
						content = '<p class="error-save">' + data.msg + '</p>';

					self.elem.saveNewsletterTemplateMessage.show().empty().append(content);
			});
		}, // end of saveAsNewsletterTemplate

		changeNewsletterTemplate : function( element ) {
			var self = this;

			$.postAjax({'submit': 'changeNewsletterTemplate', changeNewsletterTemplate: element.val() }).done(function(data) {
				if ( data.status )
					location.reload();
				else
					self.elem.changeNewsletterTemplateMessage.empty().append( '<span class="error-msg">'+data.msg +'</span>');

				setTimeout( function() { self.elem.changeNewsletterTemplateMessage.hide(); }, 5000);
			});
		}, // end of changeNewsletterTemplate

		changeProductTemplate : function( element ) {
			var self = this;

			$.postAjax({'submit': 'changeProductTemplate', changeProductTemplate: element.val() }).done(function(data) {
				if ( data.status )
					location.reload();
				else
					self.elem.changeProductTemplateMessage.empty().append( '<span class="error-msg">'+data.msg +'</span>');

				setTimeout( function() { self.elem.changeProductTemplateMessage.hide(); }, 5000);
			});

		}, // end of changeProductTemplate

		changeProductImageSize : function( element ) {
			var self = this;

			$.postAjax({'submit': 'changeProductImageSize', changeProductImageSize : element.val()}).done(function(data) {
				if ( data.status == true )
					location.reload();
				else
					self.elem.changeProductImageSizeMessage.empty().append( '<span class="error-msg">'+data.msg +'</span>');

				setTimeout( function() { self.elem.changeProductImageSizeMessage.hide(); }, 5000);
			});
		}, // end of changeProductImageSize

		changeProductCurrency : function ( element ) {
			var self = this;

			$.postAjax({'submit': 'changeProductCurrency', changeProductCurrency : element.val()}).done(function(data) {
				if ( data.status )
					location.reload();
				else
					self.elem.changeProductCurrencyMessage.empty().append( '<span class="error-msg">'+data.msg +'</span>');

				setTimeout( function() { self.elem.changeProductCurrencyMessage.hide(); }, 5000);
			}); 
		}, // end of changeProductCurrency

		changeProductLanguage : function ( id ) {
			$.postAjax({'submit': 'changeProductLanguage', changeProductLanguage : id}).done(function(data) {
				if ( data.status )
					location.reload();
				else
					self.changeProductLanguageMessage.empty().append( '<span class="error-msg">'+data.msg +'</span>');

				setTimeout( function() { self.elem.changeProductLanguageMessage.hide(); }, 5000);
			});
		}, // end of changeProductLanguage

		saveProductNumberPerRow : function( element ) {
			var self = this;

	 		var val = parseInt(element.val());
	 		val = (/^\d{1}$/.test(val)) ? parseInt(val) : 3;
	 		element.val(val);

			$.postAjax({'submit': 'saveProductNumberPerRow', saveProductNumberPerRow: element.val()}).done(function(data) {

				if( data.status == true )
					self.elem.saveProductNumberPerRowMessage.empty().show().append('<span class="success-icon">&nbsp;</span>');
				else
					self.elem.saveProductNumberPerRowMessage.empty().show().append('<span class="error-msg">'+data.msg+'</span>');

				setTimeout( function() { self.elem.saveProductNumberPerRowMessage.hide(); }, 5000);

				if( NewsletterProComponents.objs.selectedProducts !== 'undefined' )	{
					NewsletterProComponents.objs.selectedProducts.columns = element.val();
				}
			});

		}, // end of saveProductNumberPerRow

		toggleShowProductTpl : function( element ) {
			var self = this;
			if ( self.elem.productTemplate.is(':visible') ) {
				self.elem.productTemplate.slideUp('slow');
				element.find('span.text').empty().html(element.data('name').show);
			} else {
				self.elem.productTemplate.css('display', 'inline-block').hide().slideDown('slow');
				element.find('span.text').empty().html(element.data('name').hide);
			}
		}, // end of saveProductNumberPerRow

		saveProductTemplate : function() {
			var self = this;
			var tinyContent;

			if (NewsletterPro.dataStorage.get('is_product_template')) {
				tinyContent = $('#product-template-content-textarea').val();
			} else {
				tinyContent = tinyMCE.get('product_template_text').getContent();
			}

			NewsletterPro.modules.selectProducts.updateProductTemplateView(tinyContent);

			$.postAjax({'submit': 'saveProductTemplate', saveProductTemplate : tinyContent, numberPerRow : NewsletterPro.dataStorage.data.product_tpl_nr }).done(function(data) {

				var content = '';
				if ( data.type )
					content = '<p class="success-save">' + data.message + '</p>';
				else
					content = '<p class="error-save">' + data.message + '</p>';

				self.elem.saveProductTemplateMessage.show().empty().append(content);
				
				NewsletterPro.dataStorage.set('product_template', data.content);
				NewsletterPro.components.Product.changeTemplate(data.content);

			}).fail(function( data ) {
				self.elem.saveProductTemplateMessage.show().empty().append('<p class="error-save">Save failure</p>');
			}).always(function( data ) {
				self.viewProductTemplate().always(function() { self.elem.productTemplateContent.hide(); });
			});
		}, // end of saveProductTemplate

		saveToggleProductTemplate  : function ( element ) 
		{
			var self = this;

			var buttonName = element.find('span');

			if ( self.elem.productTemplateContent.is(':visible') ) {
				self.saveProductTemplate();
				buttonName.html(element.data('name').edit);

			} else {
				self.elem.productTemplateContent.show();
				self.elem.viewProductTemplateContent.hide();
				buttonName.html(element.data('name').view);
			}
		}, // end of toggleProductSaveView

		viewProductTemplate : function() {
			var self = this;
				selectProducts = NewsletterPro.modules.selectProducts;

			$.postAjax({ 'submit': 'getProductContent', getProductContent : true}, 'html').done(function(data) {
				if( NewsletterProComponents.objs.selectedProducts !== 'undefined' )	{
						NewsletterProComponents.objs.selectedProducts.template = data;
				}
			});

			return $.postAjax({ 'submit': 'viewProductTemplate', viewProductTemplate: true}, 'html').done(function(data) {
				self.elem.viewProductTemplateContent.show().empty().append(data + '<div style="clear: both; height: 7px;">&nbsp</div>');
				selectProducts.refreshProducts();
			});
		}, // end of viewProductTemplate

		loadProductTemplate: function() {
			var self = this;

			return $.postAjax({ 'submit': 'getProductContent', getProductContent : true}, 'html').done(function(data) {
				if( NewsletterProComponents.objs.selectedProducts !== 'undefined' )	{
					NewsletterProComponents.objs.selectedProducts.template = data;
				}
			}).promise();
		},

		isProductTemplateLoaded: function() {
			return NewsletterProComponents.objs.selectedProducts.template == '' ? false : true;
		},

		saveAsProductTemplate : function( element ) {
			var self = this;
			var nbProducts = NewsletterPro.modules.selectProducts.vars.nbProducts;
			var name = prompt(element.data('message'), '');
			if ( name == '' || name == null )
				return false;

			var content;

			if (NewsletterPro.dataStorage.get('is_product_template')) {
				content = $('#product-template-content-textarea').val();
			} else {
				content = tinyMCE.get('product_template_text').getContent();
			}

			$.postAjax({ 'submit': 'saveAsProductTemplate', saveAsProductTemplate : name, content : content, numberPerRow : nbProducts.val() }).done(function(data) {
				var content = '';
				if ( data.status ) {
					NewsletterPro.modules.selectProducts.vars.templateDataSource.sync();
					var fullContent = data.full_content;
					NewsletterPro.dataStorage.set('product_template', fullContent);
				}
				else
					content = '<p class="error-save">' + data.msg + '</p>';

				self.elem.saveProductTemplateMessage.show().empty().append(content);
			});

		}, // end of saveAsProductTemplate

		deleteImage : function( element, id ) {
			var self = this;

			$.postAjax({ 'submit': 'deleteImage', deleteImage : id }).done(function(data) {

				if ( data.status ) {
					element.parent().parent().remove();
					if ( self.elem.deleteImage.children().length == 0 ) {
						self.elem.deleteImageEmptyShow.show();
						self.elem.deleteImageEmptyHide.hide();
						self.elem.deleteImageNavigation.hide();
					}
				} else
					self.elem.deleteImageMessage.empty().show().append('<span class="error-msg">'+data.msg+'</span>');

				setTimeout( function() { self.elem.deleteImageMessage.hide(); }, 5000);

			});

		}, // end of deleteImage

		showProductHelp : function() 
		{
			var self = this,
				l = NewsletterProControllers.l;

			if (typeof self.productTemplateWin === 'undefined' ) {

				self.productTemplateWin = new gkWindow({
					width: 640,
					height: 540,
					title: l('view available variables product'),
					className: 'newsletter-help-win',
					show: function(win) {

					},
					close: function(win) {

					},
					content: function(win) {
						$.postAjax({'submit': 'showProductHelp'}, 'html').done(function(response) {
							if (typeof self.showProductHelpContent === 'undefined') {
								self.showProductHelpContent = self.showProductHelpContent || response;
								win.setContent(response);
							}
						});
						return '';
					}
				});
			}

			self.productTemplateWin.show();

		}, // end of showProductHelp

		showNewsletterHelp : function() 
		{
			var self = this,
				l = NewsletterProControllers.l;

			if (typeof self.newsletterTemplateWin === 'undefined' ) {

				self.newsletterTemplateWin = new gkWindow({
					width: 640,
					height: 540,
					title: l('view available variables'),
					className: 'newsletter-help-win',
					show: function(win) {},
					close: function(win) {},
					content: function(win) {
						$.postAjax({'submit': 'showNewsletterHelp'}, 'html').done(function(response) {
							if (typeof self.showNewsletterHelpContent === 'undefined') {
								self.showNewsletterHelpContent = self.showNewsletterHelpContent || response;
								win.setContent(response);
							}
						});
						return '';
					}
				});
			}

			self.newsletterTemplateWin.show();
		}, // end of showNewsletterHelp

	}, // end of TemplateController

	NavigationController : 
	{
		init : function() {
			var self = this,
				l = NewsletterProControllers.l;

			self.addElement( 'categoriesList', $('#categories-list') )
				.addElement( 'categoriesListLi', self.elem.categoriesList.find('li') )
				.addElement( 'productList', $('#product-list') )
				.addElement( 'productListDsiplayImages', $('#display-product-image-container') )
				.addElement( 'productListDsiplayImagesMessage', $('#display-product-image-message') )
				.addElement( 'toggleCategoriesButton', $('#toggle-categories') )
				.addElement( 'productSearch', $('#poduct-search') )
				.addElement( 'addedList', $('#added-list') )

			self.addVariable( 'categoriesListWidth', ( parseFloat(self.elem.categoriesList.width()) / parseFloat(self.elem.categoriesList.parent().width()) ) * 100 + '%' )
				.addVariable( 'productListMarginLeft', ( parseFloat(self.elem.productList.css('margin-left')) / parseFloat(self.elem.productList.parent().css('width')) ) * 100 + '%' )

			if( typeof $.getCookie('toggleCategories') === 'undefined' || $.getCookie('toggleCategories') == 'false' ) {
				self.addVariable( 'categoriesVisibility', true );
			} else {
				self.elem.categoriesList.addClass('categories-list-slide-toggle-hide');
				self.elem.productList.addClass('product-list-slide-toggle-hide');
				self.elem.productListDsiplayImages.css('margin-left', 0);
				self.addVariable( 'categoriesVisibility', false );
				self.elem.toggleCategoriesButton.css('background-position', 'bottom left');
			}

			if( window.location.hash == '#viewImported' ) 
			{
				window.location.hash = '#sendNewsletters';
				self.goToStep( 5, self.elem.addedList );
			}
		},

		vars : {},
		addVariable : function( name, varsiable ) {
			this.vars[name] = varsiable;
			return this;
		},
		elem : {},
		addElement : function( name, elem ) {
			this.elem[name] = elem;
			return this;
		},
		objs : {},
		addObject : function( name, obj ) {
			this.objs[name] = obj;
			return this;
		},

		toggleCategories : function( element ) {
			var self = this;
			var speed = 500;

			if( self.vars.categoriesVisibility == true ) {
				self.elem.categoriesListLi.css('width', self.elem.categoriesListLi.width() + 'px');
				self.elem.categoriesList.css('overflow', 'hidden');
				self.elem.productList.animate({
					'margin-left' : 0,
				}, speed);

				self.elem.productListDsiplayImages.animate({
					'margin-left' : 0,
				}, speed);

				self.elem.categoriesList.animate({
					'width' : '1px',
					'padding-left': '1px'
				}, speed, function() {
					self.vars.categoriesVisibility = false;
					element.css('background-position', 'bottom left');
					$.setCookie('toggleCategories', true);
				});

			} else {
				self.elem.productList.animate({
					'margin-left' : self.vars.productListMarginLeft,
				}, speed);

				self.elem.productListDsiplayImages.animate({
					'margin-left' : self.vars.productListMarginLeft,
				}, speed);

				self.vars.categoriesVisibility = true;

				self.elem.categoriesList.animate({
					'width' : self.vars.categoriesListWidth,
					'padding-left' : 0
				}, speed, function() {
					self.elem.categoriesList.css('overflow', 'visible');
					element.css('background-position', 'top left');
					self.elem.categoriesListLi.css('width', 'auto');
					$.setCookie('toggleCategories', false);
				});
			}

		}, // end of toggleCategories

		displayProductImage : function( element ) {
			var self = this;

			$.postAjax({'submit': 'displayProductImage', displayProductImage: element.prop('checked') }).done(function(data) {
				if( data.status )
					location.reload();
				else
					self.elem.productListDsiplayImagesMessage.empty().show().append('<span class="error-msg">'+data.msg+'</span>');

				setTimeout( function() { self.elem.productListDsiplayImagesMessage.hide(); }, 5000);

			});
		}, // end of displayProductImage

		goToStep : function( step, offset ) {
			offset = offset || $('#content');
			NewsletterProComponents.objs.tabItems.trigger('tab_newsletter_' + step );
			 $('html, body').animate({
					scrollTop: offset.offset().top
				}, 1000);
		},

		viewImported: function()
		{		
			NewsletterPro.modules.sendNewsletters.vars.added.sync().done(function(dataSource){
				NewsletterProComponents.objs.tabItems.trigger('tab_newsletter_5');
				$('html, body').animate({
					scrollTop: parseInt($('#added-list').offset().top) - 120
				}, 1000);
			});
		}

	}, // end of NavigationController

	UpgradeController: {

		init : function() 
		{
			var self = this,
				l = NewsletterProControllers.l;
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

	ClearCacheController: {
		init: function()
		{
			var self = this,
				l = NewsletterProControllers.l;
		},

		clear: function(element)
		{
			var box = NewsletterPro;

			$.updateConfiguration('SHOW_CLEAR_CACHE', 0).done(function(response){
				if (!response.success)
					box.alertErrors(response.errors);
				else
					$('#clear-cache-box').hide();
			});
		}
	},
}

NewsletterProControllers.init();