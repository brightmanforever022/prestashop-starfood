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

NewsletterPro.namespace('modules.createCustomField');
NewsletterPro.modules.createCustomField = ({
	dom: null,
	box: null,
	init: function(box) 
	{
		var self = this;
		this.box = box;

		this.ready(function(dom){

			var fieldsDataModel,
				fieldsDataSource,
				valuesListDataModel,
				valuesListDataSource,
				editableTypes = [];

			for (var key in box.dataStorage.get('custom_field.types_editable')) {
				editableTypes.push(Number(key));
			}

			var updateValuesListDataSourceAndSync = function(id, idLang)
			{
				if (typeof valuesListDataSource !== 'undefined')
				{
					valuesListDataSource.transport.read.data = {
						'id': id,
						'id_lang': idLang
					};

					valuesListDataSource.sync();
				}
			};

			self.l = l = box.translations.l(box.translations.modules.createCustomField);
			self.win = win = new gkWindow({
				width: 800,
				height: 600,
				setScrollContent: 540,
				title: l('Create Custom Fields'),
				className: 'np-costum-fields-win',
				show: function(win) {},
				close: function(win) {},
				content: function(win) 
				{
					var tempalte = self.tempalte();

					dom.btnAddNewVariable.on('click', function(){
						var btn = $(this);

						if (Number(btn.data('active')))
						{
							btn.data('active', 0);
							btn.html('<i class="icon icon-plus-square"></i> '+self.l('Add New Variable'));
							dom.fiedAdd.slideUp();
						}
						else
						{
							btn.data('active', 1);
							btn.html('<i class="icon icon-minus-square"></i> '+self.l('Add New Variable'));
							dom.fiedAdd.slideDown();
						}
					});

					dom.btnSaveVariable.on('click', function(){
						var btn = $(this),
							variable_name = dom.inputVariableName.val(),	
							type = dom.inputVariableType.val(),
							required = Number($('[name="np_custom_field_required"]:checked').val());

						box.showAjaxLoader(btn);

						$.postAjax({'submit_custom_field': 'addField', variable_name: variable_name, type: type, required: required}).done(function(response){

							if (!response.success)
								box.alertErrors(response.errors);
							else
							{
								if (typeof fieldsDataSource !== 'undefined')
									fieldsDataSource.sync();
							}

						}).always(function(){
							box.hideAjaxLoader(btn);
						});

					});

					fieldsDataModel = new gk.data.Model({
						id: 'id_newsletter_pro_subscribers_custom_field',
					});

					fieldsDataSource = new gk.data.DataSource({
						pageSize: 6,
						transport: {
							read: {
								url: NewsletterPro.dataStorage.get('ajax_url')+'&submit_custom_field=getFieldsList',
								dataType: 'json',
							},

							destroy: {
								url: NewsletterPro.dataStorage.get('ajax_url')+'&submit_custom_field=deleteField&id',
								type: 'POST',
								dateType: 'json',
								success: function(response, itemData) 
								{
									if(!response.success) 
										box.alertErrors(response.errors);

								},
								error: function(data, itemData) 
								{
									box.alertErrors(l('An error occurred.'));
								},
								complete: function(data, itemData) {},
							},					
						},
						schema: {
							model: fieldsDataModel
						},
						trySteps: 2,
						errors: 
						{
							read: function(xhr, ajaxOptions, thrownError) 
							{
								fieldsDataSource.syncStepAvailableAdd(3000, function(){
									fieldsDataSource.sync();
								});
							},
						},
					});

					dom.variablesList.gkGrid({
						dataSource: fieldsDataSource,
						selectable: false,
						currentPage: 1,
						pageable: true,
						template: 
						{
							variable_name: function(item, value)
							{
								return '{'+value+'}';
							},

							type_name: function(item)
							{
								return item.data.type_name;
							},

							required: function(item, value)
							{
								var div = $('<div>'),
									value = Number(value),
									buttonEnabled = $('\
										<a class="list-action-enable action-enabled" data-enabled="1" href="javascript:{}" title="Enabled">\
											<i class="icon-check"></i>\
											<i class="icon-remove hidden"></i>\
										</a>\
									'),
									buttonDisabled = $('\
										<a class="list-action-enable action-disabled" data-enabled="0" href="javascript:{}" title="Disabled">\
											<i class="icon-check hidden"></i>\
											<i class="icon-remove"></i>\
										</a>\
									');

								if (value)
									div.html(buttonEnabled);
								else
									div.html(buttonDisabled);
								
								var clickFunction = function(event)
								{
									var target = $(this),
										enabled = Number(target.data('enabled')),
										id = item.data.id_newsletter_pro_subscribers_custom_field,
										value = (enabled ? 0 : 1);

									$.postAjax({'submit_custom_field': 'changeFieldRequired', id: id, value: value }).done(function(response){
										if (!response.success)
											box.alertErrors(response.errors);
										else
										{
											if (enabled)
												div.html(buttonDisabled);
											else
												div.html(buttonEnabled);

											buttonEnabled.on('click', clickFunction);
											buttonDisabled.on('click', clickFunction);
										}
									});
								};

								buttonEnabled.on('click', clickFunction);
								buttonDisabled.on('click', clickFunction);

								return div;
							},

							actions: function(item)
							{
								var div = $('<div>');

								var deleteButton = $('#np-delete-custom-field-variables-list')
									.gkButton({
										title: self.l('delete'),
										name: 'np-delete-custom-field-variables-list',
										className: 'btn btn-default btn-margin pull-right',
										click: function(e) 
										{

											if (!confirm(self.l('Are you sure you want to do this action? You will lose all the data collected with this field.')))
												return false;

											item.destroy('success');
										},
										icon: '<i class="icon icon-trash-o"></i> ',
									});

								div.append(deleteButton);

								if (editableTypes.indexOf(Number(item.data.type)) != -1)
								{
									var editButton = $('#np-edit-custom-field-variables-list')
										.gkButton({
											title: self.l('Edit'),
											name: 'np-edit-custom-field-variables-list',
											className: 'btn btn-default btn-margin pull-right',
											click: function(e) 
											{
												box.dataStorage.set('custom_field.current_field_id', Number(item.data.id_newsletter_pro_subscribers_custom_field));

												dom.spanEditVariableName.html('{'+item.data.variable_name+'}')
											},
											icon: '<i class="icon icon-edit"></i> ',
										});

									div.append(editButton);
								}

								return div;
							}
						}
					});
					
					box.dataStorage.on('change', 'custom_field.current_field_id', function(id){
						var idLang = box.dataStorage.getNumber('id_selected_lang');

						if (typeof valuesListDataModel === 'undefined')
						{
							dom.fiedEdit.show();

							valuesListDataModel = new gk.data.Model({
								id: 'key',
							});

							valuesListDataSource = new gk.data.DataSource({
								pageSize: 6,
								transport: {
									read: {
										url: NewsletterPro.dataStorage.get('ajax_url')+'&submit_custom_field=getValuesList',
										dataType: 'json',
										data: {
											'id': id,
											'id_lang': idLang,
										}
									},
								},
								schema: {
									model: valuesListDataModel
								},
								trySteps: 2,
								errors: 
								{
									read: function(xhr, ajaxOptions, thrownError) 
									{
										valuesListDataSource.syncStepAvailableAdd(3000, function(){
											valuesListDataSource.sync();
										});
									},
								},
							});

							dom.valuesList.gkGrid({
								dataSource: valuesListDataSource,
								selectable: false,
								currentPage: 1,
								pageable: true,
								template: 
								{
									value: function(item, value)
									{
										return value;
									},

									actions: function(item, value)
									{
										var div = $('<div>');
										var deleteButton = $('#np-delete-custom-field-value')
											.gkButton({
												title: self.l('delete'),
												name: 'np-delete-custom-field-value',
												className: 'btn btn-default btn-margin pull-right',
												click: function(e) 
												{
													var id = box.dataStorage.getNumber('custom_field.current_field_id'),
														key = item.data.key;
													
													$.postAjax({'submit_custom_field': 'removeValueByKey', id: id, key: key}).done(function(response){
														if (!response.success)
															box.alertErrors(response.errors);
														else
															valuesListDataSource.sync();
													});

												},
												icon: '<i class="icon icon-trash-o btn-margin"></i> ',
											});

										div.append(deleteButton);

										var editButton = $('#np-edit-custom-field-value')
											.gkButton({
												title: self.l('Edit'),
												name: 'np-edit-custom-field-value',
												className: 'btn btn-default btn-margin pull-right',
												click: function(e) 
												{
													dom.btnFieldAdd.html('<i class="icon icon-save"></i> ' + self.l('Update'));
													dom.btnFieldAdd.data('edit', 1);
													dom.btnFieldAdd.data('editKey', item.data.key);

													var id = box.dataStorage.getNumber('custom_field.current_field_id'),
														key = item.data.key;

													$.postAjax({'submit_custom_field': 'getValueByKey', id: id, key: key }).done(function(response){
														if (!response.success)
															box.alertErrors(response.errors);
														else
														{
															var value = response.value;

															$.each(dom.customFieldVariable, function(key, item){
																var input = $(item),
																	inputIdLang = input.data('lang');

																if (value.hasOwnProperty(inputIdLang))
																	input.val(value[inputIdLang]);
															});
														}
													});

												},
												icon: '<i class="icon icon icon-edit btn-margin"></i> ',
											});

										div.append(editButton);

										return div;
									}
								}
							});

							dom.langSelect.on('click', function(){
								idLang = box.dataStorage.getNumber('id_selected_lang');
								id =  box.dataStorage.getNumber('custom_field.current_field_id');
								updateValuesListDataSourceAndSync(id, idLang);
							});

							dom.btnFieldAdd.on('click', function(){
								var btn = $(this),
									isEdit = btn.data('edit'),
									editKey = btn.data('editkey'),
									id =  box.dataStorage.getNumber('custom_field.current_field_id');

								if (isEdit)
								{
									var value = self.getInputsVal(dom.customFieldVariable),
										key = dom.btnFieldAdd.data('editKey');

									$.postAjax({'submit_custom_field': 'updateValue', id: id, key: key, value: value}).done(function(response){
										if (!response.success)
											box.alertErrors(response.errors);
										else
										{
											dom.btnFieldAdd.html('<i class="icon icon-plus-square"></i> ' + self.l('Add'));
											dom.btnFieldAdd.data('edit', 0);
											dom.btnFieldAdd.data('editKey', 0);

											$.each(dom.customFieldVariable, function(key, item){
												$(item).val('');
											});

											idLang = box.dataStorage.getNumber('id_selected_lang');
											updateValuesListDataSourceAndSync(id, idLang);
										}
									});
								}
								else
								{
									var value = self.getInputsVal(dom.customFieldVariable);

									$.postAjax({'submit_custom_field': 'addValue', id: id, value: value}).done(function(response){
										if (!response.success)
											box.alertErrors(response.errors);
										else
										{
											idLang = box.dataStorage.getNumber('id_selected_lang');
											updateValuesListDataSourceAndSync(id, idLang);
										}
									});
								}

							});

						} // end of undefined

						updateValuesListDataSourceAndSync(id, idLang);
					});

					return tempalte;
				}
			});

			dom.btnOpenWindow.on('click', function(){
				win.show();
			});

			dom.btnDisplayNewColumns.on('click', function(){
				win.hide();
				box.modules.sendNewsletters.vars.winDisplayCustomColumns.show()
			});

		});
	},

	getInputsVal: function(selector)
	{
		var result = {};
		$.each(selector, function(key, item){
			var matchLang = $(item).attr('name').match(/\d+$/),
				idLang = matchLang !== null ? matchLang[0] : 0;

			result[idLang] = $(item).val();
		});
		return result;
	},

	tempalte: function()
	{
		var l = this.l,
			box = this.box,
			self = this,
			types = box.dataStorage.get('custom_field.types'),
			all_active_languages = box.dataStorage.get('all_active_languages');

		var displayTypeOptions = '',
			displayAllActivaLanguagesValue = '';

		for (var key in types) {
			displayTypeOptions += '<option value="'+key+'">'+types[key]+'</option>';
		}

		for (var key in all_active_languages) {
			var lang = all_active_languages[key];

			displayAllActivaLanguagesValue += '<input data-lang="'+lang.id_lang+'" name="np_custom_field_variable_input_'+lang.id_lang+'" type="text" class="form-control fixed-width-xxl pull-left" style="'+(lang.selected ? 'display: block;' : 'display: none;')+'">';
		}

		var tempalte = $('\
			<div id="np-create-custom-field-content" class="np-create-custom-field-content">\
				<div class="form-group clearfix">\
					<a href="javascript:{}" id="np-add-new-variable" class="btn btn-default pull-right" date-active="0">\
						<i class="icon icon-plus-square"></i> '+self.l('Add New Variable')+'\
					</a>\
				</div>\
				<div id="np-custom-field-add" class="clearfix" style="display: none">\
					<hr style="margin-top: 0">\
					<div class="form-group clearfix">\
						<label class="control-label col-sm-3"><span class="label-tooltip">'+self.l('Variable Name')+'</span></label>\
						<div class="form-group col-sm-9">\
							<input id="np-create-custom-field-variable-name" type="text" class="form-control fixed-width-xxl pull-left">\
						</div>\
					</div>\
					<div class="form-group clearfix">\
						<label class="control-label col-sm-3"><span class="label-tooltip">'+self.l('Type')+'</span></label>\
						<div class="form-group col-sm-9">\
							<select class="form-control fixed-width-xxl pull-left" id="np-create-custom-field-variable-type">\
								<option value="0">- '+self.l('none')+' -</option>\
								'+displayTypeOptions+'\
							</select>\
						</div>\
					</div>\
					<div class="form-group clearfix">\
						<label class="control-label col-sm-3"><span class="label-tooltip">'+l('Required')+'</span></label>\
						<div class="col-sm-9">\
							<div class="fixed-width-l clearfix">\
								<div class="col-sm-3">\
									<div class="row">\
										<span class="switch prestashop-switch">\
											<input id="np_custom_field_required_yes" type="radio" name="np_custom_field_required" value="1">\
											<label for="np_custom_field_required_yes">\
												'+l('Yes')+'\
											</label>\
											<input id="np_custom_field_required_no" type="radio" name="np_custom_field_required" value="0" checked="checked">\
											<label for="np_custom_field_required_no">\
												'+l('No')+'\
											</label>\
											<a class="slide-button btn"></a>\
										</span>\
									</div>\
								</div>\
							</div>\
						</div>\
					</div>\
					<div class="form-group clearfix">\
						<div class="col-sm-offset-3 com-sm-9">\
							<a id="np-create-custom-field-save-variable" href="javascript:{}" class="btn btn-success pull-left">\
								<span class="btn-ajax-loader"></span>\
								<i class="icon icon-save"></i>\
								'+self.l('Save Variable')+'\
							</a>\
						</div>\
					</div>\
					<hr style="margin-top: 0">\
				</div>\
				<div class="form-group clearfix">\
					<table id="np-custom-field-variables-list" class="table table-bordered np-custom-field-variables-list">\
						<thead>\
							<tr>\
								<th class="variable-name" data-field="variable_name">'+l('Variable Name')+'</th>\
								<th class="type-name" data-template="type_name">'+l('Type')+'</th>\
								<th class="required" data-field="required">'+l('Required')+'</th>\
								<th class="np-actions" data-template="actions">'+l('Actions')+'</th>\
							</tr>\
						</thead>\
					</table>\
				</div>\
				<div class="form-group clearfix">\
					<label class="control-label col-sm-3"><span class="label-tooltip">'+self.l('List Columns')+'</span></label>\
					<div class="col-sm-9">\
						<div class="form-group clearfix">\
							<a href="javascript:{}" id="np-display-new-columns-custom-field" class="btn btn-default pull-left"><i class="icon icon-eye"></i> '+self.l('Display Custom Columns')+'</a>\
						</div>\
						<p class="help-block">'+self.l('Display a new column for the created fields on the list Visitors Subscribed (at the Newsletter Pro module).')+'</p>\
					</div>\
				</div>\
				<div id="np-custom-field-edit" class="form-group clearfix" style="display: none">\
					<h4 class="np-win-h4">'+l('Edit Variable')+' <span id="np-custom-field-edit-variable-name"></span></h4>\
					<div class="form-group clearfix">\
						<label class="control-label col-sm-3"><span class="label-tooltip">'+l('Value')+'</span></label>\
						<div class="col-sm-9" style="padding-right: 0">\
							<div class="form-inline">\
								<div class="form-group">\
									'+displayAllActivaLanguagesValue+'\
								</div>\
								<div class="form-group">\
									<div id="np-custom-field-value-lang" class="pull-right np-custom-field-value-lang gk_lang_select pull-left"></div>\
								</div>\
								<div class="form-group pull-right">\
									<a id="np-btn-custom-field-add" data-edit="0" data-editkey="0" href="javascript:{}" class="btn btn-default">\
										<i class="icon icon-plus-square"></i>\
										'+l('Add')+'\
									</a>\
								</div>\
							</div>\
						</div>\
					</div>\
					<div class="form-group clearfix">\
						<table id="np-custom-field-values-list" class="table table-bordered np-custom-field-values-list">\
							<thead>\
								<tr>\
									<th class="np-value" data-field="value">'+l('Value')+'</th>\
									<th class="np-actions" data-template="actions">'+l('Actions')+'</th>\
								</tr>\
							</thead>\
						</table>\
					</div>\
				</div>\
			</div>\
		');

		$.extend(this.dom, {
			winTemplate: tempalte,
			btnAddNewVariable: tempalte.find('#np-add-new-variable'),
			fiedAdd: tempalte.find('#np-custom-field-add'),
			fiedEdit: tempalte.find('#np-custom-field-edit'),
			valuesList: tempalte.find('#np-custom-field-values-list'),
			variablesList: tempalte.find('#np-custom-field-variables-list'),
			inputVariableName: tempalte.find('#np-create-custom-field-variable-name'),
			inputVariableType: tempalte.find('#np-create-custom-field-variable-type'),
			btnSaveVariable: tempalte.find('#np-create-custom-field-save-variable'),
			spanEditVariableName: tempalte.find('#np-custom-field-edit-variable-name'),
			langSelect: tempalte.find('#np-custom-field-value-lang'),
			btnFieldAdd: tempalte.find('#np-btn-custom-field-add'),
			customFieldVariable: tempalte.find('[name^="np_custom_field_variable_input_"]'),
			btnDisplayNewColumns: tempalte.find('#np-display-new-columns-custom-field'),
		});

		return tempalte;
	},

	ready: function(func) 
	{
		var self = this;

		$(document).ready(function(){
			self.dom = {
				btnOpenWindow: $('#np-create-custom-field')
			};

			func(self.dom);
		});
	}
}.init(NewsletterPro));