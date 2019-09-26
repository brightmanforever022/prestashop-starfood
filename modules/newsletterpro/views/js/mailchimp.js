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

NewsletterPro.namespace('modules.mailChimp');
NewsletterPro.modules.mailChimp = ({
	dom: null,
	box: null,
	init: function(box) {
		var self = this,
			dataStorage,
			chimpConfig,
			chimpSyncProgress,
			syncRefreshRate = 5000,
			l,
			exportTemplateWinDom = {},
			exportTemplateWin;

		self.box = box;

		function installChimp(api_key, list_id)
		{
			var dom = self.dom;

			addLoading(dom.installLoading);
			return $.postAjax({
				'chimp': 'installChimp', 
				'api_key': api_key,
				'list_id': list_id,
			}).always(function(){
				removeLoading(dom.installLoading);
			}).done(function(response){
				if (response.status) {
					installedChimpSettings();
					alert(response.message);
				} else {
					box.alertErrors(response.errors);
				}
			});
		}

		function uninstallChimp()
		{
			if (confirm(l('confirm uninstall chimp')))
			{
				var dom = self.dom;

				addLoading(dom.uninstallLoading);
				return $.postAjax({'chimp': 'uninstallChimp'})
					.always(function(){
						removeLoading(dom.uninstallLoading);
					}).done(function(response){
						if(response.status) {
							dom.inputChimpApiKey.val(''),
							dom.inputChimpListId.val('');
							uninstalledChimpSettings();
							alert(response.message);
						} else {
							box.alertErrors(response.errors);
						}
					});
			}
			return false;
		}

		function addLoading(element)
		{
			if (!element.hasClass('ajax-loader'))
				element.addClass('ajax-loader');
		}

		function removeLoading(element)
		{
			element.removeClass('ajax-loader');
		}

		function pingChimp()
		{
			return $.postAjax({'chimp': 'pingChimp'}).done(function(response){
				if (response.status) {
					alert(response.message);
				} else {
					box.alertErrors(response.errors);
				}
			});
		}

		function showChimpMenu()
		{

		}

		function showInstallButton()
		{
			var dom = self.dom;
			dom.btnUninstallChimp.hide();
			dom.btnInstallChimp.show();
			dom.chimpMenu.slideUp();
		}

		function showUninstallButton()
		{
			var dom = self.dom;
			dom.btnUninstallChimp.show();
			dom.btnInstallChimp.hide();
			dom.chimpMenu.slideDown();
		}

		function isChecked(checkbox)
		{
			if (checkbox.is(':checked'))
				return 1;
			return 0;
		}

		function getConfig(name) 
		{
			if (chimpConfig.hasOwnProperty(name))
				return chimpConfig[name];
			return false;
		}

		function configExists(name) 
		{
			if (chimpConfig.hasOwnProperty(name))
				return true;
			return false;
		}

		function updateSyncCheckbox(name, value, doneCallback)
		{
			value = ( value ? 1 : 0 );
			$.postAjax({'chimp': 'updateSyncCheckbox', 'name': name, 'value': value }).done(function(response){
				if (typeof doneCallback === 'function')
					doneCallback(response);

				if (!response.status)
					box.alertErrors(response.errors);
				else
				{
					box.dataStorage.data.chimp_config[name] = value;
				}
			});
		}

		function writeProgress(instance, users)
		{
			instance.total.html(users.total);
			instance.created.html(users.created);
			instance.updated.html(users.updates);
			instance.errors.html(users.errors);

			if (!users.done)
			{
				if (!isVisible(instance.box))
					instance.box.slideDown();

				if (users.in_progress)
					instance.ajaxLoader.show();
			}
			else
			{
				hideProgress(instance);
			}
		}

		function resetProgress(instance)
		{
			if (isVisible(instance.box))
			{
				instance.box.slideUp('slow', function(){
					instance.total.html(0);
					instance.created.html(0);
					instance.updated.html(0);
					instance.errors.html(0);
					instance.ajaxLoader.hide();
				});
			}
			else
			{
				instance.total.html(0);
				instance.created.html(0);
				instance.updated.html(0);
				instance.errors.html(0);
				instance.ajaxLoader.hide();
			}
		}

		function isVisible(box)
		{
			if (box.is(':visible'))
				return true;
			return false;
		}

		function hideProgress(instance)
		{
			var dfd = new $.Deferred();
			if(isVisible(instance.box))
			{
				instance.ajaxLoader.hide();
				setTimeout(function(){
					dfd.resolve();
					resetProgress(instance);
				}, 15000);
			}
			return dfd.promise();
		}

		function resetAllProgress()
		{
			hideProgress(self.dom.objSyncCustomersProgress);
			hideProgress(self.dom.objSyncVisitorsProgress);
			hideProgress(self.dom.objSyncAddedProgress);
			hideProgress(self.dom.objSyncOrdersProgress);
		}

		function stopSync()
		{
			return $.postAjax({'chimp': 'stopSync'}).done(function(response) {
				if (response.status)
				{
					resetAllProgress();
					hideBox();
					showSyncListButton();
				}
				else
					box.alertErrors(response.errors);
			});
		}

		function checkStatus(response)
		{
			var chimpSync = response.chimp_sync,
				added,
				visitors,
				customers;

			if (chimpSync.hasOwnProperty('ADDED_CHECKBOX'))
			{
				users = chimpSync.ADDED_CHECKBOX;
				writeProgress(self.dom.objSyncAddedProgress, users);
			}
			else
				hideProgress(self.dom.objSyncAddedProgress);

			if (chimpSync.hasOwnProperty('VISITORS_CHECKBOX'))
			{
				users = chimpSync.VISITORS_CHECKBOX;
				writeProgress(self.dom.objSyncVisitorsProgress, users);
			}
			else
				hideProgress(self.dom.objSyncVisitorsProgress);

			if (chimpSync.hasOwnProperty('CUSTOMERS_CHECKBOX'))
			{
				users = chimpSync.CUSTOMERS_CHECKBOX;
				writeProgress(self.dom.objSyncCustomersProgress, users);
			}
			else
				hideProgress(self.dom.objSyncCustomersProgress);

			if (chimpSync.hasOwnProperty('ORDERS_CHECKBOX'))
			{
				users = chimpSync.ORDERS_CHECKBOX;
				writeProgress(self.dom.objSyncOrdersProgress, users);
				self.dom.lastSyncOrders.html(users.date_add);
			}
			else
				hideProgress(self.dom.objSyncOrdersProgress);


			if (chimpSync.hasOwnProperty('ERRORS') && chimpSync.ERRORS.length > 0)
			{
				self.dom.objErrorMessageBox.box.show();
				self.dom.objErrorMessageBox.span.html(chimpSync.ERRORS.join('<br>'));
				setTimeout(function(){
					self.dom.objErrorMessageBox.box.hide();
					self.dom.objErrorMessageBox.span.html('');
				}, 15000);
			}

			if (chimpSync.hasOwnProperty('ERRORS_MESSAGE') && chimpSync.ERRORS_MESSAGE.length > 0)
			{
				var errorsArray = chimpSync.ERRORS_MESSAGE,
					errors = [],
					errorsDisplay;

				for (var i = 0, length = errorsArray.length; i <= length; i++) {

					if (typeof errorsArray[i] === 'object' && errorsArray[i].hasOwnProperty('error')) {
						errors.push(errorsArray[i].error);
					}
				}

				if (errors.length > 0) {
					errorsDisplay = errors.splice(0, 10);

					var contnet = l('Display first #s errors:').replace('#s', 10) + '<br><br>';
						contnet += errorsDisplay.join('<br>');
					self.dom.syncChimpErrorsMessage.show().html(contnet);

					setTimeout(function(){
						self.dom.syncChimpErrorsMessage.hide().html('');
					}, 15000);
				}

			}

			if (getInProgress(chimpSync).length == 0)
			{
				stopSync();
				return true;
			}

			return false;
		}

		function getInProgress(chimpSync)
		{
			return $.map(chimpSync, function(item, index){
				if (item.hasOwnProperty('done') && item.done == false)
					return item.done;
			});
		}

		function showBox()
		{
			var dom = self.dom;
			if (!isVisible(dom.syncListsProgressBox))
				dom.syncListsProgressBox.show();
		}

		function hideBox()
		{
			var dom = self.dom;
			if (isVisible(dom.syncListsProgressBox))
			{
				setTimeout(function(){
					dom.syncListsProgressBox.slideUp();
				}, 15000);
			}
		}

		function showSyncListButton()
		{
			var dom = self.dom;
			dom.btnSyncLists.show();
			dom.btnDeleteChimpOrders.show();
			dom.btnStopSyncLists.hide();
		}

		function hideSyncListButton()
		{
			var dom = self.dom;
			dom.btnSyncLists.hide();
			dom.btnDeleteChimpOrders.hide();
			dom.btnStopSyncLists.show();
		}

		function getSyncListsStatus()
		{	
			var dom = self.dom;
			showBox();
			hideSyncListButton();

			$.postAjax({'chimp': 'getSyncListsStatus'}).done(function(response){
				checkStatus(response);
			});

			interval(function(response, that){
				if (checkStatus(response))
					clearInterval(that);
			}, 'getSyncListsStatus' , syncRefreshRate);
		}

		function interval(func, php_function, time) 
		{
			var interval = setInterval(function(){
				$.postAjax({'chimp': php_function}).done(function(response){
					func(response, interval);
				});
			}, time);
		}

		function setSyncLists(data)
		{
			var dom = self.dom;
			ajaxStart(dom.btnSyncLists);
			$.postAjax({'chimp': 'setSyncLists', 'data': data}).done(function(response){
				if(!response.status)
					box.alertErrors(response.errors);
				else
				{
					getSyncListsStatus();
					startSyncLists();
				}
			}).always(function(){
				ajaxDone(dom.btnSyncLists);
			});
		}

		function deleteChimpOrders()
		{
			var dom = self.dom;
			ajaxStart(dom.btnDeleteChimpOrders);
			dom.btnSyncLists.hide();
			$.postAjax({'chimp': 'deleteChimpOrders'}).done(function(response){
				alert(box.displayAlert(response.msg));
				dom.lastSyncOrders.html(response.date_add);
			}).always(function(){
				ajaxDone(dom.btnDeleteChimpOrders);
				dom.btnSyncLists.show();
			});
		}

		function startSyncLists()
		{
			$.postAjax({'chimp': 'startSyncLists'}).done(function(response){
				if (response.hasOwnProperty('ADDED_CHECKBOX') && !response.ADDED_CHECKBOX.done)
					return startSyncLists();

				if (response.hasOwnProperty('VISITORS_CHECKBOX') && !response.VISITORS_CHECKBOX.done)
					return startSyncLists();

				if (response.hasOwnProperty('CUSTOMERS_CHECKBOX') && !response.CUSTOMERS_CHECKBOX.done)
					return startSyncLists();

				if (response.hasOwnProperty('ORDERS_CHECKBOX') && !response.ORDERS_CHECKBOX.done)
					return startSyncLists();
			});
		}

		function checkSyncInProgress(chimpSync)		
		{

			if (getInProgress(chimpSync).length > 0)
				getSyncListsStatus();
			else
				showSyncListButton();
		}

		function inArray(value, arr)
		{
			return (arr.indexOf(value) === -1 ? false : true);
		}

		function ajaxStart(target)
		{
			var ajaxLoader = target.find('.ajax-loader');
			if (ajaxLoader.length > 0) 
			{
				ajaxLoader.show();
			}
		}

		function ajaxDone(target)
		{
			var ajaxLoader = target.find('.ajax-loader');
			if (ajaxLoader.length > 0) 
			{
				ajaxLoader.hide();
			}
		}

		function buildChimpTemplateGrid(id, arr)
		{
			var table = '' +
			'<table id="'+id+'" class="table table-bordered '+id+'">\
				<thead>\
				<tr>';

				if (inArray('preview_image', arr))
					table += '<th class="preview_image" data-field="preview_image">'+l('preview image')+'</th>';

				if (inArray('name', arr))
					table += '<th class="name" data-field="name">'+l('name')+'</th>';

				if (inArray('layout', arr))
					table += '<th class="layout" data-field="layout">'+l('layout')+'</th>';

				if (inArray('category', arr))
					table += '<th class="category" data-field="category">'+l('category')+'</th>';

				if (inArray('date_created', arr))
					table += '<th class="date_created" data-field="date_created">'+l('date created')+'</th>';

				if (inArray('active', arr))
					table += '<th class="np-active" data-field="active">'+l('active')+'</th>';

			table += '<th class="actions" data-template="actions">'+l('actions')+'</th>\
				</tr>\
				</thead>\
			</table>';

			return $(table);
		}

		function buildGalleryGrid(data)
		{
			return buildGkGrid('chimp-template-gallery', data, 'gallery');
		}

		function buildBaseGrid(data)
		{
			return buildGkGrid('chimp-template-base', data, 'base');
		}

		function buildUserGrid(data)
		{
			return buildGkGrid('chimp-template-user', data, 'user');
		}

		function buildGkGrid( id, data, type )
		{
			var dataModel,
				dataSource,
				dataGrid = buildChimpTemplateGrid(id, ['name', 'layout', 'category', 'active']);

			dataModel = new gk.data.Model({
				id: 'id',
			});

			dataSource = new gk.data.DataSource({
				pageSize: 7,
				transport: {
					data: data,
				},
				schema: {
					model: dataModel
				},
			});

			dataGrid.gkGrid({
				dataSource: dataSource,
				selectable: false,
				currentPage: 1,
				pageable: true,
				template: {
					active: function(item, value) {
						return (value ? '<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>' : '<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>');
					},

					actions: function(item) 
					{
						var preview_image = item.data.preview_image,
							content       = $('<div></div>'),
							preview       = preview_image == null ? '' : $('<a href="'+preview_image+'" class="btn btn-default btn-margin pull-right" target="_blank"><i class="icon icon-eye" style="margin-right: 8px;"></i> '+l('preview')+'</a>'),
							add           = $('<a href="javascript:{}" class="btn btn-default btn-margin pull-right btn-import-chimp-tpl"><span class="ajax-loader" style="display: none; margin-left: 0; margin-top: 0;"></span><span class="import-text" style="display: inline-block;"><i class="icon icon-download" style="margin-right: 8px;"></i> '+l('import')+'</span></a>');

						add.on('click', function(){

								var importText = add.find('.import-text');
								var name    = item.data.name;
								ajaxStart(add);
								importText.css({'padding-right': '7px'});
								importTemplate(item.data.id, type, name).always(function(){
									ajaxDone(add);
									importText.css({'padding-right': '0'});		
								});

						});

						content.append(preview);
						content.append(add);

						return content;
					},
				}
			});

			return {
				'dataSource': dataSource,
				'dataModel': dataModel,
				'dataGrid': dataGrid,
			};
		}

		function importTemplate(chimpIdTemplate, type, name)
		{
			return $.postAjax({'chimp': 'getTemplateSource', 'template_id': chimpIdTemplate, 'type': type}).done(function(response){
				if (!response.status)
					alertErrors(response.errors);
				else
				{
					var template = response.template;
					var message = l('template name');
					var newName = popUpImport(message, name);

					if ( newName == '' || newName == null )
						return false;

					if (newName)
						saveTemplate(newName, template, 0);
					else
						return false;
				}
			});
		}

		function saveTemplate(name, content, override) {
			var dom = self.dom,
				override = typeof override !== 'undefined' ? override : 0;

			return $.postAjax({'chimp': 'importTemplate', 'name': name, 'content': content, 'override': override } ).done(function( response ) {
				if (!response.status) {
					NewsletterPro.alertErrors(response.errors);
				} else {

					if (response.worning.hasOwnProperty('101'))
					{
						var message = response.worning['101'].replace(/\&quot;/g, '"');
						if (prompt(message, name))
							saveTemplate(name, content, 1);
						else
							return false;
					}

					var createTemplate = NewsletterPro.modules.createTemplate;

					createTemplate.vars.templateDataSource.sync(function(dataSource){
						var currentTemplate = dataSource.getItemByValue('data.filename', response.template_name);
						dataSource.setSelected(currentTemplate);
						createTemplate.changeTemplate(currentTemplate);
					});
				}

			}).fail(function( response ) {
				NewsletterPro.alertErrors([l('import failure')]);
			}).always(function( response ) {

			});
		}

		function setImportTemplateData(obj)
		{
			importTemplateData.user    = obj.user;
			importTemplateData.base    = obj.base;
			importTemplateData.gallery = obj.gallery;
		}

		function getImportTemplateData()
		{
			return importTemplateData;
		}

		function getAllTemplates(func)
		{
			return $.postAjax({'chimp': 'getAllTemplates'}).done(function(response){
				func(response);
			});
		}

		function popUpImport(message, name)
		{
			return prompt(message, name);
		}

		function startLoading(contentBox, contentAjax)
		{
			contentBox.css({
				'opacity': '0.5'
			});

			contentAjax.show();
			contentAjax.css({
				position: 'absolute',
				display: 'block',
				margin: '0',
				padding: '0',
				left: 780 / 2 - contentAjax.width() / 2,
				top: 400 / 2 - contentAjax.height() / 2,
			});
		}

		function endLoading(contentBox, contentAjax)
		{
			contentBox.css({
				'opacity': '1'
			});
			contentAjax.hide();
		}

		function exportTemplateRequest(name, idLang, filename, override)
		{
			box.modules.createTemplate.newsletterTemplate.saveTemplate().done(function(resp){
				if (!resp.status)
				{
					box.alertErrors(resp.errors);
				}
				else
				{
					box.showAjaxLoader(exportTemplateWinDom.btnExport)

					$.postAjax({'chimp': 'exportTemplate', 'name': name, 'id_lang': idLang, 'filename': filename, 'override': override }).done(function(response){
						if (!response.status) 
						{
							box.alertErrors(response.errors);
						}
						else
						{
							if (response.name_exists)
							{
								var message = box.displayAlert(response.message);
								var conf = confirm(message);
								if (conf)
								{
									exportTemplate(name, idLang, 1);
									return true;
								}
								else
									return false;
							}

							exportTemplateWinDom.success.show();
							setTimeout(function(){
								exportTemplateWinDom.success.hide();
							}, 7000);

						}
					}).always(function(){
						box.hideAjaxLoader(exportTemplateWinDom.btnExport);
					});
				}
			});
		}

		function exportTemplate(name, idLang, override)
		{
			var info     = NewsletterPro.modules.createTemplate.vars.templateDataSource.selected.data,
				name     = name || info.name,
				filename = info.filename,
				override = typeof override === 'undefined' ? 0 : override;

			exportTemplateRequest(name, idLang, filename, override);
		}

		// this function will run if the Mail Chimp is installed
		function installedChimpSettings()
		{
			var dom = self.dom;
			dom.btnChimpImportHtml.show();
			dom.btnChimpExportHtml.show();
			showUninstallButton();
			dom.syncBackChimpContent.show();

		}

		// this function will run if the Mail Chimp is not installed
		function uninstalledChimpSettings()
		{
			var dom = self.dom;
			dom.btnChimpImportHtml.hide();
			dom.btnChimpExportHtml.hide();
			showInstallButton();
			dom.syncBackChimpContent.hide();
		}

		self.ready(function(dom) 
		{
			l                  = NewsletterPro.translations.l(NewsletterPro.translations.modules.mailChimp);
			dataStorage        = box.dataStorage.data;
			chimpConfig        = dataStorage.chimp_config;
			chimpSyncProgress  = dataStorage.chimp_sync,
			contentIsSet	   = null,
			importTemplateComponents = {
				user: null,
				base: null,
				gallery: null,
			},
			importTemplateData = {
				user: null,
				base: null,
				gallery: null,
			};

			var getMailChimpTemplateName = function(isoColde, addIso)
			{
				var tnArray = box.dataStorage.get('configuration.NEWSLETTER_TEMPLATE').replace(/_/, ' ').replace(/\.html$/, '').split(' '),
					templateName = '',
					firstLang = box.dataStorage.get('all_languages')[0],
					isoColde = typeof isoColde !== 'undefined' ? isoColde : firstLang.iso_code,
					addIso = typeof addIso !== 'undefined' ? addIso : true;

				for (var i = 0; i < tnArray.length; i++) {
					templateName += box.ucfirst(tnArray[i]) + ' ';
				}

				if (addIso)
					templateName += isoColde.toUpperCase();

				// if (typeof isoColde !== 'undefined')

				return templateName;
			};

			exportTemplateWin = new gkWindow({
				width: 600,
				height: 400,
				setScrollContent: 340,
				title: l('Export Template To MailChimp'),
				className: 'np-export-mailchimp-template-win',
				show: function(win) 
				{
					exportTemplateWinDom.templateName.html(getMailChimpTemplateName(null, false));
					exportTemplateWinDom.chimpTemplateName.val(getMailChimpTemplateName());
				},
				close: function(win) {},
				content: function(win)
				{
					var languages = box.dataStorage.get('all_languages'),
						template,
						lang,
						languagesOptions = '';
					
					for (var i = 0; i < languages.length; i++)
					{
						lang = languages[i];

						languagesOptions += '<option value="'+lang.iso_code+'">'+lang.name+'</option>';
					}

					template = $('\
						<div class="form-group clearfix">\
							<div class="row">\
								<div class="form-group clearfix">\
									<label class="control-label col-sm-4"><span class="label-tooltip">'+l('Template Name')+'</span></label>\
									<div class="col-sm-8">\
										<span id="np-export-mailchimp-template-name" style="margin-top: 6px; display: block;"></span>\
									</div>\
								</div>\
								<div class="form-group clearfix">\
									<label class="control-label col-sm-4"><span class="label-tooltip">'+l('Language')+'</span></label>\
									<div class="col-sm-8">\
										<select id="np-export-mailchimp-template-lang-select" class="form-control fixed-width-xxl">\
											'+languagesOptions+'\
										</select>\
									</div>\
								</div>\
								<div class="form-group clearfix">\
									<label class="control-label col-sm-4"><span class="label-tooltip">'+l('MailChimp Name')+'</span></label>\
									<div class="col-sm-8">\
										<input id="np-export-mailchimp-template-input-tn" type="text" class="form-control fixed-width-xxl" value="">\
									</div>\
								</div>\
								<div class="form-group clearfix">\
									<div class="col-sm-8 col-sm-offset-4">\
										<a id="np-export-mailchimp-template-btn" href="javascript:{}" class="btn btn-default">\
											<span class="btn-ajax-loader"></span>\
											<i class="icon icon-download"></i>\
											'+l('Export')+'\
										</a>\
										<span id="np-export-mailchimp-template-success" style="display: none;"><span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span></span>\
									</div>\
								</div>\
							</div>\
						</div>\
					');

					exportTemplateWinDom = {
						templateName: template.find('#np-export-mailchimp-template-name'),
						langSelect: template.find('#np-export-mailchimp-template-lang-select'),
						chimpTemplateName: template.find('#np-export-mailchimp-template-input-tn'),
						btnExport: template.find('#np-export-mailchimp-template-btn'),
						success: template.find('#np-export-mailchimp-template-success'),
					};

					exportTemplateWinDom.langSelect.on('change', function(){
						var ctn = exportTemplateWinDom.chimpTemplateName,
							isoColde = $(this).val(),
							templateName = getMailChimpTemplateName(isoColde);

						ctn.val(templateName);
					});

					exportTemplateWinDom.btnExport.on('click', function(){
						var templateName = exportTemplateWinDom.chimpTemplateName.val(),
							isoColde = exportTemplateWinDom.langSelect.val(),
							languages = box.dataStorage.get('all_languages'),
							idLang = 0;

						for (var i = 0; i < languages.length; i++)
						{
							var lang = languages[i];
							if (lang.iso_code === isoColde)
							{
								idLang = Number(lang.id_lang);
								break;
							}
						}

						exportTemplate(templateName, idLang);
					});

					return template;
				}
			});

			var importTemplateWin = gkWindow({
				width: 800,
				height: 500,
				title: l('import template from chimp'),
				className: 'gk-import-template-window-view',
				show: function(win) {

					var content = contentIsSet != null ? contentIsSet : $('<div id="gk-import-content" style="position: relative;"><div id="gk-import-template-content-box"></div><span class="tpl-content ajax-loader"></span></div>'),
						contentBox = content.find('#gk-import-template-content-box'),
						contentAjax = content.find('.tpl-content.ajax-loader');

					startLoading(contentBox, contentAjax);

					getAllTemplates(function(response)
					{
						endLoading(contentBox, contentAjax);

						if (!response.status)
						{
							box.alertErrors(response.errors);
						}
						else
						{
							setImportTemplateData({
								user: response.templates.user,
								base: response.templates.base,
								gallery: response.templates.gallery,
							});

							if (importTemplateComponents.user == null)
							{
								importTemplateComponents.user = buildUserGrid(getImportTemplateData().user);
								var tableBox = $('<div></div>');
								tableBox.append('<h4 class="chimp-import-tpl-title user-template">'+l('user template')+'</h4>');
								tableBox.append(importTemplateComponents.user.dataGrid);
								contentBox.append(tableBox);
							}
							else
							{
								importTemplateComponents.user.dataSource.setData(getImportTemplateData().user);
								importTemplateComponents.user.dataSource.sync();
							}

							if (importTemplateComponents.gallery == null)
							{
								importTemplateComponents.gallery = buildGalleryGrid(getImportTemplateData().gallery);
								var tableBox = $('<div></div>');
								tableBox.append('<h4 class="chimp-import-tpl-title">'+l('gallery template')+'</h4>');
								tableBox.append(importTemplateComponents.gallery.dataGrid);
								contentBox.append(tableBox);
							}
							else
							{
								importTemplateComponents.gallery.dataSource.sync(getImportTemplateData().gallery);
							}

							if (importTemplateComponents.base == null)
							{
								importTemplateComponents.base = buildBaseGrid(getImportTemplateData().base);
								var tableBox = $('<div></div>');
								tableBox.append('<h4 class="chimp-import-tpl-title">'+l('base template')+'</h4>');
								tableBox.append(importTemplateComponents.base.dataGrid);
								contentBox.append(tableBox);
							}
							else
							{
								importTemplateComponents.base.dataSource.sync(getImportTemplateData().base);
							}
						}
					});

					if (contentIsSet == null)
					{
						contentIsSet = content;
						importTemplateWin.setContent(content);
					}
				},
			});

			checkSyncInProgress(chimpSyncProgress);

			if (dataStorage.chimpIsInstalled) 
				installedChimpSettings();
			else 
				uninstalledChimpSettings();

			if (getConfig('ORDERS_CHECKBOX'))
				dom.syncOrdersButtonText.show();
			else
				dom.syncOrdersButtonText.hide();

			dom.checkboxSyncCustomers.prop('checked', getConfig('CUSTOMERS_CHECKBOX'))
			dom.checkboxSyncVisitors.prop('checked', getConfig('VISITORS_CHECKBOX'))
			dom.checkboxSyncAdded.prop('checked', getConfig('ADDED_CHECKBOX'))
			dom.checkboxSyncOrders.prop('checked', getConfig('ORDERS_CHECKBOX'));

			// add events 
			dom.btnUninstallChimp.on('click', function(){
				uninstallChimp();
			});

			dom.btnInstallChimp.on('click', function(){
				var api_key = dom.inputChimpApiKey.val(),
					list_id = dom.inputChimpListId.val();

				installChimp(api_key, list_id);
			});

			dom.btnPingChimp.on('click', function(){
				pingChimp();
			});

			dom.checkboxSyncCustomers.on('change', function(){
				updateSyncCheckbox('CUSTOMERS_CHECKBOX', isChecked($(this)));
			});

			dom.checkboxSyncVisitors.on('change', function(){
				updateSyncCheckbox('VISITORS_CHECKBOX', isChecked($(this)));
			});

			dom.checkboxSyncAdded.on('change', function(){
				updateSyncCheckbox('ADDED_CHECKBOX', isChecked($(this)));
			});

			dom.checkboxSyncOrders.on('change', function(){
				var checked = isChecked($(this));
				if (checked)
					dom.syncOrdersButtonText.show();
				else
					dom.syncOrdersButtonText.hide();

				updateSyncCheckbox('ORDERS_CHECKBOX', checked);
			});

			dom.btnSyncLists.on('click', function(){
				setSyncLists({
					'CUSTOMERS_CHECKBOX': isChecked(dom.checkboxSyncCustomers),
					'VISITORS_CHECKBOX': isChecked(dom.checkboxSyncVisitors),
					'ADDED_CHECKBOX': isChecked(dom.checkboxSyncAdded),
					'ORDERS_CHECKBOX': isChecked(dom.checkboxSyncOrders),
				});
			});

			dom.btnStopSyncLists.on('click', function(){
				stopSync();
			});

			dom.btnChimpImportHtml.on('click', function(){
				importTemplateWin.show();
			});

			dom.btnChimpExportHtml.on('click', function(){
				exportTemplateWin.show();
			});

			dom.btnDeleteChimpOrders.on('click', function(){
				deleteChimpOrders();
			});

			dom.resetSyncOrderDate.on('click', function(){
				$.postAjax({'chimp': 'resetSyncOrderDate'}).done(function(response){
					if (response.success)
						dom.lastSyncOrders.html(response.date_add);
					else
						box.alertErrors(response.errors);
				});
			});

			var start  = 0,
				limit = 25,
				backTotal = 0,
				backCreated = 0,
				backUpdated = 0,
				backErrors = 0;


			var refreshLists = function()
			{
				var sn = box.modules.sendNewsletters;
				
				sn.vars.customers.sync();

				if (sn.isNewsletterProSubscriptionActive())
					sn.vars.visitorsNP.sync();
				else
					sn.vars.visitors.sync();

				sn.vars.added.sync();
			};

			var syncListBackFunc = function(start, limit)
			{
				dom.syncListsBack.show();
				dom.objSyncListBack.box.show();
				dom.objSyncListBack.ajaxLoader.show();
				dom.btnSyncListsBack.addClass('disabled');
				box.showAjaxLoader(dom.btnSyncListsBack);

				$.postAjax({'chimp': 'syncListsBack', start: start, limit: limit}).done(function(response){
					if (response.success)
					{
						backTotal += response.total;
						backCreated += response.created;
						backUpdated += response.updated;
						backErrors += response.errors_count;

						dom.objSyncListBack.total.html(response.member_count);
						dom.objSyncListBack.created.html(backCreated);
						dom.objSyncListBack.updated.html(backUpdated);
						dom.objSyncListBack.errors.html(backErrors);

						if (response.total > 0)
						{
							start++;
							syncListBackFunc(start, limit);
						}
						else
						{
							dom.btnSyncListsBack.removeClass('disabled');
							dom.objSyncListBack.ajaxLoader.hide();

							setTimeout(function(){
								dom.syncListsBack.hide();
								dom.objSyncListBack.box.hide();
							}, 15000);

							refreshLists();
						}
					}
					else
					{
						refreshLists();

						dom.btnSyncListsBack.removeClass('disabled');
						dom.objSyncBackErrorMessageBox.box.show();
						dom.objSyncBackErrorMessageBox.span.show().html(box.displayAlert(response.errors, '<br>'));
					}
				}).always(function(){
					box.hideAjaxLoader(dom.btnSyncListsBack);
				})
			};

			dom.btnSyncListsBack.on('click', function(){

				start = 0;
				limit = 25;
				backTotal = 0;
				backCreated = 0;
				backUpdated = 0;
				backErrors = 0;

				syncListBackFunc(start, limit);
			});
		});
	},

	ready: function(func) 
	{
		var self = this;
		$(document).ready(function(){

			var syncAddedProgress     = $('#sync-added-progress');
			var syncVisitorsProgress  = $('#sync-visitors-progress');
			var syncCustomersProgress = $('#sync-customers-progress');
			var syncOrdersProgress    = $('#sync-orders-progress');
			var errorMessageBox       = $('#sync-error-message-box');

			// var syncListBackBox       = $('#sync-lists-back-progress-box');
			var syncListBackError     = $('#sync-list-back-error-message-box');
			var syncListBackProgress  = $('#sync-list-back-progress');

			self.dom = {
				btnInstallChimp: $('#install-chimp'),
				btnPingChimp: $('#ping-chimp'),

				inputChimpApiKey: $('#chimp-api-key'),
				inputChimpListId: $('#chimp-list-id'),

				installLoading: $('#install-chimp-loading'),

				lastSyncOrders: $('#last-sync-orders'),

				btnUninstallChimp: $('#uninstall-chimp'),
				uninstallLoading: $('#uninstall-chimp-loading'),

				checkboxSyncCustomers: $('#sync-customers'),
				checkboxSyncVisitors: $('#sync-visitors'),
				checkboxSyncAdded: $('#sync-added'),
				checkboxSyncOrders: $('#sync-orders'),

				resetSyncOrderDate: $('#reset-sync-order-date'),

				syncOrdersButtonText: $('#sync-orders-button-text'),

				btnSyncLists: $('#sync-lists'),
				btnStopSyncLists: $('#stop-sync-lists'),

				btnDeleteChimpOrders: $('#delete-chimp-orders'),

				syncListsProgressBox: $('#sync-lists-progress-box'),

				syncChimpErrorsMessage: $('#sync-chimp-errors-message'),

				chimpMenu: $('#chimp-menu'),

				objSyncAddedProgress: {
					box: syncAddedProgress,
					total: syncAddedProgress.find('.sync-emails-total'),
					created: syncAddedProgress.find('.sync-emails-created'),
					updated: syncAddedProgress.find('.sync-emails-updated'),
					errors: syncAddedProgress.find('.sync-emails-errors'),
					ajaxLoader: syncAddedProgress.find('.ajax-loader'),
				},

				objSyncVisitorsProgress: {
					box: syncVisitorsProgress,
					total: syncVisitorsProgress.find('.sync-emails-total'),
					created: syncVisitorsProgress.find('.sync-emails-created'),
					updated: syncVisitorsProgress.find('.sync-emails-updated'),
					errors: syncVisitorsProgress.find('.sync-emails-errors'),
					ajaxLoader: syncVisitorsProgress.find('.ajax-loader'),
				},

				objSyncCustomersProgress: {
					box: syncCustomersProgress,
					total: syncCustomersProgress.find('.sync-emails-total'),
					created: syncCustomersProgress.find('.sync-emails-created'),
					updated: syncCustomersProgress.find('.sync-emails-updated'),
					errors: syncCustomersProgress.find('.sync-emails-errors'),
					ajaxLoader: syncCustomersProgress.find('.ajax-loader'),
				},

				objSyncOrdersProgress: {
					box: syncOrdersProgress,
					total: syncOrdersProgress.find('.sync-emails-total'),
					created: syncOrdersProgress.find('.sync-emails-created'),
					updated: syncOrdersProgress.find('.sync-emails-updated'),
					errors: syncOrdersProgress.find('.sync-emails-errors'),
					ajaxLoader: syncOrdersProgress.find('.ajax-loader'),
				},

				objErrorMessageBox : {
					box: errorMessageBox,
					span: errorMessageBox.find('.sync-error-message'),
				},

				btnChimpImportHtml: $('#chimp-import-html'),
				btnChimpExportHtml: $('#chimp-export-html'),

				syncListsBack: $('#sync-lists-back-progress-box'),
				btnSyncListsBack: $('#sync-chimp-lists-back'),

				objSyncBackErrorMessageBox: {
					box: syncListBackError,
					span: syncListBackError.find('.sync-error-message')
				},

				objSyncListBack: {
					box: syncListBackProgress,
					total: syncListBackProgress.find('.sync-emails-total'),
					created: syncListBackProgress.find('.sync-emails-created'),
					updated: syncListBackProgress.find('.sync-emails-updated'),
					errors: syncListBackProgress.find('.sync-emails-errors'),
					ajaxLoader: syncListBackProgress.find('.ajax-loader'),
				},

				syncBackChimpContent: $('#sync-back-chimp-content'),
			};

			func(self.dom);
		});
	},
}.init(NewsletterPro));