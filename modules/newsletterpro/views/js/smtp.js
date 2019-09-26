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

NewsletterPro.namespace('modules.smtp');
NewsletterPro.modules.smtp = ({
	dom: null,
	init: function(box) {
		var self = this;

		self.ready(function(dom) {
			var taskList,
				smtpOptions;

			domSettings();

			function domSettings()
			{
				if (isSmtpActive())
					showSMTP();
				else
					hideSMTP();
			}

			function hideSMTP()
			{
				if (dom.smptConfigBox.is(':visible'))
					dom.smptConfigBox.slideUp('slow');
			}

			function showSMTP()
			{
				if (!dom.smptConfigBox.is(':visible'))
					dom.smptConfigBox.slideDown('slow');
			}

			function isSmtpActive()
			{
				return Boolean(box.dataStorage.data.smtp_active);
			}

			function setListUnsubscribeSwitcher(value)
			{	 
				if (value) {
					dom.listUnsubscribeEmailBox.show();
					$('input[name="list_unsubscribe_active"][value="1"]').prop('checked', true);
					$('input[name="list_unsubscribe_active"][value="0"]').prop('checked', false);
				} else {
					dom.listUnsubscribeEmailBox.hide();
					$('input[name="list_unsubscribe_active"][value="0"]').prop('checked', true);
					$('input[name="list_unsubscribe_active"][value="1"]').prop('checked', false);
				}

			}

			function updateFields(data) 
			{
				var listUnsubscribeActive = Number(data.list_unsubscribe_active);

				if (listUnsubscribeActive) {
					setListUnsubscribeSwitcher(1);
				} else {
					setListUnsubscribeSwitcher(0);
				}

				dom.listUnsubscribeEmail.val(data.list_unsubscribe_email);

				$('#smtpForm [name=method]').prop('checked', false);

				if (data.method == 1)
					dom.smtpMethodMail.prop('checked', true);
				else
					dom.smtpMethodSmtp.prop('checked', true);
				
				hideFieldsByMethod(data.method);

				dom.smptId.val(data.id_newsletter_pro_smtp);
				dom.smtpName.val(data.name);

				dom.smtpFromName.val(data.from_name);
				dom.smtpFromEmail.val(data.from_email);
				dom.smtpFromReplyTo.val(data.reply_to);

				dom.smtpDomain.val(data.domain);
				dom.smtpServer.val(data.server);
				dom.smtpUser.val(data.user);
				dom.smtpPasswd.val(data.passwd);
				dom.smtpEncryption.val(data.encryption);
				dom.smtpPort.val(data.port);
			}

			function emptyFields() 
			{
				setListUnsubscribeSwitcher(0);

				dom.listUnsubscribeEmail.val('');

				dom.smptId.val('0');
				dom.smtpName.val('');

				dom.smtpFromName.val('');
				dom.smtpFromEmail.val('');
				dom.smtpFromReplyTo.val('');

				dom.smtpDomain.val('');
				dom.smtpServer.val('');
				dom.smtpUser.val('');
				dom.smtpPasswd.val('');
				dom.smtpEncryption.val('');
				dom.smtpPort.val('');
			}

			$('input[name="list_unsubscribe_active"]').on('change', function() {
				var val = Number($(this).val());
				if (val) {
					dom.listUnsubscribeEmailBox.slideDown();
				} else {
					dom.listUnsubscribeEmailBox.slideUp();
				}
			});

			var options = $.map(NewsletterPro.dataStorage.get('all_smtp'), function(option){
				var obj = {name: option.name, value: option.id_newsletter_pro_smtp, data: option};

				if (option.hasOwnProperty('selected')) {
					delete option['selected'];
					obj['selected'] = true;

					if (dom.smtpActive.is(':checked')) {
						var opt = $.extend(true, {}, option);

						opt.passwd = '';
						updateFields(opt);
					}
				}
				return obj;
			});

			if (options.length > 0) {
				dom.saveSmtp.show();
				dom.deleteSmtp.show();
			} else {
				dom.saveSmtp.hide();
				dom.deleteSmtp.hide();
			}

			var ui = self.ui;
			var select = ui.SelectOption({
					name: 'smptSelect',
					template: dom.selectSmtp,
					className: 'gk-smtp-select',
					options: options,
					onChange: function(select) 
					{
						var selected = select.getSelected();
						if (selected != null) 
						{
							var data = selected.data,
								val = Number(selected.data.id_newsletter_pro_smtp),
								opt = $.extend(true, {}, data);

							hideFieldsByMethod(data.method);

							opt.passwd = '';
							updateFields(opt);

							$.postAjax({'submit': 'changeSMTP', changeSMTP: val}).done(function(response){
								if (!response.status)
									dom.smtpMessage.empty().show().append('<div class="alert alert-danger">'+response.msg+'</div>');

								setTimeout( function() { dom.smtpMessage.hide(); }, 5000);
							});
						}

					}
				});

			var saveSmtp = ui.Button({
				name: 'saveSmtp',
				template: dom.saveSmtp,
				click: function() {

					dom.saveSmtpMessage.hide();

					$.submitAjax( {'submit': 'saveSMTP', name: 'saveSMTP', form: dom.smtpForm} ).done(function(response) {
						taskList = taskList || NewsletterPro.modules.task.ui.components.taskList;
						smtpOptions = smtpOptions || NewsletterPro.dataStorage;
						if ( response.status ) {
							var obj = response.obj;

							select.updateOption(obj);

							dom.saveSmtpSuccess.empty().show().append('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
						} else {
							if (response.errors.length > 0)
								dom.saveSmtpMessage.empty().show().append(response.errors.join('<br />'));
							else
								dom.saveSmtpSuccess.empty().show().append('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
						}
						$.postAjax({'submit': 'getAllSMTPJson', getAllSMTPJson: true}).done(function(res){

							smtpOptions.add('all_smtp', res);
							taskList.sync();
						});
						setTimeout( function() { dom.saveSmtpSuccess.hide(); }, 5000);
						setTimeout( function() { dom.saveSmtpMessage.hide(); }, 5000);
					});

				}
			});

			var addSmtp = ui.Button({
				name: 'addSmtp',
				template: dom.addSmtp,
				click: function() {
					var that = addSmtp;

					$.submitAjax({'submit': 'addSMTP', name: 'addSMTP', form: dom.smtpForm}).done(function(response){
						taskList = taskList || NewsletterPro.modules.task.ui.components.taskList;
						smtpOptions = smtpOptions || NewsletterPro.dataStorage;
						if (response.status) 
						{

							var obj = response.obj;
							select.addOption({name: obj.name, value: obj.name, data: obj, selected: true});
							dom.smptId.val(obj.id_newsletter_pro_smtp);
							dom.saveSmtpSuccess.empty().show().append('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');

							var opt = select.getOptions();

							if (opt.length > 0) {
								dom.saveSmtp.show();
								dom.deleteSmtp.show();
							}

						} 
						else
							dom.saveSmtpMessage.empty().show().append(response.errors.join('<br />'));

						$.postAjax({'submit': 'getAllSMTPJson', getAllSMTPJson: true}).done(function(res){
							smtpOptions.add('all_smtp', res);
							taskList.sync();
						});

						setTimeout( function() { dom.saveSmtpSuccess.hide(); }, 5000);
						setTimeout( function() { dom.saveSmtpMessage.hide(); }, 15000);
					});
				}
			});

			var deleteSmtp = ui.Button({
				name: 'deleteSmtp',
				template: dom.deleteSmtp,
				click: function() {
					var selected = select.getSelected();

					if (selected != null)
					{
						var id = selected.data.id_newsletter_pro_smtp;

						$.postAjax( {'submit': 'deleteSMTP', deleteSMTP: id} ).done(function(response) {

							if (response.hasOwnProperty('demo_mode') && response.demo_mode == true && response.errors.length > 0) {
								NewsletterPro.alertErrors(response.errors);
								return;
							}

							taskList = taskList || NewsletterPro.modules.task.ui.components.taskList;
							smtpOptions = smtpOptions || NewsletterPro.dataStorage;
							if (response.status) 
							{
								selected.destroy();

								var sel = select.getSelected();

								if (sel != null) {
									var obj = $.extend(true, {}, sel.data),
										val = obj.id_newsletter_pro_smtp;

									obj.passwd = '';
									dom.smptId.val(obj.id_newsletter_pro_smtp);

									$.postAjax({'submit': 'changeSMTP', changeSMTP: val}).done(function(res){
										if (!res.status)
											dom.smtpMessage.empty().show().append('<span class="error-msg">'+res.msg+'</span>');
										setTimeout( function() { dom.smtpMessage.hide(); }, 5000);
									});

									updateFields(obj);
								} else {
									emptyFields();

									dom.saveSmtp.hide();
									dom.deleteSmtp.hide();
								}
							} 
							else 
								box.alertErrors(response.errors);

							$.postAjax({'submit': 'getAllSMTPJson', getAllSMTPJson: true}).done(function(res){
								smtpOptions.add('all_smtp', res);
								taskList.sync();
							});
						});
					}
				}
			});

			dom.smtpActive.on('change', function(){
				if( $(this).is(':checked') ) 
				{
					$.postAjax({'submit': 'smtpActive', smtpActive: true}).done(function(response) {
						if( response.status == true ) 
						{
							select.enable();

							var selected = select.getSelected();

							if (selected != null)
							{
								var data = selected.data,
									opt = $.extend(true, {}, data);

								opt.passwd = '';

								updateFields(opt);
							}
							showSMTP();
						}
					});
				} 
				else 
				{
					$.postAjax({'submit': 'smtpActive', smtpActive: false}).done(function(response) {
						if( response.status == true ) 
						{
							select.disable();
							emptyFields();
							hideSMTP();
						}
					});
				}
			});

			dom.smtpTestInput.on('blur', function(){
				box.dataStorage.set('configuration.PS_SHOP_EMAIL', $(this).val());
			});

			var smtpTestButton = ui.Button({
				name: 'smtpTestButton',
				template: dom.smtpTestButton,
				click: function() 
				{
					var that = this,
						message = dom.smtpTestMessage,
						success = dom.smtpTestSuccess,
						email = dom.smtpTestInput.val(),
						buttonElement = $(this);

					success.hide(); 
					message.hide();
					
					box.showAjaxLoader(buttonElement);

					$.postAjax({ 'submit': 'sendMailTest', sendMailTest: email }).done(function( data ) {
						if( data.status )
							success.empty().show().append('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
						else
							message.empty().show().append('<div class="alert alert-danger">' + data.msg + '</div>');

						setTimeout( function() { 
							success.hide(); 
							message.hide(); 
						}, 10000);
					}).always(function(){
						box.hideAjaxLoader(buttonElement);
					});
				}
			});

			$('#smtpForm [name="method"]').on('change', function(){
				hideFieldsByMethod($(this).val());
			});

			function hideFieldsByMethod(method)
			{
				switch(method)
				{
					// mail method
					case '1':
						dom.smtpOnly.slideUp();
					break;

					// smtp method
					case '2':
						dom.smtpOnly.slideDown();
					break;
				}
			}

		});
		return self;
	},

	ready: function(func) {
		var self = this;
		$(document).ready(function(){
			self.dom = {
				smtpOnly: $('#smtp-only'),
				selectSmtp: $('#select-smtp'),

				smtpActive: $('#smtp-active'),
				smtpFromName: $('#smtp-from-name'),
				smtpFromEmail: $('#smtp-from-email'),
				smtpFromReplyTo: $('#smtp-reply-to'),
				smtpMethodSmtp: $('#method-smtp'),
				smtpMethodMail: $('#method-mail'),

				smtpName: $('#smtp-name'),
				smtpDomain: $('#smtp-domain'),
				smtpServer: $('#smtp-server'),
				smtpUser: $('#smtp-user'),
				smtpPasswd: $('#smtp-passwd'),
				smtpEncryption: $('#smtp-encryption'),
				smtpPort: $('#smtp-port'),

				saveSmtp: $('#save-smtp'),
				addSmtp: $('#add-smtp'),
				deleteSmtp: $('#delete-smtp'),

				smtpMessage: $('#change-smtp-message'),
				saveSmtpMessage: $('#save-smtp-message'),
				saveSmtpSuccess: $('#save-smtp-success'),

				smtpForm: $('#smtpForm'),

				smtpTestInput: $('#smtp-test-email'),
				smtpTestMessage: $('#smtp-test-email-message'),
				smtpTestSuccess: $('#smtp-test-email-success'),
				smtpTestButton: $('#smtp-test-email-button'),

				listUnsubscribeEmailBox: $('#smtp-list-unsubscribe-email-box'),
				listUnsubscribeEmail: $('#smtp-list-unsubscribe-email'),

				smptId: $('#smpt-id'),

				smptConfigBox: $('#smpt-config-box'),
			};
			func(self.dom);
		});
	},

	each: function(array, func) {
		for (var name in array)
			func(array[name], name);
	},

	ui: ({
		components: {},
		init: function() {
			return this;
		},

		add: function(name, value) {
			this.components[name] = value;
		},

		SelectOption: function SelectOption(cfg) {
			if (!(this instanceof SelectOption))
				return new SelectOption(cfg);
			var main = NewsletterPro.modules.smtp,
				ui = NewsletterPro.modules.smtp.ui,
				self = this,
				name = cfg.name,
				template = cfg.template,
				className = cfg.className || '',
				options = cfg.options || [],
				change = cfg.onChange || null,
				sameAs = cfg.sameAs || null,
				selected;

			self.name = name;
			ui.add(name, self);

			function setTemplate(template) {
				template = $(template);
				self.template = template;

				template.attr({
					'autocomplete': 'off',
				});

				template.addClass('gk-select');
				template.addClass(className);

				template.options = [];
				main.each(options, function(opt_data) {
					addOption(opt_data);
				});

				addEvents(template);
			}

			function addEvents(template) {
				template.on('change', function(event){
					self.onChange.call(template, self);
				});
			}

			function addOption(opt_data) {
				self.template.show();

				var option = $('<option value="'+opt_data.value+'">'+opt_data.name+'</option>'),
					data = opt_data.data || {};

				if (opt_data.hasOwnProperty('selected') && opt_data.selected) {
					option.prop('selected', true);
					self.selected = option;
				}

				option.data = data;
				option.dataInit = opt_data;
				self.template.options.push(option);
				self.template.append(option);

				option.destroy = function() {
					var index = self.template.options.indexOf(this);
					if (index > -1)
						self.template.options.splice(index, 1);

					this.remove();

					var options = self.getOptions();

					if (options.length > 0)
						self.setSelected(options[0]);
					else {
						self.hide();
						self.selected = null;
					}
				}
			}

			function getInstanceByName(name) {
				var components = main.ui.components,
					name,
					component;

				for (name in components) {
					component = components[name];
					if (component.hasOwnProperty('name') && component.name === sameAs)
						return component;
				}
				return false;
			}

			setTemplate(template);

			self.addOption = function(opt_data) {
				addOption(opt_data);
			};

			self.updateOption = function(data) {
				var selected = self.getSelected();
				if (selected != null) {
					selected.data = data;
					selected.text(data.name);
					selected.val(data.name);
				}
			};

			self.getData = function() {
				var data = [];
				main.each(self.template.options, function(option){
					data.push(option.data);
				});
				return data;
			};

			self.getOptions = function() {
				var data = [];
				main.each(self.template.options, function(option){
					data.push(option);
				});
				return data;
			};

			self.getSelected = function() {
				return self.selected;
			};

			self.setSelected = function(value) {
				value['selected'] = true;
				value.prop('selected', true);
				self.selected = value;
			};

			self.onChange = function() {
				var options = self.getOptions(),
					value = this.val();

				var match = $.grep(options, function(item) {
					return item.dataInit.value === value;
				});

				var selected = self.getSelected();
				if (selected != null)
					delete selected.data['selected'];

				if (match.length > 0)
					self.setSelected(match[0]);

				if (typeof change === 'function')
					change.call(this, self);
			};

			self.disable = function() {
				self.template.prop('disabled', true);
			};

			self.enable = function() {
				self.template.prop('disabled', false);
			};

			self.hide = function() {
				self.template.hide();
			};

			self.show = function() {
				self.template.show();
			};

			self.refresh = function(opt_data) {

				var options = self.getOptions()
					selected = false;

				main.each(opt_data, function(opt) {
					if (opt.hasOwnProperty('selected') && opt.selected == true) {
						selected = true;
					}
				});

				main.each(options, function(option) {
					option.destroy();
				});

				main.each(opt_data, function(opt) {
					if (!selected) {
						opt['selected'] = true;
						selected = false;
					}
					addOption(opt);
				});
			};



			return self;
		}, // end of SelectOption

		Button: function Button(cfg) {
			if (!(this instanceof Button))
				return new Button(cfg);
			var main = NewsletterPro.modules.smtp,
				ui = NewsletterPro.modules.smtp.ui,
				self = this,
				template = cfg.template,
				name = cfg.name;

			function setTemplate(template) {
				template = $(template);

				addEvents(template);
				self.template = template;
			}

			function addEvents(template) {
				template.on('click', function() {
					self.click.call(template, self);
				});
			}

			setTemplate(template);

			self.click = function() {
				if (typeof cfg.click === 'function')
					cfg.click.call(this, self);
			};

			ui.add(name, self);
			return self;
		}, // end of SelectOption

	}.init()),

}.init(NewsletterPro));