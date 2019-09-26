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

NewsletterPro.namespace('components.NewsletterTemplate');
NewsletterPro.components.NewsletterTemplate = function NewsletterTemplate(cfg)
{
	if (!(this instanceof NewsletterTemplate))
		return new NewsletterTemplate(cfg);

	var self = this,
		box = NewsletterPro,
		langLength = box.dataStorage.get('all_languages').length;

	box.extendSubscribeFeature(this);

	this.l = NewsletterPro.translations.l(box.translations.components.NewsletterTemplate);
	this.cfg = cfg;
	this.createTemplate = box.modules.createTemplate;
	this.dom = {};
	this.tiny = [];
	this.tinyLangIndex = [];
	this.viewTabActive = false;
	this.view = {};
	this.view.products = [];
	this.template = {
		container: null,
		content: null,
		products: {},
	};

	this.triggerTitleChange = true;

	box.onObject.setCallback('tinyNewsletter', function(ed){
		self.add(ed);

		if (self.tiny.length == langLength)
		{
			$(document).ready(function(){

				self.createTemplate.tinyInitDfd.resolve();

				self.dom = {
					viewNewsletterTemplate: $('#view-newsletter-template-content'),
					saveTemplate: $('#save-newsletter-template'),
					saveAsTemplate: $('#save-as-newsletter-template'),
					exportHTML: $('#export-html'),
					inputImportHTML: $('#inputImportHTML'),
					inputImportHTMLForm: $('#inputImportHTMLForm'),
					templateStyle: $('#template-css-style'),
					templateGlobalStyle: $('#template-css'),
					templateTitle: $('[id^="page-title-"]'),
					templateHeader: $('[id^="template-header-"]'),
					templateFooter: $('[id^="template-footer-"]'),
					saveMessage: $('#save-newsletter-template-message'),
					langSelect: $('#newsletter-template-lang-select'),
					pageTitleMessage: $('#page-title-message'),
					vewInBrowser: $('#np-view-newsletter-template-in-browser'),
					color: {
						templateContainerColorObj: new jscolor.color(document.getElementById('template-container-color')),
						templateContentColorObj: new jscolor.color(document.getElementById('template-content-color')),
						productsBgColorObj: new jscolor.color(document.getElementById('products-bg-color')),
						productsNameColorObj: new jscolor.color(document.getElementById('products-name-color')),
						productsSDescriptionColorObj: new jscolor.color(document.getElementById('products-s-description-color')),
						productsDescriptionColorObj: new jscolor.color(document.getElementById('products-description-color')),
						productsPriceColorObj: new jscolor.color(document.getElementById('products-price-color')),
						linksColorObj: new jscolor.color(document.getElementById('links-color')),
						productsBorderColorObj: new jscolor.color(document.getElementById('products-border-color')),
					},
				};

				self.ready(self.dom);

			});
		}
	});
};

NewsletterPro.components.NewsletterTemplate.prototype.add = function(ed)
{
	var idLang = parseInt(ed.id.match(/\d+$/));
	this.tiny.push({
		id: ed.id,
		idLang: idLang,
		ed: ed,
	});

	this.tinyLangIndex.push(idLang);
};

