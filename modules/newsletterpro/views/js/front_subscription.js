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

NewsletterPro.namespace('modules.frontSubscription');
NewsletterPro.modules.frontSubscription = ({
	tinyWasInit: false,
	domSave: null,
	dom: null,
	box: null,
	vars: {},
	events: {},
	tabName: 'tab_newsletter_15',
	tinySetupArray: [],
	initTinyCallback: function(config, cfg)
	{
		this.tinySetupArray.push(config);
	},
	onTabShow: function()
	{

	},
	addVar: function(name, value)
	{
		this.vars[name] = value;
	},
	getVar: function(name)
	{
		if (this.vars.hasOwnProperty(name))
			return this.vars[name];
		return false;
	},
	isTinyInit: function()
	{
		return this.tinyWasInit;
	},

	initTiny: function()
	{
		var self = this;
		this.tinyInitDfd = $.Deferred();
		this.tinyWasInit = true;

		// resolve the deffered after 10 seconds if is not resolved
		setTimeout(function(){
			var message = 'Deffered was never resolved.';
			if (typeof self.tinyInitDfd.state === 'function')
			{
				if (self.tinyInitDfd.state() !== 'resolved')
				{
					self.tinyInitDfd.resolve();
					console.warn(message);
				}
			}
			else if (typeof self.tinyInitDfd.isResolved === 'function')
			{
				if (!self.tinyInitDfd.isResolved())
				{
					self.tinyInitDfd.resolve();
					console.warn(message);
				}
			}
		}, 10000);

		if (this.tinySetupArray.length > 0)
		{
			$.each(this.tinySetupArray, function(key, config){
				tinySetup(config);
			});				
		}

		return this.tinyInitDfd.promise();
	},
	init: function(box) 
	{

		var VALUE_PX      = this.VALUE_PX = 1,
			VALUE_PERCENT = this.VALUE_PERCENT = 0;

		var self = this,
			l,
			listOfInterestDataModel,
			listOfInterestDataSource,
			dataSourceRead,
			listOfInterestGrid,
			leftBox,
			addButton,
			winAdd,
			winUpdate,
			currentUpdateItem = null,
			subscriptionTemplatesDataModel,
			subscriptionTemplatesDataSource,
			subscriptionTemplatesDataGrid,
			subscriptionTemplateLastActiveItem,
			components = NewsletterProComponents,
			templateTab,
			tinyInstances,
			tinySubscribeMessage,
			tinyEmailSubscribeVoucherMessage,
			tinyEmailSubscribeConfirmationMessage,
			subscriptionTemplate,
			sliderTemplateWidth = {},
			sliderTemplateMaxMinWidth = {},
			sliderTemplateTop = {},
			settingsSlidersRefresh = [],
			subscrptionTemplateInit;

		self.box = box;

		self.ready(function(dom){
			self.dom = dom;
			l = self.l = NewsletterPro.translations.l(NewsletterPro.translations.modules.frontSubscription);
			dataSourceRead = { url: NewsletterPro.dataStorage.get('ajax_url')+'&submitSubscriptionController=getListOfInterest&id_lang=' + box.dataStorage.get('id_current_lang'), dataType: 'json' };

			box.dataStorage.set('id_selected_lang', parseInt(box.dataStorage.get('id_current_lang')));

			// select the languages from the content
			box.components.LanguageSelect.initBySelection($('.gk_lang_select'));
			// select languages from the template
			box.components.LanguageSelect.initBySelection(dom.listOfInterestTemplate.find('.gk_lang_select'));
			box.components.LanguageSelect.clickEvent = function(lang, key) {
				if (typeof templateTab !== 'undefined')
				{
					var lastItem = templateTab.getLastItem();
					if (lastItem)
					{
						idTab = lastItem.attr('id');
						if (idTab == 'tab_subscription_template-template_1')
						{
							setView(box.dataStorage.get('subscription_template_id'), lang.id_lang);
						}
					}
				}
			};

			// resize the iframe after content loads
			self.dom.subscriptionTemplateView.on('load', function(event){
				var item = templateTab.lastItem;
				if (item.length > 0 && item.attr('id') == 'tab_subscription_template-template_1')
					self.resizeView();
			});

			var langSelect = new box.components.LanguageSelect({
				selector: dom.frontSubscriptionLang,
				languages: box.dataStorage.get('all_languages'),
				// onChangeTrigger: false,
				click: function(lang, key)
				{
					setRead(lang.id_lang);
					listOfInterestDataSource.sync();
				}
			});

			listOfInterestDataModel = new gk.data.Model({
				id: 'id_newsletter_pro_list_of_interest',
			});

			listOfInterestDataSource = self.listOfInterestDataSource = new gk.data.DataSource({
				pageSize: 15,
				transport: {
					read: getRead(),
					update: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submitSubscriptionController=updateListOfInterestRecord&id',
						dataType: 'json',
					},

					destroy: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submitSubscriptionController=deleteListOfInterestRecord&id',
						type: 'POST',
						dateType: 'json',
						success: function(response, itemData) {
							if(!response)
								alert(l('delete record error'));
						},
						error: function(data, itemData) {
							alert(l('delete record error'));
						},
						complete: function(data, itemData) {},
					},

				},
				schema: {
					model: listOfInterestDataModel, 
				},
				trySteps: 2,
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						listOfInterestDataSource.syncStepAvailableAdd(3000, function(){
							listOfInterestDataSource.sync();
						});
					},
				}
			});

			listOfInterestGrid = dom.listOfInterest.gkGrid({
				dataSource: listOfInterestDataSource,
				selectable: false,
				currentPage: 1,
				pageable: true,
				template: {
					active: function(item, val)
					{
						var content = $('<div></div>');
						var a = getToggleButton(item, 'active', {'data': item.data});

						content.append(a);

						return content;
					},
					actions: function(item)
					{
						var content = $('<div></div>');

						var deleteRecord = $('#delete-loi-item')
							.gkButton({
								name: 'delete',
								title: l('delete'),
								className: 'btn btn-default btn-margin pull-right delete-loi-item',
								item: item,
								command: 'delete',
								confirm: function() {
									return confirm(l('delete record confirm'));
								},
								icon: '<i class="icon icon-trash-o"></i> ',
							});

						content.append(deleteRecord);

						var updateRecord = $('#update-loi-item')
							.gkButton({
								name: 'update',
								title: l('update'),
								className: 'btn btn-default btn-margin pull-right update-loi-item',
								item: item,
								click: function()
								{
									winUpdate.show();
									fillInputsVal(dom.loiUpdateInputs, item.data.id, item.data);
									dom.btnPosition.val(item.data.position);

									currentUpdateItem = item;
								},
								icon: '<i class="icon icon-save"></i> ',
							});

						content.append(updateRecord);

						return content;
					},
				}
			});

			winAdd = new gkWindow({
				width: 400,
				title: l('add'),
				className: 'add-new-listofinterest-window',
				show: function(win){},
				hide: function(win){},
				content: function(win) 
				{
					dom.listOfInterestAddNew.find('[name^="loi_input_"]').val('');
					return dom.listOfInterestAddNew; 
				}
			});

			winUpdate = new gkWindow({
				width: 400,
				title: l('update'),
				className: 'update-listofinterest-window',
				show: function(win){},
				hide: function(win){},
				content: function(win) 
				{
					return dom.listOfInterestUpdate; 
				}
			});

			var listOfInterestFooter = listOfInterestGrid.addFooter(function(columns){

				leftBox = $('<div style="float: right;"></div>');

				addButton = $('#add-new-listofinterest')
					.gkButton({
						title: l('add'),
						name: 'add-new-listofinterest',
						className: 'add-new-listofinterest',
						css: {
							'margin-right': '0',
						},
						click: function(event) 
						{
							dom.listOfInterestAddNew.find('[name^="loi_input_"]').val('');
							winAdd.show();
						},
						icon: '<i class="icon icon-plus-square"></i> ',
					});

				leftBox.append(addButton);

				return listOfInterestGrid.makeRow([leftBox]);
			}, 'prepend');

			dom.addLoiButton.on('click', function(event){
				$.postAjax({'submitSubscriptionController':'addNewListOfInterest', value: getInputsVal(dom.loiInputs)}).done(function(response){
					if (response.status)
					{
						winAdd.hide();
						listOfInterestDataSource.sync();
					}
					else
						box.alertErrors(response.errors);
				});
			});

			dom.updateLoiButton.on('click', function(event){
				if (currentUpdateItem != null)
				{					
					var dataPost = {};

					currentUpdateItem.data.position = parseInt(dom.btnPosition.val());

					dataPost['data'] = currentUpdateItem.data;
					dataPost['name'] = getInputsVal(dom.loiUpdateInputs);

					var default_lang = box.dataStorage.get('default_lang');

					var errorMessage = l('default lang empty');

					if (!dataPost['name'].hasOwnProperty(default_lang))
					{
						box.alertErrors(errorMessage);
						return;
					}
					else if ($.trim(dataPost['name'][default_lang]) == '')
					{
						box.alertErrors(errorMessage);
						return;
					}

					currentUpdateItem.update(dataPost).done(function(response) {
						if (!response)
							box.alertErrors(l('error when updateing the record'));
						else
						{
							listOfInterestDataSource.sync();
						}
					});
				}
				else
					box.alertErrors(l('error when updateing the record'));

				winUpdate.hide();
			});

			// Subscription Templates Grid
			subscriptionTemplatesDataModel = new gk.data.Model({
				id: 'id_newsletter_pro_subscription_tpl',
			});

			subscriptionTemplatesDataSource = self.subscriptionTemplatesDataSource = new gk.data.DataSource({
				pageSize: 5,
				transport: {
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submitSubscriptionController=getTemplatesDataGrid',
						dataType: 'json',
					},

					update: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submitSubscriptionController=updateTemplatesDataGrid&id',
						dataType: 'json',
					},

					destroy: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submitSubscriptionController=deleteTemplatesDataGrid&id',
						type: 'POST',
						dateType: 'json',
						success: function(response, itemData) {
							if(!response)
								alert(l('delete record error'));
						},
						error: function(data, itemData) {
							alert(l('delete record error'));
						},
						complete: function(data, itemData) {},
					},

				},
				schema: {
					model: subscriptionTemplatesDataModel, 
				},
				trySteps: 2,
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						subscriptionTemplatesDataSource.syncStepAvailableAdd(3000, function(){
							subscriptionTemplatesDataSource.sync();
						});
					},
				}
			});

			subscriptionTemplatesDataGrid = dom.subscriptionTemplates.gkGrid({
				dataSource: subscriptionTemplatesDataSource,
				selectable: true,
				currentPage: 1,
				pageable: true,
				template: {
					name: function(item, val)
					{
						return box.ucfirst(val).replace(/_/g, ' ');
					},
					display_gender: function(item, val)
					{
						return getToggleButton(item, 'display_gender');
					},
					display_firstname: function(item, val)
					{
						return getToggleButton(item, 'display_firstname');
					},

					display_lastname: function(item, val)
					{
						return getToggleButton(item, 'display_lastname');
					},

					display_language: function(item, val)
					{
						return getToggleButton(item, 'display_language');
					},

					display_birthday: function(item, val)
					{
						return getToggleButton(item, 'display_birthday');
					},

					display_list_of_interest: function(item, val)
					{
						return getToggleButton(item, 'display_list_of_interest');
					},

					list_of_interest_type: function(item, val)
					{
						return getToggleButton(item, 'list_of_interest_type');
					},

					active: function(item, val)
					{
						if (parseInt(item.data.active) == 1)
							subscriptionTemplateLastActiveItem = item;

						var images = {
							'1': '../modules/newsletterpro/views/img/active.png', 
							'2': '../modules/newsletterpro/views/img/inactive.png'
						};

						var button = getActiveButton(item, 'active', null, images);

						button.on('click', function(){

							subscriptionTemplatesDataSource.parse(function(currentItem){
								currentItem.instance.find('td.np-active img').attr('src', images[2]);
							});

							button.find('img').attr('src', images[1]);
						});

						return button;
					},
					actions: function(item)
					{
						var content = $('<div></div>');

						var deleteRecord = $('#delete-loi-item')
							.gkButton({
								name: 'delete',
								title: l('delete'),
								className: 'btn btn-default pull-right delete-loi-item',
								item: item,
								command: 'delete',
								click: function()
								{
									if ($.trim(String(item.data.name)) == 'default')
									{
										box.alertErrors([l('the default template cannot be deleted')]);
										return false;
									}
								},
								confirm: function() 
								{
									var conf = confirm(l('delete record confirm'));

									if (conf && NewsletterPro.dataStorage.get('subscription_template') == item.data.name)
									{
										var defaultItem = subscriptionTemplatesDataSource.getItemByValue('data.name', 'default');
										if (defaultItem)
										{
											subscriptionTemplatesDataSource.setSelected(defaultItem);
											changeTemplate(defaultItem);
										}
									}

									return conf;
								},
								icon: '<i class="icon icon-trash-o"></i> ',
							});

						content.append(deleteRecord);
						return content;
					},
				},
				events: {
					select: function(item) 
					{
						changeTemplate(item);

					}
				},
				defineSelected: function(item) 
				{
					return item.data.selected;
				},
			}); 
			// end of Subscription Templates Grid

			dom.createSubscriptionTemplateBackup.on('click', function(){
				var name = prompt(l('backup name'));
				if (!$.trim(name))
					return false;

				var that = this;
				box.showAjaxLoader($(this));

				$.postAjax({'submitSubscriptionController': 'ajaxCreateBackupSubscriptionTemplate', 'name': name}).done(function(response){
					if (response.status)
					{
						box.alertErrors(response.msg)
						if (typeof self.loadBackupDataSource !== 'undefined')
							self.loadBackupDataSource.sync();

					}
					else
						box.alertErrors(response.errors.join('\n'));
				}).always(function(){
					box.hideAjaxLoader($(that));
				})
			});

			dom.loadSubscriptionTemplateBackup.on('click', function(){
				self.showLoadBackup();
			});

			templateTab = components.TabItems.clone().init( dom.tabSubscriptionTemplate, dom.tabSubscriptionTemplateContent, function(item){
				var id = item.attr('id');
				var languageDisplay = [
					'tab_subscription_template-template_2', 
					'tab_subscription_template-template_3', 
					'tab_subscription_template-template_4',
					'tab_subscription_template-template_5',
					'tab_subscription_template-template_7',
				];

				if (languageDisplay.indexOf(id) == -1)
					dom.subscriptionTemplateLanguage.show();
				else
					dom.subscriptionTemplateLanguage.hide();

				if (id == 'tab_subscription_template-template_1')
				{
					if (typeof subscriptionTemplate !== 'undefined')
						subscriptionTemplate.save();

					self.resizeView();
				}

				// settings tab
				if (id == 'tab_subscription_template-template_3')
				{
					refreshSlider(sliderTemplateWidth);
					refreshSlider(sliderTemplateMaxMinWidth);
					refreshSlider(sliderTemplateTop);
				}

			});

			try {
				var txtAreas = [dom.subscriptionTemplateCss];

				$.each(txtAreas, function(i, item){

					item.on('keydown', function(event){
						var key = event.keyCode,
							textarea = item.get(0);

						if (key == 9) {
							try {
						        var newCaretPosition;
						        newCaretPosition = textarea.getCaretPosition() + "    ".length;
						        textarea.value = textarea.value.substring(0, textarea.getCaretPosition()) + "    " + textarea.value.substring(textarea.getCaretPosition(), textarea.value.length);
						        textarea.setCaretPosition(newCaretPosition);
						        return false;
							} catch (error) {
								console.warn(error);
							}
						}
						if (key == 8) {
							try {
							    if (textarea.value.substring(textarea.getCaretPosition() - 4, textarea.getCaretPosition()) == "    ") { 
						            var newCaretPosition;
						            newCaretPosition = textarea.getCaretPosition() - 3;
						            textarea.value = textarea.value.substring(0, textarea.getCaretPosition() - 3) + textarea.value.substring(textarea.getCaretPosition(), textarea.value.length);
						            textarea.setCaretPosition(newCaretPosition);
						        }
							} catch (error) {
								console.warn(error);	
							}
						}
					});
				});
			} catch (error) {
				console.warn(error);
			}

			// subscription template initialization
			tinyInstances = [];
			NewsletterPro.onObject.setCallback('subscription_template', function(ed){
				tinyInstances.push({
					id: ed.id,
					idLang: parseInt(ed.id.match(/\d+$/)),
					ed: ed,
				});

				// all the multilanguage tinymce has been initialized
				if (tinyInstances.length == box.dataStorage.get('all_languages').length)
				{

				}
			});	// end of subscription template initialization

			tinySubscribeMessage = [];
			NewsletterPro.onObject.setCallback('s_subscribe_message', function(ed){
				tinySubscribeMessage.push({
					id: ed.id,
					idLang: parseInt(ed.id.match(/\d+$/)),
					ed: ed,
				});

				if (tinySubscribeMessage.length == box.dataStorage.get('all_languages').length)
				{
					// all the tinymce are ready 
				}
			});

			tinyEmailSubscribeVoucherMessage = [];
			NewsletterPro.onObject.setCallback('s_email_subscribe_voucher_message', function(ed){

				tinyEmailSubscribeVoucherMessage.push({
					id: ed.id,
					idLang: parseInt(ed.id.match(/\d+$/)),
					ed: ed,
				});

				if (tinyEmailSubscribeVoucherMessage.length == box.dataStorage.get('all_languages').length)
				{
					// all the tinymce are ready 
				}
			});

			tinyEmailSubscribeConfirmationMessage = [];

			NewsletterPro.onObject.setCallback('s_email_subscribe_confirmation_message', function(ed){
				tinyEmailSubscribeConfirmationMessage.push({
					id: ed.id,
					idLang: parseInt(ed.id.match(/\d+$/)),
					ed: ed,
				});

				if (tinyEmailSubscribeConfirmationMessage.length == box.dataStorage.get('all_languages').length)
				{
					// this script should run at the last tinymce init
					subscrptionTemplateInit.init();
					self.tinyInitDfd.resolve();
				}
			});

			subscrptionTemplateInit = {
				init: function()
				{
					// create an instance of the subscription template
					subscriptionTemplate = new box.components.SubscriptionTemplate({
						id: parseInt(NewsletterPro.dataStorage.get('subscription_template_id')),
						tiny: function() 
						{
							var instances = {};
							$.each(tinyInstances, function(i, item){
								instances[item.idLang] = item.ed;
							});
							return instances;
						},
						tinySubscribeMessage: function()
						{
							var instances = {};
							$.each(tinySubscribeMessage, function(i, item){
								instances[item.idLang] = item.ed;
							});
							return instances;
						},
						tinyEmailSubscribeVoucherMessage: function()
						{
							var instances = {};
							$.each(tinyEmailSubscribeVoucherMessage, function(i, item){
								instances[item.idLang] = item.ed;
							});
							return instances;
						},
						tinyEmailSubscribeConfirmationMessage: function()
						{
							var instances = {};
							$.each(tinyEmailSubscribeConfirmationMessage, function(i, item){
								instances[item.idLang] = item.ed;
							});
							return instances;
						},
						cssStyle: dom.subscriptionTemplateCss, 
						cssGlobalStyle: dom.subscriptionTemplateCssGlobal, 
						view: dom.subscriptionTemplateView, 
						viewInANewWindow: dom.viewInANewWindow,
						settings: {
							displayGender: dom.displayGender,
							displayFirstName: dom.displayFirstName,
							displayLastName: dom.displayLastName,
							displayLanguage: dom.displayLanguage,
							displayBirthday: dom.displayBirthday,
							displayListOfInterest: dom.displayListOfInterest,
							displaySubscribeMessage: dom.displaySubscribeMessage,
							listOfInterestType: dom.listOfInterestType,
							allowMultipleTimeSubscription: dom.allowMultipleTimeSubscription,
							activateTemplate: dom.activateTemplate,
							voucher: dom.voucher,
							termsAndConditionsUrl: dom.termsAndConditionsUrl,

							showOnPages: dom.showOnPages,
							cookieLifetime: dom.cookieLifetime,
							cookieLifetimeSeconds: dom.cookieLifetimeSeconds,
							startTimer: dom.startTimer,
							whenToShow: dom.whenToShow,
						},
						sliders: {
							sliderTemplateWidth: sliderTemplateWidth,
							sliderTemplateMaxMinWidth: sliderTemplateMaxMinWidth,
							sliderTemplateTop: sliderTemplateTop,
						},
						dom: {
							firstNameMandatory: $('[name="newsletter_pro_subscription_mandatory_firstname"]'),
							lastNameMandatory: $('[name="newsletter_pro_subscription_mandatory_lastname"]'),
						},
						saveBtn: dom.saveSubscriptionTemplate, 
						saveAsBtn: dom.saveAsSubscriptionTemplate, 
						saveMessage: dom.saveSubscriptionTemplateMessage, 

						afterSettingsChange: function(instance, settingsName, response, bool)
						{
							if (response.status)
								subscriptionTemplatesDataSource.sync();
						},

						afterSave: function(instance, response)
						{
							if (response.status)
								subscriptionTemplatesDataSource.sync();
						},

						afterSaveAs: function(instance, response)
						{
							if (response.status)
							{
								subscriptionTemplatesDataSource.sync(function(){

									var defaultItem = subscriptionTemplatesDataSource.getItemByValue('data.name', $.trim(response.name));
									if (defaultItem)
									{
										subscriptionTemplatesDataSource.setSelected(defaultItem);
										changeTemplate(defaultItem);
									}
								});
							}
						}
					});

					self.addVar('subscriptionTemplate', subscriptionTemplate);
				}
			};

			// don't catch error if exists
			$.postAjax({'submitSubscriptionController': 'ajaxGetActiveTemplatesVouchersErrors'}, 'json', false).done(function(errors){
				if (errors.length > 0)
				{
					dom.voucherAlert.show().html(errors.length);
					var offset = dom.voucherAlert.offset(),
						width = dom.voucherAlert.outerWidth(),
						height = dom.voucherAlert.outerHeight(),
						boxWidth = dom.voucherAlertBox.outerWidth(),
						left = ( offset.left + width / 2 - boxWidth / 2) + 'px',
						top = ( offset.top + height + 5 ) + 'px';

					dom.voucherAlertBox.css({
						left: left,
						top: top,
					});

					var ul = $('<ul>');
					var len = errors.length;
					for (var i in errors)
					{
						var li = $('<li>');
						if (len - 1 == i)
							li.addClass('last-item');

						li.html(errors[i]);
						ul.append(li);
					}

					dom.voucherAlertBox.html(ul);

					dom.voucherAlert.on('mouseover', function(){
						dom.voucherAlertBox.stop(true, true).fadeIn();
					});

					dom.voucherAlert.on('mouseout', function(){
						dom.voucherAlertBox.stop(true, true).fadeOut();
					});
				}
			});

			var bodyInfo = getStroageBodyInfo();
			var currentType = getBodyWidthType(bodyInfo.body_width);

			box.dataStorage.set('subscription_template_current_type', currentType);

			setSliderTemplateWidth( gkSlider(getSliderTemplateWidthConfig(currentType)) );

			setCheckboxType(currentType);

			var defalutMin = 0;
			var defaultMax = 1280;

			var minValueMaxMin = bodyInfo.body_min_width > 0 && bodyInfo.body_min_width >= defalutMin ? bodyInfo.body_min_width : defalutMin,
				maxValueMaxMin = bodyInfo.body_max_width > 0 && bodyInfo.body_max_width <= defaultMax ? bodyInfo.body_max_width : defaultMax;

			var lastMaxValue = maxValueMaxMin,
				lastMinValue = minValueMaxMin;

			sliderTemplateMaxMinWidth = gkSliderRange({
				target: dom.sliderTemplateMaxMinWidth,
				min : defalutMin,
				max : defaultMax,
				valueMin : minValueMaxMin,
				valueMax : maxValueMaxMin,
				values : [defalutMin, defaultMax],
				snap: 2,
				prefix: 'px',
				move: function(obj) {},
				done: function(obj) 
				{
					var max = obj.getValueMax();
					if (lastMaxValue != max)
					{
						lastMaxValue = max;
						setStorageBodyInfo('body_max_width', parseInt(max));
						subscriptionTemplate.setSetting('body_max_width', parseInt(max));
					}

					var min = obj.getValueMin();
					if (lastMinValue != min)
					{
						lastMinValue = min;
						setStorageBodyInfo('body_min_width', parseInt(max));
						subscriptionTemplate.setSetting('body_min_width', parseInt(min));
					}
				},
			});

			sliderTemplateTop = gkSlider({
				target: dom.sliderTemplateTop,
				min : 0,
				max : 500,
				value : ( bodyInfo.body_top <= 500 ? bodyInfo.body_top : 100 ) ,
				values : [0,500],
				corectPosition: -7,
				prefix: 'px',
				snap: 2,
				move: function(obj) {

				},
				start: function(obj) 
				{

				},
				done: function(obj) 
				{
					var value = parseInt(obj.getValue());

					setStorageBodyInfo('body_top', value);
					subscriptionTemplate.setSetting('body_top', value);
				},
			});

			dom.sliderTemplateWidthType.on('change', function(){
				var selector = dom.sliderTemplateWidthType.selector;
				var val = $(selector + ':checked').val();

				switch(parseInt(val))
				{
					case VALUE_PX:
							setSliderTemplateWidth( gkSlider(getSliderTemplateWidthConfig(VALUE_PX)) );
							box.dataStorage.set('subscription_template_current_type', parseInt(val));
						break;

					case VALUE_PERCENT:
							setSliderTemplateWidth( gkSlider(getSliderTemplateWidthConfig(VALUE_PERCENT)) );
							box.dataStorage.set('subscription_template_current_type', parseInt(val));
						break;
				}
				sliderTemplateWidth.refresh();
				// execute the done event to actualize the database
				eventSliderTemplateDone(sliderTemplateWidth);
			});

			dom.voucher.on('change', function(event){
				var value = $(event.currentTarget).val();
				dom.voucher.val(value);
				subscriptionTemplate.setSetting('voucher', value);
			});

			var crossClassName = box.dataStorage.get('configuration.CROSS_TYPE_CLASS');
			$('span.'+crossClassName).addClass('selected');

			dom.corss.on('click', function(){
				updateCross($(this).attr('class'));
			});

			dom.corss1.on('click', function(){
				updateCross($(this).attr('class'));
			});

			dom.corss2.on('click', function(){
				updateCross($(this).attr('class'));
			});

			dom.corss3.on('click', function(){
				updateCross($(this).attr('class'));
			});

			dom.corss4.on('click', function(){
				updateCross($(this).attr('class'));
			});

			dom.corss5.on('click', function(){
				updateCross($(this).attr('class'));
			});
		}); // end of document ready

		function updateCross(crossClassName)
		{
			$('.np-subscription-cross span').removeClass('selected');
			$('span.'+crossClassName).addClass('selected');

			$.updateConfiguration('CROSS_TYPE_CLASS', crossClassName).done(function(response){
				if (response.success)
					box.dataStorage.set('configuration.CROSS_TYPE_CLASS', crossClassName);
				else
					box.alertErrors(response.errors);
			});
		}

		function setSliderTemplateWidth(slider)
		{
			sliderTemplateWidth = slider;

			if (typeof subscriptionTemplate !== 'undefined')
				subscriptionTemplate.setSliderConfiguration('sliderTemplateWidth', sliderTemplateWidth);
		}

		self.setSliderTemplateWidth = function(slider)
		{
			return setSliderTemplateWidth(slider);
		}

		function setStorageBodyInfo(name, value)
		{
			dataObj = getStroageBodyInfo();

			if (dataObj.hasOwnProperty(name))
				dataObj[name] = value;

			box.dataStorage.set('subscription_template_body_info', dataObj);
		}

		self.setStorageBodyInfo = function(name, value)
		{
			return setStorageBodyInfo(name, value);
		};

		function getStroageBodyInfo()
		{
			return box.dataStorage.get('subscription_template_body_info');
		}

		function getBodyWidthType(value)
		{
			if (/\%/.test(value))
				return VALUE_PERCENT;
			return VALUE_PX;
		}

		self.getBodyWidthType = function(value)
		{
			return getBodyWidthType(value);
		};

		function setCheckboxType(currentType)
		{
			var checkedSLiderType = self.dom.sliderTemplateWidthType.filter(function(i, item){
				item = $(item);
				return parseInt(item.prop('value')) == parseInt(currentType);
			});

			checkedSLiderType.prop('checked', true);
		}

		self.setCheckboxType = function(currentType)
		{
			return setCheckboxType(currentType);
		};

		// slider template callbacks
		var eventSliderTemplateDone = function(obj)
		{
			currentType = box.dataStorage.get('subscription_template_current_type');

			var value = obj.getValue();

			switch(currentType)
			{
				case VALUE_PERCENT:
					value = String(value + '%');
					break;
			}

			setStorageBodyInfo('body_width', value);
			subscriptionTemplate.setSetting('body_width', value);
		};

		var eventSliderTemplateStart = function(obj) {};
		var eventSliderTemplateMove = function(obj) {};

		function getSliderTemplateWidthConfig(type)
		{
			var cfg = {};
			var info = getStroageBodyInfo();
			var bodyWidth = parseInt(info.body_width);

			switch(type)
			{
				case VALUE_PX:
					cfg = {
						target: self.dom.sliderTemplateWidth,
						min : 200,
						max : 1280,
						value : (bodyWidth > 0 && bodyWidth >= 200 ? bodyWidth : 600),
						values : [200,1280],
						corectPosition: -15,
						prefix: 'px',
						snap: 2,
					};
					break;

				case VALUE_PERCENT:
					cfg = {
						target: self.dom.sliderTemplateWidth,
						min : 1,
						max : 100,
						value : (bodyWidth > 0 && bodyWidth <= 100 ? bodyWidth : 40),
						values : [1,50,100],
						corectPosition: -7,
						prefix: '%',
						snap: 2,
					};
					break;
			}

			cfg = $.extend({}, cfg, {
				move: eventSliderTemplateMove,
				start: eventSliderTemplateStart,
				done: eventSliderTemplateDone,
			});

			return cfg;
		}

		self.getSliderTemplateWidthConfig = function(type)
		{
			return getSliderTemplateWidthConfig(type);
		}

		// function changeActiveSettings(item)
		// {
		// 	if (NewsletterPro.dataStorage.get('subscription_template') == item.data.name)
		// 	{
		// 		subscriptionTemplate.setSettings({
		// 			display_gender: item.data.display_gender,
		// 			display_firstname: item.data.display_firstname,
		// 			display_lastname: item.data.display_lastname,
		// 			display_language: item.data.display_language,
		// 			display_birthday: item.data.display_birthday,
		// 			display_list_of_interest: item.data.display_list_of_interest,
		// 			display_subscribe_message: item.data.display_subscribe_message,
		// 			list_of_interest_type: item.data.list_of_interest_type,
		// 			active: item.data.active,
		// 		});
		// 	}
		// }

		function refreshSlider(sliderObj)
		{
			if (settingsSlidersRefresh.indexOf(sliderObj) == -1)
			{
				settingsSlidersRefresh.push(sliderObj);
				sliderObj.refresh();
			}
		}

		function clearRefreshSlider()
		{
			settingsSlidersRefresh = [];
		}

		function fillInputsVal(selector, id, selectedData)
		{
			// fill fast the current fields
			$.each(selector, function(i, item){
				item = $(item);
				var matchLang = item.attr('name').match(/\d+$/),
				idLang = matchLang !== null ? matchLang[0] : 0;
				if (selectedData.id_lang == idLang)
					item.val(selectedData.name);
			});

			// fill all fields
			$.postAjax({'submitSubscriptionController': 'getListOfInterestNameLang', 'id': id}).done(function(response){
				if (response.status)
				{
					var fields = response.name;

					$.each(selector, function(key, item){
						item = $(item);

						var matchLang = item.attr('name').match(/\d+$/),
							idLang = matchLang !== null ? matchLang[0] : 0;

						if (fields.hasOwnProperty(idLang))
							item.val(fields[idLang]);
					});
				}
				else
					box.alertErrors(response.errors);
			});
		}

		function getInputsVal(selector)
		{
			var result = {};
			$.each(selector, function(key, item){
				var matchLang = $(item).attr('name').match(/\d+$/),
					idLang = matchLang !== null ? matchLang[0] : 0;

				result[idLang] = $(item).val();
			});
			return result;
		}

		function getRead()
		{
			return dataSourceRead;
		}

		function setRead(idLang)
		{
			dataSourceRead.url = dataSourceRead.url.replace(/\&id_lang=\d+/, '&id_lang='+idLang);
		}

		function getToggleButton(item, field, updateData, icons)
		{
			updateData = updateData || item.data;
			icons = icons || {
				'1': '<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>', 
				'2': '<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>'
			};

			var a = $('<a href="javascript:{}"></a>');
			var switchActive = function switchActive() 
			{
				if (isActive()) 
					a.html(icons['1']);
				else
					a.html(icons['2']);
			};

			var isActive = function isActive()
			{
				return parseInt(item.data[field]);
			};

			switchActive();
			a.on('click', function(e){
				e.stopPropagation();

				item.data[field] = (isActive() ? 0 : 1);
				switchActive();

				item.update(updateData).done(function(response) {
					if (!response) 
					{
						item.data[field] = (isActive() ? 0 : 1);
						switchActive();
						box.alertErrors(l('error when updateing the record'));
					}
					// else
					// 	changeActiveSettings(item);

				}).fail(function(){
					item.data[field] = (isActive() ? 0 : 1);
					switchActive();
					box.alertErrors(l('error when updateing the record'));
				});				
			});
			return a;
		}

		function getActiveButton(item, field, updateData, icons)
		{
			updateData = updateData || item.data;
			icons = icons || {'1': '../img/admin/enabled.gif', '2': '../img/admin/disabled.gif'};

			var a = $('<a href="javascript:{}"></a>');
			var switchActive = function switchActive() 
			{
				if (isActive()) 
					a.html('<img src="'+icons['1']+'" alt="'+l('unsubscribe')+'">');
				else
					a.html('<img src="'+icons['2']+'" alt="'+l('subscribe')+'">');
			};

			var isActive = function isActive()
			{
				return parseInt(item.data[field]);
			};

			switchActive();
			a.on('click', function(e){

				e.stopPropagation();

				item.data[field] = 1;
				switchActive();

				item.update(updateData).done(function(response) {
					if (!response) 
					{
						if (typeof subscriptionTemplateLastActiveItem !== 'undefined')
						{
							subscriptionTemplateLastActiveItem.data[field] = 1;
							subscriptionTemplateLastActiveItem.instance.find('td.active img').attr('src', icons[1])
						}

						item.data[field] = 0;
						switchActive();
						box.alertErrors(l('error when updateing the record'));
					}
					else
					{
						subscriptionTemplateLastActiveItem = item;
						// changeActiveSettings(item);
					}

				}).fail(function(){

					if (typeof subscriptionTemplateLastActiveItem !== 'undefined')
					{
						subscriptionTemplateLastActiveItem.data[field] = 1;
						subscriptionTemplateLastActiveItem.instance.find('td.active img').attr('src', icons[1])
					}

					item.data[field] = 0;
					switchActive();
					box.alertErrors(l('error when updateing the record'));
				});

			});
			return a;
		}

		function setView(idTemplate, idLang)
		{
			idTemplate = parseInt(idTemplate);
			idLang     = parseInt(idLang);

			return $.postAjax({'submitSubscriptionController': 'ajaxGetViewLink', id_template: idTemplate, id_lang: idLang}).done(function(response){
				if (response.status)
					subscriptionTemplate.setView(response.view);
			});
		}

		function changeTemplate(item)
		{
			if (typeof subscriptionTemplate !== 'undefined')
			{
				clearRefreshSlider();
				subscriptionTemplate.setTemplateById(item.data.id);
			}
		}
		return self;
	},

	resizeView: function()
	{
		var self = this;

		function getView()
		{
			return self.dom.subscriptionTemplateView.contents().find('html');
		}

		if (getView().length > 0) 
		{
			var body = getView().find('body');
			if (body.length > 0) 
			{
				var wnt = self.dom.subscriptionTemplateView.get(0);
				wnt.height = '';
				wnt.height = wnt.contentWindow.document.body.scrollHeight + "px";

			}
		}
	},

	showSubscriptionHelp: function()
	{
		var self = this;
		var l = self.l;

		if (typeof self.subscriptionHelpWindow === 'undefined')
		{
			self.subscriptionHelpWindow =  new gkWindow({
				width: 640,
				height: 540,
				title: l('view available variables'),
				className: 'subscription-help-win',
				show: function(win) {},
				close: function(win) {},
				content: function(win) {
					$.postAjax({'submitSubscriptionController': 'showSubscriptionHelp'}, 'html').done(function(response) {
						win.setContent(response);
					});
					return '';
				}
			});

			self.subscriptionHelpWindow.show();
		}
		else
			self.subscriptionHelpWindow.show();
	},

	showLoadBackup: function()
	{
		var self = this,
			l = self.l,
			box = NewsletterPro;

		if (typeof self.loadBackupWindow === 'undefined')
		{
			var content,
				dataModel,
				dataSource,
				dataGrind;

			self.loadBackupWindow = new gkWindow({
				width: 800,
				height: 500,
				title: l('load subscription template backup'),
				content: function(win)
				{
					$.postAjax({'submitSubscriptionController': 'showLoadBackup'}, 'html').done(function(response) {
						win.setContent(response);					

						content = win.getContent();
						content.css({
							'padding': 0,
						});

						dataGrind = content.find('#load-backup-subscription-templates');

						dataModel = new gk.data.Model({
							id: 'id',
						});

						dataSource = self.loadBackupDataSource = new gk.data.DataSource({
							pageSize: 9,
							transport: {
								read: {
									url: NewsletterPro.dataStorage.get('ajax_url')+'&submitSubscriptionController=getSubscriptionTemplateBackup',
									dataType: 'json',
								},

								destroy: {
									url: NewsletterPro.dataStorage.get('ajax_url')+'&submitSubscriptionController=ajaxDeleteBackupSubscriptionTemplate&id',
									type: 'POST',
									dateType: 'json',
									success: function(response, itemData) {

										if(!response.status) {
											alert(response.errors.join("\n"));
										}
									},
									error: function(data, itemData) {
										alert(l('delete record error'));
									},
									complete: function(data, itemData) {},
								},					
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

						dataGrind.gkGrid({
							dataSource: dataSource,
							selectable: false,
							currentPage: 1,
							pageable: true,
							template: {
								actions: function(item) 
								{
									var contentBox = $('<div style="text-align: center;"></div>');
									var deleteRecord = $('#delete-subscription-backup')
										.gkButton({
											name: 'delete',
											title: l('delete'),
											className: 'btn btn-default btn-margin pull-right ',
											click: function(e)
											{
												if (!confirm(l('delete record confirm')))
													return false;

												item.destroy('status');
											},
											icon: '<i class="icon icon-trash-o"></i> ',
										});

									var loadRecord = $('#load-subscription-backup')
										.gkButton({
											name: 'load',
											title: l('load'),
											className: 'btn btn-default btn-margin pull-right ',
											click: function(e)
											{
												var conf = confirm(l('load backup confirm'));
												if (!conf || conf == '')
													return false;

												box.showAjaxLoader(loadRecord);

												$.postAjax({'submitSubscriptionController': 'ajaxLoadBackupSubscriptionTemplate', name: item.data.name}).done(function(response) {
													if (response.status)
													{
														box.alertErrors(response.msg);

														if (!NewsletterPro.dataStorage.get('subscription_template_id'))
															window.location.reload();
													}
													else
														box.alertErrors(response.errors.join('\n'))

												}).always(function(){
													box.hideAjaxLoader(loadRecord);
													if (typeof self.subscriptionTemplatesDataSource !== 'undefined')
														self.subscriptionTemplatesDataSource.sync();

													if (typeof self.listOfInterestDataSource !== 'undefined')
														self.listOfInterestDataSource.sync();
												});
											},
											icon: '<span class="btn-ajax-loader" style="margin-top: 4px;"></span> <i class="icon icon-upload"></i> ',
										});

									contentBox.append(loadRecord);
									contentBox.append(deleteRecord);

									return contentBox;
								}
							},
						});
					});
				}
			});

			self.loadBackupWindow.show();
		}
		else 
			self.loadBackupWindow.show();

		if (typeof self.loadBackupDataSource !== 'undefined')
			self.loadBackupDataSource.sync();
	},

	ready: function(func) 
	{
		var self = this;
		$(document).ready(function(){
			var template = $($('#list-of-interest-template').html());
			var listOfInterestAddNew = template.find('#list-of-interest-template-add');
			var listOfInterestUpdate = template.find('#list-of-interest-template-update');

			self.dom = self.dom || {
				listOfInterestTemplate: template,
				listOfInterestAddNew: listOfInterestAddNew,
				addLoiButton: listOfInterestAddNew.find('#add-loi-button'),
				loiInputs: listOfInterestAddNew.find('[name^="loi_input_"]'),

				listOfInterestUpdate: listOfInterestUpdate,
				loiUpdateInputs: listOfInterestUpdate.find('[name^="loi_input_update_"]'),
				updateLoiButton: listOfInterestUpdate.find('#update-loi-button'),

				btnPosition: listOfInterestUpdate.find('#loi-position'),

				listOfInterest: $('#list-of-interest-table'),
				frontSubscriptionLang: $('#front-subscription-lang'),

				subscriptionTemplates: $('#subscription-templates-table'),

				tabSubscriptionTemplate: $('#tab_subscription_template'),
				tabSubscriptionTemplateContent: $('#tab_subscription_template_content'),
				subscriptionTemplateLanguage: $('#subscription-template-language'),

				subscriptionTemplateCss: $('#subscription-template-css'),
				subscriptionTemplateCssGlobal: $('#subscription-template-css-global'),
				subscriptionTemplateView: $('#subscription-template-view'),

				saveSubscriptionTemplate: $('#save-subscription-template'),
				saveAsSubscriptionTemplate: $('#save-as-subscription-template'),
				saveSubscriptionTemplateMessage: $('#save-subscription-template-message'),

				// subscription template settings
				displayGender: $('[name="displayGender"]'),
				displayFirstName: $('[name="displayFirstName"]'),
				displayLastName: $('[name="displayLastName"]'),
				displayLanguage: $('[name="displayLanguage"]'),
				displayBirthday: $('[name="displayBirthday"]'),
				displayListOfInterest: $('[name="displayListOfInterest"]'),
				displaySubscribeMessage: $('[name="displaySubscribeMessage"]'),
				listOfInterestType: $('[name="listOfInterestType"]'),
				allowMultipleTimeSubscription: $('[name="allowMultipleTimeSubscription"]'),
				activateTemplate: $('#activate-template'),

				corss: $('#np-cross'),
				corss1: $('#np-cross1'),
				corss2: $('#np-cross2'),
				corss3: $('#np-cross3'),
				corss4: $('#np-cross4'),
				corss5: $('#np-cross5'),

				sliderTemplateWidthType: $('[name="sliderFsTemplateWidth"]'),
				sliderTemplateWidth: $('#slider-fs-template-width'),
				sliderTemplateMaxMinWidth: $('#slider-fs-template-maxmin-width'),
				sliderTemplateTop: $('#slider-fs-template-top'),

				radioFsPercent: $('#radio-fs-percent'),
				radioFsPixels: $('#radio-fs-pixels'),

				voucher: $('.subscription-template-voucher'),

				termsAndConditionsUrl: $('#np-terms-and-conditions'),

				viewInANewWindow: $('#subscription-view-in-a-new-window'),

				voucherAlert: $('#fs-vouchers-alert'),
				voucherAlertBox: $('#voucher-alert-box'),

				showOnPages: $('#show-on-pages'),
				cookieLifetime: $('#cookie-lifetime'),
				startTimer: $('#start-timer'),
				whenToShow: $('#when-shop-popup'),

				cookieLifetimeSeconds: $('#cookie-lifetime-seconds'),

				createSubscriptionTemplateBackup: $('#create-subscription-template-backup'),
				loadSubscriptionTemplateBackup: $('#load-subscription-template-backup'),
			};
			func(self.dom);
		});
	},

}.init(NewsletterPro));