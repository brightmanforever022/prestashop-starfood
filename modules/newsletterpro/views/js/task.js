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

NewsletterPro.namespace('modules.syncTask');
NewsletterPro.modules.syncTask = ({
	speed: {
		slow: 60,
		normal: 45,
		fast: 15,
	},
	dom: null,
	box: null,
	interval: null,
	progressIds: [],

	init: function(box) {
		var self = this;
		self.box = box;

		self.ready(function(dom){

			var taskList,
				taskHistory,
				sendHistory,
				progressIds = self.progressIds || [],
				removedIds = [];

			function updateRow(id_task, data) 
			{
				taskList = taskList || NewsletterPro.modules.task.ui.components.taskList;
				taskHistory = taskHistory || NewsletterPro.modules.task.ui.components.taskHistory;

				var pIndex = progressIds.indexOf(id_task),
					rIndex = removedIds.indexOf(id_task);
				if (pIndex == -1 && rIndex == -1)
					progressIds.push(id_task);

				if (typeof taskList !== 'undefined') {
					taskList.syncDataById(id_task, data);
				}
			}

			function done(done) 
			{
				if (done.length > 0) {
					$.each(done, function(index, data) {
						var id_task = data.id_newsletter_pro_task,
							isDone = parseInt(data.done) ? true : false;

						 if (isDone) {
							var item = taskList.getItemById(id_task);
							if (item) {
							 	taskList.sync();
							 	taskHistory.sync();
							}
						 }
					});
					taskList.refreshView();
				}
			}

			function progress(progress) 
			{
				if (progress.length > 0) {
					self.runInterval(self.speed.fast);
					$.each(progress, function(index, data) {
						var id_task = data.id_newsletter_pro_task;
						updateRow(id_task, data);
					});
				} else {
					self.runInterval(self.speed.normal);
				}
			}

			self.updateRow = function(id_task, data) {
				updateRow(id_task, data);
			};

			self.syncAjax = function () {
				taskList = taskList || NewsletterPro.modules.task.ui.components.taskList;
				taskHistory = taskHistory || NewsletterPro.modules.task.ui.components.taskHistory;

				$.postAjax({'submit': 'getTasksInProgress', getTasksInProgress: true, progressIds: progressIds}, 'json', false).done(function(response) {
					if (typeof response === 'object') 
					{
						progress(response.result);
						done(response.result_look);
					} 
					else if (response > 0 && response !== taskList.items.length) 
					{
						taskList.sync();
					 	taskHistory.sync();
					}
				});
			};

			self.runInterval(self.speed.normal);
		});

		return self;
	},

	runInterval: function(seconds) {
		var self = this;

		if (self.interval != null)
			clearInterval(self.interval);

		self.interval = setInterval(function() {
			self.syncAjax();
		}, 1000 * seconds);
	},

	ready: function(func) {
		var self = this;

		$(document).ready(function(){
			self.dom = {};
			func(self.dom)
		});
	},

}.init(NewsletterPro));