NewsletterPro.components.NewsletterTemplate.prototype.ready = function(dom)
{
	var self = this,
		box = NewsletterPro,
		languageSwitcher = box.components.LanguageSelect.getInstanceById('#newsletter-template-lang-select');

	try {
		self.updateTemplate();
	} catch (e) {

	}

	self.parseTiny(function(id, idLang, ed){
		if (box.isTinyHigherVersion())
		{
			ed.on('change', function(ed, l){
				// call this to verify if the porducts exists into template
				self.updateBoth();
			});
		}
		else
		{
			ed.onChange.add(function(ed, l){
				// call this to verify if the porducts exists into template
				self.updateBoth();
			});
		}
	});

	try {
		var txtAreas = [dom.templateStyle, dom.templateGlobalStyle, dom.templateHeader, dom.templateFooter];

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

	// fix languge problems
	box.components.LanguageSelect.updateLanguages();

	this.subscribe('save', function(data){

	});

	this.subscribe('afterSave', function(data){

	});

	var getViewInBrowserUrl = function(idLang){
		idLang = idLang || NewsletterPro.dataStorage.get('id_selected_lang');

		return box.getUrl({
				'submit_template_controller': 'viewTemplate',
				'name': box.dataStorage.get('configuration.NEWSLETTER_TEMPLATE'),
				'id_lang': idLang,
		});
	};

	dom.vewInBrowser.on('click', function(){
		event.preventDefault();

		$(this).attr('href', getViewInBrowserUrl());

		window.open($(this).attr('href'),'_blank');
	});

	// console.log('RAMSASSSSSSS');
/*
	languageSwitcher.subscribe('change', function(obj){

		var lang = obj.lang,
			idLang = Number(lang.id_lang),
			viewInBrowserUrl = getViewInBrowserUrl(idLang);

		dom.vewInBrowser.attr('href', viewInBrowserUrl);
	});
*/

	NewsletterProComponents.objs.tabNewsletterTemplate.subscribe('change', function(item){
		var id = item.attr('id');

		if (id === 'tab_newsletter-template_1') 
		{
			self.viewTabActive = true;
			self.saveWriteView();
		}
		else if (id === 'tab_newsletter-template_0') 
			self.tinyRefresh();

		if (id === 'tab_newsletter-template_5' || id === 'tab_newsletter-template_2')
			self.dom.langSelect.hide();
		else
			self.dom.langSelect.show();

		if (id !== 'tab_newsletter-template_1')
			self.viewTabActive = false;
	});

	box.dataStorage.on('change', 'id_selected_lang', function(value){
		if (self.viewTabActive)
			self.render();
	});

	dom.saveTemplate.on('click', function(){
		var btn = $(this);
		box.showAjaxLoader(btn);
		self.saveTemplate().always(function(){
			box.hideAjaxLoader(btn);
		});
	});

	dom.saveAsTemplate.on('click', function(){
		var name = prompt(self.l('Enter the template name.'));

		if (name == '' || name == null)
				return false;

		var btn = $(this);
		box.showAjaxLoader(btn);
		self.saveTemplate(name).always(function(){

			// update the templates
			$.postAjax({'submit_template_controller': 'getNewsletterTemplates'}).done(function(response){
				box.dataStorage.set('templates', response);
			});
			
			box.hideAjaxLoader(btn);
		});
	});

	dom.templateTitle.on('change', function(event){
		var target = $(event.currentTarget),
			val = target.val(),
			idLang = Number(target.data('lang'));

		var header = self.getHeaderByIdLang(idLang);
		var headerVal = header.val();
		var headerNewVal = headerVal.replace(/<\s*?(title)\s*?.*?>(.*?)<\s*?\/\s*?\1\s*?>/, '<title>'+val+'</title>');
		header.val(headerNewVal);
	});

	dom.templateTitle.on('blur', function(){

		if (!self.triggerTitleChange)
			return false;

		var element = $(this),
			value = element.val(),
			idLang = Number(element.data('lang'));

		$.postAjax({ 'submit_template_controller': 'saveNewsletterPageTitle', saveNewsletterPageTitle : value, id_lang: idLang }).done(function(response) {

			if (response.success)
			{
				dom.pageTitleMessage.empty().show().append('\
					<span class="list-action-enable action-enabled">\
						<i class="icon-check"></i>\
					</span>\
				');
			}
			else
				box.alertErrors(response.errors);

			setTimeout(function() { 
				dom.pageTitleMessage.hide(); 
			}, 5000);
		});

	});


	dom.templateHeader.on('blur', function(){
		self.saveTemplate().done(function(response){
			
			self.setTitle(response.html);

			self.refreshStyle();
		});
	});

	dom.templateFooter.on('blur', function(){
		self.saveTemplate().done(function(response){
		});
	});

	dom.templateStyle.on('blur', function(){
		self.saveTemplate().done(function(response){
			self.refreshStyle();
		});
	});

	dom.templateGlobalStyle.on('blur', function(){
		self.saveTemplate().done(function(response){
			self.refreshStyle();
		});
	});

	dom.exportHTML.on('click', function(){
		event.preventDefault();

		var button = $(this),
			href = button.attr('href'),
			conf = confirm(self.l('Do you want to render the template variables?'));

		if (conf)
		{
			href = href.replace(/exportHTML(=\w+)?/, 'exportHTML&renderView');
			button.attr('href', href);
		}

		self.saveTemplate().done(function(response){
			window.location.href = href;
		});
	});

	dom.inputImportHTML.on('change', function(){

		if ($.trim(dom.inputImportHTML.val()) != '')
		{
			$.submitAjax({'submit_template_controller': 'inputImportHTML', 'form': dom.inputImportHTMLForm}).done(function(response){
				if (response.success)
				{

					self.createTemplate.vars.templateDataSource.sync(function(dataSource){
						var currentTemplate = dataSource.getItemByValue('data.filename', response.name);
						if (currentTemplate)
						{
							dataSource.setSelected(currentTemplate);
							self.createTemplate.changeTemplate(currentTemplate);
						}
					});
				}
				else
					box.alertErrors(response.errors);
			});
		}
	});
};

NewsletterPro.components.NewsletterTemplate.prototype.refreshStyle = function(css)
{
	this.parseTiny(function(id, idLang, ed){
		ed.refreshStyle();
	});
};

NewsletterPro.components.NewsletterTemplate.prototype.setTitle = function(html)
{
	for (var idLang in html)
	{
		this.triggerTitleChange = false;
		var title = this.getTitleByIdLang(idLang);
		title.val(html[idLang].title);
		this.triggerTitleChange = true;
	}
};

NewsletterPro.components.NewsletterTemplate.prototype.setTinyContent = function(html)
{
	for (var idLang in html)
	{
		var ed = this.getTinyByIdLang(idLang);
		if (ed)
		{
			ed.setContent(html[idLang].body);
		}

	}
};

NewsletterPro.components.NewsletterTemplate.prototype.saveTemplate = function(name)
{
	var self = this,
		box = NewsletterPro,
		dom = this.dom,
		css = self.stripComments(dom.templateStyle.val()),
		cssGlobal = self.stripComments(dom.templateGlobalStyle.val()),
		templateName,
		action;

	if (typeof name !== 'undefined')
	{
		action = 'saveAsNewsletterTemplate';
		templateName = name;
	}
	else
	{
		action = 'saveNewsletterTemplate';
		templateName = box.dataStorage.get('configuration.NEWSLETTER_TEMPLATE');
	}

	var  content = self.buildContent();

	this.publish('save', {
		saveAs: (action == 'saveAsNewsletterTemplate' ? true : false),
		action: action,
		templateName: templateName,
		content: content,
		css: css,
		cssGlobal: cssGlobal
	});

	return $.postAjax({'submit_template_controller': action, name: templateName, content: content, css: css, css_global: cssGlobal}).done(function(response){

		if (!response.success)
			box.alertErrors(response.errors);
		else
		{
			if (action === 'saveAsNewsletterTemplate')
			{
				self.createTemplate.vars.templateDataSource.sync(function(dataSource){
					var currentTemplate = dataSource.getItemByValue('data.filename', response.template_name);
					dataSource.setSelected(currentTemplate);
					self.createTemplate.changeTemplate(currentTemplate);
				});
			}

			dom.saveMessage.show().html('<p class="np-success-message">' + response.message + '</p>');
			setTimeout(function(){
				dom.saveMessage.hide();
			}, 5000);

		}

		self.publish('afterSave', {
			status: true,
			response: response
		});

	}).fail(function(response){
		self.publish('afterSave', {
			status: false,
			response: response
		});
	}).promise();

};

NewsletterPro.components.NewsletterTemplate.prototype.buildContent = function()
{
	var self = this,
		box = NewsletterPro,
		dom = this.dom;

	var template = {};

	this.parseTiny(function(id, idLang, ed){
		template[idLang] = {
			'title': self.getTitleByIdLang(idLang).val(),
			'body': ed.getContent(),
			'header': self.stripComments(self.getHeaderByIdLang(idLang).val()),
			'footer': self.getFooterByIdLang(idLang).val(),
		};
	});

	return template;
};

NewsletterPro.components.NewsletterTemplate.prototype.setTemplate = function(obj)
{
	var self = this,
		box = NewsletterPro,
		dom = this.dom,
		title = obj.title,
		header = obj.header,
		body = obj.body,
		footer = obj.footer,
		cssFile = obj.css_file,
		cssGlobalFile = obj.css_global_file,
		cssLink = obj.css_link,
		idSelectedLang = box.dataStorage.getNumber('id_selected_lang');

	this.parseTiny(function(id, idLang, ed){
		if (body.hasOwnProperty(idLang)) {
			ed.setContent(body[idLang]);
			
			if (cssLink.hasOwnProperty(idLang)) {
				ed.refreshStyle(cssLink[idLang]);
			}
		}
	});

	this.parseTitle(function(i, idLang, item){
		if (title.hasOwnProperty(idLang)) {
			item.val(title[idLang]);
		}
	});
	
	this.parseHeader(function(i, idLang, item){
		if (header.hasOwnProperty(idLang)) {
			item.val(header[idLang]);
		}
	});
	
	this.parseFooter(function(i, idLang, item){
		if (footer.hasOwnProperty(idLang)) {
			item.val(footer[idLang]);
		}
	});

	if (cssFile.hasOwnProperty(idSelectedLang)) {
		dom.templateStyle.val(cssFile[idSelectedLang]);
	}

	if (cssGlobalFile.hasOwnProperty(idSelectedLang)) {
		dom.templateGlobalStyle.val(cssGlobalFile[idSelectedLang]);
	}

	if (self.isViewActive)
		self.render();

	self.updateBoth();
};

NewsletterPro.components.NewsletterTemplate.prototype.getTitleByIdLang = function(idLang)
{
	return 	$(this.dom.templateTitle.filter(function(i, item){
		return (Number($(item).data('lang')) == Number(idLang));
	})[0]);
};

NewsletterPro.components.NewsletterTemplate.prototype.getHeaderByIdLang = function(idLang)
{
	return 	$(this.dom.templateHeader.filter(function(i, item){
		return (Number($(item).data('lang')) == Number(idLang));
	})[0]);
};

NewsletterPro.components.NewsletterTemplate.prototype.getFooterByIdLang = function(idLang)
{
	return 	$(this.dom.templateFooter.filter(function(i, item){
		return (Number($(item).data('lang')) == Number(idLang));
	})[0]);
};

NewsletterPro.components.NewsletterTemplate.prototype.parseTitle = function(func)
{
	$.each(this.dom.templateTitle, function(i, item){
		item = $(item)
		var idLang = Number(item.data('lang'));
		func(i, idLang, item);
	});
};

NewsletterPro.components.NewsletterTemplate.prototype.parseHeader = function(func)
{
	$.each(this.dom.templateHeader, function(i, item){
		item = $(item)
		var idLang = Number(item.data('lang'));
		func(i, idLang, item);
	});
};

NewsletterPro.components.NewsletterTemplate.prototype.parseFooter = function(func)
{
	$.each(this.dom.templateFooter, function(i, item){
		item = $(item)
		var idLang = Number(item.data('lang'));
		func(i, idLang, item);
	});
};

NewsletterPro.components.NewsletterTemplate.prototype.parseTiny = function(func)
{
	for (var i = 0; i < this.tiny.length; i++)
	{
		var item = this.tiny[i],
			ed = item.ed,
			id = item.id,
			idLang = Number(item.idLang);

		var ret = func(id, idLang, ed);

		if (typeof ret !== 'undefined')
			return ret;
	}
};

NewsletterPro.components.NewsletterTemplate.prototype.getTinyByIdLang = function(idLang)
{
	var index = this.tinyLangIndex.indexOf(Number(idLang));

	if (index != -1)
		return this.tiny[index].ed;
};

NewsletterPro.components.NewsletterTemplate.prototype.stripComments = function(str)
{
	return str.replace(/\/\*[\s\S]*?\*\//, '');
};

NewsletterPro.components.NewsletterTemplate.prototype.tinyRefresh = function()
{
	this.parseTiny(function(id, idLang, ed){
		if ($.browser.hasOwnProperty('mozilla')) 
			ed.setContent(ed.getContent());
	});
};

NewsletterPro.components.NewsletterTemplate.prototype.saveWriteView = function()
{
	var self = this;
	this.saveTemplate().done(function(response){
		if (response.success)
			self.render();
	});
};

NewsletterPro.components.NewsletterTemplate.prototype.render = function()
{
	var box = NewsletterPro,
		self = this,
		idLang = box.dataStorage.getNumber('id_selected_lang'),
		templateName = box.dataStorage.get('configuration.NEWSLETTER_TEMPLATE');

	$.postAjax({'submit_template_controller': 'renderTemplate', name: templateName, id_lang: idLang}).done(function(response){
		if (!response.success)
		{
			box.displayAlert(response.errors);
			self.writeView('');
		}
		else
			self.writeView(response.render);
	}).promise();
};

NewsletterPro.components.NewsletterTemplate.prototype.writeView = function(content)
{
	var self = this;

	if (this.getView().length > 0) 
	{
		var html = this.getView().get(0);

		html.innerHTML = content;

		setTimeout(function(){
			self.resizeView();
			self.updateBoth();
		}, 50);
	}
};

NewsletterPro.components.NewsletterTemplate.prototype.resizeView = function()
{
	if (this.getView().length > 0) 
	{
		var body = this.getView().find('body');
		if (body.length > 0) 
		{
			var wnt = this.dom.viewNewsletterTemplate.get(0);
			wnt.height = '';
			wnt.height = wnt.contentWindow.document.body.scrollHeight + "px";
		}
	}
};

NewsletterPro.components.NewsletterTemplate.prototype.getView = function()
{
	return this.dom.viewNewsletterTemplate.contents().find('html');
};

NewsletterPro.components.NewsletterTemplate.prototype.updateBoth = function()
{
	this.updateView();
	this.updateTemplate();
};

NewsletterPro.components.NewsletterTemplate.prototype.updateView = function()
{
	var self = this,
		box = NewsletterPro,
		view = this.view;

	view['container'] = this.getView().find('.newsletter-pro-container');
	view['content'] = view.container.find('.newsletter-pro-content');
	view['links'] = view.container.find('a');

	var products = view.container.find('.newsletter-pro-product');

	view.products = [];

	if (products.length > 0) 
	{
		for (var i = 0; i < products.length; i++) 
		{
			var product = $(products[i]),
				name = product.find('.newsletter-pro-name'),
				description = product.find('.newsletter-pro-description'),
				description_short = product.find('.newsletter-pro-description_short'),
				price = product.find('.newsletter-pro-price');

			var obj = {
				product: product,
				name: name,
				description: description,
				description_short: description_short,
				price: price,
			};

			// view.products = view.products || [];
			view.products.push(obj);
		}
	}
};

NewsletterPro.components.NewsletterTemplate.prototype.updateTemplate = function()
{
	var self = this,
		box = NewsletterPro,
		template = this.template,
		idSelectedLang = box.dataStorage.getNumber('id_selected_lang'),
		ct = this.createTemplate,
		vars = this.createTemplate.vars,
		ctDom = this.createTemplate.dom,
		dom = self.dom,
		tabs = NewsletterProComponents.objs.tabNewsletterTemplate;


	template['tinyBody'] = this.tinySelect('body');
	template['container'] = this.tinySelect('.newsletter-pro-container');
	template['content'] = this.tinySelect('.newsletter-pro-content');
	template['links'] = this.tinySelect('a');

	var products = this.tinySelect('.newsletter-pro-product');

	template.products = {};

	for (var idLang in products)
	{
		if (products[idLang].length > 0) 
		{
			var prod = [];

			for (var i = 0; i < products[idLang].length; i++) 
			{
				var product = $(products[idLang][i]),
					name = product.find('.newsletter-pro-name'),
					description = product.find('.newsletter-pro-description'),
					description_short = product.find('.newsletter-pro-description_short'),
					price = product.find('.newsletter-pro-price');

				var obj = {
					product: product,
					name: name,
					description: description,
					description_short: description_short,
					price: price,
				};

				prod.push(obj);
			}

			template.products[idLang] = prod;
		}
	}

	if (ct.varExists('templateWidthSlider')) 
	{
		var width = template.content[idSelectedLang].innerWidth();
		vars.templateWidthSlider.setValue(width);
	}

	var containerColor = template.container[idSelectedLang].css('background-color');
	if (/rgb/i.test(containerColor))
		containerColor = self.rgb2hex(containerColor);

	ctDom.templateContainerColor.val(containerColor);

	self.setStyleObject(template.tinyBody, {
		'background-color': '#'+containerColor
	});

	if (template.container[idSelectedLang].length > 0 ) 
	{
		dom.color.templateContainerColorObj.importColor();
		ctDom.templateContainerColor.parent().show();
	} 
	else 
	{
	 	ctDom.templateContainerColor.parent().hide();
	}

	// Content bg color
	var contentColor = template.content[idSelectedLang].css('background-color');
	if (/rgb/i.test(contentColor))
		contentColor = self.rgb2hex(contentColor);

	ctDom.templateContentColor.val(contentColor);


	if (template.content[idSelectedLang].length > 0)
	{
		dom.color.templateContentColorObj.importColor();

		ctDom.templateContentColor.parent().show();
		ctDom.sliderContainer.show();

		// show global css
		ctDom.tabGlobalCss.show();
	} 
	else 
	{
		ctDom.tabGlobalCss.hide();

		if (tabs.lastItem !== null && typeof tabs.lastItem !== 'undefined' )
		{	
			if (tabs.lastItem.attr('id') === 'tab_newsletter-template_2')
				tabs.buttons[0].click();
		}

		// hide global css
		ctDom.sliderContainer.hide();
		ctDom.templateContentColor.parent().hide();
	}

	// Product border color
	var products = template.products;
	
	if (products.hasOwnProperty(idSelectedLang) && products[idSelectedLang].length > 0) 
	{
		// Products bg color
		var product = products[idSelectedLang][0].product;
		if (product.length > 0) 
		{
			var color = product.css('background-color');

			if (/rgb/i.test(color))
				color = self.rgb2hex(color);

			ctDom.productsBgColor.val(color);
			ctDom.productsBgColor.parent().show();

			var borderW = product.css('border-width');

			if (/^[1-9]+/.test(borderW))
			{
				var color = product.css('border-color');

				if (/rgb/i.test(color))
					color = self.rgb2hex(color);	

				ctDom.productsBorderColor.parent().show();
				ctDom.productsBorderColor.val(color);

			} 
			else 
			{
				ctDom.productsBorderColor.parent().hide();
			}

		}
		else 
		{
			ctDom.productsBgColor.parent().hide();
			ctDom.productsBorderColor.parent().hide();
		}

		// Products name color
		var name = products[idSelectedLang][0].name;

		if (name.length > 0) {

			var color = name.css('color');
			if (/rgb/i.test(color))
				color = self.rgb2hex(color);	

			ctDom.productsNameColor.val(color);
			ctDom.productsNameColor.parent().show();
		}
		else 
		{
			ctDom.productsNameColor.parent().hide();
		}

		// Short description color
		var description_short = products[idSelectedLang][0].description_short;
		if (description_short.length > 0) 
		{
			var color = description_short.css('color')	;
			if (/rgb/i.test(color))
				color = self.rgb2hex(color);	

			ctDom.productsSDescriptionColor.val(color);
			ctDom.productsSDescriptionColor.parent().show();
		}
		else 
		{
			ctDom.productsSDescriptionColor.parent().hide();
		}

		// Description color
		var description = products[idSelectedLang][0].description;
		if (description.length > 0) {

			var color = description.css('color')	;
			if (/rgb/i.test(color))
				color = self.rgb2hex(color);	

			ctDom.productsDescriptionColor.val(color);
			ctDom.productsDescriptionColor.parent().show();
		}
		else 
		{
			ctDom.productsDescriptionColor.parent().hide();
		}

		// Price color
		var price = products[idSelectedLang][0].price;

		if (price.length > 0) 
		{
			var color = price.css('color')	;
			if (/rgb/i.test(color))
				color = self.rgb2hex(color);	

			ctDom.productsPriceColor.val(color);
			ctDom.productsPriceColor.parent().show();
		}
		else 
		{
			ctDom.productsPriceColor.parent().hide();
		}
	} 
	else 
	{
		ctDom.productsBgColor.val('FFFFFF');
		ctDom.productsBgColor.parent().hide();

		ctDom.productsBorderColor.val('FFFFFF');
		ctDom.productsBorderColor.parent().hide();

		ctDom.productsNameColor.val('FFFFFF');
		ctDom.productsNameColor.parent().hide();

		ctDom.productsSDescriptionColor.val('FFFFFF');
		ctDom.productsSDescriptionColor.parent().hide();

		ctDom.productsDescriptionColor.val('FFFFFF');
		ctDom.productsDescriptionColor.parent().hide();

		ctDom.productsPriceColor.val('FFFFFF');
		ctDom.productsPriceColor.parent().hide();
	}

	// }

	dom.color.productsBgColorObj.importColor();
	dom.color.productsBorderColorObj.importColor();
	dom.color.productsNameColorObj.importColor();
	dom.color.productsSDescriptionColorObj.importColor();
	dom.color.productsDescriptionColorObj.importColor();
	dom.color.productsPriceColorObj.importColor();

	// All links color
	ctDom.linksColor.val('FFFFFF');

	if (template.links[idSelectedLang].length > 0 ) 
	{
		ctDom.linksColor.parent().show();
	} 
	else 
	{
		ctDom.linksColor.parent().hide();
	}

	dom.color.linksColorObj.importColor();

	ct.refreshSliders();
};


NewsletterPro.components.NewsletterTemplate.prototype.tinySelect = function(name, idLang)
{
	var self = this,
		select = false;

	if (typeof idLang === 'undefined')
	{
		select = {};

		this.parseTiny(function(id, idLang, ed){
			select[idLang] = $(ed.dom.select(name));
		});
	}
	else
	{
		var ed = this.getTinyByIdLang(idLang);
		select = $(ed.dom.select(name));
	}

	return select;
};

NewsletterPro.components.NewsletterTemplate.prototype.setStyle = function(selection, name, value, lang)
{
	if (typeof lang !== 'undefined')
	{
		var ed = this.getTinyByIdLang(lang);
		
		if (ed)
			ed.dom.setStyle(selection, name, value);
	}
	else
	{		
		for (var idLang in selection)
		{
			var ed = this.getTinyByIdLang(idLang);

			if (ed)
				ed.dom.setStyle(selection[idLang], name, value);
		}
	}
};

NewsletterPro.components.NewsletterTemplate.prototype.setStyleObject = function(obj, css)
{
	for (var idLang in obj)
	{
		obj[idLang].css(css);
	}
};

NewsletterPro.components.NewsletterTemplate.prototype.isViewActive = function()
{
	return this.view.hasOwnProperty('container');
};

NewsletterPro.components.NewsletterTemplate.prototype.isLoadTemplate = function()
{
	return (typeof this.template.container === 'undefined' || this.template.container === null) ? false : true;
};

NewsletterPro.components.NewsletterTemplate.prototype.rgb2hex = function(rgb)
{
	rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
	if (rgb != null) 
	{
		return  ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
				("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
				("0" + parseInt(rgb[3],10).toString(16)).slice(-2);
	}
};