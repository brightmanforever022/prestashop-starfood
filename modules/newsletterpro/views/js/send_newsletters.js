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

NewsletterPro.namespace('modules.sendNewsletters');
NewsletterPro.modules.sendNewsletters = ({
	domSave: null,
	dom: null,
	box: null,
	vars: {},
	events: {},
	init: function(box) 
	{
		var self = this;
		self.box = box;
		self.initCustomersGrid();
		self.initVisitorsGrid();
		self.initVisitorsGridNewsletterPro();
		self.initAddedGrid();

		this.ready(function(dom){
			var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.sendNewsletters),
				exclusionWindow,
				exclusionWindowContent,
				dataModelExclusionEmails,
				dataSourceExclusionEmails,
				dataSourceExclusionEmailList,
				exclusionEmailFooter,
				performancesWindow,
				performancesWindowContent,
				dataModelConnection,
				dataSourceConnection,
				dataGridConnection,
				smtpSelect,
				filterSelection;

			box.dataStorage.on('change', 'count_send_connections', function(value){
				updateSendMethodDisplay();
			});
			// exclisionView
			exclusionViewWindow = new gkWindow({
				width: 640,
				height: 480,
				setScrollContent: 420,
				title: l('exclusion emails'),
				className: 'exclusion-window',
				show: function(win) 
				{
					if(typeof dataSourceExclusionEmailList !== 'undefined')
						dataSourceExclusionEmailList.sync();
				},
				close: function(win) {},
				content: function(win) 
				{
					var tpl = $('\
						<div id="exclusion-view-box">\
							<div style="margin-top: 10px;">\
								<h4>'+l('List of excluded emails')+'</h4>\
								<table id="exclusion-view" class="table table-bordered exclusion-view">\
									<thead>\
										<tr>\
											<th class="np-checkbox" data-template="checkbox">&nbsp;</th>\
											<th class="email" data-field="email">'+l('Email')+'</th>\
										</tr>\
										</thead>\
								</table>\
							</div>\
						<div>\
					');

					var exclusionEmailList = tpl.find('#exclusion-view');
					if (exclusionEmailList.length)
					{
						dataModelExclusionEmailList = new gk.data.Model({
							'id' : 'id_newsletter_pro_tpl_exclu',
						});

						dataSourceExclusionEmailList = new gk.data.DataSource({
							pageSize: 10,
							transport: {
								read: {
									url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getExclusionList',
									dataType: 'json',
								},
							},
							schema: {
								model: dataModelExclusionEmailList
							},
							trySteps: 2,
							errors: 
							{
								read: function(xhr, ajaxOptions, thrownError) 
								{
									dataSourceExclusionEmailList.syncStepAvailableAdd(3000, function(){
										dataSourceExclusionEmailList.sync();
									});

								},
							},
						});

						exclusionEmailList.gkGrid({
							dataSource: dataSourceExclusionEmailList,
							checkable: false,
							selectable: false,
							currentPage: 1,
							pageable: true,
							template: {
								actions: function(item, value)
								{

								},
								/*checkbox: function(item, value) 
								{
									var checkBox = $('<input type="checkbox" value="'+value+'" '+(item.isChecked() ? 'checked="checked"' : '')+'> ');
									return checkBox;
								},
								emails_count: function(item, value)
								{
									return '<span style="font-weight: bold;">'+value+'</span>';
								},
								emails_success: function(item, value)
								{
									return '<span style="color: green; font-weight: bold;">'+value+'</span>';
								},
								emails_error: function(item, value)
								{
									return '<span style="color: red; font-weight: bold;">'+value+'</span>';
								},
								type: function(item, value)
								{
									return item.data.type;
								}*/
							}
						});
					}
					exclusionViewWindowContent = tpl;
					return exclusionViewWindowContent;
				}
			});

			// exclusionView
			exclusionWindow = new gkWindow({
				width: 640,
				height: 600,
				setScrollContent: 540,
				title: l('exclusion emails'),
				className: 'exclusion-window',
				show: function(win) 
				{
					if(typeof dataSourceExclusionEmails !== 'undefined')
						dataSourceExclusionEmails.sync();
				},
				close: function(win) {},
				content: function(win) 
				{
					var tpl = $('\
						<div id="exclusion-email-box">\
							<div class="form-group clearfix">\
								<div class="col-sm-4">\
									<label for="input-exclude-emails" class="control-label"><span class="label-tooltip">'+l('import from csv file')+'</span></label>\
								</div>\
								<div class="col-sm-8">\
									<form id="form-exclusion-emails" method="post" enctype="multipart/form-data">\
										<div class="input-group">\
											<span class="input-group-addon">'+l('File')+'</span>\
											<input type="file" name="exclusion_emails_emails" class="form-control">\
											<span class="input-group-addon">'+l('Separator')+'</span>\
											<input type="text" name="exclusion_emails_csv_separator" class="form-control text-center" value=";" style="width: 35px;">\
											<div class="clear"></div>\
										</div>\
									</form>\
								</div>\
								<div class="col-sm-8 col-sm-offset-4">\
									<a id="btn-add-exclusion-csv" class="btn btn-default pull-left" href="javascript:{}"><span class="btn-ajax-loader"></span><i class="icon icon-plus-square"></i> '+l('add to exclusion')+'</a>\
								</div>\
							</div>\
							<div style="margin-top: 10px;">\
								<h4>'+l('select emails from history')+'</h4>\
								<table id="exclusion-email-send-history" class="table table-bordered exclusion-email-send-history">\
									<thead>\
										<tr>\
											<th class="np-checkbox" data-template="checkbox">&nbsp;</th>\
											<th class="template" data-field="template">'+l('template name')+'</th>\
											<th class="date" data-field="date">'+l('template date')+'</th>\
											<th class="emails-count" data-field="emails_count">'+l('total emails')+'</th>\
											<th class="emails-success" data-field="emails_success">'+l('sent success')+'</th>\
											<th class="emails-error" data-field="emails_error">'+l('sent errors')+'</th>\
											<th class="type" data-template="type">'+l('type')+'</th>\
										</tr>\
										</thead>\
								</table>\
							</div>\
						<div>\
					');

					var exclusionEmailSendHistory = tpl.find('#exclusion-email-send-history');
					if (exclusionEmailSendHistory.length)
					{
						dataModelExclusionEmails = new gk.data.Model({
							'id' : 'id_newsletter_pro_tpl_history',
						});

						dataSourceExclusionEmails = new gk.data.DataSource({
							pageSize: 7,
							transport: {
								read: {
									url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getHistoryExclusion',
									dataType: 'json',
								},
							},
							schema: {
								model: dataModelExclusionEmails
							},
							trySteps: 2,
							errors: 
							{
								read: function(xhr, ajaxOptions, thrownError) 
								{
									dataSourceExclusionEmails.syncStepAvailableAdd(3000, function(){
										dataSourceExclusionEmails.sync();
									});

								},
							},
						});

						exclusionEmailSendHistory.gkGrid({
							dataSource: dataSourceExclusionEmails,
							checkable: true,
							selectable: false,
							currentPage: 1,
							pageable: true,
							template: {
								actions: function(item, value)
								{

								},
								checkbox: function(item, value) 
								{
									var checkBox = $('<input type="checkbox" value="'+value+'" '+(item.isChecked() ? 'checked="checked"' : '')+'> ');
									return checkBox;
								},
								emails_count: function(item, value)
								{
									return '<span style="font-weight: bold;">'+value+'</span>';
								},
								emails_success: function(item, value)
								{
									return '<span style="color: green; font-weight: bold;">'+value+'</span>';
								},
								emails_error: function(item, value)
								{
									return '<span style="color: red; font-weight: bold;">'+value+'</span>';
								},
								type: function(item, value)
								{
									return item.data.type;
								}
							}
						});

						exclusionEmailSendHistory.addFooter(function(columns){
							var check = self.createCheckToggle('btn-exclusion-checkall', dataSourceExclusionEmails)
							check.addClass('pull-left');
							
							var tpl = $('\
								<div class="clearfix pull-left" style="margin-left: 4px;">\
									<input id="exclusion-remaingin-emails" class="valign-middle pull-left" type="checkbox" style="margin-left: 8px; margin-top: 9px; margin-right: 5px;">\
									<label for="exclusion-remaingin-emails" class="control-label valign-middle pull-left">'+l('remaining email')+'</label>\
									<input id="exclusion-sent-emails" class="align-middle pull-left" type="checkbox" checked="checked" style="margin-left: 8px; margin-top: 9px; margin-right: 5px;">\
									<label for="exclusion-sent-emails" class="control-label valign-middle pull-left">'+l('sent email')+'</label>\
								</div>\
							');

							var add = $('<a href="javascript:{}" class="btn btn-default pull-right"><span class="btn-ajax-loader"></span><i class="icon icon-plus-square"></i> '+l('add to exclusion')+'</a>');

							add.on('click', function(){
								var selectedItem = dataSourceExclusionEmails.getSelection();
								var selectedData = [];
								if (selectedItem.length > 0)
								{
									for(var obj in selectedItem) 
									{
										var data = selectedItem[obj].data,
											type = data.type,
											id = (data.hasOwnProperty('id_newsletter_pro_send') ? data.id_newsletter_pro_send : data.id_newsletter_pro_task);

										selectedData.push({
											'id': id,
											'type': type,
										});
									}
								}

								var re = $('#exclusion-remaingin-emails').is(':checked') ? 1 : 0;
								var se = $('#exclusion-sent-emails').is(':checked') ? 1 : 0;

								box.showAjaxLoader(add);
								$.postAjax({'submit': 'addHistoryEmailsToExclusion', 'data': selectedData, 'remainingEmails': re, 'sentEmails': se}).done(function(response){
									box.hideAjaxLoader(add);
									if (!response.success)
										box.alertErrors(response.errors);
									else
									{
										dom.exclusionEmailsCount.html(response.count);
										alert(box.displayAlert(response.msg));
									}
								}).always(function(){
									box.hideAjaxLoader(add);
								});
							});

							

							return exclusionEmailSendHistory.makeRow([check, tpl, add]);
						}, 'prepend');
					}

					var btnAddExclusionCsv = tpl.find('#btn-add-exclusion-csv');

					btnAddExclusionCsv.on('click', function(){
						box.showAjaxLoader(btnAddExclusionCsv);
						$.submitAjax({'submit': 'addCsvEmailsToExclusion', name: 'addCsvEmailsToExclusion', form: $('#form-exclusion-emails')}).done(function(response){
							box.hideAjaxLoader(btnAddExclusionCsv);
							if (response.success)
							{
								dom.exclusionEmailsCount.html(response.count);
								alert(box.displayAlert(response.msg));
							}
							else
								box.alertErrors(response.errors);
						}).always(function(){
							box.hideAjaxLoader(btnAddExclusionCsv);
						})
					});

					exclusionWindowContent = tpl;
					return exclusionWindowContent;
				}
			});

			var performancesWindowTpl;

			performancesWindow = new gkWindow({
				width: 640,
				height: 475,
				title: l('Performances & Limits'),
				className: 'performances-window',
				setScrollContent: 415,
				show: function(win) 
				{
					if(typeof dataSourceConnection !== 'undefined')
					{
						dataSourceConnection.sync(function(){
							box.dataStorage.set('count_send_connections', this.count());
						});
					}

					// update the smtp select
					var select = box.modules.smtp.ui.components.smptSelect;
					if (typeof select !== 'undefined' && typeof smtpSelect !== 'undefined')
					{
						var data = select.getData(),
						options = self.getSelectOptions(data);

						smtpSelect.refresh(options);
					}

				},
				close: function(win) {},
				content: function(win) 
				{
					var storage = box.dataStorage,
						getThrottlerTypeText = function(value) {
							value = value || storage.getNumber('configuration.SEND_THROTTLER_TYPE');
							return (value == box.define.SEND_THROTTLER_TYPE_EMAILS ? l('emails') : 'MB');
						},
						getThrottlerTypeButtonText = function(value) {
							value = value || storage.getNumber('configuration.SEND_THROTTLER_TYPE');
							return '(' + (value == box.define.SEND_THROTTLER_TYPE_EMAILS ? l('change limit to MB') : l('change limit to emails')) + ')';
						};

					var tpl = performancesWindowTpl = $('\
						<div id="np-performances-window-content" class="np-performances-window-content">\
							<h4>'+l('Define the sending performances and limits.')+'</h4>\
							<div class="np-send-method-sleep">\
								<input id="np-radio-send-method-sleep" name="SEND_METHOD" value="' + box.define.SEND_METHOD_DEFAULT + '" type="radio" ' + (storage.getNumber('configuration.SEND_METHOD') == box.define.SEND_METHOD_DEFAULT ? ' checked="checked" ' : '') + '>\
								<span data-for="np-radio-send-method-sleep">'+l('Send one newsletter at')+'</span> \
								<input id="np-send-method-sleep-input" class="gk-input" type="number" min="0" max="60" value="' + storage.get('configuration.SLEEP') + '"> \
								<span data-for="np-radio-send-method-sleep">'+l('seconds')+'</span>\
								<span style="display: inline-block; margin-bottom: 5px; font-style: italic;">('+l('for all connections')+')</span>\
							</div>\
							<div class="np-send-method-antiflod">\
								<input id="np-radio-send-method-antiflood" class="np-radio-send-method-antiflood" name="SEND_METHOD" value="' + box.define.SEND_METHOD_ANTIFLOOD + '" type="radio" ' + (storage.getNumber('configuration.SEND_METHOD') == box.define.SEND_METHOD_ANTIFLOOD ? ' checked="checked" ' : '') + '> \
								<span data-for="np-radio-send-method-antiflood">'+l('Antiflood & Speed limits')+'</span>\
								<span style="display: inline-block; margin-bottom: 5px; font-style: italic;">('+l('for each connection')+')</span>\
								<div class="np-send-method-antiflod-settings">\
									<div class="np-send-method-antiflod-row">\
										<input id="np-send-antifllod-active" name="SEND_ANTIFLOOD_ACTIVE" type="checkbox" ' + (storage.getNumber('configuration.SEND_ANTIFLOOD_ACTIVE') ? ' checked="checked" ' : '' ) + '> \
										<span data-for="np-send-antifllod-active">'+l('Reconnect after')+'</span> \
										<input id="np-send-antiflood-emails"class="gk-input" type="number" min="1" max="100" value="' + (storage.get('configuration.SEND_ANTIFLOOD_EMAILS')) + '"> \
										<span data-for="np-send-antifllod-active">'+l('emails sent, and pause')+'</span> \
										<input id="np-send-antiflood-sleep" class="gk-input" type="number" min="0" max="60" value="' + (storage.get('configuration.SEND_ANTIFLOOD_SLEEP')) + '"> \
										<span>'+l('seconds')+'</span>\
										<div id="np-send-antiflood-info" style="display: none; margin-left: 22px; margin-bottom: 5px;">\
											<br>\
											<span style="display: inline-block; font-style: italic;"></span>\
										</div>\
									</div>\
									<div class="np-send-method-antiflod-row">\
										<input id="np-send-throttler-active" name="SEND_THROTTLER_ACTIVE" type="checkbox" ' + (storage.getNumber('configuration.SEND_THROTTLER_ACTIVE') ? ' checked="checked" ' : '' ) + '> \
										<span data-for="np-send-throttler-active">'+l('Limit')+'</span> \
										<input id="np-send-throttler-limit" class="gk-input" type="number" min="1" max="5000" value="' + (storage.getNumber('configuration.SEND_THROTTLER_LIMIT')) + '"> \
										<span id="np-send-throttler-type-text" data-for="np-send-throttler-active">' + getThrottlerTypeText() + '</span> \
										<span data-for="np-send-throttler-active">'+l('per minute')+'</span> \
										<a id="np-send-throttler-changetype" class="np-send-throttler-changetype" href="javascript:{}">' + getThrottlerTypeButtonText() + '</a>\
										<div id="np-send-throttler-info" style="display:none; margin-left: 22px; margin-bottom: 5px;">\
											<br>\
											<span style="display: inline-block; font-style: italic;"></span>\
										</div>\
									</div>\
								</div>\
							</div>\
							<div style="margin-top: 20px;">\
								<h4 for="input-exclude-emails" class="label-spacing" style="margin-bottom: 0;">'+l('Add or remove connections')+'</h4>\
								<span style="color: red; font-style: italic;">'+l('Don\'t add to many connections from the same server. You will risk to be banned.')+'</span>\
								<span style="display: inline-block; margin-bottom: 5px; font-style: italic;">'+l('Leave the table empty if you want to have a single connection with the default configuration')+'</span>\
								<table id="np-send-connection" class="table table-bordered np-send-connection">\
									<thead>\
										<tr>\
											<th class="name" data-field="name">'+l('Connection Name')+'</th>\
											<th class="connection-type" data-field="connection_type">'+l('Connection Type')+'</th>\
											<th class="connection-test" data-template="connection_test">'+l('Test Connection')+'</th>\
											<th class="actions" data-template="actions">'+l('Actions')+'</th>\
										</tr>\
									</thead>\
								</table>\
							</div>\
							<div class="form-group clearfix" style="margin-top: 10px;">\
								<label class="control-label col-sm-3"><span class="label-tooltip">'+l('Backend limit')+'</span></label>\
								<div class="col-sm-9">\
									<input id="np-send-backend-limit" type="number" class="form-control fixed-width-xs" value="'+box.dataStorage.get('configuration.SEND_LIMIT_END_SCRIPT')+'" min="3" max="100">\
								<div>\
								<p class="help-block">'+l('Decrease this number if the newsletter stops from the sending process, and it\'s not continue.')+'</p>\
							</div>\
						</div>\
					');

					var dataFor = tpl.find('[data-for]');

					dataFor.css('cursor', 'default');

					dataFor.on('click', function(event){
						var currentTarget = $(event.currentTarget),
							id = currentTarget.data('for'),
							selector = $('#'+id);

						if (selector.length)
							selector.trigger('click');
					});

					var regexNumber = /^\d+$/;

					tpl.find('#np-send-backend-limit').on('change', function(){
						var value = Number($(this).val());

						if (isNaN(value))
							value = box.dataStorage.getNumber('configuration.SEND_LIMIT_END_SCRIPT');

						if (value >= 100)
							value = 100;
						else if (value <= 3)
							value = 3;

						$(this).val(value);

						box.dataStorage.set('configuration.SEND_LIMIT_END_SCRIPT', value);

						$.updateConfiguration('SEND_LIMIT_END_SCRIPT', value).done(function(response){
							if (!response.success)
								box.alertErrors(response.errors);
						});
					});

					tpl.find('#np-send-throttler-changetype').on('click', function(){
						var currentValue = storage.getNumber('configuration.SEND_THROTTLER_TYPE'),
							value = (currentValue ? 0 : 1),
							btn = $(this);

						tpl.find('#np-send-throttler-type-text').html(getThrottlerTypeText(value));
						btn.html(getThrottlerTypeButtonText(value));

						$.updateConfiguration('SEND_THROTTLER_TYPE', value).done(function(response){
							if (response.success)
								storage.set('configuration.SEND_THROTTLER_TYPE', value);
							else
								box.alertErrors(response.errors);

						}).always(function(){
							tpl.find('#np-send-throttler-type-text').html(getThrottlerTypeText());
							btn.html(getThrottlerTypeButtonText());
							updateSendMethodDisplay();
						});
					});

					tpl.find('[name="SEND_METHOD"]').on('change', function(){
						var btn = $(this),
							value = btn.val();

						$.updateConfiguration('SEND_METHOD', value).done(function(response){
							if (response.success)
								storage.set('configuration.SEND_METHOD', value);
							else
								box.alertErrors(response.errors);
						}).always(function(){
							updateSendMethodDisplay();
						});
					});

					tpl.find('#np-send-method-sleep-input').on('blur', function(){
						var btn = $(this),
							value = btn.val();

						if (String(value).match(regexNumber) === null || (value < 0 || value > 100))
						{
							btn.val(storage.get('configuration.SLEEP'));
							return;
						}

						$.updateConfiguration('SLEEP', value).done(function(response){
							if (response.success)
								storage.set('configuration.SLEEP', value);
							else
								box.alertErrors(response.errors);
						}).always(function(){
							updateSendMethodDisplay();
						});
					});

					tpl.find('#np-send-throttler-limit').on('blur', function(){
						var btn = $(this),
							value = btn.val();

						if (String(value).match(regexNumber) === null || (value < 1 || value > 5000))
						{
							btn.val(storage.get('configuration.SEND_THROTTLER_LIMIT'));
							return;
						}

						$.updateConfiguration('SEND_THROTTLER_LIMIT', value).done(function(response){
							if (response.success)
								storage.set('configuration.SEND_THROTTLER_LIMIT', value);
							else
								box.alertErrors(response.errors);
						}).always(function(){
							updateSendMethodDisplay();
						});
					});

					var antifloodValidateMsg = l('At least one antiflood option needs to be activated. If you want to activate the other antiflood option select it first.');

					tpl.find('#np-send-throttler-active').on('change', function(){

						if (!$(this).is(':checked') && !tpl.find('#np-send-antifllod-active').is(':checked'))
						{
							$(this).prop('checked', true);
							box.alertErrors([antifloodValidateMsg]);
							return;
						}

						var btn = $(this),
							value = Number(btn.is(':checked'));

						$.updateConfiguration('SEND_THROTTLER_ACTIVE', value).done(function(response){
							if (response.success)
								storage.set('configuration.SEND_THROTTLER_ACTIVE', value);
							else
								box.alertErrors(response.errors);
						}).always(function(){
							updateSendMethodDisplay();
						});
					});

					tpl.find('#np-send-antifllod-active').on('change', function(){

						if (!$(this).is(':checked') && !tpl.find('#np-send-throttler-active').is(':checked'))
						{
							$(this).prop('checked', true);
							box.alertErrors([antifloodValidateMsg]);
							return;
						}

						var btn = $(this),
							value = Number(btn.is(':checked'));

						$.updateConfiguration('SEND_ANTIFLOOD_ACTIVE', value).done(function(response){
							if (response.success)
								storage.set('configuration.SEND_ANTIFLOOD_ACTIVE', value);
							else
								box.alertErrors(response.errors);
						}).always(function(){
							updateSendMethodDisplay();
						});
					});

					tpl.find('#np-send-antiflood-emails').on('blur', function(){
						var btn = $(this),
							value = btn.val();

						if (String(value).match(regexNumber) === null || (value < 1 || value > 100))
						{
							btn.val(storage.get('configuration.SEND_ANTIFLOOD_EMAILS'));
							return;
						}

						$.updateConfiguration('SEND_ANTIFLOOD_EMAILS', value).done(function(response){
							if (response.success)
								storage.set('configuration.SEND_ANTIFLOOD_EMAILS', value);
							else
								box.alertErrors(response.errors);
						}).always(function(){
							updateSendMethodDisplay();
						});
					});

					tpl.find('#np-send-antiflood-sleep').on('blur', function(){
						var btn = $(this),
							value = btn.val();

						if (String(value).match(regexNumber) === null || (value < 0 || value > 60))
						{
							btn.val(storage.get('configuration.SEND_ANTIFLOOD_SLEEP'));
							return;
						}

						$.updateConfiguration('SEND_ANTIFLOOD_SLEEP', value).done(function(response){
							if (response.success)
								storage.set('configuration.SEND_ANTIFLOOD_SLEEP', value);
							else
								box.alertErrors(response.errors);
						}).always(function(){
							updateSendMethodDisplay();
						});
					});

					dataModelConnection = new gk.data.Model({
						'id': 'id_newsletter_pro_send_connection'
					});

					dataSourceConnection = new gk.data.DataSource({
						pageSize: 7,
						transport: {
							read: 
							{
								url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=ajaxGetConnections',
								dataType: 'json',
							},

							destroy: 
							{
								url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=ajaxDeleteConnection&id',
								type: 'POST',
								dateType: 'json',
								success: function(response, itemData) 
								{
									if(!response.success)
									{
										dataSourceConnection.sync(function(){
											box.dataStorage.set('count_send_connections', this.count());
										});
										box.alertErrors(response.errors);
									}
									else
									{
										var val = getConnections();
										if (val > 2)
											box.dataStorage.set('count_send_connections', --val);
										else
											box.dataStorage.set('count_send_connections', 1);
									}
								},
								error: function(data, itemData) 
								{
									dataSourceConnection.sync(function(){
										box.dataStorage.set('count_send_connections', this.count());
									});
									alert(l('The connection cannot be deleted.'));
								},
								complete: function(data, itemData) {},
							},
						},
						schema: {
							model: dataModelConnection
						},
						trySteps: 2,
						errors: {
							read: function(xhr, ajaxOptions, thrownError) 
							{
								dataSourceConnection.syncStepAvailableAdd(3000, function(){
									dataSourceConnection.sync(function(){
										box.dataStorage.set('count_send_connections', this.count());
									});
								});
							}
						},
						done: function() {
							box.dataStorage.set('count_send_connections', this.count());
						}
					});

					dataGridConnection = tpl.find('#np-send-connection');

					dataGridConnection.gkGrid({
						dataSource: dataSourceConnection,
						checkable: false,
						selectable: false,
						currentPage: 1,
						pageable: true,
						template: {
							actions: function(item, value) 
							{
								var deleteconnection = $('#delete-connection').gkButton({
									name: 'delete',
									title: l('delete'),
									className: 'connection-delete',
									item: item,
									command: 'delete',
									icon: '<i class="icon icon-trash-o"></i> '
								});

								return deleteconnection;
							},
							connection_test: function(item)
							{
								var btn = $('<a href="javascript:{}" class="btn btn-default" style="float: right;">\
												<span class="btn-ajax-loader" style="margin-top: 4px;"></span>\
												<span class="pull-left np-connection-status" style="display: none;"></span>\
												<i class="icon icon-envelope"></i> \
												'+l('Test')+'\
											</a>'),
									connectionStatus = btn.find('.np-connection-status'),
									connectionDefault = function() {
										connectionStatus.hide();
									},
									connectionYep = function() {
										connectionStatus.show().html('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
									},
									connectionNup = function() {
										connectionStatus.show().html('<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>');
									};

								btn.on('click', function(){
									var idSmtp = !isNaN(Number(item.data.id_newsletter_pro_smtp)) ? Number(item.data.id_newsletter_pro_smtp) : 0;

									connectionDefault();
									box.showAjaxLoader(btn);
									$.postAjax({ 'submit': 'sendMailTest', sendMailTest: box.dataStorage.get('configuration.PS_SHOP_EMAIL'), id_smtp: idSmtp }).done(function(response) {
										if (!response.success)
										{
											connectionNup();
											box.alertErrors(response.errors);
										}
										else
											connectionYep();

									}).always(function(){
										box.hideAjaxLoader(btn);
									});
								});

								return btn;
							}
						}
					});

					dataGridConnection.addFooter(function(columns){

						var smtp = box.modules.smtp;

						var smtpSelectTemplate = $('<select id="connection-smtp-select" class="fixed-width-xxl"></select>');

						smtpSelect = smtp.ui.SelectOption({
							name: 'connectionSmptSelect',
							template: smtpSelectTemplate,
							className: 'gk-smtp-select',
							options: self.getSelectOptions(NewsletterPro.dataStorage.get('all_smtp')),
						});

						var add = $('<a href="javascript:{}" class="btn btn-default" style="float: right;">\
										<span class="btn-ajax-loader"></span>\
										<i class="icon icon-plus-square"></i> '+l('Add Connection')+'\
									</a>');

						add.on('click', function(){
							var selected = smtpSelect.getSelected();
							if (selected == null) 
							{
								alert(box.displayAlert(l('You smtp value has been selected.')));
								return;
							}
							
							if (!confirm(l('Are you sure you want to add a new connection? If you don\'t setup the connections properly you risk to be banned.' )))
								return;

							box.showAjaxLoader(add);

							$.postAjax({'submit': 'ajaxAddConnection', id_smtp: selected.data.id_newsletter_pro_smtp}).done(function(response){
								box.hideAjaxLoader(add);
								if (!response.success)
									box.alertErrors(response.errors);
								else
								{
									dataSourceConnection.sync(function(){
										box.dataStorage.set('count_send_connections', this.count());
									});
								}
							}).always(function(){
								box.hideAjaxLoader(add);
							});
						});

						return this.makeRow([smtpSelectTemplate, add]);
					}, 'prepend'); // end of dataGridConnection addFooter

					performancesWindowContent = tpl;
					return performancesWindowContent;
				}
			});

			updateSendMethodDisplay();

			dom.btnAddExclusion.on('click', function(){
				exclusionWindow.show();
			});

			dom.btnViewExclusion.on('click', function(){
				exclusionViewWindow.show();
			});

			dom.btnClearExclusion.on('click', function(){
				if (!confirm(l('Are you sure you want to clear exclusions?' )))
					return;

				box.showAjaxLoader(dom.btnClearExclusion);

				$.postAjax({'submit': 'clearExclusionEmails'}).done(function(response){
					if (response.success)
					{
						box.hideAjaxLoader(dom.btnClearExclusion);
						dom.exclusionEmailsCount.html(0);
						alert(box.displayAlert(response.msg));
					}
					else
						box.alertErrors(response.errors);
				}).always(function(){
					box.hideAjaxLoader(dom.btnClearExclusion);
				});
			});

			dom.btnPerformances.on('click', function(){
				performancesWindow.show();
			});

			function getConnections()
			{
				var connections = Number(box.dataStorage.get('count_send_connections')),
					defaultConnection = 1;

				return (!isNaN(connections) 
							? ( connections == 0 ? defaultConnection : connections )
							: defaultConnection );
			}

			function updateSendMethodDisplay()
			{
				var storage = box.dataStorage,
					define = box.define,
					connections = getConnections(),
					display = '',
					str = '',
					sendAntifloodEmails = storage.getNumber('configuration.SEND_ANTIFLOOD_EMAILS'),
					sendAntifloodSleep = storage.getNumber('configuration.SEND_ANTIFLOOD_SLEEP'),
					sendThrottlerLimit = storage.getNumber('configuration.SEND_THROTTLER_LIMIT');

				if (typeof performancesWindowTpl !== 'undefined' && performancesWindowTpl)
				{
					var npSendAntifloodInfo = performancesWindowTpl.find('#np-send-antiflood-info'),
						npSendThrottlerInfo = performancesWindowTpl.find('#np-send-throttler-info');

					if (storage.getNumber('count_send_connections') > 0)
					{
						var displayAntifloodInfoMsg = l('With #s connections send #s emails, and pause #s seconds.')
								.replace(/\#s/, connections)
								.replace(/\#s/, sendAntifloodEmails * connections)
								.replace(/\#s/, sendAntifloodSleep),
							displayThrottlerInfo = l('With #s connections limit #s emails per minute.')
								.replace(/\#s/, connections)
								.replace(/\#s/, sendThrottlerLimit * connections);

						npSendAntifloodInfo.show().find('span').html(displayAntifloodInfoMsg);
						npSendThrottlerInfo.show().find('span').html(displayThrottlerInfo);
					}
					else
					{
						npSendAntifloodInfo.hide();
						npSendThrottlerInfo.hide();
					}
				}

				if (storage.getNumber('configuration.SEND_METHOD') == define.SEND_METHOD_DEFAULT)
				{
					str = l('Send one newsletter at #s seconds - (for all connections).').replace(/\#s/, storage.getNumber('configuration.SLEEP'));
				}
				else
				{
					var antifloodMsg = l('(Antiflood) Send #s emails, and pause #s seconds.')
											.replace(/\#s/, sendAntifloodEmails * connections)
											.replace(/\#s/, sendAntifloodSleep),
						throttlerMsg = (storage.getNumber('configuration.SEND_THROTTLER_TYPE') == define.SEND_THROTTLER_TYPE_EMAILS 
											? l('(Speed limits) Limit  #s emails per minute.') 
											: l('(Speed limits) Limit  #s MB per minute.'))
												.replace(/\#s/, sendThrottlerLimit * connections);

					if (storage.getNumber('configuration.SEND_ANTIFLOOD_ACTIVE') && storage.getNumber('configuration.SEND_THROTTLER_ACTIVE'))
						str = antifloodMsg + ' / ' + throttlerMsg;
					else if (storage.getNumber('configuration.SEND_ANTIFLOOD_ACTIVE'))
						str = antifloodMsg;
					else if (storage.getNumber('configuration.SEND_THROTTLER_ACTIVE'))
						str = throttlerMsg;
					else
						str = l('No send method was selected.');
				}

				display = '<label class="control-label">' + str + '</label>\
							<span style="display: block; font-style: italic;">('+(l('#s connections').replace(/\#s/, connections))+')</span>';

				dom.sendMethodDisplay.html(display);
				return str;
			}


			filterSelection = new box.components.FilterSelection({
				customers: self.vars.filterCustomers,
				visitors: self.vars.filterVisitor,
				visitors_np: self.vars.filterVisitorNP,
				added: self.vars.filterAdded,

				customers_apply_callback: self.vars.applyFilerCustomersCallback,
				visitor_apply_callback: self.vars.applyFilerVisitorCallback,
				visitor_np_apply_callback: self.vars.applyFilerVisitorNpCallback,
				added_apply_callback: self.vars.applyFilerAddedCallback,
			});

			dom.addFilterSelection.on('click', function(){
				var filters = filterSelection.getFilters(),
					name = dom.nameFilterSelection.val();

				$.postAjax({'submit': 'addFilterSelection', name: name, filters: filters}).done(function(response){
					if (!response.success)
						box.alertErrors(response.errors);
					else
					{
						dom.deleteFilterSelection.show();
						dom.nameFilterSelection.val('');

						dom.filterSelection.empty();
						dom.filterSelection.append('<option value="0">- '+l('none')+' -</option>');

						for(var i = 0; i < response.filters.length; i++)
						{
							var filter = response.filters[i],
								option = '<option value="' + filter.id_newsletter_pro_filters_selection + '" ' + ($.trim(name) === $.trim(filter.name) ? 'selected="selected"' : '') + '>'+filter.name+'</option>';

							dom.filterSelection.append(option);

						}
					}
				});
			});

			dom.addFilterSelection.on('change', function(){
				var id = Number(dom.filterSelection.val());
				if (id > 0)
					dom.deleteFilterSelection.show();
				else
					dom.deleteFilterSelection.hide();

			});

			dom.deleteFilterSelection.on('click', function(){

				var id = Number(dom.filterSelection.val());

				if (id == 0)
					return false;

				$.postAjax({'submit': 'deleteFilterSelection', id: id}).done(function(response){
					if (!response.success)
						box.alertErrors(response.errors);
					else
					{
						$(dom.filterSelection.selector + ' ' + 'option[value="' + id + '"]').remove();
						filterSelection.clearfilters();
						
						if (dom.filterSelection.children().length <= 1)
							dom.deleteFilterSelection.hide();
					}
				});
			});

			function uncheckAll(list, button)
			{
				if (typeof list !== 'undefined')
				{
					// uncheck the selections
					list.uncheckAll();
					button.data('checked', false);
					button.changeTitle(l('check all'));
				}
			}

			dom.filterSelection.on('change', function(){

				var id = Number(dom.filterSelection.val());
				if (id == 0)
				{
					dom.deleteFilterSelection.hide();
					filterSelection.clearfilters();
					return false;
				}
				else
					dom.deleteFilterSelection.show();

				uncheckAll(self.vars.customers, self.vars.customersCheckAll);
				uncheckAll(self.vars.visitors, self.vars.visitorsCheckAll);
				uncheckAll(self.vars.visitorsNP, self.vars.visitorsNPCheckAll);
				uncheckAll(self.vars.added, self.vars.addedCheckAll);

				$.postAjax({'submit': 'getFilterSelectionById', id: id}).done(function(response){
					if (!response.success)
						box.alertErrors(response.errors);
					else
					{
						if (response.hasOwnProperty('value'))
						{
							filterSelection.clearfilters();
							filterSelection.applyFilters(response.value);
						}
						else
							box.alertErrors([l('An error occured.')]);
					}
				});
			});
		});

		return self;
	},

	addEvent: function(name, value) {
		this.events[name] = value;
	},

	triggerEvent: function(name) {
		if (this.events.hasOwnProperty(name) && typeof this.events[name] == 'function')
			this.events[name]();
	},

	addVar: function(name, value) {
		this.vars = this.vars || {};
		this.vars[name] = value;
	},

	ready: function(func) 
	{
		var self = this;
		$(document).ready(function(){

			self.domSave = self.domSave || {
				template : $($.trim($('#add-new-email-template').html()))
			};

			if (typeof self.domSave.template[1] !== 'undefined')
				self.domSave.template = $(self.domSave.template[1]);

			var template = self.domSave.template;
			var listOfInterestTemplate =  $($('#list-of-interest-template').html());

			self.dom = self.dom || {
				customersGrid: $('#customers-list'),
				visitorsGrid: $('#visitors-list'),
				visitorsNPGrid: $('#visitors-np-list'),
				addedGrid: $('#added-list'),
				customersCount: $('#customers-count'),
				visitorsCount: $('#visitors-count'),
				visitorsNPCount: $('#visitors-np-count'),
				addedCount: $('#added-count'),
				addNewEmailTemplate: template,
				addNewEmail: template.find('#add-new-email-action'),
				addNewEmailForm: template.find('#add-new-email-from'),
				addNewEmailError: template.find('#add-new-email-error'),

				btnAddExclusion: $('#btn-add-exclusion'),
				btnClearExclusion: $('#btn-clear-exclusion'),
				btnViewExclusion: $('#btn-view-exclusion'),
				exclusionEmailsCount: $('#exclusion-emails-count'),
				usersAjaxLoader: $('#users-ajax-loader'),
				visitorsAjaxLoader: $('#visitors-ajax-loader'),
				visitorsNPAjaxLoader: $('#visitors-np-ajax-loader'),
				addedAjaxLoader: $('#added-ajax-loader'),
				btnBouncedEmails: $('#btn-bounced-emails'),

				btnPerformances: $('#np-btn-performances'),
				sendMethodDisplay: $('#np-send-method-display'),

				addFilterSelection: $('#np-add-filter-selection'),
				deleteFilterSelection: $('#np-delete-filter-selection'),
				nameFilterSelection: $('#np-name-filter-selection'),
				filterSelection: $('#np-filter-selection'),
			};

			func(self.dom);
		});
	},

	createCheckToggle: function(name, dataSource) 
	{
		var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.sendNewsletters);
		var button = $('#'+name)
			.gkButton({
				name: name,
				title: l('check all'),
				className: name,
				css: {
					'padding-left': '10px',
					'padding-right': '10px',
					'margin-left': '0',
					'display': 'inline-block',
				},
				attr: {
					'data-checked': 0,
				},

				click: function(event) {

					function isChecked() {
						return button.data('checked') ? true : false;
					};

					function toggleName(trueStr, falseStr) {
						if (isChecked()) {
							button.data('checked', false);
							button.changeTitle(falseStr);
							return false;
						} else {
							button.data('checked', true);
							button.changeTitle(trueStr);
							return true;
						}
					}

					if (toggleName(l('uncheck all'), l('check all'))) {
						dataSource.checkAll();
					} else {
						dataSource.uncheckAll();
					}
				},
			});
		return button;
	},

	getGenderImageById: function(idGender)
	{
		var img = '';
		switch(parseInt(idGender))
		{
			case 1:
				img = '<img src="../modules/newsletterpro/views/img/gender_1.png" style="margin-left: 7px;">';
			break;

			case 2:
				img = '<img src="../modules/newsletterpro/views/img/gender_2.png" style="margin-left: 7px;">';
			break;
		}

		return img;
	},

	isNewsletterProSubscriptionActive: function()
	{
		return NewsletterPro.dataStorage.getNumber('configuration.SUBSCRIPTION_ACTIVE');
	},

	getAjaxLoader: function()
	{
		var loader = $('<span class="ajax-loader" style="display: block;"></span>');
		return loader;
	},

	getRangeSelectionContent: function(func)
	{
		$.postAjax({'submit': 'getRangeSelectionContent'}, 'html').done(function(content){
			func($(content));
		});
	},

	resetButtons: function() 
	{

	},

	isPS16: function ()
	{
		return this.box.dataStorage.data.isPS16;
	},

	resetCustomersButton: function() 
	{

	},

	resetVisitorsButton: function() 
	{

	},

	resetVisitorsNPButton: function() 
	{

	},

	resetAddedButton: function() 
	{

	},

	buildExportToCSVData: function(dataSource, listRef)
	{
		var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.sendNewsletters);

		var defaultSeparator = ';',
			exportForm = $('\
				<form id="' + NewsletterPro.uniqueId() + '" method="POST" action="' + NewsletterPro.dataStorage.get('ajax_url') + '#sendNewsletters">\
					<input type="hidden" name="export_all_columns" value="1">\
					<input type="hidden" name="export_email_addresses" value="1">\
					<input type="hidden" name="csv_separator" value="' + defaultSeparator + '">\
					<input type="hidden" name="list_ref" value="' + listRef + '">\
					<div id="np-export-csv-range-box">\
					</div>\
				</form>\
			'),
			separatorInput = exportForm.find('input[name="csv_separator"]'),
			exportRangeBox = exportForm.find('#np-export-csv-range-box');

		var btnExportCsv = $('\
			<a id="' + NewsletterPro.uniqueId()  + '" href="javascript:{}" class="btn btn-default pull-right">\
				<i class="icon icon-download"></i>\
				'+l('Export Selection')+'\
			</a>');

		btnExportCsv.on('click', function(){

			var selected = dataSource.getSelectedIds(true);
				separator = prompt(l('CSV Separator'), defaultSeparator);

			if (!selected.length) {
				alert(l('You must select at least an email address.'));
				return;
			}
			
			if (separator == null) {
				return;
			}

			separator = $.trim(separator);

			if (separator == ';' || separator == ',') {

				exportRangeBox.empty();

				for (var i = 0, length = selected.length; i < length; i++) {
					exportRangeBox.append('<input type="hidden" name="export_range[]" value="' + selected[i] + '">');
				}

				separatorInput.val(separator);
				exportForm.submit();
			} else {
				alert(l('Invalid CSV separator.'));
				return;
			}

		});

		return btnExportCsv;
	},

	initCustomersGrid: function() {
		var self = this;

		self.ready(function(dom) {
			var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.sendNewsletters),
				customersDataModel,
				customersDataSource,
				customersGrid = dom.customersGrid,
				maxTotalSpent = 0,
				filterByCountryDataModel,
				filterByCountryDataSource,
				filterByCountryDataGrid,
				filterByCountrySearch;

			customersDataModel = new gk.data.Model({
				id: 'id_customer',
			});

			customersDataSource = new gk.data.DataSource({
				pageSize: 10,
				trySteps: 2,
				transport: 
				{
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getCustomers',
						dataType: 'json',
					},
					update: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=updateCustomer&id',
						dataType: 'json',
					},

					destroy: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=deleteCustomer&id',
						type: 'POST',
						dateType: 'json',
						success: function(response, itemData) {
							if(!response)
								alert(l('delete customer'));
						},
						error: function(data, itemData) {
							alert(l('delete customer'));
						},
						complete: function(data, itemData) {},
					},

					search: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=searchCustomer&value',
						dataType: 'json',
					},

					filter: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=filterCustomer',
						dataType: 'json',
					},

				},
				schema: {
					model: customersDataModel
				},
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						customersDataSource.syncStepAvailableAdd(3000, function(){
							customersDataSource.sync();
						});
					},
				}
			});

			customersGrid.gkGrid({
				dataSource: customersDataSource,
				selectable: false,
				checkable: true,
				currentPage: 1,
				pageable: true,
				start: function()
				{
					dom.usersAjaxLoader.show();
				},
				done: function(dataSource) 
				{
					dom.customersCount.html(dataSource.items.length);
					dom.usersAjaxLoader.hide();
				},
				template: {
					img_path: function(item, value) {

						var div = $('<div></div>');
						var lang_img = '<img src="'+value+'">';
						var gender_img = self.getGenderImageById(item.data.id_gender);

						div.append(lang_img);
						div.append(gender_img);
						div.width('38');
						return div;
					},

					newsletter: function(item, value) 
					{
						var a = $('<a href="javascript:{}"></a>'),
							data = item.data;

						function isSubscribed() {
							return parseInt(item.data.newsletter) ? true : false;
						}

						function viewOnlySubscribed() {
							return NewsletterPro.dataStorage.getNumber('configuration.VIEW_ACTIVE_ONLY') ? true : false;
						}

						function switchSubscription() 
						{
							if (isSubscribed()) {
								a.html('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
							} else {
								a.html('<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>');
							}
						}

						switchSubscription();

						a.on('click', function(e){
							e.stopPropagation();
							data.newsletter = (isSubscribed() ? 0 : 1);

							item.update().done(function(response) {
								if (!response) {
									alert('error on subscribe or unsubscribe');
								} else {
									if (viewOnlySubscribed()) {
										item.removeFromScreen();
									} else {
										switchSubscription();
									}
								}
							});
						});

						return a;
					},

					chackbox: function(item, value) 
					{
						var checkBox = $('<input type="checkbox" value="'+value+'" '+(item.isChecked() ? 'checked="checked"' : '')+'> ');
						return checkBox;
					},

					actions: function(item) {
						var deleteCustomer = $('#delete-customer')
							.gkButton({
								name: 'delete',
								title: l('delete'),
								className: 'customer-delete',
								item: item,
								command: 'delete',
								confirm: function() {
									return confirm(l('delete customer confirm'));
								},
								icon: '<i class="icon icon-trash-o"></i> ',
							});

						return deleteCustomer;
					},
				}
			});

			var checkToggle,
				checkToggleSearch,
				searchLoading,
				search;

			var footerActions = customersGrid.addFooter(function(columns){
				var tr, td;
				function makeRow(arr) 
				{
					tr = $('<tr></tr>');
					td = $('<td class="form-inline gk-footer" colspan="'+columns+'"></td>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);
					return tr;
				}

				function createCheckToggle(name) 
				{
					var button = $('#'+name)
						.gkButton({
							name: name,
							title: l('check all'),
							className: name,
							css: {
								'padding-left': '10px',
								'padding-right': '10px',
								'margin-left': '0',
								'display': 'inline-block',
							},
							attr: {
								'data-checked': 0,
							},

							click: function(event) {

								function isChecked() {
									return button.data('checked') ? true : false;
								};

								function toggleName(trueStr, falseStr) {
									if (isChecked()) {
										button.data('checked', false);
										button.changeTitle(falseStr);
										return false;
									} else {
										button.data('checked', true);
										button.changeTitle(trueStr);
										return true;
									}
								}

								if (toggleName(l('uncheck all'), l('check all'))) {
									customersDataSource.checkAll();
								} else {
									customersDataSource.uncheckAll();
								}
							},
						});
					return button;
				}

				checkToggle = createCheckToggle('check-toggle');
				checkToggleSearch = createCheckToggle('check-toggle-search');
				checkToggleSearch.addClass('gk-onfilter');
				checkToggleSearch.hide();

				self.addVar('customersCheckAll', checkToggle);

				btnExportCsv = self.buildExportToCSVData(customersDataSource, NewsletterPro.dataStorage.get('csv_export_list_ref.LIST_CUSTOMERS'));

				return makeRow([checkToggle, checkToggleSearch, btnExportCsv]);
			}, 'prepend');

			function clearCategoriesFilters()
			{
				var categoriesTree = NewsletterPro.modules.categoriesTree;
				categoriesTree.setNeedUncheckAll(true);
				categoriesTree.uncheckAllCategories(false);
			}

			function clearByPurchaseFilters()
			{

				searchByPurchase.clearVal();
				productList.removeItems();

				pfbGrid.dataSource.parse(function(item){
					item.removeFromScreen();
				});
				pfbGrid.dataSource.setData([]);
				pfbGrid.dataSource.sync();
			}

			function setFilterBirthdayFrom(val)
			{
				birthdayDate.from = val;
			}

			function setFilterBirthdayTo(val)
			{
				birthdayDate.to = val;
			}

			function clearByBirthdayFilter()
			{
				if (typeof birthdayFrom !== 'undefined')
				{
					birthdayFrom.val('');
					setFilterBirthdayFrom('');
				}

				if (typeof birthdayTo !== 'undefined')
				{
					birthdayTo.val('');
					setFilterBirthdayTo('');
				}
			}

			function clearRangeSelection()
			{
				resetSliderRange('clear', 1, customersCount());
			}

			var birthdayDate = {
				'from': '',
				'to': '',
			};

			function customersCount()
			{
				return customersDataSource.items.length;
			}

			function resetSliderRange(trigger, min, max)
			{
				if (trigger !== 'range' && typeof sliderRangeCustomer !== 'undefined')
				{
					var reset = {
						min : min,
						max : max,
						valueMin : min,
						valueMax : max,
						values : [min, max],
					};
					if (max <= 0)
					{
						reset['snap'] = 0;
						reset['min'] = 0;
						reset['max'] = 1;
					}
					sliderRangeCustomer.reset(reset);
					sliderRangeCustomer.resetPositionMin();
					sliderRangeCustomer.resetPositionMax();
				}
			}

			function resetTotalSpentFilter(min, max)
			{
				if (typeof sliderTotalSpent !== 'undefined')
				{
					var reset = {
						min: min,
						max: max,
						valueMin: min,
						valueMax: max,
						values: [min, max],
					};

					sliderTotalSpent.reset(reset);
					sliderTotalSpent.resetPositionMin();
					sliderTotalSpent.resetPositionMax();
				}
			}

			function clearTotalSpentFilter()
			{
				resetTotalSpentFilter(0, maxTotalSpent);
			}

			function clearFilterByCountries()
			{
				if (typeof filterByCountryDataSource !== 'undefined')
				{
					filterByCountrySearch.val('');
					filterByCountryDataSource.clearSearch();
					filterByCountryDataSource.uncheckAll();
				}
			}

			var sliderRangeCustomer;
			var sliderTotalSpent;
			var birthdayFrom;
			var birthdayTo;
			var fbbClear;

			var searchByPurchase;
			var productList;
			var pfbGrid;

			var headerActions1 = customersGrid.addHeader(function(columns){
				var tr, 
					td;
				function makeRow(arr) 
				{
					tr = $('<tr></tr>');
					td = $('<th class="gk-header-datagrid customers-header" colspan="'+columns+'"></th>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);

					return tr;
				}

				var filterGroups = $('#gk-filter-groups').gkDropDownMenu({
					title: l('groups'),
					name: 'gk-filter-groups',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},
					data: NewsletterPro.dataStorage.data.filter_groups,
					change: function(values) {
						appyFilters('groups');
					},
				});

				var dataLangs = NewsletterPro.dataStorage.data.filter_languages;
				$.each(dataLangs, function(i, lang){
					dataLangs[i].title = '<img src="' + lang.img_path + '" width="16" height="11">' + '<span style="margin-left: 10px;">'+lang.title+'</span>';
				});

				var filterLanguages = $('#gk-filter-languages').gkDropDownMenu({
					title: l('languages'),
					name: 'gk-filter-languages',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},
					data: dataLangs,
					change: function(values) {
						appyFilters('languages');
					},
				});

				var filterShops = $('#gk-filter-shops').gkDropDownMenu({
					title: l('shops'),
					name: 'gk-filter-shops',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},
					data: NewsletterPro.dataStorage.data.filter_shops,
					change: function(values) {
						appyFilters('shops');
					},
				});

				var filterGender = $('#gk-filter-gender').gkDropDownMenu({
					title: l('gender'),
					name: 'gk-filter-gender',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: NewsletterPro.dataStorage.get('filter_genders'),

					change: function(values) {
						appyFilters('gender');
					},
				});

				var filterSubscribed = $('#gk-filter-subscribed').gkDropDownMenu({
					title: l('subscribed'),
					name: 'gk-filter-gender',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: [
						{'title': l('yes'), 'value': 1},
						{'title': l('no'), 'value': 0},
					],

					change: function(values) {
						appyFilters('subscribed');
					},
				});
				
				var filterActive = $('#gk-filter-active').gkDropDownMenu({
					title: l('active'),
					name: 'gk-filter-active',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: [
						{'title': l('yes'), 'value': 1},
						{'title': l('no'), 'value': 0},
					],

					change: function(values) {
						appyFilters('active');
					},
				});
				var filterPostcode = $('#gk-filter-postcode').gkDropDownMenu({
					title: l('postcode'),
					name: 'gk-filter-postcode',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: [
						{'title': l('0'), 'value': 0},
						{'title': l('1'), 'value': 1},
						{'title': l('2'), 'value': 2},
						{'title': l('3'), 'value': 3},
						{'title': l('4'), 'value': 4},
						{'title': l('5'), 'value': 5},
						{'title': l('6'), 'value': 6},
						{'title': l('7'), 'value': 7},
						{'title': l('8'), 'value': 8},
						{'title': l('9'), 'value': 9},
					],

					change: function(values) {
						appyFilters('postcode');
					},
				});


				if (NewsletterPro.dataStorage.get('view_active_only'))
					filterSubscribed.hide();

				function getCategories(content) {
					var categories = content.find('input[type="checkbox"]');
					if (categories.length > 0)
						return categories;
					return [];
				}

				function getSelected(categoryTree) {
					var values = [];
					if (typeof categoryTree !== 'array' && typeof categoryTree !== 'undefined') {
						values = $.map(categoryTree, function(item){
							var item = $(item);
							var val = item.val();
							if ( val !== 'undefined' && item.is(':checked')) {
								return parseInt(val);
							}
						});
					}
					return values;
				}

				var categoryTree;
				var winCategoryTree = new gkWindow({
						width: 640,
						height: 540,
						title: l('category filter title'),
						className: 'category-filters',
						show: function(win) {},
						close: function(win) {},
						content: function(win) {
							$.postAjax({'submit': 'getCategoryTree'}, 'html').done(function(response){
								win.setContent(response);
							});
							return '';
						}
					});

				var filterCategories = $('#filter-categories')
					.gkButton({
						name: 'filter-categories',
						title: l('categories'),
						className: 'filter-categories',
						css: {
							'padding-left': '10px',
							'padding-right': '10px',
							'float': 'left',
							'margin-right': '10px',
							'position': 'relative',
						},
						attr: {
							'data-checked': 0,
						},
						click: function(event) {
							winCategoryTree.show();
						},
					});

				self.addEvent('clickOnCategoryBox', function(){
					categoryTree = getCategories(winCategoryTree.template);
					appyFilters('categories');
				});

				function getFilterByPurchaseContent(func)
				{
					$.postAjax({'submit': 'getFilterByPurchaseContent'}, 'html').done(function(content){
						func($(content));
					});
				}

				function getFiltersByPurchase()
				{
					var ids = [];
					pfbGrid.dataSource.parse(function(item)
					{
						ids.push(item.data.id);
					});

					return ids;
				}

				function buildFBPGrid(fpbDataGrid, data)
				{
					data = data || [];

					var fbpDataModel = new gk.data.Model({
						id: 'id_product',
					});

					var fbpDataSource = new gk.data.DataSource({
						pageSize: 7,
						transport: {
							data: data,
						},
						schema: {
							model: fbpDataModel
						},
					});

					fpbDataGrid.gkGrid({
						dataSource: fbpDataSource,
						selectable: false,
						currentPage: 1,
						pageable: true,
						template: {
							actions: function(item) 
							{
								var a = $('<a href="javascript:{}" class="btn btn-default"><i class="icon icon-times" style="margin-right: 7px;"></i> '+l('remove')+'</a>');
								a.on('click', function(){

									var id     = item.data.id_product;
									var search = pfbGrid.dataSource.getItemByValue('data.id_product', id);
									var gridData   = pfbGrid.dataSource.getData();
									if (search)
									{
										search.removeFromScreen()
										index = getProductIndexById(gridData, id);
										if (index > -1)
											gridData.splice(index, 1);
									}

									var pli = productList.getItems(false),
										len = pli.length;

									if (len > 0)
									{
										var current;
										for(var i = 0; i < len; i++)
										{
											current = pli[i];
											if (current.data.id == id)
											{
												current.remove();
												break;
											}
										}
									}

								});
								return a;
							},
							price_display: function(item, value)
							{
								return '<span style="font-weight: bold;">'+value+'</span>';
							},
							reference: function(item, value)
							{
								return '<span style="font-weight: bold;">'+value+'</span>';
							},
							image: function(item)
							{
								return '<img src="'+item.data.thumb_path+'" style="width: 40px; height: 40px;">';
							},
						}
					});

					return {
						'dataSource': fbpDataSource,
						'dataModel': fbpDataModel,
						'dataGrid': fpbDataGrid,
					};
				}

				function getProductIndexById(gridData, id)
				{
					for(var i = 0; i < gridData.length; i++)
					{
						var itm = gridData[i];

						if (itm.id_product == id)
							return i;
					}
					return -1;
				}

				var filterByInterest = $('#gk-customer-filter-by-interest-np').gkDropDownMenu({
					title: l('by list of interest'),
					name: 'gk-filter-loi',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: NewsletterPro.dataStorage.get('filter_list_of_interest'),
					activeClass: {
						enable: '',
						disable: 'btn-filter-list-of-interst-inactive',
					},
					change: function(values) 
					{
						appyFilters();
					},
				});

				var winByPurchase = new gkWindow({
						width: 640,
						height: 540,
						title: l('filter by purchase'),
						className: 'filter-by-purchase-window',
						show: function(win) {},
						close: function(win) {},
						content: function(win, parent) 
						{
							getFilterByPurchaseContent(function(content)
							{
								win.setContent(content);

								searchByPurchase = new gkSearch({
									read: {'submit': 'searchByPurchase'},
									element: content.find('#filter-poduct-search'),
									ajaxLoader: content.find('.product-search-span'),
									result: function(response)
									{
										var products = response.products;
										productList.createItems(products);
									},

									reset: function()
									{
										productList.removeItems();
									},
								});

								productList = new gkProductList({
									element: content.find('#filter-product-list'),
									inList: function(data)
									{
										var id     = data.id_product;
										var gridData   = pfbGrid.dataSource.getData();
										index = getProductIndexById(gridData, id);

										if (index > -1)
											return false;
										return true;
									},
									add: function(data, item)
									{
										var gridData   = pfbGrid.dataSource.getData();
										var id         = data.id_product;

										if (!pfbGrid.dataSource.getItemByValue('data.id_product', id))
											gridData.push(data);

										pfbGrid.dataSource.setData(gridData);
										pfbGrid.dataSource.sync().done(function()
										{
											appyFilters('purchase');
										});

									},

									remove: function(data, item)
									{
										var id     = data.id_product;
										var search = pfbGrid.dataSource.getItemByValue('data.id_product', id);
										var gridData   = pfbGrid.dataSource.getData();

										if (search)
										{
											search.removeFromScreen()

											index = getProductIndexById(gridData, id);
											if (index > -1)
												gridData.splice(index, 1);
										}

										appyFilters('purchase');
									},
								});

								pfbGrid = buildFBPGrid(content.find('#fbp-grid'));

							});

							return '<span class="ajax-loader" style="margin-left: 312px; margin-top: 224px;"></span>';
						}
					});

				var filterByPurchase = $('#by-purchase-filters')
					.gkButton({
						name: 'by-purchase-filters',
						title: l('by purchase'),
						className: 'by-purchase-filters',
						css: {
							'padding-left': '10px',
							'padding-right': '10px',
							'float': 'left',
							'margin-right': '10px',
						},
						attr: {
							'data-checked': 0,
						},
						click: function(event) {
							winByPurchase.show();
						},
					});

				function getFilterByBirthdayContent(func)
				{
					$.postAjax({'submit': 'getFilterByBirthdayContent', fbb_class: 'customers'}, 'html').done(function(content){
						func($(content));
					});
				}

				function getFilterByBirthday()
				{
					return birthdayDate;
				}

				function getMySqlDate(dateObj)
				{
					var year = dateObj.selectedYear,
						mounth = (String(dateObj.selectedMonth).length == 1 ? '0'+String(dateObj.selectedMonth+1) : String(dateObj.selectedMonth+1)),
						day = (String(dateObj.selectedDay).length == 1 ? '0'+String(dateObj.selectedDay) : String(dateObj.selectedDay));
					return mysql_date = year+'-'+mounth+'-'+day;
				}

				var winByBirthday = new gkWindow({
						width: 640,
						height: 320,
						title: l('filter by birthday'),
						className: 'filter-by-birthday-window',
						show: function(win) {},
						close: function(win) {},
						content: function(win, parent) 
						{
							getFilterByBirthdayContent(function(content)
							{
								win.setContent(content);

								birthdayFrom = content.find('#fbb-from-customers');
								birthdayTo = content.find('#fbb-to-customers');
								fbbClear = content.find('#fbb-clear-customers');								

								birthdayFrom.datepicker({ 
									dateFormat: self.box.dataStorage.get('jquery_date_birthday'),
									onSelect: function(date, dateObj)
									{
										setFilterBirthdayFrom(getMySqlDate(dateObj));
										appyFilters('birthday');
									},
									beforeShow: function(input, inst)
									{
										if (!inst.dpDiv.hasClass('date-birthday'))
											inst.dpDiv.addClass('date-birthday');
									}
								});

								birthdayTo.datepicker({
									dateFormat: self.box.dataStorage.get('jquery_date_birthday'),
									onSelect: function(date, dateObj)  
									{
										setFilterBirthdayTo(getMySqlDate(dateObj));
										appyFilters('birthday');
									},
									beforeShow: function(input, inst)
									{
										if (!inst.dpDiv.hasClass('date-birthday'))
											inst.dpDiv.addClass('date-birthday');
									}
								});

								birthdayFrom.on('change', function()
								{
									if ($.trim($(this).val()) == '')
									{
										setFilterBirthdayFrom('');
										appyFilters('birthday');
									}
								});

								birthdayTo.on('change', function()
								{
									if ($.trim($(this).val()) == '')
									{
										setFilterBirthdayTo('');
										appyFilters('birthday');
									}
								});

								fbbClear.on('click', function()
								{
									clearByBirthdayFilter();
									appyFilters('birthday');
								});

							});
							return '<span class="ajax-loader" style="margin-left: 310px; margin-top: 119px;"></span>';
						}
					});

				var filterByBirthday = $('#by-birthday-filters')
					.gkButton({
						name: 'by-birthday-filters',
						title: l('by birthday'),
						className: 'by-birthday-filters',
						css: {
							'padding-left': '10px',
							'padding-right': '10px',
							'margin-right': '10px',
							'float': 'left',
						},
						attr: {
							'data-checked': 0,
						},
						click: function(event) {
							winByBirthday.show();
						},
					});

				function getRangeSelection()
				{
					return {
						'min': (typeof sliderRangeCustomer !== 'undefined' && !isNaN(sliderRangeCustomer.getValueMin()) ? sliderRangeCustomer.getValueMin() : 0) ,
						'max': (typeof sliderRangeCustomer !== 'undefined' && !isNaN(sliderRangeCustomer.getValueMax()) ? sliderRangeCustomer.getValueMax() : 0) ,
					};
				}

				var winRangeSelection = new gkWindow({
						width: 640,
						height: 150,
						title: l('range selection'),
						className: 'range-selection-window',
						show: function(win) {
							if (typeof sliderRangeCustomer !== 'undefined')
								sliderRangeCustomer.refresh();
						},
						close: function(win) {},
						content: function(win, parent) 
						{
							customersDataSource.ready().done(function(){
								self.getRangeSelectionContent(function(content)
								{
									win.setContent(content);

									sliderRangeCustomer = gkSliderRange({
										target: content.find('#slider-range-selection'),
										min : 1,
										max : customersCount(),
										valueMin : 1,
										valueMax : customersCount(),
										editable: true,
										values : [1, customersCount()],

										move: function(obj) {},
										done: function(obj) 
										{
											appyFilters('range');
										},
									});
								});
							});

							return '<span class="ajax-loader" style="margin-left: 310px; margin-top: 36px;"></span>';
						}
					});
				
				var rangeSelection = $('#range-selection')
				.gkButton({
					name: 'range-selection',
					title: l('range selection'),
					className: 'range-selection',
					css: {
						'padding-left': '10px',
						'padding-right': '10px',
						'margin-right': '10px',
						'float': 'left',
					},
					attr: {
						'data-checked': 0,
					},
					click: function(event) 
					{
						winRangeSelection.show();
					},
				});

				var winFilterTotalSpentTemplate;
				var winFilterTotalSpent = new gkWindow({
					width: 640,
					height: 150,
					title: l('Total spent filter'),
					className: 'win-filter-total-spent',
					show: function(win) 
					{
						if (typeof sliderTotalSpent === 'undefined')
						{
							$.postAjax({'submit': 'getMaxTotalSpent'}).done(function(ts){
								maxTotalSpent = Number(ts);

								sliderTotalSpent = gkSliderRange({
									target: winFilterTotalSpentTemplate,
									min : 0,
									max : maxTotalSpent,
									prefix: ' ' + NewsletterPro.dataStorage.get('currency_default.sign'),
									valueMin : 0,
									valueMax : maxTotalSpent,

									values : [0, maxTotalSpent],

									move: function(obj) {},
									done: function(obj) 
									{
										appyFilters('total_spent');
									},
								});
							});
						}
						else
							sliderTotalSpent.refresh();
					},
					close: function(win) {},
					content: function(win, parent) 
					{
						winFilterTotalSpentTemplate = $('\
							<div id="slider-filter-total-spent" class="slider-filter-total-spent">\
								<div class="slider-container">\
									<label>'+l('Total spent')+'</label>\
									<div id="slider-range-selection"></div>\
								</div>\
							</div>\
						');

						return winFilterTotalSpentTemplate;
					}
				});

				var filterTotalSpent = $('#filter-total-spent')
				.gkButton({
					name: '',
					icon: '<i class="icon icon-usd"></i>',
					title: '',
					className: 'filter-total-spent',
					css: {
						'padding-left': '10px',
						'padding-right': '10px',
						'margin-right': '10px',
						'float': 'left',
					},
					attr: {
						'data-checked': 0,
					},
					click: function(event) 
					{
						winFilterTotalSpent.show();
					},
				});

				var winFilterByCountryTemplate;
				var winFilterByCountry = new gkWindow({
					width: 640,
					height: 475,
					setScrollContent: 415,
					title: l('Filter customers by country'),
					className: 'win-filter-total-spent',
					show: function(win)
					{
						if (typeof filterByCountryDataSource === 'undefined')
						{
							filterByCountryDataModel = new gk.data.Model({
								id: 'id_country',
							});

							filterByCountryDataSource = new gk.data.DataSource({
								pageSize: 10,
								trySteps: 2,
								transport: 
								{
									read: {
										url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getCountries',
										dataType: 'json',
									},

									search: {
										url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=searchCountries&value',
										dataType: 'json',
									},
								},
								schema: {
									model: filterByCountryDataModel
								},
								errors: 
								{
									read: function(xhr, ajaxOptions, thrownError) 
									{
										filterByCountryDataSource.syncStepAvailableAdd(3000, function(){
											filterByCountryDataSource.sync();
										});
									},
								},
							});

							filterByCountryDataGrid = winFilterByCountryTemplate.find('#filter-by-country-list');

							filterByCountryDataGrid.gkGrid({
								dataSource: filterByCountryDataSource,
								selectable: false,
								checkable: true,
								currentPage: 1,
								pageable: true,
								start: function()
								{
									// dom.usersAjaxLoader.show();
								},
								done: function(dataSource) 
								{
									// dom.customersCount.html(dataSource.items.length);
									// dom.usersAjaxLoader.hide();
								},
								template: {
									active: function(item, value)
									{
										var active = Number(value);

										if (active)
											return '<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>';
										else
											return '<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>';
									},
									chackbox: function(item, value) 
									{
										var checkBox = $('<input type="checkbox" value="'+value+'" '+(item.isChecked() ? 'checked="checked"' : '')+'> ');
										return checkBox;
									},
								},
							});
					
							filterByCountryDataGrid.addHeader(function(columns){
								var timer = null,
									searchBox = $('\
										<div class="clearfix">\
											<span>'+l('Search')+':</span>\
											<div class="fixed-width-xxl filter-by-country-search-box">\
												<input id="filter-by-country-search" class="form-control filter-by-country-search" type="text">\
												<span id="filter-by-country-search-loading" class="filter-by-country-search-loading" style="display: none;"></span>\
											</div>\
											<a id="filter-by-country-clear-selection" href="javascript:{}" class="btn btn-default pull-right">\
												<i class="icon icon-remove"></i>\
												'+l('Clear Selection')+'\
											</a>\
										</div>\
									'),
									searchLoading = searchBox.find('#filter-by-country-search-loading'),
									clearSelection = searchBox.find('#filter-by-country-clear-selection');

								filterByCountrySearch = searchBox.find('#filter-by-country-search');

								filterByCountrySearch.on('keyup', function(event){
									var val = $.trim(filterByCountrySearch.val());

									if (val.length < 3) 
									{
										filterByCountryDataSource.clearSearch();
										return true;
									} 

									searchLoading.show();

									if (timer != null) clearTimeout(timer);

									timer = setTimeout(function(){

										filterByCountryDataSource.search(val).done(function(response){
											filterByCountryDataSource.applySearch(response);
											searchLoading.hide();
										});

									}, 300);
								});

								clearSelection.on('click', function(event){
									clearFilterByCountries();
								});

								return filterByCountryDataGrid.makeRow([searchBox]);
							});

						}
					},
					close: function(win) 
					{
						appyFilters('filter_by_country');
					},
					content: function(win, parent) 
					{
						winFilterByCountryTemplate = $('\
							<div class="form-group clearfix">\
								<table id="filter-by-country-list" class="table table-bordered filter-by-country-list">\
									<thead>\
										<tr>\
											<th class="chackbox" data-template="chackbox">&nbsp;</th>\
											<th class="np-fc-country-name" data-field="name">'+l('Country Name')+'</th>\
											<th class="np-fc-iso-code" data-field="iso_code">'+l('ISO Code')+'</th>\
											<th class="np-fc-active" data-field="active">'+l('Active')+'</th>\
										</tr>\
									</thead>\
								</table>\
							</div>\
						');

						return winFilterByCountryTemplate;
					}
				});

				var filterByCountry = $('#filter-by-country')
				.gkButton({
					name: '',
					icon: '<i class="icon icon-globe"></i>',
					title: '',
					className: 'filter-by-country',
					css: {
						'padding-left': '10px',
						'padding-right': '10px',
						'margin-right': '10px',
						'float': 'left',
					},
					attr: {
						'data-checked': 0,
					},
					click: function(event) 
					{
						winFilterByCountry.show();
					},
				});

				var getTotalSpent = function()
				{
					return {
						from: (typeof sliderTotalSpent !== 'undefined' && !isNaN(sliderTotalSpent.getValueMin()) ? sliderTotalSpent.getValueMin() : 0),
						to: (typeof sliderTotalSpent !== 'undefined' && !isNaN(sliderTotalSpent.getValueMax()) ? sliderTotalSpent.getValueMax() : 0),
					}
				};

				var getCountries = function()
				{
					var countriesIso = [],
						selection;

					if (typeof filterByCountryDataSource !== 'undefined')
					{
						selection = filterByCountryDataSource.getSelection();

						for (var i = 0; i < selection.length; i++) {
							countriesIso.push(selection[i].data.iso_code);
						}
					}

					return countriesIso;
				};

				function appyFilters(trigger) 
				{
					var filters = {
						groups: filterGroups.getSelected(),
						shops: filterShops.getSelected(),
						gender: filterGender.getSelected(),
						subscribed: filterSubscribed.getSelected(),
						languages: filterLanguages.getSelected(),
						active: filterActive.getSelected(),
						postcode: filterPostcode.getSelected(),
						categories: getSelected(categoryTree),
						by_interest: filterByInterest.getSelected(),
						purchased_product: getFiltersByPurchase(),
						by_birthday: getFilterByBirthday(),
						total_spent: getTotalSpent(),
						filter_by_country: getCountries(),
					};

					var breakFilters = false;

					$.each(filters, function(i, filter){
						if (filter.length) {
							breakFilters = true;
						}
					});

					if ($.trim(filters.by_birthday.from) == '' || $.trim(filters.by_birthday.to) == '' )
						delete filters['by_birthday'];
					else
						breakFilters = true;

					if (trigger == 'range')
					{
						filters['range_selection'] = getRangeSelection();

						if ($.trim(filters.range_selection.min) == 0 || $.trim(filters.range_selection.max) == 0 )
							delete filters['range_selection'];
						else
							breakFilters = true;
					}

					if ((filters.total_spent.from == 0 && filters.total_spent.to == 0) || (filters.total_spent.from == 0 && filters.total_spent.to == maxTotalSpent))
						delete filters['total_spent'];
					else
						breakFilters = true;

					if (filters.filter_by_country.length == 0)
						delete filters['filter_by_country'];
					else
						breakFilters = true;

					if (breakFilters) 
					{
						search.val('');
						checkToggle.hide();
						checkToggleSearch.css({'display': 'inline-block'});

						customersDataSource.filter(filters).done(function(response){
							customersDataSource.applySearch(response);
								resetSliderRange(trigger, 1, response.length);
						});
					} 
					else 
					{
						resetSliderRange(trigger, 1, customersCount());

						checkToggle.show();
						checkToggleSearch.hide();
						customersDataSource.clearSearch();
					}
				}

				var filtersText = $('<span style="margin-left: 0px; margin-right: 10px; float: left; line-height: 28px;">'+l('filters')+'</span>');

				self.reset                              = self.reset || {};
				self.reset.customer                     = self.reset.customer || {};
				self.reset.customer['filterGroups']     = filterGroups;
				self.reset.customer['filterLanguages']  = filterLanguages;
				self.reset.customer['filterShops']      = filterShops;
				self.reset.customer['filterGender']     = filterGender;
				self.reset.customer['filterSubscribed'] = filterSubscribed;
				self.reset.customer['filterActive'] = filterActive;
				self.reset.customer['filterPostcode'] = filterPostcode;
				self.reset.customer['filterCategories'] = filterCategories;
				self.reset.customer['filterByInterest'] = filterByInterest;
				self.reset.customer['filterByPurchase'] = filterByPurchase;
				self.reset.customer['filterByBirthday'] = filterByBirthday;
				self.reset.customer['filterTotalSpent'] = filterTotalSpent;
				self.reset.customer['filterByCountry'] = filterByCountry;
				self.reset.customer['rangeSelection']   = rangeSelection;

				self.addVar('filterCustomers', {
					'groups': filterGroups,
					'languages': filterLanguages,
					'shops': filterShops,
					'gender': filterGender,
					'subscribed': filterSubscribed,
					'active': filterActive,
					'postcode': filterPostcode,
					'by_category': filterCategories,
					'by_interest': filterByInterest,
					'by_purchase': filterByPurchase,
					'by_birthday': filterByBirthday,
					'total_spent': filterTotalSpent,
					'filter_by_country': filterByCountry,
					'range': rangeSelection,
				});

				self.addVar('applyFilerCustomersCallback', appyFilters);

				self.addVar('customers', customersDataSource);

				return makeRow([filtersText ,filterGroups, filterLanguages ,filterShops, filterGender, filterSubscribed, filterActive, filterPostcode, filterTotalSpent, filterByCountry, filterCategories, filterByInterest, filterByPurchase, filterByBirthday, rangeSelection]);
			}, 'prepend');

			var headerActions = customersGrid.addHeader(function(columns){
				var tr, 
					td, 
					searchDiv,
					timer = null, 
					searchText;

				function makeRow(arr) 
				{
					tr = $('<tr></tr>');
					td = $('<th class="gk-header-datagrid customers-header" colspan="'+columns+'"></th>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);

					return tr;
				}

				searchDiv = $('<div class="customers-search-div"></div>');
				search = $('<input class="form-control customers-search" type="text">');
				searchLoading = $('<span class="customers-search-loading" style="display: none;"></span>');

				search.on('keyup', function(event){
					var val = $.trim(search.val());

					if (val.length < 3) {
						checkToggle.show();
						checkToggleSearch.hide();

						customersDataSource.clearSearch();
						return true;
					} else {
						checkToggle.hide();
						checkToggleSearch.css({'display': 'inline-block'});
					}

					searchLoading.show();

					if ( timer != null ) clearTimeout(timer);

					timer = setTimeout(function(){

						customersDataSource.search(val).done(function(response){
							customersDataSource.applySearch(response);
							searchLoading.hide();
						});

					}, 300);

				});
				searchText = $('<span>'+l('search')+':</span>');

				searchDiv.append(search);
				searchDiv.append(searchLoading);

				var clearFilters = $('#clear-filters')
						.gkButton({
							name: 'clear-filters',
							title: l('clear filters'),
							className: 'clear-filters',
							css: {
								'padding-left': '10px',
								'padding-right': '10px',
								'margin-left': '0',
								'margin-right': '0',
								'position': 'absolute',
								'right': '5px',
							},
							attr: {
								'data-checked': 0,
							},

							click: function(event) {

								search.val('');
								self.reset.customer.filterGroups.uncheckAll();
								self.reset.customer.filterShops.uncheckAll();
								self.reset.customer.filterLanguages.uncheckAll();
								self.reset.customer.filterGender.uncheckAll();
								self.reset.customer.filterSubscribed.uncheckAll();
								self.reset.customer.filterActive.uncheckAll();
								self.reset.customer.filterPostcode.uncheckAll();

								clearCategoriesFilters();
								clearByPurchaseFilters();
								clearByBirthdayFilter();
								clearRangeSelection();
								clearTotalSpentFilter();
								clearFilterByCountries();

								checkToggle.show();
								checkToggleSearch.hide();
								customersDataSource.clearSearch();
							},
							icon: '<i class="icon icon-times"></i> ',
						});

				self.reset = self.reset || {};
				self.reset.customer = self.reset.customer || {};
				self.reset.customer['clearFilters']     = clearFilters;

				self.vars.filterCustomers['clear'] = clearFilters;

				return makeRow([searchText, searchDiv, clearFilters]);
			}, 'prepend');

			self.resetCustomersButton();


			var box = NewsletterPro;
			var winBouncedEmailsContent;

			var winBouncedEmails = new gkWindow({
				width: 640,
				title: l('bounced emails'),
				className: 'bounced-emails-window',
				show: function(win) {},
				close: function(win) {},
				content: function(win, parent) 
				{
					var form = $('\
						<form id="form-bounced-emails" method="post" enctype="multipart/form-data">\
							<div class="form-group clearfix">\
								<label class="control-label col-sm-4"><span class="label-tooltip">'+l('select the csv file')+'</span></label>\
								<div class="col-sm-8 clearfix">\
									<div class="input-group">\
										<span class="input-group-addon">'+l('File')+'</span>\
										<input type="file" name="bounced_emails" class="form-control" style="float: left; margin-right: 10px;">\
										<span class="input-group-addon">'+l('Separator')+'</span>\
										<input type="text" name="bounced_csv_separator" class="form-control" value=";" style="width: 30px; text-align: center; float: left;">\
										<div class="clear"></div>\
									</div>\
								</div>\
								<div class="clear"></div>\
							</div>\
							<div class="clear"></div>\
							<div class="form-group clearfix">\
								<label class="control-label padding-top col-sm-4">'+l('bounced emails method')+'</label>\
								<div class="col-sm-8 clearfix">\
									<div class="radio">\
										<label for="bounced-method-delete" class="in-win">\
											<input id="bounced-method-delete" type="radio" name="bounced_method" value="-1" checked>'+l('delete bounced emails')+'\
										</label>\
									</div>\
									<div class="radio">\
										<label for="bounced-method-unsubscribe" class="in-win">\
											<input id="bounced-method-unsubscribe" type="radio" name="bounced_method" value="0"> '+l('unsubscribe bounced emails')+'\
										</label>\
									</div>\
									<p class="help-block">'+l('bounced emails info')+'</p>\
									<div class="clear"></div>\
								</div>\
							</div>\
							<label class="control-label padding-top col-sm-4">'+l('apply on the lists')+'</label>\
							<div class="col-sm-8 clearfix">\
								<div class="checkbox">\
									<label for="bounced-customers" class="in-win">\
										<input id="bounced-customers" type="checkbox" name="bounced_customers_list" value="1">'+l('customers list')+'\
									</label>\
								</div>\
								<div class="checkbox">\
									<label for="bounced-visitors" class="in-win">\
										<input id="bounced-visitors" type="checkbox" name="bounced_visitors_list" value="1">'+l('visitors subscribed at module block newsletter')+'\
									</label>\
								</div>\
								<div class="checkbox">\
									<label for="bounced-visitors-np" class="in-win">\
										<input id="bounced-visitors-np" type="checkbox" name="bounced_visitors_np_list" value="1">'+l('visitors subscribed at module newsletter pro')+'\
									</label>\
								</div>\
								<div class="checkbox">\
									<label for="bounced-added" class="in-win">\
										<input id="bounced-added" type="checkbox" name="bounced_added_list" value="1">'+l('added list')+'\
									</label>\
								</div>\
							</div>\
							<div class="form-group clearfix">\
								<div class="col-sm-8 col-sm-offset-4">\
									<a id="submit-bounced-emails" href="javascript:{}" class="btn btn-default"><span class="btn-ajax-loader"></span> <i class="icon icon-eraser"></i> '+l('remove bounced')+'</a>\
								</div>\
							</div>\
							<div class="form-group clearfix">\
								<div style="display: block; height: auto; background-position: 5px; padding-top: 10px; padding-bottom: 10px;" class="hint clear">\
									<p style="margin-top: 0;" class="cron-link"><span style="color: black;">'+l('webhook url')+'</span> <span class="icon icon-cron-link"></span>'+box.dataStorage.get('bounce_link')+'</p>\
									<p style="margin-bottom: 0;">'+l('webhook info')+'</p>\
									<div class="clear"></div>\
								</div>\
							</div>\
						</form>\
					');

					var submitBouncedEmails = form.find('#submit-bounced-emails');
					
					submitBouncedEmails.on('click', function(event){
						var conf = confirm(l('confirm delete bounced'));

						if (!conf)
							return;

						box.showAjaxLoader(submitBouncedEmails);

						$.submitAjax({'submit': 'deleteBouncedEmails', name: 'deleteBouncedEmails', form: form}).done(function(response){
							box.hideAjaxLoader(submitBouncedEmails);
							if (response.success)
							{
								var vars = box.modules.sendNewsletters.vars;

								if (response.lists.indexOf('customers') != -1 && vars.hasOwnProperty('customers'))
									vars.customers.sync();
								
								if (response.lists.indexOf('visitors') != -1 && vars.hasOwnProperty('visitors'))
									vars.visitors.sync();
								
								if (response.lists.indexOf('visitors_np') != -1 && vars.hasOwnProperty('visitorsNP'))
									vars.visitorsNP.sync();
								
								if (response.lists.indexOf('added') != -1 && vars.hasOwnProperty('added'))
									vars.added.sync();

								alert(box.displayAlert(response.msg));
							}
							else
								box.alertErrors(response.errors);
						});
					});
					
					return form;
				}
			});

			dom.btnBouncedEmails.on('click', function(event){
				winBouncedEmails.show();
			});

		});
	},

	initVisitorsGrid: function() 
	{
		var self = this;
		self.ready(function(dom){

			// if this feature is active stop the code from execution
			if (self.isNewsletterProSubscriptionActive())
				return;

			var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.sendNewsletters),
				visitorsDataModel,
				visitorsDataSource,
				visitorsGrid = dom.visitorsGrid;

			visitorsDataModel = new gk.data.Model({
				id: 'id',
			});

			visitorsDataSource = new gk.data.DataSource({
				pageSize: 5,
				transport: {
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getVisitors',
						dataType: 'json',
					},
					update: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=updateVisitor&id',
						dataType: 'json',
					},

					destroy: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=deleteVisitor&id',
						type: 'POST',
						dateType: 'json',
						success: function(response, itemData) {
							if(!response)
								alert(l('delete visitor'));
						},
						error: function(data, itemData) {
							alert(l('delete visitor'));
						},
						complete: function(data, itemData) {},
					},

					search: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=searchVisitor&value',
						dataType: 'json',
					},

					filter: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=filterVisitor',
						dataType: 'json',
					},

				},
				schema: {
					model: visitorsDataModel
				},
				trySteps: 2,
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						visitorsDataSource.syncStepAvailableAdd(3000, function(){
							visitorsDataSource.sync();
						});
					},
				}
			});

			visitorsGrid.gkGrid({
				dataSource: visitorsDataSource,
				selectable: false,
				checkable: true,
				currentPage: 1,
				pageable: true,
				start: function()
				{
					dom.visitorsAjaxLoader.show();
				},
				done: function(dataSource) 
				{
					dom.visitorsCount.html(dataSource.items.length);
					dom.visitorsAjaxLoader.hide();
				},
				template: {
					chackbox: function(item, value) {
						var checkBox = $('<input type="checkbox" value="'+value+'" '+(item.isChecked() ? 'checked="checked"' : '')+'> ');			
						return checkBox;
					},

					img_path: function(item, value) {
						return '<img src="'+value+'">';
					},

					active: function(item, value) {
						var a = $('<a href="javascript:{}"></a>'),
							data = item.data;

						function isSubscribed() {
							return parseInt(item.data.active) ? true : false;
						}

						function viewOnlySubscribed() {
							return NewsletterPro.dataStorage.getNumber('configuration.VIEW_ACTIVE_ONLY') ? true : false;
						}

						function switchSubscription() 
						{
							if (isSubscribed()) {
								a.html('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
							} else {
								a.html('<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>');
							}
						}

						switchSubscription();

						a.on('click', function(e){
							e.stopPropagation();

							data.active = (isSubscribed() ? 0 : 1);
							item.update().done(function(response) {
								if (!response) {
									alert('error on subscribe or unsubscribe');
								} else {
									if (viewOnlySubscribed()) {
										item.removeFromScreen();
									} else {
										switchSubscription();
									}
								}
							});
						});

						return a;
					},

					actions: function(item) {
						var deleteVisitor = $('#delete-visitor')
							.gkButton({
								name: 'delete',
								title: l('delete'),
								className: 'visitor-delete',
								item: item,
								command: 'delete',
								confirm: function() {
									return confirm(l('delete visitor confirm'));
								},
								icon: '<i class="icon icon-trash-o"></i> ',
							});

						return deleteVisitor;
					},
				}
			});

			function resetSliderRange(trigger, min, max)
			{
				if (trigger !== 'range' && typeof sliderRange !== 'undefined')
				{
					var reset = {
						min : min,
						max : max,
						valueMin : min,
						valueMax : max,
						values : [min, max],
					};
					if (max <= 0)
					{
						reset['snap'] = 0;
						reset['min'] = 0;
						reset['max'] = 1;
					}
					sliderRange.reset(reset);
					sliderRange.resetPositionMin();
					sliderRange.resetPositionMax();
				}
			}

			function clearRangeSelection()
			{
				resetSliderRange('clear', 1, visitorsCount());
			}

			function visitorsCount()
			{
				return visitorsDataSource.items.length;
			}

			var checkToggle,
				checkToggleSearch,
				searchLoading,
				search;

			var sliderRange;

			var footerActions = visitorsGrid.addFooter(function(columns){
				var tr, td;
				function makeRow(arr) {
					tr = $('<tr></tr>');
					td = $('<td class="gk-footer" colspan="'+columns+'"></td>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);
					return tr;
				}

				function createCheckToggle(name) {
					var button = $('#'+name)
						.gkButton({
							name: name,
							title: l('check all'),
							className: name,
							css: {
								'padding-left': '10px',
								'padding-right': '10px',
								'margin-left': '0'
							},
							attr: {
								'data-checked': 0,
							},

							click: function(event) {

								function isChecked() {
									return button.data('checked') ? true : false;
								};

								function toggleName(trueStr, falseStr) {
									if (isChecked()) {
										button.data('checked', false);
										button.changeTitle(falseStr);
										return false;
									} else {
										button.data('checked', true);
										button.changeTitle(trueStr);
										return true;
									}
								}

								if (toggleName(l('uncheck all'), l('check all'))) {
									visitorsDataSource.checkAll();
								} else {
									visitorsDataSource.uncheckAll();
								}
							}
						});
					return button;
				}

				checkToggle = createCheckToggle('check-toggle');
				checkToggleSearch = createCheckToggle('check-toggle-search');
				checkToggleSearch.addClass('gk-onfilter');
				checkToggleSearch.hide();

				self.addVar('visitorsCheckAll', checkToggle);

				btnExportCsv = self.buildExportToCSVData(visitorsDataSource, NewsletterPro.dataStorage.get('csv_export_list_ref.LIST_VISITORS'));

				return makeRow([checkToggle, checkToggleSearch, btnExportCsv]);
			}, 'prepend');

			var headerActions = visitorsGrid.addHeader(function(columns){
				var tr, 
					td, 
					searchDiv,
					timer = null, 
					searchText;

				function makeRow(arr) {
					tr = $('<tr></tr>');
					td = $('<th class="gk-header-datagrid visitors-header" colspan="'+columns+'"></th>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);

					return tr;
				}

				searchDiv = $('<div class="visitors-search-div" style="float: left; margin-right: 10px;"></div>');
				search = $('<input class="gk-input visitors-search" type="text">');
				searchLoading = $('<span class="visitors-search-loading" style="display: none;"></span>');

				search.on('keyup', function(event){
					var val = $.trim(search.val());

					if (val.length < 3) {
						checkToggle.show();
						checkToggleSearch.hide();

						visitorsDataSource.clearSearch();
						return true;
					} else {
						checkToggle.hide();
						checkToggleSearch.css({'display': 'inline-block'});
					}

					searchLoading.show();

					if ( timer != null ) clearTimeout(timer);

					timer = setTimeout(function(){

						visitorsDataSource.search(val).done(function(response){
							visitorsDataSource.applySearch(response);
							searchLoading.hide();
						});

					}, 300);

				});
				searchText = $('<span style="float: left; margin-right: 10px;">'+l('search')+':</span>');

				searchDiv.append(search);
				searchDiv.append(searchLoading);

				var filterShops = $('#gk-filter-shops').gkDropDownMenu({
					title: l('shops'),
					name: 'gk-filter-shops',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},
					data: NewsletterPro.dataStorage.data.filter_shops,
					change: function(values) {
						appyFilters('shops');
					},
				});

				var filterSubscribed = $('#gk-filter-subscribed-visitors').gkDropDownMenu({
					title: l('subscribed'),
					name: 'gk-filter-gender',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: [
						{'title': l('yes'), 'value': 1},
						{'title': l('no'), 'value': 0},
					],

					change: function(values) {
						appyFilters('subscribed');
					},
				});

				if (NewsletterPro.dataStorage.get('view_active_only'))
					filterSubscribed.hide();

				function getRangeSelection()
				{
					return {
						'min': (typeof sliderRange !== 'undefined' ? sliderRange.getValueMin() : 0) ,
						'max': (typeof sliderRange !== 'undefined' ? sliderRange.getValueMax() : 0) ,
					};
				}

				var winRangeSelection = new gkWindow({
						width: 640,
						height: 150,
						title: l('range selection'),
						className: 'range-selection-window',
						show: function(win) {
							if (typeof sliderRange !== 'undefined')
								sliderRange.refresh();
						},
						close: function(win) {},
						content: function(win, parent) 
						{
							visitorsDataSource.ready().done(function(){
								self.getRangeSelectionContent(function(content)
								{
									win.setContent(content);

									sliderRange = gkSliderRange({
										target: content.find('#slider-range-selection'),
										min : 1,
										max : visitorsCount(),
										valueMin : 1,
										valueMax : visitorsCount(),
										editable: true,
										values : [1, visitorsCount()],

										move: function(obj) {},
										done: function(obj) 
										{
											appyFilters('range');
										},
									});
								});
							});
							return '<span class="ajax-loader" style="margin-left: 310px; margin-top: 36px;"></span>';
						}
					});

				var rangeSelection = $('#range-selection-visitors')
				.gkButton({
					name: 'range-selection-visitors',
					title: l('range selection'),
					className: 'range-selection',
					css: {
						'padding-left': '10px',
						'padding-right': '10px',
						'float': 'left',
						'margin-right': '10px',
						'position': 'relative',
					},
					attr: {
						'data-checked': 0,
					},
					click: function(event) 
					{
						winRangeSelection.show();
					},
				});

				function appyFilters(trigger) 
				{
					var filters = {
						shops: filterShops.getSelected(),
						subscribed: filterSubscribed.getSelected(),
					};

					var breakFilters = false;
					$.each(filters, function(i, filter){
						if (filter.length) {
							breakFilters = true;
						}
					});

					if (trigger == 'range')
					{
						filters['range_selection'] = getRangeSelection();

						if ($.trim(filters.range_selection.min) == 0 || $.trim(filters.range_selection.max) == 0 )
							delete filters['range_selection'];
						else
							breakFilters = true;
					}

					if (breakFilters) {
						search.val('');
						checkToggle.hide();
						checkToggleSearch.css({'display': 'inline-block'});

						visitorsDataSource.filter(filters).done(function(response){
							visitorsDataSource.applySearch(response);
							resetSliderRange(trigger, 1, response.length);
						});

					} else {
						resetSliderRange(trigger, 1, visitorsCount());

						checkToggle.show();
						checkToggleSearch.hide();
						visitorsDataSource.clearSearch();
					}
				}

				var filtersText = $('<span style="margin-left: 20px; margin-right: 10px; float: left; line-height: 28px;">'+l('filters')+'</span>');
				var clearFilters = $('#clear-filters-visitors')
						.gkButton({
							name: 'clear-filters-visitors',
							title: l('clear filters'),
							className: 'clear-filters',
							css: {
								'padding-left': '10px',
								'padding-right': '10px',
								'margin-left': '0',
								'margin-right': '0',
								'position': 'absolute',
								'right': '5px',
							},
							attr: {
								'data-checked': 0,
							},

							click: function(event) {

								search.val('');
								filterShops.uncheckAll();
								filterSubscribed.uncheckAll();

								clearRangeSelection();

								checkToggle.show();
								checkToggleSearch.hide();
								visitorsDataSource.clearSearch();

							},
							icon: '<i class="icon icon-times"></i> ',
						});

				self.reset = self.reset || {};
				self.reset.visitor = {
					filterShops: filterShops,
					filterSubscribed: filterSubscribed,
					rangeSelection: rangeSelection,
					clearFilters: clearFilters,
				};

				self.addVar('filterVisitor', {
					'shops': filterShops,
					'subscribed': filterSubscribed,
					'range': rangeSelection,
					'clear': clearFilters,
				});

				self.addVar('applyFilerVisitorCallback', appyFilters);

				self.addVar('visitors', visitorsDataSource);

				return makeRow([searchText, searchDiv, filtersText, filterShops, filterSubscribed, rangeSelection, clearFilters]);
			}, 'prepend');

			self.resetVisitorsButton();
		});

	},

	initVisitorsGridNewsletterPro: function()
	{
		var self = this,
			box = this.box;

		self.ready(function(dom){

			// if this feature is not active stop the cod from acecution
			if (!self.isNewsletterProSubscriptionActive())
				return;

			var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.sendNewsletters),
				visitorsNPDataModel,
				visitorsNPDataSource,
				visitorsNPGrid = dom.visitorsNPGrid,
				clearFilters;

			visitorsNPDataModel = new gk.data.Model({
				id: 'id_newsletter_pro_subscribers',
			});

			visitorsNPDataSource = new gk.data.DataSource({
				pageSize: 5,
				transport: {
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getVisitorsNP',
						dataType: 'json',
					},
					update: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=updateVisitorNP&id',
						dataType: 'json',
					},

					destroy: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=deleteVisitorNP&id',
						type: 'POST',
						dateType: 'json',
						success: function(response, itemData) {
							if(!response)
								alert(l('delete visitor'));
						},
						error: function(data, itemData) {
							alert(l('delete visitor'));
						},
						complete: function(data, itemData) {},
					},

					search: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=searchVisitorNP&value',
						dataType: 'json',
					},

					filter: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=filterVisitorNP',
						dataType: 'json',
					},

				},
				schema: {
					model: visitorsNPDataModel
				},
				trySteps: 2,
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						visitorsNPDataSource.syncStepAvailableAdd(3000, function(){
							visitorsNPDataSource.sync();
						});
					},
				}
			});

			var visitorsNpTemplate = {
				img_path: function(item, value) 
				{
					var div = $('<div></div>');
					var lang_img = '<img src="'+value+'">';
					var gender_img = self.getGenderImageById(item.data.id_gender);

					div.append(lang_img);
					div.append(gender_img);
					div.width('38');
					return div;
				},

				active: function(item, value) 
				{
					var a = $('<a href="javascript:{}"></a>'),
						data = item.data;

					function isSubscribed() 
					{
						return parseInt(item.data.active) ? true : false;
					}

					function viewOnlySubscribed() 
					{
						return NewsletterPro.dataStorage.getNumber('configuration.VIEW_ACTIVE_ONLY') ? true : false;
					}

					function switchSubscription() 
					{
						if (isSubscribed()) 
							a.html('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
						else 
							a.html('<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>');
					}

					switchSubscription();

					a.on('click', function(e){
						e.stopPropagation();
						data.active = (isSubscribed() ? 0 : 1);

						item.update().done(function(response) {
							if (!response) 
							{
								alert('error on subscribe or unsubscribe');
							} 
							else 
							{
								if (viewOnlySubscribed()) 
									item.removeFromScreen();
								else 
									switchSubscription();
							}
						});
					});

					return a;
				},

				chackbox: function(item, value) 
				{
					var checkBox = $('<input type="checkbox" value="'+value+'" '+(item.isChecked() ? 'checked="checked"' : '')+'> ');			
					return checkBox;
				},

				actions: function(item) 
				{
					var deleteVisitorNP = $('#delete-visitor-np')
						.gkButton({
							name: 'delete',
							title: l('delete'),
							className: 'added-delete',
							item: item,
							command: 'delete',
							confirm: function() {
								return confirm(l('delete added confirm'));
							},
							icon: '<i class="icon icon-trash-o"></i> ',
						});
					return deleteVisitorNP;
				},
			};

			var newCustomColumns = box.dataStorage.get('configuration.SHOW_CUSTOM_COLUMNS');
			var allVariables = box.dataStorage.get('custom_field.variables_types');
			var typesConst = box.dataStorage.get('custom_field.types_cost');
			var displayLength = 25;

			var winDisplayLimit = new gkWindow({
				width: 600,
				height: 400,
				setScrollContent: 340,
				title: l('Details'),
				className: 'np-costum-fields-win',
				show: function(win) {},
				close: function(win) {},
				content: function(win) {

				}
			});

			var newVariablesTypes = [];

			if (allVariables.length > 0)
			{
				for (var i = 0; i < allVariables.length; i++)
				{
					var variable = allVariables[i];
					if (newCustomColumns.indexOf(variable.variable_name) != -1)
						newVariablesTypes.push(variable);
				}

				for (var i = 0; i < newVariablesTypes.length; i++)
				{
					var item = newVariablesTypes[i];
					// has multiple values
					if (Number(item.type) == Number(typesConst.TYPE_CHECKBOX))
					{
						visitorsNpTemplate[item.variable_name] = function(item, value)
						{
							var showDetails = $('<a href="javascript:{}">...</a>');
							var displayStr = value;
							var displayValue = value;

							try
							{
								var array = jQuery.parseJSON(value);
								displayStr = array.join(', ');
							}
							catch(e)
							{

							}

							if (displayStr.length <= displayLength)
							{
								displayStr = displayStr;
								displayValue = displayStr;
							}
							else
							{
								displayValue = displayStr;
								tmpDisplayStr = displayStr.slice(0, displayLength);

								displayStr = $('<span>');
								displayStr.append(tmpDisplayStr);
								displayStr.append(showDetails);
							}

							showDetails.on('click', function(e){
								e.stopPropagation();
								winDisplayLimit.show(displayValue);
							});

							return displayStr;
						};
					}
					else
					{
						visitorsNpTemplate[item.variable_name] = function(item, value) 
						{
							var showDetails = $('<a href="javascript:{}">...</a>');
							var displayStr = value;

							if (displayStr.length <= displayLength)
								displayStr = displayStr;
							else
							{
								tmpDisplayStr = displayStr.slice(0, displayLength);

								displayStr = $('<span>');
								displayStr.append(tmpDisplayStr);
								displayStr.append(showDetails);
							}

							showDetails.on('click', function(e){
								e.stopPropagation();
								winDisplayLimit.show(value);
							});

							return displayStr;
						}
					}
				}
			}

			visitorsNPGrid.gkGrid({
				dataSource: visitorsNPDataSource,
				selectable: false,
				checkable: true,
				currentPage: 1,
				pageable: true,
				start: function()
				{
					dom.visitorsNPAjaxLoader.show();
				},
				done: function(dataSource) 
				{
					dom.visitorsNPCount.html(dataSource.items.length);
					dom.visitorsNPAjaxLoader.hide();
				},
				template: visitorsNpTemplate
			});

			function setFilterBirthdayFrom(val)
			{
				birthdayDate.from = val;
			}

			function setFilterBirthdayTo(val)
			{
				birthdayDate.to = val;
			}

			function clearByBirthdayFilter()
			{
				if (typeof birthdayFrom !== 'undefined')
				{
					birthdayFrom.val('');
					setFilterBirthdayFrom('');
				}

				if (typeof birthdayTo !== 'undefined')
				{
					birthdayTo.val('');
					setFilterBirthdayTo('');
				}
			}

			var birthdayDate = {
				'from': '',
				'to': '',
			};

			function clearRangeSelection()
			{
				resetSliderRange('clear', 1, visitorsNPCount());
			}

			function visitorsNPCount()
			{
				return visitorsNPDataSource.items.length;
			}

			function resetSliderRange(trigger, min, max)
			{
				if (trigger !== 'range' && typeof sliderRange !== 'undefined')
				{
					var reset = {
						min : min,
						max : max,
						valueMin : min,
						valueMax : max,
						values : [min, max],
					};
					if (max <= 0)
					{
						reset['snap'] = 0;
						reset['min'] = 0;
						reset['max'] = 1;
					}
					sliderRange.reset(reset);
					sliderRange.resetPositionMin();
					sliderRange.resetPositionMax();
				}
			}

			var checkToggle,
			checkToggleSearch,
			searchLoading,
			search,
			conditions = box.dataStorage.get('search_conditions.conditions'),
			conditionsConst = box.dataStorage.get('search_conditions.conditions_const'),
			allColumns = box.dataStorage.get('search_conditions.visitors_np_columns'),
			defaultConditionType = Number(conditionsConst.SEARCH_CONDITION_CONTAINS),
			defaultFieldValue = 0,
			selectFilterCondition,
			selectFilterField;

			var sliderRange;
			var birthdayFrom;
			var birthdayTo;
			var fbbClear;

			var footerActions = visitorsNPGrid.addFooter(function(columns){
				var tr, td;
				function makeRow(arr) 
				{
					tr = $('<tr></tr>');
					td = $('<td class="gk-footer" colspan="'+columns+'"></td>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);
					return tr;
				}

				var getDisplayCustomColumnsContent = function()
				{
					var fields = box.dataStorage.get('custom_field.variables'),
						selectedFields = box.dataStorage.get('configuration.SHOW_CUSTOM_COLUMNS'),
						renderColumns = '';

					for (var i = 0; i < fields.length; i++) 
					{
						var field = fields[i],
							name = '',
							split, 
							checked = selectedFields.indexOf(field) != -1 ? true : false;

						split = field.split('_');

						for (var j = 1; j < split.length; j++)
						{
							name += split[j][0].toUpperCase() + split[j].slice(1);
						}

						renderColumns += '\
							<div class="checkbox">\
								<label class="control-label in-win">\
									<input type="checkbox" name="np_show_custom_colums_'+i+'" value="'+field+'" '+(checked ? 'checked="checked"' : '')+'>\
									'+name+'\
								</label>\
							</div>';
					}

					var template = $('\
						<div class="form-group clearfix">\
							<label class="control-label col-sm-3" style="padding-top: 13px;">'+l('Show Columns')+'</label>\
							<div class="col-sm-9">\
								'+renderColumns+'\
							</div>\
						</div>\
						<div class="form-group clearfix">\
							<div class="col-sm-9 col-sm-offset-3">\
								<a href="javascript:{}" id="np-save-show-columns" class="btn btn-success pull-left"><i class="icon icon-save"></i> '+l('Save')+'</a>\
							</div>\
						</div>\
					');

					var btnShowColumns = template.find('#np-save-show-columns');

					btnShowColumns.on('click', function(){
						var columns = [];

						$.each(template.find('[name^="np_show_custom_colums"]:checked'), function(i, item){
							columns.push($(item).val());
						});

						$.postAjax({'submit_custom_field': 'saveShowColumns', columns: columns}).done(function(response){
							if (!response.success)
								box.alertErrors(response.errors);
							else
								location.reload();
						});
					});

					return template;
				};

				var winDisplayCustomColumns = new gkWindow({
					width: 600,
					height: 400,
					setScrollContent: 340,
					title: l('Display Custom Columns'),
					className: 'np-costum-fields-win',
					show: function(win) 
					{
						$.postAjax({'submit_custom_field': 'getCustomColumns'}).done(function(response){
							if (response.success)
							{
								box.dataStorage.set('custom_field.variables', response.variables);
								win.setContent(getDisplayCustomColumnsContent());
							}
						});
					},
					close: function(win) {},
					content: function(win) 
					{
						return getDisplayCustomColumnsContent();
					}
				});

				self.addVar('winDisplayCustomColumns', winDisplayCustomColumns);
				
				var displayCustomColumns = $('<a href="javascript:{}" class="btn btn-default pull-right"><i class="icon icon-eye"></i> '+l('Display Custom Columns')+'</a>');

				displayCustomColumns.on('click', function(){
					winDisplayCustomColumns.show();
				});

				function createCheckToggle(name) 
				{
					var button = $('#'+name)
						.gkButton({
							name: name,
							title: l('check all'),
							className: name,
							css: {
								'padding-left': '10px',
								'padding-right': '10px',
								'margin-left': '0'
							},
							attr: {
								'data-checked': 0,
							},

							click: function(event) 
							{
								function isChecked() 
								{
									return button.data('checked') ? true : false;
								};

								function toggleName(trueStr, falseStr) {
									if (isChecked()) {
										button.data('checked', false);
										button.changeTitle(falseStr);
										return false;
									} else {
										button.data('checked', true);
										button.changeTitle(trueStr);
										return true;
									}
								}

								if (toggleName(l('uncheck all'), l('check all')))
									visitorsNPDataSource.checkAll();
								else
									visitorsNPDataSource.uncheckAll();
							}
						});

					return button;
				}

				checkToggle = createCheckToggle('check-toggle');
				checkToggleSearch = createCheckToggle('check-toggle-search');
				checkToggleSearch.addClass('gk-onfilter');
				checkToggleSearch.hide();

				self.addVar('visitorsNPCheckAll', checkToggle);

				btnExportCsv = self.buildExportToCSVData(visitorsNPDataSource, NewsletterPro.dataStorage.get('csv_export_list_ref.LIST_VISITORS_NP'));

				btnExportCsv.css({
					'margin-right': '3px',
				});

				return makeRow([checkToggle, checkToggleSearch, displayCustomColumns, btnExportCsv]);
			}, 'prepend');

			var filterLanguages,
				filterShops,
				filterGender,
				filterSubscribed,
				filterByInterest,
				filterByBirthday,
				rangeSelection;

			var headerActions1 = visitorsNPGrid.addHeader(function(columns){
				var tr, 
					td, 
					searchDiv,
					timer = null, 
					searchText;

				function makeRow(arr) 
				{
					tr = $('<tr></tr>');
					td = $('<th class="gk-header-datagrid visitors-np-header" colspan="'+columns+'"></th>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);

					return tr;
				}

				filterLanguages = $('#gk-filter-languages').gkDropDownMenu({
					title: l('languages'),
					name: 'gk-filter-languages',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},
					data: NewsletterPro.dataStorage.data.filter_languages,
					change: function(values) {
						appyFilters();
					},
				});

				filterShops = $('#gk-filter-shops').gkDropDownMenu({
					title: l('shops'),
					name: 'gk-filter-shops',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},
					data: NewsletterPro.dataStorage.data.filter_shops,
					change: function(values) {
						appyFilters();
					},
				});

				filterGender = $('#gk-filter-gender-np').gkDropDownMenu({
					title: l('gender'),
					name: 'gk-filter-gender',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: NewsletterPro.dataStorage.get('filter_genders'),

					change: function(values) {
						appyFilters();
					},
				});


				filterSubscribed = $('#gk-filter-subscribed-np').gkDropDownMenu({
					title: l('subscribed'),
					name: 'gk-filter-gender',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: [
						{'title': l('yes'), 'value': 1},
						{'title': l('no'), 'value': 0},
					],

					change: function(values) {
						appyFilters('subscribed');
					},
				});
				
				if (NewsletterPro.dataStorage.get('view_active_only'))
					filterSubscribed.hide();

				filterByInterest = $('#gk-filter-by-interest-np').gkDropDownMenu({
					title: l('by list of interest'),
					name: 'gk-filter-gender',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: NewsletterPro.dataStorage.get('filter_list_of_interest'),
					activeClass: {
						enable: '',
						disable: 'btn-filter-list-of-interst-inactive',
					},
					change: function(values) 
					{
						appyFilters();
					},
				});

				function getFilterByBirthdayContent(func)
				{
					$.postAjax({'submit': 'getFilterByBirthdayContent', fbb_class: 'visitorsNP'}, 'html').done(function(content){
						func($(content));
					});
				}

				function getFilterByBirthday()
				{
					return birthdayDate;
				}

				function getMySqlDate(dateObj)
				{
					var year = dateObj.selectedYear,
						mounth = (String(dateObj.selectedMonth).length == 1 ? '0'+String(dateObj.selectedMonth+1) : String(dateObj.selectedMonth+1)),
						day = (String(dateObj.selectedDay).length == 1 ? '0'+String(dateObj.selectedDay) : String(dateObj.selectedDay));
					return mysql_date = year+'-'+mounth+'-'+day;
				}

				var winByBirthday = new gkWindow({
						width: 640,
						height: 320,
						title: l('filter by birthday'),
						className: 'filter-by-birthday-window',
						show: function(win) {},
						close: function(win) {},
						content: function(win, parent) 
						{
							getFilterByBirthdayContent(function(content)
							{
								win.setContent(content);

								birthdayFrom = content.find('#fbb-from-visitorsNP');
								birthdayTo = content.find('#fbb-to-visitorsNP');
								fbbClear = content.find('#fbb-clear-visitorsNP');

								birthdayFrom.datepicker({ 
									dateFormat: self.box.dataStorage.get('jquery_date_birthday'),
									onSelect: function(date, dateObj)
									{
										setFilterBirthdayFrom(getMySqlDate(dateObj));
										appyFilters('birthday');
									},
									beforeShow: function(input, inst)
									{
										if (!inst.dpDiv.hasClass('date-birthday'))
											inst.dpDiv.addClass('date-birthday');
									}
								});

								birthdayTo.datepicker({
									dateFormat: self.box.dataStorage.get('jquery_date_birthday'),
									onSelect: function(date, dateObj)  
									{
										setFilterBirthdayTo(getMySqlDate(dateObj));
										appyFilters('birthday');
									},
									beforeShow: function(input, inst)
									{
										if (!inst.dpDiv.hasClass('date-birthday'))
											inst.dpDiv.addClass('date-birthday');
									}
								});

								birthdayFrom.on('change', function()
								{									
									if ($.trim($(this).val()) == '')
									{
										setFilterBirthdayFrom('');
										appyFilters('birthday');
									}
								});

								birthdayTo.on('change', function()
								{

									if ($.trim($(this).val()) == '')
									{
										setFilterBirthdayTo('');
										appyFilters('birthday');
									}
								});

								fbbClear.on('click', function()
								{

									clearByBirthdayFilter();
									appyFilters('birthday');
								});

							});
							return '<span class="ajax-loader" style="margin-left: 310px; margin-top: 119px;"></span>';
						}
					});

				filterByBirthday = $('#by-birthday-filters')
					.gkButton({
						name: 'by-birthday-filters',
						title: l('by birthday'),
						className: 'by-birthday-filters',
						css: {
							'padding-left': '10px',
							'padding-right': '10px',
							'float': 'left',
							'margin-right': '10px',
							'position': 'relative',
						},
						attr: {
							'data-checked': 0,
						},
						click: function(event) {
							winByBirthday.show();
						},
					});

				function getRangeSelection()
				{
					return {
						'min': (typeof sliderRange !== 'undefined' ? sliderRange.getValueMin() : 0) ,
						'max': (typeof sliderRange !== 'undefined' ? sliderRange.getValueMax() : 0) ,
					};
				}

				var winRangeSelection = new gkWindow({
					width: 640,
					height: 150,
					title: l('range selection'),
					className: 'range-selection-window',
					show: function(win) {
						if (typeof sliderRange !== 'undefined')
							sliderRange.refresh();
					},
					close: function(win) {},
					content: function(win, parent) 
					{
						visitorsNPDataSource.ready().done(function(){
							self.getRangeSelectionContent(function(content)
							{
								win.setContent(content);

								sliderRange = gkSliderRange({
									target: content.find('#slider-range-selection'),
									min : 1,
									max : visitorsNPCount(),
									valueMin : 1,
									valueMax : visitorsNPCount(),
									editable: true,
									values : [1, visitorsNPCount()],

									move: function(obj) {},
									done: function(obj) 
									{
										appyFilters('range');
									},
								});
							});
						});
						return '<span class="ajax-loader" style="margin-left: 310px; margin-top: 36px;"></span>';
					}
				});

				rangeSelection = $('#range-selection-added')
				.gkButton({
					name: 'range-selection-added',
					title: l('range selection'),
					className: 'range-selection',
					css: {
						'padding-left': '10px',
						'padding-right': '10px',
						'float': 'left',
						'margin-right': '10px',
						'position': 'relative',
					},
					attr: {
						'data-checked': 0,
					},
					click: function(event) 
					{
						winRangeSelection.show();
					},
				});

				function appyFilters(trigger) 
				{
					var filters = {
						shops: filterShops.getSelected(),
						languages: filterLanguages.getSelected(),
						gender: filterGender.getSelected(),
						subscribed: filterSubscribed.getSelected(),
						by_interest: filterByInterest.getSelected(),
						by_birthday: getFilterByBirthday(),
					};

					var breakFilters = false;
					$.each(filters, function(i, filter){
						if (filter.length) {
							breakFilters = true;
						}
					});

					if ($.trim(filters.by_birthday.from) == '' || $.trim(filters.by_birthday.to) == '' )
						delete filters['by_birthday'];
					else
						breakFilters = true;

					if (trigger == 'range')
					{
						filters['range_selection'] = getRangeSelection();

						if ($.trim(filters.range_selection.min) == 0 || $.trim(filters.range_selection.max) == 0 )
							delete filters['range_selection'];
						else
							breakFilters = true;
					}

					if (breakFilters) {
						search.val('');

						if (typeof selectFilterCondition !== 'undefined')
						{
							selectFilterCondition.val(defaultConditionType);
							selectFilterField.val(defaultFieldValue);
							
							box.dataStorage.set('search_conditions.selected_condition', defaultConditionType);
							box.dataStorage.set('search_conditions.selected_field', defaultFieldValue);
						}

						checkToggle.hide();
						checkToggleSearch.css({'display': 'inline-block'});

						visitorsNPDataSource.filter(filters).done(function(response){
							visitorsNPDataSource.applySearch(response);
							resetSliderRange(trigger, 1, response.length);

						});

					} else {
						resetSliderRange(trigger, 1, visitorsNPCount());

						checkToggle.show();
						checkToggleSearch.hide();
						visitorsNPDataSource.clearSearch();
					}
				}

				var filtersText = $('<span style="margin-left: 0px; margin-right: 10px; float: left; line-height: 28px;">'+l('filters')+'</span>');

				self.reset = self.reset || {};

				self.reset.visitorsNP = self.reset.visitorsNP || {};
				self.reset.visitorsNP['filterLanguages'] = filterLanguages;
				self.reset.visitorsNP['filterShops'] = filterShops;
				self.reset.visitorsNP['filterGender'] = filterGender;
				self.reset.visitorsNP['filterSubscribed'] = filterSubscribed;
				self.reset.visitorsNP['filterByInterest'] = filterByInterest;
				self.reset.visitorsNP['filterByBirthday'] = filterByBirthday;
				self.reset.visitorsNP['rangeSelection'] = rangeSelection;

				self.addVar('filterVisitorNP', {
					'languages': filterLanguages,
					'shops': filterShops,
					'gender': filterGender,
					'subscribed': filterSubscribed,
					'filter_by_interest': filterByInterest,
					'by_birthday': filterByBirthday,
					'range': rangeSelection,
				});

				self.addVar('applyFilerVisitorNpCallback', appyFilters);

				return makeRow([filtersText , filterLanguages ,filterShops, filterGender, filterSubscribed, filterByInterest, filterByBirthday, rangeSelection]);
			}, 'prepend');

			var headerActions = visitorsNPGrid.addHeader(function(columns){
				var tr, 
					td, 
					searchDiv,
					timer = null, 
					searchText,
					searchFilterDiv,
					searchFilter,
					searchFilterText;

				box.dataStorage.set('search_conditions.selected_condition', defaultConditionType);
				box.dataStorage.set('search_conditions.selected_field', defaultFieldValue);

				function makeRow(arr) 
				{
					tr = $('<tr></tr>');
					td = $('<th class="gk-header-datagrid visitors-np-header" colspan="'+columns+'"></th>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);

					return tr;
				}

				searchDiv = $('<div class="visitors-np-search-div"></div>');
				search = $('<input class="gk-input visitors-np-search" type="text">');
				searchLoading = $('<span class="visitors-np-search-loading" style="display: none;"></span>');

				search.on('keyup', function(event){
					var val = $.trim(search.val());

					// accept one value for integers
					if (val.length <= 0) {
						checkToggle.show();
						checkToggleSearch.hide();

						visitorsNPDataSource.clearSearch();
						return true;
					} else {
						checkToggle.hide();
						checkToggleSearch.css({'display': 'inline-block'});
					}

					searchLoading.show();

					if ( timer != null ) clearTimeout(timer);

					timer = setTimeout(function(){

						var conditions = {};

						if (typeof selectFilterCondition !== 'undefined')
						{
							conditions = {
								'selected_condition': Number(selectFilterCondition.val()),
								'selected_field': selectFilterField.val()
							};
						}

						visitorsNPDataSource.search(val, {'conditions': conditions}).done(function(response){
							visitorsNPDataSource.applySearch(response);
							searchLoading.hide();
						});

					}, 300);

				});
				searchText = $('<span>'+l('search')+':</span>');

				searchDiv.append(search);
				searchDiv.append(searchLoading);

				clearFilters = $('#clear-filters')
						.gkButton({
							name: 'clear-filters',
							title: l('clear filters'),
							className: 'clear-filters',
							css: {
								'padding-left': '10px',
								'padding-right': '10px',
								'margin-left': '0',
								'margin-right': '0',
								'position': 'absolute',
								'right': '5px',
							},
							attr: {
								'data-checked': 0,
							},

							click: function(event) {

								search.val('');

								if (typeof selectFilterCondition !== 'undefined')
								{
									selectFilterCondition.val(defaultConditionType);
									selectFilterField.val(defaultFieldValue);
									
									box.dataStorage.set('search_conditions.selected_condition', defaultConditionType);
									box.dataStorage.set('search_conditions.selected_field', defaultFieldValue);
								}

								filterShops.uncheckAll();
								filterGender.uncheckAll();
								filterSubscribed.uncheckAll();

								filterByInterest.uncheckAll();
								filterLanguages.uncheckAll();

								clearRangeSelection();
								clearByBirthdayFilter();

								checkToggle.show();
								checkToggleSearch.hide();
								visitorsNPDataSource.clearSearch();
							},
							icon: '<i class="icon icon-times"></i> ',
						});

				var searchConditionOptions = '';

				for (var key in conditions) {
					searchConditionOptions += '<option value="'+key+'" '+(Number(key) == defaultConditionType ? 'selected="selected"' : '')+'>'+conditions[key]+'</option>';
				}

				var searchConditionColumns = '';

				for (var i = 0; i < allColumns.length; i++) 
				{
					var column = allColumns[i],
						columnSplit = column.replace(/^np_/, '').split('_'),
						columnName = '';

					for (var j = 0; j < columnSplit.length; j++) {
						columnSplit[j] = columnSplit[j][0].toUpperCase() + columnSplit[j].slice(1);
					}

					columnName = columnSplit.join(' ');

					searchConditionColumns += '<option value="'+column+'">'+columnName+'</option>';
				}

				searchFilterDiv = $('\
					<div class="np-visitors-np-search-filter-condition-div">\
						<select id="np-visitors-np-select-search-filter-condition-div" class="form-control fixed-width-l">\
							'+searchConditionOptions+'\
						</select>\
					</div>\
					<div class="np-visitors-np-search-filter-columns-div">\
						<select id="np-visitors-np-select-search-filter-columns-div" class="form-control fixed-width-l">\
							<option value="'+defaultFieldValue+'" selected="selected">'+l('all fields')+'</option>\
							'+searchConditionColumns+'\
						</select>\
					</div>\
				');

				selectFilterCondition = searchFilterDiv.find('#np-visitors-np-select-search-filter-condition-div');
				selectFilterField = searchFilterDiv.find('#np-visitors-np-select-search-filter-columns-div');

				selectFilterCondition.on('change', function(){
					box.dataStorage.set('search_conditions.selected_condition', Number($(this).val()));
					search.trigger('keyup');
				});

				selectFilterField.on('change', function(){
					box.dataStorage.set('search_conditions.selected_field', $(this).val());
					search.trigger('keyup');
				});

				self.reset = self.reset || {};
				self.reset.visitorsNP = self.reset.visitorsNP || {};
				self.reset.visitorsNP['clearFilters'] = clearFilters;

				self.vars.filterVisitorNP['clear'] = clearFilters;

				self.addVar('visitorsNP', visitorsNPDataSource);

				return makeRow([searchText, searchDiv, searchFilterDiv, clearFilters]);
			}, 'prepend');

			self.resetVisitorsNPButton();
		});
	},

	initAddedGrid: function() {
		var self = this;

		self.ready(function(dom) {
			var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.sendNewsletters),
				addedDataModel,
				addedDataSource,
				addedGrid = dom.addedGrid;

				addedDataModel = new gk.data.Model({
					id: 'id_newsletter_pro_email',
				});

				addedDataSource = new gk.data.DataSource({
					pageSize: 10,
					transport: {
						read: {
							url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getAdded',
							dataType: 'json',
						},

						create: {
							url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=createAdded',
							dataType: 'json',
						},

						update: {
							url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=updateAdded&id',
							dataType: 'json',
						},

						destroy: {
							url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=deleteAdded&id',
							type: 'POST',
							dateType: 'json',
							success: function(response, itemData) {
								if(!response)
									alert(l('delete record'));
							},
							error: function(data, itemData) {
								alert(l('delete record'));
							},
							complete: function(data, itemData) {},
						},

						search: {
							url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=searchAdded&value',
							dataType: 'json',
						},

						filter: {
							url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=filterAdded',
							dataType: 'json',
						},

					},
					schema: {
						model: addedDataModel
					},
					trySteps: 2,
					errors: 
					{
						read: function(xhr, ajaxOptions, thrownError) 
						{
							addedDataSource.syncStepAvailableAdd(3000, function(){
								addedDataSource.sync();
							});
						},
					},
				});

				addedGrid.gkGrid({
					dataSource: addedDataSource,
					selectable: false,
					checkable: true,
					currentPage: 1,
					pageable: true,
					start: function()
					{
						dom.addedAjaxLoader.show();
					},
					done: function(dataSource) 
					{
						dom.addedCount.html(dataSource.items.length);
						dom.addedAjaxLoader.hide();
					},
					template: {
						img_path: function(item, value) 
						{
							return '<img src="'+value+'">';
						},

						active: function(item, value) 
						{
							var a = $('<a href="javascript:{}"></a>'),
								data = item.data;

							function isSubscribed() {
								return parseInt(item.data.active) ? true : false;
							}

							function viewOnlySubscribed() {
								return NewsletterPro.dataStorage.getNumber('configuration.VIEW_ACTIVE_ONLY') ? true : false;
							}

							function switchSubscription() 
							{
								if (isSubscribed()) {
									a.html('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
								} else {
									a.html('<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>');
								}
							}

							switchSubscription();

							a.on('click', function(e){
								e.stopPropagation();
								data.active = (isSubscribed() ? 0 : 1);

								item.update().done(function(response) {
									if (!response) {
										alert('error on subscribe or unsubscribe');
									} else {
										if (viewOnlySubscribed()) {
											item.removeFromScreen();
										} else {
											switchSubscription();
										}
									}
								});
							});

							return a;
						},

						chackbox: function(item, value) 
						{
							var checkBox = $('<input type="checkbox" value="'+value+'" '+(item.isChecked() ? 'checked="checked"' : '')+'> ');			
							return checkBox;
						},

						actions: function(item) 
						{
							var deleteAdded = $('#delete-added')
								.gkButton({
									name: 'delete',
									title: l('delete'),
									className: 'added-delete',
									item: item,
									command: 'delete',
									confirm: function() {
										return confirm(l('delete added confirm'));
									},
									icon: '<i class="icon icon-trash-o"></i> ',
								});
							return deleteAdded;
						},
					}
				});

				function clearRangeSelection()
				{
					resetSliderRange('clear', 1, addedCount());
				}
				function addedCount()
				{
					return addedDataSource.items.length;
				}
				function resetSliderRange(trigger, min, max)
				{
					if (trigger !== 'range' && typeof sliderRange !== 'undefined')
					{
						var reset = {
							min : min,
							max : max,
							valueMin : min,
							valueMax : max,
							values : [min, max],
						};
						if (max <= 0)
						{
							reset['snap'] = 0;
							reset['min'] = 0;
							reset['max'] = 1;
						}
						sliderRange.reset(reset);
						sliderRange.resetPositionMin();
						sliderRange.resetPositionMax();
					}
				}

				var checkToggle,
				checkToggleSearch,
				searchLoading,
				search;

				var sliderRange;

				var footerActions = addedGrid.addFooter(function(columns){
					var tr, td, addButton, winAdd;
					function makeRow(arr) {
						tr = $('<tr></tr>');
						td = $('<td class="gk-footer" colspan="'+columns+'"></td>');

						$.each(arr, function(i, item){
							td.append(item);
						});

						tr.html(td);
						return tr;
					}

					function createCheckToggle(name) {
						var button = $('#'+name)
							.gkButton({
								name: name,
								title: l('check all'),
								className: name,
								css: {
									'padding-left': '10px',
									'padding-right': '10px',
									'margin-left': '0'
								},
								attr: {
									'data-checked': 0,
								},

								click: function(event) 
								{
									function isChecked() {
										return button.data('checked') ? true : false;
									};

									function toggleName(trueStr, falseStr) {
										if (isChecked()) {
											button.data('checked', false);
											button.changeTitle(falseStr);
											return false;
										} else {
											button.data('checked', true);
											button.changeTitle(trueStr);
											return true;
										}
									}

									if (toggleName(l('uncheck all'), l('check all'))) {
										addedDataSource.checkAll();
									} else {
										addedDataSource.uncheckAll();
									}
								}
							});
						return button;
					}

					checkToggle = createCheckToggle('check-toggle');
					checkToggleSearch = createCheckToggle('check-toggle-search');
					checkToggleSearch.addClass('gk-onfilter');
					checkToggleSearch.hide();

					self.addVar('addedCheckAll', checkToggle);

					addButton = $('#add-new-email')
						.gkButton({
							title: l('add'),
							name: 'add-new-email',
							className: 'add-new-email btn-margin',
							css: {
								'margin-right': '0',
							},
							click: function(event) {
								winAdd.show();
							},
							icon: '<i class="icon icon-plus-square"></i> ',
						});

					function resetWindow() {
						var inputs = dom.addNewEmailTemplate.find('input[name="firstname"], input[name="lastname"], input[name="email"]');

						if (inputs.length) {
							$.each(inputs, function(i, item){
								$(item).val('');
							});
						}
						dom.addNewEmailError.html('');
					}

					winAdd = new gkWindow({
						width: 400,
						title: l('add title'),
						className: 'add-new-email-window',
						show: function(win) {},
						close: function(win) {
							resetWindow();
						},
						content: function(win) {

							var addNewEmail = dom.addNewEmail,
								form = dom.addNewEmailForm,
								addNewEmailError = dom.addNewEmailError;

							addNewEmail.on('click', function(e) {	
								addedDataSource.create(form.getFormData()).done(function(response){
									if (!response.status) {
										if (response.errors.length) {
											addNewEmailError.html(response.errors[0]);
										}
									} else {

										// need to fix the selection lost when i add a new email to the list 
										addedDataSource.sync();
										addedDataSource.dataGrid.footer.setCheckedInfo(0);

										resetWindow();
										win.hide();
									}
								});
							});

							return dom.addNewEmailTemplate;
						}
					});

					var emptyList = $('#added-empty')
						.gkButton({
							title: l('empty list'),
							name: 'added-empty',
							className: 'added-empty btn-margin',
							css: {
								'padding-left': '10px',
								'padding-right': '10px',
							},
							click: function(event) {
								if (confirm(l('empty list confirm'))) {
									$.postAjax({submit: 'emptyAddedEmails'}).done(function(response){
										if (!response.status) {
											alert(l('empty list error'));
										} else {
											addedDataSource.sync();
										}
									});
								}
							},
							icon: '<i class="icon icon-trash-o"></i> ',
						});

					var floatRight = $('<div></div>');
					floatRight.css({
						'display': 'inline-block',
						'float': 'right',
					});
					floatRight.append(emptyList);
					floatRight.append(addButton);

					btnExportCsv = self.buildExportToCSVData(addedDataSource, NewsletterPro.dataStorage.get('csv_export_list_ref.LIST_ADDED'));

					btnExportCsv.css({
						'margin-right': '2px',
					});

					return makeRow([checkToggle, checkToggleSearch, floatRight, btnExportCsv]);
				}, 'prepend');

				var headerActions = addedGrid.addHeader(function(columns){
					var tr, 
						td, 
						searchDiv,
						timer = null, 
						searchText;

					function makeRow(arr) {
						tr = $('<tr></tr>');
						td = $('<th class="gk-header-datagrid added-header" colspan="'+columns+'"></th>');

						$.each(arr, function(i, item){
							td.append(item);
						});

						tr.html(td);

						return tr;
					}

					searchDiv = $('<div class="added-search-div" style="margin-right: 10px; float: left;"></div>');
					search = $('<input class="gk-input added-search" type="text">');
					searchLoading = $('<span class="added-search-loading" style="display: none;"></span>');

					search.on('keyup', function(event){
						var val = $.trim(search.val());

						if (val.length < 3) {
							checkToggle.show();
							checkToggleSearch.hide();

							addedDataSource.clearSearch();
							return true;
						} else {
							checkToggle.hide();
							checkToggleSearch.css({'display': 'inline-block'});
						}

						searchLoading.show();

						if ( timer != null ) clearTimeout(timer);

						timer = setTimeout(function(){

							addedDataSource.search(val).done(function(response){
								addedDataSource.applySearch(response);
								searchLoading.hide();
							});

						}, 300);

					});
					searchText = $('<span style="margin-right: 10px; float: left;">'+l('search')+':</span>');

					searchDiv.append(search);
					searchDiv.append(searchLoading);

					var filterLanguages = $('#gk-filter-languages').gkDropDownMenu({
						title: l('languages'),
						name: 'gk-filter-languages',
						css: {
							'float': 'left',
							'margin-right': '10px',
						},
						data: NewsletterPro.dataStorage.data.filter_languages,
						change: function(values) {
							appyFilters();
						},
					});

					var filterShops = $('#gk-filter-shops').gkDropDownMenu({
						title: l('shops'),
						name: 'gk-filter-shops',
						css: {
							'float': 'left',
							'margin-right': '10px',
						},
						data: NewsletterPro.dataStorage.data.filter_shops,
						change: function(values) {
							appyFilters();
						},
					});

					var filterCSVName = $('#gk-filter-csv-name').gkDropDownMenu({
						title: l('CSV Name'),
						name: 'gk-filter-csv-name',
						css: {
							'float': 'left',
							'margin-right': '10px',
						},
						data: NewsletterPro.dataStorage.get('csv_name'),
						change: function(values) {
							appyFilters();
						},
					});

					var filterSubscribed = $('#gk-filter-subscribed-add').gkDropDownMenu({
						title: l('subscribed'),
						name: 'gk-filter-gender',
						css: {
							'float': 'left',
							'margin-right': '10px',
						},

						data: [
							{'title': l('yes'), 'value': 1},
							{'title': l('no'), 'value': 0},
						],

						change: function(values) {
							appyFilters('subscribed');
						},
					});

					if (NewsletterPro.dataStorage.get('view_active_only'))
						filterSubscribed.hide();

					function getRangeSelection()
					{
						return {
							'min': (typeof sliderRange !== 'undefined' ? sliderRange.getValueMin() : 0) ,
							'max': (typeof sliderRange !== 'undefined' ? sliderRange.getValueMax() : 0) ,
						};
					}

					var winRangeSelection = new gkWindow({
						width: 640,
						height: 150,
						title: l('range selection'),
						className: 'range-selection-window',
						show: function(win) {
							if (typeof sliderRange !== 'undefined')
								sliderRange.refresh();
						},
						close: function(win) {},
						content: function(win, parent) 
						{
							addedDataSource.ready().done(function(){
								self.getRangeSelectionContent(function(content)
								{
									win.setContent(content);

									sliderRange = gkSliderRange({
										target: content.find('#slider-range-selection'),
										min : 1,
										max : addedCount(),
										valueMin : 1,
										valueMax : addedCount(),
										editable: true,
										values : [1, addedCount()],

										move: function(obj) {},
										done: function(obj) 
										{
											appyFilters('range');
										},
									});
								});
							});
							return '<span class="ajax-loader" style="margin-left: 310px; margin-top: 36px;"></span>';
						}
					});

					var rangeSelection = $('#range-selection-added')
					.gkButton({
						name: 'range-selection-added',
						title: l('range selection'),
						className: 'range-selection',
						css: {
							'padding-left': '10px',
							'padding-right': '10px',
							'float': 'left',
							'margin-right': '10px',
							'position': 'relative',
						},
						attr: {
							'data-checked': 0,
						},
						click: function(event) 
						{
							winRangeSelection.show();
						},
					});				

					function appyFilters(trigger) 
					{
						var filters = {
							shops: filterShops.getSelected(),
							subscribed: filterSubscribed.getSelected(),
							languages: filterLanguages.getSelected(),
							csv_name: filterCSVName.getSelected(),
						};

						var breakFilters = false;
						$.each(filters, function(i, filter){
							if (filter.length) {
								breakFilters = true;
							}
						});

						if (trigger == 'range')
						{
							filters['range_selection'] = getRangeSelection();

							if ($.trim(filters.range_selection.min) == 0 || $.trim(filters.range_selection.max) == 0 )
								delete filters['range_selection'];
							else
								breakFilters = true;
						}

						if (breakFilters) {
							search.val('');
							checkToggle.hide();
							checkToggleSearch.css({'display': 'inline-block'});

							addedDataSource.filter(filters).done(function(response){
								addedDataSource.applySearch(response);
								resetSliderRange(trigger, 1, response.length);

							});

						} else {
							resetSliderRange(trigger, 1, addedCount());

							checkToggle.show();
							checkToggleSearch.hide();
							addedDataSource.clearSearch();
						}
					}

					var filtersText = $('<span style="margin-left: 20px; margin-right: 10px; float: left; line-height: 28px;">'+l('filters')+'</span>');
					var clearFilters = $('#clear-filters')
							.gkButton({
								name: 'clear-filters',
								title: l('clear filters'),
								className: 'clear-filters',
								css: {
									'padding-left': '10px',
									'padding-right': '10px',
									'margin-left': '0',
									'margin-right': '0',
									'position': 'absolute',
									'right': '5px',
								},
								attr: {
									'data-checked': 0,
								},

								click: function(event) {

									search.val('');
									filterShops.uncheckAll();
									filterCSVName.uncheckAll();
									filterSubscribed.uncheckAll();
									filterLanguages.uncheckAll();

									clearRangeSelection();

									checkToggle.show();
									checkToggleSearch.hide();
									addedDataSource.clearSearch();
								},
								icon: '<i class="icon icon-times"></i> ',
							});

					self.reset = self.reset || {};
					self.reset.added = {
						filterLanguages: filterLanguages,
						filterShops: filterShops,
						filterCSVName: filterCSVName,
						filterSubscribed: filterSubscribed,
						rangeSelection: rangeSelection,
						clearFilters: clearFilters,
					}
	
					self.addVar('filterAdded', {
						'languages': filterLanguages,
						'shops': filterShops,
						'csv_name': filterCSVName,
						'subscribed': filterSubscribed,
						'range': rangeSelection,
						'clear': clearFilters,
					});

					self.addVar('applyFilerAddedCallback', appyFilters);

					self.addVar('added', addedDataSource);

					return makeRow([searchText, searchDiv, filtersText , filterLanguages ,filterShops, filterCSVName, filterSubscribed, rangeSelection, clearFilters]);
				}, 'prepend');

				self.resetAddedButton();
		});

	},

	getSelectOptions: function(opt) 
	{
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
	},
}.init(NewsletterPro));