NewsletterPro.namespace('modules.task');
NewsletterPro.modules.task = ({
	storage: null,
	dom: null,
	box: null,
	init: function(box) {
		var self = this,
			syncTask = NewsletterPro.modules.syncTask;

		self.box = box;

		self.ready(function(dom) {
			var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.task);

			self.initStorage();

			dom.taskButton.on('click', function() {
				self.taskClick.call(dom.taskButton, self);
			});

			function getOptions(opt) {
				opt = $.extend(true, {}, opt);
				var options = $.map(opt, function(option){
					var obj = {name: option.name, value: option.id_newsletter_pro_smtp, data: option};
					if (option.hasOwnProperty('selected')) {
						delete option['selected'];
						obj['selected'] = true;
					}
					return obj;
				});
				return options;
			}

			function setSelected(options, obj) {
				var key = obj.key,
					value = obj.value,
					selectedSet = false,
					name;

				for (name in options) {
					var option = options[name];

					if (option.data.hasOwnProperty(key) && option.data[key] === value) {
						option['selected'] = true;
						selectedSet = true;
					} else if (option.hasOwnProperty('selected')) {
						delete option['selected'];
					}
				}

				if (!selectedSet && options.length > 0) {
					options[0]['selected'] = true;
				}
				return options;
			}

			dom.taskTemplateSelect.on('click', function(event) {
				self.setStorage('template', $(this).val());
			});

			var options = getOptions(NewsletterPro.dataStorage.get('all_smtp'));
			var smtp = NewsletterPro.modules.smtp,
				smtpSelect = smtp.ui.SelectOption({
					name: 'taskSmptSelect',
					template: dom.taskSmptSelect,
					className: 'gk-smtp-select',
					options: options,
					onChange: function() 
					{
						var selected = smtpSelect.getSelected();
						if (selected != null) {
							var data = selected.data,
								email = NewsletterPro.dataStorage.get('configuration.PS_SHOP_EMAIL'),
								smtp = data.name,
								id_newsletter_pro_smtp = data.id_newsletter_pro_smtp;
								// email = data.user,

							dom.taskEmailTest.val(email);

							self.setStorage('smtp', smtp);
							self.setStorage('id_newsletter_pro_smtp', id_newsletter_pro_smtp);
						}
					}
				});

			var smtpSelected = smtpSelect.getSelected();

			if (smtpSelected != null)
				self.setStorage('smtp', smtpSelected.data.name);

			var template = dom.taskTemplate,
				datepicker = dom.datepicker,
				ui = self.ui,
				win = ui.TaskWindow({
					width: 425,
					className: 'gk-task-window',
					show: function(win) {
						dom.emailsCount.text(self.getEmails().length);

						var smptSelect = smtp.ui.components.smptSelect,
							data = smptSelect.getData(),
							options = getOptions(data);

						smtpSelect.refresh(options);
					},
				});

			win.setHeader(l('new task'));

			datepicker.datetimepicker({
				prevText: '',
				nextText: '',
				dateFormat: box.dataStorage.get('jquery_date_format'),
				currentText: l('Now'),
				closeText: l('Done'),
				ampm: false,
				amNames: ['AM', 'A'],
				pmNames: ['PM', 'P'],
				timeFormat: 'hh:mm:ss tt',
				timeSuffix: '',
				timeOnlyTitle: l('Choose Time'),
				timeText: l('Time'),
				hourText: l('Hour'),
				onSelect: function(date, dateObj) 
				{
			      	var dateAsObject = $(this).datepicker('getDate'),
			      		date = new Date(dateAsObject),
						m = date.getMonth() + 1,
						d = date.getDate();

					var year = date.getFullYear(),
						month = (String(m).length == 1 ? '0' + String(m) : String(m)),
						day = (String(d).length == 1 ? '0'+String(d) : String(d)),
						hours = date.getHours(),
						minutes = date.getMinutes(),
						seconds = date.getSeconds(),
						mysql_date = year+'-'+month+'-'+day + ' ' + hours + ':' + minutes + ':' + seconds;

					self.setStorage('mysql_date', mysql_date);
				},
				minDate: new Date(),
			});

			win.setContent(template);

			var sleepVal = parseInt(dom.taskSleep.val());
			dom.taskSleep.on('change', function(event) {
				var button = $(this),
					val = parseInt(button.val());

				if ( val < 0 ) {
					val = sleepVal;
				} else {
					sleepVal = val;
				}
				button.val(val);
				self.setStorage('sleep', val);
			});

			dom.taskSmtpTest.on('click', function(event) {
				var selected = smtpSelect.getSelected();

					self.storage.template = dom.taskTemplateSelect.val();

					var email = dom.taskEmailTest.val(),
						smtpId = (selected != null ? selected.data.id_newsletter_pro_smtp : 0),
						message = dom.taskSmtpTestMessage,
						templateName = self.storage.template,
						sendMethod = self.storage.send_method,
						idLang = (self.storage.id_lang ? self.storage.id_lang : box.dataStorage.get('id_selected_lang'));

					box.showAjaxLoader(dom.taskSmtpTest);

					$.postAjax({ 'submit': 'sendTestEmail', sendTestEmail: email, smtpId: smtpId, templateName: templateName, sendMethod: sendMethod, idLang: idLang }).done(function( response ) {
						if( response.status )
							message.empty().show().append('<div class="alert alert-success">'+l('email sent')+'</div>');
						else {
							message.empty().show().append('<div class="alert alert-danger">'+response.msg+'</div>');
						}

						setTimeout( function() { message.hide(); }, 15000);
					}).always(function(){
						box.hideAjaxLoader(dom.taskSmtpTest);
					});
			});


			dom.btnTaskLangSelectTest = $('#task-test-email-lang-select');

			var langSelect = new box.components.LanguageSelect({
				selector: dom.btnTaskLangSelectTest,
				languages: box.dataStorage.get('all_languages'),
				click: function(lang, key) {
					var idLang = Number(lang.id_lang);
					self.storage.id_lang = idLang;
				},
			});

			dom.addTask.on('click', function(event) {
				var message = dom.taskSmtpTestMessage;

				var emails = JSON.stringify(self.storage.emails);
				self.storage.emails = [];
				self.storage.template = dom.taskTemplateSelect.val();

				$.postAjax({'submit': 'addTask', addTask: self.storage, emails: emails}).done(function(response) {
					if (response.errors.length > 0) {
						message.empty().show().append('<div class="alert alert-danger">'+(response.errors.join('<br />'))+'</div>');
					} else {
						var taskList = taskList || NewsletterPro.modules.task.ui.components.taskList;
						win.hide().done(function() {
							taskList.sync();
						});
					}
					setTimeout( function() { message.hide(); }, 15000);
				});
			});

			var dataModel = new gk.data.Model({
				id: 'id_newsletter_pro_task',
			});

			var dataSource = new gk.data.DataSource({
				pageSize: 10,
				transport: {
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getTasks',
						dataType: 'json',
					},
					update: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=updateTask&updateTask',
						dataType: 'json'
					},
					destroy: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=deleteTask&deleteTask',
						type: 'POST',
						dateType: 'json',
						success: function(response, itemData) {
							if(!response)
								alert(l('delete task'));
						},
						error: function(data, itemData) {
							alert(l('delete task'));
						},
						complete: function(data, itemData) {},
					}
				},
				schema: {
					model: dataModel
				},
				trySteps: 2,
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						dataSource.syncStepAvailableAdd(3000, function(){
							dataSource.sync();
						});
					},
				},
			});

			dom.mailMethod.on('change', function() {
				if (dom.mailMethod.is(':checked'))
				{
					self.setStorage('send_method', 'mail');
					dom.smtpSelectContainer.slideUp();
				}

			});

			dom.smtpMethod.on('change', function() {
				if (dom.smtpMethod.is(':checked'))
				{
					self.setStorage('send_method', 'smtp');
					dom.smtpSelectContainer.slideDown();
				}
			});

			dom.taskList.gkGrid({
				dataSource: dataSource,
				selectable: false,
				currentPage: 1,
				pageable: true,
				template: {
					template: function(item, value) 
					 {
						var div = $('<div></div>'),
							data = item.data,
							templateList = getTemplateList(),
							select = $('<select id="template-select-list" class="template-select-list gk-select"></select>');

						function addOption(item) {
							var name = item.name,
								value = item.value,
								selected = item.selected;

							var option = $('<option value="'+value+'" '+(selected ? 'selected="selected"' : '')+'>'+name+'</option>');
							select.append(option);
						}

						function getTemplateList() {
							var list = box.dataStorage.get('templates'),
								objs = [];

							for (var i in list)
							{
								var item = list[i];

								var obj = {
									name: item.name,
									value: item.filename,
									selected: false,
								};

								if ($.trim(obj.value) === $.trim(value)) {
									obj.selected = true;
								}
								objs.push(obj);
							}
							return objs;
						}

						for( var i in templateList) {
							var itemsObj = templateList[i];
							addOption(itemsObj);
						}

						select.on('change', function(event) {
							var val = select.val();
							if (val) {
								item.data.template = val;
								item.update().done(function(response) {
									if (!response)
										alert(l('template not found'));
								});
							}
						});

						if (parseInt(data.status) == 1 && parseInt(data.done) == 0 && parseInt(data.pause) == 0) {
							div.append($('<span class="task-text-p">'+value+'</span>'));
						} else {
							div.append(select);
						}

						return div;
					},

					actions : function(item, value) 
					{
						var data = item.data;

						var button = $('#task-delete').gkButton({
							name: 'delete',
							title: l('delete'),
							className: 'btn btn-default btn-margin task-delete',
							item: item,
							css: { 'display': 'inline-block' },
							command: 'delete',
							confirm: function() 
							{
								return confirm(l('delete record'));
							},
							icon: '<i class="icon icon-trash-o"></i> ',
						});

						var div = $('<div></div>');

						var send = $('#task-send').gkButton({
							name: 'send-task',
							title: l('send'),
							className: 'btn btn-default btn-margin send-task',
							css: { 'display': 'inline-block' },
							click: function(event) {
								syncTask.runInterval(syncTask.speed.fast);
								var id = data.id_newsletter_pro_task;
								send.hide();
								pauseBtn.show();

								$.postAjax({'submit': 'sendTaskAjax', sendTaskAjax: id}).done(function(response) {
									if (response.status)
									{
										send.disable();
										syncTask.syncAjax();
									}
									else
										box.alertErrors(response.errors);
								});
							},
							icon: '<i class="icon icon-send"></i> ',
						});
						send.hide();

						var continueBtn = $('#continue-send').gkButton({
							name: 'continue-task',
							title: l('continue'),
							className: 'btn btn-default btn-margin continue-task',
							css: {'display': 'inline-block'},
							click: function(event) {
								syncTask.runInterval(syncTask.speed.fast);
								var id = data.id_newsletter_pro_task;

								togglePauseContinue();

								$.postAjax({'submit': 'continueTaskAjax', 'id': id}).done(function(response) {
									syncTask.syncAjax();
								});
							},
							icon: '<i class="icon icon-refresh"></i> ',
						});

						continueBtn.hide();

						var pauseBtn = $('#pause-task').gkButton({
							name: 'pause-task',
							title: l('pause'),
							className: 'btn btn-default btn-margin pause-task',
							css: { 'display': 'inline-block' },
							click: function(event) {
								syncTask.runInterval(syncTask.speed.fast);
								var id = data.id_newsletter_pro_task;

								togglePauseContinue();

								$.postAjax({'submit': 'pauseTask', 'id': id}).done(function(response) {
									syncTask.syncAjax();
								});
							},
							icon: '<i class="icon icon-pause"></i> ',
						});
						pauseBtn.hide();

						function togglePauseContinue() {
							if (continueBtn.is(':visible')) {
								continueBtn.hide();
								pauseBtn.css({ 'display': 'inline-block' });
							} else {
								continueBtn.css({ 'display': 'inline-block' });
								pauseBtn.hide();
							}
							send.hide();
						}

						div.append(continueBtn);
						div.append(pauseBtn);
						div.append(send);

						if (parseInt(data.status) == 1 && parseInt(data.done) == 0) {
							if (parseInt(data.pause))
								continueBtn.css({ 'display': 'inline-block' });
							else
								pauseBtn.css({ 'display': 'inline-block' });
						} else {
							send.css({ 'display': 'inline-block' });
						}

						div.append(button);
						return div;
					},

					smtp: function(item, value) {
						var opt = getOptions(NewsletterPro.dataStorage.get('all_smtp')),
							select = $('<select id="smtp-select-list" class="gk-select" style="min-width: 160px !important; width: auto;"></select>'),
							id_smtp = item.data.id_newsletter_pro_smtp,
							options = setSelected(opt, {key:'id_newsletter_pro_smtp', value:id_smtp});

						var smtpSelect = smtp.ui.SelectOption({
								name: 'taskSmptSelectList',
								template: select,
								className: 'gk-smtp-select',
								options: options,
								onChange: function() {

									var selected = smtpSelect.getSelected();
									if (selected != null) {
										var data = selected.data,
											id_newsletter_pro_smtp = data.id_newsletter_pro_smtp;

										item.data.id_newsletter_pro_smtp = id_newsletter_pro_smtp;

										item.update().done(function(response) {
											if (!response)
												alert(l('smtp not update'));
										});
									}
								}
							});

						if (isMail()) {
							smtpSelect.template.hide();
						}

						function isMail() {
							if (item.data.send_method == 'mail')
								return true;
							return false;
						}

						var sendMethodSelect = $('<select id="send-method-select" class="send-method-select gk-select" autocomplete="off"><option value="smtp">SMTP</option><option value="mail" '+(isMail() ? 'selected="selected"' :'')+'>mail()</option></select>')

						sendMethodSelect.on('change', function(){
							var send_method = sendMethodSelect.val();
							if (send_method === 'mail') {
								smtpSelect.template.hide();
								item.data.id_newsletter_pro_smtp = 0;
							} else {

								var selected = smtpSelect.getSelected();
								if (selected != null) {
									var data = selected.data,
										id_newsletter_pro_smtp = data.id_newsletter_pro_smtp;

									item.data.id_newsletter_pro_smtp = id_newsletter_pro_smtp;
								}
								smtpSelect.template.show();
							}

							item.data.send_method = send_method;
							item.update().done(function(response) {
								if (!response)
									alert(l('send method not update'));
							});
						});

						var div = $('<div></div>');
						var sendTestListMessage = $('<span class="send-test-list-message" style="margin-top: 4px; display: inline-block;"></span>');

						var sendTest = $('#send-test-list').gkButton({
							name: 'send-test-list',
							className: 'btn btn-default task-smtp-test-list',
							title: l('test'),
							click: function()
							{
								var selected = smtpSelect.getSelected();
								var smtpId = (selected != null ? selected.data.id_newsletter_pro_smtp : 0),
									email = NewsletterPro.dataStorage.get('configuration.PS_SHOP_EMAIL'),
									templateName = item.data.template,
									sendMethod = item.data.send_method;

								sendTestListMessage.empty().show().append('<span class="ajax-loader">&nbsp;</span>');

								$.postAjax({'submit': 'sendTestEmail', sendTestEmail:email, smtpId: smtpId, templateName: templateName, sendMethod: sendMethod}).done(function(response) {

									if( response.status )
										sendTestListMessage.empty().show().append('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
									else 
									{
										sendTestListMessage.empty().show().append('<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>');
										NewsletterPro.alertErrors(response.msg);
									}

									setTimeout( function() { sendTestListMessage.hide(); }, 10000);
								});
							},
							icon: '<i class="icon icon-envelope"></i> ',
						});

						var css = {'float': 'left'};
						select.css(css);
						sendTest.css(css);
						sendTestListMessage.css(css);

						function getSelectVal()
						{
							return sendMethodSelect.val() === 'mail' ? 'mail()' : 'SMTP';
						}

						if (parseInt(item.data.status) == 1 && parseInt(item.data.done) == 0 && parseInt(item.data.pause) == 0) {
							div.append( '<span class="task-text-p">' + (getSelectVal()) + '</span>' );
						} else {
							div.append(sendMethodSelect);
							div.append(select);
							div.append(sendTest);
							div.append(sendTestListMessage);
						}
						return div;
					},

					active: function(item, value) 
					{
						var id = item.data.id_newsletter_pro_task;
						function isActive() {
							return String(item.data.active) === '0' ? false : true;
						}

						var activeToggle = $('<a class="status-button" href="javascript:{}"></a>');

						var enabledHTML = '<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>',
							disableHTML = '<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>';

						if (isActive())
							activeToggle.html(enabledHTML);
						else
							activeToggle.html(disableHTML);

						activeToggle.toggleActive =	function ()
						{
							var button = activeToggle;
							item.data.active = isActive() ? 0 : 1;

							if (!isActive()) {
								button.html(disableHTML);
							} else {
								button.html(enabledHTML);
							}
						}

						activeToggle.on('click', function() {
							var button = activeToggle;
							button.toggleActive();

							item.update().done(function(response) {
								if (!response)
									button.toggleActive();
							});
						});
						return activeToggle;
					},

					date_start: function(item, value) 
					{

						var datePicker,
							tempDate = new Date(),
							tempYear = tempDate.getFullYear(),
							tempMonth = tempDate.getMonth(),
							tempDay = tempDate.getDate(),
							data = item.data,
							date = new Date(item.data.date_start),
							// minDate = ( date.getTime() <= new Date().getTime() ? date : new Date() ),
							minDate = new Date(tempYear, tempMonth, tempDay, 0, 0, 0),
							dateFormat = box.dataStorage.get('jquery_date_format');

						if (parseInt(data.status) == 1 && parseInt(data.done) == 0 && parseInt(data.pause) == 0) 
						{
							datePicker = $('<span>'+($.datepicker.formatDate(dateFormat, date))+'</span>');
						} 
						else 
						{
							datePicker = $('<input type="text" class="task-list-date-input gk-input" style="position: relative; z-index: 100000;">').datetimepicker({
								prevText: '',
								nextText: '',
								dateFormat: dateFormat,
								currentText: l('Now'),
								closeText: l('Done'),
								ampm: false,
								amNames: ['AM', 'A'],
								pmNames: ['PM', 'P'],
								timeFormat: 'hh:mm:ss tt',
								timeSuffix: '',
								timeOnlyTitle: l('Choose Time'),
								timeText: l('Time'),
								hourText: l('Hour'),
								onSelect: function(date, dateObj) 
								{
									var dateAsObject = $(this).datepicker('getDate'),
							      		dateObject = new Date(dateAsObject),
										m = dateObject.getMonth() + 1,
										d = dateObject.getDate();

									var year = dateObject.getFullYear(),
										month = (String(m).length == 1 ? '0' + String(m) : String(m)),
										day = (String(d).length == 1 ? '0'+String(d) : String(d)),
										hours = dateObject.getHours(),
										minutes = dateObject.getMinutes(),
										seconds = dateObject.getSeconds(),
										mysql_date = year+'-'+month+'-'+day + ' ' + hours + ':' + minutes + ':' + seconds;

									item.data.date_start = mysql_date;

									item.update().done(function(response) {
										if (!response)
											alert(l('date not changed'));
									});
								},
								minDate: minDate,
							});

							datePicker.datetimepicker('setDate', date);
						}

						return datePicker;
					},

					status: function(item, value) 
					{
						var div = $('<div></div>'),
							data = item.data,
							status = $('<span class="task-emails-status"> ( '+parseInt(data.emails_count)+' ) '+data.status+' </span>'),
							count = $('<span class="task-emails-count"> ( <span class="count">'+parseInt(data.emails_count)+'</span> ) emails </span>'),
							error = $('<span class="task-emails-error"> ( <span class="count">'+parseInt(data.emails_error)+'</span> ) </span>'),
							success = $('<span class="task-emails-success"> ( <span class="count">'+parseInt(data.emails_success)+'</span> ) </span>'),
							error_msg = $('<a href="javascript:{}" class="task-error-msg" style="display:none;"></a>'),
							messages = getMessage(item.data.error_msg);

						function getMessage(obj) {
							var arr = [];
							for (var i in obj)
								arr.push(obj[i]);

							if (arr.length > 0)
								return arr.join('<br />');
							return false;
						}

						if (messages) {
							error_msg.show();
							error_msg.on('click', function() {

							var winMessage = ui.TaskWindow({
									width: 425,
									className: 'gk-task-window',
									show: function(win) {},
								});

							winMessage.setHeader(l('errors'));
							winMessage.setContent('<span class="error-msg" style="float: none;">'+(messages.replace(/\\'/g, '"'))+'</span>');
							winMessage.show();

							});
						}

						if (parseInt(data.status) === 1) {
							div.append(count);
							div.append(success);
							div.append(error);

							function updateInitRow(id_task, data) {
								var pIndex = syncTask.progressIds.indexOf(id_task);

								if (pIndex == -1)
									syncTask.progressIds.push(id_task);
							}

							updateInitRow(data.id_newsletter_pro_task, data);

						} else {
							div.append(status);
						}
						div.append(error_msg);

						return div;
					}
				}
			});

			var dataModelSendHistory = new gk.data.Model({
				id: 'id_newsletter_pro_send',
			});

			var dataSourceSendHistory = new gk.data.DataSource({
				pageSize: 10,
				transport: {
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getSendHistory',
						dataType: 'json',
					},

					destroy: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=deleteSendHistory&deleteSendHistory',
						dateType: 'json',
						success: function(response, itemData)
						{
							if(!response)
								alert(l('delete send history'));
						},
						error: function(data, itemData)
						{
							alert(l('delete send history'));
						},
						complete: function(data, itemData)
						{

						},
					}

				},
				schema: {
					model: dataModelSendHistory
				},
				trySteps: 2,
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						dataSourceSendHistory.syncStepAvailableAdd(3000, function(){
							dataSourceSendHistory.sync();
						});
					},
				},
			});

			function isSendNewsletterInProgress(func)
			{
				$.postAjax({'submit': 'isSendNewsletterInProgress'}).done(function(id){
					if (id)
					{
						var conf = confirm('The newsletter is in progress. Do you want to stop the sending process before to proceed?');

						if (conf)
							NewsletterProControllers.SendController.stopNewsletters().done(function(){
								return func(false);
							});
						else
						{
							return func(true);
						}
					}
					else
						return func(false);
				});
			}

			dom.sendHistory.gkGrid({
				dataSource: dataSourceSendHistory,
				selectable: false,
				currentPage: 1,
				pageable: true,
				template: {
					actions: function(item, value) 
					{
						var data = item.data,
							div = $('<div></div>'),
							steps = ( typeof item.data.steps !== 'undefined' && item.data.steps ? item.data.steps.split(',') : null ),
							detailsContent = $('<div class="detail-content"></div>'),
							id_history = parseInt(item.data.id_newsletter_pro_tpl_history),
							stepsButtons,
							winDetails;

						function exportCsv(idHisotry, emailsToSend, emailsSent) {
							var defaultSeparator = ';',
								exportForm = $('\
									<form id="' + NewsletterPro.uniqueId() + '" method="POST" action="' + NewsletterPro.dataStorage.get('ajax_url') + '#history">\
										<input type="hidden" name="export_send_history" value="1">\
										<input type="hidden" name="id_history" value="' + idHisotry + '">\
										<input type="hidden" name="export_emails_to_send" value="' + emailsToSend + '">\
										<input type="hidden" name="export_emails_sent" value="' + emailsSent + '">\
										<input type="hidden" name="csv_separator" value="' + defaultSeparator + '">\
									</form>\
								'),
								separator = prompt(l('CSV Separator'), defaultSeparator);

							if (separator == null) {
								return;
							}

							separator = $.trim(separator);

							if (separator == ';' || separator == ',') {
								
								exportForm.find('input[name="csv_separator"]').val(separator);
								exportForm.submit();
							} else {
								alert(l('Invalid CSV separator.'));
								return;
							}
						}

						function getDetail(id) {

							$.postAjax({'submit': 'getSendHistoryDetail', getSendHistoryDetail: id},'html').done(function(response){
								detailsContent.html(response);

								var localDom = {
									btnResend: detailsContent.find('#np-btn-resend-send'),
									checkboxResendLeft: detailsContent.find('#resend-left-list-send'),
									checkboxResendUndelivered: detailsContent.find('#resend-undelivered-list-send'),
									exportSend: detailsContent.find('#np-btn-export-send-history'),
									exportSendRem: detailsContent.find('#np-btn-export-send-history-rem'),
								};

								localDom.exportSendRem.on('click', function(e) {
									exportCsv(id_history, 1, 0);
								});

								localDom.exportSend.on('click', function(e) {
									exportCsv(id_history, 0, 1);
								});

								// add events here
								localDom.btnResend.on('click', function(e){

									isSendNewsletterInProgress(function(stopFunction){

										if (stopFunction)
											return;

										box.showAjaxLoader(localDom.btnResend);
										$.postAjax({'submit': 'resendSendHistory', id: id_history, resendLeft: Number(localDom.checkboxResendLeft.is(':checked')), resendUndelivered: Number(localDom.checkboxResendUndelivered.is(':checked'))}).done(function(response) {
											if (!response.success)
												box.alertErrors(response.errors);
											else
											{
												if (typeof winDetails !== 'undefined')
													winDetails.hide();

												NewsletterProComponents.objs.tabItems.trigger('tab_newsletter_5');
												$('html, body').animate({
													scrollTop: parseInt($('#emails-to-send').offset().top) - 120
												}, 1000);

												NewsletterProControllers.SendController.prepareEmails(response.emails);
											}
										}).always(function(){
											box.hideAjaxLoader(localDom.btnResend);
										}); // end of postAjax resendSendHistory

									});
								});

							});
						}

						function createButton(cfg) {

							var button = ({
									id: null,
									instance: null,
									init: function(cfg) {
										var self = this;
											id = cfg.step,
											count = cfg.count;

										self.id = id;
										self.instance = $('<a href="javascript:{}" class="history-details-steps" data-id="'+id+'">'+(l('step'))+' '+(count)+'</a>');

										if (count == 1) {
											getDetail(self.id);
											self.instance.addClass('selected');
										}

										(function addEvents(instance) {
											instance.on('click', function(event){
												click.call(instance, self ,event);
											});
										}(self.instance));

										function click(obj) {
											if (stepsButtons != null) {
												var buttons = stepsButtons.find('a.selected');
												$.each(buttons, function(index, button) {
													$(button).removeClass('selected');
												});
											}
											this.addClass('selected');
											getDetail(obj.id);
										}

										return self;
									},
									getInstance: function() {
										return this.instance;
									}
								}.init(cfg));
							return button;
						}

						function createButtons () {
							var count = 0,
								buttons = $('<div class="detail-buttons"></div>');

							$.each(steps, function(index, step) {
								var button = createButton({step:step, count: ++count});
								buttons.append(button.getInstance());
							});
							return buttons;
						}

						var button = $('#task-history-delete').gkButton({
							name: 'delete',
							title: l('delete'),
							className: 'btn btn-default btn-margin task-delete',
							item: item,
							command: 'delete',
							confirm: function() 
							{
								return confirm(l('delete record'));
							},
							icon: '<i class="icon icon-trash-o"></i> ',
						});

						var details = $('#send-history-details').gkButton({
							name: 'send-history-details',
							title: l('details'),
							className: 'btn-margin send-history-details',

							click: function(event) {
								var content = $('<div></div>');
								stepsButtons = createButtons();

								winDetails = ui.TaskWindow({
										width: 800,
										height: 500,
										className: 'gk-task-window-details',
										show: function(win) {},
									});

								winDetails.setHeader(l('details'));
								if (steps.length > 1)
									content.append(stepsButtons);
								content.append(detailsContent);
								winDetails.setContent(content);

								winDetails.show();								
							},
							icon: '<i class="icon icon-info-circle"></i> ',
						});

						var view = $('#send-history-view').gkButton({
							name: 'send-history-view',
							title: l('view'),
							className: 'btn btn-default btn-margin task-history-view',

							click: function(event) 
							{
								var content = $('<div></div>'),
									open,
									title,
									viewTemplate;
								var winDetails = gkWindow({
										width: 800,
										height: 500,
										className: 'gk-task-window-view',
										show: function(win) {},
									});

								var header = $('<span></span>');
								title = l('view template');
								open = $('<a href="javascript:{}" target="_blank" class="history-details-steps view-template-in-br-button">'+l('veiw in a new window')+'</a>');
								header.append(title);
								header.append(open);
								winDetails.setHeader(header);

								viewTemplate = $('<div class="view-template-in-br"></div>');

								$.postAjax({'submit': 'renderTemplateHistory', renderTemplateHistory: id_history}).done(function(response) {
									open.attr('href', response.url);

									var contentIframe = $('<iframe style="display: block; vertical-align: top; height: 462px;" scrolling="yes" src="'+(response.url)+'"> </iframe>');

									viewTemplate.html(contentIframe);

									var table = viewTemplate.find('table').first();
									if (table.length && content.parent().length) {
										content.parent().css({
											'background-color': table.css('background-color')
										});
									}
								});

								content.append(viewTemplate);

								winDetails.setContent(content);
								winDetails.show();
							},
							icon: '<i class="icon icon-eye"></i> ',
						});

						if (id_history > 0)
							div.append(view);

						div.append(details);
						div.append(button);

						return div;
					},

					emails_count: function(item, value) 
					{
						return '<span style="padding: 0; margin: 0; font-weight: bold; float: none;">'+(parseInt(value) ? value : '0')+'</span>';
					},

					emails_success: function(item, value) 
					{
						return '<span class="success-msg" style="padding: 0; margin: 0; float: none;">'+(parseInt(value) ? value : '0')+'</span>';
					},

					emails_error: function(item, value) 
					{
						return '<span class="error-msg" style="padding: 0; margin: 0; float: none;">'+(parseInt(value) ? value : '0')+'</span>';
					},

					unsubscribed: function(item, value)
					{
						var button = $('<a href="javascript:{}" class="btn btn-default task-error-msg-text" style="display:none;"><span class="icon"></span><span style="font-weight: bold; color: #F00;">'+value+'</span></a>');

						if (parseInt(value) <= 0 )
							return value;

						var detailsContent = $('<div></div>');

						function getDetail(id) 
						{
							$.postAjax({'submit': 'getUnsubscribedDetails', id_newsletter: id},'html').done(function(response){
								detailsContent.html(response);
							});
						}

						button.css({'display':'inline-block'});

						button.on('click', function(){
							getDetail(item.data.id_newsletter_pro_tpl_history);

							var content = $('<div></div>');
							var winDetails = ui.TaskWindow({
									width: 800,
									height: 500,
									className: 'gk-task-window-details',
									show: function(win) {},
								});

							winDetails.setHeader(l('unsubscribed'));

							content.append(detailsContent);
							winDetails.setContent(content);
							winDetails.show();
						});

						return button;
					},

					fwd_unsubscribed: function(item, value)
					{
						var button = $('<a href="javascript:{}" class="btn btn-default task-error-msg-text" style="display:none;"><span class="icon"></span><span style="font-weight: bold; color: #F00;">'+value+'</span></a>');

						if (parseInt(value) <= 0 )
							return value;

						var detailsContent = $('<div></div>');

						function getDetail(id) 
						{
							$.postAjax({'submit': 'getTaskFwdUnsubscribedDetails', id_newsletter: id},'html').done(function(response){
								detailsContent.html(response);
							});
						}

						button.css({'display':'inline-block'});

						button.on('click', function(){
							getDetail(item.data.id_newsletter_pro_tpl_history);

							var content = $('<div></div>');
							var winDetails = ui.TaskWindow({
									width: 800,
									height: 500,
									className: 'gk-task-window-details',
									show: function(win) {},
								});

							winDetails.setHeader(l('unsubscribed'));

							content.append(detailsContent);
							winDetails.setContent(content);
							winDetails.show();
						});

						return button;
					},

					error_msg: function(item, value) 
					{
						var error_msg = $('<a href="javascript:{}" class="btn btn-default task-error-msg-text" style="display:none;"><span class="icon"></span>'+(l('View'))+'</a>'),
							messages = getMessage(value);

						function getMessage(obj) 
						{
							if (typeof value === 'object') {
								var arr = [];
								for (var i in obj)
									arr.push(obj[i]);

								if (arr.length > 0)
									return arr.join('<br />');
							}
							else if (typeof value === 'string')
							{
								return value;
							}

							return false;
						}

						if (messages) {
							error_msg.css({display:'inline-block'});
							error_msg.show();
							error_msg.on('click', function() {

								var winMessage = ui.TaskWindow({
										width: 425,
										className: 'gk-task-window',
										show: function(win) {},
									});

								winMessage.setHeader(l('errors'));
								winMessage.setContent('<span class="error-msg" style="float: none;">'+(messages.replace(/\\'/g, '"'))+'</span>');
								winMessage.show();
							});
						}

						return error_msg;
					},
				}
			});

			var dataModelHistory = new gk.data.Model({
				id: 'id_newsletter_pro_task',
			});

			var dataSourceTaskHistory = new gk.data.DataSource({
				pageSize: 10,
				transport: {
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getTasksHistory',
						dataType: 'json',
					},

					destroy: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=deleteTask&deleteTask',
						type: 'DELETE',
						dateType: 'json',
						success: function(response, itemData) {
							if(!response)
								alert(l('delete task'));
						},
						error: function(data, itemData) {
							alert(l('delete task'));
						},
						complete: function(data, itemData) {},
					}
				},
				schema: {
					model: dataModelHistory
				},
				trySteps: 2,
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						dataSourceTaskHistory.syncStepAvailableAdd(3000, function(){
							dataSourceTaskHistory.sync();
						});
					},
				},
			});

			dom.taskHistory.gkGrid({
				dataSource: dataSourceTaskHistory,
				selectable: false,
				currentPage: 1,
				pageable: true,
				template: {
					actions: function(item, value) 
					{
						var data = item.data,
							div = $('<div></div>'),
							steps = ( typeof item.data.steps !== 'undefined' ? item.data.steps.split(',') : null ),
							detailsContent = $('<div class="detail-content"></div>'),
							id_history = parseInt(item.data.id_newsletter_pro_tpl_history),
							stepsButtons;

						function getDetail(id) {
							$.postAjax({'submit': 'getTasksHistoryDetail', getTasksHistoryDetail: id},'html').done(function(response){
								detailsContent.html(response);
							});
						}

						function createButton(cfg) {

							var button = ({
									id: null,
									instance: null,
									init: function(cfg) {
										var self = this;
											id = cfg.step,
											count = cfg.count;

										self.id = id;
										self.instance = $('<a href="javascript:{}" class="history-details-steps" data-id="'+id+'">'+(l('step'))+' '+(count)+'</a>');

										if (count == 1) {
											getDetail(self.id);
											self.instance.addClass('selected');
										}

										(function addEvents(instance) {
											instance.on('click', function(event){
												click.call(instance, self ,event);
											});
										}(self.instance));

										function click(obj) {
											if (stepsButtons != null) {
												var buttons = stepsButtons.find('a.selected');
												$.each(buttons, function(index, button) {
													$(button).removeClass('selected');
												});
											}
											this.addClass('selected');
											getDetail(obj.id);
										}

										return self;
									},
									getInstance: function() {
										return this.instance;
									}
								}.init(cfg));
							return button;
						}

						function createButtons () {
							var count = 0,
								buttons = $('<div class="detail-buttons"></div>');

							$.each(steps, function(index, step) {
								var button = createButton({step:step, count: ++count});
								buttons.append(button.getInstance());
							});
							return buttons;
						}

						var button = $('#task-history-delete').gkButton({
							name: 'delete',
							title: l('delete'),
							className: 'btn btn-default btn-margin task-delete',
							item: item,
							command: 'delete',
							confirm: function() 
							{
								return confirm(l('delete record'));
							},
							icon: '<i class="icon icon-trash-o"></i> ',
						});

						var details = $('#task-history-details').gkButton({
							name: 'history-details',
							title: l('details'),
							className: 'btn-margin task-history-details',

							click: function(event) {
								var content = $('<div></div>');
								stepsButtons = createButtons();
								var winDetails = ui.TaskWindow({
										width: 800,
										height: 500,
										className: 'gk-task-window-details',
										show: function(win) {},
									});

								winDetails.setHeader(l('details'));
								if (steps.length > 1)
									content.append(stepsButtons);
								content.append(detailsContent);
								winDetails.setContent(content);
								winDetails.show();
							},
							icon: '<i class="icon icon-info-circle"></i> ',
						});

						var view = $('#task-history-view').gkButton({
							name: 'history-view',
							title: l('view'),
							className: 'btn btn-default btn-margin task-history-view',

							click: function(event) 
							{
								var content = $('<div></div>'),
									open,
									viewTemplate;

								var winDetails = gkWindow({
										width: 800,
										height: 500,
										className: 'gk-task-window-view',
										show: function(win) {},
									});

							var header = $('<span></span>');
								title = l('view template');
								open = $('<a href="javascript:{}" target="_blank" class="history-details-steps view-template-in-br-button">'+l('veiw in a new window')+'</a>');
								header.append(title);
								header.append(open);
								winDetails.setHeader(header);

								viewTemplate = $('<div class="view-template-in-br"></div>');

								$.postAjax({'submit': 'renderTemplateHistory', renderTemplateHistory: id_history}).done(function(response) {
									open.attr('href', response.url);

									var contentIframe = $('<iframe style="display: block; vertical-align: top; height: 462px;" scrolling="yes" src="'+(response.url)+'"> </iframe>');
									viewTemplate.html(contentIframe);

									var table = viewTemplate.find('table').first();
									if (table.length && content.parent().length) {
										content.parent().css({
											'background-color': table.css('background-color')
										});
									}
								});

								content.append(viewTemplate);

								winDetails.setContent(content);
								winDetails.show();
							},
							icon: '<i class="icon icon-eye"></i> ',
						});

						if (id_history > 0)
							div.append(view);

						div.append(details);
						div.append(button);
						return div;
					},

					emails_count: function(item, value) {
						return '<span style="padding: 0; margin: 0; font-weight: bold; float: none;">'+(parseInt(value) ? value : '0')+'</span>';
					},

					emails_success: function(item, value) {
						return '<span class="success-msg" style="padding: 0; margin: 0; float: none;">'+(parseInt(value) ? value : '0')+'</span>';
					},

					emails_error: function(item, value) {
						return '<span class="error-msg" style="padding: 0; margin: 0; float: none;">'+(parseInt(value) ? value : '0')+'</span>';
					},

					unsubscribed: function(item, value)
					{
						var button = $('<a href="javascript:{}" class="btn btn-default task-error-msg-text" style="display:none;"><span class="icon"></span><span style="font-weight: bold; color: #F00;">'+value+'</span></a>');

						if (parseInt(value) <= 0 )
							return value;

						var detailsContent = $('<div></div>');

						function getDetail(id) 
						{
							$.postAjax({'submit': 'getTaskUnsubscribedDetails', id_newsletter: id},'html').done(function(response){
								detailsContent.html(response);
							});
						}

						button.css({'display':'inline-block'});

						button.on('click', function(){
							getDetail(item.data.id_newsletter_pro_tpl_history);

							var content = $('<div></div>');
							var winDetails = ui.TaskWindow({
									width: 800,
									height: 500,
									className: 'gk-task-window-details',
									show: function(win) {},
								});

							winDetails.setHeader(l('unsubscribed'));

							content.append(detailsContent);
							winDetails.setContent(content);
							winDetails.show();
						});

						return button;
					},

					fwd_unsubscribed: function(item, value)
					{
						var button = $('<a href="javascript:{}" class="btn btn-default task-error-msg-text" style="display:none;"><span class="icon"></span><span style="font-weight: bold; color: #F00;">'+value+'</span></a>');

						if (parseInt(value) <= 0 )
							return value;

						var detailsContent = $('<div></div>');

						function getDetail(id) 
						{
							$.postAjax({'submit': 'getTaskFwdUnsubscribedDetails', id_newsletter: id},'html').done(function(response){
								detailsContent.html(response);
							});
						}

						button.css({'display':'inline-block'});

						button.on('click', function(){
							getDetail(item.data.id_newsletter_pro_tpl_history);

							var content = $('<div></div>');
							var winDetails = ui.TaskWindow({
									width: 800,
									height: 500,
									className: 'gk-task-window-details',
									show: function(win) {},
								});

							winDetails.setHeader(l('unsubscribed'));

							content.append(detailsContent);
							winDetails.setContent(content);
							winDetails.show();
						});

						return button;
					},

					error_msg: function(item, value) 
					{
						var error_msg = $('<a href="javascript:{}" class="btn btn-default task-error-msg-text" style="display:none;"><span class="icon"></span>'+(l('View'))+'</a>'),
							messages = getMessage(value);

						function getMessage(obj) 
						{
							if (typeof value === 'object') 
							{
								var arr = [];
								for (var i in obj)
									arr.push(obj[i]);

								if (arr.length > 0)
									return arr.join('<br />');
							}
							else if (typeof value === 'string')
							{
								return value;
							}

							return false;
						}

						if (messages) 
						{
							error_msg.css({display:'inline-block'});
							error_msg.show();
							error_msg.on('click', function() {

								var winMessage = ui.TaskWindow({
										width: 425,
										className: 'gk-task-window',
										show: function(win) {},
									});

								winMessage.setHeader('Errors');
								winMessage.setContent('<span class="error-msg" style="float: none;">'+(messages.replace(/\\'/g, '"'))+'</span>');
								winMessage.show();
							});
						}

						return error_msg;
					}
				}
			});

			dom.clearTaskHistory.on('click', function() {
				$.postAjax({'submit': 'clearTaskHistory', clearTaskHistory:true}).done(function(response) {
					if(response.status) {
						var taskHistory = dataSourceTaskHistory || NewsletterPro.modules.task.ui.components.taskHistory;
						taskHistory.sync();
					} else {
						alert(response.msg);
					}
				});
			});

			dom.clearSendHistory.on('click', function() {
				$.postAjax({'submit': 'clearSendHistory', clearSendHistory:true}).done(function(response) {
					if(response.status) {
						var sendHistory = dataSourceSendHistory || NewsletterPro.modules.task.ui.components.sendHistory;
						sendHistory.sync();
					} else {
						alert(response.msg);
					}
				});
			});

			dom.clearSendDetails.on('click', function(){
				box.showAjaxLoader(dom.clearSendDetails);

				$.postAjax({'submit': 'clearSendHistoryDetails'}).done(function(response){
					if (!response.status)
						box.alertErrors(response.errors);

				}).always(function(){
					box.hideAjaxLoader(dom.clearSendDetails);
				});
			});

			dom.clearTaskDetails.on('click', function(){
				box.showAjaxLoader(dom.clearTaskDetails);

				$.postAjax({'submit': 'clearTaskHistoryDetails'}).done(function(response){
					if (!response.status)
						box.alertErrors(response.errors);

				}).always(function(){
					box.hideAjaxLoader(dom.clearTaskDetails);
				});
			});

			dom.taskMoreInfoButton.on('click', function(event) {
				dom.taskMoreInfo.toggle();
				if (dom.taskMoreInfo.is(':visible')) {
					$(this).text(l('less info'));
				} else {
					$(this).text(l('more info'));
				}
			});

			ui.add('taskList', dataSource);
			ui.add('taskHistory', dataSourceTaskHistory);
			ui.add('sendHistory', dataSourceSendHistory);
			ui.add('taskWindow', win);
		});

		return self;
	},

	initStorage: function() {
		var box = NewsletterPro;

		this.storage = {
			template: this.dom.taskTemplateSelect.val(),
			emails: [],
			mysql_date: '',
			smtp: '',
			id_lang: null,
			id_newsletter_pro_smtp: 0,
			sleep: parseInt(this.dom.taskSleep.val()) || 5,
			send_method: 'mail',
		};
	},

	setStorage: function(name, value) {
		this.storage[name] = value;
	},

	ready: function(func) {
		var self = this;

		$(document).ready(function(){			

			var template = $('#task-template'),
				templateHTML = $($.trim(template.html()));

			if (typeof templateHTML[1] !== 'undefined')
				templateHTML = $(templateHTML[1]);

			self.dom = {
				chooseNewsletterTemplate : $('#change-newsletter-template'),
				taskButton: $('#new-task'),
				testEmailInput: $('#test-email-input'),
				taskList: $('#task-list'),
				taskHistory: $('#task-history'),
				clearTaskHistory: $('#clear-task-history'),
				clearTaskDetails: $('#clear-task-details'),

				clearSendHistory: $('#clear-send-history'),
				clearSendDetails: $('#clear-send-details'),

				sendHistory: $('#send-history'),

				taskMoreInfo: $('#task-more-info'),
				taskMoreInfoButton: $('#task-more-info-button'),

				taskTemplate: templateHTML,
				datepicker: templateHTML.find('#task-datepicker'),

				emailsCount: templateHTML.find('#selected_emails_count'),
				taskSmptSelect: templateHTML.find('#task-smtp-select'),
				taskTemplateSelect: templateHTML.find('#task-select-template'),
				taskSmtpTest: templateHTML.find('#task-smtp-test'),
				taskSmtpTestMessage: templateHTML.find('#task-smtp-test-message'),
				taskEmailTest: templateHTML.find('#task-email-test'),
				addTask: templateHTML.find('#add-task'),
				taskSleep: templateHTML.find('#task-sleep'),

				btnTaskLangSelectTest: $('#task-test-email-lang-select'),

				mailMethod: templateHTML.find('#task-mail-method'),
				smtpMethod: templateHTML.find('#task-smtp-method'),
				smtpSelectContainer: templateHTML.find('#div-task-smtp-select'),
			};

			func(self.dom);
		});
	},

	getStorage: function() {
		return this.storage;
	},

	taskClick: function(self) 
	{
		var dom = self.dom;
		var emails = self.getEmails();

		if (emails.length == 0) 
		{
			alert(this.data('trans-noemail'));
			return false;
		}

		self.setStorage('emails', emails);

		var ui = self.ui,
			win = ui.components.taskWindow;

		win.show();

		$.postAjax({'submit_template_controller': 'getNewsletterTemplates'}).done(function(response){
			if (response.length)
			{
				dom.taskTemplateSelect.empty();
				for (var i = 0; i < response.length; i++)
				{
					var template = response[i],
						option = '<option value="'+template.filename+'" '+(template.selected ? 'selected="selected"' : '')+'>'+template.name+'</option>';

					dom.taskTemplateSelect.append(option);
				}
			}
		});

		var smtp = NewsletterPro.modules.smtp.ui.components.taskSmptSelect,
			selected = smtp.getSelected();

		if (selected != null)
			self.setStorage('id_newsletter_pro_smtp', selected.data.id_newsletter_pro_smtp);
	},

	getEmails: function() {
		return NewsletterProControllers.SendController.getSelectedEmails();
	},

	ui: ({
		components: {},

		init: function() {
			return this;
		},

		add: function(name, value) {
			this.components[name] = value;
		},

		TaskWindow: function TaskWindow(cfg) {
			if (!(this instanceof TaskWindow))
				return new TaskWindow(cfg);

			var self = this;
				task = NewsletterPro.modules.task,
				l = NewsletterPro.translations.l(NewsletterPro.translations.modules.task);

			function setTemplate() {
				var background = $('<div class="gk-background"></div>'),
					template = $('<div><div class="gk-header"><span class="gk-title"></span><a href="javascript:{}" class="gk-close"><i class="icon icon-remove"></i></a></div><div class="bootstrap gk-content"></div></div>'),
					body = $('body'),
					width = cfg.width || 0,
					height = cfg.height || 0;

				background.css({
					width: '100%',
					height: '100%',
					top: 0,
					left: 0,
					position: 'fixed',
					display: 'none',
					'z-index': '99999',
				});

				background.appendTo(body);

				template.css({
					width: cfg.width || 'auto',
					height: cfg.height || 'auto',
					position: 'fixed',
					left: width != 0 ? body.width() / 2 - width / 2 : body.width() / 2,
					top: height != 0 ? $(window).height() / 2 - height / 2 : $(window).height() / 2,
					display: 'none',
					'z-index': '999999',
				});

				template.header = template.find('.gk-header');
				template.content = template.find('.gk-content');
				template.close = template.find('.gk-close');
				template.background = background;

				template.addClass('gk-task-window');
				template.addClass(cfg.className);
				background.addClass(cfg.className+'-'+'background');

				template.appendTo(body);

				addEvents(template, background);

				return template;
			}

			function addEvents(template, background) {
				template.close.on('click', function(event) {
					self.hide();
				});

				background.on('click', function(){
					template.close.trigger('click');
				});

				$(window).resize(function(event) {
					self.resetPosition();
				});
			}

			this.template = setTemplate();

			this.resetPosition = function() {
				self.template.css({
					left: $('body').width() / 2 - self.template.width() / 2,
					top: $(window).height() / 2 - self.template.height() / 2,
				});
			}

			this.setHeader = function(value) {
				this.template.header.find('.gk-title').html(value);
			};

			this.setContent = function(value) {
				this.template.content.html(value);
			};

			this.hide = function() {
				this.template.fadeOut(200);
				return this.template.background.fadeOut(200).promise();
			};

			this.show = function() {

				this.resetPosition();
				this.template.fadeIn(200);

				if (typeof cfg.show === 'function')
					cfg.show(self);

				return this.template.background.fadeIn(200).promise();
			};

			return this;
		},

	}.init()),

}.init(NewsletterPro));