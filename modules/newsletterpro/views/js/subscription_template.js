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

NewsletterPro.namespace('components.SubscriptionTemplate');
NewsletterPro.components.SubscriptionTemplate = function SubscriptionTemplate(cfg)
{
	if (!(this instanceof SubscriptionTemplate))
		return new SubscriptionTemplate(cfg);


	var box = NewsletterPro;
	var l = box.translations.l(box.translations.components.SubscriptionTemplate);
	var self = this;

	// fix languge problems
	box.components.LanguageSelect.updateLanguages();

	var id                                    = cfg.id,
		tiny                                  = cfg.tiny(),
		tinySubscribeMessage                  = cfg.tinySubscribeMessage(),
		tinyEmailSubscribeVoucherMessage      = cfg.tinyEmailSubscribeVoucherMessage(),
		tinyEmailSubscribeConfirmationMessage = cfg.tinyEmailSubscribeConfirmationMessage(),
		dom = {
			cssStyle         : cfg.cssStyle,
			cssGlobalStyle   : cfg.cssGlobalStyle,
			view			 : cfg.view,
			viewInANewWindow : cfg.viewInANewWindow,
			settings         : cfg.settings,
			sliders          : cfg.sliders,
			saveBtn          : cfg.saveBtn,
			saveAsBtn        : cfg.saveAsBtn,
			saveMessage      : cfg.saveMessage,
		},
		// callbacks
		callbackOnSave              = cfg.onSave,
		callbackAfterSave           = cfg.afterSave,
		callbackAfterSaveAs         = cfg.afterSaveAs,
		callbackOnChange            = cfg.onChange,
		callbackOnSetContent        = cfg.onSetContent,
		callbackOnSettingsChange    = cfg.onSettingsChange,
		callbackAfterSettingsChange = cfg.afterSettingsChange,
		callbackOnTemplateChange    = cfg.onTemplateChange,
		errors                      = [];

	for (var key in cfg.dom) {
		dom[key] = cfg.dom[key];
	}

	var fs = NewsletterPro.modules.frontSubscription;

	// add events
	var settingsOnChange = {
		displayGender: dom.settings.displayGender,
		displayFirstName: dom.settings.displayFirstName,
		displayLastName: dom.settings.displayLastName,
		displayLanguage: dom.settings.displayLanguage,
		displayBirthday: dom.settings.displayBirthday,
		displayListOfInterest: dom.settings.displayListOfInterest,
		displaySubscribeMessage: dom.settings.displaySubscribeMessage,
		listOfInterestType: dom.settings.listOfInterestType,
		allowMultipleTimeSubscription: dom.settings.allowMultipleTimeSubscription,

	};

	$.each(settingsOnChange, function(key, item){
		item.on('change', function(event){
			var target = $(event.currentTarget);
			var bool = parseInt(target.val());

			switch(key)
			{
				case 'displayGender':
					saveDisplayGender(bool);
					break;

				case 'displayFirstName':
					saveDisplayFirstName(bool);
					break;

				case 'displayLastName':
					saveDisplayLastName(bool);
					break;

				case 'displayLanguage':
					saveDisplayLanguage(bool);
					break;

				case 'displayBirthday':
					saveDisplayBirthday(bool);
					break;

				case 'displayListOfInterest':
					saveDisplayListOfInterest(bool);
					break;

				case 'displaySubscribeMessage':
					saveDisplaySubscribeMessage(bool);
					break;

				case 'listOfInterestType':
					saveListOfInterestType(bool);
					break;

				case 'allowMultipleTimeSubscription':
					saveAllowMultipleTimeSubscription(bool);
					break;

				default:
					break;
			}
		});
	});

	dom.settings.showOnPages.on('change', function(){
		self.setSetting('show_on_pages', $(this).val());
	});

	dom.settings.cookieLifetime.on('change', function(){
		var value = $(this).val();
		var mathString = value.replace(/[^\d\/\*\+\-\.\,]+/g, ' ');
		var val = value;

		try
		{
			val = eval(mathString);
			if (val === 'undefined')
				val = 366;
		}
		catch(e)
		{
			val = 366;
		}

		var floatString = parseFloat(val).toFixed(10).replace(/.0+$/, '');
		var endValue = parseFloat(floatString);

		dom.settings.cookieLifetimeSeconds.html(Math.round(endValue * 60 * 60 * 24));

		$(this).val(endValue);

		self.setSetting('cookie_lifetime', parseFloat(endValue));
	});

	dom.settings.startTimer.on('change', function(){
		self.setSetting('start_timer', $(this).val());
	});

	dom.settings.whenToShow.on('change', function(){
		self.setSetting('when_to_show',  $(this).val());
	});

	dom.settings.termsAndConditionsUrl.on('change', function(){
		self.setSetting('terms_and_conditions_url',  $(this).val());
	});

	dom.settings.activateTemplate.on('click', function(){
		saveActivateTemplate(1);
	});

	dom.saveBtn.on('click', function(event){
		return save();
	});

	dom.saveAsBtn.on('click', function(event){
		return saveAs();
	});

	// set the selected mandatory fields
	var mandatoryFields = box.dataStorage.get('mandatory_fields');

	if (mandatoryFields.indexOf('firstname') > -1) {
		dom.firstNameMandatory.prop('checked', true);
	} else {
		dom.firstNameMandatory.prop('checked', false);
	}

	if (mandatoryFields.indexOf('lastname') > -1) {
		dom.lastNameMandatory.prop('checked', true);
	} else {
		dom.lastNameMandatory.prop('checked', false);
	}

	var setMandatoryFields = function(fieldName, checked)
	{	
		$.postAjax({'submitSubscriptionController': 'ajaxSetMandatory', id_template: id, field: fieldName, checked: Number(checked)}).done(function(response){
			if (!response.status)
				box.alertErrors(response.errors);
		});
	};

	dom.firstNameMandatory.on('change', function(){
		setMandatoryFields('firstname', $(this).is(':checked'));
	});

	dom.lastNameMandatory.on('change', function(){
		setMandatoryFields('lastname', $(this).is(':checked'));
	});

	// setup globals var
	this.id   = id;
	this.dom  = dom;
	this.tiny = tiny;

	this.setTemplateById = function(idTemplate)
	{
		return setTemplateById(idTemplate);
	};

	this.getContent = function()
	{
		return getContent();
	};

	this.setContent = function(name, value)
	{
		return setContent(name, value);
	};

	this.save = function()
	{
		return save();
	};

	this.saveAs = function(name)
	{
		return saveAs();
	};

	this.setView = function(url)
	{
		return setView(url);
	};

	this.setSettings = function(template)
	{
		return setSettings(template);
	};

	this.setSetting = function(settingsDbField, width)
	{
		return $.postAjax({'submitSubscriptionController': 'ajaxSetSetting', id_template: id, field: settingsDbField, value: width}).done(function(response){
			if (!response.status)
				box.alertErrors(response.errors);
		});
	};

	this.setSliderConfiguration = function(name, slider)
	{
		dom.sliders[name] = slider;
	};

	function getContent()
	{
		return {
			tiny: getTinyContent(),

			tinySubscribeMessage: getTinySubscribeMessageContent(),
			tinyEmailSubscribeVoucherMessage: getTinyEmailSubscribeVoucherMessageContent(),
			tinyEmailSubscribeConfirmationMessage: getTinyEmailSubscribeConfirmationMessageContent(),

			css_style: getCssStyleContent(),
			css_style_global: getCssGlobalStyleContent(),
		};
	}

	function setContent(name, value)
	{
		if (typeof callbackOnChange === 'function')
			callbackOnChange(self);

		if (typeof callbackOnSetContent === 'function')
			callbackOnSetContent(self, name, value);
	}

	function save()
	{
		if (typeof callbackOnSave === 'function')
			callbackOnSave(self);

		return $.postAjax({'submitSubscriptionController': 'ajaxSaveTemplate', id_template: id, value: getContent(), id_lang: box.dataStorage.get('id_selected_lang')}).done(function(response){
			if (typeof callbackAfterSave === 'function')
				callbackAfterSave(self, response);

			if (!response.status)
				box.alertErrors(response.errors);
			else
			{
				writeMessage(true, [response.message]);
				setView(response.view);
				refreshTinyCss();
			}
		});
	}

	function saveAs()
	{
		if (typeof callbackOnSave === 'function')
			callbackOnSave(self);

		var content = {
			content: getContent(),
			settings: getSettings(),
			sliders: getSlidersValues(),
		};

		var templateName = prompt(l('insert the template name'));
		if ( $.trim(templateName) == '' || templateName == null )
			return false;

		$.postAjax({'submitSubscriptionController': 'ajaxSaveAsTemplate', name: templateName, value: content, id_lang: box.dataStorage.get('id_selected_lang')}).done(function(response){
			if (typeof callbackAfterSave === 'function')
				callbackAfterSave(self, response);

			if (typeof callbackAfterSave === 'function')
				callbackAfterSaveAs(self, response);

			if (!response.status)
				box.alertErrors(response.errors);
			else
			{
				setDataStorage({
					'id': response.id_template,
					'name': response.name,
				});

				writeMessage(true, [response.message]);
				setView(response.view);
				refreshTinyCss();
			}
		});
	}

	function getSlidersValues()
	{
		var currentType = box.dataStorage.get('subscription_template_current_type');
		var bodyWidth = dom.sliders.sliderTemplateWidth.getValue();

		if (currentType == fs.VALUE_PERCENT)
			bodyWidth = String(bodyWidth + '%');

		return {
			body_width: bodyWidth,
			body_min_width: dom.sliders.sliderTemplateMaxMinWidth.getValueMin(),
			body_max_width: dom.sliders.sliderTemplateMaxMinWidth.getValueMax(),
			body_top: dom.sliders.sliderTemplateTop.getValue(),
		};
	}

	function changeMandatoryFields(mandatoryFields)
	{
		box.dataStorage.get('mandatory_fields', mandatoryFields);

		if (mandatoryFields.indexOf('firstname') > -1) {
			dom.firstNameMandatory.prop('checked', true);
		} else {
			dom.firstNameMandatory.prop('checked', false);
		}

		if (mandatoryFields.indexOf('lastname') > -1) {
			dom.lastNameMandatory.prop('checked', true);
		} else {
			dom.lastNameMandatory.prop('checked', false);
		}
	}

	function setTemplateById(idTemplate)
	{
		// the language problem appear on the view
		var idLang = box.dataStorage.get('id_selected_lang');
		$.postAjax({'submitSubscriptionController': 'ajaxSetTemplateById', id : idTemplate, 'id_lang': idLang}).done(function(response){
			if (response.status)
			{
				// reset errors
				errors = [];
				var template = response.template;

				id = template.id;

				setDataStorage({'id': template.id, 'name': template.name});

				setTinyContent(template.content);

				setTinySubscribeMessageContent(template.subscribe_message);
				setTinyEmailSubscribeVoucherMessageContent(template.email_subscribe_voucher_message);
				setTinyEmailSubscribeConfirmationMessageContent(template.email_subscribe_confirmation_message);

				setCssStyle(template.css_style);
				setCssGlobalStyle(template.css_style_global);
				setView(template.view);

				setDisplayGender(template.display_gender);
				setDisplayFirstname(template.display_firstname);
				setDisplayLastname(template.display_lastname);
				setDisplayLanguage(template.display_language);
				setDisplayBirthday(template.display_birthday);
				setDisplayListOfInterest(template.display_list_of_interest);
				setDisplaySubscribeMessage(template.display_subscribe_message);
				setListOfInterestType(template.list_of_interest_type);
				setAllowMultipleTimeSubscription(template.allow_multiple_time_subscription);
				setVoucher(template.voucher);

				changeMandatoryFields(template.mandatory_fields);

				setInput(dom.settings.termsAndConditionsUrl, template.terms_and_conditions_url);
				setInput(dom.settings.showOnPages, template.show_on_pages);
				setInput(dom.settings.cookieLifetime, template.cookie_lifetime);
				setInput(dom.settings.startTimer, template.start_timer);
				setInput(dom.settings.whenToShow, template.when_to_show);

				setActivateTemplate(template.active);

				setSliderValueWidth(dom.sliders.sliderTemplateWidth, template.body_width);
				setSliderRangeValue(dom.sliders.sliderTemplateMaxMinWidth, template.body_min_width, template.body_max_width );
				setSliderTopValue(dom.sliders.sliderTemplateTop, template.body_top);

				if (typeof callbackOnTemplateChange === 'function')
					callbackOnTemplateChange(self);

				if (errors.length > 0)
					box.alertErrors(errors);
			}
			else
				box.alertErrors(response.errors);
		});
	}

	function setInput(elem, value)
	{
		elem.val(value);
	}

	function setSliderTopValue(slider, value)
	{
		fs.setStorageBodyInfo('body_top', value);
		slider.setValue(parseInt(value));
	}

	function setSliderValueWidth(slider, value)
	{
		box.dataStorage.set('subscription_template_current_type', fs.getBodyWidthType(value));

		var currentType = box.dataStorage.get('subscription_template_current_type');

		fs.setStorageBodyInfo('body_width', value);
		fs.setSliderTemplateWidth( gkSlider(fs.getSliderTemplateWidthConfig(currentType)) );

		fs.setCheckboxType(currentType);

		slider.setValue(parseInt(value));
	}

	function setSliderRangeValue(slider, min, max)
	{
		var min = parseInt(min),
		 	max = parseInt(max);

		fs.setStorageBodyInfo('body_min_width', min);
		fs.setStorageBodyInfo('body_max_width', max);

		slider.setValueMin(min);
		slider.setValueMax(max);
	}

	// function setSettings(template)
	// {
	// 	setDisplayGender(template.display_gender);
	// 	setDisplayFirstname(template.display_firstname);
	// 	setDisplayLastname(template.display_lastname);
	// 	setDisplayLanguage(template.display_language);
	// 	setDisplayBirthday(template.display_birthday);
	// 	setDisplayListOfInterest(template.display_list_of_interest);
	// 	setDisplaySubscribeMessage(template.display_subscribe_message);
	// 	setListOfInterestType(template.list_of_interest_type);
	// 	setActivateTemplate(template.active);
	// 	setVoucher(template.voucher);

	// 	setInput(dom.settings.termsAndConditionsUrl, template.terms_and_conditions_url);
	// 	setInput(dom.settings.showOnPages, template.show_on_pages);
	// 	setInput(dom.settings.cookieLifetime, template.cookie_lifetime);
	// 	setInput(dom.settings.startTimer, template.start_timer);
	// 	setInput(dom.settings.whenToShow, template.when_to_show);
	// }

	function setDataStorage(obj)
	{
		box.dataStorage.add('subscription_template_id', obj.id);
		box.dataStorage.add('subscription_template', obj.name);
	}

	function setTinyContent(objLang)
	{
		parseTiny(function(idLang, ed){
			if (typeof objLang[idLang] !== 'undefined')
			{
				var value = (typeof objLang[idLang] === 'string' ? objLang[idLang] : '');
				ed.setContent(value);
			}
			else
				error.push(l('error on set tiny content'));
		});
	}

	function setTinySubscribeMessageContent(objLang)
	{
		parseTinySubscribeMessage(function(idLang, ed){
			if (typeof objLang[idLang] !== 'undefined')
			{
				var value = (typeof objLang[idLang] === 'string' ? objLang[idLang] : '');
				ed.setContent(value);
			}
			else
				error.push(l('error on set tiny content'));
		});
	}

	function setTinyEmailSubscribeVoucherMessageContent(objLang)
	{
		parseTinyEmailSubscribeVoucherMessage(function(idLang, ed){
			if (typeof objLang[idLang] !== 'undefined')
			{
				var value = (typeof objLang[idLang] === 'string' ? objLang[idLang] : '');
				ed.setContent(value);
			}
			else
				error.push(l('error on set tiny content'));
		});
	}

	function setTinyEmailSubscribeConfirmationMessageContent(objLang)
	{
		parseTinyEmailSubscribeConfirmationMessage(function(idLang, ed){
			if (typeof objLang[idLang] !== 'undefined')
			{
				var value = (typeof objLang[idLang] === 'string' ? objLang[idLang] : '');
				ed.setContent(value);
			}
			else
				error.push(l('error on set tiny content'));
		});
	}

	function setCssStyle(value)
	{
		dom.cssStyle.val(value);
	}

	function setCssGlobalStyle(value)
	{
		dom.cssGlobalStyle.val(value);
	}

	function setView(url)
	{
		dom.view.attr('src', url);
		dom.viewInANewWindow.attr('href', url);

	}

	function setDisplayGender(bool)
	{
		setSettingsItems('displayGender', Boolean(bool));
	}

	function setDisplayFirstname(bool)
	{
		setSettingsItems('displayFirstName', Boolean(bool));
	}

	function setDisplayLastname(bool)
	{
		setSettingsItems('displayLastName', Boolean(bool));
	}

	function setDisplayLanguage(bool)
	{
		setSettingsItems('displayLanguage', Boolean(bool));
	}

	function setDisplayBirthday(bool)
	{
		setSettingsItems('displayBirthday', Boolean(bool));
	}

	function setDisplayListOfInterest(bool)
	{
		setSettingsItems('displayListOfInterest', Boolean(bool));
	}

	function setDisplaySubscribeMessage(bool)
	{
		setSettingsItems('displaySubscribeMessage', Boolean(bool));
	}

	function setListOfInterestType(bool)
	{
		setSettingsItems('listOfInterestType', Boolean(bool));
	}
	
	function setAllowMultipleTimeSubscription(bool)
	{
		setSettingsItems('allowMultipleTimeSubscription', Boolean(bool));
	}

	function setActivateTemplate(bool)
	{
		if (Boolean(bool))
			dom.settings.activateTemplate.parent().hide();
		else
			dom.settings.activateTemplate.parent().show();
	}

	function setVoucher(value)
	{
		dom.settings.voucher.val(value);
	}

	function parseTiny(func)
	{
		$.each(tiny, function(i, item){
			func(i, item);
		});
	}

	function parseTinySubscribeMessage(func)
	{
		$.each(tinySubscribeMessage, function(i, item){
			func(i, item);
		});
	}

	function parseTinyEmailSubscribeVoucherMessage(func)
	{
		$.each(tinyEmailSubscribeVoucherMessage, function(i, item){
			func(i, item);
		});
	}

	function parseTinyEmailSubscribeConfirmationMessage(func)
	{
		$.each(tinyEmailSubscribeConfirmationMessage, function(i, item){
			func(i, item);
		});
	}

	function getTinyContent()
	{
		var tinyContent = {};
		parseTiny(function(idLang, ed){
			tinyContent[idLang] = ed.getContent();
		});
		return tinyContent;
	}

	function getTinySubscribeMessageContent()
	{
		var tinyContent = {};
		parseTinySubscribeMessage(function(idLang, ed){
			tinyContent[idLang] = ed.getContent();
		});
		return tinyContent;
	}

	function getTinyEmailSubscribeVoucherMessageContent()
	{
		var tinyContent = {};
		parseTinyEmailSubscribeVoucherMessage(function(idLang, ed){
			tinyContent[idLang] = ed.getContent();
		});
		return tinyContent;
	}

	function getTinyEmailSubscribeConfirmationMessageContent()
	{
		var tinyContent = {};
		parseTinyEmailSubscribeConfirmationMessage(function(idLang, ed){
			tinyContent[idLang] = ed.getContent();
		});
		return tinyContent;
	}

	function refreshTinyCss()
	{
		parseTiny(function(idLang, ed){
			ed.refreshStyle();
		});

		parseTinySubscribeMessage(function(idLang, ed){
			ed.refreshStyle();
		});

		parseTinyEmailSubscribeVoucherMessage(function(idLang, ed){
			ed.refreshStyle();
		});

		parseTinyEmailSubscribeConfirmationMessage(function(idLang, ed){
			ed.refreshStyle();
		});
	}

	function getCssStyleContent()
	{
		return dom.cssStyle.val();
	}

	function getCssGlobalStyleContent()
	{
		return dom.cssGlobalStyle.val();
	}

	function getSettingsItems(name)
	{
		var obj = {
			yes: null,
			no: null,
		};
		$.each(dom.settings[name], function(i, item){
			item = $(item);
			var val = parseInt(item.val());
			if (val)
				obj['yes'] = item;
			else
				obj['no'] = item;
		});
		return obj;
	}

	function setSettingsItems(name, bool)
	{
		var obj = getSettingsItems(name);
		if (bool)
		{
			obj.yes.prop('checked', true);
			obj.yes.attr('checked', 'checked');
			obj.no.prop('checked', false);
			obj.no.removeAttr('checked');
		}
		else
		{
			obj.no.prop('checked', true);
			obj.no.prop('checked', 'checked');
			obj.yes.prop('checked', false);
			obj.yes.removeAttr('checked');
		}
	}

	function saveDisplayGender(bool)
	{
		changeSetting('displayGender', 'saveDisplayGender', bool);
	}

	function saveDisplayFirstName(bool)
	{
		changeSetting('displayFirstName', 'saveDisplayFirstName', bool);
	}

	function saveDisplayLastName(bool)
	{
		changeSetting('displayLastName', 'saveDisplayLastName', bool);
	}

	function saveDisplayLanguage(bool)
	{
		changeSetting('displayLanguage', 'saveDisplayLanguage', bool);
	}

	function saveDisplayBirthday(bool)
	{
		changeSetting('displayBirthday', 'saveDisplayBirthday', bool);
	}

	function saveDisplayListOfInterest(bool)
	{
		changeSetting('displayListOfInterest', 'saveDisplayListOfInterest', bool);
	}

	function saveDisplaySubscribeMessage(bool)
	{
		changeSetting('displaySubscribeMessage', 'saveDisplaySubscribeMessage', bool);
	}

	function saveListOfInterestType(bool)
	{
		changeSetting('listOfInterestType', 'saveListOfInterestType', bool);
	}

	function saveAllowMultipleTimeSubscription(bool)
	{
		changeSetting('allowMultipleTimeSubscription', 'saveAllowMultipleTimeSubscription', bool);
	}

	function saveActivateTemplate(bool)
	{
		changeSetting('activateTemplate', 'saveActivateTemplate', bool).done(function(response){
			setActivateTemplate(bool);
		});
	}

	function changeSetting(settingsName, submitName, bool)
	{
		if (typeof callbackOnSettingsChange === 'function')
			callbackOnSettingsChange(self, bool);

		return $.postAjax({'submitSubscriptionController': submitName, id_template: id, value: bool}).done(function(response){

			if (!response.status)
				bool.alertErrors(response.errors);

			if (typeof callbackAfterSettingsChange === 'function')
				callbackAfterSettingsChange(self, settingsName, response, bool);
		});
	}

	function getSettings()
	{
		var settings = {	
			display_gender: getSettingsValue(dom.settings.displayGender),
			display_firstname: getSettingsValue(dom.settings.displayFirstName),
			display_lastname: getSettingsValue(dom.settings.displayLastName),
			display_language: getSettingsValue(dom.settings.displayLanguage),
			display_birthday: getSettingsValue(dom.settings.displayBirthday),
			display_list_of_interest: getSettingsValue(dom.settings.displayListOfInterest),
			display_subscribe_message: getSettingsValue(dom.settings.displaySubscribeMessage),
			list_of_interest_type: getSettingsValue(dom.settings.listOfInterestType),
			allow_multiple_time_subscription: getSettingsValue(dom.settings.allowMultipleTimeSubscription),

			voucher: dom.settings.voucher.val(),
			show_on_pages: dom.settings.showOnPages.val(),
			when_to_show: dom.settings.whenToShow.val(),
			start_timer: dom.settings.startTimer.val(),
			cookie_lifetime: dom.settings.cookieLifetime.val(),
		};

		return settings;
	}

	function getSettingsValue(obj)
	{
		var selected = $.grep(obj, function(item){
			return (parseInt($(item).val()) == 1 && $(item).prop('checked') == true);
		});

		if (selected.length > 0)
			return 1;
		return 0;
	}

	function writeMessage(type, array)
	{
		var success = function()
		{
			dom.saveMessage.show().html('<p class="success-save">'+array.join('<br>')+'</p>')
			setTimeout(function(){
				dom.saveMessage.hide();
			}, 5000);
		};

		var error = function()
		{
			dom.saveMessage.show().html('<p class="error-save">'+array.join('<br>')+'</p>')
			setTimeout(function(){
				dom.saveMessage.hide();
			}, 5000);
		};

		switch(type)
		{
			case true:
				success();
			break;

			case false:
				errors();
			break;
		}
	}
};